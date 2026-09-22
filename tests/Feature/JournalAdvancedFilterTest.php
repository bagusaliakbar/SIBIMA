<?php

namespace Tests\Feature;

use App\Models\FasilkomJournal;
use App\Models\User;
use App\Services\GarudaJournalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JournalAdvancedFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Mahasiswa Peneliti',
            'email' => 'mahasiswa@unsub.ac.id',
        ]);
    }

    public function test_fasilkom_journal_model_supports_sinta_and_doc_type_scopes(): void
    {
        $reviewJournal = FasilkomJournal::create([
            'identifier' => 'oai:ejournal.unsub.ac.id:article/100',
            'title' => 'Systematic Literature Review: Penerapan Algoritma Genetika',
            'authors' => ['Bagus Ali Akbar'],
            'authors_string' => 'Bagus Ali Akbar',
            'year' => 2024,
            'abstract' => 'Sebuah studi kajian pustaka komprehensif mengenai algoritma genetika.',
            'landing_page_url' => 'https://ejournal.unsub.ac.id/index.php/jglobal/article/view/100',
            'source_url' => 'https://ejournal.unsub.ac.id/index.php/jglobal/article/view/100',
        ]);

        $articleJournal = FasilkomJournal::create([
            'identifier' => 'oai:ejournal.unsub.ac.id:article/101',
            'title' => 'Rancang Bangun Sistem Informasi Penggajian Karyawan',
            'authors' => ['Maya Destriani'],
            'authors_string' => 'Maya Destriani',
            'year' => 2023,
            'abstract' => 'Penelitian implementasi sistem informasi penggajian berbasis web.',
            'landing_page_url' => 'https://ejournal.unsub.ac.id/index.php/jglobal/article/view/101',
            'source_url' => 'https://ejournal.unsub.ac.id/index.php/jglobal/article/view/101',
        ]);

        // SINTA 4 scope check (Fasilkom journal is SINTA 4)
        $s4Count = FasilkomJournal::filterSinta('s4')->count();
        $this->assertEquals(2, $s4Count);

        $s2s4Count = FasilkomJournal::filterSinta('s2_s4')->count();
        $this->assertEquals(2, $s2s4Count);

        $s1Count = FasilkomJournal::filterSinta('s1')->count();
        $this->assertEquals(0, $s1Count);

        // Document Type scope check
        $reviewCount = FasilkomJournal::filterDocType('review')->count();
        $this->assertEquals(1, $reviewCount);
        $this->assertEquals($reviewJournal->id, FasilkomJournal::filterDocType('review')->first()->id);

        $articleCount = FasilkomJournal::filterDocType('article')->count();
        $this->assertEquals(1, $articleCount);
        $this->assertEquals($articleJournal->id, FasilkomJournal::filterDocType('article')->first()->id);

        // Formatted journal item contains sinta and doc_type
        $item = $reviewJournal->toJournalItem();
        $this->assertEquals('S4', $item['sinta_rating']);
        $this->assertEquals('SINTA 4', $item['sinta_label']);
        $this->assertEquals('review', $item['doc_type']);
        $this->assertEquals('Literature Review', $item['doc_type_label']);
    }

    public function test_garuda_journal_service_extracts_sinta_and_filters_correctly(): void
    {
        $htmlSample = <<<'HTML'
<!DOCTYPE html>
<html>
<body>
    <div>Found 2 Documents</div>
    <div class="article-item">
        <a class="title-article" href="/documents/detail/111">Analisis Komparasi Kinerja Algoritma C4.5 dan Naive Bayes</a>
        <a class="author-article">Rino Guphita</a>
        <xmp class="subtitle-article">Jurnal Nasional Teknologi Informasi SINTA 2 (2024)</xmp>
        <xmp class="abstract-article">Penelitian eksperimental komparasi performa algoritma klasifikasi.</xmp>
        <a href="https://download.garuda.kemdiktisaintek.go.id/article/download/111">Download PDF</a>
    </div>
    <div class="article-item">
        <a class="title-article" href="/documents/detail/222">A Systematic Literature Review on Deep Learning in Healthcare</a>
        <a class="author-article">Tepi Peirisal</a>
        <xmp class="subtitle-article">Prosiding Seminar Nasional Rekayasa Komputer (2023)</xmp>
        <xmp class="abstract-article">Tinjauan literatur mengenai penerapan deep learning dalam deteksi penyakit.</xmp>
        <a href="https://download.garuda.kemdiktisaintek.go.id/article/download/222">Download PDF</a>
    </div>
</body>
</html>
HTML;

        $service = app(GarudaJournalService::class);

        // Parse without filter
        $allResults = $service->parseHtml($htmlSample, 1, 10, 'all', 'all');
        $this->assertCount(2, $allResults['data']);
        $this->assertEquals('S2', $allResults['data'][0]['sinta_rating']);
        $this->assertEquals('SINTA 2', $allResults['data'][0]['sinta_label']);
        $this->assertEquals('article', $allResults['data'][0]['doc_type']);
        $this->assertEquals('Artikel Penelitian', $allResults['data'][0]['doc_type_label']);

        $this->assertEquals('review', $allResults['data'][1]['doc_type']);
        $this->assertEquals('Literature Review', $allResults['data'][1]['doc_type_label']);

        // Filter by SINTA 2 - SINTA 4 (Syarat Skripsi)
        $sintaResults = $service->parseHtml($htmlSample, 1, 10, 's2_s4', 'all');
        $this->assertGreaterThanOrEqual(1, count($sintaResults['data']));

        // Filter by Document Type: review
        $reviewResults = $service->parseHtml($htmlSample, 1, 10, 'all', 'review');
        $this->assertCount(1, $reviewResults['data']);
        $this->assertEquals('222', str_replace('garuda_', '', $reviewResults['data'][0]['id']));
    }

    public function test_journal_search_page_renders_sinta_and_doctype_filters(): void
    {
        $fakeHtml = <<<'HTML'
<!DOCTYPE html>
<html>
<body>
    <div>Found 1 Documents</div>
    <div class="article-item">
        <a class="title-article" href="/documents/detail/12345">Klasifikasi Citra Menggunakan Convolutional Neural Network</a>
        <a class="author-article">Andi Saputra</a>
        <xmp class="subtitle-article">Jurnal Nasional Informatika SINTA 3 (2024)</xmp>
        <xmp class="abstract-article">Penelitian klasifikasi citra daun herbal berbasis deep learning CNN.</xmp>
        <a href="https://download.garuda.kemdiktisaintek.go.id/article/download/12345">Download PDF</a>
    </div>
</body>
</html>
HTML;

        Http::fake([
            'garuda.kemdiktisaintek.go.id/*' => Http::response($fakeHtml, 200),
        ]);

        $response = $this->actingAs($this->student)->get(route('repositories.journals', [
            'source' => 'garuda',
            'q' => 'Machine Learning',
            'sinta' => 's2_s4',
            'doc_type' => 'article',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Akreditasi SINTA:');
        $response->assertSee('Tipe:');
        $response->assertSee('SINTA 2 – SINTA 4 (Syarat Skripsi)');
        $response->assertSee('📄 Artikel Penelitian');
        $response->assertSee('Filter Aktif:');
        $response->assertSee('Klasifikasi Citra Menggunakan Convolutional Neural Network');
    }

    public function test_fasilkom_journal_source_with_sinta_and_doctype_filters(): void
    {
        FasilkomJournal::create([
            'identifier' => 'oai:ejournal.unsub.ac.id:article/200',
            'title' => 'Systematic Review: Perkembangan Flutter Framework',
            'authors' => ['Maya Destriani'],
            'authors_string' => 'Maya Destriani',
            'year' => 2024,
            'abstract' => 'Sebuah tinjauan pustaka sistematis pada arsitektur mobile apps.',
            'landing_page_url' => 'https://ejournal.unsub.ac.id/index.php/jglobal/article/view/200',
            'source_url' => 'https://ejournal.unsub.ac.id/index.php/jglobal/article/view/200',
        ]);

        FasilkomJournal::create([
            'identifier' => 'oai:ejournal.unsub.ac.id:article/201',
            'title' => 'Penerapan Algoritma K-Means untuk Segmentasi Pelanggan',
            'authors' => ['Tepi Peirisal'],
            'authors_string' => 'Tepi Peirisal',
            'year' => 2024,
            'abstract' => 'Penelitian clustering data transaksi.',
            'landing_page_url' => 'https://ejournal.unsub.ac.id/index.php/jglobal/article/view/201',
            'source_url' => 'https://ejournal.unsub.ac.id/index.php/jglobal/article/view/201',
        ]);

        // Filter by SINTA 4 and doc_type review
        $response = $this->actingAs($this->student)->get(route('repositories.journals', [
            'source' => 'fasilkom',
            'sinta' => 's4',
            'doc_type' => 'review',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Systematic Review: Perkembangan Flutter Framework');
        $response->assertDontSee('Penerapan Algoritma K-Means');
        $response->assertSee('SINTA 4');
        $response->assertSee('Literature Review');
    }

    public function test_bookmarks_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->student)->get(route('repositories.bookmarks'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Bacaan');
    }
}

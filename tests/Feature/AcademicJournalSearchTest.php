<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\OpenAlexService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AcademicJournalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_guest_cannot_access_journal_search()
    {
        $response = $this->get(route('repositories.journals'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_student_can_view_journal_search_initial_page()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($student)->get(route('repositories.journals'));

        $response->assertStatus(200);
        $response->assertSee('Eksplorasi Jurnal Ilmiah');
        $response->assertSee('Open Access');
        $response->assertSee('Cari & Temukan Referensi Skripsi', false);
        $response->assertSee('Machine Learning');
    }

    public function test_journal_search_with_query_and_openalex_mock()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        Http::fake([
            'api.openalex.org/works*' => Http::response([
                'meta' => [
                    'count' => 1,
                    'db_response_time_ms' => 12,
                    'page' => 1,
                    'per_page' => 12,
                ],
                'results' => [
                    [
                        'id' => 'https://openalex.org/W12345678',
                        'title' => 'Implementasi Machine Learning untuk Klasifikasi Berita Palsu',
                        'publication_year' => 2024,
                        'publication_date' => '2024-03-15',
                        'doi' => 'https://doi.org/10.1234/test.2024',
                        'cited_by_count' => 45,
                        'authorships' => [
                            [
                                'author' => ['display_name' => 'Budi Santoso']
                            ],
                            [
                                'author' => ['display_name' => 'Dewi Lestari']
                            ]
                        ],
                        'primary_location' => [
                            'source' => [
                                'display_name' => 'Jurnal Teknologi Informasi',
                                'type' => 'journal'
                            ],
                            'pdf_url' => 'https://example.com/paper.pdf',
                            'landing_page_url' => 'https://doi.org/10.1234/test.2024'
                        ],
                        'open_access' => [
                            'is_oa' => true,
                            'oa_url' => 'https://example.com/paper.pdf'
                        ],
                        'abstract_inverted_index' => [
                            'Penelitian' => [0],
                            'ini' => [1],
                            'membahas' => [2],
                            'klasifikasi' => [3],
                            'teks.' => [4]
                        ],
                        'concepts' => [
                            ['display_name' => 'Machine learning', 'score' => 0.95],
                            ['display_name' => 'Artificial intelligence', 'score' => 0.88]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->actingAs($student)->get(route('repositories.journals', [
            'q' => 'Machine Learning',
            'year_filter' => '5_years',
            'sort' => 'newest',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Implementasi Machine Learning untuk Klasifikasi Berita Palsu');
        $response->assertSee('Budi Santoso, Dewi Lestari');
        $response->assertSee('Jurnal Teknologi Informasi');
        $response->assertSee('2024');
        $response->assertSee('45 Sitasi');
        $response->assertSee('Open Access (Gratis)');
        $response->assertSee('Penelitian ini membahas klasifikasi teks.');
        $response->assertSee('Buka PDF Full-Text');
        $response->assertSee('https://example.com/paper.pdf');
    }

    public function test_service_abstract_reconstruction_and_citations()
    {
        $service = new OpenAlexService();

        // Test abstract reconstruction
        $invertedIndex = [
            'Sistem' => [0],
            'informasi' => [1],
            'akademik' => [2],
            'berbasis' => [3],
            'web.' => [4],
        ];
        $abstract = $service->reconstructAbstract($invertedIndex);
        $this->assertEquals('Sistem informasi akademik berbasis web.', $abstract);

        // Test citations generation
        $citations = $service->generateCitations(
            'Analisis Sentimen Twitter',
            ['Ahmad Fauzi', 'Siti Rahma'],
            2023,
            'Jurnal Komputer Nasional',
            'https://doi.org/10.1234/jkn.2023'
        );

        $this->assertArrayHasKey('apa', $citations);
        $this->assertArrayHasKey('ieee', $citations);
        $this->assertArrayHasKey('bibtex', $citations);

        $this->assertStringContainsString('Fauzi, A., Rahma, S.', $citations['apa']);
        $this->assertStringContainsString('Analisis Sentimen Twitter', $citations['apa']);
        $this->assertStringContainsString('2023', $citations['apa']);

        $this->assertStringContainsString('A. Fauzi, S. Rahma', $citations['ieee']);
        $this->assertStringContainsString('@article{', $citations['bibtex']);
    }

    public function test_graceful_handling_when_openalex_api_fails()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        Http::fake([
            'api.openalex.org/works*' => Http::response('Internal Server Error', 500),
        ]);

        $response = $this->actingAs($student)->get(route('repositories.journals', [
            'q' => 'Deep Learning Error Case',
        ]));

        $response->assertStatus(200);
        $response->assertSee('gangguan');
    }

    public function test_fasilkom_journal_model_and_citations()
    {
        $journal = \App\Models\FasilkomJournal::create([
            'identifier' => 'oai:ojs.pkp.sfu.ca:article/101',
            'article_id' => '101',
            'title' => 'Rancang Bangun Sistem Informasi Inventory Berbasis Web',
            'authors' => ['Syarif Hidayat', 'Denis'],
            'authors_string' => 'Syarif Hidayat, Denis',
            'abstract' => 'Penelitian ini mengembangkan sistem informasi inventory barang...',
            'subjects' => ['Inventory', 'Web', 'Laravel'],
            'publication_date' => '2023-06-15',
            'year' => 2023,
            'volume' => '7',
            'issue' => '1',
            'pages' => '1-10',
            'landing_page_url' => 'https://ejournal.unsub.ac.id/index.php/FASILKOM/article/view/101',
            'pdf_url' => 'https://ejournal.unsub.ac.id/index.php/FASILKOM/article/download/101/101',
        ]);

        $item = $journal->toJournalItem();

        $this->assertEquals('fasilkom', $item['source']);
        $this->assertEquals('Jurnal GLOBAL FASILKOM UNSUB', $item['source_label']);
        $this->assertEquals(2023, $item['year']);
        $this->assertArrayHasKey('apa', $item['citations']);
        $this->assertArrayHasKey('ieee', $item['citations']);
        $this->assertArrayHasKey('bibtex', $item['citations']);
        $this->assertStringContainsString('Hidayat, S. & Denis', $item['citations']['apa']);
        $this->assertStringContainsString('GLOBAL: Jurnal Fakultas Ilmu Komputer', $item['citations']['apa']);
    }

    public function test_fasilkom_journal_search_source()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        \App\Models\FasilkomJournal::create([
            'identifier' => 'oai:ojs.pkp.sfu.ca:article/201',
            'article_id' => '201',
            'title' => 'Penerapan Metode SAW pada Pemilihan Karyawan Terbaik',
            'authors' => ['Santi Purwanti'],
            'authors_string' => 'Santi Purwanti',
            'abstract' => 'Sistem Pendukung Keputusan pemilihan karyawan...',
            'subjects' => ['SPK', 'SAW'],
            'year' => 2024,
            'volume' => '8',
            'issue' => '1',
            'landing_page_url' => 'https://ejournal.unsub.ac.id/index.php/FASILKOM/article/view/201',
            'pdf_url' => 'https://ejournal.unsub.ac.id/index.php/FASILKOM/article/download/201/201',
        ]);

        $response = $this->actingAs($student)->get(route('repositories.journals', [
            'q' => 'Metode SAW',
            'source' => 'fasilkom',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Penerapan Metode SAW pada Pemilihan Karyawan Terbaik');
        $response->assertSee('Santi Purwanti');
        $response->assertSee('Jurnal GLOBAL FASILKOM UNSUB');
        $response->assertSee('Buka di OJS FASILKOM');
    }

    public function test_fasilkom_journal_browse_without_query()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        \App\Models\FasilkomJournal::create([
            'identifier' => 'oai:ojs.pkp.sfu.ca:article/301',
            'article_id' => '301',
            'title' => 'Analisis Kinerja Jaringan Komputer',
            'authors' => ['Jaja'],
            'authors_string' => 'Jaja',
            'year' => 2022,
            'volume' => '6',
            'issue' => '2',
            'landing_page_url' => 'https://ejournal.unsub.ac.id/index.php/FASILKOM/article/view/301',
        ]);

        $response = $this->actingAs($student)->get(route('repositories.journals', [
            'source' => 'fasilkom',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Analisis Kinerja Jaringan Komputer');
        $response->assertSee('Jurnal GLOBAL FASILKOM UNSUB');
        $response->assertDontSee('Cari & Temukan Referensi Skripsi');
    }

    public function test_sync_fasilkom_journals_command()
    {
        $fakeXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
  <responseDate>2026-09-10T00:00:00Z</responseDate>
  <request verb="ListRecords" metadataPrefix="oai_dc">https://ejournal.unsub.ac.id/index.php/FASILKOM/oai</request>
  <ListRecords>
    <record>
      <header>
        <identifier>oai:ojs.pkp.sfu.ca:article/999</identifier>
        <datestamp>2024-05-01T00:00:00Z</datestamp>
      </header>
      <metadata>
        <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
                   xmlns:dc="http://purl.org/dc/elements/1.1/">
          <dc:title>Penerapan Algoritma K-Means pada Segmentasi Pelanggan</dc:title>
          <dc:creator>Syarif Hidayat</dc:creator>
          <dc:subject>K-Means; Clustering</dc:subject>
          <dc:description>Penelitian ini bertujuan untuk...</dc:description>
          <dc:date>2024-05-01</dc:date>
          <dc:source>GLOBAL: Jurnal Fakultas Ilmu Komputer; Vol. 8 No. 1 (2024); 10-18</dc:source>
          <dc:identifier>https://ejournal.unsub.ac.id/index.php/FASILKOM/article/view/999</dc:identifier>
          <dc:relation>https://ejournal.unsub.ac.id/index.php/FASILKOM/article/download/999/999</dc:relation>
        </oai_dc:dc>
      </metadata>
    </record>
  </ListRecords>
</OAI-PMH>
XML;

        Http::fake([
            'ejournal.unsub.ac.id/index.php/FASILKOM/oai*' => Http::response($fakeXml, 200, ['Content-Type' => 'text/xml']),
        ]);

        $this->artisan('journals:sync-fasilkom')
            ->expectsOutputToContain('Memulai pemanenan artikel dari Jurnal GLOBAL FASILKOM UNSUB')
            ->assertExitCode(0);

        $this->assertDatabaseHas('fasilkom_journals', [
            'identifier' => 'oai:ojs.pkp.sfu.ca:article/999',
            'title' => 'Penerapan Algoritma K-Means pada Segmentasi Pelanggan',
            'year' => 2024,
            'volume' => '8',
            'issue' => '1',
        ]);
    }
}

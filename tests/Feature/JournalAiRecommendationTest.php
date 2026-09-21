<?php

namespace Tests\Feature;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JournalAiRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_student_with_active_thesis_sees_smart_recommendation_banner()
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Ahmad Mahasiswa']);
        
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Pendukung Keputusan Pemilihan Dosen Berprestasi Menggunakan Metode AHP dan TOPSIS',
            'status' => 'active',
            'topic' => 'Sistem Informasi',
        ]);

        $response = $this->actingAs($student)->get(route('repositories.journals'));

        $response->assertStatus(200);
        $response->assertSee('Rekomendasi Pintar AI');
        $response->assertSee('Judul Skripsi Anda Terdeteksi');
        $response->assertSee('Sistem Pendukung Keputusan Pemilihan Dosen Berprestasi Menggunakan Metode AHP dan TOPSIS');
        $response->assertSee('#AHP');
        $response->assertSee('#TOPSIS');
        $response->assertSee('Cari Rujukan untuk Skripsiku');
    }

    public function test_student_without_thesis_does_not_see_recommendation_banner()
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Baru']);

        $response = $this->actingAs($student)->get(route('repositories.journals'));

        $response->assertStatus(200);
        $response->assertDontSee('Judul Skripsi Anda Terdeteksi');
        $response->assertDontSee('Cari Rujukan untuk Skripsiku');
    }

    public function test_non_student_does_not_see_recommendation_banner()
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Bagus Dosen']);

        $response = $this->actingAs($dosen)->get(route('repositories.journals'));

        $response->assertStatus(200);
        $response->assertDontSee('Judul Skripsi Anda Terdeteksi');
        $response->assertDontSee('Cari Rujukan untuk Skripsiku');
    }

    public function test_search_results_include_ai_tldr_and_similar_paper_button()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        Http::fake([
            'api.openalex.org/works*' => Http::response([
                'meta' => [
                    'count' => 1,
                    'db_response_time_ms' => 10,
                    'page' => 1,
                    'per_page' => 12,
                ],
                'results' => [
                    [
                        'id' => 'https://openalex.org/W99999999',
                        'title' => 'Analisis Sentimen Twitter Terhadap Pemilu Menggunakan Deep Learning LSTM',
                        'publication_year' => 2024,
                        'publication_date' => '2024-04-10',
                        'doi' => 'https://doi.org/10.1234/test.lstm.2024',
                        'cited_by_count' => 12,
                        'authorships' => [
                            [
                                'author' => ['display_name' => 'Rina Wijaya']
                            ]
                        ],
                        'primary_location' => [
                            'source' => [
                                'display_name' => 'Jurnal Komputer dan Informatika',
                                'type' => 'journal'
                            ],
                            'pdf_url' => 'https://example.com/lstm_paper.pdf',
                            'landing_page_url' => 'https://doi.org/10.1234/test.lstm.2024'
                        ],
                        'open_access' => [
                            'is_oa' => true,
                            'oa_url' => 'https://example.com/lstm_paper.pdf'
                        ],
                        'abstract_inverted_index' => [
                            'Penelitian' => [0],
                            'ini' => [1],
                            'bertujuan' => [2],
                            'menganalisis' => [3],
                            'opini' => [4],
                            'masyarakat.' => [5],
                            'Metode' => [6],
                            'yang' => [7],
                            'digunakan' => [8],
                            'adalah' => [9],
                            'Long' => [10],
                            'Short-Term' => [11],
                            'Memory' => [12],
                            '(LSTM).' => [13],
                            'Hasil' => [14],
                            'penelitian' => [15],
                            'menunjukkan' => [16],
                            'bahwa' => [17],
                            'model' => [18],
                            'mencapai' => [19],
                            'akurasi' => [20],
                            '92%.' => [21]
                        ],
                        'concepts' => [
                            ['display_name' => 'Deep learning', 'score' => 0.95],
                            ['display_name' => 'Sentiment analysis', 'score' => 0.90]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->actingAs($student)->get(route('repositories.journals', [
            'q' => 'Sentimen LSTM',
            'source' => 'openalex',
        ]));

        $response->assertStatus(200);
        $response->assertSee('AI Quick Summary');
        $response->assertSee('Poin Kunci Penelitian');
        $response->assertSee('Masalah / Tujuan');
        $response->assertSee('Metode / Algoritma');
        $response->assertSee('Hasil Utama');
        $response->assertSee('Artikel Serupa');
        $response->assertSee('Deep learning');

        // Reference Manager Integration elements
        $response->assertSee('FASILKOM UNSUB (Word)');
        $response->assertSee('File .RIS');
        $response->assertSee('Unduh .RIS');
        $response->assertSee('Salin untuk Word');
    }

    public function test_export_citations_returns_fasilkom_and_ris_formats()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        \App\Models\JournalBookmark::create([
            'user_id' => $student->id,
            'journal_identifier' => 'openalex:W12345678',
            'title' => 'Pengembangan Sistem Cerdas Berbasis Web',
            'authors' => ['Bagus Ali Akbar'],
            'authors_string' => 'Bagus Ali Akbar',
            'year' => 2024,
            'venue' => 'Jurnal FASILKOM',
            'doi' => 'https://doi.org/10.1234/fasilkom.2024.1',
            'citations' => [
                'apa' => 'Akbar, B. A. (2024). Pengembangan Sistem Cerdas Berbasis Web.',
                'fasilkom' => 'Akbar, B. A. (2024). "Pengembangan Sistem Cerdas Berbasis Web". Jurnal FASILKOM.',
                'ris' => "TY  - JOUR\r\nTI  - Pengembangan Sistem Cerdas Berbasis Web\r\nER  - ",
            ],
        ]);

        $response = $this->actingAs($student)->getJson(route('repositories.bookmarks.export_citations'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'count',
            'fasilkom',
            'apa',
            'ieee',
            'bibtex',
            'ris',
        ]);

        $data = $response->json();
        $this->assertEquals(1, $data['count']);
        $this->assertStringContainsString('Pengembangan Sistem Cerdas Berbasis Web', $data['fasilkom']);
        $this->assertStringContainsString('TY  - JOUR', $data['ris']);
    }
}

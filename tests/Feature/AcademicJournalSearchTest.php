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
}

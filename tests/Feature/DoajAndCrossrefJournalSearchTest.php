<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CrossrefJournalService;
use App\Services\DoajJournalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DoajAndCrossrefJournalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_doaj_service_formats_and_returns_clean_data()
    {
        Http::fake([
            'https://doaj.org/api/search/articles/*' => Http::response([
                'total' => 1,
                'page' => 1,
                'pageSize' => 12,
                'results' => [
                    [
                        'id' => 'doaj-test-123',
                        'bibjson' => [
                            'title' => 'Deep Learning in Agriculture',
                            'year' => '2024',
                            'month' => '05',
                            'journal' => [
                                'title' => 'Journal of Agricultural AI',
                                'publisher' => 'Open Academic Press',
                            ],
                            'author' => [
                                ['name' => 'Budi Pratama'],
                                ['name' => 'Siti Nurhaliza'],
                            ],
                            'identifier' => [
                                ['type' => 'doi', 'id' => '10.1234/doaj.2024.01'],
                            ],
                            'link' => [
                                ['type' => 'fulltext', 'content_type' => 'application/pdf', 'url' => 'https://example.org/agri-ai.pdf'],
                            ],
                            'abstract' => 'This paper explores deep learning models for crop disease detection.',
                            'keywords' => ['deep learning', 'agriculture', 'computer vision'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new DoajJournalService();
        $result = $service->search('agriculture', [
            'page' => 1,
            'per_page' => 12,
            'year_filter' => '3_years',
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['count']);
        $this->assertCount(1, $result['data']);

        $item = $result['data'][0];
        $this->assertEquals('doaj-test-123', $item['id']);
        $this->assertEquals('Deep Learning in Agriculture', $item['title']);
        $this->assertEquals('Budi Pratama, Siti Nurhaliza', $item['authors_string']);
        $this->assertEquals(2024, $item['year']);
        $this->assertEquals('Journal of Agricultural AI', $item['venue']);
        $this->assertEquals('10.1234/doaj.2024.01', $item['doi']);
        $this->assertTrue($item['is_oa']);
        $this->assertEquals('https://example.org/agri-ai.pdf', $item['pdf_url']);
        $this->assertEquals('doaj', $item['source']);
        $this->assertEquals('DOAJ Open Access', $item['source_label']);
        $this->assertArrayHasKey('apa', $item['citations']);
        $this->assertArrayHasKey('ieee', $item['citations']);
        $this->assertArrayHasKey('bibtex', $item['citations']);
    }

    public function test_crossref_service_formats_and_returns_clean_data()
    {
        Http::fake([
            'https://api.crossref.org/works*' => Http::response([
                'status' => 'ok',
                'message' => [
                    'total-results' => 1,
                    'items-per-page' => 12,
                    'items' => [
                        [
                            'DOI' => '10.1016/j.compag.2023.107890',
                            'title' => ['Robotics and Machine Learning in Precision Agriculture'],
                            'author' => [
                                ['family' => 'Smith', 'given' => 'John'],
                                ['family' => 'Doe', 'given' => 'Jane'],
                            ],
                            'container-title' => ['Computers and Electronics in Agriculture'],
                            'publisher' => 'Elsevier',
                            'published-print' => [
                                'date-parts' => [[2023, 8, 15]],
                            ],
                            'is-referenced-by-count' => 78,
                            'URL' => 'https://doi.org/10.1016/j.compag.2023.107890',
                            'abstract' => '<jats:p>An in-depth study of autonomous robots in modern agriculture.</jats:p>',
                            'subject' => ['Computer Science', 'Agronomy'],
                            'link' => [
                                [
                                    'URL' => 'https://example.com/robotics-agri.pdf',
                                    'content-type' => 'application/pdf',
                                ],
                            ],
                            'license' => [
                                [
                                    'URL' => 'http://creativecommons.org/licenses/by/4.0/',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new CrossrefJournalService();
        $result = $service->search('robotics agriculture', [
            'page' => 1,
            'per_page' => 12,
            'year_filter' => '5_years',
            'sort' => 'cited',
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['count']);
        $this->assertCount(1, $result['data']);

        $item = $result['data'][0];
        $this->assertEquals('10.1016/j.compag.2023.107890', $item['doi']);
        $this->assertEquals('Robotics and Machine Learning in Precision Agriculture', $item['title']);
        $this->assertEquals('John Smith, Jane Doe', $item['authors_string']);
        $this->assertEquals(2023, $item['year']);
        $this->assertEquals('Computers and Electronics in Agriculture', $item['venue']);
        $this->assertEquals(78, $item['cited_by_count']);
        $this->assertTrue($item['is_oa']);
        $this->assertEquals('https://example.com/robotics-agri.pdf', $item['pdf_url']);
        $this->assertEquals('crossref', $item['source']);
        $this->assertEquals('Crossref DOI Registry', $item['source_label']);
        $this->assertEquals('An in-depth study of autonomous robots in modern agriculture.', $item['abstract']);
    }

    public function test_journals_endpoint_handles_doaj_source()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        Http::fake([
            'https://doaj.org/api/search/articles/*' => Http::response([
                'total' => 1,
                'page' => 1,
                'pageSize' => 12,
                'results' => [
                    [
                        'id' => 'doaj-article-999',
                        'bibjson' => [
                            'title' => 'Cloud Computing Architectures in Higher Education',
                            'year' => '2024',
                            'journal' => ['title' => 'International Journal of Cloud Tech'],
                            'author' => [['name' => 'Ahmad Fauzi']],
                            'identifier' => [['type' => 'doi', 'id' => '10.5555/cloud.2024']],
                            'link' => [['type' => 'fulltext', 'content_type' => 'application/pdf', 'url' => 'https://example.org/cloud.pdf']],
                            'abstract' => 'Cloud computing evaluation for university systems.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($student)->get(route('repositories.journals', [
            'q' => 'cloud computing',
            'source' => 'doaj',
        ]));

        $response->assertStatus(200);
        $response->assertSee('DOAJ (Open Access)');
        $response->assertSee('Cloud Computing Architectures in Higher Education');
        $response->assertSee('Ahmad Fauzi');
        $response->assertSee('DOAJ Open Access');
        $response->assertSee('Buka PDF Full-Text');
        $response->assertSee('https://example.org/cloud.pdf');
    }

    public function test_journals_endpoint_handles_crossref_source()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        Http::fake([
            'https://api.crossref.org/works*' => Http::response([
                'status' => 'ok',
                'message' => [
                    'total-results' => 1,
                    'items-per-page' => 12,
                    'items' => [
                        [
                            'DOI' => '10.1145/3333333.4444444',
                            'title' => ['Quantum Computing Algorithms for Optimization'],
                            'author' => [['family' => 'Turing', 'given' => 'Alan']],
                            'container-title' => ['ACM Computing Surveys'],
                            'published-print' => ['date-parts' => [[2024]]],
                            'is-referenced-by-count' => 120,
                            'URL' => 'https://doi.org/10.1145/3333333.4444444',
                            'abstract' => 'Comprehensive survey on quantum computing techniques.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($student)->get(route('repositories.journals', [
            'q' => 'quantum computing',
            'source' => 'crossref',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Crossref (DOI Registry)');
        $response->assertSee('Quantum Computing Algorithms for Optimization');
        $response->assertSee('Alan Turing');
        $response->assertSee('Crossref DOI Registry');
        $response->assertSee('120 Sitasi');
        $response->assertSee('https://doi.org/10.1145/3333333.4444444');
    }

    public function test_federated_search_includes_doaj_and_crossref()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        Http::fake([
            // DOAJ
            'https://doaj.org/api/search/articles/*' => Http::response([
                'total' => 1,
                'page' => 1,
                'results' => [
                    [
                        'id' => 'doaj-1',
                        'bibjson' => [
                            'title' => 'DOAJ Federated Article Test',
                            'year' => '2024',
                            'journal' => ['title' => 'DOAJ Journal'],
                            'author' => [['name' => 'Author DOAJ']],
                        ],
                    ],
                ],
            ], 200),
            // Crossref
            'https://api.crossref.org/works*' => Http::response([
                'status' => 'ok',
                'message' => [
                    'total-results' => 1,
                    'items' => [
                        [
                            'DOI' => '10.1000/crossref.1',
                            'title' => ['Crossref Federated Article Test'],
                            'container-title' => ['Crossref Journal'],
                            'author' => [['family' => 'Author Crossref']],
                            'published-print' => ['date-parts' => [[2024]]],
                        ],
                    ],
                ],
            ], 200),
            // OpenAlex
            'https://api.openalex.org/works*' => Http::response([
                'meta' => ['count' => 1],
                'results' => [
                    [
                        'id' => 'https://openalex.org/W1',
                        'title' => 'OpenAlex Federated Article Test',
                        'publication_year' => 2024,
                        'primary_location' => [
                            'source' => ['display_name' => 'OpenAlex Journal'],
                        ],
                    ],
                ],
            ], 200),
            // GARUDA
            'https://garuda.kemdiktisaintek.go.id/*' => Http::response(
                '<html><body><div class="article-item"><a class="title-link" href="#">GARUDA Federated Article Test</a></div></body></html>',
                200
            ),
        ]);

        $response = $this->actingAs($student)->get(route('repositories.journals', [
            'q' => 'federated search',
            'source' => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertSee('DOAJ Federated Article Test');
        $response->assertSee('Crossref Federated Article Test');
        $response->assertSee('OpenAlex Federated Article Test');
    }
}

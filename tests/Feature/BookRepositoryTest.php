<?php

namespace Tests\Feature;

use App\Models\JournalBookmark;
use App\Models\User;
use App\Services\BookRepositoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $this->student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Bagus Mahasiswa SI',
            'email' => 'mahasiswasi@unsub.ac.id',
        ]);
    }

    public function test_service_provides_eight_information_systems_topics(): void
    {
        $topics = BookRepositoryService::getInformationSystemsTopics();

        $this->assertCount(8, $topics);
        $this->assertArrayHasKey('all_si', $topics);
        $this->assertArrayHasKey('basis_data', $topics);
        $this->assertArrayHasKey('analisis_desain', $topics);
        $this->assertArrayHasKey('rekayasa_web', $topics);
        $this->assertArrayHasKey('manajemen_ti', $topics);
        $this->assertArrayHasKey('ai_data_science', $topics);
        $this->assertArrayHasKey('keamanan_informasi', $topics);
        $this->assertArrayHasKey('e_business', $topics);

        foreach ($topics as $topic) {
            $this->assertNotEmpty($topic['name']);
            $this->assertNotEmpty($topic['icon']);
            $this->assertNotEmpty($topic['default_query']);
            $this->assertIsArray($topic['keywords']);
        }
    }

    public function test_service_generates_standard_book_citations(): void
    {
        $service = new BookRepositoryService();
        $citations = $service->generateBookCitations(
            'Management Information Systems: Managing the Digital Firm',
            ['Kenneth C. Laudon', 'Jane P. Laudon'],
            2020,
            'Pearson Education',
            'https://doi.org/10.1000/182'
        );

        $this->assertArrayHasKey('apa', $citations);
        $this->assertArrayHasKey('ieee', $citations);
        $this->assertArrayHasKey('chicago', $citations);
        $this->assertArrayHasKey('bibtex', $citations);
        $this->assertArrayHasKey('ris', $citations);

        // APA check
        $this->assertStringContainsString('Laudon', $citations['apa']);
        $this->assertStringContainsString('(2020)', $citations['apa']);
        $this->assertStringContainsString('Pearson Education', $citations['apa']);

        // BibTeX check (@book format)
        $this->assertStringStartsWith('@book', $citations['bibtex']);
        $this->assertStringContainsString('Management Information Systems', $citations['bibtex']);

        // RIS check (TY - BOOK)
        $this->assertStringContainsString('TY  - BOOK', $citations['ris']);
        $this->assertStringContainsString('PB  - Pearson Education', $citations['ris']);
    }

    public function test_books_page_renders_successfully_for_authenticated_user(): void
    {
        Http::fake([
            'https://openlibrary.org/search.json*' => Http::response([
                'docs' => [
                    [
                        'key' => '/works/OL123W',
                        'title' => 'Fundamentals of Database Systems',
                        'author_name' => ['Ramez Elmasri', 'Shamkant B. Navathe'],
                        'first_publish_year' => 2017,
                        'cover_i' => 123456,
                        'isbn' => ['9780133970777'],
                        'publisher' => ['Pearson'],
                        'ebook_access' => 'public',
                        'ia' => ['fundamentalsofdatabasesystems00elma'],
                        'subject' => ['Database management', 'Computer science'],
                    ]
                ]
            ], 200),
            'https://directory.doabooks.org/rest/search*' => Http::response([], 200),
            'https://www.googleapis.com/books/v1/volumes*' => Http::response(['items' => []], 200),
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('repositories.books'));

        $response->assertOk();
        $response->assertSee('Katalog Buku Teks');
        $response->assertSee('Sistem Informasi');
        $response->assertSee('Semua Topik SI');
        $response->assertSee('Fundamentals of Database Systems');
        $response->assertSee('Ramez Elmasri');
    }

    public function test_books_page_supports_topic_filter(): void
    {
        Http::fake([
            'https://openlibrary.org/search.json*' => Http::response([
                'docs' => [
                    [
                        'key' => '/works/OL456W',
                        'title' => 'Systems Analysis and Design Methods',
                        'author_name' => ['Jeffrey L. Whitten', 'Lonnie D. Bentley'],
                        'first_publish_year' => 2007,
                        'cover_i' => 789101,
                        'publisher' => ['McGraw-Hill'],
                        'ebook_access' => 'borrowable',
                        'ia' => ['systemsanalysis00whit'],
                    ]
                ]
            ], 200),
            'https://directory.doabooks.org/rest/search*' => Http::response([], 200),
            'https://www.googleapis.com/books/v1/volumes*' => Http::response(['items' => []], 200),
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('repositories.books', ['topic' => 'analisis_desain']));

        $response->assertOk();
        $response->assertSee('Analisis & Perancangan Sistem');
        $response->assertSee('Systems Analysis and Design Methods');
    }

    public function test_user_can_bookmark_book_and_view_it_in_bookmarks_page(): void
    {
        $bookData = [
            'journal_identifier' => 'ol:OL999W',
            'title' => 'Enterprise Architecture at Work',
            'authors' => ['Marc Lankhorst'],
            'authors_string' => 'Marc Lankhorst',
            'year' => 2017,
            'venue' => 'Buku Teks Akademik',
            'publisher' => 'Springer',
            'url' => 'https://archive.org/details/enterprisearch00lank',
            'abstract' => 'Pemodelan Enterprise Architecture menggunakan TOGAF dan ArchiMate.',
            'source' => 'openlibrary',
            'source_label' => 'Open Library (Internet Archive)',
            'citations' => [
                'apa' => 'Lankhorst, M. (2017). Enterprise Architecture at Work. Springer.',
                'ieee' => 'M. Lankhorst, Enterprise Architecture at Work. Springer, 2017.',
            ]
        ];

        // 1. Toggle bookmark via AJAX
        $response = $this->actingAs($this->student)
            ->postJson(route('repositories.bookmarks.toggle'), $bookData);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'status' => 'added',
            'is_bookmarked' => true,
        ]);

        $this->assertDatabaseHas('journal_bookmarks', [
            'user_id' => $this->student->id,
            'journal_identifier' => 'ol:OL999W',
            'title' => 'Enterprise Architecture at Work',
            'source' => 'openlibrary',
        ]);

        // 2. View in bookmarks page under books source filter
        $bookmarkViewResponse = $this->actingAs($this->student)
            ->get(route('repositories.bookmarks', ['source' => 'books']));

        $bookmarkViewResponse->assertOk();
        $bookmarkViewResponse->assertSee('Enterprise Architecture at Work');
        $bookmarkViewResponse->assertSee('Marc Lankhorst');
        $bookmarkViewResponse->assertSee('Buku & E-Book');
    }
}

<?php

namespace Tests\Unit;

use App\Services\JournalAiService;
use PHPUnit\Framework\TestCase;

class JournalAiServiceTest extends TestCase
{
    protected JournalAiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new JournalAiService();
    }

    public function test_extracts_clean_keywords_from_indonesian_thesis_title()
    {
        $title = 'Implementasi Algoritma Naive Bayes untuk Klasifikasi Sentimen Ulasan Pengguna pada Aplikasi Mobile';
        $res = $this->service->extractKeywords($title);

        $this->assertNotEmpty($res['query']);
        $this->assertStringContainsString('naive', strtolower($res['query']));
        $this->assertStringContainsString('bayes', strtolower($res['query']));
        $this->assertStringContainsString('sentimen', strtolower($res['query']));
        
        // Stopwords should be eliminated
        $this->assertStringNotContainsString('implementasi', strtolower($res['query']));
        $this->assertStringNotContainsString('untuk', strtolower($res['query']));
        $this->assertStringNotContainsString('pada', strtolower($res['query']));

        $this->assertContains('NAIVE', $res['chips']);
        $this->assertContains('BAYES', $res['chips']);
    }

    public function test_extracts_decision_support_system_keywords()
    {
        $title = 'Sistem Pendukung Keputusan Pemilihan Dosen Berprestasi Menggunakan Metode AHP dan TOPSIS pada Universitas Subang';
        $res = $this->service->extractKeywords($title);

        $this->assertNotEmpty($res['query']);
        $this->assertStringContainsString('ahp', strtolower($res['query']));
        $this->assertStringContainsString('topsis', strtolower($res['query']));
        $this->assertContains('AHP', $res['chips']);
        $this->assertContains('TOPSIS', $res['chips']);
        $this->assertStringNotContainsString('menggunakan', strtolower($res['query']));
        $this->assertStringNotContainsString('universitas', strtolower($res['query']));
    }

    public function test_generates_structured_tldr_from_abstract()
    {
        $abstract = "Penelitian ini bertujuan untuk menganalisis sentimen publik terhadap layanan transportasi online. Metode yang digunakan adalah algoritma Support Vector Machine dengan ekstraksi fitur TF-IDF. Hasil pengujian menunjukkan bahwa model mencapai akurasi sebesar 91.5% dan presisi 89.2%.";
        
        $tldr = $this->service->generateTldr($abstract, 'Sentimen Analisis');

        $this->assertTrue($tldr['has_tldr']);
        $this->assertNotEmpty($tldr['problem']);
        $this->assertNotEmpty($tldr['method']);
        $this->assertNotEmpty($tldr['result']);

        $this->assertStringContainsString('bertujuan', strtolower($tldr['problem']));
        $this->assertStringContainsString('metode', strtolower($tldr['method']));
        $this->assertStringContainsString('hasil', strtolower($tldr['result']));
    }

    public function test_extracts_similar_query_from_item()
    {
        $item = [
            'title' => 'Deep Learning for Medical Image Segmentation',
            'concepts' => [
                ['name' => 'Deep learning'],
                ['name' => 'Image segmentation'],
                ['name' => 'Convolutional neural network']
            ]
        ];

        $similarQuery = $this->service->extractSimilarQuery($item);
        $this->assertEquals('Deep learning Image segmentation Convolutional neural network', $similarQuery);
    }

    public function test_generates_standard_ris_format()
    {
        $item = [
            'title' => 'Sistem Rekomendasi Jurnal Ilmiah Menggunakan Pendekatan TF-IDF',
            'authors' => ['Bagus Ali Akbar', 'Ahmad Dani'],
            'year' => 2024,
            'venue' => 'Jurnal Teknologi Informasi',
            'volume' => '10',
            'issue' => '2',
            'pages' => '120-135',
            'doi' => 'https://doi.org/10.1234/jti.2024.102',
            'landing_page_url' => 'https://example.com/jti/paper120',
            'abstract' => 'Penelitian ini mengembangkan sistem rekomendasi...',
            'publisher' => 'FASILKOM UNSUB',
        ];

        $ris = $this->service->generateRis($item);

        $this->assertStringContainsString('TY  - JOUR', $ris);
        $this->assertStringContainsString('TI  - Sistem Rekomendasi Jurnal Ilmiah Menggunakan Pendekatan TF-IDF', $ris);
        $this->assertStringContainsString('AU  - Akbar, Bagus Ali', $ris);
        $this->assertStringContainsString('AU  - Dani, Ahmad', $ris);
        $this->assertStringContainsString('T2  - Jurnal Teknologi Informasi', $ris);
        $this->assertStringContainsString('PY  - 2024', $ris);
        $this->assertStringContainsString('VL  - 10', $ris);
        $this->assertStringContainsString('IS  - 2', $ris);
        $this->assertStringContainsString('SP  - 120', $ris);
        $this->assertStringContainsString('EP  - 135', $ris);
        $this->assertStringContainsString('DO  - 10.1234/jti.2024.102', $ris);
        $this->assertStringContainsString('UR  - https://example.com/jti/paper120', $ris);
        $this->assertStringContainsString('PB  - FASILKOM UNSUB', $ris);
        $this->assertStringContainsString('ER  - ', $ris);
    }

    public function test_generates_fasilkom_citation_format()
    {
        $item = [
            'title' => 'Implementasi Machine Learning untuk Prediksi Kelulusan Tepat Waktu',
            'authors' => ['Bagus Ali Akbar', 'Ahmad Dani', 'Citra Dewi'],
            'year' => 2023,
            'venue' => 'Jurnal FASILKOM UNSUB',
            'volume' => '5',
            'issue' => '1',
            'pages' => '45-55',
            'doi' => 'https://doi.org/10.9999/jfu.2023.51',
        ];

        $cit = $this->service->generateFasilkomCitation($item);

        $this->assertArrayHasKey('plain', $cit);
        $this->assertArrayHasKey('html', $cit);

        // Plain test
        $this->assertStringContainsString('Akbar, B. A.', $cit['plain']);
        $this->assertStringContainsString('Dani, A.', $cit['plain']);
        $this->assertStringContainsString('Dewi, C.', $cit['plain']);
        $this->assertStringContainsString('(2023).', $cit['plain']);
        $this->assertStringContainsString('"Implementasi Machine Learning untuk Prediksi Kelulusan Tepat Waktu".', $cit['plain']);
        $this->assertStringContainsString('Jurnal FASILKOM UNSUB', $cit['plain']);
        $this->assertStringContainsString('Vol. 5(No. 1)', $cit['plain']);
        $this->assertStringContainsString('hlm. 45-55', $cit['plain']);

        // HTML test (with italics)
        $this->assertStringContainsString('<i>Jurnal FASILKOM UNSUB</i>', $cit['html']);
        $this->assertStringContainsString('&ldquo;Implementasi Machine Learning untuk Prediksi Kelulusan Tepat Waktu&rdquo;', $cit['html']);
    }
}

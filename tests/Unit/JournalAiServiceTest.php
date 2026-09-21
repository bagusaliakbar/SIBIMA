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
}

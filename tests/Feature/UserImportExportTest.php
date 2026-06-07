<?php

namespace Tests\Feature;

use App\Models\User;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UserImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_export_contains_correct_headings_and_data()
    {
        // 1. Create dummy users
        $student = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@sibima.com',
            'role' => 'mahasiswa',
            'identifier' => '12345678',
            'entry_year' => 2021,
            'phone_number' => '081234567890',
            'is_active' => true,
        ]);

        $lecturer = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@sibima.com',
            'role' => 'dosen',
            'identifier' => '987654321',
            'entry_year' => null,
            'phone_number' => '089876543210',
            'is_active' => false,
        ]);

        $export = new UsersExport();

        // Verify headings count and items
        $headings = $export->headings();
        $this->assertCount(7, $headings);
        $this->assertEquals([
            'Nama',
            'Email',
            'Peran (dosen/mahasiswa)',
            'NPM/NIDN',
            'Tahun Angkatan',
            'No. Telepon',
            'Status Aktif (1=Aktif, 0=Pending)',
        ], $headings);

        // Verify mapped data
        $studentMap = $export->map($student);
        $this->assertCount(7, $studentMap);
        $this->assertEquals('John Doe', $studentMap[0]);
        $this->assertEquals('john@sibima.com', $studentMap[1]);
        $this->assertEquals('mahasiswa', $studentMap[2]);
        $this->assertEquals('12345678', $studentMap[3]);
        $this->assertEquals(2021, $studentMap[4]);
        $this->assertEquals('081234567890', $studentMap[5]);
        $this->assertEquals(1, $studentMap[6]);

        $lecturerMap = $export->map($lecturer);
        $this->assertCount(7, $lecturerMap);
        $this->assertEquals('Jane Smith', $lecturerMap[0]);
        $this->assertEquals('jane@sibima.com', $lecturerMap[1]);
        $this->assertEquals('dosen', $lecturerMap[2]);
        $this->assertEquals('987654321', $lecturerMap[3]);
        $this->assertNull($lecturerMap[4]);
        $this->assertEquals('089876543210', $lecturerMap[5]);
        $this->assertEquals(0, $lecturerMap[6]);
    }

    public function test_users_import_correctly_saves_all_fields()
    {
        $rows = new Collection([
            // Header Row (will be skipped)
            ['Nama', 'Email', 'Peran', 'NPM/NIDN', 'Tahun Angkatan', 'No. Telepon', 'Status Aktif'],
            // Mahasiswa Active Row
            ['Alice Cooper', 'alice@sibima.com', 'mahasiswa', '88881234', '2022', '08111222333', '1'],
            // Dosen Pending Row
            ['Bob Marley', 'bob@sibima.com', 'dosen', '99991234', '', '08222333444', '0'],
        ]);

        $import = new UsersImport();
        $import->collection($rows);

        $this->assertEquals(2, $import->importedCount);
        $this->assertEquals(0, $import->skippedCount);

        // Check Alice Cooper (mahasiswa)
        $alice = User::where('email', 'alice@sibima.com')->first();
        $this->assertNotNull($alice);
        $this->assertEquals('Alice Cooper', $alice->name);
        $this->assertEquals('mahasiswa', $alice->role);
        $this->assertEquals('88881234', $alice->identifier);
        $this->assertEquals(2022, $alice->entry_year);
        $this->assertEquals('08111222333', $alice->phone_number);
        $this->assertTrue($alice->is_active);

        // Check Bob Marley (dosen)
        $bob = User::where('email', 'bob@sibima.com')->first();
        $this->assertNotNull($bob);
        $this->assertEquals('Bob Marley', $bob->name);
        $this->assertEquals('dosen', $bob->role);
        $this->assertEquals('99991234', $bob->identifier);
        $this->assertNull($bob->entry_year); // Dosen shouldn't have entry year
        $this->assertEquals('08222333444', $bob->phone_number);
        $this->assertFalse($bob->is_active);
    }

    public function test_users_import_captures_skipped_details_for_invalid_and_duplicate_rows()
    {
        // Pre-create a user to test duplicates
        User::factory()->create([
            'email' => 'duplicate@sibima.com',
            'identifier' => '123456',
        ]);

        $rows = new Collection([
            // Header Row (will be skipped)
            ['Nama', 'Email', 'Peran', 'NPM/NIDN', 'Tahun Angkatan', 'No. Telepon', 'Status Aktif'],
            // Row 2: Invalid Role (admin is not a valid import role)
            ['Admin User', 'admin@sibima.com', 'admin', '555555', '', '', '1'],
            // Row 3: Missing columns
            ['John Doe', 'john@sibima.com'],
            // Row 4: Duplicate Email / NPM
            ['Duplicate User', 'duplicate@sibima.com', 'mahasiswa', '123456', '2020', '', '1'],
            // Row 5: Empty Name/Email
            ['', '', 'mahasiswa', '999999', '', '', '1'],
        ]);

        $import = new UsersImport();
        $import->collection($rows);

        $this->assertEquals(0, $import->importedCount);
        $this->assertEquals(4, $import->skippedCount);

        // Verify skippedDetails matches row numbers and failure reasons
        $this->assertCount(4, $import->skippedDetails);

        // Row 2 verification (Invalid role)
        $this->assertEquals(2, $import->skippedDetails[0]['row']);
        $this->assertEquals('555555', $import->skippedDetails[0]['identifier']);
        $this->assertStringContainsString("Peran 'admin' tidak valid", $import->skippedDetails[0]['reason']);

        // Row 3 verification (Incomplete columns)
        $this->assertEquals(3, $import->skippedDetails[1]['row']);
        $this->assertEquals('-', $import->skippedDetails[1]['identifier']);
        $this->assertStringContainsString("tidak lengkap", $import->skippedDetails[1]['reason']);

        // Row 4 verification (Duplicate)
        $this->assertEquals(4, $import->skippedDetails[2]['row']);
        $this->assertEquals('123456', $import->skippedDetails[2]['identifier']);
        $this->assertStringContainsString("sudah terdaftar", $import->skippedDetails[2]['reason']);

        // Row 5 verification (Empty name/email)
        $this->assertEquals(5, $import->skippedDetails[3]['row']);
        $this->assertEquals('999999', $import->skippedDetails[3]['identifier']);
        $this->assertStringContainsString("kosong", $import->skippedDetails[3]['reason']);
    }
}

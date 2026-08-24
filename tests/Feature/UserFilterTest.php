<?php

namespace Tests\Feature;

use App\Models\User;
use App\Exports\UsersExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $student = User::factory()->create([
            'name' => 'Student User',
            'role' => 'mahasiswa',
            'identifier' => 'MHS001',
            'entry_year' => 2024,
            'is_active' => true,
        ]);

        $lecturer = User::factory()->create([
            'name' => 'Lecturer User',
            'role' => 'dosen',
            'identifier' => 'DSN001',
            'is_active' => true,
        ]);

        $kaprodi = User::factory()->create([
            'name' => 'Kaprodi User',
            'role' => 'kaprodi',
            'identifier' => 'KPR001',
            'is_active' => true,
        ]);

        // 1. Filter Mahasiswa
        $responseMhs = $this->actingAs($this->admin)->get(route('users.index', ['role' => 'mahasiswa']));
        $responseMhs->assertStatus(200);
        $responseMhs->assertSee('Student User');
        $responseMhs->assertDontSee('Lecturer User');
        $responseMhs->assertDontSee('Kaprodi User');

        // 2. Filter Dosen
        $responseDsn = $this->actingAs($this->admin)->get(route('users.index', ['role' => 'dosen']));
        $responseDsn->assertStatus(200);
        $responseDsn->assertSee('Lecturer User');
        $responseDsn->assertDontSee('Student User');
        $responseDsn->assertDontSee('Kaprodi User');

        // 3. Filter Kaprodi
        $responseKpr = $this->actingAs($this->admin)->get(route('users.index', ['role' => 'kaprodi']));
        $responseKpr->assertStatus(200);
        $responseKpr->assertSee('Kaprodi User');
        $responseKpr->assertDontSee('Student User');
        $responseKpr->assertDontSee('Lecturer User');
    }

    public function test_admin_can_filter_users_by_status(): void
    {
        $activeUser = User::factory()->create([
            'name' => 'Active Person',
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        $pendingUser = User::factory()->create([
            'name' => 'Pending Person',
            'role' => 'mahasiswa',
            'is_active' => false,
        ]);

        // Filter Active
        $responseActive = $this->actingAs($this->admin)->get(route('users.index', ['status' => 'active']));
        $responseActive->assertStatus(200);
        $responseActive->assertSee('Active Person');
        $responseActive->assertDontSee('Pending Person');

        // Filter Pending
        $responsePending = $this->actingAs($this->admin)->get(route('users.index', ['status' => 'pending']));
        $responsePending->assertStatus(200);
        $responsePending->assertSee('Pending Person');
        $responsePending->assertDontSee('Active Person');
    }

    public function test_admin_can_filter_students_by_cohort_and_entry_year(): void
    {
        $newStudent = User::factory()->create([
            'name' => 'New Cohort Student',
            'role' => 'mahasiswa',
            'entry_year' => now()->year,
            'is_active' => true,
        ]);

        $oldStudent = User::factory()->create([
            'name' => 'Old Cohort Student',
            'role' => 'mahasiswa',
            'entry_year' => 2018,
            'is_active' => true,
        ]);

        // Filter New Cohort
        $responseNew = $this->actingAs($this->admin)->get(route('users.index', ['cohort_filter' => 'new', 'role' => 'mahasiswa']));
        $responseNew->assertStatus(200);
        $responseNew->assertSee('New Cohort Student');
        $responseNew->assertDontSee('Old Cohort Student');

        // Filter Old Cohort
        $responseOld = $this->actingAs($this->admin)->get(route('users.index', ['cohort_filter' => 'old', 'role' => 'mahasiswa']));
        $responseOld->assertStatus(200);
        $responseOld->assertSee('Old Cohort Student');
        $responseOld->assertDontSee('New Cohort Student');

        // Filter Specific Entry Year
        $responseSpecific = $this->actingAs($this->admin)->get(route('users.index', ['entry_year' => '2018', 'role' => 'mahasiswa']));
        $responseSpecific->assertStatus(200);
        $responseSpecific->assertSee('Old Cohort Student');
        $responseSpecific->assertDontSee('New Cohort Student');
    }

    public function test_admin_can_paginate_with_custom_per_page(): void
    {
        User::factory()->count(15)->create([
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        $response10 = $this->actingAs($this->admin)->get(route('users.index', ['per_page' => 10]));
        $response10->assertStatus(200);
        $this->assertCount(10, $response10->viewData('users'));

        $response25 = $this->actingAs($this->admin)->get(route('users.index', ['per_page' => 25]));
        $response25->assertStatus(200);
        $this->assertCount(15, $response25->viewData('users'));
    }

    public function test_export_respects_filter_parameters(): void
    {
        $student = User::factory()->create([
            'name' => 'Export Student',
            'role' => 'mahasiswa',
            'entry_year' => 2024,
            'is_active' => true,
        ]);

        $lecturer = User::factory()->create([
            'name' => 'Export Lecturer',
            'role' => 'dosen',
            'is_active' => true,
        ]);

        // Export with role=dosen filter
        $exportDosen = new UsersExport(null, 'all', 'dosen');
        $collectionDosen = $exportDosen->collection();
        $this->assertTrue($collectionDosen->contains('id', $lecturer->id));
        $this->assertFalse($collectionDosen->contains('id', $student->id));

        // Export with role=mahasiswa filter
        $exportMhs = new UsersExport(null, 'all', 'mahasiswa');
        $collectionMhs = $exportMhs->collection();
        $this->assertTrue($collectionMhs->contains('id', $student->id));
        $this->assertFalse($collectionMhs->contains('id', $lecturer->id));

        // Endpoint test
        $response = $this->actingAs($this->admin)->get(route('users.export', ['role' => 'dosen']));
        $response->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use App\Models\SeminarApplication;
use App\Models\Thesis;
use App\Models\ThesisDefenseApplication;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelApplicationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_cancel_and_delete_seminar_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Skripsi Seminar',
            'status' => 'acc_up',
        ]);

        $seminarApp = SeminarApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'status' => 'pending',
            'file_acc_pembimbing' => 'seminar/acc.pdf',
            'file_pembayaran' => 'seminar/pay.pdf',
            'file_kartu_bimbingan' => 'seminar/card.pdf',
            'file_skripsi' => 'seminar/thesis.pdf',
            'file_formulir' => 'seminar/form.pdf',
        ]);

        $this->assertDatabaseHas('seminar_applications', ['id' => $seminarApp->id]);

        $response = $this->actingAs($admin)->delete(route('seminar-applications.destroy', $seminarApp));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('seminar_applications', ['id' => $seminarApp->id]);
    }

    public function test_admin_can_cancel_and_delete_thesis_defense_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Skripsi Sidang',
            'status' => 'acc_kompre',
        ]);

        $defenseApp = ThesisDefenseApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'status' => 'rejected',
            'file_formulir' => 'defense/form.pdf',
            'file_transkrip' => 'defense/transkrip.pdf',
            'file_acc_pembimbing' => 'defense/acc.pdf',
            'file_logbook' => 'defense/log.pdf',
            'file_pembayaran' => 'defense/pay.pdf',
            'file_skripsi' => 'defense/thesis.pdf',
            'file_ktm' => 'defense/ktm.pdf',
            'file_pkkmb_univ' => 'defense/pkkmb.pdf',
            'file_pkkmb_fak' => 'defense/pkkmb_fak.pdf',
            'file_makrab' => 'defense/makrab.pdf',
            'file_cisco' => 'defense/cisco.pdf',
            'file_workshop' => 'defense/workshop.pdf',
            'file_organisasi' => 'defense/org.pdf',
            'file_toefl' => 'defense/toefl.pdf',
            'file_kewirausahaan' => 'defense/kwu.pdf',
            'file_tahsin' => 'defense/tahsin.pdf',
            'file_komputer' => 'defense/komp.pdf',
            'file_perpus_pinjam' => 'defense/pinjam.pdf',
            'file_perpus_sumbang' => 'defense/sumbang.pdf',
            'file_ijazah' => 'defense/ijazah.pdf',
        ]);

        $this->assertDatabaseHas('thesis_defense_applications', ['id' => $defenseApp->id]);

        $response = $this->actingAs($admin)->delete(route('thesis-defense-applications.destroy', $defenseApp));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('thesis_defense_applications', ['id' => $defenseApp->id]);
    }
}

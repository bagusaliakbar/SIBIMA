<?php

namespace App\Services;

use App\Models\Thesis;
use App\Models\User;
use App\Models\ActivityLog;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ThesisService
{
    /**
     * Create a new thesis application.
     */
    public function createThesis(array $data)
    {
        $thesis = Thesis::create([
            'student_id'               => Auth::id(),
            'title'                    => $data['title'],
            'abstract'                 => $data['abstract'],
            'requested_pembimbing1_id' => $data['requested_pembimbing1_id'] ?? null,
            'requested_pembimbing2_id' => $data['requested_pembimbing2_id'] ?? null,
            'status'                   => 'pending',
        ]);

        ActivityLog::log('Pengajuan Judul', "Mahasiswa mengajukan judul skripsi baru: {$data['title']}", 'Skripsi', $thesis, [
            'title' => $data['title'],
            'status' => 'pending'
        ]);

        // Notify Admins & Kaprodi
        $admins = User::whereIn('role', ['admin', 'kaprodi'])->get();
        Notification::send($admins, new GeneralNotification(
            'Pengajuan Judul Baru',
            "Mahasiswa " . Auth::user()->name . " mengajukan judul skripsi baru.",
            route('theses.index'),
            'info'
        ));

        return $thesis;
    }

    /**
     * Assign supervisors to a thesis.
     */
    public function assignPembimbing(Thesis $thesis, array $data)
    {
        $thesis->update([
            'pembimbing1_id' => $data['pembimbing1_id'],
            'pembimbing2_id' => $data['pembimbing2_id'],
            'status' => 'active',
        ]);

        // Define relations first so they are available for logging and notifications
        $student = $thesis->student;
        $p1 = $thesis->pembimbing1;
        $p2 = $thesis->pembimbing2;

        ActivityLog::log('Penugasan Pembimbing', "Admin menetapkan pembimbing dan mengaktifkan status skripsi untuk mahasiswa {$student->name}.", 'Skripsi', $thesis, [
            'pembimbing1' => $p1->name,
            'pembimbing2' => $p2->name,
            'status' => 'active'
        ]);

        // Notify Student & Dosens

        if ($student) {
            $student->notify(new GeneralNotification(
                'Dosen Pembimbing Ditetapkan',
                "Dosen pembimbing Anda telah ditetapkan: {$p1->name} dan {$p2->name}.",
                route('dashboard'),
                'success'
            ));
        }

        Notification::send([$p1, $p2], new GeneralNotification(
            'Penugasan Pembimbing Baru',
            "Anda telah ditugaskan sebagai pembimbing skripsi mahasiswa {$student->name}.",
            route('theses.index'),
            'info'
        ));
    }

    /**
     * Update thesis data.
     */
    public function updateThesis(Thesis $thesis, array $data)
    {
        $thesis->update([
            'final_title' => $data['final_title'],
            'pembimbing1_id' => $data['pembimbing1_id'],
            'pembimbing2_id' => $data['pembimbing2_id'],
        ]);
    }

    /**
     * Toggle ACC status for a thesis.
     */
    public function toggleAcc(Thesis $thesis, string $type)
    {
        $user = Auth::user();
        $column = $this->getAccColumn($thesis, $user->id, $type);

        if (!$column) {
            throw new \Exception('Anda tidak memiliki otoritas untuk memberikan ACC pada mahasiswa ini.');
        }

        $thesis->$column = !$thesis->$column;
        $thesis->save();

        $typeName = $type === 'up' ? 'Seminar UP' : 'Sidang Akhir';
        $statusText = $thesis->$column ? 'memberikan' : 'membatalkan';

        ActivityLog::log('ACC Bimbingan', "Dosen {$user->name} {$statusText} ACC {$typeName} untuk mahasiswa {$thesis->student->name}.", 'Skripsi', $thesis, [
            'type' => $type,
            'action' => $statusText,
            'dosen' => $user->name
        ]);
        
        if ($thesis->$column) {
            $thesis->student->notify(new GeneralNotification(
                'ACC Pembimbing',
                "Pembimbing " . $user->name . " telah memberikan ACC untuk {$typeName}.",
                route('mentoring-sessions.index'),
                'success'
            ));
        }

        return $thesis->$column ? 'diberikan' : 'dibatalkan';
    }

    private function getAccColumn(Thesis $thesis, int $userId, string $type)
    {
        if ($userId === $thesis->pembimbing1_id) {
            return $type === 'up' ? 'acc_up_p1' : 'acc_sidang_p1';
        } elseif ($userId === $thesis->pembimbing2_id) {
            return $type === 'up' ? 'acc_up_p2' : 'acc_sidang_p2';
        }
        return null;
    }
}

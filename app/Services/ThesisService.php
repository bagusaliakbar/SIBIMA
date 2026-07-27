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
        $studentId = $data['student_id'] ?? Auth::id();
        $student = User::find($studentId);

        $thesis = Thesis::create([
            'student_id'               => $studentId,
            'title'                    => $data['title'],
            'abstract'                 => $data['abstract'],
            'requested_pembimbing1_id' => $data['requested_pembimbing1_id'] ?? null,
            'requested_pembimbing2_id' => $data['requested_pembimbing2_id'] ?? null,
            'status'                   => 'pending',
        ]);

        $submitterName = Auth::user()->name;
        ActivityLog::log('Pengajuan Judul', "Pengajuan judul skripsi baru untuk {$student->name}: {$data['title']} (Diajukan oleh {$submitterName})", 'Skripsi', $thesis, [
            'title' => $data['title'],
            'status' => 'pending'
        ]);

        // Notify Admins & Kaprodi
        $admins = User::whereIn('role', ['admin', 'kaprodi'])->get();
        Notification::send($admins, new GeneralNotification(
            'Pengajuan Judul Baru',
            "Pengajuan judul skripsi baru untuk mahasiswa " . $student->name . ".",
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
    public function toggleAcc(Thesis $thesis, string $type, ?string $slot = null)
    {
        $user = Auth::user();
        
        if (in_array($user->role, ['admin', 'kaprodi'])) {
            $targetSlot = $slot ?? request('slot', 'p1');
            if ($targetSlot === 'p2') {
                $column = $type === 'up' ? 'acc_up_p2' : 'acc_sidang_p2';
            } elseif ($targetSlot === 'all') {
                $col1 = $type === 'up' ? 'acc_up_p1' : 'acc_sidang_p1';
                $col2 = $type === 'up' ? 'acc_up_p2' : 'acc_sidang_p2';
                $newVal = !($thesis->$col1 && $thesis->$col2);
                $thesis->$col1 = $newVal;
                $thesis->$col2 = $newVal;
                $thesis->save();

                $typeName = $type === 'up' ? 'Seminar UP' : 'Sidang Akhir';
                $statusText = $newVal ? 'memberikan' : 'membatalkan';
                ActivityLog::log('ACC Bimbingan', "{$user->role} ({$user->name}) {$statusText} ACC {$typeName} (P1 & P2) untuk mahasiswa {$thesis->student->name}.", 'Skripsi', $thesis);
                return $newVal ? 'diberikan' : 'dibatalkan';
            } else {
                $column = $type === 'up' ? 'acc_up_p1' : 'acc_sidang_p1';
            }
        } else {
            $column = $this->getAccColumn($thesis, $user->id, $type);
        }

        if (!$column) {
            throw new \Exception('Anda tidak memiliki otoritas untuk memberikan ACC pada mahasiswa ini.');
        }

        $thesis->$column = !$thesis->$column;
        $thesis->save();

        $typeName = $type === 'up' ? 'Seminar UP' : 'Sidang Akhir';
        $statusText = $thesis->$column ? 'memberikan' : 'membatalkan';

        ActivityLog::log('ACC Bimbingan', "{$user->name} ({$user->role}) {$statusText} ACC {$typeName} untuk mahasiswa {$thesis->student->name}.", 'Skripsi', $thesis, [
            'type' => $type,
            'action' => $statusText,
            'user' => $user->name
        ]);
        
        if ($thesis->$column) {
            $thesis->student->notify(new GeneralNotification(
                'ACC Pembimbing',
                "ACC untuk {$typeName} telah diberikan.",
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

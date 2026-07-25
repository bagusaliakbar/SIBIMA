<?php

namespace App\Services;

use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MentoringService
{
    /**
     * Store a new mentoring session.
     */
    public function storeSession(array $data)
    {
        $user = Auth::user();

        if ($user->role === 'dosen') {
            return $this->handleDosenStore($data);
        } else {
            return $this->handleStudentStore($data);
        }
    }

    private function handleDosenStore(array $data)
    {
        if ($data['thesis_id'] === 'all') {
            $theses = Thesis::where(function($q) {
                    $q->where('pembimbing1_id', Auth::id())
                      ->orWhere('pembimbing2_id', Auth::id());
                })
                ->where('status', 'active')
                ->get();
            
            if ($theses->isEmpty()) {
                throw new \Exception('Anda tidak memiliki mahasiswa bimbingan yang aktif.');
            }
            
            foreach ($theses as $thesis) {
                $session = $this->createSessionForThesis($thesis, $data, 'approved');
                
                ActivityLog::log('Jadwal Bimbingan Massal', "Dosen menjadwalkan bimbingan untuk {$thesis->student->name}: {$data['topic']}", 'Bimbingan', $session);

                $thesis->student->notify(new GeneralNotification(
                    'Jadwal Bimbingan Baru',
                    "Dosen " . Auth::user()->name . " menjadwalkan bimbingan baru: {$data['topic']}",
                    route('mentoring-sessions.index'),
                    'info'
                ));
            }
            
            return ['count' => $theses->count(), 'type' => 'mass'];
        } else {
            $thesis = Thesis::findOrFail($data['thesis_id']);
            
            if ($thesis->pembimbing1_id !== Auth::id() && $thesis->pembimbing2_id !== Auth::id()) {
                throw new \Exception('Unauthorized access to thesis.', 403);
            }
            
            $scheduledAt = \Carbon\Carbon::parse($data['scheduled_at'])->format('Y-m-d H:i:00');
            $data['scheduled_at'] = $scheduledAt;

            $existingDosenSession = MentoringSession::where('dosen_id', Auth::id())
                ->where('scheduled_at', $scheduledAt)
                ->where('status', '!=', 'rejected')
                ->first();

            if ($existingDosenSession) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'scheduled_at' => 'Anda sudah memiliki jadwal bimbingan lain pada tanggal dan jam tersebut.',
                ]);
            }

            $existingStudentSession = MentoringSession::whereHas('thesis', function($q) use ($thesis) {
                    $q->where('student_id', $thesis->student_id);
                })
                ->where('scheduled_at', $scheduledAt)
                ->where('status', '!=', 'rejected')
                ->first();

            if ($existingStudentSession) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'scheduled_at' => "Mahasiswa {$thesis->student->name} sudah memiliki jadwal bimbingan lain pada tanggal dan jam tersebut.",
                ]);
            }

            $session = $this->createSessionForThesis($thesis, $data, 'approved');

            ActivityLog::log('Jadwal Bimbingan', "Dosen menjadwalkan bimbingan untuk {$thesis->student->name}: {$data['topic']}", 'Bimbingan', $session);

            $thesis->student->notify(new GeneralNotification(
                'Jadwal Bimbingan Baru',
                "Dosen " . Auth::user()->name . " menjadwalkan bimbingan: {$data['topic']}",
                route('mentoring-sessions.index'),
                'info'
            ));
            
            return ['type' => 'single'];
        }
    }

    private function handleStudentStore(array $data)
    {
        $thesis = Thesis::where('student_id', Auth::id())->firstOrFail();
        
        $scheduledAt = \Carbon\Carbon::parse($data['scheduled_at'])->format('Y-m-d H:i:00');

        $existingStudentSession = MentoringSession::whereHas('thesis', function($q) {
                $q->where('student_id', Auth::id());
            })
            ->where('scheduled_at', $scheduledAt)
            ->where('status', '!=', 'rejected')
            ->first();

        if ($existingStudentSession) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'scheduled_at' => 'Anda sudah memiliki jadwal bimbingan lain pada tanggal dan jam yang sama.',
            ]);
        }

        $existingDosenSession = MentoringSession::where('dosen_id', $data['dosen_id'])
            ->where('scheduled_at', $scheduledAt)
            ->where('status', '!=', 'rejected')
            ->first();

        if ($existingDosenSession) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'scheduled_at' => 'Dosen pembimbing yang Anda pilih sudah memiliki jadwal bimbingan lain pada tanggal dan jam tersebut.',
            ]);
        }

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $data['dosen_id'],
            'scheduled_at' => $scheduledAt,
            'topic' => $data['topic'],
            'type' => $data['type'],
            'location' => $data['location'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        ActivityLog::log('Pengajuan Bimbingan', "Mahasiswa mengajukan bimbingan: {$data['topic']}", 'Bimbingan', $session);

        $dosen = User::find($data['dosen_id']);
        $dosen->notify(new GeneralNotification(
            'Pengajuan Bimbingan',
            "Mahasiswa " . Auth::user()->name . " mengajukan bimbingan: {$data['topic']}",
            route('mentoring-sessions.index'),
            'info'
        ));
        
        return ['type' => 'student'];
    }

    private function createSessionForThesis(Thesis $thesis, array $data, string $status)
    {
        return MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => Auth::id(),
            'scheduled_at' => $data['scheduled_at'],
            'topic' => $data['topic'],
            'type' => $data['type'],
            'location' => $data['location'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $status,
        ]);
    }

    /**
     * Update status of a mentoring session.
     */
    public function updateStatus(MentoringSession $session, array $data)
    {
        if ($data['status'] === 'absent') {
            $session->update([
                'status' => 'completed',
                'is_absent' => true,
                'feedback' => $data['feedback'] ?? null,
            ]);
            $message = 'Sesi bimbingan ditandai sebagai: Tidak Hadir.';
        } else {
            $session->update([
                'status' => $data['status'],
                'feedback' => $data['feedback'] ?? $session->feedback,
            ]);

            $session->thesis->student->notify(new GeneralNotification(
                'Status Bimbingan Diperbarui',
                "Status bimbingan Anda ({$session->topic}) diperbarui menjadi: " . strtoupper($data['status']),
                route('mentoring-sessions.index'),
                $data['status'] === 'approved' ? 'success' : ($data['status'] === 'rejected' ? 'danger' : 'info')
            ));

            $message = 'Status sesi bimbingan diperbarui menjadi: ' . ucfirst($data['status']);
        }

        ActivityLog::log('Update Status Bimbingan', "Dosen memperbarui status bimbingan ({$session->topic}) menjadi: " . strtoupper($data['status']), 'Bimbingan', $session);

        return $message;
    }

    /**
     * Upload document for a mentoring session.
     */
    public function uploadDocument(MentoringSession $session, $url)
    {
        if (!$url) {
            throw new \Exception('Link Google Drive tidak ditemukan.');
        }

        if ($session->document_path && !filter_var($session->document_path, FILTER_VALIDATE_URL) && Storage::disk(config('filesystems.default'))->exists($session->document_path)) {
            Storage::disk(config('filesystems.default'))->delete($session->document_path);
        }

        $session->update([
            'document_path'          => $url,
            'document_original_name' => 'Link Google Drive',
        ]);

        try {
            ActivityLog::log('Upload Dokumen Bimbingan', "Mahasiswa melampirkan link dokumen bimbingan", 'Bimbingan', $session);
        } catch (\Exception $e) {
            // Abaikan error log aktivitas jika terjadi masalah (misal database atau koneksi)
        }

        return 'Link Google Drive';
    }

    /**
     * Delete document from a mentoring session.
     */
    public function deleteDocument(MentoringSession $session)
    {
        if ($session->document_path && !filter_var($session->document_path, FILTER_VALIDATE_URL) && Storage::disk('local')->exists($session->document_path)) {
            Storage::disk('local')->delete($session->document_path);
        }

        $session->update([
            'document_path'          => null,
            'document_original_name' => null,
        ]);
    }
}

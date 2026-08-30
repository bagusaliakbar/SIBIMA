<?php

namespace App\Services;

use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Notifications\MentoringScheduledByDosenNotification;
use App\Notifications\MentoringRescheduledNotification;
use App\Notifications\MentoringCancelledNotification;
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

        if (in_array($user->role, ['dosen', 'admin', 'kaprodi'])) {
            return $this->handleDosenStore($data);
        } else {
            return $this->handleStudentStore($data);
        }
    }

    /**
     * Update / Reschedule an existing mentoring session (Single or Group).
     */
    public function updateSession(MentoringSession $session, array $data)
    {
        $user = Auth::user();

        if ($session->status === 'completed') {
            throw new \Exception('Sesi bimbingan yang sudah selesai tidak dapat diubah / dijadwalkan ulang.');
        }

        $scheduledAt = \Carbon\Carbon::parse($data['scheduled_at'])->format('Y-m-d H:i:00');
        $data['scheduled_at'] = $scheduledAt;
        $targetDosenId = $session->dosen_id ?? Auth::id();
        $applyToGroup = !empty($data['apply_to_group']);

        if ($applyToGroup) {
            // Find all peer sessions of this lecturer at the exact same original scheduled_at (not completed)
            $groupSessions = MentoringSession::where('dosen_id', $targetDosenId)
                ->where('scheduled_at', $session->scheduled_at)
                ->where('status', '!=', 'completed')
                ->with('thesis.student')
                ->get();
            
            if (!$groupSessions->contains('id', $session->id)) {
                $groupSessions->push($session);
            }

            $groupSessionIds = $groupSessions->pluck('id')->toArray();

            // 1. Check Dosen conflict (excluding all group sessions)
            $existingDosenSession = MentoringSession::where('dosen_id', $targetDosenId)
                ->whereNotIn('id', $groupSessionIds)
                ->where('scheduled_at', $scheduledAt)
                ->where('status', '!=', 'rejected')
                ->first();

            if ($existingDosenSession) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'scheduled_at' => 'Terdapat jadwal bimbingan lain pada tanggal dan jam tersebut untuk dosen pembimbing ini.',
                ]);
            }

            // 2. Check Student conflict for each student in the group (excluding this group's sessions)
            foreach ($groupSessions as $groupSession) {
                $stThesis = $groupSession->thesis;
                if ($stThesis) {
                    $existingStudentSession = MentoringSession::whereHas('thesis', function($q) use ($stThesis) {
                            $q->where('student_id', $stThesis->student_id);
                        })
                        ->whereNotIn('id', $groupSessionIds)
                        ->where('scheduled_at', $scheduledAt)
                        ->where('status', '!=', 'rejected')
                        ->first();

                    if ($existingStudentSession) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'scheduled_at' => "Mahasiswa {$stThesis->student->name} sudah memiliki jadwal bimbingan lain pada tanggal dan jam tersebut.",
                        ]);
                    }
                }
            }

            $timeChanged = $session->scheduled_at->format('Y-m-d H:i:00') !== $scheduledAt;
            $oldDate = $session->scheduled_at->format('d/m/Y H:i');

            // Update each session in the group
            foreach ($groupSessions as $groupSession) {
                $groupSession->update([
                    'scheduled_at' => $scheduledAt,
                    'topic'        => $data['topic'],
                    'type'         => $data['type'],
                    'location'     => $data['location'] ?? null,
                    'notes'        => $data['notes'] ?? null,
                    'student_attendance_status' => ($timeChanged && in_array($user->role, ['dosen', 'admin', 'kaprodi'])) ? 'pending' : $groupSession->student_attendance_status,
                    'student_attendance_reason' => ($timeChanged && in_array($user->role, ['dosen', 'admin', 'kaprodi'])) ? null : $groupSession->student_attendance_reason,
                    'student_confirmed_at'      => ($timeChanged && in_array($user->role, ['dosen', 'admin', 'kaprodi'])) ? null : $groupSession->student_confirmed_at,
                ]);

                if ($groupSession->thesis?->student) {
                    $groupSession->thesis->student->notify(new MentoringRescheduledNotification($groupSession));
                }
            }

            ActivityLog::log(
                'Reschedule Bimbingan Bersama', 
                "{$user->name} memperbarui jadwal bimbingan bersama untuk {$groupSessions->count()} mahasiswa (dari {$oldDate} menjadi " . \Carbon\Carbon::parse($scheduledAt)->format('d/m/Y H:i') . "): {$data['topic']}", 
                'Bimbingan', 
                $session
            );

            return ['type' => 'group', 'count' => $groupSessions->count(), 'session' => $session];
        }

        // Single session update
        // 1. Check Dosen conflict (excluding this session)
        $existingDosenSession = MentoringSession::where('dosen_id', $targetDosenId)
            ->where('id', '!=', $session->id)
            ->where('scheduled_at', $scheduledAt)
            ->where('status', '!=', 'rejected')
            ->first();

        if ($existingDosenSession) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'scheduled_at' => 'Terdapat jadwal bimbingan lain pada tanggal dan jam tersebut untuk dosen pembimbing ini.',
            ]);
        }

        // 2. Check Student conflict (excluding this session)
        $thesis = $session->thesis;
        if ($thesis) {
            $existingStudentSession = MentoringSession::whereHas('thesis', function($q) use ($thesis) {
                    $q->where('student_id', $thesis->student_id);
                })
                ->where('id', '!=', $session->id)
                ->where('scheduled_at', $scheduledAt)
                ->where('status', '!=', 'rejected')
                ->first();

            if ($existingStudentSession) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'scheduled_at' => "Mahasiswa {$thesis->student->name} sudah memiliki jadwal bimbingan lain pada tanggal dan jam tersebut.",
                ]);
            }
        }

        $timeChanged = $session->scheduled_at->format('Y-m-d H:i:00') !== $scheduledAt;
        $oldDate = $session->scheduled_at->format('d/m/Y H:i');

        // Update fields
        $session->update([
            'scheduled_at' => $scheduledAt,
            'topic'        => $data['topic'],
            'type'         => $data['type'],
            'location'     => $data['location'] ?? null,
            'notes'        => $data['notes'] ?? null,
            'student_attendance_status' => ($timeChanged && in_array($user->role, ['dosen', 'admin', 'kaprodi'])) ? 'pending' : $session->student_attendance_status,
            'student_attendance_reason' => ($timeChanged && in_array($user->role, ['dosen', 'admin', 'kaprodi'])) ? null : $session->student_attendance_reason,
            'student_confirmed_at'      => ($timeChanged && in_array($user->role, ['dosen', 'admin', 'kaprodi'])) ? null : $session->student_confirmed_at,
        ]);

        ActivityLog::log(
            'Reschedule Bimbingan', 
            "{$user->name} memperbarui jadwal bimbingan {$thesis->student->name} (dari {$oldDate} menjadi " . \Carbon\Carbon::parse($scheduledAt)->format('d/m/Y H:i') . "): {$data['topic']}", 
            'Bimbingan', 
            $session
        );

        // Send notifications if rescheduled
        if ($thesis && $thesis->student) {
            $thesis->student->notify(new MentoringRescheduledNotification($session));
        }

        return ['type' => 'single', 'count' => 1, 'session' => $session];
    }

    private function handleDosenStore(array $data)
    {
        $user = Auth::user();
        $scheduledAt = \Carbon\Carbon::parse($data['scheduled_at'])->format('Y-m-d H:i:00');
        $data['scheduled_at'] = $scheduledAt;

        // Normalize thesis IDs from either thesis_ids array or thesis_id string
        $thesisIds = $data['thesis_ids'] ?? (isset($data['thesis_id']) ? (is_array($data['thesis_id']) ? $data['thesis_id'] : [$data['thesis_id']]) : []);

        $isAll = in_array('all', $thesisIds);

        if ($isAll) {
            $thesesQuery = Thesis::with('student')->where('status', 'active');
            if ($user->role === 'dosen') {
                $thesesQuery->where(function($q) {
                    $q->where('pembimbing1_id', Auth::id())
                      ->orWhere('pembimbing2_id', Auth::id());
                });
            }
            $theses = $thesesQuery->get();
        } else {
            $theses = Thesis::with('student')->whereIn('id', $thesisIds)->where('status', 'active')->get();
        }

        if ($theses->isEmpty()) {
            throw new \Exception('Tidak ada mahasiswa bimbingan aktif yang dipilih.');
        }

        // 1. Check Dosen conflict at this time slot
        $targetDosenId = ($user->role === 'dosen') ? Auth::id() : null;
        if (in_array($user->role, ['admin', 'kaprodi']) && !empty($data['dosen_id']) && !in_array($data['dosen_id'], ['p1', 'p2'])) {
            $targetDosenId = $data['dosen_id'];
        }

        if ($targetDosenId) {
            $existingDosenSession = MentoringSession::where('dosen_id', $targetDosenId)
                ->where('scheduled_at', $scheduledAt)
                ->where('status', '!=', 'rejected')
                ->first();

            if ($existingDosenSession) {
                $dosenName = User::find($targetDosenId)?->name ?? 'Dosen yang dipilih';
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'scheduled_at' => ($user->role === 'dosen') 
                        ? 'Anda sudah memiliki jadwal bimbingan lain pada tanggal dan jam tersebut.' 
                        : "{$dosenName} sudah memiliki jadwal bimbingan lain pada tanggal dan jam tersebut.",
                ]);
            }
        }

        // 2. Check each student for conflicting sessions at scheduled_at
        foreach ($theses as $thesis) {
            if ($user->role === 'dosen' && $thesis->pembimbing1_id !== Auth::id() && $thesis->pembimbing2_id !== Auth::id()) {
                throw new \Exception("Akses ditolak untuk skripsi mahasiswa {$thesis->student->name}.", 403);
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
        }

        // 3. Create mentoring session for each student
        $createdSessions = collect();
        $isGroup = $theses->count() > 1;

        foreach ($theses as $thesis) {
            $session = $this->createSessionForThesis($thesis, $data, 'approved');
            $createdSessions->push($session);

            ActivityLog::log(
                $isGroup ? 'Jadwal Bimbingan Kelompok' : 'Jadwal Bimbingan', 
                "{$user->name} menjadwalkan bimbingan untuk {$thesis->student->name}: {$data['topic']}", 
                'Bimbingan', 
                $session
            );

            if ($thesis->student) {
                $thesis->student->notify(new MentoringScheduledByDosenNotification($session));
            }
        }

        if ($isGroup) {
            return ['count' => $theses->count(), 'type' => 'mass'];
        }

        return ['count' => 1, 'type' => 'single', 'session' => $createdSessions->first()];
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
            'student_attendance_status' => 'attending',
            'student_confirmed_at' => now(),
        ]);

        ActivityLog::log('Pengajuan Bimbingan', "Mahasiswa mengajukan bimbingan: {$data['topic']}", 'Bimbingan', $session);

        $dosen = User::find($data['dosen_id']);
        $dosen->notify(new GeneralNotification(
            'Pengajuan Bimbingan',
            "Mahasiswa " . Auth::user()->name . " mengajukan bimbingan: {$data['topic']}",
            route('mentoring-sessions.index'),
            'info'
        ));
        
        $dosen->notify(new \App\Notifications\MentoringRequested($session));
        
        return ['type' => 'student'];
    }

    private function createSessionForThesis(Thesis $thesis, array $data, string $status)
    {
        if (in_array(Auth::user()->role, ['admin', 'kaprodi'])) {
            if (!empty($data['dosen_id'])) {
                if ($data['dosen_id'] === 'p2') {
                    $dosenId = $thesis->pembimbing2_id ?? $thesis->pembimbing1_id ?? Auth::id();
                } else {
                    $dosenId = $data['dosen_id'];
                }
            } else {
                $dosenId = $thesis->pembimbing1_id ?? $thesis->pembimbing2_id ?? Auth::id();
            }
        } else {
            $dosenId = Auth::id();
        }

        return MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosenId,
            'scheduled_at' => $data['scheduled_at'],
            'topic' => $data['topic'],
            'type' => $data['type'],
            'location' => $data['location'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $status,
            'student_attendance_status' => 'pending',
            'student_attendance_reason' => null,
            'student_confirmed_at' => null,
        ]);
    }

    /**
     * Confirm attendance by student.
     */
    public function confirmAttendance(MentoringSession $session, array $data)
    {
        $status = $data['status']; // 'attending' or 'permission'
        $reason = $status === 'permission' ? ($data['reason'] ?? null) : null;

        $session->update([
            'student_attendance_status' => $status,
            'student_attendance_reason' => $reason,
            'student_confirmed_at' => now(),
        ]);

        $statusText = $status === 'attending' ? 'Akan Hadir' : 'Izin (Berhalangan Hadir)';
        $studentName = $session->thesis->student->name ?? Auth::user()->name;

        // Log Activity
        ActivityLog::log(
            'Konfirmasi Kehadiran Bimbingan',
            "Mahasiswa {$studentName} mengonfirmasi: {$statusText} pada sesi bimbingan {$session->topic}" . ($reason ? " (Alasan: {$reason})" : ""),
            'Bimbingan',
            $session
        );

        // Notify Dosen
        if ($session->dosen) {
            $session->dosen->notify(new GeneralNotification(
                'Konfirmasi Kehadiran Mahasiswa',
                "Mahasiswa {$studentName} telah mengonfirmasi {$statusText} pada sesi bimbingan: {$session->topic}" . ($reason ? " (Alasan: {$reason})" : ""),
                route('mentoring-sessions.index'),
                $status === 'attending' ? 'success' : 'warning'
            ));
        }

        return $status === 'attending' 
            ? 'Terima kasih, konfirmasi kehadiran (Akan Hadir) berhasil disimpan.' 
            : 'Konfirmasi izin berhasil dikirimkan ke Dosen Pembimbing.';
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
            $wasAbsent = (bool) $session->is_absent;
            $session->update([
                'status' => $data['status'],
                'is_absent' => false,
                'feedback' => $data['feedback'] ?? $session->feedback,
            ]);

            $session->thesis?->student?->notify(new GeneralNotification(
                'Status Bimbingan Diperbarui',
                "Status bimbingan Anda ({$session->topic}) diperbarui menjadi: " . strtoupper($data['status']),
                route('mentoring-sessions.index'),
                $data['status'] === 'approved' ? 'success' : ($data['status'] === 'rejected' ? 'danger' : 'info')
            ));

            if ($session->thesis?->student) {
                $session->thesis->student->notify(new \App\Notifications\MentoringStatusUpdatedNotification(
                    $session,
                    $data['status'],
                    $data['feedback'] ?? null
                ));
            }

            if ($wasAbsent && $data['status'] === 'approved') {
                $message = 'Status tidak hadir berhasil dibatalkan dan jadwal bimbingan kembali aktif.';
            } else {
                $message = 'Status sesi bimbingan diperbarui menjadi: ' . ucfirst($data['status']);
            }
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

    /**
     * Cancel / Delete a mentoring session (Single or Group).
     */
    public function cancelSession(MentoringSession $session, array $data)
    {
        $user = Auth::user();
        $reason = !empty($data['reason']) ? trim($data['reason']) : 'Tidak ada alasan khusus yang dicantumkan.';
        $applyToGroup = !empty($data['apply_to_group']);

        if ($session->status === 'completed') {
            throw new \Exception('Sesi bimbingan yang sudah selesai tidak dapat dibatalkan.');
        }

        if ($applyToGroup && in_array($user->role, ['dosen', 'admin', 'kaprodi'])) {
            $groupSessions = MentoringSession::where('dosen_id', $session->dosen_id)
                ->where('scheduled_at', $session->scheduled_at)
                ->where('status', '!=', 'completed')
                ->with(['thesis.student', 'dosen'])
                ->get();

            if (!$groupSessions->contains('id', $session->id)) {
                $groupSessions->push($session);
            }

            $count = $groupSessions->count();
            $formattedDate = $session->scheduled_at->locale('id')->translatedFormat('l, d F Y H:i');

            foreach ($groupSessions as $gSession) {
                $student = $gSession->thesis?->student;
                if ($student) {
                    $student->notify(new MentoringCancelledNotification($gSession, $user, $reason));
                }

                ActivityLog::log(
                    'Pembatalan Bimbingan Bersama',
                    "{$user->name} membatalkan sesi bimbingan bersama ({$gSession->topic}) untuk {$student?->name} pada {$formattedDate} WIB. Alasan: {$reason}",
                    'Bimbingan',
                    $gSession
                );

                if ($gSession->document_path && !filter_var($gSession->document_path, FILTER_VALIDATE_URL) && Storage::disk('local')->exists($gSession->document_path)) {
                    Storage::disk('local')->delete($gSession->document_path);
                }

                $gSession->delete();
            }

            return ['type' => 'group', 'count' => $count];
        }

        // Single cancellation
        $formattedDate = $session->scheduled_at->locale('id')->translatedFormat('l, d F Y H:i');
        $student = $session->thesis?->student;
        $dosen = $session->dosen ?? $session->thesis?->pembimbing1;

        // If cancelled by Dosen/Admin/Kaprodi -> notify Student
        // If cancelled by Student -> notify Dosen
        if (in_array($user->role, ['dosen', 'admin', 'kaprodi'])) {
            if ($student) {
                $student->notify(new MentoringCancelledNotification($session, $user, $reason));
            }
        } else {
            if ($dosen) {
                $dosen->notify(new MentoringCancelledNotification($session, $user, $reason));
            }
        }

        ActivityLog::log(
            'Pembatalan Bimbingan',
            "{$user->name} membatalkan sesi bimbingan ({$session->topic}) pada {$formattedDate} WIB. Alasan: {$reason}",
            'Bimbingan',
            $session
        );

        if ($session->document_path && !filter_var($session->document_path, FILTER_VALIDATE_URL) && Storage::disk('local')->exists($session->document_path)) {
            Storage::disk('local')->delete($session->document_path);
        }

        $session->delete();

        return ['type' => 'single', 'count' => 1];
    }
}

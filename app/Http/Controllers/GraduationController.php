<?php

namespace App\Http\Controllers;

use App\Models\Graduation;
use App\Models\Thesis;
use App\Models\User;
use App\Models\LetterSetting;
use App\Models\ActivityLog;
use App\Services\WhatsAppService;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GraduationController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Generate sequential letter number for SKL.
     */
    private function getNextSklNumber()
    {
        return DB::transaction(function () {
            $setting = LetterSetting::where('type', 'surat_keterangan_lulus')->first();
            if (!$setting) {
                $setting = LetterSetting::create([
                    'type' => 'surat_keterangan_lulus',
                    'title' => 'Surat Keterangan Lulus (SKL)',
                    'format' => '[NUMBER]/SKL/UNSUB/FIK/[ROMAN_MONTH]/[YEAR]',
                    'last_number' => 0,
                ]);
            }

            $setting->increment('last_number');
            $number = str_pad($setting->last_number, 3, '0', STR_PAD_LEFT);
            $month = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
            $romans = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $romanMonth = $romans[(int)$month] ?? 'I';

            return str_replace(
                ['[NUMBER]', '[MONTH]', '[ROMAN_MONTH]', '[YEAR]'],
                [$number, $month, $romanMonth, $year],
                $setting->format
            );
        });
    }

    /**
     * Student Portal: Yudisium & Bebas Tanggungan.
     */
    public function studentIndex()
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            abort(403);
        }

        $thesis = Thesis::where('student_id', $user->id)
            ->with(['pembimbing1', 'pembimbing2', 'graduation.approver'])
            ->first();

        // Check if student is eligible:
        // Must have thesis, defense schedule detail exists, and all defense revisions approved, or thesis status is completed
        $hasDefense = false;
        $isDefensePassed = false;

        if ($thesis) {
            $defenseDetail = \App\Models\ThesisDefenseScheduleDetail::where('thesis_id', $thesis->id)->latest()->first();
            if ($defenseDetail) {
                $hasDefense = true;
                $isDefensePassed = $defenseDetail->isRevisionAllApproved() || $thesis->status === 'completed';
            } elseif ($thesis->status === 'completed') {
                $hasDefense = true;
                $isDefensePassed = true;
            }
        }

        $isEligible = $hasDefense && $isDefensePassed;

        $graduation = null;
        if ($thesis) {
            $graduation = Graduation::firstOrNew([
                'thesis_id' => $thesis->id,
                'student_id' => $user->id,
            ]);
        }

        return view('graduations.student_index', compact('thesis', 'graduation', 'isEligible', 'hasDefense', 'isDefensePassed'));
    }

    /**
     * Student submission of clearance files.
     */
    public function studentStore(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            abort(403);
        }

        $thesis = Thesis::where('student_id', $user->id)->firstOrFail();

        // Normalize URLs (prepend https:// if missing)
        $urlFields = ['final_thesis_file', 'journal_article_file', 'plagiarism_file', 'publication_link'];
        foreach ($urlFields as $field) {
            if ($request->filled($field)) {
                $val = trim($request->input($field));
                if (!str_starts_with($val, 'http://') && !str_starts_with($val, 'https://')) {
                    $request->merge([$field => 'https://' . $val]);
                }
            }
        }

        $request->validate([
            'final_thesis_file' => 'required|url|max:1000',
            'journal_article_file' => 'required|url|max:1000',
            'plagiarism_file' => 'nullable|url|max:1000',
            'publication_link' => 'nullable|url|max:1000',
            'student_notes' => 'nullable|string|max:1000',
        ], [
            'final_thesis_file.required' => 'Tautan Google Drive naskah skripsi lengkap wajib diisi.',
            'final_thesis_file.url' => 'Format tautan naskah skripsi tidak valid (contoh: https://drive.google.com/...).',
            'journal_article_file.required' => 'Tautan Google Drive artikel jurnal ilmiah wajib diisi.',
            'journal_article_file.url' => 'Format tautan artikel jurnal tidak valid (contoh: https://drive.google.com/...).',
            'plagiarism_file.url' => 'Format tautan bukti plagiasi tidak valid.',
            'publication_link.url' => 'Format tautan publikasi tidak valid.',
        ]);

        $graduation = Graduation::firstOrNew([
            'thesis_id' => $thesis->id,
            'student_id' => $user->id,
        ]);

        $graduation->final_thesis_file = $request->final_thesis_file;
        $graduation->journal_article_file = $request->journal_article_file;
        $graduation->plagiarism_file = $request->plagiarism_file;
        $graduation->publication_link = $request->publication_link;
        $graduation->student_notes = $request->student_notes;

        // Set status to pending if previously rejected or new
        if ($graduation->status !== 'approved') {
            $graduation->status = 'pending';
            $graduation->rejection_reason = null;
        }

        $graduation->save();

        ActivityLog::log(
            'Pemberkasan Yudisium',
            "Mahasiswa {$user->name} mengunggah/memperbarui berkas bebas tanggungan.",
            'Yudisium',
            $graduation
        );

        // Notify Admins & Kaprodi
        $recipients = User::whereIn('role', ['admin', 'kaprodi'])->get();
        foreach ($recipients as $recipient) {
            $recipient->notify(new GeneralNotification(
                'Pengajuan Bebas Tanggungan Baru',
                "Mahasiswa {$user->name} ({$user->identifier}) telah mengajukan berkas bebas tanggungan pra-yudisium.",
                route('graduations.index'),
                'info'
            ));
        }

        return redirect()->route('student.graduation')->with('success', 'Berkas bebas tanggungan berhasil disimpan dan dikirim untuk verifikasi Program Studi.');
    }

    /**
     * Admin & Kaprodi Index: Manage Yudisium & Clearance.
     */
    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $status = $request->query('status', 'all');
        $search = $request->query('search', '');

        $baseQuery = Graduation::query();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        $graduationsQuery = Graduation::with(['student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'approver'])
            ->latest();

        if ($status !== 'all') {
            $graduationsQuery->where('status', $status);
        }

        if (!empty($search)) {
            $graduationsQuery->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('identifier', 'like', "%{$search}%");
                })->orWhereHas('thesis', function ($tq) use ($search) {
                    $tq->where('title', 'like', "%{$search}%")
                       ->orWhere('final_title', 'like', "%{$search}%");
                })->orWhere('skl_number', 'like', "%{$search}%");
            });
        }

        $graduations = $graduationsQuery->paginate(15)->withQueryString();

        return view('graduations.admin_index', compact('graduations', 'stats', 'status', 'search'));
    }

    /**
     * Admin/Kaprodi verification & approval action.
     */
    public function adminVerify(Request $request, Graduation $graduation)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $action = $request->input('action');

        if ($action === 'update_clearance') {
            // Update individual clearance checkboxes
            $hardcover = $request->boolean('hardcover_collected');
            $library = $request->boolean('library_clearance');
            $lab = $request->boolean('lab_clearance');
            $cdRepo = $request->boolean('cd_or_repository_collected');

            $graduation->hardcover_collected = $hardcover;
            $graduation->hardcover_collected_at = $hardcover ? ($graduation->hardcover_collected_at ?: now()) : null;

            $graduation->library_clearance = $library;
            $graduation->library_clearance_at = $library ? ($graduation->library_clearance_at ?: now()) : null;

            $graduation->lab_clearance = $lab;
            $graduation->lab_clearance_at = $lab ? ($graduation->lab_clearance_at ?: now()) : null;

            $graduation->cd_or_repository_collected = $cdRepo;
            $graduation->cd_or_repository_collected_at = $cdRepo ? ($graduation->cd_or_repository_collected_at ?: now()) : null;

            $graduation->save();

            return redirect()->back()->with('success', 'Status checklist bebas tanggungan berhasil diperbarui.');
        }

        if ($action === 'approve') {
            $request->validate([
                'graduation_date' => 'required|date',
                'gpa' => 'required|numeric|min:2.00|max:4.00',
                'predicate' => 'required|string|max:100',
                'skl_number' => 'nullable|string|max:100',
            ]);

            $sklNumber = $request->filled('skl_number') 
                ? trim($request->skl_number) 
                : ($graduation->skl_number ?: $this->getNextSklNumber());

            if (!$graduation->verification_token) {
                $graduation->verification_token = Str::random(64);
            }

            $graduation->hardcover_collected = true;
            $graduation->hardcover_collected_at = $graduation->hardcover_collected_at ?: now();
            $graduation->library_clearance = true;
            $graduation->library_clearance_at = $graduation->library_clearance_at ?: now();
            $graduation->lab_clearance = true;
            $graduation->lab_clearance_at = $graduation->lab_clearance_at ?: now();
            $graduation->cd_or_repository_collected = true;
            $graduation->cd_or_repository_collected_at = $graduation->cd_or_repository_collected_at ?: now();

            $graduation->status = 'approved';
            $graduation->rejection_reason = null;
            $graduation->skl_number = $sklNumber;
            $graduation->graduation_date = $request->graduation_date;
            $graduation->gpa = $request->gpa;
            $graduation->predicate = $request->predicate;
            $graduation->approved_by = $user->id;
            $graduation->approved_at = now();
            $graduation->save();

            // Ensure thesis status is completed
            if ($graduation->thesis && $graduation->thesis->status !== 'completed') {
                $graduation->thesis->update(['status' => 'completed']);
            }

            // Send in-app notification to student
            $graduation->student->notify(new GeneralNotification(
                'Selamat! SKL Digital Diterbitkan',
                "Pengajuan bebas tanggungan Anda telah disetujui. Surat Keterangan Lulus (SKL) No. {$sklNumber} telah diterbitkan dan dapat diunduh.",
                route('student.graduation'),
                'success'
            ));

            // Activity Log
            ActivityLog::log(
                'Penerbitan SKL',
                "Petugas {$user->name} menyetujui bebas tanggungan dan menerbitkan SKL No. {$sklNumber} untuk {$graduation->student->name}.",
                'Yudisium',
                $graduation
            );

            // WhatsApp Notification if available
            if ($graduation->student->phone_number) {
                $waMsg = "🎓 *SURAT KETERANGAN LULUS (SKL) DITERBITKAN*\n\n"
                    . "Halo *{$graduation->student->name}*,\n"
                    . "Selamat! Seluruh proses bebas tanggungan pra-yudisium Anda telah diverifikasi dan disetujui oleh Program Studi.\n\n"
                    . "📄 *Nomor SKL:* {$sklNumber}\n"
                    . "📅 *Tanggal Kelulusan:* " . Carbon::parse($request->graduation_date)->locale('id')->translatedFormat('d F Y') . "\n"
                    . "⭐ *Predikat:* {$request->predicate}\n\n"
                    . "Silakan login ke SIBIMA untuk mengunduh dokumen SKL resmi Anda:\n"
                    . url('/student/graduation') . "\n\n"
                    . "_Sistem Informasi Bimbingan Mahasiswa (SIBIMA) - FASILKOM UNSUB_";

                $this->whatsAppService->sendMessage($graduation->student->phone_number, $waMsg);
            }

            return redirect()->back()->with('success', "SKL No. {$sklNumber} berhasil diterbitkan untuk mahasiswa {$graduation->student->name}.");
        }

        if ($action === 'reject') {
            $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ], [
                'rejection_reason.required' => 'Wajib mencantumkan alasan penolakan/revisi berkas.',
            ]);

            $graduation->status = 'rejected';
            $graduation->rejection_reason = $request->rejection_reason;
            $graduation->save();

            // Notify student
            $graduation->student->notify(new GeneralNotification(
                'Perbaikan Berkas Bebas Tanggungan',
                "Pengajuan bebas tanggungan Anda memerlukan perbaikan: {$request->rejection_reason}",
                route('student.graduation'),
                'warning'
            ));

            ActivityLog::log(
                'Penolakan Berkas Yudisium',
                "Petugas {$user->name} menolak berkas bebas tanggungan {$graduation->student->name}. Alasan: {$request->rejection_reason}",
                'Yudisium',
                $graduation
            );

            return redirect()->back()->with('warning', 'Pengajuan berkas ditolak dan notifikasi perbaikan telah dikirim ke mahasiswa.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }

    /**
     * Download Surat Keterangan Lulus (SKL) PDF.
     */
    public function downloadSkl(Graduation $graduation)
    {
        $user = Auth::user();
        $isAuthorized = in_array($user->role, ['admin', 'kaprodi', 'dosen']) || 
                        ($user->role === 'mahasiswa' && $graduation->student_id === $user->id);

        if (!$isAuthorized) {
            abort(403);
        }

        if ($graduation->status !== 'approved') {
            abort(400, 'Surat Keterangan Lulus belum disetujui / diterbitkan.');
        }

        $graduation->load(['student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'approver']);

        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        $pdf = Pdf::loadView('graduations.skl_pdf', compact('graduation', 'kaprodi'))
            ->setPaper('a4', 'portrait');

        $fileName = 'SKL_' . str_replace(' ', '_', $graduation->student->name) . '_' . ($graduation->student->identifier ?? 'UNSUB') . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Public Verification of SKL via QR Code.
     */
    public function verifySkl($token)
    {
        $graduation = Graduation::with(['student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'approver'])
            ->where('verification_token', $token)
            ->first();

        if (!$graduation || $graduation->status !== 'approved') {
            return view('verification.invalid');
        }

        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        return view('verification.skl', compact('graduation', 'kaprodi'));
    }
}

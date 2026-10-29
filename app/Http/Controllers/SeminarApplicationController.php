<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSeminarApplicationRequest;
use App\Http\Requests\ValidateApplicationRequest;
use App\Models\SeminarApplication;
use App\Models\SeminarTemplate;
use App\Models\Thesis;
use App\Models\Wave;
use App\Models\User;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeminarApplicationsExport;
use App\Models\ActivityLog;
use App\Notifications\GeneralNotification;

class SeminarApplicationController extends Controller
{
    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'mahasiswa') {
            $thesis = Thesis::where('student_id', $user->id)->with(['pembimbing1', 'pembimbing2'])->first();
            
            $hasSeminar = $thesis ? SeminarApplication::where('thesis_id', $thesis->id)
                ->whereIn('status', ['approved', 'completed', 'finished'])
                ->exists() || \App\Models\SeminarScheduleDetail::where('thesis_id', $thesis->id)->exists() : false;

            if ($hasSeminar) {
                return redirect()->route('dashboard')->with('warning', 'Pendaftaran seminar sudah ditutup karena Anda sudah mendaftar atau melaksanakan seminar.');
            }

            $activeWave = Wave::getCurrentActive();
            $application = null;
            if ($thesis) {
                $application = SeminarApplication::where('thesis_id', $thesis->id)
                    ->where('wave_id', $activeWave?->id)
                    ->first();
                
                if (!$application) {
                    $application = SeminarApplication::where('thesis_id', $thesis->id)
                        ->latest()
                        ->first();
                }
            }
            
            $isEligible = $thesis && $thesis->isAccUpFinal();
            $template = SeminarTemplate::where('is_active', true)->latest()->first();
            
            return view('seminars.student_index', compact('thesis', 'application', 'isEligible', 'template', 'activeWave'));
        }

        if ($user->role === 'admin' || $user->role === 'kaprodi') {
            $activeWave = Wave::getCurrentActive();
            $selectedWaveId = $request->input('wave_id', $activeWave?->id);
            $search = $request->input('search');
            $status = $request->input('status', 'all');

            // Base query for the selected wave (or all waves)
            $baseWaveQuery = SeminarApplication::query();
            if (!empty($selectedWaveId) && $selectedWaveId !== 'all') {
                $baseWaveQuery->where('wave_id', $selectedWaveId);
            }

            // Statistics for the selected wave scope
            $rawStats = (clone $baseWaveQuery)->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            ")->first();

            $stats = [
                'total' => (int) ($rawStats->total ?? 0),
                'pending' => (int) ($rawStats->pending ?? 0),
                'approved' => (int) ($rawStats->approved ?? 0),
                'rejected' => (int) ($rawStats->rejected ?? 0),
            ];

            // Filtered applications query
            $applicationsQuery = SeminarApplication::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'wave']);
            
            if (!empty($selectedWaveId) && $selectedWaveId !== 'all') {
                $applicationsQuery->where('wave_id', $selectedWaveId);
            }

            if (!empty($status) && $status !== 'all') {
                $applicationsQuery->where('status', $status);
            }

            if (!empty($search)) {
                $applicationsQuery->where(function($q) use ($search) {
                    $q->whereHas('thesis.student', function($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                           ->orWhere('identifier', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('thesis', function($tq) use ($search) {
                        $tq->where('title', 'like', "%{$search}%");
                    });
                });
            }

            $applications = $applicationsQuery->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends([
                    'wave_id' => $selectedWaveId,
                    'status' => $status,
                    'search' => $search,
                ]);
            
            $waves = Wave::withCount('seminarApplications as app_count')->orderBy('created_at', 'desc')->get();
            $template = SeminarTemplate::where('is_active', true)->latest()->first();

            // Candidate theses for manual registration by Admin/Kaprodi
            $candidateTheses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
                ->whereDoesntHave('seminarApplications', function($q) {
                    $q->whereIn('status', ['approved', 'completed', 'finished']);
                })
                ->whereDoesntHave('seminarScheduleDetails')
                ->get()
                ->filter(fn($t) => $t->student !== null)
                ->sortBy(fn($t) => $t->student->name)
                ->values();
            
            return view('seminars.admin_index', compact('applications', 'template', 'waves', 'selectedWaveId', 'activeWave', 'search', 'status', 'stats', 'candidateTheses'));
        }

        if ($user->role === 'dosen') {
            return redirect()->route('theses.index')->with('info', 'Halaman pendaftaran seminar khusus mahasiswa dan admin. Anda dialihkan ke data bimbingan skripsi.');
        }

        abort(403);
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'kaprodi') {
            abort(403);
        }

        $activeWave = Wave::getCurrentActive();
        $selectedWaveId = $request->input('wave_id', $activeWave?->id);
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $filename = 'data-validasi-seminar-' . now()->format('Y-m-d') . '.xlsx';
        if (!empty($selectedWaveId) && $selectedWaveId !== 'all') {
            $wave = Wave::find($selectedWaveId);
            if ($wave) {
                $filename = 'data-validasi-seminar-' . \Illuminate\Support\Str::slug($wave->name) . '-' . now()->format('Y-m-d') . '.xlsx';
            }
        }

        return Excel::download(new SeminarApplicationsExport($selectedWaveId, $status, $search), $filename);
    }

    public function exportPaymentPdf(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'kaprodi') {
            abort(403);
        }

        $activeWave = Wave::getCurrentActive();
        $selectedWaveId = $request->input('wave_id', $activeWave?->id);
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $applicationsQuery = SeminarApplication::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'wave']);

        if (!empty($selectedWaveId) && $selectedWaveId !== 'all') {
            $applicationsQuery->where('wave_id', $selectedWaveId);
        }

        if (!empty($status) && $status !== 'all') {
            $applicationsQuery->where('status', $status);
        }

        if (!empty($search)) {
            $applicationsQuery->where(function($q) use ($search) {
                $q->whereHas('thesis.student', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('identifier', 'like', "%{$search}%");
                })->orWhereHas('thesis', function($tq) use ($search) {
                    $tq->where('title', 'like', "%{$search}%");
                });
            });
        }

        $applications = $applicationsQuery->orderBy('created_at', 'desc')->get();

        foreach ($applications as $app) {
            $app->payment_proof_data = $this->preparePaymentProofData($app);
        }

        $wave = (!empty($selectedWaveId) && $selectedWaveId !== 'all') ? Wave::find($selectedWaveId) : null;
        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        $filename = 'rekap-pembayaran-seminar-' . now()->format('Y-m-d') . '.pdf';
        if ($wave) {
            $filename = 'rekap-pembayaran-seminar-' . Str::slug($wave->name) . '-' . now()->format('Y-m-d') . '.pdf';
        }

        $pdf = Pdf::loadView('seminars.payment_recap_pdf', compact('applications', 'wave', 'status', 'search', 'kaprodi'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    public function exportPaymentWord(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'kaprodi') {
            abort(403);
        }

        $activeWave = Wave::getCurrentActive();
        $selectedWaveId = $request->input('wave_id', $activeWave?->id);
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $applicationsQuery = SeminarApplication::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'wave']);

        if (!empty($selectedWaveId) && $selectedWaveId !== 'all') {
            $applicationsQuery->where('wave_id', $selectedWaveId);
        }

        if (!empty($status) && $status !== 'all') {
            $applicationsQuery->where('status', $status);
        }

        if (!empty($search)) {
            $applicationsQuery->where(function($q) use ($search) {
                $q->whereHas('thesis.student', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('identifier', 'like', "%{$search}%");
                })->orWhereHas('thesis', function($tq) use ($search) {
                    $tq->where('title', 'like', "%{$search}%");
                });
            });
        }

        $applications = $applicationsQuery->orderBy('created_at', 'desc')->get();

        foreach ($applications as $app) {
            $app->payment_proof_data = $this->preparePaymentProofData($app);
        }

        $wave = (!empty($selectedWaveId) && $selectedWaveId !== 'all') ? Wave::find($selectedWaveId) : null;
        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        $filename = 'rekap-pembayaran-seminar-' . now()->format('Y-m-d') . '.doc';
        if ($wave) {
            $filename = 'rekap-pembayaran-seminar-' . Str::slug($wave->name) . '-' . now()->format('Y-m-d') . '.doc';
        }

        $content = view('seminars.payment_recap_word', compact('applications', 'wave', 'status', 'search', 'kaprodi'))->render();

        return response($content)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0, must-revalidate');
    }

    protected function preparePaymentProofData($application): array
    {
        $rawPath = $application->file_pembayaran;
        if (empty($rawPath)) {
            return [
                'type' => 'empty',
                'label' => 'Belum Ada Bukti Pembayaran',
                'url' => null,
                'base64' => null,
                'filename' => null,
            ];
        }

        if (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
            $isGdrive = str_contains($rawPath, 'drive.google.com') || str_contains($rawPath, 'docs.google.com');

            if ($isGdrive) {
                $fileId = null;
                if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $rawPath, $m)) {
                    $fileId = $m[1];
                } elseif (preg_match('/drive\.google\.com\/(?:open|uc)\?[^#]*\bid=([a-zA-Z0-9_-]+)/', $rawPath, $m)) {
                    $fileId = $m[1];
                } elseif (preg_match('/docs\.google\.com\/[^\/]+\/d\/([a-zA-Z0-9_-]+)/', $rawPath, $m)) {
                    $fileId = $m[1];
                }

                if ($fileId) {
                    $gdriveCandidates = [
                        "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000",
                        "https://lh3.googleusercontent.com/d/{$fileId}",
                        "https://drive.google.com/uc?export=view&id={$fileId}",
                    ];

                    foreach ($gdriveCandidates as $candidateUrl) {
                        try {
                            $resp = Http::withoutVerifying()
                                ->timeout(4)
                                ->get($candidateUrl);

                            if ($resp->successful() && strlen($resp->body()) > 200) {
                                $cType = $resp->header('Content-Type');
                                if ($cType && str_starts_with($cType, 'image/')) {
                                    return [
                                        'type' => 'image',
                                        'label' => 'Bukti Pembayaran (Google Drive)',
                                        'url' => $rawPath,
                                        'base64' => 'data:' . $cType . ';base64,' . base64_encode($resp->body()),
                                        'filename' => 'Google Drive File',
                                    ];
                                }
                            }
                        } catch (\Throwable $e) {
                            // Abaikan dan coba URL berikutnya
                        }
                    }
                }
            } elseif (\App\Support\UrlSecurity::isSafeExternalUrl($rawPath)) {
                // Direct image URL (validated against SSRF)
                try {
                    $resp = Http::withoutVerifying()
                        ->withOptions(['allow_redirects' => false])
                        ->timeout(4)
                        ->get($rawPath);

                    if ($resp->successful() && strlen($resp->body()) > 200) {
                        $cType = $resp->header('Content-Type');
                        if ($cType && str_starts_with($cType, 'image/')) {
                            return [
                                'type' => 'image',
                                'label' => basename(parse_url($rawPath, PHP_URL_PATH) ?? 'bukti_bayar'),
                                'url' => $rawPath,
                                'base64' => 'data:' . $cType . ';base64,' . base64_encode($resp->body()),
                                'filename' => basename(parse_url($rawPath, PHP_URL_PATH) ?? 'bukti_bayar'),
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    // Abaikan
                }
            }

            return [
                'type' => 'url',
                'is_gdrive' => $isGdrive,
                'label' => 'Tautan Bukti Pembayaran Cloud/Drive',
                'url' => $rawPath,
                'base64' => null,
                'filename' => basename(parse_url($rawPath, PHP_URL_PATH) ?? 'link'),
            ];
        }

        $defaultDisk = config('filesystems.default');
        $disksToCheck = array_unique([$defaultDisk, 'local', 'public']);
        $fullPath = null;

        foreach ($disksToCheck as $disk) {
            if (Storage::disk($disk)->exists($rawPath)) {
                $fullPath = Storage::disk($disk)->path($rawPath);
                break;
            }
        }

        if (!$fullPath) {
            $candidates = [
                storage_path('app/' . $rawPath),
                storage_path('app/private/' . $rawPath),
                storage_path('app/public/' . $rawPath),
                public_path($rawPath),
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && is_readable($cand)) {
                    $fullPath = $cand;
                    break;
                }
            }
        }

        $downloadUrl = route('download.private', ['path' => $rawPath, 'inline' => 1]);
        $filename = basename($rawPath);
        $extension = strtolower(pathinfo($rawPath, PATHINFO_EXTENSION));

        if ($fullPath && file_exists($fullPath) && is_readable($fullPath)) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
            if (in_array($extension, $imageExtensions)) {
                $mime = mime_content_type($fullPath) ?: ('image/' . $extension);
                $content = file_get_contents($fullPath);
                return [
                    'type' => 'image',
                    'label' => $filename,
                    'url' => $downloadUrl,
                    'base64' => 'data:' . $mime . ';base64,' . base64_encode($content),
                    'filename' => $filename,
                ];
            }

            if ($extension === 'pdf') {
                return [
                    'type' => 'pdf',
                    'label' => 'Dokumen PDF Terlampir',
                    'url' => $downloadUrl,
                    'base64' => null,
                    'filename' => $filename,
                ];
            }

            return [
                'type' => 'file',
                'label' => $filename,
                'url' => $downloadUrl,
                'base64' => null,
                'filename' => $filename,
            ];
        }

        return [
            'type' => 'file',
            'label' => $filename,
            'url' => $downloadUrl,
            'base64' => null,
            'filename' => $filename,
        ];
    }

    public function store(Request $request)
    {
        $activeWave = Wave::getCurrentActive();
        if (!$activeWave) {
            return redirect()->back()->with('error', 'Pendaftaran seminar belum dibuka (tidak ada gelombang aktif).');
        }

        $thesis = Thesis::where('student_id', Auth::id())->first();
        if (!$thesis || !$thesis->isAccUpFinal()) {
            return redirect()->back()->with('error', 'Anda belum memenuhi syarat untuk mengajukan seminar.');
        }

        $hasApprovedSeminar = SeminarApplication::where('thesis_id', $thesis->id)
            ->whereIn('status', ['approved', 'completed', 'finished'])
            ->exists() || \App\Models\SeminarScheduleDetail::where('thesis_id', $thesis->id)->exists();
        if ($hasApprovedSeminar) {
            return redirect()->back()->with('error', 'Anda sudah melakukan pendaftaran seminar dan telah disetujui atau dijadwalkan.');
        }

        $files = ['file_acc_pembimbing', 'file_pembayaran', 'file_kartu_bimbingan', 'file_skripsi', 'file_formulir'];

        $formRequest = new StoreSeminarApplicationRequest();
        $rules = $formRequest->rules();

        $validator = \Illuminate\Support\Facades\Validator::make(
            $request->all(), 
            $rules, 
            $formRequest->messages(), 
            $formRequest->attributes()
        );

        // Store any valid files in the session before returning validation errors
        foreach ($files as $file) {
            if ($request->hasFile($file) && !$validator->errors()->has($file)) {
                $path = $request->file($file)->store('seminar_temp_uploads', 'local');
                session()->put('seminar_uploads.path.' . $file, $path);
                session()->put('seminar_uploads.name.' . $file, $request->file($file)->getClientOriginalName());
            }
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $this->applicationService->submitApplication(
                SeminarApplication::class, $thesis, $activeWave, $request, $files,
                'Pengajuan Seminar', 'Pengajuan Seminar Baru', 'seminar-applications.index'
            );
            return redirect()->route('seminar-applications.index')->with('success', 'Pengajuan seminar berhasil dikirim. Menunggu validasi admin.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengajukan seminar: ' . $e->getMessage())->withInput();
        }
    }

    public function uploadTemplate(Request $request)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'kaprodi') abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'template_file' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        try {
            $this->applicationService->uploadTemplate(
                SeminarTemplate::class, $request->only('title'), $request->file('template_file'), 'seminar_templates'
            );
            return redirect()->back()->with('success', 'Templat formulir seminar berhasil diunggah.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunggah templat: ' . $e->getMessage());
        }
    }

    public function validateApplication(ValidateApplicationRequest $request, SeminarApplication $application)
    {
        $this->applicationService->validateApplication(
            $application, $request->validated(),
            'Validasi Seminar', 'Status Seminar Diperbarui', 'seminar-applications.index'
        );

        return redirect()->back()->with('success', 'Status pengajuan seminar berhasil diperbarui.');
    }

    public function destroy(SeminarApplication $application)
    {
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['admin', 'kaprodi']);
        $isStudentOwner = $user->role === 'mahasiswa' && $application->thesis && $application->thesis->student_id === $user->id;

        if (!$isAdmin && !$isStudentOwner) {
            abort(403);
        }

        try {
            $files = ['file_acc_pembimbing', 'file_pembayaran', 'file_kartu_bimbingan', 'file_skripsi', 'file_formulir'];
            $this->applicationService->deleteApplication($application, $files, $isAdmin, 'Seminar Proposal');
            
            $msg = $isAdmin 
                ? 'Pengajuan seminar proposal mahasiswa berhasil dibatalkan dan dihapus.' 
                : 'Pengajuan seminar proposal Anda telah dibatalkan. Silakan ajukan ulang dengan berkas yang benar.';
                
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function downloadZip(SeminarApplication $application)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'kaprodi') abort(403);

        $fileMap = [
            'file_acc_pembimbing' => '1_ACC_Pembimbing',
            'file_pembayaran' => '2_Bukti_Pembayaran',
            'file_kartu_bimbingan' => '3_Kartu_Bimbingan',
            'file_skripsi' => '4_Naskah_Skripsi',
            'file_formulir' => '5_Formulir_Seminar',
        ];

        $tempFile = $this->applicationService->downloadZip($application, 'Seminar', $fileMap);

        if ($tempFile) {
            $fileName = "Seminar_{$application->thesis->student->identifier}_" . str_replace(' ', '_', $application->thesis->student->name) . ".zip";
            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
    }

    /**
     * Daftarkan mahasiswa ke seminar proposal secara manual oleh Admin/Kaprodi
     */
    public function adminRegister(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'kaprodi') {
            abort(403);
        }

        $validated = $request->validate([
            'thesis_id' => 'required|exists:theses,id',
            'wave_id' => 'required|exists:waves,id',
            'status' => 'required|in:pending,approved',
            'bypass_files' => 'nullable|boolean',
            'admin_notes' => 'nullable|string|max:1000',
            'batch_folder_url' => 'nullable|string|max:1000',
            'file_formulir' => 'nullable|string|max:1000',
            'file_pembayaran' => 'nullable|string|max:1000',
            'file_acc_pembimbing' => 'nullable|string|max:1000',
            'file_kartu_bimbingan' => 'nullable|string|max:1000',
            'file_skripsi' => 'nullable|string|max:1000',
        ], [
            'thesis_id.required' => 'Silakan pilih mahasiswa yang akan didaftarkan.',
            'wave_id.required' => 'Silakan pilih gelombang pelaksanaan.',
            'status.required' => 'Pilih status pendaftaran awal.',
        ]);

        $thesis = Thesis::with('student')->findOrFail($validated['thesis_id']);

        // Check if student already has an approved or scheduled seminar
        $hasApprovedSeminar = SeminarApplication::where('thesis_id', $thesis->id)
            ->whereIn('status', ['approved', 'completed', 'finished'])
            ->exists() || \App\Models\SeminarScheduleDetail::where('thesis_id', $thesis->id)->exists();

        if ($hasApprovedSeminar) {
            return redirect()->back()->with('error', "Mahasiswa {$thesis->student->name} sudah terdaftar dan disetujui / dijadwalkan pada seminar proposal.");
        }

        $fileFields = ['file_formulir', 'file_pembayaran', 'file_acc_pembimbing', 'file_kartu_bimbingan', 'file_skripsi'];
        $uploadedFiles = [];
        $bypassFiles = $request->boolean('bypass_files');
        $batchFolder = $request->input('batch_folder_url');

        foreach ($fileFields as $field) {
            if ($request->filled($field)) {
                $uploadedFiles[$field] = $request->input($field);
            } elseif (!empty($batchFolder)) {
                $uploadedFiles[$field] = $batchFolder;
            } elseif ($request->hasFile($field)) {
                $uploadedFiles[$field] = $request->file($field)->store('SeminarApplication_files', config('filesystems.default', 'local'));
            } elseif ($bypassFiles) {
                $uploadedFiles[$field] = 'DISPENSASI_ADMIN: ' . ($validated['admin_notes'] ?: 'Didaftarkan langsung oleh ' . ucfirst($user->role));
            } else {
                $uploadedFiles[$field] = null;
            }
        }

        $adminNote = $validated['admin_notes'] ?? null;
        $feedbackText = "Didaftarkan langsung oleh " . ucfirst($user->role) . " ({$user->name})" . ($adminNote ? " - Catatan: {$adminNote}" : "");

        $application = SeminarApplication::where('thesis_id', $thesis->id)
            ->where('wave_id', $validated['wave_id'])
            ->first();

        if ($application) {
            $dataToUpdate = array_filter($uploadedFiles, fn($val) => !is_null($val));
            $dataToUpdate['status'] = $validated['status'];
            $dataToUpdate['admin_feedback'] = $feedbackText;
            $application->update($dataToUpdate);
        } else {
            $application = SeminarApplication::create(array_merge([
                'thesis_id' => $thesis->id,
                'wave_id' => $validated['wave_id'],
                'status' => $validated['status'],
                'admin_feedback' => $feedbackText,
            ], $uploadedFiles));
        }

        // Activity log
        try {
            ActivityLog::log(
                activity: 'Pendaftaran Seminar oleh Admin',
                description: "Mendaftarkan mahasiswa {$thesis->student->name} ({$thesis->student->identifier}) ke Seminar Proposal dengan status: " . strtoupper($validated['status']),
                module: 'Seminar',
                subject: $application
            );
        } catch (\Throwable $e) {}

        // Notification to student
        try {
            $thesis->student->notify(new GeneralNotification(
                title: 'Pendaftaran Seminar Berhasil',
                message: "Anda telah didaftarkan ke Seminar Proposal oleh Administrator/Kaprodi (" . ($validated['status'] === 'approved' ? 'Siap Dijadwalkan' : 'Menunggu Validasi') . ").",
                url: route('seminar-applications.index'),
                type: 'success'
            ));
        } catch (\Throwable $e) {}

        $wave = Wave::find($validated['wave_id']);
        $waveName = $wave ? $wave->name : '';

        return redirect()->route('seminar-applications.index', ['wave_id' => $validated['wave_id']])->with(
            'success',
            "Mahasiswa {$thesis->student->name} ({$thesis->student->identifier}) berhasil didaftarkan ke Seminar Proposal pada {$waveName} dengan status " . strtoupper($validated['status']) . "!"
        );
    }
}

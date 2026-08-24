<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThesisDefenseApplicationRequest;
use App\Http\Requests\ValidateApplicationRequest;
use App\Models\ThesisDefenseApplication;
use App\Models\ThesisDefenseTemplate;
use App\Models\Thesis;
use App\Models\Wave;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThesisDefenseApplicationController extends Controller
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
            $thesis = Thesis::where('student_id', $user->id)->first();
            
            $hasDefense = $thesis ? ThesisDefenseApplication::where('thesis_id', $thesis->id)
                ->whereIn('status', ['approved', 'completed', 'finished'])
                ->exists() || \App\Models\ThesisDefenseScheduleDetail::where('thesis_id', $thesis->id)->exists() : false;

            if ($hasDefense) {
                return redirect()->route('dashboard')->with('warning', 'Pendaftaran sidang skripsi sudah ditutup karena Anda sudah mendaftar atau melaksanakan sidang.');
            }

            $activeWave = Wave::getCurrentActive();
            $application = null;
            if ($thesis) {
                $application = ThesisDefenseApplication::where('thesis_id', $thesis->id)
                    ->where('wave_id', $activeWave?->id)
                    ->first();
                
                if (!$application) {
                    $application = ThesisDefenseApplication::where('thesis_id', $thesis->id)
                        ->latest()
                        ->first();
                }
            }
            
            $isEligible = $thesis && $thesis->isAccSidangFinal();
            $template = ThesisDefenseTemplate::where('is_active', true)->latest()->first();
            
            return view('thesis_defenses.student_index', compact('thesis', 'application', 'isEligible', 'template', 'activeWave'));
        }

        if ($user->role === 'admin' || $user->role === 'kaprodi') {
            $activeWave = Wave::getCurrentActive();
            $selectedWaveId = $request->input('wave_id', $activeWave?->id);
            $search = $request->input('search');
            $status = $request->input('status', 'all');

            // Base query for the selected wave (or all waves)
            $baseWaveQuery = ThesisDefenseApplication::query();
            if (!empty($selectedWaveId) && $selectedWaveId !== 'all') {
                $baseWaveQuery->where('wave_id', $selectedWaveId);
            }

            // Statistics for the selected wave scope
            $stats = [
                'total' => (clone $baseWaveQuery)->count(),
                'pending' => (clone $baseWaveQuery)->where('status', 'pending')->count(),
                'approved' => (clone $baseWaveQuery)->where('status', 'approved')->count(),
                'rejected' => (clone $baseWaveQuery)->where('status', 'rejected')->count(),
            ];

            // Filtered applications query
            $applicationsQuery = ThesisDefenseApplication::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'wave']);
            
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
            
            $waves = Wave::orderBy('created_at', 'desc')->get()->map(function($w) {
                $w->app_count = ThesisDefenseApplication::where('wave_id', $w->id)->count();
                return $w;
            });
            $template = ThesisDefenseTemplate::where('is_active', true)->latest()->first();
            
            return view('thesis_defenses.admin_index', compact('applications', 'template', 'waves', 'selectedWaveId', 'activeWave', 'search', 'status', 'stats'));
        }

        abort(403);
    }

    public function store(Request $request)
    {
        $activeWave = Wave::getCurrentActive();
        if (!$activeWave) {
            return redirect()->back()->with('error', 'Pendaftaran sidang belum dibuka (tidak ada gelombang aktif).');
        }

        $thesis = Thesis::where('student_id', Auth::id())->first();
        if (!$thesis || !$thesis->isAccSidangFinal()) {
            return redirect()->back()->with('error', 'Anda belum memenuhi syarat untuk mengajukan sidang skripsi.');
        }

        $hasApprovedDefense = ThesisDefenseApplication::where('thesis_id', $thesis->id)
            ->whereIn('status', ['approved', 'completed', 'finished'])
            ->exists() || \App\Models\ThesisDefenseScheduleDetail::where('thesis_id', $thesis->id)->exists();
        if ($hasApprovedDefense) {
            return redirect()->back()->with('error', 'Anda sudah melakukan pendaftaran sidang skripsi dan telah disetujui atau dijadwalkan.');
        }

        $files = [
            'file_formulir', 'file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_pembayaran',
            'file_skripsi', 'file_ktm', 'file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab',
            'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan',
            'file_tahsin', 'file_komputer', 'file_perpus_pinjam', 'file_perpus_sumbang', 'file_ijazah'
        ];

        $formRequest = new StoreThesisDefenseApplicationRequest();
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
                $path = $request->file($file)->store('defense_temp_uploads', 'local');
                session()->put('defense_uploads.path.' . $file, $path);
                session()->put('defense_uploads.name.' . $file, $request->file($file)->getClientOriginalName());
            }
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $this->applicationService->submitApplication(
                ThesisDefenseApplication::class, $thesis, $activeWave, $request, $files,
                'Pengajuan Sidang', 'Pengajuan Sidang Baru', 'thesis-defense-applications.index'
            );
            return redirect()->route('thesis-defense-applications.index')->with('success', 'Pengajuan sidang skripsi berhasil dikirim. Menunggu validasi admin.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengajukan sidang skripsi: ' . $e->getMessage())->withInput();
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
                ThesisDefenseTemplate::class, $request->only('title'), $request->file('template_file'), 'thesis_defense_templates'
            );
            return redirect()->back()->with('success', 'Templat formulir sidang skripsi berhasil diunggah.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunggah templat: ' . $e->getMessage());
        }
    }

    public function validateApplication(ValidateApplicationRequest $request, ThesisDefenseApplication $application)
    {
        $this->applicationService->validateApplication(
            $application, $request->validated(),
            'Validasi Sidang', 'Status Sidang Diperbarui', 'thesis-defense-applications.index'
        );

        return redirect()->back()->with('success', 'Status pengajuan sidang skripsi berhasil diperbarui.');
    }

    public function destroy(ThesisDefenseApplication $application)
    {
        if (Auth::user()->role !== 'mahasiswa' || $application->thesis->student_id !== Auth::id()) {
            abort(403);
        }

        try {
            $files = [
                'file_formulir', 'file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_pembayaran',
                'file_skripsi', 'file_ktm', 'file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab',
                'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan',
                'file_tahsin', 'file_komputer', 'file_perpus_pinjam', 'file_perpus_sumbang', 'file_ijazah'
            ];
            $this->applicationService->deleteRejectedApplication($application, $files);
            return redirect()->back()->with('success', 'Pengajuan sebelumnya telah dihapus. Silakan ajukan ulang dengan berkas yang benar.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function downloadZip(ThesisDefenseApplication $application)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'kaprodi') abort(403);

        $fileMap = [
            'file_formulir' => '1_Formulir_Pendaftaran',
            'file_transkrip' => '2_Transkrip_Nilai',
            'file_acc_pembimbing' => '3_ACC_Pembimbing',
            'file_logbook' => '4_Logbook',
            'file_pembayaran' => '5_Bukti_Pembayaran',
            'file_skripsi' => '6_Soft_File_Skripsi',
            'file_ktm' => '7_KTM',
            'file_pkkmb_univ' => '8_PKKMB_Univ',
            'file_pkkmb_fak' => '9_PKKMB_Fakultas',
            'file_makrab' => '10_Makrab_Himpunan',
            'file_cisco' => '11_Cisco_IPv6',
            'file_workshop' => '12_Sertifikat_Workshop',
            'file_organisasi' => '13_Sertifikat_Organisasi',
            'file_toefl' => '14_TOEFL',
            'file_kewirausahaan' => '15_Kewirausahaan',
            'file_tahsin' => '16_Tahsin',
            'file_komputer' => '17_Komputer',
            'file_perpus_pinjam' => '18_Bebas_Pinjam_Perpus',
            'file_perpus_sumbang' => '19_Sumbang_Buku_Perpus',
            'file_ijazah' => '20_Ijazah_SMA',
        ];

        $tempFile = $this->applicationService->downloadZip($application, 'Sidang', $fileMap);

        if ($tempFile) {
            $fileName = "Sidang_{$application->thesis->student->identifier}_" . str_replace(' ', '_', $application->thesis->student->name) . ".zip";
            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
    }
}

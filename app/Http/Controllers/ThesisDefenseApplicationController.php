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
            $activeWave = Wave::getCurrentActive();
            $application = $thesis ? ThesisDefenseApplication::where('thesis_id', $thesis->id)
                ->where('wave_id', $activeWave?->id)
                ->first() : null;
            $isEligible = $thesis && $thesis->isAccSidangFinal();
            $template = ThesisDefenseTemplate::where('is_active', true)->latest()->first();
            
            return view('thesis_defenses.student_index', compact('thesis', 'application', 'isEligible', 'template', 'activeWave'));
        }

        if ($user->role === 'admin') {
            $activeWave = Wave::getCurrentActive();
            $selectedWaveId = $request->input('wave_id', $activeWave?->id);

            $applications = ThesisDefenseApplication::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'wave'])
                ->when($selectedWaveId, function($q) use ($selectedWaveId) {
                    $q->where('wave_id', $selectedWaveId);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends(['wave_id' => $selectedWaveId]);
            
            $waves = Wave::orderBy('created_at', 'desc')->get()->map(function($w) {
                $w->app_count = ThesisDefenseApplication::where('wave_id', $w->id)->count();
                return $w;
            });
            $template = ThesisDefenseTemplate::where('is_active', true)->latest()->first();
            
            return view('thesis_defenses.admin_index', compact('applications', 'template', 'waves', 'selectedWaveId', 'activeWave'));
        }

        abort(403);
    }

    public function store(StoreThesisDefenseApplicationRequest $request)
    {
        $activeWave = Wave::getCurrentActive();
        if (!$activeWave) {
            return redirect()->back()->with('error', 'Pendaftaran sidang belum dibuka (tidak ada gelombang aktif).');
        }

        $thesis = Thesis::where('student_id', Auth::id())->first();
        if (!$thesis || !$thesis->isAccSidangFinal()) {
            return redirect()->back()->with('error', 'Anda belum memenuhi syarat untuk mengajukan sidang skripsi.');
        }

        $files = [
            'file_formulir', 'file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_pembayaran',
            'file_skripsi', 'file_ktm', 'file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab',
            'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan',
            'file_tahsin', 'file_komputer', 'file_perpus_pinjam', 'file_perpus_sumbang', 'file_ijazah'
        ];

        $this->applicationService->submitApplication(
            ThesisDefenseApplication::class, $thesis, $activeWave, $request, $files,
            'Pengajuan Sidang', 'Pengajuan Sidang Baru', 'thesis-defense-applications.index'
        );

        return redirect()->route('thesis-defense-applications.index')->with('success', 'Pengajuan sidang skripsi berhasil dikirim. Menunggu validasi admin.');
    }

    public function uploadTemplate(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'template_file' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $this->applicationService->uploadTemplate(
            ThesisDefenseTemplate::class, $request->only('title'), $request->file('template_file'), 'thesis_defense_templates'
        );

        return redirect()->back()->with('success', 'Templat formulir sidang skripsi berhasil diunggah.');
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
        if (Auth::user()->role !== 'admin') abort(403);

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

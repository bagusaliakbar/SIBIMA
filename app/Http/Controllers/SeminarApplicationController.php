<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSeminarApplicationRequest;
use App\Http\Requests\ValidateApplicationRequest;
use App\Models\SeminarApplication;
use App\Models\SeminarTemplate;
use App\Models\Thesis;
use App\Models\Wave;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $thesis = Thesis::where('student_id', $user->id)->first();
            $activeWave = Wave::getCurrentActive();
            $application = $thesis ? SeminarApplication::where('thesis_id', $thesis->id)
                ->where('wave_id', $activeWave?->id)
                ->first() : null;
            $isEligible = $thesis && $thesis->isAccUpFinal();
            $template = SeminarTemplate::where('is_active', true)->latest()->first();
            
            return view('seminars.student_index', compact('thesis', 'application', 'isEligible', 'template', 'activeWave'));
        }

        if ($user->role === 'admin') {
            $activeWave = Wave::getCurrentActive();
            $selectedWaveId = $request->get('wave_id', $activeWave?->id);

            $applications = SeminarApplication::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'wave'])
                ->when($selectedWaveId, function($q) use ($selectedWaveId) {
                    $q->where('wave_id', $selectedWaveId);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends(['wave_id' => $selectedWaveId]);
            
            $waves = Wave::orderBy('created_at', 'desc')->get()->map(function($w) {
                $w->app_count = SeminarApplication::where('wave_id', $w->id)->count();
                return $w;
            });
            $template = SeminarTemplate::where('is_active', true)->latest()->first();
            
            return view('seminars.admin_index', compact('applications', 'template', 'waves', 'selectedWaveId', 'activeWave'));
        }

        abort(403);
    }

    public function store(StoreSeminarApplicationRequest $request)
    {
        $activeWave = Wave::getCurrentActive();
        if (!$activeWave) {
            return redirect()->back()->with('error', 'Pendaftaran seminar belum dibuka (tidak ada gelombang aktif).');
        }

        $thesis = Thesis::where('student_id', Auth::id())->first();
        if (!$thesis || !$thesis->isAccUpFinal()) {
            return redirect()->back()->with('error', 'Anda belum memenuhi syarat untuk mengajukan seminar.');
        }

        $files = ['file_acc_pembimbing', 'file_pembayaran', 'file_kartu_bimbingan', 'file_skripsi', 'file_formulir'];

        $this->applicationService->submitApplication(
            SeminarApplication::class, $thesis, $activeWave, $request, $files,
            'Pengajuan Seminar', 'Pengajuan Seminar Baru', 'seminar-applications.index'
        );

        return redirect()->route('seminar-applications.index')->with('success', 'Pengajuan seminar berhasil dikirim. Menunggu validasi admin.');
    }

    public function uploadTemplate(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'template_file' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $this->applicationService->uploadTemplate(
            SeminarTemplate::class, $request->only('title'), $request->file('template_file'), 'seminar_templates'
        );

        return redirect()->back()->with('success', 'Templat formulir seminar berhasil diunggah.');
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
        if (Auth::user()->role !== 'mahasiswa' || $application->thesis->student_id !== Auth::id()) {
            abort(403);
        }

        try {
            $files = ['file_acc_pembimbing', 'file_pembayaran', 'file_kartu_bimbingan', 'file_skripsi', 'file_formulir'];
            $this->applicationService->deleteRejectedApplication($application, $files);
            return redirect()->back()->with('success', 'Pengajuan sebelumnya telah dihapus. Silakan ajukan ulang dengan berkas yang benar.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function downloadZip(SeminarApplication $application)
    {
        if (Auth::user()->role !== 'admin') abort(403);

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
}

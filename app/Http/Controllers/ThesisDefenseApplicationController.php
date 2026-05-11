<?php

namespace App\Http\Controllers;

use App\Models\ThesisDefenseApplication;
use App\Models\ThesisDefenseTemplate;
use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

use App\Models\Wave;

class ThesisDefenseApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'mahasiswa') {
            $thesis = Thesis::where('student_id', $user->id)->first();
            $application = $thesis ? ThesisDefenseApplication::where('thesis_id', $thesis->id)->first() : null;
            
            // Check if both supervisors have ACC'd for Sidang
            $isEligible = $thesis && $thesis->isAccSidangFinal();
            
            // Get active template
            $template = ThesisDefenseTemplate::where('is_active', true)->latest()->first();
            
            return view('thesis_defenses.student_index', compact('thesis', 'application', 'isEligible', 'template'));
        }

        if ($user->role === 'admin') {
            $activeWave = Wave::active() ?: Wave::where('is_active', true)->latest()->first() ?: Wave::latest()->first();
            $selectedWaveId = $request->get('wave_id', $activeWave?->id);

            $applications = ThesisDefenseApplication::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'wave'])
                ->when($selectedWaveId, function($q) use ($selectedWaveId) {
                    $q->where('wave_id', $selectedWaveId);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends(['wave_id' => $selectedWaveId]);
            
            $waves = Wave::orderBy('created_at', 'desc')->get();
            $template = ThesisDefenseTemplate::where('is_active', true)->latest()->first();
            
            return view('thesis_defenses.admin_index', compact('applications', 'template', 'waves', 'selectedWaveId', 'activeWave'));
        }

        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'mahasiswa') {
            abort(403);
        }

        $activeWave = Wave::active();
        if (!$activeWave) {
            return redirect()->back()->with('error', 'Pendaftaran sidang skripsi belum dibuka (tidak ada gelombang aktif).');
        }

        $thesis = Thesis::where('student_id', Auth::id())->first();
        
        if (!$thesis || !$thesis->isAccSidangFinal()) {
            return redirect()->back()->with('error', 'Anda belum memenuhi syarat untuk mengajukan sidang skripsi.');
        }

        $existingApplication = ThesisDefenseApplication::where('thesis_id', $thesis->id)
            ->where('wave_id', $activeWave->id)
            ->first();

        $files = [
            'file_formulir', 'file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_pembayaran',
            'file_skripsi', 'file_ktm', 'file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab',
            'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan',
            'file_tahsin', 'file_komputer', 'file_perpus_pinjam', 'file_perpus_sumbang', 'file_ijazah'
        ];

        $rules = [];
        foreach ($files as $file) {
            $isRejected = isset($existingApplication->file_reviews[$file]['status']) && $existingApplication->file_reviews[$file]['status'] === 'rejected';
            $isMissing = !$existingApplication || !$existingApplication->$file;

            if ($isMissing || $isRejected) {
                if ($file === 'file_skripsi') {
                    $rules[$file] = 'required|file|mimes:pdf,doc,docx|max:10240';
                } elseif (in_array($file, ['file_formulir'])) {
                    $rules[$file] = 'required|file|mimes:pdf,doc,docx|max:2048';
                } else {
                    $rules[$file] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
                }
            } else {
                $rules[$file] = 'nullable|file';
            }
        }

        $request->validate($rules);

        $data = [
            'thesis_id' => $thesis->id,
            'wave_id' => $activeWave->id,
            'status' => 'pending',
        ];

        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $path = $request->file($file)->store('thesis_defense_applications', 'public');
                $data[$file] = $path;

                // Clear rejection status for this file
                if ($existingApplication) {
                    $reviews = $existingApplication->file_reviews;
                    unset($reviews[$file]);
                    $existingApplication->file_reviews = $reviews;
                    $existingApplication->save();
                }
            }
        }

        if ($existingApplication) {
            $existingApplication->update($data);
        } else {
            ThesisDefenseApplication::create($data);
        }

        \App\Models\ActivityLog::log('Pengajuan Sidang', "Mahasiswa mengajukan sidang skripsi.", 'Sidang');

        // Notify Admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralNotification(
            'Pengajuan Sidang Baru',
            "Mahasiswa " . Auth::user()->name . " mengajukan sidang skripsi.",
            route('thesis-defense-applications.index'),
            'info'
        ));

        return redirect()->route('thesis-defense-applications.index')->with('success', 'Pengajuan sidang skripsi berhasil dikirim. Menunggu validasi admin.');
    }

    public function uploadTemplate(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'template_file' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Deactivate old templates
        ThesisDefenseTemplate::query()->update(['is_active' => false]);

        $path = $request->file('template_file')->store('thesis_defense_templates', 'public');

        ThesisDefenseTemplate::create([
            'title' => $request->title,
            'file_path' => $path,
            'original_name' => $request->file('template_file')->getClientOriginalName(),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Templat formulir sidang skripsi berhasil diunggah.');
    }

    public function validateApplication(Request $request, ThesisDefenseApplication $application)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_feedback' => 'nullable|string',
            'file_reviews' => 'nullable|array',
        ]);

        $application->update([
            'status' => $request->status,
            'admin_feedback' => $request->admin_feedback,
            'file_reviews' => $request->file_reviews,
        ]);

        \App\Models\ActivityLog::log('Validasi Sidang', "Admin memperbarui status sidang mahasiswa " . $application->thesis->student->name . " menjadi: " . strtoupper($request->status), 'Sidang');

        // Notify Student
        $application->thesis->student->notify(new GeneralNotification(
            'Status Sidang Diperbarui',
            "Pengajuan sidang skripsi Anda telah " . strtoupper($request->status),
            route('thesis-defense-applications.index'),
            $request->status === 'approved' ? 'success' : 'danger'
        ));

        return redirect()->back()->with('success', 'Status pengajuan sidang skripsi berhasil diperbarui.');
    }

    public function destroy(ThesisDefenseApplication $application)
    {
        // Only student owner can delete their rejected application
        if (Auth::user()->role === 'mahasiswa' && $application->thesis->student_id === Auth::id()) {
            if ($application->status === 'rejected') {
                // Delete files from storage
                $files = [
                    $application->file_formulir, $application->file_transkrip, $application->file_acc_pembimbing, 
                    $application->file_logbook, $application->file_pembayaran, $application->file_skripsi,
                    $application->file_ktm, $application->file_pkkmb_univ, $application->file_pkkmb_fak,
                    $application->file_makrab, $application->file_cisco, $application->file_workshop,
                    $application->file_organisasi, $application->file_toefl, $application->file_kewirausahaan,
                    $application->file_tahsin, $application->file_komputer, $application->file_perpus_pinjam,
                    $application->file_perpus_sumbang, $application->file_ijazah
                ];
                foreach ($files as $file) {
                    if ($file) {
                        Storage::disk('public')->delete($file);
                    }
                }
                
                $application->delete();
                return redirect()->back()->with('success', 'Pengajuan sebelumnya telah dihapus. Silakan ajukan ulang dengan berkas yang benar.');
            }
        }

        abort(403);
    }

    public function downloadZip(ThesisDefenseApplication $application)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $studentName = str_replace(' ', '_', $application->thesis->student->name);
        $studentId = $application->thesis->student->identifier;
        $fileName = "Sidang_{$studentId}_{$studentName}.zip";

        $zip = new \ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        if ($zip->open($tempFile, \ZipArchive::CREATE) === TRUE) {
            $files = [
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

            foreach ($files as $field => $label) {
                if ($application->$field && Storage::disk('public')->exists($application->$field)) {
                    $extension = pathinfo($application->$field, PATHINFO_EXTENSION);
                    $zip->addFromString($label . '.' . $extension, Storage::disk('public')->get($application->$field));
                }
            }

            $zip->close();

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
    }
}

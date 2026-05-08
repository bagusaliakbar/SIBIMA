<?php

namespace App\Http\Controllers;

use App\Models\SeminarApplication;
use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class SeminarApplicationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'mahasiswa') {
            $thesis = Thesis::where('student_id', $user->id)->first();
            $application = $thesis ? SeminarApplication::where('thesis_id', $thesis->id)->first() : null;
            
            // Check if both supervisors have ACC'd
            $isEligible = $thesis && $thesis->isAccUpFinal();
            
            // Get active template
            $template = \App\Models\SeminarTemplate::where('is_active', true)->latest()->first();
            
            return view('seminars.student_index', compact('thesis', 'application', 'isEligible', 'template'));
        }

        if ($user->role === 'admin') {
            $applications = SeminarApplication::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            // Get active template for admin
            $template = \App\Models\SeminarTemplate::where('is_active', true)->latest()->first();
            
            return view('seminars.admin_index', compact('applications', 'template'));
        }

        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'mahasiswa') {
            abort(403);
        }

        $thesis = Thesis::where('student_id', Auth::id())->first();
        
        if (!$thesis || !$thesis->isAccUpFinal()) {
            return redirect()->back()->with('error', 'Anda belum memenuhi syarat untuk mengajukan seminar.');
        }

        $request->validate([
            'file_acc_pembimbing' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_pembayaran' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_kartu_bimbingan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_skripsi' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'file_formulir' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $data = [
            'thesis_id' => $thesis->id,
            'status' => 'pending',
        ];

        $files = ['file_acc_pembimbing', 'file_pembayaran', 'file_kartu_bimbingan', 'file_skripsi', 'file_formulir'];
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $path = $request->file($file)->store('seminar_applications', 'public');
                $data[$file] = $path;
            }
        }

        SeminarApplication::create($data);

        \App\Models\ActivityLog::log('Pengajuan Seminar', "Mahasiswa mengajukan seminar UP.", 'Seminar');

        // Notify Admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralNotification(
            'Pengajuan Seminar Baru',
            "Mahasiswa " . Auth::user()->name . " mengajukan seminar UP.",
            route('seminar-applications.index'),
            'info'
        ));

        return redirect()->route('seminar-applications.index')->with('success', 'Pengajuan seminar berhasil dikirim. Menunggu validasi admin.');
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
        \App\Models\SeminarTemplate::query()->update(['is_active' => false]);

        $path = $request->file('template_file')->store('seminar_templates', 'public');

        \App\Models\SeminarTemplate::create([
            'title' => $request->title,
            'file_path' => $path,
            'original_name' => $request->file('template_file')->getClientOriginalName(),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Templat formulir seminar berhasil diunggah.');
    }

    public function validateApplication(Request $request, SeminarApplication $application)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_feedback' => 'nullable|string',
        ]);

        $application->update([
            'status' => $request->status,
            'admin_feedback' => $request->admin_feedback,
        ]);

        \App\Models\ActivityLog::log('Validasi Seminar', "Admin memperbarui status seminar mahasiswa " . $application->thesis->student->name . " menjadi: " . strtoupper($request->status), 'Seminar');

        // Notify Student
        $application->thesis->student->notify(new GeneralNotification(
            'Status Seminar Diperbarui',
            "Pengajuan seminar Anda telah " . strtoupper($request->status),
            route('seminar-applications.index'),
            $request->status === 'approved' ? 'success' : 'danger'
        ));

        return redirect()->back()->with('success', 'Status pengajuan seminar berhasil diperbarui.');
    }

    public function destroy(SeminarApplication $application)
    {
        // Only student owner can delete their rejected application
        if (Auth::user()->role === 'mahasiswa' && $application->thesis->student_id === Auth::id()) {
            if ($application->status === 'rejected') {
                // Delete files from storage
                $files = [$application->file_acc_pembimbing, $application->file_pembayaran, $application->file_kartu_bimbingan, $application->file_skripsi];
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

    public function downloadZip(SeminarApplication $application)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $studentName = str_replace(' ', '_', $application->thesis->student->name);
        $studentId = $application->thesis->student->identifier;
        $fileName = "Seminar_{$studentId}_{$studentName}.zip";

        $zip = new \ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        if ($zip->open($tempFile, \ZipArchive::CREATE) === TRUE) {
            $files = [
                'file_acc_pembimbing' => '1_ACC_Pembimbing',
                'file_pembayaran' => '2_Bukti_Pembayaran',
                'file_kartu_bimbingan' => '3_Kartu_Bimbingan',
                'file_skripsi' => '4_Naskah_Skripsi',
                'file_formulir' => '5_Formulir_Seminar',
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

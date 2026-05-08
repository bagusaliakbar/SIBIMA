<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MentoringSession;
use App\Models\Thesis;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\ActivityLog;

class MentoringSessionController extends Controller
{
    public function create()
    {
        if (Auth::user()->role === 'mahasiswa') {
            $thesis = Thesis::where('student_id', Auth::id())->first();
            
            if (!$thesis || $thesis->status !== 'active') {
                return redirect()->route('dashboard')->with('error', 'Anda harus memiliki skripsi aktif untuk mengajukan bimbingan.');
            }

            return view('mentoring.create', compact('thesis'));
        } elseif (Auth::user()->role === 'dosen') {
            $theses = Thesis::with('student')
                ->where(function($q) {
                    $q->where('pembimbing1_id', Auth::id())
                      ->orWhere('pembimbing2_id', Auth::id());
                })
                ->where('status', 'active')
                ->get();
            return view('mentoring.create', compact('theses'));
        }

        abort(403);
    }

    public function store(Request $request)
    {
        // Combine date and time
        if ($request->has('scheduled_date') && $request->has('scheduled_hour') && $request->has('scheduled_minute')) {
            $request->merge([
                'scheduled_at' => $request->scheduled_date . ' ' . $request->scheduled_hour . ':' . $request->scheduled_minute
            ]);
        } elseif ($request->has('scheduled_date') && $request->has('scheduled_time')) {
            $request->merge([
                'scheduled_at' => $request->scheduled_date . ' ' . $request->scheduled_time
            ]);
        }

        $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'topic' => 'required|string|max:255',
            'type' => 'required|in:offline,online',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'thesis_id' => Auth::user()->role === 'dosen' ? 'required' : 'nullable',
        ], [
            'scheduled_at.after' => 'Waktu bimbingan harus di masa mendatang.',
        ]);

        if (Auth::user()->role === 'dosen') {
            if ($request->thesis_id === 'all') {
                $theses = Thesis::where(function($q) {
                        $q->where('pembimbing1_id', Auth::id())
                          ->orWhere('pembimbing2_id', Auth::id());
                    })
                    ->where('status', 'active')
                    ->get();
                
                if ($theses->isEmpty()) {
                    return back()->with('error', 'Anda tidak memiliki mahasiswa bimbingan yang aktif.');
                }
                
                foreach ($theses as $thesis) {
                    MentoringSession::create([
                        'thesis_id' => $thesis->id,
                        'dosen_id' => Auth::id(),
                        'scheduled_at' => $request->scheduled_at,
                        'topic' => $request->topic,
                        'type' => $request->type,
                        'location' => $request->location,
                        'notes' => $request->notes,
                        'status' => 'approved',
                    ]);

                    ActivityLog::log('Jadwal Bimbingan Massal', "Dosen menjadwalkan bimbingan untuk {$thesis->student->name}: {$request->topic}", 'Bimbingan');

                    // Notify student
                    $thesis->student->notify(new GeneralNotification(
                        'Jadwal Bimbingan Baru',
                        "Dosen " . Auth::user()->name . " menjadwalkan bimbingan baru: {$request->topic}",
                        route('mentoring-sessions.index'),
                        'info'
                    ));
                }
                
                return redirect()->route('mentoring-sessions.index')->with('success', 'Jadwal bimbingan massal berhasil dibuat untuk ' . $theses->count() . ' mahasiswa.');
            } else {
                $thesis = Thesis::findOrFail($request->thesis_id);
                // Verify ownership
                if ($thesis->pembimbing1_id !== Auth::id() && $thesis->pembimbing2_id !== Auth::id()) {
                    abort(403);
                }
                
                MentoringSession::create([
                    'thesis_id' => $thesis->id,
                    'dosen_id' => Auth::id(),
                    'scheduled_at' => $request->scheduled_at,
                    'topic' => $request->topic,
                    'type' => $request->type,
                    'location' => $request->location,
                    'notes' => $request->notes,
                    'status' => 'approved',
                ]);

                ActivityLog::log('Jadwal Bimbingan', "Dosen menjadwalkan bimbingan untuk {$thesis->student->name}: {$request->topic}", 'Bimbingan');

                // Notify student
                $thesis->student->notify(new GeneralNotification(
                    'Jadwal Bimbingan Baru',
                    "Dosen " . Auth::user()->name . " menjadwalkan bimbingan: {$request->topic}",
                    route('mentoring-sessions.index'),
                    'info'
                ));
                
                return redirect()->route('mentoring-sessions.index')->with('success', 'Jadwal bimbingan berhasil dibuat.');
            }
        } else {
            $thesis = Thesis::where('student_id', Auth::id())->firstOrFail();
            
            $request->validate([
                'dosen_id' => 'required|exists:users,id',
            ]);

            $session = MentoringSession::create([
                'thesis_id' => $thesis->id,
                'dosen_id' => $request->dosen_id,
                'scheduled_at' => $request->scheduled_at,
                'topic' => $request->topic,
                'type' => $request->type,
                'location' => $request->location,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            ActivityLog::log('Pengajuan Bimbingan', "Mahasiswa mengajukan bimbingan: {$request->topic}", 'Bimbingan');

            // Notify Dosen
            $dosen = \App\Models\User::find($request->dosen_id);
            $dosen->notify(new GeneralNotification(
                'Pengajuan Bimbingan',
                "Mahasiswa " . Auth::user()->name . " mengajukan bimbingan: {$request->topic}",
                route('mentoring-sessions.index'),
                'info'
            ));
            
            return redirect()->route('dashboard')->with('success', 'Jadwal bimbingan berhasil diajukan dan menunggu persetujuan dosen.');
        }
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        if (Auth::user()->role === 'dosen') {
            $sessions = MentoringSession::where('dosen_id', Auth::id())
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('thesis.student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('topic', 'like', "%{$search}%");
                });
            })
            ->with('thesis.student')->orderBy('scheduled_at', 'desc')->paginate(12)
            ->appends(['search' => $search]);
            
            return view('mentoring.index', compact('sessions', 'search'));
        } elseif (Auth::user()->role === 'mahasiswa') {
            $sessions = MentoringSession::whereHas('thesis', function($query) {
                $query->where('student_id', Auth::id());
            })
            ->when($search, function ($query, $search) {
                $query->where('topic', 'like', "%{$search}%");
            })
            ->with('thesis.pembimbing1', 'thesis.pembimbing2')->orderBy('scheduled_at', 'desc')->paginate(10)
            ->appends(['search' => $search]);
            
            return view('mentoring.student_index', compact('sessions', 'search'));
        } elseif (Auth::user()->role === 'admin') {
            $sessions = MentoringSession::when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('thesis.student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('dosen', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('topic', 'like', "%{$search}%");
                });
            })
            ->with(['thesis.student', 'dosen'])->orderBy('scheduled_at', 'desc')->paginate(15)
            ->appends(['search' => $search]);
            
            return view('mentoring.index', compact('sessions', 'search'));
        }

        abort(403);
    }

    public function updateStatus(Request $request, MentoringSession $session)
    {
        if (Auth::user()->role !== 'dosen') {
            abort(403);
        }

        // Verify that this dosen owns the thesis
        if ($session->thesis->pembimbing1_id !== Auth::id() && $session->thesis->pembimbing2_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,completed,absent',
            'feedback' => 'nullable|string',
        ]);

        if ($request->status === 'absent') {
            $session->update([
                'status' => 'completed',
                'is_absent' => true,
                'feedback' => $request->feedback,
            ]);
            $message = 'Sesi bimbingan ditandai sebagai: Tidak Hadir.';
        } else {
            $session->update([
                'status' => $request->status,
                'feedback' => $request->feedback ?? $session->feedback,
            ]);

            // Notify Student
            $session->thesis->student->notify(new GeneralNotification(
                'Status Bimbingan Diperbarui',
                "Status bimbingan Anda ({$session->topic}) diperbarui menjadi: " . strtoupper($request->status),
                route('mentoring-sessions.index'),
                $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'info')
            ));

            $message = 'Status sesi bimbingan diperbarui menjadi: ' . ucfirst($request->status);
        }

        ActivityLog::log('Update Status Bimbingan', "Dosen memperbarui status bimbingan ({$session->topic}) menjadi: " . strtoupper($request->status), 'Bimbingan');

        return redirect()->back()->with('success', $message);
    }

    public function uploadDocument(Request $request, MentoringSession $session)
    {
        // Only the session's student can upload
        if (Auth::user()->role !== 'mahasiswa') {
            abort(403);
        }
        if ($session->thesis->student_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:10240',
        ], [
            'document.required'  => 'Pilih file dokumen terlebih dahulu.',
            'document.mimes'     => 'Format file harus: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, atau RAR.',
            'document.max'       => 'Ukuran file maksimal 10 MB.',
        ]);

        // Delete old file if exists
        if ($session->document_path && \Storage::disk('public')->exists($session->document_path)) {
            \Storage::disk('public')->delete($session->document_path);
        }

        $file = $request->file('document');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('session-documents', 'public');

        $session->update([
            'document_path'          => $path,
            'document_original_name' => $originalName,
        ]);

        ActivityLog::log('Upload Dokumen Bimbingan', "Mahasiswa mengunggah dokumen bimbingan: {$originalName}", 'Bimbingan');

        return redirect()->back()->with('success', 'Dokumen "' . $originalName . '" berhasil diunggah.');
    }

    public function deleteDocument(MentoringSession $session)
    {
        // Only the session's student can delete
        if (Auth::user()->role !== 'mahasiswa') {
            abort(403);
        }
        if ($session->thesis->student_id !== Auth::id()) {
            abort(403);
        }

        if ($session->document_path && \Storage::disk('public')->exists($session->document_path)) {
            \Storage::disk('public')->delete($session->document_path);
        }

        $session->update([
            'document_path'          => null,
            'document_original_name' => null,
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
}

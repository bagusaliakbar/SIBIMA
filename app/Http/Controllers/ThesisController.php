<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\User;
use App\Exports\ThesesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\ActivityLog;

class ThesisController extends Controller
{
    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        return Excel::download(new ThesesExport($search), 'data-skripsi-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        
        $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2']);

        if ($user->role === 'dosen') {
            $query->where(function($q) use ($user) {
                $q->where('pembimbing1_id', $user->id)
                  ->orWhere('pembimbing2_id', $user->id);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%')
                       ->orWhere('identifier', 'like', '%' . $search . '%');
                })
                ->orWhere('title', 'like', '%' . $search . '%')
                ->orWhere('final_title', 'like', '%' . $search . '%');
            });
        }

        $theses = $query->get();
        
        $pdf = Pdf::loadView('theses.pdf', compact('theses'));
        return $pdf->download('data-skripsi-' . now()->format('Y-m-d') . '.pdf');
    }
    public function create()
    {
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect()->route('dashboard')->with('error', 'Hanya mahasiswa yang dapat mengajukan skripsi.');
        }

        // Check if student already has a thesis
        $existingThesis = Thesis::where('student_id', Auth::id())->first();
        if ($existingThesis) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah mengajukan skripsi.');
        }

        $dosens = User::where('role', 'dosen')->orderBy('name')->get();

        return view('theses.create', compact('dosens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'abstract' => 'required|string',
            'requested_pembimbing1_id' => 'nullable|exists:users,id',
            'requested_pembimbing2_id' => 'nullable|exists:users,id|different:requested_pembimbing1_id',
        ], [
            'requested_pembimbing2_id.different' => 'Usulan Pembimbing 1 dan Pembimbing 2 tidak boleh orang yang sama.',
        ]);

        $thesis = Thesis::create([
            'student_id'               => Auth::id(),
            'title'                    => $request->title,
            'abstract'                 => $request->abstract,
            'requested_pembimbing1_id' => $request->requested_pembimbing1_id ?: null,
            'requested_pembimbing2_id' => $request->requested_pembimbing2_id ?: null,
            'status'                   => 'pending',
        ]);

        ActivityLog::log('Pengajuan Judul', "Mahasiswa mengajukan judul: {$request->title}", 'Skripsi');

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralNotification(
            'Pengajuan Judul Baru',
            "Mahasiswa " . Auth::user()->name . " mengajukan judul skripsi baru.",
            route('theses.index'),
            'info'
        ));

        return redirect()->route('dashboard')->with('success', 'Pengajuan skripsi berhasil dikirim. Admin akan segera meninjau usulan pembimbing Anda.');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        if (Auth::user()->role === 'admin') {
            $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'requestedPembimbing1', 'requestedPembimbing2'])
                ->when($search, function ($query, $search) {
                    $query->whereHas('student', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('identifier', 'like', "%{$search}%");
                    })
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('final_title', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->appends(['search' => $search]);

            $dosens = User::where('role', 'dosen')->get();
            return view('theses.index', compact('theses', 'dosens', 'search'));
        } elseif (Auth::user()->role === 'dosen') {
            $theses = Thesis::with('student')
                ->where(function($q) {
                    $q->where('pembimbing1_id', Auth::id())
                      ->orWhere('pembimbing2_id', Auth::id());
                })
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('student', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%")
                               ->orWhere('identifier', 'like', "%{$search}%");
                        })
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('final_title', 'like', "%{$search}%");
                    });
                })
                ->paginate(10)
                ->appends(['search' => $search]);
            return view('theses.index', compact('theses', 'search'));
        }

        return redirect()->route('dashboard');
    }

    public function assignPembimbing(Request $request, Thesis $thesis)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'pembimbing1_id' => 'required|exists:users,id',
            'pembimbing2_id' => 'required|exists:users,id|different:pembimbing1_id',
        ], [
            'pembimbing2_id.different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh orang yang sama.'
        ]);

        $thesis->update([
            'pembimbing1_id' => $request->pembimbing1_id,
            'pembimbing2_id' => $request->pembimbing2_id,
            'status' => 'active', // Automatically active once assigned
        ]);

        ActivityLog::log('Penugasan Pembimbing', "Admin menetapkan pembimbing untuk mahasiswa {$thesis->student->name}.", 'Skripsi');

        // Notify Student & Dosens
        $student = $thesis->student;
        $p1 = $thesis->pembimbing1;
        $p2 = $thesis->pembimbing2;

        if ($student) {
            $student->notify(new GeneralNotification(
                'Dosen Pembimbing Ditetapkan',
                "Dosen pembimbing Anda telah ditetapkan: {$p1->name} dan {$p2->name}.",
                route('dashboard'),
                'success'
            ));
        }

        Notification::send([$p1, $p2], new GeneralNotification(
            'Penugasan Pembimbing Baru',
            "Anda telah ditugaskan sebagai pembimbing skripsi mahasiswa {$student->name}.",
            route('theses.index'),
            'info'
        ));

        return redirect()->back()->with('success', 'Dosen pembimbing berhasil ditugaskan.');
    }

    public function update(Request $request, Thesis $thesis)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'final_title' => 'required|string|max:255',
            'pembimbing1_id' => 'required|exists:users,id',
            'pembimbing2_id' => 'required|exists:users,id|different:pembimbing1_id',
        ], [
            'pembimbing2_id.different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh orang yang sama.'
        ]);

        $thesis->update([
            'final_title' => $request->final_title,
            'pembimbing1_id' => $request->pembimbing1_id,
            'pembimbing2_id' => $request->pembimbing2_id,
        ]);

        return redirect()->back()->with('success', 'Data skripsi berhasil diperbarui.');
    }

    public function toggleAcc(Thesis $thesis, $type)
    {
        $user = Auth::user();
        
        // Determine if user is P1 or P2
        $column = null;
        if ($user->id === $thesis->pembimbing1_id) {
            $column = $type === 'up' ? 'acc_up_p1' : 'acc_sidang_p1';
        } elseif ($user->id === $thesis->pembimbing2_id) {
            $column = $type === 'up' ? 'acc_up_p2' : 'acc_sidang_p2';
        }

        if (!$column) {
            return redirect()->back()->with('error', 'Anda tidak memiliki otoritas untuk memberikan ACC pada mahasiswa ini.');
        }

        // Toggle status
        $thesis->$column = !$thesis->$column;
        $thesis->save();

        $typeName = $type === 'up' ? 'Seminar UP' : 'Sidang Akhir';
        $statusText = $thesis->$column ? 'memberikan' : 'membatalkan';

        ActivityLog::log('ACC Bimbingan', "Dosen {$user->name} {$statusText} ACC {$typeName} untuk mahasiswa {$thesis->student->name}.", 'Skripsi');
        
        if ($thesis->$column) {
            $thesis->student->notify(new GeneralNotification(
                'ACC Pembimbing',
                "Pembimbing " . $user->name . " telah memberikan ACC untuk {$typeName}.",
                route('mentoring-sessions.index'),
                'success'
            ));
        }

        $statusText = $thesis->$column ? 'diberikan' : 'dibatalkan';
        
        return redirect()->back()->with('success', "ACC $typeName berhasil $statusText.");
    }
}

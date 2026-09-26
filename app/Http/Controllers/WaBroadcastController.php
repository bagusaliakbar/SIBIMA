<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wave;
use App\Models\WaBroadcast;
use App\Models\WaBroadcastLog;
use App\Models\Setting;
use App\Services\WaBroadcastService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WaBroadcastController extends Controller implements HasMiddleware
{
    protected $broadcastService;
    protected $whatsAppService;

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!in_array(Auth::user()->role, ['admin', 'kaprodi'])) {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function __construct(WaBroadcastService $broadcastService, WhatsAppService $whatsAppService)
    {
        $this->broadcastService = $broadcastService;
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Display broadcast history and overview statistics.
     */
    public function index(Request $request)
    {
        $broadcasts = WaBroadcast::with('sender')
            ->withCount('logs')
            ->latest()
            ->paginate(12);

        $totalBroadcasts = WaBroadcast::count();
        $totalSent = WaBroadcast::sum('successful_count');
        $totalFailed = WaBroadcast::sum('failed_count');
        $isWhatsAppEnabled = Setting::isWhatsAppEnabled();

        return view('wa_broadcasts.index', compact(
            'broadcasts',
            'totalBroadcasts',
            'totalSent',
            'totalFailed',
            'isWhatsAppEnabled'
        ));
    }

    /**
     * Show form to compose a targeted broadcast.
     */
    public function create()
    {
        $waves = Wave::orderByDesc('start_date')->get();
        $cohorts = User::where('role', 'mahasiswa')
            ->whereNotNull('entry_year')
            ->pluck('entry_year')
            ->unique()
            ->sortDesc()
            ->values();

        $senderPhone = Auth::user()->phone_number;
        $isWhatsAppEnabled = Setting::isWhatsAppEnabled();

        // Sample preset templates for quick inspiration
        $presets = [
            'mahasiswa_belum_seminar' => [
                'title' => 'Pengingat Pendaftaran Seminar Proposal Skripsi',
                'message' => "🔔 *PENGINGAT PENDAFTARAN SEMINAR PROPOSAL*\n\nHalo *{nama}* (NPM: {npm}),\n\nBerdasarkan data sistem SIBIMA, judul skripsi Anda \"_{judul}_\" telah disetujui, namun Anda *belum mendaftar seminar proposal*.\n\nMari segera tuntaskan naskah proposal Anda bersama Dosen Pembimbing:\n1. {pembimbing_1}\n2. {pembimbing_2}\n\nPendaftaran seminar gelombang terbaru dapat dilakukan melalui tautan berikut:\n{link_seminar}\n\nTetap semangat! 🎓\n_Program Studi FASILKOM UNSUB_",
            ],
            'mahasiswa_bimbingan_pasif' => [
                'title' => 'Teguran & Evaluasi Bimbingan Skripsi Pasif',
                'message' => "⚠️ *EVALUASI KEAKTIFAN BIMBINGAN SKRIPSI*\n\nHalo *{nama}* (NPM: {npm}),\n\nSistem SIBIMA mendeteksi Anda telah {hari_tanpa_bimbingan} tidak melakukan sesi bimbingan skripsi (Aktivitas terakhir: {terakhir_bimbingan}).\n\nJudul: \"_{judul}_\"\nPembimbing Utama: {pembimbing_1}\n\nMohon segera mengajukan jadwal bimbingan kembali dengan dosen pembimbing Anda untuk mencegah keterlambatan studi:\n{link_bimbingan}\n\nTerima kasih.\n_SIBIMA FASILKOM UNSUB_",
            ],
            'mahasiswa_kritis' => [
                'title' => 'Peringatan Batas Akhir Masa Studi (DO Alert)',
                'message' => "🚨 *PERINGATAN MASA STUDI - SEMESTER {semester}*\n\nHalo *{nama}* (NPM: {npm}),\n\nAnda saat ini berada pada *Semester Kritis ({semester})*. Kami menghimbau Anda untuk segera memprioritaskan penyelesaian skripsi Anda sebelum batas maksimal masa studi berakhir.\n\nSilakan segera berkonsultasi langsung dengan Ketua Program Studi atau Dosen Pembimbing untuk mencari solusi kendala penelitian Anda:\n{link_login}\n\n_Program Studi FASILKOM UNSUB_",
            ],
            'mahasiswa_belum_sidang' => [
                'title' => 'Pemberitahuan Pendaftaran Sidang Skripsi',
                'message' => "🎓 *INFORMASI PENDAFTARAN SIDANG SKRIPSI*\n\nHalo *{nama}*,\n\nSelamat atas penyelesaian revisi seminar proposal Anda! Segera lengkapi naskah final skripsi Anda dan ajukan pendaftaran sidang skripsi melalui sistem SIBIMA:\n{link_sidang}\n\nJangan menunda, selangkah lagi menuju toga wisuda! 🚀\n_SIBIMA FASILKOM UNSUB_",
            ],
            'dosen_pembimbing_aktif' => [
                'title' => 'Laporan Singkat Monitoring Bimbingan Mahasiswa',
                'message' => "Yth. Bpk/Ibu *{nama}*,\n\nTerima kasih atas dedikasi Bpk/Ibu dalam membimbing {jumlah_bimbingan} mahasiswa skripsi aktif di SIBIMA.\n\nMohon bantuannya untuk mengecek kemajuan naskah dan logbook bimbingan mahasiswa bimbingan melalui dashboard SIBIMA:\n{link_dashboard}\n\nSalam takzim,\n_Program Studi FASILKOM UNSUB_",
            ],
        ];

        return view('wa_broadcasts.create', compact('waves', 'cohorts', 'senderPhone', 'presets', 'isWhatsAppEnabled'));
    }

    /**
     * AJAX endpoint: Get real-time target recipients preview.
     */
    public function previewTargets(Request $request)
    {
        $targetType = $request->input('target_type', 'mahasiswa_belum_seminar');
        $filters = [
            'cohort' => $request->input('cohort', 'all'),
            'days' => (int) $request->input('days', 30),
            'wave_id' => $request->input('wave_id'),
        ];

        $recipients = $this->broadcastService->getTargetRecipients($targetType, $filters);

        $validPhoneCount = $recipients->filter(fn($r) => !empty($r['phone']))->count();
        $missingPhoneCount = $recipients->count() - $validPhoneCount;

        // Sample personalized message preview if template provided
        $sampleMessage = null;
        if ($request->has('template') && $recipients->isNotEmpty()) {
            $sample = $recipients->first();
            $sampleMessage = $this->broadcastService->renderPersonalizedMessage($request->input('template'), $sample['context'] ?? []);
        }

        return response()->json([
            'total' => $recipients->count(),
            'valid_phone_count' => $validPhoneCount,
            'missing_phone_count' => $missingPhoneCount,
            'recipients' => $recipients->map(function ($r) {
                return [
                    'user_id' => $r['user_id'],
                    'name' => $r['name'],
                    'identifier' => $r['identifier'],
                    'phone' => $r['phone'],
                    'has_phone' => !empty($r['phone']),
                    'cohort' => $r['cohort'],
                    'status_info' => $r['status_info'],
                ];
            }),
            'sample_preview' => $sampleMessage,
        ]);
    }

    /**
     * Send test message to Admin/Kaprodi's own number.
     */
    public function testSend(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|min:5',
            'target_type' => 'nullable|string',
            'cohort' => 'nullable|string',
        ]);

        // Get sample context if possible
        $targetType = $request->input('target_type', 'mahasiswa_belum_seminar');
        $sample = $this->broadcastService->getTargetRecipients($targetType, [
            'cohort' => $request->input('cohort', 'all'),
        ])->first();

        $context = $sample['context'] ?? [
            'nama' => Auth::user()->name,
            'npm' => Auth::user()->identifier ?? '0410019202',
            'angkatan' => '2022',
            'judul' => 'Rancang Bangun Sistem Informasi SIBIMA Berbasis Web',
            'pembimbing_1' => 'Bagus Ali Akbar, S.SI., M.Kom',
            'pembimbing_2' => 'Tazkia Salsabila Ardan, M.Kom',
            'link_seminar' => route('seminar-applications.create'),
            'link_login' => url('/login'),
        ];

        $rendered = $this->broadcastService->renderPersonalizedMessage($request->input('message'), $context);
        $testHeader = "🧪 *[PESAN UJI COBA BROADCAST SIBIMA]*\n_Sasaran simulasi: {$context['nama']}_\n\n";

        $success = $this->whatsAppService->sendMessage($request->input('phone'), $testHeader . $rendered);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan uji coba berhasil terkirim ke ' . $request->input('phone'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim pesan uji coba. Periksa token Fonnte atau nomor telepon Anda.',
        ], 500);
    }

    /**
     * Store and execute the broadcast.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'target_type' => 'required|string',
            'message_template' => 'required|string|min:10',
            'delay_seconds' => 'nullable|integer|min:1|max:15',
            'selected_user_ids' => 'nullable|array',
            'selected_user_ids.*' => 'integer',
        ], [
            'title.required' => 'Judul broadcast wajib diisi sebagai arsip internal.',
            'message_template.required' => 'Isi pesan WhatsApp wajib diisi.',
            'message_template.min' => 'Isi pesan minimal 10 karakter.',
        ]);

        $filters = [
            'cohort' => $request->input('cohort', 'all'),
            'days' => (int) $request->input('days', 30),
            'wave_id' => $request->input('wave_id'),
        ];

        $broadcast = WaBroadcast::create([
            'sender_id' => Auth::id(),
            'title' => $request->input('title'),
            'target_type' => $request->input('target_type'),
            'target_filter' => $filters,
            'message_template' => $request->input('message_template'),
            'delay_seconds' => (int) ($request->input('delay_seconds') ?: 4),
            'status' => 'processing',
        ]);

        $selectedIds = $request->input('selected_user_ids');

        // Execute broadcast
        $result = $this->broadcastService->executeBroadcast($broadcast, $selectedIds);

        return redirect()->route('wa-broadcasts.show', $broadcast)
            ->with('success', "Siaran berhasil diproses! Terkirim: {$result['successful']}, Gagal: {$result['failed']}.");
    }

    /**
     * Show detailed broadcast report & delivery logs.
     */
    public function show(WaBroadcast $waBroadcast)
    {
        $waBroadcast->load('sender');
        $logs = WaBroadcastLog::where('wa_broadcast_id', $waBroadcast->id)
            ->orderBy('id')
            ->paginate(30);

        return view('wa_broadcasts.show', compact('waBroadcast', 'logs'));
    }

    /**
     * Resend failed messages for a specific broadcast.
     */
    public function resendFailed(WaBroadcast $waBroadcast)
    {
        $failedLogs = WaBroadcastLog::where('wa_broadcast_id', $waBroadcast->id)
            ->where('status', 'failed')
            ->get();

        if ($failedLogs->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada pesan berstatus gagal pada siaran ini.');
        }

        $resendSuccess = 0;
        $delay = max(1, (int) $waBroadcast->delay_seconds);

        foreach ($failedLogs as $index => $log) {
            if (empty($log->recipient_phone)) continue;

            if ($index > 0 && $delay > 0) {
                sleep($delay);
            }

            try {
                $sent = $this->whatsAppService->sendMessage($log->recipient_phone, $log->message_content);
                if ($sent) {
                    $log->update([
                        'status' => 'sent',
                        'error_message' => null,
                        'sent_at' => now(),
                    ]);
                    $resendSuccess++;
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        // Recalculate stats
        $totalSent = WaBroadcastLog::where('wa_broadcast_id', $waBroadcast->id)->where('status', 'sent')->count();
        $totalFailed = WaBroadcastLog::where('wa_broadcast_id', $waBroadcast->id)->where('status', 'failed')->count();

        $waBroadcast->update([
            'successful_count' => $totalSent,
            'failed_count' => $totalFailed,
        ]);

        return redirect()->back()->with('success', "Proses kirim ulang selesai. {$resendSuccess} pesan berhasil terkirim.");
    }
}

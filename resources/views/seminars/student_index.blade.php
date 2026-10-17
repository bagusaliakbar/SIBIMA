<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pendaftaran Seminar', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        @if($isEligible)
            <!-- Physical Documents Notice -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-md p-6 mb-6">
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg mr-4 shadow-sm">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-blue-800 dark:text-blue-200 uppercase tracking-widest mb-3">Penting: Berkas Fisik (Penyerahan ke BAAK)</h3>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mb-4 font-medium">Selain mengunggah berkas digital di bawah ini, Anda wajib mengumpulkan berkas fisik berikut langsung ke kantor BAAK:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Dokumen Skripsi:</strong> Cetak rangkap 3 (Khusus jika pelaksanaan Seminar dilakukan secara Offline).</span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Formulir Pendaftaran Seminar:</strong> Wajib sudah ditandatangani Dosen Pembimbing dan dibubuhi materai.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        @if(!$isEligible)
            @php
                $p1 = $thesis?->pembimbing1;
                $p2 = $thesis?->pembimbing2;
                $p1Sessions = $thesis ? $thesis->mentoringSessions()->where('dosen_id', $thesis->pembimbing1_id)->where('status', 'completed')->where('is_absent', false)->count() : 0;
                $p2Sessions = $thesis && $thesis->pembimbing2_id ? $thesis->mentoringSessions()->where('dosen_id', $thesis->pembimbing2_id)->where('status', 'completed')->where('is_absent', false)->count() : 0;
                $totalSessions = $p1Sessions + $p2Sessions;
                $accP1 = (bool) ($thesis?->acc_up_p1);
                $accP2 = (bool) ($thesis?->acc_up_p2);
                $hasActiveWave = !empty($activeWave);

                $criteria = [
                    [
                        'number' => 1,
                        'title' => 'Pengajuan Judul Skripsi Disetujui',
                        'desc' => $thesis ? ($thesis->final_title ?: $thesis->title) : 'Belum mengajukan usulan judul skripsi.',
                        'is_met' => (bool) $thesis,
                        'status_text' => $thesis ? 'Terpenuhi' : 'Belum Ada Judul',
                        'badge_type' => $thesis ? 'emerald' : 'rose',
                        'action_hint' => $thesis ? null : 'Silakan ajukan judul skripsi Anda terlebih dahulu melalui menu Pengajuan Judul.',
                        'action_url' => $thesis ? null : route('theses.create'),
                        'action_label' => $thesis ? null : 'Ajukan Judul',
                    ],
                    [
                        'number' => 2,
                        'title' => 'Aktivitas Bimbingan Skripsi Proposal',
                        'desc' => "Tercatat {$totalSessions} sesi bimbingan selesai disetujui (P1: {$p1Sessions} sesi • P2: {$p2Sessions} sesi).",
                        'is_met' => $totalSessions >= 4,
                        'status_text' => $totalSessions >= 4 ? "Terpenuhi ({$totalSessions} Sesi)" : "{$totalSessions} / 4 Sesi Selesai",
                        'badge_type' => $totalSessions >= 4 ? 'emerald' : 'amber',
                        'action_hint' => $totalSessions >= 4 ? null : 'Lakukan sesi bimbingan proposal secara berkala dan pastikan presensi kehadiran divalidasi oleh dosen.',
                        'action_url' => route('mentoring-sessions.index'),
                        'action_label' => 'Buka Logbook',
                    ],
                    [
                        'number' => 3,
                        'title' => 'Persetujuan (ACC Seminar UP) Pembimbing 1',
                        'desc' => $p1 ? "Dosen Pembimbing 1: {$p1->name}" : 'Dosen Pembimbing 1 belum ditetapkan oleh Program Studi.',
                        'is_met' => $accP1,
                        'status_text' => $accP1 ? 'Disetujui (ACC UP)' : 'Menunggu Persetujuan',
                        'badge_type' => $accP1 ? 'emerald' : 'amber',
                        'action_hint' => $accP1 ? null : ($p1 ? "Silakan konsultasikan naskah proposal Bab 1–3 kepada Bpk/Ibu {$p1->name} untuk mendapatkan ACC UP di SIBIMA." : 'Menunggu penetapan dosen pembimbing oleh Kaprodi.'),
                        'action_url' => route('mentoring-sessions.index'),
                        'action_label' => 'Jadwalkan Bimbingan',
                    ],
                    [
                        'number' => 4,
                        'title' => 'Persetujuan (ACC Seminar UP) Pembimbing 2',
                        'desc' => $p2 ? "Dosen Pembimbing 2: {$p2->name}" : ($thesis?->pembimbing2_id ? 'Dosen tidak aktif' : 'Belum ditugaskan / Opsional'),
                        'is_met' => $accP2,
                        'status_text' => $accP2 ? 'Disetujui (ACC UP)' : 'Menunggu Persetujuan',
                        'badge_type' => $accP2 ? 'emerald' : 'amber',
                        'action_hint' => $accP2 ? null : ($p2 ? "Silakan konsultasikan naskah proposal Bab 1–3 kepada Bpk/Ibu {$p2->name} untuk mendapatkan ACC UP di SIBIMA." : 'Menunggu penetapan dosen pembimbing oleh Kaprodi.'),
                        'action_url' => route('mentoring-sessions.index'),
                        'action_label' => 'Jadwalkan Bimbingan',
                    ],
                ];

                $metCount = count(array_filter($criteria, fn($c) => $c['is_met']));
                $totalCriteria = count($criteria);
                $percentMet = round(($metCount / $totalCriteria) * 100);
            @endphp

            <div class="space-y-6">
                <!-- Pre-requisite Checklist Master Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 relative overflow-hidden">
                    <!-- Subtle Glow Decoration -->
                    <div class="absolute -top-20 -right-20 w-56 h-56 bg-amber-500/10 dark:bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Header & Overall Progress -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100 dark:border-slate-700/80 relative z-10">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h3 class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">
                                    📋 Kesiapan Pendaftaran Seminar Proposal
                                </h3>
                                <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider {{ $metCount === $totalCriteria ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80' }}">
                                    {{ $metCount }} dari {{ $totalCriteria }} Prasyarat Terpenuhi
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed max-w-2xl">
                                Formulir pendaftaran seminar proposal akan <strong class="text-slate-700 dark:text-slate-200">terbuka secara otomatis</strong> setelah seluruh checklist prasyarat di bawah ini berstatus terpenuhi.
                            </p>
                        </div>

                        <!-- Progress Bar & Percentage Pill -->
                        <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-900/60 p-3.5 sm:p-4 rounded-2xl border border-slate-100 dark:border-slate-700/60 shrink-0">
                            <div class="text-right">
                                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1">Status Kesiapan</p>
                                <p class="text-xl sm:text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $percentMet }}%</p>
                            </div>
                            <div class="w-16 sm:w-20 bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                <div class="h-2.5 rounded-full transition-all duration-700 {{ $metCount === $totalCriteria ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $percentMet }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Dual Supervisor Direct Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-6 relative z-10">
                        <!-- Pembimbing 1 -->
                        <div class="p-4 rounded-2xl border transition-all {{ $accP1 ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/50' : 'bg-slate-50/70 dark:bg-slate-900/50 border-slate-200/80 dark:border-slate-700/80' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/40 flex items-center justify-center text-xs font-black shrink-0">
                                        P1
                                    </span>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Dosen Pembimbing 1</p>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                            {{ $p1 ? $p1->name : 'Belum Ditugaskan' }}
                                        </h4>
                                    </div>
                                </div>
                                <div>
                                    @if($accP1)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-500 text-white shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Sudah ACC</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Belum ACC</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span>Bimbingan Selesai:</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $p1Sessions }} Sesi</span>
                            </div>
                        </div>

                        <!-- Pembimbing 2 -->
                        <div class="p-4 rounded-2xl border transition-all {{ $accP2 ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/50' : 'bg-slate-50/70 dark:bg-slate-900/50 border-slate-200/80 dark:border-slate-700/80' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/40 flex items-center justify-center text-xs font-black shrink-0">
                                        P2
                                    </span>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Dosen Pembimbing 2</p>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                            {{ $p2 ? $p2->name : ($thesis?->pembimbing2_id ? '-' : 'Belum Ditugaskan') }}
                                        </h4>
                                    </div>
                                </div>
                                <div>
                                    @if($accP2)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-500 text-white shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Sudah ACC</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Belum ACC</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span>Bimbingan Selesai:</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $p2Sessions }} Sesi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Interactive Criteria List -->
                    <div class="space-y-3 mb-6 relative z-10">
                        @foreach($criteria as $c)
                            <div class="p-4 rounded-2xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $c['is_met'] ? 'bg-emerald-50/30 dark:bg-emerald-950/15 border-emerald-200/70 dark:border-emerald-800/40' : 'bg-slate-50/60 dark:bg-slate-900/40 border-slate-200/70 dark:border-slate-700/60' }}">
                                <div class="flex items-start gap-3.5">
                                    <!-- Indicator Icon -->
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $c['is_met'] ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                        @if($c['is_met'])
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <span class="text-xs font-black">{{ $c['number'] }}</span>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">
                                                {{ $c['title'] }}
                                            </h4>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $c['is_met'] ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300' }}">
                                                {{ $c['status_text'] }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                            {{ $c['desc'] }}
                                        </p>
                                        @if($c['action_hint'])
                                            <p class="text-[11px] font-medium text-amber-600 dark:text-amber-400 mt-1.5 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>{{ $c['action_hint'] }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if($c['action_url'])
                                    <div class="shrink-0 self-end sm:self-center">
                                        <a href="{{ $c['action_url'] }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all">
                                            <span>{{ $c['action_label'] }}</span>
                                            <span>➔</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Wave Availability Info Banner -->
                    <div class="mb-6 p-4 rounded-2xl flex items-center justify-between gap-3 text-xs border {{ $hasActiveWave ? 'bg-blue-50/60 dark:bg-blue-950/30 border-blue-200/70 dark:border-blue-800/40 text-blue-800 dark:text-blue-200' : 'bg-slate-100/70 dark:bg-slate-900/40 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full {{ $hasActiveWave ? 'bg-blue-500 animate-pulse' : 'bg-slate-400' }}"></span>
                            <span class="font-bold">Status Periode Gelombang:</span>
                            <span>{{ $hasActiveWave ? "{$activeWave->name} (Sedang Dibuka)" : 'Belum Ada Gelombang Seminar yang Dibuka' }}</span>
                        </div>
                        <span class="text-[10px] uppercase font-black tracking-wider px-2 py-0.5 rounded-md bg-white/70 dark:bg-slate-800/70 border border-slate-200/50 dark:border-slate-700/50">
                            {{ $hasActiveWave ? 'Aktif' : 'Tutup' }}
                        </span>
                    </div>

                    <!-- Contextual Action Bar (Replaces dead disabled button) -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                                    Formulir Pendaftaran Seminar Terkunci
                                </h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Masih ada <strong class="text-amber-600 dark:text-amber-400">{{ $totalCriteria - $metCount }} prasyarat</strong> yang belum lengkap. Tombol akan aktif otomatis begitu syarat terpenuhi.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 w-full sm:w-auto shrink-0">
                            <button type="button" disabled class="w-full sm:w-auto px-5 py-2.5 bg-slate-200 dark:bg-slate-700/60 text-slate-400 dark:text-slate-500 rounded-xl text-xs font-black uppercase tracking-wider cursor-not-allowed flex items-center justify-center gap-2 shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>Daftar Terkunci</span>
                            </button>
                            <a href="{{ route('mentoring-sessions.index') }}" class="w-full sm:w-auto px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm hover:scale-105 active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                                <span>Buka Bimbingan</span>
                                <span>➔</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($application)
            <!-- Status Application -->
            <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 {{ $application->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-900/10' : ($application->status === 'rejected' ? 'bg-red-50 dark:bg-red-900/10' : 'bg-amber-50 dark:bg-amber-900/10') }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold {{ $application->status === 'approved' ? 'text-emerald-800 dark:text-emerald-400' : ($application->status === 'rejected' ? 'text-red-800 dark:text-red-400' : 'text-amber-800 dark:text-amber-400') }}">
                                Status Pengajuan: {{ strtoupper($application->status) }}
                            </h3>
                            <p class="text-sm {{ $application->status === 'approved' ? 'text-emerald-600 dark:text-emerald-500/70' : ($application->status === 'rejected' ? 'text-red-600 dark:text-red-500/70' : 'text-amber-600 dark:text-amber-500/70') }} mt-1">
                                Diajukan pada {{ $application->created_at->locale('id')->translatedFormat('d M Y • H:i') }}
                            </p>
                        </div>
                        <div class="px-4 py-2 rounded-full font-bold text-xs uppercase tracking-widest border {{ $application->status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : ($application->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800') }}">
                            {{ $application->status }}
                        </div>
                    </div>
                </div>
                
                @if($application->admin_feedback)
                    <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                        <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Catatan Admin:</h4>
                        <p class="text-sm text-slate-700 dark:text-slate-300 italic">"{{ $application->admin_feedback }}"</p>
                    </div>
                @endif

                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-700/80">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Detail Berkas Persyaratan</h4>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800/60">
                                    5 Berkas Terlampir
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Dokumen pendaftaran seminar yang telah Anda ajukan untuk diverifikasi.</p>
                        </div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-700/70 text-slate-500 dark:text-slate-400 text-xs">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="font-medium text-[11px]">Tersimpan di Google Drive</span>
                        </div>
                    </div>

                    @php
                        $fileFields = [
                            'file_formulir' => [
                                'label' => 'Formulir Seminar',
                                'subtitle' => 'Formulir Pendaftaran Resmi',
                                'category' => 'Administrasi',
                                'theme' => [
                                    'bg' => 'bg-indigo-50 dark:bg-indigo-950/40',
                                    'text' => 'text-indigo-600 dark:text-indigo-400',
                                    'border' => 'border-indigo-100 dark:border-indigo-800/50',
                                ],
                                'svg' => '
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 13l1.5 1.5 3-3" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 17h8" />
                                '
                            ],
                            'file_pembayaran' => [
                                'label' => 'Bukti Pembayaran',
                                'subtitle' => 'Kwitansi / Slip Bukti Bayar',
                                'category' => 'Keuangan',
                                'theme' => [
                                    'bg' => 'bg-emerald-50 dark:bg-emerald-950/40',
                                    'text' => 'text-emerald-600 dark:text-emerald-400',
                                    'border' => 'border-emerald-100 dark:border-emerald-800/50',
                                ],
                                'svg' => '
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" />
                                    <rect x="7" y="11" width="10" height="7" rx="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 14h10m-7 2h2" />
                                '
                            ],
                            'file_acc_pembimbing' => [
                                'label' => 'Bukti ACC Pembimbing',
                                'subtitle' => 'Persetujuan Seminar P1 & P2',
                                'category' => 'Persetujuan',
                                'theme' => [
                                    'bg' => 'bg-amber-50 dark:bg-amber-950/40',
                                    'text' => 'text-amber-600 dark:text-amber-400',
                                    'border' => 'border-amber-100 dark:border-amber-800/50',
                                ],
                                'svg' => '
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" />
                                    <circle cx="12" cy="14.5" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.8 14.5l1 1 1.7-1.7" />
                                '
                            ],
                            'file_kartu_bimbingan' => [
                                'label' => 'Kartu Bimbingan',
                                'subtitle' => 'Rekap Logbook Bimbingan',
                                'category' => 'Akademik',
                                'theme' => [
                                    'bg' => 'bg-sky-50 dark:bg-sky-950/40',
                                    'text' => 'text-sky-600 dark:text-sky-400',
                                    'border' => 'border-sky-100 dark:border-sky-800/50',
                                ],
                                'svg' => '
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M11 12h5" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 15h.01M11 15h5" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 18h.01M11 18h3" />
                                '
                            ],
                            'file_skripsi' => [
                                'label' => 'Soft File Skripsi',
                                'subtitle' => 'Draft Naskah Proposal / Skripsi',
                                'category' => 'Naskah Ilmiah',
                                'theme' => [
                                    'bg' => 'bg-purple-50 dark:bg-purple-950/40',
                                    'text' => 'text-purple-600 dark:text-purple-400',
                                    'border' => 'border-purple-100 dark:border-purple-800/50',
                                ],
                                'svg' => '
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11.5l-4.5 2 4.5 2 4.5-2-4.5-2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.5 14.8v1.8c0 .7 1.1 1.2 2.5 1.2s2.5-.5 2.5-1.2v-1.8" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 13.5v2" />
                                '
                            ],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($fileFields as $field => $info)
                            @php
                                $review = $application->file_reviews[$field] ?? null;
                                $isRejected = ($review['status'] ?? '') === 'rejected';
                                $isApproved = ($review['status'] ?? '') === 'approved';
                                $hasFile = !empty($application->$field);
                            @endphp
                            <div class="group relative flex flex-col justify-between p-4 rounded-2xl border transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 {{ $isRejected ? 'border-rose-200 bg-rose-50/40 dark:border-rose-900/60 dark:bg-rose-950/20' : 'border-slate-200/80 dark:border-slate-700/80 bg-white dark:bg-slate-800/90 hover:border-slate-300 dark:hover:border-slate-600' }}">
                                <div>
                                    <!-- Top row: Icon Badge + Category + Status Pill -->
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 border {{ $info['theme']['bg'] }} {{ $info['theme']['text'] }} {{ $info['theme']['border'] }} shadow-2xs transition-transform group-hover:scale-105">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    {!! $info['svg'] !!}
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                                    {{ $info['category'] }}
                                                </span>
                                                <h5 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors leading-snug truncate">
                                                    {{ $info['label'] }}
                                                </h5>
                                            </div>
                                        </div>

                                        <!-- Status Pill -->
                                        <div class="shrink-0">
                                            @if($isApproved)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Valid</span>
                                                </span>
                                            @elseif($isRejected)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                    <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    <span>Revisi</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-400 border border-slate-200/60 dark:border-slate-600/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                    <span>Diproses</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mb-3">
                                        {{ $info['subtitle'] }}
                                    </p>

                                    <!-- Rejection Note if any -->
                                    @if($isRejected && !empty($review['note']))
                                        <div class="mb-3 p-2.5 bg-rose-100/70 dark:bg-rose-900/40 rounded-xl border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-300">
                                            <div class="flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider">
                                                <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span>Catatan Validator:</span>
                                            </div>
                                            <p class="text-xs italic mt-0.5 leading-relaxed">"{{ $review['note'] }}"</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Footer: Action link to Google Drive -->
                                <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2 mt-auto">
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        <span>Google Drive</span>
                                    </span>

                                    @if($hasFile)
                                        <a href="{{ $application->$field }}" target="_blank" rel="noopener noreferrer" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-orange-600 dark:text-orange-400 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/40 dark:hover:bg-orange-900/50 border border-orange-200/80 dark:border-orange-800/50 transition-all shadow-2xs group-hover:shadow-xs cursor-pointer">
                                            <span>Buka Dokumen</span>
                                            <svg class="w-3.5 h-3.5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Belum diunggah</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- 6th Tile: Quick Helper Card to balance 2x3 grid perfectly -->
                        <div class="p-4 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/30 flex flex-col justify-between">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-500 border border-orange-200/60 dark:border-orange-800/40 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                        Panduan Akses
                                    </span>
                                    <h5 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug">
                                        Izin Akses Berkas Drive
                                    </h5>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mt-1">
                                        Pastikan link Google Drive diatur ke <strong>"Anyone with the link can view"</strong> agar berkas dapat ditinjau oleh BAAK / Kaprodi.
                                    </p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-slate-200/60 dark:border-slate-700/60 mt-3 flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500">
                                <span>Status Sinkronisasi</span>
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($application->status === 'rejected')
                        <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-3 mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/50">
                                <div class="p-2 bg-amber-100 dark:bg-amber-800 rounded-lg">
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-xs text-amber-700 dark:text-amber-300 font-bold">Silakan unggah ulang berkas yang ditolak (<span class="text-rose-600">REJECT</span>) di bawah ini:</p>
                            </div>

                            <form action="{{ route('seminar-applications.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    @foreach($fileFields as $field => $info)
                                        @if(isset($application->file_reviews[$field]['status']) && $application->file_reviews[$field]['status'] === 'rejected')
                                            <div class="space-y-2">
                                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ $info['label'] }} <span class="text-red-500">*</span></label>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold tracking-wide mb-2">
                                                    Masukkan link dari Google Drive.
                                                </p>
                                                <input type="url" name="{{ $field }}" value="{{ old($field, $application->$field ?? '') }}" placeholder="https://drive.google.com/..." required class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900 shadow-sm py-2 px-3">
                                                @error($field) <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="mt-8 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-orange-600 text-white font-bold rounded-md hover:bg-orange-700 transition-colors shadow-md">
                                        Perbarui Pengajuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Upload Form -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden"
                 x-data="{
                    activeTab: 'all',
                    batchFolderUrl: '',
                    files: {
                        file_formulir: '{{ old('file_formulir', $application->file_formulir ?? '') }}',
                        file_pembayaran: '{{ old('file_pembayaran', $application->file_pembayaran ?? '') }}',
                        file_acc_pembimbing: '{{ old('file_acc_pembimbing', $application->file_acc_pembimbing ?? '') }}',
                        file_kartu_bimbingan: '{{ old('file_kartu_bimbingan', $application->file_kartu_bimbingan ?? '') }}',
                        file_skripsi: '{{ old('file_skripsi', $application->file_skripsi ?? '') }}'
                    },
                    applyBatchFolderUrl() {
                        if (!this.batchFolderUrl || !this.batchFolderUrl.trim().startsWith('http')) {
                            alert('Silakan masukkan link Google Drive yang valid (dimulai dengan https://)');
                            return;
                        }
                        const url = this.batchFolderUrl.trim();
                        Object.keys(this.files).forEach(k => {
                            this.files[k] = url;
                        });
                    },
                    async pasteSingle(field) {
                        try {
                            const text = await navigator.clipboard.readText();
                            if (text) {
                                this.files[field] = text.trim();
                            }
                        } catch (e) {
                            alert('Gunakan tombol pintasan Ctrl + V untuk menempelkan link.');
                        }
                    },
                    isDrive(url) {
                        return url && url.includes('drive.google.com');
                    },
                    countFilled(keys) {
                        return keys.filter(k => this.files[k] && this.files[k].trim() !== '').length;
                    },
                    get totalFilled() {
                        return Object.values(this.files).filter(v => v && v.trim() !== '').length;
                    },
                    get totalCount() {
                        return Object.keys(this.files).length;
                    },
                    get percentFilled() {
                        return Math.round((this.totalFilled / this.totalCount) * 100);
                    }
                 }">
                
                <!-- Header Card -->
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">Form Pengajuan Seminar</h3>
                                @if($activeWave)
                                    <span class="px-2.5 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 text-[10px] font-black rounded-lg uppercase tracking-widest border border-indigo-200 dark:border-indigo-800/50">
                                        {{ $activeWave->name }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 text-[10px] font-black rounded-lg uppercase tracking-widest border border-rose-200 dark:border-rose-800/80">
                                        Gelombang Belum Dibuka
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Lengkapi 5 berkas persyaratan seminar berikut dengan tautan Google Drive yang dapat diakses publik.</p>
                        </div>
                        
                        @if($template)
                            <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xs">
                                <div class="p-2 bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Unduh Formulir</div>
                                    <a href="{{ route('download.private', ['path' => $template->file_path]) }}" target="_blank" class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline">
                                        {{ $template->title }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if(!$activeWave)
                    <div class="m-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 flex items-start gap-3">
                        <div class="p-2 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-amber-900 dark:text-amber-100">Gelombang Pendaftaran Belum Dibuka</h4>
                            <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5 leading-relaxed">
                                Seluruh persetujuan (ACC UP) dari dosen pembimbing Anda telah berstatus lengkap ✅. Namun, saat ini periode gelombang pendaftaran seminar belum dibuka oleh Program Studi. Tombol kirim berkas akan aktif secara otomatis saat gelombang dibuka.
                            </p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('seminar-applications.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- 1. Smart Batch Folder Filler Banner -->
                    <div class="p-4 bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 dark:from-slate-900 dark:via-slate-800/80 dark:to-slate-900 border border-orange-200/80 dark:border-slate-700 rounded-2xl shadow-2xs">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                            <div class="flex items-start sm:items-center gap-3">
                                <div class="p-2.5 bg-orange-600 text-white rounded-xl shadow-xs shrink-0 mt-0.5 sm:mt-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">💡 Punya 1 Folder Google Drive untuk Semua Berkas?</h4>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">Jika seluruh scan berkas Anda berada di dalam satu folder Google Drive, tempelkan link foldernya di sini untuk mengisi 5 kolom sekaligus secara instan.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 w-full lg:w-auto">
                                <input type="url" 
                                       x-model="batchFolderUrl" 
                                       placeholder="https://drive.google.com/drive/folders/..." 
                                       class="py-2 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 w-full lg:w-72 shadow-2xs">
                                <button type="button" 
                                        @click="applyBatchFolderUrl()" 
                                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-xs shrink-0 active:scale-95 cursor-pointer">
                                    Terapkan ke Semua
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Real-Time Progress Bar -->
                    <div class="p-4 bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                <span>📊 Status Kelengkapan Berkas:</span>
                                <span class="font-black text-orange-600 dark:text-orange-400" x-text="totalFilled + ' dari ' + totalCount + ' Berkas Terisi'"></span>
                            </span>
                            <span class="text-xs font-black" :class="totalFilled === totalCount ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400'" x-text="percentFilled + '%'"></span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-500 rounded-full" 
                                 :class="totalFilled === totalCount ? 'bg-emerald-500' : 'bg-gradient-to-r from-orange-500 to-amber-500'" 
                                 :style="'width: ' + percentFilled + '%'"></div>
                        </div>
                    </div>

                    @php
                        $categories = [
                            'utama' => [
                                'title' => 'Berkas Pendaftaran & Administrasi',
                                'icon' => 'document',
                                'keys' => ['file_formulir', 'file_pembayaran', 'file_acc_pembimbing'],
                                'fields' => [
                                    'file_formulir' => ['label' => '1. Formulir Pendaftaran Seminar (Terisi)', 'desc' => 'Unduh formulir pendaftaran seminar, isi lengkap, lalu unggah ke Google Drive.'],
                                    'file_pembayaran' => ['label' => '2. Scan Bukti Pembayaran Seminar', 'desc' => 'Bukti transfer atau kwitansi resmi pembayaran biaya pendaftaran seminar.'],
                                    'file_acc_pembimbing' => ['label' => '3. Scan Bukti ACC Pembimbing 1 & 2', 'desc' => 'Tangkapan layar / scan bukti persetujuan seminar dari Pembimbing 1 & 2.'],
                                ]
                            ],
                            'skripsi' => [
                                'title' => 'Naskah Skripsi & Bimbingan',
                                'icon' => 'academic',
                                'keys' => ['file_kartu_bimbingan', 'file_skripsi'],
                                'fields' => [
                                    'file_kartu_bimbingan' => ['label' => '4. Scan Kartu Bimbingan / Logbook', 'desc' => 'Scan kartu bimbingan yang telah divalidasi atau rekap logbook bimbingan P1 & P2.'],
                                    'file_skripsi' => ['label' => '5. Dokumen Soft File Skripsi', 'desc' => 'Naskah proposal / skripsi lengkap (Cover hingga Bab Pembahasan/Lampiran).'],
                                ]
                            ]
                        ];
                    @endphp

                    <!-- 3. Category Filter Tabs (Sleek Border-Bottom Design) -->
                    <div class="border-b border-slate-200 dark:border-slate-700 -mx-6 px-6">
                        <div class="flex items-center gap-1 sm:gap-2 overflow-x-auto no-scrollbar">
                            <!-- All Categories Tab -->
                            <button type="button" 
                                    @click="activeTab = 'all'" 
                                    class="px-4 py-3 border-b-2 text-xs font-bold transition-all whitespace-nowrap inline-flex items-center gap-2 cursor-pointer shrink-0"
                                    :class="activeTab === 'all' ? 'border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50/50 dark:bg-orange-500/10' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/40'">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                <span>Semua Kategori</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold transition-colors" 
                                      :class="totalFilled === totalCount ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200/80 text-slate-600 dark:bg-slate-700 dark:text-slate-300'"
                                      x-text="totalFilled + '/' + totalCount"></span>
                            </button>

                            @foreach($categories as $catKey => $cat)
                                <button type="button" 
                                        @click="activeTab = '{{ $catKey }}'" 
                                        class="px-4 py-3 border-b-2 text-xs font-bold transition-all whitespace-nowrap inline-flex items-center gap-2 cursor-pointer shrink-0"
                                        :class="activeTab === '{{ $catKey }}' ? 'border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50/50 dark:bg-orange-500/10' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/40'">
                                    @if($cat['icon'] === 'document')
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @elseif($cat['icon'] === 'academic')
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                    @endif
                                    <span>{{ $cat['title'] }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold transition-colors"
                                          :class="countFilled({{ json_encode($cat['keys']) }}) === {{ count($cat['keys']) }} ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200/80 text-slate-600 dark:bg-slate-700 dark:text-slate-300'"
                                          x-text="countFilled({{ json_encode($cat['keys']) }}) + '/{{ count($cat['keys']) }}'"></span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- 4. Grouped Input Fields -->
                    <div class="space-y-8">
                        @foreach($categories as $catKey => $cat)
                            <div x-show="activeTab === 'all' || activeTab === '{{ $catKey }}'" x-cloak class="space-y-4">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 bg-orange-100 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 rounded-lg">
                                            @if($cat['icon'] === 'document')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            @elseif($cat['icon'] === 'academic')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $cat['title'] }}</h4>
                                    </div>
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg"
                                          :class="countFilled({{ json_encode($cat['keys']) }}) === {{ count($cat['keys']) }} ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                                          x-text="countFilled({{ json_encode($cat['keys']) }}) + ' dari {{ count($cat['keys']) }} Terisi'"></span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($cat['fields'] as $field => $info)
                                        <div class="p-4 bg-slate-50/60 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl transition-all hover:border-orange-300 dark:hover:border-orange-500/40 hover:bg-white dark:hover:bg-slate-900 space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <label for="{{ $field }}" class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                                                    {{ $info['label'] }} <span class="text-rose-500">*</span>
                                                </label>
                                                <button type="button" 
                                                        @click="pasteSingle('{{ $field }}')" 
                                                        class="px-2 py-0.5 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 rounded-md text-[10px] font-bold transition-all shadow-2xs cursor-pointer active:scale-95 shrink-0"
                                                        title="Tempel tautan dari clipboard">
                                                    📋 Tempel
                                                </button>
                                            </div>
                                            
                                            <p class="text-[11px] text-slate-400 dark:text-slate-500 leading-tight">{{ $info['desc'] }}</p>

                                            <div class="relative">
                                                <input type="url" 
                                                       name="{{ $field }}" 
                                                       id="{{ $field }}" 
                                                       x-model="files['{{ $field }}']" 
                                                       placeholder="https://drive.google.com/..." 
                                                       {{ isset($application->$field) ? '' : 'required' }} 
                                                       class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-xl border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-800 shadow-2xs py-2 px-3 transition-all">
                                            </div>

                                            <!-- Live Status Indicator -->
                                            <div class="flex items-center justify-between pt-1">
                                                <template x-if="isDrive(files['{{ $field }}'])">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Google Drive Valid</span>
                                                    </span>
                                                </template>
                                                <template x-if="files['{{ $field }}'] && !isDrive(files['{{ $field }}'])">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                                        <span>🔗 Link Terisi</span>
                                                    </span>
                                                </template>
                                                <template x-if="!files['{{ $field }}']">
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Belum diisi</span>
                                                </template>

                                                <template x-if="files['{{ $field }}']">
                                                    <a :href="files['{{ $field }}']" target="_blank" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-0.5">
                                                        <span>Buka Link</span>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    </a>
                                                </template>
                                            </div>
                                            @error($field) <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 pt-6 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            Pastikan seluruh link Google Drive telah disetel ke mode <b class="text-slate-700 dark:text-slate-200">"Anyone with the link can view" (Siapa saja yang memiliki link dapat melihat)</b>.
                        </div>
                        @if($activeWave)
                            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md active:scale-95 cursor-pointer">
                                Kirim Pengajuan Seminar
                            </button>
                        @else
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <button type="button" disabled class="w-full sm:w-auto px-8 py-3 bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 text-xs font-black uppercase tracking-widest rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    <span>Pendaftaran Belum Dibuka</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>

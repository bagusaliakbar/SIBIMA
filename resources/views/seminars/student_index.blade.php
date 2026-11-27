<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pendaftaran Seminar', 'route' => null]
        ]" />
    </x-slot>

    @php
        $student = Auth::user();
        $period = $defaultAcademicPeriod ?? \App\Models\SeminarApplication::getDefaultAcademicPeriod();
        $studyProgramFixed = \App\Models\SeminarApplication::DEFAULT_STUDY_PROGRAM;
    @endphp

    <div class="w-full">
        @if($isEligible)
            <!-- Physical Documents Notice -->
            <div class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/60 rounded-2xl p-5 mb-6">
                <div class="flex items-start gap-4">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/60 rounded-xl shrink-0">
                        <svg class="w-5 h-5 text-blue-700 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-xs font-bold text-blue-900 dark:text-blue-200 uppercase tracking-wider mb-1.5">Penting: Berkas Fisik (Penyerahan ke BAAK)</h3>
                        <p class="text-xs text-blue-800 dark:text-blue-300 mb-3">Selain melengkapi formulir digital dan 4 tautan berkas di bawah ini, Anda wajib menyerahkan berkas fisik berikut ke kantor BAAK:</p>
                        <ul class="space-y-2">
                            <li class="flex items-start text-xs text-blue-800 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 mr-2.5 shrink-0"></span>
                                <span><strong>Dokumen Skripsi:</strong> Cetak rangkap 3 (khusus jika pelaksanaan Seminar dilakukan secara Offline).</span>
                            </li>
                            <li class="flex items-start text-xs text-blue-800 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 mr-2.5 shrink-0"></span>
                                <span><strong>Formulir Pendaftaran Seminar (Cetak dari SIBIMA):</strong> Setelah mengirim pengajuan di bawah ini, klik tombol <strong>Cetak Formulir (PDF)</strong>, tempel <strong>Materai 10.000</strong>, bubuhkan tanda tangan Anda, lalu serahkan ke BAAK.</span>
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
                        'tag' => 'Judul Skripsi',
                        'title' => 'Pengajuan & Persetujuan Judul Skripsi',
                        'desc' => $thesis ? ($thesis->final_title ?: $thesis->title) : 'Belum mengajukan usulan judul skripsi ke sistem SIBIMA.',
                        'is_met' => (bool) $thesis,
                        'status_text' => $thesis ? 'Terpenuhi' : 'Belum Ada Judul',
                        'badge_type' => $thesis ? 'emerald' : 'rose',
                        'extra_info' => $thesis ? ($thesis->topic ? 'Topik: ' . $thesis->topic : null) : null,
                        'action_hint' => $thesis ? null : 'Silakan ajukan judul skripsi Anda terlebih dahulu melalui menu Pengajuan Judul.',
                        'action_url' => $thesis ? null : route('theses.create'),
                        'action_label' => $thesis ? null : 'Ajukan Judul',
                    ],
                    [
                        'number' => 2,
                        'tag' => 'Bimbingan',
                        'title' => 'Aktivitas Bimbingan Skripsi Proposal',
                        'desc' => $totalSessions >= 4
                            ? "Tercatat {$totalSessions} sesi bimbingan selesai disetujui (P1: {$p1Sessions} sesi • P2: {$p2Sessions} sesi). Target minimal terpenuhi."
                            : "Tercatat {$totalSessions} dari minimal 4 sesi bimbingan proposal selesai (kurang " . (4 - $totalSessions) . " sesi).",
                        'is_met' => $totalSessions >= 4,
                        'status_text' => $totalSessions >= 4 ? "Terpenuhi ({$totalSessions} Sesi)" : "{$totalSessions} / 4 Sesi",
                        'badge_type' => $totalSessions >= 4 ? 'emerald' : 'amber',
                        'extra_info' => "Progres Sesi: P1 ({$p1Sessions} Sesi) • P2 ({$p2Sessions} Sesi)",
                        'action_hint' => $totalSessions >= 4 ? null : 'Lakukan sesi bimbingan proposal secara berkala dan pastikan presensi logbook divalidasi oleh dosen.',
                        'action_url' => route('mentoring-sessions.index'),
                        'action_label' => 'Buka Logbook',
                    ],
                    [
                        'number' => 3,
                        'tag' => 'Pembimbing 1',
                        'title' => 'Persetujuan (ACC Seminar UP) Pembimbing 1',
                        'desc' => $p1
                            ? "Dosen Pembimbing 1: {$p1->name}"
                            : 'Dosen Pembimbing 1 belum ditetapkan oleh Program Studi.',
                        'is_met' => $accP1,
                        'status_text' => $accP1 ? 'Sudah ACC' : 'Belum ACC',
                        'badge_type' => $accP1 ? 'emerald' : 'amber',
                        'extra_info' => $p1 ? "Bimbingan Selesai: {$p1Sessions} Sesi" : null,
                        'action_hint' => $accP1 ? null : ($p1 ? "Konsultasikan naskah proposal Bab 1–3 kepada Bpk/Ibu {$p1->name} untuk mendapatkan ACC UP di SIBIMA." : 'Menunggu penetapan dosen pembimbing oleh Kaprodi.'),
                        'action_url' => $p1 ? route('mentoring-sessions.index') : null,
                        'action_label' => $p1 ? 'Jadwalkan Bimbingan' : null,
                    ],
                    [
                        'number' => 4,
                        'tag' => 'Pembimbing 2',
                        'title' => 'Persetujuan (ACC Seminar UP) Pembimbing 2',
                        'desc' => $p2
                            ? "Dosen Pembimbing 2: {$p2->name}"
                            : ($thesis?->pembimbing2_id ? 'Dosen tidak aktif' : 'Belum ditugaskan oleh Program Studi'),
                        'is_met' => $accP2,
                        'status_text' => $accP2 ? 'Sudah ACC' : 'Belum ACC',
                        'badge_type' => $accP2 ? 'emerald' : 'amber',
                        'extra_info' => $p2 ? "Bimbingan Selesai: {$p2Sessions} Sesi" : null,
                        'action_hint' => $accP2 ? null : ($p2 ? "Konsultasikan naskah proposal Bab 1–3 kepada Bpk/Ibu {$p2->name} untuk mendapatkan ACC UP di SIBIMA." : 'Menunggu penetapan dosen pembimbing oleh Kaprodi.'),
                        'action_url' => $p2 ? route('mentoring-sessions.index') : null,
                        'action_label' => $p2 ? 'Jadwalkan Bimbingan' : null,
                    ],
                ];

                $metCount = count(array_filter($criteria, fn($c) => $c['is_met']));
                $totalCriteria = count($criteria);
                $percentMet = round(($metCount / $totalCriteria) * 100);
            @endphp

            <div class="space-y-6">
                <!-- Pre-requisite Checklist Master Card -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 sm:p-8 relative overflow-hidden">
                    <!-- Header & Overall Progress -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h3 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                                    Kesiapan Pendaftaran Seminar Proposal
                                </h3>
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $metCount === $totalCriteria ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60' : 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60' }}">
                                    {{ $metCount }} dari {{ $totalCriteria }} Prasyarat Terpenuhi
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed max-w-2xl">
                                Formulir pendaftaran seminar proposal akan <strong class="text-slate-700 dark:text-slate-200">terbuka secara otomatis</strong> setelah seluruh checklist prasyarat di bawah ini berstatus terpenuhi.
                            </p>
                        </div>

                        <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800/60 p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shrink-0">
                            <div class="text-right">
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider leading-none mb-1">Status Kesiapan</p>
                                <p class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">{{ $percentMet }}%</p>
                            </div>
                            <div class="w-16 sm:w-20 bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                <div class="h-2.5 rounded-full transition-all duration-700 {{ $metCount === $totalCriteria ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $percentMet }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between my-5 pt-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Checklist Prasyarat Pendaftaran
                        </span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                            Lengkapi seluruh poin di bawah untuk membuka form
                        </span>
                    </div>

                    <div class="space-y-3 mb-6">
                        @foreach($criteria as $c)
                            <div class="p-4 rounded-2xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $c['is_met'] ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/50' : 'bg-slate-50/60 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700/80' }}">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $c['is_met'] ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                        @if($c['is_met'])
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <span class="text-xs font-bold">{{ $c['number'] }}</span>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @if(!empty($c['tag']))
                                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider {{ $c['is_met'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">
                                                    {{ $c['tag'] }}
                                                </span>
                                            @endif
                                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">
                                                {{ $c['title'] }}
                                            </h4>
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $c['is_met'] ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300' : 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300' }}">
                                                {{ $c['status_text'] }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                            {{ $c['desc'] }}
                                        </p>
                                        @if(!empty($c['extra_info']))
                                            <div class="mt-1.5 flex items-center gap-2 text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                                    {{ $c['extra_info'] }}
                                                </span>
                                            </div>
                                        @endif
                                        @if($c['action_hint'])
                                            <p class="text-[11px] font-medium text-amber-700 dark:text-amber-400 mt-1.5 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>{{ $c['action_hint'] }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if($c['action_url'])
                                    <div class="shrink-0 self-end sm:self-center">
                                        <a href="{{ $c['action_url'] }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition-colors">
                                            <span>{{ $c['action_label'] }}</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-6 p-4 rounded-2xl flex items-center justify-between gap-3 text-xs border {{ $hasActiveWave ? 'bg-blue-50/70 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800/60 text-blue-800 dark:text-blue-200' : 'bg-slate-100/70 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full {{ $hasActiveWave ? 'bg-blue-500 animate-pulse' : 'bg-slate-400' }}"></span>
                            <span class="font-bold">Status Periode Gelombang:</span>
                            <span>{{ $hasActiveWave ? "{$activeWave->name} (Sedang Dibuka)" : 'Belum Ada Gelombang Seminar yang Dibuka' }}</span>
                        </div>
                        <span class="text-[11px] uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-md bg-white/80 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700">
                            {{ $hasActiveWave ? 'Aktif' : 'Tutup' }}
                        </span>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100">
                                    Formulir Pendaftaran Seminar Terkunci
                                </h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Masih ada <strong class="text-amber-700 dark:text-amber-400">{{ $totalCriteria - $metCount }} prasyarat</strong> yang belum lengkap. Formulir akan aktif otomatis begitu syarat terpenuhi.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 w-full sm:w-auto shrink-0">
                            <button type="button" disabled class="w-full sm:w-auto h-10 px-4 bg-slate-200 dark:bg-slate-700/60 text-slate-500 dark:text-slate-400 rounded-xl text-xs font-bold uppercase tracking-wider cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>Daftar Terkunci</span>
                            </button>
                            <a href="{{ route('mentoring-sessions.index') }}" class="w-full sm:w-auto h-10 px-5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-colors shadow-sm flex items-center justify-center gap-2">
                                <span>Buka Bimbingan</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($application)
            @php
                $hasDigitalForm = $application->hasDigitalForm();
                $resolvedForm = $application->getResolvedFormData();
                $isFormulirRejected = ($application->file_reviews['file_formulir']['status'] ?? '') === 'rejected';
            @endphp

            <!-- Status Application -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-800 {{ $application->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/30' : ($application->status === 'rejected' ? 'bg-rose-50 dark:bg-rose-950/30' : 'bg-amber-50 dark:bg-amber-950/30') }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h3 class="text-base font-bold {{ $application->status === 'approved' ? 'text-emerald-900 dark:text-emerald-300' : ($application->status === 'rejected' ? 'text-rose-900 dark:text-rose-300' : 'text-amber-900 dark:text-amber-300') }}">
                                    Status Pengajuan: {{ strtoupper($application->status) }}
                                </h3>
                                <span class="px-3 py-0.5 rounded-full font-bold text-[11px] uppercase tracking-wider border {{ $application->status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : ($application->status === 'rejected' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800') }}">
                                    {{ $application->status === 'approved' ? 'Disetujui' : ($application->status === 'rejected' ? 'Perlu Revisi' : 'Menunggu Validasi') }}
                                </span>
                            </div>
                            <p class="text-xs {{ $application->status === 'approved' ? 'text-emerald-700 dark:text-emerald-400' : ($application->status === 'rejected' ? 'text-rose-700 dark:text-rose-400' : 'text-amber-700 dark:text-amber-400') }} mt-1">
                                Diajukan pada {{ $application->created_at->locale('id')->translatedFormat('d M Y • H:i') }} WIB
                                @if($application->wave)
                                    • {{ $application->wave->name }}
                                @endif
                            </p>
                        </div>

                        @if($hasDigitalForm)
                            <div class="flex items-center gap-2 flex-wrap shrink-0">
                                <a href="{{ route('seminar-applications.form-pdf', ['application' => $application->id, 'inline' => 1]) }}"
                                   target="_blank"
                                   class="h-10 inline-flex items-center justify-center gap-1.5 px-3.5 rounded-xl text-xs font-bold bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Pratinjau PDF</span>
                                </a>
                                <a href="{{ route('seminar-applications.form-pdf', $application->id) }}"
                                   class="h-10 inline-flex items-center justify-center gap-1.5 px-4 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white shadow-sm transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Cetak Formulir (PDF)</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                @if($application->admin_feedback)
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40">
                        <h4 class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Catatan Validator Admin / Kaprodi:</h4>
                        <p class="text-sm text-slate-700 dark:text-slate-200 italic">"{{ $application->admin_feedback }}"</p>
                    </div>
                @endif

                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Detail Persyaratan Pendaftaran Seminar</h4>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60">
                                    {{ $hasDigitalForm ? '1 Formulir Digital + 4 Berkas Drive' : '5 Berkas Terlampir' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Ringkasan formulir digital dan tautan berkas yang telah Anda ajukan untuk diverifikasi.</p>
                        </div>
                    </div>

                    @php
                        $fileFields = [
                            'file_formulir' => [
                                'label' => 'Formulir Pendaftaran Seminar',
                                'subtitle' => $hasDigitalForm ? 'Formulir Pendaftaran Seminar' : 'Formulir Pendaftaran Seminar',
                                'category' => $hasDigitalForm ? 'Formulir Digital' : 'Administrasi',
                                'theme' => [
                                    'bg' => 'bg-blue-50 dark:bg-blue-950/40',
                                    'text' => 'text-blue-700 dark:text-blue-300',
                                    'border' => 'border-blue-200 dark:border-blue-800/60',
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
                                'subtitle' => 'Kwitansi / Slip Bukti Bayar Seminar',
                                'category' => 'Keuangan',
                                'theme' => [
                                    'bg' => 'bg-emerald-50 dark:bg-emerald-950/40',
                                    'text' => 'text-emerald-700 dark:text-emerald-300',
                                    'border' => 'border-emerald-200 dark:border-emerald-800/60',
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
                                    'text' => 'text-amber-700 dark:text-amber-300',
                                    'border' => 'border-amber-200 dark:border-amber-800/60',
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
                                'subtitle' => 'Rekap Logbook Bimbingan P1 & P2',
                                'category' => 'Akademik',
                                'theme' => [
                                    'bg' => 'bg-slate-100 dark:bg-slate-800',
                                    'text' => 'text-slate-700 dark:text-slate-300',
                                    'border' => 'border-slate-200 dark:border-slate-700',
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
                                'subtitle' => 'Draf Naskah Proposal / Skripsi',
                                'category' => 'Naskah Ilmiah',
                                'theme' => [
                                    'bg' => 'bg-rose-50 dark:bg-rose-950/40',
                                    'text' => 'text-rose-700 dark:text-rose-300',
                                    'border' => 'border-rose-200 dark:border-rose-800/60',
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($fileFields as $field => $info)
                            @php
                                $review = $application->file_reviews[$field] ?? null;
                                $isRejected = ($review['status'] ?? '') === 'rejected';
                                $isApproved = ($review['status'] ?? '') === 'approved';
                                $isDigitalCard = $field === 'file_formulir' && $hasDigitalForm;
                                $hasFile = $isDigitalCard || !empty($application->$field);
                            @endphp
                            <div class="group relative flex flex-col justify-between p-4 rounded-2xl border transition-all {{ $field === 'file_formulir' ? 'md:col-span-2' : '' }} {{ $isRejected ? 'border-rose-200 bg-rose-50/40 dark:border-rose-800/60 dark:bg-rose-950/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300 dark:hover:border-slate-700' }}">
                                <div>
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border {{ $info['theme']['bg'] }} {{ $info['theme']['text'] }} {{ $info['theme']['border'] }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    {!! $info['svg'] !!}
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                                    {{ $info['category'] }}
                                                </span>
                                                <h5 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug truncate">
                                                    {{ $info['label'] }}
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="shrink-0">
                                            @if($isApproved)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Valid</span>
                                                </span>
                                            @elseif($isRejected)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    <span>Revisi</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    <span>Diproses</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mb-3">
                                        {{ $info['subtitle'] }}
                                    </p>

                                    @if($isRejected && !empty($review['note']))
                                        <div class="mb-3 p-2.5 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-300">
                                            <div class="flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span>Catatan Validator:</span>
                                            </div>
                                            <p class="text-xs italic mt-0.5 leading-relaxed">"{{ $review['note'] }}"</p>
                                        </div>
                                    @endif
                                </div>

                                @if($isDigitalCard)
                                    <div class="mt-auto p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-slate-600 dark:text-slate-300">
                                        <div>
                                            <span class="block text-[11px] text-slate-500 dark:text-slate-400">Program Studi / Jenjang</span>
                                            <span class="block font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $resolvedForm['study_program'] }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[11px] text-slate-500 dark:text-slate-400">Tempat / Tanggal Lahir</span>
                                            <span class="block font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $resolvedForm['birth_place'] }}, {{ $resolvedForm['birth_date'] ? \Carbon\Carbon::parse($resolvedForm['birth_date'])->locale('id')->translatedFormat('d M Y') : '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[11px] text-slate-500 dark:text-slate-400">Semester / Tahun Akademik</span>
                                            <span class="block font-semibold text-slate-800 dark:text-slate-100 mt-0.5">Semester {{ $resolvedForm['semester_number'] }} / {{ $resolvedForm['academic_year'] }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 mt-auto">
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                            <span>Google Drive</span>
                                        </span>

                                        @if($hasFile)
                                            <a href="{{ $application->$field }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-orange-700 dark:text-orange-400 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/40 dark:hover:bg-orange-900/50 border border-orange-200 dark:border-orange-800/50 transition-colors">
                                                <span>Buka Dokumen</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @else
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400 italic">Belum diunggah</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($application->status === 'rejected')
                        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-3 mb-6 p-4 bg-amber-50 dark:bg-amber-950/30 rounded-2xl border border-amber-200 dark:border-amber-800/60">
                                <div class="p-2 bg-amber-100 dark:bg-amber-900/60 rounded-xl shrink-0">
                                    <svg class="w-5 h-5 text-amber-700 dark:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-xs text-amber-800 dark:text-amber-200 font-bold">Silakan perbaiki data formulir atau unggah ulang berkas yang berstatus revisi di bawah ini:</p>
                            </div>

                            @if($errors->any())
                                <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-200 text-xs">
                                    <p class="font-bold mb-1">Periksa kembali isian berikut:</p>
                                    <ul class="list-disc list-inside space-y-0.5">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('seminar-applications.store') }}" method="POST" class="space-y-6">
                                @csrf

                                @if($isFormulirRejected)
                                    <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 space-y-4">
                                        <div>
                                            <h5 class="text-sm font-bold text-slate-800 dark:text-slate-100">Perbaikan Formulir Pendaftaran Seminar Digital</h5>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui biodata di bawah ini sesuai catatan validator. Sistem akan memperbarui PDF formulir secara otomatis.</p>
                                        </div>

                                        <input type="hidden" name="study_program" value="{{ $studyProgramFixed }}">

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tempat Lahir <span class="text-rose-500">*</span></label>
                                                <input type="text" name="birth_place" value="{{ old('birth_place', $resolvedForm['birth_place'] !== '-' ? $resolvedForm['birth_place'] : ($student->birth_place ?? '')) }}" required class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                                @error('birth_place') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Lahir <span class="text-rose-500">*</span></label>
                                                <input type="date" name="birth_date" value="{{ old('birth_date', $resolvedForm['birth_date'] ?? ($student->birth_date ? $student->birth_date->format('Y-m-d') : '')) }}" required class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                                @error('birth_date') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Semester <span class="text-rose-500">*</span></label>
                                                <input type="number" min="1" max="20" name="semester_number" value="{{ old('semester_number', is_numeric($resolvedForm['semester_number']) ? $resolvedForm['semester_number'] : ($student->current_semester ?? 8)) }}" required class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                                @error('semester_number') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tahun Akademik <span class="text-rose-500">*</span></label>
                                                <input type="text" name="academic_year" value="{{ old('academic_year', $resolvedForm['academic_year'] !== '-' ? $resolvedForm['academic_year'] : $period['label']) }}" required class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                                @error('academic_year') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">E-mail Aktif <span class="text-rose-500">*</span></label>
                                                <input type="email" name="email" value="{{ old('email', $resolvedForm['email'] !== '-' ? $resolvedForm['email'] : $student->email) }}" required class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                                @error('email') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">No. HP / WhatsApp <span class="text-rose-500">*</span></label>
                                                <input type="text" name="phone_number" value="{{ old('phone_number', $resolvedForm['phone_number'] !== '-' ? $resolvedForm['phone_number'] : ($student->phone_number ?? '')) }}" required class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                                @error('phone_number') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Lengkap <span class="text-rose-500">*</span></label>
                                                <textarea name="address" rows="2" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">{{ old('address', $resolvedForm['address'] !== '-' ? $resolvedForm['address'] : ($student->address ?? '')) }}</textarea>
                                                @error('address') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kode Pos <span class="text-rose-500">*</span></label>
                                                <input type="text" name="postal_code" value="{{ old('postal_code', $resolvedForm['postal_code'] !== '-' ? $resolvedForm['postal_code'] : ($student->postal_code ?? '')) }}" required class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                                @error('postal_code') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Telepon Rumah <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                                <input type="text" name="home_phone" value="{{ old('home_phone', $resolvedForm['home_phone'] !== '-' ? $resolvedForm['home_phone'] : ($student->home_phone ?? '')) }}" placeholder="Kosongkan jika tidak ada" class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                                @error('home_phone') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </div>

                                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 cursor-pointer">
                                            <input type="checkbox" name="statement_agreed" value="1" required {{ old('statement_agreed', '1') ? 'checked' : '' }} class="mt-0.5 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                            <span class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                                                Dengan ini saya menyatakan siap mengikuti Seminar Skripsi/Tugas Akhir dan bersedia mengoreksi serta memperbaiki kesalahan dan kekurangan berdasarkan saran dari dosen pembimbing dan penguji.
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                @php
                                    $rejectedDriveFields = collect(['file_pembayaran', 'file_acc_pembimbing', 'file_kartu_bimbingan', 'file_skripsi'])
                                        ->filter(fn($f) => ($application->file_reviews[$f]['status'] ?? '') === 'rejected');
                                @endphp

                                @if($rejectedDriveFields->isNotEmpty())
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($rejectedDriveFields as $field)
                                            @php $info = $fileFields[$field]; @endphp
                                            <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 space-y-2">
                                                <label class="block text-xs font-bold text-slate-800 dark:text-slate-100">{{ $info['label'] }} <span class="text-rose-500">*</span></label>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Masukkan tautan baru Google Drive yang dapat dilihat publik.</p>
                                                <input type="url" name="{{ $field }}" value="{{ old($field, $application->$field ?? '') }}" placeholder="https://drive.google.com/..." required class="w-full h-10 px-3 text-xs text-slate-800 dark:text-slate-100 rounded-xl border border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900">
                                                @error($field) <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex justify-end">
                                    <button type="submit" class="h-10 px-6 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-colors shadow-sm cursor-pointer">
                                        Perbarui Pengajuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- New Registration Form: Digital Form (Tahap 1) + 4 Google Drive Links (Tahap 2) -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden"
                 x-data="{
                    activeTab: 'all',
                    batchFolderUrl: '',
                    biodata: {
                        birth_place: @js(old('birth_place', $student->birth_place ?? '')),
                        birth_date: @js(old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '')),
                        semester_number: @js((string) old('semester_number', $student->current_semester ?? 8)),
                        academic_year: @js(old('academic_year', $period['label'])),
                        email: @js(old('email', $student->email ?? '')),
                        phone_number: @js(old('phone_number', $student->phone_number ?? '')),
                        address: @js(old('address', $student->address ?? '')),
                        postal_code: @js(old('postal_code', $student->postal_code ?? '')),
                        home_phone: @js(old('home_phone', $student->home_phone ?? '')),
                        statement_agreed: @js((bool) old('statement_agreed', false)),
                    },
                    files: {
                        file_pembayaran: @js(old('file_pembayaran', '')),
                        file_acc_pembimbing: @js(old('file_acc_pembimbing', '')),
                        file_kartu_bimbingan: @js(old('file_kartu_bimbingan', '')),
                        file_skripsi: @js(old('file_skripsi', ''))
                    },
                    applyBatchFolderUrl() {
                        if (!this.batchFolderUrl || !this.batchFolderUrl.trim().startsWith('http')) {
                            window.sibimaToast('Silakan masukkan link Google Drive yang valid (dimulai dengan https://)', 'warning', 'Tautan Tidak Valid');
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
                            window.sibimaToast('Gunakan tombol pintasan Ctrl + V untuk menempelkan link.', 'info', 'Pintasan Tempel');
                        }
                    },
                    isDrive(url) {
                        return url && url.includes('drive.google.com');
                    },
                    countFilled(keys) {
                        return keys.filter(k => this.files[k] && this.files[k].trim() !== '').length;
                    },
                    get biodataFilledCount() {
                        const requiredKeys = ['birth_place', 'birth_date', 'semester_number', 'academic_year', 'email', 'phone_number', 'address', 'postal_code'];
                        let count = requiredKeys.filter(k => String(this.biodata[k] || '').trim() !== '').length;
                        if (this.biodata.statement_agreed) count += 1;
                        return count;
                    },
                    get biodataTotalCount() {
                        return 9;
                    },
                    get isBiodataComplete() {
                        return this.biodataFilledCount === this.biodataTotalCount;
                    },
                    get totalFilesFilled() {
                        return Object.values(this.files).filter(v => v && v.trim() !== '').length;
                    },
                    get totalFilesCount() {
                        return Object.keys(this.files).length;
                    },
                    get overallFilled() {
                        return (this.isBiodataComplete ? 1 : 0) + this.totalFilesFilled;
                    },
                    get overallTotal() {
                        return 1 + this.totalFilesCount;
                    },
                    get percentFilled() {
                        return Math.round((this.overallFilled / this.overallTotal) * 100);
                    }
                 }">

                <!-- Header Card -->
                <div class="p-6 border-b border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Form Pendaftaran Seminar Proposal</h3>
                                @if($activeWave)
                                    <span class="px-2.5 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 text-[11px] font-bold rounded-lg uppercase tracking-wider border border-blue-200 dark:border-blue-800/60">
                                        {{ $activeWave->name }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 text-[11px] font-bold rounded-lg uppercase tracking-wider border border-rose-200 dark:border-rose-800/60">
                                        Gelombang Belum Dibuka
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Lengkapi Formulir Pendaftaran Digital (otomatis menjadi PDF resmi BAAK) dan 4 tautan berkas Google Drive.
                            </p>
                        </div>

                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-xs font-semibold shrink-0">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Formulir Otomatis Cetak PDF</span>
                        </div>
                    </div>
                </div>

                @if(!$activeWave)
                    <div class="m-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 flex items-start gap-3">
                        <div class="p-2 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 dark:text-amber-200">Gelombang Pendaftaran Belum Dibuka</h4>
                            <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5 leading-relaxed">
                                Seluruh persetujuan (ACC UP) dari dosen pembimbing Anda telah lengkap. Namun, saat ini periode gelombang pendaftaran seminar belum dibuka oleh Program Studi. Tombol kirim akan aktif otomatis saat gelombang dibuka.
                            </p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mx-6 mt-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-200 text-xs">
                        <p class="font-bold mb-1">Terdapat isian yang belum valid atau belum lengkap:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('seminar-applications.store') }}" method="POST" class="p-6 space-y-8">
                    @csrf
                    <input type="hidden" name="study_program" value="{{ $studyProgramFixed }}">

                    <!-- Overall Progress Bar -->
                    <div class="p-4 bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 rounded-2xl space-y-2">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs font-bold">
                            <span class="text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                <span>Progres Kelengkapan Pendaftaran:</span>
                                <span class="text-orange-600 dark:text-orange-400" x-text="overallFilled + ' dari ' + overallTotal + ' Syarat Lengkap'"></span>
                            </span>
                            <span class="text-xs font-bold" :class="overallFilled === overallTotal ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400'" x-text="percentFilled + '%'"></span>
                        </div>
                        <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-300 rounded-full"
                                 :class="overallFilled === overallTotal ? 'bg-emerald-500' : 'bg-orange-600'"
                                 :style="'width: ' + percentFilled + '%'"></div>
                        </div>
                    </div>

                    <!-- ========================================================= -->
                    <!-- TAHAP 1: FORMULIR PENDAFTARAN SEMINAR DIGITAL (RESMI BAAK) -->
                    <!-- ========================================================= -->
                    <div class="space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-orange-600 text-white text-xs font-bold flex items-center justify-center shrink-0">1</span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Formulir Pendaftaran Seminar Digital (Data Pribadi &amp; Kesiapan)</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Data inti ditarik otomatis dari SIBIMA. Lengkapi biodata tambahan untuk mencetak PDF resmi BAAK.</p>
                                </div>
                            </div>
                            <span class="self-start sm:self-center px-2.5 py-1 rounded-lg text-[11px] font-bold border"
                                  :class="isBiodataComplete ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60' : 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60'"
                                  x-text="isBiodataComplete ? 'Formulir Lengkap' : (biodataFilledCount + '/' + biodataTotalCount + ' Isian Terisi')"></span>
                        </div>

                        <!-- A.1 Auto-Pulled Read-Only Academic Summary -->
                        <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Data Terintegrasi Otomatis dari SIBIMA</span>
                                <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">Otomatis Huruf Kapital di PDF</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400">Nama Lengkap</span>
                                    <span class="block font-bold text-slate-800 dark:text-slate-100 uppercase mt-0.5">{{ $student->name }}</span>
                                </div>
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400">NPM</span>
                                    <span class="block font-mono font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $student->identifier ?? '-' }}</span>
                                </div>
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400">Program Studi / Jenjang</span>
                                    <span class="block font-bold text-slate-800 dark:text-slate-100 uppercase mt-0.5">{{ $studyProgramFixed }}</span>
                                </div>
                                <div class="sm:col-span-2 lg:col-span-3 p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400">Judul Skripsi / Tugas Akhir</span>
                                    <span class="block font-bold text-slate-800 dark:text-slate-100 uppercase mt-0.5 leading-relaxed">{{ $thesis?->display_title ?? '-' }}</span>
                                </div>
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400">Dosen Pembimbing 1</span>
                                    <span class="block font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $thesis?->pembimbing1?->name ?? '-' }}</span>
                                    <span class="inline-block mt-1 text-[11px] font-semibold {{ $thesis?->pembimbing1?->signature ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">
                                        {{ $thesis?->pembimbing1?->signature ? 'TTD Digital + QR otomatis terpasang di PDF' : 'Area TTD basah disiapkan di PDF' }}
                                    </span>
                                </div>
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400">Dosen Pembimbing 2</span>
                                    <span class="block font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $thesis?->pembimbing2?->name ?? '-' }}</span>
                                    <span class="inline-block mt-1 text-[11px] font-semibold {{ $thesis?->pembimbing2?->signature ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">
                                        {{ $thesis?->pembimbing2?->signature ? 'TTD Digital + QR otomatis terpasang di PDF' : 'Area TTD basah disiapkan di PDF' }}
                                    </span>
                                </div>
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400">Tanda Tangan Mahasiswa</span>
                                    <span class="block font-bold text-slate-800 dark:text-slate-100 mt-0.5">Kosong + Kotak Materai 10.000</span>
                                    <span class="inline-block mt-1 text-[11px] text-slate-500 dark:text-slate-400">Dicetak lalu tempel materai fisik untuk BAAK</span>
                                </div>
                            </div>
                        </div>

                        <!-- A.2 Editable Personal Biodata Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="birth_place" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Tempat Lahir <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="birth_place"
                                       name="birth_place"
                                       x-model="biodata.birth_place"
                                       placeholder="Contoh: Subang"
                                       required
                                       class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                @error('birth_place') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="birth_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Tanggal Lahir <span class="text-rose-500">*</span>
                                </label>
                                <input type="date"
                                       id="birth_date"
                                       name="birth_date"
                                       x-model="biodata.birth_date"
                                       required
                                       class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                @error('birth_date') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="semester_number" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Semester Saat Ini <span class="text-rose-500">*</span>
                                </label>
                                <input type="number"
                                       id="semester_number"
                                       name="semester_number"
                                       min="1"
                                       max="20"
                                       x-model="biodata.semester_number"
                                       required
                                       class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                @error('semester_number') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="academic_year" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Tahun Akademik <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="academic_year"
                                       name="academic_year"
                                       x-model="biodata.academic_year"
                                       placeholder="Contoh: Ganjil 2026/2027"
                                       required
                                       class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                @error('academic_year') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    E-mail Aktif <span class="text-rose-500">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       x-model="biodata.email"
                                       placeholder="nama@email.com"
                                       required
                                       class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                @error('email') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="phone_number" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    No. HP / WhatsApp <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="phone_number"
                                       name="phone_number"
                                       x-model="biodata.phone_number"
                                       placeholder="08xxxxxxxxxx"
                                       required
                                       class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                @error('phone_number') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Alamat Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <textarea id="address"
                                          name="address"
                                          rows="2"
                                          x-model="biodata.address"
                                          placeholder="Contoh: Jl. R.A Kartini KM 3, RT 01/RW 02, Kel. Pasirkareumbi, Kec. Subang, Kab. Subang"
                                          required
                                          class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500"></textarea>
                                @error('address') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="postal_code" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Kode Pos <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="postal_code"
                                       name="postal_code"
                                       x-model="biodata.postal_code"
                                       placeholder="Contoh: 41211"
                                       required
                                       class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                @error('postal_code') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="home_phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Telepon Rumah <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input type="text"
                                       id="home_phone"
                                       name="home_phone"
                                       x-model="biodata.home_phone"
                                       placeholder="Kosongkan jika tidak ada"
                                       class="w-full h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                                @error('home_phone') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- B. Pernyataan Kesiapan Seminar -->
                        <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/60 space-y-2">
                            <span class="block text-[11px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">Pernyataan Kesiapan Seminar</span>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox"
                                       name="statement_agreed"
                                       value="1"
                                       x-model="biodata.statement_agreed"
                                       required
                                       class="mt-0.5 rounded border-amber-400 text-orange-600 focus:ring-orange-500">
                                <span class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed font-medium">
                                    Dengan ini saya menyatakan siap mengikuti Seminar Skripsi/Tugas Akhir dan bersedia mengoreksi serta memperbaiki kesalahan dan kekurangan berdasarkan saran dari dosen pembimbing dan penguji.
                                </span>
                            </label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 pl-7">
                                Biodata yang Anda lengkapi juga otomatis disimpan ke profil SIBIMA agar dapat dipakai kembali saat Pendaftaran Sidang Skripsi.
                            </p>
                            @error('statement_agreed') <p class="text-rose-600 dark:text-rose-400 text-[11px] pl-7 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- ========================================================= -->
                    <!-- TAHAP 2: 4 LINK BERKAS PERSYARATAN GOOGLE DRIVE           -->
                    <!-- ========================================================= -->
                    <div class="space-y-5 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-orange-600 text-white text-xs font-bold flex items-center justify-center shrink-0">2</span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Tautan Berkas Persyaratan (4 Berkas Google Drive)</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Upload formulir sudah tidak diperlukan — cukup lampirkan 4 tautan berkas pendukung di bawah ini.</p>
                                </div>
                            </div>
                            <span class="self-start sm:self-center px-2.5 py-1 rounded-lg text-[11px] font-bold border"
                                  :class="totalFilesFilled === totalFilesCount ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60' : 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                                  x-text="totalFilesFilled + '/' + totalFilesCount + ' Berkas Terisi'"></span>
                        </div>

                        <!-- Smart Batch Folder Filler Banner -->
                        <div class="p-4 bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 rounded-2xl">
                            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                                <div class="flex items-start sm:items-center gap-3">
                                    <div class="p-2.5 bg-orange-600 text-white rounded-xl shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Punya 1 Folder Google Drive untuk Semua Berkas?</h5>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Tempelkan tautan folder Google Drive Anda di sini untuk mengisi 4 kolom berkas sekaligus.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 w-full lg:w-auto">
                                    <input type="url"
                                           x-model="batchFolderUrl"
                                           placeholder="https://drive.google.com/drive/folders/..."
                                           class="h-10 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-orange-500 focus:border-orange-500 w-full lg:w-72">
                                    <button type="button"
                                            @click="applyBatchFolderUrl()"
                                            class="h-10 px-4 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-colors shrink-0 cursor-pointer">
                                        Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>

                        @php
                            $categories = [
                                'utama' => [
                                    'title' => 'Pembayaran & Persetujuan Pembimbing',
                                    'icon' => 'document',
                                    'keys' => ['file_pembayaran', 'file_acc_pembimbing'],
                                    'fields' => [
                                        'file_pembayaran' => ['label' => '1. Scan Bukti Pembayaran Seminar', 'desc' => 'Bukti transfer atau kwitansi resmi pembayaran biaya pendaftaran seminar.'],
                                        'file_acc_pembimbing' => ['label' => '2. Scan Bukti ACC Pembimbing 1 & 2', 'desc' => 'Tangkapan layar / scan bukti persetujuan seminar dari Pembimbing 1 & 2.'],
                                    ]
                                ],
                                'skripsi' => [
                                    'title' => 'Naskah Skripsi & Bimbingan',
                                    'icon' => 'academic',
                                    'keys' => ['file_kartu_bimbingan', 'file_skripsi'],
                                    'fields' => [
                                        'file_kartu_bimbingan' => ['label' => '3. Scan Kartu Bimbingan / Logbook', 'desc' => 'Scan kartu bimbingan yang telah divalidasi atau rekap logbook bimbingan P1 & P2.'],
                                        'file_skripsi' => ['label' => '4. Dokumen Soft File Skripsi', 'desc' => 'Naskah proposal / skripsi lengkap (Cover hingga Bab Pembahasan/Lampiran).'],
                                    ]
                                ]
                            ];
                        @endphp

                        <!-- Category Filter Tabs -->
                        <div class="border-b border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-1 sm:gap-2 overflow-x-auto no-scrollbar">
                                <button type="button"
                                        @click="activeTab = 'all'"
                                        class="px-4 py-2.5 border-b-2 text-xs font-bold transition-colors whitespace-nowrap inline-flex items-center gap-2 cursor-pointer shrink-0"
                                        :class="activeTab === 'all' ? 'border-orange-600 text-orange-600 dark:text-orange-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                    <span>Semua Kategori</span>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold"
                                          :class="totalFilesFilled === totalFilesCount ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
                                          x-text="totalFilesFilled + '/' + totalFilesCount"></span>
                                </button>

                                @foreach($categories as $catKey => $cat)
                                    <button type="button"
                                            @click="activeTab = '{{ $catKey }}'"
                                            class="px-4 py-2.5 border-b-2 text-xs font-bold transition-colors whitespace-nowrap inline-flex items-center gap-2 cursor-pointer shrink-0"
                                            :class="activeTab === '{{ $catKey }}' ? 'border-orange-600 text-orange-600 dark:text-orange-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                        <span>{{ $cat['title'] }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold"
                                              :class="countFilled({{ json_encode($cat['keys']) }}) === {{ count($cat['keys']) }} ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
                                              x-text="countFilled({{ json_encode($cat['keys']) }}) + '/{{ count($cat['keys']) }}'"></span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Grouped Input Fields -->
                        <div class="space-y-6">
                            @foreach($categories as $catKey => $cat)
                                <div x-show="activeTab === 'all' || activeTab === '{{ $catKey }}'" x-cloak class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">{{ $cat['title'] }}</h5>
                                        <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-lg"
                                              :class="countFilled({{ json_encode($cat['keys']) }}) === {{ count($cat['keys']) }} ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                                              x-text="countFilled({{ json_encode($cat['keys']) }}) + ' dari {{ count($cat['keys']) }} Terisi'"></span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($cat['fields'] as $field => $info)
                                            <div class="p-4 bg-slate-50/60 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 rounded-2xl transition-colors hover:border-slate-300 dark:hover:border-slate-600 space-y-2">
                                                <div class="flex items-center justify-between gap-2">
                                                    <label for="{{ $field }}" class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                                                        {{ $info['label'] }} <span class="text-rose-500">*</span>
                                                    </label>
                                                    <button type="button"
                                                            @click="pasteSingle('{{ $field }}')"
                                                            class="px-2.5 py-1 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-lg text-[11px] font-bold transition-colors cursor-pointer shrink-0"
                                                            title="Tempel tautan dari clipboard">
                                                        Tempel
                                                    </button>
                                                </div>

                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight">{{ $info['desc'] }}</p>

                                                <input type="url"
                                                       name="{{ $field }}"
                                                       id="{{ $field }}"
                                                       x-model="files['{{ $field }}']"
                                                       placeholder="https://drive.google.com/..."
                                                       required
                                                       class="block w-full h-10 px-3 text-xs text-slate-800 dark:text-slate-100 rounded-xl border border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900">

                                                <div class="flex items-center justify-between pt-0.5">
                                                    <template x-if="isDrive(files['{{ $field }}'])">
                                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-400">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            <span>Google Drive Valid</span>
                                                        </span>
                                                    </template>
                                                    <template x-if="files['{{ $field }}'] && !isDrive(files['{{ $field }}'])">
                                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 dark:text-amber-400">
                                                            <span>Link Terisi</span>
                                                        </span>
                                                    </template>
                                                    <template x-if="!files['{{ $field }}']">
                                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 italic">Belum diisi</span>
                                                    </template>

                                                    <template x-if="files['{{ $field }}']">
                                                        <a :href="files['{{ $field }}']" target="_blank" rel="noopener noreferrer" class="text-[11px] font-bold text-blue-700 dark:text-blue-400 hover:underline inline-flex items-center gap-0.5">
                                                            <span>Buka Link</span>
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                        </a>
                                                    </template>
                                                </div>
                                                @error($field) <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit Footer -->
                    <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            Pastikan seluruh tautan Google Drive disetel ke mode <strong class="text-slate-700 dark:text-slate-200">"Anyone with the link can view"</strong>. Setelah dikirim, Anda langsung dapat mencetak PDF Formulir Pendaftaran.
                        </div>
                        @if($activeWave)
                            <button type="submit" class="w-full sm:w-auto h-10 px-6 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-colors shadow-sm cursor-pointer shrink-0">
                                Kirim Pengajuan Seminar
                            </button>
                        @else
                            <button type="button" disabled class="w-full sm:w-auto h-10 px-6 bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider rounded-xl cursor-not-allowed flex items-center justify-center gap-2 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>Pendaftaran Belum Dibuka</span>
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>

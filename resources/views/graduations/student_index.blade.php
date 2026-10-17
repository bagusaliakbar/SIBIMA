<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Yudisium & SKL', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-8 pb-12">

        <!-- Top Header Hero -->
        <div class="bg-gradient-to-r from-orange-600 via-amber-600 to-orange-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-orange-600/15 relative overflow-hidden">
            <!-- Background Glows & Patterns -->
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-amber-400/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-3xl">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/20 backdrop-blur-md text-white border border-white/25">
                            🎓 Tahap Final 6 / 6 • Kelulusan Akademik
                        </span>
                        @if($graduation && $graduation->status === 'approved')
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-white shadow-xs">
                                ✓ Lulus & SKL Diterbitkan
                            </span>
                        @elseif($isEligible)
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-900 shadow-xs">
                                Bebas Tanggungan Aktif
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/20 text-white">
                                Prasyarat Belum Lengkap
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Yudisium, Bebas Tanggungan & SKL Digital
                    </h1>
                    <p class="text-xs sm:text-sm text-orange-50 leading-relaxed font-medium">
                        Pemberkasan akhir penyelesaian skripsi, validasi bebas tanggungan fakultas, dan penerbitan Surat Keterangan Lulus (SKL) resmi dengan QR Code verifikasi.
                    </p>
                </div>

                @if($graduation && $graduation->status === 'approved')
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('graduations.download-skl', $graduation) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-orange-700 hover:bg-orange-50 font-black text-xs uppercase tracking-wider shadow-lg transition-all hover:scale-105 active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Unduh SKL Digital</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @if(!$isEligible)
            @php
                $yudisiumCriteria = [
                    [
                        'number' => 1,
                        'tag' => 'Skripsi',
                        'title' => 'Pengesahan Judul & Penulisan Skripsi',
                        'desc' => $thesis ? ($thesis->final_title ?: $thesis->title) : 'Belum memiliki usulan judul skripsi yang terdaftar di SIBIMA.',
                        'is_met' => (bool) $thesis,
                        'status_text' => $thesis ? 'Terpenuhi' : 'Belum Ada Judul',
                        'badge_type' => $thesis ? 'emerald' : 'rose',
                        'extra_info' => $thesis ? 'Topik: ' . ($thesis->topic ?: 'Umum') : null,
                        'action_hint' => $thesis ? null : 'Silakan ajukan judul skripsi terlebih dahulu melalui menu Pengajuan Judul.',
                        'action_url' => $thesis ? null : route('theses.create'),
                        'action_label' => 'Ajukan Judul',
                    ],
                    [
                        'number' => 2,
                        'tag' => 'Sidang',
                        'title' => 'Pelaksanaan Ujian Sidang Akhir Skripsi',
                        'desc' => $hasDefense 
                            ? 'Telah mendaftar dan melaksanakan ujian sidang akhir skripsi.' 
                            : 'Belum mendaftar atau belum melaksanakan ujian sidang akhir skripsi.',
                        'is_met' => (bool) $hasDefense,
                        'status_text' => $hasDefense ? 'Terpenuhi' : 'Belum Sidang',
                        'badge_type' => $hasDefense ? 'emerald' : 'amber',
                        'extra_info' => $hasDefense ? 'Status: Sidang telah terdaftar' : 'Wajib menuntaskan ujian sidang',
                        'action_hint' => $hasDefense ? null : 'Daftarkan diri Anda untuk ujian sidang skripsi pada periode gelombang aktif.',
                        'action_url' => $hasDefense ? null : route('thesis-defense-applications.index'),
                        'action_label' => 'Daftar Sidang',
                    ],
                    [
                        'number' => 3,
                        'tag' => 'Revisi',
                        'title' => 'Persetujuan (ACC) Revisi Naskah oleh Seluruh Penguji',
                        'desc' => $isDefensePassed 
                            ? 'Seluruh catatan perbaikan revisi sidang telah disetujui dan di-ACC oleh semua dosen penguji.' 
                            : 'Masih terdapat catatan revisi yang belum disetujui (ACC) oleh dosen penguji atau revisi belum diajukan.',
                        'is_met' => (bool) $isDefensePassed,
                        'status_text' => $isDefensePassed ? 'Terpenuhi' : 'Menunggu ACC Revisi',
                        'badge_type' => $isDefensePassed ? 'emerald' : 'amber',
                        'extra_info' => $isDefensePassed ? 'Semua penguji telah approve' : 'Perlu ACC seluruh dosen penguji',
                        'action_hint' => $isDefensePassed ? null : 'Periksa lembar revisi, konsultasikan perbaikan kepada para dosen penguji, dan pastikan seluruh approval tuntas di SIBIMA.',
                        'action_url' => route('student-defense-revisions.index'),
                        'action_label' => 'Cek Lembar Revisi',
                    ],
                ];

                $yMetCount = count(array_filter($yudisiumCriteria, fn($c) => $c['is_met']));
                $yTotalCriteria = count($yudisiumCriteria);
                $yPercentMet = round(($yMetCount / $yTotalCriteria) * 100);
            @endphp

            <!-- Pre-requisite Checklist Master Card for Yudisium -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 relative overflow-hidden">
                <!-- Subtle Glow Decoration -->
                <div class="absolute -top-20 -right-20 w-56 h-56 bg-amber-500/10 dark:bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Header & Overall Progress -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100 dark:border-slate-700/80 relative z-10">
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">
                                📋 Kesiapan Akses Portal Yudisium & SKL
                            </h3>
                            <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider {{ $yMetCount === $yTotalCriteria ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80' }}">
                                {{ $yMetCount }} dari {{ $yTotalCriteria }} Prasyarat Terpenuhi
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed max-w-2xl">
                            Portal pemberkasan bebas tanggungan dan permohonan SKL akan <strong class="text-slate-700 dark:text-slate-200">terbuka secara otomatis</strong> setelah ujian sidang selesai dan seluruh revisi disetujui para dosen penguji.
                        </p>
                    </div>

                    <!-- Progress Bar & Percentage Pill -->
                    <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-900/60 p-3.5 sm:p-4 rounded-2xl border border-slate-100 dark:border-slate-700/60 shrink-0">
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1">Status Kesiapan</p>
                            <p class="text-xl sm:text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $yPercentMet }}%</p>
                        </div>
                        <div class="w-16 sm:w-20 bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full transition-all duration-700 {{ $yMetCount === $yTotalCriteria ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $yPercentMet }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Criteria Subtitle -->
                <div class="flex items-center justify-between my-5 pt-1">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        Checklist Prasyarat Kelulusan
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                        Lengkapi seluruh poin di bawah untuk membuka form
                    </span>
                </div>

                <!-- Detailed Interactive Criteria List -->
                <div class="space-y-3 mb-6 relative z-10">
                    @foreach($yudisiumCriteria as $c)
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
                                        @if(!empty($c['tag']))
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $c['is_met'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">
                                                {{ $c['tag'] }}
                                            </span>
                                        @endif
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">
                                            {{ $c['title'] }}
                                        </h4>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $c['is_met'] ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300' }}">
                                            {{ $c['status_text'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                        {{ $c['desc'] }}
                                    </p>
                                    @if(!empty($c['extra_info']))
                                        <div class="mt-1.5 flex items-center gap-2 text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 shadow-2xs">
                                                📌 {{ $c['extra_info'] }}
                                            </span>
                                        </div>
                                    @endif
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

                <!-- Contextual Action Bar -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                                Formulir Yudisium & Bebas Tanggungan Terkunci
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Masih ada <strong class="text-amber-600 dark:text-amber-400">{{ $yTotalCriteria - $yMetCount }} prasyarat</strong> yang belum terpenuhi. Selesaikan revisi sidang untuk membuka formulir.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 w-full sm:w-auto shrink-0">
                        <a href="{{ route('student-defense-revisions.index') }}" class="w-full sm:w-auto px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm hover:scale-105 active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                            <span>Buka Revisi Sidang</span>
                            <span>➔</span>
                        </a>
                    </div>
                </div>
            </div>

        @else
            <!-- Visual Academic Progression Timeline (4 Steps) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-7 border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            Alur Proses Bebas Tanggungan & Yudisium
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 font-bold mt-0.5">
                            4 Tahapan Menuju Penerbitan SKL Resmi
                        </p>
                    </div>
                    <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800/50">
                        @if($graduation && $graduation->status === 'approved')
                            Tahap 4/4 Selesai (100%)
                        @elseif($graduation && $graduation->isClearanceComplete())
                            Tahap 3/4 (Verifikasi)
                        @elseif($graduation && $graduation->final_thesis_file)
                            Tahap 2/4 (Penyerahan Fisik)
                        @else
                            Tahap 1/4 (Unggah Berkas)
                        @endif
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Step 1 -->
                    @php $step1Done = $graduation && !empty($graduation->final_thesis_file); @endphp
                    <div class="p-4 rounded-2xl border transition-all {{ $step1Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50/80 dark:bg-slate-900/40 border-slate-200/70 dark:border-slate-700/60' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black {{ $step1Done ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">
                                @if($step1Done) ✓ @else 1 @endif
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $step1Done ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">
                                {{ $step1Done ? 'Selesai' : 'Wajib' }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100">1. Unggah Tautan Digital</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Tautan Drive skripsi final, artikel jurnal & turnitin.</p>
                    </div>

                    <!-- Step 2 -->
                    @php $step2Done = $graduation && $graduation->hardcover_collected && $graduation->cd_or_repository_collected; @endphp
                    <div class="p-4 rounded-2xl border transition-all {{ $step2Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50/80 dark:bg-slate-900/40 border-slate-200/70 dark:border-slate-700/60' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black {{ $step2Done ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">
                                @if($step2Done) ✓ @else 2 @endif
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $step2Done ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">
                                {{ $step2Done ? 'Selesai' : 'Fisik' }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100">2. Berkas Fisik Hardcover & CD</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Penyerahan buku hardcover & CD ke loket BAAK.</p>
                    </div>

                    <!-- Step 3 -->
                    @php $step3Done = $graduation && $graduation->isClearanceComplete(); @endphp
                    <div class="p-4 rounded-2xl border transition-all {{ $step3Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50/80 dark:bg-slate-900/40 border-slate-200/70 dark:border-slate-700/60' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black {{ $step3Done ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">
                                @if($step3Done) ✓ @else 3 @endif
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $step3Done ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">
                                {{ $step3Done ? 'Selesai' : 'Validasi' }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100">3. Bebas Perpustakaan & Lab</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Pemeriksaan pinjaman buku & peralatan lab.</p>
                    </div>

                    <!-- Step 4 -->
                    @php $step4Done = $graduation && $graduation->status === 'approved'; @endphp
                    <div class="p-4 rounded-2xl border transition-all {{ $step4Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 ring-2 ring-emerald-500/30' : 'bg-slate-50/80 dark:bg-slate-900/40 border-slate-200/70 dark:border-slate-700/60' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black {{ $step4Done ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">
                                @if($step4Done) 🎓 @else 4 @endif
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $step4Done ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">
                                {{ $step4Done ? 'Resmi' : 'Final' }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100">4. Penerbitan SKL Digital</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Surat Keterangan Lulus resmi & QR Code validasi.</p>
                    </div>
                </div>
            </div>

            <!-- Dynamic State Notifications / Certificate Hero -->
            @if($graduation && $graduation->status === 'approved')
                <!-- Official Graduation Credential Showcase -->
                <div class="bg-gradient-to-br from-slate-900 via-slate-850 to-slate-900 rounded-3xl p-6 sm:p-9 text-white border border-emerald-500/40 shadow-2xl relative overflow-hidden">
                    <!-- Glow & Confetti Effects -->
                    <div class="absolute -top-24 -right-24 w-72 h-72 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div class="space-y-4 max-w-2xl">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                <span>🏅 SKL Resmi Telah Terbit & Sah</span>
                            </div>
                            
                            <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                                Selamat, {{ Auth::user()->name }}! Anda Resmi Lulus 🎓
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                                Seluruh pemberkasan akhir dan bebas tanggungan Anda telah diverifikasi oleh Program Studi. Surat Keterangan Lulus (SKL) resmi dengan pengesahan digital dan QR Code verifikasi telah terbit.
                            </p>

                            <!-- Mini Certificate Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                                <div class="p-3 bg-white/5 rounded-2xl border border-white/10">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Nomor SKL</span>
                                    <span class="text-xs font-mono font-bold text-emerald-400 truncate block" title="{{ $graduation->skl_number }}">{{ $graduation->skl_number }}</span>
                                </div>
                                <div class="p-3 bg-white/5 rounded-2xl border border-white/10">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Tanggal Yudisium</span>
                                    <span class="text-xs font-bold text-white block">{{ $graduation->formatted_graduation_date ?? now()->locale('id')->translatedFormat('d F Y') }}</span>
                                </div>
                                <div class="p-3 bg-white/5 rounded-2xl border border-white/10">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Predikat</span>
                                    <span class="text-xs font-bold text-amber-300 block truncate">{{ $graduation->predicate ?? 'Memuaskan' }}</span>
                                </div>
                                <div class="p-3 bg-white/5 rounded-2xl border border-white/10">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Indeks Prestasi</span>
                                    <span class="text-xs font-bold text-white block">{{ $graduation->gpa ? number_format($graduation->gpa, 2) : '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Certificate Action Buttons -->
                        <div class="flex flex-col sm:flex-row lg:flex-col items-stretch sm:items-center lg:items-end gap-3 shrink-0">
                            <a href="{{ route('graduations.download-skl', $graduation) }}" target="_blank" class="px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-2.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Unduh SKL Digital (PDF)</span>
                            </a>
                            
                            @if($graduation->verification_token)
                                <a href="{{ route('skl.verify', $graduation->verification_token) }}" target="_blank" class="px-6 py-3 bg-white/10 hover:bg-white/15 text-slate-200 hover:text-white rounded-2xl font-bold text-xs uppercase tracking-wider border border-white/15 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <span>Verifikasi QR Code</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            @elseif($graduation && $graduation->status === 'rejected')
                <!-- Rejected Notification -->
                <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 rounded-3xl p-6 sm:p-7 text-rose-800 dark:text-rose-200 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-rose-600/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-rose-950 dark:text-rose-100">
                                Perbaikan Berkas Bebas Tanggungan Diperlukan
                            </h4>
                            <p class="text-xs text-rose-700 dark:text-rose-300 mt-0.5">
                                Catatan dari BAAK / Program Studi: <strong class="text-rose-900 dark:text-rose-100">"{{ $graduation->rejection_reason }}"</strong>
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-rose-600 dark:text-rose-400 leading-relaxed pl-13">
                        Silakan periksa kembali tautan berkas yang Anda berikan atau hubungi loket BAAK jika ada berkas fisik yang belum lengkap, lalu kirimkan kembali formulir di bawah ini.
                    </p>
                </div>

            @elseif($graduation && $graduation->final_thesis_file)
                <!-- Pending Review Notification -->
                <div class="bg-blue-50/90 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 rounded-3xl p-6 sm:p-7 text-blue-900 dark:text-blue-200 space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-600/20">
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-blue-950 dark:text-blue-100">
                                Berkas Sedang Dalam Proses Verifikasi BAAK & Program Studi
                            </h4>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-0.5">
                                Tautan dokumen akhir telah tersimpan. Petugas sedang memvalidasi berkas digital serta mencocokkan bebas tanggungan fisik Anda.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 2-Columns: Clearance Checklist Tracker & Upload Form -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <!-- Left Column: Clearance Checklist Progress (1/3) -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">
                                    Bebas Tanggungan
                                </h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Status verifikasi fisik & unit</p>
                            </div>
                            <span class="text-xs font-black font-mono px-2.5 py-1 rounded-lg {{ $graduation && $graduation->isClearanceComplete() ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                {{ $graduation ? $graduation->clearance_progress_percentage : 0 }}%
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full bg-emerald-500 transition-all duration-500 rounded-full" style="width: {{ $graduation ? $graduation->clearance_progress_percentage : 0 }}%"></div>
                        </div>

                        <div class="space-y-3 pt-1">
                            <!-- 1. Hardcover -->
                            <div class="p-3.5 rounded-2xl border transition-all flex items-start gap-3 {{ $graduation && $graduation->hardcover_collected ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400' }}">
                                <div class="mt-0.5 w-6 h-6 rounded-xl flex items-center justify-center shrink-0 {{ $graduation && $graduation->hardcover_collected ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }}">
                                    @if($graduation && $graduation->hardcover_collected)
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <span class="text-[11px] font-black">1</span>
                                    @endif
                                </div>
                                <div class="text-xs space-y-0.5 flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="font-bold text-slate-800 dark:text-slate-200 truncate">Hardcover Skripsi</p>
                                        <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded {{ $graduation && $graduation->hardcover_collected ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                            {{ $graduation && $graduation->hardcover_collected ? 'Valid' : 'Pending' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] {{ $graduation && $graduation->hardcover_collected ? 'text-emerald-700 dark:text-emerald-400 font-medium' : 'text-slate-400' }}">
                                        {{ $graduation && $graduation->hardcover_collected ? 'Sudah diserahkan ke BAAK' : 'Diserahkan langsung ke loket BAAK' }}
                                    </p>
                                    @if($graduation && $graduation->hardcover_collected_at)
                                        <p class="text-[10px] text-slate-400 italic">Divalidasi: {{ $graduation->hardcover_collected_at->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- 2. Bebas Perpustakaan -->
                            <div class="p-3.5 rounded-2xl border transition-all flex items-start gap-3 {{ $graduation && $graduation->library_clearance ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400' }}">
                                <div class="mt-0.5 w-6 h-6 rounded-xl flex items-center justify-center shrink-0 {{ $graduation && $graduation->library_clearance ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }}">
                                    @if($graduation && $graduation->library_clearance)
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <span class="text-[11px] font-black">2</span>
                                    @endif
                                </div>
                                <div class="text-xs space-y-0.5 flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="font-bold text-slate-800 dark:text-slate-200 truncate">Bebas Perpustakaan</p>
                                        <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded {{ $graduation && $graduation->library_clearance ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                            {{ $graduation && $graduation->library_clearance ? 'Bebas' : 'Pending' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] {{ $graduation && $graduation->library_clearance ? 'text-emerald-700 dark:text-emerald-400 font-medium' : 'text-slate-400' }}">
                                        {{ $graduation && $graduation->library_clearance ? 'Bebas pinjaman & sumbangan buku' : 'Bebas pinjaman buku & sumbangan' }}
                                    </p>
                                    @if($graduation && $graduation->library_clearance_at)
                                        <p class="text-[10px] text-slate-400 italic">Divalidasi: {{ $graduation->library_clearance_at->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- 3. Bebas Lab Komputer -->
                            <div class="p-3.5 rounded-2xl border transition-all flex items-start gap-3 {{ $graduation && $graduation->lab_clearance ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400' }}">
                                <div class="mt-0.5 w-6 h-6 rounded-xl flex items-center justify-center shrink-0 {{ $graduation && $graduation->lab_clearance ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }}">
                                    @if($graduation && $graduation->lab_clearance)
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <span class="text-[11px] font-black">3</span>
                                    @endif
                                </div>
                                <div class="text-xs space-y-0.5 flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="font-bold text-slate-800 dark:text-slate-200 truncate">Bebas Laboratorium</p>
                                        <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded {{ $graduation && $graduation->lab_clearance ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                            {{ $graduation && $graduation->lab_clearance ? 'Bebas' : 'Pending' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] {{ $graduation && $graduation->lab_clearance ? 'text-emerald-700 dark:text-emerald-400 font-medium' : 'text-slate-400' }}">
                                        {{ $graduation && $graduation->lab_clearance ? 'Bebas pinjaman alat praktikum' : 'Bebas tanggungan alat praktikum' }}
                                    </p>
                                    @if($graduation && $graduation->lab_clearance_at)
                                        <p class="text-[10px] text-slate-400 italic">Divalidasi: {{ $graduation->lab_clearance_at->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- 4. CD / Repositori -->
                            <div class="p-3.5 rounded-2xl border transition-all flex items-start gap-3 {{ $graduation && $graduation->cd_or_repository_collected ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400' }}">
                                <div class="mt-0.5 w-6 h-6 rounded-xl flex items-center justify-center shrink-0 {{ $graduation && $graduation->cd_or_repository_collected ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }}">
                                    @if($graduation && $graduation->cd_or_repository_collected)
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <span class="text-[11px] font-black">4</span>
                                    @endif
                                </div>
                                <div class="text-xs space-y-0.5 flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="font-bold text-slate-800 dark:text-slate-200 truncate">CD Program & Repositori</p>
                                        <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded {{ $graduation && $graduation->cd_or_repository_collected ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                            {{ $graduation && $graduation->cd_or_repository_collected ? 'Valid' : 'Pending' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] {{ $graduation && $graduation->cd_or_repository_collected ? 'text-emerald-700 dark:text-emerald-400 font-medium' : 'text-slate-400' }}">
                                        {{ $graduation && $graduation->cd_or_repository_collected ? 'CD source code & softcopy diterima' : 'CD source code & softcopy skripsi' }}
                                    </p>
                                    @if($graduation && $graduation->cd_or_repository_collected_at)
                                        <p class="text-[10px] text-slate-400 italic">Divalidasi: {{ $graduation->cd_or_repository_collected_at->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-800/50 text-[11px] text-amber-900 dark:text-amber-300 leading-relaxed">
                            💡 <strong>Catatan Penting:</strong> Checklist bebas tanggungan fisik diverifikasi secara manual oleh Petugas BAAK / Perpustakaan saat penyerahan berkas fisik di kampus.
                        </div>
                    </div>
                </div>

                <!-- Right Column: Submission Form (Google Drive Links) (2/3) -->
                <div class="lg:col-span-2" x-data="{
                    batchFolderUrl: '',
                    files: {
                        final_thesis_file: '{{ old('final_thesis_file', $graduation->final_thesis_file ?? '') }}',
                        journal_article_file: '{{ old('journal_article_file', $graduation->journal_article_file ?? '') }}',
                        plagiarism_file: '{{ old('plagiarism_file', $graduation->plagiarism_file ?? '') }}',
                        publication_link: '{{ old('publication_link', $graduation->publication_link ?? '') }}'
                    },
                    applyBatchFolderUrl() {
                        if (!this.batchFolderUrl || !this.batchFolderUrl.trim().startsWith('http')) {
                            alert('Silakan masukkan link Google Drive yang valid (dimulai dengan https://)');
                            return;
                        }
                        const url = this.batchFolderUrl.trim();
                        this.files.final_thesis_file = url;
                        this.files.journal_article_file = url;
                        if (!this.files.plagiarism_file) {
                            this.files.plagiarism_file = url;
                        }
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
                    }
                }">
                    <form action="{{ route('student.graduation.store') }}" method="POST" class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-6">
                        @csrf

                        <!-- Form Header -->
                        <div class="border-b border-slate-100 dark:border-slate-700/60 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm sm:text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Tautan Berkas Akhir Skripsi</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Unggah dokumen akhir melalui tautan Google Drive publik.</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 self-start sm:self-center">
                                Google Drive
                            </span>
                        </div>

                        <!-- Info Banner: Google Drive Share Permission -->
                        <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-800/40 text-blue-900 dark:text-blue-300 text-xs flex items-start gap-3">
                            <div class="p-1.5 rounded-lg bg-blue-500 text-white shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="space-y-1">
                                <p class="font-bold">Penting: Pengaturan Akses Berkas Google Drive</p>
                                <p class="text-[11px] leading-relaxed text-blue-800 dark:text-blue-300/90">
                                    Pastikan seluruh tautan Google Drive telah disetel ke mode <strong class="text-blue-900 dark:text-white">"Siapa saja yang memiliki link dapat melihat" (Anyone with the link can view)</strong> agar tim Program Studi / BAAK dapat memeriksa dokumen tanpa kendala permohonan izin akses.
                                </p>
                            </div>
                        </div>

                        <!-- Smart Batch Folder Filler Banner -->
                        <div class="p-4 bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 dark:from-slate-900 dark:via-slate-800/80 dark:to-slate-900 border border-orange-200/80 dark:border-slate-700 rounded-2xl shadow-2xs">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="p-2 bg-orange-600 text-white rounded-xl shadow-xs shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">💡 Punya 1 Folder Google Drive untuk Semua Berkas?</h5>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Tempelkan link foldernya di sini untuk mengisi kolom wajib secara serentak.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <input type="url" 
                                           x-model="batchFolderUrl" 
                                           placeholder="https://drive.google.com/drive/folders/..." 
                                           class="py-1.5 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 w-full sm:w-60 shadow-2xs">
                                    <button type="button" 
                                            @click="applyBatchFolderUrl()" 
                                            class="px-3.5 py-1.5 bg-orange-600 hover:bg-orange-500 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-xs shrink-0 active:scale-95 cursor-pointer">
                                        Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 1. Naskah Skripsi Final (Wajib) -->
                        <div class="p-4 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <label for="final_thesis_file" class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                                    1. Naskah Skripsi Lengkap Final (Disahkan) <span class="text-rose-500">*</span>
                                </label>
                                <button type="button" 
                                        @click="pasteSingle('final_thesis_file')" 
                                        class="px-2 py-0.5 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 rounded-md text-[10px] font-bold transition-all shadow-2xs cursor-pointer active:scale-95 shrink-0">
                                    📋 Tempel
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 leading-tight">
                                Naskah lengkap Cover s.d. Lampiran yang telah memuat lembar pengesahan tanda tangan asli / TTE.
                            </p>
                            
                            <div class="relative">
                                <input type="url" 
                                       name="final_thesis_file" 
                                       id="final_thesis_file"
                                       x-model="files.final_thesis_file" 
                                       placeholder="https://drive.google.com/file/d/... atau link folder" 
                                       required 
                                       class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-xl border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-800 shadow-2xs py-2 px-3 transition-all font-mono">
                            </div>

                            <!-- Live Status Indicator -->
                            <div class="flex items-center justify-between pt-1">
                                <template x-if="isDrive(files.final_thesis_file)">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Google Drive Valid</span>
                                    </span>
                                </template>
                                <template x-if="files.final_thesis_file && !isDrive(files.final_thesis_file)">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                        <span>🔗 Link Terisi</span>
                                    </span>
                                </template>
                                <template x-if="!files.final_thesis_file">
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Wajib diisi</span>
                                </template>

                                <template x-if="files.final_thesis_file">
                                    <a :href="files.final_thesis_file" target="_blank" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-0.5">
                                        <span>Buka Link ↗</span>
                                    </a>
                                </template>
                            </div>
                            
                            @error('final_thesis_file')
                                <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 2. Naskah Artikel Jurnal Ilmiah (Wajib) -->
                        <div class="p-4 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <label for="journal_article_file" class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                                    2. Artikel Jurnal Ilmiah (Format Siap Publikasi) <span class="text-rose-500">*</span>
                                </label>
                                <button type="button" 
                                        @click="pasteSingle('journal_article_file')" 
                                        class="px-2 py-0.5 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 rounded-md text-[10px] font-bold transition-all shadow-2xs cursor-pointer active:scale-95 shrink-0">
                                    📋 Tempel
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 leading-tight">
                                Ringkasan naskah skripsi dalam format artikel jurnal ilmiah sesuai pedoman publikasi Program Studi.
                            </p>
                            
                            <div class="relative">
                                <input type="url" 
                                       name="journal_article_file" 
                                       id="journal_article_file"
                                       x-model="files.journal_article_file" 
                                       placeholder="https://drive.google.com/file/d/... atau link folder" 
                                       required 
                                       class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-xl border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-800 shadow-2xs py-2 px-3 transition-all font-mono">
                            </div>

                            <!-- Live Status Indicator -->
                            <div class="flex items-center justify-between pt-1">
                                <template x-if="isDrive(files.journal_article_file)">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Google Drive Valid</span>
                                    </span>
                                </template>
                                <template x-if="files.journal_article_file && !isDrive(files.journal_article_file)">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                        <span>🔗 Link Terisi</span>
                                    </span>
                                </template>
                                <template x-if="!files.journal_article_file">
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Wajib diisi</span>
                                </template>

                                <template x-if="files.journal_article_file">
                                    <a :href="files.journal_article_file" target="_blank" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-0.5">
                                        <span>Buka Link ↗</span>
                                    </a>
                                </template>
                            </div>
                            
                            @error('journal_article_file')
                                <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 3. Bukti Uji Plagiasi / Turnitin (Opsional) -->
                        <div class="p-4 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <label for="plagiarism_file" class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                                    3. Bukti Uji Plagiasi / Turnitin <span class="text-[10px] text-slate-400 font-normal uppercase tracking-wider">(Opsional)</span>
                                </label>
                                <button type="button" 
                                        @click="pasteSingle('plagiarism_file')" 
                                        class="px-2 py-0.5 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 rounded-md text-[10px] font-bold transition-all shadow-2xs cursor-pointer active:scale-95 shrink-0">
                                    📋 Tempel
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 leading-tight">
                                Sertifikat hasil cek kemiripan (similarity Turnitin) yang telah disahkan bila dipersyaratkan oleh prodi.
                            </p>
                            
                            <div class="relative">
                                <input type="url" 
                                       name="plagiarism_file" 
                                       id="plagiarism_file"
                                       x-model="files.plagiarism_file" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-xl border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-800 shadow-2xs py-2 px-3 transition-all font-mono">
                            </div>

                            <!-- Live Status Indicator -->
                            <div class="flex items-center justify-between pt-1">
                                <template x-if="isDrive(files.plagiarism_file)">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Google Drive Valid</span>
                                    </span>
                                </template>
                                <template x-if="files.plagiarism_file && !isDrive(files.plagiarism_file)">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                        <span>🔗 Link Terisi</span>
                                    </span>
                                </template>
                                <template x-if="!files.plagiarism_file">
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Opsional</span>
                                </template>

                                <template x-if="files.plagiarism_file">
                                    <a :href="files.plagiarism_file" target="_blank" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-0.5">
                                        <span>Buka Link ↗</span>
                                    </a>
                                </template>
                            </div>
                            
                            @error('plagiarism_file')
                                <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 4. Link Publikasi OJS / Repositori Online -->
                        <div class="space-y-1.5">
                            <label for="publication_link" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                4. Tautan Publikasi Jurnal Online / OJS <span class="text-[10px] text-slate-400 font-normal uppercase tracking-wider">(Opsional)</span>
                            </label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Tautan URL publikasi jika artikel ilmiah Anda sudah terbit di jurnal elektronik (OJS) atau repositori kampus.
                            </p>
                            <input type="url" 
                                   name="publication_link" 
                                   id="publication_link"
                                   x-model="files.publication_link" 
                                   placeholder="https://ejournal.unsub.ac.id/index.php/..." 
                                   class="w-full text-xs font-mono rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 shadow-2xs py-2 px-3">
                            @error('publication_link')
                                <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 5. Catatan Tambahan Mahasiswa -->
                        <div class="space-y-1.5">
                            <label for="student_notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                5. Catatan Tambahan untuk Petugas BAAK / Prodi <span class="text-[10px] text-slate-400 font-normal uppercase tracking-wider">(Opsional)</span>
                            </label>
                            <textarea name="student_notes" 
                                      id="student_notes"
                                      rows="3" 
                                      placeholder="Tuliskan keterangan bila terdapat berkas fisik atau persyaratan khusus yang sudah diserahkan di BAAK / TU..." 
                                      class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 shadow-2xs p-3 leading-relaxed">{{ old('student_notes', $graduation->student_notes ?? '') }}</textarea>
                            @error('student_notes')
                                <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 dark:border-slate-700/60">
                            <span class="text-[11px] text-slate-400">
                                Pastikan seluruh link dapat diakses publik oleh petugas BAAK.
                            </span>
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-black text-xs uppercase tracking-wider shadow-md shadow-orange-600/25 transition-all hover:scale-105 active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>Simpan & Ajukan Verifikasi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Yudisium & SKL', 'route' => null]
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
                        <h3 class="text-sm font-bold text-blue-800 dark:text-blue-200 uppercase tracking-widest mb-3">Penting: Berkas Fisik & Bebas Tanggungan (Penyerahan ke BAAK / Perpustakaan)</h3>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mb-4 font-medium">Selain mengunggah berkas digital di bawah ini, Anda wajib menyelesaikan penyerahan berkas fisik dan validasi bebas tanggungan berikut langsung ke kantor BAAK / loket terkait:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Buku Hardcover Skripsi:</strong> Naskah skripsi lengkap dijilid hardcover (sesuai warna prodi) dan telah memuat lembar pengesahan tanda tangan asli / TTE, diserahkan ke BAAK.</span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Surat Bebas Perpustakaan:</strong> Bukti bebas pinjaman buku perpustakaan dan penyerahan buku sumbangan alumni ke Perpustakaan Universitas.</span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Surat Bebas Laboratorium:</strong> Bebas tanggungan peminjaman alat laboratorium/perangkat keras ke pengelola Lab Komputer.</span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>CD Program & Repositori:</strong> CD berisi source code aplikasi, data, naskah PDF/Word lengkap, dan berkas naskah publikasi ilmiah.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if(!$isEligible)
            @php
                $yudisiumCriteria = [
                    [
                        'number' => 1,
                        'tag' => 'Judul Skripsi',
                        'title' => 'Pengesahan Judul & Penulisan Skripsi',
                        'desc' => $thesis ? ($thesis->final_title ?: $thesis->title) : 'Belum memiliki usulan judul skripsi yang terdaftar di SIBIMA.',
                        'is_met' => (bool) $thesis,
                        'status_text' => $thesis ? 'Terpenuhi' : 'Belum Ada Judul',
                        'badge_type' => $thesis ? 'emerald' : 'rose',
                        'extra_info' => $thesis ? ($thesis->topic ? 'Topik: ' . $thesis->topic : null) : null,
                        'action_hint' => $thesis ? null : 'Silakan ajukan judul skripsi Anda terlebih dahulu melalui menu Pengajuan Judul.',
                        'action_url' => $thesis ? null : route('theses.create'),
                        'action_label' => 'Ajukan Judul',
                    ],
                    [
                        'number' => 2,
                        'tag' => 'Ujian Sidang',
                        'title' => 'Pelaksanaan Ujian Sidang Akhir Skripsi',
                        'desc' => $hasDefense 
                            ? 'Telah mendaftar dan melaksanakan ujian sidang akhir skripsi.' 
                            : 'Belum mendaftar atau belum melaksanakan ujian sidang akhir skripsi.',
                        'is_met' => (bool) $hasDefense,
                        'status_text' => $hasDefense ? 'Terpenuhi' : 'Belum Sidang',
                        'badge_type' => $hasDefense ? 'emerald' : 'amber',
                        'extra_info' => $hasDefense ? 'Status: Sidang telah terdaftar' : 'Wajib menuntaskan ujian sidang skripsi',
                        'action_hint' => $hasDefense ? null : 'Daftarkan diri Anda untuk ujian sidang skripsi pada periode gelombang aktif.',
                        'action_url' => $hasDefense ? null : route('thesis-defense-applications.index'),
                        'action_label' => 'Daftar Sidang',
                    ],
                    [
                        'number' => 3,
                        'tag' => 'Revisi Penguji',
                        'title' => 'Persetujuan (ACC) Revisi Naskah oleh Seluruh Dosen Penguji',
                        'desc' => $isDefensePassed 
                            ? 'Seluruh catatan perbaikan revisi sidang telah disetujui dan di-ACC oleh semua dosen penguji.' 
                            : 'Masih terdapat catatan revisi yang belum disetujui (ACC) oleh dosen penguji atau revisi belum diajukan.',
                        'is_met' => (bool) $isDefensePassed,
                        'status_text' => $isDefensePassed ? 'Terpenuhi' : 'Menunggu ACC Revisi',
                        'badge_type' => $isDefensePassed ? 'emerald' : 'amber',
                        'extra_info' => $isDefensePassed ? 'Semua penguji telah approve' : 'Perlu ACC seluruh dosen penguji',
                        'action_hint' => $isDefensePassed ? null : 'Periksa lembar revisi, konsultasikan perbaikan kepada para dosen penguji, dan pastikan seluruh approval tuntas di SIBIMA.',
                        'action_url' => route('student-defense-revisions.index'),
                        'action_label' => 'Buka Revisi Sidang',
                    ],
                ];

                $metCount = count(array_filter($yudisiumCriteria, fn($c) => $c['is_met']));
                $totalCriteria = count($yudisiumCriteria);
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
                                    📋 Kesiapan Pengajuan Yudisium & SKL
                                </h3>
                                <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider {{ $metCount === $totalCriteria ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80' }}">
                                    {{ $metCount }} dari {{ $totalCriteria }} Prasyarat Terpenuhi
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed max-w-2xl">
                                Formulir pemberkasan bebas tanggungan dan pengajuan SKL akan <strong class="text-slate-700 dark:text-slate-200">terbuka secara otomatis</strong> setelah seluruh checklist prasyarat di bawah ini berstatus terpenuhi.
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

                    <!-- Contextual Action Bar (Replaces dead disabled button) -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                                    Formulir Bebas Tanggungan & SKL Terkunci
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
                            <a href="{{ route('student-defense-revisions.index') }}" class="w-full sm:w-auto px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm hover:scale-105 active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                                <span>Buka Revisi Sidang</span>
                                <span>➔</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($graduation && $graduation->exists && $graduation->final_thesis_file)
            <!-- Status Pengajuan Yudisium & SKL -->
            <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden space-y-6">
                <!-- Status Header -->
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 {{ $graduation->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-900/10' : ($graduation->status === 'rejected' ? 'bg-red-50 dark:bg-red-900/10' : 'bg-blue-50 dark:bg-blue-900/10') }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold {{ $graduation->status === 'approved' ? 'text-emerald-800 dark:text-emerald-400' : ($graduation->status === 'rejected' ? 'text-red-800 dark:text-red-400' : 'text-blue-800 dark:text-blue-400') }}">
                                Status Yudisium & SKL: {{ strtoupper($graduation->status === 'approved' ? 'Lulus & SKL Diterbitkan' : ($graduation->status === 'rejected' ? 'Perlu Perbaikan' : 'Sedang Diverifikasi')) }}
                            </h3>
                            <p class="text-sm {{ $graduation->status === 'approved' ? 'text-emerald-600 dark:text-emerald-500/70' : ($graduation->status === 'rejected' ? 'text-red-600 dark:text-red-500/70' : 'text-blue-600 dark:text-blue-500/70') }} mt-1">
                                Diajukan pada {{ $graduation->created_at ? $graduation->created_at->locale('id')->translatedFormat('d M Y • H:i') : now()->locale('id')->translatedFormat('d M Y • H:i') }}
                            </p>
                        </div>
                        <div class="px-4 py-2 rounded-full font-bold text-xs uppercase tracking-widest border {{ $graduation->status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : ($graduation->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800') }}">
                            {{ $graduation->status }}
                        </div>
                    </div>
                </div>

                @if($graduation->status === 'approved')
                    <!-- Official Graduation Credential Showcase -->
                    <div class="mx-6 p-6 bg-gradient-to-br from-slate-900 via-slate-850 to-slate-900 rounded-2xl text-white border border-emerald-500/30 shadow-lg relative overflow-hidden">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                            <div class="space-y-3">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    🎓 Surat Keterangan Lulus (SKL) Resmi Sah
                                </div>
                                <h4 class="text-xl font-black text-white">Selamat, {{ Auth::user()->name }}! Anda Resmi Lulus</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                                    <div class="p-2.5 bg-white/5 rounded-xl border border-white/10">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-400 block">Nomor SKL</span>
                                        <span class="text-xs font-mono font-bold text-emerald-400 truncate block" title="{{ $graduation->skl_number }}">{{ $graduation->skl_number }}</span>
                                    </div>
                                    <div class="p-2.5 bg-white/5 rounded-xl border border-white/10">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-400 block">Tanggal Yudisium</span>
                                        <span class="text-xs font-bold text-white block">{{ $graduation->formatted_graduation_date ?? now()->locale('id')->translatedFormat('d F Y') }}</span>
                                    </div>
                                    <div class="p-2.5 bg-white/5 rounded-xl border border-white/10">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-400 block">Predikat</span>
                                        <span class="text-xs font-bold text-amber-300 block truncate">{{ $graduation->predicate ?? 'Memuaskan' }}</span>
                                    </div>
                                    <div class="p-2.5 bg-white/5 rounded-xl border border-white/10">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-400 block">IPK</span>
                                        <span class="text-xs font-bold text-white block">{{ $graduation->gpa ? number_format($graduation->gpa, 2) : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row lg:flex-col gap-2.5 shrink-0">
                                <a href="{{ route('graduations.download-skl', $graduation) }}" target="_blank" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh SKL Digital (PDF)</span>
                                </a>
                                @if($graduation->verification_token)
                                    <a href="{{ route('skl.verify', $graduation->verification_token) }}" target="_blank" class="px-5 py-2.5 bg-white/10 hover:bg-white/15 text-slate-200 hover:text-white rounded-xl font-bold text-xs uppercase tracking-wider border border-white/15 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        <span>Verifikasi QR Code</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if($graduation->status === 'rejected' && $graduation->rejection_reason)
                    <div class="mx-6 p-6 rounded-2xl bg-slate-50/50 dark:bg-slate-900/30 border border-rose-200 dark:border-rose-800/60">
                        <h4 class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest mb-1.5">Catatan BAAK / Program Studi:</h4>
                        <p class="text-sm text-slate-700 dark:text-slate-300 italic">"{{ $graduation->rejection_reason }}"</p>
                    </div>
                @endif

                <!-- Status Verifikasi Bebas Tanggungan Fisik -->
                <div class="px-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Status Verifikasi Bebas Tanggungan Fisik</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Validasi berkas fisik dan bebas tanggungan unit oleh petugas BAAK & Perpustakaan.</p>
                        </div>
                        <span class="text-xs font-mono font-bold px-3 py-1 rounded-full {{ $graduation->isClearanceComplete() ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                            Progress: {{ $graduation->clearance_progress_percentage }}%
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- 1. Hardcover -->
                        <div class="p-4 rounded-2xl border {{ $graduation->hardcover_collected ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">1. Hardcover Skripsi</span>
                                <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded {{ $graduation->hardcover_collected ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                    {{ $graduation->hardcover_collected ? 'Valid' : 'Pending' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $graduation->hardcover_collected ? 'Diserahkan ke BAAK' : 'Menunggu penyerahan fisik' }}</p>
                            @if($graduation->hardcover_collected_at)
                                <p class="text-[10px] text-slate-400 italic mt-1">{{ $graduation->hardcover_collected_at->format('d/m/Y') }}</p>
                            @endif
                        </div>

                        <!-- 2. Perpustakaan -->
                        <div class="p-4 rounded-2xl border {{ $graduation->library_clearance ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">2. Bebas Perpustakaan</span>
                                <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded {{ $graduation->library_clearance ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                    {{ $graduation->library_clearance ? 'Bebas' : 'Pending' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $graduation->library_clearance ? 'Bebas pinjaman & sumbangan' : 'Bebas pinjaman & sumbangan buku' }}</p>
                            @if($graduation->library_clearance_at)
                                <p class="text-[10px] text-slate-400 italic mt-1">{{ $graduation->library_clearance_at->format('d/m/Y') }}</p>
                            @endif
                        </div>

                        <!-- 3. Lab Komputer -->
                        <div class="p-4 rounded-2xl border {{ $graduation->lab_clearance ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">3. Bebas Laboratorium</span>
                                <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded {{ $graduation->lab_clearance ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                    {{ $graduation->lab_clearance ? 'Bebas' : 'Pending' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $graduation->lab_clearance ? 'Bebas pinjaman alat lab' : 'Bebas tanggungan alat praktikum' }}</p>
                            @if($graduation->lab_clearance_at)
                                <p class="text-[10px] text-slate-400 italic mt-1">{{ $graduation->lab_clearance_at->format('d/m/Y') }}</p>
                            @endif
                        </div>

                        <!-- 4. CD / Repositori -->
                        <div class="p-4 rounded-2xl border {{ $graduation->cd_or_repository_collected ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">4. CD & Repositori</span>
                                <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded {{ $graduation->cd_or_repository_collected ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                    {{ $graduation->cd_or_repository_collected ? 'Valid' : 'Pending' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $graduation->cd_or_repository_collected ? 'CD source code diterima' : 'CD source code & softcopy' }}</p>
                            @if($graduation->cd_or_repository_collected_at)
                                <p class="text-[10px] text-slate-400 italic mt-1">{{ $graduation->cd_or_repository_collected_at->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Detail Berkas Digital (Card Grid identical to thesis_defenses) -->
                <div class="p-6 pt-2">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-700/80">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Detail Berkas Tautan Digital</h4>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800/60">
                                    Berkas Tersimpan
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Dokumen naskah skripsi final dan kelengkapan digital yang telah diajukan.</p>
                        </div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-700/70 text-slate-500 dark:text-slate-400 text-xs">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="font-medium text-[11px]">Tersimpan di Google Drive</span>
                        </div>
                    </div>

                    @php
                        $gradFileFields = [
                            'final_thesis_file' => [
                                'label' => 'Naskah Skripsi Lengkap Final',
                                'subtitle' => 'Naskah Lengkap Pengesahan Tanda Tangan / TTE',
                                'category' => 'Skripsi Final',
                                'theme' => ['bg' => 'bg-purple-50 dark:bg-purple-950/40', 'text' => 'text-purple-600 dark:text-purple-400', 'border' => 'border-purple-100 dark:border-purple-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11.5l-4.5 2 4.5 2 4.5-2-4.5-2z" />'
                            ],
                            'journal_article_file' => [
                                'label' => 'Artikel Jurnal Ilmiah',
                                'subtitle' => 'Format Siap Publikasi Program Studi',
                                'category' => 'Publikasi',
                                'theme' => ['bg' => 'bg-indigo-50 dark:bg-indigo-950/40', 'text' => 'text-indigo-600 dark:text-indigo-400', 'border' => 'border-indigo-100 dark:border-indigo-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />'
                            ],
                            'plagiarism_file' => [
                                'label' => 'Bukti Cek Kemiripan / Turnitin',
                                'subtitle' => 'Sertifikat Hasil Uji Plagiasi',
                                'category' => 'Verifikasi',
                                'theme' => ['bg' => 'bg-amber-50 dark:bg-amber-950/40', 'text' => 'text-amber-600 dark:text-amber-400', 'border' => 'border-amber-100 dark:border-amber-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />'
                            ],
                            'publication_link' => [
                                'label' => 'Tautan Publikasi Jurnal / OJS',
                                'subtitle' => 'Link Online Artikel Terbit',
                                'category' => 'OJS Online',
                                'theme' => ['bg' => 'bg-sky-50 dark:bg-sky-950/40', 'text' => 'text-sky-600 dark:text-sky-400', 'border' => 'border-sky-100 dark:border-sky-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />'
                            ],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($gradFileFields as $field => $info)
                            @php $val = $graduation->$field ?? null; @endphp
                            <div class="group relative flex flex-col justify-between p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 bg-white dark:bg-slate-800/90 hover:border-slate-300 dark:hover:border-slate-600 transition-all">
                                <div>
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border {{ $info['theme']['bg'] }} {{ $info['theme']['text'] }} {{ $info['theme']['border'] }} shadow-2xs">
                                                <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    {!! $info['svg'] !!}
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                                    {{ $info['category'] }}
                                                </span>
                                                <h5 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors leading-snug truncate" title="{{ $info['label'] }}">
                                                    {{ $info['label'] }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mb-3 line-clamp-1">
                                        {{ $info['subtitle'] }}
                                    </p>
                                </div>

                                <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2 mt-auto">
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        <span>Google Drive</span>
                                    </span>

                                    @if(!empty($val))
                                        <a href="{{ $val }}" target="_blank" rel="noopener noreferrer" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-orange-600 dark:text-orange-400 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/40 dark:hover:bg-orange-900/50 border border-orange-200/80 dark:border-orange-800/50 transition-all shadow-2xs group-hover:shadow-xs cursor-pointer">
                                            <span>Buka Tautan</span>
                                            <svg class="w-3.5 h-3.5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Belum diisi</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($graduation->student_notes)
                        <div class="mt-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/70 dark:border-slate-700/60 text-xs">
                            <span class="font-bold text-slate-600 dark:text-slate-300">Catatan Tambahan Mahasiswa:</span>
                            <span class="text-slate-500 dark:text-slate-400 italic ml-1">"{{ $graduation->student_notes }}"</span>
                        </div>
                    @endif

                    <!-- Toggle Button to Update Links -->
                    @if($graduation->status === 'rejected' || $graduation->status === 'pending')
                        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                            <button type="button" onclick="document.getElementById('form-edit-section').classList.toggle('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-orange-300 dark:border-orange-700 text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-xs font-bold transition-all cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Perbarui Tautan Berkas</span>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Collapsible Form to Update Files if Needed -->
                @if($graduation->status === 'rejected' || $graduation->status === 'pending')
                    <div id="form-edit-section" class="{{ $graduation->status === 'rejected' ? '' : 'hidden' }} p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30"
                         x-data="{
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
                                    alert('Gunakan pintasan Ctrl + V untuk menempelkan link.');
                                }
                            },
                            isDrive(url) {
                                return url && url.includes('drive.google.com');
                            }
                        }">
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider mb-4">
                            {{ $graduation->status === 'rejected' ? 'Formulir Perbaikan Tautan Berkas' : 'Perbarui Tautan Berkas' }}
                        </h4>

                        <form action="{{ route('student.graduation.store') }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- Batch Folder Filler -->
                            <div class="p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">💡</span>
                                        <div>
                                            <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200">Satu Folder Drive untuk Semua Berkas?</h5>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Tempelkan link folder di sini untuk mengisi kolom wajib secara otomatis.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 w-full sm:w-auto">
                                        <input type="url" x-model="batchFolderUrl" placeholder="https://drive.google.com/drive/folders/..." class="py-1.5 px-3 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 w-full sm:w-64">
                                        <button type="button" @click="applyBatchFolderUrl()" class="px-3.5 py-1.5 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded-lg transition-all shrink-0 cursor-pointer">
                                            Terapkan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- 1. final_thesis_file -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label for="edit_final_thesis_file" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                            1. Naskah Skripsi Lengkap Final <span class="text-rose-500">*</span>
                                        </label>
                                        <button type="button" @click="pasteSingle('final_thesis_file')" class="text-[10px] text-slate-500 hover:text-orange-600 font-bold px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">📋 Tempel</button>
                                    </div>
                                    <input type="url" name="final_thesis_file" id="edit_final_thesis_file" x-model="files.final_thesis_file" placeholder="https://drive.google.com/..." required class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-md border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900 py-2 px-3 font-mono">
                                    @error('final_thesis_file') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                                </div>

                                <!-- 2. journal_article_file -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label for="edit_journal_article_file" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                            2. Artikel Jurnal Ilmiah <span class="text-rose-500">*</span>
                                        </label>
                                        <button type="button" @click="pasteSingle('journal_article_file')" class="text-[10px] text-slate-500 hover:text-orange-600 font-bold px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">📋 Tempel</button>
                                    </div>
                                    <input type="url" name="journal_article_file" id="edit_journal_article_file" x-model="files.journal_article_file" placeholder="https://drive.google.com/..." required class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-md border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900 py-2 px-3 font-mono">
                                    @error('journal_article_file') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                                </div>

                                <!-- 3. plagiarism_file -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label for="edit_plagiarism_file" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                            3. Bukti Uji Plagiasi / Turnitin <span class="text-[10px] text-slate-400 font-normal uppercase">(Opsional)</span>
                                        </label>
                                        <button type="button" @click="pasteSingle('plagiarism_file')" class="text-[10px] text-slate-500 hover:text-orange-600 font-bold px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">📋 Tempel</button>
                                    </div>
                                    <input type="url" name="plagiarism_file" id="edit_plagiarism_file" x-model="files.plagiarism_file" placeholder="https://drive.google.com/..." class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-md border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900 py-2 px-3 font-mono">
                                    @error('plagiarism_file') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                                </div>

                                <!-- 4. publication_link -->
                                <div class="space-y-2">
                                    <label for="edit_publication_link" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                        4. Tautan Publikasi Jurnal Online / OJS <span class="text-[10px] text-slate-400 font-normal uppercase">(Opsional)</span>
                                    </label>
                                    <input type="url" name="publication_link" id="edit_publication_link" x-model="files.publication_link" placeholder="https://ejournal.unsub.ac.id/..." class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-md border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900 py-2 px-3 font-mono">
                                    @error('publication_link') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label for="edit_student_notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Catatan Tambahan untuk Petugas BAAK / Prodi (Opsional)
                                </label>
                                <textarea name="student_notes" id="edit_student_notes" rows="2" placeholder="Tuliskan keterangan bila terdapat berkas fisik atau persyaratan khusus yang sudah diserahkan..." class="w-full text-xs rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 p-2.5">{{ old('student_notes', $graduation->student_notes ?? '') }}</textarea>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" onclick="document.getElementById('form-edit-section').classList.add('hidden')" class="px-5 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-all">
                                    Batal
                                </button>
                                <button type="submit" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs uppercase tracking-wider rounded-md shadow-md transition-all cursor-pointer">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

        @else
            <!-- Upload Form (Initial Submission) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden" 
                 x-data="{
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
                            alert('Gunakan pintasan keyboard Ctrl + V untuk menempelkan link.');
                        }
                    },
                    isDrive(url) {
                        return url && url.includes('drive.google.com');
                    }
                }">
                
                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">
                        Formulir Pengajuan Yudisium & Berkas Bebas Tanggungan
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Silakan masukkan tautan Google Drive untuk berkas naskah skripsi final, artikel jurnal ilmiah, dan dokumen pendukung lainnya.
                    </p>
                </div>

                <form action="{{ route('student.graduation.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- Google Drive Permission Notice -->
                    <div class="p-4 rounded-xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-800/40 text-blue-900 dark:text-blue-300 text-xs flex items-start gap-3">
                        <div class="p-1 rounded bg-blue-500 text-white shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="font-bold">Pengaturan Akses Google Drive:</p>
                            <p class="text-[11px] leading-relaxed text-blue-800 dark:text-blue-300/90">
                                Pastikan izin berbagi folder/file disetel ke <strong class="text-blue-900 dark:text-white">"Siapa saja yang memiliki link dapat melihat" (Anyone with the link can view)</strong> agar tim verifikator BAAK / Program Studi dapat membuka dokumen tanpa kendala permohonan izin akses.
                            </p>
                        </div>
                    </div>

                    <!-- Batch Folder Filler -->
                    <div class="p-4 bg-orange-50/50 dark:bg-slate-900/50 border border-orange-200/80 dark:border-slate-700 rounded-xl">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-base">💡</span>
                                <div>
                                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200">Satu Folder Drive untuk Semua Berkas?</h5>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Tempelkan link folder di sini untuk mengisi kolom wajib secara otomatis.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <input type="url" x-model="batchFolderUrl" placeholder="https://drive.google.com/drive/folders/..." class="py-1.5 px-3 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 w-full sm:w-64">
                                <button type="button" @click="applyBatchFolderUrl()" class="px-3.5 py-1.5 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded-lg transition-all shrink-0 cursor-pointer">
                                    Terapkan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Form Inputs Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 1. Naskah Skripsi Final -->
                        <div class="p-4 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <label for="final_thesis_file" class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                                    1. Naskah Skripsi Lengkap Final <span class="text-rose-500">*</span>
                                </label>
                                <button type="button" @click="pasteSingle('final_thesis_file')" class="px-2 py-0.5 bg-white dark:bg-slate-800 hover:bg-orange-50 text-slate-600 dark:text-slate-300 rounded text-[10px] font-bold border border-slate-200 dark:border-slate-700 shadow-2xs cursor-pointer">
                                    📋 Tempel
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                Naskah Cover s.d. Lampiran yang telah memuat pengesahan tanda tangan asli / TTE.
                            </p>
                            <input type="url" name="final_thesis_file" id="final_thesis_file" x-model="files.final_thesis_file" placeholder="https://drive.google.com/..." required class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-xl border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-800 shadow-2xs py-2 px-3 font-mono">
                            
                            <div class="flex items-center justify-between pt-1">
                                <template x-if="isDrive(files.final_thesis_file)">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Drive Valid</span>
                                    </span>
                                </template>
                                <template x-if="files.final_thesis_file && !isDrive(files.final_thesis_file)">
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">🔗 Link Terisi</span>
                                </template>
                                <template x-if="!files.final_thesis_file">
                                    <span class="text-[10px] text-slate-400 italic">Wajib diisi</span>
                                </template>
                                <template x-if="files.final_thesis_file">
                                    <a :href="files.final_thesis_file" target="_blank" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Buka Link ↗</a>
                                </template>
                            </div>
                            @error('final_thesis_file') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- 2. Artikel Jurnal Ilmiah -->
                        <div class="p-4 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <label for="journal_article_file" class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                                    2. Artikel Jurnal Ilmiah (Siap Publikasi) <span class="text-rose-500">*</span>
                                </label>
                                <button type="button" @click="pasteSingle('journal_article_file')" class="px-2 py-0.5 bg-white dark:bg-slate-800 hover:bg-orange-50 text-slate-600 dark:text-slate-300 rounded text-[10px] font-bold border border-slate-200 dark:border-slate-700 shadow-2xs cursor-pointer">
                                    📋 Tempel
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                Format artikel jurnal ilmiah siap publikasi sesuai pedoman Program Studi.
                            </p>
                            <input type="url" name="journal_article_file" id="journal_article_file" x-model="files.journal_article_file" placeholder="https://drive.google.com/..." required class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-xl border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-800 shadow-2xs py-2 px-3 font-mono">
                            
                            <div class="flex items-center justify-between pt-1">
                                <template x-if="isDrive(files.journal_article_file)">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Drive Valid</span>
                                    </span>
                                </template>
                                <template x-if="files.journal_article_file && !isDrive(files.journal_article_file)">
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">🔗 Link Terisi</span>
                                </template>
                                <template x-if="!files.journal_article_file">
                                    <span class="text-[10px] text-slate-400 italic">Wajib diisi</span>
                                </template>
                                <template x-if="files.journal_article_file">
                                    <a :href="files.journal_article_file" target="_blank" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Buka Link ↗</a>
                                </template>
                            </div>
                            @error('journal_article_file') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- 3. Bukti Uji Plagiasi / Turnitin -->
                        <div class="p-4 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <label for="plagiarism_file" class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                                    3. Bukti Uji Plagiasi / Turnitin <span class="text-[10px] text-slate-400 font-normal uppercase">(Opsional)</span>
                                </label>
                                <button type="button" @click="pasteSingle('plagiarism_file')" class="px-2 py-0.5 bg-white dark:bg-slate-800 hover:bg-orange-50 text-slate-600 dark:text-slate-300 rounded text-[10px] font-bold border border-slate-200 dark:border-slate-700 shadow-2xs cursor-pointer">
                                    📋 Tempel
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                Sertifikat hasil uji kemiripan (Turnitin) yang telah disahkan bila dipersyaratkan.
                            </p>
                            <input type="url" name="plagiarism_file" id="plagiarism_file" x-model="files.plagiarism_file" placeholder="https://drive.google.com/..." class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-xl border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-800 shadow-2xs py-2 px-3 font-mono">
                            
                            <div class="flex items-center justify-between pt-1">
                                <template x-if="isDrive(files.plagiarism_file)">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Drive Valid</span>
                                    </span>
                                </template>
                                <template x-if="files.plagiarism_file && !isDrive(files.plagiarism_file)">
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">🔗 Link Terisi</span>
                                </template>
                                <template x-if="!files.plagiarism_file">
                                    <span class="text-[10px] text-slate-400 italic">Opsional</span>
                                </template>
                                <template x-if="files.plagiarism_file">
                                    <a :href="files.plagiarism_file" target="_blank" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Buka Link ↗</a>
                                </template>
                            </div>
                            @error('plagiarism_file') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- 4. Link Publikasi OJS -->
                        <div class="p-4 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl space-y-2">
                            <label for="publication_link" class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                                4. Tautan Publikasi Jurnal / OJS <span class="text-[10px] text-slate-400 font-normal uppercase">(Opsional)</span>
                            </label>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                URL publikasi jika artikel telah terbit di jurnal elektronik (OJS) atau repositori kampus.
                            </p>
                            <input type="url" name="publication_link" id="publication_link" x-model="files.publication_link" placeholder="https://ejournal.unsub.ac.id/..." class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-xl border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-800 shadow-2xs py-2 px-3 font-mono">
                            @error('publication_link') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Student Notes -->
                    <div class="space-y-1.5">
                        <label for="student_notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Catatan Tambahan untuk Petugas BAAK / Prodi <span class="text-[10px] text-slate-400 font-normal uppercase tracking-wider">(Opsional)</span>
                        </label>
                        <textarea name="student_notes" id="student_notes" rows="3" placeholder="Tuliskan keterangan bila terdapat berkas fisik atau persyaratan khusus yang sudah diserahkan di loket..." class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 shadow-2xs p-3 leading-relaxed">{{ old('student_notes', $graduation->student_notes ?? '') }}</textarea>
                        @error('student_notes') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit Footer -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 dark:border-slate-700">
                        <span class="text-[11px] text-slate-400">
                            Pastikan seluruh link dapat diakses publik oleh petugas BAAK.
                        </span>
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs uppercase tracking-wider rounded-md shadow-md transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Kirim Pengajuan Yudisium</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>

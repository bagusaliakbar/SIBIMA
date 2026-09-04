<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Logbook Bimbingan', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6">
        <!-- 1. KARTU INFORMASI SKRIPSI & PROGRESS BIMBINGAN (BANNER ATAS) -->
        @if($thesis)
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-7 border border-slate-200/90 dark:border-slate-700 shadow-xs space-y-6">
                <!-- Top Row: Tag & Status Skripsi -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2.5">
                        <span class="px-3 py-1 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase tracking-wider border border-orange-500/20">
                            Logbook Skripsi Mahasiswa
                        </span>
                        @if($thesis->topic)
                            <span class="text-xs text-slate-400 font-medium">Bidang: <strong class="text-slate-700 dark:text-slate-200">{{ $thesis->topic }}</strong></span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Status Skripsi:</span>
                        <x-status-badge type="orange" :label="strtoupper($thesis->status)" />
                    </div>
                </div>

                <!-- Judul Skripsi -->
                <div class="space-y-1.5">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Judul Skripsi:</span>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-slate-100 leading-relaxed">
                        {{ $thesis->display_title ?: 'Judul skripsi belum diatur' }}
                    </h2>
                </div>

                <!-- Dosen Pembimbing & Progress Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 pt-2">
                    <!-- Pembimbing 1 -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700 flex flex-col justify-between gap-4">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-wider border border-indigo-200 dark:border-indigo-800">
                                    Pembimbing 1
                                </span>
                                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">
                                    {{ $countP1 }}x Bimbingan
                                </span>
                            </div>

                            @if($thesis->pembimbing1)
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0">
                                        <img src="{{ $thesis->pembimbing1->avatar_url }}" alt="{{ $thesis->pembimbing1->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-black text-slate-900 dark:text-slate-100 truncate">{{ $thesis->pembimbing1->name }}</h4>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $thesis->pembimbing1->identifier ?: 'NIP/NIDN -' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Belum ditentukan</p>
                            @endif
                        </div>

                        <!-- ACC Badges P1 -->
                        <div class="pt-3 border-t border-slate-200/60 dark:border-slate-700 flex items-center gap-2 flex-wrap text-[10px]">
                            @if($thesis->acc_up_p1)
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-black uppercase">
                                    ✓ ACC Seminar
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 font-semibold">
                                    Belum ACC Seminar
                                </span>
                            @endif

                            @if($thesis->acc_sidang_p1)
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-black uppercase">
                                    ✓ ACC Sidang
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 font-semibold">
                                    Belum ACC Sidang
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Pembimbing 2 -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700 flex flex-col justify-between gap-4">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 text-[10px] font-black uppercase tracking-wider border border-purple-200 dark:border-purple-800">
                                    Pembimbing 2
                                </span>
                                <span class="text-xs font-black text-purple-600 dark:text-purple-400">
                                    {{ $countP2 }}x Bimbingan
                                </span>
                            </div>

                            @if($thesis->pembimbing2)
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0">
                                        <img src="{{ $thesis->pembimbing2->avatar_url }}" alt="{{ $thesis->pembimbing2->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-black text-slate-900 dark:text-slate-100 truncate">{{ $thesis->pembimbing2->name }}</h4>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $thesis->pembimbing2->identifier ?: 'NIP/NIDN -' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Belum ditentukan</p>
                            @endif
                        </div>

                        <!-- ACC Badges P2 -->
                        <div class="pt-3 border-t border-slate-200/60 dark:border-slate-700 flex items-center gap-2 flex-wrap text-[10px]">
                            @if($thesis->acc_up_p2)
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-black uppercase">
                                    ✓ ACC Seminar
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 font-semibold">
                                    Belum ACC Seminar
                                </span>
                            @endif

                            @if($thesis->acc_sidang_p2)
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-black uppercase">
                                    ✓ ACC Sidang
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 font-semibold">
                                    Belum ACC Sidang
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Keseluruhan -->
                    @php
                        $targetSessions = 8;
                        $progressPercent = min(100, round(($totalCompletedCount / $targetSessions) * 100));
                        $isTargetReached = $totalCompletedCount >= $targetSessions;
                    @endphp
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-orange-200/80 dark:border-orange-900/40 flex flex-col justify-between gap-3">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-wider">
                                    Total Sesi Bimbingan
                                </span>
                                <span class="text-xs font-black text-slate-900 dark:text-slate-100">
                                    {{ $totalCompletedCount }} / {{ $targetSessions }} Sesi
                                </span>
                            </div>

                            <!-- Progress bar -->
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden mb-2">
                                <div class="bg-gradient-to-r from-orange-500 to-amber-500 h-full rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] font-bold">
                                <span class="text-slate-500 dark:text-slate-400">Pencapaian:</span>
                                <span class="{{ $isTargetReached ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }} font-black">
                                    {{ $progressPercent }}% {{ $isTargetReached ? '(Target Terpenuhi)' : '' }}
                                </span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-200/80 dark:border-slate-700 text-[10px] text-slate-600 dark:text-slate-300 font-medium">
                            @if($isTargetReached)
                                <span class="text-emerald-700 dark:text-emerald-300 font-bold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Syarat minimal bimbingan untuk mendaftar seminar/sidang telah terpenuhi.
                                </span>
                            @else
                                <span class="text-slate-500 dark:text-slate-400">
                                    Tersisa <strong>{{ max(0, $targetSessions - $totalCompletedCount) }} sesi lagi</strong> untuk memenuhi target bimbingan.
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. KARTU UTAMA LOGBOOK (FILTER, PENCARIAN, EXPORT, TIMELINE) -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/90 dark:border-slate-700 overflow-hidden">
            <!-- Header Bar -->
            <div class="p-6 border-b border-slate-200/80 dark:border-slate-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-50 dark:bg-slate-800">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Riwayat Catatan Logbook</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                        Dokumentasi resmi seluruh arahan, revisi, dan hasil bimbingan skripsi.
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <!-- Search Input (mencakup topik, catatan mhs, feedback dosen) -->
                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari topik, catatan, atau revisi..." 
                        route="logbooks.index" 
                        :params="['dosen' => ($filterDosen ?? 'all') !== 'all' ? $filterDosen : null]" />

                    <!-- Export PDF Button -->
                    <a href="{{ route('logbooks.export-pdf', array_filter(['dosen' => ($filterDosen ?? 'all') !== 'all' ? $filterDosen : null])) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider transition-all shadow-xs shrink-0" 
                       title="Unduh seluruh catatan logbook dalam format PDF">
                        <svg class="w-4 h-4 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Export PDF</span>
                    </a>
                </div>
            </div>

            <!-- 3. QUICK FILTER PEMBIMBING TABS -->
            @if($thesis && ($thesis->pembimbing1 || $thesis->pembimbing2))
                <div class="px-6 py-3.5 bg-slate-100/70 dark:bg-slate-900/70 border-b border-slate-200/80 dark:border-slate-700 flex items-center gap-2 overflow-x-auto">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 shrink-0 mr-1">
                        Filter Pembimbing:
                    </span>

                    <!-- Tab Semua -->
                    <a href="{{ route('logbooks.index', array_filter(['search' => $search, 'dosen' => 'all'])) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($filterDosen ?? 'all') === 'all' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                        <span>Semua Pembimbing</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ ($filterDosen ?? 'all') === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                            {{ $totalCompletedCount }}
                        </span>
                    </a>

                    <!-- Tab P1 -->
                    @if($thesis->pembimbing1)
                        <a href="{{ route('logbooks.index', array_filter(['search' => $search, 'dosen' => 'p1'])) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($filterDosen ?? 'all') === 'p1' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                            <span>Pembimbing 1 ({{ Str::limit($thesis->pembimbing1->name, 22) }})</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ ($filterDosen ?? 'all') === 'p1' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                {{ $countP1 }}
                            </span>
                        </a>
                    @endif

                    <!-- Tab P2 -->
                    @if($thesis->pembimbing2)
                        <a href="{{ route('logbooks.index', array_filter(['search' => $search, 'dosen' => 'p2'])) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($filterDosen ?? 'all') === 'p2' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                            <span>Pembimbing 2 ({{ Str::limit($thesis->pembimbing2->name, 22) }})</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ ($filterDosen ?? 'all') === 'p2' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                {{ $countP2 }}
                            </span>
                        </a>
                    @endif
                </div>
            @endif

            <!-- 4. DAFTAR RIWAYAT LOGBOOK -->
            <div class="p-6 bg-slate-50/60 dark:bg-slate-900/60">
                @if($sessions->isEmpty())
                    @if($search || ($filterDosen ?? 'all') !== 'all')
                        <div class="py-12 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Tidak ada catatan bimbingan yang cocok</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                                Coba sesuaikan kata kunci pencarian atau pilih filter pembimbing yang berbeda.
                            </p>
                            <a href="{{ route('logbooks.index') }}" class="inline-flex items-center mt-4 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                Reset Filter
                            </a>
                        </div>
                    @else
                        <x-empty-state description="Belum ada data logbook bimbingan yang telah diselesaikan oleh dosen pembimbing." icon="logbook" />
                    @endif
                @else
                    <!-- Daftar Kartu Sesi Logbook -->
                    <div class="space-y-6">
                        @foreach($sessions as $session)
                            @php
                                $seqNumber = $sessionOrderMap[$session->id] ?? $loop->iteration;
                                $dosenSeq = $sessionDosenOrderMap[$session->id] ?? null;
                                
                                $isP1 = $thesis && $session->dosen_id === $thesis->pembimbing1_id;
                                $isP2 = $thesis && $session->dosen_id === $thesis->pembimbing2_id;
                                $pembimbingLabel = $isP1 ? 'Pembimbing 1' : ($isP2 ? 'Pembimbing 2' : 'Dosen Pembimbing');

                                $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                                $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                                $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                            @endphp

                            <!-- Session Card Content -->
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/90 dark:border-slate-700 p-5 sm:p-6 transition-all hover:border-orange-300 dark:hover:border-orange-500/40 shadow-xs dark:shadow-none space-y-4">
                                <!-- Card Header: Badges & Info -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <!-- Bimbingan #N Pill -->
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-500 text-white text-xs font-black shadow-xs shadow-orange-500/20">
                                            Bimbingan #{{ $seqNumber }}
                                        </span>

                                        @if($dosenSeq)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/80 text-xs font-bold shadow-2xs">
                                                {{ $isP1 ? 'P1' : ($isP2 ? 'P2' : 'Dosen') }} ke-{{ $dosenSeq }}
                                            </span>
                                        @endif

                                        <!-- Waktu Bimbingan -->
                                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-300 font-bold">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }} • {{ $session->scheduled_at->format('H:i') }} WIB</span>
                                        </span>

                                        <!-- Metode & Lokasi -->
                                        @if($session->type === 'online')
                                            @if($session->location)
                                                <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl {{ $isMeet ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : ($isZoom ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800') }} transition-all font-bold text-xs shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                    <span>{{ $isMeet ? 'Google Meet' : ($isZoom ? 'Zoom Meeting' : 'Online') }}</span>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-xl border border-indigo-200 dark:border-indigo-800 text-xs font-bold shadow-2xs">
                                                    🎥 Daring
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-600 text-xs font-bold shadow-2xs">
                                                🏢 {{ $session->location ?: 'Tatap Muka' }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="shrink-0">
                                        <x-status-badge type="emerald" label="SELESAI" />
                                    </div>
                                </div>

                                <!-- Topik Sesi -->
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 block mb-1">Topik Diskusi / Bahasan:</span>
                                    <h4 class="text-base sm:text-lg font-black text-slate-900 dark:text-slate-100 leading-snug">
                                        {{ $session->topic }}
                                    </h4>
                                </div>

                                <!-- Hasil & Catatan Pembimbing (Highlight Box) -->
                                <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/60 dark:bg-slate-900/80 border border-amber-200/80 dark:border-amber-500/30 space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <h5 class="text-[10px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            <span>Hasil & Catatan Pembimbing</span>
                                        </h5>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed font-medium whitespace-pre-line italic">
                                        {{ $session->feedback ?: 'Tidak ada catatan pembimbing tertulis untuk sesi ini.' }}
                                    </p>
                                </div>

                                <!-- Catatan Mahasiswa (Jika Ada) -->
                                @if($session->notes)
                                    <div class="p-4 sm:p-5 bg-slate-50/90 dark:bg-slate-900/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-3 shadow-2xs">
                                        <div class="flex items-center gap-2 pb-2.5 border-b border-slate-200/70 dark:border-slate-700/70">
                                            <div class="w-5 h-5 rounded-md bg-slate-200/80 dark:bg-slate-800 flex items-center justify-center shrink-0 text-slate-500">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Catatan Mahasiswa</span>
                                        </div>
                                        <div class="px-0.5">
                                            <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal whitespace-pre-line">
                                                {{ $session->notes }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Dokumen Draft Lampiran (Jika Ada) -->
                                @if($session->document_path)
                                    <div class="pt-1">
                                        <a href="{{ filter_var($session->document_path, FILTER_VALIDATE_URL) ? $session->document_path : route('download.private', ['path' => $session->document_path]) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 text-xs font-bold transition-all shadow-2xs group/doc">
                                            <svg class="w-3.5 h-3.5 text-indigo-500 group-hover/doc:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            <span>Unduh Berkas Bimbingan:</span>
                                            <span class="font-normal underline">{{ $session->document_original_name ?: 'Dokumen Draft' }}</span>
                                        </a>
                                    </div>
                                @endif

                                <!-- Footer Card: Dosen Pembimbing Info -->
                                @if($session->dosen)
                                    <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3 flex-wrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0">
                                                <img src="{{ $session->dosen->avatar_url }}" alt="{{ $session->dosen->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $session->dosen->name }}</span>
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase tracking-wider {{ $isP1 ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300' : ($isP2 ? 'bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400') }}">
                                                {{ $pembimbingLabel }}
                                            </span>
                                                </div>
                                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $session->dosen->identifier ?: 'NIP/NIDN -' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Pagination -->
                @if($sessions->hasPages())
                    <div class="mt-8 pt-6 border-t border-slate-200/80 dark:border-slate-700">
                        {{ $sessions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

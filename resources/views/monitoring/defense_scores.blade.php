<x-app-layout>
    <x-slot name="header">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Monitoring', 'route' => null],
                ['label' => 'Nilai Sidang', 'route' => route('monitoring.defense-scores')]
            ]" />
            <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight mt-1">
                Rekapitulasi Nilai Sidang
            </h2>
        </div>
    </x-slot>

    <div x-data="{ 
        selectedIds: [], 
        allIds: {{ json_encode($defenseDetails->pluck('id')->toArray()) }},
        toggleAll() {
            if (this.selectedIds.length === this.allIds.length) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [...this.allIds];
            }
        },
        downloadSelected() {
            if (this.selectedIds.length === 0) {
                alert('Pilih minimal satu mahasiswa.');
                return;
            }
            window.location.href = '{{ route('monitoring.batch-export-berita-acara') }}?category=defense&ids=' + this.selectedIds.join(',');
        }
    }" class="space-y-5">

        <!-- Filter Chips Sekali Klik -->
        <div class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
            <a href="{{ route('monitoring.defense-scores', array_merge(request()->query(), ['status_filter' => 'all'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-black transition-all shadow-xs shrink-0
               {{ ($statusFilter ?? 'all') === 'all' 
                   ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-md ring-2 ring-slate-900/20 dark:ring-white/20' 
                   : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/80 border border-slate-200/80 dark:border-slate-700/60' }}">
                <span>Semua</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($statusFilter ?? 'all') === 'all' ? 'bg-white/20 text-white dark:bg-slate-900/20 dark:text-slate-900' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                    {{ $filterCounts['all'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('monitoring.defense-scores', array_merge(request()->query(), ['status_filter' => 'incomplete_grades'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-black transition-all shadow-xs shrink-0
               {{ ($statusFilter ?? 'all') === 'incomplete_grades' 
                   ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25 ring-2 ring-amber-500/30' 
                   : 'bg-white dark:bg-slate-800 text-amber-700 dark:text-amber-400 hover:bg-amber-50/60 dark:hover:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60' }}">
                <span>⚠️ Nilai Belum Lengkap</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($statusFilter ?? 'all') === 'incomplete_grades' ? 'bg-white/25 text-white' : 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300' }}">
                    {{ $filterCounts['incomplete_grades'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('monitoring.defense-scores', array_merge(request()->query(), ['status_filter' => 'pending_revisions'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-black transition-all shadow-xs shrink-0
               {{ ($statusFilter ?? 'all') === 'pending_revisions' 
                   ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25 ring-2 ring-indigo-500/30' 
                   : 'bg-white dark:bg-slate-800 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-50/60 dark:hover:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60' }}">
                <span>⏳ Revisi Menunggu Review</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($statusFilter ?? 'all') === 'pending_revisions' ? 'bg-white/25 text-white' : 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300' }}">
                    {{ $filterCounts['pending_revisions'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('monitoring.defense-scores', array_merge(request()->query(), ['status_filter' => 'all_passed'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-black transition-all shadow-xs shrink-0
               {{ ($statusFilter ?? 'all') === 'all_passed' 
                   ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25 ring-2 ring-emerald-500/30' 
                   : 'bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50/60 dark:hover:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60' }}">
                <span>✅ Sudah Lulus Semua</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($statusFilter ?? 'all') === 'all_passed' ? 'bg-white/25 text-white' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300' }}">
                    {{ $filterCounts['all_passed'] ?? 0 }}
                </span>
            </a>
        </div>

        <!-- Premium Toolbar Row -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-5 rounded-3xl border border-slate-100 dark:border-slate-700/50 flex flex-col lg:flex-row lg:items-center justify-between gap-4 shadow-sm">
            <!-- Left: Wave Filter and Search Input -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1 max-w-4xl w-full">
                <!-- Wave Filter -->
                <form action="{{ route('monitoring.defense-scores') }}" method="GET" class="shrink-0 w-full sm:w-auto">
                    @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                    @if(request('status_filter')) <input type="hidden" name="status_filter" value="{{ request('status_filter') }}"> @endif
                    <select name="wave_id" onchange="this.form.submit()" 
                            class="w-full sm:w-auto pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm min-w-[200px] text-slate-700 dark:text-slate-350">
                        <option value="">Semua Gelombang</option>
                        @foreach($waves as $wave)
                            <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                {{ $wave->name }} {{ $wave->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- Search Input -->
                <form action="{{ route('monitoring.defense-scores') }}" method="GET" class="relative group flex-1 w-full">
                    @if($selectedWaveId) <input type="hidden" name="wave_id" value="{{ $selectedWaveId }}"> @endif
                    @if(request('status_filter')) <input type="hidden" name="status_filter" value="{{ request('status_filter') }}"> @endif
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-transform group-focus-within:scale-105">
                        <svg class="w-4 h-4 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari mahasiswa atau judul..." 
                           class="w-full pl-10 pr-9 py-2.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-medium focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm placeholder:text-slate-400/85">
                    @if($search)
                        <a href="{{ route('monitoring.defense-scores', ['wave_id' => $selectedWaveId, 'status_filter' => request('status_filter')]) }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-rose-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Right: Export Buttons and ZIP Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <!-- Batch Actions (ZIP Selected) -->
                <button @click="downloadSelected()" 
                        x-show="selectedIds.length > 0"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold transition-all shadow-md animate-in fade-in zoom-in duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    ZIP Terpilih (<span x-text="selectedIds.length"></span>)
                </button>

                <a href="{{ route('monitoring.batch-export-berita-acara', ['category' => 'defense', 'wave_id' => $selectedWaveId]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl text-xs font-bold transition-all shadow-md group w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    ZIP Semua
                </a>

                <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1 hidden sm:block"></div>

                <a href="{{ route('monitoring.defense-scores.export-excel', ['wave_id' => $selectedWaveId]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold transition-all shadow-md group w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Excel
                </a>
                <a href="{{ route('monitoring.defense-scores.export-pdf', ['wave_id' => $selectedWaveId]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-xs font-bold transition-all shadow-md group w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    PDF
                </a>
            </div>
        </div>

        <!-- Table Container with Sticky Headers & Freeze Column -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-270px)] min-h-[460px] relative">
                <table class="w-full text-left border-collapse border-separate border-spacing-0">
                    <thead class="sticky top-0 z-30">
                        <!-- Header Row 1 -->
                        <tr class="bg-slate-100/95 dark:bg-slate-900/95 backdrop-blur-md text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                            <!-- Frozen Column 1: Checkbox -->
                            <th rowspan="2" class="sticky top-0 left-0 z-40 bg-slate-100 dark:bg-slate-900 py-4 px-3 text-center border-r border-b border-slate-200 dark:border-slate-700 w-12 min-w-[48px]">
                                <input type="checkbox" @click="toggleAll()" :checked="selectedIds.length === allIds.length && allIds.length > 0" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 transition-all cursor-pointer">
                            </th>
                            <!-- Frozen Column 2: NO -->
                            <th rowspan="2" class="sticky top-0 left-[48px] z-40 bg-slate-100 dark:bg-slate-900 py-4 px-3 text-center font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] w-12 min-w-[48px]">
                                NO
                            </th>
                            <!-- Frozen Column 3: NPM -->
                            <th rowspan="2" class="sticky top-0 left-[96px] z-40 bg-slate-100 dark:bg-slate-900 py-4 px-3 text-center font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] w-28 min-w-[112px]">
                                NPM
                            </th>
                            <!-- Frozen Column 4: NAMA MAHASISWA & JUDUL -->
                            <th rowspan="2" class="sticky top-0 left-[208px] z-40 bg-slate-100 dark:bg-slate-900 py-4 px-5 text-left font-black text-[10px] border-r-2 border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] w-72 min-w-[280px] shadow-[4px_0_12px_-2px_rgba(0,0,0,0.08)] dark:shadow-[4px_0_12px_-2px_rgba(0,0,0,0.4)]">
                                NAMA MAHASISWA & JUDUL
                            </th>
                            <!-- Scrollable Headers -->
                            <th rowspan="2" class="sticky top-0 z-30 bg-slate-100 dark:bg-slate-900 py-4 px-5 text-left font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] w-56 min-w-[220px]">
                                TIM PENELAAH
                            </th>
                            <th colspan="3" class="sticky top-0 z-30 bg-slate-200/80 dark:bg-slate-800 py-2.5 px-4 text-center font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em]">
                                KOMPONEN PENILAIAN SIDANG
                            </th>
                            <th rowspan="2" class="sticky top-0 z-30 bg-slate-100 dark:bg-slate-900 py-4 px-3 text-center font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] w-24 min-w-[90px]">
                                JML NILAI
                            </th>
                            <th rowspan="2" class="sticky top-0 z-30 bg-slate-100 dark:bg-slate-900 py-4 px-3 text-center font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] w-20 min-w-[80px]">
                                TOTAL
                            </th>
                            <th rowspan="2" class="sticky top-0 z-30 bg-slate-100 dark:bg-slate-900 py-4 px-4 text-center font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] w-24 min-w-[90px]">
                                NILAI AKHIR
                            </th>
                            <th rowspan="2" class="sticky top-0 z-30 bg-slate-100 dark:bg-slate-900 py-4 px-4 text-center font-black text-[10px] border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] w-20 min-w-[80px]">
                                NILAI HURUF
                            </th>
                        </tr>

                        <!-- Header Row 2 (Component Subheaders) -->
                        <tr class="bg-slate-100/90 dark:bg-slate-900/90 backdrop-blur-md text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                            <th class="sticky top-[42px] z-30 bg-slate-100/95 dark:bg-slate-900/95 py-2.5 px-3 text-center font-black text-[9px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-tighter min-w-[120px]">
                                PRESENTASI (25%)
                            </th>
                            <th class="sticky top-[42px] z-30 bg-slate-100/95 dark:bg-slate-900/95 py-2.5 px-3 text-center font-black text-[9px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-tighter min-w-[140px]">
                                PENJELASAN NASKAH (40%)
                            </th>
                            <th class="sticky top-[42px] z-30 bg-slate-100/95 dark:bg-slate-900/95 py-2.5 px-3 text-center font-black text-[9px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-tighter min-w-[130px]">
                                PENULISAN NASKAH (35%)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($defenseDetails as $index => $detail)
                            @php
                                $revP1 = $detail->revisions->where('examiner_id', $detail->thesis->pembimbing1_id)->first();
                                $revE1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
                                $revE2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();

                                $calc = function($rev) {
                                    if (!$rev || $rev->score_presentation === null) return null;
                                    return ($rev->score_presentation * 0.25) + ($rev->score_explanation * 0.40) + ($rev->score_writing * 0.35);
                                };

                                $scoreP1 = $calc($revP1);
                                $scoreE1 = $calc($revE1);
                                $scoreE2 = $calc($revE2);

                                $scores = collect([$scoreP1, $scoreE1, $scoreE2])->filter(fn($s) => $s !== null);
                                $totalScore = $scores->sum();
                                $finalScore = $scores->count() > 0 ? $totalScore / $scores->count() : 0;

                                $getGrade = function($s) {
                                    if ($s >= 80) return 'A';
                                    if ($s >= 70) return 'B';
                                    if ($s >= 60) return 'C';
                                    if ($s >= 50) return 'D';
                                    return 'E';
                                };
                                $finalGrade = $scores->count() > 0 ? $getGrade($finalScore) : '-';
                                $gradeColor = match($finalGrade) {
                                    'A' => 'emerald',
                                    'B' => 'blue',
                                    'C' => 'amber',
                                    default => 'slate'
                                };
                            @endphp
                            
                            <!-- Row 1: Pembimbing 1 with Frozen Left Columns -->
                            <tr class="group hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors" :class="selectedIds.includes({{ $detail->id }}) ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : ''">
                                <!-- Frozen Checkbox -->
                                <td rowspan="3" class="sticky left-0 z-20 bg-white dark:bg-slate-900 group-hover:bg-slate-50 dark:group-hover:bg-slate-800 py-4 px-3 text-center border-r border-b border-slate-100 dark:border-slate-700 align-middle">
                                    <input type="checkbox" x-model="selectedIds" value="{{ $detail->id }}" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 cursor-pointer transition-all">
                                </td>
                                <!-- Frozen NO -->
                                <td rowspan="3" class="sticky left-[48px] z-20 bg-slate-50 dark:bg-slate-950 group-hover:bg-slate-100 dark:group-hover:bg-slate-800 py-4 px-3 text-center font-bold text-xs border-r border-b border-slate-100 dark:border-slate-700 align-middle">
                                    {{ ($defenseDetails->currentPage() - 1) * $defenseDetails->perPage() + $index + 1 }}
                                </td>
                                <!-- Frozen NPM -->
                                <td rowspan="3" class="sticky left-[96px] z-20 bg-white dark:bg-slate-900 group-hover:bg-slate-50 dark:group-hover:bg-slate-800 py-4 px-3 text-center font-bold text-[11px] border-r border-b border-slate-100 dark:border-slate-700 align-middle text-slate-500 dark:text-slate-400 group-hover:text-emerald-600 transition-colors">
                                    {{ $detail->thesis->student->identifier }}
                                </td>
                                <!-- Frozen NAMA MAHASISWA & JUDUL -->
                                <td rowspan="3" class="sticky left-[208px] z-20 bg-white dark:bg-slate-900 group-hover:bg-slate-50 dark:group-hover:bg-slate-800 py-4 px-5 border-r-2 border-b border-slate-200 dark:border-slate-700 align-middle shadow-[4px_0_12px_-2px_rgba(0,0,0,0.08)] dark:shadow-[4px_0_12px_-2px_rgba(0,0,0,0.4)]">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <div class="font-black text-xs text-slate-800 dark:text-slate-100 uppercase tracking-tight group-hover:text-emerald-600 transition-colors truncate" title="{{ $detail->thesis->student->name }}">{{ $detail->thesis->student->name }}</div>
                                            <div class="text-[10px] text-slate-400 mt-1 line-clamp-2 font-medium leading-relaxed" title="{{ $detail->thesis->title }}">"{{ $detail->thesis->title }}"</div>
                                        </div>
                                        <a href="{{ route('monitoring.defense-scores.berita-acara', $detail->id) }}" 
                                           class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-white text-slate-400 rounded-lg transition-all shadow-xs group/btn shrink-0"
                                           title="Cetak Berita Acara">
                                            <svg class="w-4 h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    </div>
                                </td>

                                <!-- Scrollable Columns: Pembimbing 1 -->
                                <td class="py-3 px-5 text-[10px] font-bold border-r border-slate-100 dark:border-slate-700 bg-indigo-50/20 dark:bg-indigo-900/10">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-5 h-5 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[8px] font-black uppercase shrink-0">1.</span>
                                            <span class="text-slate-600 dark:text-slate-300 truncate" title="{{ $detail->thesis->pembimbing1?->name }}">{{ $detail->thesis->pembimbing1?->name ?? '-' }}</span>
                                        </div>
                                        @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->thesis?->pembimbing1_id)
                                            <a href="{{ route('defense-examiner.grading', ['detail' => $detail->id, 'target_examiner_id' => $detail->thesis->pembimbing1_id, 'redirect_to' => 'monitoring']) }}" 
                                               class="p-1 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 rounded transition-all shrink-0" 
                                               title="Input / Ubah Nilai Pembimbing 1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revP1->score_presentation ?? '-' }}</td>
                                <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revP1->score_explanation ?? '-' }}</td>
                                <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revP1->score_writing ?? '-' }}</td>
                                <td class="py-3 px-3 text-center text-xs font-black text-emerald-600 border-r border-slate-100 dark:border-slate-700 bg-emerald-50/40 dark:bg-emerald-900/10">
                                    {{ $scoreP1 ? number_format($scoreP1, 1) : '-' }}
                                </td>
                                <td rowspan="3" class="py-4 px-4 text-center font-black text-[13px] border-r border-b border-slate-100 dark:border-slate-700 align-middle text-slate-800 dark:text-slate-200">
                                    {{ number_format($totalScore, 1) }}
                                </td>
                                <td rowspan="3" class="py-4 px-4 text-center align-middle border-r border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                                    <div class="font-black text-lg text-slate-800 dark:text-slate-100">{{ number_format($finalScore, 1) }}</div>
                                </td>
                                <td rowspan="3" class="py-4 px-4 text-center align-middle border-b border-slate-100 dark:border-slate-700 bg-{{ $gradeColor }}-50/50 dark:bg-{{ $gradeColor }}-900/10 transition-colors duration-500">
                                    <div class="text-3xl font-black text-{{ $gradeColor }}-600 drop-shadow-xs">{{ $finalGrade }}</div>
                                </td>
                            </tr>

                            <!-- Row 2: Penguji 1 -->
                            <tr class="group hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors">
                                <td class="py-3 px-5 text-[10px] font-bold border-r border-slate-100 dark:border-slate-700 bg-rose-50/20 dark:bg-rose-900/10">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-5 h-5 rounded bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center text-[8px] font-black uppercase shrink-0">2.</span>
                                            <span class="text-slate-600 dark:text-slate-300 truncate" title="{{ $detail->examiner1?->name }}">{{ $detail->examiner1?->name ?? '-' }}</span>
                                        </div>
                                        @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->examiner1_id)
                                            <a href="{{ route('defense-examiner.grading', ['detail' => $detail->id, 'target_examiner_id' => $detail->examiner1_id, 'redirect_to' => 'monitoring']) }}" 
                                               class="p-1 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 rounded transition-all shrink-0" 
                                               title="Input / Ubah Nilai Penguji 1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE1->score_presentation ?? '-' }}</td>
                                <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE1->score_explanation ?? '-' }}</td>
                                <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE1->score_writing ?? '-' }}</td>
                                <td class="py-3 px-3 text-center text-xs font-black text-emerald-600 border-r border-slate-100 dark:border-slate-700 bg-emerald-50/40 dark:bg-emerald-900/10">
                                    {{ $scoreE1 ? number_format($scoreE1, 1) : '-' }}
                                </td>
                            </tr>

                            <!-- Row 3: Penguji 2 -->
                            <tr class="group hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors border-b-2 border-slate-200 dark:border-slate-700">
                                <td class="py-3 px-5 text-[10px] font-bold border-r border-b border-slate-100 dark:border-slate-700 bg-rose-50/20 dark:bg-rose-900/10">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-5 h-5 rounded bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center text-[8px] font-black uppercase shrink-0">3.</span>
                                            <span class="text-slate-600 dark:text-slate-300 truncate" title="{{ $detail->examiner2?->name }}">{{ $detail->examiner2?->name ?? '-' }}</span>
                                        </div>
                                        @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->examiner2_id)
                                            <a href="{{ route('defense-examiner.grading', ['detail' => $detail->id, 'target_examiner_id' => $detail->examiner2_id, 'redirect_to' => 'monitoring']) }}" 
                                               class="p-1 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 rounded transition-all shrink-0" 
                                               title="Input / Ubah Nilai Penguji 2">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center text-xs font-bold border-r border-b border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE2->score_presentation ?? '-' }}</td>
                                <td class="py-3 px-3 text-center text-xs font-bold border-r border-b border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE2->score_explanation ?? '-' }}</td>
                                <td class="py-3 px-3 text-center text-xs font-bold border-r border-b border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE2->score_writing ?? '-' }}</td>
                                <td class="py-3 px-3 text-center text-xs font-black text-emerald-600 border-r border-b border-slate-100 dark:border-slate-700 bg-emerald-50/40 dark:bg-emerald-900/10">
                                    {{ $scoreE2 ? number_format($scoreE2, 1) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4 border border-slate-100 dark:border-slate-700 shadow-inner">
                                            <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                        <h3 class="text-slate-400 font-bold text-sm uppercase tracking-widest">Belum Ada Data Nilai</h3>
                                        <p class="text-[11px] text-slate-400 mt-1 italic">Data nilai mahasiswa akan muncul setelah diinput oleh dosen penelaah atau coba ubah filter status di atas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($defenseDetails->hasPages())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
                    {{ $defenseDetails->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

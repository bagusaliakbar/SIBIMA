<x-app-layout>
    <x-slot name="header">
        <div class="w-full">
            <x-breadcrumb :items="[
                ['label' => 'Monitoring', 'route' => route('monitoring.index')],
                ['label' => 'Grafik Analitik', 'route' => null]
            ]" />

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight">
                    Grafik & Statistik Analitik SIBIMA
                </h2>

                <!-- Export Toolbar Actions -->
                <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap shrink-0">
                    <!-- Export Excel -->
                    <a href="{{ route('analytics.export-excel', request()->query()) }}" 
                       class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all"
                       title="Unduh seluruh data tabel analitik ke format Excel">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Export Excel</span>
                    </a>

                    <!-- Export PDF -->
                    <a href="{{ route('analytics.export-pdf', request()->query()) }}" 
                       class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all"
                       title="Cetak dan unduh laporan resmi berstandar PDF">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span>Export PDF</span>
                    </a>

                    <!-- Batch PNG Download Button -->
                    <button type="button" 
                            onclick="downloadAllChartsAsPng()"
                            class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all cursor-pointer"
                            title="Unduh seluruh 13 grafik sebagai berkas gambar PNG">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Unduh Semua PNG</span>
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8 pb-16" x-data="{ filterOpen: true }">

        <!-- FILTER TOOLBAR CARD -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm transition-all">
            <div class="flex items-center justify-between cursor-pointer select-none" @click="filterOpen = !filterOpen">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-200/50 dark:border-orange-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">Filter Data Analitik</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Saring visualisasi grafik berdasarkan gelombang, rentang angkatan (misal 2020-2022), dosen, status, dan tanggal.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if(!empty($filters['wave_id']) || !empty($filters['entry_year_from']) || !empty($filters['entry_year_to']) || !empty($filters['dosen_id']) || ($filters['status'] ?? 'all') !== 'all' || !empty($filters['date_from']) || !empty($filters['date_to']))
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30">
                            Filter Aktif
                            @if(!empty($filters['entry_year_from']) || !empty($filters['entry_year_to']))
                                • {{ $filters['entry_year_label'] ?? '' }}
                            @endif
                        </span>
                    @endif
                    <button type="button" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': filterOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Filter Inputs -->
            <form action="{{ route('analytics.index') }}" method="GET" x-show="filterOpen" x-transition class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <!-- Filter Gelombang -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Gelombang Pelaksanaan</label>
                        <select name="wave_id" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0">
                            <option value="all" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Semua Gelombang</option>
                            @foreach($waves as $w)
                                <option value="{{ $w->id }}" {{ ($filters['wave_id'] ?? '') == $w->id ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                    {{ $w->name }} {{ $w->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Angkatan: Per Angkatan & Rentang Angkatan -->
                    <div x-data="{ 
                        cohortType: '{{ ($filters['cohort_type'] ?? 'single') === 'range' ? 'range' : 'single' }}' 
                    }" class="relative">
                        <input type="hidden" name="cohort_type" :value="cohortType">

                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                <span x-show="cohortType === 'single'">Per Angkatan</span>
                                <span x-show="cohortType === 'range'">Rentang Angkatan</span>
                            </label>
                            
                            <!-- Mode Switcher Tabs -->
                            <div class="inline-flex items-center p-0.5 rounded-lg bg-slate-200/70 dark:bg-slate-700/70 text-[9px] font-bold">
                                <button type="button" 
                                        @click="cohortType = 'single'"
                                        :class="cohortType === 'single' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'"
                                        class="px-2 py-0.5 rounded-md transition-all cursor-pointer select-none"
                                        title="Saring satu tahun angkatan spesifik">
                                    Per Angkatan
                                </button>
                                <button type="button" 
                                        @click="cohortType = 'range'"
                                        :class="cohortType === 'range' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'"
                                        class="px-2 py-0.5 rounded-md transition-all cursor-pointer select-none"
                                        title="Saring rentang beberapa angkatan">
                                    Rentang
                                </button>
                            </div>
                        </div>

                        <!-- 1. Mode Per Angkatan (Single Select) -->
                        <div x-show="cohortType === 'single'">
                            <select name="entry_year" 
                                    :disabled="cohortType !== 'single'"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0">
                                <option value="all" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Semua Angkatan</option>
                                @foreach($cohortYears as $year)
                                    <option value="{{ $year }}" {{ ($filters['entry_year'] ?? '') == $year ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                        Angkatan {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Mode Rentang Angkatan (From - To) -->
                        <div x-show="cohortType === 'range'" class="grid grid-cols-2 gap-1.5">
                            <div>
                                <select name="entry_year_from" 
                                        :disabled="cohortType !== 'range'"
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0" 
                                        title="Dari Tahun Angkatan">
                                    <option value="all" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Dari Thn</option>
                                    @foreach($cohortYears as $year)
                                        <option value="{{ $year }}" {{ ($filters['entry_year_from'] ?? '') == $year ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="entry_year_to" 
                                        :disabled="cohortType !== 'range'"
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0" 
                                        title="Sampai Tahun Angkatan">
                                    <option value="all" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Sampai Thn</option>
                                    @foreach($cohortYears as $year)
                                        <option value="{{ $year }}" {{ ($filters['entry_year_to'] ?? '') == $year ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Dosen Pembimbing -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Dosen Pembimbing</label>
                        <select name="dosen_id" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0">
                            <option value="all" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Semua Dosen</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}" {{ ($filters['dosen_id'] ?? '') == $d->id ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status Skripsi -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Status Skripsi</label>
                        <select name="status" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0">
                            <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Semua Status</option>
                            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Skripsi Aktif</option>
                            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Sudah Selesai / Lulus</option>
                            <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pending Pengajuan</option>
                        </select>
                    </div>

                    <!-- Rentang Tanggal (From - To) -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Dari Tgl</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0 [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Sampai Tgl</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0 [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                    </div>
                </div>

                <!-- Submit & Reset Buttons -->
                <div class="flex items-center justify-end gap-2.5 mt-5">
                    <a href="{{ route('analytics.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        Reset Filter
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md shadow-orange-600/20 hover:shadow-orange-600/30 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- TOP KPI SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <!-- KPI 1: Total Mahasiswa -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-orange-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Mahasiswa</span>
                    <div class="w-8 h-8 rounded-lg bg-orange-500/10 text-orange-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ number_format($kpi['totalStudents']) }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="font-bold text-orange-600 dark:text-orange-400">{{ $kpi['activeTheses'] }} Aktif</span>
                    <span>•</span>
                    <span class="font-bold text-slate-600 dark:text-slate-300">{{ $kpi['completedTheses'] }} Lulus</span>
                </div>
            </div>

            <!-- KPI 2: Status Seminar -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-emerald-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Progres Seminar</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ number_format($kpi['seminarDone']) }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Sudah Seminar</span>
                    <span>•</span>
                    <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $kpi['seminarPending'] }} Belum</span>
                </div>
            </div>

            <!-- KPI 3: Status Sidang Akhir -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-indigo-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Sidang Skripsi</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ number_format($kpi['defenseDone']) }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">Sudah Sidang</span>
                    <span>•</span>
                    <span class="text-slate-500 dark:text-slate-400 font-bold">{{ $kpi['defensePending'] }} Belum</span>
                </div>
            </div>

            <!-- KPI 4: Sesi Bimbingan Selesai -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-cyan-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Sesi Bimbingan</span>
                    <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ number_format($kpi['completedSessions']) }}</div>
                <div class="mt-1 text-[11px] text-cyan-600 dark:text-cyan-400 font-bold">
                    Sesi Terlaksana & Disetujui
                </div>
            </div>

            <!-- KPI 5: Lulus Tepat Waktu -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-violet-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Tepat Waktu</span>
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 text-violet-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $kpi['onTimePercentage'] }}%</div>
                <div class="mt-1 text-[11px] text-violet-600 dark:text-violet-400 font-bold">
                    Masa Studi ≤ 4 Tahun
                </div>
            </div>

            <!-- KPI 6: Mahasiswa Kritis -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-rose-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Perlu Perhatian</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-rose-600 dark:text-rose-400 tracking-tight">{{ $kpi['criticalMentoringStudents'] + $kpi['criticalCohortStudents'] }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="text-rose-500 dark:text-rose-400 font-bold">{{ $kpi['criticalMentoringStudents'] }} Bimb. Pasif</span>
                    <span>•</span>
                    <span class="text-amber-500 dark:text-amber-400 font-bold">{{ $kpi['criticalCohortStudents'] }} > 4 Thn</span>
                </div>
            </div>
        </div>

        <!-- ROW 1: SEMINAR STATUS BY ADVISOR (P1 & P2) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart 1: Seminar Status per Dosen Pembimbing 1 -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            Mahasiswa Seminar per Pembimbing 1 (P1)
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Komparasi mahasiswa sudah vs belum seminar proposal berdasarkan Dosen Pembimbing 1.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartSeminarP1', 'grafik-seminar-pembimbing-1')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartSeminarP1"></canvas>
                </div>
            </div>

            <!-- Chart 2: Seminar Status per Dosen Pembimbing 2 -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            Mahasiswa Seminar per Pembimbing 2 (P2)
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Komparasi mahasiswa sudah vs belum seminar proposal berdasarkan Dosen Pembimbing 2.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartSeminarP2', 'grafik-seminar-pembimbing-2')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartSeminarP2"></canvas>
                </div>
            </div>
        </div>

        <!-- ROW 2: COHORT PROGRESS & UNFINISHED ADVISOR BACKLOG -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart 3: Mahasiswa Sudah vs Belum Seminar per Angkatan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Status Seminar per Angkatan Mahasiswa
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Distribusi mahasiswa yang sudah dan belum melaksanakan seminar proposal tiap angkatan.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartCohortSeminar', 'grafik-seminar-per-angkatan')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartCohortSeminar"></canvas>
                </div>
            </div>

            <!-- Chart 4: Mahasiswa Belum Lulus per Dosen Pembimbing -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Mahasiswa Belum Lulus per Pembimbing
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Jumlah mahasiswa aktif yang masih dalam proses bimbingan (belum lulus) per dosen.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartUnfinishedByAdvisor', 'grafik-belum-lulus-per-pembimbing')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartUnfinishedByAdvisor"></canvas>
                </div>
            </div>
        </div>

        <!-- ROW: DISTRIBUSI TAHAPAN BIMBINGAN PER DOSEN (FULL WIDTH) -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        Grafik Distribusi Bimbingan per Dosen
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pemetaan tahapan mahasiswa yang belum lulus (Belum Seminar, Seminar, Sidang Akhir, dan Kritikal Semester ≥ 13) per Dosen Pembimbing.</p>
                </div>
                <button type="button" onclick="downloadChartAsPng('chartDistributionByAdvisor', 'grafik-distribusi-bimbingan-per-dosen')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1 self-start sm:self-auto">
                    <span>📸 PNG</span>
                </button>
            </div>
            <div class="h-80 sm:h-96 relative">
                <canvas id="chartDistributionByAdvisor"></canvas>
            </div>
        </div>

        <!-- ROW 3: OVERALL STAGES & DEFENSE PROGRESS PER COHORT -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart 5: Distribusi Tahapan Skripsi Keseluruhan (Donut) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Distribusi Tahapan Skripsi
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Proporsi mahasiswa di tiap tahapan skripsi.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartStages', 'grafik-tahapan-skripsi')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-72 relative flex items-center justify-center">
                    <canvas id="chartStages"></canvas>
                </div>
            </div>

            <!-- Chart 6: Status Sidang Akhir per Angkatan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm lg:col-span-2 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            Status Sidang Akhir & Kelulusan per Angkatan
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Komparasi mahasiswa yang sudah sidang/lulus vs yang belum sidang per tahun angkatan.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartCohortDefense', 'grafik-sidang-per-angkatan')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-72 relative">
                    <canvas id="chartCohortDefense"></canvas>
                </div>
            </div>
        </div>

        <!-- ROW 4: MONTHLY MENTORING TRENDS & HEALTH MONITOR -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart 7: Tren Frekuensi Sesi Bimbingan per Bulan (Area Spline) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm lg:col-span-2 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            Tren Frekuensi Bimbingan per Bulan
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Volume aktivitas sesi bimbingan yang terlaksana selama 6 bulan terakhir.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartMonthlyTrends', 'grafik-tren-bimbingan-bulanan')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-72 relative">
                    <canvas id="chartMonthlyTrends"></canvas>
                </div>
            </div>

            <!-- Chart 8: Status Keaktifan Bimbingan (Early Warning) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Keaktifan Bimbingan (Health)
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Monitoring kontinuitas bimbingan mahasiswa aktif.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartHealth', 'grafik-keaktifan-bimbingan')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-72 relative flex items-center justify-center">
                    <canvas id="chartHealth"></canvas>
                </div>
            </div>
        </div>

        <!-- ROW 5: WORKLOAD VS QUOTA & DEFENSE GRADES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart 9: Beban Bimbingan vs Kuota Maksimal Dosen -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Beban Bimbingan Aktif vs Kuota per Dosen
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kapasitas beban bimbingan dosen aktif terhadap batas kuota institusi.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartWorkload', 'grafik-beban-kuota-dosen')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartWorkload"></canvas>
                </div>
            </div>

            <!-- Chart 10: Distribusi Nilai Kelulusan Sidang Skripsi -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Distribusi Nilai Sidang Skripsi
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Sebaran nilai mutu kelulusan sidang skripsi (Grade A, B, C, D, E).</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartScores', 'grafik-distribusi-nilai-sidang')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartScores"></canvas>
                </div>
            </div>
        </div>

        <!-- ROW 6: TOPIC DISTRIBUTION & WAVE DURATION -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart 11: Sebaran Bidang Minat / Topik Skripsi -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            Sebaran Bidang Minat & Topik Skripsi
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Klasifikasi penelitian skripsi berdasarkan bidang konsentrasi keilmuan.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartTopics', 'grafik-topik-skripsi')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-80 relative flex items-center justify-center">
                    <canvas id="chartTopics"></canvas>
                </div>
            </div>

            <!-- Chart 12: Rata-rata Durasi Penyelesaian per Gelombang -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            Rata-rata Durasi Pengerjaan per Gelombang
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Rata-rata masa pengerjaan skripsi (dalam bulan) tiap gelombang pelaksanaan.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartWaveDuration', 'grafik-durasi-gelombang')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <span>📸 PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartWaveDuration"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js & Datalabels Plugin CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        // Global chart collection for dynamic dark/light mode updates
        window.sibimaCharts = {};

        // Color palettes and theme configs
        function getChartThemeColors(isDark) {
            return {
                textColor: isDark ? '#94a3b8' : '#64748b',
                gridColor: isDark ? 'rgba(255, 255, 255, 0.07)' : 'rgba(0, 0, 0, 0.05)',
                tooltipBg: isDark ? '#0f172a' : '#1e293b',
                tooltipBorder: isDark ? '#334155' : '#cbd5e1',
                tooltipText: '#ffffff',
            };
        }

        // Initialize All Charts
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const theme = getChartThemeColors(isDark);

            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = theme.textColor;

            // Register ChartDataLabels plugin and configure global 'inside end' defaults
            if (typeof ChartDataLabels !== 'undefined') {
                Chart.register(ChartDataLabels);
                Chart.defaults.plugins.datalabels = {
                    anchor: 'end',
                    align: 'start',
                    color: '#ffffff',
                    font: {
                        family: "'Inter', sans-serif",
                        weight: 'bold',
                        size: 10
                    },
                    offset: 3,
                    clamp: true,
                    textStrokeColor: 'rgba(0, 0, 0, 0.45)',
                    textStrokeWidth: 2,
                    formatter: function(value) {
                        return (value !== null && value !== undefined && value > 0) ? value : '';
                    },
                    display: function(context) {
                        const val = context.dataset.data[context.dataIndex];
                        return val !== null && val !== undefined && val > 0;
                    }
                };
            }

            // 1. Chart Seminar P1 (Horizontal Bar)
            const rawP1 = @json($chartSeminarP1);
            const ctxP1 = document.getElementById('chartSeminarP1').getContext('2d');
            window.sibimaCharts['chartSeminarP1'] = new Chart(ctxP1, {
                type: 'bar',
                data: {
                    labels: rawP1.labels,
                    datasets: [
                        {
                            label: 'Sudah Seminar',
                            data: rawP1.done,
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                        },
                        {
                            label: 'Belum Seminar',
                            data: rawP1.pending,
                            backgroundColor: '#f59e0b',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: { stacked: true, grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } },
                        y: { stacked: true, grid: { display: false }, ticks: { color: theme.textColor, font: { size: 10 } } }
                    }
                }
            });

            // 2. Chart Seminar P2 (Horizontal Bar)
            const rawP2 = @json($chartSeminarP2);
            const ctxP2 = document.getElementById('chartSeminarP2').getContext('2d');
            window.sibimaCharts['chartSeminarP2'] = new Chart(ctxP2, {
                type: 'bar',
                data: {
                    labels: rawP2.labels,
                    datasets: [
                        {
                            label: 'Sudah Seminar',
                            data: rawP2.done,
                            backgroundColor: '#06b6d4',
                            borderRadius: 6,
                        },
                        {
                            label: 'Belum Seminar',
                            data: rawP2.pending,
                            backgroundColor: '#f97316',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: { stacked: true, grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } },
                        y: { stacked: true, grid: { display: false }, ticks: { color: theme.textColor, font: { size: 10 } } }
                    }
                }
            });

            // 3. Chart Cohort Seminar (Stacked Column)
            const rawCohortSem = @json($chartCohortSeminar);
            const ctxCohortSem = document.getElementById('chartCohortSeminar').getContext('2d');
            window.sibimaCharts['chartCohortSeminar'] = new Chart(ctxCohortSem, {
                type: 'bar',
                data: {
                    labels: rawCohortSem.labels,
                    datasets: [
                        {
                            label: 'Sudah Seminar',
                            data: rawCohortSem.done,
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                        },
                        {
                            label: 'Belum Seminar',
                            data: rawCohortSem.pending,
                            backgroundColor: '#f59e0b',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { color: theme.textColor } },
                        y: { stacked: true, grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } }
                    }
                }
            });

            // 4. Chart Unfinished By Advisor (Grouped Horizontal Bar)
            const rawUnfinished = @json($chartUnfinishedByAdvisor);
            const ctxUnfinished = document.getElementById('chartUnfinishedByAdvisor').getContext('2d');
            window.sibimaCharts['chartUnfinishedByAdvisor'] = new Chart(ctxUnfinished, {
                type: 'bar',
                data: {
                    labels: rawUnfinished.labels,
                    datasets: [
                        {
                            label: 'Belum Lulus (P1)',
                            data: rawUnfinished.p1,
                            backgroundColor: '#ef4444',
                            borderRadius: 6,
                        },
                        {
                            label: 'Belum Lulus (P2)',
                            data: rawUnfinished.p2,
                            backgroundColor: '#0ea5e9',
                            borderRadius: 6,
                        },
                        {
                            label: 'Batas Kuota',
                            data: rawUnfinished.quota,
                            backgroundColor: isDark ? 'rgba(148, 163, 184, 0.35)' : 'rgba(148, 163, 184, 0.4)',
                            borderColor: isDark ? '#94a3b8' : '#64748b',
                            borderWidth: 1.5,
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'end',
                            align: 'start',
                            color: function(ctx) {
                                if (ctx.datasetIndex === 2) {
                                    return document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#1e293b';
                                }
                                return '#ffffff';
                            },
                            textStrokeColor: function(ctx) {
                                if (ctx.datasetIndex === 2) {
                                    return document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff';
                                }
                                return 'rgba(0, 0, 0, 0.45)';
                            },
                            textStrokeWidth: 2,
                            font: { weight: 'bold', size: 10 }
                        }
                    },
                    scales: {
                        x: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } },
                        y: { grid: { display: false }, ticks: { color: theme.textColor, font: { size: 10 } } }
                    }
                }
            });

            // 4b. Chart Distribution By Advisor (Stacked Column)
            const rawDistribution = @json($chartDistributionByAdvisor);
            const ctxDistribution = document.getElementById('chartDistributionByAdvisor').getContext('2d');
            window.sibimaCharts['chartDistributionByAdvisor'] = new Chart(ctxDistribution, {
                type: 'bar',
                data: {
                    labels: rawDistribution.labels,
                    datasets: [
                        {
                            label: 'Belum Seminar',
                            data: rawDistribution.belum_seminar,
                            backgroundColor: '#3b82f6',
                            borderRadius: 4,
                        },
                        {
                            label: 'Seminar',
                            data: rawDistribution.seminar,
                            backgroundColor: '#f97316',
                            borderRadius: 4,
                        },
                        {
                            label: 'Sidang Akhir',
                            data: rawDistribution.sidang_akhir,
                            backgroundColor: '#10b981',
                            borderRadius: 4,
                        },
                        {
                            label: 'Kritikal (Sem >= 13)',
                            data: rawDistribution.kritikal,
                            backgroundColor: '#ef4444',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { boxWidth: 12, font: { weight: '600', size: 11 } }
                        },
                        tooltip: {
                            backgroundColor: theme.tooltipBg,
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    return (rawDistribution.full_labels && rawDistribution.full_labels[index])
                                        ? rawDistribution.full_labels[index]
                                        : context[0].label;
                                },
                                afterBody: function(context) {
                                    const index = context[0].dataIndex;
                                    const total = rawDistribution.totals ? rawDistribution.totals[index] : 0;
                                    return 'Total Belum Lulus: ' + total;
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#ffffff',
                            font: { weight: 'bold', size: 10 },
                            textStrokeColor: 'rgba(0, 0, 0, 0.45)',
                            textStrokeWidth: 2,
                            formatter: function(val) {
                                return (val && val > 0) ? val : '';
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: {
                                color: theme.textColor,
                                font: { size: 10, weight: '600' },
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: theme.gridColor },
                            ticks: { color: theme.textColor, stepSize: 1 }
                        }
                    }
                }
            });

            // 5. Chart Stages (Donut)
            const rawStages = @json($chartStages);
            const ctxStages = document.getElementById('chartStages').getContext('2d');
            window.sibimaCharts['chartStages'] = new Chart(ctxStages, {
                type: 'doughnut',
                data: {
                    labels: rawStages.labels,
                    datasets: [{
                        data: rawStages.data,
                        backgroundColor: [
                            '#f97316', // Judul
                            '#3b82f6', // Bimbingan Bab 1-3
                            '#eab308', // ACC Sempro
                            '#8b5cf6', // Bimbingan Bab 4-5
                            '#06b6d4', // ACC Sidang
                            '#10b981', // Lulus
                        ],
                        borderWidth: isDark ? 2 : 1,
                        borderColor: isDark ? '#1e293b' : '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: '500' } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#ffffff',
                            font: { weight: 'bold', size: 11 },
                            textStrokeColor: 'rgba(0, 0, 0, 0.45)',
                            textStrokeWidth: 2
                        }
                    }
                }
            });

            // 6. Chart Cohort Defense (Grouped Column)
            const rawCohortDef = @json($chartCohortDefense);
            const ctxCohortDef = document.getElementById('chartCohortDefense').getContext('2d');
            window.sibimaCharts['chartCohortDefense'] = new Chart(ctxCohortDef, {
                type: 'bar',
                data: {
                    labels: rawCohortDef.labels,
                    datasets: [
                        {
                            label: 'Sudah Sidang / Lulus',
                            data: rawCohortDef.done,
                            backgroundColor: '#6366f1',
                            borderRadius: 6,
                        },
                        {
                            label: 'Belum Sidang',
                            data: rawCohortDef.pending,
                            backgroundColor: '#f43f5e',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: theme.textColor } },
                        y: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } }
                    }
                }
            });

            // 7. Chart Monthly Trends (Smooth Area Line)
            const rawMonthly = @json($chartMonthlyTrends);
            const ctxMonthly = document.getElementById('chartMonthlyTrends').getContext('2d');
            const gradientOrange = ctxMonthly.createLinearGradient(0, 0, 0, 280);
            gradientOrange.addColorStop(0, 'rgba(249, 115, 22, 0.4)');
            gradientOrange.addColorStop(1, 'rgba(249, 115, 22, 0.0)');

            window.sibimaCharts['chartMonthlyTrends'] = new Chart(ctxMonthly, {
                type: 'line',
                data: {
                    labels: rawMonthly.labels,
                    datasets: [
                        {
                            label: 'Sesi Selesai (Hadir)',
                            data: rawMonthly.completed,
                            borderColor: '#ea580c',
                            backgroundColor: gradientOrange,
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#ea580c',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Sesi Pending / Izin / Lainnya',
                            data: rawMonthly.other,
                            borderColor: '#94a3b8',
                            backgroundColor: 'transparent',
                            borderDash: [5, 5],
                            tension: 0.35,
                            borderWidth: 2,
                            pointBackgroundColor: '#94a3b8',
                            pointRadius: 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            offset: 4,
                            color: isDark ? '#f1f5f9' : '#1e293b',
                            textStrokeColor: isDark ? '#0f172a' : '#ffffff',
                            textStrokeWidth: 2,
                            font: { weight: 'bold', size: 10 }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: theme.textColor } },
                        y: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } }
                    }
                }
            });

            // 8. Chart Health (Donut)
            const rawHealth = @json($chartHealth);
            const ctxHealth = document.getElementById('chartHealth').getContext('2d');
            window.sibimaCharts['chartHealth'] = new Chart(ctxHealth, {
                type: 'doughnut',
                data: {
                    labels: rawHealth.labels,
                    datasets: [{
                        data: rawHealth.data,
                        backgroundColor: [
                            '#10b981', // Aktif
                            '#f59e0b', // Waspada
                            '#ef4444', // Kritis
                        ],
                        borderWidth: isDark ? 2 : 1,
                        borderColor: isDark ? '#1e293b' : '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: '500' } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#ffffff',
                            font: { weight: 'bold', size: 11 },
                            textStrokeColor: 'rgba(0, 0, 0, 0.45)',
                            textStrokeWidth: 2
                        }
                    }
                }
            });

            // 9. Chart Workload (Horizontal Bar)
            const rawWorkload = @json($chartWorkload);
            const ctxWorkload = document.getElementById('chartWorkload').getContext('2d');
            window.sibimaCharts['chartWorkload'] = new Chart(ctxWorkload, {
                type: 'bar',
                data: {
                    labels: rawWorkload.labels,
                    datasets: [
                        {
                            label: 'Mahasiswa Bimbingan Aktif',
                            data: rawWorkload.active,
                            backgroundColor: '#3b82f6',
                            borderRadius: 6,
                        },
                        {
                            label: 'Batas Kuota',
                            data: rawWorkload.quota,
                            backgroundColor: isDark ? 'rgba(148, 163, 184, 0.35)' : 'rgba(148, 163, 184, 0.4)',
                            borderColor: isDark ? '#94a3b8' : '#64748b',
                            borderWidth: 1.5,
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'end',
                            align: 'start',
                            color: function(ctx) {
                                if (ctx.datasetIndex === 1) {
                                    return document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#1e293b';
                                }
                                return '#ffffff';
                            },
                            textStrokeColor: function(ctx) {
                                if (ctx.datasetIndex === 1) {
                                    return document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff';
                                }
                                return 'rgba(0, 0, 0, 0.45)';
                            },
                            textStrokeWidth: 2,
                            font: { weight: 'bold', size: 10 }
                        }
                    },
                    scales: {
                        x: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } },
                        y: { grid: { display: false }, ticks: { color: theme.textColor, font: { size: 10 } } }
                    }
                }
            });

            // 10. Chart Scores (Bar)
            const rawScores = @json($chartScores);
            const ctxScores = document.getElementById('chartScores').getContext('2d');
            window.sibimaCharts['chartScores'] = new Chart(ctxScores, {
                type: 'bar',
                data: {
                    labels: rawScores.labels,
                    datasets: [{
                        label: 'Jumlah Mahasiswa',
                        data: rawScores.data,
                        backgroundColor: [
                            '#10b981', // A
                            '#3b82f6', // B
                            '#f59e0b', // C
                            '#ea580c', // D
                            '#ef4444', // E
                        ],
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: theme.textColor } },
                        y: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } }
                    }
                }
            });

            // 11. Chart Topics (Polar Area)
            const rawTopics = @json($chartTopics);
            const ctxTopics = document.getElementById('chartTopics').getContext('2d');
            window.sibimaCharts['chartTopics'] = new Chart(ctxTopics, {
                type: 'polarArea',
                data: {
                    labels: rawTopics.labels,
                    datasets: [{
                        data: rawTopics.data,
                        backgroundColor: [
                            'rgba(249, 115, 22, 0.75)',
                            'rgba(99, 102, 241, 0.75)',
                            'rgba(16, 185, 129, 0.75)',
                            'rgba(236, 72, 153, 0.75)',
                            'rgba(14, 165, 233, 0.75)',
                            'rgba(168, 85, 247, 0.75)',
                        ],
                        borderWidth: isDark ? 2 : 1,
                        borderColor: isDark ? '#1e293b' : '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#ffffff',
                            font: { weight: 'bold', size: 11 },
                            textStrokeColor: 'rgba(0, 0, 0, 0.45)',
                            textStrokeWidth: 2
                        }
                    },
                    scales: {
                        r: {
                            grid: { color: theme.gridColor },
                            ticks: { display: false }
                        }
                    }
                }
            });

            // 12. Chart Wave Duration (Bar)
            const rawWave = @json($chartWaveDuration);
            const ctxWave = document.getElementById('chartWaveDuration').getContext('2d');
            window.sibimaCharts['chartWaveDuration'] = new Chart(ctxWave, {
                type: 'bar',
                data: {
                    labels: rawWave.labels,
                    datasets: [{
                        label: 'Durasi Rata-rata (Bulan)',
                        data: rawWave.data,
                        backgroundColor: '#0d9488',
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'end',
                            align: 'start',
                            formatter: function(val) {
                                return (val && val > 0) ? val + ' bln' : '';
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: theme.textColor } },
                        y: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor } }
                    }
                }
            });

            // Listen for Dark Mode toggle via MutationObserver on <html> class
            const htmlEl = document.documentElement;
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.attributeName === 'class') {
                        const newIsDark = htmlEl.classList.contains('dark');
                        applyThemeToAllCharts(newIsDark);
                    }
                });
            });
            observer.observe(htmlEl, { attributes: true });
        });

        // Function to dynamically refresh all Chart.js colors on dark mode toggle
        function applyThemeToAllCharts(isDark) {
            const theme = getChartThemeColors(isDark);
            Chart.defaults.color = theme.textColor;

            // Update Batas Kuota dataset styling on theme change
            if (window.sibimaCharts['chartUnfinishedByAdvisor']) {
                const c = window.sibimaCharts['chartUnfinishedByAdvisor'];
                if (c.data && c.data.datasets && c.data.datasets[2]) {
                    c.data.datasets[2].backgroundColor = isDark ? 'rgba(148, 163, 184, 0.35)' : 'rgba(148, 163, 184, 0.4)';
                    c.data.datasets[2].borderColor = isDark ? '#94a3b8' : '#64748b';
                }
            }

            if (window.sibimaCharts['chartWorkload']) {
                const c = window.sibimaCharts['chartWorkload'];
                if (c.data && c.data.datasets && c.data.datasets[1]) {
                    c.data.datasets[1].backgroundColor = isDark ? 'rgba(148, 163, 184, 0.35)' : 'rgba(148, 163, 184, 0.4)';
                    c.data.datasets[1].borderColor = isDark ? '#94a3b8' : '#64748b';
                }
            }

            // Update border colors for doughnut & polar charts
            ['chartStages', 'chartHealth', 'chartTopics'].forEach(id => {
                if (window.sibimaCharts[id] && window.sibimaCharts[id].data && window.sibimaCharts[id].data.datasets && window.sibimaCharts[id].data.datasets[0]) {
                    window.sibimaCharts[id].data.datasets[0].borderColor = isDark ? '#1e293b' : '#ffffff';
                    window.sibimaCharts[id].data.datasets[0].borderWidth = isDark ? 2 : 1;
                }
            });

            Object.values(window.sibimaCharts).forEach(chart => {
                if (!chart) return;

                if (chart.options.scales) {
                    if (chart.options.scales.x) {
                        if (chart.options.scales.x.ticks) chart.options.scales.x.ticks.color = theme.textColor;
                        if (chart.options.scales.x.grid) chart.options.scales.x.grid.color = theme.gridColor;
                    }
                    if (chart.options.scales.y) {
                        if (chart.options.scales.y.ticks) chart.options.scales.y.ticks.color = theme.textColor;
                        if (chart.options.scales.y.grid) chart.options.scales.y.grid.color = theme.gridColor;
                    }
                    if (chart.options.scales.r && chart.options.scales.r.grid) {
                        chart.options.scales.r.grid.color = theme.gridColor;
                    }
                }

                if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                    chart.options.plugins.legend.labels.color = theme.textColor;
                }

                if (chart.options.plugins && chart.options.plugins.tooltip) {
                    chart.options.plugins.tooltip.backgroundColor = theme.tooltipBg;
                }

                if (chart.options.plugins && chart.options.plugins.datalabels) {
                    if (chart.canvas && chart.canvas.id === 'chartMonthlyTrends') {
                        chart.options.plugins.datalabels.color = isDark ? '#f1f5f9' : '#1e293b';
                        chart.options.plugins.datalabels.textStrokeColor = isDark ? '#0f172a' : '#ffffff';
                    }
                }

                chart.update();
            });
        }

        // Function to download a single chart as high-resolution PNG with solid background
        function downloadChartAsPng(canvasId, fileName) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            const isDark = document.documentElement.classList.contains('dark');
            const bgColor = isDark ? '#1e293b' : '#ffffff';

            // Create temporary canvas to paint solid background before saving
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = canvas.width;
            tempCanvas.height = canvas.height;
            const tempCtx = tempCanvas.getContext('2d');

            // Draw solid background
            tempCtx.fillStyle = bgColor;
            tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

            // Draw chart on top
            tempCtx.drawImage(canvas, 0, 0);

            // Trigger download
            const link = document.createElement('a');
            link.download = (fileName || 'grafik-sibima') + '-' + new Date().toISOString().slice(0, 10) + '.png';
            link.href = tempCanvas.toDataURL('image/png', 1.0);
            link.click();
        }

        // Function to download all charts sequentially as PNG
        function downloadAllChartsAsPng() {
            const chartIds = [
                { id: 'chartSeminarP1', name: 'grafik-seminar-pembimbing-1' },
                { id: 'chartSeminarP2', name: 'grafik-seminar-pembimbing-2' },
                { id: 'chartCohortSeminar', name: 'grafik-seminar-per-angkatan' },
                { id: 'chartUnfinishedByAdvisor', name: 'grafik-belum-lulus-per-pembimbing' },
                { id: 'chartDistributionByAdvisor', name: 'grafik-distribusi-bimbingan-per-dosen' },
                { id: 'chartStages', name: 'grafik-tahapan-skripsi' },
                { id: 'chartCohortDefense', name: 'grafik-sidang-per-angkatan' },
                { id: 'chartMonthlyTrends', name: 'grafik-tren-bimbingan-bulanan' },
                { id: 'chartHealth', name: 'grafik-keaktifan-bimbingan' },
                { id: 'chartWorkload', name: 'grafik-beban-kuota-dosen' },
                { id: 'chartScores', name: 'grafik-distribusi-nilai-sidang' },
                { id: 'chartTopics', name: 'grafik-topik-skripsi' },
                { id: 'chartWaveDuration', name: 'grafik-durasi-gelombang' },
            ];

            let delay = 0;
            chartIds.forEach(item => {
                setTimeout(() => {
                    downloadChartAsPng(item.id, item.name);
                }, delay);
                delay += 300; // staggering to allow browser download handling
            });
        }
    </script>
</x-app-layout>

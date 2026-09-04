<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                    Logbook Mahasiswa Bimbingan
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Pantau kemajuan berkas logbook dan aktivitas bimbingan mahasiswa skripsi Anda.
                </p>
            </div>
        </div>
    </x-slot>

    @php
        // Helper untuk menyusun URL dengan mempertahankan parameter filter aktif
        $buildQuery = function(array $overrides = []) use ($status, $roleFilter, $filter, $entryYear, $search) {
            $params = [
                'status' => ($status ?? 'active') !== 'active' ? $status : null,
                'role_filter' => (!empty($roleFilter) && $roleFilter !== 'all') ? $roleFilter : null,
                'filter' => (!empty($filter) && $filter !== 'all') ? $filter : null,
                'entry_year' => (!empty($entryYear) && $entryYear !== 'all') ? $entryYear : null,
                'search' => !empty($search) ? $search : null,
            ];
            foreach ($overrides as $k => $v) {
                if ($v === null || $v === '' || $v === 'all') {
                    unset($params[$k]);
                } else {
                    $params[$k] = $v;
                }
            }
            return route('logbooks.index', $params);
        };

        $hasActiveFilters = (!empty($roleFilter) && $roleFilter !== 'all') 
            || (!empty($filter) && $filter !== 'all') 
            || (!empty($entryYear) && $entryYear !== 'all') 
            || !empty($search);
    @endphp

    <div class="w-full space-y-6">
        <!-- TOP KPI CARDS (Interactive 4-Card Summary Grid for Active Students) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Mahasiswa Bimbingan Aktif -->
            @php
                $isTotalActive = ($status ?? 'active') === 'active' && (empty($filter) || $filter === 'all');
            @endphp
            <a href="{{ $buildQuery(['status' => 'active', 'filter' => 'all']) }}" 
               class="group relative p-5 bg-white dark:bg-slate-800 rounded-2xl border transition-all duration-200 flex flex-col justify-between shadow-xs hover:shadow-md cursor-pointer {{ $isTotalActive ? 'ring-2 ring-blue-500/80 border-blue-500 bg-blue-50/20 dark:bg-blue-950/20 dark:border-blue-500' : 'border-slate-200/80 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-500/40' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Bimbingan Aktif</span>
                            @if($isTotalActive)
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300">Semua</span>
                            @endif
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">{{ $stats['total'] ?? 0 }}</span>
                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">Mahasiswa</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-medium text-slate-500 dark:text-slate-400">
                    <span>{{ $stats['p1'] ?? 0 }} Pemb. 1 · {{ $stats['p2'] ?? 0 }} Pemb. 2</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>

            <!-- 2. Siap Seminar Proposal (UP) -->
            @php
                $isUpActive = ($status ?? 'active') === 'active' && ($filter === 'ready_up');
            @endphp
            <a href="{{ $buildQuery(['status' => 'active', 'filter' => $isUpActive ? 'all' : 'ready_up']) }}" 
               class="group relative p-5 bg-white dark:bg-slate-800 rounded-2xl border transition-all duration-200 flex flex-col justify-between shadow-xs hover:shadow-md cursor-pointer {{ $isUpActive ? 'ring-2 ring-indigo-500/80 border-indigo-500 bg-indigo-50/20 dark:bg-indigo-950/20 dark:border-indigo-500' : 'border-slate-200/80 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-500/40' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Siap Seminar Proposal</span>
                            @if($isUpActive)
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">Aktif</span>
                            @endif
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">{{ $stats['ready_up'] ?? 0 }}</span>
                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">Mahasiswa</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-600/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-medium text-slate-500 dark:text-slate-400">
                    <span>Tuntas &ge; 4 sesi bimbingan</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>

            <!-- 3. Siap Sidang Akhir -->
            @php
                $isSidangActive = ($status ?? 'active') === 'active' && ($filter === 'ready_sidang');
            @endphp
            <a href="{{ $buildQuery(['status' => 'active', 'filter' => $isSidangActive ? 'all' : 'ready_sidang']) }}" 
               class="group relative p-5 bg-white dark:bg-slate-800 rounded-2xl border transition-all duration-200 flex flex-col justify-between shadow-xs hover:shadow-md cursor-pointer {{ $isSidangActive ? 'ring-2 ring-emerald-500/80 border-emerald-500 bg-emerald-50/20 dark:bg-emerald-950/20 dark:border-emerald-500' : 'border-slate-200/80 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-500/40' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Siap Sidang Akhir</span>
                            @if($isSidangActive)
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300">Aktif</span>
                            @endif
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">{{ $stats['ready_sidang'] ?? 0 }}</span>
                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">Mahasiswa</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-600/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-medium text-slate-500 dark:text-slate-400">
                    <span>Tuntas &ge; 8 sesi bimbingan</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>

            <!-- 4. Perlu Perhatian (Pasif / Macet) -->
            @php
                $isStalledActive = ($status ?? 'active') === 'active' && ($filter === 'stalled');
                $stalledCount = $stats['stalled'] ?? 0;
            @endphp
            <a href="{{ $buildQuery(['status' => 'active', 'filter' => $isStalledActive ? 'all' : 'stalled']) }}" 
               class="group relative p-5 bg-white dark:bg-slate-800 rounded-2xl border transition-all duration-200 flex flex-col justify-between shadow-xs hover:shadow-md cursor-pointer {{ $isStalledActive ? 'ring-2 ring-rose-500/80 border-rose-500 bg-rose-50/20 dark:bg-rose-950/20 dark:border-rose-500' : 'border-slate-200/80 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-500/40' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Perlu Perhatian</span>
                            @if($stalledCount > 0)
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                </span>
                            @endif
                            @if($isStalledActive)
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300">Aktif</span>
                            @endif
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl sm:text-3xl font-black {{ $stalledCount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-slate-100' }} tracking-tight">{{ $stalledCount }}</span>
                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">Mahasiswa</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-rose-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-medium text-slate-500 dark:text-slate-400">
                    <span>0 sesi atau &gt;14 hari pasif</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
        </div>

        <!-- STATUS TABS NAVIGATION (Bimbingan Aktif vs Riwayat Lulus) -->
        <div class="flex items-center gap-1 border-b border-slate-200 dark:border-slate-800 overflow-x-auto pb-px custom-scrollbar">
            <a href="{{ $buildQuery(['status' => 'active']) }}" 
               class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'active') === 'active' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/10 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>Bimbingan Aktif ({{ $stats['total'] ?? 0 }})</span>
            </a>
            <a href="{{ $buildQuery(['status' => 'completed']) }}" 
               class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'active') === 'completed' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/10 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Riwayat Lulus / Selesai ({{ $stats['graduated_total'] ?? 0 }})</span>
            </a>
        </div>

        <!-- TAB FILTER CEPAT (Quick Filter Tabs Toolbar) -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 sm:p-5 shadow-xs space-y-4">
            <!-- Header Filter Bar: Title, Dropdown Angkatan, & Reset Button -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-700/60">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            Tab Filter Cepat
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                            Pilah data bimbingan secara instan berdasarkan peran pembimbing, tahapan progres, dan tahun angkatan mahasiswa.
                        </p>
                    </div>
                </div>

                <!-- Controls: Filter Angkatan Dropdown & Reset -->
                <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                    <!-- Dropdown Angkatan -->
                    <form action="{{ route('logbooks.index') }}" method="GET" class="relative inline-flex items-center">
                        @if(($status ?? 'active') !== 'active')
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                        @if(!empty($roleFilter) && $roleFilter !== 'all')
                            <input type="hidden" name="role_filter" value="{{ $roleFilter }}">
                        @endif
                        @if(!empty($filter) && $filter !== 'all')
                            <input type="hidden" name="filter" value="{{ $filter }}">
                        @endif
                        @if(!empty($search))
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            </div>
                            <select name="entry_year" onchange="this.form.submit()" 
                                    class="pl-8 pr-8 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all cursor-pointer shadow-2xs">
                                <option value="all" {{ empty($entryYear) || $entryYear === 'all' ? 'selected' : '' }}>
                                    Semua Angkatan
                                </option>
                                @foreach($availableEntryYears as $year)
                                    <option value="{{ $year }}" {{ ((string)$entryYear === (string)$year) ? 'selected' : '' }}>
                                        Angkatan {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <!-- Reset All Filters -->
                    @if($hasActiveFilters)
                        <a href="{{ route('logbooks.index', array_filter(['status' => ($status ?? 'active') !== 'active' ? $status : null])) }}" 
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 rounded-xl text-xs font-bold transition-all border border-rose-200 dark:border-rose-800/80 shadow-2xs"
                           title="Reset Semua Filter">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Filter Row 1: Filter Peran Dosen -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 text-xs">
                <span class="font-bold text-slate-500 dark:text-slate-400 shrink-0 sm:w-28 flex items-center gap-1.5">
                    <span>Filter Peran:</span>
                </span>
                <div class="flex items-center gap-1.5 flex-wrap">
                    @php
                        $isRoleAll = empty($roleFilter) || $roleFilter === 'all';
                        $isRoleP1 = ($roleFilter === 'p1');
                        $isRoleP2 = ($roleFilter === 'p2');
                    @endphp
                    <!-- Semua Peran -->
                    <a href="{{ $buildQuery(['role_filter' => 'all']) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold transition-all shadow-2xs {{ $isRoleAll ? 'bg-orange-500 text-white shadow-orange-500/20' : 'bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-900/70 dark:hover:bg-slate-700/60 text-slate-600 dark:text-slate-300' }}">
                        <span>Semua</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ $isRoleAll ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                            {{ $stats['total'] ?? 0 }}
                        </span>
                    </a>

                    <!-- Sebagai Pembimbing 1 -->
                    <a href="{{ $buildQuery(['role_filter' => 'p1']) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold transition-all shadow-2xs {{ $isRoleP1 ? 'bg-indigo-600 text-white shadow-indigo-600/20' : 'bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-900/70 dark:hover:bg-slate-700/60 text-slate-600 dark:text-slate-300' }}">
                        <span>Sebagai Pembimbing 1</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ $isRoleP1 ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                            {{ $stats['p1'] ?? 0 }}
                        </span>
                    </a>

                    <!-- Sebagai Pembimbing 2 -->
                    <a href="{{ $buildQuery(['role_filter' => 'p2']) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold transition-all shadow-2xs {{ $isRoleP2 ? 'bg-purple-600 text-white shadow-purple-600/20' : 'bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-900/70 dark:hover:bg-slate-700/60 text-slate-600 dark:text-slate-300' }}">
                        <span>Sebagai Pembimbing 2</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ $isRoleP2 ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                            {{ $stats['p2'] ?? 0 }}
                        </span>
                    </a>
                </div>
            </div>

            <!-- Filter Row 2: Filter Kategori Progres (Hanya aktif untuk bimbingan aktif) -->
            @if(($status ?? 'active') === 'active')
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 text-xs pt-1">
                    <span class="font-bold text-slate-500 dark:text-slate-400 shrink-0 sm:w-28 flex items-center gap-1.5">
                        <span>Kategori Progres:</span>
                    </span>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        @php
                            $isProgAll = empty($filter) || $filter === 'all';
                            $isProgProposal = ($filter === 'proposal');
                            $isProgUp = ($filter === 'ready_up');
                            $isProgSidang = ($filter === 'ready_sidang');
                            $isProgStalled = ($filter === 'stalled');
                        @endphp

                        <!-- Semua Kategori -->
                        <a href="{{ $buildQuery(['filter' => 'all']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold transition-all shadow-2xs {{ $isProgAll ? 'bg-orange-500 text-white shadow-orange-500/20' : 'bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-900/70 dark:hover:bg-slate-700/60 text-slate-600 dark:text-slate-300' }}">
                            <span>Semua</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ $isProgAll ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                {{ $stats['total'] ?? 0 }}
                            </span>
                        </a>

                        <!-- Tahap Proposal (< 4 sesi) -->
                        <a href="{{ $buildQuery(['filter' => 'proposal']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold transition-all shadow-2xs {{ $isProgProposal ? 'bg-sky-600 text-white shadow-sky-600/20' : 'bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-900/70 dark:hover:bg-slate-700/60 text-slate-600 dark:text-slate-300' }}">
                            <span>Tahap Proposal (&lt; 4 sesi)</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ $isProgProposal ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                {{ $stats['proposal'] ?? 0 }}
                            </span>
                        </a>

                        <!-- Siap UP (≥ 4 sesi) -->
                        <a href="{{ $buildQuery(['filter' => 'ready_up']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold transition-all shadow-2xs {{ $isProgUp ? 'bg-indigo-600 text-white shadow-indigo-600/20' : 'bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-900/70 dark:hover:bg-slate-700/60 text-slate-600 dark:text-slate-300' }}">
                            <span>Siap UP (&ge; 4 sesi)</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ $isProgUp ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                {{ $stats['ready_up'] ?? 0 }}
                            </span>
                        </a>

                        <!-- Siap Sidang (≥ 8 sesi) -->
                        <a href="{{ $buildQuery(['filter' => 'ready_sidang']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold transition-all shadow-2xs {{ $isProgSidang ? 'bg-emerald-600 text-white shadow-emerald-600/20' : 'bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-900/70 dark:hover:bg-slate-700/60 text-slate-600 dark:text-slate-300' }}">
                            <span>Siap Sidang (&ge; 8 sesi)</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ $isProgSidang ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                {{ $stats['ready_sidang'] ?? 0 }}
                            </span>
                        </a>

                        <!-- Macet (> 14 hari) -->
                        @php
                            $stalledCount = $stats['stalled'] ?? 0;
                        @endphp
                        <a href="{{ $buildQuery(['filter' => 'stalled']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold transition-all shadow-2xs {{ $isProgStalled ? 'bg-rose-600 text-white shadow-rose-600/20' : ($stalledCount > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200/80 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900/60' : 'bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-900/70 dark:hover:bg-slate-700/60 text-slate-600 dark:text-slate-300') }}">
                            @if($stalledCount > 0 && !$isProgStalled)
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                            @endif
                            <span>Macet (&gt; 14 hari)</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ $isProgStalled ? 'bg-white/20 text-white' : ($stalledCount > 0 ? 'bg-rose-200/80 dark:bg-rose-900/80 text-rose-800 dark:text-rose-200' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300') }}">
                                {{ $stalledCount }}
                            </span>
                        </a>
                    </div>
                </div>
            @else
                <div class="text-[11px] text-slate-500 dark:text-slate-400 italic pt-1">
                    * Kategori progres bimbingan (Proposal, Siap UP, Siap Sidang, Macet) hanya berlaku untuk tab Mahasiswa Bimbingan Aktif.
                </div>
            @endif
        </div>

        <!-- ACTIVE FILTER TAGS BAR (Shown when any filter or search is active) -->
        @if($hasActiveFilters)
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 bg-orange-50/70 dark:bg-orange-950/30 border border-orange-200/80 dark:border-orange-800/60 rounded-2xl text-xs">
                <div class="flex items-center gap-2 flex-wrap text-orange-900 dark:text-orange-200 font-medium">
                    <span class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filter Aktif ({{ $theses->total() }} Mahasiswa):</span>
                    </span>

                    <!-- Role Filter Tag -->
                    @if(!empty($roleFilter) && $roleFilter !== 'all')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-orange-200 dark:border-orange-800 text-orange-800 dark:text-orange-200 font-bold shadow-2xs">
                            <span>Peran: {{ $roleFilter === 'p1' ? 'Pembimbing 1' : 'Pembimbing 2' }}</span>
                            <a href="{{ $buildQuery(['role_filter' => 'all']) }}" class="text-orange-500 hover:text-orange-700 dark:hover:text-orange-300 font-black text-sm leading-none" title="Hapus filter peran">&times;</a>
                        </span>
                    @endif

                    <!-- Progress Category Tag -->
                    @if(!empty($filter) && $filter !== 'all')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-orange-200 dark:border-orange-800 text-orange-800 dark:text-orange-200 font-bold shadow-2xs">
                            <span>Progres: 
                                @if($filter === 'proposal') Tahap Proposal (&lt; 4 Sesi)
                                @elseif($filter === 'ready_up') Siap UP (&ge; 4 Sesi)
                                @elseif($filter === 'ready_sidang') Siap Sidang (&ge; 8 Sesi)
                                @elseif($filter === 'stalled') Macet (&gt; 14 Hari)
                                @else {{ ucfirst($filter) }}
                                @endif
                            </span>
                            <a href="{{ $buildQuery(['filter' => 'all']) }}" class="text-orange-500 hover:text-orange-700 dark:hover:text-orange-300 font-black text-sm leading-none" title="Hapus filter kategori progres">&times;</a>
                        </span>
                    @endif

                    <!-- Entry Year Tag -->
                    @if(!empty($entryYear) && $entryYear !== 'all')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-orange-200 dark:border-orange-800 text-orange-800 dark:text-orange-200 font-bold shadow-2xs">
                            <span>Angkatan: {{ $entryYear }}</span>
                            <a href="{{ $buildQuery(['entry_year' => 'all']) }}" class="text-orange-500 hover:text-orange-700 dark:hover:text-orange-300 font-black text-sm leading-none" title="Hapus filter angkatan">&times;</a>
                        </span>
                    @endif

                    <!-- Search Tag -->
                    @if(!empty($search))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-orange-200 dark:border-orange-800 text-orange-800 dark:text-orange-200 font-bold shadow-2xs">
                            <span>Pencarian: "{{ $search }}"</span>
                            <a href="{{ $buildQuery(['search' => null]) }}" class="text-orange-500 hover:text-orange-700 dark:hover:text-orange-300 font-black text-sm leading-none" title="Hapus pencarian">&times;</a>
                        </span>
                    @endif
                </div>

                <a href="{{ route('logbooks.index', array_filter(['status' => ($status ?? 'active') !== 'active' ? $status : null])) }}" 
                   class="inline-flex items-center gap-1 px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-orange-100 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-300 font-bold rounded-xl border border-orange-200 dark:border-orange-700 transition-colors shadow-2xs">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Reset Semua</span>
                </a>
            </div>
        @endif

        <!-- MAIN TABLE CARD -->
        <x-table-card 
            title="{{ ($status ?? 'active') === 'completed' ? 'Riwayat Mahasiswa Lulus / Selesai' : 'Daftar Mahasiswa Bimbingan Aktif' }}"
            subtitle="{{ ($status ?? 'active') === 'completed' ? 'Arsip berkas logbook mahasiswa bimbingan yang telah dinyatakan lulus skripsi.' : 'Pilih mahasiswa untuk memantau berkas dan aktivitas bimbingan mereka.' }}"
            :footer="$theses->hasPages() ? $theses->links() : null">
            
            <x-slot name="headerActions">
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input Form -->
                    <form action="{{ route('logbooks.index') }}" method="GET" class="relative w-full sm:w-72">
                        @if(($status ?? 'active') !== 'active')
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                        @if(!empty($roleFilter) && $roleFilter !== 'all')
                            <input type="hidden" name="role_filter" value="{{ $roleFilter }}">
                        @endif
                        @if(!empty($filter) && $filter !== 'all')
                            <input type="hidden" name="filter" value="{{ $filter }}">
                        @endif
                        @if(!empty($entryYear) && $entryYear !== 'all')
                            <input type="hidden" name="entry_year" value="{{ $entryYear }}">
                        @endif
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               placeholder="Cari nama, NPM, atau judul..." 
                               class="block w-full pl-10 pr-9 py-2 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs sm:text-sm transition-all shadow-2xs">
                        @if(isset($search) && $search !== '')
                            <a href="{{ $buildQuery(['search' => null]) }}" 
                               class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                               title="Hapus pencarian">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </form>
                </div>
            </x-slot>

            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/50">
                        <th class="py-3.5 px-6 font-semibold text-xs tracking-wider">MAHASISWA</th>
                        <th class="py-3.5 px-6 font-semibold text-xs tracking-wider">RENCANA JUDUL SKRIPSI</th>
                        <th class="py-3.5 px-6 font-semibold text-xs tracking-wider text-center">PROGRES TARGET BIMBINGAN</th>
                        <th class="py-3.5 px-6 font-semibold text-xs tracking-wider text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                    @forelse($theses as $thesis)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3.5">
                                    <div class="relative w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0 shadow-2xs">
                                        <img src="{{ $thesis->student->avatar_url }}" alt="{{ $thesis->student->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="space-y-1">
                                        <!-- Student Name & WhatsApp Shortcut -->
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight text-xs">{{ $thesis->student->name }}</span>
                                            
                                            @php
                                                $waNumber = \App\Helpers\PhoneHelper::formatForWhatsApp($thesis->student->phone_number);
                                                $dosenName = Auth::user()->name;
                                                $roleLabel = ($thesis->pembimbing1_id === Auth::id()) ? 'Pembimbing 1' : (($thesis->pembimbing2_id === Auth::id()) ? 'Pembimbing 2' : 'Dosen');
                                                $waMessage = urlencode("Halo {$thesis->student->name}, saya {$dosenName} ({$roleLabel}). Terkait logbook bimbingan skripsi: \"" . ($thesis->final_title ?? $thesis->title) . "\"");
                                            @endphp

                                            @if($waNumber)
                                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-md text-[10px] font-bold transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer"
                                                   title="Kirim Pesan WhatsApp ke {{ $thesis->student->name }} ({{ $thesis->student->phone_number }})">
                                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400 fill-current shrink-0" viewBox="0 0 24 24">
                                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                                    </svg>
                                                    <span>Chat WA</span>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 rounded text-[9px] font-medium" title="Nomor WhatsApp belum didaftarkan oleh mahasiswa">
                                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                    <span>No WA -</span>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold font-mono tracking-wider">{{ $thesis->student->identifier ?? 'NPM -' }}</span>
                                            @if($thesis->student->entry_year)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                    Angkatan {{ $thesis->student->entry_year }}
                                                </span>
                                            @endif
                                            @if($thesis->pembimbing1_id === Auth::id())
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 shadow-2xs">
                                                    Pembimbing 1
                                                </span>
                                            @elseif($thesis->pembimbing2_id === Auth::id())
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 shadow-2xs">
                                                    Pembimbing 2
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 max-w-sm whitespace-normal">
                                <div class="font-medium text-slate-700 dark:text-slate-300 line-clamp-2" title="{{ $thesis->final_title ?? $thesis->title }}">
                                    {{ $thesis->final_title ?? $thesis->title }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $completedCount = (int) $thesis->completed_sessions_count;
                                    $isP1 = ($thesis->pembimbing1_id === Auth::id());
                                    $hasMyAccUp = $isP1 ? (bool) $thesis->acc_up_p1 : (bool) $thesis->acc_up_p2;
                                    $hasMyAccSidang = $isP1 ? (bool) $thesis->acc_sidang_p1 : (bool) $thesis->acc_sidang_p2;
                                    $isAccUpFinal = $thesis->acc_up_p1 && $thesis->acc_up_p2;
                                    $isAccSidangFinal = $thesis->acc_sidang_p1 && $thesis->acc_sidang_p2;

                                    $isGraduated = ($thesis->status === 'completed');

                                    $upTarget = 4;
                                    $sidangTarget = 8;
                                    $upPercent = $isGraduated ? 100 : min(100, round(($completedCount / $upTarget) * 100));
                                    $sidangPercent = $isGraduated ? 100 : min(100, round(($completedCount / $sidangTarget) * 100));

                                    $lastSession = $thesis->mentoringSessions ? $thesis->mentoringSessions->first() : null;
                                    $daysSinceLast = ($lastSession && $lastSession->scheduled_at) 
                                        ? (int) abs(now()->diffInDays($lastSession->scheduled_at)) 
                                        : ($thesis->created_at ? (int) abs(now()->diffInDays($thesis->created_at)) : null);
                                @endphp

                                <div class="inline-flex flex-col gap-2 min-w-[200px] max-w-[240px] text-left">
                                    <!-- Target UP (4 Sesi) Progress Bar -->
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between text-[10px] font-bold">
                                            <span class="text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $upPercent >= 100 || $hasMyAccUp ? 'bg-indigo-500' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                                <span>Target UP</span>
                                            </span>
                                            <div class="flex items-center gap-1.5">
                                                @if($hasMyAccUp || $isAccUpFinal || $isGraduated)
                                                    <span class="inline-flex items-center gap-0.5 text-[9px] font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded border border-emerald-200 dark:border-emerald-800" title="Sudah di-ACC Seminar Proposal">
                                                        <svg class="w-2.5 h-2.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        <span>ACC UP</span>
                                                    </span>
                                                @endif
                                                <span class="font-mono text-[10px] {{ $completedCount >= 4 || $isGraduated ? 'text-indigo-600 dark:text-indigo-400 font-black' : 'text-slate-500 dark:text-slate-400' }}">
                                                    {{ $isGraduated ? '4/4' : min($completedCount, 4) . '/4' }} Sesi
                                                </span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-700/70 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-500 {{ $upPercent >= 100 ? 'bg-indigo-600 dark:bg-indigo-500' : ($upPercent > 0 ? 'bg-indigo-400 dark:bg-indigo-400' : 'bg-transparent') }}" 
                                                 style="width: {{ $upPercent }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Target Sidang (8 Sesi) Progress Bar -->
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between text-[10px] font-bold">
                                            <span class="text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $sidangPercent >= 100 || $hasMyAccSidang ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                                <span>Target Sidang</span>
                                            </span>
                                            <div class="flex items-center gap-1.5">
                                                @if($hasMyAccSidang || $isAccSidangFinal || $isGraduated)
                                                    <span class="inline-flex items-center gap-0.5 text-[9px] font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded border border-emerald-200 dark:border-emerald-800" title="Sudah di-ACC Sidang Akhir">
                                                        <svg class="w-2.5 h-2.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        <span>ACC Sidang</span>
                                                    </span>
                                                @endif
                                                <span class="font-mono text-[10px] {{ $completedCount >= 8 || $isGraduated ? 'text-emerald-600 dark:text-emerald-400 font-black' : 'text-slate-500 dark:text-slate-400' }}">
                                                    {{ $isGraduated ? '8/8' : min($completedCount, 8) . '/8' }} Sesi
                                                </span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-700/70 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-500 {{ $sidangPercent >= 100 ? 'bg-emerald-600 dark:bg-emerald-500' : ($sidangPercent > 0 ? 'bg-emerald-400 dark:bg-emerald-400' : 'bg-transparent') }}" 
                                                 style="width: {{ $sidangPercent }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Bottom Milestone Status Pill -->
                                    <div class="flex items-center justify-between pt-0.5">
                                        <div class="flex items-center gap-1">
                                            <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500">Total:</span>
                                            <span class="text-[10px] font-black {{ $completedCount > 0 ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400' }}">{{ $completedCount }} Sesi</span>
                                        </div>

                                        <div>
                                            @if($isGraduated)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.2 rounded text-[9px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800">
                                                    <span>🎓 Lulus</span>
                                                </span>
                                            @elseif($hasMyAccSidang)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.2 rounded text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800">
                                                    <span>✅ ACC Sidang</span>
                                                </span>
                                            @elseif($completedCount >= 8)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.2 rounded text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800">
                                                    <span>🟢 Siap Sidang</span>
                                                </span>
                                            @elseif($hasMyAccUp)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.2 rounded text-[9px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800">
                                                    <span>✅ ACC UP</span>
                                                </span>
                                            @elseif($completedCount >= 4)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.2 rounded text-[9px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800">
                                                    <span>🔵 Siap UP</span>
                                                </span>
                                            @elseif($completedCount === 0)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.2 rounded text-[9px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800">
                                                    <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                                    <span>Belum Ada Sesi</span>
                                                </span>
                                            @elseif($daysSinceLast !== null && $daysSinceLast > 14)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.2 rounded text-[9px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800">
                                                    <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                                    <span>Pasif ({{ $daysSinceLast }}h)</span>
                                                </span>
                                            @else
                                                <span class="text-[9px] font-medium text-slate-400 dark:text-slate-500">
                                                    {{ $daysSinceLast !== null ? ($daysSinceLast == 0 ? 'Hari ini' : "{$daysSinceLast}h lalu") : 'Aktif' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('theses.logbooks', $thesis->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300 dark:hover:bg-orange-950/40 dark:hover:text-orange-400 dark:hover:border-orange-800 rounded-xl text-xs font-bold transition-all shadow-2xs hover:shadow-xs group/btn">
                                    <span>Lihat Logbook</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover/btn:text-orange-500 group-hover/btn:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                                    <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100">Tidak ada mahasiswa yang sesuai</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest font-black">
                                    @if(($status ?? 'active') === 'completed')
                                        Belum ada riwayat mahasiswa bimbingan yang lulus
                                    @elseif($hasActiveFilters)
                                        Tidak ditemukan mahasiswa pada kombinasi filter aktif ini
                                    @else
                                        Data akan muncul setelah Anda ditugaskan sebagai pembimbing
                                    @endif
                                </p>
                                @if(($status ?? 'active') === 'completed' || $hasActiveFilters)
                                    <div class="mt-4">
                                        <a href="{{ route('logbooks.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                            <span>Kembali ke Bimbingan Aktif (Reset Filter)</span>
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>
</x-app-layout>

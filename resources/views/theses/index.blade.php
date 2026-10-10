<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => Auth::user()->role === 'dosen' ? 'Daftar Mahasiswa Bimbingan' : 'Daftar Pengajuan Skripsi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6" x-data="{ 
        pageLoading: true,
        rollbackModalOpen: false,
        rollbackData: { id: null, student: '', p1: '', p2: '' },
        openRollbackModal(id, student, p1, p2) {
            this.rollbackData = { id: id, student: student, p1: p1, p2: p2 };
            this.rollbackModalOpen = true;
        }
    }" x-init="$nextTick(() => { setTimeout(() => pageLoading = false, 150) })">
    @php
        $filter = $filter ?? 'all';
        $stats = $stats ?? null;
        $dosenStats = $dosenStats ?? [];
        // Helper untuk menyusun URL dengan mempertahankan parameter filter aktif
        $buildQuery = function(array $overrides = []) use ($status, $roleFilter, $filter, $entryYear, $search, $cohortFilter, $mentoringHealth, $accStatus) {
            $defaultStatus = Auth::user()->role === 'dosen' ? 'active' : 'all';
            $params = [
                'status' => ($status ?? $defaultStatus) !== $defaultStatus ? $status : null,
                'role_filter' => (!empty($roleFilter) && $roleFilter !== 'all') ? $roleFilter : null,
                'filter' => (!empty($filter) && $filter !== 'all') ? $filter : null,
                'cohort_filter' => (!empty($cohortFilter) && $cohortFilter !== 'all') ? $cohortFilter : null,
                'entry_year' => (!empty($entryYear) && $entryYear !== 'all') ? $entryYear : null,
                'mentoring_health' => (!empty($mentoringHealth) && $mentoringHealth !== 'all') ? $mentoringHealth : null,
                'acc_status' => (!empty($accStatus) && $accStatus !== 'all') ? $accStatus : null,
                'search' => !empty($search) ? $search : null,
            ];
            foreach ($overrides as $k => $v) {
                if ($v === null || $v === '' || $v === 'all') {
                    unset($params[$k]);
                } else {
                    $params[$k] = $v;
                }
            }
            return route('theses.index', $params);
        };

        $hasActiveDosenFilters = (!empty($roleFilter) && $roleFilter !== 'all') 
            || (!empty($filter) && $filter !== 'all') 
            || (!empty($entryYear) && $entryYear !== 'all') 
            || (($cohortFilter ?? 'all') !== 'all')
            || (($mentoringHealth ?? 'all') !== 'all')
            || (($accStatus ?? 'all') !== 'all')
            || !empty($search);
    @endphp

        <!-- Status Tabs Navigation -->
        <div class="flex items-center gap-1 border-b border-slate-100 dark:border-slate-800 overflow-x-auto pb-px custom-scrollbar">
            @if(Auth::user()->role === 'dosen')
                <a href="{{ $buildQuery(['status' => 'active']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'active') === 'active' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Mahasiswa Bimbingan Aktif ({{ $stats['total'] ?? $dosenStats['total'] ?? 0 }})
                </a>
                <a href="{{ $buildQuery(['status' => 'completed']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'active') === 'completed' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Mahasiswa Bimbingan (Lulus) ({{ $stats['graduated_total'] ?? 0 }})
                </a>
            @else
                <a href="{{ route('theses.index', ['status' => 'all', 'search' => $search, 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'all' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Semua Pengajuan
                </a>
                <a href="{{ route('theses.index', ['status' => 'pending', 'search' => $search, 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'pending' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Menunggu Pembimbing
                </a>
                <a href="{{ route('theses.index', ['status' => 'active', 'search' => $search, 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'active' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Aktif / Berjalan
                </a>
                <a href="{{ route('theses.index', ['status' => 'completed', 'search' => $search, 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'completed' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Riwayat Lulus
                </a>
                <a href="{{ route('theses.unsubmitted-students') }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 border-transparent text-amber-600 dark:text-amber-400 hover:text-amber-700 hover:bg-amber-50/50 dark:hover:bg-amber-900/10">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Belum Mengajukan Judul
                </a>
            @endif
        </div>

        @if(Auth::user()->role === 'dosen')
            <!-- TAB FILTER CEPAT (Quick Filter Dropdowns Toolbar Dosen) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 sm:p-5 shadow-xs relative z-20">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <!-- Left: Title & Subtitle -->
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 border border-orange-200/70 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20 flex items-center justify-center shrink-0 shadow-2xs">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                                    Tab Filter Cepat
                                </h3>
                                @if($hasActiveDosenFilters)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-black bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400 border border-orange-200 dark:border-orange-800/60">
                                        Filter Aktif
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                Pilah data bimbingan secara instan berdasarkan peran pembimbing, tahapan progres, dan tahun angkatan mahasiswa.
                            </p>
                        </div>
                    </div>

                    <!-- Right: Filter Dropdowns Toolbar -->
                    <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                        <!-- 1. Dropdown Filter Peran -->
                        <div class="relative" x-data="{ openRoleDropdown: false }" @click.outside="openRoleDropdown = false">
                            @php
                                $isRoleActive = !empty($roleFilter) && $roleFilter !== 'all';
                                $currentRoleLabel = 'Semua Peran';
                                $currentRoleBadge = $stats['total'] ?? $dosenStats['total'] ?? 0;
                                if ($roleFilter === 'p1') {
                                    $currentRoleLabel = 'Pembimbing 1';
                                    $currentRoleBadge = $stats['p1'] ?? $dosenStats['p1'] ?? 0;
                                } elseif ($roleFilter === 'p2') {
                                    $currentRoleLabel = 'Pembimbing 2';
                                    $currentRoleBadge = $stats['p2'] ?? $dosenStats['p2'] ?? 0;
                                }
                            @endphp
                            <button type="button" 
                                    @click="openRoleDropdown = !openRoleDropdown"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-all border cursor-pointer shadow-2xs {{ $isRoleActive ? 'bg-orange-50/80 dark:bg-orange-950/30 border-orange-300 dark:border-orange-500/50 text-orange-700 dark:text-orange-300 ring-2 ring-orange-500/10' : 'bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700' }}"
                                    aria-haspopup="true"
                                    :aria-expanded="openRoleDropdown">
                                <svg class="w-3.5 h-3.5 {{ $isRoleActive ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                
                                <span>{{ $currentRoleLabel }}</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ $isRoleActive ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                    {{ $currentRoleBadge }}
                                </span>

                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openRoleDropdown ? 'rotate-180 text-orange-600 dark:text-orange-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu Peran -->
                            <div x-show="openRoleDropdown"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                 class="absolute left-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200 dark:border-slate-700 p-1.5 z-50 text-left"
                                 style="display: none;">
                                
                                <div class="px-3 py-2 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                                    <span>Filter Peran</span>
                                    @if($isRoleActive)
                                        <a href="{{ $buildQuery(['role_filter' => 'all']) }}" 
                                           class="text-orange-600 hover:text-orange-700 dark:text-orange-400 font-bold lowercase hover:underline">
                                            Reset
                                        </a>
                                    @endif
                                </div>

                                <div class="py-1 space-y-0.5">
                                    <a href="{{ $buildQuery(['role_filter' => 'all']) }}"
                                       class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ empty($roleFilter) || $roleFilter === 'all' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full {{ empty($roleFilter) || $roleFilter === 'all' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600' }} shrink-0"></span>
                                            <span>Semua Peran</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ empty($roleFilter) || $roleFilter === 'all' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                {{ $stats['total'] ?? $dosenStats['total'] ?? 0 }}
                                            </span>
                                            @if(empty($roleFilter) || $roleFilter === 'all')
                                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </div>
                                    </a>

                                    <a href="{{ $buildQuery(['role_filter' => 'p1']) }}"
                                       class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($roleFilter ?? '') === 'p1' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full {{ ($roleFilter ?? '') === 'p1' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600' }} shrink-0"></span>
                                            <span>Sebagai Pembimbing 1</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($roleFilter ?? '') === 'p1' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                {{ $stats['p1'] ?? $dosenStats['p1'] ?? 0 }}
                                            </span>
                                            @if(($roleFilter ?? '') === 'p1')
                                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </div>
                                    </a>

                                    <a href="{{ $buildQuery(['role_filter' => 'p2']) }}"
                                       class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($roleFilter ?? '') === 'p2' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full {{ ($roleFilter ?? '') === 'p2' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600' }} shrink-0"></span>
                                            <span>Sebagai Pembimbing 2</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($roleFilter ?? '') === 'p2' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                {{ $stats['p2'] ?? $dosenStats['p2'] ?? 0 }}
                                            </span>
                                            @if(($roleFilter ?? '') === 'p2')
                                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Dropdown Filter Kategori Progres -->
                        @if(($status ?? 'active') === 'active')
                            <div class="relative" x-data="{ openProgressDropdown: false }" @click.outside="openProgressDropdown = false">
                                @php
                                    $isProgressActive = !empty($filter) && $filter !== 'all';
                                    $isProgressStalled = ($filter === 'stalled');
                                    $currentProgressLabel = 'Semua Progres';
                                    $currentProgressBadge = $stats['total'] ?? $dosenStats['total'] ?? 0;
                                    if ($filter === 'proposal') {
                                        $currentProgressLabel = 'Tahap Proposal';
                                        $currentProgressBadge = $stats['proposal'] ?? 0;
                                    } elseif ($filter === 'ready_up') {
                                        $currentProgressLabel = 'Siap UP';
                                        $currentProgressBadge = $stats['ready_up'] ?? 0;
                                    } elseif ($filter === 'ready_sidang') {
                                        $currentProgressLabel = 'Siap Sidang';
                                        $currentProgressBadge = $stats['ready_sidang'] ?? 0;
                                    } elseif ($filter === 'stalled') {
                                        $currentProgressLabel = 'Macet (>14 hr)';
                                        $currentProgressBadge = $stats['stalled'] ?? 0;
                                    }
                                @endphp
                                <button type="button" 
                                        @click="openProgressDropdown = !openProgressDropdown"
                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-all border cursor-pointer shadow-2xs {{ $isProgressStalled ? 'bg-rose-50/90 dark:bg-rose-950/40 border-rose-300 dark:border-rose-500/50 text-rose-700 dark:text-rose-300 ring-2 ring-rose-500/10' : ($isProgressActive ? 'bg-orange-50/80 dark:bg-orange-950/30 border-orange-300 dark:border-orange-500/50 text-orange-700 dark:text-orange-300 ring-2 ring-orange-500/10' : 'bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700') }}"
                                        aria-haspopup="true"
                                        :aria-expanded="openProgressDropdown">
                                    <svg class="w-3.5 h-3.5 {{ $isProgressStalled ? 'text-rose-600 dark:text-rose-400' : ($isProgressActive ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500') }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    
                                    @if(($stats['stalled'] ?? 0) > 0 && !$isProgressActive)
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse" title="{{ $stats['stalled'] }} mahasiswa pasif > 14 hari"></span>
                                    @endif

                                    <span>{{ $currentProgressLabel }}</span>
                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ $isProgressStalled ? 'bg-rose-200/80 dark:bg-rose-500/30 text-rose-800 dark:text-rose-200' : ($isProgressActive ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300') }}">
                                        {{ $currentProgressBadge }}
                                    </span>

                                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openProgressDropdown ? 'rotate-180 text-orange-600 dark:text-orange-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Dropdown Menu Kategori Progres -->
                                <div x-show="openProgressDropdown"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                     class="absolute left-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200 dark:border-slate-700 p-1.5 z-50 text-left"
                                     style="display: none;">
                                    
                                    <div class="px-3 py-2 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                                        <span>Kategori Progres</span>
                                        @if($isProgressActive)
                                            <a href="{{ $buildQuery(['filter' => 'all']) }}" 
                                               class="text-orange-600 hover:text-orange-700 dark:text-orange-400 font-bold lowercase hover:underline">
                                                Reset
                                            </a>
                                        @endif
                                    </div>

                                    <div class="py-1 space-y-0.5">
                                        <a href="{{ $buildQuery(['filter' => 'all']) }}"
                                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ empty($filter) || $filter === 'all' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ empty($filter) || $filter === 'all' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600' }} shrink-0"></span>
                                                <span>Semua Progres</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ empty($filter) || $filter === 'all' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                    {{ $stats['total'] ?? $dosenStats['total'] ?? 0 }}
                                                </span>
                                                @if(empty($filter) || $filter === 'all')
                                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                @endif
                                            </div>
                                        </a>

                                        <a href="{{ $buildQuery(['filter' => 'proposal']) }}"
                                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($filter ?? '') === 'proposal' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ ($filter ?? '') === 'proposal' ? 'bg-orange-500' : 'bg-blue-500' }} shrink-0"></span>
                                                <div>
                                                    <span>Tahap Proposal</span>
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">&lt; 8 sesi (belum 4x P1 &amp; 4x P2)</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($filter ?? '') === 'proposal' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                    {{ $stats['proposal'] ?? 0 }}
                                                </span>
                                                @if(($filter ?? '') === 'proposal')
                                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                @endif
                                            </div>
                                        </a>

                                        <a href="{{ $buildQuery(['filter' => 'ready_up']) }}"
                                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($filter ?? '') === 'ready_up' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ ($filter ?? '') === 'ready_up' ? 'bg-orange-500' : 'bg-emerald-500' }} shrink-0"></span>
                                                <div>
                                                    <span>Siap Seminar Proposal (UP)</span>
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">&ge; 8 sesi (4x P1 &amp; 4x P2)</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($filter ?? '') === 'ready_up' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                    {{ $stats['ready_up'] ?? 0 }}
                                                </span>
                                                @if(($filter ?? '') === 'ready_up')
                                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                @endif
                                            </div>
                                        </a>

                                        <a href="{{ $buildQuery(['filter' => 'ready_sidang']) }}"
                                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($filter ?? '') === 'ready_sidang' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ ($filter ?? '') === 'ready_sidang' ? 'bg-orange-500' : 'bg-indigo-500' }} shrink-0"></span>
                                                <div>
                                                    <span>Siap Sidang Skripsi</span>
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">&ge; 16 sesi (8x P1 &amp; 8x P2)</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($filter ?? '') === 'ready_sidang' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                    {{ $stats['ready_sidang'] ?? 0 }}
                                                </span>
                                                @if(($filter ?? '') === 'ready_sidang')
                                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                @endif
                                            </div>
                                        </a>

                                        @php $stalledCount = $stats['stalled'] ?? 0; @endphp
                                        <a href="{{ $buildQuery(['filter' => 'stalled']) }}"
                                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($filter ?? '') === 'stalled' ? 'bg-rose-50 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                                                <div>
                                                    <span class="text-rose-700 dark:text-rose-400">Macet / Pasif</span>
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">&gt; 14 hari tanpa bimbingan</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($filter ?? '') === 'stalled' ? 'bg-rose-200/80 dark:bg-rose-500/30 text-rose-800 dark:text-rose-200' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' }}">
                                                    {{ $stalledCount }}
                                                </span>
                                                @if(($filter ?? '') === 'stalled')
                                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- 3. Dropdown Tahun Angkatan -->
                        <div class="relative" x-data="{ openYearDropdown: false }" @click.outside="openYearDropdown = false">
                            @php $isYearActive = !empty($entryYear) && $entryYear !== 'all'; @endphp
                            <button type="button" 
                                    @click="openYearDropdown = !openYearDropdown"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-all border cursor-pointer shadow-2xs {{ $isYearActive ? 'bg-orange-50/80 dark:bg-orange-950/30 border-orange-300 dark:border-orange-500/50 text-orange-700 dark:text-orange-300 ring-2 ring-orange-500/10' : 'bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700' }}"
                                    aria-haspopup="true"
                                    :aria-expanded="openYearDropdown">
                                <svg class="w-3.5 h-3.5 {{ $isYearActive ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                                
                                <span>{{ $isYearActive ? 'Angkatan ' . $entryYear : 'Semua Angkatan' }}</span>

                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openYearDropdown ? 'rotate-180 text-orange-600 dark:text-orange-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu Angkatan -->
                            <div x-show="openYearDropdown"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                 class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200 dark:border-slate-700 p-1.5 z-50 text-left"
                                 style="display: none;">
                                
                                <div class="px-3 py-2 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                                    <span>Tahun Angkatan</span>
                                    @if($isYearActive)
                                        <a href="{{ $buildQuery(['entry_year' => 'all']) }}" 
                                           class="text-orange-600 hover:text-orange-700 dark:text-orange-400 font-bold lowercase hover:underline">
                                            Reset
                                        </a>
                                    @endif
                                </div>

                                <div class="py-1 max-h-56 overflow-y-auto custom-scrollbar space-y-0.5">
                                    <a href="{{ $buildQuery(['entry_year' => 'all']) }}"
                                       class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ empty($entryYear) || $entryYear === 'all' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                        <span>Semua Angkatan</span>
                                        @if(empty($entryYear) || $entryYear === 'all')
                                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </a>

                                    @foreach($availableEntryYears as $year)
                                        <a href="{{ $buildQuery(['entry_year' => $year]) }}"
                                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ((string)$entryYear === (string)$year) ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                            <span>Angkatan {{ $year }}</span>
                                            @if((string)$entryYear === (string)$year)
                                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- 4. Reset Button -->
                        @if($hasActiveDosenFilters)
                            <a href="{{ route('theses.index', array_filter(['status' => ($status ?? 'active') !== 'active' ? $status : null])) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 dark:text-rose-300 dark:border-rose-800 rounded-xl text-xs font-bold transition-all shadow-2xs hover:scale-[1.02] active:scale-95 cursor-pointer shrink-0"
                               title="Reset Semua Filter">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <span>Reset</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ACTIVE FILTER TAGS BAR -->
            @if($hasActiveDosenFilters)
                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 bg-orange-50/70 dark:bg-slate-900/80 border border-orange-200/80 dark:border-slate-700 rounded-2xl text-xs">
                    <div class="flex items-center gap-2 flex-wrap text-orange-900 dark:text-slate-200 font-medium">
                        <span class="font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Filter Aktif ({{ $theses->total() }} Mahasiswa):</span>
                        </span>

                        @if(!empty($roleFilter) && $roleFilter !== 'all')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-orange-200 dark:border-slate-700 text-orange-800 dark:text-orange-300 font-bold shadow-2xs">
                                <span>Peran: {{ $roleFilter === 'p1' ? 'Pembimbing 1' : 'Pembimbing 2' }}</span>
                                <a href="{{ $buildQuery(['role_filter' => 'all']) }}" class="text-orange-500 hover:text-orange-700 dark:hover:text-orange-300 font-black text-sm leading-none" title="Hapus filter peran">&times;</a>
                            </span>
                        @endif

                        @if(!empty($filter) && $filter !== 'all')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-orange-200 dark:border-slate-700 text-orange-800 dark:text-orange-300 font-bold shadow-2xs">
                                <span>Progres: 
                                    @if($filter === 'proposal') Tahap Proposal (&lt; 8 Sesi)
                                    @elseif($filter === 'ready_up') Siap UP (&ge; 8 Sesi)
                                    @elseif($filter === 'ready_sidang') Siap Sidang (&ge; 16 Sesi)
                                    @elseif($filter === 'stalled') Macet (&gt; 14 Hari)
                                    @else {{ ucfirst($filter) }}
                                    @endif
                                </span>
                                <a href="{{ $buildQuery(['filter' => 'all']) }}" class="text-orange-500 hover:text-orange-700 dark:hover:text-orange-300 font-black text-sm leading-none" title="Hapus filter kategori progres">&times;</a>
                            </span>
                        @endif

                        @if(!empty($entryYear) && $entryYear !== 'all')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-orange-200 dark:border-slate-700 text-orange-800 dark:text-orange-300 font-bold shadow-2xs">
                                <span>Angkatan: {{ $entryYear }}</span>
                                <a href="{{ $buildQuery(['entry_year' => 'all']) }}" class="text-orange-500 hover:text-orange-700 dark:hover:text-orange-300 font-black text-sm leading-none" title="Hapus filter angkatan">&times;</a>
                            </span>
                        @endif

                        @if(($cohortFilter ?? 'all') !== 'all')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-orange-200 dark:border-slate-700 text-orange-800 dark:text-orange-300 font-bold shadow-2xs">
                                <span>Kategori: {{ $cohortFilter === 'new' ? 'Angkatan Baru' : 'Angkatan Lama' }}</span>
                                <a href="{{ $buildQuery(['cohort_filter' => 'all']) }}" class="text-orange-500 hover:text-orange-700 dark:hover:text-orange-300 font-black text-sm leading-none" title="Hapus filter cohort">&times;</a>
                            </span>
                        @endif

                        @if(($accStatus ?? 'all') !== 'all')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-indigo-200 dark:border-slate-700 text-indigo-800 dark:text-indigo-300 font-bold shadow-2xs">
                                <span>Status ACC: {{ ucfirst(str_replace('_', ' ', $accStatus)) }}</span>
                                <a href="{{ $buildQuery(['acc_status' => 'all']) }}" class="text-indigo-500 hover:text-indigo-700 dark:hover:text-indigo-300 font-black text-sm leading-none" title="Hapus filter ACC">&times;</a>
                            </span>
                        @endif

                        @if(($mentoringHealth ?? 'all') !== 'all')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-rose-200 dark:border-slate-700 text-rose-800 dark:text-rose-300 font-bold shadow-2xs">
                                <span>Keaktifan: {{ ucfirst($mentoringHealth) }}</span>
                                <a href="{{ $buildQuery(['mentoring_health' => 'all']) }}" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-300 font-black text-sm leading-none" title="Hapus filter keaktifan">&times;</a>
                            </span>
                        @endif

                        @if(!empty($search))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-orange-200 dark:border-slate-700 text-orange-800 dark:text-orange-300 font-bold shadow-2xs">
                                <span>Pencarian: "{{ $search }}"</span>
                                <a href="{{ $buildQuery(['search' => null]) }}" class="text-orange-500 hover:text-orange-700 dark:hover:text-orange-300 font-black text-sm leading-none" title="Hapus pencarian">&times;</a>
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('theses.index', array_filter(['status' => ($status ?? 'active') !== 'active' ? $status : null])) }}" 
                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-slate-700 text-orange-700 dark:text-orange-300 font-bold rounded-xl border border-orange-200 dark:border-slate-700 transition-colors shadow-2xs">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Reset Semua</span>
                    </a>
                </div>
            @endif
        @endif

        <x-table-card 
            title="{{ Auth::user()->role === 'dosen' ? 'Daftar Mahasiswa Bimbingan' : 'Daftar Pengajuan Skripsi' }}"
            :footer="$theses->links()">
            
            <x-slot name="headerActions">
                <div class="flex flex-wrap items-center justify-between sm:justify-end gap-2.5 w-full">
                    <!-- Search Input -->
                    <div class="w-full sm:w-64">
                        <x-search-input 
                            name="search" 
                            :value="$search ?? ''" 
                            placeholder="Cari nama, NPM, judul..." 
                            route="theses.index"
                            :params="['status' => $status ?? '', 'role_filter' => $roleFilter ?? '', 'cohort_filter' => $cohortFilter ?? '', 'entry_year' => $entryYear ?? '', 'mentoring_health' => $mentoringHealth ?? 'all', 'acc_status' => $accStatus ?? 'all']" />
                    </div>

                    @if(Auth::user()->role === 'dosen')
                        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                            <!-- Dosen Role Filter Dropdown -->
                            <form action="{{ route('theses.index') }}" method="GET" class="relative">
                                <input type="hidden" name="status" value="{{ $status ?? 'active' }}">
                                <input type="hidden" name="cohort_filter" value="{{ $cohortFilter ?? 'all' }}">
                                <input type="hidden" name="mentoring_health" value="{{ $mentoringHealth ?? 'all' }}">
                                <input type="hidden" name="acc_status" value="{{ $accStatus ?? 'all' }}">
                                @if($entryYear)
                                    <input type="hidden" name="entry_year" value="{{ $entryYear }}">
                                @endif
                                @if($search)
                                    <input type="hidden" name="search" value="{{ $search }}">
                                @endif
                                <select name="role_filter" onchange="this.form.submit()" 
                                        class="pl-3 pr-8 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all shadow-2xs cursor-pointer">
                                    <option value="" {{ empty($roleFilter) ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">
                                        Semua Peran ({{ $dosenStats['total'] ?? $theses->total() }})
                                    </option>
                                    <option value="p1" {{ ($roleFilter ?? '') === 'p1' ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">
                                        👤 Pembimbing 1 ({{ $dosenStats['p1'] ?? 0 }})
                                    </option>
                                    <option value="p2" {{ ($roleFilter ?? '') === 'p2' ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">
                                        👥 Pembimbing 2 ({{ $dosenStats['p2'] ?? 0 }})
                                    </option>
                                </select>
                            </form>

                            <!-- Export PDF for Dosen -->
                            <a href="{{ route('theses.export-pdf', ['search' => $search ?? '', 'status' => $status ?? 'active', 'role_filter' => $roleFilter ?? '', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'mentoring_health' => $mentoringHealth ?? 'all', 'acc_status' => $accStatus ?? 'all']) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all" 
                               title="Ekspor PDF Mahasiswa Bimbingan">
                                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <span>PDF</span>
                            </a>
                        </div>
                    @endif
                    
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <!-- Group 1: Tools (Clean Audit, Kanban, Migrasi) -->
                            <div class="inline-flex items-center p-1 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs gap-0.5">
                                <!-- Clean Audit -->
                                <a href="{{ route('theses.clean-audit') }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded-lg text-xs font-bold transition-all border border-emerald-200/80 dark:border-emerald-800/60"
                                   title="Pusat Validasi & Clean Audit Judul">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <span>Clean Audit</span>
                                    @if(isset($pendingSummary) && $pendingSummary['total'] > 0)
                                        <span class="px-1.5 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-black">
                                            {{ $pendingSummary['total'] }}
                                        </span>
                                    @endif
                                </a>

                                <!-- Kanban View -->
                                <a href="{{ route('theses.kanban') }}" 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg text-xs font-bold transition-all"
                                   title="Tampilan Mode Kanban">
                                    <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                                    <span>Kanban</span>
                                </a>

                                <!-- Input Migrasi -->
                                <a href="{{ route('theses.migration.create') }}" 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-slate-600 dark:text-slate-300 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg text-xs font-bold transition-all"
                                   title="Input Data Migrasi">
                                    <svg class="w-3.5 h-3.5 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                    <span>Migrasi</span>
                                </a>

                                <!-- SK Pembimbing -->
                                <a href="{{ route('advisor-decrees.index') }}" 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg text-xs font-bold transition-all"
                                   title="Arsip & Generator SK Pembimbing Skripsi">
                                    <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>SK Pembimbing</span>
                                </a>
                            </div>

                            <!-- Group 2: Export Dropdown / Segment -->
                            <div class="inline-flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xs divide-x divide-slate-200 dark:divide-slate-700 overflow-hidden">
                                <a href="{{ route('theses.export-excel', ['search' => request('search'), 'status' => $status ?? 'all', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'mentoring_health' => $mentoringHealth ?? 'all', 'acc_status' => $accStatus ?? 'all']) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors" 
                                   title="Ekspor ke Excel">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>Excel</span>
                                </a>
                                <a href="{{ route('theses.export-pdf', ['search' => request('search'), 'status' => $status ?? 'all', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'mentoring_health' => $mentoringHealth ?? 'all', 'acc_status' => $accStatus ?? 'all']) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors" 
                                   title="Ekspor ke PDF">
                                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <span>PDF</span>
                                </a>
                            </div>

                            <!-- Group 3: Primary Action (Ajukan Judul) -->
                            <a href="{{ route('theses.create') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:scale-[1.02] transition-all shrink-0 cursor-pointer">
                                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                <span>Ajukan Judul</span>
                            </a>
                        </div>
                    @endif
                </div>
            </x-slot>

            <!-- Sub-Toolbar: Filter Angkatan & Keaktifan Bimbingan (Dropdown Modern) -->
            @if(Auth::user()->role !== 'dosen')
            <div class="px-5 py-3 bg-slate-50/70 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <!-- 1. Dropdown Filter Angkatan -->
                    <div class="relative" x-data="{ openCohortDropdown: false }" @click.outside="openCohortDropdown = false">
                        <button type="button" 
                                @click="openCohortDropdown = !openCohortDropdown"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border cursor-pointer shadow-2xs {{ ($cohortFilter ?? 'all') !== 'all' ? 'bg-orange-50/80 dark:bg-orange-950/30 border-orange-300 dark:border-orange-500/50 text-orange-700 dark:text-orange-300 ring-2 ring-orange-500/10' : 'bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700' }}"
                                aria-haspopup="true"
                                :aria-expanded="openCohortDropdown">
                            <svg class="w-3.5 h-3.5 {{ ($cohortFilter ?? 'all') !== 'all' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                            
                            @if(($cohortFilter ?? 'all') === 'new')
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>🌱 Angkatan Baru</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300">
                                    {{ $cohortCounts['new'] ?? 0 }}
                                </span>
                            @elseif(($cohortFilter ?? 'all') === 'old')
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                <span>⏳ Angkatan Lama</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300">
                                    {{ $cohortCounts['old'] ?? 0 }}
                                </span>
                            @else
                                <span>Angkatan: Semua</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                    {{ $cohortCounts['all'] ?? 0 }}
                                </span>
                            @endif

                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openCohortDropdown ? 'rotate-180 text-orange-600 dark:text-orange-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu Angkatan -->
                        <div x-show="openCohortDropdown"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute left-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200 dark:border-slate-700 p-1.5 z-50 text-left"
                             style="display: none;">
                            
                            <div class="px-3 py-2 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                                <span>Filter Angkatan</span>
                                @if(($cohortFilter ?? 'all') !== 'all')
                                    <a href="{{ route('theses.index', ['status' => $status ?? ($user->role === 'dosen' ? 'active' : 'all'), 'search' => $search, 'role_filter' => $roleFilter ?? '', 'cohort_filter' => 'all', 'entry_year' => $entryYear ?? '', 'mentoring_health' => $mentoringHealth ?? 'all']) }}" 
                                       class="text-orange-600 hover:text-orange-700 dark:text-orange-400 font-bold lowercase hover:underline">
                                        Reset
                                    </a>
                                @endif
                            </div>

                            <div class="py-1 space-y-0.5">
                                <!-- 1. Semua -->
                                <a href="{{ route('theses.index', ['status' => $status ?? ($user->role === 'dosen' ? 'active' : 'all'), 'search' => $search, 'role_filter' => $roleFilter ?? '', 'cohort_filter' => 'all', 'entry_year' => $entryYear ?? '', 'mentoring_health' => $mentoringHealth ?? 'all']) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($cohortFilter ?? 'all') === 'all' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ ($cohortFilter ?? 'all') === 'all' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600' }} shrink-0"></span>
                                        <span>Semua Mahasiswa</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($cohortFilter ?? 'all') === 'all' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                            {{ $cohortCounts['all'] ?? 0 }}
                                        </span>
                                        @if(($cohortFilter ?? 'all') === 'all')
                                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </div>
                                </a>

                                <!-- 2. Baru -->
                                <a href="{{ route('theses.index', ['status' => $status ?? ($user->role === 'dosen' ? 'active' : 'all'), 'search' => $search, 'role_filter' => $roleFilter ?? '', 'cohort_filter' => 'new', 'entry_year' => $entryYear ?? '', 'mentoring_health' => $mentoringHealth ?? 'all']) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($cohortFilter ?? 'all') === 'new' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <div>
                                            <span>🌱 Angkatan Baru</span>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Semester Normal / Baru Mengajukan</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($cohortFilter ?? 'all') === 'new' ? 'bg-emerald-200/80 dark:bg-emerald-500/30 text-emerald-800 dark:text-emerald-200' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' }}">
                                            {{ $cohortCounts['new'] ?? 0 }}
                                        </span>
                                        @if(($cohortFilter ?? 'all') === 'new')
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </div>
                                </a>

                                <!-- 3. Lama -->
                                <a href="{{ route('theses.index', ['status' => $status ?? ($user->role === 'dosen' ? 'active' : 'all'), 'search' => $search, 'role_filter' => $roleFilter ?? '', 'cohort_filter' => 'old', 'entry_year' => $entryYear ?? '', 'mentoring_health' => $mentoringHealth ?? 'all']) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($cohortFilter ?? 'all') === 'old' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                        <div>
                                            <span>⏳ Angkatan Lama</span>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Masa Studi Lanjut (Semester 9+)</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($cohortFilter ?? 'all') === 'old' ? 'bg-amber-200/80 dark:bg-amber-500/30 text-amber-800 dark:text-amber-200' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' }}">
                                            {{ $cohortCounts['old'] ?? 0 }}
                                        </span>
                                        @if(($cohortFilter ?? 'all') === 'old')
                                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Dropdown Filter Keaktifan Bimbingan (Early Warning) -->
                    <div class="relative" x-data="{ openHealthDropdown: false }" @click.outside="openHealthDropdown = false">
                        <button type="button" 
                                @click="openHealthDropdown = !openHealthDropdown"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border cursor-pointer shadow-2xs {{ ($mentoringHealth ?? 'all') !== 'all' ? 'bg-orange-50/80 dark:bg-orange-950/30 border-orange-300 dark:border-orange-500/50 text-orange-700 dark:text-orange-300 ring-2 ring-orange-500/10' : 'bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700' }}"
                                aria-haspopup="true"
                                :aria-expanded="openHealthDropdown">
                            <svg class="w-3.5 h-3.5 {{ ($mentoringHealth ?? 'all') !== 'all' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            
                            @if(($mentoringHealth ?? 'all') === 'active')
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>Lancar (≤7h)</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300">
                                    {{ $mentoringHealthCounts['active'] ?? 0 }}
                                </span>
                            @elseif(($mentoringHealth ?? 'all') === 'warning')
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                <span>Pasif (>7h)</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300">
                                    {{ $mentoringHealthCounts['warning'] ?? 0 }}
                                </span>
                            @elseif(($mentoringHealth ?? 'all') === 'critical')
                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 animate-pulse"></span>
                                <span>Macet (>14h)</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-300">
                                    {{ $mentoringHealthCounts['critical'] ?? 0 }}
                                </span>
                            @else
                                <span>Bimbingan: Semua</span>
                            @endif

                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openHealthDropdown ? 'rotate-180 text-orange-600 dark:text-orange-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu Keaktifan -->
                        <div x-show="openHealthDropdown"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute left-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200 dark:border-slate-700 p-1.5 z-50 text-left"
                             style="display: none;">
                            
                            <div class="px-3 py-2 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                                <span>Keaktifan Bimbingan</span>
                                @if(($mentoringHealth ?? 'all') !== 'all')
                                    <a href="{{ route('theses.index', ['status' => $status ?? ($user->role === 'dosen' ? 'active' : 'all'), 'search' => $search, 'role_filter' => $roleFilter ?? '', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'mentoring_health' => 'all']) }}" 
                                       class="text-orange-600 hover:text-orange-700 dark:text-orange-400 font-bold lowercase hover:underline">
                                        Reset
                                    </a>
                                @endif
                            </div>

                            <div class="py-1 space-y-0.5">
                                <!-- 1. Semua -->
                                <a href="{{ route('theses.index', ['status' => $status ?? ($user->role === 'dosen' ? 'active' : 'all'), 'search' => $search, 'role_filter' => $roleFilter ?? '', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'mentoring_health' => 'all']) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($mentoringHealth ?? 'all') === 'all' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ ($mentoringHealth ?? 'all') === 'all' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600' }} shrink-0"></span>
                                        <span>Semua Status Keaktifan</span>
                                    </div>
                                    @if(($mentoringHealth ?? 'all') === 'all')
                                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @endif
                                </a>

                                <!-- 2. Lancar -->
                                <a href="{{ route('theses.index', ['status' => $status ?? ($user->role === 'dosen' ? 'active' : 'all'), 'search' => $search, 'role_filter' => $roleFilter ?? '', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'mentoring_health' => 'active']) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($mentoringHealth ?? 'all') === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <div>
                                            <span class="font-bold text-emerald-800 dark:text-emerald-300">Lancar (≤ 7 hari)</span>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Bimbingan aktif dalam seminggu</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($mentoringHealth ?? 'all') === 'active' ? 'bg-emerald-200/80 dark:bg-emerald-500/30 text-emerald-800 dark:text-emerald-200' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' }}">
                                            {{ $mentoringHealthCounts['active'] ?? 0 }}
                                        </span>
                                        @if(($mentoringHealth ?? 'all') === 'active')
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </div>
                                </a>

                                <!-- 3. Pasif -->
                                <a href="{{ route('theses.index', ['status' => $status ?? ($user->role === 'dosen' ? 'active' : 'all'), 'search' => $search, 'role_filter' => $roleFilter ?? '', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'mentoring_health' => 'warning']) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($mentoringHealth ?? 'all') === 'warning' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                                        <div>
                                            <span class="font-bold text-amber-800 dark:text-amber-300">Pasif (8 - 14 hari)</span>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Peringatan: Belum bimbingan > 1 minggu</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($mentoringHealth ?? 'all') === 'warning' ? 'bg-amber-200/80 dark:bg-amber-500/30 text-amber-800 dark:text-amber-200' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' }}">
                                            {{ $mentoringHealthCounts['warning'] ?? 0 }}
                                        </span>
                                        @if(($mentoringHealth ?? 'all') === 'warning')
                                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </div>
                                </a>

                                <!-- 4. Macet -->
                                <a href="{{ route('theses.index', ['status' => $status ?? ($user->role === 'dosen' ? 'active' : 'all'), 'search' => $search, 'role_filter' => $roleFilter ?? '', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'mentoring_health' => 'critical']) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($mentoringHealth ?? 'all') === 'critical' ? 'bg-rose-50 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0 animate-pulse"></span>
                                        <div>
                                            <span class="font-bold text-rose-800 dark:text-rose-300">Macet (> 14 hari)</span>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Kritis: Tidak bimbingan > 2 minggu</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($mentoringHealth ?? 'all') === 'critical' ? 'bg-rose-200/80 dark:bg-rose-500/30 text-rose-800 dark:text-rose-200' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' }}">
                                            {{ $mentoringHealthCounts['critical'] ?? 0 }}
                                        </span>
                                        @if(($mentoringHealth ?? 'all') === 'critical')
                                            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Dropdown Filter Status Kelayakan ACC (Seminar / Sidang) -->
                    <div class="relative" x-data="{ openAccDropdown: false }" @click.outside="openAccDropdown = false">
                        <button type="button" 
                                @click="openAccDropdown = !openAccDropdown"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border cursor-pointer shadow-2xs {{ ($accStatus ?? 'all') !== 'all' ? 'bg-indigo-50/80 dark:bg-indigo-950/30 border-indigo-300 dark:border-indigo-500/50 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/10' : 'bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700' }}"
                                aria-haspopup="true"
                                :aria-expanded="openAccDropdown">
                            <svg class="w-3.5 h-3.5 {{ ($accStatus ?? 'all') !== 'all' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            
                            @if(($accStatus ?? 'all') === 'ready_seminar')
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>Siap Seminar</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300">
                                    {{ $accCounts['ready_seminar'] ?? 0 }}
                                </span>
                            @elseif(($accStatus ?? 'all') === 'ready_defense')
                                <span class="w-2 h-2 rounded-full bg-indigo-500 shrink-0"></span>
                                <span>Siap Sidang</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-indigo-100 dark:bg-indigo-950/80 text-indigo-800 dark:text-indigo-300">
                                    {{ $accCounts['ready_defense'] ?? 0 }}
                                </span>
                            @elseif(($accStatus ?? 'all') === 'partial_acc')
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                <span>ACC 1/2</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300">
                                    {{ $accCounts['partial_acc'] ?? 0 }}
                                </span>
                            @elseif(($accStatus ?? 'all') === 'my_acc_pending')
                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 animate-pulse"></span>
                                <span>Menunggu ACC Saya</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-300">
                                    {{ $accCounts['my_acc_pending'] ?? 0 }}
                                </span>
                            @elseif(($accStatus ?? 'all') === 'waiting_partner')
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                <span>Tunggu Rekan</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300">
                                    {{ $accCounts['waiting_partner'] ?? 0 }}
                                </span>
                            @else
                                <span>Status ACC: Semua</span>
                            @endif

                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openAccDropdown ? 'rotate-180 text-indigo-600 dark:text-indigo-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu ACC Status -->
                        <div x-show="openAccDropdown"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute left-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200 dark:border-slate-700 p-1.5 z-50 text-left"
                             style="display: none;">
                            
                            <div class="px-3 py-2 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                                <span>Kelayakan Ujian & ACC</span>
                                @if(($accStatus ?? 'all') !== 'all')
                                    <a href="{{ route('theses.index', array_merge(request()->query(), ['acc_status' => 'all'])) }}" 
                                       class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-bold lowercase hover:underline">
                                        Reset
                                    </a>
                                @endif
                            </div>

                            <div class="py-1 space-y-0.5">
                                <!-- 1. Semua -->
                                <a href="{{ route('theses.index', array_merge(request()->query(), ['acc_status' => 'all'])) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($accStatus ?? 'all') === 'all' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ ($accStatus ?? 'all') === 'all' ? 'bg-indigo-500' : 'bg-slate-300 dark:bg-slate-600' }} shrink-0"></span>
                                        <span>Semua Status ACC</span>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-400">{{ $accCounts['all'] ?? 0 }}</span>
                                </a>

                                <!-- 2. Siap Seminar UP -->
                                <a href="{{ route('theses.index', array_merge(request()->query(), ['acc_status' => 'ready_seminar'])) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($accStatus ?? 'all') === 'ready_seminar' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <div>
                                            <span>🎓 Siap Seminar UP</span>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">ACC Lengkap (P1 & P2)</p>
                                        </div>
                                    </div>
                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300">
                                        {{ $accCounts['ready_seminar'] ?? 0 }}
                                    </span>
                                </a>

                                <!-- 3. Siap Sidang Akhir -->
                                <a href="{{ route('theses.index', array_merge(request()->query(), ['acc_status' => 'ready_defense'])) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($accStatus ?? 'all') === 'ready_defense' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 shrink-0"></span>
                                        <div>
                                            <span>🏛️ Siap Sidang Akhir</span>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">ACC Lengkap (P1 & P2)</p>
                                        </div>
                                    </div>
                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-indigo-100 dark:bg-indigo-950/80 text-indigo-800 dark:text-indigo-300">
                                        {{ $accCounts['ready_defense'] ?? 0 }}
                                    </span>
                                </a>

                                <!-- 4. ACC Parsial -->
                                <a href="{{ route('theses.index', array_merge(request()->query(), ['acc_status' => 'partial_acc'])) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($accStatus ?? 'all') === 'partial_acc' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                        <div>
                                            <span>⏳ ACC Parsial (1/2)</span>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Baru 1 Pembimbing ACC</p>
                                        </div>
                                    </div>
                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300">
                                        {{ $accCounts['partial_acc'] ?? 0 }}
                                    </span>
                                </a>

                                @if(Auth::user()->role === 'dosen')
                                    <div class="pt-1 mt-1 border-t border-slate-100 dark:border-slate-700/80">
                                        <!-- Khusus Dosen: Menunggu ACC Saya -->
                                        <a href="{{ route('theses.index', array_merge(request()->query(), ['acc_status' => 'my_acc_pending'])) }}"
                                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($accStatus ?? 'all') === 'my_acc_pending' ? 'bg-rose-50 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 animate-pulse"></span>
                                                <div>
                                                    <span class="font-bold text-rose-700 dark:text-rose-300">Menunggu ACC Saya</span>
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Belum Anda berikan ACC</p>
                                                </div>
                                            </div>
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-300">
                                                {{ $accCounts['my_acc_pending'] ?? 0 }}
                                            </span>
                                        </a>

                                        <!-- Khusus Dosen: Menunggu Rekan Pembimbing -->
                                        <a href="{{ route('theses.index', array_merge(request()->query(), ['acc_status' => 'waiting_partner'])) }}"
                                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ ($accStatus ?? 'all') === 'waiting_partner' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                                <div>
                                                    <span>Tunggu Rekan Pembimbing</span>
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Anda sudah ACC, rekan belum</p>
                                                </div>
                                            </div>
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300">
                                                {{ $accCounts['waiting_partner'] ?? 0 }}
                                            </span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Reset Filter Button (Jika filter aktif) -->
                    @if(($cohortFilter ?? 'all') !== 'all' || (!empty($entryYear) && $entryYear !== 'all') || (($mentoringHealth ?? 'all') !== 'all') || (($accStatus ?? 'all') !== 'all'))
                        <a href="{{ route('theses.index', ['status' => $status ?? ($user->role === 'dosen' ? 'active' : 'all'), 'search' => $search, 'role_filter' => $roleFilter ?? '']) }}"
                           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-bold text-rose-600 hover:text-rose-700 dark:text-rose-400 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800/60 rounded-xl transition-all shadow-2xs"
                           title="Reset semua filter">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span>Reset Filter</span>
                        </a>
                    @endif
                </div>

                <!-- Right: Dropdown Pilihan Spesifik Tahun Angkatan -->
                <div class="flex items-center gap-2">
                    <form action="{{ route('theses.index') }}" method="GET" class="flex items-center">
                        <input type="hidden" name="status" value="{{ $status ?? ($user->role === 'dosen' ? 'active' : 'all') }}">
                        <input type="hidden" name="cohort_filter" value="{{ $cohortFilter ?? 'all' }}">
                        <input type="hidden" name="mentoring_health" value="{{ $mentoringHealth ?? 'all' }}">
                        <input type="hidden" name="acc_status" value="{{ $accStatus ?? 'all' }}">
                        @if($search)
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        @if(isset($roleFilter) && $roleFilter)
                            <input type="hidden" name="role_filter" value="{{ $roleFilter }}">
                        @endif

                        <select name="entry_year" onchange="this.form.submit()"
                                class="pl-3 pr-8 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all shadow-2xs cursor-pointer">
                            <option value="all" {{ empty($entryYear) || $entryYear === 'all' ? 'selected' : '' }}>
                                Semua Tahun Angkatan
                            </option>
                            @foreach($availableEntryYears as $yr)
                                <option value="{{ $yr }}" {{ (string)$entryYear === (string)$yr ? 'selected' : '' }}>
                                    Angkatan {{ $yr }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            @endif

            <!-- Table Skeleton Shimmer -->
            <div x-show="pageLoading" x-cloak>
                <x-skeleton type="table" :rows="6" />
            </div>

            <table x-show="!pageLoading"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0"
                   x-transition:enter-end="opacity-100"
                   class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    @if(Auth::user()->role === 'dosen')
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-xs border-b border-slate-100 dark:border-slate-700/80">
                            <th class="py-3.5 px-6 font-semibold tracking-wider">MAHASISWA</th>
                            <th class="py-3.5 px-6 font-semibold tracking-wider">RENCANA JUDUL SKRIPSI</th>
                            <th class="py-3.5 px-6 font-semibold tracking-wider text-center">
                                <span class="block">Status &amp; Progres</span>
                                <span class="block text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-black">PROGRES TARGET BIMBINGAN</span>
                            </th>
                            <th class="py-3.5 px-6 font-semibold tracking-wider text-right">AKSI</th>
                        </tr>
                    @else
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                            <th class="py-4 px-6">Mahasiswa</th>
                            <th class="py-4 px-6">Rencana Judul Skripsi</th>
                            <th class="py-4 px-6">Deskripsi</th>
                            <th class="py-4 px-6 text-center">Status &amp; Progres</th>
                            <th class="py-4 px-6">Pembimbing</th>
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                                <th class="py-4 px-6 text-right whitespace-nowrap">Aksi</th>
                            @endif
                        </tr>
                    @endif
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($theses as $thesis)
                        @if(Auth::user()->role === 'dosen')
                            <!-- BARIS DOSEN (4 KOLOM TERPADU: MAHASISWA, RENCANA JUDUL SKRIPSI, STATUS & PROGRES, AKSI) -->
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors group">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3.5">
                                        <div class="relative w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200/80 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0 ring-2 ring-white dark:ring-slate-800 shadow-2xs">
                                            <img src="{{ $thesis->student->avatar_url }}" alt="{{ $thesis->student->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="space-y-1">
                                            <!-- Baris 1: Nama Mahasiswa -->
                                            <div>
                                                <a href="{{ route('theses.logbooks', $thesis->id) }}" class="font-bold text-slate-900 dark:text-white tracking-tight text-xs sm:text-sm hover:text-orange-600 dark:hover:text-orange-400 transition-colors" title="Buka catatan bimbingan {{ $thesis->student->name }}">
                                                    {{ ucwords(strtolower($thesis->student->name)) }}
                                                </a>
                                            </div>
                                            
                                            @php
                                                $waNumber = \App\Helpers\PhoneHelper::formatForWhatsApp($thesis->student->phone_number);
                                                $dosenName = Auth::user()->name;
                                                $roleLabel = ($thesis->pembimbing1_id === Auth::id()) ? 'Pembimbing 1' : (($thesis->pembimbing2_id === Auth::id()) ? 'Pembimbing 2' : 'Dosen');
                                                $waMessage = urlencode("Halo {$thesis->student->name}, saya {$dosenName} ({$roleLabel}). Terkait logbook bimbingan skripsi: \"" . ($thesis->final_title ?? $thesis->title) . "\"");
                                            @endphp

                                            <!-- Baris 2: Chip NPM & Tombol Kontak WhatsApp Sejajar -->
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <!-- Chip NPM -->
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs"
                                                      title="Nomor Pokok Mahasiswa (NPM)">
                                                    <svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                                    <span>{{ $thesis->student->identifier ?? 'NPM -' }}</span>
                                                </span>

                                                <!-- Tombol WhatsApp Shortcut -->
                                                @if($waNumber)
                                                    <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" 
                                                       target="_blank" 
                                                       rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-2xs hover:scale-105 active:scale-95 transition-all cursor-pointer"
                                                       title="Kirim Pesan WhatsApp ke {{ $thesis->student->name }} ({{ $thesis->student->phone_number }})">
                                                        <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400 fill-current shrink-0" viewBox="0 0 24 24">
                                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                                        </svg>
                                                        <span>Chat WA</span>
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-medium bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 border border-slate-200/50 dark:border-slate-700/50" 
                                                          title="Nomor WhatsApp belum didaftarkan oleh mahasiswa">
                                                        <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                        <span>No WA</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kolom 2: Rencana Judul Skripsi -->
                                <td class="py-4 px-6 max-w-sm whitespace-normal" x-data="{ openAbstract: false }">
                                    @if($thesis->final_title)
                                        <div class="font-bold text-slate-900 dark:text-white line-clamp-2 text-xs sm:text-sm leading-snug tracking-tight" title="{{ $thesis->final_title }}">
                                            {{ $thesis->final_title }}
                                        </div>
                                        <div class="mt-1 flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400" title="Rencana awal: {{ $thesis->title }}">
                                            <span class="text-amber-500 shrink-0 font-bold">↳</span>
                                            <span class="italic line-clamp-1">Awal: {{ $thesis->title }}</span>
                                        </div>
                                    @else
                                        <div class="font-bold text-slate-900 dark:text-white line-clamp-2 text-xs sm:text-sm leading-snug tracking-tight" title="{{ $thesis->title }}">
                                            {{ $thesis->title }}
                                        </div>
                                    @endif

                                    <!-- Similarity Score & Abstract Modal Trigger -->
                                    <div class="mt-2 flex items-center gap-2 flex-wrap">
                                        @php
                                            $rawTitle = trim($thesis->final_title ?? $thesis->title ?? '');
                                            $titleUpper = strtoupper($rawTitle);
                                            $hasValidTitle = !empty($rawTitle) && !in_array($titleUpper, ['BELUM DIKETAHUI', 'BELUM DITENTUKAN', 'BELUM ADA JUDUL', 'BELUM ADA', '-']) && strlen($rawTitle) >= 8;
                                            $simScore = $hasValidTitle ? $thesis->getMaxSimilarityScore() : 0;
                                            $simMatches = $hasValidTitle ? $thesis->getDetailedSimilarityMatches() : collect();
                                        @endphp
                                        @if($hasValidTitle)
                                            <button @click='$dispatch("open-audit-modal", {
                                                student: "{{ addslashes($thesis->student->name) }}",
                                                npm: "{{ $thesis->student->identifier }}",
                                                title: "{{ addslashes($rawTitle) }}",
                                                score: {{ $simScore }},
                                                matches: {{ json_encode($simMatches->values()->toArray()) }}
                                            })' class="group focus:outline-hidden cursor-pointer">
                                                @if($simScore >= 66)
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 hover:bg-rose-500/20 text-rose-700 dark:text-rose-300 transition-all">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping shrink-0"></span>
                                                        <span>{{ $simScore }}% Sangat Mirip</span>
                                                    </span>
                                                @elseif($simScore >= 35)
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 transition-all">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                                        <span>{{ $simScore }}% Kemiripan</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 transition-all">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                        <span>{{ 100 - $simScore }}% Unik</span>
                                                    </span>
                                                @endif
                                            </button>
                                        @endif

                                        @if($thesis->abstract)
                                            <button @click="openAbstract = true" class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold flex items-center gap-1 transition-colors cursor-pointer" title="Lihat Abstrak & Detail Lengkap">
                                                <span>Lihat Detail Lengkap</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        @endif
                                    </div>

                                    @if($thesis->abstract)
                                        <!-- Abstract Modal -->
                                        <template x-teleport="body">
                                            <div x-show="openAbstract" class="fixed inset-0 overflow-y-auto" style="z-index: 99999 !important;" x-cloak x-transition>
                                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAbstract = false">
                                                        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                    </div>
                                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                    <div class="inline-block align-middle bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-slate-200 dark:border-slate-700 relative" style="z-index: 100000 !important;">
                                                        <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                                                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 uppercase tracking-widest">Detail Deskripsi Skripsi</h3>
                                                            <button @click="openAbstract = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                        </div>
                                                        <div class="px-8 py-8 max-h-[60vh] overflow-y-auto">
                                                            <div class="mb-8">
                                                                <p class="text-[10px] font-bold text-orange-600 dark:text-orange-400 uppercase tracking-widest mb-2">Judul Pengajuan</p>
                                                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-tight uppercase">{{ $thesis->final_title ?? $thesis->title }}</h4>
                                                            </div>
                                                            <div class="p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                                                <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3">Deskripsi / Rencana</p>
                                                                <div class="text-xs text-slate-600 dark:text-slate-400 leading-loose text-justify font-medium whitespace-pre-line">
                                                                    {{ $thesis->abstract }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                                                            <button type="button" @click="openAbstract = false" class="px-8 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm cursor-pointer">
                                                                Tutup
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    @endif

                                    <!-- Supervisor Chips with Session Counts & (anda) -->
                                    @php
                                        $p1Count = $thesis->mentoringSessions ? $thesis->mentoringSessions->where('dosen_id', $thesis->pembimbing1_id)->count() : 0;
                                        $p2Count = $thesis->mentoringSessions ? $thesis->mentoringSessions->where('dosen_id', $thesis->pembimbing2_id)->count() : 0;
                                        $isMeP1 = (Auth::id() === $thesis->pembimbing1_id);
                                        $isMeP2 = (Auth::id() === $thesis->pembimbing2_id);
                                    @endphp
                                    <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex flex-col gap-1 text-[11px]">
                                        @if($thesis->pembimbing1)
                                            <div class="flex items-center gap-1.5 flex-wrap {{ $isMeP1 ? 'font-bold text-indigo-700 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400' }}">
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/80">P1</span>
                                                <span class="truncate max-w-[190px]" title="{{ $thesis->pembimbing1->name }}">{{ $thesis->pembimbing1->name }}</span>
                                                @if($isMeP1)
                                                    <span class="text-[9px] font-bold text-orange-500 dark:text-orange-400 lowercase">(anda)</span>
                                                @endif
                                                <span class="font-mono text-[10px] text-slate-400 dark:text-slate-500 font-normal">({{ $p1Count }} sesi)</span>
                                            </div>
                                        @endif
                                        @if($thesis->pembimbing2)
                                            <div class="flex items-center gap-1.5 flex-wrap {{ $isMeP2 ? 'font-bold text-purple-700 dark:text-purple-300' : 'text-slate-600 dark:text-slate-400' }}">
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-black bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800/80">P2</span>
                                                <span class="truncate max-w-[190px]" title="{{ $thesis->pembimbing2->name }}">{{ $thesis->pembimbing2->name }}</span>
                                                @if($isMeP2)
                                                    <span class="text-[9px] font-bold text-orange-500 dark:text-orange-400 lowercase">(anda)</span>
                                                @endif
                                                <span class="font-mono text-[10px] text-slate-400 dark:text-slate-500 font-normal">({{ $p2Count }} sesi)</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Kolom 3: Status & Progres (PROGRES TARGET BIMBINGAN) -->
                                <td class="py-4 px-6 text-center align-middle">
                                    @php
                                        $stageInfo = $thesis->getCurrentAcademicStage();
                                        $hasSeminar = $thesis->hasPassedSeminar();
                                        $hasDefense = $thesis->hasPassedDefense();

                                        $completedCount = (int) $thesis->completed_sessions_count;
                                        $isP1 = ($thesis->pembimbing1_id === Auth::id());
                                        $isP2 = ($thesis->pembimbing2_id === Auth::id());
                                        $hasMyAccUp = $isP1 ? (bool) $thesis->acc_up_p1 : (bool) $thesis->acc_up_p2;
                                        $hasMyAccSidang = $isP1 ? (bool) $thesis->acc_sidang_p1 : (bool) $thesis->acc_sidang_p2;
                                        $isAccUpFinal = $thesis->acc_up_p1 && $thesis->acc_up_p2;
                                        $isAccSidangFinal = $thesis->acc_sidang_p1 && $thesis->acc_sidang_p2;

                                        $isGraduated = ($thesis->status === 'completed');

                                        $upTarget = 8;
                                        $sidangTarget = 16;
                                        $upPercent = $isGraduated ? 100 : min(100, round(($completedCount / $upTarget) * 100));
                                        $sidangPercent = $isGraduated ? 100 : min(100, round(($completedCount / $sidangTarget) * 100));

                                        $isTargetSidang = $hasSeminar;
                                        $p1Acc = $isTargetSidang ? $thesis->acc_sidang_p1 : $thesis->acc_up_p1;
                                        $p2Acc = $isTargetSidang ? $thesis->acc_sidang_p2 : $thesis->acc_up_p2;
                                        $targetAccLabel = $isTargetSidang ? 'Sidang' : 'Sempro';

                                        $lastSession = $thesis->mentoringSessions ? $thesis->mentoringSessions->first() : null;
                                        $lastDate = ($lastSession && $lastSession->scheduled_at) 
                                            ? \Carbon\Carbon::parse($lastSession->scheduled_at) 
                                            : ($thesis->created_at ? \Carbon\Carbon::parse($thesis->created_at) : null);

                                        if ($lastDate) {
                                            $daysSinceLast = (int) $lastDate->copy()->startOfDay()->diffInDays(now()->startOfDay());
                                            if ($lastDate->isToday()) {
                                                $lastActivityText = 'Hari ini';
                                            } elseif ($lastDate->isYesterday()) {
                                                $lastActivityText = 'Kemarin';
                                            } else {
                                                $lastActivityText = "{$daysSinceLast}h lalu";
                                            }
                                        } else {
                                            $daysSinceLast = null;
                                            $lastActivityText = 'Aktif';
                                        }
                                    @endphp

                                    <div class="flex flex-col items-center gap-2.5 min-w-[210px] max-w-[250px] mx-auto text-left">
                                        <!-- 1. Tahap Akademik Utama Mahasiswa -->
                                        <div class="w-full flex items-center justify-center">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold {{ $stageInfo['badge_class'] }} border shadow-2xs tracking-tight">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $stageInfo['dot_class'] }} shrink-0"></span>
                                                <span>{{ $stageInfo['label'] }}</span>
                                            </span>
                                        </div>

                                        <!-- 2. Milestone Stepper: Seminar & Sidang Tracker -->
                                        <div class="w-full inline-flex items-center justify-between p-1 bg-slate-100/80 dark:bg-slate-800/80 rounded-xl border border-slate-200/60 dark:border-slate-700/60 text-[10px]">
                                            <!-- Seminar Milestone -->
                                            @if($hasSeminar)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/60 shrink-0 shadow-2xs" title="Seminar Proposal: Selesai">
                                                    <svg class="w-2.5 h-2.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    <span>Sempro</span>
                                                </span>
                                            @elseif($thesis->isAccUpFinal())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 shrink-0 shadow-2xs" title="Seminar Proposal: ACC Lengkap, Siap Daftar">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    <span>ACC Seminar</span>
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-lg font-medium text-slate-400 dark:text-slate-500 shrink-0" title="Seminar Proposal: Belum">
                                                    Seminar : Belum
                                                </span>
                                            @endif

                                            <svg class="w-3 h-3 text-slate-300 dark:text-slate-600 shrink-0 mx-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>

                                            <!-- Sidang Milestone -->
                                            @if($hasDefense)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 shrink-0 shadow-2xs" title="Sidang Skripsi: Selesai">
                                                    <svg class="w-2.5 h-2.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    <span>Sidang</span>
                                                </span>
                                            @elseif($thesis->isAccSidangFinal())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60 shrink-0 shadow-2xs" title="Sidang Skripsi: ACC Lengkap, Siap Daftar">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                                    <span>ACC Sidang</span>
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-lg font-medium text-slate-400 dark:text-slate-500 shrink-0" title="Sidang Skripsi: Belum">
                                                    Sidang : Belum
                                                </span>
                                            @endif
                                        </div>

                                        <!-- 3. Dual Progress Bar Target UP (8 Sesi) & Sidang (16 Sesi) -->
                                        <div class="w-full space-y-2 pt-1 border-t border-slate-100 dark:border-slate-800/80">
                                            <!-- Target UP (8 Sesi) -->
                                            <div class="space-y-1">
                                                <div class="flex items-center justify-between text-[10px] font-bold">
                                                    <span class="text-slate-600 dark:text-slate-400 flex items-center gap-1.5" title="Target Seminar UP: 8 Sesi (4x P1 & 4x P2)">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $upPercent >= 100 || $hasMyAccUp ? 'bg-indigo-600 dark:bg-indigo-400' : ($upPercent > 0 ? 'bg-indigo-500 animate-pulse' : 'bg-slate-300 dark:bg-slate-600') }}"></span>
                                                        <span>Target UP</span>
                                                    </span>
                                                    <div class="flex items-center gap-1.5">
                                                        @if($hasMyAccUp || $isAccUpFinal || $isGraduated)
                                                            <span class="inline-flex items-center gap-0.5 text-[9px] font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded border border-emerald-200 dark:border-emerald-800" title="Sudah di-ACC Seminar Proposal">
                                                                <svg class="w-2.5 h-2.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                                <span>ACC UP</span>
                                                            </span>
                                                        @endif
                                                        <span class="font-mono text-[10px] {{ $completedCount >= 8 || $isGraduated ? 'text-indigo-600 dark:text-indigo-400 font-black' : ($completedCount > 0 ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400') }}">
                                                            {{ $isGraduated ? '8/8' : min($completedCount, 8) . '/8' }} Sesi
                                                            <span class="text-[9px] font-normal text-slate-400 dark:text-slate-500">({{ $upPercent }}%)</span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="w-full bg-slate-100 dark:bg-slate-700/80 rounded-full h-1.5 overflow-hidden border border-slate-200/50 dark:border-slate-700/50">
                                                    <div class="h-full rounded-full transition-all duration-500 {{ $upPercent >= 100 ? 'bg-indigo-600 dark:bg-indigo-500' : ($upPercent > 0 ? 'bg-indigo-500 dark:bg-indigo-400' : 'bg-transparent') }}" 
                                                         style="width: {{ $upPercent }}%; {{ $upPercent > 0 ? 'min-width: 6px;' : '' }}"></div>
                                                </div>
                                            </div>

                                            <!-- Target Sidang (16 Sesi) -->
                                            <div class="space-y-1">
                                                <div class="flex items-center justify-between text-[10px] font-bold">
                                                    <span class="text-slate-600 dark:text-slate-400 flex items-center gap-1.5" title="Target Sidang Skripsi: 16 Sesi (8x P1 & 8x P2)">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $sidangPercent >= 100 || $hasMyAccSidang ? 'bg-emerald-600 dark:bg-emerald-400' : ($sidangPercent > 0 ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600') }}"></span>
                                                        <span>Target Sidang</span>
                                                    </span>
                                                    <div class="flex items-center gap-1.5">
                                                        @if($hasMyAccSidang || $isAccSidangFinal || $isGraduated)
                                                            <span class="inline-flex items-center gap-0.5 text-[9px] font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded border border-emerald-200 dark:border-emerald-800" title="Sudah di-ACC Sidang Akhir">
                                                                <svg class="w-2.5 h-2.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                                <span>ACC Sidang</span>
                                                            </span>
                                                        @endif
                                                        <span class="font-mono text-[10px] {{ $completedCount >= 16 || $isGraduated ? 'text-emerald-600 dark:text-emerald-400 font-black' : ($completedCount > 0 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400') }}">
                                                            {{ $isGraduated ? '16/16' : min($completedCount, 16) . '/16' }} Sesi
                                                            <span class="text-[9px] font-normal text-slate-400 dark:text-slate-500">({{ $sidangPercent }}%)</span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="w-full bg-slate-100 dark:bg-slate-700/80 rounded-full h-1.5 overflow-hidden border border-slate-200/50 dark:border-slate-700/50">
                                                    <div class="h-full rounded-full transition-all duration-500 {{ $sidangPercent >= 100 ? 'bg-emerald-600 dark:bg-emerald-500' : ($sidangPercent > 0 ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-transparent') }}" 
                                                         style="width: {{ $sidangPercent }}%; {{ $sidangPercent > 0 ? 'min-width: 6px;' : '' }}"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 4. Rincian ACC Pembimbing (P1 & P2 Status) & Keaktifan Status -->
                                        <div class="w-full flex items-center justify-between gap-1.5 text-[10px] pt-1">
                                            <div class="flex items-center gap-1">
                                                <!-- P1 Status -->
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md font-bold border transition-all {{ $p1Acc ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-slate-200/60 dark:border-slate-700/60' }} {{ $isP1 ? 'ring-1 ring-orange-500/40' : '' }}"
                                                      title="Pembimbing 1 ACC {{ $targetAccLabel }}: {{ $p1Acc ? 'Sudah Disetujui' : 'Belum' }} {{ $isP1 ? '(Anda)' : '' }}">
                                                    <span class="text-[9px] font-black {{ $isP1 ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500' }}">P1</span>
                                                    @if($p1Acc)
                                                        <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    @else
                                                        <span class="text-slate-300 dark:text-slate-600 font-bold">—</span>
                                                    @endif
                                                </span>

                                                <!-- P2 Status -->
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md font-bold border transition-all {{ $p2Acc ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-slate-200/60 dark:border-slate-700/60' }} {{ $isP2 ? 'ring-1 ring-orange-500/40' : '' }}"
                                                      title="Pembimbing 2 ACC {{ $targetAccLabel }}: {{ $p2Acc ? 'Sudah Disetujui' : 'Belum' }} {{ $isP2 ? '(Anda)' : '' }}">
                                                    <span class="text-[9px] font-black {{ $isP2 ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500' }}">P2</span>
                                                    @if($p2Acc)
                                                        <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    @else
                                                        <span class="text-slate-300 dark:text-slate-600 font-bold">—</span>
                                                    @endif
                                                </span>
                                            </div>

                                            <!-- Health Status Pill / Milestone Pill -->
                                            <div class="shrink-0">
                                                @if($isGraduated)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800">
                                                        <span>🎓 Lulus</span>
                                                    </span>
                                                @elseif(($completedCount >= 16 || ($p1Count >= 8 && $p2Count >= 8)) && !$hasMyAccSidang)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800" title="Mahasiswa siap untuk di-ACC Sidang">
                                                        <span>🟢 Siap Sidang</span>
                                                    </span>
                                                @elseif(($completedCount >= 8 || ($p1Count >= 4 && $p2Count >= 4)) && !$hasMyAccUp)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800" title="Mahasiswa siap untuk di-ACC Seminar">
                                                        <span>🔵 Siap UP</span>
                                                    </span>
                                                @elseif($completedCount === 0)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800">
                                                        <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                                        <span>0 Sesi</span>
                                                    </span>
                                                @elseif($daysSinceLast !== null && $daysSinceLast > 14)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800"
                                                          title="Bimbingan terakhir: {{ $lastDate ? $lastDate->isoFormat('D MMMM Y') : '-' }}">
                                                        <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                                        <span>Pasif ({{ $daysSinceLast }}h)</span>
                                                    </span>
                                                @else
                                                    <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500"
                                                          title="Bimbingan terakhir: {{ $lastDate ? $lastDate->isoFormat('D MMMM Y') : '-' }}">
                                                        {{ $lastActivityText }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- 5. Tombol Shortcut Buka Logbook -->
                                        <div class="w-full flex justify-center pt-0.5">
                                            <a href="{{ route('theses.logbooks', $thesis->id) }}" 
                                               class="inline-flex items-center gap-1 text-[10px] font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 group/link transition-colors cursor-pointer"
                                               title="Buka catatan bimbingan & logbook {{ $thesis->student->name }}">
                                                <span>Buka Logbook</span>
                                                <svg class="w-3 h-3 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kolom 4: Aksi -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <!-- Tombol Pintasan Intip Riwayat (Quick Preview) -->
                                        <button type="button" 
                                                @click="$dispatch('open-logbook-preview', { thesisId: {{ $thesis->id }} })"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-50 hover:bg-orange-50 hover:text-orange-600 dark:bg-slate-800 dark:hover:bg-orange-950/40 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all shadow-2xs hover:shadow-xs group/btn cursor-pointer"
                                                title="Intip Riwayat Bimbingan Terakhir (3-5 Sesi)">
                                            <svg class="w-4 h-4 text-slate-400 group-hover/btn:text-orange-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span class="hidden sm:inline">Intip</span>
                                        </button>

                                        <!-- Tombol Lihat Logbook Lengkap -->
                                        <a href="{{ route('theses.logbooks', $thesis->id) }}" 
                                           class="inline-flex items-center gap-2 pl-4 pr-3.5 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300 dark:hover:bg-orange-950/40 dark:hover:text-orange-400 dark:hover:border-orange-800 rounded-xl text-xs font-bold transition-all shadow-2xs hover:shadow-xs group/btn whitespace-nowrap"
                                           title="Buka Catatan Logbook Lengkap">
                                            <span>Logbook</span>
                                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover/btn:text-orange-500 group-hover/btn:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <!-- BARIS ADMIN / KAPRODI (6 KOLOM ASLI) -->
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3.5">
                                    <a href="{{ route('theses.logbooks', $thesis->id) }}" class="relative w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200/80 dark:border-slate-700/80 bg-slate-100 dark:bg-slate-800 shrink-0 ring-2 ring-white dark:ring-slate-800 shadow-2xs hover:scale-105 transition-transform" title="Buka catatan bimbingan {{ $thesis->student->name }}">
                                        <img src="{{ $thesis->student->avatar_url }}" alt="{{ $thesis->student->name }}" class="w-full h-full object-cover">
                                    </a>
                                    <div class="space-y-1">
                                        <!-- Baris 1: Nama Mahasiswa -->
                                        <div>
                                            <a href="{{ route('theses.logbooks', $thesis->id) }}" class="font-bold text-slate-900 dark:text-white tracking-tight text-xs sm:text-sm hover:text-orange-600 dark:hover:text-orange-400 transition-colors" title="Buka catatan bimbingan {{ $thesis->student->name }}">
                                                {{ ucwords(strtolower($thesis->student->name)) }}
                                            </a>
                                        </div>
                                        
                                        @php
                                             $waNumber = \App\Helpers\PhoneHelper::formatForWhatsApp($thesis->student->phone_number);
                                             $dosenName = Auth::user()->name;
                                             $roleLabel = ($thesis->pembimbing1_id === Auth::id()) ? 'Pembimbing 1' : (($thesis->pembimbing2_id === Auth::id()) ? 'Pembimbing 2' : 'Dosen');
                                             $waMessage = urlencode("Halo {$thesis->student->name}, saya {$dosenName} ({$roleLabel}). Terkait bimbingan skripsi: \"" . ($thesis->final_title ?? $thesis->title) . "\"");
                                        @endphp

                                        <!-- Baris 2: Chip NPM & Tombol Kontak WhatsApp Sejajar -->
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <!-- Chip NPM -->
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs"
                                                  title="Nomor Pokok Mahasiswa (NPM)">
                                                <svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                                <span>{{ $thesis->student->identifier ?? 'NPM -' }}</span>
                                            </span>

                                            <!-- Tombol WhatsApp Shortcut -->
                                            @if($waNumber)
                                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-2xs hover:scale-105 active:scale-95 transition-all cursor-pointer"
                                                   title="Kirim Pesan WhatsApp ke {{ $thesis->student->name }} ({{ $thesis->student->phone_number }})">
                                                    <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400 fill-current shrink-0" viewBox="0 0 24 24">
                                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                                    </svg>
                                                    <span>Chat WA</span>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-medium bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 border border-slate-200/50 dark:border-slate-700/50" 
                                                      title="Nomor WhatsApp belum didaftarkan oleh mahasiswa">
                                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                    <span>No WA</span>
                                                </span>
                                            @endif

                                            <!-- Cohort Badge (Hanya untuk Admin/Kaprodi) -->
                                            @if(Auth::user()->role !== 'dosen' && $thesis->student->entry_year)
                                                <span class="text-slate-300 dark:text-slate-600 font-bold">•</span>
                                                @if($thesis->isOldCohort())
                                                    @if($thesis->student->is_critical_semester)
                                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/10 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300" title="Masa studi kritis (Semester {{ $thesis->student->current_semester }})">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                                            Angkatan Lama '{{ substr($thesis->student->entry_year, -2) }} (Smt {{ $thesis->student->current_semester }})
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300" title="Angkatan Lama / Senior (Semester {{ $thesis->student->current_semester }})">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                            Angkatan Lama '{{ substr($thesis->student->entry_year, -2) }} (Smt {{ $thesis->student->current_semester }})
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300" title="Angkatan Baru (Semester {{ $thesis->student->current_semester ?? '-' }})">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Angkatan {{ $thesis->student->entry_year }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 max-w-sm whitespace-normal">
                                @if($thesis->final_title)
                                    <div class="font-bold text-slate-900 dark:text-white line-clamp-2 text-xs sm:text-sm leading-snug tracking-tight" title="{{ $thesis->final_title }}">{{ $thesis->final_title }}</div>
                                    <div class="mt-1 flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400" title="Rencana awal: {{ $thesis->title }}">
                                        <span class="text-amber-500 shrink-0 font-bold">↳</span>
                                        <span class="italic line-clamp-1">Awal: {{ $thesis->title }}</span>
                                    </div>
                                @else
                                    <div class="font-bold text-slate-900 dark:text-white line-clamp-2 text-xs sm:text-sm leading-snug tracking-tight" title="{{ $thesis->title }}">{{ $thesis->title }}</div>
                                @endif

                                @php
                                    $rawTitle = trim($thesis->final_title ?? $thesis->title ?? '');
                                    $titleUpper = strtoupper($rawTitle);
                                    $hasValidTitle = !empty($rawTitle) && !in_array($titleUpper, ['BELUM DIKETAHUI', 'BELUM DITENTUKAN', 'BELUM ADA JUDUL', 'BELUM ADA', '-']) && strlen($rawTitle) >= 8;
                                    $simScore = $hasValidTitle ? $thesis->getMaxSimilarityScore() : 0;
                                    $simMatches = $hasValidTitle ? $thesis->getDetailedSimilarityMatches() : collect();
                                @endphp
                                @if($hasValidTitle)
                                    <div class="mt-2 inline-block">
                                        <button @click='$dispatch("open-audit-modal", {
                                            student: "{{ addslashes($thesis->student->name) }}",
                                            npm: "{{ $thesis->student->identifier }}",
                                            title: "{{ addslashes($rawTitle) }}",
                                            score: {{ $simScore }},
                                            matches: {{ json_encode($simMatches->values()->toArray()) }}
                                        })' class="group focus:outline-hidden cursor-pointer">
                                            @if($simScore >= 66)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 hover:bg-rose-500/20 dark:bg-rose-500/15 dark:hover:bg-rose-500/25 text-rose-700 dark:text-rose-300 transition-all hover:scale-105 active:scale-95" title="Klik untuk lihat rincian data kemiripan">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping shrink-0"></span>
                                                    <span>{{ $simScore }}% Sangat Mirip</span>
                                                    <svg class="w-3 h-3 opacity-60 ml-0.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                </span>
                                            @elseif($simScore >= 35)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 hover:bg-amber-500/20 dark:bg-amber-500/15 dark:hover:bg-amber-500/25 text-amber-700 dark:text-amber-300 transition-all hover:scale-105 active:scale-95" title="Klik untuk lihat rincian data kemiripan">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                                    <span>{{ $simScore }}% Kemiripan</span>
                                                    <svg class="w-3 h-3 opacity-60 ml-0.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 hover:bg-emerald-500/20 dark:bg-emerald-500/15 dark:hover:bg-emerald-500/25 text-emerald-700 dark:text-emerald-300 transition-all hover:scale-105 active:scale-95" title="Klik untuk lihat rincian data kemiripan">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                    <span>{{ 100 - $simScore }}% Unik</span>
                                                    <svg class="w-3 h-3 opacity-60 ml-0.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                </span>
                                            @endif
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 max-w-[14rem] whitespace-normal" x-data="{ openAbstract: false }">
                                @if($thesis->abstract)
                                    <div class="relative">
                                        <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed font-normal">"{{ Str::limit($thesis->abstract, 90) }}"</p>
                                        <button @click="openAbstract = true" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold mt-1.5 flex items-center gap-1 group/btn transition-colors cursor-pointer">
                                            <span>Lihat Detail Lengkap</span>
                                            <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                    </div>

                                    <!-- Abstract Modal -->
                                    <template x-teleport="body">
                                        <div x-show="openAbstract" class="fixed inset-0 overflow-y-auto" style="z-index: 99999 !important;" x-cloak x-transition>
                                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAbstract = false">
                                                    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                </div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <div class="inline-block align-middle bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-slate-200 dark:border-slate-700 relative" style="z-index: 100000 !important;">
                                                    <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                                                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 uppercase tracking-widest">Detail Deskripsi Skripsi</h3>
                                                        <button @click="openAbstract = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                    <div class="px-8 py-8 max-h-[60vh] overflow-y-auto">
                                                        <div class="mb-8">
                                                            <p class="text-[10px] font-bold text-orange-600 dark:text-orange-400 uppercase tracking-widest mb-2">Judul Pengajuan</p>
                                                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-tight uppercase">{{ $thesis->final_title ?? $thesis->title }}</h4>
                                                        </div>
                                                        <div class="p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                                            <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3">Deskripsi / Rencana</p>
                                                            <div class="text-xs text-slate-600 dark:text-slate-400 leading-loose text-justify font-medium whitespace-pre-line">
                                                                {{ $thesis->abstract }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                                                        <button type="button" @click="openAbstract = false" class="px-8 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm cursor-pointer">
                                                            Tutup
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center align-middle">
                                @php
                                    $stageInfo = $thesis->getCurrentAcademicStage();
                                    $hasSeminar = $thesis->hasPassedSeminar();
                                    $hasDefense = $thesis->hasPassedDefense();

                                    // Contextual ACC status (Sempro vs Sidang)
                                    $isTargetSidang = $hasSeminar;
                                    $p1Acc = $isTargetSidang ? $thesis->acc_sidang_p1 : $thesis->acc_up_p1;
                                    $p2Acc = $isTargetSidang ? $thesis->acc_sidang_p2 : $thesis->acc_up_p2;
                                    $targetAccLabel = $isTargetSidang ? 'Sidang' : 'Sempro';
                                    
                                    $isMyP1 = (Auth::id() === $thesis->pembimbing1_id);
                                    $isMyP2 = (Auth::id() === $thesis->pembimbing2_id);
                                @endphp

                                <div class="flex flex-col items-center gap-2 min-w-[190px] max-w-[230px] mx-auto">
                                    <!-- 1. Tahap Akademik Utama Mahasiswa -->
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold {{ $stageInfo['badge_class'] }} border shadow-2xs tracking-tight">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $stageInfo['dot_class'] }} shrink-0"></span>
                                        <span>{{ $stageInfo['label'] }}</span>
                                    </span>

                                    <!-- 2. Milestone Stepper: Seminar & Sidang Tracker -->
                                    <div class="w-full inline-flex items-center justify-between p-1 bg-slate-100/80 dark:bg-slate-800/80 rounded-xl border border-slate-200/60 dark:border-slate-700/60 text-[10px]">
                                        <!-- Seminar Milestone -->
                                        @if($hasSeminar)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/60 shrink-0 shadow-2xs" title="Seminar Proposal: Selesai">
                                                <svg class="w-2.5 h-2.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Sempro</span>
                                            </span>
                                        @elseif($thesis->isAccUpFinal())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 shrink-0 shadow-2xs" title="Seminar Proposal: ACC Lengkap, Siap Daftar">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>ACC Seminar</span>
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-lg font-medium text-slate-400 dark:text-slate-500 shrink-0" title="Seminar Proposal: Belum">
                                                Seminar : Belum
                                            </span>
                                        @endif

                                        <svg class="w-3 h-3 text-slate-300 dark:text-slate-600 shrink-0 mx-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>

                                        <!-- Sidang Milestone -->
                                        @if($hasDefense)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 shrink-0 shadow-2xs" title="Sidang Skripsi: Selesai">
                                                <svg class="w-2.5 h-2.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Sidang</span>
                                            </span>
                                        @elseif($thesis->isAccSidangFinal())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60 shrink-0 shadow-2xs" title="Sidang Skripsi: ACC Lengkap, Siap Daftar">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                                <span>ACC Sidang</span>
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-lg font-medium text-slate-400 dark:text-slate-500 shrink-0" title="Sidang Skripsi: Belum">
                                                Sidang : Belum
                                            </span>
                                        @endif
                                    </div>

                                    <!-- 3. Rincian ACC Pembimbing (P1 & P2 Status) -->
                                    @if($thesis->status !== 'completed' && !$hasDefense)
                                        <div class="flex items-center justify-center gap-1.5 w-full text-[10px]">
                                            <!-- P1 Status -->
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold border transition-all {{ $p1Acc ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-slate-200/60 dark:border-slate-700/60' }} {{ $isMyP1 ? 'ring-1 ring-orange-500/40' : '' }}"
                                                  title="Pembimbing 1 ACC {{ $targetAccLabel }}: {{ $p1Acc ? 'Sudah Disetujui' : 'Belum' }} {{ $isMyP1 ? '(Anda)' : '' }}">
                                                <span class="text-[9px] font-black {{ $isMyP1 ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500' }}">P1{{ $isMyP1 ? '*' : '' }}</span>
                                                @if($p1Acc)
                                                    <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <span class="text-slate-300 dark:text-slate-600 font-bold">—</span>
                                                @endif
                                            </span>

                                            <!-- P2 Status -->
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold border transition-all {{ $p2Acc ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-slate-200/60 dark:border-slate-700/60' }} {{ $isMyP2 ? 'ring-1 ring-orange-500/40' : '' }}"
                                                  title="Pembimbing 2 ACC {{ $targetAccLabel }}: {{ $p2Acc ? 'Sudah Disetujui' : 'Belum' }} {{ $isMyP2 ? '(Anda)' : '' }}">
                                                <span class="text-[9px] font-black {{ $isMyP2 ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500' }}">P2{{ $isMyP2 ? '*' : '' }}</span>
                                                @if($p2Acc)
                                                    <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <span class="text-slate-300 dark:text-slate-600 font-bold">—</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    <!-- 4. Keaktifan Bimbingan (Health & Recency) -->
                                    @if($thesis->status === 'active')
                                        @php
                                            $healthBadge = $thesis->mentoring_health_badge;
                                            $healthColor = $healthBadge['color'] ?? 'emerald';
                                            $healthBgClass = $healthColor === 'rose' 
                                                ? 'bg-rose-500/10 text-rose-700 dark:text-rose-300 border border-rose-500/20' 
                                                : ($healthColor === 'amber' ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20');
                                            $healthDotClass = $healthColor === 'rose' ? 'bg-rose-500 animate-pulse' : ($healthColor === 'amber' ? 'bg-amber-500' : 'bg-emerald-500');
                                        @endphp
                                        <div class="flex items-center justify-center">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $healthBgClass }}"
                                                  title="Aktivitas Bimbingan: Terakhir bimbingan {{ $thesis->days_since_last_mentoring ?? 0 }} hari lalu">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $healthDotClass }}"></span>
                                                <span>{{ $healthBadge['label'] }}</span>
                                            </span>
                                        </div>
                                    @endif

                                    <!-- 5. Tombol Shortcut ke Logbook Mahasiswa -->
                                    <a href="{{ route('theses.logbooks', $thesis->id) }}" 
                                       class="inline-flex items-center gap-1 text-[10px] font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 group/link transition-colors mt-0.5 cursor-pointer"
                                       title="Buka catatan bimbingan & logbook {{ $thesis->student->name }}">
                                        <span>Buka Logbook</span>
                                        <svg class="w-3 h-3 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                            
                            <td class="py-4 px-6 align-middle">
                                @if($thesis->pembimbing1 || $thesis->pembimbing2)
                                    <div class="flex flex-col gap-2">
                                        @if($thesis->pembimbing1)
                                            @php $isMeP1 = (Auth::id() === $thesis->pembimbing1_id); @endphp
                                            <div class="flex items-center gap-2">
                                                <span class="w-4 h-4 rounded {{ $isMeP1 ? 'bg-orange-500 text-white font-extrabold shadow-2xs' : 'bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30' }} flex items-center justify-center text-[9px] font-bold shrink-0">1</span>
                                                <span class="font-medium text-[10px] uppercase tracking-tight {{ $isMeP1 ? 'font-bold text-orange-600 dark:text-orange-400' : 'text-slate-700 dark:text-slate-300' }}">
                                                    {{ $thesis->pembimbing1->name }}
                                                    @if($isMeP1)
                                                        <span class="text-[9px] font-bold text-orange-500 dark:text-orange-400 lowercase">(anda)</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                        @if($thesis->pembimbing2)
                                            @php $isMeP2 = (Auth::id() === $thesis->pembimbing2_id); @endphp
                                            <div class="flex items-center gap-2">
                                                <span class="w-4 h-4 rounded {{ $isMeP2 ? 'bg-orange-500 text-white font-extrabold shadow-2xs' : 'bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30' }} flex items-center justify-center text-[9px] font-bold shrink-0">2</span>
                                                <span class="font-medium text-[10px] uppercase tracking-tight {{ $isMeP2 ? 'font-bold text-orange-600 dark:text-orange-400' : 'text-slate-700 dark:text-slate-300' }}">
                                                    {{ $thesis->pembimbing2->name }}
                                                    @if($isMeP2)
                                                        <span class="text-[9px] font-bold text-orange-500 dark:text-orange-400 lowercase">(anda)</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <x-status-badge type="slate" label="BELUM DITENTUKAN" />
                                @endif
                            </td>
                            
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                                <td class="py-4 px-6 text-right">
                                    @if(!$thesis->pembimbing1 || !$thesis->pembimbing2)
                                        <div x-data="{ openAssignModal: false }" class="inline-block whitespace-nowrap">
                                            <button @click="openAssignModal = true" class="inline-flex items-center justify-center gap-2.5 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-bold transition-all shadow-xs shadow-orange-500/20 hover:scale-[1.02] cursor-pointer whitespace-nowrap shrink-0">
                                                <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                <span>Tugaskan</span>
                                            </button>

                                            <!-- Modal Penugasan Pembimbing -->
                                            <template x-teleport="body">
                                                <div x-show="openAssignModal" class="fixed inset-0 overflow-y-auto text-left" style="z-index: 99999 !important;" x-cloak x-transition>
                                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAssignModal = false">
                                                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                        </div>
                                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative" style="z-index: 100000 !important;">
                                                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                                                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Penugasan Dosen Pembimbing</h3>
                                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase mt-1">Mahasiswa: {{ $thesis->student->name }} ({{ $thesis->student->identifier }})</p>
                                                            </div>
                                                            
                                                            <form action="{{ route('theses.assign', $thesis->id) }}" method="POST" class="p-8 space-y-5">
                                                                @csrf
                                                                <div x-data='{ 
                                                                    p1_id: "{{ $thesis->requested_pembimbing1_id }}", 
                                                                    p2_id: "{{ $thesis->requested_pembimbing2_id }}",
                                                                    dosens: {{ $dosens->mapWithKeys(fn($d) => [$d->id => $d])->toJson() }}
                                                                }' class="space-y-5">
                                                                    
                                                                    <!-- Suggestions Chips Header -->
                                                                    @if($thesis->requestedPembimbing1 || $thesis->requestedPembimbing2)
                                                                        <div class="p-4 bg-indigo-50/80 dark:bg-indigo-950/40 rounded-2xl border border-indigo-100 dark:border-indigo-900/50 space-y-2">
                                                                            <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest flex items-center gap-1.5">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                                                Usulan Pembimbing dari Mahasiswa:
                                                                            </span>
                                                                            <div class="flex flex-wrap gap-2">
                                                                                @if($thesis->requestedPembimbing1)
                                                                                    @php $p1Full = $thesis->requestedPembimbing1->total_workload >= $thesis->requestedPembimbing1->max_quota; @endphp
                                                                                    <div class="px-3 py-1.5 rounded-xl text-xs font-extrabold uppercase border flex items-center gap-2 {{ $p1Full ? 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50' : 'bg-white text-indigo-700 border-indigo-200 dark:bg-slate-900 dark:text-indigo-400 dark:border-indigo-800 shadow-2xs' }}">
                                                                                        <span>P1: {{ $thesis->requestedPembimbing1->name }}</span>
                                                                                        <span class="font-black">({{ $thesis->requestedPembimbing1->total_workload }}/{{ $thesis->requestedPembimbing1->max_quota }})</span>
                                                                                        @if($p1Full)<span class="text-rose-600 font-black">🔴 Penuh</span>@endif
                                                                                    </div>
                                                                                @endif
                                                                                @if($thesis->requestedPembimbing2)
                                                                                    @php $p2Full = $thesis->requestedPembimbing2->total_workload >= $thesis->requestedPembimbing2->max_quota; @endphp
                                                                                    <div class="px-3 py-1.5 rounded-xl text-xs font-extrabold uppercase border flex items-center gap-2 {{ $p2Full ? 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50' : 'bg-white text-indigo-700 border-indigo-200 dark:bg-slate-900 dark:text-indigo-400 dark:border-indigo-800 shadow-2xs' }}">
                                                                                        <span>P2: {{ $thesis->requestedPembimbing2->name }}</span>
                                                                                        <span class="font-black">({{ $thesis->requestedPembimbing2->total_workload }}/{{ $thesis->requestedPembimbing2->max_quota }})</span>
                                                                                        @if($p2Full)<span class="text-rose-600 font-black">🔴 Penuh</span>@endif
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Select P1 -->
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pilih Dosen Pembimbing 1</label>
                                                                        <select name="pembimbing1_id" x-model="p1_id" required class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all text-slate-800 dark:text-slate-100">
                                                                            <option value="">Pilih P1...</option>
                                                                            @foreach($dosens as $dosen)
                                                                                @php 
                                                                                    $score = $thesis->getMatchScore($dosen);
                                                                                    $isFull = $dosen->total_workload >= $dosen->max_quota;
                                                                                @endphp
                                                                                <option value="{{ $dosen->id }}" class="{{ $isFull ? 'text-rose-500 font-bold' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-600' : 'text-slate-800') }}">
                                                                                    {{ $isFull ? '🔴 [PENUH] ' : '' }}{{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <template x-if="p1_id && dosens[p1_id]">
                                                                            <div class="mt-1.5 flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'text-rose-600 dark:text-rose-400' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400')">
                                                                                <span class="w-2 h-2 rounded-full shrink-0" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'bg-rose-500' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                                                                                <span x-text="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'Quota Penuh (' + dosens[p1_id].total_workload + '/' + dosens[p1_id].max_quota + ')' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia (' + dosens[p1_id].total_workload + '/' + dosens[p1_id].max_quota + ')')"></span>
                                                                            </div>
                                                                        </template>
                                                                    </div>

                                                                    <!-- Select P2 -->
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pilih Dosen Pembimbing 2</label>
                                                                        <select name="pembimbing2_id" x-model="p2_id" required class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all text-slate-800 dark:text-slate-100">
                                                                            <option value="">Pilih P2...</option>
                                                                            @foreach($dosens as $dosen)
                                                                                @php 
                                                                                    $score = $thesis->getMatchScore($dosen);
                                                                                    $isFull = $dosen->total_workload >= $dosen->max_quota;
                                                                                @endphp
                                                                                <option value="{{ $dosen->id }}" class="{{ $isFull ? 'text-rose-500 font-bold' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-600' : 'text-slate-800') }}">
                                                                                    {{ $isFull ? '🔴 [PENUH] ' : '' }}{{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <template x-if="p2_id && dosens[p2_id]">
                                                                            <div class="mt-1.5 flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'text-rose-600 dark:text-rose-400' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400')">
                                                                                <span class="w-2 h-2 rounded-full shrink-0" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'bg-rose-500' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                                                                                <span x-text="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'Quota Penuh (' + dosens[p2_id].total_workload + '/' + dosens[p2_id].max_quota + ')' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia (' + dosens[p2_id].total_workload + '/' + dosens[p2_id].max_quota + ')')"></span>
                                                                            </div>
                                                                        </template>
                                                                    </div>

                                                                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                                                                        <button type="button" @click="openAssignModal = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors">Batal</button>
                                                                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 transition-all">Simpan Penugasan</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    @else
                                        <div class="flex justify-end items-center gap-2 shrink-0 whitespace-nowrap" x-data="{ openEditModal: false }">
                                            <!-- Rollback Button -->
                                            <button type="button"
                                                    @click="openRollbackModal('{{ $thesis->id }}', '{{ addslashes($thesis->student->name ?? 'Mahasiswa') }}', '{{ addslashes($thesis->pembimbing1?->name ?? '-') }}', '{{ addslashes($thesis->pembimbing2?->name ?? '-') }}')" 
                                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-800 rounded-xl text-xs font-bold transition-all shadow-2xs hover:scale-[1.02] active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
                                                    title="Rollback / Batalkan Penugasan Pembimbing">
                                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                <span>Rollback</span>
                                            </button>

                                            <!-- Edit Button -->
                                            <button type="button"
                                                    @click="openEditModal = true" 
                                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-2xs hover:scale-[1.02] active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
                                                    title="Edit Judul & Data Skripsi">
                                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                <span>Edit</span>
                                            </button>

                                            <!-- Logbook Button -->
                                            <a href="{{ route('theses.logbooks', $thesis->id) }}" 
                                               class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-bold transition-all shadow-xs shadow-orange-500/20 hover:scale-[1.02] cursor-pointer whitespace-nowrap shrink-0"
                                               title="Lihat Logbook Bimbingan">
                                                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                                <span>Logbook</span>
                                            </a>

                                            <!-- Edit Modal -->
                                            <template x-teleport="body">
                                                <div x-show="openEditModal" class="fixed inset-0 overflow-y-auto text-left" style="z-index: 99999 !important;" x-cloak x-transition>
                                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openEditModal = false">
                                                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                        </div>
                                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative" style="z-index: 100000 !important;">
                                                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                                                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Edit Data Skripsi</h3>
                                                                <p class="text-[10px] text-slate-500 uppercase font-black mt-1">Mahasiswa: {{ $thesis->student->name }}</p>
                                                            </div>
                                                            <form action="{{ route('theses.update', $thesis->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="px-8 py-8 space-y-6">
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Judul Final Skripsi</label>
                                                                        <textarea name="final_title" rows="3" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all uppercase leading-relaxed p-4 text-slate-800 dark:text-slate-100" required>{{ $thesis->final_title ?? $thesis->title }}</textarea>
                                                                    </div>
                                                                    <div x-data='{ 
                                                                        p1_id: "{{ $thesis->pembimbing1_id }}", 
                                                                        p2_id: "{{ $thesis->pembimbing2_id }}",
                                                                        dosens: {{ $dosens->mapWithKeys(fn($d) => [$d->id => $d])->toJson() }}
                                                                    }' class="grid grid-cols-2 gap-4">
                                                                        <div>
                                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pembimbing 1</label>
                                                                            <select name="pembimbing1_id" x-model="p1_id" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all p-3 text-slate-800 dark:text-slate-100" required>
                                                                                @foreach($dosens as $dosen)
                                                                                    @php $score = $thesis->getMatchScore($dosen); @endphp
                                                                                    <option value="{{ $dosen->id }}" 
                                                                                            class="{{ $dosen->total_workload >= $dosen->max_quota ? 'text-rose-500' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500') }}">
                                                                                        {{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <template x-if="p1_id && dosens[p1_id]">
                                                                                <div class="mt-2 flex items-center gap-1.5">
                                                                                    <div class="w-2 h-2 rounded-full" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'bg-rose-500' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></div>
                                                                                    <span class="text-[9px] font-black uppercase tracking-widest" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'text-rose-500' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500')">
                                                                                        <span x-text="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'Quota Penuh' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia')"></span>
                                                                                    </span>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                        <div>
                                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pembimbing 2</label>
                                                                            <select name="pembimbing2_id" x-model="p2_id" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all p-3 text-slate-800 dark:text-slate-100" required>
                                                                                @foreach($dosens as $dosen)
                                                                                    @php $score = $thesis->getMatchScore($dosen); @endphp
                                                                                    <option value="{{ $dosen->id }}" 
                                                                                            class="{{ $dosen->total_workload >= $dosen->max_quota ? 'text-rose-500' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500') }}">
                                                                                        {{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <template x-if="p2_id && dosens[p2_id]">
                                                                                <div class="mt-2 flex items-center gap-1.5">
                                                                                    <div class="w-2 h-2 rounded-full" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'bg-rose-500' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></div>
                                                                                    <span class="text-[9px] font-black uppercase tracking-widest" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'text-rose-500' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500')">
                                                                                        <span x-text="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'Quota Penuh' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia')"></span>
                                                                                    </span>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                                                    <button type="button" @click="openEditModal = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors">Batal</button>
                                                                    <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-500/20 transition-all">Simpan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    @endif
                                </td>
                            @endif
                        </tr>
                        @endif
                    @empty
                        <x-empty-state colspan="{{ (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') ? '6' : (Auth::user()->role === 'dosen' ? '4' : '5') }}" description="Belum ada data skripsi yang ditemukan." />
                    @endforelse
                </tbody>
            </table>
        </x-table-card>

        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
            <!-- MODAL: KONFIRMASI ROLLBACK PENUGASAN PEMBIMBING -->
            <template x-teleport="body">
                <div x-show="rollbackModalOpen" 
                     class="fixed inset-0 overflow-y-auto" 
                     style="z-index: 99999 !important;" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity" 
                         @click="rollbackModalOpen = false" 
                         aria-hidden="true"></div>

                    <!-- Centered Modal Box -->
                    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 text-center">
                        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 relative z-50 my-auto transform transition-all"
                             @click.stop>
                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-rose-50/50 dark:bg-rose-950/30 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-rose-800 dark:text-rose-200 uppercase tracking-widest">Konfirmasi Rollback Pembimbing</h3>
                                    <p class="text-[10px] text-rose-600 dark:text-rose-400 font-bold uppercase mt-0.5">Kembalikan status skripsi ke Menunggu</p>
                                </div>
                            </div>

                            <form :action="'/theses/' + rollbackData.id + '/unassign'" method="POST" class="p-8 space-y-5">
                                @csrf
                                <div class="space-y-3">
                                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                                        Apakah Anda yakin ingin membatalkan penugasan dosen pembimbing untuk mahasiswa <strong class="text-slate-800 dark:text-white uppercase" x-text="rollbackData.student"></strong>?
                                    </p>
                                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-1.5 text-[11px]">
                                        <div class="flex justify-between">
                                            <span class="text-slate-400 uppercase font-black text-[9px]">Pembimbing 1 Saat Ini:</span>
                                            <span class="font-extrabold text-slate-700 dark:text-slate-200" x-text="rollbackData.p1"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-400 uppercase font-black text-[9px]">Pembimbing 2 Saat Ini:</span>
                                            <span class="font-extrabold text-slate-700 dark:text-slate-200" x-text="rollbackData.p2"></span>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-amber-600 dark:text-amber-400 font-bold italic">
                                        * Status skripsi akan dikembalikan ke 'pending', kuota pembimbing akan dilepaskan, dan seluruh riwayat ACC awal akan di-reset.
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                                    <button type="button" @click="rollbackModalOpen = false" class="px-5 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-md shadow-rose-500/20 cursor-pointer">
                                        Ya, Rollback Penugasan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        @endif

        @if(Auth::user()->role === 'dosen')
            <!-- Quick Preview Slide-over Drawer (Alpine.js) -->
            <div x-data="quickPreviewDrawer()" 
                 @open-logbook-preview.window="fetchPreview($event.detail.thesisId)"
                 @keydown.escape.window="if(open) closeDrawer()"
                 class="relative"
                 x-cloak>
                <template x-teleport="body">
                    <div x-show="open" class="relative" style="z-index: 99999 !important;">
                        <!-- Backdrop -->
                        <div x-show="open"
                             x-transition:enter="ease-in-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in-out duration-300"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click="closeDrawer()"
                             class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

                        <!-- Slide-over Container -->
                        <div class="fixed inset-y-0 right-0 max-w-full flex pl-6 sm:pl-10">
                            <div x-show="open"
                                 x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                                 x-transition:enter-start="translate-x-full"
                                 x-transition:enter-end="translate-x-0"
                                 x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                                 x-transition:leave-start="translate-x-0"
                                 x-transition:leave-end="translate-x-full"
                                 class="w-screen max-w-md sm:max-w-lg bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200 dark:border-slate-800 flex flex-col h-full relative">

                                <!-- Drawer Header -->
                                <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 flex items-start justify-between shrink-0">
                                    <div class="flex items-center gap-3 min-w-0 pr-3">
                                        <div class="w-10 h-10 rounded-2xl bg-orange-50 dark:orange-950/50 border border-orange-200 dark:border-orange-800 flex items-center justify-center shrink-0 shadow-2xs">
                                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 tracking-tight">Intip Logbook</h3>
                                                <span x-show="data && data.total_completed !== undefined" 
                                                      class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800"
                                                      x-text="(data ? data.total_completed : 0) + ' Sesi Selesai'"></span>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">3–5 sesi bimbingan terakhir beserta arahan revisi</p>
                                        </div>
                                    </div>
                                    <button type="button"
                                            @click="closeDrawer()" 
                                            class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer"
                                            title="Tutup (Esc)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Student Profile Banner (When Loaded) -->
                                <template x-if="data">
                                    <div class="px-5 py-3.5 bg-slate-100/70 dark:bg-slate-800/60 border-b border-slate-200/70 dark:border-slate-700/60 flex flex-col gap-2 shrink-0">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <template x-if="data.student_avatar">
                                                    <img :src="data.student_avatar" :alt="data.student_name" class="w-9 h-9 rounded-full object-cover border border-slate-300 dark:border-slate-600 shrink-0 shadow-2xs">
                                                </template>
                                                <template x-if="!data.student_avatar">
                                                    <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs flex items-center justify-center shrink-0" x-text="data.student_name ? data.student_name.charAt(0).toUpperCase() : 'M'"></div>
                                                </template>
                                                <div class="min-w-0">
                                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate" x-text="data.student_name"></h4>
                                                    <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400" x-text="'NPM: ' + data.student_identifier"></p>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full"
                                                      :class="data.thesis_status === 'completed' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300'"
                                                      x-text="data.thesis_status === 'completed' ? 'Lulus' : 'Bimbingan Aktif'"></span>
                                            </div>
                                        </div>
                                        <!-- Supervisor Breakdown Banner -->
                                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between gap-2 text-[11px] flex-wrap">
                                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">P1</span>
                                                <span class="font-medium truncate max-w-[140px]" x-text="data.pembimbing1_name"></span>
                                                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 font-mono" x-text="'(' + data.p1_completed + ' sesi)'"></span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">P2</span>
                                                <span class="font-medium truncate max-w-[140px]" x-text="data.pembimbing2_name"></span>
                                                <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 font-mono" x-text="'(' + data.p2_completed + ' sesi)'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Content Body (Scrollable) -->
                                <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6">

                                    <!-- Skeleton Loading State -->
                                    <div x-show="loading" class="space-y-4 animate-pulse">
                                        <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-100 dark:bg-slate-800/50">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                                            <div class="space-y-1.5 flex-1">
                                                <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-1/3"></div>
                                                <div class="h-2.5 bg-slate-200 dark:bg-slate-700 rounded w-2/3"></div>
                                            </div>
                                        </div>
                                        <div class="space-y-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800">
                                            <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-1/4"></div>
                                            <div class="h-3.5 bg-slate-200 dark:bg-slate-700 rounded w-3/4"></div>
                                            <div class="h-16 bg-slate-200 dark:bg-slate-700 rounded w-full"></div>
                                        </div>
                                        <div class="space-y-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800">
                                            <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-1/4"></div>
                                            <div class="h-3.5 bg-slate-200 dark:bg-slate-700 rounded w-3/4"></div>
                                            <div class="h-16 bg-slate-200 dark:bg-slate-700 rounded w-full"></div>
                                        </div>
                                    </div>

                                    <!-- Error State -->
                                    <div x-show="!loading && error" class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs">
                                        <div class="font-bold mb-1">Gagal Memuat Data</div>
                                        <p x-text="error"></p>
                                    </div>

                                    <!-- Empty State (0 Sessions) -->
                                    <div x-show="!loading && !error && data && data.sessions && data.sessions.length === 0" 
                                         class="py-12 text-center space-y-3">
                                        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="font-bold text-sm text-slate-700 dark:text-slate-300">Belum Ada Sesi Bimbingan Selesai</div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto leading-relaxed">
                                            Mahasiswa ini belum memiliki catatan riwayat bimbingan yang telah dituntaskan dan diverifikasi.
                                        </p>
                                    </div>

                                    <!-- Timeline List of Sessions (Loaded) -->
                                    <div x-show="!loading && !error && data && data.sessions && data.sessions.length > 0" 
                                         class="space-y-6">

                                        <!-- Thesis Title Card -->
                                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-100/80 dark:bg-slate-800/50">
                                            <div class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 mb-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                <span>Judul Skripsi</span>
                                            </div>
                                            <p class="text-xs sm:text-[13px] font-bold text-slate-800 dark:text-slate-100 leading-relaxed" x-text="data ? data.thesis_title : ''"></p>
                                        </div>

                                        <div class="relative border-l-2 border-slate-200 dark:border-slate-700/80 ml-3.5 sm:ml-4 space-y-8 pb-4">
                                            <template x-for="session in (data ? data.sessions : [])" :key="session.id">
                                                <div class="relative pl-7 sm:pl-8">
                                                    <!-- Timeline Dot -->
                                                    <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-orange-500 ring-4 ring-white dark:ring-slate-900 shadow-xs"></div>

                                                    <!-- Session Header -->
                                                    <div class="flex items-center justify-between gap-2 mb-2">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-orange-100 dark:bg-orange-950/70 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800"
                                                                  x-text="'Sesi #' + session.session_number"></span>
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                                                  :class="session.dosen_role === 'Pembimbing 1' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800' : 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800'">
                                                                <span class="w-1.5 h-1.5 rounded-full" :class="session.dosen_role === 'Pembimbing 1' ? 'bg-indigo-500' : 'bg-purple-500'"></span>
                                                                <span x-text="session.dosen_role + ': ' + session.dosen_name"></span>
                                                                <span x-show="session.is_my_session" class="text-[9px] font-black opacity-80">(Anda)</span>
                                                            </span>
                                                            <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-400" x-text="'• ' + session.time_ago"></span>
                                                        </div>
                                                        <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400" x-text="session.scheduled_at"></span>
                                                    </div>

                                                    <!-- Topic & Type -->
                                                    <div class="space-y-1.5 mb-3">
                                                        <h5 class="text-sm sm:text-[15px] font-bold text-slate-900 dark:text-white leading-snug" x-text="session.topic"></h5>
                                                        <div class="flex items-center gap-2 text-xs">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded font-semibold text-[10px]"
                                                                  :class="session.type === 'online' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                                                  x-text="session.type === 'online' ? 'Online' : 'Offline'"></span>
                                                            <span x-show="session.location" class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[220px]" x-text="session.location"></span>
                                                        </div>
                                                    </div>

                                                    <!-- Feedback Pembimbing Box (Primary) -->
                                                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 dark:bg-amber-500/10 space-y-3">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <div class="flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-amber-900 dark:text-amber-300">
                                                                <span>📝 Catatan & Arahan Pembimbing</span>
                                                            </div>
                                                            <span class="text-[10px] font-bold text-amber-800 dark:text-amber-300 bg-amber-500/20 dark:bg-amber-500/20 px-2.5 py-0.5 rounded-lg shrink-0" 
                                                                  x-text="session.dosen_name + ' (' + session.dosen_role + ')'"></span>
                                                        </div>
                                                        <p class="text-xs sm:text-[13px] text-slate-800 dark:text-slate-200 leading-relaxed font-normal whitespace-pre-wrap"
                                                           x-text="session.feedback ? session.feedback : 'Tidak ada catatan khusus pada sesi ini.'"></p>
                                                    </div>

                                                    <!-- Student Notes (If Any) -->
                                                    <template x-if="session.notes">
                                                        <div class="mt-6 sm:mt-7 p-4 sm:p-5 rounded-2xl bg-slate-100/90 dark:bg-slate-800/60 space-y-3">
                                                            <div class="flex items-center justify-between gap-2">
                                                                <div class="flex items-center gap-2 text-[11px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                                                                    <div class="w-6 h-6 rounded-lg bg-slate-200/80 dark:bg-slate-700/80 flex items-center justify-center shrink-0 text-slate-600 dark:text-slate-300">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                        </svg>
                                                                    </div>
                                                                    <span>Catatan Mahasiswa</span>
                                                                </div>
                                                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 bg-slate-200/80 dark:bg-slate-700/80 px-2.5 py-0.5 rounded-lg">
                                                                    Agenda / Persiapan
                                                                </span>
                                                            </div>
                                                            <p class="text-xs sm:text-[13px] text-slate-700 dark:text-slate-200 leading-relaxed font-normal whitespace-pre-wrap" x-text="session.notes"></p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex items-center justify-between gap-3 shrink-0">
                                    <button type="button" 
                                            @click="closeDrawer()" 
                                            class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer">
                                        Tutup
                                    </button>

                                    <template x-if="data && data.full_logbook_url">
                                        <a :href="data.full_logbook_url" 
                                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-bold transition-all shadow-xs shadow-orange-500/20 hover:scale-[1.02] cursor-pointer whitespace-nowrap">
                                            <span>Buka Logbook Lengkap</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <script>
                function quickPreviewDrawer() {
                    return {
                        open: false,
                        loading: false,
                        error: null,
                        data: null,

                        async fetchPreview(thesisId) {
                            this.open = true;
                            this.loading = true;
                            this.error = null;
                            this.data = null;
                            document.body.classList.add('overflow-hidden');

                            try {
                                const res = await fetch(`/theses/${thesisId}/logbooks/quick-preview`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                if (!res.ok) {
                                    const errData = await res.json().catch(() => ({}));
                                    throw new Error(errData.error || 'Gagal memuat riwayat logbook.');
                                }
                                this.data = await res.json();
                            } catch (e) {
                                this.error = e.message || 'Terjadi kesalahan saat mengambil riwayat bimbingan.';
                            } finally {
                                this.loading = false;
                            }
                        },

                        closeDrawer() {
                            this.open = false;
                            document.body.classList.remove('overflow-hidden');
                        }
                    }
                }
            </script>
        @endif
    </div>
</x-app-layout>

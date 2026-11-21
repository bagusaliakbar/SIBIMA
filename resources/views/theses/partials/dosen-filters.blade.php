        @if(Auth::user()->role === 'dosen')
            <!-- TAB FILTER CEPAT (Quick Filter Dropdowns Toolbar Dosen) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 sm:p-5 shadow-xs relative z-20">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <!-- Left: Title & Subtitle -->
                    <div class="flex items-center gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                                    Tab Filter
                                </h3>
                                @if($hasActiveDosenFilters)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[11px] font-black bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400 border border-orange-200 dark:border-orange-800/60">
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
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ $isRoleActive ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
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
                                
                                <div class="px-3 py-2 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
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
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ empty($roleFilter) || $roleFilter === 'all' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
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
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ ($roleFilter ?? '') === 'p1' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
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
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ ($roleFilter ?? '') === 'p2' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
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
                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ $isProgressStalled ? 'bg-rose-200/80 dark:bg-rose-500/30 text-rose-800 dark:text-rose-200' : ($isProgressActive ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300') }}">
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
                                    
                                    <div class="px-3 py-2 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
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
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ empty($filter) || $filter === 'all' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
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
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal">&lt; 8 sesi (belum 4x P1 &amp; 4x P2)</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ ($filter ?? '') === 'proposal' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
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
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal">&ge; 8 sesi (4x P1 &amp; 4x P2)</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ ($filter ?? '') === 'ready_up' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
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
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal">&ge; 16 sesi (8x P1 &amp; 8x P2)</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ ($filter ?? '') === 'ready_sidang' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
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
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal">&gt; 14 hari tanpa bimbingan</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ ($filter ?? '') === 'stalled' ? 'bg-rose-200/80 dark:bg-rose-500/30 text-rose-800 dark:text-rose-200' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' }}">
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
                                
                                <div class="px-3 py-2 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
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

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
                                <span>Angkatan Baru</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300">
                                    {{ $cohortCounts['new'] ?? 0 }}
                                </span>
                            @elseif(($cohortFilter ?? 'all') === 'old')
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                <span>Angkatan Lama</span>
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
                                            <span>Angkatan Baru</span>
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
                                            <span>Angkatan Lama</span>
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
                                            <span>Siap Seminar UP</span>
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
                                            <span>Siap Sidang Akhir</span>
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
                                            <span>ACC Parsial (1/2)</span>
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

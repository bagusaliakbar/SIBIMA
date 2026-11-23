<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => Auth::user()->role === 'dosen' ? 'Daftar Mahasiswa Bimbingan' : 'Daftar Pengajuan Skripsi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6" x-data="{ 
        pageLoading: false,
        rollbackModalOpen: false,
        rollbackData: { id: null, student: '', p1: '', p2: '' },
        openRollbackModal(id, student, p1, p2) {
            this.rollbackData = { id: id, student: student, p1: p1, p2: p2 };
            this.rollbackModalOpen = true;
        }
    }">
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

        @include('theses.partials.status-tabs')

        @include('theses.partials.dosen-filters')

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
                                        <span class="px-1.5 py-0.5 rounded-full bg-emerald-600 text-white text-[11px] font-black">
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

            @include('theses.partials.admin-subtoolbar')

            <!-- Table Skeleton Shimmer -->
            <div x-show="pageLoading" x-cloak>
                <x-skeleton type="table" :rows="6" />
            </div>

            <table x-show="!pageLoading"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0"
                   x-transition:enter-end="opacity-100"
                   class="hidden sm:table w-full text-left text-sm whitespace-nowrap">
                <thead>
                    @if(Auth::user()->role === 'dosen')
                        <tr class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-xs border-b border-slate-100 dark:border-slate-700/80">
                            <th class="sibima-sticky-th py-3.5 px-6 font-semibold tracking-wider bg-slate-50 dark:bg-slate-900">MAHASISWA</th>
                            <th class="py-3.5 px-6 font-semibold tracking-wider bg-slate-50 dark:bg-slate-900">RENCANA JUDUL SKRIPSI</th>
                            <th class="py-3.5 px-6 font-semibold tracking-wider text-center bg-slate-50 dark:bg-slate-900">
                                <span class="block">Status &amp; Progres</span>
                                <span class="block text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-widest font-black">PROGRES TARGET BIMBINGAN</span>
                            </th>
                            <th class="py-3.5 px-6 font-semibold tracking-wider text-right bg-slate-50 dark:bg-slate-900">AKSI</th>
                        </tr>
                    @else
                        <tr class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-[11px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                            <th class="sibima-sticky-th py-4 px-6">Mahasiswa</th>
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
                            @include('theses.partials.table-row-dosen', ['thesis' => $thesis])
                        @else
                            @include('theses.partials.table-row-admin', ['thesis' => $thesis])
                        @endif
                    @empty
                        <x-empty-state colspan="{{ (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') ? '6' : (Auth::user()->role === 'dosen' ? '4' : '5') }}" description="Belum ada data skripsi yang ditemukan." />
                    @endforelse
                </tbody>
            </table>

            <!-- MOBILE VIEW: RESPONSIVE STACKED CARDS (< sm / 640px) -->
            <div x-show="!pageLoading" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="block sm:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($theses as $thesis)
                    @if(Auth::user()->role === 'dosen')
                        @include('theses.partials.mobile-card-dosen', ['thesis' => $thesis])
                    @else
                        @include('theses.partials.mobile-card-admin', ['thesis' => $thesis])
                    @endif
                @empty
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-500 dark:text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum ada data skripsi</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Tidak ada data skripsi yang sesuai dengan filter aktif.</p>
                    </div>
                @endforelse
            </div>
        </x-table-card>

        @include('theses.partials.rollback-modal')

        @include('theses.partials.quick-preview-drawer')
    </div>
</x-app-layout>

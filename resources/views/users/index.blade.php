<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Manajemen Pengguna', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full mx-auto" x-data="{ 
        pageLoading: true,
        openImportModal: false, 
        openDetailModal: false, 
        selectedUser: null,
        selectedUserIds: [],
        openBulkDeleteModal: false,
        pageUserIds: {{ json_encode($users->filter(fn($u) => $u->role !== 'admin')->pluck('id')->map(fn($id) => (int)$id)->values()) }},
        toggleSelectAll() {
            if (this.selectedUserIds.length === this.pageUserIds.length) {
                this.selectedUserIds = [];
            } else {
                this.selectedUserIds = [...this.pageUserIds];
            }
        },
        isAllSelected() {
            return this.pageUserIds.length > 0 && this.selectedUserIds.length === this.pageUserIds.length;
        },
        isSelected(id) {
            return this.selectedUserIds.map(Number).includes(Number(id));
        }
    }" x-init="$nextTick(() => { setTimeout(() => pageLoading = false, 150) })">
        @if(session('skippedDetails'))
            <div class="mb-6 p-6 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 rounded-2xl shadow-sm relative overflow-hidden transition-all duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/5 rounded-full -mr-12 -mt-12 opacity-50"></div>
                <div class="flex items-center gap-4 mb-4 z-10 relative">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-widest text-slate-800 dark:text-slate-100">Detail Baris yang Dilewati ({{ count(session('skippedDetails')) }} Baris)</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase mt-0.5 tracking-tight">Data berikut diabaikan demi menjaga validitas data di database:</p>
                    </div>
                </div>
                <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-900/30 z-10 relative">
                    @foreach(session('skippedDetails') as $detail)
                        <div class="px-4 py-3.5 flex flex-wrap justify-between items-center gap-2 text-[11px] text-slate-600 dark:text-slate-400 font-bold uppercase tracking-wider">
                            <span class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Baris {{ $detail['row'] }} (ID/NPM: <span class="font-mono text-slate-700 dark:text-slate-300">{{ $detail['identifier'] }}</span>)
                            </span>
                            <span class="text-[11px] px-2.5 py-1 bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 rounded-lg border border-rose-100 dark:border-rose-900/20 font-black tracking-widest">{{ $detail['reason'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @include('users.partials.kpi-cards')

        <!-- Status Tabs Navigation -->
        <div class="flex items-center gap-1 border-b border-slate-100 dark:border-slate-800 overflow-x-auto pb-px custom-scrollbar mb-6">
            <a href="{{ route('users.index', ['status' => 'all', 'role' => $role ?? 'all', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'search' => $search, 'per_page' => $perPage]) }}" 
               class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'all' ? 'border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50/50 dark:bg-orange-500/10 font-bold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span>Semua Pengguna</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-black {{ ($status ?? 'all') === 'all' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                    {{ $statusCounts['all'] ?? 0 }}
                </span>
            </a>
            <a href="{{ route('users.index', ['status' => 'active', 'role' => $role ?? 'all', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'search' => $search, 'per_page' => $perPage]) }}" 
               class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'active' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-500/10 font-bold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Aktif</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-black {{ ($status ?? 'all') === 'active' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                    {{ $statusCounts['active'] ?? 0 }}
                </span>
            </a>
            <a href="{{ route('users.index', ['status' => 'pending', 'role' => $role ?? 'all', 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '', 'search' => $search, 'per_page' => $perPage]) }}" 
               class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'pending' ? 'border-amber-500 text-amber-600 dark:text-amber-400 bg-amber-50/50 dark:bg-amber-500/10 font-bold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span class="w-2 h-2 rounded-full bg-amber-500 {{ ($statusCounts['pending'] ?? 0) > 0 ? 'animate-pulse' : '' }}"></span>
                <span>Pending (Perlu Approve)</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-black {{ ($status ?? 'all') === 'pending' ? 'bg-amber-600 text-white' : (($statusCounts['pending'] ?? 0) > 0 ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400') }}">
                    {{ $statusCounts['pending'] ?? 0 }}
                </span>
            </a>
        </div>

        <x-table-card 
            title="Manajemen Pengguna"
            :footer="$users->links()">
            
            <x-slot name="headerActions">
                <div class="flex flex-wrap items-center gap-3">
                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari nama, NPM, email..." 
                        route="users.index"
                        :params="[
                            'status' => $status ?? 'all',
                            'role' => $role ?? 'all',
                            'cohort_filter' => $cohortFilter ?? 'all',
                            'entry_year' => $entryYear ?? '',
                            'per_page' => $perPage
                        ]" />
                    
                    <div class="flex items-center gap-2">
                        <a href="{{ route('users.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-black text-[11px] text-slate-600 dark:text-slate-400 uppercase tracking-widest hover:bg-slate-100 dark:hover:bg-slate-800 transition-all shadow-sm" title="Export data pengguna sesuai filter aktif">
                            <svg class="w-4 h-4 mr-2 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Export
                        </a>
                        <button type="button" @click="openImportModal = true" class="inline-flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-black text-[11px] text-slate-600 dark:text-slate-400 uppercase tracking-widest hover:bg-slate-100 dark:hover:bg-slate-800 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import
                        </button>
                        <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-xl font-black text-[11px] uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-500/20">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Baru
                        </a>
                    </div>
                </div>
            </x-slot>

            <!-- Sub-Toolbar: Filter Peran, Tahun Angkatan, dan Paginasi -->
            <div class="px-5 py-3.5 bg-slate-50/70 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                <!-- Left: Role Filter Segment -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mr-1 hidden sm:inline">Peran:</span>
                    
                    {{-- Semua Peran --}}
                    <a href="{{ route('users.index', ['role' => 'all', 'status' => $status ?? 'all', 'entry_year' => $entryYear ?? '', 'search' => $search, 'per_page' => $perPage]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($role ?? 'all') === 'all' ? 'bg-orange-500 text-white border-orange-500 shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>Semua</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[11px] font-black {{ ($role ?? 'all') === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                            {{ $roleCounts['all'] ?? 0 }}
                        </span>
                    </a>

                    {{-- Mahasiswa --}}
                    <a href="{{ route('users.index', ['role' => 'mahasiswa', 'status' => $status ?? 'all', 'entry_year' => $entryYear ?? '', 'search' => $search, 'per_page' => $perPage]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($role ?? '') === 'mahasiswa' ? 'bg-indigo-600 text-white border-indigo-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-indigo-700 dark:text-indigo-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>Mahasiswa</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[11px] font-black {{ ($role ?? '') === 'mahasiswa' ? 'bg-white/20 text-white' : 'bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-200' }}">
                            {{ $roleCounts['mahasiswa'] ?? 0 }}
                        </span>
                    </a>

                    {{-- Dosen --}}
                    <a href="{{ route('users.index', ['role' => 'dosen', 'status' => $status ?? 'all', 'entry_year' => '', 'search' => $search, 'per_page' => $perPage]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($role ?? '') === 'dosen' ? 'bg-blue-600 text-white border-blue-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-blue-700 dark:text-blue-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>Dosen</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[11px] font-black {{ ($role ?? '') === 'dosen' ? 'bg-white/20 text-white' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-200' }}">
                            {{ $roleCounts['dosen'] ?? 0 }}
                        </span>
                    </a>

                    {{-- Kaprodi --}}
                    <a href="{{ route('users.index', ['role' => 'kaprodi', 'status' => $status ?? 'all', 'entry_year' => '', 'search' => $search, 'per_page' => $perPage]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($role ?? '') === 'kaprodi' ? 'bg-amber-600 text-white border-amber-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-amber-700 dark:text-amber-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>Kaprodi</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[11px] font-black {{ ($role ?? '') === 'kaprodi' ? 'bg-white/20 text-white' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200' }}">
                            {{ $roleCounts['kaprodi'] ?? 0 }}
                        </span>
                    </a>
                </div>

                <!-- Right: Dropdown Pilihan Spesifik Tahun Angkatan & Jumlah Per Halaman -->
                <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                    <form action="{{ route('users.index') }}" method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="role" value="{{ $role ?? 'all' }}">
                        <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
                        @if($search)
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif

                        @if(($role ?? 'all') === 'all' || ($role ?? '') === 'mahasiswa')
                            <!-- Specific Entry Year Dropdown -->
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
                        @endif

                        <!-- Per Page Dropdown -->
                        <select name="per_page" onchange="this.form.submit()"
                                class="pl-3 pr-8 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all shadow-2xs cursor-pointer"
                                title="Tampilkan jumlah baris per halaman">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 / hal</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 / hal</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 / hal</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 / hal</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Inline Bulk Action Banner (Appears when >= 1 user selected) -->
            <div x-show="selectedUserIds.length > 0" 
                 x-cloak
                 class="px-6 py-3 bg-orange-50 dark:bg-orange-950/40 border-b border-orange-200 dark:border-orange-800/60 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-lg bg-orange-600 text-white font-black text-xs" x-text="selectedUserIds.length + ' Pengguna Dipilih'"></span>
                    <span class="text-xs font-bold text-orange-950 dark:text-orange-200">Siap untuk aksi massal</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="selectedUserIds = []" 
                            class="px-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-2xs">
                        Batalkan Pilihan
                    </button>
                    <button type="button" 
                            @click="openBulkDeleteModal = true" 
                            class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black tracking-wide transition-all shadow-md shadow-rose-600/20 active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Hapus Massal Terpilih (<span x-text="selectedUserIds.length"></span>)</span>
                    </button>
                </div>
            </div>

            <!-- Table Skeleton Shimmer -->
            <div x-show="pageLoading" x-cloak>
                <x-skeleton type="table" :rows="6" />
            </div>

            @include('users.partials.mobile-cards')

            <table x-show="!pageLoading"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0"
                   x-transition:enter-end="opacity-100"
                   class="hidden sm:table w-full text-sm text-left">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-[11px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                        <th scope="col" class="py-4 px-4 text-center w-10">
                            <input type="checkbox" 
                                   @change="toggleSelectAll()" 
                                   :checked="isAllSelected()"
                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500 dark:bg-slate-800 cursor-pointer" 
                                   title="Pilih Semua di Halaman Ini">
                        </th>
                        <th scope="col" class="py-4 px-6">Pengguna</th>
                        <th scope="col" class="py-4 px-6 text-center">Peran</th>
                        <th scope="col" class="py-4 px-6 text-center">NPM / NIDN & Angkatan</th>
                        <th scope="col" class="py-4 px-6 text-center">Kontak & WhatsApp</th>
                        <th scope="col" class="py-4 px-6 text-center">Terakhir Login</th>
                        <th scope="col" class="py-4 px-6 text-center">Status</th>
                        <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($users as $user)
                        @php
                            $cleanPhone = null;
                            $waMessage = '';
                            if ($user->phone_number) {
                                $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone_number);
                                if (str_starts_with($cleanPhone, '0')) {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                                if ($user->role === 'mahasiswa') {
                                    $waMessage = "Halo {$user->name} ({$user->identifier}), saya Admin SIBIMA Program Studi. Terkait akun SIBIMA Anda:";
                                } elseif ($user->role === 'dosen') {
                                    $waMessage = "Halo Bapak/Ibu {$user->name}, saya Admin SIBIMA Program Studi. Terkait akun dan bimbingan SIBIMA:";
                                } else {
                                    $waMessage = "Halo {$user->name}, saya Admin SIBIMA Program Studi:";
                                }
                                if (!$user->is_active) {
                                    $waMessage .= " Akun SIBIMA Anda saat ini menunggu aktivasi/verifikasi data.";
                                }
                            }

                            // Thesis details for Mahasiswa
                            $thesisData = null;
                            if ($user->role === 'mahasiswa' && $user->thesis) {
                                $stageText = 'Pengajuan Judul';
                                $stageColor = 'slate';
                                if ($user->thesis->status === 'completed') {
                                    $stageText = 'Lulus / Selesai';
                                    $stageColor = 'emerald';
                                } elseif ($user->thesis->isAccSidangFinal()) {
                                    $stageText = 'Siap Sidang Skripsi (ACC Pembimbing)';
                                    $stageColor = 'indigo';
                                } elseif ($user->thesis->isAccUpFinal()) {
                                    $stageText = 'Selesai Seminar UP (ACC Pembimbing)';
                                    $stageColor = 'blue';
                                } elseif ($user->thesis->completed_mentoring_count > 0) {
                                    $stageText = "Bimbingan Reguler ({$user->thesis->completed_mentoring_count} Sesi Selesai)";
                                    $stageColor = 'amber';
                                }

                                $thesisData = [
                                    'id' => $user->thesis->id,
                                    'title' => $user->thesis->final_title ?: $user->thesis->title,
                                    'topic' => $user->thesis->topic ?: 'Belum diatur',
                                    'stage' => $stageText,
                                    'stage_color' => $stageColor,
                                    'pembimbing1' => $user->thesis->pembimbing1->name ?? 'Belum ditentukan',
                                    'pembimbing2' => $user->thesis->pembimbing2->name ?? 'Belum ditentukan',
                                    'url' => route('theses.show', $user->thesis->id),
                                    'is_old_cohort' => $user->entry_year ? ($user->entry_year <= $oldCohortThresholdYear) : false,
                                ];
                            }

                            // Dosen supervision details
                            $dosenData = null;
                            if ($user->role === 'dosen' || $user->role === 'kaprodi') {
                                $p1List = $user->thesesAsP1->map(fn($t) => [
                                    'student_name' => $t->student->name ?? '-',
                                    'student_npm' => $t->student->identifier ?? '-',
                                    'title' => $t->final_title ?: $t->title,
                                    'role' => 'Pembimbing 1',
                                ])->values()->toArray();

                                $p2List = $user->thesesAsP2->map(fn($t) => [
                                    'student_name' => $t->student->name ?? '-',
                                    'student_npm' => $t->student->identifier ?? '-',
                                    'title' => $t->final_title ?: $t->title,
                                    'role' => 'Pembimbing 2',
                                ])->values()->toArray();

                                $dosenData = [
                                    'research_interests' => $user->research_interests ?: 'Belum diatur',
                                    'max_quota' => $user->max_quota ?? 10,
                                    'p1_count' => count($p1List),
                                    'p2_count' => count($p2List),
                                    'total_active' => count($p1List) + count($p2List),
                                    'students' => array_merge($p1List, $p2List),
                                ];
                            }

                            $userData = [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'has_email_verified' => (bool)$user->email_verified_at,
                                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->translatedFormat('d F Y, H:i') : null,
                                'avatar_url' => $user->avatar_url,
                                'role' => $user->role,
                                'identifier' => $user->identifier,
                                'phone_number' => $user->phone_number,
                                'has_wa' => !empty($user->phone_number),
                                'clean_phone' => $cleanPhone,
                                'wa_message' => rawurlencode($waMessage),
                                'entry_year' => $user->entry_year,
                                'is_old_cohort' => $user->entry_year ? ($user->entry_year <= $oldCohortThresholdYear) : false,
                                'is_active' => (bool)$user->is_active,
                                'registered_at' => $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') : '-',
                                'last_login_at_human' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah login',
                                'last_login_at_full' => $user->last_login_at ? $user->last_login_at->translatedFormat('d F Y, H:i') : null,
                                'edit_url' => route('users.edit', $user->id),
                                'toggle_url' => route('users.toggle', $user->id),
                                'can_impersonate' => in_array($user->role, ['dosen', 'mahasiswa'], true) && $user->is_active && $user->id !== Auth::id() && !session()->has(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_ID),
                                'impersonate_url' => route('users.impersonate', $user->id),
                                'thesis' => $thesisData,
                                'dosen' => $dosenData,
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors group" :class="isSelected({{ $user->id }}) ? 'bg-orange-50/60 dark:bg-orange-950/30' : ''">
                            <td class="py-4 px-4 text-center w-10">
                                @if($user->role !== 'admin')
                                    <input type="checkbox" 
                                           value="{{ $user->id }}" 
                                           x-model.number="selectedUserIds"
                                           class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500 dark:bg-slate-800 cursor-pointer">
                                @else
                                    <span class="w-4 h-4 inline-block text-slate-300 dark:text-slate-600 text-xs" title="Akun Admin dilindungi">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center cursor-pointer group/user" 
                                     @click="selectedUser = {{ json_encode($userData) }}; openDetailModal = true"
                                     title="Klik untuk melihat profil detail cepat">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden mr-4 border border-slate-200 dark:border-slate-700 shadow-sm group-hover/user:scale-110 group-hover/user:border-orange-500 transition-all flex items-center justify-center bg-slate-50 dark:bg-slate-800 shrink-0">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight truncate group-hover/user:text-orange-600 dark:group-hover/user:text-orange-400 transition-colors flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            <svg class="w-3 h-3 text-slate-400 opacity-0 group-hover/user:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-tighter truncate">{{ $user->email }}</span>
                                            @if($user->email_verified_at)
                                                <span class="inline-flex items-center gap-0.5 text-[11px] font-black text-emerald-600 dark:text-emerald-400" title="Email terverifikasi">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <x-status-badge 
                                    :type="$user->role === 'dosen' ? 'blue' : ($user->role === 'kaprodi' ? 'orange' : 'slate')" 
                                    :label="strtoupper($user->role)" />
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="font-black text-[11px] text-slate-700 dark:text-slate-300 tracking-wider font-mono">{{ $user->identifier }}</span>
                                    @if($user->role === 'mahasiswa' && $user->entry_year)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            Angkatan {{ $user->entry_year }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @if($cleanPhone)
                                    <div class="flex flex-col items-center gap-1">
                                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode($waMessage) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 rounded-xl text-[11px] font-bold hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:shadow-sm transition-all"
                                           title="Kirim Pesan WhatsApp Langsung">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            <span>{{ $user->phone_number }}</span>
                                        </a>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-black text-emerald-700 dark:text-emerald-300">
                                            <span>● Terhubung WA</span>
                                        </span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700">
                                        Belum Ada WA
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @if($user->last_login_at)
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-100" title="{{ $user->last_login_at->translatedFormat('d F Y, H:i') }}">
                                            {{ $user->last_login_at->diffForHumans() }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                            {{ $user->last_login_at->translatedFormat('d M Y, H:i') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 italic bg-slate-100 dark:bg-slate-800/60 px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700">
                                        Belum Pernah
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <form action="{{ route('users.toggle', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" title="Klik untuk mengubah status aktif/pending">
                                        @if($user->is_active)
                                            <x-status-badge type="emerald" label="AKTIF" />
                                        @else
                                            <x-status-badge type="orange" label="PENDING" />
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex justify-end items-center gap-1">
                                    <!-- Quick Preview Button -->
                                    <button type="button" @click="selectedUser = {{ json_encode($userData) }}; openDetailModal = true" 
                                            class="p-2 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors cursor-pointer" 
                                            title="Lihat Detail & Profil Pengguna" aria-label="Lihat Detail & Profil Pengguna">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>

                                    @if($userData['can_impersonate'])
                                        <form action="{{ route('users.impersonate', $user->id) }}"
                                              method="POST"
                                              data-confirm="Anda akan masuk ke sistem sebagai {{ $user->name }} ({{ ucfirst($user->role) }}). Aktivitas Login As dicatat pada log audit sistem. Lanjutkan?"
                                              data-confirm-title="Konfirmasi Login As"
                                              data-confirm-type="warning"
                                              data-confirm-button="Ya, Masuk Sebagai Pengguna">
                                            @csrf
                                            <button type="submit" class="p-2 text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors cursor-pointer" title="Login As (Masuk sebagai {{ $user->name }})" aria-label="Masuk sebagai {{ $user->name }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('users.reset-password', $user->id) }}"
                                          method="POST"
                                          data-confirm="Apakah Anda yakin ingin me-reset password pengguna ini menjadi sama dengan NPM/NIDN-nya?"
                                          data-confirm-title="Reset Password Pengguna"
                                          data-confirm-type="warning"
                                          data-confirm-button="Ya, Reset Password">
                                        @csrf
                                        <button type="submit" class="p-2 text-slate-500 dark:text-slate-400 hover:text-amber-500 transition-colors" title="Reset Password ke NPM/NIDN" aria-label="Reset Password ke NPM/NIDN">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                        </button>
                                    </form>
                                    <a href="{{ route('users.edit', $user->id) }}" class="p-2 text-slate-500 dark:text-slate-400 hover:text-blue-600 transition-colors" title="Edit Pengguna">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}"
                                          method="POST"
                                          data-confirm="Apakah Anda yakin ingin menghapus pengguna ini?"
                                          data-confirm-title="Hapus Pengguna"
                                          data-confirm-type="danger"
                                          data-confirm-button="Ya, Hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 dark:text-slate-400 hover:text-red-600 transition-colors" title="Hapus Pengguna" aria-label="Hapus Pengguna">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="8" description="Tidak ada data pengguna yang sesuai dengan filter yang dipilih." icon="user">
                            <div class="mt-3">
                                <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800 text-xs font-bold hover:bg-orange-100 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <span>Reset Semua Filter</span>
                                </a>
                            </div>
                        </x-empty-state>
                    @endforelse
                </tbody>
            </table>
        </x-table-card>

        @include('users.partials.modals')
    </div>
</x-app-layout>

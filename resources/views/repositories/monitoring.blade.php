<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Katalog Pustaka', 'route' => route('repositories.index')],
                ['label' => 'Monitoring Pustaka Civitas', 'route' => null]
            ]" />
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="civitasMonitoringApp()">
        @include('repositories.partials.tabs')

        <!-- HERO & KPI SUMMARY STATS -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="space-y-2 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Hak Akses Khusus: Admin & Kaprodi</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 dark:text-white">
                        Monitoring Koleksi Pustaka Civitas
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Pantau daftar bacaan dan referensi jurnal ilmiah yang disimpan oleh mahasiswa dan dosen. Evaluasi kecukupan literatur acuan tugas akhir/skripsi dan topik penelitian civitas secara transparan.
                    </p>
                </div>

                <!-- Export / Quick summary pill -->
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ number_format($totalMahasiswaWithBookmarks + $totalDosenWithBookmarks) }} Civitas Aktif Mengoleksi</span>
                    </span>
                </div>
            </div>

            <!-- 4 KPI STATS CARDS -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4 pt-2">
                <!-- Card 1: Mahasiswa Aktif -->
                <div class="bg-gradient-to-br from-indigo-50 to-white dark:from-indigo-950/20 dark:to-slate-900 p-4 sm:p-5 rounded-2xl border border-indigo-100 dark:border-indigo-900/40 relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">Mahasiswa</span>
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($totalMahasiswaWithBookmarks) }}
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            Menyimpan referensi skripsi
                        </p>
                    </div>
                </div>

                <!-- Card 2: Dosen Aktif -->
                <div class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-950/20 dark:to-slate-900 p-4 sm:p-5 rounded-2xl border border-blue-100 dark:border-blue-900/40 relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Dosen</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($totalDosenWithBookmarks) }}
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            Mengoleksi bacaan riset
                        </p>
                    </div>
                </div>

                <!-- Card 3: Total Koleksi Pustaka -->
                <div class="bg-gradient-to-br from-orange-50 to-white dark:from-orange-950/20 dark:to-slate-900 p-4 sm:p-5 rounded-2xl border border-orange-100 dark:border-orange-900/40 relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-orange-700 dark:text-orange-400">Total Koleksi</span>
                        <div class="w-8 h-8 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($totalBookmarks) }}
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            Artikel & buku ditandai
                        </p>
                    </div>
                </div>

                <!-- Card 4: Total Folder / Topik -->
                <div class="bg-gradient-to-br from-purple-50 to-white dark:from-purple-950/20 dark:to-slate-900 p-4 sm:p-5 rounded-2xl border border-purple-100 dark:border-purple-900/40 relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-purple-700 dark:text-purple-400">Folder Topik</span>
                        <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($totalFolders) }}
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            Kategori bab & topik dibuat
                        </p>
                    </div>
                </div>
            </div>

            <!-- POPULAR SOURCES BREAKDOWN -->
            @if(!empty($topSources))
                <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mr-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.316.492-.63 1.1-.97 1.833C8.483 6.208 7.39 8.212 6.544 9.878A14.28 14.28 0 005.1 13.06a4.5 4.5 0 008.8 0 14.28 14.28 0 00-1.444-3.182c-.846-1.666-1.939-3.67-2.61-4.997-.339-.733-.653-1.341-.97-1.833a4.01 4.01 0 00-.481-.495zM10 16a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" clip-rule="evenodd"></path></svg>
                        Sumber Terpopuler:
                    </span>
                    @foreach($topSources as $src => $count)
                        @php
                            $label = match($src) {
                                'fasilkom' => 'Jurnal GLOBAL FASILKOM',
                                'garuda' => 'GARUDA (SINTA)',
                                'doaj' => 'DOAJ Open Access',
                                'crossref' => 'Crossref DOI',
                                'openalex' => 'Academic OpenAlex',
                                'google_books' => 'Google Books',
                                'openlibrary' => 'Open Library',
                                default => ucfirst($src)
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-600/60">
                            <span>{{ $label }}</span>
                            <span class="px-1.5 py-0.2 rounded-md bg-white dark:bg-slate-800 text-[10px] font-black text-slate-900 dark:text-white shadow-2xs">
                                {{ number_format($count) }}
                            </span>
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- FILTER & SEARCH TOOLBAR -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-4">
            <form action="{{ route('repositories.monitoring') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                
                <!-- Search Box -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" 
                           name="q" 
                           value="{{ $search }}" 
                           placeholder="Cari nama civitas, NIM/NIDN, email, atau judul skripsi..."
                           class="w-full pl-10 pr-10 py-2.5 rounded-xl border text-xs sm:text-sm bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    @if($search)
                        <a href="{{ route('repositories.monitoring', ['role' => $roleFilter, 'sort' => $sortBy]) }}" 
                           class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                           title="Hapus pencarian">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </div>

                <!-- Role Filter Segmented Buttons -->
                <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-700/80 shrink-0">
                    <a href="{{ route('repositories.monitoring', ['q' => $search, 'role' => 'all', 'sort' => $sortBy]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $roleFilter === 'all' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Semua
                    </a>
                    <a href="{{ route('repositories.monitoring', ['q' => $search, 'role' => 'mahasiswa', 'sort' => $sortBy]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $roleFilter === 'mahasiswa' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Mahasiswa ({{ $totalMahasiswaWithBookmarks }})
                    </a>
                    <a href="{{ route('repositories.monitoring', ['q' => $search, 'role' => 'dosen', 'sort' => $sortBy]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $roleFilter === 'dosen' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Dosen ({{ $totalDosenWithBookmarks }})
                    </a>
                </div>

                <!-- Sort Dropdown -->
                <div class="flex items-center gap-2 shrink-0">
                    <label for="sort-select" class="text-xs font-bold text-slate-500 dark:text-slate-400 hidden sm:inline">Urutkan:</label>
                    <select id="sort-select" 
                            name="sort" 
                            onchange="this.form.submit()"
                            class="py-2 pl-3 pr-8 rounded-xl border text-xs font-bold bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-orange-500">
                        <option value="most_bookmarks" {{ $sortBy === 'most_bookmarks' ? 'selected' : '' }}>Koleksi Terbanyak</option>
                        <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Terakhir Menyimpan</option>
                        <option value="most_folders" {{ $sortBy === 'most_folders' ? 'selected' : '' }}>Folder Terbanyak</option>
                        <option value="name_asc" {{ $sortBy === 'name_asc' ? 'selected' : '' }}>Nama Civitas (A-Z)</option>
                        <option value="name_desc" {{ $sortBy === 'name_desc' ? 'selected' : '' }}>Nama Civitas (Z-A)</option>
                    </select>
                </div>
                
                <input type="hidden" name="role" value="{{ $roleFilter }}">
            </form>
        </div>

        <!-- CIVITAS TABLE / LIST -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
            @if($users->isEmpty())
                <!-- Empty State -->
                <div class="p-12 text-center space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-3xl bg-orange-100 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">Tidak ada civitas yang ditemukan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                            @if($search || $roleFilter !== 'all')
                                Tidak ada data yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih. Coba sesuaikan kata kunci.
                            @else
                                Belum ada mahasiswa atau dosen yang menyimpan referensi katalog ke daftar bacaan mereka.
                            @endif
                        </p>
                    </div>
                    @if($search || $roleFilter !== 'all')
                        <a href="{{ route('repositories.monitoring') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Reset Semua Filter</span>
                        </a>
                    @endif
                </div>
            @else
                <!-- Desktop Table View -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-900/50 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <th class="py-4 px-5">Civitas Akademika</th>
                                <th class="py-4 px-5">Topik Skripsi / Fokus Riset</th>
                                <th class="py-4 px-4 text-center">Total Koleksi</th>
                                <th class="py-4 px-4 text-center">Folder Bab</th>
                                <th class="py-4 px-5">Aktivitas Terakhir</th>
                                <th class="py-4 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs text-slate-700 dark:text-slate-300">
                            @foreach($users as $user)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors">
                                    <!-- Civitas Profile Column -->
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-10 h-10 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 shrink-0 shadow-2xs">
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-slate-900 dark:text-white truncate max-w-[180px] sm:max-w-[220px]" title="{{ $user->name }}">
                                                        {{ $user->name }}
                                                    </span>
                                                    @if($user->role === 'mahasiswa')
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide bg-indigo-50 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60 shrink-0">
                                                            Mahasiswa
                                                        </span>
                                                    @elseif($user->role === 'dosen')
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 shrink-0">
                                                            Dosen
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 shrink-0">
                                                            {{ ucfirst($user->role) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                    <span class="font-mono">{{ $user->identifier ?: '-' }}</span>
                                                    <span>•</span>
                                                    <span class="truncate max-w-[150px]">{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Focus / Thesis Column -->
                                    <td class="py-4 px-5">
                                        @if($user->role === 'mahasiswa')
                                            @if($user->thesis)
                                                <div class="space-y-1 max-w-md">
                                                    <p class="font-semibold text-slate-800 dark:text-slate-200 line-clamp-2 leading-snug" title="{{ $user->thesis->judul }}">
                                                        {{ $user->thesis->judul }}
                                                    </p>
                                                    <div class="flex flex-wrap items-center gap-1.5 text-[10px] text-slate-500 dark:text-slate-400">
                                                        @if($user->thesis->status)
                                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 font-bold text-slate-700 dark:text-slate-300">
                                                                {{ ucfirst(str_replace('_', ' ', $user->thesis->status)) }}
                                                            </span>
                                                        @endif
                                                        @if($user->thesis->pembimbing1)
                                                            <span title="Pembimbing 1: {{ $user->thesis->pembimbing1->name }}">P1: {{ \Illuminate\Support\Str::words($user->thesis->pembimbing1->name, 2, '...') }}</span>
                                                        @endif
                                                        @if($user->thesis->pembimbing2)
                                                            <span>•</span>
                                                            <span title="Pembimbing 2: {{ $user->thesis->pembimbing2->name }}">P2: {{ \Illuminate\Support\Str::words($user->thesis->pembimbing2->name, 2, '...') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500 italic text-[11px]">Belum mengajukan skripsi</span>
                                            @endif
                                        @else
                                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                                                <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                                <span class="text-[11px] font-medium">Dosen Pembimbing & Riset Pustaka</span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Total Bookmarks Badge -->
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-orange-50 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg>
                                            <span>{{ number_format($user->bookmarks_count) }}</span>
                                        </span>
                                    </td>

                                    <!-- Total Folders Badge -->
                                    <td class="py-4 px-4 text-center">
                                        @if($user->folders_count > 0)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60">
                                                <svg class="w-3.5 h-3.5 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                                <span>{{ $user->folders_count }}</span>
                                            </span>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 text-[11px]">-</span>
                                        @endif
                                    </td>

                                    <!-- Last Activity Column -->
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        @if($user->last_bookmarked_at)
                                            <div class="text-[11px]">
                                                <span class="font-bold text-slate-800 dark:text-slate-200">
                                                    {{ \Carbon\Carbon::parse($user->last_bookmarked_at)->diffForHumans() }}
                                                </span>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                                    {{ \Carbon\Carbon::parse($user->last_bookmarked_at)->translatedFormat('d M Y, H:i') }}
                                                </p>
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
                                    </td>

                                    <!-- Actions Column -->
                                    <td class="py-4 px-5 text-right whitespace-nowrap">
                                        <button type="button" 
                                                @click="inspectUser({{ $user->id }})"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white shadow-xs shadow-orange-600/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            <span>Lihat Koleksi</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination footer -->
                @if($users->hasPages())
                    <div class="p-5 border-t border-slate-200/80 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/30">
                        {{ $users->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- ========================================== -->
        <!-- DETAIL INSPECTOR MODAL (Alpine.js Drawer)  -->
        <!-- ========================================== -->
        <div x-show="modalOpen" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true"
             x-cloak>
            
            <!-- Backdrop -->
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="closeModal()"
                 class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

            <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
                <div x-show="modalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all w-full max-w-4xl border border-slate-200 dark:border-slate-700 flex flex-col max-h-[90vh]">

                    <!-- MODAL HEADER -->
                    <div class="px-6 py-5 bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-4 shrink-0">
                        <!-- Left: Avatar + Details -->
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-12 h-12 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-2xs bg-white dark:bg-slate-700 shrink-0 flex items-center justify-center">
                                <template x-if="selectedUser && selectedUser.avatar_url">
                                    <img :src="selectedUser.avatar_url" :alt="selectedUser.name" class="w-full h-full object-cover">
                                </template>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base font-black text-slate-900 dark:text-white truncate" x-text="selectedUser ? selectedUser.name : 'Memuat data...'"></h3>
                                    <template x-if="selectedUser">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider shrink-0"
                                              :class="{
                                                'bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700': selectedUser.role === 'mahasiswa',
                                                'bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700': selectedUser.role === 'dosen'
                                              }"
                                              x-text="selectedUser.role"></span>
                                    </template>
                                </div>
                                <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    <span class="font-mono" x-text="selectedUser ? (selectedUser.identifier || '-') : ''"></span>
                                    <template x-if="selectedUser && selectedUser.thesis">
                                        <span class="truncate max-w-[280px] sm:max-w-md text-slate-600 dark:text-slate-300 italic" :title="selectedUser.thesis.judul">
                                            • Skripsi: <span x-text="selectedUser.thesis.judul"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Total Pill & Close button -->
                        <div class="flex items-center gap-2 shrink-0">
                            <template x-if="selectedUser">
                                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 text-xs font-black border border-orange-200 dark:border-orange-800/60">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg>
                                    <span x-text="userBookmarks.length + ' Pustaka Disimpan'"></span>
                                </span>
                            </template>
                            <button type="button" 
                                    @click="closeModal()" 
                                    class="w-9 h-9 rounded-xl bg-slate-200/70 dark:bg-slate-700/70 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-colors cursor-pointer" 
                                    title="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- MODAL BODY -->
                    <div class="p-6 space-y-5 overflow-y-auto flex-1">
                        <!-- Loading State -->
                        <div x-show="loading" class="py-16 text-center space-y-3">
                            <div class="inline-block w-8 h-8 border-3 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Mengambil daftar koleksi pustaka civitas...</p>
                        </div>

                        <!-- Content when Loaded -->
                        <div x-show="!loading" class="space-y-4">
                            
                            <!-- Search & Folder Filter Bar inside modal -->
                            <div class="space-y-3">
                                <!-- Inner Search Input -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" 
                                           x-model="innerSearch" 
                                           placeholder="Cari dalam koleksi civitas ini (judul artikel, penulis, atau catatan)..."
                                           class="w-full pl-9 pr-9 py-2 rounded-xl border text-xs bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-orange-500 transition-all">
                                    <button type="button" 
                                            x-show="innerSearch" 
                                            @click="innerSearch = ''" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <!-- Folder Chips -->
                                <div class="flex items-center gap-1.5 flex-wrap pt-1">
                                    <button type="button" 
                                            @click="activeFolderId = 'all'"
                                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                            :class="activeFolderId === 'all' ? 'bg-orange-500 text-white shadow-2xs' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'">
                                        Semua Koleksi (<span x-text="userBookmarks.length"></span>)
                                    </button>

                                    <!-- Unassigned Folder -->
                                    <template x-if="unassignedCount > 0">
                                        <button type="button" 
                                                @click="activeFolderId = 'unassigned'"
                                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                                :class="activeFolderId === 'unassigned' ? 'bg-orange-500 text-white shadow-2xs' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'">
                                            Tanpa Folder (<span x-text="unassignedCount"></span>)
                                        </button>
                                    </template>

                                    <!-- User Custom Folders -->
                                    <template x-for="folder in userFolders" :key="folder.id">
                                        <button type="button" 
                                                @click="activeFolderId = folder.id"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                                :class="activeFolderId === folder.id ? 'bg-orange-500 text-white shadow-2xs' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'">
                                            <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: folder.color || '#6366f1' }"></span>
                                            <span x-text="folder.name"></span>
                                            <span class="text-[10px] opacity-75" x-text="'(' + folder.bookmarks_count + ')'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Bookmark Items List -->
                            <div class="space-y-3 pt-2">
                                <template x-if="filteredBookmarks.length === 0">
                                    <div class="py-12 text-center space-y-2">
                                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Tidak ada koleksi pustaka yang cocok</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Coba ubah kata kunci atau pilih tab folder lainnya.</p>
                                    </div>
                                </template>

                                <template x-for="item in filteredBookmarks" :key="item.id">
                                    <div class="p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-900/40 hover:border-orange-300 dark:hover:border-orange-500/50 transition-all space-y-3 shadow-2xs">
                                        
                                        <!-- Top Row: Source badge, folder badge, date -->
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <!-- Source badge -->
                                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700"
                                                      x-text="item.source_label"></span>
                                                
                                                <!-- Folder badge if exists -->
                                                <template x-if="item.folder">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                                        <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: item.folder.color }"></span>
                                                        <span x-text="item.folder.name"></span>
                                                    </span>
                                                </template>
                                            </div>

                                            <span class="text-[11px] text-slate-400 dark:text-slate-500" :title="item.date_formatted" x-text="'Disimpan ' + item.time_ago"></span>
                                        </div>

                                        <!-- Title -->
                                        <div>
                                            <h4 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-snug">
                                                <a :href="item.url || item.doi || '#'" target="_blank" class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors" x-text="item.title"></a>
                                            </h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                <span class="font-medium text-slate-700 dark:text-slate-300" x-text="item.authors"></span>
                                                <template x-if="item.year">
                                                    <span x-text="' (' + item.year + ')'"></span>
                                                </template>
                                                <template x-if="item.venue">
                                                    <span> • <span class="italic" x-text="item.venue"></span></span>
                                                </template>
                                            </p>
                                        </div>

                                        <!-- Notes Box (Catatan Keterkaitan Teori) -->
                                        <template x-if="item.notes">
                                            <div class="p-3 rounded-xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/40 text-xs text-amber-900 dark:text-amber-200 space-y-1">
                                                <div class="flex items-center gap-1.5 font-bold text-[11px] text-amber-800 dark:text-amber-300">
                                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    <span>Catatan / Keterkaitan Teori:</span>
                                                </div>
                                                <p class="whitespace-pre-line text-xs leading-relaxed" x-text="item.notes"></p>
                                            </div>
                                        </template>

                                        <!-- Action Buttons Row -->
                                        <div class="flex items-center gap-2 pt-1 flex-wrap">
                                            <!-- Open URL / DOI -->
                                            <template x-if="item.url || item.doi">
                                                <a :href="item.url || ('https://doi.org/' + item.doi)" target="_blank"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-all">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    <span>Buka Sumber</span>
                                                </a>
                                            </template>

                                            <!-- PDF Download if OA -->
                                            <template x-if="item.pdf_url">
                                                <a :href="item.pdf_url" target="_blank"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 transition-all">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    <span>Unduh PDF</span>
                                                </a>
                                            </template>

                                            <!-- Copy Citation -->
                                            <button type="button" 
                                                    @click="copyCitation(item)"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/80 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-all cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                <span x-text="copiedId === item.id ? 'Tersalin!' : 'Salin Sitasi APA'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </div>

                    <!-- MODAL FOOTER -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs text-slate-500 shrink-0">
                        <span x-show="selectedUser">
                            Menampilkan <strong class="text-slate-800 dark:text-white" x-text="filteredBookmarks.length"></strong> dari <strong class="text-slate-800 dark:text-white" x-text="userBookmarks.length"></strong> total pustaka
                        </span>
                        <button type="button" 
                                @click="closeModal()" 
                                class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition-all cursor-pointer">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function civitasMonitoringApp() {
            return {
                modalOpen: false,
                loading: false,
                selectedUser: null,
                userFolders: [],
                unassignedCount: 0,
                userBookmarks: [],
                activeFolderId: 'all',
                innerSearch: '',
                copiedId: null,

                async inspectUser(userId) {
                    this.modalOpen = true;
                    this.loading = true;
                    this.selectedUser = null;
                    this.userFolders = [];
                    this.unassignedCount = 0;
                    this.userBookmarks = [];
                    this.activeFolderId = 'all';
                    this.innerSearch = '';

                    try {
                        const response = await fetch(`/repositories/monitoring/${userId}/collection`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Gagal mengambil data koleksi civitas.');
                        }

                        const data = await response.json();
                        if (data.success) {
                            this.selectedUser = data.user;
                            this.userFolders = data.folders || [];
                            this.unassignedCount = data.unassigned_count || 0;
                            this.userBookmarks = data.bookmarks || [];
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat memuat data.');
                            this.closeModal();
                        }
                    } catch (err) {
                        console.error('Error fetching user collection:', err);
                        alert('Terjadi kesalahan koneksi saat memuat data koleksi.');
                        this.closeModal();
                    } finally {
                        this.loading = false;
                    }
                },

                closeModal() {
                    this.modalOpen = false;
                    this.selectedUser = null;
                },

                get filteredBookmarks() {
                    let list = this.userBookmarks;

                    // Filter by folder
                    if (this.activeFolderId !== 'all') {
                        if (this.activeFolderId === 'unassigned') {
                            list = list.filter(item => !item.folder);
                        } else {
                            list = list.filter(item => item.folder && item.folder.id === this.activeFolderId);
                        }
                    }

                    // Filter by inner search
                    if (this.innerSearch && this.innerSearch.trim() !== '') {
                        const q = this.innerSearch.toLowerCase().trim();
                        list = list.filter(item => {
                            const title = (item.title || '').toLowerCase();
                            const authors = (item.authors || '').toLowerCase();
                            const notes = (item.notes || '').toLowerCase();
                            const venue = (item.venue || '').toLowerCase();
                            return title.includes(q) || authors.includes(q) || notes.includes(q) || venue.includes(q);
                        });
                    }

                    return list;
                },

                copyCitation(item) {
                    const author = item.authors || 'Anonim';
                    const year = item.year ? `(${item.year})` : '(n.d.)';
                    const title = item.title;
                    const venue = item.venue ? ` ${item.venue}.` : '';
                    const doi = item.doi ? ` https://doi.org/${item.doi}` : (item.url ? ` ${item.url}` : '');
                    
                    const apa = `${author} ${year}. ${title}.${venue}${doi}`;

                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(apa);
                    } else {
                        const textArea = document.createElement("textarea");
                        textArea.value = apa;
                        document.body.appendChild(textArea);
                        textArea.focus();
                        textArea.select();
                        try {
                            document.execCommand('copy');
                        } catch (err) {
                            console.error('Copy fallback failed', err);
                        }
                        document.body.removeChild(textArea);
                    }

                    this.copiedId = item.id;
                    setTimeout(() => {
                        if (this.copiedId === item.id) {
                            this.copiedId = null;
                        }
                    }, 2000);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

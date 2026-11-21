<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Katalog Pustaka', 'route' => route('repositories.index')],
                ['label' => 'Jurnal Ilmiah', 'route' => route('repositories.journals')],
                ['label' => 'Daftar Bacaan Saya', 'route' => null]
            ]" />
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="bookmarksApp(@js($folders), '{{ $folderFilter }}')">
        @include('repositories.partials.tabs')

        <!-- HERO BANNER -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="space-y-2 max-w-2xl">
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 dark:text-white">
                        Daftar Bacaan Saya <span class="text-orange-600 dark:text-orange-400 font-black">({{ number_format($totalBookmarksCount) }})</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Koleksi artikel jurnal ilmiah yang Anda tandai untuk acuan skripsi. Kelola per Bab Skripsi, catat keterkaitan teori, unduh PDF, dan salin daftar pustaka instan.
                    </p>
                </div>

                <!-- Top Quick Actions -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <button type="button" 
                            @click="openBatchCitationModal()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-orange-400 dark:hover:border-orange-500 shadow-xs transition-all hover:scale-[1.01] active:scale-95 cursor-pointer"
                            title="Salin seluruh sitasi artikel terkompilasi dalam format APA 7th / IEEE / BibTeX">
                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        <span>Salin Semua Sitasi</span>
                    </button>

                    <button type="button" 
                            @click="openCreateFolderModal()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white shadow-sm shadow-orange-500/25 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                        <span>+ Buat Folder</span>
                    </button>
                </div>
            </div>

            <!-- SEARCH & FILTER BAR -->
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 space-y-4">
                <form action="{{ route('repositories.bookmarks') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <input type="hidden" name="source" value="{{ $sourceFilter }}">
                    <input type="hidden" name="folder" value="{{ $folderFilter }}">
                    
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 dark:text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" 
                               name="q" 
                               value="{{ $query }}" 
                               placeholder="Cari judul artikel, nama penulis, nama jurnal, atau catatan Anda..."
                               class="w-full pl-10 pr-10 py-2.5 rounded-xl border text-xs bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-orange-500 dark:focus:ring-orange-500 focus:border-transparent transition-all">
                        @if(!empty($query))
                            <a href="{{ route('repositories.bookmarks', ['source' => $sourceFilter, 'folder' => $folderFilter]) }}" 
                               class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                               title="Bersihkan pencarian">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>

                    <button type="submit" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white shadow-sm shadow-orange-500/25 transition-all hover:scale-[1.01] active:scale-95 cursor-pointer whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Cari di Bacaan</span>
                    </button>
                </form>

                <!-- SOURCE FILTER PILLS -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 pt-1">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 shrink-0">Filter Sumber:</span>
                    <div class="inline-flex flex-wrap items-center gap-2 p-1.5 rounded-2xl bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                        @php
                            $sourcesList = [
                                'all' => ['label' => 'Semua Sumber', 'count' => $sourceCounts['all'] ?? 0],
                                'books' => ['label' => 'Buku & E-Book', 'count' => $sourceCounts['books'] ?? 0],
                                'fasilkom' => ['label' => 'GLOBAL FASILKOM', 'count' => $sourceCounts['fasilkom'] ?? 0],
                                'garuda' => ['label' => 'GARUDA (SINTA)', 'count' => $sourceCounts['garuda'] ?? 0],
                                'doaj' => ['label' => 'DOAJ', 'count' => $sourceCounts['doaj'] ?? 0],
                                'crossref' => ['label' => 'Crossref', 'count' => $sourceCounts['crossref'] ?? 0],
                                'openalex' => ['label' => 'OpenAlex', 'count' => $sourceCounts['openalex'] ?? 0],
                            ];
                        @endphp

                        @foreach($sourcesList as $sKey => $sData)
                            @if($sKey === 'all' || $sData['count'] > 0)
                                <a href="{{ route('repositories.bookmarks', array_merge(request()->query(), ['source' => $sKey, 'page' => 1])) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap
                                          {{ $sourceFilter === $sKey 
                                             ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/25' 
                                             : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-800' }}">
                                    <span>{{ $sData['label'] }}</span>
                                    <span class="px-2 py-0.5 rounded-lg text-[11px] font-black {{ $sourceFilter === $sKey ? 'bg-white/25 text-white' : 'bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60' }}">
                                        {{ $sData['count'] }}
                                    </span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- FOLDER / BAB SKRIPSI NAVIGATION BAR -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-800 dark:text-slate-100">Koleksi Bab Skripsi</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">({{ $folders->count() }} Folder)</span>
                </div>

                <!-- Template Chapters Generator Button -->
                <button type="button" 
                        @click="applyThesisTemplate()"
                        :disabled="templateLoading"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 bg-slate-100 dark:bg-slate-700/60 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200/80 dark:border-slate-700 transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-orange-500" :class="{ 'animate-spin': templateLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span x-text="templateLoading ? 'Menyiapkan...' : 'Gunakan Template 5 BAB Skripsi'"></span>
                </button>
            </div>

            <!-- Folder Tabs List -->
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <!-- Tab: Semua Bacaan -->
                <a href="{{ route('repositories.bookmarks', array_merge(request()->query(), ['folder' => 'all', 'page' => 1])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap
                          {{ $folderFilter === 'all' 
                             ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-xs' 
                             : 'bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span>Semua Bacaan</span>
                    <span class="px-2 py-0.5 rounded-lg text-[11px] font-black {{ $folderFilter === 'all' ? 'bg-white/20 text-white dark:bg-slate-900/20 dark:text-slate-900' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $totalBookmarksCount }}
                    </span>
                </a>

                <!-- Custom User Folders -->
                @foreach($folders as $folder)
                    <div class="inline-flex items-center rounded-xl transition-all border
                                {{ (string)$folderFilter === (string)$folder->id 
                                   ? 'bg-orange-500 text-white border-orange-500 shadow-sm shadow-orange-500/25' 
                                   : 'bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 border-slate-200/80 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <a href="{{ route('repositories.bookmarks', array_merge(request()->query(), ['folder' => $folder->id, 'page' => 1])) }}"
                           class="inline-flex items-center gap-2 pl-3.5 pr-2 py-2 text-xs font-bold whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ (string)$folderFilter === (string)$folder->id ? 'text-white' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                            <span>{{ $folder->name }}</span>
                            <span class="px-2 py-0.5 rounded-lg text-[11px] font-black {{ (string)$folderFilter === (string)$folder->id ? 'bg-white/25 text-white' : 'bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300' }}">
                                {{ $folder->bookmarks_count }}
                            </span>
                        </a>

                        <!-- Manage Folder Button (Edit / Delete) -->
                        <div class="pr-2 pl-0.5 flex items-center">
                            <button type="button" 
                                    @click="openEditFolderModal(@js($folder))" 
                                    class="p-1 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 transition-colors"
                                    title="Ubah Nama Folder" aria-label="Ubah Nama Folder">
                                <svg class="w-3 h-3 {{ (string)$folderFilter === (string)$folder->id ? 'text-white/80 hover:text-white' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button type="button" 
                                    @click="deleteFolder(@js($folder))" 
                                    class="p-1 rounded-lg hover:bg-rose-500/20 text-rose-400 hover:text-rose-600 transition-colors cursor-pointer"
                                    title="Hapus Folder (Artikel tidak akan terhapus)"
                                    aria-label="Hapus folder {{ $folder->name }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                @endforeach

                <!-- Tab: Tanpa Folder -->
                @if($uncategorizedCount > 0 || $folders->isNotEmpty())
                    <a href="{{ route('repositories.bookmarks', array_merge(request()->query(), ['folder' => 'uncategorized', 'page' => 1])) }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap border
                              {{ $folderFilter === 'uncategorized' 
                                 ? 'bg-slate-800 text-white border-slate-800 shadow-xs' 
                                 : 'bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 border-dashed border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                       title="Artikel yang belum dimasukkan ke dalam folder manapun">
                        <span>Tanpa Folder</span>
                        <span class="px-2 py-0.5 rounded-lg text-[11px] font-black {{ $folderFilter === 'uncategorized' ? 'bg-white/20 text-white' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                            {{ $uncategorizedCount }}
                        </span>
                    </a>
                @endif
            </div>
        </div>

        @include('repositories.partials.bookmark-list')

        @include('repositories.partials.bookmark-modals')
    </div>

    @include('repositories.partials.bookmark-scripts')
</x-app-layout>

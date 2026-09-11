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
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" 
                               name="q" 
                               value="{{ $query }}" 
                               placeholder="Cari judul artikel, nama penulis, nama jurnal, atau catatan Anda..."
                               class="w-full pl-10 pr-10 py-2.5 rounded-xl border text-xs bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-orange-500 dark:focus:ring-orange-500 focus:border-transparent transition-all">
                        @if(!empty($query))
                            <a href="{{ route('repositories.bookmarks', ['source' => $sourceFilter, 'folder' => $folderFilter]) }}" 
                               class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
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
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-black {{ $sourceFilter === $sKey ? 'bg-white/25 text-white' : 'bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60' }}">
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
                    <span class="text-xs text-slate-400">({{ $folders->count() }} Folder)</span>
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
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-black {{ $folderFilter === 'all' ? 'bg-white/20 text-white dark:bg-slate-900/20 dark:text-slate-900' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
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
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black {{ (string)$folderFilter === (string)$folder->id ? 'bg-white/25 text-white' : 'bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300' }}">
                                {{ $folder->bookmarks_count }}
                            </span>
                        </a>

                        <!-- Manage Folder Button (Edit / Delete) -->
                        <div class="pr-2 pl-0.5 flex items-center">
                            <button type="button" 
                                    @click="openEditFolderModal(@js($folder))" 
                                    class="p-1 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 transition-colors"
                                    title="Ubah Nama Folder">
                                <svg class="w-3 h-3 {{ (string)$folderFilter === (string)$folder->id ? 'text-white/80 hover:text-white' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button type="button" 
                                    @click="deleteFolder(@js($folder))" 
                                    class="p-1 rounded-lg hover:bg-rose-500/20 text-rose-400 hover:text-rose-600 transition-colors"
                                    title="Hapus Folder (Artikel tidak akan terhapus)">
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
                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-black {{ $folderFilter === 'uncategorized' ? 'bg-white/20 text-white' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                            {{ $uncategorizedCount }}
                        </span>
                    </a>
                @endif
            </div>
        </div>

        <!-- CONTENT AREA / LIST OF BOOKMARKS -->
        @if($bookmarks->isEmpty())
            <!-- Empty State -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-10 sm:p-14 border border-slate-200/80 dark:border-slate-700/80 text-center space-y-4 shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-orange-50 dark:bg-orange-950/60 text-orange-500 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                </div>
                <div class="max-w-md mx-auto space-y-2">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">
                        @if(!empty($query))
                            Tidak Ditemukan Artikel yang Cocok
                        @elseif($folderFilter !== 'all')
                            Folder Ini Masih Kosong
                        @else
                            Belum Ada Artikel di Daftar Bacaan
                        @endif
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        @if(!empty($query))
                            Tidak ada artikel tersimpan yang sesuai dengan kata kunci "{{ $query }}".
                        @elseif($folderFilter !== 'all')
                            Belum ada artikel jurnal yang dimasukkan ke dalam folder ini. Anda dapat memindahkan artikel dari folder lain atau dari daftar bacaan utama.
                        @else
                            Saat Anda mencari artikel di katalog jurnal ilmiah, klik tombol <strong>"Simpan Bacaan"</strong> pada artikel yang relevan agar tersimpan di sini.
                        @endif
                    </p>
                </div>
                <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                    @if(!empty($query) || $sourceFilter !== 'all' || $folderFilter !== 'all')
                        <a href="{{ route('repositories.bookmarks') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 transition-colors">
                            <span>Reset Semua Filter</span>
                        </a>
                    @endif
                    <a href="{{ route('repositories.journals') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white shadow-sm shadow-orange-500/25 transition-all hover:scale-[1.02] active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Eksplorasi Jurnal Ilmiah Sekarang</span>
                    </a>
                </div>
            </div>
        @else
            <!-- Bookmarked Articles List -->
            <div class="space-y-4">
                @foreach($bookmarks as $bookmark)
                    @php
                        $itemData = $bookmark->toJournalItem();
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:border-orange-300 dark:hover:border-orange-500/50 hover:shadow-lg transition-all space-y-4"
                         x-data="{ 
                            showAbstract: false, 
                            editingNotes: false, 
                            notesText: '{{ addslashes($bookmark->notes ?? '') }}',
                            savingNotes: false,
                            saveNotes() {
                                this.savingNotes = true;
                                fetch('{{ route('repositories.bookmarks.notes', $bookmark) }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ notes: this.notesText })
                                })
                                .then(r => r.json())
                                .then(d => {
                                    this.savingNotes = false;
                                    this.editingNotes = false;
                                    window.dispatchEvent(new CustomEvent('notify', {
                                        detail: { title: 'Catatan Diperbarui', message: d.message, type: 'success' }
                                    }));
                                })
                                .catch(() => {
                                    this.savingNotes = false;
                                    window.dispatchEvent(new CustomEvent('notify', {
                                        detail: { title: 'Kesalahan', message: 'Gagal memperbarui catatan.', type: 'error' }
                                    }));
                                });
                            }
                         }"
                         id="bookmark-item-{{ $bookmark->id }}">
                        
                        <!-- Top Metadata Badges -->
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Source Badge -->
                                @if($bookmark->source === 'fasilkom')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-800/70 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <span>Jurnal GLOBAL FASILKOM UNSUB</span>
                                    </span>
                                @elseif($bookmark->source === 'garuda')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200 border border-rose-200 dark:border-rose-800/70 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        <span>Jurnal Nasional GARUDA (SINTA)</span>
                                    </span>
                                @elseif($bookmark->source === 'doaj')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800/70 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        <span>DOAJ Open Access</span>
                                    </span>
                                @elseif($bookmark->source === 'crossref')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-800/70 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        <span>Crossref DOI</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                        <span>{{ $bookmark->clean_source_label }}</span>
                                    </span>
                                @endif

                                @if($bookmark->year)
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600">
                                        {{ $bookmark->year }}
                                    </span>
                                @endif

                                @if(!empty($bookmark->pdf_url))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                        <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span>Ada PDF</span>
                                    </span>
                                @endif

                                <!-- Folder Badge -->
                                @if($bookmark->folder)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-orange-50 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60 shadow-2xs"
                                          id="card-folder-badge-{{ $bookmark->id }}">
                                        <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                        <span>{{ $bookmark->folder->name }}</span>
                                    </span>
                                @else
                                    <span class="hidden inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-orange-50 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60 shadow-2xs"
                                          id="card-folder-badge-{{ $bookmark->id }}">
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 text-[11px] text-slate-400 dark:text-slate-500">
                                <span>Disimpan: {{ $bookmark->created_at->diffForHumans() }}</span>
                                
                                @if($bookmark->doi)
                                    <span>•</span>
                                    <a href="{{ $bookmark->doi }}" target="_blank" rel="noopener noreferrer" 
                                       class="font-mono hover:text-orange-500 dark:hover:text-orange-400 flex items-center gap-1 transition-colors">
                                        <span>DOI: {{ str_replace('https://doi.org/', '', $bookmark->doi) }}</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Paper Title & Authors/Venue with Proper Breathing Room -->
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 leading-snug">
                                <a href="{{ $bookmark->url ?: ($bookmark->pdf_url ?: '#') }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                    {{ $bookmark->title }}
                                </a>
                            </h3>
                            <div class="flex flex-wrap items-center gap-x-1.5 gap-y-1 text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                                <span class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ $bookmark->authors_string }}
                                </span>
                                @if($bookmark->venue)
                                    <span class="text-slate-300 dark:text-slate-600 font-bold px-1 select-none">•</span>
                                    <span class="italic text-orange-600 dark:text-orange-400 font-medium">
                                        {{ $bookmark->venue }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Abstract Section (Expandable) -->
                        @if(!empty($bookmark->abstract))
                            <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-900/60 p-3.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                <div :class="showAbstract ? '' : 'line-clamp-2'">
                                    <span class="font-bold text-slate-700 dark:text-slate-200">Abstrak:</span>
                                    {{ $bookmark->abstract }}
                                </div>
                                <button type="button" 
                                        @click="showAbstract = !showAbstract" 
                                        class="mt-1.5 text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-bold inline-flex items-center gap-1 cursor-pointer">
                                    <span x-text="showAbstract ? 'Sembunyikan Abstrak' : 'Baca Abstrak Lengkap...'"></span>
                                    <svg class="w-3 h-3 transition-transform" :class="showAbstract ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        <!-- PERSONAL STUDENT NOTES BOX WITH CHAPTER SHORTCUTS -->
                        <div class="bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-800/50 rounded-xl p-3.5 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-amber-800 dark:text-amber-200">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Catatan Referensi Skripsi Anda:</span>
                                </div>
                                <button type="button" 
                                        @click="editingNotes = !editingNotes" 
                                        class="text-[11px] font-bold text-amber-700 dark:text-amber-300 hover:text-amber-900 dark:hover:text-white underline cursor-pointer">
                                    <span x-text="editingNotes ? 'Batal' : (notesText ? 'Edit Catatan' : '+ Tambah Catatan')"></span>
                                </button>
                            </div>

                            <!-- Notes View Mode -->
                            <div x-show="!editingNotes" class="text-xs text-slate-700 dark:text-slate-300">
                                <template x-if="notesText">
                                    <p class="whitespace-pre-line leading-relaxed italic" x-text="notesText"></p>
                                </template>
                                <template x-if="!notesText">
                                    <p class="text-slate-400 dark:text-slate-500 italic cursor-pointer" @click="editingNotes = true">
                                        Belum ada catatan. Klik di sini untuk mencatat keterkaitan artikel ini dengan skripsi Anda (misal: "BAB II - Landasan Teori Algoritma").
                                    </p>
                                </template>
                            </div>

                            <!-- Notes Edit Mode -->
                            <div x-show="editingNotes" x-cloak class="space-y-2 pt-1">
                                <textarea x-model="notesText" 
                                          rows="3" 
                                          placeholder="Tuliskan catatan referensi skripsi Anda di sini..."
                                          class="w-full text-xs p-3 rounded-xl bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"></textarea>
                                
                                <!-- Quick Chapter Insert Chips -->
                                <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="text-slate-500 dark:text-slate-400 text-[10px] font-semibold mr-1">Sisipkan Label:</span>
                                    <button type="button" @click="notesText = (notesText ? notesText + '\n' : '') + '[BAB I PENDAHULUAN] '" class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 font-bold hover:bg-amber-200 cursor-pointer">+ BAB I</button>
                                    <button type="button" @click="notesText = (notesText ? notesText + '\n' : '') + '[BAB II LANDASAN TEORI] '" class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 font-bold hover:bg-amber-200 cursor-pointer">+ BAB II</button>
                                    <button type="button" @click="notesText = (notesText ? notesText + '\n' : '') + '[BAB III OBJEK DAN METODOLOGI PENELITIAN] '" class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 font-bold hover:bg-amber-200 cursor-pointer">+ BAB III</button>
                                    <button type="button" @click="notesText = (notesText ? notesText + '\n' : '') + '[BAB IV HASIL DAN PEMBAHASAN] '" class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 font-bold hover:bg-amber-200 cursor-pointer">+ BAB IV</button>
                                    <button type="button" @click="notesText = (notesText ? notesText + '\n' : '') + '[BAB V PENUTUP] '" class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 font-bold hover:bg-amber-200 cursor-pointer">+ BAB V</button>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-1">
                                    <button type="button" 
                                            @click="editingNotes = false" 
                                            class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-400 hover:bg-amber-100/60 dark:hover:bg-slate-800 transition-colors">
                                        Batal
                                    </button>
                                    <button type="button" 
                                            @click="saveNotes()" 
                                            :disabled="savingNotes"
                                            class="px-4 py-1.5 rounded-lg text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white flex items-center gap-1.5 transition-all shadow-xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': savingNotes }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span x-text="savingNotes ? 'Menyimpan...' : 'Simpan Catatan'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Bar -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                                <!-- Citation Trigger -->
                                <button type="button"
                                        @click="openCitationModal(@js($itemData))"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-orange-400 dark:hover:border-orange-500 transition-all shadow-xs cursor-pointer">
                                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                    </svg>
                                    <span>Salin Sitasi</span>
                                </button>

                                <!-- Move Folder Action Button -->
                                <button type="button"
                                        @click="openMoveModal(@js($itemData))"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-orange-400 dark:hover:border-orange-500 transition-all shadow-xs cursor-pointer"
                                        title="Atur / Pindahkan artikel ini ke folder Bab Skripsi">
                                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                    <span>Pindah Folder</span>
                                </button>

                                <!-- Landing page / publisher link -->
                                @if($bookmark->url)
                                    <a href="{{ $bookmark->url }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white transition-all shadow-2xs">
                                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span>Buka Halaman Jurnal</span>
                                    </a>
                                @endif

                                <!-- Remove Bookmark Button (Generously Padded) -->
                                <button type="button" 
                                        @click="deleteBookmark({{ $bookmark->id }})"
                                        class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/80 hover:border-rose-300 dark:hover:border-rose-700 transition-all shadow-2xs hover:scale-[1.02] active:scale-95 cursor-pointer whitespace-nowrap"
                                        title="Hapus artikel dari Daftar Bacaan">
                                    <svg class="w-4 h-4 shrink-0 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Hapus</span>
                                </button>
                            </div>

                            <!-- PDF CTA -->
                            @if($bookmark->pdf_url)
                                <a href="{{ $bookmark->pdf_url }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white shadow-sm shadow-orange-500/25 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>Buka PDF Full-Text</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- PAGINATION -->
            <div class="pt-4">
                {{ $bookmarks->links() }}
            </div>
        @endif

        <!-- ================= MODAL 1: SINGLE CITATION MODAL ================= -->
        <template x-teleport="body">
            <div x-show="citationModalOpen" 
                 x-cloak 
                 class="fixed inset-0 overflow-y-auto"
                 style="z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
                <div class="flex min-h-full items-center justify-center p-4" style="z-index: 100000 !important;">
                    <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5 transform transition-all"
                         style="z-index: 100001 !important;"
                         @click.stop>
                        
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Salin Sitasi Ilmiah</h3>
                            </div>
                            <button type="button" 
                                    @click="citationModalOpen = false" 
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 line-clamp-2" x-text="activePaper.title"></h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400" x-text="activePaper.authors_string + ' (' + (activePaper.year || '') + ')'"></p>
                        </div>

                        <!-- Format Selector Tabs -->
                        <div class="space-y-3">
                            <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-900 p-1 border border-slate-200 dark:border-slate-700 w-full text-xs">
                                <button type="button" 
                                        @click="activeCitationFormat = 'apa'"
                                        class="flex-1 py-1.5 text-center rounded-lg font-bold transition-all"
                                        :class="activeCitationFormat === 'apa' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    APA 7th Edition
                                </button>
                                <button type="button" 
                                        @click="activeCitationFormat = 'ieee'"
                                        class="flex-1 py-1.5 text-center rounded-lg font-bold transition-all"
                                        :class="activeCitationFormat === 'ieee' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    IEEE Format
                                </button>
                                <button type="button" 
                                        @click="activeCitationFormat = 'bibtex'"
                                        class="flex-1 py-1.5 text-center rounded-lg font-bold transition-all"
                                        :class="activeCitationFormat === 'bibtex' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    BibTeX
                                </button>
                            </div>

                            <!-- Citation Display Box -->
                            <div class="relative">
                                <div class="p-4 bg-slate-900 dark:bg-slate-950 border border-slate-800 text-slate-100 rounded-2xl font-mono text-xs leading-relaxed max-h-48 overflow-y-auto select-all whitespace-pre-wrap break-words"
                                     x-text="getCurrentCitationText()">
                                </div>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1" x-show="copiedToast">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">Sitasi berhasil disalin ke clipboard!</span>
                            </span>
                            <div class="ml-auto flex items-center gap-2">
                                <button type="button" 
                                        @click="citationModalOpen = false" 
                                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    Tutup
                                </button>
                                <button type="button" 
                                        @click="copyCitation()" 
                                        class="px-5 py-2 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white flex items-center gap-1.5 shadow-sm shadow-orange-500/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                    <span>Salin Sitasi</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ================= MODAL 2: BATCH CITATION EXPORT MODAL ================= -->
        <template x-teleport="body">
            <div x-show="batchCitationModalOpen" 
                 x-cloak 
                 class="fixed inset-0 overflow-y-auto"
                 style="z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
                <div class="flex min-h-full items-center justify-center p-4 sm:p-6" style="z-index: 100000 !important;">
                    <div class="relative w-full max-w-3xl sm:max-w-4xl bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5 transform transition-all my-8"
                         style="z-index: 100001 !important;"
                         @click.stop>
                        
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Ekspor Seluruh Daftar Pustaka</h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                        Kompilasi sitasi dari <span class="font-bold text-orange-600" x-text="batchCitationData.count"></span> artikel tersimpan. Langsung tempel (*paste*) ke Bab Daftar Pustaka skripsi Anda.
                                    </p>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="batchCitationModalOpen = false" 
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Loading Indicator -->
                        <div x-show="batchCitationLoading" class="py-12 text-center space-y-3">
                            <svg class="w-8 h-8 mx-auto text-orange-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Menyusun format daftar pustaka...</p>
                        </div>

                        <!-- Content Box -->
                        <div x-show="!batchCitationLoading" class="space-y-4">
                            <!-- Format Tabs -->
                            <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-900 p-1 border border-slate-200 dark:border-slate-700 w-full text-xs">
                                <button type="button" 
                                        @click="batchCitationActiveFormat = 'apa'"
                                        class="flex-1 py-2 text-center rounded-lg font-bold transition-all"
                                        :class="batchCitationActiveFormat === 'apa' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    APA 7th Edition (Standar Skripsi)
                                </button>
                                <button type="button" 
                                        @click="batchCitationActiveFormat = 'ieee'"
                                        class="flex-1 py-2 text-center rounded-lg font-bold transition-all"
                                        :class="batchCitationActiveFormat === 'ieee' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    IEEE Format (Format Nomor [1], [2])
                                </button>
                                <button type="button" 
                                        @click="batchCitationActiveFormat = 'bibtex'"
                                        class="flex-1 py-2 text-center rounded-lg font-bold transition-all"
                                        :class="batchCitationActiveFormat === 'bibtex' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    BibTeX (LaTeX / Mendeley)
                                </button>
                            </div>

                            <!-- Textarea Display Box with proper wrapping -->
                            <div class="relative">
                                <textarea readonly 
                                          rows="12" 
                                          class="w-full p-4 bg-slate-900 dark:bg-slate-950 border border-slate-800 text-slate-100 rounded-2xl font-mono text-xs leading-relaxed select-all focus:ring-0 focus:outline-hidden whitespace-pre-wrap break-words"
                                          x-text="getBatchCitationText()"></textarea>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1" x-show="batchCitationCopied">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">Seluruh daftar pustaka berhasil disalin!</span>
                            </span>
                            <div class="ml-auto flex items-center gap-2">
                                <button type="button" 
                                        @click="batchCitationModalOpen = false" 
                                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    Tutup
                                </button>
                                <button type="button" 
                                        @click="copyBatchCitation()" 
                                        class="px-5 py-2 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white flex items-center gap-1.5 shadow-sm shadow-orange-500/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                    <span>Salin Seluruh Teks</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ================= MODAL 3: CREATE / EDIT FOLDER MODAL ================= -->
        <template x-teleport="body">
            <div x-show="folderModalOpen" 
                 x-cloak 
                 class="fixed inset-0 overflow-y-auto"
                 style="z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
                <div class="flex min-h-full items-center justify-center p-4" style="z-index: 100000 !important;">
                    <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5 transform transition-all"
                         style="z-index: 100001 !important;"
                         @click.stop>
                        
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="folderFormMode === 'create' ? 'Buat Folder Baru' : 'Ubah Nama Folder'"></h3>
                            </div>
                            <button type="button" 
                                    @click="folderModalOpen = false" 
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form @submit.prevent="saveFolder()" class="space-y-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Nama Folder / Bab Skripsi <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       x-model="folderFormName" 
                                       required 
                                       placeholder="Contoh: BAB II LANDASAN TEORI"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            </div>

                            <!-- Quick suggestions -->
                            <div class="space-y-1" x-show="folderFormMode === 'create'">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Saran Standar Bab Skripsi:</span>
                                <div class="flex flex-wrap gap-1 text-[11px]">
                                    <button type="button" @click="folderFormName = 'BAB I PENDAHULUAN'" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-orange-100 dark:hover:bg-orange-950/60 transition-colors">BAB I</button>
                                    <button type="button" @click="folderFormName = 'BAB II LANDASAN TEORI'" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-orange-100 dark:hover:bg-orange-950/60 transition-colors">BAB II</button>
                                    <button type="button" @click="folderFormName = 'BAB III OBJEK DAN METODOLOGI PENELITIAN'" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-orange-100 dark:hover:bg-orange-950/60 transition-colors">BAB III</button>
                                    <button type="button" @click="folderFormName = 'BAB IV HASIL DAN PEMBAHASAN'" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-orange-100 dark:hover:bg-orange-950/60 transition-colors">BAB IV</button>
                                    <button type="button" @click="folderFormName = 'BAB V PENUTUP'" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-orange-100 dark:hover:bg-orange-950/60 transition-colors">BAB V</button>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button" 
                                        @click="folderModalOpen = false" 
                                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" 
                                        :disabled="folderFormSaving || !folderFormName.trim()"
                                        class="px-5 py-2 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white flex items-center gap-1.5 shadow-sm shadow-orange-500/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': folderFormSaving }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span x-text="folderFormSaving ? 'Menyimpan...' : (folderFormMode === 'create' ? 'Buat Folder' : 'Simpan Perubahan')"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- ================= MODAL 4: MOVE BOOKMARK TO FOLDER ================= -->
        <template x-teleport="body">
            <div x-show="moveModalOpen" 
                 x-cloak 
                 class="fixed inset-0 overflow-y-auto"
                 style="z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
                <div class="flex min-h-full items-center justify-center p-4" style="z-index: 100000 !important;">
                    <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5 transform transition-all"
                         style="z-index: 100001 !important;"
                         @click.stop>
                        
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Pindahkan ke Folder</h3>
                            </div>
                            <button type="button" 
                                    @click="moveModalOpen = false" 
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Pilih folder tujuan untuk artikel ini:</p>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 line-clamp-2 mt-1" x-text="moveActiveBookmark?.title"></h4>
                        </div>

                        <!-- Folder Selection Radio List -->
                        <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            <!-- Option: Tanpa Folder -->
                            <label class="flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer"
                                   :class="moveTargetFolderId === null ? 'bg-orange-50/60 dark:bg-orange-950/30 border-orange-400 text-orange-700 dark:text-orange-300' : 'bg-slate-50 dark:bg-slate-900/60 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100'">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" :value="null" x-model="moveTargetFolderId" class="text-orange-600 focus:ring-orange-500">
                                    <span class="text-xs font-bold">Tanpa Folder (Semua Bacaan)</span>
                                </div>
                            </label>

                            <!-- Option per Folder -->
                            <template x-for="f in folders" :key="f.id">
                                <label class="flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer"
                                       :class="moveTargetFolderId == f.id ? 'bg-orange-50/60 dark:bg-orange-950/30 border-orange-400 text-orange-700 dark:text-orange-300' : 'bg-slate-50 dark:bg-slate-900/60 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100'">
                                    <div class="flex items-center gap-2.5">
                                        <input type="radio" :value="f.id" x-model="moveTargetFolderId" class="text-orange-600 focus:ring-orange-500">
                                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                        <span class="text-xs font-bold" x-text="f.name"></span>
                                    </div>
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-300" x-text="(f.bookmarks_count || 0) + ' artikel'"></span>
                                </label>
                            </template>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" 
                                    @click="moveModalOpen = false" 
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                Batal
                            </button>
                            <button type="button" 
                                    @click="confirmMoveFolder()" 
                                    :disabled="moveSaving"
                                    class="px-5 py-2 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white flex items-center gap-1.5 shadow-sm shadow-orange-500/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': moveSaving }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span x-text="moveSaving ? 'Memindahkan...' : 'Pindahkan Artikel'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
        function bookmarksApp(initialFolders = [], initialFolderFilter = 'all') {
            return {
                folders: initialFolders,
                folderFilter: initialFolderFilter,

                // Citation Modal State
                citationModalOpen: false,
                activeCitationFormat: 'apa',
                copiedToast: false,
                activePaper: {
                    title: '',
                    authors_string: '',
                    year: null,
                    citations: {
                        apa: '',
                        ieee: '',
                        bibtex: ''
                    }
                },

                // Batch Citation State
                batchCitationModalOpen: false,
                batchCitationLoading: false,
                batchCitationActiveFormat: 'apa',
                batchCitationCopied: false,
                batchCitationData: {
                    count: 0,
                    apa: '',
                    ieee: '',
                    bibtex: ''
                },

                // Folder Form Modal State
                folderModalOpen: false,
                folderFormMode: 'create', // 'create' or 'edit'
                folderFormId: null,
                folderFormName: '',
                folderFormSaving: false,
                templateLoading: false,

                // Move Bookmark Modal State
                moveModalOpen: false,
                moveActiveBookmark: null,
                moveTargetFolderId: null,
                moveSaving: false,

                // ============ SINGLE CITATION ============
                openCitationModal(paper) {
                    this.activePaper = paper;
                    this.citationModalOpen = true;
                    this.copiedToast = false;
                },

                getCurrentCitationText() {
                    if (!this.activePaper || !this.activePaper.citations) return '';
                    return this.activePaper.citations[this.activeCitationFormat] || '';
                },

                copyCitation() {
                    const text = this.getCurrentCitationText();
                    if (!text) return;

                    navigator.clipboard.writeText(text).then(() => {
                        this.copiedToast = true;
                        setTimeout(() => {
                            this.copiedToast = false;
                        }, 3000);
                    });
                },

                // ============ BATCH CITATIONS ============
                openBatchCitationModal() {
                    this.batchCitationModalOpen = true;
                    this.batchCitationLoading = true;
                    this.batchCitationCopied = false;

                    const urlParams = new URLSearchParams(window.location.search);
                    fetch(`/repositories/bookmarks/export-citations?${urlParams.toString()}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        this.batchCitationLoading = false;
                        if (d.success) {
                            this.batchCitationData = d;
                        }
                    })
                    .catch(() => {
                        this.batchCitationLoading = false;
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Gagal memuat seluruh sitasi.', type: 'error' }
                        }));
                    });
                },

                getBatchCitationText() {
                    return this.batchCitationData[this.batchCitationActiveFormat] || 'Tidak ada sitasi untuk ditampilkan.';
                },

                copyBatchCitation() {
                    const text = this.getBatchCitationText();
                    if (!text) return;

                    navigator.clipboard.writeText(text).then(() => {
                        this.batchCitationCopied = true;
                        setTimeout(() => {
                            this.batchCitationCopied = false;
                        }, 3000);
                    });
                },

                // ============ FOLDER CRUD ============
                openCreateFolderModal() {
                    this.folderFormMode = 'create';
                    this.folderFormId = null;
                    this.folderFormName = '';
                    this.folderModalOpen = true;
                },

                openEditFolderModal(folder) {
                    this.folderFormMode = 'edit';
                    this.folderFormId = folder.id;
                    this.folderFormName = folder.name;
                    this.folderModalOpen = true;
                },

                saveFolder() {
                    if (!this.folderFormName.trim()) return;
                    this.folderFormSaving = true;

                    const isCreate = this.folderFormMode === 'create';
                    const url = isCreate ? '/repositories/bookmarks/folders' : `/repositories/bookmarks/folders/${this.folderFormId}`;
                    const method = isCreate ? 'POST' : 'PATCH';

                    fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ name: this.folderFormName })
                    })
                    .then(r => r.json())
                    .then(d => {
                        this.folderFormSaving = false;
                        if (d.success) {
                            this.folderModalOpen = false;
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { title: 'Berhasil', message: d.message, type: 'success' }
                            }));
                            setTimeout(() => window.location.reload(), 300);
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { title: 'Gagal', message: d.message || 'Gagal menyimpan folder.', type: 'error' }
                            }));
                        }
                    })
                    .catch(() => {
                        this.folderFormSaving = false;
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Terjadi kesalahan sistem.', type: 'error' }
                        }));
                    });
                },

                deleteFolder(folder) {
                    if (!confirm(`Apakah Anda yakin ingin menghapus folder "${folder.name}"?\nArtikel di dalamnya TIDAK akan terhapus, melainkan dikembalikan ke status Tanpa Folder.`)) {
                        return;
                    }

                    fetch(`/repositories/bookmarks/folders/${folder.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { title: 'Folder Dihapus', message: d.message, type: 'info' }
                            }));
                            setTimeout(() => {
                                // If currently viewing the deleted folder, redirect to 'all'
                                const url = new URL(window.location.href);
                                url.searchParams.set('folder', 'all');
                                window.location.href = url.toString();
                            }, 300);
                        }
                    })
                    .catch(() => {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Gagal menghapus folder.', type: 'error' }
                        }));
                    });
                },

                applyThesisTemplate() {
                    if (!confirm('Apakah Anda ingin membuat 5 folder standar Bab Skripsi FASILKOM (BAB I s/d BAB V)?')) {
                        return;
                    }

                    this.templateLoading = true;
                    fetch('/repositories/bookmarks/folders/templates', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        this.templateLoading = false;
                        if (d.success) {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { title: 'Template Diterapkan', message: d.message, type: 'success' }
                            }));
                            setTimeout(() => window.location.reload(), 400);
                        }
                    })
                    .catch(() => {
                        this.templateLoading = false;
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Gagal membuat template folder.', type: 'error' }
                        }));
                    });
                },

                // ============ MOVE BOOKMARK TO FOLDER ============
                openMoveModal(paper) {
                    this.moveActiveBookmark = paper;
                    this.moveTargetFolderId = paper.folder_id || null;
                    this.moveModalOpen = true;
                },

                confirmMoveFolder() {
                    if (!this.moveActiveBookmark) return;
                    this.moveSaving = true;

                    const bookmarkId = this.moveActiveBookmark.bookmark_id;
                    fetch(`/repositories/bookmarks/${bookmarkId}/folder`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ folder_id: this.moveTargetFolderId })
                    })
                    .then(r => r.json())
                    .then(d => {
                        this.moveSaving = false;
                        if (d.success) {
                            this.moveModalOpen = false;
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { title: 'Folder Diperbarui', message: d.message, type: 'success' }
                            }));
                            setTimeout(() => window.location.reload(), 300);
                        }
                    })
                    .catch(() => {
                        this.moveSaving = false;
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Gagal memindahkan artikel.', type: 'error' }
                        }));
                    });
                },

                // ============ DELETE BOOKMARK ============
                deleteBookmark(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus artikel ini dari Daftar Bacaan?')) return;

                    fetch(`/repositories/bookmarks/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { title: 'Dihapus', message: d.message, type: 'info' }
                            }));
                            const el = document.getElementById(`bookmark-item-${id}`);
                            if (el) {
                                el.style.transition = 'all 0.3s ease';
                                el.style.opacity = '0';
                                el.style.transform = 'scale(0.95)';
                                setTimeout(() => window.location.reload(), 400);
                            } else {
                                window.location.reload();
                            }
                        }
                    })
                    .catch(() => {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Gagal menghapus artikel.', type: 'error' }
                        }));
                    });
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

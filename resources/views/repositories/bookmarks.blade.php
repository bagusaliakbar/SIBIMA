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

    <div class="w-full space-y-6" x-data="bookmarksApp()">
        @include('repositories.partials.tabs')

        <!-- HERO BANNER -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                        <span>Koleksi Referensi Mandiri</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 dark:text-white">
                        Daftar Bacaan Saya <span class="text-amber-500 font-black">({{ number_format($totalBookmarksCount) }})</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Koleksi artikel jurnal ilmiah yang Anda tandai untuk acuan skripsi. Dilengkapi catatan pribadi per artikel, unduh PDF instan, dan salin sitasi format ilmiah otomatis.
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('repositories.journals') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white shadow-sm shadow-orange-500/25 transition-all hover:scale-[1.02] active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Cari Artikel Lain</span>
                    </a>
                </div>
            </div>

            <!-- SEARCH & FILTER BAR -->
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 space-y-4">
                <form action="{{ route('repositories.bookmarks') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <input type="hidden" name="source" value="{{ $sourceFilter }}">
                    
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" 
                               name="q" 
                               value="{{ $query }}" 
                               placeholder="Cari judul artikel, nama penulis, nama jurnal, atau catatan Anda..."
                               class="w-full pl-10 pr-10 py-2.5 rounded-xl border text-xs bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-amber-400 dark:focus:ring-amber-500 focus:border-transparent transition-all">
                        @if(!empty($query))
                            <a href="{{ route('repositories.bookmarks', ['source' => $sourceFilter]) }}" 
                               class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                               title="Bersihkan pencarian">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>

                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white transition-all cursor-pointer">
                        Cari di Bacaan
                    </button>
                </form>

                <!-- SOURCE FILTER PILLS -->
                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mr-1.5">Filter Sumber:</span>
                    
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
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border
                                      {{ $sourceFilter === $sKey 
                                         ? 'bg-amber-500 text-white border-amber-500 shadow-xs' 
                                         : 'bg-white dark:bg-slate-900/60 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                                <span>{{ $sData['label'] }}</span>
                                <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ $sourceFilter === $sKey ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                    {{ $sData['count'] }}
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- CONTENT AREA / LIST OF BOOKMARKS -->
        @if($bookmarks->isEmpty())
            <!-- Empty State -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-10 sm:p-14 border border-slate-200/80 dark:border-slate-700/80 text-center space-y-4 shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                </div>
                <div class="max-w-md mx-auto space-y-2">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">
                        @if(!empty($query))
                            Tidak Ditemukan Artikel yang Cocok
                        @else
                            Belum Ada Artikel di Daftar Bacaan
                        @endif
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        @if(!empty($query))
                            Tidak ada artikel tersimpan yang sesuai dengan kata kunci "{{ $query }}". Coba cari dengan kata kunci lain.
                        @else
                            Saat Anda mencari artikel di katalog jurnal ilmiah, klik tombol <strong>"Simpan Bacaan"</strong> pada artikel yang relevan agar tersimpan di sini.
                        @endif
                    </p>
                </div>
                <div class="pt-2 flex items-center justify-center gap-3">
                    @if(!empty($query) || $sourceFilter !== 'all')
                        <a href="{{ route('repositories.bookmarks') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 transition-colors">
                            <span>Reset Filter</span>
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
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:border-amber-300 dark:hover:border-amber-500/50 hover:shadow-lg transition-all space-y-4"
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

                        <!-- Paper Title -->
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 leading-snug">
                                <a href="{{ $bookmark->url ?: ($bookmark->pdf_url ?: '#') }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                    {{ $bookmark->title }}
                                </a>
                            </h3>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                                <span class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ $bookmark->authors_string }}
                                </span>
                                @if($bookmark->venue)
                                    <span>•</span>
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
                                        class="mt-1.5 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-bold inline-flex items-center gap-1 cursor-pointer">
                                    <span x-text="showAbstract ? 'Sembunyikan Abstrak' : 'Baca Abstrak Lengkap...'"></span>
                                    <svg class="w-3 h-3 transition-transform" :class="showAbstract ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        <!-- PERSONAL STUDENT NOTES BOX -->
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
                                    <p class="text-slate-400 dark:text-slate-500 italic text-[11px] cursor-pointer" @click="editingNotes = true">
                                        Belum ada catatan. Klik di sini untuk mencatat keterkaitan artikel ini dengan skripsi Anda (misal: "Referensi Bab 2 - Metode Random Forest").
                                    </p>
                                </template>
                            </div>

                            <!-- Notes Edit Mode -->
                            <div x-show="editingNotes" x-cloak class="space-y-2 pt-1">
                                <textarea x-model="notesText" 
                                          rows="3" 
                                          placeholder="Tuliskan catatan referensi skripsi Anda di sini (misal: Landasan teori Bab 2, studi pembanding Bab 4, dll)..."
                                          class="w-full text-xs p-3 rounded-xl bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"></textarea>
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" 
                                            @click="editingNotes = false" 
                                            class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-400 hover:bg-amber-100/60 dark:hover:bg-slate-800 transition-colors">
                                        Batal
                                    </button>
                                    <button type="button" 
                                            @click="saveNotes()" 
                                            :disabled="savingNotes"
                                            class="px-4 py-1.5 rounded-lg text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white flex items-center gap-1.5 transition-all shadow-xs cursor-pointer">
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

                                <!-- Remove Bookmark Button -->
                                <button type="button" 
                                        @click="deleteBookmark({{ $bookmark->id }})"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 transition-all cursor-pointer">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Hapus</span>
                                </button>
                            </div>

                            <!-- PDF CTA -->
                            @if($bookmark->pdf_url)
                                <a href="{{ $bookmark->pdf_url }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white transition-all shadow-sm shadow-orange-500/20 hover:scale-[1.02] active:scale-95">
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Buka PDF Full-Text</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- PAGINATION -->
            <div class="pt-2">
                {{ $bookmarks->links() }}
            </div>
        @endif

        <!-- CITATION MODAL DIALOG -->
        <div x-show="citationModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto"
             @keydown.escape.window="citationModalOpen = false">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                 @click="citationModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5 transform transition-all"
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
                            <div class="p-4 bg-slate-900 dark:bg-slate-950 border border-slate-800 text-slate-100 rounded-2xl font-mono text-xs leading-relaxed max-h-48 overflow-y-auto select-all"
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
    </div>

    @push('scripts')
    <script>
        function bookmarksApp() {
            return {
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

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
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-all space-y-4"
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
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-black border shadow-2xs bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/70" title="Terakreditasi SINTA 4">
                                        <svg class="w-3 h-3 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span>SINTA 4</span>
                                    </span>
                                @elseif($bookmark->source === 'garuda')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200 border border-rose-200 dark:border-rose-800/70 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        <span>Jurnal Nasional GARUDA (SINTA)</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-black border shadow-2xs bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800/70" title="Terakreditasi SINTA Nasional">
                                        <svg class="w-3 h-3 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span>SINTA Nasional</span>
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
                                @elseif(in_array($bookmark->source, ['openlibrary', 'doab', 'googlebooks', 'books']))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-sky-50 dark:bg-sky-950/60 text-sky-800 dark:text-sky-200 border border-sky-200 dark:border-sky-800/70 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-sky-600 dark:text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span>{{ $bookmark->clean_source_label ?: 'Buku Teks / E-Book' }}</span>
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

                            @php
                                $safeBookmarkUrl = (is_string($bookmark->url) && preg_match('#^https?://#i', trim($bookmark->url))) ? trim($bookmark->url) : null;
                                $safeBookmarkPdfUrl = (is_string($bookmark->pdf_url) && preg_match('#^https?://#i', trim($bookmark->pdf_url))) ? trim($bookmark->pdf_url) : null;
                                $safeBookmarkDoi = null;
                                if (is_string($bookmark->doi) && trim($bookmark->doi) !== '') {
                                    $rawDoi = trim($bookmark->doi);
                                    if (preg_match('#^https?://#i', $rawDoi)) {
                                        $safeBookmarkDoi = $rawDoi;
                                    } elseif (!str_contains($rawDoi, ':') && !preg_match('/\s/', $rawDoi)) {
                                        $safeBookmarkDoi = 'https://doi.org/' . $rawDoi;
                                    }
                                }
                            @endphp
                            <div class="flex items-center gap-3 text-[11px] text-slate-400 dark:text-slate-500">
                                <span>Disimpan: {{ $bookmark->created_at->diffForHumans() }}</span>
                                
                                @if($safeBookmarkDoi)
                                    <span>•</span>
                                    <a href="{{ $safeBookmarkDoi }}" target="_blank" rel="noopener noreferrer" 
                                       class="font-mono hover:text-orange-500 dark:hover:text-orange-400 flex items-center gap-1 transition-colors">
                                        <span>DOI: {{ str_replace('https://doi.org/', '', $safeBookmarkDoi) }}</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Paper Title & Authors/Venue with Proper Breathing Room -->
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 leading-snug">
                                <a href="{{ $safeBookmarkUrl ?: ($safeBookmarkPdfUrl ?: ($safeBookmarkDoi ?: '#')) }}" 
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
                                @if($safeBookmarkUrl)
                                    <a href="{{ $safeBookmarkUrl }}" 
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
                            @if($safeBookmarkPdfUrl)
                                <a href="{{ $safeBookmarkPdfUrl }}" 
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

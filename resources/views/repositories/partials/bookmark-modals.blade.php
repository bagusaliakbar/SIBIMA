        <!-- ================= MODAL 1: SINGLE CITATION MODAL ================= -->
        <template x-teleport="body">
            <div x-show="citationModalOpen" 
                 x-cloak 
                 @keydown.escape.window="if (citationModalOpen) citationModalOpen = false"
                 role="dialog"
                 aria-modal="true"
                 class="fixed inset-0 z-[60] overflow-y-auto"
                 style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
                <div class="relative z-10 flex min-h-full items-center justify-center p-4">
                    <div class="relative z-20 w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5 transform transition-all"
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
                                    aria-label="Tutup modal sitasi"
                                    class="text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 line-clamp-2" x-text="activePaper.title"></h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400" x-text="activePaper.authors_string + ' (' + (activePaper.year || '') + ')'"></p>
                        </div>

                        <!-- Format Selector Tabs -->
                        <div class="space-y-3">
                            <div class="inline-flex flex-wrap rounded-xl bg-slate-100 dark:bg-slate-900 p-1 w-full text-xs gap-1">
                                <button type="button" 
                                        @click="activeCitationFormat = 'fasilkom'"
                                        class="flex-1 min-w-24 py-1.5 px-2 text-center rounded-lg font-bold transition-all cursor-pointer"
                                        :class="activeCitationFormat === 'fasilkom' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    FASILKOM UNSUB (Word)
                                </button>
                                <button type="button" 
                                        @click="activeCitationFormat = 'apa'"
                                        class="flex-1 min-w-16 py-1.5 px-2 text-center rounded-lg font-bold transition-all cursor-pointer"
                                        :class="activeCitationFormat === 'apa' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    APA 7th
                                </button>
                                <button type="button" 
                                        @click="activeCitationFormat = 'ieee'"
                                        class="flex-1 min-w-16 py-1.5 px-2 text-center rounded-lg font-bold transition-all cursor-pointer"
                                        :class="activeCitationFormat === 'ieee' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    IEEE
                                </button>
                                <button type="button" 
                                        @click="activeCitationFormat = 'bibtex'"
                                        class="flex-1 min-w-16 py-1.5 px-2 text-center rounded-lg font-bold transition-all cursor-pointer"
                                        :class="activeCitationFormat === 'bibtex' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    BibTeX
                                </button>
                                <button type="button" 
                                        @click="activeCitationFormat = 'ris'"
                                        class="flex-1 min-w-16 py-1.5 px-2 text-center rounded-lg font-bold transition-all cursor-pointer"
                                        :class="activeCitationFormat === 'ris' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    File .RIS
                                </button>
                            </div>

                            <!-- Citation Display Box -->
                            <div class="relative">
                                <div class="p-4 bg-slate-900 dark:bg-slate-950 text-slate-100 rounded-2xl font-mono text-xs leading-relaxed max-h-48 overflow-y-auto select-all whitespace-pre-wrap break-words"
                                     x-text="getCurrentCitationText()">
                                </div>
                            </div>

                            <!-- Helper Tip -->
                            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 px-1">
                                <span x-show="activeCitationFormat === 'fasilkom'">
                                    Format resmi panduan skripsi FASILKOM UNSUB untuk lembar daftar pustaka Microsoft Word.
                                </span>
                                <span x-show="activeCitationFormat === 'ris'">
                                    Format metadata untuk Mendeley Desktop/Reference Manager, Zotero, dan EndNote.
                                </span>
                                <span x-show="activeCitationFormat === 'bibtex'">
                                    Format entri sitasi untuk LaTeX / Overleaf.
                                </span>
                                <span x-show="activeCitationFormat === 'apa' || activeCitationFormat === 'ieee'">
                                    Format sitasi standar akademik internasional.
                                </span>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-700/80">
                            <div class="text-xs">
                                <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5" x-show="copiedToast">
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="copiedToastMessage"></span>
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <!-- Download .RIS -->
                                <button type="button" 
                                        @click="downloadRis()" 
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 flex items-center gap-1.5 transition-all shadow-2xs cursor-pointer active:scale-95"
                                        title="Unduh metadata artikel dalam file .RIS untuk Mendeley, Zotero, atau EndNote">
                                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span>Unduh .RIS</span>
                                </button>

                                <!-- Copy Word (FASILKOM) -->
                                <button type="button" 
                                        @click="copyFasilkomWord()" 
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white flex items-center gap-1.5 shadow-sm shadow-amber-500/25 transition-all hover:scale-[1.01] active:scale-95 cursor-pointer"
                                        title="Salin daftar pustaka siap pakai untuk Microsoft Word sesuai panduan skripsi FASILKOM UNSUB">
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Salin untuk Word</span>
                                </button>

                                <!-- Copy Selected Text -->
                                <button type="button" 
                                        @click="copyCitation()" 
                                        class="px-4 py-2 rounded-xl text-xs font-bold bg-orange-500 hover:bg-orange-600 text-white flex items-center gap-1.5 shadow-sm shadow-orange-500/25 transition-all hover:scale-[1.01] active:scale-95 cursor-pointer">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                    <span>Salin Teks</span>
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
                 @keydown.escape.window="if (batchCitationModalOpen) batchCitationModalOpen = false"
                 role="dialog"
                 aria-modal="true"
                 class="fixed inset-0 z-[60] overflow-y-auto"
                 style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
                <div class="relative z-10 flex min-h-full items-center justify-center p-4 sm:p-6">
                    <div class="relative z-20 w-full max-w-3xl sm:max-w-4xl bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5 transform transition-all my-8"
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
                                    aria-label="Tutup modal ekspor sitasi massal"
                                    class="text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
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
                            <div class="inline-flex flex-wrap rounded-xl bg-slate-100 dark:bg-slate-900 p-1 w-full text-xs gap-1">
                                <button type="button" 
                                        @click="batchCitationActiveFormat = 'fasilkom'"
                                        class="flex-1 min-w-28 py-2 px-2 text-center rounded-lg font-bold transition-all cursor-pointer"
                                        :class="batchCitationActiveFormat === 'fasilkom' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    Panduan FASILKOM (Word)
                                </button>
                                <button type="button" 
                                        @click="batchCitationActiveFormat = 'apa'"
                                        class="flex-1 min-w-20 py-2 px-2 text-center rounded-lg font-bold transition-all cursor-pointer"
                                        :class="batchCitationActiveFormat === 'apa' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    APA 7th
                                </button>
                                <button type="button" 
                                        @click="batchCitationActiveFormat = 'ieee'"
                                        class="flex-1 min-w-20 py-2 px-2 text-center rounded-lg font-bold transition-all cursor-pointer"
                                        :class="batchCitationActiveFormat === 'ieee' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    IEEE Format
                                </button>
                                <button type="button" 
                                        @click="batchCitationActiveFormat = 'bibtex'"
                                        class="flex-1 min-w-20 py-2 px-2 text-center rounded-lg font-bold transition-all cursor-pointer"
                                        :class="batchCitationActiveFormat === 'bibtex' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    BibTeX (LaTeX)
                                </button>
                                <button type="button" 
                                        @click="batchCitationActiveFormat = 'ris'"
                                        class="flex-1 min-w-20 py-2 px-2 text-center rounded-lg font-bold transition-all cursor-pointer"
                                        :class="batchCitationActiveFormat === 'ris' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                    File .RIS (Semua)
                                </button>
                            </div>

                            <!-- Textarea Display Box with proper wrapping -->
                            <div class="relative">
                                <textarea readonly 
                                          rows="12" 
                                          class="w-full p-4 bg-slate-900 dark:bg-slate-950 text-slate-100 rounded-2xl font-mono text-xs leading-relaxed select-all focus:ring-0 focus:outline-hidden whitespace-pre-wrap break-words"
                                          x-text="getBatchCitationText()"></textarea>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-700/80">
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1" x-show="batchCitationCopied">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="batchCitationCopiedMessage || 'Seluruh daftar pustaka berhasil disalin!'"></span>
                            </span>
                            <div class="flex flex-wrap items-center justify-end gap-2 ml-auto">
                                <!-- Download all .RIS -->
                                <button type="button" 
                                        @click="downloadBatchRis()" 
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 flex items-center gap-1.5 transition-all shadow-2xs cursor-pointer active:scale-95"
                                        title="Unduh seluruh daftar pustaka sebagai file .RIS untuk Mendeley/Zotero">
                                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span>Unduh Semua .RIS</span>
                                </button>

                                <!-- Copy Word (FASILKOM) -->
                                <button type="button" 
                                        @click="copyBatchFasilkomWord()" 
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white flex items-center gap-1.5 shadow-sm shadow-amber-500/25 transition-all hover:scale-[1.01] active:scale-95 cursor-pointer"
                                        title="Salin seluruh daftar pustaka siap pakai untuk Microsoft Word">
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Salin untuk Word</span>
                                </button>

                                <button type="button" 
                                        @click="copyBatchCitation()" 
                                        class="px-4 py-2 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white flex items-center gap-1.5 shadow-sm shadow-orange-500/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                    <span>Salin Teks</span>
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
                 @keydown.escape.window="if (folderModalOpen) folderModalOpen = false"
                 role="dialog"
                 aria-modal="true"
                 class="fixed inset-0 z-[60] overflow-y-auto"
                 style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
                <div class="relative z-10 flex min-h-full items-center justify-center p-4">
                    <div class="relative z-20 w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5 transform transition-all"
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
                                    aria-label="Tutup modal folder"
                                    class="text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
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
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Saran Standar Bab Skripsi:</span>
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
                 @keydown.escape.window="if (moveModalOpen) moveModalOpen = false"
                 role="dialog"
                 aria-modal="true"
                 class="fixed inset-0 z-[60] overflow-y-auto"
                 style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
                <div class="relative z-10 flex min-h-full items-center justify-center p-4">
                    <div class="relative z-20 w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5 transform transition-all"
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
                                    aria-label="Tutup modal pindah folder"
                                    class="text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
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
                                    <span class="text-[11px] font-black px-2 py-0.5 rounded-md bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-300" x-text="(f.bookmarks_count || 0) + ' artikel'"></span>
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

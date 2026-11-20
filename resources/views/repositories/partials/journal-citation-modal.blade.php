        <!-- INTERACTIVE CITATION MODAL -->
        <template x-teleport="body">
            <div x-show="citationModalOpen" 
                 x-cloak 
                 @keydown.escape.window="if (citationModalOpen) citationModalOpen = false"
                 class="fixed inset-0 z-[60] overflow-y-auto"
                 role="dialog" 
                 aria-modal="true">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm transition-opacity" 
                     @click="citationModalOpen = false"></div>

            <div class="relative z-10 flex min-h-screen items-center justify-center p-4">
                <div class="relative z-20 bg-white dark:bg-slate-800 rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200/80 dark:border-slate-700 space-y-5"
                     @click.stop>
                    
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 dark:border-slate-700/80 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-orange-500/10 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Kutip / Sitasi Artikel Ini</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Pilih format sitasi yang diinginkan dan salin ke daftar pustaka skripsi Anda.</p>
                            </div>
                        </div>
                        <button type="button" 
                                @click="citationModalOpen = false" 
                                aria-label="Tutup modal sitasi jurnal"
                                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Paper Mini Preview -->
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900 rounded-xl space-y-1">
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
                            <div class="p-4 bg-slate-900 dark:bg-slate-950 text-slate-100 rounded-2xl font-mono text-xs leading-relaxed max-h-48 overflow-y-auto select-all"
                                 x-text="getCurrentCitationText()">
                            </div>
                        </div>

                        <!-- Helper Tip -->
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 px-1">
                            <span x-show="activeCitationFormat === 'fasilkom'">
                                Format resmi panduan skripsi FASILKOM UNSUB. Siap disalin langsung ke lembar daftar pustaka Microsoft Word.
                            </span>
                            <span x-show="activeCitationFormat === 'ris'">
                                Format metadata standar untuk diimpor ke Mendeley Desktop/Reference Manager, Zotero, dan EndNote.
                            </span>
                            <span x-show="activeCitationFormat === 'bibtex'">
                                Format entri sitasi untuk penulisan skripsi berbasis LaTeX / Overleaf.
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

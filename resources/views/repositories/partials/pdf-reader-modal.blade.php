        <!-- MODAL IN-APP PDF READER -->
        <template x-teleport="body">
            <div x-show="pdfReaderOpen" 
                 id="pdfReaderModalElement"
                 role="dialog"
                 aria-modal="true"
                 class="fixed inset-0 select-none overflow-hidden" 
                 :style="'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 68 !important; background-color: #0b0f19 !important;' + (pdfReaderOpen ? ' display: flex !important; flex-direction: column !important;' : ' display: none !important;')"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.window.escape="if (pdfReaderOpen) closePdfReader()"
                 @keydown.window.left="if (pdfReaderOpen && !['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) changePage(-1)"
                 @keydown.window.right="if (pdfReaderOpen && !['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) changePage(1)">
                
                <!-- TOP HEADER TOOLBAR -->
                <header style="background-color: #0f172a !important; border-bottom: 1px solid #1e293b !important; color: #f8fafc !important; flex: 0 0 auto !important;" 
                        class="px-3 sm:px-5 py-2.5 shadow-2xl flex flex-wrap lg:flex-nowrap items-center justify-between gap-2.5 z-30 shrink-0">
                    
                    <!-- LEFT: Doc Info & Chapter Switcher -->
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Chapter Switcher Segmented Control -->
                        <div style="background-color: #020617 !important; border: 1px solid #1e293b !important;" 
                             class="inline-flex p-1 rounded-xl shrink-0 gap-0.5">
                            <button type="button" 
                                    @click="switchChapter('bab1')" 
                                    :disabled="!pdfRepo.file_path"
                                    :style="pdfChapter === 'bab1' ? 'background-color: #e11d48 !important; color: #ffffff !important;' : 'color: #94a3b8 !important;'"
                                    class="px-2 sm:px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                    title="BAB 1 (Pendahuluan)">
                                <span>BAB 1</span>
                            </button>
                            <button type="button" 
                                    @click="switchChapter('bab2')" 
                                    :disabled="!pdfRepo.file_path_bab2"
                                    :style="pdfChapter === 'bab2' ? 'background-color: #d97706 !important; color: #ffffff !important;' : 'color: #94a3b8 !important;'"
                                    class="px-2 sm:px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                    title="BAB 2 (Tinjauan Pustaka)">
                                <span>BAB 2</span>
                            </button>
                            <button type="button" 
                                    @click="switchChapter('bab3')" 
                                    :disabled="!pdfRepo.file_path_bab3"
                                    :style="pdfChapter === 'bab3' ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : 'color: #94a3b8 !important;'"
                                    class="px-2 sm:px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                    title="BAB 3 (Metodologi Penelitian)">
                                <span>BAB 3</span>
                            </button>
                            <button type="button" 
                                    @click="switchChapter('bab4')" 
                                    :disabled="!pdfRepo.file_path_bab4"
                                    :style="pdfChapter === 'bab4' ? 'background-color: #059669 !important; color: #ffffff !important;' : 'color: #94a3b8 !important;'"
                                    class="px-2 sm:px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                    title="BAB 4 (Hasil dan Pembahasan)">
                                <span>BAB 4</span>
                            </button>
                            <button type="button" 
                                    @click="switchChapter('bab5')" 
                                    :disabled="!pdfRepo.file_path_bab5"
                                    :style="pdfChapter === 'bab5' ? 'background-color: #7c3aed !important; color: #ffffff !important;' : 'color: #94a3b8 !important;'"
                                    class="px-2 sm:px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                    title="BAB 5">
                                <span>BAB 5</span>
                            </button>
                            <button type="button" 
                                    @click="switchChapter('bab6')" 
                                    :disabled="!pdfRepo.file_path_bab6"
                                    :style="pdfChapter === 'bab6' ? 'background-color: #0891b2 !important; color: #ffffff !important;' : 'color: #94a3b8 !important;'"
                                    class="px-2 sm:px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                    title="BAB 6">
                                <span>BAB 6</span>
                            </button>
                        </div>

                        <!-- Thesis Metadata Summary -->
                        <div class="hidden md:block truncate max-w-xs lg:max-w-md">
                            <h4 class="text-xs font-black text-slate-100 truncate tracking-tight" :title="pdfRepo.title" x-text="pdfRepo.title"></h4>
                            <p class="text-[10px] text-slate-400 font-medium truncate mt-0.5">
                                <span class="font-bold text-orange-400" x-text="pdfRepo.name"></span>
                                <span class="mx-1">•</span>
                                <span x-text="'NIM: ' + pdfRepo.identifier"></span>
                                <span class="mx-1">•</span>
                                <span x-text="'Angkatan ' + pdfRepo.year"></span>
                            </p>
                        </div>
                    </div>

                    <!-- CENTER: Navigation & Zoom -->
                    <div class="flex items-center gap-2 sm:gap-3 flex-wrap justify-center">
                        <!-- Page Controls -->
                        <div style="background-color: #020617 !important; border: 1px solid #1e293b !important;" 
                             class="flex items-center gap-1 rounded-xl px-2 py-1">
                            <button type="button" 
                                    @click="changePage(-1)" 
                                    :disabled="pdfCurrentPage <= 1"
                                    class="p-1 text-slate-300 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed rounded transition-colors cursor-pointer" 
                                    title="Halaman Sebelumnya (←)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <div class="flex items-center gap-1 text-xs font-bold text-slate-200 px-1">
                                <input type="number" 
                                       :value="pdfCurrentPage" 
                                       @change="goToPage($event.target.value)" 
                                       min="1" 
                                       :max="pdfTotalPages || 1" 
                                       style="background-color: #0f172a !important; border: 1px solid #334155 !important; color: #ffffff !important;"
                                       class="w-10 text-center rounded px-1 py-0.5 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <span class="text-slate-500">/</span>
                                <span x-text="pdfTotalPages || '-'" class="font-mono text-slate-300 min-w-[16px] text-center"></span>
                            </div>
                            <button type="button" 
                                    @click="changePage(1)" 
                                    :disabled="pdfCurrentPage >= pdfTotalPages"
                                    class="p-1 text-slate-300 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed rounded transition-colors cursor-pointer" 
                                    title="Halaman Berikutnya (→)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>

                        <!-- Zoom Controls -->
                        <div style="background-color: #020617 !important; border: 1px solid #1e293b !important;" 
                             class="flex items-center gap-1 rounded-xl px-2 py-1">
                            <button type="button" 
                                    @click="zoomOut()" 
                                    class="p-1 text-slate-300 hover:text-white rounded transition-colors cursor-pointer" 
                                    title="Perkecil Zoom (-)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg>
                            </button>
                            <span x-text="Math.round(pdfScale * 100) + '%'" class="text-[11px] font-mono font-bold text-slate-200 min-w-[40px] text-center"></span>
                            <button type="button" 
                                    @click="zoomIn()" 
                                    class="p-1 text-slate-300 hover:text-white rounded transition-colors cursor-pointer" 
                                    title="Perbesar Zoom (+)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                            <button type="button" 
                                    @click="fitWidth()" 
                                    style="background-color: #1e293b !important; color: #f1f5f9 !important;"
                                    class="px-2 py-0.5 text-[10px] font-bold hover:bg-slate-700 rounded transition-colors cursor-pointer" 
                                    title="Sesuaikan Lebar Layar">
                                Fit
                            </button>
                        </div>
                    </div>

                    <!-- RIGHT: Search, Protection Toggle, Actions -->
                    <div class="flex items-center gap-2 shrink-0">
                        <!-- In-Document Search Input -->
                        <div style="background-color: #020617 !important; border: 1px solid #1e293b !important;" 
                             class="flex items-center rounded-xl px-2.5 py-1 gap-1.5 focus-within:border-orange-500 transition-all shrink-0">
                            <!-- Magnifying Glass Icon -->
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>

                            <!-- Input Field -->
                            <input type="text" 
                                   x-model="pdfSearchQuery" 
                                   @keydown.enter="searchInPdf()" 
                                   placeholder="Cari di bab..." 
                                   style="background: transparent !important; border: none !important; color: #ffffff !important; outline: none !important; box-shadow: none !important; padding: 0 !important;"
                                   class="w-20 sm:w-28 text-xs placeholder-slate-500 focus:outline-none focus:ring-0">

                            <!-- Clear Query Button (only visible when text entered) -->
                            <button type="button" 
                                    x-show="pdfSearchQuery && pdfSearchQuery.length > 0"
                                    aria-label="Hapus pencarian teks PDF"
                                    @click="pdfSearchQuery = ''; pdfSearchStatus = ''; pdfSearchMatches = [];"
                                    class="p-0.5 text-slate-400 hover:text-white rounded transition-colors shrink-0 cursor-pointer" 
                                    title="Hapus kata kunci">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>

                            <!-- Search Trigger Button -->
                            <button type="button" 
                                    @click="searchInPdf()" 
                                    style="background-color: #ea580c !important; color: #ffffff !important;"
                                    class="px-2 py-0.5 rounded-lg text-[10px] font-bold hover:bg-orange-600 active:scale-95 transition-all shrink-0 cursor-pointer" 
                                    title="Cari kata kunci di naskah (Enter)">
                                Cari
                            </button>

                            <!-- Prev / Next Navigation when matches exist -->
                            <template x-if="pdfSearchMatches.length > 0">
                                <div class="flex items-center gap-0.5 border-l border-slate-700 pl-1.5 shrink-0">
                                    <button type="button" @click="prevSearchMatch()" class="p-0.5 text-slate-300 hover:text-white rounded cursor-pointer" title="Hasil Sebelumnya">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    <button type="button" @click="nextSearchMatch()" class="p-0.5 text-slate-300 hover:text-white rounded cursor-pointer" title="Hasil Berikutnya">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Read-Only Mode Toggle -->
                        <button type="button" 
                                @click="toggleReadOnly()" 
                                :style="pdfReadOnly ? 'background-color: #064e3b !important; color: #6ee7b7 !important; border: 1px solid #059669 !important;' : 'background-color: #0f172a !important; color: #94a3b8 !important; border: 1px solid #1e293b !important;'"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-2xs" 
                                :title="pdfReadOnly ? 'Mode Proteksi Aktif (Anti-Copy & Watermark Digital)' : 'Mode Proteksi Nonaktif'">
                            <svg class="w-3.5 h-3.5" :class="pdfReadOnly ? 'text-emerald-300' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span class="hidden sm:inline" x-text="pdfReadOnly ? 'Read-Only' : 'Standar'"></span>
                        </button>

                        <!-- Open in External Tab -->
                        <a :href="'/repositories/' + pdfRepo.id + '/' + pdfChapter" 
                           target="_blank" 
                           style="background-color: #020617 !important; border: 1px solid #1e293b !important; color: #cbd5e1 !important;"
                           class="p-1.5 hover:bg-slate-800 hover:text-white rounded-xl transition-colors" 
                           title="Buka File di Tab Baru Browser">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>

                        <!-- Fullscreen Toggle -->
                        <button type="button" 
                                @click="toggleFullscreen()" 
                                style="background-color: #020617 !important; border: 1px solid #1e293b !important; color: #cbd5e1 !important;"
                                class="p-1.5 hover:bg-slate-800 hover:text-white rounded-xl transition-colors cursor-pointer" 
                                title="Layar Penuh (Fullscreen)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        </button>

                        <!-- Close Button -->
                        <button type="button" 
                                @click="closePdfReader()" 
                                style="background-color: #e11d48 !important; color: #ffffff !important;"
                                class="px-3 py-1 hover:bg-rose-700 active:scale-95 rounded-xl text-xs font-bold flex items-center gap-1 transition-all cursor-pointer shadow-md" 
                                title="Tutup Viewer (Esc)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span class="hidden sm:inline">Tutup</span>
                        </button>
                    </div>
                </header>

                <!-- SEARCH STATUS SUB-BAR (IF ACTIVE) -->
                <div x-show="pdfSearchStatus" 
                     style="background-color: #451a03 !important; border-bottom: 1px solid #78350f !important; color: #fde68a !important;"
                     class="px-4 py-1 text-center text-xs font-bold flex items-center justify-center gap-2 shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Hasil Pencarian: </span>
                    <span x-text="pdfSearchStatus" class="underline font-mono"></span>
                    <button type="button" @click="pdfSearchQuery = ''; pdfSearchStatus = ''; pdfSearchMatches = [];" class="ml-2 text-amber-300 hover:text-white text-[10px] uppercase font-bold">&times; Bersihkan</button>
                </div>

                <!-- MAIN VIEWER VIEWPORT (RELATIVE, OVERFLOW HIDDEN, NON-SCROLLING WRAPPER) -->
                <div class="relative flex-1 min-h-0 w-full h-full overflow-hidden flex flex-col"
                     style="position: relative !important; flex: 1 1 0% !important; min-height: 0 !important; width: 100% !important; height: 100% !important; overflow: hidden !important; display: flex !important; flex-direction: column !important;">
                    
                    <!-- Loading State Overlay (Dead-Center in Screen) -->
                    <div x-show="pdfIsLoading" 
                         x-cloak
                         :style="pdfIsLoading ? 'position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100% !important; height: 100% !important; background-color: rgba(2, 6, 23, 0.88) !important; backdrop-filter: blur(4px) !important; z-index: 50 !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; text-align: center !important; padding: 1.5rem !important;' : 'display: none !important;'">
                        <div class="w-12 h-12 border-4 border-orange-500/20 border-t-orange-500 rounded-full animate-spin mb-4 shrink-0 shadow-lg shadow-orange-500/20 mx-auto"></div>
                        <p class="text-xs text-slate-300 mt-1.5 font-medium text-center max-w-sm mx-auto" 
                           x-text="({'bab1':'Menyiapkan BAB 1 (Pendahuluan)','bab2':'Menyiapkan BAB 2 (Tinjauan Pustaka)','bab3':'Menyiapkan BAB 3 (Metodologi Penelitian)','bab4':'Menyiapkan BAB 4 (Hasil dan Pembahasan)','bab5':'Menyiapkan BAB 5 (Implementasi / Kesimpulan)','bab6':'Menyiapkan BAB 6 (Kesimpulan dan Saran)'})[pdfChapter] || 'Menyiapkan Naskah PDF'"></p>
                    </div>

                    <!-- Error State Overlay (Dead-Center in Screen) -->
                    <div x-show="pdfLoadingError" 
                         x-cloak
                         :style="pdfLoadingError ? 'position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100% !important; height: 100% !important; background-color: rgba(2, 6, 23, 0.92) !important; backdrop-filter: blur(4px) !important; z-index: 50 !important; display: flex !important; align-items: center !important; justify-content: center !important; text-align: center !important; padding: 1.5rem !important;' : 'display: none !important;'">
                        <div style="background-color: #0f172a !important; border: 1px solid #991b1b !important;"
                             class="max-w-md w-full p-6 rounded-3xl text-center shadow-2xl mx-auto">
                            <div class="w-12 h-12 bg-rose-500/10 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h4 class="text-base font-bold text-white mb-1">Gagal Membuka Naskah</h4>
                            <p class="text-xs text-slate-400 mb-5" x-text="pdfLoadingError"></p>
                            <div class="flex items-center justify-center gap-3">
                                <button type="button" @click="loadPdf()" style="background-color: #1e293b !important; color: #ffffff !important; border: 1px solid #334155 !important;" class="px-4 py-2 hover:bg-slate-700 rounded-xl text-xs font-bold transition-colors cursor-pointer">Coba Lagi</button>
                                <a :href="'/repositories/' + pdfRepo.id + '/' + pdfChapter" target="_blank" style="background-color: #ea580c !important; color: #ffffff !important;" class="px-4 py-2 hover:bg-orange-700 rounded-xl text-xs font-bold transition-colors">Buka di Tab Baru</a>
                            </div>
                        </div>
                    </div>

                    <!-- DOCUMENT CANVAS SCROLL BODY -->
                    <div id="pdfViewerContainer" 
                         class="p-4 sm:p-8 flex-1 min-h-0 w-full h-full overflow-y-auto overflow-x-auto select-none"
                         style="background-color: #020617 !important; flex: 1 1 0% !important; min-height: 0 !important; overflow-y: auto !important; overflow-x: auto !important; width: 100% !important; height: 100% !important; -webkit-overflow-scrolling: touch;"
                         @contextmenu="if (pdfReadOnly) { $event.preventDefault(); return false; }"
                         @copy="if (pdfReadOnly) { $event.preventDefault(); return false; }">
                        
                        <!-- Canvas Viewport -->
                        <div class="pb-16 pt-2 flex justify-center items-start w-full min-h-full">
                            <canvas id="pdfViewerCanvas" 
                                    x-ref="pdfCanvas"
                                    style="background-color: #ffffff !important; border: 1px solid #334155 !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important; display: block !important; margin: 0 auto !important; border-radius: 2px !important; outline: none !important;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM STATUS BAR -->
                <footer style="background-color: #0f172a !important; border-top: 1px solid #1e293b !important; color: #94a3b8 !important; flex: 0 0 auto !important; z-index: 30 !important;" 
                        class="px-4 py-2 flex items-center justify-between text-[11px] shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full" :style="pdfReadOnly ? 'background-color: #34d399 !important;' : 'background-color: #94a3b8 !important;'"></span>
                        <span x-text="pdfReadOnly ? 'Mode Proteksi Aktif: Anti-copy, klik kanan dinonaktifkan, watermark resmi aktif.' : 'Mode Standar.'"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline">Gunakan <kbd style="background-color: #1e293b !important; color: #e2e8f0 !important;" class="px-1.5 py-0.5 rounded font-mono">←</kbd> / <kbd style="background-color: #1e293b !important; color: #e2e8f0 !important;" class="px-1.5 py-0.5 rounded font-mono">→</kbd> untuk ganti halaman, <kbd style="background-color: #1e293b !important; color: #e2e8f0 !important;" class="px-1.5 py-0.5 rounded font-mono">Esc</kbd> untuk tutup.</span>
                        <span class="font-mono text-slate-200 font-bold" x-text="'Hal ' + pdfCurrentPage + ' dari ' + (pdfTotalPages || 1)"></span>
                    </div>
                </footer>
            </div>
        </template>

        <!-- Modal Detail Abstrak Pustaka -->
        <template x-teleport="body">
            <div x-show="abstractModalOpen && !pdfReaderOpen" 
                 @keydown.escape.window="if (abstractModalOpen && !pdfReaderOpen) abstractModalOpen = false"
                 role="dialog"
                 aria-modal="true"
                 class="fixed inset-0 z-[60] overflow-y-auto text-left" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="abstractModalOpen = false">
                        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                    </div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-slate-200 dark:border-slate-700 relative z-10">
                        <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-lg" x-text="'Angkatan ' + abstractData.year"></span>
                                <span class="px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-lg border" :class="abstractData.badge?.bg" x-text="abstractData.badge?.label"></span>
                            </div>
                            <button type="button" @click="abstractModalOpen = false" aria-label="Tutup modal detail abstrak" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold p-1 cursor-pointer">&times;</button>
                        </div>
                        
                        <div class="p-8 space-y-5 max-h-[70vh] overflow-y-auto">
                            <!-- Judul -->
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-orange-600 dark:text-orange-400 mb-1">Judul Skripsi</p>
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 leading-snug uppercase" x-text="abstractData.title"></h3>
                            </div>

                            <!-- Meta Mahasiswa & Pembimbing -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Mahasiswa Alumni</p>
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 mt-0.5" x-text="abstractData.name"></p>
                                    <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400" x-text="'NPM: ' + (abstractData.identifier || '-')"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Dosen Pembimbing</p>
                                    <div class="space-y-1">
                                        <template x-if="abstractData.pembimbing1">
                                            <div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-200">
                                                <span class="px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-bold text-[9px] rounded border border-indigo-200/80 dark:border-indigo-800/80 shrink-0">P1</span>
                                                <span class="font-bold truncate" x-text="abstractData.pembimbing1"></span>
                                            </div>
                                        </template>
                                        <template x-if="abstractData.pembimbing2">
                                            <div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-200">
                                                <span class="px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 font-bold text-[9px] rounded border border-purple-200/80 dark:border-purple-800/80 shrink-0">P2</span>
                                                <span class="font-bold truncate" x-text="abstractData.pembimbing2"></span>
                                            </div>
                                        </template>
                                        <template x-if="!abstractData.pembimbing1 && !abstractData.pembimbing2">
                                            <span class="text-xs text-slate-400 italic">-</span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Abstrak -->
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-2">Abstrak / Rangkuman</p>
                                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line text-justify font-medium" 
                                     x-text="abstractData.abstract || 'Tidak ada teks abstrak yang tersedia untuk arsip ini.'"></div>
                            </div>
                        </div>

                        <div class="px-8 py-5 bg-slate-50/75 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5 sm:gap-4 flex-wrap">
                                <template x-if="abstractData.file_path">
                                    <div class="inline-flex items-center rounded-xl shadow-xs overflow-hidden bg-rose-600 hover:bg-rose-700 dark:bg-rose-700 dark:hover:bg-rose-600 border border-black/10 dark:border-white/15 transition-all">
                                        <button type="button" 
                                                @click="abstractModalOpen = false; openPdfReader(abstractData, 'bab1')" 
                                                class="inline-flex items-center gap-2.5 pl-4 pr-3.5 py-2.5 text-white text-xs font-bold hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer"
                                                title="Baca Naskah BAB 1 di Web (In-App Reader)">
                                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span>Baca BAB 1</span>
                                        </button>
                                        <span class="w-px h-4 bg-white/30 shrink-0" aria-hidden="true"></span>
                                        <a :href="'/repositories/' + abstractData.id + '/bab1'" 
                                           target="_blank" 
                                           class="inline-flex items-center justify-center pl-3 pr-4 py-2.5 text-white/90 hover:text-white hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer" 
                                           title="Buka File di Tab Baru">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </template>
                                <template x-if="abstractData.file_path_bab2">
                                    <div class="inline-flex items-center rounded-xl shadow-xs overflow-hidden bg-amber-600 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-600 border border-black/10 dark:border-white/15 transition-all">
                                        <button type="button" 
                                                @click="abstractModalOpen = false; openPdfReader(abstractData, 'bab2')" 
                                                class="inline-flex items-center gap-2.5 pl-4 pr-3.5 py-2.5 text-white text-xs font-bold hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer"
                                                title="Baca Naskah BAB 2 di Web (In-App Reader)">
                                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span>Baca BAB 2</span>
                                        </button>
                                        <span class="w-px h-4 bg-white/30 shrink-0" aria-hidden="true"></span>
                                        <a :href="'/repositories/' + abstractData.id + '/bab2'" 
                                           target="_blank" 
                                           class="inline-flex items-center justify-center pl-3 pr-4 py-2.5 text-white/90 hover:text-white hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer" 
                                           title="Buka File di Tab Baru">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </template>
                                <template x-if="abstractData.file_path_bab3">
                                    <div class="inline-flex items-center rounded-xl shadow-xs overflow-hidden bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 border border-black/10 dark:border-white/15 transition-all">
                                        <button type="button" 
                                                @click="abstractModalOpen = false; openPdfReader(abstractData, 'bab3')" 
                                                class="inline-flex items-center gap-2.5 pl-4 pr-3.5 py-2.5 text-white text-xs font-bold hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer"
                                                title="Baca Naskah BAB 3 di Web (In-App Reader)">
                                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span>Baca BAB 3</span>
                                        </button>
                                        <span class="w-px h-4 bg-white/30 shrink-0" aria-hidden="true"></span>
                                        <a :href="'/repositories/' + abstractData.id + '/bab3'" 
                                           target="_blank" 
                                           class="inline-flex items-center justify-center pl-3 pr-4 py-2.5 text-white/90 hover:text-white hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer" 
                                           title="Buka File di Tab Baru">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </template>
                                <template x-if="abstractData.file_path_bab4">
                                    <div class="inline-flex items-center rounded-xl shadow-xs overflow-hidden bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 border border-black/10 dark:border-white/15 transition-all">
                                        <button type="button" 
                                                @click="abstractModalOpen = false; openPdfReader(abstractData, 'bab4')" 
                                                class="inline-flex items-center gap-2.5 pl-4 pr-3.5 py-2.5 text-white text-xs font-bold hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer"
                                                title="Baca Naskah BAB 4 di Web (In-App Reader)">
                                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span>Baca BAB 4</span>
                                        </button>
                                        <span class="w-px h-4 bg-white/30 shrink-0" aria-hidden="true"></span>
                                        <a :href="'/repositories/' + abstractData.id + '/bab4'" 
                                           target="_blank" 
                                           class="inline-flex items-center justify-center pl-3 pr-4 py-2.5 text-white/90 hover:text-white hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer" 
                                           title="Buka File di Tab Baru">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </template>
                                <template x-if="abstractData.file_path_bab5">
                                    <div class="inline-flex items-center rounded-xl shadow-xs overflow-hidden bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 border border-black/10 dark:border-white/15 transition-all">
                                        <button type="button" 
                                                @click="abstractModalOpen = false; openPdfReader(abstractData, 'bab5')" 
                                                class="inline-flex items-center gap-2.5 pl-4 pr-3.5 py-2.5 text-white text-xs font-bold hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer"
                                                title="Baca Naskah BAB 5 di Web (In-App Reader)">
                                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span>Baca BAB 5</span>
                                        </button>
                                        <span class="w-px h-4 bg-white/30 shrink-0" aria-hidden="true"></span>
                                        <a :href="'/repositories/' + abstractData.id + '/bab5'" 
                                           target="_blank" 
                                           class="inline-flex items-center justify-center pl-3 pr-4 py-2.5 text-white/90 hover:text-white hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer" 
                                           title="Buka File di Tab Baru">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </template>
                                <template x-if="abstractData.file_path_bab6">
                                    <div class="inline-flex items-center rounded-xl shadow-xs overflow-hidden bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-700 dark:hover:bg-cyan-600 border border-black/10 dark:border-white/15 transition-all">
                                        <button type="button" 
                                                @click="abstractModalOpen = false; openPdfReader(abstractData, 'bab6')" 
                                                class="inline-flex items-center gap-2.5 pl-4 pr-3.5 py-2.5 text-white text-xs font-bold hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer"
                                                title="Baca Naskah BAB 6 di Web (In-App Reader)">
                                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span>Baca BAB 6</span>
                                        </button>
                                        <span class="w-px h-4 bg-white/30 shrink-0" aria-hidden="true"></span>
                                        <a :href="'/repositories/' + abstractData.id + '/bab6'" 
                                           target="_blank" 
                                           class="inline-flex items-center justify-center pl-3 pr-4 py-2.5 text-white/90 hover:text-white hover:bg-white/10 active:bg-white/20 transition-colors cursor-pointer" 
                                           title="Buka File di Tab Baru">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </template>
                                <template x-if="!abstractData.file_path && !abstractData.file_path_bab2 && !abstractData.file_path_bab3 && !abstractData.file_path_bab4 && !abstractData.file_path_bab5 && !abstractData.file_path_bab6">
                                    <span class="text-xs text-slate-400 italic">File naskah PDF belum terhubung</span>
                                </template>
                            </div>
                            <button type="button" @click="abstractModalOpen = false" class="px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-orange-600 dark:hover:bg-orange-500 dark:hover:text-white transition-all shadow-sm cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

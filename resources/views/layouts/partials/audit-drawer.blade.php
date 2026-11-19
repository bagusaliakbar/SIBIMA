        <!-- Global Slide-Over Drawer Kemiripan Judul -->
        <div x-data="{ 
            open: false, 
            data: { student: '', npm: '', title: '', score: 0, matches: [] } 
        }"
        @open-audit-modal.window="data = $event.detail; open = true"
        x-show="open" 
        class="fixed inset-0 overflow-hidden z-[55]"
        style="display: none;"
        x-cloak>
            <!-- Backdrop Overlay -->
            <div x-show="open"
                 x-transition:enter="ease-in-out duration-400"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in-out duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-[55]"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10 pointer-events-none z-[56]">
                <div x-show="open"
                     x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="w-screen max-w-lg bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200 dark:border-slate-800 flex flex-col h-full relative pointer-events-auto">
                    
                    <!-- Drawer Header -->
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">Kemiripan Judul</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase mt-0.5">
                                    Mahasiswa: <span class="text-slate-700 dark:text-slate-200" x-text="data.student"></span> (<span x-text="data.npm"></span>)
                                </p>
                            </div>
                        </div>
                        <button @click="open = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Drawer Body (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                        <!-- Judul Skripsi Diuji -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-none">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Judul Skripsi Yang Diuji:</span>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase leading-relaxed" x-text="data.title"></h4>
                        </div>

                        <!-- Match Summary Banner -->
                        <div class="flex items-center justify-between p-4 rounded-2xl border dark:border-none"
                             :class="data.score >= 66 ? 'bg-rose-50 border-rose-200/80 text-rose-900 dark:bg-rose-950/60 dark:text-rose-200' : (data.score >= 35 ? 'bg-amber-50 border-amber-200/80 text-amber-900 dark:bg-amber-950/60 dark:text-amber-200' : 'bg-emerald-50 border-emerald-200/80 text-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-200')">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-widest block opacity-75">Tingkat Kemiripan:</span>
                                <span class="text-xs font-bold uppercase tracking-tight block mt-0.5">
                                    <template x-if="data.score >= 66">
                                        <span class="text-rose-800 dark:text-rose-300">🔴 Terdeteksi Sangat Mirip</span>
                                    </template>
                                    <template x-if="data.score >= 35 && data.score < 66">
                                        <span class="text-amber-800 dark:text-amber-300">🟧 Terdeteksi Kemiripan Sedang</span>
                                    </template>
                                    <template x-if="data.score < 35">
                                        <span class="text-emerald-800 dark:text-emerald-300">🟢 Judul Unik & Orisinal</span>
                                    </template>
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black block tracking-tight" :class="data.score >= 66 ? 'text-rose-700 dark:text-rose-300' : (data.score >= 35 ? 'text-amber-700 dark:text-amber-300' : 'text-emerald-700 dark:text-emerald-300')" x-text="data.score + '%'"></span>
                                <span class="text-[9px] uppercase font-bold opacity-75">Kemiripan Max</span>
                            </div>
                        </div>

                        <!-- Daftar Judul Mirip -->
                        <div>
                            <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Rincian Judul Terkait:</h5>
                            <div class="space-y-3">
                                <template x-for="(match, index) in data.matches" :key="index">
                                    <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-none space-y-2.5 shadow-2xs dark:shadow-none">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="space-y-0.5">
                                                <span class="text-[9px] font-bold text-orange-600 dark:text-orange-400 uppercase tracking-wider block" x-text="match.source"></span>
                                                <h6 class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase leading-snug" x-text="match.title"></h6>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold shrink-0 border dark:border-none" 
                                                  :class="match.percentage >= 66 ? 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/20 dark:text-rose-300' : (match.percentage >= 35 ? 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-500/20 dark:text-amber-300' : 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-700 dark:text-slate-300')"
                                                  x-text="match.percentage + '%'">
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-700/50">
                                            <span>Oleh: <strong class="text-slate-700 dark:text-slate-300 uppercase" x-text="match.author"></strong> (<span x-text="match.year"></span>)</span>
                                            <template x-if="match.matched_words && match.matched_words.length">
                                                <span class="text-[9px] font-medium text-slate-400">Kata Cocok: <span class="text-slate-600 dark:text-slate-300 font-semibold" x-text="match.matched_words.slice(0,4).join(', ')"></span></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!data.matches || data.matches.length === 0">
                                    <div class="p-6 text-center bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-none space-y-1">
                                        <svg class="w-8 h-8 text-emerald-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-tight">Tidak Ada Kemiripan Signifikan</p>
                                        <p class="text-[10px] text-slate-400">Judul ini memiliki tingkat keunikan tinggi dibanding arsip skripsi lainnya.</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Drawer Footer -->
                    <div class="p-4 px-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex justify-end shrink-0">
                        <button type="button" @click="open = false" class="px-6 py-2.5 bg-slate-800 dark:bg-white text-white dark:text-slate-800 text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-slate-900 transition-all shadow-md">Tutup Panel</button>
                    </div>
                </div>
            </div>
        </div>

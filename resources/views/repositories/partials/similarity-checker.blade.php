        <!-- INSTANT SIMILARITY CHECKER DRAWER -->
        <div x-show="showChecker" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             x-cloak
             class="bg-gradient-to-br from-orange-500/5 via-white to-amber-500/5 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-3xl p-6 border-2 border-orange-200 dark:border-orange-500/30 shadow-xl space-y-4">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-orange-100 dark:border-slate-700/80 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-black shadow-md shadow-orange-500/30 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight">Cek Kemiripan Judul Skripsi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Ketik calon judul proposal Anda untuk mengecek kemiripan dengan repositori alumni & skripsi aktif secara real-time.</p>
                    </div>
                </div>
                <button type="button" @click="showChecker = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="runTitleCheck" class="space-y-2">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1 flex items-center">
                        <input type="text" 
                               x-model="testTitle" 
                               @input="errorMessage = ''; hasChecked = false;"
                               placeholder="Masukkan draf rencana judul skripsi (contoh: Sistem Informasi Pengelolaan Stok Barang...)" 
                               class="w-full pl-4 pr-12 py-3 bg-white dark:bg-slate-900 border border-orange-200 dark:border-slate-700 rounded-2xl text-xs font-semibold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all shadow-inner text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500">
                        <button type="button" 
                                x-show="testTitle" 
                                x-cloak
                                @click="testTitle = ''; errorMessage = ''; hasChecked = false; checkResults = [];" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer"
                                title="Hapus teks judul">
                            <div class="w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </button>
                    </div>
                    <button type="submit" 
                            :disabled="isChecking || !testTitle"
                            class="px-6 py-3 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-2xl font-bold text-xs shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2 shrink-0 disabled:opacity-50 cursor-pointer">
                        <template x-if="!isChecking">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <span>Analisis Orisinalitas</span>
                            </span>
                        </template>
                        <template x-if="isChecking">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Memeriksa Kemiripan...</span>
                            </span>
                        </template>
                    </button>
                </div>
                <template x-if="errorMessage">
                    <p class="text-xs font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1 pt-1" x-text="errorMessage"></p>
                </template>
            </form>

            <!-- Results Display -->
            <div x-show="hasChecked && !errorMessage" x-cloak class="space-y-3 pt-1">
                <template x-if="checkResults.length === 0">
                    <div class="p-4 bg-emerald-500/10 dark:bg-emerald-950/70 border border-emerald-500/30 dark:border-emerald-700/60 rounded-2xl flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black shrink-0 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-tight">Judul Aman / Orisinal!</h4>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400">Tidak ditemukan judul yang memiliki kemiripan signifikan (&ge;45%) di database pustaka alumni dan skripsi berjalan.</p>
                        </div>
                    </div>
                </template>

                <template x-if="checkResults.length > 0">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Ditemukan <span class="text-orange-600 dark:text-orange-400" x-text="checkResults.length"></span> Judul Serupa Terkait:
                            </p>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500">Batas toleransi kemiripan: &lt; 45%</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <template x-for="(item, idx) in checkResults" :key="idx">
                                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-lg"
                                              :class="item.percentage >= 70 ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300 border dark:border-rose-800/60' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 border dark:border-amber-800/60'"
                                              x-text="item.percentage + '% Mirip'"></span>
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500" x-text="item.source + ' (' + item.year + ')'"></span>
                                    </div>
                                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-snug" x-text="item.title"></h5>
                                    <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800">
                                        <span x-text="'Oleh: ' + item.student_name"></span>
                                        <span class="font-mono text-orange-600 dark:text-orange-400 font-bold" x-text="item.matched_words ? item.matched_words.length + ' kata cocok' : ''"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
            <!-- MODAL: KONFIRMASI ROLLBACK PENUGASAN PEMBIMBING -->
            <template x-teleport="body">
                <div x-show="rollbackModalOpen" 
                     @keydown.escape.window="if (rollbackModalOpen) rollbackModalOpen = false"
                     role="dialog"
                     aria-modal="true"
                     class="fixed inset-0 z-[60] overflow-y-auto" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity" 
                         @click="rollbackModalOpen = false" 
                         aria-hidden="true"></div>

                    <!-- Centered Modal Box -->
                    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 text-center">
                        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 relative z-50 my-auto transform transition-all"
                             @click.stop>
                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-rose-50/50 dark:bg-rose-950/30 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-rose-800 dark:text-rose-200 uppercase tracking-widest">Konfirmasi Rollback Pembimbing</h3>
                                    <p class="text-[10px] text-rose-600 dark:text-rose-400 font-bold uppercase mt-0.5">Kembalikan status skripsi ke Menunggu</p>
                                </div>
                            </div>

                            <form :action="'/theses/' + rollbackData.id + '/unassign'" method="POST" class="p-8 space-y-5">
                                @csrf
                                <div class="space-y-3">
                                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                                        Apakah Anda yakin ingin membatalkan penugasan dosen pembimbing untuk mahasiswa <strong class="text-slate-800 dark:text-white uppercase" x-text="rollbackData.student"></strong>?
                                    </p>
                                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-1.5 text-[11px]">
                                        <div class="flex justify-between">
                                            <span class="text-slate-400 uppercase font-black text-[9px]">Pembimbing 1 Saat Ini:</span>
                                            <span class="font-extrabold text-slate-700 dark:text-slate-200" x-text="rollbackData.p1"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-400 uppercase font-black text-[9px]">Pembimbing 2 Saat Ini:</span>
                                            <span class="font-extrabold text-slate-700 dark:text-slate-200" x-text="rollbackData.p2"></span>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-amber-600 dark:text-amber-400 font-bold italic">
                                        * Status skripsi akan dikembalikan ke 'pending', kuota pembimbing akan dilepaskan, dan seluruh riwayat ACC awal akan di-reset.
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                                    <button type="button" @click="rollbackModalOpen = false" class="px-5 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-md shadow-rose-500/20 cursor-pointer">
                                        Ya, Rollback Penugasan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        @endif

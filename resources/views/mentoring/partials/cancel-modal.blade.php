        <!-- 4. MODAL KONFIRMASI PEMBATALAN JADWAL BIMBINGAN -->
        <template x-teleport="body">
            <div x-data="mentoringCancelModal()" 
                 @open-cancel-modal.window="openModal($event.detail)" 
                 @keydown.escape.window="if (cancelModalOpen) cancelModalOpen = false"
                 x-show="cancelModalOpen" 
                 x-cloak 
                 class="fixed inset-0 z-[60] overflow-y-auto" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Backdrop -->
                    <div x-show="cancelModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                         @click="cancelModalOpen = false" 
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!-- Modal Content -->
                    <div x-show="cancelModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700 relative z-10">
                        
                        <form :action="'{{ url('mentoring-sessions') }}/' + cancelData.id" method="POST" class="p-6 sm:p-8 space-y-5">
                            @csrf
                            @method('DELETE')

                            <!-- Header -->
                            <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100 dark:border-slate-700/80">
                                <div class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">
                                        Batalkan Jadwal Bimbingan
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Jadwal ini akan dibatalkan dan mahasiswa terkait akan menerima notifikasi.
                                    </p>
                                </div>
                            </div>

                            <!-- Summary Card -->
                            <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2.5 text-xs">
                                <div class="flex justify-between items-start gap-3">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Mahasiswa:</span>
                                    <div class="text-right">
                                        <span class="font-bold text-slate-800 dark:text-slate-200" x-text="cancelData.student_name"></span>
                                        <span class="text-slate-500 dark:text-slate-400 text-[11px] ml-1" x-text="'(' + cancelData.student_npm + ')'"></span>
                                    </div>
                                </div>
                                <div class="h-px bg-slate-200/70 dark:bg-slate-700/70"></div>
                                <div class="flex justify-between items-start gap-3">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Waktu Sesi:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="cancelData.scheduled_date + ' • ' + cancelData.scheduled_time"></span>
                                </div>
                                <div class="h-px bg-slate-200/70 dark:bg-slate-700/70"></div>
                                <div class="flex justify-between items-start gap-3">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Topik:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right line-clamp-2" x-text="cancelData.topic"></span>
                                </div>
                            </div>

                            <!-- Group Option -->
                            <template x-if="cancelData.is_group">
                                <div class="p-3 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/60 rounded-xl space-y-2">
                                    <span class="text-[11px] font-black uppercase tracking-wider text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Sesi Bimbingan Bersama Terdeteksi</span>
                                    </span>
                                    <label class="flex items-start gap-2.5 cursor-pointer text-xs">
                                        <input type="checkbox" 
                                               name="apply_to_group" 
                                               value="1" 
                                               x-model="cancelData.apply_to_group"
                                               class="mt-0.5 w-4 h-4 text-indigo-600 rounded border-slate-300 dark:border-slate-700 focus:ring-indigo-500">
                                        <div>
                                            <span class="font-bold text-slate-800 dark:text-slate-200">
                                                Batalkan untuk seluruh (<span x-text="cancelData.group_count"></span>) mahasiswa dalam kelompok jam ini
                                            </span>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                Jika tidak dicentang, hanya jadwal untuk <strong x-text="cancelData.student_name"></strong> yang dibatalkan.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </template>

                            <!-- Input Alasan -->
                            <div>
                                <label for="cancel_reason" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Alasan Pembatalan (Opsional / Disampaikan ke Mahasiswa)
                                </label>
                                <textarea name="reason" 
                                          id="cancel_reason" 
                                          x-model="cancelData.reason"
                                          rows="3" 
                                          class="w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 p-3 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-1 focus:ring-rose-500 focus:border-rose-500 shadow-2xs transition-all" 
                                          placeholder="Contoh: Ada agenda rapat mendadak, silakan buat jadwal bimbingan kembali..."></textarea>
                            </div>

                            <!-- Info Notice -->
                            <div class="p-3 bg-rose-50/70 dark:bg-rose-950/20 border border-rose-200/80 dark:border-rose-800/40 rounded-xl text-[11px] text-rose-800 dark:text-rose-300 flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span>Tindakan ini akan menghapus jadwal dari agenda dan mengirim pemberitahuan ke mahasiswa.</span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button" 
                                        @click="cancelModalOpen = false" 
                                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                    Tutup
                                </button>
                                <button type="submit" 
                                        class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md shadow-rose-500/20 hover:scale-[1.02] active:scale-95 flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Ya, Batalkan Jadwal</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

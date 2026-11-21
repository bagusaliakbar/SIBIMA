        <!-- Floating Bulk Action Bar (Docked at Bottom, Teleported to Body) -->
        <template x-teleport="body">
            <div x-data="mentoringBulkActionBar()"
                 x-show="selectedIds && selectedIds.length > 0" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-12 opacity-0 scale-95"
                 x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-y-0 opacity-100 scale-100"
                 x-transition:leave-end="translate-y-12 opacity-0 scale-95"
                 class="fixed bottom-6 inset-x-0 z-[45] max-w-2xl mx-auto px-4 pointer-events-none">
                <div class="pointer-events-auto bg-slate-900/95 dark:bg-slate-800/95 text-white backdrop-blur-xl rounded-2xl border border-slate-700/80 shadow-2xl p-3 sm:px-5 sm:py-3.5 flex flex-wrap items-center justify-between gap-3 shadow-orange-950/20">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl bg-orange-600 text-white font-black text-xs shadow-xs tracking-wider" 
                              x-text="selectedIds.length + ' Mahasiswa'"></span>
                        <span class="text-xs font-bold text-slate-300 hidden sm:inline">Dipilih untuk aksi massal</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="clear()" 
                                class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-bold transition-all cursor-pointer border border-slate-700 shadow-2xs">
                            Batal
                        </button>
                        <button type="button" 
                                @click="openBulkAbsent()" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-rose-600/30 active:scale-95 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span>Absen Massal</span>
                        </button>
                        <button type="button" 
                                @click="openBulkComplete()" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-emerald-600/30 active:scale-95 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Selesai Massal</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Modal Absen Massal (Tandai Tidak Hadir) -->
        <template x-teleport="body">
            <div x-data="mentoringBulkAbsentModal()"
                 @open-bulk-absent-modal.window="openModal($event.detail.ids)"
                 @keydown.escape.window="if (open) closeModal()"
                 x-show="open" 
                 x-cloak
                 role="dialog"
                 aria-modal="true"
                 class="fixed inset-0 z-[60] overflow-y-auto"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal()"></div>

                <div class="relative min-h-screen flex items-center justify-center p-4">
                    <div class="relative bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200/80 dark:border-slate-700/80 overflow-hidden"
                         @click.away="closeModal()"
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="scale-95 opacity-0"
                         x-transition:enter-end="scale-100 opacity-100">
                        
                        <div class="flex items-center gap-3.5 mb-4">
                            <div class="w-11 h-11 rounded-2xl bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 shadow-sm border border-rose-200 dark:border-rose-800/80">
                                <svg class="w-6 h-6 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tandai Tidak Hadir (Absen Massal)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Konfirmasi status kehadiran mahasiswa</p>
                            </div>
                        </div>

                        <form action="{{ route('mentoring-sessions.bulk-status') }}" method="POST" class="space-y-4">
                            @csrf
                            <template x-for="id in sessionIds" :key="id">
                                <input type="hidden" name="session_ids[]" :value="id">
                            </template>
                            <input type="hidden" name="status" value="absent">

                            <div class="p-4 bg-rose-50/70 dark:bg-rose-950/20 rounded-2xl border border-rose-200/80 dark:border-rose-800/40 text-xs text-rose-900 dark:text-rose-300 space-y-1.5">
                                <p class="font-bold">
                                    Anda akan menandai <span class="font-black text-rose-600 dark:text-rose-400" x-text="sessionIds.length"></span> mahasiswa terpilih sebagai <span class="font-black underline">TIDAK HADIR (Absen)</span>.
                                </p>
                                <p class="text-[11px] text-rose-700/80 dark:text-rose-300/80">
                                    Sesi bimbingan akan diselesaikan dengan status tidak hadir, dan notifikasi akan dikirimkan kepada masing-masing mahasiswa.
                                </p>
                            </div>

                            <div>
                                <label for="absent_feedback" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Catatan / Alasan Tidak Hadir (Opsional)
                                </label>
                                <textarea name="feedback" 
                                          id="absent_feedback" 
                                          rows="2" 
                                          class="w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 p-3 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 shadow-2xs transition-all" 
                                          placeholder="Contoh: Mahasiswa tidak hadir tanpa keterangan pada jam yang telah ditentukan..."></textarea>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700">
                                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                    <input type="checkbox" name="notify_student_wa" value="1" 
                                           class="mt-0.5 rounded border-slate-300 dark:border-slate-600 text-rose-600 focus:ring-rose-500">
                                    <div>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                                            Kirim Notifikasi WhatsApp ke Mahasiswa
                                        </span>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                            Default tidak dicentang untuk aksi massal guna melindungi nomor WhatsApp kampus dari risiko pemblokiran spam.
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-200 dark:border-slate-700">
                                <button type="button" 
                                        @click="closeModal()" 
                                        class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md shadow-rose-600/20 active:scale-95 cursor-pointer">
                                    Ya, Tandai Tidak Hadir
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- Modal Selesai Massal (Hadir & Simpan Catatan) -->
        <template x-teleport="body">
            <div x-data="mentoringBulkCompleteModal()"
                 @open-bulk-complete-modal.window="openModal($event.detail.ids)"
                 @keydown.escape.window="if (open) closeModal()"
                 x-show="open" 
                 x-cloak
                 role="dialog"
                 aria-modal="true"
                 class="fixed inset-0 z-[60] overflow-y-auto"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal()"></div>

                <div class="relative min-h-screen flex items-center justify-center p-4">
                    <div class="relative bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200/80 dark:border-slate-700/80 overflow-hidden"
                         @click.away="closeModal()"
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="scale-95 opacity-0"
                         x-transition:enter-end="scale-100 opacity-100">
                        
                        <div class="flex items-center gap-3.5 mb-4">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm border border-emerald-200 dark:border-emerald-800/80">
                                <svg class="w-6 h-6 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Selesaikan Bimbingan Massal</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tandai hadir dan masukkan catatan hasil bimbingan</p>
                            </div>
                        </div>

                        <form action="{{ route('mentoring-sessions.bulk-status') }}" method="POST" class="space-y-4">
                            @csrf
                            <template x-for="id in sessionIds" :key="id">
                                <input type="hidden" name="session_ids[]" :value="id">
                            </template>
                            <input type="hidden" name="status" value="completed">

                            <div class="p-3.5 bg-emerald-50/70 dark:bg-emerald-950/20 rounded-2xl border border-emerald-200/80 dark:border-emerald-800/40 text-xs text-emerald-900 dark:text-emerald-300">
                                <p class="font-bold">
                                    Menyelesaikan bimbingan untuk <span class="font-black text-emerald-600 dark:text-emerald-400" x-text="sessionIds.length"></span> mahasiswa terpilih sekaligus.
                                </p>
                            </div>

                            <div>
                                <label for="complete_feedback" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Catatan Hasil Bimbingan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="feedback" 
                                          id="complete_feedback" 
                                          rows="3" 
                                          required
                                          class="w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 p-3 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all" 
                                          placeholder="Masukkan catatan hasil bimbingan (catatan ini akan dikirimkan ke seluruh mahasiswa terpilih)..."></textarea>
                            </div>

                            <div>
                                <label for="complete_document_url" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Link Dokumen / Revisi Google Drive (Opsional)
                                </label>
                                <div class="relative">
                                    <input type="url" 
                                           name="feedback_document_url" 
                                           id="complete_document_url"
                                           placeholder="https://drive.google.com/..." 
                                           class="w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2 pl-9 pr-3 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 dark:text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700">
                                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                    <input type="checkbox" name="notify_student_wa" value="1" 
                                           class="mt-0.5 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                                            Kirim Notifikasi WhatsApp ke Mahasiswa
                                        </span>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                            Default tidak dicentang untuk aksi massal guna melindungi nomor WhatsApp kampus dari risiko pemblokiran spam.
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-200 dark:border-slate-700">
                                <button type="button" 
                                        @click="closeModal()" 
                                        class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md shadow-emerald-600/20 active:scale-95 cursor-pointer">
                                    Simpan & Selesaikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

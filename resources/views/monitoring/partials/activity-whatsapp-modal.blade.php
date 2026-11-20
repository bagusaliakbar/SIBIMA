        <!-- ========================================== -->
        <!-- WHATSAPP REMINDER INTERACTIVE MODAL        -->
        <!-- ========================================== -->
        <div x-show="modalOpen" 
             @keydown.escape.window="if (modalOpen) closeReminderModal()"
             style="display: none;"
             class="fixed inset-0 z-[60] overflow-y-auto"
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true"
             x-cloak>
            
            <!-- Backdrop -->
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="closeReminderModal()"
                 class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

            <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
                <div x-show="modalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all w-full max-w-lg border border-slate-200 dark:border-slate-700">

                    <form action="{{ route('monitoring.activity.remind') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" :value="modalTargetType">
                        <input type="hidden" name="user_id" :value="modalUserId">
                        <input type="hidden" name="thesis_id" :value="modalThesisId">
                        <input type="hidden" name="days_inactive" :value="modalDaysInactive">

                        <!-- Header -->
                        <div class="px-6 py-5 bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Kirim Pengingat WhatsApp</h3>
                                    <p class="text-xs text-slate-500">Pemberitahuan resmi prodi ke penerima</p>
                                </div>
                            </div>
                            <button type="button" @click="closeReminderModal()" aria-label="Tutup modal pengingat WhatsApp" class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 space-y-4 text-xs">
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 space-y-1">
                                <p class="text-slate-500">Penerima:</p>
                                <p class="font-bold text-slate-900 dark:text-white text-sm" x-text="modalTargetName"></p>
                                <p class="text-slate-500 font-mono" x-text="'No. WA: ' + (modalTargetPhone || 'Belum diatur')"></p>
                            </div>

                            <div>
                                <label for="message" class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pesan Pengingat (Dapat Diedit)</label>
                                <textarea id="message" 
                                          name="message" 
                                          rows="6" 
                                          x-model="modalMessage"
                                          class="w-full p-3 rounded-xl border text-xs bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 font-sans leading-relaxed"></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
                            <template x-if="modalTargetPhone">
                                <a :href="'https://wa.me/' + modalTargetPhone.replace(/[^0-9]/g, '') + '?text=' + encodeURIComponent(modalMessage)" 
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                    <span>Buka di Web WhatsApp</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </template>

                            <div class="flex items-center gap-2 ml-auto">
                                <button type="button" @click="closeReminderModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 cursor-pointer">Batal</button>
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                    <span>Kirim Otomatis</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>


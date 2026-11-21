            <!-- Modal Konfirmasi Hadir & Pilih Jam Kedatangan -->
            <template x-teleport="body">
                <div x-show="attendModalOpen"
                     @keydown.escape.window="if (attendModalOpen) attendModalOpen = false"
                     role="dialog"
                     aria-modal="true"
                     aria-labelledby="attend-modal-title"
                     class="fixed inset-0 z-[60] overflow-y-auto text-left"
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="attendModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative z-10">
                            <form :action="'/mentoring-sessions/' + attendData.id + '/confirm-attendance'" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="attending">

                                <!-- Header -->
                                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <h3 id="attend-modal-title" class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Pilih Jam Kedatangan</h3>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Tentukan estimasi waktu hadir sesuai rentang dosen</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="attendModalOpen = false" aria-label="Tutup modal konfirmasi hadir" class="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <!-- Body -->
                                <div class="p-6 space-y-4">
                                    <!-- Session Summary -->
                                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1.5 text-xs">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rentang Jadwal Dosen</span>
                                            <span class="px-2 py-0.5 rounded-md bg-orange-50 dark:bg-orange-950/50 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60 font-mono font-bold text-[11px]" x-text="attendData.time_range"></span>
                                        </div>
                                        <p class="font-bold text-slate-800 dark:text-slate-100" x-text="attendData.topic"></p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400" x-text="attendData.dosen_name + ' • ' + attendData.scheduled_date"></p>
                                    </div>

                                    <!-- 30-Minute Quick Slot Chips -->
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                                Pilihan Slot (Per 30 Menit)
                                            </label>
                                            <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400" x-show="attendData.arrival_time" x-text="'Terpilih: ' + attendData.arrival_time + ' WIB'"></span>
                                        </div>
                                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                            <template x-for="slot in attendSlots" :key="slot">
                                                <button type="button"
                                                        @click="attendData.arrival_time = slot"
                                                        :class="attendData.arrival_time === slot
                                                            ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm ring-2 ring-emerald-500/25'
                                                            : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300'"
                                                        class="py-2 px-2 rounded-xl border text-xs font-bold transition-all flex flex-col items-center justify-center gap-0.5 cursor-pointer">
                                                    <span class="font-mono" x-text="slot"></span>
                                                    <template x-if="(attendData.peer_counts[slot] || 0) > 0">
                                                        <span class="text-[11px] font-semibold leading-none"
                                                              :class="attendData.arrival_time === slot ? 'text-emerald-100' : 'text-slate-500 dark:text-slate-400'"
                                                              x-text="attendData.peer_counts[slot] + ' mhs'"></span>
                                                    </template>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Manual Time Input -->
                                    <div class="p-3.5 bg-slate-50/80 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                        <div>
                                            <label for="student_arrival_time_input" class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                                                Atau Input Jam Manual <span class="text-orange-600">*</span>
                                            </label>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                                Rentang diperbolehkan: <span class="font-semibold" x-text="attendData.start_time + ' – ' + attendData.end_time + ' WIB'"></span>
                                            </p>
                                        </div>
                                        <input type="time"
                                               id="student_arrival_time_input"
                                               name="arrival_time"
                                               x-model="attendData.arrival_time"
                                               :min="attendData.start_time"
                                               :max="attendData.end_time"
                                               @change="validateManualArrivalTime()"
                                               required
                                               class="w-full sm:w-36 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2 px-3 text-slate-900 dark:text-slate-100 shadow-2xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm font-bold font-mono">
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                                    <button type="button" @click="attendModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Konfirmasi Akan Hadir</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Modal Izin Bimbingan -->
            <template x-teleport="body">
                <div x-show="permissionModalOpen" 
                     @keydown.escape.window="if (permissionModalOpen) permissionModalOpen = false"
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
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="permissionModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative z-10">
                            <form :action="'/mentoring-sessions/' + activeSessionId + '/confirm-attendance'" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="permission">
                                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Formulir Izin Bimbingan</h3>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Konfirmasi bahwa Anda berhalangan hadir</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="permissionModalOpen = false" class="text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold p-1 cursor-pointer">&times;</button>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700">
                                        <label class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">Topik Sesi Bimbingan</label>
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="activeSessionTopic"></p>
                                    </div>
                                    <div>
                                        <label for="permission_reason" class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1.5">
                                            Alasan / Keterangan Berhalangan Hadir <span class="text-rose-500">*</span>
                                        </label>
                                        <textarea name="reason" 
                                                  id="permission_reason" 
                                                  rows="4" 
                                                  required 
                                                  x-model="permissionReason"
                                                  placeholder="Jelaskan alasan izin Anda (misal: Sakit, Jadwal praktikum/ujian bentrok, Sedang dinas luar, dll.)..." 
                                                  class="w-full text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-2xl p-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Alasan ini akan dikirimkan langsung ke Dosen Pembimbing Anda.</p>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                                    <button type="button" @click="permissionModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-sm cursor-pointer">
                                        Kirim Izin
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Modal Pembatalan Jadwal Bimbingan (Mahasiswa) -->
            <template x-teleport="body">
                <div x-show="cancelModalOpen" 
                     @keydown.escape.window="if (cancelModalOpen) cancelModalOpen = false"
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
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="cancelModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative z-10">
                            <form :action="'{{ url('mentoring-sessions') }}/' + cancelData.id" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Batalkan Jadwal Bimbingan</h3>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Batalkan pengajuan / jadwal bimbingan skripsi</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="cancelModalOpen = false" class="text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold p-1 cursor-pointer">&times;</button>
                                </div>
                                <div class="p-6 space-y-4 text-xs">
                                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="text-slate-500 dark:text-slate-400 font-medium">Dosen Pembimbing:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right" x-text="cancelData.dosen_name"></span>
                                        </div>
                                        <div class="h-px bg-slate-200 dark:bg-slate-700"></div>
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="text-slate-500 dark:text-slate-400 font-medium">Jadwal Sesi:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right" x-text="cancelData.scheduled_date + ' • ' + cancelData.scheduled_time"></span>
                                        </div>
                                        <div class="h-px bg-slate-200 dark:bg-slate-700"></div>
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="text-slate-500 dark:text-slate-400 font-medium">Topik:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right line-clamp-2" x-text="cancelData.topic"></span>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="student_cancel_reason" class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1.5">
                                            Alasan Pembatalan (Opsional / Disampaikan ke Dosen)
                                        </label>
                                        <textarea name="reason" 
                                                  id="student_cancel_reason" 
                                                  rows="3" 
                                                  x-model="cancelData.reason"
                                                  placeholder="Jelaskan alasan pembatalan (misal: Ada kendala dokumen, perlu penyesuaian materi skripsi, dll.)..." 
                                                  class="w-full text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-2xl p-3 focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
                                    </div>

                                    <div class="p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 rounded-xl text-[11px] text-rose-700 dark:text-rose-300 flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span>Jadwal ini akan dibatalkan dan Dosen Pembimbing akan diberitahu.</span>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                                    <button type="button" @click="cancelModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                        Tutup
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Ya, Batalkan</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

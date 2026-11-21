        <!-- Quick Preview User Detail Modal -->
        <div x-show="openDetailModal" @keydown.escape.window="if (openDetailModal) openDetailModal = false" role="dialog" aria-modal="true" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openDetailModal = false">
                    <div class="absolute inset-0 bg-slate-900/75 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-slate-200 dark:border-slate-700"
                     x-show="openDetailModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                     
                     <template x-if="selectedUser">
                        <div>
                            <!-- Modal Header Profile Banner -->
                            <div class="px-6 py-5 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-4">
                                <!-- Left: Avatar + Details -->
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-14 h-14 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-700 shrink-0 flex items-center justify-center">
                                        <img :src="selectedUser.avatar_url" :alt="selectedUser.name" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight truncate" x-text="selectedUser.name"></h3>
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider shrink-0"
                                                  :class="{
                                                    'bg-blue-100 dark:bg-blue-900/70 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700': selectedUser.role === 'dosen',
                                                    'bg-amber-100 dark:bg-amber-900/70 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700': selectedUser.role === 'kaprodi',
                                                    'bg-indigo-100 dark:bg-indigo-900/70 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700': selectedUser.role === 'mahasiswa'
                                                  }"
                                                  x-text="selectedUser.role"></span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5 truncate" x-text="selectedUser.email"></p>
                                    </div>
                                </div>
                                
                                <!-- Right: WhatsApp Button + Close Button -->
                                <div class="flex items-center gap-2 shrink-0">
                                    <template x-if="selectedUser.clean_phone">
                                        <a :href="'https://wa.me/' + selectedUser.clean_phone + '?text=' + selectedUser.wa_message" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-sm shadow-emerald-600/20"
                                           title="Kirim pesan WhatsApp">
                                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            <span>Chat WA</span>
                                        </a>
                                    </template>
                                    <button type="button" @click="openDetailModal = false" aria-label="Tutup detail pengguna" class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-colors cursor-pointer" title="Tutup">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Body Details -->
                            <div class="p-6 space-y-5 max-h-[72vh] overflow-y-auto">
                                <!-- Clean Unified User Overview Card -->
                                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4.5 border border-slate-200 dark:border-slate-700/80">
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-4 gap-x-6">
                                        <!-- 1. NPM / NIDN -->
                                        <div>
                                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">NPM / NIDN</span>
                                            <span class="text-xs font-mono font-black text-slate-800 dark:text-slate-100 mt-1 block" x-text="selectedUser.identifier || '-'"></span>
                                        </div>

                                        <!-- 2. WhatsApp -->
                                        <div>
                                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Kontak WhatsApp</span>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100" x-text="selectedUser.phone_number || '-'"></span>
                                                <template x-if="selectedUser.has_wa">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">
                                                        Terhubung
                                                    </span>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- 3. Status Akun -->
                                        <div>
                                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Status Akun</span>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="w-2 h-2 rounded-full" :class="selectedUser.is_active ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'"></span>
                                                <span class="text-xs font-bold" :class="selectedUser.is_active ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300'" x-text="selectedUser.is_active ? 'Aktif' : 'Menunggu Aktivasi'"></span>
                                            </div>
                                        </div>

                                        <!-- 4. Terakhir Login -->
                                        <div>
                                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Terakhir Login</span>
                                            <div class="mt-1">
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100" x-text="selectedUser.last_login_at_human"></span>
                                                <template x-if="selectedUser.last_login_at_full">
                                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 block font-mono mt-0.5" x-text="selectedUser.last_login_at_full"></span>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- 5. Verifikasi Email -->
                                        <div>
                                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Verifikasi Email</span>
                                            <div class="mt-1 flex items-center gap-1">
                                                <template x-if="selectedUser.has_email_verified">
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Terverifikasi</span>
                                                    </span>
                                                </template>
                                                <template x-if="!selectedUser.has_email_verified">
                                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 italic">Belum Verifikasi</span>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- 6. Terdaftar Sejak -->
                                        <div>
                                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Terdaftar Pada</span>
                                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1 block" x-text="selectedUser.registered_at"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- SECTION: Mahasiswa Specific Academic Info -->
                                <template x-if="selectedUser.role === 'mahasiswa'">
                                    <div class="space-y-3.5">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                                <span>Informasi Skripsi & Akademik</span>
                                            </h4>
                                            <template x-if="selectedUser.entry_year">
                                                <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-bold border bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700">
                                                    <span x-text="'Angkatan ' + selectedUser.entry_year"></span>
                                                </span>
                                            </template>
                                        </div>

                                        <template x-if="selectedUser.thesis">
                                            <div class="p-4.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 space-y-3.5">
                                                <!-- Thesis Title -->
                                                <div>
                                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Judul Skripsi Aktif:</span>
                                                    <h5 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-tight mt-1 leading-relaxed" x-text="selectedUser.thesis.title"></h5>
                                                </div>

                                                <!-- Stage & Topic -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-200 dark:border-slate-700/60">
                                                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/70">
                                                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Tahapan Saat Ini</p>
                                                        <p class="text-xs font-black text-indigo-600 dark:text-indigo-400" x-text="selectedUser.thesis.stage"></p>
                                                    </div>
                                                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/70">
                                                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Topik / Minat</p>
                                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200" x-text="selectedUser.thesis.topic"></p>
                                                    </div>
                                                </div>

                                                <!-- Supervisors -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/70">
                                                        <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Pembimbing 1 (Utama)</span>
                                                        <p class="text-xs font-bold text-slate-900 dark:text-white mt-1" x-text="selectedUser.thesis.pembimbing1"></p>
                                                    </div>
                                                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/70">
                                                        <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Pembimbing 2 (Pendamping)</span>
                                                        <p class="text-xs font-bold text-slate-900 dark:text-white mt-1" x-text="selectedUser.thesis.pembimbing2"></p>
                                                    </div>
                                                </div>

                                                <!-- Open Thesis Action -->
                                                <div class="pt-1 flex justify-end">
                                                    <a :href="selectedUser.thesis.url" target="_blank"
                                                       class="inline-flex items-center gap-2 px-4.5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs">
                                                        <span>Buka Halaman Skripsi</span>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="!selectedUser.thesis">
                                            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-dashed border-slate-300 dark:border-slate-700 text-center">
                                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Mahasiswa ini belum mengajukan judul skripsi.</p>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- SECTION: Dosen Specific Academic Info -->
                                <template x-if="selectedUser.role === 'dosen' || selectedUser.role === 'kaprodi'">
                                    <div class="space-y-3.5">
                                        <h4 class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                            <span>Statistik & Beban Bimbingan Skripsi</span>
                                        </h4>

                                        <template x-if="selectedUser.dosen">
                                            <div class="p-4.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 space-y-3.5">
                                                <!-- Dosen KPI Stat Mini Grid -->
                                                <div class="grid grid-cols-3 gap-3">
                                                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/70 text-center">
                                                        <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider block">Pembimbing 1</span>
                                                        <p class="text-lg font-black text-blue-700 dark:text-blue-300 mt-0.5" x-text="selectedUser.dosen.p1_count + ' Mhs'"></p>
                                                    </div>
                                                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/70 text-center">
                                                        <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">Pembimbing 2</span>
                                                        <p class="text-lg font-black text-indigo-700 dark:text-indigo-300 mt-0.5" x-text="selectedUser.dosen.p2_count + ' Mhs'"></p>
                                                    </div>
                                                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/70 text-center">
                                                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Total Bimbingan</span>
                                                        <p class="text-lg font-black text-slate-900 dark:text-white mt-0.5" x-text="selectedUser.dosen.total_active + ' / ' + selectedUser.dosen.max_quota"></p>
                                                    </div>
                                                </div>

                                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/70">
                                                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bidang Minat / Riset</p>
                                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="selectedUser.dosen.research_interests"></p>
                                                </div>

                                                <!-- List of Supervised Students -->
                                                <div>
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Mahasiswa Bimbingan Aktif</span>
                                                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400" x-text="selectedUser.dosen.students.length + ' Mahasiswa'"></span>
                                                    </div>

                                                    <template x-if="selectedUser.dosen.students.length > 0">
                                                        <div class="divide-y divide-slate-200 dark:divide-slate-700/70 border border-slate-200 dark:border-slate-700/70 rounded-xl overflow-hidden max-h-48 overflow-y-auto bg-white dark:bg-slate-900">
                                                            <template x-for="(st, idx) in selectedUser.dosen.students" :key="idx">
                                                                <div class="p-3 flex items-start justify-between gap-3 text-xs hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                                                    <div class="min-w-0 flex-1">
                                                                        <div class="flex items-center gap-2">
                                                                            <span class="font-bold text-slate-900 dark:text-white uppercase" x-text="st.student_name"></span>
                                                                            <span class="font-mono text-[11px] text-slate-500 dark:text-slate-400" x-text="'(' + st.student_npm + ')'"></span>
                                                                        </div>
                                                                        <p class="text-[11px] text-slate-600 dark:text-slate-300 truncate mt-0.5" x-text="st.title"></p>
                                                                    </div>
                                                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold uppercase shrink-0 border"
                                                                          :class="st.role === 'Pembimbing 1' ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800' : 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800'"
                                                                          x-text="st.role"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <template x-if="selectedUser.dosen.students.length === 0">
                                                        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-dashed border-slate-300 dark:border-slate-700 text-center py-4">
                                                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Belum ada mahasiswa bimbingan skripsi yang aktif saat ini.</p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Modal Footer Actions -->
                            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3">
                                <form :action="selectedUser.toggle_url" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border shadow-xs cursor-pointer"
                                            :class="selectedUser.is_active ? 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800 hover:bg-rose-100 dark:hover:bg-rose-900' : 'bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600'">
                                        <span x-text="selectedUser.is_active ? 'Nonaktifkan Akun' : 'Setujui & Aktifkan Akun'"></span>
                                    </button>
                                </form>

                                <div class="flex items-center gap-2.5">
                                    <a :href="selectedUser.edit_url" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold transition-all border border-slate-300 dark:border-slate-600 shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span>Edit Data</span>
                                    </a>
                                    <button type="button" @click="openDetailModal = false" class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-600 transition-all shadow-xs">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                     </template>
                </div>
            </div>
        </div>

        <!-- Import Modal -->
        <div x-show="openImportModal" @keydown.escape.window="if (openImportModal) openImportModal = false" role="dialog" aria-modal="true" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openImportModal = false">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-100 dark:border-slate-700">
                    <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">
                            Import Data Pengguna
                        </h3>
                        <p class="text-[11px] text-slate-500 uppercase font-black mt-1">Silakan gunakan template Excel yang sesuai</p>
                        
                        <div class="mt-6 p-4 bg-orange-50 dark:bg-orange-500/5 rounded-xl border border-orange-100 dark:border-orange-500/10">
                            <h4 class="text-[11px] font-black text-orange-700 dark:text-orange-400 uppercase tracking-widest mb-2">Format Kolom:</h4>
                            <p class="font-mono text-[11px] text-orange-600 dark:text-orange-500 bg-white/50 dark:bg-slate-900/50 p-2 rounded-lg border border-orange-100 dark:border-orange-500/10">Nama, Email, Peran, NPM/NIDN, Tahun Angkatan, No_WhatsApp, Status Aktif (1/0), Tanggal Lahir (YYYY-MM-DD)</p>
                            <div class="mt-2 space-y-1 text-[11px] text-orange-600/70 dark:text-orange-400/70 font-bold uppercase">
                                <p>* Peran: dosen, mahasiswa, atau kaprodi</p>
                                <p>* Tahun Angkatan: Khusus mahasiswa (cth: 2020)</p>
                                <p>* Status Aktif: 1 untuk Aktif, 0 untuk Pending</p>
                                <p>* Tanggal Lahir: Opsional (cth: 2002-05-15)</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-8 py-8">
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Pilih File Excel (.xlsx, .xls)</label>
                            <div class="relative group">
                                <input type="file" name="excel_file" required class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-[11px] file:font-black file:uppercase file:tracking-widest file:bg-orange-600 file:text-white hover:file:bg-orange-700 transition-all cursor-pointer bg-slate-50 dark:bg-slate-900 rounded-xl p-2 border border-dashed border-slate-200 dark:border-slate-700">
                            </div>
                        </div>
                        <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                            <button type="button" @click="openImportModal = false" class="px-6 py-2.5 text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest hover:text-slate-800 dark:hover:text-slate-200 transition-colors">Batal</button>
                            <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white text-[11px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-500/20 transition-all">Mulai Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bulk Delete Confirmation Modal -->
        <div x-show="openBulkDeleteModal" 
             x-cloak
             @keydown.escape.window="if (openBulkDeleteModal) openBulkDeleteModal = false"
             role="dialog"
             aria-modal="true"
             class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-700 shadow-2xl relative"
                 @click.away="openBulkDeleteModal = false">
                
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-800 dark:text-slate-100">Konfirmasi Hapus Massal</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>

                <div class="p-4 bg-rose-50/70 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800/40 rounded-xl mb-5 text-xs text-rose-800 dark:text-rose-300">
                    <p class="font-bold">
                        Anda akan menghapus <span class="font-black text-rose-600 dark:text-rose-400" x-text="selectedUserIds.length"></span> pengguna terpilih secara permanen.
                    </p>
                    <p class="mt-1 text-[11px] text-rose-700/80 dark:text-rose-400/80">
                        Data mahasiswa beserta draf skripsi terkait (jika ada) akan dihapus dari sistem. Akun administrator dilindungi dan tidak akan terhapus.
                    </p>
                </div>

                <form action="{{ route('users.bulk-delete') }}" method="POST">
                    @csrf
                    <template x-for="id in selectedUserIds" :key="id">
                        <input type="hidden" name="user_ids[]" :value="id">
                    </template>

                    <div class="flex items-center justify-end gap-2.5">
                        <button type="button" 
                                @click="openBulkDeleteModal = false" 
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black tracking-wide transition-all shadow-md shadow-rose-600/30 cursor-pointer">
                            <span>Ya, Hapus Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

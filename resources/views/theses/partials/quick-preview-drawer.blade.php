        @if(Auth::user()->role === 'dosen')
            <!-- Quick Preview Slide-over Drawer (Alpine.js) -->
            <div x-data="quickPreviewDrawer()" 
                 @open-logbook-preview.window="fetchPreview($event.detail.thesisId)"
                 @keydown.escape.window="if(open) closeDrawer()"
                 class="relative"
                 x-cloak>
                <template x-teleport="body">
                    <div x-show="open" class="relative z-[60]">
                        <!-- Backdrop -->
                        <div x-show="open"
                             x-transition:enter="ease-in-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in-out duration-300"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click="closeDrawer()"
                             class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

                        <!-- Slide-over Container -->
                        <div class="fixed inset-y-0 right-0 max-w-full flex pl-6 sm:pl-10">
                            <div x-show="open"
                                 x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                                 x-transition:enter-start="translate-x-full"
                                 x-transition:enter-end="translate-x-0"
                                 x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                                 x-transition:leave-start="translate-x-0"
                                 x-transition:leave-end="translate-x-full"
                                 class="w-screen max-w-md sm:max-w-lg bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200 dark:border-slate-800 flex flex-col h-full relative">

                                <!-- Drawer Header -->
                                <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 flex items-start justify-between shrink-0">
                                    <div class="flex items-center gap-3 min-w-0 pr-3">
                                        <div class="w-10 h-10 rounded-2xl bg-orange-50 dark:orange-950/50 border border-orange-200 dark:border-orange-800 flex items-center justify-center shrink-0 shadow-2xs">
                                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 tracking-tight">Intip Logbook</h3>
                                                <span x-show="data && data.total_completed !== undefined" 
                                                      class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800"
                                                      x-text="(data ? data.total_completed : 0) + ' Sesi Selesai'"></span>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">3–5 sesi bimbingan terakhir beserta arahan revisi</p>
                                        </div>
                                    </div>
                                    <button type="button"
                                            @click="closeDrawer()" 
                                            class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer"
                                            title="Tutup (Esc)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Student Profile Banner (When Loaded) -->
                                <template x-if="data">
                                    <div class="px-5 py-3.5 bg-slate-100/70 dark:bg-slate-800/60 border-b border-slate-200/70 dark:border-slate-700/60 flex flex-col gap-2 shrink-0">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <template x-if="data.student_avatar">
                                                    <img :src="data.student_avatar" :alt="data.student_name" class="w-9 h-9 rounded-full object-cover border border-slate-300 dark:border-slate-600 shrink-0 shadow-2xs">
                                                </template>
                                                <template x-if="!data.student_avatar">
                                                    <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs flex items-center justify-center shrink-0" x-text="data.student_name ? data.student_name.charAt(0).toUpperCase() : 'M'"></div>
                                                </template>
                                                <div class="min-w-0">
                                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate" x-text="data.student_name"></h4>
                                                    <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400" x-text="'NPM: ' + data.student_identifier"></p>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full"
                                                      :class="data.thesis_status === 'completed' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300'"
                                                      x-text="data.thesis_status === 'completed' ? 'Lulus' : 'Bimbingan Aktif'"></span>
                                            </div>
                                        </div>
                                        <!-- Supervisor Breakdown Banner -->
                                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between gap-2 text-[11px] flex-wrap">
                                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">P1</span>
                                                <span class="font-medium truncate max-w-[140px]" x-text="data.pembimbing1_name"></span>
                                                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 font-mono" x-text="'(' + data.p1_completed + ' sesi)'"></span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">P2</span>
                                                <span class="font-medium truncate max-w-[140px]" x-text="data.pembimbing2_name"></span>
                                                <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 font-mono" x-text="'(' + data.p2_completed + ' sesi)'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Content Body (Scrollable) -->
                                <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6">

                                    <!-- Skeleton Loading State -->
                                    <div x-show="loading" class="space-y-4 animate-pulse">
                                        <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-100 dark:bg-slate-800/50">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                                            <div class="space-y-1.5 flex-1">
                                                <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-1/3"></div>
                                                <div class="h-2.5 bg-slate-200 dark:bg-slate-700 rounded w-2/3"></div>
                                            </div>
                                        </div>
                                        <div class="space-y-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800">
                                            <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-1/4"></div>
                                            <div class="h-3.5 bg-slate-200 dark:bg-slate-700 rounded w-3/4"></div>
                                            <div class="h-16 bg-slate-200 dark:bg-slate-700 rounded w-full"></div>
                                        </div>
                                        <div class="space-y-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800">
                                            <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-1/4"></div>
                                            <div class="h-3.5 bg-slate-200 dark:bg-slate-700 rounded w-3/4"></div>
                                            <div class="h-16 bg-slate-200 dark:bg-slate-700 rounded w-full"></div>
                                        </div>
                                    </div>

                                    <!-- Error State -->
                                    <div x-show="!loading && error" class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs">
                                        <div class="font-bold mb-1">Gagal Memuat Data</div>
                                        <p x-text="error"></p>
                                    </div>

                                    <!-- Empty State (0 Sessions) -->
                                    <div x-show="!loading && !error && data && data.sessions && data.sessions.length === 0" 
                                         class="py-12 text-center space-y-3">
                                        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="font-bold text-sm text-slate-700 dark:text-slate-300">Belum Ada Sesi Bimbingan Selesai</div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto leading-relaxed">
                                            Mahasiswa ini belum memiliki catatan riwayat bimbingan yang telah dituntaskan dan diverifikasi.
                                        </p>
                                    </div>

                                    <!-- Timeline List of Sessions (Loaded) -->
                                    <div x-show="!loading && !error && data && data.sessions && data.sessions.length > 0" 
                                         class="space-y-6">

                                        <!-- Thesis Title Card -->
                                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-100/80 dark:bg-slate-800/50">
                                            <div class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 mb-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                <span>Judul Skripsi</span>
                                            </div>
                                            <p class="text-xs sm:text-[13px] font-bold text-slate-800 dark:text-slate-100 leading-relaxed" x-text="data ? data.thesis_title : ''"></p>
                                        </div>

                                        <div class="relative border-l-2 border-slate-200 dark:border-slate-700/80 ml-3.5 sm:ml-4 space-y-8 pb-4">
                                            <template x-for="session in (data ? data.sessions : [])" :key="session.id">
                                                <div class="relative pl-7 sm:pl-8">
                                                    <!-- Timeline Dot -->
                                                    <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-orange-500 ring-4 ring-white dark:ring-slate-900 shadow-xs"></div>

                                                    <!-- Session Header -->
                                                    <div class="flex items-center justify-between gap-2 mb-2">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-orange-100 dark:bg-orange-950/70 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800"
                                                                  x-text="'Sesi #' + session.session_number"></span>
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                                                  :class="session.dosen_role === 'Pembimbing 1' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800' : 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800'">
                                                                <span class="w-1.5 h-1.5 rounded-full" :class="session.dosen_role === 'Pembimbing 1' ? 'bg-indigo-500' : 'bg-purple-500'"></span>
                                                                <span x-text="session.dosen_role + ': ' + session.dosen_name"></span>
                                                                <span x-show="session.is_my_session" class="text-[9px] font-black opacity-80">(Anda)</span>
                                                            </span>
                                                            <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-400" x-text="'• ' + session.time_ago"></span>
                                                        </div>
                                                        <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400" x-text="session.scheduled_at"></span>
                                                    </div>

                                                    <!-- Topic & Type -->
                                                    <div class="space-y-1.5 mb-3">
                                                        <h5 class="text-sm sm:text-[15px] font-bold text-slate-900 dark:text-white leading-snug" x-text="session.topic"></h5>
                                                        <div class="flex items-center gap-2 text-xs">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded font-semibold text-[10px]"
                                                                  :class="session.type === 'online' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                                                  x-text="session.type === 'online' ? 'Online' : 'Offline'"></span>
                                                            <span x-show="session.location" class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[220px]" x-text="session.location"></span>
                                                        </div>
                                                    </div>

                                                    <!-- Feedback Pembimbing Box (Primary) -->
                                                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 dark:bg-amber-500/10 space-y-3">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <div class="flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-amber-900 dark:text-amber-300">
                                                                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                <span>Catatan &amp; Arahan Pembimbing</span>
                                                            </div>
                                                            <span class="text-[10px] font-bold text-amber-800 dark:text-amber-300 bg-amber-500/20 dark:bg-amber-500/20 px-2.5 py-0.5 rounded-lg shrink-0" 
                                                                  x-text="session.dosen_name + ' (' + session.dosen_role + ')'"></span>
                                                        </div>
                                                        <p class="text-xs sm:text-[13px] text-slate-800 dark:text-slate-200 leading-relaxed font-normal whitespace-pre-wrap"
                                                           x-text="session.feedback ? session.feedback : 'Tidak ada catatan khusus pada sesi ini.'"></p>
                                                    </div>

                                                    <!-- Student Notes (If Any) -->
                                                    <template x-if="session.notes">
                                                        <div class="mt-6 sm:mt-7 p-4 sm:p-5 rounded-2xl bg-slate-100/90 dark:bg-slate-800/60 space-y-3">
                                                            <div class="flex items-center justify-between gap-2">
                                                                <div class="flex items-center gap-2 text-[11px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                                                                    <div class="w-6 h-6 rounded-lg bg-slate-200/80 dark:bg-slate-700/80 flex items-center justify-center shrink-0 text-slate-600 dark:text-slate-300">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                        </svg>
                                                                    </div>
                                                                    <span>Catatan Mahasiswa</span>
                                                                </div>
                                                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 bg-slate-200/80 dark:bg-slate-700/80 px-2.5 py-0.5 rounded-lg">
                                                                    Agenda / Persiapan
                                                                </span>
                                                            </div>
                                                            <p class="text-xs sm:text-[13px] text-slate-700 dark:text-slate-200 leading-relaxed font-normal whitespace-pre-wrap" x-text="session.notes"></p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex items-center justify-between gap-3 shrink-0">
                                    <button type="button" 
                                            @click="closeDrawer()" 
                                            class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer">
                                        Tutup
                                    </button>

                                    <template x-if="data && data.full_logbook_url">
                                        <a :href="data.full_logbook_url" 
                                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-bold transition-all shadow-xs shadow-orange-500/20 hover:scale-[1.02] cursor-pointer whitespace-nowrap">
                                            <span>Buka Logbook Lengkap</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <script>
                function quickPreviewDrawer() {
                    return {
                        open: false,
                        loading: false,
                        error: null,
                        data: null,

                        async fetchPreview(thesisId) {
                            this.open = true;
                            this.loading = true;
                            this.error = null;
                            this.data = null;
                            document.body.classList.add('overflow-hidden');

                            try {
                                const res = await fetch(`/theses/${thesisId}/logbooks/quick-preview`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                if (!res.ok) {
                                    const errData = await res.json().catch(() => ({}));
                                    throw new Error(errData.error || 'Gagal memuat riwayat logbook.');
                                }
                                this.data = await res.json();
                            } catch (e) {
                                this.error = e.message || 'Terjadi kesalahan saat mengambil riwayat bimbingan.';
                            } finally {
                                this.loading = false;
                            }
                        },

                        closeDrawer() {
                            this.open = false;
                            document.body.classList.remove('overflow-hidden');
                        }
                    }
                }
            </script>
        @endif

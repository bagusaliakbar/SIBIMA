        <!-- 3. LIVE REAL-TIME ATTENDANCE MONITOR MODAL -->
        <template x-teleport="body">
            <div x-data="mentoringLiveAttendance()" 
                 @open-live-modal.window="openModal()" 
                 x-show="liveModalOpen" 
                 x-cloak 
                 class="fixed inset-0 z-[60] overflow-y-auto" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Backdrop -->
                    <div x-show="liveModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                         @click="liveModalOpen = false" 
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!-- Modal Content -->
                    <div x-show="liveModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-slate-200 dark:border-slate-700 relative z-10">
                    
                    <!-- Modal Header -->
                    <div class="p-6 bg-slate-50/80 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 tracking-tight">Monitor Kehadiran Mahasiswa</h3>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pantau status konfirmasi kehadiran mahasiswa untuk seluruh sesi bimbingan aktif.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="fetchLiveAttendance(false)" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-2xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" :class="isSyncing ? 'animate-spin text-orange-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Segarkan</span>
                            </button>
                            <button type="button" @click="liveModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                        <!-- KPI Status Quick Metrics -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Akan Hadir -->
                            <button type="button" 
                                    @click="liveTab = 'attending'" 
                                    :class="liveTab === 'attending' 
                                        ? 'ring-2 ring-emerald-500 border-emerald-300 dark:border-emerald-700 bg-emerald-50/90 dark:bg-emerald-950/50 shadow-xs' 
                                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/20 hover:border-emerald-200 dark:hover:border-emerald-800'"
                                    class="p-4 rounded-2xl border text-left transition-all cursor-pointer relative overflow-hidden group">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-100/80 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase tracking-wider border border-emerald-200/60 dark:border-emerald-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Akan Hadir
                                    </span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></span>
                                </div>
                                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-2.5" x-text="attendanceStats.attending || 0">0</div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Mahasiswa siap hadir</p>
                            </button>

                            <!-- Izin / Berhalangan -->
                            <button type="button" 
                                    @click="liveTab = 'permission'" 
                                    :class="liveTab === 'permission' 
                                        ? 'ring-2 ring-amber-500 border-amber-300 dark:border-amber-700 bg-amber-50/90 dark:bg-amber-950/50 shadow-xs' 
                                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 hover:bg-amber-50/40 dark:hover:bg-amber-950/20 hover:border-amber-200 dark:hover:border-amber-800'"
                                    class="p-4 rounded-2xl border text-left transition-all cursor-pointer relative overflow-hidden group">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-100/80 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 text-[10px] font-black uppercase tracking-wider border border-amber-200/60 dark:border-amber-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Izin / Berhalangan
                                    </span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.6)]"></span>
                                </div>
                                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-2.5" x-text="attendanceStats.permission || 0">0</div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Dengan alasan izin</p>
                            </button>

                            <!-- Belum Konfirmasi -->
                            <button type="button" 
                                    @click="liveTab = 'pending'" 
                                    :class="liveTab === 'pending' 
                                        ? 'ring-2 ring-slate-500 border-slate-400 dark:border-slate-500 bg-slate-100 dark:bg-slate-800/80 shadow-xs' 
                                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-800/40'"
                                    class="p-4 rounded-2xl border text-left transition-all cursor-pointer relative overflow-hidden group">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                                        Belum Konfirmasi
                                    </span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                </div>
                                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-2.5" x-text="attendanceStats.pending || 0">0</div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Menunggu respon mahasiswa</p>
                            </button>
                        </div>

                        <!-- Filter Tabs, View Switcher & Search in Modal -->
                        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-2.5 pt-1">
                            <!-- Status Tabs -->
                            <div class="inline-flex flex-wrap sm:flex-nowrap items-center gap-1 p-1 bg-slate-100 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shrink-0">
                                <button type="button" 
                                        @click="liveTab = 'all'" 
                                        :class="liveTab === 'all' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-2xs font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-slate-800/60 font-medium'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap cursor-pointer">
                                    <span>Semua</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[10px] leading-none tabular-nums" :class="liveTab === 'all' ? 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-500 dark:text-slate-400'" x-text="attendanceStats.total || 0"></span>
                                </button>
                                <button type="button" 
                                        @click="liveTab = 'attending'" 
                                        :class="liveTab === 'attending' ? 'bg-emerald-600 text-white shadow-2xs font-semibold' : 'text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 font-medium'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap cursor-pointer">
                                    <span>Akan Hadir</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[10px] leading-none tabular-nums" :class="liveTab === 'attending' ? 'bg-emerald-500/40 text-white' : 'bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300'" x-text="attendanceStats.attending || 0"></span>
                                </button>
                                <button type="button" 
                                        @click="liveTab = 'permission'" 
                                        :class="liveTab === 'permission' ? 'bg-amber-600 text-white shadow-2xs font-semibold' : 'text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 font-medium'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap cursor-pointer">
                                    <span>Izin</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[10px] leading-none tabular-nums" :class="liveTab === 'permission' ? 'bg-amber-500/40 text-white' : 'bg-amber-100/80 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300'" x-text="attendanceStats.permission || 0"></span>
                                </button>
                                <button type="button" 
                                        @click="liveTab = 'pending'" 
                                        :class="liveTab === 'pending' ? 'bg-slate-800 dark:bg-slate-700 text-white shadow-2xs font-semibold' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/80 dark:hover:bg-slate-800/60 font-medium'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap cursor-pointer">
                                    <span>Belum Konfirmasi</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[10px] leading-none tabular-nums" :class="liveTab === 'pending' ? 'bg-slate-700 dark:bg-slate-600 text-white' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-300'" x-text="attendanceStats.pending || 0"></span>
                                </button>
                            </div>

                            <!-- Right Controls: View Mode Switcher & Search -->
                            <div class="flex items-center gap-2 flex-1 lg:justify-end min-w-0">
                                <!-- Mode Tampilan: Per Sesi (Default) vs Semua Mahasiswa -->
                                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shrink-0">
                                    <button type="button" 
                                            @click="liveViewMode = 'session'" 
                                            :class="liveViewMode === 'session' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                            title="Kelompokkan berdasarkan sesi bimbingan"
                                            class="px-2.5 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <span>Per Sesi</span>
                                    </button>
                                    <button type="button" 
                                            @click="liveViewMode = 'flat'" 
                                            :class="liveViewMode === 'flat' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                            title="Tampilkan seluruh daftar mahasiswa"
                                            class="px-2.5 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                        <span>Semua Mhs</span>
                                    </button>
                                </div>

                                <div class="relative flex-1 lg:max-w-[200px] min-w-[140px]">
                                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <input type="text" 
                                           x-model="liveSearch" 
                                           placeholder="Cari mhs / topik..." 
                                           class="w-full h-9 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 pl-8 pr-3 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                        </div>

                        <!-- 1. TAMPILAN PER SESI BIMBINGAN (DEFAULT) -->
                        <div x-show="liveViewMode === 'session'" class="space-y-4">
                            <template x-for="session in groupedLiveSessions" :key="session.key">
                                <div class="bg-slate-50/70 dark:bg-slate-900/60 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 overflow-hidden shadow-2xs transition-all hover:border-slate-300 dark:hover:border-slate-600">
                                    <!-- Session Header Banner -->
                                    <div class="p-4 sm:px-5 bg-white dark:bg-slate-800/90 border-b border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="space-y-1.5 min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span x-text="session.scheduled_date_formatted + ' • ' + session.scheduled_time_formatted"></span>
                                                </div>

                                                <template x-if="session.is_today">
                                                    <span class="px-2 py-0.5 rounded-md bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-300 text-[10px] font-black uppercase tracking-wider border border-red-200 dark:border-red-800/80 animate-pulse">
                                                        Hari Ini
                                                    </span>
                                                </template>

                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                                    <span x-text="session.type === 'online' ? 'Daring' : 'Tatap Muka'"></span>
                                                    <template x-if="session.location">
                                                        <span class="text-slate-500 dark:text-slate-400" x-text="'(' + session.location + ')'"></span>
                                                    </template>
                                                </span>

                                                <template x-if="session.dosen_name">
                                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium" x-text="'• Dosen: ' + session.dosen_name"></span>
                                                </template>
                                            </div>

                                            <!-- Session Topic -->
                                            <div class="flex items-baseline gap-2 pt-0.5">
                                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 shrink-0">Topik Sesi:</span>
                                                <h4 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-1" x-text="session.topic"></h4>
                                            </div>
                                        </div>

                                        <!-- Session Quick Metrics -->
                                        <div class="flex items-center gap-1.5 shrink-0 flex-wrap">
                                            <span class="text-[11px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mr-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700" x-text="session.students.length + ' Mahasiswa'"></span>
                                            <template x-if="session.stats.attending > 0">
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-1">
                                                    <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    <span x-text="session.stats.attending + ' Hadir'"></span>
                                                </span>
                                            </template>
                                            <template x-if="session.stats.permission > 0">
                                                <span class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200 text-[10px] font-bold border border-amber-200 dark:border-amber-800/60 flex items-center gap-1">
                                                    <svg class="w-2.5 h-2.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01"></path></svg>
                                                    <span x-text="session.stats.permission + ' Izin'"></span>
                                                </span>
                                            </template>
                                            <template x-if="session.stats.pending > 0">
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold border border-slate-200 dark:border-slate-700 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                    <span x-text="session.stats.pending + ' Belum Respon'"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Student Rows Inside Session -->
                                    <div class="p-3 sm:p-4 space-y-2.5 bg-white/70 dark:bg-slate-900/40">
                                        <template x-for="item in session.students" :key="item.id">
                                            <div class="p-3.5 rounded-xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-800/80"
                                                 :class="{
                                                     'border-emerald-200 dark:border-emerald-700/60 bg-emerald-50/20 dark:bg-emerald-950/20': item.attendance_status === 'attending',
                                                     'border-amber-200 dark:border-amber-700/60 bg-amber-50/20 dark:bg-amber-950/20': item.attendance_status === 'permission',
                                                     'border-slate-200 dark:border-slate-700': item.attendance_status === 'pending'
                                                 }">
                                                
                                                <div class="flex items-start gap-3 min-w-0 flex-1">
                                                    <template x-if="item.student_avatar">
                                                        <img :src="item.student_avatar" class="w-9 h-9 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                                    </template>
                                                    <template x-if="!item.student_avatar">
                                                        <div class="w-9 h-9 rounded-xl text-white flex items-center justify-center font-bold text-xs shrink-0"
                                                             :class="{
                                                                 'bg-emerald-600': item.attendance_status === 'attending',
                                                                 'bg-amber-600': item.attendance_status === 'permission',
                                                                 'bg-slate-700': item.attendance_status === 'pending'
                                                             }"
                                                             x-text="getInitials(item.student_name)">
                                                        </div>
                                                    </template>
                                                    
                                                    <div class="min-w-0 flex-1 space-y-0.5">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <h5 class="text-xs font-black text-slate-900 dark:text-white" x-text="item.student_name"></h5>
                                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-[10px] font-bold border border-slate-200 dark:border-slate-700" x-text="item.student_identifier"></span>
                                                        </div>
                                                        <template x-if="item.thesis_title && item.thesis_title !== '-'">
                                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate" x-text="'Skripsi: ' + item.thesis_title"></p>
                                                        </template>

                                                        <!-- Permission Reason Callout -->
                                                        <template x-if="item.attendance_status === 'permission' && item.attendance_reason">
                                                            <div class="mt-1.5 p-2 bg-amber-100/70 dark:bg-amber-950/60 rounded-lg border border-amber-200 dark:border-amber-700/80">
                                                                <span class="text-[9px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider block">Alasan Izin:</span>
                                                                <p class="text-xs text-amber-950 dark:text-amber-100 font-medium italic mt-0.5" x-text="'&ldquo;' + item.attendance_reason + '&rdquo;'"></p>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                <!-- Status Badge & WhatsApp Action -->
                                                <div class="flex sm:flex-col items-end justify-between sm:justify-center gap-1.5 shrink-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-200 dark:border-slate-700">
                                                    <template x-if="item.attendance_status === 'attending'">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[10px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-700/80">
                                                            <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            <span>Akan Hadir</span>
                                                        </span>
                                                    </template>
                                                    <template x-if="item.attendance_status === 'permission'">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[10px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-700/80">
                                                            <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                            <span>Izin / Berhalangan</span>
                                                        </span>
                                                    </template>
                                                    <template x-if="item.attendance_status === 'pending'">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-[10px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-600">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                                                            <span>Belum Respon</span>
                                                        </span>
                                                    </template>

                                                    <template x-if="item.confirmed_at_formatted">
                                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium" x-text="item.confirmed_at_formatted"></span>
                                                    </template>

                                                    <!-- WhatsApp Reminder Button for Pending / Permission -->
                                                    <template x-if="item.student_phone && (item.attendance_status === 'pending' || item.attendance_status === 'permission')">
                                                        <a :href="getWaLink(item)" 
                                                           target="_blank" 
                                                           class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/80 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/80 rounded-lg text-[10px] font-bold transition-all shadow-2xs">
                                                            <span>📲 Chat WA</span>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="groupedLiveSessions.length === 0">
                                <div class="p-8 text-center bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-200 dark:border-slate-700">
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Tidak ada sesi bimbingan atau mahasiswa yang sesuai dengan filter kehadiran ini.</p>
                                </div>
                            </template>
                        </div>

                        <!-- 2. TAMPILAN SEMUA MAHASISWA (FLAT LIST ALTERNATIF) -->
                        <div x-show="liveViewMode === 'flat'" class="space-y-2.5">
                            <template x-for="item in filteredLiveSessions" :key="item.id">
                                <div class="p-4 rounded-2xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                     :class="{
                                         'bg-emerald-50/40 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-700/80': item.attendance_status === 'attending',
                                         'bg-amber-50/40 dark:bg-amber-950/30 border-amber-200 dark:border-amber-700/80': item.attendance_status === 'permission',
                                         'bg-white dark:bg-slate-900/80 border-slate-200 dark:border-slate-700': item.attendance_status === 'pending'
                                     }">
                                    
                                    <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                        <template x-if="item.student_avatar">
                                            <img :src="item.student_avatar" class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                        </template>
                                        <template x-if="!item.student_avatar">
                                            <div class="w-10 h-10 rounded-xl bg-slate-800 text-white flex items-center justify-center font-bold text-xs shrink-0"
                                                 :class="{
                                                     'bg-emerald-600': item.attendance_status === 'attending',
                                                     'bg-amber-600': item.attendance_status === 'permission',
                                                     'bg-slate-700': item.attendance_status === 'pending'
                                                 }"
                                                 x-text="getInitials(item.student_name)">
                                            </div>
                                        </template>
                                        
                                        <div class="min-w-0 flex-1 space-y-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="text-xs font-black text-slate-900 dark:text-white" x-text="item.student_name"></h4>
                                                <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-[10px] font-bold border border-slate-200/80 dark:border-slate-700" x-text="item.student_identifier"></span>
                                                <template x-if="item.is_today">
                                                    <span class="px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-300 text-[9px] font-black uppercase border border-red-200 dark:border-red-800/80">Hari Ini</span>
                                                </template>
                                            </div>
                                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate" x-text="item.topic"></p>
                                            <div class="flex items-center gap-3 text-[11px] text-slate-600 dark:text-slate-300">
                                                <span class="font-bold text-slate-800 dark:text-slate-100" x-text="item.scheduled_date_formatted + ' • ' + item.scheduled_time_formatted"></span>
                                                <span class="font-medium text-slate-600 dark:text-slate-300" x-text="item.type === 'online' ? 'Daring' : 'Tatap Muka'"></span>
                                                <template x-if="item.dosen_name">
                                                    <span class="text-slate-500 dark:text-slate-400" x-text="'Dosen: ' + item.dosen_name"></span>
                                                </template>
                                            </div>

                                            <!-- Permission Reason Callout -->
                                            <template x-if="item.attendance_status === 'permission' && item.attendance_reason">
                                                <div class="mt-2 p-2.5 bg-amber-100/70 dark:bg-amber-950/60 rounded-xl border border-amber-200 dark:border-amber-700/80">
                                                    <span class="text-[9px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider block">Alasan Izin Mahasiswa:</span>
                                                    <p class="text-xs text-amber-950 dark:text-amber-100 font-medium italic mt-0.5" x-text="'&ldquo;' + item.attendance_reason + '&rdquo;'"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Status Badge & Quick Actions -->
                                    <div class="flex sm:flex-col items-end justify-between sm:justify-center gap-2 shrink-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-200 dark:border-slate-700">
                                        <template x-if="item.attendance_status === 'attending'">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[10px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-700/80">
                                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Akan Hadir</span>
                                            </span>
                                        </template>
                                        <template x-if="item.attendance_status === 'permission'">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[10px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-700/80">
                                                <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <span>Izin / Berhalangan</span>
                                            </span>
                                        </template>
                                        <template x-if="item.attendance_status === 'pending'">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-[10px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                                                <span>Belum Respon</span>
                                            </span>
                                        </template>

                                        <template x-if="item.confirmed_at_formatted">
                                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium" x-text="item.confirmed_at_formatted"></span>
                                        </template>

                                        <!-- WhatsApp Reminder Button for Pending / Permission -->
                                        <template x-if="item.student_phone && (item.attendance_status === 'pending' || item.attendance_status === 'permission')">
                                            <a :href="getWaLink(item)" 
                                               target="_blank" 
                                               class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/80 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/80 rounded-lg text-[10px] font-bold transition-all shadow-2xs">
                                                <span>📲 Chat WA</span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="filteredLiveSessions.length === 0">
                                <div class="p-8 text-center bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-200 dark:border-slate-700">
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Tidak ada mahasiswa yang sesuai dengan filter kehadiran ini.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 px-6 bg-slate-50/80 dark:bg-slate-900/80 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2 text-[11px] text-slate-600 dark:text-slate-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Live Sync Otomatis Aktif (tiap 10 detik) • Terakhir: <strong class="text-slate-800 dark:text-slate-100" x-text="lastUpdated"></strong></span>
                        </div>
                        <button type="button" @click="liveModalOpen = false" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-100 dark:hover:bg-white text-white dark:text-slate-900 font-bold rounded-xl text-xs transition-all shadow-xs cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </template>

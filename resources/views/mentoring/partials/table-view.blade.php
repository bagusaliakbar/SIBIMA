                <!-- 1. TABLE VIEW (Ringkas, Scannable & Expandable) -->
                <div x-show="viewMode === 'table'" x-transition class="space-y-5">
                    <!-- Sub-Toolbar: Navigation Tabs & Real-Time Actions -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-slate-200 dark:border-slate-800">
                        <!-- Primary Tabs: Aktif vs Riwayat -->
                        <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-2xs">
                            <a href="{{ route('mentoring-sessions.index', ['tab' => 'active', 'search' => $search, 'dosen_id' => $dosenId ?? '']) }}" 
                               class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $activeTab === 'active' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-200/60 dark:hover:bg-slate-700/60' }}">
                                Bimbingan Aktif
                            </a>
                            <a href="{{ route('mentoring-sessions.index', ['tab' => 'history', 'search' => $search, 'dosen_id' => $dosenId ?? '']) }}" 
                               class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $activeTab === 'history' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-200/60 dark:hover:bg-slate-700/60' }}">
                                Bimbingan Selesai (Mhs Lulus)
                            </a>
                        </div>

                        <!-- Right Actions: Status Filter, Attendance Filter, Expand All & Live Attendance -->
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end flex-wrap">
                            @php
                                $tableActionableCount = $sessions->whereIn('status', ['pending', 'approved'])->count();
                                $tableCompletedCount = $sessions->count() - $tableActionableCount;
                            @endphp
                            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                                <button type="button"
                                        @click="sessionStatusFilter = 'all'"
                                        :class="sessionStatusFilter === 'all' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">
                                    <span>Semua</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[11px] tabular-nums bg-slate-200/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-300">{{ $sessions->count() }}</span>
                                </button>
                                <button type="button"
                                        @click="sessionStatusFilter = 'actionable'"
                                        :class="sessionStatusFilter === 'actionable' ? 'bg-orange-600 text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">
                                    <span>Perlu Diproses</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[11px] tabular-nums" :class="sessionStatusFilter === 'actionable' ? 'bg-orange-500/40 text-white' : 'bg-slate-200/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-300'">{{ $tableActionableCount }}</span>
                                </button>
                                <button type="button"
                                        @click="sessionStatusFilter = 'completed'"
                                        :class="sessionStatusFilter === 'completed' ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">
                                    <span>Selesai</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[11px] tabular-nums" :class="sessionStatusFilter === 'completed' ? 'bg-slate-700 dark:bg-slate-500 text-white' : 'bg-slate-200/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-300'">{{ $tableCompletedCount }}</span>
                                </button>
                            </div>
                            @if($activeTab === 'active')
                                <!-- Dropdown Filter Kehadiran (Table View) -->
                                <div class="relative" x-data="{ openAttendanceDropdown: false }" @click.outside="openAttendanceDropdown = false">
                                    <button type="button" 
                                            @click="openAttendanceDropdown = !openAttendanceDropdown"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border cursor-pointer shadow-2xs {{ !empty($attendanceFilter) ? 'bg-orange-50/80 dark:bg-orange-950/30 border-orange-300 dark:border-orange-500/50 text-orange-700 dark:text-orange-300 ring-2 ring-orange-500/10' : 'bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700' }}"
                                            aria-haspopup="true"
                                            :aria-expanded="openAttendanceDropdown">
                                        <svg class="w-3.5 h-3.5 {{ !empty($attendanceFilter) ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                        
                                        @if(empty($attendanceFilter))
                                            <span>Kehadiran: Semua</span>
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300" x-text="attendanceStats?.total ?? '{{ $attendanceStats['total'] ?? 0 }}'">
                                                {{ $attendanceStats['total'] ?? 0 }}
                                            </span>
                                        @elseif($attendanceFilter === 'attending')
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                            <span>Akan Hadir</span>
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300" x-text="attendanceStats?.attending ?? '{{ $attendanceStats['attending'] ?? 0 }}'">
                                                {{ $attendanceStats['attending'] ?? 0 }}
                                            </span>
                                        @elseif($attendanceFilter === 'permission')
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                            <span>Izin</span>
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300" x-text="attendanceStats?.permission ?? '{{ $attendanceStats['permission'] ?? 0 }}'">
                                                {{ $attendanceStats['permission'] ?? 0 }}
                                            </span>
                                        @elseif($attendanceFilter === 'pending')
                                            <span class="w-2 h-2 rounded-full bg-slate-400 dark:bg-slate-500 animate-pulse shrink-0"></span>
                                            <span>Belum Respon</span>
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200" x-text="attendanceStats?.pending ?? '{{ $attendanceStats['pending'] ?? 0 }}'">
                                                {{ $attendanceStats['pending'] ?? 0 }}
                                            </span>
                                        @endif

                                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openAttendanceDropdown ? 'rotate-180 text-orange-600 dark:text-orange-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>

                                    <!-- Dropdown Menu Panel -->
                                    <div x-show="openAttendanceDropdown"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                         class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200 dark:border-slate-700 p-1.5 z-50 text-left"
                                         style="display: none;">
                                        
                                        <div class="px-3 py-2 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                                Filter Status Kehadiran
                                            </span>
                                            @if(!empty($attendanceFilter))
                                                <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '']) }}" 
                                                   class="text-orange-600 hover:text-orange-700 dark:text-orange-400 font-bold lowercase hover:underline">
                                                    Reset
                                                </a>
                                            @endif
                                        </div>

                                        <div class="py-1 space-y-0.5">
                                            <!-- 1. Semua -->
                                            <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '']) }}"
                                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ empty($attendanceFilter) ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full {{ empty($attendanceFilter) ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600' }} shrink-0"></span>
                                                    <span>Semua Kehadiran</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ empty($attendanceFilter) ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}" x-text="attendanceStats?.total ?? '{{ $attendanceStats['total'] ?? 0 }}'">
                                                        {{ $attendanceStats['total'] ?? 0 }}
                                                    </span>
                                                    @if(empty($attendanceFilter))
                                                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    @endif
                                                </div>
                                            </a>

                                            <!-- 2. Akan Hadir -->
                                            <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '', 'attendance' => 'attending']) }}"
                                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $attendanceFilter === 'attending' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                                    <span>Akan Hadir</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ $attendanceFilter === 'attending' ? 'bg-emerald-200/80 dark:bg-emerald-500/30 text-emerald-800 dark:text-emerald-200' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' }}" x-text="attendanceStats?.attending ?? '{{ $attendanceStats['attending'] ?? 0 }}'">
                                                        {{ $attendanceStats['attending'] ?? 0 }}
                                                    </span>
                                                    @if($attendanceFilter === 'attending')
                                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    @endif
                                                </div>
                                            </a>

                                            <!-- 3. Izin -->
                                            <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '', 'attendance' => 'permission']) }}"
                                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $attendanceFilter === 'permission' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                                    <span>Izin / Berhalangan</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ $attendanceFilter === 'permission' ? 'bg-amber-200/80 dark:bg-amber-500/30 text-amber-800 dark:text-amber-200' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' }}" x-text="attendanceStats?.permission ?? '{{ $attendanceStats['permission'] ?? 0 }}'">
                                                        {{ $attendanceStats['permission'] ?? 0 }}
                                                    </span>
                                                    @if($attendanceFilter === 'permission')
                                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    @endif
                                                </div>
                                            </a>

                                            <!-- 4. Belum Respon -->
                                            <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '', 'attendance' => 'pending']) }}"
                                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $attendanceFilter === 'pending' ? 'bg-slate-100 text-slate-900 dark:bg-slate-700/60 dark:text-slate-100 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-slate-400 dark:bg-slate-500 animate-pulse shrink-0"></span>
                                                    <span>Belum Respon</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ $attendanceFilter === 'pending' ? 'bg-slate-300 dark:bg-slate-600 text-slate-800 dark:text-slate-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}" x-text="attendanceStats?.pending ?? '{{ $attendanceStats['pending'] ?? 0 }}'">
                                                        {{ $attendanceStats['pending'] ?? 0 }}
                                                    </span>
                                                    @if($attendanceFilter === 'pending')
                                                        <svg class="w-4 h-4 text-slate-700 dark:text-slate-300 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    @endif
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($sessions->isNotEmpty())
                                <button type="button" 
                                        @click="toggleExpandAll()" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    <span x-text="expandedSessions.length > 0 ? 'Tutup Semua Rincian' : 'Buka Semua Rincian'">Buka Semua Rincian</span>
                                </button>
                            @endif

                            <button type="button" 
                                    @click="openLiveModal()" 
                                    onclick="window.openLiveModal()" 
                                    class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold shadow-2xs hover:shadow-xs transition-all cursor-pointer">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span>Monitor Kehadiran</span>
                            </button>

                            <button type="button" 
                                    @click="fetchLiveAttendance(false)" 
                                    title="Segarkan data kehadiran real-time"
                                    aria-label="Segarkan data kehadiran real-time"
                                    class="p-2 rounded-xl bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer shadow-2xs">
                                <svg class="w-4 h-4 shrink-0" :class="isSyncing ? 'animate-spin text-orange-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                        </div>
                    </div>

                    @if($sessions->isEmpty())
                        @if($activeTab === 'history')
                            <x-empty-state description="Belum ada riwayat bimbingan untuk mahasiswa yang sudah lulus." icon="mentoring" />
                        @else
                            <x-empty-state description="Belum ada jadwal bimbingan aktif." icon="mentoring" />
                        @endif
                    @else
                        <!-- The Master Table Container (Desktop Only: hidden on viewport < 768px) -->
                        <div class="hidden md:block overflow-x-auto rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-2xs bg-white dark:bg-slate-900">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/90 dark:bg-slate-800/90 border-b border-slate-200 dark:border-slate-700 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 select-none">
                                        @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                            <th class="py-3.5 px-4 w-10 text-center">
                                                <input type="checkbox" 
                                                       @change="toggleSelectAll()" 
                                                       :checked="isAllSelected()"
                                                       title="Pilih Semua Sesi yang Dapat Diproses"
                                                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500/30 transition-all cursor-pointer">
                                            </th>
                                        @endif
                                        <th class="py-3.5 px-4">
                                            <span>Mahasiswa</span>
                                            <span class="ml-1 text-[11px] font-normal lowercase tracking-normal text-slate-500 dark:text-slate-400 hidden sm:inline">(klik baris untuk berkas & catatan)</span>
                                        </th>
                                        <th class="py-3.5 px-4">Jadwal & Waktu</th>
                                        <th class="py-3.5 px-4">Topik & Judul Skripsi</th>
                                        <th class="py-3.5 px-4 text-center">Kehadiran Mhs</th>
                                        <th class="py-3.5 px-4 text-center">Status Sesi</th>
                                        <th class="py-3.5 px-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @php $prevGroupKey = null; @endphp
                                    @foreach($sessions as $session)
                                        @php
                                            $student = $session->thesis?->student;
                                            $thesis = $session->thesis;
                                            $gKey = ($session->dosen_id ?? '0') . '_' . $session->scheduled_at->format('Y-m-d H:i');
                                            $isGroup = ($groupCountMap[$gKey] ?? 1) > 1;
                                            $isSameGroupAsPrev = $isGroup && ($gKey === $prevGroupKey);
                                            $prevGroupKey = $gKey;
                                            $sMentoringCount = (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') 
                                                ? $thesis?->completed_mentoring_count 
                                                : $thesis?->getCompletedMentoringCountForDosen(Auth::id());
                                            
                                            $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                                            $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                                            $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                                        @endphp
                                        
                                        <!-- Main Session Row -->
                                        <tr x-show="matchesStatusFilter('{{ $session->status }}')"
                                            class="transition-colors hover:bg-slate-50/80 dark:hover:bg-slate-800/50 cursor-pointer {{ $isGroup ? 'border-l-[3px] border-l-indigo-500/70 dark:border-l-indigo-400/70' : '' }}"
                                            :class="{'bg-orange-50/40 dark:bg-orange-950/20': isSelected('{{ $session->id }}'), 'bg-slate-50/60 dark:bg-slate-800/40 font-medium': isExpanded('{{ $session->id }}')}"
                                            @click="if (!$event.target.closest('button') && !$event.target.closest('a') && !$event.target.closest('input')) toggleExpand('{{ $session->id }}')">
                                            
                                            @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                <td class="py-3.5 px-4 text-center" @click.stop>
                                                    @if($session->status !== 'completed' || $session->is_absent)
                                                        <input type="checkbox" 
                                                               value="{{ $session->id }}" 
                                                               x-model="selectedSessionIds" 
                                                               @change="notifySelection()"
                                                               class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500/30 transition-all cursor-pointer">
                                                    @else
                                                        <span class="w-4 h-4 inline-block text-slate-300 dark:text-slate-600">•</span>
                                                    @endif
                                                </td>
                                            @endif

                                            <!-- Mahasiswa Column -->
                                            <td class="py-3.5 px-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0">
                                                        <img src="{{ $student?->avatar_url }}" alt="{{ $student?->name ?? 'Mhs' }}" class="w-full h-full object-cover">
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="font-extrabold text-slate-900 dark:text-white uppercase tracking-tight text-xs flex items-center gap-1.5 flex-wrap">
                                                            <span>{{ $student?->name ?? 'Mahasiswa' }}</span>
                                                            @if($isGroup)
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 text-[11px] font-bold border border-indigo-200 dark:border-indigo-800" title="Bimbingan Bersama ({{ $groupCountMap[$gKey] }} Mahasiswa)">
                                                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                    <span>Kelompok ({{ $groupCountMap[$gKey] }})</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                            <span class="font-mono">{{ $student?->identifier ?? '-' }}</span>
                                                            <span>•</span>
                                                            <span class="font-bold text-slate-600 dark:text-slate-300">{{ $sMentoringCount }}x Bimbingan</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Jadwal & Waktu Column -->
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="space-y-1 {{ $isSameGroupAsPrev ? 'opacity-65' : '' }}">
                                                    <div class="flex items-center gap-1.5 font-extrabold text-slate-900 dark:text-slate-100 text-xs">
                                                        @if($session->scheduled_at->isToday())
                                                            <span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-[11px] font-black uppercase">Hari Ini</span>
                                                        @elseif($session->scheduled_at->isTomorrow())
                                                            <span class="px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 text-[11px] font-black uppercase">Besok</span>
                                                        @endif
                                                        <span>{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                                                        <span class="font-bold text-slate-700 dark:text-slate-300 font-mono tabular-nums">{{ $session->time_range }}</span>
                                                        <span>•</span>
                                                        @if($session->type === 'online')
                                                            @if($session->location)
                                                                <a href="{{ $linkUrl }}" target="_blank" @click.stop class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                    <span>{{ $isMeet ? 'G-Meet' : ($isZoom ? 'Zoom' : 'Online') }}</span>
                                                                </a>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 font-bold text-indigo-600 dark:text-indigo-400">
                                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                    <span>Online</span>
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="inline-flex items-center gap-1 font-bold text-slate-600 dark:text-slate-300 truncate max-w-[130px]" title="{{ $session->location ?? 'Offline' }}">
                                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                <span class="truncate">{{ $session->location ?? 'Offline' }}</span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Topik & Skripsi Column -->
                                            <td class="py-3.5 px-4 min-w-[200px] max-w-xs">
                                                <div class="space-y-0.5">
                                                    <p class="font-extrabold text-slate-900 dark:text-white truncate text-xs" title="{{ $session->topic }}">
                                                        {{ $session->topic }}
                                                    </p>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 italic" title="{{ $thesis?->title }}">
                                                        "{{ $thesis?->title ?? 'Judul Skripsi' }}"
                                                    </p>
                                                    @if($session->document_path)
                                                        <div class="pt-1">
                                                            <a href="{{ $session->document_path }}" 
                                                               target="_blank" 
                                                               @click.stop 
                                                               class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/40 dark:hover:bg-orange-900/50 text-orange-600 dark:text-orange-400 border border-orange-200/80 dark:border-orange-800 text-[11px] font-bold transition-colors shadow-2xs" 
                                                               title="Buka Dokumen Bimbingan Mahasiswa">
                                                                <x-google-drive-icon class="w-3.5 h-3.5 shrink-0" />
                                                                <span>Lihat Dokumen</span>
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Konfirmasi Kehadiran Column -->
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                @if($session->is_absent)
                                                    @if($session->student_attendance_status === 'attending')
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[11px] font-bold border border-slate-200 dark:border-slate-700" title="Mahasiswa sempat konfirmasi akan hadir, namun tercatat tidak hadir saat sesi">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                            <span>Konfirmasi: Hadir</span>
                                                        </span>
                                                    @elseif($session->student_attendance_status === 'permission')
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 text-[11px] font-bold border border-amber-200 dark:border-amber-800/60" title="{{ $session->student_attendance_reason }}">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                            <span>Izin</span>
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[11px] font-bold border border-slate-200 dark:border-slate-700">
                                                            <span>Tanpa Konfirmasi</span>
                                                        </span>
                                                    @endif
                                                @elseif($session->status === 'completed')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-[11px] font-black uppercase border border-emerald-200 dark:border-emerald-800">
                                                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Hadir</span>
                                                    </span>
                                                @elseif($session->student_attendance_status === 'attending')
                                                    <div class="inline-flex flex-col items-center gap-1">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[11px] font-black uppercase border border-emerald-200 dark:border-emerald-700">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                            <span>Akan Hadir</span>
                                                        </span>
                                                        @if($session->formatted_arrival_time)
                                                            <span class="text-[11px] font-bold font-mono text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800/60">
                                                                {{ $session->formatted_arrival_time }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @elseif($session->student_attendance_status === 'permission')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[11px] font-black uppercase border border-amber-200 dark:border-amber-700" title="{{ $session->student_attendance_reason }}">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        <span>Izin</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-bold border border-slate-200 dark:border-slate-600">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                                                        <span>Belum Respon</span>
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Status Sesi Column -->
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                @if($session->is_absent)
                                                    <x-status-badge type="red" label="TIDAK HADIR" />
                                                @else
                                                    @php
                                                        $statusLabelId = match($session->status) {
                                                            'completed' => 'SELESAI',
                                                            'approved' => 'DISETUJUI',
                                                            'pending' => 'MENUNGGU',
                                                            'rejected' => 'DITOLAK',
                                                            default => strtoupper($session->status),
                                                        };
                                                    @endphp
                                                    <x-status-badge 
                                                        :type="$session->status === 'pending' ? 'amber' : ($session->status === 'approved' ? 'orange' : ($session->status === 'rejected' ? 'red' : ($session->status === 'completed' ? 'emerald' : 'slate')))" 
                                                        :label="$statusLabelId" />
                                                @endif
                                            </td>

                                            <!-- Aksi & Rincian Column -->
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap" @click.stop>
                                                <div class="inline-flex items-center gap-1.5 justify-end">
                                                    @if($session->status === 'pending')
                                                        @can('updateStatus', $session)
                                                            <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="approved">
                                                                <button type="submit" 
                                                                        class="px-2.5 py-1.5 bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white rounded-xl text-[11px] font-black uppercase tracking-wider transition-colors shadow-xs cursor-pointer flex items-center gap-1" 
                                                                        title="Terima Pengajuan Jadwal Mahasiswa">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                    <span>Terima</span>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    @endif

                                                    <!-- Expand Button -->
                                                    <button type="button" 
                                                            @click="toggleExpand('{{ $session->id }}')" 
                                                            class="px-2.5 py-1.5 rounded-xl border text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer"
                                                            :class="isExpanded('{{ $session->id }}') ? 'bg-orange-600 text-white border-orange-600' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-orange-300 hover:text-orange-600'">
                                                        <span>Detail</span>
                                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="isExpanded('{{ $session->id }}') ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                    </button>

                                                    @can('update', $session)
                                                        <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-all" title="Ubah / Reschedule">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        </a>
                                                    @endcan

                                                    @can('delete', $session)
                                                        <button type="button" 
                                                                @click="openCancelModalFromEl($el)"
                                                                onclick="window.openCancelModalFromEl(this)"
                                                                aria-label="Batalkan jadwal bimbingan"
                                                                data-session-id="{{ $session->id }}"
                                                                data-student-name="{{ e($student?->name ?? 'Mahasiswa') }}"
                                                                data-student-npm="{{ e($student?->identifier ?? '-') }}"
                                                                data-topic="{{ e($session->topic) }}"
                                                                data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                data-scheduled-time="{{ $session->time_range }}"
                                                                data-is-group="{{ $isGroup ? '1' : '0' }}"
                                                                data-group-count="{{ $groupCountMap[($session->dosen_id ?? '0') . '_' . $session->scheduled_at->format('Y-m-d H:i')] ?? 1 }}"
                                                                class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-all cursor-pointer"
                                                                title="{{ $session->status === 'pending' ? 'Tolak Pengajuan Jadwal' : 'Batalkan Sesi Bimbingan' }}">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Expandable Detail Row (Accordion Panel) -->
                                        <tr x-show="isExpanded('{{ $session->id }}') && matchesStatusFilter('{{ $session->status }}')" x-cloak class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 {{ $isGroup ? 'border-l-[3px] border-l-indigo-500/70 dark:border-l-indigo-400/70' : '' }}">
                                            <td colspan="{{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 7 : 6 }}" class="p-4 sm:p-5">
                                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-xs">
                                                    <!-- Panel Kiri: Informasi Bimbingan & Konfirmasi Mahasiswa -->
                                                    <div class="space-y-3 bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200/90 dark:border-slate-700/80 shadow-2xs">
                                                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700 pb-2">
                                                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Rincian Mahasiswa</span>
                                                            <span class="font-bold text-slate-600 dark:text-slate-300 font-mono">{{ $student?->identifier ?? '-' }}</span>
                                                        </div>
                                                        
                                                        <div class="space-y-1.5 text-[11px]">
                                                            <p class="font-bold text-slate-800 dark:text-slate-200">
                                                                <span class="text-slate-500 dark:text-slate-400 font-normal">Judul Skripsi:</span><br>
                                                                {{ $thesis?->title ?? '-' }}
                                                            </p>
                                                            @if($thesis?->pembimbing1)
                                                                <p class="text-slate-600 dark:text-slate-400">
                                                                    <span class="text-slate-400">P1:</span> <strong class="text-slate-700 dark:text-slate-300">{{ $thesis->pembimbing1->name }}</strong>
                                                                </p>
                                                            @endif
                                                            @if($thesis?->pembimbing2)
                                                                <p class="text-slate-600 dark:text-slate-400">
                                                                    <span class="text-slate-400">P2:</span> <strong class="text-slate-700 dark:text-slate-300">{{ $thesis->pembimbing2->name }}</strong>
                                                                </p>
                                                            @endif
                                                        </div>

                                                        <!-- Konfirmasi Kehadiran Detail -->
                                                        <div class="pt-2 border-t border-slate-100 dark:border-slate-700 space-y-1">
                                                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Status Kehadiran Mahasiswa:</span>
                                                            @if($session->student_attendance_status === 'attending')
                                                                <p class="text-emerald-700 dark:text-emerald-400 font-bold flex items-center gap-1.5 flex-wrap">
                                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                                    <span>Konfirmasi Hadir</span>
                                                                    @if($session->formatted_arrival_time)
                                                                        <span class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 text-[11px] font-mono">Tiba {{ $session->formatted_arrival_time }}</span>
                                                                    @endif
                                                                    @if($session->student_confirmed_at)
                                                                        <span class="text-[11px] font-normal text-slate-500 dark:text-slate-400">({{ $session->student_confirmed_at->locale('id')->translatedFormat('d M, H:i') }})</span>
                                                                    @endif
                                                                </p>
                                                            @elseif($session->student_attendance_status === 'permission')
                                                                <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80">
                                                                    <p class="text-amber-800 dark:text-amber-300 font-bold text-[11px] mb-0.5">Alasan Izin:</p>
                                                                    <p class="text-amber-900 dark:text-amber-200 italic leading-relaxed text-[11px]">"{{ $session->student_attendance_reason }}"</p>
                                                                </div>
                                                            @else
                                                                <p class="text-slate-500 dark:text-slate-400 italic text-[11px]">Mahasiswa belum melakukan konfirmasi kehadiran.</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Panel Tengah: Catatan Pengajuan Mahasiswa -->
                                                    <div class="space-y-3 bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200/90 dark:border-slate-700/80 shadow-2xs flex flex-col justify-between">
                                                        <div class="space-y-2">
                                                            <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700 pb-2">
                                                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Catatan Mahasiswa</span>
                                                                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Pengajuan Bimbingan</span>
                                                            </div>
                                                            @if($session->notes)
                                                                <p class="text-slate-700 dark:text-slate-300 italic leading-relaxed whitespace-pre-line text-xs">
                                                                    "{{ $session->notes }}"
                                                                </p>
                                                            @else
                                                                <p class="text-slate-500 dark:text-slate-400 italic text-xs">Tidak ada catatan pengajuan khusus dari mahasiswa.</p>
                                                            @endif

                                                            @if($session->document_path)
                                                                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/80">
                                                                    <span class="text-[11px] font-black uppercase tracking-wider text-orange-600 dark:text-orange-400 block mb-1">Dokumen Bimbingan Mahasiswa:</span>
                                                                    <a href="{{ $session->document_path }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/60 dark:hover:bg-orange-900/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800 rounded-xl font-bold text-xs transition-colors shadow-2xs">
                                                                        <x-google-drive-icon class="w-4 h-4 shrink-0" />
                                                                        <span>Lihat Dokumen Bimbingan Mahasiswa</span>
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Fast Link to Meeting / Location -->
                                                        <div class="pt-2 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                                                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Media Bimbingan:</span>
                                                            @if($session->type === 'online' && $session->location)
                                                                <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-lg font-bold text-[11px] hover:bg-emerald-100 transition-all">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                    <span>Buka Link Meeting</span>
                                                                </a>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 font-bold text-slate-700 dark:text-slate-300 text-[11px]"><svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>{{ $session->location ?? 'Tatap Muka Langsung' }}</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Panel Kanan: Catatan Hasil Bimbingan Dosen (Feedback & Revisi Form) -->
                                                    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200/90 dark:border-slate-700/80 shadow-2xs space-y-3" x-data="{ editingFeedback: false }">
                                                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700 pb-2">
                                                            <span class="text-[11px] font-black uppercase tracking-wider text-orange-600 dark:text-orange-400 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                                                <span>Hasil Bimbingan Dosen</span>
                                                            </span>
                                                            @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) && ($session->status === 'completed' || $session->feedback))
                                                                <button type="button" 
                                                                        @click="editingFeedback = !editingFeedback" 
                                                                        class="text-[11px] font-bold text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                                                                    <span x-text="editingFeedback ? 'Batal' : 'Ubah Catatan'">Ubah Catatan</span>
                                                                </button>
                                                            @endif
                                                        </div>

                                                        @if($session->status === 'pending')
                                                            <div class="p-3.5 bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 rounded-xl space-y-2.5">
                                                                <div class="flex items-center justify-between gap-2">
                                                                    <span class="text-[11px] font-black text-amber-800 dark:text-amber-300 flex items-center gap-1.5">
                                                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                        <span>Pengajuan Jadwal Menunggu Persetujuan</span>
                                                                    </span>
                                                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-black bg-amber-200 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200 uppercase">Pending</span>
                                                                </div>
                                                                <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed">
                                                                    Mahasiswa mengajukan jadwal ini. Klik <strong>Terima Jadwal</strong> untuk mengonfirmasi jadwal bimbingan atau <strong>Tolak</strong> jika waktu tidak sesuai.
                                                                </p>
                                                                <div class="pt-1 flex items-center gap-2 flex-wrap">
                                                                    @can('updateStatus', $session)
                                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="inline">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <input type="hidden" name="status" value="approved">
                                                                            <button type="submit" class="px-3.5 py-1.5 bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white rounded-xl text-xs font-bold transition-colors shadow-xs cursor-pointer flex items-center gap-1.5">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                                <span>Terima Jadwal</span>
                                                                            </button>
                                                                        </form>
                                                                    @endcan
                                                                    @can('update', $session)
                                                                        <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-2xs">Ubah Jadwal</a>
                                                                    @endcan
                                                                    @can('delete', $session)
                                                                        <button type="button" 
                                                                                @click="openCancelModalFromEl($el)"
                                                                                onclick="window.openCancelModalFromEl(this)"
                                                                                data-session-id="{{ $session->id }}"
                                                                                data-student-name="{{ e($student?->name ?? 'Mahasiswa') }}"
                                                                                data-student-npm="{{ e($student?->identifier ?? '-') }}"
                                                                                data-topic="{{ e($session->topic) }}"
                                                                                data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                                data-scheduled-time="{{ $session->time_range }}"
                                                                                data-is-group="{{ $isGroup ? '1' : '0' }}"
                                                                                data-group-count="{{ $groupCountMap[($session->dosen_id ?? '0') . '_' . $session->scheduled_at->format('Y-m-d H:i')] ?? 1 }}"
                                                                                class="px-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                                                            Tolak Pengajuan
                                                                        </button>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        @elseif($session->feedback && $session->status === 'completed')
                                                            <div x-show="!editingFeedback" class="space-y-2">
                                                                <p class="text-slate-800 dark:text-slate-200 font-medium leading-relaxed whitespace-pre-line text-xs bg-orange-50/40 dark:bg-orange-950/20 p-3 rounded-lg border border-orange-200/60 dark:border-orange-900/40">
                                                                    {{ $session->feedback }}
                                                                </p>
                                                                @if($session->feedback_document_url)
                                                                    <a href="{{ $session->feedback_document_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                                        <x-google-drive-icon class="w-3.5 h-3.5 shrink-0" />
                                                                        <span>Dokumen Koreksi / Feedback Dosen</span>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        @elseif($session->status !== 'completed' && $session->status !== 'pending')
                                                            <div x-show="!editingFeedback" class="space-y-2">
                                                                <p class="text-slate-500 dark:text-slate-400 italic text-xs">
                                                                    Sesi bimbingan belum diselesaikan. Anda dapat langsung mengisikan catatan hasil bimbingan di bawah ini.
                                                                </p>
                                                            </div>
                                                        @endif

                                                        <!-- Form Input / Edit Feedback Langsung di Tempat -->
                                                        @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                            <div x-show="editingFeedback || {{ ($session->status !== 'completed' && $session->status !== 'pending') ? 'true' : 'false' }}" class="space-y-2">
                                                                <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="space-y-2">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="completed">
                                                                    <div>
                                                                        <textarea name="feedback" 
                                                                                  rows="2" 
                                                                                  required 
                                                                                  placeholder="Ketik catatan hasil bimbingan & arahan revisi..." 
                                                                                  class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 p-2 shadow-2xs">{{ $session->feedback }}</textarea>
                                                                    </div>
                                                                    <div>
                                                                        <input type="url" 
                                                                               name="feedback_document_url" 
                                                                               value="{{ $session->feedback_document_url }}" 
                                                                               placeholder="Link Dokumen Google Drive (opsional)..." 
                                                                               class="w-full text-[11px] rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 px-2.5 py-1.5 shadow-2xs">
                                                                    </div>
                                                                    <div class="flex items-center justify-end gap-2 pt-1">
                                                                        @if($session->status === 'completed')
                                                                            <button type="button" @click="editingFeedback = false" class="px-2.5 py-1 text-slate-500 dark:text-slate-400 hover:text-slate-700 text-[11px] font-bold cursor-pointer">Batal</button>
                                                                            <button type="submit" class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer">Simpan Catatan</button>
                                                                        @else
                                                                            <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                                <span>Simpan & Selesaikan Sesi</span>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @include('mentoring.partials.table-view-mobile')
                    @endif
                </div>

                <!-- 2. CARDS VIEW -->
                <div x-show="viewMode === 'cards'" x-transition>
                    <!-- Sub-Toolbar: Navigation Tabs & Real-Time Actions -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-800">
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

                        <!-- Grouping Switcher, Status Filter & Real-Time Actions -->
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end flex-wrap">
                            @php
                                $cardActionableCount = $sessions->whereIn('status', ['pending', 'approved'])->count();
                                $cardCompletedCount = $sessions->count() - $cardActionableCount;
                            @endphp
                            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                                <button type="button"
                                        @click="sessionStatusFilter = 'all'"
                                        :class="sessionStatusFilter === 'all' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">
                                    <span>Semua</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[10px] tabular-nums bg-slate-200/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-300">{{ $sessions->count() }}</span>
                                </button>
                                <button type="button"
                                        @click="sessionStatusFilter = 'actionable'"
                                        :class="sessionStatusFilter === 'actionable' ? 'bg-orange-600 text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">
                                    <span>Perlu Diproses</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[10px] tabular-nums" :class="sessionStatusFilter === 'actionable' ? 'bg-orange-500/40 text-white' : 'bg-slate-200/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-300'">{{ $cardActionableCount }}</span>
                                </button>
                                <button type="button"
                                        @click="sessionStatusFilter = 'completed'"
                                        :class="sessionStatusFilter === 'completed' ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">
                                    <span>Selesai</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[10px] tabular-nums" :class="sessionStatusFilter === 'completed' ? 'bg-slate-700 dark:bg-slate-500 text-white' : 'bg-slate-200/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-300'">{{ $cardCompletedCount }}</span>
                                </button>
                            </div>
                            <!-- Toggle Mode Grouping: Per Sesi (Default) vs Per Mahasiswa -->
                            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                                <button type="button" 
                                        @click="setGrouping('session')" 
                                        :class="cardGrouping === 'session' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs font-black' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-bold'"
                                        title="Kelompokkan kartu berdasarkan sesi bimbingan"
                                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    <span>Per Sesi</span>
                                </button>
                                <button type="button" 
                                        @click="setGrouping('student')" 
                                        :class="cardGrouping === 'student' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs font-black' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-bold'"
                                        title="Kelompokkan kartu berdasarkan nama mahasiswa"
                                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span>Per Mahasiswa</span>
                                </button>
                            </div>

                            @if($activeTab === 'active')
                                <!-- Dropdown Filter Kehadiran (Card View) -->
                                <div class="relative" x-data="{ openAttendanceDropdownCard: false }" @click.outside="openAttendanceDropdownCard = false">
                                    <button type="button" 
                                            @click="openAttendanceDropdownCard = !openAttendanceDropdownCard"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border cursor-pointer shadow-2xs {{ !empty($attendanceFilter) ? 'bg-orange-50/80 dark:bg-orange-950/30 border-orange-300 dark:border-orange-500/50 text-orange-700 dark:text-orange-300 ring-2 ring-orange-500/10' : 'bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700' }}"
                                            aria-haspopup="true"
                                            :aria-expanded="openAttendanceDropdownCard">
                                        <svg class="w-3.5 h-3.5 {{ !empty($attendanceFilter) ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                        
                                        @if(empty($attendanceFilter))
                                            <span>Kehadiran: Semua</span>
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300" x-text="attendanceStats?.total ?? '{{ $attendanceStats['total'] ?? 0 }}'">
                                                {{ $attendanceStats['total'] ?? 0 }}
                                            </span>
                                        @elseif($attendanceFilter === 'attending')
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                            <span>Akan Hadir</span>
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300" x-text="attendanceStats?.attending ?? '{{ $attendanceStats['attending'] ?? 0 }}'">
                                                {{ $attendanceStats['attending'] ?? 0 }}
                                            </span>
                                        @elseif($attendanceFilter === 'permission')
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                            <span>Izin</span>
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300" x-text="attendanceStats?.permission ?? '{{ $attendanceStats['permission'] ?? 0 }}'">
                                                {{ $attendanceStats['permission'] ?? 0 }}
                                            </span>
                                        @elseif($attendanceFilter === 'pending')
                                            <span class="w-2 h-2 rounded-full bg-slate-400 dark:bg-slate-500 animate-pulse shrink-0"></span>
                                            <span>Belum Respon</span>
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200" x-text="attendanceStats?.pending ?? '{{ $attendanceStats['pending'] ?? 0 }}'">
                                                {{ $attendanceStats['pending'] ?? 0 }}
                                            </span>
                                        @endif

                                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openAttendanceDropdownCard ? 'rotate-180 text-orange-600 dark:text-orange-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>

                                    <!-- Dropdown Menu Panel -->
                                    <div x-show="openAttendanceDropdownCard"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                         class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200 dark:border-slate-700 p-1.5 z-50 text-left"
                                         style="display: none;">
                                        
                                        <div class="px-3 py-2 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
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
                                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ empty($attendanceFilter) ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}" x-text="attendanceStats?.total ?? '{{ $attendanceStats['total'] ?? 0 }}'">
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
                                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ $attendanceFilter === 'attending' ? 'bg-emerald-200/80 dark:bg-emerald-500/30 text-emerald-800 dark:text-emerald-200' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' }}" x-text="attendanceStats?.attending ?? '{{ $attendanceStats['attending'] ?? 0 }}'">
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
                                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ $attendanceFilter === 'permission' ? 'bg-amber-200/80 dark:bg-amber-500/30 text-amber-800 dark:text-amber-200' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' }}" x-text="attendanceStats?.permission ?? '{{ $attendanceStats['permission'] ?? 0 }}'">
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
                                                    <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ $attendanceFilter === 'pending' ? 'bg-slate-300 dark:bg-slate-600 text-slate-800 dark:text-slate-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}" x-text="attendanceStats?.pending ?? '{{ $attendanceStats['pending'] ?? 0 }}'">
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

                            <button type="button" 
                                    id="btn-monitor-kehadiran"
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
                                    class="p-2 rounded-xl bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all cursor-pointer shadow-2xs hover:scale-105 active:scale-95">
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
                        @include('mentoring.partials.cards-timeline')

                        @include('mentoring.partials.cards-by-student')
                    
                    @endif
                </div>

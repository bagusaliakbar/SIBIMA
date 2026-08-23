<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Bimbingan', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full" x-data="mentoringSchedule()">
        
        <!-- KPI Quick Bar (4 Metrik Ringkas) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- 1. Jadwal Minggu Ini -->
            <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-orange-200 dark:hover:border-orange-500/30">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Jadwal Minggu Ini</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $kpiStats['this_week'] ?? 0 }}</span>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Sesi</span>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Sesi terjadwal aktif minggu ini</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-slate-700/60 text-orange-600 dark:text-orange-400 border border-orange-200/60 dark:border-slate-600/60 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- 2. Menunggu Hasil / Catatan Dosen -->
            <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-amber-200 dark:hover:border-amber-500/30">
                <div class="space-y-1">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Perlu Catatan / Hasil</span>
                        @if(($kpiStats['pending_feedback'] ?? 0) > 0)
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black {{ ($kpiStats['pending_feedback'] ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-100' }}">{{ $kpiStats['pending_feedback'] ?? 0 }}</span>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Sesi</span>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Sesi lewat waktu belum dinilai</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-slate-700/60 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-slate-600/60 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
            </div>

            <!-- 3. Mahasiswa Siap ACC Seminar -->
            <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-500/30">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Siap ACC Seminar</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black {{ ($kpiStats['ready_acc_seminar'] ?? 0) > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-800 dark:text-slate-100' }}">{{ $kpiStats['ready_acc_seminar'] ?? 0 }}</span>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Mhs</span>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Mencapai syarat &ge; 4x bimbingan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-slate-700/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-slate-600/60 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
            </div>

            <!-- 4. Mahasiswa Siap ACC Sidang -->
            <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-500/30">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Siap ACC Sidang</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black {{ ($kpiStats['ready_acc_sidang'] ?? 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-slate-100' }}">{{ $kpiStats['ready_acc_sidang'] ?? 0 }}</span>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Mhs</span>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Mencapai syarat &ge; 8x bimbingan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-slate-700/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-slate-600/60 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                </div>
            </div>
        </div>
        
        <x-table-card 
            title="{{ $activeTab === 'history' ? 'Riwayat Bimbingan' : 'Jadwal Bimbingan' }}"
            :footer="$sessions->links()">
            
            <x-slot name="headerActions">
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <!-- View Mode Toggle (Cards vs Calendar) -->
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                        <button type="button" 
                                @click="switchView('cards')" 
                                :class="viewMode === 'cards' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span>Kartu</span>
                        </button>
                        <button type="button" 
                                @click="switchView('calendar')" 
                                :class="viewMode === 'calendar' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Kalender</span>
                        </button>
                    </div>

                    @if(in_array(Auth::user()->role, ['admin', 'kaprodi']) && isset($dosens))
                        <form action="{{ route('mentoring-sessions.index') }}" method="GET" class="inline-block">
                            <input type="hidden" name="tab" value="{{ $activeTab }}">
                            <input type="hidden" name="search" value="{{ $search }}">
                            <select name="dosen_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-[11px] font-bold text-slate-700 dark:text-slate-200 py-2.5 px-3 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Filter Dosen Pembimbing...</option>
                                @foreach($dosens as $d)
                                    <option value="{{ $d->id }}" {{ ($dosenId ?? '') == $d->id ? 'selected' : '' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif

                    <template x-if="viewMode === 'cards'">
                        <x-search-input 
                            name="search" 
                            :value="$search ?? ''" 
                            placeholder="Cari nama atau topik..." 
                            route="mentoring-sessions.index"
                            :params="['tab' => $activeTab, 'dosen_id' => $dosenId ?? '']" />
                    </template>

                    @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                        <a href="{{ route('mentoring-sessions.create') }}" class="inline-flex items-center px-4 py-2.5 bg-orange-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-700 transition-all shadow-sm whitespace-nowrap">+ Tambah Jadwal</a>
                    @endif
                </div>
            </x-slot>

            <div class="p-6">
                <!-- 1. CARDS VIEW -->
                <div x-show="viewMode === 'cards'" x-transition>
                    <!-- Sub-Toolbar: Navigation Tabs & Real-Time Actions -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                        <!-- Primary Tabs: Aktif vs Riwayat -->
                        <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-2xs">
                            <a href="{{ route('mentoring-sessions.index', ['tab' => 'active', 'search' => $search, 'dosen_id' => $dosenId ?? '']) }}" 
                               class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $activeTab === 'active' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-200/60 dark:hover:bg-slate-700/60' }}">
                                Bimbingan Aktif
                            </a>
                            <a href="{{ route('mentoring-sessions.index', ['tab' => 'history', 'search' => $search, 'dosen_id' => $dosenId ?? '']) }}" 
                               class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $activeTab === 'history' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-200/60 dark:hover:bg-slate-700/60' }}">
                                Riwayat Bimbingan
                            </a>
                        </div>

                        <!-- Real-Time Actions (Monitor & Sync) -->
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                            <button type="button" 
                                    @click="openLiveModal()" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition-all active:scale-95 cursor-pointer">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-200 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                </span>
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
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

                    @if($activeTab === 'active')
                    <!-- Quick Attendance Filter Pills (Clean Segmented) -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 shrink-0 mr-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            <span>Filter Kehadiran:</span>
                        </span>

                        {{-- Semua --}}
                        <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ empty($attendanceFilter) ? 'bg-orange-500 text-white border-orange-500 shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <span>Semua</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ empty($attendanceFilter) ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}" x-text="attendanceStats.total">
                                {{ $attendanceStats['total'] ?? 0 }}
                            </span>
                        </a>

                        {{-- Akan Hadir --}}
                        <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '', 'attendance' => 'attending']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ $attendanceFilter === 'attending' ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 border-emerald-200/80 dark:border-emerald-800/80 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }}">
                            <span class="w-2 h-2 rounded-full {{ $attendanceFilter === 'attending' ? 'bg-white' : 'bg-emerald-500' }} shrink-0"></span>
                            <span>Akan Hadir</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $attendanceFilter === 'attending' ? 'bg-white/20 text-white' : 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200' }}" x-text="attendanceStats.attending">
                                {{ $attendanceStats['attending'] ?? 0 }}
                            </span>
                        </a>

                        {{-- Izin / Berhalangan --}}
                        <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '', 'attendance' => 'permission']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ $attendanceFilter === 'permission' ? 'bg-amber-600 text-white border-amber-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-amber-700 dark:text-amber-400 border-amber-200/80 dark:border-amber-800/80 hover:bg-amber-50 dark:hover:bg-amber-950/40' }}">
                            <span class="w-2 h-2 rounded-full {{ $attendanceFilter === 'permission' ? 'bg-white' : 'bg-amber-500' }} shrink-0"></span>
                            <span>Izin / Berhalangan</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $attendanceFilter === 'permission' ? 'bg-white/20 text-white' : 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200' }}" x-text="attendanceStats.permission">
                                {{ $attendanceStats['permission'] ?? 0 }}
                            </span>
                        </a>

                        {{-- Belum Respon --}}
                        <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '', 'attendance' => 'pending']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ $attendanceFilter === 'pending' ? 'bg-slate-700 dark:bg-slate-600 text-white border-slate-700 dark:border-slate-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <span class="w-2 h-2 rounded-full {{ $attendanceFilter === 'pending' ? 'bg-white' : 'bg-slate-400 dark:bg-slate-500' }} animate-pulse shrink-0"></span>
                            <span>Belum Respon</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $attendanceFilter === 'pending' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}" x-text="attendanceStats.pending">
                                {{ $attendanceStats['pending'] ?? 0 }}
                            </span>
                        </a>
                    </div>
                    @endif

                    <div class="space-y-12">
                    @php
                        $groupedSessions = $sessions->groupBy(function($session) {
                            return $session->thesis->student->name ?? 'Mahasiswa';
                        });
                    @endphp

                    @forelse($groupedSessions as $studentName => $studentSessions)
                        <div>
                            @php
                                $studentThesis = $studentSessions->first()->thesis;
                                $mentoringCount = (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') 
                                    ? $studentThesis->completed_mentoring_count 
                                    : $studentThesis->getCompletedMentoringCountForDosen(Auth::id());
                            @endphp
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b border-slate-100 dark:border-slate-700/50 pb-4 gap-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-sm bg-orange-50 dark:bg-orange-900/10 mr-3 shrink-0">
                                        <img src="{{ $studentThesis->student?->avatar_url }}" alt="{{ $studentName }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $studentName }}</h4>
                                            @if($studentThesis->status === 'completed')
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider">
                                                    Lulus
                                                </span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded-md bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-wider">
                                                {{ $mentoringCount }} Bimbingan {{ (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') ? 'Total' : 'dengan Anda' }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2.5 mt-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                            <span class="inline-flex items-center gap-1 bg-slate-50 dark:bg-slate-900/60 px-2 py-0.5 rounded-md border border-slate-100 dark:border-slate-700/50 text-[10px]">
                                                <span class="font-bold text-slate-400">P1:</span> <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $studentThesis->pembimbing1->name ?? '-' }}</span>
                                            </span>
                                            <span class="inline-flex items-center gap-1 bg-slate-50 dark:bg-slate-900/60 px-2 py-0.5 rounded-md border border-slate-100 dark:border-slate-700/50 text-[10px]">
                                                <span class="font-bold text-slate-400">P2:</span> <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $studentThesis->pembimbing2->name ?? '-' }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                    <div class="flex flex-wrap gap-2">
                                        {{-- ACC UP Button --}}
                                        @php
                                            $isAdminOrKaprodi = in_array(Auth::user()->role, ['admin', 'kaprodi']);
                                            $isP1 = Auth::id() === $studentThesis->pembimbing1_id;
                                            $isP2 = Auth::id() === $studentThesis->pembimbing2_id;
                                            $hasAccUp = $isAdminOrKaprodi ? ($studentThesis->acc_up_p1 && $studentThesis->acc_up_p2) : ($isP1 ? $studentThesis->acc_up_p1 : ($isP2 ? $studentThesis->acc_up_p2 : false));
                                        @endphp
                                        {{-- ACC UP Group --}}
                                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/50 p-1.5 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                            <form action="{{ route('theses.toggle-acc', [$studentThesis->id, 'up']) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin {{ $hasAccUp ? 'membatalkan' : 'memberikan' }} ACC Seminar untuk {{ $studentName }}?{{ $mentoringCount < 4 && !$hasAccUp ? ' Catatan: Jumlah bimbingan mahasiswa belum mencapai 4 kali.' : '' }}')">
                                                @csrf
                                                @if($isAdminOrKaprodi)
                                                    <input type="hidden" name="slot" value="all">
                                                @endif
                                                <button type="submit" 
                                                    title="{{ $hasAccUp ? 'Batalkan ACC Seminar' : 'Berikan ACC Seminar' }}"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm
                                                    {{ $hasAccUp ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}
                                                    {{ $mentoringCount < 4 && !$hasAccUp ? 'opacity-75' : '' }}">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    ACC SEMINAR
                                                    @if($mentoringCount < 4 && !$hasAccUp)
                                                        <span class="ml-1.5 px-1.5 rounded bg-orange-100 text-orange-700 text-[9px]">{{ $mentoringCount }}/4</span>
                                                    @endif
                                                </button>
                                            </form>
                                            <div class="flex gap-1 border-l border-slate-200 dark:border-slate-700 pl-2 ml-1">
                                                <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_up_p1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P1"></div>
                                                <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_up_p2 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P2"></div>
                                            </div>
                                        </div>

                                        {{-- ACC Sidang Group --}}
                                        @php
                                            $hasAccSidang = $isAdminOrKaprodi ? ($studentThesis->acc_sidang_p1 && $studentThesis->acc_sidang_p2) : ($isP1 ? $studentThesis->acc_sidang_p1 : ($isP2 ? $studentThesis->acc_sidang_p2 : false));
                                        @endphp
                                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/50 p-1.5 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                            <form action="{{ route('theses.toggle-acc', [$studentThesis->id, 'sidang']) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin {{ $hasAccSidang ? 'membatalkan' : 'memberikan' }} ACC Sidang untuk {{ $studentName }}?{{ $mentoringCount < 8 && !$hasAccSidang ? ' Catatan: Jumlah bimbingan mahasiswa belum mencapai 8 kali.' : '' }}')">
                                                @csrf
                                                @if($isAdminOrKaprodi)
                                                    <input type="hidden" name="slot" value="all">
                                                @endif
                                                <button type="submit" 
                                                    title="{{ $hasAccSidang ? 'Batalkan ACC Sidang' : 'Berikan ACC Sidang' }}"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm
                                                    {{ $hasAccSidang ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}
                                                    {{ $mentoringCount < 8 && !$hasAccSidang ? 'opacity-75' : '' }}">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    ACC SIDANG
                                                    @if($mentoringCount < 8 && !$hasAccSidang)
                                                        <span class="ml-1.5 px-1.5 rounded bg-orange-100 text-orange-700 text-[9px]">{{ $mentoringCount }}/8</span>
                                                    @endif
                                                </button>
                                            </form>
                                            <div class="flex gap-1 border-l border-slate-200 dark:border-slate-700 pl-2 ml-1">
                                                <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_sidang_p1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P1"></div>
                                                <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_sidang_p2 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P2"></div>
                                            </div>
                                        </div>

                                        @if($studentThesis->isAccUpFinal())
                                            <div class="flex items-center px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-100 dark:border-emerald-500/20 text-[10px] font-black uppercase tracking-widest shadow-sm">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                UP FINAL ACC
                                            </div>
                                        @endif
                                        @if($studentThesis->isAccSidangFinal())
                                            <div class="flex items-center px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-100 dark:border-emerald-500/20 text-[10px] font-black uppercase tracking-widest shadow-sm">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                SIDANG FINAL ACC
                                            </div>
                                        @endif
                                    </div>
                                @elseif(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                                    <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest">
                                        Dosen Pembimbing: <span class="text-slate-700 dark:text-slate-300">{{ $studentSessions->first()->dosen->name }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($studentSessions as $session)
                                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-700/50 rounded-2xl p-5 relative overflow-hidden group hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-none hover:border-orange-200 dark:hover:border-orange-500/30 transition-all">
                                        <!-- Status Indicator -->
                                        <div class="absolute top-0 left-0 w-full h-1.5 
                                            {{ $session->status === 'pending' ? 'bg-amber-400' : '' }}
                                            {{ $session->status === 'approved' ? 'bg-orange-600' : '' }}
                                            {{ $session->status === 'rejected' || $session->is_absent ? 'bg-red-500' : '' }}
                                            {{ $session->status === 'completed' && !$session->is_absent ? 'bg-slate-300 dark:bg-slate-700' : '' }}
                                        "></div>
                                        
                                        <div class="flex justify-between items-start mb-5 mt-2">
                                            <div>
                                                <p class="text-[10px] text-orange-600 font-black uppercase tracking-widest">{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</p>
                                                <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">{{ $session->scheduled_at->format('H:i') }} WIB</p>
                                            </div>
                                            @if($session->is_absent)
                                                <x-status-badge type="red" label="TIDAK HADIR" />
                                            @else
                                                <x-status-badge 
                                                    :type="$session->status === 'pending' ? 'amber' : ($session->status === 'approved' ? 'orange' : ($session->status === 'rejected' ? 'red' : ($session->status === 'completed' ? 'emerald' : 'slate')))" 
                                                    :label="$session->status === 'completed' ? 'HADIR' : strtoupper($session->status)" />
                                            @endif
                                        </div>
                                        
                                        <div class="mb-5">
                                            <p class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight line-clamp-2 min-h-[2rem]">{{ $session->topic }}</p>
                                            
                                            <div class="mt-3 flex items-center text-[10px] font-black uppercase tracking-wider">
                                                @if($session->type === 'online')
                                                    @php 
                                                        $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                                                        $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                                                        $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                                                    @endphp
                                                    @if($session->location)
                                                        <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $isMeet ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/60' : ($isZoom ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/60' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100') }} transition-all shadow-2xs text-[10px] font-black uppercase tracking-wider">
                                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                            <span>{{ $isMeet ? '🎥 Google Meet' : ($isZoom ? '📹 Zoom' : 'Link Meeting') }}</span>
                                                        </a>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-600 border border-blue-100 dark:border-blue-500/20">Online</span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-50 dark:bg-slate-800 text-slate-500 mr-2 border border-slate-100 dark:border-slate-700">Offline</span>
                                                    @if($session->location)
                                                        <span class="text-slate-400 truncate max-w-[120px]" title="{{ $session->location }}">{{ $session->location }}</span>
                                                    @endif
                                                @endif
                                            </div>

                                            @if($session->notes)
                                                <div class="mt-4 pl-3 border-l-2 border-slate-100 dark:border-slate-700">
                                                    <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Catatan Mahasiswa</div>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 italic line-clamp-2">"{{ $session->notes }}"</p>
                                                </div>
                                            @endif
                                            
                                            @if($session->feedback)
                                                <div class="mt-4">
                                                    <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Hasil Bimbingan</div>
                                                    <p class="text-[11px] text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 p-2.5 rounded-xl font-medium italic">"{{ $session->feedback }}"</p>
                                                </div>
                                            @endif

                                            @if($session->document_path)
                                                <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-800/50">
                                                    <a href="{{ $session->document_path }}" target="_blank"
                                                       class="flex items-center gap-3 p-2 bg-indigo-50/50 dark:bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/10 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all group/doc">
                                                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 flex items-center justify-center text-indigo-500 border border-indigo-100 dark:border-indigo-500/20 group-hover/doc:scale-110 transition-transform">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-[10px] font-black text-indigo-700 dark:text-indigo-400 truncate uppercase tracking-tighter">{{ $session->document_original_name }}</p>
                                                            <p class="text-[9px] text-indigo-400 dark:text-indigo-500 font-bold uppercase">Buka Dokumen</p>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif

                                            <!-- Konfirmasi Kehadiran Mahasiswa -->
                                            @if(!in_array($session->status, ['completed', 'rejected']))
                                                <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800/80 space-y-2">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Kehadiran Mhs:</span>
                                                        @if($session->student_attendance_status === 'attending')
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 text-[9px] font-black uppercase tracking-wider">
                                                                <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                Akan Hadir
                                                            </span>
                                                        @elseif($session->student_attendance_status === 'permission')
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 text-[9px] font-black uppercase tracking-wider">
                                                                <svg class="w-2.5 h-2.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"></path></svg>
                                                                Izin / Berhalangan
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 text-[9px] font-black uppercase tracking-wider">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                                Menunggu Konfirmasi
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($session->student_attendance_status === 'permission' && $session->student_attendance_reason)
                                                        <div class="p-2.5 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-800/50 rounded-xl space-y-1">
                                                            <p class="text-[9px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-tighter">Alasan Izin Mahasiswa:</p>
                                                            <p class="text-[11px] text-amber-900 dark:text-amber-200 font-medium italic leading-relaxed">"{{ $session->student_attendance_reason }}"</p>
                                                            @if($session->student_confirmed_at)
                                                                <p class="text-[8px] text-amber-700/70 dark:text-amber-400/60 text-right">{{ $session->student_confirmed_at->locale('id')->translatedFormat('d M H:i') }} WIB</p>
                                                            @endif
                                                        </div>
                                                    @elseif($session->student_attendance_status === 'attending' && $session->student_confirmed_at)
                                                        <p class="text-[9px] text-slate-400 dark:text-slate-500 text-right font-medium">Dikonfirmasi: {{ $session->student_confirmed_at->locale('id')->translatedFormat('d M H:i') }} WIB</p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if($session->status === 'pending')
                                        <div class="flex items-center space-x-2 mt-auto pt-4 border-t border-slate-50 dark:border-slate-800/50">
                                            <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="w-full px-3 py-2 bg-orange-600 text-white hover:bg-orange-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Terima</button>
                                            </form>
                                            @can('update', $session)
                                                <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-3 py-2 bg-slate-100 hover:bg-orange-50 dark:bg-slate-800 dark:hover:bg-orange-950/40 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 dark:border-orange-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    <span>Ubah</span>
                                                </a>
                                            @endcan
                                            <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 hover:border-red-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Tolak</button>
                                            </form>
                                        </div>
                                        @elseif($session->status === 'approved')
                                        <div class="mt-auto pt-4 border-t border-slate-50 dark:border-slate-800/50" x-data="{ showFeedback: false }">
                                            <div class="flex items-center gap-2" x-show="!showFeedback">
                                                <button type="button" @click="showFeedback = true" class="flex-1 px-3 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Selesai</button>
                                                @can('update', $session)
                                                    <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-3 py-2 bg-slate-100 hover:bg-orange-50 dark:bg-slate-800 dark:hover:bg-orange-950/40 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 dark:hover:border-orange-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs inline-flex items-center gap-1" title="Ubah / Reschedule Jadwal">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        <span>Ubah</span>
                                                    </a>
                                                @endcan
                                                <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="absent">
                                                    <button type="submit" class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 hover:border-red-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Absen</button>
                                                </form>
                                            </div>
                                            <div x-show="showFeedback" x-cloak class="mt-2" x-transition>
                                                <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <textarea name="feedback" rows="3" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-3" placeholder="Catatan hasil bimbingan..."></textarea>
                                                    <div class="flex space-x-2">
                                                        <button type="submit" class="flex-1 px-3 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Simpan</button>
                                                        <button type="button" @click="showFeedback = false" class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        @if($activeTab === 'history')
                            <x-empty-state description="Belum ada riwayat bimbingan untuk mahasiswa yang sudah lulus." icon="mentoring" />
                        @else
                            <x-empty-state description="Belum ada jadwal bimbingan aktif." icon="mentoring" />
                        @endif
                    @endforelse
                    </div>
                </div>

                <!-- 2. CALENDAR VIEW -->
                <div x-show="viewMode === 'calendar'" x-cloak x-transition class="space-y-4">
                    <!-- Legend Indicators -->
                    <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-100 dark:border-slate-700/60">
                        <div class="flex flex-wrap items-center gap-4 text-xs font-bold">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></span>
                                <span class="text-slate-600 dark:text-slate-400 text-[11px]">Selesai / Hadir</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-600 shadow-[0_0_8px_rgba(234,88,12,0.4)]"></span>
                                <span class="text-slate-600 dark:text-slate-400 text-[11px]">Dijadwalkan (Aktif)</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.4)]"></span>
                                <span class="text-slate-600 dark:text-slate-400 text-[11px]">Menunggu Konfirmasi</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.4)]"></span>
                                <span class="text-slate-600 dark:text-slate-400 text-[11px]">Ditolak / Absen</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">💡 Klik pada jadwal untuk melihat rincian & aksi cepat</span>
                    </div>

                    <!-- Calendar Container -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-100 dark:border-slate-700/60 min-h-[600px]">
                        <div id="mentoring-calendar"></div>
                    </div>
                </div>
            </div>
        </x-table-card>

        <!-- Interactive Event Modal -->
        <div x-show="eventModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 max-w-lg w-full overflow-hidden"
                 @click.outside="eventModalOpen = false">
                
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-2xs bg-orange-50 shrink-0">
                            <template x-if="selectedEvent?.student_avatar">
                                <img :src="selectedEvent.student_avatar" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!selectedEvent?.student_avatar">
                                <span class="font-black text-xs text-orange-600" x-text="selectedEvent?.student_name?.charAt(0) || 'M'"></span>
                            </template>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase" x-text="selectedEvent?.student_name"></h4>
                            <p class="text-[10px] text-slate-400 font-bold" x-text="'NPM: ' + (selectedEvent?.student_npm || '-')"></p>
                        </div>
                    </div>
                    <button type="button" @click="eventModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4 text-xs">
                    <!-- Date & Time Banner -->
                    <div class="flex items-center justify-between p-3.5 bg-orange-50/60 dark:bg-orange-950/20 border border-orange-200/60 dark:border-orange-900/40 rounded-xl">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <div>
                                <p class="font-black text-slate-800 dark:text-slate-200" x-text="selectedEvent?.date"></p>
                                <p class="text-[10px] text-orange-600 dark:text-orange-400 font-bold" x-text="selectedEvent?.time"></p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider"
                              :class="{
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300': selectedEvent?.status === 'completed',
                                'bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-300': selectedEvent?.status === 'approved',
                                'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300': selectedEvent?.status === 'pending',
                                'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300': selectedEvent?.status === 'rejected' || selectedEvent?.is_absent,
                              }"
                              x-text="selectedEvent?.is_absent ? 'TIDAK HADIR' : (selectedEvent?.status === 'completed' ? 'HADIR / SELESAI' : selectedEvent?.status)">
                        </span>
                    </div>

                    <!-- Topic -->
                    <div class="space-y-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Topik Pembahasan:</span>
                        <p class="font-black text-sm text-slate-800 dark:text-slate-100" x-text="selectedEvent?.topic"></p>
                    </div>

                    <!-- Location / GMeet Link -->
                    <div class="space-y-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Metode & Lokasi:</span>
                        <div>
                            <template x-if="selectedEvent?.type === 'online' && selectedEvent?.location">
                                <a :href="selectedEvent.location.startsWith('http') ? selectedEvent.location : 'https://' + selectedEvent.location" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl font-bold hover:bg-emerald-100 transition-all text-xs">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>🎥 Buka Link Google Meet / Meeting</span>
                                </a>
                            </template>
                            <template x-if="selectedEvent?.type === 'offline' || !selectedEvent?.location">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">
                                    <span>🏢 Tatap Muka Langsung</span>
                                    <span x-text="selectedEvent?.location ? '(' + selectedEvent.location + ')' : ''"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Student Attendance Status in Calendar Modal -->
                    <template x-if="selectedEvent?.student_attendance_status && selectedEvent?.status !== 'completed'">
                        <div class="p-3.5 rounded-xl border space-y-1.5"
                             :class="{
                                 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800': selectedEvent.student_attendance_status === 'attending',
                                 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800': selectedEvent.student_attendance_status === 'permission',
                                 'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700': selectedEvent.student_attendance_status === 'pending'
                             }">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Konfirmasi Kehadiran Mahasiswa:</span>
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider"
                                      :class="{
                                          'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200': selectedEvent.student_attendance_status === 'attending',
                                          'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200': selectedEvent.student_attendance_status === 'permission',
                                          'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300': selectedEvent.student_attendance_status === 'pending'
                                      }"
                                      x-text="selectedEvent.student_attendance_status === 'attending' ? '🟢 Akan Hadir' : (selectedEvent.student_attendance_status === 'permission' ? '🟡 Izin / Berhalangan' : '⚪ Menunggu Konfirmasi')">
                                </span>
                            </div>
                            <template x-if="selectedEvent.student_attendance_status === 'permission' && selectedEvent.student_attendance_reason">
                                <div class="mt-1">
                                    <span class="text-[9px] font-bold text-amber-800 dark:text-amber-300">Alasan Izin:</span>
                                    <p class="text-[11px] text-amber-950 dark:text-amber-200 italic font-medium leading-relaxed" x-text="'&ldquo;' + selectedEvent.student_attendance_reason + '&rdquo;'"></p>
                                </div>
                            </template>
                            <template x-if="selectedEvent.student_confirmed_at">
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 text-right font-medium" x-text="'Dikonfirmasi: ' + selectedEvent.student_confirmed_at"></p>
                            </template>
                        </div>
                    </template>

                    <!-- Student Notes -->
                    <template x-if="selectedEvent?.notes">
                        <div class="space-y-1 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Catatan Pengajuan Mahasiswa:</span>
                            <p class="text-slate-600 dark:text-slate-300 italic" x-text="'&ldquo;' + selectedEvent.notes + '&rdquo;'"></p>
                        </div>
                    </template>

                    <!-- Lecturer Feedback -->
                    <template x-if="selectedEvent?.feedback">
                        <div class="space-y-1 p-3 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-xl border border-emerald-100 dark:border-emerald-900/30">
                            <span class="text-[9px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">Hasil / Catatan Bimbingan Dosen:</span>
                            <p class="text-emerald-900 dark:text-emerald-200 font-medium italic" x-text="'&ldquo;' + selectedEvent.feedback + '&rdquo;'"></p>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 px-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3">
                    <div>
                        <template x-if="selectedEvent?.status !== 'completed' && selectedEvent?.id">
                            <a :href="'/mentoring-sessions/' + selectedEvent.id + '/edit'" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/40 dark:hover:bg-orange-900/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/80 rounded-xl font-bold text-xs transition-all shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                <span>Ubah / Reschedule</span>
                            </a>
                        </template>
                    </div>
                    <button type="button" @click="eventModalOpen = false" class="px-5 py-2 bg-slate-800 dark:bg-white text-white dark:text-slate-800 font-bold rounded-xl text-xs hover:bg-slate-900 dark:hover:bg-slate-100 transition-all shadow-xs cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. LIVE REAL-TIME ATTENDANCE MONITOR MODAL -->
        <div x-show="liveModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="liveModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     @click="liveModalOpen = false" 
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="liveModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-slate-100 dark:border-slate-700">
                    
                    <!-- Modal Header -->
                    <div class="p-6 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-sm shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 tracking-tight">Monitor Kehadiran Mahasiswa</h3>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Real-Time Sync
                                    </span>
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
                            <button type="button" 
                                    @click="liveTab = 'attending'" 
                                    :class="liveTab === 'attending' ? 'ring-2 ring-emerald-500 bg-emerald-50/80 dark:bg-emerald-950/40 shadow-xs' : 'bg-slate-50 dark:bg-slate-900/40 hover:bg-emerald-50/30'"
                                    class="p-4 rounded-2xl border border-emerald-200/80 dark:border-emerald-900/50 text-left transition-all cursor-pointer">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Akan Hadir</span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                </div>
                                <div class="text-2xl font-black text-emerald-950 dark:text-emerald-100 mt-1.5" x-text="attendanceStats.attending || 0">0</div>
                                <p class="text-[10px] text-emerald-700/80 dark:text-emerald-400/80 font-medium mt-0.5">Mahasiswa siap hadir</p>
                            </button>

                            <button type="button" 
                                    @click="liveTab = 'permission'" 
                                    :class="liveTab === 'permission' ? 'ring-2 ring-amber-500 bg-amber-50/80 dark:bg-amber-950/40 shadow-xs' : 'bg-slate-50 dark:bg-slate-900/40 hover:bg-amber-50/30'"
                                    class="p-4 rounded-2xl border border-amber-200/80 dark:border-amber-900/50 text-left transition-all cursor-pointer">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400">Izin / Berhalangan</span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                                </div>
                                <div class="text-2xl font-black text-amber-950 dark:text-amber-100 mt-1.5" x-text="attendanceStats.permission || 0">0</div>
                                <p class="text-[10px] text-amber-700/80 dark:text-amber-400/80 font-medium mt-0.5">Dengan alasan izin</p>
                            </button>

                            <button type="button" 
                                    @click="liveTab = 'pending'" 
                                    :class="liveTab === 'pending' ? 'ring-2 ring-slate-500 bg-slate-100 dark:bg-slate-700/50 shadow-xs' : 'bg-slate-50 dark:bg-slate-900/40 hover:bg-slate-100'"
                                    class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 text-left transition-all cursor-pointer">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Belum Konfirmasi</span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400 animate-pulse"></span>
                                </div>
                                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1.5" x-text="attendanceStats.pending || 0">0</div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Menunggu respon mahasiswa</p>
                            </button>
                        </div>

                        <!-- Filter Tabs & Search in Modal -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-1">
                            <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto p-1 bg-slate-100 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                                <button type="button" 
                                        @click="liveTab = 'all'" 
                                        :class="liveTab === 'all' ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 shadow-2xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                                        class="px-3 py-1 rounded-lg text-xs font-bold transition-all whitespace-nowrap cursor-pointer">
                                    Semua (<span x-text="attendanceStats.total || 0"></span>)
                                </button>
                                <button type="button" 
                                        @click="liveTab = 'attending'" 
                                        :class="liveTab === 'attending' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40'"
                                        class="px-3 py-1 rounded-lg text-xs font-bold transition-all whitespace-nowrap cursor-pointer">
                                    Akan Hadir (<span x-text="attendanceStats.attending || 0"></span>)
                                </button>
                                <button type="button" 
                                        @click="liveTab = 'permission'" 
                                        :class="liveTab === 'permission' ? 'bg-amber-600 text-white shadow-2xs' : 'text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40'"
                                        class="px-3 py-1 rounded-lg text-xs font-bold transition-all whitespace-nowrap cursor-pointer">
                                    Izin (<span x-text="attendanceStats.permission || 0"></span>)
                                </button>
                                <button type="button" 
                                        @click="liveTab = 'pending'" 
                                        :class="liveTab === 'pending' ? 'bg-slate-700 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                                        class="px-3 py-1 rounded-lg text-xs font-bold transition-all whitespace-nowrap cursor-pointer">
                                    Belum Konfirmasi (<span x-text="attendanceStats.pending || 0"></span>)
                                </button>
                            </div>

                            <div class="w-full sm:w-64">
                                <input type="text" 
                                       x-model="liveSearch" 
                                       placeholder="Cari nama / topik..." 
                                       class="w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-1.5 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>

                        <!-- Students List in Modal -->
                        <div class="space-y-2.5">
                            <template x-for="item in filteredLiveSessions" :key="item.id">
                                <div class="p-4 rounded-2xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                     :class="{
                                         'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200/70 dark:border-emerald-900/40': item.attendance_status === 'attending',
                                         'bg-amber-50/40 dark:bg-amber-950/20 border-amber-200/70 dark:border-amber-900/40': item.attendance_status === 'permission',
                                         'bg-white dark:bg-slate-900/60 border-slate-200/80 dark:border-slate-700/80': item.attendance_status === 'pending'
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
                                                 x-text="item.student_name ? item.student_name.substring(0, 2).toUpperCase() : 'MH'">
                                            </div>
                                        </template>
                                        
                                        <div class="min-w-0 flex-1 space-y-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="text-xs font-black text-slate-800 dark:text-slate-100" x-text="item.student_name"></h4>
                                                <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold" x-text="item.student_identifier"></span>
                                                <template x-if="item.is_today">
                                                    <span class="px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300 text-[9px] font-black uppercase">Hari Ini</span>
                                                </template>
                                            </div>
                                            <p class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 truncate" x-text="item.topic"></p>
                                            <div class="flex items-center gap-3 text-[10px] text-slate-500 dark:text-slate-400">
                                                <span class="font-bold text-slate-700 dark:text-slate-300" x-text="item.scheduled_date_formatted + ' • ' + item.scheduled_time_formatted"></span>
                                                <span x-text="item.type === 'online' ? '🎥 Daring' : '🏢 Tatap Muka'"></span>
                                                <template x-if="item.dosen_name">
                                                    <span class="text-slate-400" x-text="'Dosen: ' + item.dosen_name"></span>
                                                </template>
                                            </div>

                                            <!-- Permission Reason Callout -->
                                            <template x-if="item.attendance_status === 'permission' && item.attendance_reason">
                                                <div class="mt-2 p-2 bg-amber-100/70 dark:bg-amber-950/50 rounded-xl border border-amber-200 dark:border-amber-800/80">
                                                    <span class="text-[9px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider block">Alasan Izin Mahasiswa:</span>
                                                    <p class="text-[11px] text-amber-950 dark:text-amber-200 font-medium italic" x-text="'&ldquo;' + item.attendance_reason + '&rdquo;'"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Status Badge & Quick Actions -->
                                    <div class="flex sm:flex-col items-end justify-between sm:justify-center gap-2 shrink-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100 dark:border-slate-800">
                                        <template x-if="item.attendance_status === 'attending'">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[10px] font-black uppercase tracking-wider">
                                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Akan Hadir</span>
                                            </span>
                                        </template>
                                        <template x-if="item.attendance_status === 'permission'">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[10px] font-black uppercase tracking-wider">
                                                <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <span>Izin / Berhalangan</span>
                                            </span>
                                        </template>
                                        <template x-if="item.attendance_status === 'pending'">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                                                <span>Belum Respon</span>
                                            </span>
                                        </template>

                                        <template x-if="item.confirmed_at_formatted">
                                            <span class="text-[9px] text-slate-400 dark:text-slate-500 font-medium" x-text="item.confirmed_at_formatted"></span>
                                        </template>

                                        <!-- WhatsApp Reminder Button for Pending / Permission -->
                                        <template x-if="item.student_phone && (item.attendance_status === 'pending' || item.attendance_status === 'permission')">
                                            <a :href="'https://wa.me/' + item.student_phone.replace(/^0/, '62') + '?text=' + encodeURIComponent('Halo ' + item.student_name + ', pengingat jadwal bimbingan SIBIMA pada ' + item.scheduled_date_formatted + ' pukul ' + item.scheduled_time_formatted + '. Topik: ' + item.topic + '.')" 
                                               target="_blank" 
                                               class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-lg text-[10px] font-bold transition-all">
                                                <span>📲 Chat WA</span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="filteredLiveSessions.length === 0">
                                <div class="p-8 text-center bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-200/60 dark:border-slate-800">
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Tidak ada mahasiswa yang sesuai dengan filter kehadiran ini.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 px-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Live Sync Otomatis Aktif (tiap 10 detik) • Terakhir: <strong x-text="lastUpdated"></strong></span>
                        </div>
                        <button type="button" @click="liveModalOpen = false" class="px-5 py-2 bg-slate-800 dark:bg-white text-white dark:text-slate-800 font-bold rounded-xl text-xs hover:bg-slate-900 dark:hover:bg-slate-100 transition-all shadow-xs cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
    <style>
        :root {
            --fc-border-color: #f1f5f9;
            --fc-daygrid-event-dot-width: 8px;
        }
        .dark {
            --fc-border-color: #334155;
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: #1e293b;
            --fc-list-event-hover-bg-color: #334155;
        }
        .fc {
            font-family: 'Inter', sans-serif;
        }
        .fc .fc-toolbar-title {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: -0.025em;
            color: #1e293b;
        }
        .dark .fc .fc-toolbar-title {
            color: #f1f5f9;
        }
        .fc .fc-button {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0.4rem 0.8rem;
            border-radius: 0.75rem;
            transition: all 0.2s;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .fc .fc-button {
            background-color: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }
        .fc .fc-button:hover {
            background-color: #f8fafc;
            color: #1e293b;
        }
        .dark .fc .fc-button:hover {
            background-color: #334155;
            color: #f1f5f9;
        }
        .fc .fc-button-active {
            background-color: #ea580c !important;
            border-color: #ea580c !important;
            color: #ffffff !important;
        }
        .fc-event {
            cursor: pointer;
            border-radius: 6px;
            padding: 2px 4px;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        function mentoringSchedule() {
            return {
                viewMode: '{{ request('view', 'cards') }}',
                selectedEvent: null,
                eventModalOpen: false,
                liveModalOpen: false,
                liveTab: 'all',
                liveSearch: '',
                isSyncing: false,
                lastUpdated: '{{ now()->locale('id')->translatedFormat('H:i:s') }} WIB',
                attendanceStats: @json($attendanceStats),
                liveSessions: [],
                calendarInitialized: false,
                events: @json($calendarEvents ?? []),
                init() {
                    if (this.viewMode === 'calendar') {
                        this.initCalendar();
                    }
                    this.fetchLiveAttendance(true);
                    setInterval(() => {
                        this.fetchLiveAttendance(true);
                    }, 10000);
                },
                async fetchLiveAttendance(silent = false) {
                    if (!silent) this.isSyncing = true;
                    try {
                        const dosenId = '{{ $dosenId ?? '' }}';
                        const url = '{{ route('mentoring-sessions.live-attendance') }}' + (dosenId ? '?dosen_id=' + dosenId : '');
                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (data.summary) {
                                this.attendanceStats = data.summary;
                                this.lastUpdated = data.summary.last_updated || this.lastUpdated;
                            }
                            if (data.sessions) {
                                this.liveSessions = data.sessions;
                            }
                        }
                    } catch (e) {
                        console.error('Live attendance sync error:', e);
                    } finally {
                        if (!silent) this.isSyncing = false;
                    }
                },
                openLiveModal() {
                    this.liveModalOpen = true;
                    this.fetchLiveAttendance(false);
                },
                get filteredLiveSessions() {
                    return this.liveSessions.filter(item => {
                        const matchesTab = (this.liveTab === 'all') || (item.attendance_status === this.liveTab);
                        const searchLower = this.liveSearch.toLowerCase();
                        const matchesSearch = !this.liveSearch || 
                            (item.student_name && item.student_name.toLowerCase().includes(searchLower)) ||
                            (item.student_identifier && item.student_identifier.toLowerCase().includes(searchLower)) ||
                            (item.topic && item.topic.toLowerCase().includes(searchLower));
                        return matchesTab && matchesSearch;
                    });
                },
                initCalendar() {
                    if (this.calendarInitialized) return;
                    this.$nextTick(() => {
                        const calendarEl = document.getElementById('mentoring-calendar');
                        if (!calendarEl || typeof FullCalendar === 'undefined') return;
                        
                        const calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth',
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,listMonth'
                            },
                            buttonText: {
                                today: 'Hari Ini',
                                month: 'Bulan',
                                week: 'Minggu',
                                list: 'Agenda'
                            },
                            locale: 'id',
                            events: this.events,
                            eventClick: (info) => {
                                this.selectedEvent = info.event.extendedProps;
                                this.eventModalOpen = true;
                            },
                            eventTimeFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                meridiem: false,
                                hour12: false
                            },
                            height: 'auto',
                            contentHeight: 650,
                            dayMaxEvents: 3
                        });
                        calendar.render();
                        this.calendarInitialized = true;
                    });
                },
                switchView(mode) {
                    this.viewMode = mode;
                    if (mode === 'calendar') {
                        this.initCalendar();
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

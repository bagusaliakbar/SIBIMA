<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Sidang', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6" x-data="{ allOpen: true, openImportModal: false }">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-xs font-bold flex items-center border border-emerald-100 dark:border-emerald-800/50 shadow-sm animate-pulse">
                <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 text-xs font-bold flex items-center border border-rose-100 dark:border-rose-800/50 shadow-sm">
                <svg class="w-5 h-5 mr-3 text-rose-600 dark:text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if(session('skippedDetails') && count(session('skippedDetails')) > 0)
            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 shadow-sm text-xs">
                <div class="flex items-center gap-2 font-bold text-amber-800 dark:text-amber-300 mb-2">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Perhatian: Beberapa baris data sesi dilewati (tidak diimpor):</span>
                </div>
                <div class="max-h-40 overflow-y-auto space-y-1 pl-6">
                    @foreach(session('skippedDetails') as $detail)
                        <div class="text-[11px] text-amber-900 dark:text-amber-200 list-item">
                            @if(isset($detail['row']) && $detail['row'] !== '-')
                                <span class="font-bold">Baris {{ $detail['row'] }}:</span>
                            @endif
                            {{ $detail['reason'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(Auth::user()->role === 'dosen')
            <!-- DOSEN VIEW: Standard Top Status Tabs -->
            <div class="flex items-center gap-1 border-b border-slate-200 dark:border-slate-700 overflow-x-auto pb-px custom-scrollbar">
                <!-- Upcoming Tab -->
                <a href="{{ route('thesis-defense-schedules.index', array_merge(request()->except(['page', 'filter_date', 'date', 'date_from', 'date_to', 'my_schedules']), ['filter_date' => 'upcoming', 'wave_id' => $selectedWaveId])) }}"
                   class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($filterDate ?? 'upcoming') === 'upcoming' && !$mySchedules ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <span class="w-2 h-2 rounded-full {{ ($filterDate ?? 'upcoming') === 'upcoming' && !$mySchedules ? 'bg-orange-500 animate-pulse' : 'bg-slate-400' }}"></span>
                    Mendatang
                    <span class="px-2 py-0.5 text-[10px] rounded-full {{ ($filterDate ?? 'upcoming') === 'upcoming' && !$mySchedules ? 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $counts['upcoming'] }}</span>
                </a>

                <!-- Today Tab -->
                <a href="{{ route('thesis-defense-schedules.index', array_merge(request()->except(['page', 'filter_date', 'date', 'date_from', 'date_to', 'my_schedules']), ['filter_date' => 'today', 'wave_id' => $selectedWaveId])) }}"
                   class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ $filterDate === 'today' && !$mySchedules ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <span class="w-2 h-2 rounded-full {{ $filterDate === 'today' && !$mySchedules ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                    Hari Ini
                    <span class="px-2 py-0.5 text-[10px] rounded-full {{ $filterDate === 'today' && !$mySchedules ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $counts['today'] }}</span>
                </a>

                <!-- Past Tab -->
                <a href="{{ route('thesis-defense-schedules.index', array_merge(request()->except(['page', 'filter_date', 'date', 'date_from', 'date_to', 'my_schedules']), ['filter_date' => 'past', 'wave_id' => $selectedWaveId])) }}"
                   class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ $filterDate === 'past' && !$mySchedules ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Riwayat
                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">{{ $counts['past'] }}</span>
                </a>

                <!-- All Tab -->
                <a href="{{ route('thesis-defense-schedules.index', array_merge(request()->except(['page', 'filter_date', 'date', 'date_from', 'date_to', 'my_schedules']), ['filter_date' => 'all', 'wave_id' => $selectedWaveId])) }}"
                   class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ $filterDate === 'all' && !$mySchedules ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Semua Agenda
                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">{{ $counts['all'] }}</span>
                </a>

                <!-- "Jadwal Saya" Tab -->
                <a href="{{ route('thesis-defense-schedules.index', array_merge(request()->except(['page', 'my_schedules', 'filter_date', 'date', 'date_from', 'date_to']), ['my_schedules' => '1', 'filter_date' => 'all', 'wave_id' => $selectedWaveId])) }}"
                   class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ $mySchedules ? 'border-amber-500 text-amber-700 dark:text-amber-400 bg-amber-50/60 dark:bg-amber-500/10 font-bold' : 'border-transparent text-amber-600 dark:text-amber-400 hover:text-amber-700 hover:bg-amber-50/40 dark:hover:bg-slate-800' }}"
                   title="Hanya tampilkan jadwal di mana Anda menjadi Penguji, Pembimbing, Ketua Sidang, atau Moderator">
                    <svg class="w-3.5 h-3.5" fill="{{ $mySchedules ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    Jadwal Saya
                    <span class="px-2 py-0.5 text-[10px] rounded-full {{ $mySchedules ? 'bg-amber-200 dark:bg-amber-500/20 text-amber-900 dark:text-amber-300 font-black' : 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400' }}">{{ $counts['mySchedules'] }}</span>
                </a>
            </div>

            <!-- DOSEN VIEW: Compact Single-Line Filter Toolbar -->
            <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-xs flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                <form action="{{ route('thesis-defense-schedules.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2.5 w-full lg:w-auto flex-1">
                    @if($filterDate && $filterDate !== 'custom')
                        <input type="hidden" name="filter_date" value="{{ $filterDate }}">
                    @endif
                    @if($mySchedules)
                        <input type="hidden" name="my_schedules" value="1">
                    @endif

                    <!-- Search Input -->
                    <div class="relative w-full sm:flex-1 sm:min-w-[200px] sm:max-w-md">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Cari mahasiswa, judul, dosen, ruangan..."
                                class="w-full pl-10 pr-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                    </div>

                    <!-- Wave Select Dropdown -->
                    <select name="wave_id" onchange="this.form.submit()"
                            class="w-full sm:w-auto py-2 pl-3 pr-8 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all sm:min-w-[170px]">
                        @if(auth()->user()->role !== 'dosen')
                            <option value="">Semua Gelombang</option>
                        @endif
                        @foreach($waves as $wave)
                            <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                {{ strtoupper($wave->name) }} {{ $wave->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Compact Inline Date Range -->
                    <div class="flex items-center justify-between sm:justify-start gap-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-1 w-full sm:w-auto">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Tgl:</span>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" title="Dari Tanggal"
                               class="border-0 bg-transparent p-0 text-xs font-semibold text-slate-800 dark:text-slate-100 dark:bg-transparent focus:ring-0 w-[110px] sm:w-[115px] [color-scheme:light] dark:[color-scheme:dark]">
                        <span class="text-slate-400 dark:text-slate-500 text-xs font-bold">-</span>
                        <input type="date" name="date_to" value="{{ $dateTo }}" title="Sampai Tanggal"
                               class="border-0 bg-transparent p-0 text-xs font-semibold text-slate-800 dark:text-slate-100 dark:bg-transparent focus:ring-0 w-[110px] sm:w-[115px] [color-scheme:light] dark:[color-scheme:dark]">
                    </div>

                    <!-- Filter Action Buttons Container -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-initial justify-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-xs flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filter
                        </button>

                        @if(request()->hasAny(['search', 'wave_id', 'date', 'date_from', 'date_to', 'my_schedules']) || ($filterDate && $filterDate !== 'upcoming'))
                            <a href="{{ route('thesis-defense-schedules.index') }}" class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-xl transition-all" title="Reset Filter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Right Side: Controls & Batch PDF -->
                <div class="flex items-center gap-2.5 shrink-0 border-t lg:border-t-0 pt-2 lg:pt-0 w-full lg:w-auto justify-end flex-wrap">
                    <a href="{{ route('thesis-defense-schedules.export-batch-pdf', array_filter(['wave_id' => $selectedWaveId, 'date' => request('date'), 'my_schedules' => $mySchedules ? 1 : null])) }}" 
                       class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2 px-4 py-2.5 bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 shadow-2xs transition-all whitespace-nowrap"
                       title="{{ $mySchedules ? 'Cetak ringkasan seluruh jadwal bimbingan dan uji Anda dalam 1 file PDF' : 'Cetak seluruh ruangan dalam 1 file PDF' }}">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>{{ $mySchedules ? 'Cetak Ringkasan (PDF)' : 'Cetak Semua (PDF)' }}</span>
                    </a>
                    <button type="button" @click="allOpen = true" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2.5 px-5 py-2.5 bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 shadow-2xs transition-all whitespace-nowrap">
                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        <span>Buka Semua</span>
                    </button>
                    <button type="button" @click="allOpen = false" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2.5 px-5 py-2.5 bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 shadow-2xs transition-all whitespace-nowrap">
                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path></svg>
                        <span>Tutup Semua</span>
                    </button>
                </div>
            </div>

            <!-- Schedule Cards Grid / List -->
            <div class="space-y-4">
                @forelse($schedules as $schedule)
                    @php
                        $currentUserId = Auth::id();
                        $myRoles = [];
                        if ($schedule->chairman_id === $currentUserId) $myRoles[] = 'Ketua Sidang';
                        if ($schedule->moderator_id === $currentUserId) $myRoles[] = 'Moderator';

                        $isExaminer = $schedule->details->contains(function($d) use ($currentUserId) {
                            return $d->examiner1_id === $currentUserId || $d->examiner2_id === $currentUserId;
                        });
                        if ($isExaminer) $myRoles[] = 'Penguji';

                        $isAdvisor = $schedule->details->contains(function($d) use ($currentUserId) {
                            return $d->thesis && ($d->thesis->pembimbing1_id === $currentUserId || $d->thesis->pembimbing2_id === $currentUserId);
                        });
                        if ($isAdvisor) $myRoles[] = 'Pembimbing';

                        $scheduleDate = \Carbon\Carbon::parse($schedule->date);
                        $isToday = $scheduleDate->isToday();
                        $isPast = $scheduleDate->isPast() && !$isToday;
                        $isUpcoming = $scheduleDate->isFuture();
                        $studentCount = $schedule->details->whereNotNull('thesis_id')->count();
                    @endphp

                    <div x-data="{ open: true }" x-init="$watch('allOpen', value => open = value)"
                         class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden transition-all">
                        
                        <!-- Header Banner -->
                        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/60">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-2 flex-1 min-w-0">
                                    <!-- Badges Row -->
                                    <div class="flex flex-wrap items-center gap-2">
                                        <!-- Date Badge -->
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-semibold border border-slate-200/90 dark:border-slate-700 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $scheduleDate->locale('id')->translatedFormat('l, d F Y') }}
                                        </span>

                                        <!-- Status Badge -->
                                        @if($isToday)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 rounded-lg text-xs font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Hari Ini
                                            </span>
                                        @elseif($isUpcoming)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 rounded-lg text-xs font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Mendatang
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Selesai
                                            </span>
                                        @endif

                                        <!-- User Role Badge -->
                                        @if(count($myRoles) > 0)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60 rounded-lg text-xs font-semibold">
                                                <svg class="w-3.5 h-3.5 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                Peran Anda: {{ implode(' & ', $myRoles) }}
                                            </span>
                                        @endif

                                        <!-- Student Count Badge -->
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200/90 dark:border-slate-700 rounded-lg text-xs font-medium shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            {{ $studentCount }} {{ $mySchedules ? 'Mahasiswa Bimbingan/Uji' : 'Mahasiswa' }}
                                        </span>
                                    </div>

                                    <!-- Session Title -->
                                    <h3 class="text-sm sm:text-base font-black tracking-tight text-slate-800 dark:text-slate-100 uppercase break-words">
                                        {{ $schedule->title }}
                                    </h3>
                                </div>

                                <!-- Header Action Buttons -->
                                <div class="flex items-center justify-between sm:justify-end gap-2.5 shrink-0 w-full sm:w-auto pt-1 sm:pt-0">
                                    <a href="{{ route('thesis-defense-schedules.export-pdf', array_filter(['thesis_defense_schedule' => $schedule->id, 'my_schedules' => $mySchedules ? 1 : null])) }}" 
                                       class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2.5 px-5 py-2.5 bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold shadow-2xs transition-all whitespace-nowrap shrink-0"
                                       title="{{ $mySchedules ? 'Cetak ringkasan jadwal ini' : 'Cetak PDF' }}">
                                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span>{{ $mySchedules ? 'Cetak Ringkasan' : 'Cetak PDF' }}</span>
                                    </a>

                                    <button type="button" @click="open = !open" 
                                            class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2.5 px-5 py-2.5 bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700 shadow-2xs whitespace-nowrap shrink-0"
                                            title="Buka/Tutup Rincian">
                                        <span x-text="open ? 'Tutup' : 'Rincian'"></span>
                                        <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-500 dark:text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Meta Info Chips Bar (Clear & Readable) -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-3 mt-3 border-t border-slate-200 dark:border-slate-700 text-xs">
                                <div class="px-2.5 sm:px-3 py-2 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700 min-w-0">
                                    <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Ketua Sidang</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate block text-xs" title="{{ $schedule->chairman?->name ?? '-' }}">{{ $schedule->chairman?->name ?? '-' }}</span>
                                </div>
                                <div class="px-2.5 sm:px-3 py-2 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700 min-w-0">
                                    <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Moderator</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate block text-xs" title="{{ $schedule->moderator?->name ?? '-' }}">{{ $schedule->moderator?->name ?? '-' }}</span>
                                </div>
                                <div class="px-2.5 sm:px-3 py-2 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700 min-w-0">
                                    <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Ruangan</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate block text-xs" title="{{ $schedule->location ?: 'BELUM DIATUR' }}">{{ $schedule->location ?: 'BELUM DIATUR' }}</span>
                                </div>
                                <div class="px-2.5 sm:px-3 py-2 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700 min-w-0">
                                    <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Virtual Meeting</span>
                                    @if($schedule->meeting_link)
                                        <a href="{{ $schedule->meeting_link }}" target="_blank" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline truncate block text-xs">Buka Link &rarr;</a>
                                    @else
                                        <span class="font-bold text-slate-400 truncate block text-xs">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Presentation Table -->
                        <div x-show="open" x-transition.opacity.duration.150ms class="p-3 sm:p-5">
                            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs custom-scrollbar">
                                <table class="w-full min-w-[650px] text-xs text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 dark:bg-slate-900/80 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-slate-200 dark:border-slate-700">
                                            <th class="py-3 px-3 w-12 text-center border-r border-slate-200 dark:border-slate-700">NO</th>
                                            <th class="py-3 px-3 w-32 text-center border-r border-slate-200 dark:border-slate-700">WAKTU</th>
                                            <th class="py-3 px-5 border-r border-slate-200 dark:border-slate-700">PESERTA & JUDUL SKRIPSI</th>
                                            <th class="py-3 px-4 w-52 border-r border-slate-200 dark:border-slate-700">DOSEN PEMBIMBING</th>
                                            <th class="py-3 px-4 w-52">DOSEN PENGUJI</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                        @php $studentNo = 1; @endphp
                                        @forelse($schedule->details as $detail)
                                            @if($detail->thesis_id)
                                                @php
                                                    $isMyStudent = $detail->thesis && ($detail->thesis->pembimbing1_id === $currentUserId || $detail->thesis->pembimbing2_id === $currentUserId);
                                                    $isMyExam = $detail->examiner1_id === $currentUserId || $detail->examiner2_id === $currentUserId;
                                                @endphp
                                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors {{ ($isMyStudent || $isMyExam) ? 'bg-amber-500/5 dark:bg-amber-500/10' : '' }}">
                                                    <td class="py-3.5 px-3 text-center border-r border-slate-200 dark:border-slate-700">
                                                        <span class="inline-flex items-center justify-center w-6 h-6 {{ ($isMyStudent || $isMyExam) ? 'bg-amber-500 text-white font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200/60 dark:border-slate-700 font-bold' }} rounded-lg text-xs">
                                                            {{ $studentNo++ }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3.5 px-3 text-center border-r border-slate-200 dark:border-slate-700">
                                                        <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800/40 rounded-lg font-bold text-[11px] tracking-tight whitespace-nowrap">
                                                            <svg class="w-3 h-3 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3.5 px-5 border-r border-slate-200 dark:border-slate-700">
                                                        <div class="space-y-1.5">
                                                            <div class="flex items-center flex-wrap gap-2">
                                                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-black text-[10px] rounded uppercase tracking-wider">
                                                                    {{ $detail->thesis->student->identifier }}
                                                                </span>
                                                                <span class="font-bold text-slate-800 dark:text-slate-100 text-xs">{{ $detail->thesis->student->name }}</span>
                                                                
                                                                @if($isMyExam)
                                                                    <span class="px-2 py-0.5 bg-amber-500 text-slate-950 font-black text-[9px] rounded-full uppercase tracking-wider">Anda Menguji</span>
                                                                @endif
                                                                @if($isMyStudent)
                                                                    <span class="px-2 py-0.5 bg-indigo-500 text-white font-black text-[9px] rounded-full uppercase tracking-wider">Bimbingan Anda</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed bg-slate-50/50 dark:bg-slate-900/30 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700">
                                                                {{ $detail->thesis->title }}
                                                            </p>
                                                        </div>
                                                    </td>
                                                    <td class="py-3.5 px-4 border-r border-slate-200 dark:border-slate-700">
                                                        <div class="space-y-1.5 text-xs">
                                                            <div class="flex items-start gap-1.5 {{ $detail->thesis->pembimbing1_id === $currentUserId ? 'font-black text-indigo-600 dark:text-indigo-400' : '' }}">
                                                                <span class="px-1.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-[9px] rounded border border-amber-500/20 shrink-0 mt-0.5">P1</span>
                                                                <span class="leading-tight">{{ $detail->thesis->pembimbing1?->name ?? '-' }}</span>
                                                            </div>
                                                            @if($detail->thesis->pembimbing2)
                                                                <div class="flex items-start gap-1.5 {{ $detail->thesis->pembimbing2_id === $currentUserId ? 'font-black text-indigo-600 dark:text-indigo-400' : '' }}">
                                                                    <span class="px-1.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-[9px] rounded border border-amber-500/20 shrink-0 mt-0.5">P2</span>
                                                                    <span class="leading-tight">{{ $detail->thesis->pembimbing2?->name ?? '-' }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="py-3.5 px-4">
                                                        <div class="space-y-1.5 text-xs">
                                                            @if($detail->examiner1)
                                                                <div class="flex items-start gap-1.5 {{ $detail->examiner1_id === $currentUserId ? 'font-black text-amber-600 dark:text-amber-400' : '' }}">
                                                                    <span class="px-1.5 py-0.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-black text-[9px] rounded border border-indigo-500/20 shrink-0 mt-0.5">U1</span>
                                                                    <span class="leading-tight">{{ $detail->examiner1->name }}</span>
                                                                </div>
                                                            @endif
                                                            @if($detail->examiner2)
                                                                <div class="flex items-start gap-1.5 {{ $detail->examiner2_id === $currentUserId ? 'font-black text-amber-600 dark:text-amber-400' : '' }}">
                                                                    <span class="px-1.5 py-0.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-black text-[9px] rounded border border-indigo-500/20 shrink-0 mt-0.5">U2</span>
                                                                    <span class="leading-tight">{{ $detail->examiner2->name }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr class="bg-slate-50/80 dark:bg-slate-900/60">
                                                    <td class="py-2.5 px-3 text-center border-r border-slate-200 dark:border-slate-700 font-bold text-slate-400">#</td>
                                                    <td class="py-2.5 px-3 text-center border-r border-slate-200 dark:border-slate-700">
                                                        <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded-lg font-bold text-[11px] text-slate-600 dark:text-slate-400 whitespace-nowrap border border-slate-200 dark:border-slate-700">
                                                            {{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}
                                                        </span>
                                                    </td>
                                                    <td colspan="3" class="py-2.5 px-5">
                                                        <span class="inline-flex items-center text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest bg-slate-200/50 dark:bg-slate-800 px-3 py-1 rounded-full border border-slate-200 dark:border-slate-700">
                                                            <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ $detail->activity_name }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-6 text-center text-xs text-slate-400 italic">Belum ada rincian peserta untuk jadwal ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-12 text-center shadow-xs">
                        <div class="w-16 h-16 bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-orange-200 dark:border-orange-900/60 shadow-inner">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-1">Tidak Ada Jadwal Sidang Ditemukan</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                            @if(request()->hasAny(['search', 'wave_id', 'date_from', 'date_to', 'my_schedules']) || ($filterDate && $filterDate !== 'upcoming'))
                                Tidak ada jadwal yang cocok dengan filter atau kata kunci pencarian Anda.
                            @else
                                Belum ada agenda sidang skripsi yang aktif atau mendatang untuk saat ini.
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'wave_id', 'date_from', 'date_to', 'my_schedules']) || ($filterDate && $filterDate !== 'upcoming'))
                            <a href="{{ route('thesis-defense-schedules.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase tracking-wider transition-all">
                                <svg class="w-4 h-4 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Reset Filter Pencarian
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
            
            @if($schedules->hasPages())
                <div class="mt-6">
                    {{ $schedules->links() }}
                </div>
            @endif
        @else
            <!-- KAPRODI / ADMIN VIEW: Advanced Management Table with Search & Status Filter -->
            <x-table-card title="Daftar Jadwal Sidang Skripsi">
                <x-slot name="headerActions">
                    <div class="flex flex-wrap items-center justify-between sm:justify-end gap-3 w-full">
                        <!-- Wave Filter Dropdown -->
                        <form action="{{ route('thesis-defense-schedules.index') }}" method="GET" class="relative">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <select name="wave_id" onchange="this.form.submit()"
                                class="pl-4 pr-10 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-all cursor-pointer shadow-2xs">
                                <option value="">Semua Gelombang</option>
                                @foreach($waves as $wave)
                                    <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                        {{ $wave->name }} {{ $wave->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <!-- Search Input -->
                        <div class="w-full sm:w-64">
                            <x-search-input 
                                name="search" 
                                :value="request('search', '')" 
                                placeholder="Cari judul, dosen, ruangan..." 
                                route="thesis-defense-schedules.index"
                                :params="['wave_id' => request('wave_id', '')]" />
                        </div>

                        <!-- Simulation Button -->
                        <a href="{{ route('thesis-defense-schedules.simulation', ['wave_id' => $selectedWaveId]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs font-bold rounded-xl tracking-wide transition-all shadow-sm active:scale-95 cursor-pointer shrink-0"
                            title="Generate simulasi jadwal sidang otomatis cerdas">
                            <svg class="w-4 h-4 fill-none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Simulasi Otomatis</span>
                        </a>

                        <!-- Import Excel Button -->
                        <button type="button" @click="openImportModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-bold rounded-xl tracking-wide transition-all shadow-sm cursor-pointer shrink-0"
                            title="Import jadwal sidang skripsi dari file Excel">
                            <svg class="w-4 h-4 fill-none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span class="whitespace-nowrap">Import Excel</span>
                        </button>

                        <!-- Cetak Semua Ruangan (Batch PDF) Button -->
                        <a href="{{ route('thesis-defense-schedules.export-batch-pdf', ['wave_id' => $selectedWaveId, 'date' => request('date')]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-bold rounded-xl tracking-wide transition-all shadow-sm active:scale-95 cursor-pointer shrink-0"
                            title="Cetak seluruh ruangan dalam 1 file PDF">
                            <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Cetak Semua Ruangan (PDF)</span>
                        </a>

                        <!-- Cetak Semua Ruangan (Batch Word) Button -->
                        <a href="{{ route('thesis-defense-schedules.export-batch-word', ['wave_id' => $selectedWaveId, 'date' => request('date')]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-700 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-500 text-white text-xs font-bold rounded-xl tracking-wide transition-all shadow-sm active:scale-95 cursor-pointer shrink-0"
                            title="Cetak seluruh ruangan dalam 1 file Word (.docx)">
                            <svg class="w-4 h-4 text-blue-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Cetak Semua (Word)</span>
                        </a>

                        <!-- Add Button -->
                        <a href="{{ route('thesis-defense-schedules.create', ['wave_id' => $selectedWaveId]) }}" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl tracking-wide transition-all shadow-sm active:scale-95 cursor-pointer shrink-0">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="whitespace-nowrap">Tambah Jadwal Manual</span>
                        </a>
                    </div>
                </x-slot>

                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-200 dark:border-slate-700">
                            <th class="py-4 px-6">Informasi Sesi</th>
                            <th class="py-4 px-6">Waktu & Tanggal</th>
                            <th class="py-4 px-6">Pimpinan Sidang</th>
                            <th class="py-4 px-6">Lokasi / Tautan</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($schedules as $schedule)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group">
                                <td class="py-4 px-6">
                                    <p class="text-[11px] font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $schedule->title }}</p>
                                    <p class="text-[9px] text-slate-500 dark:text-slate-500 mt-1 uppercase font-black tracking-widest">Oleh: {{ $schedule->creator->name }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-black text-slate-800 dark:text-slate-100 uppercase tracking-tighter">{{ \Carbon\Carbon::parse($schedule->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                        <span class="text-[9px] text-indigo-600 dark:text-indigo-400 uppercase font-black tracking-[0.2em] mt-0.5">{{ \Carbon\Carbon::parse($schedule->date)->locale('id')->translatedFormat('l') }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="w-14 text-[8px] text-slate-400 uppercase font-black tracking-widest">Ketua</span>
                                            <span class="text-[10px] text-slate-700 dark:text-slate-300 font-black uppercase tracking-tighter">{{ $schedule->chairman?->name ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-14 text-[8px] text-slate-400 uppercase font-black tracking-widest">Moderator</span>
                                            <span class="text-[10px] text-slate-700 dark:text-slate-300 font-black uppercase tracking-tighter">{{ $schedule->moderator?->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-2 items-start">
                                        <span class="px-2 py-1 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-[9px] font-black text-slate-600 dark:text-slate-400 uppercase tracking-widest">
                                            {{ $schedule->location ?: 'BELUM DIATUR' }}
                                        </span>
                                        @if($schedule->meeting_link)
                                            <a href="{{ $schedule->meeting_link }}" target="_blank" class="inline-flex items-center text-[8px] font-black text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-2 py-1 rounded-lg border border-blue-100 dark:border-blue-500/20 hover:bg-blue-100 transition-all uppercase tracking-widest">
                                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                Meeting Link
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <a href="{{ route('thesis-defense-schedules.export-pdf', $schedule) }}" class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 transition-all group/pdf" title="Export PDF">
                                            <svg class="w-5 h-5 group-hover/pdf:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                        <a href="{{ route('thesis-defense-schedules.export-word', $schedule) }}" class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 transition-all group/word" title="Export Word (.docx)">
                                            <svg class="w-5 h-5 group-hover/word:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                        <a href="{{ route('thesis-defense-schedules.show', $schedule) }}" class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 transition-all group/view" title="Detail">
                                            <svg class="w-5 h-5 group-hover/view:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('thesis-defense-schedules.edit', $schedule) }}" class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-amber-600 transition-all group/edit" title="Edit">
                                            <svg class="w-5 h-5 group-hover/edit:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form id="delete-defense-schedule-{{ $schedule->id }}" action="{{ route('thesis-defense-schedules.destroy', $schedule) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" onclick="if(confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) document.getElementById('delete-defense-schedule-{{ $schedule->id }}').submit();" class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 transition-all group/del cursor-pointer" title="Hapus">
                                            <svg class="w-5 h-5 group-hover/del:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="5" description="Agenda pelaksanaan sidang skripsi belum tersedia untuk filter atau gelombang ini." icon="calendar" />
                        @endforelse
                    </tbody>
                </table>
            </x-table-card>
        @endif

        <!-- Import Excel Modal (Admin & Kaprodi) -->
        <div x-show="openImportModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openImportModal = false">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-200 dark:border-slate-700">
                    <!-- Modal Header -->
                    <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-200 dark:border-emerald-800/60 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">
                                    Import Jadwal Sidang Skripsi
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Unggah berkas spreadsheet Excel (.xlsx, .xls, .csv)</p>
                            </div>
                        </div>
                        <button type="button" @click="openImportModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Modal Body Form -->
                    <form action="{{ route('thesis-defense-schedules.import') }}" method="POST" enctype="multipart/form-data" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                        @csrf
                        <div class="p-6 space-y-5">
                            <!-- Download Template Card -->
                            <div class="p-4 rounded-2xl bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-950/30 dark:to-amber-950/20 border border-orange-200/80 dark:border-orange-800/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                                <div class="space-y-0.5">
                                    <h4 class="text-xs font-black text-orange-900 dark:text-orange-200 uppercase tracking-wider flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Format / Template Excel
                                    </h4>
                                    <p class="text-[11px] text-orange-800/80 dark:text-orange-300/80">Gunakan template resmi agar susunan kolom sesuai sistem</p>
                                </div>
                                <a href="{{ route('thesis-defense-schedules.template') }}"
                                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-orange-500 hover:text-white dark:hover:bg-orange-600 text-orange-600 dark:text-orange-400 font-bold text-xs rounded-xl border border-orange-300 dark:border-orange-700/60 shadow-xs transition-all shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Download Format Excel</span>
                                </a>
                            </div>

                            <!-- Gelombang Target -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Target Gelombang Sidang <span class="text-rose-500">*</span>
                                </label>
                                <select name="wave_id" required
                                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                                    @foreach($waves as $wave)
                                        <option value="{{ $wave->id }}" {{ ($selectedWaveId ?? $activeWave?->id) == $wave->id ? 'selected' : '' }}>
                                            {{ strtoupper($wave->name) }} {{ $wave->is_active ? '(Aktif Saat Ini)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- File Input -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Pilih File Excel (.xlsx, .xls, .csv) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                                           class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-600 file:text-white hover:file:bg-orange-700 file:cursor-pointer transition-all bg-slate-50 dark:bg-slate-900 rounded-2xl p-2 border border-dashed border-slate-300 dark:border-slate-700 focus:outline-none">
                                </div>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Ukuran maksimal file: 5 MB</p>
                            </div>

                            <!-- Petunjuk Pengisian Singkat -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700/80 text-[11px] text-slate-600 dark:text-slate-400 space-y-1">
                                <p class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px]">Petunjuk Format:</p>
                                <ul class="list-disc list-inside space-y-0.5 text-[10.5px]">
                                    <li>Baris dengan <b>Nama Sesi, Tanggal, dan Lokasi</b> yang sama otomatis digabung menjadi 1 kelompok jadwal.</li>
                                    <li>Format tanggal: <b>YYYY-MM-DD</b> (cth: 2026-10-25). Jam: <b>HH:mm</b> (cth: 08:00).</li>
                                    <li>Kolom Dosen (Ketua, Penguji 1 & 2): dapat diisi <b>NIDN</b> atau <b>Nama Lengkap</b>.</li>
                                    <li>Untuk sesi non-mahasiswa (cth: Rapat Yudisium), kosongkan NPM dan isi kolom <b>Kegiatan Khusus</b>.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-3">
                            <button type="button" @click="openImportModal = false" :disabled="isSubmitting"
                                    class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmitting"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-500/20 hover:shadow-emerald-500/30 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="isSubmitting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmitting ? 'Memproses Import...' : 'Mulai Import Data'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

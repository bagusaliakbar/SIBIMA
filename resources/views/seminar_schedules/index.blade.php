<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Seminar', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6" x-data="{ allOpen: true }">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-[11px] font-black uppercase tracking-widest flex items-center border border-emerald-100 dark:border-emerald-800/50 shadow-sm animate-pulse">
                <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(Auth::user()->role === 'dosen')
            <!-- DOSEN VIEW: Standard Top Status Tabs -->
            <div class="flex items-center gap-1 border-b border-slate-200 dark:border-slate-700 overflow-x-auto pb-px custom-scrollbar">
                <!-- Upcoming Tab -->
                <a href="{{ route('seminar-schedules.index', array_merge(request()->except(['page', 'filter_date', 'date', 'date_from', 'date_to']), ['filter_date' => 'upcoming'])) }}"
                   class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($filterDate ?? 'upcoming') === 'upcoming' && !$mySchedules ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <span class="w-2 h-2 rounded-full {{ ($filterDate ?? 'upcoming') === 'upcoming' && !$mySchedules ? 'bg-orange-500 animate-pulse' : 'bg-slate-400' }}"></span>
                    Mendatang
                    <span class="px-2 py-0.5 text-[10px] rounded-full {{ ($filterDate ?? 'upcoming') === 'upcoming' && !$mySchedules ? 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $counts['upcoming'] }}</span>
                </a>

                <!-- Today Tab -->
                <a href="{{ route('seminar-schedules.index', array_merge(request()->except(['page', 'filter_date', 'date', 'date_from', 'date_to']), ['filter_date' => 'today'])) }}"
                   class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ $filterDate === 'today' && !$mySchedules ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <span class="w-2 h-2 rounded-full {{ $filterDate === 'today' && !$mySchedules ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                    Hari Ini
                    <span class="px-2 py-0.5 text-[10px] rounded-full {{ $filterDate === 'today' && !$mySchedules ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $counts['today'] }}</span>
                </a>

                <!-- Past Tab -->
                <a href="{{ route('seminar-schedules.index', array_merge(request()->except(['page', 'filter_date', 'date', 'date_from', 'date_to']), ['filter_date' => 'past'])) }}"
                   class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ $filterDate === 'past' && !$mySchedules ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Riwayat
                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">{{ $counts['past'] }}</span>
                </a>

                <!-- All Tab -->
                <a href="{{ route('seminar-schedules.index', array_merge(request()->except(['page', 'filter_date', 'date', 'date_from', 'date_to']), ['filter_date' => 'all'])) }}"
                   class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ $filterDate === 'all' && !$mySchedules ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Semua Agenda
                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">{{ $counts['all'] }}</span>
                </a>

                <!-- "Jadwal Saya" Tab -->
                <a href="{{ route('seminar-schedules.index', array_merge(request()->except(['page', 'my_schedules']), ['my_schedules' => $mySchedules ? '0' : '1', 'filter_date' => 'all'])) }}"
                   class="px-5 py-3.5 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ $mySchedules ? 'border-amber-500 text-amber-700 dark:text-amber-400 bg-amber-50/60 dark:bg-amber-500/10 font-bold' : 'border-transparent text-amber-600 dark:text-amber-400 hover:text-amber-700 hover:bg-amber-50/40 dark:hover:bg-slate-800' }}"
                   title="Hanya tampilkan jadwal di mana Anda menjadi Penguji, Pembimbing, Ketua Sidang, atau Moderator">
                    <svg class="w-3.5 h-3.5" fill="{{ $mySchedules ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    Jadwal Saya
                    <span class="px-2 py-0.5 text-[10px] rounded-full {{ $mySchedules ? 'bg-amber-200 dark:bg-amber-500/20 text-amber-900 dark:text-amber-300 font-black' : 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400' }}">{{ $counts['mySchedules'] }}</span>
                </a>
            </div>

            <!-- DOSEN VIEW: Compact Single-Line Filter Toolbar -->
            <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-xs flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                <form action="{{ route('seminar-schedules.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2.5 w-full lg:w-auto flex-1">
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
                        <option value="">Semua Gelombang</option>
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
                            <a href="{{ route('seminar-schedules.index') }}" class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-xl transition-all" title="Reset Filter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Right Side: Expand / Collapse All Controls -->
                <div class="flex items-center gap-2.5 shrink-0 border-t lg:border-t-0 pt-2 lg:pt-0 w-full lg:w-auto justify-end">
                    <button type="button" @click="allOpen = true" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2.5 px-5 py-2.5 bg-slate-100 dark:bg-slate-700/80 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-600 shadow-2xs transition-all whitespace-nowrap">
                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        <span>Buka Semua</span>
                    </button>
                    <button type="button" @click="allOpen = false" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2.5 px-5 py-2.5 bg-slate-100 dark:bg-slate-700/80 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-600 shadow-2xs transition-all whitespace-nowrap">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path></svg>
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
                         class="bg-white dark:bg-slate-800 rounded-2xl border {{ count($myRoles) > 0 ? 'border-amber-300 dark:border-amber-500/50 shadow-sm ring-1 ring-amber-400/20' : 'border-slate-200 dark:border-slate-700/80 shadow-xs' }} overflow-hidden transition-all">
                        
                        <!-- Header Banner -->
                        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-700/60 bg-gradient-to-r {{ count($myRoles) > 0 ? 'from-amber-50/40 via-white to-white dark:from-amber-950/20 dark:via-slate-800 dark:to-slate-800' : 'from-slate-50/80 via-white to-white dark:from-slate-800/80 dark:via-slate-800 dark:to-slate-800' }}">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <!-- Badges Row -->
                                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-300 rounded-lg text-xs font-bold border border-orange-200 dark:border-orange-500/20">
                                            <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $scheduleDate->locale('id')->translatedFormat('l, d F Y') }}
                                        </span>

                                        @if($isToday)
                                            <span class="px-2.5 py-0.5 bg-emerald-500 text-white rounded-full text-[10px] font-black uppercase tracking-wider animate-pulse">
                                                🟢 Hari Ini
                                            </span>
                                        @elseif($isUpcoming)
                                            <span class="px-2.5 py-0.5 bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/20 rounded-full text-[10px] font-black uppercase tracking-wider">
                                                🔵 Mendatang
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-black uppercase tracking-wider">
                                                ⚪ Selesai
                                            </span>
                                        @endif

                                        @if(count($myRoles) > 0)
                                            <span class="px-2.5 py-0.5 bg-amber-400 text-slate-950 font-black rounded-full text-[10px] uppercase tracking-wider shadow-2xs">
                                                ⭐ Peran Anda: {{ implode(' & ', $myRoles) }}
                                            </span>
                                        @endif

                                        <span class="px-2.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-full text-[10px] font-bold">
                                            👥 {{ $studentCount }} Mahasiswa
                                        </span>
                                    </div>

                                    <!-- Session Title -->
                                    <h3 class="text-sm sm:text-base font-black tracking-tight text-slate-800 dark:text-slate-100 uppercase break-words">
                                        {{ $schedule->title }}
                                    </h3>
                                </div>

                                <!-- Header Action Buttons -->
                                <div class="flex items-center justify-between sm:justify-end gap-2.5 shrink-0 w-full sm:w-auto pt-1 sm:pt-0">
                                    <a href="{{ route('seminar-schedules.export-pdf', $schedule) }}" 
                                       class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2.5 px-5 py-2.5 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 rounded-xl text-xs font-bold shadow-2xs transition-all whitespace-nowrap shrink-0"
                                       title="Cetak PDF">
                                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span>Cetak PDF</span>
                                    </a>

                                    <button type="button" @click="open = !open" 
                                            class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2.5 px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-600 whitespace-nowrap shrink-0"
                                            title="Buka/Tutup Rincian">
                                        <span x-text="open ? 'Tutup' : 'Rincian'"></span>
                                        <svg class="w-4 h-4 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Meta Info Chips Bar (Clear & Readable) -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-3 mt-3 border-t border-slate-100 dark:border-slate-700/60 text-xs">
                                <div class="px-2.5 sm:px-3 py-2 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700/60 min-w-0">
                                    <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Ketua Sidang</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate block text-xs" title="{{ $schedule->chairman?->name ?? '-' }}">{{ $schedule->chairman?->name ?? '-' }}</span>
                                </div>
                                <div class="px-2.5 sm:px-3 py-2 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700/60 min-w-0">
                                    <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Moderator</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate block text-xs" title="{{ $schedule->moderator?->name ?? '-' }}">{{ $schedule->moderator?->name ?? '-' }}</span>
                                </div>
                                <div class="px-2.5 sm:px-3 py-2 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700/60 min-w-0">
                                    <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Ruangan</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate block text-xs" title="{{ $schedule->location ?: 'BELUM DIATUR' }}">{{ $schedule->location ?: 'BELUM DIATUR' }}</span>
                                </div>
                                <div class="px-2.5 sm:px-3 py-2 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700/60 min-w-0">
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
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                                        @php $studentNo = 1; @endphp
                                        @forelse($schedule->details as $detail)
                                            @if($detail->thesis_id)
                                                @php
                                                    $isMyStudent = $detail->thesis && ($detail->thesis->pembimbing1_id === $currentUserId || $detail->thesis->pembimbing2_id === $currentUserId);
                                                    $isMyExam = $detail->examiner1_id === $currentUserId || $detail->examiner2_id === $currentUserId;
                                                @endphp
                                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors {{ ($isMyStudent || $isMyExam) ? 'bg-amber-500/5 dark:bg-amber-500/10' : '' }}">
                                                    <td class="py-3.5 px-3 text-center border-r border-slate-100 dark:border-slate-700/50">
                                                        <span class="inline-flex items-center justify-center w-6 h-6 {{ ($isMyStudent || $isMyExam) ? 'bg-amber-500 text-white font-black' : 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold' }} rounded-lg text-xs">
                                                            {{ $studentNo++ }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3.5 px-3 text-center border-r border-slate-100 dark:border-slate-700/50">
                                                        <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800/40 rounded-lg font-bold text-[11px] tracking-tight whitespace-nowrap">
                                                            <svg class="w-3 h-3 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3.5 px-5 border-r border-slate-100 dark:border-slate-700/50">
                                                        <div class="space-y-1.5">
                                                            <div class="flex items-center flex-wrap gap-2">
                                                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-black text-[10px] rounded uppercase tracking-wider">
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
                                                            <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed bg-slate-50/50 dark:bg-slate-900/30 p-2.5 rounded-lg border border-slate-100 dark:border-slate-700/40">
                                                                {{ $detail->thesis->title }}
                                                            </p>
                                                        </div>
                                                    </td>
                                                    <td class="py-3.5 px-4 border-r border-slate-100 dark:border-slate-700/50">
                                                        <div class="space-y-1.5 text-xs">
                                                            <div class="flex items-start gap-1.5 {{ $detail->thesis->pembimbing1_id === $currentUserId ? 'font-black text-indigo-600 dark:text-indigo-400' : '' }}">
                                                                <span class="px-1.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-[9px] rounded border border-amber-500/20 shrink-0 mt-0.5">P1</span>
                                                                <span class="leading-tight">{{ $detail->thesis->pembimbing1->name }}</span>
                                                            </div>
                                                            @if($detail->thesis->pembimbing2)
                                                                <div class="flex items-start gap-1.5 {{ $detail->thesis->pembimbing2_id === $currentUserId ? 'font-black text-indigo-600 dark:text-indigo-400' : '' }}">
                                                                    <span class="px-1.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-[9px] rounded border border-amber-500/20 shrink-0 mt-0.5">P2</span>
                                                                    <span class="leading-tight">{{ $detail->thesis->pembimbing2->name }}</span>
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
                                                    <td class="py-2.5 px-3 text-center border-r border-slate-100 dark:border-slate-700/50 font-bold text-slate-400">#</td>
                                                    <td class="py-2.5 px-3 text-center border-r border-slate-100 dark:border-slate-700/50">
                                                        <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded-lg font-bold text-[11px] text-slate-600 dark:text-slate-400 whitespace-nowrap border border-slate-200 dark:border-slate-700">
                                                            {{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}
                                                        </span>
                                                    </td>
                                                    <td colspan="3" class="py-2.5 px-5">
                                                        <span class="inline-flex items-center text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest bg-slate-200/50 dark:bg-slate-800 px-3 py-1 rounded-full border border-slate-200/60 dark:border-slate-700/50">
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
                    <div class="p-10 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-xs space-y-3">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-800 dark:text-white">
                            Tidak Ada Jadwal Seminar Ditemukan
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed font-medium">
                            Tidak ditemukan agenda seminar yang sesuai dengan filter atau kriteria pencarian yang Anda pilih.
                        </p>
                        <div>
                            <a href="{{ route('seminar-schedules.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-black uppercase tracking-wider transition-all">
                                Reset Semua Filter
                            </a>
                        </div>
                    </div>
                @endforelse

                <div class="mt-4">
                    {{ $schedules->links() }}
                </div>
            </div>
        @else
            <!-- ADMIN & KAPRODI VIEW: Compact Table Card with Enhanced Filters -->
            <x-table-card 
                title="Agenda Pelaksanaan Seminar"
                :footer="$schedules->links()">
                
                <x-slot name="headerActions">
                    <div class="flex flex-wrap items-center gap-3">
                        <form action="{{ route('seminar-schedules.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                            <!-- Search -->
                            <div class="relative">
                                <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Cari jadwal / mahasiswa..."
                                       class="py-2 pl-3 pr-8 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 min-w-[180px]">
                            </div>

                            <!-- Date From & To -->
                            <input type="date" name="date_from" value="{{ $dateFrom }}" title="Dari Tanggal"
                                   class="py-2 px-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest">
                            <input type="date" name="date_to" value="{{ $dateTo }}" title="Sampai Tanggal"
                                   class="py-2 px-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest">

                            <!-- Wave Filter -->
                            <select name="wave_id" onchange="this.form.submit()"
                                class="py-2 pl-3 pr-8 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 min-w-[170px]">
                                <option value="">SEMUA GELOMBANG</option>
                                @foreach($waves as $wave)
                                    <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                        {{ strtoupper($wave->name) }} {{ $wave->is_active ? '(AKTIF)' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="p-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-xl text-slate-700 dark:text-slate-200" title="Cari">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>

                            @if(request()->hasAny(['search', 'date_from', 'date_to', 'wave_id']))
                                <a href="{{ route('seminar-schedules.index') }}" class="p-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-500 rounded-xl" title="Reset">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </form>
                        
                        <a href="{{ route('seminar-schedules.create') }}"
                            class="inline-flex items-center px-5 py-2 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-500/20">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Buat Jadwal
                        </a>
                    </div>
                </x-slot>

                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                            <th class="py-4 px-6">Informasi Sesi</th>
                            <th class="py-4 px-6">Waktu & Tanggal</th>
                            <th class="py-4 px-6">Pimpinan Sidang</th>
                            <th class="py-4 px-6">Lokasi / Tautan</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
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
                                        <a href="{{ route('seminar-schedules.export-pdf', $schedule) }}" class="p-2 text-slate-400 hover:text-rose-600 transition-all group/pdf" title="Export PDF">
                                            <svg class="w-5 h-5 group-hover/pdf:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                        <a href="{{ route('seminar-schedules.show', $schedule) }}" class="p-2 text-slate-400 hover:text-indigo-600 transition-all group/view" title="Detail">
                                            <svg class="w-5 h-5 group-hover/view:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('seminar-schedules.edit', $schedule) }}" class="p-2 text-slate-400 hover:text-amber-600 transition-all group/edit" title="Edit">
                                            <svg class="w-5 h-5 group-hover/edit:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('seminar-schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition-all group/del" title="Hapus">
                                                <svg class="w-5 h-5 group-hover/del:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="5" description="Agenda pelaksanaan seminar belum tersedia untuk filter atau gelombang ini." icon="calendar" />
                        @endforelse
                    </tbody>
                </table>
            </x-table-card>
        @endif
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Monitoring', 'route' => route('monitoring.index')],
            ['label' => 'Bimbingan Mingguan', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full" x-data="weeklyMonitoringApp()">
        @include('monitoring.partials.tabs')

        <!-- Week Navigator & Period Banner -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 dark:border-slate-700/80 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 border border-orange-200/60 dark:border-orange-800/40">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight">
                                    Monitoring Bimbingan Mingguan
                                </h3>
                                @if($isCurrentWeek)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Minggu Berjalan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                        Arsip Riwayat
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Periode: <span class="font-bold text-slate-700 dark:text-slate-200">{{ $startDate->locale('id')->isoFormat('D MMMM Y') }}</span> s/d <span class="font-bold text-slate-700 dark:text-slate-200">{{ $endDate->locale('id')->isoFormat('D MMMM Y') }}</span> (Senin - Minggu)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('monitoring.weekly', array_merge(request()->except(['page']), ['date' => $prevWeekDate])) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Minggu Lalu</span>
                    </a>

                    @if(!$isCurrentWeek)
                        <a href="{{ route('monitoring.weekly', array_merge(request()->except(['page']), ['date' => $currentWeekDate])) }}" 
                           class="inline-flex items-center px-3 py-2 text-xs font-bold rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 border border-orange-200/60 dark:border-orange-800/40 hover:bg-orange-100 transition-colors shadow-2xs">
                            Minggu Ini
                        </a>
                    @endif

                    <a href="{{ route('monitoring.weekly', array_merge(request()->except(['page']), ['date' => $nextWeekDate])) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-2xs">
                        <span>Minggu Depan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>

                    <!-- Jump to specific date -->
                    <form action="{{ route('monitoring.weekly') }}" method="GET" class="inline-flex items-center">
                        @foreach(request()->except(['date', 'page']) as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <input type="date" 
                               name="date" 
                               value="{{ $startDate->format('Y-m-d') }}" 
                               onchange="this.form.submit()" 
                               title="Pilih tanggal dalam minggu tertentu"
                               class="py-1.5 px-2.5 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-semibold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-2xs">
                    </form>
                </div>
            </div>

            <!-- Notice / Standard Guidance Banner -->
            <div class="mt-4 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800 flex items-start gap-3">
                <svg class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    <span class="font-bold text-slate-800 dark:text-slate-100">Ketentuan Evaluasi:</span> Mahasiswa aktif diwajibkan melakukan bimbingan <strong>minimal 1 kali ke Pembimbing 1</strong> dan <strong>minimal 1 kali ke Pembimbing 2</strong> setiap minggunya. Gunakan tombol aksi di tabel untuk mengirim pesan pengingat langsung ke nomor WhatsApp mahasiswa yang belum memenuhi target.
                </div>
            </div>
        </div>

        <!-- 4 KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Active Theses -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Mahasiswa Aktif</p>
                        <h4 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-slate-100 mt-1">
                            {{ $stats['total'] }}
                        </h4>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1">Skripsi belum selesai</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Compliant Count (P1 >= 1 and P2 >= 1) -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Lengkap (P1 & P2)</p>
                        <h4 class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                            {{ $stats['compliant'] }}
                        </h4>
                        <div class="flex items-center gap-1.5 mt-1">
                            <div class="w-14 bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $stats['compliant_percent'] }}%"></div>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ $stats['compliant_percent'] }}%</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Partial Count (Only P1 or P2) -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest">Sebagian (1 Dosen)</p>
                        <h4 class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">
                            {{ $stats['partial'] }}
                        </h4>
                        <div class="flex items-center gap-1.5 mt-1">
                            <div class="w-14 bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $stats['partial_percent'] }}%"></div>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ $stats['partial_percent'] }}%</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Inactive Count (0x Mentoring) -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest">Belum Bimbingan (0x)</p>
                        <h4 class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 mt-1">
                            {{ $stats['inactive'] }}
                        </h4>
                        <div class="flex items-center gap-1.5 mt-1">
                            <div class="w-14 bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $stats['inactive_percent'] }}%"></div>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ $stats['inactive_percent'] }}%</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-xs border border-slate-200/80 dark:border-slate-700/80 mb-6">
            <form action="{{ route('monitoring.weekly') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                <input type="hidden" name="date" value="{{ $startDate->format('Y-m-d') }}">

                <div class="flex flex-wrap items-center gap-2.5 flex-1">
                    <!-- Search Input -->
                    <div class="relative flex-1 min-w-[220px] max-w-sm group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 group-focus-within:text-orange-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ $filters['search'] ?? '' }}" 
                               placeholder="Cari mahasiswa atau NPM..."
                               style="padding-left: 2.6rem !important;"
                               class="w-full pr-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs font-semibold shadow-2xs transition-all">
                    </div>

                    <!-- Filter Status Kepatuhan -->
                    <select name="compliance_status" 
                            onchange="this.form.submit()" 
                            style="padding-right: 2.25rem !important; padding-left: 0.875rem !important;"
                            class="py-2 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs font-semibold shadow-2xs cursor-pointer">
                        <option value="">-- Semua Status Kepatuhan --</option>
                        <option value="compliant" {{ ($filters['compliance_status'] ?? '') === 'compliant' ? 'selected' : '' }}>🟢 Lengkap (>= 1x P1 & P2)</option>
                        <option value="partial" {{ ($filters['compliance_status'] ?? '') === 'partial' ? 'selected' : '' }}>🟡 Sebagian (Hanya 1 Dosen)</option>
                        <option value="inactive" {{ ($filters['compliance_status'] ?? '') === 'inactive' ? 'selected' : '' }}>🔴 Belum Bimbingan (0x)</option>
                    </select>

                    <!-- Filter Dosen Pembimbing -->
                    <select name="pembimbing_id" 
                            onchange="this.form.submit()" 
                            style="padding-right: 2.25rem !important; padding-left: 0.875rem !important;"
                            class="py-2 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs font-semibold shadow-2xs cursor-pointer">
                        <option value="">-- Semua Dosen Pembimbing --</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}" {{ ($filters['pembimbing_id'] ?? '') == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Angkatan -->
                    <select name="entry_year" 
                            onchange="this.form.submit()" 
                            style="padding-right: 2.25rem !important; padding-left: 0.875rem !important;"
                            class="py-2 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs font-semibold shadow-2xs cursor-pointer">
                        <option value="">-- Semua Angkatan --</option>
                        @foreach($entryYears as $year)
                            <option value="{{ $year }}" {{ ($filters['entry_year'] ?? '') == $year ? 'selected' : '' }}>Angkatan {{ $year }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-3 py-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-colors shadow-2xs">
                        Terapkan
                    </button>

                    @if(!empty($filters['search']) || !empty($filters['compliance_status']) || !empty($filters['pembimbing_id']) || !empty($filters['entry_year']))
                        <a href="{{ route('monitoring.weekly', ['date' => $startDate->format('Y-m-d')]) }}" class="px-2.5 py-2 text-xs font-bold text-rose-500 hover:text-rose-700 transition-colors">
                            Reset Filter
                        </a>
                    @endif
                </div>

                <!-- Export Excel Button -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('monitoring.weekly.export-excel', array_merge(request()->query(), ['date' => $startDate->format('Y-m-d')])) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm shadow-emerald-600/20 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Ekspor Excel</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Weekly Mentoring Students Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/40 text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4 text-center w-12">No</th>
                            <th class="py-3.5 px-5 whitespace-nowrap">Mahasiswa</th>
                            <th class="py-3.5 px-5 whitespace-nowrap">Pembimbing 1 & Sesi</th>
                            <th class="py-3.5 px-5 whitespace-nowrap">Pembimbing 2 & Sesi</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status Minggu Ini</th>
                            <th class="py-3.5 px-5 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($theses as $index => $thesis)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors align-top group">
                                <!-- No -->
                                <td class="py-4 px-4 text-center text-xs font-semibold text-slate-400">
                                    {{ $theses->firstItem() + $index }}
                                </td>

                                <!-- Mahasiswa Info -->
                                <td class="py-4 px-5">
                                    <div class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors tracking-tight">
                                        {{ $thesis->student ? ucwords(strtolower($thesis->student->name)) : '-' }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-slate-400 dark:text-slate-500 mt-0.5 font-medium">
                                        <span class="font-mono">{{ $thesis->student->identifier ?? '-' }}</span>
                                        @if($thesis->student && $thesis->student->entry_year)
                                            <span class="text-slate-300 dark:text-slate-700">•</span>
                                            <span>Angkatan {{ $thesis->student->entry_year }}</span>
                                        @endif
                                    </div>
                                    @if($thesis->student && $thesis->student->phone)
                                        <div class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400 mt-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                            <span>{{ $thesis->student->phone }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic mt-1 block">No WA belum ada</span>
                                    @endif
                                </td>

                                <!-- Pembimbing 1 Details -->
                                <td class="py-4 px-5">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-50 dark:bg-blue-950/60 text-[10px] font-bold text-blue-600 dark:text-blue-400 shrink-0">1</span>
                                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-100 tracking-tight">
                                                {{ $thesis->pembimbing1 ? $thesis->pembimbing1->name : '-' }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2 pl-7">
                                            @if($thesis->weekly_p1_count >= 1)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/40">
                                                    <svg class="w-2.5 h-2.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    {{ $thesis->weekly_p1_count }}x Bimbingan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200/70 dark:border-rose-800/40">
                                                    0x Bimbingan
                                                </span>
                                            @endif

                                            @if($thesis->weekly_latest_p1)
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500">
                                                    ({{ \Carbon\Carbon::parse($thesis->weekly_latest_p1->scheduled_at)->isoFormat('dd, D MMM') }})
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Pembimbing 2 Details -->
                                <td class="py-4 px-5">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-[10px] font-bold text-indigo-600 dark:text-indigo-400 shrink-0">2</span>
                                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-100 tracking-tight">
                                                {{ $thesis->pembimbing2 ? $thesis->pembimbing2->name : ($thesis->pembimbing2_id ? '-' : 'Belum Ditugaskan') }}
                                            </span>
                                        </div>

                                        @if($thesis->pembimbing2_id)
                                            <div class="flex items-center gap-2 pl-7">
                                                @if($thesis->weekly_p2_count >= 1)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/40">
                                                        <svg class="w-2.5 h-2.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        {{ $thesis->weekly_p2_count }}x Bimbingan
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200/70 dark:border-rose-800/40">
                                                        0x Bimbingan
                                                    </span>
                                                @endif

                                                @if($thesis->weekly_latest_p2)
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500">
                                                        ({{ \Carbon\Carbon::parse($thesis->weekly_latest_p2->scheduled_at)->isoFormat('dd, D MMM') }})
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="pl-7">
                                                <span class="text-[10px] text-slate-400 italic">P2 belum aktif</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status Kepatuhan Badge -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($thesis->weekly_compliance_status === 'compliant')
                                        <div class="inline-flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Lengkap</span>
                                            </span>
                                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">P1 & P2 Terpenuhi</span>
                                        </div>
                                    @elseif($thesis->weekly_compliance_status === 'partial')
                                        <div class="inline-flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <span>Sebagian</span>
                                            </span>
                                            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                                {{ $thesis->weekly_p1_count >= 1 ? 'Kurang P2' : 'Kurang P1' }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="inline-flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                <span>Belum Bimbingan</span>
                                            </span>
                                            <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400">0 Sesi Minggu Ini</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Send WhatsApp Reminder Button (Available for all, especially for partial and inactive) -->
                                        <button type="button" 
                                                @click="openReminderModal({
                                                    id: {{ $thesis->id }},
                                                    studentName: '{{ addslashes($thesis->student ? $thesis->student->name : '') }}',
                                                    studentNpm: '{{ addslashes($thesis->student ? $thesis->student->identifier : '') }}',
                                                    studentPhone: '{{ addslashes($thesis->student ? $thesis->student->phone : '') }}',
                                                    p1Name: '{{ addslashes($thesis->pembimbing1 ? $thesis->pembimbing1->name : '-') }}',
                                                    p2Name: '{{ addslashes($thesis->pembimbing2 ? $thesis->pembimbing2->name : '-') }}',
                                                    p1Count: {{ $thesis->weekly_p1_count }},
                                                    p2Count: {{ $thesis->weekly_p2_count }}
                                                })"
                                                title="Kirim pengingat WhatsApp ke mahasiswa"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $thesis->weekly_compliance_status === 'compliant' ? 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200' : 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 shadow-2xs' }}">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.586-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.392-12.416c-5.514 0-10 4.486-10 10 0 1.914.542 3.702 1.482 5.226l-1.571 5.744 5.897-1.547c1.472.872 3.19 1.377 5.023 1.377 5.514 0 10-4.486 10-10s-4.486-10-10-10z"/>
                                            </svg>
                                            <span>Ingatkan</span>
                                        </button>

                                        <!-- View Thesis / Mentoring History Details -->
                                        <a href="{{ route('theses.show', $thesis->id) }}" 
                                           title="Lihat profil dan riwayat bimbingan lengkap"
                                           class="inline-flex items-center justify-center p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 px-6 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                        <svg class="w-12 h-12 mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Tidak ada data mahasiswa skripsi aktif yang cocok.</p>
                                        <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter pencarian, status kepatuhan, atau pilih minggu lain.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($theses->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/40">
                    {{ $theses->links() }}
                </div>
            @endif
        </div>

        <!-- WhatsApp Reminder Modal -->
        <div x-show="reminderModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div x-show="reminderModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     @click="reminderModalOpen = false" 
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Panel -->
                <div x-show="reminderModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700">
                    
                    <form :action="'{{ url('/monitoring/weekly') }}/' + currentTarget.id + '/remind'" method="POST">
                        @csrf
                        <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                        <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">

                        <!-- Modal Header -->
                        <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.586-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.392-12.416c-5.514 0-10 4.486-10 10 0 1.914.542 3.702 1.482 5.226l-1.571 5.744 5.897-1.547c1.472.872 3.19 1.377 5.023 1.377 5.514 0 10-4.486 10-10s-4.486-10-10-10z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Kirim Pengingat WhatsApp</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Peringatan kewajiban bimbingan skripsi mingguan</p>
                                </div>
                            </div>
                            <button type="button" @click="reminderModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-5 space-y-4">
                            <!-- Student Info Card -->
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-700/60 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100" x-text="currentTarget.studentName"></p>
                                    <p class="text-[11px] text-slate-400 font-mono" x-text="currentTarget.studentNpm"></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block" x-text="currentTarget.studentPhone || 'Tidak ada nomor'"></span>
                                    <span class="text-[10px] text-slate-400">Nomor WhatsApp</span>
                                </div>
                            </div>

                            <!-- Editable Message Template -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Isi Pesan WhatsApp:
                                </label>
                                <textarea name="message" 
                                          x-model="reminderMessage" 
                                          rows="7" 
                                          required 
                                          class="w-full text-xs p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 leading-relaxed font-mono shadow-2xs"></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Anda dapat menyesuaikan isi pesan sebelum dikirimkan ke mahasiswa.</p>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="p-5 border-t border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/50 flex flex-wrap items-center justify-between gap-2">
                            <!-- Direct Web WhatsApp fallback -->
                            <div>
                                <template x-if="currentTarget.studentPhone">
                                    <a :href="'https://wa.me/' + formatPhoneForWa(currentTarget.studentPhone) + '?text=' + encodeURIComponent(reminderMessage)" 
                                       target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.586-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.392-12.416c-5.514 0-10 4.486-10 10 0 1.914.542 3.702 1.482 5.226l-1.571 5.744 5.897-1.547c1.472.872 3.19 1.377 5.023 1.377 5.514 0 10-4.486 10-10s-4.486-10-10-10z"/></svg>
                                        <span>Buka via WhatsApp Web</span>
                                    </a>
                                </template>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="reminderModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" 
                                        :disabled="!currentTarget.studentPhone" 
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl text-xs font-bold transition-all shadow-sm shadow-emerald-600/20">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                    <span>Kirim Otomatis (Gateway)</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function weeklyMonitoringApp() {
            return {
                reminderModalOpen: false,
                currentTarget: {
                    id: '',
                    studentName: '',
                    studentNpm: '',
                    studentPhone: '',
                    p1Name: '',
                    p2Name: '',
                    p1Count: 0,
                    p2Count: 0
                },
                reminderMessage: '',
                weekRangeText: '{{ $startDate->locale("id")->isoFormat("D MMMM") }} - {{ $endDate->locale("id")->isoFormat("D MMMM Y") }}',

                openReminderModal(target) {
                    this.currentTarget = target;
                    
                    let missingNotice = '';
                    if (target.p1Count === 0 && target.p2Count === 0) {
                        missingNotice = `ke Pembimbing 1 (${target.p1Name}) maupun ke Pembimbing 2 (${target.p2Name})`;
                    } else if (target.p1Count === 0) {
                        missingNotice = `ke Pembimbing 1 (${target.p1Name})`;
                    } else {
                        missingNotice = `ke Pembimbing 2 (${target.p2Name})`;
                    }

                    this.reminderMessage = `Halo Sdr/i *${target.studentName}*,\n\nBerdasarkan pantauan sistem SIBIMA FASILKOM UNSUB untuk periode minggu ini (*${this.weekRangeText}*), Anda belum tercatat melakukan sesi bimbingan skripsi ${missingNotice}.\n\nSesuai standar akademik, mahasiswa diwajibkan melakukan bimbingan *minimal 1x setiap minggunya* baik ke Pembimbing 1 dan Pembimbing 2. Mohon segera berkoordinasi dan menjadwalkan sesi bimbingan dengan dosen pembimbing Anda.\n\nTetap semangat menyelesaikan tugas akhir Anda!`;

                    this.reminderModalOpen = true;
                },

                formatPhoneForWa(phone) {
                    if (!phone) return '';
                    let clean = phone.replace(/\D/g, '');
                    if (clean.startsWith('0')) {
                        clean = '62' + clean.substring(1);
                    }
                    return clean;
                }
            };
        }
    </script>
</x-app-layout>

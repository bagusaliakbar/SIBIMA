<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Monitoring Bimbingan', 'route' => route('monitoring.index')],
                ['label' => 'Radar Keaktifan & Leaderboard', 'route' => null]
            ]" />
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="mentoringActivityApp('{{ $activeTab }}')">
        
        <!-- FLASH MESSAGES -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs sm:text-sm font-semibold flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs sm:text-sm font-semibold flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs sm:text-sm font-semibold flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- TABS NAV -->
        @include('monitoring.partials.tabs')

        <!-- HERO SECTION & KPI CARDS -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="space-y-2 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Radar Keaktifan & Early Warning System</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 dark:text-white">
                        Radar Keaktifan & Peringkat Bimbingan
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Pantau civitas terajin yang konsisten melakukan bimbingan serta deteksi dini mahasiswa dan dosen yang pasif / berisiko mengalami keterlambatan skripsi. Evaluasi secara berkala dan kirim pengingat langsung.
                    </p>
                </div>

                <!-- Export Actions -->
                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    <a href="{{ route('monitoring.activity.export-excel', request()->query()) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs shadow-emerald-600/20 transition-all hover:scale-[1.02] active:scale-95">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Ekspor Excel</span>
                    </a>
                    <a href="{{ route('monitoring.activity.export-pdf', request()->query()) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs shadow-rose-600/20 transition-all hover:scale-[1.02] active:scale-95">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak PDF</span>
                    </a>
                </div>
            </div>

            <!-- 4 KPI SUMMARY CARDS -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4 pt-2">
                <!-- KPI 1: Total Sesi Terlaksana -->
                <div class="bg-gradient-to-br from-indigo-50 to-white dark:from-indigo-950/20 dark:to-slate-900 p-4 sm:p-5 rounded-2xl border border-indigo-100 dark:border-indigo-900/40 relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">Sesi Periode Ini</span>
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($kpi['total_sessions_in_period']) }}
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5 truncate" title="{{ $period_label }}">
                            {{ $period_label }}
                        </p>
                    </div>
                </div>

                <!-- KPI 2: Rata-Rata Sesi per Dosen -->
                <div class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-950/20 dark:to-slate-900 p-4 sm:p-5 rounded-2xl border border-blue-100 dark:border-blue-900/40 relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Rata-Rata Dosen</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">
                            {{ $kpi['avg_sessions_per_dosen'] }} <span class="text-xs sm:text-sm font-semibold text-slate-500">sesi</span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            Produktivitas rata-rata dosen
                        </p>
                    </div>
                </div>

                <!-- KPI 3: Juara Mahasiswa Terajin -->
                <div class="bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-950/20 dark:to-slate-900 p-4 sm:p-5 rounded-2xl border border-emerald-100 dark:border-emerald-900/40 relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Mahasiswa Teraktif</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-base sm:text-lg font-black text-slate-800 dark:text-white truncate" title="{{ $kpi['top_student_name'] }}">
                            {{ $kpi['top_student_name'] }}
                        </div>
                        <p class="text-[11px] sm:text-xs text-emerald-700 dark:text-emerald-400 font-bold mt-0.5">
                            {{ $kpi['top_student_sessions'] }} kali bimbingan selesai
                        </p>
                    </div>
                </div>

                <!-- KPI 4: Mahasiswa Pasif (Early Warning) -->
                <div class="bg-gradient-to-br from-rose-50 to-white dark:from-rose-950/20 dark:to-slate-900 p-4 sm:p-5 rounded-2xl border border-rose-100 dark:border-rose-900/40 relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400">Pasif / Kritis</span>
                        <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                            <span class="inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400">
                            {{ number_format($kpi['inactive_students_count']) }} <span class="text-xs sm:text-sm font-semibold text-slate-500">mhs</span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            > 14 hari tidak bimbingan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER TOOLBAR -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
            <form action="{{ route('monitoring.activity') }}" method="GET" class="space-y-4">
                <input type="hidden" name="tab" :value="activeMainTab">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <!-- Period Filter -->
                    <div>
                        <label for="period" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Rentang Periode</label>
                        <select id="period" name="period" onchange="this.form.submit()"
                                class="w-full py-2.5 pl-3 pr-8 rounded-xl border text-xs sm:text-sm bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white font-medium focus:ring-2 focus:ring-orange-500">
                            <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>Bulan Ini (Default)</option>
                            <option value="last_30_days" {{ $period === 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                            <option value="last_90_days" {{ $period === 'last_90_days' ? 'selected' : '' }}>90 Hari Terakhir</option>
                            <option value="this_semester" {{ $period === 'this_semester' ? 'selected' : '' }}>Semester Berjalan</option>
                            <option value="all_time" {{ $period === 'all_time' ? 'selected' : '' }}>Semua Waktu (Akumulasi)</option>
                            <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Kustom Rentang Tanggal</option>
                        </select>
                    </div>

                    <!-- Angkatan Mahasiswa Filter -->
                    <div>
                        <label for="cohort" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Angkatan Mahasiswa</label>
                        <select id="cohort" name="cohort" onchange="this.form.submit()"
                                class="w-full py-2.5 pl-3 pr-8 rounded-xl border text-xs sm:text-sm bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white font-medium focus:ring-2 focus:ring-orange-500">
                            <option value="">Semua Angkatan</option>
                            @foreach($cohortYears as $yr)
                                <option value="{{ $yr }}" {{ (string)$cohort === (string)$yr ? 'selected' : '' }}>Angkatan {{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="sm:col-span-2">
                        <label for="search" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pencarian Nama / NIM / NIDN</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 dark:text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" 
                                   id="search"
                                   name="search" 
                                   value="{{ $search }}" 
                                   placeholder="Ketik nama mahasiswa/dosen, NIM, atau judul skripsi..."
                                   class="w-full pl-10 pr-10 py-2.5 rounded-xl border text-xs sm:text-sm bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-orange-500 transition-all">
                            @if($search)
                                <a href="{{ route('monitoring.activity', ['period' => $period, 'cohort' => $cohort, 'tab' => $activeTab]) }}" 
                                   class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 dark:text-slate-400 hover:text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Custom Date Inputs if custom selected -->
                @if($period === 'custom')
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center gap-3 flex-wrap">
                        <span class="text-xs font-bold text-slate-500">Rentang Tanggal:</span>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="py-1.5 px-3 rounded-xl border text-xs bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700">
                        <span class="text-xs text-slate-500 dark:text-slate-400">s/d</span>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="py-1.5 px-3 rounded-xl border text-xs bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700">
                        <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white shadow-xs">Terapkan Tanggal</button>
                    </div>
                @endif
            </form>
        </div>

        <!-- MAIN TAB SWITCHER (MAHASISWA VS DOSEN) -->
        <div class="flex items-center justify-center sm:justify-start">
            <div class="inline-flex p-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700">
                <button type="button" 
                        @click="switchMainTab('mahasiswa')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer"
                        :class="activeMainTab === 'mahasiswa' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                    <span>Keaktifan Mahasiswa</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-black"
                          :class="activeMainTab === 'mahasiswa' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'">
                        {{ count($top_students) }}
                    </span>
                </button>

                <button type="button" 
                        @click="switchMainTab('dosen')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer"
                        :class="activeMainTab === 'dosen' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Keaktifan Dosen Pembimbing</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-black"
                          :class="activeMainTab === 'dosen' ? 'bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'">
                        {{ count($top_dosens) }}
                    </span>
                </button>
            </div>
        </div>

        @include('monitoring.partials.activity-student-tab')

        @include('monitoring.partials.activity-lecturer-tab')

        @include('monitoring.partials.activity-whatsapp-modal')
    </div>

    @include('monitoring.partials.activity-scripts')
</x-app-layout>

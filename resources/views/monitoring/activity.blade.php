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
                        <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Mahasiswa Teraktif 🥇</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-sm">
                            🏆
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
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                            </span>
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
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
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
                                   class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
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
                        <span class="text-xs text-slate-400">s/d</span>
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
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black"
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
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black"
                          :class="activeMainTab === 'dosen' ? 'bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'">
                        {{ count($top_dosens) }}
                    </span>
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: MAHASISWA ACTIVITY                 -->
        <!-- ========================================== -->
        <div x-show="activeMainTab === 'mahasiswa'" class="space-y-6">
            
            <!-- SUB-TAB TOGGLE: TERAJIN VS PASIF -->
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button type="button" 
                            @click="studentSubTab = 'terajin'"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer"
                            :class="studentSubTab === 'terajin' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                        🏆 Paling Rajin (Leaderboard Top)
                    </button>
                    <button type="button" 
                            @click="studentSubTab = 'pasif'"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer"
                            :class="studentSubTab === 'pasif' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                        <span>⚠️ Pasif & Kritis (Perlu Perhatian)</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black"
                              :class="studentSubTab === 'pasif' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300'">
                            {{ count($inactive_students) }}
                        </span>
                    </button>
                </div>

                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan data berdasarkan periode: <strong class="text-slate-800 dark:text-white">{{ $period_label }}</strong>
                </span>
            </div>

            <!-- SUB-TAB CONTENT A: MAHASISWA TERAJIN -->
            <div x-show="studentSubTab === 'terajin'" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
                @if(count($top_students) === 0)
                    <div class="p-12 text-center space-y-3">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada riwayat bimbingan mahasiswa pada periode ini.</p>
                        <p class="text-xs text-slate-500">Pilih opsi periode lain (seperti "Semua Waktu" atau "Semester Berjalan") untuk melihat riwayat lengkap.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-900/50 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="py-4 px-4 text-center w-14">Rank</th>
                                    <th class="py-4 px-5">Mahasiswa</th>
                                    <th class="py-4 px-4 text-center">Sesi Periode Ini</th>
                                    <th class="py-4 px-4 text-center">P1 / P2</th>
                                    <th class="py-4 px-4 text-center">Total Akumulasi</th>
                                    <th class="py-4 px-5">Dosen Pembimbing</th>
                                    <th class="py-4 px-4">Bimbingan Terakhir</th>
                                    <th class="py-4 px-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs text-slate-700 dark:text-slate-300">
                                @foreach($top_students as $idx => $s)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors {{ $idx < 3 ? 'bg-amber-50/20 dark:bg-amber-950/10' : '' }}">
                                        <!-- Rank -->
                                        <td class="py-4 px-4 text-center">
                                            @if($idx === 0)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-400 text-white font-black text-sm shadow-xs" title="Juara 1 Teraktif">🥇</span>
                                            @elseif($idx === 1)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white font-black text-sm shadow-xs" title="Juara 2 Teraktif">🥈</span>
                                            @elseif($idx === 2)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-700 text-white font-black text-sm shadow-xs" title="Juara 3 Teraktif">🥉</span>
                                            @else
                                                <span class="font-bold text-slate-500 text-xs">{{ $idx + 1 }}</span>
                                            @endif
                                        </td>

                                        <!-- Profile -->
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white">
                                                    <img src="{{ $s['avatar_url'] }}" alt="{{ $s['name'] }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-900 dark:text-white block truncate max-w-[200px] sm:max-w-xs" title="{{ $s['name'] }}">
                                                        {{ $s['name'] }}
                                                    </span>
                                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                                                        <span>{{ $s['identifier'] ?: '-' }}</span>
                                                        <span>•</span>
                                                        <span>Angkatan {{ $s['entry_year'] ?: '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Sesi Periode Ini -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span>{{ $s['sessions_in_period'] }} Sesi</span>
                                            </span>
                                        </td>

                                        <!-- Breakdown P1 / P2 -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 font-semibold text-slate-700 dark:text-slate-300 text-[11px]">
                                                {{ $s['p1_sessions'] }} P1 / {{ $s['p2_sessions'] }} P2
                                            </span>
                                        </td>

                                        <!-- Total Akumulasi -->
                                        <td class="py-4 px-4 text-center font-bold text-slate-800 dark:text-slate-200">
                                            {{ $s['total_all_time'] }}x
                                        </td>

                                        <!-- Pembimbing -->
                                        <td class="py-4 px-5">
                                            <div class="text-[11px] space-y-0.5 max-w-xs">
                                                <p class="truncate" title="Pembimbing 1: {{ $s['pembimbing1_name'] }}">
                                                    <span class="font-bold text-slate-500">P1:</span> {{ $s['pembimbing1_name'] ?: '-' }}
                                                </p>
                                                <p class="truncate text-slate-500" title="Pembimbing 2: {{ $s['pembimbing2_name'] }}">
                                                    <span class="font-bold text-slate-500">P2:</span> {{ $s['pembimbing2_name'] ?: '-' }}
                                                </p>
                                            </div>
                                        </td>

                                        <!-- Bimbingan Terakhir -->
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @if($s['last_session_at'])
                                                <span class="font-semibold text-slate-800 dark:text-slate-200 block text-[11px]">
                                                    {{ \Carbon\Carbon::parse($s['last_session_at'])->diffForHumans() }}
                                                </span>
                                                <span class="text-[10px] text-slate-400">
                                                    {{ \Carbon\Carbon::parse($s['last_session_at'])->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">-</span>
                                            @endif
                                        </td>

                                        <!-- Status Keaktifan -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-{{ $s['health_color'] }}-50 dark:bg-{{ $s['health_color'] }}-950/70 text-{{ $s['health_color'] }}-700 dark:text-{{ $s['health_color'] }}-300 border border-{{ $s['health_color'] }}-200 dark:border-{{ $s['health_color'] }}-800/60">
                                                {{ $s['health_status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- SUB-TAB CONTENT B: MAHASISWA PASIF & KRITIS (EARLY WARNING) -->
            <div x-show="studentSubTab === 'pasif'" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
                <div class="p-5 bg-rose-50/60 dark:bg-rose-950/20 border-b border-rose-100 dark:border-rose-900/40 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-rose-900 dark:text-rose-200">Radar Peringatan Dini: Mahasiswa Terancam Mandek</h3>
                            <p class="text-xs text-rose-700 dark:text-rose-400">Daftar mahasiswa aktif yang sudah lebih dari 14 hari tidak melakukan bimbingan atau belum pernah bimbingan sama sekali.</p>
                        </div>
                    </div>
                </div>

                @if(count($inactive_students) === 0)
                    <div class="p-12 text-center space-y-3">
                        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">Luar biasa! Tidak ada mahasiswa yang pasif pada kriteria ini.</p>
                        <p class="text-xs text-slate-500">Semua mahasiswa aktif telah melaksanakan bimbingan dalam 14 hari terakhir.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-900/50 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="py-4 px-4 text-center w-12">No</th>
                                    <th class="py-4 px-5">Mahasiswa</th>
                                    <th class="py-4 px-4 text-center">Masa Inaktif (Hari)</th>
                                    <th class="py-4 px-4">Status & Sesi Terakhir</th>
                                    <th class="py-4 px-5">Dosen Pembimbing</th>
                                    <th class="py-4 px-5">Judul Skripsi</th>
                                    <th class="py-4 px-5 text-right">Aksi Tindak Lanjut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs text-slate-700 dark:text-slate-300">
                                @foreach($inactive_students as $idx => $s)
                                    <tr class="hover:bg-rose-50/30 dark:hover:bg-rose-950/10 transition-colors">
                                        <td class="py-4 px-4 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                        
                                        <!-- Profile -->
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white">
                                                    <img src="{{ $s['avatar_url'] }}" alt="{{ $s['name'] }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-900 dark:text-white block truncate max-w-[180px] sm:max-w-xs" title="{{ $s['name'] }}">
                                                        {{ $s['name'] }}
                                                    </span>
                                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 font-mono">
                                                        <span>{{ $s['identifier'] ?: '-' }}</span>
                                                        <span>•</span>
                                                        <span class="{{ $s['entry_year'] && $s['entry_year'] <= (now()->year - 4) ? 'text-rose-600 font-bold' : '' }}">
                                                            Angkatan {{ $s['entry_year'] ?: '-' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Inactive Days -->
                                        <td class="py-4 px-4 text-center">
                                            @if($s['days_since_last'] !== null)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 shadow-2xs">
                                                    {{ $s['days_since_last'] }} Hari
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                                    Belum Pernah Sesi
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Status & Sesi Terakhir -->
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 mb-1">
                                                {{ $s['stage'] }}
                                            </span>
                                            <p class="text-[11px] text-slate-500">
                                                {{ $s['last_session_at'] ? \Carbon\Carbon::parse($s['last_session_at'])->format('d M Y') : 'Nol Bimbingan' }}
                                            </p>
                                        </td>

                                        <!-- Pembimbing -->
                                        <td class="py-4 px-5">
                                            <div class="text-[11px] space-y-0.5 max-w-xs">
                                                <p class="truncate" title="Pembimbing 1: {{ $s['pembimbing1_name'] }}">P1: {{ $s['pembimbing1_name'] ?: '-' }}</p>
                                                <p class="truncate text-slate-500" title="Pembimbing 2: {{ $s['pembimbing2_name'] }}">P2: {{ $s['pembimbing2_name'] ?: '-' }}</p>
                                            </div>
                                        </td>

                                        <!-- Judul Skripsi -->
                                        <td class="py-4 px-5">
                                            <p class="font-medium text-slate-800 dark:text-slate-200 line-clamp-2 max-w-sm text-xs leading-snug" title="{{ $s['title'] }}">
                                                {{ $s['title'] }}
                                            </p>
                                        </td>

                                        <!-- Aksi WA -->
                                        <td class="py-4 px-5 text-right whitespace-nowrap">
                                            <button type="button" 
                                                    @click="openReminderModal('student', {{ $s['student_id'] }}, '{{ addslashes($s['name']) }}', '{{ $s['phone'] }}', {{ $s['thesis_id'] }}, {{ $s['days_since_last'] ?: 0 }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs shadow-emerald-600/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                <span>Kirim Pengingat WA</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        <!-- ========================================== -->
        <!-- TAB 2: DOSEN PEMBIMBING ACTIVITY           -->
        <!-- ========================================== -->
        <div x-show="activeMainTab === 'dosen'" class="space-y-6">
            
            <!-- SUB-TAB TOGGLE: TERAJIN VS PERLU PERHATIAN -->
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button type="button" 
                            @click="dosenSubTab = 'terajin'"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer"
                            :class="dosenSubTab === 'terajin' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                        🏆 Dosen Paling Rajin (Leaderboard)
                    </button>
                    <button type="button" 
                            @click="dosenSubTab = 'pasif'"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer"
                            :class="dosenSubTab === 'pasif' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                        <span>⚠️ Perlu Perhatian / Pasif</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black"
                              :class="dosenSubTab === 'pasif' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'">
                            {{ count($inactive_dosens) }}
                        </span>
                    </button>
                </div>

                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan data dosen aktif berdasarkan periode: <strong class="text-slate-800 dark:text-white">{{ $period_label }}</strong>
                </span>
            </div>

            <!-- SUB-TAB CONTENT A: DOSEN TERAJIN -->
            <div x-show="dosenSubTab === 'terajin'" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
                @if(count($top_dosens) === 0)
                    <div class="p-12 text-center space-y-3">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada sesi bimbingan dosen tercatat pada periode ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-900/50 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="py-4 px-4 text-center w-14">Rank</th>
                                    <th class="py-4 px-5">Dosen Pembimbing</th>
                                    <th class="py-4 px-4 text-center">Sesi Periode Ini</th>
                                    <th class="py-4 px-4 text-center">Mhs Dibimbing (Periode)</th>
                                    <th class="py-4 px-4 text-center">Total Mhs Aktif</th>
                                    <th class="py-4 px-4 text-center">Total Akumulasi</th>
                                    <th class="py-4 px-4">Bimbingan Terakhir</th>
                                    <th class="py-4 px-4 text-center">Kategori Keaktifan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs text-slate-700 dark:text-slate-300">
                                @foreach($top_dosens as $idx => $d)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors {{ $idx < 3 ? 'bg-blue-50/20 dark:bg-blue-950/10' : '' }}">
                                        <!-- Rank -->
                                        <td class="py-4 px-4 text-center">
                                            @if($idx === 0)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-400 text-white font-black text-sm shadow-xs" title="Juara 1 Dosen Teraktif">🥇</span>
                                            @elseif($idx === 1)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white font-black text-sm shadow-xs" title="Juara 2 Dosen Teraktif">🥈</span>
                                            @elseif($idx === 2)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-700 text-white font-black text-sm shadow-xs" title="Juara 3 Dosen Teraktif">🥉</span>
                                            @else
                                                <span class="font-bold text-slate-500 text-xs">{{ $idx + 1 }}</span>
                                            @endif
                                        </td>

                                        <!-- Profile -->
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white">
                                                    <img src="{{ $d['avatar_url'] }}" alt="{{ $d['name'] }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-900 dark:text-white block truncate max-w-[200px] sm:max-w-xs" title="{{ $d['name'] }}">
                                                        {{ $d['name'] }}
                                                    </span>
                                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 font-mono">
                                                        <span>{{ $d['identifier'] ?: '-' }}</span>
                                                        <span>•</span>
                                                        <span>{{ $d['email'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Sesi Periode Ini -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 shadow-2xs">
                                                {{ $d['sessions_in_period'] }} Sesi
                                            </span>
                                        </td>

                                        <!-- Mhs Dibimbing Periode Ini -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $d['unique_students_in_period'] }} Mhs</span>
                                        </td>

                                        <!-- Total Mhs Aktif Dibimbing -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 font-semibold text-slate-700 dark:text-slate-300 text-[11px]">
                                                {{ $d['supervised_count'] }} Mahasiswa
                                            </span>
                                        </td>

                                        <!-- Total Akumulasi Sesi -->
                                        <td class="py-4 px-4 text-center font-bold text-slate-800 dark:text-slate-200">
                                            {{ $d['total_all_time'] }}x
                                        </td>

                                        <!-- Sesi Terakhir -->
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @if($d['last_session_at'])
                                                <span class="font-semibold text-slate-800 dark:text-slate-200 block text-[11px]">
                                                    {{ \Carbon\Carbon::parse($d['last_session_at'])->diffForHumans() }}
                                                </span>
                                                <span class="text-[10px] text-slate-400">
                                                    {{ \Carbon\Carbon::parse($d['last_session_at'])->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">-</span>
                                            @endif
                                        </td>

                                        <!-- Kategori Keaktifan -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-{{ $d['activity_color'] }}-50 dark:bg-{{ $d['activity_color'] }}-950/70 text-{{ $d['activity_color'] }}-700 dark:text-{{ $d['activity_color'] }}-300 border border-{{ $d['activity_color'] }}-200 dark:border-{{ $d['activity_color'] }}-800/60">
                                                {{ $d['activity_level'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- SUB-TAB CONTENT B: DOSEN PERLU PERHATIAN (PASIF) -->
            <div x-show="dosenSubTab === 'pasif'" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
                <div class="p-5 bg-amber-50/60 dark:bg-amber-950/20 border-b border-amber-100 dark:border-amber-900/40 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-amber-900 dark:text-amber-200">Dosen Pembimbing yang Perlu Koordinasi / Pengingat</h3>
                            <p class="text-xs text-amber-700 dark:text-amber-400">Dosen yang memiliki mahasiswa bimbingan aktif namun belum tercatat membimbing dalam periode ini (> 14 hari tanpa sesi).</p>
                        </div>
                    </div>
                </div>

                @if(count($inactive_dosens) === 0)
                    <div class="p-12 text-center space-y-3">
                        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">Seluruh Dosen Pembimbing aktif membimbing mahasiswa!</p>
                        <p class="text-xs text-slate-500">Tidak ada dosen dengan mahasiswa aktif yang mengalami stagnasi bimbingan.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-900/50 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="py-4 px-4 text-center w-12">No</th>
                                    <th class="py-4 px-5">Dosen Pembimbing</th>
                                    <th class="py-4 px-4 text-center">Mahasiswa Menunggu</th>
                                    <th class="py-4 px-4 text-center">Sesi Periode Ini</th>
                                    <th class="py-4 px-4 text-center">Hari Sejak Bimbingan Terakhir</th>
                                    <th class="py-4 px-4">Tanggal Terakhir</th>
                                    <th class="py-4 px-5 text-right">Aksi Koordinasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs text-slate-700 dark:text-slate-300">
                                @foreach($inactive_dosens as $idx => $d)
                                    <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/10 transition-colors">
                                        <td class="py-4 px-4 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                        
                                        <!-- Profile -->
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white">
                                                    <img src="{{ $d['avatar_url'] }}" alt="{{ $d['name'] }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-900 dark:text-white block truncate max-w-[200px] sm:max-w-xs" title="{{ $d['name'] }}">
                                                        {{ $d['name'] }}
                                                    </span>
                                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 font-mono">
                                                        <span>{{ $d['identifier'] ?: '-' }}</span>
                                                        <span>•</span>
                                                        <span>{{ $d['email'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Mhs Menunggu -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs">
                                                {{ $d['supervised_count'] }} Mahasiswa
                                            </span>
                                        </td>

                                        <!-- Sesi Periode Ini -->
                                        <td class="py-4 px-4 text-center font-bold text-slate-500">
                                            {{ $d['sessions_in_period'] }}
                                        </td>

                                        <!-- Inactivity Duration -->
                                        <td class="py-4 px-4 text-center">
                                            @if($d['days_since_last'] !== null)
                                                <span class="text-xs font-bold text-amber-700 dark:text-amber-400">
                                                    {{ $d['days_since_last'] }} Hari Lalu
                                                </span>
                                            @else
                                                <span class="text-xs font-bold text-rose-600">Belum Pernah Sesi</span>
                                            @endif
                                        </td>

                                        <!-- Tanggal Terakhir -->
                                        <td class="py-4 px-4 whitespace-nowrap text-xs text-slate-500">
                                            {{ $d['last_session_at'] ? \Carbon\Carbon::parse($d['last_session_at'])->format('d M Y') : '-' }}
                                        </td>

                                        <!-- Aksi WA -->
                                        <td class="py-4 px-5 text-right whitespace-nowrap">
                                            <button type="button" 
                                                    @click="openReminderModal('dosen', {{ $d['id'] }}, '{{ addslashes($d['name']) }}', '{{ $d['phone'] }}', null, {{ $d['days_since_last'] ?: 0 }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-xs shadow-amber-600/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                <span>Kirim Pengingat WA</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        <!-- ========================================== -->
        <!-- WHATSAPP REMINDER INTERACTIVE MODAL        -->
        <!-- ========================================== -->
        <div x-show="modalOpen" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true"
             x-cloak>
            
            <!-- Backdrop -->
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="closeReminderModal()"
                 class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

            <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
                <div x-show="modalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all w-full max-w-lg border border-slate-200 dark:border-slate-700">

                    <form action="{{ route('monitoring.activity.remind') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" :value="modalTargetType">
                        <input type="hidden" name="user_id" :value="modalUserId">
                        <input type="hidden" name="thesis_id" :value="modalThesisId">
                        <input type="hidden" name="days_inactive" :value="modalDaysInactive">

                        <!-- Header -->
                        <div class="px-6 py-5 bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Kirim Pengingat WhatsApp</h3>
                                    <p class="text-xs text-slate-500">Pemberitahuan resmi prodi ke penerima</p>
                                </div>
                            </div>
                            <button type="button" @click="closeReminderModal()" class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 space-y-4 text-xs">
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 space-y-1">
                                <p class="text-slate-500">Penerima:</p>
                                <p class="font-bold text-slate-900 dark:text-white text-sm" x-text="modalTargetName"></p>
                                <p class="text-slate-500 font-mono" x-text="'No. WA: ' + (modalTargetPhone || 'Belum diatur')"></p>
                            </div>

                            <div>
                                <label for="message" class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pesan Pengingat (Dapat Diedit)</label>
                                <textarea id="message" 
                                          name="message" 
                                          rows="6" 
                                          x-model="modalMessage"
                                          class="w-full p-3 rounded-xl border text-xs bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 font-sans leading-relaxed"></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
                            <template x-if="modalTargetPhone">
                                <a :href="'https://wa.me/' + modalTargetPhone.replace(/[^0-9]/g, '') + '?text=' + encodeURIComponent(modalMessage)" 
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-emerald-600 transition-colors">
                                    <span>Buka di Web WhatsApp</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </template>

                            <div class="flex items-center gap-2 ml-auto">
                                <button type="button" @click="closeReminderModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-200 hover:bg-slate-300 text-slate-700">Batal</button>
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                    <span>Kirim Otomatis</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function mentoringActivityApp(initialTab) {
            return {
                activeMainTab: initialTab || 'mahasiswa',
                studentSubTab: 'terajin',
                dosenSubTab: 'terajin',
                
                modalOpen: false,
                modalTargetType: 'student',
                modalUserId: null,
                modalTargetName: '',
                modalTargetPhone: '',
                modalThesisId: null,
                modalDaysInactive: 0,
                modalMessage: '',

                switchMainTab(tab) {
                    this.activeMainTab = tab;
                    // update URL param without full reload
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tab);
                    window.history.replaceState({}, '', url);
                },

                openReminderModal(type, userId, name, phone, thesisId, daysInactive) {
                    this.modalTargetType = type;
                    this.modalUserId = userId;
                    this.modalTargetName = name;
                    this.modalTargetPhone = phone || '';
                    this.modalThesisId = thesisId;
                    this.modalDaysInactive = daysInactive;

                    if (type === 'dosen') {
                        const daysText = daysInactive > 0 ? `dalam ${daysInactive} hari terakhir` : 'pada periode ini';
                        this.modalMessage = `Yth. Bapak/Ibu ${name},\n\nSalam hangat dari Program Studi FASILKOM UNSUB.\n\nBerdasarkan pantauan sistem SIBIMA, belum tercatat aktivitas sesi bimbingan skripsi dengan mahasiswa bimbingan aktif Bapak/Ibu ${daysText}.\n\nMohon kesediaan Bapak/Ibu untuk memeriksa pengajuan jadwal atau mengoordinasikan sesi bimbingan bersama mahasiswa bimbingan agar progres skripsi mereka tetap berjalan lancar.\n\nTerima kasih banyak atas perhatian dan dedikasi Bapak/Ibu.`;
                    } else {
                        const daysText = daysInactive > 0 ? `selama ${daysInactive} hari terakhir` : 'sejak pengajuan skripsi';
                        this.modalMessage = `Halo Sdr/i ${name},\n\nBerdasarkan pantauan radar keaktifan bimbingan SIBIMA FASILKOM UNSUB, Anda tercatat belum melakukan sesi bimbingan skripsi ${daysText}.\n\nKami mengingatkan agar Anda SEGERA berinisiatif menghubungi Dosen Pembimbing untuk mengajukan jadwal dan berkonsultasi mengenai kelanjutan tugas akhir Anda.\n\nJangan biarkan skripsi Anda tertunda. Tetap semangat menyelesaikan studi!`;
                    }

                    this.modalOpen = true;
                },

                closeReminderModal() {
                    this.modalOpen = false;
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

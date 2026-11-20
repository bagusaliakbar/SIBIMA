        <!-- TOP KPI SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <!-- KPI 1: Total Mahasiswa -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-orange-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Mahasiswa</span>
                    <div class="w-8 h-8 rounded-lg bg-orange-500/10 text-orange-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ number_format($kpi['totalStudents']) }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="font-bold text-orange-600 dark:text-orange-400">{{ $kpi['activeTheses'] }} Aktif</span>
                    <span>•</span>
                    <span class="font-bold text-slate-600 dark:text-slate-300">{{ $kpi['completedTheses'] }} Lulus</span>
                </div>
            </div>

            <!-- KPI 2: Status Seminar -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-emerald-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Progres Seminar</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ number_format($kpi['seminarDone']) }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Sudah Seminar</span>
                    <span>•</span>
                    <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $kpi['seminarPending'] }} Belum</span>
                </div>
            </div>

            <!-- KPI 3: Status Sidang Akhir -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-indigo-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Sidang Skripsi</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ number_format($kpi['defenseDone']) }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">Sudah Sidang</span>
                    <span>•</span>
                    <span class="text-slate-500 dark:text-slate-400 font-bold">{{ $kpi['defensePending'] }} Belum</span>
                </div>
            </div>

            <!-- KPI 4: Sesi Bimbingan Selesai -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-cyan-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Sesi Bimbingan</span>
                    <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ number_format($kpi['completedSessions']) }}</div>
                <div class="mt-1 text-[11px] text-cyan-600 dark:text-cyan-400 font-bold">
                    Sesi Terlaksana & Disetujui
                </div>
            </div>

            <!-- KPI 5: Lulus Tepat Waktu -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-violet-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Tepat Waktu</span>
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 text-violet-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $kpi['onTimePercentage'] }}%</div>
                <div class="mt-1 text-[11px] text-violet-600 dark:text-violet-400 font-bold">
                    Masa Studi ≤ 4 Tahun
                </div>
            </div>

            <!-- KPI 6: Mahasiswa Kritis -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-rose-500/10 rounded-full blur-xl pointer-events-none transition-all group-hover:scale-150"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Perlu Perhatian</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-500 flex items-center justify-center text-xs font-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-rose-600 dark:text-rose-400 tracking-tight">{{ $kpi['criticalMentoringStudents'] + $kpi['criticalCohortStudents'] }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="text-rose-500 dark:text-rose-400 font-bold">{{ $kpi['criticalMentoringStudents'] }} Bimb. Pasif</span>
                    <span>•</span>
                    <span class="text-amber-500 dark:text-amber-400 font-bold">{{ $kpi['criticalCohortStudents'] }} > 4 Thn</span>
                </div>
            </div>
        </div>

        <!-- STATISTICAL METRIC SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Metric 1: Total Arsip -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-4 hover:border-orange-200 dark:hover:border-orange-800/60 transition-all group">
                <div class="w-12 h-12 rounded-2xl bg-orange-500/10 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Total Arsip Skripsi</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ number_format($stats['total']) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Judul</span></h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">Database Arsip Alumni</p>
                </div>
            </div>

            <!-- Metric 2: Rentang Angkatan -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-4 hover:border-indigo-200 dark:hover:border-indigo-800/60 transition-all group">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Rentang Angkatan</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $stats['year_range'] }}</h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ $stats['total_years'] }} Angkatan Terdaftar</p>
                </div>
            </div>

            <!-- Metric 3: Topik Populer -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-4 hover:border-emerald-200 dark:hover:border-emerald-800/60 transition-all group">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Topik Terpopuler</p>
                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 tracking-tight truncate max-w-[170px]" title="{{ $stats['top_topic'] }}">{{ $stats['top_topic'] }}</h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ $stats['top_topic_count'] }} Judul Terkait</p>
                </div>
            </div>

            <!-- Metric 4: Pembimbing Terdata -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-4 hover:border-purple-200 dark:hover:border-purple-800/60 transition-all group">
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Dosen Pembimbing</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $stats['total_advisors'] }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Dosen</span></h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">Riwayat Pembimbingan</p>
                </div>
            </div>
        </div>

<div class="bg-white dark:bg-slate-800 p-1.5 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 flex flex-wrap items-center gap-1.5 mb-6">
    <!-- Tab 1: Rekap Progres Mahasiswa -->
    <a href="{{ route('monitoring.index') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 {{ request()->routeIs('monitoring.index') ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-700/50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('monitoring.index') ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <span>Rekap Progres Mahasiswa</span>
    </a>

    <!-- Tab 2: Monitoring Bimbingan Mingguan -->
    <a href="{{ route('monitoring.weekly') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 {{ request()->routeIs('monitoring.weekly') ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-700/50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('monitoring.weekly') ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <span>Monitoring Mingguan</span>
        <span class="px-2 py-0.5 text-[10px] font-black rounded-full uppercase tracking-wider {{ request()->routeIs('monitoring.weekly') ? 'bg-white/20 text-white' : 'bg-orange-100 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 border border-orange-200/60 dark:border-orange-800/40' }}">
            Wajib P1 & P2
        </span>
    </a>

    <!-- Tab 3: Mahasiswa Masa Studi Kritikal -->
    <a href="{{ route('monitoring.critical') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 {{ request()->routeIs('monitoring.critical') ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-700/50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('monitoring.critical') ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span>Masa Studi Kritikal</span>
    </a>

    <!-- Tab 4: Laporan Lanjutan -->
    <a href="{{ route('monitoring.advanced-reporting') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 {{ request()->routeIs('monitoring.advanced-reporting') ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-700/50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('monitoring.advanced-reporting') ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <span>Laporan Lanjutan</span>
    </a>
</div>

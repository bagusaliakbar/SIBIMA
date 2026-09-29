<div class="bg-white dark:bg-slate-800 p-1.5 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 flex flex-wrap items-center gap-1.5 mb-6">
    <!-- Tab 1: Log Aktivitas Sistem -->
    <a href="{{ route('admin.logs') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 {{ request()->routeIs('admin.logs') ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-700/50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('admin.logs') ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <span>Log Aktivitas Sistem</span>
    </a>

    <!-- Tab 2: Peringkat Keaktifan Login -->
    <a href="{{ route('admin.logs.login-activity') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 {{ request()->routeIs('admin.logs.login-activity*') ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-700/50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('admin.logs.login-activity*') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
        </svg>
        <span>Peringkat Keaktifan Login</span>
        <span class="px-1.5 py-0.5 rounded-md text-[10px] uppercase tracking-wider font-extrabold {{ request()->routeIs('admin.logs.login-activity*') ? 'bg-white/20 text-white' : 'bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300' }}">
            Monitoring
        </span>
    </a>
</div>

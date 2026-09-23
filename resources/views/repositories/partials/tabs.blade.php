<div class="bg-white dark:bg-slate-800 p-1.5 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 flex flex-wrap items-center gap-1.5 mb-6">
    <!-- Tab 1: Skripsi Fasilkom & UNSUB -->
    <a href="{{ route('repositories.index') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 {{ request()->routeIs('repositories.index') ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-700/50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('repositories.index') ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
        <span>Skripsi Fasilkom & UNSUB</span>
    </a>

    <!-- Tab 2: Jurnal Ilmiah Open Access (External OpenAlex) -->
    <a href="{{ route('repositories.journals') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 {{ request()->routeIs('repositories.journals') ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-700/50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('repositories.journals') ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
        </svg>
        <span>Jurnal Ilmiah (Open Access)</span>
    </a>

    <!-- Tab 3: Buku & E-Book Pustaka Digital (Fokus Sistem Informasi) -->
    <a href="{{ route('repositories.books') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 {{ request()->routeIs('repositories.books') ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-700/50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('repositories.books') ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
        <span>Buku & E-Book (Open Access)</span>
    </a>

    <!-- Tab 3: Daftar Bacaan Saya (Reading List) -->
    @php
        $myBookmarksTotal = auth()->check() ? auth()->user()->journalBookmarks()->count() : 0;
    @endphp
    <a href="{{ route('repositories.bookmarks') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 {{ request()->routeIs('repositories.bookmarks') ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-700/50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('repositories.bookmarks') ? 'text-white' : 'text-amber-500' }}" fill="{{ request()->routeIs('repositories.bookmarks') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
        </svg>
        <span>Daftar Bacaan Saya</span>
        @if($myBookmarksTotal > 0)
            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black {{ request()->routeIs('repositories.bookmarks') ? 'bg-white/25 text-white' : 'bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60' }}">
                {{ $myBookmarksTotal }}
            </span>
        @endif
    </a>
</div>

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Terjadi Kesalahan') - {{ config('app.name', 'SIBIMA') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-grid-pattern {
            background-size: 32px 32px;
            background-image: radial-gradient(rgba(148, 163, 184, 0.15) 1px, transparent 1px);
        }
        .dark .bg-grid-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1px, transparent 1px);
        }
    </style>
    
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body x-data="{ 
        darkMode: document.documentElement.classList.contains('dark'),
        toggleDark() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            }
        }
      }"
      class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300 relative flex flex-col justify-between items-center p-4 sm:p-6 overflow-x-hidden selection:bg-orange-500/20 selection:text-orange-600">

    <!-- Ambient Floating Aurora Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[25%] -left-[15%] w-[65vw] h-[65vw] max-w-[700px] max-h-[700px] rounded-full bg-gradient-to-tr from-orange-500/15 via-amber-500/10 to-rose-500/15 blur-[120px] dark:from-orange-600/15 dark:via-amber-600/10 dark:to-rose-600/15 animate-pulse"></div>
        <div class="absolute -bottom-[25%] -right-[15%] w-[65vw] h-[65vw] max-w-[700px] max-h-[700px] rounded-full bg-gradient-to-tr from-indigo-500/15 via-purple-500/10 to-blue-500/15 blur-[120px] dark:from-indigo-600/15 dark:via-purple-600/10 dark:to-blue-600/15"></div>
        <div class="absolute inset-0 bg-grid-pattern pointer-events-none opacity-80"></div>
    </div>

    <!-- Header Bar (Logo & Darkmode Switcher) -->
    <header class="w-full max-w-5xl mx-auto flex items-center justify-between z-10 py-2">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 text-white flex items-center justify-center font-black shadow-md shadow-orange-500/20 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <span class="text-sm font-black tracking-wider text-slate-900 dark:text-white uppercase flex items-center gap-1.5">
                    SIBIMA
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </span>
                <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest block -mt-0.5">Universitas Subang</span>
            </div>
        </a>

        <!-- Dark Mode Toggle Button -->
        <button @click="toggleDark()" 
                type="button"
                aria-label="Toggle Dark Mode"
                class="p-2.5 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800 text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 shadow-sm backdrop-blur-md transition-all hover:scale-105 active:scale-95 cursor-pointer">
            <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            <svg x-show="darkMode" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </button>
    </header>

    <!-- Main Central Card Container -->
    <main class="w-full max-w-xl my-auto py-8 z-10">
        <div class="relative bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-8 sm:p-12 shadow-2xl shadow-slate-200/50 dark:shadow-black/70 text-center overflow-hidden transition-all duration-300">
            
            <!-- Top Light Accent Bar -->
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-orange-500 via-amber-500 to-rose-500"></div>
            
            <!-- Icon Wrapper -->
            <div class="mb-5 inline-flex items-center justify-center p-4 sm:p-5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700/60 shadow-inner group hover:scale-110 transition-transform duration-300">
                @yield('icon')
            </div>

            <!-- Error Code Banner -->
            <div class="relative select-none mb-1">
                <div class="text-[5.5rem] sm:text-[7.5rem] font-black leading-none tracking-tighter bg-gradient-to-b from-slate-900 via-slate-800 to-slate-700 dark:from-white dark:via-slate-200 dark:to-slate-400 bg-clip-text text-transparent opacity-95">
                    @yield('code')
                </div>
                <!-- Ambient Code Shadow -->
                <div class="absolute inset-0 flex items-center justify-center blur-2xl opacity-20 dark:opacity-30 pointer-events-none bg-gradient-to-r from-orange-500 to-rose-500 bg-clip-text text-transparent text-[5.5rem] sm:text-[7.5rem] font-black">
                    @yield('code')
                </div>
            </div>

            <!-- Status Pill Badge -->
            <div class="mb-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/80">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-ping"></span>
                    @yield('status_badge', 'HTTP Status')
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase mb-3">
                @yield('title')
            </h1>

            <!-- Message Description -->
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 font-medium leading-relaxed max-w-md mx-auto mb-8">
                @yield('message')
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full">
                @yield('actions')
            </div>

            <!-- Optional Extra Helpful Links -->
            @hasSection('extra')
                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800/80 text-left">
                    @yield('extra')
                </div>
            @endif
        </div>
    </main>

    <!-- Footer Copyright & University Subtitle -->
    <footer class="w-full max-w-5xl mx-auto py-4 text-center z-10">
        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
            Sistem Informasi Bimbingan Mahasiswa &bull; &copy; {{ date('Y') }} Universitas Subang
        </p>
    </footer>
</body>
</html>

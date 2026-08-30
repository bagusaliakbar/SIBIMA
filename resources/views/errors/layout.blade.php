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
            background-image: radial-gradient(rgba(148, 163, 184, 0.18) 1px, transparent 1px);
        }
        .dark .bg-grid-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
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
      class="min-h-screen bg-slate-100/70 dark:bg-[#0b0f19] text-slate-800 dark:text-slate-100 transition-colors duration-300 relative flex flex-col justify-between items-center p-4 sm:p-6 overflow-x-hidden selection:bg-orange-500/20 selection:text-orange-600">

    <!-- Ambient Glowing Aurora Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[20%] -left-[10%] w-[60vw] h-[60vw] max-w-[650px] max-h-[650px] rounded-full bg-gradient-to-tr from-orange-500/20 via-amber-500/15 to-rose-500/20 blur-[130px] dark:from-orange-600/20 dark:via-amber-600/15 dark:to-rose-600/20 animate-pulse"></div>
        <div class="absolute -bottom-[20%] -right-[10%] w-[60vw] h-[60vw] max-w-[650px] max-h-[650px] rounded-full bg-gradient-to-tr from-indigo-500/20 via-purple-500/15 to-blue-500/20 blur-[130px] dark:from-indigo-600/20 dark:via-purple-600/15 dark:to-blue-600/20"></div>
        <div class="absolute inset-0 bg-grid-pattern pointer-events-none"></div>
    </div>

    <!-- Header Navigation -->
    <header class="w-full max-w-4xl mx-auto flex items-center justify-between z-10 py-3">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 text-white flex items-center justify-center font-black shadow-md shadow-orange-500/25 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <span class="text-sm font-black tracking-wider text-slate-900 dark:text-white uppercase flex items-center gap-1.5 leading-none">
                    SIBIMA
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </span>
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest block mt-0.5">Universitas Subang</span>
            </div>
        </a>

        <!-- Dark Mode Toggle Button -->
        <button @click="toggleDark()" 
                type="button"
                aria-label="Toggle Dark Mode"
                class="p-2.5 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 shadow-sm backdrop-blur-md transition-all hover:scale-105 active:scale-95 cursor-pointer">
            <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            <svg x-show="darkMode" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </button>
    </header>

    <!-- Main Central Card -->
    <main class="w-full max-w-xl my-auto py-6 z-10">
        <div class="relative bg-white/95 dark:bg-slate-900/90 backdrop-blur-2xl border border-slate-200/90 dark:border-slate-800 rounded-3xl p-8 sm:p-12 shadow-2xl shadow-slate-300/40 dark:shadow-[0_20px_60px_rgba(0,0,0,0.8)] text-center overflow-hidden transition-all duration-300">
            
            <!-- Top Gradient Accent Bar -->
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-orange-500 via-amber-500 to-rose-500"></div>

            <!-- Error Code Hero Typography -->
            <div class="relative select-none my-1">
                <div class="text-7xl sm:text-9xl font-black leading-none tracking-tighter bg-gradient-to-r from-orange-500 via-amber-400 to-rose-500 dark:from-orange-400 dark:via-amber-300 dark:to-rose-400 bg-clip-text text-transparent drop-shadow-sm dark:drop-shadow-[0_10px_35px_rgba(249,115,22,0.3)]">
                    @yield('code')
                </div>
            </div>

            <!-- Status Pill Badge -->
            <div class="mb-4">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-orange-100 dark:bg-orange-950/70 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/80 shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                    @yield('status_badge', 'HTTP Status')
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase mb-3">
                @yield('title')
            </h1>

            <!-- Message Description -->
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-medium leading-relaxed max-w-md mx-auto mb-8">
                @yield('message')
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full">
                @yield('actions')
            </div>

            <!-- Optional Extra Section -->
            @hasSection('extra')
                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800/80">
                    @yield('extra')
                </div>
            @endif
        </div>
    </main>

    <!-- Footer Info -->
    <footer class="w-full max-w-4xl mx-auto py-3 text-center z-10">
        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
            Sistem Informasi Bimbingan Mahasiswa &bull; &copy; {{ date('Y') }} Universitas Subang
        </p>
    </footer>
</body>
</html>

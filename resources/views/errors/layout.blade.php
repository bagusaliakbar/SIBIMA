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
      class="min-h-full bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-200 flex flex-col justify-between">

    <!-- Subtle Ambient Glow Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[20%] left-1/2 -translate-x-1/2 w-[700px] h-[500px] rounded-full bg-gradient-to-b from-orange-500/10 via-amber-500/5 to-transparent blur-[120px] dark:from-orange-600/10 dark:via-amber-600/5"></div>
    </div>

    <!-- Header Navigation -->
    <header class="w-full max-w-6xl mx-auto px-6 py-6 flex items-center justify-between z-10">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-bold shadow-md shadow-orange-600/20 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <span class="text-base font-black tracking-wide text-slate-900 dark:text-white uppercase flex items-center gap-1.5 leading-none">
                    SIBIMA
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                </span>
                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest block mt-0.5">Universitas Subang</span>
            </div>
        </a>

        <!-- Dark Mode Toggle Button -->
        <button @click="toggleDark()" 
                type="button"
                aria-label="Toggle Dark Mode"
                class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 shadow-sm transition-all hover:scale-105 active:scale-95 cursor-pointer">
            <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            <svg x-show="darkMode" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </button>
    </header>

    <!-- Main Content Area -->
    <main class="w-full max-w-3xl mx-auto px-6 py-12 flex-1 flex flex-col items-center justify-center text-center z-10">
        
        <!-- Error Code Tag Pill -->
        <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100/90 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300 border border-orange-200/90 dark:border-orange-800/80 shadow-2xs mb-6">
            <span>@yield('code', 'ERROR')</span>
            <span class="text-orange-400 dark:text-orange-600 font-bold">&bull;</span>
            <span>@yield('status_badge', 'HTTP Status')</span>
        </div>

        <!-- Main Title -->
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight uppercase max-w-2xl">
            @yield('title')
        </h1>

        <!-- Friendly Description -->
        <p class="mt-4 text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium leading-relaxed max-w-lg mx-auto">
            @yield('message')
        </p>

        <!-- Action Button Hub -->
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
            @yield('actions')
        </div>

        <!-- Extra Quick Links -->
        @hasSection('extra')
            <div class="mt-12 w-full max-w-md">
                @yield('extra')
            </div>
        @endif
    </main>

    <!-- Clean Footer -->
    <footer class="w-full max-w-6xl mx-auto px-6 py-6 text-center z-10">
        <p class="text-xs font-medium text-slate-400 dark:text-slate-500">
            Sistem Informasi Bimbingan Mahasiswa (SIBIMA) &bull; &copy; {{ date('Y') }} Universitas Subang
        </p>
    </footer>
</body>
</html>

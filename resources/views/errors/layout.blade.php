<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name', 'SIBIMA') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 transition-colors duration-300 overflow-hidden flex items-center justify-center selection:bg-orange-500/10 selection:text-orange-500">
    <!-- Glowing background accents -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[40%] -left-[20%] w-[85%] h-[85%] rounded-full bg-gradient-to-tr from-indigo-500/10 to-purple-500/10 blur-[130px] dark:from-indigo-600/5 dark:to-purple-600/5"></div>
        <div class="absolute -bottom-[40%] -right-[20%] w-[85%] h-[85%] rounded-full bg-gradient-to-tr from-orange-500/10 to-amber-500/10 blur-[130px] dark:from-orange-600/5 dark:to-amber-600/5"></div>
    </div>

    <!-- Main Container -->
    <div class="relative max-w-xl w-full mx-4 text-center z-10">
        <!-- Icon Container -->
        <div class="mb-6 inline-flex items-center justify-center p-5 bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-100 dark:shadow-none border border-slate-100 dark:border-slate-800/80 group hover:scale-110 hover:rotate-3 transition-all duration-300">
            @yield('icon')
        </div>

        <!-- Error Code -->
        <div class="text-[7rem] sm:text-[9rem] font-extrabold leading-none bg-gradient-to-r from-orange-500 via-purple-600 to-indigo-600 bg-clip-text text-transparent select-none tracking-tighter">
            @yield('code')
        </div>

        <!-- Title / Message -->
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase mb-3">
            @yield('title')
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed max-w-md mx-auto mb-8">
            @yield('message')
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition-all active:scale-95 shadow-sm">
                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </button>
            <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white font-black text-xs rounded-xl transition-all shadow-md hover:shadow-orange-500/20 active:scale-95 uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7-7-7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Ke Dashboard
            </a>
        </div>
        
        <!-- Footer Info -->
        <div class="mt-12 text-[10px] text-slate-400 dark:text-slate-600 font-bold tracking-widest uppercase select-none">
            SIBIMA &bull; Universitas Subang
        </div>
    </div>
</body>
</html>

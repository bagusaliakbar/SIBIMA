<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') - @endif{{ config('app.name', 'SIBIMA') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; }

            /* Focus outline reset */
            input, textarea, select, button {
                outline: none !important;
                -webkit-tap-highlight-color: transparent !important;
            }

            input:focus, 
            input:focus-visible, 
            textarea:focus, 
            select:focus {
                outline: none !important;
                border-color: #f97316 !important;
                box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15) !important;
            }

            /* Browser Autofill styling */
            input:-webkit-autofill,
            input:-webkit-autofill:hover, 
            input:-webkit-autofill:focus {
                -webkit-text-fill-color: #0f172a !important;
                -webkit-box-shadow: 0 0 0px 1000px #ffffff inset !important;
                box-shadow: 0 0 0px 1000px #ffffff inset !important;
                border-color: #f97316 !important;
            }

            .dark input:-webkit-autofill,
            .dark input:-webkit-autofill:hover, 
            .dark input:-webkit-autofill:focus {
                -webkit-text-fill-color: #f8fafc !important;
                -webkit-box-shadow: 0 0 0px 1000px #1e293b inset !important;
                box-shadow: 0 0 0px 1000px #1e293b inset !important;
                border-color: #f97316 !important;
            }
        </style>
    </head>
    <body class="font-sans text-slate-800 dark:text-slate-100 antialiased bg-[#f5f7fa] dark:bg-[#0f1117] min-h-screen flex flex-col justify-between transition-colors duration-200">
        
        <!-- Top Navigation / Brand Bar (Metronic style) -->
        <header class="w-full px-6 py-5 flex items-center justify-between max-w-7xl mx-auto">
            <!-- Brand Logo -->
            <a href="{{ route('login') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-9 h-9 object-contain group-hover:scale-105 transition-transform">
                <div class="leading-tight">
                    <span class="block text-sm font-extrabold tracking-tight text-slate-900 dark:text-white">SIBIMA</span>
                    <span class="block text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">FASILKOM UNSUB</span>
                </div>
            </a>

            <!-- Metronic Style Theme Switcher -->
            <button type="button" 
                    @click="darkMode = !darkMode" 
                    class="p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 shadow-2xs hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all cursor-pointer flex items-center gap-1.5"
                    :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                <svg x-show="darkMode" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <svg x-show="!darkMode" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <span class="text-xs font-semibold" x-text="darkMode ? 'Terang' : 'Gelap'"></span>
            </button>
        </header>

        <!-- Center Card Container (Metronic 8 Layout Style) -->
        <main class="w-full flex-1 flex flex-col items-center justify-center p-4 sm:p-6 my-auto">
            <div class="w-full {{ request()->routeIs('register') ? 'max-w-[560px]' : 'max-w-[440px]' }} bg-white dark:bg-[#181a20] rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none p-7 sm:p-10 transition-all">
                {{ $slot }}
            </div>
        </main>

        <!-- Metronic Style Clean Footer -->
        <footer class="w-full py-6 px-6 max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400 dark:text-slate-500 font-medium border-t border-slate-200/60 dark:border-slate-800/60 mt-auto">
            <div>
                &copy; {{ date('Y') }} SIBIMA — Fakultas Ilmu Komputer, Universitas Subang.
            </div>
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-slate-500 dark:text-slate-400">Sistem Akademik Aktif</span>
            </div>
        </footer>

    </body>
</html>

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
    <body class="font-sans text-slate-800 dark:text-slate-100 antialiased bg-[#f8fafc] dark:bg-[#090d16] min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 transition-colors duration-300 relative overflow-x-hidden selection:bg-orange-500 selection:text-white">
        
        <!-- Tech Dot-Grid & Ambient Glow Background (Linear / Vercel Style) -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            <!-- Ambient Glow Orb 1: SIBIMA Vibrant Orange (Upper Right) -->
            <div class="absolute top-[5%] right-[10%] w-[500px] h-[500px] rounded-full bg-gradient-to-br from-orange-500/25 via-amber-500/15 to-transparent dark:from-orange-500/20 dark:via-amber-500/10 dark:to-transparent blur-[120px] transform-gpu"></div>
            
            <!-- Ambient Glow Orb 2: Deep Cyber Blue/Indigo (Lower Left) -->
            <div class="absolute bottom-[5%] left-[10%] w-[550px] h-[550px] rounded-full bg-gradient-to-tr from-blue-600/20 via-indigo-500/15 to-transparent dark:from-blue-600/25 dark:via-indigo-600/15 dark:to-transparent blur-[130px] transform-gpu"></div>
            
            <!-- Subtle Center Ambient Glow (Behind Card) -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] rounded-full bg-orange-400/5 dark:bg-orange-500/5 blur-[140px] transform-gpu"></div>

            <!-- Tech Dot-Grid Pattern with Soft Radial Fade Mask -->
            <div class="absolute inset-0 bg-[radial-gradient(#94a3b8_1px,transparent_1px)] dark:bg-[radial-gradient(rgba(255,255,255,0.16)_1px,transparent_1px)] [background-size:24px_24px] [mask-image:radial-gradient(ellipse_75%_75%_at_50%_50%,#000_50%,transparent_100%)]"></div>
        </div>

        <!-- Center Auth Wrapper -->
        <div class="w-full flex flex-col items-center justify-center my-auto py-8 relative z-10">
            
            <!-- Auth Card (Clean Elevated Surface) -->
            <div class="w-full {{ request()->routeIs('register') ? 'max-w-xl' : 'max-w-md' }} bg-white/95 dark:bg-[#111827]/95 backdrop-blur-xl rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl shadow-slate-200/60 dark:shadow-black/70 p-7 sm:p-10 transition-all mx-auto"
                 style="max-width: {{ request()->routeIs('register') ? '560px' : '440px' }};">
                {{ $slot }}
            </div>

            <!-- Compact Footer below card with Theme Switcher -->
            <div class="mt-8 text-center text-xs text-slate-400 dark:text-slate-500 font-medium space-y-2">
                <div class="flex items-center justify-center gap-3">
                    <span>&copy; {{ date('Y') }} SIBIMA FASILKOM &mdash; Universitas Subang</span>
                    <span>&bull;</span>
                    <!-- Safe, non-intrusive theme toggle in footer -->
                    <button type="button" 
                            @click="darkMode = !darkMode" 
                            class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors flex items-center gap-1.5 cursor-pointer font-medium"
                            :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                        <svg x-show="darkMode" x-cloak class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <svg x-show="!darkMode" class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <span x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
                    </button>
                </div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-200/60 dark:bg-slate-800/80 text-[11px] text-slate-500 dark:text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Sistem Bimbingan Akademik Aktif</span>
                </div>
            </div>

        </div>

    </body>
</html>

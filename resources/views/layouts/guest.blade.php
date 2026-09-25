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
    <body class="font-sans text-slate-800 dark:text-slate-100 antialiased bg-[#f8fafc] dark:bg-[#07090e] min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 transition-colors duration-300 relative overflow-x-hidden selection:bg-orange-500 selection:text-white">
        
        <!-- Abstract Gradient Mesh Background (Stripe Style) -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            <!-- Mesh Orb 1: SIBIMA Brand Orange (Top-Right / Behind Card) -->
            <div class="absolute -top-[15%] -right-[10%] w-[650px] h-[650px] rounded-full bg-gradient-to-br from-orange-400/25 via-amber-500/20 to-transparent dark:from-orange-600/15 dark:via-amber-600/10 dark:to-transparent blur-[120px] transform-gpu"></div>
            
            <!-- Mesh Orb 2: Deep Indigo / Violet (Bottom-Left) -->
            <div class="absolute -bottom-[20%] -left-[10%] w-[600px] h-[600px] rounded-full bg-gradient-to-tr from-indigo-500/20 via-blue-500/15 to-transparent dark:from-indigo-900/30 dark:via-blue-900/20 dark:to-transparent blur-[130px] transform-gpu"></div>
            
            <!-- Mesh Orb 3: Soft Radiant Rose (Top-Left / Center Glow) -->
            <div class="absolute top-[25%] left-[15%] w-[450px] h-[450px] rounded-full bg-orange-300/15 dark:bg-orange-500/10 blur-[100px] transform-gpu"></div>

            <!-- Stripe-style SVG Fluid Curved Mesh Wave -->
            <svg class="absolute inset-0 w-full h-full opacity-[0.35] dark:opacity-[0.22] mix-blend-multiply dark:mix-blend-screen" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 1440 900">
                <defs>
                    <linearGradient id="stripeMesh1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#f97316" stop-opacity="0.35" />
                        <stop offset="50%" stop-color="#fb923c" stop-opacity="0.15" />
                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0.25" />
                    </linearGradient>
                    <linearGradient id="stripeMesh2" x1="100%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#ea580c" stop-opacity="0.25" />
                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.1" />
                    </linearGradient>
                </defs>
                <path d="M-100,200 C300,50 650,450 1100,220 C1300,120 1500,280 1600,320 L1600,900 L-100,900 Z" fill="url(#stripeMesh1)"/>
                <path d="M-50,600 C250,420 700,750 1150,550 C1350,450 1550,620 1650,600 L1650,900 L-50,900 Z" fill="url(#stripeMesh2)"/>
            </svg>

            <!-- Subtle Grid Pattern Overlay for High-End Tactile Texture -->
            <div class="absolute inset-0 opacity-[0.22] dark:opacity-[0.12]" 
                 style="background-image: radial-gradient(rgba(100, 116, 139, 0.35) 1px, transparent 1px); background-size: 24px 24px;">
            </div>
        </div>

        <!-- Center Auth Wrapper -->
        <div class="w-full flex flex-col items-center justify-center my-auto py-8 relative z-10">
            
            <!-- Auth Card (Glassmorphic Elevated Surface) -->
            <div class="w-full {{ request()->routeIs('register') ? 'max-w-xl' : 'max-w-md' }} bg-white/95 dark:bg-[#111827]/90 backdrop-blur-xl rounded-3xl border border-white/80 dark:border-slate-800/80 shadow-2xl shadow-slate-200/60 dark:shadow-black/70 p-7 sm:p-10 transition-all mx-auto"
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

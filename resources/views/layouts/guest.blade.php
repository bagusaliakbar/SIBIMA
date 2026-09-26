@props(['card' => true, 'maxWidth' => null])
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

        <!-- Theme Persistence Guard -->
        <script>
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Cloudflare Turnstile Script -->
        @if(config('services.turnstile.site_key'))
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endif

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; }

            /* Focus outline reset */
            input, textarea, select, button {
                outline: none !important;
                -webkit-tap-highlight-color: transparent !important;
            }

            /* Light Mode Styles (Deterministic, no arbitrary class failure) */
            body {
                background-color: #f8fafc;
                color: #0f172a;
            }
            .auth-card {
                background-color: #ffffff;
                border: 1px solid #e2e8f0;
                box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(15, 23, 42, 0.04);
            }
            .auth-title {
                color: #0f172a;
            }
            .auth-subtitle {
                color: #64748b;
            }
            .auth-label {
                color: #334155;
            }
            .auth-input {
                background-color: #f8fafc;
                border: 1px solid #cbd5e1;
                color: #0f172a;
            }
            .auth-input::placeholder {
                color: #94a3b8;
            }
            .auth-input:focus {
                background-color: #ffffff;
                border-color: #f97316 !important;
                box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.18) !important;
            }
            .auth-footer-text {
                color: #64748b;
            }
            .auth-footer-border {
                border-color: #f1f5f9;
            }
            .dot-pattern {
                color: #cbd5e1;
                opacity: 0.65;
            }

            /* Dark Mode Styles (Deterministic, no arbitrary class failure) */
            html.dark body {
                background-color: #0b0f19 !important;
                color: #f8fafc !important;
            }
            html.dark .auth-card {
                background-color: #111827 !important;
                border-color: #1f2937 !important;
                box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7), 0 0 1px 1px rgba(255, 255, 255, 0.05) !important;
            }
            html.dark .auth-title {
                color: #ffffff !important;
            }
            html.dark .auth-subtitle {
                color: #94a3b8 !important;
            }
            html.dark .auth-label {
                color: #cbd5e1 !important;
            }
            html.dark .auth-input {
                background-color: #1e293b !important;
                border-color: #334155 !important;
                color: #f8fafc !important;
            }
            html.dark .auth-input::placeholder {
                color: #64748b !important;
            }
            html.dark .auth-input:focus {
                background-color: #0f172a !important;
                border-color: #f97316 !important;
                box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.25) !important;
            }
            html.dark .auth-footer-text {
                color: #94a3b8 !important;
            }
            html.dark .auth-footer-border {
                border-color: #1f2937 !important;
            }
            html.dark .dot-pattern {
                color: #334155 !important;
                opacity: 0.45 !important;
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

            html.dark input:-webkit-autofill,
            html.dark input:-webkit-autofill:hover, 
            html.dark input:-webkit-autofill:focus {
                -webkit-text-fill-color: #f8fafc !important;
                -webkit-box-shadow: 0 0 0px 1000px #1e293b inset !important;
                box-shadow: 0 0 0px 1000px #1e293b inset !important;
                border-color: #f97316 !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col {{ $card ? 'justify-center items-center p-4 sm:p-6' : 'justify-between items-stretch p-0' }} transition-colors duration-300 relative overflow-x-hidden selection:bg-orange-500 selection:text-white">
        
        <!-- Tech Dot-Grid Background & Soft Warm Halo -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            <!-- Subtle Top Center SIBIMA Orange Ambient Glow -->
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[600px] h-[350px] rounded-full bg-orange-500/10 dark:bg-orange-500/15 blur-3xl pointer-events-none"></div>

            <!-- Crisp Tech Dot-Grid (Native SVG pattern: 100% reliable across all browsers & devices) -->
            <svg class="absolute inset-0 w-full h-full dot-pattern" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="tech-dots" width="24" height="24" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1.2" fill="currentColor" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#tech-dots)" />
            </svg>
        </div>

        @if(!$card)
            <!-- Public Guest Top Navigation Bar -->
            <header class="w-full border-b border-slate-200/80 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md sticky top-0 z-50 transition-colors">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <a href="{{ route('login') }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-black tracking-tight text-slate-800 dark:text-white">SIBIMA <span class="text-orange-600 dark:text-orange-400">FASILKOM</span></span>
                            <span class="hidden sm:inline-block text-[11px] font-bold text-slate-400 dark:text-slate-500 ml-1.5 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60">Pusat Bantuan</span>
                        </div>
                    </a>

                    <div class="flex items-center gap-3">
                        <!-- Theme Switcher -->
                        <button type="button" 
                                @click="darkMode = !darkMode; if (darkMode) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); }" 
                                class="w-9 h-9 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 shadow-xs transition-colors cursor-pointer"
                                :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                            <svg x-show="darkMode" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <svg x-show="!darkMode" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        </button>

                        <!-- Back to Login Button -->
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-600 shadow-md shadow-orange-500/20 hover:scale-105 active:scale-95 transition-all">
                            <span>Masuk ke Sistem</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Full-width Content Area -->
            <main class="w-full flex-1 relative z-10">
                {{ $slot }}
            </main>

            <!-- Clean Footer for Full-width pages -->
            <footer class="mt-auto py-8 text-center text-xs text-slate-500 dark:text-slate-400 relative z-10 border-t border-slate-200/80 dark:border-slate-800">
                <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span>&copy; {{ date('Y') }} SIBIMA FASILKOM &mdash; Universitas Subang. All rights reserved.</span>
                    <div class="flex items-center gap-4 text-xs font-medium">
                        <a href="{{ route('login') }}" class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Halaman Login</a>
                        <span>&bull;</span>
                        <a href="{{ route('faqs.index') }}" class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Pusat Bantuan (FAQ)</a>
                    </div>
                </div>
            </footer>
        @else
            <!-- Center Auth Wrapper for Standard Login/Register Cards -->
            <div class="w-full flex flex-col items-center justify-center my-auto py-8 sm:py-12 px-4 relative z-10">
                
                <!-- Auth Card (Clean Elevated Surface, comfortable & substantial) -->
                <div class="w-full auth-card rounded-3xl p-7 sm:p-10 pb-8 sm:pb-10 transition-all mx-auto"
                     style="max-width: {{ $maxWidth ?? (request()->routeIs('register') ? '620px' : '480px') }};">
                    {{ $slot }}
                </div>

                <!-- Clean Footer below card with Theme Switcher -->
                <div class="mt-10 sm:mt-12 text-center text-xs sm:text-sm auth-footer-text font-medium">
                    <div class="flex items-center justify-center gap-3 flex-wrap">
                        <span>&copy; {{ date('Y') }} SIBIMA FASILKOM &mdash; Universitas Subang</span>
                        <span>&bull;</span>
                        <!-- Safe, non-intrusive theme toggle in footer -->
                        <button type="button" 
                                @click="darkMode = !darkMode; if (darkMode) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); }" 
                                class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors flex items-center gap-1.5 cursor-pointer font-medium"
                                :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                            <svg x-show="darkMode" x-cloak class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <svg x-show="!darkMode" class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                            <span x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
                        </button>
                    </div>
                </div>

            </div>
        @endif

    </body>
</html>

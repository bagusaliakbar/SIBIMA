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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; }

            /* Force completely remove native browser focus ring / blue / purple outline */
            *, *::before, *::after {
                --tw-ring-color: rgba(249, 115, 22, 0.25) !important;
            }
            
            input, textarea, select, button {
                outline: none !important;
                -webkit-tap-highlight-color: transparent !important;
            }

            input:focus, 
            input:focus-visible, 
            input:active,
            textarea:focus, 
            textarea:focus-visible, 
            textarea:active,
            select:focus, 
            select:focus-visible, 
            select:active {
                outline: none !important;
                outline-width: 0px !important;
                outline-style: none !important;
                outline-color: transparent !important;
                border-color: #f97316 !important;
                box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.18) !important;
            }

            /* Override Browser Autofill Blue/Purple Background & Border */
            input:-webkit-autofill,
            input:-webkit-autofill:hover, 
            input:-webkit-autofill:focus,
            input:-webkit-autofill:active,
            textarea:-webkit-autofill,
            textarea:-webkit-autofill:hover,
            textarea:-webkit-autofill:focus,
            textarea:-webkit-autofill:active,
            select:-webkit-autofill,
            select:-webkit-autofill:hover,
            select:-webkit-autofill:focus,
            select:-webkit-autofill:active {
                -webkit-text-fill-color: #1e293b !important;
                -webkit-box-shadow: 0 0 0px 1000px #ffffff inset, 0 0 0 4px rgba(249, 115, 22, 0.2) !important;
                box-shadow: 0 0 0px 1000px #ffffff inset, 0 0 0 4px rgba(249, 115, 22, 0.2) !important;
                border-color: #f97316 !important;
            }

            .dark input:-webkit-autofill,
            .dark input:-webkit-autofill:hover, 
            .dark input:-webkit-autofill:focus,
            .dark input:-webkit-autofill:active {
                -webkit-text-fill-color: #f8fafc !important;
                -webkit-box-shadow: 0 0 0px 1000px #0f172a inset, 0 0 0 4px rgba(249, 115, 22, 0.25) !important;
                box-shadow: 0 0 0px 1000px #0f172a inset, 0 0 0 4px rgba(249, 115, 22, 0.25) !important;
                border-color: #f97316 !important;
            }

            /* Date & Time Picker Calendar / Clock Icons */
            input[type="date"],
            input[type="time"],
            input[type="datetime-local"] {
                color-scheme: light;
            }

            input[type="date"]::-webkit-calendar-picker-indicator,
            input[type="time"]::-webkit-calendar-picker-indicator,
            input[type="datetime-local"]::-webkit-calendar-picker-indicator {
                cursor: pointer;
                opacity: 0.75;
                transition: all 0.2s ease-in-out;
            }

            .dark input[type="date"],
            .dark input[type="time"],
            .dark input[type="datetime-local"] {
                color-scheme: dark !important;
            }
        </style>
    </head>
    <body class="font-sans text-slate-900 dark:text-slate-100 antialiased bg-slate-50 dark:bg-[#090d16] min-h-screen relative selection:bg-orange-500 selection:text-white transition-colors duration-300 overflow-x-hidden flex flex-col justify-between">
        
        <!-- Ambient Decorative Background Gradients -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            <!-- Subtle Dot Grid Pattern -->
            <div class="absolute inset-0 opacity-40 dark:opacity-20" 
                 style="background-image: radial-gradient(rgba(148, 163, 184, 0.5) 1px, transparent 1px); background-size: 28px 28px;"></div>
            
            <!-- Soft Warm Orange Glow (Top-Left) -->
            <div class="absolute -top-32 -left-32 w-96 sm:w-[32rem] h-96 sm:h-[32rem] bg-gradient-to-br from-orange-400/25 via-amber-300/15 to-transparent rounded-full blur-3xl dark:from-orange-600/15 dark:via-amber-500/10"></div>
            
            <!-- Subtle Indigo/Blue Glow (Bottom-Right) -->
            <div class="absolute -bottom-32 -right-32 w-96 sm:w-[34rem] h-96 sm:h-[34rem] bg-gradient-to-tl from-indigo-500/15 via-blue-400/10 to-transparent rounded-full blur-3xl dark:from-indigo-600/15 dark:via-blue-500/10"></div>
        </div>

        <!-- Floating Dark/Light Mode Switcher (Top-Right) -->
        <div class="fixed top-4 right-4 sm:top-6 sm:right-6 z-50">
            <button type="button" 
                    @click="darkMode = !darkMode" 
                    class="p-2.5 rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-slate-200/80 dark:border-slate-700/80 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 shadow-sm hover:scale-105 active:scale-95 transition-all cursor-pointer flex items-center justify-center gap-1.5"
                    :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                <!-- Sun Icon -->
                <svg x-show="darkMode" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <!-- Moon Icon -->
                <svg x-show="!darkMode" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <span class="text-[11px] font-bold hidden sm:inline" x-text="darkMode ? 'Terang' : 'Gelap'"></span>
            </button>
        </div>

        <!-- Main Content Area -->
        <main class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 relative z-10">
            <div class="w-full max-w-md {{ request()->routeIs('register') ? 'sm:max-w-xl' : 'sm:max-w-[440px]' }} relative z-10 px-6 sm:px-9 py-8 sm:py-9 backdrop-blur-xl bg-white/90 dark:bg-slate-900/90 shadow-2xl shadow-slate-300/40 dark:shadow-black/60 border border-slate-200/80 dark:border-slate-800 rounded-3xl transition-all">
                {{ $slot }}
            </div>
        </main>

    </body>
    <!-- Script to translate HTML5 validation messages to Indonesian -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overrideValidationMessages = () => {
                const elements = document.querySelectorAll('input, select, textarea');
                elements.forEach(el => {
                    if (el.dataset.validationBound) return;
                    el.dataset.validationBound = "true";
                    
                    el.addEventListener('invalid', function(e) {
                        e.target.setCustomValidity("");
                        if (!e.target.validity.valid) {
                            if (e.target.validity.valueMissing) {
                                e.target.setCustomValidity("Bagian ini wajib diisi.");
                            } else if (e.target.type === 'email') {
                                e.target.setCustomValidity("Harap masukkan alamat email yang valid.");
                            } else if (e.target.type === 'url') {
                                e.target.setCustomValidity("Harap masukkan URL yang valid.");
                            } else {
                                e.target.setCustomValidity("Format masukan tidak sesuai.");
                            }
                        }
                    });
                    
                    el.addEventListener('input', function(e) {
                        e.target.setCustomValidity("");
                    });
                });
            };
            
            overrideValidationMessages();
            
            const observer = new MutationObserver((mutations) => {
                overrideValidationMessages();
            });
            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>
</html>

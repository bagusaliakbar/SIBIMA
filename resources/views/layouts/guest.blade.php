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
    <body class="font-sans text-slate-900 dark:text-slate-100 antialiased bg-white dark:bg-slate-950 min-h-screen relative selection:bg-orange-500 selection:text-white transition-colors duration-300">
        <div class="min-h-screen flex flex-col lg:flex-row w-full">
            
            <!-- LEFT PANEL: Brand, Showcase, & Visuals (Desktop only) -->
            <div class="hidden lg:flex lg:w-1/2 xl:w-5/12 bg-slate-900 dark:bg-[#070b14] text-white flex-col justify-between p-10 xl:p-14 relative overflow-hidden border-r border-slate-800">
                <!-- Background Decorative Mesh Gradients & Grid Pattern -->
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute inset-0 opacity-15" style="background-image: radial-gradient(rgba(255, 255, 255, 0.3) 1px, transparent 1px); background-size: 24px 24px;"></div>
                    <div class="absolute -top-24 -left-24 w-96 h-96 bg-orange-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-10 right-10 w-80 h-80 bg-amber-500/15 rounded-full blur-3xl"></div>
                    <div class="absolute top-1/2 -right-20 w-72 h-72 bg-indigo-500/15 rounded-full blur-3xl"></div>
                </div>

                <!-- Top Brand Header -->
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md p-2 border border-white/15 shadow-inner flex items-center justify-center">
                            <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-8 h-8 object-contain">
                        </div>
                        <div>
                            <span class="block text-xs font-black tracking-wider text-orange-400 uppercase">FASILKOM UNSUB</span>
                            <h2 class="text-base font-black tracking-tight text-white leading-tight">SIBIMA</h2>
                        </div>
                    </div>
                </div>

                <!-- Center Showcase Content -->
                <div class="relative z-10 my-auto py-8 space-y-7">
                    <div class="space-y-3">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-orange-500/15 text-orange-300 border border-orange-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                            Portal Bimbingan Tugas Akhir & Skripsi
                        </span>
                        <h1 class="text-2xl xl:text-3xl font-black text-white tracking-tight leading-snug">
                            Bimbingan Terstruktur,<br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 via-amber-300 to-orange-200">
                                Kelulusan Tepat Waktu.
                            </span>
                        </h1>
                        <p class="text-xs xl:text-sm text-slate-300 leading-relaxed max-w-md">
                            Platform akademik terpadu Fakultas Ilmu Komputer Universitas Subang untuk mempermudah monitoring logbook, jadwal bimbingan, dan riset skripsi.
                        </p>
                    </div>

                    <!-- 3 Feature Highlight Cards -->
                    <div class="space-y-3">
                        <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/[0.04] border border-white/10 backdrop-blur-md">
                            <div class="w-9 h-9 rounded-xl bg-orange-500/20 border border-orange-500/30 text-orange-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Target Bimbingan 16x Terjadwal</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">Pencatatan presisi 8x bimbingan dengan Pembimbing 1 dan 8x bimbingan dengan Pembimbing 2.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/[0.04] border border-white/10 backdrop-blur-md">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Repositori Skripsi & Jurnal Ilmiah</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">Eksplorasi referensi naskah Open Access, e-book, dan salin sitasi otomatis format Word FASILKOM.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/[0.04] border border-white/10 backdrop-blur-md">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Notifikasi Real-time WhatsApp</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">Pengingat konfirmasi jadwal, izin kehadiran, dan umpan balik revisi bimbingan otomatis.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Brand Footer -->
                <div class="relative z-10 pt-4 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                    <span>© {{ date('Y') }} SIBIMA • FASILKOM UNSUB</span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-400 text-[11px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Sistem Terenkripsi SSL
                    </span>
                </div>
            </div>

            <!-- RIGHT PANEL: Authentication Form Area -->
            <div class="flex-1 flex flex-col justify-between bg-slate-50/50 dark:bg-slate-900/60 min-h-screen relative p-6 sm:p-12 overflow-y-auto">
                <!-- Top Action Bar (Mobile Header & Dark Mode Toggle) -->
                <div class="w-full flex items-center justify-between relative z-20 mb-6">
                    <!-- Mobile only logo & brand -->
                    <div class="flex lg:hidden items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 p-1.5 border border-slate-200 dark:border-slate-700 shadow-xs flex items-center justify-center">
                            <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-6 h-6 object-contain">
                        </div>
                        <div>
                            <span class="text-xs font-black tracking-tight text-orange-600 dark:text-orange-400">SIBIMA</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-medium">FASILKOM UNSUB</span>
                        </div>
                    </div>

                    <div class="hidden lg:block"></div>

                    <!-- Dark Mode Toggle Button -->
                    <button type="button" 
                            @click="darkMode = !darkMode" 
                            class="p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 shadow-2xs hover:scale-105 active:scale-95 transition-all cursor-pointer flex items-center gap-1.5"
                            :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                        <svg x-show="darkMode" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <svg x-show="!darkMode" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <span class="text-xs font-semibold" x-text="darkMode ? 'Terang' : 'Gelap'"></span>
                    </button>
                </div>

                <!-- Center Form Container -->
                <div class="my-auto py-4 w-full {{ request()->routeIs('register') ? 'max-w-lg' : 'max-w-md' }} mx-auto">
                    {{ $slot }}
                </div>

                <!-- Bottom Footer (Mobile Only) -->
                <div class="w-full text-center text-xs text-slate-400 dark:text-slate-500 pt-6 lg:hidden">
                    <span>&copy; {{ date('Y') }} SIBIMA • FASILKOM Universitas Subang</span>
                </div>
            </div>

        </div>
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

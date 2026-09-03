<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }" 
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
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

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
                box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.2) !important;
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
                -webkit-text-fill-color: inherit !important;
                -webkit-box-shadow: 0 0 0px 1000px #ffffff inset, 0 0 0 4px rgba(249, 115, 22, 0.2) !important;
                box-shadow: 0 0 0px 1000px #ffffff inset, 0 0 0 4px rgba(249, 115, 22, 0.2) !important;
                border-color: #f97316 !important;
                outline: none !important;
                transition: background-color 5000s ease-in-out 0s !important;
            }

            .dark input:-webkit-autofill,
            .dark input:-webkit-autofill:hover, 
            .dark input:-webkit-autofill:focus,
            .dark input:-webkit-autofill:active {
                -webkit-text-fill-color: #f8fafc !important;
                -webkit-box-shadow: 0 0 0px 1000px #0f172a inset, 0 0 0 4px rgba(249, 115, 22, 0.2) !important;
                box-shadow: 0 0 0px 1000px #0f172a inset, 0 0 0 4px rgba(249, 115, 22, 0.2) !important;
            }
            .sidebar-link { transition: all 0.2s ease-in-out; }
            .sidebar-link:hover { color: white; background-color: rgba(255, 255, 255, 0.05); }
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

            input[type="date"]::-webkit-calendar-picker-indicator:hover,
            input[type="time"]::-webkit-calendar-picker-indicator:hover,
            input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover {
                opacity: 1;
            }

            /* Dark Mode: Crisp White / Bright Calendar & Clock Icons */
            .dark input[type="date"],
            .dark input[type="time"],
            .dark input[type="datetime-local"] {
                color-scheme: dark !important;
            }

            .dark input[type="date"]::-webkit-calendar-picker-indicator,
            .dark input[type="time"]::-webkit-calendar-picker-indicator,
            .dark input[type="datetime-local"]::-webkit-calendar-picker-indicator {
                filter: none !important;
                opacity: 0.9 !important;
                cursor: pointer;
            }

            .dark input[type="date"]::-webkit-calendar-picker-indicator:hover,
            .dark input[type="time"]::-webkit-calendar-picker-indicator:hover,
            .dark input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover {
                opacity: 1 !important;
                filter: drop-shadow(0 0 2px rgba(249, 115, 22, 0.7)) !important;
            }

            /* Custom Sleek Dark Scrollbar */
            .custom-scrollbar::-webkit-scrollbar {
                width: 5px;
                height: 5px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.15);
                border-radius: 9999px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.35);
            }
            /* Soft Bordered Table & Sticky Header */
            table thead th {
                position: sticky;
                top: 0;
                z-index: 10;
                background-color: #f8fafc;
            }
            .dark table thead th {
                background-color: #0f172a;
            }
            table tbody tr {
                transition: background-color 0.15s ease-in-out;
            }
            table tbody tr:hover {
                background-color: #f1f5f9 !important;
            }
            .dark table tbody tr:hover {
                background-color: #1e293b !important;
            }
        </style>
        <script>
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 overflow-hidden selection:bg-orange-100 selection:text-orange-900 transition-colors duration-300">
        <div class="flex h-screen w-full overflow-hidden">
            <!-- Sidebar Backdrop for Mobile -->
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false" 
                x-transition:enter="transition ease-in-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in-out duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 md:hidden"
                style="display: none;">
            </div>

            <!-- Sidebar (Dark & Premium) -->
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="w-[280px] bg-[#0c1427] dark:bg-[#070b14] text-slate-400 flex flex-col z-50 shrink-0 shadow-2xl fixed inset-y-0 left-0 transition-all duration-300 ease-in-out md:relative md:translate-x-0 md:inset-auto md:h-full md:flex">
                <!-- Sidebar Pattern Overlay -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                
                <div class="h-20 flex items-center justify-between px-8 border-b border-white/5 dark:border-white/[0.02] relative z-10">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-10 h-10 object-contain">
                        <h1 class="text-xl font-black text-white tracking-tighter">SIBIMA</h1>
                    </div>
                    <!-- Close Button for Mobile -->
                    <button @click="sidebarOpen = false" class="md:hidden w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto py-6 px-4 custom-scrollbar relative z-10">
                    <!-- General Menu -->
                    <div class="px-4 mb-4">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Menu Utama</p>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <a href="{{ route('dashboard') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Dashboard
                        </a>

                        <a href="{{ route('chat.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('chat.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('chat.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            Pesan
                        </a>
                        <a href="{{ route('calendar.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('calendar.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('calendar.index') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Kalender Akademik
                        </a>
                        <a href="{{ route('repositories.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('repositories.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('repositories.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Katalog Pustaka
                        </a>
                    </nav>
                        
                        @if(Auth::user()->role === 'mahasiswa')
                        @php 
                            $thesis = \App\Models\Thesis::where('student_id', Auth::id())->first();
                            $isGraduated = $thesis && $thesis->status === 'completed';
                        @endphp

                        @if(!$isGraduated)
                        <div class="px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Tahapan Skripsi</p>
                        </div>
                        <nav class="space-y-1 mb-10">
                            @if($thesis)
                            <a href="#" onclick="alert('Anda sudah melakukan pengajuan judul skripsi.'); return false;" class="group flex items-center px-4 py-3 rounded-xl text-sm text-slate-600 font-medium opacity-50 cursor-not-allowed">
                                <svg class="w-5 h-5 mr-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                1. Pengajuan Judul
                            </a>
                            @else
                            <a href="{{ route('theses.create') }}" 
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('theses.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('theses.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                1. Pengajuan Judul
                            </a>
                            @endif

                            @php
                                $hasSeminar = $thesis ? \App\Models\SeminarApplication::where('thesis_id', $thesis->id)->whereIn('status', ['approved', 'completed', 'finished'])->exists() || \App\Models\SeminarScheduleDetail::where('thesis_id', $thesis->id)->exists() : false;
                            @endphp
                            @if($hasSeminar)
                            <a href="#" onclick="alert('Anda sudah melaksanakan atau mendaftar seminar proposal/hasil.'); return false;" class="group flex items-center px-4 py-3 rounded-xl text-sm text-slate-600 font-medium opacity-50 cursor-not-allowed">
                                <svg class="w-5 h-5 mr-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                2. Pendaftaran Seminar
                            </a>
                            @else
                            <a href="{{ route('seminar-applications.index') }}" 
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('seminar-applications.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('seminar-applications.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                2. Pendaftaran Seminar
                            </a>
                            @endif

                            @php
                                $hasDefense = $thesis ? \App\Models\ThesisDefenseApplication::where('thesis_id', $thesis->id)->whereIn('status', ['approved', 'completed', 'finished'])->exists() || \App\Models\ThesisDefenseScheduleDetail::where('thesis_id', $thesis->id)->exists() : false;
                            @endphp
                            @if($hasDefense)
                            <a href="#" onclick="alert('Anda sudah melaksanakan atau mendaftar sidang skripsi.'); return false;" class="group flex items-center px-4 py-3 rounded-xl text-sm text-slate-600 font-medium opacity-50 cursor-not-allowed">
                                <svg class="w-5 h-5 mr-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                3. Pendaftaran Sidang
                            </a>
                            @else
                            <a href="{{ route('thesis-defense-applications.index') }}" 
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('thesis-defense-applications.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('thesis-defense-applications.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                3. Pendaftaran Sidang
                            </a>
                            @endif
                        </nav>

                        <div class="px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Aktivitas & Bimbingan</p>
                        </div>
                        <nav class="space-y-1 mb-10">
                            <a href="{{ route('mentoring-sessions.index') }}" 
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('mentoring-sessions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('mentoring-sessions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Jadwal Bimbingan
                            </a>
                            <a href="{{ route('logbooks.index') }}" 
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('logbooks.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('logbooks.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                Logbook Bimbingan
                            </a>
                        </nav>
                        @endif

                        <div class="px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Pasca Seminar & Sidang</p>
                        </div>
                        <nav class="space-y-1 mb-10">
                            <a href="{{ route('student-seminar-revisions.index') }}" 
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('student-seminar-revisions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('student-seminar-revisions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Revisi Seminar
                            </a>
                            <a href="{{ route('student-defense-revisions.index') }}" 
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('student-defense-revisions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('student-defense-revisions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Revisi Sidang
                            </a>
                        </nav>
                        @endif

                    <!-- Admin & Kaprodi Specific -->
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    <div class="px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Pelaksanaan Skripsi</p>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <!-- Direct Link: Daftar Pengajuan -->
                        <a href="{{ route('theses.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('theses.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('theses.index') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Daftar Pengajuan
                        </a>

                        <!-- Dropdown 1: Validasi & Verifikasi -->
                        @php $isValidasiActive = request()->routeIs('seminar-applications.*') || request()->routeIs('thesis-defense-applications.*'); @endphp
                        <div x-data="{ open: {{ $isValidasiActive ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isValidasiActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isValidasiActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <span>Validasi & Verifikasi</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('seminar-applications.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-applications.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Validasi Seminar
                                </a>
                                <a href="{{ route('thesis-defense-applications.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('thesis-defense-applications.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Validasi Sidang
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 2: Jadwal & Agenda -->
                        @php $isJadwalActive = request()->routeIs('mentoring-sessions.*') || request()->routeIs('seminar-schedules.*') || request()->routeIs('thesis-defense-schedules.*'); @endphp
                        <div x-data="{ open: {{ $isJadwalActive ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isJadwalActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isJadwalActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>Jadwal & Agenda</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('mentoring-sessions.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('mentoring-sessions.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Bimbingan
                                </a>
                                <a href="{{ route('seminar-schedules.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-schedules.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Seminar
                                </a>
                                <a href="{{ route('thesis-defense-schedules.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('thesis-defense-schedules.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Sidang
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 3: Penugasan Penguji -->
                        @php $isPengujiActive = request()->routeIs('seminar-examiner.*') || request()->routeIs('defense-examiner.*'); @endphp
                        <div x-data="{ open: {{ $isPengujiActive ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isPengujiActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isPengujiActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 022 2h2a2 2 0 022-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    <span>Penugasan Penguji</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('seminar-examiner.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-examiner.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Penguji Seminar
                                </a>
                                <a href="{{ route('defense-examiner.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('defense-examiner.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Penguji Sidang
                                </a>
                            </div>
                        </div>
                                  <div class="px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Monitoring</p>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <!-- Direct Link: Monitoring Bimbingan -->
                        <a href="{{ route('monitoring.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('monitoring.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('monitoring.index') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Monitoring Bimbingan
                        </a>

                        <!-- Dropdown 1: Monitoring Revisi -->
                        @php $isMonitoringRevisiActive = request()->routeIs('monitoring.revisions') || request()->routeIs('monitoring.defense-revisions'); @endphp
                        <div x-data="{ open: {{ $isMonitoringRevisiActive ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isMonitoringRevisiActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isMonitoringRevisiActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 022 2h2a2 2 0 022-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    <span>Monitoring Revisi</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('monitoring.revisions') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.revisions') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Revisi Seminar
                                </a>
                                <a href="{{ route('monitoring.defense-revisions') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.defense-revisions') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Revisi Sidang
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 2: Evaluasi & Laporan -->
                        @php $isEvaluasiActive = request()->routeIs('monitoring.defense-scores') || request()->routeIs('monitoring.critical') || request()->routeIs('monitoring.advanced-reporting') || request()->routeIs('theses.unsubmitted-students'); @endphp
                        <div x-data="{ open: {{ $isEvaluasiActive ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isEvaluasiActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isEvaluasiActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    <span>Evaluasi & Laporan</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('theses.unsubmitted-students') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('theses.unsubmitted-students') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Belum Mengajukan Judul
                                </a>
                                <a href="{{ route('monitoring.defense-scores') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.defense-scores') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Nilai Sidang
                                </a>
                                <a href="{{ route('monitoring.critical') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.critical') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Monitoring Masa Studi
                                </a>
                                <a href="{{ route('monitoring.advanced-reporting') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.advanced-reporting') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Statistik & Pelaporan
                                </a>
                            </div>
                        </div>
                    </nav>

                    <div class="px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Sistem & Konfigurasi</p>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <!-- Direct Link: Pengumuman -->
                        <a href="{{ route('announcements.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('announcements.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('announcements.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            Pengumuman
                        </a>

                        <!-- Direct Link: Data Pengguna -->
                        <a href="{{ route('users.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Data Pengguna
                        </a>

                        <!-- Dropdown 1: Konfigurasi Sistem -->
                        @php $isConfigActive = request()->routeIs('waves.*') || request()->routeIs('admin.letter-settings.*') || request()->routeIs('wa-templates.*'); @endphp
                        <div x-data="{ open: {{ $isConfigActive ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isConfigActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isConfigActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                    <span>Konfigurasi Sistem</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('waves.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('waves.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Gelombang Pelaksanaan
                                </a>
                                <a href="{{ route('admin.letter-settings.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('admin.letter-settings.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Pengaturan Nomor Surat
                                </a>
                                <a href="{{ route('wa-templates.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('wa-templates.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Template WA
                                </a>
                            </div>
                        </div>

                        <!-- Direct Link: Log Sistem -->
                        <a href="{{ route('admin.logs') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('admin.logs') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.logs') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Log Sistem
                        </a>
                    </nav>
                    @endif

                    <!-- Dosen Specific -->
                    @if(Auth::user()->role === 'dosen')
                    <div class="px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Dokumentasi & Bimbingan</p>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <a href="{{ route('theses.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('theses.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('theses.index') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Mahasiswa Bimbingan
                        </a>
                        <a href="{{ route('mentoring-sessions.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('mentoring-sessions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('mentoring-sessions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Jadwal Bimbingan
                        </a>
                        <a href="{{ route('seminar-schedules.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('seminar-schedules.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('seminar-schedules.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Jadwal Seminar
                        </a>
                        <a href="{{ route('thesis-defense-schedules.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('thesis-defense-schedules.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('thesis-defense-schedules.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Jadwal Sidang
                        </a>
                        <a href="{{ route('seminar-examiner.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('seminar-examiner.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('seminar-examiner.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Penguji Seminar
                        </a>
                        <a href="{{ route('defense-examiner.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('defense-examiner.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('defense-examiner.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Penguji Sidang
                        </a>
                        <a href="{{ route('logbooks.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('logbooks.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('logbooks.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Logbook Mahasiswa
                        </a>
                    </nav>
                    @endif
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 relative transition-colors duration-300">
                <!-- Top Header (Clean & Modern) -->
                <header class="h-20 bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between px-6 lg:px-10 shrink-0 transition-colors duration-300">
                    <div class="flex items-center md:hidden gap-2">
                        <!-- Hamburger toggle for mobile sidebar -->
                        <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-500 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-slate-700/50 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-8 h-8 object-contain">
                            <h1 class="text-lg font-black text-slate-800 dark:text-white tracking-tighter">SIBIMA</h1>
                        </div>
                    </div>

                    <!-- Desktop Spacer to keep right elements aligned -->
                    <div class="hidden md:block"></div>


                    <div class="flex items-center space-x-3 sm:space-x-5">
                        <div class="flex items-center space-x-1 sm:space-x-2">
                            <!-- Dark Mode Toggle -->
                            <button @click="darkMode = !darkMode" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-slate-700 transition-all relative" title="Toggle Mode">
                                <svg x-cloak x-show="!darkMode" class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                <svg x-cloak x-show="darkMode" class="w-5 h-5 text-yellow-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </button>

                            <div x-data="notificationDropdown()" x-init="init()" class="relative">
                                <button @click="toggle()" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-slate-700 transition-all relative" title="Notifikasi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    <template x-if="unreadCount > 0">
                                        <span class="absolute -top-1 -right-1 min-w-[1.15rem] h-4.5 px-1 bg-gradient-to-r from-orange-600 to-rose-600 text-white text-[9px] font-black rounded-full flex items-center justify-center shadow-md ring-2 ring-white dark:ring-slate-800 animate-in fade-in zoom-in duration-200">
                                            <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                                        </span>
                                    </template>
                                </button>

                                <!-- Dropdown Panel -->
                                <div x-show="isOpen" 
                                     @click.away="isOpen = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                     class="absolute right-0 mt-3 w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200/80 dark:border-slate-700 z-50 overflow-hidden text-left"
                                     style="width: 380px; max-width: calc(100vw - 2rem); display: none;">
                                    
                                    <!-- Header & Filter Tabs -->
                                    <div class="p-4 pb-3 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900/60">
                                        <div class="flex justify-between items-center mb-3">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">Notifikasi</h3>
                                                <span x-show="unreadCount > 0" class="px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-400 text-[9px] font-black" x-text="unreadCount + ' Baru'"></span>
                                            </div>
                                            <div>
                                                <button type="button" @click="markAllAsRead()" x-show="unreadCount > 0" class="text-[10px] font-bold text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                                                    Tandai semua dibaca
                                                </button>
                                                <span x-show="unreadCount === 0" class="text-[10px] text-slate-400 font-medium">Semua telah dibaca</span>
                                            </div>
                                        </div>

                                        <!-- Filter Tabs (Semua & Belum Dibaca) -->
                                        <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-200/60 dark:bg-slate-800 rounded-xl text-xs">
                                            <button type="button" 
                                                    @click="activeTab = 'all'" 
                                                    class="py-1.5 px-3 rounded-lg text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap"
                                                    :class="activeTab === 'all' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'">
                                                <span>Semua</span>
                                                <span class="text-[10px] py-0.5 px-1.5 rounded-full font-bold" 
                                                      :class="activeTab === 'all' ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' : 'bg-slate-300/50 dark:bg-slate-700 text-slate-500'" 
                                                      x-text="notifications.length"></span>
                                            </button>
                                            <button type="button" 
                                                    @click="activeTab = 'unread'" 
                                                    class="py-1.5 px-3 rounded-lg text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap"
                                                    :class="activeTab === 'unread' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'">
                                                <span>Belum Dibaca</span>
                                                <span x-show="unreadCount > 0" class="text-[9px] font-black px-1.5 py-0.5 bg-orange-600 text-white rounded-full" x-text="unreadCount"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Notification List (Fixed max-height to prevent vertical stretching) -->
                                    <div class="overflow-y-auto custom-scrollbar divide-y divide-slate-100 dark:divide-slate-700/60"
                                         style="max-height: 384px;">
                                        <template x-if="filteredNotifications.length === 0">
                                            <div class="p-8 text-center">
                                                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-6 h-6 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                                </div>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 font-medium" x-text="activeTab === 'unread' ? 'Semua notifikasi sudah Anda baca' : 'Belum ada notifikasi'"></p>
                                            </div>
                                        </template>

                                        <template x-for="notif in filteredNotifications" :key="notif.id">
                                            <div @click="markAsRead(notif.id, getUrl(notif))" 
                                                 class="p-4 hover:bg-slate-50/90 dark:hover:bg-slate-700/40 transition-colors cursor-pointer relative group flex items-start gap-3.5"
                                                 :class="notif.read_at ? 'opacity-70' : 'bg-orange-50/30 dark:bg-orange-950/20'">
                                                
                                                <!-- Category Semantic Icon -->
                                                <div class="w-9 h-9 rounded-2xl shrink-0 flex items-center justify-center border shadow-2xs transition-transform group-hover:scale-105"
                                                     :class="{
                                                        'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-700/50': getCategory(notif) === 'schedule',
                                                        'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-700/50': getCategory(notif) === 'success',
                                                        'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-700/50': getCategory(notif) === 'reminder',
                                                        'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-700/50': getCategory(notif) === 'revision',
                                                        'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-700/50': getCategory(notif) === 'cancelled',
                                                        'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-700/50': getCategory(notif) === 'message',
                                                        'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700': getCategory(notif) === 'info'
                                                     }">
                                                    
                                                    <!-- 📅 Schedule / Kalender -->
                                                    <svg x-show="getCategory(notif) === 'schedule'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>

                                                    <!-- ✅ Success / ACC / Selesai (Clean checkmark) -->
                                                    <svg x-show="getCategory(notif) === 'success'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>

                                                    <!-- ⏰ Reminder / Alarm / Bell -->
                                                    <svg x-show="getCategory(notif) === 'reminder'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                                    </svg>

                                                    <!-- 📝 Revision / Catatan Revisi -->
                                                    <svg x-show="getCategory(notif) === 'revision'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>

                                                    <!-- 🚫 Cancelled / Ditolak -->
                                                    <svg x-show="getCategory(notif) === 'cancelled'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>

                                                    <!-- 💬 Chat Message -->
                                                    <svg x-show="getCategory(notif) === 'message'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                                    </svg>

                                                    <!-- ℹ️ Info / Default -->
                                                    <svg x-show="getCategory(notif) === 'info'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>

                                                <!-- Content -->
                                                <div class="flex-1 min-w-0 pr-1">
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-snug mb-1" x-text="getTitle(notif)"></p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed" x-text="notif.data.message || ''"></p>
                                                    <div class="flex items-center gap-2 mt-1.5">
                                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium" x-text="formatDate(notif.created_at)"></span>
                                                        <span x-show="!notif.read_at" class="text-[9px] font-black text-orange-600 dark:text-orange-400">• Baru</span>
                                                    </div>
                                                </div>

                                                <!-- Hover Action: Single Mark as Read / Unread Indicator -->
                                                <div class="flex items-center gap-1.5 shrink-0 self-center">
                                                    <!-- Quick Mark as Read on Hover -->
                                                    <button type="button" 
                                                            x-show="!notif.read_at"
                                                            @click.stop="markSingleRead(notif.id)" 
                                                            class="opacity-0 group-hover:opacity-100 p-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 hover:text-emerald-600 hover:border-emerald-300 dark:hover:border-emerald-700 transition-all shadow-xs cursor-pointer"
                                                            title="Tandai sudah dibaca">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>

                                                    <!-- Pulsing Unread Indicator -->
                                                    <div x-show="!notif.read_at" class="w-2.5 h-2.5 rounded-full bg-orange-600 ring-4 ring-orange-100 dark:ring-orange-950/60 group-hover:hidden transition-all"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div class="p-3 bg-slate-50/70 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-800 text-center">
                                        <p class="text-[10px] font-bold text-slate-400">Menampilkan 20 notifikasi terakhir</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Cool Divider -->
                            <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 mx-1"></div>

                            <!-- Logout Button -->
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all group" title="Keluar">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                </button>
                            </form>
                        </div>

                        <audio id="notif-sound" preload="auto">
                            <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
                        </audio>

                        <script>
                            function notificationDropdown() {
                                return {
                                    isOpen: false,
                                    notifications: [],
                                    unreadCount: 0,
                                    lastCount: 0,
                                    activeTab: 'all',

                                    get filteredNotifications() {
                                        if (this.activeTab === 'unread') {
                                            return this.notifications.filter(n => !n.read_at);
                                        }
                                        return this.notifications;
                                    },

                                    init() {
                                        this.fetchNotifications();
                                        window.refreshNotifications = () => this.fetchNotifications();
                                        setInterval(() => this.fetchNotifications(), 30000); // Polling 30s
                                    },

                                    toggle() {
                                        this.isOpen = !this.isOpen;
                                        if (this.isOpen) this.fetchNotifications();
                                    },

                                    fetchNotifications() {
                                        fetch('{{ route("notifications.index") }}', {
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json'
                                            }
                                        })
                                            .then(res => res.json())
                                            .then(data => {
                                                this.notifications = data.notifications || [];
                                                this.unreadCount = data.unread_count || 0;
                                                
                                                if (this.unreadCount > this.lastCount) {
                                                    this.playSound();
                                                }
                                                this.lastCount = this.unreadCount;
                                            });
                                    },

                                    playSound() {
                                        const audio = document.getElementById('notif-sound');
                                        if (audio) {
                                            audio.currentTime = 0;
                                            audio.play().catch(e => console.log('Audio play failed:', e));
                                        }
                                    },

                                    getCategory(notif) {
                                        const type = notif.data?.type || '';
                                        const title = (notif.data?.title || '').toLowerCase();
                                        const msg = (notif.data?.message || '').toLowerCase();

                                        if (type === 'danger' || title.includes('batal') || title.includes('tolak') || msg.includes('dibatalkan') || msg.includes('ditolak')) {
                                            return 'cancelled';
                                        }
                                        if (type === 'success' || title.includes('acc') || title.includes('selesai') || msg.includes('completed') || msg.includes('selesai') || msg.includes('acc')) {
                                            return 'success';
                                        }
                                        if (type === 'warning' || title.includes('pengingat') || title.includes('h-1') || msg.includes('pengingat') || msg.includes('h-1') || msg.includes('deadline')) {
                                            return 'reminder';
                                        }
                                        if (title.includes('revisi') || msg.includes('revisi')) {
                                            return 'revision';
                                        }
                                        if (type === 'message' || title.includes('pesan') || msg.includes('pesan')) {
                                            return 'message';
                                        }
                                        if (type === 'info' || title.includes('jadwal') || msg.includes('menjadwalkan') || msg.includes('jadwal') || msg.includes('reschedule')) {
                                            return 'schedule';
                                        }
                                        return 'info';
                                    },

                                    getTitle(notif) {
                                        if (notif.data?.title) return notif.data.title;
                                        const msg = notif.data?.message || '';
                                        const lower = msg.toLowerCase();
                                        if (lower.includes('completed') || lower.includes('selesai')) return 'Status Bimbingan Selesai';
                                        if (lower.includes('status bimbingan')) return 'Pembaruan Status Bimbingan';
                                        if (lower.includes('pengingat')) return 'Pengingat Jadwal Bimbingan (H-1)';
                                        if (lower.includes('acc')) return 'Rekomendasi ACC Dosen';
                                        if (lower.includes('menjadwalkan')) return 'Jadwal Bimbingan Baru';
                                        if (lower.includes('batal')) return 'Jadwal Bimbingan Dibatalkan';
                                        if (lower.includes('revisi')) return 'Pembaruan Revisi Skripsi';
                                        return 'Pemberitahuan SIBIMA';
                                    },

                                    getUrl(notif) {
                                        if (notif.data?.url && notif.data.url !== '#') return notif.data.url;
                                        if (notif.data?.mentoring_id) return '{{ route("mentoring-sessions.index") }}';
                                        if (notif.data?.thesis_id) return '{{ route("mentoring-sessions.index") }}';
                                        return '#';
                                    },

                                    markAsRead(id, url) {
                                        fetch(`/notifications/${id}/read`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json'
                                            }
                                        }).then(() => {
                                            if (url && url !== '#') {
                                                window.location.href = url;
                                            } else {
                                                this.fetchNotifications();
                                            }
                                        });
                                    },

                                    markSingleRead(id) {
                                        fetch(`/notifications/${id}/read`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json'
                                            }
                                        }).then(() => {
                                            this.fetchNotifications();
                                        });
                                    },

                                    markAllAsRead() {
                                        fetch('{{ route("notifications.read-all") }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json'
                                            }
                                        }).then(() => this.fetchNotifications());
                                    },

                                    formatDate(dateString) {
                                        if (!dateString) return '';
                                        const date = new Date(dateString);
                                        const now = new Date();
                                        const diffSec = Math.floor((now - date) / 1000);
                                        
                                        if (diffSec < 60) return 'Baru saja';
                                        const diffMin = Math.floor(diffSec / 60);
                                        if (diffMin < 60) return `${diffMin} menit lalu`;
                                        const diffHour = Math.floor(diffMin / 60);
                                        if (diffHour < 24) return `${diffHour} jam lalu`;
                                        
                                        const isYesterday = (now.getDate() - date.getDate() === 1) && 
                                                            (now.getMonth() === date.getMonth()) && 
                                                            (now.getFullYear() === date.getFullYear());
                                        const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
                                        if (isYesterday) return `Kemarin, ${timeStr}`;
                                        
                                        const diffDay = Math.floor(diffHour / 24);
                                        if (diffDay < 7) return `${diffDay} hari lalu`;
                                        
                                        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + `, ${timeStr}`;
                                    }
                                }
                            }
                        </script>
                        
                        <a href="{{ route('profile.edit') }}" class="flex items-center pl-5 border-l border-slate-100 dark:border-slate-700 ml-2 group/profile">
                            <div class="hidden sm:flex flex-col items-end mr-4">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-none mb-1 group-hover/profile:text-orange-600 transition-colors">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] font-black text-orange-600 uppercase tracking-widest">{{ Auth::user()->role }}</span>
                            </div>
                            <div class="w-10 h-10 rounded-xl overflow-hidden shadow-lg shadow-orange-200 dark:shadow-none ring-2 ring-white dark:ring-slate-700 cursor-pointer hover:scale-110 transition-transform relative group">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </a>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 lg:p-8 z-10 w-full relative">
                    @isset($header)
                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between">
                            {{ $header }}
                        </div>
                    @endisset
                    
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-toast />

        <!-- Global Slide-Over Drawer Kemiripan Judul -->
        <div x-data="{ 
            open: false, 
            data: { student: '', npm: '', title: '', score: 0, matches: [] } 
        }"
        @open-audit-modal.window="data = $event.detail; open = true"
        x-show="open" 
        class="fixed inset-0 overflow-hidden"
        style="z-index: 99999 !important;"
        x-cloak>
            <!-- Backdrop Overlay -->
            <div x-show="open"
                 x-transition:enter="ease-in-out duration-400"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in-out duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                 style="z-index: 99999 !important;"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10" style="z-index: 100000 !important;">
                <div x-show="open"
                     x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="w-screen max-w-lg bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200 dark:border-slate-800 flex flex-col h-full relative">
                    
                    <!-- Drawer Header -->
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">Kemiripan Judul</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase mt-0.5">
                                    Mahasiswa: <span class="text-slate-700 dark:text-slate-200" x-text="data.student"></span> (<span x-text="data.npm"></span>)
                                </p>
                            </div>
                        </div>
                        <button @click="open = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Drawer Body (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                        <!-- Judul Skripsi Diuji -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-none">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Judul Skripsi Yang Diuji:</span>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase leading-relaxed" x-text="data.title"></h4>
                        </div>

                        <!-- Match Summary Banner -->
                        <div class="flex items-center justify-between p-4 rounded-2xl border dark:border-none"
                             :class="data.score >= 66 ? 'bg-rose-50 border-rose-200/80 text-rose-900 dark:bg-rose-950/60 dark:text-rose-200' : (data.score >= 35 ? 'bg-amber-50 border-amber-200/80 text-amber-900 dark:bg-amber-950/60 dark:text-amber-200' : 'bg-emerald-50 border-emerald-200/80 text-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-200')">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-widest block opacity-75">Tingkat Kemiripan:</span>
                                <span class="text-xs font-bold uppercase tracking-tight block mt-0.5">
                                    <template x-if="data.score >= 66">
                                        <span class="text-rose-800 dark:text-rose-300">🔴 Terdeteksi Sangat Mirip</span>
                                    </template>
                                    <template x-if="data.score >= 35 && data.score < 66">
                                        <span class="text-amber-800 dark:text-amber-300">🟧 Terdeteksi Kemiripan Sedang</span>
                                    </template>
                                    <template x-if="data.score < 35">
                                        <span class="text-emerald-800 dark:text-emerald-300">🟢 Judul Unik & Orisinal</span>
                                    </template>
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black block tracking-tight" :class="data.score >= 66 ? 'text-rose-700 dark:text-rose-300' : (data.score >= 35 ? 'text-amber-700 dark:text-amber-300' : 'text-emerald-700 dark:text-emerald-300')" x-text="data.score + '%'"></span>
                                <span class="text-[9px] uppercase font-bold opacity-75">Kemiripan Max</span>
                            </div>
                        </div>

                        <!-- Daftar Judul Mirip -->
                        <div>
                            <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Rincian Judul Terkait:</h5>
                            <div class="space-y-3">
                                <template x-for="(match, index) in data.matches" :key="index">
                                    <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-none space-y-2.5 shadow-2xs dark:shadow-none">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="space-y-0.5">
                                                <span class="text-[9px] font-bold text-orange-600 dark:text-orange-400 uppercase tracking-wider block" x-text="match.source"></span>
                                                <h6 class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase leading-snug" x-text="match.title"></h6>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold shrink-0 border dark:border-none" 
                                                  :class="match.percentage >= 66 ? 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/20 dark:text-rose-300' : (match.percentage >= 35 ? 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-500/20 dark:text-amber-300' : 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-700 dark:text-slate-300')"
                                                  x-text="match.percentage + '%'">
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-700/50">
                                            <span>Oleh: <strong class="text-slate-700 dark:text-slate-300 uppercase" x-text="match.author"></strong> (<span x-text="match.year"></span>)</span>
                                            <template x-if="match.matched_words && match.matched_words.length">
                                                <span class="text-[9px] font-medium text-slate-400">Kata Cocok: <span class="text-slate-600 dark:text-slate-300 font-semibold" x-text="match.matched_words.slice(0,4).join(', ')"></span></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!data.matches || data.matches.length === 0">
                                    <div class="p-6 text-center bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-none space-y-1">
                                        <svg class="w-8 h-8 text-emerald-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-tight">Tidak Ada Kemiripan Signifikan</p>
                                        <p class="text-[10px] text-slate-400">Judul ini memiliki tingkat keunikan tinggi dibanding arsip skripsi lainnya.</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Drawer Footer -->
                    <div class="p-4 px-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex justify-end shrink-0">
                        <button type="button" @click="open = false" class="px-6 py-2.5 bg-slate-800 dark:bg-white text-white dark:text-slate-800 text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-slate-900 transition-all shadow-md">Tutup Panel</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if(session('success'))
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { title: 'Berhasil', message: {!! json_encode(session('success')) !!}, type: 'success' } 
                    }));
                @endif

                @if(session('error'))
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { title: 'Kesalahan', message: {!! json_encode(session('error')) !!}, type: 'error' } 
                    }));
                @endif

                @if(session('warning'))
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { title: 'Peringatan', message: {!! json_encode(session('warning')) !!}, type: 'warning' } 
                    }));
                @endif

                @if($errors->any())
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { title: 'Validasi Gagal', message: 'Terdapat kesalahan pada input Anda. Silakan periksa kembali.', type: 'error' } 
                    }));
                @endif
            
                const userId = {{ Auth::id() }};
                
                // Generic Notifications
                if (window.Echo) {
                    window.Echo.private(`notifications.${userId}`)
                        .listen('NewNotification', (e) => {
                            window.dispatchEvent(new CustomEvent('notify', { 
                                detail: { 
                                    title: e.title, 
                                    message: e.message, 
                                    type: e.type 
                                } 
                            }));
                            
                            // Play sound
                            const audio = document.getElementById('notif-sound');
                            if (audio) {
                                audio.currentTime = 0;
                                audio.play().catch(err => console.log('Audio play failed:', err));
                            }

                            // Refresh dropdown count if the function exists
                            if (window.refreshNotifications) {
                                window.refreshNotifications();
                            }
                        });

                    // Chat Notifications (when not on chat page)
                    window.Echo.private(`chat.${userId}`)
                        .listen('MessageSent', (e) => {
                            // Only show toast if we're not on the chat page with that specific user
                            const isChatPage = window.location.pathname.includes('/chat/');
                            const chatPartnerId = window.location.pathname.split('/').pop();
                            
                            if (!isChatPage || chatPartnerId != e.message.sender_id) {
                                window.dispatchEvent(new CustomEvent('notify', { 
                                    detail: { 
                                        title: 'Pesan Baru', 
                                        message: `${e.message.sender.name}: ${e.message.message.substring(0, 50)}...`, 
                                        type: 'message' 
                                    } 
                                }));
                                
                                const audio = document.getElementById('notif-sound');
                                if (audio) {
                                    audio.currentTime = 0;
                                    audio.play().catch(err => console.log('Audio play failed:', err));
                                }
                            }
                        });
                } else {
                    console.warn('Echo is not defined. Real-time notifications are disabled.');
                }
            });
        </script>
        
        <!-- Script to translate HTML5 validation messages to Indonesian -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const overrideValidationMessages = () => {
                    const elements = document.querySelectorAll('input, select, textarea');
                    elements.forEach(el => {
                        // Prevent attaching multiple listeners
                        if (el.dataset.validationBound) return;
                        el.dataset.validationBound = "true";
                        
                        el.addEventListener('invalid', function(e) {
                            e.target.setCustomValidity("");
                            if (!e.target.validity.valid) {
                                if (e.target.validity.valueMissing) {
                                    e.target.setCustomValidity("Bagian ini wajib diisi.");
                                } else if (e.target.validity.typeMismatch) {
                                    if (e.target.type === 'email') {
                                        e.target.setCustomValidity("Harap masukkan alamat email yang valid.");
                                    } else if (e.target.type === 'url') {
                                        e.target.setCustomValidity("Harap masukkan URL yang valid.");
                                    } else {
                                        e.target.setCustomValidity("Format masukan tidak sesuai.");
                                    }
                                } else {
                                    e.target.setCustomValidity("Masukan tidak valid.");
                                }
                            }
                        });
                        
                        el.addEventListener('input', function(e) {
                            e.target.setCustomValidity("");
                        });
                    });
                };
                
                // Run on load
                overrideValidationMessages();
                
                // Also observe DOM changes in case of dynamic forms (Livewire/Alpine)
                const observer = new MutationObserver((mutations) => {
                    overrideValidationMessages();
                });
                observer.observe(document.body, { childList: true, subtree: true });
            });
        </script>
        @stack('scripts')
    </body>
</html>

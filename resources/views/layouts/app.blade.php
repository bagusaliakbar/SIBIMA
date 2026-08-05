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
            .sidebar-link { transition: all 0.2s ease-in-out; }
            .sidebar-link:hover { color: white; background-color: rgba(255, 255, 255, 0.05); }
            .dark input[type="date"]::-webkit-calendar-picker-indicator,
            .dark input[type="time"]::-webkit-calendar-picker-indicator {
                filter: invert(1) brightness(0.9);
                cursor: pointer;
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
                        <a href="{{ route('seminar-applications.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('seminar-applications.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('seminar-applications.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Validasi Seminar
                        </a>
                        <a href="{{ route('thesis-defense-applications.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('thesis-defense-applications.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('thesis-defense-applications.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Validasi Sidang
                        </a>
                        <a href="{{ route('theses.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('theses.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('theses.index') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Daftar Pengajuan
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
                    </nav>

                    <div class="px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Monitoring</p>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <a href="{{ route('monitoring.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('monitoring.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('monitoring.index') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
                            Monitoring Bimbingan
                        </a>
                        <a href="{{ route('monitoring.revisions') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('monitoring.revisions') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('monitoring.revisions') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Monitoring Revisi Seminar
                        </a>
                        <a href="{{ route('monitoring.defense-revisions') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('monitoring.defense-revisions') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('monitoring.defense-revisions') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Monitoring Revisi Sidang
                        </a>
                        <a href="{{ route('monitoring.defense-scores') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('monitoring.defense-scores') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('monitoring.defense-scores') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a.75.75 0 00-1.061 0l-1.061 1.06a.75.75 0 101.06 1.061l1.061-1.06a.75.75 0 000-1.061zM6 8a2 2 0 11-4 0 2 2 0 014 0zM22 12a10 10 0 11-20 0 10 10 0 0120 0z"></path></svg>
                            Nilai Sidang
                        </a>
                        <a href="{{ route('monitoring.critical') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('monitoring.critical') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('monitoring.critical') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Monitoring Masa Studi
                        </a>
                        <a href="{{ route('monitoring.advanced-reporting') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('monitoring.advanced-reporting') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('monitoring.advanced-reporting') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Statistik & Pelaporan
                        </a>
                    </nav>

                    <div class="px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Sistem & Konfigurasi</p>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <a href="{{ route('announcements.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('announcements.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('announcements.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            Pengumuman
                        </a>
                        <a href="{{ route('users.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Data Pengguna
                        </a>
                        <a href="{{ route('waves.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('waves.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('waves.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Gelombang Pelaksanaan
                        </a>
                        <a href="{{ route('admin.letter-settings.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('admin.letter-settings.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.letter-settings.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Pengaturan Nomor Surat
                        </a>
                        <a href="{{ route('wa-templates.index') }}" 
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('wa-templates.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('wa-templates.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            Template WA
                        </a>
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
                                <button @click="toggle()" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-slate-700 transition-all relative">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    <template x-if="unreadCount > 0">
                                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-orange-600 rounded-full ring-2 ring-white"></span>
                                    </template>
                                </button>

                                <!-- Dropdown Panel -->
                                <div x-show="isOpen" 
                                     @click.away="isOpen = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-1"
                                     class="absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 z-50 overflow-hidden"
                                     style="display: none;">
                                    
                                    <div class="p-4 border-b border-slate-50 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex justify-between items-center">
                                        <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest">Notifikasi</h3>
                                        <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-[10px] font-bold text-orange-600 hover:underline">Tandai semua dibaca</button>
                                    </div>

                                    <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                        <template x-if="notifications.length === 0">
                                            <div class="p-8 text-center">
                                                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                                </div>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Tidak ada notifikasi baru</p>
                                            </div>
                                        </template>

                                        <template x-for="notif in notifications" :key="notif.id">
                                            <div @click="markAsRead(notif.id, notif.data.url)" 
                                                 class="p-4 border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors cursor-pointer relative group"
                                                 :class="notif.read_at ? 'opacity-60' : 'bg-orange-50/20 dark:bg-orange-900/10'">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center"
                                                         :class="{
                                                            'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400': notif.data.type === 'info',
                                                            'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400': notif.data.type === 'success',
                                                            'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400': notif.data.type === 'danger',
                                                            'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400': notif.data.type === 'message'
                                                         }">
                                                        <svg x-show="notif.data.type !== 'message'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <svg x-show="notif.data.type === 'message'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-[11px] font-black text-slate-800 dark:text-slate-200 leading-tight mb-0.5" x-text="notif.data.title"></p>
                                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2" x-text="notif.data.message"></p>
                                                        <p class="text-[9px] text-slate-400 mt-1 font-bold" x-text="formatDate(notif.created_at)"></p>
                                                    </div>
                                                    <div x-show="!notif.read_at" class="w-2 h-2 bg-orange-600 rounded-full mt-1.5 ring-4 ring-orange-50 dark:ring-slate-800 group-hover:ring-white dark:group-hover:ring-slate-700 transition-all"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div class="p-3 bg-slate-50/50 dark:bg-slate-900/50 text-center">
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

                                    init() {
                                        this.fetchNotifications();
                                        window.refreshNotifications = () => this.fetchNotifications();
                                        setInterval(() => this.fetchNotifications(), 30000); // Polling reduced to 30s since we have real-time now
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
                                                this.notifications = data.notifications;
                                                this.unreadCount = data.unread_count;
                                                
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
                                        const date = new Date(dateString);
                                        return date.toLocaleString('id-ID', { 
                                            day: 'numeric', 
                                            month: 'short', 
                                            hour: '2-digit', 
                                            minute: '2-digit' 
                                        });
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

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
          darkMode: localStorage.getItem('darkMode') === 'true', 
          sidebarOpen: false,
          sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true'
      }" 
      x-init="
          $watch('darkMode', val => localStorage.setItem('darkMode', val));
          $watch('sidebarCollapsed', val => {
              localStorage.setItem('sidebarCollapsed', val);
              if (val) {
                  document.documentElement.classList.add('sidebar-is-collapsed');
              } else {
                  document.documentElement.classList.remove('sidebar-is-collapsed');
              }
          });
      "
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
        
        <!-- Progressive Web App (PWA) Meta & Service Worker -->
        <x-pwa-meta />
        
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; }
            .sidebar-link { transition: all 0.2s ease-in-out; }
            .sidebar-link:hover { color: white; background-color: rgba(255, 255, 255, 0.05); }

            /* ========================================================
               MINIMIZED / COLLAPSED SIDEBAR (EXPAND ON HOVER)
               ======================================================== */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) {
                width: 80px !important;
            }

            aside.sidebar-hover-expanded {
                width: 280px !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
                z-index: 50 !important;
            }

            /* Smooth width, transform, & shadow transitions */
            aside {
                transition-property: width, transform, box-shadow;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 300ms;
            }

            /* Hide text labels, descriptions, and dropdown arrows only when collapsed AND not hovered */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-text,
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-arrow,
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-header-title,
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-section-title {
                display: none !important;
            }

            /* Center icons when collapsed and not hovered */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-link {
                font-size: 0 !important;
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin-left: auto !important;
                margin-right: auto !important;
                width: 44px !important;
                height: 44px !important;
                border-radius: 12px !important;
                position: relative !important;
            }

            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-link svg {
                margin-right: 0 !important;
                width: 20px !important;
                height: 20px !important;
                flex-shrink: 0 !important;
            }

            /* Hide dropdown submenu children when collapsed and not hovered */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-dropdown-content {
                display: none !important;
            }

            /* Section headers become minimalist pill separators when collapsed and not hovered */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-section-header {
                padding-left: 0 !important;
                padding-right: 0 !important;
                text-align: center !important;
                margin-top: 1.25rem !important;
                margin-bottom: 0.5rem !important;
                border-top: none !important;
            }

            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-section-divider {
                display: block !important;
            }

            /* Mini notification ping badge on icon when collapsed and not hovered */
            aside.sidebar-collapsed:not(.sidebar-hover-expanded) .sidebar-badge {
                position: absolute !important;
                top: 6px !important;
                right: 6px !important;
                padding: 0 !important;
                width: 8px !important;
                height: 8px !important;
                min-width: 8px !important;
                border-radius: 9999px !important;
                font-size: 0 !important;
                line-height: 0 !important;
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
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                document.documentElement.classList.add('sidebar-is-collapsed');
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
                x-data="{ sidebarHovered: false }"
                @mouseenter="if (sidebarCollapsed) sidebarHovered = true"
                @mouseleave="sidebarHovered = false"
                :class="{
                    'translate-x-0': sidebarOpen,
                    '-translate-x-full': !sidebarOpen,
                    'md:w-[280px]': !sidebarCollapsed || sidebarHovered,
                    'md:w-[80px] sidebar-collapsed': sidebarCollapsed && !sidebarHovered,
                    'sidebar-hover-expanded shadow-2xl z-50': sidebarCollapsed && sidebarHovered
                }"
                class="w-[280px] bg-[#0c1427] dark:bg-[#070b14] text-slate-400 flex flex-col z-40 shrink-0 shadow-2xl fixed inset-y-0 left-0 transition-all duration-300 ease-in-out md:relative md:translate-x-0 md:inset-auto md:h-full md:flex">
                <!-- Sidebar Pattern Overlay -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: url('{{ asset('images/carbon-fibre.png') }}')"></div>
                
                <div class="h-20 flex items-center border-b border-white/5 dark:border-white/[0.02] relative z-10 transition-all duration-300"
                     :class="(sidebarCollapsed && !sidebarHovered) ? 'px-3 justify-center' : 'px-6 justify-between'">
                    <div class="flex items-center gap-3 overflow-hidden cursor-pointer">
                        <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-10 h-10 object-contain shrink-0 transition-transform duration-300" :class="{ 'hover:scale-105': (sidebarCollapsed && !sidebarHovered) }">
                        <div x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity.duration.200ms class="sidebar-header-title whitespace-nowrap overflow-hidden">
                            <h1 class="text-xl font-black text-white tracking-tighter">SIBIMA</h1>
                            <span class="text-[11px] uppercase tracking-wider text-orange-400 font-bold block -mt-1">Fasilkom UNSUB</span>
                        </div>
                    </div>
                    <!-- Close Button for Mobile -->
                    <button type="button" @click="sidebarOpen = false" aria-label="Tutup menu navigasi" title="Tutup menu navigasi" class="md:hidden w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 transition-all">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto py-6 custom-scrollbar relative z-10 transition-all duration-300"
                     :class="(sidebarCollapsed && !sidebarHovered) ? 'px-2 py-4' : 'px-4 py-6'">
                    <!-- General Menu -->
                    <div class="sidebar-section-header px-4 mb-4">
                        <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Menu Utama</p>
                        <div class="sidebar-section-divider hidden w-6 h-0.5 bg-white/10 rounded-full mx-auto my-2" title="Menu Utama"></div>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <a href="{{ route('dashboard') }}" 
                           data-tooltip="Dashboard" title="Dashboard"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span class="sidebar-text">Dashboard</span>
                        </a>

                        <a href="{{ route('chat.index') }}" 
                           data-tooltip="Pesan" title="Pesan"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('chat.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('chat.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            <span class="sidebar-text">Pesan</span>
                        </a>
                        <a href="{{ route('calendar.index') }}" 
                           data-tooltip="Kalender Akademik" title="Kalender Akademik"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('calendar.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('calendar.index') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="sidebar-text">Kalender Akademik</span>
                        </a>
                        <a href="{{ route('repositories.index') }}" 
                           data-tooltip="Katalog Pustaka" title="Katalog Pustaka"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('repositories.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('repositories.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <span class="sidebar-text">Katalog Pustaka</span>
                        </a>
                        <a href="{{ route('guidance-documents.index') }}" 
                           data-tooltip="Panduan & Dokumen" title="Panduan & Dokumen"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('guidance-documents.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('guidance-documents.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="sidebar-text">Panduan & Dokumen</span>
                        </a>
                        <a href="{{ route('faqs.index') }}" 
                           data-tooltip="Pusat Bantuan (FAQ)" title="Pusat Bantuan (FAQ)"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('faqs.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('faqs.index') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="sidebar-text">Pusat Bantuan (FAQ)</span>
                        </a>
                        <button type="button" 
                                data-tooltip="Lapor Kendala / Bug" title="Lapor Kendala / Bug"
                                @click="sidebarOpen = false; if (window.openBugReportModal) window.openBugReportModal();"
                                class="w-full sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 text-slate-400 hover:text-white hover:bg-white/5 font-medium text-left cursor-pointer">
                            <svg class="w-5 h-5 mr-3 transition-colors text-slate-500 group-hover:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="sidebar-text">Lapor Kendala / Bug</span>
                        </button>
                    </nav>
                        
                        @if(Auth::user()->role === 'mahasiswa')
                        @php 
                            $thesis = cache()->remember('u_' . Auth::id() . '_thesis_sidebar', 30, fn() => \App\Models\Thesis::where('student_id', Auth::id())->first());
                        @endphp

                        <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                            <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Tahapan Skripsi</p>
                            <div class="sidebar-section-divider hidden w-6 h-0.5 bg-white/10 rounded-full mx-auto my-2" title="Tahapan Skripsi"></div>
                        </div>
                        <nav class="space-y-1 mb-10">
                            @if($thesis)
                            <button type="button"
                               @click="$dispatch('notify', { title: 'Tahap 1 Selesai', message: 'Anda sudah mengajukan judul skripsi. Silakan pantau progres bimbingan pada Dashboard atau Logbook.', type: 'success' })"
                               data-tooltip="1. Pengajuan Judul (Selesai)" title="1. Pengajuan Judul (Selesai)"
                               class="w-full group flex items-center justify-between px-4 py-3 rounded-xl text-sm text-emerald-400/90 hover:text-emerald-300 bg-emerald-500/[0.06] hover:bg-emerald-500/10 border border-emerald-500/20 font-semibold transition-all cursor-pointer text-left">
                                <span class="flex items-center min-w-0">
                                    <svg aria-hidden="true" class="w-5 h-5 mr-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="sidebar-text truncate">1. Pengajuan Judul</span>
                                </span>
                                <span class="sidebar-badge px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 leading-none shrink-0">Selesai</span>
                            </button>
                            @else
                            <a href="{{ route('theses.create') }}" 
                               data-tooltip="1. Pengajuan Judul" title="1. Pengajuan Judul"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('theses.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('theses.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="sidebar-text">1. Pengajuan Judul</span>
                            </a>
                            @endif

                            @php
                                $hasSeminar = $thesis ? cache()->remember('u_' . Auth::id() . '_has_sem', 30, fn() => \App\Models\SeminarApplication::where('thesis_id', $thesis->id)->whereIn('status', ['approved', 'completed', 'finished'])->exists() || \App\Models\SeminarScheduleDetail::where('thesis_id', $thesis->id)->exists()) : false;
                            @endphp
                            @if($hasSeminar)
                            <a href="{{ route('seminar-applications.index') }}"
                               data-tooltip="2. Pendaftaran Seminar (Selesai)" title="2. Pendaftaran Seminar (Selesai - Klik untuk lihat detail)"
                               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm text-emerald-400/90 hover:text-emerald-300 bg-emerald-500/[0.06] hover:bg-emerald-500/10 border border-emerald-500/20 font-semibold transition-all">
                                <span class="flex items-center min-w-0">
                                    <svg aria-hidden="true" class="w-5 h-5 mr-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="sidebar-text truncate">2. Pendaftaran Seminar</span>
                                </span>
                                <span class="sidebar-badge px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 leading-none shrink-0">Selesai</span>
                            </a>
                            @else
                            <a href="{{ route('seminar-applications.index') }}" 
                               data-tooltip="2. Pendaftaran Seminar" title="2. Pendaftaran Seminar"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('seminar-applications.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('seminar-applications.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="sidebar-text">2. Pendaftaran Seminar</span>
                            </a>
                            @endif

                            <a href="{{ route('student-seminar-revisions.index') }}" 
                               data-tooltip="3. Revisi Seminar" title="3. Revisi Seminar"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('student-seminar-revisions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('student-seminar-revisions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span class="sidebar-text">3. Revisi Seminar</span>
                            </a>

                            @php
                                $hasDefense = $thesis ? cache()->remember('u_' . Auth::id() . '_has_def', 30, fn() => \App\Models\ThesisDefenseApplication::where('thesis_id', $thesis->id)->whereIn('status', ['approved', 'completed', 'finished'])->exists() || \App\Models\ThesisDefenseScheduleDetail::where('thesis_id', $thesis->id)->exists()) : false;
                            @endphp
                            @if($hasDefense)
                            <a href="{{ route('thesis-defense-applications.index') }}"
                               data-tooltip="4. Pendaftaran Sidang (Selesai)" title="4. Pendaftaran Sidang (Selesai - Klik untuk lihat detail)"
                               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm text-emerald-400/90 hover:text-emerald-300 bg-emerald-500/[0.06] hover:bg-emerald-500/10 border border-emerald-500/20 font-semibold transition-all">
                                <span class="flex items-center min-w-0">
                                    <svg aria-hidden="true" class="w-5 h-5 mr-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="sidebar-text truncate">4. Pendaftaran Sidang</span>
                                </span>
                                <span class="sidebar-badge px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 leading-none shrink-0">Selesai</span>
                            </a>
                            @else
                            <a href="{{ route('thesis-defense-applications.index') }}" 
                               data-tooltip="4. Pendaftaran Sidang" title="4. Pendaftaran Sidang"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('thesis-defense-applications.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('thesis-defense-applications.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <span class="sidebar-text">4. Pendaftaran Sidang</span>
                            </a>
                            @endif

                            <a href="{{ route('student-defense-revisions.index') }}" 
                               data-tooltip="5. Revisi Sidang" title="5. Revisi Sidang"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('student-defense-revisions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('student-defense-revisions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span class="sidebar-text">5. Revisi Sidang</span>
                            </a>

                            <a href="{{ route('student.graduation') }}" 
                               data-tooltip="6. Yudisium & SKL" title="6. Yudisium & SKL"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('student.graduation') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('student.graduation') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                <span class="sidebar-text">6. Yudisium & SKL</span>
                            </a>
                        </nav>

                        <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                            <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Aktivitas & Bimbingan</p>
                            <div class="sidebar-section-divider hidden w-6 h-0.5 bg-white/10 rounded-full mx-auto my-2" title="Aktivitas & Bimbingan"></div>
                        </div>
                        <nav class="space-y-1 mb-10">
                            <a href="{{ route('mentoring-sessions.index') }}" 
                               data-tooltip="Jadwal Bimbingan" title="Jadwal Bimbingan"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('mentoring-sessions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('mentoring-sessions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="sidebar-text">Jadwal Bimbingan</span>
                            </a>
                            <a href="{{ route('logbooks.index') }}" 
                               data-tooltip="Logbook Bimbingan" title="Logbook Bimbingan"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('logbooks.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('logbooks.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                <span class="sidebar-text">Logbook Bimbingan</span>
                            </a>
                        </nav>
                        @endif

                    <!-- Admin & Kaprodi Specific -->
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Pelaksanaan Skripsi</p>
                        <div class="sidebar-section-divider"></div>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <!-- Dropdown 1: Pengajuan & Bimbingan -->
                        @php $isBimbinganActive = request()->routeIs('theses.index') || request()->routeIs('advisor-decrees.*') || request()->routeIs('mentoring-sessions.*'); @endphp
                        <div x-data="{ open: {{ $isBimbinganActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Pengajuan & Bimbingan" title="Pengajuan & Bimbingan"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isBimbinganActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isBimbinganActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <span class="sidebar-text">Pengajuan & Bimbingan</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('theses.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('theses.index') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Pengajuan Judul
                                </a>
                                <a href="{{ route('advisor-decrees.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('advisor-decrees.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • SK Pembimbing
                                </a>
                                <a href="{{ route('mentoring-sessions.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('mentoring-sessions.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Bimbingan
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 2: Seminar Proposal -->
                        @php $isSeminarActive = request()->routeIs('seminar-applications.*') || request()->routeIs('seminar-schedules.*') || request()->routeIs('seminar-examiner.*'); @endphp
                        <div x-data="{ open: {{ $isSeminarActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Seminar Proposal" title="Seminar Proposal"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isSeminarActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isSeminarActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="sidebar-text">Seminar Proposal</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('seminar-applications.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-applications.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Validasi Pendaftaran
                                </a>
                                <a href="{{ route('seminar-schedules.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-schedules.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Seminar
                                </a>
                                <a href="{{ route('seminar-examiner.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-examiner.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Penguji Seminar
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 3: Sidang Skripsi -->
                        @php $isSidangActive = request()->routeIs('thesis-defense-applications.*') || request()->routeIs('thesis-defense-schedules.*') || request()->routeIs('defense-examiner.*'); @endphp
                        <div x-data="{ open: {{ $isSidangActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Sidang Skripsi" title="Sidang Skripsi"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isSidangActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isSidangActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <span class="sidebar-text">Sidang Skripsi</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('thesis-defense-applications.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('thesis-defense-applications.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Validasi Pendaftaran
                                </a>
                                <a href="{{ route('thesis-defense-schedules.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('thesis-defense-schedules.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Sidang
                                </a>
                                <a href="{{ route('defense-examiner.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('defense-examiner.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Penguji Sidang
                                </a>
                            </div>
                        </div>

                        <!-- Direct Link: Yudisium & SKL -->
                        @php $pendingGraduationsCount = \App\Models\Graduation::where('status', 'pending')->count(); @endphp
                        <a href="{{ route('graduations.index') }}" 
                           data-tooltip="Yudisium & SKL" title="Yudisium & SKL"
                           class="sidebar-link group flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('graduations.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('graduations.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                <span class="sidebar-text">Yudisium & SKL</span>
                            </div>
                            @if($pendingGraduationsCount > 0)
                                <span class="sidebar-badge px-2 py-0.5 text-[10px] font-black rounded-full bg-orange-500 text-white animate-pulse">
                                    {{ $pendingGraduationsCount }}
                                </span>
                            @endif
                        </a>
                    </nav>
                    @endif

                    <!-- Monitoring Section -->
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Monitoring & Laporan</p>
                        <div class="sidebar-section-divider"></div>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <!-- Dropdown 1: Monitoring Mahasiswa -->
                        @php 
                            $isMonitoringMhsActive = request()->routeIs('monitoring.index') || request()->routeIs('monitoring.weekly') || request()->routeIs('monitoring.activity*') || request()->routeIs('monitoring.uncompleted-mentoring*') || request()->routeIs('monitoring.revisions') || request()->routeIs('monitoring.defense-revisions') || request()->routeIs('monitoring.critical') || request()->routeIs('theses.unsubmitted-students'); 
                        @endphp
                        <div x-data="{ open: {{ $isMonitoringMhsActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Monitoring Mahasiswa" title="Monitoring Mahasiswa"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isMonitoringMhsActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isMonitoringMhsActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span class="sidebar-text">Monitoring Mahasiswa</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('monitoring.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ (request()->routeIs('monitoring.index') || request()->routeIs('monitoring.weekly') || request()->routeIs('monitoring.activity*') || request()->routeIs('monitoring.uncompleted-mentoring*')) ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Monitoring Bimbingan
                                </a>
                                <a href="{{ route('monitoring.revisions') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.revisions') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Revisi Seminar
                                </a>
                                <a href="{{ route('monitoring.defense-revisions') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.defense-revisions') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Revisi Sidang
                                </a>
                                <a href="{{ route('monitoring.critical') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.critical') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Monitoring Masa Studi
                                </a>
                                <a href="{{ route('theses.unsubmitted-students') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('theses.unsubmitted-students') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Belum Mengajukan Judul
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 2: Analitik & Evaluasi -->
                        @php 
                            $isAnalitikActive = request()->routeIs('analytics.*') || request()->routeIs('monitoring.defense-scores') || request()->routeIs('monitoring.advanced-reporting'); 
                        @endphp
                        <div x-data="{ open: {{ $isAnalitikActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Analitik & Evaluasi" title="Analitik & Evaluasi"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isAnalitikActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isAnalitikActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                    </svg>
                                    <span class="sidebar-text">Analitik & Evaluasi</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('analytics.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('analytics.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Grafik Analitik
                                </a>
                                <a href="{{ route('monitoring.defense-scores') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.defense-scores') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Nilai Sidang & Rekap
                                </a>
                                <a href="{{ route('monitoring.advanced-reporting') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.advanced-reporting') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Statistik & Pelaporan
                                </a>
                            </div>
                        </div>
                    </nav>
                    @endif

                    <!-- Sistem & Konfigurasi Section -->
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Sistem & Konfigurasi</p>
                        <div class="sidebar-section-divider"></div>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <!-- Direct Link: Data Pengguna -->
                        <a href="{{ route('users.index') }}" 
                           data-tooltip="Data Pengguna" title="Data Pengguna"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="sidebar-text">Data Pengguna</span>
                        </a>

                        <!-- Dropdown 1: Pusat Informasi (Pengumuman & FAQ) -->
                        @php $isInfoActive = request()->routeIs('announcements.*') || request()->routeIs('faqs.manage'); @endphp
                        <div x-data="{ open: {{ $isInfoActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Pusat Informasi" title="Pusat Informasi"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isInfoActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isInfoActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                    </svg>
                                    <span class="sidebar-text">Pusat Informasi</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('announcements.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('announcements.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Pengumuman
                                </a>
                                <a href="{{ route('faqs.manage') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('faqs.manage') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Kelola FAQ
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 2: Konfigurasi Sistem -->
                        @php $isConfigActive = request()->routeIs('waves.*') || request()->routeIs('admin.letter-settings.*') || request()->routeIs('wa-templates.*') || request()->routeIs('wa-broadcasts.*'); @endphp
                        <div x-data="{ open: {{ $isConfigActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Konfigurasi Sistem" title="Konfigurasi Sistem"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isConfigActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isConfigActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                    <span class="sidebar-text">Konfigurasi Sistem</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
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
                                <a href="{{ route('wa-broadcasts.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('wa-broadcasts.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Broadcast WA
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 3: Pemeliharaan Sistem (Backup, Log, Bug, Cache) -->
                        @php 
                            $openBugCount = cache()->remember('sidebar_open_bug_count', 30, function() {
                                try {
                                    if (\Illuminate\Support\Facades\Schema::hasTable('bug_reports')) {
                                        return \App\Models\BugReport::where('status', 'open')->count();
                                    }
                                } catch (\Throwable $e) {}
                                return 0;
                            });
                            $isMaintenanceActive = request()->routeIs('admin.backups.*') || request()->routeIs('admin.logs*') || request()->routeIs('admin.bug-reports.*');
                        @endphp
                        <div x-data="{ open: {{ $isMaintenanceActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Pemeliharaan Sistem" title="Pemeliharaan Sistem"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isMaintenanceActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isMaintenanceActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                                    </svg>
                                    <span class="sidebar-text">Pemeliharaan Sistem</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if($openBugCount > 0)
                                        <span class="sidebar-badge px-1.5 py-0.5 rounded-full text-[9px] font-black bg-rose-500 text-white leading-none">
                                            {{ $openBugCount }}
                                        </span>
                                    @endif
                                    <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('admin.backups.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('admin.backups.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Backup & Restore
                                </a>
                                <a href="{{ route('admin.logs') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('admin.logs*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Log Sistem
                                </a>
                                <a href="{{ route('admin.bug-reports.index') }}" 
                                   class="flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('admin.bug-reports.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    <span>• Laporan Bug</span>
                                    @if($openBugCount > 0)
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-rose-500 text-white leading-none">
                                            {{ $openBugCount }}
                                        </span>
                                    @endif
                                </a>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('system.clear-cache', ['migrate' => 1]) }}" 
                                   target="_blank"
                                   onclick="return confirm('Bersihkan cache sistem dan jalankan migrasi database sekarang?')"
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all text-slate-400 hover:text-amber-400 hover:bg-white/5 font-medium">
                                    • Bersihkan Cache
                                </a>
                                @endif
                            </div>
                        </div>
                    </nav>
                    @endif

                    <!-- Dosen Specific -->
                    @if(Auth::user()->role === 'dosen')
                    <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Dokumentasi & Bimbingan</p>
                        <div class="sidebar-section-divider"></div>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <a href="{{ route('theses.index') }}" 
                           data-tooltip="Mahasiswa Bimbingan" title="Mahasiswa Bimbingan"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ (request()->routeIs('theses.*') || request()->routeIs('logbooks.*')) ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ (request()->routeIs('theses.*') || request()->routeIs('logbooks.*')) ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="sidebar-text">Mahasiswa Bimbingan</span>
                        </a>
                        <a href="{{ route('mentoring-sessions.index') }}" 
                           data-tooltip="Jadwal Bimbingan" title="Jadwal Bimbingan"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('mentoring-sessions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('mentoring-sessions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="sidebar-text">Jadwal Bimbingan</span>
                        </a>

                        <!-- Direct Link: Surat Tugas Pembimbing (BKD / SISTER) -->
                        <a href="{{ route('advisor-decrees.surat-tugas') }}" 
                           data-tooltip="Surat Tugas" title="Surat Tugas"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('advisor-decrees.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('advisor-decrees.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="sidebar-text">Surat Tugas</span>
                        </a>

                        <!-- Dropdown: Jadwal Ujian (Seminar & Sidang) -->
                        @php $isJadwalUjianActive = request()->routeIs('seminar-schedules.*') || request()->routeIs('thesis-defense-schedules.*'); @endphp
                        <div x-data="{ open: {{ $isJadwalUjianActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Jadwal Ujian" title="Jadwal Ujian"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isJadwalUjianActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isJadwalUjianActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="sidebar-text">Jadwal Ujian</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
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

                        <!-- Dropdown: Tugas Penguji (Seminar & Sidang) -->
                        @php $isTugasPengujiActive = request()->routeIs('seminar-examiner.*') || request()->routeIs('defense-examiner.*'); @endphp
                        <div x-data="{ open: {{ $isTugasPengujiActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Tugas Penguji" title="Tugas Penguji"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isTugasPengujiActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isTugasPengujiActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    <span class="sidebar-text">Tugas Penguji</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
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
                    </nav>
                    @endif
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 relative transition-colors duration-300">
                <!-- Top Header (Clean & Modern, Fluid Mobile Height h-14 sm:h-20) -->
                <header class="h-14 sm:h-20 bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between px-3 sm:px-6 lg:px-10 shrink-0 transition-all duration-300">
                    <div class="flex items-center md:hidden gap-1.5 sm:gap-2">
                        <!-- Hamburger toggle for mobile sidebar -->
                        <button type="button" @click="sidebarOpen = !sidebarOpen" :aria-expanded="sidebarOpen" aria-label="Buka atau tutup menu navigasi" title="Menu Navigasi" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center text-slate-500 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-slate-700/50 transition-all cursor-pointer">
                            <svg aria-hidden="true" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-7 h-7 sm:w-8 sm:h-8 object-contain">
                            <h1 class="text-base sm:text-lg font-black text-slate-800 dark:text-white tracking-tighter">SIBIMA</h1>
                        </div>
                    </div>

                    <!-- Desktop Sidebar Toggle & Quick Minimize/Expand Button -->
                    <div class="hidden md:flex items-center gap-3">
                        <button type="button" 
                                @click="sidebarCollapsed = !sidebarCollapsed" 
                                :aria-expanded="!sidebarCollapsed"
                                :aria-label="sidebarCollapsed ? 'Perluas Sidebar' : 'Kecilkan Sidebar'"
                                class="h-10 px-3.5 rounded-xl flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50/80 dark:hover:bg-slate-700/60 transition-all border border-slate-200/80 dark:border-slate-700/60 shadow-xs cursor-pointer group"
                                :title="sidebarCollapsed ? 'Perluas Sidebar (Expanded)' : 'Kecilkan Sidebar (Collapsed)'">
                            <div class="w-5 h-5 flex items-center justify-center transition-colors">
                                <svg aria-hidden="true" class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors" x-text="sidebarCollapsed ? 'Buka Menu' : 'Minimize'"></span>
                        </button>
                    </div>



                    <div class="flex items-center space-x-1.5 sm:space-x-5">
                        <div class="flex items-center space-x-1 sm:space-x-2">
                            <!-- Dark Mode Toggle -->
                            <button type="button" @click="darkMode = !darkMode" :aria-label="darkMode ? 'Aktifkan Mode Terang' : 'Aktifkan Mode Gelap'" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-slate-700 transition-all relative cursor-pointer" title="Toggle Mode Gelap/Terang">
                                <svg aria-hidden="true" x-cloak x-show="!darkMode" class="w-4.5 h-4.5 sm:w-5 sm:h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                <svg aria-hidden="true" x-cloak x-show="darkMode" class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-yellow-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </button>

                            <div x-data="notificationDropdown()" x-init="init()" class="relative">
                                <button type="button" x-ref="button" @click="toggle()" :aria-expanded="isOpen" aria-label="Buka pusat notifikasi" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-slate-700 transition-all relative cursor-pointer" title="Notifikasi">
                                    <svg aria-hidden="true" class="w-4.5 h-4.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    <template x-if="unreadCount > 0">
                                        <span style="top: 3px; right: 3px;" class="absolute flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-rose-600 text-[10px] font-bold text-white border-2 border-white dark:border-slate-800 leading-none select-none shadow-xs pointer-events-none">
                                            <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                                        </span>
                                    </template>
                                </button>

                                <!-- Dropdown Panel: Responsive Width for 360px Compact Phones -->
                                <div x-show="isOpen" 
                                     @click.away="isOpen = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-1"
                                     class="fixed sm:absolute inset-x-2 sm:inset-x-auto sm:right-0 top-16 sm:top-full mt-0 sm:mt-2 w-auto sm:w-96 max-w-full sm:max-w-sm bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 z-50 overflow-hidden text-left"
                                     style="display: none;">
                                    
                                    <!-- Header -->
                                    <div class="p-4 border-b border-slate-50 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Notifikasi</h3>
                                            <span x-show="unreadCount > 0" class="px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-400 text-[10px] font-black" x-text="unreadCount + ' Baru'"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <!-- Push Notification Device Toggle Button -->
                                            <button type="button" 
                                                    @click.stop="togglePushNotifications()" 
                                                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                                                    :title="pushEnabled ? 'Notifikasi HP: Aktif (Klik untuk Uji Notifikasi)' : 'Notifikasi HP: Nonaktif (Klik untuk Aktifkan di Perangkat)'">
                                                <svg aria-hidden="true" class="w-4 h-4" :class="pushEnabled ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </button>

                                            <!-- Sound Mute/Unmute Toggle -->
                                            <button type="button" 
                                                    @click.stop="toggleSound()" 
                                                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                                                    :title="soundEnabled ? 'Suara Notifikasi: Aktif (Klik untuk Mute)' : 'Suara Notifikasi: Nonaktif (Klik untuk Aktifkan)'">
                                                <template x-if="soundEnabled">
                                                    <!-- Speaker ON -->
                                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                                                </template>
                                                <template x-if="!soundEnabled">
                                                    <!-- Speaker OFF -->
                                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path></svg>
                                                </template>
                                            </button>

                                            <button type="button" @click="markAllAsRead()" x-show="unreadCount > 0" class="text-[10px] font-bold text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                                                Tandai semua dibaca
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Notification List -->
                                    <div class="max-h-96 overflow-y-auto custom-scrollbar divide-y divide-slate-50 dark:divide-slate-700/60">
                                        <template x-if="notifications.length === 0">
                                            <div class="p-8 text-center">
                                                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                                </div>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Tidak ada notifikasi baru</p>
                                            </div>
                                        </template>

                                        <template x-for="notif in notifications" :key="notif.id">
                                            <div @click="markAsRead(notif.id, getUrl(notif))" 
                                                 class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors cursor-pointer relative group flex items-start gap-3.5"
                                                 :class="notif.read_at ? 'opacity-60' : 'bg-orange-50/20 dark:bg-orange-900/10'">
                                                
                                                <!-- Category Semantic Icon (Square 40x40 with rounded-xl, perfectly proportioned) -->
                                                <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center border shadow-xs"
                                                     style="width: 40px; height: 40px; min-width: 40px; min-height: 40px;"
                                                     :class="{
                                                        'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-blue-200/80 dark:border-blue-700/50': getCategory(notif) === 'schedule',
                                                        'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-200/80 dark:border-emerald-700/50': getCategory(notif) === 'success',
                                                        'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border-amber-200/80 dark:border-amber-700/50': getCategory(notif) === 'reminder',
                                                        'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border-indigo-200/80 dark:border-indigo-700/50': getCategory(notif) === 'revision',
                                                        'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-200/80 dark:border-rose-700/50': getCategory(notif) === 'cancelled',
                                                        'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200/80 dark:border-orange-700/50': getCategory(notif) === 'message',
                                                        'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700': getCategory(notif) === 'info'
                                                     }">
                                                    
                                                    <!-- 📅 Schedule -->
                                                    <svg x-show="getCategory(notif) === 'schedule'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>

                                                    <!-- ✅ Success / ACC -->
                                                    <svg x-show="getCategory(notif) === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>

                                                    <!-- ⏰ Reminder -->
                                                    <svg x-show="getCategory(notif) === 'reminder'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>

                                                    <!-- 📝 Revision -->
                                                    <svg x-show="getCategory(notif) === 'revision'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>

                                                    <!-- 🚫 Cancelled -->
                                                    <svg x-show="getCategory(notif) === 'cancelled'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>

                                                    <!-- 💬 Message -->
                                                    <svg x-show="getCategory(notif) === 'message'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>

                                                    <!-- ℹ️ Default Info -->
                                                    <svg x-show="getCategory(notif) === 'info'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>

                                                <!-- Message Body -->
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-snug" x-text="getTitle(notif)"></p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed" 
                                                       :class="(notif.data.topic || notif.data.notes) ? '' : 'line-clamp-2'" 
                                                       x-text="(notif.data.topic || notif.data.notes) ? (notif.data.message || '').split('\n')[0] : (notif.data.message || '')"></p>

                                                    <template x-if="notif.data.topic || notif.data.notes">
                                                        <div class="mt-2 p-2 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-800 text-[11px] space-y-1">
                                                            <template x-if="notif.data.topic">
                                                                <div class="flex items-start gap-1.5">
                                                                    <span class="font-bold text-slate-700 dark:text-slate-300 shrink-0">Topik:</span>
                                                                    <span class="text-slate-900 dark:text-slate-100 font-semibold" x-text="notif.data.topic"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="notif.data.notes">
                                                                <div class="flex items-start gap-1.5">
                                                                    <span class="font-bold text-slate-700 dark:text-slate-300 shrink-0">Catatan:</span>
                                                                    <span class="text-slate-700 dark:text-slate-300 italic" x-text="notif.data.notes"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="notif.data.scheduled_at">
                                                                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-[11px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-200/60 dark:border-slate-800/60">
                                                                    <span class="inline-flex items-center gap-1">
                                                                        <svg aria-hidden="true" class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                        <span x-text="notif.data.scheduled_at"></span>
                                                                    </span>
                                                                    <template x-if="notif.data.type_location">
                                                                        <span class="inline-flex items-center gap-1">
                                                                            <svg aria-hidden="true" class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                                            <span x-text="notif.data.type_location"></span>
                                                                        </span>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <div class="flex items-center gap-2 mt-1.5">
                                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium" x-text="formatDate(notif.created_at)"></span>
                                                        <span x-show="!notif.read_at" class="text-[11px] font-black text-orange-600 dark:text-orange-400">&bull; Baru</span>
                                                    </div>

                                                    <!-- Actionable Attendance Confirmation (For Students) -->
                                                    <template x-if="isAttendanceNotification(notif)">
                                                        <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-700/60" @click.stop>
                                                            <!-- If already confirmed in this session -->
                                                            <template x-if="attendanceSuccess[notif.data.mentoring_id]">
                                                                <div class="flex items-center gap-1.5 py-0.5 text-[11px] font-bold">
                                                                    <template x-if="attendanceSuccess[notif.data.mentoring_id] === 'attending'">
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700/80 text-[10px] font-black uppercase tracking-wider">
                                                                            <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                            <span>Akan Hadir</span>
                                                                        </span>
                                                                    </template>
                                                                    <template x-if="attendanceSuccess[notif.data.mentoring_id] === 'permission'">
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700/80 text-[10px] font-black uppercase tracking-wider">
                                                                            <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"/></svg>
                                                                            <span>Izin Diajukan</span>
                                                                        </span>
                                                                    </template>
                                                                </div>
                                                            </template>

                                                            <!-- If not yet confirmed -->
                                                            <template x-if="!attendanceSuccess[notif.data.mentoring_id]">
                                                                <div>
                                                                    <!-- Fast Action Buttons -->
                                                                    <div x-show="activePermissionInput !== notif.id" class="flex items-center gap-2">
                                                                        <button type="button" 
                                                                                @click.stop="confirmAttendance(notif, 'attending')" 
                                                                                :disabled="attendanceLoading[notif.data.mentoring_id]"
                                                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider shadow-2xs transition-all cursor-pointer disabled:opacity-50">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                            <span>Akan Hadir</span>
                                                                        </button>

                                                                        <button type="button" 
                                                                                @click.stop="activePermissionInput = notif.id" 
                                                                                :disabled="attendanceLoading[notif.data.mentoring_id]"
                                                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 dark:bg-slate-700 dark:hover:bg-rose-950/40 text-slate-700 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200 dark:border-slate-600 text-[10px] font-black uppercase tracking-wider shadow-2xs transition-all cursor-pointer disabled:opacity-50">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"/></svg>
                                                                            <span>Izin</span>
                                                                        </button>

                                                                        <span x-show="attendanceLoading[notif.data.mentoring_id]" class="text-[10px] text-slate-400 animate-pulse font-medium">Menyimpan...</span>
                                                                    </div>

                                                                    <!-- Inline Reason Form for Izin -->
                                                                    <div x-show="activePermissionInput === notif.id" class="space-y-1.5 mt-1">
                                                                        <input type="text" 
                                                                               x-model="permissionReason[notif.id]" 
                                                                               placeholder="Alasan berhalangan hadir..." 
                                                                               class="w-full text-xs px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-orange-500"
                                                                               @keydown.enter.prevent="submitPermission(notif)">
                                                                        <div class="flex items-center gap-1.5">
                                                                            <button type="button" 
                                                                                    @click.stop="submitPermission(notif)"
                                                                                    :disabled="attendanceLoading[notif.data.mentoring_id]"
                                                                                    class="px-2.5 py-1 rounded-md bg-orange-600 hover:bg-orange-700 text-white text-[10px] font-black uppercase tracking-wider transition-colors cursor-pointer">
                                                                                Kirim Izin
                                                                            </button>
                                                                            <button type="button" 
                                                                                    @click.stop="activePermissionInput = null" 
                                                                                    class="px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-semibold hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors cursor-pointer">
                                                                                Batal
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>

                                                <!-- Unread Indicator Dot -->
                                                <div x-show="!notif.read_at" class="w-2.5 h-2.5 rounded-full bg-orange-600 flex-shrink-0 self-center ring-4 ring-orange-100 dark:ring-orange-950/60"></div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <!-- Footer -->
                                    <div class="p-2.5 bg-slate-50/70 dark:bg-slate-900/70 border-t border-slate-100 dark:border-slate-700 text-center">
                                        <a href="{{ route('notifications.index') }}" class="inline-flex items-center justify-center gap-1.5 w-full py-1.5 px-3 rounded-xl text-xs font-bold text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40 transition-colors">
                                            <span>Lihat Semua Riwayat Notifikasi</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Cool Divider -->
                            <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 mx-1 self-center"></div>

                            <!-- Logout Button (Direct Button Sibling for Perfect Flexbox Alignment) -->
                            <form id="header-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                                @csrf
                            </form>
                            <button type="button" 
                                    onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();" 
                                    aria-label="Keluar dari akun"
                                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all group cursor-pointer" 
                                    title="Keluar">
                                <svg aria-hidden="true" class="w-4.5 h-4.5 sm:w-5 sm:h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </div>

                        <audio id="notif-sound" preload="none" class="hidden" hidden>
                            <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
                        </audio>

                        <script>
                            function notificationDropdown() {
                                return {
                                    isOpen: false,
                                    notifications: [],
                                    unreadCount: 0,
                                    lastCount: 0,
                                    activeTab: 'all',
                                    userRole: '{{ Auth::user()->role }}',
                                    isStudent: {{ Auth::user()->role === 'mahasiswa' ? 'true' : 'false' }},
                                    soundEnabled: localStorage.getItem('sibima_notif_sound_enabled') !== 'false',
                                    pushEnabled: ('Notification' in window) && Notification.permission === 'granted',
                                    attendanceLoading: {},
                                    attendanceSuccess: {},
                                    activePermissionInput: null,
                                    permissionReason: {},

                                    async togglePushNotifications() {
                                        if (!('Notification' in window)) {
                                            alert('Browser ini belum mendukung fitur Web Push Notification.');
                                            return;
                                        }

                                        if (Notification.permission === 'granted') {
                                            // Trigger a test notification via server endpoint
                                            try {
                                                const res = await fetch('{{ route("push-subscriptions.test") }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    }
                                                });
                                                const data = await res.json();
                                                if (data.status === 'success' && data.payload) {
                                                    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                                                        const reg = await navigator.serviceWorker.ready;
                                                        reg.showNotification(data.payload.title, {
                                                            body: data.payload.body,
                                                            icon: data.payload.icon,
                                                            badge: data.payload.badge,
                                                            vibrate: [100, 50, 100],
                                                            data: { url: data.payload.url }
                                                        });
                                                    } else {
                                                        new Notification(data.payload.title, {
                                                            body: data.payload.body,
                                                            icon: data.payload.icon
                                                        });
                                                    }
                                                }
                                            } catch (err) {
                                                console.warn('[SIBIMA PWA] Error testing push notification:', err);
                                            }
                                            return;
                                        }

                                        if (Notification.permission === 'denied') {
                                            alert('Izin notifikasi saat ini diblokir di setelan browser. Silakan izinkan notifikasi melalui ikon gembok / pengaturan browser Anda.');
                                            return;
                                        }

                                        // Request permission from user
                                        const permission = await Notification.requestPermission();
                                        if (permission === 'granted') {
                                            this.pushEnabled = true;
                                            if ('serviceWorker' in navigator) {
                                                try {
                                                    const reg = await navigator.serviceWorker.ready;
                                                    let sub = await reg.pushManager.getSubscription();
                                                    if (!sub) {
                                                        sub = await reg.pushManager.subscribe({
                                                            userVisibleOnly: true,
                                                            applicationServerKey: null
                                                        }).catch(() => null);
                                                    }
                                                    if (sub) {
                                                        const subJson = sub.toJSON();
                                                        await fetch('{{ route("push-subscriptions.store") }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                            },
                                                            body: JSON.stringify({
                                                                endpoint: sub.endpoint,
                                                                keys: subJson.keys || {},
                                                                contentEncoding: 'aes128gcm'
                                                            })
                                                        });
                                                    }
                                                } catch (e) {
                                                    console.warn('[SIBIMA PWA] Push subscription sync notice:', e);
                                                }
                                            }
                                            new Notification('🔔 Notifikasi SIBIMA Aktif!', {
                                                body: 'Perangkat Anda kini siap menerima pembaruan jadwal dan progres bimbingan secara langsung.',
                                                icon: '{{ asset("icons/icon-192x192.png") }}'
                                            });
                                        }
                                    },

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

                                    toggleSound() {
                                        this.soundEnabled = !this.soundEnabled;
                                        localStorage.setItem('sibima_notif_sound_enabled', this.soundEnabled);
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
                                        if (!this.soundEnabled) return;
                                        const audio = document.getElementById('notif-sound');
                                        if (audio) {
                                            audio.currentTime = 0;
                                            audio.play().catch(e => console.log('Audio play failed:', e));
                                        }
                                    },

                                    isAttendanceNotification(notif) {
                                        if (!this.isStudent) return false;
                                        const mentoringId = notif.data?.mentoring_id;
                                        if (!mentoringId) return false;
                                        const actionable = notif.data?.actionable;
                                        const type = notif.data?.type;
                                        const title = (notif.data?.title || '').toLowerCase();
                                        const msg = (notif.data?.message || '').toLowerCase();

                                        return actionable === 'attendance' ||
                                               type === 'attendance' ||
                                               title.includes('jadwal bimbingan baru') ||
                                               title.includes('menunggu konfirmasi') ||
                                               msg.includes('menjadwalkan');
                                    },

                                    async confirmAttendance(notif, status, reason = null) {
                                        const mentoringId = notif.data?.mentoring_id;
                                        if (!mentoringId) return;

                                        this.attendanceLoading[mentoringId] = true;
                                        try {
                                            const payload = {
                                                status: status,
                                                _token: '{{ csrf_token() }}'
                                            };
                                            if (status === 'permission' && reason) {
                                                payload.reason = reason;
                                            }

                                            const res = await fetch(`/mentoring-sessions/${mentoringId}/confirm-attendance`, {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-Requested-With': 'XMLHttpRequest',
                                                    'Accept': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify(payload)
                                            });

                                            const data = await res.json();
                                            if (res.ok && data.success) {
                                                this.attendanceSuccess[mentoringId] = status;
                                                this.activePermissionInput = null;
                                                this.markSingleRead(notif.id);
                                            } else {
                                                alert(data.message || 'Gagal menyimpan konfirmasi kehadiran.');
                                            }
                                        } catch (e) {
                                            console.error(e);
                                            alert('Terjadi kesalahan jaringan saat mengonfirmasi kehadiran.');
                                        } finally {
                                            this.attendanceLoading[mentoringId] = false;
                                        }
                                    },

                                    submitPermission(notif) {
                                        const reason = (this.permissionReason[notif.id] || '').trim();
                                        if (!reason) {
                                            alert('Mohon tuliskan alasan berhalangan hadir.');
                                            return;
                                        }
                                        this.confirmAttendance(notif, 'permission', reason);
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
                                        if (title.includes('revisi') || msg.includes('revisi') || type === 'revision') {
                                            return 'revision';
                                        }
                                        if (type === 'message' || title.includes('pesan') || msg.includes('pesan')) {
                                            return 'message';
                                        }
                                        if (type === 'info' || type === 'attendance' || title.includes('jadwal') || msg.includes('menjadwalkan') || msg.includes('jadwal') || msg.includes('reschedule')) {
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
                                        let url = notif.data?.url;
                                        const title = (notif.data?.title || '').toLowerCase();
                                        const msg = (notif.data?.message || '').toLowerCase();
                                        const isLecturer = this.userRole === 'dosen';
                                        const isStaff = this.userRole === 'admin' || this.userRole === 'kaprodi';

                                        // If explicit URL is provided in notification data, validate role compatibility
                                        if (url && url !== '#') {
                                            if (isLecturer) {
                                                // Prevent lecturer from opening student-only revision routes
                                                if (url.includes('student-defense-revisions')) {
                                                    return '{{ route("defense-examiner.index") }}';
                                                }
                                                if (url.includes('student-seminar-revisions')) {
                                                    return '{{ route("seminar-examiner.index") }}';
                                                }
                                                // Prevent lecturer from opening student/admin-only application routes
                                                if (url.includes('seminar-applications') || url.includes('thesis-defense-applications')) {
                                                    return '{{ route("theses.index") }}';
                                                }
                                            }
                                            return url;
                                        }

                                        // 1. Revision Notifications: Role-aware deep link
                                        if (title.includes('revisi') || msg.includes('revisi') || notif.data?.type === 'revision') {
                                            const isSidang = title.includes('sidang') || msg.includes('sidang');
                                            if (isLecturer) {
                                                return isSidang ? '{{ route("defense-examiner.index") }}' : '{{ route("seminar-examiner.index") }}';
                                            }
                                            if (isStaff) {
                                                return isSidang ? '{{ route("thesis-defense-schedules.index") }}' : '{{ route("seminar-schedules.index") }}';
                                            }
                                            return isSidang ? '{{ route("student-defense-revisions.index") }}' : '{{ route("student-seminar-revisions.index") }}';
                                        }

                                        // 2. ACC Notifications: Role-aware deep link
                                        if (title.includes('acc') || msg.includes('acc')) {
                                            if (isLecturer) {
                                                return '{{ route("theses.index") }}';
                                            }
                                            const isSidang = title.includes('sidang') || msg.includes('sidang') || notif.data?.acc_type === 'sidang';
                                            return isSidang ? '{{ route("thesis-defense-applications.index") }}' : '{{ route("seminar-applications.index") }}';
                                        }

                                        // 3. Status Bimbingan / Jadwal: Deep link to mentoring-sessions.index with anchor/highlight
                                        if (notif.data?.mentoring_id) {
                                            return '{{ route("mentoring-sessions.index") }}?highlight=' + notif.data.mentoring_id + '#session-' + notif.data.mentoring_id;
                                        }

                                        // 4. Jadwal / Penugasan
                                        if (title.includes('jadwal') || msg.includes('jadwal') || title.includes('penugasan') || msg.includes('penugasan')) {
                                            const isSidang = title.includes('sidang') || msg.includes('sidang');
                                            if (isLecturer) {
                                                return isSidang ? '{{ route("defense-examiner.index") }}' : '{{ route("seminar-examiner.index") }}';
                                            }
                                        }

                                        if (notif.data?.thesis_id) return '{{ route("theses.index") }}';
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
                        
                        <a href="{{ route('profile.edit') }}" class="flex items-center pl-2.5 sm:pl-5 border-l border-slate-100 dark:border-slate-700 ml-1 sm:ml-2 group/profile">
                            <div class="hidden sm:flex flex-col items-end mr-4">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-none mb-1 group-hover/profile:text-orange-600 transition-colors">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] font-black text-orange-600 uppercase tracking-widest">{{ Auth::user()->role }}</span>
                            </div>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl overflow-hidden shadow-md shadow-orange-200 dark:shadow-none ring-2 ring-white dark:ring-slate-700 cursor-pointer hover:scale-105 transition-transform relative group">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </a>
                    </div>
                </header>

                <!-- Page Content (Mobile Optimized Padding & Safe Area) -->
                <main class="flex-1 overflow-y-auto p-3 sm:p-5 lg:p-8 z-10 w-full relative pb-[max(1rem,env(safe-area-inset-bottom))]">
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

        @include('components.bug-report-widget')

        <!-- Global Slide-Over Drawer Kemiripan Judul -->
        <div x-data="{ 
            open: false, 
            data: { student: '', npm: '', title: '', score: 0, matches: [] } 
        }"
        @open-audit-modal.window="data = $event.detail; open = true"
        x-show="open" 
        class="fixed inset-0 overflow-hidden"
        style="display: none; z-index: 99999 !important;"
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

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10 pointer-events-none" style="z-index: 100000 !important;">
                <div x-show="open"
                     x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="w-screen max-w-lg bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200 dark:border-slate-800 flex flex-col h-full relative pointer-events-auto">
                    
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

        <!-- Instant Navigation: Just-In-Time Link Prefetching -->
        <script src="{{ asset('assets/vendor/instantpage/instantpage.min.js') }}" type="module"></script>

        <!-- Progressive Web App (PWA) Install Banner -->
        <x-pwa-install-banner />

        @stack('scripts')
    </body>
</html>

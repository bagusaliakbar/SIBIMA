<!DOCTYPE html>
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
        
        @include('layouts.partials.styles')
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
        <a href="#main-content"
           class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[90] focus:px-4 focus:py-2.5 focus:rounded-xl focus:bg-orange-600 focus:text-white focus:text-xs focus:font-bold focus:shadow-lg">
            Lewati ke Konten Utama
        </a>
        @php
            $isBugReportEnabled = \App\Models\Setting::isBugReportEnabled();
        @endphp
        <div class="flex h-screen w-full overflow-hidden">
            @include('layouts.partials.sidebar')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 relative transition-colors duration-300">
                @include('layouts.partials.topbar')

                <!-- Page Content (Mobile Optimized Padding & Safe Area) -->
                <main id="main-content" tabindex="-1" class="flex-1 overflow-y-auto p-3 sm:p-5 lg:p-8 z-10 w-full relative pb-24 sm:pb-6 flex flex-col focus:outline-none">
                    @isset($header)
                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between shrink-0">
                            {{ $header }}
                        </div>
                    @endisset
                    
                    <div class="flex-1 min-w-0">
                        {{ $slot }}
                    </div>

                    @unless(request()->routeIs('chat.*'))
                        <!-- Slim Non-Intrusive Enterprise Footer -->
                        <footer class="mt-8 pt-3.5 border-t border-slate-200/75 dark:border-slate-800/80 {{ $isBugReportEnabled ? 'md:pr-20' : '' }} flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-500 dark:text-slate-400 print:hidden shrink-0">
                            <div class="flex items-center gap-2 text-center sm:text-left">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 shrink-0 hidden sm:inline-block"></span>
                                <span class="font-semibold text-slate-600 dark:text-slate-300">&copy; {{ date('Y') }} SIBIMA</span>
                                <span class="text-slate-300 dark:text-slate-700">&middot;</span>
                                <span>Sistem Informasi Bimbingan Mahasiswa <span class="hidden md:inline">&mdash; FASILKOM Universitas Subang</span></span>
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="{{ route('faqs.index') }}" class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors font-medium">
                                    FAQ
                                </a>
                                <span class="text-slate-300 dark:text-slate-700">&middot;</span>
                                <button type="button"
                                        onclick="if (window.openBugReportModal) window.openBugReportModal();"
                                        class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors font-medium cursor-pointer">
                                    Lapor Kendala
                                </button>
                                <span class="text-slate-300 dark:text-slate-700">&middot;</span>
                                <button type="button"
                                        @click="$dispatch('open-changelog-modal')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100/80 hover:bg-orange-50 dark:bg-slate-800/80 dark:hover:bg-orange-950/40 border border-slate-200/80 hover:border-orange-200 dark:border-slate-700/80 dark:hover:border-orange-800/60 text-[11px] font-mono font-semibold text-slate-600 hover:text-orange-600 dark:text-slate-300 dark:hover:text-orange-400 transition-colors cursor-pointer"
                                        title="Lihat Catatan Versi &amp; Pembaruan Sistem">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                    <span>{{ config('changelog.current_version', 'v2.5.0') }}</span>
                                </button>
                            </div>
                        </footer>
                    @endunless
                </main>

                <!-- Mobile Bottom Navigation Bar (PWA One-Handed Thumb Zone) -->
                <x-mobile-bottom-nav />
            </div>
        </div>

        <x-toast />

        @include('components.confirm-dialog')
        @include('components.changelog-modal')
        @include('components.bug-report-widget')
        @include('components.session-timeout-modal')

        @include('layouts.partials.audit-drawer')

        @include('layouts.partials.scripts')

        <!-- Instant Navigation: Just-In-Time Link Prefetching -->
        <script src="{{ asset('assets/vendor/instantpage/instantpage.min.js') }}" type="module"></script>

        <!-- Progressive Web App (PWA) Install Banner -->
        <x-pwa-install-banner />

        @stack('scripts')
    </body>
</html>

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
            $maintenanceConfig = \App\Models\Setting::getMaintenanceConfig();
            $showUserMaintenanceBanner = !empty($maintenanceConfig['enabled'])
                && ($maintenanceConfig['mode'] ?? 'banner') === 'banner'
                && \App\Models\Setting::isMaintenanceActiveForRole(Auth::user()?->role, $maintenanceConfig);
            $showAdminMaintenanceNotice = !empty($maintenanceConfig['enabled'])
                && in_array(Auth::user()?->role, ['admin', 'kaprodi'], true);
        @endphp
        <div class="flex h-screen w-full overflow-hidden">
            @include('layouts.partials.sidebar')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 relative transition-colors duration-300">
                @if(session()->has(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_ID))
                    <div role="status" class="bg-amber-50 dark:bg-amber-950/80 border-b border-amber-300 dark:border-amber-800 px-3 sm:px-6 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-amber-950 dark:text-amber-100 shrink-0 z-30 print:hidden">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-7 h-7 rounded-lg bg-amber-500/20 dark:bg-amber-500/30 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <span class="font-extrabold uppercase tracking-wider text-amber-800 dark:text-amber-300">Mode Login As Aktif:</span>
                                <span class="ml-1">
                                    Anda masuk sebagai <strong class="font-bold">{{ Auth::user()?->name }}</strong>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-200/80 dark:bg-amber-900/80 text-amber-900 dark:text-amber-200 text-[11px] font-bold uppercase ml-1">{{ Auth::user()?->role }}</span>
                                    @if(Auth::user()?->identifier)
                                        <span class="font-mono text-[11px] text-amber-800 dark:text-amber-300 ml-1">({{ Auth::user()->identifier }})</span>
                                    @endif
                                </span>
                                <span class="hidden md:inline text-amber-800/90 dark:text-amber-300/90 ml-1">
                                    &mdash; Akun utama: {{ session(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_NAME) }}
                                </span>
                            </div>
                        </div>
                        <form action="{{ route('impersonate.leave') }}" method="POST" class="shrink-0">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Kembali ke {{ ucfirst(session(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_ROLE, 'Admin')) }}</span>
                            </button>
                        </form>
                    </div>
                @endif

                @if($showUserMaintenanceBanner)
                    <div role="status" class="bg-amber-50 dark:bg-amber-950/75 border-b border-amber-300 dark:border-amber-800 px-3 sm:px-6 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-amber-950 dark:text-amber-100 shrink-0 z-20 print:hidden">
                        <div class="flex items-start sm:items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="min-w-0">
                                <span class="font-bold text-amber-900 dark:text-amber-200">{{ $maintenanceConfig['title'] }}:</span>
                                <span class="ml-1 text-amber-900/90 dark:text-amber-100/90">{{ $maintenanceConfig['message'] }}</span>
                            </div>
                        </div>
                        @if(!empty($maintenanceConfig['estimate']))
                            <span class="px-2.5 py-1 rounded-lg bg-amber-200/80 dark:bg-amber-900/80 text-amber-900 dark:text-amber-200 text-[11px] font-bold whitespace-nowrap shrink-0 self-start sm:self-auto">
                                Estimasi: {{ $maintenanceConfig['estimate'] }}
                            </span>
                        @endif
                    </div>
                @elseif($showAdminMaintenanceNotice)
                    <div role="status" class="bg-rose-50 dark:bg-rose-950/70 border-b border-rose-200 dark:border-rose-800 px-3 sm:px-6 py-2 flex items-center justify-between gap-3 text-xs text-rose-900 dark:text-rose-200 shrink-0 z-20 print:hidden">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                            <span class="font-semibold truncate">
                                Mode Pemeliharaan Aktif ({{ $maintenanceConfig['mode'] === 'lock' ? 'Kunci Akses' : 'Banner' }} &mdash; Target: {{ $maintenanceConfig['target'] === 'both' ? 'Mahasiswa & Dosen' : ucfirst($maintenanceConfig['target']) }})
                            </span>
                        </div>
                        <a href="{{ route('admin.maintenance.index') }}" class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-bold whitespace-nowrap shrink-0 transition-colors">
                            Kelola
                        </a>
                    </div>
                @endif

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

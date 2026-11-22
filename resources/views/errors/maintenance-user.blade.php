@php
    $endsAtIso = $config['ends_at'] ?? '';
    $formattedEndsAt = null;
    if (!empty($endsAtIso)) {
        try {
            $formattedEndsAt = \Carbon\Carbon::parse($endsAtIso)
                ->timezone(config('app.timezone', 'Asia/Jakarta'))
                ->locale('id')
                ->translatedFormat('d M Y, H:i') . ' WIB';
        } catch (\Throwable $e) {
            $formattedEndsAt = null;
            $endsAtIso = '';
        }
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          darkMode: localStorage.getItem('darkMode') === 'true',
          reloading: false,
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('darkMode', this.darkMode);
          }
      }"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $config['title'] ?? 'Pemeliharaan Sistem' }} - {{ config('app.name', 'SIBIMA') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        </script>
        <style>
            .maintenance-grid-bg {
                background-image: radial-gradient(rgba(148, 163, 184, 0.28) 1px, transparent 1px);
                background-size: 24px 24px;
            }
            .dark .maintenance-grid-bg {
                background-image: radial-gradient(rgba(148, 163, 184, 0.12) 1px, transparent 1px);
            }
            @keyframes slow-spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            .animate-slow-spin {
                animation: slow-spin 18s linear infinite;
            }
            @media (prefers-reduced-motion: reduce) {
                .animate-slow-spin {
                    animation: none;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen relative overflow-x-hidden flex flex-col justify-between">
        <!-- Subtle Enterprise Dot Grid & Soft Top Spotlight -->
        <div class="pointer-events-none fixed inset-0 maintenance-grid-bg" aria-hidden="true"></div>
        <div class="pointer-events-none fixed inset-x-0 top-0 h-96 bg-[radial-gradient(ellipse_at_top,rgba(234,88,12,0.10),transparent_65%)] dark:bg-[radial-gradient(ellipse_at_top,rgba(234,88,12,0.16),transparent_65%)]" aria-hidden="true"></div>

        <!-- Top Bar -->
        <header class="relative z-10 w-full max-w-2xl mx-auto px-4 pt-6 sm:pt-8 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-orange-600 text-white flex items-center justify-center shadow-sm shadow-orange-600/20 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-extrabold tracking-tight text-slate-900 dark:text-white">SIBIMA</p>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400">FASILKOM Universitas Subang</p>
                </div>
            </div>

            <button type="button" @click="toggleTheme()"
                    class="h-9 px-3 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold inline-flex items-center gap-1.5 shadow-xs transition-colors cursor-pointer"
                    :aria-label="darkMode ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'">
                <svg x-show="!darkMode" class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <svg x-show="darkMode" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span x-text="darkMode ? 'Terang' : 'Gelap'"></span>
            </button>
        </header>

        <!-- Main Card -->
        <main class="relative z-10 w-full max-w-2xl mx-auto px-4 py-6 sm:py-8 flex-1 flex items-center justify-center">
            <div class="w-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm border border-slate-200/90 dark:border-slate-800 rounded-3xl shadow-xl shadow-slate-200/60 dark:shadow-black/40 overflow-hidden">
                <!-- Precision Top Accent Bar -->
                <div class="h-1.5 w-full bg-orange-600 dark:bg-orange-500"></div>

                <div class="p-6 sm:p-10 space-y-7 text-center">
                    <!-- Precision Emblem + Live Status Badge -->
                    <div class="flex flex-col items-center gap-4">
                        <div class="relative flex items-center justify-center">
                            <div class="w-20 h-20 rounded-3xl border border-dashed border-orange-300/80 dark:border-orange-700/60 flex items-center justify-center bg-orange-50/40 dark:bg-orange-950/20">
                                <div class="w-14 h-14 rounded-2xl bg-orange-600/10 dark:bg-orange-500/15 border border-orange-200 dark:border-orange-800/70 text-orange-600 dark:text-orange-400 flex items-center justify-center shadow-xs">
                                    <svg class="w-7 h-7 animate-slow-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-amber-500 border-2 border-white dark:border-slate-900"></span>
                            </span>
                        </div>

                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/90 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/60">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Pemeliharaan Sistem Sedang Berlangsung</span>
                        </span>
                    </div>

                    <!-- Headline & Message -->
                    <div class="space-y-3 max-w-xl mx-auto">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                            {{ $config['title'] ?: 'Pemeliharaan Sistem SIBIMA' }}
                        </h1>
                        <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-300 leading-relaxed">
                            {!! nl2br(e($config['message'] ?: 'Sistem sedang dalam pemeliharaan terjadwal untuk peningkatan layanan dan performa. Mohon maaf atas ketidaknyamanannya.')) !!}
                        </p>
                    </div>

                    <!-- Live Countdown Panel -->
                    @if(!empty($endsAtIso))
                        <div x-data="{
                                targetMs: new Date(@js($endsAtIso)).getTime(),
                                days: '00',
                                hours: '00',
                                minutes: '00',
                                seconds: '00',
                                finished: false,
                                timer: null,
                                tick() {
                                    const diff = this.targetMs - Date.now();
                                    if (isNaN(diff) || diff <= 0) {
                                        this.days = '00';
                                        this.hours = '00';
                                        this.minutes = '00';
                                        this.seconds = '00';
                                        this.finished = true;
                                        if (this.timer) {
                                            clearInterval(this.timer);
                                            this.timer = null;
                                        }
                                        return;
                                    }
                                    const totalSec = Math.floor(diff / 1000);
                                    const d = Math.floor(totalSec / 86400);
                                    const h = Math.floor((totalSec % 86400) / 3600);
                                    const m = Math.floor((totalSec % 3600) / 60);
                                    const s = totalSec % 60;
                                    this.days = String(d).padStart(2, '0');
                                    this.hours = String(h).padStart(2, '0');
                                    this.minutes = String(m).padStart(2, '0');
                                    this.seconds = String(s).padStart(2, '0');
                                },
                                init() {
                                    this.tick();
                                    if (!this.finished) {
                                        this.timer = setInterval(() => this.tick(), 1000);
                                    }
                                }
                             }"
                             class="rounded-2xl bg-slate-50/90 dark:bg-slate-800/50 border border-slate-200/90 dark:border-slate-700/80 p-5 sm:p-6 space-y-4">
                            <div class="flex items-center justify-center gap-2 text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span x-text="finished ? 'Estimasi Waktu Pemeliharaan Tercapai' : 'Hitung Mundur Estimasi Selesai'"></span>
                            </div>

                            <!-- 4-Tile Tabular Countdown -->
                            <div x-show="!finished" class="grid grid-cols-4 gap-2.5 sm:gap-3.5 max-w-md mx-auto">
                                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700 py-3 sm:py-4 px-2 shadow-xs">
                                    <div class="text-2xl sm:text-3xl font-extrabold tabular-nums tracking-tight text-slate-900 dark:text-white" x-text="days">00</div>
                                    <div class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-1">Hari</div>
                                </div>
                                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700 py-3 sm:py-4 px-2 shadow-xs">
                                    <div class="text-2xl sm:text-3xl font-extrabold tabular-nums tracking-tight text-slate-900 dark:text-white" x-text="hours">00</div>
                                    <div class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-1">Jam</div>
                                </div>
                                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700 py-3 sm:py-4 px-2 shadow-xs">
                                    <div class="text-2xl sm:text-3xl font-extrabold tabular-nums tracking-tight text-slate-900 dark:text-white" x-text="minutes">00</div>
                                    <div class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-1">Menit</div>
                                </div>
                                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-orange-200 dark:border-orange-800/70 py-3 sm:py-4 px-2 shadow-xs">
                                    <div class="text-2xl sm:text-3xl font-extrabold tabular-nums tracking-tight text-orange-600 dark:text-orange-400" x-text="seconds">00</div>
                                    <div class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-orange-700/80 dark:text-orange-400/80 mt-1">Detik</div>
                                </div>
                            </div>

                            <!-- Finished State -->
                            <div x-show="finished" x-cloak class="rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 px-4 py-3 text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Estimasi waktu telah tercapai. Klik <strong>Cek Status Sekarang</strong> untuk memeriksa ketersediaan sistem.</span>
                            </div>

                            <!-- Target Timestamp + Optional Estimate Label -->
                            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1.5 text-xs text-slate-600 dark:text-slate-300 pt-1">
                                @if($formattedEndsAt)
                                    <span class="inline-flex items-center gap-1.5 font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                        <span>Target selesai: {{ $formattedEndsAt }}</span>
                                    </span>
                                @endif
                                @if(!empty($config['estimate']))
                                    <span class="inline-flex items-center gap-1.5 font-medium text-slate-500 dark:text-slate-400">
                                        <span>&middot;</span>
                                        <span>Estimasi selesai: {{ $config['estimate'] }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @elseif(!empty($config['estimate']))
                        <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Estimasi selesai: {{ $config['estimate'] }}</span>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="pt-5 border-t border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-center gap-2.5">
                        @if(session()->has(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_ID))
                            <form action="{{ route('impersonate.leave') }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto h-10 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm transition-colors inline-flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                    </svg>
                                    <span>Kembali ke {{ ucfirst(session(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_ROLE, 'Admin')) }}</span>
                                </button>
                            </form>
                        @endif

                        <button type="button"
                                @click="reloading = true; window.location.reload()"
                                :disabled="reloading"
                                class="w-full sm:w-auto h-10 px-5 rounded-xl bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white text-xs font-bold shadow-sm shadow-orange-600/20 transition-colors inline-flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75">
                            <svg class="w-4 h-4" :class="{ 'animate-spin': reloading }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span x-text="reloading ? 'Memeriksa...' : 'Cek Status Sekarang'"></span>
                        </button>

                        <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto h-10 px-4 rounded-xl bg-white hover:bg-rose-50 dark:bg-slate-900 dark:hover:bg-rose-950/40 text-slate-700 hover:text-rose-700 dark:text-slate-200 dark:hover:text-rose-300 border border-slate-200 hover:border-rose-200 dark:border-slate-700 dark:hover:border-rose-800 text-xs font-bold transition-colors inline-flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full max-w-2xl mx-auto px-4 pb-6 text-center space-y-1">
            @if(!empty($user))
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Masuk sebagai <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $user->name }}</span>
                    ({{ ucfirst($user->role) }})
                </p>
            @endif
            <p class="text-[11px] text-slate-400 dark:text-slate-500">
                &copy; {{ date('Y') }} SIBIMA &middot; Sistem Informasi Bimbingan Mahasiswa &middot; FASILKOM Universitas Subang
            </p>
        </footer>
    </body>
</html>

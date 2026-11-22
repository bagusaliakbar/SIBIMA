@php
    $endsAtIso = \App\Models\Setting::resolveMaintenanceEndsAt($config) ?? '';
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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
          darkMode: localStorage.getItem('darkMode') === 'true',
          reloading: false,
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('darkMode', this.darkMode);
          }
      }" :class="{ 'dark': darkMode }">

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
            background-size: 26px 26px;
        }

        .dark .maintenance-grid-bg {
            background-image: radial-gradient(rgba(148, 163, 184, 0.12) 1px, transparent 1px);
        }

        /* Construction Scene Theme Tokens */
        .maint-scene {
            --scene-ink: #1e293b;
            --scene-ink-soft: #475569;
            --scene-surface: #ffffff;
            --scene-surface-alt: #f1f5f9;
            --scene-depth: #cbd5e1;
            --scene-steel: #94a3b8;
            --scene-sky: #fff7ed;
            --scene-ground: #cbd5e1;
            --scene-shadow: rgba(15, 23, 42, 0.08);
        }

        .dark .maint-scene {
            --scene-ink: #e2e8f0;
            --scene-ink-soft: #94a3b8;
            --scene-surface: #1e293b;
            --scene-surface-alt: #0f172a;
            --scene-depth: #334155;
            --scene-steel: #64748b;
            --scene-sky: rgba(234, 88, 12, 0.10);
            --scene-ground: #334155;
            --scene-shadow: rgba(0, 0, 0, 0.38);
        }

        @keyframes gear-cw {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes gear-ccw {
            from {
                transform: rotate(360deg);
            }

            to {
                transform: rotate(0deg);
            }
        }

        @keyframes crane-trolley {

            0%,
            100% {
                transform: translateX(-6px);
            }

            50% {
                transform: translateX(8px);
            }
        }

        @keyframes crane-hoist {

            0%,
            100% {
                transform: translateY(0px) rotate(-1.4deg);
            }

            30% {
                transform: translateY(5px) rotate(1.2deg);
            }

            60% {
                transform: translateY(8px) rotate(-1deg);
            }

            85% {
                transform: translateY(2px) rotate(1deg);
            }
        }

        @keyframes worker-hammer {

            0%,
            100% {
                transform: rotate(-28deg);
            }

            35% {
                transform: rotate(18deg);
            }

            50% {
                transform: rotate(14deg);
            }

            75% {
                transform: rotate(-28deg);
            }
        }

        @keyframes worker-wave {

            0%,
            100% {
                transform: rotate(-18deg);
            }

            25% {
                transform: rotate(-42deg);
            }

            50% {
                transform: rotate(-16deg);
            }

            75% {
                transform: rotate(-40deg);
            }
        }

        @keyframes worker-nod {

            0%,
            100% {
                transform: rotate(0deg);
            }

            50% {
                transform: rotate(-6deg);
            }
        }

        @keyframes spark-pop {

            0%,
            30%,
            58%,
            100% {
                opacity: 0;
                transform: scale(0.4);
            }

            38% {
                opacity: 1;
                transform: scale(1.15);
            }

            48% {
                opacity: 0.7;
                transform: scale(1);
            }
        }

        @keyframes beacon-blink {

            0%,
            100% {
                opacity: 0.35;
            }

            50% {
                opacity: 1;
            }
        }

        @keyframes float-slow {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        @keyframes cloud-drift {

            0%,
            100% {
                transform: translateX(0px);
            }

            50% {
                transform: translateX(10px);
            }
        }

        @keyframes progress-scan {
            0% {
                transform: scaleX(0.32);
            }

            50% {
                transform: scaleX(0.88);
            }

            100% {
                transform: scaleX(0.32);
            }
        }

        .anim-gear-cw {
            animation: gear-cw 10s linear infinite;
        }

        .anim-gear-ccw {
            animation: gear-ccw 6.5s linear infinite;
        }

        .anim-trolley {
            animation: crane-trolley 9s ease-in-out infinite;
        }

        .anim-hoist {
            animation: crane-hoist 5.2s ease-in-out infinite;
        }

        .anim-hammer {
            animation: worker-hammer 1.5s ease-in-out infinite;
        }

        .anim-wave {
            animation: worker-wave 2.6s ease-in-out infinite;
        }

        .anim-nod {
            animation: worker-nod 3.4s ease-in-out infinite;
        }

        .anim-spark {
            animation: spark-pop 1.5s ease-out infinite;
        }

        .anim-beacon {
            animation: beacon-blink 1.4s ease-in-out infinite;
        }

        .anim-float {
            animation: float-slow 3.8s ease-in-out infinite;
        }

        .anim-cloud {
            animation: cloud-drift 11s ease-in-out infinite;
        }

        .anim-progress {
            animation: progress-scan 3.4s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {

            .anim-gear-cw,
            .anim-gear-ccw,
            .anim-trolley,
            .anim-hoist,
            .anim-hammer,
            .anim-wave,
            .anim-nod,
            .anim-spark,
            .anim-beacon,
            .anim-float,
            .anim-cloud,
            .anim-progress {
                animation: none;
            }
        }
    </style>
</head>

<body
    class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen relative overflow-x-hidden flex flex-col">
    <!-- Full-Bleed Top Brand Accent Line -->
    <div class="relative z-20 h-1.5 w-full bg-orange-600 dark:bg-orange-500 shrink-0"></div>

    <!-- Subtle Architectural Dot Grid & Ambient Spotlight -->
    <div class="pointer-events-none fixed inset-0 maintenance-grid-bg" aria-hidden="true"></div>
    <div class="pointer-events-none fixed inset-x-0 top-0 h-[440px] bg-[radial-gradient(ellipse_at_top_right,rgba(234,88,12,0.11),transparent_62%)] dark:bg-[radial-gradient(ellipse_at_top_right,rgba(234,88,12,0.16),transparent_62%)]"
        aria-hidden="true"></div>
    <div class="pointer-events-none fixed inset-x-0 bottom-0 h-80 bg-[radial-gradient(ellipse_at_bottom_left,rgba(245,158,11,0.08),transparent_60%)] dark:bg-[radial-gradient(ellipse_at_bottom_left,rgba(245,158,11,0.10),transparent_60%)]"
        aria-hidden="true"></div>

    <!-- Full-Width Top Navigation Bar -->
    <header
        class="relative z-10 w-full px-5 sm:px-8 lg:px-14 xl:px-20 pt-5 sm:pt-7 flex items-center justify-between gap-4 shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-10 h-10 object-contain shrink-0">
            <div class="whitespace-nowrap">
                <p class="text-xl font-black text-slate-900 dark:text-white tracking-tighter leading-none">SIBIMA</p>
                <span
                    class="text-[11px] uppercase tracking-wider text-orange-600 dark:text-orange-400 font-bold block mt-0.5">Fasilkom
                    UNSUB</span>
            </div>
        </div>

        <button type="button" @click="toggleTheme()"
            class="h-10 px-3.5 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold inline-flex items-center gap-2 shadow-xs transition-colors cursor-pointer"
            :aria-label="darkMode ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'">
            <svg x-show="!darkMode" class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            <svg x-show="darkMode" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>
            <span x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
        </button>
    </header>

    <!-- Full-Width Immersive Main Content (No Centered Container, No Side-by-Side Grid) -->
    <main
        class="relative z-10 w-full px-5 sm:px-8 lg:px-14 xl:px-20 py-5 sm:py-7 flex-1 flex flex-col justify-between gap-6">
        <!-- Top Row: Full-Width Headline + Message (Left) & Actions (Right) -->
        <div class="w-full flex flex-col lg:flex-row lg:items-end justify-between gap-5">
            <div class="space-y-2 max-w-3xl text-left">
                <h1
                    class="text-2xl sm:text-4xl xl:text-[42px] font-extrabold text-slate-900 dark:text-white tracking-tight leading-[1.12]">
                    {{ $config['title'] ?: 'Pemeliharaan Sistem SIBIMA' }}
                </h1>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed">
                    {!! nl2br(e($config['message'] ?: 'Sistem sedang dalam pemeliharaan terjadwal untuk peningkatan layanan dan performa. Mohon maaf atas ketidaknyamanannya.')) !!}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                @if(session()->has(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_ID))
                    <form action="{{ route('impersonate.leave') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="h-11 px-5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs sm:text-sm font-bold shadow-sm transition-colors inline-flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                            </svg>
                            <span>Kembali ke
                                {{ ucfirst(session(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_ROLE, 'Admin')) }}</span>
                        </button>
                    </form>
                @endif

                <button type="button" @click="reloading = true; window.location.reload()" :disabled="reloading"
                    class="h-11 px-6 rounded-xl bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white text-xs sm:text-sm font-bold shadow-sm shadow-orange-600/20 transition-colors inline-flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75">
                    <svg class="w-4 h-4" :class="{ 'animate-spin': reloading }" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    <span x-text="reloading ? 'Memeriksa...' : 'Cek Status Sekarang'"></span>
                </button>

                @if(!empty($user))
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="h-11 px-5 rounded-xl bg-white hover:bg-rose-50 dark:bg-slate-900 dark:hover:bg-rose-950/40 text-slate-700 hover:text-rose-700 dark:text-slate-200 dark:hover:text-rose-300 border border-slate-200 hover:border-rose-200 dark:border-slate-700 dark:hover:border-rose-800 text-xs sm:text-sm font-bold shadow-2xs transition-colors inline-flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Centerpiece: Large Enlarged Animated 3D-Depth Construction Showcase -->
        <div class="w-full flex-1 flex items-center justify-center py-2">
            <svg class="maint-scene w-full max-w-[1160px] h-auto max-h-[48vh] overflow-visible select-none"
                viewBox="0 0 520 222" preserveAspectRatio="xMidYMid meet" fill="none" xmlns="http://www.w3.org/2000/svg"
                role="img" aria-label="Ilustrasi tim teknisi sedang mengerjakan proyek pemeliharaan sistem SIBIMA">
                <defs>
                    <linearGradient id="maint-orange-grad" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#fb923c" />
                        <stop offset="100%" stop-color="#ea580c" />
                    </linearGradient>
                    <linearGradient id="maint-amber-grad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#fcd34d" />
                        <stop offset="100%" stop-color="#f59e0b" />
                    </linearGradient>
                    <linearGradient id="maint-building-front" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="var(--scene-surface)" />
                        <stop offset="100%" stop-color="var(--scene-surface-alt)" />
                    </linearGradient>
                    <linearGradient id="maint-beacon-glow" x1="0.5" y1="0" x2="0.5" y2="1">
                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.65" />
                        <stop offset="100%" stop-color="#f59e0b" stop-opacity="0" />
                    </linearGradient>
                </defs>

                <!-- Soft Ambient Backdrop Stage -->
                <ellipse cx="260" cy="138" rx="220" ry="76" fill="var(--scene-sky)" />

                <!-- Soft Drifting Clouds -->
                <g class="anim-cloud" opacity="0.75">
                    <path d="M54 52h38a8 8 0 0 0 0-16 11 11 0 0 0-21-3 9 9 0 0 0-17 4 7.5 7.5 0 0 0 0 15Z"
                        fill="var(--scene-surface-alt)" stroke="var(--scene-depth)" stroke-width="1.2" />
                    <path d="M188 30h28a6 6 0 0 0 0-12 8.5 8.5 0 0 0-16-2 7 7 0 0 0-12 3 5.5 5.5 0 0 0 0 11Z"
                        fill="var(--scene-surface-alt)" stroke="var(--scene-depth)" stroke-width="1.1" />
                </g>

                <!-- Floating Accent Particles -->
                <g class="anim-float">
                    <rect x="308" y="20" width="7" height="7" rx="1.5" fill="#fbbf24" stroke="var(--scene-ink)"
                        stroke-width="1.3" />
                    <circle cx="88" cy="84" r="3" fill="#fb923c" />
                    <path d="M116 32v7M112.5 35.5h7" stroke="var(--scene-steel)" stroke-width="1.6"
                        stroke-linecap="round" />
                    <path d="M472 96v6M469 99h6" stroke="#f59e0b" stroke-width="1.6" stroke-linecap="round" />
                    <!-- Floating Code Token Badge -->
                    <g transform="translate(106, 98)">
                        <circle cx="0" cy="0" r="12" fill="url(#maint-amber-grad)" stroke="var(--scene-ink)"
                            stroke-width="1.6" />
                        <path d="M-4.5 -2.5L-7.5 0l3 2.5M4.5 -2.5L7.5 0l-3 2.5M1.2 -4l-2.4 8" stroke="#1e293b"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                </g>

                <!-- Interlocking Animated Gears -->
                <g transform="translate(140, 62)">
                    <g class="anim-gear-cw">
                        <path
                            d="M-3 -17h6l1.2 3.8a13.2 13.2 0 0 1 3.3 1.4l3.5-1.8 4.2 4.2-1.8 3.5a13.2 13.2 0 0 1 1.4 3.3L17 -3v6l-3.8 1.2a13.2 13.2 0 0 1-1.4 3.3l1.8 3.5-4.2 4.2-3.5-1.8a13.2 13.2 0 0 1-3.3 1.4L3 17h-6l-1.2-3.8a13.2 13.2 0 0 1-3.3-1.4l-3.5 1.8-4.2-4.2 1.8-3.5a13.2 13.2 0 0 1-1.4-3.3L-17 3v-6l3.8-1.2a13.2 13.2 0 0 1 1.4-3.3l-1.8-3.5 4.2-4.2 3.5 1.8a13.2 13.2 0 0 1 3.3-1.4Z"
                            fill="url(#maint-amber-grad)" stroke="var(--scene-ink)" stroke-width="1.5"
                            stroke-linejoin="round" />
                        <circle cx="0" cy="0" r="6" fill="var(--scene-surface)" stroke="var(--scene-ink)"
                            stroke-width="1.5" />
                    </g>
                </g>
                <g transform="translate(162, 74)">
                    <g class="anim-gear-ccw">
                        <path
                            d="M-2 -11.5h4l.9 2.6a9 9 0 0 1 2.2 1l2.4-1.2 2.8 2.8-1.2 2.4a9 9 0 0 1 1 2.2L11.5 -2v4l-2.6.9a9 9 0 0 1-1 2.2l1.2 2.4-2.8 2.8-2.4-1.2a9 9 0 0 1-2.2 1L2 11.5h-4l-.9-2.6a9 9 0 0 1-2.2-1l-2.4 1.2-2.8-2.8 1.2-2.4a9 9 0 0 1-1-2.2L-11.5 2v-4l2.6-.9a9 9 0 0 1 1-2.2l-1.2-2.4 2.8-2.8 2.4 1.2a9 9 0 0 1 2.2-1Z"
                            fill="var(--scene-depth)" stroke="var(--scene-ink)" stroke-width="1.4"
                            stroke-linejoin="round" />
                        <circle cx="0" cy="0" r="4" fill="var(--scene-surface)" stroke="var(--scene-ink)"
                            stroke-width="1.4" />
                    </g>
                </g>

                <!-- Ground Contact Shadows -->
                <ellipse cx="258" cy="199" rx="76" ry="5" fill="var(--scene-shadow)" />
                <ellipse cx="424" cy="199" rx="34" ry="4.5" fill="var(--scene-shadow)" />
                <ellipse cx="86" cy="199" rx="22" ry="4" fill="var(--scene-shadow)" />
                <ellipse cx="362" cy="199" rx="20" ry="4" fill="var(--scene-shadow)" />

                <!-- TOWER CRANE (Right Side) -->
                <g id="tower-crane">
                    <!-- Concrete Base (3D Extruded) -->
                    <polygon points="444,185 453,181 453,195 444,198" fill="var(--scene-depth)"
                        stroke="var(--scene-ink)" stroke-width="1.6" stroke-linejoin="round" />
                    <rect x="398" y="185" width="46" height="13" rx="2" fill="var(--scene-surface-alt)"
                        stroke="var(--scene-ink)" stroke-width="1.6" />
                    <rect x="408" y="177" width="26" height="8" rx="1.5" fill="url(#maint-orange-grad)"
                        stroke="var(--scene-ink)" stroke-width="1.5" />

                    <!-- Vertical Truss Mast -->
                    <rect x="415" y="28" width="14" height="149" fill="var(--scene-surface)" stroke="var(--scene-ink)"
                        stroke-width="1.7" />
                    <!-- Diagonal Cross Bracing -->
                    <path
                        d="M415 44l14 14M429 44l-14 14M415 62l14 14M429 62l-14 14M415 80l14 14M429 80l-14 14M415 98l14 14M429 98l-14 14M415 116l14 14M429 116l-14 14M415 134l14 14M429 134l-14 14M415 152l14 14M429 152l-14 14M415 168l14 9"
                        stroke="var(--scene-ink)" stroke-width="1.25" />

                    <!-- Top A-Frame Peak & Guy Wires -->
                    <polygon points="415,28 422,12 429,28" fill="var(--scene-surface-alt)" stroke="var(--scene-ink)"
                        stroke-width="1.7" stroke-linejoin="round" />
                    <line x1="422" y1="12" x2="210" y2="38" stroke="var(--scene-ink)" stroke-width="1.4" />
                    <line x1="422" y1="12" x2="472" y2="38" stroke="var(--scene-ink)" stroke-width="1.4" />

                    <!-- Horizontal Jib Boom -->
                    <polygon points="196,43 210,36 474,36 474,48 196,48" fill="var(--scene-surface-alt)"
                        stroke="var(--scene-ink)" stroke-width="1.7" stroke-linejoin="round" />
                    <!-- Jib Lattice Struts -->
                    <path
                        d="M214 36l12 12 12-12 12 12 12-12 12 12 12-12 12 12 12-12 12 12 12-12 12 12 12-12 12 12 12-12 12 12 12-12 12 12 12-12 12 12"
                        stroke="var(--scene-ink)" stroke-width="1.2" stroke-linejoin="round" />

                    <!-- Counterweight Block (Right of Mast) -->
                    <rect x="454" y="48" width="22" height="14" rx="2" fill="url(#maint-amber-grad)"
                        stroke="var(--scene-ink)" stroke-width="1.6" />
                    <line x1="461" y1="48" x2="461" y2="62" stroke="var(--scene-ink)" stroke-width="1.2" />

                    <!-- Operator Cabin -->
                    <rect x="400" y="48" width="17" height="17" rx="2.5" fill="url(#maint-orange-grad)"
                        stroke="var(--scene-ink)" stroke-width="1.6" />
                    <rect x="403" y="51" width="9" height="8" rx="1.2" fill="#e0f2fe" stroke="var(--scene-ink)"
                        stroke-width="1.1" />

                    <!-- Animated Crane Trolley + Hoist + Suspended 3D SIBIMA Module -->
                    <g class="anim-trolley">
                        <g transform="translate(253, 48)">
                            <!-- Trolley Wheels & Carriage -->
                            <rect x="-11" y="0" width="22" height="6" rx="2" fill="url(#maint-orange-grad)"
                                stroke="var(--scene-ink)" stroke-width="1.5" />
                            <!-- Animated Swinging & Hoisting Load (pivots at 0,6) -->
                            <g transform="translate(0, 6)">
                                <g class="anim-hoist">
                                    <!-- Steel Cable -->
                                    <line x1="0" y1="0" x2="0" y2="11" stroke="var(--scene-ink)" stroke-width="1.8" />
                                    <!-- Pulley & Hook -->
                                    <rect x="-4.5" y="9" width="9" height="6.5" rx="2" fill="url(#maint-amber-grad)"
                                        stroke="var(--scene-ink)" stroke-width="1.4" />
                                    <path d="M0 15.5v3.2a2.8 2.8 0 1 1-2.6 2.8" stroke="var(--scene-ink)"
                                        stroke-width="1.6" stroke-linecap="round" />
                                    <!-- Diagonal Rigging Slings -->
                                    <line x1="0" y1="20" x2="-46" y2="33" stroke="var(--scene-ink)"
                                        stroke-width="1.35" />
                                    <line x1="0" y1="20" x2="46" y2="33" stroke="var(--scene-ink)"
                                        stroke-width="1.35" />

                                    <!-- Suspended 3D Modular SIBIMA Floor/Window Block -->
                                    <!-- 3D Right Extrusion Face -->
                                    <polygon points="56,33 64,28 64,71 56,76" fill="var(--scene-depth)"
                                        stroke="var(--scene-ink)" stroke-width="1.6" stroke-linejoin="round" />
                                    <!-- 3D Top Bevel Face -->
                                    <polygon points="-56,33 -48,28 64,28 56,33" fill="var(--scene-surface-alt)"
                                        stroke="var(--scene-ink)" stroke-width="1.6" stroke-linejoin="round" />
                                    <!-- Front UI Card Face -->
                                    <rect x="-56" y="33" width="112" height="43" rx="3" fill="var(--scene-surface)"
                                        stroke="var(--scene-ink)" stroke-width="1.8" />
                                    <!-- Window Top Bar -->
                                    <rect x="-56" y="33" width="112" height="11" rx="2" fill="var(--scene-ink)" />
                                    <circle cx="-46" cy="38.5" r="2.1" fill="#f87171" />
                                    <circle cx="-39" cy="38.5" r="2.1" fill="#fbbf24" />
                                    <circle cx="-32" cy="38.5" r="2.1" fill="#34d399" />
                                    <!-- Mini Content Bars inside Module -->
                                    <rect x="-46" y="49" width="42" height="5.5" rx="2"
                                        fill="url(#maint-orange-grad)" />
                                    <rect x="-46" y="58" width="92" height="3.8" rx="1.9" fill="var(--scene-depth)" />
                                    <!-- Animated Live Upgrade Progress Bar -->
                                    <rect x="-46" y="65" width="92" height="5" rx="2.5" fill="var(--scene-surface-alt)"
                                        stroke="var(--scene-depth)" stroke-width="1" />
                                    <g transform="translate(-46, 65)">
                                        <rect class="anim-progress" x="0" y="0" width="92" height="5" rx="2.5"
                                            fill="#10b981" />
                                    </g>
                                </g>
                            </g>
                        </g>
                    </g>
                </g>

                <!-- CENTER BUILDING UNDER CONSTRUCTION (3D Depth) -->
                <g id="building-structure">
                    <!-- 3D Right Side Wall -->
                    <polygon points="310,148 324,140 324,198 310,198" fill="var(--scene-depth)"
                        stroke="var(--scene-ink)" stroke-width="1.7" stroke-linejoin="round" />
                    <!-- 3D Roof Deck -->
                    <polygon points="196,148 212,140 324,140 310,148" fill="var(--scene-surface-alt)"
                        stroke="var(--scene-ink)" stroke-width="1.7" stroke-linejoin="round" />
                    <!-- Main Building Front Facade -->
                    <rect x="196" y="148" width="114" height="50" fill="url(#maint-building-front)"
                        stroke="var(--scene-ink)" stroke-width="1.8" />

                    <!-- Steel Corner Column Frame Being Fastened by Worker 2 -->
                    <rect x="195" y="128" width="5" height="20" rx="1" fill="var(--scene-steel)"
                        stroke="var(--scene-ink)" stroke-width="1.3" />
                    <!-- Roof Connector Pins / Steel Rebar Waiting for Crane Module -->
                    <path d="M214 144v-7M240 144v-7M266 144v-7M292 144v-7" stroke="#ea580c" stroke-width="2"
                        stroke-linecap="round" />

                    <!-- Windows & Entrance -->
                    <rect x="208" y="157" width="18" height="14" rx="2" fill="#e0f2fe" stroke="var(--scene-ink)"
                        stroke-width="1.4" />
                    <rect x="234" y="157" width="18" height="14" rx="2" fill="#fef3c7" stroke="var(--scene-ink)"
                        stroke-width="1.4" />
                    <rect x="260" y="157" width="18" height="14" rx="2" fill="#e0f2fe" stroke="var(--scene-ink)"
                        stroke-width="1.4" />
                    <rect x="286" y="157" width="14" height="14" rx="2" fill="#dcfce7" stroke="var(--scene-ink)"
                        stroke-width="1.4" />

                    <!-- Lower Floor Entrance & Trim -->
                    <rect x="242" y="177" width="24" height="21" rx="2" fill="url(#maint-orange-grad)"
                        stroke="var(--scene-ink)" stroke-width="1.5" />
                    <circle cx="261" cy="188" r="1.5" fill="#ffffff" />
                    <rect x="208" y="178" width="24" height="11" rx="1.5" fill="var(--scene-surface-alt)"
                        stroke="var(--scene-ink)" stroke-width="1.3" />
                    <rect x="276" y="178" width="24" height="11" rx="1.5" fill="var(--scene-surface-alt)"
                        stroke="var(--scene-ink)" stroke-width="1.3" />

                    <!-- Metal Scaffolding (Left of Building) -->
                    <g id="scaffolding">
                        <line x1="148" y1="126" x2="148" y2="198" stroke="var(--scene-steel)" stroke-width="2.4"
                            stroke-linecap="round" />
                        <line x1="191" y1="126" x2="191" y2="198" stroke="var(--scene-steel)" stroke-width="2.4"
                            stroke-linecap="round" />
                        <!-- Cross Bracing -->
                        <line x1="148" y1="158" x2="191" y2="194" stroke="var(--scene-steel)" stroke-width="1.5" />
                        <line x1="191" y1="158" x2="148" y2="194" stroke="var(--scene-steel)" stroke-width="1.5" />
                        <!-- Safety Guard Rail -->
                        <line x1="145" y1="133" x2="194" y2="133" stroke="var(--scene-steel)" stroke-width="1.8"
                            stroke-linecap="round" />
                        <!-- Wooden Plank Platform -->
                        <rect x="142" y="153" width="54" height="5.5" rx="1.5" fill="#d97706" stroke="var(--scene-ink)"
                            stroke-width="1.4" />
                    </g>
                </g>

                <!-- WORKER 2: Active Builder on Scaffolding Hammering Building Frame -->
                <g id="worker-builder">
                    <!-- Legs & Boots (standing on plank y=153) -->
                    <rect x="161" y="137" width="6.5" height="14" rx="2" fill="#334155" stroke="var(--scene-ink)"
                        stroke-width="1.3" />
                    <rect x="170" y="137" width="6.5" height="14" rx="2" fill="#1e293b" stroke="var(--scene-ink)"
                        stroke-width="1.3" />
                    <rect x="160" y="149" width="8.5" height="4" rx="1.5" fill="#0f172a" />
                    <rect x="170" y="149" width="9" height="4" rx="1.5" fill="#0f172a" />
                    <!-- Torso with High-Vis Orange Safety Vest -->
                    <rect x="159" y="119" width="19" height="20" rx="4.5" fill="url(#maint-orange-grad)"
                        stroke="var(--scene-ink)" stroke-width="1.5" />
                    <!-- Reflective Vest Stripes -->
                    <line x1="160" y1="127" x2="177" y2="127" stroke="#fef3c7" stroke-width="2" />
                    <line x1="160" y1="133" x2="177" y2="133" stroke="#fef3c7" stroke-width="2" />
                    <!-- Head & Yellow Hard Hat -->
                    <circle cx="169" cy="111" r="6.5" fill="#fed7aa" stroke="var(--scene-ink)" stroke-width="1.4" />
                    <path d="M162 110c0-4.5 3.1-7.5 7-7.5s7 3 7 7.5Z" fill="url(#maint-amber-grad)"
                        stroke="var(--scene-ink)" stroke-width="1.4" />
                    <line x1="160.5" y1="110" x2="178.5" y2="110" stroke="var(--scene-ink)" stroke-width="1.8"
                        stroke-linecap="round" />

                    <!-- Animated Hammering Arm (pivots at shoulder 175, 124) -->
                    <g transform="translate(175, 124)">
                        <g class="anim-hammer">
                            <!-- Arm -->
                            <line x1="0" y1="0" x2="12" y2="4" stroke="#334155" stroke-width="4.5"
                                stroke-linecap="round" />
                            <circle cx="12.5" cy="4" r="2.6" fill="#fed7aa" stroke="var(--scene-ink)"
                                stroke-width="1.1" />
                            <!-- Wrench / Hammer Tool -->
                            <line x1="12.5" y1="4" x2="18" y2="-6" stroke="#92400e" stroke-width="2.4"
                                stroke-linecap="round" />
                            <rect x="15" y="-10" width="8.5" height="4.5" rx="1.2" transform="rotate(28 18 -7)"
                                fill="#64748b" stroke="var(--scene-ink)" stroke-width="1.2" />
                        </g>
                    </g>

                    <!-- Animated Impact Sparks on Structure Edge -->
                    <g transform="translate(197, 134)">
                        <g class="anim-spark">
                            <line x1="0" y1="0" x2="6" y2="-5" stroke="#f59e0b" stroke-width="2"
                                stroke-linecap="round" />
                            <line x1="1" y1="2" x2="8" y2="1" stroke="#ea580c" stroke-width="2"
                                stroke-linecap="round" />
                            <line x1="0" y1="4" x2="5" y2="8" stroke="#fbbf24" stroke-width="2"
                                stroke-linecap="round" />
                        </g>
                    </g>
                </g>

                <!-- WORKER 1: Site Supervisor Directing Crane (Left Ground) -->
                <g id="worker-supervisor">
                    <!-- Legs & Boots (ground y=198) -->
                    <rect x="77" y="177" width="7" height="18" rx="2.2" fill="#334155" stroke="var(--scene-ink)"
                        stroke-width="1.4" />
                    <rect x="87" y="177" width="7" height="18" rx="2.2" fill="#1e293b" stroke="var(--scene-ink)"
                        stroke-width="1.4" />
                    <rect x="75.5" y="193" width="9.5" height="4.5" rx="1.6" fill="#0f172a" />
                    <rect x="87" y="193" width="10" height="4.5" rx="1.6" fill="#0f172a" />

                    <!-- Left Arm Holding Rolled Blueprint -->
                    <path d="M76 161l-7 10 6 3" stroke="#1e293b" stroke-width="4.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <rect x="65" y="169" width="13" height="5" rx="2" transform="rotate(-18 71 171)" fill="#38bdf8"
                        stroke="var(--scene-ink)" stroke-width="1.2" />

                    <!-- Torso & High-Vis Safety Vest -->
                    <rect x="75" y="156" width="21" height="23" rx="5" fill="url(#maint-orange-grad)"
                        stroke="var(--scene-ink)" stroke-width="1.6" />
                    <line x1="76" y1="166" x2="95" y2="166" stroke="#fef3c7" stroke-width="2.2" />
                    <line x1="76" y1="172" x2="95" y2="172" stroke="#fef3c7" stroke-width="2.2" />

                    <!-- Animated Head Looking Up at Crane -->
                    <g transform="translate(86, 152)">
                        <g class="anim-nod">
                            <circle cx="0" cy="-5" r="7.5" fill="#fed7aa" stroke="var(--scene-ink)"
                                stroke-width="1.5" />
                            <circle cx="3" cy="-5.5" r="1.1" fill="#1e293b" />
                            <!-- Safety Hard Hat -->
                            <path d="M-8 -6c0-5 3.6-8.5 8-8.5s8 3.5 8 8.5Z" fill="url(#maint-amber-grad)"
                                stroke="var(--scene-ink)" stroke-width="1.5" />
                            <path d="M-2 -14.2v5" stroke="#fffbeb" stroke-width="1.6" stroke-linecap="round"
                                opacity="0.7" />
                            <line x1="-9.5" y1="-6" x2="10.5" y2="-6" stroke="var(--scene-ink)" stroke-width="2"
                                stroke-linecap="round" />
                        </g>
                    </g>

                    <!-- Animated Right Arm Signaling Crane -->
                    <g transform="translate(94, 162)">
                        <g class="anim-wave">
                            <line x1="0" y1="0" x2="15" y2="-6" stroke="#1e293b" stroke-width="4.5"
                                stroke-linecap="round" />
                            <circle cx="16" cy="-6.5" r="2.8" fill="#fed7aa" stroke="var(--scene-ink)"
                                stroke-width="1.2" />
                        </g>
                    </g>
                </g>

                <!-- WORKER 3: System Technician Inspecting Tablet (Right Ground) -->
                <g id="worker-tech">
                    <!-- Legs & Boots -->
                    <rect x="353" y="177" width="7" height="18" rx="2.2" fill="#1e293b" stroke="var(--scene-ink)"
                        stroke-width="1.4" />
                    <rect x="363" y="177" width="7" height="18" rx="2.2" fill="#334155" stroke="var(--scene-ink)"
                        stroke-width="1.4" />
                    <rect x="350.5" y="193" width="10" height="4.5" rx="1.6" fill="#0f172a" />
                    <rect x="362" y="193" width="9.5" height="4.5" rx="1.6" fill="#0f172a" />

                    <!-- Torso & Vest -->
                    <rect x="351" y="156" width="21" height="23" rx="5" fill="#0f172a" stroke="var(--scene-ink)"
                        stroke-width="1.6" />
                    <path d="M353 158h17v19h-17Z" fill="url(#maint-amber-grad)" opacity="0.92" />
                    <line x1="352" y1="167" x2="371" y2="167" stroke="#ffffff" stroke-width="2" />

                    <!-- Arm Holding Glowing Tablet -->
                    <path d="M353 163l-11 6" stroke="#1e293b" stroke-width="4.2" stroke-linecap="round" />
                    <rect x="334" y="162" width="11" height="14" rx="2" transform="rotate(-14 339 169)" fill="#38bdf8"
                        stroke="var(--scene-ink)" stroke-width="1.4" />
                    <!-- Pulsing Sync Indicator Above Tablet -->
                    <g class="anim-beacon">
                        <circle cx="337" cy="153" r="3" fill="#10b981" />
                    </g>

                    <!-- Head & Orange Hard Hat -->
                    <circle cx="361" cy="147" r="7.2" fill="#fed7aa" stroke="var(--scene-ink)" stroke-width="1.5" />
                    <circle cx="358" cy="146.5" r="1.1" fill="#1e293b" />
                    <path d="M353 146c0-4.8 3.6-8.2 8-8.2s8 3.4 8 8.2Z" fill="url(#maint-orange-grad)"
                        stroke="var(--scene-ink)" stroke-width="1.5" />
                    <line x1="350.5" y1="146" x2="370.5" y2="146" stroke="var(--scene-ink)" stroke-width="2"
                        stroke-linecap="round" />
                </g>

                <!-- SAFETY BARRICADE WITH BLINKING BEACON -->
                <g id="safety-barricade">
                    <!-- Beacon Glow -->
                    <circle class="anim-beacon" cx="124" cy="175" r="7.5" fill="url(#maint-beacon-glow)" />
                    <circle class="anim-beacon" cx="124" cy="175" r="2.8" fill="#f59e0b" stroke="var(--scene-ink)"
                        stroke-width="1.1" />
                    <!-- Barricade Legs -->
                    <line x1="115" y1="184" x2="113" y2="198" stroke="var(--scene-ink)" stroke-width="2"
                        stroke-linecap="round" />
                    <line x1="133" y1="184" x2="135" y2="198" stroke="var(--scene-ink)" stroke-width="2"
                        stroke-linecap="round" />
                    <!-- Striped Bar -->
                    <rect x="110" y="179" width="28" height="7" rx="1.5" fill="#fbbf24" stroke="var(--scene-ink)"
                        stroke-width="1.4" />
                    <path d="M116 179l-3 7M124 179l-3 7M132 179l-3 7" stroke="var(--scene-ink)" stroke-width="2.2" />
                </g>

                <!-- TRAFFIC CONES (Left & Right) -->
                <g id="cone-left" transform="translate(36, 178)">
                    <polygon points="9,0 16,18 2,18" fill="url(#maint-orange-grad)" stroke="var(--scene-ink)"
                        stroke-width="1.4" stroke-linejoin="round" />
                    <line x1="6" y1="9" x2="12" y2="9" stroke="#ffffff" stroke-width="2.6" />
                    <rect x="0" y="17.5" width="18" height="2.8" rx="1.2" fill="var(--scene-ink)" />
                </g>
                <g id="cone-right" transform="translate(466, 178)">
                    <polygon points="9,0 16,18 2,18" fill="url(#maint-orange-grad)" stroke="var(--scene-ink)"
                        stroke-width="1.4" stroke-linejoin="round" />
                    <line x1="6" y1="9" x2="12" y2="9" stroke="#ffffff" stroke-width="2.6" />
                    <rect x="0" y="17.5" width="18" height="2.8" rx="1.2" fill="var(--scene-ink)" />
                </g>

                <!-- Crisp Ground Baseline -->
                <line x1="18" y1="198.5" x2="502" y2="198.5" stroke="var(--scene-ink)" stroke-width="2"
                    stroke-linecap="round" />
                <line x1="175" y1="204.5" x2="345" y2="204.5" stroke="var(--scene-ground)" stroke-width="1.8"
                    stroke-linecap="round" />
            </svg>
        </div>

        <!-- Bottom Strip: Full-Width Horizontal Live Countdown Bar (Non-Grid) -->
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
                class="w-full rounded-2xl bg-white/90 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/90 dark:border-slate-800 px-5 sm:px-7 py-4 sm:py-5 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Left: Label & Target Time -->
                <div class="space-y-1.5 text-left">
                    <div
                        class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">

                        <span x-text="finished ? 'Estimasi Waktu Pemeliharaan Tercapai' : 'Estimasi Selesai'"></span>
                    </div>

                    <div
                        class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                        @if($formattedEndsAt)
                            <span class="inline-flex items-center gap-1.5 font-semibold text-slate-800 dark:text-slate-100">
                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
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

                <!-- Right: Inline Flex Tabular Countdown -->
                <div x-show="!finished" class="flex items-center justify-between sm:justify-start gap-2 sm:gap-3 shrink-0">
                    <div
                        class="min-w-[68px] sm:min-w-[80px] rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/90 dark:border-slate-700/80 py-2.5 px-3 text-center">
                        <div class="text-2xl sm:text-3xl font-extrabold tabular-nums tracking-tight text-slate-900 dark:text-white leading-none"
                            x-text="days">00</div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-1">
                            Hari</div>
                    </div>
                    <span class="text-lg sm:text-2xl font-bold text-slate-300 dark:text-slate-700 select-none">:</span>
                    <div
                        class="min-w-[68px] sm:min-w-[80px] rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/90 dark:border-slate-700/80 py-2.5 px-3 text-center">
                        <div class="text-2xl sm:text-3xl font-extrabold tabular-nums tracking-tight text-slate-900 dark:text-white leading-none"
                            x-text="hours">00</div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-1">
                            Jam</div>
                    </div>
                    <span class="text-lg sm:text-2xl font-bold text-slate-300 dark:text-slate-700 select-none">:</span>
                    <div
                        class="min-w-[68px] sm:min-w-[80px] rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/90 dark:border-slate-700/80 py-2.5 px-3 text-center">
                        <div class="text-2xl sm:text-3xl font-extrabold tabular-nums tracking-tight text-slate-900 dark:text-white leading-none"
                            x-text="minutes">00</div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-1">
                            Menit</div>
                    </div>
                    <span class="text-lg sm:text-2xl font-bold text-slate-300 dark:text-slate-700 select-none">:</span>
                    <div
                        class="min-w-[68px] sm:min-w-[80px] rounded-xl bg-orange-50/80 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-800/70 py-2.5 px-3 text-center">
                        <div class="text-2xl sm:text-3xl font-extrabold tabular-nums tracking-tight text-orange-600 dark:text-orange-400 leading-none"
                            x-text="seconds">00</div>
                        <div
                            class="text-[10px] font-bold uppercase tracking-wider text-orange-700/80 dark:text-orange-400/80 mt-1">
                            Detik</div>
                    </div>
                </div>

                <!-- Finished State -->
                <div x-show="finished" x-cloak
                    class="rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 px-4 py-3 text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Estimasi waktu telah tercapai. Klik <strong>Cek Status Sekarang</strong> untuk memeriksa
                        ketersediaan sistem.</span>
                </div>
            </div>
        @elseif(!empty($config['estimate']))
            <div
                class="w-full rounded-2xl bg-white/90 dark:bg-slate-900/85 border border-slate-200/90 dark:border-slate-800 px-5 sm:px-7 py-4 flex items-center gap-3 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 shadow-2xs">
                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Estimasi selesai: {{ $config['estimate'] }}</span>
            </div>
        @endif
    </main>

    <!-- Full-Width Footer -->
    <footer
        class="relative z-10 w-full px-5 sm:px-8 lg:px-14 xl:px-20 py-4 sm:py-5 border-t border-slate-200/75 dark:border-slate-800/75 flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left shrink-0">
        @if(!empty($user))
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Masuk sebagai <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $user->name }}</span>
                <span class="text-slate-400 dark:text-slate-500">({{ ucfirst($user->role) }})</span>
            </p>
        @else
            <span></span>
        @endif
        <p class="text-xs text-slate-400 dark:text-slate-500">
            &copy; {{ date('Y') }} SIBIMA &middot; Sistem Informasi Bimbingan Mahasiswa &middot; FASILKOM UNSUB
        </p>
    </footer>
</body>

</html>
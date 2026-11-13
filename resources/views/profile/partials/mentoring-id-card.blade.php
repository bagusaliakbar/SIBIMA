@php
    $isStudent = $user->role === 'mahasiswa';
    $p1Count = isset($p1Sessions) ? $p1Sessions->count() : 0;
    $p2Count = isset($p2Sessions) ? $p2Sessions->count() : 0;
    $totalSessions = $p1Count + $p2Count;
    $qrPayload = $user->signature_token
        ? url('/verify-signature/' . $user->signature_token)
        : ($isStudent ? route('logbooks.index') : route('profile.edit'));
    $cardCode = 'SIBIMA-' . ($user->identifier ? preg_replace('/\s+/', '', $user->identifier) : str_pad($user->id, 4, '0', STR_PAD_LEFT));
@endphp

<style>
    @media print {
        body * {
            visibility: hidden !important;
        }
        #printable-mentoring-card-area,
        #printable-mentoring-card-area * {
            visibility: visible !important;
        }
        #printable-mentoring-card-area {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            padding: 16px !important;
            background: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        #printable-mentoring-card-area .print-always-show {
            display: block !important;
        }
        #printable-mentoring-card-area .print-grid-two {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 16px !important;
            max-width: 100% !important;
        }
    }
</style>

<section x-data="{ cardSide: 'front', showBoth: false }">
    <!-- 2-Tier Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 mb-6 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-start gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-slate-100 tracking-tight">
                    {{ $isStudent ? 'Kartu Bimbingan Digital' : 'Kartu Dosen Pembimbing' }}
                </h2>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                    {{ $isStudent
                        ? 'Kartu pengenal mahasiswa pada SIBIMA.'
                        : 'Kartu pengenal dosen pembimbing pada SIBIMA.' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($isStudent)
                <!-- Toggle Depan / Belakang / Keduanya -->
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button type="button"
                            @click="showBoth = false; cardSide = 'front'"
                            :class="!showBoth && cardSide === 'front' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                        Sisi Depan
                    </button>
                    <button type="button"
                            @click="showBoth = false; cardSide = 'back'"
                            :class="!showBoth && cardSide === 'back' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                        Sisi Belakang
                    </button>
                    <button type="button"
                            @click="showBoth = true"
                            :class="showBoth ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                            class="hidden xl:inline-flex px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                        Keduanya
                    </button>
                </div>
            @endif

            <button type="button"
                    onclick="window.print()"
                    class="h-9 inline-flex items-center gap-1.5 px-3.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-all shadow-2xs cursor-pointer">
                <svg class="w-4 h-4 text-slate-500 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Kartu</span>
            </button>

            @if($isStudent)
                <a href="{{ route('logbooks.index') }}"
                   class="h-9 inline-flex items-center gap-1.5 px-3.5 rounded-xl bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white text-xs font-bold transition-all shadow-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Logbook</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Showcase Kartu -->
    <div id="printable-mentoring-card-area" class="bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-4 sm:p-6">
        @if($isStudent)
            <div class="grid gap-6 items-stretch mx-auto print-grid-two"
                 :class="showBoth ? 'grid-cols-1 xl:grid-cols-2 max-w-6xl' : 'grid-cols-1 max-w-[540px]'">

                <!-- ==========================================
                     SISI DEPAN: KARTU PENGENAL BIMBINGAN
                     ========================================== -->
                <div x-show="showBoth || cardSide === 'front'"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-[0.98]"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="print-always-show relative overflow-hidden rounded-2xl border border-slate-300/90 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-md flex flex-col justify-between">

                    <!-- Subtle Geometric Pattern & Institutional Watermark -->
                    <div class="pointer-events-none absolute inset-0 opacity-[0.03] dark:opacity-[0.05]"
                         style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 16px 16px;"></div>
                    <img src="{{ asset('logo_unsub.png') }}" alt="" aria-hidden="true"
                         class="pointer-events-none absolute -right-8 -bottom-8 w-44 h-44 object-contain opacity-[0.045] dark:opacity-[0.06] select-none">

                    <!-- Institutional Top Band -->
                    <div class="relative z-10 bg-slate-900 dark:bg-slate-950 text-white px-5 py-3.5 border-b-[3px] border-orange-500 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-9 h-9 object-contain shrink-0">
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold tracking-[0.22em] text-orange-400 uppercase truncate">Universitas Subang</p>
                                <p class="text-xs sm:text-sm font-extrabold tracking-wide text-white truncate">Fakultas Ilmu Komputer</p>
                            </div>
                        </div>
                        <div class="shrink-0 pl-3 border-l border-slate-700/80 text-right">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/[0.06] border border-white/15">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-400 shrink-0"></span>
                                <span class="text-[9px] font-bold uppercase tracking-[0.14em] text-slate-100">Kartu Bimbingan</span>
                            </div>
                            <p class="mt-1.5 text-[9px] font-mono tracking-wider text-slate-400">
                                <span class="text-slate-500 uppercase mr-1">No.</span>{{ $cardCode }}
                            </p>
                        </div>
                    </div>

                    <!-- Card Main Body -->
                    <div class="relative z-10 p-5 space-y-4 flex-1">
                        <!-- Top Identity Section -->
                        <div class="flex items-start gap-4">
                            <!-- Pas Foto + Smart Chip Badge -->
                            <div class="shrink-0 space-y-2">
                                <div class="relative w-24 h-28 sm:w-[104px] sm:h-[124px] rounded-xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shadow-xs">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    <!-- Subtle corner smart-chip emblem -->
                                    <div class="absolute bottom-1.5 right-1.5 w-6 h-4 rounded bg-amber-300/90 border border-amber-500/70 shadow-2xs flex items-center justify-center">
                                        <div class="w-3.5 h-2.5 border border-amber-700/50 rounded-[2px]"></div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <span class="inline-flex items-center justify-center w-full px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                        {{ $user->current_semester ? 'Semester ' . $user->current_semester : 'Mahasiswa Aktif' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Identity Metadata -->
                            <div class="flex-1 min-w-0 space-y-2.5">
                                <div>
                                    <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Nama Mahasiswa</p>
                                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-slate-100 leading-snug break-words">
                                        {{ $user->name }}
                                    </h3>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div class="px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/70 border border-slate-200/80 dark:border-slate-700/80">
                                        <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">NPM</span>
                                        <span class="block text-xs font-mono font-bold text-orange-600 dark:text-orange-400 truncate">
                                            {{ $user->identifier ?: 'Belum Diatur' }}
                                        </span>
                                    </div>
                                    <div class="px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/70 border border-slate-200/80 dark:border-slate-700/80">
                                        <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Angkatan</span>
                                        <span class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                                            {{ $user->entry_year ?: '-' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Academic Stage Pill -->
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @if($academicStage)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[10px] font-bold {{ $academicStage['badge_class'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $academicStage['dot_class'] }}"></span>
                                            <span>{{ $academicStage['label'] }}</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[10px] font-bold bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Belum Mengajukan Skripsi</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Pembimbing 1 & 2 Compact Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-3 border-t border-slate-200/80 dark:border-slate-800">
                            <div class="p-2.5 rounded-xl bg-slate-50/90 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Pembimbing 1</span>
                                    <span class="text-[10px] font-mono font-bold text-orange-600 dark:text-orange-400">{{ min($p1Count, 8) }}/8</span>
                                </div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" title="{{ $thesis?->pembimbing1?->name }}">
                                    {{ $thesis?->pembimbing1?->name ?? 'Belum ditentukan' }}
                                </p>
                                @if($thesis)
                                    <div class="flex items-center gap-1.5 mt-1.5 text-[9px] font-bold">
                                        <span class="px-1.5 py-0.5 rounded {{ $thesis->acc_up_p1 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60' : 'bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                            UP: {{ $thesis->acc_up_p1 ? 'ACC' : 'Proses' }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded {{ $thesis->acc_sidang_p1 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60' : 'bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                            Sidang: {{ $thesis->acc_sidang_p1 ? 'ACC' : 'Proses' }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-50/90 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Pembimbing 2</span>
                                    <span class="text-[10px] font-mono font-bold text-orange-600 dark:text-orange-400">{{ min($p2Count, 8) }}/8</span>
                                </div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" title="{{ $thesis?->pembimbing2?->name }}">
                                    {{ $thesis?->pembimbing2?->name ?? 'Belum ditentukan' }}
                                </p>
                                @if($thesis)
                                    <div class="flex items-center gap-1.5 mt-1.5 text-[9px] font-bold">
                                        <span class="px-1.5 py-0.5 rounded {{ $thesis->acc_up_p2 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60' : 'bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                            UP: {{ $thesis->acc_up_p2 ? 'ACC' : 'Proses' }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded {{ $thesis->acc_sidang_p2 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60' : 'bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                            Sidang: {{ $thesis->acc_sidang_p2 ? 'ACC' : 'Proses' }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Bottom Strip + QR -->
                        <div class="flex items-center justify-between gap-3 pt-3 border-t border-dashed border-slate-200 dark:border-slate-800">
                            <div class="space-y-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5 text-[10px] font-bold text-slate-700 dark:text-slate-200">
                                    <span>Total Kehadiran Bimbingan:</span>
                                    <span class="px-1.5 py-0.5 rounded bg-orange-50 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60 font-mono">
                                        {{ $totalSessions }} / 16 Sesi
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">
                                    SIBIMA · Sistem Informasi Bimbingan Tugas Akhir
                                </p>
                            </div>
                            <div class="p-1.5 bg-white rounded-lg border border-slate-200 shadow-2xs shrink-0" title="QR Verifikasi Kartu Bimbingan">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(48)->margin(0)->generate($qrPayload) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==========================================
                     SISI BELAKANG: KARTU KENDALI & JUDUL SKRIPSI
                     ========================================== -->
                <div x-show="showBoth || cardSide === 'back'"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-[0.98]"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="print-always-show relative overflow-hidden rounded-2xl border border-slate-300/90 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-md flex flex-col justify-between">

                    <!-- Top Magnetic/Header Strip -->
                    <div class="bg-slate-900 dark:bg-slate-950 text-white px-5 py-3 border-b-[3px] border-orange-500 flex items-center justify-between gap-3">
                        <span class="text-[10px] font-black uppercase tracking-widest text-orange-400">Kartu Kendali Bimbingan Skripsi</span>
                        <span class="text-[10px] font-mono text-slate-300 truncate">{{ $user->identifier ?: $cardCode }}</span>
                    </div>

                    <div class="p-5 space-y-4 flex-1">
                        <!-- Judul Skripsi -->
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Judul Skripsi</span>
                                @if($thesis?->topic)
                                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 text-[9px] font-bold truncate max-w-[180px]">
                                        {{ $thesis->topic }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-slate-900 dark:text-slate-100 leading-relaxed line-clamp-3">
                                {{ $thesis?->display_title ?: 'Judul skripsi belum terdaftar pada sistem.' }}
                            </p>
                        </div>

                        <!-- Matriks Kendali P1 (Slot 1-8) -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between gap-2 text-[10px]">
                                <span class="font-bold text-slate-700 dark:text-slate-200 truncate">
                                    P1 · {{ $thesis?->pembimbing1?->name ?? 'Belum ditentukan' }}
                                </span>
                                <span class="font-mono font-bold text-slate-500 dark:text-slate-400 shrink-0">{{ $p1Count }} Sesi</span>
                            </div>
                            <div class="grid grid-cols-4 sm:grid-cols-8 gap-1.5">
                                @for($slot = 0; $slot < 8; $slot++)
                                    @php $sess = $p1Sessions->get($slot); @endphp
                                    @if($sess)
                                        <div class="h-11 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300/90 dark:border-emerald-800/70 flex flex-col items-center justify-center px-1 text-center"
                                             title="Sesi #{{ $slot + 1 }}: {{ $sess->topic }} ({{ $sess->scheduled_at->format('d M Y') }})">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-[8px] font-mono font-bold text-emerald-800 dark:text-emerald-300 mt-0.5">{{ $sess->scheduled_at->format('d/m') }}</span>
                                        </div>
                                    @else
                                        <div class="h-11 rounded-lg bg-slate-50/70 dark:bg-slate-800/30 border border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                            <span class="text-[10px] font-mono font-bold">#{{ $slot + 1 }}</span>
                                            <span class="text-[8px]">{{ $slot < 4 ? 'UP' : 'Sidang' }}</span>
                                        </div>
                                    @endif
                                @endfor
                            </div>
                        </div>

                        <!-- Matriks Kendali P2 (Slot 1-8) -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between gap-2 text-[10px]">
                                <span class="font-bold text-slate-700 dark:text-slate-200 truncate">
                                    P2 · {{ $thesis?->pembimbing2?->name ?? 'Belum ditentukan' }}
                                </span>
                                <span class="font-mono font-bold text-slate-500 dark:text-slate-400 shrink-0">{{ $p2Count }} Sesi</span>
                            </div>
                            <div class="grid grid-cols-4 sm:grid-cols-8 gap-1.5">
                                @for($slot = 0; $slot < 8; $slot++)
                                    @php $sess = $p2Sessions->get($slot); @endphp
                                    @if($sess)
                                        <div class="h-11 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300/90 dark:border-emerald-800/70 flex flex-col items-center justify-center px-1 text-center"
                                             title="Sesi #{{ $slot + 1 }}: {{ $sess->topic }} ({{ $sess->scheduled_at->format('d M Y') }})">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-[8px] font-mono font-bold text-emerald-800 dark:text-emerald-300 mt-0.5">{{ $sess->scheduled_at->format('d/m') }}</span>
                                        </div>
                                    @else
                                        <div class="h-11 rounded-lg bg-slate-50/70 dark:bg-slate-800/30 border border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                            <span class="text-[10px] font-mono font-bold">#{{ $slot + 1 }}</span>
                                            <span class="text-[8px]">{{ $slot < 4 ? 'UP' : 'Sidang' }}</span>
                                        </div>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Card Back Footer -->
                    <div class="px-5 py-3 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2 text-[10px] text-slate-500 dark:text-slate-400">
                        <span>Syarat minimal: 4x/Dosen (Seminar UP) &amp; 8x/Dosen (Sidang)</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Tervalidasi Logbook SIBIMA</span>
                    </div>
                </div>
            </div>
        @else
            <!-- ==========================================
                 KARTU IDENTITAS AKADEMIK (DOSEN / KAPRODI / ADMIN)
                 ========================================== -->
            @php
                $roleLabel = match($user->role) {
                    'kaprodi' => 'Ketua Program Studi',
                    'admin' => 'Administrator Akademik',
                    default => 'Dosen Pembimbing',
                };
                $interests = collect(explode(',', (string) $user->research_interests))
                    ->map(fn($item) => trim($item))
                    ->filter()
                    ->take(4);
            @endphp
            <div class="max-w-[540px] mx-auto">
                <div class="relative overflow-hidden rounded-2xl border border-slate-300/90 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-md flex flex-col justify-between">
                    <div class="pointer-events-none absolute inset-0 opacity-[0.03] dark:opacity-[0.05]"
                         style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 16px 16px;"></div>
                    <img src="{{ asset('logo_unsub.png') }}" alt="" aria-hidden="true"
                         class="pointer-events-none absolute -right-8 -bottom-8 w-44 h-44 object-contain opacity-[0.045] dark:opacity-[0.06] select-none">

                    <!-- Institutional Top Band -->
                    <div class="relative z-10 bg-slate-900 dark:bg-slate-950 text-white px-5 py-3.5 border-b-[3px] border-orange-500 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-9 h-9 object-contain shrink-0">
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold tracking-[0.22em] text-orange-400 uppercase truncate">Universitas Subang</p>
                                <p class="text-xs sm:text-sm font-extrabold tracking-wide text-white truncate">Fakultas Ilmu Komputer</p>
                            </div>
                        </div>
                        <div class="shrink-0 pl-3 border-l border-slate-700/80 text-right">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/[0.06] border border-white/15">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-400 shrink-0"></span>
                                <span class="text-[9px] font-bold uppercase tracking-[0.14em] text-slate-100">{{ $roleLabel }}</span>
                            </div>
                            <p class="mt-1.5 text-[9px] font-mono tracking-wider text-slate-400">
                                <span class="text-slate-500 uppercase mr-1">No Kartu.</span>{{ $cardCode }}
                            </p>
                        </div>
                    </div>

                    <div class="relative z-10 p-5 space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0">
                                <div class="relative w-24 h-28 sm:w-[104px] sm:h-[124px] rounded-xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shadow-xs">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                </div>
                            </div>

                            <div class="flex-1 min-w-0 flex flex-col justify-between space-y-2.5">
                                <div>
                                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-slate-100 leading-snug break-words">
                                        {{ $user->name }}
                                    </h3>
                                </div>

                                <div class="space-y-1.5 text-xs">
                                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                        <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                                        </svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">NIDN/NIP</span>
                                        <span class="font-mono font-bold text-orange-600 dark:text-orange-400 truncate">
                                            {{ $user->identifier ?: 'Belum Diatur' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                        <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs font-medium text-slate-600 dark:text-slate-300 truncate">{{ $user->email }}</span>
                                    </div>
                                </div>

                                @if($interests->isNotEmpty())
                                    <div class="flex flex-wrap gap-1.5 pt-0.5">
                                        @foreach($interests as $tag)
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100/90 dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700 text-[10px] font-semibold text-slate-700 dark:text-slate-300">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($dosenStats)
                            <div class="pt-3 border-t border-slate-200/80 dark:border-slate-800">
                                <div class="grid grid-cols-2 divide-x divide-slate-200/80 dark:divide-slate-700/80 rounded-xl bg-slate-50/90 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 overflow-hidden">
                                    <div class="px-3.5 py-2.5">
                                        <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 truncate">Pembimbing 1</span>
                                        <div class="mt-0.5 flex items-baseline gap-1">
                                            <span class="text-sm font-extrabold font-mono text-slate-900 dark:text-slate-100">{{ $dosenStats['active_p1'] }}</span>
                                            <span class="text-[10px] font-medium text-slate-500 dark:text-slate-400">Mahasiswa</span>
                                        </div>
                                    </div>
                                    <div class="px-3.5 py-2.5">
                                        <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 truncate">Pembimbing 2</span>
                                        <div class="mt-0.5 flex items-baseline gap-1">
                                            <span class="text-sm font-extrabold font-mono text-slate-900 dark:text-slate-100">{{ $dosenStats['active_p2'] }}</span>
                                            <span class="text-[10px] font-medium text-slate-500 dark:text-slate-400">Mahasiswa</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-3 pt-3 border-t border-dashed border-slate-200 dark:border-slate-800">
                            <div class="min-w-0 space-y-0.5">
                                <p class="text-[11px] font-bold text-slate-800 dark:text-slate-200 truncate">Sistem Informasi Bimbingan Mahasiswa</p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Kartu Dosen Pembimbing</p>
                            </div>
                            <div class="p-1.5 bg-white rounded-lg border border-slate-200 shadow-2xs shrink-0" title="QR Verifikasi Identitas">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(48)->margin(0)->generate($qrPayload) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>


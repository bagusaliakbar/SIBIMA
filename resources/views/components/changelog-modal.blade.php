@php
    $currentVersion = config('changelog.current_version', 'v2.5.0');
    $releases = config('changelog.releases', []);
@endphp

<div x-data="{ open: false }"
     @open-changelog-modal.window="open = true"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[99999] overflow-y-auto print:hidden"
     style="display: none;"
     role="dialog"
     aria-modal="true"
     aria-labelledby="changelog-modal-title">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs transition-opacity"></div>

    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 pointer-events-auto">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-orange-50 dark:bg-orange-950/50 border border-orange-200/80 dark:border-orange-800/60 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 id="changelog-modal-title" class="text-base font-bold text-slate-900 dark:text-slate-100 tracking-tight">
                                Catatan Versi SIBIMA
                            </h3>
                            <span class="px-2 py-0.5 rounded-full bg-orange-50 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60 text-[10px] font-mono font-bold">
                                {{ $currentVersion }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                            Riwayat pembaruan fitur, peningkatan antarmuka, dan perbaikan sistem.
                        </p>
                    </div>
                </div>

                <button type="button"
                        @click="open = false"
                        class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer shrink-0"
                        aria-label="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Release Timeline Body -->
            <div class="p-6 max-h-[68vh] overflow-y-auto space-y-5 custom-scrollbar">
                @foreach($releases as $release)
                    @php
                        $badgeColors = match($release['badge_type'] ?? 'slate') {
                            'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
                            'blue' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60',
                            'indigo' => 'bg-indigo-50 text-indigo-800 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/60',
                            'amber' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
                            default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                        };
                    @endphp
                    <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2 pb-2.5 border-b border-slate-200/70 dark:border-slate-700/70">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-bold text-slate-900 dark:text-slate-100 px-2 py-0.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                                    {{ $release['version'] }}
                                </span>
                                @if(!empty($release['badge']))
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeColors }}">
                                        {{ $release['badge'] }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                {{ $release['date'] }}
                            </span>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-2">
                                {{ $release['title'] }}
                            </h4>
                            <ul class="space-y-1.5">
                                @foreach($release['items'] as $item)
                                    <li class="flex items-start gap-2 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                        <svg class="w-3.5 h-3.5 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer -->
            <div class="px-6 py-3.5 bg-slate-50/70 dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3 text-[11px] text-slate-500 dark:text-slate-400">
                <span>SIBIMA FASILKOM UNSUB</span>
                <button type="button"
                        @click="open = false"
                        class="px-4 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

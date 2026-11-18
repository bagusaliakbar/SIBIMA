@php
    $currentVersion = config('changelog.current_version', 'v2.5.0');
    $releases = config('changelog.releases', []);
@endphp

<div x-data="{
        expandedVersions: ['{{ $currentVersion }}'],
        activeFilter: 'all',
        allReleases: @js(collect($releases)->map(fn($r) => ['version' => $r['version'], 'badge_type' => $r['badge_type'] ?? 'slate'])->values()->all()),
        get allVersions() {
            return this.allReleases.map(r => r.version);
        },
        toggleVersion(ver) {
            if (this.expandedVersions.includes(ver)) {
                this.expandedVersions = this.expandedVersions.filter(v => v !== ver);
            } else {
                this.expandedVersions.push(ver);
            }
        },
        isExpanded(ver) {
            return this.expandedVersions.includes(ver);
        },
        matchesFilter(badgeType) {
            if (this.activeFilter === 'all') return true;
            if (this.activeFilter === 'feature') return ['emerald', 'blue'].includes(badgeType);
            if (this.activeFilter === 'ux') return badgeType === 'indigo';
            if (this.activeFilter === 'perf') return ['amber', 'slate'].includes(badgeType);
            return true;
        },
        selectFilter(filter) {
            this.activeFilter = filter;
            if (filter === 'all') {
                this.expandedVersions = ['{{ $currentVersion }}'];
            } else {
                this.expandedVersions = this.allReleases
                    .filter(r => this.matchesFilter(r.badge_type))
                    .map(r => r.version);
            }
        },
        toggleAll() {
            if (this.expandedVersions.length === this.allVersions.length) {
                this.expandedVersions = ['{{ $currentVersion }}'];
            } else {
                this.expandedVersions = [...this.allVersions];
            }
        }
     }"
     @open-changelog-modal.window="$dispatch('open-modal', 'changelog-modal')">

    <x-modal name="changelog-modal" maxWidth="2xl" focusable>
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900 flex items-center justify-between gap-4">
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
                        Riwayat {{ count($releases) }} rilis resmi dari v1.0.0 hingga {{ $currentVersion }}.
                    </p>
                </div>
            </div>

            <button type="button"
                    @click="$dispatch('close-modal', 'changelog-modal')"
                    class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer shrink-0"
                    aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Toolbar Filter & Expand All -->
        <div class="px-5 sm:px-6 py-2.5 bg-slate-50/40 dark:bg-slate-950/40 border-b border-slate-200/70 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 overflow-x-auto">
                <button type="button" @click="selectFilter('all')"
                        :class="activeFilter === 'all' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'"
                        class="px-2.5 py-1 rounded-lg border text-[11px] font-bold transition-colors cursor-pointer shrink-0">
                    Semua ({{ count($releases) }})
                </button>
                <button type="button" @click="selectFilter('feature')"
                        :class="activeFilter === 'feature' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'"
                        class="px-2.5 py-1 rounded-lg border text-[11px] font-bold transition-colors cursor-pointer shrink-0">
                    Fitur Utama
                </button>
                <button type="button" @click="selectFilter('ux')"
                        :class="activeFilter === 'ux' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'"
                        class="px-2.5 py-1 rounded-lg border text-[11px] font-bold transition-colors cursor-pointer shrink-0">
                    UI/UX &amp; Revisi
                </button>
                <button type="button" @click="selectFilter('perf')"
                        :class="activeFilter === 'perf' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'"
                        class="px-2.5 py-1 rounded-lg border text-[11px] font-bold transition-colors cursor-pointer shrink-0">
                    Performa &amp; Fondasi
                </button>
            </div>

            <button type="button"
                    @click="toggleAll()"
                    class="text-[11px] font-bold text-slate-500 hover:text-orange-600 dark:text-slate-400 dark:hover:text-orange-400 transition-colors cursor-pointer">
                <span x-text="expandedVersions.length === allVersions.length ? 'Ringkas Rilis Lama' : 'Buka Semua'"></span>
            </button>
        </div>

        <!-- Release Timeline Body -->
        <div class="p-5 sm:p-6 space-y-3 max-h-[62vh] overflow-y-auto custom-scrollbar">
            @foreach($releases as $release)
                @php
                    $ver = $release['version'];
                    $badgeType = $release['badge_type'] ?? 'slate';
                    $badgeColors = match($badgeType) {
                        'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
                        'blue' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60',
                        'indigo' => 'bg-indigo-50 text-indigo-800 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/60',
                        'amber' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
                        default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                    };
                @endphp
                <div x-show="matchesFilter('{{ $badgeType }}')"
                     class="rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80 overflow-hidden transition-colors">

                    <!-- Clickable Accordion Header -->
                    <button type="button"
                            @click="toggleVersion('{{ $ver }}')"
                            :aria-expanded="isExpanded('{{ $ver }}')"
                            class="w-full px-4 py-3.5 flex items-center justify-between gap-3 text-left hover:bg-slate-100/70 dark:hover:bg-slate-800/70 transition-colors cursor-pointer">
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-mono font-bold text-slate-900 dark:text-slate-100 px-2 py-0.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                                    {{ $ver }}
                                </span>
                                @if(!empty($release['badge']))
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeColors }}">
                                        {{ $release['badge'] }}
                                    </span>
                                @endif
                                <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                    {{ $release['date'] }}
                                </span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                                {{ $release['title'] }}
                            </h4>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 hidden sm:inline">
                                {{ count($release['items']) }} item
                            </span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                 :class="isExpanded('{{ $ver }}') ? 'rotate-180 text-orange-500' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Collapsible Release Items -->
                    <div x-show="isExpanded('{{ $ver }}')"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="px-4 pb-4 pt-2 border-t border-slate-200/70 dark:border-slate-700/70 bg-white/60 dark:bg-slate-900/40">
                        <ul class="space-y-2">
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
        <div class="px-5 sm:px-6 py-3.5 bg-slate-50/70 dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3 text-[11px] text-slate-500 dark:text-slate-400">
            <span>SIBIMA FASILKOM UNSUB</span>
            <button type="button"
                    @click="$dispatch('close-modal', 'changelog-modal')"
                    class="h-9 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-colors cursor-pointer">
                Tutup
            </button>
        </div>
    </x-modal>
</div>

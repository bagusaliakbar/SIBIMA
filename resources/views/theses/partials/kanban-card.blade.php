@php
    $accentBorderClass = [
        'slate' => 'border-l-slate-400 dark:border-l-slate-500',
        'blue' => 'border-l-blue-500',
        'amber' => 'border-l-amber-500',
        'orange' => 'border-l-orange-500',
        'purple' => 'border-l-orange-500',
        'emerald' => 'border-l-emerald-500',
    ][$accent] ?? 'border-l-slate-400 dark:border-l-slate-500';

    $simScore = method_exists($thesis, 'getMaxSimilarityScore') ? $thesis->getMaxSimilarityScore() : 0;
@endphp

<div class="w-full min-w-0 box-border overflow-hidden p-4 bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 border-l-4 {{ $accentBorderClass }} hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all cursor-pointer group"
     role="link"
     tabindex="0"
     onclick="window.location='{{ route('theses.show', $thesis->id) }}'"
     onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.location='{{ route('theses.show', $thesis->id) }}'; }"
     x-show="!search || '{{ strtolower(addslashes($thesis->student->name ?? '')) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($thesis->student->identifier ?? '')) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($thesis->final_title ?? $thesis->title ?? '')) }}'.includes(search.toLowerCase())">

    <!-- Student Header -->
    <div class="flex items-center justify-between gap-3 w-full min-w-0 mb-3">
        <div class="flex items-center gap-2.5 flex-1 min-w-0 overflow-hidden">
            <div class="w-8 h-8 rounded-full overflow-hidden bg-orange-50 dark:bg-orange-950/60 border border-orange-100 dark:border-orange-900/60 flex items-center justify-center shrink-0 font-black text-orange-600 dark:text-orange-400 text-xs">
                @if($thesis->student && $thesis->student->avatar_url)
                    <img src="{{ $thesis->student->avatar_url }}" alt="Foto {{ $thesis->student->name ?? 'Mahasiswa' }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($thesis->student->name ?? 'M', 0, 2)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0 overflow-hidden">
                <h4 class="m-0 text-xs font-extrabold uppercase truncate text-slate-800 dark:text-slate-100 leading-snug group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                    {{ $thesis->student->name ?? 'Mahasiswa' }}
                </h4>
                <div class="flex items-center gap-1.5 mt-0.5 min-w-0 overflow-hidden">
                    <span class="text-[11px] font-mono font-semibold truncate text-slate-500 dark:text-slate-400">
                        {{ $thesis->student->identifier ?? 'NPM -' }}
                    </span>
                    @if($thesis->student && $thesis->student->entry_year)
                        <span class="text-[11px] font-bold whitespace-nowrap text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded shrink-0">
                            '{{ substr($thesis->student->entry_year, -2) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($thesis->is_migrated)
            <span class="shrink-0 px-1.5 py-0.5 text-[11px] font-black tracking-wider uppercase text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-900/50 rounded-md">
                Migrasi
            </span>
        @endif
    </div>

    <!-- Title Section -->
    <div class="w-full min-w-0 overflow-hidden mt-2.5 mb-3">
        <h5 class="m-0 text-[11px] font-semibold leading-relaxed line-clamp-2 break-words text-slate-700 dark:text-slate-200 uppercase" title="{{ $thesis->final_title ?? $thesis->title }}">
            {{ $thesis->final_title ?? $thesis->title }}
        </h5>

        @if($simScore > 0)
            <div class="mt-2 flex items-center">
                @if($simScore >= 60)
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/60 dark:border-rose-900/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse shrink-0"></span>
                        {{ $simScore }}% SANGAT MIRIP
                    </span>
                @elseif($simScore >= 40)
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/60 dark:border-amber-900/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                        {{ $simScore }}% MIRIP
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-900/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                        {{ $simScore }}% UNIK
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Supervisors Section -->
    <div class="w-full min-w-0 pt-3 border-t border-slate-100 dark:border-slate-800">
        <!-- Pembimbing 1 -->
        <div class="flex items-center gap-2 w-full min-w-0 overflow-hidden mb-1.5">
            <span class="px-1.5 py-0.5 rounded text-[11px] font-black uppercase shrink-0 bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/80">P1</span>
            @if($thesis->pembimbing1)
                <span class="text-[11px] font-bold truncate text-slate-700 dark:text-slate-300 uppercase">{{ $thesis->pembimbing1->name }}</span>
            @else
                <span class="text-[11px] font-bold truncate text-amber-600 dark:text-amber-400 uppercase italic">Belum Ditentukan</span>
            @endif
        </div>

        <!-- Pembimbing 2 -->
        <div class="flex items-center gap-2 w-full min-w-0 overflow-hidden">
            <span class="px-1.5 py-0.5 rounded text-[11px] font-black uppercase shrink-0 bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 border border-purple-200 dark:border-purple-800/80">P2</span>
            @if($thesis->pembimbing2)
                <span class="text-[11px] font-bold truncate text-slate-700 dark:text-slate-300 uppercase">{{ $thesis->pembimbing2->name }}</span>
            @else
                <span class="text-[11px] font-bold truncate text-amber-600 dark:text-amber-400 uppercase italic">Belum Ditentukan</span>
            @endif
        </div>
    </div>
</div>

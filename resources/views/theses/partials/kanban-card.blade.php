@php
    $accentColor = [
        'slate' => '#94a3b8',
        'blue' => '#3b82f6',
        'amber' => '#f59e0b',
        'purple' => '#a855f7',
        'emerald' => '#10b981',
    ][$accent] ?? '#94a3b8';

    $simScore = method_exists($thesis, 'getMaxSimilarityScore') ? $thesis->getMaxSimilarityScore() : 0;
@endphp

<div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 hover:shadow-md transition-all cursor-pointer group" 
     style="border-left: 4px solid {{ $accentColor }} !important; width: 100% !important; box-sizing: border-box !important; min-width: 0 !important; overflow: hidden !important;"
     onclick="window.location='{{ route('theses.show', $thesis->id) }}'"
     x-show="!search || '{{ strtolower(addslashes($thesis->student->name ?? '')) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($thesis->student->identifier ?? '')) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($thesis->final_title ?? $thesis->title ?? '')) }}'.includes(search.toLowerCase())">
    
    <!-- Student Header -->
    <div class="flex items-center justify-between gap-2 mb-2" style="width: 100%; min-width: 0;">
        <div class="flex items-center gap-2 min-w-0" style="flex: 1; overflow: hidden;">
            <div class="w-7 h-7 rounded-full overflow-hidden bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center shrink-0 font-black text-indigo-600 dark:text-indigo-400 text-xs">
                @if($thesis->student && $thesis->student->avatar_url)
                    <img src="{{ $thesis->student->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($thesis->student->name ?? 'M', 0, 2)) }}
                @endif
            </div>
            <div style="flex: 1; min-width: 0; overflow: hidden;">
                <h4 style="font-size: 11px; font-weight: 800; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-slate-800 dark:text-slate-100 leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $thesis->student->name ?? 'Mahasiswa' }}
                </h4>
                <div class="flex items-center gap-1 mt-0.5" style="min-w-0; overflow: hidden;">
                    <span style="font-size: 9px; font-family: monospace; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-slate-400">
                        {{ $thesis->student->identifier ?? 'NPM -' }}
                    </span>
                    @if($thesis->student && $thesis->student->entry_year)
                        <span style="font-size: 8px; font-weight: 700; white-space: nowrap;" class="text-slate-400 bg-slate-100 dark:bg-slate-800 px-1 py-0.2 rounded shrink-0">
                            '{{ substr($thesis->student->entry_year, -2) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        @if($thesis->is_migrated)
            <span style="font-size: 8px; font-weight: 900; letter-spacing: 0.05em;" class="shrink-0 uppercase text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-900/50 px-1.5 py-0.5 rounded">
                Migrasi
            </span>
        @endif
    </div>

    <!-- Title Section -->
    <div class="my-2" style="width: 100%; min-width: 0; overflow: hidden;">
        <h5 style="font-size: 11px; font-weight: 600; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; word-break: break-word;" class="text-slate-700 dark:text-slate-300 uppercase" title="{{ $thesis->final_title ?? $thesis->title }}">
            {{ $thesis->final_title ?? $thesis->title }}
        </h5>
        
        @if($simScore > 0)
            <div class="mt-1.5 flex items-center">
                @if($simScore >= 60)
                    <span style="font-size: 8px; font-weight: 900;" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded uppercase tracking-wider bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200/60 dark:border-rose-900/40">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse shrink-0"></span>
                        {{ $simScore }}% SANGAT MIRIP
                    </span>
                @elseif($simScore >= 40)
                    <span style="font-size: 8px; font-weight: 900;" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded uppercase tracking-wider bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/60 dark:border-amber-900/40">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                        {{ $simScore }}% MIRIP
                    </span>
                @else
                    <span style="font-size: 8px; font-weight: 900;" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-900/40">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                        {{ $simScore }}% UNIK
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Supervisors Section -->
    <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 space-y-1" style="width: 100%; min-width: 0;">
        <!-- Pembimbing 1 -->
        <div class="flex items-center gap-1.5 text-[10px]" style="width: 100%; min-width: 0; overflow: hidden;">
            <span style="background-color: #e0e7ff; color: #3730a3; font-size: 8px; font-weight: 900; padding: 2px 5px;" class="rounded uppercase shrink-0">P1</span>
            @if($thesis->pembimbing1)
                <span style="font-size: 10px; font-weight: 700; uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-slate-700 dark:text-slate-300 uppercase">{{ $thesis->pembimbing1->name }}</span>
            @else
                <span style="font-size: 9px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-amber-600 dark:text-amber-400 uppercase italic">Belum Ditentukan</span>
            @endif
        </div>

        <!-- Pembimbing 2 -->
        <div class="flex items-center gap-1.5 text-[10px]" style="width: 100%; min-width: 0; overflow: hidden;">
            <span style="background-color: #f3e8ff; color: #6b21a8; font-size: 8px; font-weight: 900; padding: 2px 5px;" class="rounded uppercase shrink-0">P2</span>
            @if($thesis->pembimbing2)
                <span style="font-size: 10px; font-weight: 700; uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-slate-700 dark:text-slate-300 uppercase">{{ $thesis->pembimbing2->name }}</span>
            @else
                <span style="font-size: 9px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-amber-600 dark:text-amber-400 uppercase italic">Belum Ditentukan</span>
            @endif
        </div>
    </div>
</div>

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

<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all cursor-pointer group" 
     style="padding: 16px !important; border-left: 4px solid {{ $accentColor }} !important; width: 100% !important; box-sizing: border-box !important; min-width: 0 !important; overflow: hidden !important;"
     onclick="window.location='{{ route('theses.show', $thesis->id) }}'"
     x-show="!search || '{{ strtolower(addslashes($thesis->student->name ?? '')) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($thesis->student->identifier ?? '')) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($thesis->final_title ?? $thesis->title ?? '')) }}'.includes(search.toLowerCase())">
    
    <!-- Student Header -->
    <div class="flex items-center justify-between gap-3" style="width: 100%; min-width: 0; margin-bottom: 12px;">
        <div class="flex items-center gap-2.5 min-w-0" style="flex: 1; overflow: hidden;">
            <div class="w-8 h-8 rounded-full overflow-hidden bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-100 dark:border-indigo-900/60 flex items-center justify-center shrink-0 font-black text-indigo-600 dark:text-indigo-400 text-xs" style="width: 32px; height: 32px; flex-shrink: 0;">
                @if($thesis->student && $thesis->student->avatar_url)
                    <img src="{{ $thesis->student->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($thesis->student->name ?? 'M', 0, 2)) }}
                @endif
            </div>
            <div style="flex: 1; min-width: 0; overflow: hidden;">
                <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;" class="text-slate-800 dark:text-slate-100 leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $thesis->student->name ?? 'Mahasiswa' }}
                </h4>
                <div class="flex items-center gap-1.5" style="margin-top: 2px; min-width: 0; overflow: hidden;">
                    <span style="font-size: 10px; font-family: monospace; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-slate-400 dark:text-slate-500">
                        {{ $thesis->student->identifier ?? 'NPM -' }}
                    </span>
                    @if($thesis->student && $thesis->student->entry_year)
                        <span style="font-size: 9px; font-weight: 700; white-space: nowrap;" class="text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.2 rounded shrink-0">
                            '{{ substr($thesis->student->entry_year, -2) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        @if($thesis->is_migrated)
            <span style="font-size: 8px; font-weight: 900; letter-spacing: 0.05em; padding: 3px 6px;" class="shrink-0 uppercase text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-900/50 rounded-md">
                Migrasi
            </span>
        @endif
    </div>

    <!-- Title Section -->
    <div style="width: 100%; min-width: 0; overflow: hidden; margin-top: 10px; margin-bottom: 12px;">
        <h5 style="font-size: 11px; font-weight: 600; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; word-break: break-word; margin: 0;" class="text-slate-700 dark:text-slate-200 uppercase" title="{{ $thesis->final_title ?? $thesis->title }}">
            {{ $thesis->final_title ?? $thesis->title }}
        </h5>
        
        @if($simScore > 0)
            <div style="margin-top: 8px;" class="flex items-center">
                @if($simScore >= 60)
                    <span style="font-size: 9px; font-weight: 800; padding: 3px 8px;" class="inline-flex items-center gap-1.5 rounded-md uppercase tracking-wider bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/60 dark:border-rose-900/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse shrink-0"></span>
                        {{ $simScore }}% SANGAT MIRIP
                    </span>
                @elseif($simScore >= 40)
                    <span style="font-size: 9px; font-weight: 800; padding: 3px 8px;" class="inline-flex items-center gap-1.5 rounded-md uppercase tracking-wider bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/60 dark:border-amber-900/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                        {{ $simScore }}% MIRIP
                    </span>
                @else
                    <span style="font-size: 9px; font-weight: 800; padding: 3px 8px;" class="inline-flex items-center gap-1.5 rounded-md uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-900/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                        {{ $simScore }}% UNIK
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Supervisors Section -->
    <div style="width: 100%; min-width: 0; padding-top: 12px;" class="border-t border-slate-100 dark:border-slate-800">
        <!-- Pembimbing 1 -->
        <div class="flex items-center gap-2" style="width: 100%; min-width: 0; overflow: hidden; margin-bottom: 6px;">
            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase shrink-0 bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/80">P1</span>
            @if($thesis->pembimbing1)
                <span style="font-size: 10px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-slate-700 dark:text-slate-300 uppercase">{{ $thesis->pembimbing1->name }}</span>
            @else
                <span style="font-size: 10px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-amber-600 dark:text-amber-400 uppercase italic">Belum Ditentukan</span>
            @endif
        </div>

        <!-- Pembimbing 2 -->
        <div class="flex items-center gap-2" style="width: 100%; min-width: 0; overflow: hidden;">
            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase shrink-0 bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 border border-purple-200 dark:border-purple-800/80">P2</span>
            @if($thesis->pembimbing2)
                <span style="font-size: 10px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-slate-700 dark:text-slate-300 uppercase">{{ $thesis->pembimbing2->name }}</span>
            @else
                <span style="font-size: 10px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="text-amber-600 dark:text-amber-400 uppercase italic">Belum Ditentukan</span>
            @endif
        </div>
    </div>
</div>

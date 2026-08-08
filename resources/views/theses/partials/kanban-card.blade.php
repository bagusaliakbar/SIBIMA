@php
    $accentBorder = [
        'slate' => 'border-l-slate-400 dark:border-l-slate-500',
        'blue' => 'border-l-blue-500 dark:border-l-blue-400',
        'amber' => 'border-l-amber-500 dark:border-l-amber-400',
        'purple' => 'border-l-purple-500 dark:border-l-purple-400',
        'emerald' => 'border-l-emerald-500 dark:border-l-emerald-400',
    ][$accent] ?? 'border-l-slate-400';
    
    $simScore = method_exists($thesis, 'getMaxSimilarityScore') ? $thesis->getMaxSimilarityScore() : 0;
@endphp

<div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-200 cursor-pointer border-l-4 {{ $accentBorder }} group relative" 
     onclick="window.location='{{ route('theses.show', $thesis->id) }}'"
     x-show="!search || '{{ strtolower(addslashes($thesis->student->name ?? '')) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($thesis->student->identifier ?? '')) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($thesis->final_title ?? $thesis->title ?? '')) }}'.includes(search.toLowerCase())">
    
    <!-- Header: Student Avatar & Info -->
    <div class="flex items-center justify-between gap-3 mb-2.5">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-full overflow-hidden bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center shrink-0 font-black text-indigo-600 dark:text-indigo-400 text-xs">
                @if($thesis->student && $thesis->student->avatar_url)
                    <img src="{{ $thesis->student->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($thesis->student->name ?? 'M', 0, 2)) }}
                @endif
            </div>
            <div class="min-w-0">
                <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 leading-tight truncate uppercase tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $thesis->student->name ?? 'Mahasiswa' }}
                </h4>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="text-[9px] text-slate-400 font-mono font-bold">{{ $thesis->student->identifier ?? 'NPM -' }}</span>
                    @if($thesis->student && $thesis->student->entry_year)
                        <span class="text-[8px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-1 py-0.2 rounded">
                            '{{ substr($thesis->student->entry_year, -2) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        @if($thesis->is_migrated)
            <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-900/50 px-1.5 py-0.5 rounded-md">
                Migrasi
            </span>
        @endif
    </div>

    <!-- Title Section -->
    <div class="my-2.5">
        <h5 class="text-[11px] font-bold text-slate-700 dark:text-slate-300 leading-snug line-clamp-2 uppercase" title="{{ $thesis->final_title ?? $thesis->title }}">
            {{ $thesis->final_title ?? $thesis->title }}
        </h5>
        
        @if($simScore > 0)
            <div class="mt-1.5 flex items-center gap-1">
                @if($simScore >= 60)
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.3 rounded text-[8px] font-black uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                        {{ $simScore }}% SANGAT MIRIP
                    </span>
                @elseif($simScore >= 40)
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.3 rounded text-[8px] font-black uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        {{ $simScore }}% MIRIP
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.3 rounded text-[8px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $simScore }}% UNIK
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Supervisors Section -->
    <div class="pt-2.5 border-t border-slate-100 dark:border-slate-800/80 space-y-1.5">
        <!-- Pembimbing 1 -->
        <div class="flex items-center gap-1.5 text-[10px] min-w-0">
            <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase shrink-0" style="background-color: #e0e7ff; color: #3730a3;">P1</span>
            @if($thesis->pembimbing1)
                <span class="font-bold text-slate-700 dark:text-slate-300 text-[10px] uppercase truncate tracking-tight">{{ $thesis->pembimbing1->name }}</span>
            @else
                <span class="font-bold text-amber-600 dark:text-amber-400 text-[9px] uppercase tracking-tight italic">Belum Ditentukan</span>
            @endif
        </div>

        <!-- Pembimbing 2 -->
        <div class="flex items-center gap-1.5 text-[10px] min-w-0">
            <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase shrink-0" style="background-color: #f3e8ff; color: #6b21a8;">P2</span>
            @if($thesis->pembimbing2)
                <span class="font-bold text-slate-700 dark:text-slate-300 text-[10px] uppercase truncate tracking-tight">{{ $thesis->pembimbing2->name }}</span>
            @else
                <span class="font-bold text-amber-600 dark:text-amber-400 text-[9px] uppercase tracking-tight italic">Belum Ditentukan</span>
            @endif
        </div>
    </div>
</div>

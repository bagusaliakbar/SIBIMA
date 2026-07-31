<div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-md hover:border-{{ $accent }}-300 dark:hover:border-{{ $accent }}-700 transition-all cursor-pointer group relative overflow-hidden" onclick="window.location='{{ route('theses.show', $thesis->id) }}'">
    <!-- Left Accent Border -->
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-{{ $accent }}-400 opacity-0 group-hover:opacity-100 transition-opacity"></div>
    
    <div class="flex items-start gap-3 mb-3">
        <div class="w-8 h-8 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 shrink-0 border border-slate-200 dark:border-slate-600">
            <img src="{{ $thesis->student->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
        </div>
        <div>
            <h4 class="text-[11px] font-black text-slate-800 dark:text-slate-100 leading-tight uppercase tracking-tight">{{ $thesis->student->name }}</h4>
            <p class="text-[9px] text-slate-500 font-mono font-bold mt-0.5">{{ $thesis->student->identifier }}</p>
        </div>
    </div>
    
    <p class="text-[10px] text-slate-600 dark:text-slate-400 font-medium mb-3 line-clamp-3 leading-relaxed" title="{{ $thesis->title }}">
        {{ $thesis->title }}
    </p>
    
    <div class="space-y-1 border-t border-slate-100 dark:border-slate-700/50 pt-2.5">
        @if($thesis->pembimbing1)
        <div class="flex items-center text-[8.5px] text-slate-500 uppercase tracking-wider font-bold">
            <span class="w-5 text-center mr-1.5 px-1 py-0.5 bg-slate-100 dark:bg-slate-700 rounded text-slate-400">P1</span> 
            <span class="truncate">{{ $thesis->pembimbing1->name }}</span>
        </div>
        @else
        <div class="flex items-center text-[8.5px] text-orange-500 uppercase tracking-wider font-bold">
            <span class="w-5 text-center mr-1.5 px-1 py-0.5 bg-orange-50 dark:bg-orange-900/20 rounded text-orange-400">P1</span> 
            <span class="truncate italic">Belum Ditentukan</span>
        </div>
        @endif
        
        @if($thesis->pembimbing2)
        <div class="flex items-center text-[8.5px] text-slate-500 uppercase tracking-wider font-bold">
            <span class="w-5 text-center mr-1.5 px-1 py-0.5 bg-slate-100 dark:bg-slate-700 rounded text-slate-400">P2</span> 
            <span class="truncate">{{ $thesis->pembimbing2->name }}</span>
        </div>
        @endif
        
        @if($thesis->is_migrated)
        <div class="mt-2 text-right">
            <span class="inline-flex items-center gap-1 text-[8px] font-black uppercase tracking-widest text-orange-500 bg-orange-50 dark:bg-orange-900/20 px-2 py-0.5 rounded">
                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Migrasi
            </span>
        </div>
        @endif
    </div>
</div>

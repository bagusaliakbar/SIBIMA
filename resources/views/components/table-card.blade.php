@props([
    'title' => '',
    'subtitle' => '',
    'headerActions' => null,
    'footer' => null
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/70 dark:border-slate-700/80 overflow-hidden transition-all']) }}>
    @if($title || $subtitle || $headerActions)
        <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-700/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/40 dark:bg-slate-800/40">
            <div>
                @if($title)
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 tracking-tight">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">{{ $subtitle }}</p>
                @endif
            </div>
            
            @if($headerActions)
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="overflow-x-auto max-h-[72vh] custom-scrollbar">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="p-4 sm:p-6 border-t border-slate-100 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/50">
            {!! $footer !!}
        </div>
    @endif
</div>

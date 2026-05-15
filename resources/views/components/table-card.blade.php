@props([
    'title' => '',
    'subtitle' => '',
    'headerActions' => null,
    'footer' => null
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden']) }}>
    @if($title || $subtitle || $headerActions)
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                @if($title)
                    <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            
            @if($headerActions)
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="overflow-x-auto">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
            {!! $footer !!}
        </div>
    @endif
</div>

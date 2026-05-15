@props([
    'title' => '',
    'value' => '',
    'icon' => null,
    'color' => 'orange', // orange, emerald, blue, indigo, pink, red
    'subtitle' => null
])

@php
    $colorClasses = [
        'orange' => [
            'bg' => 'bg-orange-50 dark:bg-orange-500/5',
            'icon_bg' => 'bg-orange-100 dark:bg-orange-500/10',
            'icon_text' => 'text-orange-600 dark:text-orange-400',
            'icon_hover' => 'group-hover:bg-orange-600',
            'border_hover' => 'hover:border-orange-200 dark:hover:border-orange-900/30'
        ],
        'emerald' => [
            'bg' => 'bg-emerald-50 dark:bg-emerald-500/5',
            'icon_bg' => 'bg-emerald-100 dark:bg-emerald-500/10',
            'icon_text' => 'text-emerald-600 dark:text-emerald-400',
            'icon_hover' => 'group-hover:bg-emerald-600',
            'border_hover' => 'hover:border-emerald-200 dark:hover:border-emerald-900/30'
        ],
        'blue' => [
            'bg' => 'bg-blue-50 dark:bg-blue-500/5',
            'icon_bg' => 'bg-blue-100 dark:bg-blue-500/10',
            'icon_text' => 'text-blue-600 dark:text-blue-400',
            'icon_hover' => 'group-hover:bg-blue-600',
            'border_hover' => 'hover:border-blue-200 dark:hover:border-blue-900/30'
        ],
        'indigo' => [
            'bg' => 'bg-indigo-50 dark:bg-indigo-500/5',
            'icon_bg' => 'bg-indigo-100 dark:bg-indigo-500/10',
            'icon_text' => 'text-indigo-600 dark:text-indigo-400',
            'icon_hover' => 'group-hover:bg-indigo-600',
            'border_hover' => 'hover:border-indigo-200 dark:hover:border-indigo-900/30'
        ],
        'pink' => [
            'bg' => 'bg-pink-50 dark:bg-pink-500/5',
            'icon_bg' => 'bg-pink-100 dark:bg-pink-500/10',
            'icon_text' => 'text-pink-600 dark:text-pink-400',
            'icon_hover' => 'group-hover:bg-pink-600',
            'border_hover' => 'hover:border-pink-200 dark:hover:border-pink-900/30'
        ],
        'red' => [
            'bg' => 'bg-red-50 dark:bg-red-500/5',
            'icon_bg' => 'bg-red-100 dark:bg-red-500/10',
            'icon_text' => 'text-red-600 dark:text-red-400',
            'icon_hover' => 'group-hover:bg-red-600',
            'border_hover' => 'hover:border-red-200 dark:hover:border-red-900/30'
        ],
    ][$color] ?? $colorClasses['orange'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800/50 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all hover:shadow-md ' . $colorClasses['border_hover']]) }}>
    <div class="absolute right-0 top-0 -mr-4 -mt-4 w-24 h-24 {{ $colorClasses['bg'] }} rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
    <div class="relative">
        <div class="w-10 h-10 {{ $colorClasses['icon_bg'] }} {{ $colorClasses['icon_text'] }} rounded-lg flex items-center justify-center mb-4 transition-colors {{ $colorClasses['icon_hover'] }} group-hover:text-white">
            @if($icon)
                {{ $icon }}
            @endif
        </div>
        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $title }}</h3>
        <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mt-1 uppercase">{{ $value }}</h2>
        @if($subtitle)
            <div class="mt-2 space-y-0.5 border-t border-slate-100 dark:border-slate-700/50 pt-2">
                {{ $subtitle }}
            </div>
        @endif
        {{ $slot }}
    </div>
</div>

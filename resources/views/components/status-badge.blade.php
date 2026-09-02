@props([
    'type' => 'slate', // emerald, rose, orange, blue, slate, indigo, amber, pink
    'label' => '',
    'pulse' => false,
    'size' => 'xs'
])

@php
    $baseClasses = "inline-flex items-center px-2 py-0.5 rounded-md font-bold border uppercase tracking-wider transition-colors";
    
    $types = [
        'emerald' => 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30',
        'rose' => 'bg-rose-50 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-500/30',
        'red' => 'bg-rose-50 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-500/30',
        'orange' => 'bg-orange-50 dark:bg-orange-500/15 text-orange-700 dark:text-orange-300 border-orange-200 dark:border-orange-500/30',
        'blue' => 'bg-blue-50 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/30',
        'slate' => 'bg-slate-100 dark:bg-slate-800/70 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700/60',
        'indigo' => 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-500/30',
        'amber' => 'bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30',
        'pink' => 'bg-pink-50 dark:bg-pink-500/15 text-pink-700 dark:text-pink-300 border-pink-200 dark:border-pink-500/30',
    ];

    $sizes = [
        'xs' => 'text-[9px]',
        'sm' => 'text-[10px]',
        'md' => 'text-xs',
    ];

    $typeClasses = $types[$type] ?? $types['slate'];
    $sizeClasses = $sizes[$size] ?? $sizes['xs'];
    $pulseClass = $pulse ? 'animate-pulse' : '';
@endphp

<span {{ $attributes->merge(['class' => "$baseClasses $typeClasses $sizeClasses $pulseClass"]) }}>
    {{ $label ?: $slot }}
</span>

@props([
    'type' => 'slate', // emerald, rose, orange, blue, slate
    'label' => '',
    'pulse' => false,
    'size' => 'xs'
])

@php
    $baseClasses = "inline-flex items-center px-1.5 py-0.5 rounded font-black border uppercase tracking-tight";
    
    $types = [
        'emerald' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
        'rose' => 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-800',
        'orange' => 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border-orange-100 dark:border-orange-800',
        'blue' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
        'slate' => 'bg-slate-50 dark:bg-slate-900/30 text-slate-500 dark:text-slate-400 border-slate-100 dark:border-slate-800',
        'indigo' => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border-indigo-100 dark:border-indigo-800',
        'amber' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
        'pink' => 'bg-pink-50 dark:bg-pink-900/30 text-pink-700 dark:text-pink-400 border-pink-100 dark:border-pink-800',
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

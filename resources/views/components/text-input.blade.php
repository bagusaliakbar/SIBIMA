@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:outline-none focus:border-orange-500 dark:focus:border-orange-500 focus:ring-4 focus:ring-orange-500/15 dark:focus:ring-orange-500/20 rounded-xl shadow-xs transition-all']) }}>

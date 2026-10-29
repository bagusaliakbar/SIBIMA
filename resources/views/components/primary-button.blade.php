<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-4 h-10 bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 active:bg-orange-800 border border-transparent rounded-xl font-bold text-xs text-white tracking-wide shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500/40 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 disabled:opacity-50 transition-all duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>

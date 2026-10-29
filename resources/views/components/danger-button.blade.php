<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-4 h-10 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 border border-transparent rounded-xl font-bold text-xs text-white tracking-wide shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/40 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 disabled:opacity-50 transition-all duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>

@props([
    'name' => 'search',
    'value' => '',
    'placeholder' => 'Cari...',
    'route' => null,
    'params' => [] // Additional query params to keep
])

@php
    $actionUrl = $route ? (Route::has($route) ? route($route) : $route) : url()->current();
@endphp

<form action="{{ $actionUrl }}" method="GET" class="relative w-full sm:w-auto group m-0 p-0 h-10 flex items-center">
    @foreach($params as $key => $val)
        @if($val)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endif
    @endforeach

    <button type="submit"
            aria-label="Jalankan pencarian"
            title="Cari"
            class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 group-focus-within:text-orange-500 transition-colors cursor-pointer">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </button>

    <input type="text"
           name="{{ $name }}"
           value="{{ $value }}"
           placeholder="{{ $placeholder }}"
           class="block w-full sm:w-64 h-10 m-0 pl-9 pr-9 box-border border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm sm:text-xs transition-colors font-medium">

    @if($value)
        <a href="{{ $actionUrl }}?{{ http_build_query(collect($params)->forget($name)->toArray()) }}"
           aria-label="Hapus pencarian"
           title="Hapus kata kunci"
           class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </a>
    @endif
</form>

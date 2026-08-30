@extends('errors.layout')

@section('title', 'Kendala Server Internal')
@section('code', '500')
@section('status_badge', 'Kesalahan Server Internal')

@section('icon')
<div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-200/60 dark:border-rose-800/60 shadow-sm group-hover:scale-110 transition-all duration-300">
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
    </svg>
</div>
@endsection

@section('message')
    Mohon maaf atas ketidaknyamanannya. Terjadi kendala internal pada sistem kami saat memproses permintaan Anda. Laporan kesalahan otomatis telah dicatat dan sedang ditangani oleh tim teknis.
@endsection

@section('actions')
<button onclick="window.location.reload()" 
        type="button"
        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-all hover:scale-105 active:scale-95 shadow-sm cursor-pointer uppercase tracking-wider">
    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    Muat Ulang
</button>

<a href="{{ url('/dashboard') }}" 
   class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3 bg-gradient-to-r from-rose-600 via-orange-600 to-amber-500 hover:from-rose-500 hover:to-amber-400 text-white font-black text-xs rounded-xl transition-all shadow-lg shadow-rose-600/25 hover:shadow-rose-600/40 hover:scale-105 active:scale-95 uppercase tracking-widest cursor-pointer">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7-7-7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
    Ke Dashboard
</a>
@endsection

@section('extra')
<div class="flex items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400 bg-slate-50/60 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800">
    <span class="font-medium">Kendala berlanjut? Silakan hubungi Administrator.</span>
    <span class="font-mono text-[10px] text-slate-400 dark:text-slate-500">ID: ERR-{{ date('YmdHi') }}</span>
</div>
@endsection

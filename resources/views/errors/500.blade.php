@extends('errors.layout')

@section('title', 'Kendala Server Internal')
@section('code', '500')
@section('status_badge', 'Internal Server Error')

@section('message')
    Mohon maaf atas ketidaknyamanannya. Terjadi kendala internal pada sistem saat memproses permintaan Anda. Laporan kesalahan telah dicatat untuk ditangani oleh tim teknis.
@endsection

@section('actions')
<button onclick="window.location.reload()" 
        type="button"
        class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3 bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md shadow-orange-600/20 hover:shadow-orange-600/30 active:scale-95 transition-all cursor-pointer">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    Muat Ulang
</button>

<a href="{{ url('/dashboard') }}" 
   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm active:scale-95 transition-all cursor-pointer">
    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7-7-7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
    Ke Dashboard
</a>
@endsection

@section('extra')
<div class="pt-8 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
    <span>Kendala berlanjut? Hubungi Administrator.</span>
    <span class="font-mono text-[10px] text-slate-400 dark:text-slate-500">REF: ERR-500</span>
</div>
@endsection

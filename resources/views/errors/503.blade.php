@extends('errors.layout')

@section('title', 'Pemeliharaan Sistem')
@section('code', '503')
@section('status_badge', 'Maintenance')

@section('message')
    Aplikasi SIBIMA sedang menjalani pemeliharaan sistem berkala demi peningkatan performa, stabilitas, dan keamanan. Kami akan segera kembali beroperasi normal.
@endsection

@section('actions')
<button onclick="window.location.reload()" 
        type="button"
        class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md shadow-orange-600/20 hover:shadow-orange-600/30 active:scale-95 transition-all cursor-pointer">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    Periksa Status Sistem
</button>
@endsection

@section('extra')
<div class="pt-8 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
    <span>Pemeliharaan sedang berlangsung &bull; Mohon menunggu beberapa saat</span>
</div>
@endsection

@extends('errors.layout')

@section('title', 'Pemeliharaan Sistem')
@section('code', '503')
@section('status_badge', 'Mode Pemeliharaan Berkala')

@section('message')
    Aplikasi SIBIMA sedang menjalani pemeliharaan sistem rutin demi peningkatan kecepatan, stabilitas, dan keamanan data. Kami akan segera kembali beroperasi penuh sesaat lagi.
@endsection

@section('actions')
<button onclick="window.location.reload()" 
        type="button"
        class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs rounded-xl transition-all shadow-lg shadow-emerald-600/25 hover:shadow-emerald-600/40 hover:scale-105 active:scale-95 uppercase tracking-widest cursor-pointer">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    Periksa Status Sistem
</button>
@endsection

@section('extra')
<div class="flex items-center justify-center gap-2 text-xs text-slate-500 dark:text-slate-400">
    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
    <span class="font-medium">Pemeliharaan sedang berlangsung &bull; Mohon tunggu beberapa saat</span>
</div>
@endsection

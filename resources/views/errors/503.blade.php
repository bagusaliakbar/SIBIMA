@extends('errors.layout')

@section('title', 'Pemeliharaan Sistem')
@section('code', '503')
@section('status_badge', 'Mode Pemeliharaan Berkala')

@section('icon')
<div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-200/60 dark:border-emerald-800/60 shadow-sm group-hover:rotate-45 transition-all duration-500">
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
    </svg>
</div>
@endsection

@section('message')
    Aplikasi SIBIMA sedang menjalani pemeliharaan sistem rutin demi peningkatan kecepatan, stabilitas, dan keamanan data. Kami akan segera kembali beroperasi penuh sesaat lagi.
@endsection

@section('actions')
<button onclick="window.location.reload()" 
        type="button"
        class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs rounded-xl transition-all shadow-lg shadow-emerald-600/25 hover:shadow-emerald-600/40 hover:scale-105 active:scale-95 uppercase tracking-widest cursor-pointer">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    Periksa Kembali Status Sistem
</button>
@endsection

@section('extra')
<div class="flex items-center justify-center gap-2 text-xs text-slate-500 dark:text-slate-400">
    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
    <span class="font-medium">Pemeliharaan sedang berlangsung &bull; Mohon menunggu beberapa menit</span>
</div>
@endsection

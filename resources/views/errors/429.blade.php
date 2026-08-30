@extends('errors.layout')

@section('title', 'Terlalu Banyak Permintaan')
@section('code', '429')
@section('status_badge', 'Batas Frekuensi Akses')

@section('icon')
<div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-200/60 dark:border-indigo-800/60 shadow-sm group-hover:scale-110 transition-all duration-300">
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
    </svg>
</div>
@endsection

@section('message')
    Harap tenang. Sistem mendeteksi terlalu banyak permintaan secara beruntun dari perangkat Anda. Harap beri jeda sejenak (sekitar 1-2 menit) sebelum memuat kembali halaman.
@endsection

@section('actions')
<button onclick="setTimeout(() => window.location.reload(), 1000)" 
        type="button"
        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-all hover:scale-105 active:scale-95 shadow-sm cursor-pointer uppercase tracking-wider">
    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    Coba Muat Ulang
</button>

<a href="{{ url('/dashboard') }}" 
   class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3 bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-black text-xs rounded-xl transition-all shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/40 hover:scale-105 active:scale-95 uppercase tracking-widest cursor-pointer">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7-7-7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
    Ke Dashboard
</a>
@endsection

@section('extra')
<div class="flex items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400 bg-slate-50/60 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800">
    <span class="font-medium">🔒 Pembatasan ini otomatis dicabut setelah beberapa saat.</span>
</div>
@endsection

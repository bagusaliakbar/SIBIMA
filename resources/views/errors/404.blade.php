@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')
@section('status_badge', 'URL Tidak Ditemukan')

@section('icon')
<div class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-200/60 dark:border-purple-800/60 shadow-sm group-hover:scale-110 transition-all duration-300">
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
    </svg>
</div>
@endsection

@section('message')
    Halaman atau berkas yang Anda cari tidak dapat ditemukan. Kemungkinan tautan rusak, data telah dipindahkan, atau alamat web yang dimasukkan kurang tepat.
@endsection

@section('actions')
<button onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}'" 
        type="button"
        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-all hover:scale-105 active:scale-95 shadow-sm cursor-pointer uppercase tracking-wider">
    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    Kembali
</button>

<a href="{{ url('/dashboard') }}" 
   class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3 bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 hover:from-orange-500 hover:to-amber-400 text-white font-black text-xs rounded-xl transition-all shadow-lg shadow-orange-600/25 hover:shadow-orange-600/40 hover:scale-105 active:scale-95 uppercase tracking-widest cursor-pointer">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7-7-7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
    Ke Dashboard
</a>
@endsection

@section('extra')
<div class="flex items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400 bg-slate-50/60 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800">
    <span class="font-medium">Bingung mencari menu yang hilang?</span>
    <a href="{{ url('/dashboard') }}" class="font-black text-orange-600 dark:text-orange-400 hover:underline uppercase tracking-wider text-[11px] shrink-0">
        Buka Beranda &rarr;
    </a>
</div>
@endsection

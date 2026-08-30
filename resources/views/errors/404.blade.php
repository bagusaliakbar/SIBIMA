@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')
@section('status_badge', 'Not Found')

@section('message')
    Maaf, halaman atau berkas yang Anda cari tidak dapat ditemukan. Kemungkinan tautan rusak, data telah dipindahkan, atau alamat URL yang dimasukkan kurang tepat.
@endsection

@section('actions')
<a href="{{ url('/dashboard') }}" 
   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md shadow-orange-600/20 hover:shadow-orange-600/30 active:scale-95 transition-all">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7-7-7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
    Ke Dashboard
</a>

<button onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}'" 
        type="button"
        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm active:scale-95 transition-all cursor-pointer">
    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    Kembali
</button>
@endsection

@section('extra')
<div class="pt-8 border-t border-slate-200/80 dark:border-slate-800 flex flex-wrap items-center justify-center gap-4 text-xs font-semibold text-slate-500 dark:text-slate-400">
    <span class="text-slate-400 dark:text-slate-500 font-medium">Navigasi Cepat:</span>
    <a href="{{ route('dashboard') }}" class="text-orange-600 dark:text-orange-400 hover:underline">Dashboard</a>
    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
    <a href="{{ route('theses.index') }}" class="hover:text-slate-900 dark:hover:text-white transition-colors">Judul Skripsi</a>
    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
    <a href="{{ route('mentoring-sessions.index') }}" class="hover:text-slate-900 dark:hover:text-white transition-colors">Bimbingan</a>
</div>
@endsection

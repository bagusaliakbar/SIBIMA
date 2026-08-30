@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '403')
@section('status_badge', 'Forbidden')

@section('message')
    Maaf, akun Anda tidak memiliki hak akses untuk membuka halaman atau fitur ini. Pastikan Anda telah masuk dengan peran (*role*) yang berwenang.
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
<div class="pt-8 border-t border-slate-200/80 dark:border-slate-800 text-xs font-semibold text-slate-500 dark:text-slate-400">
    <span>Memerlukan izin khusus? Silakan hubungi <b>Administrator</b> atau <b>Koordinator Program Studi</b>.</span>
</div>
@endsection

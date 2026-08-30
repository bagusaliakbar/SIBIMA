@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '403')
@section('status_badge', 'Izin Akses Dibatasi')

@section('message')
    Maaf, akun Anda tidak memiliki hak akses untuk membuka halaman atau fitur ini. Pastikan Anda telah masuk dengan peran (*role*) yang sesuai untuk tindakan ini.
@endsection

@section('actions')
<button onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}'" 
        type="button"
        class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-all hover:scale-105 active:scale-95 shadow-xs cursor-pointer uppercase tracking-wider">
    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    Kembali
</button>

<a href="{{ url('/dashboard') }}" 
   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-gradient-to-r from-rose-600 via-orange-600 to-amber-500 hover:from-rose-500 hover:to-amber-400 text-white font-black text-xs rounded-xl transition-all shadow-lg shadow-rose-600/25 hover:shadow-rose-600/40 hover:scale-105 active:scale-95 uppercase tracking-widest cursor-pointer">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7-7-7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
    Ke Dashboard
</a>
@endsection

@section('extra')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400 bg-slate-50/60 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80 text-left">
    <span class="font-medium">Memerlukan hak akses khusus?</span>
    <span class="font-bold text-slate-700 dark:text-slate-300">Hubungi Administrator / Kaprodi</span>
</div>
@endsection

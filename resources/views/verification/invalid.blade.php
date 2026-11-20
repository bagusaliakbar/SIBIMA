@extends('layouts.verification')

@section('title', 'Dokumen Tidak Valid')

@section('content')
    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/60 dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="bg-rose-600 dark:bg-rose-700 p-10 text-center">
            <div class="w-16 h-16 bg-white/15 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-white/25">
                <svg aria-hidden="true" class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h1 class="text-white text-xl sm:text-2xl font-extrabold uppercase tracking-wider">Dokumen Tidak Valid</h1>
            <p class="text-rose-100 text-xs font-medium mt-2">Verifikasi Gagal / Token Tidak Ditemukan</p>
        </div>

        <div class="p-8 text-center">
            <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-7">
                Maaf, token verifikasi yang Anda gunakan tidak terdaftar di sistem SIBIMA atau telah kedaluwarsa. Mohon pastikan Anda memindai QR Code asli dari dokumen resmi.
            </p>

            <a href="{{ route('login') }}" class="inline-flex items-center justify-center h-10 px-6 bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-colors shadow-sm">
                Kembali ke Portal SIBIMA
            </a>
        </div>

        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 text-center border-t border-slate-200/80 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Layanan Verifikasi SIBIMA &mdash; FASILKOM UNSUB</p>
        </div>
    </div>
@endsection

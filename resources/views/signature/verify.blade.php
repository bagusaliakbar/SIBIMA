@extends('layouts.verification')

@section('title', 'Verifikasi Tanda Tangan Digital')

@section('content')
    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/60 dark:shadow-none overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="p-8 text-center">
            <div class="mb-6 flex justify-center">
                @if($status === 'valid')
                    <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/60 rounded-full flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <svg aria-hidden="true" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                @else
                    <div class="w-20 h-20 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800/60 rounded-full flex items-center justify-center text-rose-600 dark:text-rose-400">
                        <svg aria-hidden="true" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                @endif
            </div>

            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 mb-2 tracking-tight">
                {{ $status === 'valid' ? 'Tanda Tangan Valid' : 'Verifikasi Gagal' }}
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 font-medium mb-8">
                {{ $message }}
            </p>

            @if($status === 'valid')
                <div class="bg-slate-50/80 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-700/80 text-left space-y-4 mb-6">
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Nama Pemilik</span>
                        <p class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $user->name }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Peran</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100 capitalize">{{ $user->role }}</p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">NIDN / NPM</span>
                            <p class="text-sm font-bold font-mono text-slate-900 dark:text-slate-100">{{ $user->identifier }}</p>
                        </div>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Status Sistem</span>
                        <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                            <svg aria-hidden="true" class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            SIBIMA Digital Signature Verified
                        </p>
                    </div>
                </div>

                @if($user->signature)
                    <div class="mb-8">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-3 text-center">Spesimen Tanda Tangan</span>
                        {{-- Intentional white paper canvas for dark ink signature legibility in both themes --}}
                        <div class="p-4 bg-white rounded-xl border border-slate-200 dark:border-slate-700 inline-block shadow-inner">
                            <img src="{{ $user->decrypted_signature }}" alt="Spesimen tanda tangan {{ $user->name }}" class="h-16 w-auto">
                        </div>
                    </div>
                @endif
            @endif

            <div class="pt-6 border-t border-slate-200/80 dark:border-slate-800">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-xs font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 transition-colors">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Portal SIBIMA
                </a>
            </div>
        </div>
        <div class="bg-slate-900 dark:bg-slate-950 p-4 text-center border-t border-slate-800">
            <p class="text-[11px] font-bold text-slate-300 uppercase tracking-widest">Verified by SIBIMA Portal &mdash; FASILKOM UNSUB</p>
        </div>
    </div>
@endsection

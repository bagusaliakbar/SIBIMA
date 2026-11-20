@extends('layouts.verification')

@section('title', 'Verifikasi Dokumen Berita Acara')

@section('content')
    <div class="max-w-2xl w-full bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/60 dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden">
        <!-- Header Terverifikasi -->
        <div class="bg-white dark:bg-slate-900 p-8 text-center border-b border-slate-200/80 dark:border-slate-800">
            <div class="space-y-3 max-w-xl mx-auto">
                <div class="w-16 h-16 bg-white rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center justify-center mx-auto shadow-xs p-2">
                    <img src="{{ asset('logo_unsub.png') }}" class="w-full h-auto object-contain" alt="Logo Universitas Subang">
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 rounded-full text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                    <svg aria-hidden="true" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Dokumen Resmi Terverifikasi</span>
                </div>

                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-slate-100 uppercase tracking-tight">
                    Berita Acara {{ $type }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium">
                    Sistem Informasi Bimbingan Mahasiswa (SIBIMA) &mdash; FASILKOM UNSUB
                </p>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Informasi Akademik -->
            <div class="bg-slate-50/80 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-700/80">
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">Informasi Akademik</p>

                <div class="space-y-3.5 text-xs sm:text-sm">
                    <div class="flex justify-between items-start gap-4">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Nama Mahasiswa</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 text-right">{{ $detail->thesis->student->name }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">NPM</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 text-right font-mono">{{ $detail->thesis->student->identifier }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4 border-t border-slate-200/80 dark:border-slate-700 pt-3.5">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Jenis Dokumen</span>
                        <span class="px-2.5 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 text-[11px] font-bold rounded-md border border-blue-200 dark:border-blue-800/60">
                            BERITA ACARA {{ strtoupper($type) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Waktu Pelaksanaan</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 text-right">{{ $detail->start_time->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                </div>
            </div>

            <!-- Tim Akademik Terverifikasi -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 space-y-4">
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tim Akademik Terverifikasi</p>

                @php
                    $kaprodiUser = \App\Models\User::where('role', 'kaprodi')->first();
                    $kaprodiName = $kaprodiUser ? $kaprodiUser->name : 'Bagus Ali Akbar, S.SI., M.Kom';
                @endphp

                <!-- Ketua Program Studi -->
                <div class="flex items-center gap-3 p-3.5 bg-orange-50/70 dark:bg-orange-950/30 rounded-xl border border-orange-200/80 dark:border-orange-800/60">
                    <div class="w-9 h-9 rounded-xl bg-orange-600 flex items-center justify-center shrink-0">
                        <svg aria-hidden="true" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 truncate">{{ $kaprodiName }}</p>
                        <p class="text-[11px] font-semibold text-orange-700 dark:text-orange-400">Ketua Program Studi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Pembimbing -->
                    <div class="space-y-3">
                        @if($detail->thesis->pembimbing1)
                            <div class="flex items-center gap-3 p-3 bg-emerald-50/70 dark:bg-emerald-950/30 rounded-xl border border-emerald-200/80 dark:border-emerald-800/60">
                                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center shrink-0">
                                    <svg aria-hidden="true" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $detail->thesis->pembimbing1->name }}</p>
                                    <p class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">Pembimbing I</p>
                                </div>
                            </div>
                        @endif

                        @if($detail->thesis->pembimbing2)
                            <div class="flex items-center gap-3 p-3 bg-emerald-50/70 dark:bg-emerald-950/30 rounded-xl border border-emerald-200/80 dark:border-emerald-800/60">
                                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center shrink-0">
                                    <svg aria-hidden="true" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $detail->thesis->pembimbing2->name }}</p>
                                    <p class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">Pembimbing II</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Penguji -->
                    <div class="space-y-3">
                        @if($detail->examiner1)
                            <div class="flex items-center gap-3 p-3 bg-blue-50/70 dark:bg-blue-950/30 rounded-xl border border-blue-200/80 dark:border-blue-800/60">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0">
                                    <svg aria-hidden="true" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $detail->examiner1->name }}</p>
                                    <p class="text-[11px] font-semibold text-blue-700 dark:text-blue-400">Penguji I</p>
                                </div>
                            </div>
                        @endif

                        @if($detail->examiner2)
                            <div class="flex items-center gap-3 p-3 bg-blue-50/70 dark:bg-blue-950/30 rounded-xl border border-blue-200/80 dark:border-blue-800/60">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0">
                                    <svg aria-hidden="true" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $detail->examiner2->name }}</p>
                                    <p class="text-[11px] font-semibold text-blue-700 dark:text-blue-400">Penguji II</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-center space-y-2">
                <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                    * Dokumen ini dihasilkan secara otomatis oleh sistem SIBIMA dan telah tervalidasi secara digital.
                </p>
                <p class="text-[11px] font-mono font-semibold text-slate-500 dark:text-slate-400 tracking-wider">
                    Token: {{ substr($detail->verification_token, 0, 8) }}...{{ substr($detail->verification_token, -8) }}
                </p>
            </div>
        </div>

        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 text-center border-t border-slate-200/80 dark:border-slate-800">
            <a href="{{ route('login') }}" class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 transition-colors">
                Kunjungi Portal Utama SIBIMA &rarr;
            </a>
        </div>
    </div>
@endsection

@extends('layouts.verification')

@section('title', 'Verifikasi Surat Keterangan Lulus (SKL)')

@section('content')
    <div class="max-w-2xl w-full bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/60 dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden">
        <!-- Header Terverifikasi -->
        <div class="bg-emerald-600 dark:bg-emerald-700 p-8 sm:p-10 text-center text-white">
            <div class="space-y-3">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto shadow-md p-2">
                    <img src="{{ asset('logo_unsub.png') }}" class="w-full h-auto object-contain" alt="Logo Universitas Subang">
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 rounded-full text-xs font-bold uppercase tracking-wider text-white border border-white/25">
                    <svg aria-hidden="true" class="w-4 h-4 text-emerald-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Dokumen Resmi Terverifikasi</span>
                </div>

                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">SURAT KETERANGAN LULUS (SKL)</h1>
                <p class="text-xs sm:text-sm text-emerald-100 font-medium max-w-md mx-auto">
                    Fakultas Ilmu Komputer &mdash; Universitas Subang
                </p>
            </div>
        </div>

        <!-- Body Details -->
        <div class="p-6 sm:p-8 space-y-6">
            <!-- Dokumen Information Box -->
            <div class="bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl p-5 space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300">Nomor Surat Resmi</span>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-mono font-bold text-xs shadow-xs">
                        {{ $graduation->skl_number }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-300 pt-2 border-t border-emerald-200/70 dark:border-emerald-800/60">
                    <span>Tanggal Yudisium / Kelulusan:</span>
                    <strong class="text-slate-900 dark:text-slate-100 font-bold">{{ $graduation->formatted_graduation_date }}</strong>
                </div>
            </div>

            <!-- Mahasiswa Detail Card -->
            <div class="bg-slate-50/80 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-700/80 space-y-4">
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Identitas Mahasiswa Lulusan</p>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Nama Lengkap</span>
                        <strong class="text-slate-900 dark:text-slate-100 font-bold text-right">{{ $graduation->student->name }}</strong>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Nomor Pokok Mahasiswa (NPM)</span>
                        <span class="text-slate-900 dark:text-slate-100 font-bold font-mono text-right">{{ $graduation->student->identifier }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Program Studi</span>
                        @php
                            $id = strtoupper(trim($graduation->student->identifier ?? ''));
                            $pName = str_starts_with($id, 'D1A') ? 'Sistem Informasi' : (str_starts_with($id, 'D1B') ? 'Teknik Informatika' : 'Ilmu Komputer');
                        @endphp
                        <strong class="text-slate-900 dark:text-slate-100 font-bold text-right">{{ $pName }} (S1)</strong>
                    </div>
                    @if($graduation->gpa)
                        <div class="flex justify-between items-start gap-4 border-t border-slate-200/80 dark:border-slate-700 pt-3">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">IPK Kelulusan</span>
                            <span class="text-emerald-700 dark:text-emerald-400 font-extrabold text-base tabular-nums">{{ number_format($graduation->gpa, 2) }}</span>
                        </div>
                    @endif
                    @if($graduation->predicate)
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">Predikat Kelulusan</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 font-bold text-xs">
                                {{ $graduation->predicate }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Thesis Detail -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 space-y-2">
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Judul Skripsi Disahkan</p>
                <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100 italic leading-snug">
                    "{{ $graduation->thesis->display_title }}"
                </h2>
                <div class="pt-3 border-t border-slate-200/80 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 space-y-1">
                    <p><span class="font-medium">Pembimbing 1:</span> <strong class="text-slate-800 dark:text-slate-200">{{ $graduation->thesis->pembimbing1->name ?? '-' }}</strong></p>
                    <p><span class="font-medium">Pembimbing 2:</span> <strong class="text-slate-800 dark:text-slate-200">{{ $graduation->thesis->pembimbing2->name ?? '-' }}</strong></p>
                </div>
            </div>

            <!-- Verified By Section -->
            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Disetujui &amp; Diterbitkan Oleh</p>
                        <p class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $graduation->approver->name ?? $kaprodi->name ?? 'Ketua Program Studi' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $graduation->approved_at ? $graduation->approved_at->locale('id')->translatedFormat('d F Y, H:i') : '-' }} WIB</p>
                    </div>
                </div>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 hover:underline">
                    &larr; Masuk ke Portal SIBIMA FASILKOM
                </a>
            </div>
        </div>
    </div>
@endsection

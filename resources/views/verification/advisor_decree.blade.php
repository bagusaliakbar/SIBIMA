@extends('layouts.verification')

@section('title', 'Verifikasi SK Dosen Pembimbing Skripsi')

@section('content')
    <div class="max-w-3xl w-full bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/60 dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden">
        <!-- Header Terverifikasi -->
        <div class="bg-white dark:bg-slate-900 p-8 sm:p-10 text-center border-b border-slate-200/80 dark:border-slate-800">
            <div class="space-y-3.5 max-w-xl mx-auto">
                <div class="w-16 h-16 bg-white border border-slate-200 dark:border-slate-700 rounded-2xl flex items-center justify-center mx-auto shadow-xs p-2">
                    @if(file_exists(public_path('logo_unsub.png')))
                        <img src="{{ asset('logo_unsub.png') }}" class="w-full h-auto object-contain" alt="Logo Universitas Subang">
                    @elseif(file_exists(public_path('images/logo_unsub.png')))
                        <img src="{{ asset('images/logo_unsub.png') }}" class="w-full h-auto object-contain" alt="Logo Universitas Subang">
                    @else
                        <span class="text-orange-600 font-extrabold text-lg">UNSUB</span>
                    @endif
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 rounded-full text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                    <svg aria-hidden="true" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Dokumen Resmi Terverifikasi</span>
                </div>

                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight uppercase text-slate-900 dark:text-slate-100">
                    {{ $decree->target_type === 'individual_dosen' ? 'SURAT TUGAS PEMBIMBING SKRIPSI' : 'SK DOSEN PEMBIMBING SKRIPSI' }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium max-w-md mx-auto">
                    Fakultas Ilmu Komputer &mdash; Universitas Subang
                </p>
            </div>
        </div>

        <!-- Body Details -->
        <div class="p-6 sm:p-8 space-y-6">
            <!-- Dokumen Information Box -->
            <div class="bg-orange-50/70 dark:bg-orange-950/30 border border-orange-200 dark:border-orange-800/60 rounded-2xl p-5 space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-orange-800 dark:text-orange-300">
                        {{ $decree->target_type === 'individual_dosen' ? 'Nomor Surat Tugas' : 'Nomor Surat Keputusan' }}
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-orange-600 text-white font-mono font-bold text-xs shadow-xs">
                        {{ $decree->decree_number }}
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-600 dark:text-slate-300 pt-2.5 border-t border-orange-200/70 dark:border-orange-800/60">
                    <div>
                        <span class="text-slate-500 dark:text-slate-400">Tanggal Ditetapkan:</span>
                        <strong class="text-slate-900 dark:text-slate-100 font-bold block mt-0.5">{{ $decree->formatted_decree_date }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 dark:text-slate-400">Tahun Akademik:</span>
                        <strong class="text-slate-900 dark:text-slate-100 font-bold block mt-0.5">{{ $decree->academic_year }} (Semester {{ $decree->semester }})</strong>
                    </div>
                </div>
            </div>

            @if($decree->target_type === 'individual_dosen' && $decree->dosen)
                <!-- Dosen Pembimbing Info (For Surat Tugas) -->
                <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/50 border border-orange-200 dark:border-orange-800/60 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Dosen Pembimbing Ditugaskan</p>
                            <p class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $decree->dosen->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">NIDN: {{ $decree->dosen->identifier ?? '-' }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-50 dark:bg-orange-950/50 text-orange-800 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60">
                        Dosen Tetap
                    </span>
                </div>
            @endif

            <!-- Signatory Info -->
            <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/50 border border-orange-200 dark:border-orange-800/60 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pejabat Pengesah / Penandatangan</p>
                        <p class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $decree->signatory_name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $decree->signatory_title }} {{ $decree->signatory_identifier ? '('.$decree->signatory_identifier.')' : '' }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                    {{ $decree->total_students }} Mahasiswa Disahkan
                </span>
            </div>

            <!-- List of Enrolled Students -->
            <div class="space-y-3">
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Daftar Mahasiswa &amp; Dosen Pembimbing</p>

                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden max-h-96 overflow-y-auto divide-y divide-slate-200/80 dark:divide-slate-800">
                    @foreach($decree->theses_data as $idx => $row)
                        <div class="p-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors text-xs space-y-1.5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $idx + 1 }}. {{ $row['student_name'] ?? '-' }}</span>
                                <span class="font-mono text-slate-600 dark:text-slate-400 font-semibold">{{ $row['student_npm'] ?? '-' }}</span>
                            </div>
                            <p class="text-slate-600 dark:text-slate-300 italic">"{{ $row['title'] ?? '-' }}"</p>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400 pt-1">
                                <span>Pembimbing 1: <strong class="text-slate-800 dark:text-slate-200">{{ $row['pembimbing1_name'] ?? '-' }}</strong></span>
                                <span>Pembimbing 2: <strong class="text-slate-800 dark:text-slate-200">{{ $row['pembimbing2_name'] ?? '-' }}</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer note & login -->
            <div class="text-center pt-2 space-y-2 border-t border-slate-200/80 dark:border-slate-800">
                <p class="text-xs text-slate-500 dark:text-slate-400 pt-3">
                    Sistem Informasi Bimbingan Mahasiswa (SIBIMA) &bull; Fakultas Ilmu Komputer Universitas Subang
                </p>
                <div>
                    <a href="{{ route('login') }}" class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 hover:underline">
                        &larr; Masuk ke Portal SIBIMA FASILKOM
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

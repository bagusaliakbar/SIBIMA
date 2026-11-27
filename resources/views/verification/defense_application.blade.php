@extends('layouts.verification')

@section('title', 'Verifikasi Formulir Pendaftaran Sidang')

@section('content')
    @php
        $student = $application->thesis?->student;
        $thesis = $application->thesis;
        $p1 = $thesis?->pembimbing1;
        $p2 = $thesis?->pembimbing2;
        $submittedAt = $application->statement_agreed_at ?? $application->created_at;

        $statusConfig = match($application->status) {
            'approved' => [
                'label' => 'Disetujui Admin / Kaprodi',
                'class' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
            ],
            'rejected' => [
                'label' => 'Perlu Revisi Berkas',
                'class' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
            ],
            default => [
                'label' => 'Menunggu Validasi Admin',
                'class' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
            ],
        };
    @endphp

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
                    Formulir Pendaftaran Sidang Skripsi
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium">
                    Sistem Informasi Bimbingan Mahasiswa (SIBIMA) &mdash; FASILKOM UNSUB
                </p>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Kode Keaslian -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">ID Dokumen / Kode Keaslian</p>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">Cocokkan kode di samping dengan kode yang tercetak di bagian bawah formulir fisik.</p>
                </div>
                <span class="font-mono text-sm font-extrabold text-slate-900 dark:text-slate-100 bg-white dark:bg-slate-900 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 shrink-0">
                    {{ $integrityCode }}
                </span>
            </div>

            <!-- Informasi Mahasiswa & Pendaftaran -->
            <div class="bg-slate-50/80 dark:bg-slate-800/40 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-700/80">
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">Data Terdaftar di Server SIBIMA</p>

                <div class="space-y-3.5 text-xs sm:text-sm">
                    <div class="flex justify-between items-start gap-4">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Nama Mahasiswa</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 text-right uppercase">{{ $formData['full_name'] ?? $student?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">NPM</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 text-right font-mono">{{ $formData['npm'] ?? $student?->identifier ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Program Studi / Jenjang</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 text-right uppercase">{{ $formData['study_program'] ?? 'SISTEM INFORMASI / S1' }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Semester / Tahun Akademik</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 text-right">Semester {{ $formData['semester_number'] ?? '-' }} / {{ $formData['academic_year'] ?? '-' }}</span>
                    </div>
                    <div class="border-t border-slate-200/80 dark:border-slate-700 pt-3.5">
                        <span class="block font-medium text-slate-500 dark:text-slate-400 mb-1">Judul Skripsi / Tugas Akhir</span>
                        <p class="font-bold text-slate-900 dark:text-slate-100 leading-relaxed uppercase">{{ $formData['thesis_title'] ?? $thesis?->display_title ?? '-' }}</p>
                    </div>
                    <div class="flex justify-between items-center gap-4 border-t border-slate-200/80 dark:border-slate-700 pt-3.5">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Gelombang</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 text-right">{{ $application->wave?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-4">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Status Pendaftaran</span>
                        <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full border {{ $statusConfig['class'] }}">
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Waktu Pengajuan</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 text-right">
                            {{ $submittedAt ? $submittedAt->locale('id')->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Persetujuan Dosen Pembimbing -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 space-y-3">
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Persetujuan Dosen Pembimbing (ACC Sidang)</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-center gap-3 p-3.5 rounded-xl border {{ $thesis?->acc_sidang_p1 ? 'bg-emerald-50/70 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800/60' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700' }}">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $thesis?->acc_sidang_p1 ? 'bg-emerald-600 text-white' : 'bg-slate-300 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $p1?->name ?? ($formData['pembimbing1_name'] ?? '-') }}</p>
                            <p class="text-[11px] font-semibold {{ $thesis?->acc_sidang_p1 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">
                                Pembimbing 1 &bull; {{ $thesis?->acc_sidang_p1 ? 'ACC Sidang Terverifikasi' : 'Belum ACC Digital' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-xl border {{ $thesis?->acc_sidang_p2 ? 'bg-emerald-50/70 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800/60' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700' }}">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $thesis?->acc_sidang_p2 ? 'bg-emerald-600 text-white' : 'bg-slate-300 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $p2?->name ?? ($formData['pembimbing2_name'] ?? '-') }}</p>
                            <p class="text-[11px] font-semibold {{ $thesis?->acc_sidang_p2 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">
                                Pembimbing 2 &bull; {{ $thesis?->acc_sidang_p2 ? 'ACC Sidang Terverifikasi' : 'Belum ACC Digital' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center space-y-1.5">
                <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                    * Apabila terdapat perbedaan antara data pada cetakan fisik dengan data di atas, maka data pada server SIBIMA merupakan rujukan yang sah.
                </p>
                <p class="text-[11px] font-mono font-semibold text-slate-500 dark:text-slate-400 tracking-wider">
                    Token: {{ substr($application->verification_token, 0, 8) }}...{{ substr($application->verification_token, -8) }}
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

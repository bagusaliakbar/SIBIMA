<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Yudisium & SKL', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6" x-data="{
        activeTab: '{{ $status ?? 'all' }}',
        modalOpen: false,
        activeGraduation: null,
        rejectionOpen: false,
        openVerificationModal(data) {
            this.activeGraduation = data;
            this.rejectionOpen = false;
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
            this.activeGraduation = null;
        }
    }">

        <!-- KPI / Metric Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Pengajuan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Pengajuan</p>
                    <p class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center text-slate-600 dark:text-slate-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
            </div>

            <!-- Menunggu Verifikasi -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-amber-200/80 dark:border-amber-800/40 shadow-xs flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Menunggu Verifikasi</p>
                    <p class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- SKL Diterbitkan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-emerald-200/80 dark:border-emerald-800/40 shadow-xs flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">SKL Terbit (Lulus)</p>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Perlu Perbaikan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-rose-200/80 dark:border-rose-800/40 shadow-xs flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Perlu Perbaikan</p>
                    <p class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400">{{ $stats['rejected'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 flex items-center justify-center text-rose-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Filter Status Tabs -->
        <div class="flex items-center gap-1 border-b border-slate-200 dark:border-slate-800 overflow-x-auto pb-px custom-scrollbar">
            <a href="{{ route('graduations.index', ['status' => 'all', 'search' => $search]) }}" 
               class="px-5 py-3 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'all' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span>Semua Pengajuan</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? 'all') === 'all' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('graduations.index', ['status' => 'pending', 'search' => $search]) }}" 
               class="px-5 py-3 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'pending' ? 'border-amber-500 text-amber-600 bg-amber-50/50 dark:bg-amber-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span>Menunggu Verifikasi</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? 'all') === 'pending' ? 'bg-amber-500 text-white' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' }}">{{ $stats['pending'] }}</span>
            </a>
            <a href="{{ route('graduations.index', ['status' => 'approved', 'search' => $search]) }}" 
               class="px-5 py-3 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'approved' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/50 dark:bg-emerald-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span>SKL Terbit (Disetujui)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? 'all') === 'approved' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' }}">{{ $stats['approved'] }}</span>
            </a>
            <a href="{{ route('graduations.index', ['status' => 'rejected', 'search' => $search]) }}" 
               class="px-5 py-3 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'rejected' ? 'border-rose-500 text-rose-600 bg-rose-50/50 dark:bg-rose-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span>Perlu Perbaikan</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? 'all') === 'rejected' ? 'bg-rose-500 text-white' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' }}">{{ $stats['rejected'] }}</span>
            </a>
        </div>

        <!-- Table Card -->
        <x-table-card title="Daftar Bebas Tanggungan & SKL Mahasiswa" :footer="$graduations->links()">
            <x-slot name="headerActions">
                <div class="w-full sm:w-72">
                    <form action="{{ route('graduations.index') }}" method="GET" class="relative">
                        <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, NPM, judul, No SKL..." 
                                   class="w-full pl-9 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                        </div>
                    </form>
                </div>
            </x-slot>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/60 dark:bg-slate-800/60 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Mahasiswa</th>
                        <th class="py-3.5 px-4">Judul Skripsi</th>
                        <th class="py-3.5 px-4 text-center">Checklist Bebas Tanggungan</th>
                        <th class="py-3.5 px-4 text-center">Status & SKL</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($graduations as $index => $item)
                        @php
                            $student = $item->student;
                            $thesis = $item->thesis;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <!-- No -->
                            <td class="py-4 px-4 text-center font-bold text-slate-400">
                                {{ $graduations->firstItem() + $index }}
                            </td>

                            <!-- Mahasiswa -->
                            <td class="py-4 px-4 min-w-[200px]">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-950/50 border border-orange-200 dark:border-orange-800/40 text-orange-600 dark:text-orange-400 font-black flex items-center justify-center text-xs shrink-0">
                                        {{ substr($student->name ?? 'M', 0, 2) }}
                                    </div>
                                    <div class="space-y-0.5">
                                        <p class="font-bold text-slate-800 dark:text-slate-100 leading-snug">{{ $student->name ?? 'Mahasiswa' }}</p>
                                        <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                                            <span class="font-mono">{{ $student->identifier ?? '-' }}</span>
                                            @if($student->phone_number)
                                                <span>•</span>
                                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $student->phone_number)) }}" target="_blank" class="text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-0.5 font-medium" title="Kirim Pesan WhatsApp">
                                                    <span>WA</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Judul Skripsi -->
                            <td class="py-4 px-4 max-w-xs">
                                <p class="font-semibold text-slate-800 dark:text-slate-200 line-clamp-2 leading-relaxed" title="{{ $thesis->final_title ?? $thesis->title ?? '-' }}">
                                    {{ $thesis->final_title ?? $thesis->title ?? '-' }}
                                </p>
                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                    @if($item->final_thesis_file)
                                        <a href="{{ route('download.private', ['path' => $item->final_thesis_file]) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-orange-600 text-[10px] font-bold" title="Unduh Skripsi Final PDF">
                                            <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>
                                            <span>Skripsi</span>
                                        </a>
                                    @endif
                                    @if($item->journal_article_file)
                                        <a href="{{ route('download.private', ['path' => $item->journal_article_file]) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-orange-600 text-[10px] font-bold" title="Unduh Jurnal PDF">
                                            <svg class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>
                                            <span>Jurnal</span>
                                        </a>
                                    @endif
                                    @if($item->plagiarism_file)
                                        <a href="{{ route('download.private', ['path' => $item->plagiarism_file]) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-orange-600 text-[10px] font-bold" title="Unduh Bukti Cek Plagiasi">
                                            <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            <span>Turnitin</span>
                                        </a>
                                    @endif
                                    @if($item->publication_link)
                                        <a href="{{ $item->publication_link }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 text-[10px] font-bold hover:underline" title="Tautan Publikasi Jurnal">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            <span>Link Jurnal</span>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- Checklist Bebas Tanggungan -->
                            <td class="py-4 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-xl">
                                    <!-- Hardcover -->
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold {{ $item->hardcover_collected ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}" title="Hardcover Skripsi: {{ $item->hardcover_collected ? 'Sudah Diserahkan' : 'Belum Diserahkan' }}">
                                        HC
                                    </span>
                                    <!-- Lab -->
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold {{ $item->lab_clearance ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}" title="Bebas Lab: {{ $item->lab_clearance ? 'Bebas Tanggungan' : 'Belum Bebas' }}">
                                        LAB
                                    </span>
                                    <!-- Pustaka -->
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold {{ $item->library_clearance ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}" title="Bebas Pustaka: {{ $item->library_clearance ? 'Bebas Tanggungan' : 'Belum Bebas' }}">
                                        PUS
                                    </span>
                                    <!-- CD/Repo -->
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold {{ $item->cd_or_repository_collected ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}" title="CD & Repositori: {{ $item->cd_or_repository_collected ? 'Sudah Diserahkan' : 'Belum Diserahkan' }}">
                                        CD
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-semibold">
                                    {{ $item->clearance_percentage }}% Lengkap
                                </p>
                            </td>

                            <!-- Status & SKL -->
                            <td class="py-4 px-4 text-center">
                                @if($item->status === 'approved')
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            SKL Diterbitkan
                                        </span>
                                        <p class="font-mono text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                            {{ $item->skl_number }}
                                        </p>
                                    </div>
                                @elseif($item->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800" title="{{ $item->rejection_reason }}">
                                        Perlu Perbaikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 animate-pulse">
                                        Menunggu Verifikasi
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    @if($item->status === 'approved')
                                        <a href="{{ route('graduations.download-skl', $item) }}" target="_blank" class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all shadow-2xs" title="Cetak / Unduh SKL (PDF)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                    @endif

                                    <button type="button" 
                                            @click="openVerificationModal({{ json_encode([
                                                'id' => $item->id,
                                                'student_name' => $student->name ?? '',
                                                'student_npm' => $student->identifier ?? '',
                                                'thesis_title' => $thesis->final_title ?? $thesis->title ?? '',
                                                'status' => $item->status,
                                                'hardcover_collected' => (bool)$item->hardcover_collected,
                                                'library_clearance' => (bool)$item->library_clearance,
                                                'lab_clearance' => (bool)$item->lab_clearance,
                                                'cd_or_repository_collected' => (bool)$item->cd_or_repository_collected,
                                                'skl_number' => $item->skl_number ?? '',
                                                'graduation_date' => $item->graduation_date ? $item->graduation_date->format('Y-m-d') : date('Y-m-d'),
                                                'gpa' => $item->gpa ? number_format($item->gpa, 2, '.', '') : '3.50',
                                                'predicate' => $item->predicate ?? 'Dengan Pujian (Cum Laude)',
                                                'rejection_reason' => $item->rejection_reason ?? '',
                                                'final_thesis_file' => $item->final_thesis_file ? route('download.private', ['path' => $item->final_thesis_file]) : null,
                                                'journal_article_file' => $item->journal_article_file ? route('download.private', ['path' => $item->journal_article_file]) : null,
                                                'plagiarism_file' => $item->plagiarism_file ? route('download.private', ['path' => $item->plagiarism_file]) : null,
                                                'publication_link' => $item->publication_link ?? null,
                                                'student_notes' => $item->student_notes ?? '',
                                                'verify_url' => route('graduations.verify', $item)
                                            ]) }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs shadow-xs transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                        <span>Verifikasi</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="font-bold text-slate-700 dark:text-slate-300">Tidak ada pengajuan ditemukan</p>
                                    <p class="text-xs">Belum ada mahasiswa yang mengajukan bebas tanggungan pada kategori ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-card>

        <!-- Verification Modal -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                     @click="closeModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="modalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block w-full max-w-3xl my-8 text-left align-middle transition-all transform bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    
                    <template x-if="activeGraduation">
                        <div>
                            <!-- Header Modal -->
                            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black text-slate-800 dark:text-slate-100" id="modal-title">
                                            Verifikasi Pra-Yudisium & SKL
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                            <span x-text="activeGraduation.student_name"></span> (<span x-text="activeGraduation.student_npm"></span>)
                                        </p>
                                    </div>
                                </div>
                                <button type="button" @click="closeModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-500 hover:text-slate-700 dark:hover:text-white flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
                                <!-- Student Thesis Title Box -->
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Judul Skripsi Disahkan</p>
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-relaxed" x-text="activeGraduation.thesis_title"></p>
                                </div>

                                <!-- Berkas Digital yang Diunggah Mahasiswa -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Berkas Digital Mahasiswa</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <!-- Skripsi Final -->
                                        <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-950/40 text-red-500 flex items-center justify-center font-bold text-xs">PDF</div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Naskah Skripsi Final</p>
                                                    <p class="text-[10px] text-slate-400">Format PDF Lengkap</p>
                                                </div>
                                            </div>
                                            <template x-if="activeGraduation.final_thesis_file">
                                                <a :href="activeGraduation.final_thesis_file" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-orange-50 text-slate-700 dark:text-slate-200 hover:text-orange-600 text-xs font-bold transition-all">
                                                    Lihat
                                                </a>
                                            </template>
                                            <template x-if="!activeGraduation.final_thesis_file">
                                                <span class="text-[10px] text-amber-500 font-semibold">Belum Unggah</span>
                                            </template>
                                        </div>

                                        <!-- Jurnal Final -->
                                        <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-500 flex items-center justify-center font-bold text-xs">PDF</div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Artikel Jurnal Ilmiah</p>
                                                    <p class="text-[10px] text-slate-400">Format Jurnal Nasional</p>
                                                </div>
                                            </div>
                                            <template x-if="activeGraduation.journal_article_file">
                                                <a :href="activeGraduation.journal_article_file" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-orange-50 text-slate-700 dark:text-slate-200 hover:text-orange-600 text-xs font-bold transition-all">
                                                    Lihat
                                                </a>
                                            </template>
                                            <template x-if="!activeGraduation.journal_article_file">
                                                <span class="text-[10px] text-slate-400">Opsional/Belum</span>
                                            </template>
                                        </div>

                                        <!-- Bukti Turnitin -->
                                        <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 flex items-center justify-center font-bold text-xs">PDF</div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Bukti Cek Plagiasi</p>
                                                    <p class="text-[10px] text-slate-400">Similarity Report</p>
                                                </div>
                                            </div>
                                            <template x-if="activeGraduation.plagiarism_file">
                                                <a :href="activeGraduation.plagiarism_file" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-orange-50 text-slate-700 dark:text-slate-200 hover:text-orange-600 text-xs font-bold transition-all">
                                                    Lihat
                                                </a>
                                            </template>
                                            <template x-if="!activeGraduation.plagiarism_file">
                                                <span class="text-[10px] text-slate-400">Opsional/Belum</span>
                                            </template>
                                        </div>

                                        <!-- Link Jurnal -->
                                        <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-500 flex items-center justify-center font-bold text-xs">URL</div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Tautan OJS / Jurnal</p>
                                                    <p class="text-[10px] text-slate-400">Link Publikasi Online</p>
                                                </div>
                                            </div>
                                            <template x-if="activeGraduation.publication_link">
                                                <a :href="activeGraduation.publication_link" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-300 hover:bg-purple-100 text-xs font-bold transition-all">
                                                    Buka
                                                </a>
                                            </template>
                                            <template x-if="!activeGraduation.publication_link">
                                                <span class="text-[10px] text-slate-400">Tidak Ada</span>
                                            </template>
                                        </div>
                                    </div>

                                    <template x-if="activeGraduation.student_notes">
                                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300">
                                            <strong class="font-bold">Catatan Mahasiswa:</strong> <span x-text="activeGraduation.student_notes"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Form Checklist & Verifikasi -->
                                <form :action="activeGraduation.verify_url" method="POST" class="space-y-6">
                                    @csrf

                                    <!-- Section Checklist Fisik & Bebas Tanggungan -->
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                                Checklist Bebas Tanggungan
                                            </h4>
                                            <span class="text-[11px] text-slate-400 font-medium">Beri centang jika sudah diserahkan / lunas</span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <!-- Hardcover Skripsi -->
                                            <label class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 flex items-start gap-3 cursor-pointer transition-colors">
                                                <input type="checkbox" name="hardcover_collected" value="1" :checked="activeGraduation.hardcover_collected" 
                                                       class="w-4 h-4 mt-0.5 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                                <div class="space-y-0.5">
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Naskah Hardcover Skripsi</p>
                                                    <p class="text-[11px] text-slate-400">Hardcover asli lengkap tanda tangan pengesahan (3-4 exp)</p>
                                                </div>
                                            </label>

                                            <!-- Bebas Perpustakaan -->
                                            <label class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 flex items-start gap-3 cursor-pointer transition-colors">
                                                <input type="checkbox" name="library_clearance" value="1" :checked="activeGraduation.library_clearance" 
                                                       class="w-4 h-4 mt-0.5 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                                <div class="space-y-0.5">
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Bebas Pustaka</p>
                                                    <p class="text-[11px] text-slate-400">Bebas pinjaman buku & denda perpustakaan</p>
                                                </div>
                                            </label>

                                            <!-- Bebas Laboratorium -->
                                            <label class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 flex items-start gap-3 cursor-pointer transition-colors">
                                                <input type="checkbox" name="lab_clearance" value="1" :checked="activeGraduation.lab_clearance" 
                                                       class="w-4 h-4 mt-0.5 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                                <div class="space-y-0.5">
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Bebas Laboratorium</p>
                                                    <p class="text-[11px] text-slate-400">Bebas pinjaman alat/komponen laboratorium komputer</p>
                                                </div>
                                            </label>

                                            <!-- CD Skripsi / Repository -->
                                            <label class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 flex items-start gap-3 cursor-pointer transition-colors">
                                                <input type="checkbox" name="cd_or_repository_collected" value="1" :checked="activeGraduation.cd_or_repository_collected" 
                                                       class="w-4 h-4 mt-0.5 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                                <div class="space-y-0.5">
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Penyerahan CD & Repositori</p>
                                                    <p class="text-[11px] text-slate-400">CD softcopy skripsi, source code, dan upload repositori</p>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="flex justify-end">
                                            <button type="submit" name="action" value="update_clearance" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Simpan Checklist Saja</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Section Parameter Penerbitan SKL Digital -->
                                    <div class="p-5 rounded-2xl bg-orange-50/50 dark:bg-orange-950/20 border border-orange-200/80 dark:border-orange-800/40 space-y-4">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                            <h4 class="text-xs font-black uppercase tracking-wider text-orange-900 dark:text-orange-300">
                                                Parameter Surat Keterangan Lulus (SKL)
                                            </h4>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <!-- Tanggal Kelulusan -->
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                                                    Tanggal Kelulusan / SKL <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="date" name="graduation_date" :value="activeGraduation.graduation_date" required
                                                       class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                            </div>

                                            <!-- IPK Terakhir -->
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                                                    Indeks Prestasi Kumulatif (IPK) <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="number" step="0.01" min="2.00" max="4.00" name="gpa" :value="activeGraduation.gpa" required
                                                       placeholder="Contoh: 3.65"
                                                       class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                            </div>

                                            <!-- Predikat Kelulusan -->
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                                                    Predikat Kelulusan <span class="text-rose-500">*</span>
                                                </label>
                                                <select name="predicate" required
                                                        class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                                    <option value="Dengan Pujian (Cum Laude)" :selected="activeGraduation.predicate === 'Dengan Pujian (Cum Laude)'">Dengan Pujian (Cum Laude)</option>
                                                    <option value="Sangat Memuaskan" :selected="activeGraduation.predicate === 'Sangat Memuaskan'">Sangat Memuaskan</option>
                                                    <option value="Memuaskan" :selected="activeGraduation.predicate === 'Memuaskan'">Memuaskan</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Nomor SKL (Opsional / Override) -->
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                                                Nomor Surat Keterangan Lulus (SKL)
                                                <span class="text-[10px] font-normal text-slate-400 dark:text-slate-500">(Kosongkan untuk penomoran otomatis berurutan dari sistem)</span>
                                            </label>
                                            <input type="text" name="skl_number" :value="activeGraduation.skl_number" placeholder="Contoh: 014/SKL/UNSUB/FIK/IX/2026"
                                                   class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-mono font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                        </div>

                                        <!-- Tombol Setujui & Terbitkan SKL -->
                                        <div class="pt-2">
                                            <button type="submit" name="action" value="approve" 
                                                    class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>Setujui Bebas Tanggungan & Terbitkan SKL Digital Resmi</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Section Tolak / Minta Perbaikan (Accordion) -->
                                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                                        <button type="button" @click="rejectionOpen = !rejectionOpen" class="flex items-center justify-between w-full text-left text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline">
                                            <span>Minta Perbaikan / Tolak Berkas Bebas Tanggungan</span>
                                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': rejectionOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>

                                        <div x-show="rejectionOpen" x-cloak class="mt-3 space-y-3 p-4 rounded-2xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200/80 dark:border-rose-800/40">
                                            <div>
                                                <label class="block text-[11px] font-bold text-rose-900 dark:text-rose-300 mb-1">
                                                    Alasan Penolakan / Catatan Perbaikan <span class="text-rose-500">*</span>
                                                </label>
                                                <textarea name="rejection_reason" rows="3" placeholder="Sebutkan berkas yang kurang atau perbaikan yang harus dilakukan mahasiswa..."
                                                          class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-rose-300 dark:border-rose-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"></textarea>
                                            </div>
                                            <button type="submit" name="action" value="reject" 
                                                    class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                <span>Kirim Perbaikan ke Mahasiswa</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

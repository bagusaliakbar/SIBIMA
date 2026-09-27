<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Dokumen & Legalitas', 'route' => null],
                ['label' => 'SK Dosen Pembimbing Skripsi', 'route' => null]
            ]" />

            <div class="flex items-center gap-3">
                @if($isStaff)
                    <a href="{{ route('admin.letter-settings.index') }}" 
                       class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                        <span>Format Penomoran</span>
                    </a>
                @endif
                <a href="{{ route('advisor-decrees.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-black uppercase tracking-wider transition shadow-lg shadow-orange-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Terbitkan SK / Surat Tugas</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- KPI / Metric Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Dokumen -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Arsip Dokumen</p>
                    <p class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center text-slate-600 dark:text-slate-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>

            <!-- SK Kolektif Prodi -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-emerald-200/80 dark:border-emerald-800/40 shadow-2xs flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">SK Kolektif Prodi</p>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['collective'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>

            <!-- Surat Tugas BKD Dosen -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-blue-200/80 dark:border-blue-800/40 shadow-2xs flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Surat Tugas BKD</p>
                    <p class="text-2xl sm:text-3xl font-black text-blue-600 dark:text-blue-400">{{ $stats['individual'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
            </div>

            <!-- Mahasiswa Terlampir -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-orange-200/80 dark:border-orange-800/40 shadow-2xs flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-orange-600 dark:text-orange-400">Mahasiswa Terlampir</p>
                    <p class="text-2xl sm:text-3xl font-black text-orange-600 dark:text-orange-400">{{ $stats['total_students'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center text-orange-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Filter Status / Category Tabs -->
        <div class="flex items-center gap-1 border-b border-slate-200 dark:border-slate-800 overflow-x-auto pb-px custom-scrollbar">
            <a href="{{ route('advisor-decrees.index', array_merge(request()->except('target_type'), ['target_type' => 'all'])) }}" 
               class="px-5 py-3 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($targetType ?? 'all') === 'all' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span>Semua Dokumen</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($targetType ?? 'all') === 'all' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('advisor-decrees.index', array_merge(request()->except('target_type'), ['target_type' => 'collective'])) }}" 
               class="px-5 py-3 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($targetType ?? 'all') === 'collective' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/50 dark:bg-emerald-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span>SK Kolektif Prodi</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($targetType ?? 'all') === 'collective' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' }}">{{ $stats['collective'] }}</span>
            </a>
            <a href="{{ route('advisor-decrees.index', array_merge(request()->except('target_type'), ['target_type' => 'individual_dosen'])) }}" 
               class="px-5 py-3 border-b-2 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0 {{ ($targetType ?? 'all') === 'individual_dosen' ? 'border-blue-500 text-blue-600 bg-blue-50/50 dark:bg-blue-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span>Surat Tugas Dosen (BKD)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($targetType ?? 'all') === 'individual_dosen' ? 'bg-blue-500 text-white' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300' }}">{{ $stats['individual'] }}</span>
            </a>
        </div>

        <!-- Table Card -->
        <x-table-card 
            title="Daftar SK & Surat Tugas Pembimbing Skripsi" 
            subtitle="Dokumen penetapan resmi ber-QR Code untuk administrasi prodi dan pelaporan BKD dosen"
            :footer="$decrees->links()">
            
            <x-slot name="headerActions">
                <form method="GET" action="{{ route('advisor-decrees.index') }}" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                    <input type="hidden" name="target_type" value="{{ $targetType ?? 'all' }}">

                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari nomor SK, nama, judul..." 
                               class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 pl-8 focus:ring-orange-500 focus:border-orange-500 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <!-- Filter Academic Year -->
                    <select name="academic_year" onchange="this.form.submit()" 
                            class="text-xs rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500 transition shadow-2xs">
                        <option value="all">Semua T.A</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Semester -->
                    <select name="semester" onchange="this.form.submit()" 
                            class="text-xs rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500 transition shadow-2xs">
                        <option value="all">Semua Semester</option>
                        <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>

                    <button type="submit" 
                            class="p-2 bg-slate-900 hover:bg-slate-800 active:scale-95 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer"
                            title="Terapkan Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    </button>

                    @if(request()->hasAny(['search', 'academic_year', 'semester']) && (request('search') || (request('academic_year') && request('academic_year') !== 'all') || (request('semester') && request('semester') !== 'all')))
                        <a href="{{ route('advisor-decrees.index', ['target_type' => $targetType ?? 'all']) }}" 
                           class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition shadow-xs"
                           title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </form>
            </x-slot>

            @if($decrees->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="px-5 py-4">Nomor SK & Jenis</th>
                                <th class="px-5 py-4">Perihal & Penandatangan</th>
                                <th class="px-5 py-4 text-center">Tahun Akademik</th>
                                <th class="px-5 py-4 text-center">Mahasiswa Terlampir</th>
                                <th class="px-5 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @foreach($decrees as $decree)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-black text-slate-900 dark:text-white text-xs tracking-tight">
                                            {{ $decree->decree_number }}
                                        </div>
                                        <div class="mt-1 flex items-center gap-1.5">
                                            @if($decree->target_type === 'individual_dosen' && $decree->dosen)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200 dark:border-blue-900">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    Surat Tugas: {{ Str::limit($decree->dosen->name, 22) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    SK Kolektif Prodi
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <a href="{{ route('advisor-decrees.show', $decree) }}" 
                                           class="font-bold text-slate-900 dark:text-white hover:text-orange-600 transition block line-clamp-1">
                                            {{ $decree->title }}
                                        </a>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5">
                                            <span>📅 {{ $decree->formatted_decree_date }}</span>
                                            <span>✍️ {{ $decree->signatory_name }}</span>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                            {{ $decree->academic_year }} ({{ $decree->semester }})
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-orange-50 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300 border border-orange-200 dark:border-orange-800">
                                            <span>🎓</span>
                                            <span>{{ $decree->total_students }} Mahasiswa</span>
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('advisor-decrees.show', $decree) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                <span>Detail</span>
                                            </a>

                                            <a href="{{ route('advisor-decrees.pdf', $decree) }}" target="_blank"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 active:scale-95 text-white transition shadow-sm shadow-orange-600/20">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <span>Cetak PDF</span>
                                            </a>

                                            @if($isStaff)
                                                <form action="{{ route('advisor-decrees.destroy', $decree) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip SK Pembimbing ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/30 transition cursor-pointer" title="Hapus Arsip">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-16 text-center w-full">
                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                        <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Belum Ada SK Pembimbing yang Diterbitkan</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest font-black">
                        Arsip Surat Keputusan Dosen Pembimbing Skripsi akan muncul di sini setelah diterbitkan.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('advisor-decrees.create') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-black uppercase tracking-wider transition shadow-lg shadow-orange-600/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            <span>Terbitkan SK / Surat Tugas Sekarang</span>
                        </a>
                    </div>
                </div>
            @endif
        </x-table-card>
    </div>
</x-app-layout>

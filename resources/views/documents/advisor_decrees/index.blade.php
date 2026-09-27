<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-black tracking-wider uppercase bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400 border border-orange-200 dark:border-orange-800">
                        Dokumen Legalitas & Penugasan
                    </span>
                    <span class="text-xs text-slate-400">#BKD-SISTER</span>
                </div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                    SK Dosen Pembimbing Skripsi
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Arsip Surat Keputusan dan Surat Tugas Pembimbing Skripsi resmi ber-QR Code untuk administrasi prodi dan pelaporan BKD dosen.
                </p>
            </div>

            @if($isStaff)
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.letter-settings.index') }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition shadow-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    <span>Format Nomor Surat</span>
                </a>
                <a href="{{ route('advisor-decrees.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-black uppercase tracking-wider transition shadow-lg shadow-orange-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Terbitkan SK Baru</span>
                </a>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Filter & Search Bar -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <form method="GET" action="{{ route('advisor-decrees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pencarian</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari nomor SK, perihal, penandatangan..." 
                               class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 pl-9 focus:ring-orange-500 focus:border-orange-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tahun Akademik</label>
                    <select name="academic_year" class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-orange-500 focus:border-orange-500">
                        <option value="all">Semua Tahun Akademik</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Semester</label>
                    <select name="semester" class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-orange-500 focus:border-orange-500">
                        <option value="all">Semua Semester</option>
                        <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['search', 'academic_year', 'semester', 'target_type']))
                        <a href="{{ route('advisor-decrees.index') }}" 
                           class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition text-center"
                           title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Decrees List -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            @if($decrees->count() > 0)
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($decrees as $decree)
                        <div class="p-6 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                            <div class="space-y-2 max-w-3xl">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-black bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400 border border-orange-200 dark:border-orange-900">
                                        {{ $decree->decree_number }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        T.A {{ $decree->academic_year }} ({{ $decree->semester }})
                                    </span>
                                    @if($decree->target_type === 'individual_dosen' && $decree->dosen)
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200 dark:border-blue-900">
                                            Surat Tugas: {{ $decree->dosen->name }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                            Kolektif Seluruh Mahasiswa
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <h3 class="text-base font-black text-slate-900 dark:text-white leading-snug">
                                        <a href="{{ route('advisor-decrees.show', $decree) }}" class="hover:text-orange-600 transition">
                                            {{ $decree->title }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                                        <span>📅 Ditetapkan: <strong class="text-slate-700 dark:text-slate-200">{{ $decree->formatted_decree_date }}</strong></span>
                                        <span>✍️ Penandatangan: <strong class="text-slate-700 dark:text-slate-200">{{ $decree->signatory_name }}</strong> ({{ $decree->signatory_title }})</span>
                                        <span>🎓 Mahasiswa Tercantum: <strong class="text-orange-600 font-black">{{ $decree->total_students }} Orang</strong></span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('advisor-decrees.show', $decree) }}" 
                                   class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Pratinjau</span>
                                </a>

                                <a href="{{ route('advisor-decrees.pdf', $decree) }}" target="_blank"
                                   class="px-4 py-2 rounded-xl text-xs font-black bg-orange-600 hover:bg-orange-700 text-white transition shadow-sm flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>Cetak PDF</span>
                                </a>

                                @if($isStaff)
                                    <form action="{{ route('advisor-decrees.destroy', $decree) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip SK Pembimbing ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/30 transition" title="Hapus Arsip">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($decrees->hasPages())
                    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $decrees->links() }}
                    </div>
                @endif
            @else
                <div class="p-16 text-center space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-3xl bg-orange-100 dark:bg-orange-950/60 flex items-center justify-center text-orange-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-base font-black text-slate-800 dark:text-slate-200">Belum Ada SK Pembimbing yang Diterbitkan</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1">
                            Arsip Surat Keputusan Dosen Pembimbing Skripsi akan muncul di sini setelah diterbitkan secara resmi oleh Kaprodi atau Dekan.
                        </p>
                    </div>
                    @if($isStaff)
                        <div>
                            <a href="{{ route('advisor-decrees.create') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition shadow-lg shadow-orange-600/30">
                                🚀 Terbitkan SK Pembimbing Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

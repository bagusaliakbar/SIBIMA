<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-1 font-semibold">
                    <a href="{{ route('theses.index') }}" class="hover:text-orange-600 transition-colors">Pengajuan Skripsi</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">Belum Mengajukan Judul</span>
                </div>
                <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-500/10 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span>Monitoring Mahasiswa Belum Mengajukan Judul</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Daftar mahasiswa yang telah membuat akun di sistem SIBIMA namun belum memiliki draf pengajuan skripsi.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('theses.index') }}" 
                   class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all shadow-xs">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Daftar Skripsi
                </a>
                <a href="{{ route('theses.unsubmitted-students.export-excel', request()->query()) }}" 
                   class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-500/20">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Ekspor Excel
                </a>
                <a href="{{ route('theses.unsubmitted-students.export-pdf', request()->query()) }}" 
                   class="inline-flex items-center px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md shadow-rose-500/20">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Ekspor PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Unsubmitted -->
            <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/60 shadow-xs relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Total Belum Mengajukan</p>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $totalUnsubmitted }}</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">Mahasiswa terdaftar aktif</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-orange-500/10 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Card 2: Semester Kritis -->
            <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/60 shadow-xs relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-rose-500 dark:text-rose-400 uppercase tracking-widest">Semester Kritis (≥13)</p>
                        <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $criticalCount }}</h3>
                        <p class="text-[11px] text-rose-500/80 dark:text-rose-400/80 mt-1 font-medium">Potensi masa studi habis</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Card 3: Semester Perhatian -->
            <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/60 shadow-xs relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-amber-500 dark:text-amber-400 uppercase tracking-widest">Perhatian (Sem 7–12)</p>
                        <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $warningCount }}</h3>
                        <p class="text-[11px] text-amber-600/80 dark:text-amber-400/80 mt-1 font-medium">Wajib segera mengajukan</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Card 4: Distribusi Angkatan -->
            <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/60 shadow-xs relative overflow-hidden group">
                <div>
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Sebaran Angkatan Terbanyak</p>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($yearDistribution->take(4) as $year => $count)
                            <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700/60 rounded-lg text-[10px] font-black text-slate-700 dark:text-slate-300">
                                '{{ substr($year, -2) }}: <span class="text-orange-600 dark:text-orange-400">{{ $count }}</span>
                            </span>
                        @empty
                            <span class="text-xs text-slate-400">Tidak ada data</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <!-- Filter Bar -->
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/20">
                <form action="{{ route('theses.unsubmitted-students') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search Input -->
                    <div class="relative flex items-center">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari nama, NPM, email..." 
                               class="w-full pl-10 pr-4 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium">
                    </div>

                    <!-- Angkatan Filter -->
                    <div>
                        <select name="entry_year" 
                                onchange="this.form.submit()" 
                                class="w-full py-2 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-semibold">
                            <option value="">Semua Angkatan</option>
                            @foreach($entryYears as $year)
                                <option value="{{ $year }}" {{ $entryYear == $year ? 'selected' : '' }}>Angkatan {{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester Status Filter -->
                    <div>
                        <select name="semester_filter" 
                                onchange="this.form.submit()" 
                                class="w-full py-2 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-semibold">
                            <option value="" {{ empty($semesterFilter) ? 'selected' : '' }}>Semua Status Semester</option>
                            <option value="critical" {{ $semesterFilter === 'critical' ? 'selected' : '' }}>🔴 Semester Kritis (≥ 13)</option>
                            <option value="warning" {{ $semesterFilter === 'warning' ? 'selected' : '' }}>🟡 Semester Perhatian (Sem 7–12)</option>
                            <option value="normal" {{ $semesterFilter === 'normal' ? 'selected' : '' }}>🟢 Semester Normal (&lt; 7)</option>
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-sm">
                            Filter
                        </button>
                        @if($search || $entryYear || $semesterFilter)
                            <a href="{{ route('theses.unsubmitted-students') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-colors" title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-900/60 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                            <th class="py-4 px-6">Mahasiswa</th>
                            <th class="py-4 px-6">NPM & Angkatan</th>
                            <th class="py-4 px-6 text-center">Semester</th>
                            <th class="py-4 px-6">Terdaftar Sejak</th>
                            <th class="py-4 px-6 text-center">Status Akun</th>
                            <th class="py-4 px-6 text-right">Tindakan / Kontak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($students as $student)
                            @php
                                $semester = $student->current_semester;
                                $isCritical = $student->is_critical_semester;
                                $isWarning = $semester >= 7 && !$isCritical;
                                $daysSinceCreation = $student->created_at ? (int) $student->created_at->diffInDays(now()) : 0;
                                $formattedPhone = $student->phone_number ? \App\Helpers\PhoneHelper::formatForWhatsApp($student->phone_number) : null;
                                
                                $waMessage = "Halo {$student->name} (NPM: {$student->identifier}), kami dari Program Studi menginformasikan bahwa akun SIBIMA Anda telah aktif sejak " . ($student->created_at ? $student->created_at->translatedFormat('d M Y') : 'beberapa waktu lalu') . ", namun hingga saat ini Anda belum mengajukan judul skripsi. Mohon segera melengkapi pengajuan judul skripsi Anda di SIBIMA: " . route('theses.create');
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group">
                                <!-- Student Info -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3.5">
                                        <div class="relative w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0 shadow-2xs">
                                            <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-slate-100 text-sm group-hover:text-orange-600 transition-colors">
                                                {{ $student->name }}
                                            </div>
                                            <div class="text-xs text-slate-400 font-medium flex items-center gap-1.5 mt-0.5">
                                                <span>{{ $student->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Identifier & Entry Year -->
                                <td class="py-4 px-6">
                                    <div class="space-y-1">
                                        <div class="font-mono text-xs font-black text-slate-700 dark:text-slate-300">
                                            {{ $student->identifier ?? '-' }}
                                        </div>
                                        @if($student->entry_year)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                                Angkatan {{ $student->entry_year }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Semester -->
                                <td class="py-4 px-6 text-center">
                                    @if($semester)
                                        @if($isCritical)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                Semester {{ $semester }} (Kritis)
                                            </span>
                                        @elseif($isWarning)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                                Semester {{ $semester }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                Semester {{ $semester }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">-</span>
                                    @endif
                                </td>

                                <!-- Registration Date & Duration -->
                                <td class="py-4 px-6">
                                    <div>
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                            {{ $student->created_at ? $student->created_at->translatedFormat('d M Y') : '-' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-medium mt-0.5">
                                            {{ $daysSinceCreation }} hari yang lalu
                                        </div>
                                    </div>
                                </td>

                                <!-- Status Akun -->
                                <td class="py-4 px-6 text-center">
                                    @if($student->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <!-- Action / Contact -->
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($formattedPhone)
                                            <a href="https://wa.me/{{ $formattedPhone }}?text={{ urlencode($waMessage) }}" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold tracking-wide transition-all shadow-xs hover:scale-105 active:scale-95"
                                               title="Kirim Pesan Pengingat WhatsApp">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                <span>Ingatkan WA</span>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 text-slate-400 rounded-lg text-[10px] font-semibold" title="Nomor WhatsApp belum terdaftar di profil mahasiswa">
                                                No WA Kosong
                                            </span>
                                        @endif

                                        <a href="{{ route('chat.show', $student->id) }}" 
                                           class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 rounded-xl transition-colors" 
                                           title="Chat Internal SIBIMA">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        </a>

                                        <form action="{{ route('users.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun mahasiswa {{ addslashes($student->name) }} ({{ $student->identifier }})?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 rounded-xl transition-colors cursor-pointer" title="Hapus Akun Mahasiswa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="w-16 h-16 mx-auto bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-800 dark:text-white">Semua Mahasiswa Sudah Mengajukan!</h4>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Tidak ditemukan mahasiswa terdaftar yang belum memiliki draf atau pengajuan skripsi untuk kriteria filter ini.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            @if($students->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

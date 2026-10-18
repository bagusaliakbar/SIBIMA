<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Tugas Penguji Sidang', 'route' => null]
                ]" />
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-table-card 
            title="Daftar Mahasiswa Ujian Sidang"
            :footer="$examinations->hasPages() ? $examinations->links() : null">
            
            <x-slot name="headerActions">
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari nama, NPM, atau judul..." 
                        route="defense-examiner.index"
                        :params="[
                            'wave_id' => $selectedWaveId ?? ''
                        ]" />

                    <form action="{{ route('defense-examiner.index') }}" method="GET" class="relative group m-0 p-0 flex items-center">
                        @if(!empty($search))
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        <select name="wave_id" onchange="this.form.submit()" 
                                class="block w-full sm:w-auto pl-4 pr-10 h-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-colors shadow-2xs min-w-[220px] cursor-pointer">
                            <option value="" {{ empty($selectedWaveId) ? 'selected' : '' }}>
                                📅 Gelombang Aktif ({{ $activeWave?->name ?? 'Berjalan' }})
                            </option>
                            @foreach($waves as $wave)
                                <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                    {{ $wave->name }} {{ $wave->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </x-slot>

            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-50 dark:border-slate-700">
                        <th class="px-6 py-4">Mahasiswa & Judul</th>
                        <th class="px-6 py-4 text-center">Jadwal Sidang</th>
                        <th class="px-6 py-4 text-center">Lokasi</th>
                        <th class="px-6 py-4 text-center">Dokumen Skripsi</th>
                        <th class="px-6 py-4 text-center">Status Revisi</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($examinations as $exam)
                        @php
                            $currentUserId = Auth::id();
                            $isExaminer1 = ($exam->examiner1_id === $currentUserId);
                            $isExaminer2 = ($exam->examiner2_id === $currentUserId);
                            $isSupervisor1 = ($exam->thesis && $exam->thesis->pembimbing1_id === $currentUserId);
                            $isSupervisor2 = ($exam->thesis && $exam->thesis->pembimbing2_id === $currentUserId);
                            $isExaminer = $isExaminer1 || $isExaminer2;
                            $isSupervisor = $isSupervisor1 || $isSupervisor2;
                            $isStaff = in_array(Auth::user()->role, ['admin', 'kaprodi']);

                            $myRev = $exam->revisions->where('examiner_id', $currentUserId)->first();
                            $revP1 = $exam->thesis ? $exam->revisions->where('examiner_id', $exam->thesis->pembimbing1_id)->first() : null;
                            $revE1 = $exam->revisions->where('examiner_id', $exam->examiner1_id)->first();
                            $revE2 = $exam->revisions->where('examiner_id', $exam->examiner2_id)->first();
                            $gradedCount = ($revP1?->isGraded() ? 1 : 0) + ($revE1?->isGraded() ? 1 : 0) + ($revE2?->isGraded() ? 1 : 0);

                            $defDate = \Carbon\Carbon::parse($exam->schedule->date)->format('Y-m-d');
                            $defEnd = \Carbon\Carbon::parse($defDate . ' ' . \Carbon\Carbon::parse($exam->end_time)->format('H:i:s'));
                            $isFinished = ($exam->thesis && $exam->thesis->defenseApplication && $exam->thesis->defenseApplication->status === 'completed')
                                || $exam->isGradingComplete()
                                || $exam->isRevisionAllApproved()
                                || $defEnd->isPast();

                            $app = $exam->thesis?->defenseApplications?->where('status', '!=', 'rejected')->sortByDesc('id')->first() 
                                ?? $exam->thesis?->defenseApplication
                                ?? $exam->thesis?->seminarApplications?->where('status', '!=', 'rejected')->sortByDesc('id')->first()
                                ?? $exam->thesis?->seminarApplication;
                            $filePath = $app?->file_skripsi;
                            $docUrl = null;
                            if ($filePath) {
                                $docUrl = filter_var($filePath, FILTER_VALIDATE_URL) 
                                    ? $filePath 
                                    : route('download.private', ['path' => $filePath, 'inline' => 1]);
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors group">
                            <!-- Mahasiswa & Judul -->
                            <td class="px-6 py-5">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden mr-3.5 border border-slate-200 dark:border-slate-700 shadow-2xs shrink-0 flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                                        <img src="{{ $exam->thesis?->student?->avatar_url }}" alt="{{ $exam->thesis?->student?->name ?? 'Mahasiswa' }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="max-w-xs md:max-w-md min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-bold text-slate-900 dark:text-slate-100 text-sm tracking-tight">{{ $exam->thesis?->student?->name ?? 'Mahasiswa' }}</h4>
                                            @if($isSupervisor1)
                                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-semibold rounded-md border border-slate-200 dark:border-slate-700/80">Pembimbing 1</span>
                                            @elseif($isSupervisor2)
                                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-semibold rounded-md border border-slate-200 dark:border-slate-700/80">Pembimbing 2</span>
                                            @endif
                                            @if($isExaminer1)
                                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-semibold rounded-md border border-slate-200 dark:border-slate-700/80">Penguji 1</span>
                                            @elseif($isExaminer2)
                                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-semibold rounded-md border border-slate-200 dark:border-slate-700/80">Penguji 2</span>
                                            @elseif($isStaff)
                                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[10px] font-semibold rounded-md border border-slate-200 dark:border-slate-700/80">P1 & Penguji</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1 italic font-normal" title="{{ $exam->thesis?->title ?? '-' }}">
                                            "{{ $exam->thesis?->title ?? '-' }}"
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Jadwal Sidang -->
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($exam->schedule->date)->locale('id')->translatedFormat('d M Y') }}</span>
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center font-medium">
                                        <svg class="w-3 h-3 mr-1 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($exam->end_time)->format('H:i') }}
                                    </span>
                                    @if($isFinished)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-400 dark:text-slate-500 mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                            Selesai
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Lokasi -->
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex items-center justify-center">
                                    @if($exam->schedule->meeting_link && !$isFinished)
                                        <a href="{{ $exam->schedule->meeting_link }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50/70 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60 text-xs font-medium transition-colors" title="Buka tautan meeting">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            <span>{{ $exam->schedule->location }}</span>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 dark:text-slate-300">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span>{{ $exam->schedule->location }}</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Dokumen Skripsi -->
                            <td class="px-6 py-5 text-center">
                                @if($docUrl)
                                    <a href="{{ $docUrl }}" target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium transition-all shadow-2xs group/doc"
                                       title="Buka / Baca Dokumen Skripsi">
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover/doc:text-slate-600 dark:group-hover/doc:text-slate-200 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>Dokumen</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500 font-medium italic">-</span>
                                @endif
                            </td>

                            <!-- Status & Nilai -->
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    {{-- Score Status --}}
                                    @if($isStaff)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                                            <span class="text-slate-400 font-normal">Penilai:</span>
                                            <b class="text-slate-800 dark:text-slate-100">{{ $gradedCount }}/3</b>
                                        </span>
                                    @else
                                        @if($myRev && $myRev->isGraded())
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                                                <span class="text-slate-400 font-normal">Nilai:</span>
                                                <b class="text-slate-800 dark:text-slate-100">{{ number_format($myRev->total_score, 1) }}</b>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60">
                                                Belum Dinilai
                                            </span>
                                        @endif
                                    @endif

                                    {{-- Revision Status Badge (Purposeful Subtle Colors) --}}
                                    @if($isExaminer || $isStaff)
                                        @php
                                            $displayRev = $isStaff ? ($revE1 ?? $revE2) : $myRev;
                                        @endphp
                                        @if($displayRev)
                                            @if($displayRev->status === 'approved')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Revisi Selesai
                                                </span>
                                            @elseif($displayRev->status === 'resubmitted')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    Revisi Masuk
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                    Menunggu Mahasiswa
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Belum ada catatan</span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                            Hanya Penilaian
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('defense-examiner.grading', $exam->id) }}" 
                                       class="inline-flex items-center px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-slate-100 dark:hover:bg-white text-white dark:text-slate-900 rounded-xl text-xs font-bold transition-all shadow-2xs hover:shadow-xs active:scale-95"
                                       title="Buka Lembar Penilaian Sidang">
                                        Input Nilai
                                    </a>
                                    @if($isExaminer || $isStaff)
                                        <a href="{{ route('defense-examiner.show', $exam->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-2xs"
                                           title="Buka Catatan & Thread Revisi">
                                            Revisi
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white">
                                    {{ !empty($search) ? 'Pencarian Tidak Ditemukan' : (empty($selectedWaveId) ? 'Tidak Ada Tugas Penguji Sidang Aktif / Mendatang' : 'Tidak Ada Tugas Penguji Pada Gelombang Ini') }}
                                </h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-1 leading-relaxed">
                                    @if(!empty($search))
                                        Tidak ditemukan data mahasiswa sidang dengan kata kunci "<span class="font-bold text-slate-700 dark:text-slate-200">{{ $search }}</span>". Coba gunakan kata kunci nama atau NPM lain.
                                    @elseif(empty($selectedWaveId))
                                        Jadwal sidang pada tanggal pelaksanaan sebelumnya telah selesai. Untuk melihat riwayat tugas penguji dan nilai terdahulu, silakan <strong>pilih Gelombang Pelaksanaan</strong> pada menu di atas.
                                    @else
                                        Belum ada penugasan penguji sidang untuk gelombang yang dipilih.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>
</x-app-layout>

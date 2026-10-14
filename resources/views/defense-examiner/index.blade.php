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
            title="Daftar Mahasiswa Ujian Sidang">
            
            <x-slot name="headerActions">
                <div class="flex items-center gap-3">
                    <form action="{{ route('defense-examiner.index') }}" method="GET" class="relative group">
                        <select name="wave_id" onchange="this.form.submit()" 
                                class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-bold focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all shadow-sm min-w-[240px] text-slate-700 dark:text-slate-300">
                            <option value="" {{ empty($selectedWaveId) ? 'selected' : '' }}>
                                📅 Jadwal Mendatang (Hari Ini+)
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
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden mr-4 border border-slate-200 dark:border-slate-700 shadow-sm group-hover:scale-110 transition-transform flex items-center justify-center bg-slate-50 dark:bg-slate-800">
                                        <img src="{{ $exam->thesis?->student?->avatar_url }}" alt="{{ $exam->thesis?->student?->name ?? 'Mahasiswa' }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="max-w-xs md:max-w-md">
                                        <h4 class="font-black text-slate-800 dark:text-slate-100 text-sm group-hover:text-rose-600 transition-colors uppercase tracking-tight">{{ $exam->thesis?->student?->name ?? 'Mahasiswa' }}</h4>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-1 uppercase font-bold tracking-tighter">"{{ $exam->thesis?->title ?? '-' }}"</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @php
                                    $defDate = \Carbon\Carbon::parse($exam->schedule->date)->format('Y-m-d');
                                    $defEnd = \Carbon\Carbon::parse($defDate . ' ' . \Carbon\Carbon::parse($exam->end_time)->format('H:i:s'));
                                    $isFinished = ($exam->thesis && $exam->thesis->defenseApplication && $exam->thesis->defenseApplication->status === 'completed')
                                        || $exam->isGradingComplete()
                                        || $exam->isRevisionAllApproved()
                                        || $defEnd->isPast();
                                @endphp
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase">{{ \Carbon\Carbon::parse($exam->schedule->date)->locale('id')->translatedFormat('d M Y') }}</span>
                                    <span class="text-[10px] text-slate-400 flex items-center font-bold uppercase">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($exam->end_time)->format('H:i') }}
                                    </span>
                                    @if($isFinished)
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-emerald-200 mt-1">Selesai</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($exam->schedule->meeting_link && !$isFinished)
                                    <a href="{{ $exam->schedule->meeting_link }}" target="_blank" class="group/link inline-block">
                                        <x-status-badge type="rose" :label="$exam->schedule->location" />
                                        <div class="text-[9px] text-rose-500 font-black mt-1 uppercase tracking-tighter opacity-0 group-hover/link:opacity-100 transition-opacity">Klik Buka Link</div>
                                    </a>
                                @else
                                    <x-status-badge type="slate" :label="$exam->schedule->location" />
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                @php
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
                                @if($docUrl)
                                    <a href="{{ $docUrl }}" target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800/60 text-rose-700 dark:text-rose-300 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white text-xs font-bold transition-all shadow-xs group/doc"
                                       title="Buka / Baca Dokumen Skripsi">
                                        <svg class="w-4 h-4 text-rose-500 group-hover/doc:text-white transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>Dokumen</span>
                                        <svg class="w-3 h-3 text-rose-400 group-hover/doc:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                @php
                                    $isExaminer1 = ($exam->examiner1_id === Auth::id());
                                    $isExaminer2 = ($exam->examiner2_id === Auth::id());
                                    $isSupervisor1 = ($exam->thesis->pembimbing1_id === Auth::id());
                                    $isSupervisor2 = ($exam->thesis->pembimbing2_id === Auth::id());
                                    $isExaminer = $isExaminer1 || $isExaminer2;
                                    $isSupervisor = $isSupervisor1 || $isSupervisor2;
                                    $revision = $exam->revisions()->where('examiner_id', Auth::id())->first();
                                @endphp
                                <div class="flex flex-col items-center gap-2">
                                    <div class="flex gap-1">
                                        @if($isSupervisor1)
                                            <span class="px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[8px] font-black uppercase rounded border border-indigo-100 dark:border-indigo-500/20">Pembimbing 1</span>
                                        @elseif($isSupervisor2)
                                            <span class="px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[8px] font-black uppercase rounded border border-indigo-100 dark:border-indigo-500/20">Pembimbing 2</span>
                                        @endif
                                        @if($isExaminer1)
                                            <span class="px-1.5 py-0.5 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-[8px] font-black uppercase rounded border border-rose-100 dark:border-rose-500/20">Penguji 1</span>
                                        @elseif($isExaminer2)
                                            <span class="px-1.5 py-0.5 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-[8px] font-black uppercase rounded border border-rose-100 dark:border-rose-500/20">Penguji 2</span>
                                        @endif
                                    </div>
                                    @if($isExaminer)
                                        @if($revision)
                                            @if($revision->status === 'approved')
                                                <x-status-badge type="emerald" label="REVISI SELESAI" />
                                            @elseif($revision->status === 'resubmitted')
                                                <x-status-badge type="blue" label="REVISI TERKIRIM" />
                                            @else
                                                <x-status-badge type="orange" label="REVISI DIKIRIM" />
                                            @endif
                                        @else
                                            <x-status-badge type="amber" label="BELUM ADA REVISI" />
                                        @endif
                                    @else
                                        <x-status-badge type="blue" label="HANYA NILAI" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('defense-examiner.grading', $exam->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20">
                                        Input Nilai
                                    </a>
                                    @if($isExaminer)
                                        <a href="{{ route('defense-examiner.show', $exam->id) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-lg shadow-rose-500/20">
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
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white">
                                    {{ empty($selectedWaveId) ? 'Tidak Ada Tugas Penguji Sidang Aktif / Mendatang' : 'Tidak Ada Tugas Penguji Pada Gelombang Ini' }}
                                </h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-1 leading-relaxed">
                                    @if(empty($selectedWaveId))
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

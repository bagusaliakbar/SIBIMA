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
                                class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm min-w-[200px] text-slate-700 dark:text-slate-300">
                            <option value="">Semua Gelombang</option>
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
                                        <img src="{{ $exam->thesis->student->avatar_url }}" alt="{{ $exam->thesis->student->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="max-w-xs md:max-w-md">
                                        <h4 class="font-black text-slate-800 dark:text-slate-100 text-sm group-hover:text-rose-600 transition-colors uppercase tracking-tight">{{ $exam->thesis->student->name }}</h4>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-1 uppercase font-bold tracking-tighter">"{{ $exam->thesis->title }}"</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @php
                                    $isFinished = ($exam->thesis && $exam->thesis->defenseApplication && $exam->thesis->defenseApplication->status === 'completed')
                                        || $exam->isGradingComplete()
                                        || $exam->isRevisionAllApproved()
                                        || \Carbon\Carbon::parse($exam->schedule->date)->isPast();
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
                        <x-empty-state colspan="5" description="Anda belum diplot sebagai penguji sidang." icon="book" />
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Monitoring', 'route' => route('monitoring.index')],
            ['label' => 'Revisi Sidang', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Monitoring Progres Revisi Sidang</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pantau status perbaikan skripsi mahasiswa setelah sidang tugas akhir.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <!-- Wave Filter -->
                    <form action="{{ route('monitoring.defense-revisions') }}" method="GET" class="flex items-center gap-2">
                        @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                        <select name="wave_id" onchange="this.form.submit()" 
                                class="pl-4 pr-10 py-1.5 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-[11px] font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all shadow-sm min-w-[180px] text-slate-700 dark:text-slate-300">
                            <option value="">Semua Gelombang</option>
                            @foreach($waves as $wave)
                                <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                    {{ $wave->name }} {{ $wave->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <!-- Search Input -->
                    <form action="{{ route('monitoring.defense-revisions') }}" method="GET" class="relative w-full sm:w-auto">
                        @if($selectedWaveId) <input type="hidden" name="wave_id" value="{{ $selectedWaveId }}"> @endif
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari mahasiswa..." class="block w-full sm:w-64 pl-10 pr-10 py-1.5 border border-slate-200 dark:border-slate-700 rounded-lg leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:bg-white dark:focus:bg-slate-800 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 text-xs transition-colors font-medium">
                        @if(isset($search) && $search !== '')
                            <a href="{{ route('monitoring.defense-revisions', ['wave_id' => $selectedWaveId]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">MAHASISWA & SIDANG</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">PENGUJI 1</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">PENGUJI 2</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">PEMBIMBING 1</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center">SUMMARY STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($defenseDetails as $detail)
                            @php
                                $rev1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
                                $rev2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();
                                $revP1 = $detail->revisions->where('examiner_id', $detail->thesis->pembimbing1_id)->first();
                                
                                // Grading Status
                                $grade1 = ($rev1 && $rev1->score_presentation !== null);
                                $grade2 = ($rev2 && $rev2->score_presentation !== null);
                                $gradeP1 = ($revP1 && $revP1->score_presentation !== null);
                                $isGradingComplete = $grade1 && $grade2 && $gradeP1;

                                // Revision Status
                                $revStatus1 = $rev1 ? $rev1->status : 'none';
                                $revStatus2 = $rev2 ? $rev2->status : 'none';
                                $isRevisionComplete = ($revStatus1 === 'approved') && ($revStatus2 === 'approved');
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors align-top">
                                <td class="py-4 px-6 border-r border-slate-50 dark:border-slate-800">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 truncate max-w-[150px]" title="{{ $detail->thesis->student->name }}">{{ $detail->thesis->student->name ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 tracking-tight uppercase">{{ $detail->thesis->student->identifier ?? 'N/A' }}</div>
                                    <div class="mt-3">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Tanggal Sidang</span>
                                        <div class="inline-flex items-center gap-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                                            <svg class="w-3 h-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Penguji 1 -->
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-3">
                                        <span class="text-[10px] text-slate-600 dark:text-slate-300 font-bold uppercase truncate" title="{{ $detail->examiner1->name }}">{{ $detail->examiner1->name }}</span>
                                        <div class="space-y-2">
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Penilaian</span>
                                                @if($grade1)
                                                    @php $total1 = ($rev1->score_presentation * 0.25) + ($rev1->score_explanation * 0.40) + ($rev1->score_writing * 0.35); @endphp
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-black border border-emerald-100">{{ $total1 }}</span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 text-[9px] font-black border border-rose-100 animate-pulse">BELUM ADA</span>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Revisi</span>
                                                @if($revStatus1 === 'approved')
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[9px] font-black border border-emerald-200 uppercase">
                                                        Selesai
                                                    </span>
                                                @elseif($revStatus1 === 'resubmitted')
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 text-[9px] font-black border border-blue-200 uppercase animate-pulse">
                                                        Terkirim
                                                    </span>
                                                @elseif($rev1)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-orange-100 text-orange-700 text-[9px] font-black border border-orange-200 uppercase">
                                                        Dikirim
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-400 text-[9px] font-black border border-slate-200 uppercase">
                                                        None
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Penguji 2 -->
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-3">
                                        <span class="text-[10px] text-slate-600 dark:text-slate-300 font-bold uppercase truncate" title="{{ $detail->examiner2->name }}">{{ $detail->examiner2->name }}</span>
                                        <div class="space-y-2">
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Penilaian</span>
                                                @if($grade2)
                                                    @php $total2 = ($rev2->score_presentation * 0.25) + ($rev2->score_explanation * 0.40) + ($rev2->score_writing * 0.35); @endphp
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-black border border-emerald-100">{{ $total2 }}</span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 text-[9px] font-black border border-rose-100 animate-pulse">BELUM ADA</span>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Revisi</span>
                                                @if($revStatus2 === 'approved')
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[9px] font-black border border-emerald-200 uppercase">
                                                        Selesai
                                                    </span>
                                                @elseif($revStatus2 === 'resubmitted')
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 text-[9px] font-black border border-blue-200 uppercase animate-pulse">
                                                        Terkirim
                                                    </span>
                                                @elseif($rev2)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-orange-100 text-orange-700 text-[9px] font-black border border-orange-200 uppercase">
                                                        Dikirim
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-400 text-[9px] font-black border border-slate-200 uppercase">
                                                        None
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Pembimbing 1 -->
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-3">
                                        <span class="text-[10px] text-slate-600 dark:text-slate-300 font-bold uppercase truncate" title="{{ $detail->thesis->pembimbing1->name }}">{{ $detail->thesis->pembimbing1->name }}</span>
                                        <div class="space-y-2">
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Penilaian</span>
                                                @if($gradeP1)
                                                    @php $totalP1 = ($revP1->score_presentation * 0.25) + ($revP1->score_explanation * 0.40) + ($revP1->score_writing * 0.35); @endphp
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-black border border-emerald-100">{{ $totalP1 }}</span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 text-[9px] font-black border border-rose-100 animate-pulse">BELUM ADA</span>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Revisi</span>
                                                <span class="text-[8px] font-bold text-slate-300 uppercase italic">Tidak Ada Revisi</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-6 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <!-- Grading Summary -->
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="w-7 h-7 rounded-full {{ $isGradingComplete ? 'bg-emerald-600' : 'bg-slate-200' }} flex items-center justify-center text-white shadow-sm transition-all duration-300">
                                                @if($isGradingComplete)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"></path></svg>
                                                @endif
                                            </div>
                                            <span class="text-[8px] font-black {{ $isGradingComplete ? 'text-emerald-600' : 'text-slate-400' }} uppercase tracking-tighter">Penilaian</span>
                                        </div>
                                        <!-- Revision Summary -->
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="w-7 h-7 rounded-full {{ $isRevisionComplete ? 'bg-blue-600' : 'bg-slate-200' }} flex items-center justify-center text-white shadow-sm transition-all duration-300">
                                                @if($isRevisionComplete)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                @endif
                                            </div>
                                            <span class="text-[8px] font-black {{ $isRevisionComplete ? 'text-blue-600' : 'text-slate-400' }} uppercase tracking-tighter">Revisi</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                                        <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Tidak ada data ditemukan</h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest font-black">Belum ada mahasiswa yang dijadwalkan sidang</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($defenseDetails->hasPages())
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    {{ $defenseDetails->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

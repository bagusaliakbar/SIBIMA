<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Monitoring', 'route' => route('monitoring.index')],
            ['label' => 'Revisi Seminar', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Monitoring Progres Revisi Seminar</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pantau status perbaikan draf skripsi mahasiswa setelah seminar.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <!-- Wave Filter -->
                    <form action="{{ route('monitoring.revisions') }}" method="GET" class="flex items-center gap-2">
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
                    <form action="{{ route('monitoring.revisions') }}" method="GET" class="relative w-full sm:w-auto">
                        @if($selectedWaveId) <input type="hidden" name="wave_id" value="{{ $selectedWaveId }}"> @endif
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-transform group-focus-within:scale-110">
                            <svg class="h-4 w-4 text-slate-400 group-focus-within:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari mahasiswa..." class="block w-full sm:w-64 pl-10 pr-10 py-1.5 border border-slate-200 dark:border-slate-700 rounded-lg leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:bg-white dark:focus:bg-slate-800 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 text-xs transition-colors font-medium">
                        @if(isset($search) && $search !== '')
                            <a href="{{ route('monitoring.revisions', ['wave_id' => $selectedWaveId]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300">
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
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">MAHASISWA & SEMINAR</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">STATUS REVISI (PENGUJI 1)</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">STATUS REVISI (PENGUJI 2)</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center">OVERALL STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($seminarDetails as $detail)
                            @php
                                $rev1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
                                $rev2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();
                                
                                $isAllApproved = ($rev1 && $rev1->status === 'approved') && ($rev2 && $rev2->status === 'approved');
                                $isStarted = $rev1 || $rev2;
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors align-top">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800 dark:text-slate-100">{{ $detail->thesis->student->name ?? 'N/A' }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 tracking-tight uppercase">{{ $detail->thesis->student->identifier ?? 'N/A' }}</div>
                                    <div class="mt-2 flex flex-col gap-1">
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>{{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H:i') }} WIB</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">{{ $detail->examiner1->name }}</span>
                                        </div>
                                        @if($rev1)
                                            @if($rev1->status === 'approved')
                                                <span class="inline-flex items-center w-fit px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase border border-emerald-200 dark:border-emerald-800">Revisi Selesai</span>
                                            @elseif($rev1->status === 'resubmitted')
                                                <span class="inline-flex items-center w-fit px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] font-bold uppercase border border-blue-200 dark:border-blue-800 animate-pulse">Revisi Terkirim</span>
                                            @else
                                                <span class="inline-flex items-center w-fit px-2 py-0.5 rounded bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 text-[10px] font-bold uppercase border border-orange-200 dark:border-orange-800">Revisi Dikirim</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center w-fit px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 text-[10px] font-bold uppercase border border-slate-200 dark:border-slate-600">Belum Ada Revisi</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">{{ $detail->examiner2->name }}</span>
                                        </div>
                                        @if($rev2)
                                            @if($rev2->status === 'approved')
                                                <span class="inline-flex items-center w-fit px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase border border-emerald-200 dark:border-emerald-800">Revisi Selesai</span>
                                            @elseif($rev2->status === 'resubmitted')
                                                <span class="inline-flex items-center w-fit px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] font-bold uppercase border border-blue-200 dark:border-blue-800 animate-pulse">Revisi Terkirim</span>
                                            @else
                                                <span class="inline-flex items-center w-fit px-2 py-0.5 rounded bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 text-[10px] font-bold uppercase border border-orange-200 dark:border-orange-800">Revisi Dikirim</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center w-fit px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 text-[10px] font-bold uppercase border border-slate-200 dark:border-slate-600">Belum Ada Revisi</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="py-4 px-6 text-center">
                                    @if($isAllApproved)
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-900/20 mx-auto">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-tighter">FINALIZED</span>
                                        </div>
                                    @elseif($isStarted)
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white shadow-lg shadow-orange-900/20 mx-auto animate-pulse">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <span class="text-[10px] font-black text-orange-500 uppercase tracking-tighter">IN PROGRESS</span>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 mx-auto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            </div>
                                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-tighter">NOT STARTED</span>
                                        </div>
                                    @endif
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
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest font-black">Belum ada mahasiswa yang dijadwalkan seminar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($seminarDetails->hasPages())
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    {{ $seminarDetails->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

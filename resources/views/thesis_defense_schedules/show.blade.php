<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Sidang', 'route' => route('thesis-defense-schedules.index')],
            ['label' => 'Detail Jadwal', 'route' => null]
        ]" />
        <div class="flex justify-end items-center w-full">
            <a href="{{ route('documents.sk-penguji-sidang', $thesisDefenseSchedule) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm mr-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                SK Tim Penguji
            </a>
            <a href="{{ route('thesis-defense-schedules.export-pdf', $thesisDefenseSchedule) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm mr-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export PDF
            </a>
            <a href="{{ route('thesis-defense-schedules.edit', $thesisDefenseSchedule) }}" class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-slate-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Jadwal
            </a>
        </div>
    </x-slot>

    <div class="w-full mx-auto">
        <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden mb-6">
            <div class="p-8 text-center border-b border-slate-200 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 uppercase tracking-[0.2em] mb-2">JADWAL SIDANG SKRIPSI</h2>
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest">{{ $thesisDefenseSchedule->title }}</h3>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <span class="w-32 text-[10px] font-black text-slate-400 uppercase tracking-widest">Hari / Tanggal</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">: {{ \Carbon\Carbon::parse($thesisDefenseSchedule->date)->locale('id')->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-32 text-[10px] font-black text-slate-400 uppercase tracking-widest">Ketua Sidang</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">: {{ $thesisDefenseSchedule->chairman?->name ?? '-' }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-32 text-[10px] font-black text-slate-400 uppercase tracking-widest">Moderator</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">: {{ $thesisDefenseSchedule->moderator?->name ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col md:items-end justify-start space-y-3">
                        <div class="inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-slate-700/50 rounded-lg border border-slate-200 dark:border-slate-600">
                            <span class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mr-3">Tempat:</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $thesisDefenseSchedule->location ?: '-' }}</span>
                        </div>
                        @if($thesisDefenseSchedule->meeting_link)
                        <a href="{{ $thesisDefenseSchedule->meeting_link }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg border border-orange-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-black uppercase tracking-widest">Join Google Meet / Link</span>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-sm text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">
                                <th class="py-4 px-3 w-12 border-b border-r border-slate-200 dark:border-slate-700">NO</th>
                                <th class="py-4 px-3 w-32 border-b border-r border-slate-200 dark:border-slate-700">WAKTU</th>
                                <th class="py-4 px-3 border-b border-r border-slate-200 dark:border-slate-700">DETAIL KEGIATAN / PESERTA</th>
                                <th class="py-4 px-3 w-50 border-b border-r border-slate-200 dark:border-slate-700">PEMBIMBING</th>
                                <th class="py-4 px-3 w-50 border-b border-slate-200 dark:border-slate-700">PENGUJI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @php $studentNo = 1; @endphp
                            @foreach($thesisDefenseSchedule->details as $detail)
                                @if($detail->thesis_id)
                                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="py-5 px-3 text-center border-r border-slate-200 dark:border-slate-700 font-bold text-slate-800 dark:text-slate-200">{{ $studentNo++ }}</td>
                                        <td class="py-5 px-3 text-center border-r border-slate-200 dark:border-slate-700">
                                            <span class="inline-flex items-center px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs font-bold text-slate-700 dark:text-slate-300">
                                                {{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}
                                            </span>
                                        </td>
                                        <td class="py-5 px-6 border-r border-slate-200 dark:border-slate-700">
                                            <div class="space-y-2">
                                                <div class="flex items-center flex-wrap gap-2">
                                                    <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">
                                                        {{ $detail->thesis->student->identifier }}
                                                    </span> 
                                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $detail->thesis->student->name }}</span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed max-w-lg">{{ $detail->thesis->title }}</p>
                                            </div>
                                        </td>
                                        <td class="py-5 px-5 border-r border-slate-200 dark:border-slate-700">
                                            <div class="space-y-2">
                                                <div class="flex items-start">
                                                    <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 mt-0.5 mr-2">1.</span>
                                                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 leading-tight">{{ $detail->thesis->pembimbing1?->name ?? '-' }}</span>
                                                </div>
                                                @if($detail->thesis->pembimbing2)
                                                <div class="flex items-start">
                                                    <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 mt-0.5 mr-2">2.</span>
                                                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 leading-tight">{{ $detail->thesis->pembimbing2->name }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-5 px-5">
                                            <div class="space-y-2">
                                                @if($detail->examiner1)
                                                <div class="flex items-start">
                                                    <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 mt-0.5 mr-2">1.</span>
                                                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 leading-tight">{{ $detail->examiner1->name }}</span>
                                                </div>
                                                @endif
                                                @if($detail->examiner2)
                                                <div class="flex items-start">
                                                    <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 mt-0.5 mr-2">2.</span>
                                                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 leading-tight">{{ $detail->examiner2->name }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="bg-slate-50/50 dark:bg-slate-900/30">
                                        <td class="py-3 px-3 text-center border-r border-slate-200 dark:border-slate-700 font-bold text-slate-400">#</td>
                                        <td class="py-3 px-3 text-center border-r border-slate-200 dark:border-slate-700">
                                            <span class="text-xs font-bold text-slate-500">{{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}</span>
                                        </td>
                                        <td colspan="3" class="py-3 px-8">
                                            <span class="text-[11px] font-black text-slate-600 dark:text-slate-400 uppercase tracking-[0.1em]">{{ $detail->activity_name }}</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

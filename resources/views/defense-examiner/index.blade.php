<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :links="[
                    ['name' => 'Penguji Sidang', 'url' => route('defense-examiner.index')]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Tugas Penguji Sidang
                    <span class="ml-3 px-2 py-0.5 bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-black uppercase tracking-wider rounded-md border border-rose-200 dark:border-rose-500/20 shadow-sm">Penguji</span>
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full mr-2"></span>
                    Daftar mahasiswa yang dijadwalkan untuk Anda uji pada Sidang Tugas Akhir
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            {{-- Wave Filter Section --}}
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Gelombang Pelaksanaan</h3>
                    <div class="flex items-center gap-2 mt-0.5 text-[10px] font-bold">
                        <span class="text-slate-400 uppercase tracking-widest">Gelombang Aktif :</span>
                        @if($activeWave)
                            <span class="text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-1.5 rounded">{{ $activeWave->name }}</span>
                        @else
                            <span class="text-rose-500 bg-rose-50 dark:bg-rose-500/10 px-1.5 rounded italic">Tidak Ada Gelombang Aktif</span>
                        @endif
                    </div>
                </div>

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
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-50 dark:border-slate-700">
                            <th class="px-6 py-4">Mahasiswa & Judul</th>
                            <th class="px-6 py-4">Jadwal Sidang</th>
                            <th class="px-6 py-4">Lokasi</th>
                            <th class="px-6 py-4">Status Revisi</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                        @forelse($examinations as $exam)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 flex items-center justify-center mr-4 font-bold text-sm shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr($exam->thesis->student->name, 0, 1) }}
                                    </div>
                                    <div class="max-w-xs md:max-w-md">
                                        <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm group-hover:text-rose-600 transition-colors">{{ $exam->thesis->student->name }}</h4>
                                        <p class="text-[11px] text-slate-400 mt-1 line-clamp-1 italic">"{{ $exam->thesis->title }}"</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ \Carbon\Carbon::parse($exam->schedule->date)->format('d M Y') }}</span>
                                    <span class="text-[10px] text-slate-400 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($exam->end_time)->format('H:i') }} WIB
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded text-[10px] font-bold">
                                    {{ $exam->schedule->location }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                @php
                                    $isExaminer = ($exam->examiner1_id === Auth::id() || $exam->examiner2_id === Auth::id());
                                    $isSupervisor = ($exam->thesis->pembimbing1_id === Auth::id());
                                    $revision = $exam->revisions()->where('examiner_id', Auth::id())->first();
                                @endphp
                                <div class="flex flex-col gap-2">
                                    <div class="flex gap-1">
                                        @if($isSupervisor)
                                            <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 text-[8px] font-black uppercase rounded border border-indigo-200">Pembimbing</span>
                                        @endif
                                        @if($isExaminer)
                                            <span class="px-1.5 py-0.5 bg-rose-100 text-rose-700 text-[8px] font-black uppercase rounded border border-rose-200">Penguji</span>
                                        @endif
                                    </div>
                                    @if($isExaminer)
                                        @if($revision)
                                            @if($revision->status === 'approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-emerald-100 text-emerald-700 border border-emerald-200">
                                                    Revisi Selesai
                                                </span>
                                            @elseif($revision->status === 'resubmitted')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-blue-100 text-blue-700 border border-blue-200 animate-pulse">
                                                    Revisi Terkirim
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-orange-100 text-orange-700 border border-orange-200">
                                                    Revisi Dikirim
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-amber-100 text-amber-700 border border-amber-200">
                                                Belum Ada Revisi
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">
                                            Hanya Input Nilai
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('defense-examiner.grading', $exam->id) }}" 
                                       title="Input Nilai Sidang"
                                       class="inline-flex items-center px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100 dark:border-emerald-500/20">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a.75.75 0 00-1.061 0l-1.061 1.06a.75.75 0 101.06 1.061l1.061-1.06a.75.75 0 000-1.061zM6 8a2 2 0 11-4 0 2 2 0 014 0zM22 12a10 10 0 11-20 0 10 10 0 0120 0z"></path></svg>
                                        Input Nilai
                                    </a>
                                    @if($isExaminer)
                                        <a href="{{ route('defense-examiner.show', $exam->id) }}" 
                                           title="Diskusi Revisi"
                                           class="inline-flex items-center px-3 py-1.5 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-rose-100 dark:border-rose-500/20">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Revisi
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-700/50 rounded-2xl flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-200 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    <h3 class="text-slate-400 font-bold text-sm uppercase tracking-widest">Tidak Ada Jadwal Menguji Sidang</h3>
                                    <p class="text-[11px] text-slate-400 mt-1 italic">Anda belum diplot sebagai penguji sidang pada jadwal manapun.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

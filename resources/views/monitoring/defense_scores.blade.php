<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :links="[
                    ['name' => 'Monitoring', 'url' => '#'],
                    ['name' => 'Nilai Sidang', 'url' => route('monitoring.defense-scores')]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Rekapitulasi Nilai Sidang
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2"></span>
                    Data nilai mahasiswa yang telah diinput oleh pembimbing dan penguji
                </p>
            </div>
            
            <div class="flex flex-col md:flex-row items-center gap-3">
                <!-- Wave Filter -->
                <form action="{{ route('monitoring.defense-scores') }}" method="GET" class="flex items-center gap-2">
                    @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                    <select name="wave_id" onchange="this.form.submit()" 
                            class="pl-4 pr-10 py-3 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 rounded-2xl text-[12px] font-bold focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm min-w-[180px] text-slate-700 dark:text-slate-300">
                        <option value="">Semua Gelombang</option>
                        @foreach($waves as $wave)
                            <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                {{ $wave->name }} {{ $wave->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- Export Buttons -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('monitoring.defense-scores.export-excel', ['wave_id' => $selectedWaveId]) }}" 
                       class="flex items-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-[12px] font-bold transition-all shadow-lg shadow-emerald-900/20 group">
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Excel
                    </a>
                    <a href="{{ route('monitoring.defense-scores.export-pdf', ['wave_id' => $selectedWaveId]) }}" 
                       class="flex items-center gap-2 px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-[12px] font-bold transition-all shadow-lg shadow-rose-900/20 group">
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        PDF
                    </a>
                </div>

                <form action="{{ route('monitoring.defense-scores') }}" method="GET" class="relative group min-w-[320px]">
                    @if($selectedWaveId) <input type="hidden" name="wave_id" value="{{ $selectedWaveId }}"> @endif
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-transform group-focus-within:scale-110">
                        <svg class="w-4 h-4 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari Mahasiswa atau NPM..." 
                           class="w-full pl-12 pr-10 py-3 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 rounded-2xl text-[13px] font-medium focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm placeholder:text-slate-400/80">
                    @if($search)
                        <a href="{{ route('monitoring.defense-scores', ['wave_id' => $selectedWaveId]) }}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-rose-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/80 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <th rowspan="2" class="py-5 px-3 text-center font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] w-12">NO</th>
                        <th rowspan="2" class="py-5 px-4 text-center font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] w-32">NPM</th>
                        <th rowspan="2" class="py-5 px-6 text-left font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em]">NAMA MAHASISWA & JUDUL</th>
                        <th rowspan="2" class="py-5 px-6 text-left font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] w-56">TIM PENELAAH</th>
                        <th colspan="3" class="py-3 px-4 text-center font-black text-[10px] border-b border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] bg-slate-100/50 dark:bg-slate-800/50">KOMPONEN PENILAIAN SIDANG</th>
                        <th rowspan="2" class="py-5 px-4 text-center font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] w-24">JML NILAI</th>
                        <th rowspan="2" class="py-5 px-4 text-center font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] w-20">TOTAL</th>
                        <th rowspan="2" class="py-5 px-4 text-center font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] w-24">NILAI AKHIR</th>
                        <th rowspan="2" class="py-5 px-4 text-center font-black text-[10px] uppercase tracking-[0.2em] w-20">NILAI HURUF</th>
                    </tr>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-3 text-center font-black text-[9px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-tighter">PRESENTASI (25%)</th>
                        <th class="py-3 px-3 text-center font-black text-[9px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-tighter">NASKAH TA (40%)</th>
                        <th class="py-3 px-3 text-center font-black text-[9px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-tighter">PENULISAN (35%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($defenseDetails as $index => $detail)
                        @php
                            $revP1 = $detail->revisions->where('examiner_id', $detail->thesis->pembimbing1_id)->first();
                            $revE1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
                            $revE2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();

                            $calc = function($rev) {
                                if (!$rev || $rev->score_presentation === null) return null;
                                return ($rev->score_presentation * 0.25) + ($rev->score_explanation * 0.40) + ($rev->score_writing * 0.35);
                            };

                            $scoreP1 = $calc($revP1);
                            $scoreE1 = $calc($revE1);
                            $scoreE2 = $calc($revE2);

                            $scores = collect([$scoreP1, $scoreE1, $scoreE2])->filter(fn($s) => $s !== null);
                            $totalScore = $scores->sum();
                            $finalScore = $scores->count() > 0 ? $totalScore / $scores->count() : 0;

                            $getGrade = function($s) {
                                if ($s >= 80) return 'A';
                                if ($s >= 75) return 'B+';
                                if ($s >= 70) return 'B';
                                if ($s >= 65) return 'C+';
                                if ($s >= 60) return 'C';
                                if ($s >= 50) return 'D';
                                return 'E';
                            };
                            $finalGrade = $scores->count() > 0 ? $getGrade($finalScore) : '-';
                            $gradeColor = match($finalGrade) {
                                'A' => 'emerald',
                                'B+', 'B' => 'blue',
                                'C+', 'C' => 'amber',
                                default => 'slate'
                            };
                        @endphp
                        
                        <!-- Row Pembimbing 1 -->
                        <tr class="group hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors">
                            <td rowspan="3" class="py-4 px-3 text-center font-bold text-xs border-r border-slate-100 dark:border-slate-700 align-middle bg-slate-50/30 dark:bg-slate-900/20">
                                {{ ($defenseDetails->currentPage() - 1) * $defenseDetails->perPage() + $index + 1 }}
                            </td>
                            <td rowspan="3" class="py-4 px-4 text-center font-bold text-[11px] border-r border-slate-100 dark:border-slate-700 align-middle text-slate-400 group-hover:text-emerald-600 transition-colors">
                                {{ $detail->thesis->student->identifier }}
                            </td>
                            <td rowspan="3" class="py-4 px-6 border-r border-slate-100 dark:border-slate-700 align-middle">
                                <div class="font-black text-xs text-slate-800 dark:text-slate-100 uppercase tracking-tight group-hover:text-emerald-600 transition-colors">{{ $detail->thesis->student->name }}</div>
                                <div class="text-[10px] text-slate-400 mt-1 line-clamp-2 italic font-medium leading-relaxed">"{{ $detail->thesis->title }}"</div>
                            </td>
                            <td class="py-3 px-5 text-[10px] font-bold border-r border-slate-100 dark:border-slate-700 bg-indigo-50/20 dark:bg-indigo-900/10">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[8px] font-black uppercase">P1</span>
                                    <span class="text-slate-600 dark:text-slate-300 truncate" title="{{ $detail->thesis->pembimbing1->name }}">{{ $detail->thesis->pembimbing1->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revP1->score_presentation ?? '-' }}</td>
                            <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revP1->score_explanation ?? '-' }}</td>
                            <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revP1->score_writing ?? '-' }}</td>
                            <td class="py-3 px-3 text-center text-xs font-black text-emerald-600 border-r border-slate-100 dark:border-slate-700 bg-emerald-50/40 dark:bg-emerald-900/10">
                                {{ $scoreP1 ? number_format($scoreP1, 1) : '-' }}
                            </td>
                            <td rowspan="3" class="py-4 px-4 text-center font-black text-[13px] border-r border-slate-100 dark:border-slate-700 align-middle text-slate-800 dark:text-slate-200">
                                {{ number_format($totalScore, 1) }}
                            </td>
                            <td rowspan="3" class="py-4 px-4 text-center align-middle border-r border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                                <div class="font-black text-lg text-slate-800 dark:text-slate-100">{{ number_format($finalScore, 1) }}</div>
                            </td>
                            <td rowspan="3" class="py-4 px-4 text-center align-middle bg-{{ $gradeColor }}-50/50 dark:bg-{{ $gradeColor }}-900/10 transition-colors duration-500">
                                <div class="text-3xl font-black text-{{ $gradeColor }}-600 drop-shadow-sm">{{ $finalGrade }}</div>
                            </td>
                        </tr>

                        <!-- Row Penguji 1 -->
                        <tr class="group hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors">
                            <td class="py-3 px-5 text-[10px] font-bold border-r border-slate-100 dark:border-slate-700 bg-rose-50/20 dark:bg-rose-900/10">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center text-[8px] font-black uppercase">U1</span>
                                    <span class="text-slate-600 dark:text-slate-300 truncate" title="{{ $detail->examiner1->name }}">{{ $detail->examiner1->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE1->score_presentation ?? '-' }}</td>
                            <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE1->score_explanation ?? '-' }}</td>
                            <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE1->score_writing ?? '-' }}</td>
                            <td class="py-3 px-3 text-center text-xs font-black text-emerald-600 border-r border-slate-100 dark:border-slate-700 bg-emerald-50/40 dark:bg-emerald-900/10">
                                {{ $scoreE1 ? number_format($scoreE1, 1) : '-' }}
                            </td>
                        </tr>

                        <!-- Row Penguji 2 -->
                        <tr class="group hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors border-b-2 border-slate-100 dark:border-slate-700">
                            <td class="py-3 px-5 text-[10px] font-bold border-r border-slate-100 dark:border-slate-700 bg-rose-50/20 dark:bg-rose-900/10">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center text-[8px] font-black uppercase">U2</span>
                                    <span class="text-slate-600 dark:text-slate-300 truncate" title="{{ $detail->examiner2->name }}">{{ $detail->examiner2->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE2->score_presentation ?? '-' }}</td>
                            <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE2->score_explanation ?? '-' }}</td>
                            <td class="py-3 px-3 text-center text-xs font-bold border-r border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $revE2->score_writing ?? '-' }}</td>
                            <td class="py-3 px-3 text-center text-xs font-black text-emerald-600 border-r border-slate-100 dark:border-slate-700 bg-emerald-50/40 dark:bg-emerald-900/10">
                                {{ $scoreE2 ? number_format($scoreE2, 1) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4 border border-slate-100 dark:border-slate-700 shadow-inner">
                                        <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    <h3 class="text-slate-400 font-bold text-sm uppercase tracking-widest">Belum Ada Data Nilai</h3>
                                    <p class="text-[11px] text-slate-400 mt-1 italic">Data nilai mahasiswa akan muncul setelah diinput oleh dosen penelaah.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($defenseDetails->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
                {{ $defenseDetails->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

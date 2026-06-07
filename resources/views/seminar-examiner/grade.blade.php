<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Tugas Penguji Seminar', 'route' => route('seminar-examiner.index')],
                    ['label' => 'Input Nilai Seminar', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Penilaian Seminar Proposal/Hasil
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    Berikan penilaian berdasarkan komponen yang telah ditetapkan
                </p>
            </div>
            <a href="{{ route('seminar-examiner.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Student Info Card -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 dark:bg-indigo-900/10 rounded-full -mr-16 -mt-16"></div>
            <div class="relative flex flex-col md:flex-row items-start md:items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-2xl font-black shadow-lg shadow-indigo-200 dark:shadow-none">
                    {{ substr($detail->thesis->student->name, 0, 1) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $detail->thesis->student->name }}</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $detail->thesis->student->identifier }}</p>
                    <p class="text-xs font-bold text-slate-600 dark:text-slate-300 mt-2 italic">"{{ $detail->thesis->title }}"</p>
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/50 px-4 py-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                    <div class="flex items-center text-xs font-bold text-slate-700 dark:text-slate-200">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ \Carbon\Carbon::parse($detail->schedule->date)->format('d F Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Grading Form -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            <div class="p-6 border-b border-slate-50 dark:border-slate-700">
                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest flex items-center">
                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a.75.75 0 00-1.061 0l-1.061 1.06a.75.75 0 101.06 1.061l1.061-1.06a.75.75 0 000-1.061zM6 8a2 2 0 11-4 0 2 2 0 014 0zM22 12a10 10 0 11-20 0 10 10 0 0120 0z"></path></svg>
                    Formulir Penilaian Komponen Seminar
                </h3>
            </div>
            
            <form action="{{ route('seminar-examiner.store-grading', $detail->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                    <table class="w-full text-[11px] text-left border-collapse" id="gradingTable">
                        <thead>
                            <tr class="bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-black uppercase tracking-widest">
                                <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 w-12 text-center">NO</th>
                                <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700">KOMPONEN PENILAIAN SEMINAR</th>
                                <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 w-20 text-center">Bobot (%)</th>
                                <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 w-32 text-center">Nilai (0-100)</th>
                                <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 w-24 text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <!-- 1. Presentasi -->
                            <tr>
                                <td class="py-4 px-4 text-center font-bold align-top">1</td>
                                <td class="py-4 px-4 align-top">
                                    <div class="font-bold text-slate-700 dark:text-slate-200 uppercase">Presentasi</div>
                                    <ul class="mt-1 space-y-1 text-slate-500 dark:text-slate-400 list-none">
                                        <li class="flex items-start">
                                            <span class="mr-1.5">a.</span>
                                            <span>Sistematika penyajian</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="mr-1.5">b.</span>
                                            <span>Penguasaan materi dan kemampuan menjawab</span>
                                        </li>
                                    </ul>
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs align-top">25</td>
                                <td class="py-4 px-4 align-top">
                                    <input type="number" name="score_presentation" id="score_presentation" value="{{ $myRevision->score_presentation ?? '' }}" min="0" max="100" required 
                                           class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-center font-bold text-slate-800 dark:text-slate-100 focus:ring-indigo-500 focus:border-indigo-500 px-2 py-2" 
                                           oninput="calculateTotal()" placeholder="0">
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-slate-700 dark:text-slate-200 align-top" id="total_presentation">
                                    {{ isset($myRevision->score_presentation) ? ($myRevision->score_presentation * 0.25) : '0' }}
                                </td>
                            </tr>
                            <!-- 2. Materi/Isi -->
                            <tr>
                                <td class="py-4 px-4 text-center font-bold align-top">2</td>
                                <td class="py-4 px-4 align-top">
                                    <div class="font-bold text-slate-700 dark:text-slate-200 uppercase">Materi dan Isi Laporan</div>
                                    <ul class="mt-1 space-y-1 text-slate-500 dark:text-slate-400 list-none">
                                        <li class="flex items-start">
                                            <span class="mr-1.5">a.</span>
                                            <span>Kesesuaian dengan rumusan masalah</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="mr-1.5">b.</span>
                                            <span>Metodologi yang digunakan</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="mr-1.5">c.</span>
                                            <span>Kedalaman analisis dan pembahasan</span>
                                        </li>
                                    </ul>
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs align-top">40</td>
                                <td class="py-4 px-4 align-top">
                                    <input type="number" name="score_explanation" id="score_explanation" value="{{ $myRevision->score_explanation ?? '' }}" min="0" max="100" required 
                                           class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-center font-bold text-slate-800 dark:text-slate-100 focus:ring-indigo-500 focus:border-indigo-500 px-2 py-2" 
                                           oninput="calculateTotal()" placeholder="0">
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-slate-700 dark:text-slate-200 align-top" id="total_explanation">
                                    {{ isset($myRevision->score_explanation) ? ($myRevision->score_explanation * 0.40) : '0' }}
                                </td>
                            </tr>
                            <!-- 3. Penulisan -->
                            <tr>
                                <td class="py-4 px-4 text-center font-bold align-top">3</td>
                                <td class="py-4 px-4 align-top">
                                    <div class="font-bold text-slate-700 dark:text-slate-200 uppercase">Kualitas Penulisan</div>
                                    <ul class="mt-1 space-y-1 text-slate-500 dark:text-slate-400 list-none">
                                        <li class="flex items-start">
                                            <span class="mr-1.5">a.</span>
                                            <span>Tata tulis dan penggunaan bahasa Indonesia</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="mr-1.5">b.</span>
                                            <span>Sitasi dan daftar pustaka</span>
                                        </li>
                                    </ul>
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs align-top">35</td>
                                <td class="py-4 px-4 align-top">
                                    <input type="number" name="score_writing" id="score_writing" value="{{ $myRevision->score_writing ?? '' }}" min="0" max="100" required 
                                           class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-center font-bold text-slate-800 dark:text-slate-100 focus:ring-indigo-500 focus:border-indigo-500 px-2 py-2" 
                                           oninput="calculateTotal()" placeholder="0">
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-slate-700 dark:text-slate-200 align-top" id="total_writing">
                                    {{ isset($myRevision->score_writing) ? ($myRevision->score_writing * 0.35) : '0' }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-100/50 dark:bg-slate-800/50 font-bold border-t-2 border-slate-200 dark:border-slate-700">
                            <tr>
                                <td colspan="4" class="py-3 px-6 text-right uppercase tracking-wider text-slate-600 dark:text-slate-400">Jumlah Skor</td>
                                <td class="py-3 px-4 text-center text-slate-800 dark:text-slate-100" id="final_sum_score">0</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="py-3 px-6 text-right uppercase tracking-wider text-slate-600 dark:text-slate-400">Rata-rata Skor</td>
                                <td class="py-3 px-4 text-center text-slate-800 dark:text-slate-100" id="final_avg_score">0</td>
                            </tr>
                            <tr class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400">
                                <td colspan="4" class="py-4 px-6 text-right font-black uppercase tracking-[0.2em] text-[13px]">Nilai Akhir</td>
                                <td class="py-4 px-4 text-center font-black text-lg" id="final_score">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <script>
                    function calculateTotal() {
                        const p = parseFloat(document.getElementById('score_presentation').value) || 0;
                        const e = parseFloat(document.getElementById('score_explanation').value) || 0;
                        const w = parseFloat(document.getElementById('score_writing').value) || 0;

                        const tp = (p * 0.25);
                        const te = (e * 0.40);
                        const tw = (w * 0.35);

                        document.getElementById('total_presentation').innerText = tp.toFixed(2);
                        document.getElementById('total_explanation').innerText = te.toFixed(2);
                        document.getElementById('total_writing').innerText = tw.toFixed(2);

                        const finalScore = tp + te + tw;
                        const sumScore = p + e + w;
                        const avgScore = sumScore / 3;

                        document.getElementById('final_sum_score').innerText = sumScore.toFixed(2);
                        document.getElementById('final_avg_score').innerText = avgScore.toFixed(2);
                        document.getElementById('final_score').innerText = finalScore.toFixed(2);
                    }
                    // Initial calculation
                    window.onload = calculateTotal;
                </script>

                <div class="flex items-center justify-between bg-indigo-50/50 dark:bg-indigo-500/5 p-4 rounded-xl border border-indigo-100 dark:border-indigo-500/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Informasi</p>
                            <p class="text-xs font-bold text-indigo-700 dark:text-indigo-400">Pastikan nilai yang diinput sudah sesuai dengan hasil seminar.</p>
                        </div>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-200 dark:shadow-none transition-all">
                        Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

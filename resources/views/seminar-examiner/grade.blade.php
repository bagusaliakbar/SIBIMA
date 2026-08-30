<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => request('redirect_to') === 'monitoring' ? 'Monitoring Revisi Seminar' : 'Tugas Penguji Seminar', 'route' => request('redirect_to') === 'monitoring' ? route('monitoring.revisions') : route('seminar-examiner.index')],
                    ['label' => 'Input Nilai Seminar', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Penilaian Seminar Proposal/Hasil
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($targetUser))
                        Mode {{ ucfirst(auth()->user()->role) }}: Mengisi nilai atas nama <span class="font-black text-indigo-600 dark:text-indigo-400 ml-1">{{ $targetUser->name }}</span>
                    @else
                        Berikan penilaian berdasarkan komponen yang telah ditetapkan
                    @endif
                </p>
            </div>
            @php
                $backUrl = request('redirect_to') === 'monitoring' ? route('monitoring.revisions') : route('seminar-examiner.index');
            @endphp
            <a href="{{ $backUrl }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($examiners))
            <!-- Admin / Kaprodi Examiner Selector Card -->
            <div class="bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-pink-500/10 border border-indigo-200/60 dark:border-indigo-800/40 rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                            Mode {{ ucfirst(auth()->user()->role) }}
                        </span>
                        <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">
                            Pilih Dosen Penguji Yang Dinilai
                        </h4>
                    </div>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium italic">
                        *Klik dosen untuk menginput atau memperbarui nilainya
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($examiners as $ex)
                        @php
                            $isSelected = ($actingId == $ex['user']->id);
                            $hasScore = $ex['revision'] && $ex['revision']->score_presentation !== null;
                            $calcScore = $hasScore ? (($ex['revision']->score_presentation * 0.25) + ($ex['revision']->score_explanation * 0.40) + ($ex['revision']->score_writing * 0.35)) : null;
                        @endphp
                        <a href="{{ route('seminar-examiner.grading', ['detail' => $detail->id, 'target_examiner_id' => $ex['user']->id, 'redirect_to' => request('redirect_to')]) }}"
                           class="relative p-4 rounded-xl border transition-all flex flex-col justify-between {{ $isSelected ? 'bg-white dark:bg-slate-800 border-indigo-500 shadow-md ring-2 ring-indigo-500/20' : 'bg-white/70 dark:bg-slate-800/50 border-slate-200/80 dark:border-slate-700/60 hover:bg-white dark:hover:bg-slate-800 hover:border-slate-300' }}">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[9px] font-black uppercase tracking-wider {{ $isSelected ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                                        {{ $ex['role_label'] }}
                                    </span>
                                    @if($hasScore)
                                        <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[8px] font-black rounded-md uppercase">
                                            Nilai: {{ number_format($calcScore, 1) }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[8px] font-black rounded-md uppercase">
                                            Belum Diisi
                                        </span>
                                    @endif
                                </div>
                                <div class="font-bold text-xs text-slate-800 dark:text-slate-100 truncate" title="{{ $ex['user']->name }}">
                                    {{ $ex['user']->name }}
                                </div>
                            </div>
                            @if($isSelected)
                                <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-tighter">
                                    <span>Sedang Aktif Dipilih</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Student Info Card -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 dark:bg-indigo-900/10 rounded-full -mr-16 -mt-16"></div>
            <div class="relative flex flex-col md:flex-row items-start md:items-center gap-6">
                <div class="w-16 h-16 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-md">
                    <img src="{{ $detail->thesis?->student?->avatar_url }}" alt="{{ $detail->thesis?->student?->name ?? 'Mahasiswa' }}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $detail->thesis?->student?->name ?? 'Mahasiswa' }}</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $detail->thesis?->student?->identifier ?? '-' }}</p>
                    <p class="text-xs font-bold text-slate-600 dark:text-slate-300 mt-2 italic">"{{ $detail->thesis?->title ?? '-' }}"</p>
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
            <div class="p-6 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest flex items-center">
                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a.75.75 0 00-1.061 0l-1.061 1.06a.75.75 0 101.06 1.061l1.061-1.06a.75.75 0 000-1.061zM6 8a2 2 0 11-4 0 2 2 0 014 0zM22 12a10 10 0 11-20 0 10 10 0 0120 0z"></path></svg>
                    Formulir Penilaian Komponen Seminar
                </h3>
                @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($targetUser))
                    <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                        Penilai: <span class="font-black text-indigo-600 dark:text-indigo-400">{{ $targetUser->name }}</span>
                    </div>
                @endif
            </div>
            
            <style>
                /* Reliable dark mode input styling & hidden spinners */
                input.score-input::-webkit-outer-spin-button,
                input.score-input::-webkit-inner-spin-button {
                    -webkit-appearance: none !important;
                    margin: 0 !important;
                }
                input.score-input {
                    -moz-appearance: textfield !important;
                    appearance: textfield !important;
                }
                .dark input.score-input {
                    background-color: #0f172a !important;
                    color: #ffffff !important;
                    border-color: #334155 !important;
                }
            </style>

            <form action="{{ route('seminar-examiner.store-grading', $detail->id) }}" method="POST" class="p-6 space-y-6" x-data="{
                scores: {
                    presentation: '{{ old('score_presentation', $myRevision->score_presentation ?? '') }}',
                    explanation: '{{ old('score_explanation', $myRevision->score_explanation ?? '') }}',
                    writing: '{{ old('score_writing', $myRevision->score_writing ?? '') }}',
                },
                weights: {
                    presentation: 0.25,
                    explanation: 0.40,
                    writing: 0.35,
                },
                presets: [70, 75, 80, 85, 90, 95],
                get weightedPresentation() {
                    const val = parseFloat(this.scores.presentation) || 0;
                    return (val * this.weights.presentation).toFixed(2);
                },
                get weightedExplanation() {
                    const val = parseFloat(this.scores.explanation) || 0;
                    return (val * this.weights.explanation).toFixed(2);
                },
                get weightedWriting() {
                    const val = parseFloat(this.scores.writing) || 0;
                    return (val * this.weights.writing).toFixed(2);
                },
                get sumScore() {
                    const p = parseFloat(this.scores.presentation) || 0;
                    const e = parseFloat(this.scores.explanation) || 0;
                    const w = parseFloat(this.scores.writing) || 0;
                    return (p + e + w).toFixed(2);
                },
                get avgScore() {
                    const s = parseFloat(this.sumScore);
                    return (s / 3).toFixed(2);
                },
                get finalScore() {
                    const p = (parseFloat(this.scores.presentation) || 0) * this.weights.presentation;
                    const e = (parseFloat(this.scores.explanation) || 0) * this.weights.explanation;
                    const w = (parseFloat(this.scores.writing) || 0) * this.weights.writing;
                    return (p + e + w).toFixed(2);
                },
                get gradeLetter() {
                    const s = parseFloat(this.finalScore);
                    if (s >= 80) return 'A';
                    if (s >= 70) return 'B';
                    if (s >= 60) return 'C';
                    if (s >= 50) return 'D';
                    return 'E';
                },
                setScore(component, value) {
                    this.scores[component] = Math.max(0, Math.min(100, value));
                },
                adjustScore(component, delta) {
                    const current = parseFloat(this.scores[component]) || 0;
                    this.setScore(component, current + delta);
                }
            }">
                @csrf
                @if(isset($actingId))
                    <input type="hidden" name="target_examiner_id" value="{{ $actingId }}">
                @endif
                @if(request('redirect_to'))
                    <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                @endif

                <!-- Actual inputs submitted to backend -->
                <input type="hidden" name="score_presentation" :value="scores.presentation">
                <input type="hidden" name="score_explanation" :value="scores.explanation">
                <input type="hidden" name="score_writing" :value="scores.writing">
                
                <!-- DESKTOP VIEW: Table Layout (Hidden on Mobile) -->
                <div class="hidden md:block overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                    <table class="w-full text-[11px] text-left border-collapse" id="gradingTable">
                        <thead>
                            <tr class="bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-black uppercase tracking-widest">
                                <th class="py-3.5 px-4 border-b border-slate-200 dark:border-slate-700 w-12 text-center">NO</th>
                                <th class="py-3.5 px-4 border-b border-slate-200 dark:border-slate-700">KOMPONEN PENILAIAN SEMINAR</th>
                                <th class="py-3.5 px-4 border-b border-slate-200 dark:border-slate-700 w-24 text-center">Bobot (%)</th>
                                <th class="py-3.5 px-4 border-b border-slate-200 dark:border-slate-700 w-52 text-center">Nilai (0-100)</th>
                                <th class="py-3.5 px-4 border-b border-slate-200 dark:border-slate-700 w-28 text-center">Jumlah</th>
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
                                    <div class="space-y-2 max-w-[190px] mx-auto">
                                        <!-- Stepper Input Row -->
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" 
                                                    @click="adjustScore('presentation', -5)" 
                                                    title="Kurangi 5"
                                                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 font-black text-xs flex items-center justify-center transition-all active:scale-95 shrink-0 shadow-xs">
                                                -5
                                            </button>
                                            <input type="number" 
                                                   id="score_presentation" 
                                                   x-model="scores.presentation" 
                                                   min="0" max="100" 
                                                   class="score-input w-20 py-1.5 px-1 bg-white dark:bg-slate-900 text-slate-800 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl text-center font-black text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-xs" 
                                                   placeholder="0">
                                            <button type="button" 
                                                    @click="adjustScore('presentation', 5)" 
                                                    title="Tambah 5"
                                                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 font-black text-xs flex items-center justify-center transition-all active:scale-95 shrink-0 shadow-xs">
                                                +5
                                            </button>
                                        </div>
                                        <!-- Quick Presets -->
                                        <div class="flex items-center justify-center gap-1 flex-wrap">
                                            <template x-for="val in presets" :key="val">
                                                <button type="button" 
                                                        @click="setScore('presentation', val)" 
                                                        :class="scores.presentation == val ? 'bg-indigo-600 text-white font-black shadow-xs ring-1 ring-indigo-500' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-950/60 dark:hover:text-indigo-300 border border-slate-200/80 dark:border-slate-700/60'" 
                                                        class="px-1.5 py-0.5 rounded-md text-[9px] font-bold transition-all" 
                                                        x-text="val">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center font-black text-slate-700 dark:text-slate-200 align-top text-xs" x-text="weightedPresentation">
                                    0
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
                                    <div class="space-y-2 max-w-[190px] mx-auto">
                                        <!-- Stepper Input Row -->
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" 
                                                    @click="adjustScore('explanation', -5)" 
                                                    title="Kurangi 5"
                                                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 font-black text-xs flex items-center justify-center transition-all active:scale-95 shrink-0 shadow-xs">
                                                -5
                                            </button>
                                            <input type="number" 
                                                   id="score_explanation" 
                                                   x-model="scores.explanation" 
                                                   min="0" max="100" 
                                                   class="score-input w-20 py-1.5 px-1 bg-white dark:bg-slate-900 text-slate-800 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl text-center font-black text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-xs" 
                                                   placeholder="0">
                                            <button type="button" 
                                                    @click="adjustScore('explanation', 5)" 
                                                    title="Tambah 5"
                                                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 font-black text-xs flex items-center justify-center transition-all active:scale-95 shrink-0 shadow-xs">
                                                +5
                                            </button>
                                        </div>
                                        <!-- Quick Presets -->
                                        <div class="flex items-center justify-center gap-1 flex-wrap">
                                            <template x-for="val in presets" :key="val">
                                                <button type="button" 
                                                        @click="setScore('explanation', val)" 
                                                        :class="scores.explanation == val ? 'bg-indigo-600 text-white font-black shadow-xs ring-1 ring-indigo-500' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-950/60 dark:hover:text-indigo-300 border border-slate-200/80 dark:border-slate-700/60'" 
                                                        class="px-1.5 py-0.5 rounded-md text-[9px] font-bold transition-all" 
                                                        x-text="val">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center font-black text-slate-700 dark:text-slate-200 align-top text-xs" x-text="weightedExplanation">
                                    0
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
                                    <div class="space-y-2 max-w-[190px] mx-auto">
                                        <!-- Stepper Input Row -->
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" 
                                                    @click="adjustScore('writing', -5)" 
                                                    title="Kurangi 5"
                                                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 font-black text-xs flex items-center justify-center transition-all active:scale-95 shrink-0 shadow-xs">
                                                -5
                                            </button>
                                            <input type="number" 
                                                   id="score_writing" 
                                                   x-model="scores.writing" 
                                                   min="0" max="100" 
                                                   class="score-input w-20 py-1.5 px-1 bg-white dark:bg-slate-900 text-slate-800 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl text-center font-black text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-xs" 
                                                   placeholder="0">
                                            <button type="button" 
                                                    @click="adjustScore('writing', 5)" 
                                                    title="Tambah 5"
                                                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 font-black text-xs flex items-center justify-center transition-all active:scale-95 shrink-0 shadow-xs">
                                                +5
                                            </button>
                                        </div>
                                        <!-- Quick Presets -->
                                        <div class="flex items-center justify-center gap-1 flex-wrap">
                                            <template x-for="val in presets" :key="val">
                                                <button type="button" 
                                                        @click="setScore('writing', val)" 
                                                        :class="scores.writing == val ? 'bg-indigo-600 text-white font-black shadow-xs ring-1 ring-indigo-500' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-950/60 dark:hover:text-indigo-300 border border-slate-200/80 dark:border-slate-700/60'" 
                                                        class="px-1.5 py-0.5 rounded-md text-[9px] font-bold transition-all" 
                                                        x-text="val">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center font-black text-slate-700 dark:text-slate-200 align-top text-xs" x-text="weightedWriting">
                                    0
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-100/50 dark:bg-slate-800/50 font-bold border-t-2 border-slate-200 dark:border-slate-700">
                            <tr>
                                <td colspan="4" class="py-3 px-6 text-right uppercase tracking-wider text-slate-600 dark:text-slate-400">Jumlah Skor</td>
                                <td class="py-3 px-4 text-center text-slate-800 dark:text-slate-100 font-black text-xs" x-text="sumScore">0</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="py-3 px-6 text-right uppercase tracking-wider text-slate-600 dark:text-slate-400">Rata-rata Skor</td>
                                <td class="py-3 px-4 text-center text-slate-800 dark:text-slate-100 font-black text-xs" x-text="avgScore">0</td>
                            </tr>
                            <tr class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400">
                                <td colspan="4" class="py-3.5 px-6 text-right font-black uppercase tracking-[0.2em] text-[12px]">Nilai Akhir</td>
                                <td class="py-3.5 px-4 text-center font-black text-base">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span x-text="finalScore">0</span>
                                        <span class="text-[10px] px-1.5 py-0.5 bg-indigo-600 text-white rounded font-black" x-text="gradeLetter">A</span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- MOBILE VIEW: Dedicated Responsive Component Cards (Visible on Mobile Only) -->
                <div class="md:hidden space-y-4">
                    <!-- 1. Presentasi Card -->
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700 shadow-xs space-y-3.5">
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-black text-xs flex items-center justify-center">1</span>
                                <h4 class="font-black text-xs uppercase tracking-wider text-slate-800 dark:text-slate-100">Presentasi</h4>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200/70 dark:border-indigo-800">
                                Bobot 25%
                            </span>
                        </div>

                        <ul class="text-[11px] text-slate-500 dark:text-slate-400 space-y-1.5 pl-1">
                            <li class="flex items-start gap-1.5">
                                <span class="text-slate-400 font-bold">a.</span>
                                <span>Sistematika penyajian</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-slate-400 font-bold">b.</span>
                                <span>Penguasaan materi dan kemampuan menjawab</span>
                            </li>
                        </ul>

                        <div class="pt-2 border-t border-dashed border-slate-100 dark:border-slate-700/60 space-y-2.5">
                            <!-- Stepper Row -->
                            <div class="flex items-center justify-center gap-3">
                                <button type="button" 
                                        @click="adjustScore('presentation', -5)" 
                                        class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-sm flex items-center justify-center transition-all active:scale-90 shrink-0">
                                    -5
                                </button>
                                <input type="number" 
                                       x-model="scores.presentation" 
                                       min="0" max="100" 
                                       class="score-input w-28 py-2 px-2 bg-white dark:bg-slate-900 text-slate-800 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl text-center font-black text-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-inner" 
                                       placeholder="0">
                                <button type="button" 
                                        @click="adjustScore('presentation', 5)" 
                                        class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-sm flex items-center justify-center transition-all active:scale-90 shrink-0">
                                    +5
                                </button>
                            </div>
                            <!-- Preset Chips -->
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <template x-for="val in presets" :key="val">
                                    <button type="button" 
                                            @click="setScore('presentation', val)" 
                                            :class="scores.presentation == val ? 'bg-indigo-600 text-white font-black ring-2 ring-indigo-500/50 shadow-xs' : 'bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-700 dark:hover:bg-indigo-950/60 dark:hover:text-indigo-300 border border-slate-200/80 dark:border-slate-700/60'" 
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all active:scale-95" 
                                            x-text="val">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Card Footer Subtotal -->
                        <div class="pt-2 flex items-center justify-between text-xs bg-slate-50/80 dark:bg-slate-900/50 -mx-4 -mb-4 p-3 rounded-b-2xl border-t border-slate-100 dark:border-slate-700/60">
                            <span class="text-slate-400 dark:text-slate-500 font-medium">Subtotal Terbobot:</span>
                            <div class="flex items-center gap-1 font-black text-slate-800 dark:text-slate-100">
                                <span x-text="scores.presentation || 0">0</span>
                                <span class="text-slate-400 font-normal">× 25% =</span>
                                <span class="text-indigo-600 dark:text-indigo-400" x-text="weightedPresentation">0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Materi dan Isi Laporan Card -->
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700 shadow-xs space-y-3.5">
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-black text-xs flex items-center justify-center">2</span>
                                <h4 class="font-black text-xs uppercase tracking-wider text-slate-800 dark:text-slate-100">Materi & Isi Laporan</h4>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200/70 dark:border-indigo-800">
                                Bobot 40%
                            </span>
                        </div>

                        <ul class="text-[11px] text-slate-500 dark:text-slate-400 space-y-1.5 pl-1">
                            <li class="flex items-start gap-1.5">
                                <span class="text-slate-400 font-bold">a.</span>
                                <span>Kesesuaian dengan rumusan masalah</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-slate-400 font-bold">b.</span>
                                <span>Metodologi yang digunakan</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-slate-400 font-bold">c.</span>
                                <span>Kedalaman analisis dan pembahasan</span>
                            </li>
                        </ul>

                        <div class="pt-2 border-t border-dashed border-slate-100 dark:border-slate-700/60 space-y-2.5">
                            <!-- Stepper Row -->
                            <div class="flex items-center justify-center gap-3">
                                <button type="button" 
                                        @click="adjustScore('explanation', -5)" 
                                        class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-sm flex items-center justify-center transition-all active:scale-90 shrink-0">
                                    -5
                                </button>
                                <input type="number" 
                                       x-model="scores.explanation" 
                                       min="0" max="100" 
                                       class="score-input w-28 py-2 px-2 bg-white dark:bg-slate-900 text-slate-800 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl text-center font-black text-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-inner" 
                                       placeholder="0">
                                <button type="button" 
                                        @click="adjustScore('explanation', 5)" 
                                        class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-sm flex items-center justify-center transition-all active:scale-90 shrink-0">
                                    +5
                                </button>
                            </div>
                            <!-- Preset Chips -->
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <template x-for="val in presets" :key="val">
                                    <button type="button" 
                                            @click="setScore('explanation', val)" 
                                            :class="scores.explanation == val ? 'bg-indigo-600 text-white font-black ring-2 ring-indigo-500/50 shadow-xs' : 'bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-700 dark:hover:bg-indigo-950/60 dark:hover:text-indigo-300 border border-slate-200/80 dark:border-slate-700/60'" 
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all active:scale-95" 
                                            x-text="val">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Card Footer Subtotal -->
                        <div class="pt-2 flex items-center justify-between text-xs bg-slate-50/80 dark:bg-slate-900/50 -mx-4 -mb-4 p-3 rounded-b-2xl border-t border-slate-100 dark:border-slate-700/60">
                            <span class="text-slate-400 dark:text-slate-500 font-medium">Subtotal Terbobot:</span>
                            <div class="flex items-center gap-1 font-black text-slate-800 dark:text-slate-100">
                                <span x-text="scores.explanation || 0">0</span>
                                <span class="text-slate-400 font-normal">× 40% =</span>
                                <span class="text-indigo-600 dark:text-indigo-400" x-text="weightedExplanation">0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Kualitas Penulisan Card -->
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700 shadow-xs space-y-3.5">
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-black text-xs flex items-center justify-center">3</span>
                                <h4 class="font-black text-xs uppercase tracking-wider text-slate-800 dark:text-slate-100">Kualitas Penulisan</h4>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200/70 dark:border-indigo-800">
                                Bobot 35%
                            </span>
                        </div>

                        <ul class="text-[11px] text-slate-500 dark:text-slate-400 space-y-1.5 pl-1">
                            <li class="flex items-start gap-1.5">
                                <span class="text-slate-400 font-bold">a.</span>
                                <span>Tata tulis & penggunaan bahasa Indonesia</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-slate-400 font-bold">b.</span>
                                <span>Sitasi dan daftar pustaka</span>
                            </li>
                        </ul>

                        <div class="pt-2 border-t border-dashed border-slate-100 dark:border-slate-700/60 space-y-2.5">
                            <!-- Stepper Row -->
                            <div class="flex items-center justify-center gap-3">
                                <button type="button" 
                                        @click="adjustScore('writing', -5)" 
                                        class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-sm flex items-center justify-center transition-all active:scale-90 shrink-0">
                                    -5
                                </button>
                                <input type="number" 
                                       x-model="scores.writing" 
                                       min="0" max="100" 
                                       class="score-input w-28 py-2 px-2 bg-white dark:bg-slate-900 text-slate-800 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl text-center font-black text-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-inner" 
                                       placeholder="0">
                                <button type="button" 
                                        @click="adjustScore('writing', 5)" 
                                        class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-sm flex items-center justify-center transition-all active:scale-90 shrink-0">
                                    +5
                                </button>
                            </div>
                            <!-- Preset Chips -->
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <template x-for="val in presets" :key="val">
                                    <button type="button" 
                                            @click="setScore('writing', val)" 
                                            :class="scores.writing == val ? 'bg-indigo-600 text-white font-black ring-2 ring-indigo-500/50 shadow-xs' : 'bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-700 dark:hover:bg-indigo-950/60 dark:hover:text-indigo-300 border border-slate-200/80 dark:border-slate-700/60'" 
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all active:scale-95" 
                                            x-text="val">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Card Footer Subtotal -->
                        <div class="pt-2 flex items-center justify-between text-xs bg-slate-50/80 dark:bg-slate-900/50 -mx-4 -mb-4 p-3 rounded-b-2xl border-t border-slate-100 dark:border-slate-700/60">
                            <span class="text-slate-400 dark:text-slate-500 font-medium">Subtotal Terbobot:</span>
                            <div class="flex items-center gap-1 font-black text-slate-800 dark:text-slate-100">
                                <span x-text="scores.writing || 0">0</span>
                                <span class="text-slate-400 font-normal">× 35% =</span>
                                <span class="text-indigo-600 dark:text-indigo-400" x-text="weightedWriting">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clean Sticky Bottom Save & Live Recap Bar -->
                <div class="sticky bottom-4 z-30 bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl p-4 sm:p-5 rounded-2xl border border-slate-200/90 dark:border-slate-700 shadow-xl shadow-slate-900/10 dark:shadow-black/40 transition-all">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <!-- Live Score Summary -->
                        <div class="flex items-center justify-between w-full md:w-auto gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black shadow-xs shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Total Nilai Akhir</span>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-2xl font-black text-slate-800 dark:text-slate-100" x-text="finalScore">0.00</span>
                                        <span class="text-xs font-black text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-950/80 px-2 py-0.5 rounded-md border border-indigo-200 dark:border-indigo-800" x-text="gradeLetter">A</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Quick Component Chips -->
                            <div class="flex md:hidden items-center gap-2 text-[10px] text-slate-500 dark:text-slate-400 font-bold bg-slate-100/80 dark:bg-slate-900/60 px-2.5 py-1.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                <span>P: <b class="text-slate-800 dark:text-slate-200" x-text="scores.presentation || 0"></b></span>
                                <span>•</span>
                                <span>M: <b class="text-slate-800 dark:text-slate-200" x-text="scores.explanation || 0"></b></span>
                                <span>•</span>
                                <span>N: <b class="text-slate-800 dark:text-slate-200" x-text="scores.writing || 0"></b></span>
                            </div>
                        </div>

                        <!-- Desktop Detail Chips -->
                        <div class="hidden md:flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 border-l border-slate-200 dark:border-slate-700 pl-4">
                            <span>Presentasi: <b class="text-slate-800 dark:text-slate-200 font-bold" x-text="scores.presentation || 0"></b></span>
                            <span class="text-slate-300 dark:text-slate-700">•</span>
                            <span>Materi: <b class="text-slate-800 dark:text-slate-200 font-bold" x-text="scores.explanation || 0"></b></span>
                            <span class="text-slate-300 dark:text-slate-700">•</span>
                            <span>Naskah: <b class="text-slate-800 dark:text-slate-200 font-bold" x-text="scores.writing || 0"></b></span>
                        </div>

                        <!-- Action Button -->
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-500/25 dark:shadow-none hover:shadow-indigo-500/40 active:scale-95 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>Simpan Penilaian</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

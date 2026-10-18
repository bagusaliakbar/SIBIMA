<x-app-layout>
    <x-slot name="header">
        @php
            $isStaff = in_array(auth()->user()->role, ['admin', 'kaprodi']);
            $backUrl = match(request('redirect_to')) {
                'monitoring-revisions' => route('monitoring.defense-revisions'),
                'monitoring', 'monitoring-scores' => route('monitoring.defense-scores'),
                default => $isStaff ? route('monitoring.defense-scores') : route('defense-examiner.index'),
            };
            $backLabel = match(request('redirect_to')) {
                'monitoring-revisions' => 'Monitoring Revisi Sidang',
                'monitoring', 'monitoring-scores' => 'Rekap Nilai Sidang',
                default => $isStaff ? 'Rekap Nilai Sidang' : 'Tugas Penguji Sidang',
            };
        @endphp
        <div>
            <x-breadcrumb :items="[
                ['label' => $backLabel, 'route' => $backUrl],
                ['label' => 'Input Nilai Sidang', 'route' => null]
            ]" />
            <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight mt-1">
                Penilaian Sidang Tugas Akhir
            </h2>
            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($targetUser))
                    Mode {{ ucfirst(auth()->user()->role) }}: Mengisi nilai atas nama <span class="font-black text-emerald-600 dark:text-emerald-400 ml-1">{{ $targetUser->name }}</span>
                @else
                    Berikan penilaian berdasarkan komponen yang telah ditetapkan
                @endif
            </p>
        </div>
        <div class="flex items-center shrink-0 mt-3 sm:mt-0">
            <a href="{{ $backUrl }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border border-slate-200/90 dark:border-slate-700 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-xs hover:shadow-sm hover:-translate-x-0.5 group"
               title="Kembali ke {{ $backLabel }}">
                <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200 transition-transform group-hover:-translate-x-0.5 duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali</span>
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-6">

        {{-- Integrated Workflow Navigation Tabs --}}
        <div class="flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-slate-800/60 p-2.5 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs">
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-900/60 rounded-xl border border-slate-200/60 dark:border-slate-800">
                <a href="{{ route('defense-examiner.show', ['detail' => $detail->id, 'target_examiner_id' => request('target_examiner_id'), 'redirect_to' => request('redirect_to')]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold text-xs rounded-lg transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Catatan & Lembar Revisi</span>
                </a>
                <a href="{{ route('defense-examiner.grading', ['detail' => $detail->id, 'target_examiner_id' => request('target_examiner_id'), 'redirect_to' => request('redirect_to')]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 font-black text-xs rounded-lg shadow-xs border border-emerald-500/20">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Formulir Penilaian</span>
                    @if($myRevision && $myRevision->isGraded())
                        <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 text-[8px] font-black rounded uppercase">
                            {{ number_format($myRevision->total_score, 1) }}
                        </span>
                    @endif
                </a>
            </div>
            @if($detail->schedule->meeting_link)
                <a href="{{ $detail->schedule->meeting_link }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-700 dark:text-emerald-300 hover:text-white text-xs font-bold rounded-xl border border-emerald-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span>Join GMeet Sidang</span>
                </a>
            @endif
        </div>

        @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($examiners))
            <!-- Admin / Kaprodi Examiner Selector Card -->
            <div class="bg-gradient-to-r from-emerald-500/10 via-indigo-500/10 to-purple-500/10 border border-emerald-200/60 dark:border-emerald-800/40 rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                            Mode {{ ucfirst(auth()->user()->role) }}
                        </span>
                        <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">
                            Pilih Dosen Penelaah Yang Dinilai
                        </h4>
                    </div>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium italic">
                        *Klik dosen untuk menginput atau memperbarui nilainya
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach($examiners as $ex)
                        @php
                            $isSelected = ($actingId == $ex['user']->id);
                            $hasScore = $ex['revision'] && $ex['revision']->score_presentation !== null;
                            $calcScore = $hasScore ? (($ex['revision']->score_presentation * 0.25) + ($ex['revision']->score_explanation * 0.40) + ($ex['revision']->score_writing * 0.35)) : null;
                        @endphp
                        <a href="{{ route('defense-examiner.grading', ['detail' => $detail->id, 'target_examiner_id' => $ex['user']->id, 'redirect_to' => request('redirect_to')]) }}"
                           class="relative p-4 rounded-xl border transition-all flex flex-col justify-between {{ $isSelected ? 'bg-white dark:bg-slate-800 border-emerald-500 shadow-md ring-2 ring-emerald-500/20' : 'bg-white/70 dark:bg-slate-800/50 border-slate-200/80 dark:border-slate-700/60 hover:bg-white dark:hover:bg-slate-800 hover:border-slate-300' }}">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[9px] font-black uppercase tracking-wider {{ $isSelected ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
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
                                <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-tighter">
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
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 dark:bg-emerald-900/10 rounded-full -mr-16 -mt-16"></div>
            <div class="relative flex flex-col md:flex-row items-start md:items-center gap-6">
                <div class="w-16 h-16 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-md">
                    <img src="{{ $detail->thesis?->student?->avatar_url }}" alt="{{ $detail->thesis?->student?->name ?? 'Mahasiswa' }}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $detail->thesis?->student?->name ?? 'Mahasiswa' }}</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $detail->thesis?->student?->identifier ?? '-' }}</p>
                    <p class="text-xs font-bold text-slate-600 dark:text-slate-300 mt-2 italic">"{{ $detail->thesis?->title ?? '-' }}"</p>
                </div>

                @php
                    $app = $detail->thesis?->defenseApplications?->where('status', '!=', 'rejected')->sortByDesc('id')->first() 
                        ?? $detail->thesis?->defenseApplication
                        ?? $detail->thesis?->seminarApplications?->where('status', '!=', 'rejected')->sortByDesc('id')->first()
                        ?? $detail->thesis?->seminarApplication;
                    $filePath = $app?->file_skripsi;
                    $docUrl = null;
                    if ($filePath) {
                        $docUrl = filter_var($filePath, FILTER_VALIDATE_URL) 
                            ? $filePath 
                            : route('download.private', ['path' => $filePath, 'inline' => 1]);
                    }
                @endphp
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0">
                    @if($docUrl)
                        <a href="{{ $docUrl }}" target="_blank" 
                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-600 text-emerald-700 dark:text-emerald-300 hover:text-white dark:hover:text-white border border-emerald-500/30 text-xs font-bold transition-all shadow-2xs group/doc"
                           title="Buka / Baca Naskah Skripsi Mahasiswa">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 group-hover/doc:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Buka Naskah Skripsi</span>
                            <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    @endif
                    <div class="bg-slate-50 dark:bg-slate-900/50 px-4 py-2.5 rounded-xl border border-slate-100 dark:border-slate-700/50 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-200">
                        <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ \Carbon\Carbon::parse($detail->schedule->date)->format('d F Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Grading Form -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            <div class="p-6 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest flex items-center">
                    <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Formulir Penilaian Komponen Sidang
                </h3>
                @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($targetUser))
                    <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                        Penilai: <span class="font-black text-emerald-600 dark:text-emerald-400">{{ $targetUser->name }}</span>
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

            <form action="{{ route('defense-examiner.store-grading', $detail->id) }}" method="POST" class="p-4 sm:p-6 space-y-6" x-data="{
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
                presets: [60, 65, 70, 75, 80, 85, 90, 95, 100],
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
                get isComplete() {
                    return this.scores.presentation !== '' && this.scores.explanation !== '' && this.scores.writing !== '';
                },
                get gradeLetter() {
                    if (!this.isComplete) return '-';
                    const s = parseFloat(this.finalScore);
                    if (s >= 80) return 'A';
                    if (s >= 70) return 'B';
                    if (s >= 60) return 'C';
                    if (s >= 50) return 'D';
                    return 'E';
                },
                get gradeBadgeClass() {
                    if (!this.isComplete) {
                        return 'bg-slate-700/60 text-slate-300 border border-slate-600/60';
                    }
                    const letter = this.gradeLetter;
                    if (letter === 'A') return 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/30 ring-2 ring-emerald-500/30';
                    if (letter === 'B') return 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 ring-2 ring-sky-500/30';
                    if (letter === 'C') return 'bg-amber-600 text-white shadow-sm shadow-amber-600/30 ring-2 ring-amber-500/30';
                    return 'bg-rose-600 text-white shadow-sm shadow-rose-600/30 ring-2 ring-rose-500/30';
                },
                setScore(component, value) {
                    this.scores[component] = Math.max(0, Math.min(100, Math.round(value)));
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

                <!-- Dynamic Real-Time Scorecard Header Banner -->
                <div class="bg-gradient-to-br from-slate-900 via-slate-850 to-slate-900 text-white dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-800 shadow-lg relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative z-10">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    Touch-Friendly Scoring
                                </span>
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs text-slate-300 font-bold">
                                    Bobot: 25% + 40% + 35% = 100%
                                </span>
                            </div>
                            <h4 class="text-base sm:text-lg font-black text-white tracking-tight">
                                Rubrik Penilaian Sidang Tugas Akhir
                            </h4>
                            <p class="text-xs text-slate-400">
                                Ketuk tombol pil skor untuk penilaian cepat dan akurat di tablet/iPad.
                            </p>
                        </div>

                        <!-- Live Score Hero -->
                        <div class="flex items-center gap-4 bg-white/5 dark:bg-black/20 p-3 sm:p-4 rounded-2xl border border-white/10 shrink-0">
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Nilai Huruf</span>
                                <div class="mt-1">
                                    <span class="inline-flex items-center justify-center min-w-[38px] h-8 px-3 rounded-xl text-base font-black uppercase tracking-wider transition-all"
                                          :class="gradeBadgeClass"
                                          x-text="gradeLetter">
                                        -
                                    </span>
                                </div>
                            </div>
                            <div class="border-l border-white/10 pl-4">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Nilai Akhir</span>
                                <span class="text-2xl sm:text-3xl font-black text-emerald-400 tracking-tight" x-text="finalScore">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Touch-Friendly Scoring Cards Grid -->
                <div class="space-y-5">

                    <!-- ========================================== -->
                    <!-- CARD 1: PRESENTASI (Bobot 25%) -->
                    <!-- ========================================== -->
                    <div x-data="{ showRubric: false }"
                         class="bg-white dark:bg-slate-800/90 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 p-5 sm:p-6 shadow-xs space-y-4 transition-all hover:border-slate-300 dark:hover:border-slate-600">
                        
                        <!-- Card Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-xs">
                                    1
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-black text-sm sm:text-base text-slate-800 dark:text-slate-100 uppercase tracking-tight">
                                            Presentasi
                                        </h4>
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Bobot 25%
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                        Sistematika penyajian materi dan penggunaan bahasa saat pemaparan sidang
                                    </p>
                                </div>
                            </div>

                            <!-- Live Subtotal Calculation -->
                            <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/60 px-3.5 py-2 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shrink-0 self-start sm:self-auto">
                                <span class="text-[11px] text-slate-400 font-medium">Subtotal Terbobot:</span>
                                <div class="text-xs font-black text-slate-800 dark:text-slate-100 flex items-center gap-1">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="scores.presentation || 0">0</span>
                                    <span class="text-slate-400 font-normal">× 25% =</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-black text-sm" x-text="weightedPresentation">0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Collapsible Rubric Guidance -->
                        <div>
                            <button type="button" 
                                    @click="showRubric = !showRubric"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors cursor-pointer">
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showRubric }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                <span x-text="showRubric ? 'Sembunyikan Kriteria Rubrik' : 'Lihat Kriteria Rubrik Penilaian (2 Indikator)'"></span>
                            </button>

                            <div x-show="showRubric" x-collapse x-cloak class="mt-2.5 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300 space-y-1.5">
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-emerald-600">a.</span>
                                    <span>Penyajian materi presentasi (kejelasan slide, intonasi, penguasaan urutan presentasi).</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-emerald-600">b.</span>
                                    <span>Penggunaan bahasa Indonesia yang baik, lugas, santun, dan komunikatif.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Pills & Stepper Touch Controls -->
                        <div class="space-y-3 pt-1">
                            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                Ketuk Skor Pil Cepat:
                            </label>
                            
                            <!-- Finger-Friendly Pills -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <template x-for="val in presets" :key="val">
                                    <button type="button" 
                                            @click="setScore('presentation', val)"
                                            :class="scores.presentation == val 
                                                ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-black shadow-md shadow-emerald-500/25 ring-2 ring-emerald-500/50 scale-105' 
                                                : 'bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700/80 hover:border-emerald-500 hover:text-emerald-600 font-bold'"
                                            class="py-2.5 px-3.5 sm:px-4 sm:py-3 rounded-2xl text-xs sm:text-sm font-mono min-w-[50px] sm:min-w-[56px] text-center transition-all duration-150 active:scale-95 cursor-pointer">
                                        <span x-text="val"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Stepper & Direct Custom Number Field -->
                            <div class="flex items-center gap-2 pt-2 flex-wrap">
                                <span class="text-xs text-slate-400 font-medium mr-1">Penyesuaian Manual:</span>
                                <button type="button" 
                                        @click="adjustScore('presentation', -5)" 
                                        title="Kurangi 5"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    -5
                                </button>
                                <button type="button" 
                                        @click="adjustScore('presentation', -1)" 
                                        title="Kurangi 1"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    -1
                                </button>
                                
                                <div class="relative">
                                    <input type="number" 
                                           id="score_presentation" 
                                           x-model="scores.presentation" 
                                           min="0" max="100" 
                                           placeholder="0"
                                           class="score-input w-24 h-10 bg-white dark:bg-slate-900 text-slate-800 dark:text-white border-2 border-slate-300 dark:border-slate-700 rounded-xl text-center font-black text-base focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-inner">
                                </div>

                                <button type="button" 
                                        @click="adjustScore('presentation', 1)" 
                                        title="Tambah 1"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    +1
                                </button>
                                <button type="button" 
                                        @click="adjustScore('presentation', 5)" 
                                        title="Tambah 5"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    +5
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- CARD 2: KEMAMPUAN MENJELASKAN (Bobot 40%) -->
                    <!-- ========================================== -->
                    <div x-data="{ showRubric: false }"
                         class="bg-white dark:bg-slate-800/90 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 p-5 sm:p-6 shadow-xs space-y-4 transition-all hover:border-slate-300 dark:hover:border-slate-600">
                        
                        <!-- Card Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-xs">
                                    2
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-black text-sm sm:text-base text-slate-800 dark:text-slate-100 uppercase tracking-tight">
                                            Kemampuan Menjelaskan Naskah Skripsi
                                        </h4>
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Bobot 40%
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                        Penguasaan materi teori, kerangka berpikir, instrumen analisis, dan argumentasi jawaban
                                    </p>
                                </div>
                            </div>

                            <!-- Live Subtotal Calculation -->
                            <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/60 px-3.5 py-2 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shrink-0 self-start sm:self-auto">
                                <span class="text-[11px] text-slate-400 font-medium">Subtotal Terbobot:</span>
                                <div class="text-xs font-black text-slate-800 dark:text-slate-100 flex items-center gap-1">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="scores.explanation || 0">0</span>
                                    <span class="text-slate-400 font-normal">× 40% =</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-black text-sm" x-text="weightedExplanation">0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Collapsible Rubric Guidance -->
                        <div>
                            <button type="button" 
                                    @click="showRubric = !showRubric"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors cursor-pointer">
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showRubric }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                <span x-text="showRubric ? 'Sembunyikan Kriteria Rubrik' : 'Lihat Kriteria Rubrik Penilaian (5 Indikator)'"></span>
                            </button>

                            <div x-show="showRubric" x-collapse x-cloak class="mt-2.5 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300 space-y-1.5">
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-emerald-600">a.</span>
                                    <span>Relevansi teori yang digunakan dengan masalah penelitian yang diangkat.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-emerald-600">b.</span>
                                    <span>Argumentasi teoritis yang dibangun dalam penyusunan kerangka berpikir.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-emerald-600">c.</span>
                                    <span>Kedalaman dan keluasan penguasaan teori keilmuan yang relevan.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-emerald-600">d.</span>
                                    <span>Teknik pengumpulan data dan keabsahan instrumen analisis data.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-emerald-600">e.</span>
                                    <span>Pembahasan hasil penelitian, penarikan kesimpulan serta ketajaman saran.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Pills & Stepper Touch Controls -->
                        <div class="space-y-3 pt-1">
                            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                Ketuk Skor Pil Cepat:
                            </label>
                            
                            <!-- Finger-Friendly Pills -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <template x-for="val in presets" :key="val">
                                    <button type="button" 
                                            @click="setScore('explanation', val)"
                                            :class="scores.explanation == val 
                                                ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-black shadow-md shadow-emerald-500/25 ring-2 ring-emerald-500/50 scale-105' 
                                                : 'bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700/80 hover:border-emerald-500 hover:text-emerald-600 font-bold'"
                                            class="py-2.5 px-3.5 sm:px-4 sm:py-3 rounded-2xl text-xs sm:text-sm font-mono min-w-[50px] sm:min-w-[56px] text-center transition-all duration-150 active:scale-95 cursor-pointer">
                                        <span x-text="val"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Stepper & Direct Custom Number Field -->
                            <div class="flex items-center gap-2 pt-2 flex-wrap">
                                <span class="text-xs text-slate-400 font-medium mr-1">Penyesuaian Manual:</span>
                                <button type="button" 
                                        @click="adjustScore('explanation', -5)" 
                                        title="Kurangi 5"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    -5
                                </button>
                                <button type="button" 
                                        @click="adjustScore('explanation', -1)" 
                                        title="Kurangi 1"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    -1
                                </button>
                                
                                <div class="relative">
                                    <input type="number" 
                                           id="score_explanation" 
                                           x-model="scores.explanation" 
                                           min="0" max="100" 
                                           placeholder="0"
                                           class="score-input w-24 h-10 bg-white dark:bg-slate-900 text-slate-800 dark:text-white border-2 border-slate-300 dark:border-slate-700 rounded-xl text-center font-black text-base focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-inner">
                                </div>

                                <button type="button" 
                                        @click="adjustScore('explanation', 1)" 
                                        title="Tambah 1"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    +1
                                </button>
                                <button type="button" 
                                        @click="adjustScore('explanation', 5)" 
                                        title="Tambah 5"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    +5
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- CARD 3: PENULISAN NASKAH (Bobot 35%) -->
                    <!-- ========================================== -->
                    <div x-data="{ showRubric: false }"
                         class="bg-white dark:bg-slate-800/90 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 p-5 sm:p-6 shadow-xs space-y-4 transition-all hover:border-slate-300 dark:hover:border-slate-600">
                        
                        <!-- Card Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-xs">
                                    3
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-black text-sm sm:text-base text-slate-800 dark:text-slate-100 uppercase tracking-tight">
                                            Penulisan Naskah Skripsi
                                        </h4>
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Bobot 35%
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                        Struktur tata tulis ilmiah, logika penulisan, sitasi standar, dan orisinalitas karya
                                    </p>
                                </div>
                            </div>

                            <!-- Live Subtotal Calculation -->
                            <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/60 px-3.5 py-2 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 shrink-0 self-start sm:self-auto">
                                <span class="text-[11px] text-slate-400 font-medium">Subtotal Terbobot:</span>
                                <div class="text-xs font-black text-slate-800 dark:text-slate-100 flex items-center gap-1">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="scores.writing || 0">0</span>
                                    <span class="text-slate-400 font-normal">× 35% =</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-black text-sm" x-text="weightedWriting">0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Collapsible Rubric Guidance -->
                        <div>
                            <button type="button" 
                                    @click="showRubric = !showRubric"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors cursor-pointer">
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showRubric }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                <span x-text="showRubric ? 'Sembunyikan Kriteria Rubrik' : 'Lihat Kriteria Rubrik Penilaian (2 Indikator)'"></span>
                            </button>

                            <div x-show="showRubric" x-collapse x-cloak class="mt-2.5 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300 space-y-1.5">
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-emerald-600">a.</span>
                                    <span>Struktur penulisan, kerapian format baku, tata bahasa akademik, logika alur bahasan.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-emerald-600">b.</span>
                                    <span>Orisinalitas ide dan bebas dari indikasi plagiarisme (similarity test).</span>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Pills & Stepper Touch Controls -->
                        <div class="space-y-3 pt-1">
                            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                Ketuk Skor Pil Cepat:
                            </label>
                            
                            <!-- Finger-Friendly Pills -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <template x-for="val in presets" :key="val">
                                    <button type="button" 
                                            @click="setScore('writing', val)"
                                            :class="scores.writing == val 
                                                ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-black shadow-md shadow-emerald-500/25 ring-2 ring-emerald-500/50 scale-105' 
                                                : 'bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700/80 hover:border-emerald-500 hover:text-emerald-600 font-bold'"
                                            class="py-2.5 px-3.5 sm:px-4 sm:py-3 rounded-2xl text-xs sm:text-sm font-mono min-w-[50px] sm:min-w-[56px] text-center transition-all duration-150 active:scale-95 cursor-pointer">
                                        <span x-text="val"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Stepper & Direct Custom Number Field -->
                            <div class="flex items-center gap-2 pt-2 flex-wrap">
                                <span class="text-xs text-slate-400 font-medium mr-1">Penyesuaian Manual:</span>
                                <button type="button" 
                                        @click="adjustScore('writing', -5)" 
                                        title="Kurangi 5"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    -5
                                </button>
                                <button type="button" 
                                        @click="adjustScore('writing', -1)" 
                                        title="Kurangi 1"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    -1
                                </button>
                                
                                <div class="relative">
                                    <input type="number" 
                                           id="score_writing" 
                                           x-model="scores.writing" 
                                           min="0" max="100" 
                                           placeholder="0"
                                           class="score-input w-24 h-10 bg-white dark:bg-slate-900 text-slate-800 dark:text-white border-2 border-slate-300 dark:border-slate-700 rounded-xl text-center font-black text-base focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-inner">
                                </div>

                                <button type="button" 
                                        @click="adjustScore('writing', 1)" 
                                        title="Tambah 1"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    +1
                                </button>
                                <button type="button" 
                                        @click="adjustScore('writing', 5)" 
                                        title="Tambah 5"
                                        class="h-10 px-3 rounded-xl bg-slate-100 dark:bg-slate-700/70 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-black text-xs transition-all active:scale-90 shadow-2xs">
                                    +5
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Clean Sticky Bottom Save & Live Dynamic Recap Bar -->
                <div class="sticky bottom-4 z-30 bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl p-4 sm:p-5 rounded-3xl border border-slate-200/90 dark:border-slate-700 shadow-2xl shadow-slate-900/15 dark:shadow-black/50 transition-all">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                        <!-- Live Score & Dynamic Status Badge -->
                        <div class="flex items-center justify-between w-full lg:w-auto gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black shadow-xs shrink-0 border border-emerald-500/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Nilai Akhir</span>
                                    </div>
                                    <div class="flex items-baseline gap-2 mt-0.5">
                                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight" x-text="finalScore">0.00</span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">Huruf:</span>
                                            <span class="text-xs font-black px-2.5 py-0.5 rounded-lg transition-all"
                                                  :class="gradeBadgeClass"
                                                  x-text="gradeLetter">A</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Quick Component Chips -->
                            <div class="flex lg:hidden items-center gap-1.5 text-[10px] text-slate-500 dark:text-slate-400 font-bold bg-slate-100/80 dark:bg-slate-900/60 px-2.5 py-1.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                <span>P: <b class="text-slate-800 dark:text-slate-200" x-text="scores.presentation || 0"></b></span>
                                <span>•</span>
                                <span>M: <b class="text-slate-800 dark:text-slate-200" x-text="scores.explanation || 0"></b></span>
                                <span>•</span>
                                <span>N: <b class="text-slate-800 dark:text-slate-200" x-text="scores.writing || 0"></b></span>
                            </div>
                        </div>

                        <!-- Desktop Detail Chips -->
                        <div class="hidden lg:flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 border-l border-slate-200 dark:border-slate-700 pl-4">
                            <span>Presentasi: <b class="text-slate-800 dark:text-slate-200 font-bold" x-text="scores.presentation || 0"></b> <span class="text-[10px] text-slate-400">(<span x-text="weightedPresentation"></span>)</span></span>
                            <span class="text-slate-300 dark:text-slate-700">•</span>
                            <span>Materi: <b class="text-slate-800 dark:text-slate-200 font-bold" x-text="scores.explanation || 0"></b> <span class="text-[10px] text-slate-400">(<span x-text="weightedExplanation"></span>)</span></span>
                            <span class="text-slate-300 dark:text-slate-700">•</span>
                            <span>Naskah: <b class="text-slate-800 dark:text-slate-200 font-bold" x-text="scores.writing || 0"></b> <span class="text-[10px] text-slate-400">(<span x-text="weightedWriting"></span>)</span></span>
                        </div>

                        <!-- Action Button -->
                        <div class="flex items-center gap-3 w-full lg:w-auto">
                            <button type="submit" class="w-full lg:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>Simpan Penilaian Sidang</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

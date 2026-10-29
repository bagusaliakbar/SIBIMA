@php
    $stageInfo = $thesis->getCurrentAcademicStage();
    $hasSeminar = $thesis->hasPassedSeminar();
    $hasDefense = $thesis->hasPassedDefense();

    $completedCount = (int) $thesis->completed_sessions_count;
    $isP1 = ($thesis->pembimbing1_id === Auth::id());
    $isP2 = ($thesis->pembimbing2_id === Auth::id());
    $hasMyAccUp = $isP1 ? (bool) $thesis->acc_up_p1 : (bool) $thesis->acc_up_p2;
    $hasMyAccSidang = $isP1 ? (bool) $thesis->acc_sidang_p1 : (bool) $thesis->acc_sidang_p2;
    $isAccUpFinal = $thesis->acc_up_p1 && $thesis->acc_up_p2;
    $isAccSidangFinal = $thesis->acc_sidang_p1 && $thesis->acc_sidang_p2;

    $isGraduated = ($thesis->status === 'completed');

    $upTarget = 8;
    $sidangTarget = 16;
    $upPercent = $isGraduated ? 100 : min(100, round(($completedCount / $upTarget) * 100));
    $sidangPercent = $isGraduated ? 100 : min(100, round(($completedCount / $sidangTarget) * 100));

    $isTargetSidang = $hasSeminar;
    $p1Acc = $isTargetSidang ? $thesis->acc_sidang_p1 : $thesis->acc_up_p1;
    $p2Acc = $isTargetSidang ? $thesis->acc_sidang_p2 : $thesis->acc_up_p2;
    $targetAccLabel = $isTargetSidang ? 'Sidang' : 'Sempro';

    $p1Count = $thesis->mentoringSessions ? $thesis->mentoringSessions->where('dosen_id', $thesis->pembimbing1_id)->count() : 0;
    $p2Count = $thesis->mentoringSessions ? $thesis->mentoringSessions->where('dosen_id', $thesis->pembimbing2_id)->count() : 0;
    $isMeP1 = (Auth::id() === $thesis->pembimbing1_id);
    $isMeP2 = (Auth::id() === $thesis->pembimbing2_id);

    $lastSession = $thesis->mentoringSessions ? $thesis->mentoringSessions->first() : null;
    $lastDate = ($lastSession && $lastSession->scheduled_at) 
        ? \Carbon\Carbon::parse($lastSession->scheduled_at) 
        : ($thesis->created_at ? \Carbon\Carbon::parse($thesis->created_at) : null);

    if ($lastDate) {
        $daysSinceLast = (int) $lastDate->copy()->startOfDay()->diffInDays(now()->startOfDay());
        if ($lastDate->isToday()) {
            $lastActivityText = 'Hari ini';
        } elseif ($lastDate->isYesterday()) {
            $lastActivityText = 'Kemarin';
        } else {
            $lastActivityText = "{$daysSinceLast}h lalu";
        }
    } else {
        $daysSinceLast = null;
        $lastActivityText = 'Aktif';
    }

    $waNumber = \App\Helpers\PhoneHelper::formatForWhatsApp($thesis->student->phone_number);
    $dosenName = Auth::user()->name;
    $roleLabel = $isP1 ? 'Pembimbing 1' : ($isP2 ? 'Pembimbing 2' : 'Dosen');
    $waMessage = urlencode("Halo {$thesis->student->name}, saya {$dosenName} ({$roleLabel}). Terkait logbook bimbingan skripsi: \"" . ($thesis->final_title ?? $thesis->title) . "\"");

    $rawTitle = trim($thesis->final_title ?? $thesis->title ?? '');
    $titleUpper = strtoupper($rawTitle);
    $hasValidTitle = !empty($rawTitle) && !in_array($titleUpper, ['BELUM DIKETAHUI', 'BELUM DITENTUKAN', 'BELUM ADA JUDUL', 'BELUM ADA', '-']) && strlen($rawTitle) >= 8;
    $simScore = $hasValidTitle ? $thesis->getMaxSimilarityScore() : 0;
    $simMatches = $hasValidTitle ? $thesis->getDetailedSimilarityMatches() : collect();
@endphp

<div class="p-4 sm:p-5 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors space-y-3.5" x-data="{ openAbstractMobile: false }">
    <!-- Header Kartu: Avatar, Nama, NPM, dan Tombol WhatsApp -->
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="relative w-11 h-11 rounded-full overflow-hidden flex items-center justify-center border border-slate-200/80 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0 ring-2 ring-white dark:ring-slate-800 shadow-2xs">
                <img src="{{ $thesis->student->avatar_url }}" alt="{{ $thesis->student->name }}" class="w-full h-full object-cover">
            </div>
            <div class="min-w-0">
                <a href="{{ route('theses.logbooks', $thesis->id) }}" class="font-bold text-slate-900 dark:text-white tracking-tight text-sm hover:text-orange-600 dark:hover:text-orange-400 transition-colors block truncate" title="{{ $thesis->student->name }}">
                    {{ ucwords(strtolower($thesis->student->name)) }}
                </a>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80">
                        {{ $thesis->student->identifier ?? 'NPM -' }}
                    </span>
                    <span class="text-slate-300 dark:text-slate-700">•</span>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">
                        {{ $isP1 ? 'P1 (Anda)' : ($isP2 ? 'P2 (Anda)' : 'Dosen') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tombol Chat WA -->
        @if($waNumber)
            <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" 
               target="_blank" 
               rel="noopener noreferrer"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shrink-0 shadow-2xs transition-all active:scale-95 cursor-pointer">
                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 fill-current shrink-0" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                <span>WA</span>
            </a>
        @endif
    </div>

    <!-- Status Tahap & Keaktifan Bimbingan -->
    <div class="flex items-center justify-between gap-2 flex-wrap pt-1 border-t border-slate-100 dark:border-slate-800">
        <!-- Tahap Akademik Badge -->
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $stageInfo['badge_class'] }} border shadow-2xs tracking-tight">
            <span class="w-1.5 h-1.5 rounded-full {{ $stageInfo['dot_class'] }} shrink-0"></span>
            <span>{{ $stageInfo['label'] }}</span>
        </span>

        <!-- Keaktifan / Milestone Status -->
        <div>
            @if($isGraduated)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800">
                    🎓 Lulus
                </span>
            @elseif(($completedCount >= 16 || ($p1Count >= 8 && $p2Count >= 8)) && !$hasMyAccSidang)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800">
                    🟢 Siap Sidang
                </span>
            @elseif(($completedCount >= 8 || ($p1Count >= 4 && $p2Count >= 4)) && !$hasMyAccUp)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800">
                    🔵 Siap UP
                </span>
            @elseif($completedCount === 0)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800">
                    <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                    <span>0 Sesi</span>
                </span>
            @elseif($daysSinceLast !== null && $daysSinceLast > 14)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800"
                      title="Bimbingan terakhir: {{ $lastDate ? $lastDate->isoFormat('D MMMM Y') : '-' }}">
                    <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                    <span>Pasif ({{ $daysSinceLast }}h)</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800">
                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    <span>{{ $lastActivityText }}</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Judul Skripsi -->
    <div class="space-y-1">
        <a href="{{ route('theses.logbooks', $thesis->id) }}" class="font-bold text-slate-800 dark:text-slate-100 text-xs leading-relaxed line-clamp-3 block tracking-tight hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
            {{ $thesis->final_title ?? $thesis->title }}
        </a>
        @if($thesis->final_title)
            <p class="text-[10px] text-slate-400 dark:text-slate-500 italic line-clamp-1">
                ↳ Awal: {{ $thesis->title }}
            </p>
        @endif
    </div>

    <!-- Metadata Judul: Similarity Badge & Abstract Modal Trigger -->
    <div class="flex items-center gap-2 flex-wrap text-[10px]">
        @if($hasValidTitle)
            <button @click='$dispatch("open-audit-modal", {
                student: "{{ addslashes($thesis->student->name) }}",
                npm: "{{ $thesis->student->identifier }}",
                title: "{{ addslashes($rawTitle) }}",
                score: {{ $simScore }},
                matches: {{ json_encode($simMatches->values()->toArray()) }}
            })' class="group focus:outline-hidden cursor-pointer">
                @if($simScore >= 66)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-bold bg-rose-500/10 text-rose-700 dark:text-rose-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping shrink-0"></span>
                        <span>{{ $simScore }}% Mirip</span>
                    </span>
                @elseif($simScore >= 35)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-bold bg-amber-500/10 text-amber-700 dark:text-amber-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                        <span>{{ $simScore }}% Mirip</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <span>{{ 100 - $simScore }}% Unik</span>
                    </span>
                @endif
            </button>
        @endif

        @if($thesis->abstract)
            <button @click="openAbstractMobile = true" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold flex items-center gap-0.5 cursor-pointer">
                <span>Lihat Deskripsi</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        @endif
    </div>

    @if($thesis->abstract)
        <!-- Abstract Bottom Sheet Modal for Mobile -->
        <template x-teleport="body">
            <div x-show="openAbstractMobile" class="fixed inset-0 z-50 flex flex-col justify-end sm:justify-center items-center p-0 sm:p-4" x-cloak x-transition>
                <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/75 backdrop-blur-xs" @click="openAbstractMobile = false"></div>
                <div class="w-full bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-2xl border-t border-x sm:border border-slate-200 dark:border-slate-800 max-h-[85vh] overflow-hidden flex flex-col relative z-10 pb-[max(1rem,env(safe-area-inset-bottom))] sm:pb-0 sm:max-w-xl shadow-2xl">
                    <div class="sm:hidden w-12 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto my-2.5 shrink-0"></div>
                    <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">Detail Deskripsi Skripsi</h3>
                        <button @click="openAbstractMobile = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="p-5 overflow-y-auto space-y-4 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-orange-600 uppercase tracking-widest block mb-1">Judul</span>
                            <p class="font-bold text-slate-900 dark:text-white uppercase leading-snug">{{ $thesis->final_title ?? $thesis->title }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-700">
                            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest block mb-2">Deskripsi / Rencana</span>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-justify whitespace-pre-line">{{ $thesis->abstract }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    @endif

    <!-- Pembimbing & Rincian Sesi -->
    <div class="grid grid-cols-2 gap-2 text-[10px] p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-700/60">
        <!-- P1 -->
        <div class="space-y-0.5">
            <div class="flex items-center gap-1">
                <span class="px-1 py-0.2 rounded font-black text-[9px] bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200">P1</span>
                @if($isMeP1)
                    <span class="text-[9px] font-bold text-orange-600">(Anda)</span>
                @endif
                <span class="font-mono text-slate-400">({{ $p1Count }} sesi)</span>
            </div>
            <p class="font-semibold text-slate-700 dark:text-slate-300 truncate" title="{{ $thesis->pembimbing1?->name ?? 'Belum Ditugaskan' }}">
                {{ $thesis->pembimbing1?->name ?? 'Belum Ada' }}
            </p>
        </div>

        <!-- P2 -->
        <div class="space-y-0.5">
            <div class="flex items-center gap-1">
                <span class="px-1 py-0.2 rounded font-black text-[9px] bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200">P2</span>
                @if($isMeP2)
                    <span class="text-[9px] font-bold text-orange-600">(Anda)</span>
                @endif
                <span class="font-mono text-slate-400">({{ $p2Count }} sesi)</span>
            </div>
            <p class="font-semibold text-slate-700 dark:text-slate-300 truncate" title="{{ $thesis->pembimbing2?->name ?? 'Belum Ditugaskan' }}">
                {{ $thesis->pembimbing2?->name ?? 'Belum Ada' }}
            </p>
        </div>
    </div>

    <!-- Progress Target Bimbingan (UP & Sidang) -->
    <div class="space-y-2 p-3 bg-slate-50/80 dark:bg-slate-900/60 rounded-xl border border-slate-200/70 dark:border-slate-800">
        <!-- Target UP -->
        <div class="space-y-1">
            <div class="flex items-center justify-between text-[10px] font-bold">
                <span class="text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $upPercent >= 100 || $hasMyAccUp ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>Target Sempro (8 Sesi)</span>
                </span>
                <span class="font-mono {{ $completedCount >= 8 ? 'text-indigo-600 font-black' : 'text-slate-500' }}">
                    {{ min($completedCount, 8) }}/8 Sesi ({{ $upPercent }}%)
                </span>
            </div>
            <div class="w-full bg-slate-200/80 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                <div class="h-full rounded-full bg-indigo-600 dark:bg-indigo-500 transition-all duration-300" style="width: {{ $upPercent }}%;"></div>
            </div>
        </div>

        <!-- Target Sidang -->
        <div class="space-y-1">
            <div class="flex items-center justify-between text-[10px] font-bold">
                <span class="text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $sidangPercent >= 100 || $hasMyAccSidang ? 'bg-emerald-600' : 'bg-slate-300' }}"></span>
                    <span>Target Sidang (16 Sesi)</span>
                </span>
                <span class="font-mono {{ $completedCount >= 16 ? 'text-emerald-600 font-black' : 'text-slate-500' }}">
                    {{ min($completedCount, 16) }}/16 Sesi ({{ $sidangPercent }}%)
                </span>
            </div>
            <div class="w-full bg-slate-200/80 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                <div class="h-full rounded-full bg-emerald-600 dark:bg-emerald-500 transition-all duration-300" style="width: {{ $sidangPercent }}%;"></div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi Mobile Ergonomis (Touch Target Min-Height 42px) -->
    <div class="pt-1 flex items-center gap-2">
        <!-- Tombol Intip Riwayat -->
        <button type="button" 
                @click="$dispatch('open-logbook-preview', { thesisId: {{ $thesis->id }} })"
                class="h-10 px-3.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-2xs flex items-center justify-center gap-1.5 active:scale-95 cursor-pointer shrink-0"
                title="Intip Riwayat Bimbingan Terakhir">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <span>Intip</span>
        </button>

        <!-- Tombol Buka Logbook Lengkap (Full-Width Touch Area) -->
        <a href="{{ route('theses.logbooks', $thesis->id) }}" 
           class="h-10 flex-1 flex items-center justify-center gap-2 px-4 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer">
            <span>Buka Logbook Bimbingan</span>
            <svg class="w-3.5 h-3.5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>

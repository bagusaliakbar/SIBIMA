@php
    $stageInfo = $thesis->getCurrentAcademicStage();
    $completedCount = (int) $thesis->completed_sessions_count;
    $waNumber = \App\Helpers\PhoneHelper::formatForWhatsApp($thesis->student->phone_number);
    $dosenName = Auth::user()->name;
    $waMessage = urlencode("Halo {$thesis->student->name}, terkait status pengajuan skripsi: \"" . ($thesis->final_title ?? $thesis->title) . "\"");

    $rawTitle = trim($thesis->final_title ?? $thesis->title ?? '');
    $titleUpper = strtoupper($rawTitle);
    $hasValidTitle = !empty($rawTitle) && !in_array($titleUpper, ['BELUM DIKETAHUI', 'BELUM DITENTUKAN', 'BELUM ADA JUDUL', 'BELUM ADA', '-']) && strlen($rawTitle) >= 8;
    $simScore = $hasValidTitle ? $thesis->getMaxSimilarityScore() : 0;
    $simMatches = $hasValidTitle ? $thesis->getDetailedSimilarityMatches() : collect();
@endphp

<div class="p-4 sm:p-5 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors space-y-3.5" x-data="{ openAbstractAdmin: false }">
    <!-- Header Kartu: Avatar, Nama, NPM, dan Tombol WhatsApp -->
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('theses.logbooks', $thesis->id) }}" class="relative w-11 h-11 rounded-full overflow-hidden flex items-center justify-center border border-slate-200/80 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0 ring-2 ring-white dark:ring-slate-800 shadow-2xs hover:scale-105 transition-transform">
                <img src="{{ $thesis->student->avatar_url }}" alt="{{ $thesis->student->name }}" class="w-full h-full object-cover">
            </a>
            <div class="min-w-0">
                <a href="{{ route('theses.logbooks', $thesis->id) }}" class="font-bold text-slate-900 dark:text-white tracking-tight text-sm hover:text-orange-600 dark:hover:text-orange-400 transition-colors block truncate" title="{{ $thesis->student->name }}">
                    {{ ucwords(strtolower($thesis->student->name)) }}
                </a>
                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80">
                        {{ $thesis->student->identifier ?? 'NPM -' }}
                    </span>

                    <!-- Cohort Badge -->
                    @if($thesis->student->entry_year)
                        @if($thesis->isOldCohort())
                            @if($thesis->student->is_critical_semester)
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-700 dark:text-rose-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    Lama '{{ substr($thesis->student->entry_year, -2) }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Lama '{{ substr($thesis->student->entry_year, -2) }}
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300">
                                '{{ substr($thesis->student->entry_year, -2) }}
                            </span>
                        @endif
                    @endif
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

    <!-- Status & Progres Badge -->
    <div class="flex items-center justify-between gap-2 flex-wrap pt-1 border-t border-slate-100 dark:border-slate-800">
        <!-- Status Badge -->
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $stageInfo['badge_class'] }} border shadow-2xs">
            <span class="w-1.5 h-1.5 rounded-full {{ $stageInfo['dot_class'] }} shrink-0"></span>
            <span>{{ $stageInfo['label'] }}</span>
        </span>

        <!-- Sesi Bimbingan Count -->
        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">
            {{ $completedCount }} Sesi Bimbingan
        </span>
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

    <!-- Similarity Score -->
    @if($hasValidTitle)
        <div class="flex items-center gap-2 text-[10px]">
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
        </div>
    @endif

    <!-- Pembimbing Ditugaskan -->
    <div class="grid grid-cols-2 gap-2 text-[10px] p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-700/60">
        <div>
            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Pembimbing 1</span>
            <span class="font-bold text-slate-700 dark:text-slate-300 truncate block" title="{{ $thesis->pembimbing1?->name ?? 'Belum Ada' }}">
                {{ $thesis->pembimbing1?->name ?? 'Belum Ada' }}
            </span>
        </div>
        <div>
            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Pembimbing 2</span>
            <span class="font-bold text-slate-700 dark:text-slate-300 truncate block" title="{{ $thesis->pembimbing2?->name ?? 'Belum Ada' }}">
                {{ $thesis->pembimbing2?->name ?? 'Belum Ada' }}
            </span>
        </div>
    </div>

    <!-- Tombol Aksi Admin / Kaprodi (Touch Target Min-Height 42px) -->
    <div class="pt-1 flex items-center gap-2">
        @if(!$thesis->pembimbing1_id && !$thesis->pembimbing2_id)
            <!-- Belum Ada Pembimbing: Tombol Tunggal Tugaskan -->
            <button type="button" 
                    @click="$dispatch('open-assign-modal', { thesisId: {{ $thesis->id }} })"
                    class="h-10 w-full flex items-center justify-center gap-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Tugaskan Pembimbing</span>
            </button>
        @else
            <!-- Rollback Button -->
            <button type="button"
                    @click="openRollbackModal('{{ $thesis->id }}', '{{ addslashes($thesis->student->name ?? 'Mahasiswa') }}', '{{ addslashes($thesis->pembimbing1?->name ?? '-') }}', '{{ addslashes($thesis->pembimbing2?->name ?? '-') }}')" 
                    class="h-10 px-3 bg-white dark:bg-slate-800 hover:bg-rose-50 text-rose-600 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-2xs active:scale-95 cursor-pointer shrink-0"
                    title="Rollback Penugasan Pembimbing">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </button>

            <!-- Edit Button -->
            <a href="{{ route('theses.edit', $thesis->id) }}" 
               class="h-10 px-3.5 bg-white dark:bg-slate-800 hover:bg-slate-50 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-2xs flex items-center justify-center gap-1.5 active:scale-95 cursor-pointer shrink-0"
               title="Edit Data Skripsi">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Edit</span>
            </a>

            <!-- Logbook Button (Primary Full-Width) -->
            <a href="{{ route('theses.logbooks', $thesis->id) }}" 
               class="h-10 flex-1 flex items-center justify-center gap-2 px-4 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer">
                <span>Buka Logbook</span>
                <svg class="w-3.5 h-3.5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        @endif
    </div>
</div>


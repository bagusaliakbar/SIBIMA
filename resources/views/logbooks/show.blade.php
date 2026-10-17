<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ Auth::user()->role === 'mahasiswa' ? route('logbooks.index') : route('theses.index') }}" 
               class="w-9 h-9 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-orange-50 dark:hover:bg-orange-950/40 hover:text-orange-600 dark:hover:text-orange-400 hover:border-orange-200 dark:hover:border-orange-800 transition-all shadow-xs" 
               title="Kembali ke {{ Auth::user()->role === 'mahasiswa' ? 'Logbook Mandiri' : 'Daftar Mahasiswa Bimbingan' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-slate-100 tracking-tight">
                    Monitoring Logbook Bimbingan
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                    Pantau seluruh arahan, catatan revisi, dan kemajuan bimbingan skripsi mahasiswa.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- 1. KARTU INFORMASI MAHASISWA, SKRIPSI, DAN PROGRESS TARGET (BANNER ATAS) -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-7 border border-slate-200/90 dark:border-slate-700 shadow-xs space-y-6">
            <!-- Top Row: Tag, Mahasiswa Info, Kontak WA & Status Skripsi -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0 shadow-2xs">
                        <img src="{{ $thesis->student->avatar_url }}" alt="{{ $thesis->student->name }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="text-base font-black text-slate-900 dark:text-slate-100">{{ $thesis->student->name }}</h3>
                            @php
                                $waNumber = \App\Helpers\PhoneHelper::formatForWhatsApp($thesis->student->phone_number);
                                $dosenName = Auth::user()->name;
                                $roleLabel = ($thesis->pembimbing1_id === Auth::id()) ? 'Pembimbing 1' : (($thesis->pembimbing2_id === Auth::id()) ? 'Pembimbing 2' : 'Dosen');
                                $waMessage = urlencode("Halo {$thesis->student->name}, saya {$dosenName} ({$roleLabel}). Terkait logbook bimbingan skripsi: \"" . ($thesis->final_title ?? $thesis->title) . "\"");
                            @endphp
                            @if($waNumber)
                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl text-[10px] font-bold transition-all shadow-2xs hover:scale-105"
                                   title="Kirim Pesan WhatsApp ke {{ $thesis->student->name }} ({{ $thesis->student->phone_number }})">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 fill-current shrink-0" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    <span>WhatsApp</span>
                                </a>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                            {{ $thesis->student->identifier ?? 'NPM -' }}
                            @if($thesis->topic)
                                • <span class="font-sans font-medium text-slate-700 dark:text-slate-300">Bidang: {{ $thesis->topic }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Status Skripsi:</span>
                    <x-status-badge type="orange" :label="strtoupper($thesis->status)" />
                </div>
            </div>

            <!-- Judul Skripsi -->
            <div class="space-y-1.5">
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Judul Skripsi:</span>
                <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-slate-100 leading-relaxed">
                    {{ $thesis->final_title ?? ($thesis->title ?: 'Judul skripsi belum diatur') }}
                </h2>
            </div>

            <!-- Dosen Pembimbing & Progress Grid (Sama seperti Halaman Mahasiswa) -->
            @php
                $targetPerDosen = 8;
                $targetTotal = 16;

                // Perhitungan Pembimbing 1
                $progressP1 = min(100, round(($p1SessionCount / $targetPerDosen) * 100));
                $isP1Reached = $p1SessionCount >= $targetPerDosen;
                $remainingP1 = max(0, $targetPerDosen - $p1SessionCount);

                // Perhitungan Pembimbing 2
                $progressP2 = min(100, round(($p2SessionCount / $targetPerDosen) * 100));
                $isP2Reached = $p2SessionCount >= $targetPerDosen;
                $remainingP2 = max(0, $targetPerDosen - $p2SessionCount);

                // Perhitungan Total
                $progressPercent = min(100, round(($totalCompletedCount / $targetTotal) * 100));
                $isTargetReached = ($p1SessionCount >= $targetPerDosen && $p2SessionCount >= $targetPerDosen);
                $remainingTotal = max(0, $targetTotal - $totalCompletedCount);
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 pt-2">
                <!-- Pembimbing 1 -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700 flex flex-col justify-between gap-4">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-wider border border-indigo-200 dark:border-indigo-800">
                                Pembimbing 1
                            </span>
                            <span class="text-xs font-black {{ $isP1Reached ? 'text-emerald-600 dark:text-emerald-400' : 'text-indigo-600 dark:text-indigo-400' }}">
                                {{ $p1SessionCount }} / {{ $targetPerDosen }} Sesi
                            </span>
                        </div>

                        @if($thesis->pembimbing1)
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0">
                                    <img src="{{ $thesis->pembimbing1->avatar_url }}" alt="{{ $thesis->pembimbing1->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-black text-slate-900 dark:text-slate-100 truncate flex items-center gap-1">
                                        <span>{{ $thesis->pembimbing1->name }}</span>
                                        @if($thesis->pembimbing1_id === Auth::id())
                                            <span class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-950/60 px-1 rounded">(Anda)</span>
                                        @endif
                                    </h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $thesis->pembimbing1->identifier ?: 'NIP/NIDN -' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic mb-3">Belum ditentukan</p>
                        @endif

                        <!-- Mini Progress Bar P1 -->
                        <div class="space-y-1">
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-indigo-600 dark:bg-indigo-500 h-full rounded-full transition-all duration-500" style="width: {{ $progressP1 }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                                <span>Target: 8 Sesi</span>
                                <span class="{{ $isP1Reached ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-600 dark:text-slate-300 font-semibold' }}">
                                    {{ $isP1Reached ? '✓ Target Terpenuhi' : ($remainingP1 . ' sesi lagi') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ACC Badges P1 -->
                    <div class="pt-3 border-t border-slate-200/60 dark:border-slate-700 flex items-center gap-2 flex-wrap text-[10px]">
                        @if($thesis->acc_up_p1)
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-black uppercase">
                                ✓ ACC Seminar
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 font-semibold">
                                Belum ACC Seminar
                            </span>
                        @endif

                        @if($thesis->acc_sidang_p1)
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-black uppercase">
                                ✓ ACC Sidang
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 font-semibold">
                                Belum ACC Sidang
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Pembimbing 2 -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700 flex flex-col justify-between gap-4">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 text-[10px] font-black uppercase tracking-wider border border-purple-200 dark:border-purple-800">
                                Pembimbing 2
                            </span>
                            <span class="text-xs font-black {{ $isP2Reached ? 'text-emerald-600 dark:text-emerald-400' : 'text-purple-600 dark:text-purple-400' }}">
                                {{ $p2SessionCount }} / {{ $targetPerDosen }} Sesi
                            </span>
                        </div>

                        @if($thesis->pembimbing2)
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0">
                                    <img src="{{ $thesis->pembimbing2->avatar_url }}" alt="{{ $thesis->pembimbing2->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-black text-slate-900 dark:text-slate-100 truncate flex items-center gap-1">
                                        <span>{{ $thesis->pembimbing2->name }}</span>
                                        @if($thesis->pembimbing2_id === Auth::id())
                                            <span class="text-[9px] font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-950/60 px-1 rounded">(Anda)</span>
                                        @endif
                                    </h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $thesis->pembimbing2->identifier ?: 'NIP/NIDN -' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic mb-3">Belum ditentukan</p>
                        @endif

                        <!-- Mini Progress Bar P2 -->
                        <div class="space-y-1">
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-purple-600 dark:bg-purple-500 h-full rounded-full transition-all duration-500" style="width: {{ $progressP2 }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                                <span>Target: 8 Sesi</span>
                                <span class="{{ $isP2Reached ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-600 dark:text-slate-300 font-semibold' }}">
                                    {{ $isP2Reached ? '✓ Target Terpenuhi' : ($remainingP2 . ' sesi lagi') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ACC Badges P2 -->
                    <div class="pt-3 border-t border-slate-200/60 dark:border-slate-700 flex items-center gap-2 flex-wrap text-[10px]">
                        @if($thesis->acc_up_p2)
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-black uppercase">
                                ✓ ACC Seminar
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 font-semibold">
                                Belum ACC Seminar
                            </span>
                        @endif

                        @if($thesis->acc_sidang_p2)
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-black uppercase">
                                ✓ ACC Sidang
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 font-semibold">
                                Belum ACC Sidang
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Progress Keseluruhan (Target 16 Sesi) -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-orange-200/80 dark:border-orange-900/40 flex flex-col justify-between gap-3">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-wider">
                                Total Sesi Bimbingan
                            </span>
                            <span class="text-xs font-black text-slate-900 dark:text-slate-100">
                                {{ $totalCompletedCount }} / {{ $targetTotal }} Sesi
                            </span>
                        </div>

                        <!-- Progress bar -->
                        <div class="w-full bg-slate-200 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden mb-2">
                            <div class="bg-gradient-to-r from-orange-500 to-amber-500 h-full rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                        </div>

                        <div class="flex items-center justify-between text-[11px] font-bold">
                            <span class="text-slate-500 dark:text-slate-400">Pencapaian:</span>
                            <span class="{{ $isTargetReached ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }} font-black">
                                {{ $progressPercent }}% {{ $isTargetReached ? '(Target Terpenuhi)' : '' }}
                            </span>
                        </div>

                        <!-- Rincian P1 & P2 Badges -->
                        <div class="grid grid-cols-2 gap-2 mt-2.5 pt-2.5 border-t border-slate-200/60 dark:border-slate-700/60 text-[10px]">
                            <div class="flex items-center justify-between px-2 py-1 rounded-lg bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/50">
                                <span class="font-bold text-indigo-700 dark:text-indigo-300">P1:</span>
                                <span class="font-black text-indigo-900 dark:text-indigo-100">{{ $p1SessionCount }}/8 Sesi</span>
                            </div>
                            <div class="flex items-center justify-between px-2 py-1 rounded-lg bg-purple-50/70 dark:bg-purple-950/40 border border-purple-100 dark:border-purple-900/50">
                                <span class="font-bold text-purple-700 dark:text-purple-300">P2:</span>
                                <span class="font-black text-purple-900 dark:text-purple-100">{{ $p2SessionCount }}/8 Sesi</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2.5 border-t border-slate-200/80 dark:border-slate-700 text-[10px] text-slate-600 dark:text-slate-300 font-medium">
                        @if($isTargetReached)
                            <span class="text-emerald-700 dark:text-emerald-300 font-bold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Target 16 sesi bimbingan (8x P1 & 8x P2) telah terpenuhi.
                            </span>
                        @else
                            <span class="text-slate-500 dark:text-slate-400">
                                Tersisa <strong>{{ $remainingTotal }} sesi lagi</strong> untuk target 16 sesi (8x P1 & 8x P2).
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            @php
                $isAdminOrKaprodi = in_array(Auth::user()->role, ['admin', 'kaprodi']);
                $isP1 = Auth::id() === $thesis->pembimbing1_id;
                $isP2 = Auth::id() === $thesis->pembimbing2_id;
                $canToggleAcc = $isAdminOrKaprodi || $isP1 || $isP2;
                
                $hasAccUp = $isAdminOrKaprodi 
                    ? ($thesis->acc_up_p1 && $thesis->acc_up_p2) 
                    : ($isP1 ? $thesis->acc_up_p1 : ($isP2 ? $thesis->acc_up_p2 : false));
                    
                $hasAccSidang = $isAdminOrKaprodi 
                    ? ($thesis->acc_sidang_p1 && $thesis->acc_sidang_p2) 
                    : ($isP1 ? $thesis->acc_sidang_p1 : ($isP2 ? $thesis->acc_sidang_p2 : false));

                $isFullyAccUp = $thesis->acc_up_p1 && $thesis->acc_up_p2;
                $isFullyAccSidang = $thesis->acc_sidang_p1 && $thesis->acc_sidang_p2;
            @endphp

            <!-- Integrated ACC & Exam Readiness Bar -->
            <div class="pt-5 border-t border-slate-100 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 border border-orange-500/20 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">Status Persetujuan Ujian (ACC)</h4>
                            @if($isFullyAccUp && $isFullyAccSidang)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    Lengkap (UP & Sidang)
                                </span>
                            @elseif($isFullyAccUp)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                                    ACC Seminar Lengkap
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            @if($canToggleAcc)
                                Berikan persetujuan bimbingan agar mahasiswa dapat mendaftar Seminar Proposal atau Sidang Skripsi.
                            @else
                                Status persetujuan dari kedua Dosen Pembimbing untuk pendaftaran Seminar dan Sidang.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    {{-- ACC SEMINAR (UP) GROUP --}}
                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/60 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                        @if($canToggleAcc)
                            <form action="{{ route('theses.toggle-acc', [$thesis->id, 'up']) }}" method="POST" class="inline"
                                onsubmit="return confirm('Apakah Anda yakin ingin {{ $hasAccUp ? 'membatalkan' : 'memberikan' }} ACC Seminar Proposal untuk {{ addslashes($thesis->student->name) }}?')">
                                @csrf
                                @if($isAdminOrKaprodi)
                                    <input type="hidden" name="slot" value="all">
                                @endif
                                <button type="submit" 
                                    title="{{ $hasAccUp ? 'Klik untuk membatalkan ACC Seminar' : 'Klik untuk memberikan ACC Seminar' }}"
                                    class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold tracking-wide transition-all shadow-xs
                                    {{ $hasAccUp 
                                        ? 'bg-emerald-600 text-white hover:bg-emerald-700 active:bg-emerald-800' 
                                        : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 dark:hover:bg-emerald-950/40 dark:hover:border-emerald-800' }}">
                                    <svg class="w-3.5 h-3.5 mr-1.5 {{ $hasAccUp ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>ACC SEMINAR</span>
                                </button>
                            </form>
                        @else
                            <div class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold rounded-xl {{ $isFullyAccUp ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                <svg class="w-3.5 h-3.5 mr-1.5 {{ $isFullyAccUp ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>ACC SEMINAR</span>
                            </div>
                        @endif

                        {{-- Status P1 & P2 Indicator Dots with Tooltip --}}
                        <div class="flex items-center gap-1.5 border-l border-slate-200 dark:border-slate-700 pl-2.5 pr-1.5">
                            @if($isAdminOrKaprodi)
                                <form action="{{ route('theses.toggle-acc', [$thesis->id, 'up']) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Toggle ACC Seminar P1 untuk {{ addslashes($thesis->student->name) }}?')">
                                    @csrf
                                    <input type="hidden" name="slot" value="p1">
                                    <button type="submit" class="flex items-center gap-1 cursor-pointer hover:opacity-80" title="Klik untuk toggle status ACC Seminar P1">
                                        <span class="text-[10px] font-mono {{ $thesis->acc_up_p1 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-400 dark:text-slate-500' }}">P1</span>
                                        <div class="w-2.5 h-2.5 rounded-full {{ $thesis->acc_up_p1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                    </button>
                                </form>
                                <form action="{{ route('theses.toggle-acc', [$thesis->id, 'up']) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Toggle ACC Seminar P2 untuk {{ addslashes($thesis->student->name) }}?')">
                                    @csrf
                                    <input type="hidden" name="slot" value="p2">
                                    <button type="submit" class="flex items-center gap-1 ml-1 cursor-pointer hover:opacity-80" title="Klik untuk toggle status ACC Seminar P2">
                                        <span class="text-[10px] font-mono {{ $thesis->acc_up_p2 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-400 dark:text-slate-500' }}">P2</span>
                                        <div class="w-2.5 h-2.5 rounded-full {{ $thesis->acc_up_p2 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center gap-1" title="Pembimbing 1: {{ $thesis->acc_up_p1 ? 'Sudah ACC' : 'Belum ACC' }}">
                                    <span class="text-[10px] font-mono {{ $thesis->acc_up_p1 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-400 dark:text-slate-500' }}">P1</span>
                                    <div class="w-2.5 h-2.5 rounded-full {{ $thesis->acc_up_p1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                </div>
                                <div class="flex items-center gap-1 ml-1" title="Pembimbing 2: {{ $thesis->acc_up_p2 ? 'Sudah ACC' : 'Belum ACC' }}">
                                    <span class="text-[10px] font-mono {{ $thesis->acc_up_p2 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-400 dark:text-slate-500' }}">P2</span>
                                    <div class="w-2.5 h-2.5 rounded-full {{ $thesis->acc_up_p2 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ACC SIDANG GROUP --}}
                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/60 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                        @if($canToggleAcc)
                            <form action="{{ route('theses.toggle-acc', [$thesis->id, 'sidang']) }}" method="POST" class="inline"
                                onsubmit="return confirm('Apakah Anda yakin ingin {{ $hasAccSidang ? 'membatalkan' : 'memberikan' }} ACC Sidang Akhir untuk {{ addslashes($thesis->student->name) }}?')">
                                @csrf
                                @if($isAdminOrKaprodi)
                                    <input type="hidden" name="slot" value="all">
                                @endif
                                <button type="submit" 
                                    title="{{ $hasAccSidang ? 'Klik untuk membatalkan ACC Sidang' : 'Klik untuk memberikan ACC Sidang' }}"
                                    class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold tracking-wide transition-all shadow-xs
                                    {{ $hasAccSidang 
                                        ? 'bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800' 
                                        : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 dark:hover:bg-indigo-950/40 dark:hover:border-indigo-800' }}">
                                    <svg class="w-3.5 h-3.5 mr-1.5 {{ $hasAccSidang ? 'text-white' : 'text-indigo-600 dark:text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>ACC SIDANG</span>
                                </button>
                            </form>
                        @else
                            <div class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold rounded-xl {{ $isFullyAccSidang ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                <svg class="w-3.5 h-3.5 mr-1.5 {{ $isFullyAccSidang ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>ACC SIDANG</span>
                            </div>
                        @endif

                        {{-- Status P1 & P2 Indicator Dots with Tooltip --}}
                        <div class="flex items-center gap-1.5 border-l border-slate-200 dark:border-slate-700 pl-2.5 pr-1.5">
                            @if($isAdminOrKaprodi)
                                <form action="{{ route('theses.toggle-acc', [$thesis->id, 'sidang']) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Toggle ACC Sidang P1 untuk {{ addslashes($thesis->student->name) }}?')">
                                    @csrf
                                    <input type="hidden" name="slot" value="p1">
                                    <button type="submit" class="flex items-center gap-1 cursor-pointer hover:opacity-80" title="Klik untuk toggle status ACC Sidang P1">
                                        <span class="text-[10px] font-mono {{ $thesis->acc_sidang_p1 ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-400 dark:text-slate-500' }}">P1</span>
                                        <div class="w-2.5 h-2.5 rounded-full {{ $thesis->acc_sidang_p1 ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                    </button>
                                </form>
                                <form action="{{ route('theses.toggle-acc', [$thesis->id, 'sidang']) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Toggle ACC Sidang P2 untuk {{ addslashes($thesis->student->name) }}?')">
                                    @csrf
                                    <input type="hidden" name="slot" value="p2">
                                    <button type="submit" class="flex items-center gap-1 ml-1 cursor-pointer hover:opacity-80" title="Klik untuk toggle status ACC Sidang P2">
                                        <span class="text-[10px] font-mono {{ $thesis->acc_sidang_p2 ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-400 dark:text-slate-500' }}">P2</span>
                                        <div class="w-2.5 h-2.5 rounded-full {{ $thesis->acc_sidang_p2 ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center gap-1" title="Pembimbing 1: {{ $thesis->acc_sidang_p1 ? 'Sudah ACC' : 'Belum ACC' }}">
                                    <span class="text-[10px] font-mono {{ $thesis->acc_sidang_p1 ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-400 dark:text-slate-500' }}">P1</span>
                                    <div class="w-2.5 h-2.5 rounded-full {{ $thesis->acc_sidang_p1 ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                </div>
                                <div class="flex items-center gap-1 ml-1" title="Pembimbing 2: {{ $thesis->acc_sidang_p2 ? 'Sudah ACC' : 'Belum ACC' }}">
                                    <span class="text-[10px] font-mono {{ $thesis->acc_sidang_p2 ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-400 dark:text-slate-500' }}">P2</span>
                                    <div class="w-2.5 h-2.5 rounded-full {{ $thesis->acc_sidang_p2 ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. JADWAL BIMBINGAN AKTIF / MENDATANG (JIKA ADA) -->
        @if($activeSessions->isNotEmpty())
            <div class="bg-amber-500/10 border border-amber-500/25 rounded-3xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">
                                Jadwal Bimbingan Mendatang / Menunggu Konfirmasi ({{ $activeSessions->count() }})
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400">
                                Sesi bimbingan yang telah dijadwalkan atau sedang berlangsung.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($activeSessions as $session)
                        @php
                            $isP1Active = ($session->dosen_id === $thesis->pembimbing1_id);
                            $isP2Active = ($session->dosen_id === $thesis->pembimbing2_id);
                            $isMyActive = ($session->dosen_id === Auth::id());
                            $activeRoleTag = $isP1Active ? 'Pembimbing 1' : ($isP2Active ? 'Pembimbing 2' : 'Dosen');
                            $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                            $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                            $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                        @endphp
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-amber-200/80 dark:border-amber-500/30 p-4 shadow-2xs space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-black text-amber-700 dark:text-amber-400">
                                    {{ $session->scheduled_at->locale('id')->translatedFormat('d M Y • H:i') }} WIB
                                </span>
                                <x-status-badge :type="$session->status === 'approved' ? 'orange' : 'amber'" :label="strtoupper($session->status)" />
                            </div>

                            <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-slate-100 line-clamp-2">
                                {{ $session->topic }}
                            </h4>

                            <div class="flex items-center gap-2 text-[11px] font-bold">
                                @if($session->type === 'online')
                                    @if($session->location)
                                        <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            <span>{{ $isMeet ? 'Google Meet' : ($isZoom ? 'Zoom' : 'Tautan Online') }}</span>
                                        </a>
                                    @else
                                        <span class="text-blue-600 dark:text-blue-400">Online</span>
                                    @endif
                                @else
                                    <span class="text-slate-600 dark:text-slate-300">🏢 {{ $session->location ?: 'Tatap Muka' }}</span>
                                @endif
                            </div>

                            @if($session->notes)
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 italic bg-slate-50 dark:bg-slate-900 p-2 rounded-xl border border-slate-100 dark:border-slate-700">
                                    "{{ $session->notes }}"
                                </p>
                            @endif

                            @if($session->dosen)
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-[10px]">
                                    <span class="font-bold text-slate-700 dark:text-slate-300">
                                        {{ $session->dosen->name }} {{ $isMyActive ? '(Anda)' : '' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded font-black {{ $isP1Active ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300' : 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' }}">
                                        {{ $activeRoleTag }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. KARTU UTAMA RIWAYAT CATATAN LOGBOOK (FULL WIDTH, SEARCH, EXPORT, TIMELINE) -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/90 dark:border-slate-700 overflow-hidden">
            <!-- Header Bar -->
            <div class="p-6 border-b border-slate-200/80 dark:border-slate-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-50 dark:bg-slate-800">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Riwayat Catatan Logbook</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                        Dokumentasi resmi seluruh arahan, revisi, dan hasil bimbingan skripsi mahasiswa.
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full md:w-auto" style="display: flex !important; align-items: center !important;">
                    <!-- Search Input (mencakup topik, catatan mhs, feedback dosen) -->
                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari topik, catatan, atau revisi..." 
                        :params="['dosen' => ($filterDosen ?? 'all') !== 'all' ? $filterDosen : null]" />

                    <!-- Export PDF Button (Modern SIBIMA Style) -->
                    <a href="{{ route('theses.logbooks.export-pdf', array_filter(['thesis' => $thesis->id, 'dosen' => ($filterDosen ?? 'all') !== 'all' ? $filterDosen : null])) }}" 
                       target="_blank"
                       class="h-10 inline-flex items-center justify-center gap-2 px-4 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider transition-all shadow-xs shrink-0 cursor-pointer" 
                       style="height: 40px !important; margin: 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; box-sizing: border-box !important;"
                       title="Unduh seluruh catatan logbook mahasiswa dalam format PDF">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-6 4h4"/>
                        </svg>
                        <span>Export PDF</span>
                    </a>
                </div>
            </div>

            <!-- Filter Pembimbing Tabs -->
            @if($thesis->pembimbing1 || $thesis->pembimbing2)
                <div class="px-6 py-3.5 bg-slate-100/70 dark:bg-slate-900/70 border-b border-slate-200/80 dark:border-slate-700 flex items-center gap-2 overflow-x-auto">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 shrink-0 mr-1">
                        Filter Pembimbing:
                    </span>

                    <!-- Tab Semua -->
                    <a href="{{ route('theses.logbooks', array_filter(['thesis' => $thesis->id, 'search' => $search, 'dosen' => 'all'])) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($filterDosen ?? 'all') === 'all' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                        <span>Semua Pembimbing</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ ($filterDosen ?? 'all') === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                            {{ $totalCompletedCount }}
                        </span>
                    </a>

                    <!-- Tab P1 -->
                    @if($thesis->pembimbing1)
                        <a href="{{ route('theses.logbooks', array_filter(['thesis' => $thesis->id, 'search' => $search, 'dosen' => 'p1'])) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($filterDosen ?? 'all') === 'p1' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                            <span>Pembimbing 1 ({{ Str::limit($thesis->pembimbing1->name, 22) }}){{ $thesis->pembimbing1_id === Auth::id() ? ' • Anda' : '' }}</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ ($filterDosen ?? 'all') === 'p1' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                {{ $p1SessionCount }}
                            </span>
                        </a>
                    @endif

                    <!-- Tab P2 -->
                    @if($thesis->pembimbing2)
                        <a href="{{ route('theses.logbooks', array_filter(['thesis' => $thesis->id, 'search' => $search, 'dosen' => 'p2'])) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($filterDosen ?? 'all') === 'p2' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                            <span>Pembimbing 2 ({{ Str::limit($thesis->pembimbing2->name, 22) }}){{ $thesis->pembimbing2_id === Auth::id() ? ' • Anda' : '' }}</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[10px] font-black {{ ($filterDosen ?? 'all') === 'p2' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                {{ $p2SessionCount }}
                            </span>
                        </a>
                    @endif
                </div>
            @endif

            <!-- 4. DAFTAR RIWAYAT LOGBOOK -->
            <div class="p-6 bg-slate-50/60 dark:bg-slate-900/60">
                @if($completedSessions->isEmpty())
                    @if($search || ($filterDosen ?? 'all') !== 'all')
                        <div class="py-12 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Tidak ada catatan bimbingan yang cocok</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                                Coba sesuaikan kata kunci pencarian atau pilih filter pembimbing yang berbeda.
                            </p>
                            <a href="{{ route('theses.logbooks', $thesis->id) }}" class="inline-flex items-center mt-4 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                Reset Filter
                            </a>
                        </div>
                    @else
                        <x-empty-state description="Mahasiswa ini belum memiliki riwayat catatan bimbingan yang telah diselesaikan oleh dosen pembimbing." icon="logbook" />
                    @endif
                @else
                    <!-- Daftar Kartu Sesi Logbook (Modern Timeline Cards) -->
                    <div class="space-y-6">
                        @foreach($completedSessions as $session)
                            @php
                                $seqNumber = $sessionOrderMap[$session->id] ?? ($loop->count - $loop->index);
                                $dosenSeq = $sessionDosenOrderMap[$session->id] ?? null;
                                
                                $isP1 = $thesis && $session->dosen_id === $thesis->pembimbing1_id;
                                $isP2 = $thesis && $session->dosen_id === $thesis->pembimbing2_id;
                                $isMySession = ($session->dosen_id === Auth::id());
                                $pembimbingLabel = $isP1 ? 'Pembimbing 1' : ($isP2 ? 'Pembimbing 2' : 'Dosen Pembimbing');

                                $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                                $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                                $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                            @endphp

                            <!-- Session Card Content -->
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/90 dark:border-slate-700 p-5 sm:p-6 transition-all hover:border-orange-300 dark:hover:border-orange-500/40 shadow-xs dark:shadow-none space-y-4">
                                <!-- Card Header: Badges & Info -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <!-- Bimbingan #N Pill -->
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-500 text-white text-xs font-black shadow-xs shadow-orange-500/20">
                                            Bimbingan #{{ $seqNumber }}
                                        </span>

                                        @if($dosenSeq)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/80 text-xs font-bold shadow-2xs">
                                                {{ $isP1 ? 'P1' : ($isP2 ? 'P2' : 'Dosen') }} ke-{{ $dosenSeq }}
                                            </span>
                                        @endif

                                        <!-- Waktu Bimbingan -->
                                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-300 font-bold">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }} • {{ $session->scheduled_at->format('H:i') }} WIB</span>
                                        </span>

                                        <!-- Metode & Lokasi -->
                                        @if($session->type === 'online')
                                            @if($session->location)
                                                <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl {{ $isMeet ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : ($isZoom ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800') }} transition-all font-bold text-xs shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                    <span>{{ $isMeet ? 'Google Meet' : ($isZoom ? 'Zoom Meeting' : 'Online') }}</span>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-xl border border-indigo-200 dark:border-indigo-800 text-xs font-bold shadow-2xs">
                                                    🎥 Daring
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-600 text-xs font-bold shadow-2xs">
                                                🏢 {{ $session->location ?: 'Tatap Muka' }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="shrink-0">
                                        <x-status-badge type="emerald" label="SELESAI" />
                                    </div>
                                </div>

                                <!-- Topik Sesi -->
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 block mb-1">Topik Diskusi / Bahasan:</span>
                                    <h4 class="text-base sm:text-lg font-black text-slate-900 dark:text-slate-100 leading-snug">
                                        {{ $session->topic }}
                                    </h4>
                                </div>

                                <!-- Hasil & Catatan Pembimbing (Highlight Box) -->
                                <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/60 dark:bg-slate-900/80 border border-amber-200/80 dark:border-amber-500/30 space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <h5 class="text-[10px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            <span>Hasil & Catatan Pembimbing</span>
                                        </h5>
                                        @if(!$isMySession && Auth::user()->role === 'dosen')
                                            <span class="text-[10px] font-bold text-amber-700 dark:text-amber-400 bg-amber-100/70 dark:bg-amber-950/60 px-2 py-0.5 rounded-lg border border-amber-200/60 dark:border-amber-800">
                                                Catatan {{ $pembimbingLabel }} ({{ $session->dosen->name ?? '-' }})
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed font-medium whitespace-pre-line italic">
                                        {{ $session->feedback ?: 'Tidak ada catatan pembimbing tertulis untuk sesi ini.' }}
                                    </p>
                                    @if($session->feedback_document_url)
                                        <div class="pt-2 border-t border-amber-200/50 dark:border-amber-500/20">
                                            <a href="{{ $session->feedback_document_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                <span>Unduh Berkas Koreksi / Feedback Dosen</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <!-- Catatan Mahasiswa (Jika Ada) -->
                                @if($session->notes)
                                    <div class="p-4 sm:p-5 bg-slate-50/90 dark:bg-slate-900/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-3 shadow-2xs">
                                        <div class="flex items-center gap-2 pb-2.5 border-b border-slate-200/70 dark:border-slate-700/70">
                                            <div class="w-5 h-5 rounded-md bg-slate-200/80 dark:bg-slate-800 flex items-center justify-center shrink-0 text-slate-500">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Catatan Pengajuan Mahasiswa</span>
                                        </div>
                                        <div class="px-0.5">
                                            <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal whitespace-pre-line">
                                                {{ $session->notes }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Dokumen Draft Lampiran Mahasiswa (Jika Ada) -->
                                @if($session->document_path)
                                    <div class="pt-1">
                                        <a href="{{ filter_var($session->document_path, FILTER_VALIDATE_URL) ? $session->document_path : route('download.private', ['path' => $session->document_path]) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 text-xs font-bold transition-all shadow-2xs group/doc">
                                            <svg class="w-3.5 h-3.5 text-indigo-500 group-hover/doc:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            <span>Unduh Draft Bimbingan Mahasiswa:</span>
                                            <span class="font-normal underline">{{ $session->document_original_name ?: 'Dokumen Draft' }}</span>
                                        </a>
                                    </div>
                                @endif

                                <!-- Footer Card: Dosen Pembimbing Info -->
                                @if($session->dosen)
                                    <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3 flex-wrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0">
                                                <img src="{{ $session->dosen->avatar_url }}" alt="{{ $session->dosen->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $session->dosen->name }}</span>
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase tracking-wider {{ $isP1 ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300' : ($isP2 ? 'bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400') }}">
                                                        {{ $pembimbingLabel }}
                                                    </span>
                                                    @if($isMySession)
                                                        <span class="text-[9px] font-bold text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-950/60 px-1 rounded">(Anda)</span>
                                                    @endif
                                                </div>
                                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $session->dosen->identifier ?: 'NIP/NIDN -' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

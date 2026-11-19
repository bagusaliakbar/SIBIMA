                            <!-- BARIS ADMIN / KAPRODI (6 KOLOM ASLI) -->
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3.5">
                                    <a href="{{ route('theses.logbooks', $thesis->id) }}" class="relative w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200/80 dark:border-slate-700/80 bg-slate-100 dark:bg-slate-800 shrink-0 ring-2 ring-white dark:ring-slate-800 shadow-2xs hover:scale-105 transition-transform" title="Buka catatan bimbingan {{ $thesis->student->name }}">
                                        <img src="{{ $thesis->student->avatar_url }}" alt="{{ $thesis->student->name }}" class="w-full h-full object-cover">
                                    </a>
                                    <div class="space-y-1">
                                        <!-- Baris 1: Nama Mahasiswa -->
                                        <div>
                                            <a href="{{ route('theses.logbooks', $thesis->id) }}" class="font-bold text-slate-900 dark:text-white tracking-tight text-xs sm:text-sm hover:text-orange-600 dark:hover:text-orange-400 transition-colors" title="Buka catatan bimbingan {{ $thesis->student->name }}">
                                                {{ ucwords(strtolower($thesis->student->name)) }}
                                            </a>
                                        </div>
                                        
                                        @php
                                             $waNumber = \App\Helpers\PhoneHelper::formatForWhatsApp($thesis->student->phone_number);
                                             $dosenName = Auth::user()->name;
                                             $roleLabel = ($thesis->pembimbing1_id === Auth::id()) ? 'Pembimbing 1' : (($thesis->pembimbing2_id === Auth::id()) ? 'Pembimbing 2' : 'Dosen');
                                             $waMessage = urlencode("Halo {$thesis->student->name}, saya {$dosenName} ({$roleLabel}). Terkait bimbingan skripsi: \"" . ($thesis->final_title ?? $thesis->title) . "\"");
                                        @endphp

                                        <!-- Baris 2: Chip NPM & Tombol Kontak WhatsApp Sejajar -->
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <!-- Chip NPM -->
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs"
                                                  title="Nomor Pokok Mahasiswa (NPM)">
                                                <svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                                <span>{{ $thesis->student->identifier ?? 'NPM -' }}</span>
                                            </span>

                                            <!-- Tombol WhatsApp Shortcut -->
                                            @if($waNumber)
                                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-2xs hover:scale-105 active:scale-95 transition-all cursor-pointer"
                                                   title="Kirim Pesan WhatsApp ke {{ $thesis->student->name }} ({{ $thesis->student->phone_number }})">
                                                    <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400 fill-current shrink-0" viewBox="0 0 24 24">
                                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                                    </svg>
                                                    <span>Chat WA</span>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-medium bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 border border-slate-200/50 dark:border-slate-700/50" 
                                                      title="Nomor WhatsApp belum didaftarkan oleh mahasiswa">
                                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                    <span>No WA</span>
                                                </span>
                                            @endif

                                            <!-- Cohort Badge (Hanya untuk Admin/Kaprodi) -->
                                            @if(Auth::user()->role !== 'dosen' && $thesis->student->entry_year)
                                                <span class="text-slate-300 dark:text-slate-600 font-bold">•</span>
                                                @if($thesis->isOldCohort())
                                                    @if($thesis->student->is_critical_semester)
                                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/10 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300" title="Masa studi kritis (Semester {{ $thesis->student->current_semester }})">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                                            Angkatan Lama '{{ substr($thesis->student->entry_year, -2) }} (Smt {{ $thesis->student->current_semester }})
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300" title="Angkatan Lama / Senior (Semester {{ $thesis->student->current_semester }})">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                            Angkatan Lama '{{ substr($thesis->student->entry_year, -2) }} (Smt {{ $thesis->student->current_semester }})
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300" title="Angkatan Baru (Semester {{ $thesis->student->current_semester ?? '-' }})">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Angkatan {{ $thesis->student->entry_year }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 max-w-sm whitespace-normal">
                                @if($thesis->final_title)
                                    <div class="font-bold text-slate-900 dark:text-white line-clamp-2 text-xs sm:text-sm leading-snug tracking-tight" title="{{ $thesis->final_title }}">{{ $thesis->final_title }}</div>
                                    @if(trim(mb_strtolower($thesis->final_title)) !== trim(mb_strtolower($thesis->title ?? '')))
                                        <div class="mt-1 flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400" title="Rencana awal: {{ $thesis->title }}">
                                            <span class="text-amber-500 shrink-0 font-bold">↳</span>
                                            <span class="italic line-clamp-1">Awal: {{ $thesis->title }}</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="font-bold text-slate-900 dark:text-white line-clamp-2 text-xs sm:text-sm leading-snug tracking-tight" title="{{ $thesis->title }}">{{ $thesis->title }}</div>
                                @endif

                                @php
                                    $rawTitle = trim($thesis->final_title ?? $thesis->title ?? '');
                                    $titleUpper = strtoupper($rawTitle);
                                    $hasValidTitle = !empty($rawTitle) && !in_array($titleUpper, ['BELUM DIKETAHUI', 'BELUM DITENTUKAN', 'BELUM ADA JUDUL', 'BELUM ADA', '-']) && strlen($rawTitle) >= 8;
                                    $simScore = $hasValidTitle ? $thesis->getMaxSimilarityScore() : 0;
                                    $simMatches = $hasValidTitle ? $thesis->getDetailedSimilarityMatches() : collect();
                                @endphp
                                @if($hasValidTitle)
                                    <div class="mt-2 inline-block">
                                        <button @click='$dispatch("open-audit-modal", {
                                            student: "{{ addslashes($thesis->student->name) }}",
                                            npm: "{{ $thesis->student->identifier }}",
                                            title: "{{ addslashes($rawTitle) }}",
                                            score: {{ $simScore }},
                                            matches: {{ json_encode($simMatches->values()->toArray()) }}
                                        })' class="group focus:outline-hidden cursor-pointer">
                                            @if($simScore >= 66)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 hover:bg-rose-500/20 dark:bg-rose-500/15 dark:hover:bg-rose-500/25 text-rose-700 dark:text-rose-300 transition-all hover:scale-105 active:scale-95" title="Klik untuk lihat rincian data kemiripan">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping shrink-0"></span>
                                                    <span>{{ $simScore }}% Sangat Mirip</span>
                                                    <svg class="w-3 h-3 opacity-60 ml-0.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                </span>
                                            @elseif($simScore >= 35)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 hover:bg-amber-500/20 dark:bg-amber-500/15 dark:hover:bg-amber-500/25 text-amber-700 dark:text-amber-300 transition-all hover:scale-105 active:scale-95" title="Klik untuk lihat rincian data kemiripan">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                                    <span>{{ $simScore }}% Kemiripan</span>
                                                    <svg class="w-3 h-3 opacity-60 ml-0.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 hover:bg-emerald-500/20 dark:bg-emerald-500/15 dark:hover:bg-emerald-500/25 text-emerald-700 dark:text-emerald-300 transition-all hover:scale-105 active:scale-95" title="Klik untuk lihat rincian data kemiripan">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                    <span>{{ 100 - $simScore }}% Unik</span>
                                                    <svg class="w-3 h-3 opacity-60 ml-0.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                </span>
                                            @endif
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 max-w-[14rem] whitespace-normal" x-data="{ openAbstract: false }">
                                @if($thesis->abstract)
                                    <div class="relative">
                                        <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed font-normal">"{{ Str::limit($thesis->abstract, 90) }}"</p>
                                        <button @click="openAbstract = true" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold mt-1.5 flex items-center gap-1 group/btn transition-colors cursor-pointer">
                                            <span>Lihat Detail Lengkap</span>
                                            <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                    </div>

                                    <!-- Abstract Modal -->
                                    <template x-teleport="body">
                                        <div x-show="openAbstract" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak x-transition>
                                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAbstract = false">
                                                    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                </div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <div class="inline-block align-middle bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-slate-200 dark:border-slate-700 relative z-10">
                                                    <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                                                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 uppercase tracking-widest">Detail Deskripsi Skripsi</h3>
                                                        <button @click="openAbstract = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                    <div class="px-8 py-8 max-h-[60vh] overflow-y-auto">
                                                        <div class="mb-8">
                                                            <p class="text-[10px] font-bold text-orange-600 dark:text-orange-400 uppercase tracking-widest mb-2">Judul Pengajuan</p>
                                                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-tight uppercase">{{ $thesis->final_title ?? $thesis->title }}</h4>
                                                        </div>
                                                        <div class="p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                                            <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3">Deskripsi / Rencana</p>
                                                            <div class="text-xs text-slate-600 dark:text-slate-400 leading-loose text-justify font-medium whitespace-pre-line">
                                                                {{ $thesis->abstract }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                                                        <button type="button" @click="openAbstract = false" class="px-8 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm cursor-pointer">
                                                            Tutup
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center align-middle">
                                @php
                                    $stageInfo = $thesis->getCurrentAcademicStage();
                                    $hasSeminar = $thesis->hasPassedSeminar();
                                    $hasDefense = $thesis->hasPassedDefense();

                                    // Contextual ACC status (Sempro vs Sidang)
                                    $isTargetSidang = $hasSeminar;
                                    $p1Acc = $isTargetSidang ? $thesis->acc_sidang_p1 : $thesis->acc_up_p1;
                                    $p2Acc = $isTargetSidang ? $thesis->acc_sidang_p2 : $thesis->acc_up_p2;
                                    $targetAccLabel = $isTargetSidang ? 'Sidang' : 'Sempro';
                                    
                                    $isMyP1 = (Auth::id() === $thesis->pembimbing1_id);
                                    $isMyP2 = (Auth::id() === $thesis->pembimbing2_id);
                                @endphp

                                <div class="flex flex-col items-center gap-2 min-w-[190px] max-w-[230px] mx-auto">
                                    <!-- 1. Tahap Akademik Utama Mahasiswa -->
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold {{ $stageInfo['badge_class'] }} border shadow-2xs tracking-tight">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $stageInfo['dot_class'] }} shrink-0"></span>
                                        <span>{{ $stageInfo['label'] }}</span>
                                    </span>

                                    <!-- 2. Milestone Stepper: Seminar & Sidang Tracker -->
                                    <div class="w-full inline-flex items-center justify-between p-1 bg-slate-100/80 dark:bg-slate-800/80 rounded-xl border border-slate-200/60 dark:border-slate-700/60 text-[10px]">
                                        <!-- Seminar Milestone -->
                                        @if($hasSeminar)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/60 shrink-0 shadow-2xs" title="Seminar Proposal: Selesai">
                                                <svg class="w-2.5 h-2.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Sempro</span>
                                            </span>
                                        @elseif($thesis->isAccUpFinal())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 shrink-0 shadow-2xs" title="Seminar Proposal: ACC Lengkap, Siap Daftar">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>ACC Seminar</span>
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-lg font-medium text-slate-400 dark:text-slate-500 shrink-0" title="Seminar Proposal: Belum">
                                                Seminar : Belum
                                            </span>
                                        @endif

                                        <svg class="w-3 h-3 text-slate-300 dark:text-slate-600 shrink-0 mx-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>

                                        <!-- Sidang Milestone -->
                                        @if($hasDefense)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 shrink-0 shadow-2xs" title="Sidang Skripsi: Selesai">
                                                <svg class="w-2.5 h-2.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Sidang</span>
                                            </span>
                                        @elseif($thesis->isAccSidangFinal())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60 shrink-0 shadow-2xs" title="Sidang Skripsi: ACC Lengkap, Siap Daftar">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                                <span>ACC Sidang</span>
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-lg font-medium text-slate-400 dark:text-slate-500 shrink-0" title="Sidang Skripsi: Belum">
                                                Sidang : Belum
                                            </span>
                                        @endif
                                    </div>

                                    <!-- 3. Rincian ACC Pembimbing (P1 & P2 Status) -->
                                    @if($thesis->status !== 'completed' && !$hasDefense)
                                        <div class="flex items-center justify-center gap-1.5 w-full text-[10px]">
                                            <!-- P1 Status -->
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold border transition-all {{ $p1Acc ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-slate-200/60 dark:border-slate-700/60' }} {{ $isMyP1 ? 'ring-1 ring-orange-500/40' : '' }}"
                                                  title="Pembimbing 1 ACC {{ $targetAccLabel }}: {{ $p1Acc ? 'Sudah Disetujui' : 'Belum' }} {{ $isMyP1 ? '(Anda)' : '' }}">
                                                <span class="text-[9px] font-black {{ $isMyP1 ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500' }}">P1{{ $isMyP1 ? '*' : '' }}</span>
                                                @if($p1Acc)
                                                    <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <span class="text-slate-300 dark:text-slate-600 font-bold">—</span>
                                                @endif
                                            </span>

                                            <!-- P2 Status -->
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold border transition-all {{ $p2Acc ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-slate-200/60 dark:border-slate-700/60' }} {{ $isMyP2 ? 'ring-1 ring-orange-500/40' : '' }}"
                                                  title="Pembimbing 2 ACC {{ $targetAccLabel }}: {{ $p2Acc ? 'Sudah Disetujui' : 'Belum' }} {{ $isMyP2 ? '(Anda)' : '' }}">
                                                <span class="text-[9px] font-black {{ $isMyP2 ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500' }}">P2{{ $isMyP2 ? '*' : '' }}</span>
                                                @if($p2Acc)
                                                    <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <span class="text-slate-300 dark:text-slate-600 font-bold">—</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    <!-- 4. Keaktifan Bimbingan (Health & Recency) -->
                                    @if($thesis->status === 'active')
                                        @php
                                            $healthBadge = $thesis->mentoring_health_badge;
                                            $healthColor = $healthBadge['color'] ?? 'emerald';
                                            $healthBgClass = $healthColor === 'rose' 
                                                ? 'bg-rose-500/10 text-rose-700 dark:text-rose-300 border border-rose-500/20' 
                                                : ($healthColor === 'amber' ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20');
                                            $healthDotClass = $healthColor === 'rose' ? 'bg-rose-500 animate-pulse' : ($healthColor === 'amber' ? 'bg-amber-500' : 'bg-emerald-500');
                                        @endphp
                                        <div class="flex items-center justify-center">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $healthBgClass }}"
                                                  title="Aktivitas Bimbingan: Terakhir bimbingan {{ $thesis->days_since_last_mentoring ?? 0 }} hari lalu">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $healthDotClass }}"></span>
                                                <span>{{ $healthBadge['label'] }}</span>
                                            </span>
                                        </div>
                                    @endif

                                    <!-- 5. Tombol Shortcut ke Logbook Mahasiswa -->
                                    <a href="{{ route('theses.logbooks', $thesis->id) }}" 
                                       class="inline-flex items-center gap-1 text-[10px] font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 group/link transition-colors mt-0.5 cursor-pointer"
                                       title="Buka catatan bimbingan & logbook {{ $thesis->student->name }}">
                                        <span>Buka Logbook</span>
                                        <svg class="w-3 h-3 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                            
                            <td class="py-4 px-6 align-middle">
                                @if($thesis->pembimbing1 || $thesis->pembimbing2)
                                    <div class="flex flex-col gap-2">
                                        @if($thesis->pembimbing1)
                                            @php $isMeP1 = (Auth::id() === $thesis->pembimbing1_id); @endphp
                                            <div class="flex items-center gap-2">
                                                <span class="w-4 h-4 rounded {{ $isMeP1 ? 'bg-orange-500 text-white font-extrabold shadow-2xs' : 'bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30' }} flex items-center justify-center text-[9px] font-bold shrink-0">1</span>
                                                <span class="font-medium text-[10px] uppercase tracking-tight {{ $isMeP1 ? 'font-bold text-orange-600 dark:text-orange-400' : 'text-slate-700 dark:text-slate-300' }}">
                                                    {{ $thesis->pembimbing1->name }}
                                                    @if($isMeP1)
                                                        <span class="text-[9px] font-bold text-orange-500 dark:text-orange-400 lowercase">(anda)</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                        @if($thesis->pembimbing2)
                                            @php $isMeP2 = (Auth::id() === $thesis->pembimbing2_id); @endphp
                                            <div class="flex items-center gap-2">
                                                <span class="w-4 h-4 rounded {{ $isMeP2 ? 'bg-orange-500 text-white font-extrabold shadow-2xs' : 'bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30' }} flex items-center justify-center text-[9px] font-bold shrink-0">2</span>
                                                <span class="font-medium text-[10px] uppercase tracking-tight {{ $isMeP2 ? 'font-bold text-orange-600 dark:text-orange-400' : 'text-slate-700 dark:text-slate-300' }}">
                                                    {{ $thesis->pembimbing2->name }}
                                                    @if($isMeP2)
                                                        <span class="text-[9px] font-bold text-orange-500 dark:text-orange-400 lowercase">(anda)</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <x-status-badge type="slate" label="BELUM DITENTUKAN" />
                                @endif
                            </td>
                            
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                                <td class="py-4 px-6 text-right">
                                    @if(!$thesis->pembimbing1 || !$thesis->pembimbing2)
                                        <div x-data="{ openAssignModal: false }" class="inline-block whitespace-nowrap">
                                            <button @click="openAssignModal = true" class="inline-flex items-center justify-center gap-2.5 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-bold transition-all shadow-xs shadow-orange-500/20 hover:scale-[1.02] cursor-pointer whitespace-nowrap shrink-0">
                                                <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                <span>Tugaskan</span>
                                            </button>

                                            <!-- Modal Penugasan Pembimbing -->
                                            <template x-teleport="body">
                                                <div x-show="openAssignModal" class="fixed inset-0 z-[60] overflow-y-auto text-left" x-cloak x-transition>
                                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAssignModal = false">
                                                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                        </div>
                                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative z-10">
                                                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                                                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Penugasan Dosen Pembimbing</h3>
                                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase mt-1">Mahasiswa: {{ $thesis->student->name }} ({{ $thesis->student->identifier }})</p>
                                                            </div>
                                                            
                                                            <form action="{{ route('theses.assign', $thesis->id) }}" method="POST" class="p-8 space-y-5">
                                                                @csrf
                                                                <div x-data='{ 
                                                                    p1_id: "{{ $thesis->requested_pembimbing1_id }}", 
                                                                    p2_id: "{{ $thesis->requested_pembimbing2_id }}",
                                                                    dosens: {{ $dosens->mapWithKeys(fn($d) => [$d->id => $d])->toJson() }}
                                                                }' class="space-y-5">
                                                                    
                                                                    <!-- Suggestions Chips Header -->
                                                                    @if($thesis->requestedPembimbing1 || $thesis->requestedPembimbing2)
                                                                        <div class="p-4 bg-indigo-50/80 dark:bg-indigo-950/40 rounded-2xl border border-indigo-100 dark:border-indigo-900/50 space-y-2">
                                                                            <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest flex items-center gap-1.5">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                                                Usulan Pembimbing dari Mahasiswa:
                                                                            </span>
                                                                            <div class="flex flex-wrap gap-2">
                                                                                @if($thesis->requestedPembimbing1)
                                                                                    @php $p1Full = $thesis->requestedPembimbing1->total_workload >= $thesis->requestedPembimbing1->max_quota; @endphp
                                                                                    <div class="px-3 py-1.5 rounded-xl text-xs font-extrabold uppercase border flex items-center gap-2 {{ $p1Full ? 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50' : 'bg-white text-indigo-700 border-indigo-200 dark:bg-slate-900 dark:text-indigo-400 dark:border-indigo-800 shadow-2xs' }}">
                                                                                        <span>P1: {{ $thesis->requestedPembimbing1->name }}</span>
                                                                                        <span class="font-black">({{ $thesis->requestedPembimbing1->total_workload }}/{{ $thesis->requestedPembimbing1->max_quota }})</span>
                                                                                        @if($p1Full)<span class="px-1.5 py-0.5 rounded bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300 font-black text-[10px]">Penuh</span>@endif
                                                                                    </div>
                                                                                @endif
                                                                                @if($thesis->requestedPembimbing2)
                                                                                    @php $p2Full = $thesis->requestedPembimbing2->total_workload >= $thesis->requestedPembimbing2->max_quota; @endphp
                                                                                    <div class="px-3 py-1.5 rounded-xl text-xs font-extrabold uppercase border flex items-center gap-2 {{ $p2Full ? 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50' : 'bg-white text-indigo-700 border-indigo-200 dark:bg-slate-900 dark:text-indigo-400 dark:border-indigo-800 shadow-2xs' }}">
                                                                                        <span>P2: {{ $thesis->requestedPembimbing2->name }}</span>
                                                                                        <span class="font-black">({{ $thesis->requestedPembimbing2->total_workload }}/{{ $thesis->requestedPembimbing2->max_quota }})</span>
                                                                                        @if($p2Full)<span class="px-1.5 py-0.5 rounded bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300 font-black text-[10px]">Penuh</span>@endif
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Select P1 -->
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pilih Dosen Pembimbing 1</label>
                                                                        <select name="pembimbing1_id" x-model="p1_id" required class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all text-slate-800 dark:text-slate-100">
                                                                            <option value="">Pilih P1...</option>
                                                                            @foreach($dosens as $dosen)
                                                                                @php 
                                                                                    $score = $thesis->getMatchScore($dosen);
                                                                                    $isFull = $dosen->total_workload >= $dosen->max_quota;
                                                                                @endphp
                                                                                <option value="{{ $dosen->id }}" class="{{ $isFull ? 'text-rose-500 font-bold' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-600' : 'text-slate-800') }}">
                                                                                    {{ $isFull ? '🔴 [PENUH] ' : '' }}{{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <template x-if="p1_id && dosens[p1_id]">
                                                                            <div class="mt-1.5 flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'text-rose-600 dark:text-rose-400' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400')">
                                                                                <span class="w-2 h-2 rounded-full shrink-0" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'bg-rose-500' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                                                                                <span x-text="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'Quota Penuh (' + dosens[p1_id].total_workload + '/' + dosens[p1_id].max_quota + ')' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia (' + dosens[p1_id].total_workload + '/' + dosens[p1_id].max_quota + ')')"></span>
                                                                            </div>
                                                                        </template>
                                                                    </div>

                                                                    <!-- Select P2 -->
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pilih Dosen Pembimbing 2</label>
                                                                        <select name="pembimbing2_id" x-model="p2_id" required class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all text-slate-800 dark:text-slate-100">
                                                                            <option value="">Pilih P2...</option>
                                                                            @foreach($dosens as $dosen)
                                                                                @php 
                                                                                    $score = $thesis->getMatchScore($dosen);
                                                                                    $isFull = $dosen->total_workload >= $dosen->max_quota;
                                                                                @endphp
                                                                                <option value="{{ $dosen->id }}" class="{{ $isFull ? 'text-rose-500 font-bold' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-600' : 'text-slate-800') }}">
                                                                                    {{ $isFull ? '🔴 [PENUH] ' : '' }}{{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <template x-if="p2_id && dosens[p2_id]">
                                                                            <div class="mt-1.5 flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'text-rose-600 dark:text-rose-400' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400')">
                                                                                <span class="w-2 h-2 rounded-full shrink-0" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'bg-rose-500' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                                                                                <span x-text="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'Quota Penuh (' + dosens[p2_id].total_workload + '/' + dosens[p2_id].max_quota + ')' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia (' + dosens[p2_id].total_workload + '/' + dosens[p2_id].max_quota + ')')"></span>
                                                                            </div>
                                                                        </template>
                                                                    </div>

                                                                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                                                                        <button type="button" @click="openAssignModal = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors">Batal</button>
                                                                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 transition-all">Simpan Penugasan</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    @else
                                        <div class="flex justify-end items-center gap-2 shrink-0 whitespace-nowrap" x-data="{ openEditModal: false }">
                                            <!-- Rollback Button -->
                                            <button type="button"
                                                    @click="openRollbackModal('{{ $thesis->id }}', '{{ addslashes($thesis->student->name ?? 'Mahasiswa') }}', '{{ addslashes($thesis->pembimbing1?->name ?? '-') }}', '{{ addslashes($thesis->pembimbing2?->name ?? '-') }}')" 
                                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-800 rounded-xl text-xs font-bold transition-all shadow-2xs hover:scale-[1.02] active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
                                                    title="Rollback / Batalkan Penugasan Pembimbing">
                                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                <span>Rollback</span>
                                            </button>

                                            <!-- Edit Button -->
                                            <button type="button"
                                                    @click="openEditModal = true" 
                                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-2xs hover:scale-[1.02] active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
                                                    title="Edit Judul & Data Skripsi">
                                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                <span>Edit</span>
                                            </button>

                                            <!-- Logbook Button -->
                                            <a href="{{ route('theses.logbooks', $thesis->id) }}" 
                                               class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-bold transition-all shadow-xs shadow-orange-500/20 hover:scale-[1.02] cursor-pointer whitespace-nowrap shrink-0"
                                               title="Lihat Logbook Bimbingan">
                                                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                                <span>Logbook</span>
                                            </a>

                                            <!-- Edit Modal -->
                                            <template x-teleport="body">
                                                <div x-show="openEditModal" class="fixed inset-0 z-[60] overflow-y-auto text-left" x-cloak x-transition>
                                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openEditModal = false">
                                                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                        </div>
                                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative z-10">
                                                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                                                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Edit Data Skripsi</h3>
                                                                <p class="text-[10px] text-slate-500 uppercase font-black mt-1">Mahasiswa: {{ $thesis->student->name }}</p>
                                                            </div>
                                                            <form action="{{ route('theses.update', $thesis->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="px-8 py-8 space-y-6">
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Judul Final Skripsi</label>
                                                                        <textarea name="final_title" rows="3" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all uppercase leading-relaxed p-4 text-slate-800 dark:text-slate-100" required>{{ $thesis->final_title ?? $thesis->title }}</textarea>
                                                                    </div>
                                                                    <div x-data='{ 
                                                                        p1_id: "{{ $thesis->pembimbing1_id }}", 
                                                                        p2_id: "{{ $thesis->pembimbing2_id }}",
                                                                        dosens: {{ $dosens->mapWithKeys(fn($d) => [$d->id => $d])->toJson() }}
                                                                    }' class="grid grid-cols-2 gap-4">
                                                                        <div>
                                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pembimbing 1</label>
                                                                            <select name="pembimbing1_id" x-model="p1_id" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all p-3 text-slate-800 dark:text-slate-100" required>
                                                                                @foreach($dosens as $dosen)
                                                                                    @php $score = $thesis->getMatchScore($dosen); @endphp
                                                                                    <option value="{{ $dosen->id }}" 
                                                                                            class="{{ $dosen->total_workload >= $dosen->max_quota ? 'text-rose-500' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500') }}">
                                                                                        {{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <template x-if="p1_id && dosens[p1_id]">
                                                                                <div class="mt-2 flex items-center gap-1.5">
                                                                                    <div class="w-2 h-2 rounded-full" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'bg-rose-500' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></div>
                                                                                    <span class="text-[9px] font-black uppercase tracking-widest" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'text-rose-500' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500')">
                                                                                        <span x-text="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'Quota Penuh' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia')"></span>
                                                                                    </span>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                        <div>
                                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pembimbing 2</label>
                                                                            <select name="pembimbing2_id" x-model="p2_id" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all p-3 text-slate-800 dark:text-slate-100" required>
                                                                                @foreach($dosens as $dosen)
                                                                                    @php $score = $thesis->getMatchScore($dosen); @endphp
                                                                                    <option value="{{ $dosen->id }}" 
                                                                                            class="{{ $dosen->total_workload >= $dosen->max_quota ? 'text-rose-500' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500') }}">
                                                                                        {{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <template x-if="p2_id && dosens[p2_id]">
                                                                                <div class="mt-2 flex items-center gap-1.5">
                                                                                    <div class="w-2 h-2 rounded-full" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'bg-rose-500' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></div>
                                                                                    <span class="text-[9px] font-black uppercase tracking-widest" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'text-rose-500' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500')">
                                                                                        <span x-text="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'Quota Penuh' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia')"></span>
                                                                                    </span>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                                                    <button type="button" @click="openEditModal = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors">Batal</button>
                                                                    <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-500/20 transition-all">Simpan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    @endif
                                </td>
                            @endif
                        </tr>

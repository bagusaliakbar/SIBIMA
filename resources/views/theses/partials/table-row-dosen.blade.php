                            <!-- BARIS DOSEN (4 KOLOM TERPADU: MAHASISWA, RENCANA JUDUL SKRIPSI, STATUS & PROGRES, AKSI) -->
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors group">
                                <td class="sibima-sticky-col py-4 px-6">
                                    <div class="flex items-center gap-3.5">
                                        <div class="relative w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200/80 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0 ring-2 ring-white dark:ring-slate-800 shadow-2xs">
                                            <img src="{{ $thesis->student->avatar_url }}" alt="{{ $thesis->student->name }}" class="w-full h-full object-cover">
                                        </div>
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
                                                $waMessage = urlencode("Halo {$thesis->student->name}, saya {$dosenName} ({$roleLabel}). Terkait logbook bimbingan skripsi: \"" . ($thesis->final_title ?? $thesis->title) . "\"");
                                            @endphp

                                            <!-- Baris 2: Chip NPM & Tombol Kontak WhatsApp Sejajar -->
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <!-- Chip NPM -->
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs"
                                                      title="Nomor Pokok Mahasiswa (NPM)">
                                                    <svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                                    <span>{{ $thesis->student->identifier ?? 'NPM -' }}</span>
                                                </span>

                                                <!-- Tombol WhatsApp Shortcut -->
                                                @if($waNumber)
                                                    <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" 
                                                       target="_blank" 
                                                       rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-2xs hover:scale-105 active:scale-95 transition-all cursor-pointer"
                                                       title="Kirim Pesan WhatsApp ke {{ $thesis->student->name }} ({{ $thesis->student->phone_number }})">
                                                        <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400 fill-current shrink-0" viewBox="0 0 24 24">
                                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                                        </svg>
                                                        <span>Chat WA</span>
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[11px] font-medium bg-slate-100/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 border border-slate-200/50 dark:border-slate-700/50" 
                                                          title="Nomor WhatsApp belum didaftarkan oleh mahasiswa">
                                                        <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                        <span>No WA</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kolom 2: Rencana Judul Skripsi -->
                                <td class="py-4 px-6 max-w-sm whitespace-normal" x-data="{ openAbstract: false }">
                                    @if($thesis->final_title)
                                        <div class="font-bold text-slate-900 dark:text-white line-clamp-2 text-xs sm:text-sm leading-snug tracking-tight" title="{{ $thesis->final_title }}">
                                            {{ $thesis->final_title }}
                                        </div>
                                        @if(trim(mb_strtolower($thesis->final_title)) !== trim(mb_strtolower($thesis->title ?? '')))
                                            <div class="mt-1 flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400" title="Rencana awal: {{ $thesis->title }}">
                                                <span class="text-amber-500 shrink-0 font-bold">↳</span>
                                                <span class="italic line-clamp-1">Awal: {{ $thesis->title }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="font-bold text-slate-900 dark:text-white line-clamp-2 text-xs sm:text-sm leading-snug tracking-tight" title="{{ $thesis->title }}">
                                            {{ $thesis->title }}
                                        </div>
                                    @endif

                                    <!-- Similarity Score & Abstract Modal Trigger -->
                                    <div class="mt-2 flex items-center gap-2 flex-wrap">
                                        @php
                                            $rawTitle = trim($thesis->final_title ?? $thesis->title ?? '');
                                            $titleUpper = strtoupper($rawTitle);
                                            $hasValidTitle = !empty($rawTitle) && !in_array($titleUpper, ['BELUM DIKETAHUI', 'BELUM DITENTUKAN', 'BELUM ADA JUDUL', 'BELUM ADA', '-']) && strlen($rawTitle) >= 8;
                                            $simScore = $hasValidTitle ? $thesis->getMaxSimilarityScore() : 0;
                                            $simMatches = $hasValidTitle ? $thesis->getDetailedSimilarityMatches() : collect();
                                        @endphp
                                        @if($hasValidTitle)
                                            <button type="button" @click='$dispatch("open-audit-modal", {
                                                student: "{{ addslashes($thesis->student->name) }}",
                                                npm: "{{ $thesis->student->identifier }}",
                                                title: "{{ addslashes($rawTitle) }}",
                                                score: {{ $simScore }},
                                                matches: {{ json_encode($simMatches->values()->toArray()) }}
                                            })' class="group focus:outline-hidden cursor-pointer">
                                                @if($simScore >= 66)
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/10 hover:bg-rose-500/20 text-rose-700 dark:text-rose-300 transition-all">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                                        <span>{{ $simScore }}% Sangat Mirip</span>
                                                    </span>
                                                @elseif($simScore >= 35)
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 transition-all">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                                        <span>{{ $simScore }}% Kemiripan</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 transition-all">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                        <span>{{ 100 - $simScore }}% Unik</span>
                                                    </span>
                                                @endif
                                            </button>
                                        @endif

                                        @if($thesis->abstract)
                                            <button type="button" @click="openAbstract = true" class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold flex items-center gap-1 transition-colors cursor-pointer" title="Lihat Abstrak & Detail Lengkap">
                                                <span>Lihat Detail Lengkap</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        @endif
                                    </div>

                                    @if($thesis->abstract)
                                        <!-- Abstract Modal -->
                                        <template x-teleport="body">
                                            <div x-show="openAbstract" @keydown.escape.window="if (openAbstract) openAbstract = false" role="dialog" aria-modal="true" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak x-transition>
                                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAbstract = false">
                                                        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                    </div>
                                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                    <div class="inline-block align-middle bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-slate-200 dark:border-slate-700 relative z-10">
                                                        <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                                                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 uppercase tracking-widest">Detail Deskripsi Skripsi</h3>
                                                            <button type="button" @click="openAbstract = false" aria-label="Tutup detail deskripsi skripsi" class="text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                        </div>
                                                        <div class="px-8 py-8 max-h-[60vh] overflow-y-auto">
                                                            <div class="mb-8">
                                                                <p class="text-[11px] font-bold text-orange-600 dark:text-orange-400 uppercase tracking-widest mb-2">Judul Pengajuan</p>
                                                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-tight uppercase">{{ $thesis->final_title ?? $thesis->title }}</h4>
                                                            </div>
                                                            <div class="p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                                                <p class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3">Deskripsi / Rencana</p>
                                                                <div class="text-xs text-slate-600 dark:text-slate-400 leading-loose text-justify font-medium whitespace-pre-line">
                                                                    {{ $thesis->abstract }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                                                            <button type="button" @click="openAbstract = false" class="px-8 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm cursor-pointer">
                                                                Tutup
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    @endif

                                    <!-- Supervisor Chips with Session Counts & (anda) -->
                                    @php
                                        $p1Count = $thesis->mentoringSessions ? $thesis->mentoringSessions->where('dosen_id', $thesis->pembimbing1_id)->count() : 0;
                                        $p2Count = $thesis->mentoringSessions ? $thesis->mentoringSessions->where('dosen_id', $thesis->pembimbing2_id)->count() : 0;
                                        $isMeP1 = (Auth::id() === $thesis->pembimbing1_id);
                                        $isMeP2 = (Auth::id() === $thesis->pembimbing2_id);
                                    @endphp
                                    <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex flex-col gap-1 text-[11px]">
                                        @if($thesis->pembimbing1)
                                            <div class="flex items-center gap-1.5 flex-wrap {{ $isMeP1 ? 'font-bold text-indigo-700 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400' }}">
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[11px] font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/80">P1</span>
                                                <span class="truncate max-w-[190px]" title="{{ $thesis->pembimbing1->name }}">{{ $thesis->pembimbing1->name }}</span>
                                                @if($isMeP1)
                                                    <span class="text-[11px] font-bold text-orange-500 dark:text-orange-400 lowercase">(anda)</span>
                                                @endif
                                                <span class="font-mono text-[11px] text-slate-500 dark:text-slate-400 font-normal">({{ $p1Count }} sesi)</span>
                                            </div>
                                        @endif
                                        @if($thesis->pembimbing2)
                                            <div class="flex items-center gap-1.5 flex-wrap {{ $isMeP2 ? 'font-bold text-purple-700 dark:text-purple-300' : 'text-slate-600 dark:text-slate-400' }}">
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[11px] font-black bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800/80">P2</span>
                                                <span class="truncate max-w-[190px]" title="{{ $thesis->pembimbing2->name }}">{{ $thesis->pembimbing2->name }}</span>
                                                @if($isMeP2)
                                                    <span class="text-[11px] font-bold text-orange-500 dark:text-orange-400 lowercase">(anda)</span>
                                                @endif
                                                <span class="font-mono text-[11px] text-slate-500 dark:text-slate-400 font-normal">({{ $p2Count }} sesi)</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Kolom 3: Status & Progres (PROGRES TARGET BIMBINGAN) -->
                                <td class="py-4 px-6 text-center align-middle">
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
                                        $targetAccLabel = $isTargetSidang ? 'Sidang' : 'Seminar';

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
                                    @endphp

                                    <div class="flex flex-col items-center gap-2.5 min-w-[210px] max-w-[250px] mx-auto text-left">
                                        <!-- 1. Tahap Akademik Utama Mahasiswa -->
                                        <div class="w-full flex items-center justify-center">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold {{ $stageInfo['badge_class'] }} border shadow-2xs tracking-tight">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $stageInfo['dot_class'] }} shrink-0"></span>
                                                <span>{{ $stageInfo['label'] }}</span>
                                            </span>
                                        </div>

                                        <!-- 2. Milestone Stepper: Seminar & Sidang Tracker -->
                                        <div class="w-full inline-flex items-center justify-between p-1 bg-slate-100/80 dark:bg-slate-800/80 rounded-xl border border-slate-200/60 dark:border-slate-700/60 text-[11px]">
                                            <!-- Seminar Milestone -->
                                            @if($hasSeminar)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/60 shrink-0 shadow-2xs" title="Seminar Proposal: Selesai">
                                                    <svg class="w-2.5 h-2.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    <span>Seminar</span>
                                                </span>
                                            @elseif($thesis->isAccUpFinal())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 shrink-0 shadow-2xs" title="Seminar Proposal: ACC Lengkap, Siap Daftar">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    <span>ACC Seminar</span>
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-lg font-medium text-slate-500 dark:text-slate-400 shrink-0" title="Seminar Proposal: Belum">
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
                                                <span class="px-2 py-0.5 rounded-lg font-medium text-slate-500 dark:text-slate-400 shrink-0" title="Sidang Skripsi: Belum">
                                                    Sidang : Belum
                                                </span>
                                            @endif
                                        </div>

                                        <!-- 3. Active Stage Progress Bar (Target UP 8 Sesi / Target Sidang 16 Sesi) -->
                                        <div class="w-full space-y-1.5 pt-1 border-t border-slate-100 dark:border-slate-800/80">
                                            @if(!$isTargetSidang && !$isGraduated)
                                                <!-- Target UP (8 Sesi) -->
                                                <div class="space-y-1">
                                                    <div class="flex items-center justify-between text-[11px] font-bold">
                                                        <span class="text-slate-600 dark:text-slate-300 flex items-center gap-1.5" title="Target Seminar UP: 8 Sesi (4x P1 & 4x P2)">
                                                            <span class="w-1.5 h-1.5 rounded-full {{ $upPercent >= 100 || $hasMyAccUp ? 'bg-indigo-600 dark:bg-indigo-400' : ($upPercent > 0 ? 'bg-indigo-500 animate-pulse' : 'bg-slate-300 dark:bg-slate-600') }}"></span>
                                                            <span>Target UP</span>
                                                        </span>
                                                        <div class="flex items-center gap-1.5">
                                                            @if($hasMyAccUp || $isAccUpFinal)
                                                                <span class="inline-flex items-center gap-0.5 text-[11px] font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded border border-emerald-200 dark:border-emerald-800" title="Sudah di-ACC Seminar Proposal">
                                                                    <svg class="w-2.5 h-2.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                                    <span>ACC UP</span>
                                                                </span>
                                                            @endif
                                                            <span class="font-mono tabular-nums text-[11px] {{ $completedCount >= 8 ? 'text-indigo-600 dark:text-indigo-400 font-black' : ($completedCount > 0 ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400') }}">
                                                                {{ min($completedCount, 8) }}/8 Sesi
                                                                <span class="text-[11px] font-medium text-slate-500 dark:text-slate-300">({{ $upPercent }}%)</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="w-full bg-slate-100 dark:bg-slate-700/80 rounded-full h-1.5 overflow-hidden border border-slate-200/50 dark:border-slate-700/50">
                                                        <div class="h-full rounded-full transition-all duration-500 {{ $upPercent >= 100 ? 'bg-indigo-600 dark:bg-indigo-500' : ($upPercent > 0 ? 'bg-indigo-500 dark:bg-indigo-400' : 'bg-transparent') }}" 
                                                             style="width: {{ $upPercent }}%; {{ $upPercent > 0 ? 'min-width: 6px;' : '' }}"></div>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Target Sidang (16 Sesi) -->
                                                <div class="space-y-1">
                                                    <div class="flex items-center justify-between text-[11px] font-bold">
                                                        <span class="text-slate-600 dark:text-slate-300 flex items-center gap-1.5" title="Target Sidang Skripsi: 16 Sesi (8x P1 & 8x P2)">
                                                            <span class="w-1.5 h-1.5 rounded-full {{ $sidangPercent >= 100 || $hasMyAccSidang ? 'bg-emerald-600 dark:bg-emerald-400' : ($sidangPercent > 0 ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600') }}"></span>
                                                            <span>Target Sidang</span>
                                                        </span>
                                                        <div class="flex items-center gap-1.5">
                                                            @if($hasMyAccSidang || $isAccSidangFinal || $isGraduated)
                                                                <span class="inline-flex items-center gap-0.5 text-[11px] font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded border border-emerald-200 dark:border-emerald-800" title="Sudah di-ACC Sidang Akhir">
                                                                    <svg class="w-2.5 h-2.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                                    <span>ACC Sidang</span>
                                                                </span>
                                                            @endif
                                                            <span class="font-mono tabular-nums text-[11px] {{ $completedCount >= 16 || $isGraduated ? 'text-emerald-600 dark:text-emerald-400 font-black' : ($completedCount > 0 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400') }}">
                                                                {{ $isGraduated ? '16/16' : min($completedCount, 16) . '/16' }} Sesi
                                                                <span class="text-[11px] font-medium text-slate-500 dark:text-slate-300">({{ $sidangPercent }}%)</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="w-full bg-slate-100 dark:bg-slate-700/80 rounded-full h-1.5 overflow-hidden border border-slate-200/50 dark:border-slate-700/50">
                                                        <div class="h-full rounded-full transition-all duration-500 {{ $sidangPercent >= 100 ? 'bg-emerald-600 dark:bg-emerald-500' : ($sidangPercent > 0 ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-transparent') }}" 
                                                             style="width: {{ $sidangPercent }}%; {{ $sidangPercent > 0 ? 'min-width: 6px;' : '' }}"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- 4. Rincian ACC Pembimbing (P1 & P2 Status) & Keaktifan Status -->
                                        <div class="w-full flex items-center justify-between gap-1.5 text-[11px] pt-0.5">
                                            <div class="flex items-center gap-1">
                                                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mr-0.5">ACC {{ $targetAccLabel }}:</span>
                                                <!-- P1 Status -->
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md font-bold border transition-all {{ $p1Acc ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-slate-200/60 dark:border-slate-700/60' }} {{ $isP1 ? 'ring-1 ring-orange-500/40' : '' }}"
                                                      title="Pembimbing 1 ACC {{ $targetAccLabel }}: {{ $p1Acc ? 'Sudah Disetujui' : 'Belum' }} {{ $isP1 ? '(Anda)' : '' }}">
                                                    <span class="text-[11px] font-black {{ $isP1 ? 'text-orange-600 dark:text-orange-400' : 'text-slate-500 dark:text-slate-400' }}">P1</span>
                                                    @if($p1Acc)
                                                        <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    @else
                                                        <span class="text-slate-300 dark:text-slate-500 font-bold">—</span>
                                                    @endif
                                                </span>

                                                <!-- P2 Status -->
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md font-bold border transition-all {{ $p2Acc ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-slate-200/60 dark:border-slate-700/60' }} {{ $isP2 ? 'ring-1 ring-orange-500/40' : '' }}"
                                                      title="Pembimbing 2 ACC {{ $targetAccLabel }}: {{ $p2Acc ? 'Sudah Disetujui' : 'Belum' }} {{ $isP2 ? '(Anda)' : '' }}">
                                                    <span class="text-[11px] font-black {{ $isP2 ? 'text-orange-600 dark:text-orange-400' : 'text-slate-500 dark:text-slate-400' }}">P2</span>
                                                    @if($p2Acc)
                                                        <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    @else
                                                        <span class="text-slate-300 dark:text-slate-500 font-bold">—</span>
                                                    @endif
                                                </span>
                                            </div>

                                            <!-- Health Status Pill / Milestone Pill -->
                                            <div class="shrink-0">
                                                @if($isGraduated)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                        <span>Lulus</span>
                                                    </span>
                                                @elseif(($completedCount >= 16 || ($p1Count >= 8 && $p2Count >= 8)) && !$hasMyAccSidang)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800" title="Mahasiswa siap untuk di-ACC Sidang">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        <span>Siap Sidang</span>
                                                    </span>
                                                @elseif(($completedCount >= 8 || ($p1Count >= 4 && $p2Count >= 4)) && !$hasMyAccUp)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800" title="Mahasiswa siap untuk di-ACC Seminar">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                        <span>Siap UP</span>
                                                    </span>
                                                @elseif($completedCount === 0)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                        <span>0 Sesi</span>
                                                    </span>
                                                @elseif($daysSinceLast !== null && $daysSinceLast > 14)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800"
                                                          title="Bimbingan terakhir: {{ $lastDate ? $lastDate->isoFormat('D MMMM Y') : '-' }}">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        <span>Pasif ({{ $daysSinceLast }}h)</span>
                                                    </span>
                                                @else
                                                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400"
                                                          title="Bimbingan terakhir: {{ $lastDate ? $lastDate->isoFormat('D MMMM Y') : '-' }}">
                                                        {{ $lastActivityText }}
                                                    </span>
                                                @endif
                                            </div>
                                    </div>
                                </td>

                                <!-- Kolom 4: Aksi -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <!-- Tombol Pintasan Pratinjau Riwayat (Quick Preview) -->
                                        <button type="button" 
                                                @click="$dispatch('open-logbook-preview', { thesisId: {{ $thesis->id }} })"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-50 hover:bg-orange-50 hover:text-orange-600 dark:bg-slate-800 dark:hover:bg-orange-950/40 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all shadow-2xs hover:shadow-xs group/btn cursor-pointer"
                                                title="Pratinjau Riwayat Bimbingan Terakhir (3-5 Sesi)">
                                            <svg class="w-4 h-4 text-slate-400 group-hover/btn:text-orange-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span class="hidden sm:inline">Pratinjau</span>
                                        </button>

                                        <!-- Tombol Lihat Logbook Lengkap -->
                                        <a href="{{ route('theses.logbooks', $thesis->id) }}" 
                                           class="inline-flex items-center gap-2 pl-4 pr-3.5 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300 dark:hover:bg-orange-950/40 dark:hover:text-orange-400 dark:hover:border-orange-800 rounded-xl text-xs font-bold transition-all shadow-2xs hover:shadow-xs group/btn whitespace-nowrap"
                                           title="Buka Catatan Logbook Lengkap">
                                            <span>Logbook</span>
                                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover/btn:text-orange-500 group-hover/btn:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>

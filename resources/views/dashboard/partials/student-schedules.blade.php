        @if(Auth::user()->role === 'mahasiswa' && ($mySeminarSchedule || $myDefenseSchedule))
            {{-- Student Schedule Section (already here) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if($mySeminarSchedule)
                    @php
                        $semDateStr = \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->format('Y-m-d');
                        $semStart = \Carbon\Carbon::parse($semDateStr . ' ' . \Carbon\Carbon::parse($mySeminarSchedule->start_time)->format('H:i:s'));
                        $semEnd = \Carbon\Carbon::parse($semDateStr . ' ' . \Carbon\Carbon::parse($mySeminarSchedule->end_time)->format('H:i:s'));
                        $isSeminarOngoing = now()->between($semStart, $semEnd);
                        $isSeminarFinished = $mySeminarSchedule->isGraded()
                            || $mySeminarSchedule->isAllRevisionsApproved()
                            || ($semEnd->isPast() && ($mySeminarSchedule->revisions()->exists() || $progress['currentStage'] >= 4))
                            || ($progress['currentStage'] >= 4 && $semStart->isPast());
                    @endphp
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 {{ $isSeminarFinished ? 'bg-emerald-50 dark:bg-emerald-950/10' : ($isSeminarOngoing ? 'bg-blue-50 dark:bg-blue-900/10' : 'bg-orange-50 dark:bg-orange-900/10') }} rounded-full -mr-12 -mt-12 opacity-50"></div>
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center">
                                    <svg class="w-4 h-4 mr-2 {{ $isSeminarFinished ? 'text-emerald-600' : ($isSeminarOngoing ? 'text-blue-600' : 'text-orange-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Jadwal Seminar Skripsi
                                </h3>
                                <div class="flex items-center gap-2">
                                    @php
                                        $semPdfTarget = $mySeminarSchedule->schedule ?? $mySeminarSchedule->seminar_schedule_id;
                                    @endphp
                                    @if($semPdfTarget)
                                        <a href="{{ route('seminar-schedules.export-pdf', $semPdfTarget) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-slate-100 hover:bg-orange-50 hover:text-orange-700 dark:bg-slate-700/60 dark:hover:bg-orange-950/40 dark:hover:text-orange-300 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-bold border border-slate-200 dark:border-slate-700 transition-all shadow-2xs" title="Cetak Bukti Jadwal Seminar PDF">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span>Cetak PDF</span>
                                        </a>
                                    @endif
                                    @if($isSeminarFinished)
                                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 text-[11px] font-extrabold rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-800/80">Selesai</span>
                                    @elseif($isSeminarOngoing)
                                        <span class="px-2.5 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300 text-[11px] font-extrabold rounded-full uppercase tracking-wider border border-blue-200 dark:border-blue-800/80 animate-pulse">Berlangsung</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-orange-100 text-orange-700 dark:bg-orange-950/80 dark:text-orange-300 text-[11px] font-extrabold rounded-full uppercase tracking-wider border border-orange-200 dark:border-orange-800/80">Terjadwal</span>
                                    @endif
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-slate-50 dark:bg-slate-900/50 rounded-xl flex flex-col items-center justify-center border border-slate-100 dark:border-slate-700">
                                        <span class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase leading-none">{{ \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->locale('id')->translatedFormat('M') }}</span>
                                        <span class="text-lg font-black text-slate-800 dark:text-slate-100 leading-none mt-1">{{ \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->format('d') }}</span>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-none mb-1">Hari & Tanggal</p>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100">{{ \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->locale('id')->translatedFormat('l, d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Waktu</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($mySeminarSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($mySeminarSchedule->end_time)->format('H:i') }} WIB</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Ruangan/Tempat</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $mySeminarSchedule->schedule->location ?: '-' }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Ketua Sidang</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ $mySeminarSchedule->schedule->chairman?->name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Moderator</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ $mySeminarSchedule->schedule->moderator?->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-200 dark:border-slate-700/60">
                                    <div>
                                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1.5">Dosen Pembimbing</p>
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 space-y-1.5">
                                            <div class="flex items-center gap-1.5 truncate">
                                                <span class="px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-bold text-[11px] rounded border border-indigo-200/80 dark:border-indigo-800/80 shrink-0">P1</span>
                                                <span class="truncate font-semibold">{{ $mySeminarSchedule->thesis->pembimbing1->name ?? $thesis->pembimbing1->name ?? '-' }}</span>
                                            </div>
                                            @if(($mySeminarSchedule->thesis->pembimbing2->name ?? $thesis->pembimbing2->name ?? false))
                                                <div class="flex items-center gap-1.5 truncate">
                                                    <span class="px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 font-bold text-[11px] rounded border border-purple-200/80 dark:border-purple-800/80 shrink-0">P2</span>
                                                    <span class="truncate font-semibold">{{ $mySeminarSchedule->thesis->pembimbing2->name ?? $thesis->pembimbing2->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1.5">Dosen Penguji</p>
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 space-y-1.5">
                                            <div class="flex items-center gap-1.5 truncate">
                                                <span class="px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 font-bold text-[11px] rounded border border-amber-200/80 dark:border-amber-800/80 shrink-0">U1</span>
                                                <span class="truncate font-semibold">{{ $mySeminarSchedule->examiner1->name ?? '-' }}</span>
                                            </div>
                                            @if($mySeminarSchedule->examiner2)
                                                <div class="flex items-center gap-1.5 truncate">
                                                    <span class="px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 font-bold text-[11px] rounded border border-amber-200/80 dark:border-amber-800/80 shrink-0">U2</span>
                                                    <span class="truncate font-semibold">{{ $mySeminarSchedule->examiner2->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($mySeminarSchedule->schedule->meeting_link && !$isSeminarFinished)
                                    <div class="pt-2">
                                        <a href="{{ $mySeminarSchedule->schedule->meeting_link }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2.5 {{ $isSeminarOngoing ? 'bg-blue-600 hover:bg-blue-700 animate-pulse' : 'bg-orange-600 hover:bg-orange-700' }} text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xs group">
                                            <x-google-meet-icon class="w-4 h-4 mr-2 shrink-0 transition-transform group-hover:scale-110" />
                                            {{ $isSeminarOngoing ? 'Gabung Sekarang (Sedang Berlangsung)' : 'Gabung Sekarang' }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if($myDefenseSchedule)
                    @php
                        $defDateStr = \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->format('Y-m-d');
                        $defStart = \Carbon\Carbon::parse($defDateStr . ' ' . \Carbon\Carbon::parse($myDefenseSchedule->start_time)->format('H:i:s'));
                        $defEnd = \Carbon\Carbon::parse($defDateStr . ' ' . \Carbon\Carbon::parse($myDefenseSchedule->end_time)->format('H:i:s'));
                        $isDefenseOngoing = now()->between($defStart, $defEnd);
                        $isDefenseFinished = $myDefenseSchedule->isGradingComplete()
                            || $myDefenseSchedule->isRevisionAllApproved()
                            || ($defEnd->isPast() && ($myDefenseSchedule->revisions()->exists() || $progress['currentStage'] >= 5))
                            || ($progress['currentStage'] >= 5 && $defStart->isPast());
                    @endphp
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 {{ $isDefenseFinished ? 'bg-emerald-50 dark:bg-emerald-950/10' : ($isDefenseOngoing ? 'bg-blue-50 dark:bg-blue-900/10' : 'bg-indigo-50 dark:bg-indigo-900/10') }} rounded-full -mr-12 -mt-12 opacity-50"></div>
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center">
                                    <svg class="w-4 h-4 mr-2 {{ $isDefenseFinished ? 'text-emerald-600' : ($isDefenseOngoing ? 'text-blue-600' : 'text-emerald-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Jadwal Sidang Skripsi
                                </h3>
                                <div class="flex items-center gap-2">
                                    @php
                                        $defPdfTarget = $myDefenseSchedule->schedule ?? $myDefenseSchedule->thesis_defense_schedule_id;
                                    @endphp
                                    @if($defPdfTarget)
                                        <a href="{{ route('thesis-defense-schedules.export-pdf', $defPdfTarget) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-700/60 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-300 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-bold border border-slate-200 dark:border-slate-700 transition-all shadow-2xs" title="Cetak Bukti Jadwal Sidang PDF">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span>Cetak PDF</span>
                                        </a>
                                    @endif
                                    @if($isDefenseFinished)
                                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 text-[11px] font-extrabold rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-800/80">Selesai</span>
                                    @elseif($isDefenseOngoing)
                                        <span class="px-2.5 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300 text-[11px] font-extrabold rounded-full uppercase tracking-wider border border-blue-200 dark:border-blue-800/80 animate-pulse">Berlangsung</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 text-[11px] font-extrabold rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-800/80">Terjadwal</span>
                                    @endif
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-slate-50 dark:bg-slate-900/50 rounded-xl flex flex-col items-center justify-center border border-slate-100 dark:border-slate-700">
                                        <span class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase leading-none">{{ \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->locale('id')->translatedFormat('M') }}</span>
                                        <span class="text-lg font-black text-slate-800 dark:text-slate-100 leading-none mt-1">{{ \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->format('d') }}</span>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-none mb-1">Hari & Tanggal</p>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100">{{ \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->locale('id')->translatedFormat('l, d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Waktu</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($myDefenseSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($myDefenseSchedule->end_time)->format('H:i') }} WIB</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Ruangan/Tempat</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $myDefenseSchedule->schedule->location ?: '-' }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Ketua Sidang</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ $myDefenseSchedule->schedule->chairman?->name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Moderator</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ $myDefenseSchedule->schedule->moderator?->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-200 dark:border-slate-700/60">
                                    <div>
                                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1.5">Dosen Pembimbing</p>
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 space-y-1.5">
                                            <div class="flex items-center gap-1.5 truncate">
                                                <span class="px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-bold text-[11px] rounded border border-indigo-200/80 dark:border-indigo-800/80 shrink-0">P1</span>
                                                <span class="truncate font-semibold">{{ $myDefenseSchedule->thesis->pembimbing1->name ?? $thesis->pembimbing1->name ?? '-' }}</span>
                                            </div>
                                            @if(($myDefenseSchedule->thesis->pembimbing2->name ?? $thesis->pembimbing2->name ?? false))
                                                <div class="flex items-center gap-1.5 truncate">
                                                    <span class="px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 font-bold text-[11px] rounded border border-purple-200/80 dark:border-purple-800/80 shrink-0">P2</span>
                                                    <span class="truncate font-semibold">{{ $myDefenseSchedule->thesis->pembimbing2->name ?? $thesis->pembimbing2->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1.5">Dosen Penguji</p>
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 space-y-1.5">
                                            <div class="flex items-center gap-1.5 truncate">
                                                <span class="px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 font-bold text-[11px] rounded border border-amber-200/80 dark:border-amber-800/80 shrink-0">U1</span>
                                                <span class="truncate font-semibold">{{ $myDefenseSchedule->examiner1->name ?? '-' }}</span>
                                            </div>
                                            @if($myDefenseSchedule->examiner2)
                                                <div class="flex items-center gap-1.5 truncate">
                                                    <span class="px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 font-bold text-[11px] rounded border border-amber-200/80 dark:border-amber-800/80 shrink-0">U2</span>
                                                    <span class="truncate font-semibold">{{ $myDefenseSchedule->examiner2->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($myDefenseSchedule->schedule->meeting_link && !$isDefenseFinished)
                                    <div class="pt-2">
                                        <a href="{{ $myDefenseSchedule->schedule->meeting_link }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2.5 {{ $isDefenseOngoing ? 'bg-blue-600 hover:bg-blue-700 animate-pulse' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xs group">
                                            <x-google-meet-icon class="w-4 h-4 mr-2 shrink-0 transition-transform group-hover:scale-110" />
                                            {{ $isDefenseOngoing ? 'Gabung Sekarang (Sedang Berlangsung)' : 'Join Google Meet' }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

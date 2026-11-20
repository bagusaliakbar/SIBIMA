                        <!-- 2. TAMPILAN PER MAHASISWA -->
                        <div x-show="cardGrouping === 'student'" class="space-y-12">
                            @foreach($groupedSessions as $studentName => $studentSessions)
                                @php
                                    $studentThesis = $studentSessions->first()->thesis;
                                    $mentoringCount = (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') 
                                        ? $studentThesis->completed_mentoring_count 
                                        : $studentThesis->getCompletedMentoringCountForDosen(Auth::id());
                                    $hasStudentActionable = $studentSessions->contains(fn($s) => in_array($s->status, ['pending', 'approved']));
                                    $isStudentAllDone = !$hasStudentActionable;
                                    $mentoringTarget = 8;
                                    $mentoringPct = min(100, (int) round(($mentoringCount / $mentoringTarget) * 100));
                                @endphp
                                <div x-data="{ showAllStudentSessions: false }"
                                     x-show="sessionStatusFilter === 'all' || (sessionStatusFilter === 'actionable' ? {{ $hasStudentActionable ? 'true' : 'false' }} : {{ $isStudentAllDone ? 'true' : 'false' }})"
                                     class="bg-white dark:bg-slate-800/80 rounded-3xl p-6 sm:p-7 border border-slate-200/90 dark:border-slate-700/80 shadow-xs space-y-6 transition-all hover:border-slate-300 dark:hover:border-slate-600">
                                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-5 border-b border-slate-200/80 dark:border-slate-700/80 gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0">
                                                <img src="{{ $studentThesis->student?->avatar_url }}" alt="{{ $studentName }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="space-y-2 min-w-0">
                                                <div class="flex items-center gap-2.5 flex-wrap">
                                                    <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $studentName }}</h4>
                                                    @if($studentThesis->status === 'completed')
                                                        <span class="px-2.5 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase tracking-wider">
                                                            Lulus
                                                        </span>
                                                    @endif
                                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-[10px] font-bold shadow-2xs">
                                                        <span class="tabular-nums">{{ $mentoringCount }}/{{ $mentoringTarget }} Bimbingan {{ (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') ? 'Total' : 'dengan Anda' }}</span>
                                                        <span class="w-12 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden inline-block">
                                                            <span class="block h-full rounded-full {{ $mentoringCount >= $mentoringTarget ? 'bg-emerald-500' : 'bg-orange-500' }}" style="width: {{ $mentoringPct }}%"></span>
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="flex flex-wrap items-center gap-2.5 text-[11px] font-medium">
                                                    @if($studentThesis->pembimbing1)
                                                        <span class="inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-700 text-xs shadow-2xs">
                                                            <span class="px-1.5 py-0.5 bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-black text-[9px] rounded-md border border-indigo-200 dark:border-indigo-800 shrink-0">P1</span>
                                                            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $studentThesis->pembimbing1->name }}</span>
                                                        </span>
                                                    @endif
                                                    @if($studentThesis->pembimbing2)
                                                        <span class="inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-700 text-xs shadow-2xs">
                                                            <span class="px-1.5 py-0.5 bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 font-black text-[9px] rounded-md border border-purple-200 dark:border-purple-800 shrink-0">P2</span>
                                                            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $studentThesis->pembimbing2->name }}</span>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                            <div class="flex flex-wrap gap-2">
                                                @php
                                                    $studentActionableIds = $studentSessions->filter(fn($s) => $s->status !== 'completed' || $s->is_absent)->pluck('id')->values()->all();
                                                @endphp
                                                @if(count($studentActionableIds) > 0)
                                                    <button type="button" 
                                                            @click="toggleSelectGroup(@json($studentActionableIds))"
                                                            :class="isGroupSelected(@json($studentActionableIds)) ? 'bg-orange-600 text-white border-orange-600 shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50'"
                                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                        <span x-text="isGroupSelected(@json($studentActionableIds)) ? 'Lepas Pilihan' : 'Pilih Sesi'">Pilih Sesi</span>
                                                    </button>
                                                @endif

                                                {{-- ACC UP Group --}}
                                                @php
                                                    $isAdminOrKaprodi = in_array(Auth::user()->role, ['admin', 'kaprodi']);
                                                    $isP1 = Auth::id() === $studentThesis->pembimbing1_id;
                                                    $isP2 = Auth::id() === $studentThesis->pembimbing2_id;
                                                    $hasAccUp = $isAdminOrKaprodi ? ($studentThesis->acc_up_p1 && $studentThesis->acc_up_p2) : ($isP1 ? $studentThesis->acc_up_p1 : ($isP2 ? $studentThesis->acc_up_p2 : false));
                                                @endphp
                                                <div class="inline-flex items-center gap-2 bg-slate-50 dark:bg-slate-900/50 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-700">
                                                    <form action="{{ route('theses.toggle-acc', [$studentThesis->id, 'up']) }}" method="POST" class="m-0 p-0 inline-flex items-center"
                                                        data-confirm="Apakah Anda yakin ingin {{ $hasAccUp ? 'membatalkan' : 'memberikan' }} ACC Seminar untuk {{ $studentName }}?"
                                                        data-confirm-title="Konfirmasi ACC Seminar"
                                                        data-confirm-type="{{ $hasAccUp ? 'warning' : 'emerald' }}">
                                                        @csrf
                                                        @if($isAdminOrKaprodi)
                                                            <input type="hidden" name="slot" value="all">
                                                        @endif
                                                        <button type="submit" 
                                                            title="{{ $hasAccUp ? 'Batalkan ACC Seminar' : 'Berikan ACC Seminar' }}"
                                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm
                                                            {{ $hasAccUp ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}">
                                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            ACC SEMINAR
                                                        </button>
                                                    </form>
                                                    <div class="inline-flex items-center gap-2 border-l border-slate-200 dark:border-slate-700 pl-2 ml-1 self-center text-[9px] font-bold text-slate-500 dark:text-slate-400">
                                                        <span class="inline-flex items-center gap-1" title="Status ACC Pembimbing 1">
                                                            <span>P1</span>
                                                            <span class="w-2 h-2 rounded-full {{ $studentThesis->acc_up_p1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                                                        </span>
                                                        <span class="inline-flex items-center gap-1" title="Status ACC Pembimbing 2">
                                                            <span>P2</span>
                                                            <span class="w-2 h-2 rounded-full {{ $studentThesis->acc_up_p2 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- ACC Sidang Group --}}
                                                @php
                                                    $hasAccSidang = $isAdminOrKaprodi ? ($studentThesis->acc_sidang_p1 && $studentThesis->acc_sidang_p2) : ($isP1 ? $studentThesis->acc_sidang_p1 : ($isP2 ? $studentThesis->acc_sidang_p2 : false));
                                                @endphp
                                                <div class="inline-flex items-center gap-2 bg-slate-50 dark:bg-slate-900/50 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-700">
                                                    <form action="{{ route('theses.toggle-acc', [$studentThesis->id, 'sidang']) }}" method="POST" class="m-0 p-0 inline-flex items-center"
                                                        data-confirm="Apakah Anda yakin ingin {{ $hasAccSidang ? 'membatalkan' : 'memberikan' }} ACC Sidang untuk {{ $studentName }}?"
                                                        data-confirm-title="Konfirmasi ACC Sidang"
                                                        data-confirm-type="{{ $hasAccSidang ? 'warning' : 'emerald' }}">
                                                        @csrf
                                                        @if($isAdminOrKaprodi)
                                                            <input type="hidden" name="slot" value="all">
                                                        @endif
                                                        <button type="submit" 
                                                            title="{{ $hasAccSidang ? 'Batalkan ACC Sidang' : 'Berikan ACC Sidang' }}"
                                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm
                                                            {{ $hasAccSidang ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}">
                                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            ACC SIDANG
                                                        </button>
                                                    </form>
                                                    <div class="inline-flex items-center gap-2 border-l border-slate-200 dark:border-slate-700 pl-2 ml-1 self-center text-[9px] font-bold text-slate-500 dark:text-slate-400">
                                                        <span class="inline-flex items-center gap-1" title="Status ACC Pembimbing 1">
                                                            <span>P1</span>
                                                            <span class="w-2 h-2 rounded-full {{ $studentThesis->acc_sidang_p1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                                                        </span>
                                                        <span class="inline-flex items-center gap-1" title="Status ACC Pembimbing 2">
                                                            <span>P2</span>
                                                            <span class="w-2 h-2 rounded-full {{ $studentThesis->acc_sidang_p2 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                                            <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-900/60 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-700">
                                                Dosen Pembimbing: <span class="text-slate-700 dark:text-slate-300 font-bold">{{ $studentSessions->first()->dosen->name }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        @foreach($studentSessions as $session)
                                            @php
                                                $gKey = ($session->dosen_id ?? '0') . '_' . $session->scheduled_at->format('Y-m-d H:i');
                                                $isGroupSession = ($groupCountMap[$gKey] ?? 1) > 1;
                                            @endphp
                                            <div id="session-{{ $session->id }}" 
                                                 x-show="(showAllStudentSessions || {{ $loop->index < 3 ? 'true' : 'false' }}) && matchesStatusFilter('{{ $session->status }}')"
                                                 :class="isSelected('{{ $session->id }}') ? 'ring-2 ring-orange-500/80 border-orange-500 dark:border-orange-500 shadow-md' : 'border-slate-200/90 dark:border-slate-700/80'"
                                                 class="bg-slate-50/60 dark:bg-slate-900/80 border rounded-2xl p-5 sm:p-6 relative overflow-hidden group hover:shadow-lg hover:shadow-slate-200/40 dark:hover:shadow-none hover:border-orange-300 dark:hover:border-orange-500/40 transition-all flex flex-col justify-between">
                                                <div>
                                                    <!-- Status Indicator -->
                                                    <div class="absolute top-0 left-0 w-full h-1.5 
                                                        {{ $session->status === 'pending' ? 'bg-amber-400' : '' }}
                                                        {{ $session->status === 'approved' ? 'bg-orange-600' : '' }}
                                                        {{ $session->status === 'rejected' || $session->is_absent ? 'bg-red-500' : '' }}
                                                        {{ $session->status === 'completed' && !$session->is_absent ? 'bg-slate-300 dark:bg-slate-700' : '' }}
                                                    "></div>
                                                    
                                                    <div class="flex justify-between items-start gap-3 mb-4 pt-1">
                                                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                                            @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) && ($session->status !== 'completed' || $session->is_absent))
                                                                <span class="shrink-0 flex items-center pr-1">
                                                                    <input type="checkbox" 
                                                                           value="{{ $session->id }}"
                                                                           x-model="selectedSessionIds"
                                                                           @change="notifySelection()"
                                                                           class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500/30 transition-all cursor-pointer">
                                                                </span>
                                                            @endif
                                                            <div class="space-y-0.5">
                                                                <p class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</p>
                                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase">{{ $session->scheduled_at->format('H:i') }} WIB</p>
                                                            </div>
                                                        </label>
                                                        <div class="shrink-0">
                                                            @if($session->is_absent)
                                                                <x-status-badge type="red" label="TIDAK HADIR" />
                                                            @else
                                                                @php
                                                                    $perStudentStatusLabel = match($session->status) {
                                                                        'completed' => 'SELESAI',
                                                                        'approved' => 'DISETUJUI',
                                                                        'pending' => 'MENUNGGU',
                                                                        'rejected' => 'DITOLAK',
                                                                        default => strtoupper($session->status),
                                                                    };
                                                                @endphp
                                                                <x-status-badge 
                                                                    :type="$session->status === 'pending' ? 'amber' : ($session->status === 'approved' ? 'orange' : ($session->status === 'rejected' ? 'red' : ($session->status === 'completed' ? 'emerald' : 'slate')))" 
                                                                    :label="$perStudentStatusLabel" />
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-4 space-y-3">
                                                        <h5 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-tight leading-snug">
                                                            {{ $session->topic }}
                                                        </h5>
                                                        
                                                        <div class="flex items-center gap-2 flex-wrap text-[10px] font-bold">
                                                            @if($session->type === 'online')
                                                                @php 
                                                                    $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                                                                    $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                                                                    $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                                                                @endphp
                                                                @if($session->location)
                                                                    <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $isMeet ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/60' : ($isZoom ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/60' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100') }} transition-all shadow-2xs font-bold text-[10px] uppercase tracking-wider">
                                                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                        <span>{{ $isMeet ? 'Buka Meet' : ($isZoom ? 'Buka Zoom' : 'Buka Link') }}</span>
                                                                    </a>
                                                                @else
                                                                    <span class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 px-2 py-0.5 rounded border border-indigo-100 dark:border-indigo-800 uppercase tracking-wider">
                                                                        Daring
                                                                    </span>
                                                                @endif
                                                            @else
                                                                <span class="inline-flex items-center gap-1 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 uppercase tracking-wider shadow-2xs">
                                                                    <svg class="w-3 h-3 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> {{ $session->location ?? 'Offline' }}
                                                                </span>
                                                            @endif

                                                            @if($isGroupSession)
                                                                <span class="inline-flex items-center gap-1 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/40 px-2.5 py-1 rounded-lg border border-indigo-200 dark:border-indigo-800/80 uppercase tracking-wider shadow-2xs" title="Sesi Bimbingan Bersama">
                                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Kelompok ({{ $groupCountMap[$gKey] }})
                                                                </span>
                                                            @endif
                                                        </div>
                                                        
                                                        @if($session->notes)
                                                            <div class="mt-3.5 p-3.5 bg-white dark:bg-slate-800/80 rounded-xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                                                                <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Catatan Mahasiswa</p>
                                                                <p class="text-xs text-slate-600 dark:text-slate-300 italic leading-relaxed">"{{ $session->notes }}"</p>
                                                            </div>
                                                        @endif

                                                        <!-- Catatan Dosen (dengan mode revisi) -->
                                                        <div x-data="{ editingFeedback: false }" class="mt-3.5">
                                                            @if($session->feedback)
                                                                <div x-show="!editingFeedback" class="p-3.5 bg-orange-50/50 dark:bg-orange-950/20 rounded-xl border border-orange-200/70 dark:border-orange-900/40 shadow-2xs group relative">
                                                                    <div class="flex items-center justify-between gap-2 mb-1">
                                                                        <p class="text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest flex items-center gap-1">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                                                            <span>Catatan Dosen</span>
                                                                        </p>
                                                                        @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                            <button type="button" 
                                                                                    @click="editingFeedback = true" 
                                                                                    title="Ubah catatan bimbingan" 
                                                                                    class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 hover:bg-orange-100/60 dark:hover:bg-orange-900/40 transition-all cursor-pointer">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                                <span>Ubah</span>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                    <p class="text-xs text-slate-700 dark:text-slate-300 font-medium leading-relaxed whitespace-pre-line">{{ $session->feedback }}</p>
                                                                    @if($session->feedback_document_url)
                                                                        <div class="mt-2 pt-1.5 border-t border-orange-200/60 dark:border-orange-900/40 flex items-center justify-between">
                                                                            <a href="{{ $session->feedback_document_url }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                                                <x-google-drive-icon class="w-3.5 h-3.5 shrink-0" />
                                                                                <span>Dokumen Koreksi / Feedback Dosen</span>
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @elseif($session->status === 'completed' && !$session->is_absent && in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                <div x-show="!editingFeedback" class="p-2.5 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-center">
                                                                    <button type="button" 
                                                                            @click="editingFeedback = true" 
                                                                            class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                                        <span>+ Tambah Catatan Hasil Bimbingan</span>
                                                                    </button>
                                                                </div>
                                                            @endif

                                                            @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                <div x-show="editingFeedback" x-cloak class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-orange-300 dark:border-orange-500/50 shadow-sm" x-transition>
                                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="completed">
                                                                        <div class="flex items-center justify-between gap-2 mb-1.5">
                                                                            <label class="block text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Revisi Catatan Dosen:</label>
                                                                            <span class="text-[9px] text-slate-400 font-medium">Bimbingan Selesai</span>
                                                                        </div>
                                                                        <textarea name="feedback" rows="3" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-2 leading-relaxed" placeholder="Tuliskan revisi catatan hasil bimbingan...">{{ $session->feedback }}</textarea>
                                                                        <div class="mb-2">
                                                                            <label class="block text-[9px] font-bold text-slate-600 dark:text-slate-400 mb-1">Link Dokumen Feedback / Revisi (Opsional):</label>
                                                                            <div class="relative">
                                                                                <input type="url" name="feedback_document_url" value="{{ $session->feedback_document_url }}" placeholder="https://drive.google.com/..." class="block w-full text-[11px] rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-orange-500 focus:border-orange-500 py-1 pl-7 pr-2">
                                                                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-slate-400">
                                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                                                </div>
                                                                            </div>
                                                                            <span class="text-[8px] text-slate-400">Tautan Google Drive / Docs berisi dokumen yang sudah dikoreksi</span>
                                                                        </div>
                                                                        <div class="flex items-center justify-end gap-1.5">
                                                                            <button type="button" @click="editingFeedback = false" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-black uppercase cursor-pointer">Batal</button>
                                                                            <button type="submit" class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer">Simpan Catatan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @if($session->document_path)
                                                            <div class="mt-3 pt-1">
                                                                <a href="{{ $session->document_path }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-orange-600 dark:text-orange-400 font-bold hover:underline">
                                                                    <x-google-drive-icon class="w-3.5 h-3.5 shrink-0" />
                                                                    <span>Lihat Dokumen Mahasiswa</span>
                                                                </a>
                                                            </div>
                                                        @endif

                                                        <!-- Konfirmasi Kehadiran Mahasiswa -->
                                                        @if(!in_array($session->status, ['completed', 'rejected']))
                                                            <div class="mt-3.5 p-3 rounded-xl border space-y-1.5 {{ $session->student_attendance_status === 'attending' ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800/80' : ($session->student_attendance_status === 'permission' ? 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/80' : 'bg-white dark:bg-slate-800/80 border-slate-200/80 dark:border-slate-700/80 shadow-2xs') }}">
                                                                <div class="flex items-center justify-between gap-2">
                                                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Kehadiran Mahasiswa:</span>
                                                                    @if($session->student_attendance_status === 'attending')
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[9px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-700">
                                                                            <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                            Akan Hadir
                                                                        </span>
                                                                    @elseif($session->student_attendance_status === 'permission')
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[9px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-700">
                                                                            <svg class="w-2.5 h-2.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"></path></svg>
                                                                            Izin / Berhalangan
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[9px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-600">
                                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                                            Menunggu Konfirmasi
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                
                                                                @if($session->student_attendance_status === 'permission' && $session->student_attendance_reason)
                                                                    <div class="mt-1 pt-1.5 border-t border-amber-200/60 dark:border-amber-800/40">
                                                                        <span class="text-[9px] font-bold text-amber-800 dark:text-amber-300">Alasan Izin:</span>
                                                                        <p class="text-[11px] text-amber-950 dark:text-amber-200 italic font-medium leading-relaxed">"{{ $session->student_attendance_reason }}"</p>
                                                                        @if($session->student_confirmed_at)
                                                                            <p class="text-[9px] text-amber-700/80 dark:text-amber-400/80 text-right mt-1 font-medium">{{ $session->student_confirmed_at->locale('id')->translatedFormat('d M H:i') }} WIB</p>
                                                                        @endif
                                                                    </div>
                                                                @elseif($session->student_attendance_status === 'attending' && $session->student_confirmed_at)
                                                                    <p class="text-[9px] text-emerald-700/80 dark:text-emerald-400/80 text-right font-medium">Dikonfirmasi: {{ $session->student_confirmed_at->locale('id')->translatedFormat('d M H:i') }} WIB</p>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                @if($session->status === 'pending')
                                                <div class="flex items-center space-x-2 mt-5 pt-4 border-t border-slate-200/70 dark:border-slate-800">
                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="w-full px-3 py-2 bg-orange-600 text-white hover:bg-orange-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Terima</button>
                                                    </form>
                                                    @can('update', $session)
                                                        <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2.5 py-2 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 dark:border-orange-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs inline-flex items-center gap-1" title="Ubah / Reschedule Jadwal">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                            <span>Ubah</span>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $session)
                                                        <button type="button" 
                                                                @click="openCancelModalFromEl($el)"
                                                                onclick="window.openCancelModalFromEl(this)"
                                                                aria-label="Batalkan jadwal bimbingan"
                                                                data-session-id="{{ $session->id }}"
                                                                data-student-name="{{ e($session->thesis?->student?->name ?? 'Mahasiswa') }}"
                                                                data-student-npm="{{ e($session->thesis?->student?->identifier ?? '-') }}"
                                                                data-topic="{{ e($session->topic) }}"
                                                                data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                                data-is-group="{{ $isGroupSession ? '1' : '0' }}"
                                                                data-group-count="{{ $groupCountMap[$gKey] ?? 1 }}"
                                                                class="flex-1 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-600 hover:border-rose-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">
                                                            Tolak / Batal
                                                        </button>
                                                    @endcan
                                                </div>
                                                @elseif($session->status === 'approved')
                                                <div class="mt-auto pt-5 border-t border-slate-200/70 dark:border-slate-800" x-data="{ showFeedback: false }">
                                                    <div class="flex items-center gap-1.5" x-show="!showFeedback">
                                                        <button type="button" @click="showFeedback = true" class="flex-1 px-3 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Selesai</button>
                                                        @can('update', $session)
                                                            <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2.5 py-2 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 dark:border-orange-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs inline-flex items-center gap-1" title="Ubah / Reschedule Jadwal">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                            <span>Ubah</span>
                                                        </a>
                                                        @endcan
                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="absent">
                                                            <button type="submit" class="px-2.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 hover:border-red-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Absen</button>
                                                        </form>
                                                        @can('delete', $session)
                                                            <button type="button" 
                                                                    @click="openCancelModalFromEl($el)"
                                                                    onclick="window.openCancelModalFromEl(this)"
                                                                    aria-label="Batalkan jadwal bimbingan"
                                                                    data-session-id="{{ $session->id }}"
                                                                    data-student-name="{{ e($session->thesis?->student?->name ?? 'Mahasiswa') }}"
                                                                    data-student-npm="{{ e($session->thesis?->student?->identifier ?? '-') }}"
                                                                    data-topic="{{ e($session->topic) }}"
                                                                    data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                    data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                                    data-is-group="{{ $isGroupSession ? '1' : '0' }}"
                                                                    data-group-count="{{ $groupCountMap[$gKey] ?? 1 }}"
                                                                    class="px-2.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:border-rose-300 dark:border-rose-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs cursor-pointer flex items-center justify-center" 
                                                                    title="Batalkan Jadwal Bimbingan">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                    <div x-show="showFeedback" x-cloak class="mt-3" x-transition>
                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="completed">
                                                            <textarea name="feedback" rows="3" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-2" placeholder="Catatan hasil bimbingan..."></textarea>
                                                            <div class="mb-3">
                                                                <div class="relative">
                                                                    <input type="url" name="feedback_document_url" placeholder="Link dokumen/revisi Google Drive (opsional)..." class="block w-full text-[11px] rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-orange-500 focus:border-orange-500 py-1 pl-7 pr-2">
                                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-slate-400">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex space-x-2">
                                                                <button type="submit" class="flex-1 px-3 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Simpan</button>
                                                                <button type="button" @click="showFeedback = false" class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer">Batal</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                @elseif($session->is_absent)
                                                <div class="mt-auto pt-5 border-t border-slate-200/70 dark:border-slate-800 flex items-center gap-1.5">
                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" 
                                                                class="w-full px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer flex items-center justify-center gap-1"
                                                                title="Batalkan status tidak hadir dan kembalikan ke jadwal aktif">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                            <span>Batalkan Tidak Hadir</span>
                                                        </button>
                                                    </form>
                                                    @can('update', $session)
                                                        <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2.5 py-2 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 dark:border-orange-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs inline-flex items-center gap-1" title="Ubah / Reschedule Jadwal">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                            <span>Ubah</span>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $session)
                                                        <button type="button" 
                                                                @click="openCancelModalFromEl($el)"
                                                                onclick="window.openCancelModalFromEl(this)"
                                                                aria-label="Batalkan jadwal bimbingan"
                                                                data-session-id="{{ $session->id }}"
                                                                data-student-name="{{ e($session->thesis?->student?->name ?? 'Mahasiswa') }}"
                                                                data-student-npm="{{ e($session->thesis?->student?->identifier ?? '-') }}"
                                                                data-topic="{{ e($session->topic) }}"
                                                                data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                                data-is-group="{{ $isGroupSession ? '1' : '0' }}"
                                                                data-group-count="{{ $groupCountMap[$gKey] ?? 1 }}"
                                                                class="px-2.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:border-rose-300 dark:border-rose-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs cursor-pointer flex items-center justify-center" 
                                                                title="Batalkan / Hapus Sesi Bimbingan">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    @endcan
                                                </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($studentSessions->count() > 3)
                                        <div class="pt-1 flex justify-center">
                                            <button type="button"
                                                    @click="showAllStudentSessions = !showAllStudentSessions"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-all cursor-pointer">
                                                <span x-text="showAllStudentSessions ? 'Ringkas (tampilkan 3 sesi terbaru)' : 'Tampilkan semua {{ $studentSessions->count() }} sesi'"></span>
                                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showAllStudentSessions ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

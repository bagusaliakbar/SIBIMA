                        <!-- 1. TAMPILAN PER SESI BIMBINGAN (TIMELINE CHRONOLOGICAL) -->
                        <div x-show="cardGrouping === 'session'" class="space-y-10">
                            @foreach($timelineSections as $section)
                                <div class="space-y-4">
                                    <!-- Timeline Section Header -->
                                    <div class="flex items-center justify-between gap-3 pb-2.5 border-b border-slate-200/80 dark:border-slate-800">
                                        <div class="flex items-center gap-2.5 flex-wrap">
                                            @if($section['id'] === 'today')
                                                <span class="flex h-3 w-3 relative shrink-0">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                                </span>
                                                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                                    <span>Hari Ini</span>
                                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 normal-case hidden sm:inline">• {{ $section['subtitle'] }}</span>
                                                </h3>
                                            @elseif($section['id'] === 'tomorrow')
                                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                                                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                                    <span>Besok</span>
                                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 normal-case hidden sm:inline">• {{ $section['subtitle'] }}</span>
                                                </h3>
                                            @elseif($section['id'] === 'overdue')
                                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                                                <h3 class="text-sm font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-2">
                                                    <span>{{ $section['title'] }}</span>
                                                </h3>
                                            @else
                                                <span class="w-2.5 h-2.5 rounded-full bg-slate-400 dark:bg-slate-500 shrink-0"></span>
                                                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                                    <span>{{ $section['title'] }}</span>
                                                    @if(!empty($section['subtitle']))
                                                        <span class="text-xs font-normal text-slate-500 dark:text-slate-400 normal-case hidden sm:inline">• {{ $section['subtitle'] }}</span>
                                                    @endif
                                                </h3>
                                            @endif
                                        </div>

                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border {{ $section['badge_class'] }}">
                                            {{ $section['badge'] }}
                                        </span>
                                    </div>

                                    <!-- Session Cards in this Timeline Section -->
                                    <div class="space-y-6">
                                        @foreach($section['groups'] as $groupKey => $sessionItems)
                                            @php
                                                $firstSession = $sessionItems->first();
                                                $sessionDosen = $firstSession->dosen ?? $firstSession->thesis?->pembimbing1;
                                                $totalMhs = $sessionItems->count();
                                                $attendingCount = $sessionItems->where('student_attendance_status', 'attending')->count();
                                                $permissionCount = $sessionItems->where('student_attendance_status', 'permission')->count();
                                                $pendingCount = $sessionItems->where('student_attendance_status', 'pending')->count();
                                                
                                                $isMeet = Str::contains($firstSession->location ?? '', 'meet.google.com'); 
                                                $isZoom = Str::contains($firstSession->location ?? '', ['zoom.us', 'zoom.com']);
                                                $linkUrl = Str::startsWith($firstSession->location ?? '', 'http') ? $firstSession->location : 'https://' . $firstSession->location;
                                                $actionableSessionIds = $sessionItems->filter(fn($s) => $s->status !== 'completed' || $s->is_absent)->pluck('id')->values()->all();
                                                $hasActionableInGroup = $sessionItems->contains(fn($s) => in_array($s->status, ['pending', 'approved']));
                                                $isGroupAllDone = !$hasActionableInGroup;
                                            @endphp

                                            <div x-data="{ sessionOpen: {{ $isGroupAllDone ? 'false' : 'true' }} }"
                                                 x-show="sessionStatusFilter === 'all' || (sessionStatusFilter === 'actionable' ? {{ $hasActionableInGroup ? 'true' : 'false' }} : {{ $isGroupAllDone ? 'true' : 'false' }})"
                                                 class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden transition-all hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-md">
                                                <!-- Session Header Bar -->
                                                <div class="p-4 sm:px-5 bg-slate-50/80 dark:bg-slate-800/90 border-b border-slate-200/80 dark:border-slate-700/80 flex flex-col md:flex-row md:items-center justify-between gap-3">
                                                    <div class="space-y-1.5 min-w-0 flex-1">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <!-- Tanggal & Jam -->
                                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-700/80 text-slate-800 dark:text-slate-100 border border-slate-200/90 dark:border-slate-600/80 shadow-2xs text-xs">
                                                                <div class="flex items-center gap-1.5 font-extrabold text-slate-900 dark:text-white">
                                                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                    <span>{{ $firstSession->scheduled_at->locale('id')->translatedFormat('d M Y') }}</span>
                                                                </div>
                                                                <span class="text-slate-300 dark:text-slate-500 font-light">•</span>
                                                                <div class="flex items-center gap-1 font-semibold text-slate-600 dark:text-slate-300">
                                                                    <svg class="w-3 h-3 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    <span>{{ $firstSession->time_range }}</span>
                                                                </div>
                                                            </div>

                                                            @if($firstSession->scheduled_at->isToday())
                                                                <span class="px-2.5 py-1 rounded-lg bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-300 text-[11px] font-black uppercase tracking-wider border border-red-200 dark:border-red-800/80 animate-pulse">
                                                                    Hari Ini
                                                                </span>
                                                            @endif

                                                            <!-- Lokasi / Metode -->
                                                            @if($firstSession->type === 'online')
                                                                @if($firstSession->location)
                                                                    <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl {{ $isMeet ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100' : ($isZoom ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 hover:bg-blue-100' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800') }} transition-all font-bold text-xs shadow-2xs">
                                                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                        <span>{{ $isMeet ? 'Buka Google Meet' : ($isZoom ? 'Buka Zoom' : 'Buka Link Online') }}</span>
                                                                    </a>
                                                                @else
                                                                    <span class="inline-flex items-center gap-1.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-xl border border-indigo-200 dark:border-indigo-800 text-xs font-bold shadow-2xs">
                                                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                        <span>Daring</span>
                                                                    </span>
                                                                @endif
                                                            @else
                                                                <span class="inline-flex items-center gap-1.5 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-600 text-xs font-bold shadow-2xs">
                                                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                    <span>{{ $firstSession->location ?? 'Offline' }}</span>
                                                                </span>
                                                            @endif

                                                            <!-- Dosen info jika relevan -->
                                                            @if($sessionDosen && (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi'))
                                                                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                                                    • Dosen: <strong class="text-slate-700 dark:text-slate-200">{{ $sessionDosen->name }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <!-- Topik Sesi jika multi-mahasiswa -->
                                                        @if($totalMhs > 1)
                                                            <div class="pt-1">
                                                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1.5">Topik Sesi:</span>
                                                                <h4 class="inline text-sm sm:text-base font-extrabold text-slate-900 dark:text-white leading-snug">
                                                                    {{ $firstSession->topic }}
                                                                </h4>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Header Right Actions & Badges -->
                                                    <div class="flex items-center gap-2 shrink-0 flex-wrap">
                                                        @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) && count($actionableSessionIds) > 0)
                                                            <button type="button" 
                                                                    @click="toggleSelectGroup(@json($actionableSessionIds))"
                                                                    :class="isGroupSelected(@json($actionableSessionIds)) ? 'bg-orange-600 text-white border-orange-600 shadow-sm' : 'bg-white dark:bg-slate-700/80 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-600 hover:border-orange-300 hover:text-orange-600 dark:hover:text-orange-400'"
                                                                    class="px-2.5 py-1.5 rounded-xl border text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                                <span x-text="isGroupSelected(@json($actionableSessionIds)) ? 'Lepas Pilihan Sesi' : 'Pilih Sesi Ini'">Pilih Sesi Ini</span>
                                                            </button>
                                                        @endif
                                                        @if($totalMhs > 1)
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-200/80 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-black uppercase tracking-wider border border-transparent dark:border-slate-600 shadow-2xs">
                                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                <span>{{ $totalMhs }} Mahasiswa</span>
                                                            </span>
                                                            @if($attendingCount > 0)
                                                                 <span class="px-2 py-0.5 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800/80 flex items-center gap-1">
                                                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                    <span>{{ $attendingCount }} Hadir</span>
                                                                </span>
                                                            @endif
                                                            @if($permissionCount > 0)
                                                                 <span class="px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 text-xs font-bold border border-amber-200 dark:border-amber-800/80 flex items-center gap-1">
                                                                    <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01"></path></svg>
                                                                    <span>{{ $permissionCount }} Izin</span>
                                                                </span>
                                                            @endif
                                                            @if($pendingCount > 0)
                                                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-600 flex items-center gap-1">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                                    <span>{{ $pendingCount }} Belum Respon</span>
                                                                </span>
                                                            @endif
                                                        @else
                                                            <!-- Single Student Session Status Badge in Header -->
                                                            @if($firstSession->is_absent)
                                                                <x-status-badge type="red" label="TIDAK HADIR" />
                                                            @else
                                                                @php
                                                                    $cardSessionStatusLabel = match($firstSession->status) {
                                                                        'completed' => 'SELESAI',
                                                                        'approved' => 'DISETUJUI',
                                                                        'pending' => 'MENUNGGU',
                                                                        'rejected' => 'DITOLAK',
                                                                        default => strtoupper($firstSession->status),
                                                                    };
                                                                @endphp
                                                                <x-status-badge 
                                                                    :type="$firstSession->status === 'pending' ? 'amber' : ($firstSession->status === 'approved' ? 'orange' : ($firstSession->status === 'rejected' ? 'red' : ($firstSession->status === 'completed' ? 'emerald' : 'slate')))" 
                                                                    :label="$cardSessionStatusLabel" />
                                                            @endif
                                                        @endif

                                                        <button type="button"
                                                                @click="sessionOpen = !sessionOpen"
                                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500 text-[11px] font-bold transition-all cursor-pointer"
                                                                :title="sessionOpen ? 'Ringkas kartu sesi' : 'Tampilkan rincian sesi'">
                                                            <span x-text="sessionOpen ? 'Ringkas' : 'Rincian'"></span>
                                                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="sessionOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Session Content (Single vs Multi) -->
                                                <div x-show="sessionOpen" x-transition>
                                                @if($totalMhs === 1)
                                                    {{-- TAMPILAN TERPADU 1-ON-1 (TIDAK ADA NESTING GANDA) --}}
                                                    @php
                                                        $session = $firstSession;
                                                        $student = $session->thesis?->student;
                                                        $thesis = $session->thesis;
                                                        $sMentoringCount = (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') 
                                                            ? $thesis?->completed_mentoring_count 
                                                            : $thesis?->getCompletedMentoringCountForDosen(Auth::id());
                                                    @endphp

                                                    <div id="session-{{ $session->id }}" 
                                                         :class="isSelected('{{ $session->id }}') ? 'ring-2 ring-orange-500/80 border-orange-500 dark:border-orange-500 shadow-md' : ''"
                                                         class="p-5 sm:px-6 space-y-4">
                                                        
                                                        <!-- Student Profile Bar & Quick Attendance Info -->
                                                        <div class="flex items-start justify-between gap-3">
                                                            <label class="flex items-center gap-3 min-w-0 cursor-pointer select-none">
                                                                @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) && ($session->status !== 'completed' || $session->is_absent))
                                                                    <span class="shrink-0 flex items-center pr-1">
                                                                        <input type="checkbox" 
                                                                               value="{{ $session->id }}"
                                                                               x-model="selectedSessionIds"
                                                                               @change="notifySelection()"
                                                                               class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500/30 transition-all cursor-pointer">
                                                                    </span>
                                                                @endif
                                                                <div class="w-11 h-11 rounded-2xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0">
                                                                    <img src="{{ $student?->avatar_url }}" alt="{{ $student?->name ?? 'Mhs' }}" class="w-full h-full object-cover">
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <h5 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight truncate">{{ $student?->name ?? 'Mahasiswa' }}</h5>
                                                                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                                                        <span class="text-xs text-slate-500 dark:text-slate-400 font-bold font-mono">{{ $student?->identifier ?? '-' }}</span>
                                                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-[11px] border border-slate-200 dark:border-slate-600">
                                                                            {{ $sMentoringCount }}x Bimbingan
                                                                        </span>
                                                                        @if($thesis?->pembimbing1)
                                                                            <span class="text-[11px] text-slate-500 dark:text-slate-400 hidden sm:inline">P1: <strong class="text-slate-700 dark:text-slate-300">{{ $thesis->pembimbing1->name }}</strong></span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </label>

                                                            <!-- Kehadiran Konfirmasi Mahasiswa -->
                                                            @if(!in_array($session->status, ['completed', 'rejected']))
                                                                <div class="shrink-0 text-right">
                                                                    @if($session->student_attendance_status === 'attending')
                                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[11px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-700">
                                                                            <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                            Akan Hadir{{ $session->formatted_arrival_time ? ' • ' . $session->formatted_arrival_time : '' }}
                                                                        </span>
                                                                    @elseif($session->student_attendance_status === 'permission')
                                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[11px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-700">
                                                                            <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"></path></svg>
                                                                            Izin
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-600">
                                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                                            Menunggu Respon Mhs
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Detail Topik & Judul Skripsi -->
                                                        <div class="p-3.5 bg-slate-50/80 dark:bg-slate-900/50 rounded-xl border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                                                            <div class="flex items-baseline gap-2">
                                                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 shrink-0">Topik Sesi:</span>
                                                                <p class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white leading-snug">{{ $session->topic }}</p>
                                                            </div>
                                                            @if($thesis?->title)
                                                                <div class="flex items-baseline gap-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                                                                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 shrink-0">Judul Skripsi:</span>
                                                                    <p class="text-xs text-slate-600 dark:text-slate-300 font-medium line-clamp-2 leading-relaxed" title="{{ $thesis->title }}">{{ $thesis->title }}</p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Catatan & Feedback -->
                                                        <div class="space-y-2">
                                                            <!-- Alasan Izin (jika ada) -->
                                                            @if($session->student_attendance_status === 'permission' && $session->student_attendance_reason)
                                                                <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-200/80 dark:border-amber-800/60 text-xs">
                                                                    <span class="text-[11px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider block">Alasan Izin Mahasiswa:</span>
                                                                    <p class="text-[11px] text-amber-950 dark:text-amber-100 italic mt-0.5 font-medium">"{{ $session->student_attendance_reason }}"</p>
                                                                </div>
                                                            @endif

                                                            <!-- Catatan Mahasiswa -->
                                                            @if($session->notes)
                                                                <div class="p-2.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                                                                    <p class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-0.5">Catatan Mahasiswa:</p>
                                                                    <p class="text-[11px] text-slate-600 dark:text-slate-300 italic leading-relaxed">"{{ $session->notes }}"</p>
                                                                </div>
                                                            @endif

                                                            <!-- Dokumen Mahasiswa Link -->
                                                            @if($session->document_path)
                                                                <div class="pt-0.5">
                                                                    <a href="{{ $session->document_path }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-orange-600 dark:text-orange-400 font-bold hover:underline">
                                                                        <x-google-drive-icon class="w-4 h-4 shrink-0" />
                                                                        <span>Lihat Dokumen Bimbingan Mahasiswa</span>
                                                                    </a>
                                                                </div>
                                                            @endif

                                                            <!-- Catatan Dosen (dengan mode revisi) -->
                                                            <div x-data="{ editingFeedback: false }">
                                                                @if($session->feedback)
                                                                    <div x-show="!editingFeedback" class="p-3 bg-orange-50/50 dark:bg-orange-950/20 rounded-xl border border-orange-200/70 dark:border-orange-900/40 shadow-2xs group relative">
                                                                        <div class="flex items-center justify-between gap-2 mb-1">
                                                                            <p class="text-[11px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest flex items-center gap-1">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                                                                <span>Catatan Hasil Bimbingan Dosen:</span>
                                                                            </p>
                                                                            @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                                <button type="button" 
                                                                                        @click="editingFeedback = true" 
                                                                                        title="Ubah catatan bimbingan" 
                                                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 hover:bg-orange-100/60 dark:hover:bg-orange-900/40 transition-all cursor-pointer">
                                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                                    <span>Ubah Catatan</span>
                                                                                </button>
                                                                            @endif
                                                                        </div>
                                                                        <p class="text-xs text-slate-700 dark:text-slate-300 font-medium leading-relaxed whitespace-pre-line">{{ $session->feedback }}</p>
                                                                        @if($session->feedback_document_url)
                                                                            <div class="mt-2 pt-1.5 border-t border-orange-200/60 dark:border-orange-900/40 flex items-center justify-between">
                                                                                <a href="{{ $session->feedback_document_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
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
                                                                            <span>+ Tambah Catatan Bimbingan</span>
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
                                                                                <label class="block text-[11px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Revisi Catatan Dosen:</label>
                                                                                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Bimbingan Selesai</span>
                                                                            </div>
                                                                            <textarea name="feedback" rows="2" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-2 leading-relaxed" placeholder="Tuliskan revisi catatan bimbingan...">{{ $session->feedback }}</textarea>
                                                                            <div class="mb-2">
                                                                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Link Dokumen Feedback / Revisi (Opsional):</label>
                                                                                <div class="relative">
                                                                                    <input type="url" name="feedback_document_url" value="{{ $session->feedback_document_url }}" placeholder="https://drive.google.com/..." class="block w-full text-xs rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-orange-500 focus:border-orange-500 py-1.5 pl-7 pr-2">
                                                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-slate-500 dark:text-slate-400">
                                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                                                    </div>
                                                                                </div>
                                                                                <span class="text-[11px] text-slate-500 dark:text-slate-400">Tautan Google Drive / Docs berisi dokumen yang sudah dikoreksi</span>
                                                                            </div>
                                                                            <div class="flex items-center justify-end gap-2">
                                                                                <button type="button" @click="editingFeedback = false" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-bold cursor-pointer">Batal</button>
                                                                                <button type="submit" class="px-3.5 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-xs font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer">Simpan Catatan</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Action Buttons Bar -->
                                                        @if($session->status !== 'completed' || $session->is_absent)
                                                            <div class="pt-3 border-t border-slate-200/80 dark:border-slate-700/80">
                                                                @if($session->status === 'pending')
                                                                    <div class="flex items-center space-x-2">
                                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <input type="hidden" name="status" value="approved">
                                                                            <button type="submit" class="w-full px-4 py-2 bg-orange-600 text-white hover:bg-orange-700 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                                <span>Terima Jadwal</span>
                                                                            </button>
                                                                        </form>
                                                                        @can('update', $session)
                                                                            <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-3 py-2 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-2xs" title="Ubah Jadwal">Ubah</a>
                                                                        @endcan
                                                                        @can('delete', $session)
                                                                            <button type="button" 
                                                                                    @click="openCancelModalFromEl($el)"
                                                                                    onclick="window.openCancelModalFromEl(this)"
                                                                                    data-session-id="{{ $session->id }}"
                                                                                    data-student-name="{{ e($student?->name ?? 'Mahasiswa') }}"
                                                                                    data-student-npm="{{ e($student?->identifier ?? '-') }}"
                                                                                    data-topic="{{ e($session->topic) }}"
                                                                                    data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                                    data-scheduled-time="{{ $session->time_range }}"
                                                                                    data-is-group="0"
                                                                                    data-group-count="1"
                                                                                    class="px-3 py-2 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200 dark:border-slate-700 hover:border-rose-200 dark:border-rose-800 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-2xs cursor-pointer">Tolak</button>
                                                                        @endcan
                                                                    </div>
                                                                @elseif($session->status === 'approved')
                                                                    <div x-data="{ showFeedback: false }">
                                                                        <div class="flex items-center gap-2 flex-wrap" x-show="!showFeedback">
                                                                            <button type="button" @click="showFeedback = true" class="flex-1 px-4 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer flex items-center justify-center gap-1.5">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                                <span>Selesai & Beri Catatan</span>
                                                                            </button>
                                                                            @can('update', $session)
                                                                                <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-3 py-2 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-2xs" title="Ubah Jadwal">Ubah</a>
                                                                            @endcan
                                                                            <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                                                @csrf
                                                                                @method('PATCH')
                                                                                <input type="hidden" name="status" value="absent">
                                                                                <button type="submit" class="px-3 py-2 bg-white dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-950/40 text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 border border-slate-200 dark:border-slate-700 hover:border-red-200 dark:border-red-800 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-2xs cursor-pointer">Absen</button>
                                                                            </form>
                                                                            @can('delete', $session)
                                                                                <button type="button" 
                                                                                        @click="openCancelModalFromEl($el)"
                                                                                        onclick="window.openCancelModalFromEl(this)"
                                                                                        aria-label="Batalkan jadwal bimbingan"
                                                                                        data-session-id="{{ $session->id }}"
                                                                                        data-student-name="{{ e($student?->name ?? 'Mahasiswa') }}"
                                                                                        data-student-npm="{{ e($student?->identifier ?? '-') }}"
                                                                                        data-topic="{{ e($session->topic) }}"
                                                                                        data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                                        data-scheduled-time="{{ $session->time_range }}"
                                                                                        data-is-group="0"
                                                                                        data-group-count="1"
                                                                                        class="p-2 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-slate-200 dark:border-slate-700 hover:border-rose-200 dark:border-rose-800 rounded-xl transition-all shadow-2xs cursor-pointer flex items-center justify-center" 
                                                                                        title="Batalkan Jadwal Bimbingan">
                                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                                </button>
                                                                            @endcan
                                                                        </div>
                                                                        <div x-show="showFeedback" x-cloak class="mt-2.5 p-3.5 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700" x-transition>
                                                                            <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                                                @csrf
                                                                                @method('PATCH')
                                                                                <input type="hidden" name="status" value="completed">
                                                                                <label class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">Catatan Hasil Bimbingan:</label>
                                                                                <textarea name="feedback" rows="2" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-emerald-500 focus:border-emerald-500 mb-2 leading-relaxed" placeholder="Tuliskan catatan dan arahan bimbingan untuk mahasiswa..."></textarea>
                                                                                <div class="mb-2.5">
                                                                                    <div class="relative">
                                                                                        <input type="url" name="feedback_document_url" placeholder="Link dokumen/revisi Google Drive (opsional)..." class="block w-full text-xs rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-emerald-500 focus:border-emerald-500 py-1.5 pl-7 pr-2">
                                                                                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-slate-500 dark:text-slate-400">
                                                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none text-xs text-slate-600 dark:text-slate-300">
                                                                                        <input type="checkbox" name="notify_student_wa" value="1" 
                                                                                               {{ ($session->scheduled_at && $session->scheduled_at->isPast()) ? '' : 'checked' }}
                                                                                               class="rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500">
                                                                                        <span class="flex items-center gap-1.5 font-medium">
                                                                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                                                                                            Kirim notifikasi WhatsApp ke mahasiswa
                                                                                            @if($session->scheduled_at && $session->scheduled_at->isPast())
                                                                                                <span class="text-[11px] text-slate-500 dark:text-slate-400 italic">(Otomatis nonaktif untuk sesi lampau)</span>
                                                                                            @endif
                                                                                        </span>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="flex items-center justify-end space-x-2">
                                                                                    <button type="button" @click="showFeedback = false" class="px-3 py-1.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                                                                                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm cursor-pointer">Simpan & Selesaikan</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                @elseif($session->is_absent)
                                                                    <div class="flex items-center gap-2">
                                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <input type="hidden" name="status" value="approved">
                                                                            <button type="submit" class="w-full px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-2xs cursor-pointer flex items-center justify-center gap-1.5">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                                                <span>Batalkan Status Tidak Hadir</span>
                                                                            </button>
                                                                        </form>
                                                                        @can('update', $session)
                                                                            <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-3 py-2 bg-white dark:bg-slate-800 hover:bg-orange-50 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-2xs">Ubah</a>
                                                                        @endcan
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    {{-- TAMPILAN SESI KELOMPOK (> 1 MAHASISWA) --}}
                                                    <div class="p-5 sm:p-6 bg-slate-100/50 dark:bg-slate-900/60">
                                                        <div class="grid grid-cols-1 {{ $totalMhs === 2 ? 'md:grid-cols-2' : 'md:grid-cols-2 lg:grid-cols-3' }} gap-5">
                                                            @foreach($sessionItems as $session)
                                                                @php
                                                                    $student = $session->thesis?->student;
                                                                    $thesis = $session->thesis;
                                                                    $sMentoringCount = (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') 
                                                                        ? $thesis?->completed_mentoring_count 
                                                                        : $thesis?->getCompletedMentoringCountForDosen(Auth::id());
                                                                @endphp

                                                                <div id="session-{{ $session->id }}" 
                                                                     :class="isSelected('{{ $session->id }}') ? 'ring-2 ring-orange-500/80 border-orange-500 dark:border-orange-500 shadow-md' : 'border-slate-200/90 dark:border-slate-700/80'"
                                                                     class="bg-white dark:bg-slate-800 border rounded-2xl p-5 relative overflow-hidden group hover:border-orange-300 dark:hover:border-orange-500/40 transition-all flex flex-col justify-between">
                                                                    <div>
                                                                        <!-- Top Bar Status Accent -->
                                                                        <div class="absolute top-0 left-0 w-full h-1.5 
                                                                            {{ $session->status === 'pending' ? 'bg-amber-400' : '' }}
                                                                            {{ $session->status === 'approved' ? 'bg-orange-600' : '' }}
                                                                            {{ $session->status === 'rejected' || $session->is_absent ? 'bg-red-500' : '' }}
                                                                            {{ $session->status === 'completed' && !$session->is_absent ? 'bg-slate-300 dark:bg-slate-700' : '' }}
                                                                        "></div>

                                                                        <!-- Student Avatar & Name Header -->
                                                                        <div class="flex items-start justify-between gap-3 mb-3 pt-1">
                                                                            <label class="flex items-center gap-3 min-w-0 cursor-pointer select-none">
                                                                                @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) && ($session->status !== 'completed' || $session->is_absent))
                                                                                    <span class="shrink-0 flex items-center pr-1">
                                                                                        <input type="checkbox" 
                                                                                               value="{{ $session->id }}"
                                                                                               x-model="selectedSessionIds"
                                                                                               @change="notifySelection()"
                                                                                               class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500/30 transition-all cursor-pointer">
                                                                                    </span>
                                                                                @endif
                                                                                <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0">
                                                                                    <img src="{{ $student?->avatar_url }}" alt="{{ $student?->name ?? 'Mhs' }}" class="w-full h-full object-cover">
                                                                                </div>
                                                                                <div class="min-w-0">
                                                                                    <h5 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight truncate">{{ $student?->name ?? 'Mahasiswa' }}</h5>
                                                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold font-mono">{{ $student?->identifier ?? '-' }}</p>
                                                                                </div>
                                                                            </label>

                                                                            <!-- Status Badge -->
                                                                            <div class="shrink-0">
                                                                                @if($session->is_absent)
                                                                                    <x-status-badge type="red" label="TIDAK HADIR" />
                                                                                @else
                                                                                    <x-status-badge 
                                                                                        :type="$session->status === 'pending' ? 'amber' : ($session->status === 'approved' ? 'orange' : ($session->status === 'rejected' ? 'red' : ($session->status === 'completed' ? 'emerald' : 'slate')))" 
                                                                                        :label="$session->status === 'completed' ? 'HADIR' : strtoupper($session->status)" />
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <!-- Judul Skripsi & Info Tambahan -->
                                                                        <div class="space-y-2 mb-3">
                                                                            @if($thesis?->title)
                                                                                <p class="text-[11px] text-slate-600 dark:text-slate-300 font-medium line-clamp-2 leading-relaxed" title="{{ $thesis->title }}">
                                                                                    <span class="text-slate-500 dark:text-slate-400 font-bold uppercase text-[11px] tracking-wider block">Judul Skripsi:</span>
                                                                                    {{ $thesis->title }}
                                                                                </p>
                                                                            @endif

                                                                            <div class="flex items-center gap-2 flex-wrap text-[11px]">
                                                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold border border-slate-200 dark:border-slate-600">
                                                                                    {{ $sMentoringCount }}x Bimbingan
                                                                                </span>
                                                                                @if($thesis?->pembimbing1)
                                                                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">P1: <strong class="text-slate-700 dark:text-slate-300">{{ $thesis->pembimbing1->name }}</strong></span>
                                                                                @endif
                                                                            </div>

                                                                            <!-- Catatan Mahasiswa -->
                                                                            @if($session->notes)
                                                                                <div class="p-2.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                                                                                    <p class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-0.5">Catatan Mhs:</p>
                                                                                    <p class="text-[11px] text-slate-600 dark:text-slate-300 italic leading-relaxed">"{{ $session->notes }}"</p>
                                                                                </div>
                                                                            @endif

                                                                            <!-- Catatan Dosen (dengan mode revisi) -->
                                                                            <div x-data="{ editingFeedback: false }">
                                                                                @if($session->feedback)
                                                                                    <div x-show="!editingFeedback" class="p-2.5 bg-orange-50/50 dark:bg-orange-950/20 rounded-xl border border-orange-200/70 dark:border-orange-900/40 shadow-2xs group relative">
                                                                                        <div class="flex items-center justify-between gap-2 mb-0.5">
                                                                                            <p class="text-[11px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest flex items-center gap-1">
                                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                                                                                <span>Catatan Dosen:</span>
                                                                                            </p>
                                                                                            @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                                                <button type="button" 
                                                                                                        @click="editingFeedback = true" 
                                                                                                        title="Ubah catatan bimbingan" 
                                                                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[11px] font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 hover:bg-orange-100/60 dark:hover:bg-orange-900/40 transition-all cursor-pointer">
                                                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                                                    <span>Ubah</span>
                                                                                                </button>
                                                                                            @endif
                                                                                        </div>
                                                                                        <p class="text-[11px] text-slate-700 dark:text-slate-300 font-medium leading-relaxed whitespace-pre-line">{{ $session->feedback }}</p>
                                                                                        @if($session->feedback_document_url)
                                                                                            <div class="mt-2 pt-1.5 border-t border-orange-200/60 dark:border-orange-900/40 flex items-center justify-between">
                                                                                                <a href="{{ $session->feedback_document_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                                                                    <x-google-drive-icon class="w-3.5 h-3.5 shrink-0" />
                                                                                                    <span>Dokumen Koreksi</span>
                                                                                                </a>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                @elseif($session->status === 'completed' && !$session->is_absent && in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                                    <div x-show="!editingFeedback" class="p-2 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-center">
                                                                                        <button type="button" 
                                                                                                @click="editingFeedback = true" 
                                                                                                class="text-[11px] font-bold text-orange-600 dark:text-orange-400 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                                                            <span>+ Tambah Catatan Bimbingan</span>
                                                                                        </button>
                                                                                    </div>
                                                                                @endif

                                                                                @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                                    <div x-show="editingFeedback" x-cloak class="p-2.5 bg-white dark:bg-slate-800 rounded-xl border border-orange-300 dark:border-orange-500/50 shadow-sm" x-transition>
                                                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                                                                            <input type="hidden" name="status" value="completed">
                                                                                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                                                                                <label class="block text-[11px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Revisi Catatan Dosen:</label>
                                                                                                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Bimbingan Selesai</span>
                                                                                            </div>
                                                                                            <textarea name="feedback" rows="2" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-2 leading-relaxed" placeholder="Tuliskan revisi catatan bimbingan...">{{ $session->feedback }}</textarea>
                                                                                            <div class="mb-2">
                                                                                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Link Dokumen Feedback / Revisi (Opsional):</label>
                                                                                                <div class="relative">
                                                                                                    <input type="url" name="feedback_document_url" value="{{ $session->feedback_document_url }}" placeholder="https://drive.google.com/..." class="block w-full text-[11px] rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-orange-500 focus:border-orange-500 py-1 pl-7 pr-2">
                                                                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-slate-500 dark:text-slate-400">
                                                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="flex items-center justify-end gap-1.5">
                                                                                                <button type="button" @click="editingFeedback = false" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[11px] font-black uppercase cursor-pointer">Batal</button>
                                                                                                <button type="submit" class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer">Simpan Catatan</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                @endif
                                                                            </div>

                                                                            <!-- Dokumen Link -->
                                                                            @if($session->document_path)
                                                                                <div class="pt-0.5">
                                                                                    <a href="{{ $session->document_path }}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] text-orange-600 dark:text-orange-400 font-bold hover:underline">
                                                                                        <x-google-drive-icon class="w-3.5 h-3.5 shrink-0" />
                                                                                        <span>Lihat Dokumen</span>
                                                                                    </a>
                                                                                </div>
                                                                            @endif

                                                                            <!-- Kehadiran Mahasiswa -->
                                                                            @if(!in_array($session->status, ['completed', 'rejected']))
                                                                                <div class="mt-2.5 p-2.5 rounded-xl border space-y-1 {{ $session->student_attendance_status === 'attending' ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800/80' : ($session->student_attendance_status === 'permission' ? 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/80' : 'bg-slate-50/80 dark:bg-slate-900/40 border-slate-200/80 dark:border-slate-700/80') }}">
                                                                                    <div class="flex items-center justify-between gap-2">
                                                                                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Kehadiran:</span>
                                                                                        @if($session->student_attendance_status === 'attending')
                                                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[11px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-700">
                                                                                                <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                                                Akan Hadir{{ $session->formatted_arrival_time ? ' • ' . $session->formatted_arrival_time : '' }}
                                                                                            </span>
                                                                                        @elseif($session->student_attendance_status === 'permission')
                                                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[11px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-700">
                                                                                                <svg class="w-2.5 h-2.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"></path></svg>
                                                                                                Izin
                                                                                            </span>
                                                                                        @else
                                                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-600">
                                                                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                                                                Menunggu
                                                                                            </span>
                                                                                        @endif
                                                                                    </div>
                                                                                    
                                                                                    @if($session->student_attendance_status === 'permission' && $session->student_attendance_reason)
                                                                                        <div class="mt-1 pt-1 border-t border-amber-200/60 dark:border-amber-800/40">
                                                                                            <p class="text-[11px] text-amber-950 dark:text-amber-200 italic font-medium leading-relaxed">"{{ $session->student_attendance_reason }}"</p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <!-- Bottom Actions -->
                                                                    @if($session->status !== 'completed' || $session->is_absent)
                                                                        <div class="mt-4 pt-3 border-t border-slate-200/80 dark:border-slate-700/80">
                                                                            @if($session->status === 'pending')
                                                                                <div class="flex items-center space-x-2">
                                                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                                                        @csrf
                                                                                        @method('PATCH')
                                                                                        <input type="hidden" name="status" value="approved">
                                                                                        <button type="submit" class="w-full px-3 py-1.5 bg-orange-600 text-white hover:bg-orange-700 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Terima</button>
                                                                                    </form>
                                                                                    @can('update', $session)
                                                                                        <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all" title="Ubah Jadwal">Ubah</a>
                                                                                    @endcan
                                                                                    @can('delete', $session)
                                                                                        <button type="button" 
                                                                                                @click="openCancelModalFromEl($el)"
                                                                                                onclick="window.openCancelModalFromEl(this)"
                                                                                                data-session-id="{{ $session->id }}"
                                                                                                data-student-name="{{ e($student?->name ?? 'Mahasiswa') }}"
                                                                                                data-student-npm="{{ e($student?->identifier ?? '-') }}"
                                                                                                data-topic="{{ e($session->topic) }}"
                                                                                                data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                                                data-scheduled-time="{{ $session->time_range }}"
                                                                                                data-is-group="{{ $totalMhs > 1 ? '1' : '0' }}"
                                                                                                data-group-count="{{ $totalMhs }}"
                                                                                                class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200 dark:border-slate-700 hover:border-rose-200 dark:border-rose-800 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all cursor-pointer">Tolak</button>
                                                                                    @endcan
                                                                                </div>
                                                                            @elseif($session->status === 'approved')
                                                                                <div x-data="{ showFeedback: false }">
                                                                                    <div class="flex items-center gap-1.5" x-show="!showFeedback">
                                                                                        <button type="button" @click="showFeedback = true" class="flex-1 px-3 py-1.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Selesai</button>
                                                                                        @can('update', $session)
                                                                                            <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2 py-1.5 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all" title="Ubah Jadwal">Ubah</a>
                                                                                        @endcan
                                                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                                                                            <input type="hidden" name="status" value="absent">
                                                                                            <button type="submit" class="px-2 py-1.5 bg-white dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-950/40 text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 border border-slate-200 dark:border-slate-700 hover:border-red-200 dark:border-red-800 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all cursor-pointer">Absen</button>
                                                                                        </form>
                                                                                        @can('delete', $session)
                                                                                            <button type="button" 
                                                                                                    @click="openCancelModalFromEl($el)"
                                                                                                    onclick="window.openCancelModalFromEl(this)"
                                                                                                    aria-label="Batalkan jadwal bimbingan"
                                                                                                    data-session-id="{{ $session->id }}"
                                                                                                    data-student-name="{{ e($student?->name ?? 'Mahasiswa') }}"
                                                                                                    data-student-npm="{{ e($student?->identifier ?? '-') }}"
                                                                                                    data-topic="{{ e($session->topic) }}"
                                                                                                    data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                                                    data-scheduled-time="{{ $session->time_range }}"
                                                                                                    data-is-group="{{ $totalMhs > 1 ? '1' : '0' }}"
                                                                                                    data-group-count="{{ $totalMhs }}"
                                                                                                    class="px-2 py-1.5 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-slate-200 dark:border-slate-700 hover:border-rose-200 dark:border-rose-800 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all cursor-pointer flex items-center justify-center" 
                                                                                                    title="Batalkan Jadwal Bimbingan">
                                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                                            </button>
                                                                                        @endcan
                                                                                    </div>
                                                                                    <div x-show="showFeedback" x-cloak class="mt-2" x-transition>
                                                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                                                                            <input type="hidden" name="status" value="completed">
                                                                                            <textarea name="feedback" rows="2" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-1.5" placeholder="Catatan hasil bimbingan..."></textarea>
                                                                                            <div class="mb-2">
                                                                                                <div class="relative">
                                                                                                    <input type="url" name="feedback_document_url" placeholder="Link dokumen/revisi Google Drive (opsional)..." class="block w-full text-[11px] rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-orange-500 focus:border-orange-500 py-1 pl-7 pr-2">
                                                                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-slate-500 dark:text-slate-400">
                                                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                    <label class="inline-flex items-center gap-1.5 cursor-pointer select-none text-[11px] text-slate-600 dark:text-slate-300">
                                                                                                        <input type="checkbox" name="notify_student_wa" value="1" 
                                                                                                               {{ ($session->scheduled_at && $session->scheduled_at->isPast()) ? '' : 'checked' }}
                                                                                                               class="rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500">
                                                                                                        <span class="flex items-center gap-1 font-medium">
                                                                                                            <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                                                                                                            <span>Notif WA</span>
                                                                                                            @if($session->scheduled_at && $session->scheduled_at->isPast())
                                                                                                                <span class="text-[11px] text-slate-500 dark:text-slate-400 italic">(off sesi lampau)</span>
                                                                                                            @endif
                                                                                                        </span>
                                                                                                    </label>
                                                                                                </div>
                                                                                                <div class="flex space-x-2">
                                                                                                <button type="submit" class="flex-1 px-3 py-1.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all cursor-pointer">Simpan</button>
                                                                                                <button type="button" @click="showFeedback = false" class="px-2 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 rounded-xl text-[11px] font-black uppercase cursor-pointer">Batal</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            @elseif($session->is_absent)
                                                                                <div class="flex items-center gap-1.5">
                                                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                                                        @csrf
                                                                                        @method('PATCH')
                                                                                        <input type="hidden" name="status" value="approved">
                                                                                        <button type="submit" class="w-full px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest transition-all cursor-pointer">Batal Absen</button>
                                                                                    </form>
                                                                                    @can('update', $session)
                                                                                        <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2 py-1.5 bg-white dark:bg-slate-800 hover:bg-orange-50 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all">Ubah</a>
                                                                                    @endcan
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

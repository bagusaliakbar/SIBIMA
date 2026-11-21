                        <!-- Mobile Cards View with Expandable Accordion (Viewport < 768px) -->
                        <div class="block md:hidden space-y-3.5">
                            @foreach($sessions as $session)
                                @php
                                    $student = $session->thesis?->student;
                                    $thesis = $session->thesis;
                                    $gKey = ($session->dosen_id ?? '0') . '_' . $session->scheduled_at->format('Y-m-d H:i');
                                    $isGroup = ($groupCountMap[$gKey] ?? 1) > 1;
                                    $sMentoringCount = (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') 
                                        ? $thesis?->completed_mentoring_count 
                                        : $thesis?->getCompletedMentoringCountForDosen(Auth::id());
                                    
                                    $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                                    $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                                    $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                                @endphp

                                <div id="mobile-dosen-session-{{ $session->id }}" 
                                     x-show="matchesStatusFilter('{{ $session->status }}')"
                                     class="rounded-2xl border border-slate-200/90 dark:border-slate-700/80 bg-white dark:bg-slate-800 shadow-2xs hover:shadow-xs transition-all overflow-hidden {{ $isGroup ? 'border-l-[3px] border-l-indigo-500/70 dark:border-l-indigo-400/70' : '' }}"
                                     :class="{'border-orange-500/80 ring-2 ring-orange-500/20': isSelected('{{ $session->id }}')}">
                                    
                                    <!-- Summary / Header (Tap to toggle accordion) -->
                                    <div class="p-4 space-y-3 cursor-pointer select-none" 
                                         @click="if (!$event.target.closest('button') && !$event.target.closest('a') && !$event.target.closest('input')) toggleExpand('{{ $session->id }}')">
                                        
                                        <!-- Top Row: Checkbox, Tanggal, Waktu & Status Badge -->
                                        <div class="flex items-start justify-between gap-2.5">
                                            <div class="flex items-center gap-2 min-w-0">
                                                @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                    @if($session->status !== 'completed' || $session->is_absent)
                                                        <input type="checkbox" 
                                                               value="{{ $session->id }}" 
                                                               x-model="selectedSessionIds" 
                                                               @change="notifySelection()"
                                                               class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500/30 transition-all cursor-pointer shrink-0"
                                                               @click.stop>
                                                    @endif
                                                @endif

                                                <div class="space-y-0.5 min-w-0">
                                                    <div class="flex items-center gap-1.5 font-black text-slate-800 dark:text-slate-100 text-xs">
                                                        <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        <span>{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</span>
                                                        <span class="text-slate-300 dark:text-slate-600">•</span>
                                                        <span class="text-slate-600 dark:text-slate-300 font-mono">{{ $session->time_range }}</span>
                                                    </div>
                                                    <div class="text-[11px]" @click.stop>
                                                        @if($session->type === 'online')
                                                            @if($session->location)
                                                                <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                    <span>{{ $isMeet ? 'Gabung Google Meet' : ($isZoom ? 'Gabung Zoom' : 'Buka Meeting') }}</span>
                                                                </a>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 font-bold text-blue-600 dark:text-blue-400">
                                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                    <span>Online</span>
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="inline-flex items-center gap-1 font-bold text-slate-600 dark:text-slate-300">
                                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                <span>Tatap Muka: {{ $session->location ?? 'Offline' }}</span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Status Sesi Badge -->
                                            <div class="shrink-0 text-right">
                                                @if($session->is_absent)
                                                    <x-status-badge type="red" label="TIDAK HADIR" />
                                                @else
                                                    @php
                                                        $mobileStatusLabel = match($session->status) {
                                                            'completed' => 'SELESAI',
                                                            'approved' => 'DISETUJUI',
                                                            'pending' => 'MENUNGGU',
                                                            'rejected' => 'DITOLAK',
                                                            default => strtoupper($session->status),
                                                        };
                                                    @endphp
                                                    <x-status-badge 
                                                        :type="$session->status === 'pending' ? 'amber' : ($session->status === 'approved' ? 'orange' : ($session->status === 'rejected' ? 'red' : ($session->status === 'completed' ? 'emerald' : 'slate')))" 
                                                        :label="$mobileStatusLabel" />
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Middle Row: Mahasiswa Avatar, Nama & Kehadiran Mhs -->
                                        <div class="flex items-center justify-between gap-2.5 pt-1">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0">
                                                    @if($student)
                                                        <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">M</span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="text-xs font-black text-slate-900 dark:text-slate-100 truncate">
                                                        {{ $student?->name ?? 'Mahasiswa' }}
                                                    </h4>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                                                        {{ $student?->identifier ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="shrink-0">
                                                @if($session->is_absent)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-bold border border-slate-200 dark:border-slate-600">
                                                        <span>{{ $session->student_attendance_status === 'attending' ? 'Konfirmasi: Hadir' : ($session->student_attendance_status === 'permission' ? 'Izin' : 'Tanpa Konfirmasi') }}</span>
                                                    </span>
                                                @elseif($session->status === 'completed')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-[11px] font-black uppercase border border-emerald-200 dark:border-emerald-800">
                                                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Hadir</span>
                                                    </span>
                                                @elseif($session->student_attendance_status === 'attending')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[11px] font-black uppercase border border-emerald-200 dark:border-emerald-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        <span>Akan Hadir</span>
                                                    </span>
                                                @elseif($session->student_attendance_status === 'permission')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[11px] font-black uppercase border border-amber-200 dark:border-amber-700" title="{{ $session->student_attendance_reason }}">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        <span>Izin</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-bold border border-slate-200 dark:border-slate-600">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                                                        <span>Belum Respon</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Topik Pembahasan & Bab Skripsi -->
                                        <div class="space-y-0.5 pt-0.5">
                                            <p class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm leading-snug">
                                                {{ $session->topic }}
                                            </p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 italic">
                                                {{ $thesis?->title ?? '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Bottom Action & Accordion Trigger Bar -->
                                    <div class="py-2.5 px-4 bg-slate-50/90 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5">
                                            @if($session->status === 'pending')
                                                @can('updateStatus', $session)
                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" 
                                                                class="px-2.5 py-1 bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            <span>Terima</span>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif

                                            @can('update', $session)
                                                <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-all" title="Ubah / Reschedule">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </a>
                                            @endcan

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
                                                        data-is-group="{{ $isGroup ? '1' : '0' }}"
                                                        data-group-count="{{ $groupCountMap[($session->dosen_id ?? '0') . '_' . $session->scheduled_at->format('Y-m-d H:i')] ?? 1 }}"
                                                        class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-all cursor-pointer"
                                                        title="{{ $session->status === 'pending' ? 'Tolak Pengajuan Jadwal' : 'Batalkan Sesi Bimbingan' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            @endcan
                                        </div>

                                        <!-- Accordion Toggle Button -->
                                        <button type="button" 
                                                @click="toggleExpand('{{ $session->id }}')" 
                                                class="px-3 py-1 rounded-xl border text-[11px] font-bold transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer ml-auto"
                                                :class="isExpanded('{{ $session->id }}') ? 'bg-orange-600 text-white border-orange-600' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-orange-300 hover:text-orange-600'">
                                            <span x-text="isExpanded('{{ $session->id }}') ? 'Tutup Detail' : 'Detail & Catatan'">Detail & Catatan</span>
                                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="isExpanded('{{ $session->id }}') ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                    </div>

                                    <!-- Expandable Accordion Body -->
                                    <div x-show="isExpanded('{{ $session->id }}')" 
                                         x-cloak 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 -translate-y-1"
                                         class="p-4 space-y-3.5 bg-slate-50/50 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-800 text-xs">
                                        
                                        <!-- 1. Rincian Skripsi & Dosen Pembimbing -->
                                        <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 space-y-2 shadow-2xs">
                                            <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700 pb-1.5">
                                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Info Akademik Skripsi</span>
                                                <span class="font-bold text-slate-600 dark:text-slate-300 font-mono text-[11px]">{{ $student?->identifier ?? '-' }}</span>
                                            </div>
                                            <p class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                                                <span class="text-slate-500 dark:text-slate-400 font-normal">Judul:</span> {{ $thesis?->title ?? '-' }}
                                            </p>
                                            <div class="grid grid-cols-2 gap-2 pt-1 text-[11px]">
                                                <div>
                                                    <span class="text-slate-500 dark:text-slate-400 block text-[11px]">Pembimbing 1:</span>
                                                    <strong class="text-slate-700 dark:text-slate-300">{{ $thesis?->pembimbing1?->name ?? '-' }}</strong>
                                                </div>
                                                <div>
                                                    <span class="text-slate-500 dark:text-slate-400 block text-[11px]">Pembimbing 2:</span>
                                                    <strong class="text-slate-700 dark:text-slate-300">{{ $thesis?->pembimbing2?->name ?? '-' }}</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 2. Catatan Pengajuan & Berkas Naskah Mahasiswa -->
                                        <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 space-y-2 shadow-2xs">
                                            <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700 pb-1.5">
                                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Catatan Mahasiswa</span>
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400">Pengajuan Bimbingan</span>
                                            </div>
                                            @if($session->notes)
                                                <p class="text-slate-700 dark:text-slate-300 italic leading-relaxed whitespace-pre-line text-xs">
                                                    "{{ $session->notes }}"
                                                </p>
                                            @else
                                                <p class="text-slate-500 dark:text-slate-400 italic text-xs">Tidak ada catatan pengajuan khusus dari mahasiswa.</p>
                                            @endif

                                            @if($session->document_path)
                                                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/80">
                                                    <span class="text-[11px] font-black uppercase tracking-wider text-orange-600 dark:text-orange-400 block mb-1">Dokumen Bimbingan:</span>
                                                    <a href="{{ $session->document_path }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/60 dark:hover:bg-orange-900/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800 rounded-xl font-bold text-xs transition-all shadow-2xs">
                                                        <x-google-drive-icon class="w-4 h-4 shrink-0" />
                                                        <span>Buka Dokumen Naskah Google Drive</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- 3. Catatan Hasil Bimbingan Dosen & Form Selesaikan Sesi -->
                                        <div class="p-3.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 space-y-2.5 shadow-2xs" x-data="{ editingFeedbackMob: false }">
                                            <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700 pb-1.5">
                                                <span class="text-[11px] font-black uppercase tracking-wider text-orange-600 dark:text-orange-400 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                                    <span>Catatan Logbook Pembimbing</span>
                                                </span>
                                                @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) && ($session->status === 'completed' || $session->feedback))
                                                    <button type="button" 
                                                            @click="editingFeedbackMob = !editingFeedbackMob" 
                                                            class="text-[11px] font-bold text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                                                        <span x-text="editingFeedbackMob ? 'Batal' : 'Ubah Catatan'">Ubah Catatan</span>
                                                    </button>
                                                @endif
                                            </div>

                                            @if($session->status === 'pending')
                                                <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed font-medium">
                                                    Pengajuan bimbingan ini masih berstatus <strong>Pending</strong>. Harap terima atau tolak jadwal terlebih dahulu.
                                                </p>
                                            @elseif($session->feedback && $session->status === 'completed')
                                                <div x-show="!editingFeedbackMob" class="space-y-2">
                                                    <p class="text-slate-800 dark:text-slate-200 font-medium leading-relaxed whitespace-pre-line text-xs bg-orange-50/40 dark:bg-orange-950/20 p-2.5 rounded-lg border border-orange-200/60 dark:border-orange-900/40">
                                                        {{ $session->feedback }}
                                                    </p>
                                                    @if($session->feedback_document_url)
                                                        <a href="{{ $session->feedback_document_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                            <span>Dokumen Koreksi / Feedback Dosen</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Form Input Feedback Langsung di Accordion -->
                                            @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                <div x-show="editingFeedbackMob || {{ ($session->status !== 'completed' && $session->status !== 'pending') ? 'true' : 'false' }}" class="space-y-2 pt-1">
                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="space-y-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="completed">
                                                        <div>
                                                            <textarea name="feedback" 
                                                                      rows="2" 
                                                                      required 
                                                                      placeholder="Ketik catatan hasil bimbingan & arahan revisi..." 
                                                                      class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 p-2 shadow-2xs">{{ $session->feedback }}</textarea>
                                                        </div>
                                                        <div>
                                                            <input type="url" 
                                                                   name="feedback_document_url" 
                                                                   value="{{ $session->feedback_document_url }}" 
                                                                   placeholder="Link Google Drive feedback (opsional)..." 
                                                                   class="w-full text-[11px] rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 px-2.5 py-1.5 shadow-2xs">
                                                        </div>
                                                        <div class="flex items-center justify-end gap-2 pt-1">
                                                            @if($session->status === 'completed')
                                                                <button type="button" @click="editingFeedbackMob = false" class="px-2.5 py-1 text-slate-500 dark:text-slate-400 text-[11px] font-bold cursor-pointer">Batal</button>
                                                                <button type="submit" class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer">Simpan Catatan</button>
                                                            @else
                                                                <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                    <span>Simpan & Selesaikan Sesi</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

            <!-- Mobile Cards View with Expandable Accordion (< 768px) -->
            <div class="block md:hidden p-4 space-y-3.5 bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-700">
                @forelse($sessions as $session)
                    @php
                        $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                        $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                        $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                    @endphp
                    <div id="mobile-session-{{ $session->id }}" 
                         x-data="{ expanded: false, uploading: false }"
                         x-show="matchesStudentFilter('{{ $session->status }}')"
                         class="rounded-2xl border border-slate-200/90 dark:border-slate-700/80 bg-white dark:bg-slate-800 shadow-2xs hover:shadow-xs transition-all overflow-hidden">
                        
                        <!-- Card Header & Summary (Tap anywhere to toggle accordion) -->
                        <div class="p-4 space-y-3 cursor-pointer select-none" @click="expanded = !expanded">
                            <!-- Top Row: Tanggal, Waktu & Status Badge -->
                            <div class="flex items-start justify-between gap-2.5">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-1.5 font-black text-slate-900 dark:text-slate-100 text-xs">
                                        <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</span>
                                        <span class="text-slate-300 dark:text-slate-600">•</span>
                                        <span class="text-slate-600 dark:text-slate-300 font-mono">{{ $session->scheduled_at->format('H:i') }} WIB</span>
                                        @if($session->scheduled_at->isToday())
                                            <span class="px-1.5 py-0.5 rounded bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300 text-[9px] font-bold uppercase tracking-wide">Hari Ini</span>
                                        @elseif($session->scheduled_at->isTomorrow())
                                            <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 text-[9px] font-bold uppercase tracking-wide">Besok</span>
                                        @endif
                                    </div>
                                    <!-- Media / Lokasi -->
                                    <div class="text-[11px]" @click.stop>
                                        @if($session->type === 'online')
                                            @if($session->location)
                                                <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                    <span>{{ $isMeet ? 'Gabung Google Meet' : ($isZoom ? 'Gabung Zoom' : 'Buka Meeting') }}</span>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1 font-bold text-blue-600 dark:text-blue-400">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                    <span>Online</span>
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1 font-bold text-slate-600 dark:text-slate-300">
                                                <svg class="w-3.5 h-3.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                <span>Tatap Muka: {{ $session->location ?? 'Offline' }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Sesi Badge -->
                                <div class="shrink-0 text-right">
                                    @if($session->is_absent)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                            Tidak Hadir
                                        </span>
                                    @else
                                        @php
                                            $mobileStudentStatusLabel = match($session->status) {
                                                'completed' => 'SELESAI',
                                                'approved' => 'DISETUJUI',
                                                'pending' => 'MENUNGGU',
                                                'rejected' => 'DITOLAK',
                                                default => strtoupper($session->status),
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider
                                            {{ $session->status === 'pending' ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : '' }}
                                            {{ $session->status === 'approved' ? 'bg-orange-600 text-white shadow-xs' : '' }}
                                            {{ $session->status === 'rejected' ? 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800' : '' }}
                                            {{ $session->status === 'completed' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : '' }}
                                        ">
                                            {{ $mobileStudentStatusLabel }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Middle Row: Dosen & Topik Pembahasan (Bab) -->
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0">
                                        @if($session->dosen)
                                            <img src="{{ $session->dosen->avatar_url }}" alt="{{ $session->dosen->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[10px] font-bold text-slate-500">D</span>
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">
                                        {{ $session->dosen->name ?? '-' }}
                                    </span>
                                    <span class="text-[9px] font-black px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase border border-slate-200 dark:border-slate-700">
                                        @if($session->dosen_id === $session->thesis?->pembimbing1_id)
                                            P1
                                        @elseif($session->dosen_id === $session->thesis?->pembimbing2_id)
                                            P2
                                        @else
                                            Dosen
                                        @endif
                                    </span>
                                </div>

                                <p class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm leading-snug">
                                    {{ $session->topic }}
                                </p>
                            </div>

                            <!-- Urgent Attendance Confirmation Banner (Khusus Belum Respon) -->
                            @if(in_array($session->status, ['pending', 'approved']) && $session->student_attendance_status === 'pending')
                                <div class="p-3 bg-amber-500/10 dark:bg-amber-500/15 rounded-xl border border-amber-500/30 space-y-2 mt-2" @click.stop>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-black text-amber-900 dark:text-amber-200 uppercase tracking-tight">Konfirmasi Kehadiran Anda:</span>
                                        <span class="text-[9px] font-black uppercase text-amber-600 dark:text-amber-400 animate-pulse bg-amber-100 dark:bg-amber-950/60 px-1.5 py-0.5 rounded">Wajib Konfirmasi</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <form action="{{ route('mentoring-sessions.confirm-attendance', $session->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="attending">
                                            <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-black uppercase tracking-wider transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Akan Hadir</span>
                                            </button>
                                        </form>
                                        <button type="button" 
                                                @click="openPermissionModal({{ json_encode($session) }})" 
                                                class="w-full py-2 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-black uppercase tracking-wider transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Izin</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Accordion Trigger Button -->
                        <div class="border-t border-slate-100 dark:border-slate-700/80">
                            <button type="button" 
                                    @click="expanded = !expanded" 
                                    class="w-full flex items-center justify-between py-2.5 px-4 bg-slate-50/90 hover:bg-slate-100 dark:bg-slate-800/60 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors cursor-pointer select-none">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span x-text="expanded ? 'Tutup Rincian' : 'Lihat Catatan & Berkas (Logbook)'"></span>
                                    @if($session->feedback)
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                                            ✓ Feedback Dosen
                                        </span>
                                    @endif
                                    @if($session->document_path)
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-orange-100 text-orange-800 dark:bg-orange-950/80 dark:text-orange-300">
                                            Berkas Naskah
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1 text-slate-400 dark:text-slate-500 shrink-0 ml-2">
                                    <svg class="w-4 h-4 transform transition-transform duration-200" :class="expanded ? 'rotate-180 text-orange-600 dark:text-orange-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                        </div>

                        <!-- Expandable Accordion Body -->
                        <div x-show="expanded" 
                             x-cloak 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="p-4 space-y-3.5 bg-slate-50/40 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-800">
                            
                            <!-- 1. Catatan Logbook & Arahan Dosen -->
                            @if($session->feedback || $session->feedback_document_url || $session->notes)
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 text-xs space-y-2 shadow-2xs">
                                    @if($session->feedback)
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-orange-600 dark:text-orange-400 block mb-0.5">
                                                Catatan Hasil Bimbingan Dosen:
                                            </span>
                                            <p class="text-slate-700 dark:text-slate-300 leading-relaxed font-medium whitespace-pre-line">
                                                {{ $session->feedback }}
                                            </p>
                                        </div>
                                    @endif

                                    @if($session->feedback_document_url)
                                        <div class="pt-1.5 border-t border-slate-100 dark:border-slate-700/80">
                                            <a href="{{ $session->feedback_document_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                <x-google-drive-icon class="w-3.5 h-3.5 shrink-0" />
                                                <span>Dokumen Koreksi / Feedback Dosen</span>
                                            </a>
                                        </div>
                                    @endif

                                    @if($session->notes)
                                        <div class="{{ ($session->feedback || $session->feedback_document_url) ? 'pt-2 border-t border-slate-100 dark:border-slate-700/80' : '' }}">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-0.5">
                                                Catatan Pengajuan Anda:
                                            </span>
                                            <p class="text-slate-600 dark:text-slate-400 italic text-[11px] whitespace-pre-line">
                                                "{{ $session->notes }}"
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 text-xs text-slate-400 italic">
                                    Belum ada catatan logbook tertulis untuk sesi ini.
                                </div>
                            @endif

                            <!-- 2. Dokumen Naskah Mahasiswa (Google Drive) -->
                            <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 space-y-2 text-xs shadow-2xs">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                        Dokumen Naskah Bimbingan
                                    </span>
                                    @if(in_array($session->status, ['pending', 'approved']))
                                        <div>
                                            @if($session->document_path)
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button" @click="uploading = !uploading" class="text-[10px] font-bold text-orange-600 dark:text-orange-400 hover:underline">Ganti</button>
                                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                                    <form action="{{ route('mentoring-sessions.delete-document', $session->id) }}"
                                                          method="POST"
                                                          class="inline"
                                                          data-confirm="Yakin ingin menghapus dokumen ini?"
                                                          data-confirm-title="Hapus Dokumen Bimbingan"
                                                          data-confirm-type="danger"
                                                          data-confirm-button="Ya, Hapus">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-[10px] font-bold text-rose-600 dark:text-rose-400 hover:underline cursor-pointer">Hapus</button>
                                                    </form>
                                                </div>
                                            @else
                                                <button type="button" @click="uploading = !uploading" class="text-[10px] font-bold text-orange-600 dark:text-orange-400 hover:underline">+ Lampirkan</button>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                @if(in_array($session->status, ['pending', 'approved']))
                                    <div x-show="uploading" x-cloak x-transition class="pt-2 border-t border-slate-100 dark:border-slate-700">
                                        <form action="{{ route('mentoring-sessions.upload-document', $session->id) }}" method="POST" class="space-y-1.5">
                                            @csrf
                                            <input type="url" name="document" placeholder="https://drive.google.com/..." required
                                                   class="w-full text-xs text-slate-800 dark:text-slate-200 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 py-1.5 px-2.5">
                                            <p class="text-[10px] text-slate-400">Pastikan link Google Drive sudah diatur publik / dapat dilihat.</p>
                                            <div class="flex items-center gap-1.5">
                                                <button type="submit" class="px-2.5 py-1 bg-orange-600 text-white text-[10px] font-bold rounded-md">Simpan</button>
                                                <button type="button" @click="uploading = false" class="px-2.5 py-1 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold rounded-md">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if($session->document_path)
                                    <a href="{{ $session->document_path }}" target="_blank" class="flex items-center gap-1.5 text-orange-700 dark:text-orange-400 font-bold hover:underline">
                                        <x-google-drive-icon class="w-3.5 h-3.5 shrink-0" />
                                        <span class="truncate">{{ $session->document_original_name ?: 'Buka Berkas Google Drive' }}</span>
                                    </a>
                                @else
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500 italic">Belum melampirkan berkas naskah.</p>
                                @endif
                            </div>

                            <!-- 3. Status Kehadiran & Opsi Ubah (Jika Bukan Status Pending Tanpa Konfirmasi) -->
                            @if($session->status === 'completed')
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between text-xs">
                                    <span class="text-slate-400 dark:text-slate-500 font-medium">Status Kehadiran:</span>
                                    @if($session->is_absent)
                                        <span class="text-rose-600 dark:text-rose-400 font-bold">Tidak Hadir (Absen)</span>
                                    @else
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">✓ Telah Hadir</span>
                                    @endif
                                </div>
                            @elseif($session->status === 'rejected')
                                <div class="p-3 bg-rose-50 dark:bg-rose-950/30 rounded-xl border border-rose-200 dark:border-rose-900/60 text-xs text-rose-700 dark:text-rose-300 italic">
                                    Sesi bimbingan ditolak oleh dosen pembimbing.
                                </div>
                            @else
                                @if($session->student_attendance_status === 'attending')
                                    <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 font-bold">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Dikonfirmasi Siap Hadir</span>
                                        </div>
                                        <button type="button" 
                                                @click="openPermissionModal({{ json_encode($session) }})" 
                                                class="text-[10px] font-bold text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 underline decoration-dotted transition-colors cursor-pointer">
                                            Ubah ke Izin
                                        </button>
                                    </div>
                                @elseif($session->student_attendance_status === 'permission')
                                    <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 text-xs space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-amber-800 dark:text-amber-400 font-bold">Status: Izin / Berhalangan</span>
                                            <form action="{{ route('mentoring-sessions.confirm-attendance', $session->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="attending">
                                                <button type="submit" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-700 underline decoration-dotted cursor-pointer">
                                                    Ubah Siap Hadir
                                                </button>
                                            </form>
                                        </div>
                                        @if($session->student_attendance_reason)
                                            <p class="text-[11px] text-slate-600 dark:text-slate-400 italic">"{{ $session->student_attendance_reason }}"</p>
                                        @endif
                                    </div>
                                @endif

                                <!-- 4. Batalkan Pengajuan -->
                                @if(in_array($session->status, ['pending', 'approved']))
                                    <div class="flex justify-end pt-1">
                                        <button type="button" 
                                                @click="openCancelModalFromEl($el)"
                                                data-session-id="{{ $session->id }}"
                                                data-topic="{{ $session->topic }}"
                                                data-dosen-name="{{ $session->dosen?->name ?? 'Dosen Pembimbing' }}"
                                                data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                class="text-[10px] font-bold text-rose-600 dark:text-rose-400 hover:underline flex items-center gap-1 cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            <span>Batalkan Pengajuan Jadwal</span>
                                        </button>
                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-500 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="font-medium text-sm text-slate-600 dark:text-slate-400">Belum ada riwayat bimbingan.</p>
                    </div>
                @endforelse
            </div>

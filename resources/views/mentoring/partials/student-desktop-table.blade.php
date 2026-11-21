            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">TANGGAL & WAKTU</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">TOPIK PEMBAHASAN</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">DOSEN PEMBIMBING</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">DOKUMEN</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">KONFIRMASI KEHADIRAN</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">CATATAN (LOGBOOK)</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($sessions as $session)
                            <tr id="session-{{ $session->id }}" data-session-id="{{ $session->id }}"
                                x-show="matchesStudentFilter('{{ $session->status }}')"
                                class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-all duration-500 group align-top">
                                <td class="py-4 px-5 text-slate-800 dark:text-slate-200 font-medium whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span>{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</span>
                                        @if($session->scheduled_at->isToday())
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 text-[11px] font-black uppercase">Hari Ini</span>
                                        @elseif($session->scheduled_at->isTomorrow())
                                            <span class="px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 text-[11px] font-black uppercase">Besok</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-normal tabular-nums">{{ $session->scheduled_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="py-4 px-5 text-slate-700 dark:text-slate-300">
                                    <div class="font-medium text-slate-800 dark:text-slate-100 leading-snug">{{ $session->topic }}</div>
                                    <div class="mt-1.5 flex items-center text-[11px]">
                                        @if($session->type === 'online')
                                            @php 
                                                $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                                                $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                                                $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                                            @endphp
                                            @if($session->location)
                                                <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $isMeet ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/60' : ($isZoom ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/60' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100') }} transition-all shadow-2xs font-bold text-[11px] uppercase tracking-wider">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                    <span>{{ $isMeet ? 'Gabung Google Meet' : ($isZoom ? 'Gabung Zoom' : 'Buka Meeting') }}</span>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-semibold border border-blue-100 dark:border-blue-500/20">Online</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold mr-2 border border-slate-200 dark:border-slate-700">Offline</span>
                                            @if($session->location)
                                                <span class="text-slate-500 truncate max-w-[130px] inline-block" title="{{ $session->location }}">{{ $session->location }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-600 shadow-sm bg-slate-50 dark:bg-slate-800">
                                            @if($session->dosen)
                                                <img src="{{ $session->dosen->avatar_url }}" alt="{{ $session->dosen->name }}" class="w-full h-full object-cover">
                                            @else
                                                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><rect width='100%25' height='100%25' fill='%23f1f5f9'/><path d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z' fill='%2394a3b8'/></svg>" alt="Foto default dosen" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-xs text-slate-800 dark:text-slate-200 truncate max-w-[130px]">{{ $session->dosen->name ?? '-' }}</div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-tighter">
                                                @if($session->dosen_id === $session->thesis->pembimbing1_id)
                                                    Pembimbing 1
                                                @elseif($session->dosen_id === $session->thesis->pembimbing2_id)
                                                    Pembimbing 2
                                                @else
                                                    Dosen
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- === DOKUMEN COLUMN === --}}
                                <td class="py-4 px-5 min-w-[190px]">
                                    @if(in_array($session->status, ['pending', 'approved']))
                                        <div x-data="{ uploading: false }">
                                            @if($session->document_path)
                                                {{-- Dokumen sudah ada --}}
                                                <div class="flex items-center gap-2 mb-2 p-2 bg-orange-50 dark:bg-orange-500/10 border border-orange-100 dark:border-orange-500/20 rounded-md">
                                                    <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                    <a href="{{ $session->document_path }}" target="_blank" class="text-xs text-orange-700 dark:text-orange-400 font-semibold hover:underline truncate max-w-[110px]" title="{{ $session->document_original_name }}">
                                                        {{ $session->document_original_name }}
                                                    </a>
                                                </div>
                                                <div class="flex gap-1">
                                                    {{-- Ganti dokumen --}}
                                                    <button type="button" @click="uploading = !uploading" class="flex items-center gap-1 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                        Ganti
                                                    </button>
                                                    {{-- Hapus dokumen --}}
                                                    <form action="{{ route('mentoring-sessions.delete-document', $session->id) }}"
                                                          method="POST"
                                                          data-confirm="Yakin ingin menghapus dokumen ini?"
                                                          data-confirm-title="Hapus Dokumen Bimbingan"
                                                          data-confirm-type="danger"
                                                          data-confirm-button="Ya, Hapus">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex items-center gap-1 px-2 py-1 text-[11px] font-semibold text-red-600 dark:text-red-400 bg-white dark:bg-slate-700 border border-red-200 dark:border-red-900 rounded hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors relative z-10 cursor-pointer">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                {{-- Belum ada dokumen --}}
                                                <button type="button" @click="uploading = !uploading" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-orange-700 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-500/20 transition-colors whitespace-nowrap cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                    <span>Lampirkan</span>
                                                </button>
                                            @endif

                                            {{-- Upload form (collapsible) --}}
                                            <div x-show="uploading" x-cloak x-transition class="mt-2">
                                                <form action="{{ route('mentoring-sessions.upload-document', $session->id) }}" method="POST" class="flex flex-col gap-1.5">
                                                    @csrf
                                                    <label class="block">
                                                        <input type="url" name="document" placeholder="https://drive.google.com/..." required
                                                            class="block w-full text-xs text-slate-600 dark:text-slate-400 rounded border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900 shadow-sm py-1.5 px-2">
                                                    </label>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Masukkan Dokumen dari Google Drive.</p>
                                                    <div class="flex gap-1">
                                                        <button type="submit" class="px-2.5 py-1 bg-orange-600 text-white text-[11px] font-bold rounded hover:bg-orange-700 transition-colors">Simpan</button>
                                                        <button type="button" @click="uploading = false" class="px-2.5 py-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 text-[11px] font-semibold rounded hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($session->document_path)
                                        {{-- Sesi selesai/ditolak tapi ada dokumen: tampilkan read-only --}}
                                        <a href="{{ $session->document_path }}" target="_blank" class="flex items-center gap-1.5 text-xs text-slate-600 hover:text-orange-700 transition-colors">
                                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            <span class="truncate max-w-[130px]">{{ $session->document_original_name }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-300 text-xs italic">—</span>
                                    @endif
                                </td>
                                {{-- === END DOKUMEN COLUMN === --}}

                                {{-- === KONFIRMASI KEHADIRAN MAHASISWA COLUMN === --}}
                                <td class="py-4 px-5 min-w-[200px]">
                                    @if($session->status === 'completed')
                                        @if($session->is_absent)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60">
                                                Tidak Hadir (Absen)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                Telah Hadir
                                            </span>
                                        @endif
                                    @elseif($session->status === 'rejected')
                                        <span class="text-slate-500 dark:text-slate-400 text-xs italic">-</span>
                                    @else
                                        @if($session->student_attendance_status === 'pending')
                                            <div class="space-y-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700/80 text-[11px] font-black uppercase tracking-wider animate-pulse">
                                                    Belum Konfirmasi
                                                </span>
                                                <div class="flex items-center gap-1.5">
                                                    <form action="{{ route('mentoring-sessions.confirm-attendance', $session->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="attending">
                                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-xs flex items-center gap-1 cursor-pointer" title="Konfirmasi Akan Hadir">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            <span>Akan Hadir</span>
                                                        </button>
                                                    </form>
                                                    <button type="button" 
                                                            @click="openPermissionModal({{ json_encode($session) }})" 
                                                            class="px-2.5 py-1 bg-slate-100 hover:bg-rose-50 dark:bg-slate-800 dark:hover:bg-rose-950/40 text-slate-700 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200 dark:border-slate-700 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-xs flex items-center gap-1 cursor-pointer" title="Ajukan Izin dengan Alasan">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <span>Izin</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @elseif($session->student_attendance_status === 'attending')
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700/80 text-[11px] font-black uppercase tracking-wider">
                                                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Akan Hadir</span>
                                                    </span>
                                                </div>
                                                @if($session->student_confirmed_at)
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Dikonfirmasi: {{ $session->student_confirmed_at->format('d/m H:i') }} WIB</p>
                                                @endif
                                                <button type="button" 
                                                        @click="openPermissionModal({{ json_encode($session) }})" 
                                                        class="text-[11px] font-bold text-slate-400 hover:text-rose-600 dark:text-slate-500 dark:hover:text-rose-400 underline decoration-dotted transition-colors cursor-pointer">
                                                    Ubah ke Izin
                                                </button>
                                            </div>
                                        @elseif($session->student_attendance_status === 'permission')
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700/80 text-[11px] font-black uppercase tracking-wider">
                                                        <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"></path></svg>
                                                        <span>Izin / Berhalangan</span>
                                                    </span>
                                                </div>
                                                @if($session->student_attendance_reason)
                                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 italic line-clamp-2" title="{{ $session->student_attendance_reason }}">
                                                        "{{ $session->student_attendance_reason }}"
                                                    </p>
                                                @endif
                                                <form action="{{ route('mentoring-sessions.confirm-attendance', $session->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <input type="hidden" name="status" value="attending">
                                                    <button type="submit" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 underline decoration-dotted transition-colors cursor-pointer">
                                                        Ubah Siap Hadir
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                <td class="py-4 px-5 max-w-sm">
                                    @if($session->feedback)
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-0.5">Catatan Pembimbing:</div>
                                        <div class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed">{{ $session->feedback }}</div>
                                    @elseif($session->notes)
                                        <div class="text-[11px] uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400 mb-0.5">Catatan Pengajuan:</div>
                                        <div class="text-slate-600 dark:text-slate-400 text-xs italic leading-relaxed">{{ $session->notes }}</div>
                                    @else
                                        @if(!$session->feedback_document_url)
                                            <span class="text-slate-400 dark:text-slate-600">-</span>
                                        @endif
                                    @endif

                                    @if($session->feedback_document_url)
                                        <div class="mt-2 pt-1.5 border-t border-slate-100 dark:border-slate-800">
                                            <a href="{{ $session->feedback_document_url }}" target="_blank" rel="noopener noreferrer" 
                                               class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/80 hover:bg-blue-100 dark:hover:bg-blue-900/60 transition-colors shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                <span>Dokumen Feedback / Koreksi Dosen</span>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    @if($session->is_absent)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                            Tidak Hadir
                                        </span>
                                    @else
                                        @php
                                            $studentStatusLabel = match($session->status) {
                                                'completed' => 'SELESAI',
                                                'approved' => 'DISETUJUI',
                                                'pending' => 'MENUNGGU',
                                                'rejected' => 'DITOLAK',
                                                default => strtoupper($session->status),
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider
                                            {{ $session->status === 'pending' ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : '' }}
                                            {{ $session->status === 'approved' ? 'bg-orange-600 text-white' : '' }}
                                            {{ $session->status === 'rejected' ? 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800' : '' }}
                                            {{ $session->status === 'completed' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900' : '' }}
                                        ">
                                            {{ $studentStatusLabel }}
                                        </span>

                                        @if(in_array($session->status, ['pending', 'approved']))
                                            <div class="mt-1.5">
                                                <button type="button" 
                                                        @click="openCancelModalFromEl($el)"
                                                        data-session-id="{{ $session->id }}"
                                                        data-topic="{{ $session->topic }}"
                                                        data-dosen-name="{{ $session->dosen?->name ?? 'Dosen Pembimbing' }}"
                                                        data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                        data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                        class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 hover:underline cursor-pointer flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    <span>Batalkan</span>
                                                </button>
                                            </div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500 border-b border-slate-200 dark:border-slate-700">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="font-medium text-sm text-slate-600 dark:text-slate-400">Belum ada riwayat bimbingan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

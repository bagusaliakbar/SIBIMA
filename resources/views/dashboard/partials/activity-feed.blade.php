        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Left Column: Main Feed -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Upcoming Sessions Table -->
                <x-table-card 
                    title="Jadwal Bimbingan Terdekat"
                    subtitle="Pantau jadwal yang telah disetujui atau menunggu konfirmasi.">
                    
                    <x-slot name="headerActions">
                        <a href="{{ route('mentoring-sessions.index') }}" class="text-xs font-bold text-orange-600 hover:underline flex items-center">
                            Lihat Semua
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </x-slot>

                    {{-- Desktop Table View --}}
                    <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-50 dark:border-slate-700">
                                <th class="px-6 py-3">Topik & Mahasiswa</th>
                                <th class="px-6 py-3">Waktu Pelaksanaan</th>
                                <th class="px-6 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                            @forelse(isset($upcomingSessions) ? $upcomingSessions : [] as $session)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors cursor-pointer group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-600 flex items-center justify-center mr-3 font-bold text-xs">
                                            {{ substr($session->topic, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-700 dark:text-slate-200 text-xs">{{ $session->topic }}</h4>
                                            @if(Auth::user()->role !== 'mahasiswa')
                                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $session->thesis->student->name }}</p>
                                            @else
                                                <p class="text-[10px] text-slate-400 mt-0.5">Dosen: {{ $session->dosen->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-600">{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $session->scheduled_at->format('H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end gap-1">
                                        <x-status-badge 
                                            :type="$session->status === 'approved' ? 'emerald' : 'orange'" 
                                            :label="$session->status === 'approved' ? 'Disetujui' : 'Menunggu'" />

                                        @if($session->status === 'approved')
                                            @if($session->student_attendance_status === 'attending')
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-tight">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Akan Hadir
                                                </span>
                                            @elseif($session->student_attendance_status === 'permission')
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-tight" title="{{ $session->student_attendance_reason }}">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Izin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-tight">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                    Perlu Konfirmasi
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <x-empty-state colspan="3" description="Belum ada jadwal bimbingan terdekat." icon="clock" />
                            @endforelse
                        </tbody>
                    </table>
                    </div>

                    {{-- Mobile Card View --}}
                    <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700/80">
                        @forelse(isset($upcomingSessions) ? $upcomingSessions : [] as $session)
                            <div class="p-4 space-y-2.5 bg-white dark:bg-slate-800/60">
                                <div class="flex items-start justify-between gap-2.5">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ substr($session->topic, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-slate-800 dark:text-slate-100 text-xs truncate">{{ $session->topic }}</h4>
                                            @if(Auth::user()->role !== 'mahasiswa')
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $session->thesis->student->name }}</p>
                                            @else
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Dosen: {{ $session->dosen->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <x-status-badge
                                        :type="$session->status === 'approved' ? 'emerald' : 'orange'"
                                        :label="$session->status === 'approved' ? 'Disetujui' : 'Menunggu'" />
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-1">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }} &bull; {{ $session->scheduled_at->format('H:i') }} WIB</span>
                                    @if($session->status === 'approved')
                                        @if($session->student_attendance_status === 'attending')
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">Akan Hadir</span>
                                        @elseif($session->student_attendance_status === 'permission')
                                            <span class="font-bold text-amber-600 dark:text-amber-400">Izin</span>
                                        @else
                                            <span class="font-bold text-slate-500 dark:text-slate-400">Perlu Konfirmasi</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-10 px-4 text-center text-xs text-slate-500 dark:text-slate-400">
                                Belum ada jadwal bimbingan terdekat.
                            </div>
                        @endforelse
                    </div>
                </x-table-card>

                <!-- Recent Activity / Logbook -->
                @if(Auth::user()->role !== 'mahasiswa')
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden transition-all duration-300">
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Logbook Terbaru</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative border-l border-slate-100 dark:border-slate-700 ml-3 space-y-8">
                            @forelse(isset($recentLogbooks) ? $recentLogbooks : [] as $logbook)
                            <div class="relative pl-7 group">
                                <div class="absolute -left-[5px] top-1.5 w-2.5 h-2.5 rounded-full bg-orange-500 ring-4 ring-orange-50 dark:ring-slate-900 transition-all group-hover:scale-125"></div>
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                                    <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $logbook->topic }}</h4>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $logbook->scheduled_at->locale('id')->translatedFormat('d M Y • H:i') }} WIB</span>
                                </div>
                                
                                <div class="space-y-2.5 text-[11px]">
                                    <!-- Logbook Details Box -->
                                    <div class="bg-slate-50/50 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-800/80 rounded-xl p-3 space-y-2">
                                        @if($logbook->notes)
                                            <div class="flex items-start gap-1.5">
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider select-none shrink-0 w-16">Catatan:</span>
                                                <p class="text-slate-600 dark:text-slate-300 font-medium leading-relaxed italic">"{{ $logbook->notes }}"</p>
                                            </div>
                                        @endif

                                        @if($logbook->feedback)
                                            <div class="flex items-start gap-1.5 pt-2 {{ $logbook->notes ? 'border-t border-slate-100 dark:border-slate-800/50' : '' }}">
                                                <span class="text-[9px] font-black text-orange-500 uppercase tracking-wider select-none shrink-0 w-16">Feedback:</span>
                                                <div class="space-y-1">
                                                    <p class="text-slate-800 dark:text-slate-200 font-bold leading-relaxed">{{ $logbook->feedback }}</p>
                                                    @if($logbook->feedback_document_url)
                                                        <a href="{{ $logbook->feedback_document_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                            <span>Dokumen Koreksi</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Metadata footer -->
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-tight">
                                        @if(Auth::user()->role === 'mahasiswa')
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                                <span>Dosen: <span class="text-slate-600 dark:text-slate-400 font-black">{{ $logbook->dosen->name ?? '-' }}</span> ({{ $logbook->dosen_id === $logbook->thesis->pembimbing1_id ? 'Pembimbing 1' : ($logbook->dosen_id === $logbook->thesis->pembimbing2_id ? 'Pembimbing 2' : 'Dosen') }})</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                                <span>Mahasiswa: <span class="text-slate-600 dark:text-slate-400 font-black">{{ $logbook->thesis->student->name }}</span></span>
                                            </div>
                                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                                                <div class="flex items-center gap-1.5 border-l border-slate-200 dark:border-slate-700 pl-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                                    <span>Dosen: <span class="text-slate-600 dark:text-slate-400 font-black">{{ $logbook->dosen->name ?? '-' }}</span> ({{ $logbook->dosen_id === $logbook->thesis->pembimbing1_id ? 'Pembimbing 1' : ($logbook->dosen_id === $logbook->thesis->pembimbing2_id ? 'Pembimbing 2' : 'Dosen') }})</span>
                                                </div>
                                            @endif
                                        @endif
                                        
                                        @if($logbook->document_path)
                                            <div class="flex items-center gap-1.5 border-l border-slate-200 dark:border-slate-700 pl-4">
                                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                <a href="{{ route('download.private', ['path' => $logbook->document_path]) }}" target="_blank" class="text-orange-600 hover:text-orange-700 transition-colors font-black hover:underline" title="{{ $logbook->document_original_name }}">Unduh Berkas</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-100 dark:border-slate-800">
                                    <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Belum ada catatan logbook terbaru.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @if(method_exists($recentLogbooks, 'links') && $recentLogbooks->hasPages())
                        <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/30 border-t border-slate-100 dark:border-slate-700/50">
                            {{ $recentLogbooks->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-6">
                <!-- Live Online Users Card (Admin & Kaprodi & Dosen) -->
                @if(Auth::user()->role !== 'mahasiswa')
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden transition-all duration-300">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/50 bg-gradient-to-r from-emerald-500/10 via-slate-50 to-emerald-500/5 dark:from-emerald-950/20 dark:to-slate-900 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse shadow-xs"></span>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">Pengguna Online</h3>
                        </div>
                        <span id="online-users-count-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50">
                            {{ $onlineUsersCount }} Aktif
                        </span>
                    </div>

                    <!-- Role Breakdown Pills -->
                    <div class="px-5 py-2.5 bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] gap-1 font-bold">
                        <span class="text-blue-600 dark:text-blue-400">Mhs: <strong id="online-count-mhs">{{ $onlineUsersByRole['mahasiswa'] ?? 0 }}</strong></span>
                        <span class="text-indigo-600 dark:text-indigo-400">Dosen: <strong id="online-count-dosen">{{ $onlineUsersByRole['dosen'] ?? 0 }}</strong></span>
                        <span class="text-orange-600 dark:text-orange-400">Admin/Kaprodi: <strong id="online-count-admin">{{ ($onlineUsersByRole['admin'] ?? 0) + ($onlineUsersByRole['kaprodi'] ?? 0) }}</strong></span>
                    </div>

                    <!-- Online Users List -->
                    <div class="p-3 max-h-72 overflow-y-auto space-y-2" id="online-users-list">
                        @forelse($onlineUsers as $u)
                        <div class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors group">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="relative w-10 h-10 shrink-0">
                                    <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-xs bg-slate-100 dark:bg-slate-800">
                                        <img src="{{ $u->avatar_url }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full shadow-xs z-10" title="Online"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate group-hover:text-orange-600 transition-colors">{{ $u->name }}</p>
                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ $u->role }}</span>
                                </div>
                            </div>
                            @if($u->id !== Auth::id())
                            <a href="{{ route('chat.show', $u->id) }}" class="p-1.5 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 hover:bg-orange-500 hover:text-white transition-all shrink-0" title="Kirim Pesan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </a>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <p class="text-xs text-slate-400 font-medium italic">Tidak ada pengguna aktif lain saat ini.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endif

                <!-- Announcements Card -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden transition-all duration-300">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/70 dark:bg-slate-900/50 flex justify-between items-center">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">Papan Informasi</h3>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-orange-100/80 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300 border border-orange-200/80 dark:border-orange-800/80">
                            Terbaru
                        </span>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @forelse($announcements as $announcement)
                        <div class="p-5 hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">
                            <div class="flex items-center gap-2 mb-2">
                                @php
                                    $color = $announcement->type === 'important' ? 'red' : ($announcement->type === 'warning' ? 'orange' : 'blue');
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[8px] font-extrabold uppercase tracking-wider border {{ $color === 'red' ? 'bg-red-100 text-red-700 border-red-200 dark:bg-red-950/80 dark:text-red-300 dark:border-red-800/80' : ($color === 'orange' ? 'bg-orange-100 text-orange-700 border-orange-200 dark:bg-orange-950/80 dark:text-orange-300 dark:border-orange-800/80' : 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-950/80 dark:text-blue-300 dark:border-blue-800/80') }}">
                                    {{ $announcement->type }}
                                </span>
                                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ $announcement->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-tight mb-1">{{ $announcement->title }}</p>
                            <div class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $announcement->content }}</div>
                        </div>
                        @empty
                        <div class="p-10 text-center">
                            <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-100 dark:border-slate-800">
                                <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </div>
                            <p class="text-xs text-slate-400 font-medium italic">Belum ada pengumuman.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

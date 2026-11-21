        <!-- ========================================== -->
        <!-- TAB 1: MAHASISWA ACTIVITY                 -->
        <!-- ========================================== -->
        <div x-show="activeMainTab === 'mahasiswa'" class="space-y-6">
            
            <!-- SUB-TAB TOGGLE: TERAJIN VS PASIF -->
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button type="button" 
                            @click="studentSubTab = 'terajin'"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer"
                            :class="studentSubTab === 'terajin' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100'">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span>Paling Rajin (Leaderboard Top)</span>
                    </button>
                    <button type="button" 
                            @click="studentSubTab = 'pasif'"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer"
                            :class="studentSubTab === 'pasif' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100'">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Pasif & Kritis (Perlu Perhatian)</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[11px] font-black"
                              :class="studentSubTab === 'pasif' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300'">
                            {{ count($inactive_students) }}
                        </span>
                    </button>
                </div>

                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan data berdasarkan periode: <strong class="text-slate-800 dark:text-white">{{ $period_label }}</strong>
                </span>
            </div>

            <!-- SUB-TAB CONTENT A: MAHASISWA TERAJIN -->
            <div x-show="studentSubTab === 'terajin'" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
                @if(count($top_students) === 0)
                    <div class="p-12 text-center space-y-3">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada riwayat bimbingan mahasiswa pada periode ini.</p>
                        <p class="text-xs text-slate-500">Pilih opsi periode lain (seperti "Semua Waktu" atau "Semester Berjalan") untuk melihat riwayat lengkap.</p>
                    </div>
                @else
                    <!-- Mobile Cards -->
                    <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($top_students as $idx => $s)
                            <div class="p-4 space-y-2.5 {{ $idx < 3 ? 'bg-amber-50/20 dark:bg-amber-950/10' : '' }}">
                                <div class="flex items-start justify-between gap-2.5">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        @if($idx === 0)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-400 text-amber-950 font-black text-xs shrink-0">#1</span>
                                        @elseif($idx === 1)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-slate-200 dark:bg-slate-600 text-slate-800 dark:text-white font-black text-xs shrink-0">#2</span>
                                        @elseif($idx === 2)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-700 text-white font-black text-xs shrink-0">#3</span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs shrink-0">{{ $idx + 1 }}</span>
                                        @endif
                                        <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white dark:bg-slate-800">
                                            <img src="{{ $s['avatar_url'] }}" alt="{{ $s['name'] }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $s['name'] }}</p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono truncate">{{ $s['identifier'] ?: '-' }} • Angkatan {{ $s['entry_year'] ?: '-' }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shrink-0">
                                        {{ $s['sessions_in_period'] }} Sesi
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-600 dark:text-slate-300">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 font-semibold">{{ $s['p1_sessions'] }} P1 / {{ $s['p2_sessions'] }} P2</span>
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 font-bold">{{ $s['total_all_time'] }}x Total</span>
                                    </div>
                                    @php
                                        $healthBadgeClass = match($s['health_color'] ?? 'slate') {
                                            'emerald', 'green' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                                            'blue', 'sky' => 'bg-blue-50 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800/60',
                                            'amber', 'yellow', 'orange' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                                            'rose', 'red' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                                            default => 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider border {{ $healthBadgeClass }}">
                                        {{ $s['health_status'] }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 space-y-0.5">
                                    <p class="truncate">P1: {{ $s['pembimbing1_name'] ?: '-' }} | P2: {{ $s['pembimbing2_name'] ?: '-' }}</p>
                                    <p>Terakhir: {{ $s['last_session_at'] ? \Carbon\Carbon::parse($s['last_session_at'])->diffForHumans() . ' (' . \Carbon\Carbon::parse($s['last_session_at'])->format('d M Y') . ')' : '-' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-900/50 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="py-4 px-4 text-center w-14">Rank</th>
                                    <th class="py-4 px-5">Mahasiswa</th>
                                    <th class="py-4 px-4 text-center">Sesi Periode Ini</th>
                                    <th class="py-4 px-4 text-center">P1 / P2</th>
                                    <th class="py-4 px-4 text-center">Total Akumulasi</th>
                                    <th class="py-4 px-5">Dosen Pembimbing</th>
                                    <th class="py-4 px-4">Bimbingan Terakhir</th>
                                    <th class="py-4 px-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs text-slate-700 dark:text-slate-300">
                                @foreach($top_students as $idx => $s)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors {{ $idx < 3 ? 'bg-amber-50/20 dark:bg-amber-950/10' : '' }}">
                                        <!-- Rank -->
                                        <td class="py-4 px-4 text-center">
                                            @if($idx === 0)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-400 text-amber-950 font-black text-xs shadow-xs" title="Juara 1 Teraktif">#1</span>
                                            @elseif($idx === 1)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-slate-200 dark:bg-slate-600 text-slate-800 dark:text-white font-black text-xs shadow-xs" title="Juara 2 Teraktif">#2</span>
                                            @elseif($idx === 2)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-700 text-white font-black text-xs shadow-xs" title="Juara 3 Teraktif">#3</span>
                                            @else
                                                <span class="font-bold text-slate-500 text-xs">{{ $idx + 1 }}</span>
                                            @endif
                                        </td>

                                        <!-- Profile -->
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white dark:bg-slate-800">
                                                    <img src="{{ $s['avatar_url'] }}" alt="{{ $s['name'] }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-900 dark:text-white block truncate max-w-[200px] sm:max-w-xs" title="{{ $s['name'] }}">
                                                        {{ $s['name'] }}
                                                    </span>
                                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                                                        <span>{{ $s['identifier'] ?: '-' }}</span>
                                                        <span>•</span>
                                                        <span>Angkatan {{ $s['entry_year'] ?: '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Sesi Periode Ini -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span>{{ $s['sessions_in_period'] }} Sesi</span>
                                            </span>
                                        </td>

                                        <!-- Breakdown P1 / P2 -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 font-semibold text-slate-700 dark:text-slate-300 text-[11px]">
                                                {{ $s['p1_sessions'] }} P1 / {{ $s['p2_sessions'] }} P2
                                            </span>
                                        </td>

                                        <!-- Total Akumulasi -->
                                        <td class="py-4 px-4 text-center font-bold text-slate-800 dark:text-slate-200">
                                            {{ $s['total_all_time'] }}x
                                        </td>

                                        <!-- Pembimbing -->
                                        <td class="py-4 px-5">
                                            <div class="text-[11px] space-y-0.5 max-w-xs">
                                                <p class="truncate" title="Pembimbing 1: {{ $s['pembimbing1_name'] }}">
                                                    <span class="font-bold text-slate-500">P1:</span> {{ $s['pembimbing1_name'] ?: '-' }}
                                                </p>
                                                <p class="truncate text-slate-500" title="Pembimbing 2: {{ $s['pembimbing2_name'] }}">
                                                    <span class="font-bold text-slate-500">P2:</span> {{ $s['pembimbing2_name'] ?: '-' }}
                                                </p>
                                            </div>
                                        </td>

                                        <!-- Bimbingan Terakhir -->
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @if($s['last_session_at'])
                                                <span class="font-semibold text-slate-800 dark:text-slate-200 block text-[11px]">
                                                    {{ \Carbon\Carbon::parse($s['last_session_at'])->diffForHumans() }}
                                                </span>
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                                    {{ \Carbon\Carbon::parse($s['last_session_at'])->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="text-slate-500 dark:text-slate-400 italic text-[11px]">-</span>
                                            @endif
                                        </td>

                                        <!-- Status Keaktifan -->
                                        <td class="py-4 px-4 text-center">
                                            @php
                                        $healthBadgeClass = match($s['health_color'] ?? 'slate') {
                                            'emerald', 'green' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                                            'blue', 'sky' => 'bg-blue-50 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800/60',
                                            'amber', 'yellow', 'orange' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                                            'rose', 'red' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                                            default => 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider border {{ $healthBadgeClass }}">
                                                {{ $s['health_status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- SUB-TAB CONTENT B: MAHASISWA PASIF & KRITIS (EARLY WARNING) -->
            <div x-show="studentSubTab === 'pasif'" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
                <div class="p-5 bg-rose-50/60 dark:bg-rose-950/20 border-b border-rose-100 dark:border-rose-900/40 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-rose-900 dark:text-rose-200">Radar Peringatan Dini: Mahasiswa Terancam Mandek</h3>
                            <p class="text-xs text-rose-700 dark:text-rose-400">Daftar mahasiswa aktif yang sudah lebih dari 14 hari tidak melakukan bimbingan atau belum pernah bimbingan sama sekali.</p>
                        </div>
                    </div>
                </div>

                @if(count($inactive_students) === 0)
                    <div class="p-12 text-center space-y-3">
                        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">Luar biasa! Tidak ada mahasiswa yang pasif pada kriteria ini.</p>
                        <p class="text-xs text-slate-500">Semua mahasiswa aktif telah melaksanakan bimbingan dalam 14 hari terakhir.</p>
                    </div>
                @else
                    <!-- Mobile Cards -->
                    <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($inactive_students as $idx => $s)
                            <div class="p-4 space-y-3 hover:bg-rose-50/30 dark:hover:bg-rose-950/10 transition-colors">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white dark:bg-slate-800">
                                            <img src="{{ $s['avatar_url'] }}" alt="{{ $s['name'] }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $s['name'] }}</p>
                                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                                                <span>{{ $s['identifier'] ?: '-' }}</span>
                                                <span>•</span>
                                                <span class="{{ $s['entry_year'] && $s['entry_year'] <= (now()->year - 4) ? 'text-rose-600 dark:text-rose-400 font-bold' : '' }}">Angkatan {{ $s['entry_year'] ?: '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if($s['days_since_last'] !== null)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-black bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 shrink-0">
                                            {{ $s['days_since_last'] }} Hari
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-xl text-[11px] font-black bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-800 shrink-0">
                                            Belum Pernah
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-700 dark:text-slate-300 font-medium line-clamp-2 leading-snug">{{ $s['title'] }}</p>
                                <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                                    <div class="space-y-0.5 min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">{{ $s['stage'] }}</span>
                                            <span>• {{ $s['last_session_at'] ? \Carbon\Carbon::parse($s['last_session_at'])->format('d M Y') : 'Nol Bimbingan' }}</span>
                                        </div>
                                        <p class="truncate">P1: {{ $s['pembimbing1_name'] ?: '-' }} | P2: {{ $s['pembimbing2_name'] ?: '-' }}</p>
                                    </div>
                                    <button type="button"
                                            @click="openReminderModal('student', {{ $s['student_id'] }}, '{{ addslashes($s['name']) }}', '{{ $s['phone'] }}', {{ $s['thesis_id'] }}, {{ $s['days_since_last'] ?: 0 }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-all active:scale-95 cursor-pointer ml-auto">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        <span>Kirim Pengingat WA</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-900/50 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="py-4 px-4 text-center w-12">No</th>
                                    <th class="py-4 px-5">Mahasiswa</th>
                                    <th class="py-4 px-4 text-center">Masa Inaktif (Hari)</th>
                                    <th class="py-4 px-4">Status & Sesi Terakhir</th>
                                    <th class="py-4 px-5">Dosen Pembimbing</th>
                                    <th class="py-4 px-5">Judul Skripsi</th>
                                    <th class="py-4 px-5 text-right">Aksi Tindak Lanjut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs text-slate-700 dark:text-slate-300">
                                @foreach($inactive_students as $idx => $s)
                                    <tr class="hover:bg-rose-50/30 dark:hover:bg-rose-950/10 transition-colors">
                                        <td class="py-4 px-4 text-center font-bold text-slate-500 dark:text-slate-400">{{ $idx + 1 }}</td>
                                        
                                        <!-- Profile -->
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white dark:bg-slate-800">
                                                    <img src="{{ $s['avatar_url'] }}" alt="{{ $s['name'] }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-900 dark:text-white block truncate max-w-[180px] sm:max-w-xs" title="{{ $s['name'] }}">
                                                        {{ $s['name'] }}
                                                    </span>
                                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 font-mono">
                                                        <span>{{ $s['identifier'] ?: '-' }}</span>
                                                        <span>•</span>
                                                        <span class="{{ $s['entry_year'] && $s['entry_year'] <= (now()->year - 4) ? 'text-rose-600 font-bold' : '' }}">
                                                            Angkatan {{ $s['entry_year'] ?: '-' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Inactive Days -->
                                        <td class="py-4 px-4 text-center">
                                            @if($s['days_since_last'] !== null)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 shadow-2xs">
                                                    {{ $s['days_since_last'] }} Hari
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                                    Belum Pernah Sesi
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Status & Sesi Terakhir -->
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 mb-1">
                                                {{ $s['stage'] }}
                                            </span>
                                            <p class="text-[11px] text-slate-500">
                                                {{ $s['last_session_at'] ? \Carbon\Carbon::parse($s['last_session_at'])->format('d M Y') : 'Nol Bimbingan' }}
                                            </p>
                                        </td>

                                        <!-- Pembimbing -->
                                        <td class="py-4 px-5">
                                            <div class="text-[11px] space-y-0.5 max-w-xs">
                                                <p class="truncate" title="Pembimbing 1: {{ $s['pembimbing1_name'] }}">P1: {{ $s['pembimbing1_name'] ?: '-' }}</p>
                                                <p class="truncate text-slate-500" title="Pembimbing 2: {{ $s['pembimbing2_name'] }}">P2: {{ $s['pembimbing2_name'] ?: '-' }}</p>
                                            </div>
                                        </td>

                                        <!-- Judul Skripsi -->
                                        <td class="py-4 px-5">
                                            <p class="font-medium text-slate-800 dark:text-slate-200 line-clamp-2 max-w-sm text-xs leading-snug" title="{{ $s['title'] }}">
                                                {{ $s['title'] }}
                                            </p>
                                        </td>

                                        <!-- Aksi WA -->
                                        <td class="py-4 px-5 text-right whitespace-nowrap">
                                            <button type="button" 
                                                    @click="openReminderModal('student', {{ $s['student_id'] }}, '{{ addslashes($s['name']) }}', '{{ $s['phone'] }}', {{ $s['thesis_id'] }}, {{ $s['days_since_last'] ?: 0 }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs shadow-emerald-600/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                <span>Kirim Pengingat WA</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

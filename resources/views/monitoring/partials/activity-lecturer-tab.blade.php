        <!-- ========================================== -->
        <!-- TAB 2: DOSEN PEMBIMBING ACTIVITY           -->
        <!-- ========================================== -->
        <div x-show="activeMainTab === 'dosen'" class="space-y-6">
            
            <!-- SUB-TAB TOGGLE: TERAJIN VS PERLU PERHATIAN -->
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button type="button" 
                            @click="dosenSubTab = 'terajin'"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer"
                            :class="dosenSubTab === 'terajin' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100'">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span>Dosen Paling Rajin (Leaderboard)</span>
                    </button>
                    <button type="button" 
                            @click="dosenSubTab = 'pasif'"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer"
                            :class="dosenSubTab === 'pasif' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100'">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Perlu Perhatian / Pasif</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[11px] font-black"
                              :class="dosenSubTab === 'pasif' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'">
                            {{ count($inactive_dosens) }}
                        </span>
                    </button>
                </div>

                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan data dosen aktif berdasarkan periode: <strong class="text-slate-800 dark:text-white">{{ $period_label }}</strong>
                </span>
            </div>

            <!-- SUB-TAB CONTENT A: DOSEN TERAJIN -->
            <div x-show="dosenSubTab === 'terajin'" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
                @if(count($top_dosens) === 0)
                    <div class="p-12 text-center space-y-3">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada sesi bimbingan dosen tercatat pada periode ini.</p>
                    </div>
                @else
                    <!-- Mobile Cards -->
                    <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($top_dosens as $idx => $d)
                            <div class="p-4 space-y-2.5 {{ $idx < 3 ? 'bg-blue-50/20 dark:bg-blue-950/10' : '' }}">
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
                                            <img src="{{ $d['avatar_url'] }}" alt="{{ $d['name'] }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $d['name'] }}</p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono truncate">{{ $d['identifier'] ?: '-' }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-black bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 shrink-0">
                                        {{ $d['sessions_in_period'] }} Sesi
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-600 dark:text-slate-300">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 font-semibold">{{ $d['unique_students_in_period'] }} Mhs Periode</span>
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 font-semibold">{{ $d['supervised_count'] }} Mhs Aktif</span>
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 font-bold">{{ $d['total_all_time'] }}x Total</span>
                                    </div>
                                    @php
                                        $activityBadgeClass = match($d['activity_color'] ?? 'slate') {
                                            'emerald', 'green' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                                            'blue', 'sky' => 'bg-blue-50 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800/60',
                                            'amber', 'yellow', 'orange' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                                            'rose', 'red' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                                            default => 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider border {{ $activityBadgeClass }}">
                                        {{ $d['activity_level'] }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Terakhir: {{ $d['last_session_at'] ? \Carbon\Carbon::parse($d['last_session_at'])->diffForHumans() . ' (' . \Carbon\Carbon::parse($d['last_session_at'])->format('d M Y') . ')' : '-' }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-900/50 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="py-4 px-4 text-center w-14">Rank</th>
                                    <th class="py-4 px-5">Dosen Pembimbing</th>
                                    <th class="py-4 px-4 text-center">Sesi Periode Ini</th>
                                    <th class="py-4 px-4 text-center">Mhs Dibimbing (Periode)</th>
                                    <th class="py-4 px-4 text-center">Total Mhs Aktif</th>
                                    <th class="py-4 px-4 text-center">Total Akumulasi</th>
                                    <th class="py-4 px-4">Bimbingan Terakhir</th>
                                    <th class="py-4 px-4 text-center">Kategori Keaktifan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs text-slate-700 dark:text-slate-300">
                                @foreach($top_dosens as $idx => $d)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors {{ $idx < 3 ? 'bg-blue-50/20 dark:bg-blue-950/10' : '' }}">
                                        <!-- Rank -->
                                        <td class="py-4 px-4 text-center">
                                            @if($idx === 0)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-400 text-amber-950 font-black text-xs shadow-xs" title="Juara 1 Dosen Teraktif">#1</span>
                                            @elseif($idx === 1)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-slate-200 dark:bg-slate-600 text-slate-800 dark:text-white font-black text-xs shadow-xs" title="Juara 2 Dosen Teraktif">#2</span>
                                            @elseif($idx === 2)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-700 text-white font-black text-xs shadow-xs" title="Juara 3 Dosen Teraktif">#3</span>
                                            @else
                                                <span class="font-bold text-slate-500 text-xs">{{ $idx + 1 }}</span>
                                            @endif
                                        </td>

                                        <!-- Profile -->
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white dark:bg-slate-800">
                                                    <img src="{{ $d['avatar_url'] }}" alt="{{ $d['name'] }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-900 dark:text-white block truncate max-w-[200px] sm:max-w-xs" title="{{ $d['name'] }}">
                                                        {{ $d['name'] }}
                                                    </span>
                                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 font-mono">
                                                        <span>{{ $d['identifier'] ?: '-' }}</span>
                                                        <span>•</span>
                                                        <span>{{ $d['email'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Sesi Periode Ini -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 shadow-2xs">
                                                {{ $d['sessions_in_period'] }} Sesi
                                            </span>
                                        </td>

                                        <!-- Mhs Dibimbing Periode Ini -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $d['unique_students_in_period'] }} Mhs</span>
                                        </td>

                                        <!-- Total Mhs Aktif Dibimbing -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 font-semibold text-slate-700 dark:text-slate-300 text-[11px]">
                                                {{ $d['supervised_count'] }} Mahasiswa
                                            </span>
                                        </td>

                                        <!-- Total Akumulasi Sesi -->
                                        <td class="py-4 px-4 text-center font-bold text-slate-800 dark:text-slate-200">
                                            {{ $d['total_all_time'] }}x
                                        </td>

                                        <!-- Sesi Terakhir -->
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @if($d['last_session_at'])
                                                <span class="font-semibold text-slate-800 dark:text-slate-200 block text-[11px]">
                                                    {{ \Carbon\Carbon::parse($d['last_session_at'])->diffForHumans() }}
                                                </span>
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                                    {{ \Carbon\Carbon::parse($d['last_session_at'])->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="text-slate-500 dark:text-slate-400 italic text-[11px]">-</span>
                                            @endif
                                        </td>

                                        <!-- Kategori Keaktifan -->
                                        <td class="py-4 px-4 text-center">
                                            @php
                                        $activityBadgeClass = match($d['activity_color'] ?? 'slate') {
                                            'emerald', 'green' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                                            'blue', 'sky' => 'bg-blue-50 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800/60',
                                            'amber', 'yellow', 'orange' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                                            'rose', 'red' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                                            default => 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider border {{ $activityBadgeClass }}">
                                                {{ $d['activity_level'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- SUB-TAB CONTENT B: DOSEN PERLU PERHATIAN (PASIF) -->
            <div x-show="dosenSubTab === 'pasif'" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
                <div class="p-5 bg-amber-50/60 dark:bg-amber-950/20 border-b border-amber-100 dark:border-amber-900/40 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-amber-900 dark:text-amber-200">Dosen Pembimbing yang Perlu Koordinasi / Pengingat</h3>
                            <p class="text-xs text-amber-700 dark:text-amber-400">Dosen yang memiliki mahasiswa bimbingan aktif namun belum tercatat membimbing dalam periode ini (> 14 hari tanpa sesi).</p>
                        </div>
                    </div>
                </div>

                @if(count($inactive_dosens) === 0)
                    <div class="p-12 text-center space-y-3">
                        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">Seluruh Dosen Pembimbing aktif membimbing mahasiswa!</p>
                        <p class="text-xs text-slate-500">Tidak ada dosen dengan mahasiswa aktif yang mengalami stagnasi bimbingan.</p>
                    </div>
                @else
                    <!-- Mobile Cards -->
                    <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($inactive_dosens as $idx => $d)
                            <div class="p-4 space-y-3 hover:bg-amber-50/30 dark:hover:bg-amber-950/10 transition-colors">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white dark:bg-slate-800">
                                            <img src="{{ $d['avatar_url'] }}" alt="{{ $d['name'] }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $d['name'] }}</p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono truncate">{{ $d['identifier'] ?: '-' }} • {{ $d['email'] }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-black bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shrink-0">
                                        {{ $d['supervised_count'] }} Mhs
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-2 text-[11px]">
                                    <div class="space-y-0.5 text-slate-500 dark:text-slate-400">
                                        <p>Sesi periode ini: <strong class="text-slate-700 dark:text-slate-200">{{ $d['sessions_in_period'] }}</strong></p>
                                        <p>
                                            @if($d['days_since_last'] !== null)
                                                <span class="font-bold text-amber-700 dark:text-amber-400">{{ $d['days_since_last'] }} Hari Lalu</span>
                                            @else
                                                <span class="font-bold text-rose-600 dark:text-rose-400">Belum Pernah Sesi</span>
                                            @endif
                                            ({{ $d['last_session_at'] ? \Carbon\Carbon::parse($d['last_session_at'])->format('d M Y') : '-' }})
                                        </p>
                                    </div>
                                    <button type="button"
                                            @click="openReminderModal('dosen', {{ $d['id'] }}, '{{ addslashes($d['name']) }}', '{{ $d['phone'] }}', null, {{ $d['days_since_last'] ?: 0 }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-xs transition-all active:scale-95 cursor-pointer">
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
                                    <th class="py-4 px-5">Dosen Pembimbing</th>
                                    <th class="py-4 px-4 text-center">Mahasiswa Menunggu</th>
                                    <th class="py-4 px-4 text-center">Sesi Periode Ini</th>
                                    <th class="py-4 px-4 text-center">Hari Sejak Bimbingan Terakhir</th>
                                    <th class="py-4 px-4">Tanggal Terakhir</th>
                                    <th class="py-4 px-5 text-right">Aksi Koordinasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs text-slate-700 dark:text-slate-300">
                                @foreach($inactive_dosens as $idx => $d)
                                    <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/10 transition-colors">
                                        <td class="py-4 px-4 text-center font-bold text-slate-500 dark:text-slate-400">{{ $idx + 1 }}</td>
                                        
                                        <!-- Profile -->
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-white dark:bg-slate-800">
                                                    <img src="{{ $d['avatar_url'] }}" alt="{{ $d['name'] }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-900 dark:text-white block truncate max-w-[200px] sm:max-w-xs" title="{{ $d['name'] }}">
                                                        {{ $d['name'] }}
                                                    </span>
                                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 font-mono">
                                                        <span>{{ $d['identifier'] ?: '-' }}</span>
                                                        <span>•</span>
                                                        <span>{{ $d['email'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Mhs Menunggu -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs">
                                                {{ $d['supervised_count'] }} Mahasiswa
                                            </span>
                                        </td>

                                        <!-- Sesi Periode Ini -->
                                        <td class="py-4 px-4 text-center font-bold text-slate-500">
                                            {{ $d['sessions_in_period'] }}
                                        </td>

                                        <!-- Inactivity Duration -->
                                        <td class="py-4 px-4 text-center">
                                            @if($d['days_since_last'] !== null)
                                                <span class="text-xs font-bold text-amber-700 dark:text-amber-400">
                                                    {{ $d['days_since_last'] }} Hari Lalu
                                                </span>
                                            @else
                                                <span class="text-xs font-bold text-rose-600">Belum Pernah Sesi</span>
                                            @endif
                                        </td>

                                        <!-- Tanggal Terakhir -->
                                        <td class="py-4 px-4 whitespace-nowrap text-xs text-slate-500">
                                            {{ $d['last_session_at'] ? \Carbon\Carbon::parse($d['last_session_at'])->format('d M Y') : '-' }}
                                        </td>

                                        <!-- Aksi WA -->
                                        <td class="py-4 px-5 text-right whitespace-nowrap">
                                            <button type="button" 
                                                    @click="openReminderModal('dosen', {{ $d['id'] }}, '{{ addslashes($d['name']) }}', '{{ $d['phone'] }}', null, {{ $d['days_since_last'] ?: 0 }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-xs shadow-amber-600/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
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

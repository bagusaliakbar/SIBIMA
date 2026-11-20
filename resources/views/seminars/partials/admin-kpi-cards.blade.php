        {{-- KPI STAT CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Total Pendaftar --}}
            <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'all', 'search' => $search]) }}"
               class="group relative overflow-hidden bg-white dark:bg-slate-800 p-5 rounded-2xl border transition-all duration-200 shadow-xs hover:shadow-md {{ ($status ?? 'all') === 'all' ? 'border-indigo-500/80 ring-2 ring-indigo-500/20 dark:ring-indigo-500/30' : 'border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Pendaftar</p>
                        <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-1 tracking-tight">{{ $stats['total'] ?? 0 }}</h4>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-[10px] font-bold {{ ($status ?? 'all') === 'all' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}">
                    <span>Semua pendaftaran seminar pada gelombang</span>
                </div>
            </a>

            {{-- Card 2: Menunggu Validasi --}}
            <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'pending', 'search' => $search]) }}"
               class="group relative overflow-hidden bg-white dark:bg-slate-800 p-5 rounded-2xl border transition-all duration-200 shadow-xs hover:shadow-md {{ ($status ?? '') === 'pending' ? 'border-amber-500/80 ring-2 ring-amber-500/20 dark:ring-amber-500/30' : 'border-slate-200/80 dark:border-slate-700/80 hover:border-amber-300 dark:hover:border-amber-700' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">Menunggu Validasi</p>
                            @if(($stats['pending'] ?? 0) > 0)
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                            @endif
                        </div>
                        <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 tracking-tight">{{ $stats['pending'] ?? 0 }}</h4>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/60 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-[10px] font-bold text-amber-700 dark:text-amber-400">
                    <span>Perlu verifikasi 5 berkas</span>
                </div>
            </a>

            {{-- Card 3: Disetujui / Siap Jadwal --}}
            <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'approved', 'search' => $search]) }}"
               class="group relative overflow-hidden bg-white dark:bg-slate-800 p-5 rounded-2xl border transition-all duration-200 shadow-xs hover:shadow-md {{ ($status ?? '') === 'approved' ? 'border-emerald-500/80 ring-2 ring-emerald-500/20 dark:ring-emerald-500/30' : 'border-slate-200/80 dark:border-slate-700/80 hover:border-emerald-300 dark:hover:border-emerald-700' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Disetujui / Siap Jadwal</p>
                        <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 tracking-tight">{{ $stats['approved'] ?? 0 }}</h4>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">
                    <span>Berkas valid & siap diseminarkan</span>
                </div>
            </a>

            {{-- Card 4: Perlu Revisi / Ditolak --}}
            <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'rejected', 'search' => $search]) }}"
               class="group relative overflow-hidden bg-white dark:bg-slate-800 p-5 rounded-2xl border transition-all duration-200 shadow-xs hover:shadow-md {{ ($status ?? '') === 'rejected' ? 'border-rose-500/80 ring-2 ring-rose-500/20 dark:ring-rose-500/30' : 'border-slate-200/80 dark:border-slate-700/80 hover:border-rose-300 dark:hover:border-rose-700' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-rose-600 dark:text-rose-400">Perlu Revisi / Ditolak</p>
                        <h4 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1 tracking-tight">{{ $stats['rejected'] ?? 0 }}</h4>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60 flex items-center justify-center text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-[10px] font-bold text-rose-700 dark:text-rose-400">
                    <span>Berkas belum memenuhi syarat</span>
                </div>
            </a>
        </div>

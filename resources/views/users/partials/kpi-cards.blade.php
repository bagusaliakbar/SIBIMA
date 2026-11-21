        <!-- KPI Summary Cards (Interactive Top Statistics) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- 1. Total Pengguna -->
            <a href="{{ route('users.index', ['status' => 'all', 'role' => 'all', 'per_page' => $perPage]) }}" 
               class="p-5 rounded-2xl border transition-all cursor-pointer group relative overflow-hidden flex flex-col justify-between bg-white dark:bg-slate-800 {{ ($status ?? 'all') === 'all' && ($role ?? 'all') === 'all' ? 'ring-2 ring-orange-500 border-orange-500 dark:border-orange-500 shadow-md shadow-orange-500/10' : 'border-slate-200 dark:border-slate-700 hover:border-orange-300 dark:hover:border-slate-600 hover:shadow-xs' }}"
               title="Klik untuk melihat semua pengguna">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Pengguna</span>
                    <div class="w-8 h-8 rounded-xl bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 border border-orange-200/60 dark:border-orange-800/60 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $kpiStats['total_users'] ?? 0 }}</div>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $kpiStats['total_active'] ?? 0 }} Aktif</span> • 
                        <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $kpiStats['total_pending'] ?? 0 }} Pending</span>
                    </p>
                </div>
            </a>

            <!-- 2. Dosen Pembimbing Aktif -->
            <a href="{{ route('users.index', ['role' => 'dosen', 'status' => 'active', 'per_page' => $perPage]) }}" 
               class="p-5 rounded-2xl border transition-all cursor-pointer group relative overflow-hidden flex flex-col justify-between bg-white dark:bg-slate-800 {{ ($role ?? '') === 'dosen' && ($status ?? 'all') === 'active' ? 'ring-2 ring-blue-500 border-blue-500 dark:border-blue-500 shadow-md shadow-blue-500/10' : 'border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-slate-600 hover:shadow-xs' }}"
               title="Klik untuk memfilter dosen pembimbing aktif">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400">Dosen Pembimbing Aktif</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/60 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $kpiStats['active_dosen'] ?? 0 }}</div>
                    <p class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $kpiStats['total_supervised'] ?? 0 }} Bimbingan Skripsi Aktif
                    </p>
                </div>
            </a>

            <!-- 3. Mahasiswa Aktif -->
            <a href="{{ route('users.index', ['role' => 'mahasiswa', 'status' => 'active', 'per_page' => $perPage]) }}" 
               class="p-5 rounded-2xl border transition-all cursor-pointer group relative overflow-hidden flex flex-col justify-between bg-white dark:bg-slate-800 {{ ($role ?? '') === 'mahasiswa' && ($status ?? 'all') === 'active' ? 'ring-2 ring-indigo-500 border-indigo-500 dark:border-indigo-500 shadow-md shadow-indigo-500/10' : 'border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-slate-600 hover:shadow-xs' }}"
               title="Klik untuk memfilter mahasiswa aktif">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Mahasiswa Aktif</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $kpiStats['active_mahasiswa'] ?? 0 }}</div>
                    <p class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 mt-1">
                        {{ $cohortCounts['new'] ?? 0 }} Baru • {{ $cohortCounts['old'] ?? 0 }} Lama
                    </p>
                </div>
            </a>

            <!-- 4. Menunggu Persetujuan (Pending) -->
            <a href="{{ route('users.index', ['status' => 'pending', 'role' => 'all', 'per_page' => $perPage]) }}" 
               class="p-5 rounded-2xl border transition-all cursor-pointer group relative overflow-hidden flex flex-col justify-between bg-white dark:bg-slate-800 {{ ($status ?? '') === 'pending' ? 'ring-2 ring-amber-500 border-amber-500 dark:border-amber-500 shadow-md shadow-amber-500/10' : (($kpiStats['total_pending'] ?? 0) > 0 ? 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/60 hover:border-amber-300 dark:hover:border-amber-700' : 'border-slate-200 dark:border-slate-700 hover:border-amber-300 dark:hover:border-slate-600 hover:shadow-xs') }}"
               title="Klik untuk memfilter akun pending yang perlu disetujui">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ ($kpiStats['total_pending'] ?? 0) > 0 ? 'text-amber-700 dark:text-amber-400' : 'text-slate-500 dark:text-slate-400' }}">Menunggu Persetujuan</span>
                    @if(($kpiStats['total_pending'] ?? 0) > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse shadow-[0_0_8px_rgba(245,158,11,0.6)]"></span>
                    @else
                        <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700/60 text-slate-500 dark:text-slate-400 border border-slate-200/60 dark:border-slate-600/60 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    @endif
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black {{ ($kpiStats['total_pending'] ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-100' }} tracking-tight">
                        {{ $kpiStats['total_pending'] ?? 0 }}
                    </div>
                    <p class="text-[11px] font-semibold mt-1 {{ ($kpiStats['total_pending'] ?? 0) > 0 ? 'text-amber-700 dark:text-amber-400 font-bold' : 'text-slate-500 dark:text-slate-400' }}">
                        {{ ($kpiStats['total_pending'] ?? 0) > 0 ? 'Butuh Verifikasi Akun' : 'Semua Akun Terverifikasi' }}
                    </p>
                </div>
            </a>
        </div>

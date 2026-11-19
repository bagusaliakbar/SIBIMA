        <!-- Status Tabs Navigation -->
        <div class="flex items-center gap-1 border-b border-slate-100 dark:border-slate-800 overflow-x-auto pb-px custom-scrollbar">
            @if(Auth::user()->role === 'dosen')
                <a href="{{ $buildQuery(['status' => 'active']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'active') === 'active' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Mahasiswa Bimbingan Aktif ({{ $stats['total'] ?? $dosenStats['total'] ?? 0 }})
                </a>
                <a href="{{ $buildQuery(['status' => 'completed']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'active') === 'completed' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Mahasiswa Bimbingan (Lulus) ({{ $stats['graduated_total'] ?? 0 }})
                </a>
            @else
                <a href="{{ route('theses.index', ['status' => 'all', 'search' => $search, 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'all' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Semua Pengajuan
                </a>
                <a href="{{ route('theses.index', ['status' => 'pending', 'search' => $search, 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'pending' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Menunggu Pembimbing
                </a>
                <a href="{{ route('theses.index', ['status' => 'active', 'search' => $search, 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'active' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Aktif / Berjalan
                </a>
                <a href="{{ route('theses.index', ['status' => 'completed', 'search' => $search, 'cohort_filter' => $cohortFilter ?? 'all', 'entry_year' => $entryYear ?? '']) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'completed' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Riwayat Lulus
                </a>
                <a href="{{ route('theses.unsubmitted-students') }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 border-transparent text-amber-600 dark:text-amber-400 hover:text-amber-700 hover:bg-amber-50/50 dark:hover:bg-amber-900/10">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Belum Mengajukan Judul
                </a>
            @endif
        </div>

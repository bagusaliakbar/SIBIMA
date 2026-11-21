            <!-- Admin & Kaprodi Experience with Alpine Tab Switcher -->
            <div x-data="{ 
                adminTab: 'operasional',
                setTab(tab) {
                    this.adminTab = tab;
                    if (tab === 'analitik') {
                        this.$nextTick(() => {
                            window.dispatchEvent(new Event('resize'));
                            if (typeof Chart !== 'undefined' && Chart.instances) {
                                Object.values(Chart.instances).forEach(c => c.resize());
                            }
                        });
                    }
                }
            }" class="space-y-6 mb-6">

                <!-- Executive Mode Switcher & Quick Actions Hub -->
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-4 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <!-- Segmented Tabs -->
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-900/80 rounded-xl border border-slate-200/60 dark:border-slate-800/80">
                        <button type="button" @click="setTab('operasional')" 
                            :class="adminTab === 'operasional' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-sm font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-bold'"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs uppercase tracking-wider transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>Operasional &amp; Antrean Berkas</span>
                            @if(($adminApprovals['total_pending'] ?? 0) > 0)
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-black bg-rose-600 text-white shadow-2xs">
                                    {{ $adminApprovals['total_pending'] }}
                                </span>
                            @endif
                        </button>
                        <button type="button" @click="setTab('analitik')" 
                            :class="adminTab === 'analitik' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-sm font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-bold'"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs uppercase tracking-wider transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Analitik &amp; Tren Skripsi</span>
                            <span class="text-[11px] uppercase tracking-widest px-1.5 py-0.5 rounded-md bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-400 font-black">9 Grafik</span>
                        </button>
                    </div>

                    <!-- Quick Actions Hub -->
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('wa-broadcasts.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl transition-all shadow-md shadow-emerald-600/20 active:scale-95 cursor-pointer" title="Kirim Pengumuman / Broadcast WhatsApp ke Mahasiswa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>Broadcast WA</span>
                        </a>
                        <a href="{{ route('monitoring.advanced-reporting') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white text-xs font-black rounded-xl transition-all shadow-sm active:scale-95 cursor-pointer" title="Buka Pusat Statistik & Pelaporan Lengkap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Pelaporan</span>
                        </a>
                        <a href="{{ route('waves.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-700/80 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer" title="Kelola Jadwal Gelombang Seminar & Sidang">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Gelombang</span>
                        </a>
                        <a href="{{ route('theses.index', ['status' => 'pending']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-700/80 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer" title="Plotting Dosen Pembimbing Skripsi">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Plotting</span>
                        </a>
                    </div>
                </div>

                <!-- TAB 1: OPERASIONAL & ANTREAN BERKAS -->
                <div x-show="adminTab === 'operasional'" class="space-y-6">
                    <!-- Antrean Verifikasi Berkas Prodi (4 Approval Cards) -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                Antrean Verifikasi &amp; Approval Prodi
                            </h3>
                            <span class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">
                                {{ ($adminApprovals['total_pending'] ?? 0) > 0 ? ($adminApprovals['total_pending'] . ' Berkas Menunggu Validasi') : 'Seluruh Berkas Telah Diproses' }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Card 1: Usulan Judul Baru -->
                            <a href="{{ route('theses.index', ['status' => 'pending']) }}" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border {{ (($adminApprovals['pending_theses'] ?? 0) + ($adminApprovals['unassigned_theses'] ?? 0)) > 0 ? 'border-amber-300 dark:border-amber-700/80 bg-gradient-to-br from-amber-50/50 via-white to-amber-50/20 dark:from-amber-950/20 dark:via-slate-800 dark:to-slate-800' : 'border-slate-200/80 dark:border-slate-700/80' }} shadow-xs hover:shadow-lg transition-all duration-300">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="w-9 h-9 rounded-xl {{ (($adminApprovals['pending_theses'] ?? 0) + ($adminApprovals['unassigned_theses'] ?? 0)) > 0 ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider {{ (($adminApprovals['pending_theses'] ?? 0) + ($adminApprovals['unassigned_theses'] ?? 0)) > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200 border border-amber-300 dark:border-amber-700' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                                        {{ (($adminApprovals['pending_theses'] ?? 0) + ($adminApprovals['unassigned_theses'] ?? 0)) > 0 ? 'Perlu ACC' : 'Tuntas' }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Usulan Judul Baru</h4>
                                <div class="flex items-baseline gap-2 mt-1 mb-1">
                                    <span class="text-2xl font-black text-slate-800 dark:text-white">{{ ($adminApprovals['pending_theses'] ?? 0) + ($adminApprovals['unassigned_theses'] ?? 0) }}</span>
                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Judul</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                    {{ $adminApprovals['unassigned_theses'] ?? 0 }} judul belum di-plot pembimbing.
                                </p>
                                <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-bold text-amber-600 dark:text-amber-400 group-hover:translate-x-0.5 transition-transform">
                                    <span>Tinjau &amp; Plotting</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </a>

                            <!-- Card 2: Pendaftaran Seminar -->
                            <a href="{{ route('seminar-applications.index') }}" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border {{ ($adminApprovals['pending_seminars'] ?? 0) > 0 ? 'border-blue-300 dark:border-blue-700/80 bg-gradient-to-br from-blue-50/50 via-white to-blue-50/20 dark:from-blue-950/20 dark:via-slate-800 dark:to-slate-800' : 'border-slate-200/80 dark:border-slate-700/80' }} shadow-xs hover:shadow-lg transition-all duration-300">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="w-9 h-9 rounded-xl {{ ($adminApprovals['pending_seminars'] ?? 0) > 0 ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider {{ ($adminApprovals['pending_seminars'] ?? 0) > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200 border border-blue-300 dark:border-blue-700' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                                        {{ ($adminApprovals['pending_seminars'] ?? 0) > 0 ? 'Validasi Syarat' : 'Tuntas' }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Berkas Seminar</h4>
                                <div class="flex items-baseline gap-2 mt-1 mb-1">
                                    <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $adminApprovals['pending_seminars'] ?? 0 }}</span>
                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pendaftar</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                    Menunggu verifikasi berkas pendaftaran seminar.
                                </p>
                                <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-bold text-blue-600 dark:text-blue-400 group-hover:translate-x-0.5 transition-transform">
                                    <span>Verifikasi Berkas</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </a>

                            <!-- Card 3: Pendaftaran Sidang -->
                            <a href="{{ route('thesis-defense-applications.index') }}" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border {{ ($adminApprovals['pending_defenses'] ?? 0) > 0 ? 'border-rose-300 dark:border-rose-700/80 bg-gradient-to-br from-rose-50/50 via-white to-rose-50/20 dark:from-rose-950/20 dark:via-slate-800 dark:to-slate-800' : 'border-slate-200/80 dark:border-slate-700/80' }} shadow-xs hover:shadow-lg transition-all duration-300">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="w-9 h-9 rounded-xl {{ ($adminApprovals['pending_defenses'] ?? 0) > 0 ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider {{ ($adminApprovals['pending_defenses'] ?? 0) > 0 ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200 border border-rose-300 dark:border-rose-700' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                                        {{ ($adminApprovals['pending_defenses'] ?? 0) > 0 ? 'Validasi Syarat' : 'Tuntas' }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Berkas Sidang</h4>
                                <div class="flex items-baseline gap-2 mt-1 mb-1">
                                    <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $adminApprovals['pending_defenses'] ?? 0 }}</span>
                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pendaftar</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                    Menunggu validasi naskah lengkap &amp; syarat sidang.
                                </p>
                                <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-bold text-rose-600 dark:text-rose-400 group-hover:translate-x-0.5 transition-transform">
                                    <span>Verifikasi Berkas</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </a>

                            <!-- Card 4: Pengajuan SKL & Bebas Lab -->
                            <a href="{{ route('graduations.index') }}" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border {{ ($adminApprovals['pending_graduations'] ?? 0) > 0 ? 'border-emerald-300 dark:border-emerald-700/80 bg-gradient-to-br from-emerald-50/50 via-white to-emerald-50/20 dark:from-emerald-950/20 dark:via-slate-800 dark:to-slate-800' : 'border-slate-200/80 dark:border-slate-700/80' }} shadow-xs hover:shadow-lg transition-all duration-300">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="w-9 h-9 rounded-xl {{ ($adminApprovals['pending_graduations'] ?? 0) > 0 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider {{ ($adminApprovals['pending_graduations'] ?? 0) > 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-700' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                                        {{ ($adminApprovals['pending_graduations'] ?? 0) > 0 ? 'Approval SKL' : 'Tuntas' }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Pengajuan SKL &amp; Yudisium</h4>
                                <div class="flex items-baseline gap-2 mt-1 mb-1">
                                    <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $adminApprovals['pending_graduations'] ?? 0 }}</span>
                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pengajuan</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                    Bebas tanggungan &amp; penerbitan SKL resmi.
                                </p>
                                <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-bold text-emerald-600 dark:text-emerald-400 group-hover:translate-x-0.5 transition-transform">
                                    <span>Tinjau Bebas Lab &amp; SKL</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Active Wave Status Widget for Admin -->
                    @if(isset($activeWaveInfo) && $activeWaveInfo)
                    <div class="bg-gradient-to-r from-orange-500/10 via-amber-500/10 to-orange-500/5 dark:from-orange-950/40 dark:via-amber-950/30 dark:to-slate-800/60 border border-orange-200/80 dark:border-orange-800/60 rounded-2xl p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start sm:items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-orange-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-orange-600/30 font-black">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-black uppercase tracking-wider bg-orange-600 text-white">Gelombang Ujian Sedang Berjalan</span>
                                    <h4 class="text-base font-black text-slate-800 dark:text-white">{{ $activeWaveInfo['name'] }}</h4>
                                    @if(!empty($activeWaveInfo['academic_year']) || !empty($activeWaveInfo['semester']))
                                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">({{ trim(($activeWaveInfo['academic_year'] ?? '') . ' ' . ($activeWaveInfo['semester'] ?? '')) }})</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 font-medium mt-1">
                                    Periode: <strong>{{ \Carbon\Carbon::parse($activeWaveInfo['start_date'])->translatedFormat('d M Y') }}</strong> s.d. <strong>{{ \Carbon\Carbon::parse($activeWaveInfo['end_date'])->translatedFormat('d M Y') }}</strong>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0 flex-wrap">
                            <div class="flex items-center gap-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white/90 dark:bg-slate-800/90 px-3.5 py-2 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                <span>Pendaftar:</span>
                                <span class="text-orange-600 font-black">{{ $activeWaveInfo['seminar_applicant_count'] }} Seminar</span>
                                <span class="text-slate-300 dark:text-slate-600">|</span>
                                <span class="text-emerald-600 font-black">{{ $activeWaveInfo['defense_applicant_count'] }} Sidang</span>
                            </div>
                            <span class="px-3.5 py-2 rounded-xl text-xs font-black uppercase tracking-wider {{ $activeWaveInfo['days_remaining'] < 0 ? 'bg-slate-600 text-white shadow-slate-600/20' : ($activeWaveInfo['days_remaining'] <= 3 ? 'bg-rose-600 text-white shadow-rose-600/20' : 'bg-amber-500 text-white shadow-amber-500/20') }} shadow-md">
                                {{ $activeWaveInfo['days_remaining'] < 0 ? 'Pendaftaran Berakhir' : ($activeWaveInfo['days_remaining'] === 0 ? 'Hari Terakhir!' : 'Sisa ' . $activeWaveInfo['days_remaining'] . ' Hari') }}
                            </span>
                            <a href="{{ route('seminar-schedules.index') }}" class="px-4 py-2 bg-slate-900 hover:bg-black dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-xs cursor-pointer">
                                Jadwalkan Ujian &rarr;
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="bg-slate-50 dark:bg-slate-800/50 border border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-500 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400 font-medium">Tidak ada gelombang ujian yang sedang aktif saat ini.</p>
                        </div>
                        <a href="{{ route('waves.index') }}" class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-2xs">
                            Buka Gelombang Baru
                        </a>
                    </div>
                    @endif

                    <!-- Admin Core Stats Row (4 Cards) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <x-stat-card title="Total Skripsi" :value="$activeThesesCount ?? 0" color="orange">
                            <x-slot name="icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </x-slot>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 italic tracking-tight">Total judul skripsi terdaftar</p>
                        </x-stat-card>

                        <x-stat-card title="Jadwal Minggu Ini" color="blue">
                            <x-slot name="icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </x-slot>
                            <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ $sessionsThisWeek ?? 0 }} <span class="text-xs font-medium text-slate-400 lowercase tracking-normal">Sesi</span></h2>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 italic tracking-tight">Bimbingan pekan berjalan</p>
                        </x-stat-card>

                        <x-stat-card title="Sesi Selesai" :value="$totalCompletedSessions ?? 0" color="emerald">
                            <x-slot name="icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </x-slot>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 italic tracking-tight">Total seluruh bimbingan</p>
                        </x-stat-card>

                        <x-stat-card title="Progres Rata-rata" :value="($averageStudentProgress ?? 0) . '%'" color="pink">
                            <x-slot name="icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </x-slot>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 italic tracking-tight">Performa bimbingan global</p>
                        </x-stat-card>
                    </div>

                    <!-- Early Warning System for Admin -->
                    @if(isset($earlyWarning) && ($earlyWarning['total_inactive'] ?? 0) > 0)
                        <div class="bg-gradient-to-r from-amber-100/70 via-rose-50/70 to-orange-100/60 dark:from-amber-950/40 dark:via-rose-950/20 dark:to-orange-950/30 rounded-3xl p-6 sm:p-7 shadow-sm relative overflow-hidden">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-start sm:items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-amber-500/30">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-sm sm:text-base font-black text-slate-800 dark:text-white uppercase tracking-tight">
                                                Early Warning System: Bimbingan Butuh Intervensi
                                            </h3>
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-2xs">
                                                {{ $earlyWarning['total_inactive'] }} Mahasiswa Pasif / Macet
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 dark:text-slate-300 font-medium mt-1.5 leading-relaxed">
                                            Terdapat 
                                            <a href="{{ route('theses.index', ['mentoring_health' => 'warning']) }}" 
                                               class="underline decoration-amber-400 font-bold text-slate-800 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition-colors" 
                                               title="Klik untuk filter mahasiswa pasif (8–14 hari)">
                                                {{ $earlyWarning['warning_count'] }} mahasiswa pasif (8–14 hari)
                                            </a> 
                                            dan 
                                            <a href="{{ route('theses.index', ['filter' => 'stalled']) }}" 
                                               class="underline decoration-rose-400 font-bold text-slate-800 dark:text-white hover:text-rose-600 dark:hover:text-rose-400 transition-colors" 
                                               title="Klik untuk filter mahasiswa macet (>14 hari)">
                                                {{ $stalledThesesCount ?? $earlyWarning['stalled_count'] ?? $earlyWarning['critical_count'] }} mahasiswa macet (&gt;14 hari)
                                            </a> 
                                            yang belum melakukan bimbingan rutin (standar prodi: min 1x/pekan).
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('theses.index', ['filter' => 'stalled']) }}" 
                                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 active:scale-95 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-md shadow-amber-600/25 transition-all cursor-pointer">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>Tinjau &amp; Ingatkan Mahasiswa</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- TAB 2: ANALITIK & TREN SKRIPSI (9 GRAFIK) -->
                <div x-show="adminTab === 'analitik'" x-cloak class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Chart 1: Distribution / Status Skripsi Global -->
                        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                                    Status Skripsi Global
                                </h3>
                                <div class="h-64">
                                    <canvas id="distributionChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Chart 2: Trends -->
                        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 dark:bg-blue-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                                    Tren Aktivitas Bimbingan
                                </h3>
                                <div class="h-64">
                                    <canvas id="activityChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Chart 3: Score Distribution -->
                        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    Distribusi Nilai Akhir (Grade)
                                </h3>
                                <div class="h-64">
                                    <canvas id="scoreDistributionChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Chart 4: Wave Duration -->
                        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Lama Pengerjaan per Gelombang (Bulan)
                                </h3>
                                <div class="h-64">
                                    <canvas id="waveDurationChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Chart 5: On-time Graduation -->
                        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 dark:bg-indigo-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Ketepatan Waktu Lulus
                                </h3>
                                <div class="h-64">
                                    <canvas id="onTimeGraduationChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Chart 6: Student Health -->
                        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 dark:bg-rose-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Kesehatan Masa Studi Mahasiswa
                                </h3>
                                <div class="h-64">
                                    <canvas id="studentHealthChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart 7: Topic Trend Analysis (AI Insights) -->
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.989-2.386l-.548-.547z"></path></svg>
                                    Analisa Tren Topik (AI Insights)
                                </h3>
                                <div class="px-3 py-1 bg-orange-100/80 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 text-[11px] font-black uppercase tracking-wider rounded-lg border border-orange-200 dark:border-orange-500/20">
                                    Berbasis Data Tren
                                </div>
                            </div>
                            <div class="h-96">
                                <canvas id="topicTrendChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart 8: Cohort Completion (Full Width) -->
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/10 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                Rata-rata Masa Studi per Angkatan (Tahun)
                            </h3>
                            <div class="h-80">
                                <canvas id="cohortCompletionChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart 9: Dosen Workload (Full Width) -->
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 dark:bg-emerald-900/10 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Dosen dengan Beban Bimbingan Terbanyak (Top 10)
                            </h3>
                            <div class="h-80">
                                <canvas id="workloadChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

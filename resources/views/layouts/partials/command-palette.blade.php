@php
    $cmdRole = Auth::user()?->role ?? 'mahasiswa';
    $cmdMenus = [
        ['category' => 'Menu Utama', 'title' => 'Dashboard', 'subtitle' => 'Ringkasan aktivitas & statistik utama', 'badge' => 'Menu', 'url' => route('dashboard'), 'icon' => 'home', 'keywords' => 'beranda home ringkasan'],
        ['category' => 'Menu Utama', 'title' => 'Pesan / Chat', 'subtitle' => 'Percakapan langsung antar pengguna SIBIMA', 'badge' => 'Menu', 'url' => route('chat.index'), 'icon' => 'chat', 'keywords' => 'pesan chat obrolan dm'],
        ['category' => 'Menu Utama', 'title' => 'Kalender Akademik', 'subtitle' => 'Jadwal bimbingan, seminar, dan sidang', 'badge' => 'Menu', 'url' => route('calendar.index'), 'icon' => 'calendar', 'keywords' => 'kalender jadwal tanggal'],
        ['category' => 'Menu Utama', 'title' => 'Katalog Pustaka', 'subtitle' => 'Arsip judul & dokumen skripsi lulus', 'badge' => 'Menu', 'url' => route('repositories.index'), 'icon' => 'book', 'keywords' => 'repositori pustaka arsip skripsi'],
        ['category' => 'Menu Utama', 'title' => 'Panduan & Dokumen', 'subtitle' => 'Template, pedoman penulisan, dan berkas prodi', 'badge' => 'Menu', 'url' => route('guidance-documents.index'), 'icon' => 'document', 'keywords' => 'panduan template dokumen unduh'],
        ['category' => 'Menu Utama', 'title' => 'Pusat Bantuan (FAQ)', 'subtitle' => 'Tanya jawab & panduan penggunaan SIBIMA', 'badge' => 'Bantuan', 'url' => route('faqs.index'), 'icon' => 'help', 'keywords' => 'faq bantuan cara tanya jawab'],
        ['category' => 'Akun', 'title' => 'Profil & Pengaturan Akun', 'subtitle' => 'Ubah profil, foto, tanda tangan digital, dan password', 'badge' => 'Akun', 'url' => route('profile.edit'), 'icon' => 'user', 'keywords' => 'profil password tanda tangan ttd akun'],
    ];

    if ($cmdRole === 'mahasiswa') {
        $cmdMenus = array_merge($cmdMenus, [
            ['category' => 'Skripsi Saya', 'title' => 'Pengajuan & Status Skripsi', 'subtitle' => 'Pantau judul, pembimbing, dan status ACC', 'badge' => 'Skripsi', 'url' => route('theses.index'), 'icon' => 'thesis', 'keywords' => 'judul skripsi acc pembimbing'],
            ['category' => 'Skripsi Saya', 'title' => 'Jadwal & Logbook Bimbingan', 'subtitle' => 'Konfirmasi kehadiran, jam datang, dan riwayat bimbingan', 'badge' => 'Bimbingan', 'url' => route('mentoring.student_index'), 'icon' => 'calendar', 'keywords' => 'bimbingan jadwal logbook hadir izin'],
            ['category' => 'Skripsi Saya', 'title' => 'Pendaftaran Seminar Proposal', 'subtitle' => 'Daftar & pantau jadwal Seminar UP', 'badge' => 'Seminar', 'url' => route('seminar-applications.index'), 'icon' => 'document', 'keywords' => 'seminar sempro up pendaftaran'],
            ['category' => 'Skripsi Saya', 'title' => 'Revisi Seminar Proposal', 'subtitle' => 'Unggah & pantau persetujuan revisi penguji seminar', 'badge' => 'Revisi', 'url' => route('student-seminar-revisions.index'), 'icon' => 'document', 'keywords' => 'revisi seminar penguji'],
            ['category' => 'Skripsi Saya', 'title' => 'Pendaftaran Sidang Skripsi', 'subtitle' => 'Daftar & pantau jadwal Sidang Akhir', 'badge' => 'Sidang', 'url' => route('thesis-defense-applications.index'), 'icon' => 'document', 'keywords' => 'sidang akhir pendaftaran'],
            ['category' => 'Skripsi Saya', 'title' => 'Revisi Sidang Skripsi', 'subtitle' => 'Unggah & pantau persetujuan revisi penguji sidang', 'badge' => 'Revisi', 'url' => route('student-defense-revisions.index'), 'icon' => 'document', 'keywords' => 'revisi sidang penguji'],
            ['category' => 'Skripsi Saya', 'title' => 'Yudisium & SKL', 'subtitle' => 'Verifikasi bebas tanggungan dan Surat Keterangan Lulus', 'badge' => 'Kelulusan', 'url' => route('student.graduation'), 'icon' => 'thesis', 'keywords' => 'yudisium skl lulus bebas tanggungan'],
        ]);
    } elseif ($cmdRole === 'dosen') {
        $cmdMenus = array_merge($cmdMenus, [
            ['category' => 'Bimbingan & Pengujian', 'title' => 'Daftar Mahasiswa Bimbingan', 'subtitle' => 'Kelola logbook, progres, dan ACC mahasiswa P1/P2', 'badge' => 'Bimbingan', 'url' => route('theses.index'), 'icon' => 'thesis', 'keywords' => 'mahasiswa bimbingan acc logbook'],
            ['category' => 'Bimbingan & Pengujian', 'title' => 'Jadwal Bimbingan Dosen', 'subtitle' => 'Buat jadwal, atur rentang jam, reschedule, dan absensi', 'badge' => 'Jadwal', 'url' => route('mentoring-sessions.index'), 'icon' => 'calendar', 'keywords' => 'jadwal bimbingan reschedule hadir'],
            ['category' => 'Bimbingan & Pengujian', 'title' => 'Penguji Seminar Proposal', 'subtitle' => 'Input nilai & tinjau revisi seminar proposal', 'badge' => 'Penguji', 'url' => route('seminar-examiner.index'), 'icon' => 'document', 'keywords' => 'penguji seminar nilai revisi'],
            ['category' => 'Bimbingan & Pengujian', 'title' => 'Penguji Sidang Skripsi', 'subtitle' => 'Input nilai & tinjau revisi sidang skripsi', 'badge' => 'Penguji', 'url' => route('defense-examiner.index'), 'icon' => 'document', 'keywords' => 'penguji sidang nilai revisi'],
        ]);
    } elseif (in_array($cmdRole, ['admin', 'kaprodi'], true)) {
        $cmdMenus = array_merge($cmdMenus, [
            ['category' => 'Manajemen Akademik', 'title' => 'Daftar Pengajuan Skripsi', 'subtitle' => 'Validasi judul & penetapan dosen pembimbing', 'badge' => 'Akademik', 'url' => route('theses.index'), 'icon' => 'thesis', 'keywords' => 'skripsi judul pembimbing acc'],
            ['category' => 'Manajemen Akademik', 'title' => 'Monitoring Progres Mahasiswa', 'subtitle' => 'Pantau progres, keaktifan bimbingan, dan kesiapan ACC', 'badge' => 'Monitoring', 'url' => route('monitoring.index'), 'icon' => 'thesis', 'keywords' => 'monitoring progres bimbingan acc'],
            ['category' => 'Manajemen Akademik', 'title' => 'Rekap Nilai Sidang', 'subtitle' => 'Lihat nilai akhir dan cetak berita acara sidang', 'badge' => 'Nilai', 'url' => route('monitoring.defense-scores'), 'icon' => 'document', 'keywords' => 'nilai sidang berita acara'],
            ['category' => 'Manajemen Akademik', 'title' => 'Jadwal Seminar Proposal', 'subtitle' => 'Atur jadwal, simulasi otomatis, dan cetak berita acara', 'badge' => 'Seminar', 'url' => route('seminar-schedules.index'), 'icon' => 'calendar', 'keywords' => 'jadwal seminar sempro simulasi'],
            ['category' => 'Manajemen Akademik', 'title' => 'Jadwal Sidang Skripsi', 'subtitle' => 'Atur jadwal sidang akhir, penguji, dan berita acara', 'badge' => 'Sidang', 'url' => route('thesis-defense-schedules.index'), 'icon' => 'calendar', 'keywords' => 'jadwal sidang simulasi'],
            ['category' => 'Manajemen Akademik', 'title' => 'Yudisium & SKL', 'subtitle' => 'Validasi kelulusan & penerbitan SKL mahasiswa', 'badge' => 'Kelulusan', 'url' => route('graduations.index'), 'icon' => 'thesis', 'keywords' => 'yudisium skl lulus'],
            ['category' => 'Manajemen Akademik', 'title' => 'Gelombang Pendaftaran', 'subtitle' => 'Kelola periode aktif dan arsip gelombang', 'badge' => 'Periode', 'url' => route('waves.index'), 'icon' => 'calendar', 'keywords' => 'gelombang wave periode arsip'],
            ['category' => 'Pengguna & Sistem', 'title' => 'Manajemen Pengguna', 'subtitle' => 'Kelola akun mahasiswa, dosen, dan Login As', 'badge' => 'Pengguna', 'url' => route('users.index'), 'icon' => 'user', 'keywords' => 'users akun mahasiswa dosen login as'],
            ['category' => 'Pengguna & Sistem', 'title' => 'Mode Pemeliharaan Sistem', 'subtitle' => 'Atur banner, layar kunci, countdown, dan URL rahasia', 'badge' => 'Sistem', 'url' => route('admin.maintenance.index'), 'icon' => 'settings', 'keywords' => 'maintenance pemeliharaan countdown kunci'],
            ['category' => 'Pengguna & Sistem', 'title' => 'Log Sistem & Aktivitas', 'subtitle' => 'Audit riwayat aktivitas pengguna dan sistem', 'badge' => 'Sistem', 'url' => route('admin.logs'), 'icon' => 'document', 'keywords' => 'log audit aktivitas sistem'],
            ['category' => 'Pengguna & Sistem', 'title' => 'Laporan Bug & Masukan', 'subtitle' => 'Kelola laporan kendala dari mahasiswa & dosen', 'badge' => 'Sistem', 'url' => route('admin.bug-reports.index'), 'icon' => 'help', 'keywords' => 'bug laporan kendala'],
        ]);
    }
@endphp

<div x-data="{
        open: false,
        query: '',
        loading: false,
        selectedIndex: 0,
        menus: @js($cmdMenus),
        serverResults: [],
        debounceTimer: null,
        abortCtrl: null,
        get filteredMenus() {
            const q = this.query.trim().toLowerCase();
            if (!q) return this.menus.slice(0, 8);
            return this.menus.filter(item =>
                item.title.toLowerCase().includes(q)
                || item.subtitle.toLowerCase().includes(q)
                || (item.keywords && item.keywords.toLowerCase().includes(q))
                || item.category.toLowerCase().includes(q)
            ).slice(0, 6);
        },
        get combinedItems() {
            const items = [];
            this.filteredMenus.forEach(m => items.push(m));
            this.serverResults.forEach(r => items.push(r));
            return items;
        },
        openPalette() {
            this.open = true;
            this.selectedIndex = 0;
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                    this.$refs.searchInput.select();
                }
            });
        },
        closePalette() {
            this.open = false;
            this.query = '';
            this.serverResults = [];
            this.loading = false;
            if (this.abortCtrl) this.abortCtrl.abort();
        },
        onInput() {
            this.selectedIndex = 0;
            clearTimeout(this.debounceTimer);
            const q = this.query.trim();
            if (q.length < 2) {
                this.serverResults = [];
                this.loading = false;
                return;
            }
            this.loading = true;
            this.debounceTimer = setTimeout(() => this.fetchServerResults(q), 240);
        },
        async fetchServerResults(q) {
            if (this.abortCtrl) this.abortCtrl.abort();
            this.abortCtrl = new AbortController();
            try {
                const res = await fetch('{{ route("quick-search") }}?q=' + encodeURIComponent(q), {
                    signal: this.abortCtrl.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) return;
                const data = await res.json();
                this.serverResults = Array.isArray(data.results) ? data.results : [];
                this.selectedIndex = 0;
            } catch (e) {
                if (e.name !== 'AbortError') {
                    this.serverResults = [];
                }
            } finally {
                this.loading = false;
            }
        },
        moveSelection(delta) {
            const total = this.combinedItems.length;
            if (total === 0) return;
            this.selectedIndex = (this.selectedIndex + delta + total) % total;
            this.$nextTick(() => {
                const el = this.$refs.resultsContainer?.querySelector('[data-cmd-idx=&quot;' + this.selectedIndex + '&quot;]');
                if (el) el.scrollIntoView({ block: 'nearest' });
            });
        },
        activateSelected() {
            const item = this.combinedItems[this.selectedIndex];
            if (item && item.url) {
                window.location.href = item.url;
            }
        }
    }"
    @keydown.window.prevent.ctrl.k="open ? closePalette() : openPalette()"
    @keydown.window.prevent.meta.k="open ? closePalette() : openPalette()"
    @sibima-open-search.window="openPalette()"
    class="print:hidden">

    <!-- Backdrop & Modal -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="if (open) closePalette()"
         class="fixed inset-0 z-[80] bg-slate-950/55 backdrop-blur-xs flex items-start justify-center pt-14 sm:pt-24 px-3 sm:px-4"
         @click.self="closePalette()"
         role="dialog"
         aria-modal="true"
         aria-label="Pencarian Cepat SIBIMA">

        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
             class="w-full max-w-xl bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-2xl overflow-hidden">

            <!-- Search Input Row -->
            <div class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-200/80 dark:border-slate-800">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input x-ref="searchInput"
                       type="text"
                       x-model="query"
                       @input="onInput()"
                       @keydown.arrow-down.prevent="moveSelection(1)"
                       @keydown.arrow-up.prevent="moveSelection(-1)"
                       @keydown.enter.prevent="activateSelected()"
                       placeholder="{{ in_array($cmdRole, ['admin', 'kaprodi']) ? 'Cari nama mahasiswa, NPM, judul skripsi, dosen, atau menu...' : ($cmdRole === 'dosen' ? 'Cari mahasiswa bimbingan, judul skripsi, atau menu...' : 'Cari menu, panduan, katalog pustaka, atau FAQ...') }}"
                       class="flex-1 bg-transparent border-0 p-0 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:ring-0 focus:outline-none"
                       style="box-shadow: none !important; border: 0 !important;"
                       autocomplete="off" />

                <svg x-show="loading" x-cloak class="w-4 h-4 text-orange-600 animate-spin shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>

                <button type="button" @click="closePalette()" class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors cursor-pointer">
                    ESC
                </button>
            </div>

            <!-- Results List -->
            <div x-ref="resultsContainer" class="max-h-[60vh] overflow-y-auto custom-scrollbar py-2 divide-y divide-slate-100/70 dark:divide-slate-800/70">
                <template x-if="combinedItems.length === 0 && !loading">
                    <div class="px-6 py-10 text-center space-y-1.5">
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Tidak ada hasil untuk "<span x-text="query"></span>"</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Coba kata kunci lain seperti nama mahasiswa, NPM, judul, atau nama menu.</p>
                    </div>
                </template>

                <div x-show="combinedItems.length > 0" class="px-2 space-y-0.5">
                    <template x-for="(item, idx) in combinedItems" :key="item.category + '-' + item.title + '-' + idx">
                        <a :href="item.url"
                           :data-cmd-idx="idx"
                           @mouseenter="selectedIndex = idx"
                           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl transition-colors"
                           :class="selectedIndex === idx ? 'bg-orange-50 dark:bg-orange-950/40 text-orange-950 dark:text-orange-100' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60'">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border"
                                      :class="selectedIndex === idx ? 'bg-orange-600 text-white border-orange-600' : 'bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200/80 dark:border-slate-700'">
                                    <svg x-show="item.icon === 'home'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    <svg x-show="item.icon === 'thesis'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                    <svg x-show="item.icon === 'user'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <svg x-show="item.icon === 'calendar'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <svg x-show="item.icon === 'chat'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                    <svg x-show="item.icon === 'book'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    <svg x-show="item.icon === 'document'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <svg x-show="item.icon === 'help'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <svg x-show="item.icon === 'settings'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold truncate" x-text="item.title"></p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate" x-text="item.subtitle"></p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider shrink-0 border"
                                  :class="selectedIndex === idx ? 'bg-orange-100 dark:bg-orange-900/60 text-orange-800 dark:text-orange-200 border-orange-200 dark:border-orange-700' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200/80 dark:border-slate-700'"
                                  x-text="item.category"></span>
                        </a>
                    </template>
                </div>
            </div>

            <!-- Keyboard Hints Footer -->
            <div class="px-4 py-2.5 bg-slate-50/90 dark:bg-slate-950/60 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                <div class="hidden sm:flex items-center gap-3">
                    <span class="inline-flex items-center gap-1"><kbd class="px-1.5 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-[10px]">&uarr;&darr;</kbd> Navigasi</span>
                    <span class="inline-flex items-center gap-1"><kbd class="px-1.5 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-[10px]">Enter</kbd> Buka</span>
                    <span class="inline-flex items-center gap-1"><kbd class="px-1.5 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-[10px]">Esc</kbd> Tutup</span>
                </div>
                <span class="font-semibold text-slate-500 dark:text-slate-400">Pencarian Cepat SIBIMA</span>
            </div>
        </div>
    </div>
</div>

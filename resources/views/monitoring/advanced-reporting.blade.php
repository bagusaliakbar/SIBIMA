<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Monitoring', 'route' => route('monitoring.index')],
                    ['label' => 'Pusat Laporan & Analisa', 'route' => route('monitoring.advanced-reporting')]
                ]" />
                <div class="flex items-center gap-3 mt-1">
                    <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight">
                        Pusat Statistik & Pelaporan Terpadu
                    </h2>
                    <span class="px-2.5 py-0.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                        Enterprise Pro
                    </span>
                </div>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    Platform eksekutif Kaprodi & Admin untuk rekapitulasi data akademik, yudisium, beban dosen, dan instrumen akreditasi.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Pelaporan Aktif
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ 
        activeTab: 'akademik',
        reportInfo: {
            akademik: {
                title: 'Laporan Progres & Status Skripsi Mahasiswa',
                desc: 'Rekapitulasi lengkap status bimbingan mahasiswa, jumlah sesi bimbingan P1/P2, status kelayakan seminar (ACC UP), dan kesiapan sidang akhir skripsi.',
                tag: 'Data Pokok Akademik',
                columns: 'NPM, Nama, Angkatan, Judul, Pembimbing 1 & 2, Sesi Bimbingan, Status ACC UP, Status ACC Sidang, Status Skripsi'
            },
            nilai_sidang: {
                title: 'Laporan Rekapitulasi Nilai Sidang & Yudisium',
                desc: 'Rincian penilaian objektif dari Pembimbing 1, Penguji 1, dan Penguji 2, mencakup nilai akhir angka, konversi nilai huruf, dan status kelulusan sidang.',
                tag: 'Bahan Rapat Yudisium',
                columns: 'NPM, Nama, Angkatan, Judul, Tim Penguji, Nilai P1, Nilai E1, Nilai E2, Total Nilai, Nilai Akhir (Angka & Huruf), Status Lulus'
            },
            beban_dosen: {
                title: 'Laporan Beban Kinerja Dosen (Bimbingan & Penguji)',
                desc: 'Rekapitulasi kuota bimbingan mahasiswa (P1/P2) serta frekuensi menguji ujian seminar proposal dan sidang skripsi per dosen untuk dasar SK Dekan & pencairan honorarium.',
                tag: 'SK Beban & Honorarium',
                columns: 'Nama Dosen, NIP/NIDN, Pembimbing 1, Pembimbing 2, Penguji Seminar, Penguji Sidang, Total Beban Kinerja'
            },
            kepatuhan: {
                title: 'Laporan Kepatuhan Bimbingan & Early Warning Pasif',
                desc: 'Audit kepatuhan bimbingan mingguan untuk mendeteksi mahasiswa pasif (>14 hari tanpa interaksi) dan evaluasi pemenuhan kuota minimal sesi (8x seminar, 16x sidang).',
                tag: 'SLA & Peringatan Dini',
                columns: 'NPM, Nama, Angkatan, Pembimbing, Sesi Bimbingan, Hari Sejak Bimbingan Terakhir, Status Risiko Keaktifan, Kesiapan Kuota'
            },
            topik: {
                title: 'Laporan Distribusi Topik & Bidang Peminatan Skripsi',
                desc: 'Pemetaan klasifikasi bidang keahlian topik penelitian skripsi per angkatan dan per dosen pembimbing sebagai instrumen wajib Akreditasi Prodi (LAM-INFOKOM / BAN-PT).',
                tag: 'Borang Akreditasi Prodi',
                columns: 'NPM, Nama, Angkatan, Bidang/Topik Peminatan, Judul Skripsi, Pembimbing 1 & 2, Status Skripsi'
            },
            kelulusan: {
                title: 'Laporan Rekapitulasi Kelulusan & Masa Studi',
                desc: 'Daftar resmi mahasiswa yang telah menyelesaikan seluruh tahapan skripsi dan bebas tanggungan akademik beserta durasi masa studi riil kelulusan.',
                tag: 'Data Kelulusan & Alumni',
                columns: 'NPM, Nama, Angkatan, Judul Final, Pembimbing, Tanggal Lulus, Masa Studi Riil (Tahun), Predikat Kelulusan'
            },
            waktu: {
                title: 'Laporan Analisis Masa Studi & Tren Gelombang',
                desc: 'Evaluasi rata-rata durasi pengerjaan skripsi mahasiswa per gelombang pendaftaran serta perbandingan persentase kelulusan tepat waktu (<=4 tahun) vs terlambat.',
                tag: 'Statistik & Indikator Kinerja',
                columns: 'Nama Gelombang, Periode Pelaksanaan, Total Mahasiswa Lulus, Rata-rata Durasi (Bulan), Lulus Tepat Waktu, % Kelulusan'
            },
            logs: {
                title: 'Laporan Audit Log Aktivitas & Jejak Sistem',
                desc: 'Rekaman jejak audit trail seluruh aktivitas administratif, perubahan data nilai, dan mutasi skripsi oleh pengguna untuk kebutuhan akuntabilitas dan keamanan.',
                tag: 'Audit Trail Keamanan',
                columns: 'Waktu Aktivitas, Pengguna & Role, Tipe Aktivitas, Deskripsi Mutasi/Perubahan Data'
            }
        }
    }">

        <!-- Navigation Tabs Terpadu Ekosistem Monitoring -->
        @include('monitoring.partials.tabs')

        <!-- Top Section: Executive Overview / Hero KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5">
            <!-- Card 1: Total Skripsi Aktif -->
            <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group hover:border-orange-500/50 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Skripsi Aktif</span>
                    <div class="w-8 h-8 rounded-xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <p class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $activeThesesCount }}</p>
                    <span class="text-[11px] font-bold text-slate-400">Mahasiswa</span>
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 truncate">Tahap bimbingan berjalan</p>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-500 to-amber-500 opacity-80"></div>
            </div>

            <!-- Card 2: Bimbingan Minggu Ini -->
            <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group hover:border-blue-500/50 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Sesi Minggu Ini</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <p class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $sessionsThisWeek }}</p>
                    <span class="text-[11px] font-bold text-slate-400">Interaksi</span>
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 truncate">Keaktifan dosen & mhs</p>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-500 opacity-80"></div>
            </div>

            <!-- Card 3: Mahasiswa Lulus -->
            <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group hover:border-emerald-500/50 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Lulus</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <p class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $thesisStatusCounts['completed'] ?? 0 }}</p>
                    <span class="text-[11px] font-bold text-slate-400">Lulusan</span>
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 truncate">Telah yudisium & SKL</p>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500 opacity-80"></div>
            </div>

            <!-- Card 4: Mahasiswa Pasif (Early Warning) -->
            @php
                $inactiveCount = count($globalInactiveList ?? []);
            @endphp
            <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group hover:border-amber-500/50 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Mhs Pasif (>14 H)</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <p class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $inactiveCount }}</p>
                    <span class="text-[11px] font-bold text-slate-400">Mahasiswa</span>
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 truncate">Perlu surat teguran</p>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-500 opacity-80"></div>
            </div>

            <!-- Card 5: Masa Studi Kritis -->
            <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group hover:border-rose-500/50 transition-all col-span-2 sm:col-span-1">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Masa Studi Kritis</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <p class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $studentHealthStats['Kritis'] ?? 0 }}</p>
                    <span class="text-[11px] font-bold text-slate-400">Mahasiswa</span>
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 truncate">Batas waktu kelulusan</p>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-rose-500 opacity-80"></div>
            </div>
        </div>

        <!-- Section 2: Pilihan Kategori Laporan (Interactive Cards Grid) -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                        Pilih Kategori Laporan yang Diinginkan
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pilih salah satu dari 8 laporan spesifik di bawah ini untuk mengonfigurasi filter dan mengekspor data.</p>
                </div>
                <span class="text-xs font-bold text-slate-400 hidden sm:inline-block">8 Jenis Laporan Tersedia</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- 1. Akademik & Mahasiswa -->
                <button type="button" @click="activeTab = 'akademik'" 
                        :class="activeTab === 'akademik' ? 'ring-2 ring-orange-500 bg-orange-50/50 dark:bg-orange-500/10 border-orange-500/40 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600'" 
                        class="p-4 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between group cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <div :class="activeTab === 'akademik' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 group-hover:bg-orange-100 dark:group-hover:bg-orange-950/40 group-hover:text-orange-600'" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200/60 dark:border-orange-800/40">Utama</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-1">Status & Progres Akademik</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">Rekap bimbingan, logbook, sesi P1/P2, dan kesiapan ACC seminar & sidang.</p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] font-bold" :class="activeTab === 'akademik' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400'">
                        <span>Konfigurasi Filter &rarr;</span>
                    </div>
                </button>

                <!-- 2. Nilai Sidang & Yudisium -->
                <button type="button" @click="activeTab = 'nilai_sidang'" 
                        :class="activeTab === 'nilai_sidang' ? 'ring-2 ring-orange-500 bg-orange-50/50 dark:bg-orange-500/10 border-orange-500/40 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600'" 
                        class="p-4 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between group cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <div :class="activeTab === 'nilai_sidang' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 group-hover:bg-orange-100 dark:group-hover:bg-orange-950/40 group-hover:text-orange-600'" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/40">Yudisium</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-1">Rekap Nilai & Yudisium</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">Nilai tim penelaah (P1, E1, E2), nilai akhir angka/huruf, dan status kelulusan sidang.</p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] font-bold" :class="activeTab === 'nilai_sidang' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400'">
                        <span>Konfigurasi Filter &rarr;</span>
                    </div>
                </button>

                <!-- 3. Beban Dosen (Bimbing & Uji) -->
                <button type="button" @click="activeTab = 'beban_dosen'" 
                        :class="activeTab === 'beban_dosen' ? 'ring-2 ring-orange-500 bg-orange-50/50 dark:bg-orange-500/10 border-orange-500/40 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600'" 
                        class="p-4 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between group cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <div :class="activeTab === 'beban_dosen' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 group-hover:bg-orange-100 dark:group-hover:bg-orange-950/40 group-hover:text-orange-600'" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/40">SK & Honor</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-1">Beban Kinerja Dosen</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">Beban bimbingan P1/P2 ditambah frekuensi menguji seminar & sidang untuk SK.</p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] font-bold" :class="activeTab === 'beban_dosen' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400'">
                        <span>Konfigurasi Filter &rarr;</span>
                    </div>
                </button>

                <!-- 4. Kepatuhan & Mahasiswa Pasif -->
                <button type="button" @click="activeTab = 'kepatuhan'" 
                        :class="activeTab === 'kepatuhan' ? 'ring-2 ring-orange-500 bg-orange-50/50 dark:bg-orange-500/10 border-orange-500/40 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600'" 
                        class="p-4 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between group cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <div :class="activeTab === 'kepatuhan' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 group-hover:bg-orange-100 dark:group-hover:bg-orange-950/40 group-hover:text-orange-600'" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/40">Early Warning</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-1">Kepatuhan & Pasif</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">Peringatan mahasiswa tidak bimbingan (>14 hari) dan kepatuhan kuota sesi.</p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] font-bold" :class="activeTab === 'kepatuhan' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400'">
                        <span>Konfigurasi Filter &rarr;</span>
                    </div>
                </button>

                <!-- 5. Distribusi Topik & Peminatan -->
                <button type="button" @click="activeTab = 'topik'" 
                        :class="activeTab === 'topik' ? 'ring-2 ring-orange-500 bg-orange-50/50 dark:bg-orange-500/10 border-orange-500/40 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600'" 
                        class="p-4 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between group cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <div :class="activeTab === 'topik' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 group-hover:bg-orange-100 dark:group-hover:bg-orange-950/40 group-hover:text-orange-600'" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40">Akreditasi</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-1">Topik & Peminatan</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">Klasifikasi topik riset skripsi per angkatan untuk evaluasi kurikulum LAM-INFOKOM.</p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] font-bold" :class="activeTab === 'topik' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400'">
                        <span>Konfigurasi Filter &rarr;</span>
                    </div>
                </button>

                <!-- 6. Rekapitulasi Kelulusan -->
                <button type="button" @click="activeTab = 'kelulusan'" 
                        :class="activeTab === 'kelulusan' ? 'ring-2 ring-orange-500 bg-orange-50/50 dark:bg-orange-500/10 border-orange-500/40 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600'" 
                        class="p-4 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between group cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <div :class="activeTab === 'kelulusan' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 group-hover:bg-orange-100 dark:group-hover:bg-orange-950/40 group-hover:text-orange-600'" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/40">Alumni</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-1">Rekapitulasi Kelulusan</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">Daftar alumni yang telah tuntas sidang, masa studi riil, dan tanggal penyelesaian.</p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] font-bold" :class="activeTab === 'kelulusan' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400'">
                        <span>Konfigurasi Filter &rarr;</span>
                    </div>
                </button>

                <!-- 7. Waktu & Tren Masa Studi -->
                <button type="button" @click="activeTab = 'waktu'" 
                        :class="activeTab === 'waktu' ? 'ring-2 ring-orange-500 bg-orange-50/50 dark:bg-orange-500/10 border-orange-500/40 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600'" 
                        class="p-4 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between group cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <div :class="activeTab === 'waktu' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 group-hover:bg-orange-100 dark:group-hover:bg-orange-950/40 group-hover:text-orange-600'" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/40">Statistik</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-1">Analisis Durasi & Tren</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">Rata-rata bulan pengerjaan skripsi per gelombang & rasio lulus tepat waktu.</p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] font-bold" :class="activeTab === 'waktu' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400'">
                        <span>Konfigurasi Filter &rarr;</span>
                    </div>
                </button>

                <!-- 8. Audit Log Sistem -->
                <button type="button" @click="activeTab = 'logs'" 
                        :class="activeTab === 'logs' ? 'ring-2 ring-orange-500 bg-orange-50/50 dark:bg-orange-500/10 border-orange-500/40 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600'" 
                        class="p-4 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between group cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <div :class="activeTab === 'logs' ? 'bg-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 group-hover:bg-orange-100 dark:group-hover:bg-orange-950/40 group-hover:text-orange-600'" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">Audit Trail</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-1">Audit Log Sistem</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">Riwayat audit aktivitas admin, mutasi data skripsi, dan rekaman perubahan sistem.</p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] font-bold" :class="activeTab === 'logs' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400'">
                        <span>Konfigurasi Filter &rarr;</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Section 3: Smart Filter Matrix & Download Hub -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
            <!-- Header Panel Laporan Aktif -->
            <div class="p-6 md:p-8 bg-gradient-to-r from-slate-50 via-white to-slate-50 dark:from-slate-900/40 dark:via-slate-800 dark:to-slate-900/40 border-b border-slate-200/80 dark:border-slate-700/80">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 bg-orange-500/10 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase tracking-wider rounded-md border border-orange-500/20" x-text="reportInfo[activeTab].tag"></span>
                            <span class="text-xs text-slate-400">• Konfigurator Filter & Ekspor</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-slate-100" x-text="reportInfo[activeTab].title"></h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-3xl leading-relaxed" x-text="reportInfo[activeTab].desc"></p>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-400 bg-slate-100 dark:bg-slate-900/60 px-3.5 py-2 rounded-xl border border-slate-200/60 dark:border-slate-700/60 shrink-0">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Format Dokumen: Landscape A4 / Excel OpenXML</span>
                    </div>
                </div>
            </div>

            <!-- Formulir Filter Multi-Dimensi -->
            <form action="{{ route('monitoring.export') }}" method="GET" class="p-6 md:p-8 space-y-6">
                <input type="hidden" name="type" :value="activeTab">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Filter 1: Tanggal Mulai -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 ml-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Dari Tanggal
                        </label>
                        <input type="date" name="start_date" value="{{ now()->startOfYear()->format('Y-m-d') }}" 
                               class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    <!-- Filter 2: Tanggal Selesai -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 ml-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Sampai Tanggal
                        </label>
                        <input type="date" name="end_date" value="{{ now()->format('Y-m-d') }}" 
                               class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    <!-- Filter 3: Gelombang (Wave) -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 ml-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Gelombang Ujian
                        </label>
                        <select name="wave_id" class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            <option value="">Semua Gelombang</option>
                            @foreach($waves as $w)
                                <option value="{{ $w->id }}">{{ $w->name }} {{ $w->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter 4: Angkatan (Cohort) -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 ml-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Angkatan Masuk
                        </label>
                        <select name="entry_year" class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            <option value="">Semua Angkatan</option>
                            @foreach($entryYears as $year)
                                <option value="{{ $year }}">Angkatan {{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter 5: Dosen Pembimbing / Penguji -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 ml-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Dosen Pembimbing
                        </label>
                        <select name="pembimbing_id" class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            <option value="">Semua Dosen</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter 6: Status Kelulusan -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 ml-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Status Kelulusan
                        </label>
                        <select name="graduation_status" class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            <option value="active">Mahasiswa Aktif (Belum Lulus)</option>
                            <option value="graduated">Mahasiswa Telah Lulus (Selesai)</option>
                            <option value="all">Semua Mahasiswa (Aktif & Lulus)</option>
                        </select>
                    </div>

                    <!-- Filter 7: Status ACC Seminar/Sidang -->
                    <div class="space-y-1.5" x-show="activeTab === 'akademik' || activeTab === 'kepatuhan'">
                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 ml-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Tahapan ACC
                        </label>
                        <select name="acc_status" class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            <option value="all">Semua Tahapan ACC</option>
                            <option value="ready_seminar">Siap Seminar (ACC UP)</option>
                            <option value="ready_defense">Siap Sidang (ACC Sidang)</option>
                            <option value="partial_acc">ACC Parsial (1 Pembimbing)</option>
                            <option value="in_mentoring">Proses Bimbingan</option>
                        </select>
                    </div>

                    <!-- Filter 8: Pencarian Keyword -->
                    <div class="space-y-1.5" :class="(activeTab === 'akademik' || activeTab === 'kepatuhan') ? '' : 'sm:col-span-2'">
                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 ml-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Kata Kunci (NPM / Nama / Judul)
                        </label>
                        <input type="text" name="search" placeholder="Ketik kata kunci untuk menyaring..." 
                               class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>
                </div>

                <!-- Action Download Buttons Bar -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Filter otomatis diaplikasikan ke format dokumen yang dipilih.</span>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <!-- Tombol Download PDF -->
                        <button type="submit" name="format" value="pdf" 
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2.5 px-6 py-3 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-slate-800 dark:hover:bg-white active:scale-95 transition-all shadow-md shadow-slate-900/10 group cursor-pointer">
                            <svg class="w-4 h-4 text-red-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.49 2 2 6.49 2 12s4.49 10 10 10 10-4.49 10-10S17.51 2 12 2zm-1 8h2v5h-2v-5zm1-3c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/>
                            </svg>
                            <span>Download Laporan PDF</span>
                        </button>

                        <!-- Tombol Export Excel -->
                        <button type="submit" name="format" value="excel" 
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2.5 px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-black uppercase tracking-wider active:scale-95 transition-all shadow-md shadow-emerald-600/20 group cursor-pointer">
                            <svg class="w-4 h-4 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Export Data Excel (.xlsx)</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Section 4: Live Data Preview Panel -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>Pratinjau Struktur Data (Live Sample Preview)</span>
                            <span class="px-2 py-0.5 text-[9px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-md">5 Baris Teratas</span>
                        </h4>
                        <p class="text-xs text-slate-400">Cuplikan data aktual dari database untuk memvalidasi kolom dan representasi sebelum diunduh.</p>
                    </div>
                </div>
                <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5">
                    <span>Struktur Kolom:</span>
                    <span class="font-bold text-slate-700 dark:text-slate-200 truncate max-w-xs sm:max-w-md" x-text="reportInfo[activeTab].columns"></span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <!-- Preview 1: Akademik & Mahasiswa -->
                <div x-show="activeTab === 'akademik'">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/40 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-4">NPM</th>
                                <th class="py-3 px-4">Nama Mahasiswa</th>
                                <th class="py-3 px-4">Angkatan</th>
                                <th class="py-3 px-4">Judul Skripsi</th>
                                <th class="py-3 px-4">Pembimbing 1 & 2</th>
                                <th class="py-3 px-4 text-center">Total Sesi</th>
                                <th class="py-3 px-4 text-center">Status ACC UP</th>
                                <th class="py-3 px-4 text-center">Status ACC Sidang</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                            @forelse($previewAkademik as $t)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                                    <td class="py-3 px-4 font-mono font-bold text-slate-700 dark:text-slate-300">{{ $t->student->identifier ?? '-' }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $t->student->name ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ $t->student->entry_year ?? '-' }}</td>
                                    <td class="py-3 px-4 max-w-xs truncate" title="{{ $t->final_title ?? $t->title }}">{{ $t->final_title ?? $t->title }}</td>
                                    <td class="py-3 px-4 text-[11px]">
                                        <div class="truncate max-w-[180px]">P1: {{ $t->pembimbing1->name ?? '-' }}</div>
                                        <div class="truncate max-w-[180px] text-slate-400">P2: {{ $t->pembimbing2->name ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold">{{ ($t->sessions_p1 ?? 0) + ($t->sessions_p2 ?? 0) }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if($t->isAccUpFinal())
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">Siap Seminar</span>
                                        @else
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400">Belum ACC</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($t->isAccSidangFinal())
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">Siap Sidang</span>
                                        @else
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400">Belum ACC</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-orange-50 text-orange-600 dark:bg-orange-950/50 dark:text-orange-400">{{ ucfirst($t->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center text-slate-400">Belum ada data skripsi aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Preview 2: Nilai Sidang & Yudisium -->
                <div x-show="activeTab === 'nilai_sidang'">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/40 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-4">NPM</th>
                                <th class="py-3 px-4">Nama Mahasiswa</th>
                                <th class="py-3 px-4">Judul Skripsi</th>
                                <th class="py-3 px-4">Tim Penelaah (P1, E1, E2)</th>
                                <th class="py-3 px-4 text-center">Nilai Akhir</th>
                                <th class="py-3 px-4 text-center">Huruf</th>
                                <th class="py-3 px-4 text-center">Status Kelulusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                            @php $monitoringService = app(\App\Services\MonitoringService::class); @endphp
                            @forelse($previewNilaiSidang as $d)
                                @php 
                                    $sc = $monitoringService->calculateDefenseScores($d); 
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                                    <td class="py-3 px-4 font-mono font-bold">{{ $d->thesis->student->identifier ?? '-' }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $d->thesis->student->name ?? '-' }}</td>
                                    <td class="py-3 px-4 max-w-xs truncate">{{ $d->thesis->final_title ?? $d->thesis->title ?? '-' }}</td>
                                    <td class="py-3 px-4 text-[11px] text-slate-500">
                                        P1: {{ $d->thesis->pembimbing1->name ?? '-' }} • E1: {{ $d->examiner1->name ?? '-' }} • E2: {{ $d->examiner2->name ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold text-sm text-slate-800 dark:text-slate-100">
                                        {{ $sc['finalScore'] > 0 ? number_format($sc['finalScore'], 1) : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-black text-sm text-indigo-600 dark:text-indigo-400">
                                        {{ $sc['finalGrade'] ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($sc['finalScore'] >= 60)
                                            <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">LULUS</span>
                                        @elseif($sc['finalScore'] > 0)
                                            <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300">TIDAK LULUS</span>
                                        @else
                                            <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400">BELUM LENGKAP</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data sidang skripsi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Preview 3: Beban Kinerja Dosen -->
                <div x-show="activeTab === 'beban_dosen'">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/40 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-4">Nama Dosen</th>
                                <th class="py-3 px-4">NIP / NIDN</th>
                                <th class="py-3 px-4 text-center">Bimbingan P1</th>
                                <th class="py-3 px-4 text-center">Bimbingan P2</th>
                                <th class="py-3 px-4 text-center">Total Beban Bimbingan</th>
                                <th class="py-3 px-4 text-center">Status Beban</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                            @forelse($previewBebanDosen as $dos)
                                @php $totDos = ($dos->theses_as_p1_count ?? 0) + ($dos->theses_as_p2_count ?? 0); @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $dos->name }}</td>
                                    <td class="py-3 px-4 font-mono">{{ $dos->identifier ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center font-bold">{{ $dos->theses_as_p1_count ?? 0 }}</td>
                                    <td class="py-3 px-4 text-center font-bold">{{ $dos->theses_as_p2_count ?? 0 }}</td>
                                    <td class="py-3 px-4 text-center font-black text-sm text-slate-800 dark:text-slate-100">{{ $totDos }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if($totDos >= 10)
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400">Padat (&ge;10)</span>
                                        @elseif($totDos >= 5)
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">Normal</span>
                                        @else
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">Tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data dosen pengajar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Preview 4: Kepatuhan Bimbingan -->
                <div x-show="activeTab === 'kepatuhan'">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/40 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-4">NPM</th>
                                <th class="py-3 px-4">Nama Mahasiswa</th>
                                <th class="py-3 px-4">Pembimbing 1</th>
                                <th class="py-3 px-4 text-center">Total Sesi</th>
                                <th class="py-3 px-4 text-center">Bimbingan Terakhir</th>
                                <th class="py-3 px-4 text-center">Status Risiko Keaktifan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                            @forelse($previewKepatuhan as $kp)
                                @php $daysInactive = $kp->days_since_last_mentoring; @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                                    <td class="py-3 px-4 font-mono font-bold">{{ $kp->student->identifier ?? '-' }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $kp->student->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-slate-500">{{ $kp->pembimbing1->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center font-bold">{{ ($kp->sessions_p1 ?? 0) + ($kp->sessions_p2 ?? 0) }}</td>
                                    <td class="py-3 px-4 text-center">
                                        {{ $daysInactive !== null ? $daysInactive . ' Hari Lalu' : 'Belum Pernah' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($daysInactive === null || $daysInactive > 14)
                                            <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300">KRITIS (>14 HARI)</span>
                                        @elseif($daysInactive > 7)
                                            <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">PERINGATAN (>7 HARI)</span>
                                        @else
                                            <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">AKTIF</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">Semua mahasiswa aktif melakukan bimbingan secara teratur.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Preview 5: Topik & Peminatan -->
                <div x-show="activeTab === 'topik'">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/40 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-4">NPM</th>
                                <th class="py-3 px-4">Nama Mahasiswa</th>
                                <th class="py-3 px-4">Bidang / Topik Riset</th>
                                <th class="py-3 px-4">Judul Skripsi</th>
                                <th class="py-3 px-4">Pembimbing 1</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                            @forelse($previewTopik as $top)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                                    <td class="py-3 px-4 font-mono font-bold">{{ $top->student->identifier ?? '-' }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $top->student->name ?? '-' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">
                                            {{ strtoupper($top->topic ?? 'UMUM') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 max-w-sm truncate">{{ $top->final_title ?? $top->title }}</td>
                                    <td class="py-3 px-4 text-slate-500">{{ $top->pembimbing1->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ ucfirst($top->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data topik skripsi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Preview 6: Kelulusan -->
                <div x-show="activeTab === 'kelulusan'">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/40 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-4">NPM</th>
                                <th class="py-3 px-4">Nama Mahasiswa</th>
                                <th class="py-3 px-4">Angkatan</th>
                                <th class="py-3 px-4">Judul Final Skripsi</th>
                                <th class="py-3 px-4 text-center">Tanggal Lulus</th>
                                <th class="py-3 px-4 text-center">Masa Studi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                            @forelse($previewKelulusan as $kel)
                                @php
                                    $studyY = '-';
                                    if ($kel->student?->entry_year && $kel->updated_at) {
                                        $studyY = round($kel->updated_at->year - $kel->student->entry_year, 1) . ' Tahun';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                                    <td class="py-3 px-4 font-mono font-bold">{{ $kel->student->identifier ?? '-' }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $kel->student->name ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ $kel->student->entry_year ?? '-' }}</td>
                                    <td class="py-3 px-4 max-w-sm truncate">{{ $kel->final_title ?? $kel->title }}</td>
                                    <td class="py-3 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $kel->updated_at ? $kel->updated_at->format('d/m/Y') : '-' }}</td>
                                    <td class="py-3 px-4 text-center font-bold">{{ $studyY }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data kelulusan yang tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Preview 7: Waktu & Tren -->
                <div x-show="activeTab === 'waktu'">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h5 class="text-xs font-black uppercase tracking-wider text-slate-400">Rata-Rata Durasi Skripsi per Gelombang</h5>
                            <div class="space-y-2">
                                @forelse($waveDurationStats ?? [] as $wName => $avgM)
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/60">
                                        <span class="font-bold text-slate-700 dark:text-slate-200">{{ $wName }}</span>
                                        <span class="font-black text-indigo-600 dark:text-indigo-400">{{ $avgM }} Bulan</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400">Belum ada data gelombang kelulusan.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h5 class="text-xs font-black uppercase tracking-wider text-slate-400">Rasio Ketepatan Waktu Lulus</h5>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-4 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-800/40 text-center">
                                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $onTimeStats['Tepat Waktu'] ?? 0 }}</p>
                                    <p class="text-xs font-bold text-slate-500 mt-1">Lulus Tepat Waktu (&le;4 Thn)</p>
                                </div>
                                <div class="p-4 rounded-xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-800/40 text-center">
                                    <p class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $onTimeStats['Terlambat'] ?? 0 }}</p>
                                    <p class="text-xs font-bold text-slate-500 mt-1">Lulus Terlambat (>4 Thn)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview 8: Logs -->
                <div x-show="activeTab === 'logs'">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/40 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4">Pengguna</th>
                                <th class="py-3 px-4">Aktivitas</th>
                                <th class="py-3 px-4">Deskripsi Audit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                            @forelse($previewLogs as $lg)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                                    <td class="py-3 px-4 text-slate-400 whitespace-nowrap">{{ $lg->created_at ? $lg->created_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200">{{ $lg->user->name ?? 'System' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-md bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                            {{ strtoupper($lg->activity ?? '-') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300">{{ $lg->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400">Belum ada riwayat aktivitas log.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

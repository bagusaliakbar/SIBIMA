<!-- Modal Panduan Interaktif Penggunaan Menu Jadwal Bimbingan -->
<template x-teleport="body">
    <div x-show="openGuideModal" 
         x-cloak 
         class="fixed inset-0 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200"
         style="z-index: 99999 !important;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="openGuideModal = false">
        
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 max-w-4xl w-full max-h-[92vh] flex flex-col overflow-hidden relative"
             style="z-index: 100000 !important;"
             @click.outside="openGuideModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700/80 bg-gradient-to-r from-amber-500/10 via-orange-500/5 to-transparent flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">
                                Panduan Jadwal Bimbingan
                            </h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-100 dark:bg-orange-950/80 text-orange-600 dark:text-orange-400 border border-orange-200/60 dark:border-orange-800/40">
                                Panduan Dosen
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Pelajari alur kerja bimbingan skripsi, fitur melihat berkas naskah, dan 3 mode tampilan.
                        </p>
                    </div>
                </div>

                <button type="button" 
                        @click="openGuideModal = false" 
                        class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700/60 transition-all cursor-pointer"
                        title="Tutup (Esc)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Tab Navigation Buttons -->
            <div class="px-6 pt-3 pb-1 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/60 dark:bg-slate-900/30 flex items-center gap-2 overflow-x-auto no-scrollbar shrink-0">
                <button type="button"
                        @click="activeGuideTab = 'workflow'"
                        :class="activeGuideTab === 'workflow' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs border-orange-200 dark:border-orange-500/30' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 border-transparent'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all border flex items-center gap-2 cursor-pointer whitespace-nowrap">
                    <span class="w-2 h-2 rounded-full" :class="activeGuideTab === 'workflow' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600'"></span>
                    <span>1. Alur Kerja Bimbingan</span>
                </button>

                <button type="button"
                        @click="activeGuideTab = 'views'"
                        :class="activeGuideTab === 'views' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs border-orange-200 dark:border-orange-500/30' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 border-transparent'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all border flex items-center gap-2 cursor-pointer whitespace-nowrap">
                    <span class="w-2 h-2 rounded-full" :class="activeGuideTab === 'views' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600'"></span>
                    <span>2. Pilihan 3 Tampilan</span>
                </button>

                <button type="button"
                        @click="activeGuideTab = 'tips'"
                        :class="activeGuideTab === 'tips' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs border-orange-200 dark:border-orange-500/30' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 border-transparent'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all border flex items-center gap-2 cursor-pointer whitespace-nowrap">
                    <span class="w-2 h-2 rounded-full" :class="activeGuideTab === 'tips' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600'"></span>
                    <span>3. Tips & Fitur Canggih</span>
                </button>

                <button type="button"
                        @click="activeGuideTab = 'faq'"
                        :class="activeGuideTab === 'faq' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs border-orange-200 dark:border-orange-500/30' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 border-transparent'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all border flex items-center gap-2 cursor-pointer whitespace-nowrap">
                    <span class="w-2 h-2 rounded-full" :class="activeGuideTab === 'faq' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600'"></span>
                    <span>4. Tanya & Jawab (FAQ)</span>
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6">
                
                <!-- TAB 1: ALUR KERJA BIMBINGAN (STEP-BY-STEP) -->
                <div x-show="activeGuideTab === 'workflow'" class="space-y-5">
                    <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-800/40 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-black text-sm">
                            💡
                        </div>
                        <div class="text-xs text-amber-900 dark:text-amber-200 leading-relaxed">
                            <strong class="font-bold">Prinsip Utama:</strong> Bimbingan skripsi mahasiswa dihitung resmi oleh sistem SIBIMA jika statusnya telah ditandai <strong>"Selesai"</strong> dan dosen telah memasukkan <strong>catatan/feedback</strong> hasil bimbingan.
                        </div>
                    </div>

                    <!-- 3 Step Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Step 1 -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 relative">
                            <div class="w-8 h-8 rounded-xl bg-orange-600 text-white flex items-center justify-center font-black text-xs mb-3 shadow-xs">
                                1
                            </div>
                            <h4 class="text-xs font-black text-slate-900 dark:text-white mb-1.5 uppercase tracking-wider">
                                Atur / Setujui Jadwal
                            </h4>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                Klik tombol <strong>"+ Tambah Jadwal"</strong> untuk membuka sesi baru (dapat memilih 1 atau banyak mahasiswa sekaligus). Jika mahasiswa yang mengajukan, Anda cukup klik <strong>"Setujui"</strong> atau lakukan <strong>"Reschedule"</strong> bila jam berhalangan.
                            </p>
                        </div>

                        <!-- Step 2 -->
                        <div class="p-4 rounded-2xl border border-orange-200 dark:border-orange-500/30 bg-orange-50/30 dark:bg-orange-950/10 relative">
                            <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-xs mb-3 shadow-xs">
                                2
                            </div>
                            <h4 class="text-xs font-black text-slate-900 dark:text-white mb-1.5 uppercase tracking-wider">
                                Buka Naskah & Cek Hadir
                            </h4>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                <span class="text-orange-600 dark:text-orange-400 font-bold">Klik pada baris mahasiswa</span> di tabel untuk membuka detail berkas naskah draft skripsi yang diunggah serta catatan pertemuan sebelumnya. Anda juga bisa melihat konfirmasi kehadiran mahasiswa.
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 relative">
                            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-xs mb-3 shadow-xs">
                                3
                            </div>
                            <h4 class="text-xs font-black text-slate-900 dark:text-white mb-1.5 uppercase tracking-wider">
                                Selesaikan & Catat Revisi
                            </h4>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                Setelah sesi bimbingan berakhir, klik tombol <strong>"Selesaikan"</strong> (ikon centang). Masukkan poin-poin arahan revisi untuk mahasiswa. Progres bimbingan mahasiswa akan langsung bertambah dan notifikasi WhatsApp otomatis dikirimkan ke mahasiswa.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: PILIHAN 3 TAMPILAN -->
                <div x-show="activeGuideTab === 'views'" class="space-y-4">
                    <p class="text-xs text-slate-600 dark:text-slate-400">
                        Anda dapat berganti tampilan kapan saja melalui tombol pengalih di pojok kanan atas:
                    </p>

                    <div class="space-y-3.5">
                        <!-- 1. Mode Tabel -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/40 flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Tampilan Tabel (Default & Paling Lengkap)</h4>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Format Baris</span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                    Menampilkan daftar terstruktur. <strong>Fitur unggulan:</strong> Klik baris mana saja untuk melebarkan (<em>expand</em>) lembar catatan dan riwayat mahasiswa tanpa berpindah halaman. Terdapat kotak centang untuk memproses banyak mahasiswa sekaligus.
                                </p>
                            </div>
                        </div>

                        <!-- 2. Mode Kartu -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/40 flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Tampilan Kartu (Timeline Waktu)</h4>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300">Visual</span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                    Sesi disajikan dalam bentuk kartu visual yang dikelompokkan berdasarkan waktu: <strong>Hari Ini</strong>, <strong>Besok</strong>, dan <strong>Mendatang</strong>. Sangat ideal saat Anda ingin melihat jadwal bimbingan terdekat dengan cepat.
                                </p>
                            </div>
                        </div>

                        <!-- 3. Mode Kalender -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/40 flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Tampilan Kalender (Agenda Bulanan)</h4>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-100 dark:bg-sky-950 text-sky-700 dark:text-sky-300">Bulanan</span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                    Menampilkan kalender agenda interaktif. Klik tanggal bimbingan untuk melihat nama mahasiswa dan topik yang dijadwalkan pada hari tersebut.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: TIPS & FITUR CANGGIH -->
                <div x-show="activeGuideTab === 'tips'" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Fitur 1: Expand Baris -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                            <div class="flex items-center gap-2.5 mb-2">
                                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950 text-orange-600 flex items-center justify-center font-bold text-xs">
                                    📂
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">Klik Baris untuk Lihat Naskah</h4>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                Pada mode tabel, cukup klik di bagian mana saja pada baris mahasiswa. Panel riwayat akan terbuka ke bawah, menampilkan link berkas naskah PDF/Word mahasiswa dan catatan pertemuan terdahulu.
                            </p>
                        </div>

                        <!-- Fitur 2: Bimbingan Kelompok -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                            <div class="flex items-center gap-2.5 mb-2">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                    👥
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">Bimbingan Bersama (Kelompok)</h4>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                Jika ingin membimbing 2-5 mahasiswa di ruang atau link Google Meet yang sama, Anda dapat menambahkan mahasiswa lain saat membuat jadwal atau melalui menu <em>"Edit / Tambah Mahasiswa"</em>.
                            </p>
                        </div>

                        <!-- Fitur 3: Aksi Massal -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                            <div class="flex items-center gap-2.5 mb-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center font-bold text-xs">
                                    ☑️
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">Selesaikan Sesi Secara Massal</h4>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                Centang kotak di samping kiri nama mahasiswa. Bilah aksi (dock) akan muncul di bawah layar, memungkinkan Anda menandai selesai beberapa mahasiswa sekaligus dengan cepat.
                            </p>
                        </div>

                        <!-- Fitur 4: Kehadiran Mahasiswa -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                            <div class="flex items-center gap-2.5 mb-2">
                                <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-950 text-blue-600 flex items-center justify-center font-bold text-xs">
                                    📡
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">Konfirmasi Kehadiran Mahasiswa</h4>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                Anda dapat melihat badge hijau (<em>Akan Hadir</em>), kuning (<em>Izin</em>), atau abu-abu (<em>Belum Respon</em>) untuk memastikan kesiapan mahasiswa sebelum sesi dimulai.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: TANYA & JAWAB (FAQ) -->
                <div x-show="activeGuideTab === 'faq'" class="space-y-3.5">
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/40 space-y-1.5">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="text-orange-500 font-mono font-bold">Q:</span>
                            Di mana saya bisa melihat berkas naskah yang dikirim mahasiswa?
                        </h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 pl-5 leading-relaxed">
                            <strong class="text-emerald-600 dark:text-emerald-400">Jawab:</strong> Pada mode Tabel, klik baris mahasiswa tersebut. Panel detail akan terbuka dan menampilkan tautan berkas dokumen naskah yang diunggah mahasiswa.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/40 space-y-1.5">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="text-orange-500 font-mono font-bold">Q:</span>
                            Mengapa progres bimbingan mahasiswa belum bertambah setelah kami selesai berdiskusi?
                        </h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 pl-5 leading-relaxed">
                            <strong class="text-emerald-600 dark:text-emerald-400">Jawab:</strong> Dosen perlu mengeklik tombol <strong>"Selesaikan"</strong> pada sesi tersebut dan memasukkan catatan hasil bimbingan. Setelah tersimpan, statusnya berubah menjadi selesai dan progres bimbingan mahasiswa otomatis bertambah.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/40 space-y-1.5">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="text-orange-500 font-mono font-bold">Q:</span>
                            Bagaimana jika saya berhalangan hadir pada jam yang diajukan mahasiswa?
                        </h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 pl-5 leading-relaxed">
                            <strong class="text-emerald-600 dark:text-emerald-400">Jawab:</strong> Klik tombol <strong>"Reschedule"</strong> (Ubah Jadwal). Anda dapat memindahkan tanggal atau jam pelaksanaan bimbingan ke waktu lain, dan mahasiswa akan menerima pemberitahuan WhatsApp secara otomatis.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/40 space-y-1.5">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="text-orange-500 font-mono font-bold">Q:</span>
                            Apakah mode tampilan (Tabel / Kartu / Kalender) saya berpengaruh ke dosen lain?
                        </h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 pl-5 leading-relaxed">
                            <strong class="text-emerald-600 dark:text-emerald-400">Jawab:</strong> Tidak sama sekali. Pilihan tampilan tersimpan khusus di perangkat peramban Anda masing-masing secara independen.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-slate-100 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 flex items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>Panduan ini dapat dibuka kembali kapan saja via tombol di atas</span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="openGuideModal = false"
                            class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs hover:shadow-sm cursor-pointer">
                        Saya Paham, Tutup
                    </button>
                </div>
            </div>

        </div>
    </div>
</template>

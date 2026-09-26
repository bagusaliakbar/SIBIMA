<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Yudisium & SKL', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6">

        @if(!$isEligible)
            <!-- State Belum Memenuhi Syarat -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 sm:p-10 border border-slate-200/80 dark:border-slate-700/80 shadow-xs text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/40 flex items-center justify-center mx-auto text-amber-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="max-w-xl mx-auto space-y-2">
                    <h3 class="text-xl font-black text-slate-800 dark:text-slate-100">Portal Yudisium & Bebas Tanggungan Belum Aktif</h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Tahapan ini akan terbuka secara otomatis setelah Anda menyelesaikan <strong>Sidang Skripsi</strong> dan seluruh <strong>Revisi Sidang</strong> telah disetujui (ACC) oleh seluruh dosen penguji.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('student-defense-revisions.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold shadow-md shadow-orange-600/20 transition-all">
                        <span>Cek Status Revisi Sidang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        @else
            <!-- Hero Status Banner -->
            @if($graduation && $graduation->status === 'approved')
                <!-- Status Lulus & SKL Siap -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border-2 border-emerald-500/80 dark:border-emerald-500/60 shadow-lg shadow-emerald-500/5 relative overflow-hidden">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-3 max-w-2xl">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 rounded-full text-xs font-black tracking-wide border border-emerald-200 dark:border-emerald-800/80">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span>Bebas Tanggungan Terverifikasi & SKL Diterbitkan</span>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-slate-100 tracking-tight">
                                Selamat, Anda Resmi Lulus! 🎓
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                                Seluruh berkas akhir dan bebas tanggungan Anda telah disetujui oleh Program Studi. Surat Keterangan Lulus (SKL) resmi dengan QR Code verifikasi telah diterbitkan dan dapat diunduh sekarang.
                            </p>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4 pt-1 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700/60 font-mono font-bold text-slate-700 dark:text-slate-200">
                                    No. SKL: <span class="text-emerald-600 dark:text-emerald-400">{{ $graduation->skl_number }}</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-200">
                                    Yudisium: <strong class="text-slate-900 dark:text-white">{{ $graduation->formatted_graduation_date }}</strong>
                                </span>
                                @if($graduation->predicate)
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 font-bold text-emerald-700 dark:text-emerald-300">
                                        Predikat: {{ $graduation->predicate }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('graduations.download-skl', $graduation) }}" target="_blank" class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all hover:scale-105 active:scale-95 cursor-pointer">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Unduh SKL Digital (PDF)</span>
                            </a>
                        </div>
                    </div>
                </div>
            @elseif($graduation && $graduation->status === 'rejected')
                <!-- Status Ditolak / Perbaikan -->
                <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50 rounded-3xl p-6 sm:p-8 text-rose-800 dark:text-rose-200 space-y-3">
                    <div class="flex items-center gap-2 text-rose-700 dark:text-rose-300 font-bold text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Perbaikan Berkas Bebas Tanggungan Diperlukan</span>
                    </div>
                    <p class="text-xs sm:text-sm text-rose-700 dark:text-rose-300 leading-relaxed">
                        Catatan dari Program Studi: <strong>"{{ $graduation->rejection_reason }}"</strong>. Silakan periksa kembali berkas yang Anda unggah dan kirimkan ulang melalui formulir di bawah ini.
                    </p>
                </div>
            @elseif($graduation && $graduation->final_thesis_file)
                <!-- Status Menunggu Verifikasi -->
                <div class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/50 rounded-3xl p-6 sm:p-8 text-blue-800 dark:text-blue-200 space-y-2">
                    <div class="flex items-center gap-2 text-blue-700 dark:text-blue-300 font-bold text-base">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Berkas Dalam Proses Verifikasi Program Studi</span>
                    </div>
                    <p class="text-xs sm:text-sm text-blue-600 dark:text-blue-400 leading-relaxed">
                        Pengajuan berkas bebas tanggungan Anda telah diterima dan sedang diperiksa oleh BAAK / Program Studi. Surat Keterangan Lulus (SKL) akan diterbitkan setelah seluruh checklist terverifikasi.
                    </p>
                </div>
            @else
                <!-- Pengantar Baru -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-2">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">
                        Pemberkasan Bebas Tanggungan & Pengajuan SKL
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Langkah terakhir sebelum wisuda: Unggah naskah skripsi final lengkap yang sudah disahkan, artikel jurnal ilmiah, dan lengkapi penyerahan berkas fisik (hardcover & CD) untuk mendapatkan <strong>Surat Keterangan Lulus (SKL) Digital</strong> resmi.
                    </p>
                </div>
            @endif

            <!-- 2-Columns: Clearance Checklist Tracker & Upload Form -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Clearance Checklist Progress -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-5">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">Checklist Bebas Tanggungan</h4>
                            <span class="text-xs font-bold text-slate-400 font-mono">{{ $graduation ? $graduation->clearance_progress_percentage : 0 }}%</span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full bg-emerald-500 transition-all duration-500" style="width: {{ $graduation ? $graduation->clearance_progress_percentage : 0 }}%"></div>
                        </div>

                        <div class="space-y-3.5 pt-2">
                            <!-- 1. Hardcover -->
                            <div class="p-3.5 rounded-2xl border transition-all flex items-start gap-3 {{ $graduation && $graduation->hardcover_collected ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400' }}">
                                <div class="mt-0.5 w-5 h-5 rounded-full flex items-center justify-center shrink-0 {{ $graduation && $graduation->hardcover_collected ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }}">
                                    @if($graduation && $graduation->hardcover_collected)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <span class="text-[10px] font-bold">1</span>
                                    @endif
                                </div>
                                <div class="text-xs space-y-0.5">
                                    <p class="font-bold text-slate-800 dark:text-slate-200">Hardcover Skripsi</p>
                                    <p class="text-[11px] {{ $graduation && $graduation->hardcover_collected ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">
                                        {{ $graduation && $graduation->hardcover_collected ? 'Sudah diserahkan ke BAAK' : 'Wajib diserahkan ke BAAK' }}
                                    </p>
                                </div>
                            </div>

                            <!-- 2. Bebas Perpustakaan -->
                            <div class="p-3.5 rounded-2xl border transition-all flex items-start gap-3 {{ $graduation && $graduation->library_clearance ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400' }}">
                                <div class="mt-0.5 w-5 h-5 rounded-full flex items-center justify-center shrink-0 {{ $graduation && $graduation->library_clearance ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }}">
                                    @if($graduation && $graduation->library_clearance)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <span class="text-[10px] font-bold">2</span>
                                    @endif
                                </div>
                                <div class="text-xs space-y-0.5">
                                    <p class="font-bold text-slate-800 dark:text-slate-200">Bebas Perpustakaan</p>
                                    <p class="text-[11px] {{ $graduation && $graduation->library_clearance ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">
                                        {{ $graduation && $graduation->library_clearance ? 'Bebas pinjaman pustaka' : 'Tidak ada pinjaman buku' }}
                                    </p>
                                </div>
                            </div>

                            <!-- 3. Bebas Lab Komputer -->
                            <div class="p-3.5 rounded-2xl border transition-all flex items-start gap-3 {{ $graduation && $graduation->lab_clearance ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400' }}">
                                <div class="mt-0.5 w-5 h-5 rounded-full flex items-center justify-center shrink-0 {{ $graduation && $graduation->lab_clearance ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }}">
                                    @if($graduation && $graduation->lab_clearance)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <span class="text-[10px] font-bold">3</span>
                                    @endif
                                </div>
                                <div class="text-xs space-y-0.5">
                                    <p class="font-bold text-slate-800 dark:text-slate-200">Bebas Laboratorium</p>
                                    <p class="text-[11px] {{ $graduation && $graduation->lab_clearance ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">
                                        {{ $graduation && $graduation->lab_clearance ? 'Bebas tanggungan lab' : 'Bebas pinjaman alat lab' }}
                                    </p>
                                </div>
                            </div>

                            <!-- 4. CD / Repositori -->
                            <div class="p-3.5 rounded-2xl border transition-all flex items-start gap-3 {{ $graduation && $graduation->cd_or_repository_collected ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50/70 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400' }}">
                                <div class="mt-0.5 w-5 h-5 rounded-full flex items-center justify-center shrink-0 {{ $graduation && $graduation->cd_or_repository_collected ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }}">
                                    @if($graduation && $graduation->cd_or_repository_collected)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <span class="text-[10px] font-bold">4</span>
                                    @endif
                                </div>
                                <div class="text-xs space-y-0.5">
                                    <p class="font-bold text-slate-800 dark:text-slate-200">CD Program / Repositori</p>
                                    <p class="text-[11px] {{ $graduation && $graduation->cd_or_repository_collected ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">
                                        {{ $graduation && $graduation->cd_or_repository_collected ? 'CD & berkas lengkap' : 'Penyerahan CD skripsi & kode' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-800/40 text-[11px] text-amber-800 dark:text-amber-300 leading-relaxed">
                            💡 <strong>Catatan:</strong> Verifikasi centang di atas dilakukan oleh Admin BAAK / Kaprodi setelah mahasiswa menyerahkan berkas fisik langsung ke kampus.
                        </div>
                    </div>
                </div>

                <!-- Right Column: Submission Form (Google Drive Links) -->
                <div class="lg:col-span-2" x-data="{
                    batchFolderUrl: '',
                    thesisUrl: '{{ old('final_thesis_file', $graduation->final_thesis_file ?? '') }}',
                    journalUrl: '{{ old('journal_article_file', $graduation->journal_article_file ?? '') }}',
                    plagiarismUrl: '{{ old('plagiarism_file', $graduation->plagiarism_file ?? '') }}',
                    applyBatchFolder() {
                        if (!this.batchFolderUrl.trim()) return;
                        const url = this.batchFolderUrl.trim();
                        if (!this.thesisUrl) this.thesisUrl = url;
                        if (!this.journalUrl) this.journalUrl = url;
                        if (!this.plagiarismUrl) this.plagiarismUrl = url;
                    }
                }">
                    <form action="{{ route('student.graduation.store') }}" method="POST" class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-6">
                        @csrf

                        <div class="border-b border-slate-100 dark:border-slate-700/60 pb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-800 dark:text-slate-100">Tautan Berkas Akhir Skripsi (Google Drive)</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Masukkan tautan Google Drive dokumen final yang telah ditandatangani dan disahkan.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Info Banner: Google Drive Share Permission -->
                        <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-800/40 text-blue-900 dark:text-blue-300 text-xs flex items-start gap-3">
                            <div class="p-1.5 rounded-lg bg-blue-500 text-white shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="space-y-1">
                                <p class="font-bold">Penting: Pengaturan Akses Google Drive</p>
                                <p class="text-[11px] leading-relaxed text-blue-800 dark:text-blue-300/90">
                                    Pastikan hak akses tautan Google Drive Anda diatur ke <strong>"Siapa saja yang memiliki link" (Anyone with the link can view)</strong> agar tim Program Studi / BAAK dapat memeriksa dokumen tanpa kendala izin akses.
                                </p>
                            </div>
                        </div>

                        <!-- Smart Batch Folder Filler Banner -->
                        <div class="p-4 bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 dark:from-slate-900 dark:via-slate-800/80 dark:to-slate-900 border border-orange-200/80 dark:border-slate-700 rounded-2xl shadow-2xs">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="p-2 bg-orange-600 text-white rounded-xl shadow-xs shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">💡 Punya 1 Folder Google Drive untuk Semua Berkas?</h5>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Tempelkan link folder Google Drive Anda untuk mengisi kolom sekaligus.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <input type="url" 
                                           x-model="batchFolderUrl" 
                                           placeholder="https://drive.google.com/drive/folders/..." 
                                           class="py-1.5 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 w-full sm:w-60 shadow-2xs">
                                    <button type="button" 
                                            @click="applyBatchFolder()" 
                                            class="px-3 py-1.5 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded-xl transition-all shadow-xs shrink-0 active:scale-95 cursor-pointer">
                                        Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 1. Naskah Skripsi Final -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Tautan Google Drive Naskah Skripsi Lengkap Final <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Naskah lengkap dari Cover sampai Lampiran yang sudah dibubuhi lembar pengesahan tanda tangan asli / TTE.
                            </p>
                            
                            <div class="relative flex items-center">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </span>
                                <input type="url" 
                                       name="final_thesis_file" 
                                       x-model="thesisUrl" 
                                       placeholder="https://drive.google.com/file/d/... atau link folder" 
                                       required 
                                       class="w-full pl-9 pr-24 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                
                                <template x-if="thesisUrl">
                                    <a :href="thesisUrl" target="_blank" class="absolute right-2 px-3 py-1 bg-slate-200 dark:bg-slate-700 hover:bg-orange-500 hover:text-white text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-bold transition-all">
                                        Tes Link
                                    </a>
                                </template>
                            </div>
                            
                            @error('final_thesis_file')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 2. Naskah Artikel Jurnal -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Tautan Google Drive Artikel Jurnal Ilmiah <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Ringkasan naskah skripsi dalam format artikel jurnal ilmiah siap publikasi (template jurnal prodi).
                            </p>
                            
                            <div class="relative flex items-center">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </span>
                                <input type="url" 
                                       name="journal_article_file" 
                                       x-model="journalUrl" 
                                       placeholder="https://drive.google.com/file/d/... atau link folder" 
                                       required 
                                       class="w-full pl-9 pr-24 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                
                                <template x-if="journalUrl">
                                    <a :href="journalUrl" target="_blank" class="absolute right-2 px-3 py-1 bg-slate-200 dark:bg-slate-700 hover:bg-orange-500 hover:text-white text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-bold transition-all">
                                        Tes Link
                                    </a>
                                </template>
                            </div>
                            
                            @error('journal_article_file')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 3. Bukti Uji Plagiasi / Turnitin (Opsional) -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Tautan Google Drive Bukti Uji Plagiasi / Turnitin (Opsional)
                            </label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Lembar sertifikat / hasil cek similarity Turnitin resmi yang telah disetujui.
                            </p>
                            
                            <div class="relative flex items-center">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </span>
                                <input type="url" 
                                       name="plagiarism_file" 
                                       x-model="plagiarismUrl" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full pl-9 pr-24 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                
                                <template x-if="plagiarismUrl">
                                    <a :href="plagiarismUrl" target="_blank" class="absolute right-2 px-3 py-1 bg-slate-200 dark:bg-slate-700 hover:bg-orange-500 hover:text-white text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-bold transition-all">
                                        Tes Link
                                    </a>
                                </template>
                            </div>
                            
                            @error('plagiarism_file')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 4. Link Publikasi OJS / Repositori -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Tautan Publikasi Jurnal / Repositori Online (Opsional)
                            </label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Tautan artikel yang telah dipublikasikan di OJS/jurnal ilmiah atau repositori universitas.
                            </p>
                            <input type="url" name="publication_link" value="{{ old('publication_link', $graduation->publication_link ?? '') }}" placeholder="https://ejournal.unsub.ac.id/..." class="w-full text-xs font-mono rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500">
                            @error('publication_link')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 5. Catatan Mahasiswa -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Catatan Tambahan (Opsional)
                            </label>
                            <textarea name="student_notes" rows="3" placeholder="Tuliskan keterangan bila ada dokumen fisik yang sudah diserahkan di BAAK / Prodi..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500">{{ old('student_notes', $graduation->student_notes ?? '') }}</textarea>
                            @error('student_notes')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-700/60">
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-orange-600/25 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Simpan Tautan & Ajukan Verifikasi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

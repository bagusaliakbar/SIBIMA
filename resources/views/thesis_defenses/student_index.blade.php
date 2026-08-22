<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pendaftaran Sidang Skripsi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        @if($isEligible)
            <!-- Physical Documents Notice -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-md p-6 mb-6">
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg mr-4 shadow-sm">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-blue-800 dark:text-blue-200 uppercase tracking-widest mb-3">Penting: Berkas Fisik (Penyerahan ke BAAK)</h3>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mb-4 font-medium">Selain mengunggah berkas digital di bawah ini, Anda wajib mengumpulkan berkas fisik berikut langsung ke kantor BAAK:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Dokumen Skripsi:</strong> Cetak rangkap 3 (Khusus jika pelaksanaan Sidang dilakukan secara Offline).</span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Formulir Pendaftaran Sidang:</strong> Wajib sudah ditandatangani Dosen Pembimbing dan dibubuhi materai.</span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span>
                                    <strong>Pas Photo Hitam Putih:</strong> Terbaru, dicetak di kertas DOP.<br>
                                    <span class="inline-block mt-1 font-bold bg-blue-100 dark:bg-blue-800 px-2 py-0.5 rounded">2x3 (5 lembar)</span>
                                    <span class="inline-block mt-1 font-bold bg-blue-100 dark:bg-blue-800 px-2 py-0.5 rounded">3x4 (10 lembar)</span>
                                    <span class="inline-block mt-1 font-bold bg-blue-100 dark:bg-blue-800 px-2 py-0.5 rounded">4x6 (10 lembar)</span>
                                    <p class="mt-2 opacity-80 italic text-[10px]">Ketentuan: Pria & Wanita memakai baju putih hitam, berdasi, berjas hitam. Bagi wanita berhijab menggunakan kerudung warna hitam.</p>
                                </span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Map:</strong> Map Plastik warna Biru (2 buah) dan Map Kertas (2 buah).</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        @if(!$isEligible)
            <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 p-8 text-center">
                <div class="w-20 h-20 bg-amber-50 dark:bg-amber-900/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-100 dark:border-amber-800/50">
                    <svg class="w-10 h-10 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Menu Belum Aktif</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                    Anda dapat mengakses menu pengajuan sidang skripsi jika **kedua dosen pembimbing** sudah memberikan ACC Sidang pada jadwal bimbingan Anda.
                </p>
                <div class="flex justify-center gap-4">
                    <div class="flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900 rounded border {{ ($thesis->acc_sidang_p1 ?? false) ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500' }} text-xs font-bold uppercase">
                        Pembimbing 1: {{ ($thesis->acc_sidang_p1 ?? false) ? 'Sudah ACC' : 'Belum ACC' }}
                    </div>
                    <div class="flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900 rounded border {{ ($thesis->acc_sidang_p2 ?? false) ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500' }} text-xs font-bold uppercase">
                        Pembimbing 2: {{ ($thesis->acc_sidang_p2 ?? false) ? 'Sudah ACC' : 'Belum ACC' }}
                    </div>
                </div>
            </div>
        @elseif($application)
            <!-- Status Application -->
            <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 {{ $application->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-900/10' : ($application->status === 'rejected' ? 'bg-red-50 dark:bg-red-900/10' : 'bg-amber-50 dark:bg-amber-900/10') }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold {{ $application->status === 'approved' ? 'text-emerald-800 dark:text-emerald-400' : ($application->status === 'rejected' ? 'text-red-800 dark:text-red-400' : 'text-amber-800 dark:text-amber-400') }}">
                                Status Pengajuan Sidang: {{ strtoupper($application->status) }}
                            </h3>
                            <p class="text-sm {{ $application->status === 'approved' ? 'text-emerald-600 dark:text-emerald-500/70' : ($application->status === 'rejected' ? 'text-red-600 dark:text-red-500/70' : 'text-amber-600 dark:text-amber-500/70') }} mt-1">
                                Diajukan pada {{ $application->created_at->locale('id')->translatedFormat('d M Y • H:i') }}
                            </p>
                        </div>
                        <div class="px-4 py-2 rounded-full font-bold text-xs uppercase tracking-widest border {{ $application->status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : ($application->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800') }}">
                            {{ $application->status }}
                        </div>
                    </div>
                </div>
                
                @if($application->admin_feedback)
                    <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                        <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Catatan Admin:</h4>
                        <p class="text-sm text-slate-700 dark:text-slate-300 italic">"{{ $application->admin_feedback }}"</p>
                    </div>
                @endif
                <div class="p-6">
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4 uppercase tracking-widest">Detail Berkas Persyaratan:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $fileLabels = [
                                'file_formulir' => 'Formulir Pendaftaran Sidang',
                                'file_transkrip' => 'Scan Transkrip Nilai Akhir',
                                'file_acc_pembimbing' => 'Scan Bukti ACC Pembimbing',
                                'file_logbook' => 'Scan Kartu Bimbingan / Logbook',
                                'file_pembayaran' => 'Scan Bukti Pembayaran Sidang',
                                'file_skripsi' => 'Dokumen Soft File Skripsi',
                                'file_ktm' => 'Scan KTM',
                                'file_pkkmb_univ' => 'Scan Sertifikat PKKMB Univ',
                                'file_pkkmb_fak' => 'Scan Sertifikat PKKMB Fak',
                                'file_makrab' => 'Scan Sertifikat Makrab',
                                'file_cisco' => 'Scan Sertifikat Cisco',
                                'file_workshop' => 'Scan Sertifikat Workshop',
                                'file_organisasi' => 'Scan Sertifikat / Surat Aktif Organisasi',
                                'file_toefl' => 'Scan Sertifikat TOEFL',
                                'file_kewirausahaan' => 'Scan Sertifikat Wirausaha',
                                'file_tahsin' => 'Scan Sertifikat Tahsin',
                                'file_komputer' => 'Scan Sertifikat Komputer',
                                'file_perpus_pinjam' => 'Scan Bebas Pinjam Perpus',
                                'file_perpus_sumbang' => 'Scan Sumbang Buku Perpus',
                                'file_ijazah' => 'Scan Ijazah SMA/SMK',
                            ];
                        @endphp

                        @foreach($fileLabels as $field => $label)
                            <div class="relative p-3 border rounded-xl transition-all {{ isset($application->file_reviews[$field]['status']) && $application->file_reviews[$field]['status'] === 'rejected' ? 'border-rose-200 bg-rose-50/30 dark:bg-rose-900/10 dark:border-rose-800' : 'border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                                <div class="flex items-start">
                                    <div class="p-2 bg-slate-100 dark:bg-slate-900 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-[10px] font-bold text-slate-700 dark:text-slate-200 truncate">{{ $label }}</p>
                                            @if(isset($application->file_reviews[$field]['status']))
                                                <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded flex-shrink-0 {{ $application->file_reviews[$field]['status'] === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                    {{ $application->file_reviews[$field]['status'] === 'approved' ? 'OK' : 'REJECT' }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-1 flex items-center gap-2">
                                            <a href="{{ $application->$field }}" target="_blank" class="text-[9px] font-bold text-indigo-600 hover:underline">Buka Link Dokumen</a>
                                        </div>
                                        @if(isset($application->file_reviews[$field]['note']) && $application->file_reviews[$field]['note'])
                                            <div class="mt-2 p-1.5 bg-rose-100/50 dark:bg-rose-900/30 rounded border border-rose-200/50 dark:border-rose-800/50">
                                                <p class="text-[9px] text-rose-700 dark:text-rose-400 font-medium italic leading-tight">"{{ $application->file_reviews[$field]['note'] }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($application->status === 'rejected')
                        <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-3 mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/50">
                                <div class="p-2 bg-amber-100 dark:bg-amber-800 rounded-lg">
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-xs text-amber-700 dark:text-amber-300 font-bold">Silakan unggah ulang berkas yang ditolak (<span class="text-rose-600">REJECT</span>) di bawah ini:</p>
                            </div>

                            <form action="{{ route('thesis-defense-applications.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    @foreach($fileLabels as $field => $label)
                                        @if(isset($application->file_reviews[$field]['status']) && $application->file_reviews[$field]['status'] === 'rejected')
                                            <div class="space-y-2">
                                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ $label }} <span class="text-red-500">*</span></label>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold tracking-wide mb-2">
                                                    Masukkan link dari Google Drive.
                                                </p>
                                                <input type="url" name="{{ $field }}" value="{{ old($field, $application->$field ?? '') }}" placeholder="https://drive.google.com/..." required class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900 shadow-sm py-2 px-3">
                                                @error($field) <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="mt-8 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-orange-600 text-white font-bold rounded-md hover:bg-orange-700 transition-colors shadow-md">
                                        Perbarui Pengajuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Upload Form -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden" 
                 x-data="{
                    activeTab: 'all',
                    batchFolderUrl: '',
                    files: {
                        file_formulir: '{{ old('file_formulir', $application->file_formulir ?? '') }}',
                        file_pembayaran: '{{ old('file_pembayaran', $application->file_pembayaran ?? '') }}',
                        file_ktm: '{{ old('file_ktm', $application->file_ktm ?? '') }}',
                        file_ijazah: '{{ old('file_ijazah', $application->file_ijazah ?? '') }}',
                        file_transkrip: '{{ old('file_transkrip', $application->file_transkrip ?? '') }}',
                        file_acc_pembimbing: '{{ old('file_acc_pembimbing', $application->file_acc_pembimbing ?? '') }}',
                        file_logbook: '{{ old('file_logbook', $application->file_logbook ?? '') }}',
                        file_skripsi: '{{ old('file_skripsi', $application->file_skripsi ?? '') }}',
                        file_pkkmb_univ: '{{ old('file_pkkmb_univ', $application->file_pkkmb_univ ?? '') }}',
                        file_pkkmb_fak: '{{ old('file_pkkmb_fak', $application->file_pkkmb_fak ?? '') }}',
                        file_makrab: '{{ old('file_makrab', $application->file_makrab ?? '') }}',
                        file_cisco: '{{ old('file_cisco', $application->file_cisco ?? '') }}',
                        file_workshop: '{{ old('file_workshop', $application->file_workshop ?? '') }}',
                        file_organisasi: '{{ old('file_organisasi', $application->file_organisasi ?? '') }}',
                        file_toefl: '{{ old('file_toefl', $application->file_toefl ?? '') }}',
                        file_kewirausahaan: '{{ old('file_kewirausahaan', $application->file_kewirausahaan ?? '') }}',
                        file_tahsin: '{{ old('file_tahsin', $application->file_tahsin ?? '') }}',
                        file_komputer: '{{ old('file_komputer', $application->file_komputer ?? '') }}',
                        file_perpus_pinjam: '{{ old('file_perpus_pinjam', $application->file_perpus_pinjam ?? '') }}',
                        file_perpus_sumbang: '{{ old('file_perpus_sumbang', $application->file_perpus_sumbang ?? '') }}'
                    },
                    applyBatchFolderUrl() {
                        if (!this.batchFolderUrl || !this.batchFolderUrl.trim().startsWith('http')) {
                            alert('Silakan masukkan link Google Drive yang valid (dimulai dengan https://)');
                            return;
                        }
                        const url = this.batchFolderUrl.trim();
                        Object.keys(this.files).forEach(k => {
                            this.files[k] = url;
                        });
                    },
                    async pasteSingle(field) {
                        try {
                            const text = await navigator.clipboard.readText();
                            if (text) {
                                this.files[field] = text.trim();
                            }
                        } catch (e) {
                            alert('Gunakan tombol pintasan Ctrl + V untuk menempelkan link.');
                        }
                    },
                    isDrive(url) {
                        return url && url.includes('drive.google.com');
                    },
                    countFilled(keys) {
                        return keys.filter(k => this.files[k] && this.files[k].trim() !== '').length;
                    },
                    get totalFilled() {
                        return Object.values(this.files).filter(v => v && v.trim() !== '').length;
                    },
                    get totalCount() {
                        return Object.keys(this.files).length;
                    },
                    get percentFilled() {
                        return Math.round((this.totalFilled / this.totalCount) * 100);
                    }
                 }">
                
                <!-- Header Card -->
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">Form Pengajuan Sidang Skripsi</h3>
                                <span class="px-2.5 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 text-[10px] font-black rounded-lg uppercase tracking-widest border border-indigo-200 dark:border-indigo-800/50">
                                    {{ $activeWave->name ?? 'Gelombang Aktif' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Lengkapi 20 berkas persyaratan di bawah ini dengan tautan Google Drive yang dapat diakses publik.</p>
                        </div>
                        
                        @if($template)
                            <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xs">
                                <div class="p-2 bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Unduh Formulir</div>
                                    <a href="{{ route('download.private', ['path' => $template->file_path]) }}" target="_blank" class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline">
                                        {{ $template->title }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <form action="{{ route('thesis-defense-applications.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- 1. Smart Batch Folder Filler Banner -->
                    <div class="p-4 bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 dark:from-slate-900 dark:via-slate-800/80 dark:to-slate-900 border border-orange-200/80 dark:border-slate-700 rounded-2xl shadow-2xs">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                            <div class="flex items-start sm:items-center gap-3">
                                <div class="p-2.5 bg-orange-600 text-white rounded-xl shadow-xs shrink-0 mt-0.5 sm:mt-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">💡 Punya 1 Folder Google Drive untuk Semua Berkas?</h4>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">Jika seluruh scan berkas Anda berada di dalam satu folder Google Drive, tempelkan link foldernya di sini untuk mengisi 20 kolom sekaligus secara instan.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 w-full lg:w-auto">
                                <input type="url" 
                                       x-model="batchFolderUrl" 
                                       placeholder="https://drive.google.com/drive/folders/..." 
                                       class="py-2 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 w-full lg:w-72 shadow-2xs">
                                <button type="button" 
                                        @click="applyBatchFolderUrl()" 
                                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-xs shrink-0 active:scale-95 cursor-pointer">
                                    Terapkan ke Semua
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Real-Time Progress Bar -->
                    <div class="p-4 bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                <span>📊 Status Kelengkapan Berkas:</span>
                                <span class="font-black text-orange-600 dark:text-orange-400" x-text="totalFilled + ' dari ' + totalCount + ' Berkas Terisi'"></span>
                            </span>
                            <span class="text-xs font-black" :class="totalFilled === totalCount ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400'" x-text="percentFilled + '%'"></span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-500 rounded-full" 
                                 :class="totalFilled === totalCount ? 'bg-emerald-500' : 'bg-gradient-to-r from-orange-500 to-amber-500'" 
                                 :style="'width: ' + percentFilled + '%'"></div>
                        </div>
                    </div>

                    @php
                        $categories = [
                            'utama' => [
                                'title' => 'Berkas Utama & Identitas',
                                'badge' => '📑',
                                'keys' => ['file_formulir', 'file_pembayaran', 'file_ktm', 'file_ijazah'],
                                'fields' => [
                                    'file_formulir' => ['label' => '1. Formulir Pendaftaran Sidang', 'desc' => 'Scan formulir pendaftaran yang telah diisi & ditandatangani.'],
                                    'file_pembayaran' => ['label' => '5. Scan Bukti Pembayaran Sidang', 'desc' => 'Kwitansi / bukti transfer pembayaran pendaftaran sidang.'],
                                    'file_ktm' => ['label' => '7. Scan Kartu Tanda Mahasiswa (KTM)', 'desc' => 'Scan KTM asli yang masih berlaku.'],
                                    'file_ijazah' => ['label' => '20. Scan Ijazah SMA/SMK', 'desc' => 'Scan legalisir / asli ijazah pendidikan terakhir.'],
                                ]
                            ],
                            'skripsi' => [
                                'title' => 'Skripsi & Akademik',
                                'badge' => '🎓',
                                'keys' => ['file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_skripsi'],
                                'fields' => [
                                    'file_transkrip' => ['label' => '2. Scan Transkrip Nilai Akhir', 'desc' => 'Transkrip nilai akademik lengkap semester 1 s/d akhir.'],
                                    'file_acc_pembimbing' => ['label' => '3. Scan Bukti ACC Pembimbing', 'desc' => 'Lembar persetujuan sidang dari Pembimbing 1 & Pembimbing 2.'],
                                    'file_logbook' => ['label' => '4. Scan Kartu Bimbingan / Logbook', 'desc' => 'Kartu kendali bimbingan / lembar logbook yang telah divalidasi.'],
                                    'file_skripsi' => ['label' => '6. Dokumen Soft File Skripsi', 'desc' => 'Draft lengkap naskah skripsi (Cover hingga Lampiran).'],
                                ]
                            ],
                            'sertifikat' => [
                                'title' => 'Sertifikat & Kompetensi',
                                'badge' => '🏆',
                                'keys' => ['file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab', 'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan', 'file_tahsin', 'file_komputer'],
                                'fields' => [
                                    'file_pkkmb_univ' => ['label' => '8. Scan Sertifikat PKKMB Univ', 'desc' => 'Sertifikat Pengenalan Kehidupan Kampus Universitas.'],
                                    'file_pkkmb_fak' => ['label' => '9. Scan Sertifikat PKKMB Fak', 'desc' => 'Sertifikat Pengenalan Kehidupan Kampus Fakultas.'],
                                    'file_makrab' => ['label' => '10. Scan Sertifikat Makrab', 'desc' => 'Sertifikat Malam Keakraban / LKMM.'],
                                    'file_cisco' => ['label' => '11. Scan Sertifikat Cisco IPv6', 'desc' => 'Sertifikat pelatihan jaringan / Cisco IPv6.'],
                                    'file_workshop' => ['label' => '12. Scan Sertifikat Workshop', 'desc' => 'Sertifikat seminar / workshop keilmuan prodi.'],
                                    'file_organisasi' => ['label' => '13. Scan Sertifikat / Surat Aktif Organisasi', 'desc' => 'SK pengurus / surat keterangan aktif ormawa / sertifikat kegiatan.'],
                                    'file_toefl' => ['label' => '14. Scan Sertifikat TOEFL', 'desc' => 'Sertifikat tes kemampuan Bahasa Inggris (TOEFL / sejenis).'],
                                    'file_kewirausahaan' => ['label' => '15. Scan Sertifikat Wirausaha', 'desc' => 'Sertifikat pelatihan / program kewirausahaan.'],
                                    'file_tahsin' => ['label' => '16. Scan Sertifikat Tahsin', 'desc' => 'Sertifikat kelulusan uji kemampuan baca Al-Quran / keagamaan.'],
                                    'file_komputer' => ['label' => '17. Scan Sertifikat Komputer', 'desc' => 'Sertifikat uji kompetensi / sertifikasi keahlian komputer.'],
                                ]
                            ],
                            'perpus' => [
                                'title' => 'Bebas Perpustakaan',
                                'badge' => '📚',
                                'keys' => ['file_perpus_pinjam', 'file_perpus_sumbang'],
                                'fields' => [
                                    'file_perpus_pinjam' => ['label' => '18. Scan Bebas Pinjam Perpus', 'desc' => 'Surat keterangan bebas peminjaman buku perpustakaan.'],
                                    'file_perpus_sumbang' => ['label' => '19. Scan Sumbang Buku Perpus', 'desc' => 'Tanda terima sumbangan buku / karya ilmiah ke perpustakaan.'],
                                ]
                            ]
                        ];
                    @endphp

                    <!-- 3. Category Filter Tabs -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-100 dark:border-slate-700">
                        <button type="button" 
                                @click="activeTab = 'all'" 
                                class="px-3.5 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer"
                                :class="activeTab === 'all' ? 'bg-orange-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'">
                            <span>📋 Semua Kategori</span>
                            <span class="px-1.5 py-0.2 bg-white/20 rounded-md text-[10px]" x-text="totalFilled + '/' + totalCount"></span>
                        </button>

                        @foreach($categories as $catKey => $cat)
                            <button type="button" 
                                    @click="activeTab = '{{ $catKey }}'" 
                                    class="px-3.5 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer"
                                    :class="activeTab === '{{ $catKey }}' ? 'bg-orange-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'">
                                <span>{{ $cat['badge'] }} {{ $cat['title'] }}</span>
                                <span class="px-1.5 py-0.2 rounded-md text-[10px]"
                                      :class="countFilled({{ json_encode($cat['keys']) }}) === {{ count($cat['keys']) }} ? 'bg-emerald-500 text-white font-bold' : (activeTab === '{{ $catKey }}' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300')"
                                      x-text="countFilled({{ json_encode($cat['keys']) }}) + '/{{ count($cat['keys']) }}'"></span>
                            </button>
                        @endforeach
                    </div>

                    <!-- 4. Grouped Input Fields -->
                    <div class="space-y-8">
                        @foreach($categories as $catKey => $cat)
                            <div x-show="activeTab === 'all' || activeTab === '{{ $catKey }}'" x-cloak class="space-y-4">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">{{ $cat['badge'] }}</span>
                                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">{{ $cat['title'] }}</h4>
                                    </div>
                                    <span class="text-xs font-black uppercase tracking-widest px-2 py-0.5 rounded-lg"
                                          :class="countFilled({{ json_encode($cat['keys']) }}) === {{ count($cat['keys']) }} ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                                          x-text="countFilled({{ json_encode($cat['keys']) }}) + ' dari {{ count($cat['keys']) }} Terisi'"></span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($cat['fields'] as $field => $info)
                                        <div class="p-4 bg-slate-50/60 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl transition-all hover:border-orange-300 dark:hover:border-orange-500/40 hover:bg-white dark:hover:bg-slate-900 space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <label for="{{ $field }}" class="block text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">
                                                    {{ $info['label'] }} <span class="text-rose-500">*</span>
                                                </label>
                                                <button type="button" 
                                                        @click="pasteSingle('{{ $field }}')" 
                                                        class="px-2 py-0.5 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 rounded-md text-[10px] font-bold transition-all shadow-2xs cursor-pointer active:scale-95 shrink-0"
                                                        title="Tempel tautan dari clipboard">
                                                    📋 Tempel
                                                </button>
                                            </div>
                                            
                                            <p class="text-[11px] text-slate-400 dark:text-slate-500 leading-tight">{{ $info['desc'] }}</p>

                                            <div class="relative">
                                                <input type="url" 
                                                       name="{{ $field }}" 
                                                       id="{{ $field }}" 
                                                       x-model="files['{{ $field }}']" 
                                                       placeholder="https://drive.google.com/..." 
                                                       {{ isset($application->$field) ? '' : 'required' }} 
                                                       class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-xl border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-800 shadow-2xs py-2 px-3 transition-all">
                                            </div>

                                            <!-- Live Status Indicator -->
                                            <div class="flex items-center justify-between pt-1">
                                                <template x-if="isDrive(files['{{ $field }}'])">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Google Drive Valid</span>
                                                    </span>
                                                </template>
                                                <template x-if="files['{{ $field }}'] && !isDrive(files['{{ $field }}'])">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                                        <span>🔗 Link Terisi</span>
                                                    </span>
                                                </template>
                                                <template x-if="!files['{{ $field }}']">
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Belum diisi</span>
                                                </template>

                                                <template x-if="files['{{ $field }}']">
                                                    <a :href="files['{{ $field }}']" target="_blank" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-0.5">
                                                        <span>Buka Link</span>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    </a>
                                                </template>
                                            </div>
                                            @error($field) <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 pt-6 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            Pastikan seluruh link Google Drive telah disetel ke mode <b class="text-slate-700 dark:text-slate-200">"Anyone with the link can view" (Siapa saja yang memiliki link dapat melihat)</b>.
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md active:scale-95 cursor-pointer">
                            Kirim Pengajuan Sidang
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>

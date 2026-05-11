<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pendaftaran Sidang Skripsi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6">
        {{-- Wave Filter Section --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Gelombang Pelaksanaan</h3>
                    <div class="flex items-center gap-2 mt-0.5 text-[10px] font-bold">
                        <span class="text-slate-400 uppercase tracking-widest">Gelombang Aktif :</span>
                        @if($activeWave)
                            <span class="text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-1.5 rounded">{{ $activeWave->name }}</span>
                        @else
                            <span class="text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 px-1.5 rounded uppercase tracking-tighter">Tidak Ada Gelombang Aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            <form action="{{ route('thesis-defense-applications.index') }}" method="GET" class="flex items-center gap-2">
                <select name="wave_id" onchange="this.form.submit()" 
                        class="pl-4 pr-10 py-2 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm min-w-[200px]">
                    <option value="">Semua Gelombang</option>
                    @foreach($waves as $wave)
                        <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                            {{ $wave->name }} {{ $wave->is_active ? '(Aktif)' : '(Arsip)' }}
                        </option>
                    @endforeach
                </select>
                @if($selectedWaveId)
                    <a href="{{ route('thesis-defense-applications.index') }}" class="p-2 text-slate-400 hover:text-rose-600 transition-colors" title="Clear Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </form>
        </div>
        {{-- Template Management Section --}}
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center">
                        <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Manajemen Formulir Pendaftaran Sidang
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Upload templat formulir sidang skripsi terbaru untuk diunduh mahasiswa.</p>
                </div>
                
                @if($template)
                    <div class="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 rounded-lg">
                        <div class="p-2 bg-white dark:bg-slate-700 rounded shadow-sm">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] font-bold text-emerald-800 dark:text-emerald-200 truncate max-w-[200px]">{{ $template->title }}</div>
                            <div class="text-[9px] text-emerald-600 dark:text-emerald-400 font-medium truncate max-w-[200px]">{{ $template->original_name }}</div>
                        </div>
                        <a href="{{ Storage::url($template->file_path) }}" target="_blank" class="ml-2 p-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        </a>
                    </div>
                @endif

                <div x-data="{ open: false }">
                    <button @click="open = true" class="px-4 py-2 bg-orange-600 text-white text-xs font-bold rounded hover:bg-orange-700 transition-all shadow-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Upload Templat Sidang
                    </button>

                    <!-- Upload Template Modal -->
                    <div x-show="open" x-cloak class="fixed inset-0 z-[110] overflow-y-auto">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-slate-900 bg-opacity-50 dark:bg-opacity-80 transition-opacity" @click="open = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100 dark:border-slate-700">
                                <div class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Upload Templat Sidang</h3>
                                    <button @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                <form action="{{ route('thesis-defense-applications.upload-template') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">Judul Formulir</label>
                                            <input type="text" name="title" required class="block w-full rounded-md border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm focus:ring-orange-500 focus:border-orange-500 shadow-sm text-slate-800 dark:text-slate-100 placeholder:text-slate-400" placeholder="Contoh: Formulir Pendaftaran Sidang Skripsi 2024">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">File Templat (PDF/DOC/DOCX)</label>
                                            <input type="file" name="template_file" required accept=".pdf,.doc,.docx" class="block w-full text-xs text-slate-600 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-orange-50 dark:file:bg-orange-900/20 file:text-orange-700 dark:file:text-orange-500 hover:file:bg-orange-100 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-md">
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                        <button type="button" @click="open = false" class="px-4 py-2 text-slate-600 dark:text-slate-400 text-xs font-bold rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">Batal</button>
                                        <button type="submit" class="px-6 py-2 bg-orange-600 text-white text-xs font-bold rounded hover:bg-orange-700 transition-colors shadow-sm">Simpan Templat</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap w-1/2">MAHASISWA</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center w-1/4">STATUS</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-right w-1/4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($applications as $app)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors align-top">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800 dark:text-slate-100">{{ $app->thesis->student->name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 tracking-tight uppercase">{{ $app->thesis->student->identifier }}</div>
                                    <div class="mt-2 space-y-1">
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500">
                                            <span class="w-3 h-3 rounded-full bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-500 flex items-center justify-center font-bold">1</span>
                                            {{ $app->thesis->pembimbing1->name }}
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500">
                                            <span class="w-3 h-3 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold">2</span>
                                            {{ $app->thesis->pembimbing2->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ $app->status === 'approved' ? 'bg-emerald-600 text-white' : '' }}
                                        {{ $app->status === 'rejected' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                                        {{ $app->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                    ">
                                        {{ $app->status }}
                                    </span>
                                    @if($app->admin_feedback)
                                        <div class="mt-1 text-[9px] text-slate-400 italic max-w-[150px] mx-auto truncate" title="{{ $app->admin_feedback }}">"{{ $app->admin_feedback }}"</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex justify-end items-center gap-2" x-data="{ open: false, showFiles: false }">
                                        <a href="{{ route('thesis-defense-applications.download-zip', $app->id) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 dark:bg-slate-700 rounded text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition-all">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            ZIP
                                        </a>
                                        <button @click="showFiles = true" type="button" class="inline-flex items-center px-3 py-1.5 bg-slate-100 dark:bg-slate-700 rounded text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition-all">
                                            Lihat Berkas (20)
                                        </button>
                                        <button @click="open = true" type="button" class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white rounded text-xs font-bold hover:bg-orange-700 transition-all shadow-sm">
                                            Validasi
                                        </button>
                                        
                                        <!-- Files List Modal -->
                                        <div x-show="showFiles" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
                                            <div class="flex items-center justify-center min-h-screen p-4">
                                                <div class="fixed inset-0 bg-slate-900 bg-opacity-50 transition-opacity" @click="showFiles = false"></div>
                                                <div class="relative bg-white dark:bg-slate-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700 flex flex-col">
                                                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-widest">Berkas Persyaratan: {{ $app->thesis->student->name }}</h3>
                                                        <button @click="showFiles = false" class="text-slate-400 hover:text-slate-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                    <div class="p-6 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        @php
                                                            $fileLabels = [
                                                                'file_formulir' => '1. Formulir Pendaftaran Sidang',
                                                                'file_transkrip' => '2. Scan Transkrip Nilai Akhir',
                                                                'file_acc_pembimbing' => '3. Scan Bukti ACC Pembimbing 1 & 2',
                                                                'file_logbook' => '4. Scan Kartu Bimbingan / Logbook',
                                                                'file_pembayaran' => '5. Scan Bukti Pembayaran Sidang',
                                                                'file_skripsi' => '6. Dokumen Soft File Skripsi',
                                                                'file_ktm' => '7. Scan KTM',
                                                                'file_pkkmb_univ' => '8. Scan Sertifikat PKKMB Universitas',
                                                                'file_pkkmb_fak' => '9. Scan Sertifikat PKKMB Fakultas',
                                                                'file_makrab' => '10. Scan Sertifikat Makrab Himpunan',
                                                                'file_cisco' => '11. Scan Sertifikat Cisco IPv6',
                                                                'file_workshop' => '12. Scan Sertifikat Pelatihan/Workshop',
                                                                'file_organisasi' => '13. Scan Sertifikat Aktif Organisasi',
                                                                'file_toefl' => '14. Scan Sertifikat TOEFL',
                                                                'file_kewirausahaan' => '15. Scan Sertifikat Kewirausahaan',
                                                                'file_tahsin' => '16. Scan Sertifikat Tahsin',
                                                                'file_komputer' => '17. Scan Sertifikat Komputer',
                                                                'file_perpus_pinjam' => '18. Scan Bebas Pinjam Perpus',
                                                                'file_perpus_sumbang' => '19. Scan Sumbang Buku Perpus',
                                                                'file_ijazah' => '20. Scan Ijazah SMA/SMK',
                                                            ];
                                                        @endphp
                                                        @foreach($fileLabels as $field => $label)
                                                            <a href="{{ Storage::url($app->$field) }}" target="_blank" class="flex items-center p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors group">
                                                                <svg class="w-5 h-5 text-slate-400 group-hover:text-orange-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">{{ $label }}</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Validation Modal -->
                                        <div x-show="open" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
                                            <div class="flex items-center justify-center min-h-screen p-4">
                                                <div class="fixed inset-0 bg-slate-900 bg-opacity-50 transition-opacity" @click="open = false"></div>
                                                <div class="relative bg-white dark:bg-slate-800 rounded-lg max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700">
                                                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Validasi: {{ $app->thesis->student->name }}</h3>
                                                        <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('thesis-defense-applications.validate', $app->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="p-6 bg-slate-50/50 dark:bg-slate-900/50 space-y-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                                                            <div>
                                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 ml-1">Validasi Per Berkas</label>
                                                                <div class="space-y-3">
                                                                    @php
                                                                        $fileLabels = [
                                                                            'file_formulir' => '1. Formulir Pendaftaran Sidang',
                                                                            'file_transkrip' => '2. Scan Transkrip Nilai Akhir',
                                                                            'file_acc_pembimbing' => '3. Scan Bukti ACC Pembimbing',
                                                                            'file_logbook' => '4. Scan Kartu Bimbingan',
                                                                            'file_pembayaran' => '5. Scan Bukti Pembayaran',
                                                                            'file_skripsi' => '6. Dokumen Soft File Skripsi',
                                                                            'file_ktm' => '7. Scan KTM',
                                                                            'file_pkkmb_univ' => '8. Scan Sertifikat PKKMB Univ',
                                                                            'file_pkkmb_fak' => '9. Scan Sertifikat PKKMB Fak',
                                                                            'file_makrab' => '10. Scan Sertifikat Makrab',
                                                                            'file_cisco' => '11. Scan Sertifikat Cisco',
                                                                            'file_workshop' => '12. Scan Sertifikat Workshop',
                                                                            'file_organisasi' => '13. Scan Sertifikat Organisasi',
                                                                            'file_toefl' => '14. Scan Sertifikat TOEFL',
                                                                            'file_kewirausahaan' => '15. Scan Sertifikat Wirausaha',
                                                                            'file_tahsin' => '16. Scan Sertifikat Tahsin',
                                                                            'file_komputer' => '17. Scan Sertifikat Komputer',
                                                                            'file_perpus_pinjam' => '18. Scan Bebas Pinjam Perpus',
                                                                            'file_perpus_sumbang' => '19. Scan Sumbang Buku Perpus',
                                                                            'file_ijazah' => '20. Scan Ijazah SMA/SMK',
                                                                        ];
                                                                    @endphp
                                                                    @foreach($fileLabels as $field => $label)
                                                                        <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
                                                                            <div class="flex items-center justify-between mb-2">
                                                                                <div class="flex flex-col">
                                                                                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">{{ $label }}</span>
                                                                                    <a href="{{ Storage::url($app->$field) }}" target="_blank" class="text-[9px] text-blue-500 font-bold hover:underline">Lihat File</a>
                                                                                </div>
                                                                                <div class="flex items-center gap-2">
                                                                                    <label class="inline-flex items-center">
                                                                                        <input type="radio" name="file_reviews[{{ $field }}][status]" value="approved" 
                                                                                               {{ !isset($app->file_reviews[$field]['status']) || $app->file_reviews[$field]['status'] === 'approved' ? 'checked' : '' }}
                                                                                               class="w-3 h-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900">
                                                                                        <span class="ml-1.5 text-[10px] font-bold text-emerald-600 uppercase">OK</span>
                                                                                    </label>
                                                                                    <label class="inline-flex items-center ml-2">
                                                                                        <input type="radio" name="file_reviews[{{ $field }}][status]" value="rejected"
                                                                                               {{ isset($app->file_reviews[$field]['status']) && $app->file_reviews[$field]['status'] === 'rejected' ? 'checked' : '' }}
                                                                                               class="w-3 h-3 text-rose-600 focus:ring-rose-500 border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900">
                                                                                        <span class="ml-1.5 text-[10px] font-bold text-rose-600 uppercase">REJECT</span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <input type="text" name="file_reviews[{{ $field }}][note]" 
                                                                                   value="{{ $app->file_reviews[$field]['note'] ?? '' }}"
                                                                                   placeholder="Catatan jika ditolak..."
                                                                                   class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-[10px] focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>

                                                            <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Status Akhir Pengajuan</label>
                                                                <select name="status" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 shadow-sm text-slate-800 dark:text-slate-100 py-3">
                                                                    <option value="approved" {{ $app->status === 'approved' ? 'selected' : '' }}>SETUJUI SEMUA (BERKAS OK)</option>
                                                                    <option value="rejected" {{ $app->status === 'rejected' ? 'selected' : '' }}>TOLAK / PERLU REVISI BERKAS</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div>
                                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Catatan Admin (Global)</label>
                                                                <textarea name="admin_feedback" rows="3" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 shadow-sm placeholder:text-slate-400 text-slate-800 dark:text-slate-100 py-3 italic" placeholder="Contoh: Silakan upload ulang berkas yang ditolak...">{{ $app->admin_feedback }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="px-6 py-4 bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                                            <button type="button" @click="open = false" class="px-4 py-2 text-slate-600 dark:text-slate-400 text-xs font-bold rounded hover:bg-slate-100 transition-colors">Batal</button>
                                                            <button type="submit" class="px-6 py-2 bg-orange-600 text-white text-xs font-bold rounded hover:bg-orange-700 transition-colors shadow-sm">Simpan Keputusan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-20 text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                                        <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-widest">Tidak ada pengajuan masuk</h3>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pendaftaran Seminar', 'route' => null]
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
                                <span><strong>Dokumen Skripsi:</strong> Cetak rangkap 3 (Khusus jika pelaksanaan Seminar dilakukan secara Offline).</span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Formulir Pendaftaran Seminar:</strong> Wajib sudah ditandatangani Dosen Pembimbing dan dibubuhi materai.</span>
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
                    Anda dapat mengakses menu pengajuan seminar jika **kedua dosen pembimbing** sudah memberikan ACC Seminar UP pada jadwal bimbingan Anda.
                </p>
                <div class="flex justify-center gap-4">
                    <div class="flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900 rounded border {{ ($thesis->acc_up_p1 ?? false) ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500' }} text-xs font-bold uppercase">
                        Pembimbing 1: {{ ($thesis->acc_up_p1 ?? false) ? 'Sudah ACC' : 'Belum ACC' }}
                    </div>
                    <div class="flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900 rounded border {{ ($thesis->acc_up_p2 ?? false) ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500' }} text-xs font-bold uppercase">
                        Pembimbing 2: {{ ($thesis->acc_up_p2 ?? false) ? 'Sudah ACC' : 'Belum ACC' }}
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
                                Status Pengajuan: {{ strtoupper($application->status) }}
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
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4">Detail Berkas:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $fileFields = [
                                'file_acc_pembimbing' => ['label' => 'Bukti ACC Pembimbing', 'icon' => 'orange', 'svg' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                'file_pembayaran' => ['label' => 'Bukti Pembayaran', 'icon' => 'emerald', 'svg' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                                'file_kartu_bimbingan' => ['label' => 'Kartu Bimbingan', 'icon' => 'blue', 'svg' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                                'file_skripsi' => ['label' => 'Soft File Skripsi', 'icon' => 'indigo', 'svg' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                                'file_formulir' => ['label' => 'Formulir Seminar', 'icon' => 'pink', 'svg' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z']
                            ];
                        @endphp
                        @foreach($fileFields as $field => $info)
                            <div class="relative p-4 border rounded-xl transition-all {{ isset($application->file_reviews[$field]['status']) && $application->file_reviews[$field]['status'] === 'rejected' ? 'border-rose-200 bg-rose-50/30 dark:bg-rose-900/10 dark:border-rose-800' : 'border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                                <div class="flex items-start">
                                    <div class="p-2 bg-{{ $info['icon'] }}-100 dark:bg-{{ $info['icon'] }}-900/30 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-{{ $info['icon'] }}-600 dark:text-{{ $info['icon'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['svg'] }}"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $info['label'] }}</p>
                                            @if(isset($application->file_reviews[$field]['status']))
                                                <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded {{ $application->file_reviews[$field]['status'] === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                    {{ $application->file_reviews[$field]['status'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-1 flex items-center gap-2">
                                            <a href="{{ Storage::url($application->$field) }}" target="_blank" class="text-[10px] font-bold text-indigo-600 hover:underline">Lihat Berkas</a>
                                        </div>
                                        @if(isset($application->file_reviews[$field]['note']) && $application->file_reviews[$field]['note'])
                                            <div class="mt-2 p-2 bg-rose-100/50 dark:bg-rose-900/30 rounded border border-rose-200/50 dark:border-rose-800/50">
                                                <p class="text-[10px] text-rose-700 dark:text-rose-400 font-medium italic">"{{ $application->file_reviews[$field]['note'] }}"</p>
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

                            <form action="{{ route('seminar-applications.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    @foreach($fileFields as $field => $info)
                                        @if(isset($application->file_reviews[$field]['status']) && $application->file_reviews[$field]['status'] === 'rejected')
                                            <div class="space-y-2">
                                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ $info['label'] }} <span class="text-red-500">*</span></label>
                                                <input type="file" name="{{ $field }}" required class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 dark:file:bg-orange-900/20 file:text-orange-700 dark:file:text-orange-400 hover:file:bg-orange-100 dark:hover:file:bg-orange-900/30 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-md p-1">
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
            <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Form Pengajuan Seminar</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 text-[10px] font-black rounded uppercase tracking-widest border border-indigo-200 dark:border-indigo-800/50">
                                    {{ $activeWave->name ?? 'Gelombang Aktif' }}
                                </span>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Pastikan semua dokumen dalam format PDF atau Gambar (maks 2MB per file), kecuali Soft File Skripsi (maks 10MB).</p>
                            </div>
                        </div>
                        
                        @if($template)
                            <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm">
                                <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Unduh Formulir</div>
                                    <a href="{{ Storage::url($template->file_path) }}" target="_blank" class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 hover:underline">
                                        {{ $template->title }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <form action="{{ route('seminar-applications.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Requirement 1 -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">1. Scan Bukti ACC Pembimbing 1 & 2 <span class="text-red-500">*</span></label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Unggah tangkapan layar atau scan bukti persetujuan dari kedua dosen pembimbing.</p>
                            <input type="file" name="file_acc_pembimbing" required class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 dark:file:bg-orange-900/20 file:text-orange-700 dark:file:text-orange-400 hover:file:bg-orange-100 dark:hover:file:bg-orange-900/30 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-md p-1">
                            @error('file_acc_pembimbing') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Requirement 2 -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">2. Scan Bukti Pembayaran Seminar <span class="text-red-500">*</span></label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Unggah bukti transfer atau kwitansi pembayaran biaya pendaftaran seminar.</p>
                            <input type="file" name="file_pembayaran" required class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 dark:file:bg-orange-900/20 file:text-orange-700 dark:file:text-orange-400 hover:file:bg-orange-100 dark:hover:file:bg-orange-900/30 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-md p-1">
                            @error('file_pembayaran') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Requirement 3 -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">3. Scan Kartu Bimbingan / Logbook <span class="text-red-500">*</span></label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Unggah scan kartu bimbingan yang sudah divalidasi atau logbook bimbingan P1 & P2.</p>
                            <input type="file" name="file_kartu_bimbingan" required class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 dark:file:bg-orange-900/20 file:text-orange-700 dark:file:text-orange-400 hover:file:bg-orange-100 dark:hover:file:bg-orange-900/30 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-md p-1">
                            @error('file_kartu_bimbingan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Requirement 4 -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">4. Dokumen Soft File Skripsi <span class="text-red-500">*</span></label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Unggah naskah skripsi lengkap dalam format PDF atau Word (Maks 10MB).</p>
                            <input type="file" name="file_skripsi" required class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 dark:file:bg-orange-900/20 file:text-orange-700 dark:file:text-orange-400 hover:file:bg-orange-100 dark:hover:file:bg-orange-900/30 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-md p-1">
                            @error('file_skripsi') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Requirement 5 -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">5. Formulir Pendaftaran Seminar (Terisi) <span class="text-red-500">*</span></label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Unduh templat di atas, isi lengkap, lalu unggah kembali di sini.</p>
                            <input type="file" name="file_formulir" required class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 dark:file:bg-orange-900/20 file:text-orange-700 dark:file:text-orange-400 hover:file:bg-orange-100 dark:hover:file:bg-orange-900/30 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-md p-1">
                            @error('file_formulir') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-10 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-orange-600 text-white font-bold rounded-md hover:bg-orange-700 transition-colors shadow-md">
                            Kirim Pengajuan Seminar
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>

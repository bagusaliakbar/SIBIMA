<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pendaftaran Sidang Skripsi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
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
                                Diajukan pada {{ $application->created_at->format('d M Y • H:i') }}
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
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4 uppercase tracking-widest">Berkas yang Diunggah:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $fileLabels = [
                                'file_formulir' => 'Formulir Pendaftaran Sidang',
                                'file_transkrip' => 'Scan Transkrip Nilai Akhir',
                                'file_acc_pembimbing' => 'Scan Bukti ACC Pembimbing 1 & 2',
                                'file_logbook' => 'Scan Kartu Bimbingan / Logbook',
                                'file_pembayaran' => 'Scan Bukti Pembayaran Sidang',
                                'file_skripsi' => 'Dokumen Soft File Skripsi',
                                'file_ktm' => 'Scan KTM',
                                'file_pkkmb_univ' => 'Scan Sertifikat PKKMB Universitas',
                                'file_pkkmb_fak' => 'Scan Sertifikat PKKMB Fakultas',
                                'file_makrab' => 'Scan Sertifikat Makrab Himpunan',
                                'file_cisco' => 'Scan Sertifikat Cisco IPv6',
                                'file_workshop' => 'Scan Sertifikat Pelatihan/Workshop',
                                'file_organisasi' => 'Scan Sertifikat Aktif Organisasi',
                                'file_toefl' => 'Scan Sertifikat TOEFL',
                                'file_kewirausahaan' => 'Scan Sertifikat Kewirausahaan',
                                'file_tahsin' => 'Scan Sertifikat Tahsin',
                                'file_komputer' => 'Scan Sertifikat Komputer',
                                'file_perpus_pinjam' => 'Scan Bebas Pinjam Perpus',
                                'file_perpus_sumbang' => 'Scan Sumbang Buku Perpus',
                                'file_ijazah' => 'Scan Ijazah SMA/SMK',
                            ];
                        @endphp

                        @foreach($fileLabels as $field => $label)
                            <a href="{{ Storage::url($application->$field) }}" target="_blank" class="flex items-center p-3 border border-slate-100 dark:border-slate-700 rounded hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <svg class="w-6 h-6 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-700 dark:text-slate-200 leading-tight">{{ $label }}</p>
                                    <p class="text-[9px] text-slate-400">Klik untuk melihat file</p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if($application->status === 'rejected')
                        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700 text-center">
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Silakan perbaiki berkas Anda dan ajukan kembali.</p>
                            <form action="{{ route('thesis-defense-applications.destroy', $application->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-6 py-2 bg-orange-600 text-white text-sm font-bold rounded-md hover:bg-orange-700 transition-colors shadow-sm">
                                    Ajukan Ulang
                                </button>
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
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Form Pengajuan Sidang Skripsi</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Lengkapi 20 persyaratan berikut dalam format PDF/Gambar (maks 2MB per file), kecuali Soft File Skripsi (maks 10MB).</p>
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

                <form action="{{ route('thesis-defense-applications.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        @php
                            $requirements = [
                                'file_formulir' => ['label' => '1. Formulir Pendaftaran Sidang', 'desc' => 'Unduh templat di atas, isi lengkap, lalu unggah kembali.'],
                                'file_transkrip' => ['label' => '2. Scan Transkrip Nilai Akhir', 'desc' => 'Scan transkrip nilai resmi dari fakultas/universitas.'],
                                'file_acc_pembimbing' => ['label' => '3. Scan Bukti ACC Pembimbing 1 & 2', 'desc' => 'Bukti persetujuan sidang dari kedua pembimbing.'],
                                'file_logbook' => ['label' => '4. Scan Kartu Bimbingan / Logbook', 'desc' => 'Kartu bimbingan yang sudah divalidasi P1 & P2.'],
                                'file_pembayaran' => ['label' => '5. Scan Bukti Pembayaran Sidang', 'desc' => 'Kwitansi atau bukti transfer pembayaran sidang.'],
                                'file_skripsi' => ['label' => '6. Dokumen Soft File Skripsi', 'desc' => 'Naskah skripsi lengkap (PDF/Word, maks 10MB).'],
                                'file_ktm' => ['label' => '7. Scan KTM', 'desc' => 'Kartu Tanda Mahasiswa yang masih aktif.'],
                                'file_pkkmb_univ' => ['label' => '8. Scan Sertifikat PKKMB Universitas', 'desc' => 'Sertifikat/Surat Keterangan PKKMB Universitas.'],
                                'file_pkkmb_fak' => ['label' => '9. Scan Sertifikat PKKMB Fakultas', 'desc' => 'Sertifikat/Surat Keterangan PKKMB Fakultas.'],
                                'file_makrab' => ['label' => '10. Scan Sertifikat Makrab Himpunan', 'desc' => 'Sertifikat/Surat Keterangan Makrab Himpunan.'],
                                'file_cisco' => ['label' => '11. Scan Sertifikat Cisco IPv6', 'desc' => 'Sertifikat Pelatihan Cisco IPv6.'],
                                'file_workshop' => ['label' => '12. Scan Sertifikat Pelatihan/Workshop', 'desc' => 'Sertifikat pelatihan atau workshop lainnya.'],
                                'file_organisasi' => ['label' => '13. Scan Sertifikat Aktif Organisasi', 'desc' => 'Surat keterangan aktif organisasi internal/eksternal.'],
                                'file_toefl' => ['label' => '14. Scan Sertifikat TOEFL', 'desc' => 'Sertifikat TOEFL dengan skor yang dipersyaratkan.'],
                                'file_kewirausahaan' => ['label' => '15. Scan Sertifikat Kewirausahaan', 'desc' => 'Sertifikat Praktikum Kewirausahaan.'],
                                'file_tahsin' => ['label' => '16. Scan Sertifikat Tahsin', 'desc' => 'Sertifikat Praktikum Tahsin.'],
                                'file_komputer' => ['label' => '17. Scan Sertifikat Komputer', 'desc' => 'Sertifikat Praktikum Komputer.'],
                                'file_perpus_pinjam' => ['label' => '18. Scan Bebas Pinjam Perpus', 'desc' => 'Surat keterangan tidak sedang meminjam buku.'],
                                'file_perpus_sumbang' => ['label' => '19. Scan Sumbang Buku Perpus', 'desc' => 'Surat keterangan telah menyumbang buku.'],
                                'file_ijazah' => ['label' => '20. Scan Ijazah SMA/SMK', 'desc' => 'Scan Ijazah pendidikan terakhir (SMA/Sederajat).'],
                            ];
                        @endphp

                        @foreach($requirements as $name => $info)
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">{{ $info['label'] }} <span class="text-red-500">*</span></label>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-1 leading-tight">{{ $info['desc'] }}</p>
                                <input type="file" name="{{ $name }}" required class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 dark:file:bg-slate-700 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 cursor-pointer border border-slate-200 dark:border-slate-700 rounded p-0.5">
                                @error($name) <p class="text-red-500 text-[9px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-orange-600 text-white font-bold rounded-md hover:bg-orange-700 transition-colors shadow-md uppercase text-xs tracking-widest">
                            Kirim Pengajuan Sidang
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>

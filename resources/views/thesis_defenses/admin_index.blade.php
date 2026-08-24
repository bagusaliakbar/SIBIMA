<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Validasi Sidang Skripsi', 'route' => null]
        ]" />
    </x-slot>

    @php
        $fileLabels = [
            'file_formulir' => '1. Formulir Pendaftaran Sidang',
            'file_transkrip' => '2. Scan Transkrip Nilai Akhir',
            'file_acc_pembimbing' => '3. Scan Bukti ACC Pembimbing',
            'file_logbook' => '4. Scan Kartu Bimbingan / Logbook',
            'file_pembayaran' => '5. Scan Bukti Pembayaran Sidang',
            'file_skripsi' => '6. Dokumen Soft File Skripsi',
            'file_ktm' => '7. Scan KTM',
            'file_pkkmb_univ' => '8. Scan Sertifikat PKKMB Univ',
            'file_pkkmb_fak' => '9. Scan Sertifikat PKKMB Fak',
            'file_makrab' => '10. Scan Sertifikat Makrab',
            'file_cisco' => '11. Scan Sertifikat Cisco IPv6',
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

    <div class="w-full space-y-6" 
         x-data="{
             openValidation: false,
             showFiles: false,
             selectedApp: null,
             setAllStatus(val) {
                 if (!this.$refs.validForm) return;
                 const radios = this.$refs.validForm.querySelectorAll('input[type=radio][value=' + val + ']');
                 radios.forEach(r => {
                     r.checked = true;
                     r.dispatchEvent(new Event('change'));
                 });
             }
         }">
        {{-- Wave Filter & Template Management Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Wave Filter --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em]">Gelombang Pelaksanaan</h3>
                    <div class="flex items-center gap-2 mt-2 font-black text-sm text-slate-800 dark:text-slate-100 uppercase tracking-tight">
                        @if($activeWave)
                            <span class="bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-lg border border-indigo-100 dark:border-indigo-500/20 shadow-2xs">{{ $activeWave->name }}</span>
                        @else
                            <span class="bg-rose-50 dark:bg-rose-500/10 px-3 py-1 rounded-lg border border-rose-100 dark:border-rose-500/20 text-rose-600 dark:text-rose-400 uppercase tracking-tighter">Tidak Ada Gelombang Aktif</span>
                        @endif
                    </div>
                </div>

                <form action="{{ route('thesis-defense-applications.index') }}" method="GET" class="relative group w-full md:w-auto flex items-center gap-2">
                    @if(!empty($status) && $status !== 'all')
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    @if(!empty($search))
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    <select name="wave_id" onchange="this.form.submit()" 
                            class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-[11px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs min-w-[220px] cursor-pointer">
                        <option value="">SEMUA GELOMBANG</option>
                        @foreach($waves as $wave)
                            <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                {{ strtoupper($wave->name) }} {{ $wave->is_active ? '(AKTIF)' : '(ARSIP)' }} — [{{ $wave->app_count }} Mhs]
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Template Management --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-[0.2em]">Templat Formulir Sidang</h3>
                    @if($template)
                        <div class="flex items-center gap-3 mt-2">
                            <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[11px] font-black text-slate-800 dark:text-slate-100 uppercase truncate max-w-[150px] tracking-tighter">{{ $template->title }}</div>
                                <a href="{{ route('download.private', ['path' => $template->file_path]) }}" target="_blank" class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest hover:underline">Download File</a>
                            </div>
                        </div>
                    @else
                        <div class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase mt-2 italic tracking-widest">Belum Ada Templat</div>
                    @endif
                </div>

                <div x-data="{ open: false }">
                    <button @click="open = true" class="w-full md:w-auto px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-[10px] font-black rounded-xl uppercase tracking-widest transition-all shadow-xs shadow-orange-500/20 flex items-center justify-center cursor-pointer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Update Templat
                    </button>

                    <!-- Upload Template Modal -->
                    <template x-teleport="body">
                        <div x-show="open" class="fixed inset-0 overflow-y-auto" style="z-index: 99999 !important;" x-cloak>
                            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" @click="open = false"></div>
                            <div class="min-h-full flex items-center justify-center p-4 text-center sm:p-6">
                                <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-100 dark:border-slate-700 my-8" @click.stop>
                                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                                        <div>
                                            <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Update Templat Sidang</h3>
                                            <p class="text-[10px] text-slate-500 uppercase font-bold mt-1 tracking-wider">File pendaftaran sidang skripsi</p>
                                        </div>
                                        <button @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <form action="{{ route('thesis-defense-applications.upload-template') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="px-8 py-6 space-y-5">
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Judul Formulir</label>
                                                <input type="text" name="title" required class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all p-3" placeholder="Contoh: Formulir Pendaftaran Sidang 2026">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Pilih File (PDF/DOCX)</label>
                                                <input type="file" name="template_file" required accept=".pdf,.doc,.docx" class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-orange-600 file:text-white hover:file:bg-orange-700 transition-all cursor-pointer bg-slate-50 dark:bg-slate-900 rounded-xl p-2 border border-dashed border-slate-200 dark:border-slate-700">
                                            </div>
                                        </div>
                                        <div class="px-8 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                            <button type="button" @click="open = false" class="px-5 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 dark:hover:text-slate-300 transition-colors">Batal</button>
                                            <button type="submit" class="px-5 py-2.5 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 shadow-xs shadow-orange-500/20 transition-all">Upload Templat</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- KPI STAT CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Total Pendaftar --}}
            <a href="{{ route('thesis-defense-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'all', 'search' => $search]) }}"
               class="group relative overflow-hidden bg-white dark:bg-slate-800 p-5 rounded-2xl border transition-all duration-200 shadow-xs hover:shadow-md {{ ($status ?? 'all') === 'all' ? 'border-indigo-500/80 ring-2 ring-indigo-500/20 dark:ring-indigo-500/30' : 'border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Pendaftar</p>
                        <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-1 tracking-tight">{{ $stats['total'] ?? 0 }}</h4>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-[10px] font-bold {{ ($status ?? 'all') === 'all' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}">
                    <span>Semua pendaftaran sidang pada gelombang</span>
                </div>
            </a>

            {{-- Card 2: Menunggu Validasi --}}
            <a href="{{ route('thesis-defense-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'pending', 'search' => $search]) }}"
               class="group relative overflow-hidden bg-white dark:bg-slate-800 p-5 rounded-2xl border transition-all duration-200 shadow-xs hover:shadow-md {{ ($status ?? '') === 'pending' ? 'border-amber-500/80 ring-2 ring-amber-500/20 dark:ring-amber-500/30' : 'border-slate-200/80 dark:border-slate-700/80 hover:border-amber-300 dark:hover:border-amber-700' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">Menunggu Validasi</p>
                            @if(($stats['pending'] ?? 0) > 0)
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                            @endif
                        </div>
                        <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 tracking-tight">{{ $stats['pending'] ?? 0 }}</h4>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/60 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-[10px] font-bold text-amber-700 dark:text-amber-400">
                    <span>Perlu verifikasi 20 berkas</span>
                </div>
            </a>

            {{-- Card 3: Disetujui / Siap Jadwal --}}
            <a href="{{ route('thesis-defense-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'approved', 'search' => $search]) }}"
               class="group relative overflow-hidden bg-white dark:bg-slate-800 p-5 rounded-2xl border transition-all duration-200 shadow-xs hover:shadow-md {{ ($status ?? '') === 'approved' ? 'border-emerald-500/80 ring-2 ring-emerald-500/20 dark:ring-emerald-500/30' : 'border-slate-200/80 dark:border-slate-700/80 hover:border-emerald-300 dark:hover:border-emerald-700' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Disetujui / Siap Jadwal</p>
                        <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 tracking-tight">{{ $stats['approved'] ?? 0 }}</h4>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">
                    <span>Berkas valid & siap sidang</span>
                </div>
            </a>

            {{-- Card 4: Perlu Revisi / Ditolak --}}
            <a href="{{ route('thesis-defense-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'rejected', 'search' => $search]) }}"
               class="group relative overflow-hidden bg-white dark:bg-slate-800 p-5 rounded-2xl border transition-all duration-200 shadow-xs hover:shadow-md {{ ($status ?? '') === 'rejected' ? 'border-rose-500/80 ring-2 ring-rose-500/20 dark:ring-rose-500/30' : 'border-slate-200/80 dark:border-slate-700/80 hover:border-rose-300 dark:hover:border-rose-700' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-rose-600 dark:text-rose-400">Perlu Revisi / Ditolak</p>
                        <h4 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1 tracking-tight">{{ $stats['rejected'] ?? 0 }}</h4>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60 flex items-center justify-center text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-[10px] font-bold text-rose-700 dark:text-rose-400">
                    <span>Berkas belum memenuhi syarat</span>
                </div>
            </a>
        </div>

        {{-- TABLE CARD WITH INTEGRATED SEARCH & STATUS FILTER --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
            {{-- Toolbar: Status Tabs & Search Bar --}}
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-700/80 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-900/30">
                {{-- Status Filter Tabs --}}
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mr-1 hidden sm:inline">Status:</span>

                    {{-- Semua Status --}}
                    <a href="{{ route('thesis-defense-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'all', 'search' => $search]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($status ?? 'all') === 'all' ? 'bg-orange-500 text-white border-orange-500 shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>Semua</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? 'all') === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                            {{ $stats['total'] ?? 0 }}
                        </span>
                    </a>

                    {{-- Menunggu --}}
                    <a href="{{ route('thesis-defense-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'pending', 'search' => $search]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($status ?? '') === 'pending' ? 'bg-amber-600 text-white border-amber-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-amber-700 dark:text-amber-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>⏳ Menunggu</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? '') === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200' }}">
                            {{ $stats['pending'] ?? 0 }}
                        </span>
                    </a>

                    {{-- Disetujui --}}
                    <a href="{{ route('thesis-defense-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'approved', 'search' => $search]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($status ?? '') === 'approved' ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>✓ Disetujui</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? '') === 'approved' ? 'bg-white/20 text-white' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200' }}">
                            {{ $stats['approved'] ?? 0 }}
                        </span>
                    </a>

                    {{-- Ditolak / Revisi --}}
                    <a href="{{ route('thesis-defense-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'rejected', 'search' => $search]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($status ?? '') === 'rejected' ? 'bg-rose-600 text-white border-rose-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-rose-700 dark:text-rose-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>✕ Perlu Revisi</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? '') === 'rejected' ? 'bg-white/20 text-white' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200' }}">
                            {{ $stats['rejected'] ?? 0 }}
                        </span>
                    </a>
                </div>

                {{-- Search Bar Form --}}
                <form action="{{ route('thesis-defense-applications.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                    @if(!empty($selectedWaveId))
                        <input type="hidden" name="wave_id" value="{{ $selectedWaveId }}">
                    @endif
                    @if(!empty($status) && $status !== 'all')
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif

                    <div class="relative w-full md:w-72">
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Cari nama, NPM, judul..."
                               class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all shadow-2xs">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        @if($search)
                            <a href="{{ route('thesis-defense-applications.index', ['wave_id' => $selectedWaveId, 'status' => $status]) }}" 
                               class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs font-bold">
                                ✕
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white rounded-xl text-xs font-bold hover:bg-slate-800 dark:hover:bg-slate-600 transition-all shadow-2xs shrink-0 cursor-pointer">
                        Cari
                    </button>
                </form>
            </div>

            {{-- Table Content --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/70 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700/80">
                            <th class="py-4 px-6">Mahasiswa & Judul Skripsi</th>
                            <th class="py-4 px-6 text-center">Berkas Persyaratan</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                        @forelse($applications as $app)
                            @php
                                $rejectedCount = 0;
                                if (!empty($app->file_reviews)) {
                                    foreach ($app->file_reviews as $rev) {
                                        if (($rev['status'] ?? '') === 'rejected') $rejectedCount++;
                                    }
                                }

                                $appData = [
                                    'id' => $app->id,
                                    'validate_url' => route('thesis-defense-applications.validate', $app->id),
                                    'student_name' => $app->thesis->student->name,
                                    'student_id' => $app->thesis->student->identifier,
                                    'status' => $app->status,
                                    'admin_feedback' => $app->admin_feedback,
                                    'file_reviews' => $app->file_reviews ?? [],
                                    'files' => [
                                        'file_formulir' => $app->file_formulir,
                                        'file_transkrip' => $app->file_transkrip,
                                        'file_acc_pembimbing' => $app->file_acc_pembimbing,
                                        'file_logbook' => $app->file_logbook,
                                        'file_pembayaran' => $app->file_pembayaran,
                                        'file_skripsi' => $app->file_skripsi,
                                        'file_ktm' => $app->file_ktm,
                                        'file_pkkmb_univ' => $app->file_pkkmb_univ,
                                        'file_pkkmb_fak' => $app->file_pkkmb_fak,
                                        'file_makrab' => $app->file_makrab,
                                        'file_cisco' => $app->file_cisco,
                                        'file_workshop' => $app->file_workshop,
                                        'file_organisasi' => $app->file_organisasi,
                                        'file_toefl' => $app->file_toefl,
                                        'file_kewirausahaan' => $app->file_kewirausahaan,
                                        'file_tahsin' => $app->file_tahsin,
                                        'file_komputer' => $app->file_komputer,
                                        'file_perpus_pinjam' => $app->file_perpus_pinjam,
                                        'file_perpus_sumbang' => $app->file_perpus_sumbang,
                                        'file_ijazah' => $app->file_ijazah,
                                    ]
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors group">
                                <td class="py-4 px-6 max-w-md">
                                    <div class="font-black text-slate-900 dark:text-white uppercase tracking-tight text-sm">
                                        {{ $app->thesis->student->name }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="font-mono text-[10px] font-bold px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded border border-slate-200 dark:border-slate-700">
                                            {{ $app->thesis->student->identifier }}
                                        </span>
                                        @if($app->created_at)
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold">
                                                • Daftar: {{ $app->created_at->translatedFormat('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Thesis Title --}}
                                    @if($app->thesis->title)
                                        <div class="mt-2.5 text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-700/60">
                                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-0.5">Judul Skripsi:</span>
                                            {{ $app->thesis->title }}
                                        </div>
                                    @endif

                                    {{-- Supervisors --}}
                                    <div class="mt-2.5 flex flex-wrap gap-2">
                                        @if($app->thesis->pembimbing1)
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-indigo-50/80 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-800/60 text-[10px]">
                                                <span class="w-3.5 h-3.5 rounded bg-indigo-600 text-white flex items-center justify-center text-[8px] font-black">1</span>
                                                <span class="font-bold text-indigo-950 dark:text-indigo-200 truncate max-w-[150px]">{{ $app->thesis->pembimbing1->name }}</span>
                                            </div>
                                        @endif
                                        @if($app->thesis->pembimbing2)
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-blue-50/80 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-800/60 text-[10px]">
                                                <span class="w-3.5 h-3.5 rounded bg-blue-600 text-white flex items-center justify-center text-[8px] font-black">2</span>
                                                <span class="font-bold text-blue-950 dark:text-blue-200 truncate max-w-[150px]">{{ $app->thesis->pembimbing2->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <button type="button"
                                                @click="selectedApp = {{ json_encode($appData) }}; showFiles = true" 
                                                class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border {{ $rejectedCount > 0 ? 'border-rose-300 dark:border-rose-800 bg-rose-50/30' : 'border-slate-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-600' }} rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-2xs group/doc cursor-pointer">
                                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span>20 Berkas Sidang</span>
                                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover/doc:text-indigo-600 dark:group-hover/doc:text-indigo-400 transition-transform group-hover/doc:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>

                                        @if($rejectedCount > 0)
                                            <span class="inline-flex items-center gap-1 text-[9px] font-black text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded-md border border-rose-200 dark:border-rose-800/80">
                                                ⚠️ {{ $rejectedCount }} Berkas Ditolak
                                            </span>
                                        @else
                                            <span class="text-[9px] font-medium text-slate-400 dark:text-slate-500">
                                                Klik untuk periksa
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex flex-col items-center gap-1.5">
                                        @if($app->status === 'approved')
                                            <x-status-badge type="emerald" label="DISETUJUI" />
                                        @elseif($app->status === 'rejected')
                                            <x-status-badge type="rose" label="DITOLAK / REVISI" />
                                        @else
                                            <x-status-badge type="amber" label="MENUNGGU" />
                                        @endif
                                        
                                        @if($app->admin_feedback)
                                            <p class="text-[9px] text-rose-600 dark:text-rose-400 font-bold mt-1 max-w-[140px] truncate bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded border border-rose-100 dark:border-rose-900/50" 
                                               title="{{ $app->admin_feedback }}">
                                                "{{ $app->admin_feedback }}"
                                            </p>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button type="button"
                                            @click="selectedApp = {{ json_encode($appData) }}; openValidation = true" 
                                            class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-[11px] font-bold rounded-xl uppercase tracking-wider transition-all shadow-xs shadow-orange-500/20 hover:scale-[1.02] active:scale-95 cursor-pointer">
                                        Validasi
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="4" description="Tidak ada pengajuan sidang skripsi yang sesuai dengan kriteria filter." icon="academic-cap" />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="p-4 sm:p-5 border-t border-slate-100 dark:border-slate-700/80 bg-slate-50/30 dark:bg-slate-900/30">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>

        {{-- GLOBAL VIEW FILES MODAL (TELEPORTED TO BODY, GUARANTEED TOP LAYER) --}}
        <template x-teleport="body">
            <div x-show="showFiles" class="fixed inset-0 overflow-y-auto" style="z-index: 99999 !important;" x-cloak>
                <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" @click="showFiles = false"></div>

                <div class="min-h-full flex items-center justify-center p-4 text-center sm:p-6">
                    <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-100 dark:border-slate-700 my-8" @click.stop>
                        <template x-if="selectedApp">
                            <div>
                                <div class="px-5 sm:px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                                    <div>
                                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Daftar Dokumen Sidang Skripsi (20 File)</h3>
                                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-0.5">
                                            <span x-text="selectedApp.student_name"></span> (<span x-text="selectedApp.student_id" class="font-mono"></span>)
                                        </p>
                                    </div>
                                    <button @click="showFiles = false" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <div class="p-5 sm:p-6 max-h-[68vh] overflow-y-auto grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    @foreach($fileLabels as $field => $label)
                                        <div class="p-2.5 bg-slate-50 dark:bg-slate-900/70 border rounded-2xl flex items-center justify-between gap-2 shadow-2xs"
                                             :class="(selectedApp.file_reviews && selectedApp.file_reviews['{{ $field }}'] && selectedApp.file_reviews['{{ $field }}'].status === 'rejected') ? 'border-rose-300 dark:border-rose-800 bg-rose-50/50 dark:bg-rose-950/30' : 'border-slate-200/80 dark:border-slate-700/80'">
                                            <div class="min-w-0 flex-1">
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate">{{ $label }}</span>
                                                <template x-if="selectedApp.file_reviews && selectedApp.file_reviews['{{ $field }}'] && selectedApp.file_reviews['{{ $field }}'].status === 'rejected'">
                                                    <span class="text-[10px] font-black text-rose-600 dark:text-rose-400 mt-0.5 block truncate" x-text="'Ditolak: ' + (selectedApp.file_reviews['{{ $field }}'].note || 'Perlu revisi')"></span>
                                                </template>
                                            </div>
                                            <a :href="selectedApp.files['{{ $field }}']" target="_blank" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 rounded-lg text-[10px] font-bold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors shrink-0 shadow-2xs">
                                                <span>Buka File</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="px-5 sm:px-6 py-3.5 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                                    <button type="button" @click="showFiles = false" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl transition-colors">Tutup</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        {{-- GLOBAL WIDE 2-COLUMN VALIDATION MODAL (TELEPORTED TO BODY, GUARANTEED TOP LAYER) --}}
        <template x-teleport="body">
            <div x-show="openValidation" class="fixed inset-0 overflow-y-auto" style="z-index: 99999 !important;" x-cloak>
                <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" @click="openValidation = false"></div>

                <div class="min-h-full flex items-center justify-center p-4 text-center sm:p-6">
                    <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-100 dark:border-slate-700 my-8" @click.stop>
                        <template x-if="selectedApp">
                            <div>
                                {{-- Modal Header --}}
                                <div class="px-5 sm:px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/70 dark:bg-slate-900/60">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Validasi Berkas Sidang Skripsi</h3>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800">
                                                20 Dokumen
                                            </span>
                                        </div>
                                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400 mt-0.5">
                                            <span x-text="selectedApp.student_name"></span> <span class="font-mono text-slate-400">(<span x-text="selectedApp.student_id"></span>)</span>
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="setAllStatus('approved')" 
                                                class="px-2.5 py-1.5 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 rounded-xl text-[10px] font-bold transition-all shadow-2xs cursor-pointer">
                                            ✓ Setujui Semua (OK)
                                        </button>
                                        <button type="button" @click="setAllStatus('rejected')" 
                                                class="px-2.5 py-1.5 bg-rose-50 dark:bg-rose-950/50 hover:bg-rose-100 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/80 rounded-xl text-[10px] font-bold transition-all shadow-2xs cursor-pointer">
                                            ✕ Tolak Semua
                                        </button>
                                        <button @click="openValidation = false" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors ml-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <form x-ref="validForm" :action="selectedApp.validate_url" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="px-5 sm:px-6 py-4 space-y-4 max-h-[66vh] overflow-y-auto">
                                        {{-- 2-Column Responsive Grid for 20 Files --}}
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block">
                                                    Verifikasi 20 Berkas Persyaratan:
                                                </label>
                                                <span class="text-[10px] text-slate-400 font-medium">Klik nama berkas untuk melihat file</span>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                @foreach($fileLabels as $field => $label)
                                                    <div class="p-2.5 bg-slate-50 dark:bg-slate-900/70 rounded-xl border border-slate-200/80 dark:border-slate-700/80 flex flex-col justify-between gap-1.5 transition-all shadow-2xs"
                                                         x-data="{ isRejected: (selectedApp.file_reviews && selectedApp.file_reviews['{{ $field }}'] && selectedApp.file_reviews['{{ $field }}'].status === 'rejected') }">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <a :href="selectedApp.files['{{ $field }}']" target="_blank" 
                                                               class="text-xs font-bold text-slate-800 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1.5 truncate group/filelink"
                                                               title="Buka {{ $label }}">
                                                                <span class="truncate">{{ $label }}</span>
                                                                <svg class="w-3 h-3 text-slate-400 group-hover/filelink:text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                            </a>

                                                            {{-- Radio Controls (OK / Tolak) --}}
                                                            <div class="flex items-center gap-1 bg-white dark:bg-slate-800 p-0.5 rounded-lg border border-slate-200 dark:border-slate-700 shrink-0">
                                                                <label class="inline-flex items-center px-1.5 py-0.5 rounded cursor-pointer transition-colors"
                                                                       :class="!isRejected ? 'bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 font-black' : 'text-slate-400 hover:text-slate-600'">
                                                                    <input type="radio" name="file_reviews[{{ $field }}][status]" value="approved" 
                                                                           @change="isRejected = false"
                                                                           :checked="!isRejected"
                                                                           class="w-3 h-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600">
                                                                    <span class="ml-1 text-[9px]">OK</span>
                                                                </label>
                                                                <label class="inline-flex items-center px-1.5 py-0.5 rounded cursor-pointer transition-colors"
                                                                       :class="isRejected ? 'bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-black' : 'text-slate-400 hover:text-slate-600'">
                                                                    <input type="radio" name="file_reviews[{{ $field }}][status]" value="rejected" 
                                                                           @change="isRejected = true"
                                                                           :checked="isRejected"
                                                                           class="w-3 h-3 text-rose-600 focus:ring-rose-500 border-slate-300 dark:border-slate-600">
                                                                    <span class="ml-1 text-[9px]">Tolak</span>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        {{-- Note Input --}}
                                                        <input type="text" name="file_reviews[{{ $field }}][note]" 
                                                               :value="selectedApp.file_reviews && selectedApp.file_reviews['{{ $field }}'] ? selectedApp.file_reviews['{{ $field }}'].note : ''" 
                                                               placeholder="Catatan revisi jika berkas ditolak..." 
                                                               :class="isRejected ? 'border-rose-300 dark:border-rose-800 bg-rose-50/40 text-rose-900 dark:text-rose-200 placeholder-rose-400' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 placeholder-slate-400'"
                                                               class="w-full rounded-lg text-[11px] font-medium px-2.5 py-1 border transition-all focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Final Decision & Admin Global Note in 2 Columns --}}
                                        <div class="pt-3.5 border-t border-slate-100 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50/50 dark:bg-slate-900/30 p-3 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">
                                                    Status Akhir Pengajuan:
                                                </label>
                                                <select name="status" class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs font-bold uppercase tracking-wider p-2 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-2xs">
                                                    <option value="approved" :selected="selectedApp.status === 'approved'">✓ SETUJUI (BERKAS VALID & LENGKAP)</option>
                                                    <option value="rejected" :selected="selectedApp.status === 'rejected'">✕ TOLAK (PERLU REVISI BERKAS)</option>
                                                    <option value="pending" :selected="selectedApp.status === 'pending'">⏳ TETAP MENUNGGU</option>
                                                </select>
                                                <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1">
                                                    Pilih "SETUJUI" jika seluruh berkas sesuai syarat.
                                                </p>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">
                                                    Catatan Admin (Global / Feedback Mahasiswa):
                                                </label>
                                                <textarea name="admin_feedback" rows="2" class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs font-medium p-2 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-2xs" placeholder="Silakan upload ulang berkas yang ditolak..." x-text="selectedApp.admin_feedback || ''"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal Footer --}}
                                    <div class="px-5 sm:px-6 py-3.5 bg-slate-50/70 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                            Perubahan akan tersimpan ke sistem.
                                        </span>
                                        <div class="flex items-center gap-2.5">
                                            <button type="button" @click="openValidation = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-slate-300 transition-colors">Batal</button>
                                            <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl uppercase tracking-wider shadow-xs shadow-orange-500/20 transition-all cursor-pointer">Simpan Keputusan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>

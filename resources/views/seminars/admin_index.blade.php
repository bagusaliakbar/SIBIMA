<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Validasi Seminar', 'route' => null]
        ]" />
    </x-slot>

    @php
        $fileLabels = [
            'file_acc_pembimbing' => '1. Bukti ACC Pembimbing',
            'file_pembayaran' => '2. Scan Bukti Pembayaran',
            'file_kartu_bimbingan' => '3. Scan Kartu Bimbingan / Logbook',
            'file_skripsi' => '4. Dokumen Draf Skripsi',
            'file_formulir' => '5. Formulir Pendaftaran Seminar'
        ];
    @endphp

    <div class="w-full space-y-6"
         x-data="{
             openValidation: false,
             selectedApp: null,
             setAllStatus(val) {
                 if (!this.$refs.validSeminarForm) return;
                 const radios = this.$refs.validSeminarForm.querySelectorAll('input[type=radio][value=' + val + ']');
                 radios.forEach(r => {
                     r.checked = true;
                     r.dispatchEvent(new Event('change'));
                 });
             },

             // PREVIEW PENDAFTARAN SEMINAR
             openPreview: false,
             previewApp: null,
             previewFileKey: 'file_acc_pembimbing',
             previewFileLabel: '1. Bukti ACC Pembimbing',
             previewRawUrl: '',
             previewFileUrl: '',
             previewLoading: false,

             fileLabels: {
                 'file_acc_pembimbing': '1. Bukti ACC Pembimbing',
                 'file_pembayaran': '2. Scan Bukti Pembayaran',
                 'file_kartu_bimbingan': '3. Scan Kartu Bimbingan / Logbook',
                 'file_skripsi': '4. Dokumen Draf Skripsi',
                 'file_formulir': '5. Formulir Pendaftaran Seminar'
             },

             openPreviewModal(app) {
                 this.previewApp = app;
                 this.openPreview = true;
                 const firstKey = (app && app.files && Object.keys(app.files).length > 0) ? Object.keys(app.files)[0] : 'file_acc_pembimbing';
                 this.selectPreviewFile(firstKey);
             },

             selectPreviewFile(key) {
                 this.previewFileKey = key;
                 this.previewFileLabel = this.fileLabels[key] || key;
                 const rawUrl = (this.previewApp && this.previewApp.files) ? (this.previewApp.files[key] || '') : '';
                 this.previewRawUrl = rawUrl;
                 this.previewLoading = true;
                 this.previewFileUrl = this.formatPreviewUrl(rawUrl);
                 setTimeout(() => { this.previewLoading = false; }, 600);
             },

             formatPreviewUrl(link) {
                 if (!link) return '';
                 if (link.includes('drive.google.com/file/d/')) {
                     let url = link.replace(/\/view(\?.*)?$/, '/preview').replace(/\/edit(\?.*)?$/, '/preview');
                     if (!url.includes('/preview')) {
                         url = url.replace(/\/?$/, '/preview');
                     }
                     return url;
                 } else if (link.includes('drive.google.com/open?id=')) {
                     const id = link.split('id=')[1]?.split('&')[0];
                     return id ? 'https://drive.google.com/file/d/' + id + '/preview' : link;
                 } else if (link.includes('docs.google.com/document/d/') || link.includes('docs.google.com/spreadsheets/d/') || link.includes('docs.google.com/presentation/d/')) {
                     return link.replace(/\/edit(\?.*)?$/, '/preview').replace(/\/view(\?.*)?$/, '/preview');
                 } else if (!link.startsWith('http://') && !link.startsWith('https://')) {
                     return '{{ route('download.private') }}?path=' + encodeURIComponent(link) + '&inline=1';
                 }
                 return link;
             },

             nextPreviewFile() {
                 if (!this.previewApp || !this.previewApp.files) return;
                 const keys = Object.keys(this.previewApp.files);
                 const currIdx = keys.indexOf(this.previewFileKey);
                 if (currIdx < keys.length - 1) {
                     this.selectPreviewFile(keys[currIdx + 1]);
                 }
             },

             prevPreviewFile() {
                 if (!this.previewApp || !this.previewApp.files) return;
                 const keys = Object.keys(this.previewApp.files);
                 const currIdx = keys.indexOf(this.previewFileKey);
                 if (currIdx > 0) {
                     this.selectPreviewFile(keys[currIdx - 1]);
                 }
             },

             switchToValidation() {
                 this.selectedApp = this.previewApp;
                 this.openPreview = false;
                 this.openValidation = true;
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

                <form action="{{ route('seminar-applications.index') }}" method="GET" class="relative group w-full md:w-auto flex items-center gap-2">
                    @if(!empty($status) && $status !== 'all')
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    @if(!empty($search))
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    <select name="wave_id" onchange="this.form.submit()" 
                            class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-[11px] font-black uppercase tracking-widest focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all shadow-2xs min-w-[220px] cursor-pointer">
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
                    <h3 class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-[0.2em]">Templat Formulir</h3>
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
                                            <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Update Templat Formulir</h3>
                                            <p class="text-[10px] text-slate-500 uppercase font-bold mt-1 tracking-wider">File ini akan diunduh oleh mahasiswa</p>
                                        </div>
                                        <button @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <form action="{{ route('seminar-applications.upload-template') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="px-8 py-6 space-y-5">
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Judul Formulir</label>
                                                <input type="text" name="title" required class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all p-3" placeholder="Contoh: Formulir Pendaftaran Seminar 2026">
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
            <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'all', 'search' => $search]) }}"
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
                    <span>Semua pendaftaran seminar pada gelombang</span>
                </div>
            </a>

            {{-- Card 2: Menunggu Validasi --}}
            <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'pending', 'search' => $search]) }}"
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
                    <span>Perlu verifikasi 5 berkas</span>
                </div>
            </a>

            {{-- Card 3: Disetujui / Siap Jadwal --}}
            <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'approved', 'search' => $search]) }}"
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
                    <span>Berkas valid & siap diseminarkan</span>
                </div>
            </a>

            {{-- Card 4: Perlu Revisi / Ditolak --}}
            <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'rejected', 'search' => $search]) }}"
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
                    <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'all', 'search' => $search]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($status ?? 'all') === 'all' ? 'bg-orange-500 text-white border-orange-500 shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>Semua</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? 'all') === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                            {{ $stats['total'] ?? 0 }}
                        </span>
                    </a>

                    {{-- Menunggu --}}
                    <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'pending', 'search' => $search]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($status ?? '') === 'pending' ? 'bg-amber-600 text-white border-amber-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-amber-700 dark:text-amber-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>⏳ Menunggu</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? '') === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200' }}">
                            {{ $stats['pending'] ?? 0 }}
                        </span>
                    </a>

                    {{-- Disetujui --}}
                    <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'approved', 'search' => $search]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($status ?? '') === 'approved' ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>✓ Disetujui</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? '') === 'approved' ? 'bg-white/20 text-white' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200' }}">
                            {{ $stats['approved'] ?? 0 }}
                        </span>
                    </a>

                    {{-- Ditolak / Revisi --}}
                    <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'rejected', 'search' => $search]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border shrink-0 {{ ($status ?? '') === 'rejected' ? 'bg-rose-600 text-white border-rose-600 shadow-2xs' : 'bg-white dark:bg-slate-800 text-rose-700 dark:text-rose-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <span>✕ Perlu Revisi</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($status ?? '') === 'rejected' ? 'bg-white/20 text-white' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200' }}">
                            {{ $stats['rejected'] ?? 0 }}
                        </span>
                    </a>
                </div>

                {{-- Search Bar Form --}}
                <form action="{{ route('seminar-applications.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
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
                            <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => $status]) }}" 
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
                            <th class="py-4 px-6">Berkas Persyaratan</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                        @forelse($applications as $app)
                            @php
                                $student = $app->thesis->student;
                                $p1 = $app->thesis->pembimbing1;
                                $p2 = $app->thesis->pembimbing2;

                                $phone = $student->phone_number;
                                $cleanPhone = null;
                                if ($phone) {
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                }
                                $waMessage = rawurlencode("Halo {$student->name}, terkait pengajuan Seminar Proposal Anda di SIBIMA...");

                                $appData = [
                                    'id' => $app->id,
                                    'validate_url' => route('seminar-applications.validate', $app->id),
                                    'download_zip_url' => route('seminar-applications.download-zip', $app->id),
                                    'student_name' => $student->name,
                                    'student_id' => $student->identifier,
                                    'student_email' => $student->email,
                                    'student_phone' => $phone,
                                    'clean_phone' => $cleanPhone,
                                    'wa_message' => $waMessage,
                                    'entry_year' => $student->entry_year,
                                    'avatar_url' => $student->avatar ? \Illuminate\Support\Facades\Storage::url($student->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=f97316&color=ffffff&bold=true',
                                    'thesis_title' => $app->thesis->final_title ?: $app->thesis->title,
                                    'pembimbing1_name' => $p1 ? $p1->name : null,
                                    'pembimbing2_name' => $p2 ? $p2->name : null,
                                    'wave_name' => $app->wave ? $app->wave->name : '-',
                                    'created_at_formatted' => $app->created_at ? $app->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
                                    'status' => $app->status,
                                    'admin_feedback' => $app->admin_feedback,
                                    'file_reviews' => $app->file_reviews ?? [],
                                    'files' => [
                                        'file_acc_pembimbing' => $app->file_acc_pembimbing,
                                        'file_pembayaran' => $app->file_pembayaran,
                                        'file_kartu_bimbingan' => $app->file_kartu_bimbingan,
                                        'file_skripsi' => $app->file_skripsi,
                                        'file_formulir' => $app->file_formulir,
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
                                <td class="py-4 px-6">
                                    <div class="flex flex-wrap gap-1.5 max-w-[280px]">
                                        @php
                                            $files = [
                                                ['key' => 'file_acc_pembimbing', 'label' => 'Bukti ACC', 'path' => $app->file_acc_pembimbing],
                                                ['key' => 'file_pembayaran', 'label' => 'Bukti Bayar', 'path' => $app->file_pembayaran],
                                                ['key' => 'file_kartu_bimbingan', 'label' => 'Logbook', 'path' => $app->file_kartu_bimbingan],
                                                ['key' => 'file_skripsi', 'label' => 'Draf Skripsi', 'path' => $app->file_skripsi],
                                                ['key' => 'file_formulir', 'label' => 'Formulir', 'path' => $app->file_formulir],
                                            ];
                                        @endphp
                                        @foreach($files as $file)
                                            @php
                                                $review = $app->file_reviews[$file['key']] ?? null;
                                                $isRejected = ($review['status'] ?? '') === 'rejected';
                                            @endphp
                                            <a href="{{ $file['path'] }}" target="_blank" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 dark:bg-slate-900 border {{ $isRejected ? 'border-rose-300 dark:border-rose-800 bg-rose-50/50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300' : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-indigo-400 dark:hover:border-indigo-600 hover:text-indigo-600 dark:hover:text-indigo-400' }} rounded-lg text-[10px] font-bold transition-all shadow-2xs"
                                               title="{{ $isRejected ? 'Ditolak: ' . ($review['note'] ?? 'Perlu revisi') : 'Lihat ' . $file['label'] }}">
                                                <span>{{ $file['label'] }}</span>
                                                @if($isRejected)
                                                    <span class="text-rose-500 font-black text-xs">✕</span>
                                                @else
                                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                @endif
                                            </a>
                                        @endforeach
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
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                                @click="openPreviewModal({{ json_encode($appData) }})" 
                                                class="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-xl text-[11px] font-bold uppercase tracking-wider transition-all shadow-2xs hover:scale-[1.02] active:scale-95 cursor-pointer flex items-center gap-1.5"
                                                title="Pratinjau Lengkap Pendaftaran Seminar">
                                            <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Preview</span>
                                        </button>
                                        <button type="button"
                                                @click="selectedApp = {{ json_encode($appData) }}; openValidation = true" 
                                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-[11px] font-bold rounded-xl uppercase tracking-wider transition-all shadow-xs shadow-orange-500/20 hover:scale-[1.02] active:scale-95 cursor-pointer">
                                            Validasi
                                        </button>
                                        <form action="{{ route('seminar-applications.destroy', $app) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN & MENGHAPUS pengajuan seminar proposal untuk mahasiswa {{ addslashes($app->thesis->student->name) }}?\n\nMahasiswa dapat mengajukan ulang berkas pendaftaran baru.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Batalkan & Hapus Pengajuan Seminar" 
                                                    class="p-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-xl transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="4" description="Tidak ada pengajuan seminar yang sesuai dengan kriteria filter." icon="book" />
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

        {{-- GLOBAL WIDE 2-COLUMN VALIDATION MODAL (TELEPORTED TO BODY, GUARANTEED TOP LAYER) --}}
        <template x-teleport="body">
            <div x-show="openValidation" class="fixed inset-0 overflow-y-auto" style="z-index: 99999 !important;" x-cloak>
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="openValidation = false"></div>

                <div class="min-h-full flex items-center justify-center p-4 text-center sm:p-6">
                    <div class="relative w-full max-w-4xl bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 my-8" @click.stop>
                        <template x-if="selectedApp">
                            <div>
                                {{-- Modal Header --}}
                                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-slate-900">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight">Validasi Berkas Seminar Proposal</h3>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-orange-100 dark:bg-orange-950 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800">
                                                5 Dokumen
                                            </span>
                                        </div>
                                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400 mt-0.5">
                                            <span x-text="selectedApp.student_name"></span> <span class="font-mono text-slate-400 dark:text-slate-500">(<span x-text="selectedApp.student_id"></span>)</span>
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="setAllStatus('approved')" 
                                                class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl text-[10px] font-bold transition-all shadow-2xs cursor-pointer">
                                            ✓ Setujui Semua (OK)
                                        </button>
                                        <button type="button" @click="setAllStatus('rejected')" 
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 dark:hover:bg-rose-900 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-xl text-[10px] font-bold transition-all shadow-2xs cursor-pointer">
                                            ✕ Tolak Semua
                                        </button>
                                        <button @click="openValidation = false" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors ml-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <form x-ref="validSeminarForm" :action="selectedApp.validate_url" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="px-6 py-5 space-y-5 max-h-[66vh] overflow-y-auto bg-white dark:bg-slate-900">
                                        <div>
                                            <div class="flex items-center justify-between mb-2.5">
                                                <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest block">
                                                    Verifikasi 5 Berkas Persyaratan:
                                                </label>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Klik nama berkas untuk melihat dokumen</span>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                @foreach($fileLabels as $field => $label)
                                                    <div class="p-3 rounded-2xl border flex flex-col justify-between gap-2 transition-all shadow-2xs"
                                                         :class="(selectedApp.file_reviews && selectedApp.file_reviews['{{ $field }}'] && selectedApp.file_reviews['{{ $field }}'].status === 'rejected') ? 'border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800'"
                                                         x-data="{ isRejected: (selectedApp.file_reviews && selectedApp.file_reviews['{{ $field }}'] && selectedApp.file_reviews['{{ $field }}'].status === 'rejected') }">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <a :href="selectedApp.files['{{ $field }}']" target="_blank" 
                                                               class="text-xs font-bold text-slate-900 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1.5 truncate group/filelink"
                                                               title="Buka {{ $label }}">
                                                                <span class="truncate">{{ $label }}</span>
                                                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover/filelink:text-indigo-600 dark:group-hover/filelink:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                            </a>

                                                            {{-- Radio Controls (OK / Tolak) Segmented Pill --}}
                                                            <div class="flex items-center p-0.5 bg-slate-200 dark:bg-slate-900 rounded-xl border border-slate-300 dark:border-slate-700 shrink-0">
                                                                <label class="px-2 py-1 rounded-lg text-[10px] cursor-pointer transition-all flex items-center gap-1"
                                                                       :class="!isRejected ? 'bg-emerald-600 text-white shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-bold'">
                                                                    <input type="radio" name="file_reviews[{{ $field }}][status]" value="approved" 
                                                                           @change="isRejected = false"
                                                                           :checked="!isRejected"
                                                                           class="sr-only">
                                                                    <span>✓ OK</span>
                                                                </label>
                                                                <label class="px-2 py-1 rounded-lg text-[10px] cursor-pointer transition-all flex items-center gap-1"
                                                                       :class="isRejected ? 'bg-rose-600 text-white shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-bold'">
                                                                    <input type="radio" name="file_reviews[{{ $field }}][status]" value="rejected" 
                                                                           @change="isRejected = true"
                                                                           :checked="isRejected"
                                                                           class="sr-only">
                                                                    <span>✕ Tolak</span>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        {{-- Note Input --}}
                                                        <input type="text" name="file_reviews[{{ $field }}][note]" 
                                                               :value="selectedApp.file_reviews && selectedApp.file_reviews['{{ $field }}'] ? selectedApp.file_reviews['{{ $field }}'].note : ''" 
                                                               placeholder="Catatan revisi jika berkas ditolak..." 
                                                               :class="isRejected ? 'border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950 text-rose-900 dark:text-rose-200 placeholder-rose-400 dark:placeholder-rose-400' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500'"
                                                               class="w-full rounded-xl text-xs font-medium px-3 py-1.5 border transition-all focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Final Decision & Admin Global Note in 2 Columns --}}
                                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                                    Status Akhir Pengajuan:
                                                </label>
                                                <select name="status" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs font-bold uppercase tracking-wider p-2.5 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-2xs">
                                                    <option value="approved" :selected="selectedApp.status === 'approved'">✓ SETUJUI (BERKAS VALID & LENGKAP)</option>
                                                    <option value="rejected" :selected="selectedApp.status === 'rejected'">✕ TOLAK (PERLU REVISI BERKAS)</option>
                                                    <option value="pending" :selected="selectedApp.status === 'pending'">⏳ TETAP MENUNGGU</option>
                                                </select>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">
                                                    Pilih "SETUJUI" jika seluruh 5 berkas telah lengkap dan sesuai syarat.
                                                </p>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                                    Catatan Admin (Global / Feedback Mahasiswa):
                                                </label>
                                                <textarea name="admin_feedback" rows="2" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs font-medium p-2.5 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-2xs" placeholder="Silakan upload ulang berkas yang ditolak..." x-text="selectedApp.admin_feedback || ''"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal Footer --}}
                                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                            Perubahan akan tersimpan dan dapat dilihat oleh mahasiswa.
                                        </span>
                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="openValidation = false" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">Batal</button>
                                            <button type="submit" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl uppercase tracking-wider shadow-xs shadow-orange-500/20 transition-all cursor-pointer">Simpan Keputusan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        {{-- GLOBAL PREVIEW APPLICATION MODAL WITH IN-APP VIEWER --}}
        <template x-teleport="body">
            <div x-show="openPreview" class="fixed inset-0 overflow-y-auto" style="z-index: 99999 !important;" x-cloak>
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="openPreview = false"></div>

                <div class="min-h-full flex items-center justify-center p-3 sm:p-5 text-center">
                    <div class="relative w-full max-w-6xl bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 my-4 flex flex-col max-h-[92vh]" @click.stop>
                        <template x-if="previewApp">
                            <div class="flex flex-col h-full overflow-hidden">
                                {{-- Modal Header --}}
                                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/90 shrink-0">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight">Pratinjau Pendaftaran Seminar Proposal</h3>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider"
                                                      :class="{
                                                          'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800': previewApp.status === 'approved',
                                                          'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800': previewApp.status === 'rejected',
                                                          'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800': previewApp.status === 'pending'
                                                      }"
                                                      x-text="previewApp.status === 'approved' ? '✓ DISETUJUI' : (previewApp.status === 'rejected' ? '✕ PERLU REVISI' : '⏳ MENUNGGU VALIDASI')"></span>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                Gelombang: <span class="font-bold text-slate-700 dark:text-slate-300" x-text="previewApp.wave_name"></span> • Tanggal Daftar: <span class="font-mono text-slate-600 dark:text-slate-400" x-text="previewApp.created_at_formatted"></span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" @click="switchToValidation()" class="px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-xs shadow-orange-500/20 cursor-pointer flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span class="hidden sm:inline">Validasi Berkas</span>
                                        </button>
                                        <button type="button" @click="openPreview = false" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Modal Body: Split Pane --}}
                                <div class="flex-1 overflow-hidden grid grid-cols-1 lg:grid-cols-12 bg-slate-100 dark:bg-slate-950">
                                    {{-- Kolom Kiri: Info Mahasiswa & Daftar 5 Berkas (lg:col-span-4) --}}
                                    <div class="lg:col-span-4 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 p-5 overflow-y-auto space-y-4 max-h-[40vh] lg:max-h-none">
                                        {{-- Mahasiswa Card --}}
                                        <div class="bg-slate-50 dark:bg-slate-800/70 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-xs bg-white dark:bg-slate-700 shrink-0">
                                                    <img :src="previewApp.avatar_url" :alt="previewApp.student_name" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight truncate" x-text="previewApp.student_name"></h4>
                                                    <div class="flex items-center gap-1.5 mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                                        <span class="font-mono font-bold" x-text="previewApp.student_id"></span>
                                                        <template x-if="previewApp.entry_year">
                                                            <span>• Angkatan <span x-text="previewApp.entry_year"></span></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-3 pt-3 border-t border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between text-xs">
                                                <span class="text-slate-500 dark:text-slate-400 truncate max-w-[170px]" x-text="previewApp.student_email"></span>
                                                <template x-if="previewApp.clean_phone">
                                                    <a :href="'https://wa.me/' + previewApp.clean_phone + '?text=' + previewApp.wa_message" target="_blank"
                                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 rounded-lg text-[10px] font-bold border border-emerald-200 dark:border-emerald-800 transition-colors">
                                                        <span>WhatsApp</span>
                                                    </a>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Judul Skripsi & Pembimbing --}}
                                        <div class="bg-slate-50 dark:bg-slate-800/70 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 space-y-2.5">
                                            <div>
                                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Judul Proposal Skripsi:</span>
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1 leading-relaxed" x-text="previewApp.thesis_title || '-'"></p>
                                            </div>
                                            <div class="pt-2 border-t border-slate-200/80 dark:border-slate-700/80 space-y-1.5 text-xs">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-4 h-4 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[8px] font-black shrink-0">1</span>
                                                    <span class="text-slate-700 dark:text-slate-300 font-medium truncate" x-text="previewApp.pembimbing1_name || 'Belum ada Pembimbing 1'"></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[8px] font-black shrink-0">2</span>
                                                    <span class="text-slate-700 dark:text-slate-300 font-medium truncate" x-text="previewApp.pembimbing2_name || 'Belum ada Pembimbing 2'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Catatan Admin Sebelumnya (jika ada) --}}
                                        <template x-if="previewApp.admin_feedback">
                                            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-2xl border border-amber-200 dark:border-amber-800 text-xs">
                                                <span class="text-[9px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400 block mb-1">Catatan Validasi Admin:</span>
                                                <p class="text-amber-900 dark:text-amber-200 font-medium italic" x-text="'&quot;' + previewApp.admin_feedback + '&quot;'"></p>
                                            </div>
                                        </template>

                                        {{-- Daftar 5 Berkas Persyaratan Interaktif --}}
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Pilih Berkas untuk Ditinjau:</span>
                                                <span class="text-[10px] font-bold text-orange-600 dark:text-orange-400">5 Dokumen</span>
                                            </div>
                                            <div class="space-y-1.5">
                                                @foreach($fileLabels as $field => $label)
                                                    <button type="button" 
                                                            @click="selectPreviewFile('{{ $field }}')"
                                                            class="w-full text-left p-3 rounded-xl border transition-all flex items-center justify-between gap-2 cursor-pointer shadow-2xs"
                                                            :class="previewFileKey === '{{ $field }}' ? 'border-orange-500 bg-orange-50 dark:bg-orange-950/40 ring-1 ring-orange-500' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/60'">
                                                        <div class="min-w-0 flex-1">
                                                            <span class="text-xs font-bold block truncate" 
                                                                  :class="previewFileKey === '{{ $field }}' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-800 dark:text-slate-200'">
                                                                {{ $label }}
                                                            </span>
                                                            <template x-if="previewApp.file_reviews && previewApp.file_reviews['{{ $field }}'] && previewApp.file_reviews['{{ $field }}'].note">
                                                                <span class="text-[9px] text-rose-600 dark:text-rose-400 truncate block mt-0.5" x-text="'Catatan: ' + previewApp.file_reviews['{{ $field }}'].note"></span>
                                                            </template>
                                                        </div>
                                                        <div class="shrink-0">
                                                            <template x-if="previewApp.file_reviews && previewApp.file_reviews['{{ $field }}'] && previewApp.file_reviews['{{ $field }}'].status === 'rejected'">
                                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">✕ Tolak</span>
                                                            </template>
                                                            <template x-if="previewApp.file_reviews && previewApp.file_reviews['{{ $field }}'] && previewApp.file_reviews['{{ $field }}'].status === 'approved'">
                                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">✓ OK</span>
                                                            </template>
                                                            <template x-if="!previewApp.file_reviews || !previewApp.file_reviews['{{ $field }}'] || !previewApp.file_reviews['{{ $field }}'].status">
                                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">Lihat</span>
                                                            </template>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Kolom Kanan: In-App Document Viewer (lg:col-span-8) --}}
                                    <div class="lg:col-span-8 flex flex-col h-[65vh] lg:h-auto bg-slate-900 overflow-hidden">
                                        {{-- Viewer Toolbar --}}
                                        <div class="px-4 py-3 bg-slate-800 border-b border-slate-700 flex items-center justify-between gap-3 text-white text-xs shrink-0">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="text-orange-400 font-bold shrink-0">📄 Dokumen:</span>
                                                <span class="font-bold truncate" x-text="previewFileLabel"></span>
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0">
                                                {{-- Navigasi Sebelumnya / Selanjutnya --}}
                                                <button type="button" @click="prevPreviewFile()" class="p-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-300 hover:text-white transition-colors cursor-pointer" title="Dokumen Sebelumnya">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                </button>
                                                <button type="button" @click="nextPreviewFile()" class="p-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-300 hover:text-white transition-colors cursor-pointer" title="Dokumen Selanjutnya">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </button>

                                                <template x-if="previewRawUrl">
                                                    <div class="flex items-center gap-2">
                                                        <a :href="previewRawUrl" target="_blank" 
                                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-xs font-bold transition-all shadow-2xs"
                                                           title="Buka dokumen di tab baru">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                            <span class="hidden sm:inline">Buka Tab Baru</span>
                                                        </a>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Viewer Content Area --}}
                                        <div class="flex-1 relative bg-slate-950 flex items-center justify-center overflow-hidden">
                                            {{-- Loading State --}}
                                            <div x-show="previewLoading" class="absolute inset-0 z-20 bg-slate-950/80 backdrop-blur-xs flex flex-col items-center justify-center text-slate-300 gap-2">
                                                <svg class="w-8 h-8 animate-spin text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                <span class="text-xs font-bold uppercase tracking-wider">Memuat Dokumen...</span>
                                            </div>

                                            <template x-if="previewFileUrl">
                                                <iframe :src="previewFileUrl" 
                                                        class="w-full h-full border-0 bg-white"
                                                        allow="autoplay"
                                                        sandbox="allow-scripts allow-same-origin allow-popups allow-forms">
                                                </iframe>
                                            </template>

                                            <template x-if="!previewFileUrl">
                                                <div class="text-center p-8 text-slate-500">
                                                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <p class="text-sm font-bold text-slate-400">Berkas ini belum diunggah atau tautan kosong.</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Footer --}}
                                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0">
                                    <div>
                                        <template x-if="previewApp.download_zip_url">
                                            <a :href="previewApp.download_zip_url" 
                                               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                <span>Download ZIP (5 Berkas)</span>
                                            </a>
                                        </template>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="openPreview = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">Tutup</button>
                                        <button type="button" @click="switchToValidation()" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl uppercase tracking-wider shadow-xs shadow-orange-500/20 transition-all cursor-pointer">
                                            Lanjut ke Validasi →
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>

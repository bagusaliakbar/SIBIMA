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
             validationReviews: {},
             validationStatus: 'pending',
             validationFeedback: '',
             isSubmittingValidation: false,
             openRegisterModal: false,
             candidateTheses: {{ Js::from($candidateTheses->map(fn($t) => [
                 'id' => $t->id,
                 'student_name' => $t->student->name,
                 'student_identifier' => $t->student->identifier,
                 'title' => $t->display_title,
                 'p1_name' => $t->pembimbing1?->name ?? 'Belum ditentukan',
                 'p2_name' => $t->pembimbing2?->name ?? 'Belum ditentukan',
                 'is_acc_up' => $t->isAccUpFinal(),
             ])) }},
             selectedThesisId: '',
             selectedThesisData: null,
             bypassFiles: true,
             isRegistering: false,
             regBatchFolderUrl: '',
             regFiles: {
                 file_formulir: '',
                 file_pembayaran: '',
                 file_acc_pembimbing: '',
                 file_kartu_bimbingan: '',
                 file_skripsi: ''
             },
             applyRegBatchFolder() {
                 if (!this.regBatchFolderUrl || !this.regBatchFolderUrl.trim().startsWith('http')) {
                     window.sibimaToast('Silakan masukkan link Google Drive yang valid (dimulai dengan https://)', 'warning', 'Tautan Tidak Valid');
                     return;
                 }
                 const url = this.regBatchFolderUrl.trim();
                 Object.keys(this.regFiles).forEach(k => {
                     this.regFiles[k] = url;
                 });
             },
             onSelectThesis(id) {
                 this.selectedThesisId = id;
                 this.selectedThesisData = this.candidateTheses.find(t => t.id == id) || null;
             },
             openValidationModal(app) {
                 const keys = Object.keys(this.fileLabels);
                 const reviews = {};
                 keys.forEach(k => {
                     const existing = app && app.file_reviews && app.file_reviews[k] ? app.file_reviews[k] : null;
                     reviews[k] = {
                         status: existing && existing.status === 'rejected' ? 'rejected' : 'approved',
                         note: existing && existing.note ? existing.note : ''
                     };
                 });
                 this.validationReviews = reviews;
                 this.validationStatus = (app && app.status) ? app.status : 'pending';
                 this.validationFeedback = (app && app.admin_feedback) ? app.admin_feedback : '';
                 this.isSubmittingValidation = false;
                 this.selectedApp = app;
                 this.openValidation = true;
             },
             setFileReviewStatus(field, status) {
                 if (!this.validationReviews[field]) {
                     this.validationReviews[field] = { status: 'approved', note: '' };
                 }
                 this.validationReviews[field].status = status;
                 if (status === 'rejected') {
                     this.validationStatus = 'rejected';
                 } else if (this.rejectedReviewCount === 0 && this.validationStatus === 'rejected') {
                     this.validationStatus = 'approved';
                 }
             },
             setAllStatus(val) {
                 Object.keys(this.fileLabels).forEach(k => {
                     if (!this.validationReviews[k]) {
                         this.validationReviews[k] = { status: val, note: '' };
                     } else {
                         this.validationReviews[k].status = val;
                     }
                 });
                 this.validationStatus = val;
             },
             get rejectedReviewCount() {
                 return Object.values(this.validationReviews).filter(r => r && r.status === 'rejected').length;
             },
             get approvedReviewCount() {
                 return Object.values(this.validationReviews).filter(r => r && r.status === 'approved').length;
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

             isGoogleDrive() {
                 if (!this.previewRawUrl) return false;
                 return this.previewRawUrl.includes('drive.google.com') || this.previewRawUrl.includes('docs.google.com');
             },

             formatPreviewUrl(link) {
                 if (!link) return '';

                 // Google Drive: /file/d/FILE_ID/...
                 const gdriveFileMatch = link.match(/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/);
                 if (gdriveFileMatch && gdriveFileMatch[1]) {
                     return 'https://drive.google.com/file/d/' + gdriveFileMatch[1] + '/preview';
                 }

                 // Google Drive: open?id=FILE_ID or uc?id=FILE_ID
                 const gdriveIdMatch = link.match(/drive\.google\.com\/(?:open|uc)\?[^#]*\bid=([a-zA-Z0-9_-]+)/);
                 if (gdriveIdMatch && gdriveIdMatch[1]) {
                     return 'https://drive.google.com/file/d/' + gdriveIdMatch[1] + '/preview';
                 }

                 // Google Docs / Sheets / Slides
                 const gdocsMatch = link.match(/docs\.google\.com\/(document|spreadsheets|presentation)\/d\/([a-zA-Z0-9_-]+)/);
                 if (gdocsMatch && gdocsMatch[1] && gdocsMatch[2]) {
                     return 'https://docs.google.com/' + gdocsMatch[1] + '/d/' + gdocsMatch[2] + '/preview';
                 }

                 // Fallback for Google Drive links without standard format
                 if (link.includes('drive.google.com') && (link.includes('/view') || link.includes('/edit'))) {
                     return link.replace(/\/view(\?.*)?$/, '/preview').replace(/\/edit(\?.*)?$/, '/preview');
                 }

                 // Local storage private files
                 if (!link.startsWith('http://') && !link.startsWith('https://')) {
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
                 const app = this.previewApp;
                 this.openPreview = false;
                 if (app) this.openValidationModal(app);
             }
         }">
        @include('seminars.partials.admin-wave-header')

        @include('seminars.partials.admin-kpi-cards')

        {{-- TABLE CARD WITH INTEGRATED SEARCH & STATUS FILTER --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            {{-- Toolbar: Status Tabs & Action Controls --}}
            <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 flex flex-col gap-3.5">
                {{-- Row 1: Status Filter Tabs (Left) & Quick Wave Summary (Right) --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    {{-- Status Filter Tabs (Capsule / Segmented Control) --}}
                    <div class="inline-flex items-center gap-1 p-1 bg-slate-200/60 dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-700/80 overflow-x-auto max-w-full">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 px-2 hidden md:inline">Status:</span>

                        {{-- Semua Status --}}
                        <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'all', 'search' => $search]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($status ?? 'all') === 'all' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-slate-800/60' }}">
                            <span>Semua</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[11px] font-bold {{ ($status ?? 'all') === 'all' ? 'bg-white/20 text-white' : 'bg-slate-300/60 dark:bg-slate-700 text-slate-700 dark:text-slate-200' }}">
                                {{ $stats['total'] ?? 0 }}
                            </span>
                        </a>

                        {{-- Menunggu --}}
                        <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'pending', 'search' => $search]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($status ?? '') === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-200 hover:bg-amber-100/50 dark:hover:bg-amber-950/40' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Menunggu</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[11px] font-bold {{ ($status ?? '') === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200' }}">
                                {{ $stats['pending'] ?? 0 }}
                            </span>
                        </a>

                        {{-- Disetujui --}}
                        <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'approved', 'search' => $search]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($status ?? '') === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-200 hover:bg-emerald-100/50 dark:hover:bg-emerald-950/40' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Disetujui</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[11px] font-bold {{ ($status ?? '') === 'approved' ? 'bg-white/20 text-white' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200' }}">
                                {{ $stats['approved'] ?? 0 }}
                            </span>
                        </a>

                        {{-- Ditolak / Revisi --}}
                        <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => 'rejected', 'search' => $search]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($status ?? '') === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'text-rose-700 dark:text-rose-400 hover:text-rose-900 dark:hover:text-rose-200 hover:bg-rose-100/50 dark:hover:bg-rose-950/40' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Perlu Revisi</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[11px] font-bold {{ ($status ?? '') === 'rejected' ? 'bg-white/20 text-white' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200' }}">
                                {{ $stats['rejected'] ?? 0 }}
                            </span>
                        </a>
                    </div>

                    {{-- Wave & Filter Indicator (Right) --}}
                    <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 hidden sm:flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ $activeWave ? $activeWave->title : 'Semua Gelombang' }}</span>
                    </div>
                </div>

                {{-- Row 2: Search Bar (Left) & Action Buttons (Right) --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-200/70 dark:border-slate-700/60">
                    {{-- Search Bar Form --}}
                    <form action="{{ route('seminar-applications.index') }}" method="GET" class="relative flex items-center w-full sm:w-72 md:w-80 lg:w-96 min-w-0">
                        @if(!empty($selectedWaveId))
                            <input type="hidden" name="wave_id" value="{{ $selectedWaveId }}">
                        @endif
                        @if(!empty($status) && $status !== 'all')
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif

                        <div class="relative w-full">
                            {{-- Search Icon / Submit Button --}}
                            <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 dark:text-slate-400 hover:text-orange-600 transition-colors cursor-pointer" title="Cari" aria-label="Cari">
                                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>

                            {{-- Input Field --}}
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Cari nama mahasiswa, NPM, judul skripsi..."
                                   class="w-full h-10 pl-9 pr-9 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all shadow-2xs">

                            {{-- Reset / Clear Button --}}
                            @if($search)
                                <a href="{{ route('seminar-applications.index', ['wave_id' => $selectedWaveId, 'status' => $status]) }}"
                                   class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                                   title="Reset Pencarian" aria-label="Reset Pencarian">
                                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </form>

                    {{-- Action Buttons: Daftarkan Mahasiswa, Export Excel, Rekap Pembayaran Dropdown --}}
                    <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                        {{-- Daftarkan Mahasiswa Button --}}
                        <button type="button"
                                @click="openRegisterModal = true"
                                class="h-10 inline-flex items-center justify-center gap-1.5 px-3.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-colors shadow-sm shrink-0 whitespace-nowrap cursor-pointer"
                                title="Daftarkan Mahasiswa ke Seminar Proposal">
                            <svg class="w-4 h-4 text-white shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="whitespace-nowrap">Daftarkan Mahasiswa</span>
                        </button>

                        {{-- Export Excel Button --}}
                        <a href="{{ route('seminar-applications.export-excel', ['wave_id' => $selectedWaveId, 'status' => $status, 'search' => $search]) }}"
                           class="h-10 inline-flex items-center justify-center gap-1.5 px-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors shadow-sm shrink-0 whitespace-nowrap cursor-pointer"
                           title="Export Data Validasi Seminar ke Excel">
                            <svg class="w-4 h-4 text-white shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Export Excel</span>
                        </a>

                        {{-- Dropdown Rekap Pembayaran (PDF & Word) --}}
                        <div x-data="{ openRekap: false }" @click.away="openRekap = false" class="relative shrink-0">
                            <button type="button"
                                    @click="openRekap = !openRekap"
                                    :aria-expanded="openRekap"
                                    class="h-10 inline-flex items-center justify-center gap-1.5 px-3.5 bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-colors shadow-2xs shrink-0 whitespace-nowrap cursor-pointer"
                                    title="Unduh Rekap Bukti Pembayaran">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="whitespace-nowrap">Rekap Pembayaran</span>
                                <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': openRekap }" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            {{-- Dropdown Menu --}}
                            <div x-show="openRekap" 
                                 x-cloak 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
                                 class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 py-2 z-30">
                                
                                <div class="px-3.5 py-1 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Pilih Format Dokumen
                                </div>

                                {{-- PDF --}}
                                <a href="{{ route('seminar-applications.export-payment-pdf', ['wave_id' => $selectedWaveId, 'status' => $status, 'search' => $search]) }}" 
                                   class="flex items-center gap-3 px-3.5 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                                    <span class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-slate-100">Format PDF (.pdf)</p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal">Layout resmi siap cetak & arsip</p>
                                    </div>
                                </a>

                                <div class="my-1 border-t border-slate-100 dark:border-slate-700/60"></div>

                                {{-- Word --}}
                                <a href="{{ route('seminar-applications.export-payment-word', ['wave_id' => $selectedWaveId, 'status' => $status, 'search' => $search]) }}" 
                                   class="flex items-center gap-3 px-3.5 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-950/40 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <span class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-slate-100">Format Word (.doc)</p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal">Dapat disunting & disesuaikan</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('seminars.partials.admin-mobile-cards')

            {{-- Table Content --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/70 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-[11px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700/80">
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
                                $hasDigitalForm = $app->hasDigitalForm();
                                $formulirUrl = $hasDigitalForm
                                    ? route('seminar-applications.form-pdf', ['application' => $app->id, 'inline' => 1])
                                    : $app->file_formulir;
                                $formulirDownloadUrl = $hasDigitalForm
                                    ? route('seminar-applications.form-pdf', $app->id)
                                    : null;

                                $appData = [
                                    'id' => $app->id,
                                    'validate_url' => route('seminar-applications.validate', $app->id),
                                    'download_zip_url' => route('seminar-applications.download-zip', $app->id),
                                    'has_digital_form' => $hasDigitalForm,
                                    'form_pdf_download_url' => $formulirDownloadUrl,
                                    'form_data' => $hasDigitalForm ? $app->getResolvedFormData() : null,
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
                                        'file_formulir' => $formulirUrl,
                                    ]
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors group">
                                <td class="py-4 px-6 max-w-md">
                                    <div class="font-black text-slate-900 dark:text-white uppercase tracking-tight text-sm">
                                        {{ $app->thesis->student->name }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="font-mono text-[11px] font-bold px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded border border-slate-200 dark:border-slate-700">
                                            {{ $app->thesis->student->identifier }}
                                        </span>
                                        @if($app->created_at)
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400 font-bold">
                                                • Daftar: {{ $app->created_at->translatedFormat('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Thesis Title --}}
                                    @if($app->thesis->title)
                                        <div class="mt-2.5 text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-700/60">
                                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-0.5">Judul Skripsi:</span>
                                            {{ $app->thesis->title }}
                                        </div>
                                    @endif

                                    {{-- Supervisors --}}
                                    <div class="mt-2.5 flex flex-wrap gap-2">
                                        @if($app->thesis->pembimbing1)
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[11px]">
                                                <span class="w-4 h-4 rounded bg-slate-700 dark:bg-slate-600 text-white flex items-center justify-center text-[10px] font-bold">1</span>
                                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[150px]">{{ $app->thesis->pembimbing1->name }}</span>
                                            </div>
                                        @endif
                                        @if($app->thesis->pembimbing2)
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-blue-50/80 dark:bg-blue-950/50 border border-blue-200/80 dark:border-blue-800/60 text-[11px]">
                                                <span class="w-4 h-4 rounded bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold">2</span>
                                                <span class="font-bold text-blue-900 dark:text-blue-200 truncate max-w-[150px]">{{ $app->thesis->pembimbing2->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-wrap gap-1.5 max-w-[280px]">
                                        @php
                                            $files = [
                                                ['key' => 'file_acc_pembimbing', 'label' => 'Bukti ACC', 'path' => $app->file_acc_pembimbing, 'is_digital' => false],
                                                ['key' => 'file_pembayaran', 'label' => 'Bukti Bayar', 'path' => $app->file_pembayaran, 'is_digital' => false],
                                                ['key' => 'file_kartu_bimbingan', 'label' => 'Logbook', 'path' => $app->file_kartu_bimbingan, 'is_digital' => false],
                                                ['key' => 'file_skripsi', 'label' => 'Draf Skripsi', 'path' => $app->file_skripsi, 'is_digital' => false],
                                                ['key' => 'file_formulir', 'label' => $hasDigitalForm ? 'Formulir (PDF)' : 'Formulir', 'path' => $formulirUrl, 'is_digital' => $hasDigitalForm],
                                            ];
                                        @endphp
                                        @foreach($files as $file)
                                            @php
                                                $review = $app->file_reviews[$file['key']] ?? null;
                                                $isRejected = ($review['status'] ?? '') === 'rejected';
                                            @endphp
                                            @if(!empty($file['path']))
                                                <a href="{{ $file['path'] }}" target="_blank"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 border {{ $isRejected ? 'border-rose-300 dark:border-rose-800 bg-rose-50/60 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300' : ($file['is_digital'] ? 'border-blue-200 dark:border-blue-800/60 bg-blue-50/70 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:border-orange-400 dark:hover:border-orange-600 hover:text-orange-600 dark:hover:text-orange-400') }} rounded-lg text-[11px] font-bold transition-colors shadow-2xs"
                                                   title="{{ $isRejected ? 'Ditolak: ' . ($review['note'] ?? 'Perlu revisi') : ($file['is_digital'] ? 'Lihat PDF Formulir Digital SIBIMA' : 'Lihat ' . $file['label']) }}">
                                                    <span>{{ $file['label'] }}</span>
                                                    @if($isRejected)
                                                        <svg class="w-3 h-3 text-rose-600 dark:text-rose-400 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    @elseif($file['is_digital'])
                                                        <svg class="w-3 h-3 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    @else
                                                        <svg class="w-3 h-3 text-slate-400 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    @endif
                                                </a>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-lg text-[11px] font-medium">
                                                    {{ $file['label'] }} (Kosong)
                                                </span>
                                            @endif
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
                                            <p class="text-[11px] text-rose-600 dark:text-rose-400 font-bold mt-1 max-w-[140px] truncate bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded border border-rose-100 dark:border-rose-900/50"
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
                                                class="px-3 py-2 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/50 dark:hover:bg-blue-900/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 rounded-xl text-[11px] font-bold uppercase tracking-wider transition-colors shadow-2xs cursor-pointer flex items-center gap-1.5"
                                                title="Pratinjau Lengkap Pendaftaran Seminar">
                                            <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Preview</span>
                                        </button>
                                        <button type="button"
                                                @click="openValidationModal({{ json_encode($appData) }})"
                                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-[11px] font-bold rounded-xl uppercase tracking-wider transition-colors shadow-xs cursor-pointer">
                                            Validasi
                                        </button>
                                        <form action="{{ route('seminar-applications.destroy', $app) }}"
                                              method="POST"
                                              class="inline"
                                              data-confirm="Apakah Anda yakin ingin MEMBATALKAN &amp; MENGHAPUS pengajuan seminar proposal untuk mahasiswa {{ $app->thesis->student->name }}?&#10;&#10;Mahasiswa dapat mengajukan ulang berkas pendaftaran baru."
                                              data-confirm-title="Batalkan Pengajuan Seminar"
                                              data-confirm-type="danger"
                                              data-confirm-button="Ya, Batalkan & Hapus">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Batalkan & Hapus Pengajuan Seminar" aria-label="Batalkan & Hapus Pengajuan Seminar" 
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

        @include('seminars.partials.admin-validation-modals')

        @include('seminars.partials.admin-register-modal')
    </div>
</x-app-layout>

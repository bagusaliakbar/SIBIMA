<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Katalog Pustaka Skripsi', 'route' => null]
            ]" />
            <div class="flex flex-wrap items-center gap-2.5">
                @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                    <!-- Export Segmented Group -->
                    <div class="inline-flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xs divide-x divide-slate-200 dark:divide-slate-700 overflow-hidden">
                        <a href="{{ route('repositories.export-excel', request()->query()) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors" 
                           title="Ekspor Katalog ke Excel Sesuai Filter">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Excel</span>
                        </a>
                        <a href="{{ route('repositories.export-pdf', request()->query()) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors" 
                           title="Ekspor Katalog ke PDF Sesuai Filter">
                            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            <span>PDF</span>
                        </a>
                    </div>
                @endif

                <!-- Instant Similarity Checker (Primary Feature Action) -->
                <button type="button" 
                        @click="$dispatch('toggle-checker')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl shadow-md shadow-orange-500/20 hover:scale-[1.02] active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Cek Kemiripan Judul</span>
                </button>

                @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                    <!-- Sync Skripsi Lulus SIBIMA -->
                    <form action="{{ route('repositories.sync-completed') }}"
                          method="POST"
                          class="inline"
                          data-confirm="Tarik dan arsipkan semua skripsi mahasiswa yang telah lulus/completed di SIBIMA ke katalog pustaka?"
                          data-confirm-title="Sinkronisasi Skripsi SIBIMA"
                          data-confirm-type="emerald"
                          data-confirm-button="Ya, Sinkronkan">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all cursor-pointer"
                                title="Sinkronisasi otomatis semua skripsi mahasiswa SIBIMA yang sudah berstatus Lulus / Selesai">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>Sync Skripsi SIBIMA</span>
                        </button>
                    </form>

                    <!-- Sync Repositori UNSUB (FASILKOM & BAB 1) -->
                    <button type="button"
                            onclick="openUnsubSyncModal()" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/50 dark:hover:bg-purple-900/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/80 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all cursor-pointer"
                            title="Tarik & Perkaya Skripsi Fasilkom dari Repositori Universitas Subang">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Sync Repositori UNSUB</span>
                    </button>

                    <!-- Migrasi Portal (Secondary Action) -->
                    <button type="button"
                            onclick="startSync()" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all cursor-pointer">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Migrasi Portal</span>
                    </button>

                    <!-- Import Arsip (Secondary Action) -->
                    <a href="{{ route('repositories.import.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span>Import Arsip</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6" 
         x-data="{ 
            showChecker: false,
            testTitle: '',
            isChecking: false,
            checkResults: [],
            hasChecked: false,
            errorMessage: '',
            viewMode: localStorage.getItem('sibima_repo_view_mode') || 'grid',

            init() {
                this.$watch('viewMode', val => localStorage.setItem('sibima_repo_view_mode', val));
            },

            // Abstract Detail Modal State
            abstractModalOpen: false,
            abstractData: {
                id: null,
                name: '',
                identifier: '',
                year: '',
                title: '',
                pembimbing1: '',
                pembimbing2: '',
                abstract: '',
                file_path: '',
                file_path_bab2: '',
                file_path_bab3: '',
                file_path_bab4: '',
                file_path_bab5: '',
                file_path_bab6: '',
                badge: {}
            },
            openAbstractModal(repo, badge) {
                this.abstractData = {
                    id: repo.id,
                    name: repo.name || '',
                    identifier: repo.identifier || '-',
                    year: repo.year || '',
                    title: repo.title || '',
                    pembimbing1: repo.pembimbing1 || '',
                    pembimbing2: repo.pembimbing2 || '',
                    abstract: repo.abstract || '',
                    file_path: repo.file_path || '',
                    file_path_bab2: repo.file_path_bab2 || '',
                    file_path_bab3: repo.file_path_bab3 || '',
                    file_path_bab4: repo.file_path_bab4 || '',
                    file_path_bab5: repo.file_path_bab5 || '',
                    file_path_bab6: repo.file_path_bab6 || '',
                    badge: badge || {}
                };
                this.abstractModalOpen = true;
            },

            // Quick Edit Modal State
            editModalOpen: false,
            editData: {
                id: null,
                name: '',
                identifier: '',
                year: '',
                title: '',
                pembimbing1: '',
                pembimbing2: '',
                abstract: ''
            },
            openEditModal(repo) {
                this.editData = {
                    id: repo.id,
                    name: repo.name || '',
                    identifier: repo.identifier || '',
                    year: repo.year || '',
                    title: repo.title || '',
                    pembimbing1: repo.pembimbing1 || '',
                    pembimbing2: repo.pembimbing2 || '',
                    abstract: repo.abstract || ''
                };
                this.editModalOpen = true;
            },

            // Quick Delete Modal State
            deleteModalOpen: false,
            deleteData: {
                id: null,
                name: '',
                title: ''
            },
            openDeleteModal(repo) {
                this.deleteData = {
                    id: repo.id,
                    name: repo.name || '',
                    title: repo.title || ''
                };
                this.deleteModalOpen = true;
            },

            // In-App PDF Reader Modal State
            pdfReaderOpen: false,
            pdfRepo: {
                id: null,
                name: '',
                identifier: '',
                year: '',
                title: '',
                file_path: '',
                file_path_bab2: '',
                file_path_bab3: '',
                file_path_bab4: '',
                file_path_bab5: '',
                file_path_bab6: ''
            },
            pdfChapter: 'bab1',
            pdfCurrentPage: 1,
            pdfTotalPages: 0,
            pdfScale: 1.25,
            pdfIsLoading: false,
            pdfLoadingError: null,
            pdfIsRendering: false,
            pdfPagePending: null,
            pdfSearchQuery: '',
            pdfSearchMatches: [],
            pdfCurrentMatchIndex: -1,
            pdfIsSearching: false,
            pdfSearchStatus: '',
            pdfReadOnly: true,
            pdfFullscreen: false,
            pdfPagesTextCache: {},
            pdfTextIndexed: false,

            async openPdfReader(repo, chapter = 'bab1') {
                this.abstractModalOpen = false; // Langsung tutup modal abstrak agar viewer naskah tampil penuh tanpa terhalang
                this.pdfRepo = {
                    id: repo.id,
                    name: repo.name || '',
                    identifier: repo.identifier || '-',
                    year: repo.year || '',
                    title: repo.title || '',
                    file_path: repo.file_path || '',
                    file_path_bab2: repo.file_path_bab2 || '',
                    file_path_bab3: repo.file_path_bab3 || '',
                    file_path_bab4: repo.file_path_bab4 || '',
                    file_path_bab5: repo.file_path_bab5 || '',
                    file_path_bab6: repo.file_path_bab6 || ''
                };
                if (chapter === 'bab6' && repo.file_path_bab6) {
                    this.pdfChapter = 'bab6';
                } else if (chapter === 'bab5' && repo.file_path_bab5) {
                    this.pdfChapter = 'bab5';
                } else if (chapter === 'bab4' && repo.file_path_bab4) {
                    this.pdfChapter = 'bab4';
                } else if (chapter === 'bab3' && repo.file_path_bab3) {
                    this.pdfChapter = 'bab3';
                } else if (chapter === 'bab2' && repo.file_path_bab2) {
                    this.pdfChapter = 'bab2';
                } else {
                    this.pdfChapter = 'bab1';
                }
                this.pdfCurrentPage = 1;
                this.pdfTotalPages = 0;
                window._sibimaPdfDoc = null;
                this.pdfIsRendering = false;
                this.pdfPagePending = null;
                if (window._sibimaPdfRenderTask) {
                    try { window._sibimaPdfRenderTask.cancel(); } catch (e) {}
                    window._sibimaPdfRenderTask = null;
                }
                
                // Responsif skala pembacaan awal
                const screenW = window.innerWidth || 1200;
                if (screenW >= 1440) {
                    this.pdfScale = 1.35;
                } else if (screenW >= 1024) {
                    this.pdfScale = 1.2;
                } else if (screenW >= 768) {
                    this.pdfScale = 1.05;
                } else {
                    this.pdfScale = Math.max(0.6, Math.min(0.95, Math.round(((screenW - 32) / 595) * 100) / 100));
                }

                this.pdfSearchQuery = '';
                this.pdfSearchMatches = [];
                this.pdfCurrentMatchIndex = -1;
                this.pdfSearchStatus = '';
                this.pdfPagesTextCache = {};
                this.pdfTextIndexed = false;
                this.pdfReaderOpen = true;

                await this.$nextTick();
                const container = document.getElementById('pdfViewerContainer');
                if (container) {
                    container.scrollTop = 0;
                    container.scrollLeft = 0;
                }
                await this.loadPdf();
            },

            closePdfReader() {
                if (window._sibimaPdfRenderTask) {
                    try { window._sibimaPdfRenderTask.cancel(); } catch (e) {}
                    window._sibimaPdfRenderTask = null;
                }
                window._sibimaPdfDoc = null;
                this.pdfReaderOpen = false;
                this.pdfIsLoading = false;
                this.pdfIsRendering = false;
                this.pdfLoadingError = null;
                if (document.fullscreenElement) {
                    document.exitFullscreen().catch(() => {});
                }
            },

            async switchChapter(chapter) {
                if (this.pdfChapter === chapter) return;
                if (chapter === 'bab1' && !this.pdfRepo.file_path) return;
                if (chapter === 'bab2' && !this.pdfRepo.file_path_bab2) return;
                if (chapter === 'bab3' && !this.pdfRepo.file_path_bab3) return;
                if (chapter === 'bab4' && !this.pdfRepo.file_path_bab4) return;
                if (chapter === 'bab5' && !this.pdfRepo.file_path_bab5) return;
                if (chapter === 'bab6' && !this.pdfRepo.file_path_bab6) return;

                if (window._sibimaPdfRenderTask) {
                    try { window._sibimaPdfRenderTask.cancel(); } catch (e) {}
                    window._sibimaPdfRenderTask = null;
                }

                this.pdfChapter = chapter;
                this.pdfCurrentPage = 1;
                this.pdfSearchQuery = '';
                this.pdfSearchMatches = [];
                this.pdfCurrentMatchIndex = -1;
                this.pdfSearchStatus = '';
                this.pdfPagesTextCache = {};
                this.pdfTextIndexed = false;
                const container = document.getElementById('pdfViewerContainer');
                if (container) {
                    container.scrollTop = 0;
                    container.scrollLeft = 0;
                }
                await this.loadPdf();
            },

            async loadPdf() {
                this.pdfIsLoading = true;
                this.pdfLoadingError = null;
                window._sibimaPdfDoc = null;
                this.pdfTotalPages = 0;
                this.pdfIsRendering = false;
                this.pdfPagePending = null;

                const streamUrl = `/repositories/${this.pdfRepo.id}/${this.pdfChapter}`;

                try {
                    const lib = window.pdfjsLib || (typeof pdfjsLib !== 'undefined' ? pdfjsLib : null);
                    if (!lib) {
                        throw new Error('Pustaka Mozilla PDF.js belum selesai dimuat di browser.');
                    }
                    
                    // Pastikan worker selalu menggunakan Blob URL wrapper dengan importScripts (kompatibel 100% lokal & CDN)
                    try {
                        const localWorker = (window.location.origin || '') + '{{ asset('assets/pdfjs/pdf.worker.min.js') }}'.replace(/^https?:\/\/[^\/]+/, '');
                        const workerCode = `
                            try {
                                importScripts('${localWorker}');
                            } catch(e) {
                                importScripts('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js');
                            }
                        `;
                        const blob = new Blob([workerCode], { type: 'application/javascript' });
                        lib.GlobalWorkerOptions.workerSrc = URL.createObjectURL(blob);
                    } catch(e) {
                        lib.GlobalWorkerOptions.workerSrc = '{{ asset('assets/pdfjs/pdf.worker.min.js') }}';
                    }

                    const loadingTask = lib.getDocument({
                        url: streamUrl,
                        withCredentials: false
                    });

                    window._sibimaPdfDoc = await loadingTask.promise;
                    this.pdfTotalPages = window._sibimaPdfDoc.numPages;
                    this.pdfCurrentPage = 1;

                    await this.$nextTick();
                    await this.renderPage(this.pdfCurrentPage);

                } catch (err) {
                    console.error('[PDF Viewer] Error loading PDF:', err);
                    let msg = err.message || 'Gagal memuat dokumen PDF.';
                    if (msg.includes('Failed to fetch')) {
                        msg = 'Koneksi ke server naskah terputus atau terhambat. Silakan coba lagi atau gunakan tombol Buka di Tab Baru.';
                    }
                    this.pdfLoadingError = msg;
                } finally {
                    this.pdfIsLoading = false;
                }
            },

            async renderPage(num) {
                if (!window._sibimaPdfDoc) return;
                
                // Batalkan proses render sebelumnya bila masih berjalan (mencegah tabrakan render task PDF.js)
                if (window._sibimaPdfRenderTask) {
                    try {
                        window._sibimaPdfRenderTask.cancel();
                    } catch (e) {}
                    window._sibimaPdfRenderTask = null;
                }

                if (this.pdfIsRendering) {
                    this.pdfPagePending = num;
                    return;
                }
                this.pdfIsRendering = true;

                try {
                    await this.$nextTick();
                    const page = await window._sibimaPdfDoc.getPage(num);
                    
                    let canvas = document.getElementById('pdfViewerCanvas') || (this.$refs && this.$refs.pdfCanvas);
                    if (!canvas) {
                        await this.$nextTick();
                        canvas = document.getElementById('pdfViewerCanvas') || (this.$refs && this.$refs.pdfCanvas);
                    }
                    if (!canvas) {
                        await new Promise(r => setTimeout(r, 120));
                        canvas = document.getElementById('pdfViewerCanvas') || (this.$refs && this.$refs.pdfCanvas);
                    }
                    if (!canvas) {
                        this.pdfLoadingError = 'Elemen kanvas penampil dokumen belum terpasang di browser.';
                        this.pdfIsRendering = false;
                        return;
                    }

                    const ctx = canvas.getContext('2d');
                    if (!ctx) {
                        this.pdfLoadingError = 'Browser tidak mendukung 2D Canvas context.';
                        this.pdfIsRendering = false;
                        return;
                    }

                    const dpr = window.devicePixelRatio || 1;
                    
                    // Gunakan viewport dengan skala yang dikalikan DPR langsung agar bebas distorsi matriks
                    const viewport = page.getViewport({ scale: this.pdfScale * dpr });

                    canvas.width = Math.floor(viewport.width);
                    canvas.height = Math.floor(viewport.height);
                    canvas.style.width = Math.floor(viewport.width / dpr) + 'px';
                    canvas.style.height = Math.floor(viewport.height / dpr) + 'px';

                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };

                    window._sibimaPdfRenderTask = page.render(renderContext);
                    await window._sibimaPdfRenderTask.promise;
                    window._sibimaPdfRenderTask = null;

                    // Terapkan watermark proteksi resmi arsip digital bila mode proteksi aktif
                    if (this.pdfReadOnly) {
                        this.drawWatermark(ctx, canvas.width, canvas.height, dpr);
                    }

                } catch (err) {
                    if (err?.name === 'RenderingCancelledException') {
                        return;
                    }
                    console.error('[PDF Viewer] Error rendering page:', err);
                    this.pdfLoadingError = 'Gagal menampilkan halaman ' + num + ': ' + (err.message || err);
                } finally {
                    this.pdfIsRendering = false;
                    if (this.pdfPagePending !== null) {
                        const next = this.pdfPagePending;
                        this.pdfPagePending = null;
                        this.renderPage(next);
                    }
                }
            },

            drawWatermark(ctx, width, height, dpr) {
                ctx.save();
                ctx.globalAlpha = 0.12;
                ctx.fillStyle = '#dc2626';
                ctx.font = 'bold ' + Math.max(12, Math.round(16 * this.pdfScale * dpr)) + 'px sans-serif';
                ctx.textAlign = 'center';

                const stepY = 180 * this.pdfScale * dpr;
                const stepX = 320 * this.pdfScale * dpr;

                for (let y = 60 * dpr; y < height + 200 * dpr; y += stepY) {
                    for (let x = -80 * dpr; x < width + 200 * dpr; x += stepX) {
                        ctx.save();
                        ctx.translate(x, y);
                        ctx.rotate(-32 * Math.PI / 180);
                        ctx.fillText('SIBIMA FASILKOM UNSUB • ARSIP DIGITAL RESMI', 0, 0);
                        ctx.restore();
                    }
                }
                ctx.restore();
            },

            changePage(delta) {
                const target = this.pdfCurrentPage + delta;
                if (target >= 1 && target <= this.pdfTotalPages) {
                    this.pdfCurrentPage = target;
                    const container = document.getElementById('pdfViewerContainer');
                    if (container) container.scrollTop = 0;
                    this.renderPage(this.pdfCurrentPage);
                }
            },

            goToPage(num) {
                let p = parseInt(num);
                if (isNaN(p)) p = 1;
                p = Math.max(1, Math.min(p, this.pdfTotalPages));
                this.pdfCurrentPage = p;
                const container = document.getElementById('pdfViewerContainer');
                if (container) container.scrollTop = 0;
                this.renderPage(this.pdfCurrentPage);
            },

            zoomIn() {
                if (this.pdfScale < 2.4) {
                    this.pdfScale = Math.round((this.pdfScale + 0.15) * 100) / 100;
                    this.renderPage(this.pdfCurrentPage);
                }
            },

            zoomOut() {
                if (this.pdfScale > 0.6) {
                    this.pdfScale = Math.round((this.pdfScale - 0.15) * 100) / 100;
                    this.renderPage(this.pdfCurrentPage);
                }
            },

            fitWidth() {
                const container = document.getElementById('pdfViewerContainer');
                if (!container || !window._sibimaPdfDoc) return;
                window._sibimaPdfDoc.getPage(this.pdfCurrentPage).then(page => {
                    const vp = page.getViewport({ scale: 1.0 });
                    const availableWidth = container.clientWidth - 56;
                    if (availableWidth > 200 && vp.width > 0) {
                        this.pdfScale = Math.round((availableWidth / vp.width) * 100) / 100;
                        this.pdfScale = Math.max(0.6, Math.min(this.pdfScale, 2.3));
                        this.renderPage(this.pdfCurrentPage);
                    }
                });
            },

            toggleReadOnly() {
                this.pdfReadOnly = !this.pdfReadOnly;
                this.renderPage(this.pdfCurrentPage);
            },

            toggleFullscreen() {
                const modalEl = document.getElementById('pdfReaderModalElement');
                if (!modalEl) return;
                if (!document.fullscreenElement) {
                    modalEl.requestFullscreen().then(() => {
                        this.pdfFullscreen = true;
                    }).catch(() => {});
                } else {
                    document.exitFullscreen().then(() => {
                        this.pdfFullscreen = false;
                    }).catch(() => {});
                }
            },

            async indexDocumentText() {
                if (!window._sibimaPdfDoc) return;
                const numPages = window._sibimaPdfDoc.numPages;
                for (let i = 1; i <= numPages; i++) {
                    if (!this.pdfPagesTextCache[i]) {
                        try {
                            const page = await window._sibimaPdfDoc.getPage(i);
                            const textContent = await page.getTextContent();
                            const str = textContent.items.map(item => item.str).join(' ');
                            this.pdfPagesTextCache[i] = str.toLowerCase();
                        } catch (e) {}
                    }
                }
            },

            async searchInPdf() {
                const q = (this.pdfSearchQuery || '').trim().toLowerCase();
                if (!q) {
                    this.pdfSearchMatches = [];
                    this.pdfCurrentMatchIndex = -1;
                    this.pdfSearchStatus = '';
                    return;
                }

                this.pdfIsSearching = true;
                this.pdfSearchMatches = [];
                this.pdfCurrentMatchIndex = -1;

                await this.indexDocumentText();

                for (let i = 1; i <= this.pdfTotalPages; i++) {
                    const pageText = this.pdfPagesTextCache[i] || '';
                    if (pageText.includes(q)) {
                        this.pdfSearchMatches.push(i);
                    }
                }

                this.pdfIsSearching = false;

                if (this.pdfSearchMatches.length > 0) {
                    this.pdfCurrentMatchIndex = 0;
                    this.pdfSearchStatus = `${this.pdfSearchMatches.length} hal cocok`;
                    this.goToPage(this.pdfSearchMatches[0]);
                } else {
                    this.pdfSearchStatus = '0 cocok';
                }
            },

            nextSearchMatch() {
                if (this.pdfSearchMatches.length === 0) return;
                this.pdfCurrentMatchIndex = (this.pdfCurrentMatchIndex + 1) % this.pdfSearchMatches.length;
                this.goToPage(this.pdfSearchMatches[this.pdfCurrentMatchIndex]);
                this.pdfSearchStatus = `Hal ${this.pdfSearchMatches[this.pdfCurrentMatchIndex]} (${this.pdfCurrentMatchIndex + 1}/${this.pdfSearchMatches.length})`;
            },

            prevSearchMatch() {
                if (this.pdfSearchMatches.length === 0) return;
                this.pdfCurrentMatchIndex = (this.pdfCurrentMatchIndex - 1 + this.pdfSearchMatches.length) % this.pdfSearchMatches.length;
                this.goToPage(this.pdfSearchMatches[this.pdfCurrentMatchIndex]);
                this.pdfSearchStatus = `Hal ${this.pdfSearchMatches[this.pdfCurrentMatchIndex]} (${this.pdfCurrentMatchIndex + 1}/${this.pdfSearchMatches.length})`;
            },

            async runTitleCheck() {
                const cleaned = this.testTitle.trim();
                if (!cleaned || cleaned.length < 10) {
                    this.errorMessage = 'Judul terlalu pendek. Masukkan rencana judul minimal 10 karakter (contoh: Sistem Informasi Pengelolaan...).';
                    this.hasChecked = false;
                    this.checkResults = [];
                    return;
                }
                const words = cleaned.split(/\s+/).filter(Boolean);
                if (words.length < 2) {
                    this.errorMessage = 'Judul harus terdiri dari minimal 2 kata yang bermakna.';
                    this.hasChecked = false;
                    this.checkResults = [];
                    return;
                }
                this.errorMessage = '';
                this.isChecking = true;
                this.hasChecked = false;
                try {
                    const res = await fetch('{{ route('theses.check-title') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title: cleaned })
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.errorMessage = data.message || 'Gagal menganalisis judul. Pastikan format judul sesuai.';
                        this.hasChecked = false;
                        this.checkResults = [];
                        return;
                    }
                    this.checkResults = data.similar || [];
                    this.hasChecked = true;
                } catch (err) {
                    this.errorMessage = 'Terjadi kendala jaringan saat menghubungi server.';
                    this.hasChecked = false;
                } finally {
                    this.isChecking = false;
                }
            }
         }"
         @toggle-checker.window="showChecker = !showChecker">

        @include('repositories.partials.tabs')

        @include('repositories.partials.kpi-cards')

        @include('repositories.partials.similarity-checker')

        @include('repositories.partials.filter-bar')

        @include('repositories.partials.grid-view')

        @include('repositories.partials.table-view')

        <!-- Pagination -->
        <div class="mt-6">
            {{ $repositories->links() }}
        </div>

        @include('repositories.partials.abstract-modal')

        @include('repositories.partials.pdf-reader-modal')

        @include('repositories.partials.admin-crud-modals')
    </div>

@include('repositories.partials.sync-modals')

@push('scripts')
<script src="{{ asset('assets/pdfjs/pdf.min.js') }}"></script>
<script>
    if (typeof pdfjsLib === 'undefined') {
        document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"><\/script>');
    }
    function initPdfJsWorker() {
        if (!window.pdfjsLib) return;
        try {
            const localWorker = (window.location.origin || '') + '{{ asset('assets/pdfjs/pdf.worker.min.js') }}'.replace(/^https?:\/\/[^\/]+/, '');
            const workerCode = `
                try {
                    importScripts('${localWorker}');
                } catch(e) {
                    importScripts('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js');
                }
            `;
            const blob = new Blob([workerCode], { type: 'application/javascript' });
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = URL.createObjectURL(blob);
        } catch(e) {
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ asset('assets/pdfjs/pdf.worker.min.js') }}';
        }
    }
    initPdfJsWorker();
    window.addEventListener('DOMContentLoaded', initPdfJsWorker);
</script>
@endpush
</x-app-layout>

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
                file_path_bab2: ''
            },
            pdfChapter: 'bab1',
            pdfDoc: null,
            pdfCurrentPage: 1,
            pdfTotalPages: 0,
            pdfScale: 1.25,
            pdfIsLoading: false,
            pdfLoadingError: null,
            pdfIsRendering: false,
            pdfPagePending: null,
            pdfCurrentRenderTask: null,
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
                    file_path_bab2: repo.file_path_bab2 || ''
                };
                this.pdfChapter = (chapter === 'bab2' && repo.file_path_bab2) ? 'bab2' : 'bab1';
                this.pdfCurrentPage = 1;
                this.pdfTotalPages = 0;
                this.pdfDoc = null;
                this.pdfIsRendering = false;
                this.pdfPagePending = null;
                if (this.pdfCurrentRenderTask) {
                    try { this.pdfCurrentRenderTask.cancel(); } catch (e) {}
                    this.pdfCurrentRenderTask = null;
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
                await this.loadPdf();
            },

            closePdfReader() {
                if (this.pdfCurrentRenderTask) {
                    try { this.pdfCurrentRenderTask.cancel(); } catch (e) {}
                    this.pdfCurrentRenderTask = null;
                }
                this.pdfReaderOpen = false;
                this.pdfDoc = null;
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

                if (this.pdfCurrentRenderTask) {
                    try { this.pdfCurrentRenderTask.cancel(); } catch (e) {}
                    this.pdfCurrentRenderTask = null;
                }

                this.pdfChapter = chapter;
                this.pdfCurrentPage = 1;
                this.pdfSearchQuery = '';
                this.pdfSearchMatches = [];
                this.pdfCurrentMatchIndex = -1;
                this.pdfSearchStatus = '';
                this.pdfPagesTextCache = {};
                this.pdfTextIndexed = false;
                await this.loadPdf();
            },

            async loadPdf() {
                this.pdfIsLoading = true;
                this.pdfLoadingError = null;
                this.pdfDoc = null;
                this.pdfTotalPages = 0;
                this.pdfIsRendering = false;
                this.pdfPagePending = null;

                const streamUrl = `/repositories/${this.pdfRepo.id}/${this.pdfChapter}`;

                try {
                    const lib = window.pdfjsLib || (typeof pdfjsLib !== 'undefined' ? pdfjsLib : null);
                    if (!lib) {
                        throw new Error('Pustaka Mozilla PDF.js belum selesai dimuat di browser.');
                    }
                    
                    // Pastikan worker selalu menggunakan origin yang sama persis (bebas Cross-Origin SecurityError)
                    const workerRel = '{{ asset('vendor/pdfjs/pdf.worker.min.js') }}'.replace(/^https?:\/\/[^\/]+/, '');
                    lib.GlobalWorkerOptions.workerSrc = (window.location.origin || '') + workerRel;

                    const loadingTask = lib.getDocument({
                        url: streamUrl,
                        withCredentials: true
                    });

                    this.pdfDoc = await loadingTask.promise;
                    this.pdfTotalPages = this.pdfDoc.numPages;
                    this.pdfCurrentPage = 1;

                    await this.$nextTick();
                    await this.renderPage(this.pdfCurrentPage);

                } catch (err) {
                    console.error('[PDF Viewer] Error loading PDF:', err);
                    this.pdfLoadingError = err.message || 'Gagal memuat dokumen PDF.';
                } finally {
                    this.pdfIsLoading = false;
                }
            },

            async renderPage(num) {
                if (!this.pdfDoc) return;
                
                // Batalkan proses render sebelumnya bila masih berjalan (mencegah tabrakan render task PDF.js)
                if (this.pdfCurrentRenderTask) {
                    try {
                        this.pdfCurrentRenderTask.cancel();
                    } catch (e) {}
                    this.pdfCurrentRenderTask = null;
                }

                if (this.pdfIsRendering) {
                    this.pdfPagePending = num;
                    return;
                }
                this.pdfIsRendering = true;

                try {
                    await this.$nextTick();
                    const page = await this.pdfDoc.getPage(num);
                    
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

                    this.pdfCurrentRenderTask = page.render(renderContext);
                    await this.pdfCurrentRenderTask.promise;
                    this.pdfCurrentRenderTask = null;

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
                    this.renderPage(this.pdfCurrentPage);
                }
            },

            goToPage(num) {
                let p = parseInt(num);
                if (isNaN(p)) p = 1;
                p = Math.max(1, Math.min(p, this.pdfTotalPages));
                this.pdfCurrentPage = p;
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
                if (!container || !this.pdfDoc) return;
                this.pdfDoc.getPage(this.pdfCurrentPage).then(page => {
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
                if (!this.pdfDoc) return;
                const numPages = this.pdfDoc.numPages;
                for (let i = 1; i <= numPages; i++) {
                    if (!this.pdfPagesTextCache[i]) {
                        try {
                            const page = await this.pdfDoc.getPage(i);
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

        <!-- STATISTICAL METRIC SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Metric 1: Total Arsip -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-4 hover:border-orange-200 dark:hover:border-orange-800/60 transition-all group">
                <div class="w-12 h-12 rounded-2xl bg-orange-500/10 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Total Arsip Skripsi</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ number_format($stats['total']) }} <span class="text-xs font-bold text-slate-400 dark:text-slate-500">Judul</span></h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">Database Arsip Alumni</p>
                </div>
            </div>

            <!-- Metric 2: Rentang Angkatan -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-4 hover:border-indigo-200 dark:hover:border-indigo-800/60 transition-all group">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Rentang Angkatan</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $stats['year_range'] }}</h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ $stats['total_years'] }} Angkatan Terdaftar</p>
                </div>
            </div>

            <!-- Metric 3: Topik Populer -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-4 hover:border-emerald-200 dark:hover:border-emerald-800/60 transition-all group">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Topik Terpopuler</p>
                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 tracking-tight truncate max-w-[170px]" title="{{ $stats['top_topic'] }}">{{ $stats['top_topic'] }}</h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ $stats['top_topic_count'] }} Judul Terkait</p>
                </div>
            </div>

            <!-- Metric 4: Pembimbing Terdata -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-4 hover:border-purple-200 dark:hover:border-purple-800/60 transition-all group">
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Dosen Pembimbing</p>
                    <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ $stats['total_advisors'] }} <span class="text-xs font-bold text-slate-400 dark:text-slate-500">Dosen</span></h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">Riwayat Pembimbingan</p>
                </div>
            </div>
        </div>

        <!-- INSTANT SIMILARITY CHECKER DRAWER -->
        <div x-show="showChecker" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             x-cloak
             class="bg-gradient-to-br from-orange-500/5 via-white to-amber-500/5 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-3xl p-6 border-2 border-orange-200 dark:border-orange-500/30 shadow-xl space-y-4">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-orange-100 dark:border-slate-700/80 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-black shadow-md shadow-orange-500/30 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight">Cek Kemiripan Judul Skripsi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Ketik calon judul proposal Anda untuk mengecek kemiripan dengan repositori alumni & skripsi aktif secara real-time.</p>
                    </div>
                </div>
                <button type="button" @click="showChecker = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="runTitleCheck" class="space-y-2">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1 flex items-center">
                        <input type="text" 
                               x-model="testTitle" 
                               @input="errorMessage = ''; hasChecked = false;"
                               placeholder="Masukkan draf rencana judul skripsi (contoh: Sistem Informasi Pengelolaan Stok Barang...)" 
                               class="w-full pl-4 pr-12 py-3 bg-white dark:bg-slate-900 border border-orange-200 dark:border-slate-700 rounded-2xl text-xs font-semibold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all shadow-inner text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500">
                        <button type="button" 
                                x-show="testTitle" 
                                x-cloak
                                @click="testTitle = ''; errorMessage = ''; hasChecked = false; checkResults = [];" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer"
                                title="Hapus teks judul">
                            <div class="w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </button>
                    </div>
                    <button type="submit" 
                            :disabled="isChecking || !testTitle"
                            class="px-6 py-3 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-2xl font-bold text-xs shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2 shrink-0 disabled:opacity-50 cursor-pointer">
                        <template x-if="!isChecking">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <span>Analisis Orisinalitas</span>
                            </span>
                        </template>
                        <template x-if="isChecking">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Memeriksa Kemiripan...</span>
                            </span>
                        </template>
                    </button>
                </div>
                <template x-if="errorMessage">
                    <p class="text-xs font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1 pt-1" x-text="errorMessage"></p>
                </template>
            </form>

            <!-- Results Display -->
            <div x-show="hasChecked && !errorMessage" x-cloak class="space-y-3 pt-1">
                <template x-if="checkResults.length === 0">
                    <div class="p-4 bg-emerald-500/10 dark:bg-emerald-950/70 border border-emerald-500/30 dark:border-emerald-700/60 rounded-2xl flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black shrink-0 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-tight">Judul Aman / Orisinal!</h4>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400">Tidak ditemukan judul yang memiliki kemiripan signifikan (&ge;45%) di database pustaka alumni dan skripsi berjalan.</p>
                        </div>
                    </div>
                </template>

                <template x-if="checkResults.length > 0">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Ditemukan <span class="text-orange-600 dark:text-orange-400" x-text="checkResults.length"></span> Judul Serupa Terkait:
                            </p>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500">Batas toleransi kemiripan: &lt; 45%</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <template x-for="(item, idx) in checkResults" :key="idx">
                                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-lg"
                                              :class="item.percentage >= 70 ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300 border dark:border-rose-800/60' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 border dark:border-amber-800/60'"
                                              x-text="item.percentage + '% Mirip'"></span>
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500" x-text="item.source + ' (' + item.year + ')'"></span>
                                    </div>
                                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-snug" x-text="item.title"></h5>
                                    <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800">
                                        <span x-text="'Oleh: ' + item.student_name"></span>
                                        <span class="font-mono text-orange-600 dark:text-orange-400 font-bold" x-text="item.matched_words ? item.matched_words.length + ' kata cocok' : ''"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- ADVANCED FILTER & DISCOVERY CARD -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm space-y-5">
            <form action="{{ route('repositories.index') }}" method="GET" id="filterForm" class="space-y-4">
                <!-- Hidden Topic Field -->
                <input type="hidden" name="topic" id="topicInput" value="{{ $topic ?? 'all' }}">

                <!-- Search & Dropdowns Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    <!-- Keyword Search -->
                    <div class="lg:col-span-5 relative" x-data="{ q: '{{ addslashes($search) }}' }">
                        <label for="search" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1.5">Kata Kunci / Judul / Nama</label>
                        <div class="relative flex items-center">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   id="search" 
                                   x-model="q"
                                   placeholder="Cari judul, topik, NPM, abstrak..." 
                                   class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500">
                            <button type="button" 
                                    x-show="q" 
                                    x-cloak
                                    @click="q = ''; document.getElementById('search').value = ''; document.getElementById('filterForm').submit();"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer"
                                    title="Hapus pencarian">
                                <div class="w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-300">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Angkatan Dropdown -->
                    <div class="lg:col-span-3">
                        <label for="year" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1.5">Tahun Angkatan</label>
                        <select name="year" id="year" onchange="this.form.submit()" 
                                class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all cursor-pointer">
                            <option value="" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">Semua Angkatan</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">Angkatan {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dosen Pembimbing Dropdown -->
                    <div class="lg:col-span-4">
                        <label for="advisor" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1.5">Dosen Pembimbing</label>
                        <select name="advisor" id="advisor" onchange="this.form.submit()" 
                                class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all cursor-pointer">
                            <option value="" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">Semua Dosen Pembimbing</option>
                            @foreach($advisors as $adv)
                                <option value="{{ $adv }}" {{ $advisor == $adv ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">{{ $adv }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Horizontal Topic Filter Pills -->
                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60">
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 shrink-0 mr-1">Kategori:</span>
                        @foreach($topics as $key => $topData)
                            <button type="button" 
                                    onclick="document.getElementById('topicInput').value='{{ $key }}'; document.getElementById('filterForm').submit();"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 shrink-0 {{ ($topic ?? 'all') === $key ? 'bg-orange-600 text-white shadow-sm shadow-orange-500/30' : 'bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700/80' }}">
                                <span>{{ $topData['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </form>

            <!-- Active Filter Bar, Results Count & View Switcher -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 text-xs">
                <div class="flex items-center gap-2 flex-wrap text-slate-600 dark:text-slate-400">
                    <span class="font-bold">Menampilkan <strong class="text-slate-900 dark:text-white">{{ $repositories->total() }}</strong> dari {{ $totalCount }} pustaka</span>
                    
                    @if($search || $year || $advisor || ($topic && $topic !== 'all'))
                        <span class="text-slate-300 dark:text-slate-600">•</span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @if($search)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 text-[10px] font-bold border border-orange-200 dark:border-orange-800">
                                    "{{ $search }}"
                                </span>
                            @endif
                            @if($year)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold border border-indigo-200 dark:border-indigo-800">
                                    Angkatan {{ $year }}
                                </span>
                            @endif
                            @if($advisor)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold border border-purple-200 dark:border-purple-800">
                                    Pembimbing: {{ $advisor }}
                                </span>
                            @endif
                            @if($topic && $topic !== 'all')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 text-[10px] font-bold border border-amber-200 dark:border-amber-800">
                                    {{ $topics[$topic]['label'] ?? $topic }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    @if($search || $year || $advisor || ($topic && $topic !== 'all'))
                        <a href="{{ route('repositories.index') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reset Filter
                        </a>
                    @endif

                    <!-- Mode Tampilan Ganda (Grid vs Table) -->
                    <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 rounded-xl shadow-2xs">
                        <button type="button" 
                                @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span>Grid</span>
                        </button>
                        <button type="button" 
                                @click="viewMode = 'table'"
                                :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            <span>Tabel</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW MODE 1: REPOSITORY CARDS GRID -->
        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($repositories as $repo)
                @php
                    $badge = $repo->topic_badge;
                @endphp
                <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-200 hover:-translate-y-1 flex flex-col group">
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Header Meta Badges & Admin Actions -->
                            <div class="flex justify-between items-center gap-2 mb-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-[9px] font-black uppercase tracking-widest rounded-lg border border-slate-200 dark:border-slate-700">
                                        Angkatan {{ $repo->year }}
                                    </span>

                                    <span class="px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-lg border {{ $badge['bg'] }}">
                                        {{ $badge['label'] }}
                                    </span>

                                    @if($repo->file_path)
                                        <button type="button" 
                                                @click="openPdfReader({{ json_encode($repo) }}, 'bab1')" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/80 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                title="Baca Naskah BAB 1 di Web (In-App Reader)">
                                            <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span>BAB 1 PDF</span>
                                        </button>
                                    @endif

                                    @if($repo->file_path_bab2)
                                        <button type="button" 
                                                @click="openPdfReader({{ json_encode($repo) }}, 'bab2')" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/80 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                title="Baca Naskah BAB 2 di Web (In-App Reader)">
                                            <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span>BAB 2 PDF</span>
                                        </button>
                                    @endif
                                </div>

                                @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                    <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <!-- Quick Edit Button -->
                                        <button type="button" 
                                                @click="openEditModal({{ json_encode($repo) }})"
                                                class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 rounded-lg transition-all"
                                                title="Edit Data Arsip">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <!-- Quick Delete Button -->
                                        <button type="button" 
                                                @click="openDeleteModal({{ json_encode($repo) }})"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-all"
                                                title="Hapus Arsip">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Title (Click to open abstract) -->
                            <h3 @click="openAbstractModal({{ json_encode($repo) }}, {{ json_encode($badge) }})"
                                class="text-sm font-black text-slate-800 dark:text-slate-100 mb-2.5 leading-snug group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors line-clamp-3 cursor-pointer"
                                title="Klik untuk membaca detail & abstrak">
                                {{ $repo->title }}
                            </h3>
                            
                            <!-- Abstract Snippet -->
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 line-clamp-3 leading-relaxed">
                                {{ $repo->abstract ?: 'Tidak ada abstrak yang tersedia.' }}
                            </p>
                        </div>
                        
                        <!-- Footer Info -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700/80 mt-auto space-y-2.5">
                            <!-- Student -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200/60 dark:border-orange-800/40 flex items-center justify-center text-[10px] font-black shrink-0">
                                        {{ substr($repo->name, 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $repo->name }}</span>
                                </div>
                                <span class="text-[10px] font-mono font-bold text-slate-400 dark:text-slate-500 shrink-0">{{ $repo->identifier ?? '-' }}</span>
                            </div>
                            
                            <!-- Advisors -->
                            @if($repo->pembimbing1 || $repo->pembimbing2)
                                <div class="p-2.5 bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl space-y-1.5 text-[10px]">
                                    @if($repo->pembimbing1)
                                        <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300 min-w-0">
                                            <span class="px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-bold text-[8px] rounded border border-indigo-200/80 dark:border-indigo-800/80 shrink-0">P1</span>
                                            <span class="truncate font-bold text-slate-700 dark:text-slate-200">{{ $repo->pembimbing1 }}</span>
                                        </div>
                                    @endif
                                    @if($repo->pembimbing2)
                                        <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300 min-w-0">
                                            <span class="px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 font-bold text-[8px] rounded border border-purple-200/80 dark:border-purple-800/80 shrink-0">P2</span>
                                            <span class="truncate font-bold text-slate-700 dark:text-slate-200">{{ $repo->pembimbing2 }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Read Detail Button in Card -->
                            <button type="button" 
                                    @click="openAbstractModal({{ json_encode($repo) }}, {{ json_encode($badge) }})"
                                    class="w-full py-2 bg-slate-50 hover:bg-orange-50 dark:bg-slate-900 dark:hover:bg-orange-950/40 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 rounded-xl text-[11px] font-bold transition-all border border-slate-200/80 dark:border-slate-700 flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Lihat Detail Abstrak</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white dark:bg-slate-800 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 space-y-3">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-50 dark:bg-orange-950/30 text-orange-500 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Tidak Ada Pustaka Ditemukan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Tidak ada arsip skripsi yang cocok dengan kriteria pencarian dan filter Anda saat ini.</p>
                    <div class="pt-2">
                        <a href="{{ route('repositories.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-xs font-bold rounded-xl hover:bg-orange-700 transition-all shadow-sm">
                            Reset Filter Pencarian
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- VIEW MODE 2: COMPACT TABLE / LIST -->
        <div x-show="viewMode === 'table'" x-cloak class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50/75 dark:bg-slate-900/60 text-slate-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                            <th class="py-3.5 px-5">Angkatan & Mahasiswa</th>
                            <th class="py-3.5 px-5">Judul Skripsi & Topik</th>
                            <th class="py-3.5 px-5">Dosen Pembimbing</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                        @forelse($repositories as $repo)
                            @php
                                $badge = $repo->topic_badge;
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors group">
                                <!-- Student & Year -->
                                <td class="py-3.5 px-5 align-top">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-[9px] font-black uppercase tracking-wider rounded-md border border-slate-200 dark:border-slate-700">
                                                {{ $repo->year }}
                                            </span>
                                            <span class="font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight text-xs">{{ $repo->name }}</span>
                                        </div>
                                        <div class="text-[10px] font-mono font-semibold text-slate-400 dark:text-slate-500">
                                            NPM: {{ $repo->identifier ?? '-' }}
                                        </div>
                                    </div>
                                </td>

                                <!-- Title & Smart Topic Badge -->
                                <td class="py-3.5 px-5 align-top max-w-md whitespace-normal">
                                    <div class="space-y-1.5">
                                        <h4 class="font-bold text-slate-800 dark:text-slate-100 text-xs leading-snug line-clamp-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors cursor-pointer"
                                            @click="openAbstractModal({{ json_encode($repo) }}, {{ json_encode($badge) }})">
                                            {{ $repo->title }}
                                        </h4>
                                        <div class="flex items-center gap-2.5 flex-wrap">
                                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md border {{ $badge['bg'] }}">
                                                {{ $badge['label'] }}
                                            </span>
                                            @if($repo->file_path)
                                                <button type="button" 
                                                        @click="openPdfReader({{ json_encode($repo) }}, 'bab1')" 
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/80 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                        title="Baca Naskah BAB 1 di Web (In-App Reader)">
                                                    <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <span>BAB 1 PDF</span>
                                                </button>
                                            @endif
                                            @if($repo->file_path_bab2)
                                                <button type="button" 
                                                        @click="openPdfReader({{ json_encode($repo) }}, 'bab2')" 
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/80 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                        title="Baca Naskah BAB 2 di Web (In-App Reader)">
                                                    <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <span>BAB 2 PDF</span>
                                                </button>
                                            @endif
                                            @if($repo->abstract)
                                                <button type="button" 
                                                        @click="openAbstractModal({{ json_encode($repo) }}, {{ json_encode($badge) }})"
                                                        class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-0.5 cursor-pointer">
                                                    <span>Baca Abstrak</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Advisors -->
                                <td class="py-3.5 px-5 align-top">
                                    @if($repo->pembimbing1 || $repo->pembimbing2)
                                        <div class="space-y-1 max-w-xs text-[11px]">
                                            @if($repo->pembimbing1)
                                                <div class="flex items-center gap-1.5 truncate">
                                                    <span class="w-3.5 h-3.5 rounded bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-[8px] font-black border border-indigo-200 dark:border-indigo-800/80 shrink-0">1</span>
                                                    <span class="text-slate-700 dark:text-slate-300 font-semibold truncate">{{ $repo->pembimbing1 }}</span>
                                                </div>
                                            @endif
                                            @if($repo->pembimbing2)
                                                <div class="flex items-center gap-1.5 truncate">
                                                    <span class="w-3.5 h-3.5 rounded bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 flex items-center justify-center text-[8px] font-black border border-purple-200 dark:border-purple-800/80 shrink-0">2</span>
                                                    <span class="text-slate-700 dark:text-slate-300 font-semibold truncate">{{ $repo->pembimbing2 }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-[11px] italic">-</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-5 align-middle text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" 
                                                @click="openAbstractModal({{ json_encode($repo) }}, {{ json_encode($badge) }})"
                                                class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-orange-50 dark:hover:bg-orange-950/50 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 rounded-lg text-xs font-bold transition-all border border-slate-200/80 dark:border-slate-600 shadow-2xs cursor-pointer"
                                                title="Lihat Detail Abstrak">
                                            Detail
                                        </button>

                                        @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                            <button type="button" 
                                                    @click="openEditModal({{ json_encode($repo) }})"
                                                    class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 rounded-lg transition-all cursor-pointer"
                                                    title="Edit Data Arsip">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            <button type="button" 
                                                    @click="openDeleteModal({{ json_encode($repo) }})"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-all cursor-pointer"
                                                    title="Hapus Arsip">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-500 dark:text-slate-400">
                                    Tidak ada arsip skripsi yang cocok dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $repositories->links() }}
        </div>

        <!-- Modal Detail Abstrak Pustaka -->
        <template x-teleport="body">
            <div x-show="abstractModalOpen && !pdfReaderOpen" 
                 class="fixed inset-0 overflow-y-auto text-left" 
                 style="z-index: 99999 !important;" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="abstractModalOpen = false">
                        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                    </div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-slate-200 dark:border-slate-700 relative" 
                         style="z-index: 100000 !important;">
                        <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-lg" x-text="'Angkatan ' + abstractData.year"></span>
                                <span class="px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-lg border" :class="abstractData.badge?.bg" x-text="abstractData.badge?.label"></span>
                            </div>
                            <button type="button" @click="abstractModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold p-1 cursor-pointer">&times;</button>
                        </div>
                        
                        <div class="p-8 space-y-5 max-h-[70vh] overflow-y-auto">
                            <!-- Judul -->
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-orange-600 dark:text-orange-400 mb-1">Judul Skripsi</p>
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 leading-snug uppercase" x-text="abstractData.title"></h3>
                            </div>

                            <!-- Meta Mahasiswa & Pembimbing -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Mahasiswa Alumni</p>
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 mt-0.5" x-text="abstractData.name"></p>
                                    <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400" x-text="'NPM: ' + (abstractData.identifier || '-')"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Dosen Pembimbing</p>
                                    <div class="space-y-1">
                                        <template x-if="abstractData.pembimbing1">
                                            <div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-200">
                                                <span class="px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-bold text-[9px] rounded border border-indigo-200/80 dark:border-indigo-800/80 shrink-0">P1</span>
                                                <span class="font-bold truncate" x-text="abstractData.pembimbing1"></span>
                                            </div>
                                        </template>
                                        <template x-if="abstractData.pembimbing2">
                                            <div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-200">
                                                <span class="px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 font-bold text-[9px] rounded border border-purple-200/80 dark:border-purple-800/80 shrink-0">P2</span>
                                                <span class="font-bold truncate" x-text="abstractData.pembimbing2"></span>
                                            </div>
                                        </template>
                                        <template x-if="!abstractData.pembimbing1 && !abstractData.pembimbing2">
                                            <span class="text-xs text-slate-400 italic">-</span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Abstrak -->
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-2">Abstrak / Rangkuman</p>
                                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line text-justify font-medium" 
                                     x-text="abstractData.abstract || 'Tidak ada teks abstrak yang tersedia untuk arsip ini.'"></div>
                            </div>
                        </div>

                        <div class="px-8 py-5 bg-slate-50/75 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5 sm:gap-4 flex-wrap">
                                <template x-if="abstractData.file_path">
                                    <div class="inline-flex items-center rounded-xl shadow-sm overflow-hidden">
                                        <button type="button" 
                                                @click="abstractModalOpen = false; openPdfReader(abstractData, 'bab1')" 
                                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white text-xs font-bold transition-all cursor-pointer"
                                                title="Baca Naskah BAB 1 di Web (In-App Reader)">
                                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span>Baca BAB 1</span>
                                        </button>
                                        <a :href="'/repositories/' + abstractData.id + '/bab1'" 
                                           target="_blank" 
                                           class="px-2.5 py-2.5 bg-rose-700 hover:bg-rose-800 text-rose-100 hover:text-white transition-colors" 
                                           title="Buka File di Tab Baru">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </template>
                                <template x-if="abstractData.file_path_bab2">
                                    <div class="inline-flex items-center rounded-xl shadow-sm overflow-hidden">
                                        <button type="button" 
                                                @click="abstractModalOpen = false; openPdfReader(abstractData, 'bab2')" 
                                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 active:scale-95 text-white text-xs font-bold transition-all cursor-pointer"
                                                title="Baca Naskah BAB 2 di Web (In-App Reader)">
                                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span>Baca BAB 2</span>
                                        </button>
                                        <a :href="'/repositories/' + abstractData.id + '/bab2'" 
                                           target="_blank" 
                                           class="px-2.5 py-2.5 bg-amber-700 hover:bg-amber-800 text-amber-100 hover:text-white transition-colors" 
                                           title="Buka File di Tab Baru">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </template>
                                <template x-if="!abstractData.file_path && !abstractData.file_path_bab2">
                                    <span class="text-xs text-slate-400 italic">File naskah PDF belum terhubung</span>
                                </template>
                            </div>
                            <button type="button" @click="abstractModalOpen = false" class="px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-orange-600 dark:hover:bg-orange-500 dark:hover:text-white transition-all shadow-sm cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- MODAL IN-APP PDF READER -->
        <template x-teleport="body">
            <div x-show="pdfReaderOpen" 
                 id="pdfReaderModalElement"
                 class="fixed inset-0 select-none overflow-hidden" 
                 :style="'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 999999 !important; background-color: #0b0f19 !important;' + (pdfReaderOpen ? ' display: flex !important; flex-direction: column !important;' : ' display: none !important;')"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.window.escape="if (pdfReaderOpen) closePdfReader()"
                 @keydown.window.left="if (pdfReaderOpen && !['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) changePage(-1)"
                 @keydown.window.right="if (pdfReaderOpen && !['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) changePage(1)">
                
                <!-- TOP HEADER TOOLBAR -->
                <header style="background-color: #0f172a !important; border-bottom: 1px solid #1e293b !important; color: #f8fafc !important;" 
                        class="px-3 sm:px-5 py-2.5 shadow-2xl flex flex-wrap lg:flex-nowrap items-center justify-between gap-2.5 z-30 shrink-0">
                    
                    <!-- LEFT: Doc Info & Chapter Switcher -->
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Chapter Switcher Segmented Control -->
                        <div style="background-color: #020617 !important; border: 1px solid #1e293b !important;" 
                             class="inline-flex p-1 rounded-xl shrink-0">
                            <button type="button" 
                                    @click="switchChapter('bab1')" 
                                    :disabled="!pdfRepo.file_path"
                                    :style="pdfChapter === 'bab1' ? 'background-color: #e11d48 !important; color: #ffffff !important;' : 'color: #94a3b8 !important;'"
                                    class="px-2.5 sm:px-3 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>BAB 1</span>
                            </button>
                            <button type="button" 
                                    @click="switchChapter('bab2')" 
                                    :disabled="!pdfRepo.file_path_bab2"
                                    :style="pdfChapter === 'bab2' ? 'background-color: #d97706 !important; color: #ffffff !important;' : 'color: #94a3b8 !important;'"
                                    class="px-2.5 sm:px-3 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                <span>BAB 2</span>
                            </button>
                        </div>

                        <!-- Thesis Metadata Summary -->
                        <div class="hidden md:block truncate max-w-xs lg:max-w-md">
                            <h4 class="text-xs font-black text-slate-100 truncate tracking-tight" :title="pdfRepo.title" x-text="pdfRepo.title"></h4>
                            <p class="text-[10px] text-slate-400 font-medium truncate mt-0.5">
                                <span class="font-bold text-orange-400" x-text="pdfRepo.name"></span>
                                <span class="mx-1">•</span>
                                <span x-text="'NIM: ' + pdfRepo.identifier"></span>
                                <span class="mx-1">•</span>
                                <span x-text="'Angkatan ' + pdfRepo.year"></span>
                            </p>
                        </div>
                    </div>

                    <!-- CENTER: Navigation & Zoom -->
                    <div class="flex items-center gap-2 sm:gap-3 flex-wrap justify-center">
                        <!-- Page Controls -->
                        <div style="background-color: #020617 !important; border: 1px solid #1e293b !important;" 
                             class="flex items-center gap-1 rounded-xl px-2 py-1">
                            <button type="button" 
                                    @click="changePage(-1)" 
                                    :disabled="pdfCurrentPage <= 1"
                                    class="p-1 text-slate-300 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed rounded transition-colors cursor-pointer" 
                                    title="Halaman Sebelumnya (←)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <div class="flex items-center gap-1 text-xs font-bold text-slate-200 px-1">
                                <input type="number" 
                                       :value="pdfCurrentPage" 
                                       @change="goToPage($event.target.value)" 
                                       min="1" 
                                       :max="pdfTotalPages || 1" 
                                       style="background-color: #0f172a !important; border: 1px solid #334155 !important; color: #ffffff !important;"
                                       class="w-10 text-center rounded px-1 py-0.5 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <span class="text-slate-500">/</span>
                                <span x-text="pdfTotalPages || '-'" class="font-mono text-slate-300 min-w-[16px] text-center"></span>
                            </div>
                            <button type="button" 
                                    @click="changePage(1)" 
                                    :disabled="pdfCurrentPage >= pdfTotalPages"
                                    class="p-1 text-slate-300 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed rounded transition-colors cursor-pointer" 
                                    title="Halaman Berikutnya (→)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>

                        <!-- Zoom Controls -->
                        <div style="background-color: #020617 !important; border: 1px solid #1e293b !important;" 
                             class="flex items-center gap-1 rounded-xl px-2 py-1">
                            <button type="button" 
                                    @click="zoomOut()" 
                                    class="p-1 text-slate-300 hover:text-white rounded transition-colors cursor-pointer" 
                                    title="Perkecil Zoom (-)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg>
                            </button>
                            <span x-text="Math.round(pdfScale * 100) + '%'" class="text-[11px] font-mono font-bold text-slate-200 min-w-[40px] text-center"></span>
                            <button type="button" 
                                    @click="zoomIn()" 
                                    class="p-1 text-slate-300 hover:text-white rounded transition-colors cursor-pointer" 
                                    title="Perbesar Zoom (+)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                            <button type="button" 
                                    @click="fitWidth()" 
                                    style="background-color: #1e293b !important; color: #f1f5f9 !important;"
                                    class="px-2 py-0.5 text-[10px] font-bold hover:bg-slate-700 rounded transition-colors cursor-pointer" 
                                    title="Sesuaikan Lebar Layar">
                                Fit
                            </button>
                        </div>
                    </div>

                    <!-- RIGHT: Search, Protection Toggle, Actions -->
                    <div class="flex items-center gap-2 shrink-0">
                        <!-- In-Document Search Input -->
                        <div class="relative flex items-center">
                            <input type="text" 
                                   x-model="pdfSearchQuery" 
                                   @keydown.enter="searchInPdf()" 
                                   placeholder="Cari teks di bab..." 
                                   style="background-color: #020617 !important; border: 1px solid #1e293b !important; color: #ffffff !important;"
                                   class="w-32 sm:w-44 pl-7 pr-14 py-1 rounded-xl text-xs placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            
                            <div class="absolute right-1 flex items-center gap-0.5">
                                <button type="button" 
                                        @click="searchInPdf()" 
                                        class="p-0.5 text-slate-400 hover:text-orange-400 rounded transition-colors" 
                                        title="Mulai Cari">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                                <template x-if="pdfSearchMatches.length > 0">
                                    <div class="flex items-center border-l border-slate-700 pl-0.5">
                                        <button type="button" @click="prevSearchMatch()" class="p-0.5 text-slate-300 hover:text-white rounded" title="Hasil Sebelumnya">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                                        </button>
                                        <button type="button" @click="nextSearchMatch()" class="p-0.5 text-slate-300 hover:text-white rounded" title="Hasil Berikutnya">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Read-Only Mode Toggle -->
                        <button type="button" 
                                @click="toggleReadOnly()" 
                                :style="pdfReadOnly ? 'background-color: #064e3b !important; color: #6ee7b7 !important; border: 1px solid #059669 !important;' : 'background-color: #0f172a !important; color: #94a3b8 !important; border: 1px solid #1e293b !important;'"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-2xs" 
                                :title="pdfReadOnly ? 'Mode Proteksi Aktif (Anti-Copy & Watermark Digital)' : 'Mode Proteksi Nonaktif'">
                            <svg class="w-3.5 h-3.5" :class="pdfReadOnly ? 'text-emerald-300' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span class="hidden sm:inline" x-text="pdfReadOnly ? 'Read-Only' : 'Standar'"></span>
                        </button>

                        <!-- Open in External Tab -->
                        <a :href="'/repositories/' + pdfRepo.id + '/' + pdfChapter" 
                           target="_blank" 
                           style="background-color: #020617 !important; border: 1px solid #1e293b !important; color: #cbd5e1 !important;"
                           class="p-1.5 hover:bg-slate-800 hover:text-white rounded-xl transition-colors" 
                           title="Buka File di Tab Baru Browser">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>

                        <!-- Fullscreen Toggle -->
                        <button type="button" 
                                @click="toggleFullscreen()" 
                                style="background-color: #020617 !important; border: 1px solid #1e293b !important; color: #cbd5e1 !important;"
                                class="p-1.5 hover:bg-slate-800 hover:text-white rounded-xl transition-colors cursor-pointer" 
                                title="Layar Penuh (Fullscreen)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        </button>

                        <!-- Close Button -->
                        <button type="button" 
                                @click="closePdfReader()" 
                                style="background-color: #e11d48 !important; color: #ffffff !important;"
                                class="px-3 py-1 hover:bg-rose-700 active:scale-95 rounded-xl text-xs font-bold flex items-center gap-1 transition-all cursor-pointer shadow-md" 
                                title="Tutup Viewer (Esc)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span class="hidden sm:inline">Tutup</span>
                        </button>
                    </div>
                </header>

                <!-- SEARCH STATUS SUB-BAR (IF ACTIVE) -->
                <div x-show="pdfSearchStatus" 
                     style="background-color: #451a03 !important; border-bottom: 1px solid #78350f !important; color: #fde68a !important;"
                     class="px-4 py-1 text-center text-xs font-bold flex items-center justify-center gap-2 shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Hasil Pencarian: </span>
                    <span x-text="pdfSearchStatus" class="underline font-mono"></span>
                    <button type="button" @click="pdfSearchQuery = ''; pdfSearchStatus = ''; pdfSearchMatches = [];" class="ml-2 text-amber-300 hover:text-white text-[10px] uppercase font-bold">&times; Bersihkan</button>
                </div>

                <!-- DOCUMENT CANVAS BODY -->
                <div id="pdfViewerContainer" 
                     style="background-color: #020617 !important; flex: 1 1 0% !important; overflow: auto !important; width: 100% !important; height: 100% !important; position: relative !important;"
                     class="p-4 sm:p-8"
                     :style="pdfReadOnly ? 'user-select: none; -webkit-user-select: none;' : ''"
                     @contextmenu="if (pdfReadOnly) { $event.preventDefault(); return false; }"
                     @copy="if (pdfReadOnly) { $event.preventDefault(); return false; }">
                    
                    <!-- Loading State Overlay -->
                    <div x-show="pdfIsLoading" 
                         style="position: absolute !important; inset: 0 !important; background-color: rgba(2, 6, 23, 0.9) !important; z-index: 50 !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important;">
                        <div class="w-12 h-12 border-4 border-orange-500/20 border-t-orange-500 rounded-full animate-spin mb-3"></div>
                        <p class="text-sm font-bold text-white">Memuat Naskah PDF...</p>
                        <p class="text-xs text-slate-400 mt-1" x-text="pdfChapter === 'bab1' ? 'Menyiapkan BAB 1 (Pendahuluan)' : 'Menyiapkan BAB 2 (Tinjauan Pustaka)'"></p>
                    </div>

                    <!-- Error State -->
                    <div x-show="pdfLoadingError" 
                         style="background-color: #0f172a !important; border: 1px solid #991b1b !important; z-index: 50 !important;"
                         class="max-w-md mx-auto my-16 p-6 rounded-3xl text-center shadow-2xl">
                        <div class="w-12 h-12 bg-rose-500/10 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h4 class="text-base font-bold text-white mb-1">Gagal Membuka Naskah</h4>
                        <p class="text-xs text-slate-400 mb-5" x-text="pdfLoadingError"></p>
                        <div class="flex items-center justify-center gap-3">
                            <button type="button" @click="loadPdf()" style="background-color: #1e293b !important; color: #ffffff !important; border: 1px solid #334155 !important;" class="px-4 py-2 hover:bg-slate-700 rounded-xl text-xs font-bold transition-colors">Coba Lagi</button>
                            <a :href="'/repositories/' + pdfRepo.id + '/' + pdfChapter" target="_blank" style="background-color: #ea580c !important; color: #ffffff !important;" class="px-4 py-2 hover:bg-orange-700 rounded-xl text-xs font-bold transition-colors">Buka di Tab Baru</a>
                        </div>
                    </div>

                    <!-- Canvas Viewport -->
                    <div class="pb-12 pt-2 flex justify-center items-start w-full">
                        <canvas id="pdfViewerCanvas" 
                                x-ref="pdfCanvas"
                                style="background-color: #ffffff !important; border: 1px solid #334155 !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important; display: block !important; margin: 0 auto !important; border-radius: 2px !important; outline: none !important;"></canvas>
                    </div>
                </div>

                <!-- BOTTOM STATUS BAR -->
                <footer style="background-color: #0f172a !important; border-top: 1px solid #1e293b !important; color: #94a3b8 !important;" 
                        class="px-4 py-2 flex items-center justify-between text-[11px] shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full" :style="pdfReadOnly ? 'background-color: #34d399 !important;' : 'background-color: #94a3b8 !important;'"></span>
                        <span x-text="pdfReadOnly ? 'Mode Proteksi Aktif: Anti-copy, klik kanan dinonaktifkan, watermark resmi aktif.' : 'Mode Standar.'"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline">Gunakan <kbd style="background-color: #1e293b !important; color: #e2e8f0 !important;" class="px-1.5 py-0.5 rounded font-mono">←</kbd> / <kbd style="background-color: #1e293b !important; color: #e2e8f0 !important;" class="px-1.5 py-0.5 rounded font-mono">→</kbd> untuk ganti halaman, <kbd style="background-color: #1e293b !important; color: #e2e8f0 !important;" class="px-1.5 py-0.5 rounded font-mono">Esc</kbd> untuk tutup.</span>
                        <span class="font-mono text-slate-200 font-bold" x-text="'Hal ' + pdfCurrentPage + ' dari ' + (pdfTotalPages || 1)"></span>
                    </div>
                </footer>
            </div>
        </template>

        @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
            <!-- Modal Edit Arsip Pustaka -->
            <template x-teleport="body">
                <div x-show="editModalOpen" 
                     class="fixed inset-0 overflow-y-auto text-left" 
                     style="z-index: 99999 !important;" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="editModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-200 dark:border-slate-700 relative" 
                             style="z-index: 100000 !important;">
                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Edit Data Arsip Pustaka</h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold mt-0.5">Koreksi judul, nama mahasiswa, atau data pembimbing</p>
                                </div>
                                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold p-1">&times;</button>
                            </div>
                            <form :action="'/repositories/' + editData.id" method="POST" class="p-8 space-y-4">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Nama Mahasiswa -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Nama Mahasiswa <span class="text-rose-500">*</span></label>
                                        <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>
                                    
                                    <!-- NPM / Identifier -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">NPM / Identifier</label>
                                        <input type="text" name="identifier" x-model="editData.identifier" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>
                                </div>

                                <!-- Judul Skripsi -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Judul Skripsi <span class="text-rose-500">*</span></label>
                                    <textarea name="title" x-model="editData.title" required rows="3" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 leading-relaxed"></textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <!-- Tahun Angkatan -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Tahun Angkatan</label>
                                        <input type="number" name="year" x-model="editData.year" placeholder="YYYY" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>

                                    <!-- Pembimbing 1 -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Pembimbing 1</label>
                                        <input type="text" name="pembimbing1" x-model="editData.pembimbing1" placeholder="Nama P1" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>

                                    <!-- Pembimbing 2 -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Pembimbing 2</label>
                                        <input type="text" name="pembimbing2" x-model="editData.pembimbing2" placeholder="Nama P2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>
                                </div>

                                <!-- Abstrak -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Abstrak</label>
                                    <textarea name="abstract" x-model="editData.abstract" rows="4" placeholder="Abstrak skripsi..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-normal text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 leading-relaxed"></textarea>
                                </div>

                                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                                    <button type="button" @click="editModalOpen = false" class="px-5 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:scale-[1.02] transition-all">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Modal Hapus Arsip Pustaka -->
            <template x-teleport="body">
                <div x-show="deleteModalOpen" 
                     class="fixed inset-0 overflow-y-auto text-left" 
                     style="z-index: 99999 !important;" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="deleteModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200 dark:border-slate-700 relative" 
                             style="z-index: 100000 !important;">
                            <div class="p-6">
                                <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </div>
                                <h3 class="text-base font-black text-center text-slate-800 dark:text-slate-100 uppercase tracking-tight">Hapus Arsip Skripsi?</h3>
                                <p class="text-xs text-center text-slate-500 dark:text-slate-400 mt-2">
                                    Apakah Anda yakin ingin menghapus data arsip berikut dari katalog pustaka?
                                </p>
                                <div class="my-4 p-3 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 line-clamp-2" x-text="deleteData.title"></div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-semibold" x-text="'Mahasiswa: ' + deleteData.name"></div>
                                </div>

                                <form :action="'/repositories/' + deleteData.id" method="POST" class="flex items-center justify-end gap-3 pt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl font-bold text-xs shadow-md shadow-rose-500/20 hover:scale-[1.02] transition-all">
                                        Ya, Hapus Arsip
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        @endif
    </div>

<!-- Modal Sync Portal -->
<div id="syncModal" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-md items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-md shadow-2xl border border-slate-100 dark:border-slate-700/60 overflow-hidden transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black shrink-0">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-white uppercase tracking-tight">Migrasi Data Portal</h3>
                    <p class="text-[11px] font-medium text-slate-400">Sinkronisasi Pustaka FASILKOM</p>
                </div>
            </div>
            
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">Sistem sedang menarik data dari website portal FASILKOM (41 Halaman). Mohon jangan tutup jendela ini hingga proses selesai.</p>

            <!-- Progress Bar Track Container -->
            <div class="relative w-full bg-slate-100 dark:bg-slate-700/60 rounded-full h-4 mb-2 overflow-hidden border border-slate-200/80 dark:border-slate-600 shadow-inner">
                <div id="syncProgress" class="bg-gradient-to-r from-orange-500 via-amber-500 to-emerald-500 h-full rounded-full transition-all duration-300 shadow-sm" style="width: 0%;"></div>
            </div>
            
            <div class="flex justify-between items-center text-xs font-bold text-slate-600 dark:text-slate-300 mb-5">
                <span id="syncStatus" class="truncate">Menunggu...</span>
                <span id="syncPercentage" class="shrink-0 ml-2 font-black text-orange-600 dark:text-orange-400">0% (0/41)</span>
            </div>

            <!-- Live Migration Statistics Cards -->
            <div class="grid grid-cols-3 gap-2.5 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Data Baru</p>
                    <p id="statNewCount" class="text-base font-black text-emerald-600 dark:text-emerald-400">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Duplikat</p>
                    <p id="statDupCount" class="text-base font-black text-amber-600 dark:text-amber-400">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Total</p>
                    <p id="statTotalCount" class="text-base font-black text-indigo-600 dark:text-indigo-400">0</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button id="closeSyncModalBtn" onclick="closeSyncModal()" class="hidden bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider hover:bg-orange-600 dark:hover:bg-orange-500 dark:hover:text-white transition-all shadow-sm">Tutup & Muat Ulang</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sync Repositori UNSUB (Khusus FASILKOM & File BAB 1) -->
<div id="unsubSyncModal" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-md items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-lg shadow-2xl border border-slate-100 dark:border-slate-700/60 overflow-hidden transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-purple-100 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center font-black shrink-0" style="background-color: #f3e8ff; color: #7e22ce;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-white uppercase tracking-tight">Sync Repositori UNSUB</h3>
                    <p class="text-[11px] font-medium text-slate-400">Sinkronisasi Pustaka & Naskah BAB I & II</p>
                </div>
            </div>

            <!-- Policy & Constraints Badges -->
            <div class="flex items-center gap-2 mb-4 flex-wrap">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800" style="background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                    <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Khusus FASILKOM (144 Dokumen)</span>
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800" style="background-color: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe;">
                    <svg class="w-3 h-3 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Hanya File BAB I & II (Hemat Storage)</span>
                </span>
            </div>

            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                Sistem akan memindai repositori Universitas Subang, memfilter secara ketat dokumen Fakultas Ilmu Komputer, memperkaya data skripsi dengan teks abstrak lengkap, dan menghubungkan file naskah BAB I & BAB II (PDF).
            </p>

            <!-- Download PDF option toggle -->
            <div id="unsubOptionsContainer" class="p-3 mb-4 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-700/80">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" id="unsubDownloadPdf" checked class="mt-0.5 rounded text-purple-600 focus:ring-purple-500 border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <div>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">Unduh Fisik File BAB 1 & BAB 2 ke Server (Storage)</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Disarankan. Membutuhkan ~200–230 MB total untuk 144 dokumen, tersimpan aman di server lokal.</span>
                    </div>
                </label>
            </div>

            <!-- Progress Bar Track Container -->
            <div class="relative w-full bg-slate-100 dark:bg-slate-700/60 rounded-full h-4 mb-2 overflow-hidden border border-slate-200/80 dark:border-slate-600 shadow-inner">
                <div id="unsubSyncProgress" class="bg-gradient-to-r from-purple-500 via-indigo-500 to-emerald-500 h-full rounded-full transition-all duration-300 shadow-sm" style="background: linear-gradient(to right, #9333ea, #6366f1, #10b981); width: 0%;"></div>
            </div>
            
            <div class="flex justify-between items-center text-xs font-bold text-slate-600 dark:text-slate-300 mb-5">
                <span id="unsubSyncStatus" class="truncate">Siap untuk sinkronisasi...</span>
                <span id="unsubSyncPercentage" class="shrink-0 ml-2 font-black text-purple-600 dark:text-purple-400" style="color: #7e22ce;">0% (0 / 144)</span>
            </div>

            <!-- Live Statistics Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[8px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Fasilkom</p>
                    <p id="statUnsubTotal" class="text-sm font-black text-slate-700 dark:text-slate-200" style="color: #334155;">144</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[8px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Data Baru</p>
                    <p id="statUnsubNew" class="text-sm font-black text-emerald-600 dark:text-emerald-400" style="color: #059669;">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[8px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Diperkaya</p>
                    <p id="statUnsubEnriched" class="text-sm font-black text-indigo-600 dark:text-indigo-400" style="color: #4f46e5;">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[8px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">BAB 1 PDF</p>
                    <p id="statUnsubBab1" class="text-sm font-black text-purple-600 dark:text-purple-400" style="color: #7e22ce;">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[8px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">BAB 2 PDF</p>
                    <p id="statUnsubBab2" class="text-sm font-black text-amber-600 dark:text-amber-400" style="color: #d97706;">0</p>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-end gap-3">
                <button type="button" 
                        id="cancelUnsubBtn" 
                        onclick="closeUnsubSyncModal(false)" 
                        style="background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-bold text-xs bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600 shadow-2xs hover:shadow-sm transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Batal</span>
                </button>
                <button type="button" 
                        id="startUnsubBtn" 
                        onclick="startUnsubSync()" 
                        style="background-color: #7e22ce !important; color: #ffffff !important; border: 1px solid #6b21a8;"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-white bg-purple-700 hover:bg-purple-800 active:scale-95 shadow-md shadow-purple-700/30 hover:shadow-lg transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Mulai Sinkronisasi</span>
                </button>
                <button type="button" 
                        id="closeUnsubModalBtn" 
                        onclick="closeUnsubSyncModal(true)" 
                        style="display: none; background-color: #0f172a !important; color: #ffffff !important;"
                        class="hidden inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-white bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 shadow-md transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Tutup & Muat Ulang</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let isSyncing = false;
    const totalPages = 41;
    let currentPage = 1;
    let totalImported = 0;
    let totalNew = 0;
    let totalDuplicates = 0;
    let pageRetryCount = 0;
    const maxRetries = 3;

    async function startSync() {
        if (isSyncing) return;
        
        if (!confirm('Anda yakin ingin memulai migrasi 41 halaman dari portal FASILKOM? Proses ini mungkin memakan waktu beberapa menit.')) {
            return;
        }

        isSyncing = true;
        currentPage = 1;
        totalImported = 0;
        totalNew = 0;
        totalDuplicates = 0;
        pageRetryCount = 0;
        
        document.getElementById('statNewCount').innerText = '0';
        document.getElementById('statDupCount').innerText = '0';
        document.getElementById('statTotalCount').innerText = '0';
        
        const modal = document.getElementById('syncModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('closeSyncModalBtn').classList.add('hidden');
        
        await processNextPage();
    }

    async function processNextPage() {
        if (currentPage > totalPages) {
            finishSync();
            return;
        }

        updateProgressUI(currentPage, totalPages, pageRetryCount > 0 
            ? `Mencoba ulang halaman ${currentPage} (Percobaan ${pageRetryCount+1}/${maxRetries})...` 
            : `Menarik data halaman ${currentPage}...`);

        try {
            const response = await fetch(`/repositories/sync-page/${currentPage}`);
            const result = await response.json();
            
            if (result.success) {
                totalImported += (result.count || 0);
                totalNew += (result.new_count || 0);
                totalDuplicates += (result.duplicate_count || 0);

                document.getElementById('statNewCount').innerText = totalNew;
                document.getElementById('statDupCount').innerText = totalDuplicates;
                document.getElementById('statTotalCount').innerText = totalImported;

                pageRetryCount = 0; // Reset retry counter on success
                currentPage++;
                setTimeout(processNextPage, 300);
            } else {
                throw new Error(result.message || 'Gagal merespons');
            }
        } catch (error) {
            pageRetryCount++;
            if (pageRetryCount < maxRetries) {
                // Retry current page after 1.5s delay
                setTimeout(processNextPage, 1500);
            } else {
                // Skip problematic page after max retries and continue to next page
                console.warn(`Halaman ${currentPage} dilewati setelah ${maxRetries}x percobaan gagal.`);
                pageRetryCount = 0;
                currentPage++;
                setTimeout(processNextPage, 500);
            }
        }
    }

    function updateProgressUI(current, total, statusText) {
        const percentage = Math.round((current / total) * 100);
        const progressBar = document.getElementById('syncProgress');
        if (progressBar) progressBar.style.width = `${percentage}%`;
        
        document.getElementById('syncPercentage').innerText = `${percentage}% (${current}/${total})`;
        document.getElementById('syncStatus').innerText = statusText;
    }

    function handleSyncError(message) {
        document.getElementById('syncStatus').innerText = message;
        document.getElementById('syncStatus').classList.add('text-red-500');
        document.getElementById('closeSyncModalBtn').classList.remove('hidden');
        isSyncing = false;
    }

    function finishSync() {
        const progressBar = document.getElementById('syncProgress');
        if (progressBar) progressBar.style.width = `100%`;
        
        document.getElementById('syncPercentage').innerText = `100% (Selesai)`;
        document.getElementById('syncStatus').innerText = `Sinkronisasi selesai! ${totalNew} data baru, ${totalDuplicates} duplikat di-update.`;
        document.getElementById('syncStatus').classList.add('text-emerald-600', 'dark:text-emerald-400');
        document.getElementById('closeSyncModalBtn').classList.remove('hidden');
        isSyncing = false;
    }

    function closeSyncModal() {
        window.location.reload();
    }

    // UNSUB REPOSITORY SYNC JAVASCRIPT
    let isUnsubSyncing = false;
    let unsubOffset = 0;
    const unsubLimit = 4; // Batasi 4 dokumen per request agar request selesai dalam beberapa detik dan aman dari Nginx 504 Timeout
    let unsubTotalFasilkom = 144;
    let unsubTotalNew = 0;
    let unsubTotalEnriched = 0;
    let unsubTotalBab1 = 0;
    let unsubTotalBab2 = 0;
    let unsubRetryCount = 0;

    function openUnsubSyncModal() {
        if (isUnsubSyncing) return;
        const modal = document.getElementById('unsubSyncModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Reset UI
        unsubRetryCount = 0;
        document.getElementById('startUnsubBtn').style.display = 'inline-flex';
        document.getElementById('cancelUnsubBtn').style.display = 'inline-flex';
        document.getElementById('closeUnsubModalBtn').style.display = 'none';
        document.getElementById('unsubOptionsContainer').classList.remove('hidden');
        document.getElementById('unsubSyncProgress').style.width = '0%';
        document.getElementById('unsubSyncPercentage').innerText = '0%';
        document.getElementById('unsubSyncStatus').innerText = 'Menyiapkan sinkronisasi...';
        document.getElementById('statUnsubNew').innerText = '0';
        document.getElementById('statUnsubEnriched').innerText = '0';
        document.getElementById('statUnsubBab1').innerText = '0';
        document.getElementById('statUnsubBab2').innerText = '0';

        // Pre-fetch info
        fetch('{{ route('repositories.unsub-info') }}')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    unsubTotalFasilkom = data.total_fasilkom || 144;
                    document.getElementById('statUnsubTotal').innerText = unsubTotalFasilkom;
                    document.getElementById('unsubSyncStatus').innerText = `Terdeteksi ${unsubTotalFasilkom} dokumen skripsi FASILKOM (dari ${data.total_all} total).`;
                }
            })
            .catch(() => {
                document.getElementById('unsubSyncStatus').innerText = 'Siap untuk sinkronisasi FASILKOM.';
            });
    }

    async function startUnsubSync() {
        if (isUnsubSyncing) return;
        isUnsubSyncing = true;
        unsubOffset = 0;
        unsubTotalNew = 0;
        unsubTotalEnriched = 0;
        unsubTotalBab1 = 0;
        unsubTotalBab2 = 0;
        unsubRetryCount = 0;

        document.getElementById('startUnsubBtn').style.display = 'none';
        document.getElementById('cancelUnsubBtn').style.display = 'none';
        document.getElementById('closeUnsubModalBtn').style.display = 'none';
        document.getElementById('unsubOptionsContainer').classList.add('hidden');

        await processNextUnsubChunk();
    }

    async function processNextUnsubChunk() {
        const downloadPdf = document.getElementById('unsubDownloadPdf').checked;
        const currentProgress = Math.min(unsubOffset, unsubTotalFasilkom);
        const pct = Math.round((currentProgress / unsubTotalFasilkom) * 100);

        document.getElementById('unsubSyncProgress').style.width = `${pct}%`;
        document.getElementById('unsubSyncPercentage').innerText = `${pct}% (${currentProgress} / ${unsubTotalFasilkom})`;
        document.getElementById('unsubSyncStatus').innerText = `Memproses dokumen ${currentProgress + 1} s.d. ${Math.min(currentProgress + unsubLimit, unsubTotalFasilkom)}...`;

        try {
            const res = await fetch('{{ route('repositories.sync-unsub-chunk') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    offset: unsubOffset,
                    limit: unsubLimit,
                    download_pdf: downloadPdf
                })
            });

            if (!res.ok) {
                if (res.status === 504 || res.status === 502) {
                    throw new Error('Server timeout (504 Gateway Timeout) saat mengunduh PDF');
                }
                throw new Error(`Server error HTTP ${res.status}`);
            }

            const contentType = res.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Respons server bukan JSON (koneksi terputus atau sesi habis)');
            }

            const data = await res.json();
            if (!data.success) {
                throw new Error(data.message || 'Gagal memproses data');
            }

            unsubRetryCount = 0; // Reset counter percobaan saat sukses
            unsubTotalNew += (data.created || 0);
            unsubTotalEnriched += (data.enriched || 0);
            unsubTotalBab1 += (data.has_bab1 || 0);
            unsubTotalBab2 += (data.has_bab2 || 0);

            document.getElementById('statUnsubNew').innerText = unsubTotalNew;
            document.getElementById('statUnsubEnriched').innerText = unsubTotalEnriched;
            document.getElementById('statUnsubBab1').innerText = unsubTotalBab1;
            document.getElementById('statUnsubBab2').innerText = unsubTotalBab2;

            if (data.is_finished || data.next_offset >= unsubTotalFasilkom) {
                finishUnsubSync();
                return;
            }

            unsubOffset = data.next_offset;
            setTimeout(processNextUnsubChunk, 200);

        } catch (err) {
            unsubRetryCount++;
            if (unsubRetryCount <= 2) {
                document.getElementById('unsubSyncStatus').innerText = `${err.message}. Mengulang chunk ${currentProgress + 1} (Percobaan ${unsubRetryCount}/2)...`;
                setTimeout(processNextUnsubChunk, 2000);
            } else {
                document.getElementById('unsubSyncStatus').innerText = `Melanjutkan ke dokumen berikutnya setelah kendala jaringan...`;
                unsubRetryCount = 0;
                unsubOffset += unsubLimit;
                if (unsubOffset >= unsubTotalFasilkom) {
                    finishUnsubSync();
                } else {
                    setTimeout(processNextUnsubChunk, 1000);
                }
            }
        }
    }

    function finishUnsubSync() {
        document.getElementById('unsubSyncProgress').style.width = '100%';
        document.getElementById('unsubSyncPercentage').innerText = '100% (Selesai)';
        document.getElementById('unsubSyncStatus').innerText = `Sinkronisasi Selesai! ${unsubTotalNew} skripsi baru, ${unsubTotalEnriched} diperkaya, ${unsubTotalBab1} BAB 1 & ${unsubTotalBab2} BAB 2 PDF terhubung.`;
        document.getElementById('unsubSyncStatus').classList.add('text-emerald-600', 'dark:text-emerald-400');
        document.getElementById('startUnsubBtn').style.display = 'none';
        document.getElementById('cancelUnsubBtn').style.display = 'none';
        document.getElementById('closeUnsubModalBtn').style.display = 'inline-flex';
        isUnsubSyncing = false;
    }

    function closeUnsubSyncModal(reload = false) {
        const modal = document.getElementById('unsubSyncModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        isUnsubSyncing = false;
        if (reload) {
            window.location.reload();
        }
    }
</script>

@push('scripts')
<script src="{{ asset('vendor/pdfjs/pdf.min.js') }}"></script>
<script>
    if (typeof pdfjsLib === 'undefined') {
        document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"><\/script>');
    }
    function initPdfJsWorker() {
        if (window.pdfjsLib) {
            const workerRel = '{{ asset('vendor/pdfjs/pdf.worker.min.js') }}'.replace(/^https?:\/\/[^\/]+/, '');
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = (window.location.origin || '') + workerRel;
        }
    }
    initPdfJsWorker();
    window.addEventListener('DOMContentLoaded', initPdfJsWorker);
</script>
@endpush
</x-app-layout>

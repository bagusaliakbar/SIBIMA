    @push('scripts')
    <script>
        function bookmarksApp(initialFolders = [], initialFolderFilter = 'all') {
            return {
                folders: initialFolders,
                folderFilter: initialFolderFilter,

                // Citation Modal State
                citationModalOpen: false,
                activeCitationFormat: 'apa',
                copiedToast: false,
                activePaper: {
                    title: '',
                    authors_string: '',
                    year: null,
                    citations: {
                        apa: '',
                        ieee: '',
                        bibtex: ''
                    }
                },

                // Batch Citation State
                batchCitationModalOpen: false,
                batchCitationLoading: false,
                batchCitationActiveFormat: 'apa',
                batchCitationCopied: false,
                batchCitationData: {
                    count: 0,
                    apa: '',
                    ieee: '',
                    bibtex: ''
                },

                // Folder Form Modal State
                folderModalOpen: false,
                folderFormMode: 'create', // 'create' or 'edit'
                folderFormId: null,
                folderFormName: '',
                folderFormSaving: false,
                templateLoading: false,

                // Move Bookmark Modal State
                moveModalOpen: false,
                moveActiveBookmark: null,
                moveTargetFolderId: null,
                moveSaving: false,

                // ============ SINGLE CITATION ============
                openCitationModal(paper) {
                    this.activePaper = paper;
                    this.activeCitationFormat = 'fasilkom';
                    this.citationModalOpen = true;
                    this.copiedToast = false;
                    this.copiedToastMessage = '';
                },

                getCurrentCitationText() {
                    if (!this.activePaper) return '';
                    if (this.activePaper.citations && this.activePaper.citations[this.activeCitationFormat]) {
                        return this.activePaper.citations[this.activeCitationFormat];
                    }
                    return this.buildFallbackCitation(this.activeCitationFormat);
                },

                buildFallbackCitation(format) {
                    if (!this.activePaper) return '';
                    const p = this.activePaper;
                    const author = p.authors_string || 'Anonim';
                    const year = p.year || new Date().getFullYear();
                    const title = p.title || '';
                    const venue = p.venue || 'Jurnal Ilmiah';
                    const link = p.doi || p.landing_page_url || p.pdf_url || '';

                    if (format === 'fasilkom') {
                        return author + ' (' + year + '). "' + title + '". ' + venue + '.' + (link ? ' Tersedia di: ' + link + '.' : '');
                    } else if (format === 'ris') {
                        return 'TY  - JOUR\r\nTI  - ' + title + '\r\nAU  - ' + author + '\r\nT2  - ' + venue + '\r\nPY  - ' + year + '\r\nUR  - ' + link + '\r\nER  - ';
                    } else if (format === 'ieee') {
                        return author + ', "' + title + '," ' + venue + ', ' + year + '.' + (link ? ' doi: ' + link : '');
                    } else if (format === 'bibtex') {
                        return '@article{referensi' + year + ',\n  title = {' + title + '},\n  author = {' + author + '},\n  journal = {' + venue + '},\n  year = {' + year + '}\n}';
                    }
                    return author + ' (' + year + '). ' + title + '. ' + venue + '.' + (link ? ' ' + link : '');
                },

                copyCitation() {
                    const text = this.getCurrentCitationText();
                    if (!text) return;

                    navigator.clipboard.writeText(text).then(() => {
                        this.copiedToastMessage = 'Teks sitasi berhasil disalin ke clipboard!';
                        this.copiedToast = true;
                        setTimeout(() => {
                            this.copiedToast = false;
                        }, 3000);
                    });
                },

                copyFasilkomWord() {
                    if (!this.activePaper) return;
                    const p = this.activePaper;
                    const plainText = (p.citations && p.citations.fasilkom) ? p.citations.fasilkom : this.buildFallbackCitation('fasilkom');
                    const htmlText = (p.citations && p.citations.fasilkom_html) ? p.citations.fasilkom_html : `<p>${plainText}</p>`;

                    if (navigator.clipboard && window.ClipboardItem) {
                        const textBlob = new Blob([plainText], { type: 'text/plain' });
                        const htmlBlob = new Blob([htmlText], { type: 'text/html' });
                        navigator.clipboard.write([
                            new ClipboardItem({
                                'text/plain': textBlob,
                                'text/html': htmlBlob
                            })
                        ]).then(() => {
                            this.copiedToastMessage = 'Daftar pustaka siap pakai Word (FASILKOM) berhasil disalin!';
                            this.copiedToast = true;
                            setTimeout(() => { this.copiedToast = false; }, 3000);
                        }).catch(() => {
                            navigator.clipboard.writeText(plainText).then(() => {
                                this.copiedToastMessage = 'Daftar pustaka FASILKOM disalin (teks biasa)!';
                                this.copiedToast = true;
                                setTimeout(() => { this.copiedToast = false; }, 3000);
                            });
                        });
                    } else {
                        navigator.clipboard.writeText(plainText).then(() => {
                            this.copiedToastMessage = 'Daftar pustaka FASILKOM berhasil disalin!';
                            this.copiedToast = true;
                            setTimeout(() => { this.copiedToast = false; }, 3000);
                        });
                    }
                },

                downloadRis() {
                    if (!this.activePaper) return;
                    const p = this.activePaper;
                    const risText = (p.citations && p.citations.ris) ? p.citations.ris : this.buildFallbackCitation('ris');

                    const blob = new Blob([risText], { type: 'application/x-research-info-systems;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);

                    const cleanTitle = (p.title || 'referensi').toLowerCase().replace(/[^a-z0-9]/g, '_').substring(0, 35);
                    link.setAttribute('download', `sitasi_${cleanTitle}.ris`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    this.copiedToastMessage = 'File .RIS berhasil diunduh untuk Mendeley/Zotero!';
                    this.copiedToast = true;
                    setTimeout(() => { this.copiedToast = false; }, 3000);
                },

                // ============ BATCH CITATIONS ============
                openBatchCitationModal() {
                    this.batchCitationModalOpen = true;
                    this.batchCitationLoading = true;
                    this.batchCitationCopied = false;
                    this.batchCitationCopiedMessage = '';
                    this.batchCitationActiveFormat = 'fasilkom';

                    const urlParams = new URLSearchParams(window.location.search);
                    fetch(`/repositories/bookmarks/export-citations?${urlParams.toString()}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        this.batchCitationLoading = false;
                        if (d.success) {
                            this.batchCitationData = d;
                        }
                    })
                    .catch(() => {
                        this.batchCitationLoading = false;
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Gagal memuat seluruh sitasi.', type: 'error' }
                        }));
                    });
                },

                getBatchCitationText() {
                    return this.batchCitationData[this.batchCitationActiveFormat] || 'Tidak ada sitasi untuk ditampilkan.';
                },

                copyBatchCitation() {
                    const text = this.getBatchCitationText();
                    if (!text) return;

                    navigator.clipboard.writeText(text).then(() => {
                        this.batchCitationCopied = true;
                        this.batchCitationCopiedMessage = 'Seluruh daftar pustaka berhasil disalin!';
                        setTimeout(() => {
                            this.batchCitationCopied = false;
                        }, 3000);
                    });
                },

                copyBatchFasilkomWord() {
                    const text = this.batchCitationData['fasilkom'] || this.getBatchCitationText();
                    if (!text) return;

                    navigator.clipboard.writeText(text).then(() => {
                        this.batchCitationCopied = true;
                        this.batchCitationCopiedMessage = 'Seluruh daftar pustaka (FASILKOM Word) berhasil disalin!';
                        setTimeout(() => {
                            this.batchCitationCopied = false;
                        }, 3000);
                    });
                },

                downloadBatchRis() {
                    const risText = this.batchCitationData['ris'] || '';
                    if (!risText) return;

                    const blob = new Blob([risText], { type: 'application/x-research-info-systems;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.setAttribute('download', 'koleksi_pustaka_sibima.ris');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    this.batchCitationCopied = true;
                    this.batchCitationCopiedMessage = 'File .RIS (seluruh artikel) berhasil diunduh!';
                    setTimeout(() => {
                        this.batchCitationCopied = false;
                    }, 3000);
                },

                // ============ FOLDER CRUD ============
                openCreateFolderModal() {
                    this.folderFormMode = 'create';
                    this.folderFormId = null;
                    this.folderFormName = '';
                    this.folderModalOpen = true;
                },

                openEditFolderModal(folder) {
                    this.folderFormMode = 'edit';
                    this.folderFormId = folder.id;
                    this.folderFormName = folder.name;
                    this.folderModalOpen = true;
                },

                saveFolder() {
                    if (!this.folderFormName.trim()) return;
                    this.folderFormSaving = true;

                    const isCreate = this.folderFormMode === 'create';
                    const url = isCreate ? '/repositories/bookmarks/folders' : `/repositories/bookmarks/folders/${this.folderFormId}`;
                    const method = isCreate ? 'POST' : 'PATCH';

                    fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ name: this.folderFormName })
                    })
                    .then(r => r.json())
                    .then(d => {
                        this.folderFormSaving = false;
                        if (d.success) {
                            this.folderModalOpen = false;
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { title: 'Berhasil', message: d.message, type: 'success' }
                            }));
                            setTimeout(() => window.location.reload(), 300);
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { title: 'Gagal', message: d.message || 'Gagal menyimpan folder.', type: 'error' }
                            }));
                        }
                    })
                    .catch(() => {
                        this.folderFormSaving = false;
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Terjadi kesalahan sistem.', type: 'error' }
                        }));
                    });
                },

                deleteFolder(folder) {
                    window.sibimaConfirm({
                        title: 'Hapus Folder',
                        message: `Apakah Anda yakin ingin menghapus folder "${folder.name}"?\nArtikel di dalamnya TIDAK akan terhapus, melainkan dikembalikan ke status Tanpa Folder.`,
                        type: 'danger',
                        confirmText: 'Ya, Hapus',
                        onConfirm: () => {
                            fetch(`/repositories/bookmarks/folders/${folder.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(r => r.json())
                            .then(d => {
                                if (d.success) {
                                    window.dispatchEvent(new CustomEvent('notify', {
                                        detail: { title: 'Folder Dihapus', message: d.message, type: 'info' }
                                    }));
                                    setTimeout(() => {
                                        // If currently viewing the deleted folder, redirect to 'all'
                                        const url = new URL(window.location.href);
                                        url.searchParams.set('folder', 'all');
                                        window.location.href = url.toString();
                                    }, 300);
                                }
                            })
                            .catch(() => {
                                window.dispatchEvent(new CustomEvent('notify', {
                                    detail: { title: 'Kesalahan', message: 'Gagal menghapus folder.', type: 'error' }
                                }));
                            });
                        }
                    });
                },

                applyThesisTemplate() {
                    window.sibimaConfirm({
                        title: 'Buat Folder Standar Skripsi',
                        message: 'Apakah Anda ingin membuat 5 folder standar Bab Skripsi FASILKOM (BAB I s/d BAB V)?',
                        type: 'primary',
                        confirmText: 'Ya, Buat Folder',
                        onConfirm: () => {
                            this.templateLoading = true;
                            fetch('/repositories/bookmarks/folders/templates', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(r => r.json())
                    .then(d => {
                        this.templateLoading = false;
                        if (d.success) {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { title: 'Template Diterapkan', message: d.message, type: 'success' }
                            }));
                            setTimeout(() => window.location.reload(), 400);
                        }
                    })
                    .catch(() => {
                        this.templateLoading = false;
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Gagal membuat template folder.', type: 'error' }
                        }));
                    });
                },

                // ============ MOVE BOOKMARK TO FOLDER ============
                openMoveModal(paper) {
                    this.moveActiveBookmark = paper;
                    this.moveTargetFolderId = paper.folder_id || null;
                    this.moveModalOpen = true;
                },

                confirmMoveFolder() {
                    if (!this.moveActiveBookmark) return;
                    this.moveSaving = true;

                    const bookmarkId = this.moveActiveBookmark.bookmark_id;
                    fetch(`/repositories/bookmarks/${bookmarkId}/folder`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ folder_id: this.moveTargetFolderId })
                    })
                    .then(r => r.json())
                    .then(d => {
                        this.moveSaving = false;
                        if (d.success) {
                            this.moveModalOpen = false;
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { title: 'Folder Diperbarui', message: d.message, type: 'success' }
                            }));
                            setTimeout(() => window.location.reload(), 300);
                        }
                    })
                    .catch(() => {
                        this.moveSaving = false;
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Gagal memindahkan artikel.', type: 'error' }
                        }));
                    });
                },

                // ============ DELETE BOOKMARK ============
                deleteBookmark(id) {
                    window.sibimaConfirm({
                        title: 'Hapus dari Daftar Bacaan',
                        message: 'Apakah Anda yakin ingin menghapus artikel ini dari Daftar Bacaan?',
                        type: 'danger',
                        confirmText: 'Ya, Hapus',
                        onConfirm: () => {
                            fetch(`/repositories/bookmarks/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(r => r.json())
                            .then(d => {
                                if (d.success) {
                                    window.dispatchEvent(new CustomEvent('notify', {
                                        detail: { title: 'Dihapus', message: d.message, type: 'info' }
                                    }));
                            const el = document.getElementById(`bookmark-item-${id}`);
                            if (el) {
                                el.style.transition = 'all 0.3s ease';
                                el.style.opacity = '0';
                                el.style.transform = 'scale(0.95)';
                                setTimeout(() => window.location.reload(), 400);
                            } else {
                                window.location.reload();
                            }
                        }
                    })
                    .catch(() => {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { title: 'Kesalahan', message: 'Gagal menghapus artikel.', type: 'error' }
                        }));
                    });
                }
            };
        }
    </script>
    @endpush

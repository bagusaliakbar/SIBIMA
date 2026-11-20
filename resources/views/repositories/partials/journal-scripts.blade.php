    @push('scripts')
    <script>
        function academicJournalApp() {
            return {
                searchQuery: '{{ addslashes($query) }}',
                citationModalOpen: false,
                activeCitationFormat: 'fasilkom',
                copiedToast: false,
                copiedToastMessage: '',
                bookmarkedIds: {!! json_encode($userBookmarkIds ?? []) !!},
                isSyncingBookmark: {},

                isBookmarked(id) {
                    return this.bookmarkedIds.includes(id);
                },

                toggleBookmark(paper) {
                    const id = paper.id;
                    if (this.isSyncingBookmark[id]) return;
                    this.isSyncingBookmark[id] = true;

                    fetch('{{ route('repositories.bookmarks.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            journal_identifier: paper.id,
                            title: paper.title,
                            authors: paper.authors || [],
                            authors_string: paper.authors_string || '',
                            year: paper.year,
                            venue: paper.venue,
                            publisher: paper.publisher || '',
                            doi: paper.doi || '',
                            landing_page_url: paper.landing_page_url || '',
                            pdf_url: paper.pdf_url || '',
                            abstract: paper.abstract || '',
                            source: paper.source || 'unknown',
                            source_label: paper.source_label || '',
                            citations: paper.citations || {}
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        delete this.isSyncingBookmark[id];
                        if (data.success) {
                            if (data.is_bookmarked) {
                                if (!this.bookmarkedIds.includes(id)) {
                                    this.bookmarkedIds.push(id);
                                }
                            } else {
                                this.bookmarkedIds = this.bookmarkedIds.filter(item => item !== id);
                            }

                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    title: data.is_bookmarked ? 'Tersimpan ke Bacaan' : 'Dihapus dari Bacaan',
                                    message: data.message,
                                    type: data.is_bookmarked ? 'success' : 'info'
                                }
                            }));
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    title: 'Gagal Menyimpan',
                                    message: data.message || 'Terjadi kesalahan.',
                                    type: 'error'
                                }
                            }));
                        }
                    })
                    .catch(err => {
                        delete this.isSyncingBookmark[id];
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                title: 'Kesalahan Jaringan',
                                message: 'Tidak dapat menghubungkan ke peladen saat ini.',
                                type: 'error'
                            }
                        }));
                    });
                },
                activePaper: {
                    title: '',
                    authors_string: '',
                    year: null,
                    citations: {
                        fasilkom: '',
                        fasilkom_html: '',
                        apa: '',
                        ieee: '',
                        bibtex: '',
                        ris: ''
                    }
                },

                openCitationModal(paper) {
                    this.activePaper = paper;
                    this.citationModalOpen = true;
                    this.copiedToast = false;
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
                        return `${author} (${year}). "${title}". ${venue}.${link ? ' Tersedia di: ' + link + '.' : ''}`;
                    } else if (format === 'ris') {
                        return `TY  - JOUR\r\nTI  - ${title}\r\nAU  - ${author}\r\nT2  - ${venue}\r\nPY  - ${year}\r\nUR  - ${link}\r\nER  - `;
                    } else if (format === 'ieee') {
                        return `${author}, "${title}," ${venue}, ${year}.${link ? ' doi: ' + link : ''}`;
                    } else if (format === 'bibtex') {
                        return '@article{referensi' + year + ',\n  title = {' + title + '},\n  author = {' + author + '},\n  journal = {' + venue + '},\n  year = {' + year + '}\n}';
                    }
                    return `${author} (${year}). ${title}. ${venue}.${link ? ' ' + link : ''}`;
                },

                copyCitation() {
                    const text = this.getCurrentCitationText();
                    if (!text) return;

                    navigator.clipboard.writeText(text).then(() => {
                        this.copiedToastMessage = 'Teks sitasi berhasil disalin ke clipboard!';
                        this.copiedToast = true;
                        setTimeout(() => { this.copiedToast = false; }, 3000);
                    });
                },

                copyFasilkomWord() {
                    if (!this.activePaper) return;
                    const p = this.activePaper;
                    const plainText = (p.citations && p.citations.fasilkom) ? p.citations.fasilkom : this.buildFallbackCitation('fasilkom');
                    const htmlText = (p.citations && p.citations.fasilkom_html) ? p.citations.fasilkom_html : `<p>${plainText}</p>`;

                    // Copy rich text HTML so Word preserves italicized journal name
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
                }
            };
        }
    </script>
    @endpush

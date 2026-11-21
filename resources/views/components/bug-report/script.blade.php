<script>
// =========================================================================
// GLOBAL CONTROLLERS FOR BUG REPORT WIDGET (VANILLA JS + ALPINE DUAL COMPATIBLE)
// =========================================================================
window.currentBugReportType = 'bug';

window.switchBugReportType = function(type) {
    window.currentBugReportType = type;
    const isFeature = type === 'feature';

    // 1. Update Tabs styling
    const tabBug = document.getElementById('tab-btn-bug');
    const tabFeature = document.getElementById('tab-btn-feature');
    const typeInput = document.getElementById('bug-report-type-input');
    if (typeInput) typeInput.value = type;

    if (isFeature) {
        if (tabFeature) {
            tabFeature.className = 'flex-1 py-2 px-3 rounded-xl text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 border border-slate-200/80 dark:border-slate-700';
        }
        if (tabBug) {
            tabBug.className = 'flex-1 py-2 px-3 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition-all cursor-pointer text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200';
        }
    } else {
        if (tabBug) {
            tabBug.className = 'flex-1 py-2 px-3 rounded-xl text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs bg-white dark:bg-slate-900 text-orange-600 dark:text-orange-400 border border-slate-200/80 dark:border-slate-700';
        }
        if (tabFeature) {
            tabFeature.className = 'flex-1 py-2 px-3 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition-all cursor-pointer text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200';
        }
    }

    // 2. Update Header
    const iconContainer = document.getElementById('bug-report-header-icon-box');
    const titleEl = document.getElementById('bug-report-header-title');
    const subtitleEl = document.getElementById('bug-report-header-subtitle');

    if (iconContainer) {
        if (isFeature) {
            iconContainer.className = 'w-12 h-12 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 shadow-xs';
            iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>';
        } else {
            iconContainer.className = 'w-12 h-12 rounded-2xl bg-orange-500/10 dark:bg-orange-500/20 border border-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 shadow-xs';
            iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';
        }
    }

    if (titleEl) {
        titleEl.innerText = isFeature ? 'Usulan / Request Fitur Baru' : 'Lapor Kendala / Bug Sistem';
    }
    if (subtitleEl) {
        subtitleEl.innerText = isFeature
            ? 'Punya ide fitur untuk mempermudah bimbingan atau persuratan? Sampaikan ke Tim Pengembang SIBIMA.'
            : 'Laporan Anda akan otomatis diteruskan kepada Tim Teknis SIBIMA.';
    }

    // 3. Update Form Labels & Inputs
    const titleLabel = document.getElementById('bug-report-title-label');
    const titleInput = document.getElementById('bug-report-title-input');
    const catLabel = document.getElementById('bug-report-category-label');
    const catSelect = document.getElementById('bug-report-category-select');
    const sevLabel = document.getElementById('bug-report-severity-label');
    const sevSelect = document.getElementById('bug-report-severity-select');
    const urlLabel = document.getElementById('bug-report-url-label');
    const descLabel = document.getElementById('bug-report-desc-label');
    const descInput = document.getElementById('bug-report-desc-input');
    const stepsLabel = document.getElementById('bug-report-steps-label');
    const stepsInput = document.getElementById('bug-report-steps-input');
    const attachLabel = document.getElementById('bug-report-attachment-label');
    const submitBtnText = document.getElementById('bug-report-submit-btn-text');

    if (titleLabel) {
        titleLabel.innerHTML = isFeature ? 'Nama / Judul Fitur yang Diusulkan <span class="text-rose-500">*</span>' : 'Judul Kendala <span class="text-rose-500">*</span>';
    }
    if (titleInput) {
        titleInput.placeholder = isFeature
            ? 'Contoh: Integrasi kalender bimbingan ke Google Calendar, atau export rekap nilai'
            : 'Contoh: Tombol simpan revisi tidak merespons, atau layout berantakan';
    }

    if (catLabel) {
        catLabel.innerHTML = isFeature ? 'Kategori Usulan <span class="text-rose-500">*</span>' : 'Kategori Masalah <span class="text-rose-500">*</span>';
    }
    if (catSelect) {
        if (isFeature) {
            catSelect.innerHTML = `
                <option value="feature_request" selected>Fitur Baru yang Belum Ada di Sistem (Default)</option>
                <option value="improvement">Peningkatan / Penyempurnaan Fitur yang Ada</option>
                <option value="ui_ux">Saran Tampilan / Desain (UI/UX)</option>
                <option value="other">Ide &amp; Masukan Lainnya</option>
            `;
        } else {
            catSelect.innerHTML = `
                <option value="functionality" selected>Fitur / Fungsi Eror (Default)</option>
                <option value="ui_ux">Tampilan / Layout / UI Rusak</option>
                <option value="performance">Kinerja / Sangat Lambat / Hang</option>
                <option value="data_error">Ketidaksesuaian Data / Nilai</option>
                <option value="security">Izin Akses / Akun / Keamanan</option>
                <option value="other">Masalah Lainnya</option>
            `;
        }
    }

    if (sevLabel) {
        sevLabel.innerHTML = isFeature ? 'Tingkat Kebutuhan / Urgensi <span class="text-rose-500">*</span>' : 'Tingkat Keparahan <span class="text-rose-500">*</span>';
    }
    if (sevSelect) {
        if (isFeature) {
            sevSelect.innerHTML = `
                <option value="low">Saran Tambahan (Nice to Have)</option>
                <option value="medium" selected>Cukup Dibutuhkan (Mempermudah Aktivitas)</option>
                <option value="high">Sangat Dibutuhkan (Penting untuk Alur Kerja)</option>
                <option value="critical">Prioritas Utama (Sangat Mendesak)</option>
            `;
        } else {
            sevSelect.innerHTML = `
                <option value="low">Rendah (Minor / Masalah Estetika)</option>
                <option value="medium" selected>Sedang (Fitur Lambat / Agak Terganggu)</option>
                <option value="high">Tinggi (Fitur Utama Gagal / Eror 500)</option>
                <option value="critical">Kritis (Fatal / Seluruh Sistem Terkunci)</option>
            `;
        }
    }

    if (urlLabel) {
        urlLabel.innerText = isFeature ? 'Halaman / Menu Terkait Fitur (URL)' : 'Halaman Lokasi Bug (URL)';
    }

    if (descLabel) {
        descLabel.innerHTML = isFeature ? 'Uraian & Manfaat Fitur <span class="text-rose-500">*</span>' : 'Uraian Masalah / Deskripsi <span class="text-rose-500">*</span>';
    }
    if (descInput) {
        descInput.placeholder = isFeature
            ? 'Jelaskan bagaimana fitur ini bekerja dan apa manfaatnya bagi mahasiswa atau dosen...'
            : 'Jelaskan apa yang Anda lakukan, apa pesan eror yang muncul, dan apa hasil yang seharusnya terjadi...';
    }

    if (stepsLabel) {
        stepsLabel.innerText = isFeature ? 'Contoh Alur Kerja Fitur (Opsional)' : 'Langkah-langkah Memicu Masalah (Opsional)';
    }
    if (stepsInput) {
        stepsInput.placeholder = isFeature
            ? '1. Pengguna membuka menu X\n2. Klik tombol Y untuk aksi cepat\n3. Data otomatis tersimpan...'
            : '1. Masuk ke menu X\n2. Pilih tanggal Y\n3. Klik tombol Simpan...';
    }

    if (attachLabel) {
        attachLabel.innerText = isFeature ? 'Mockup / Sketsa / Contoh Dokumen (Opsional, Maks. 5MB)' : 'Tangkapan Layar / Bukti Gambar (Opsional, Maks. 5MB)';
    }

    if (submitBtnText) {
        submitBtnText.innerText = isFeature ? 'Kirim Usulan Fitur' : 'Kirim Laporan';
    }
};

window.openBugReportModal = function(initialType) {
    const modal = document.getElementById('bug-report-modal');
    if (modal) {
        modal.style.setProperty('display', 'block', 'important');
        const urlInput = modal.querySelector('input[name="page_url"]');
        if (urlInput && !urlInput.value) {
            urlInput.value = window.location.href;
        }
    }
    if (initialType === 'feature' || initialType === 'bug') {
        window.switchBugReportType(initialType);
    }
    if (window.innerWidth < 768) {
        window.toggleBugCollapse(true);
    }
    if (window._bugWidget) {
        window._bugWidget.showReportModal = true;
        window._bugWidget.form.page_url = window.location.href;
        window._bugWidget.form.device_info = navigator.userAgent + ' [' + window.innerWidth + 'x' + window.innerHeight + ']';
    }
};

window.closeBugReportModal = function() {
    const modal = document.getElementById('bug-report-modal');
    if (modal) {
        modal.style.setProperty('display', 'none', 'important');
    }
    if (window._bugWidget) {
        window._bugWidget.showReportModal = false;
    }
};

window.openBugHistoryModal = function() {
    const modal = document.getElementById('bug-history-modal');
    if (modal) {
        modal.style.setProperty('display', 'block', 'important');
    }
    if (window.innerWidth < 768) {
        window.toggleBugCollapse(true);
    }
    if (window._bugWidget) {
        window._bugWidget.showHistoryModal = true;
        window._bugWidget.fetchMyReports();
    } else {
        window.fetchMyReportsDirect();
    }
};

window.closeBugHistoryModal = function() {
    const modal = document.getElementById('bug-history-modal');
    if (modal) {
        modal.style.setProperty('display', 'none', 'important');
    }
    if (window._bugWidget) {
        window._bugWidget.showHistoryModal = false;
    }
};

window.openBugGuideModal = function() {
    const modal = document.getElementById('bug-guide-modal');
    if (modal) {
        modal.style.setProperty('display', 'block', 'important');
    }
    if (window.innerWidth < 768) {
        window.toggleBugCollapse(true);
    }
    if (window._bugWidget) {
        window._bugWidget.showGuideModal = true;
    }
};

window.closeBugGuideModal = function() {
    const modal = document.getElementById('bug-guide-modal');
    if (modal) {
        modal.style.setProperty('display', 'none', 'important');
    }
    if (window._bugWidget) {
        window._bugWidget.showGuideModal = false;
    }
};

window.closeAllBugModals = function() {
    window.closeBugReportModal();
    window.closeBugHistoryModal();
    window.closeBugGuideModal();
};

window.toggleBugCollapse = function(collapse) {
    const container = document.getElementById('bug-report-dock-container');
    const dock = document.getElementById('bug-report-full-dock');
    const pill = document.getElementById('bug-report-collapsed-pill');

    let isCurrentlyCollapsed = true;
    if (dock) {
        const display = window.getComputedStyle(dock).display;
        isCurrentlyCollapsed = (display === 'none');
    }
    const shouldCollapse = (typeof collapse === 'boolean') ? collapse : !isCurrentlyCollapsed;

    if (container) {
        if (shouldCollapse) {
            container.classList.add('dock-collapsed');
            container.classList.remove('dock-expanded');
        } else {
            container.classList.remove('dock-collapsed');
            container.classList.add('dock-expanded');
        }
    }

    if (dock) {
        dock.style.setProperty('display', shouldCollapse ? 'none' : 'flex', 'important');
    }
    if (pill) {
        pill.style.setProperty('display', shouldCollapse ? 'flex' : 'none', 'important');
    }

    if (window.innerWidth >= 768) {
        try {
            localStorage.setItem('sibima_bug_dock_collapsed', shouldCollapse ? 'true' : 'false');
        } catch(e) {}
    }

    if (window._bugWidget) {
        window._bugWidget.isCollapsed = shouldCollapse;
    }
};

// Auto initialize based on viewport width
document.addEventListener('DOMContentLoaded', function() {
    const isMobile = window.innerWidth < 768;
    if (isMobile) {
        window.toggleBugCollapse(true);
    } else {
        const savedPref = localStorage.getItem('sibima_bug_dock_collapsed');
        if (savedPref === 'true') {
            window.toggleBugCollapse(true);
        } else {
            window.toggleBugCollapse(false);
        }
    }
});

// Auto collapse on mobile when tapping outside
document.addEventListener('click', function(e) {
    if (window.innerWidth < 768) {
        const container = document.getElementById('bug-report-dock-container');
        const dock = document.getElementById('bug-report-full-dock');
        if (container && dock && window.getComputedStyle(dock).display !== 'none') {
            if (!container.contains(e.target)) {
                window.toggleBugCollapse(true);
            }
        }
    }
});

// Auto collapse on window resize down to mobile
let _bugResizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(_bugResizeTimer);
    _bugResizeTimer = setTimeout(function() {
        if (window.innerWidth < 768) {
            const dock = document.getElementById('bug-report-full-dock');
            if (dock && window.getComputedStyle(dock).display !== 'none') {
                window.toggleBugCollapse(true);
            }
        }
    }, 250);
});

window.fetchMyReportsDirect = async function() {
    const listContainer = document.getElementById('bug-history-list');
    const loadingState = document.getElementById('bug-history-loading');
    const emptyState = document.getElementById('bug-history-empty');

    if (loadingState) loadingState.style.display = 'block';
    if (emptyState) emptyState.style.display = 'none';

    try {
        const res = await fetch('{{ route("bug-reports.my-reports") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (loadingState) loadingState.style.display = 'none';

        if (data.success && data.reports && data.reports.length > 0) {
            if (emptyState) emptyState.style.display = 'none';
            if (listContainer) {
                const escapeBugHtml = (str) => String(str ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');

                listContainer.innerHTML = data.reports.map(item => {
                    const isFeature = item.is_feature_request || (item.ticket_number && item.ticket_number.startsWith('REQ-'));
                    const typeBadge = isFeature
                        ? `<span class="px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60">USULAN FITUR</span>`
                        : `<span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60">BUG SISTEM</span>`;
                    const ticketColor = isFeature ? 'text-indigo-600 dark:text-indigo-400' : 'text-orange-600 dark:text-orange-400';

                    const safeTicket = escapeBugHtml(item.ticket_number);
                    const safeCreated = escapeBugHtml(item.created_at_human);
                    const safeTitle = escapeBugHtml(item.title);
                    const safeStatus = escapeBugHtml(item.status_label);
                    const safeDesc = escapeBugHtml(item.description);
                    const safeAdminNotes = escapeBugHtml(item.admin_notes);

                    return `
                    <div class="p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-800/60 shadow-2xs space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[11px] font-mono font-bold ${ticketColor}">${safeTicket}</span>
                                    ${typeBadge}
                                    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">${safeCreated}</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">${safeTitle}</h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                ${safeStatus}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2">${safeDesc}</p>
                        ${item.admin_notes ? `
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/80 border-l-4 border-emerald-500 text-xs text-slate-700 dark:text-slate-300 space-y-1">
                                <div class="font-bold text-slate-800 dark:text-slate-200 text-[11px]">Tanggapan Tim SIBIMA:</div>
                                <p class="text-[11px] leading-relaxed text-slate-600 dark:text-slate-300">${safeAdminNotes}</p>
                            </div>
                        ` : ''}
                    </div>
                `;}).join('');
            }
        } else {
            if (emptyState) emptyState.style.display = 'block';
            if (listContainer) listContainer.innerHTML = '';
        }
    } catch (err) {
        console.error('Error loading bug reports:', err);
        if (loadingState) loadingState.style.display = 'none';
    }
};

window.handleBugFormSubmit = async function(event) {
    if (event) event.preventDefault();
    const form = event.target || document.getElementById('bug-report-form');
    if (!form) return;

    const btnSubmit = form.querySelector('button[type="submit"]');
    const errBox = document.getElementById('bug-form-error');
    const isFeature = window.currentBugReportType === 'feature';

    if (errBox) errBox.style.display = 'none';
    if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerText = 'Mengirim...';
    }

    try {
        const formData = new FormData(form);
        const res = await fetch('{{ route("bug-reports.store") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData,
        });

        const data = await res.json();
        if (res.ok && data.success) {
            window.closeBugReportModal();
            form.reset();
            const fileNameSpan = document.getElementById('selected-file-name');
            if (fileNameSpan) fileNameSpan.innerText = '';
            window.sibimaToast(data.message + ' (Nomor Tiket: ' + data.ticket_number + ')', 'success', 'Laporan Terkirim');
        } else {
            if (errBox) {
                errBox.innerText = data.message || 'Gagal mengirim laporan.';
                errBox.style.display = 'block';
            } else {
                window.sibimaToast(data.message || 'Gagal mengirim laporan.', 'error', 'Gagal Mengirim');
            }
        }
    } catch (e) {
        console.error('Submit error:', e);
        if (errBox) {
            errBox.innerText = 'Terjadi kesalahan jaringan saat mengirim laporan.';
            errBox.style.display = 'block';
        }
    } finally {
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerText = isFeature ? 'Kirim Usulan Fitur' : 'Kirim Laporan';
        }
    }
};

// ESC key listener to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.closeAllBugModals();
    }
});
</script>

<script>
// =========================================================================
// GLOBAL CONTROLLERS FOR BUG REPORT WIDGET (VANILLA JS + ALPINE DUAL COMPATIBLE)
// =========================================================================
window.openBugReportModal = function() {
    const modal = document.getElementById('bug-report-modal');
    if (modal) {
        modal.style.setProperty('display', 'block', 'important');
        const urlInput = modal.querySelector('input[name="page_url"]');
        if (urlInput && !urlInput.value) {
            urlInput.value = window.location.href;
        }
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
    const dock = document.getElementById('bug-report-full-dock');
    const pill = document.getElementById('bug-report-collapsed-pill');
    const isCurrentlyCollapsed = dock && dock.style.display === 'none';
    const shouldCollapse = (typeof collapse === 'boolean') ? collapse : !isCurrentlyCollapsed;

    if (dock) {
        dock.style.setProperty('display', shouldCollapse ? 'none' : 'flex', 'important');
    }
    if (pill) {
        pill.style.setProperty('display', shouldCollapse ? 'flex' : 'none', 'important');
    }
    if (window._bugWidget) {
        window._bugWidget.isCollapsed = shouldCollapse;
    }
};


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
                listContainer.innerHTML = data.reports.map(item => `
                    <div class="p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-800/60 shadow-2xs space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[11px] font-mono font-bold text-orange-600 dark:text-orange-400">${item.ticket_number}</span>
                                    <span class="text-slate-300 dark:text-slate-700">•</span>
                                    <span class="text-[10px] text-slate-400">${item.created_at_human}</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">${item.title}</h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                ${item.status_label}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2">${item.description}</p>
                        ${item.admin_notes ? `
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/80 border-l-4 border-emerald-500 text-xs text-slate-700 dark:text-slate-300 space-y-1">
                                <div class="font-bold text-slate-800 dark:text-slate-200 text-[11px]">Tanggapan Admin / Kaprodi:</div>
                                <p class="text-[11px] leading-relaxed text-slate-600 dark:text-slate-300">${item.admin_notes}</p>
                            </div>
                        ` : ''}
                    </div>
                `).join('');
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
            alert('Laporan bug berhasil dikirim! Nomor Tiket: ' + data.ticket_number);
        } else {
            if (errBox) {
                errBox.innerText = data.message || 'Gagal mengirim laporan bug.';
                errBox.style.display = 'block';
            } else {
                alert(data.message || 'Gagal mengirim laporan bug.');
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
            btnSubmit.innerText = 'Kirim Laporan';
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

<!-- ========================================================================= -->
<!-- FLOATING CAPSULE DOCK (BOTTOM-RIGHT CORNER)                                -->
<!-- ========================================================================= -->
<div id="bug-report-dock-container"
     class="select-none"
     style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 9999999 !important; pointer-events: auto !important; display: flex !important; flex-direction: column !important; align-items: center !important;">

    <!-- 1. Collapsed Mini Pill (When user clicks arrow to collapse) -->
    <div id="bug-report-collapsed-pill"
         style="display: none; pointer-events: auto !important;">
        <button onclick="window.toggleBugCollapse(false); event.stopPropagation();"
                type="button"
                style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: rgba(255, 255, 255, 0.98); border-radius: 9999px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; cursor: pointer; pointer-events: auto !important;"
                class="group relative flex items-center gap-2 px-3 py-2.5 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 rounded-full shadow-2xl border border-slate-200 dark:border-slate-700 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer"
                title="Buka Toolbar Pelaporan Bug">
            <span class="flex h-3 w-3 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
            </span>
            <span class="text-xs font-bold tracking-tight text-slate-800 dark:text-gray-400">Lapor Bug</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors">
                <path d="M5 15l7-7 7 7" />
            </svg>
        </button>
    </div>

    <!-- 2. Full Vertical Capsule Dock (Pixel-Perfect Reference Design) -->
    <div id="bug-report-full-dock"
         style="width: 56px !important; min-width: 56px !important; max-width: 56px !important; background: #ffffff; border-radius: 9999px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 4px 10px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0; padding: 14px 7px; display: flex !important; flex-direction: column !important; align-items: center !important; gap: 10px !important; pointer-events: auto !important;"
         class="bg-white dark:bg-slate-900 rounded-full shadow-2xl border border-slate-200 dark:border-slate-700 py-3.5 px-1.5 flex flex-col items-center gap-2.5">
        
        <!-- Grip Drag Indicator (6 dots) -->
        <div class="flex flex-col items-center justify-center py-0.5 cursor-default text-slate-400 dark:text-slate-500" title="Toolbar Pelaporan Bug">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" style="width: 16px; height: 16px;">
                <circle cx="7" cy="5" r="1.5" />
                <circle cx="13" cy="5" r="1.5" />
                <circle cx="7" cy="10" r="1.5" />
                <circle cx="13" cy="10" r="1.5" />
                <circle cx="7" cy="15" r="1.5" />
                <circle cx="13" cy="15" r="1.5" />
            </svg>
        </div>

        <!-- Divider Line -->
        <div style="width: 24px; height: 1.5px; background: #e2e8f0; border-radius: 9999px;" class="w-6 h-[1.5px] bg-slate-200 dark:bg-slate-700/80 rounded-full mb-0.5"></div>

        <!-- Button 1: Gray Circle with Left Arrow (Collapse/Perkecil) -->
        <button onclick="window.toggleBugCollapse(true); event.stopPropagation();"
                type="button"
                style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #f1f5f9; color: #334155; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; border: none !important; padding: 0 !important; overflow: hidden !important; pointer-events: auto !important;"
                class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-90 group relative cursor-pointer"
                title="Perkecil Toolbar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; min-width: 20px; min-height: 20px; max-width: 20px; max-height: 20px;" class="group-hover:-translate-x-0.5 transition-transform">
                <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                Perkecil
            </span>
        </button>

        <!-- Button 2: Orange Circle with Info (ℹ) (Panduan Pelaporan Bug) -->
        <button onclick="window.openBugGuideModal(); event.stopPropagation();"
                type="button"
                style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #f97316; color: #ffffff; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; border: none !important; padding: 0 !important; overflow: hidden !important; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.35); pointer-events: auto !important;"
                class="w-10 h-10 rounded-full bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-90 shadow-md shadow-orange-500/25 group relative cursor-pointer"
                title="Panduan & Bantuan Pelaporan">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; min-width: 20px; min-height: 20px; max-width: 20px; max-height: 20px;">
                <circle cx="12" cy="12" r="8.5" />
                <circle cx="12" cy="8" r="1.1" fill="currentColor" stroke="none" />
                <path d="M12 11.5v4.5" />
            </svg>
            <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                Panduan
            </span>
        </button>

        <!-- Button 3: Green Circle with Checkmark (✓) (Status Laporan Saya) -->
        <button onclick="window.openBugHistoryModal(); event.stopPropagation();"
                type="button"
                style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #10b981; color: #ffffff; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; border: none !important; padding: 0 !important; overflow: hidden !important; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35); pointer-events: auto !important;"
                class="w-10 h-10 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-90 shadow-md shadow-emerald-500/25 group relative cursor-pointer"
                title="Status & Riwayat Laporan Saya">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; min-width: 20px; min-height: 20px; max-width: 20px; max-height: 20px;">
                <path d="M5 13l4.5 4.5L19 7" />
            </svg>
            <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                Status Laporan
            </span>
        </button>

        <!-- Button 5: Orange Circle with Edit/Pencil (📝) (Lapor Bug Baru) -->
        <button onclick="window.openBugReportModal(); event.stopPropagation();"
                type="button"
                style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #f97316; color: #ffffff; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; border: none !important; padding: 0 !important; overflow: hidden !important; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4); pointer-events: auto !important;"
                class="w-10 h-10 rounded-full bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-90 shadow-lg shadow-orange-500/30 group relative cursor-pointer"
                title="Laporkan Bug Baru">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; min-width: 20px; min-height: 20px; max-width: 20px; max-height: 20px;">
                <path d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5" />
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                Lapor Bug Baru
            </span>
        </button>

        @if(in_array(Auth::user()?->role, ['admin', 'kaprodi']))
        <!-- Quick Link for Admin/Kaprodi to Bug Inbox -->
        <div style="width: 24px; height: 1.5px; background: #e2e8f0; border-radius: 9999px;" class="w-6 h-[1.5px] bg-slate-200 dark:bg-slate-700/80 rounded-full my-0.5"></div>
        <a href="{{ route('admin.bug-reports.index') }}" 
           style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #eef2ff; color: #4f46e5; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; text-decoration: none !important; padding: 0 !important; overflow: hidden !important; pointer-events: auto !important;"
           class="w-10 h-10 rounded-full bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-90 group relative"
           title="Inbox Kelola Bug (Admin/Kaprodi)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; min-width: 20px; min-height: 20px; max-width: 20px; max-height: 20px;">
                <path d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                Panel Kelola Bug
            </span>
        </a>
        @endif

    </div>
</div>


<!-- ========================================================================= -->
<!-- MODAL 1: FORMULIR PELAPORAN BUG                                            -->
<!-- ========================================================================= -->
<div id="bug-report-modal"
     style="display: none; position: fixed !important; inset: 0 !important; z-index: 99999999 !important; overflow-y: auto;"
     role="dialog"
     aria-modal="true">
    
    <!-- Backdrop Overlay -->
    <div onclick="window.closeBugReportModal()"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
         style="position: fixed; inset: 0; pointer-events: auto; cursor: pointer;"></div>

    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
        <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 pointer-events-auto">
            
            <!-- Modal Header -->
            <div class="px-6 sm:px-8 pt-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between bg-gradient-to-r from-orange-50/60 via-amber-50/40 to-transparent dark:from-orange-950/20 dark:via-transparent">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-orange-500/10 dark:bg-orange-500/20 border border-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
                            Lapor Kendala / Bug Sistem
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Laporan Anda akan otomatis diteruskan kepada Tim Teknis SIBIMA.
                        </p>
                    </div>
                </div>

                <button onclick="window.closeBugReportModal()"
                        type="button"
                        class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body / Form -->
            <form id="bug-report-form" onsubmit="window.handleBugFormSubmit(event)" class="p-6 sm:p-8 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                @csrf
                
                <!-- Judul Laporan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Judul Kendala <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           required
                           maxlength="255"
                           placeholder="Contoh: Tombol simpan revisi tidak merespons, atau layout berantakan"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>

                <!-- Grid: Kategori & Tingkat Keparahan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Kategori Masalah <span class="text-rose-500">*</span>
                        </label>
                        <select name="category"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                            <option value="functionality">⚙️ Fitur / Fungsi Eror (Default)</option>
                            <option value="ui_ux">🎨 Tampilan / Layout / UI Rusak</option>
                            <option value="performance">⚡ Kinerja / Sangat Lambat / Hang</option>
                            <option value="data_error">📄 Ketidaksesuaian Data / Nilai</option>
                            <option value="security">🔒 Izin Akses / Akun / Keamanan</option>
                            <option value="other">❓ Masalah Lainnya</option>
                        </select>
                    </div>

                    <!-- Severity -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Tingkat Keparahan <span class="text-rose-500">*</span>
                        </label>
                        <select name="severity"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                            <option value="low">🟢 Rendah (Minor / Masalah Estetika)</option>
                            <option value="medium" selected>🟡 Sedang (Fitur Lambat / Agak Terganggu)</option>
                            <option value="high">🟠 Tinggi (Fitur Utama Gagal / Eror 500)</option>
                            <option value="critical">🔴 Kritis (Fatal / Seluruh Sistem Terkunci)</option>
                        </select>
                    </div>
                </div>

                <!-- Halaman Terkait (URL) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Halaman Lokasi Bug (URL)
                        </label>
                        <button type="button" 
                                onclick="const inp = document.querySelector('input[name=\'page_url\']'); if (inp) inp.value = window.location.href;"
                                class="text-[11px] font-semibold text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                            Ambil URL Sekarang
                        </button>
                    </div>
                    <input type="text"
                           name="page_url"
                           placeholder="https://..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-xs font-mono focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>

                <!-- Deskripsi Bug -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Uraian Masalah / Deskripsi <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description"
                              required
                              rows="3"
                              placeholder="Jelaskan apa yang Anda lakukan, apa pesan eror yang muncul, dan apa hasil yang seharusnya terjadi..."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"></textarea>
                </div>

                <!-- Langkah Reproduksi (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Langkah-langkah Memicu Masalah (Opsional)
                    </label>
                    <textarea name="steps_to_reproduce"
                              rows="2"
                              placeholder="1. Masuk ke menu X&#10;2. Pilih tanggal Y&#10;3. Klik tombol Simpan..."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"></textarea>
                </div>

                <!-- Upload Bukti Tangkapan Layar (Screenshot) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Tangkapan Layar / Bukti Gambar (Opsional, Maks. 5MB)
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-700 border-dashed rounded-2xl hover:border-orange-400 dark:hover:border-orange-500 transition-colors bg-slate-50/50 dark:bg-slate-800/40 relative">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-xs text-slate-600 dark:text-slate-400 justify-center">
                                <label class="relative cursor-pointer font-bold text-orange-600 dark:text-orange-400 hover:underline">
                                    <span>Pilih file attachment</span>
                                    <input type="file" 
                                           name="attachment"
                                           accept="image/*,.pdf" 
                                           class="sr-only"
                                           onchange="const span = document.getElementById('selected-file-name'); if(span) span.innerText = this.files[0] ? this.files[0].name : '';">
                                </label>
                            </div>
                            <p id="selected-file-name" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1"></p>
                            <p class="text-[10px] text-slate-400">PNG, JPG, WEBP atau PDF hingga 5MB</p>
                        </div>
                    </div>
                </div>

                <!-- Error Alert (If Any) -->
                <div id="bug-form-error" style="display: none;" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-xs font-semibold text-rose-600 dark:text-rose-400"></div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button"
                            onclick="window.closeBugReportModal()"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-600 text-white text-xs font-black uppercase tracking-wider shadow-md shadow-orange-600/20 flex items-center gap-2 transition-all cursor-pointer">
                        <span>Kirim Laporan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: STATUS & RIWAYAT LAPORAN SAYA                                     -->
<!-- ========================================================================= -->
<div id="bug-history-modal"
     style="display: none; position: fixed !important; inset: 0 !important; z-index: 99999999 !important; overflow-y: auto;"
     role="dialog"
     aria-modal="true">
    
    <!-- Backdrop Overlay -->
    <div onclick="window.closeBugHistoryModal()"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
         style="position: fixed; inset: 0; pointer-events: auto; cursor: pointer;"></div>

    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
        <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 flex flex-col max-h-[85vh] pointer-events-auto">
            
            <!-- Header -->
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-emerald-50/60 via-teal-50/40 to-transparent dark:from-emerald-950/20 dark:via-transparent shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">
                            Status Laporan Bug Saya
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Pantau progres penanganan kendala yang pernah Anda kirimkan.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button onclick="window.fetchMyReportsDirect()"
                            type="button"
                            title="Segarkan Data"
                            class="w-8 h-8 rounded-lg text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <button onclick="window.closeBugHistoryModal()"
                            type="button"
                            class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- List Content -->
            <div class="p-6 sm:p-8 overflow-y-auto space-y-4 custom-scrollbar flex-1">
                <!-- Loading State -->
                <div id="bug-history-loading" style="display: none;" class="py-12 text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-500 border-t-transparent"></div>
                    <p class="text-xs font-semibold text-slate-400 mt-2">Memuat riwayat laporan...</p>
                </div>

                <!-- Empty State -->
                <div id="bug-history-empty" style="display: none;" class="py-12 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">Belum Ada Laporan Bug</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Anda belum pernah mengirimkan laporan kendala teknis. Gunakan tombol oranye 📝 untuk membuat laporan baru.
                    </p>
                </div>

                <!-- Container for Report Cards -->
                <div id="bug-history-list" class="space-y-3.5"></div>
            </div>

            <!-- Footer -->
            <div class="p-4 px-6 sm:px-8 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex justify-end shrink-0">
                <button type="button" 
                        onclick="window.closeBugHistoryModal()"
                        class="px-5 py-2 bg-slate-800 dark:bg-white text-white dark:text-slate-800 text-xs font-bold rounded-xl uppercase tracking-wider hover:bg-slate-900 transition-all shadow-xs cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: PANDUAN PELAPORAN BUG                                             -->
<!-- ========================================================================= -->
<div id="bug-guide-modal"
     style="display: none; position: fixed !important; inset: 0 !important; z-index: 99999999 !important; overflow-y: auto;"
     role="dialog"
     aria-modal="true">
    
    <!-- Backdrop Overlay -->
    <div onclick="window.closeBugGuideModal()"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
         style="position: fixed; inset: 0; pointer-events: auto; cursor: pointer;"></div>

    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
        <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 pointer-events-auto">
            
            <!-- Header -->
            <div class="px-6 pt-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-amber-50/60 to-transparent dark:from-amber-950/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">
                            Panduan Pelaporan Kendala
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Tips agar laporan bug dapat ditangani lebih cepat.
                        </p>
                    </div>
                </div>

                <button onclick="window.closeBugGuideModal()"
                        type="button"
                        class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Guide List -->
            <div class="p-6 space-y-4 text-xs text-slate-600 dark:text-slate-300">
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 font-black flex items-center justify-center shrink-0 text-[11px]">1</span>
                    <div>
                        <h5 class="font-bold text-slate-800 dark:text-slate-100 mb-0.5">Sertakan Judul Spesifik</h5>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Gunakan judul yang jelas, misalnya: <em>"Gagal klik tombol Validasi di menu Pendaftaran Seminar"</em>.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 font-black flex items-center justify-center shrink-0 text-[11px]">2</span>
                    <div>
                        <h5 class="font-bold text-slate-800 dark:text-slate-100 mb-0.5">Lampirkan Screenshot (Tangkapan Layar)</h5>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Screenshot sangat membantu tim pengembang melihat kode pesan galat atau visual yang berantakan.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 font-black flex items-center justify-center shrink-0 text-[11px]">3</span>
                    <div>
                        <h5 class="font-bold text-slate-800 dark:text-slate-100 mb-0.5">Tulis Langkah Kejadian</h5>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Tuliskan tombol apa yang diklik sebelum error muncul agar kendala mudah direka ulang oleh admin.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 font-black flex items-center justify-center shrink-0 text-[11px]">4</span>
                    <div>
                        <h5 class="font-bold text-slate-800 dark:text-slate-100 mb-0.5">Pantau di Menu Status Laporan (Tombol Hijau ✓)</h5>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Klik tombol centang hijau pada kapsul untuk melihat apakah tiket Anda sudah selesai diperbaiki.</p>
                    </div>
                </div>
            </div>

            <div class="p-4 px-6 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="button"
                        onclick="window.closeBugGuideModal()"
                        class="px-5 py-2 bg-orange-600 text-white text-xs font-bold rounded-xl uppercase tracking-wider hover:bg-orange-700 transition-colors shadow-xs cursor-pointer">
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

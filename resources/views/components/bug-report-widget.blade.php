<script>
window.bugReportWidgetData = function() {
    return {
        isCollapsed: false,
        isHidden: false,
        showReportModal: false,
        showHistoryModal: false,
        showGuideModal: false,
        isSubmitting: false,
        isLoadingReports: false,
        errorMessage: '',
        screenshotPreview: null,
        screenshotFile: null,
        myReports: [],

        form: {
            title: '',
            category: 'functionality',
            severity: 'medium',
            page_url: '',
            description: '',
            steps_to_reproduce: '',
            device_info: '',
        },

        init() {
            window._bugWidget = this;
            this.isCollapsed = false;
            this.isHidden = false;
        },

        toggleCollapse() {
            this.isCollapsed = !this.isCollapsed;
        },

        hideWidget() {
            this.isHidden = true;
        },

        unhideWidget() {
            this.isHidden = false;
            this.isCollapsed = false;
        },

        openReportModal() {
            this.errorMessage = '';
            this.form.page_url = window.location.href;
            this.form.device_info = `${navigator.userAgent} [${window.innerWidth}x${window.innerHeight}]`;
            this.showReportModal = true;
        },

        openMyReportsModal() {
            this.showHistoryModal = true;
            this.fetchMyReports();
        },

        openGuideModal() {
            this.showGuideModal = true;
        },

        closeAllModals() {
            this.showReportModal = false;
            this.showHistoryModal = false;
            this.showGuideModal = false;
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal adalah 5MB.');
                return;
            }

            this.screenshotFile = file;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.screenshotPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.screenshotPreview = null;
            }
        },

        removeScreenshot() {
            this.screenshotFile = null;
            this.screenshotPreview = null;
            if (this.$refs && this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        async submitReport() {
            if (!this.form.title.trim() || !this.form.description.trim()) {
                this.errorMessage = 'Judul dan Deskripsi kendala wajib diisi.';
                return;
            }

            this.isSubmitting = true;
            this.errorMessage = '';

            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('title', this.form.title);
                formData.append('category', this.form.category);
                formData.append('severity', this.form.severity);
                formData.append('page_url', this.form.page_url);
                formData.append('description', this.form.description);
                formData.append('steps_to_reproduce', this.form.steps_to_reproduce || '');
                formData.append('device_info', this.form.device_info || `${navigator.userAgent} [${window.innerWidth}x${window.innerHeight}]`);

                if (this.screenshotFile) {
                    formData.append('attachment', this.screenshotFile);
                }

                const response = await fetch('{{ route("bug-reports.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showReportModal = false;
                    
                    // Reset form
                    this.form.title = '';
                    this.form.description = '';
                    this.form.steps_to_reproduce = '';
                    this.removeScreenshot();

                    // Trigger toast notification if available
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'success',
                            title: 'Laporan Terkirim!',
                            message: `Nomor Tiket: ${data.ticket_number}. Tim Kaprodi & Admin telah menerima laporan Anda.`
                        }
                    }));
                } else {
                    this.errorMessage = data.message || 'Gagal mengirim laporan. Silakan periksa formulir Anda.';
                }
            } catch (err) {
                console.error('Error submitting bug report:', err);
                this.errorMessage = 'Terjadi kesalahan koneksi saat mengirim laporan bug.';
            } finally {
                this.isSubmitting = false;
            }
        },

        async fetchMyReports() {
            this.isLoadingReports = true;
            try {
                const response = await fetch('{{ route("bug-reports.my-reports") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.myReports = data.reports;
                }
            } catch (err) {
                console.error('Error fetching my reports:', err);
            } finally {
                this.isLoadingReports = false;
            }
        }
    };
};

window.bugReportWidget = window.bugReportWidgetData;

if (window.Alpine) {
    window.Alpine.data('bugReportWidget', window.bugReportWidgetData);
} else {
    document.addEventListener('alpine:init', () => {
        if (window.Alpine) {
            window.Alpine.data('bugReportWidget', window.bugReportWidgetData);
        }
    });
}
</script>

<div x-data="bugReportWidgetData()" 
     class="relative"
     @keydown.escape.window="closeAllModals()">

    <!-- ========================================================================= -->
    <!-- FLOATING CAPSULE DOCK (BOTTOM-RIGHT CORNER)                                -->
    <!-- ========================================================================= -->
    <div id="bug-report-dock"
         class="fixed bottom-6 right-6 z-[99990] flex flex-col items-center select-none transition-all duration-300"
         style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 99990 !important; display: flex !important; flex-direction: column !important; align-items: center !important;"
         :class="{ 'opacity-0 pointer-events-none translate-y-4': isHidden, 'opacity-100 translate-y-0': !isHidden }">

        <!-- Collapsed Mini Pill (When user clicks arrow to collapse) -->
        <div x-show="isCollapsed" 
             style="display: none;">
            <button @click="isCollapsed = false"
                    onclick="if(window._bugWidget) window._bugWidget.isCollapsed = false;"
                    type="button"
                    style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: rgba(255, 255, 255, 0.98); border-radius: 9999px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; cursor: pointer;"
                    class="group relative flex items-center gap-2 px-3 py-2.5 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 rounded-full shadow-2xl border border-slate-200 dark:border-slate-700 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer"
                    title="Buka Toolbar Pelaporan Bug">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                </span>
                <span class="text-xs font-bold tracking-tight text-slate-800 dark:text-white">Lapor Bug</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors">
                    <path d="M5 15l7-7 7 7" />
                </svg>
            </button>
        </div>

        <!-- Full Vertical Capsule (Exact Reference Design) -->
        <div x-show="!isCollapsed"
             style="width: 56px !important; min-width: 56px !important; max-width: 56px !important; background: #ffffff; border-radius: 9999px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 4px 10px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0; padding: 14px 7px; display: flex; flex-direction: column; align-items: center; gap: 10px;"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-4"
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
            <button @click="toggleCollapse()"
                    onclick="if(window._bugWidget) window._bugWidget.toggleCollapse();"
                    type="button"
                    style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #f1f5f9; color: #334155; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; border: none !important; padding: 0 !important; overflow: hidden !important;"
                    class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 group relative cursor-pointer"
                    title="Perkecil Toolbar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; min-width: 20px; min-height: 20px; max-width: 20px; max-height: 20px;" class="group-hover:-translate-x-0.5 transition-transform">
                    <path d="M19 12H5M12 19l-7-7 7-7" />
                </svg>
                <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                    Perkecil
                </span>
            </button>

            <!-- Button 2: Orange Circle with Info (ℹ) (Panduan Pelaporan Bug) -->
            <button @click="openGuideModal()"
                    onclick="if(window._bugWidget) window._bugWidget.openGuideModal();"
                    type="button"
                    style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #f97316; color: #ffffff; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; border: none !important; padding: 0 !important; overflow: hidden !important; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.35);"
                    class="w-10 h-10 rounded-full bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 shadow-md shadow-orange-500/25 group relative cursor-pointer"
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

            <!-- Button 3: Red Circle with Cross (✕) (Tutup/Sembunyikan Sementara) -->
            <button @click="hideWidget()"
                    onclick="if(window._bugWidget) window._bugWidget.hideWidget();"
                    type="button"
                    style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #ef4444; color: #ffffff; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; border: none !important; padding: 0 !important; overflow: hidden !important; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);"
                    class="w-10 h-10 rounded-full bg-rose-500 hover:bg-rose-600 text-white flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 shadow-md shadow-rose-500/25 group relative cursor-pointer"
                    title="Sembunyikan Toolbar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; min-width: 18px; min-height: 18px; max-width: 18px; max-height: 18px;">
                    <path d="M18 6L6 18M6 6l12 12" />
                </svg>
                <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                    Sembunyikan
                </span>
            </button>

            <!-- Button 4: Green Circle with Checkmark (✓) (Status Laporan Saya) -->
            <button @click="openMyReportsModal()"
                    onclick="if(window._bugWidget) window._bugWidget.openMyReportsModal();"
                    type="button"
                    style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #10b981; color: #ffffff; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; border: none !important; padding: 0 !important; overflow: hidden !important; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);"
                    class="w-10 h-10 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 shadow-md shadow-emerald-500/25 group relative cursor-pointer"
                    title="Status & Riwayat Laporan Saya">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; min-width: 20px; min-height: 20px; max-width: 20px; max-height: 20px;">
                    <path d="M5 13l4.5 4.5L19 7" />
                </svg>
                <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                    Status Laporan
                </span>
            </button>

            <!-- Button 5: Orange Circle with Edit/Pencil (📝) (Lapor Bug Baru) -->
            <button @click="openReportModal()"
                    onclick="if(window._bugWidget) window._bugWidget.openReportModal();"
                    type="button"
                    style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #f97316; color: #ffffff; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; border: none !important; padding: 0 !important; overflow: hidden !important; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);"
                    class="w-10 h-10 rounded-full bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 shadow-lg shadow-orange-500/30 group relative cursor-pointer"
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
               style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; border-radius: 9999px !important; background: #eef2ff; color: #4f46e5; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; text-decoration: none !important; padding: 0 !important; overflow: hidden !important;"
               class="w-10 h-10 rounded-full bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 group relative"
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

    <!-- Floating Re-open Trigger Pill (When completely hidden) -->
    <div x-show="isHidden"
         style="display: none; position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 99990 !important;"
         class="fixed bottom-6 right-6 z-[99990]">
        <button @click="unhideWidget()"
                onclick="if(window._bugWidget) window._bugWidget.unhideWidget();"
                type="button"
                style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: linear-gradient(135deg, #f97316, #ea580c); color: #ffffff; border-radius: 9999px; box-shadow: 0 10px 25px rgba(249, 115, 22, 0.35); border: none; cursor: pointer;"
                class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white text-xs font-bold rounded-full shadow-xl shadow-orange-500/25 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5" />
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            <span>Bantuan & Bug</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: FORMULIR PELAPORAN BUG                                            -->
    <!-- ========================================================================= -->
    <div id="bug-report-modal"
         x-show="showReportModal"
         style="display: none; position: fixed !important; inset: 0 !important; z-index: 999999 !important;"
         class="fixed inset-0 overflow-y-auto"
         role="dialog"
         aria-modal="true">
        
        <!-- Backdrop Overlay -->
        <div x-show="showReportModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showReportModal = false"
             onclick="if(window._bugWidget) window._bugWidget.showReportModal = false;"
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
             style="position: fixed; inset: 0; pointer-events: auto;"></div>

        <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
            <div x-show="showReportModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 pointer-events-auto">
                
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
                                Laporan Anda akan otomatis diteruskan kepada Kaprodi & Tim Administrator SIBIMA.
                            </p>
                        </div>
                    </div>

                    <button @click="showReportModal = false"
                            onclick="if(window._bugWidget) window._bugWidget.showReportModal = false;"
                            type="button"
                            class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body / Form -->
                <form @submit.prevent="submitReport" class="p-6 sm:p-8 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    
                    <!-- Judul Laporan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Judul Kendala <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               x-model="form.title"
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
                            <select x-model="form.category"
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
                            <select x-model="form.severity"
                                    required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                <option value="low">🟢 Rendah (Minor / Masalah Estetika)</option>
                                <option value="medium">🟡 Sedang (Fitur Lambat / Agak Terganggu)</option>
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
                                    @click="form.page_url = window.location.href"
                                    class="text-[11px] font-semibold text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                                Ambil URL Sekarang
                            </button>
                        </div>
                        <input type="text"
                               x-model="form.page_url"
                               placeholder="https://..."
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-xs font-mono focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                    </div>

                    <!-- Deskripsi Bug -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Uraian Masalah / Deskripsi <span class="text-rose-500">*</span>
                        </label>
                        <textarea x-model="form.description"
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
                        <textarea x-model="form.steps_to_reproduce"
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
                            <template x-if="!screenshotPreview">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-xs text-slate-600 dark:text-slate-400 justify-center">
                                        <label class="relative cursor-pointer font-bold text-orange-600 dark:text-orange-400 hover:underline focus-within:outline-hidden">
                                            <span>Pilih file screenshot</span>
                                            <input type="file" 
                                                   x-ref="fileInput"
                                                   @change="handleFileSelect($event)" 
                                                   accept="image/*,.pdf" 
                                                   class="sr-only">
                                        </label>
                                        <p class="pl-1">atau seret ke sini</p>
                                    </div>
                                    <p class="text-[10px] text-slate-400">PNG, JPG, WEBP atau PDF hingga 5MB</p>
                                </div>
                            </template>

                            <template x-if="screenshotPreview">
                                <div class="relative w-full flex flex-col items-center">
                                    <img :src="screenshotPreview" alt="Preview" class="max-h-48 rounded-xl object-contain shadow-md border border-slate-200 dark:border-slate-700">
                                    <button type="button" 
                                            @click="removeScreenshot()"
                                            class="mt-2 text-xs text-rose-600 dark:text-rose-400 font-bold hover:underline flex items-center gap-1 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Hapus Gambar
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Technical Info Info-Box -->
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700 text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span>Metadata teknis peramban & resolusi layar akan disertakan secara otomatis.</span>
                        </div>
                    </div>

                    <!-- Error Alert (If Any) -->
                    <div x-show="errorMessage" style="display: none;" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-xs font-semibold text-rose-600 dark:text-rose-400" x-text="errorMessage"></div>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button type="button"
                                @click="showReportModal = false"
                                onclick="if(window._bugWidget) window._bugWidget.showReportModal = false;"
                                class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="isSubmitting"
                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-600 text-white text-xs font-black uppercase tracking-wider shadow-md shadow-orange-600/20 flex items-center gap-2 disabled:opacity-50 transition-all cursor-pointer">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Laporan'"></span>
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
         x-show="showHistoryModal"
         style="display: none; position: fixed !important; inset: 0 !important; z-index: 999999 !important;"
         class="fixed inset-0 overflow-y-auto"
         role="dialog"
         aria-modal="true">
        
        <!-- Backdrop Overlay -->
        <div x-show="showHistoryModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showHistoryModal = false"
             onclick="if(window._bugWidget) window._bugWidget.showHistoryModal = false;"
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
             style="position: fixed; inset: 0; pointer-events: auto;"></div>

        <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
            <div x-show="showHistoryModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 flex flex-col max-h-[85vh] pointer-events-auto">
                
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
                        <button @click="fetchMyReports()"
                                type="button"
                                title="Segarkan Data"
                                class="w-8 h-8 rounded-lg text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors cursor-pointer">
                            <svg class="w-4 h-4" :class="{ 'animate-spin': isLoadingReports }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                        <button @click="showHistoryModal = false"
                                onclick="if(window._bugWidget) window._bugWidget.showHistoryModal = false;"
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
                    <div x-show="isLoadingReports" class="py-12 text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-500 border-t-transparent"></div>
                        <p class="text-xs font-semibold text-slate-400 mt-2">Memuat riwayat laporan...</p>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!isLoadingReports && myReports.length === 0" class="py-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">Belum Ada Laporan Bug</h4>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Anda belum pernah mengirimkan laporan kendala teknis. Gunakan tombol oranye 📝 untuk membuat laporan baru.
                        </p>
                    </div>

                    <!-- Reports Cards -->
                    <template x-if="!isLoadingReports && myReports.length > 0">
                        <div class="space-y-3.5">
                            <template x-for="item in myReports" :key="item.id">
                                <div class="p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-800/60 shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 transition-all space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-[11px] font-mono font-bold text-orange-600 dark:text-orange-400" x-text="item.ticket_number"></span>
                                                <span class="text-slate-300 dark:text-slate-700">•</span>
                                                <span class="text-[10px] text-slate-400" x-text="item.created_at_human"></span>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-900 dark:text-white" x-text="item.title"></h4>
                                        </div>

                                        <!-- Status Pill -->
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shrink-0"
                                              :class="{
                                                  'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800': item.status === 'open',
                                                  'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800': item.status === 'in_progress',
                                                  'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800': item.status === 'resolved',
                                                  'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700': item.status === 'closed'
                                              }"
                                              x-text="item.status_label">
                                        </span>
                                    </div>

                                    <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2" x-text="item.description"></p>

                                    <!-- Admin Response (If Handled) -->
                                    <template x-if="item.admin_notes">
                                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/80 border-l-4 border-emerald-500 text-xs text-slate-700 dark:text-slate-300 space-y-1">
                                            <div class="flex items-center gap-1.5 font-bold text-slate-800 dark:text-slate-200 text-[11px]">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                                <span>Tanggapan Admin / Kaprodi:</span>
                                                <span class="text-[10px] font-normal text-slate-400" x-show="item.resolver_name" x-text="'(' + item.resolver_name + ')'"></span>
                                            </div>
                                            <p class="text-[11px] leading-relaxed text-slate-600 dark:text-slate-300" x-text="item.admin_notes"></p>
                                        </div>
                                    </template>

                                    <!-- Footer Meta -->
                                    <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800">
                                        <span x-text="'Kategori: ' + item.category_label"></span>
                                        <template x-if="item.attachment_url">
                                            <a :href="item.attachment_url" target="_blank" class="text-orange-600 dark:text-orange-400 font-semibold hover:underline flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                                Lihat Lampiran
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="p-4 px-6 sm:px-8 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex justify-end shrink-0">
                    <button type="button" 
                            @click="showHistoryModal = false" 
                            onclick="if(window._bugWidget) window._bugWidget.showHistoryModal = false;"
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
         x-show="showGuideModal"
         style="display: none; position: fixed !important; inset: 0 !important; z-index: 999999 !important;"
         class="fixed inset-0 overflow-y-auto"
         role="dialog"
         aria-modal="true">
        
        <!-- Backdrop Overlay -->
        <div x-show="showGuideModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showGuideModal = false"
             onclick="if(window._bugWidget) window._bugWidget.showGuideModal = false;"
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
             style="position: fixed; inset: 0; pointer-events: auto;"></div>

        <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
            <div x-show="showGuideModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 pointer-events-auto">
                
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

                    <button @click="showGuideModal = false"
                            onclick="if(window._bugWidget) window._bugWidget.showGuideModal = false;"
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
                            @click="showGuideModal = false"
                            onclick="if(window._bugWidget) window._bugWidget.showGuideModal = false;"
                            class="px-5 py-2 bg-orange-600 text-white text-xs font-bold rounded-xl uppercase tracking-wider hover:bg-orange-700 transition-colors shadow-xs cursor-pointer">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

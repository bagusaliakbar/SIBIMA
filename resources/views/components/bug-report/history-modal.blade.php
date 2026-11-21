<!-- ========================================================================= -->
<!-- MODAL 2: STATUS & RIWAYAT LAPORAN SAYA                                     -->
<!-- ========================================================================= -->
<div id="bug-history-modal"
     class="bug-modal-shell"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop Overlay -->
    <div onclick="window.closeBugHistoryModal()"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity pointer-events-auto cursor-pointer"></div>

    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
        <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 flex flex-col max-h-[85vh] pointer-events-auto">

            <!-- Header -->
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40 shrink-0">
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
                            aria-label="Segarkan riwayat laporan"
                            title="Segarkan Data"
                            class="w-8 h-8 rounded-lg text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <button onclick="window.closeBugHistoryModal()"
                            type="button"
                            aria-label="Tutup modal riwayat laporan"
                            class="w-8 h-8 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- List Content -->
            <div class="p-6 sm:p-8 overflow-y-auto space-y-4 custom-scrollbar flex-1">
                <!-- Loading State -->
                <div id="bug-history-loading" class="hidden py-12 text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-500 border-t-transparent"></div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-2">Memuat riwayat laporan...</p>
                </div>

                <!-- Empty State -->
                <div id="bug-history-empty" class="hidden py-12 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">Belum Ada Laporan Bug</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                        Anda belum pernah mengirimkan laporan kendala teknis. Gunakan tombol Buat Laporan Baru di bawah untuk membuat laporan.
                    </p>
                </div>

                <!-- Container for Report Cards -->
                <div id="bug-history-list" class="space-y-3.5"></div>
            </div>

            <!-- Footer -->
            <div class="p-4 px-6 sm:px-8 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                <button type="button"
                        onclick="window.closeBugHistoryModal(); window.openBugReportModal();"
                        class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>Buat Laporan Baru</span>
                </button>
                <button type="button"
                        onclick="window.closeBugHistoryModal()"
                        class="px-5 py-2 bg-slate-800 dark:bg-white text-white dark:text-slate-800 text-xs font-bold rounded-xl uppercase tracking-wider hover:bg-slate-900 dark:hover:bg-slate-100 transition-all shadow-xs cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

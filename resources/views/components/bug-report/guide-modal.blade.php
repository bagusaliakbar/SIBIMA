<!-- ========================================================================= -->
<!-- MODAL 3: PANDUAN PELAPORAN BUG                                             -->
<!-- ========================================================================= -->
<div id="bug-guide-modal"
     class="bug-modal-shell"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop Overlay -->
    <div onclick="window.closeBugGuideModal()"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity pointer-events-auto cursor-pointer"></div>

    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
        <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 pointer-events-auto">

            <!-- Header -->
            <div class="px-6 pt-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">
                            Panduan Kendala & Usulan Fitur
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Tips agar laporan bug atau usulan fitur dapat diproses optimal.
                        </p>
                    </div>
                </div>

                <button onclick="window.closeBugGuideModal()"
                        type="button"
                        aria-label="Tutup modal panduan pelaporan"
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
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Gunakan judul yang jelas, misalnya: <em>"Gagal klik tombol Validasi di menu Pendaftaran Seminar"</em>.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 font-black flex items-center justify-center shrink-0 text-[11px]">2</span>
                    <div>
                        <h5 class="font-bold text-slate-800 dark:text-slate-100 mb-0.5">Lampirkan Screenshot / Mockup</h5>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Screenshot sangat membantu tim melihat kendala teknis atau contoh layout fitur yang diusulkan.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 font-black flex items-center justify-center shrink-0 text-[11px]">3</span>
                    <div>
                        <h5 class="font-bold text-slate-800 dark:text-slate-100 mb-0.5">Tulis Langkah Kejadian atau Alur Ide</h5>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Tuliskan tombol apa yang diklik sebelum error muncul, atau bagaimana alur kerja fitur yang diharapkan.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-black flex items-center justify-center shrink-0 text-[11px]">4</span>
                    <div>
                        <h5 class="font-bold text-slate-800 dark:text-slate-100 mb-0.5">Ingin Mengusulkan Fitur Baru?</h5>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Pilih tab <strong>"Usulan Fitur Baru"</strong> pada formulir pelaporan untuk ide otomatisasi, penambahan menu, atau integrasi baru.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-black flex items-center justify-center shrink-0 text-[11px]">5</span>
                    <div>
                        <h5 class="font-bold text-slate-800 dark:text-slate-100 mb-0.5">Pantau di Menu Status Laporan (Tombol Hijau)</h5>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Klik tombol centang hijau pada kapsul untuk melihat respons admin dan status pengerjaan tiket Anda.</p>
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

<!-- ========================================================================= -->
<!-- MODAL 1: FORMULIR PELAPORAN BUG                                            -->
<!-- ========================================================================= -->
<div id="bug-report-modal"
     class="bug-modal-shell"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop Overlay -->
    <div onclick="window.closeBugReportModal()"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity pointer-events-auto cursor-pointer"></div>

    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
        <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 pointer-events-auto">

            <!-- Modal Header -->
            <div class="px-6 sm:px-8 pt-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between bg-slate-50/70 dark:bg-slate-800/40">
                <div class="flex items-center gap-3.5">
                    <div id="bug-report-header-icon-box" class="w-12 h-12 rounded-2xl bg-orange-500/10 dark:bg-orange-500/20 border border-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="bug-report-header-title" class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
                            Lapor Kendala / Bug Sistem
                        </h3>
                        <p id="bug-report-header-subtitle" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Laporan Anda akan otomatis diteruskan kepada Tim Teknis SIBIMA.
                        </p>
                    </div>
                </div>

                <button onclick="window.closeBugReportModal()"
                        type="button"
                        aria-label="Tutup modal laporan bug"
                        class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Dual Mode Segmented Switcher: Bug vs Usulan Fitur -->
            <div class="px-6 sm:px-8 pt-3.5 pb-2 bg-slate-50/80 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800">
                <div class="flex p-1 bg-slate-200/70 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 gap-1.5">
                    <button type="button"
                            id="tab-btn-bug"
                            onclick="window.switchBugReportType('bug')"
                            class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs cursor-pointer">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span>Lapor Kendala / Bug</span>
                    </button>
                    <button type="button"
                            id="tab-btn-feature"
                            onclick="window.switchBugReportType('feature')"
                            class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                        <span>Usulan Fitur Baru</span>
                    </button>
                </div>
            </div>

            <!-- Secondary Navigation Bar: Quick Link to Check Status -->
            <div class="px-6 sm:px-8 py-2.5 bg-slate-50 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-500 dark:text-slate-400">Pernah mengirim laporan sebelumnya?</span>
                <button type="button"
                        onclick="window.closeBugReportModal(); window.openBugHistoryModal();"
                        class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Cek Status Laporan Saya &rarr;</span>
                </button>
            </div>

            <!-- Modal Body / Form -->
            <form id="bug-report-form" onsubmit="window.handleBugFormSubmit(event)" class="p-6 sm:p-8 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                @csrf
                <input type="hidden" name="report_type" id="bug-report-type-input" value="bug">

                <!-- Judul Laporan -->
                <div>
                    <label id="bug-report-title-label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Judul Kendala <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           id="bug-report-title-input"
                           required
                           maxlength="255"
                           placeholder="Contoh: Tombol simpan revisi tidak merespons, atau layout berantakan"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>

                <!-- Grid: Kategori & Tingkat Keparahan / Urgensi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label id="bug-report-category-label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Kategori Masalah <span class="text-rose-500">*</span>
                        </label>
                        <select name="category"
                                id="bug-report-category-select"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                            <option value="functionality" selected>Fitur / Fungsi Eror (Default)</option>
                            <option value="ui_ux">Tampilan / Layout / UI Rusak</option>
                            <option value="performance">Kinerja / Sangat Lambat / Hang</option>
                            <option value="data_error">Ketidaksesuaian Data / Nilai</option>
                            <option value="security">Izin Akses / Akun / Keamanan</option>
                            <option value="other">Masalah Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label id="bug-report-severity-label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Tingkat Keparahan <span class="text-rose-500">*</span>
                        </label>
                        <select name="severity"
                                id="bug-report-severity-select"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                            <option value="low">Rendah (Minor / Masalah Estetika)</option>
                            <option value="medium" selected>Sedang (Fitur Lambat / Agak Terganggu)</option>
                            <option value="high">Tinggi (Fitur Utama Gagal / Eror 500)</option>
                            <option value="critical">Kritis (Fatal / Seluruh Sistem Terkunci)</option>
                        </select>
                    </div>
                </div>

                <!-- Halaman Terkait (URL) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label id="bug-report-url-label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Halaman Lokasi Bug (URL)
                        </label>
                        <button type="button"
                                onclick="const inp = document.getElementById('bug-report-url-input') || document.querySelector('input[name=\'page_url\']'); if (inp) inp.value = window.location.href;"
                                class="text-[11px] font-semibold text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                            Ambil URL Sekarang
                        </button>
                    </div>
                    <input type="text"
                           name="page_url"
                           id="bug-report-url-input"
                           placeholder="https://..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-xs font-mono focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>

                <!-- Deskripsi / Uraian -->
                <div>
                    <label id="bug-report-desc-label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Uraian Masalah / Deskripsi <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description"
                              id="bug-report-desc-input"
                              required
                              rows="3"
                              placeholder="Jelaskan apa yang Anda lakukan, apa pesan eror yang muncul, dan apa hasil yang seharusnya terjadi..."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"></textarea>
                </div>

                <!-- Langkah Reproduksi / Alur Fitur (Opsional) -->
                <div>
                    <label id="bug-report-steps-label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Langkah-langkah Memicu Masalah (Opsional)
                    </label>
                    <textarea name="steps_to_reproduce"
                              id="bug-report-steps-input"
                              rows="2"
                              placeholder="1. Masuk ke menu X&#10;2. Pilih tanggal Y&#10;3. Klik tombol Simpan..."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"></textarea>
                </div>

                <!-- Upload Bukti Tangkapan Layar (Screenshot) / Mockup -->
                <div>
                    <label id="bug-report-attachment-label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
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
                <div id="bug-form-error" class="hidden p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-xs font-semibold text-rose-600 dark:text-rose-400"></div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button"
                            onclick="window.closeBugReportModal()"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white text-xs font-black uppercase tracking-wider shadow-sm flex items-center gap-2 transition-colors cursor-pointer">
                        <span id="bug-report-submit-btn-text">Kirim Laporan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

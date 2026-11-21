<!-- Modal Sync Portal -->
<div id="syncModal" role="dialog" aria-modal="true" class="fixed inset-0 z-[60] hidden bg-slate-900/80 backdrop-blur-md items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-md shadow-2xl border border-slate-100 dark:border-slate-700/60 overflow-hidden transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black shrink-0">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-white uppercase tracking-tight">Migrasi Data Portal</h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Sinkronisasi Pustaka FASILKOM</p>
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
                    <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">Data Baru</p>
                    <p id="statNewCount" class="text-base font-black text-emerald-600 dark:text-emerald-400">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">Duplikat</p>
                    <p id="statDupCount" class="text-base font-black text-amber-600 dark:text-amber-400">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">Total</p>
                    <p id="statTotalCount" class="text-base font-black text-indigo-600 dark:text-indigo-400">0</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" id="closeSyncModalBtn" onclick="closeSyncModal()" class="hidden bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider hover:bg-orange-600 dark:hover:bg-orange-500 dark:hover:text-white transition-all shadow-sm cursor-pointer">Tutup & Muat Ulang</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sync Repositori UNSUB (Khusus FASILKOM & File BAB 1) -->
<div id="unsubSyncModal" role="dialog" aria-modal="true" class="fixed inset-0 z-[60] hidden bg-slate-900/80 backdrop-blur-md items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-lg shadow-2xl border border-slate-100 dark:border-slate-700/60 overflow-hidden transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-purple-100 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center font-black shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-white uppercase tracking-tight">Sync Repositori UNSUB</h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Sinkronisasi Pustaka & Naskah BAB I & II</p>
                </div>
            </div>

            <!-- Policy & Constraints Badges -->
            <div class="flex items-center gap-2 mb-4 flex-wrap">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Khusus FASILKOM (144 Dokumen)</span>
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black uppercase tracking-wider bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    <svg class="w-3 h-3 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>BAB 1 s/d BAB 6 (On-Demand Ready)</span>
                </span>
            </div>

            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                Sistem akan memindai repositori Universitas Subang, memfilter secara ketat dokumen Fakultas Ilmu Komputer, memperkaya data skripsi dengan teks abstrak lengkap, dan menghubungkan seluruh file naskah BAB 1 s/d BAB 6.
            </p>

            <!-- Download PDF option toggle -->
            <div id="unsubOptionsContainer" class="p-3.5 mb-4 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-700/80 space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80">
                        <svg class="w-3 h-3 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Rekomendasi: On-Demand Streaming (Hemat Penyimpanan VPS)</span>
                    </span>
                </div>
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" id="unsubDownloadPdf" class="mt-0.5 rounded text-purple-600 focus:ring-purple-500 border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <div>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">Unduh Fisik Seluruh File PDF ke Server VPS (Opsional)</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5 leading-relaxed">
                            Secara default <strong>tidak dicentang</strong>. Tanpa unduh fisik, berkas di-stream on-demand saat dibuka (hemat disk VPS & sinkronisasi selesai seketika). Centang hanya bila ingin menyimpan fisik permanen di VPS.
                        </span>
                    </div>
                </label>
            </div>

            <!-- Progress Bar Track Container -->
            <div class="relative w-full bg-slate-100 dark:bg-slate-700/60 rounded-full h-4 mb-2 overflow-hidden border border-slate-200/80 dark:border-slate-600 shadow-inner">
                <div id="unsubSyncProgress" class="bg-orange-600 dark:bg-orange-500 h-full rounded-full transition-all duration-300 shadow-sm" style="width: 0%;"></div>
            </div>
            
            <div class="flex justify-between items-center text-xs font-bold text-slate-600 dark:text-slate-300 mb-5">
                <span id="unsubSyncStatus" class="truncate">Siap untuk sinkronisasi...</span>
                <span id="unsubSyncPercentage" class="shrink-0 ml-2 font-black text-purple-600 dark:text-purple-400">0% (0 / 144)</span>
            </div>

            <!-- Live Statistics Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">Fasilkom</p>
                    <p id="statUnsubTotal" class="text-sm font-black text-slate-700 dark:text-slate-200">144</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">Data Baru</p>
                    <p id="statUnsubNew" class="text-sm font-black text-emerald-600 dark:text-emerald-400">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">Diperkaya</p>
                    <p id="statUnsubEnriched" class="text-sm font-black text-indigo-600 dark:text-indigo-400">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">BAB 1 PDF</p>
                    <p id="statUnsubBab1" class="text-sm font-black text-purple-600 dark:text-purple-400">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">BAB 2 PDF</p>
                    <p id="statUnsubBab2" class="text-sm font-black text-amber-600 dark:text-amber-400">0</p>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-end gap-3">
                <button type="button" 
                        id="cancelUnsubBtn" 
                        onclick="closeUnsubSyncModal(false)" 
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-bold text-xs bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600 shadow-2xs hover:shadow-sm transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Batal</span>
                </button>
                <button type="button" 
                        id="startUnsubBtn" 
                        onclick="startUnsubSync()" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-white bg-purple-700 hover:bg-purple-800 border border-purple-800 active:scale-95 shadow-md shadow-purple-700/30 hover:shadow-lg transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Mulai Sinkronisasi</span>
                </button>
                <button type="button" 
                        id="closeUnsubModalBtn" 
                        onclick="closeUnsubSyncModal(true)" 
                        style="display: none;"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-white bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 shadow-md transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-400 dark:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
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

    function startSync() {
        if (isSyncing) return;

        window.sibimaConfirm({
            title: 'Mulai Migrasi Portal FASILKOM',
            message: 'Anda yakin ingin memulai migrasi 41 halaman dari portal FASILKOM? Proses ini mungkin memakan waktu beberapa menit.',
            type: 'warning',
            confirmText: 'Ya, Mulai Migrasi',
            onConfirm: async () => {
                if (isSyncing) return;
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
        });
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

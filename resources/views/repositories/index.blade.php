<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <x-breadcrumb :items="[
                ['label' => 'Katalog Pustaka Skripsi', 'route' => null]
            ]" />
            @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
            <div class="flex flex-wrap gap-2">
                <button onclick="startSync()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all hover:scale-[1.02] shadow-sm shadow-blue-500/30">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Migrasi Portal
                </button>
                <a href="{{ route('repositories.import.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all hover:scale-[1.02] shadow-sm shadow-emerald-500/30">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import Arsip
                </a>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="w-full">
        <!-- Filter and Search -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
            <form action="{{ route('repositories.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Cari Kata Kunci</label>
                    <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Judul, Abstrak, Nama Mahasiswa, atau Pembimbing..." class="w-full rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 py-2 px-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="w-full md:w-48">
                    <label for="year" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Angkatan</label>
                    <select name="year" id="year" class="w-full rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 py-2 px-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua Angkatan</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-sm">
                        Cari Arsip
                    </button>
                </div>
            </form>
        </div>

        <!-- Repository List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($repositories as $repo)
                <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl overflow-hidden hover:shadow-lg transition-all hover:scale-[1.01] flex flex-col">
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-3">
                            <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 text-[9px] font-black uppercase tracking-widest rounded">
                                Angkatan {{ $repo->year }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $repo->identifier ?? '-' }}</span>
                        </div>
                        
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-2 leading-relaxed">
                            {{ $repo->title }}
                        </h3>
                        
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-4 flex-1 line-clamp-3">
                            {{ $repo->abstract ?? 'Tidak ada abstrak yang tersedia.' }}
                        </p>
                        
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700 mt-auto">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                    {{ substr($repo->name, 0, 1) }}
                                </div>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $repo->name }}</span>
                            </div>
                            
                            @if($repo->pembimbing1 || $repo->pembimbing2)
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 space-y-1">
                                @if($repo->pembimbing1)
                                    <div><span class="font-semibold">Pembimbing 1:</span> {{ $repo->pembimbing1 }}</div>
                                @endif
                                @if($repo->pembimbing2)
                                    <div><span class="font-semibold">Pembimbing 2:</span> {{ $repo->pembimbing2 }}</div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white dark:bg-slate-800 rounded-xl border border-dashed border-slate-300 dark:border-slate-600">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-slate-900 dark:text-slate-100">Belum Ada Pustaka</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Tidak ada arsip skripsi yang ditemukan atau belum ada data yang diimpor.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $repositories->links() }}
        </div>
    </div>
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
</script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Sistem & Konfigurasi', 'route' => null],
                    ['label' => 'Backup & Restore Database', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight mt-1">
                    Backup & Restore Database
                </h2>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">
                    Kelola pembuatan cadangan basis data SIBIMA, unduh arsip SQL, dan lakukan pemulihan sistem secara aman.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 pb-12" x-data="{
        restoreTarget: '',
        restoreTargetFormatted: '',
        deleteTarget: '',
        deleteTargetFormatted: '',
        isCreating: false,
        isRestoring: false
    }">
        <!-- Session Flash Messages -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 flex items-start gap-3 shadow-xs">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs font-bold text-emerald-900 dark:text-emerald-200">Operasi Berhasil</h4>
                    <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800/60 flex items-start gap-3 shadow-xs">
                <div class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs font-bold text-rose-900 dark:text-rose-200">Terjadi Kendala</h4>
                    <p class="text-xs text-rose-700 dark:text-rose-300 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Stat Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- Total Backup -->
            <x-stat-card 
                title="Total File Backup" 
                value="{{ $stats['total_backups'] }} File" 
                color="orange">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                    </svg>
                </x-slot>
                <x-slot name="subtitle">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Tersimpan di storage lokal</p>
                </x-slot>
            </x-stat-card>

            <!-- Total Storage -->
            <x-stat-card 
                title="Total Ukuran Arsip" 
                value="{{ $stats['total_size_formatted'] }}" 
                color="indigo">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </x-slot>
                <x-slot name="subtitle">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Kapasitas media penyimpanan</p>
                </x-slot>
            </x-stat-card>

            <!-- Latest Backup -->
            <x-stat-card 
                title="Cadangan Terakhir" 
                value="{{ $stats['latest_backup'] ? $stats['latest_backup']['created_at_human'] : 'Belum Ada' }}" 
                color="emerald">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </x-slot>
                <x-slot name="subtitle">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate">
                        {{ $stats['latest_backup'] ? $stats['latest_backup']['created_at_formatted'] : 'Belum ada arsip dibuat' }}
                    </p>
                </x-slot>
            </x-stat-card>

            <!-- Database Info -->
            <x-stat-card 
                title="Basis Data Aktif" 
                value="{{ $stats['database_name'] }}" 
                color="blue">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </x-slot>
                <x-slot name="subtitle">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                        {{ $stats['tables_count'] }} Tabel ({{ $stats['database_size_formatted'] }}) • {{ $stats['database_driver'] }}
                    </p>
                </x-slot>
            </x-stat-card>
        </div>

        <!-- Security Best Practices Banner -->
        <div class="rounded-2xl bg-gradient-to-r from-amber-500/10 via-orange-500/5 to-transparent border border-amber-500/20 p-5 dark:border-amber-500/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Panduan Keamanan Data</h4>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5 leading-relaxed">
                        Lakukan backup secara berkala sebelum pembaruan sistem atau periode ujian besar. Simpan salinan file SQL unduhan ke media penyimpanan cloud/eksternal yang aman.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 self-end md:self-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300/40">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Akses Terbatas: Kaprodi & Admin</span>
                </span>
            </div>
        </div>

        <!-- Main Card: Backups Table -->
        <x-table-card 
            title="Daftar Berkas Cadangan Basis Data" 
            subtitle="Arsip data SQL tersimpan dalam direktori privat server SIBIMA.">
            
            <x-slot name="headerActions">
                <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                    <!-- Upload External Backup Button -->
                    <button 
                        type="button" 
                        @click="$dispatch('open-modal', 'upload-backup-modal')"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-xs font-bold text-slate-700 dark:text-slate-200 transition-all shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span>Unggah File SQL</span>
                    </button>

                    <!-- Create Backup Button -->
                    <button 
                        type="button" 
                        @click="$dispatch('open-modal', 'create-backup-modal')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white text-xs font-bold transition-all shadow-sm shadow-orange-600/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Buat Backup Sekarang</span>
                    </button>
                </div>
            </x-slot>

            @if(count($backups) > 0)
                <table class="w-full text-sm text-left">
                    <thead class="text-[10px] text-slate-500 dark:text-slate-400 uppercase bg-slate-50/80 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-700 font-black tracking-wider">
                        <tr>
                            <th scope="col" class="py-4 px-6">Nama Berkas Cadangan</th>
                            <th scope="col" class="py-4 px-6">Waktu Pembuatan</th>
                            <th scope="col" class="py-4 px-6 text-center">Ukuran File</th>
                            <th scope="col" class="py-4 px-6 text-center">Format</th>
                            <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                        @foreach($backups as $backup)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/40 transition-colors group">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 border border-orange-200/50 dark:border-orange-800/40">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-800 dark:text-slate-100 text-xs truncate max-w-xs sm:max-w-md" title="{{ $backup['filename'] }}">
                                                {{ $backup['filename'] }}
                                            </p>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">
                                                SIBIMA Database Dump
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                        {{ $backup['created_at_formatted'] }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                        {{ $backup['created_at_human'] }}
                                    </p>
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                        {{ $backup['size_formatted'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-orange-100/70 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300 border border-orange-200/50">
                                        {{ $backup['is_compressed'] ? 'SQL GZ' : 'SQL DUMP' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Download -->
                                        <a href="{{ route('admin.backups.download', $backup['filename']) }}" 
                                           title="Unduh Berkas SQL"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>

                                        <!-- Restore -->
                                        <button 
                                            type="button"
                                            @click="restoreTarget = '{{ $backup['filename'] }}'; restoreTargetFormatted = '{{ $backup['filename'] }} ({{ $backup['size_formatted'] }})'; $dispatch('open-modal', 'confirm-restore-modal')"
                                            title="Pulihkan Database dari File Ini"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/60 text-amber-700 dark:text-amber-300 transition-colors border border-amber-200/50 dark:border-amber-800/40">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>

                                        <!-- Delete -->
                                        <button 
                                            type="button"
                                            @click="deleteTarget = '{{ $backup['filename'] }}'; deleteTargetFormatted = '{{ $backup['filename'] }}'; $dispatch('open-modal', 'confirm-delete-modal')"
                                            title="Hapus Berkas Backup"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 transition-colors border border-rose-200/50 dark:border-rose-800/40">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-16 px-4 text-center w-full">
                    <x-empty-state 
                        title="Belum Ada Berkas Cadangan" 
                        description="Belum ada arsip database tersimpan di direktori server. Buat backup pertama Anda sekarang." 
                        icon="folder-open" />
                </div>
            @endif
        </x-table-card>

        <!-- ========================================== -->
        <!-- MODAL: BUAT BACKUP DATABASE               -->
        <!-- ========================================== -->
        <x-modal name="create-backup-modal" maxWidth="md">
            <form action="{{ route('admin.backups.create') }}" method="POST" @submit="isCreating = true" class="p-6">
                @csrf
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Buat Cadangan Database</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Ekspor seluruh skema dan record database SIBIMA.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Label / Catatan Khusus (Opsional)
                        </label>
                        <input 
                            type="text" 
                            name="custom_name" 
                            placeholder="Contoh: pra_sidang_gelombang_1" 
                            pattern="^[a-zA-Z0-9_\-]+$"
                            maxlength="50"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 py-2.5 px-3">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">
                            Hanya boleh huruf, angka, strip (-), atau garis bawah (_). Jika dikosongkan, nama akan menggunakan tanggal & waktu otomatis.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-700/60 space-y-1.5 text-xs text-slate-600 dark:text-slate-400">
                        <div class="flex items-center justify-between font-semibold">
                            <span>Database Sumber:</span>
                            <span class="text-slate-800 dark:text-slate-200 font-mono">{{ $stats['database_name'] }}</span>
                        </div>
                        <div class="flex items-center justify-between font-semibold">
                            <span>Jumlah Tabel:</span>
                            <span class="text-slate-800 dark:text-slate-200">{{ $stats['tables_count'] }} tabel</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button 
                        type="button" 
                        @click="$dispatch('close-modal', 'create-backup-modal')"
                        :disabled="isCreating"
                        class="px-4 py-2 text-xs font-bold rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="isCreating"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white text-xs font-bold transition-all shadow-sm shadow-orange-600/30 disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="isCreating">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="isCreating ? 'Mengekspor Database...' : 'Mulai Backup'"></span>
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- ========================================== -->
        <!-- MODAL: UNGGAH FILE CADANGAN (RESTORE)     -->
        <!-- ========================================== -->
        <x-modal name="upload-backup-modal" maxWidth="md">
            <form action="{{ route('admin.backups.upload-restore') }}" method="POST" enctype="multipart/form-data" @submit="isRestoring = true" class="p-6">
                @csrf
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Unggah & Pulihkan Database</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pulihkan sistem menggunakan berkas SQL eksternal.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="p-3.5 rounded-xl bg-rose-50/80 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/60 text-xs text-rose-800 dark:text-rose-300 space-y-1">
                        <p class="font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Peringatan Penting!
                        </p>
                        <p class="text-[11px] leading-relaxed">
                            File yang Anda unggah akan <strong>langsung dieksekusi</strong> untuk menimpa tabel basis data saat ini. Pastikan Anda telah membuat cadangan terbaru sebelum melakukan tindakan ini.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Pilih Berkas SQL (.sql atau .sql.gz)
                        </label>
                        <input 
                            type="file" 
                            name="backup_file" 
                            accept=".sql,.gz"
                            required
                            class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-slate-800 dark:text-slate-200 p-2">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Maksimal ukuran file: 100 MB.</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button 
                        type="button" 
                        @click="$dispatch('close-modal', 'upload-backup-modal')"
                        :disabled="isRestoring"
                        class="px-4 py-2 text-xs font-bold rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="isRestoring"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-bold transition-all shadow-sm shadow-rose-600/30 disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="isRestoring">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="isRestoring ? 'Memulihkan Database...' : 'Unggah & Restore Sekarang'"></span>
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- ========================================== -->
        <!-- MODAL: KONFIRMASI RESTORE BERKAS          -->
        <!-- ========================================== -->
        <x-modal name="confirm-restore-modal" maxWidth="md">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Konfirmasi Pemulihan Data</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Proses ini akan menimpa basis data yang ada.</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Anda akan memulihkan data sistem menggunakan berkas cadangan:
                    </p>
                    <div class="p-3 bg-slate-100 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                        <p class="font-mono text-xs font-bold text-slate-800 dark:text-slate-200 break-all" x-text="restoreTargetFormatted"></p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-[11px] text-amber-800 dark:text-amber-300 leading-relaxed">
                        Seluruh data yang dibuat setelah tanggal file backup ini akan digantikan dengan isi backup. Pastikan tidak ada aktivitas bimbingan atau ujian yang sedang berlangsung.
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button 
                        type="button" 
                        @click="$dispatch('close-modal', 'confirm-restore-modal')"
                        :disabled="isRestoring"
                        class="px-4 py-2 text-xs font-bold rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    
                    <form :action="'{{ url('/admin/backups') }}/' + encodeURIComponent(restoreTarget) + '/restore'" method="POST" @submit="isRestoring = true">
                        @csrf
                        <button 
                            type="submit" 
                            :disabled="isRestoring"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white text-xs font-bold transition-all shadow-sm shadow-amber-600/30 disabled:opacity-50 disabled:cursor-not-allowed">
                            <template x-if="isRestoring">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isRestoring ? 'Memproses Restorasi...' : 'Ya, Pulihkan Database'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>

        <!-- ========================================== -->
        <!-- MODAL: KONFIRMASI HAPUS BERKAS            -->
        <!-- ========================================== -->
        <x-modal name="confirm-delete-modal" maxWidth="sm">
            <div class="p-6">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <div class="text-center space-y-2">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Hapus File Backup?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        File cadangan ini akan dihapus permanen dari server. Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <p class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300 break-all p-2 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800" x-text="deleteTargetFormatted"></p>
                </div>

                <div class="mt-6 flex items-center justify-center gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button 
                        type="button" 
                        @click="$dispatch('close-modal', 'confirm-delete-modal')"
                        class="px-4 py-2 text-xs font-bold rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    
                    <form :action="'{{ url('/admin/backups') }}/' + encodeURIComponent(deleteTarget)" method="POST">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-bold transition-all shadow-sm shadow-rose-600/30">
                            Ya, Hapus File
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    </div>
</x-app-layout>

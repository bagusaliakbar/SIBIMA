<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 tracking-tight">Mode Pemeliharaan Sistem</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola pemberitahuan atau kunci akses sementara untuk halaman Mahasiswa dan Dosen tanpa mengganggu akses Admin &amp; Kaprodi.</p>
            </div>
            <div class="shrink-0">
                @if($config['enabled'])
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>Status: Pemeliharaan Aktif ({{ $config['mode'] === 'lock' ? 'Kunci Akses' : 'Banner' }})</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Status: Sistem Normal</span>
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="{
        enabled: {{ $config['enabled'] ? 'true' : 'false' }},
        mode: {{ json_encode(old('mode', $config['mode'])) }},
        target: {{ json_encode(old('target', $config['target'])) }},
        title: {{ json_encode(old('title', $config['title'])) }},
        message: {{ json_encode(old('message', $config['message'])) }},
        estimate: {{ json_encode(old('estimate', $config['estimate'])) }},
        get targetLabel() {
            if (this.target === 'mahasiswa') return 'Hanya Mahasiswa';
            if (this.target === 'dosen') return 'Hanya Dosen';
            return 'Mahasiswa & Dosen';
        }
    }">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-xs text-rose-800 dark:text-rose-300 space-y-1">
                <p class="font-bold">Periksa kembali input Anda:</p>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left: Configuration Form -->
            <form action="{{ route('admin.maintenance.update') }}" method="POST" class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Master Switch -->
                <div class="p-4 rounded-xl border transition-colors flex items-center justify-between gap-4"
                     :class="enabled ? 'bg-amber-50/70 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/60' : 'bg-slate-50/70 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700/80'">
                    <div class="space-y-0.5">
                        <label for="maintenance_enabled" class="text-sm font-bold text-slate-800 dark:text-slate-100 cursor-pointer">Aktifkan Mode Pemeliharaan</label>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Admin &amp; Kaprodi tetap dapat mengakses seluruh sistem secara normal saat mode ini aktif.
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="hidden" name="enabled" value="0">
                        <input type="checkbox" id="maintenance_enabled" name="enabled" value="1" x-model="enabled" class="sr-only peer">
                        <div class="w-12 h-6 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-500/40 rounded-full peer peer-checked:after:translate-x-6 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                    </label>
                </div>

                <!-- Mode Selector -->
                <div class="space-y-2.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Mode Tampilan Pemeliharaan <span class="text-orange-600">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="p-4 rounded-xl border cursor-pointer transition-all flex items-start gap-3"
                               :class="mode === 'banner' ? 'bg-orange-50/60 dark:bg-orange-950/30 border-orange-500 ring-1 ring-orange-500' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                            <input type="radio" name="mode" value="banner" x-model="mode" class="mt-0.5 text-orange-600 focus:ring-orange-500">
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Banner Pemberitahuan</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Pengguna tetap bisa bekerja sambil melihat peringatan di bagian atas setiap halaman.</p>
                            </div>
                        </label>
                        <label class="p-4 rounded-xl border cursor-pointer transition-all flex items-start gap-3"
                               :class="mode === 'lock' ? 'bg-rose-50/60 dark:bg-rose-950/30 border-rose-500 ring-1 ring-rose-500' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                            <input type="radio" name="mode" value="lock" x-model="mode" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Kunci Akses (Halaman Penuh)</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Pengguna target diarahkan ke halaman pemeliharaan penuh hingga mode dimatikan.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Target Role Selector -->
                <div class="space-y-2">
                    <label for="maintenance_target" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Target Pengguna <span class="text-orange-600">*</span></label>
                    <select id="maintenance_target" name="target" x-model="target"
                            class="block w-full h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 px-3.5 text-xs font-semibold text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                        <option value="both">Mahasiswa &amp; Dosen</option>
                        <option value="mahasiswa">Hanya Mahasiswa</option>
                        <option value="dosen">Hanya Dosen</option>
                    </select>
                </div>

                <!-- Title & Estimate -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="maintenance_title" class="block text-xs font-bold text-slate-700 dark:text-slate-300">Judul Pemberitahuan <span class="text-orange-600">*</span></label>
                        <input type="text" id="maintenance_title" name="title" x-model="title" required maxlength="150"
                               placeholder="Contoh: Pemeliharaan Sistem SIBIMA"
                               class="block w-full h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 px-3.5 text-xs text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                    </div>
                    <div class="space-y-1.5">
                        <label for="maintenance_estimate" class="block text-xs font-bold text-slate-700 dark:text-slate-300">Estimasi Selesai (Opsional)</label>
                        <input type="text" id="maintenance_estimate" name="estimate" x-model="estimate" maxlength="120"
                               placeholder="Contoh: Hari ini pukul 23:00 WIB"
                               class="block w-full h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 px-3.5 text-xs text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                    </div>
                </div>

                <!-- Custom Message -->
                <div class="space-y-1.5">
                    <label for="maintenance_message" class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pesan untuk Pengguna <span class="text-orange-600">*</span></label>
                    <textarea id="maintenance_message" name="message" x-model="message" rows="4" required maxlength="1000"
                              placeholder="Jelaskan alasan pemeliharaan dan himbauan untuk mahasiswa/dosen..."
                              class="block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 p-3.5 text-xs text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 leading-relaxed"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="submit" class="h-10 px-5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-sm transition-colors inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan Pengaturan Pemeliharaan</span>
                    </button>
                </div>
            </form>

            <!-- Right: Live Preview Card -->
            <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">Pratinjau Tampilan Pengguna</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Target: <strong x-text="targetLabel"></strong></p>
                    </div>
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold border"
                          :class="enabled ? 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60' : 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                          x-text="enabled ? 'Aktif' : 'Nonaktif (Draft)'"></span>
                </div>

                <!-- Preview: Banner Mode -->
                <div x-show="mode === 'banner'" class="space-y-3">
                    <div class="rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-800 p-3.5 text-xs text-amber-950 dark:text-amber-100 space-y-1.5">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div class="min-w-0 flex-1 space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold" x-text="title || 'Pemeliharaan Sistem SIBIMA'"></span>
                                    <template x-if="estimate && estimate.trim()">
                                        <span class="px-2 py-0.5 rounded bg-amber-200/80 dark:bg-amber-900/80 text-amber-900 dark:text-amber-200 text-[11px] font-bold" x-text="'Estimasi: ' + estimate"></span>
                                    </template>
                                </div>
                                <p class="text-[11px] text-amber-900/90 dark:text-amber-200/90 leading-relaxed" x-text="message || '-'"></p>
                            </div>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Banner ini akan muncul di bagian paling atas seluruh halaman <span class="font-semibold" x-text="targetLabel"></span>.
                    </p>
                </div>

                <!-- Preview: Lock Screen Mode -->
                <div x-show="mode === 'lock'" x-cloak class="space-y-3">
                    <div class="rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-6 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-orange-600/10 text-orange-600 dark:text-orange-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60">
                            Pemeliharaan Sistem
                        </span>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100" x-text="title || 'Pemeliharaan Sistem SIBIMA'"></h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed" x-text="message || '-'"></p>
                        <template x-if="estimate && estimate.trim()">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[11px] font-semibold text-slate-700 dark:text-slate-200">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span x-text="'Estimasi selesai: ' + estimate"></span>
                            </div>
                        </template>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Seluruh halaman <span class="font-semibold" x-text="targetLabel"></span> akan digantikan tampilan ini (HTTP 503) hingga pemeliharaan dimatikan.
                    </p>
                </div>

                <!-- Security Note -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80 text-[11px] text-slate-600 dark:text-slate-300 space-y-1">
                    <p class="font-bold text-slate-700 dark:text-slate-200">Catatan Keamanan Akses:</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        <li>Akun <strong>Admin</strong> &amp; <strong>Kaprodi</strong> tetap bisa login dan mengelola sistem secara penuh.</li>
                        <li>Halaman login, logout, Pusat Bantuan (FAQ), dan verifikasi QR tetap dapat diakses.</li>
                        <li>Setiap perubahan status pemeliharaan dicatat otomatis di <strong>Log Sistem</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

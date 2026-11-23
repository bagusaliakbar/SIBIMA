<x-app-layout>
    <x-slot name="header">
        <div class="w-full space-y-2">
            <x-breadcrumb :items="[
                ['label' => 'Pemeliharaan Sistem', 'route' => null],
                ['label' => 'Mode Pemeliharaan', 'route' => null],
            ]" />
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 w-full">
                <div>
                    <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 tracking-tight">Mode Pemeliharaan Sistem</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola pemberitahuan, hitung mundur (countdown), atau kunci akses sementara untuk halaman Mahasiswa dan Dosen tanpa mengganggu akses Admin &amp; Kaprodi.</p>
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
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="{
        enabled: {{ $config['enabled'] ? 'true' : 'false' }},
        mode: {{ json_encode(old('mode', $config['mode'])) }},
        target: {{ json_encode(old('target', $config['target'])) }},
        title: {{ json_encode(old('title', $config['title'])) }},
        message: {{ json_encode(old('message', $config['message'])) }},
        estimate: {{ json_encode(old('estimate', $config['estimate'])) }},
        endsAt: {{ json_encode(old('ends_at', $endsAtInput ?? '')) }},
        blockGuest: {{ old('block_guest', $config['block_guest'] ?? false) ? 'true' : 'false' }},
        bypassSecret: {{ json_encode(old('bypass_secret', $config['bypass_secret'] ?? '')) }},
        bypassBaseUrl: {{ json_encode(url('/akses')) }},
        copiedSecret: false,
        cdDays: '00',
        cdHours: '00',
        cdMinutes: '00',
        cdSeconds: '00',
        cdActive: false,
        cdExpired: false,
        get bypassFullUrl() {
            const clean = (this.bypassSecret || '').trim();
            return clean ? (this.bypassBaseUrl + '/' + clean) : this.bypassBaseUrl;
        },
        generateSecret() {
            const chars = 'abcdefghjkmnpqrstuvwxyz23456789';
            const bytes = new Uint8Array(12);
            if (window.crypto && window.crypto.getRandomValues) {
                window.crypto.getRandomValues(bytes);
            } else {
                for (let i = 0; i < bytes.length; i++) bytes[i] = Math.floor(Math.random() * 256);
            }
            let token = 'sibima-';
            for (let i = 0; i < bytes.length; i++) {
                token += chars[bytes[i] % chars.length];
            }
            this.bypassSecret = token;
            this.copiedSecret = false;
        },
        copyBypassUrl() {
            if (!this.bypassFullUrl) return;
            navigator.clipboard.writeText(this.bypassFullUrl).then(() => {
                this.copiedSecret = true;
                setTimeout(() => { this.copiedSecret = false; }, 2500);
            });
        },
        get targetLabel() {
            if (this.target === 'mahasiswa') return 'Hanya Mahasiswa';
            if (this.target === 'dosen') return 'Hanya Dosen';
            return 'Mahasiswa & Dosen';
        },
        get cdCompact() {
            if (!this.cdActive) return '';
            if (this.cdExpired) return 'Selesai';
            const dPrefix = parseInt(this.cdDays, 10) > 0 ? parseInt(this.cdDays, 10) + 'h ' : '';
            return dPrefix + this.cdHours + ':' + this.cdMinutes + ':' + this.cdSeconds;
        },
        setPresetMinutes(mins) {
            const dt = new Date(Date.now() + mins * 60000);
            const pad = n => String(n).padStart(2, '0');
            this.endsAt = dt.getFullYear() + '-' + pad(dt.getMonth() + 1) + '-' + pad(dt.getDate()) + 'T' + pad(dt.getHours()) + ':' + pad(dt.getMinutes());
            this.updateCountdown();
        },
        clearEndsAt() {
            this.endsAt = '';
            this.updateCountdown();
        },
        updateCountdown() {
            if (!this.endsAt) {
                this.cdActive = false;
                this.cdExpired = false;
                return;
            }
            const targetMs = new Date(this.endsAt).getTime();
            if (isNaN(targetMs)) {
                this.cdActive = false;
                return;
            }
            this.cdActive = true;
            const diff = targetMs - Date.now();
            if (diff <= 0) {
                this.cdDays = '00';
                this.cdHours = '00';
                this.cdMinutes = '00';
                this.cdSeconds = '00';
                this.cdExpired = true;
                return;
            }
            this.cdExpired = false;
            const totalSec = Math.floor(diff / 1000);
            const pad = n => String(n).padStart(2, '0');
            this.cdDays = pad(Math.floor(totalSec / 86400));
            this.cdHours = pad(Math.floor((totalSec % 86400) / 3600));
            this.cdMinutes = pad(Math.floor((totalSec % 3600) / 60));
            this.cdSeconds = pad(totalSec % 60);
        },
        init() {
            this.updateCountdown();
            setInterval(() => this.updateCountdown(), 1000);
            this.$watch('endsAt', () => this.updateCountdown());
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
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Pengguna tetap bisa bekerja sambil melihat peringatan &amp; countdown di bagian atas halaman.</p>
                            </div>
                        </label>
                        <label class="p-4 rounded-xl border cursor-pointer transition-all flex items-start gap-3"
                               :class="mode === 'lock' ? 'bg-rose-50/60 dark:bg-rose-950/30 border-rose-500 ring-1 ring-rose-500' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                            <input type="radio" name="mode" value="lock" x-model="mode" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Kunci Akses (Halaman Penuh)</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Pengguna target diarahkan ke halaman pemeliharaan penuh dengan hitung mundur hingga mode dimatikan.</p>
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

                <!-- Pre-Login Lock & Secret Bypass URL (Lock Mode Only) -->
                <div x-show="mode === 'lock'" x-cloak class="p-4 rounded-xl border space-y-4 transition-colors"
                     :class="blockGuest ? 'bg-blue-50/60 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800/70' : 'bg-slate-50/80 dark:bg-slate-800/40 border-slate-200/80 dark:border-slate-700/80'">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-0.5">
                            <label for="maintenance_block_guest" class="text-xs font-bold text-slate-800 dark:text-slate-100 cursor-pointer">Tampilkan Sebelum Login (Kunci Halaman Login &amp; Publik)</label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                Pengunjung yang membuka situs atau halaman <code class="font-mono">/login</code> langsung melihat layar pemeliharaan tanpa tombol login. Admin &amp; Kaprodi masuk melalui <strong>URL Rahasia</strong> di bawah.
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-0.5">
                            <input type="hidden" name="block_guest" value="0">
                            <input type="checkbox" id="maintenance_block_guest" name="block_guest" value="1" x-model="blockGuest" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-500/40 rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>
                    </div>

                    <div class="space-y-3 pt-3 border-t border-slate-200/80 dark:border-slate-700/80">
                        <div class="space-y-1.5">
                            <label for="maintenance_bypass_secret" class="block text-xs font-bold text-slate-700 dark:text-slate-200">Kode Rahasia URL Akses Admin / Kaprodi</label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" id="maintenance_bypass_secret" name="bypass_secret" x-model="bypassSecret" minlength="6" maxlength="64" pattern="[A-Za-z0-9_-]+"
                                       placeholder="sibima-xxxxxxxx"
                                       class="flex-1 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 px-3.5 text-xs font-mono font-semibold text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                                <button type="button" @click="generateSecret()"
                                        class="h-10 px-3.5 rounded-xl bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold inline-flex items-center justify-center gap-1.5 shrink-0 transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <span>Acak Kode</span>
                                </button>
                            </div>
                        </div>

                        <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">URL Rahasia Login Pengelola</p>
                                <p class="text-xs font-mono font-semibold text-blue-700 dark:text-blue-300 truncate mt-0.5" x-text="bypassFullUrl"></p>
                            </div>
                            <button type="button" @click="copyBypassUrl()"
                                    class="h-9 px-3.5 rounded-lg text-xs font-bold inline-flex items-center justify-center gap-1.5 shrink-0 transition-colors cursor-pointer"
                                    :class="copiedSecret ? 'bg-emerald-600 text-white' : 'bg-slate-900 hover:bg-slate-800 dark:bg-orange-600 dark:hover:bg-orange-500 text-white'">
                                <svg x-show="!copiedSecret" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <svg x-show="copiedSecret" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span x-text="copiedSecret ? 'Tersalin!' : 'Salin URL'"></span>
                            </button>
                        </div>

                        <p class="text-[11px] text-amber-800 dark:text-amber-300 leading-relaxed flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span>Simpan/bookmark URL rahasia ini sebelum mengaktifkan kunci halaman login. Simpan perubahan agar kode rahasia baru mulai berlaku.</span>
                        </p>
                    </div>
                </div>

                <!-- Countdown Target DateTime -->
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label for="maintenance_ends_at" class="block text-xs font-bold text-slate-700 dark:text-slate-200">Target Waktu Hitung Mundur / Countdown (Opsional)</label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Menampilkan timer hitung mundur (Hari : Jam : Menit : Detik) secara live di halaman pengguna.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                        <input type="datetime-local" id="maintenance_ends_at" name="ends_at" x-model="endsAt"
                               class="block w-full h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 px-3.5 text-xs font-semibold text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500">

                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" @click="setPresetMinutes(30)" class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200 dark:border-slate-700 hover:border-orange-300 text-[11px] font-bold text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">+30m</button>
                            <button type="button" @click="setPresetMinutes(60)" class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200 dark:border-slate-700 hover:border-orange-300 text-[11px] font-bold text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">+1 Jam</button>
                            <button type="button" @click="setPresetMinutes(120)" class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200 dark:border-slate-700 hover:border-orange-300 text-[11px] font-bold text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">+2 Jam</button>
                            <button type="button" @click="setPresetMinutes(240)" class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200 dark:border-slate-700 hover:border-orange-300 text-[11px] font-bold text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">+4 Jam</button>
                            <button type="button" x-show="endsAt" @click="clearEndsAt()" class="px-2.5 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/50 border border-rose-200 dark:border-rose-800 text-[11px] font-bold text-rose-700 dark:text-rose-300 transition-colors cursor-pointer">Hapus</button>
                        </div>
                    </div>
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
                        <label for="maintenance_estimate" class="block text-xs font-bold text-slate-700 dark:text-slate-300">Catatan Estimasi Tambahan (Opsional)</label>
                        <input type="text" id="maintenance_estimate" name="estimate" x-model="estimate" maxlength="120"
                               placeholder="Contoh: Hari ini pukul 12.00 WIB"
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
                            <div class="min-w-0 flex-1 space-y-1.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold" x-text="title || 'Pemeliharaan Sistem SIBIMA'"></span>
                                    <template x-if="cdCompact">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-orange-600 text-white text-[10px] font-bold tabular-nums" x-text="cdCompact"></span>
                                    </template>
                                    <template x-if="estimate && estimate.trim()">
                                        <span class="px-2 py-0.5 rounded bg-amber-200/80 dark:bg-amber-900/80 text-amber-900 dark:text-amber-200 text-[10px] font-bold" x-text="'Estimasi: ' + estimate"></span>
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
                    <div class="rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
                        <div class="h-1 bg-orange-600"></div>
                        <div class="p-5 text-center space-y-3.5">
                            <div class="w-12 h-12 rounded-2xl border border-dashed border-orange-300 dark:border-orange-700 bg-orange-50/50 dark:bg-orange-950/30 flex items-center justify-center mx-auto">
                                <div class="w-9 h-9 rounded-xl bg-orange-600/10 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>Pemeliharaan Sistem Sedang Berlangsung</span>
                            </span>
                            <div class="space-y-1">
                                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white" x-text="title || 'Pemeliharaan Sistem SIBIMA'"></h3>
                                <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed" x-text="message || '-'"></p>
                            </div>

                            <!-- Live Countdown Preview -->
                            <template x-if="cdActive && !cdExpired">
                                <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-3 space-y-2">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Hitung Mundur Estimasi Selesai</p>
                                    <div class="grid grid-cols-4 gap-1.5">
                                        <div class="rounded-lg bg-slate-50 dark:bg-slate-800 py-1.5">
                                            <div class="text-sm font-extrabold tabular-nums text-slate-900 dark:text-white" x-text="cdDays">00</div>
                                            <div class="text-[9px] font-semibold uppercase text-slate-500">Hari</div>
                                        </div>
                                        <div class="rounded-lg bg-slate-50 dark:bg-slate-800 py-1.5">
                                            <div class="text-sm font-extrabold tabular-nums text-slate-900 dark:text-white" x-text="cdHours">00</div>
                                            <div class="text-[9px] font-semibold uppercase text-slate-500">Jam</div>
                                        </div>
                                        <div class="rounded-lg bg-slate-50 dark:bg-slate-800 py-1.5">
                                            <div class="text-sm font-extrabold tabular-nums text-slate-900 dark:text-white" x-text="cdMinutes">00</div>
                                            <div class="text-[9px] font-semibold uppercase text-slate-500">Menit</div>
                                        </div>
                                        <div class="rounded-lg bg-orange-50 dark:bg-orange-950/40 py-1.5">
                                            <div class="text-sm font-extrabold tabular-nums text-orange-600 dark:text-orange-400" x-text="cdSeconds">00</div>
                                            <div class="text-[9px] font-semibold uppercase text-orange-600/80">Detik</div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!cdActive && estimate && estimate.trim()">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[11px] font-semibold text-slate-700 dark:text-slate-200">
                                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span x-text="'Estimasi selesai: ' + estimate"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Seluruh halaman <span class="font-semibold" x-text="targetLabel"></span> akan digantikan tampilan ini (HTTP 503) hingga pemeliharaan dimatikan.
                    </p>
                </div>

                <!-- Security Note -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80 text-[11px] text-slate-600 dark:text-slate-300 space-y-1">
                    <p class="font-bold text-slate-700 dark:text-slate-200">Catatan Keamanan Akses:</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        <li>Akun <strong>Admin</strong> &amp; <strong>Kaprodi</strong> tetap bisa mengelola sistem secara penuh dan dapat masuk lewat <strong>URL Rahasia</strong> saat halaman login dikunci.</li>
                        <li>Jika Mahasiswa/Dosen mencoba masuk melalui URL Rahasia, sistem tetap menolak akses selama mode pemeliharaan aktif.</li>
                        <li>Verifikasi dokumen QR (<code class="font-mono">/verify/*</code>) dan sinkronisasi server tetap dapat diakses.</li>
                        <li>Setiap perubahan status pemeliharaan dicatat otomatis di <strong>Log Sistem</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

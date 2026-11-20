@auth
@php
    $idleTimeoutMinutes = max(1, (int) config('session.idle_timeout', 60));
    $idleWarningMinutes = max(1, min((int) config('session.idle_warning', 2), $idleTimeoutMinutes - 1));
@endphp

<!-- Session Idle Timeout Countdown Modal (OWASP Session Security) -->
<div x-data="sessionIdleMonitor({
        timeoutSeconds: {{ $idleTimeoutMinutes * 60 }},
        warningSeconds: {{ $idleWarningMinutes * 60 }},
        keepAliveUrl: '{{ route('session.keep-alive') }}',
        logoutUrl: '{{ route('logout') }}',
        loginExpiredUrl: '{{ route('login', ['expired' => 1]) }}'
    })"
     x-init="init()"
     x-show="showWarning"
     x-cloak
     role="dialog"
     aria-modal="true"
     aria-labelledby="session-timeout-title"
     class="fixed inset-0 z-[75] overflow-y-auto">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Dialog -->
    <div class="min-h-screen px-4 py-8 flex items-center justify-center relative">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10">

            <!-- Header -->
            <div class="px-6 pt-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-start gap-3.5 bg-slate-50/70 dark:bg-slate-800/40">
                <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60 mb-1.5">
                        Keamanan Sesi
                    </span>
                    <h3 id="session-timeout-title" class="text-base font-bold text-slate-900 dark:text-slate-100 leading-snug">
                        Sesi Anda Akan Segera Berakhir
                    </h3>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    Tidak ada aktivitas selama mendekati <strong>{{ $idleTimeoutMinutes }} menit</strong>. Demi menjaga keamanan data akun Anda, sistem akan keluar otomatis dalam:
                </p>

                <!-- Countdown Well -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 text-center space-y-2.5">
                    <div class="text-3xl font-black font-mono tabular-nums tracking-tight text-orange-600 dark:text-orange-400" x-text="formattedRemaining">02:00</div>
                    <div class="w-full h-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full bg-orange-600 dark:bg-orange-500 transition-all duration-1000 ease-linear"
                             :style="'width: ' + progressPercent + '%'"></div>
                    </div>
                </div>

                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    Klik <strong>Lanjutkan Sesi</strong> jika Anda masih bekerja di SIBIMA, atau <strong>Keluar Sekarang</strong> untuk menutup sesi dengan aman.
                </p>
            </div>

            <!-- Footer Actions -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5">
                <button type="button"
                        @click="performLogout(false)"
                        :disabled="extending || loggingOut"
                        class="h-10 px-4 rounded-xl text-xs font-bold bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer disabled:opacity-50">
                    Keluar Sekarang
                </button>
                <button type="button"
                        @click="extendSession()"
                        :disabled="extending || loggingOut"
                        class="h-10 px-5 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white shadow-sm inline-flex items-center justify-center gap-2 transition-colors cursor-pointer disabled:opacity-60">
                    <svg x-show="extending" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="extending ? 'Memperbarui...' : 'Lanjutkan Sesi'">Lanjutkan Sesi</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Clean Auto-Logout -->
    <form x-ref="idleLogoutForm" method="POST" :action="logoutUrl" class="hidden">
        @csrf
        <input type="hidden" name="reason" :value="logoutReason">
    </form>
</div>

<script>
    function sessionIdleMonitor(config) {
        const STORAGE_ACTIVITY_KEY = 'sibima_last_activity_at';
        const STORAGE_LOGOUT_KEY = 'sibima_force_logout_at';
        const IDLE_POLLING_THRESHOLD_MS = 5 * 60 * 1000; // Pause background polling after 5 min idle

        return {
            timeoutSeconds: config.timeoutSeconds || 3600,
            warningSeconds: config.warningSeconds || 120,
            keepAliveUrl: config.keepAliveUrl,
            logoutUrl: config.logoutUrl,
            loginExpiredUrl: config.loginExpiredUrl,
            lastActivityMs: Date.now(),
            lastStorageWriteMs: 0,
            remainingSeconds: config.timeoutSeconds || 3600,
            showWarning: false,
            extending: false,
            loggingOut: false,
            logoutReason: 'idle',
            tickerId: null,

            get formattedRemaining() {
                const secs = Math.max(0, Math.floor(this.remainingSeconds));
                const mins = Math.floor(secs / 60);
                const rem = secs % 60;
                return String(mins).padStart(2, '0') + ':' + String(rem).padStart(2, '0');
            },

            get progressPercent() {
                if (this.warningSeconds <= 0) return 0;
                return Math.max(0, Math.min(100, Math.round((this.remainingSeconds / this.warningSeconds) * 100)));
            },

            init() {
                this.recordActivity(true);

                // Expose passive idle check for background notification polling
                window._sibimaIsIdle = () => (Date.now() - this.lastActivityMs) >= IDLE_POLLING_THRESHOLD_MS;

                const onUserActivity = () => {
                    // Once warning modal is open, require explicit "Lanjutkan Sesi" click
                    if (this.showWarning || this.loggingOut) return;
                    this.recordActivity(false);
                };

                ['click', 'keydown', 'scroll', 'touchstart', 'pointerdown'].forEach((evt) => {
                    window.addEventListener(evt, onUserActivity, { passive: true });
                });

                // Sync activity and logout across browser tabs
                window.addEventListener('storage', (e) => {
                    if (e.key === STORAGE_ACTIVITY_KEY && e.newValue) {
                        const synced = parseInt(e.newValue, 10);
                        if (!Number.isNaN(synced) && synced > this.lastActivityMs) {
                            this.lastActivityMs = synced;
                            if (this.showWarning && !this.loggingOut) {
                                this.showWarning = false;
                            }
                        }
                    } else if (e.key === STORAGE_LOGOUT_KEY && e.newValue && !this.loggingOut) {
                        this.loggingOut = true;
                        window.location.href = this.loginExpiredUrl;
                    }
                });

                // Immediately re-evaluate when waking from sleep or switching back to tab
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) {
                        this.checkTimeout();
                    }
                });

                this.tickerId = setInterval(() => this.checkTimeout(), 1000);
            },

            recordActivity(forceWrite) {
                const now = Date.now();
                this.lastActivityMs = now;

                if (forceWrite || (now - this.lastStorageWriteMs) >= 5000) {
                    this.lastStorageWriteMs = now;
                    try {
                        localStorage.setItem(STORAGE_ACTIVITY_KEY, String(now));
                    } catch (e) {}
                }
            },

            checkTimeout() {
                if (this.loggingOut) return;

                try {
                    const stored = parseInt(localStorage.getItem(STORAGE_ACTIVITY_KEY) || '0', 10);
                    if (!Number.isNaN(stored) && stored > this.lastActivityMs) {
                        this.lastActivityMs = stored;
                    }
                } catch (e) {}

                const elapsedSeconds = (Date.now() - this.lastActivityMs) / 1000;
                this.remainingSeconds = Math.max(0, Math.ceil(this.timeoutSeconds - elapsedSeconds));

                if (this.remainingSeconds <= 0) {
                    this.performLogout(true);
                } else if (this.remainingSeconds <= this.warningSeconds) {
                    this.showWarning = true;
                } else if (this.showWarning) {
                    this.showWarning = false;
                }
            },

            syncCsrfToken(token) {
                if (!token) return;
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', token);
                document.querySelectorAll('input[name="_token"]').forEach((input) => {
                    input.value = token;
                });
            },

            async extendSession() {
                if (this.extending || this.loggingOut) return;
                this.extending = true;

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const res = await fetch(this.keepAliveUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    });

                    if (res.status === 401 || res.status === 419) {
                        this.loggingOut = true;
                        window.location.href = this.loginExpiredUrl;
                        return;
                    }

                    const data = await res.json();
                    if (res.ok && data.ok) {
                        this.syncCsrfToken(data.csrf_token);
                        this.recordActivity(true);
                        this.remainingSeconds = this.timeoutSeconds;
                        this.showWarning = false;
                        if (typeof window.sibimaToast === 'function') {
                            window.sibimaToast('Sesi Anda berhasil diperpanjang.', 'success', 'Sesi Diperbarui');
                        }
                    } else {
                        this.loggingOut = true;
                        window.location.href = this.loginExpiredUrl;
                    }
                } catch (err) {
                    if (typeof window.sibimaToast === 'function') {
                        window.sibimaToast('Gagal menghubungi server. Periksa koneksi internet Anda.', 'warning', 'Koneksi Terputus');
                    }
                } finally {
                    this.extending = false;
                }
            },

            performLogout(isIdle) {
                if (this.loggingOut) return;
                this.loggingOut = true;
                this.logoutReason = isIdle ? 'idle' : 'manual';

                if (this.tickerId) {
                    clearInterval(this.tickerId);
                }

                try {
                    localStorage.setItem(STORAGE_LOGOUT_KEY, String(Date.now()));
                } catch (e) {}

                this.$nextTick(() => {
                    if (this.$refs.idleLogoutForm) {
                        this.$refs.idleLogoutForm.submit();
                    } else {
                        window.location.href = this.loginExpiredUrl;
                    }
                });
            }
        };
    }
</script>
@endauth

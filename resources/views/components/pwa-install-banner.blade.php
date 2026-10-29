<div x-data="pwaInstallManager()" 
     x-init="init()" 
     x-show="showBanner" 
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave-end="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
     class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:w-96 z-50 pointer-events-auto">
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-2xl shadow-slate-950/20 dark:shadow-black/50 relative overflow-hidden">
        <!-- Top Subtle Accent Line -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-500 via-amber-500 to-orange-600"></div>

        <div class="flex items-start gap-3.5">
            <!-- App Icon -->
            <div class="w-12 h-12 rounded-xl bg-slate-900 dark:bg-slate-800 p-1.5 flex items-center justify-center shrink-0 border border-slate-200/60 dark:border-slate-700/60 shadow-xs">
                <img src="{{ asset('icons/icon-96x96.png') }}" alt="SIBIMA PWA" class="w-full h-full object-contain">
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5 mb-0.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 border border-orange-200/70 dark:border-orange-500/20">
                        Aplikasi Resmi
                    </span>
                    <span class="text-[10px] text-slate-400 font-bold">PWA</span>
                </div>
                <h4 class="text-xs font-black text-slate-900 dark:text-slate-100 leading-snug">
                    Pasang SIBIMA di Layar HP
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                    Akses bimbingan, jadwal ujian, dan riwayat revisi lebih cepat langsung dari layar utama perangkat Anda.
                </p>

                <!-- Actions -->
                <div class="flex items-center gap-2 mt-3 pt-1">
                    <button type="button" 
                            @click="installApp()" 
                            class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-sm transition-all active:scale-95 cursor-pointer">
                        <svg aria-hidden="true" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Pasang Sekarang</span>
                    </button>
                    <button type="button" 
                            @click="dismissBanner()" 
                            class="px-2.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                        Nanti Saja
                    </button>
                </div>
            </div>

            <!-- Close icon button -->
            <button type="button" 
                    @click="dismissBanner()" 
                    aria-label="Tutup saran instalasi"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg transition-colors cursor-pointer shrink-0">
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    function pwaInstallManager() {
        return {
            deferredPrompt: null,
            showBanner: false,
            isStandalone: false,

            init() {
                // Check if already in standalone display mode (already installed as PWA)
                this.isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
                if (this.isStandalone) {
                    return;
                }

                // Check dismissal history (skip if dismissed in the last 3 days)
                const dismissedUntil = localStorage.getItem('sibima_pwa_dismissed_until');
                if (dismissedUntil && Date.now() < parseInt(dismissedUntil, 10)) {
                    return;
                }

                // Listen for browser install prompt event
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    this.showBanner = true;
                });

                // Listen for appinstalled event
                window.addEventListener('appinstalled', () => {
                    this.showBanner = false;
                    this.deferredPrompt = null;
                    if (window.dispatchEvent) {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                title: 'SIBIMA Terpasang!',
                                message: 'Aplikasi SIBIMA berhasil dipasang di layar utama perangkat Anda.',
                                type: 'success'
                            }
                        }));
                    }
                });
            },

            async installApp() {
                if (!this.deferredPrompt) {
                    // Fallback for browsers (e.g. iOS Safari) where beforeinstallprompt is not standard
                    alert('Untuk memasang di iPhone/iPad: Tekan tombol Bagikan (Share) di Safari, lalu pilih "Tambahkan ke Layar Utama" (Add to Home Screen).');
                    return;
                }

                this.deferredPrompt.prompt();
                const choiceResult = await this.deferredPrompt.userChoice;
                if (choiceResult.outcome === 'accepted') {
                    this.showBanner = false;
                }
                this.deferredPrompt = null;
            },

            dismissBanner() {
                this.showBanner = false;
                // Dismiss for 3 days
                const threeDaysMs = 3 * 24 * 60 * 60 * 1000;
                localStorage.setItem('sibima_pwa_dismissed_until', (Date.now() + threeDaysMs).toString());
            }
        };
    }
</script>

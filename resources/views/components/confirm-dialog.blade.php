<div x-data="{
        open: false,
        title: 'Konfirmasi Tindakan',
        message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        type: 'warning',
        confirmText: 'Ya, Lanjutkan',
        cancelText: 'Batal',
        onConfirm: null,
        openDialog(detail = {}) {
            this.title = detail.title || (detail.type === 'danger' ? 'Konfirmasi Penghapusan' : 'Konfirmasi Tindakan');
            this.message = detail.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            this.type = detail.type || 'warning';
            this.confirmText = detail.confirmText || (detail.type === 'danger' ? 'Ya, Hapus' : 'Ya, Lanjutkan');
            this.cancelText = detail.cancelText || 'Batal';
            this.onConfirm = typeof detail.onConfirm === 'function' ? detail.onConfirm : null;
            this.open = true;
            this.$nextTick(() => {
                if (this.$refs.confirmBtn) this.$refs.confirmBtn.focus();
            });
        },
        proceed() {
            const cb = this.onConfirm;
            this.open = false;
            this.onConfirm = null;
            if (cb) cb();
        },
        cancel() {
            this.open = false;
            this.onConfirm = null;
        }
     }"
     @open-confirm-dialog.window="openDialog($event.detail)"
     @keydown.escape.window="if (open) cancel()"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[70] overflow-y-auto print:hidden"
     style="display: none;"
     role="alertdialog"
     aria-modal="true"
     aria-labelledby="confirm-dialog-title">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="cancel()"
         class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs transition-opacity"></div>

    <div class="min-h-screen px-4 py-8 flex items-end sm:items-center justify-center relative pointer-events-none">
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 pointer-events-auto pb-[max(0.5rem,env(safe-area-inset-bottom))] sm:pb-0">

            <div class="p-6">
                <div class="flex items-start gap-4">
                    <!-- Semantic Icon -->
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 border"
                         :class="{
                            'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/60': type === 'danger',
                            'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/60': type === 'warning',
                            'bg-orange-50 text-orange-600 border-orange-200 dark:bg-orange-950/40 dark:text-orange-400 dark:border-orange-800/60': type === 'primary' || type === 'emerald'
                         }">
                        <svg x-show="type === 'danger'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <svg x-show="type === 'warning'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <svg x-show="type !== 'danger' && type !== 'warning'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 id="confirm-dialog-title" class="text-base font-bold text-slate-900 dark:text-slate-100 leading-snug" x-text="title"></h3>
                        <p class="mt-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line" x-text="message"></p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50/80 dark:bg-slate-800/50 border-t border-slate-200/80 dark:border-slate-800 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                <button type="button"
                        @click="cancel()"
                        class="h-10 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    <span x-text="cancelText"></span>
                </button>
                <button type="button"
                        x-ref="confirmBtn"
                        @click="proceed()"
                        class="h-10 px-4 rounded-xl text-white text-xs font-bold transition-colors shadow-sm cursor-pointer"
                        :class="{
                            'bg-rose-600 hover:bg-rose-700 dark:bg-rose-600 dark:hover:bg-rose-500': type === 'danger',
                            'bg-amber-600 hover:bg-amber-700 dark:bg-amber-600 dark:hover:bg-amber-500': type === 'warning',
                            'bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500': type === 'primary',
                            'bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500': type === 'emerald'
                        }">
                    <span x-text="confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.sibimaConfirm = function (options = {}) {
        window.dispatchEvent(new CustomEvent('open-confirm-dialog', { detail: options }));
    };

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form || !form.matches('form[data-confirm]')) return;
        if (form.dataset.confirmed === '1') {
            delete form.dataset.confirmed;
            return;
        }
        e.preventDefault();
        const submitter = e.submitter || null;
        window.sibimaConfirm({
            title: form.dataset.confirmTitle || 'Konfirmasi Tindakan',
            message: form.dataset.confirm,
            type: form.dataset.confirmType || 'warning',
            confirmText: form.dataset.confirmButton || 'Ya, Lanjutkan',
            onConfirm: () => {
                form.dataset.confirmed = '1';
                if (submitter && typeof form.requestSubmit === 'function') {
                    form.requestSubmit(submitter);
                } else {
                    form.submit();
                }
            }
        });
    });

    document.addEventListener('click', function (e) {
        const targetTrigger = e.target.closest('[data-confirm-target]');
        if (targetTrigger) {
            const targetForm = document.getElementById(targetTrigger.dataset.confirmTarget);
            if (!targetForm) return;
            e.preventDefault();
            window.sibimaConfirm({
                title: targetTrigger.dataset.confirmTitle || 'Konfirmasi Tindakan',
                message: targetTrigger.dataset.confirm || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                type: targetTrigger.dataset.confirmType || 'warning',
                confirmText: targetTrigger.dataset.confirmButton || 'Ya, Lanjutkan',
                onConfirm: () => {
                    if (targetTrigger.dataset.confirmAction) {
                        targetForm.action = targetTrigger.dataset.confirmAction;
                    }
                    targetForm.dataset.confirmed = '1';
                    targetForm.submit();
                }
            });
            return;
        }

        const link = e.target.closest('a[data-confirm]');
        if (!link) return;
        e.preventDefault();
        window.sibimaConfirm({
            title: link.dataset.confirmTitle || 'Konfirmasi Tindakan',
            message: link.dataset.confirm,
            type: link.dataset.confirmType || 'warning',
            confirmText: link.dataset.confirmButton || 'Ya, Lanjutkan',
            onConfirm: () => {
                if (link.target === '_blank') {
                    window.open(link.href, '_blank', 'noopener,noreferrer');
                } else {
                    window.location.href = link.href;
                }
            }
        });
    });
</script>

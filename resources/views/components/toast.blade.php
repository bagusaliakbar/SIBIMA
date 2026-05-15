<div
    x-data="{
        notifications: [],
        add(notification) {
            const id = Date.now();
            this.notifications.push({
                id: id,
                title: notification.title,
                message: notification.message,
                type: notification.type || 'info',
            });
            setTimeout(() => {
                this.remove(id);
            }, 5000);
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }"
    @notify.window="add($event.detail)"
    class="fixed bottom-0 right-0 p-6 z-[200] space-y-4 w-full max-w-sm pointer-events-none"
>
    <template x-for="n in notifications" :key="n.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-4 opacity-0 scale-95"
            x-transition:enter-end="translate-y-0 opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 opacity-100 scale-100"
            x-transition:leave-end="translate-y-4 opacity-0 scale-95"
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50 p-4 pointer-events-auto flex gap-4 overflow-hidden relative"
        >
            <!-- Progress Bar -->
            <div class="absolute bottom-0 left-0 h-1 bg-indigo-500 transition-all duration-[5000ms] w-0" x-init="$el.style.width = '100%'"></div>

            <div class="shrink-0">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                    :class="{
                        'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600': n.type === 'success',
                        'bg-red-50 dark:bg-red-500/10 text-red-600': n.type === 'error',
                        'bg-orange-50 dark:bg-orange-500/10 text-orange-600': n.type === 'warning',
                        'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600': n.type === 'info' || n.type === 'message',
                    }"
                >
                    <template x-if="n.type === 'success'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </template>
                    <template x-if="n.type === 'error'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </template>
                    <template x-if="n.type === 'warning'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </template>
                    <template x-if="n.type === 'info' || n.type === 'message'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </template>
                </div>
            </div>
            
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight" x-text="n.title"></h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1 leading-relaxed" x-text="n.message"></p>
            </div>

            <button @click="remove(n.id)" class="shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </template>
</div>

@props([
    'name' => 'meeting_link',
    'id' => 'meeting_link',
    'value' => '',
    'label' => 'Link Meeting (Google Meet / Zoom)',
    'placeholder' => 'https://meet.google.com/xxx-xxxx-xxx',
    'required' => false
])

<div x-data="{
    link: '{{ old($name, $value) }}',
    meetOpened: false,
    openGoogleMeet() {
        window.open('https://meet.google.com/new', '_blank');
        this.meetOpened = true;
    },
    async pasteFromClipboard() {
        try {
            const text = await navigator.clipboard.readText();
            if (text) {
                this.link = text.trim();
                const input = document.getElementById('{{ $id }}');
                if (input) {
                    input.value = this.link;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        } catch (err) {
            alert('Silakan gunakan tombol pintasan Ctrl + V untuk menempelkan link.');
        }
    },
    isGoogleMeet() {
        return this.link && this.link.includes('meet.google.com');
    },
    isZoom() {
        return this.link && (this.link.includes('zoom.us') || this.link.includes('zoom.com'));
    }
}" class="space-y-2">
    <div class="flex items-center justify-between">
        <label for="{{ $id }}" class="block text-[10px] font-black uppercase tracking-widest text-slate-500">
            {{ $label }}
        </label>
        
        <div class="flex items-center gap-1.5">
            <button type="button" 
                    @click="pasteFromClipboard()" 
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-2xs active:scale-95 cursor-pointer"
                    title="Tempel dari Clipboard">
                <span>📋 Tempel Link</span>
            </button>
            <button type="button" 
                    @click="openGoogleMeet()" 
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all shadow-2xs active:scale-95 cursor-pointer"
                    title="Buka pembuat ruang Google Meet otomatis">
                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                <span>⚡ Buat Google Meet Instan</span>
            </button>
        </div>
    </div>

    <div>
        <input type="url" 
               name="{{ $name }}" 
               id="{{ $id }}" 
               x-model="link"
               placeholder="{{ $placeholder }}" 
               {{ $required ? 'required' : '' }}
               class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-sm transition-all" />
    </div>

    <!-- Feedback & Panduan Cepat -->
    <div class="space-y-1.5">
        <div x-show="meetOpened && !link" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-xl flex items-center justify-between gap-3 text-xs text-emerald-800 dark:text-emerald-300">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Ruang Google Meet baru telah dibuka di tab sebelah. Salin link ruangannya dan tempelkan di sini.</span>
            </div>
            <button type="button" @click="pasteFromClipboard()" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs shrink-0 transition-all shadow-xs cursor-pointer">
                📋 Tempel Sekarang
            </button>
        </div>

        <div x-show="link" x-cloak class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs">
            <div class="flex items-center gap-1.5">
                <template x-if="isGoogleMeet()">
                    <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Tautan Google Meet Terverifikasi
                    </span>
                </template>
                <template x-if="isZoom()">
                    <span class="flex items-center gap-1 text-blue-600 dark:text-blue-400 font-bold">
                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Tautan Zoom Meeting Terverifikasi
                    </span>
                </template>
                <template x-if="!isGoogleMeet() && !isZoom()">
                    <span class="flex items-center gap-1 text-slate-600 dark:text-slate-300 font-medium">
                        🔗 Tautan Meeting Online
                    </span>
                </template>
            </div>

            <a :href="link.startsWith('http') ? link : 'https://' + link" 
               target="_blank" 
               class="inline-flex items-center gap-1 px-2.5 py-1 bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 rounded-lg text-xs font-bold transition-all shadow-2xs">
                <span>↗ Uji Buka Link</span>
            </a>
        </div>
    </div>
</div>

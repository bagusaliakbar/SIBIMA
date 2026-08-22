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
            alert('Silakan gunakan pintasan Ctrl + V untuk menempelkan tautan.');
        }
    },
    isGoogleMeet() {
        return this.link && this.link.includes('meet.google.com');
    },
    isZoom() {
        return this.link && (this.link.includes('zoom.us') || this.link.includes('zoom.com'));
    }
}" class="space-y-1.5">
    <div class="flex items-center justify-between">
        <label for="{{ $id }}" class="block text-[10px] font-black uppercase tracking-widest text-slate-500">
            {{ $label }}
        </label>
        
        <button type="button" 
                @click="openGoogleMeet()" 
                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all shadow-2xs active:scale-95 cursor-pointer"
                title="Buka pembuat ruang Google Meet otomatis">
            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            <span>⚡ Buat Google Meet Instan</span>
        </button>
    </div>

    <div class="relative">
        <input type="url" 
               name="{{ $name }}" 
               id="{{ $id }}" 
               x-model="link"
               placeholder="{{ $placeholder }}" 
               {{ $required ? 'required' : '' }}
               class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3 pr-24 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all" />

        <div class="absolute inset-y-0 right-1.5 flex items-center gap-1">
            <button type="button" 
                    x-show="!link"
                    @click="pasteFromClipboard()" 
                    class="px-2 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded uppercase tracking-wider transition-all"
                    title="Tempel tautan dari clipboard">
                📋 Tempel
            </button>
            <a x-show="link" 
               :href="link.startsWith('http') ? link : 'https://' + link" 
               target="_blank" 
               class="px-2 py-1 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 text-blue-600 dark:text-blue-400 text-[10px] font-bold rounded uppercase tracking-wider transition-all"
               title="Uji & Buka tautan meeting">
                ↗ Uji Link
            </a>
        </div>
    </div>

    <!-- Feedback & Panduan Cepat -->
    <div class="space-y-1">
        <div x-show="meetOpened && !link" x-cloak class="p-2.5 bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-lg flex items-start gap-2 text-xs text-emerald-800 dark:text-emerald-300 animate-pulse">
            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Tab Google Meet baru telah terbuka. Salin link ruang meeting yang muncul, lalu klik tombol <b>📋 Tempel</b> di atas.</span>
        </div>

        <div x-show="isGoogleMeet()" x-cloak class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold">
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            <span>Tautan Google Meet valid & siap digunakan.</span>
        </div>

        <div x-show="isZoom()" x-cloak class="flex items-center gap-1.5 text-xs text-blue-600 dark:text-blue-400 font-bold">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            <span>Tautan Zoom Meeting valid & siap digunakan.</span>
        </div>
    </div>
</div>

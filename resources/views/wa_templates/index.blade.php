<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <x-breadcrumb :items="[
                ['label' => 'Template WhatsApp', 'route' => null]
            ]" />
            <a href="{{ route('wa-broadcasts.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-orange-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <span>Kirim Siaran (Broadcast WA)</span>
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-6" 
         x-data="{ 
             viewMode: localStorage.getItem('sibima_wa_templates_view_mode') || 'card' 
         }" 
         x-init="$watch('viewMode', val => localStorage.setItem('sibima_wa_templates_view_mode', val))">

        @if(!empty($isCircuitTripped))
            <!-- Emergency Circuit Breaker Banner -->
            <div class="p-5 rounded-2xl bg-amber-500/15 border-2 border-amber-500/40 text-amber-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 backdrop-blur-md shadow-lg">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-amber-300">Pengiriman WhatsApp Dijeda Otomatis (Circuit Breaker Aktif)</h4>
                        <p class="text-xs text-amber-200/80 mt-0.5 leading-relaxed">
                            Sistem mendeteksi kendala koneksi atau batas kuota/indikasi pemblokiran dari gateway WhatsApp:
                            <span class="font-bold underline">{{ $circuitReason ?? 'Kendala gateway' }}</span>.
                            Sistem otomatis menahan pengiriman pesan keluar untuk melindungi akun nomor WhatsApp dari pemblokiran permanen.
                        </p>
                    </div>
                </div>
                <form action="{{ route('wa-templates.reset-circuit') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-900 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md active:scale-95 cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Reset & Coba Lagi</span>
                    </button>
                </form>
            </div>
        @endif

        <!-- Master Switch & Banner Info -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
            <div class="absolute -right-10 -bottom-10 w-56 h-56 {{ $isWhatsAppGloballyEnabled ? 'bg-emerald-500/10' : 'bg-rose-500/10' }} rounded-full blur-3xl pointer-events-none transition-all"></div>
            
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-3xl space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                      
                    </div>

                    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">
                        Pengaturan Notifikasi & Template WhatsApp
                    </h1>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        @if($isWhatsAppGloballyEnabled)
                            Seluruh pesan WhatsApp otomatis (bimbingan, ujian, revisi, dan pengingat) <strong>aktif terkirim</strong> ke mahasiswa dan dosen. Anda juga dapat mematikan pesan tertentu secara spesifik di bawah ini.
                        @else
                            <span class="text-rose-300 font-semibold">Perhatian:</span> Seluruh pengiriman pesan WhatsApp sedang <strong>dimatikan secara global</strong>. Sistem tidak akan mengirim pesan WhatsApp hingga Anda mengaktifkannya kembali.
                        @endif
                    </p>
                </div>

                <!-- Master Toggle Action Button -->
                <div class="shrink-0 bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/10 flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end justify-between gap-3">
                    <div class="text-left lg:text-right">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Saklar Utama Sistem:</span>
                        <span class="text-xs font-black {{ $isWhatsAppGloballyEnabled ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $isWhatsAppGloballyEnabled ? 'Gateway Sedang Aktif' : 'Gateway Dinonaktifkan' }}
                        </span>
                    </div>

                    <form action="{{ route('wa-templates.toggle-global') }}" method="POST" onsubmit="return confirm('{{ $isWhatsAppGloballyEnabled ? 'Apakah Anda yakin ingin MENONAKTIFKAN seluruh pengiriman notifikasi WhatsApp di sistem SIBIMA?' : 'Apakah Anda yakin ingin MENGAKTIFKAN kembali seluruh pengiriman notifikasi WhatsApp?' }}');">
                        @csrf
                        @if($isWhatsAppGloballyEnabled)
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-rose-600/30 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Matikan Semua WA</span>
                            </button>
                        @else
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-emerald-600/30 cursor-pointer animate-bounce">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Aktifkan Semua WA</span>
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Filter Tab Category & View Switcher -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-3">
            <!-- Filter Tab Category -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
                <a href="{{ route('wa-templates.index', ['category' => 'all']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedCategory === 'all' ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 border border-slate-200 dark:border-slate-700' }}">
                    Semua Kategori ({{ $totalCount ?? $templates->count() }})
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('wa-templates.index', ['category' => $cat]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedCategory === $cat ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 border border-slate-200 dark:border-slate-700' }}">
                        {{ $cat }} ({{ $categoryCounts[$cat] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <!-- View Switcher (Card vs List) -->
            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 rounded-xl shadow-2xs shrink-0 self-end sm:self-auto">
                <button type="button" 
                        @click="viewMode = 'card'"
                        :class="viewMode === 'card' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Card</span>
                </button>
                <button type="button" 
                        @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span>List</span>
                </button>
            </div>
        </div>

        <!-- VIEW MODE 1: CARD GRID -->
        <div x-show="viewMode === 'card'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($templates as $template)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border {{ $template->is_active ? 'border-slate-200 dark:border-slate-700' : 'border-slate-300 dark:border-slate-700/60 opacity-85' }} p-6 flex flex-col justify-between shadow-2xs hover:shadow-md transition-all group relative">
                    <div>
                        <!-- Card Badges Header -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                {{ $template->category }}
                            </span>
                            
                            <div class="flex items-center gap-1.5">
                                @if($template->is_customized)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        Custom
                                    </span>
                                @endif

                                @if($template->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors mb-2">
                            {{ $template->name }}
                        </h3>

                        <!-- Preview Teks -->
                        <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-3.5 border border-slate-100 dark:border-slate-800 mb-4">
                            <p class="text-xs font-mono text-slate-600 dark:text-slate-300 line-clamp-4 whitespace-pre-line leading-relaxed">
                                {{ $template->content }}
                            </p>
                        </div>
                    </div>

                    <!-- Bottom Controls: Per-Template On/Off Switch & Edit Action -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-between gap-2">
                        <!-- Per-Template Toggle Switch Form -->
                        <form action="{{ route('wa-templates.toggle-status', $template) }}" method="POST" class="shrink-0">
                            @csrf
                            <button type="submit" 
                                    title="{{ $template->is_active ? 'Klik untuk mematikan notifikasi ini' : 'Klik untuk mengaktifkan notifikasi ini' }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition-all border shadow-2xs cursor-pointer {{ $template->is_active ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 dark:hover:bg-rose-950 dark:hover:text-rose-300' : 'bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200' }}">
                                @if($template->is_active)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>ON</span>
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <span>OFF</span>
                                @endif
                            </button>
                        </form>

                        <a href="{{ route('wa-templates.edit', $template) }}" 
                           class="flex-1 inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-colors shadow-2xs shadow-orange-600/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span>Edit Format</span>
                        </a>

                        @if($template->is_customized)
                            <form action="{{ route('wa-templates.reset', $template) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan format teks ini ke versi default bawaan?');">
                                @csrf
                                <button type="submit" 
                                        title="Reset ke Default Bawaan"
                                        class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 bg-slate-100 dark:bg-slate-700/50 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-slate-200 dark:border-slate-700">
                    <p class="text-sm text-slate-500">Tidak ada template pada kategori ini.</p>
                </div>
            @endforelse
        </div>

        <!-- VIEW MODE 2: LIST VIEW (TABLE) -->
        <div x-show="viewMode === 'list'" x-cloak class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700/80 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="py-3.5 px-4 w-28">Status</th>
                            <th class="py-3.5 px-4 w-44">Kategori & Kode</th>
                            <th class="py-3.5 px-4 min-w-[220px]">Nama Template</th>
                            <th class="py-3.5 px-4 min-w-[320px]">Pratinjau Pesan WhatsApp</th>
                            <th class="py-3.5 px-4 w-36 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($templates as $template)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors {{ !$template->is_active ? 'opacity-70 bg-slate-50/30 dark:bg-slate-900/20' : '' }}">
                                <!-- Status Toggle -->
                                <td class="py-4 px-4 align-top">
                                    <form action="{{ route('wa-templates.toggle-status', $template) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                title="{{ $template->is_active ? 'Klik untuk mematikan notifikasi' : 'Klik untuk mengaktifkan notifikasi' }}"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-[11px] font-black transition-all border cursor-pointer {{ $template->is_active ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' : 'bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-emerald-50 hover:text-emerald-700' }}">
                                            @if($template->is_active)
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>ON</span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                <span>OFF</span>
                                            @endif
                                        </button>
                                    </form>
                                </td>

                                <!-- Kategori & Kode -->
                                <td class="py-4 px-4 align-top space-y-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                        {{ $template->category }}
                                    </span>
                                    <div class="font-mono text-[10px] text-slate-400 dark:text-slate-500 break-all">
                                        {{ $template->code }}
                                    </div>
                                    @if($template->is_customized)
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            Custom
                                        </span>
                                    @endif
                                </td>

                                <!-- Nama Template -->
                                <td class="py-4 px-4 align-top">
                                    <div class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                                        {{ $template->name }}
                                    </div>
                                    @if($template->available_variables && is_array($template->available_variables))
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach(array_slice($template->available_variables, 0, 4) as $vKey => $vDesc)
                                                <span class="font-mono text-[9px] bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded">
                                                    {{ '{' . $vKey . '}' }}
                                                </span>
                                            @endforeach
                                            @if(count($template->available_variables) > 4)
                                                <span class="text-[9px] text-slate-400 self-center">
                                                    +{{ count($template->available_variables) - 4 }} variabel
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- Pratinjau Pesan -->
                                <td class="py-4 px-4 align-top">
                                    <div class="bg-slate-50 dark:bg-slate-900/80 rounded-xl p-2.5 border border-slate-100 dark:border-slate-800">
                                        <p class="font-mono text-[11px] text-slate-600 dark:text-slate-300 line-clamp-3 whitespace-pre-line leading-relaxed">
                                            {{ $template->content }}
                                        </p>
                                    </div>
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-4 align-top text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <a href="{{ route('wa-templates.edit', $template) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-colors shadow-2xs shadow-orange-600/20">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            <span>Edit</span>
                                        </a>

                                        @if($template->is_customized)
                                            <form action="{{ route('wa-templates.reset', $template) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan format teks ini ke versi default bawaan?');">
                                                @csrf
                                                <button type="submit" 
                                                        title="Reset ke Default Bawaan"
                                                        class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 bg-slate-100 dark:bg-slate-700/50 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-colors cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    Tidak ada template pada kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

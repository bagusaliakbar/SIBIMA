<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard',
                ['label' => 'Template WhatsApp', 'route' => null]
            ]" />
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Banner Info -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-orange-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-bold text-orange-400 mb-3 border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    Manajemen Format Pesan WA
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight mb-2 text-white">Pengaturan Template WhatsApp (Fonnte)</h1>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Atur format pesan notifikasi WhatsApp secara dinamis. Anda dapat menyisipkan kata-kata khusus, pesan ucapan, atau mengubah struktur kalimat menggunakan variabel/tag yang tersedia tanpa perlu memodifikasi kode program.
                </p>
            </div>
        </div>

        <!-- Filter Tab Category -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar border-b border-slate-200 dark:border-slate-800">
            <a href="{{ route('wa-templates.index', ['category' => 'all']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedCategory === 'all' ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 border border-slate-200 dark:border-slate-700' }}">
                Semua Category
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('wa-templates.index', ['category' => $cat]) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedCategory === $cat ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 border border-slate-200 dark:border-slate-700' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        <!-- Notification Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($templates as $template)
                <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-all group">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                {{ $template->category }}
                            </span>
                            @if($template->is_customized)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Di-Custom
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    Default Bawaan
                                </span>
                            @endif
                        </div>

                        <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors mb-2">
                            {{ $template->name }}
                        </h3>

                        <!-- Preview Teks -->
                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-3.5 border border-slate-100 dark:border-slate-800/80 mb-4">
                            <p class="text-xs font-mono text-slate-600 dark:text-slate-400 line-clamp-4 whitespace-pre-line leading-relaxed">
                                {{ $template->content }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                        <a href="{{ route('wa-templates.edit', $template) }}" 
                           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-colors shadow-sm shadow-orange-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Format Teks
                        </a>

                        @if($template->is_customized)
                            <form action="{{ route('wa-templates.reset', $template) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan format teks ini ke versi default bawaan?');">
                                @csrf
                                <button type="submit" 
                                        title="Reset ke Default Bawaan"
                                        class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 bg-slate-100 dark:bg-slate-700/50 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-colors">
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
    </div>
</x-app-layout>

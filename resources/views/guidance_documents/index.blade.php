<x-app-layout>
    <x-slot name="header">
        <div class="w-full">
            <x-breadcrumb :items="[
                ['label' => 'Arsip Panduan & Dokumen Skripsi', 'route' => null]
            ]" />
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                        <span>Arsip Panduan & Dokumen Skripsi</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium leading-relaxed">
                        Buku pedoman resmi penulisan, format template Word/LaTeX, form bimbingan, dan berkas administrasi skripsi program studi.
                    </p>
                </div>

                @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                    <button type="button" 
                            @click="openUploadModal()"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-orange-600/25 hover:shadow-lg hover:shadow-orange-600/35 hover:scale-[1.02] active:scale-[0.98] cursor-pointer shrink-0 whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Unggah Dokumen Baru</span>
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div x-data="guidanceDocumentManager()" class="space-y-6 pb-16">
        
        <!-- Filter Bar & Search Toolbar -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 sm:gap-4">
            
            <!-- Category Filter Tabs -->
            <div class="flex items-center gap-2.5 overflow-x-auto custom-scrollbar pb-1 md:pb-0">
                <!-- Tab: Semua Dokumen -->
                <a href="{{ route('guidance-documents.index', ['category' => 'all', 'search' => $search]) }}"
                   class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($category ?? 'all') === 'all' ? 'bg-orange-600 text-white shadow-md shadow-orange-600/20 border border-orange-500' : 'bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700' }}">
                    @if(($category ?? 'all') === 'all')
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    @else
                        <svg class="w-4 h-4 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    @endif
                    <span>Semua Dokumen</span>
                    <span class="ml-0.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ ($category ?? 'all') === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300/40 dark:border-slate-700' }}">
                        {{ $categoryCounts['all'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab: Buku Panduan -->
                <a href="{{ route('guidance-documents.index', ['category' => 'panduan_skripsi', 'search' => $search]) }}"
                   class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($category ?? 'all') === 'panduan_skripsi' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 border border-indigo-500' : 'bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700' }}">
                    @if(($category ?? 'all') === 'panduan_skripsi')
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    @else
                        <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    @endif
                    <span>Buku Panduan</span>
                    <span class="ml-0.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ ($category ?? 'all') === 'panduan_skripsi' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300/40 dark:border-slate-700' }}">
                        {{ $categoryCounts['panduan_skripsi'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab: Template Dokumen -->
                <a href="{{ route('guidance-documents.index', ['category' => 'format_template', 'search' => $search]) }}"
                   class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($category ?? 'all') === 'format_template' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 border border-emerald-500' : 'bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700' }}">
                    @if(($category ?? 'all') === 'format_template')
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @else
                        <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @endif
                    <span>Template Dokumen</span>
                    <span class="ml-0.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ ($category ?? 'all') === 'format_template' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300/40 dark:border-slate-700' }}">
                        {{ $categoryCounts['format_template'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab: Pedoman Bimbingan -->
                <a href="{{ route('guidance-documents.index', ['category' => 'pedoman_bimbingan', 'search' => $search]) }}"
                   class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($category ?? 'all') === 'pedoman_bimbingan' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20 border border-amber-500' : 'bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700' }}">
                    @if(($category ?? 'all') === 'pedoman_bimbingan')
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    @else
                        <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    @endif
                    <span>Pedoman Bimbingan</span>
                    <span class="ml-0.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ ($category ?? 'all') === 'pedoman_bimbingan' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300/40 dark:border-slate-700' }}">
                        {{ $categoryCounts['pedoman_bimbingan'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab: Berkas Pendukung -->
                <a href="{{ route('guidance-documents.index', ['category' => 'lainnya', 'search' => $search]) }}"
                   class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($category ?? 'all') === 'lainnya' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20 border border-blue-500' : 'bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700' }}">
                    @if(($category ?? 'all') === 'lainnya')
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
                    @else
                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
                    @endif
                    <span>Berkas Pendukung</span>
                    <span class="ml-0.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ ($category ?? 'all') === 'lainnya' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300/40 dark:border-slate-700' }}">
                        {{ $categoryCounts['lainnya'] ?? 0 }}
                    </span>
                </a>
            </div>

            <!-- Search Bar using x-search-input Component -->
            <div class="w-full sm:w-72 shrink-0">
                <x-search-input 
                    name="search" 
                    :value="$search ?? ''" 
                    placeholder="Cari nama panduan / berkas..." 
                    route="guidance-documents.index"
                    :params="['category' => $category ?? 'all']" />
            </div>
        </div>

        <!-- Document Cards Grid -->
        @if($documents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($documents as $doc)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs hover:shadow-xl hover:shadow-slate-200/40 dark:hover:shadow-black/40 hover:border-orange-500/40 dark:hover:border-orange-500/40 hover:-translate-y-0.5 transition-all duration-300 flex flex-col justify-between group relative overflow-hidden">
                        
                        <!-- Top Ambient Glow -->
                        <div class="absolute -top-12 -right-12 w-32 h-32 rounded-full blur-2xl pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-500 {{ $doc->category_color === 'indigo' ? 'bg-indigo-500/10' : ($doc->category_color === 'emerald' ? 'bg-emerald-500/10' : ($doc->category_color === 'amber' ? 'bg-amber-500/10' : 'bg-orange-500/10')) }}"></div>

                        <div class="relative z-10">
                            <!-- Card Header: Icon, Category & Status/Actions -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3.5">
                                    <!-- Styled File Icon Container -->
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-xs border transition-transform duration-300 group-hover:scale-105 {{ $doc->icon_type === 'pdf' ? 'bg-rose-50 dark:bg-rose-900/25 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-800/40' : ($doc->icon_type === 'word' ? 'bg-blue-50 dark:bg-blue-900/25 text-blue-600 dark:text-blue-400 border-blue-100 dark:border-blue-800/40' : ($doc->icon_type === 'excel' ? 'bg-emerald-50 dark:bg-emerald-900/25 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800/40' : 'bg-amber-50 dark:bg-amber-900/25 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-800/40')) }}">
                                        @if($doc->icon_type === 'pdf')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-6 4h4"/></svg>
                                        @elseif($doc->icon_type === 'word')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @elseif($doc->icon_type === 'excel')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide {{ $doc->category_color === 'indigo' ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-700/50' : ($doc->category_color === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-700/50' : ($doc->category_color === 'amber' ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-700/50' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600')) }}">
                                            {{ $doc->category_label }}
                                        </span>
                                        <div class="text-[11px] font-bold text-slate-400 dark:text-slate-400 mt-1 flex items-center gap-1.5 font-mono">
                                            <span class="uppercase font-extrabold text-slate-600 dark:text-slate-200">{{ $doc->file_extension }}</span>
                                            <span>•</span>
                                            <span>{{ $doc->formatted_file_size }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge & Admin Action Dropdown -->
                                <div class="flex items-center gap-2">
                                    @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                        @if($doc->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-700/60 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Draf
                                            </span>
                                        @endif

                                        <!-- Admin 3-Dot Quick Action Dropdown -->
                                        <div x-data="{ open: false }" class="relative" @click.away="open = false">
                                            <button @click="open = !open" 
                                                    type="button" 
                                                    class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors cursor-pointer"
                                                    :class="open ? 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                                                    title="Menu Pengaturan">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="5" r="2"/>
                                                    <circle cx="12" cy="12" r="2"/>
                                                    <circle cx="12" cy="19" r="2"/>
                                                </svg>
                                            </button>

                                            <!-- Dropdown Popover Menu -->
                                            <div x-show="open" 
                                                 x-cloak
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                                 class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200 dark:border-slate-700/80 p-1.5 z-30 space-y-0.5">
                                                
                                                <!-- Edit Option -->
                                                <button type="button" 
                                                        @click="open = false; openEditModal({{ json_encode($doc) }})"
                                                        class="group flex w-full items-center gap-2.5 px-2.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/70 hover:text-slate-900 dark:hover:text-white rounded-xl transition-all cursor-pointer">
                                                    <span class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform border border-indigo-100/60 dark:border-indigo-800/40">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                    </span>
                                                    <span>Edit Data</span>
                                                </button>

                                                <!-- Toggle Active Option -->
                                                <form action="{{ route('guidance-documents.toggle', $doc->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="group flex w-full items-center gap-2.5 px-2.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/70 hover:text-slate-900 dark:hover:text-white rounded-xl transition-all cursor-pointer">
                                                        @if($doc->is_active)
                                                            <span class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/70 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform border border-amber-100/60 dark:border-amber-800/40">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                                            </span>
                                                            <span>Sembunyikan</span>
                                                        @else
                                                            <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform border border-emerald-100/60 dark:border-emerald-800/40">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            </span>
                                                            <span>Aktifkan</span>
                                                        @endif
                                                    </button>
                                                </form>

                                                <div class="my-1 border-t border-slate-100 dark:border-slate-700/70 mx-1"></div>

                                                <!-- Delete Option -->
                                                <form action="{{ route('guidance-documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Hapus permanen dokumen panduan {{ addslashes($doc->title) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="group flex w-full items-center gap-2.5 px-2.5 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/15 hover:text-rose-700 dark:hover:text-rose-300 rounded-xl transition-all cursor-pointer">
                                                        <span class="w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/70 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform border border-rose-100/60 dark:border-rose-800/40">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </span>
                                                        <span>Hapus Berkas</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Document Title & Description -->
                            <div class="mt-4">
                                <h3 class="font-bold text-slate-900 dark:text-white text-base sm:text-lg leading-snug tracking-tight line-clamp-2"
                                    title="{{ $doc->title }}">
                                    {{ $doc->title }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed min-h-[36px]">
                                    {{ $doc->description ?: 'Buku pedoman resmi dan format dokumen skripsi program studi.' }}
                                </p>
                            </div>

                            <!-- Document Meta Details (Clean minimal bar) -->
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-400 font-medium">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>{{ number_format($doc->download_count, 0, ',', '.') }}x diunduh</span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $doc->updated_at->isoFormat('D MMM Y') }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-2.5">
                                @if($doc->icon_type === 'pdf')
                                    <!-- Read PDF Preview Button -->
                                    <a href="{{ route('guidance-documents.view', $doc->id) }}" 
                                       target="_blank"
                                       class="flex-1 py-2.5 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5 shadow-2xs hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                                       title="Buka dan baca dokumen PDF langsung di tab baru">
                                        <svg class="w-4 h-4 text-slate-500 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Baca PDF</span>
                                    </a>

                                    <!-- Download Button -->
                                    <a href="{{ route('guidance-documents.download', $doc->id) }}" 
                                       class="flex-1 py-2.5 px-3 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5 shadow-md shadow-orange-600/20 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                                       title="Unduh berkas {{ $doc->original_name }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Unduh</span>
                                    </a>
                                @else
                                    <!-- Full Width Download Button for Non-PDF (Word/Excel/ZIP) -->
                                    <a href="{{ route('guidance-documents.download', $doc->id) }}" 
                                       class="w-full py-2.5 px-4 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-2 shadow-md shadow-orange-600/20 hover:shadow-lg hover:scale-[1.01] active:scale-[0.98] cursor-pointer"
                                       title="Unduh berkas {{ $doc->original_name }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Unduh Berkas ({{ strtoupper($doc->file_extension) }})</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $documents->links() }}
            </div>
        @else
            <!-- Empty State Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 p-12 sm:p-16 text-center shadow-xs">
                <div class="w-16 h-16 rounded-3xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-xs">
                    📂
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Belum Ada Dokumen Panduan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1.5 leading-relaxed">
                    @if(!empty($search))
                        Tidak ditemukan dokumen yang cocok dengan kata kunci "{{ $search }}". Silakan reset pencarian.
                    @else
                        Dokumen panduan skripsi atau format berkas belum diunggah untuk kategori ini.
                    @endif
                </p>
                @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                    <button type="button" 
                            @click="openUploadModal()"
                            class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Unggah Dokumen Pertama</span>
                    </button>
                @endif
            </div>
        @endif

        <!-- Upload & Edit Modal Dialog (Admin & Kaprodi) -->
        @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
            <div x-show="showModal" 
                 x-cloak 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="showModal = false">
                
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-lg overflow-hidden"
                     @click.away="showModal = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2">
                    
                    <form :action="isEditing ? '{{ url('guidance-documents') }}/' + editId : '{{ route('guidance-documents.store') }}'" 
                          method="POST" 
                          enctype="multipart/form-data">
                        @csrf
                        <template x-if="isEditing">
                            <input type="hidden" name="_method" value="PATCH">
                        </template>

                        <!-- Modal Header -->
                        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-2xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center text-lg font-bold shadow-2xs">
                                    📑
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white" x-text="isEditing ? 'Edit Dokumen Panduan' : 'Unggah Dokumen Panduan Baru'"></h3>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-400">Berkas akan tersimpan aman dan dapat diunduh mahasiswa & dosen</p>
                                </div>
                            </div>
                            <button @click="showModal = false" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 text-lg font-bold transition-colors cursor-pointer">&times;</button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
                            <!-- Judul Dokumen -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Judul Dokumen <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       name="title" 
                                       x-model="formData.title" 
                                       required
                                       placeholder="Contoh: Buku Pedoman Penulisan Skripsi Edisi 2026" 
                                       class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-semibold">
                            </div>

                            <!-- Kategori Dokumen -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Kategori Dokumen <span class="text-rose-500">*</span>
                                </label>
                                <select name="category" 
                                        x-model="formData.category" 
                                        required
                                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-semibold">
                                    <option value="panduan_skripsi">📖 Buku Panduan Skripsi</option>
                                    <option value="format_template">📝 Template Dokumen (Word/LaTeX)</option>
                                    <option value="pedoman_bimbingan">⚖️ Pedoman Alur Bimbingan & Etika</option>
                                    <option value="lainnya">📁 Berkas Pendukung / Formulir Lainnya</option>
                                </select>
                            </div>

                            <!-- Deskripsi Singkat -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Deskripsi Singkat (Opsional)
                                </label>
                                <textarea name="description" 
                                          x-model="formData.description" 
                                          rows="2" 
                                          placeholder="Tuliskan catatan penting atau cakupan pedoman ini..."
                                          class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"></textarea>
                            </div>

                            <!-- Upload Berkas File -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Pilih Berkas Dokumen <span x-show="!isEditing" class="text-rose-500">*</span>
                                </label>
                                <input type="file" 
                                       name="document_file" 
                                       :required="!isEditing"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z"
                                       class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 dark:file:bg-orange-950/60 dark:file:text-orange-300 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl p-1.5 bg-slate-50 dark:bg-slate-900">
                                <p class="text-[11px] text-slate-400 mt-1.5">
                                    Format: <strong>PDF, DOC, DOCX, XLS, XLSX, ZIP</strong>. Maksimal 25 MB.
                                    <span x-show="isEditing" class="italic">(Kosongkan jika tidak ingin mengganti file fisik).</span>
                                </p>
                            </div>

                            <!-- Checkbox Publikasi -->
                            <div class="pt-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           value="1" 
                                           x-model="formData.is_active" 
                                           class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Publikasikan langsung (Aktif)</span>
                                </label>
                                <p class="text-[11px] text-slate-400 pl-6 mt-0.5">Jika tidak dicentang, dokumen berstatus Draf dan hanya terlihat oleh Admin/Kaprodi.</p>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-2.5">
                            <button type="button" 
                                    @click="showModal = false" 
                                    class="px-4 py-2 text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold shadow-md shadow-orange-600/20 transition-all cursor-pointer">
                                <span x-text="isEditing ? 'Simpan Perubahan' : 'Unggah Dokumen'"></span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        @endif

    </div>

    <script>
        function guidanceDocumentManager() {
            return {
                showModal: false,
                isEditing: false,
                editId: null,
                formData: {
                    title: '',
                    category: 'panduan_skripsi',
                    description: '',
                    is_active: true
                },
                openUploadModal() {
                    this.isEditing = false;
                    this.editId = null;
                    this.formData = {
                        title: '',
                        category: 'panduan_skripsi',
                        description: '',
                        is_active: true
                    };
                    this.showModal = true;
                },
                openEditModal(doc) {
                    this.isEditing = true;
                    this.editId = doc.id;
                    this.formData = {
                        title: doc.title || '',
                        category: doc.category || 'panduan_skripsi',
                        description: doc.description || '',
                        is_active: Boolean(doc.is_active)
                    };
                    this.showModal = true;
                }
            }
        }
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb />
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
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-orange-600/20 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] cursor-pointer shrink-0">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Unggah Dokumen Baru</span>
                </button>
            @endif
        </div>
    </x-slot>

    <div x-data="guidanceDocumentManager()" class="space-y-6 pb-16">
        
        <!-- Filter Bar & Search Toolbar -->
        <div class="bg-white dark:bg-slate-800/90 rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            
            <!-- Category Filter Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-2 lg:pb-0">
                <a href="{{ route('guidance-documents.index', ['category' => 'all', 'search' => $search]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($category ?? 'all') === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-sm' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    <span>Semua Dokumen</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ ($category ?? 'all') === 'all' ? 'bg-white/20 text-white dark:bg-slate-900/20 dark:text-slate-900' : 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300' }}">
                        {{ $categoryCounts['all'] ?? 0 }}
                    </span>
                </a>

                <a href="{{ route('guidance-documents.index', ['category' => 'panduan_skripsi', 'search' => $search]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($category ?? 'all') === 'panduan_skripsi' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    <span>📖 Buku Panduan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ ($category ?? 'all') === 'panduan_skripsi' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300' }}">
                        {{ $categoryCounts['panduan_skripsi'] ?? 0 }}
                    </span>
                </a>

                <a href="{{ route('guidance-documents.index', ['category' => 'format_template', 'search' => $search]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($category ?? 'all') === 'format_template' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    <span>📝 Template Dokumen</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ ($category ?? 'all') === 'format_template' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300' }}">
                        {{ $categoryCounts['format_template'] ?? 0 }}
                    </span>
                </a>

                <a href="{{ route('guidance-documents.index', ['category' => 'pedoman_bimbingan', 'search' => $search]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($category ?? 'all') === 'pedoman_bimbingan' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    <span>⚖️ Pedoman Bimbingan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ ($category ?? 'all') === 'pedoman_bimbingan' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300' }}">
                        {{ $categoryCounts['pedoman_bimbingan'] ?? 0 }}
                    </span>
                </a>

                <a href="{{ route('guidance-documents.index', ['category' => 'lainnya', 'search' => $search]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($category ?? 'all') === 'lainnya' ? 'bg-slate-700 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    <span>📁 Berkas Pendukung</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ ($category ?? 'all') === 'lainnya' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300' }}">
                        {{ $categoryCounts['lainnya'] ?? 0 }}
                    </span>
                </a>
            </div>

            <!-- Search Bar -->
            <form action="{{ route('guidance-documents.index') }}" method="GET" class="relative w-full lg:w-80 shrink-0">
                <input type="hidden" name="category" value="{{ $category ?? 'all' }}">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Cari nama panduan / berkas..." 
                           class="w-full pl-10 pr-9 py-2.5 bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if(!empty($search))
                        <a href="{{ route('guidance-documents.index', ['category' => $category ?? 'all']) }}" 
                           class="w-5 h-5 flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 absolute right-3 top-2.5 rounded-full hover:bg-slate-200/60 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                           title="Hapus pencarian">
                            &times;
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Document Cards Grid -->
        @if($documents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($documents as $doc)
                    <div class="bg-white dark:bg-slate-800/90 rounded-3xl border border-slate-200/70 dark:border-slate-700/70 p-6 shadow-xs hover:shadow-xl hover:shadow-slate-200/40 dark:hover:shadow-black/30 hover:border-orange-500/40 dark:hover:border-orange-500/40 hover:-translate-y-0.5 transition-all duration-300 flex flex-col justify-between group relative overflow-hidden">
                        
                        <!-- Top Ambient Glow -->
                        <div class="absolute -top-12 -right-12 w-32 h-32 rounded-full blur-2xl pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-500 {{ $doc->category_color === 'indigo' ? 'bg-indigo-500/10' : ($doc->category_color === 'emerald' ? 'bg-emerald-500/10' : ($doc->category_color === 'amber' ? 'bg-amber-500/10' : 'bg-orange-500/10')) }}"></div>

                        <div class="relative z-10">
                            <!-- Card Header: Icon, Category & Status/Actions -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3.5">
                                    <!-- Styled File Icon Container -->
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-xs border transition-transform duration-300 group-hover:scale-105 {{ $doc->icon_type === 'pdf' ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-900/40' : ($doc->icon_type === 'word' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border-blue-100 dark:border-blue-900/40' : ($doc->icon_type === 'excel' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/40' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-900/40')) }}">
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide {{ $doc->category_color === 'indigo' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60' : ($doc->category_color === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60' : ($doc->category_color === 'amber' ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700')) }}">
                                            {{ $doc->category_label }}
                                        </span>
                                        <div class="text-[11px] font-bold text-slate-400 dark:text-slate-500 mt-1 flex items-center gap-1.5 font-mono">
                                            <span class="uppercase font-extrabold text-slate-600 dark:text-slate-300">{{ $doc->file_extension }}</span>
                                            <span>•</span>
                                            <span>{{ $doc->formatted_file_size }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge & Admin Action Dropdown -->
                                <div class="flex items-center gap-2">
                                    @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                        @if($doc->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Draf
                                            </span>
                                        @endif

                                        <!-- Admin 3-Dot Quick Action Dropdown -->
                                        <div x-data="{ open: false }" class="relative" @click.away="open = false">
                                            <button @click="open = !open" 
                                                    type="button" 
                                                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition-colors cursor-pointer"
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
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95"
                                                 class="absolute right-0 mt-1.5 w-44 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 z-30">
                                                
                                                <!-- Edit Option -->
                                                <button type="button" 
                                                        @click="open = false; openEditModal({{ json_encode($doc) }})"
                                                        class="w-full px-3.5 py-2 text-left text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 flex items-center gap-2 transition-colors">
                                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                    <span>Edit Data</span>
                                                </button>

                                                <!-- Toggle Active Option -->
                                                <form action="{{ route('guidance-documents.toggle', $doc->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="w-full px-3.5 py-2 text-left text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 flex items-center gap-2 transition-colors">
                                                        @if($doc->is_active)
                                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                                            <span>Sembunyikan</span>
                                                        @else
                                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            <span>Aktifkan</span>
                                                        @endif
                                                    </button>
                                                </form>

                                                <div class="my-1 border-t border-slate-100 dark:border-slate-700"></div>

                                                <!-- Delete Option -->
                                                <form action="{{ route('guidance-documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Hapus permanen dokumen panduan {{ addslashes($doc->title) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="w-full px-3.5 py-2 text-left text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center gap-2 transition-colors">
                                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>{{ number_format($doc->download_count, 0, ',', '.') }}x diunduh</span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $doc->updated_at->isoFormat('D MMM Y') }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-700/60">
                            <div class="flex items-center gap-2.5">
                                @if($doc->icon_type === 'pdf')
                                    <!-- Read PDF Preview Button -->
                                    <a href="{{ route('guidance-documents.view', $doc->id) }}" 
                                       target="_blank"
                                       class="flex-1 py-2.5 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5 shadow-2xs hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
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
            <div class="bg-white dark:bg-slate-800/90 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 p-12 sm:p-16 text-center shadow-xs">
                <div class="w-16 h-16 rounded-3xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-xs">
                    📂
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Belum Ada Dokumen Panduan</h3>
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
                
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200/80 dark:border-slate-700 w-full max-w-lg overflow-hidden"
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
                        <div class="p-5 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-2xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center text-lg font-bold shadow-2xs">
                                    📑
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100" x-text="isEditing ? 'Edit Dokumen Panduan' : 'Unggah Dokumen Panduan Baru'"></h3>
                                    <p class="text-[11px] text-slate-400">Berkas akan tersimpan aman dan dapat diunduh mahasiswa & dosen</p>
                                </div>
                            </div>
                            <button @click="showModal = false" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 text-lg font-bold transition-colors cursor-pointer">&times;</button>
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
                                       class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-semibold">
                            </div>

                            <!-- Kategori Dokumen -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Kategori Dokumen <span class="text-rose-500">*</span>
                                </label>
                                <select name="category" 
                                        x-model="formData.category" 
                                        required
                                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-semibold">
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
                                          class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"></textarea>
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
                                       class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 dark:file:bg-orange-950/60 dark:file:text-orange-300 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl p-1.5 bg-slate-50 dark:bg-slate-900/60">
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
                        <div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-end gap-2.5">
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

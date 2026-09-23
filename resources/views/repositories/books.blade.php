<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Katalog Pustaka', 'route' => route('repositories.index')],
                ['label' => 'Buku & E-Book (Open Access)', 'route' => null]
            ]" />
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="academicBooksApp()">
        @include('repositories.partials.tabs')

        <!-- HERO BANNER & SEARCH SECTION -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-6 w-full">
            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-950/60 border border-orange-200 dark:border-orange-800/80 text-orange-700 dark:text-orange-300 text-xs font-bold">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Pustaka Buku Digital & E-Book Sistem Informasi</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 dark:text-white">
                    Katalog Buku Teks & E-Book <span class="text-orange-600 dark:text-orange-400 font-black">Sistem Informasi</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-4xl">
                    Koleksi buku teks akademik, monograf ilmiah, dan buku referensi komprehensif untuk mahasiswa dan dosen FASILKOM. Terhubung langsung dengan <strong>Open Library (Internet Archive)</strong>, <strong>DOAB (Directory of Open Access Books)</strong>, dan <strong>Google Books</strong> untuk membaca online secara gratis serta sitasi siap pakai.
                </p>
            </div>

            <!-- TOPIK KURASI SISTEM INFORMASI (8 PILAR) -->
            <div class="space-y-2.5 pt-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Pilih Pilar Topik Sistem Informasi:
                    </span>
                    <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 hidden sm:inline">
                        8 Bidang Kurikulum FASILKOM UNSUB
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach($topics as $key => $topItem)
                        @php
                            $isActive = ($topic === $key);
                        @endphp
                        <a href="{{ route('repositories.books', array_merge(request()->query(), ['topic' => $key, 'page' => 1])) }}"
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $isActive ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30 scale-[1.02]' : 'bg-slate-100 dark:bg-slate-900/90 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-slate-200/80 dark:border-slate-700/80' }}">
                            <span>{{ $topItem['icon'] }}</span>
                            <span>{{ $topItem['short_name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- SOURCE & ACCESS SELECTOR -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <!-- Source Tabs -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 shrink-0">Sumber:</span>
                    <div class="inline-flex flex-wrap items-center gap-1.5 p-1 rounded-2xl bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                        <!-- Semua Sumber -->
                        <a href="{{ route('repositories.books', array_merge(request()->query(), ['source' => 'all', 'page' => 1])) }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $source === 'all' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                            Semua Sumber
                        </a>
                        <!-- Open Library -->
                        <a href="{{ route('repositories.books', array_merge(request()->query(), ['source' => 'openlibrary', 'page' => 1])) }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $source === 'openlibrary' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                            Open Library (Internet Archive)
                        </a>
                        <!-- DOAB -->
                        <a href="{{ route('repositories.books', array_merge(request()->query(), ['source' => 'doab', 'page' => 1])) }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $source === 'doab' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                            DOAB (Open Access Books)
                        </a>
                        <!-- Google Books -->
                        <a href="{{ route('repositories.books', array_merge(request()->query(), ['source' => 'googlebooks', 'page' => 1])) }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $source === 'googlebooks' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                            Google Books
                        </a>
                    </div>
                </div>

                <!-- Access Filter -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 shrink-0">Akses Buku:</span>
                    <div class="inline-flex items-center gap-1 p-1 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80 text-xs font-bold">
                        <a href="{{ route('repositories.books', array_merge(request()->query(), ['access' => 'all', 'page' => 1])) }}"
                           class="px-3 py-1 rounded-lg transition-all {{ $access === 'all' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                            Semua Buku
                        </a>
                        <a href="{{ route('repositories.books', array_merge(request()->query(), ['access' => 'free_read', 'page' => 1])) }}"
                           class="px-3 py-1 rounded-lg transition-all flex items-center gap-1.5 {{ $access === 'free_read' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-2xs font-black' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}"
                           title="Hanya buku yang dapat dibaca penuh online atau diunduh PDF gratis">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Bisa Dibaca / Unduh Gratis</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SEARCH FORM -->
            <form action="{{ route('repositories.books') }}" method="GET" class="w-full">
                <div class="flex flex-col sm:flex-row items-stretch gap-3 w-full">
                    <div class="relative flex-1 group min-w-0">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-400 group-focus-within:text-orange-500 dark:group-focus-within:text-orange-400 transition-colors z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               name="q" 
                               id="book-search-input"
                               x-model="searchQuery"
                               placeholder="Cari judul buku teks, pengarang, atau istilah SI (misal: Kenneth Laudon, UML, Enterprise Architecture)..." 
                               style="padding-left: 3.5rem !important; padding-right: 3.25rem !important;"
                               class="block w-full py-3.5 sm:py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 text-sm sm:text-base font-medium shadow-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                        
                        <button type="button" 
                                x-show="searchQuery" 
                                x-cloak
                                @click="searchQuery = ''; @if(!empty($query)) window.location.href = '{{ route('repositories.books', ['topic' => $topic, 'source' => $source, 'access' => $access]) }}'; @else document.getElementById('book-search-input').focus(); @endif"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors cursor-pointer z-10"
                                title="Hapus / Reset pencarian">
                            <div class="w-6 h-6 rounded-full bg-slate-100 hover:bg-rose-100 dark:bg-slate-800 dark:hover:bg-rose-950/60 flex items-center justify-center transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </button>
                    </div>

                    <!-- Hidden inputs to preserve filters -->
                    <input type="hidden" name="topic" value="{{ $topic }}">
                    <input type="hidden" name="source" value="{{ $source }}">
                    <input type="hidden" name="access" value="{{ $access }}">

                    <div class="flex flex-col sm:flex-row items-center gap-2.5 shrink-0">
                        <button type="submit" 
                                class="w-full sm:w-auto px-8 sm:px-10 py-3.5 sm:py-4 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white rounded-2xl text-sm sm:text-base font-bold shadow-md shadow-orange-500/25 transition-all flex items-center justify-center gap-2.5 hover:scale-[1.01] active:scale-95 cursor-pointer shrink-0 whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span class="tracking-wide">Cari Buku</span>
                        </button>

                        @if(!empty($query) || $topic !== 'all_si' || $source !== 'all' || $access !== 'all')
                            <a href="{{ route('repositories.books') }}" 
                               class="w-full sm:w-auto px-5 sm:px-6 py-3.5 sm:py-4 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/80 rounded-2xl text-sm sm:text-base font-bold transition-all flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-95 shrink-0 whitespace-nowrap shadow-xs"
                               title="Reset pencarian dan kembali ke topik utama">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>Reset</span>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- SELECTED TOPIC EXPLANATION BADGE -->
            <div class="p-3.5 rounded-2xl bg-orange-50/70 dark:bg-slate-900/70 border border-orange-200/60 dark:border-slate-700/60 flex items-start gap-3">
                <span class="text-2xl shrink-0 select-none">{{ $selectedTopic['icon'] }}</span>
                <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                    <span class="font-black text-slate-900 dark:text-white">{{ $selectedTopic['name'] }}:</span>
                    <span>{{ $selectedTopic['description'] }}</span>
                    @if(!empty($query))
                        <span class="block mt-1 font-semibold text-orange-600 dark:text-orange-400">
                            🔍 Menampilkan hasil pencarian kata kunci: "<strong>{{ $query }}</strong>"
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- RESULTS SECTION -->
        <div class="space-y-4">
            <!-- RESULTS HEADER & METRICS -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-1">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-black text-slate-800 dark:text-white">
                        Ditemukan {{ $results['count'] }} Koleksi Buku
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400">
                        Halaman {{ $results['current_page'] }} dari {{ max(1, $results['total_pages']) }}
                    </span>
                </div>

                <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Open Access & Free Read</span>
                    </span>
                </div>
            </div>

            <!-- BOOK CARDS GRID -->
            @if(!empty($results['data']) && count($results['data']) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($results['data'] as $book)
                        @php
                            $isBookmarked = in_array($book['identifier'], $bookmarkedIdentifiers);
                            $sourceColor = match($book['source']) {
                                'doab' => 'bg-emerald-500 text-white',
                                'googlebooks' => 'bg-amber-500 text-white',
                                default => 'bg-sky-600 text-white'
                            };
                        @endphp
                        <div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 flex flex-col justify-between overflow-hidden hover:shadow-md hover:border-orange-500/50 dark:hover:border-orange-500/50 transition-all duration-200">
                            <!-- TOP SECTION: COVER & BADGES -->
                            <div>
                                <div class="relative w-full h-52 bg-slate-100 dark:bg-slate-900 flex items-center justify-center overflow-hidden border-b border-slate-100 dark:border-slate-700/60">
                                    @if(!empty($book['cover_url']))
                                        <img src="{{ $book['cover_url'] }}" 
                                             alt="{{ $book['title'] }}" 
                                             loading="lazy"
                                             class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-300"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex flex-col items-center justify-center p-4 text-center bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900\'><span class=\'text-3xl mb-1\'>📖</span><span class=\'text-xs font-bold text-slate-600 dark:text-slate-300 line-clamp-2\'>{{ addslashes($book['title']) }}</span></div>';">
                                    @else
                                        <!-- STYLISH FALLBACK BOOK COVER -->
                                        <div class="w-full h-full flex flex-col items-center justify-center p-4 text-center bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 select-none">
                                            <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-950/80 flex items-center justify-center mb-2 shadow-2xs">
                                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                            <span class="text-xs font-black text-slate-700 dark:text-slate-200 line-clamp-2 px-2">
                                                {{ $book['title'] }}
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-1">
                                                {{ $book['authors_string'] }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- SOURCE BADGE (TOP LEFT) -->
                                    <div class="absolute top-2.5 left-2.5">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black tracking-wider uppercase shadow-xs {{ $sourceColor }}">
                                            @if($book['source'] === 'openlibrary')
                                                Open Library
                                            @elseif($book['source'] === 'doab')
                                                DOAB Open Access
                                            @elseif($book['source'] === 'googlebooks')
                                                Google Books
                                            @else
                                                E-Book
                                            @endif
                                        </span>
                                    </div>

                                    <!-- ACCESS BADGE (TOP RIGHT) -->
                                    @if($book['is_free_readable'] || $book['pdf_url'])
                                        <div class="absolute top-2.5 right-2.5">
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-emerald-500 text-white shadow-xs flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span>Free Read</span>
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- BOOK DETAILS CONTENT -->
                                <div class="p-4 sm:p-5 space-y-2.5">
                                    <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400 dark:text-slate-400">
                                        <span>{{ $book['year'] ?: 'Tahun n.d.' }}</span>
                                        <span>•</span>
                                        <span class="truncate" title="{{ $book['publisher'] }}">{{ Str::limit($book['publisher'], 25) }}</span>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="text-sm font-black text-slate-900 dark:text-white leading-snug line-clamp-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors" title="{{ $book['title'] }}">
                                        {{ $book['title'] }}
                                    </h3>

                                    <!-- Authors -->
                                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 line-clamp-1" title="{{ $book['authors_string'] }}">
                                        {{ $book['authors_string'] }}
                                    </p>

                                    <!-- Subjects Tags -->
                                    @if(!empty($book['subjects']))
                                        <div class="flex flex-wrap items-center gap-1 pt-1">
                                            @foreach(array_slice($book['subjects'], 0, 2) as $subj)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 truncate max-w-[130px]">
                                                    #{{ $subj }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Abstract snippet -->
                                    @if(!empty($book['abstract']))
                                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed pt-1">
                                            {{ $book['abstract'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- BOTTOM ACTION BUTTONS -->
                            <div class="p-4 sm:p-5 pt-0 space-y-2.5 border-t border-slate-100 dark:border-slate-700/60 mt-2">
                                <div class="grid grid-cols-2 gap-2 pt-3">
                                    <!-- Read Online Button -->
                                    @if(!empty($book['read_url']))
                                        <a href="{{ $book['read_url'] }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-orange-500 hover:bg-orange-600 text-white shadow-xs transition-all hover:scale-[1.02] active:scale-95 text-center">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <span>Baca Online</span>
                                        </a>
                                    @else
                                        <a href="https://openlibrary.org/search?q={{ urlencode($book['title']) }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition-all text-center">
                                            <span>Cari Arsip</span>
                                        </a>
                                    @endif

                                    <!-- Download PDF Button (if available) or Citation Button -->
                                    @if(!empty($book['pdf_url']))
                                        <a href="{{ $book['pdf_url'] }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-600 text-white shadow-xs transition-all hover:scale-[1.02] active:scale-95 text-center">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            <span>Unduh PDF</span>
                                        </a>
                                    @else
                                        <button type="button" 
                                                @click="openCitationModal(@js($book))"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition-all text-center cursor-pointer">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>Sitasi</span>
                                        </button>
                                    @endif
                                </div>

                                <!-- SECONDARY ACTIONS (Bookmark + Citation) -->
                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <!-- Bookmark Button -->
                                    <button type="button" 
                                            @click="toggleBookmark(@js($book))"
                                            :disabled="isSyncingBookmark['{{ $book['identifier'] }}']"
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-amber-500 dark:text-slate-400 dark:hover:text-amber-400 transition-colors cursor-pointer disabled:opacity-50">
                                        <svg class="w-4 h-4 transition-transform active:scale-125" 
                                             :class="bookmarkedMap['{{ $book['identifier'] }}'] ? 'text-amber-500 fill-amber-500' : 'text-slate-400 dark:text-slate-500 fill-none'" 
                                             stroke="currentColor" 
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                        </svg>
                                        <span x-text="bookmarkedMap['{{ $book['identifier'] }}'] ? 'Tersimpan' : 'Simpan Buku'"></span>
                                    </button>

                                    <!-- Quick Citation Link -->
                                    <button type="button" 
                                            @click="openCitationModal(@js($book))"
                                            class="text-xs font-bold text-slate-500 hover:text-orange-600 dark:text-slate-400 dark:hover:text-orange-400 transition-colors cursor-pointer flex items-center gap-1">
                                        <span>Format Sitasi</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PAGINATION CONTROLS -->
                @if($results['total_pages'] > 1)
                    <div class="flex items-center justify-between gap-4 pt-6">
                        @if($results['current_page'] > 1)
                            <a href="{{ route('repositories.books', array_merge(request()->query(), ['page' => $results['current_page'] - 1])) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                <span>Halaman Sebelumnya</span>
                            </a>
                        @else
                            <div></div>
                        @endif

                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                            Halaman {{ $results['current_page'] }} dari {{ $results['total_pages'] }}
                        </span>

                        @if($results['current_page'] < $results['total_pages'])
                            <a href="{{ route('repositories.books', array_merge(request()->query(), ['page' => $results['current_page'] + 1])) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-500 text-white text-xs sm:text-sm font-bold hover:bg-orange-600 transition-all shadow-sm shadow-orange-500/30">
                                <span>Halaman Selanjutnya</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif
            @else
                <!-- EMPTY STATE -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-10 text-center border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base sm:text-lg font-black text-slate-800 dark:text-white">
                            Tidak Ada Buku yang Cocok
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                            Tidak ditemukan buku teks untuk kata kunci ini. Silakan coba pilih pilar topik Sistem Informasi di atas atau gunakan kata kunci lain seperti <em>"Database"</em>, <em>"Information Systems"</em>, atau <em>"Software Engineering"</em>.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('repositories.books') }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs sm:text-sm font-bold shadow-xs transition-all">
                            <span>Kembali ke Topik Utama SI</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- CITATION MODAL -->
        <div x-show="citationModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="citationModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                     @click="citationModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="citationModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 space-y-5">
                    
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300 text-[10px] font-black uppercase">
                                Sitasi Buku Akademik
                            </div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white line-clamp-2" x-text="activeBook.title"></h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400" x-text="activeBook.authors_string + ' (' + (activeBook.year || 'n.d.') + ')'"></p>
                        </div>
                        <button type="button" 
                                @click="citationModalOpen = false" 
                                class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- FORMAT TABS -->
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
                            <button type="button" 
                                    @click="activeCitationFormat = 'apa'" 
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                                    :class="activeCitationFormat === 'apa' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                                APA (7th Ed.)
                            </button>
                            <button type="button" 
                                    @click="activeCitationFormat = 'ieee'" 
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                                    :class="activeCitationFormat === 'ieee' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                                IEEE
                            </button>
                            <button type="button" 
                                    @click="activeCitationFormat = 'chicago'" 
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                                    :class="activeCitationFormat === 'chicago' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                                Chicago
                            </button>
                            <button type="button" 
                                    @click="activeCitationFormat = 'bibtex'" 
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                                    :class="activeCitationFormat === 'bibtex' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                                BibTeX (@book)
                            </button>
                        </div>

                        <!-- CITATION DISPLAY BOX -->
                        <div class="relative p-4 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80 text-xs sm:text-sm font-mono text-slate-800 dark:text-slate-200 leading-relaxed break-words whitespace-pre-wrap">
                            <span x-text="getCurrentCitationText()"></span>
                        </div>
                    </div>

                    <!-- MODAL ACTIONS -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                        <button type="button" 
                                @click="downloadRis()" 
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 transition-all">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span>Unduh .RIS (Mendeley / Zotero)</span>
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="copyCitation()" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold shadow-xs transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                </svg>
                                <span>Salin Teks Sitasi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FLOATING TOAST NOTIFICATION -->
        <div x-show="copiedToast" 
             x-cloak
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed bottom-5 right-5 z-50 max-w-sm bg-slate-900 text-white text-xs font-bold px-4 py-3 rounded-2xl shadow-xl border border-slate-700 flex items-center gap-2.5">
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            <span x-text="copiedToastMessage"></span>
        </div>
    </div>

    @push('scripts')
    <script>
        function academicBooksApp() {
            return {
                searchQuery: @js($query),
                bookmarkedMap: {
                    @foreach($bookmarkedIdentifiers as $bId)
                        '{{ $bId }}': true,
                    @endforeach
                },
                isSyncingBookmark: {},
                citationModalOpen: false,
                activeCitationFormat: 'apa',
                activeBook: {},
                copiedToast: false,
                copiedToastMessage: '',

                toggleBookmark(book) {
                    const id = book.identifier;
                    if (this.isSyncingBookmark[id]) return;

                    this.isSyncingBookmark[id] = true;
                    const willBookmark = !this.bookmarkedMap[id];
                    this.bookmarkedMap[id] = willBookmark;

                    fetch("{{ route('repositories.bookmarks.toggle') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            journal_identifier: id,
                            title: book.title,
                            authors: book.authors,
                            authors_string: book.authors_string,
                            year: book.year,
                            venue: 'Buku Teks Akademik',
                            publisher: book.publisher,
                            doi: book.doi,
                            url: book.read_url,
                            pdf_url: book.pdf_url,
                            abstract: book.abstract,
                            source: book.source,
                            source_label: book.source_label,
                            citations: book.citations,
                            notes: 'Disimpan dari Katalog Buku Sistem Informasi'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        delete this.isSyncingBookmark[id];
                        if (data.success) {
                            this.copiedToastMessage = data.status === 'removed' 
                                ? 'Buku dihapus dari Daftar Bacaan Anda.' 
                                : 'Buku berhasil disimpan ke Daftar Bacaan Anda!';
                            this.copiedToast = true;
                            setTimeout(() => { this.copiedToast = false; }, 3000);
                        } else {
                            // Revert optimistic update
                            this.bookmarkedMap[id] = !willBookmark;
                        }
                    })
                    .catch(() => {
                        delete this.isSyncingBookmark[id];
                        this.bookmarkedMap[id] = !willBookmark;
                    });
                },

                openCitationModal(book) {
                    this.activeBook = book;
                    this.activeCitationFormat = 'apa';
                    this.citationModalOpen = true;
                },

                getCurrentCitationText() {
                    if (!this.activeBook || !this.activeBook.citations) return '';
                    return this.activeBook.citations[this.activeCitationFormat] || '';
                },

                copyCitation() {
                    const text = this.getCurrentCitationText();
                    if (!text) return;

                    navigator.clipboard.writeText(text).then(() => {
                        this.copiedToastMessage = 'Teks sitasi berhasil disalin ke clipboard!';
                        this.copiedToast = true;
                        setTimeout(() => { this.copiedToast = false; }, 3000);
                    });
                },

                downloadRis() {
                    if (!this.activeBook) return;
                    const b = this.activeBook;
                    const risText = (b.citations && b.citations.ris) ? b.citations.ris : '';
                    if (!risText) return;

                    const blob = new Blob([risText], { type: 'application/x-research-info-systems;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    const cleanTitle = (b.title || 'buku').toLowerCase().replace(/[^a-z0-9]/g, '_').substring(0, 30);
                    link.setAttribute('download', `buku_${cleanTitle}.ris`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    this.copiedToastMessage = 'File .RIS berhasil diunduh untuk Mendeley/Zotero!';
                    this.copiedToast = true;
                    setTimeout(() => { this.copiedToast = false; }, 3000);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Katalog Pustaka', 'route' => route('repositories.index')],
                ['label' => 'Jurnal Ilmiah (Open Access)', 'route' => null]
            ]" />
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="academicJournalApp()">
        @include('repositories.partials.tabs')

        <!-- HERO SEARCH BANNER -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-5 w-full">
            <div class="w-full space-y-4">
                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 dark:text-white">
                        Eksplorasi Jurnal Ilmiah <span class="text-orange-600 dark:text-orange-400 font-black">Open Access</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed w-full">
                        Cari artikel jurnal nasional & internasional langsung dari SIBIMA. Tersedia tautan unduh naskah PDF lengkap gratis (*Full-Text*) dan salin sitasi otomatis dalam format APA, IEEE, serta BibTeX untuk skripsi Anda.
                    </p>
                </div>

                <!-- Source Selector Tabs -->
                <div class="pt-2 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 shrink-0">Sumber:</span>
                    <div class="inline-flex flex-wrap items-center gap-2 p-1.5 rounded-2xl bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                        <!-- Semua Sumber -->
                        <a href="{{ route('repositories.journals', array_merge(request()->query(), ['source' => 'all', 'page' => 1])) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $source === 'all' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/25' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0 {{ $source === 'all' ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>Semua Sumber</span>
                        </a>

                        <!-- Jurnal GLOBAL FASILKOM UNSUB -->
                        <a href="{{ route('repositories.journals', array_merge(request()->query(), ['source' => 'fasilkom', 'page' => 1])) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $source === 'fasilkom' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/25' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0 {{ $source === 'fasilkom' ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span>Jurnal GLOBAL FASILKOM</span>
                            <span class="px-2 py-0.5 rounded-lg text-[11px] font-black {{ $source === 'fasilkom' ? 'bg-white/25 text-white' : 'bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60' }}">
                                {{ $fasilkomTotalCount }}
                            </span>
                        </a>

                        <!-- Jurnal Nasional GARUDA (SINTA) -->
                        <a href="{{ route('repositories.journals', array_merge(request()->query(), ['source' => 'garuda', 'page' => 1])) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $source === 'garuda' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/25' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0 {{ $source === 'garuda' ? 'text-white' : 'text-rose-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span>Jurnal Nasional (GARUDA / SINTA)</span>
                        </a>

                        <!-- DOAJ (Directory of Open Access Journals) -->
                        <a href="{{ route('repositories.journals', array_merge(request()->query(), ['source' => 'doaj', 'page' => 1])) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $source === 'doaj' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/25' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0 {{ $source === 'doaj' ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span>DOAJ (Open Access)</span>
                        </a>

                        <!-- Crossref (DOI Official Registry) -->
                        <a href="{{ route('repositories.journals', array_merge(request()->query(), ['source' => 'crossref', 'page' => 1])) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $source === 'crossref' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/25' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0 {{ $source === 'crossref' ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <span>Crossref (DOI Registry)</span>
                        </a>

                        <!-- OpenAlex Global -->
                        <a href="{{ route('repositories.journals', array_merge(request()->query(), ['source' => 'openalex', 'page' => 1])) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $source === 'openalex' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/25' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0 {{ $source === 'openalex' ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                            <span>Academic Global</span>
                        </a>

                        <!-- Google Scholar (Pintasan Langsung) -->
                        <a :href="searchQuery ? 'https://scholar.google.com/scholar?q=' + encodeURIComponent(searchQuery) : 'https://scholar.google.com'"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap text-blue-600 dark:text-blue-400 bg-blue-50/80 dark:bg-blue-950/50 hover:bg-blue-100 dark:hover:bg-blue-900/60 border border-blue-200/80 dark:border-blue-800/80 shadow-2xs"
                           title="Buka pencarian di Google Scholar (Tab Baru)">
                            <svg class="w-4 h-4 shrink-0 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 24a7 7 0 1 1 0-14 7 7 0 0 1 0 14zm0-24L0 9.5l4.838 3.94A8 8 0 0 1 12 10a8 8 0 0 1 7.162 3.44L24 9.5z"/>
                            </svg>
                            <span>Google Scholar</span>
                            <svg class="w-3 h-3 text-blue-500 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Search Input Box -->
                <form action="{{ route('repositories.journals') }}" method="GET" class="pt-1 w-full">
                    <div class="flex flex-col sm:flex-row items-stretch gap-3 w-full">
                        <div class="relative flex-1 group min-w-0">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 dark:text-slate-400 group-focus-within:text-orange-500 dark:group-focus-within:text-orange-400 transition-colors z-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   name="q" 
                                   id="journal-search-input"
                                   x-model="searchQuery"
                                   placeholder="{{ $source === 'fasilkom' ? 'Cari judul, penulis, atau topik di Jurnal GLOBAL FASILKOM UNSUB...' : ($source === 'garuda' ? 'Cari judul, penulis, atau topik di Jurnal Nasional GARUDA (SINTA)...' : 'Ketik topik, judul, atau kata kunci (cth: Machine Learning, Sistem Informasi, IoT)...') }}"
                                   style="padding-left: 3.5rem !important; padding-right: 3.25rem !important;"
                                   class="block w-full py-3.5 sm:py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 text-sm sm:text-base font-medium shadow-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                            <button type="button" 
                                    x-show="searchQuery" 
                                    x-cloak
                                    aria-label="Hapus pencarian jurnal"
                                    @click="searchQuery = ''; @if(!empty($query)) window.location.href = '{{ route('repositories.journals', ['source' => $source]) }}'; @else document.getElementById('journal-search-input').focus(); @endif"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors cursor-pointer z-10"
                                    title="Hapus / Reset pencarian">
                                <div class="w-6 h-6 rounded-full bg-slate-100 hover:bg-rose-100 dark:bg-slate-800 dark:hover:bg-rose-950/60 flex items-center justify-center transition-colors">
                                    <svg class="w-3.5 h-3.5 text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </button>
                        </div>
                        
                        <!-- Hidden filter fields to preserve filters -->
                        <input type="hidden" name="source" value="{{ $source }}">
                        <input type="hidden" name="year_filter" value="{{ $yearFilter }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="oa_only" value="{{ $openAccessOnly ? '1' : '0' }}">
                        <input type="hidden" name="sinta" value="{{ $sintaFilter }}">
                        <input type="hidden" name="doc_type" value="{{ $docType }}">
                        @if($source === 'fasilkom')
                            <input type="hidden" name="author" value="{{ $author }}">
                        @endif

                        <div class="flex flex-col sm:flex-row items-center gap-2.5 shrink-0">
                            <button type="submit" 
                                    class="w-full sm:w-auto px-8 sm:px-10 py-3.5 sm:py-4 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white rounded-2xl text-sm sm:text-base font-bold shadow-md shadow-orange-500/25 transition-all flex items-center justify-center gap-2.5 hover:scale-[1.01] active:scale-95 cursor-pointer shrink-0 whitespace-nowrap">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <span class="tracking-wide">Cari Jurnal</span>
                            </button>

                            @if(!empty($query) || $yearFilter !== 'all' || $sort !== 'relevance' || !$openAccessOnly || $sintaFilter !== 'all' || $docType !== 'all')
                                <a href="{{ route('repositories.journals', ['source' => $source]) }}" 
                                   class="w-full sm:w-auto px-5 sm:px-6 py-3.5 sm:py-4 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/80 rounded-2xl text-sm sm:text-base font-bold transition-all flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-95 shrink-0 whitespace-nowrap shadow-xs"
                                   title="Reset pencarian dan kembalikan ke awal">
                                    <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>Reset Pencarian</span>
                                </a>
                            @else
                                <button type="button" 
                                        x-show="searchQuery" 
                                        x-cloak
                                        @click="searchQuery = ''; document.getElementById('journal-search-input').focus();"
                                        class="w-full sm:w-auto px-5 sm:px-6 py-3.5 sm:py-4 bg-slate-100 hover:bg-rose-50 dark:bg-slate-800 dark:hover:bg-rose-950/40 text-slate-700 hover:text-rose-600 dark:text-slate-300 dark:hover:text-rose-400 border border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-800/80 rounded-2xl text-sm sm:text-base font-bold transition-all flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-95 shrink-0 whitespace-nowrap shadow-xs cursor-pointer"
                                        title="Reset kata kunci pencarian">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>Reset</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Suggested Topics Chips -->
                <div class="pt-2 flex flex-wrap items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                    <span class="font-bold text-[11px] text-slate-600 dark:text-slate-400 mr-1">Topik Populer:</span>
                    @php
                        $popularTopics = [
                            'Machine Learning', 
                            'Sistem Pendukung Keputusan', 
                            'Internet of Things', 
                            'UI/UX Design', 
                            'Cyber Security', 
                            'Deep Learning', 
                            'Cloud Computing',
                            'Natural Language Processing',
                            'Data Mining'
                        ];
                    @endphp
                    @foreach($popularTopics as $topic)
                        <a href="{{ route('repositories.journals', ['q' => $topic, 'source' => $source, 'year_filter' => $yearFilter, 'sort' => $sort, 'oa_only' => $openAccessOnly ? '1' : '0']) }}"
                           class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-50 dark:bg-slate-900/80 hover:bg-orange-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-white transition-colors border border-slate-200/90 dark:border-slate-700 shadow-2xs">
                            {{ $topic }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SMART AI SKRIPSI RECOMMENDATION BANNER --}}
        @if($userThesis && !empty($thesisRecommendation['query']))
            <div class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-7 shadow-xs">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                    <div class="space-y-2.5 flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-orange-500 text-white shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>Rekomendasi Pintar AI</span>
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300">
                                Judul Skripsi Anda Terdeteksi
                            </span>
                            @if(!empty($userThesis->topic))
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                    Bidang: {{ ucfirst($userThesis->topic) }}
                                </span>
                            @endif
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Judul Penelitian Skripsi:</h4>
                            <p class="text-sm sm:text-base font-bold text-slate-900 dark:text-white line-clamp-2 mt-0.5">
                                "{{ $userThesis->display_title }}"
                            </p>
                        </div>

                        <!-- Extracted Keyword Chips -->
                        @if(!empty($thesisRecommendation['chips']))
                            <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400 mr-1">Kata Kunci Utama:</span>
                                @foreach($thesisRecommendation['chips'] as $chip)
                                    <a href="{{ route('repositories.journals', ['q' => $chip, 'source' => $source, 'year_filter' => $yearFilter, 'sort' => $sort, 'oa_only' => $openAccessOnly ? '1' : '0']) }}"
                                       class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-900 text-orange-600 dark:text-orange-400 hover:bg-orange-500 hover:text-white dark:hover:bg-orange-600 transition-all shadow-2xs">
                                        #{{ $chip }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- 1-Click Recommendation CTA -->
                    <div class="flex flex-col sm:flex-row lg:flex-col items-stretch sm:items-center lg:items-end justify-center gap-2 shrink-0">
                        <a href="{{ route('repositories.journals', ['q' => $thesisRecommendation['query'], 'source' => $source, 'year_filter' => $yearFilter, 'sort' => $sort, 'oa_only' => $openAccessOnly ? '1' : '0']) }}"
                           class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl text-sm font-black bg-orange-600 hover:bg-orange-700 text-white shadow-sm transition-colors whitespace-nowrap cursor-pointer">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Cari Rujukan untuk Skripsiku</span>
                        </a>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 text-center lg:text-right font-medium">
                            1-Klik langsung menemukan jurnal pendukung Bab 2
                        </span>
                    </div>
                </div>
            </div>
        @endif

        @include('repositories.partials.journal-filters')

        @include('repositories.partials.journal-results')

        @include('repositories.partials.journal-citation-modal')
    </div>

    @include('repositories.partials.journal-scripts')
</x-app-layout>

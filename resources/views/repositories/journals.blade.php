<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Katalog Pustaka', 'route' => route('repositories.index')],
                ['label' => 'Jurnal Ilmiah (Open Access)', 'route' => null]
            ]" />
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Academic Index (250M+ Karya Ilmiah)</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="academicJournalApp()">
        @include('repositories.partials.tabs')

        <!-- HERO SEARCH BANNER (Adaptive Light/Dark Theme) -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-orange-50/70 via-white to-amber-50/50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 p-6 sm:p-8 border border-orange-200/70 dark:border-slate-700/80 shadow-sm dark:shadow-xl space-y-4">
            <!-- Subtle glow accents -->
            <div class="absolute -right-12 -top-12 w-64 h-64 rounded-full bg-orange-500/10 dark:bg-orange-500/5 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-12 -bottom-12 w-64 h-64 rounded-full bg-amber-500/10 dark:bg-amber-500/5 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl space-y-3">
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 dark:text-white">
                    Eksplorasi Jurnal Ilmiah <span class="text-orange-600 dark:text-orange-400 font-black">Open Access</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    Cari artikel jurnal nasional & internasional langsung dari SIBIMA. Tersedia tautan unduh naskah PDF lengkap gratis (*Full-Text*) dan salin sitasi otomatis dalam format APA, IEEE, serta BibTeX untuk skripsi Anda.
                </p>

                <!-- Search Input Box -->
                <form action="{{ route('repositories.journals') }}" method="GET" class="pt-2">
                    <div class="flex flex-col sm:flex-row items-stretch gap-3 w-full">
                        <div class="relative flex-1 group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-400 group-focus-within:text-orange-500 dark:group-focus-within:text-orange-400 transition-colors z-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   name="q" 
                                   id="journal-search-input"
                                   x-model="searchQuery"
                                   placeholder="Ketik topik, judul, atau kata kunci (cth: Machine Learning, Sistem Informasi, IoT)..."
                                   style="padding-left: 3.5rem !important; padding-right: 3.25rem !important;"
                                   class="block w-full py-3.5 sm:py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 text-sm sm:text-base font-medium shadow-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                            <button type="button" 
                                    x-show="searchQuery" 
                                    x-cloak
                                    @click="searchQuery = ''; document.getElementById('journal-search-input').focus();"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer z-10"
                                    title="Hapus pencarian">
                                <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors">
                                    <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </button>
                        </div>
                        
                        <!-- Hidden filter fields to preserve filters -->
                        <input type="hidden" name="year_filter" value="{{ $yearFilter }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="oa_only" value="{{ $openAccessOnly ? '1' : '0' }}">

                        <button type="submit" 
                                class="w-full sm:w-auto px-8 sm:px-10 py-3.5 sm:py-4 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white rounded-2xl text-sm sm:text-base font-bold shadow-md shadow-orange-500/25 transition-all flex items-center justify-center gap-3 hover:scale-[1.01] active:scale-95 cursor-pointer shrink-0 whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <span class="tracking-wide">Cari Jurnal</span>
                        </button>
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
                        <a href="{{ route('repositories.journals', ['q' => $topic, 'year_filter' => $yearFilter, 'sort' => $sort, 'oa_only' => $openAccessOnly ? '1' : '0']) }}"
                           class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-white/90 dark:bg-slate-800/90 hover:bg-orange-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-white transition-colors border border-slate-200/90 dark:border-slate-700 shadow-2xs">
                            {{ $topic }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- FILTER & SORT CONTROLS BAR -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
            <form action="{{ route('repositories.journals') }}" method="GET" id="journalFilterForm" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <input type="hidden" name="q" value="{{ $query }}">

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Year Filter -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Rentang Tahun:</span>
                        <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-900 p-1 border border-slate-200 dark:border-slate-700 text-xs">
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['year_filter' => 'all', 'page' => 1])) }}"
                               class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $yearFilter === 'all' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800' }}">
                                Semua
                            </a>
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['year_filter' => '3_years', 'page' => 1])) }}"
                               class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $yearFilter === '3_years' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800' }}">
                                3 Thn Terakhir
                            </a>
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['year_filter' => '5_years', 'page' => 1])) }}"
                               class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $yearFilter === '5_years' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800' }}">
                                5 Thn Terakhir
                            </a>
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['year_filter' => '10_years', 'page' => 1])) }}"
                               class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $yearFilter === '10_years' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800' }}">
                                10 Thn Terakhir
                            </a>
                        </div>
                    </div>

                    <!-- Sort Filter -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Urutkan:</span>
                        <select name="sort" 
                                onchange="document.getElementById('journalFilterForm').submit()"
                                class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-1.5 pl-3 pr-8 focus:ring-orange-500 focus:border-orange-500">
                            <option value="relevance" {{ $sort === 'relevance' ? 'selected' : '' }}>Paling Relevan</option>
                            <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Publikasi Terbaru</option>
                            <option value="cited" {{ $sort === 'cited' ? 'selected' : '' }}>Sitasi Terbanyak</option>
                        </select>
                    </div>
                </div>

                <!-- Open Access Indicator / Toggle -->
                <div class="flex items-center gap-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-200">
                        <input type="checkbox" 
                               name="oa_only" 
                               value="1" 
                               {{ $openAccessOnly ? 'checked' : '' }} 
                               onchange="document.getElementById('journalFilterForm').submit()"
                               class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-900 text-orange-600 focus:ring-orange-500 w-4 h-4">
                        <span>Hanya Open Access (PDF Langsung)</span>
                    </label>
                </div>
            </form>
        </div>

        <!-- SEARCH RESULTS / CONTENT AREA -->
        @if(!empty($results['error']))
            <!-- Error State -->
            <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50 rounded-2xl p-6 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h4 class="text-sm font-bold text-rose-800 dark:text-rose-200">{{ $results['error'] }}</h4>
                <p class="text-xs text-rose-600 dark:text-rose-400">Silakan coba lakukan pencarian ulang dengan kata kunci lain atau muat ulang halaman.</p>
                <a href="{{ route('repositories.journals') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold shadow-xs hover:bg-rose-700 transition-colors">
                    Reset Pencarian
                </a>
            </div>
        @elseif(empty($query))
            <!-- Initial Empty State: Guidance for Students -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 sm:p-12 border border-slate-200/80 dark:border-slate-700/80 text-center space-y-6">
                <div class="w-20 h-20 rounded-3xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 border border-orange-200/60 dark:border-orange-800/40 flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="max-w-md mx-auto space-y-2">
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight">
                        Cari & Temukan Referensi Skripsi Berkualitas
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Masukkan kata kunci judul, topik riset, atau metode penelitian yang ingin Anda pelajari pada kotak pencarian di atas untuk memulai pencarian artikel jurnal.
                    </p>
                </div>
            </div>
        @elseif(empty($results['data']))
            <!-- Zero Results Found -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 sm:p-12 border border-slate-200/80 dark:border-slate-700/80 text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-slate-800 dark:text-slate-100">
                    Tidak ditemukan jurnal untuk kata kunci "{{ $query }}"
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    Coba gunakan kata kunci bahasa Inggris atau istilah yang lebih umum (misalnya gunakan "Sentiment Analysis" daripada kalimat panjang).
                </p>
                <div class="pt-2">
                    <a href="{{ route('repositories.journals') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-xl text-xs font-bold shadow-xs hover:bg-orange-700 transition-colors">
                        Reset Kata Kunci
                    </a>
                </div>
            </div>
        @else
            <!-- Results Header Info -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-1">
                <div class="text-xs text-slate-600 dark:text-slate-300">
                    Ditemukan sekitar <span class="font-bold text-slate-900 dark:text-white">{{ number_format($results['count']) }}</span> artikel untuk kata kunci <span class="font-bold text-orange-600 dark:text-orange-400">"{{ $query }}"</span>
                    (Halaman {{ $results['current_page'] }} dari {{ $results['total_pages'] }})
                </div>
                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Tersimpan di Cache Cepat SIBIMA</span>
                </div>
            </div>

            <!-- Results List Grid -->
            <div class="space-y-4">
                @foreach($results['data'] as $index => $item)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:border-orange-300 dark:hover:border-orange-500/50 hover:shadow-lg transition-all space-y-4"
                         x-data="{ showAbstract: false }">
                        
                        <!-- Top Metadata Badges -->
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                @if($item['year'])
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600">
                                        {{ $item['year'] }}
                                    </span>
                                @endif

                                @if($item['is_oa'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                        <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span>Open Access (Gratis)</span>
                                    </span>
                                @endif

                                @if($item['cited_by_count'] > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60" title="{{ $item['cited_by_count'] }} kali dikutip paper lain">
                                        <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        <span>{{ number_format($item['cited_by_count']) }} Sitasi</span>
                                    </span>
                                @endif
                            </div>

                            @if($item['doi'])
                                <a href="{{ $item['doi'] }}" target="_blank" rel="noopener noreferrer" 
                                   class="text-[11px] font-mono text-slate-400 hover:text-orange-500 dark:text-slate-400 dark:hover:text-orange-400 flex items-center gap-1 transition-colors">
                                    <span>DOI: {{ str_replace('https://doi.org/', '', $item['doi']) }}</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            @endif
                        </div>

                        <!-- Paper Title -->
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 leading-snug">
                                <a href="{{ $item['landing_page_url'] ?: ($item['pdf_url'] ?: '#') }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                    {{ $item['title'] }}
                                </a>
                            </h3>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                                <span class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ $item['authors_string'] }}
                                </span>
                                <span>•</span>
                                <span class="italic text-orange-600 dark:text-orange-400 font-medium">
                                    {{ $item['venue'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Abstract Section (Expandable) -->
                        @if(!empty($item['abstract']))
                            <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-900/60 p-3.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                <div :class="showAbstract ? '' : 'line-clamp-3'">
                                    <span class="font-bold text-slate-700 dark:text-slate-200">Abstrak:</span>
                                    {{ $item['abstract'] }}
                                </div>
                                <button type="button" 
                                        @click="showAbstract = !showAbstract" 
                                        class="mt-1.5 text-[11px] font-bold text-orange-600 dark:text-orange-400 hover:underline flex items-center gap-1">
                                    <span x-text="showAbstract ? 'Tutup Abstrak' : 'Baca Selengkapnya...'"></span>
                                </button>
                            </div>
                        @endif

                        <!-- Concepts / Keywords Chips -->
                        @if(!empty($item['concepts']))
                            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mr-1">Topik:</span>
                                @foreach($item['concepts'] as $concept)
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 dark:bg-slate-700/70 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600">
                                        {{ $concept['name'] }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Actions Bar -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Citation Modal Trigger -->
                                <button type="button"
                                        @click="openCitationModal(@js($item))"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-orange-400 dark:hover:border-orange-500 transition-all shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                    </svg>
                                    <span>Kutip / Sitasi</span>
                                </button>

                                <!-- Landing page / publisher link -->
                                @if($item['landing_page_url'])
                                    <a href="{{ $item['landing_page_url'] }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span>Lihat di Penerbit</span>
                                    </a>
                                @endif
                            </div>

                            <!-- PDF Download CTA -->
                            @if($item['pdf_url'])
                                <a href="{{ $item['pdf_url'] }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white transition-all shadow-sm shadow-orange-500/20 hover:scale-[1.02] active:scale-95">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Buka PDF Full-Text</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- PAGINATION CONTROLS -->
            @if($results['total_pages'] > 1)
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-xs">
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        Halaman <span class="font-bold text-slate-800 dark:text-slate-200">{{ $results['current_page'] }}</span> dari <span class="font-bold text-slate-800 dark:text-slate-200">{{ $results['total_pages'] }}</span>
                    </div>

                    <div class="inline-flex items-center gap-1">
                        <!-- Prev Page -->
                        @if($results['current_page'] > 1)
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['page' => $results['current_page'] - 1])) }}" 
                               class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-orange-500 hover:text-white transition-all">
                                &laquo; Sebelumnya
                            </a>
                        @endif

                        <!-- Page numbers snippet -->
                        @php
                            $startPage = max(1, $results['current_page'] - 2);
                            $endPage = min($results['total_pages'], $results['current_page'] + 2);
                        @endphp
                        @for($p = $startPage; $p <= $endPage; $p++)
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['page' => $p])) }}"
                               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $p === $results['current_page'] ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                                {{ $p }}
                            </a>
                        @endfor

                        <!-- Next Page -->
                        @if($results['current_page'] < $results['total_pages'])
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['page' => $results['current_page'] + 1])) }}" 
                               class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-orange-500 hover:text-white transition-all">
                                Selanjutnya &raquo;
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        @endif

        <!-- INTERACTIVE CITATION MODAL -->
        <div x-show="citationModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" 
             aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm transition-opacity" 
                 @click="citationModalOpen = false"></div>

            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-white dark:bg-slate-800 rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200/80 dark:border-slate-700 space-y-5"
                     @click.stop>
                    
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 dark:border-slate-700/80 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-orange-500/10 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Kutip / Sitasi Artikel Ini</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Pilih format sitasi yang diinginkan dan salin ke daftar pustaka skripsi Anda.</p>
                            </div>
                        </div>
                        <button type="button" 
                                @click="citationModalOpen = false" 
                                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Paper Mini Preview -->
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1">
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 line-clamp-2" x-text="activePaper.title"></h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400" x-text="activePaper.authors_string + ' (' + (activePaper.year || '') + ')'"></p>
                    </div>

                    <!-- Format Selector Tabs -->
                    <div class="space-y-3">
                        <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-900 p-1 border border-slate-200 dark:border-slate-700 w-full text-xs">
                            <button type="button" 
                                    @click="activeCitationFormat = 'apa'"
                                    class="flex-1 py-1.5 text-center rounded-lg font-bold transition-all"
                                    :class="activeCitationFormat === 'apa' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                APA 7th Edition
                            </button>
                            <button type="button" 
                                    @click="activeCitationFormat = 'ieee'"
                                    class="flex-1 py-1.5 text-center rounded-lg font-bold transition-all"
                                    :class="activeCitationFormat === 'ieee' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                IEEE Format
                            </button>
                            <button type="button" 
                                    @click="activeCitationFormat = 'bibtex'"
                                    class="flex-1 py-1.5 text-center rounded-lg font-bold transition-all"
                                    :class="activeCitationFormat === 'bibtex' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                                BibTeX
                            </button>
                        </div>

                        <!-- Citation Display Box -->
                        <div class="relative">
                            <div class="p-4 bg-slate-900 dark:bg-slate-950 border border-slate-800 text-slate-100 rounded-2xl font-mono text-xs leading-relaxed max-h-48 overflow-y-auto select-all"
                                 x-text="getCurrentCitationText()">
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1" x-show="copiedToast">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">Sitasi berhasil disalin ke clipboard!</span>
                        </span>
                        <div class="ml-auto flex items-center gap-2">
                            <button type="button" 
                                    @click="citationModalOpen = false" 
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                Tutup
                            </button>
                            <button type="button" 
                                    @click="copyCitation()" 
                                    class="px-5 py-2 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white flex items-center gap-1.5 shadow-sm shadow-orange-500/20 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                <span>Salin Sitasi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function academicJournalApp() {
            return {
                searchQuery: '{{ addslashes($query) }}',
                citationModalOpen: false,
                activeCitationFormat: 'apa',
                copiedToast: false,
                activePaper: {
                    title: '',
                    authors_string: '',
                    year: null,
                    citations: {
                        apa: '',
                        ieee: '',
                        bibtex: ''
                    }
                },

                openCitationModal(paper) {
                    this.activePaper = paper;
                    this.citationModalOpen = true;
                    this.copiedToast = false;
                },

                getCurrentCitationText() {
                    if (!this.activePaper || !this.activePaper.citations) return '';
                    return this.activePaper.citations[this.activeCitationFormat] || '';
                },

                copyCitation() {
                    const text = this.getCurrentCitationText();
                    if (!text) return;

                    navigator.clipboard.writeText(text).then(() => {
                        this.copiedToast = true;
                        setTimeout(() => {
                            this.copiedToast = false;
                        }, 3000);
                    });
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

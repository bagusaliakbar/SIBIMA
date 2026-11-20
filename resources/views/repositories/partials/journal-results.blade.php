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
        @elseif(empty($query) && $source !== 'fasilkom')
            <!-- Initial Empty State: Guidance for Students -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 sm:p-12 border border-slate-200/80 dark:border-slate-700/80 text-center space-y-6">
                <div class="w-20 h-20 rounded-3xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 border border-orange-200/60 dark:border-orange-800/40 flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="max-w-md mx-auto space-y-2">
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight">
                        Cari & Temukan Referensi Skripsi
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
                    @if($source === 'fasilkom')
                        Tidak ditemukan artikel di Jurnal GLOBAL FASILKOM{{ $query ? ' untuk kata kunci "' . $query . '"' : '' }}
                    @elseif($source === 'garuda')
                        Tidak ditemukan artikel di Jurnal Nasional GARUDA (SINTA){{ $query ? ' untuk kata kunci "' . $query . '"' : '' }}
                    @elseif($source === 'doaj')
                        Tidak ditemukan artikel di DOAJ (Open Access){{ $query ? ' untuk kata kunci "' . $query . '"' : '' }}
                    @elseif($source === 'crossref')
                        Tidak ditemukan publikasi di Crossref (DOI Registry){{ $query ? ' untuk kata kunci "' . $query . '"' : '' }}
                    @else
                        Tidak ditemukan jurnal untuk kata kunci "{{ $query }}"
                    @endif
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    @if($source === 'fasilkom')
                        Coba gunakan kata kunci yang lebih umum seperti nama metode atau topik (misal: "SPK", "Android", "Web", "Keamanan").
                    @elseif($source === 'garuda')
                        Coba gunakan kata kunci bahasa Indonesia atau istilah yang lebih umum (misal: "sistem informasi", "klasifikasi", "metode").
                    @elseif($source === 'doaj')
                        Coba gunakan istilah riset dalam bahasa Inggris atau kata kunci yang lebih luas (misal: "machine learning", "deep learning", "iot").
                    @elseif($source === 'crossref')
                        Coba gunakan judul lengkap, nomor DOI resmi, atau kata kunci teknologi internasional dalam bahasa Inggris.
                    @else
                        Coba gunakan kata kunci bahasa Inggris atau istilah yang lebih umum (misalnya gunakan "Sentiment Analysis" daripada kalimat panjang).
                    @endif
                </p>
                <div class="pt-2 flex flex-wrap items-center justify-center gap-2.5">
                    <a href="{{ route('repositories.journals', ['source' => $source]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-orange-500/25 transition-all hover:scale-[1.02] active:scale-95">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Reset Pencarian</span>
                    </a>
                    @if(!empty($query))
                        <a href="https://scholar.google.com/scholar?q={{ urlencode($query) }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-xl text-xs font-bold shadow-xs transition-all hover:scale-[1.02] active:scale-95"
                           title="Cari kata kunci ini langsung di Google Scholar">
                            <svg class="w-4 h-4 shrink-0 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 24a7 7 0 1 1 0-14 7 7 0 0 1 0 14zm0-24L0 9.5l4.838 3.94A8 8 0 0 1 12 10a8 8 0 0 1 7.162 3.44L24 9.5z"/>
                            </svg>
                            <span>Cari "{{ Str::limit($query, 25) }}" di Google Scholar</span>
                            <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        @else
            <!-- Results Header Info -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-1">
                <div class="text-xs text-slate-600 dark:text-slate-300">
                    @if(empty($query) && $source === 'fasilkom')
                        Menampilkan <span class="font-bold text-slate-900 dark:text-white">{{ number_format($results['count']) }}</span> artikel dari <span class="font-bold text-orange-600 dark:text-orange-400">Jurnal GLOBAL FASILKOM UNSUB</span>
                    @elseif($source === 'garuda')
                        Ditemukan sekitar <span class="font-bold text-slate-900 dark:text-white">{{ number_format($results['count']) }}</span> artikel di <span class="font-bold text-rose-600 dark:text-rose-400">Jurnal Nasional GARUDA (SINTA)</span> untuk kata kunci <span class="font-bold text-orange-600 dark:text-orange-400">"{{ $query }}"</span>
                    @elseif($source === 'doaj')
                        Ditemukan sekitar <span class="font-bold text-slate-900 dark:text-white">{{ number_format($results['count']) }}</span> artikel di <span class="font-bold text-emerald-600 dark:text-emerald-400">DOAJ (Open Access)</span> untuk kata kunci <span class="font-bold text-orange-600 dark:text-orange-400">"{{ $query }}"</span>
                    @elseif($source === 'crossref')
                        Ditemukan sekitar <span class="font-bold text-slate-900 dark:text-white">{{ number_format($results['count']) }}</span> publikasi di <span class="font-bold text-indigo-600 dark:text-indigo-400">Crossref (DOI Registry)</span> untuk kata kunci <span class="font-bold text-orange-600 dark:text-orange-400">"{{ $query }}"</span>
                    @else
                        Ditemukan sekitar <span class="font-bold text-slate-900 dark:text-white">{{ number_format($results['count']) }}</span> artikel untuk kata kunci <span class="font-bold text-orange-600 dark:text-orange-400">"{{ $query }}"</span>
                    @endif
                    (Halaman {{ $results['current_page'] }} dari {{ $results['total_pages'] }})
                </div>
                <div class="flex items-center gap-3 text-xs flex-wrap">
                    @if(!empty($query) || $yearFilter !== 'all' || $sort !== 'relevance' || !$openAccessOnly)
                        <a href="{{ route('repositories.journals', ['source' => $source]) }}" 
                           class="inline-flex items-center gap-1.5 font-bold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 hover:underline"
                           title="Reset pencarian dan kembali ke awal">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Reset Pencarian</span>
                        </a>
                        <span class="text-slate-300 dark:text-slate-600">•</span>
                    @endif
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Tersimpan di Cache Cepat SIBIMA</span>
                    </div>
                </div>
            </div>

            <!-- Results List Grid -->
            <div class="space-y-4">
                @foreach($results['data'] as $index => $item)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-all space-y-4"
                         x-data="{ showAbstract: false, showTldr: false }">
                        
                        <!-- Top Metadata Badges -->
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Source Badge -->
                                @if(($item['source'] ?? '') === 'fasilkom')
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-800/70 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <span>Jurnal GLOBAL FASILKOM UNSUB</span>
                                        @if(!empty($item['volume']))
                                            <span class="text-[10px] font-semibold text-amber-700 dark:text-amber-300">Vol. {{ $item['volume'] }}{{ !empty($item['issue']) ? ' No. ' . $item['issue'] : '' }}</span>
                                        @endif
                                    </span>
                                @elseif(($item['source'] ?? '') === 'garuda')
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl text-xs font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200 border border-rose-200 dark:border-rose-800/70 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        <span>Jurnal Nasional GARUDA (SINTA)</span>
                                        @if(!empty($item['publisher']))
                                            <span class="text-[10px] font-semibold text-rose-700 dark:text-rose-300">({{ $item['publisher'] }})</span>
                                        @endif
                                    </span>
                                @elseif(($item['source'] ?? '') === 'doaj')
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800/70 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        <span>DOAJ Open Access</span>
                                    </span>
                                @elseif(($item['source'] ?? '') === 'crossref')
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-800/70 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        <span>Crossref DOI Registry</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                        <span>Academic Global</span>
                                    </span>
                                @endif

                                <!-- SINTA Accreditation Badge -->
                                @if(!empty($item['sinta_label']))
                                    @php
                                        $sRating = strtoupper($item['sinta_rating'] ?? '');
                                        $sintaColor = match($sRating) {
                                            'S1' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/70',
                                            'S2' => 'bg-sky-50 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 border-sky-200 dark:border-sky-800/70',
                                            'S3' => 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800/70',
                                            'S4' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/70',
                                            default => 'bg-purple-50 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border-purple-200 dark:border-purple-800/70',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-black border shadow-2xs {{ $sintaColor }}" title="Peringkat Akreditasi SINTA Kemdiktisaintek RI">
                                        <svg class="w-3 h-3 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span>{{ $item['sinta_label'] }}</span>
                                    </span>
                                @endif

                                <!-- Document Type Badge -->
                                @if(!empty($item['doc_type_label']))
                                    @php
                                        $dType = $item['doc_type'] ?? 'article';
                                        $docTypeBadge = match($dType) {
                                            'review' => [
                                                'class' => 'bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800/70',
                                                'path' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                                            ],
                                            'proceeding' => [
                                                'class' => 'bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800/70',
                                                'path' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                                            ],
                                            default => [
                                                'class' => 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800/70',
                                                'path' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                            ],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-bold border shadow-2xs {{ $docTypeBadge['class'] }}">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $docTypeBadge['path'] }}"></path></svg>
                                        <span>{{ $item['doc_type_label'] }}</span>
                                    </span>
                                @endif

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

                                @if(!empty($item['tldr']['has_tldr']))
                                    <button type="button" 
                                            @click="showTldr = !showTldr"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-bold transition-all border shadow-2xs cursor-pointer"
                                            :class="showTldr 
                                                ? 'bg-orange-500 text-white border-orange-500 shadow-sm shadow-orange-500/25' 
                                                : 'bg-orange-50 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border-orange-200 dark:border-orange-800/80 hover:bg-orange-100 dark:hover:bg-orange-900/60'"
                                            title="Tampilkan ringkasan 3 poin kunci penelitian">
                                        <svg class="w-3.5 h-3.5" :class="showTldr ? 'text-white' : 'text-orange-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        <span x-text="showTldr ? 'Tutup Ringkasan' : 'AI Quick Summary'"></span>
                                    </button>
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
                                <div class="flex items-center justify-between gap-2 mt-2">
                                    <button type="button" 
                                            @click="showAbstract = !showAbstract" 
                                            class="text-[11px] font-bold text-orange-600 dark:text-orange-400 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span x-text="showAbstract ? 'Tutup Abstrak' : 'Baca Selengkapnya...'"></span>
                                    </button>
                                    @if(!empty($item['tldr']['has_tldr']))
                                        <button type="button" 
                                                @click="showTldr = !showTldr" 
                                                class="text-[11px] font-black inline-flex items-center gap-1 text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            <span x-text="showTldr ? 'Tutup Ringkasan AI' : 'Lihat Ringkasan AI (TL;DR)'"></span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- AI Quick Summary (TL;DR) Structured Box -->
                        @if(!empty($item['tldr']['has_tldr']))
                            <div x-show="showTldr" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.99]"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.99]"
                                 class="ai-tldr-container bg-slate-100 dark:bg-slate-900 rounded-2xl p-4 sm:p-5 space-y-3 shadow-2xs border border-transparent dark:border-slate-800/80">
                                
                                <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-800 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-orange-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </span>
                                        <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                                            AI Quick Summary <span class="text-orange-600 dark:text-orange-400 font-bold">(Poin Kunci Penelitian)</span>
                                        </h4>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700/80">
                                        Ekstraksi Instan
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                    <!-- 1. Masalah / Fokus Riset -->
                                    <div class="ai-tldr-card bg-white dark:bg-slate-800 rounded-xl p-3.5 space-y-1.5 shadow-2xs">
                                        <div class="flex items-center gap-1.5 text-orange-600 dark:text-orange-400 font-black text-[11px] uppercase tracking-wide">
                                            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            <span>Masalah / Tujuan</span>
                                        </div>
                                        <p class="text-slate-700 dark:text-slate-200 leading-relaxed text-[11px] sm:text-xs">
                                            {{ $item['tldr']['problem'] }}
                                        </p>
                                    </div>

                                    <!-- 2. Metode / Algoritma -->
                                    <div class="ai-tldr-card bg-white dark:bg-slate-800 rounded-xl p-3.5 space-y-1.5 shadow-2xs">
                                        <div class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400 font-black text-[11px] uppercase tracking-wide">
                                            <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span>Metode / Algoritma</span>
                                        </div>
                                        <p class="text-slate-700 dark:text-slate-200 leading-relaxed text-[11px] sm:text-xs">
                                            {{ $item['tldr']['method'] }}
                                        </p>
                                    </div>

                                    <!-- 3. Hasil Utama -->
                                    <div class="ai-tldr-card bg-white dark:bg-slate-800 rounded-xl p-3.5 space-y-1.5 shadow-2xs">
                                        <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-black text-[11px] uppercase tracking-wide">
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                            <span>Hasil Utama</span>
                                        </div>
                                        <p class="text-slate-700 dark:text-slate-200 leading-relaxed text-[11px] sm:text-xs">
                                            {{ $item['tldr']['result'] }}
                                        </p>
                                    </div>
                                </div>
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
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                                <!-- Citation Modal Trigger -->
                                <button type="button"
                                        @click="openCitationModal(@js($item))"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-orange-400 dark:hover:border-orange-500 transition-all shadow-xs cursor-pointer">
                                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                    </svg>
                                    <span>Kutip / Sitasi</span>
                                </button>

                                <!-- Bookmark / Simpan ke Bacaan Button -->
                                <button type="button" 
                                        @click="toggleBookmark(@js($item))"
                                        :disabled="isSyncingBookmark['{{ $item['id'] }}']"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border shadow-xs cursor-pointer active:scale-95"
                                        :class="isBookmarked('{{ $item['id'] }}') 
                                            ? 'bg-amber-500 hover:bg-amber-600 text-white border-amber-500 shadow-sm shadow-amber-500/25' 
                                            : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-amber-50 dark:hover:bg-slate-700 hover:text-amber-600 dark:hover:text-amber-400 hover:border-amber-300 dark:hover:border-amber-500'"
                                        :title="isBookmarked('{{ $item['id'] }}') ? 'Hapus dari Daftar Bacaan' : 'Simpan artikel ini ke Daftar Bacaan Saya'">
                                    <svg class="w-4 h-4 shrink-0 transition-transform" 
                                         :class="isSyncingBookmark['{{ $item['id'] }}'] ? 'animate-spin' : ''"
                                         :fill="isBookmarked('{{ $item['id'] }}') ? 'currentColor' : 'none'" 
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                    </svg>
                                    <span x-text="isBookmarked('{{ $item['id'] }}') ? 'Tersimpan' : 'Simpan Bacaan'">Simpan Bacaan</span>
                                </button>

                                <!-- Cari Artikel Serupa (Similar Papers) -->
                                @if(!empty($item['similar_query']))
                                    <a href="{{ route('repositories.journals', ['q' => $item['similar_query'], 'source' => $source, 'year_filter' => $yearFilter, 'sort' => $sort, 'oa_only' => $openAccessOnly ? '1' : '0']) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-orange-50 dark:hover:bg-slate-700 hover:text-orange-600 dark:hover:text-orange-400 hover:border-orange-300 dark:hover:border-orange-500 transition-all shadow-xs cursor-pointer"
                                       title="Temukan artikel lain dengan metodologi atau topik serupa">
                                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <span>Artikel Serupa</span>
                                    </a>
                                @endif

                                <!-- Landing page / publisher link -->
                                @if($item['landing_page_url'])
                                    <a href="{{ $item['landing_page_url'] }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white transition-all shadow-2xs">
                                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span>{{ ($item['source'] ?? '') === 'fasilkom' ? 'Buka di OJS FASILKOM' : (($item['source'] ?? '') === 'garuda' ? 'Detail di Portal GARUDA' : (($item['source'] ?? '') === 'doaj' ? 'Detail di DOAJ' : (($item['source'] ?? '') === 'crossref' ? 'Detail via DOI' : 'Lihat di Penerbit'))) }}</span>
                                    </a>
                                @endif

                                @if(!empty($item['source_url']) && ($item['source'] ?? '') === 'garuda')
                                    <a href="{{ $item['source_url'] }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white transition-all shadow-2xs">
                                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        <span>Sumber Asli Kampus</span>
                                    </a>
                                @endif

                                <!-- Cek di Google Scholar -->
                                <a href="https://scholar.google.com/scholar?q={{ urlencode($item['title']) }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 hover:bg-blue-50 dark:hover:bg-blue-950/40 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-200 dark:hover:border-blue-800 transition-all shadow-2xs"
                                   title="Cek sitasi dan artikel terkait di Google Scholar (Tab Baru)">
                                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 24a7 7 0 1 1 0-14 7 7 0 0 1 0 14zm0-24L0 9.5l4.838 3.94A8 8 0 0 1 12 10a8 8 0 0 1 7.162 3.44L24 9.5z"/>
                                    </svg>
                                    <span>Google Scholar</span>
                                    <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>

                            <!-- PDF Download CTA -->
                            @if($item['pdf_url'])
                                <a href="{{ $item['pdf_url'] }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white transition-all shadow-sm shadow-orange-500/20 hover:scale-[1.02] active:scale-95">
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
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

        <!-- FILTER & SORT CONTROLS BAR -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 shadow-sm space-y-4">
            <form action="{{ route('repositories.journals') }}" method="GET" id="journalFilterForm" class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                <input type="hidden" name="q" value="{{ $query }}">
                <input type="hidden" name="source" value="{{ $source }}">
                @if($source === 'fasilkom')
                    <input type="hidden" name="author" id="authorHiddenFilter" value="{{ $author }}">
                @endif

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Year Filter -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300 whitespace-nowrap">Tahun:</span>
                        <div class="inline-flex items-center gap-1 rounded-xl bg-slate-100 dark:bg-slate-900 p-1 border border-slate-200 dark:border-slate-700 text-xs">
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['year_filter' => 'all', 'page' => 1])) }}"
                               class="px-2.5 py-1.5 rounded-lg font-bold transition-all whitespace-nowrap {{ $yearFilter === 'all' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800' }}">
                                Semua
                            </a>
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['year_filter' => '3_years', 'page' => 1])) }}"
                               class="px-2.5 py-1.5 rounded-lg font-bold transition-all whitespace-nowrap {{ $yearFilter === '3_years' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800' }}">
                                3 Thn
                            </a>
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['year_filter' => '5_years', 'page' => 1])) }}"
                               class="px-2.5 py-1.5 rounded-lg font-bold transition-all whitespace-nowrap {{ $yearFilter === '5_years' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800' }}">
                                5 Thn
                            </a>
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['year_filter' => '10_years', 'page' => 1])) }}"
                               class="px-2.5 py-1.5 rounded-lg font-bold transition-all whitespace-nowrap {{ $yearFilter === '10_years' ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/30' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800' }}">
                                10 Thn
                            </a>
                        </div>
                    </div>

                    <!-- Filter Akreditasi SINTA (Untuk GARUDA, FASILKOM, atau SEMUA) -->
                    @if(in_array($source, ['garuda', 'fasilkom', 'all']))
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300 whitespace-nowrap flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Akreditasi SINTA:
                        </span>
                        <select name="sinta" 
                                onchange="document.getElementById('journalFilterForm').submit()"
                                class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-1.5 pl-3 pr-8 focus:ring-orange-500 focus:border-orange-500 {{ $sintaFilter !== 'all' ? 'ring-2 ring-orange-500/40 text-orange-600 dark:text-orange-400 font-black' : '' }}">
                            <option value="all" {{ $sintaFilter === 'all' ? 'selected' : '' }}>Semua Akreditasi</option>
                            <option value="s2_s4" {{ $sintaFilter === 's2_s4' ? 'selected' : '' }}>SINTA 2 – SINTA 4</option>
                            <option value="s1_s2" {{ $sintaFilter === 's1_s2' ? 'selected' : '' }}>SINTA 1 – SINTA 2</option>
                            <option value="s1" {{ $sintaFilter === 's1' ? 'selected' : '' }}>SINTA 1</option>
                            <option value="s2" {{ $sintaFilter === 's2' ? 'selected' : '' }}>SINTA 2</option>
                            <option value="s3" {{ $sintaFilter === 's3' ? 'selected' : '' }}>SINTA 3</option>
                            <option value="s4" {{ $sintaFilter === 's4' ? 'selected' : '' }}>SINTA 4</option>
                            <option value="s5_s6" {{ $sintaFilter === 's5_s6' ? 'selected' : '' }}>SINTA 5 – SINTA 6</option>
                        </select>
                    </div>
                    @endif

                    <!-- Filter Tipe Naskah (Document Type) -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300 whitespace-nowrap flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Tipe:
                        </span>
                        <select name="doc_type" 
                                onchange="document.getElementById('journalFilterForm').submit()"
                                class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-1.5 pl-3 pr-8 focus:ring-orange-500 focus:border-orange-500 {{ $docType !== 'all' ? 'ring-2 ring-orange-500/40 text-orange-600 dark:text-orange-400 font-black' : '' }}">
                            <option value="all" {{ $docType === 'all' ? 'selected' : '' }}>Semua Tipe Naskah</option>
                            <option value="article" {{ $docType === 'article' ? 'selected' : '' }}>Artikel Penelitian</option>
                            <option value="proceeding" {{ $docType === 'proceeding' ? 'selected' : '' }}>Prosiding Konferensi</option>
                            <option value="review" {{ $docType === 'review' ? 'selected' : '' }}>Literature Review</option>
                        </select>
                    </div>

                    <!-- Sort Filter -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300 whitespace-nowrap">Urutkan:</span>
                        <select name="sort" 
                                onchange="document.getElementById('journalFilterForm').submit()"
                                class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-1.5 pl-3 pr-8 focus:ring-orange-500 focus:border-orange-500">
                            <option value="relevance" {{ $sort === 'relevance' ? 'selected' : '' }}>Paling Relevan</option>
                            <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Publikasi Terbaru</option>
                            <option value="cited" {{ $sort === 'cited' ? 'selected' : '' }}>Sitasi Terbanyak</option>
                        </select>
                    </div>
                </div>

                <!-- Open Access Indicator -->
                <div class="flex items-center gap-2 shrink-0">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-200">
                        <input type="checkbox" 
                               name="oa_only" 
                               value="1" 
                               {{ $openAccessOnly ? 'checked' : '' }} 
                               onchange="document.getElementById('journalFilterForm').submit()"
                               class="rounded border-slate-300 dark:border-slate-600 dark:bg-slate-900 text-orange-600 focus:ring-orange-500 w-4 h-4">
                        <span>Hanya Open Access</span>
                    </label>
                </div>
            </form>

            <!-- Active Filters Pills -->
            @if($sintaFilter !== 'all' || $docType !== 'all')
            <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Filter Aktif:</span>
                @if($sintaFilter !== 'all')
                    @php
                        $sintaLabels = [
                            's2_s4' => 'SINTA 2 – SINTA 4 (Syarat Skripsi)',
                            's1_s2' => 'SINTA 1 – SINTA 2 (Top Tier)',
                            's1' => 'SINTA 1',
                            's2' => 'SINTA 2',
                            's3' => 'SINTA 3',
                            's4' => 'SINTA 4',
                            's5_s6' => 'SINTA 5 – SINTA 6',
                        ];
                    @endphp
                    <a href="{{ route('repositories.journals', array_merge(request()->query(), ['sinta' => 'all', 'page' => 1])) }}"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/70 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-colors shadow-2xs"
                       title="Hapus filter akreditasi SINTA">
                        <span>{{ $sintaLabels[$sintaFilter] ?? strtoupper($sintaFilter) }}</span>
                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 hover:text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
                @if($docType !== 'all')
                    @php
                        $docTypeLabels = [
                            'article' => 'Artikel Penelitian',
                            'proceeding' => 'Prosiding Konferensi',
                            'review' => 'Literature Review',
                        ];
                    @endphp
                    <a href="{{ route('repositories.journals', array_merge(request()->query(), ['doc_type' => 'all', 'page' => 1])) }}"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800/70 hover:bg-blue-100 dark:hover:bg-blue-900/60 transition-colors shadow-2xs"
                       title="Hapus filter tipe naskah">
                        <span>{{ $docTypeLabels[$docType] ?? ucfirst($docType) }}</span>
                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 hover:text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
                <a href="{{ route('repositories.journals', array_merge(request()->query(), ['sinta' => 'all', 'doc_type' => 'all', 'page' => 1])) }}"
                   class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                    Reset Filter Lanjutan
                </a>
            </div>
            @endif

            {{-- ===== DOSEN FILTER — hanya untuk sumber Jurnal GLOBAL FASILKOM ===== --}}
            @if($source === 'fasilkom')
            @php
                $dosenList = [
                    'Tepi Peirisal', 'Maya Destriani', 'Tazkia Salsabila Ardan',
                    'Bagus Ali Akbar', 'M. Hairiyanov', 'Santi Purwanti',
                    'Rino Guphita', 'Sofwandi Noor', 'Rakhmayudhi',
                    'Syarif Hidayat', 'Jaja', 'Caca Arif Herdian',
                    'Bambang Tjahjo Utomo', 'Digyo', 'Achmad Syafaat',
                    'Restu Mahardika', 'Rusnedi AG',
                ];
            @endphp
            <div class="border-t border-slate-100 dark:border-slate-700/60 pt-4" id="fasilkom-dosen-filter">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">

                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Label --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 whitespace-nowrap">Filter Dosen:</span>
                        </div>

                        {{-- Dropdown --}}
                        <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                            <button type="button" @click="open = !open" id="dosen-filter-btn"
                                    class="inline-flex items-center justify-between gap-2 min-w-52 px-4 py-2 rounded-xl border text-xs font-bold transition-all
                                           {{ ($author !== 'all' && !empty($author)) ? 'bg-orange-500 hover:bg-orange-600 text-white border-orange-500 shadow-sm shadow-orange-500/25' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-orange-400 dark:hover:border-orange-500' }}">
                                <span class="flex items-center gap-2 truncate">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    @if($author !== 'all' && !empty($author))
                                        {{ $author }}
                                    @else
                                        Semua Dosen
                                    @endif
                                </span>
                                <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div x-show="open" x-cloak @click.outside="open = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.98]"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-1 scale-[0.98]"
                                 class="absolute left-0 top-full mt-1.5 w-72 z-50 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl overflow-hidden py-2 max-h-[75vh] overflow-y-auto overscroll-contain">

                                {{-- Semua Dosen --}}
                                <a href="{{ route('repositories.journals', array_merge(request()->query(), ['author' => 'all', 'page' => 1])) }}"
                                   @click="open = false"
                                   class="flex items-center justify-between px-4 py-2.5 text-xs font-bold transition-colors
                                          {{ ($author === 'all' || empty($author)) ? 'bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>Semua Dosen</span>
                                    </span>
                                    @if($author === 'all' || empty($author))
                                        <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    @endif
                                </a>

                                <div class="my-1 border-t border-slate-100 dark:border-slate-700/60"></div>

                                @foreach($dosenList as $dosen)
                                    <a href="{{ route('repositories.journals', array_merge(request()->query(), ['author' => $dosen, 'page' => 1])) }}"
                                       @click="open = false"
                                       class="flex items-center justify-between px-4 py-2.5 text-xs transition-colors
                                              {{ $author === $dosen ? 'bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 font-bold' : 'font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 shrink-0 {{ $author === $dosen ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            <span>{{ $dosen }}</span>
                                        </span>
                                        @if($author === $dosen)
                                            <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Hapus filter --}}
                        @if($author !== 'all' && !empty($author))
                            <a href="{{ route('repositories.journals', array_merge(request()->query(), ['author' => 'all', 'page' => 1])) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/80 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Hapus Filter
                            </a>
                        @endif
                    </div>

                    {{-- Tombol Sinkronisasi OJS FASILKOM --}}
                    @if(in_array(auth()->user()->role, ['admin', 'kaprodi']))
                    <div x-data="{ syncing: false }" class="shrink-0">
                        <button type="button" 
                                @click="if(!syncing) {
                                    const self = $data;
                                    window.sibimaConfirm({
                                        title: 'Sinkronisasi OJS FASILKOM',
                                        message: 'Mulai sinkronisasi seluruh artikel terbaru dari Portal OJS FASILKOM UNSUB (ejournal.unsub.ac.id)?',
                                        type: 'primary',
                                        confirmText: 'Ya, Sinkronkan',
                                        onConfirm: () => {
                                            self.syncing = true;
                                            fetch('{{ route('repositories.journals.sync-fasilkom') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json'
                                                }
                                            })
                                            .then(r => r.json())
                                            .then(d => {
                                                self.syncing = false;
                                                window.dispatchEvent(new CustomEvent('notify', { 
                                                    detail: { 
                                                        title: d.success ? 'Sinkronisasi Berhasil' : 'Gagal Sinkronisasi', 
                                                        message: d.message, 
                                                        type: d.success ? 'success' : 'error' 
                                                    } 
                                                }));
                                                if (d.success) {
                                                    setTimeout(() => window.location.reload(), 1500);
                                                }
                                            })
                                            .catch(() => {
                                                self.syncing = false;
                                                window.dispatchEvent(new CustomEvent('notify', { 
                                                    detail: { 
                                                        title: 'Kesalahan Jaringan', 
                                                        message: 'Tidak dapat menghubungi server OJS FASILKOM UNSUB.', 
                                                        type: 'error' 
                                                    } 
                                                }));
                                            });
                                        }
                                    });
                                }"
                                :disabled="syncing"
                                class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl text-xs font-bold transition-all border shadow-xs whitespace-nowrap cursor-pointer"
                                :class="syncing ? 'bg-orange-50 dark:bg-orange-950/40 text-orange-400 border-orange-200 cursor-not-allowed' : 'bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border-slate-200 dark:border-slate-700 hover:border-orange-300 dark:hover:border-orange-500'"
                                title="Sinkronkan data artikel terbaru dari OJS FASILKOM UNSUB">
                            <svg class="w-4 h-4 shrink-0 text-orange-500" :class="{ 'animate-spin': syncing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span x-text="syncing ? 'Menyinkronkan OJS...' : 'Sinkronkan Jurnal FASILKOM'">Sinkronkan Jurnal FASILKOM</span>
                        </button>
                    </div>
                    @endif

                </div>
            </div>
            @endif
        </div>

        <!-- ADVANCED FILTER & DISCOVERY CARD -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm space-y-5">
            <form action="{{ route('repositories.index') }}" method="GET" id="filterForm" class="space-y-4.5 sm:space-y-5">
                <!-- Hidden Topic Field -->
                <input type="hidden" name="topic" id="topicInput" value="{{ $topic ?? 'all' }}">

                <!-- Search & Filters Container (Spacious 2-Tier Layout) -->
                <div class="space-y-4.5 sm:space-y-5">
                    <!-- Baris 1: Pencarian Utama (Full Width + Tombol Cari) -->
                    <div x-data="{ q: '{{ addslashes($search) }}' }">
                        <label for="search" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Kata Kunci / Judul / Nama</label>
                        <div class="flex items-center gap-2">
                            <!-- Input Wrapper -->
                            <div class="relative flex-1 min-w-0">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       id="search" 
                                       x-model="q"
                                       placeholder="Cari judul skripsi, topik penelitian, nama mahasiswa, NPM, atau abstrak..." 
                                       class="w-full h-10 pl-10 pr-10 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 shadow-2xs">
                                <button type="button" 
                                        x-show="q" 
                                        x-cloak
                                        aria-label="Hapus pencarian arsip"
                                        @click="q = ''; document.getElementById('search').value = ''; document.getElementById('filterForm').submit();"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors cursor-pointer"
                                        title="Reset kata kunci pencarian">
                                    <div class="w-4 h-4 rounded-full bg-slate-200 hover:bg-rose-100 dark:bg-slate-700 dark:hover:bg-rose-950/60 flex items-center justify-center text-slate-500 hover:text-rose-600 dark:text-slate-300 dark:hover:text-rose-300 transition-colors">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                </button>
                            </div>

                            <!-- Tombol Cari -->
                            <button type="submit" 
                                    class="h-10 px-4 sm:px-5 bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm shadow-orange-500/25 transition-all flex items-center justify-center gap-1.5 cursor-pointer hover:scale-[1.01] active:scale-95 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <span>Cari</span>
                            </button>
                        </div>
                    </div>

                    <!-- Baris 2: Dropdowns Filter (Tahun Angkatan & Dosen Pembimbing) -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 sm:gap-4 items-end">
                        <!-- Angkatan Dropdown -->
                        <div class="sm:col-span-5 lg:col-span-4 min-w-0">
                            <label for="year" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Tahun Angkatan</label>
                            <select name="year" id="year" onchange="this.form.submit()" 
                                    class="w-full h-10 pl-3.5 pr-8 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all cursor-pointer shadow-2xs">
                                <option value="" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">Semua Angkatan</option>
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">Angkatan {{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dosen Pembimbing Dropdown -->
                        <div class="sm:col-span-7 lg:col-span-8 min-w-0">
                            <label for="advisor" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Dosen Pembimbing</label>
                            <select name="advisor" id="advisor" onchange="this.form.submit()" 
                                    class="w-full h-10 pl-3.5 pr-8 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all cursor-pointer shadow-2xs">
                                <option value="" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">Semua Dosen Pembimbing</option>
                                @foreach($advisors as $adv)
                                    <option value="{{ $adv }}" {{ $advisor == $adv ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">{{ $adv }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Horizontal Topic Filter Pills -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60">
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 shrink-0 mr-1">Kategori:</span>
                        @foreach($topics as $key => $topData)
                            <button type="button" 
                                    onclick="document.getElementById('topicInput').value='{{ $key }}'; document.getElementById('filterForm').submit();"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 shrink-0 {{ ($topic ?? 'all') === $key ? 'bg-orange-600 text-white shadow-sm shadow-orange-500/30' : 'bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700/80' }}">
                                <span>{{ $topData['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </form>

            <!-- Active Filter Bar, Results Count & View Switcher -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 text-xs">
                <div class="flex items-center gap-2 flex-wrap text-slate-600 dark:text-slate-400">
                    <span class="font-bold">Menampilkan <strong class="text-slate-900 dark:text-white">{{ $repositories->total() }}</strong> dari {{ $totalCount }} pustaka</span>
                    
                    @if($search || $year || $advisor || ($topic && $topic !== 'all'))
                        <span class="text-slate-300 dark:text-slate-600">•</span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @if($search)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 text-[10px] font-bold border border-orange-200 dark:border-orange-800">
                                    "{{ $search }}"
                                </span>
                            @endif
                            @if($year)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold border border-indigo-200 dark:border-indigo-800">
                                    Angkatan {{ $year }}
                                </span>
                            @endif
                            @if($advisor)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold border border-purple-200 dark:border-purple-800">
                                    Pembimbing: {{ $advisor }}
                                </span>
                            @endif
                            @if($topic && $topic !== 'all')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 text-[10px] font-bold border border-amber-200 dark:border-amber-800">
                                    {{ $topics[$topic]['label'] ?? $topic }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    @if($search || $year || $advisor || ($topic && $topic !== 'all'))
                        <a href="{{ route('repositories.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/80 rounded-xl text-xs font-bold transition-all shadow-2xs hover:scale-[1.02] active:scale-95 shrink-0"
                           title="Hapus pencarian dan kembalikan ke seluruh pustaka">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Reset Pencarian</span>
                        </a>
                    @endif

                    <!-- Mode Tampilan Ganda (Grid vs Table) -->
                    <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 rounded-xl shadow-2xs">
                        <button type="button" 
                                @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span>Grid</span>
                        </button>
                        <button type="button" 
                                @click="viewMode = 'table'"
                                :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            <span>Tabel</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

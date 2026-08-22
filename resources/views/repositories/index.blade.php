<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Katalog Pustaka Skripsi', 'route' => null]
            ]" />
            <div class="flex flex-wrap items-center gap-2.5">
                @if(Auth::user()->role !== 'mahasiswa')
                    <!-- Export Segmented Group -->
                    <div class="inline-flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xs divide-x divide-slate-200 dark:divide-slate-700 overflow-hidden">
                        <a href="{{ route('repositories.export-excel', request()->query()) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors" 
                           title="Ekspor Katalog ke Excel Sesuai Filter">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Excel</span>
                        </a>
                        <a href="{{ route('repositories.export-pdf', request()->query()) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors" 
                           title="Ekspor Katalog ke PDF Sesuai Filter">
                            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            <span>PDF</span>
                        </a>
                    </div>
                @endif

                <!-- Instant Similarity Checker (Primary Feature Action) -->
                <button type="button" 
                        @click="$dispatch('toggle-checker')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl shadow-md shadow-orange-500/20 hover:scale-[1.02] active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Cek Kemiripan Judul</span>
                </button>

                @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                    <!-- Migrasi Portal (Secondary Action) -->
                    <button type="button"
                            onclick="startSync()" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all cursor-pointer">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Migrasi Portal</span>
                    </button>

                    <!-- Import Arsip (Secondary Action) -->
                    <a href="{{ route('repositories.import.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span>Import Arsip</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6" 
         x-data="{ 
            showChecker: false,
            testTitle: '',
            isChecking: false,
            checkResults: [],
            hasChecked: false,
            errorMessage: '',

            // Quick Edit Modal State
            editModalOpen: false,
            editData: {
                id: null,
                name: '',
                identifier: '',
                year: '',
                title: '',
                pembimbing1: '',
                pembimbing2: '',
                abstract: ''
            },
            openEditModal(repo) {
                this.editData = {
                    id: repo.id,
                    name: repo.name || '',
                    identifier: repo.identifier || '',
                    year: repo.year || '',
                    title: repo.title || '',
                    pembimbing1: repo.pembimbing1 || '',
                    pembimbing2: repo.pembimbing2 || '',
                    abstract: repo.abstract || ''
                };
                this.editModalOpen = true;
            },

            // Quick Delete Modal State
            deleteModalOpen: false,
            deleteData: {
                id: null,
                name: '',
                title: ''
            },
            openDeleteModal(repo) {
                this.deleteData = {
                    id: repo.id,
                    name: repo.name || '',
                    title: repo.title || ''
                };
                this.deleteModalOpen = true;
            },

            async runTitleCheck() {
                const cleaned = this.testTitle.trim();
                if (!cleaned || cleaned.length < 10) {
                    this.errorMessage = 'Judul terlalu pendek. Masukkan rencana judul minimal 10 karakter (contoh: Sistem Informasi Pengelolaan...).';
                    this.hasChecked = false;
                    this.checkResults = [];
                    return;
                }
                const words = cleaned.split(/\s+/).filter(Boolean);
                if (words.length < 2) {
                    this.errorMessage = 'Judul harus terdiri dari minimal 2 kata yang bermakna.';
                    this.hasChecked = false;
                    this.checkResults = [];
                    return;
                }
                this.errorMessage = '';
                this.isChecking = true;
                this.hasChecked = false;
                try {
                    const res = await fetch('{{ route('theses.check-title') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title: cleaned })
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.errorMessage = data.message || 'Gagal menganalisis judul. Pastikan format judul sesuai.';
                        this.hasChecked = false;
                        this.checkResults = [];
                        return;
                    }
                    this.checkResults = data.similar || [];
                    this.hasChecked = true;
                } catch (err) {
                    this.errorMessage = 'Terjadi kendala jaringan saat menghubungi server.';
                    this.hasChecked = false;
                } finally {
                    this.isChecking = false;
                }
            }
         }"
         @toggle-checker.window="showChecker = !showChecker">

        <!-- INSTANT SIMILARITY CHECKER DRAWER -->
        <div x-show="showChecker" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             x-cloak
             class="bg-gradient-to-br from-orange-500/5 via-white to-amber-500/5 dark:from-slate-900 dark:via-slate-850 dark:to-slate-900 rounded-3xl p-6 border-2 border-orange-200 dark:border-orange-500/30 shadow-xl space-y-4">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-orange-100 dark:border-slate-700/80 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-black shadow-md shadow-orange-500/30 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight">Cek Kemiripan Judul Skripsi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Ketik calon judul proposal Anda untuk mengecek kemiripan dengan repositori alumni & skripsi aktif secara real-time.</p>
                    </div>
                </div>
                <button type="button" @click="showChecker = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="runTitleCheck" class="space-y-2">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1 flex items-center">
                        <input type="text" 
                               x-model="testTitle" 
                               @input="errorMessage = ''; hasChecked = false;"
                               placeholder="Masukkan draf rencana judul skripsi (contoh: Sistem Informasi Pengelolaan Stok Barang...)" 
                               class="w-full pl-4 pr-11 py-3 bg-white dark:bg-slate-900 border border-orange-200 dark:border-slate-700 rounded-2xl text-xs font-semibold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all shadow-inner text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500">
                        <button type="button" 
                                x-show="testTitle" 
                                x-cloak
                                @click="testTitle = ''; checkResults = []; hasChecked = false; errorMessage = '';" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <button type="submit" 
                            :disabled="isChecking || !testTitle.trim()"
                            class="px-6 py-3 bg-orange-600 hover:bg-orange-700 disabled:opacity-50 text-white rounded-2xl text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-orange-500/20 flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                        <template x-if="isChecking">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </template>
                        <span x-text="isChecking ? 'Menganalisis...' : 'Analisis Kemiripan'"></span>
                    </button>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500 px-1">
                    <span>💡 Minimal 10 karakter & 2 kata yang bermakna.</span>
                    <span x-text="testTitle.trim().length + ' karakter'"></span>
                </div>
            </form>

            <!-- Error Validation Notice -->
            <div x-show="errorMessage" x-cloak class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 rounded-2xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-black shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <p class="text-xs font-bold text-rose-700 dark:text-rose-300" x-text="errorMessage"></p>
            </div>

            <!-- Results Display -->
            <div x-show="hasChecked && !errorMessage" x-cloak class="space-y-3 pt-1">
                <template x-if="checkResults.length === 0">
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-black shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-tight">Judul Aman / Orisinal!</h4>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400">Tidak ditemukan judul yang memiliki kemiripan signifikan (&ge;45%) di database pustaka alumni dan skripsi berjalan.</p>
                        </div>
                    </div>
                </template>

                <template x-if="checkResults.length > 0">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Ditemukan <span class="text-orange-600 dark:text-orange-400" x-text="checkResults.length"></span> Judul Serupa Terkait:
                            </p>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500">Batas toleransi kemiripan: &lt; 45%</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <template x-for="(item, idx) in checkResults" :key="idx">
                                <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 shadow-sm space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-lg"
                                              :class="item.percentage >= 70 ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'"
                                              x-text="item.percentage + '% Mirip'"></span>
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500" x-text="item.source + ' (' + item.year + ')'"></span>
                                    </div>
                                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-snug" x-text="item.title"></h5>
                                    <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-700/60">
                                        <span x-text="'Oleh: ' + item.student_name"></span>
                                        <span class="font-mono text-orange-600 dark:text-orange-400 font-bold" x-text="item.matched_words ? item.matched_words.length + ' kata cocok' : ''"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- ADVANCED FILTER & DISCOVERY CARD -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm space-y-5">
            <form action="{{ route('repositories.index') }}" method="GET" id="filterForm" class="space-y-4">
                <!-- Hidden Topic Field -->
                <input type="hidden" name="topic" id="topicInput" value="{{ $topic ?? 'all' }}">

                <!-- Search & Dropdowns Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    <!-- Keyword Search -->
                    <div class="lg:col-span-5 relative">
                        <label for="search" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1.5">Kata Kunci / Judul / Nama</label>
                        <div class="relative flex items-center">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   id="search" 
                                   value="{{ $search }}" 
                                   placeholder="Cari judul, topik, NPM, abstrak..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500">
                        </div>
                    </div>

                    <!-- Angkatan Dropdown -->
                    <div class="lg:col-span-3">
                        <label for="year" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1.5">Tahun Angkatan</label>
                        <select name="year" id="year" onchange="this.form.submit()" 
                                class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all cursor-pointer">
                            <option value="" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">Semua Angkatan</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">Angkatan {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dosen Pembimbing Dropdown -->
                    <div class="lg:col-span-4">
                        <label for="advisor" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1.5">Dosen Pembimbing</label>
                        <select name="advisor" id="advisor" onchange="this.form.submit()" 
                                class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all cursor-pointer">
                            <option value="" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">Semua Dosen Pembimbing</option>
                            @foreach($advisors as $adv)
                                <option value="{{ $adv }}" {{ $advisor == $adv ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">{{ $adv }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Horizontal Topic Filter Pills -->
                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60">
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 shrink-0 mr-1">Kategori:</span>
                        @foreach($topics as $key => $topData)
                            <button type="button" 
                                    onclick="document.getElementById('topicInput').value='{{ $key }}'; document.getElementById('filterForm').submit();"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 shrink-0 {{ ($topic ?? 'all') === $key ? 'bg-orange-600 text-white shadow-sm shadow-orange-500/30' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/60 dark:border-slate-600/60' }}">
                                <span>{{ $topData['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </form>

            <!-- Active Filter Bar & Results Count -->
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

                @if($search || $year || $advisor || ($topic && $topic !== 'all'))
                    <a href="{{ route('repositories.index') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Reset Semua Filter
                    </a>
                @endif
            </div>
        </div>

        <!-- REPOSITORY CARDS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($repositories as $repo)
                @php
                    $badge = $repo->topic_badge;
                @endphp
                <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-200 hover:-translate-y-1 flex flex-col group">
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Header Meta Badges & Admin Actions -->
                            <div class="flex justify-between items-center gap-2 mb-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/80 text-slate-700 dark:text-slate-200 text-[9px] font-black uppercase tracking-widest rounded-lg border border-slate-200/80 dark:border-slate-600">
                                        Angkatan {{ $repo->year }}
                                    </span>

                                    <span class="px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-lg border {{ $badge['bg'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </div>

                                @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                    <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <!-- Quick Edit Button -->
                                        <button type="button" 
                                                @click="openEditModal({{ json_encode($repo) }})"
                                                class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 rounded-lg transition-all"
                                                title="Edit Data Arsip">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <!-- Quick Delete Button -->
                                        <button type="button" 
                                                @click="openDeleteModal({{ json_encode($repo) }})"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-all"
                                                title="Hapus Arsip">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Title -->
                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-2.5 leading-snug group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors line-clamp-3">
                                {{ $repo->title }}
                            </h3>
                            
                            <!-- Abstract Snippet -->
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 line-clamp-3 leading-relaxed">
                                {{ $repo->abstract ?: 'Tidak ada abstrak yang tersedia.' }}
                            </p>
                        </div>
                        
                        <!-- Footer Info -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/80 mt-auto space-y-2.5">
                            <!-- Student -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200/60 dark:border-orange-800/40 flex items-center justify-center text-[10px] font-black shrink-0">
                                        {{ substr($repo->name, 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $repo->name }}</span>
                                </div>
                                <span class="text-[10px] font-mono font-bold text-slate-400 dark:text-slate-500 shrink-0">{{ $repo->identifier ?? '-' }}</span>
                            </div>
                            
                            <!-- Advisors -->
                            @if($repo->pembimbing1 || $repo->pembimbing2)
                                <div class="p-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700/60 rounded-xl space-y-1 text-[10px]">
                                    @if($repo->pembimbing1)
                                        <div class="flex items-start gap-1 text-slate-600 dark:text-slate-300">
                                            <span class="font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tighter shrink-0">P1:</span>
                                            <span class="truncate font-semibold text-slate-700 dark:text-slate-300">{{ $repo->pembimbing1 }}</span>
                                        </div>
                                    @endif
                                    @if($repo->pembimbing2)
                                        <div class="flex items-start gap-1 text-slate-600 dark:text-slate-300">
                                            <span class="font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tighter shrink-0">P2:</span>
                                            <span class="truncate font-semibold text-slate-700 dark:text-slate-300">{{ $repo->pembimbing2 }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white dark:bg-slate-800 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 space-y-3">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-50 dark:bg-orange-950/30 text-orange-500 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Tidak Ada Pustaka Ditemukan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Tidak ada arsip skripsi yang cocok dengan kriteria pencarian dan filter Anda saat ini.</p>
                    <div class="pt-2">
                        <a href="{{ route('repositories.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-xs font-bold rounded-xl hover:bg-orange-700 transition-all shadow-sm">
                            Reset Filter Pencarian
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $repositories->links() }}
        </div>

        @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
            <!-- Modal Edit Arsip Pustaka -->
            <template x-teleport="body">
                <div x-show="editModalOpen" 
                     class="fixed inset-0 overflow-y-auto text-left" 
                     style="z-index: 99999 !important;" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="editModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-200 dark:border-slate-700 relative" 
                             style="z-index: 100000 !important;">
                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Edit Data Arsip Pustaka</h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold mt-0.5">Koreksi judul, nama mahasiswa, atau data pembimbing</p>
                                </div>
                                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold p-1">&times;</button>
                            </div>
                            <form :action="'/repositories/' + editData.id" method="POST" class="p-8 space-y-4">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Nama Mahasiswa -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Nama Mahasiswa <span class="text-rose-500">*</span></label>
                                        <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>
                                    
                                    <!-- NPM / Identifier -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">NPM / Identifier</label>
                                        <input type="text" name="identifier" x-model="editData.identifier" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>
                                </div>

                                <!-- Judul Skripsi -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Judul Skripsi <span class="text-rose-500">*</span></label>
                                    <textarea name="title" x-model="editData.title" required rows="3" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 leading-relaxed"></textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <!-- Tahun Angkatan -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Tahun Angkatan</label>
                                        <input type="number" name="year" x-model="editData.year" placeholder="YYYY" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>

                                    <!-- Pembimbing 1 -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Pembimbing 1</label>
                                        <input type="text" name="pembimbing1" x-model="editData.pembimbing1" placeholder="Nama P1" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>

                                    <!-- Pembimbing 2 -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Pembimbing 2</label>
                                        <input type="text" name="pembimbing2" x-model="editData.pembimbing2" placeholder="Nama P2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>
                                </div>

                                <!-- Abstrak -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Abstrak</label>
                                    <textarea name="abstract" x-model="editData.abstract" rows="4" placeholder="Abstrak skripsi..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-normal text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 leading-relaxed"></textarea>
                                </div>

                                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                                    <button type="button" @click="editModalOpen = false" class="px-5 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:scale-[1.02] transition-all">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Modal Hapus Arsip Pustaka -->
            <template x-teleport="body">
                <div x-show="deleteModalOpen" 
                     class="fixed inset-0 overflow-y-auto text-left" 
                     style="z-index: 99999 !important;" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="deleteModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200 dark:border-slate-700 relative" 
                             style="z-index: 100000 !important;">
                            <div class="p-6">
                                <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </div>
                                <h3 class="text-base font-black text-center text-slate-800 dark:text-slate-100 uppercase tracking-tight">Hapus Arsip Skripsi?</h3>
                                <p class="text-xs text-center text-slate-500 dark:text-slate-400 mt-2">
                                    Apakah Anda yakin ingin menghapus data arsip berikut dari katalog pustaka?
                                </p>
                                <div class="my-4 p-3 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 line-clamp-2" x-text="deleteData.title"></div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-semibold" x-text="'Mahasiswa: ' + deleteData.name"></div>
                                </div>

                                <form :action="'/repositories/' + deleteData.id" method="POST" class="flex items-center justify-end gap-3 pt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl font-bold text-xs shadow-md shadow-rose-500/20 hover:scale-[1.02] transition-all">
                                        Ya, Hapus Arsip
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        @endif
    </div>

<!-- Modal Sync Portal -->
<div id="syncModal" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-md items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-md shadow-2xl border border-slate-100 dark:border-slate-700/60 overflow-hidden transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black shrink-0">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-white uppercase tracking-tight">Migrasi Data Portal</h3>
                    <p class="text-[11px] font-medium text-slate-400">Sinkronisasi Pustaka FASILKOM</p>
                </div>
            </div>
            
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">Sistem sedang menarik data dari website portal FASILKOM (41 Halaman). Mohon jangan tutup jendela ini hingga proses selesai.</p>

            <!-- Progress Bar Track Container -->
            <div class="relative w-full bg-slate-100 dark:bg-slate-700/60 rounded-full h-4 mb-2 overflow-hidden border border-slate-200/80 dark:border-slate-600 shadow-inner">
                <div id="syncProgress" class="bg-gradient-to-r from-orange-500 via-amber-500 to-emerald-500 h-full rounded-full transition-all duration-300 shadow-sm" style="width: 0%;"></div>
            </div>
            
            <div class="flex justify-between items-center text-xs font-bold text-slate-600 dark:text-slate-300 mb-5">
                <span id="syncStatus" class="truncate">Menunggu...</span>
                <span id="syncPercentage" class="shrink-0 ml-2 font-black text-orange-600 dark:text-orange-400">0% (0/41)</span>
            </div>

            <!-- Live Migration Statistics Cards -->
            <div class="grid grid-cols-3 gap-2.5 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Data Baru</p>
                    <p id="statNewCount" class="text-base font-black text-emerald-600 dark:text-emerald-400">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Duplikat</p>
                    <p id="statDupCount" class="text-base font-black text-amber-600 dark:text-amber-400">0</p>
                </div>
                <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 shadow-2xs">
                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Total</p>
                    <p id="statTotalCount" class="text-base font-black text-indigo-600 dark:text-indigo-400">0</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button id="closeSyncModalBtn" onclick="closeSyncModal()" class="hidden bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider hover:bg-orange-600 dark:hover:bg-orange-500 dark:hover:text-white transition-all shadow-sm">Tutup & Muat Ulang</button>
            </div>
        </div>
    </div>
</div>

<script>
    let isSyncing = false;
    const totalPages = 41;
    let currentPage = 1;
    let totalImported = 0;
    let totalNew = 0;
    let totalDuplicates = 0;
    let pageRetryCount = 0;
    const maxRetries = 3;

    async function startSync() {
        if (isSyncing) return;
        
        if (!confirm('Anda yakin ingin memulai migrasi 41 halaman dari portal FASILKOM? Proses ini mungkin memakan waktu beberapa menit.')) {
            return;
        }

        isSyncing = true;
        currentPage = 1;
        totalImported = 0;
        totalNew = 0;
        totalDuplicates = 0;
        pageRetryCount = 0;
        
        document.getElementById('statNewCount').innerText = '0';
        document.getElementById('statDupCount').innerText = '0';
        document.getElementById('statTotalCount').innerText = '0';
        
        const modal = document.getElementById('syncModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('closeSyncModalBtn').classList.add('hidden');
        
        await processNextPage();
    }

    async function processNextPage() {
        if (currentPage > totalPages) {
            finishSync();
            return;
        }

        updateProgressUI(currentPage, totalPages, pageRetryCount > 0 
            ? `Mencoba ulang halaman ${currentPage} (Percobaan ${pageRetryCount+1}/${maxRetries})...` 
            : `Menarik data halaman ${currentPage}...`);

        try {
            const response = await fetch(`/repositories/sync-page/${currentPage}`);
            const result = await response.json();
            
            if (result.success) {
                totalImported += (result.count || 0);
                totalNew += (result.new_count || 0);
                totalDuplicates += (result.duplicate_count || 0);

                document.getElementById('statNewCount').innerText = totalNew;
                document.getElementById('statDupCount').innerText = totalDuplicates;
                document.getElementById('statTotalCount').innerText = totalImported;

                pageRetryCount = 0; // Reset retry counter on success
                currentPage++;
                setTimeout(processNextPage, 300);
            } else {
                throw new Error(result.message || 'Gagal merespons');
            }
        } catch (error) {
            pageRetryCount++;
            if (pageRetryCount < maxRetries) {
                // Retry current page after 1.5s delay
                setTimeout(processNextPage, 1500);
            } else {
                // Skip problematic page after max retries and continue to next page
                console.warn(`Halaman ${currentPage} dilewati setelah ${maxRetries}x percobaan gagal.`);
                pageRetryCount = 0;
                currentPage++;
                setTimeout(processNextPage, 500);
            }
        }
    }

    function updateProgressUI(current, total, statusText) {
        const percentage = Math.round((current / total) * 100);
        const progressBar = document.getElementById('syncProgress');
        if (progressBar) progressBar.style.width = `${percentage}%`;
        
        document.getElementById('syncPercentage').innerText = `${percentage}% (${current}/${total})`;
        document.getElementById('syncStatus').innerText = statusText;
    }

    function handleSyncError(message) {
        document.getElementById('syncStatus').innerText = message;
        document.getElementById('syncStatus').classList.add('text-red-500');
        document.getElementById('closeSyncModalBtn').classList.remove('hidden');
        isSyncing = false;
    }

    function finishSync() {
        const progressBar = document.getElementById('syncProgress');
        if (progressBar) progressBar.style.width = `100%`;
        
        document.getElementById('syncPercentage').innerText = `100% (Selesai)`;
        document.getElementById('syncStatus').innerText = `Sinkronisasi selesai! ${totalNew} data baru, ${totalDuplicates} duplikat di-update.`;
        document.getElementById('syncStatus').classList.add('text-emerald-600', 'dark:text-emerald-400');
        document.getElementById('closeSyncModalBtn').classList.remove('hidden');
        isSyncing = false;
    }

    function closeSyncModal() {
        window.location.reload();
    }
</script>
</x-app-layout>

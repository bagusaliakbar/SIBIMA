<div x-data="{
    searchQuery: '',
    selectedCategory: '{{ $selectedCategory ?? 'all' }}',
    openFaqId: {{ $faqs->first() ? $faqs->first()->id : 'null' }},
    faqsList: [
        @foreach($faqs as $f)
            { id: {{ $f->id }}, q: {{ json_encode($f->question) }}, a: {{ json_encode(strip_tags($f->answer)) }}, c: '{{ $f->category }}' },
        @endforeach
    ],
    toggleFaq(id) {
        this.openFaqId = (this.openFaqId === id) ? null : id;
    },
    matchesSearch(question, answer, category) {
        const matchesCat = (this.selectedCategory === 'all') || (category === this.selectedCategory);
        if (!matchesCat) return false;
        
        if (!this.searchQuery.trim()) return true;
        const q = this.searchQuery.toLowerCase().trim();
        return question.toLowerCase().includes(q) || answer.toLowerCase().includes(q);
    },
    get visibleCount() {
        return this.faqsList.filter(f => this.matchesSearch(f.q, f.a, f.c)).length;
    },
    resetSearch() {
        this.searchQuery = '';
        this.selectedCategory = 'all';
    }
}" class="space-y-8">

    <!-- Hero Search Card (Solid White Card with dark mode support) -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-slate-800 p-6 sm:p-10 border border-slate-200/80 dark:border-slate-700/80 shadow-xs transition-all">
        <!-- Subtle warm ambient lighting -->
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-orange-500/5 dark:bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-indigo-500/5 dark:bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto text-center space-y-4">
            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight text-slate-900 dark:text-white">
                Pusat Bantuan SIBIMA
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed max-w-2xl mx-auto">
                Temukan panduan lengkap alur bimbingan, seminar proposal, sidang skripsi, hingga solusi kendala teknis akun.
            </p>

            <!-- Search Input Box -->
            <div class="pt-2">
                <div class="relative max-w-2xl mx-auto">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" 
                        x-model="searchQuery" 
                        placeholder="Ketik kata kunci pertanyaan (misal: bimbingan, seminar, revisi, acc, berkas)..." 
                        class="block w-full pl-11 pr-10 py-3.5 text-xs sm:text-sm rounded-2xl bg-slate-50 dark:bg-slate-900/60 hover:bg-slate-100/80 dark:hover:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 border border-slate-200 dark:border-slate-700 focus:bg-white dark:focus:bg-slate-900 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/15 transition-all shadow-inner font-medium">
                    <button type="button" 
                        x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer"
                        title="Hapus pencarian"
                        x-cloak>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter Segmented Bar (Rapi & Cantik, No Ugly Scrollbar) -->
    <div class="p-1.5 sm:p-2 bg-slate-100/90 dark:bg-slate-800/90 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs flex flex-wrap items-center gap-1.5 sm:gap-2">
        <!-- Semua Topik Button -->
        <button type="button" 
            @click="selectedCategory = 'all'" 
            :class="selectedCategory === 'all' 
                ? 'bg-orange-600 text-white shadow-sm shadow-orange-600/30 scale-[1.01]' 
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-700/70'"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer select-none">
            <!-- Icon Grid -->
            <svg class="w-3.5 h-3.5 transition-colors" :class="selectedCategory === 'all' ? 'text-white' : 'text-slate-400 dark:text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span>Semua Topik</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full font-mono font-bold transition-colors"
                  :class="selectedCategory === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200/80 dark:bg-slate-700 text-slate-600 dark:text-slate-300'">
                {{ $categoryCounts['all'] ?? $faqs->count() }}
            </span>
        </button>

        @foreach($categories as $catKey => $cat)
            @php
                $count = $categoryCounts[$catKey] ?? 0;
            @endphp
            <button type="button" 
                @click="selectedCategory = '{{ $catKey }}'" 
                :class="selectedCategory === '{{ $catKey }}' 
                    ? 'bg-orange-600 text-white shadow-sm shadow-orange-600/30 scale-[1.01]' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-white/80 dark:hover:bg-slate-700/70'"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer select-none">
                
                @if($catKey === 'bimbingan')
                    <svg class="w-3.5 h-3.5 transition-colors" :class="selectedCategory === '{{ $catKey }}' ? 'text-white' : 'text-slate-400 dark:text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                @elseif($catKey === 'seminar')
                    <svg class="w-3.5 h-3.5 transition-colors" :class="selectedCategory === '{{ $catKey }}' ? 'text-white' : 'text-slate-400 dark:text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                @elseif($catKey === 'sidang')
                    <svg class="w-3.5 h-3.5 transition-colors" :class="selectedCategory === '{{ $catKey }}' ? 'text-white' : 'text-slate-400 dark:text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                @elseif($catKey === 'revisi')
                    <svg class="w-3.5 h-3.5 transition-colors" :class="selectedCategory === '{{ $catKey }}' ? 'text-white' : 'text-slate-400 dark:text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                @elseif($catKey === 'teknis')
                    <svg class="w-3.5 h-3.5 transition-colors" :class="selectedCategory === '{{ $catKey }}' ? 'text-white' : 'text-slate-400 dark:text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                @else
                    <svg class="w-3.5 h-3.5 transition-colors" :class="selectedCategory === '{{ $catKey }}' ? 'text-white' : 'text-slate-400 dark:text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                @endif

                <span>{{ $cat['name'] }}</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-mono font-bold transition-colors"
                      :class="selectedCategory === '{{ $catKey }}' ? 'bg-white/20 text-white' : 'bg-slate-200/80 dark:bg-slate-700 text-slate-600 dark:text-slate-300'">
                    {{ $count }}
                </span>
            </button>
        @endforeach
    </div>

    <!-- Live Status / Result Info Bar -->
    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 px-1 pt-1 pb-1">
        <div class="flex items-center gap-2 flex-wrap">
            <span>Menampilkan <strong class="text-slate-800 dark:text-slate-100 font-bold" x-text="visibleCount"></strong> dari {{ $categoryCounts['all'] ?? $faqs->count() }} pertanyaan</span>
            <span x-show="selectedCategory !== 'all' || searchQuery.trim().length > 0" class="text-slate-300 dark:text-slate-600" x-cloak>•</span>
            <span x-show="searchQuery.trim().length > 0" class="text-orange-600 dark:text-orange-400 font-medium" x-cloak>Kata kunci: "<span x-text="searchQuery"></span>"</span>
        </div>
        <button type="button" 
                x-show="selectedCategory !== 'all' || searchQuery.trim().length > 0" 
                @click="resetSearch()" 
                class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline flex items-center gap-1 cursor-pointer shrink-0"
                x-cloak>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Reset Filter
        </button>
    </div>

    <!-- FAQ Accordion List -->
    <div class="space-y-3">
        @foreach($faqs as $faq)
            @php
                $catColor = $categories[$faq->category]['color'] ?? 'slate';
            @endphp
            <div x-show="matchesSearch({{ json_encode($faq->question) }}, {{ json_encode(strip_tags($faq->answer)) }}, '{{ $faq->category }}')" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden transition-all hover:border-orange-300 dark:hover:border-orange-600/60 hover:shadow-md">
                
                <button type="button" 
                    @click="toggleFaq({{ $faq->id }})" 
                    class="w-full text-left p-5 sm:p-6 flex items-start justify-between gap-4 transition-colors cursor-pointer group">
                    <div class="space-y-1.5 pr-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold tracking-tight
                                {{ $catColor === 'orange' ? 'bg-orange-50 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300 border border-orange-200 dark:border-orange-800' : '' }}
                                {{ $catColor === 'emerald' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : '' }}
                                {{ $catColor === 'indigo' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : '' }}
                                {{ $catColor === 'amber' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : '' }}
                                {{ $catColor === 'blue' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : '' }}
                                {{ $catColor === 'slate' ? 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600' : '' }}">
                                {{ $faq->category_label }}
                            </span>
                            @if($faq->target_role === 'mahasiswa')
                                <span class="text-[10px] text-slate-400 font-semibold">• Khusus Mahasiswa</span>
                            @elseif($faq->target_role === 'dosen')
                                <span class="text-[10px] text-slate-400 font-semibold">• Khusus Dosen</span>
                            @endif
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-100 leading-snug group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                            {{ $faq->question }}
                        </h3>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-700/60 flex items-center justify-center text-slate-400 shrink-0 transition-transform duration-300"
                         :class="{ 'rotate-180 bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400': openFaqId === {{ $faq->id }} }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </button>

                <!-- Accordion Body -->
                <div x-show="openFaqId === {{ $faq->id }}" 
                     x-collapse 
                     x-cloak 
                     class="px-5 pb-6 sm:px-6 sm:pb-6 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed border-t border-slate-100 dark:border-slate-700/60 pt-4 bg-slate-50/50 dark:bg-slate-900/30">
                    <div class="prose prose-sm dark:prose-invert max-w-none space-y-2 text-slate-600 dark:text-slate-300">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Empty Search State -->
        <div x-show="visibleCount === 0" 
             x-cloak
             class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 p-8 sm:p-12 text-center space-y-4 shadow-xs">
            <div class="w-14 h-14 rounded-2xl bg-orange-50 dark:bg-orange-950/40 border border-orange-100 dark:border-orange-900/30 flex items-center justify-center mx-auto text-orange-500">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="text-base font-bold text-slate-800 dark:text-slate-100">Pertanyaan tidak ditemukan</h4>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-1 leading-relaxed">
                    Tidak ada tanya jawab yang sesuai dengan kata kunci atau filter yang Anda pilih. Silakan gunakan kata kunci lain atau reset filter.
                </p>
            </div>
            <button type="button" @click="resetSearch()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-orange-600/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Lihat Semua FAQ
            </button>
        </div>
    </div>

    <!-- Contact Support CTA Card -->
    <div class="rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-white dark:to-slate-800 bg-white dark:bg-slate-800 border border-emerald-200/80 dark:border-emerald-800/40 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6 transition-all">
        <div class="text-left space-y-1">
            <h4 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">Masih punya pertanyaan yang belum terjawab?</h4>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                Hubungi Admin Program Studi atau Dosen Pembimbing Anda untuk bantuan lebih lanjut seputar alur bimbingan dan sidang skripsi.
            </p>
        </div>
        <div class="shrink-0 flex items-center gap-3 w-full sm:w-auto justify-start md:justify-end">
            @auth
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    <a href="{{ route('faqs.manage') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-2xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition-all shadow-xs cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Kelola FAQ
                    </a>
                @endif
            @endauth
            <a href="https://wa.me/?text={{ urlencode('Halo Admin SIBIMA, saya memiliki pertanyaan seputar bimbingan skripsi:') }}" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="inline-flex items-center justify-center px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/25 hover:shadow-emerald-600/40 transition-all hover:scale-105 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4 mr-2 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                <span>WhatsApp Admin</span>
            </a>
        </div>
    </div>
</div>

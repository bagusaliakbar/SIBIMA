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
    setQuickQuery(query, category = null) {
        this.searchQuery = query;
        if (category) this.selectedCategory = category;
    },
    resetSearch() {
        this.searchQuery = '';
        this.selectedCategory = 'all';
    }
}" class="space-y-8">

    <!-- Hero Search Card -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-orange-950 p-6 sm:p-10 text-white shadow-xl border border-slate-800">
        <!-- Glow accents -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-orange-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl mx-auto text-center space-y-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black tracking-widest uppercase bg-orange-500/20 text-orange-300 border border-orange-500/30">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Pusat Bantuan SIBIMA
            </span>
            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white">
                Ada yang bisa kami bantu?
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed max-w-xl mx-auto">
                Temukan panduan lengkap alur bimbingan, seminar proposal, sidang skripsi, hingga solusi kendala teknis akun.
            </p>

            <!-- Search Input Box -->
            <div class="pt-2">
                <div class="relative max-w-xl mx-auto">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" 
                        x-model="searchQuery" 
                        placeholder="Ketik kata kunci pertanyaan (misal: bimbingan, seminar, revisi, acc, berkas)..." 
                        class="block w-full pl-11 pr-10 py-3.5 text-xs sm:text-sm rounded-2xl bg-white/10 hover:bg-white/15 focus:bg-white/20 text-white placeholder-slate-400 border border-white/15 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20 backdrop-blur-md transition-all shadow-inner font-medium">
                    <button type="button" 
                        x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-white transition-colors cursor-pointer"
                        title="Hapus pencarian"
                        x-cloak>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Quick Query Chips -->
                <div class="pt-3 flex items-center justify-center gap-1.5 sm:gap-2 flex-wrap text-xs">
                    <span class="text-slate-400 text-[11px] font-semibold">Pencarian Cepat:</span>
                    <button type="button" @click="setQuickQuery('minimal bimbingan', 'bimbingan')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-orange-500/20 text-slate-200 hover:text-orange-300 text-[11px] font-medium border border-white/10 hover:border-orange-500/30 transition-all cursor-pointer">Bimbingan</button>
                    <button type="button" @click="setQuickQuery('acc seminar', 'seminar')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-orange-500/20 text-slate-200 hover:text-orange-300 text-[11px] font-medium border border-white/10 hover:border-orange-500/30 transition-all cursor-pointer">ACC Seminar</button>
                    <button type="button" @click="setQuickQuery('sidang', 'sidang')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-orange-500/20 text-slate-200 hover:text-orange-300 text-[11px] font-medium border border-white/10 hover:border-orange-500/30 transition-all cursor-pointer">Sidang Akhir</button>
                    <button type="button" @click="setQuickQuery('revisi', 'revisi')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-orange-500/20 text-slate-200 hover:text-orange-300 text-[11px] font-medium border border-white/10 hover:border-orange-500/30 transition-all cursor-pointer">Batas Revisi</button>
                    <button type="button" @click="setQuickQuery('captcha', 'teknis')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-orange-500/20 text-slate-200 hover:text-orange-300 text-[11px] font-medium border border-white/10 hover:border-orange-500/30 transition-all cursor-pointer">Kendala Akun</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none sm:flex-wrap">
        <button type="button" 
            @click="selectedCategory = 'all'" 
            :class="selectedCategory === 'all' 
                ? 'bg-orange-600 text-white shadow-md shadow-orange-600/20 font-bold border-orange-600' 
                : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border-slate-200 dark:border-slate-700'"
            class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all border flex items-center gap-2 shrink-0 cursor-pointer">
            <span>Semua Topik</span>
            <span class="text-[10px] px-2 py-0.5 rounded-md font-mono font-bold"
                  :class="selectedCategory === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'">
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
                    ? 'bg-orange-600 text-white shadow-md shadow-orange-600/20 font-bold border-orange-600' 
                    : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border-slate-200 dark:border-slate-700'"
                class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all border flex items-center gap-2 shrink-0 cursor-pointer">
                <span>{{ $cat['name'] }}</span>
                <span class="text-[10px] px-2 py-0.5 rounded-md font-mono font-bold"
                      :class="selectedCategory === '{{ $catKey }}' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'">
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
    <div class="rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-white dark:to-slate-800 bg-white dark:bg-slate-800 border border-emerald-200/80 dark:border-emerald-800/40 shadow-xs flex flex-col md:flex-row items-center justify-between gap-6 transition-all">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 text-center sm:text-left">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            </div>
            <div>
                <h4 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">Masih punya pertanyaan yang belum terjawab?</h4>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xl leading-relaxed">
                    Hubungi Admin Program Studi atau Dosen Pembimbing Anda untuk bantuan lebih lanjut seputar alur bimbingan dan sidang skripsi.
                </p>
            </div>
        </div>
        <div class="shrink-0 flex items-center gap-3 w-full sm:w-auto justify-center">
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

<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Daftar Pengajuan Skripsi', 'route' => 'theses.index'],
            ['label' => 'Pusat Validasi & Data Clean Pengajuan', 'route' => null]
        ]" />
    </x-slot>

    <script>
        window.sibimaPendingSubmissions = @json($cleanDataCollection ?? []);
    </script>

    <div class="w-full space-y-6 pb-12" x-data="{
        cleanTab: '{{ $filterCategory ?? 'all' }}',
        cleanSearch: '{{ $search ?? '' }}',
        auditModalOpen: false,
        auditData: { student: '', npm: '', title: '', score: 0, matches: [] },
        openAuditModal(data) {
            this.auditData = data;
            this.auditModalOpen = true;
        },
        items: (typeof window.sibimaPendingSubmissions !== 'undefined' ? window.sibimaPendingSubmissions : []),
        get filteredItems() {
            return this.items.filter(item => {
                const matchTab = this.cleanTab === 'all' || item.category === this.cleanTab;
                const matchSearch = !this.cleanSearch || 
                    (item.student_name && item.student_name.toLowerCase().includes(this.cleanSearch.toLowerCase())) ||
                    (item.student_identifier && item.student_identifier.toLowerCase().includes(this.cleanSearch.toLowerCase())) ||
                    (item.title && item.title.toLowerCase().includes(this.cleanSearch.toLowerCase()));
                return matchTab && matchSearch;
            });
        }
    }">

        <!-- Header Hero Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-800 via-teal-800 to-slate-900 text-white p-8 sm:p-10 shadow-xl border border-emerald-700/30">
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-32 -bottom-16 w-64 h-64 bg-teal-400/15 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-[10px] font-black uppercase tracking-widest">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Quality & Fairness Audit Engine
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white uppercase">
                        Pusat Validasi & Data Clean Pengajuan Baru
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed font-medium">
                        Audit otomatis untuk mengevaluasi orisinalitas judul, kesiapan deskripsi abstrak, serta pembagian kuota dosen pembimbing yang adil dan seimbang sebelum disetujui (ACC).
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('theses.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-black uppercase tracking-wider backdrop-blur-md transition-all shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Daftar Skripsi
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Stats Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Pengajuan -->
            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:border-slate-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Total Pengajuan Baru</span>
                    <span class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </span>
                </div>
                <div class="text-3xl font-black text-slate-800 dark:text-slate-100" x-text="items.length">{{ $pendingSummary['total'] }}</div>
                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Menunggu Penugasan</p>
            </div>

            <!-- 100% Bersih -->
            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-900/60 shadow-sm transition-all hover:border-emerald-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">🟢 100% Bersih (Siap ACC)</span>
                    <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </span>
                </div>
                <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400" x-text="items.filter(i => i.category === 'clean').length">{{ $pendingSummary['clean_count'] }}</div>
                <p class="text-[10px] text-emerald-600/80 font-bold uppercase mt-1">Judul Unik & Kuota Tersedia</p>
            </div>

            <!-- Perlu Penyesuaian -->
            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-900/60 shadow-sm transition-all hover:border-amber-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest">🟡 Perlu Penyesuaian</span>
                    <span class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </span>
                </div>
                <div class="text-3xl font-black text-amber-600 dark:text-amber-400" x-text="items.filter(i => i.category === 'warning').length">{{ $pendingSummary['warning_count'] }}</div>
                <p class="text-[10px] text-amber-600/80 font-bold uppercase mt-1">Cek Kuota Dosen / Abstrak</p>
            </div>

            <!-- Kritis -->
            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-rose-200 dark:border-rose-900/60 shadow-sm transition-all hover:border-rose-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest">🔴 Duplikasi Kritis</span>
                    <span class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </span>
                </div>
                <div class="text-3xl font-black text-rose-600 dark:text-rose-400" x-text="items.filter(i => i.category === 'critical').length">{{ $pendingSummary['critical_count'] }}</div>
                <p class="text-[10px] text-rose-600/80 font-bold uppercase mt-1">Kemiripan Tinggi (≥66%)</p>
            </div>
        </div>

        <!-- Filter Tabs & Instant Search -->
        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto">
                <button @click="cleanTab = 'all'" 
                        :class="cleanTab === 'all' ? 'bg-slate-800 text-white dark:bg-slate-100 dark:text-slate-900 shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'" 
                        class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer">
                    Semua (<span x-text="items.length">{{ $pendingSummary['total'] }}</span>)
                </button>
                <button @click="cleanTab = 'clean'" 
                        :class="cleanTab === 'clean' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/25' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100'" 
                        class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer">
                    🟢 Siap ACC (<span x-text="items.filter(i => i.category === 'clean').length">{{ $pendingSummary['clean_count'] }}</span>)
                </button>
                <button @click="cleanTab = 'warning'" 
                        :class="cleanTab === 'warning' ? 'bg-amber-600 text-white shadow-sm shadow-amber-500/25' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 hover:bg-amber-100'" 
                        class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer">
                    🟡 Perhatian (<span x-text="items.filter(i => i.category === 'warning').length">{{ $pendingSummary['warning_count'] }}</span>)
                </button>
                <button @click="cleanTab = 'critical'" 
                        :class="cleanTab === 'critical' ? 'bg-rose-600 text-white shadow-sm shadow-rose-500/25' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100'" 
                        class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer">
                    🔴 Kritis (<span x-text="items.filter(i => i.category === 'critical').length">{{ $pendingSummary['critical_count'] }}</span>)
                </button>
            </div>

            <!-- Search Field -->
            <div class="relative w-full sm:w-80">
                <input type="text" 
                       x-model="cleanSearch" 
                       placeholder="Cari nama mahasiswa, NPM, atau judul..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <!-- List of Clean Proposals Cards -->
        <div class="space-y-4">
            <template x-for="item in filteredItems" :key="item.id">
                <div class="p-6 sm:p-7 rounded-3xl border bg-white dark:bg-slate-900 shadow-sm transition-all hover:shadow-md" 
                     :class="item.category === 'clean' ? 'border-emerald-200 dark:border-emerald-900/60' : (item.category === 'warning' ? 'border-amber-200 dark:border-amber-900/60' : 'border-rose-200 dark:border-rose-900/60')">
                    
                    <!-- Top Bar: Student info & Status Badges -->
                    <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0">
                                <img :src="item.avatar_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(item.student_name)" :alt="item.student_name" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-black text-slate-800 dark:text-slate-100 uppercase text-sm tracking-tight" x-text="item.student_name"></h3>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-400" x-text="item.student_identifier"></span>
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50" x-text="'Angkatan ' + item.entry_year"></span>
                                    <span class="text-[10px] font-bold text-slate-400" x-text="'Diajukan: ' + item.created_at"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <!-- Category Badge -->
                            <span class="px-3.5 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider flex items-center gap-2 border"
                                  :class="item.category === 'clean' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/60' : (item.category === 'warning' ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-900/60' : 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900/60')">
                                <span class="w-2 h-2 rounded-full" :class="item.category === 'clean' ? 'bg-emerald-500' : (item.category === 'warning' ? 'bg-amber-500' : 'bg-rose-500')"></span>
                                <span x-text="item.category_label"></span>
                            </span>

                            <!-- Similarity Score Button (Clickable for Modal Details) -->
                            <button @click="openAuditModal({
                                student: item.student_name,
                                npm: item.student_identifier,
                                title: item.title,
                                score: item.similarity_score,
                                matches: item.similarity_matches
                            })" class="px-3.5 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider border transition-all cursor-pointer hover:scale-105"
                                    :class="item.similarity_score >= 66 ? 'bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100 dark:bg-rose-950/50' : (item.similarity_score >= 35 ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100 dark:bg-amber-950/50' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-950/50')"
                                    title="Klik untuk melihat dokumen dan kata kunci pembanding">
                                <span x-text="item.similarity_score + '% Kemiripan'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Middle Content: Title & Abstract -->
                    <div class="py-5 space-y-4">
                        <div>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Rencana Judul Skripsi</span>
                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase leading-snug tracking-tight" x-text="item.title"></h4>
                        </div>

                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Deskripsi / Rencana Skripsi</span>
                                <span class="text-[10px] font-bold" 
                                      :class="item.word_count >= 50 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'"
                                      x-text="item.word_count + ' kata' + (item.word_count < 50 ? ' (Terlalu Pendek)' : '')"></span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-medium whitespace-pre-line" x-text="item.abstract || 'Tidak ada deskripsi yang dicantumkan.'"></p>
                        </div>

                        <!-- Audit Checklist Highlights -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                            <!-- Strengths -->
                            <template x-if="item.strengths && item.strengths.length > 0">
                                <div class="p-3.5 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 space-y-1.5">
                                    <span class="text-[9px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        Kesesuaian Valid:
                                    </span>
                                    <ul class="text-[11px] text-emerald-800 dark:text-emerald-300 font-medium space-y-1">
                                        <template x-for="st in item.strengths" :key="st">
                                            <li class="flex items-start gap-1.5">
                                                <span class="text-emerald-500 font-black">•</span>
                                                <span x-text="st"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </template>

                            <!-- Issues / Warnings -->
                            <template x-if="item.issues && item.issues.length > 0">
                                <div class="p-3.5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/30 space-y-1.5">
                                    <span class="text-[9px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-widest flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Catatan / Perhatian:
                                    </span>
                                    <ul class="text-[11px] text-amber-800 dark:text-amber-300 font-medium space-y-1">
                                        <template x-for="iss in item.issues" :key="iss">
                                            <li class="flex items-start gap-1.5">
                                                <span class="text-amber-500 font-black">•</span>
                                                <span x-text="iss"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Bottom Section: Supervisors & Direct Assignment Actions -->
                    <div class="pt-5 border-t border-slate-100 dark:border-slate-800 grid grid-cols-1 lg:grid-cols-2 gap-5">
                        
                        <!-- Usulan Mahasiswa -->
                        <div class="space-y-2.5">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Usulan Dosen oleh Mahasiswa:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- Usulan P1 -->
                                <div class="p-3 rounded-2xl border flex flex-col justify-between"
                                     :class="item.req_p1 ? (item.req_p1.is_full ? 'bg-rose-50/50 border-rose-200 dark:bg-rose-950/20' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700') : 'bg-slate-50 dark:bg-slate-800/40 border-dashed border-slate-300'">
                                    <div>
                                        <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest">P1 Usulan</div>
                                        <div class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase truncate mt-0.5" x-text="item.req_p1 ? item.req_p1.name : 'Tidak Memilih'"></div>
                                    </div>
                                    <template x-if="item.req_p1">
                                        <div class="mt-3 flex items-center justify-between text-[10px] font-bold">
                                            <span :class="item.req_p1.is_full ? 'text-rose-600 font-black' : 'text-slate-500'" x-text="'Beban: ' + item.req_p1.workload + '/' + item.req_p1.max_quota"></span>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-extrabold" x-text="item.req_p1.match_score > 0 ? '✨ Match: ' + item.req_p1.match_score : ''"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Usulan P2 -->
                                <div class="p-3 rounded-2xl border flex flex-col justify-between"
                                     :class="item.req_p2 ? (item.req_p2.is_full ? 'bg-rose-50/50 border-rose-200 dark:bg-rose-950/20' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700') : 'bg-slate-50 dark:bg-slate-800/40 border-dashed border-slate-300'">
                                    <div>
                                        <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest">P2 Usulan</div>
                                        <div class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase truncate mt-0.5" x-text="item.req_p2 ? item.req_p2.name : 'Tidak Memilih'"></div>
                                    </div>
                                    <template x-if="item.req_p2">
                                        <div class="mt-3 flex items-center justify-between text-[10px] font-bold">
                                            <span :class="item.req_p2.is_full ? 'text-rose-600 font-black' : 'text-slate-500'" x-text="'Beban: ' + item.req_p2.workload + '/' + item.req_p2.max_quota"></span>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-extrabold" x-text="item.req_p2.match_score > 0 ? '✨ Match: ' + item.req_p2.match_score : ''"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Rekomendasi Pintar & Quick Action -->
                        <div class="space-y-2.5 flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Rekomendasi AI Sesuai Topik:</span>
                                <template x-if="item.recommended_p1">
                                    <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-md">Kuota Tersedia</span>
                                </template>
                            </div>

                            <template x-if="item.recommended_p1">
                                <div class="p-3 bg-indigo-50/50 dark:bg-indigo-950/30 rounded-2xl border border-indigo-100 dark:border-indigo-900/40 text-[11px] space-y-1.5">
                                    <div class="flex items-center justify-between font-extrabold uppercase">
                                        <span class="text-indigo-800 dark:text-indigo-300 truncate">1. <span x-text="item.recommended_p1.name"></span></span>
                                        <span class="text-indigo-600 dark:text-indigo-400 shrink-0 font-mono" x-text="'(' + item.recommended_p1.workload + '/' + item.recommended_p1.max_quota + ')'"></span>
                                    </div>
                                    <template x-if="item.recommended_p2">
                                        <div class="flex items-center justify-between font-extrabold uppercase">
                                            <span class="text-purple-800 dark:text-purple-300 truncate">2. <span x-text="item.recommended_p2.name"></span></span>
                                            <span class="text-purple-600 dark:text-purple-400 shrink-0 font-mono" x-text="'(' + item.recommended_p2.workload + '/' + item.recommended_p2.max_quota + ')'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Action Buttons Form -->
                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <!-- Direct Assign Sesuai Usulan (if valid & not full) -->
                                <template x-if="item.req_p1 && item.req_p2 && !item.req_p1.is_full && !item.req_p2.is_full">
                                    <form :action="'/theses/' + item.id + '/assign'" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="pembimbing1_id" :value="item.req_p1.id">
                                        <input type="hidden" name="pembimbing2_id" :value="item.req_p2.id">
                                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-emerald-500/25 cursor-pointer">
                                            ✓ ACC Sesuai Usulan
                                        </button>
                                    </form>
                                </template>

                                <!-- Direct Assign Sesuai Rekomendasi -->
                                <template x-if="item.recommended_p1 && item.recommended_p2">
                                    <form :action="'/theses/' + item.id + '/assign'" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="pembimbing1_id" :value="item.recommended_p1.id">
                                        <input type="hidden" name="pembimbing2_id" :value="item.recommended_p2.id">
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-indigo-500/25 cursor-pointer">
                                            ✨ ACC Rekomendasi AI
                                        </button>
                                    </form>
                                </template>

                                <!-- View Full Detail Link -->
                                <a :href="'/theses/' + item.id" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-black uppercase tracking-wider transition-all">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </template>

            <!-- Empty State -->
            <template x-if="filteredItems.length === 0">
                <div class="text-center py-16 px-4 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="w-16 h-16 rounded-3xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">Semua Pengajuan Bersih & Terproses</h4>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto">Tidak ada pengajuan mahasiswa baru yang sesuai dengan kriteria filter atau pencarian saat ini.</p>
                    </div>
                </div>
            </template>
        </div>

        <!-- MODAL: DETAIL AUDIT KEMIRIPAN JUDUL -->
        <template x-teleport="body">
            <div x-show="auditModalOpen" 
                 class="fixed inset-0 overflow-y-auto" 
                 style="z-index: 99999 !important;" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity" 
                     @click="auditModalOpen = false" 
                     aria-hidden="true"></div>

                <!-- Centered Modal Box -->
                <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 text-center">
                    <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 relative z-50 my-auto transform transition-all"
                         @click.stop>
                        <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-between items-center">
                            <div>
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Detail Audit Kemiripan Judul</h3>
                                <p class="text-[11px] text-slate-500 font-bold uppercase mt-0.5" x-text="auditData.student + ' (' + auditData.npm + ')'"></p>
                            </div>
                            <button @click="auditModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto">
                            <!-- Checked Title -->
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Judul yang Diuji</span>
                                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 text-xs font-black uppercase leading-relaxed text-slate-800 dark:text-slate-100" x-text="auditData.title"></div>
                            </div>

                            <!-- Overall Score Gauge -->
                            <div class="p-4 rounded-2xl border flex items-center justify-between"
                                 :class="auditData.score >= 66 ? 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/60' : (auditData.score >= 35 ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:border-amber-900/60' : 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-900/60')">
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-widest">Tingkat Kemiripan Maksimal</div>
                                    <div class="text-2xl font-black mt-0.5" x-text="auditData.score + '%'"></div>
                                </div>
                                <div class="text-right text-[11px] font-extrabold uppercase"
                                     x-text="auditData.score >= 66 ? '🔴 Sangat Mirip / Indikasi Duplikasi' : (auditData.score >= 35 ? '🟡 Mirip Moderat' : '🟢 Sangat Unik / Orisinal')"></div>
                            </div>

                            <!-- Matches List -->
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Dokumen Pembanding Terdeteksi:</span>
                                <div class="space-y-3">
                                    <template x-for="(match, idx) in auditData.matches" :key="idx">
                                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50" x-text="match.source"></span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-black"
                                                      :class="match.percentage >= 66 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'"
                                                      x-text="match.percentage + '% Mirip'"></span>
                                            </div>
                                            <div class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase" x-text="match.title"></div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2">
                                                <span x-text="'Penulis: ' + match.author"></span>
                                                <span>•</span>
                                                <span x-text="'Tahun: ' + match.year"></span>
                                            </div>
                                            <template x-if="match.matched_words && match.matched_words.length > 0">
                                                <div class="flex flex-wrap gap-1 pt-1">
                                                    <span class="text-[9px] font-bold text-slate-400">Kata Kunci Cocok:</span>
                                                    <template x-for="word in match.matched_words" :key="word">
                                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-black bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300" x-text="word"></span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!auditData.matches || auditData.matches.length === 0">
                                        <div class="p-6 text-center text-xs font-bold text-slate-400 border border-dashed rounded-2xl">
                                            Tidak ditemukan kemiripan signifikan dengan repositori skripsi yang ada.
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-5 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                            <button type="button" @click="auditModalOpen = false" class="px-6 py-2.5 bg-slate-800 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-700 transition-all shadow-sm cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>

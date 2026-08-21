<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => Auth::user()->role === 'dosen' ? 'Daftar Mahasiswa Bimbingan' : 'Daftar Pengajuan Skripsi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6" x-data="{ 
        rollbackModalOpen: false,
        rollbackData: { id: null, student: '', p1: '', p2: '' },
        openRollbackModal(id, student, p1, p2) {
            this.rollbackData = { id: id, student: student, p1: p1, p2: p2 };
            this.rollbackModalOpen = true;
        }
    }">
        <!-- Status Tabs Navigation -->
        <div class="flex items-center gap-1 border-b border-slate-100 dark:border-slate-800 overflow-x-auto pb-px custom-scrollbar">
            @if(Auth::user()->role === 'dosen')
                <a href="{{ route('theses.index', ['status' => 'active', 'search' => $search]) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'active') === 'active' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Bimbingan Aktif
                </a>
                <a href="{{ route('theses.index', ['status' => 'completed', 'search' => $search]) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'active') === 'completed' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Bimbingan (Lulus)
                </a>
            @else
                <a href="{{ route('theses.index', ['status' => 'all', 'search' => $search]) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'all' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Semua Pengajuan
                </a>
                <a href="{{ route('theses.index', ['status' => 'pending', 'search' => $search]) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'pending' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Menunggu Pembimbing
                </a>
                <a href="{{ route('theses.index', ['status' => 'active', 'search' => $search]) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'active' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Aktif / Berjalan
                </a>
                <a href="{{ route('theses.index', ['status' => 'completed', 'search' => $search]) }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 {{ ($status ?? 'all') === 'completed' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Riwayat Lulus
                </a>
                <a href="{{ route('theses.unsubmitted-students') }}" 
                   class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0 border-transparent text-amber-600 dark:text-amber-400 hover:text-amber-700 hover:bg-amber-50/50 dark:hover:bg-amber-900/10">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Belum Mengajukan Judul
                </a>
            @endif
        </div>

        <x-table-card 
            title="{{ Auth::user()->role === 'dosen' ? 'Daftar Mahasiswa Bimbingan' : 'Daftar Pengajuan Skripsi' }}"
            :footer="$theses->links()">
            
            <x-slot name="headerActions">
                <div class="flex flex-wrap items-center justify-between sm:justify-end gap-2.5 w-full">
                    <!-- Search Input -->
                    <div class="w-full sm:w-64">
                        <x-search-input 
                            name="search" 
                            :value="$search ?? ''" 
                            placeholder="Cari nama, NPM, judul..." 
                            route="theses.index"
                            :params="['status' => $status ?? '']" />
                    </div>
                    
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- Group 1: Tools (Clean Audit, Kanban, Migrasi) -->
                            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-700/60 rounded-xl border border-slate-200/70 dark:border-slate-700">
                                <!-- Clean Audit -->
                                <a href="{{ route('theses.clean-audit') }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 rounded-lg text-xs font-bold transition-all shadow-2xs border border-emerald-200/60 dark:border-emerald-700/40"
                                   title="Pusat Validasi & Clean Audit Judul">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <span class="hidden md:inline">Clean Audit</span>
                                    @if(isset($pendingSummary) && $pendingSummary['total'] > 0)
                                        <span class="px-1.5 py-0.2 rounded-full bg-emerald-600 text-white text-[10px] font-black">
                                            {{ $pendingSummary['total'] }}
                                        </span>
                                    @endif
                                </a>

                                <!-- Kanban View -->
                                <a href="{{ route('theses.kanban') }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-white/70 dark:hover:bg-slate-800 rounded-lg text-xs font-semibold transition-all"
                                   title="Tampilan Mode Kanban">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                                    <span class="hidden lg:inline">Kanban</span>
                                </a>

                                <!-- Input Migrasi -->
                                <a href="{{ route('theses.migration.create') }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-slate-600 dark:text-slate-300 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-white/70 dark:hover:bg-slate-800 rounded-lg text-xs font-semibold transition-all"
                                   title="Input Data Migrasi">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                    <span class="hidden lg:inline">Migrasi</span>
                                </a>
                            </div>

                            <!-- Group 2: Export Icons -->
                            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-700/60 rounded-xl border border-slate-200/70 dark:border-slate-700">
                                <a href="{{ route('theses.export-excel', ['search' => request('search'), 'status' => $status ?? 'all']) }}" 
                                   class="p-1.5 text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-white dark:hover:bg-slate-800 rounded-lg transition-all" 
                                   title="Ekspor ke Excel">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </a>
                                <a href="{{ route('theses.export-pdf', ['search' => request('search'), 'status' => $status ?? 'all']) }}" 
                                   class="p-1.5 text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-white dark:hover:bg-slate-800 rounded-lg transition-all" 
                                   title="Ekspor ke PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </a>
                            </div>

                            <!-- Group 3: Primary Action (Ajukan Judul) -->
                            <a href="{{ route('theses.create') }}" 
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-orange-500/20 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                <span>Ajukan Judul</span>
                            </a>
                        </div>
                    @endif
                </div>
            </x-slot>

            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                        <th class="py-4 px-6">Mahasiswa</th>
                        <th class="py-4 px-6">Rencana Judul Skripsi</th>
                        <th class="py-4 px-6">Deskripsi</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6">Pembimbing</th>
                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                            <th class="py-4 px-6 text-right whitespace-nowrap">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($theses as $thesis)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3.5">
                                    <div class="relative w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0 shadow-2xs">
                                        <img src="{{ $thesis->student->avatar_url }}" alt="{{ $thesis->student->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="space-y-1">
                                        <!-- Student Name & WhatsApp Shortcut -->
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight text-xs">{{ $thesis->student->name }}</span>
                                            
                                            @php
                                                $waNumber = \App\Helpers\PhoneHelper::formatForWhatsApp($thesis->student->phone_number);
                                                $dosenName = Auth::user()->name;
                                                $roleLabel = ($thesis->pembimbing1_id === Auth::id()) ? 'Pembimbing 1' : (($thesis->pembimbing2_id === Auth::id()) ? 'Pembimbing 2' : 'Dosen');
                                                $waMessage = urlencode("Halo {$thesis->student->name}, saya {$dosenName} ({$roleLabel}). Terkait bimbingan skripsi: \"" . ($thesis->final_title ?? $thesis->title) . "\"");
                                            @endphp

                                            @if($waNumber)
                                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-md text-[10px] font-bold transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer"
                                                   title="Kirim Pesan WhatsApp ke {{ $thesis->student->name }} ({{ $thesis->student->phone_number }})">
                                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400 fill-current shrink-0" viewBox="0 0 24 24">
                                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                                    </svg>
                                                    <span>Chat WA</span>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 rounded text-[9px] font-medium" title="Nomor WhatsApp belum didaftarkan oleh mahasiswa">
                                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                    <span>No WA -</span>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Badges: NPM, Angkatan, Pembimbing Role -->
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold font-mono tracking-wider">{{ $thesis->student->identifier ?? 'NPM -' }}</span>
                                            
                                            @if($thesis->student->entry_year)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                    Angkatan {{ $thesis->student->entry_year }}
                                                </span>
                                            @endif

                                            @if(Auth::user()->role === 'dosen')
                                                @if($thesis->pembimbing1_id === Auth::id())
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 shadow-2xs">
                                                        Pembimbing 1
                                                    </span>
                                                @elseif($thesis->pembimbing2_id === Auth::id())
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 shadow-2xs">
                                                        Pembimbing 2
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 max-w-xs whitespace-normal">
                                @if($thesis->final_title)
                                    <div class="font-black text-slate-800 dark:text-slate-100 line-clamp-2 mb-1 uppercase text-xs leading-tight" title="{{ $thesis->final_title }}">{{ $thesis->final_title }}</div>
                                    <div class="text-[9px] text-orange-600 dark:text-orange-400 font-black bg-orange-50 dark:bg-orange-500/10 inline-block px-2 py-0.5 rounded-lg border border-orange-100 dark:border-orange-500/10 uppercase tracking-tighter italic mb-1">Rencana awal: {{ $thesis->title }}</div>
                                @else
                                    <div class="font-bold text-slate-700 dark:text-slate-300 line-clamp-2 uppercase text-[11px] leading-tight" title="{{ $thesis->title }}">{{ $thesis->title }}</div>
                                @endif

                                @php
                                    $rawTitle = trim($thesis->final_title ?? $thesis->title ?? '');
                                    $titleUpper = strtoupper($rawTitle);
                                    $hasValidTitle = !empty($rawTitle) && !in_array($titleUpper, ['BELUM DIKETAHUI', 'BELUM DITENTUKAN', 'BELUM ADA JUDUL', 'BELUM ADA', '-']) && strlen($rawTitle) >= 8;
                                    $simScore = $hasValidTitle ? $thesis->getMaxSimilarityScore() : 0;
                                    $simMatches = $hasValidTitle ? $thesis->getDetailedSimilarityMatches() : collect();
                                @endphp
                                @if($hasValidTitle)
                                    <div class="mt-1.5 inline-block">
                                        <button @click='$dispatch("open-audit-modal", {
                                            student: "{{ addslashes($thesis->student->name) }}",
                                            npm: "{{ $thesis->student->identifier }}",
                                            title: "{{ addslashes($rawTitle) }}",
                                            score: {{ $simScore }},
                                            matches: {{ json_encode($simMatches->values()->toArray()) }}
                                        })' class="group focus:outline-hidden">
                                            @if($simScore >= 66)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter bg-rose-50 text-rose-600 border border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50 dark:text-rose-400 group-hover:bg-rose-100 dark:group-hover:bg-rose-900/60 transition-all shadow-2xs cursor-pointer" title="Klik untuk lihat rincian data kemiripan">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping shrink-0"></span>
                                                    <span>🔴 {{ $simScore }}% Sangat Mirip</span>
                                                    <svg class="w-3 h-3 opacity-60 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </span>
                                            @elseif($simScore >= 35)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:border-amber-900/50 dark:text-amber-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/60 transition-all shadow-2xs cursor-pointer" title="Klik untuk lihat rincian data kemiripan">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                                    <span>🟧 {{ $simScore }}% Mirip</span>
                                                    <svg class="w-3 h-3 opacity-60 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-900/50 dark:text-emerald-400 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/60 transition-all shadow-2xs cursor-pointer" title="Klik untuk lihat rincian data kemiripan">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                    <span>🟢 {{ 100 - $simScore }}% Unik</span>
                                                    <svg class="w-3 h-3 opacity-60 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </span>
                                            @endif
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 max-w-[14rem] whitespace-normal" x-data="{ openAbstract: false }">
                                @if($thesis->abstract)
                                    <div class="relative">
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed uppercase font-bold tracking-tighter italic">"{{ Str::limit($thesis->abstract, 80) }}"</p>
                                        <button @click="openAbstract = true" class="text-[9px] text-indigo-600 dark:text-indigo-400 font-black uppercase tracking-widest mt-1.5 flex items-center transition-all hover:translate-x-1">
                                            <span>Lihat Detail</span>
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                    </div>

                                    <!-- Abstract Modal -->
                                    <template x-teleport="body">
                                        <div x-show="openAbstract" class="fixed inset-0 overflow-y-auto" style="z-index: 99999 !important;" x-cloak x-transition>
                                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAbstract = false">
                                                    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                </div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <div class="inline-block align-middle bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-slate-200 dark:border-slate-700 relative" style="z-index: 100000 !important;">
                                                    <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                                                        <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Detail Deskripsi Skripsi</h3>
                                                        <button @click="openAbstract = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                    <div class="px-8 py-8 max-h-[60vh] overflow-y-auto">
                                                        <div class="mb-8">
                                                            <p class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest mb-2">Judul Pengajuan</p>
                                                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 leading-tight uppercase">{{ $thesis->title }}</h4>
                                                        </div>
                                                        <div class="p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                                            <p class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3">Deskripsi / Rencana</p>
                                                            <div class="text-xs text-slate-600 dark:text-slate-400 leading-loose text-justify font-medium whitespace-pre-line">
                                                                {{ $thesis->abstract }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                                                        <button type="button" @click="openAbstract = false" class="px-8 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                                                            Tutup
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                @else
                                    <span class="text-slate-300 text-xs italic">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    <x-status-badge 
                                        :type="$thesis->status === 'active' ? 'orange' : ($thesis->status === 'completed' ? 'emerald' : 'slate')" 
                                        :label="$thesis->status === 'active' ? 'AKTIF' : ($thesis->status === 'completed' ? 'LULUS' : 'MENUNGGU')" />
                                    
                                    @php
                                        $hasSeminar = ($thesis->status === 'completed')
                                            || \App\Models\SeminarApplication::where('thesis_id', $thesis->id)->whereIn('status', ['approved', 'completed', 'finished'])->exists()
                                            || \App\Models\SeminarScheduleDetail::where('thesis_id', $thesis->id)->exists();
                                            
                                        $hasDefense = ($thesis->status === 'completed')
                                            || \App\Models\ThesisDefenseApplication::where('thesis_id', $thesis->id)->whereIn('status', ['approved', 'completed', 'finished'])->exists()
                                            || \App\Models\ThesisDefenseScheduleDetail::where('thesis_id', $thesis->id)->exists();
                                    @endphp
                                    <div class="flex flex-col gap-1 mt-1 w-full max-w-[110px]">
                                        @if($hasSeminar)
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-teal-50 dark:bg-teal-500/10 text-teal-700 dark:text-teal-400 border border-teal-100 dark:border-teal-500/20 shadow-sm">SEMINAR: SUDAH</span>
                                        @else
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-slate-50 dark:bg-slate-900/30 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-800 shadow-sm">SEMINAR: BELUM</span>
                                        @endif

                                        @if($hasDefense)
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 shadow-sm">SIDANG: SUDAH</span>
                                        @else
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-slate-50 dark:bg-slate-900/30 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-800 shadow-sm">SIDANG: BELUM</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <td class="py-4 px-6">
                                @if($thesis->pembimbing1 || $thesis->pembimbing2)
                                    <div class="flex flex-col gap-1.5">
                                        @if($thesis->pembimbing1)
                                            <div class="flex items-center gap-2">
                                                <span class="w-4 h-4 rounded text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[9px] font-black border border-indigo-200 dark:border-indigo-800 shrink-0" style="background-color: #e0e7ff !important;">1</span>
                                                <span class="font-black text-slate-700 dark:text-slate-200 text-[10px] uppercase tracking-tighter">{{ $thesis->pembimbing1->name }}</span>
                                            </div>
                                        @endif
                                        @if($thesis->pembimbing2)
                                            <div class="flex items-center gap-2">
                                                <span class="w-4 h-4 rounded text-purple-600 dark:text-purple-400 flex items-center justify-center text-[9px] font-black border border-purple-200 dark:border-purple-800 shrink-0" style="background-color: #f3e8ff !important;">2</span>
                                                <span class="font-black text-slate-700 dark:text-slate-200 text-[10px] uppercase tracking-tighter">{{ $thesis->pembimbing2->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <x-status-badge type="slate" label="BELUM DITENTUKAN" />
                                @endif
                            </td>
                            
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                                <td class="py-4 px-6 text-right">
                                    @if(!$thesis->pembimbing1 || !$thesis->pembimbing2)
                                        <div x-data="{ openAssignModal: false }" class="inline-block">
                                            <button @click="openAssignModal = true" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-all shadow-xs shadow-indigo-500/20 hover:shadow active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                <span>Tugaskan</span>
                                            </button>

                                            <!-- Modal Penugasan Pembimbing -->
                                            <template x-teleport="body">
                                                <div x-show="openAssignModal" class="fixed inset-0 overflow-y-auto text-left" style="z-index: 99999 !important;" x-cloak x-transition>
                                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAssignModal = false">
                                                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                        </div>
                                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative" style="z-index: 100000 !important;">
                                                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                                                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Penugasan Dosen Pembimbing</h3>
                                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase mt-1">Mahasiswa: {{ $thesis->student->name }} ({{ $thesis->student->identifier }})</p>
                                                            </div>
                                                            
                                                            <form action="{{ route('theses.assign', $thesis->id) }}" method="POST" class="p-8 space-y-5">
                                                                @csrf
                                                                <div x-data='{ 
                                                                    p1_id: "{{ $thesis->requested_pembimbing1_id }}", 
                                                                    p2_id: "{{ $thesis->requested_pembimbing2_id }}",
                                                                    dosens: {{ $dosens->mapWithKeys(fn($d) => [$d->id => $d])->toJson() }}
                                                                }' class="space-y-5">
                                                                    
                                                                    <!-- Suggestions Chips Header -->
                                                                    @if($thesis->requestedPembimbing1 || $thesis->requestedPembimbing2)
                                                                        <div class="p-4 bg-indigo-50/80 dark:bg-indigo-950/40 rounded-2xl border border-indigo-100 dark:border-indigo-900/50 space-y-2">
                                                                            <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest flex items-center gap-1.5">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                                                Usulan Pembimbing dari Mahasiswa:
                                                                            </span>
                                                                            <div class="flex flex-wrap gap-2">
                                                                                @if($thesis->requestedPembimbing1)
                                                                                    @php $p1Full = $thesis->requestedPembimbing1->total_workload >= $thesis->requestedPembimbing1->max_quota; @endphp
                                                                                    <div class="px-3 py-1.5 rounded-xl text-xs font-extrabold uppercase border flex items-center gap-2 {{ $p1Full ? 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50' : 'bg-white text-indigo-700 border-indigo-200 dark:bg-slate-900 dark:text-indigo-400 dark:border-indigo-800 shadow-2xs' }}">
                                                                                        <span>P1: {{ $thesis->requestedPembimbing1->name }}</span>
                                                                                        <span class="font-black">({{ $thesis->requestedPembimbing1->total_workload }}/{{ $thesis->requestedPembimbing1->max_quota }})</span>
                                                                                        @if($p1Full)<span class="text-rose-600 font-black">🔴 Penuh</span>@endif
                                                                                    </div>
                                                                                @endif
                                                                                @if($thesis->requestedPembimbing2)
                                                                                    @php $p2Full = $thesis->requestedPembimbing2->total_workload >= $thesis->requestedPembimbing2->max_quota; @endphp
                                                                                    <div class="px-3 py-1.5 rounded-xl text-xs font-extrabold uppercase border flex items-center gap-2 {{ $p2Full ? 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50' : 'bg-white text-indigo-700 border-indigo-200 dark:bg-slate-900 dark:text-indigo-400 dark:border-indigo-800 shadow-2xs' }}">
                                                                                        <span>P2: {{ $thesis->requestedPembimbing2->name }}</span>
                                                                                        <span class="font-black">({{ $thesis->requestedPembimbing2->total_workload }}/{{ $thesis->requestedPembimbing2->max_quota }})</span>
                                                                                        @if($p2Full)<span class="text-rose-600 font-black">🔴 Penuh</span>@endif
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Select P1 -->
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pilih Dosen Pembimbing 1</label>
                                                                        <select name="pembimbing1_id" x-model="p1_id" required class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase tracking-tighter focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-800 dark:text-slate-100">
                                                                            <option value="">Pilih P1...</option>
                                                                            @foreach($dosens as $dosen)
                                                                                @php 
                                                                                    $score = $thesis->getMatchScore($dosen);
                                                                                    $isFull = $dosen->total_workload >= $dosen->max_quota;
                                                                                @endphp
                                                                                <option value="{{ $dosen->id }}" class="{{ $isFull ? 'text-rose-500 font-bold' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-600' : 'text-slate-800') }}">
                                                                                    {{ $isFull ? '🔴 [PENUH] ' : '' }}{{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <template x-if="p1_id && dosens[p1_id]">
                                                                            <div class="mt-1.5 flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'text-rose-600 dark:text-rose-400' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400')">
                                                                                <span class="w-2 h-2 rounded-full shrink-0" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'bg-rose-500' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                                                                                <span x-text="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'Quota Penuh (' + dosens[p1_id].total_workload + '/' + dosens[p1_id].max_quota + ')' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia (' + dosens[p1_id].total_workload + '/' + dosens[p1_id].max_quota + ')')"></span>
                                                                            </div>
                                                                        </template>
                                                                    </div>

                                                                    <!-- Select P2 -->
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pilih Dosen Pembimbing 2</label>
                                                                        <select name="pembimbing2_id" x-model="p2_id" required class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase tracking-tighter focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-800 dark:text-slate-100">
                                                                            <option value="">Pilih P2...</option>
                                                                            @foreach($dosens as $dosen)
                                                                                @php 
                                                                                    $score = $thesis->getMatchScore($dosen);
                                                                                    $isFull = $dosen->total_workload >= $dosen->max_quota;
                                                                                @endphp
                                                                                <option value="{{ $dosen->id }}" class="{{ $isFull ? 'text-rose-500 font-bold' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-600' : 'text-slate-800') }}">
                                                                                    {{ $isFull ? '🔴 [PENUH] ' : '' }}{{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <template x-if="p2_id && dosens[p2_id]">
                                                                            <div class="mt-1.5 flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'text-rose-600 dark:text-rose-400' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400')">
                                                                                <span class="w-2 h-2 rounded-full shrink-0" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'bg-rose-500' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                                                                                <span x-text="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'Quota Penuh (' + dosens[p2_id].total_workload + '/' + dosens[p2_id].max_quota + ')' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia (' + dosens[p2_id].total_workload + '/' + dosens[p2_id].max_quota + ')')"></span>
                                                                            </div>
                                                                        </template>
                                                                    </div>

                                                                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                                                                        <button type="button" @click="openAssignModal = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors">Batal</button>
                                                                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 transition-all">Simpan Penugasan</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    @else
                                        <div class="flex justify-end items-center gap-2 shrink-0 whitespace-nowrap" x-data="{ openEditModal: false }">
                                            <!-- Rollback Button -->
                                            <button type="button"
                                                    @click="openRollbackModal('{{ $thesis->id }}', '{{ addslashes($thesis->student->name ?? 'Mahasiswa') }}', '{{ addslashes($thesis->pembimbing1?->name ?? '-') }}', '{{ addslashes($thesis->pembimbing2?->name ?? '-') }}')" 
                                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60 rounded-lg text-xs font-bold transition-all shadow-xs hover:shadow active:scale-95 cursor-pointer"
                                                    title="Rollback / Batalkan Penugasan Pembimbing">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                <span>Rollback</span>
                                            </button>

                                            <!-- Edit Button -->
                                            <button type="button"
                                                    @click="openEditModal = true" 
                                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold transition-all shadow-xs hover:shadow active:scale-95 cursor-pointer"
                                                    title="Edit Judul & Data Skripsi">
                                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                <span>Edit</span>
                                            </button>

                                            <!-- Logbook Button -->
                                            <a href="{{ route('theses.logbooks', $thesis->id) }}" 
                                               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-xs font-bold transition-all shadow-xs shadow-orange-500/20 hover:shadow active:scale-95 cursor-pointer"
                                               title="Lihat Logbook Bimbingan">
                                                <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                                <span>Logbook</span>
                                            </a>

                                            <!-- Edit Modal -->
                                            <template x-teleport="body">
                                                <div x-show="openEditModal" class="fixed inset-0 overflow-y-auto text-left" style="z-index: 99999 !important;" x-cloak x-transition>
                                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openEditModal = false">
                                                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                                                        </div>
                                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative" style="z-index: 100000 !important;">
                                                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                                                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Edit Data Skripsi</h3>
                                                                <p class="text-[10px] text-slate-500 uppercase font-black mt-1">Mahasiswa: {{ $thesis->student->name }}</p>
                                                            </div>
                                                            <form action="{{ route('theses.update', $thesis->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="px-8 py-8 space-y-6">
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Judul Final Skripsi</label>
                                                                        <textarea name="final_title" rows="3" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all uppercase leading-relaxed p-4 text-slate-800 dark:text-slate-100" required>{{ $thesis->final_title ?? $thesis->title }}</textarea>
                                                                    </div>
                                                                    <div x-data='{ 
                                                                        p1_id: "{{ $thesis->pembimbing1_id }}", 
                                                                        p2_id: "{{ $thesis->pembimbing2_id }}",
                                                                        dosens: {{ $dosens->mapWithKeys(fn($d) => [$d->id => $d])->toJson() }}
                                                                    }' class="grid grid-cols-2 gap-4">
                                                                        <div>
                                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pembimbing 1</label>
                                                                            <select name="pembimbing1_id" x-model="p1_id" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all p-3 text-slate-800 dark:text-slate-100" required>
                                                                                @foreach($dosens as $dosen)
                                                                                    @php $score = $thesis->getMatchScore($dosen); @endphp
                                                                                    <option value="{{ $dosen->id }}" 
                                                                                            class="{{ $dosen->total_workload >= $dosen->max_quota ? 'text-rose-500' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500') }}">
                                                                                        {{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <template x-if="p1_id && dosens[p1_id]">
                                                                                <div class="mt-2 flex items-center gap-1.5">
                                                                                    <div class="w-2 h-2 rounded-full" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'bg-rose-500' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></div>
                                                                                    <span class="text-[9px] font-black uppercase tracking-widest" :class="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'text-rose-500' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500')">
                                                                                        <span x-text="dosens[p1_id].total_workload >= dosens[p1_id].max_quota ? 'Quota Penuh' : (dosens[p1_id].total_workload >= dosens[p1_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia')"></span>
                                                                                    </span>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                        <div>
                                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pembimbing 2</label>
                                                                            <select name="pembimbing2_id" x-model="p2_id" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all p-3 text-slate-800 dark:text-slate-100" required>
                                                                                @foreach($dosens as $dosen)
                                                                                    @php $score = $thesis->getMatchScore($dosen); @endphp
                                                                                    <option value="{{ $dosen->id }}" 
                                                                                            class="{{ $dosen->total_workload >= $dosen->max_quota ? 'text-rose-500' : ($dosen->total_workload >= $dosen->max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500') }}">
                                                                                        {{ $dosen->name }} ({{ $dosen->total_workload }}/{{ $dosen->max_quota }}) {{ $score > 0 ? '✨ Match: '.$score : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <template x-if="p2_id && dosens[p2_id]">
                                                                                <div class="mt-2 flex items-center gap-1.5">
                                                                                    <div class="w-2 h-2 rounded-full" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'bg-rose-500' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'bg-amber-500' : 'bg-emerald-500')"></div>
                                                                                    <span class="text-[9px] font-black uppercase tracking-widest" :class="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'text-rose-500' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'text-amber-500' : 'text-emerald-500')">
                                                                                        <span x-text="dosens[p2_id].total_workload >= dosens[p2_id].max_quota ? 'Quota Penuh' : (dosens[p2_id].total_workload >= dosens[p2_id].max_quota * 0.75 ? 'Hampir Penuh' : 'Tersedia')"></span>
                                                                                    </span>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                                                    <button type="button" @click="openEditModal = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors">Batal</button>
                                                                    <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-500/20 transition-all">Simpan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <x-empty-state colspan="{{ (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') ? '6' : '5' }}" description="Belum ada data skripsi yang ditemukan." />
                    @endforelse
                </tbody>
            </table>
        </x-table-card>

        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
            <!-- MODAL: KONFIRMASI ROLLBACK PENUGASAN PEMBIMBING -->
            <template x-teleport="body">
                <div x-show="rollbackModalOpen" 
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
                         @click="rollbackModalOpen = false" 
                         aria-hidden="true"></div>

                    <!-- Centered Modal Box -->
                    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 text-center">
                        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 relative z-50 my-auto transform transition-all"
                             @click.stop>
                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-rose-50/50 dark:bg-rose-950/30 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-rose-800 dark:text-rose-200 uppercase tracking-widest">Konfirmasi Rollback Pembimbing</h3>
                                    <p class="text-[10px] text-rose-600 dark:text-rose-400 font-bold uppercase mt-0.5">Kembalikan status skripsi ke Menunggu</p>
                                </div>
                            </div>

                            <form :action="'/theses/' + rollbackData.id + '/unassign'" method="POST" class="p-8 space-y-5">
                                @csrf
                                <div class="space-y-3">
                                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                                        Apakah Anda yakin ingin membatalkan penugasan dosen pembimbing untuk mahasiswa <strong class="text-slate-800 dark:text-white uppercase" x-text="rollbackData.student"></strong>?
                                    </p>
                                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-1.5 text-[11px]">
                                        <div class="flex justify-between">
                                            <span class="text-slate-400 uppercase font-black text-[9px]">Pembimbing 1 Saat Ini:</span>
                                            <span class="font-extrabold text-slate-700 dark:text-slate-200" x-text="rollbackData.p1"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-400 uppercase font-black text-[9px]">Pembimbing 2 Saat Ini:</span>
                                            <span class="font-extrabold text-slate-700 dark:text-slate-200" x-text="rollbackData.p2"></span>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-amber-600 dark:text-amber-400 font-bold italic">
                                        * Status skripsi akan dikembalikan ke 'pending', kuota pembimbing akan dilepaskan, dan seluruh riwayat ACC awal akan di-reset.
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                                    <button type="button" @click="rollbackModalOpen = false" class="px-5 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-md shadow-rose-500/20 cursor-pointer">
                                        Ya, Rollback Penugasan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        @endif
    </div>
</x-app-layout>

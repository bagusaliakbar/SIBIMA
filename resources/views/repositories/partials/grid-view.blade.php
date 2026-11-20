        <!-- VIEW MODE 1: REPOSITORY CARDS GRID -->
        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($repositories as $repo)
                @php
                    $badge = $repo->topic_badge;
                @endphp
                <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-200 hover:-translate-y-1 flex flex-col group">
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Header Meta Badges & Admin Actions -->
                            <div class="flex justify-between items-center gap-2 mb-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-[9px] font-black uppercase tracking-widest rounded-lg border border-slate-200 dark:border-slate-700">
                                        Angkatan {{ $repo->year }}
                                    </span>

                                    <span class="px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-lg border {{ $badge['bg'] }}">
                                        {{ $badge['label'] }}
                                    </span>

                                    @if($repo->file_path)
                                        <button type="button" 
                                                @click="openPdfReader({{ json_encode($repo) }}, 'bab1')" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/80 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                title="Baca Naskah BAB 1 di Web (In-App Reader)">
                                            <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span>BAB 1 PDF</span>
                                        </button>
                                    @endif

                                    @if($repo->file_path_bab2)
                                        <button type="button" 
                                                @click="openPdfReader({{ json_encode($repo) }}, 'bab2')" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/80 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                title="Baca Naskah BAB 2 di Web (In-App Reader)">
                                            <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span>BAB 2 PDF</span>
                                        </button>
                                    @endif

                                    @if($repo->file_path_bab3)
                                        <button type="button" 
                                                @click="openPdfReader({{ json_encode($repo) }}, 'bab3')" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/80 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                title="Baca Naskah BAB 3 di Web (In-App Reader)">
                                            <svg class="w-3 h-3 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span>BAB 3 PDF</span>
                                        </button>
                                    @endif

                                    @if($repo->file_path_bab4)
                                        <button type="button" 
                                                @click="openPdfReader({{ json_encode($repo) }}, 'bab4')" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                title="Baca Naskah BAB 4 di Web (In-App Reader)">
                                            <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span>BAB 4 PDF</span>
                                        </button>
                                    @endif

                                    @if($repo->file_path_bab5)
                                        <button type="button" 
                                                @click="openPdfReader({{ json_encode($repo) }}, 'bab5')" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800/80 hover:bg-purple-100 dark:hover:bg-purple-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                title="Baca Naskah BAB 5 di Web (In-App Reader)">
                                            <svg class="w-3 h-3 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span>BAB 5 PDF</span>
                                        </button>
                                    @endif

                                    @if($repo->file_path_bab6)
                                        <button type="button" 
                                                @click="openPdfReader({{ json_encode($repo) }}, 'bab6')" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-800/80 hover:bg-cyan-100 dark:hover:bg-cyan-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                title="Baca Naskah BAB 6 di Web (In-App Reader)">
                                            <svg class="w-3 h-3 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span>BAB 6 PDF</span>
                                        </button>
                                    @endif
                                </div>

                                @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                    <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <!-- Quick Edit Button -->
                                        <button type="button" 
                                                @click="openEditModal({{ json_encode($repo) }})"
                                                class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 rounded-lg transition-all"
                                                title="Edit Data Arsip" aria-label="Edit Data Arsip">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <!-- Quick Delete Button -->
                                        <button type="button" 
                                                @click="openDeleteModal({{ json_encode($repo) }})"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-all"
                                                title="Hapus Arsip" aria-label="Hapus Arsip">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Title (Click to open abstract) -->
                            <h3 @click="openAbstractModal({{ json_encode($repo) }}, {{ json_encode($badge) }})"
                                class="text-sm font-black text-slate-800 dark:text-slate-100 mb-2.5 leading-snug group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors line-clamp-3 cursor-pointer"
                                title="Klik untuk membaca detail & abstrak">
                                {{ $repo->title }}
                            </h3>
                            
                            <!-- Abstract Snippet -->
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 line-clamp-3 leading-relaxed">
                                {{ $repo->abstract ?: 'Tidak ada abstrak yang tersedia.' }}
                            </p>
                        </div>
                        
                        <!-- Footer Info -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700/80 mt-auto space-y-2.5">
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
                                <div class="p-2.5 bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl space-y-1.5 text-[10px]">
                                    @if($repo->pembimbing1)
                                        <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300 min-w-0">
                                            <span class="px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-bold text-[8px] rounded border border-indigo-200/80 dark:border-indigo-800/80 shrink-0">P1</span>
                                            <span class="truncate font-bold text-slate-700 dark:text-slate-200">{{ $repo->pembimbing1 }}</span>
                                        </div>
                                    @endif
                                    @if($repo->pembimbing2)
                                        <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300 min-w-0">
                                            <span class="px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 font-bold text-[8px] rounded border border-purple-200/80 dark:border-purple-800/80 shrink-0">P2</span>
                                            <span class="truncate font-bold text-slate-700 dark:text-slate-200">{{ $repo->pembimbing2 }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Read Detail Button in Card -->
                            <button type="button" 
                                    @click="openAbstractModal({{ json_encode($repo) }}, {{ json_encode($badge) }})"
                                    class="w-full py-2 bg-slate-50 hover:bg-orange-50 dark:bg-slate-900 dark:hover:bg-orange-950/40 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 rounded-xl text-[11px] font-bold transition-all border border-slate-200/80 dark:border-slate-700 flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Lihat Detail Abstrak</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
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
                        <a href="{{ route('repositories.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-orange-500/25 hover:scale-[1.02] active:scale-95">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Reset Pencarian</span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

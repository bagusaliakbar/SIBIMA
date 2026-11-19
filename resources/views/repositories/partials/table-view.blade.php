        <!-- VIEW MODE 2: COMPACT TABLE / LIST -->
        <div x-show="viewMode === 'table'" x-cloak class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50/75 dark:bg-slate-900/60 text-slate-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                            <th class="py-3.5 px-5">Angkatan & Mahasiswa</th>
                            <th class="py-3.5 px-5">Judul Skripsi & Topik</th>
                            <th class="py-3.5 px-5">Dosen Pembimbing</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                        @forelse($repositories as $repo)
                            @php
                                $badge = $repo->topic_badge;
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors group">
                                <!-- Student & Year -->
                                <td class="py-3.5 px-5 align-top">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-[9px] font-black uppercase tracking-wider rounded-md border border-slate-200 dark:border-slate-700">
                                                {{ $repo->year }}
                                            </span>
                                            <span class="font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight text-xs">{{ $repo->name }}</span>
                                        </div>
                                        <div class="text-[10px] font-mono font-semibold text-slate-400 dark:text-slate-500">
                                            NPM: {{ $repo->identifier ?? '-' }}
                                        </div>
                                    </div>
                                </td>

                                <!-- Title & Smart Topic Badge -->
                                <td class="py-3.5 px-5 align-top max-w-md whitespace-normal">
                                    <div class="space-y-1.5">
                                        <h4 class="font-bold text-slate-800 dark:text-slate-100 text-xs leading-snug line-clamp-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors cursor-pointer"
                                            @click="openAbstractModal({{ json_encode($repo) }}, {{ json_encode($badge) }})">
                                            {{ $repo->title }}
                                        </h4>
                                        <div class="flex items-center gap-2.5 flex-wrap">
                                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md border {{ $badge['bg'] }}">
                                                {{ $badge['label'] }}
                                            </span>
                                            @if($repo->file_path)
                                                <button type="button" 
                                                        @click="openPdfReader({{ json_encode($repo) }}, 'bab1')" 
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/80 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                        title="Baca Naskah BAB 1 di Web (In-App Reader)">
                                                    <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <span>BAB 1 PDF</span>
                                                </button>
                                            @endif
                                            @if($repo->file_path_bab2)
                                                <button type="button" 
                                                        @click="openPdfReader({{ json_encode($repo) }}, 'bab2')" 
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/80 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                        title="Baca Naskah BAB 2 di Web (In-App Reader)">
                                                    <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <span>BAB 2 PDF</span>
                                                </button>
                                            @endif
                                            @if($repo->file_path_bab3)
                                                <button type="button" 
                                                        @click="openPdfReader({{ json_encode($repo) }}, 'bab3')" 
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/80 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                        title="Baca Naskah BAB 3 di Web (In-App Reader)">
                                                    <svg class="w-3 h-3 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <span>BAB 3 PDF</span>
                                                </button>
                                            @endif
                                            @if($repo->file_path_bab4)
                                                <button type="button" 
                                                        @click="openPdfReader({{ json_encode($repo) }}, 'bab4')" 
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                        title="Baca Naskah BAB 4 di Web (In-App Reader)">
                                                    <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <span>BAB 4 PDF</span>
                                                </button>
                                            @endif
                                            @if($repo->file_path_bab5)
                                                <button type="button" 
                                                        @click="openPdfReader({{ json_encode($repo) }}, 'bab5')" 
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800/80 hover:bg-purple-100 dark:hover:bg-purple-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                        title="Baca Naskah BAB 5 di Web (In-App Reader)">
                                                    <svg class="w-3 h-3 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <span>BAB 5 PDF</span>
                                                </button>
                                            @endif
                                            @if($repo->file_path_bab6)
                                                <button type="button" 
                                                        @click="openPdfReader({{ json_encode($repo) }}, 'bab6')" 
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-800/80 hover:bg-cyan-100 dark:hover:bg-cyan-900/60 transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer" 
                                                        title="Baca Naskah BAB 6 di Web (In-App Reader)">
                                                    <svg class="w-3 h-3 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <span>BAB 6 PDF</span>
                                                </button>
                                            @endif
                                            @if($repo->abstract)
                                                <button type="button" 
                                                        @click="openAbstractModal({{ json_encode($repo) }}, {{ json_encode($badge) }})"
                                                        class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-0.5 cursor-pointer">
                                                    <span>Baca Abstrak</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Advisors -->
                                <td class="py-3.5 px-5 align-top">
                                    @if($repo->pembimbing1 || $repo->pembimbing2)
                                        <div class="space-y-1 max-w-xs text-[11px]">
                                            @if($repo->pembimbing1)
                                                <div class="flex items-center gap-1.5 truncate">
                                                    <span class="w-3.5 h-3.5 rounded bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-[8px] font-black border border-indigo-200 dark:border-indigo-800/80 shrink-0">1</span>
                                                    <span class="text-slate-700 dark:text-slate-300 font-semibold truncate">{{ $repo->pembimbing1 }}</span>
                                                </div>
                                            @endif
                                            @if($repo->pembimbing2)
                                                <div class="flex items-center gap-1.5 truncate">
                                                    <span class="w-3.5 h-3.5 rounded bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 flex items-center justify-center text-[8px] font-black border border-purple-200 dark:border-purple-800/80 shrink-0">2</span>
                                                    <span class="text-slate-700 dark:text-slate-300 font-semibold truncate">{{ $repo->pembimbing2 }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-[11px] italic">-</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-5 align-middle text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" 
                                                @click="openAbstractModal({{ json_encode($repo) }}, {{ json_encode($badge) }})"
                                                class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-orange-50 dark:hover:bg-orange-950/50 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 rounded-lg text-xs font-bold transition-all border border-slate-200/80 dark:border-slate-600 shadow-2xs cursor-pointer"
                                                title="Lihat Detail Abstrak">
                                            Detail
                                        </button>

                                        @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                            <button type="button" 
                                                    @click="openEditModal({{ json_encode($repo) }})"
                                                    class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 rounded-lg transition-all cursor-pointer"
                                                    title="Edit Data Arsip">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            <button type="button" 
                                                    @click="openDeleteModal({{ json_encode($repo) }})"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-all cursor-pointer"
                                                    title="Hapus Arsip">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="space-y-3 max-w-sm mx-auto">
                                        <p class="text-xs">Tidak ada arsip skripsi yang cocok dengan filter pencarian.</p>
                                        <a href="{{ route('repositories.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-orange-500/25 hover:scale-[1.02] active:scale-95">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            <span>Reset Pencarian</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

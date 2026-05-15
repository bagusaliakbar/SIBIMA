<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Daftar Pengajuan Skripsi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <x-table-card 
            title="Daftar Pengajuan Skripsi"
            :footer="$theses->links()">
            
            <x-slot name="headerActions">
                <div class="flex flex-wrap items-center gap-3">
                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari nama, NPM, atau judul..." 
                        route="theses.index" />
                    
                    <div class="flex items-center gap-2">
                        <a href="{{ route('theses.export-excel', ['search' => request('search')]) }}" class="inline-flex items-center px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl font-black text-[10px] uppercase tracking-widest border border-emerald-100 dark:border-emerald-500/20 hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Excel
                        </a>
                        <a href="{{ route('theses.export-pdf', ['search' => request('search')]) }}" class="inline-flex items-center px-4 py-2 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-xl font-black text-[10px] uppercase tracking-widest border border-rose-100 dark:border-rose-500/20 hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            PDF
                        </a>
                    </div>
                </div>
            </x-slot>

            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                        <th class="py-4 px-6">Mahasiswa</th>
                        <th class="py-4 px-6">Rencana Judul Skripsi</th>
                        <th class="py-4 px-6">Deskripsi</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        @if(Auth::user()->role === 'admin')
                            <th class="py-4 px-6">Pembimbing</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($theses as $thesis)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $thesis->student->name }}</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-black tracking-widest">{{ $thesis->student->identifier ?? 'NPM TIDAK ADA' }}</div>
                            </td>
                            <td class="py-4 px-6 max-w-xs whitespace-normal">
                                @if($thesis->final_title)
                                    <div class="font-black text-slate-800 dark:text-slate-100 line-clamp-2 mb-1 uppercase text-xs leading-tight" title="{{ $thesis->final_title }}">{{ $thesis->final_title }}</div>
                                    <div class="text-[9px] text-orange-600 dark:text-orange-400 font-black bg-orange-50 dark:bg-orange-500/10 inline-block px-2 py-0.5 rounded-lg border border-orange-100 dark:border-orange-500/10 uppercase tracking-tighter italic">Rencana awal: {{ $thesis->title }}</div>
                                @else
                                    <div class="font-bold text-slate-700 dark:text-slate-300 line-clamp-2 uppercase text-[11px] leading-tight" title="{{ $thesis->title }}">{{ $thesis->title }}</div>
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
                                    <div x-show="openAbstract" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak x-transition>
                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAbstract = false">
                                                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                                            </div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div class="inline-block align-middle bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-slate-100 dark:border-slate-700">
                                                <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
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
                                                        <p class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3">Deskripsi / Abstrak</p>
                                                        <div class="text-xs text-slate-600 dark:text-slate-400 leading-loose text-justify font-medium">
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
                                @else
                                    <span class="text-slate-300 text-xs italic">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <x-status-badge 
                                    :type="$thesis->status === 'active' ? 'orange' : ($thesis->status === 'completed' ? 'emerald' : 'slate')" 
                                    :label="$thesis->status === 'active' ? 'AKTIF' : ($thesis->status === 'completed' ? 'LULUS' : 'MENUNGGU')" />
                            </td>
                            
                            @if(Auth::user()->role === 'admin')
                                <td class="py-4 px-6">
                                    @if($thesis->pembimbing1 && $thesis->pembimbing2)
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-2">
                                                <span class="w-4 h-4 rounded bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[9px] font-black border border-indigo-100 dark:border-indigo-500/20">1</span>
                                                <span class="font-black text-slate-700 dark:text-slate-200 text-[10px] uppercase tracking-tighter">{{ $thesis->pembimbing1->name }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-4 h-4 rounded bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[9px] font-black border border-indigo-100 dark:border-indigo-500/20">2</span>
                                                <span class="font-black text-slate-700 dark:text-slate-200 text-[10px] uppercase tracking-tighter">{{ $thesis->pembimbing2->name }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <x-status-badge type="slate" label="BELUM DITENTUKAN" />
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if(!$thesis->pembimbing1 || !$thesis->pembimbing2)
                                        <form action="{{ route('theses.assign', $thesis->id) }}" method="POST" class="flex flex-col items-end gap-2">
                                            @csrf
                                            @if($thesis->requestedPembimbing1 || $thesis->requestedPembimbing2)
                                                <div class="p-3 bg-indigo-50 dark:bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/10 rounded-xl text-left">
                                                    <span class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest block mb-1.5">💡 Usulan Mahasiswa:</span>
                                                    <div class="space-y-1">
                                                        @if($thesis->requestedPembimbing1)
                                                            <span class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-tighter leading-none">1: {{ $thesis->requestedPembimbing1->name }}</span>
                                                        @endif
                                                        @if($thesis->requestedPembimbing2)
                                                            <span class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-tighter leading-none">2: {{ $thesis->requestedPembimbing2->name }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-2">
                                                <select name="pembimbing1_id" required class="w-36 py-1.5 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-black uppercase tracking-tighter focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-700 dark:text-slate-300">
                                                    <option value="">Pilih P1...</option>
                                                    @foreach($dosens as $dosen)
                                                        <option value="{{ $dosen->id }}" {{ $thesis->requested_pembimbing1_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                                    @endforeach
                                                </select>
                                                <select name="pembimbing2_id" required class="w-36 py-1.5 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-black uppercase tracking-tighter focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-700 dark:text-slate-300">
                                                    <option value="">Pilih P2...</option>
                                                    @foreach($dosens as $dosen)
                                                        <option value="{{ $dosen->id }}" {{ $thesis->requested_pembimbing2_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">
                                                Tugaskan
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex justify-end gap-2" x-data="{ openEditModal: false }">
                                            <button @click="openEditModal = true" class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">
                                                Edit
                                            </button>
                                            <a href="{{ route('theses.logbooks', $thesis->id) }}" class="px-4 py-2 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-500/20">
                                                Logbook
                                            </a>

                                            <!-- Edit Modal -->
                                            <div x-show="openEditModal" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak x-transition>
                                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openEditModal = false">
                                                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                                                    </div>
                                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                    <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-100 dark:border-slate-700">
                                                        <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700">
                                                            <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Edit Data Skripsi</h3>
                                                            <p class="text-[10px] text-slate-500 uppercase font-black mt-1">Mahasiswa: {{ $thesis->student->name }}</p>
                                                        </div>
                                                        <form action="{{ route('theses.update', $thesis->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="px-8 py-8 space-y-6">
                                                                <div>
                                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Judul Final Skripsi</label>
                                                                    <textarea name="final_title" rows="3" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all uppercase leading-relaxed p-4" required>{{ $thesis->final_title ?? $thesis->title }}</textarea>
                                                                </div>
                                                                <div class="grid grid-cols-2 gap-4">
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pembimbing 1</label>
                                                                        <select name="pembimbing1_id" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all p-3" required>
                                                                            @foreach($dosens as $dosen)
                                                                                <option value="{{ $dosen->id }}" {{ $thesis->pembimbing1_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pembimbing 2</label>
                                                                        <select name="pembimbing2_id" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-tighter focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all p-3" required>
                                                                            @foreach($dosens as $dosen)
                                                                                <option value="{{ $dosen->id }}" {{ $thesis->pembimbing2_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                                                            @endforeach
                                                                        </select>
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
                                        </div>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <x-empty-state colspan="{{ Auth::user()->role === 'admin' ? '6' : '4' }}" description="Belum ada data skripsi yang ditemukan." />
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>
</x-app-layout>

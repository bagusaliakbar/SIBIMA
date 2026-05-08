<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Daftar Pengajuan Skripsi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            @if(session('success'))
                <div class="m-6 mb-0 p-4 rounded bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-sm flex items-center border border-emerald-100 dark:border-emerald-800/50">
                    <svg class="w-4 h-4 mr-3 text-emerald-600 dark:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Daftar Pengajuan Skripsi</h3>
                
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <!-- Search Input -->
                    <form action="{{ route('theses.index') }}" method="GET" class="relative w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, NPM, atau judul..." class="block w-full sm:w-64 pl-10 pr-10 py-1.5 border border-slate-200 dark:border-slate-700 rounded-md leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition-colors">
                        @if(isset($search) && $search !== '')
                            <a href="{{ route('theses.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </form>
                    
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('theses.export-excel', ['search' => request('search')]) }}" class="px-3 py-1.5 text-xs font-medium border border-slate-200 dark:border-slate-700 rounded text-emerald-600 dark:text-emerald-400 bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:border-emerald-200 dark:hover:border-emerald-800 transition-colors shadow-sm flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Excel
                        </a>
                        <a href="{{ route('theses.export-pdf', ['search' => request('search')]) }}" class="px-3 py-1.5 text-xs font-medium border border-slate-200 dark:border-slate-700 rounded text-red-600 dark:text-red-400 bg-white dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-900/30 hover:border-red-200 dark:hover:border-red-800 transition-colors shadow-sm flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider">MAHASISWA</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider">RENCANA JUDUL SKRIPSI</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider">DESKRIPSI</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider">STATUS</th>
                            @if(Auth::user()->role === 'admin')
                                <th class="py-3 px-6 font-semibold text-xs tracking-wider">PEMBIMBING</th>
                                <th class="py-3 px-6 font-semibold text-xs tracking-wider text-right">AKSI</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($theses as $thesis)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group">
                                <td class="py-4 px-6">
                                    <div class="font-medium text-slate-800 dark:text-slate-100">{{ $thesis->student->name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $thesis->student->identifier ?? 'NPM Tidak Ada' }}</div>
                                </td>
                                <td class="py-4 px-6 max-w-xs whitespace-normal">
                                    @if($thesis->final_title)
                                        <div class="font-bold text-slate-800 dark:text-slate-100 line-clamp-2 mb-1" title="{{ $thesis->final_title }}">{{ $thesis->final_title }}</div>
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium bg-slate-100 dark:bg-slate-700 inline-block px-1.5 py-0.5 rounded">Rencana awal: {{ $thesis->title }}</div>
                                    @else
                                        <div class="font-medium text-slate-700 dark:text-slate-300 line-clamp-2" title="{{ $thesis->title }}">{{ $thesis->title }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 max-w-[14rem] whitespace-normal" x-data="{ openAbstract: false }">
                                    @if($thesis->abstract)
                                        <div class="relative">
                                            <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">{{ $thesis->abstract }}</p>
                                            <button @click="openAbstract = true" class="text-[10px] text-orange-600 font-bold hover:text-orange-700 mt-1.5 flex items-center transition-colors group">
                                                <span>Lihat Selengkapnya</span>
                                                <svg class="w-3 h-3 ml-0.5 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            </button>
                                        </div>

                                        <!-- Abstract Modal -->
                                        <div x-show="openAbstract" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div x-show="openAbstract" x-transition.opacity class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="openAbstract = false"></div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <div x-show="openAbstract" x-transition.scale.origin.center class="inline-block align-middle bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-slate-100 dark:border-slate-700">
                                                    <div class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Detail Deskripsi Skripsi</h3>
                                                        <button @click="openAbstract = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                    <div class="px-8 py-6 max-h-[70vh] overflow-y-auto">
                                                        <div class="mb-5">
                                                            <p class="text-[10px] font-bold text-orange-600 uppercase tracking-widest mb-1">Judul Rencana</p>
                                                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug">{{ $thesis->title }}</h4>
                                                        </div>
                                                        <div class="border-t border-slate-50 dark:border-slate-700 pt-5">
                                                            <p class="text-[10px] font-bold text-orange-600 uppercase tracking-widest mb-2">Deskripsi</p>
                                                            <div class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-wrap text-justify">
                                                                {{ $thesis->abstract }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                                                        <button type="button" @click="openAbstract = false" class="px-5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
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
                                <td class="py-4 px-6">
                                    @if($thesis->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-600 uppercase tracking-wider">Menunggu</span>
                                    @elseif($thesis->status === 'active')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800 uppercase tracking-wider">Aktif</span>
                                    @elseif($thesis->status === 'completed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 uppercase tracking-wider">Selesai</span>
                                    @endif
                                </td>
                                
                                @if(Auth::user()->role === 'admin')
                                    <td class="py-4 px-6">
                                        @if($thesis->pembimbing1 && $thesis->pembimbing2)
                                            <div class="space-y-2">
                                                <div class="flex items-center">
                                                    <div class="w-5 h-5 rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center text-[10px] font-semibold mr-2">1</div>
                                                    <span class="font-medium text-slate-800 dark:text-slate-200 text-xs">{{ $thesis->pembimbing1->name }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <div class="w-5 h-5 rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center text-[10px] font-semibold mr-2">2</div>
                                                    <span class="font-medium text-slate-800 dark:text-slate-200 text-xs">{{ $thesis->pembimbing2->name }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 text-[11px] uppercase tracking-wider">Belum ditentukan</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if(!$thesis->pembimbing1 || !$thesis->pembimbing2)
                                            <form action="{{ route('theses.assign', $thesis->id) }}" method="POST" class="flex flex-col space-y-2 items-end">
                                                @csrf
                                                {{-- Hint: Usulan Mahasiswa --}}
                                                @if($thesis->requestedPembimbing1 || $thesis->requestedPembimbing2)
                                                    <div class="w-full mb-1 p-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded text-[10px] text-blue-700 dark:text-blue-400">
                                                        <span class="font-semibold block mb-0.5">💡 Usulan mahasiswa:</span>
                                                        @if($thesis->requestedPembimbing1)
                                                            <span class="block">Pembimbing 1: {{ $thesis->requestedPembimbing1->name }}</span>
                                                        @endif
                                                        @if($thesis->requestedPembimbing2)
                                                            <span class="block">Pembimbing 2: {{ $thesis->requestedPembimbing2->name }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="flex space-x-2">
                                                    <select name="pembimbing1_id" required class="block w-40 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-xs sm:leading-6">
                                                        <option value="">Pilih Pembimbing 1...</option>
                                                        @foreach($dosens as $dosen)
                                                            <option value="{{ $dosen->id }}" {{ $thesis->requested_pembimbing1_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <select name="pembimbing2_id" required class="block w-40 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-xs sm:leading-6">
                                                        <option value="">Pilih Pembimbing 2...</option>
                                                        @foreach($dosens as $dosen)
                                                            <option value="{{ $dosen->id }}" {{ $thesis->requested_pembimbing2_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('pembimbing2_id')
                                                    <span class="text-[10px] text-red-500">{{ $message }}</span>
                                                @enderror
                                                <button type="submit" class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 border border-transparent rounded text-xs font-medium text-white transition-colors shadow-sm whitespace-nowrap mt-2">
                                                    Tugaskan
                                                </button>
                                            </form>
                                        @else
                                            <div class="flex flex-col space-y-2 items-end" x-data="{ openEditModal: false }">
                                                <div class="flex w-full space-x-2">
                                                    <button @click="openEditModal = true" class="px-3 py-1.5 flex-1 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 hover:text-orange-600 dark:hover:text-orange-400 hover:border-orange-300 rounded text-xs font-medium transition-colors shadow-sm text-center">
                                                        Edit
                                                    </button>
                                                    <a href="{{ route('theses.logbooks', $thesis->id) }}" class="px-3 py-1.5 flex-1 text-center bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 hover:text-orange-600 dark:hover:text-orange-400 hover:border-orange-300 rounded text-xs font-medium transition-colors shadow-sm">
                                                        Pantau Bimbingan
                                                    </a>
                                                </div>

                                                <!-- Edit Modal -->
                                                <div x-show="openEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                        <!-- Background overlay -->
                                                        <div x-show="openEditModal" x-transition.opacity class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" @click="openEditModal = false" aria-hidden="true"></div>

                                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                                        <!-- Modal panel -->
                                                        <div x-show="openEditModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-100 dark:border-slate-700">
                                                            <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100 dark:border-slate-700">
                                                                <h3 class="text-lg leading-6 font-bold text-slate-800 dark:text-slate-100" id="modal-title">
                                                                    Edit Data Skripsi
                                                                 </h3>
                                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mahasiswa: <span class="font-semibold">{{ $thesis->student->name }}</span></p>
                                                            </div>
                                                            <form action="{{ route('theses.update', $thesis->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="px-4 py-5 sm:p-6 space-y-4">
                                                                    <div>
                                                                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Rencana Judul (Awal)</label>
                                                                        <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded border border-slate-200 dark:border-slate-700 text-sm text-slate-600 dark:text-slate-400 line-clamp-3">
                                                                            {{ $thesis->title }}
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <label for="final_title" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Judul Final</label>
                                                                        <textarea name="final_title" id="final_title" rows="2" class="w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm" required>{{ $thesis->final_title ?? $thesis->title }}</textarea>
                                                                    </div>
                                                                    <div class="grid grid-cols-2 gap-4">
                                                                        <div>
                                                                            <label for="pembimbing1_id" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Pembimbing 1</label>
                                                                            <select name="pembimbing1_id" id="pembimbing1_id" class="w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm" required>
                                                                                @foreach($dosens as $dosen)
                                                                                    <option value="{{ $dosen->id }}" {{ $thesis->pembimbing1_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div>
                                                                            <label for="pembimbing2_id" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Pembimbing 2</label>
                                                                            <select name="pembimbing2_id" id="pembimbing2_id" class="w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm" required>
                                                                                @foreach($dosens as $dosen)
                                                                                    <option value="{{ $dosen->id }}" {{ $thesis->pembimbing2_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="bg-slate-50 dark:bg-slate-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100 dark:border-slate-700">
                                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                                                        Simpan Perubahan
                                                                    </button>
                                                                    <button type="button" @click="openEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-700 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                                                        Batal
                                                                    </button>
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
                            <tr>
                                <td colspan="{{ Auth::user()->role === 'admin' ? '6' : '4' }}" class="py-12 text-center text-slate-500 text-sm">
                                    Belum ada data skripsi yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($theses->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                    {{ $theses->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

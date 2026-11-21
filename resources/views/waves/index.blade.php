<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pengaturan', 'route' => null],
            ['label' => 'Gelombang Pelaksanaan', 'route' => route('waves.index')]
        ]" />
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 rounded-2xl flex items-center gap-3">
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-500/20 rounded-full flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-xs font-bold text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
            </div>
        @endif

        <x-table-card 
            title="Daftar Gelombang Pelaksanaan"
            :footer="$waves->links()">
            
            <x-slot name="headerActions">
                <button type="button" onclick="document.getElementById('modal-add-wave').classList.remove('hidden')" 
                        class="h-10 inline-flex items-center px-4 bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white rounded-xl font-bold text-xs transition-colors shadow-sm cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Gelombang Baru
                </button>
            </x-slot>
            
            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700/80">
                @forelse($waves as $wave)
                    @php
                        $today = now()->toDateString();
                        $isWithinRange = $wave->start_date && $wave->end_date && $today >= $wave->start_date->toDateString() && $today <= $wave->end_date->toDateString();
                        $isActive = $wave->is_active && $isWithinRange;
                    @endphp
                    <div class="p-4 space-y-3 bg-white dark:bg-slate-800">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h4 class="font-bold text-sm text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $wave->name }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Dibuat {{ $wave->created_at->locale('id')->translatedFormat('d M Y') }}</p>
                            </div>
                            <div class="shrink-0">
                                @if($isActive)
                                    <x-status-badge type="emerald" label="AKTIF" />
                                @elseif($wave->is_active && !$isWithinRange)
                                    <x-status-badge type="orange" label="PENDING/EXPIRED" />
                                @else
                                    <x-status-badge type="slate" label="ARSIP" />
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700/70 rounded-md">{{ $wave->start_date ? $wave->start_date->locale('id')->translatedFormat('d M Y') : '-' }}</span>
                            <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700/70 rounded-md">{{ $wave->end_date ? $wave->end_date->locale('id')->translatedFormat('d M Y') : '-' }}</span>
                        </div>

                        @if($wave->description)
                            <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2">{{ $wave->description }}</p>
                        @endif

                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/70">
                            <form action="{{ route('waves.toggle', $wave) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 {{ $wave->is_active ? 'bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60' : 'bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60' }} text-[11px] font-bold rounded-lg transition-colors cursor-pointer">
                                    {{ $wave->is_active ? 'Arsipkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick='editWave(@json($wave))' class="p-2 text-slate-500 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-500/10 rounded-xl transition-colors cursor-pointer" title="Edit Gelombang" aria-label="Edit Gelombang {{ $wave->name }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"></path></svg>
                                </button>
                                <form action="{{ route('waves.destroy', $wave) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus gelombang ini?" data-confirm-title="Hapus Gelombang" data-confirm-type="danger" data-confirm-button="Ya, Hapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors cursor-pointer" title="Hapus Gelombang" aria-label="Hapus Gelombang {{ $wave->name }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Silakan tambahkan gelombang pelaksanaan baru untuk mulai menerima pengajuan seminar dan sidang.</p>
                    </div>
                @endforelse
            </div>

            <table class="hidden sm:table w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/80 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <th class="py-5 px-6 font-black text-[11px] uppercase tracking-[0.2em]">NAMA GELOMBANG</th>
                        <th class="py-5 px-6 font-black text-[11px] uppercase tracking-[0.2em]">RENTANG TANGGAL</th>
                        <th class="py-5 px-6 font-black text-[11px] uppercase tracking-[0.2em]">DESKRIPSI</th>
                        <th class="py-5 px-6 text-center font-black text-[11px] uppercase tracking-[0.2em]">STATUS</th>
                        <th class="py-5 px-6 text-right font-black text-[11px] uppercase tracking-[0.2em]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($waves as $wave)
                        <tr class="group hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors">
                            <td class="py-5 px-6">
                                <div class="font-black text-sm text-slate-800 dark:text-slate-100 uppercase tracking-tight group-hover:text-indigo-600 transition-colors">{{ $wave->name }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 font-medium italic">Dibuat pada {{ $wave->created_at->locale('id')->translatedFormat('d M Y') }}</div>
                            </td>
                            <td class="py-5 px-6">
                                <div class="flex items-center gap-2 text-[11px] font-bold text-slate-600 dark:text-slate-400">
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded">{{ $wave->start_date ? $wave->start_date->locale('id')->translatedFormat('d M Y') : '-' }}</span>
                                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded">{{ $wave->end_date ? $wave->end_date->locale('id')->translatedFormat('d M Y') : '-' }}</span>
                                </div>
                            </td>
                            <td class="py-5 px-6">
                                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium line-clamp-1 italic max-w-[200px]">{{ $wave->description ?: 'Tidak ada deskripsi' }}</p>
                            </td>
                            <td class="py-5 px-6 text-center">
                                @php
                                    $today = now()->toDateString();
                                    $isWithinRange = $wave->start_date && $wave->end_date && $today >= $wave->start_date->toDateString() && $today <= $wave->end_date->toDateString();
                                    $isActive = $wave->is_active && $isWithinRange;
                                @endphp
                                @if($isActive)
                                    <x-status-badge type="emerald" label="AKTIF" />
                                @elseif($wave->is_active && !$isWithinRange)
                                    <x-status-badge type="orange" label="PENDING/EXPIRED" />
                                @else
                                    <x-status-badge type="slate" label="ARSIP" />
                                @endif
                            </td>
                            <td class="py-5 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('waves.toggle', $wave) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 {{ $wave->is_active ? 'bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/50 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60' : 'bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60' }} text-[11px] font-black uppercase rounded-lg transition-colors shadow-2xs cursor-pointer">
                                            @if($wave->is_active)
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            @endif
                                            {{ $wave->is_active ? 'Arsipkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    
                                    <button type="button" onclick='editWave(@json($wave))' 
                                            class="p-2 text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-500/10 rounded-xl transition-colors cursor-pointer"
                                            title="Edit Gelombang"
                                            aria-label="Edit Gelombang {{ $wave->name }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"></path></svg>
                                    </button>

                                    <form action="{{ route('waves.destroy', $wave) }}"
                                          method="POST"
                                          data-confirm="Apakah Anda yakin ingin menghapus gelombang ini?"
                                          data-confirm-title="Hapus Gelombang"
                                          data-confirm-type="danger"
                                          data-confirm-button="Ya, Hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors cursor-pointer" title="Hapus Gelombang" aria-label="Hapus Gelombang {{ $wave->name }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="5" description="Silakan tambahkan gelombang pelaksanaan baru untuk mulai menerima pengajuan seminar dan sidang." icon="wave" />
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>

    <!-- Modal Add Wave -->
    <div id="modal-add-wave" role="dialog" aria-modal="true" aria-labelledby="modal-add-wave-title" class="hidden fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('modal-add-wave').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                <form action="{{ route('waves.store') }}" method="POST">
                    @csrf
                    <div class="px-8 pt-8 pb-6">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 id="modal-add-wave-title" class="text-xl font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Gelombang Baru</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Tambahkan periode pelaksanaan baru</p>
                            </div>
                            <button type="button" onclick="document.getElementById('modal-add-wave').classList.add('hidden')" aria-label="Tutup modal tambah gelombang" class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-rose-600 transition-colors rounded-xl cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Nama Gelombang</label>
                                <input type="text" name="name" required placeholder="Contoh: Gelombang 1 2024/2025"
                                       class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl text-sm font-bold focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all shadow-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Tanggal Mulai</label>
                                    <input type="date" name="start_date" required 
                                           class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-2xl text-sm font-bold focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Tanggal Selesai</label>
                                    <input type="date" name="end_date" required 
                                           class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-2xl text-sm font-bold focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all shadow-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Deskripsi Singkat</label>
                                <textarea name="description" rows="3" placeholder="Tambahkan catatan jika perlu..."
                                          class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl text-sm font-medium focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all shadow-sm italic"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="px-8 py-6 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3 items-center">
                        <button type="button" onclick="document.getElementById('modal-add-wave').classList.add('hidden')" 
                                class="px-6 py-2.5 text-[11px] font-black text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 uppercase tracking-widest transition-colors cursor-pointer">Batal</button>
                        <button type="submit" 
                                class="px-8 py-2.5 bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white text-[11px] font-black uppercase tracking-widest rounded-xl transition-colors shadow-sm cursor-pointer">
                            Simpan Gelombang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Wave -->
    <div id="modal-edit-wave" role="dialog" aria-modal="true" aria-labelledby="modal-edit-wave-title" class="hidden fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('modal-edit-wave').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700 relative z-10">
                <form id="form-edit-wave" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-8 pt-8 pb-6">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 id="modal-edit-wave-title" class="text-xl font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Edit Gelombang</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Perbarui informasi gelombang</p>
                            </div>
                            <button type="button" onclick="document.getElementById('modal-edit-wave').classList.add('hidden')" aria-label="Tutup modal edit gelombang" class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-rose-600 transition-colors rounded-xl cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Nama Gelombang</label>
                                <input type="text" id="edit-name" name="name" required placeholder="Nama Gelombang"
                                       class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl text-sm font-bold focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all shadow-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Tanggal Mulai</label>
                                    <input type="date" id="edit-start-date" name="start_date" required 
                                           class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-2xl text-sm font-bold focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Tanggal Selesai</label>
                                    <input type="date" id="edit-end-date" name="end_date" required 
                                           class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-2xl text-sm font-bold focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all shadow-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Deskripsi Singkat</label>
                                <textarea id="edit-description" name="description" rows="3" placeholder="Deskripsi..."
                                          class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl text-sm font-medium focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all shadow-sm italic"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="px-8 py-6 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3 items-center">
                        <button type="button" onclick="document.getElementById('modal-edit-wave').classList.add('hidden')" 
                                class="px-6 py-2.5 text-[11px] font-black text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 uppercase tracking-widest transition-colors cursor-pointer">Batal</button>
                        <button type="submit" 
                                class="px-8 py-2.5 bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white text-[11px] font-black uppercase tracking-widest rounded-xl transition-colors shadow-sm cursor-pointer">
                            Update Gelombang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editWave(wave) {
            const modal = document.getElementById('modal-edit-wave');
            const form = document.getElementById('form-edit-wave');
            const nameInput = document.getElementById('edit-name');
            const startInput = document.getElementById('edit-start-date');
            const endInput = document.getElementById('edit-end-date');
            const descInput = document.getElementById('edit-description');

            form.action = `/waves/${wave.id}`;
            nameInput.value = wave.name;
            
            // Format dates for input type="date" (YYYY-MM-DD)
            if (wave.start_date) {
                startInput.value = wave.start_date.split('T')[0];
            }
            if (wave.end_date) {
                endInput.value = wave.end_date.split('T')[0];
            }
            
            descInput.value = wave.description || '';

            modal.classList.remove('hidden');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.getElementById('modal-add-wave')?.classList.add('hidden');
                document.getElementById('modal-edit-wave')?.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>

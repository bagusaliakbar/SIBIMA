<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Manajemen Pengguna', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full mx-auto" x-data="{ openImportModal: false }">
        @if(session('skippedDetails'))
            <div class="mb-6 p-6 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 rounded-2xl shadow-sm relative overflow-hidden transition-all duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/5 rounded-full -mr-12 -mt-12 opacity-50"></div>
                <div class="flex items-center gap-4 mb-4 z-10 relative">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-widest text-slate-800 dark:text-slate-100">Detail Baris yang Dilewati ({{ count(session('skippedDetails')) }} Baris)</h4>
                        <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5 tracking-tight">Data berikut diabaikan demi menjaga validitas data di database:</p>
                    </div>
                </div>
                <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-900/30 z-10 relative">
                    @foreach(session('skippedDetails') as $detail)
                        <div class="px-4 py-3.5 flex flex-wrap justify-between items-center gap-2 text-[10px] text-slate-600 dark:text-slate-400 font-bold uppercase tracking-wider">
                            <span class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Baris {{ $detail['row'] }} (ID/NPM: <span class="font-mono text-slate-700 dark:text-slate-300">{{ $detail['identifier'] }}</span>)
                            </span>
                            <span class="text-[9px] px-2.5 py-1 bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 rounded-lg border border-rose-100 dark:border-rose-900/20 font-black tracking-widest">{{ $detail['reason'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <x-table-card 
            title="Manajemen Pengguna"
            :footer="$users->links()">
            
            <x-slot name="headerActions">
                <div class="flex flex-wrap items-center gap-3">
                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari nama, NPM..." 
                        route="users.index" />
                    
                    <div class="flex items-center gap-2">
                        <a href="{{ route('users.export') }}" class="inline-flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-black text-[10px] text-slate-600 dark:text-slate-400 uppercase tracking-widest hover:bg-slate-100 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Export
                        </a>
                        <button type="button" @click="openImportModal = true" class="inline-flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-black text-[10px] text-slate-600 dark:text-slate-400 uppercase tracking-widest hover:bg-slate-100 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import
                        </button>
                        <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-500/20">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Baru
                        </a>
                    </div>
                </div>
            </x-slot>

            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                        <th scope="col" class="py-4 px-6">Pengguna</th>
                        <th scope="col" class="py-4 px-6 text-center">Peran</th>
                        <th scope="col" class="py-4 px-6 text-center">NPM / NIDN</th>
                        <th scope="col" class="py-4 px-6 text-center">Status</th>
                        <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-500 flex items-center justify-center font-black text-xs mr-4 border border-orange-200 dark:border-orange-500/20 group-hover:scale-110 transition-transform">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $user->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5 tracking-tighter">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <x-status-badge 
                                    :type="$user->role === 'dosen' ? 'blue' : ($user->role === 'kaprodi' ? 'orange' : 'slate')" 
                                    :label="strtoupper($user->role)" />
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="font-black text-[10px] text-slate-600 dark:text-slate-400 uppercase tracking-widest">{{ $user->identifier }}</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <form action="{{ route('users.toggle', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit">
                                        @if($user->is_active)
                                            <x-status-badge type="emerald" label="AKTIF" />
                                        @else
                                            <x-status-badge type="orange" label="PENDING" />
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="5" description="Belum ada data mahasiswa atau dosen yang terdaftar." icon="user" />
                    @endforelse
                </tbody>
            </table>
        </x-table-card>

        <!-- Import Modal -->
        <div x-show="openImportModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openImportModal = false">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-100 dark:border-slate-700">
                    <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">
                            Import Data Pengguna
                        </h3>
                        <p class="text-[10px] text-slate-500 uppercase font-black mt-1">Silakan gunakan template Excel yang sesuai</p>
                        
                        <div class="mt-6 p-4 bg-orange-50 dark:bg-orange-500/5 rounded-xl border border-orange-100 dark:border-orange-500/10">
                            <h4 class="text-[10px] font-black text-orange-700 dark:text-orange-400 uppercase tracking-widest mb-2">Format Kolom:</h4>
                            <p class="font-mono text-[9px] text-orange-600 dark:text-orange-500 bg-white/50 dark:bg-slate-900/50 p-2 rounded-lg border border-orange-100 dark:border-orange-500/10">Nama, Email, Peran, NPM/NIDN, Tahun Angkatan, No_WhatsApp, Status Aktif (1/0)</p>
                            <div class="mt-2 space-y-1 text-[9px] text-orange-600/70 dark:text-orange-400/70 font-bold uppercase">
                                <p>* Peran: dosen atau mahasiswa</p>
                                <p>* Tahun Angkatan: Khusus mahasiswa (cth: 2020)</p>
                                <p>* Status Aktif: 1 untuk Aktif, 0 untuk Pending</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-8 py-8">
                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Pilih File Excel (.xlsx, .xls)</label>
                            <div class="relative group">
                                <input type="file" name="excel_file" required class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-orange-600 file:text-white hover:file:bg-orange-700 transition-all cursor-pointer bg-slate-50 dark:bg-slate-900 rounded-xl p-2 border border-dashed border-slate-200 dark:border-slate-700">
                            </div>
                        </div>
                        <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                            <button type="button" @click="openImportModal = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors">Batal</button>
                            <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-500/20 transition-all">Mulai Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

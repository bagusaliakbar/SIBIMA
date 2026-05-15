<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pengumuman', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div x-data="{ openModal: false, editMode: false, currentAnnouncement: {} }" class="space-y-6">
            
            <x-table-card 
                title="Manajemen Pengumuman"
                :footer="$announcements->links()">
                
                <x-slot name="headerActions">
                    <button @click="openModal = true; editMode = false; currentAnnouncement = {}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-700 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Buat Baru
                    </button>
                </x-slot>

                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap">Tanggal</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap">Judul Pengumuman</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap text-center">Tipe</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap text-center">Status</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($announcements as $announcement)
                            <tr class="bg-white dark:bg-slate-800 hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors">
                                <td class="py-4 px-6 text-slate-500 dark:text-slate-400 whitespace-nowrap text-xs font-bold">
                                    {{ $announcement->created_at->locale('id')->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800 dark:text-slate-100">{{ $announcement->title }}</div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5 uppercase font-medium">{{ Str::limit($announcement->content, 80) }}</div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <x-status-badge 
                                        :type="$announcement->type === 'info' ? 'blue' : ($announcement->type === 'warning' ? 'orange' : 'red')" 
                                        :label="strtoupper($announcement->type)" />
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <form action="{{ route('announcements.toggle', $announcement->id) }}" method="POST">
                                        @csrf
                                        <button type="submit">
                                            @if($announcement->is_active)
                                                <x-status-badge type="emerald" label="AKTIF" />
                                            @else
                                                <x-status-badge type="slate" label="NON-AKTIF" />
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button @click="openModal = true; editMode = true; currentAnnouncement = {{ json_encode($announcement) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
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
                            <x-empty-state colspan="5" description="Semua pengumuman sistem akan tampil di sini" icon="announcement" />
                        @endforelse
                    </tbody>
                </table>
            </x-table-card>

            <!-- Modal for Create/Edit -->
            <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openModal = false">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                        <form :action="editMode ? '{{ url('announcements') }}/' + currentAnnouncement.id : '{{ route('announcements.store') }}'" method="POST">
                            @csrf
                            <template x-if="editMode">
                                <input type="hidden" name="_method" value="PATCH">
                            </template>
    
                            <div class="px-8 py-8">
                                <div class="mb-8">
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest" x-text="editMode ? 'Edit Pengumuman' : 'Buat Pengumuman Baru'"></h3>
                                    <p class="text-[10px] text-slate-500 uppercase font-black mt-1">Informasi ini akan ditampilkan kepada seluruh pengguna</p>
                                </div>

                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Judul Pengumuman</label>
                                        <input type="text" name="title" x-model="currentAnnouncement.title" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Tipe</label>
                                        <select name="type" x-model="currentAnnouncement.type" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all">
                                            <option value="info">Informasi (Biru)</option>
                                            <option value="warning">Penting (Oranye)</option>
                                            <option value="important">Mendesak (Merah)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Isi Pengumuman</label>
                                        <textarea name="content" x-model="currentAnnouncement.content" rows="6" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all" placeholder="Tuliskan detail informasi di sini..."></textarea>
                                    </div>

                                    <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-700">
                                        <input type="checkbox" name="is_active" id="is_active" x-model="currentAnnouncement.is_active" class="w-5 h-5 rounded-md border-slate-300 dark:border-slate-700 text-orange-600 focus:ring-orange-500 bg-white dark:bg-slate-800">
                                        <label for="is_active" class="text-[11px] font-black text-slate-600 dark:text-slate-400 uppercase tracking-tight">Tampilkan pengumuman ini segera</label>
                                    </div>
                                </div>
                            </div>

                            <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                <button type="button" @click="openModal = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors">Batal</button>
                                <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-500/20 transition-all" x-text="editMode ? 'Simpan Perubahan' : 'Terbitkan Sekarang'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

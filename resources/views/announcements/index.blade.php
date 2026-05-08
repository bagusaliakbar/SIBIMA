<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pengumuman', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div x-data="{ openModal: false, editMode: false, currentAnnouncement: {} }" class="space-y-6">
            
            <!-- Button to open create modal -->
            <div class="flex justify-end">
                <button @click="openModal = true; editMode = false; currentAnnouncement = {}" class="px-4 py-2 bg-orange-600 text-white text-sm font-bold rounded-md hover:bg-orange-700 transition-colors shadow-sm">
                    + Buat Pengumuman Baru
                </button>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                                <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">TANGGAL</th>
                                <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">JUDUL PENGUMUMAN</th>
                                <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center">TIPE</th>
                                <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center">STATUS</th>
                                <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($announcements as $announcement)
                                <tr class="bg-white dark:bg-slate-800 hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors">
                                    <td class="py-4 px-6 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                        {{ $announcement->created_at->format('d M Y') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-800 dark:text-slate-100">{{ $announcement->title }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">{{ Str::limit($announcement->content, 80) }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                            {{ $announcement->type === 'info' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800' : '' }}
                                            {{ $announcement->type === 'warning' ? 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border border-orange-100 dark:border-orange-800' : '' }}
                                            {{ $announcement->type === 'important' ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-800' : '' }}
                                        ">
                                            {{ $announcement->type }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('announcements.toggle', $announcement->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center">
                                                @if($announcement->is_active)
                                                    <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-100 dark:border-emerald-800 uppercase tracking-tight">Aktif</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 text-[10px] font-bold border border-slate-200 dark:border-slate-600 uppercase tracking-tight">Non-aktif</span>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="openModal = true; editMode = true; currentAnnouncement = {{ json_encode($announcement) }}" class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                                            <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Belum ada pengumuman</h3>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest font-black">Semua pengumuman sistem akan tampil di sini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($announcements->hasPages())
                    <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                        {{ $announcements->links() }}
                    </div>
                @endif
            </div>

            <!-- Modal for Create/Edit -->
            <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openModal = false">
                        <div class="absolute inset-0 bg-slate-900 bg-opacity-75 dark:bg-opacity-90"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                        <form :action="editMode ? '{{ url('announcements') }}/' + currentAnnouncement.id : '{{ route('announcements.store') }}'" method="POST">
                            @csrf
                            <template x-if="editMode">
                                <input type="hidden" name="_method" value="PATCH">
                            </template>
    
                            <div class="bg-white dark:bg-slate-800 px-6 py-6">
                                <div class="mb-4">
                                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100" x-text="editMode ? 'Edit Pengumuman' : 'Buat Pengumuman Baru'"></h3>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Judul Pengumuman</label>
                                        <input type="text" name="title" x-model="currentAnnouncement.title" required class="block w-full rounded-md border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Tipe</label>
                                        <select name="type" x-model="currentAnnouncement.type" required class="block w-full rounded-md border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500">
                                            <option value="info">Info (Biru)</option>
                                            <option value="warning">Penting (Oranye)</option>
                                            <option value="important">Mendesak (Merah)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Isi Pengumuman</label>
                                        <textarea name="content" x-model="currentAnnouncement.content" rows="6" required class="block w-full rounded-md border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Tuliskan detail informasi di sini..."></textarea>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="is_active" x-model="currentAnnouncement.is_active" class="rounded border-slate-300 dark:border-slate-700 text-orange-600 focus:ring-orange-500 bg-white dark:bg-slate-900">
                                        <label for="is_active" class="ml-2 text-sm text-slate-600 dark:text-slate-400">Tampilkan pengumuman ini segera</label>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                                <button type="button" @click="openModal = false" class="px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-orange-600 text-white text-sm font-bold rounded-md hover:bg-orange-700 shadow-sm" x-text="editMode ? 'Simpan Perubahan' : 'Terbitkan Sekarang'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

        @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
            <!-- Modal Edit Arsip Pustaka -->
            <template x-teleport="body">
                <div x-show="editModalOpen" 
                     @keydown.escape.window="if (editModalOpen) editModalOpen = false"
                     role="dialog"
                     aria-modal="true"
                     class="fixed inset-0 z-[60] overflow-y-auto text-left" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="editModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-200 dark:border-slate-700 relative z-10">
                            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Edit Data Arsip Pustaka</h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold mt-0.5">Koreksi judul, nama mahasiswa, atau data pembimbing</p>
                                </div>
                                <button type="button" @click="editModalOpen = false" class="text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold p-1">&times;</button>
                            </div>
                            <form :action="'/repositories/' + editData.id" method="POST" class="p-8 space-y-4">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Nama Mahasiswa -->
                                    <div>
                                        <label class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">Nama Mahasiswa <span class="text-rose-500">*</span></label>
                                        <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>
                                    
                                    <!-- NPM / Identifier -->
                                    <div>
                                        <label class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">NPM / Identifier</label>
                                        <input type="text" name="identifier" x-model="editData.identifier" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>
                                </div>

                                <!-- Judul Skripsi -->
                                <div>
                                    <label class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">Judul Skripsi <span class="text-rose-500">*</span></label>
                                    <textarea name="title" x-model="editData.title" required rows="3" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 leading-relaxed"></textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <!-- Tahun Angkatan -->
                                    <div>
                                        <label class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">Tahun Angkatan</label>
                                        <input type="number" name="year" x-model="editData.year" placeholder="YYYY" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>

                                    <!-- Pembimbing 1 -->
                                    <div>
                                        <label class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">Pembimbing 1</label>
                                        <input type="text" name="pembimbing1" x-model="editData.pembimbing1" placeholder="Nama P1" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>

                                    <!-- Pembimbing 2 -->
                                    <div>
                                        <label class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">Pembimbing 2</label>
                                        <input type="text" name="pembimbing2" x-model="editData.pembimbing2" placeholder="Nama P2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    </div>
                                </div>

                                <!-- Abstrak -->
                                <div>
                                    <label class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">Abstrak</label>
                                    <textarea name="abstract" x-model="editData.abstract" rows="4" placeholder="Abstrak skripsi..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-normal text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 leading-relaxed"></textarea>
                                </div>

                                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                                    <button type="button" @click="editModalOpen = false" class="px-5 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl font-bold text-xs shadow-md shadow-orange-500/20 hover:scale-[1.02] transition-all">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Modal Hapus Arsip Pustaka -->
            <template x-teleport="body">
                <div x-show="deleteModalOpen" 
                     @keydown.escape.window="if (deleteModalOpen) deleteModalOpen = false"
                     role="dialog"
                     aria-modal="true"
                     class="fixed inset-0 z-[60] overflow-y-auto text-left" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="deleteModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200 dark:border-slate-700 relative z-10">
                            <div class="p-6">
                                <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </div>
                                <h3 class="text-base font-black text-center text-slate-800 dark:text-slate-100 uppercase tracking-tight">Hapus Arsip Skripsi?</h3>
                                <p class="text-xs text-center text-slate-500 dark:text-slate-400 mt-2">
                                    Apakah Anda yakin ingin menghapus data arsip berikut dari katalog pustaka?
                                </p>
                                <div class="my-4 p-3 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 line-clamp-2" x-text="deleteData.title"></div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-semibold" x-text="'Mahasiswa: ' + deleteData.name"></div>
                                </div>

                                <form :action="'/repositories/' + deleteData.id" method="POST" class="flex items-center justify-end gap-3 pt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl font-bold text-xs shadow-md shadow-rose-500/20 hover:scale-[1.02] transition-all">
                                        Ya, Hapus Arsip
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        @endif

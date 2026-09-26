<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Kelola FAQ', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div x-data="{ 
            openModal: false, 
            editMode: false, 
            currentFaq: {
                id: null,
                question: '',
                answer: '',
                category: 'bimbingan',
                target_role: 'all',
                order: 0,
                is_active: true
            },
            openCreateModal() {
                this.editMode = false;
                this.currentFaq = {
                    id: null,
                    question: '',
                    answer: '',
                    category: 'bimbingan',
                    target_role: 'all',
                    order: 0,
                    is_active: true
                };
                this.openModal = true;
            },
            openEditModal(faq) {
                this.editMode = true;
                this.currentFaq = {
                    id: faq.id,
                    question: faq.question,
                    answer: faq.answer,
                    category: faq.category,
                    target_role: faq.target_role,
                    order: faq.order,
                    is_active: Boolean(faq.is_active)
                };
                this.openModal = true;
            }
        }" class="space-y-6">

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 border border-orange-100 dark:border-orange-900/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Total FAQ</div>
                        <div class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-0.5">{{ $stats['total'] }}</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-900/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">FAQ Aktif (Tampil)</div>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $stats['active'] }}</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-600/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">FAQ Disembunyikan</div>
                        <div class="text-2xl font-black text-slate-700 dark:text-slate-300 mt-0.5">{{ $stats['inactive'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Main Table Card with Search & Filters -->
            <x-table-card 
                title="Daftar Pertanyaan & Jawaban (FAQ)"
                :footer="$faqs->links()">
                
                <x-slot name="headerActions">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('faqs.index') }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-xs" title="Lihat Tampilan Pengguna">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Lihat Halaman FAQ
                        </a>
                        <button @click="openCreateModal()" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-700 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            Tambah FAQ Baru
                        </button>
                    </div>
                </x-slot>

                <!-- Filter & Search Toolbar -->
                <div class="p-4 sm:p-5 bg-slate-50/50 dark:bg-slate-900/30 border-b border-slate-100 dark:border-slate-700/80">
                    <form method="GET" action="{{ route('faqs.manage') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="sm:col-span-2">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pertanyaan atau jawaban..." class="block w-full pl-9 pr-3 py-2 text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                            </div>
                        </div>

                        <div>
                            <select name="category" onchange="this.form.submit()" class="block w-full py-2 px-3 text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                <option value="all">Semua Kategori</option>
                                @foreach($categories as $key => $cat)
                                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <select name="role" onchange="this.form.submit()" class="block w-full py-2 px-3 text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                <option value="all">Semua Target</option>
                                <option value="all" {{ request('role') === 'all' ? 'selected' : '' }}>Semua Pengguna</option>
                                <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                            </select>
                        </div>
                    </form>
                </div>

                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-4 px-4 font-black text-[10px] tracking-widest uppercase text-center w-12">No</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase">Pertanyaan & Ringkasan Jawaban</th>
                            <th class="py-4 px-4 font-black text-[10px] tracking-widest uppercase text-center whitespace-nowrap">Kategori</th>
                            <th class="py-4 px-4 font-black text-[10px] tracking-widest uppercase text-center whitespace-nowrap">Target</th>
                            <th class="py-4 px-4 font-black text-[10px] tracking-widest uppercase text-center whitespace-nowrap">Status</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($faqs as $faq)
                            <tr class="bg-white dark:bg-slate-800 hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors">
                                <td class="py-4 px-4 text-center font-mono text-xs text-slate-400 dark:text-slate-500 font-bold">
                                    {{ $faq->order }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-snug">{{ $faq->question }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-1 leading-relaxed">{{ Str::limit(strip_tags($faq->answer), 120) }}</div>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @php
                                        $catColor = $categories[$faq->category]['color'] ?? 'slate';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                        {{ $catColor === 'orange' ? 'bg-orange-50 text-orange-700 border border-orange-200 dark:bg-orange-950/50 dark:text-orange-300 dark:border-orange-800' : '' }}
                                        {{ $catColor === 'emerald' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800' : '' }}
                                        {{ $catColor === 'indigo' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800' : '' }}
                                        {{ $catColor === 'amber' ? 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800' : '' }}
                                        {{ $catColor === 'blue' ? 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800' : '' }}
                                        {{ $catColor === 'slate' ? 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600' : '' }}">
                                        {{ $faq->category_label }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($faq->target_role === 'all')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">Semua</span>
                                    @elseif($faq->target_role === 'mahasiswa')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Mahasiswa</span>
                                    @elseif($faq->target_role === 'dosen')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border border-purple-200 dark:border-purple-800">Dosen</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <form action="{{ route('faqs.toggle', $faq->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Klik untuk ubah status">
                                            @if($faq->is_active)
                                                <x-status-badge type="emerald" label="AKTIF" />
                                            @else
                                                <x-status-badge type="slate" label="NON-AKTIF" />
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex justify-end items-center gap-1">
                                        <button @click="openEditModal({{ json_encode($faq) }})" class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Edit FAQ">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan FAQ ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Hapus FAQ">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="6" description="Belum ada data FAQ yang sesuai filter pencarian." icon="announcement" />
                        @endforelse
                    </tbody>
                </table>
            </x-table-card>

            <!-- Modal for Create / Edit FAQ -->
            <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openModal = false">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100 dark:border-slate-700">
                        <form :action="editMode ? '{{ url('manage/faqs') }}/' + currentFaq.id : '{{ route('faqs.store') }}'" method="POST">
                            @csrf
                            <template x-if="editMode">
                                <input type="hidden" name="_method" value="PUT">
                            </template>
    
                            <div class="px-8 py-7">
                                <div class="mb-6 flex justify-between items-start">
                                    <div>
                                        <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider" x-text="editMode ? 'Edit Pertanyaan & Jawaban FAQ' : 'Tambah FAQ Baru'"></h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola panduan dan jawaban bantuan untuk mahasiswa dan dosen</p>
                                    </div>
                                    <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pertanyaan <span class="text-red-500">*</span></label>
                                        <input type="text" name="question" x-model="currentFaq.question" required placeholder="Contoh: Berapa kali minimal bimbingan untuk daftar seminar?" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm font-semibold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                                            <select name="category" x-model="currentFaq.category" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-bold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                                @foreach($categories as $catKey => $cat)
                                                    <option value="{{ $catKey }}">{{ $cat['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Target Pengguna <span class="text-red-500">*</span></label>
                                            <select name="target_role" x-model="currentFaq.target_role" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-bold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                                <option value="all">Semua Pengguna</option>
                                                <option value="mahasiswa">Khusus Mahasiswa</option>
                                                <option value="dosen">Khusus Dosen</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Urutan Prioritas</label>
                                            <input type="number" name="order" x-model="currentFaq.order" min="0" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-bold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jawaban Lengkap <span class="text-red-500">*</span></label>
                                        <textarea name="answer" x-model="currentFaq.answer" rows="7" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm font-medium focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" placeholder="Tuliskan penjelasan dan langkah-langkah jawaban secara detail..."></textarea>
                                        <p class="text-[11px] text-slate-400 mt-1">Mendukung format baris baru, penomoran poin, dan tanda bintang ganda (**) untuk cetak tebal.</p>
                                    </div>

                                    <div class="flex items-center gap-3 p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700">
                                        <input type="checkbox" name="is_active" id="faq_is_active" value="1" x-model="currentFaq.is_active" class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500 bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600">
                                        <label for="faq_is_active" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Publikasikan FAQ ini (langsung tampil di Pusat Bantuan)</label>
                                    </div>
                                </div>
                            </div>

                            <div class="px-8 py-5 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2.5">
                                <button type="button" @click="openModal = false" class="px-5 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">Batal</button>
                                <button type="submit" class="px-5 py-2 bg-orange-600 text-white text-xs font-bold rounded-xl hover:bg-orange-700 shadow-md shadow-orange-500/20 transition-all" x-text="editMode ? 'Simpan Perubahan' : 'Simpan FAQ'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

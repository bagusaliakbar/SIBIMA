<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pendaftaran Seminar', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6">
        {{-- Wave Filter & Template Management Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Wave Filter --}}
            <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em]">Gelombang Pelaksanaan</h3>
                    <div class="flex items-center gap-2 mt-2 font-black text-sm text-slate-800 dark:text-slate-100 uppercase tracking-tight">
                        @if($activeWave)
                            <span class="bg-indigo-50 dark:bg-indigo-500/10 px-3 py-1 rounded-lg border border-indigo-100 dark:border-indigo-500/20 shadow-sm">{{ $activeWave->name }}</span>
                        @else
                            <span class="bg-rose-50 dark:bg-rose-500/10 px-3 py-1 rounded-lg border border-rose-100 dark:border-rose-500/20 text-rose-600 uppercase tracking-tighter">Tidak Ada Gelombang Aktif</span>
                        @endif
                    </div>
                </div>

                <form action="{{ route('seminar-applications.index') }}" method="GET" class="relative group w-full md:w-auto">
                    <select name="wave_id" onchange="this.form.submit()" 
                            class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm min-w-[220px]">
                        <option value="">SEMUA GELOMBANG</option>
                        @foreach($waves as $wave)
                            <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                {{ strtoupper($wave->name) }} {{ $wave->is_active ? '(AKTIF)' : '(ARSIP)' }} — [{{ $wave->app_count }} Mhs]
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Template Management --}}
            <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-[0.2em]">Templat Formulir</h3>
                    @if($template)
                        <div class="flex items-center gap-3 mt-2">
                            <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[11px] font-black text-slate-800 dark:text-slate-100 uppercase truncate max-w-[150px] tracking-tighter">{{ $template->title }}</div>
                                <a href="{{ route('download.private', ['path' => $template->file_path]) }}" target="_blank" class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest hover:underline">Download File</a>
                            </div>
                        </div>
                    @else
                        <div class="text-[11px] font-black text-slate-400 uppercase mt-2 italic tracking-widest">Belum Ada Templat</div>
                    @endif
                </div>

                <div x-data="{ open: false }">
                    <button @click="open = true" class="w-full md:w-auto px-6 py-2.5 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Update Templat
                    </button>

                    <!-- Upload Template Modal -->
                    <div x-show="open" class="fixed inset-0 z-[110] overflow-y-auto" x-cloak x-transition>
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="open = false">
                                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                            </div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-100 dark:border-slate-700">
                                <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700">
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Update Templat Formulir</h3>
                                    <p class="text-[10px] text-slate-500 uppercase font-black mt-1 tracking-widest">File ini akan diunduh oleh mahasiswa</p>
                                </div>
                                <form action="{{ route('seminar-applications.upload-template') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="px-8 py-8 space-y-6">
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Judul Formulir</label>
                                            <input type="text" name="title" required class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black uppercase focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all p-3" placeholder="Contoh: Formulir Pendaftaran Seminar 2024">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Pilih File (PDF/DOCX)</label>
                                            <input type="file" name="template_file" required accept=".pdf,.doc,.docx" class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-orange-600 file:text-white hover:file:bg-orange-700 transition-all cursor-pointer bg-slate-50 dark:bg-slate-900 rounded-xl p-2 border border-dashed border-slate-200 dark:border-slate-700">
                                        </div>
                                    </div>
                                    <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                        <button type="button" @click="open = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors">Batal</button>
                                        <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-500/20 transition-all">Upload Templat</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-table-card 
            title="Antrean Pengajuan Seminar"
            :footer="$applications->links()">
            
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                        <th class="py-4 px-6">Mahasiswa</th>
                        <th class="py-4 px-6">Berkas Persyaratan</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $app->thesis->student->name }}</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-black tracking-widest uppercase">{{ $app->thesis->student->identifier }}</div>
                                <div class="mt-3 flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-4 h-4 rounded bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[9px] font-black border border-indigo-100 dark:border-indigo-500/20">1</span>
                                        <span class="font-black text-slate-500 dark:text-slate-400 text-[9px] uppercase tracking-tighter">{{ $app->thesis->pembimbing1->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-4 h-4 rounded bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[9px] font-black border border-indigo-100 dark:border-indigo-500/20">2</span>
                                        <span class="font-black text-slate-500 dark:text-slate-400 text-[9px] uppercase tracking-tighter">{{ $app->thesis->pembimbing2->name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="grid grid-cols-2 gap-2 max-w-xs">
                                    @php
                                        $files = [
                                            ['label' => 'Bukti ACC', 'path' => $app->file_acc_pembimbing, 'color' => 'orange'],
                                            ['label' => 'Bukti Bayar', 'path' => $app->file_pembayaran, 'color' => 'emerald'],
                                            ['label' => 'Logbook', 'path' => $app->file_kartu_bimbingan, 'color' => 'blue'],
                                            ['label' => 'Draf Skripsi', 'path' => $app->file_skripsi, 'color' => 'indigo'],
                                            ['label' => 'Formulir', 'path' => $app->file_formulir, 'color' => 'pink'],
                                        ];
                                    @endphp
                                    @foreach($files as $file)
                                        <a href="{{ route('download.private', ['path' => $file['path']]) }}" target="_blank" 
                                           class="flex items-center px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-lg text-[9px] font-black uppercase tracking-tighter text-slate-600 dark:text-slate-400 hover:bg-{{ $file['color'] }}-50 dark:hover:bg-{{ $file['color'] }}-500/10 hover:text-{{ $file['color'] }}-600 transition-all group/file">
                                            <svg class="w-3 h-3 mr-1.5 text-slate-400 group-hover/file:text-{{ $file['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            {{ $file['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    @if($app->status === 'approved')
                                        <x-status-badge type="emerald" label="DISETUJUI" />
                                    @elseif($app->status === 'rejected')
                                        <x-status-badge type="rose" label="DITOLAK" />
                                    @else
                                        <x-status-badge type="amber" label="MENUNGGU" />
                                    @endif
                                    
                                    @if($app->admin_feedback)
                                        <p class="text-[8px] text-slate-400 font-black uppercase tracking-tighter mt-1 max-w-[120px] truncate" title="{{ $app->admin_feedback }}">"{{ $app->admin_feedback }}"</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex justify-end items-center gap-2" x-data="{ openValidation: false }">
                                    <a href="{{ route('seminar-applications.download-zip', $app->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 transition-all group/zip" title="Download ZIP">
                                        <svg class="w-5 h-5 group-hover/zip:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                    <button @click="openValidation = true" class="px-4 py-2 bg-indigo-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">
                                        Validasi
                                    </button>
                                    
                                    <!-- Validation Modal -->
                                    <div x-show="openValidation" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak x-transition>
                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openValidation = false">
                                                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                                            </div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-100 dark:border-slate-700">
                                                <div class="px-8 py-8 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Validasi Berkas</h3>
                                                    <button @click="openValidation = false" class="text-slate-400 hover:text-slate-600">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>

                                                <form action="{{ route('seminar-applications.validate', $app->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="px-8 py-8 space-y-6">
                                                        <div>
                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Verifikasi Setiap Berkas:</label>
                                                            <div class="space-y-3">
                                                                @foreach(['file_acc_pembimbing' => 'ACC Pembimbing', 'file_pembayaran' => 'Bukti Bayar', 'file_kartu_bimbingan' => 'Logbook', 'file_skripsi' => 'Draf Skripsi', 'file_formulir' => 'Formulir'] as $field => $label)
                                                                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700">
                                                                        <div class="flex items-center justify-between mb-3">
                                                                            <span class="text-[11px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">{{ $label }}</span>
                                                                            <div class="flex items-center gap-3">
                                                                                <label class="inline-flex items-center cursor-pointer group/radio">
                                                                                    <input type="radio" name="file_reviews[{{ $field }}][status]" value="approved" {{ !isset($app->file_reviews[$field]['status']) || $app->file_reviews[$field]['status'] === 'approved' ? 'checked' : '' }} class="w-3 h-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600">
                                                                                    <span class="ml-2 text-[9px] font-black text-emerald-600 uppercase">OK</span>
                                                                                </label>
                                                                                <label class="inline-flex items-center cursor-pointer group/radio">
                                                                                    <input type="radio" name="file_reviews[{{ $field }}][status]" value="rejected" {{ isset($app->file_reviews[$field]['status']) && $app->file_reviews[$field]['status'] === 'rejected' ? 'checked' : '' }} class="w-3 h-3 text-rose-600 focus:ring-rose-500 border-slate-300 dark:border-slate-600">
                                                                                    <span class="ml-2 text-[9px] font-black text-rose-600 uppercase">TOLAK</span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <input type="text" name="file_reviews[{{ $field }}][note]" value="{{ $app->file_reviews[$field]['note'] ?? '' }}" placeholder="Catatan revisi jika ditolak..." class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-bold p-2 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <div class="pt-6 border-t border-slate-100 dark:border-slate-700">
                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Status Akhir Pengajuan:</label>
                                                            <select name="status" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-black uppercase tracking-widest p-3 focus:ring-4 focus:ring-indigo-500/10">
                                                                <option value="approved" {{ $app->status === 'approved' ? 'selected' : '' }}>SETUJUI (BERKAS VALID)</option>
                                                                <option value="rejected" {{ $app->status === 'rejected' ? 'selected' : '' }}>TOLAK (PERLU REVISI)</option>
                                                                <option value="pending" {{ $app->status === 'pending' ? 'selected' : '' }}>TETAP MENUNGGU</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div>
                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Catatan Admin (Global):</label>
                                                            <textarea name="admin_feedback" rows="3" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold p-4 focus:ring-4 focus:ring-indigo-500/10 italic" placeholder="Silakan upload ulang berkas yang ditolak...">{{ $app->admin_feedback }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                                        <button type="button" @click="openValidation = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors">Batal</button>
                                                        <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-500/20 transition-all">Simpan Keputusan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="4" description="Sistem akan menampilkan pengajuan seminar di sini setelah mahasiswa mendaftar." icon="book" />
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>
</x-app-layout>

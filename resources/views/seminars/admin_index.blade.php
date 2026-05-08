<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pendaftaran Seminar', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6">
        {{-- Template Management Section --}}
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center">
                        <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Manajemen Formulir Pendaftaran
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Upload templat formulir seminar terbaru untuk diunduh mahasiswa.</p>
                </div>
                
                @if($template)
                    <div class="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 rounded-lg">
                        <div class="p-2 bg-white dark:bg-slate-700 rounded shadow-sm">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] font-bold text-emerald-800 dark:text-emerald-200 truncate max-w-[200px]">{{ $template->title }}</div>
                            <div class="text-[9px] text-emerald-600 dark:text-emerald-400 font-medium truncate max-w-[200px]">{{ $template->original_name }}</div>
                        </div>
                        <a href="{{ Storage::url($template->file_path) }}" target="_blank" class="ml-2 p-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        </a>
                    </div>
                @endif

                <div x-data="{ open: false }">
                    <button @click="open = true" class="px-4 py-2 bg-orange-600 text-white text-xs font-bold rounded hover:bg-orange-700 transition-all shadow-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Upload Templat Seminar
                    </button>

                    <!-- Upload Template Modal -->
                    <div x-show="open" x-cloak class="fixed inset-0 z-[110] overflow-y-auto">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-slate-900 bg-opacity-50 dark:bg-opacity-80 transition-opacity" @click="open = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100 dark:border-slate-700">
                                <div class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Upload Templat Formulir</h3>
                                    <button @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                <form action="{{ route('seminar-applications.upload-template') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">Judul Formulir</label>
                                            <input type="text" name="title" required class="block w-full rounded-md border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm focus:ring-orange-500 focus:border-orange-500 shadow-sm text-slate-800 dark:text-slate-100 placeholder:text-slate-400" placeholder="Contoh: Formulir Pendaftaran Seminar UP 2024">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">File Templat (PDF/DOC/DOCX)</label>
                                            <input type="file" name="template_file" required accept=".pdf,.doc,.docx" class="block w-full text-xs text-slate-600 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-orange-50 dark:file:bg-orange-900/20 file:text-orange-700 dark:file:text-orange-500 hover:file:bg-orange-100 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-md">
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                        <button type="button" @click="open = false" class="px-4 py-2 text-slate-600 dark:text-slate-400 text-xs font-bold rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">Batal</button>
                                        <button type="submit" class="px-6 py-2 bg-orange-600 text-white text-xs font-bold rounded hover:bg-orange-700 transition-colors shadow-sm">Simpan Templat</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap w-[35%]">MAHASISWA</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap w-[35%]">BERKAS PERSYARATAN</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center w-[15%]">STATUS</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-right w-[15%]">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($applications as $app)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors align-top">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800 dark:text-slate-100">{{ $app->thesis->student->name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 tracking-tight uppercase">{{ $app->thesis->student->identifier }}</div>
                                    <div class="mt-2 space-y-1">
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500">
                                            <span class="w-3 h-3 rounded-full bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-500 flex items-center justify-center font-bold">1</span>
                                            {{ $app->thesis->pembimbing1->name }}
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500">
                                            <span class="w-3 h-3 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold">2</span>
                                            {{ $app->thesis->pembimbing2->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="grid grid-cols-2 gap-2 max-w-xs">
                                        <a href="{{ Storage::url($app->file_acc_pembimbing) }}" target="_blank" class="flex items-center px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded text-[10px] font-bold text-slate-600 dark:text-slate-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Bukti ACC
                                        </a>
                                        <a href="{{ Storage::url($app->file_pembayaran) }}" target="_blank" class="flex items-center px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded text-[10px] font-bold text-slate-600 dark:text-slate-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            Bukti Bayar
                                        </a>
                                        <a href="{{ Storage::url($app->file_kartu_bimbingan) }}" target="_blank" class="flex items-center px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded text-[10px] font-bold text-slate-600 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            Kartu Bimbingan
                                        </a>
                                        <a href="{{ Storage::url($app->file_skripsi) }}" target="_blank" class="flex items-center px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded text-[10px] font-bold text-slate-600 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            Soft Copy
                                        </a>
                                        <a href="{{ Storage::url($app->file_formulir) }}" target="_blank" class="flex items-center px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded text-[10px] font-bold text-slate-600 dark:text-slate-400 hover:bg-pink-50 dark:hover:bg-pink-900/20 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Form Seminar
                                        </a>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ $app->status === 'approved' ? 'bg-emerald-600 text-white' : '' }}
                                        {{ $app->status === 'rejected' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                                        {{ $app->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                    ">
                                        {{ $app->status }}
                                    </span>
                                    @if($app->admin_feedback)
                                        <div class="mt-1 text-[9px] text-slate-400 italic max-w-[150px] mx-auto truncate" title="{{ $app->admin_feedback }}">"{{ $app->admin_feedback }}"</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex justify-end items-center gap-2" x-data="{ open: false }">
                                        <a href="{{ route('seminar-applications.download-zip', $app->id) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 dark:bg-slate-700 rounded text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition-all">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            ZIP
                                        </a>
                                        <button @click="open = true" type="button" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 hover:border-orange-300 transition-all shadow-sm">
                                            Validasi
                                            <svg class="w-3.5 h-3.5 ml-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                        
                                        <!-- Validation Modal -->
                                        <div x-show="open" 
                                             x-cloak 
                                             class="fixed inset-0 z-[100] overflow-y-auto" 
                                             aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div x-show="open" 
                                                     x-transition:enter="ease-out duration-300"
                                                     x-transition:enter-start="opacity-0"
                                                     x-transition:enter-end="opacity-100"
                                                     x-transition:leave="ease-in duration-200"
                                                     x-transition:leave-start="opacity-100"
                                                     x-transition:leave-end="opacity-0"
                                                     class="fixed inset-0 bg-slate-900 bg-opacity-50 dark:bg-opacity-80 transition-opacity" @click="open = false"></div>

                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                                <div x-show="open"
                                                     x-transition:enter="ease-out duration-300"
                                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                     x-transition:leave="ease-in duration-200"
                                                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                     class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                                                    
                                                    <div class="bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Validasi Berkas: {{ $app->thesis->student->name }}</h3>
                                                        <button @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>

                                                    <form action="{{ route('seminar-applications.validate', $app->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="p-6 bg-slate-50/50 dark:bg-slate-900/50">
                                                            <div class="mb-4">
                                                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Status Validasi</label>
                                                                <select name="status" class="block w-full rounded-md border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm focus:ring-orange-500 focus:border-orange-500 shadow-sm text-slate-800 dark:text-slate-100">
                                                                    <option value="approved" {{ $app->status === 'approved' ? 'selected' : '' }}>Setujui (Berkas Lengkap)</option>
                                                                    <option value="rejected" {{ $app->status === 'rejected' ? 'selected' : '' }}>Tolak (Berkas Bermasalah)</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Catatan / Alasan</label>
                                                                <textarea name="admin_feedback" rows="4" class="block w-full rounded-md border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm focus:ring-orange-500 focus:border-orange-500 shadow-sm placeholder:text-slate-400 text-slate-800 dark:text-slate-100" placeholder="Opsional: tambahkan alasan jika ditolak atau catatan tambahan untuk mahasiswa...">{{ $app->admin_feedback }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="px-6 py-4 bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                                            <button type="button" @click="open = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 text-xs font-bold rounded hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Batal</button>
                                                            <button type="submit" class="px-6 py-2 bg-orange-600 text-white text-xs font-bold rounded hover:bg-orange-700 transition-colors shadow-sm">Simpan Keputusan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-20 text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                                        <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Tidak ada pengajuan masuk</h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest font-black">Sistem akan menampilkan pengajuan seminar di sini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

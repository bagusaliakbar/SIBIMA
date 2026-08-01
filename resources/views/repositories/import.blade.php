<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <x-breadcrumb :items="[
                ['label' => 'Katalog Pustaka Skripsi', 'route' => route('repositories.index')],
                ['label' => 'Import Arsip', 'route' => null]
            ]" />
        </div>
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-md shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Import Arsip Alumni</h3>
                <p class="text-slate-500 dark:text-slate-400 mt-1 text-xs">Unggah data ribuan skripsi alumni lama menggunakan file Excel.</p>
            </div>

            <div class="flex flex-col md:flex-row gap-6 mb-8">
                <!-- Info & Download Template -->
                <div class="flex-1 bg-indigo-50/50 dark:bg-indigo-900/10 p-5 rounded-xl border border-indigo-100 dark:border-indigo-900/30">
                    <h4 class="text-sm font-bold text-indigo-800 dark:text-indigo-300 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Langkah-Langkah Import
                    </h4>
                    <ol class="list-decimal list-inside text-xs text-slate-600 dark:text-slate-400 space-y-2 mb-4">
                        <li>Unduh template Excel yang telah disediakan.</li>
                        <li>Isi data skripsi alumni sesuai kolom (NPM, Nama, Angkatan, Judul, dll).</li>
                        <li>Pastikan tidak merubah nama kolom (baris pertama) pada template.</li>
                        <li>Unggah file Excel (format .xlsx atau .xls) ke form di samping.</li>
                    </ol>
                    <a href="{{ route('repositories.template') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/50 text-xs font-semibold rounded-lg transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh Template Excel
                    </a>
                </div>

                <!-- Upload Form -->
                <div class="flex-1" x-data="{ isUploading: false, fileName: '' }">
                    <form action="{{ route('repositories.import.store') }}" method="POST" enctype="multipart/form-data" @submit="isUploading = true">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pilih File Excel <span class="text-red-500">*</span></label>
                            
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-xl hover:border-emerald-500 dark:hover:border-emerald-500 transition-colors bg-slate-50 dark:bg-slate-900/50 relative overflow-hidden group">
                                <div class="space-y-1 text-center relative z-10" x-show="!fileName">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 group-hover:text-emerald-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                                        <label for="file" class="relative cursor-pointer bg-white dark:bg-slate-800 rounded-md font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 focus-within:outline-none px-2 py-1">
                                            <span>Upload file</span>
                                            <input id="file" name="file" type="file" class="sr-only" required accept=".xlsx,.xls,.csv" @change="fileName = $event.target.files[0].name">
                                        </label>
                                        <p class="pl-1 py-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-[10px] text-slate-500">XLSX atau XLS maksimal 5MB</p>
                                </div>
                                
                                <!-- File Selected View -->
                                <div class="space-y-2 text-center relative z-10 py-4" x-show="fileName" style="display: none;">
                                    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 mx-auto flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200" x-text="fileName"></p>
                                    <button type="button" @click="fileName = ''; document.getElementById('file').value = ''" class="text-xs text-red-500 hover:text-red-700 font-medium">Ganti file</button>
                                </div>
                            </div>
                            @error('file')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2 flex gap-3">
                            <button type="submit" :disabled="!fileName || isUploading" class="inline-flex justify-center items-center px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-emerald-500/20">
                                <span x-show="!isUploading">Mulai Import Arsip</span>
                                <span x-show="isUploading" style="display: none;" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Sedang Memproses...
                                </span>
                            </button>
                            <a href="{{ route('repositories.index') }}" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-sm">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

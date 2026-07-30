<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Data Skripsi', 'route' => route('theses.index')],
            ['label' => 'Input Data Migrasi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-md shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Input Data Migrasi Skripsi (Bypass)</h3>
                <p class="text-slate-500 dark:text-slate-400 mt-1 text-xs">Formulir khusus Admin/Kaprodi untuk memasukkan data mahasiswa angkatan lama yang skripsinya sudah berjalan.</p>
            </div>
            
            @if ($errors->any())
                <div class="mb-6 p-4 rounded bg-red-50 text-red-700 text-sm border border-red-100">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('error'))
                <div class="mb-6 p-4 rounded bg-red-50 text-red-700 text-sm border border-red-100">
                    {{ session('error') }}
                </div>
            @endif

            <div x-data="{ tab: 'manual' }">
                <!-- Tabs -->
                <div class="flex space-x-4 mb-6 border-b border-slate-200 dark:border-slate-700">
                    <button @click="tab = 'manual'" :class="tab === 'manual' ? 'border-orange-500 text-orange-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Input Manual Tunggal
                    </button>
                    <button @click="tab = 'excel'" :class="tab === 'excel' ? 'border-orange-500 text-orange-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Import via Excel
                    </button>
                </div>

                <!-- Manual Input Form -->
                <div x-show="tab === 'manual'">
                    <form action="{{ route('theses.migration.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-md">
                            <label for="student_id" class="block text-sm font-semibold text-amber-900 dark:text-amber-300">Pilih Mahasiswa <span class="text-red-600">*</span></label>
                            <p class="text-xs text-amber-700 dark:text-amber-400 mb-2">Pilih mahasiswa angkatan lama (Pastikan tidak ada di daftar ini jika mereka sudah punya skripsi).</p>
                            <select name="student_id" id="student_id" required class="block w-full rounded-md bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm">
                                <option value="">-- Pilih Mahasiswa --</option>
                                @foreach($students as $mhs)
                                    <option value="{{ $mhs->id }}" {{ old('student_id') == $mhs->id ? 'selected' : '' }}>
                                        {{ $mhs->name }} ({{ $mhs->identifier ?? 'NPM -' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Judul Skripsi <span class="text-orange-600">*</span></label>
                            <input type="text" name="title" id="title" required value="{{ old('title') }}" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-colors">
                        </div>

                        <div>
                            <label for="abstract" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Abstrak/Deskripsi <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <textarea name="abstract" id="abstract" rows="4" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-colors">{{ old('abstract') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="pembimbing1_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Dosen Pembimbing 1 <span class="text-red-600">*</span></label>
                                <select name="pembimbing1_id" id="pembimbing1_id" required class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm transition-colors">
                                    <option value="">-- Pilih Pembimbing 1 --</option>
                                    @foreach($dosens as $dosen)
                                        <option value="{{ $dosen->id }}" {{ old('pembimbing1_id') == $dosen->id ? 'selected' : '' }}>
                                            {{ $dosen->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="pembimbing2_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Dosen Pembimbing 2 <span class="text-red-600">*</span></label>
                                <select name="pembimbing2_id" id="pembimbing2_id" required class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm transition-colors">
                                    <option value="">-- Pilih Pembimbing 2 --</option>
                                    @foreach($dosens as $dosen)
                                        <option value="{{ $dosen->id }}" {{ old('pembimbing2_id') == $dosen->id ? 'selected' : '' }}>
                                            {{ $dosen->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 p-4 border border-blue-200 dark:border-blue-900 rounded-md bg-blue-50 dark:bg-blue-900/20">
                            <label for="current_stage" class="block text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">Tahapan Saat Ini <span class="text-red-600">*</span></label>
                            <p class="text-xs text-blue-700 dark:text-blue-400 mb-3">Tentukan posisi bimbingan mahasiswa ini saat ini.</p>
                            <select name="current_stage" id="current_stage" required class="block w-full rounded-md bg-white dark:bg-slate-900 border border-blue-300 dark:border-blue-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm">
                                <option value="Bimbingan Skripsi" {{ old('current_stage') == 'Bimbingan Skripsi' ? 'selected' : '' }}>1. Sedang Bimbingan Skripsi (Belum UP)</option>
                                <option value="Selesai Seminar UP" {{ old('current_stage') == 'Selesai Seminar UP' ? 'selected' : '' }}>2. Sudah Lulus Seminar UP</option>
                                <option value="Siap Sidang" {{ old('current_stage') == 'Siap Sidang' ? 'selected' : '' }}>3. Siap Sidang Akhir</option>
                            </select>
                        </div>

                        <div class="pt-5 flex items-center gap-3">
                            <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-orange-500/20">
                                Simpan Data Migrasi
                            </button>
                            <a href="{{ route('theses.index') }}" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all hover:scale-[1.02] active:scale-95 shadow-sm duration-300">Batal</a>
                        </div>
                    </form>
                </div>

                <!-- Excel Import Form -->
                <div x-show="tab === 'excel'" x-data="{ fileName: null, isUploading: false }" style="display: none;">
                    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-700/50 rounded-xl">
                        <div class="flex-1">
                            <h4 class="text-sm font-black text-indigo-900 dark:text-indigo-300 uppercase tracking-widest mb-1">Format Data Migrasi</h4>
                            <p class="text-xs text-indigo-700 dark:text-indigo-400 font-medium">Gunakan template ini untuk mengimpor banyak data sekaligus. Pastikan format file sesuai sebelum mengunggah.</p>
                        </div>
                        <a href="{{ route('theses.migration.template') }}" class="inline-flex shrink-0 items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/20 transition-all hover:scale-[1.02] active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Template Excel
                        </a>
                    </div>
                    
                    <form action="{{ route('theses.migration.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="isUploading = true">
                        @csrf
                        <div class="border-2 border-dashed rounded-xl p-8 text-center transition-colors" :class="fileName ? 'border-indigo-400 bg-indigo-50/50 dark:border-indigo-600 dark:bg-indigo-900/10' : 'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50'">
                            <svg class="mx-auto h-12 w-12 transition-colors" :class="fileName ? 'text-indigo-500' : 'text-slate-400'" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="mt-4 flex flex-col items-center justify-center text-sm text-slate-600">
                                <label for="file" class="relative cursor-pointer bg-white dark:bg-slate-800 rounded-lg px-4 py-2 border border-slate-200 dark:border-slate-700 font-bold text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 shadow-sm transition-all">
                                    <span x-text="fileName ? 'Ganti File Excel' : 'Pilih File Excel'"></span>
                                    <input id="file" name="file" type="file" accept=".xlsx,.xls" class="sr-only" required @change="fileName = $event.target.files[0] ? $event.target.files[0].name : null">
                                </label>
                            </div>
                            
                            <!-- Menampilkan nama file yang dipilih -->
                            <template x-if="fileName">
                                <div class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="fileName"></span>
                                </div>
                            </template>
                            
                            <template x-if="!fileName">
                                <p class="text-xs text-slate-500 mt-3 font-medium uppercase tracking-widest">
                                    Hanya XLSX & XLS (Max 2MB)
                                </p>
                            </template>
                        </div>
                        
                        <div class="pt-2 flex items-center gap-3">
                            <button type="submit" :disabled="!fileName || isUploading" :class="(!fileName || isUploading) ? 'opacity-50 cursor-not-allowed' : 'hover:scale-[1.02] active:scale-95 hover:bg-orange-700 shadow-lg shadow-orange-500/20'" class="inline-flex justify-center items-center gap-2 px-8 py-3 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest transition-all">
                                <!-- Normal Icon -->
                                <svg x-show="!isUploading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                
                                <!-- Loading Spinner -->
                                <svg x-show="isUploading" style="display: none;" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                
                                <span x-text="isUploading ? 'Sedang Mengimpor...' : 'Mulai Import'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

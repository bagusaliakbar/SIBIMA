<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pengajuan Judul', 'route' => null]
        ]" />
        <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
            Pengajuan Rencana Judul Skripsi
        </h2>
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-md shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Form Pengajuan</h3>
                <p class="text-slate-500 dark:text-slate-400 mt-1 text-xs">Isi formulir di bawah ini untuk mengajukan rencana judul skripsi Anda.</p>
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

            <form action="{{ route('theses.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Rencana Judul Skripsi <span class="text-orange-600">*</span></label>
                    <input type="text" name="title" id="title" required value="{{ old('title') }}" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-colors" placeholder="Masukkan usulan rencana judul skripsi">
                </div>

                <div>
                    <label for="abstract" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi Skripsi <span class="text-orange-600">*</span></label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-2">Jelaskan secara singkat latar belakang, masalah, dan metode yang akan digunakan.</p>
                    <textarea name="abstract" id="abstract" rows="6" required class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-colors" placeholder="Tuliskan deskripsi rencana skripsi Anda di sini...">{{ old('abstract') }}</textarea>
                </div>

                {{-- Usulan Dosen Pembimbing --}}
                <div class="border border-slate-200 dark:border-slate-700 rounded-md p-5 bg-slate-50/50 dark:bg-slate-900/30">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center border border-orange-200 dark:border-orange-800">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Usulan Dosen Pembimbing <span class="text-slate-400 dark:text-slate-500 font-normal">(Opsional)</span></h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pilih dosen yang Anda usulkan sebagai pembimbing.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="requested_pembimbing1_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Usulan Pembimbing 1</label>
                            <select name="requested_pembimbing1_id" id="requested_pembimbing1_id" class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm transition-colors">
                                <option value="">-- Tidak ada usulan --</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}" {{ old('requested_pembimbing1_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ $dosen->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="requested_pembimbing2_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Usulan Pembimbing 2</label>
                            <select name="requested_pembimbing2_id" id="requested_pembimbing2_id" class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm transition-colors">
                                <option value="">-- Tidak ada usulan --</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}" {{ old('requested_pembimbing2_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ $dosen->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('requested_pembimbing2_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 flex items-start gap-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/50 rounded text-xs text-amber-700 dark:text-amber-400">
                        <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span>Usulan pembimbing bersifat <strong>tidak mengikat</strong>. Admin/Kaprodi akan menyesuaikan dengan Bidang Ilmu dari Dosen Tersebut.</span>
                    </div>
                </div>

                <div class="pt-5 flex items-center space-x-3">
                    <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 rounded text-sm font-medium text-white shadow-sm transition-colors border border-transparent">
                        Submit Pengajuan
                    </button>
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 rounded text-sm font-medium text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>


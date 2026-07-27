<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
            {{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Buat Jadwal Bimbingan' : 'Pengajuan Jadwal Bimbingan' }}
        </h2>
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 transition-colors">
            <div class="mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Form {{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Penjadwalan' : 'Pengajuan' }}</h3>
                <p class="text-slate-500 dark:text-slate-400 mt-1 text-xs">{{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Pilih mahasiswa dan tentukan waktu bimbingan.' : 'Pilih jadwal dan topik untuk sesi bimbingan Anda berikutnya.' }}</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-md bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400 dark:text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-400">Terdapat kesalahan pada penginputan:</h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('mentoring-sessions.store') }}" method="POST" class="space-y-6" x-data="{ type: 'offline' }">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                    <div class="md:col-span-2">
                        <label for="thesis_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mahasiswa Bimbingan <span class="text-orange-600">*</span></label>
                        <select name="thesis_id" id="thesis_id" required class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                            <option value="">Pilih Mahasiswa...</option>
                            <option value="all" class="font-bold text-orange-600">-- Pilih Semua Mahasiswa Bimbingan --</option>
                            @foreach($theses as $t)
                                <option value="{{ $t->id }}">{{ $t->student?->name ?? 'Mahasiswa' }} - {{ \Illuminate\Support\Str::limit($t->title, 50) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="md:col-span-2">
                        <label for="dosen_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Pilih Dosen Pembimbing <span class="text-orange-600">*</span></label>
                        <select name="dosen_id" id="dosen_id" required class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                            <option value="">Pilih Pembimbing...</option>
                            @if($thesis->pembimbing1)
                                <option value="{{ $thesis->pembimbing1->id }}">Pembimbing 1: {{ $thesis->pembimbing1->name }}</option>
                            @endif
                            @if($thesis->pembimbing2)
                                <option value="{{ $thesis->pembimbing2->id }}">Pembimbing 2: {{ $thesis->pembimbing2->name }}</option>
                            @endif
                        </select>
                        <p class="mt-1 text-xs text-slate-400 dark:text-slate-500 italic">Jadwal bimbingan akan dikirimkan secara spesifik ke dosen yang dipilih.</p>
                    </div>
                    @endif

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal <span class="text-orange-600">*</span></label>
                            <input type="date" name="scheduled_date" id="scheduled_date" required class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                        <div>
                            <label for="scheduled_hour" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Waktu <span class="text-orange-600">*</span></label>
                            <div class="mt-2 flex items-center space-x-2">
                                <div class="flex-1">
                                    <select name="scheduled_hour" id="scheduled_hour" required class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                                        @for($h = 0; $h < 24; $h++)
                                            <option value="{{ sprintf('%02d', $h) }}" {{ $h == 9 ? 'selected' : '' }}>{{ sprintf('%02d', $h) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <span class="text-slate-500 dark:text-slate-400 font-bold">:</span>
                                <div class="flex-1">
                                    <select name="scheduled_minute" id="scheduled_minute" required class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                                        @for($m = 0; $m < 60; $m += 5)
                                            <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium ml-2">WIB</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="topic" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Topik Pembahasan <span class="text-orange-600">*</span></label>
                        <input type="text" name="topic" id="topic" required placeholder="Contoh: Revisi Bab 1" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tipe Bimbingan <span class="text-orange-600">*</span></label>
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-slate-900 p-3 shadow-sm focus:outline-none transition-all" :class="type === 'offline' ? 'border-orange-500 ring-1 ring-orange-500' : 'border-slate-300 dark:border-slate-700'">
                                <input type="radio" name="type" value="offline" x-model="type" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-slate-100">Tatap Muka (Offline)</span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-orange-600" :class="type === 'offline' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </label>
                            <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-slate-900 p-3 shadow-sm focus:outline-none transition-all" :class="type === 'online' ? 'border-orange-500 ring-1 ring-orange-500' : 'border-slate-300 dark:border-slate-700'">
                                <input type="radio" name="type" value="online" x-model="type" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-slate-100">Daring (Online)</span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-orange-600" :class="type === 'online' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-slate-700 dark:text-slate-300" x-text="type === 'online' ? 'Tautan Google Meet / Zoom' : 'Ruangan / Tempat Bimbingan'"></label>
                        <input type="text" name="location" id="location" :placeholder="type === 'online' ? 'https://meet.google.com/...' : 'Contoh: Ruang Dosen T.I / Lab Komputer'" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="4" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all" placeholder="Sampaikan apa yang ingin Anda diskusikan secara spesifik..."></textarea>
                </div>

                <div class="pt-5 flex items-center space-x-3">
                    <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 rounded text-sm font-medium text-white shadow-sm transition-colors border border-transparent">
                        {{ Auth::user()->role === 'dosen' ? 'Simpan Jadwal' : 'Kirim Permintaan' }}
                    </button>
                    <a href="{{ Auth::user()->role === 'dosen' ? route('mentoring-sessions.index') : route('dashboard') }}" class="px-5 py-2 rounded text-sm font-medium text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

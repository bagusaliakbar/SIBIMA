<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pengajuan Judul', 'route' => null]
        ]" />
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

            <form action="{{ route('theses.store') }}" method="POST" class="space-y-6" 
                  x-data="{ 
                      title: '{{ old('title') }}', 
                      similarTitles: [], 
                      isChecking: false,
                      timeout: null,
                      checkSimilarity() {
                          clearTimeout(this.timeout);
                          if(this.title.length < 10) {
                              this.similarTitles = [];
                              return;
                          }
                          this.timeout = setTimeout(() => {
                              this.isChecking = true;
                              fetch('{{ route('theses.check-title') }}', {
                                  method: 'POST',
                                  headers: {
                                      'Content-Type': 'application/json',
                                      'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                      'Accept': 'application/json'
                                  },
                                  body: JSON.stringify({ title: this.title })
                              })
                              .then(res => res.json())
                              .then(data => {
                                  this.similarTitles = data.similar || [];
                                  this.isChecking = false;
                              })
                              .catch(() => { this.isChecking = false; });
                          }, 800);
                      }
                  }">
                @csrf
                
                @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-md">
                    <label for="student_id" class="block text-sm font-semibold text-amber-900 dark:text-amber-300">Pilih Mahasiswa <span class="text-red-600">*</span></label>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mb-2">Pilih mahasiswa yang diajukan judul skripsinya.</p>
                    <select name="student_id" id="student_id" required class="block w-full rounded-md bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach($students as $mhs)
                            <option value="{{ $mhs->id }}" {{ old('student_id') == $mhs->id ? 'selected' : '' }}>
                                {{ $mhs->name }} ({{ $mhs->identifier ?? 'NPM -' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Rencana Judul Skripsi <span class="text-orange-600">*</span></label>
                    <input type="text" name="title" id="title" required x-model="title" @input="checkSimilarity()" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-colors" placeholder="Masukkan usulan rencana judul skripsi">
                    
                    <!-- Checking Indicator -->
                    <p x-show="isChecking" class="text-[10px] text-slate-500 mt-2 flex items-center animate-pulse" style="display: none;">
                        <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Mengecek kemiripan judul...
                    </p>

                    <!-- Alert Box -->
                    <div x-show="similarTitles.length > 0" x-transition.opacity class="mt-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-lg relative" style="display: none;">
                        <button type="button" @click="similarTitles = []" class="absolute top-2 right-2 text-red-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 text-red-600 dark:text-red-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-red-800 dark:text-red-300 uppercase tracking-widest mb-1">Peringatan: Judul Terlalu Mirip!</h4>
                                <p class="text-[11px] text-red-700 dark:text-red-400 mb-2 leading-relaxed">Kami mendeteksi usulan judul Anda memiliki tingkat kemiripan yang tinggi (di atas 60%) dengan skripsi yang sudah ada. Mohon gunakan judul yang lebih unik untuk menghindari plagiarisme.</p>
                                
                                <ul class="space-y-2 mt-3">
                                    <template x-for="item in similarTitles">
                                        <li class="bg-white/60 dark:bg-slate-900/50 p-2.5 rounded border border-red-100 dark:border-red-900/30">
                                            <div class="flex justify-between items-start gap-2 mb-1">
                                                <p class="text-[11px] font-bold text-slate-800 dark:text-slate-200" x-text="item.title"></p>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-black" :class="item.percentage >= 80 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700'" x-text="item.percentage + '% Mirip'"></span>
                                            </div>
                                            <p class="text-[9px] text-slate-500 uppercase font-semibold">Milik: <span x-text="item.student_name"></span> (Angkatan <span x-text="item.year"></span>)</p>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
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

                <div class="pt-5 flex items-center gap-3">
                    <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-orange-500/20">
                        Submit Pengajuan
                    </button>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all hover:scale-[1.02] active:scale-95 shadow-sm duration-300">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>


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

                    <!-- Modern Enhanced Similarity Alert Box -->
                    <div x-show="similarTitles.length > 0" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         class="mt-4 p-5 rounded-2xl bg-gradient-to-br from-amber-50/90 via-red-50/60 to-orange-50/80 dark:from-red-950/40 dark:via-slate-900 dark:to-orange-950/30 border border-amber-300/80 dark:border-red-800/60 shadow-xl shadow-amber-500/5 relative overflow-hidden"
                         style="display: none;">
                        
                        <!-- Top Accent Line -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 via-amber-500 to-orange-500"></div>

                        <!-- Header Bar -->
                        <div class="flex items-center justify-between gap-3 mb-4 pb-3 border-b border-red-200/60 dark:border-red-800/40">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-red-500/10 dark:bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 border border-red-200 dark:border-red-800/40">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-red-900 dark:text-red-300 uppercase tracking-wider flex items-center gap-2">
                                        <span>Analisis Kemiripan Judul</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800/50" x-text="similarTitles.length + ' Kemiripan Terdeteksi'"></span>
                                    </h4>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">Sistem menemukan beberapa judul yang mirip di database skripsi & alumni.</p>
                                </div>
                            </div>
                            <button type="button" @click="similarTitles = []" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Content Grid: 2 Columns (Matches vs Guidance Tips) -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            
                            <!-- Similar Titles List (2 Cols) -->
                            <div class="lg:col-span-2 space-y-2.5">
                                <template x-for="item in similarTitles">
                                    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm p-3.5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-amber-400 dark:hover:border-amber-600 transition-all">
                                        <div class="flex justify-between items-start gap-3 mb-1.5">
                                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-snug" x-text="item.title"></p>
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold shrink-0 shadow-2xs flex items-center gap-1"
                                                  :class="item.percentage >= 80 ? 'bg-red-500 text-white' : (item.percentage >= 60 ? 'bg-amber-500 text-white' : 'bg-yellow-500 text-slate-900')">
                                                <span x-text="item.percentage + '%'"></span>
                                                <span class="text-[9px] opacity-90 font-normal">Mirip</span>
                                            </span>
                                        </div>

                                        <!-- Matched Keywords Pills -->
                                        <div class="flex flex-wrap items-center gap-1.5 mt-2 pt-2 border-t border-slate-100 dark:border-slate-800/60">
                                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Kata Kunci Sama:</span>
                                            <template x-for="word in (item.matched_words || [])">
                                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/40" x-text="word"></span>
                                            </template>
                                        </div>

                                        <div class="flex items-center justify-between gap-2 mt-2 text-[10px] text-slate-500 dark:text-slate-400">
                                            <span class="font-medium truncate">Milik: <strong class="text-slate-700 dark:text-slate-300" x-text="item.student_name"></strong> (Angkatan <span x-text="item.year"></span>)</span>
                                            <span class="px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-wider shrink-0" 
                                                  :class="item.source === 'Skripsi Aktif' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
                                                  x-text="item.source || 'Skripsi Aktif'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Guidance & Recommendations Tip Box (1 Col) -->
                            <div class="bg-amber-500/10 dark:bg-amber-500/5 p-4 rounded-xl border border-amber-300/60 dark:border-amber-800/40 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 mb-2">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                        <h5 class="text-xs font-bold uppercase tracking-wider">Tips Agar Judul Unik</h5>
                                    </div>
                                    <ul class="text-[11px] text-slate-700 dark:text-slate-300 space-y-2 leading-relaxed">
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-amber-600 font-bold">•</span>
                                            <span><strong>Objek / Lokasi:</strong> Tambahkan instansi/lokasi/objek spesifik (misal: <em>"pada PT XYZ"</em>).</span>
                                        </li>
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-amber-600 font-bold">•</span>
                                            <span><strong>Metode / Algoritma:</strong> Spesifikasikan metode (misal: <em>"Metode SAW / Naive Bayes"</em>).</span>
                                        </li>
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-amber-600 font-bold">•</span>
                                            <span><strong>Platform:</strong> Cantumkan platform (misal: <em>"Berbasis Android / PWA"</em>).</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="mt-3 pt-3 border-t border-amber-200/60 dark:border-amber-800/40 text-[10px] text-amber-700 dark:text-amber-400 italic font-medium">
                                    Judul yang spesifik dan scope yang jelas meningkatkan kepastian persetujuan Dosen & Kaprodi.
                                </div>
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


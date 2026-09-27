<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Dokumen & Legalitas', 'route' => route('advisor-decrees.index')],
                ['label' => 'Surat Tugas Pembimbing (BKD)', 'route' => null]
            ]" />

            <div class="flex items-center gap-2">
                @if($isStaff)
                    <a href="{{ route('advisor-decrees.index') }}" 
                       class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition">
                        <span>← Kembali ke SK Kolektif</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="suratTugasManager()">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Terjadi kesalahan saat memproses Surat Tugas:</span>
                </div>
                <ul class="list-disc list-inside pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Banner Info BKD -->
        <div class="bg-gradient-to-r from-orange-600 to-amber-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-orange-600/15 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[11px] font-black uppercase tracking-wider text-orange-100">
                    <span>📄 Cetak Mandiri Surat Tugas BKD</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                    Surat Tugas Pembimbingan Skripsi
                </h2>
                <p class="text-xs sm:text-sm text-orange-100/90 leading-relaxed">
                    Dokumen resmi yang ditugaskan oleh Wakil Dekan I Fakultas Ilmu Komputer Universitas Subang untuk keperluan Beban Kinerja Dosen (BKD) dan pelaporan SISTER Kemendikbudristek.
                </p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-xs space-y-1.5 shrink-0 w-full sm:w-auto">
                <div class="text-[11px] uppercase tracking-wider text-orange-200 font-bold">Dosen Pembimbing</div>
                <div class="text-sm font-black text-white">{{ $targetDosen->name }}</div>
                <div class="text-orange-100 text-[11px]">NIDN: {{ $targetDosen->identifier ?? '-' }} | Dosen Tetap</div>
            </div>
        </div>

        <form action="{{ route('advisor-decrees.generate-surat-tugas') }}" method="POST" id="suratTugasForm">
            @csrf
            @if($isStaff)
                <input type="hidden" name="dosen_id" value="{{ $targetDosen->id }}">
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Form Selection & Student List -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Step 1: Semester & Dosen Switcher (if Staff) -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-6 h-6 rounded-xl bg-orange-600 text-white flex items-center justify-center text-xs">1</span>
                                <span>Pilih Periode Semester Penugasan</span>
                            </h3>
                            <span class="text-xs text-slate-400">Sinkronisasi Otomatis</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if($isStaff)
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Dosen Target</label>
                                    <select onchange="window.location.href='{{ route('advisor-decrees.surat-tugas') }}?dosen_id=' + this.value + '&academic_year={{ $academicYear }}&semester={{ $semester }}'"
                                            class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2.5 font-bold focus:ring-orange-500 focus:border-orange-500">
                                        @foreach($dosens as $d)
                                            <option value="{{ $d->id }}" {{ $targetDosen->id == $d->id ? 'selected' : '' }}>
                                                {{ $d->name }} ({{ $d->identifier ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tahun Akademik</label>
                                <input type="text" name="academic_year" value="{{ $academicYear }}" required
                                       class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 font-black focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Semester</label>
                                <select name="semester" 
                                        class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 font-black focus:ring-orange-500 focus:border-orange-500">
                                    <option value="Ganjil" {{ $semester === 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                                    <option value="Genap" {{ $semester === 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Supervised Students Selection -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-xl bg-orange-600 text-white flex items-center justify-center text-xs">2</span>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white">Daftar Mahasiswa Bimbingan</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400"
                                      x-text="selectedCount + ' Mahasiswa Terpilih'"></span>
                            </div>

                            <div class="flex items-center gap-3 text-xs">
                                <button type="button" @click="selectAll()" class="font-bold text-orange-600 hover:text-orange-700 transition cursor-pointer">
                                    Pilih Semua
                                </button>
                                <span class="text-slate-300">|</span>
                                <button type="button" @click="deselectAll()" class="font-bold text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                    Batal Semua
                                </button>
                            </div>
                        </div>

                        <!-- KPI Pill Counters -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div class="p-3 rounded-2xl bg-orange-50/60 dark:bg-orange-950/20 border border-orange-200/60 dark:border-orange-900/40">
                                <div class="text-[10px] uppercase font-bold text-orange-700 dark:text-orange-400">Pembimbing I</div>
                                <div class="text-xl font-black text-orange-900 dark:text-orange-200">{{ $p1Theses->count() }} <span class="text-xs font-normal">Mhs</span></div>
                            </div>
                            <div class="p-3 rounded-2xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-200/60 dark:border-blue-900/40">
                                <div class="text-[10px] uppercase font-bold text-blue-700 dark:text-blue-400">Pembimbing II</div>
                                <div class="text-xl font-black text-blue-900 dark:text-blue-200">{{ $p2Theses->count() }} <span class="text-xs font-normal">Mhs</span></div>
                            </div>
                            <div class="col-span-2 sm:col-span-1 p-3 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-900/40">
                                <div class="text-[10px] uppercase font-bold text-emerald-700 dark:text-emerald-400">Total Bimbingan</div>
                                <div class="text-xl font-black text-emerald-900 dark:text-emerald-200">{{ $allTheses->count() }} <span class="text-xs font-normal">Mhs</span></div>
                            </div>
                        </div>

                        <!-- Supervised Students Table -->
                        @if($allTheses->count() > 0)
                            <div class="border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
                                @php $counter = 1; @endphp
                                
                                <!-- Group 1: Pembimbing I -->
                                @if($p1Theses->count() > 0)
                                    <div class="bg-orange-50/50 dark:bg-orange-950/30 px-4 py-2 text-[11px] font-black uppercase tracking-wider text-orange-800 dark:text-orange-300 flex items-center justify-between">
                                        <span>Sebagai Pembimbing I ({{ $p1Theses->count() }} Mahasiswa)</span>
                                        <span class="text-[10px] lowercase text-orange-600">Urutan Pertama di Tabel</span>
                                    </div>
                                    @foreach($p1Theses as $th)
                                        <label class="p-4 flex items-start gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 cursor-pointer transition select-none">
                                            <input type="checkbox" name="selected_theses[]" value="{{ $th->id }}"
                                                   x-model="selectedIds"
                                                   class="mt-1 rounded text-orange-600 focus:ring-orange-500 border-slate-300 dark:border-slate-700 cursor-pointer">
                                            <div class="flex-1 space-y-1 text-xs">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div>
                                                        <span class="font-black text-slate-900 dark:text-white">{{ $th->student ? $th->student->name : '-' }}</span>
                                                        <span class="text-slate-400 text-[11px]">({{ $th->student ? $th->student->identifier : '-' }})</span>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-400">
                                                        Pembimbing I
                                                    </span>
                                                </div>
                                                <div class="text-slate-600 dark:text-slate-300 italic text-[11.5px] leading-relaxed">
                                                    {{ $th->display_title }}
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                @endif

                                <!-- Group 2: Pembimbing II -->
                                @if($p2Theses->count() > 0)
                                    <div class="bg-blue-50/50 dark:bg-blue-950/30 px-4 py-2 text-[11px] font-black uppercase tracking-wider text-blue-800 dark:text-blue-300 flex items-center justify-between">
                                        <span>Sebagai Pembimbing II ({{ $p2Theses->count() }} Mahasiswa)</span>
                                        <span class="text-[10px] lowercase text-blue-600">Urutan Kedua di Tabel</span>
                                    </div>
                                    @foreach($p2Theses as $th)
                                        <label class="p-4 flex items-start gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 cursor-pointer transition select-none">
                                            <input type="checkbox" name="selected_theses[]" value="{{ $th->id }}"
                                                   x-model="selectedIds"
                                                   class="mt-1 rounded text-orange-600 focus:ring-orange-500 border-slate-300 dark:border-slate-700 cursor-pointer">
                                            <div class="flex-1 space-y-1 text-xs">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div>
                                                        <span class="font-black text-slate-900 dark:text-white">{{ $th->student ? $th->student->name : '-' }}</span>
                                                        <span class="text-slate-400 text-[11px]">({{ $th->student ? $th->student->identifier : '-' }})</span>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400">
                                                        Pembimbing II
                                                    </span>
                                                </div>
                                                <div class="text-slate-600 dark:text-slate-300 italic text-[11.5px] leading-relaxed">
                                                    {{ $th->display_title }}
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        @else
                            <div class="p-8 text-center text-slate-400 text-xs border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl space-y-2">
                                <p class="font-bold text-slate-600 dark:text-slate-300">Belum ada mahasiswa skripsi yang tercatat dibimbing oleh Anda.</p>
                                <p class="text-[11px] text-slate-500">Mahasiswa akan muncul otomatis di sini begitu judul skripsi dan dosen pembimbing ditetapkan oleh prodi.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Metadata, Signatory, and Direct Print Button -->
                <div class="space-y-6">
                    <!-- Step 3: Legalitas & Tanda Tangan -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                            <span class="w-6 h-6 rounded-xl bg-orange-600 text-white flex items-center justify-center text-xs">3</span>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Legalitas & Penandatangan</h3>
                        </div>

                        <!-- Tanggal Surat -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Penetapan</label>
                            <input type="date" name="decree_date" value="{{ now()->format('Y-m-d') }}" required
                                   class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 font-bold focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <!-- Format Penomoran -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Nomor Surat Tugas</label>
                                <span class="text-[10px] text-orange-600 font-bold">Auto Numbering</span>
                            </div>
                            <input type="text" name="custom_decree_number" value="{{ $previewLetterNumber }}"
                                   class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 font-mono font-black focus:ring-orange-500 focus:border-orange-500">
                            <p class="text-[10px] text-slate-400 mt-1">Menggunakan format resmi: <code>[NO]/PD.1.2/FIK-US/[BULAN]/[TAHUN]</code>.</p>
                        </div>

                        <!-- Signatory Details -->
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                            <div class="text-[10px] uppercase font-black tracking-wider text-slate-400">Pejabat Penandatangan Resmi</div>
                            <input type="hidden" name="signatory_title" value="{{ $defaultSignatoryTitle }}">
                            <input type="hidden" name="signatory_name" value="{{ $defaultSignatoryName }}">
                            <input type="hidden" name="signatory_identifier" value="{{ $defaultSignatoryIdentifier }}">
                            
                            <div class="text-xs font-black text-slate-900 dark:text-white">{{ $defaultSignatoryName }}</div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400">{{ $defaultSignatoryTitle }}</div>
                            <div class="text-[10px] text-slate-400">NIDN: {{ $defaultSignatoryIdentifier ?? '-' }} | QR Code Terdaftar</div>
                        </div>

                        <!-- Direct Print Solid Action Card -->
                        <div class="bg-orange-600 rounded-2xl p-5 text-white shadow-xl shadow-orange-600/20 space-y-4">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-orange-100">Status Seleksi:</span>
                                <span class="font-black text-white" x-text="selectedCount + ' Mahasiswa'"></span>
                            </div>

                            <button type="submit" name="print_direct" value="1"
                                    :disabled="selectedCount === 0"
                                    class="w-full py-3.5 px-4 bg-white text-orange-600 hover:bg-orange-50 active:scale-95 rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                <span>Cetak Surat Tugas (PDF)</span>
                            </button>

                            <button type="submit"
                                    :disabled="selectedCount === 0"
                                    class="w-full py-2.5 px-4 bg-orange-700/60 hover:bg-orange-700 text-white rounded-xl font-bold text-xs transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                Simpan ke Riwayat Dokumen
                            </button>
                        </div>
                    </div>

                    <!-- Riwayat Surat Tugas yang Telah Terbit -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Arsip Surat Tugas Anda</h3>
                            <span class="text-xs font-bold text-slate-400">{{ $history->count() }} Berkas</span>
                        </div>

                        @if($history->count() > 0)
                            <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar pr-1">
                                @foreach($history as $hist)
                                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="space-y-0.5">
                                                <div class="text-xs font-black text-slate-900 dark:text-white">{{ $hist->decree_number }}</div>
                                                <div class="text-[11px] text-slate-500">T.A {{ $hist->academic_year }} ({{ $hist->semester }})</div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400">
                                                {{ $hist->total_students }} Mhs
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2 pt-1 border-t border-slate-100 dark:border-slate-700/60">
                                            <a href="{{ route('advisor-decrees.pdf', $hist) }}" target="_blank"
                                               class="flex-1 py-1.5 px-3 rounded-lg text-center text-[11px] font-bold bg-orange-600 hover:bg-orange-700 text-white transition">
                                                Unduh PDF
                                            </a>
                                            <a href="{{ route('advisor-decrees.show', $hist) }}"
                                               class="py-1.5 px-3 rounded-lg text-center text-[11px] font-bold bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 transition">
                                                Detail
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-400 text-xs">
                                Belum ada riwayat Surat Tugas yang diterbitkan.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function suratTugasManager() {
            return {
                selectedIds: @json($allTheses->pluck('id')),
                
                get selectedCount() {
                    return this.selectedIds.length;
                },

                selectAll() {
                    this.selectedIds = @json($allTheses->pluck('id'));
                },

                deselectAll() {
                    this.selectedIds = [];
                }
            };
        }
    </script>
</x-app-layout>

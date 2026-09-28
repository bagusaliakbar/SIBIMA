<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'SK Dosen Pembimbing', 'route' => route('advisor-decrees.index')],
            ['label' => 'Terbitkan Dokumen', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6" x-data="advisorDecreeComposer()">
        <!-- Error Alert Banner -->
        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs space-y-1.5 shadow-sm">
                <div class="flex items-center gap-2 font-black text-rose-900 dark:text-rose-100">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Gagal Menerbitkan SK / Surat Tugas:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-6 text-rose-700 dark:text-rose-300">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('advisor-decrees.store') }}" method="POST" @submit="handleSubmit($event)">
            @csrf

            <!-- Hidden Inputs for Selected Candidates -->
            <template x-for="id in selectedIds" :key="'submit-sel-' + id">
                <input type="hidden" name="selected_theses[]" :value="id">
            </template>

            <!-- Hidden Inputs for Manual Students -->
            <template x-for="(st, idx) in manualStudents" :key="'submit-man-' + idx">
                <div>
                    <input type="hidden" :name="'manual_theses[' + idx + '][student_name]'" :value="st.student_name">
                    <input type="hidden" :name="'manual_theses[' + idx + '][student_npm]'" :value="st.student_npm">
                    <input type="hidden" :name="'manual_theses[' + idx + '][student_cohort]'" :value="st.student_cohort">
                    <input type="hidden" :name="'manual_theses[' + idx + '][title]'" :value="st.title">
                    <input type="hidden" :name="'manual_theses[' + idx + '][pembimbing1_name]'" :value="st.pembimbing1_name">
                    <input type="hidden" :name="'manual_theses[' + idx + '][pembimbing1_nidn]'" :value="st.pembimbing1_nidn">
                    <input type="hidden" :name="'manual_theses[' + idx + '][pembimbing2_name]'" :value="st.pembimbing2_name">
                    <input type="hidden" :name="'manual_theses[' + idx + '][pembimbing2_nidn]'" :value="st.pembimbing2_nidn">
                </div>
            </template>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Column: Filters & Candidates Table (2 cols) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Step 1: Filter Sasaran -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-2xl bg-orange-600 text-white flex items-center justify-center font-black text-sm">
                                    1
                                </span>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Filter Sasaran Mahasiswa per Semester</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Pilih mahasiswa yang mengajukan judul pada semester ini dan belum diterbitkan SK pembimbing.</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-400"
                                  x-text="(candidateCount + manualStudents.length) + ' Mahasiswa Ditemukan'"></span>
                        </div>

                        @if(Auth::user()->role === 'dosen')
                            <input type="hidden" name="target_type" value="individual_dosen">
                            <input type="hidden" name="dosen_id" value="{{ Auth::id() }}">
                            <div class="p-4 rounded-2xl bg-orange-50 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-800 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-black text-orange-900 dark:text-orange-200">👨‍🏫 Surat Tugas Pembimbingan BKD (Dosen)</div>
                                    <div class="text-[11px] text-orange-700 dark:text-orange-400 mt-0.5">Menerbitkan surat tugas resmi untuk pelaporan BKD & SISTER bagi <strong>{{ Auth::user()->name }}</strong>.</div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-orange-600 text-white uppercase tracking-wider">Khusus BKD</span>
                            </div>
                        @else
                            <!-- Target Type Segmented Control -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Jenis Penerbitan SK</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button type="button" 
                                            @click="targetType = 'collective'; fetchCandidates()"
                                            :class="targetType === 'collective' ? 'bg-orange-600 text-white shadow-md shadow-orange-600/20' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                                            class="p-3.5 rounded-2xl text-left border border-slate-200/60 dark:border-slate-700 transition cursor-pointer">
                                        <div class="text-xs font-black">🏢 Kolektif Seluruh Mahasiswa</div>
                                        <div class="text-[11px] opacity-80 mt-0.5">SK Penetapan menyeluruh untuk arsip fakultas/prodi.</div>
                                    </button>

                                    <button type="button" 
                                            @click="targetType = 'individual_dosen'; fetchCandidates()"
                                            :class="targetType === 'individual_dosen' ? 'bg-orange-600 text-white shadow-md shadow-orange-600/20' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                                            class="p-3.5 rounded-2xl text-left border border-slate-200/60 dark:border-slate-700 transition cursor-pointer">
                                        <div class="text-xs font-black">👨‍🏫 Khusus Dosen (Surat Tugas BKD)</div>
                                        <div class="text-[11px] opacity-80 mt-0.5">SK/Surat tugas khusus memuat daftar bimbingan 1 dosen.</div>
                                    </button>
                                </div>
                                <input type="hidden" name="target_type" :value="targetType">
                            </div>

                            <!-- Dropdown Dosen (if individual) -->
                            <div x-show="targetType === 'individual_dosen'" x-cloak class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/60 space-y-2">
                                <label class="block text-xs font-black text-amber-900 dark:text-amber-200">Pilih Dosen Pembimbing Target *</label>
                                <select name="dosen_id" x-model="dosenId" @change="fetchCandidates()"
                                        class="w-full text-xs rounded-xl bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">-- Pilih Dosen Pembimbing --</option>
                                    @foreach($dosens as $dosen)
                                        <option value="{{ $dosen->id }}">{{ $dosen->name }} {{ $dosen->identifier ? '('.$dosen->identifier.')' : '' }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-amber-700 dark:text-amber-400">
                                    Sistem akan memfilter mahasiswa yang dibimbing oleh dosen ini (baik sebagai Pembimbing 1 maupun Pembimbing 2).
                                </p>
                            </div>
                        @endif

                        <!-- Multi-filter Grid 1: Status SK & Periode Pengajuan (Kunci Semester) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                    <span x-text="targetType === 'collective' ? 'Status Penerbitan SK Dekan' : 'Status Surat Tugas Dosen'"></span>
                                    <span class="text-[10px] text-orange-600 font-bold">Rekomendasi</span>
                                </label>
                                <select x-model="decreeStatus" @change="fetchCandidates()"
                                        class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 font-semibold focus:ring-orange-500 focus:border-orange-500">
                                    <option value="unassigned" x-text="targetType === 'collective' ? '⚠️ Hanya yang Belum Di-SK-kan (Default)' : '⚠️ Belum Ada Surat Tugas Dosen Ini (Default)'"></option>
                                    <option value="all">Semua Mahasiswa (Belum & Sudah Ada Dokumen)</option>
                                    <option value="assigned" x-text="targetType === 'collective' ? '✅ Sudah Pernah Di-SK-kan' : '✅ Sudah Terbit Surat Tugas Dosen Ini'"></option>
                                </select>
                                <p class="text-[10px] text-slate-400 mt-1" x-text="targetType === 'collective' ? 'Mahasiswa yang baru memiliki Surat Tugas mandiri dosen tetap dihitung belum di-SK-kan.' : 'Surat Tugas bisa diterbitkan 2x (masing-masing untuk Pembimbing 1 & Pembimbing 2).'"></p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                    <span>Periode Pengajuan Judul</span>
                                    <span class="text-[10px] text-slate-400">Berdasarkan Semester</span>
                                </label>
                                <select x-model="submissionPeriod" @change="fetchCandidates()"
                                        class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 font-semibold focus:ring-orange-500 focus:border-orange-500">
                                    <option value="same_as_decree">Sesuai Semester SK Ini (<span x-text="academicYear + ' ' + semester"></span>)</option>
                                    <option value="all">Semua Periode Pengajuan (Termasuk Semester Lalu)</option>
                                </select>
                                <p class="text-[10px] text-slate-400 mt-1">Otomatis sinkron dengan T.A & Semester penetapan SK di sebelah kanan.</p>
                            </div>
                        </div>

                        <!-- Multi-filter Grid 2: Status Bimbingan, Angkatan, Gelombang -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Status Bimbingan</label>
                                <select x-model="status" @change="fetchCandidates()"
                                        class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="active">Sedang Berjalan (Aktif)</option>
                                    <option value="completed">Sudah Lulus (Completed)</option>
                                    <option value="all">Semua Status (Aktif & Lulus)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Angkatan Mahasiswa</label>
                                <select x-model="cohort" @change="fetchCandidates()"
                                        class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="all">Semua Angkatan</option>
                                    @foreach($cohorts as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Gelombang Pelaksanaan</label>
                                <select name="wave_id" x-model="waveId" @change="fetchCandidates()"
                                        class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Semua Gelombang / Tanpa Gelombang</option>
                                    @foreach($waves as $wave)
                                        <option value="{{ $wave->id }}">{{ $wave->name }} {{ $wave->is_active ? '(Aktif)' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Live Search Input -->
                        <div>
                            <div class="relative">
                                <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchCandidates()"
                                       placeholder="Cari nama mahasiswa, NPM, atau judul skripsi..." 
                                       class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3.5 py-2 pl-9 focus:ring-orange-500 focus:border-orange-500">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Candidate Selection Table -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-black text-slate-900 dark:text-white">Pilih Mahasiswa untuk Lampiran SK</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400"
                                      x-text="(selectedCount + manualStudents.length) + ' Terpilih'"></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="showManualModal = true"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-orange-50 hover:bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300 dark:hover:bg-orange-900/60 border border-orange-200 dark:border-orange-800 transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                    <span>Tambah Manual</span>
                                </button>
                                <span class="text-slate-300">|</span>
                                <button type="button" @click="selectAll()" class="text-xs font-bold text-orange-600 hover:text-orange-700 transition cursor-pointer">
                                    Pilih Semua
                                </button>
                                <span class="text-slate-300">|</span>
                                <button type="button" @click="deselectAll()" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                    Batal Semua
                                </button>
                            </div>
                        </div>

                        <!-- Info Pill Banner -->
                        <div class="px-4 py-2.5 rounded-2xl bg-orange-50/70 dark:bg-orange-950/30 border border-orange-200/70 dark:border-orange-900/40 text-[11px] text-orange-800 dark:text-orange-300 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>
                                    Menampilkan mahasiswa pengajuan <strong>Semester <span x-text="semester"></span> T.A <span x-text="academicYear"></span></strong> yang <strong x-text="decreeStatus === 'unassigned' ? 'belum memiliki SK Pembimbing' : (decreeStatus === 'assigned' ? 'sudah pernah di-SK-kan' : 'seluruhnya')"></strong>.
                                </span>
                            </div>
                            <span class="font-bold text-orange-700 dark:text-orange-400 shrink-0" x-text="candidateCount + ' Calon'"></span>
                        </div>

                        <!-- Manual Students List (if any added) -->
                        <div x-show="manualStudents.length > 0" class="space-y-2">
                            <div class="text-[11px] font-black uppercase text-orange-600 tracking-wider flex items-center gap-1.5">
                                <span>✍️ Mahasiswa Tambahan Manual (<span x-text="manualStudents.length"></span>)</span>
                            </div>
                            <div class="border border-orange-200 dark:border-orange-900/60 rounded-2xl overflow-hidden divide-y divide-orange-100 dark:divide-orange-950/60 bg-orange-50/30 dark:bg-orange-950/10">
                                <template x-for="(st, idx) in manualStudents" :key="'man-card-' + idx">
                                    <div class="p-3.5 flex items-start justify-between gap-3 text-xs">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-black text-slate-900 dark:text-white" x-text="st.student_name"></span>
                                                <span class="text-slate-400" x-text="'(' + st.student_npm + ')'"></span>
                                                <span class="px-1.5 py-0.2 rounded text-[10px] bg-orange-100 text-orange-700 font-bold">Manual</span>
                                            </div>
                                            <div class="text-slate-600 dark:text-slate-300 italic" x-text="st.title"></div>
                                            <div class="text-[11px] text-slate-500 flex flex-wrap gap-x-3">
                                                <span>P1: <strong x-text="st.pembimbing1_name || '-'"></strong></span>
                                                <span>P2: <strong x-text="st.pembimbing2_name || '-'"></strong></span>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeManualStudent(idx)" class="text-rose-500 hover:text-rose-700 p-1 text-xs font-bold cursor-pointer" title="Hapus">
                                            ✕
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Candidates Container from SIBIMA DB -->
                        <div class="border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden">
                            <div class="max-h-96 overflow-y-auto custom-scrollbar divide-y divide-slate-100 dark:divide-slate-800">
                                <!-- Loading State -->
                                <div x-show="isLoading" class="p-8 text-center text-slate-400 text-xs">
                                    <div class="animate-spin w-6 h-6 border-2 border-orange-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                                    <span>Memuat data mahasiswa...</span>
                                </div>

                                <!-- Empty State -->
                                <div x-show="!isLoading && candidates.length === 0" class="p-8 text-center text-slate-400 text-xs space-y-2">
                                    <p class="font-bold text-slate-600 dark:text-slate-300">Tidak ada mahasiswa yang sesuai dengan filter.</p>
                                    <p class="text-[11px] text-slate-500">
                                        Seluruh mahasiswa pada semester ini mungkin sudah pernah diterbitkan SK, atau Anda dapat mengubah filter <strong>Status Penerbitan SK</strong> / <strong>Periode Pengajuan</strong>, atau klik <strong>+ Tambah Manual</strong>.
                                    </p>
                                </div>

                                <!-- List of Candidates -->
                                <template x-for="(candidate, index) in candidates" :key="candidate.id">
                                    <label class="p-4 flex items-start gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 cursor-pointer transition select-none">
                                        <input type="checkbox" :value="candidate.id"
                                               :checked="selectedIds.includes(candidate.id)"
                                               @change="toggleSelection(candidate.id)"
                                               class="mt-1 rounded text-orange-600 focus:ring-orange-500 border-slate-300 dark:border-slate-700 cursor-pointer">
                                        
                                        <div class="flex-1 space-y-1.5 text-xs">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="font-black text-slate-900 dark:text-white" x-text="candidate.student_name"></span>
                                                    <span class="text-slate-400 text-[11px]" x-text="'(' + candidate.student_npm + ')'"></span>
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold"
                                                          x-text="'Angkatan ' + candidate.student_cohort"></span>
                                                    
                                                    <!-- Status Dokumen: SK Dekan vs Surat Tugas Dosen -->
                                                    <template x-if="targetType === 'collective'">
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                                              :class="candidate.has_sk ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800'"
                                                              x-text="candidate.has_sk ? ('✅ Sudah Di-SK-kan: ' + candidate.sk_info.decree_number) : '⚠️ Belum Ada SK'"></span>
                                                    </template>

                                                    <template x-if="targetType === 'individual_dosen'">
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                                              :class="candidate.has_decree ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800'"
                                                              x-text="candidate.has_decree ? ('✅ Sudah Ada Surat Tugas Dosen Ini: ' + candidate.decree_info.decree_number) : '⚠️ Belum Ada Surat Tugas Dosen Ini'"></span>
                                                    </template>

                                                    <!-- Badge Surat Tugas Mandiri Dosen (Bisa 2x: untuk Pembimbing 1 & Pembimbing 2) -->
                                                    <template x-if="candidate.p1_st">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800"
                                                              :title="'Surat Tugas Dosen Pembimbing 1: ' + candidate.p1_st.decree_number">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                            <span x-text="'ST P1: ' + candidate.p1_st.decree_number"></span>
                                                        </span>
                                                    </template>

                                                    <template x-if="candidate.p2_st">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800"
                                                              :title="'Surat Tugas Dosen Pembimbing 2: ' + candidate.p2_st.decree_number">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                            <span x-text="'ST P2: ' + candidate.p2_st.decree_number"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded"
                                                      :class="candidate.status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400'"
                                                      x-text="candidate.status === 'completed' ? 'Sudah Lulus' : 'Aktif Bimbingan'"></span>
                                            </div>

                                            <div class="text-slate-700 dark:text-slate-300 italic text-[11.5px] leading-relaxed" x-text="candidate.title"></div>

                                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800/80">
                                                <span class="text-slate-600 dark:text-slate-400">📅 <strong>Pengajuan:</strong> <span x-text="candidate.submission_date"></span> (<span x-text="candidate.submission_period"></span>)</span>
                                                <span>P1: <strong class="text-slate-800 dark:text-slate-200" x-text="candidate.pembimbing1_name"></strong> <span class="text-slate-400" x-text="'(' + candidate.pembimbing1_nidn + ')'"></span></span>
                                                <span>P2: <strong class="text-slate-800 dark:text-slate-200" x-text="candidate.pembimbing2_name"></strong> <span class="text-slate-400" x-text="'(' + candidate.pembimbing2_nidn + ')'"></span></span>
                                            </div>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Decree Metadata & Submission (1 col) -->
                <div class="space-y-6">
                    <!-- Step 3: Legal Metadata -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                            <span class="w-8 h-8 rounded-2xl bg-orange-600 text-white flex items-center justify-center font-black text-sm">
                                2
                            </span>
                            <div>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white">Legalitas Dokumen</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Nomor, perihal, & tanggal penetapan.</p>
                            </div>
                        </div>

                        <!-- Decree Title -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Perihal / Judul SK</label>
                            <input type="text" name="title" x-model="title" required
                                   class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <!-- Decree Number (Auto / Custom) -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Nomor SK Otomatis</label>
                                <span class="text-[10px] text-orange-600 font-bold">Auto via LetterSetting</span>
                            </div>
                            <input type="text" name="custom_decree_number" x-model="customDecreeNumber"
                                   placeholder="Contoh: 001/SK-PEMBIMBING/UNSUB/FIK/IX/2026"
                                   class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 font-mono font-bold focus:ring-orange-500 focus:border-orange-500">
                            <p class="text-[10px] text-slate-400 mt-1">Biarkan sesuai pratinjau untuk penomoran otomatis berurutan.</p>
                        </div>

                        <!-- Date & Semester Grid -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Penetapan</label>
                                <input type="date" name="decree_date" x-model="decreeDate" required
                                       class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">T.A & Semester</label>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <input type="text" name="academic_year" x-model="academicYear" required placeholder="2026/2027"
                                           class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-2.5 py-2 font-bold focus:ring-orange-500 focus:border-orange-500">
                                    <select name="semester" x-model="semester" required
                                            class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-1 py-2 font-bold focus:ring-orange-500 focus:border-orange-500">
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Signatory Section -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                            <h4 class="text-xs font-black uppercase text-slate-500 tracking-wider">Pejabat Penandatangan</h4>
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jabatan Penandatangan</label>
                                <input type="text" name="signatory_title" x-model="signatoryTitle" required
                                       class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pejabat</label>
                                <input type="text" name="signatory_name" x-model="signatoryName" required
                                       class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">NIDN / NIP (Opsional)</label>
                                <input type="text" name="signatory_identifier" x-model="signatoryIdentifier"
                                       placeholder="Nomor Induk Dosen / Pegawai"
                                       class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" x-model="notes" rows="2" 
                                      placeholder="Catatan internal pengarsipan..." 
                                      class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                        </div>
                    </div>

                    <!-- Final Action Solid Card -->
                    <div class="bg-orange-600 rounded-3xl p-6 text-white shadow-xl shadow-orange-600/20 border border-orange-500 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black uppercase tracking-wider text-orange-100">Ringkasan Penerbitan</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white" x-text="(selectedCount + manualStudents.length) + ' Mahasiswa'"></span>
                        </div>

                        <div class="space-y-1.5 text-xs">
                            <div class="flex justify-between text-orange-100">
                                <span>Jenis Dokumen:</span>
                                <strong class="text-white font-black" x-text="targetType === 'collective' ? 'Kolektif Seluruh Mahasiswa' : 'Surat Tugas Per Dosen'"></strong>
                            </div>
                            <div class="flex justify-between text-orange-100">
                                <span>Tahun Akademik:</span>
                                <strong class="text-white font-black" x-text="academicYear + ' (' + semester + ')'"></strong>
                            </div>
                            <div class="flex justify-between text-orange-100">
                                <span>Verifikasi Digital:</span>
                                <strong class="text-white font-black">QR Code Terdaftar</strong>
                            </div>
                        </div>

                        <button type="submit" :disabled="(selectedCount + manualStudents.length === 0) || isSubmitting"
                                class="w-full py-3.5 px-4 bg-white text-orange-600 hover:bg-orange-50 active:scale-[0.99] rounded-2xl font-black text-xs uppercase tracking-wider transition-all shadow-lg hover:shadow-xl cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span x-show="!isSubmitting">⚖️ Terbitkan & Simpan SK Sekarang</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin text-orange-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Memproses Dokumen...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Modal Tambah Mahasiswa Manual -->
        <div x-show="showManualModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl"
                 @click.away="showManualModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Tambah Mahasiswa Manual</h3>
                    <button type="button" @click="showManualModal = false" class="text-slate-400 hover:text-slate-600 text-sm cursor-pointer">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Mahasiswa *</label>
                        <input type="text" x-model="newManual.student_name" placeholder="Nama Lengkap Mahasiswa"
                               class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NPM *</label>
                            <input type="text" x-model="newManual.student_npm" placeholder="Contoh: 211011001"
                                   class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Angkatan</label>
                            <input type="text" x-model="newManual.student_cohort" placeholder="2021"
                                   class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Skripsi *</label>
                        <textarea x-model="newManual.title" rows="2" placeholder="Judul skripsi mahasiswa..."
                                  class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pembimbing 1 *</label>
                            <input type="text" x-model="newManual.pembimbing1_name" placeholder="Nama Pembimbing 1 & Gelar"
                                   class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIDN/NIP P1</label>
                            <input type="text" x-model="newManual.pembimbing1_nidn" placeholder="NIDN P1"
                                   class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pembimbing 2</label>
                            <input type="text" x-model="newManual.pembimbing2_name" placeholder="Nama Pembimbing 2 & Gelar"
                                   class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIDN/NIP P2</label>
                            <input type="text" x-model="newManual.pembimbing2_nidn" placeholder="NIDN P2"
                                   class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showManualModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="saveManualStudent()" class="px-4 py-2 rounded-xl text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white shadow-sm cursor-pointer">
                        Simpan ke Daftar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function advisorDecreeComposer() {
            return {
                title: '{{ Auth::user()->role === "dosen" ? "Surat Tugas Pembimbingan Skripsi Mahasiswa" : "Penetapan Dosen Pembimbing Skripsi Mahasiswa" }}',
                academicYear: '{{ $defaultAcademicYear }}',
                semester: '{{ $defaultSemester }}',
                decreeDate: '{{ now()->format("Y-m-d") }}',
                customDecreeNumber: '{{ $previewLetterNumber }}',
                targetType: '{{ Auth::user()->role === "dosen" ? "individual_dosen" : "collective" }}',
                dosenId: '{{ Auth::user()->role === "dosen" ? Auth::id() : "" }}',
                
                // Advanced Filters
                decreeStatus: 'unassigned', // unassigned (default), assigned, all
                submissionPeriod: 'same_as_decree', // same_as_decree (default), all
                status: 'active', // active (default), completed, all
                cohort: 'all',
                waveId: '',
                searchQuery: '',

                signatoryTitle: '{{ $defaultSignatoryTitle }}',
                signatoryName: '{{ $defaultSignatoryName }}',
                signatoryIdentifier: '{{ $defaultSignatoryIdentifier ?? "" }}',
                notes: '',
                candidates: [],
                selectedIds: [],
                manualStudents: [],
                showManualModal: false,
                newManual: {
                    student_name: '',
                    student_npm: '',
                    student_cohort: '2021',
                    title: '',
                    pembimbing1_name: '{{ Auth::user()->role === "dosen" ? Auth::user()->name : "" }}',
                    pembimbing1_nidn: '{{ Auth::user()->role === "dosen" ? (Auth::user()->identifier ?? "") : "" }}',
                    pembimbing2_name: '',
                    pembimbing2_nidn: '',
                },
                isLoading: false,
                isSubmitting: false,

                init() {
                    this.fetchCandidates();
                    this.$watch('academicYear', () => {
                        if (this.submissionPeriod === 'same_as_decree') {
                            this.fetchCandidates();
                        }
                    });
                    this.$watch('semester', () => {
                        if (this.submissionPeriod === 'same_as_decree') {
                            this.fetchCandidates();
                        }
                    });
                },

                get candidateCount() {
                    return this.candidates.length;
                },

                get selectedCount() {
                    return this.selectedIds.length;
                },

                async fetchCandidates() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('{{ route("advisor-decrees.candidates") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                target_type: this.targetType,
                                dosen_id: this.dosenId,
                                decree_status: this.decreeStatus,
                                submission_period: this.submissionPeriod,
                                academic_year: this.academicYear,
                                semester: this.semester,
                                status: this.status,
                                cohort: this.cohort,
                                wave_id: this.waveId,
                                search: this.searchQuery,
                            })
                        });

                        const data = await response.json();
                        this.candidates = data.candidates || [];
                        // By default, select all fetched candidates
                        this.selectedIds = this.candidates.map(c => c.id);
                    } catch (error) {
                        console.error('Error fetching candidates:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                selectAll() {
                    this.selectedIds = this.candidates.map(c => c.id);
                },

                deselectAll() {
                    this.selectedIds = [];
                },

                toggleSelection(id) {
                    const idx = this.selectedIds.indexOf(id);
                    if (idx > -1) {
                        this.selectedIds.splice(idx, 1);
                    } else {
                        this.selectedIds.push(id);
                    }
                },

                saveManualStudent() {
                    if (!this.newManual.student_name || !this.newManual.student_npm || !this.newManual.title || !this.newManual.pembimbing1_name) {
                        alert('Mohon lengkapi Nama Mahasiswa, NPM, Judul Skripsi, dan Nama Pembimbing 1.');
                        return;
                    }
                    this.manualStudents.push({ ...this.newManual });
                    this.newManual = {
                        student_name: '',
                        student_npm: '',
                        student_cohort: '2021',
                        title: '',
                        pembimbing1_name: '{{ Auth::user()->role === "dosen" ? Auth::user()->name : "" }}',
                        pembimbing1_nidn: '{{ Auth::user()->role === "dosen" ? (Auth::user()->identifier ?? "") : "" }}',
                        pembimbing2_name: '',
                        pembimbing2_nidn: '',
                    };
                    this.showManualModal = false;
                },

                removeManualStudent(idx) {
                    this.manualStudents.splice(idx, 1);
                },

                handleSubmit(event) {
                    if (this.selectedIds.length === 0 && this.manualStudents.length === 0) {
                        event.preventDefault();
                        alert('Pilih minimal satu mahasiswa dari daftar atau klik "Tambah Manual" untuk menambahkan mahasiswa.');
                        return;
                    }
                    this.isSubmitting = true;
                }
            };
        }
    </script>
</x-app-layout>

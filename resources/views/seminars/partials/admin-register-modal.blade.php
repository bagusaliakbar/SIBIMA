        {{-- ========================================== --}}
        {{-- MODAL: DAFTARKAN SEMINAR OLEH ADMIN/KAPRODI--}}
        {{-- ========================================== --}}
        <template x-teleport="body">
            <div x-show="openRegisterModal" @keydown.escape.window="if (openRegisterModal) openRegisterModal = false" role="dialog" aria-modal="true" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
                <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" @click="openRegisterModal = false"></div>
                
                <div class="min-h-full flex items-center justify-center p-4 text-center sm:p-6">
                    <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-100 dark:border-slate-700 my-8" @click.stop>
                        {{-- Modal Header --}}
                        <div class="px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between bg-gradient-to-r from-orange-500/10 via-slate-50/50 to-transparent dark:from-orange-500/10 dark:via-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-orange-600 text-white flex items-center justify-center shadow-sm shadow-orange-500/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight">Daftarkan Seminar Proposal</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Pendaftaran mahasiswa langsung oleh Administrator / Kaprodi</p>
                                </div>
                            </div>
                            <button type="button" @click="openRegisterModal = false" aria-label="Tutup modal pendaftaran seminar" class="text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        {{-- Modal Form --}}
                        <form action="{{ route('seminar-applications.admin-register') }}" method="POST" enctype="multipart/form-data" @submit="isRegistering = true">
                            @csrf
                            <div class="px-6 sm:px-8 py-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                                {{-- Pilih Mahasiswa --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        Pilih Mahasiswa & Judul Skripsi <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="thesis_id" 
                                            x-model="selectedThesisId" 
                                            @change="onSelectThesis($event.target.value)" 
                                            required
                                            class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all p-3">
                                        <option value="">-- Pilih Mahasiswa yang Belum Terdaftar Seminar --</option>
                                        <template x-for="t in candidateTheses" :key="t.id">
                                            <option :value="t.id" x-text="t.student_identifier + ' - ' + t.student_name + (t.is_acc_up ? ' [ACC UP LENGKAP]' : ' [BELUM ACC UP]')"></option>
                                        </template>
                                    </select>
                                    <template x-if="candidateTheses.length === 0">
                                        <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 italic">
                                            Tidak ada mahasiswa skripsi aktif yang belum seminar saat ini.
                                        </p>
                                    </template>
                                </div>

                                {{-- Detail Mahasiswa Terpilih Card --}}
                                <template x-if="selectedThesisData">
                                    <div class="p-4 rounded-2xl bg-orange-50/60 dark:bg-orange-950/20 border border-orange-200/80 dark:border-orange-800/40 space-y-2">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <h4 class="text-xs font-black text-slate-900 dark:text-slate-100" x-text="selectedThesisData.student_name + ' (' + selectedThesisData.student_identifier + ')'"></h4>
                                                <p class="text-[11px] text-slate-600 dark:text-slate-300 line-clamp-2 mt-0.5 font-medium" x-text="selectedThesisData.title"></p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-black uppercase tracking-wider shrink-0"
                                                  :class="selectedThesisData.is_acc_up ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300'">
                                                <span x-text="selectedThesisData.is_acc_up ? 'ACC UP Lengkap' : 'Perlu Dispensasi ACC'"></span>
                                            </span>
                                        </div>
                                        <div class="pt-2 border-t border-orange-200/50 dark:border-orange-800/30 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-slate-500 dark:text-slate-400">
                                            <span>Pembimbing 1: <strong class="text-slate-700 dark:text-slate-200" x-text="selectedThesisData.p1_name"></strong></span>
                                            <span>•</span>
                                            <span>Pembimbing 2: <strong class="text-slate-700 dark:text-slate-200" x-text="selectedThesisData.p2_name"></strong></span>
                                        </div>
                                    </div>
                                </template>

                                {{-- Pilih Gelombang & Status Awal --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                            Gelombang Seminar <span class="text-rose-500">*</span>
                                        </label>
                                        <select name="wave_id" required class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all p-3">
                                            @foreach($waves as $w)
                                                <option value="{{ $w->id }}" {{ $activeWave && $activeWave->id === $w->id ? 'selected' : '' }}>
                                                    {{ strtoupper($w->name) }} {{ $w->is_active ? '(AKTIF)' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                            Status Pendaftaran Awal <span class="text-rose-500">*</span>
                                        </label>
                                        <select name="status" required class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all p-3">
                                            <option value="approved">✓ Langsung Disetujui (Siap Jadwal)</option>
                                            <option value="pending">Menunggu Validasi (Pending)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Dispensasi Berkas Toggle --}}
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 space-y-3">
                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                        <input type="checkbox" name="bypass_files" value="1" x-model="bypassFiles" class="rounded text-orange-600 focus:ring-orange-500 w-4 h-4">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                            Dispensasi 5 Berkas Persyaratan (Didaftarkan Langsung oleh Admin / Kaprodi)
                                        </span>
                                    </label>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 pl-6 leading-relaxed">
                                        Jika dicentang, pengisian tautan berkas tidak diwajibkan. Berkas akan ditandai sebagai dispensasi administratif resmi dari Prodi.
                                    </p>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 pl-6">
                                            Catatan / Memo Dispensasi (Opsional)
                                        </label>
                                        <div class="pl-6">
                                            <input type="text" 
                                                   name="admin_notes" 
                                                   placeholder="Contoh: Dispensasi persetujuan Kaprodi via Memo No. 12/FASILKOM/2026" 
                                                   class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 py-2.5 px-3">
                                        </div>
                                    </div>
                                </div>

                                {{-- Input Tautan Link Google Drive jika tidak dispensasi --}}
                                <div x-show="!bypassFiles" x-collapse class="space-y-4 p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700">
                                    {{-- Batch Google Drive Folder Filler --}}
                                    <div class="p-3.5 bg-orange-50/80 dark:bg-orange-950/30 border border-orange-200 dark:border-orange-800/50 rounded-xl space-y-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">1 Link Folder Drive untuk Semua Berkas Seminar</span>
                                        </div>
                                        <p class="text-[11px] text-slate-600 dark:text-slate-400">Jika mahasiswa memiliki folder Google Drive berisi seluruh berkasnya, tempelkan di sini:</p>
                                        <div class="flex items-center gap-2">
                                            <input type="url" 
                                                   name="batch_folder_url"
                                                   x-model="regBatchFolderUrl" 
                                                   placeholder="https://drive.google.com/drive/folders/..." 
                                                   class="flex-1 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 focus:ring-orange-500 py-2 px-3">
                                            <button type="button" 
                                                    @click="applyRegBatchFolder()" 
                                                    class="px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs shrink-0 cursor-pointer">
                                                Terapkan
                                            </button>
                                        </div>
                                    </div>

                                    <h4 class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">Tautan Dokumen Seminar (5 Berkas)</h4>
                                    
                                    <div class="space-y-3 text-xs">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                                                1. Formulir Pendaftaran Seminar
                                            </label>
                                            <input type="url" 
                                                   name="file_formulir" 
                                                   x-model="regFiles.file_formulir"
                                                   placeholder="https://drive.google.com/file/d/.../view" 
                                                   class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 py-2 px-3">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                                                2. Scan Bukti Pembayaran Seminar
                                            </label>
                                            <input type="url" 
                                                   name="file_pembayaran" 
                                                   x-model="regFiles.file_pembayaran"
                                                   placeholder="https://drive.google.com/file/d/.../view" 
                                                   class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 py-2 px-3">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                                                3. Scan Bukti ACC Seminar UP
                                            </label>
                                            <input type="url" 
                                                   name="file_acc_pembimbing" 
                                                   x-model="regFiles.file_acc_pembimbing"
                                                   placeholder="https://drive.google.com/file/d/.../view" 
                                                   class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 py-2 px-3">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                                                4. Scan Kartu Bimbingan / Logbook Proposal
                                            </label>
                                            <input type="url" 
                                                   name="file_kartu_bimbingan" 
                                                   x-model="regFiles.file_kartu_bimbingan"
                                                   placeholder="https://drive.google.com/file/d/.../view" 
                                                   class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 py-2 px-3">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                                                5. Dokumen Draf Proposal Skripsi (Bab 1–3)
                                            </label>
                                            <input type="url" 
                                                   name="file_skripsi" 
                                                   x-model="regFiles.file_skripsi"
                                                   placeholder="https://drive.google.com/file/d/.../view" 
                                                   class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-orange-500 py-2 px-3">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="px-6 sm:px-8 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                                <button type="button" @click="openRegisterModal = false" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-slate-300 transition-colors">Batal</button>
                                <button type="submit" 
                                        :disabled="!selectedThesisId || isRegistering"
                                        class="px-5 py-2.5 bg-orange-600 text-white text-xs font-bold rounded-xl uppercase tracking-wider hover:bg-orange-700 shadow-sm shadow-orange-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    <template x-if="isRegistering">
                                        <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </template>
                                    <span x-text="isRegistering ? 'Menyimpan...' : 'Daftarkan Seminar'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

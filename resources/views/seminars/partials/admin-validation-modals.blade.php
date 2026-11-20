        {{-- GLOBAL WIDE 2-COLUMN VALIDATION MODAL --}}
        <template x-teleport="body">
            <div x-show="openValidation" @keydown.escape.window="if (openValidation) openValidation = false" role="dialog" aria-modal="true" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="openValidation = false"></div>

                <div class="min-h-full flex items-center justify-center p-4 text-center sm:p-6">
                    <div class="relative w-full max-w-4xl bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 my-8" @click.stop>
                        <template x-if="selectedApp">
                            <div>
                                {{-- Modal Header --}}
                                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-slate-900">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight">Validasi Berkas Seminar Proposal</h3>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-orange-100 dark:bg-orange-950 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800">
                                                5 Dokumen
                                            </span>
                                        </div>
                                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400 mt-0.5">
                                            <span x-text="selectedApp.student_name"></span> <span class="font-mono text-slate-400 dark:text-slate-500">(<span x-text="selectedApp.student_id"></span>)</span>
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="setAllStatus('approved')" 
                                                class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl text-[10px] font-bold transition-all shadow-2xs cursor-pointer">
                                            ✓ Setujui Semua (OK)
                                        </button>
                                        <button type="button" @click="setAllStatus('rejected')" 
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 dark:hover:bg-rose-900 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-xl text-[10px] font-bold transition-all shadow-2xs cursor-pointer">
                                            ✕ Tolak Semua
                                        </button>
                                        <button type="button" @click="openValidation = false" aria-label="Tutup modal validasi seminar" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors ml-1 cursor-pointer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <form x-ref="validSeminarForm" :action="selectedApp.validate_url" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="px-6 py-5 space-y-5 max-h-[66vh] overflow-y-auto bg-white dark:bg-slate-900">
                                        <div>
                                            <div class="flex items-center justify-between mb-2.5">
                                                <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest block">
                                                    Verifikasi 5 Berkas Persyaratan:
                                                </label>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Klik nama berkas untuk melihat dokumen</span>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                @foreach($fileLabels as $field => $label)
                                                    <div class="p-3 rounded-2xl border flex flex-col justify-between gap-2 transition-all shadow-2xs"
                                                         :class="(selectedApp.file_reviews && selectedApp.file_reviews['{{ $field }}'] && selectedApp.file_reviews['{{ $field }}'].status === 'rejected') ? 'border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800'"
                                                         x-data="{ isRejected: (selectedApp.file_reviews && selectedApp.file_reviews['{{ $field }}'] && selectedApp.file_reviews['{{ $field }}'].status === 'rejected') }">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <a :href="selectedApp.files['{{ $field }}']" target="_blank" 
                                                               class="text-xs font-bold text-slate-900 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1.5 truncate group/filelink"
                                                               title="Buka {{ $label }}">
                                                                <span class="truncate">{{ $label }}</span>
                                                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover/filelink:text-indigo-600 dark:group-hover/filelink:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                            </a>

                                                            {{-- Radio Controls (OK / Tolak) Segmented Pill --}}
                                                            <div class="flex items-center p-0.5 bg-slate-200 dark:bg-slate-900 rounded-xl border border-slate-300 dark:border-slate-700 shrink-0">
                                                                <label class="px-2 py-1 rounded-lg text-[10px] cursor-pointer transition-all flex items-center gap-1"
                                                                       :class="!isRejected ? 'bg-emerald-600 text-white shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-bold'">
                                                                    <input type="radio" name="file_reviews[{{ $field }}][status]" value="approved" 
                                                                           @change="isRejected = false"
                                                                           :checked="!isRejected"
                                                                           class="sr-only">
                                                                    <span>✓ OK</span>
                                                                </label>
                                                                <label class="px-2 py-1 rounded-lg text-[10px] cursor-pointer transition-all flex items-center gap-1"
                                                                       :class="isRejected ? 'bg-rose-600 text-white shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-bold'">
                                                                    <input type="radio" name="file_reviews[{{ $field }}][status]" value="rejected" 
                                                                           @change="isRejected = true"
                                                                           :checked="isRejected"
                                                                           class="sr-only">
                                                                    <span>✕ Tolak</span>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        {{-- Note Input --}}
                                                        <input type="text" name="file_reviews[{{ $field }}][note]" 
                                                               :value="selectedApp.file_reviews && selectedApp.file_reviews['{{ $field }}'] ? selectedApp.file_reviews['{{ $field }}'].note : ''" 
                                                               placeholder="Catatan revisi jika berkas ditolak..." 
                                                               :class="isRejected ? 'border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950 text-rose-900 dark:text-rose-200 placeholder-rose-400 dark:placeholder-rose-400' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500'"
                                                               class="w-full rounded-xl text-xs font-medium px-3 py-1.5 border transition-all focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Final Decision & Admin Global Note in 2 Columns --}}
                                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                                    Status Akhir Pengajuan:
                                                </label>
                                                <select name="status" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs font-bold uppercase tracking-wider p-2.5 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-2xs">
                                                    <option value="approved" :selected="selectedApp.status === 'approved'">✓ SETUJUI (BERKAS VALID & LENGKAP)</option>
                                                    <option value="rejected" :selected="selectedApp.status === 'rejected'">✕ TOLAK (PERLU REVISI BERKAS)</option>
                                                    <option value="pending" :selected="selectedApp.status === 'pending'">TETAP MENUNGGU</option>
                                                </select>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">
                                                    Pilih "SETUJUI" jika seluruh 5 berkas telah lengkap dan sesuai syarat.
                                                </p>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                                    Catatan Admin (Global / Feedback Mahasiswa):
                                                </label>
                                                <textarea name="admin_feedback" rows="2" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs font-medium p-2.5 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-2xs" placeholder="Silakan upload ulang berkas yang ditolak..." x-text="selectedApp.admin_feedback || ''"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal Footer --}}
                                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                            Perubahan akan tersimpan dan dapat dilihat oleh mahasiswa.
                                        </span>
                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="openValidation = false" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">Batal</button>
                                            <button type="submit" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl uppercase tracking-wider shadow-xs shadow-orange-500/20 transition-all cursor-pointer">Simpan Keputusan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        {{-- GLOBAL PREVIEW APPLICATION MODAL WITH IN-APP VIEWER --}}
        <template x-teleport="body">
            <div x-show="openPreview" @keydown.escape.window="if (openPreview) openPreview = false" role="dialog" aria-modal="true" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="openPreview = false"></div>

                <div class="min-h-full flex items-center justify-center p-3 sm:p-5 text-center">
                    <div class="relative w-full max-w-6xl bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 my-4 flex flex-col max-h-[92vh]" @click.stop>
                        <template x-if="previewApp">
                            <div class="flex flex-col h-full overflow-hidden">
                                {{-- Modal Header --}}
                                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/90 shrink-0">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight">Pratinjau Pendaftaran Seminar Proposal</h3>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider"
                                                      :class="{
                                                          'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800': previewApp.status === 'approved',
                                                          'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800': previewApp.status === 'rejected',
                                                          'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800': previewApp.status === 'pending'
                                                      }"
                                                      x-text="previewApp.status === 'approved' ? 'DISETUJUI' : (previewApp.status === 'rejected' ? 'PERLU REVISI' : 'MENUNGGU VALIDASI')"></span>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                Gelombang: <span class="font-bold text-slate-700 dark:text-slate-300" x-text="previewApp.wave_name"></span> • Tanggal Daftar: <span class="font-mono text-slate-600 dark:text-slate-400" x-text="previewApp.created_at_formatted"></span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" @click="switchToValidation()" class="px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-xs shadow-orange-500/20 cursor-pointer flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span class="hidden sm:inline">Validasi Berkas</span>
                                        </button>
                                        <button type="button" @click="openPreview = false" aria-label="Tutup pratinjau berkas seminar" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center transition-colors cursor-pointer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Modal Body: Split Pane --}}
                                <div class="flex-1 overflow-hidden grid grid-cols-1 lg:grid-cols-12 bg-slate-100 dark:bg-slate-950">
                                    {{-- Kolom Kiri: Info Mahasiswa & Daftar 5 Berkas (lg:col-span-4) --}}
                                    <div class="lg:col-span-4 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 p-5 overflow-y-auto space-y-4 max-h-[40vh] lg:max-h-none">
                                        {{-- Mahasiswa Card --}}
                                        <div class="bg-slate-50 dark:bg-slate-800/70 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-xs bg-white dark:bg-slate-700 shrink-0">
                                                    <img :src="previewApp.avatar_url" :alt="previewApp.student_name" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight truncate" x-text="previewApp.student_name"></h4>
                                                    <div class="flex items-center gap-1.5 mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                                        <span class="font-mono font-bold" x-text="previewApp.student_id"></span>
                                                        <template x-if="previewApp.entry_year">
                                                            <span>• Angkatan <span x-text="previewApp.entry_year"></span></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-3 pt-3 border-t border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between text-xs">
                                                <span class="text-slate-500 dark:text-slate-400 truncate max-w-[170px]" x-text="previewApp.student_email"></span>
                                                <template x-if="previewApp.clean_phone">
                                                    <a :href="'https://wa.me/' + previewApp.clean_phone + '?text=' + previewApp.wa_message" target="_blank"
                                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 rounded-lg text-[10px] font-bold border border-emerald-200 dark:border-emerald-800 transition-colors">
                                                        <span>WhatsApp</span>
                                                    </a>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Judul Skripsi & Pembimbing --}}
                                        <div class="bg-slate-50 dark:bg-slate-800/70 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 space-y-2.5">
                                            <div>
                                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Judul Proposal Skripsi:</span>
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1 leading-relaxed" x-text="previewApp.thesis_title || '-'"></p>
                                            </div>
                                            <div class="pt-2 border-t border-slate-200/80 dark:border-slate-700/80 space-y-1.5 text-xs">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-4 h-4 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[8px] font-black shrink-0">1</span>
                                                    <span class="text-slate-700 dark:text-slate-300 font-medium truncate" x-text="previewApp.pembimbing1_name || 'Belum ada Pembimbing 1'"></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[8px] font-black shrink-0">2</span>
                                                    <span class="text-slate-700 dark:text-slate-300 font-medium truncate" x-text="previewApp.pembimbing2_name || 'Belum ada Pembimbing 2'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Catatan Admin Sebelumnya (jika ada) --}}
                                        <template x-if="previewApp.admin_feedback">
                                            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-2xl border border-amber-200 dark:border-amber-800 text-xs">
                                                <span class="text-[9px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400 block mb-1">Catatan Validasi Admin:</span>
                                                <p class="text-amber-900 dark:text-amber-200 font-medium italic" x-text="'&quot;' + previewApp.admin_feedback + '&quot;'"></p>
                                            </div>
                                        </template>

                                        {{-- Daftar 5 Berkas Persyaratan Interaktif --}}
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Pilih Berkas untuk Ditinjau:</span>
                                                <span class="text-[10px] font-bold text-orange-600 dark:text-orange-400">5 Dokumen</span>
                                            </div>
                                            <div class="space-y-1.5">
                                                @foreach($fileLabels as $field => $label)
                                                    <button type="button" 
                                                            @click="selectPreviewFile('{{ $field }}')"
                                                            class="w-full text-left p-3 rounded-xl border transition-all flex items-center justify-between gap-2 cursor-pointer shadow-2xs"
                                                            :class="previewFileKey === '{{ $field }}' ? 'border-orange-500 bg-orange-50 dark:bg-orange-950/40 ring-1 ring-orange-500' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/60'">
                                                        <div class="min-w-0 flex-1">
                                                            <span class="text-xs font-bold block truncate" 
                                                                  :class="previewFileKey === '{{ $field }}' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-800 dark:text-slate-200'">
                                                                {{ $label }}
                                                            </span>
                                                            <template x-if="previewApp.file_reviews && previewApp.file_reviews['{{ $field }}'] && previewApp.file_reviews['{{ $field }}'].note">
                                                                <span class="text-[9px] text-rose-600 dark:text-rose-400 truncate block mt-0.5" x-text="'Catatan: ' + previewApp.file_reviews['{{ $field }}'].note"></span>
                                                            </template>
                                                        </div>
                                                        <div class="shrink-0">
                                                            <template x-if="previewApp.file_reviews && previewApp.file_reviews['{{ $field }}'] && previewApp.file_reviews['{{ $field }}'].status === 'rejected'">
                                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">✕ Tolak</span>
                                                            </template>
                                                            <template x-if="previewApp.file_reviews && previewApp.file_reviews['{{ $field }}'] && previewApp.file_reviews['{{ $field }}'].status === 'approved'">
                                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">✓ OK</span>
                                                            </template>
                                                            <template x-if="!previewApp.file_reviews || !previewApp.file_reviews['{{ $field }}'] || !previewApp.file_reviews['{{ $field }}'].status">
                                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">Lihat</span>
                                                            </template>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Kolom Kanan: In-App Document Viewer (lg:col-span-8) --}}
                                    <div class="lg:col-span-8 flex flex-col h-[65vh] lg:h-auto bg-slate-900 overflow-hidden">
                                        {{-- Viewer Toolbar --}}
                                        <div class="px-4 py-3 bg-slate-800 border-b border-slate-700 flex items-center justify-between gap-3 text-white text-xs shrink-0">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <svg class="w-4 h-4 text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span class="text-orange-400 font-bold shrink-0">Dokumen:</span>
                                                <span class="font-bold truncate" x-text="previewFileLabel"></span>
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0">
                                                {{-- Navigasi Sebelumnya / Selanjutnya --}}
                                                <button type="button" @click="prevPreviewFile()" class="p-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-300 hover:text-white transition-colors cursor-pointer" title="Dokumen Sebelumnya">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                </button>
                                                <button type="button" @click="nextPreviewFile()" class="p-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-300 hover:text-white transition-colors cursor-pointer" title="Dokumen Selanjutnya">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </button>

                                                <template x-if="previewRawUrl">
                                                    <div class="flex items-center gap-2">
                                                        <a :href="previewRawUrl" target="_blank" 
                                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-xs font-bold transition-all shadow-2xs"
                                                           title="Buka dokumen di tab baru">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                            <span class="hidden sm:inline">Buka Tab Baru</span>
                                                        </a>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Google Drive Notice & Fast WA Contact Banner --}}
                                        <template x-if="isGoogleDrive()">
                                            <div class="px-4 py-2.5 bg-amber-950/80 border-b border-amber-800/60 flex flex-wrap items-center justify-between gap-2.5 text-xs shrink-0">
                                                <div class="flex items-center gap-2 text-amber-200 min-w-0">
                                                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    <span class="text-[11px] font-medium leading-tight">
                                                        Tautan Google Drive. Jika muncul pesan <strong class="text-white font-bold">"403 / You do not have access"</strong>, hak akses file Drive mahasiswa belum dibuka ke publik (<em class="italic text-amber-300 font-semibold">Anyone with the link</em>).
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2 shrink-0">
                                                    <template x-if="previewApp && previewApp.clean_phone">
                                                        <a :href="'https://wa.me/' + previewApp.clean_phone + '?text=' + encodeURIComponent('Halo ' + previewApp.student_name + ', terkait pengajuan berkas di SIBIMA: Berkas ' + previewFileLabel + ' Anda tidak dapat dibuka (Google Drive 403 / Akses Dibatasi). Mohon segera ubah setelan akses link Google Drive Anda menjadi \'Siapa saja yang memiliki link dapat melihat\' (Anyone with the link) agar dapat divalidasi oleh prodi. Terima kasih.')"
                                                           target="_blank"
                                                           class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider transition-colors shadow-2xs cursor-pointer">
                                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                            <span>Minta Buka Akses via WA</span>
                                                        </a>
                                                    </template>
                                                    <a :href="previewRawUrl" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-amber-300 hover:text-white rounded-lg text-[10px] font-bold transition-colors border border-amber-500/30">
                                                        <span>Buka Tab Baru ↗</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Viewer Content Area --}}
                                        <div class="flex-1 relative bg-slate-950 flex items-center justify-center overflow-hidden">
                                            {{-- Loading State --}}
                                            <div x-show="previewLoading" class="absolute inset-0 z-20 bg-slate-950/80 backdrop-blur-xs flex flex-col items-center justify-center text-slate-300 gap-2">
                                                <svg class="w-8 h-8 animate-spin text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                <span class="text-xs font-bold uppercase tracking-wider">Memuat Dokumen...</span>
                                            </div>

                                            <template x-if="previewFileUrl">
                                                <iframe :src="previewFileUrl" 
                                                        class="w-full h-full border-0 bg-white"
                                                        allow="autoplay; fullscreen">
                                                </iframe>
                                            </template>

                                            <template x-if="!previewFileUrl">
                                                <div class="text-center p-8 text-slate-500">
                                                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <p class="text-sm font-bold text-slate-400">Berkas ini belum diunggah atau tautan kosong.</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Footer --}}
                                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0">
                                    <div>
                                        <template x-if="previewApp.download_zip_url">
                                            <a :href="previewApp.download_zip_url" 
                                               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                <span>Download ZIP (5 Berkas)</span>
                                            </a>
                                        </template>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="openPreview = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">Tutup</button>
                                        <button type="button" @click="switchToValidation()" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl uppercase tracking-wider shadow-xs shadow-orange-500/20 transition-all cursor-pointer">
                                            Lanjut ke Validasi →
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

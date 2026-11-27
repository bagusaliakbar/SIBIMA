            {{-- Mobile Card View --}}
            <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700/80">
                @forelse($applications as $app)
                    @php
                        $student = $app->thesis->student;
                        $p1 = $app->thesis->pembimbing1;
                        $p2 = $app->thesis->pembimbing2;
                        $phone = $student->phone_number;
                        $cleanPhone = $phone ? (str_starts_with(preg_replace('/[^0-9]/', '', $phone), '0') ? '62' . substr(preg_replace('/[^0-9]/', '', $phone), 1) : preg_replace('/[^0-9]/', '', $phone)) : null;
                        $rejectedCount = 0;
                        if (!empty($app->file_reviews)) {
                            foreach ($app->file_reviews as $rev) {
                                if (($rev['status'] ?? '') === 'rejected') $rejectedCount++;
                            }
                        }
                        $mHasDigitalForm = $app->hasDigitalForm();
                        $mFormulirUrl = $mHasDigitalForm
                            ? route('thesis-defense-applications.form-pdf', ['application' => $app->id, 'inline' => 1])
                            : $app->file_formulir;
                        $mFormulirDownloadUrl = $mHasDigitalForm
                            ? route('thesis-defense-applications.form-pdf', $app->id)
                            : null;
                        $mAppData = [
                            'id' => $app->id,
                            'validate_url' => route('thesis-defense-applications.validate', $app->id),
                            'download_zip_url' => route('thesis-defense-applications.download-zip', $app->id),
                            'has_digital_form' => $mHasDigitalForm,
                            'form_pdf_download_url' => $mFormulirDownloadUrl,
                            'form_data' => $mHasDigitalForm ? $app->getResolvedFormData() : null,
                            'student_name' => $student->name,
                            'student_id' => $student->identifier,
                            'student_email' => $student->email,
                            'student_phone' => $phone,
                            'clean_phone' => $cleanPhone,
                            'wa_message' => rawurlencode("Halo {$student->name}, terkait pengajuan Sidang Skripsi Anda di SIBIMA..."),
                            'entry_year' => $student->entry_year,
                            'avatar_url' => $student->avatar ? \Illuminate\Support\Facades\Storage::url($student->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=f97316&color=ffffff&bold=true',
                            'thesis_title' => $app->thesis->final_title ?: $app->thesis->title,
                            'pembimbing1_name' => $p1 ? $p1->name : null,
                            'pembimbing2_name' => $p2 ? $p2->name : null,
                            'wave_name' => $app->wave ? $app->wave->name : '-',
                            'created_at_formatted' => $app->created_at ? $app->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
                            'status' => $app->status,
                            'admin_feedback' => $app->admin_feedback,
                            'file_reviews' => $app->file_reviews ?? [],
                            'files' => [
                                'file_formulir' => $mFormulirUrl, 'file_transkrip' => $app->file_transkrip,
                                'file_acc_pembimbing' => $app->file_acc_pembimbing, 'file_logbook' => $app->file_logbook,
                                'file_pembayaran' => $app->file_pembayaran, 'file_skripsi' => $app->file_skripsi,
                                'file_ktm' => $app->file_ktm, 'file_pkkmb_univ' => $app->file_pkkmb_univ,
                                'file_pkkmb_fak' => $app->file_pkkmb_fak, 'file_makrab' => $app->file_makrab,
                                'file_cisco' => $app->file_cisco, 'file_workshop' => $app->file_workshop,
                                'file_organisasi' => $app->file_organisasi, 'file_toefl' => $app->file_toefl,
                                'file_kewirausahaan' => $app->file_kewirausahaan, 'file_tahsin' => $app->file_tahsin,
                                'file_komputer' => $app->file_komputer, 'file_perpus_pinjam' => $app->file_perpus_pinjam,
                                'file_perpus_sumbang' => $app->file_perpus_sumbang, 'file_ijazah' => $app->file_ijazah,
                            ]
                        ];
                    @endphp
                    <div class="p-4 space-y-3 bg-white dark:bg-slate-800">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h4 class="font-bold text-sm text-slate-900 dark:text-white uppercase tracking-tight">{{ $student->name }}</h4>
                                <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400 mt-0.5">{{ $student->identifier }}{{ $app->created_at ? ' • ' . $app->created_at->translatedFormat('d M Y') : '' }}</p>
                            </div>
                            <div class="shrink-0">
                                @if($app->status === 'approved')
                                    <x-status-badge type="emerald" label="DISETUJUI" size="xs" />
                                @elseif($app->status === 'rejected')
                                    <x-status-badge type="rose" label="DITOLAK" size="xs" />
                                @else
                                    <x-status-badge type="amber" label="MENUNGGU" size="xs" />
                                @endif
                            </div>
                        </div>

                        @if($app->thesis->title)
                            <p class="text-xs text-slate-700 dark:text-slate-300 line-clamp-2 bg-slate-50 dark:bg-slate-900/50 p-2.5 rounded-xl border border-slate-100 dark:border-slate-700/60">{{ $app->thesis->title }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" @click="selectedApp = {{ json_encode($mAppData) }}; showFiles = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border {{ $rejectedCount > 0 ? 'border-rose-300 dark:border-rose-800 text-rose-700 dark:text-rose-300' : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200' }} rounded-xl text-xs font-bold cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>20 Berkas{{ $rejectedCount > 0 ? " ({$rejectedCount} Ditolak)" : '' }}</span>
                            </button>
                            @if($mHasDigitalForm)
                                <a href="{{ $mFormulirUrl }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 rounded-xl text-xs font-bold">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <span>Formulir (PDF)</span>
                                </a>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <button type="button" @click="openPreviewModal({{ json_encode($mAppData) }})" class="flex-1 h-9 inline-flex items-center justify-center gap-1.5 px-3 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-xl text-xs font-bold transition-colors cursor-pointer">
                                <span>Preview</span>
                            </button>
                            <button type="button" @click="selectedApp = {{ json_encode($mAppData) }}; openValidation = true" class="flex-1 h-9 inline-flex items-center justify-center px-3 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                Validasi
                            </button>
                            <form action="{{ route('thesis-defense-applications.destroy', $app) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin MEMBATALKAN &amp; MENGHAPUS pengajuan sidang skripsi untuk mahasiswa {{ $student->name }}?" data-confirm-title="Batalkan Pengajuan Sidang" data-confirm-type="danger" data-confirm-button="Ya, Batalkan & Hapus">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="Hapus pengajuan {{ $student->name }}" title="Batalkan & Hapus Pengajuan Sidang" class="h-9 w-9 inline-flex items-center justify-center bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-xl transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Tidak ada pengajuan sidang skripsi yang sesuai dengan kriteria filter.</p>
                    </div>
                @endforelse
            </div>

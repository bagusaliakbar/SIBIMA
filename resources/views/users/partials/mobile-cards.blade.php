            <!-- Mobile Card View -->
            <div x-show="!pageLoading" class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700/80">
                @forelse($users as $user)
                    @php
                        $cleanPhone = null;
                        $waMessage = '';
                        if ($user->phone_number) {
                            $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone_number);
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                            $waMessage = $user->role === 'mahasiswa'
                                ? "Halo {$user->name} ({$user->identifier}), saya Admin SIBIMA Program Studi. Terkait akun SIBIMA Anda:"
                                : ($user->role === 'dosen' ? "Halo Bapak/Ibu {$user->name}, saya Admin SIBIMA Program Studi. Terkait akun dan bimbingan SIBIMA:" : "Halo {$user->name}, saya Admin SIBIMA Program Studi:");
                            if (!$user->is_active) {
                                $waMessage .= " Akun SIBIMA Anda saat ini menunggu aktivasi/verifikasi data.";
                            }
                        }
                        $mThesisData = null;
                        if ($user->role === 'mahasiswa' && $user->thesis) {
                            $mStageText = 'Pengajuan Judul';
                            $mStageColor = 'slate';
                            if ($user->thesis->status === 'completed') { $mStageText = 'Lulus / Selesai'; $mStageColor = 'emerald'; }
                            elseif ($user->thesis->isAccSidangFinal()) { $mStageText = 'Siap Sidang Skripsi (ACC Pembimbing)'; $mStageColor = 'indigo'; }
                            elseif ($user->thesis->isAccUpFinal()) { $mStageText = 'Selesai Seminar UP (ACC Pembimbing)'; $mStageColor = 'blue'; }
                            elseif ($user->thesis->completed_mentoring_count > 0) { $mStageText = "Bimbingan Reguler ({$user->thesis->completed_mentoring_count} Sesi Selesai)"; $mStageColor = 'amber'; }
                            $mThesisData = [
                                'id' => $user->thesis->id,
                                'title' => $user->thesis->final_title ?: $user->thesis->title,
                                'topic' => $user->thesis->topic ?: 'Belum diatur',
                                'stage' => $mStageText,
                                'stage_color' => $mStageColor,
                                'pembimbing1' => $user->thesis->pembimbing1->name ?? 'Belum ditentukan',
                                'pembimbing2' => $user->thesis->pembimbing2->name ?? 'Belum ditentukan',
                                'url' => route('theses.show', $user->thesis->id),
                                'is_old_cohort' => $user->entry_year ? ($user->entry_year <= $oldCohortThresholdYear) : false,
                            ];
                        }
                        $mDosenData = null;
                        if ($user->role === 'dosen' || $user->role === 'kaprodi') {
                            $p1List = $user->thesesAsP1->map(fn($t) => ['student_name' => $t->student->name ?? '-', 'student_npm' => $t->student->identifier ?? '-', 'title' => $t->final_title ?: $t->title, 'role' => 'Pembimbing 1'])->values()->toArray();
                            $p2List = $user->thesesAsP2->map(fn($t) => ['student_name' => $t->student->name ?? '-', 'student_npm' => $t->student->identifier ?? '-', 'title' => $t->final_title ?: $t->title, 'role' => 'Pembimbing 2'])->values()->toArray();
                            $mDosenData = ['research_interests' => $user->research_interests ?: 'Belum diatur', 'max_quota' => $user->max_quota ?? 10, 'p1_count' => count($p1List), 'p2_count' => count($p2List), 'total_active' => count($p1List) + count($p2List), 'students' => array_merge($p1List, $p2List)];
                        }
                        $mUserData = [
                            'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'has_email_verified' => (bool)$user->email_verified_at,
                            'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->translatedFormat('d F Y, H:i') : null,
                            'avatar_url' => $user->avatar_url, 'role' => $user->role, 'identifier' => $user->identifier, 'phone_number' => $user->phone_number,
                            'has_wa' => !empty($user->phone_number), 'clean_phone' => $cleanPhone, 'wa_message' => rawurlencode($waMessage),
                            'entry_year' => $user->entry_year, 'is_old_cohort' => $user->entry_year ? ($user->entry_year <= $oldCohortThresholdYear) : false,
                            'is_active' => (bool)$user->is_active, 'registered_at' => $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') : '-',
                            'last_login_at_human' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah login',
                            'last_login_at_full' => $user->last_login_at ? $user->last_login_at->translatedFormat('d F Y, H:i') : null,
                            'edit_url' => route('users.edit', $user->id), 'toggle_url' => route('users.toggle', $user->id),
                            'can_impersonate' => in_array($user->role, ['dosen', 'mahasiswa'], true) && $user->is_active && $user->id !== Auth::id() && !session()->has(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_ID),
                            'impersonate_url' => route('users.impersonate', $user->id),
                            'thesis' => $mThesisData, 'dosen' => $mDosenData,
                        ];
                    @endphp
                    <div class="p-4 space-y-3 bg-white dark:bg-slate-800" :class="isSelected({{ $user->id }}) ? 'bg-orange-50/60 dark:bg-orange-950/30' : ''">
                        <div class="flex items-start gap-3">
                            @if($user->role !== 'admin')
                                <input type="checkbox" value="{{ $user->id }}" x-model.number="selectedUserIds" aria-label="Pilih {{ $user->name }}" class="mt-1 w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500 dark:bg-slate-800 cursor-pointer shrink-0">
                            @endif
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <button type="button" @click="selectedUser = {{ json_encode($mUserData) }}; openDetailModal = true" class="text-left font-bold text-sm text-slate-800 dark:text-slate-100 hover:text-orange-600 truncate cursor-pointer">{{ $user->name }}</button>
                                    <x-status-badge :type="$user->role === 'dosen' ? 'blue' : ($user->role === 'kaprodi' ? 'orange' : 'slate')" :label="strtoupper($user->role)" size="xs" />
                                </div>
                                <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400 mt-0.5">{{ $user->identifier }} {{ $user->role === 'mahasiswa' && $user->entry_year ? '• Angkatan ' . $user->entry_year : '' }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/70">
                            <form action="{{ route('users.toggle', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="cursor-pointer">
                                    @if($user->is_active)
                                        <x-status-badge type="emerald" label="AKTIF" size="xs" />
                                    @else
                                        <x-status-badge type="orange" label="PENDING" size="xs" />
                                    @endif
                                </button>
                            </form>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="selectedUser = {{ json_encode($mUserData) }}; openDetailModal = true" class="p-2 text-slate-500 hover:text-indigo-600 rounded-lg cursor-pointer" title="Detail Pengguna" aria-label="Detail {{ $user->name }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                @if($mUserData['can_impersonate'])
                                    <form action="{{ route('users.impersonate', $user->id) }}"
                                          method="POST"
                                          data-confirm="Anda akan masuk ke sistem sebagai {{ $user->name }} ({{ ucfirst($user->role) }}). Aktivitas Login As dicatat pada log audit sistem. Lanjutkan?"
                                          data-confirm-title="Konfirmasi Login As"
                                          data-confirm-type="warning"
                                          data-confirm-button="Ya, Masuk Sebagai Pengguna">
                                        @csrf
                                        <button type="submit" class="p-2 text-slate-500 hover:text-orange-600 dark:hover:text-orange-400 rounded-lg cursor-pointer" title="Login As ({{ $user->name }})" aria-label="Masuk sebagai {{ $user->name }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('users.edit', $user->id) }}" class="p-2 text-slate-500 hover:text-blue-600 rounded-lg" title="Edit Pengguna" aria-label="Edit {{ $user->name }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus pengguna ini?" data-confirm-title="Hapus Pengguna" data-confirm-type="danger" data-confirm-button="Ya, Hapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 rounded-lg cursor-pointer" title="Hapus Pengguna" aria-label="Hapus {{ $user->name }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Tidak ada data pengguna yang sesuai dengan filter yang dipilih.</p>
                    </div>
                @endforelse
            </div>

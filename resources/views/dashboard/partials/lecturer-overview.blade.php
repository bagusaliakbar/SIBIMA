            <!-- Dosen Quick Actions Bar -->
            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-3.5 sm:p-4 mb-6 shadow-xs flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="p-1.5 rounded-lg bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                    <span class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-200">Aksi Cepat</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('mentoring-sessions.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black rounded-xl transition-all shadow-xs hover:shadow-md hover:scale-102 active:scale-98 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Jadwalkan Bimbingan</span>
                    </a>
                    <a href="{{ route('mentoring-sessions.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-700/80 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Daftar Bimbingan</span>
                        @if(($dosenActionCenter['pending_mentoring_count'] ?? 0) > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[11px] font-black bg-amber-500 text-white leading-none">{{ $dosenActionCenter['pending_mentoring_count'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('seminar-examiner.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-700/80 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Jadwal Menguji Ujian</span>
                        @if((($dosenActionCenter['need_input_count'] ?? 0) + ($dosenActionCenter['need_review_count'] ?? 0)) > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[11px] font-black bg-rose-500 text-white leading-none">{{ ($dosenActionCenter['need_input_count'] ?? 0) + ($dosenActionCenter['need_review_count'] ?? 0) }}</span>
                        @endif
                    </a>
                    <a href="{{ route('theses.index', ['filter' => 'stalled']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-700/80 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Monitoring Bimbingan Pasif</span>
                        @if(($stalledThesesCount ?? $earlyWarning['stalled_count'] ?? 0) > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[11px] font-black bg-rose-600 text-white leading-none">{{ $stalledThesesCount ?? $earlyWarning['stalled_count'] }}</span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Actionable Task Center Dosen (3 Dynamic Cards with Instant Action Modals) -->
            <div x-data="{ showInputModal: false, showReviewModal: false }" class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        Action Center Dosen: Tugas Memerlukan Tindakan
                    </h3>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        {{ ($dosenActionCenter['total_action_needed'] ?? $dosenActionCenter['total_actions'] ?? 0) > 0 ? (($dosenActionCenter['total_action_needed'] ?? $dosenActionCenter['total_actions']) . ' Tindakan Diperlukan') : 'Semua Tugas Selesai' }}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Card 1: Permintaan Bimbingan Menunggu -->
                    <a href="{{ route('mentoring-sessions.index') }}" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border {{ ($dosenActionCenter['pending_mentoring_count'] ?? 0) > 0 ? 'border-amber-300 dark:border-amber-700/80 bg-gradient-to-br from-amber-50/50 via-white to-amber-50/20 dark:from-amber-950/20 dark:via-slate-800 dark:to-slate-800' : 'border-slate-200/80 dark:border-slate-700/80' }} shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl {{ ($dosenActionCenter['pending_mentoring_count'] ?? 0) > 0 ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider {{ ($dosenActionCenter['pending_mentoring_count'] ?? 0) > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200 border border-amber-300 dark:border-amber-700' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                                {{ ($dosenActionCenter['pending_mentoring_count'] ?? 0) > 0 ? 'Perlu Respon' : 'Selesai' }}
                            </span>
                        </div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Permintaan Bimbingan</h4>
                        <div class="flex items-baseline gap-2 mt-1 mb-2">
                            <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $dosenActionCenter['pending_mentoring_count'] ?? 0 }}</span>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Sesi Menunggu</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                            {{ ($dosenActionCenter['pending_mentoring_count'] ?? 0) > 0 ? 'Mahasiswa bimbingan menunggu persetujuan jadwal Anda.' : 'Semua pengajuan sesi bimbingan telah dikonfirmasi.' }}
                        </p>
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-bold text-amber-600 dark:text-amber-400 group-hover:translate-x-0.5 transition-transform">
                            <span>Buka Permintaan Bimbingan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>

                    <!-- Card 2: Catatan & Nilai Ujian Belum Diisi -->
                    @php
                        $inputCount = $dosenActionCenter['need_input_count'] ?? 0;
                        $firstInputUrl = ($inputCount === 1 && !empty($dosenActionCenter['need_input_revisions'][0]['url'])) ? $dosenActionCenter['need_input_revisions'][0]['url'] : null;
                    @endphp
                    @if($inputCount === 1)
                        <a href="{{ $firstInputUrl }}" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border border-rose-300 dark:border-rose-700/80 bg-gradient-to-br from-rose-50/50 via-white to-rose-50/20 dark:from-rose-950/20 dark:via-slate-800 dark:to-slate-800 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer">
                    @elseif($inputCount > 1)
                        <div role="button" tabindex="0" @click="showInputModal = true" @keydown.enter="showInputModal = true" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border border-rose-300 dark:border-rose-700/80 bg-gradient-to-br from-rose-50/50 via-white to-rose-50/20 dark:from-rose-950/20 dark:via-slate-800 dark:to-slate-800 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer">
                    @else
                        <a href="{{ route('seminar-examiner.index') }}" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer">
                    @endif
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl {{ $inputCount > 0 ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider {{ $inputCount > 0 ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200 border border-rose-300 dark:border-rose-700' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                                {{ $inputCount > 0 ? 'Segera Lengkapi' : 'Lengkap' }}
                            </span>
                        </div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Catatan &amp; Nilai Ujian</h4>
                        <div class="flex items-baseline gap-2 mt-1 mb-2">
                            <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $inputCount }}</span>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Ujian Belum Diisi</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                            {{ $inputCount > 0 ? 'Ujian telah terlaksana tapi catatan atau nilai penguji belum disubmit.' : 'Seluruh berita acara ujian & penilaian telah Anda selesaikan.' }}
                        </p>
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-bold text-rose-600 dark:text-rose-400 group-hover:translate-x-0.5 transition-transform">
                            <span>
                                @if($inputCount > 1)
                                    Buka {{ $inputCount }} Lembar Ujian
                                @elseif($inputCount === 1)
                                    Buka Lembar Catatan Ujian
                                @else
                                    Lihat Riwayat Penguji
                                @endif
                            </span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    @if($inputCount > 1)
                        </div>
                    @else
                        </a>
                    @endif

                    <!-- Card 3: Perbaikan Mahasiswa Masuk Butuh Review -->
                    @php
                        $reviewCount = $dosenActionCenter['need_review_count'] ?? 0;
                        $firstReviewUrl = ($reviewCount === 1 && !empty($dosenActionCenter['need_review_revisions'][0]['url'])) ? $dosenActionCenter['need_review_revisions'][0]['url'] : null;
                    @endphp
                    @if($reviewCount === 1)
                        <a href="{{ $firstReviewUrl }}" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border border-indigo-300 dark:border-indigo-700/80 bg-gradient-to-br from-indigo-50/50 via-white to-indigo-50/20 dark:from-indigo-950/20 dark:via-slate-800 dark:to-slate-800 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer">
                    @elseif($reviewCount > 1)
                        <div role="button" tabindex="0" @click="showReviewModal = true" @keydown.enter="showReviewModal = true" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border border-indigo-300 dark:border-indigo-700/80 bg-gradient-to-br from-indigo-50/50 via-white to-indigo-50/20 dark:from-indigo-950/20 dark:via-slate-800 dark:to-slate-800 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer">
                    @else
                        <a href="{{ route('seminar-examiner.index') }}" class="group relative bg-white dark:bg-slate-800/90 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer">
                    @endif
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl {{ $reviewCount > 0 ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider {{ $reviewCount > 0 ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-200 border border-indigo-300 dark:border-indigo-700' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                                {{ $reviewCount > 0 ? 'Perlu ACC' : 'Tuntas' }}
                            </span>
                        </div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Revisi Mahasiswa Masuk</h4>
                        <div class="flex items-baseline gap-2 mt-1 mb-2">
                            <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $reviewCount }}</span>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Berkas Masuk</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                            {{ $reviewCount > 0 ? 'Mahasiswa telah mengunggah revisi naskah dan menunggu persetujuan (ACC).' : 'Tidak ada berkas perbaikan naskah yang menunggu ditinjau.' }}
                        </p>
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs font-bold text-indigo-600 dark:text-indigo-400 group-hover:translate-x-0.5 transition-transform">
                            <span>
                                @if($reviewCount > 1)
                                    Tinjau {{ $reviewCount }} Berkas Revisi
                                @elseif($reviewCount === 1)
                                    Tinjau Perbaikan Naskah
                                @else
                                    Lihat Penguji Seminar
                                @endif
                            </span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    @if($reviewCount > 1)
                        </div>
                    @else
                        </a>
                    @endif
                </div>

                <!-- MODAL 1: DAFTAR UJIAN BUTUH INPUT NILAI & CATATAN -->
                <div x-show="showInputModal" x-cloak 
                     @keydown.escape.window="if (showInputModal) showInputModal = false"
                     class="fixed inset-0 z-[60] overflow-y-auto" 
                     aria-labelledby="modal-title-input" role="dialog" aria-modal="true">
                    <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0">
                        <div x-show="showInputModal" 
                             x-transition:enter="ease-out duration-300" 
                             x-transition:enter-start="opacity-0" 
                             x-transition:enter-end="opacity-100" 
                             x-transition:leave="ease-in duration-200" 
                             x-transition:leave-start="opacity-100" 
                             x-transition:leave-end="opacity-0" 
                             @click="showInputModal = false"
                             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                        <div x-show="showInputModal" 
                             x-transition:enter="ease-out duration-300" 
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                             x-transition:leave="ease-in duration-200" 
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                             class="relative bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-slate-200 dark:border-slate-700">
                            
                            <div class="p-6 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black text-slate-800 dark:text-white" id="modal-title-input">
                                            Catatan &amp; Nilai Ujian Belum Diisi
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            Terdapat {{ $inputCount }} ujian yang memerlukan input catatan revisi atau penilaian Anda.
                                        </p>
                                    </div>
                                </div>
                                <button type="button" @click="showInputModal = false" aria-label="Tutup daftar ujian butuh nilai" class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="p-6 max-h-[60vh] overflow-y-auto space-y-3">
                                @forelse($dosenActionCenter['need_input_revisions'] ?? [] as $item)
                                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 hover:border-rose-300 dark:hover:border-rose-700 bg-slate-50/50 dark:bg-slate-900/30 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded-md text-[11px] font-black uppercase tracking-wider {{ ($item['type'] ?? '') === 'defense' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300' }}">
                                                    {{ $item['category'] ?? 'Ujian' }}
                                                </span>
                                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $item['student_name'] }}</span>
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400">({{ $item['student_identifier'] }})</span>
                                            </div>
                                            <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-1 italic">
                                                {{ $item['thesis_title'] ?? '-' }}
                                            </p>
                                            <div class="flex items-center gap-3 text-[11px] text-slate-500 dark:text-slate-400 pt-0.5">
                                                <span>{{ \Carbon\Carbon::parse($item['date'])->locale('id')->translatedFormat('d M Y') }}</span>
                                                @if(!empty($item['time']))
                                                    <span>• {{ $item['time'] }}</span>
                                                @endif
                                                @if(!empty($item['location']) && $item['location'] !== '-')
                                                    <span>• {{ $item['location'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ $item['url'] }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black shadow-md shadow-rose-600/20 active:scale-95 transition-all shrink-0 cursor-pointer">
                                            <span>Isi Catatan &amp; Nilai</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-slate-500 dark:text-slate-400 text-xs">
                                        Tidak ada catatan atau nilai ujian yang tertunda.
                                    </div>
                                @endforelse
                            </div>

                            <div class="p-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('seminar-examiner.index') }}" class="px-3 py-1.5 text-xs font-bold text-orange-600 hover:underline">Halaman Penguji Seminar</a>
                                    <span class="text-slate-300 dark:text-slate-600">|</span>
                                    <a href="{{ route('defense-examiner.index') }}" class="px-3 py-1.5 text-xs font-bold text-emerald-600 hover:underline">Halaman Penguji Sidang</a>
                                </div>
                                <button type="button" @click="showInputModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL 2: DAFTAR REVISI MAHASISWA MASUK MENUNGGU REVIEW -->
                <div x-show="showReviewModal" x-cloak 
                     @keydown.escape.window="if (showReviewModal) showReviewModal = false"
                     class="fixed inset-0 z-[60] overflow-y-auto" 
                     aria-labelledby="modal-title-review" role="dialog" aria-modal="true">
                    <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0">
                        <div x-show="showReviewModal" 
                             x-transition:enter="ease-out duration-300" 
                             x-transition:enter-start="opacity-0" 
                             x-transition:enter-end="opacity-100" 
                             x-transition:leave="ease-in duration-200" 
                             x-transition:leave-start="opacity-100" 
                             x-transition:leave-end="opacity-0" 
                             @click="showReviewModal = false"
                             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                        <div x-show="showReviewModal" 
                             x-transition:enter="ease-out duration-300" 
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                             x-transition:leave="ease-in duration-200" 
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                             class="relative bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-slate-200 dark:border-slate-700">
                            
                            <div class="p-6 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black text-slate-800 dark:text-white" id="modal-title-review">
                                            Revisi Mahasiswa Masuk Menunggu ACC
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            Terdapat {{ $reviewCount }} berkas perbaikan naskah yang telah diunggah mahasiswa dan siap ditinjau.
                                        </p>
                                    </div>
                                </div>
                                <button type="button" @click="showReviewModal = false" aria-label="Tutup daftar revisi masuk" class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="p-6 max-h-[60vh] overflow-y-auto space-y-3">
                                @forelse($dosenActionCenter['need_review_revisions'] ?? [] as $item)
                                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 hover:border-indigo-300 dark:hover:border-indigo-700 bg-slate-50/50 dark:bg-slate-900/30 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded-md text-[11px] font-black uppercase tracking-wider {{ ($item['type'] ?? '') === 'defense' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300' }}">
                                                    {{ $item['category'] ?? 'Revisi' }}
                                                </span>
                                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $item['student_name'] }}</span>
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400">({{ $item['student_identifier'] }})</span>
                                            </div>
                                            <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-1 italic">
                                                {{ $item['thesis_title'] ?? '-' }}
                                            </p>
                                            <div class="flex items-center gap-3 text-[11px] text-slate-500 dark:text-slate-400 pt-0.5">
                                                <span>Diunggah: {{ \Carbon\Carbon::parse($item['date'])->locale('id')->translatedFormat('d M Y, H:i') }} WIB</span>
                                            </div>
                                        </div>
                                        <a href="{{ $item['url'] }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white rounded-xl text-xs font-black shadow-sm active:scale-95 transition-all shrink-0 cursor-pointer">
                                            <span>Tinjau Perbaikan</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-slate-500 dark:text-slate-400 text-xs">
                                        Tidak ada berkas perbaikan naskah yang menunggu ditinjau.
                                    </div>
                                @endforelse
                            </div>

                            <div class="p-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('seminar-examiner.index') }}" class="px-3 py-1.5 text-xs font-bold text-orange-600 hover:underline">Halaman Penguji Seminar</a>
                                    <span class="text-slate-300 dark:text-slate-600">|</span>
                                    <a href="{{ route('defense-examiner.index') }}" class="px-3 py-1.5 text-xs font-bold text-emerald-600 hover:underline">Halaman Penguji Sidang</a>
                                </div>
                                <button type="button" @click="showReviewModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Wave Info Widget for Dosen (if active) -->
            @if(isset($activeWaveInfo) && $activeWaveInfo)
            <div class="bg-gradient-to-r from-orange-500/10 via-amber-500/10 to-orange-500/5 dark:from-orange-950/40 dark:via-amber-950/30 dark:to-slate-800/60 border border-orange-200/80 dark:border-orange-800/60 rounded-2xl p-4 sm:p-5 mb-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-orange-600/30 font-black text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-black uppercase tracking-wider bg-orange-600 text-white">Gelombang Aktif</span>
                            <h4 class="text-sm font-black text-slate-800 dark:text-white">{{ $activeWaveInfo['name'] }}</h4>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">({{ $activeWaveInfo['academic_year'] }} - {{ $activeWaveInfo['semester'] }})</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 font-medium mt-1">
                            Periode Pendaftaran: <strong>{{ \Carbon\Carbon::parse($activeWaveInfo['start_date'])->translatedFormat('d M Y') }}</strong> s.d. <strong>{{ \Carbon\Carbon::parse($activeWaveInfo['end_date'])->translatedFormat('d M Y') }}</strong>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 bg-white/80 dark:bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        <span>Pendaftar:</span>
                        <span class="text-orange-600 font-black">{{ $activeWaveInfo['seminar_applicant_count'] }} Sem</span>
                        <span class="text-slate-300 dark:text-slate-600">|</span>
                        <span class="text-emerald-600 font-black">{{ $activeWaveInfo['defense_applicant_count'] }} Sid</span>
                    </div>
                    <span class="px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider {{ $activeWaveInfo['days_remaining'] <= 3 ? 'bg-rose-600 text-white shadow-rose-600/20' : 'bg-amber-500 text-white shadow-amber-500/20' }} shadow-md">
                        {{ $activeWaveInfo['days_remaining'] > 0 ? ('Sisa ' . $activeWaveInfo['days_remaining'] . ' Hari') : 'Hari Terakhir!' }}
                    </span>
                </div>
            </div>
            @endif

            <!-- Dosen Stats Cards (4 Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <x-stat-card title="Mhs Bimbingan" :value="$activeThesesCount ?? 0" color="orange">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </x-slot>
                    <x-slot name="subtitle">
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                            <span>Pembimbing 1</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $totalActiveStudentsP1 ?? 0 }} Mahasiswa</span>
                        </p>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                            <span>Pembimbing 2</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $totalActiveStudentsP2 ?? 0 }} Mahasiswa</span>
                        </p>
                    </x-slot>
                </x-stat-card>

                <x-stat-card title="Jadwal Minggu Ini" color="blue">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </x-slot>
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ $sessionsThisWeek ?? 0 }} <span class="text-xs font-medium text-slate-400 lowercase tracking-normal">Sesi</span></h2>
                    <x-slot name="subtitle">
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                            <span>Menunggu</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400">{{ $pendingSessionsThisWeek ?? 0 }}</span>
                        </p>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                            <span>Disetujui</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $approvedSessionsThisWeek ?? 0 }}</span>
                        </p>
                    </x-slot>
                </x-stat-card>

                <x-stat-card title="Sesi Selesai" :value="$totalCompletedSessions ?? 0" color="emerald">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-slot>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 italic tracking-tight">Total seluruh bimbingan</p>
                </x-stat-card>

                <x-stat-card title="Progres Rata-rata" :value="($averageStudentProgress ?? 0) . '%'" color="pink">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </x-slot>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 italic tracking-tight">Performa bimbingan global</p>
                </x-stat-card>
            </div>



            <!-- Dosen Charts: Distribusi Progres & Tren Aktivitas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Chart 1: Distribution -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                            Distribusi Progres Bimbingan
                        </h3>
                        <div class="h-64">
                            <canvas id="distributionChart"></canvas>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/50 space-y-2">
                            <p class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Keterangan Progres:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                <div class="flex items-start gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mt-0.5 flex-shrink-0"></span>
                                    <span><strong>Judul:</strong> Mahasiswa aktif yang belum memulai bimbingan.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mt-0.5 flex-shrink-0"></span>
                                    <span><strong>Bimbingan:</strong> Mahasiswa aktif yang sedang dalam proses bimbingan reguler.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-0.5 flex-shrink-0"></span>
                                    <span><strong>ACC Seminar:</strong> Sudah di-ACC Seminar UP oleh kedua pembimbing.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 mt-0.5 flex-shrink-0"></span>
                                    <span><strong>ACC Sidang:</strong> Sudah di-ACC Sidang Akhir oleh kedua pembimbing.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Trends -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 dark:bg-blue-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            Tren Aktivitas Bimbingan
                        </h3>
                        <div class="h-64">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

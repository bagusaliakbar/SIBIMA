<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Pendaftaran Sidang Skripsi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        @if($isEligible)
            <!-- Physical Documents Notice -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-md p-6 mb-6">
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg mr-4 shadow-sm">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-blue-800 dark:text-blue-200 uppercase tracking-widest mb-3">Penting: Berkas Fisik (Penyerahan ke BAAK)</h3>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mb-4 font-medium">Selain mengunggah berkas digital di bawah ini, Anda wajib mengumpulkan berkas fisik berikut langsung ke kantor BAAK:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Dokumen Skripsi:</strong> Cetak rangkap 3 (Khusus jika pelaksanaan Sidang dilakukan secara Offline).</span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Formulir Pendaftaran Sidang:</strong> Wajib sudah ditandatangani Dosen Pembimbing dan dibubuhi materai.</span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span>
                                    <strong>Pas Photo Hitam Putih:</strong> Terbaru, dicetak di kertas DOP.<br>
                                    <span class="inline-block mt-1 font-bold bg-blue-100 dark:bg-blue-800 px-2 py-0.5 rounded">2x3 (5 lembar)</span>
                                    <span class="inline-block mt-1 font-bold bg-blue-100 dark:bg-blue-800 px-2 py-0.5 rounded">3x4 (10 lembar)</span>
                                    <span class="inline-block mt-1 font-bold bg-blue-100 dark:bg-blue-800 px-2 py-0.5 rounded">4x6 (10 lembar)</span>
                                    <p class="mt-2 opacity-80 italic text-[10px]">Ketentuan: Pria & Wanita memakai baju putih hitam, berdasi, berjas hitam. Bagi wanita berhijab menggunakan kerudung warna hitam.</p>
                                </span>
                            </li>
                            <li class="flex items-start text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500 mt-1 mr-3 flex-shrink-0"></span>
                                <span><strong>Map:</strong> Map Plastik warna Biru (2 buah) dan Map Kertas (2 buah).</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        @if(!$isEligible)
            @php
                $p1 = $thesis?->pembimbing1;
                $p2 = $thesis?->pembimbing2;
                $p1Sessions = $thesis ? $thesis->mentoringSessions()->where('dosen_id', $thesis->pembimbing1_id)->where('status', 'completed')->where('is_absent', false)->count() : 0;
                $p2Sessions = $thesis && $thesis->pembimbing2_id ? $thesis->mentoringSessions()->where('dosen_id', $thesis->pembimbing2_id)->where('status', 'completed')->where('is_absent', false)->count() : 0;
                $totalSessions = $p1Sessions + $p2Sessions;
                $accSidangP1 = (bool) ($thesis?->acc_sidang_p1);
                $accSidangP2 = (bool) ($thesis?->acc_sidang_p2);
                $isPassedSeminar = $thesis && ($thesis->isAccUpFinal() || \App\Models\SeminarScheduleDetail::where('thesis_id', $thesis->id)->exists());
            @endphp

            <div class="space-y-6">
                <!-- Main Status & Hero Guidance Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8">
                    <div class="mb-6 space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">
                                Progres Menuju Pendaftaran Sidang Skripsi
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50">
                                Belum Memenuhi Syarat
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                            Formulir pendaftaran sidang skripsi akan otomatis aktif setelah Anda memperoleh persetujuan (<strong class="text-slate-700 dark:text-slate-200 font-bold">ACC Sidang Skripsi</strong>) dari <strong class="text-slate-700 dark:text-slate-200 font-bold">kedua dosen pembimbing</strong>.
                        </p>
                    </div>

                    <!-- Dual Supervisor Status Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100 dark:border-slate-700/80 mb-6">
                        <!-- Pembimbing 1 -->
                        <div class="p-4 rounded-xl border transition-all {{ $accSidangP1 ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/50' : 'bg-slate-50/70 dark:bg-slate-900/50 border-slate-200/80 dark:border-slate-700/80' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/40 flex items-center justify-center text-xs font-black shrink-0">
                                        P1
                                    </span>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Dosen Pembimbing 1</p>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                            {{ $p1 ? $p1->name : 'Belum Ditugaskan' }}
                                        </h4>
                                    </div>
                                </div>
                                <div>
                                    @if($accSidangP1)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500 text-white shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Sudah ACC</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Belum ACC</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span>Bimbingan Selesai:</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $p1Sessions }} Sesi</span>
                            </div>
                        </div>

                        <!-- Pembimbing 2 -->
                        <div class="p-4 rounded-xl border transition-all {{ $accSidangP2 ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/50' : 'bg-slate-50/70 dark:bg-slate-900/50 border-slate-200/80 dark:border-slate-700/80' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/40 flex items-center justify-center text-xs font-black shrink-0">
                                        P2
                                    </span>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Dosen Pembimbing 2</p>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                            {{ $p2 ? $p2->name : ($thesis?->pembimbing2_id ? '-' : 'Belum Ditugaskan') }}
                                        </h4>
                                    </div>
                                </div>
                                <div>
                                    @if($accSidangP2)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500 text-white shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Sudah ACC</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Belum ACC</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span>Bimbingan Selesai:</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $p2Sessions }} Sesi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Checklist Prasyarat & Action CTA -->
                    <div class="bg-slate-50/70 dark:bg-slate-900/40 rounded-xl p-5 border border-slate-200/70 dark:border-slate-700/60">
                        <h4 class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Checklist Kesiapan Sidang Skripsi
                        </h4>

                        <div class="space-y-3">
                            <!-- Item 1: Lulus Seminar Proposal -->
                            <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-200/40 dark:border-slate-800">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full {{ $isPassedSeminar ? 'bg-emerald-500 text-white' : 'bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }} flex items-center justify-center shrink-0">
                                        @if($isPassedSeminar)
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <span class="text-[9px] font-black">1</span>
                                        @endif
                                    </span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Tahap Seminar Proposal Selesai</span>
                                </div>
                                <span class="text-[11px] font-bold {{ $isPassedSeminar ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                    {{ $isPassedSeminar ? 'Terpenuhi' : 'Belum Selesai' }}
                                </span>
                            </div>

                            <!-- Item 2: Sesi Bimbingan Minimal -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs py-1.5 border-b border-slate-200/40 dark:border-slate-800">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full {{ $totalSessions >= 8 ? 'bg-emerald-500 text-white' : 'bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }} flex items-center justify-center shrink-0">
                                        @if($totalSessions >= 8)
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <span class="text-[9px] font-black">2</span>
                                        @endif
                                    </span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Total Bimbingan Skripsi</span>
                                </div>
                                <div class="flex items-center gap-2 pl-7 sm:pl-0">
                                    <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">{{ $totalSessions }} Sesi Terlaksana</span>
                                </div>
                            </div>

                            <!-- Item 3: ACC Sidang Pembimbing 1 -->
                            <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-200/40 dark:border-slate-800">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full {{ $accSidangP1 ? 'bg-emerald-500 text-white' : 'bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }} flex items-center justify-center shrink-0">
                                        @if($accSidangP1)
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <span class="text-[9px] font-black">3</span>
                                        @endif
                                    </span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">ACC Sidang dari Pembimbing 1</span>
                                </div>
                                <span class="text-[11px] font-bold {{ $accSidangP1 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                    {{ $accSidangP1 ? 'Disetujui' : 'Menunggu Dosen' }}
                                </span>
                            </div>

                            <!-- Item 4: ACC Sidang Pembimbing 2 -->
                            <div class="flex items-center justify-between text-xs py-1.5">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full {{ $accSidangP2 ? 'bg-emerald-500 text-white' : 'bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }} flex items-center justify-center shrink-0">
                                        @if($accSidangP2)
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <span class="text-[9px] font-black">4</span>
                                        @endif
                                    </span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">ACC Sidang dari Pembimbing 2</span>
                                </div>
                                <span class="text-[11px] font-bold {{ $accSidangP2 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                    {{ $accSidangP2 ? 'Disetujui' : 'Menunggu Dosen' }}
                                </span>
                            </div>
                        </div>

                        <!-- CTA Action Buttons -->
                        <div class="mt-5 pt-4 border-t border-slate-200/60 dark:border-slate-700/60 flex flex-wrap items-center justify-between gap-3">
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Segera koordinasikan dan selesaikan bimbingan naskah akhir dengan dosen pembimbing Anda.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('mentoring-sessions.index') }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm shadow-orange-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>Buka Jadwal Bimbingan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($application)
            <!-- Status Application -->
            <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 {{ $application->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-900/10' : ($application->status === 'rejected' ? 'bg-red-50 dark:bg-red-900/10' : 'bg-amber-50 dark:bg-amber-900/10') }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold {{ $application->status === 'approved' ? 'text-emerald-800 dark:text-emerald-400' : ($application->status === 'rejected' ? 'text-red-800 dark:text-red-400' : 'text-amber-800 dark:text-amber-400') }}">
                                Status Pengajuan Sidang: {{ strtoupper($application->status) }}
                            </h3>
                            <p class="text-sm {{ $application->status === 'approved' ? 'text-emerald-600 dark:text-emerald-500/70' : ($application->status === 'rejected' ? 'text-red-600 dark:text-red-500/70' : 'text-amber-600 dark:text-amber-500/70') }} mt-1">
                                Diajukan pada {{ $application->created_at->locale('id')->translatedFormat('d M Y • H:i') }}
                            </p>
                        </div>
                        <div class="px-4 py-2 rounded-full font-bold text-xs uppercase tracking-widest border {{ $application->status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : ($application->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800') }}">
                            {{ $application->status }}
                        </div>
                    </div>
                </div>
                
                @if($application->admin_feedback)
                    <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                        <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Catatan Admin:</h4>
                        <p class="text-sm text-slate-700 dark:text-slate-300 italic">"{{ $application->admin_feedback }}"</p>
                    </div>
                @endif
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-700/80">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Detail Berkas Persyaratan Sidang</h4>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800/60">
                                    20 Berkas Terlampir
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Seluruh berkas persyaratan sidang skripsi yang telah Anda ajukan untuk diverifikasi oleh BAAK / Kaprodi.</p>
                        </div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-700/70 text-slate-500 dark:text-slate-400 text-xs">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="font-medium text-[11px]">Tersimpan di Google Drive</span>
                        </div>
                    </div>

                    @php
                        $fileFields = [
                            'file_formulir' => [
                                'label' => 'Formulir Pendaftaran Sidang',
                                'subtitle' => 'Scan Formulir Pendaftaran Resmi',
                                'category' => 'Administrasi',
                                'theme' => ['bg' => 'bg-indigo-50 dark:bg-indigo-950/40', 'text' => 'text-indigo-600 dark:text-indigo-400', 'border' => 'border-indigo-100 dark:border-indigo-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 13l1.5 1.5 3-3" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 17h8" />'
                            ],
                            'file_transkrip' => [
                                'label' => 'Scan Transkrip Nilai Akhir',
                                'subtitle' => 'Transkrip Nilai Sem. 1 s/d Akhir',
                                'category' => 'Akademik',
                                'theme' => ['bg' => 'bg-amber-50 dark:bg-amber-950/40', 'text' => 'text-amber-600 dark:text-amber-400', 'border' => 'border-amber-100 dark:border-amber-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 17v-3M12 17v-6M16 17v-2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 11h2" />'
                            ],
                            'file_acc_pembimbing' => [
                                'label' => 'Scan Bukti ACC Pembimbing',
                                'subtitle' => 'Persetujuan Sidang P1 & P2',
                                'category' => 'Persetujuan',
                                'theme' => ['bg' => 'bg-amber-50 dark:bg-amber-950/40', 'text' => 'text-amber-600 dark:text-amber-400', 'border' => 'border-amber-100 dark:border-amber-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><circle cx="12" cy="14.5" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.8 14.5l1 1 1.7-1.7" />'
                            ],
                            'file_logbook' => [
                                'label' => 'Scan Kartu Bimbingan / Logbook',
                                'subtitle' => 'Kartu Kendali Logbook Validasi',
                                'category' => 'Bimbingan',
                                'theme' => ['bg' => 'bg-sky-50 dark:bg-sky-950/40', 'text' => 'text-sky-600 dark:text-sky-400', 'border' => 'border-sky-100 dark:border-sky-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M11 12h5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 15h.01M11 15h5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 18h.01M11 18h3" />'
                            ],
                            'file_pembayaran' => [
                                'label' => 'Scan Bukti Pembayaran Sidang',
                                'subtitle' => 'Kwitansi / Slip Bukti Bayar',
                                'category' => 'Keuangan',
                                'theme' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/40', 'text' => 'text-emerald-600 dark:text-emerald-400', 'border' => 'border-emerald-100 dark:border-emerald-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><rect x="7" y="11" width="10" height="7" rx="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 14h10m-7 2h2" />'
                            ],
                            'file_skripsi' => [
                                'label' => 'Dokumen Soft File Skripsi',
                                'subtitle' => 'Draft Lengkap Naskah Skripsi',
                                'category' => 'Naskah Skripsi',
                                'theme' => ['bg' => 'bg-purple-50 dark:bg-purple-950/40', 'text' => 'text-purple-600 dark:text-purple-400', 'border' => 'border-purple-100 dark:border-purple-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11.5l-4.5 2 4.5 2 4.5-2-4.5-2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.5 14.8v1.8c0 .7 1.1 1.2 2.5 1.2s2.5-.5 2.5-1.2v-1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 13.5v2" />'
                            ],
                            'file_ktm' => [
                                'label' => 'Scan Kartu Tanda Mahasiswa (KTM)',
                                'subtitle' => 'Identitas Resmi Mahasiswa',
                                'category' => 'Identitas',
                                'theme' => ['bg' => 'bg-blue-50 dark:bg-blue-950/40', 'text' => 'text-blue-600 dark:text-blue-400', 'border' => 'border-blue-100 dark:border-blue-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><circle cx="12" cy="12.5" r="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 18c0-1.8 1.8-3 4-3s4 1.2 4 3" />'
                            ],
                            'file_ijazah' => [
                                'label' => 'Scan Ijazah SMA/SMK',
                                'subtitle' => 'Ijazah Pendidikan Terakhir',
                                'category' => 'Identitas',
                                'theme' => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-300', 'border' => 'border-slate-200 dark:border-slate-700'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><circle cx="12" cy="13" r="2.8" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 15.5l-1 3.5 3-1.5 3 1.5-1-3.5" />'
                            ],
                            'file_pkkmb_univ' => [
                                'label' => 'Scan Sertifikat PKKMB Univ',
                                'subtitle' => 'Pengenalan Kampus Universitas',
                                'category' => 'Sertifikasi',
                                'theme' => ['bg' => 'bg-teal-50 dark:bg-teal-950/40', 'text' => 'text-teal-600 dark:text-teal-400', 'border' => 'border-teal-100 dark:border-teal-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><circle cx="12" cy="13" r="2.8" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.2 15.5l-1.2 3.5 3-1.5 3 1.5-1.2-3.5" />'
                            ],
                            'file_pkkmb_fak' => [
                                'label' => 'Scan Sertifikat PKKMB Fak',
                                'subtitle' => 'Pengenalan Kampus Fakultas',
                                'category' => 'Sertifikasi',
                                'theme' => ['bg' => 'bg-teal-50 dark:bg-teal-950/40', 'text' => 'text-teal-600 dark:text-teal-400', 'border' => 'border-teal-100 dark:border-teal-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><circle cx="12" cy="13" r="2.8" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11.5v3M10.5 13h3" />'
                            ],
                            'file_makrab' => [
                                'label' => 'Scan Sertifikat Makrab',
                                'subtitle' => 'Malam Keakraban / LKMM',
                                'category' => 'Kemahasiswaan',
                                'theme' => ['bg' => 'bg-orange-50 dark:bg-orange-950/40', 'text' => 'text-orange-600 dark:text-orange-400', 'border' => 'border-orange-100 dark:border-orange-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><circle cx="12" cy="14" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 12l.5 1.2 1.3.2-1 1 .2 1.3-1-.6-1 .6.2-1.3-1-1 1.3-.2z" />'
                            ],
                            'file_cisco' => [
                                'label' => 'Scan Sertifikat Cisco IPv6',
                                'subtitle' => 'Pelatihan Jaringan Komputer',
                                'category' => 'Kompetensi IT',
                                'theme' => ['bg' => 'bg-cyan-50 dark:bg-cyan-950/40', 'text' => 'text-cyan-600 dark:text-cyan-400', 'border' => 'border-cyan-100 dark:border-cyan-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><circle cx="12" cy="14" r="3.5" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.5 14h7M12 10.5a5 5 0 010 7M12 10.5a5 5 0 000 7" />'
                            ],
                            'file_workshop' => [
                                'label' => 'Scan Sertifikat Workshop',
                                'subtitle' => 'Seminar / Workshop Keilmuan',
                                'category' => 'Seminar',
                                'theme' => ['bg' => 'bg-violet-50 dark:bg-violet-950/40', 'text' => 'text-violet-600 dark:text-violet-400', 'border' => 'border-violet-100 dark:border-violet-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><rect x="7.5" y="11" width="9" height="6" rx="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 17v2m-2.5 0h5" />'
                            ],
                            'file_organisasi' => [
                                'label' => 'Scan Sertifikat Organisasi',
                                'subtitle' => 'Surat Keterangan Aktif Ormawa',
                                'category' => 'Organisasi',
                                'theme' => ['bg' => 'bg-rose-50 dark:bg-rose-950/40', 'text' => 'text-rose-600 dark:text-rose-400', 'border' => 'border-rose-100 dark:border-rose-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 11h7l-1.5 2.5 1.5 2.5H8v3" />'
                            ],
                            'file_toefl' => [
                                'label' => 'Scan Sertifikat TOEFL',
                                'subtitle' => 'Uji Kemampuan Bahasa Inggris',
                                'category' => 'Bahasa',
                                'theme' => ['bg' => 'bg-indigo-50 dark:bg-indigo-950/40', 'text' => 'text-indigo-600 dark:text-indigo-400', 'border' => 'border-indigo-100 dark:border-indigo-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.5 18l3.5-7 3.5 7M9.5 15.5h5" />'
                            ],
                            'file_kewirausahaan' => [
                                'label' => 'Scan Sertifikat Wirausaha',
                                'subtitle' => 'Pelatihan / Program Kewirausahaan',
                                'category' => 'Kewirausahaan',
                                'theme' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/40', 'text' => 'text-emerald-600 dark:text-emerald-400', 'border' => 'border-emerald-100 dark:border-emerald-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 16l3-3 2 2 3.5-4m-2 0h2v2" />'
                            ],
                            'file_tahsin' => [
                                'label' => 'Scan Sertifikat Tahsin',
                                'subtitle' => 'Kelulusan Uji Baca Al-Qur\'an',
                                'category' => 'Keagamaan',
                                'theme' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/40', 'text' => 'text-emerald-600 dark:text-emerald-400', 'border' => 'border-emerald-100 dark:border-emerald-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 11.5a3 3 0 002.5 4.5 3.3 3.3 0 11-2.5-4.5z" /><circle cx="15.5" cy="12" r=".6" fill="currentColor" />'
                            ],
                            'file_komputer' => [
                                'label' => 'Scan Sertifikat Komputer',
                                'subtitle' => 'Sertifikasi Keahlian Komputer',
                                'category' => 'Kompetensi IT',
                                'theme' => ['bg' => 'bg-blue-50 dark:bg-blue-950/40', 'text' => 'text-blue-600 dark:text-blue-400', 'border' => 'border-blue-100 dark:border-blue-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><rect x="8" y="11.5" width="8" height="5.5" rx="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 17v1.5m4-1.5v1.5M6.5 13.5h1.5m8 0h1.5" />'
                            ],
                            'file_perpus_pinjam' => [
                                'label' => 'Scan Bebas Pinjam Perpus',
                                'subtitle' => 'Surat Bebas Pustaka Universitas',
                                'category' => 'Perpustakaan',
                                'theme' => ['bg' => 'bg-amber-50 dark:bg-amber-950/40', 'text' => 'text-amber-600 dark:text-amber-400', 'border' => 'border-amber-100 dark:border-amber-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h4m-4 3h3" /><circle cx="14.5" cy="14.5" r="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.5 14.5l.7.7 1.3-1.3" />'
                            ],
                            'file_perpus_sumbang' => [
                                'label' => 'Scan Sumbang Buku Perpus',
                                'subtitle' => 'Tanda Terima Sumbangan Buku',
                                'category' => 'Perpustakaan',
                                'theme' => ['bg' => 'bg-rose-50 dark:bg-rose-950/40', 'text' => 'text-rose-600 dark:text-rose-400', 'border' => 'border-rose-100 dark:border-rose-800/50'],
                                'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h8M8 15h5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.5 15.5l1.5-1.5 1.5 1.5-1.5 1.5z" />'
                            ],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($fileFields as $field => $info)
                            @php
                                $review = $application->file_reviews[$field] ?? null;
                                $isRejected = ($review['status'] ?? '') === 'rejected';
                                $isApproved = ($review['status'] ?? '') === 'approved';
                                $hasFile = !empty($application->$field);
                            @endphp
                            <div class="group relative flex flex-col justify-between p-4 rounded-2xl border transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 {{ $isRejected ? 'border-rose-200 bg-rose-50/40 dark:border-rose-900/60 dark:bg-rose-950/20' : 'border-slate-200/80 dark:border-slate-700/80 bg-white dark:bg-slate-800/90 hover:border-slate-300 dark:hover:border-slate-600' }}">
                                <div>
                                    <!-- Top row: Icon Badge + Category + Status Pill -->
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border {{ $info['theme']['bg'] }} {{ $info['theme']['text'] }} {{ $info['theme']['border'] }} shadow-2xs transition-transform group-hover:scale-105">
                                                <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    {!! $info['svg'] !!}
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                                    {{ $info['category'] }}
                                                </span>
                                                <h5 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors leading-snug truncate" title="{{ $info['label'] }}">
                                                    {{ $info['label'] }}
                                                </h5>
                                            </div>
                                        </div>

                                        <!-- Status Pill -->
                                        <div class="shrink-0">
                                            @if($isApproved)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Valid</span>
                                                </span>
                                            @elseif($isRejected)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                    <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    <span>Revisi</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-400 border border-slate-200/60 dark:border-slate-600/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                    <span>Diproses</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mb-3 line-clamp-1">
                                        {{ $info['subtitle'] }}
                                    </p>

                                    <!-- Rejection Note if any -->
                                    @if($isRejected && !empty($review['note']))
                                        <div class="mb-3 p-2.5 bg-rose-100/70 dark:bg-rose-900/40 rounded-xl border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-300">
                                            <div class="flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider">
                                                <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span>Catatan Validator:</span>
                                            </div>
                                            <p class="text-xs italic mt-0.5 leading-relaxed">"{{ $review['note'] }}"</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Footer: Action link to Google Drive -->
                                <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2 mt-auto">
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        <span>Google Drive</span>
                                    </span>

                                    @if($hasFile)
                                        <a href="{{ $application->$field }}" target="_blank" rel="noopener noreferrer" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-orange-600 dark:text-orange-400 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/40 dark:hover:bg-orange-900/50 border border-orange-200/80 dark:border-orange-800/50 transition-all shadow-2xs group-hover:shadow-xs cursor-pointer">
                                            <span>Buka Dokumen</span>
                                            <svg class="w-3.5 h-3.5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Belum diunggah</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($application->status === 'rejected')
                        <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-3 mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/50">
                                <div class="p-2 bg-amber-100 dark:bg-amber-800 rounded-lg">
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-xs text-amber-700 dark:text-amber-300 font-bold">Silakan unggah ulang berkas yang ditolak (<span class="text-rose-600">REJECT</span>) di bawah ini:</p>
                            </div>

                            <form action="{{ route('thesis-defense-applications.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    @foreach($fileFields as $field => $info)
                                        @if(isset($application->file_reviews[$field]['status']) && $application->file_reviews[$field]['status'] === 'rejected')
                                            <div class="space-y-2">
                                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ $info['label'] }} <span class="text-red-500">*</span></label>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold tracking-wide mb-2">
                                                    Masukkan link dari Google Drive.
                                                </p>
                                                <input type="url" name="{{ $field }}" value="{{ old($field, $application->$field ?? '') }}" placeholder="https://drive.google.com/..." required class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900 shadow-sm py-2 px-3">
                                                @error($field) <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="mt-8 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-orange-600 text-white font-bold rounded-md hover:bg-orange-700 transition-colors shadow-md">
                                        Perbarui Pengajuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Upload Form -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden" 
                 x-data="{
                    activeTab: 'all',
                    batchFolderUrl: '',
                    files: {
                        file_formulir: '{{ old('file_formulir', $application->file_formulir ?? '') }}',
                        file_pembayaran: '{{ old('file_pembayaran', $application->file_pembayaran ?? '') }}',
                        file_ktm: '{{ old('file_ktm', $application->file_ktm ?? '') }}',
                        file_ijazah: '{{ old('file_ijazah', $application->file_ijazah ?? '') }}',
                        file_transkrip: '{{ old('file_transkrip', $application->file_transkrip ?? '') }}',
                        file_acc_pembimbing: '{{ old('file_acc_pembimbing', $application->file_acc_pembimbing ?? '') }}',
                        file_logbook: '{{ old('file_logbook', $application->file_logbook ?? '') }}',
                        file_skripsi: '{{ old('file_skripsi', $application->file_skripsi ?? '') }}',
                        file_pkkmb_univ: '{{ old('file_pkkmb_univ', $application->file_pkkmb_univ ?? '') }}',
                        file_pkkmb_fak: '{{ old('file_pkkmb_fak', $application->file_pkkmb_fak ?? '') }}',
                        file_makrab: '{{ old('file_makrab', $application->file_makrab ?? '') }}',
                        file_cisco: '{{ old('file_cisco', $application->file_cisco ?? '') }}',
                        file_workshop: '{{ old('file_workshop', $application->file_workshop ?? '') }}',
                        file_organisasi: '{{ old('file_organisasi', $application->file_organisasi ?? '') }}',
                        file_toefl: '{{ old('file_toefl', $application->file_toefl ?? '') }}',
                        file_kewirausahaan: '{{ old('file_kewirausahaan', $application->file_kewirausahaan ?? '') }}',
                        file_tahsin: '{{ old('file_tahsin', $application->file_tahsin ?? '') }}',
                        file_komputer: '{{ old('file_komputer', $application->file_komputer ?? '') }}',
                        file_perpus_pinjam: '{{ old('file_perpus_pinjam', $application->file_perpus_pinjam ?? '') }}',
                        file_perpus_sumbang: '{{ old('file_perpus_sumbang', $application->file_perpus_sumbang ?? '') }}'
                    },
                    applyBatchFolderUrl() {
                        if (!this.batchFolderUrl || !this.batchFolderUrl.trim().startsWith('http')) {
                            alert('Silakan masukkan link Google Drive yang valid (dimulai dengan https://)');
                            return;
                        }
                        const url = this.batchFolderUrl.trim();
                        Object.keys(this.files).forEach(k => {
                            this.files[k] = url;
                        });
                    },
                    async pasteSingle(field) {
                        try {
                            const text = await navigator.clipboard.readText();
                            if (text) {
                                this.files[field] = text.trim();
                            }
                        } catch (e) {
                            alert('Gunakan tombol pintasan Ctrl + V untuk menempelkan link.');
                        }
                    },
                    isDrive(url) {
                        return url && url.includes('drive.google.com');
                    },
                    countFilled(keys) {
                        return keys.filter(k => this.files[k] && this.files[k].trim() !== '').length;
                    },
                    get totalFilled() {
                        return Object.values(this.files).filter(v => v && v.trim() !== '').length;
                    },
                    get totalCount() {
                        return Object.keys(this.files).length;
                    },
                    get percentFilled() {
                        return Math.round((this.totalFilled / this.totalCount) * 100);
                    }
                 }">
                
                <!-- Header Card -->
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">Form Pengajuan Sidang Skripsi</h3>
                                <span class="px-2.5 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 text-[10px] font-black rounded-lg uppercase tracking-widest border border-indigo-200 dark:border-indigo-800/50">
                                    {{ $activeWave->name ?? 'Gelombang Aktif' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Lengkapi 20 berkas persyaratan di bawah ini dengan tautan Google Drive yang dapat diakses publik.</p>
                        </div>
                        
                        @if($template)
                            <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xs">
                                <div class="p-2 bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Unduh Formulir</div>
                                    <a href="{{ route('download.private', ['path' => $template->file_path]) }}" target="_blank" class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline">
                                        {{ $template->title }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <form action="{{ route('thesis-defense-applications.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- 1. Smart Batch Folder Filler Banner -->
                    <div class="p-4 bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 dark:from-slate-900 dark:via-slate-800/80 dark:to-slate-900 border border-orange-200/80 dark:border-slate-700 rounded-2xl shadow-2xs">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                            <div class="flex items-start sm:items-center gap-3">
                                <div class="p-2.5 bg-orange-600 text-white rounded-xl shadow-xs shrink-0 mt-0.5 sm:mt-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">💡 Punya 1 Folder Google Drive untuk Semua Berkas?</h4>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">Jika seluruh scan berkas Anda berada di dalam satu folder Google Drive, tempelkan link foldernya di sini untuk mengisi 20 kolom sekaligus secara instan.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 w-full lg:w-auto">
                                <input type="url" 
                                       x-model="batchFolderUrl" 
                                       placeholder="https://drive.google.com/drive/folders/..." 
                                       class="py-2 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 w-full lg:w-72 shadow-2xs">
                                <button type="button" 
                                        @click="applyBatchFolderUrl()" 
                                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-xs shrink-0 active:scale-95 cursor-pointer">
                                    Terapkan ke Semua
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Real-Time Progress Bar -->
                    <div class="p-4 bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                <span>📊 Status Kelengkapan Berkas:</span>
                                <span class="font-black text-orange-600 dark:text-orange-400" x-text="totalFilled + ' dari ' + totalCount + ' Berkas Terisi'"></span>
                            </span>
                            <span class="text-xs font-black" :class="totalFilled === totalCount ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400'" x-text="percentFilled + '%'"></span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-500 rounded-full" 
                                 :class="totalFilled === totalCount ? 'bg-emerald-500' : 'bg-gradient-to-r from-orange-500 to-amber-500'" 
                                 :style="'width: ' + percentFilled + '%'"></div>
                        </div>
                    </div>

                    @php
                        $categories = [
                            'utama' => [
                                'title' => 'Berkas Utama & Identitas',
                                'icon' => 'document',
                                'keys' => ['file_formulir', 'file_pembayaran', 'file_ktm', 'file_ijazah'],
                                'fields' => [
                                    'file_formulir' => ['label' => '1. Formulir Pendaftaran Sidang', 'desc' => 'Scan formulir pendaftaran yang telah diisi & ditandatangani.'],
                                    'file_pembayaran' => ['label' => '5. Scan Bukti Pembayaran Sidang', 'desc' => 'Kwitansi / bukti transfer pembayaran pendaftaran sidang.'],
                                    'file_ktm' => ['label' => '7. Scan Kartu Tanda Mahasiswa (KTM)', 'desc' => 'Scan KTM asli yang masih berlaku.'],
                                    'file_ijazah' => ['label' => '20. Scan Ijazah SMA/SMK', 'desc' => 'Scan legalisir / asli ijazah pendidikan terakhir.'],
                                ]
                            ],
                            'skripsi' => [
                                'title' => 'Skripsi & Akademik',
                                'icon' => 'academic',
                                'keys' => ['file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_skripsi'],
                                'fields' => [
                                    'file_transkrip' => ['label' => '2. Scan Transkrip Nilai Akhir', 'desc' => 'Transkrip nilai akademik lengkap semester 1 s/d akhir.'],
                                    'file_acc_pembimbing' => ['label' => '3. Scan Bukti ACC Pembimbing', 'desc' => 'Lembar persetujuan sidang dari Pembimbing 1 & Pembimbing 2.'],
                                    'file_logbook' => ['label' => '4. Scan Kartu Bimbingan / Logbook', 'desc' => 'Kartu kendali bimbingan / lembar logbook yang telah divalidasi.'],
                                    'file_skripsi' => ['label' => '6. Dokumen Soft File Skripsi', 'desc' => 'Draft lengkap naskah skripsi (Cover hingga Lampiran).'],
                                ]
                            ],
                            'sertifikat' => [
                                'title' => 'Sertifikat & Kompetensi',
                                'icon' => 'award',
                                'keys' => ['file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab', 'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan', 'file_tahsin', 'file_komputer'],
                                'fields' => [
                                    'file_pkkmb_univ' => ['label' => '8. Scan Sertifikat PKKMB Univ', 'desc' => 'Sertifikat Pengenalan Kehidupan Kampus Universitas.'],
                                    'file_pkkmb_fak' => ['label' => '9. Scan Sertifikat PKKMB Fak', 'desc' => 'Sertifikat Pengenalan Kehidupan Kampus Fakultas.'],
                                    'file_makrab' => ['label' => '10. Scan Sertifikat Makrab', 'desc' => 'Sertifikat Malam Keakraban / LKMM.'],
                                    'file_cisco' => ['label' => '11. Scan Sertifikat Cisco IPv6', 'desc' => 'Sertifikat pelatihan jaringan / Cisco IPv6.'],
                                    'file_workshop' => ['label' => '12. Scan Sertifikat Workshop', 'desc' => 'Sertifikat seminar / workshop keilmuan prodi.'],
                                    'file_organisasi' => ['label' => '13. Scan Sertifikat / Surat Aktif Organisasi', 'desc' => 'SK pengurus / surat keterangan aktif ormawa / sertifikat kegiatan.'],
                                    'file_toefl' => ['label' => '14. Scan Sertifikat TOEFL', 'desc' => 'Sertifikat tes kemampuan Bahasa Inggris (TOEFL / sejenis).'],
                                    'file_kewirausahaan' => ['label' => '15. Scan Sertifikat Wirausaha', 'desc' => 'Sertifikat pelatihan / program kewirausahaan.'],
                                    'file_tahsin' => ['label' => '16. Scan Sertifikat Tahsin', 'desc' => 'Sertifikat kelulusan uji kemampuan baca Al-Quran / keagamaan.'],
                                    'file_komputer' => ['label' => '17. Scan Sertifikat Komputer', 'desc' => 'Sertifikat uji kompetensi / sertifikasi keahlian komputer.'],
                                ]
                            ],
                            'perpus' => [
                                'title' => 'Bebas Perpustakaan',
                                'icon' => 'library',
                                'keys' => ['file_perpus_pinjam', 'file_perpus_sumbang'],
                                'fields' => [
                                    'file_perpus_pinjam' => ['label' => '18. Scan Bebas Pinjam Perpus', 'desc' => 'Surat keterangan bebas peminjaman buku perpustakaan.'],
                                    'file_perpus_sumbang' => ['label' => '19. Scan Sumbang Buku Perpus', 'desc' => 'Tanda terima sumbangan buku / karya ilmiah ke perpustakaan.'],
                                ]
                            ]
                        ];
                    @endphp

                    <!-- 3. Category Filter Tabs (Sleek Border-Bottom Design) -->
                    <div class="border-b border-slate-200 dark:border-slate-700 -mx-6 px-6">
                        <div class="flex items-center gap-1 sm:gap-2 overflow-x-auto no-scrollbar">
                            <!-- All Categories Tab -->
                            <button type="button" 
                                    @click="activeTab = 'all'" 
                                    class="px-4 py-3 border-b-2 text-xs font-bold transition-all whitespace-nowrap inline-flex items-center gap-2 cursor-pointer shrink-0"
                                    :class="activeTab === 'all' ? 'border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50/50 dark:bg-orange-500/10' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/40'">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                <span>Semua Kategori</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold transition-colors" 
                                      :class="totalFilled === totalCount ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200/80 text-slate-600 dark:bg-slate-700 dark:text-slate-300'"
                                      x-text="totalFilled + '/' + totalCount"></span>
                            </button>

                            @foreach($categories as $catKey => $cat)
                                <button type="button" 
                                        @click="activeTab = '{{ $catKey }}'" 
                                        class="px-4 py-3 border-b-2 text-xs font-bold transition-all whitespace-nowrap inline-flex items-center gap-2 cursor-pointer shrink-0"
                                        :class="activeTab === '{{ $catKey }}' ? 'border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50/50 dark:bg-orange-500/10' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/40'">
                                    @if($cat['icon'] === 'document')
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @elseif($cat['icon'] === 'academic')
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                    @elseif($cat['icon'] === 'award')
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @elseif($cat['icon'] === 'library')
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    @endif
                                    <span>{{ $cat['title'] }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold transition-colors"
                                          :class="countFilled({{ json_encode($cat['keys']) }}) === {{ count($cat['keys']) }} ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200/80 text-slate-600 dark:bg-slate-700 dark:text-slate-300'"
                                          x-text="countFilled({{ json_encode($cat['keys']) }}) + '/{{ count($cat['keys']) }}'"></span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- 4. Grouped Input Fields -->
                    <div class="space-y-8">
                        @foreach($categories as $catKey => $cat)
                            <div x-show="activeTab === 'all' || activeTab === '{{ $catKey }}'" x-cloak class="space-y-4">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 bg-orange-100 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 rounded-lg">
                                            @if($cat['icon'] === 'document')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            @elseif($cat['icon'] === 'academic')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                            @elseif($cat['icon'] === 'award')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            @elseif($cat['icon'] === 'library')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $cat['title'] }}</h4>
                                    </div>
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg"
                                          :class="countFilled({{ json_encode($cat['keys']) }}) === {{ count($cat['keys']) }} ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                                          x-text="countFilled({{ json_encode($cat['keys']) }}) + ' dari {{ count($cat['keys']) }} Terisi'"></span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($cat['fields'] as $field => $info)
                                        <div class="p-4 bg-slate-50/60 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl transition-all hover:border-orange-300 dark:hover:border-orange-500/40 hover:bg-white dark:hover:bg-slate-900 space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <label for="{{ $field }}" class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                                                    {{ $info['label'] }} <span class="text-rose-500">*</span>
                                                </label>
                                                <button type="button" 
                                                        @click="pasteSingle('{{ $field }}')" 
                                                        class="px-2 py-0.5 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 rounded-md text-[10px] font-bold transition-all shadow-2xs cursor-pointer active:scale-95 shrink-0"
                                                        title="Tempel tautan dari clipboard">
                                                    📋 Tempel
                                                </button>
                                            </div>
                                            
                                            <p class="text-[11px] text-slate-400 dark:text-slate-500 leading-tight">{{ $info['desc'] }}</p>

                                            <div class="relative">
                                                <input type="url" 
                                                       name="{{ $field }}" 
                                                       id="{{ $field }}" 
                                                       x-model="files['{{ $field }}']" 
                                                       placeholder="https://drive.google.com/..." 
                                                       {{ isset($application->$field) ? '' : 'required' }} 
                                                       class="block w-full text-xs text-slate-700 dark:text-slate-200 rounded-xl border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-800 shadow-2xs py-2 px-3 transition-all">
                                            </div>

                                            <!-- Live Status Indicator -->
                                            <div class="flex items-center justify-between pt-1">
                                                <template x-if="isDrive(files['{{ $field }}'])">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Google Drive Valid</span>
                                                    </span>
                                                </template>
                                                <template x-if="files['{{ $field }}'] && !isDrive(files['{{ $field }}'])">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                                        <span>🔗 Link Terisi</span>
                                                    </span>
                                                </template>
                                                <template x-if="!files['{{ $field }}']">
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Belum diisi</span>
                                                </template>

                                                <template x-if="files['{{ $field }}']">
                                                    <a :href="files['{{ $field }}']" target="_blank" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-0.5">
                                                        <span>Buka Link</span>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    </a>
                                                </template>
                                            </div>
                                            @error($field) <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 pt-6 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            Pastikan seluruh link Google Drive telah disetel ke mode <b class="text-slate-700 dark:text-slate-200">"Anyone with the link can view" (Siapa saja yang memiliki link dapat melihat)</b>.
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md active:scale-95 cursor-pointer">
                            Kirim Pengajuan Sidang
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>

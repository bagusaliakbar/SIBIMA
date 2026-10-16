<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Revisi Hasil Sidang', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight mt-1">
                    Revisi Hasil Sidang Skripsi
                </h2>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">
                    Pantau catatan perbaikan naskah skripsi dari dewan penguji dan dosen pembimbing.
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $totalRevisions = $revisions->count();
        $approvedCount = $revisions->filter(fn($r) => $r->isApproved() || $r->status === 'approved')->count();
        $resubmittedCount = $revisions->filter(fn($r) => !$r->isApproved() && $r->status !== 'approved' && ($r->isResubmitted() || !empty($r->resubmitted_at)))->count();
        $pendingCount = $revisions->filter(fn($r) => !$r->isApproved() && $r->status !== 'approved' && !$r->isResubmitted() && empty($r->resubmitted_at))->count();
        $progressPercent = $totalRevisions > 0 ? round(($approvedCount / $totalRevisions) * 100) : 0;
        $allApproved = $totalRevisions > 0 && $approvedCount === $totalRevisions;
        $firstSchedule = $scheduleDetail?->schedule ?? $revisions->first()?->detail?->schedule;
        $thesisData = $thesis ?? $scheduleDetail?->thesis ?? $revisions->first()?->detail?->thesis;
    @endphp

    <div class="space-y-6 pb-12">
        <!-- 1. Top Context & Academic Status Hero Banner -->
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2 min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/40">
                            Sidang Tugas Akhir / Skripsi
                        </span>
                        @if($firstSchedule && $firstSchedule->date)
                            <span class="text-xs text-slate-400">•</span>
                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ \Carbon\Carbon::parse($firstSchedule->date)->locale('id')->translatedFormat('d F Y') }}
                            </span>
                        @endif
                        @if($firstSchedule && $firstSchedule->location)
                            <span class="text-xs text-slate-400">•</span>
                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                                {{ $firstSchedule->location }}
                            </span>
                        @endif
                    </div>

                    @if($thesisData && $thesisData->title)
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 leading-snug">
                            {{ $thesisData->title }}
                        </h3>
                    @else
                        <h3 class="text-base font-bold text-slate-500 dark:text-slate-400 italic">
                            Belum terdaftar judul skripsi aktif
                        </h3>
                    @endif

                    <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-500 dark:text-slate-400 pt-0.5">
                        @if($thesisData && $thesisData->pembimbing1)
                            <span>Pembimbing 1: <strong class="text-slate-700 dark:text-slate-300">{{ $thesisData->pembimbing1->name }}</strong></span>
                        @endif
                        @if($thesisData && $thesisData->pembimbing2)
                            <span>•</span>
                            <span>Pembimbing 2: <strong class="text-slate-700 dark:text-slate-300">{{ $thesisData->pembimbing2->name }}</strong></span>
                        @endif
                    </div>
                </div>

                <!-- Right Side: Real-time Metric or Status Box -->
                @if($totalRevisions > 0)
                    <div class="lg:w-64 w-full shrink-0 flex flex-col justify-center space-y-2 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100 dark:border-slate-700">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-500 dark:text-slate-400">Progres Revisi</span>
                            <span class="font-bold {{ $allApproved ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">{{ $progressPercent }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $allApproved ? 'bg-emerald-500' : 'bg-rose-600' }}" style="width: {{ $progressPercent }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            <strong>{{ $approvedCount }} dari {{ $totalRevisions }} penguji</strong> telah menyetujui.
                        </p>
                    </div>
                @else
                    <div class="lg:w-72 w-full shrink-0 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-700/60 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Status Revisi</span>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">
                                Menunggu Input Catatan Dosen
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Academic Milestone Workflow Bar -->
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-4 sm:p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs">
            <div class="flex items-center justify-between mb-3 text-xs font-bold text-slate-700 dark:text-slate-300">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Alur Tahapan Sidang & Revisi Skripsi
                </span>
                <span class="text-[11px] text-slate-400">Tahap 3 dari 4</span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center text-xs font-black shrink-0">
                        ✓
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Tahap 1</span>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate">Pendaftaran Sidang</p>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-full {{ $firstSchedule ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-300' }} flex items-center justify-center text-xs font-black shrink-0">
                        {{ $firstSchedule ? '✓' : '2' }}
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Tahap 2</span>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate">Pelaksanaan Ujian</p>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-rose-50/60 dark:bg-rose-950/20 border border-rose-200/80 dark:border-rose-900/40 flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center text-xs font-black shrink-0">
                        3
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase">Tahap 3 • Aktif</span>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">Catatan & Revisi</p>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 flex items-center gap-2.5 opacity-60">
                    <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-300 flex items-center justify-center text-xs font-black shrink-0">
                        4
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Tahap 4</span>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate">Pengesahan Yudisium</p>
                    </div>
                </div>
            </div>
        </div>

        @if($totalRevisions > 0)
            <!-- Section Title -->
            <div class="flex items-center justify-between pt-1">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <span>Daftar Catatan Revisi Dewan Penguji</span>
                    </h3>
                </div>
            </div>

            <!-- Active Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($revisions as $revision)
                    @php
                        $isApproved = $revision->isApproved() || $revision->status === 'approved';
                        $isResubmitted = !$isApproved && ($revision->isResubmitted() || !empty($revision->resubmitted_at));
                        $isPending = !$isApproved && !$isResubmitted;
                        $latestMessage = $revision->messages->first();

                        $thesisObj = $revision->detail->thesis;
                        $isPembimbing1 = $thesisObj && $thesisObj->pembimbing1_id === $revision->examiner_id;
                        $isPembimbing2 = $thesisObj && $thesisObj->pembimbing2_id === $revision->examiner_id;
                        $isExaminer1 = $revision->detail->examiner1_id === $revision->examiner_id;
                        $isExaminer2 = $revision->detail->examiner2_id === $revision->examiner_id;
                    @endphp

                    <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 p-5 sm:p-6 flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-600 transition-colors shadow-2xs">
                        <div class="space-y-4">
                            <!-- Card Header -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 shrink-0">
                                        <img src="{{ $revision->examiner->avatar_url }}" alt="{{ $revision->examiner->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-tight truncate">
                                            {{ $revision->examiner->name }}
                                        </h4>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            @if($isPembimbing1)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                    Pembimbing 1
                                                </span>
                                            @elseif($isPembimbing2)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                    Pembimbing 2
                                                </span>
                                            @elseif($isExaminer1)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                    Penguji 1
                                                </span>
                                            @elseif($isExaminer2)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                    Penguji 2
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                    Dosen Penguji
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div class="shrink-0">
                                    @if($isApproved)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Disetujui
                                        </span>
                                    @elseif($isResubmitted)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            Menunggu Review
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Perlu Revisi
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Note Container -->
                            <div class="bg-slate-50/70 dark:bg-slate-900/40 rounded-xl p-3.5 border border-slate-100 dark:border-slate-800 space-y-2">
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span class="font-semibold text-slate-500 dark:text-slate-400">
                                        @if($latestMessage)
                                            {{ $latestMessage->sender_id === $revision->examiner_id ? 'Catatan Dosen' : 'Tanggapan Terakhir Anda' }}
                                        @else
                                            Catatan Revisi
                                        @endif
                                    </span>
                                    @if($latestMessage)
                                        <span>{{ \Carbon\Carbon::parse($latestMessage->created_at)->diffForHumans() }}</span>
                                    @endif
                                </div>

                                @if($latestMessage)
                                    <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed line-clamp-3">
                                        {{ $latestMessage->message }}
                                    </p>
                                @else
                                    <p class="text-xs text-slate-400 dark:text-slate-500 italic">
                                        Belum ada catatan tertulis dari dosen.
                                    </p>
                                @endif

                                @if($revision->revision_file)
                                    <div class="pt-2 border-t border-slate-200/50 dark:border-slate-800 flex items-center gap-1.5 text-[11px] text-slate-600 dark:text-slate-400 font-medium">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        <span>Terdapat berkas lampiran dari dosen</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Actions -->
                        <div class="pt-5 mt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center gap-2">
                            <a href="{{ route('student-defense-revisions.show', $revision->id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded-xl text-xs font-bold transition-colors shadow-sm shadow-rose-500/20">
                                <span>Detail Revisi</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>

                            @if($revision->messages->count() > 0 || $isApproved)
                                <a href="{{ route('student-defense-revisions.print-pdf', $revision->id) }}" target="_blank" title="Cetak Lembar Catatan Revisi" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-colors">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    <span>PDF</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <!-- Structured Full-Width View When Revisions Are Not Yet Uploaded -->
            <div class="space-y-6">
                <!-- Section 1: Team Examiners Cards -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Dewan Penguji & Pembimbing Skripsi
                        </h3>
                        <span class="text-[11px] text-slate-400">Status: Belum ada catatan yang diinput</span>
                    </div>

                    @php
                        $assignedMembers = collect();
                        if ($thesisData && $thesisData->pembimbing1) {
                            $assignedMembers->push(['user' => $thesisData->pembimbing1, 'role' => 'Pembimbing 1']);
                        }
                        if ($thesisData && $thesisData->pembimbing2) {
                            $assignedMembers->push(['user' => $thesisData->pembimbing2, 'role' => 'Pembimbing 2']);
                        }
                        if ($scheduleDetail && $scheduleDetail->examiner1) {
                            $assignedMembers->push(['user' => $scheduleDetail->examiner1, 'role' => 'Penguji 1']);
                        }
                        if ($scheduleDetail && $scheduleDetail->examiner2) {
                            $assignedMembers->push(['user' => $scheduleDetail->examiner2, 'role' => 'Penguji 2']);
                        }
                    @endphp

                    @if($assignedMembers->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($assignedMembers as $m)
                                <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 p-4 space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 shrink-0">
                                            <img src="{{ $m['user']->avatar_url }}" alt="{{ $m['user']->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-slate-800 dark:text-slate-100 text-xs truncate">
                                                {{ $m['user']->name }}
                                            </h4>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 mt-0.5">
                                                {{ $m['role'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-400">Catatan Revisi:</span>
                                        <span class="font-semibold text-slate-500 dark:text-slate-400">Belum Ada</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Section 2: Informative Process Guide (3-Column Grid) -->
                <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 p-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                            Panduan & Ketentuan Revisi Naskah Sidang
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Informasi mengenai prosedur pengunggahan dan persetujuan naskah skripsi yang telah diujikan.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-slate-50/70 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 flex items-center justify-center font-bold text-sm">
                                1
                            </div>
                            <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                Input Catatan oleh Penguji
                            </h5>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                Setelah sidang berlangsung, dosen penguji akan memasukkan catatan perbaikan atau naskah beranotasi melalui akun SIBIMA masing-masing.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50/70 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 flex items-center justify-center font-bold text-sm">
                                2
                            </div>
                            <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                Perbaikan & Diskusi Online
                            </h5>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                Mahasiswa menanggapi catatan poin per poin, melampirkan naskah revisi terbaru (Google Docs/PDF), dan mengonfirmasi hasil perbaikan.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50/70 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 flex items-center justify-center font-bold text-sm">
                                3
                            </div>
                            <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100">
                                Cetak Lembar Pengesahan
                            </h5>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                Setelah seluruh penguji dan pembimbing memberikan status <em>Disetujui</em>, tombol cetak lembar pengesahan resmi akan aktif otomatis.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Bottom Notice & Quick Actions -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-700/70 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300">
                            Jika Anda telah menyelesaikan ujian sidang namun catatan belum tampil, silakan koordinasikan dengan dosen penguji Anda.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

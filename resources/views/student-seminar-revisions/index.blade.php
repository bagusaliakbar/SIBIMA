<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Revisi Hasil Seminar', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight mt-1">
                    Revisi Hasil Seminar Proposal
                </h2>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">
                    Pantau catatan perbaikan seminar proposal dari dosen penguji.
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $examinersList = $examiners ?? collect();
        $totalExaminers = $examinersList->count() > 0 ? $examinersList->count() : $revisions->count();
        $approvedCount = $examinersList->count() > 0 
            ? $examinersList->filter(fn($e) => $e['revision'] && ($e['revision']->isApproved() || $e['revision']->status === 'approved'))->count()
            : $revisions->filter(fn($r) => $r->isApproved() || $r->status === 'approved')->count();
        $progressPercent = $totalExaminers > 0 ? round(($approvedCount / $totalExaminers) * 100) : 0;
        $allApproved = $totalExaminers > 0 && $approvedCount === $totalExaminers;
        $firstSchedule = $scheduleDetail?->schedule ?? $revisions->first()?->detail?->schedule;
        $thesisData = $thesis ?? $scheduleDetail?->thesis ?? $revisions->first()?->detail?->thesis;
    @endphp

    <div class="space-y-6 pb-12">
        <!-- Top Context Card -->
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2 min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-orange-50 text-orange-700 dark:bg-orange-950/40 dark:text-orange-300 border border-orange-200/60 dark:border-orange-800/40">
                            Seminar Proposal
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
                            Belum terdaftar judul proposal aktif
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

                <!-- Right Side: Status or Progress -->
                @if($totalExaminers > 0)
                    <div class="lg:w-64 w-full shrink-0 flex flex-col justify-center space-y-2 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100 dark:border-slate-700">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-500 dark:text-slate-400">Progres Revisi</span>
                            <span class="font-bold {{ $allApproved ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }}">{{ $progressPercent }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $allApproved ? 'bg-emerald-500' : 'bg-orange-600' }}" style="width: {{ $progressPercent }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            <strong>{{ $approvedCount }} dari {{ $totalExaminers }} penguji</strong> telah menyetujui.
                        </p>
                    </div>
                @else
                    <div class="lg:w-72 w-full shrink-0 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-700/60 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Status Revisi</span>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">
                                Menunggu Jadwal & Input Penguji
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Main Card: Daftar Revisi Seminar (Seperti Biasa Saja) -->
        <x-table-card title="Daftar Catatan Revisi Seminar" subtitle="Catatan hasil seminar proposal dari dosen penguji.">
            @if($totalExaminers > 0)
                <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($examinersList as $item)
                        @php
                            $examinerUser = $item['user'];
                            $roleLabel = $item['role_label'];
                            $revision = $item['revision'];
                            $hasRevision = !is_null($revision);

                            $isApproved = $hasRevision && ($revision->isApproved() || $revision->status === 'approved');
                            $isResubmitted = $hasRevision && !$isApproved && ($revision->isResubmitted() || !empty($revision->resubmitted_at));
                            $isPending = $hasRevision && !$isApproved && !$isResubmitted;
                            $latestMessage = $hasRevision ? $revision->messages->first() : null;
                        @endphp

                        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 p-5 sm:p-6 flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-600 transition-colors shadow-2xs">
                            <div class="space-y-4">
                                <!-- Card Header -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 shrink-0">
                                            <img src="{{ $examinerUser->avatar_url }}" alt="{{ $examinerUser->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-tight truncate">
                                                {{ $examinerUser->name }}
                                            </h4>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                    {{ $roleLabel }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="shrink-0">
                                        @if($isApproved)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40">
                                                <span class="font-bold text-emerald-600">✓✓</span>
                                                <span>Disetujui</span>
                                            </span>
                                        @elseif($isResubmitted)
                                            @php
                                                $studentMessages = $revision->messages->where('sender_id', Auth::id());
                                                $lastStudentMsg = $studentMessages->last();
                                                $isReadByDosen = $lastStudentMsg && !empty($lastStudentMsg->read_at);
                                            @endphp
                                            @if($isReadByDosen)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/40" title="Revisi telah dibaca dosen">
                                                    <span class="font-bold text-sky-600">✓✓</span>
                                                    <span>Dibaca Dosen</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 text-slate-700 dark:bg-slate-900/60 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60" title="Revisi terkirim, menunggu dosen">
                                                    <span class="font-bold text-slate-400">✓</span>
                                                    <span>Terkirim</span>
                                                </span>
                                            @endif
                                        @elseif($hasRevision)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/40">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span>Perlu Revisi</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/80 dark:border-slate-700/80" title="Dosen penguji belum mengunggah catatan revisi">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                <span>Belum Ada Catatan</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Note Container -->
                                <div class="bg-slate-50/70 dark:bg-slate-900/40 rounded-xl p-3.5 border border-slate-100 dark:border-slate-800 space-y-2">
                                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                                        <span class="font-semibold text-slate-500 dark:text-slate-400">
                                            @if($latestMessage)
                                                {{ $latestMessage->sender_id === $examinerUser->id ? 'Catatan Penguji' : 'Tanggapan Terakhir Anda' }}
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
                                    @elseif($hasRevision)
                                        <p class="text-xs text-slate-400 dark:text-slate-500 italic">
                                            Belum ada catatan tertulis dari penguji.
                                        </p>
                                    @else
                                        <p class="text-xs text-slate-400 dark:text-slate-500 italic">
                                            Dosen penguji belum mengunggah catatan revisi seminar proposal.
                                        </p>
                                    @endif

                                    @if($hasRevision && $revision->revision_file)
                                        <div class="pt-2 border-t border-slate-200/50 dark:border-slate-800 flex items-center gap-1.5 text-[11px] text-slate-600 dark:text-slate-400 font-medium">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            <span>Terdapat berkas lampiran dari dosen</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Actions -->
                            <div class="pt-5 mt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center gap-2">
                                @if($hasRevision)
                                    <a href="{{ route('student-seminar-revisions.show', $revision->id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white rounded-xl text-xs font-bold transition-colors shadow-sm shadow-orange-500/20">
                                        <span>Buka Revisi</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>

                                    @if($revision->messages->count() > 0 || $isApproved)
                                        <a href="{{ route('student-seminar-revisions.print-pdf', $revision->id) }}" target="_blank" title="Cetak Lembar Catatan Revisi" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-colors">
                                            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-6 4h4"/>
                                            </svg>
                                            <span>PDF</span>
                                        </a>
                                    @endif
                                @else
                                    <button type="button" disabled class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200/80 dark:border-slate-700 rounded-xl text-xs font-bold cursor-not-allowed">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Menunggu Catatan Penguji</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Kotak Data Revisi Belum Ada (Seperti Biasa Saja) -->
                <div class="py-16 px-4 text-center w-full">
                    <x-empty-state 
                        title="Data Revisi Seminar Belum Ada" 
                        description="Dosen penguji belum mengunggah catatan revisi untuk seminar proposal Anda." 
                        icon="revision" />
                </div>
            @endif
        </x-table-card>
    </div>
</x-app-layout>

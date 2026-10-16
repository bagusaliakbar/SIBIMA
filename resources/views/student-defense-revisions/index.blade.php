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
                    Daftar catatan perbaikan naskah skripsi dari dewan penguji dan dosen pembimbing.
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
        $firstSchedule = $revisions->first()?->detail?->schedule;
        $thesisData = $thesis ?? $revisions->first()?->detail?->thesis;
    @endphp

    <div class="space-y-6">
        @if($totalRevisions > 0)
            <!-- Clean Compact Overview Card -->
            <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-1.5 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/40">
                                Sidang Tugas Akhir / Skripsi
                            </span>
                            @if($firstSchedule && $firstSchedule->date)
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">
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
                            <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-100 line-clamp-1">
                                {{ $thesisData->title }}
                            </h3>
                        @endif

                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            @if($allApproved)
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">Semua revisi telah disetujui.</span> Anda dapat mengunduh lembar pengesahan revisi.
                            @else
                                <span>Status Persetujuan:</span> <strong class="text-slate-700 dark:text-slate-200">{{ $approvedCount }} dari {{ $totalRevisions }} penguji</strong> telah menyetujui.
                            @endif
                        </p>
                    </div>

                    <!-- Compact Progress -->
                    <div class="lg:w-64 w-full shrink-0 flex flex-col justify-center space-y-2 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100 dark:border-slate-700">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-500 dark:text-slate-400">Progres Revisi</span>
                            <span class="font-bold {{ $allApproved ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">{{ $progressPercent }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $allApproved ? 'bg-emerald-500' : 'bg-rose-600' }}" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards Grid: Minimalist & Clean -->
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
            <!-- Minimal Empty State -->
            <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-10 text-center border border-slate-200/90 dark:border-slate-700/80 max-w-lg mx-auto space-y-4 my-8">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700/60 flex items-center justify-center mx-auto text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                        Belum Ada Catatan Revisi Sidang
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Dosen penguji maupun pembimbing belum mengunggah catatan revisi atau sidang skripsi belum selesai dilaksanakan.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

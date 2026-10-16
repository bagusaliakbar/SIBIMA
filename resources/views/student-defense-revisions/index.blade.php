<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Revisi Hasil Sidang', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center gap-2">
                    <span>Revisi Hasil Sidang Skripsi</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">
                    Pantau catatan perbaikan dewan penguji dan pembimbing, diskusikan naskah skripsi final, serta unduh lembar pengesahan.
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
            <!-- Milestone Summary Banner -->
            <div class="relative overflow-hidden bg-white dark:bg-slate-800/90 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/70 shadow-sm">
                <!-- Background ambient decorative glows -->
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-rose-500/10 dark:bg-rose-500/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-indigo-500/10 dark:bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <!-- Left Column: Context & Progress Status -->
                    <div class="space-y-4 max-w-2xl">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/50">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                Sidang Tugas Akhir / Skripsi
                            </span>

                            @if($firstSchedule && $firstSchedule->date)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 dark:bg-slate-700/60 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ \Carbon\Carbon::parse($firstSchedule->date)->locale('id')->translatedFormat('d F Y') }}
                                </span>
                            @endif

                            @if($firstSchedule && $firstSchedule->location)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-700">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $firstSchedule->location }}
                                </span>
                            @endif
                        </div>

                        <div>
                            @if($allApproved)
                                <div class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-black text-sm uppercase tracking-wide mb-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Seluruh Revisi Sidang Disetujui!
                                </div>
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                    Selamat! Dewan Penguji & Pembimbing Telah Menyetujui Naskah Final
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                                    Semua revisi hasil sidang tugas akhir Anda telah tuntas disetujui. Anda dapat mencetak lembar pengesahan revisi untuk kelengkapan administrasi yudisium dan wisuda.
                                </p>
                            @else
                                <div class="inline-flex items-center gap-2 text-rose-600 dark:text-rose-400 font-black text-sm uppercase tracking-wide mb-1">
                                    <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Progress Revisi Sidang Akhir
                                </div>
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                    {{ $approvedCount }} dari {{ $totalRevisions }} Dosen Penguji Telah Menyetujui
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                                    Segera selesaikan catatan perbaikan dari dosen pembimbing dan penguji, unggah tautan dokumen naskah yang telah diperbaiki, dan pantau persetujuan dosen.
                                </p>
                            @endif

                            @if($thesisData && $thesisData->title)
                                <div class="mt-3.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 flex items-start gap-2.5">
                                    <svg class="w-4 h-4 text-rose-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 line-clamp-2">
                                        {{ $thesisData->title }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column: Progress Card & Metric Badges -->
                    <div class="lg:w-80 w-full bg-slate-50/80 dark:bg-slate-900/60 p-5 rounded-2xl border border-slate-200/70 dark:border-slate-700/60 flex flex-col justify-between gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Persetujuan Revisi</span>
                                <span class="text-lg font-black {{ $allApproved ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $progressPercent }}%
                                </span>
                            </div>
                            <!-- Progress Bar -->
                            <div class="w-full h-3 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden p-0.5">
                                <div class="h-full rounded-full transition-all duration-700 {{ $allApproved ? 'bg-emerald-500' : 'bg-gradient-to-r from-rose-500 via-pink-500 to-indigo-600' }}" style="width: {{ $progressPercent }}%"></div>
                            </div>
                        </div>

                        <!-- Mini Stats Grid -->
                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-800">
                            <div class="text-center p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/60">
                                <span class="block text-base font-black text-slate-800 dark:text-slate-100">{{ $totalRevisions }}</span>
                                <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400">Penguji</span>
                            </div>
                            <div class="text-center p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-800/40">
                                <span class="block text-base font-black text-emerald-600 dark:text-emerald-400">{{ $approvedCount }}</span>
                                <span class="block text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Selesai</span>
                            </div>
                            <div class="text-center p-2 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-100 dark:border-amber-800/40">
                                <span class="block text-base font-black text-amber-600 dark:text-amber-400">{{ $pendingCount + $resubmittedCount }}</span>
                                <span class="block text-[9px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Tertunda</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Title -->
            <div class="flex items-center justify-between pt-2">
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        Daftar Dosen Penguji & Catatan Revisi Sidang
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pilih kartu dosen di bawah untuk melihat rincian catatan dan membuka ruang diskusi revisi naskah final.
                    </p>
                </div>
            </div>

            <!-- Examiner Revision Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                    <div class="group bg-white dark:bg-slate-800/90 rounded-3xl border transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between overflow-hidden relative {{ $isApproved ? 'border-emerald-200/80 dark:border-emerald-900/50 hover:border-emerald-400' : ($isResubmitted ? 'border-blue-200/80 dark:border-blue-900/50 hover:border-blue-400' : 'border-amber-200/80 dark:border-amber-900/50 hover:border-amber-400') }}">
                        
                        <!-- Top Accent Line -->
                        <div class="h-1.5 w-full {{ $isApproved ? 'bg-emerald-500' : ($isResubmitted ? 'bg-blue-500' : 'bg-amber-500') }}"></div>

                        <div class="p-6 space-y-5">
                            <!-- Card Header: Avatar, Name, Role & Status -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="relative shrink-0">
                                        <div class="w-13 h-13 w-12 h-12 rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 shadow-sm bg-slate-100 dark:bg-slate-700">
                                            <img src="{{ $revision->examiner->avatar_url }}" alt="{{ $revision->examiner->name }}" class="w-full h-full object-cover">
                                        </div>
                                        @if($isApproved)
                                            <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center ring-2 ring-white dark:ring-slate-800 text-[10px]">
                                                ✓
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-black text-slate-800 dark:text-slate-100 text-sm sm:text-base leading-tight truncate group-hover:text-rose-600 transition-colors uppercase tracking-tight">
                                            {{ $revision->examiner->name }}
                                        </h4>
                                        <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                            @if($isPembimbing1)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                                                    Pembimbing 1
                                                </span>
                                            @elseif($isPembimbing2)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                                                    Pembimbing 2
                                                </span>
                                            @elseif($isExaminer1)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                                    Penguji 1
                                                </span>
                                            @elseif($isExaminer2)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                                    Penguji 2
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                                    Dosen Penguji
                                                </span>
                                            @endif
                                            <span class="text-[10px] text-slate-400 font-bold">•</span>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                                {{ \Carbon\Carbon::parse($revision->detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div class="shrink-0">
                                    @if($isApproved)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shadow-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Selesai Disetujui
                                        </span>
                                    @elseif($isResubmitted)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200 dark:border-blue-800 shadow-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-ping"></span>
                                            Menunggu Review
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-700 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800 shadow-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Perlu Perbaikan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Catatan Revisi Snippet / Quote Box -->
                            <div class="relative p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800/80">
                                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                        @if($latestMessage)
                                            {{ $latestMessage->sender_id === $revision->examiner_id ? 'Catatan Dosen' : 'Tanggapan Terakhir Anda' }}
                                        @else
                                            Catatan Revisi
                                        @endif
                                    </span>

                                    @if($latestMessage)
                                        <span class="text-[9px] font-bold text-slate-400">
                                            {{ \Carbon\Carbon::parse($latestMessage->created_at)->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>

                                @if($latestMessage)
                                    <p class="text-xs text-slate-700 dark:text-slate-300 font-medium leading-relaxed italic line-clamp-3">
                                        "{{ Str::limit($latestMessage->message, 140) }}"
                                    </p>
                                @else
                                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium italic">
                                        Belum ada catatan revisi tertulis yang dikirimkan oleh dosen.
                                    </p>
                                @endif

                                @if($revision->revision_file)
                                    <div class="mt-2.5 pt-2.5 border-t border-slate-200/60 dark:border-slate-800 flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400">
                                            Tersedia berkas naskah bertanda dari dosen
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Additional Status Information -->
                            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 px-1">
                                <span class="font-bold flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    {{ $revision->messages_count ?? $revision->messages->count() }} pesan diskusi
                                </span>

                                @if($revision->resubmitted_at)
                                    <span class="text-[10px] font-bold text-slate-400">
                                        Dikirim ulang: {{ \Carbon\Carbon::parse($revision->resubmitted_at)->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Footer: Action Buttons -->
                        <div class="p-4 sm:p-5 bg-slate-50/70 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2.5">
                            <a href="{{ route('student-defense-revisions.show', $revision->id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 shadow-md shadow-rose-500/20 hover:shadow-rose-500/35">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                Buka Diskusi & Kirim Naskah
                            </a>

                            @if($revision->messages->count() > 0 || $isApproved)
                                <a href="{{ route('student-defense-revisions.print-pdf', $revision->id) }}" target="_blank" title="Cetak Lembar Catatan Revisi (PDF)" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-white hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    <span class="hidden sm:inline">Cetak PDF</span>
                                </a>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

        @else
            <!-- Clean Aesthetic Empty State -->
            <div class="bg-white dark:bg-slate-800/90 rounded-3xl p-12 text-center border border-slate-200/80 dark:border-slate-700/70 shadow-sm max-w-xl mx-auto space-y-5 my-8">
                <div class="w-20 h-20 rounded-3xl bg-rose-50 dark:bg-rose-950/40 border border-rose-100 dark:border-rose-800/50 flex items-center justify-center mx-auto text-rose-500 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight">
                        Belum Ada Catatan Revisi Sidang
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                        Dosen penguji maupun pembimbing belum mengunggah catatan revisi atau sidang tugas akhir Anda belum selesai dilaksanakan.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-slate-100 dark:hover:bg-white text-white dark:text-slate-900 text-xs font-black uppercase tracking-wider transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

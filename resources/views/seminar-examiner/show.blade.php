<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                @php
                    $backUrl = match(request('redirect_to')) {
                        'monitoring-revisions' => route('monitoring.revisions'),
                        'monitoring-scores' => route('monitoring.revisions'),
                        default => route('seminar-examiner.index'),
                    };
                    $backLabel = match(request('redirect_to')) {
                        'monitoring-revisions' => 'Monitoring Revisi Seminar',
                        'monitoring-scores' => 'Rekap Nilai Seminar',
                        default => 'Tugas Penguji Seminar',
                    };
                @endphp
                <x-breadcrumb :items="[
                    ['label' => $backLabel, 'route' => $backUrl],
                    ['label' => 'Riwayat Revisi', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Detail Mahasiswa & Riwayat Revisi Seminar
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($targetUser))
                        Mode {{ ucfirst(auth()->user()->role) }}: Mengelola revisi atas nama <span class="font-black text-indigo-600 dark:text-indigo-400 ml-1">{{ $targetUser->name }}</span>
                    @else
                        Pantau progres perbaikan dan berikan feedback berkelanjutan
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                @php
                    $canPrintMyRevision = $myRevision && $myRevision->messages->where('sender_id', $actingId)->count() > 0;
                @endphp
                @if($canPrintMyRevision)
                    <a href="{{ route('seminar-examiner.print-revision', $myRevision->id) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-indigo-500/20"
                       title="Cetak Lembar Poin Revisi Penguji Ini (PDF)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak Revisi</span>
                    </a>
                @endif
                <a href="{{ $backUrl }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        mode: 'points',
        points: [''],
        additionalNotes: '',
        notes: '',
        previewModalOpen: false,
        previewUrl: '',
        previewRawUrl: '',
        previewTitle: 'Preview Dokumen',
        addPoint(text = '') {
            this.points.push(text);
            this.$nextTick(() => {
                const inputs = this.$refs.pointsListContainer?.querySelectorAll('textarea');
                if (inputs && inputs.length) {
                    inputs[inputs.length - 1].focus();
                }
            });
        },
        removePoint(index) {
            if (this.points.length > 1) {
                this.points.splice(index, 1);
            } else {
                this.points = [''];
            }
        },
        appendTemplate(text) {
            if (this.mode === 'points') {
                const lastIdx = this.points.length - 1;
                if (this.points.length > 0 && this.points[lastIdx].trim() === '') {
                    this.points[lastIdx] = text;
                } else {
                    this.addPoint(text);
                }
            } else {
                if (!this.notes || this.notes.trim() === '') {
                    this.notes = text;
                } else {
                    this.notes = this.notes + '\n\n' + text;
                }
                this.$nextTick(() => {
                    if (this.$refs.noteInput) {
                        this.$refs.noteInput.focus();
                    }
                });
            }
        },
        compiledNotes() {
            if (this.mode === 'points') {
                const valid = this.points.map(p => p.trim()).filter(p => p.length > 0);
                if (valid.length === 0) return '';
                let result = valid.map((p, i) => `${i + 1}. ${p}`).join('\n');
                if (this.additionalNotes && this.additionalNotes.trim()) {
                    result += '\n\nCatatan Tambahan: ' + this.additionalNotes.trim();
                }
                return result;
            }
            return this.notes;
        },
        prepareSubmit(e) {
            if (this.mode === 'points') {
                const valid = this.points.filter(p => p.trim().length > 0);
                if (valid.length === 0) {
                    alert('Silakan isi minimal 1 poin catatan revisi.');
                    e.preventDefault();
                    return false;
                }
                this.notes = this.compiledNotes();
            } else {
                if (!this.notes || !this.notes.trim()) {
                    alert('Silakan tuliskan catatan revisi.');
                    e.preventDefault();
                    return false;
                }
            }
            return true;
        },
        openPreview(link, title) {
            this.previewTitle = title || 'Preview Dokumen';
            this.previewRawUrl = link;
            let url = link;
            if (link.includes('drive.google.com/file/d/')) {
                url = link.replace(/\/view(\?.*)?$/, '/preview').replace(/\/edit(\?.*)?$/, '/preview');
                if (!url.includes('/preview')) {
                    url = url.replace(/\/?$/, '/preview');
                }
            } else if (link.includes('drive.google.com/open?id=')) {
                const id = link.split('id=')[1]?.split('&')[0];
                if (id) {
                    url = 'https://drive.google.com/file/d/' + id + '/preview';
                }
            } else if (link.includes('docs.google.com/document/d/') || link.includes('docs.google.com/spreadsheets/d/') || link.includes('docs.google.com/presentation/d/')) {
                url = link.replace(/\/edit(\?.*)?$/, '/preview').replace(/\/view(\?.*)?$/, '/preview');
            }
            this.previewUrl = url;
            this.previewModalOpen = true;
        },
        closePreview() {
            this.previewModalOpen = false;
            this.previewUrl = '';
        }
    }" class="space-y-6">
        @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($examiners))
            <!-- Admin / Kaprodi Examiner Selector Card -->
            <div class="bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-pink-500/10 border border-indigo-200/60 dark:border-indigo-800/40 rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                            Mode {{ ucfirst(auth()->user()->role) }}
                        </span>
                        <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">
                            Pilih Dosen Penguji Yang Dikelola Revisinya
                        </h4>
                    </div>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium italic">
                        *Klik dosen untuk melihat atau memberikan revisi atas nama dosen tersebut
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($examiners as $ex)
                        @php
                            $isSelected = ($actingId == $ex['user']->id);
                            $rev = $ex['revision'];
                            $hasRevisionNotes = $rev && $rev->messages->where('sender_id', $ex['user']->id)->count() > 0;
                        @endphp
                        <a href="{{ route('seminar-examiner.show', ['detail' => $detail->id, 'target_examiner_id' => $ex['user']->id, 'redirect_to' => request('redirect_to')]) }}"
                           class="relative p-4 rounded-xl border transition-all flex flex-col justify-between {{ $isSelected ? 'bg-white dark:bg-slate-800 border-indigo-500 shadow-md ring-2 ring-indigo-500/20' : 'bg-white/70 dark:bg-slate-800/50 border-slate-200/80 dark:border-slate-700/60 hover:bg-white dark:hover:bg-slate-800 hover:border-slate-300' }}">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[9px] font-black uppercase tracking-wider {{ $isSelected ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                                        {{ $ex['role_label'] }}
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        @if($hasRevisionNotes)
                                            <a href="{{ route('seminar-examiner.print-revision', $rev->id) }}" target="_blank"
                                               class="p-1 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 rounded-md transition-all"
                                               title="Cetak Poin Revisi {{ $ex['role_label'] }} (PDF)"
                                               @click.stop>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                        @endif
                                        @if($rev && $rev->isApproved())
                                            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[8px] font-black rounded-md uppercase">
                                                Revisi Selesai
                                            </span>
                                        @elseif($rev && $rev->isResubmitted())
                                            <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-[8px] font-black rounded-md uppercase animate-pulse">
                                                Menunggu Review
                                            </span>
                                        @elseif($rev)
                                            <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[8px] font-black rounded-md uppercase">
                                                Menunggu Mahasiswa
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 text-[8px] font-black rounded-md uppercase">
                                                Belum Ada
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="font-bold text-xs text-slate-800 dark:text-slate-100 truncate" title="{{ $ex['user']->name }}">
                                    {{ $ex['user']->name }}
                                </div>
                            </div>
                            @if($isSelected)
                                <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-tighter">
                                    <span>Sedang Aktif Dipilih</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Student Info -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden sticky top-6">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 dark:bg-indigo-900/10 rounded-full -mr-12 -mt-12"></div>
                    <div class="relative">
                        <div class="w-16 h-16 rounded-2xl overflow-hidden mb-6 border border-slate-200 dark:border-slate-700 shadow-md">
                            <img src="{{ $detail->thesis?->student?->avatar_url }}" alt="{{ $detail->thesis?->student?->name ?? 'Mahasiswa' }}" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $detail->thesis?->student?->name ?? 'Mahasiswa' }}</h3>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $detail->thesis?->student?->identifier ?? '-' }}</p>
                        
                        <div class="mt-8 space-y-4">
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Judul Skripsi</span>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300 leading-relaxed italic">"{{ $detail->thesis->title }}"</p>
                            </div>
                            @php
                                $app = $detail->thesis?->seminarApplications?->where('status', '!=', 'rejected')->sortByDesc('id')->first() 
                                    ?? $detail->thesis?->seminarApplication;
                                $filePath = $app?->file_skripsi;
                                $docUrl = null;
                                if ($filePath) {
                                    $docUrl = filter_var($filePath, FILTER_VALIDATE_URL) 
                                        ? $filePath 
                                        : route('download.private', ['path' => $filePath, 'inline' => 1]);
                                }
                            @endphp
                            @if($docUrl)
                                <div class="pt-1">
                                    <a href="{{ $docUrl }}" target="_blank" 
                                       class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 text-indigo-700 dark:text-indigo-300 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 dark:hover:text-white transition-all shadow-xs group/doc">
                                        <svg class="w-4 h-4 text-indigo-500 group-hover/doc:text-white transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>Buka Dokumen Skripsi</span>
                                        <svg class="w-3.5 h-3.5 opacity-70 group-hover/doc:opacity-100 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                            <div class="pt-4 border-t border-slate-50 dark:border-slate-700">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Jadwal Seminar</span>
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                    <div class="flex items-center text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div class="flex items-center text-[11px] font-medium text-slate-500">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H:i') }} WIB
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat History -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Messages Container -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 flex flex-col h-[640px]">
                    <div class="p-5 sm:p-6 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                Diskusi Revisi Seminar
                            </h3>
                            @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($targetUser))
                                <p class="text-[10px] text-slate-400 font-bold mt-0.5">Penguji: {{ $targetUser->name }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($canPrintMyRevision)
                                <a href="{{ route('seminar-examiner.print-revision', $myRevision->id) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:hover:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-xs"
                                   title="Cetak Lembar Poin Revisi Penguji Ini (PDF)">
                                    <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    <span>Cetak Revisi</span>
                                </a>
                            @endif
                            @if($myRevision)
                                @if($myRevision->isApproved())
                                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-emerald-200">Revisi Selesai</span>
                                @elseif($myRevision->isResubmitted())
                                    <span class="px-2.5 py-0.5 bg-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-blue-200 animate-pulse">Menunggu Review</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-amber-100 text-amber-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-amber-200">Menunggu Mahasiswa</span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6 bg-slate-50/30 dark:bg-slate-900/10">
                        @if($myRevision && $myRevision->messages->count() > 0)
                            @foreach($myRevision->messages as $msg)
                                @php $isMe = $msg->sender_id === Auth::id() || (isset($targetUser) && $msg->sender_id === $targetUser->id); @endphp
                                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[85%] {{ $isMe ? 'order-1 items-end' : 'items-start' }} flex flex-col">
                                        <div class="flex items-center space-x-2 mb-1.5 {{ $isMe ? 'flex-row-reverse space-x-reverse' : '' }}">
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">
                                                @if($isMe)
                                                    {{ $msg->sender?->name ?? 'Anda (Penguji)' }}
                                                @else
                                                    {{ $msg->sender?->name ?? $detail->thesis->student->name }}
                                                @endif
                                            </span>
                                            <span class="text-[9px] text-slate-400 font-medium">{{ $msg->created_at->format('H:i, d M') }}</span>
                                        </div>
                                        <div class="p-4 rounded-2xl shadow-sm text-sm font-medium leading-relaxed {{ $isMe ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-tl-none border border-slate-100 dark:border-slate-700' }}">
                                            <x-revision-message-content :message="$msg->message" :is-me="$isMe" theme="indigo" />
                                            
                                            @if($msg->file_path)
                                                <div class="mt-3 pt-3 border-t {{ $isMe ? 'border-white/10' : 'border-slate-50 dark:border-slate-700' }} flex items-center gap-2 flex-wrap">
                                                    <!-- Modal Preview Button -->
                                                    <button type="button" 
                                                            @click="openPreview('{{ $msg->file_path }}', 'Dokumen dari {{ $isMe ? ($msg->sender?->name ?? 'Penguji') : ($msg->sender?->name ?? $detail->thesis->student->name) }}')"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-xs {{ $isMe ? 'bg-white/20 hover:bg-white/30 text-white' : 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:hover:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60' }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        <span>Lihat Preview</span>
                                                    </button>

                                                    <!-- Direct Tab Link -->
                                                    <a href="{{ $msg->file_path }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-bold transition-all {{ $isMe ? 'text-indigo-100 hover:text-white underline' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                        <span>Tab Baru</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="h-full flex flex-col items-center justify-center opacity-50 grayscale">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                </div>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Belum ada diskusi</p>
                            </div>
                        @endif
                    </div>

                    <!-- Input Area -->
                    <div class="p-5 sm:p-6 border-t border-slate-50 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-b-2xl">
                        @if($myRevision?->status === 'approved')
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl border border-emerald-100 dark:border-emerald-500/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-sm shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">Revisi Telah Selesai</p>
                                        <p class="text-[10px] text-emerald-600/80 dark:text-emerald-400 font-medium">Revisi untuk penguji ini sudah disetujui (Final).</p>
                                    </div>
                                </div>
                                @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) || $myRevision->examiner_id === auth()->id())
                                    <form action="{{ route('seminar-examiner.unapprove-revision', $myRevision->id) }}" method="POST" onsubmit="return confirm('Batalkan status selesai dan buka kembali revisi seminar untuk mahasiswa ini?');">
                                        @csrf
                                        <button type="submit" class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Batalkan Selesai & Buka Revisi
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <form action="{{ route('seminar-examiner.store-revision', $detail->id) }}" 
                                  method="POST" 
                                  @submit="prepareSubmit($event)"
                                  class="space-y-4">
                                @csrf
                                @if(isset($actingId))
                                    <input type="hidden" name="target_examiner_id" value="{{ $actingId }}">
                                @endif
                                @if(request('redirect_to'))
                                    <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                                @endif
                                <input type="hidden" name="revision_mode" :value="mode">
                                <input type="hidden" name="revision_notes" :value="compiledNotes()">

                                <div class="space-y-3">
                                    <!-- Header with Mode Selector Toggle -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700/60 pb-2.5">
                                        <div>
                                            <label class="text-[11px] font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                                <span>Catatan Revisi & Feedback</span>
                                                @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($targetUser))
                                                    <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold lowercase">(atas nama {{ $targetUser->name }})</span>
                                                @endif
                                            </label>
                                            <p class="text-[10px] text-slate-400 mt-0.5">Tuliskan butir-butir perbaikan yang wajib diperbaiki mahasiswa</p>
                                        </div>

                                        <!-- Mode Switcher Tabs -->
                                        <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700 self-start sm:self-auto shadow-xs">
                                            <button type="button" 
                                                    @click="mode = 'points'"
                                                    :class="mode === 'points' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-300 shadow-xs font-black' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[10px] uppercase tracking-wider transition-all flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                                <span>Mode Poin</span>
                                            </button>
                                            <button type="button" 
                                                    @click="mode = 'text'"
                                                    :class="mode === 'text' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-300 shadow-xs font-black' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[10px] uppercase tracking-wider transition-all flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                <span>Mode Bebas</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Quick Revision Notes Template Chips -->
                                    <div class="space-y-1">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">💡 Template Cepat (Klik untuk tambah):</span>
                                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                                            <button type="button" 
                                                    @click="appendTemplate('Mohon perbaiki format penulisan, margin, tata bahasa, dan pastikan daftar pustaka telah sesuai dengan format pedoman serta terupdate minimal 5 tahun terakhir.')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 dark:bg-slate-800 dark:hover:bg-indigo-950/40 dark:text-slate-200 dark:hover:text-indigo-300 rounded-xl text-[11px] font-bold border border-slate-200 dark:border-slate-700 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                                <span class="text-indigo-500 font-black">+</span>
                                                <span>Format Penulisan & Daftar Pustaka</span>
                                            </button>

                                            <button type="button" 
                                                    @click="appendTemplate('Perbaiki metodologi penelitian, perjelas teknik penentuan sampel/populasi, dan jelaskan langkah validasi instrumen secara detail.')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 dark:bg-slate-800 dark:hover:bg-indigo-950/40 dark:text-slate-200 dark:hover:text-indigo-300 rounded-xl text-[11px] font-bold border border-slate-200 dark:border-slate-700 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                                <span class="text-indigo-500 font-black">+</span>
                                                <span>Perbaiki Metodologi & Sampel</span>
                                            </button>

                                            <button type="button" 
                                                    @click="appendTemplate('Lengkapi analisis dan pembahasan pada Bab 4, serta bandingkan temuan hasil pengujian dengan penelitian relevan sebelumnya.')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 dark:bg-slate-800 dark:hover:bg-indigo-950/40 dark:text-slate-200 dark:hover:text-indigo-300 rounded-xl text-[11px] font-bold border border-slate-200 dark:border-slate-700 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                                <span class="text-indigo-500 font-black">+</span>
                                                <span>Lengkapi Hasil Pembahasan Bab 4</span>
                                            </button>

                                            <button type="button" 
                                                    @click="appendTemplate('Revisi telah diperiksa dan disetujui. Silakan lanjut ke proses pengesahan naskah seminar / tahap berikutnya.')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 dark:text-emerald-300 rounded-xl text-[11px] font-bold border border-emerald-200 dark:border-emerald-800 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                                <span class="text-emerald-600 font-black">✓</span>
                                                <span>Revisi Disetujui, Silakan Cetak</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- 1. MODE POIN-POIN (DEFAULT) -->
                                    <div x-show="mode === 'points'" class="space-y-3">
                                        <div x-ref="pointsListContainer" class="space-y-2.5 max-h-[300px] overflow-y-auto pr-1">
                                            <template x-for="(point, idx) in points" :key="idx">
                                                <div class="flex items-start gap-2.5 p-2 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/60 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-black shrink-0 mt-1 shadow-xs" x-text="idx + 1"></span>
                                                    <div class="flex-1">
                                                        <textarea x-model="points[idx]" 
                                                                  name="revision_points[]" 
                                                                  rows="2" 
                                                                  class="block w-full border-0 p-0 bg-transparent text-slate-800 dark:text-slate-100 text-xs font-medium focus:ring-0 placeholder:text-slate-400 resize-none leading-relaxed" 
                                                                  placeholder="Tuliskan poin catatan revisi (misal: Format tabel di Bab 3 disesuaikan pedoman)..."></textarea>
                                                    </div>
                                                    <button type="button" 
                                                            @click="removePoint(idx)" 
                                                            x-show="points.length > 1 || points[0].trim().length > 0"
                                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition-all shrink-0 mt-0.5" 
                                                            title="Hapus poin ini">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Add Point Button & Status -->
                                        <div class="flex items-center justify-between gap-3 pt-1">
                                            <button type="button" 
                                                    @click="addPoint()" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:hover:bg-indigo-900/60 dark:text-indigo-300 rounded-xl text-xs font-bold border border-indigo-200/80 dark:border-indigo-800 transition-all shadow-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                                <span>Tambah Poin Revisi</span>
                                            </button>
                                            <span class="text-[10px] text-slate-400 font-bold" x-text="points.filter(p => p.trim().length > 0).length + ' poin revisi siap dikirim'"></span>
                                        </div>

                                        <!-- Optional Additional Notes -->
                                        <div>
                                            <input type="text" 
                                                   x-model="additionalNotes" 
                                                   name="additional_notes" 
                                                   placeholder="Catatan tambahan umum (opsional, misal: batas waktu pengumpulan)..." 
                                                   class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-xs font-medium transition-all">
                                        </div>
                                    </div>

                                    <!-- 2. MODE TEKS BEBAS -->
                                    <div x-show="mode === 'text'" class="relative">
                                        <textarea x-ref="noteInput" 
                                                  x-model="notes" 
                                                  rows="3" 
                                                  class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium transition-all" 
                                                  placeholder="Tuliskan catatan revisi bebas..."></textarea>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="relative w-full md:w-64">
                                        <input type="url" name="revision_link" id="revision_link" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-xs font-medium transition-all" placeholder="Link File / Google Drive (Opsional)">
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($myRevision && $myRevision->status !== 'approved')
                                            <button type="button" onclick="if(confirm('Setujui revisi ini sebagai hasil final?')){ document.getElementById('approve-form').submit(); }" class="px-5 py-2.5 bg-emerald-100 text-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all">
                                                Revisi Selesai
                                            </button>
                                        @elseif(!$myRevision)
                                            <form action="{{ route('seminar-examiner.approve-revision-direct', $detail->id) }}" method="POST">
                                                @csrf
                                                @if(isset($actingId))
                                                    <input type="hidden" name="target_examiner_id" value="{{ $actingId }}">
                                                @endif
                                                <button type="submit" class="px-5 py-2.5 bg-emerald-100 text-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all">
                                                    Setujui Tanpa Revisi
                                                </button>
                                            </form>
                                        @endif
                                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all">
                                            Kirim Feedback
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @if($myRevision)
                            <form id="approve-form" action="{{ route('seminar-examiner.approve-revision', $myRevision->id) }}" method="POST" class="hidden">@csrf</form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Inline Document Preview Modal -->
        <div x-show="previewModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto"
             @keydown.escape.window="closePreview()">
            
            <!-- Backdrop -->
            <div x-show="previewModalOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="closePreview()"
                 class="fixed inset-0 bg-slate-950/75 backdrop-blur-md"></div>

            <!-- Modal Window -->
            <div class="min-h-screen px-4 text-center flex items-center justify-center py-6">
                <div x-show="previewModalOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="inline-block w-full max-w-5xl bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700 transform transition-all relative z-10">
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/90 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 truncate" x-text="previewTitle">Preview Dokumen</h4>
                                <p class="text-[11px] text-slate-400 truncate" x-text="previewRawUrl"></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a :href="previewRawUrl" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 transition-all shadow-xs">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                <span>Buka di Tab Baru</span>
                            </a>
                            <button type="button" @click="closePreview()" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/50 dark:hover:text-rose-400 text-slate-400 flex items-center justify-center transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body (Iframe) -->
                    <div class="relative bg-slate-100 dark:bg-slate-950 h-[72vh] w-full flex items-center justify-center">
                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 pointer-events-none text-slate-400">
                            <svg class="w-8 h-8 animate-spin text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="text-xs font-bold uppercase tracking-wider">Memuat Dokumen...</span>
                        </div>
                        
                        <iframe :src="previewUrl" 
                                class="relative z-10 w-full h-full border-0 rounded-b-3xl bg-white"
                                allow="autoplay"
                                sandbox="allow-scripts allow-same-origin allow-popups allow-forms">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

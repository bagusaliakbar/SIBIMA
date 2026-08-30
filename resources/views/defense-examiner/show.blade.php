<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                @php
                    $backUrl = match(request('redirect_to')) {
                        'monitoring-revisions' => route('monitoring.defense-revisions'),
                        'monitoring-scores' => route('monitoring.defense-scores'),
                        default => route('defense-examiner.index'),
                    };
                    $backLabel = match(request('redirect_to')) {
                        'monitoring-revisions' => 'Monitoring Revisi Sidang',
                        'monitoring-scores' => 'Rekap Nilai Sidang',
                        default => 'Tugas Penguji Sidang',
                    };
                @endphp
                <x-breadcrumb :items="[
                    ['label' => $backLabel, 'route' => $backUrl],
                    ['label' => 'Riwayat Revisi', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Detail Mahasiswa & Riwayat Revisi Sidang
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($targetUser))
                        Mode {{ ucfirst(auth()->user()->role) }}: Mengelola revisi atas nama <span class="font-black text-rose-600 dark:text-rose-400 ml-1">{{ $targetUser->name }}</span>
                    @else
                        Pantau progres perbaikan skripsi dan berikan feedback hasil sidang
                    @endif
                </p>
            </div>
            <a href="{{ $backUrl }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div x-data="{
        notes: '',
        previewModalOpen: false,
        previewUrl: '',
        previewRawUrl: '',
        previewTitle: 'Preview Dokumen',
        appendTemplate(text) {
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
            <div class="bg-gradient-to-r from-rose-500/10 via-purple-500/10 to-indigo-500/10 border border-rose-200/60 dark:border-rose-800/40 rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 bg-rose-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                            Mode {{ ucfirst(auth()->user()->role) }}
                        </span>
                        <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">
                            Pilih Dosen Penelaah Yang Dikelola Revisinya
                        </h4>
                    </div>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium italic">
                        *Klik dosen untuk melihat atau memberikan revisi atas nama dosen tersebut
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach($examiners as $ex)
                        @php
                            $isSelected = ($actingId == $ex['user']->id);
                            $rev = $ex['revision'];
                        @endphp
                        <a href="{{ route('defense-examiner.show', ['detail' => $detail->id, 'target_examiner_id' => $ex['user']->id, 'redirect_to' => request('redirect_to')]) }}"
                           class="relative p-4 rounded-xl border transition-all flex flex-col justify-between {{ $isSelected ? 'bg-white dark:bg-slate-800 border-rose-500 shadow-md ring-2 ring-rose-500/20' : 'bg-white/70 dark:bg-slate-800/50 border-slate-200/80 dark:border-slate-700/60 hover:bg-white dark:hover:bg-slate-800 hover:border-slate-300' }}">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[9px] font-black uppercase tracking-wider {{ $isSelected ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                        {{ $ex['role_label'] }}
                                    </span>
                                    @if($rev && $rev->status === 'approved')
                                        <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[8px] font-black rounded-md uppercase">
                                            Revisi Selesai
                                        </span>
                                    @elseif($rev && $rev->status === 'resubmitted')
                                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-[8px] font-black rounded-md uppercase animate-pulse">
                                            Sudah Review
                                        </span>
                                    @elseif($rev)
                                        <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[8px] font-black rounded-md uppercase">
                                            Revisi Dikirim
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 text-[8px] font-black rounded-md uppercase">
                                            Belum Ada
                                        </span>
                                    @endif
                                </div>
                                <div class="font-bold text-xs text-slate-800 dark:text-slate-100 truncate" title="{{ $ex['user']->name }}">
                                    {{ $ex['user']->name }}
                                </div>
                            </div>
                            @if($isSelected)
                                <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-[9px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-tighter">
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
                    <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 dark:bg-rose-900/10 rounded-full -mr-12 -mt-12"></div>
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
                            <div class="pt-4 border-t border-slate-50 dark:border-slate-700">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Jadwal Sidang</span>
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                    <div class="flex items-center text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                        <svg class="w-4 h-4 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div class="flex items-center text-[11px] font-medium text-slate-500">
                                        <svg class="w-4 h-4 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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
                                <svg class="w-4 h-4 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                Diskusi Revisi Sidang
                            </h3>
                            @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($targetUser))
                                <p class="text-[10px] text-slate-400 font-bold mt-0.5">Penelaah: {{ $targetUser->name }}</p>
                            @endif
                        </div>
                        @if($myRevision)
                            @if($myRevision->status === 'approved')
                                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-emerald-200">Revisi Selesai</span>
                            @elseif($myRevision->status === 'resubmitted')
                                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-blue-200 animate-pulse">Sudah Review</span>
                            @else
                                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-amber-200">Revisi Dikirim</span>
                            @endif
                        @endif
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
                                        <div class="p-4 rounded-2xl shadow-sm text-sm font-medium leading-relaxed {{ $isMe ? 'bg-rose-600 text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-tl-none border border-slate-100 dark:border-slate-700' }}">
                                            {!! nl2br(e($msg->message)) !!}
                                            
                                            @if($msg->file_path)
                                                <div class="mt-3 pt-3 border-t {{ $isMe ? 'border-white/10' : 'border-slate-50 dark:border-slate-700' }} flex items-center gap-2 flex-wrap">
                                                    <!-- Modal Preview Button -->
                                                    <button type="button" 
                                                            @click="openPreview('{{ $msg->file_path }}', 'Dokumen dari {{ $isMe ? ($msg->sender?->name ?? 'Penguji') : ($msg->sender?->name ?? $detail->thesis->student->name) }}')"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-xs {{ $isMe ? 'bg-white/20 hover:bg-white/30 text-white' : 'bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800/60' }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        <span>Lihat Preview</span>
                                                    </button>

                                                    <!-- Direct Tab Link -->
                                                    <a href="{{ $msg->file_path }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-bold transition-all {{ $isMe ? 'text-rose-100 hover:text-white underline' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
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
                                        <p class="text-[10px] text-emerald-600/80 dark:text-emerald-400 font-medium">Revisi untuk penelaah ini sudah disetujui (Final).</p>
                                    </div>
                                </div>
                                @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) || $myRevision->examiner_id === auth()->id())
                                    <form action="{{ route('defense-examiner.unapprove-revision', $myRevision->id) }}" method="POST" onsubmit="return confirm('Batalkan status selesai dan buka kembali revisi untuk mahasiswa ini?');">
                                        @csrf
                                        <button type="submit" class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Batalkan Selesai & Buka Revisi
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <form action="{{ route('defense-examiner.store-revision', $detail->id) }}" method="POST" class="space-y-4">
                                @csrf
                                @if(isset($actingId))
                                    <input type="hidden" name="target_examiner_id" value="{{ $actingId }}">
                                @endif
                                @if(request('redirect_to'))
                                    <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                                @endif

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">
                                            Catatan Revisi & Feedback @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && isset($targetUser)) (atas nama {{ $targetUser->name }}) @endif
                                        </label>
                                        <span class="text-[10px] font-bold text-slate-400">💡 Template balasan cepat:</span>
                                    </div>

                                    <!-- Quick Revision Notes Template Chips -->
                                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1.5 scrollbar-none">
                                        <button type="button" 
                                                @click="appendTemplate('Mohon perbaiki format penulisan, margin, tata bahasa, dan pastikan daftar pustaka telah sesuai dengan format pedoman serta terupdate minimal 5 tahun terakhir.')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 dark:bg-slate-700/60 dark:hover:bg-slate-700 dark:text-slate-200 dark:hover:text-rose-400 rounded-xl text-[11px] font-bold border border-slate-200 dark:border-slate-600 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                            <span class="text-rose-500 font-black">+</span>
                                            <span>Format Penulisan & Daftar Pustaka</span>
                                        </button>

                                        <button type="button" 
                                                @click="appendTemplate('Perbaiki metodologi penelitian, perjelas teknik penentuan sampel/populasi, dan jelaskan langkah validasi instrumen secara detail.')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 dark:bg-slate-700/60 dark:hover:bg-slate-700 dark:text-slate-200 dark:hover:text-rose-400 rounded-xl text-[11px] font-bold border border-slate-200 dark:border-slate-600 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                            <span class="text-rose-500 font-black">+</span>
                                            <span>Perbaiki Metodologi & Sampel</span>
                                        </button>

                                        <button type="button" 
                                                @click="appendTemplate('Lengkapi analisis dan pembahasan pada Bab 4, serta bandingkan temuan hasil pengujian dengan penelitian relevan sebelumnya.')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 dark:bg-slate-700/60 dark:hover:bg-slate-700 dark:text-slate-200 dark:hover:text-rose-400 rounded-xl text-[11px] font-bold border border-slate-200 dark:border-slate-600 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                            <span class="text-rose-500 font-black">+</span>
                                            <span>Lengkapi Hasil Pembahasan Bab 4</span>
                                        </button>

                                        <button type="button" 
                                                @click="appendTemplate('Revisi telah diperiksa dan disetujui. Silakan lanjut ke proses pengesahan naskah final / pencetakan dokumen.')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 dark:text-emerald-300 rounded-xl text-[11px] font-bold border border-emerald-200 dark:border-emerald-800 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                            <span class="text-emerald-600 font-black">✓</span>
                                            <span>Revisi Disetujui, Silakan Cetak</span>
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <textarea x-ref="noteInput" 
                                                  x-model="notes" 
                                                  name="revision_notes" 
                                                  rows="3" 
                                                  class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-200 focus:border-rose-500 focus:ring-rose-500 text-sm font-medium transition-all" 
                                                  placeholder="Tuliskan catatan revisi atau klik salah satu template di atas..." 
                                                  required></textarea>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="relative w-full md:w-64">
                                        <input type="url" name="revision_link" id="revision_link" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-200 focus:border-rose-500 focus:ring-rose-500 text-xs font-medium transition-all" placeholder="Link File / Google Drive (Opsional)">
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($myRevision && $myRevision->status !== 'approved')
                                            <button type="button" onclick="if(confirm('Setujui revisi ini sebagai hasil final?')){ document.getElementById('approve-form').submit(); }" class="px-5 py-2.5 bg-emerald-100 text-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all">
                                                Revisi Selesai
                                            </button>
                                        @elseif(!$myRevision)
                                            <form action="{{ route('defense-examiner.approve-revision-direct', $detail->id) }}" method="POST">
                                                @csrf
                                                @if(isset($actingId))
                                                    <input type="hidden" name="target_examiner_id" value="{{ $actingId }}">
                                                @endif
                                                <button type="submit" class="px-5 py-2.5 bg-emerald-100 text-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all">
                                                    Setujui Tanpa Revisi
                                                </button>
                                            </form>
                                        @endif
                                        <button type="submit" class="px-6 py-2.5 bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all">
                                            Kirim Feedback
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @if($myRevision)
                            <form id="approve-form" action="{{ route('defense-examiner.approve-revision', $myRevision->id) }}" method="POST" class="hidden">@csrf</form>
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
                            <div class="w-10 h-10 rounded-2xl bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/20">
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
                            <svg class="w-8 h-8 animate-spin text-rose-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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

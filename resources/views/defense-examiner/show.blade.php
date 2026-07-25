<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Tugas Penguji Sidang', 'route' => route('defense-examiner.index')],
                    ['label' => 'Riwayat Revisi', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Detail Mahasiswa & Riwayat Revisi Sidang
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    Pantau progres perbaikan skripsi dan berikan feedback hasil sidang
                </p>
            </div>
            <a href="{{ route('defense-examiner.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Student Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden sticky top-6">
                <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 dark:bg-rose-900/10 rounded-full -mr-12 -mt-12"></div>
                <div class="relative">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden mb-6 border border-slate-200 dark:border-slate-700 shadow-md">
                        <img src="{{ $detail->thesis->student->avatar_url }}" alt="{{ $detail->thesis->student->name }}" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $detail->thesis->student->name }}</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $detail->thesis->student->identifier }}</p>
                    
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
                                    {{ \Carbon\Carbon::parse($detail->schedule->date)->format('l, d F Y') }}
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
            <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 flex flex-col h-[600px]">
                <div class="p-6 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        Diskusi Revisi Sidang
                    </h3>
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

                <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50/30 dark:bg-slate-900/10">
                    @if($myRevision && $myRevision->messages->count() > 0)
                        @foreach($myRevision->messages as $msg)
                            @php $isMe = $msg->sender_id === Auth::id(); @endphp
                            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[85%] {{ $isMe ? 'order-1 items-end' : 'items-start' }} flex flex-col">
                                    <div class="flex items-center space-x-2 mb-1.5 {{ $isMe ? 'flex-row-reverse space-x-reverse' : '' }}">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $isMe ? 'Anda (Penguji)' : $detail->thesis->student->name }}</span>
                                        <span class="text-[9px] text-slate-400 font-medium">{{ $msg->created_at->format('H:i, d M') }}</span>
                                    </div>
                                    <div class="p-4 rounded-2xl shadow-sm text-sm font-medium leading-relaxed {{ $isMe ? 'bg-rose-600 text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-tl-none border border-slate-100 dark:border-slate-700' }}">
                                        {!! nl2br(e($msg->message)) !!}
                                        
                                        @if($msg->file_path)
                                            <div class="mt-3 pt-3 border-t {{ $isMe ? 'border-white/10' : 'border-slate-50 dark:border-slate-700' }}">
                                                <a href="{{ $msg->file_path }}" target="_blank" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest {{ $isMe ? 'text-rose-100 hover:text-white' : 'text-rose-600 hover:text-rose-700' }}">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                    Buka Tautan
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
                <div class="p-6 border-t border-slate-50 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-b-2xl">
                    @if($myRevision?->status === 'approved')
                        <div class="text-center py-4 bg-emerald-50 dark:bg-emerald-500/5 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                            <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-widest flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Revisi Selesai
                            </p>
                        </div>
                    @else
                        <form action="{{ route('defense-examiner.store-revision', $detail->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="relative">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Catatan Revisi & Feedback</label>
                                <textarea name="revision_notes" rows="3" class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-200 focus:border-rose-500 focus:ring-rose-500 text-sm font-medium transition-all pr-12" placeholder="Tuliskan catatan revisi atau feedback hasil sidang..." required></textarea>
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
                                        {{-- If no revision entry yet, supervisor might want to approve directly --}}
                                        <form action="{{ route('defense-examiner.approve-revision-direct', $detail->id) }}" method="POST">
                                            @csrf
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
</x-app-layout>

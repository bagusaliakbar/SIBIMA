<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :links="[
                    ['name' => 'Revisi Seminar', 'url' => route('student-seminar-revisions.index')],
                    ['name' => 'Diskusi Revisi', 'url' => '#']
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Diskusi & Tindak Lanjut Revisi
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    Pantau riwayat revisi dan tindak lanjuti masukan penguji
                </p>
            </div>
            <a href="{{ route('student-seminar-revisions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Examiner Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden sticky top-6">
                <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-12 -mt-12"></div>
                <div class="relative">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 rounded-xl bg-orange-600 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-orange-200 dark:shadow-none mr-4">
                            {{ substr($revision->examiner->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $revision->examiner->name }}</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Dosen Penguji</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="pt-4 border-t border-slate-50 dark:border-slate-700">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Seminar Anda</span>
                            <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-2">"{{ $revision->detail->thesis->title }}"</p>
                                <div class="flex items-center text-[10px] font-bold text-slate-500">
                                    <svg class="w-3 h-3 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ \Carbon\Carbon::parse($revision->detail->schedule->date)->format('d F Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-50 dark:border-slate-700">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Daftar Revisi Awal</span>
                            @php
                                $firstExaminerMessage = $revision->messages->where('sender_id', $revision->examiner_id)->first();
                            @endphp
                            @if($firstExaminerMessage)
                                <div class="bg-indigo-50/50 dark:bg-indigo-500/5 p-4 rounded-xl border border-indigo-100 dark:border-indigo-500/20 shadow-sm">
                                    <p class="text-[11px] font-medium text-slate-700 dark:text-slate-300 leading-relaxed">
                                        "{!! nl2br(e($firstExaminerMessage->message)) !!}"
                                    </p>
                                    <div class="mt-3 flex items-center gap-3">
                                        @if($firstExaminerMessage->file_path)
                                            <a href="{{ Storage::url($firstExaminerMessage->file_path) }}" target="_blank" class="inline-flex items-center text-[9px] font-black text-indigo-600 uppercase tracking-widest hover:underline">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                File Pendukung
                                            </a>
                                        @endif
                                        <a href="{{ route('student-seminar-revisions.print-pdf', $revision->id) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-indigo-600 text-white rounded-lg text-[8px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            Cetak PDF
                                        </a>
                                    </div>
                                </div>
                            @else
                                <p class="text-[10px] text-slate-400 italic">Belum ada revisi awal.</p>
                            @endif
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
                        <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        History Perbaikan
                    </h3>
                    <div class="flex items-center gap-2">
                        @if($revision->status === 'approved')
                            <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-emerald-200">Revisi Selesai</span>
                        @elseif($revision->status === 'resubmitted')
                            <span class="px-2.5 py-0.5 bg-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-blue-200">Revisi Terkirim</span>
                        @else
                            <span class="px-2.5 py-0.5 bg-amber-100 text-amber-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-amber-200 animate-pulse">Ada Revisi</span>
                        @endif
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50/30 dark:bg-slate-900/10">
                    @foreach($revision->messages as $msg)
                        @php $isMe = $msg->sender_id === Auth::id(); @endphp
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] {{ $isMe ? 'order-1 items-end' : 'items-start' }} flex flex-col">
                                <div class="flex items-center space-x-2 mb-1.5 {{ $isMe ? 'flex-row-reverse space-x-reverse' : '' }}">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $isMe ? 'Anda' : $revision->examiner->name }}</span>
                                    <span class="text-[9px] text-slate-400 font-medium">{{ $msg->created_at->format('H:i, d M') }}</span>
                                </div>
                                <div class="p-4 rounded-2xl shadow-sm text-sm font-medium leading-relaxed {{ $isMe ? 'bg-orange-600 text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-tl-none border border-slate-100 dark:border-slate-700' }}">
                                    {!! nl2br(e($msg->message)) !!}
                                    
                                    @if($msg->file_path)
                                        <div class="mt-3 pt-3 border-t {{ $isMe ? 'border-white/10' : 'border-slate-50 dark:border-slate-700' }}">
                                            <a href="{{ Storage::url($msg->file_path) }}" target="_blank" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest {{ $isMe ? 'text-orange-100 hover:text-white' : 'text-orange-600 hover:text-orange-700' }}">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                Unduh Berkas
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Input Area -->
                <div class="p-6 border-t border-slate-50 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-b-2xl">
                    @if($revision->status === 'approved')
                        <div class="text-center py-4 bg-emerald-50 dark:bg-emerald-500/5 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                            <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-widest flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Revisi Selesai
                            </p>
                        </div>
                    @else
                        <form action="{{ route('student-seminar-revisions.store-reply', $revision->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="relative">
                                <textarea name="student_notes" rows="3" class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-medium transition-all pr-12" placeholder="Tuliskan penjelasan perbaikan atau progres Anda..." required></textarea>
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div x-data="{ fileName: '' }" class="relative">
                                    <input type="file" id="student_file" name="student_file" class="hidden" @change="fileName = $event.target.files[0].name">
                                    <label for="student_file" class="inline-flex items-center text-[10px] font-black text-slate-500 hover:text-orange-600 cursor-pointer uppercase tracking-widest transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        <span x-text="fileName || 'Lampirkan File Perbaikan (Opsional)'"></span>
                                    </label>
                                </div>
                                <button type="submit" class="px-8 py-2.5 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-100 transition-all">
                                    Kirim Tindak Lanjut
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

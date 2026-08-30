<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Revisi Hasil Sidang', 'route' => route('student-defense-revisions.index')],
                    ['label' => 'Diskusi Revisi', 'route' => null]
                ]" />
            </div>
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
                if (this.$refs.studentNoteInput) {
                    this.$refs.studentNoteInput.focus();
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Examiner Info -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden sticky top-6">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 dark:bg-rose-900/10 rounded-full -mr-12 -mt-12"></div>
                    <div class="relative">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 rounded-xl overflow-hidden mr-4 border border-slate-200 dark:border-slate-700 shadow-md">
                                <img src="{{ $revision->examiner->avatar_url }}" alt="{{ $revision->examiner->name }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $revision->examiner->name }}</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Dosen Penguji Sidang</p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="pt-4 border-t border-slate-50 dark:border-slate-700">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Daftar Revisi Awal</span>
                                @php
                                    $firstExaminerMessage = $revision->messages->where('sender_id', $revision->examiner_id)->first();
                                @endphp
                                @if($firstExaminerMessage)
                                    <div class="bg-rose-50/50 dark:bg-rose-500/5 p-4 rounded-xl border border-rose-100 dark:border-rose-500/20 shadow-sm">
                                        <p class="text-[11px] font-medium text-slate-700 dark:text-slate-300 leading-relaxed">
                                            "{!! nl2br(e($firstExaminerMessage->message)) !!}"
                                        </p>
                                        <div class="mt-3 flex items-center gap-3">
                                            <a href="{{ route('student-defense-revisions.print-pdf', $revision->id) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-rose-600 text-white rounded-lg text-[8px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-sm">
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
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 flex flex-col h-[640px]">
                    <div class="p-5 sm:p-6 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between">
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            History Perbaikan Sidang
                        </h3>
                        <div class="flex items-center gap-2">
                            @if($revision->status === 'approved')
                                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-emerald-200">Revisi Selesai</span>
                            @elseif($revision->status === 'resubmitted')
                                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-blue-200">Terkirim</span>
                            @else
                                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-amber-200 animate-pulse">Menunggu Tindakan</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6 bg-slate-50/30 dark:bg-slate-900/10">
                        @foreach($revision->messages as $msg)
                            @php $isMe = $msg->sender_id === Auth::id(); @endphp
                            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[85%] {{ $isMe ? 'order-1 items-end' : 'items-start' }} flex flex-col">
                                    <div class="flex items-center space-x-2 mb-1.5 {{ $isMe ? 'flex-row-reverse space-x-reverse' : '' }}">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $isMe ? 'Anda' : $revision->examiner->name }}</span>
                                        <span class="text-[9px] text-slate-400 font-medium">{{ $msg->created_at->format('H:i, d M') }}</span>
                                    </div>
                                    <div class="p-4 rounded-2xl shadow-sm text-sm font-medium leading-relaxed {{ $isMe ? 'bg-rose-600 text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-tl-none border border-slate-100 dark:border-slate-700' }}">
                                        {!! nl2br(e($msg->message)) !!}
                                        
                                        @if($msg->file_path)
                                            <div class="mt-3 pt-3 border-t {{ $isMe ? 'border-white/10' : 'border-slate-50 dark:border-slate-700' }} flex items-center gap-2 flex-wrap">
                                                <!-- Modal Preview Button -->
                                                <button type="button" 
                                                        @click="openPreview('{{ $msg->file_path }}', 'Dokumen dari {{ $isMe ? 'Anda' : $revision->examiner->name }}')"
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
                    </div>

                    <!-- Input Area -->
                    <div class="p-5 sm:p-6 border-t border-slate-50 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-b-2xl">
                        @if($revision->status === 'approved')
                            <div class="text-center py-4 bg-emerald-50 dark:bg-emerald-500/5 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                                <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-widest flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Revisi Selesai
                                </p>
                            </div>
                        @else
                            <form action="{{ route('student-defense-revisions.store-reply', $revision->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">
                                            Catatan Perbaikan
                                        </label>
                                        <span class="text-[10px] font-bold text-slate-400">💡 Template balasan cepat:</span>
                                    </div>

                                    <!-- Quick Student Response Chips -->
                                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1.5 scrollbar-none">
                                        <button type="button" 
                                                @click="appendTemplate('Yth. Bapak/Ibu Dosen Penguji, seluruh catatan revisi hasil sidang telah diperbaiki sesuai arahan. Mohon kesediaan Bapak/Ibu untuk memeriksa kembali.')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 dark:bg-slate-700/60 dark:hover:bg-slate-700 dark:text-slate-200 dark:hover:text-rose-400 rounded-xl text-[11px] font-bold border border-slate-200 dark:border-slate-600 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                            <span class="text-rose-500 font-black">+</span>
                                            <span>Sudah diperbaiki sesuai arahan</span>
                                        </button>

                                        <button type="button" 
                                                @click="appendTemplate('File dokumen naskah skripsi hasil revisi dan matriks perbaikan telah saya lampirkan pada tautan Google Drive di bawah.')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 dark:bg-slate-700/60 dark:hover:bg-slate-700 dark:text-slate-200 dark:hover:text-rose-400 rounded-xl text-[11px] font-bold border border-slate-200 dark:border-slate-600 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                            <span class="text-rose-500 font-black">+</span>
                                            <span>Naskah revisi terlampir di link</span>
                                        </button>

                                        <button type="button" 
                                                @click="appendTemplate('Terima kasih banyak Bapak/Ibu atas arahan dan masukannya.')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 dark:bg-slate-700/60 dark:hover:bg-slate-700 dark:text-slate-200 dark:hover:text-rose-400 rounded-xl text-[11px] font-bold border border-slate-200 dark:border-slate-600 transition-all shrink-0 hover:scale-[1.02] active:scale-95 shadow-xs">
                                            <span class="text-rose-500 font-black">+</span>
                                            <span>Terima kasih atas arahannya</span>
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <textarea x-ref="studentNoteInput"
                                                  x-model="notes"
                                                  name="student_notes" 
                                                  rows="3" 
                                                  class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-200 focus:border-rose-500 focus:ring-rose-500 text-sm font-medium transition-all" 
                                                  placeholder="Tuliskan penjelasan perbaikan hasil sidang..." 
                                                  required></textarea>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="relative w-full md:w-64">
                                        <input type="url" name="student_link" id="student_link" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-200 focus:border-rose-500 focus:ring-rose-500 text-xs font-medium transition-all" placeholder="Link File / Google Drive (Opsional)">
                                    </div>
                                    <button type="submit" class="px-8 py-2.5 bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 shadow-lg shadow-rose-100 transition-all">
                                        Kirim Tindak Lanjut
                                    </button>
                                </div>
                            </form>
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

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Revisi Hasil Sidang', 'route' => route('student-defense-revisions.index')],
                    ['label' => 'Revisi Room: ' . $revision->examiner->name, 'route' => null]
                ]" />
                <div class="flex items-center gap-2.5 mt-1 flex-wrap">
                    <h2 class="font-black text-xl sm:text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight">
                        Revisi Room Sidang Skripsi
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60">
                        Modern Workspace
                    </span>
                </div>
            </div>

            <!-- Back to Overview & Print PDF -->
            <div class="flex items-center gap-2">
                <a href="{{ route('student-defense-revisions.print-pdf', $revision->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-2xs">
                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-6 4h4"/></svg>
                    <span>Cetak PDF</span>
                </a>
                <a href="{{ route('student-defense-revisions.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        // Prepare all available document options for the student/lecturer to preview on the left panel
        $documents = collect();

        $defenseAppFile = $revision->detail->thesis?->defenseApplication?->file_skripsi;
        if ($defenseAppFile) {
            $documents->push([
                'id' => 'initial_draft',
                'label' => 'Naskah Awal Sidang',
                'subtitle' => 'File pendaftaran sidang skripsi',
                'url' => $defenseAppFile,
                'tag' => 'Draft Awal',
            ]);
        }

        if ($revision->revision_file) {
            $documents->push([
                'id' => 'examiner_file',
                'label' => 'Catatan File Penguji',
                'subtitle' => 'Lampiran dari ' . $revision->examiner->name,
                'url' => $revision->revision_file,
                'tag' => 'Dari Dosen',
            ]);
        }

        if ($revision->student_file) {
            $documents->push([
                'id' => 'student_latest',
                'label' => 'Naskah Revisi Terakhir',
                'subtitle' => 'Unggahan terakhir mahasiswa',
                'url' => $revision->student_file,
                'tag' => 'Revisi Mahasiswa',
            ]);
        }

        // Additional attachments from conversation history
        foreach ($revision->messages->whereNotNull('file_path') as $m) {
            $isSenderMe = $m->sender_id === Auth::id();
            $documents->push([
                'id' => 'msg_' . $m->id,
                'label' => 'Tautan: ' . \Illuminate\Support\Str::limit($m->message, 24),
                'subtitle' => ($isSenderMe ? 'Oleh Anda' : $revision->examiner->name) . ' • ' . $m->created_at->format('d/m H:i'),
                'url' => $m->file_path,
                'tag' => $isSenderMe ? 'Lampiran Anda' : 'Lampiran Dosen',
            ]);
        }

        $documents = $documents->unique('url')->values();

        // Default URL to show in left viewer
        $initialViewerUrl = $defaultPdfUrl ?? ($documents->first()['url'] ?? '');
    @endphp

    <div x-data="{
        mobileActiveTab: 'chat', // 'document' or 'chat' on mobile
        currentDocUrl: '{{ $initialViewerUrl }}',
        currentDocLabel: '{{ $documents->first()['label'] ?? 'Naskah Skripsi' }}',
        notes: '',
        studentLink: '',
        
        formatEmbedUrl(link) {
            if (!link) return '';
            let url = link.trim();
            if (url.includes('drive.google.com/file/d/')) {
                url = url.replace(/\/view(\?.*)?$/, '/preview').replace(/\/edit(\?.*)?$/, '/preview');
                if (!url.includes('/preview')) {
                    url = url.replace(/\/?$/, '/preview');
                }
            } else if (url.includes('drive.google.com/open?id=')) {
                const id = url.split('id=')[1]?.split('&')[0];
                if (id) {
                    url = 'https://drive.google.com/file/d/' + id + '/preview';
                }
            } else if (url.includes('docs.google.com/document/d/') || url.includes('docs.google.com/spreadsheets/d/') || url.includes('docs.google.com/presentation/d/')) {
                url = url.replace(/\/edit(\?.*)?$/, '/preview').replace(/\/view(\?.*)?$/, '/preview');
            }
            return url;
        },

        switchDocument(url, label) {
            this.currentDocUrl = url;
            this.currentDocLabel = label || 'Dokumen Terpilih';
            // On mobile, automatically show the document when a user taps 'Lihat di Viewer'
            if (window.innerWidth < 1024) {
                this.mobileActiveTab = 'document';
            }
        },

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

        async pasteLink() {
            try {
                const text = await navigator.clipboard.readText();
                if (text) {
                    this.studentLink = text.trim();
                }
            } catch (e) {
                alert('Gunakan Ctrl+V untuk menempelkan link.');
            }
        }
    }" class="space-y-4">

        <!-- Top Channel Bar: Multi-Examiner Channel Switcher -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-3 sm:p-4 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0 scrollbar-none w-full sm:w-auto">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 shrink-0 mr-1 hidden sm:inline">
                        Channel Dosen:
                    </span>

                    @foreach($allRevisions as $otherRev)
                        @php
                            $isCurrent = $otherRev->id === $revision->id;
                            $isOtherApproved = $otherRev->isApproved() || $otherRev->status === 'approved';
                            $isOtherResubmitted = !$isOtherApproved && ($otherRev->isResubmitted() || !empty($otherRev->resubmitted_at));

                            $thesisRef = $otherRev->detail->thesis;
                            $roleName = 'Dosen Penguji';
                            if ($thesisRef) {
                                if ($thesisRef->pembimbing1_id === $otherRev->examiner_id) $roleName = 'Pembimbing 1';
                                elseif ($thesisRef->pembimbing2_id === $otherRev->examiner_id) $roleName = 'Pembimbing 2';
                                elseif ($otherRev->detail->examiner1_id === $otherRev->examiner_id) $roleName = 'Penguji 1';
                                elseif ($otherRev->detail->examiner2_id === $otherRev->examiner_id) $roleName = 'Penguji 2';
                            }
                        @endphp

                        <a href="{{ route('student-defense-revisions.show', $otherRev->id) }}"
                           class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 border {{ $isCurrent ? 'bg-rose-600 text-white border-rose-600 shadow-sm shadow-rose-600/20' : 'bg-slate-50 dark:bg-slate-900/40 text-slate-700 dark:text-slate-200 border-slate-200/80 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="w-6 h-6 rounded-lg overflow-hidden shrink-0 border {{ $isCurrent ? 'border-white/30' : 'border-slate-200 dark:border-slate-700' }}">
                                <img src="{{ $otherRev->examiner->avatar_url }}" alt="{{ $otherRev->examiner->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="text-left">
                                <span class="block text-[10px] {{ $isCurrent ? 'text-rose-100' : 'text-slate-400' }} uppercase leading-none font-black">{{ $roleName }}</span>
                                <span class="block truncate max-w-[120px] sm:max-w-[150px] leading-tight mt-0.5">{{ $otherRev->examiner->name }}</span>
                            </div>
                            <span class="ml-1">
                                @if($isOtherApproved)
                                    <span class="inline-flex items-center text-[10px] font-black {{ $isCurrent ? 'text-emerald-200' : 'text-emerald-600 dark:text-emerald-400' }}" title="Revisi Disetujui (ACC)">✓✓</span>
                                @elseif($isOtherResubmitted)
                                    <span class="inline-flex items-center text-[10px] font-black {{ $isCurrent ? 'text-sky-200' : 'text-sky-600 dark:text-sky-400' }}" title="Menunggu Review Dosen">✓</span>
                                @else
                                    <span class="w-2 h-2 rounded-full {{ $isCurrent ? 'bg-amber-300' : 'bg-amber-500' }}" title="Perlu Revisi"></span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>

                <!-- Mobile View Switcher (Visible only on mobile/tablet) -->
                <div class="flex lg:hidden items-center p-1 bg-slate-100 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 w-full sm:w-auto">
                    <button type="button" 
                            @click="mobileActiveTab = 'document'" 
                            :class="mobileActiveTab === 'document' ? 'bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 shadow-xs' : 'text-slate-500 dark:text-slate-400'"
                            class="flex-1 sm:flex-initial px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Dokumen Naskah</span>
                    </button>
                    <button type="button" 
                            @click="mobileActiveTab = 'chat'" 
                            :class="mobileActiveTab === 'chat' ? 'bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 shadow-xs' : 'text-slate-500 dark:text-slate-400'"
                            class="flex-1 sm:flex-initial px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Chat Timeline</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2-COLUMNS SPLIT WORKSPACE: PDF Viewer on Left (7 cols) + Revision Chat on Right (5 cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">

            <!-- ========================================== -->
            <!-- SISI KIRI (7/12): Live PDF & Manuscript Viewer -->
            <!-- ========================================== -->
            <div :class="mobileActiveTab === 'document' ? 'block' : 'hidden lg:block'"
                 class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 shadow-sm flex flex-col h-[650px] lg:h-[calc(100vh-14rem)] min-h-[580px] overflow-hidden">
                
                <!-- Viewer Header & Toolstrip -->
                <div class="p-3.5 sm:p-4 border-b border-slate-100 dark:border-slate-700/70 bg-slate-50/70 dark:bg-slate-900/40 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" x-text="currentDocLabel">
                                    Naskah Skripsi
                                </span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300">
                                    Viewer
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-400 truncate max-w-xs" x-text="currentDocUrl || 'Belum ada tautan dokumen terpilih'"></p>
                        </div>
                    </div>

                    <!-- Document Selector & Action Tools -->
                    <div class="flex items-center gap-2 shrink-0">
                        @if($documents->count() > 1)
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" 
                                        @click="open = !open" 
                                        @click.away="open = false" 
                                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 flex items-center gap-1.5 shadow-2xs">
                                    <span>Pilih Berkas ({{ $documents->count() }})</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute right-0 mt-1 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-2 z-30 space-y-1">
                                    @foreach($documents as $doc)
                                        <button type="button" 
                                                @click="switchDocument('{{ $doc['url'] }}', '{{ $doc['label'] }}'); open = false;"
                                                class="w-full text-left p-2 rounded-xl text-xs hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors flex flex-col">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $doc['label'] }}</span>
                                                <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-black">{{ $doc['tag'] }}</span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 mt-0.5 truncate">{{ $doc['subtitle'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <template x-if="currentDocUrl">
                            <div class="flex items-center gap-1.5">
                                <a :href="currentDocUrl" 
                                   target="_blank" 
                                   title="Buka dokumen di tab baru / Download" 
                                   class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all shadow-2xs inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span>Tab Baru</span>
                                </a>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Iframe Viewer Body -->
                <div class="flex-1 relative bg-slate-100 dark:bg-slate-950 flex items-center justify-center overflow-hidden">
                    <template x-if="currentDocUrl">
                        <div class="w-full h-full relative">
                            <!-- Background Loading Indicator -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 pointer-events-none text-slate-400">
                                <svg class="w-7 h-7 animate-spin text-rose-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="text-xs font-bold uppercase tracking-wider">Memuat Dokumen Naskah...</span>
                            </div>

                            <iframe :src="formatEmbedUrl(currentDocUrl)" 
                                    class="relative z-10 w-full h-full border-0 bg-white"
                                    allow="autoplay"
                                    sandbox="allow-scripts allow-same-origin allow-popups allow-forms">
                            </iframe>
                        </div>
                    </template>

                    <template x-if="!currentDocUrl">
                        <div class="p-8 text-center max-w-sm space-y-3">
                            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-200/60 dark:bg-slate-800 text-slate-400 flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum Ada Naskah Ditampilkan</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Pilih dokumen pada menu dropdown di atas atau klik tautan file pada chat di sebelah kanan untuk melihat naskah langsung di panel ini.
                            </p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SISI KANAN (5/12): Timeline Chat Revisi & Input -->
            <!-- ========================================== -->
            <div :class="mobileActiveTab === 'chat' ? 'block' : 'hidden lg:block'"
                 class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 shadow-sm flex flex-col h-[650px] lg:h-[calc(100vh-14rem)] min-h-[580px] overflow-hidden">
                
                <!-- Chat Header: Examiner Details & Status Badge -->
                <div class="p-4 border-b border-slate-100 dark:border-slate-700/70 bg-white dark:bg-slate-800 flex items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0">
                            <img src="{{ $revision->examiner->avatar_url }}" alt="{{ $revision->examiner->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs sm:text-sm font-black text-slate-800 dark:text-slate-100 truncate">
                                {{ $revision->examiner->name }}
                            </h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">
                                Dewan Penguji Sidang Skripsi
                            </p>
                        </div>
                    </div>

                    <!-- Status Indicator Badge -->
                    <div class="shrink-0">
                        @if($revision->isApproved())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 text-xs font-black uppercase tracking-wider rounded-xl border border-emerald-200 dark:border-emerald-800">
                                <span>✓✓ Disetujui (ACC)</span>
                            </span>
                        @elseif($revision->isResubmitted())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 text-xs font-black uppercase tracking-wider rounded-xl border border-sky-200 dark:border-sky-800">
                                <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                                <span>Menunggu Review</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 text-xs font-black uppercase tracking-wider rounded-xl border border-amber-200 dark:border-amber-800">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Perlu Revisi</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Messages Stream (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 bg-slate-50/50 dark:bg-slate-900/30">
                    
                    <!-- Pinned Initial Revision Notes from Lecturer (if any) -->
                    @php
                        $firstExaminerMsg = $revision->messages->where('sender_id', $revision->examiner_id)->first();
                    @endphp
                    @if($firstExaminerMsg)
                        <div class="p-4 rounded-2xl bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-800/50 space-y-2">
                            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-wider text-rose-700 dark:text-rose-300">
                                <span class="flex items-center gap-1.5">
                                    <span>📌</span>
                                    <span>Catatan Revisi Awal Hasil Sidang</span>
                                </span>
                                <span>{{ $firstExaminerMsg->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed font-medium">
                                <x-revision-message-content :message="$firstExaminerMsg->message" :is-me="false" theme="rose" />
                            </div>
                            @if($revision->revision_file)
                                <div class="pt-2 border-t border-rose-200/50 dark:border-rose-800/40 flex items-center justify-between gap-2">
                                    <span class="text-[10px] text-rose-600 dark:text-rose-400 font-semibold truncate">
                                        📎 Terdapat berkas catatan dari penguji
                                    </span>
                                    <button type="button" 
                                            @click="switchDocument('{{ $revision->revision_file }}', 'Catatan Penguji')" 
                                            class="text-[10px] font-black text-rose-700 dark:text-rose-300 hover:underline shrink-0 inline-flex items-center gap-1">
                                        <span>Lihat di Kolom Kiri ➔</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Timeline of Messages -->
                    @forelse($revision->messages as $msg)
                        @php 
                            $isMe = $msg->sender_id === Auth::id(); 
                            $isRead = !empty($msg->read_at);
                        @endphp
                        
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[88%] flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                                
                                <!-- Sender label & timestamp -->
                                <div class="flex items-center gap-2 mb-1 text-[10px] text-slate-400 {{ $isMe ? 'flex-row-reverse' : '' }}">
                                    <span class="font-bold">{{ $isMe ? 'Anda' : $revision->examiner->name }}</span>
                                    <span>•</span>
                                    <span>{{ $msg->created_at->format('H:i') }}</span>
                                </div>

                                <!-- Message Bubble -->
                                <div class="p-3.5 sm:p-4 rounded-2xl text-xs leading-relaxed shadow-2xs {{ $isMe ? 'bg-slate-900 text-white dark:bg-rose-600 rounded-tr-none' : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700 rounded-tl-none' }}">
                                    <x-revision-message-content :message="$msg->message" :is-me="$isMe" theme="{{ $isMe ? 'rose' : 'rose' }}" />

                                    <!-- Attachment in Message -->
                                    @if($msg->file_path)
                                        <div class="mt-2.5 pt-2.5 border-t {{ $isMe ? 'border-white/15' : 'border-slate-100 dark:border-slate-700' }} flex items-center justify-between gap-2 flex-wrap">
                                            <span class="text-[10px] {{ $isMe ? 'text-slate-300' : 'text-slate-400' }} flex items-center gap-1 truncate max-w-[160px]">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                <span>Lampiran Berkas</span>
                                            </span>
                                            
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <button type="button" 
                                                        @click="switchDocument('{{ $msg->file_path }}', 'Lampiran Pesan ({{ $msg->created_at->format('H:i') }})')"
                                                        class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $isMe ? 'bg-white/20 hover:bg-white/30 text-white' : 'bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300' }} transition-colors cursor-pointer">
                                                    👁️ Preview Kiri
                                                </button>
                                                <a href="{{ $msg->file_path }}" target="_blank" class="text-[10px] {{ $isMe ? 'text-white/80 hover:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400' }} hover:underline">
                                                    ↗ Tab
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Read & Sent Status Indicators (For messages sent by student) -->
                                @if($isMe)
                                    <div class="flex items-center gap-1.5 mt-1 text-[10px] font-bold">
                                        @if($revision->isApproved())
                                            <!-- Disetujui (Centang 2 Hijau) -->
                                            <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400" title="Catatan telah disetujui (ACC)">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7m-14 4l4 4L19 7"/></svg>
                                                <span>Disetujui</span>
                                            </span>
                                        @elseif($isRead)
                                            <!-- Dibaca oleh Dosen (Centang 2 Biru) -->
                                            <span class="inline-flex items-center gap-1 text-sky-600 dark:text-sky-400" title="Telah dibaca dosen pada {{ $msg->read_at->locale('id')->translatedFormat('d M, H:i') }}">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7m-14 4l4 4L19 7"/></svg>
                                                <span>Dibaca {{ $msg->read_at->format('H:i') }}</span>
                                            </span>
                                        @else
                                            <!-- Terkirim (Centang 1 Abu-abu) -->
                                            <span class="inline-flex items-center gap-1 text-slate-400 dark:text-slate-500" title="Terkirim, menunggu dibaca dosen">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                <span>Terkirim</span>
                                            </span>
                                        @endif
                                    </div>
                                @endif

                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-400">
                            <p class="text-xs">Belum ada riwayat percakapan revisi.</p>
                        </div>
                    @endforelse

                </div>

                <!-- Chat Footer / Input Form Area -->
                <div class="p-4 border-t border-slate-100 dark:border-slate-700/70 bg-white dark:bg-slate-800 shrink-0">
                    @if($revision->isApproved())
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-2xl border border-emerald-200 dark:border-emerald-800/60 text-center space-y-1">
                            <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Revisi Naskah Telah Disetujui (ACC)</span>
                            </span>
                            <p class="text-[11px] text-emerald-600 dark:text-emerald-400">
                                Dosen penguji telah memberikan persetujuan resmi. Anda tidak perlu mengirimkan revisi tambahan.
                            </p>
                        </div>
                    @else
                        <form action="{{ route('student-defense-revisions.store-reply', $revision->id) }}" method="POST" class="space-y-3">
                            @csrf

                            <!-- Quick Response Chips -->
                            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                                <button type="button" 
                                        @click="appendTemplate('Yth. Bapak/Ibu Dosen Penguji, seluruh catatan perbaikan revisi sidang telah diperbaiki sesuai arahan. Mohon kesediaannya untuk memeriksa kembali.')"
                                        class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-600 transition-all shrink-0 active:scale-95">
                                    + Selesai diperbaiki
                                </button>
                                <button type="button" 
                                        @click="appendTemplate('File dokumen naskah skripsi hasil revisi dan matriks perbaikan telah saya lampirkan pada tautan Google Drive.')"
                                        class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-600 transition-all shrink-0 active:scale-95">
                                    + Naskah & matriks terlampir
                                </button>
                                <button type="button" 
                                        @click="appendTemplate('Terima kasih banyak Bapak/Ibu atas arahan dan bimbingan perbaikannya.')"
                                        class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-bold border border-slate-200 dark:border-slate-600 transition-all shrink-0 active:scale-95">
                                    + Terima kasih
                                </button>
                            </div>

                            <!-- Textarea Input -->
                            <div class="relative">
                                <textarea x-ref="noteInput"
                                          x-model="notes"
                                          name="student_notes" 
                                          rows="2" 
                                          placeholder="Tuliskan keterangan perbaikan hasil sidang kepada dosen..." 
                                          required 
                                          class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-rose-500 focus:border-rose-500 shadow-2xs p-3 leading-relaxed resize-none"></textarea>
                            </div>

                            <!-- Link Input & Send Button -->
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-2.5">
                                <div class="relative w-full sm:flex-1 flex items-center gap-1.5">
                                    <input type="url" 
                                           name="student_link" 
                                           x-model="studentLink" 
                                           placeholder="Tautan Google Drive naskah baru (Opsional)" 
                                           class="w-full text-xs font-mono rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-rose-500 focus:border-rose-500 py-1.5 px-3">
                                    <button type="button" 
                                            @click="pasteLink()" 
                                            class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-rose-600 rounded-xl text-[10px] font-bold border border-slate-200 dark:border-slate-600 shrink-0 cursor-pointer"
                                            title="Tempel dari Clipboard">
                                        📋 Tempel
                                    </button>
                                </div>

                                <button type="submit" 
                                        class="w-full sm:w-auto px-5 py-2 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm shadow-rose-600/25 shrink-0 flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    <span>Kirim Tindak Lanjut</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

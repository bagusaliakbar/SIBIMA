<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Bimbingan', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div x-data="{ 
            permissionModalOpen: false, 
            activeSessionId: null, 
            activeSessionTopic: '', 
            permissionReason: '',
            openPermissionModal(session) {
                this.activeSessionId = session.id;
                this.activeSessionTopic = session.topic;
                this.permissionReason = session.student_attendance_reason || '';
                this.permissionModalOpen = true;
            },
            cancelModalOpen: false,
            cancelData: {
                id: null,
                topic: '',
                dosen_name: '',
                scheduled_date: '',
                scheduled_time: '',
                reason: '',
            },
            openCancelModal(session) {
                this.cancelData = {
                    id: session.id,
                    topic: session.topic || '-',
                    dosen_name: session.dosen_name || 'Dosen Pembimbing',
                    scheduled_date: session.scheduled_date || '-',
                    scheduled_time: session.scheduled_time || '-',
                    reason: '',
                };
                this.cancelModalOpen = true;
            },
            openCancelModalFromEl(el) {
                this.openCancelModal({
                    id: el.getAttribute('data-session-id'),
                    topic: el.getAttribute('data-topic'),
                    dosen_name: el.getAttribute('data-dosen-name'),
                    scheduled_date: el.getAttribute('data-scheduled-date'),
                    scheduled_time: el.getAttribute('data-scheduled-time'),
                });
            }
        }" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
            @if(session('success'))
                <div class="m-6 mb-0 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-sm flex items-center border border-emerald-200 dark:border-emerald-500/20">
                    <svg class="w-4 h-4 mr-3 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="m-6 mb-0 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 text-sm flex items-start border border-red-200 dark:border-red-500/20">
                    <svg class="w-4 h-4 mr-3 text-red-500 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            @php
                $pendingConfirmations = $sessions->filter(fn($s) => in_array($s->status, ['pending', 'approved']) && $s->student_attendance_status === 'pending');
            @endphp

            @if($pendingConfirmations->isNotEmpty())
                <div class="m-6 mb-0 p-4 rounded-2xl bg-amber-500/10 dark:bg-amber-500/15 border border-amber-500/30 dark:border-amber-500/30 flex items-center justify-between gap-3 shadow-2xs transition-colors">
                    <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                        <div class="w-10 h-10 min-w-[2.5rem] min-h-[2.5rem] max-w-[2.5rem] max-h-[2.5rem] aspect-square rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30 flex items-center justify-center font-black shrink-0 shadow-2xs">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-amber-900 dark:text-amber-300 uppercase tracking-tight text-xs">
                                Konfirmasi Kehadiran Diperlukan ({{ $pendingConfirmations->count() }} Sesi)
                            </h4>
                            <p class="text-[11px] text-amber-800 dark:text-amber-200/90 font-medium mt-0.5 leading-relaxed">
                                Dosen pembimbing telah menjadwalkan bimbingan. Mohon konfirmasi kehadiran Anda (<span class="font-bold text-amber-950 dark:text-white">Akan Hadir</span> atau <span class="font-bold text-amber-950 dark:text-white">Izin</span> dengan alasan).
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $thesis = Auth::user()->thesis;
            @endphp

            @if($thesis)
                @php
                    $countP1 = $thesis->getCompletedMentoringCountForDosen($thesis->pembimbing1_id);
                    $countP2 = $thesis->getCompletedMentoringCountForDosen($thesis->pembimbing2_id);
                    
                    $upProgressP1 = min(($countP1 / 4) * 100, 100);
                    $upProgressP2 = min(($countP2 / 4) * 100, 100);
                    
                    $sidangProgressP1 = min(($countP1 / 8) * 100, 100);
                    $sidangProgressP2 = min(($countP2 / 8) * 100, 100);
                @endphp
                
                {{-- Academic Progress Section --}}
                <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30" id="progress-card">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Progress Bimbingan
                        </h3>
                        <div class="flex items-center gap-3">
                            <button onclick="captureProgress()" class="inline-flex items-center px-2.5 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:bg-orange-50 dark:hover:bg-orange-500/10 hover:text-orange-600 dark:hover:text-orange-400 transition-all shadow-sm">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                DOWNLOAD BUKTI ACC
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mb-6 p-3 bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-orange-500"></div>
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Progres Sesi Bimbingan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sudah di-ACC Dosen Pembimbing</span>
                        </div>
                        <div class="ml-auto text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest hidden lg:block">
                            Syarat: 4x (UP) & 8x (Sidang) Per Dosen
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {{-- Seminar UP Progress --}}
                        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors">
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Seminar Usulan Penelitian (UP)</span>
                                @if($thesis->isAccUpFinal())
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200 dark:border-emerald-500/20">SUDAH DIACC KEDUA PEMBIMBING</span>
                                @endif
                            </div>
                            
                            <div class="space-y-5">
                                {{-- P1 UP --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">Pembimbing 1: {{ $thesis->pembimbing1->name ?? '-' }}</span>
                                        <span class="text-[10px] font-bold {{ $countP1 >= 4 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $countP1 }}/4</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $upProgressP1 }}%"></div>
                                        </div>
                                        <div class="shrink-0">
                                            @if($thesis->acc_up_p1)
                                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            @else
                                                <div class="w-4 h-4 rounded-full border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- P2 UP --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">Pembimbing 2: {{ $thesis->pembimbing2->name ?? '-' }}</span>
                                        <span class="text-[10px] font-bold {{ $countP2 >= 4 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $countP2 }}/4</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $upProgressP2 }}%"></div>
                                        </div>
                                        <div class="shrink-0">
                                            @if($thesis->acc_up_p2)
                                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            @else
                                                <div class="w-4 h-4 rounded-full border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sidang Akhir Progress --}}
                        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors">
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Sidang Akhir Skripsi</span>
                                @if($thesis->isAccSidangFinal())
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200 dark:border-emerald-500/20">SUDAH DIACC KEDUA PEMBIMBING</span>
                                @endif
                            </div>

                            <div class="space-y-5">
                                {{-- P1 Sidang --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">Pembimbing 1: {{ $thesis->pembimbing1->name ?? '-' }}</span>
                                        <span class="text-[10px] font-bold {{ $countP1 >= 8 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $countP1 }}/8</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $sidangProgressP1 }}%"></div>
                                        </div>
                                        <div class="shrink-0">
                                            @if($thesis->acc_sidang_p1)
                                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            @else
                                                <div class="w-4 h-4 rounded-full border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- P2 Sidang --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">Pembimbing 2: {{ $thesis->pembimbing2->name ?? '-' }}</span>
                                        <span class="text-[10px] font-bold {{ $countP2 >= 8 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $countP2 }}/8</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $sidangProgressP2 }}%"></div>
                                        </div>
                                        <div class="shrink-0">
                                            @if($thesis->acc_sidang_p2)
                                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            @else
                                                <div class="w-4 h-4 rounded-full border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Riwayat Bimbingan</h3>
                
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <!-- Search Input -->
                    <form action="{{ route('mentoring-sessions.index') }}" method="GET" class="relative w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Topik Pembahasan" class="block w-full sm:w-64 pl-10 pr-10 py-1.5 border border-slate-200 dark:border-slate-700 rounded-md leading-5 bg-slate-50 dark:bg-slate-900 placeholder-slate-400 dark:placeholder-slate-500 text-slate-900 dark:text-slate-100 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition-all">
                        @if(isset($search) && $search !== '')
                            <a href="{{ route('mentoring-sessions.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('mentoring-sessions.create') }}" class="px-3 py-1.5 bg-orange-600 text-white text-xs font-medium rounded hover:bg-orange-700 transition-colors shadow-sm whitespace-nowrap">+ Ajukan Bimbingan</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">TANGGAL & WAKTU</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">TOPIK PEMBAHASAN</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">DOSEN PEMBIMBING</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">DOKUMEN</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">KONFIRMASI KEHADIRAN</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">CATATAN (LOGBOOK)</th>
                            <th class="py-3 px-5 font-semibold text-xs tracking-wider whitespace-nowrap">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group align-top">
                                <td class="py-4 px-5 text-slate-800 dark:text-slate-200 font-medium whitespace-nowrap">
                                    {{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }} <br>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-normal">{{ $session->scheduled_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="py-4 px-5 text-slate-700 dark:text-slate-300">
                                    <div class="font-medium text-slate-800 dark:text-slate-100 leading-snug">{{ $session->topic }}</div>
                                    <div class="mt-1.5 flex items-center text-[11px]">
                                        @if($session->type === 'online')
                                            @php 
                                                $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                                                $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                                                $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                                            @endphp
                                            @if($session->location)
                                                <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $isMeet ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/60' : ($isZoom ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/60' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100') }} transition-all shadow-2xs font-bold text-[10px] uppercase tracking-wider">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                    <span>{{ $isMeet ? '🎥 Gabung Google Meet' : ($isZoom ? '📹 Gabung Zoom' : 'Buka Meeting') }}</span>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-semibold border border-blue-100 dark:border-blue-500/20">Online</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold mr-2 border border-slate-200 dark:border-slate-700">Offline</span>
                                            @if($session->location)
                                                <span class="text-slate-500 truncate max-w-[130px] inline-block" title="{{ $session->location }}">{{ $session->location }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-600 shadow-sm bg-slate-50 dark:bg-slate-800">
                                            @if($session->dosen)
                                                <img src="{{ $session->dosen->avatar_url }}" alt="{{ $session->dosen->name }}" class="w-full h-full object-cover">
                                            @else
                                                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><rect width='100%25' height='100%25' fill='%23f1f5f9'/><path d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z' fill='%2394a3b8'/></svg>" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-xs text-slate-800 dark:text-slate-200 truncate max-w-[130px]">{{ $session->dosen->name ?? '-' }}</div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 uppercase font-bold tracking-tighter">
                                                @if($session->dosen_id === $session->thesis->pembimbing1_id)
                                                    Pembimbing 1
                                                @elseif($session->dosen_id === $session->thesis->pembimbing2_id)
                                                    Pembimbing 2
                                                @else
                                                    Dosen
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- === DOKUMEN COLUMN === --}}
                                <td class="py-4 px-5 min-w-[190px]">
                                    @if(in_array($session->status, ['pending', 'approved']))
                                        <div x-data="{ uploading: false }">
                                            @if($session->document_path)
                                                {{-- Dokumen sudah ada --}}
                                                <div class="flex items-center gap-2 mb-2 p-2 bg-orange-50 dark:bg-orange-500/10 border border-orange-100 dark:border-orange-500/20 rounded-md">
                                                    <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                    <a href="{{ $session->document_path }}" target="_blank" class="text-xs text-orange-700 dark:text-orange-400 font-semibold hover:underline truncate max-w-[110px]" title="{{ $session->document_original_name }}">
                                                        {{ $session->document_original_name }}
                                                    </a>
                                                </div>
                                                <div class="flex gap-1">
                                                    {{-- Ganti dokumen --}}
                                                    <button type="button" @click="uploading = !uploading" class="flex items-center gap-1 px-2 py-1 text-[10px] font-semibold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                        Ganti
                                                    </button>
                                                    {{-- Hapus dokumen --}}
                                                    <form action="{{ route('mentoring-sessions.delete-document', $session->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('Yakin ingin menghapus dokumen ini?');" class="flex items-center gap-1 px-2 py-1 text-[10px] font-semibold text-red-600 dark:text-red-400 bg-white dark:bg-slate-700 border border-red-200 dark:border-red-900 rounded hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors relative z-10">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                {{-- Belum ada dokumen --}}
                                                <button type="button" @click="uploading = !uploading" class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-orange-700 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 rounded-md hover:bg-orange-100 dark:hover:bg-orange-500/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                    Lampirkan Dokumen
                                                </button>
                                            @endif

                                            {{-- Upload form (collapsible) --}}
                                            <div x-show="uploading" x-cloak x-transition class="mt-2">
                                                <form action="{{ route('mentoring-sessions.upload-document', $session->id) }}" method="POST" class="flex flex-col gap-1.5">
                                                    @csrf
                                                    <label class="block">
                                                        <input type="url" name="document" placeholder="https://drive.google.com/..." required
                                                            class="block w-full text-xs text-slate-600 dark:text-slate-400 rounded border-slate-300 dark:border-slate-700 focus:border-orange-500 focus:ring-orange-500 bg-white dark:bg-slate-900 shadow-sm py-1.5 px-2">
                                                    </label>
                                                    <p class="text-[10px] text-slate-400">Masukkan Dokumen dari Google Drive.</p>
                                                    <div class="flex gap-1">
                                                        <button type="submit" class="px-2.5 py-1 bg-orange-600 text-white text-[10px] font-bold rounded hover:bg-orange-700 transition-colors">Simpan</button>
                                                        <button type="button" @click="uploading = false" class="px-2.5 py-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 text-[10px] font-semibold rounded hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($session->document_path)
                                        {{-- Sesi selesai/ditolak tapi ada dokumen: tampilkan read-only --}}
                                        <a href="{{ $session->document_path }}" target="_blank" class="flex items-center gap-1.5 text-xs text-slate-600 hover:text-orange-700 transition-colors">
                                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            <span class="truncate max-w-[130px]">{{ $session->document_original_name }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-300 text-xs italic">—</span>
                                    @endif
                                </td>
                                {{-- === END DOKUMEN COLUMN === --}}

                                {{-- === KONFIRMASI KEHADIRAN MAHASISWA COLUMN === --}}
                                <td class="py-4 px-5 min-w-[200px]">
                                    @if($session->status === 'completed')
                                        @if($session->is_absent)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60">
                                                Tidak Hadir (Absen)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                Telah Hadir
                                            </span>
                                        @endif
                                    @elseif($session->status === 'rejected')
                                        <span class="text-slate-400 dark:text-slate-500 text-xs italic">-</span>
                                    @else
                                        @if($session->student_attendance_status === 'pending')
                                            <div class="space-y-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700/80 text-[10px] font-black uppercase tracking-wider animate-pulse">
                                                    Belum Konfirmasi
                                                </span>
                                                <div class="flex items-center gap-1.5">
                                                    <form action="{{ route('mentoring-sessions.confirm-attendance', $session->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="attending">
                                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-xs flex items-center gap-1 cursor-pointer" title="Konfirmasi Akan Hadir">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            <span>Akan Hadir</span>
                                                        </button>
                                                    </form>
                                                    <button type="button" 
                                                            @click="openPermissionModal({{ json_encode($session) }})" 
                                                            class="px-2.5 py-1 bg-slate-100 hover:bg-rose-50 dark:bg-slate-800 dark:hover:bg-rose-950/40 text-slate-700 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-xs flex items-center gap-1 cursor-pointer" title="Ajukan Izin dengan Alasan">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <span>Izin</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @elseif($session->student_attendance_status === 'attending')
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700/80 text-[10px] font-black uppercase tracking-wider">
                                                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Akan Hadir</span>
                                                    </span>
                                                </div>
                                                @if($session->student_confirmed_at)
                                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium">Dikonfirmasi: {{ $session->student_confirmed_at->format('d/m H:i') }} WIB</p>
                                                @endif
                                                <button type="button" 
                                                        @click="openPermissionModal({{ json_encode($session) }})" 
                                                        class="text-[10px] font-bold text-slate-400 hover:text-rose-600 dark:text-slate-500 dark:hover:text-rose-400 underline decoration-dotted transition-colors cursor-pointer">
                                                    Ubah ke Izin
                                                </button>
                                            </div>
                                        @elseif($session->student_attendance_status === 'permission')
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700/80 text-[10px] font-black uppercase tracking-wider">
                                                        <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"></path></svg>
                                                        <span>Izin / Berhalangan</span>
                                                    </span>
                                                </div>
                                                @if($session->student_attendance_reason)
                                                    <p class="text-[10px] text-slate-600 dark:text-slate-400 italic line-clamp-2" title="{{ $session->student_attendance_reason }}">
                                                        "{{ $session->student_attendance_reason }}"
                                                    </p>
                                                @endif
                                                <form action="{{ route('mentoring-sessions.confirm-attendance', $session->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <input type="hidden" name="status" value="attending">
                                                    <button type="submit" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 underline decoration-dotted transition-colors cursor-pointer">
                                                        Ubah Siap Hadir
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                <td class="py-4 px-5 max-w-sm">
                                    @if($session->feedback)
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-0.5">Catatan Pembimbing:</div>
                                        <div class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed">{{ $session->feedback }}</div>
                                    @elseif($session->notes)
                                        <div class="text-[10px] uppercase tracking-wider font-bold text-slate-400 dark:text-slate-500 mb-0.5">Catatan Pengajuan:</div>
                                        <div class="text-slate-600 dark:text-slate-400 text-xs italic leading-relaxed">{{ $session->notes }}</div>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-600">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    @if($session->is_absent)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                            Tidak Hadir
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                            {{ $session->status === 'pending' ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : '' }}
                                            {{ $session->status === 'approved' ? 'bg-orange-600 text-white' : '' }}
                                            {{ $session->status === 'rejected' ? 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800' : '' }}
                                            {{ $session->status === 'completed' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900' : '' }}
                                        ">
                                            {{ $session->status === 'completed' ? 'Hadir' : $session->status }}
                                        </span>

                                        @if(in_array($session->status, ['pending', 'approved']))
                                            <div class="mt-1.5">
                                                <button type="button" 
                                                        @click="openCancelModalFromEl($el)"
                                                        data-session-id="{{ $session->id }}"
                                                        data-topic="{{ $session->topic }}"
                                                        data-dosen-name="{{ $session->dosen?->name ?? 'Dosen Pembimbing' }}"
                                                        data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                        data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                        class="text-[10px] font-bold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 hover:underline cursor-pointer flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    <span>Batalkan</span>
                                                </button>
                                            </div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500 border-b border-slate-200 dark:border-slate-700">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="font-medium text-sm text-slate-600 dark:text-slate-400">Belum ada riwayat bimbingan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($sessions->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $sessions->links() }}
                </div>
            @endif

            <!-- Modal Izin Bimbingan -->
            <template x-teleport="body">
                <div x-show="permissionModalOpen" 
                     class="fixed inset-0 overflow-y-auto text-left" 
                     style="z-index: 99999 !important;" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="permissionModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative" 
                             style="z-index: 100000 !important;">
                            <form :action="'/mentoring-sessions/' + activeSessionId + '/confirm-attendance'" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="permission">
                                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Formulir Izin Bimbingan</h3>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Konfirmasi bahwa Anda berhalangan hadir</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="permissionModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold p-1 cursor-pointer">&times;</button>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700">
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Topik Sesi Bimbingan</label>
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="activeSessionTopic"></p>
                                    </div>
                                    <div>
                                        <label for="permission_reason" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1.5">
                                            Alasan / Keterangan Berhalangan Hadir <span class="text-rose-500">*</span>
                                        </label>
                                        <textarea name="reason" 
                                                  id="permission_reason" 
                                                  rows="4" 
                                                  required 
                                                  x-model="permissionReason"
                                                  placeholder="Jelaskan alasan izin Anda (misal: Sakit, Jadwal praktikum/ujian bentrok, Sedang dinas luar, dll.)..." 
                                                  class="w-full text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-2xl p-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea>
                                        <p class="text-[10px] text-slate-400 mt-1">Alasan ini akan dikirimkan langsung ke Dosen Pembimbing Anda.</p>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                                    <button type="button" @click="permissionModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-sm cursor-pointer">
                                        Kirim Izin
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Modal Pembatalan Jadwal Bimbingan (Mahasiswa) -->
            <template x-teleport="body">
                <div x-show="cancelModalOpen" 
                     class="fixed inset-0 overflow-y-auto text-left" 
                     style="z-index: 99999 !important;" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="cancelModalOpen = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 relative" 
                             style="z-index: 100000 !important;">
                            <form :action="'{{ url('mentoring-sessions') }}/' + cancelData.id" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-sm">
                                            🚫
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Batalkan Jadwal Bimbingan</h3>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Batalkan pengajuan / jadwal bimbingan skripsi</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="cancelModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold p-1 cursor-pointer">&times;</button>
                                </div>
                                <div class="p-6 space-y-4 text-xs">
                                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="text-slate-500 dark:text-slate-400 font-medium">Dosen Pembimbing:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right" x-text="cancelData.dosen_name"></span>
                                        </div>
                                        <div class="h-px bg-slate-200 dark:bg-slate-700"></div>
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="text-slate-500 dark:text-slate-400 font-medium">Jadwal Sesi:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right" x-text="cancelData.scheduled_date + ' • ' + cancelData.scheduled_time"></span>
                                        </div>
                                        <div class="h-px bg-slate-200 dark:bg-slate-700"></div>
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="text-slate-500 dark:text-slate-400 font-medium">Topik:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right line-clamp-2" x-text="cancelData.topic"></span>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="student_cancel_reason" class="block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1.5">
                                            Alasan Pembatalan (Opsional / Disampaikan ke Dosen)
                                        </label>
                                        <textarea name="reason" 
                                                  id="student_cancel_reason" 
                                                  rows="3" 
                                                  x-model="cancelData.reason"
                                                  placeholder="Jelaskan alasan pembatalan (misal: Ada kendala dokumen, perlu penyesuaian materi skripsi, dll.)..." 
                                                  class="w-full text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-2xl p-3 focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
                                    </div>

                                    <div class="p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 rounded-xl text-[11px] text-rose-700 dark:text-rose-300 flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span>Jadwal ini akan dibatalkan dan Dosen Pembimbing akan diberitahu.</span>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                                    <button type="button" @click="cancelModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                        Tutup
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Ya, Batalkan</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function captureProgress() {
            const element = document.getElementById('progress-card');
            const isDarkMode = document.documentElement.classList.contains('dark');
            
            // Define colors based on theme
            const bgColor = isDarkMode ? '#0f172a' : '#ffffff';
            
            html2canvas(element, {
                scale: 2, // High quality
                useCORS: true,
                backgroundColor: bgColor,
                logging: false,
                onclone: (clonedDoc) => {
                    const clonedElement = clonedDoc.getElementById('progress-card');
                    clonedElement.style.padding = '32px';
                    clonedElement.style.background = bgColor;
                    clonedElement.style.borderRadius = '0px'; // Clean edges for export
                    
                    // Match the theme class on cloned document
                    if (isDarkMode) {
                        clonedDoc.documentElement.classList.add('dark');
                    } else {
                        clonedDoc.documentElement.classList.remove('dark');
                    }
                }
            }).then(canvas => {
                const link = document.createElement('a');
                const timestamp = new Date().toISOString().split('T')[0];
                link.download = `Bukti-ACC-Mentoring-{{ Auth::user()->name }}-${timestamp}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</x-app-layout>

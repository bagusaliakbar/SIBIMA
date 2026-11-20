{{-- Force fresh compilation for bug report floating capsule dock: v2 --}}
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
            studentStatusFilter: 'all',
            matchesStudentFilter(status) {
                if (this.studentStatusFilter === 'all') return true;
                const isActive = status === 'pending' || status === 'approved';
                return this.studentStatusFilter === 'active' ? isActive : !isActive;
            },
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
                            <button type="button" onclick="captureProgress()" class="inline-flex items-center px-2.5 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:bg-orange-50 dark:hover:bg-orange-500/10 hover:text-orange-600 dark:hover:text-orange-400 transition-all shadow-sm cursor-pointer">
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
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between items-start gap-2.5 sm:gap-4 mb-4 pb-3 border-b border-slate-200 dark:border-slate-800">
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-100">Seminar Usulan Penelitian (UP)</span>
                                @if($thesis->isAccUpFinal())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800/60 self-start sm:self-auto shrink-0 whitespace-nowrap shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>Sudah Di-ACC Kedua Pembimbing</span>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="space-y-5">
                                {{-- P1 UP --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">Pembimbing 1: {{ $thesis->pembimbing1->name ?? '-' }}</span>
                                        <span class="text-[10px] font-bold tabular-nums {{ $countP1 >= 4 ? 'text-emerald-600' : 'text-orange-600' }}">
                                            {{ min($countP1, 4) }}/4
                                            @if($countP1 > 4)
                                                <span class="text-slate-400 dark:text-slate-500 font-medium">({{ $countP1 }} sesi)</span>
                                            @endif
                                        </span>
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
                                        <span class="text-[10px] font-bold tabular-nums {{ $countP2 >= 4 ? 'text-emerald-600' : 'text-orange-600' }}">
                                            {{ min($countP2, 4) }}/4
                                            @if($countP2 > 4)
                                                <span class="text-slate-400 dark:text-slate-500 font-medium">({{ $countP2 }} sesi)</span>
                                            @endif
                                        </span>
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
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between items-start gap-2.5 sm:gap-4 mb-4 pb-3 border-b border-slate-200 dark:border-slate-800">
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-100">Sidang Akhir Skripsi</span>
                                @if($thesis->isAccSidangFinal())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800/60 self-start sm:self-auto shrink-0 whitespace-nowrap shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>Sudah Di-ACC Kedua Pembimbing</span>
                                    </span>
                                @elseif($countP1 >= 8 && $countP2 >= 8)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 text-[10px] font-bold uppercase tracking-wider border border-amber-200 dark:border-amber-800/60 self-start sm:self-auto shrink-0 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Syarat Sesi Terpenuhi · Menunggu ACC</span>
                                    </span>
                                @else
                                    @php
                                        $sidangRemaining = max(0, 8 - $countP1) + max(0, 8 - $countP2);
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold uppercase tracking-wider border border-slate-200 dark:border-slate-700 self-start sm:self-auto shrink-0 whitespace-nowrap">
                                        <span>Butuh {{ $sidangRemaining }} Sesi Lagi</span>
                                    </span>
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

            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Riwayat Bimbingan</h3>
                    @if(isset($sessions) && $sessions->total() > 0)
                        <span class="px-2 py-0.5 text-[11px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-full border border-slate-200 dark:border-slate-700">{{ $sessions->total() }}</span>
                    @endif
                </div>
                
                <div class="flex flex-wrap lg:flex-nowrap items-center gap-2.5 w-full sm:w-auto">
                    <!-- Quick Status Filter -->
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shrink-0">
                        <button type="button"
                                @click="studentStatusFilter = 'all'"
                                :class="studentStatusFilter === 'all' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                class="px-2.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                            Semua
                        </button>
                        <button type="button"
                                @click="studentStatusFilter = 'active'"
                                :class="studentStatusFilter === 'active' ? 'bg-orange-600 text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                class="px-2.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                            Aktif
                        </button>
                        <button type="button"
                                @click="studentStatusFilter = 'completed'"
                                :class="studentStatusFilter === 'completed' ? 'bg-slate-800 dark:bg-slate-700 text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
                                class="px-2.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                            Selesai
                        </button>
                    </div>

                    <!-- Search Input -->
                    <form action="{{ route('mentoring-sessions.index') }}" method="GET" class="relative m-0 p-0 flex items-center flex-1 sm:flex-initial">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               placeholder="Cari Topik Pembahasan" 
                               class="h-10 w-full sm:w-64 pl-9 pr-9 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 placeholder-slate-400 dark:placeholder-slate-500 text-slate-900 dark:text-slate-100 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all shadow-2xs block">
                        @if(isset($search) && $search !== '')
                            <a href="{{ route('mentoring-sessions.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" title="Bersihkan Pencarian">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </form>

                    <!-- Ajukan Bimbingan Button -->
                    <a href="{{ route('mentoring-sessions.create') }}" 
                       class="h-10 inline-flex items-center justify-center gap-1.5 px-4 bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white text-xs font-bold rounded-xl shadow-sm transition-colors whitespace-nowrap shrink-0 cursor-pointer">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Ajukan Bimbingan</span>
                    </a>
                </div>
            </div>

            @include('mentoring.partials.student-desktop-table')

            @include('mentoring.partials.student-mobile-cards')
            
            @if($sessions->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $sessions->links() }}
                </div>
            @endif

            @include('mentoring.partials.student-modals')
        </div>
    </div>
    @include('mentoring.partials.student-capture-script')
</x-app-layout>

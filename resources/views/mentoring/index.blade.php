<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Bimbingan', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <x-table-card 
            title="Daftar Pengajuan Jadwal"
            :footer="$sessions->links()">
            
            <x-slot name="headerActions">
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari nama atau topik..." 
                        route="mentoring-sessions.index" />

                    @if(Auth::user()->role === 'dosen')
                        <a href="{{ route('mentoring-sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-700 transition-all shadow-sm whitespace-nowrap">+ Tambah Jadwal</a>
                    @endif
                </div>
            </x-slot>

            <div class="p-6 space-y-12">
                @php
                    $groupedSessions = $sessions->groupBy(function($session) {
                        return $session->thesis->student->name;
                    });
                @endphp

                @forelse($groupedSessions as $studentName => $studentSessions)
                    <div>
                        @php
                            $studentThesis = $studentSessions->first()->thesis;
                            $mentoringCount = Auth::user()->role === 'admin' 
                                ? $studentThesis->completed_mentoring_count 
                                : $studentThesis->getCompletedMentoringCountForDosen(Auth::id());
                        @endphp
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 flex items-center mb-4 md:mb-0 uppercase tracking-tight">
                                <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center mr-3 font-black text-xs">
                                    {{ substr($studentName, 0, 1) }}
                                </div>
                                <span>{{ $studentName }}</span>
                                <span class="ml-4 px-2 py-0.5 rounded-md bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-wider">
                                    {{ $mentoringCount }} Bimbingan {{ Auth::user()->role === 'admin' ? 'Total' : 'dengan Anda' }}
                                </span>
                            </h4>

                            @if(Auth::user()->role === 'dosen')
                                <div class="flex flex-wrap gap-2">
                                    {{-- ACC UP Button --}}
                                    @php
                                        $isP1 = Auth::id() === $studentThesis->pembimbing1_id;
                                        $isP2 = Auth::id() === $studentThesis->pembimbing2_id;
                                        $hasAccUp = $isP1 ? $studentThesis->acc_up_p1 : ($isP2 ? $studentThesis->acc_up_p2 : false);
                                    @endphp
                                    {{-- ACC UP Group --}}
                                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/50 p-1.5 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                        <form action="{{ route('theses.toggle-acc', [$studentThesis->id, 'up']) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                title="{{ $hasAccUp ? 'Batalkan ACC Seminar' : 'Berikan ACC Seminar' }}"
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm
                                                {{ $hasAccUp ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}
                                                {{ $mentoringCount < 4 && !$hasAccUp ? 'opacity-75' : '' }}">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                ACC SEMINAR
                                                @if($mentoringCount < 4 && !$hasAccUp)
                                                    <span class="ml-1.5 px-1.5 rounded bg-orange-100 text-orange-700 text-[9px]">{{ $mentoringCount }}/4</span>
                                                @endif
                                            </button>
                                        </form>
                                        <div class="flex gap-1 border-l border-slate-200 dark:border-slate-700 pl-2 ml-1">
                                            <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_up_p1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P1"></div>
                                            <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_up_p2 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P2"></div>
                                        </div>
                                    </div>

                                    {{-- ACC Sidang Group --}}
                                    @php
                                        $hasAccSidang = $isP1 ? $studentThesis->acc_sidang_p1 : ($isP2 ? $studentThesis->acc_sidang_p2 : false);
                                    @endphp
                                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/50 p-1.5 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                        <form action="{{ route('theses.toggle-acc', [$studentThesis->id, 'sidang']) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                title="{{ $hasAccSidang ? 'Batalkan ACC Sidang' : 'Berikan ACC Sidang' }}"
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm
                                                {{ $hasAccSidang ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}
                                                {{ $mentoringCount < 8 && !$hasAccSidang ? 'opacity-75' : '' }}">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                ACC SIDANG
                                                @if($mentoringCount < 8 && !$hasAccSidang)
                                                    <span class="ml-1.5 px-1.5 rounded bg-orange-100 text-orange-700 text-[9px]">{{ $mentoringCount }}/8</span>
                                                @endif
                                            </button>
                                        </form>
                                        <div class="flex gap-1 border-l border-slate-200 dark:border-slate-700 pl-2 ml-1">
                                            <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_sidang_p1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P1"></div>
                                            <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_sidang_p2 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P2"></div>
                                        </div>
                                    </div>

                                    @if($studentThesis->isAccUpFinal())
                                        <div class="flex items-center px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-100 dark:border-emerald-500/20 text-[10px] font-black uppercase tracking-widest shadow-sm">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            UP FINAL ACC
                                        </div>
                                    @endif
                                    @if($studentThesis->isAccSidangFinal())
                                        <div class="flex items-center px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-100 dark:border-emerald-500/20 text-[10px] font-black uppercase tracking-widest shadow-sm">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            SIDANG FINAL ACC
                                        </div>
                                    @endif
                                </div>
                            @elseif(Auth::user()->role === 'admin')
                                <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest">
                                    Dosen Pembimbing: <span class="text-slate-700 dark:text-slate-300">{{ $studentSessions->first()->dosen->name }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($studentSessions as $session)
                                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-700/50 rounded-2xl p-5 relative overflow-hidden group hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-none hover:border-orange-200 dark:hover:border-orange-500/30 transition-all">
                                    <!-- Status Indicator -->
                                    <div class="absolute top-0 left-0 w-full h-1.5 
                                        {{ $session->status === 'pending' ? 'bg-amber-400' : '' }}
                                        {{ $session->status === 'approved' ? 'bg-orange-600' : '' }}
                                        {{ $session->status === 'rejected' || $session->is_absent ? 'bg-red-500' : '' }}
                                        {{ $session->status === 'completed' && !$session->is_absent ? 'bg-slate-300 dark:bg-slate-700' : '' }}
                                    "></div>
                                    
                                    <div class="flex justify-between items-start mb-5 mt-2">
                                        <div>
                                            <p class="text-[10px] text-orange-600 font-black uppercase tracking-widest">{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">{{ $session->scheduled_at->format('H:i') }} WIB</p>
                                        </div>
                                        @if($session->is_absent)
                                            <x-status-badge type="red" label="TIDAK HADIR" />
                                        @else
                                            <x-status-badge 
                                                :type="$session->status === 'pending' ? 'amber' : ($session->status === 'approved' ? 'orange' : ($session->status === 'rejected' ? 'red' : 'slate'))" 
                                                :label="strtoupper($session->status)" />
                                        @endif
                                    </div>
                                    
                                    <div class="mb-5">
                                        <p class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight line-clamp-2 min-h-[2rem]">{{ $session->topic }}</p>
                                        
                                        <div class="mt-3 flex items-center text-[10px] font-black uppercase tracking-wider">
                                            @if($session->type === 'online')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-600 mr-2 border border-blue-100 dark:border-blue-500/20">Online</span>
                                                @if($session->location)
                                                    <a href="{{ Str::startsWith($session->location, 'http') ? $session->location : 'https://' . $session->location }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                        Link Rapat
                                                    </a>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-50 dark:bg-slate-800 text-slate-500 mr-2 border border-slate-100 dark:border-slate-700">Offline</span>
                                                @if($session->location)
                                                    <span class="text-slate-400 truncate max-w-[120px]" title="{{ $session->location }}">{{ $session->location }}</span>
                                                @endif
                                            @endif
                                        </div>

                                        @if($session->notes)
                                            <div class="mt-4 pl-3 border-l-2 border-slate-100 dark:border-slate-700">
                                                <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Catatan Mahasiswa</div>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 italic line-clamp-2">"{{ $session->notes }}"</p>
                                            </div>
                                        @endif
                                        
                                        @if($session->feedback)
                                            <div class="mt-4">
                                                <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Hasil Bimbingan</div>
                                                <p class="text-[11px] text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 p-2.5 rounded-xl font-medium italic">"{{ $session->feedback }}"</p>
                                            </div>
                                        @endif

                                        @if($session->document_path)
                                            <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-800/50">
                                                <a href="{{ route('download.private', ['path' => $session->document_path]) }}" target="_blank"
                                                   class="flex items-center gap-3 p-2 bg-indigo-50/50 dark:bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/10 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all group/doc">
                                                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 flex items-center justify-center text-indigo-500 border border-indigo-100 dark:border-indigo-500/20 group-hover/doc:scale-110 transition-transform">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-[10px] font-black text-indigo-700 dark:text-indigo-400 truncate uppercase tracking-tighter">{{ $session->document_original_name }}</p>
                                                        <p class="text-[9px] text-indigo-400 dark:text-indigo-500 font-bold uppercase">Unduh Dokumen</p>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($session->status === 'pending')
                                    <div class="flex space-x-2 mt-auto pt-4 border-t border-slate-50 dark:border-slate-800/50">
                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="w-full px-3 py-2 bg-orange-600 text-white hover:bg-orange-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Terima</button>
                                        </form>
                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 hover:border-red-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Tolak</button>
                                        </form>
                                    </div>
                                    @elseif($session->status === 'approved')
                                    <div class="mt-auto pt-4 border-t border-slate-50 dark:border-slate-800/50" x-data="{ showFeedback: false }">
                                        <div class="flex space-x-2" x-show="!showFeedback">
                                            <button type="button" @click="showFeedback = true" class="flex-1 px-3 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Selesai</button>
                                            <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="absent">
                                                <button type="submit" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 hover:border-red-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Absen</button>
                                            </form>
                                        </div>
                                        <div x-show="showFeedback" x-cloak class="mt-2" x-transition>
                                            <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <textarea name="feedback" rows="3" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-3" placeholder="Catatan hasil bimbingan..."></textarea>
                                                <div class="flex space-x-2">
                                                    <button type="submit" class="flex-1 px-3 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Simpan</button>
                                                    <button type="button" @click="showFeedback = false" class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <x-empty-state description="Belum ada pengajuan jadwal bimbingan dari mahasiswa." icon="mentoring" />
                @endforelse
            </div>
        </x-table-card>
    </div>
</x-app-layout>

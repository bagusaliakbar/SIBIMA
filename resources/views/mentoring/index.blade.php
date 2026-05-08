<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Bimbingan', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden transition-colors">
            @if(session('success'))
                <div class="m-6 mb-0 p-4 rounded bg-emerald-50 text-emerald-700 text-sm flex items-center border border-emerald-100">
                    <svg class="w-4 h-4 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 transition-colors">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Daftar Pengajuan Jadwal</h3>
                
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <!-- Search Input -->
                    <form action="{{ route('mentoring-sessions.index') }}" method="GET" class="relative w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama atau topik..." class="block w-full sm:w-64 pl-10 pr-10 py-1.5 border border-slate-200 dark:border-slate-700 rounded-md leading-5 bg-slate-50 dark:bg-slate-900 placeholder-slate-400 dark:placeholder-slate-500 text-slate-900 dark:text-slate-100 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition-all">
                        @if(isset($search) && $search !== '')
                            <a href="{{ route('mentoring-sessions.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </form>

                    @if(Auth::user()->role === 'dosen')
                        <a href="{{ route('mentoring-sessions.create') }}" class="px-3 py-1.5 bg-orange-600 text-white text-xs font-medium rounded hover:bg-orange-700 transition-colors shadow-sm whitespace-nowrap">+ Tambah Jadwal</a>
                    @endif
                </div>
            </div>

            <div class="p-6 space-y-8">
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
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 border-b border-slate-200 dark:border-slate-700 pb-3 transition-colors">
                            <h4 class="text-md font-bold text-slate-800 dark:text-slate-100 flex items-center mb-3 md:mb-0">
                                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $studentName }}
                                <span class="ml-3 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-xs font-semibold">{{ $mentoringCount }} Bimbingan {{ Auth::user()->role === 'admin' ? 'Total' : 'dengan Anda' }}</span>
                            </h4>

                            @if(Auth::user()->role === 'dosen')
                                <div class="flex flex-wrap gap-2">
                                    {{-- ACC UP Button --}}
                                    @php
                                        $isP1 = Auth::id() === $studentThesis->pembimbing1_id;
                                        $isP2 = Auth::id() === $studentThesis->pembimbing2_id;
                                        $hasAccUp = $isP1 ? $studentThesis->acc_up_p1 : ($isP2 ? $studentThesis->acc_up_p2 : false);
                                    @endphp
                                    <form action="{{ route('theses.toggle-acc', [$studentThesis->id, 'up']) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 rounded text-xs font-bold transition-all shadow-sm
                                            {{ $hasAccUp ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800 hover:bg-orange-100 dark:hover:bg-orange-500/20' }}
                                            {{ $mentoringCount < 4 && !$hasAccUp ? 'opacity-75' : '' }}">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            ACC SEMINAR UP
                                            @if($mentoringCount < 4 && !$hasAccUp)
                                                <span class="ml-1.5 px-1 rounded bg-orange-200 text-[9px]">{{ $mentoringCount }}/4</span>
                                            @endif
                                        </button>
                                    </form>

                                    {{-- ACC Sidang Button --}}
                                    @php
                                        $hasAccSidang = $isP1 ? $studentThesis->acc_sidang_p1 : ($isP2 ? $studentThesis->acc_sidang_p2 : false);
                                    @endphp
                                    <form action="{{ route('theses.toggle-acc', [$studentThesis->id, 'sidang']) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 rounded text-xs font-bold transition-all shadow-sm
                                            {{ $hasAccSidang ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800 hover:bg-orange-100 dark:hover:bg-orange-500/20' }}
                                            {{ $mentoringCount < 8 && !$hasAccSidang ? 'opacity-75' : '' }}">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            ACC SIDANG AKHIR
                                            @if($mentoringCount < 8 && !$hasAccSidang)
                                                <span class="ml-1.5 px-1 rounded bg-orange-200 text-[9px]">{{ $mentoringCount }}/8</span>
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Final Status Badges --}}
                                    @if($studentThesis->isAccUpFinal())
                                        <span class="inline-flex items-center px-2.5 py-1.5 rounded bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800 uppercase tracking-tight">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                            UP FINAL ACC
                                        </span>
                                    @endif
                                    @if($studentThesis->isAccSidangFinal())
                                        <span class="inline-flex items-center px-2.5 py-1.5 rounded bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800 uppercase tracking-tight transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                            SIDANG FINAL ACC
                                        </span>
                                    @endif
                                </div>
                            @elseif(Auth::user()->role === 'admin')
                                <div class="text-[10px] text-slate-400 font-medium italic">
                                    Dosen Pembimbing: {{ $studentSessions->first()->dosen->name }}
                                </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($studentSessions as $session)
                                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded p-5 relative overflow-hidden group hover:shadow-md hover:border-orange-200 dark:hover:border-orange-500/30 transition-all">
                                    <!-- Status Indicator -->
                                    <div class="absolute top-0 left-0 w-full h-1 
                                        {{ $session->status === 'pending' ? 'bg-amber-400' : '' }}
                                        {{ $session->status === 'approved' ? 'bg-orange-600' : '' }}
                                        {{ $session->status === 'rejected' || $session->is_absent ? 'bg-red-500' : '' }}
                                        {{ $session->status === 'completed' && !$session->is_absent ? 'bg-slate-400' : '' }}
                                    "></div>
                                    
                                    <div class="flex justify-between items-start mb-4 mt-1">
                                        <div>
                                            <p class="text-[11px] text-orange-600 font-bold uppercase tracking-wider mt-0.5">{{ $session->scheduled_at->format('d M Y • H:i') }}</p>
                                        </div>
                                        @if($session->is_absent)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                                TIDAK HADIR
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                                {{ $session->status === 'pending' ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : '' }}
                                                {{ $session->status === 'approved' ? 'bg-orange-600 text-white' : '' }}
                                                {{ $session->status === 'rejected' ? 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800' : '' }}
                                                {{ $session->status === 'completed' ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700' : '' }}
                                            ">
                                                {{ $session->status }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-slate-700 dark:text-slate-200 font-medium">Topik: {{ $session->topic }}</p>
                                        <div class="mt-2 flex items-start text-xs">
                                            @if($session->type === 'online')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-semibold mr-2 border border-blue-100 dark:border-blue-500/20">Online</span>
                                                @if($session->location)
                                                    <a href="{{ Str::startsWith($session->location, 'http') ? $session->location : 'https://' . $session->location }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline font-medium" title="{{ $session->location }}">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                        Gabung Rapat
                                                    </a>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold mr-2 border border-slate-200 dark:border-slate-700">Offline</span>
                                                @if($session->location)
                                                    <span class="text-slate-500 truncate inline-block max-w-[200px]" title="{{ $session->location }}">{{ $session->location }}</span>
                                                @endif
                                            @endif
                                        </div>
                                        @if($session->notes)
                                            <div class="mt-3">
                                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 transition-colors">Catatan Pengajuan</div>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 italic border-l-2 border-slate-200 dark:border-slate-700 pl-2">"{{ $session->notes }}"</p>
                                            </div>
                                        @endif
                                        
                                        @if($session->feedback)
                                            <div class="mt-3">
                                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 transition-colors">Hasil & Catatan Pembimbing</div>
                                                <p class="text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 p-2.5 rounded-md font-medium transition-colors">"{{ $session->feedback }}"</p>
                                            </div>
                                        @endif

                                        {{-- DOKUMEN MAHASISWA --}}
                                        @if($session->document_path)
                                            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 transition-colors">
                                                <div class="text-[10px] font-bold text-orange-600 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                    Dokumen Mahasiswa
                                                </div>
                                                <a href="{{ Storage::url($session->document_path) }}" target="_blank"
                                                   class="flex items-center gap-2 p-2 bg-orange-50 dark:bg-orange-500/10 border border-orange-100 dark:border-orange-500/20 rounded-md hover:bg-orange-100 dark:hover:bg-orange-500/20 hover:border-orange-200 dark:hover:border-orange-800 transition-all group">
                                                    <svg class="w-5 h-5 text-orange-400 flex-shrink-0 group-hover:text-orange-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs font-semibold text-orange-700 dark:text-orange-400 truncate">{{ $session->document_original_name }}</p>
                                                        <p class="text-[10px] text-orange-400 dark:text-orange-500">Klik untuk buka / unduh</p>
                                                    </div>
                                                    <svg class="w-4 h-4 text-orange-400 flex-shrink-0 group-hover:text-orange-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                </a>
                                            </div>
                                        @else
                                            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 transition-colors">
                                                <p class="text-[10px] text-slate-300 dark:text-slate-600 italic flex items-center gap-1 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                    Belum ada dokumen diunggah
                                                </p>
                                            </div>
                                        @endif
                                        {{-- END DOKUMEN MAHASISWA --}}
                                    </div>
                                    
                                    @if($session->status === 'pending')
                                    <div class="flex space-x-2 mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 transition-colors">
                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="w-full px-3 py-1.5 bg-orange-600 text-white hover:bg-orange-700 rounded text-xs font-semibold transition-colors shadow-sm">Terima</button>
                                        </form>
                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="w-full px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-900 rounded text-xs font-semibold transition-all shadow-sm">Tolak</button>
                                        </form>
                                    </div>
                                    @elseif($session->status === 'approved')
                                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 transition-colors" x-data="{ showFeedback: false }">
                                        <div class="flex space-x-2" x-show="!showFeedback">
                                            <button type="button" @click="showFeedback = true" class="flex-1 px-3 py-1.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded text-xs font-semibold transition-colors shadow-sm">Tandai Selesai</button>
                                            <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="absent">
                                                <button type="submit" class="w-full px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-900 rounded text-xs font-semibold transition-all shadow-sm">Tidak Hadir</button>
                                            </form>
                                        </div>
                                        <div x-show="showFeedback" x-cloak class="mt-2">
                                            <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan Pembimbing (Wajib)</label>
                                                <textarea name="feedback" rows="3" required class="block w-full rounded-md border-0 py-1.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:ring-2 focus:ring-inset focus:ring-orange-600 sm:text-xs sm:leading-5 mb-2 transition-all" placeholder="Tuliskan hasil/catatan dari sesi bimbingan ini..."></textarea>
                                                <div class="flex space-x-2">
                                                    <button type="submit" class="flex-1 px-3 py-1.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded text-xs font-semibold transition-colors shadow-sm">Simpan & Selesai</button>
                                                    <button type="button" @click="showFeedback = false" class="px-3 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600 rounded text-xs font-semibold transition-colors shadow-sm">Batal</button>
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
                    <div class="col-span-full py-16 flex flex-col items-center justify-center text-slate-500 dark:text-slate-400 border border-dashed border-slate-300 dark:border-slate-700 rounded transition-colors">
                        <svg class="w-12 h-12 mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="font-medium text-sm text-slate-600 dark:text-slate-400">Belum ada pengajuan jadwal bimbingan dari mahasiswa.</p>
                    </div>
                @endforelse
            </div>
            
            @if($sessions->hasPages())
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 transition-colors">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

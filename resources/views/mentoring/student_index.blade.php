<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Bimbingan', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden transition-colors">
            @if(session('success'))
                <div class="m-6 mb-0 p-4 rounded bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-sm flex items-center border border-emerald-100 dark:border-emerald-500/20">
                    <svg class="w-4 h-4 mr-3 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="m-6 mb-0 p-4 rounded bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 text-sm flex items-start border border-red-100 dark:border-red-500/20">
                    <svg class="w-4 h-4 mr-3 text-red-500 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
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
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30" id="progress-card">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Progress Bimbingan
                        </h3>
                        <div class="flex items-center gap-3">
                            <button onclick="captureProgress()" class="inline-flex items-center px-2.5 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:bg-orange-50 dark:hover:bg-orange-500/10 hover:text-orange-600 dark:hover:text-orange-400 transition-all shadow-sm">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                DOWNLOAD BUKTI ACC
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mb-6 p-3 bg-white/50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-lg">
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
                        <div class="bg-white dark:bg-slate-900 p-5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm transition-colors">
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Seminar Usulan Penelitian (UP)</span>
                                @if($thesis->isAccUpFinal())
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-100 dark:border-emerald-500/20">SIAP SEMINAR</span>
                                @endif
                            </div>
                            
                            <div class="space-y-5">
                                {{-- P1 UP --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">Pembimbing 1: {{ $thesis->pembimbing1->name ?? '-' }}</span>
                                        <span class="text-[10px] font-bold {{ $countP1 >= 4 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $countP1 }}/4</span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 flex items-center gap-3">
                                        <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $upProgressP1 }}%"></div>
                                        @if($thesis->acc_up_p1)
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <div class="w-4 h-4 rounded-full border-2 border-slate-200 dark:border-slate-700 shrink-0"></div>
                                        @endif
                                    </div>
                                </div>

                                {{-- P2 UP --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">Pembimbing 2: {{ $thesis->pembimbing2->name ?? '-' }}</span>
                                        <span class="text-[10px] font-bold {{ $countP2 >= 4 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $countP2 }}/4</span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 flex items-center gap-3">
                                        <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $upProgressP2 }}%"></div>
                                        @if($thesis->acc_up_p2)
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <div class="w-4 h-4 rounded-full border-2 border-slate-200 dark:border-slate-700 shrink-0"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sidang Akhir Progress --}}
                        <div class="bg-white dark:bg-slate-900 p-5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm transition-colors">
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Sidang Akhir Skripsi</span>
                                @if($thesis->isAccSidangFinal())
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-100 dark:border-emerald-500/20">SIAP SIDANG</span>
                                @endif
                            </div>

                            <div class="space-y-5">
                                {{-- P1 Sidang --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">Pembimbing 1: {{ $thesis->pembimbing1->name ?? '-' }}</span>
                                        <span class="text-[10px] font-bold {{ $countP1 >= 8 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $countP1 }}/8</span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 flex items-center gap-3">
                                        <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $sidangProgressP1 }}%"></div>
                                        @if($thesis->acc_sidang_p1)
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <div class="w-4 h-4 rounded-full border-2 border-slate-200 dark:border-slate-700 shrink-0"></div>
                                        @endif
                                    </div>
                                </div>

                                {{-- P2 Sidang --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight">Pembimbing 2: {{ $thesis->pembimbing2->name ?? '-' }}</span>
                                        <span class="text-[10px] font-bold {{ $countP2 >= 8 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $countP2 }}/8</span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 flex items-center gap-3">
                                        <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $sidangProgressP2 }}%"></div>
                                        @if($thesis->acc_sidang_p2)
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <div class="w-4 h-4 rounded-full border-2 border-slate-200 dark:border-slate-700 shrink-0"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Riwayat Bimbingan</h3>
                
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <!-- Search Input -->
                    <form action="{{ route('mentoring-sessions.index') }}" method="GET" class="relative w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari topik..." class="block w-full sm:w-64 pl-10 pr-10 py-1.5 border border-slate-200 dark:border-slate-700 rounded-md leading-5 bg-slate-50 dark:bg-slate-900 placeholder-slate-400 dark:placeholder-slate-500 text-slate-900 dark:text-slate-100 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition-all">
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
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">TANGGAL & WAKTU</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">TOPIK PEMBAHASAN</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">DOSEN PEMBIMBING</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">DOKUMEN</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">CATATAN (LOGBOOK)</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group align-top">
                                <td class="py-4 px-6 text-slate-800 dark:text-slate-200 font-medium whitespace-nowrap">
                                    {{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }} <br>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-normal">{{ $session->scheduled_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="py-4 px-6 text-slate-700 dark:text-slate-300">
                                    <div class="font-medium">{{ \Illuminate\Support\Str::limit($session->topic, 40) }}</div>
                                    <div class="mt-1.5 flex items-start text-[11px]">
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
                                                <span class="text-slate-500 truncate max-w-[150px] inline-block" title="{{ $session->location }}">{{ $session->location }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 text-[10px] font-bold border border-slate-200 dark:border-slate-600 transition-colors">
                                            {{ substr($session->dosen->name ?? 'D', 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-xs text-slate-800 dark:text-slate-200 truncate max-w-[150px]">{{ $session->dosen->name ?? '-' }}</div>
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
                                <td class="py-4 px-6 min-w-[220px]">
                                    @if(in_array($session->status, ['pending', 'approved']))
                                        <div x-data="{ uploading: false }">
                                            @if($session->document_path)
                                                {{-- Dokumen sudah ada --}}
                                                <div class="flex items-center gap-2 mb-2 p-2 bg-orange-50 dark:bg-orange-500/10 border border-orange-100 dark:border-orange-500/20 rounded-md">
                                                    <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    <a href="{{ Storage::url($session->document_path) }}" target="_blank" class="text-xs text-orange-700 dark:text-orange-400 font-semibold hover:underline truncate max-w-[120px]" title="{{ $session->document_original_name }}">
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
                                                    <form action="{{ route('mentoring-sessions.delete-document', $session->id) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex items-center gap-1 px-2 py-1 text-[10px] font-semibold text-red-600 dark:text-red-400 bg-white dark:bg-slate-700 border border-red-200 dark:border-red-900 rounded hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                {{-- Belum ada dokumen --}}
                                                <button type="button" @click="uploading = !uploading" class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-orange-700 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 rounded-md hover:bg-orange-100 dark:hover:bg-orange-500/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                    Upload Dokumen
                                                </button>
                                            @endif

                                            {{-- Upload form (collapsible) --}}
                                            <div x-show="uploading" x-cloak x-transition class="mt-2">
                                                <form action="{{ route('mentoring-sessions.upload-document', $session->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-1.5">
                                                    @csrf
                                                    <label class="block">
                                                        <input type="file" name="document" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                                            class="block w-full text-xs text-slate-600 dark:text-slate-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-orange-50 dark:file:bg-orange-500/10 file:text-orange-700 dark:file:text-orange-400 hover:file:bg-orange-100 dark:hover:file:bg-orange-500/20 cursor-pointer">
                                                    </label>
                                                    <p class="text-[10px] text-slate-400">PDF, DOC, DOCX, PPT, ZIP. Maks 10MB.</p>
                                                    <div class="flex gap-1">
                                                        <button type="submit" class="px-2.5 py-1 bg-orange-600 text-white text-[10px] font-bold rounded hover:bg-orange-700 transition-colors">Unggah</button>
                                                        <button type="button" @click="uploading = false" class="px-2.5 py-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 text-[10px] font-semibold rounded hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($session->document_path)
                                        {{-- Sesi selesai/ditolak tapi ada dokumen: tampilkan read-only --}}
                                        <a href="{{ Storage::url($session->document_path) }}" target="_blank" class="flex items-center gap-1.5 text-xs text-slate-600 hover:text-orange-700 transition-colors">
                                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span class="truncate max-w-[150px]">{{ $session->document_original_name }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-300 text-xs italic">—</span>
                                    @endif
                                </td>
                                {{-- === END DOKUMEN COLUMN === --}}

                                <td class="py-4 px-6 max-w-xs">
                                    @if($session->feedback)
                                        <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-0.5">Catatan Pembimbing:</div>
                                        <div class="text-slate-500 dark:text-slate-400 text-xs">{{ \Illuminate\Support\Str::limit($session->feedback, 60) }}</div>
                                    @elseif($session->notes)
                                        <div class="text-[10px] uppercase tracking-wider font-bold text-slate-400 dark:text-slate-500 mb-0.5">Catatan Pengajuan:</div>
                                        <div class="text-slate-500 dark:text-slate-400 text-xs italic">{{ \Illuminate\Support\Str::limit($session->notes, 60) }}</div>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-600">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @if($session->is_absent)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                            Tidak Hadir
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                            {{ $session->status === 'pending' ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : '' }}
                                            {{ $session->status === 'approved' ? 'bg-orange-600 text-white' : '' }}
                                            {{ $session->status === 'rejected' ? 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800' : '' }}
                                            {{ $session->status === 'completed' ? 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-600' : '' }}
                                        ">
                                            {{ $session->status }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-500 border-b border-slate-100">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="font-medium text-sm text-slate-600">Belum ada riwayat bimbingan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($sessions->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $sessions->links() }}
                </div>
            @endif
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

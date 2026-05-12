<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Dashboard
                    <span class="ml-3 px-2 py-0.5 bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase tracking-wider rounded-md border border-orange-200 dark:border-orange-500/20 shadow-sm">v2.0</span>
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                    Sistem Informasi Bimbingan Mahasiswa
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(Auth::user()->role === 'mahasiswa')
            @php
                $isGraduated = $thesis && $thesis->status === 'completed';
                $progressPercent = 0;
                $steps = 0;
                $seminarDone = false;
                $defenseDone = false;

                // Step 1: Judul (0% -> 20%)
                if ($thesis) { 
                    $progressPercent = 20; 
                    $steps++; 
                    
                    // Step 2: Bab 1-3 (20% -> 40%)
                    $mentoring1 = min(4, $pastSessionsCount);
                    $progressPercent += ($mentoring1 / 4) * 20;
                    if ($mentoring1 >= 4) $steps++;
                    
                    // Step 3: Seminar (40% -> 60%)
                    $seminarDone = ($seminar && in_array($seminar->status, ['approved', 'completed', 'finished']));
                    if ($seminarDone) { 
                        $progressPercent = 60; 
                        $steps++; 
                        
                        // Step 4: Bab 4-5 (60% -> 80%)
                        $mentoring2 = max(0, min(4, $pastSessionsCount - 4));
                        $progressPercent += ($mentoring2 / 4) * 20;
                        if ($mentoring2 >= 4) $steps++;
                        
                        // Step 5: Sidang (80% -> 100%)
                        $hasDefenseRevisions = \App\Models\ThesisDefenseRevision::whereHas('detail', function($q) use ($thesis) {
                            $q->where('thesis_id', $thesis?->id);
                        })->exists();
                        
                        $defenseDone = ($defense && in_array($defense->status, ['approved', 'completed', 'finished'])) || $hasDefenseRevisions;
                        if ($defenseDone) { 
                            $progressPercent = 90; // Almost there
                            $steps++; 
                        }

                        // Step 6: Kelulusan (100%)
                        if ($isGraduated) { 
                            $progressPercent = 100; 
                            $steps++; 
                        }
                    }
                }
            @endphp

            @if($isStale)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 p-4 rounded-xl mb-6 flex items-center gap-4 animate-pulse">
                    <div class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-red-800 dark:text-red-200 uppercase tracking-tight">Peringatan Progress!</h4>
                        <p class="text-xs text-red-600 dark:text-red-400 font-medium">Anda belum melakukan bimbingan selama <b>{{ $daysSinceLastSession }} hari</b>. Segera hubungi pembimbing Anda!</p>
                    </div>
                </div>
            @endif

            @if(Auth::user()->is_critical_semester)
                <div class="bg-gradient-to-r from-red-600 to-rose-700 p-4 rounded-xl mb-6 flex items-center gap-4 shadow-lg shadow-red-200 dark:shadow-none">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-md text-white rounded-xl flex items-center justify-center flex-shrink-0 border border-white/30">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-black text-white uppercase tracking-wider">Peringatan Masa Studi Kritikal!</h4>
                        <p class="text-xs text-red-50 font-medium leading-relaxed">Saat ini Anda berada di <b>Semester {{ Auth::user()->current_semester }}</b>. Harap segera menyelesaikan skripsi Anda untuk menghindari potensi Drop Out (DO). Hubungi Koordinator Prodi jika memerlukan bantuan khusus.</p>
                    </div>
                    <div class="hidden md:block">
                        <span class="px-3 py-1 bg-white/10 text-white text-[10px] font-black uppercase tracking-widest rounded-full border border-white/20">Urgent</span>
                    </div>
                </div>
            @endif
            <!-- Timeline Progres Mahasiswa -->
            <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 mb-6 relative overflow-hidden transition-all duration-300">
                <!-- Background Pattern -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 dark:bg-orange-500/5 rounded-full -mr-16 -mt-16 opacity-50 transition-colors"></div>
                
                <div class="flex items-center justify-between mb-10 relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight">Timeline Perjalanan Skripsi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Langkah Anda menuju gelar sarjana.</p>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-2xl font-black text-orange-600">{{ round($progressPercent) }}%</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Progres</span>
                    </div>
                </div>

                <div class="relative px-4">
                    <!-- Progress Line (Background) -->
                    <div class="absolute top-5 left-4 right-4 h-1 bg-slate-100 dark:bg-slate-700 rounded-full"></div>
                    <!-- Progress Line (Active) -->
                    <div class="absolute top-5 left-4 h-1 bg-gradient-to-r from-orange-500 to-orange-400 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(249,115,22,0.3)] dark:shadow-[0_0_12px_rgba(249,115,22,0.15)]" style="width: calc({{ $progressPercent }}% - 2rem)"></div>

                    <div class="relative flex justify-between">
                        <!-- Step 1: Judul -->
                        <div class="flex flex-col items-center group">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center z-10 transition-all duration-500 {{ $thesis ? 'bg-orange-600 text-white shadow-xl shadow-orange-200 dark:shadow-orange-900/20' : 'bg-white dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 text-slate-300 dark:text-slate-500' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="mt-3 text-center">
                                <span class="block text-[10px] font-black {{ $thesis ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }} uppercase tracking-tighter leading-none">Judul</span>
                                <span class="text-[8px] font-bold {{ $thesis ? 'text-emerald-500' : 'text-slate-400' }}">{{ $thesis ? 'Selesai' : 'Belum' }}</span>
                            </div>
                        </div>

                        <!-- Step 2: Bab 1-3 -->
                        <div class="flex flex-col items-center group">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center z-10 transition-all duration-500 {{ $pastSessionsCount >= 4 ? 'bg-orange-600 text-white shadow-xl shadow-orange-200 dark:shadow-orange-900/20' : ($pastSessionsCount > 0 ? 'bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-2 border-orange-200 dark:border-orange-800' : 'bg-white dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 text-slate-300 dark:text-slate-500') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <div class="mt-3 text-center">
                                <span class="block text-[10px] font-black {{ $pastSessionsCount >= 4 ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }} uppercase tracking-tighter leading-none">Bab 1-3</span>
                                <span class="text-[8px] font-bold {{ $pastSessionsCount >= 4 ? 'text-emerald-500' : 'text-slate-400' }}">{{ min(4, $pastSessionsCount) }}/4 Sesi</span>
                            </div>
                        </div>

                        <!-- Step 3: Seminar -->
                        <div class="flex flex-col items-center group">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center z-10 transition-all duration-500 {{ $seminarDone ? 'bg-orange-600 text-white shadow-xl shadow-orange-200 dark:shadow-orange-900/20' : ($seminar ? 'bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-2 border-orange-200 dark:border-orange-800' : 'bg-white dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 text-slate-300 dark:text-slate-500') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </div>
                            <div class="mt-3 text-center">
                                <span class="block text-[10px] font-black {{ $seminarDone ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }} uppercase tracking-tighter leading-none">Seminar</span>
                                <span class="text-[8px] font-bold {{ $seminarDone ? 'text-emerald-500' : 'text-slate-400' }}">{{ $seminarDone ? 'Selesai' : ($seminar ? ucfirst($seminar->status) : 'Belum') }}</span>
                            </div>
                        </div>

                        <!-- Step 4: Penelitian -->
                        <div class="flex flex-col items-center group">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center z-10 transition-all duration-500 {{ $pastSessionsCount >= 8 ? 'bg-orange-600 text-white shadow-xl shadow-orange-200 dark:shadow-orange-900/20' : ($pastSessionsCount >= 4 ? 'bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-2 border-orange-200 dark:border-orange-800' : 'bg-white dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 text-slate-300 dark:text-slate-500') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <div class="mt-3 text-center">
                                <span class="block text-[10px] font-black {{ $pastSessionsCount >= 8 ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }} uppercase tracking-tighter leading-none">Bab 4-5</span>
                                <span class="text-[8px] font-bold {{ $pastSessionsCount >= 8 ? 'text-emerald-500' : 'text-slate-400' }}">{{ min(4, max(0, $pastSessionsCount - 4)) }}/4 Sesi</span>
                            </div>
                        </div>

                        <!-- Step 5: Sidang -->
                        <div class="flex flex-col items-center group">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center z-10 transition-all duration-500 {{ $defenseDone ? 'bg-orange-600 text-white shadow-xl shadow-orange-200 dark:shadow-orange-900/20' : ($defense ? 'bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-2 border-orange-200 dark:border-orange-800' : 'bg-white dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 text-slate-300 dark:text-slate-500') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>
                            </div>
                            <div class="mt-3 text-center">
                                <span class="block text-[10px] font-black {{ $defenseDone ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }} uppercase tracking-tighter leading-none">Sidang</span>
                                <span class="text-[8px] font-bold {{ $defenseDone ? 'text-emerald-500' : 'text-slate-400' }}">{{ $defenseDone ? 'Selesai' : ($defense ? ucfirst($defense->status) : 'Belum') }}</span>
                            </div>
                        </div>

                        <!-- Step 6: Lulus -->
                        <div class="flex flex-col items-center group">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center z-10 transition-all duration-500 {{ $isGraduated ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-200 dark:shadow-emerald-900/20' : 'bg-white dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 text-slate-300 dark:text-slate-500' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div class="mt-3 text-center">
                                <span class="block text-[10px] font-black {{ $isGraduated ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }} uppercase tracking-tighter leading-none">Lulus</span>
                                <span class="text-[8px] font-bold {{ $isGraduated ? 'text-emerald-500' : 'text-slate-400' }}">{{ $isGraduated ? 'Selamat!' : 'Belum' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($isGraduated)
            <div class="bg-slate-900 dark:bg-slate-800/80 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden mb-8 border border-white/10 backdrop-blur-md">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500/10 rounded-full -ml-24 -mb-24 blur-3xl"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                    <div class="flex-1 text-center md:text-left">
                        <div class="inline-flex items-center px-3 py-1 bg-orange-500/10 text-orange-400 text-[10px] font-black uppercase tracking-[0.3em] rounded-full mb-4 border border-orange-500/20">
                            Status: LULUS
                        </div>
                        <h2 class="text-3xl font-black tracking-tight mb-3 bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Selamat, {{ Auth::user()->name }}!</h2>
                        <p class="text-slate-400 font-medium text-base leading-relaxed max-w-2xl">
                            Seluruh tahapan skripsi telah Anda selesaikan dengan baik. Anda kini dinyatakan <span class="text-amber-400 font-bold">LULUS</span>. Terima kasih atas dedikasi dan kerja keras Anda selama ini.
                        </p>
                    </div>

                    <div class="hidden lg:block w-px h-24 bg-gradient-to-b from-transparent via-white/10 to-transparent"></div>

                    <div class="text-center md:text-right">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tanggal Yudisium</p>
                        <p class="text-xl font-black text-white tracking-tighter">{{ now()->format('d F Y') }}</p>
                    </div>
                </div>
            </div>
            @endif
        @endif
        <!-- Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if(Auth::user()->role === 'mahasiswa')
                <!-- Stats Mahasiswa -->
                <div class="bg-white dark:bg-slate-800/50 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-900/30">
                    <div class="absolute right-0 top-0 -mr-4 -mt-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-500/5 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg flex items-center justify-center mb-4 transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status Skripsi</h3>
                        @if($thesis)
                            <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mt-1 uppercase">{{ $thesis->status === 'active' ? 'Aktif' : 'Menunggu' }}</h2>
                        @else
                            <h2 class="text-lg font-extrabold text-slate-400 mt-1">Belum Ada</h2>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800/50 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all hover:shadow-md hover:border-orange-200 dark:hover:border-orange-900/30">
                    <div class="absolute right-0 top-0 -mr-4 -mt-4 w-24 h-24 bg-orange-50 dark:bg-orange-500/5 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded-lg flex items-center justify-center mb-4 transition-colors group-hover:bg-orange-600 group-hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pembimbing</h3>
                        @if($thesis && $thesis->pembimbing1_id)
                            <div class="mt-1 space-y-0.5">
                                <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate" title="{{ $thesis->pembimbing1->name }}">1. {{ $thesis->pembimbing1->name }}</p>
                                <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate" title="{{ $thesis->pembimbing2->name }}">2. {{ $thesis->pembimbing2->name }}</p>
                            </div>
                        @else
                            <h2 class="text-lg font-extrabold text-slate-400 mt-1">Belum Ada</h2>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800/50 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all hover:shadow-md hover:border-blue-200 dark:hover:border-blue-900/30">
                    <div class="absolute right-0 top-0 -mr-4 -mt-4 w-24 h-24 bg-blue-50 dark:bg-blue-500/5 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mb-4 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sesi Selesai</h3>
                        <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ $pastSessionsCount ?? 0 }} <span class="text-xs font-medium text-slate-400 lowercase tracking-normal">Sesi</span></h2>
                        @if($thesis)
                            <div class="mt-2 space-y-0.5 border-t border-slate-100 dark:border-slate-700/50 pt-2">
                                <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                    <span>P1: {{ Str::limit($thesis->pembimbing1->name, 50) }}</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $pastSessionsCountP1 }} Sesi</span>
                                </p>
                                <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                    <span>P2: {{ Str::limit($thesis->pembimbing2->name, 50) }}</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $pastSessionsCountP2 }} Sesi</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800/50 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-900/30">
                    <div class="absolute right-0 top-0 -mr-4 -mt-4 w-24 h-24 bg-indigo-50 dark:bg-indigo-500/5 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center mb-4 transition-colors group-hover:bg-indigo-600 group-hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Progres Keseluruhan</h3>
                        <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ round($progressPercent) }}%</h2>
                    </div>
                </div>
            @else
                <!-- Stats Admin/Dosen -->
                <div class="bg-white dark:bg-slate-800/50 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all hover:shadow-md hover:border-orange-200 dark:hover:border-orange-900/30">
                    <div class="absolute right-0 top-0 -mr-4 -mt-4 w-24 h-24 bg-orange-50 dark:bg-orange-500/5 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded-lg flex items-center justify-center mb-4 transition-colors group-hover:bg-orange-600 group-hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ Auth::user()->role === 'admin' ? 'Total Skripsi' : 'Mhs Bimbingan' }}</h3>
                        <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ $activeThesesCount ?? 0 }}</h2>
                        @if(Auth::user()->role === 'dosen')
                            <div class="mt-2 space-y-0.5 border-t border-slate-100 dark:border-slate-700/50 pt-2">
                                <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                    <span>Pembimbing 1</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $totalActiveStudentsP1 ?? 0 }} Mahasiswa</span>
                                </p>
                                <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                    <span>Pembimbing 2</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $totalActiveStudentsP2 ?? 0 }} Mahasiswa</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800/50 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all hover:shadow-md hover:border-blue-200 dark:hover:border-blue-900/30">
                    <div class="absolute right-0 top-0 -mr-4 -mt-4 w-24 h-24 bg-blue-50 dark:bg-blue-500/5 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mb-4 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jadwal Minggu Ini</h3>
                        <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ $sessionsThisWeek ?? 0 }} <span class="text-xs font-medium text-slate-400 lowercase tracking-normal">Sesi</span></h2>
                        @if(Auth::user()->role === 'dosen')
                            <div class="mt-2 space-y-0.5 border-t border-slate-100 dark:border-slate-700/50 pt-2">
                                <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                    <span>Menunggu</span>
                                    <span class="font-bold text-amber-600 dark:text-amber-400">{{ $pendingSessionsThisWeek ?? 0 }}</span>
                                </p>
                                <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                    <span>Disetujui</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $approvedSessionsThisWeek ?? 0 }}</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800/50 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-900/30">
                    <div class="absolute right-0 top-0 -mr-4 -mt-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-500/5 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg flex items-center justify-center mb-4 transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sesi Selesai</h3>
                        <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ Auth::user()->role === 'dosen' ? ($totalCompletedSessions ?? 0) : (($activeThesesCount ?? 0) * 4) }}</h2>
                        <p class="text-[10px] font-medium text-slate-400 mt-1 italic tracking-tight">Total seluruh bimbingan</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800/50 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all hover:shadow-md hover:border-pink-200 dark:hover:border-pink-900/30">
                    <div class="absolute right-0 top-0 -mr-4 -mt-4 w-24 h-24 bg-pink-50 dark:bg-pink-500/5 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative">
                        <div class="w-10 h-10 bg-pink-100 dark:bg-pink-500/10 text-pink-600 dark:text-pink-400 rounded-lg flex items-center justify-center mb-4 transition-colors group-hover:bg-pink-600 group-hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Progres Rata-rata</h3>
                        <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ Auth::user()->role === 'dosen' ? ($averageStudentProgress ?? 0) : '68' }}%</h2>
                        <p class="text-[10px] font-medium text-slate-400 mt-1 italic tracking-tight">Performa bimbingan global</p>
                    </div>
                </div>
            @endif
        </div>

        @if(Auth::user()->role !== 'mahasiswa')
            <!-- Analytical Dashboard for Admin/Dosen -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Chart 1: Distribution -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                            {{ Auth::user()->role === 'admin' ? 'Status Skripsi Global' : 'Distribusi Progres Bimbingan' }}
                        </h3>
                        <div class="h-64">
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Trends -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 dark:bg-blue-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            Tren Aktivitas Bimbingan
                        </h3>
                        <div class="h-64">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @if(Auth::user()->role === 'admin')
            <!-- Chart 3: Dosen Workload (Full Width) -->
            <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300 mb-6">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 dark:bg-emerald-900/10 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Distribusi Beban Kerja Dosen (Top 10)
                    </h3>
                    <div class="h-80">
                        <canvas id="workloadChart"></canvas>
                    </div>
                </div>
            </div>
        @endif
    @endif

        @if(Auth::user()->role === 'mahasiswa' && ($mySeminarSchedule || $myDefenseSchedule))
            {{-- Student Schedule Section (already here) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if($mySeminarSchedule)
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-12 -mt-12 opacity-50"></div>
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Jadwal Seminar Skripsi
                                </h3>
                                <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-orange-200">Terjadwal</span>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-slate-50 dark:bg-slate-900/50 rounded-xl flex flex-col items-center justify-center border border-slate-100 dark:border-slate-700">
                                        <span class="text-[10px] font-black text-slate-400 uppercase leading-none">{{ \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->translatedFormat('M') }}</span>
                                        <span class="text-lg font-black text-slate-800 dark:text-slate-100 leading-none mt-1">{{ \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->format('d') }}</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Hari & Tanggal</p>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100">{{ \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->translatedFormat('l, d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Waktu</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($mySeminarSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($mySeminarSchedule->end_time)->format('H:i') }} WIB</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Ruangan/Tempat</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $mySeminarSchedule->schedule->location ?: '-' }}</p>
                                    </div>
                                </div>
                                @if($mySeminarSchedule->schedule->meeting_link)
                                    <div class="pt-2">
                                        <a href="{{ $mySeminarSchedule->schedule->meeting_link }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors shadow-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            Join Online Meeting
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if($myDefenseSchedule)
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/10 rounded-full -mr-12 -mt-12 opacity-50"></div>
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Jadwal Sidang Skripsi
                                </h3>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-emerald-200">Terjadwal</span>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-slate-50 dark:bg-slate-900/50 rounded-xl flex flex-col items-center justify-center border border-slate-100 dark:border-slate-700">
                                        <span class="text-[10px] font-black text-slate-400 uppercase leading-none">{{ \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->translatedFormat('M') }}</span>
                                        <span class="text-lg font-black text-slate-800 dark:text-slate-100 leading-none mt-1">{{ \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->format('d') }}</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Hari & Tanggal</p>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100">{{ \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->translatedFormat('l, d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Waktu</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($myDefenseSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($myDefenseSchedule->end_time)->format('H:i') }} WIB</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Ruangan/Tempat</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $myDefenseSchedule->schedule->location ?: '-' }}</p>
                                    </div>
                                </div>
                                @if($myDefenseSchedule->schedule->meeting_link)
                                    <div class="pt-2">
                                        <a href="{{ $myDefenseSchedule->schedule->meeting_link }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors shadow-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            Join Online Meeting
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if(Auth::user()->role === 'dosen' && ($examinerSeminarSchedules->count() > 0 || $examinerDefenseSchedules->count() > 0))
            <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden mb-6 transition-all duration-300">
                <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 bg-slate-900 text-white flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-tight flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            Jadwal Menguji Seminar & Sidang
                        </h3>
                        <p class="text-[10px] text-slate-400 font-medium mt-0.5 uppercase tracking-widest">Daftar Mahasiswa yang akan Anda uji.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-50 dark:border-slate-700">
                                <th class="px-6 py-3">Mahasiswa</th>
                                <th class="px-6 py-3">Posisi</th>
                                <th class="px-6 py-3">Jenis Ujian</th>
                                <th class="px-6 py-3">Waktu & Tempat</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                            @foreach($examinerSeminarSchedules as $detail)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-bold text-slate-800 dark:text-slate-100 text-xs">{{ $detail->thesis->student->name }}</p>
                                            <p class="text-[10px] text-slate-400 italic mt-0.5 line-clamp-1">{{ $detail->thesis->title }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($detail->thesis->pembimbing1_id == Auth::id() || $detail->thesis->pembimbing2_id == Auth::id())
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-blue-200">Pembimbing</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-indigo-200">Penguji</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-orange-200">Seminar</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-[11px]">
                                            <p class="font-bold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($detail->schedule->date)->translatedFormat('d M Y') }}</p>
                                            <p class="text-slate-400 font-medium mt-0.5">{{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} WIB @ {{ $detail->schedule->location ?: '-' }}</p>
                                            @if($detail->schedule->meeting_link)
                                                <a href="{{ $detail->schedule->meeting_link }}" target="_blank" class="text-[9px] text-blue-600 dark:text-blue-400 font-black flex items-center mt-1 hover:underline">
                                                    <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                    Link Google Meet
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('seminar-examiner.show', $detail->id) }}" class="inline-flex items-center px-3 py-1 bg-slate-100 dark:bg-slate-700 hover:bg-orange-600 hover:text-white text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded transition-all">
                                            Buka Lembar Revisi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($examinerDefenseSchedules as $detail)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-bold text-slate-800 dark:text-slate-100 text-xs">{{ $detail->thesis->student->name }}</p>
                                            <p class="text-[10px] text-slate-400 italic mt-0.5 line-clamp-1">{{ $detail->thesis->title }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($detail->thesis->pembimbing1_id == Auth::id() || $detail->thesis->pembimbing2_id == Auth::id())
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-blue-200">Pembimbing</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-indigo-200">Penguji</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-emerald-200">Sidang</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-[11px]">
                                            <p class="font-bold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($detail->schedule->date)->translatedFormat('d M Y') }}</p>
                                            <p class="text-slate-400 font-medium mt-0.5">{{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} WIB @ {{ $detail->schedule->location ?: '-' }}</p>
                                            @if($detail->schedule->meeting_link)
                                                <a href="{{ $detail->schedule->meeting_link }}" target="_blank" class="text-[9px] text-blue-600 dark:text-blue-400 font-black flex items-center mt-1 hover:underline">
                                                    <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                    Link Google Meet
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('defense-examiner.show', $detail->id) }}" class="inline-flex items-center px-3 py-1 bg-slate-100 dark:bg-slate-700 hover:bg-emerald-600 hover:text-white text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded transition-all">
                                            Buka Lembar Penilaian
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Main Feed -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Upcoming Sessions Table -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden transition-all duration-300">
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex justify-between items-center bg-slate-50/30 dark:bg-slate-900/30">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Jadwal Bimbingan Terdekat</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Pantau jadwal yang telah disetujui atau menunggu konfirmasi.</p>
                        </div>
                        <a href="{{ route('mentoring-sessions.index') }}" class="text-xs font-bold text-orange-600 hover:underline flex items-center">
                            Lihat Semua
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-50 dark:border-slate-700">
                                    <th class="px-6 py-3">Topik & Mahasiswa</th>
                                    <th class="px-6 py-3">Waktu Pelaksanaan</th>
                                    <th class="px-6 py-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                                @forelse(isset($upcomingSessions) ? $upcomingSessions : [] as $session)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors cursor-pointer group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-600 flex items-center justify-center mr-3 font-bold text-xs">
                                                {{ substr($session->topic, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-700 dark:text-slate-200 text-xs">{{ $session->topic }}</h4>
                                                @if(Auth::user()->role !== 'mahasiswa')
                                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $session->thesis->student->name }}</p>
                                                @else
                                                    <p class="text-[10px] text-slate-400 mt-0.5">Dosen: {{ $session->dosen->name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-600">{{ $session->scheduled_at->format('d M Y') }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $session->scheduled_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest
                                            {{ $session->status === 'approved' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                                            {{ $session->status === 'approved' ? 'Disetujui' : 'Menunggu' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-12 h-12 bg-slate-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center mb-3">
                                                <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <p class="text-xs text-slate-400 font-medium italic">Belum ada jadwal bimbingan terdekat.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Activity / Logbook -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden transition-all duration-300">
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Logbook Terbaru</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative border-l-2 border-slate-100 dark:border-slate-700 ml-3 space-y-8">
                            @forelse(isset($recentLogbooks) ? $recentLogbooks : [] as $logbook)
                            <div class="relative pl-7 group">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white dark:bg-slate-800 border-2 border-orange-500 ring-4 ring-orange-50 dark:ring-slate-900 transition-all group-hover:scale-125"></div>
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $logbook->topic }}</h4>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $logbook->scheduled_at->format('d M Y') }}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2">{{ $logbook->notes }}</p>
                                @if(Auth::user()->role !== 'mahasiswa')
                                    <div class="mt-2 flex items-center">
                                        <div class="w-4 h-4 rounded-full bg-slate-200 mr-2"></div>
                                        <span class="text-[10px] text-slate-400 font-medium italic">Oleh: {{ $logbook->thesis->student->name }}</span>
                                    </div>
                                @endif
                            </div>
                            @empty
                            <div class="text-center py-6">
                                <p class="text-xs text-slate-400 italic">Belum ada catatan logbook terbaru.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-6">
                <!-- Announcements Card -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden transition-all duration-300">
                    <div class="px-5 py-4 border-b border-slate-50 dark:border-slate-700/50 bg-slate-800 dark:bg-slate-800 text-white flex justify-between items-center">
                        <h3 class="text-[10px] font-extrabold uppercase tracking-widest">Papan Informasi</h3>
                        <span class="p-1 bg-white/10 rounded">
                            <svg class="w-3.5 h-3.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </span>
                    </div>
                    <div class="divide-y divide-slate-50 dark:divide-slate-700">
                        @forelse($announcements as $announcement)
                        <div class="p-5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <div class="flex items-center gap-2 mb-2">
                                @php
                                    $color = $announcement->type === 'important' ? 'red' : ($announcement->type === 'warning' ? 'orange' : 'blue');
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-tighter border {{ $color === 'red' ? 'bg-red-100 text-red-600 border-red-200' : ($color === 'orange' ? 'bg-orange-100 text-orange-600 border-orange-200' : 'bg-blue-100 text-blue-600 border-blue-200') }}">
                                    {{ $announcement->type }}
                                </span>
                                <span class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter">{{ $announcement->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-tight mb-1">{{ $announcement->title }}</p>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-wrap">{{ $announcement->content }}</div>
                        </div>
                        @empty
                        <div class="p-10 text-center">
                            <svg class="w-10 h-10 text-slate-100 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-xs text-slate-400 italic">Belum ada pengumuman.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = document.documentElement.classList.contains('dark');
            @if(Auth::user()->role !== 'mahasiswa')
                // Distribution Chart
                const distCtx = document.getElementById('distributionChart').getContext('2d');
                @if(Auth::user()->role === 'admin')
                    new Chart(distCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Aktif', 'Selesai', 'Menunggu'],
                            datasets: [{
                                data: [{{ $thesisStatusCounts['active'] ?? 0 }}, {{ $thesisStatusCounts['completed'] ?? 0 }}, {{ $thesisStatusCounts['pending'] ?? 0 }}],
                                backgroundColor: ['#f97316', '#10b981', '#6366f1'],
                                borderWidth: 0,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'bottom', 
                                    labels: { 
                                        usePointStyle: true, 
                                        padding: 20, 
                                        font: { weight: 'bold', size: 10 },
                                        color: darkMode ? '#94a3b8' : '#64748b'
                                    } 
                                } 
                            },
                            cutout: '75%'
                        }
                    });
                @else
                    new Chart(distCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_keys($studentProgressDistribution)) !!},
                            datasets: [{
                                label: 'Mahasiswa',
                                data: {!! json_encode(array_values($studentProgressDistribution)) !!},
                                backgroundColor: '#f97316',
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { 
                                y: { 
                                    beginAtZero: true, 
                                    grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b', stepSize: 1 } 
                                }, 
                                x: { 
                                    grid: { display: false },
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b' }
                                } 
                            },
                            plugins: { legend: { display: false } }
                        }
                    });
                @endif

                // Activity Chart
                const actCtx = document.getElementById('activityChart').getContext('2d');
                new Chart(actCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_keys($monthlyMentoringCounts)) !!},
                        datasets: [{
                            label: 'Sesi Selesai',
                            data: {!! json_encode(array_values($monthlyMentoringCounts)) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.05)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#3b82f6',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { 
                            y: { 
                                beginAtZero: true, 
                                grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                ticks: { color: darkMode ? '#94a3b8' : '#64748b', stepSize: 1 } 
                            }, 
                            x: { 
                                grid: { display: false },
                                ticks: { color: darkMode ? '#94a3b8' : '#64748b' }
                            } 
                        },
                        plugins: { legend: { display: false } }
                    }
                });

                @if(Auth::user()->role === 'admin')
                    // Workload Chart
                    const workCtx = document.getElementById('workloadChart').getContext('2d');
                    new Chart(workCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_keys($dosenWorkload)) !!},
                            datasets: [{
                                label: 'Jumlah Mahasiswa',
                                data: {!! json_encode(array_values($dosenWorkload)) !!},
                                backgroundColor: '#10b981',
                                borderRadius: 8,
                                barThickness: 30
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { 
                                x: { 
                                    beginAtZero: true, 
                                    grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b', stepSize: 1 } 
                                }, 
                                y: { 
                                    grid: { display: false },
                                    ticks: { 
                                        color: darkMode ? '#f1f5f9' : '#1e293b',
                                        font: { weight: 'bold', size: 11 }
                                    }
                                } 
                            },
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: darkMode ? '#1e293b' : '#fff',
                                    titleColor: darkMode ? '#fff' : '#1e293b',
                                    bodyColor: darkMode ? '#cbd5e1' : '#64748b',
                                    borderColor: darkMode ? '#334155' : '#e2e8f0',
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: false
                                }
                            }
                        }
                    });
                @endif
            @endif
        });
    </script>
</x-app-layout>

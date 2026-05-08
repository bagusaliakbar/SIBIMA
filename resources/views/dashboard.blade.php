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
                $progressPercent = 0;
                if ($thesis) $progressPercent += 25;
                if ($pastSessionsCount > 0) $progressPercent += min(25, ($pastSessionsCount / 8) * 25);
                if ($seminar && $seminar->status === 'approved') $progressPercent += 25;
                if ($thesis && $thesis->acc_sidang_p1 && $thesis->acc_sidang_p2) $progressPercent += 25;
            @endphp
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
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center z-10 transition-all duration-500 {{ $thesis ? 'bg-orange-600 text-white shadow-xl shadow-orange-200 dark:shadow-orange-900/20' : 'bg-white dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 text-slate-300 dark:text-slate-500' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="mt-4 text-center">
                                <span class="block text-[11px] font-black {{ $thesis ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }} uppercase tracking-tighter">Pengajuan Judul</span>
                                <span class="text-[9px] font-bold {{ $thesis ? 'text-emerald-500' : 'text-slate-400' }}">{{ $thesis ? 'Selesai' : 'Belum' }}</span>
                            </div>
                        </div>

                        <!-- Step 2: Bimbingan -->
                        <div class="flex flex-col items-center group">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center z-10 transition-all duration-500 {{ $pastSessionsCount >= 8 ? 'bg-orange-600 text-white shadow-xl shadow-orange-200 dark:shadow-orange-900/20' : ($pastSessionsCount > 0 ? 'bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-2 border-orange-200 dark:border-orange-800' : 'bg-white dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 text-slate-300 dark:text-slate-500') }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <div class="mt-4 text-center">
                                <span class="block text-[11px] font-black {{ $pastSessionsCount > 0 ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }} uppercase tracking-tighter">Bimbingan</span>
                                <span class="text-[9px] font-bold {{ $pastSessionsCount >= 8 ? 'text-emerald-500' : 'text-slate-400' }}">{{ $pastSessionsCount }}/8 Sesi</span>
                            </div>
                        </div>

                        <!-- Step 3: Seminar -->
                        <div class="flex flex-col items-center group">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center z-10 transition-all duration-500 {{ ($seminar && $seminar->status === 'approved') ? 'bg-orange-600 text-white shadow-xl shadow-orange-200 dark:shadow-orange-900/20' : ($seminar ? 'bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-2 border-orange-200 dark:border-orange-800' : 'bg-white dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 text-slate-300 dark:text-slate-500') }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </div>
                            <div class="mt-4 text-center">
                                <span class="block text-[11px] font-black {{ $seminar ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }} uppercase tracking-tighter">Seminar</span>
                                <span class="text-[9px] font-bold {{ ($seminar && $seminar->status === 'approved') ? 'text-emerald-500' : 'text-slate-400' }}">{{ $seminar ? ucfirst($seminar->status) : 'Belum' }}</span>
                            </div>
                        </div>

                        <!-- Step 4: Sidang -->
                        <div class="flex flex-col items-center group">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center z-10 transition-all duration-500 {{ ($thesis && $thesis->acc_sidang_p1 && $thesis->acc_sidang_p2) ? 'bg-orange-600 text-white shadow-xl shadow-orange-200 dark:shadow-orange-900/20' : 'bg-white dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 text-slate-300 dark:text-slate-500' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>
                            </div>
                            <div class="mt-4 text-center">
                                <span class="block text-[11px] font-black {{ ($thesis && $thesis->acc_sidang_p1 && $thesis->acc_sidang_p2) ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }} uppercase tracking-tighter">Sidang Akhir</span>
                                <span class="text-[9px] font-bold {{ ($thesis && $thesis->acc_sidang_p1 && $thesis->acc_sidang_p2) ? 'text-emerald-500' : 'text-slate-400' }}">{{ ($thesis && $thesis->acc_sidang_p1 && $thesis->acc_sidang_p2) ? 'Siap' : 'Belum' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            @endif
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Monitoring Bimbingan', 'route' => route('monitoring.index')],
            ['label' => 'Sesi Belum Selesai (Dosen)', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full mx-auto transition-colors duration-300" x-data="uncompletedMentoringPage()">
        <!-- Shared Header Navigation Tabs -->
        @include('monitoring.partials.tabs')

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- 1. Lewat Jadwal (Overdue) -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-rose-500/10 dark:bg-rose-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Lewat Jadwal (Overdue)</span>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-100 dark:border-rose-800/60 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 tracking-tight">{{ number_format($global_overdue_count) }}</span>
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500">sesi</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5 font-medium">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                    <span>Waktu lewat, belum diisi catatan</span>
                </p>
            </div>

            <!-- 2. Total Sesi Tertunda -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-orange-500/10 dark:bg-orange-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Belum Selesai</span>
                    <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-800/60 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($global_uncompleted_count) }}</span>
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500">sesi</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5 font-medium">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                    <span>Status pending & approved</span>
                </p>
            </div>

            <!-- 3. Dosen Terkait -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Dosen Terkait</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-800/60 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($unique_dosen_count) }}</span>
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500">dosen</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5 font-medium">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>Memiliki sesi belum selesai</span>
                </p>
            </div>

            <!-- 4. Mahasiswa Terdampak -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Mahasiswa Menunggu</span>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-100 dark:border-purple-800/60 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($affected_students_count) }}</span>
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500">mahasiswa</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5 font-medium">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                    <span>Menanti hasil bimbingan diinput</span>
                </p>
            </div>
        </div>

        <!-- Main Card: Filter & Content -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden mb-8">
            <!-- Header Bar -->
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-700/80 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <!-- Scope Tabs -->
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-900/60 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                    <a href="{{ route('monitoring.uncompleted-mentoring', array_merge(request()->query(), ['scope' => 'overdue'])) }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ ($filters['scope'] ?? 'overdue') === 'overdue' ? 'bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 shadow-xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                        <span>⚠️ Lewat Jadwal</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($filters['scope'] ?? 'overdue') === 'overdue' ? 'bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            {{ $global_overdue_count }}
                        </span>
                    </a>
                    <a href="{{ route('monitoring.uncompleted-mentoring', array_merge(request()->query(), ['scope' => 'today'])) }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ ($filters['scope'] ?? 'overdue') === 'today' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                        <span>📅 Hari Ini</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($filters['scope'] ?? 'overdue') === 'today' ? 'bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            {{ $global_today_count }}
                        </span>
                    </a>
                    <a href="{{ route('monitoring.uncompleted-mentoring', array_merge(request()->query(), ['scope' => 'all'])) }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ ($filters['scope'] ?? 'overdue') === 'all' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                        <span>📋 Semua</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($filters['scope'] ?? 'overdue') === 'all' ? 'bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            {{ $global_uncompleted_count }}
                        </span>
                    </a>
                </div>

                <!-- Right Actions: View Switcher & Action Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- View Mode Toggle -->
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-900/60 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        <a href="{{ route('monitoring.uncompleted-mentoring', array_merge(request()->query(), ['view' => 'dosen'])) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ ($filters['view'] ?? 'dosen') === 'dosen' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 dark:text-slate-400' }}"
                           title="Tampilan per Dosen">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span>Per Dosen</span>
                        </a>
                        <a href="{{ route('monitoring.uncompleted-mentoring', array_merge(request()->query(), ['view' => 'session'])) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ ($filters['view'] ?? 'dosen') === 'session' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 dark:text-slate-400' }}"
                           title="Tampilan Rincian Sesi">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            <span>Daftar Sesi</span>
                        </a>
                    </div>

                    <!-- Export Excel -->
                    <a href="{{ route('monitoring.uncompleted-mentoring.export-excel', request()->query()) }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Export Excel</span>
                    </a>

                    <!-- Broadcast WA ke Seluruh Dosen Ini -->
                    <a href="{{ route('wa-broadcasts.create', ['target_type' => 'dosen_belum_selesai_bimbingan']) }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                        <span>Broadcast WA</span>
                    </a>
                </div>
            </div>

            <!-- Filter Toolbar Form -->
            <div class="p-4 sm:p-5 bg-slate-50/50 dark:bg-slate-900/30 border-b border-slate-100 dark:border-slate-700/80">
                <form action="{{ route('monitoring.uncompleted-mentoring') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <input type="hidden" name="scope" value="{{ $filters['scope'] ?? 'overdue' }}">
                    <input type="hidden" name="view" value="{{ $filters['view'] ?? 'dosen' }}">

                    <!-- Filter Dosen -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Pilih Dosen</label>
                        <select name="dosen_id" onchange="this.form.submit()" class="w-full text-xs font-medium rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 shadow-2xs">
                            <option value="">Semua Dosen Pembimbing</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}" {{ ($filters['dosen_id'] ?? '') == $d->id ? 'selected' : '' }}>
                                    {{ $d->name }} ({{ $d->identifier ?: 'NIDN -' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Pencarian</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $filters['search'] ?? '' }}" 
                                   placeholder="Cari nama dosen, nama mahasiswa, NPM, atau topik..." 
                                   class="w-full pl-9 pr-20 text-xs font-medium rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 shadow-2xs">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <div class="absolute inset-y-0 right-0 pr-1.5 flex items-center gap-1">
                                @if(!empty($filters['search']) || !empty($filters['dosen_id']) || ($filters['scope'] ?? 'overdue') !== 'overdue')
                                    <a href="{{ route('monitoring.uncompleted-mentoring', ['view' => $filters['view'] ?? 'dosen']) }}" class="px-2 py-1 text-[11px] font-bold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-700 rounded-lg">
                                        Reset
                                    </a>
                                @endif
                                <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-white bg-orange-500 hover:bg-orange-600 rounded-lg shadow-2xs">
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Content Area -->
            @if(($filters['view'] ?? 'dosen') === 'dosen')
                <!-- VIEW 1: GROUPED BY LECTURER -->
                <div class="p-4 sm:p-6 space-y-6">
                    @forelse($lecturers as $item)
                        @php
                            $dosen = $item['dosen'];
                            $waNumber = $dosen->phone_number ? preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $dosen->phone_number)) : null;
                        @endphp
                        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-700/80 bg-white dark:bg-slate-800 shadow-xs overflow-hidden transition-all hover:border-orange-500/40">
                            <!-- Lecturer Header Bar -->
                            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/40 dark:bg-slate-900/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <img src="{{ $dosen->avatar_url }}" alt="{{ $dosen->name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow-xs">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm sm:text-base font-black text-slate-900 dark:text-white">
                                                {{ $dosen->name }}
                                            </h4>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 border border-orange-200/60 dark:border-orange-800/40">
                                                {{ ucfirst($dosen->role) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                            NIDN: {{ $dosen->identifier ?: '-' }} • {{ $dosen->email }}
                                            @if($dosen->phone_number)
                                                • <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">{{ $dosen->phone_number }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap items-center gap-2 shrink-0">
                                    <!-- Badge Sesi Count -->
                                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60 text-rose-700 dark:text-rose-300 text-xs font-black">
                                        <span>{{ $item['total_sessions'] }} Sesi Tertunda</span>
                                        @if($item['overdue_sessions'] > 0)
                                            <span class="px-1.5 py-0.2 bg-rose-500 text-white rounded text-[10px]">
                                                {{ $item['overdue_sessions'] }} Overdue
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Tombol Kirim Pengingat WA -->
                                    <button type="button" 
                                            @click="openReminderModal(@js($dosen->id), @js($dosen->name), @js($dosen->phone_number), @js($item['total_sessions']), @js($item['sessions']))"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-xs">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"></path></svg>
                                        <span>Kirim WA</span>
                                    </button>

                                    <!-- Buka Sesi Dosen -->
                                    <a href="{{ route('mentoring-sessions.index', ['dosen_id' => $dosen->id]) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700/60 hover:bg-slate-200 dark:hover:bg-slate-600 transition-all shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span>Buka di Jadwal</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Sessions Table inside Lecturer Card -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs text-left">
                                    <thead class="text-[10px] text-slate-400 dark:text-slate-500 uppercase bg-slate-50/70 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700 font-black tracking-wider">
                                        <tr>
                                            <th class="py-3 px-5">Jadwal Sesi</th>
                                            <th class="py-3 px-5">Mahasiswa</th>
                                            <th class="py-3 px-5">Topik Pembahasan</th>
                                            <th class="py-3 px-5">Kehadiran</th>
                                            <th class="py-3 px-5">Status Keterlambatan</th>
                                            <th class="py-3 px-5 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                        @foreach($item['sessions'] as $session)
                                            @php
                                                $student = $session->thesis?->student;
                                                $isOverdue = $session->scheduled_at->isPast();
                                                $daysOver = $isOverdue ? $session->scheduled_at->diffInDays(now()) : 0;
                                            @endphp
                                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                                                <td class="py-3 px-5 whitespace-nowrap">
                                                    <p class="font-bold text-slate-800 dark:text-slate-200">
                                                        {{ $session->scheduled_at->locale('id')->translatedFormat('d M Y, H:i') }} WIB
                                                    </p>
                                                    <p class="text-[10px] text-slate-400 font-medium">
                                                        {{ ucfirst($session->type ?? 'offline') }} {{ $session->location ? "• {$session->location}" : '' }}
                                                    </p>
                                                </td>
                                                <td class="py-3 px-5 whitespace-nowrap">
                                                    <div class="flex items-center gap-2">
                                                        <img src="{{ $student?->avatar_url }}" alt="" class="w-6 h-6 rounded-lg object-cover">
                                                        <div>
                                                            <p class="font-bold text-slate-800 dark:text-slate-200">{{ $student?->name ?? '-' }}</p>
                                                            <p class="text-[10px] text-slate-400">{{ $student?->identifier ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-5">
                                                    <p class="font-bold text-slate-700 dark:text-slate-300 line-clamp-1 max-w-xs">{{ $session->topic }}</p>
                                                    <p class="text-[10px] text-slate-400 line-clamp-1 max-w-xs">{{ $session->thesis?->title ?? '-' }}</p>
                                                </td>
                                                <td class="py-3 px-5 whitespace-nowrap">
                                                    @if($session->student_attendance_status === 'attending')
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                            Hadir
                                                        </span>
                                                    @elseif($session->student_attendance_status === 'permission')
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800" title="{{ $session->student_attendance_reason }}">
                                                            Izin
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">
                                                            Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-5 whitespace-nowrap">
                                                    @if($isOverdue)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-50 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                            {{ $daysOver > 0 ? "Lewat {$daysOver} Hari" : "Lewat Hari Ini" }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                                            Jadwal Mendatang
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-5 text-right whitespace-nowrap">
                                                    <a href="{{ route('mentoring-sessions.edit', $session) }}" 
                                                       class="px-2.5 py-1 text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:text-orange-500 bg-slate-100 hover:bg-orange-50 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg transition-colors">
                                                        Detail / Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-1">Semua Sesi Bimbingan Tuntas!</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                                Tidak ditemukan sesi bimbingan dosen yang tertunda atau belum diselesaikan untuk kriteria filter ini.
                            </p>
                        </div>
                    @endforelse
                </div>
            @else
                <!-- VIEW 2: FLAT SESSION LIST -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[10px] text-slate-500 dark:text-slate-400 uppercase bg-slate-50/80 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-700 font-black tracking-wider">
                            <tr>
                                <th class="py-4 px-6 text-center w-14">No</th>
                                <th class="py-4 px-6">Waktu Jadwal</th>
                                <th class="py-4 px-6">Dosen Pembimbing</th>
                                <th class="py-4 px-6">Mahasiswa</th>
                                <th class="py-4 px-6">Topik & Judul</th>
                                <th class="py-4 px-6">Kehadiran</th>
                                <th class="py-4 px-6">Keterlambatan</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                            @forelse($all_sessions as $index => $session)
                                @php
                                    $student = $session->thesis?->student;
                                    $dosen = $session->dosen ?? $session->thesis?->pembimbing1;
                                    $isOverdue = $session->scheduled_at->isPast();
                                    $daysOver = $isOverdue ? $session->scheduled_at->diffInDays(now()) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/40 transition-colors">
                                    <td class="py-4 px-6 text-center text-xs font-bold text-slate-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                            {{ $session->scheduled_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-medium">
                                            {{ ucfirst($session->type ?? 'offline') }} {{ $session->location ? "• {$session->location}" : '' }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $dosen?->name ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $dosen?->identifier ? 'NIDN: ' . $dosen->identifier : '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <img src="{{ $student?->avatar_url }}" alt="" class="w-7 h-7 rounded-lg object-cover">
                                            <div>
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $student?->name ?? '-' }}</p>
                                                <p class="text-[10px] text-slate-400 font-medium">{{ $student?->identifier ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 line-clamp-1 max-w-sm">{{ $session->topic }}</p>
                                        <p class="text-[11px] text-slate-400 line-clamp-1 max-w-sm">{{ $session->thesis?->title ?? '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($session->student_attendance_status === 'attending')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                Hadir
                                            </span>
                                        @elseif($session->student_attendance_status === 'permission')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800" title="{{ $session->student_attendance_reason }}">
                                                Izin
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($isOverdue)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                {{ $daysOver > 0 ? "Lewat {$daysOver} Hari" : "Lewat Hari Ini" }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800/60">
                                                Akan Datang
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($dosen)
                                                <button type="button" 
                                                        @click="openReminderModal(@js($dosen->id), @js($dosen->name), @js($dosen->phone_number), 1, [@js($session)])"
                                                        class="p-1.5 rounded-lg text-emerald-600 hover:text-white hover:bg-emerald-600 transition-colors"
                                                        title="Kirim Pengingat WA ke Dosen">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"></path></svg>
                                                </button>
                                            @endif
                                            <a href="{{ route('mentoring-sessions.edit', $session) }}" 
                                               class="px-2.5 py-1 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg transition-colors">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-16 text-center">
                                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-1">Semua Sesi Selesai</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Tidak ada sesi bimbingan yang tertunda untuk filter ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Alpine.js WhatsApp Reminder Modal -->
        <div x-show="isReminderModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true"
             @keydown.escape.window="closeReminderModal()">
            <!-- Backdrop -->
            <div x-show="isReminderModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" 
                 @click="closeReminderModal()"></div>

            <!-- Modal Panel -->
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div x-show="isReminderModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700"
                     @click.stop>

                    <form action="{{ route('monitoring.uncompleted-mentoring.remind') }}" method="POST">
                        @csrf
                        <input type="hidden" name="dosen_id" :value="modalData.dosenId">

                        <!-- Modal Header -->
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-gradient-to-r from-emerald-500/10 to-transparent">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/20">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900 dark:text-white" id="modal-title">
                                        Kirim Pengingat WhatsApp
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                        Pengingat penyelesaian sesi bimbingan kepada dosen
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="closeReminderModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 space-y-4">
                            <!-- Dosen Summary Card -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700 flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200 block" x-text="modalData.dosenName"></span>
                                    <span class="text-[11px] text-slate-400 font-mono" x-text="modalData.dosenPhone || 'Nomor WA belum diatur'"></span>
                                </div>
                                <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400" x-text="modalData.sessionCount + ' Sesi Tertunda'"></span>
                            </div>

                            <!-- Message Content -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Isi Pesan WhatsApp
                                </label>
                                <textarea name="message" 
                                          rows="8" 
                                          x-model="modalData.messageText"
                                          class="w-full text-xs font-medium rounded-2xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-emerald-500 focus:border-emerald-500 p-3 leading-relaxed shadow-2xs"></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Anda dapat menyesuaikan isi pesan di atas sebelum mengirimkannya.</p>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3">
                            <!-- WhatsApp Web Direct Link -->
                            <a :href="whatsappWebUrl" 
                               target="_blank" 
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                <span>Buka di WhatsApp Web / App</span>
                            </a>

                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        @click="closeReminderModal()" 
                                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path></svg>
                                    <span>Kirim Otomatis</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine Controller -->
    <script>
        function uncompletedMentoringPage() {
            return {
                isReminderModalOpen: false,
                modalData: {
                    dosenId: '',
                    dosenName: '',
                    dosenPhone: '',
                    sessionCount: 0,
                    messageText: '',
                },

                openReminderModal(dosenId, dosenName, dosenPhone, sessionCount, sessions) {
                    this.modalData.dosenId = dosenId;
                    this.modalData.dosenName = dosenName;
                    this.modalData.dosenPhone = dosenPhone;
                    this.modalData.sessionCount = sessionCount;

                    // Generate default message text
                    let sessionItems = '';
                    if (Array.isArray(sessions)) {
                        sessions.slice(0, 5).forEach((s) => {
                            const studentName = s.thesis && s.thesis.student ? s.thesis.student.name : 'Mahasiswa';
                            const studentNpm = s.thesis && s.thesis.student && s.thesis.student.identifier ? s.thesis.student.identifier : '';
                            const topic = s.topic || '-';
                            sessionItems += `• ${studentName} (${studentNpm}): "${topic}"\n`;
                        });
                        if (sessions.length > 5) {
                            sessionItems += `• ...dan ${sessions.length - 5} sesi lainnya.\n`;
                        }
                    }

                    const link = "{{ route('mentoring-sessions.index') }}";

                    this.modalData.messageText = `Yth. Bpk/Ibu *${dosenName}*,\n\nSalam takzim dari Program Studi FASILKOM UNSUB.\n\nBerdasarkan pantauan sistem SIBIMA, tercatat terdapat *${sessionCount} sesi bimbingan* yang statusnya belum diselesaikan atau belum diinput catatan/feedback hasil bimbingan:\n\n${sessionItems}\nMohon kesediaan Bpk/Ibu untuk memperbarui status dan menginput hasil bimbingan mahasiswa melalui tautan berikut:\n${link}\n\nTerima kasih banyak atas perhatian dan kerja sama Bpk/Ibu.\n_Program Studi FASILKOM UNSUB_`;

                    this.isReminderModalOpen = true;
                },

                closeReminderModal() {
                    this.isReminderModalOpen = false;
                },

                get formattedPhone() {
                    if (!this.modalData.dosenPhone) return '';
                    let p = this.modalData.dosenPhone.replace(/[^0-9]/g, '');
                    if (p.startsWith('0')) {
                        p = '62' + p.substring(1);
                    }
                    return p;
                },

                get whatsappWebUrl() {
                    const phone = this.formattedPhone;
                    const text = encodeURIComponent(this.modalData.messageText);
                    return `https://wa.me/${phone}?text=${text}`;
                }
            };
        }
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Log Aktivitas Sistem', 'route' => route('admin.logs')],
            ['label' => 'Monitoring Keaktifan Login', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full mx-auto transition-colors duration-300" x-data="userSessionModal()">
        <!-- Shared Header Navigation Tabs -->
        @include('logs.partials.tabs')

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Sesi Login Periode Ini -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-orange-500/10 dark:bg-orange-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Sesi Login</span>
                    <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-800/60 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($totalPeriodLogins) }}</span>
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500">kali</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5 font-medium">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                    <span>Periode {{ $currentPeriodLabel }}</span>
                </p>
            </div>

            <!-- User Paling Aktif (MVP) -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">User Teraktif (MVP)</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/60 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                </div>
                @if($mostActiveUser)
                    <div class="flex items-center gap-2">
                        <span class="text-base sm:text-lg font-black text-slate-900 dark:text-white truncate max-w-[170px]" title="{{ $mostActiveUser->name }}">
                            {{ $mostActiveUser->name }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 shrink-0">
                            {{ $mostActiveCount }}x
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 truncate font-medium">
                        {{ ucfirst($mostActiveUser->role) }} • {{ $mostActiveUser->identifier ?: $mostActiveUser->email }}
                    </p>
                @else
                    <div class="text-base font-bold text-slate-400 dark:text-slate-500">- Belum ada aktivitas -</div>
                    <p class="text-xs text-slate-400 mt-2 font-medium">Tidak ada login di periode ini</p>
                @endif
            </div>

            <!-- Civitas Unik Aktif -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Civitas Unik Aktif</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-800/60 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($uniqueActiveUsersCount) }}</span>
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500">user</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5 font-medium">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>Civitas berbeda yang login</span>
                </p>
            </div>

            <!-- Civitas Tidak Aktif / Belum Pernah -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-rose-500/10 dark:bg-rose-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Belum / Tidak Aktif</span>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-100 dark:border-rose-800/60 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($neverOrInactiveCount) }}</span>
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500">user</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5 font-medium">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    <span>Belum pernah / > 30 hari pasif</span>
                </p>
            </div>
        </div>

        <!-- Main Card with Tab Switcher & Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
            <!-- Header with Sub-tabs and Actions -->
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-700/80 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <!-- Sub-tab switcher -->
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-900/60 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                    <a href="{{ route('admin.logs.login-activity', array_merge(request()->query(), ['tab' => 'active', 'page' => 1])) }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-bold transition-all {{ $tab !== 'inactive' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                        <span>🏆 Peringkat Paling Aktif</span>
                    </a>
                    <a href="{{ route('admin.logs.login-activity', array_merge(request()->query(), ['tab' => 'inactive', 'page' => 1])) }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-bold transition-all {{ $tab === 'inactive' ? 'bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 shadow-xs' : 'text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400' }}">
                        <span>⚠️ User Belum / Tidak Aktif</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'inactive' ? 'bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            {{ $neverOrInactiveCount }}
                        </span>
                    </a>
                </div>

                <!-- Action Toolbar: Excel Export & Quick Filters -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Export Excel Button -->
                    <a href="{{ route('admin.logs.login-activity.export', request()->query()) }}" 
                       class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Export Excel</span>
                    </a>
                </div>
            </div>

            <!-- Filters Toolbar Form -->
            <div class="p-4 sm:p-5 bg-slate-50/50 dark:bg-slate-900/30 border-b border-slate-100 dark:border-slate-700/80">
                <form action="{{ route('admin.logs.login-activity') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <input type="hidden" name="tab" value="{{ $tab }}">

                    <!-- Period Filter -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Periode Aktivitas</label>
                        <select name="period" onchange="this.form.submit()" class="w-full text-xs font-medium rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 shadow-2xs">
                            <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>Bulan Ini ({{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }})</option>
                            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="last_7_days" {{ $period === 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                            <option value="last_30_days" {{ $period === 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                            <option value="all_time" {{ $period === 'all_time' ? 'selected' : '' }}>Semua Waktu</option>
                        </select>
                    </div>

                    <!-- Role Filter -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Peran Civitas</label>
                        <select name="role" onchange="this.form.submit()" class="w-full text-xs font-medium rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 shadow-2xs">
                            <option value="all" {{ $role === 'all' ? 'selected' : '' }}>Semua Peran</option>
                            <option value="mahasiswa" {{ $role === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="dosen" {{ $role === 'dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="kaprodi" {{ $role === 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Cari Civitas</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}" 
                                   placeholder="Cari nama, email, NIM atau NIDN..." 
                                   class="w-full pl-9 pr-20 text-xs font-medium rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 shadow-2xs">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <div class="absolute inset-y-0 right-0 pr-1.5 flex items-center gap-1">
                                @if($search || $role !== 'all' || $period !== 'this_month')
                                    <a href="{{ route('admin.logs.login-activity', ['tab' => $tab]) }}" class="px-2 py-1 text-[11px] font-bold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-700 rounded-lg">
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

            <!-- Content Tables -->
            @if($tab !== 'inactive')
                <!-- TAB 1: LEADERBOARD USER TERAKTIF -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[10px] text-slate-500 dark:text-slate-400 uppercase bg-slate-50/80 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-700 font-black tracking-wider">
                            <tr>
                                <th scope="col" class="py-4 px-6 text-center w-16">Peringkat</th>
                                <th scope="col" class="py-4 px-6">Pengguna</th>
                                <th scope="col" class="py-4 px-6">Peran</th>
                                <th scope="col" class="py-4 px-6 text-center">Login Periode Ini</th>
                                <th scope="col" class="py-4 px-6 text-center">Total All-Time</th>
                                <th scope="col" class="py-4 px-6">Login Terakhir</th>
                                <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                            @forelse($users as $index => $user)
                                @php
                                    $rank = ($users->currentPage() - 1) * $users->perPage() + $index + 1;
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/40 transition-colors group">
                                    <!-- Peringkat Medal/Badge -->
                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        @if($rank === 1)
                                            <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400 font-black text-sm shadow-xs border border-amber-300 dark:border-amber-700" title="Juara 1 Login">
                                                🥇
                                            </div>
                                        @elseif($rank === 2)
                                            <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-black text-sm shadow-xs border border-slate-300 dark:border-slate-600" title="Juara 2 Login">
                                                🥈
                                            </div>
                                        @elseif($rank === 3)
                                            <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-500 font-black text-sm shadow-xs border border-amber-200 dark:border-amber-800" title="Juara 3 Login">
                                                🥉
                                            </div>
                                        @else
                                            <span class="text-xs font-black text-slate-400 dark:text-slate-500">
                                                #{{ $rank }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- User Profile & Online Status -->
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="relative shrink-0">
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-2xs">
                                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dark:border-slate-800 {{ $user->is_online ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}" title="{{ $user->is_online ? 'Sedang Online' : 'Offline' }}"></span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                                        {{ $user->name }}
                                                    </span>
                                                    @if($user->is_online)
                                                        <span class="px-1.5 py-0.2 rounded-md text-[9px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                            Online
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                                                    {{ $user->identifier ? $user->identifier . ' • ' : '' }}{{ $user->email }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Role Badge -->
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @php
                                            $roleBadgeClass = match($user->role) {
                                                'mahasiswa' => 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800/60',
                                                'dosen' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                                                'kaprodi' => 'bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800/60',
                                                'admin' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                                default => 'bg-slate-100 text-slate-700'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $roleBadgeClass }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>

                                    <!-- Login Periode Ini -->
                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black {{ $user->period_logins_count > 0 ? 'bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 border border-orange-200/80 dark:border-orange-800/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                            <span>{{ number_format($user->period_logins_count) }}</span>
                                            <span class="text-[10px] font-bold">kali</span>
                                        </span>
                                    </td>

                                    <!-- Total All-Time -->
                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                            {{ number_format($user->total_logins_count) }} kali
                                        </span>
                                    </td>

                                    <!-- Login Terakhir -->
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($user->last_login_at)
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                                {{ $user->last_login_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                            </p>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                                {{ $user->last_login_at->locale('id')->diffForHumans() }}
                                            </p>
                                        @else
                                            <span class="text-xs italic text-slate-400 dark:text-slate-500">Belum pernah login</span>
                                        @endif
                                    </td>

                                    <!-- Aksi Button -->
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <button type="button" 
                                                @click="openModal({{ $user->id }})" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-orange-500 hover:text-white dark:bg-slate-700/60 dark:hover:bg-orange-600 transition-all shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Riwayat Sesi</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-orange-500 flex items-center justify-center mb-3">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                            </div>
                                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Tidak ada data login</h3>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm">Tidak ditemukan aktivitas login untuk filter periode dan peran yang dipilih.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <!-- TAB 2: USER BELUM / TIDAK AKTIF -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[10px] text-slate-500 dark:text-slate-400 uppercase bg-slate-50/80 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-700 font-black tracking-wider">
                            <tr>
                                <th scope="col" class="py-4 px-6 text-center w-14">No</th>
                                <th scope="col" class="py-4 px-6">Pengguna</th>
                                <th scope="col" class="py-4 px-6">Peran</th>
                                <th scope="col" class="py-4 px-6">Status Keaktifan</th>
                                <th scope="col" class="py-4 px-6">Login Terakhir</th>
                                <th scope="col" class="py-4 px-6 text-center">Kontak WhatsApp</th>
                                <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                            @forelse($users as $index => $user)
                                @php
                                    $rowNo = ($users->currentPage() - 1) * $users->perPage() + $index + 1;
                                    $isNever = is_null($user->last_login_at);
                                    $daysInactive = $user->last_login_at ? $user->last_login_at->diffInDays(now()) : null;
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/40 transition-colors group">
                                    <td class="py-4 px-6 text-center text-xs font-bold text-slate-400">
                                        {{ $rowNo }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-2xs">
                                            <div>
                                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $user->name }}</p>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                                                    {{ $user->identifier ? $user->identifier . ' • ' : '' }}{{ $user->email }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($isNever)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                                Belum Pernah Login
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Pasif ({{ $daysInactive }} Hari Lalu)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($user->last_login_at)
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                                {{ $user->last_login_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                            </p>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                                {{ $user->last_login_at->locale('id')->diffForHumans() }}
                                            </p>
                                        @else
                                            <span class="text-xs italic text-rose-400 dark:text-rose-500 font-medium">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        @if($user->phone_number)
                                            @php
                                                $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $user->phone_number));
                                                $waText = urlencode("Halo {$user->name}, kami dari Program Studi mengingatkan untuk aktif mengakses akun sistem SIBIMA Anda untuk memantau aktivitas akademik skripsi.");
                                            @endphp
                                            <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-600 hover:text-white text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-xl border border-emerald-200 dark:border-emerald-800 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"></path></svg>
                                                <span>{{ $user->phone_number }}</span>
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400 font-medium">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <button type="button" 
                                                @click="openModal({{ $user->id }})" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-orange-500 hover:text-white dark:bg-slate-700/60 dark:hover:bg-orange-600 transition-all shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Riwayat Sesi</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 flex items-center justify-center mb-3">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Semua user aktif login!</h3>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm">Tidak ada civitas yang belum pernah login atau pasif lebih dari 30 hari.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Pagination Footer -->
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-700/80 bg-slate-50/40 dark:bg-slate-900/20">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Alpine.js User Login Session Detail Modal -->
        <div x-show="isOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true"
             @keydown.escape.window="closeModal()">
            <!-- Backdrop -->
            <div x-show="isOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" 
                 @click="closeModal()"></div>

            <!-- Modal Panel -->
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div x-show="isOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200 dark:border-slate-700"
                     @click.stop>

                    <!-- Modal Header -->
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-gradient-to-r from-orange-500/5 to-transparent">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-md shadow-orange-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-900 dark:text-white" id="modal-title">
                                    Riwayat Sesi Login
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                    Detail log autentikasi, IP address & perangkat user
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <!-- Loading State -->
                        <div x-show="loading" class="py-16 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-orange-50 dark:bg-orange-950/40 text-orange-500 mb-3 animate-spin">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Memuat log sesi login...</p>
                        </div>

                        <!-- Data Content -->
                        <div x-show="!loading && userData">
                            <!-- User Card inside modal -->
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700/60 mb-5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="relative">
                                        <img :src="userData?.avatar_url" alt="" class="w-12 h-12 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow-xs">
                                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-slate-800"
                                              :class="userData?.is_online ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-black text-slate-900 dark:text-white" x-text="userData?.name"></span>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400" x-text="userData?.role"></span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                            <span x-text="userData?.identifier"></span> • <span x-text="userData?.email"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Status Saat Ini</span>
                                    <span class="text-xs font-black" :class="userData?.is_online ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500'" x-text="userData?.is_online ? '🟢 Online' : '⚪ Offline'"></span>
                                </div>
                            </div>

                            <!-- Mini Stat Counters inside modal -->
                            <div class="grid grid-cols-3 gap-3 mb-5">
                                <div class="p-3 rounded-xl bg-orange-50/60 dark:bg-orange-950/30 border border-orange-100 dark:border-orange-900/40 text-center">
                                    <span class="text-lg font-black text-orange-600 dark:text-orange-400 block" x-text="userData?.total_logins">0</span>
                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total All-Time</span>
                                </div>
                                <div class="p-3 rounded-xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/40 text-center">
                                    <span class="text-lg font-black text-blue-600 dark:text-blue-400 block" x-text="userData?.month_logins">0</span>
                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bulan Ini</span>
                                </div>
                                <div class="p-3 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 text-center">
                                    <span class="text-lg font-black text-emerald-600 dark:text-emerald-400 block" x-text="userData?.week_logins">0</span>
                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">7 Hari Lalu</span>
                                </div>
                            </div>

                            <!-- Timeline List of Sessions -->
                            <div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider mb-3 flex items-center justify-between">
                                    <span>20 Sesi Login Terakhir</span>
                                    <span class="text-[10px] font-bold text-slate-400">Urut waktu terbaru</span>
                                </h4>

                                <div class="max-h-64 overflow-y-auto space-y-2 pr-1 divide-y divide-slate-100 dark:divide-slate-700/60">
                                    <template x-for="session in sessions" :key="session.id">
                                        <div class="pt-2 pb-1 flex items-start justify-between gap-3 text-xs">
                                            <div class="flex items-start gap-2.5">
                                                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/80 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 mt-0.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-slate-800 dark:text-slate-200" x-text="session.created_at_formatted"></span>
                                                        <span class="text-[10px] font-medium text-slate-400" x-text="session.time_ago"></span>
                                                    </div>
                                                    <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                        <span class="font-semibold text-slate-600 dark:text-slate-300" x-text="session.device"></span>
                                                        <span>•</span>
                                                        <span class="font-mono text-[10px] bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300" x-text="session.ip_address"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/60 shrink-0">
                                                Sukses
                                            </span>
                                        </div>
                                    </template>

                                    <template x-if="sessions.length === 0">
                                        <div class="py-8 text-center text-xs text-slate-400">
                                            Belum ada catatan detail riwayat sesi untuk user ini.
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                        <button type="button" 
                                @click="closeModal()" 
                                class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-2xs">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js script component -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userSessionModal', () => ({
                isOpen: false,
                loading: false,
                userData: null,
                sessions: [],

                async openModal(userId) {
                    this.isOpen = true;
                    this.loading = true;
                    this.userData = null;
                    this.sessions = [];

                    try {
                        const url = `{{ url('/admin/logs/login-activity') }}/${userId}/history`;
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.userData = data.user;
                            this.sessions = data.sessions;
                        } else {
                            console.error('Failed to load session history');
                        }
                    } catch (e) {
                        console.error('Error fetching sessions:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                closeModal() {
                    this.isOpen = false;
                }
            }));
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Sidang', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-[11px] font-black uppercase tracking-widest flex items-center border border-emerald-100 dark:border-emerald-800/50 shadow-sm animate-pulse">
                <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(Auth::user()->role === 'dosen')
            <!-- DOSEN VIEW: Premium Detailed Schedule Cards -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm">
                <div>
                    <h2 class="text-lg font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Agenda & Detail Jadwal Sidang
                    </h2>
                </div>
                <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
                    <form action="{{ route('thesis-defense-schedules.index') }}" method="GET" class="relative group">
                        <select name="wave_id" onchange="this.form.submit()" 
                                class="pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm min-w-[240px]">
                            <option value="" {{ empty($selectedWaveId) ? 'selected' : '' }}>
                                📅 JADWAL MENDATANG (HARI INI+)
                            </option>
                            @foreach($waves as $wave)
                                <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                    {{ strtoupper($wave->name) }} {{ $wave->is_active ? '(AKTIF)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div class="space-y-8">
                @forelse($schedules as $schedule)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-md overflow-hidden transition-all hover:shadow-lg">
                        <!-- Session Header Banner -->
                        <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white space-y-4">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="space-y-1.5">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h3 class="text-lg font-black tracking-tight text-white uppercase">{{ $schedule->title }}</h3>
                                        
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 bg-orange-500/20 text-orange-400 border border-orange-500/30 rounded-full text-[10px] font-black uppercase tracking-widest">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ \Carbon\Carbon::parse($schedule->date)->locale('id')->translatedFormat('l, d F Y') }}
                                        </span>
                                </div>
                                <div class="flex items-center gap-2.5 shrink-0">
                                    <a href="{{ route('thesis-defense-schedules.export-pdf', $schedule) }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-md shadow-rose-900/30">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Cetak PDF
                                    </a>
                                </div>
                            </div>

                            <!-- Meta Info Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 border-t border-white/10 text-xs">
                                <div class="flex items-center gap-2.5 px-3 py-2 bg-white/5 rounded-xl border border-white/5">
                                    <div class="w-7 h-7 rounded-lg bg-orange-500/20 text-orange-400 flex items-center justify-center font-bold text-[10px]">K</div>
                                    <div class="truncate">
                                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Ketua Sidang</span>
                                        <span class="font-bold text-slate-100 truncate block">{{ $schedule->chairman?->name ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5 px-3 py-2 bg-white/5 rounded-xl border border-white/5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-[10px]">M</div>
                                    <div class="truncate">
                                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Moderator</span>
                                        <span class="font-bold text-slate-100 truncate block">{{ $schedule->moderator?->name ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5 px-3 py-2 bg-white/5 rounded-xl border border-white/5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <div class="truncate">
                                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Lokasi / Ruangan</span>
                                        <span class="font-bold text-slate-100 truncate block">{{ $schedule->location ?: 'BELUM DIATUR' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5 px-3 py-2 bg-white/5 rounded-xl border border-white/5">
                                    <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="truncate">
                                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Virtual Meeting</span>
                                        @if($schedule->meeting_link)
                                            <a href="{{ $schedule->meeting_link }}" target="_blank" class="font-bold text-sky-300 hover:text-white hover:underline truncate block">Buka Link Meeting &rarr;</a>
                                        @else
                                            <span class="font-bold text-slate-500 truncate block">Belum ada link</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Presentation Table -->
                        <div class="p-6">
                            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-100/80 dark:bg-slate-900/80 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-slate-200 dark:border-slate-700">
                                            <th class="py-4 px-4 w-12 text-center border-r border-slate-200 dark:border-slate-700">NO</th>
                                            <th class="py-4 px-4 w-32 text-center border-r border-slate-200 dark:border-slate-700">WAKTU</th>
                                            <th class="py-4 px-6 border-r border-slate-200 dark:border-slate-700">PESERTA & JUDUL SKRIPSI</th>
                                            <th class="py-4 px-5 w-52 border-r border-slate-200 dark:border-slate-700">DOSEN PEMBIMBING</th>
                                            <th class="py-4 px-5 w-52">DOSEN PENGUJI</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                                        @php $studentNo = 1; @endphp
                                        @forelse($schedule->details as $detail)
                                            @if($detail->thesis_id)
                                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors">
                                                    <td class="py-4 px-4 text-center border-r border-slate-100 dark:border-slate-700/50">
                                                        <span class="inline-flex items-center justify-center w-7 h-7 bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-black rounded-lg text-xs">
                                                            {{ $studentNo++ }}
                                                        </span>
                                                    </td>
                                                    <td class="py-4 px-4 text-center border-r border-slate-100 dark:border-slate-700/50">
                                                        <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800/40 rounded-lg font-bold text-[11px] tracking-tight whitespace-nowrap">
                                                            <svg class="w-3 h-3 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}
                                                        </span>
                                                    </td>
                                                    <td class="py-4 px-6 border-r border-slate-100 dark:border-slate-700/50">
                                                        <div class="space-y-2">
                                                            <div class="flex items-center flex-wrap gap-2">
                                                                <span class="px-2.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-black text-[10px] rounded uppercase tracking-wider">
                                                                    {{ $detail->thesis->student->identifier }}
                                                                </span>
                                                                <span class="font-bold text-slate-800 dark:text-slate-100 text-xs">{{ $detail->thesis->student->name }}</span>
                                                            </div>
                                                            <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed bg-slate-50/50 dark:bg-slate-900/30 p-2.5 rounded-lg border border-slate-100 dark:border-slate-700/40">
                                                                {{ $detail->thesis->title }}
                                                            </p>
                                                        </div>
                                                    </td>
                                                    <td class="py-4 px-5 border-r border-slate-100 dark:border-slate-700/50">
                                                        <div class="space-y-2 text-xs">
                                                            <div class="flex items-start gap-2">
                                                                <span class="px-1.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-[9px] rounded border border-amber-500/20 shrink-0 mt-0.5">P1</span>
                                                                <span class="font-semibold text-slate-800 dark:text-slate-200 leading-tight">{{ $detail->thesis->pembimbing1->name }}</span>
                                                            </div>
                                                            @if($detail->thesis->pembimbing2)
                                                                <div class="flex items-start gap-2">
                                                                    <span class="px-1.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-[9px] rounded border border-amber-500/20 shrink-0 mt-0.5">P2</span>
                                                                    <span class="font-semibold text-slate-800 dark:text-slate-200 leading-tight">{{ $detail->thesis->pembimbing2->name }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="py-4 px-5">
                                                        <div class="space-y-2 text-xs">
                                                            @if($detail->examiner1)
                                                                <div class="flex items-start gap-2">
                                                                    <span class="px-1.5 py-0.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-black text-[9px] rounded border border-indigo-500/20 shrink-0 mt-0.5">U1</span>
                                                                    <span class="font-semibold text-slate-800 dark:text-slate-200 leading-tight">{{ $detail->examiner1->name }}</span>
                                                                </div>
                                                            @endif
                                                            @if($detail->examiner2)
                                                                <div class="flex items-start gap-2">
                                                                    <span class="px-1.5 py-0.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-black text-[9px] rounded border border-indigo-500/20 shrink-0 mt-0.5">U2</span>
                                                                    <span class="font-semibold text-slate-800 dark:text-slate-200 leading-tight">{{ $detail->examiner2->name }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr class="bg-slate-50/80 dark:bg-slate-900/60">
                                                    <td class="py-3 px-4 text-center border-r border-slate-100 dark:border-slate-700/50 font-bold text-slate-400">#</td>
                                                    <td class="py-3 px-4 text-center border-r border-slate-100 dark:border-slate-700/50">
                                                        <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg font-bold text-[11px] text-slate-600 dark:text-slate-400 whitespace-nowrap border border-slate-200 dark:border-slate-700">
                                                            <svg class="w-3 h-3 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}
                                                        </span>
                                                    </td>
                                                    <td colspan="3" class="py-3 px-6">
                                                        <span class="inline-flex items-center text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest bg-slate-200/50 dark:bg-slate-800 px-3 py-1 rounded-full border border-slate-200/60 dark:border-slate-700/50">
                                                            <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ $detail->activity_name }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-6 text-center text-xs text-slate-400 italic">Belum ada rincian peserta untuk jadwal ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm space-y-3">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-800 dark:text-white">
                            {{ empty($selectedWaveId) ? 'Tidak Ada Jadwal Sidang Aktif / Mendatang' : 'Belum Ada Jadwal Pada Gelombang Ini' }}
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed font-medium">
                            @if(empty($selectedWaveId))
                                Jadwal sidang pada tanggal pelaksanaan sebelumnya telah selesai. Untuk melihat riwayat agenda pelaksanaan terdahulu, silakan <strong>pilih Gelombang Pelaksanaan</strong> pada menu pilihan di atas.
                            @else
                                Tidak ditemukan agenda pelaksanaan sidang skripsi yang terjadwal untuk gelombang yang dipilih.
                            @endif
                        </p>
                    </div>
                @endforelse

                <div class="mt-4">
                    {{ $schedules->links() }}
                </div>
            </div>
        @else
            <!-- ADMIN & KAPRODI VIEW: Original Compact Table -->
            <x-table-card 
                title="Agenda Pelaksanaan Sidang Skripsi"
                :footer="$schedules->links()">
                
                <x-slot name="headerActions">
                    <div class="flex items-center gap-4">
                        <form action="{{ route('thesis-defense-schedules.index') }}" method="GET" class="relative group">
                            <select name="wave_id" onchange="this.form.submit()" 
                                    class="pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm min-w-[200px]">
                                <option value="">SEMUA GELOMBANG</option>
                                @foreach($waves as $wave)
                                    <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                        {{ strtoupper($wave->name) }} {{ $wave->is_active ? '(AKTIF)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <a href="{{ route('thesis-defense-schedules.create') }}" 
                            class="inline-flex items-center px-6 py-2.5 bg-orange-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-500/20">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Buat Jadwal
                        </a>
                    </div>
                </x-slot>

                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                            <th class="py-4 px-6">Informasi Sesi</th>
                            <th class="py-4 px-6">Waktu & Tanggal</th>
                            <th class="py-4 px-6">Pimpinan Sidang</th>
                            <th class="py-4 px-6">Lokasi / Tautan</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($schedules as $schedule)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors group">
                                <td class="py-4 px-6">
                                    <p class="text-[11px] font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $schedule->title }}</p>
                                    <p class="text-[9px] text-slate-500 dark:text-slate-500 mt-1 uppercase font-black tracking-widest">Oleh: {{ $schedule->creator->name }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-black text-slate-800 dark:text-slate-100 uppercase tracking-tighter">{{ \Carbon\Carbon::parse($schedule->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                        <span class="text-[9px] text-indigo-600 dark:text-indigo-400 uppercase font-black tracking-[0.2em] mt-0.5">{{ \Carbon\Carbon::parse($schedule->date)->locale('id')->translatedFormat('l') }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="w-14 text-[8px] text-slate-400 uppercase font-black tracking-widest">Ketua</span>
                                            <span class="text-[10px] text-slate-700 dark:text-slate-300 font-black uppercase tracking-tighter">{{ $schedule->chairman?->name ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-14 text-[8px] text-slate-400 uppercase font-black tracking-widest">Moderator</span>
                                            <span class="text-[10px] text-slate-700 dark:text-slate-300 font-black uppercase tracking-tighter">{{ $schedule->moderator?->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-2 items-start">
                                        <span class="px-2 py-1 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-[9px] font-black text-slate-600 dark:text-slate-400 uppercase tracking-widest">
                                            {{ $schedule->location ?: 'BELUM DIATUR' }}
                                        </span>
                                        @if($schedule->meeting_link)
                                            <a href="{{ $schedule->meeting_link }}" target="_blank" class="inline-flex items-center text-[8px] font-black text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-2 py-1 rounded-lg border border-blue-100 dark:border-blue-500/20 hover:bg-blue-100 transition-all uppercase tracking-widest">
                                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                Meeting Link
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <a href="{{ route('thesis-defense-schedules.export-pdf', $schedule) }}" class="p-2 text-slate-400 hover:text-rose-600 transition-all group/pdf" title="Export PDF">
                                            <svg class="w-5 h-5 group-hover/pdf:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                        <a href="{{ route('thesis-defense-schedules.show', $schedule) }}" class="p-2 text-slate-400 hover:text-indigo-600 transition-all group/view" title="Detail">
                                            <svg class="w-5 h-5 group-hover/view:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('thesis-defense-schedules.edit', $schedule) }}" class="p-2 text-slate-400 hover:text-amber-600 transition-all group/edit" title="Edit">
                                            <svg class="w-5 h-5 group-hover/edit:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('thesis-defense-schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition-all group/del" title="Hapus">
                                                <svg class="w-5 h-5 group-hover/del:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="5" description="Agenda pelaksanaan sidang skripsi belum tersedia untuk gelombang ini." icon="calendar" />
                        @endforelse
                    </tbody>
                </table>
            </x-table-card>
        @endif
    </div>
</x-app-layout>

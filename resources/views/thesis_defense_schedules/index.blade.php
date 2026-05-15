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
                                        <span class="text-[10px] text-slate-700 dark:text-slate-300 font-black uppercase tracking-tighter">{{ $schedule->chairman->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-14 text-[8px] text-slate-400 uppercase font-black tracking-widest">Moderator</span>
                                        <span class="text-[10px] text-slate-700 dark:text-slate-300 font-black uppercase tracking-tighter">{{ $schedule->moderator->name }}</span>
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
    </div>
</x-app-layout>

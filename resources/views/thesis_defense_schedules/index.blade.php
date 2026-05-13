<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Sidang', 'route' => null]
        ]" />
        <div class="flex justify-end items-center w-full">
            <a href="{{ route('thesis-defense-schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Jadwal Baru
            </a>
        </div>
    </x-slot>

    <div class="w-full mx-auto">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-sm flex items-center border border-emerald-100 dark:border-emerald-800/50 shadow-sm">
                <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm border border-slate-100 dark:border-slate-700 rounded-lg">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 uppercase tracking-widest">Daftar Jadwal Sidang</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase font-medium">Manajemen seluruh jadwal sidang yang telah diterbitkan</p>
                </div>

                <div class="flex items-center gap-3">
                    <form action="{{ route('thesis-defense-schedules.index') }}" method="GET" class="relative group">
                        <select name="wave_id" onchange="this.form.submit()" 
                                class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm min-w-[200px] text-slate-700 dark:text-slate-300">
                            <option value="">Semua Gelombang</option>
                            @foreach($waves as $wave)
                                <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                    {{ $wave->name }} {{ $wave->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-500 dark:text-slate-400 uppercase bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 font-bold">
                        <tr>
                            <th scope="col" class="py-4 px-6">Judul / Sesi</th>
                            <th scope="col" class="py-4 px-6">Tanggal</th>
                            <th scope="col" class="py-4 px-6">Pimpinan</th>
                            <th scope="col" class="py-4 px-6">Lokasi</th>
                            <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                        @forelse($schedules as $schedule)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group">
                                <td class="py-4 px-6">
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $schedule->title }}</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 uppercase tracking-tighter">Oleh: {{ $schedule->creator->name }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ \Carbon\Carbon::parse($schedule->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-medium">{{ \Carbon\Carbon::parse($schedule->date)->locale('id')->translatedFormat('l') }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-1">
                                        <div class="flex items-center text-[11px]">
                                            <span class="w-20 text-slate-400 uppercase font-black tracking-tighter">Ketua:</span>
                                            <span class="text-slate-700 dark:text-slate-300 font-bold">{{ $schedule->chairman->name }}</span>
                                        </div>
                                        <div class="flex items-center text-[11px]">
                                            <span class="w-20 text-slate-400 uppercase font-black tracking-tighter">Moderator:</span>
                                            <span class="text-slate-700 dark:text-slate-300 font-bold">{{ $schedule->moderator->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                            {{ $schedule->location ?: '-' }}
                                        </span>
                                        @if($schedule->meeting_link)
                                            <a href="{{ $schedule->meeting_link }}" target="_blank" class="inline-flex items-center text-[9px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded border border-blue-100 dark:border-blue-800/50 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-all gap-1 mt-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                Buka Tautan
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('thesis-defense-schedules.export-pdf', $schedule) }}" class="p-2 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Download PDF">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                        <a href="{{ route('thesis-defense-schedules.show', $schedule) }}" class="p-2 text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors" title="Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('thesis-defense-schedules.edit', $schedule) }}" class="p-2 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('thesis-defense-schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700 shadow-inner">
                                        <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-widest">Belum Ada Jadwal</h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 uppercase font-black">Klik tombol di atas untuk membuat jadwal sidang pertama</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($schedules->hasPages())
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
            Logbook Mahasiswa Bimbingan
        </h2>
        <div class="hidden md:block mt-3 sm:mt-0 text-sm text-slate-500 dark:text-slate-400">
            Daftar mahasiswa bimbingan Anda beserta jumlah sesi yang telah diselesaikan.
        </div>
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Daftar Mahasiswa</h3>
                    <p class="text-slate-500 dark:text-slate-400 mt-1 text-xs">Pilih mahasiswa untuk memantau logbook bimbingan mereka.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <!-- Search Input -->
                    <form action="{{ route('logbooks.index') }}" method="GET" class="relative w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, NPM, atau judul..." class="block w-full sm:w-64 pl-10 pr-10 py-1.5 border border-slate-200 dark:border-slate-700 rounded-md leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:bg-white dark:focus:bg-slate-800 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition-colors">
                        @if(isset($search) && $search !== '')
                            <a href="{{ route('logbooks.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider">MAHASISWA</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider">RENCANA JUDUL SKRIPSI</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider text-center">TOTAL SESI (SELESAI)</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($theses as $thesis)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors group">
                                <td class="py-4 px-6">
                                    <div class="font-medium text-slate-800 dark:text-slate-100">{{ $thesis->student->name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $thesis->student->identifier ?? 'NPM Tidak Ada' }}</div>
                                </td>
                                <td class="py-4 px-6 max-w-sm whitespace-normal">
                                    <div class="font-medium text-slate-700 dark:text-slate-300 line-clamp-2" title="{{ $thesis->final_title ?? $thesis->title }}">{{ $thesis->final_title ?? $thesis->title }}</div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 rounded-full text-xs font-bold {{ $thesis->completed_sessions_count > 0 ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                        {{ $thesis->completed_sessions_count }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('theses.logbooks', $thesis->id) }}" class="inline-flex px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-orange-600 dark:hover:text-orange-400 hover:border-orange-300 dark:hover:border-orange-800 rounded text-xs font-medium transition-colors shadow-sm">
                                        Lihat Logbook
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                                        <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-100">Tidak ada mahasiswa bimbingan</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest font-black">Data akan muncul setelah pembimbing ditugaskan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($theses->hasPages())
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    {{ $theses->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

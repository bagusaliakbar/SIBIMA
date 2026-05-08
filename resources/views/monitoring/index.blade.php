<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Monitoring', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Status Progres Mahasiswa</h3>
                
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <!-- Search Input -->
                    <form action="{{ route('monitoring.index') }}" method="GET" class="relative w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari mahasiswa..." class="block w-full sm:w-64 pl-10 pr-10 py-1.5 border border-slate-200 dark:border-slate-700 rounded-md leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:bg-white dark:focus:bg-slate-800 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition-colors">
                        @if(isset($search) && $search !== '')
                            <a href="{{ route('monitoring.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </form>
                    
                    <a href="{{ route('monitoring.export') }}" class="px-4 py-1.5 bg-emerald-600 text-white text-xs font-bold rounded hover:bg-emerald-700 transition-all shadow-sm whitespace-nowrap flex items-center gap-2 group">
                        <svg class="w-4 h-4 text-emerald-100 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Data Seminar/Sidang
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">MAHASISWA</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center">TOTAL BIMBINGAN</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">PEMBIMBING</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center">STATUS ACC UP</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center">STATUS ACC SIDANG</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($theses as $thesis)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors align-top">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800 dark:text-slate-100">{{ $thesis->student->name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 tracking-tight uppercase">{{ $thesis->student->identifier }}</div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500 mt-1 truncate max-w-[250px]" title="{{ $thesis->title }}">{{ $thesis->title }}</div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $thesis->total_sessions >= 8 ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : ($thesis->total_sessions >= 4 ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400') }} font-bold text-xs">
                                            {{ $thesis->total_sessions }}
                                        </span>
                                        <div class="flex gap-1">
                                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50 px-1 rounded border border-slate-100 dark:border-slate-700" title="Bimbingan P1">Pembimbing 1: {{ $thesis->sessions_p1 }}x</span>
                                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50 px-1 rounded border border-slate-100 dark:border-slate-700" title="Bimbingan P2">Pembimbing 2: {{ $thesis->sessions_p2 }}x</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 text-[8px] flex items-center justify-center font-bold text-slate-500 dark:text-slate-400">1</span>
                                            <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">{{ $thesis->pembimbing1->name ?? 'Belum Ditugaskan' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 text-[8px] flex items-center justify-center font-bold text-slate-500 dark:text-slate-400">2</span>
                                            <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">{{ $thesis->pembimbing2->name ?? 'Belum Ditugaskan' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="flex gap-1">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $thesis->acc_up_p1 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">Pembimbing 1</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $thesis->acc_up_p2 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">Pembimbing 2</span>
                                        </div>
                                        @if($thesis->isAccUpFinal())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-tight">SIAP SEMINAR</span>
                                        @else
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Progres...</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="flex gap-1">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $thesis->acc_sidang_p1 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">Pembimbing 1</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $thesis->acc_sidang_p2 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">Pembimbing 2</span>
                                        </div>
                                        @if($thesis->isAccSidangFinal())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-tight">SIAP SIDANG</span>
                                        @else
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Progres...</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <a href="{{ route('theses.logbooks', $thesis->id) }}" class="inline-flex items-center px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-orange-600 transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                                        <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Tidak ada data ditemukan</h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest font-black">Coba kata kunci pencarian yang berbeda</p>
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

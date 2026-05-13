<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Logbook Bimbingan', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Riwayat Catatan Logbook</h3>
                
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <!-- Search Input -->
                    <form action="{{ route('logbooks.index') }}" method="GET" class="relative w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari topik atau catatan..." class="block w-full sm:w-64 pl-10 pr-10 py-1.5 border border-slate-200 dark:border-slate-700 rounded-md leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition-colors">
                        @if(isset($search) && $search !== '')
                            <a href="{{ route('logbooks.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('logbooks.export-pdf') }}" class="px-3 py-1.5 text-xs font-medium border border-slate-200 dark:border-slate-700 rounded text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm whitespace-nowrap inline-flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export PDF
                    </a>
                </div>
            </div>

            <div class="p-6">
                @php
                    $thesis = Auth::user()->thesis;
                @endphp

                @if($thesis)
                <div class="mb-8 p-5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Dosen Pembimbing</h4>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center">
                                <span class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-500 flex items-center justify-center mr-2.5 text-[10px] font-bold border border-orange-200 dark:border-orange-800">1</span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $thesis->pembimbing1->name ?? '-' }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center mr-2.5 text-[10px] font-bold border border-slate-300 dark:border-slate-600">2</span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $thesis->pembimbing2->name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Status Skripsi</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 capitalize">
                            {{ $thesis->status }}
                        </span>
                    </div>
                </div>
                @endif

                <div class="space-y-6">
                    @forelse($sessions as $session)
                        <div class="flex flex-col md:flex-row gap-4 p-5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-orange-200 dark:hover:border-orange-900/50 hover:shadow-sm transition-all">
                            
                            <!-- Date & Time Column -->
                            <div class="md:w-48 shrink-0">
                                <div class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Waktu Bimbingan</div>
                                <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</div>
                                <div class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $session->scheduled_at->format('H:i') }} WIB</div>
                                
                            </div>

                            <!-- Content Column -->
                            <div class="flex-1 min-w-0 md:border-l md:border-slate-100 dark:md:border-slate-700 md:pl-6">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">{{ $session->topic }}</h4>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/50 uppercase tracking-wider ml-2 shrink-0">
                                        Selesai
                                    </span>
                                </div>
                                
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <h5 class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 flex items-center">
                                            <svg class="w-3 h-3 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Hasil & Catatan Pembimbing
                                        </h5>
                                        <div class="bg-slate-50 dark:bg-slate-900 rounded p-4 border border-slate-100 dark:border-slate-700 text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed font-medium">
                                            {{ $session->feedback ?: 'Tidak ada catatan pembimbing untuk sesi ini.' }}
                                        </div>
                                    </div>
                                    
                                    @if($session->notes)
                                    <div>
                                        <h5 class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 flex items-center">
                                            Catatan Tambahan Pengajuan
                                        </h5>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 italic pl-2 border-l-2 border-slate-200 dark:border-slate-700">
                                            "{{ $session->notes }}"
                                        </div>
                                    </div>
                                    @endif

                                    @if($session->dosen)
                                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700">
                                        <div class="inline-flex items-center px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                            <svg class="w-3 h-3 mr-1.5 text-slate-400 dark:text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                            <span class="text-[10px] font-bold uppercase tracking-tight mr-1">Bimbingan dengan:</span>
                                            <span class="text-[10px] font-extrabold text-slate-800 dark:text-slate-100">{{ $session->dosen->name }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 flex flex-col items-center justify-center text-slate-500 dark:text-slate-400 border border-dashed border-slate-300 dark:border-slate-700 rounded-lg">
                            <svg class="w-12 h-12 mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="font-medium text-sm text-slate-600 dark:text-slate-300">Belum ada data logbook bimbingan yang selesai.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            @if($sessions->hasPages())
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

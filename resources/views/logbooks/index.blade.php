<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Logbook Bimbingan', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/30 dark:bg-slate-900/30">
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Riwayat Catatan Logbook</h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold mt-1 uppercase">Manajemen riwayat bimbingan Anda</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari topik atau catatan..." 
                        route="logbooks.index" />

                    <a href="{{ route('logbooks.export-pdf') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export PDF
                    </a>
                </div>
            </div>

            <div class="p-6">
                @php
                    $thesis = Auth::user()->thesis;
                @endphp

                @if($thesis)
                <div class="mb-8 p-6 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-3">Dosen Pembimbing</h4>
                        <div class="flex flex-wrap gap-6">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mr-3 text-xs font-black border border-indigo-200 dark:border-indigo-500/20">1</div>
                                <div>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200 block">{{ $thesis->pembimbing1->name ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter">Pembimbing Utama</span>
                                </div>
                            </div>
                            <div class="flex items-center border-l border-slate-200 dark:border-slate-700 pl-6">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center mr-3 text-xs font-black border border-slate-200 dark:border-slate-700">2</div>
                                <div>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200 block">{{ $thesis->pembimbing2->name ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter">Pembimbing Pendamping</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] block mb-2">Status Skripsi</span>
                        <x-status-badge type="orange" :label="$thesis->status" />
                    </div>
                </div>
                @endif

                <div class="space-y-6">
                    @forelse($sessions as $session)
                        <div class="flex flex-col md:flex-row gap-6 p-6 rounded-2xl border border-slate-100 dark:border-slate-700/50 hover:border-indigo-200 dark:hover:border-indigo-500/30 hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-all group">
                            
                            <!-- Date & Time Column -->
                            <div class="md:w-48 shrink-0">
                                <div class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Waktu Bimbingan</div>
                                <div class="font-black text-sm text-slate-800 dark:text-slate-100 group-hover:text-indigo-600 transition-colors uppercase tracking-tight">{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-bold flex items-center">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $session->scheduled_at->format('H:i') }} WIB
                                </div>
                            </div>

                            <!-- Content Column -->
                            <div class="flex-1 min-w-0 md:border-l md:border-slate-100 dark:md:border-slate-700 md:pl-8">
                                <div class="flex items-start justify-between mb-4">
                                    <h4 class="text-lg font-black text-slate-800 dark:text-slate-100 truncate uppercase tracking-tight">{{ $session->topic }}</h4>
                                    <x-status-badge type="emerald" label="SELESAI" />
                                </div>
                                
                                <div class="space-y-5">
                                    <div>
                                        <h5 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2.5 flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Hasil & Catatan Pembimbing
                                        </h5>
                                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/50 text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed font-medium italic">
                                            {{ $session->feedback ?: 'Tidak ada catatan pembimbing untuk sesi ini.' }}
                                        </div>
                                    </div>
                                    
                                    @if($session->notes)
                                    <div class="pl-4 border-l-2 border-slate-100 dark:border-slate-700">
                                        <h5 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Catatan Mahasiswa:</h5>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 italic">"{{ $session->notes }}"</p>
                                    </div>
                                    @endif

                                    @if($session->dosen)
                                    <div class="pt-4 border-t border-slate-50 dark:border-slate-700/50">
                                        <div class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            <svg class="w-3.5 h-3.5 mr-2 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                            <span class="text-[10px] font-black uppercase tracking-tight mr-1.5">Bimbingan dengan:</span>
                                            <span class="text-[10px] font-black text-slate-800 dark:text-slate-100 uppercase">{{ $session->dosen->name }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-empty-state description="Belum ada data logbook bimbingan yang selesai." icon="logbook" />
                    @endforelse
                </div>

                @if($sessions->hasPages())
                    <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-700">
                        {{ $sessions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

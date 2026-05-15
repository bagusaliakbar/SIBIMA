<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Monitoring', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <x-table-card 
            title="Status Progres Mahasiswa"
            :footer="$theses->links()">
            
            <x-slot name="headerActions">
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari mahasiswa..." 
                        route="monitoring.index" />
                    

                    <a href="{{ route('monitoring.export') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-700 transition-all shadow-sm whitespace-nowrap gap-2 group">
                        <svg class="w-4 h-4 text-emerald-100 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Data
                    </a>
                </div>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-4 px-6 font-black text-[10px] uppercase tracking-wider whitespace-nowrap">MAHASISWA</th>
                            <th class="py-4 px-6 font-black text-[10px] uppercase tracking-wider whitespace-nowrap text-center">TOTAL BIMBINGAN</th>
                            <th class="py-4 px-6 font-black text-[10px] uppercase tracking-wider whitespace-nowrap">PEMBIMBING</th>
                            <th class="py-4 px-6 font-black text-[10px] uppercase tracking-wider whitespace-nowrap text-center">STATUS ACC UP</th>
                            <th class="py-4 px-6 font-black text-[10px] uppercase tracking-wider whitespace-nowrap text-center">STATUS ACC SIDANG</th>
                            <th class="py-4 px-6 font-black text-[10px] uppercase tracking-wider whitespace-nowrap text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($theses as $thesis)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors align-top group">
                                <td class="py-4 px-6">
                                    <div class="font-black text-sm text-slate-800 dark:text-slate-100 group-hover:text-indigo-600 transition-colors uppercase tracking-tight">{{ $thesis->student->name }}</div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 tracking-widest font-black uppercase">{{ $thesis->student->identifier }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 italic line-clamp-1 max-w-[250px]" title="{{ $thesis->title }}">{{ $thesis->title }}</div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl {{ $thesis->total_sessions >= 8 ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600' : ($thesis->total_sessions >= 4 ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-500') }} font-black text-xs border {{ $thesis->total_sessions >= 8 ? 'border-emerald-200 dark:border-emerald-500/20' : ($thesis->total_sessions >= 4 ? 'border-orange-200 dark:border-orange-500/20' : 'border-slate-200 dark:border-slate-700') }}">
                                            {{ $thesis->total_sessions }}
                                        </span>
                                        <div class="flex gap-1.5">
                                            <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50 px-1.5 py-0.5 rounded border border-slate-100 dark:border-slate-700 uppercase" title="Bimbingan P1">P1: {{ $thesis->sessions_p1 }}x</span>
                                            <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50 px-1.5 py-0.5 rounded border border-slate-100 dark:border-slate-700 uppercase" title="Bimbingan P2">P2: {{ $thesis->sessions_p2 }}x</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-5 h-5 rounded-md bg-indigo-100 dark:bg-indigo-500/10 text-[9px] flex items-center justify-center font-black text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20">1</div>
                                            <span class="text-[11px] text-slate-700 dark:text-slate-300 font-bold uppercase tracking-tight">{{ $thesis->pembimbing1->name ?? 'Belum Ditugaskan' }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-5 h-5 rounded-md bg-slate-100 dark:bg-slate-800 text-[9px] flex items-center justify-center font-black text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">2</div>
                                            <span class="text-[11px] text-slate-700 dark:text-slate-300 font-bold uppercase tracking-tight">{{ $thesis->pembimbing2->name ?? 'Belum Ditugaskan' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="flex gap-1">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase border {{ $thesis->acc_up_p1 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border-emerald-100 dark:border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">P1</span>
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase border {{ $thesis->acc_up_p2 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border-emerald-100 dark:border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">P2</span>
                                        </div>
                                        @if($thesis->isAccUpFinal())
                                            <x-status-badge type="emerald" label="SIAP SEMINAR" />
                                        @else
                                            <span class="text-[9px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest italic">Progres...</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="flex gap-1">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase border {{ $thesis->acc_sidang_p1 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border-emerald-100 dark:border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">P1</span>
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase border {{ $thesis->acc_sidang_p2 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border-emerald-100 dark:border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">P2</span>
                                        </div>
                                        @if($thesis->isAccSidangFinal())
                                            <x-status-badge type="emerald" label="SIAP SIDANG" />
                                        @else
                                            <span class="text-[9px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest italic">Progres...</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('theses.logbooks', $thesis->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="6" description="Coba kata kunci pencarian yang berbeda" icon="monitoring" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-table-card>
    </div>
</x-app-layout>

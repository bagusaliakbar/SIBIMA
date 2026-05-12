<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Monitoring Masa Studi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 dark:bg-red-500/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                <div class="relative">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Mahasiswa Kritikal</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-slate-100">{{ $students->total() }}</h3>
                    <p class="text-[10px] text-red-500 font-bold mt-2 flex items-center">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                        MEMERLUKAN INTERVENSI
                    </p>
                </div>
            </div>
            
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 md:col-span-2 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-1">Apa itu Masa Studi Kritikal?</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-md">Mahasiswa dikategorikan kritikal apabila telah memasuki <b>Semester 13 atau 14</b> dan belum menyelesaikan tahapan kelulusan. Halaman ini membantu prodi memantau mahasiswa yang berisiko Drop Out (DO).</p>
                </div>
                <div class="hidden lg:flex gap-3">
                    <div class="px-4 py-2 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20 text-center">
                        <p class="text-[10px] font-black text-red-600 dark:text-red-400 uppercase tracking-tighter">Batas Maksimal</p>
                        <p class="text-sm font-black text-red-700 dark:text-red-300">14 Semester</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50 dark:bg-slate-900/50">
                <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Daftar Mahasiswa Berisiko DO
                </h3>
                
                <form action="{{ route('monitoring.critical') }}" method="GET" class="relative w-full sm:w-auto">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama atau NPM..." class="block w-full sm:w-72 pl-10 pr-10 py-2 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 sm:text-sm transition-all shadow-sm">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest whitespace-nowrap uppercase">Mahasiswa</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest whitespace-nowrap uppercase text-center">Angkatan</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest whitespace-nowrap uppercase text-center">Semester</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest whitespace-nowrap uppercase">Tahapan Terakhir</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest whitespace-nowrap uppercase">Pembimbing 1</th>
                            <th class="py-4 px-6 font-black text-[10px] tracking-widest whitespace-nowrap uppercase text-center">Tingkat Risiko</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($students as $student)
                            @php 
                                $sem = $student->current_semester;
                                $riskColor = $sem >= 14 ? 'red' : 'orange';
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors align-middle">
                                <td class="py-5 px-6">
                                    <div class="font-bold text-slate-800 dark:text-slate-100">{{ $student->name }}</div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 tracking-tight uppercase font-black">{{ $student->identifier }}</div>
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-black uppercase">
                                        {{ $student->entry_year }}
                                    </span>
                                </td>
                                <td class="py-5 px-6 text-center font-black text-slate-700 dark:text-slate-200">
                                    {{ $sem }}
                                </td>
                                <td class="py-5 px-6">
                                    @php $thesis = $student->thesis; @endphp
                                    @if($thesis)
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-tighter">
                                            @if($thesis->status === 'completed') Lulus
                                            @elseif($thesis->acc_sidang_p1) Siap Sidang
                                            @elseif($thesis->acc_up_p1) Siap Penelitian
                                            @else Pengerjaan Bab 1-3
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 truncate max-w-[200px] italic">"{{ $thesis->title }}"</p>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">Belum Mengajukan Judul</span>
                                    @endif
                                </td>
                                <td class="py-5 px-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-black text-slate-500">
                                            {{ substr($student->thesis->pembimbing1->name ?? '?', 0, 1) }}
                                        </div>
                                        <span class="text-xs text-slate-600 dark:text-slate-300 font-bold tracking-tight">{{ $student->thesis->pembimbing1->name ?? 'Belum Ada' }}</span>
                                    </div>
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-{{ $riskColor }}-100 dark:bg-{{ $riskColor }}-500/10 text-{{ $riskColor }}-700 dark:text-{{ $riskColor }}-400 text-[9px] font-black uppercase tracking-widest border border-{{ $riskColor }}-200 dark:border-{{ $riskColor }}-500/20 shadow-sm">
                                            {{ $sem >= 14 ? 'Sangat Tinggi' : 'Tinggi' }}
                                        </span>
                                        @if($sem >= 14)
                                            <span class="text-[8px] text-red-500 font-black animate-pulse">DROP OUT RISK</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-20 text-center">
                                    <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-500/5 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-emerald-100 dark:border-emerald-500/10">
                                        <svg class="h-10 w-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase">Tidak Ada Mahasiswa Kritikal</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-sm mx-auto">Selamat! Saat ini tidak ditemukan mahasiswa yang berada di ambang batas masa studi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

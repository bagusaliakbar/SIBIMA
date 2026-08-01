<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Monitoring', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full">
        <!-- Chart Section -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Grafik Distribusi Bimbingan per Dosen
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pemetaan tahapan mahasiswa yang belum lulus disaring berdasarkan Dosen & Angkatan.</p>
                </div>

                <!-- Combined Filters for Chart & Table -->
                <form action="{{ route('monitoring.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <!-- Filter Angkatan -->
                    <select name="entry_year" onchange="this.form.submit()" class="py-2 px-3 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs font-semibold shadow-sm">
                        <option value="">-- Semua Angkatan --</option>
                        @foreach($entryYears as $year)
                            <option value="{{ $year }}" {{ ($entryYear ?? '') == $year ? 'selected' : '' }}>Angkatan {{ $year }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Dosen Pembimbing -->
                    <select name="pembimbing_id" onchange="this.form.submit()" class="py-2 px-3 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs font-semibold shadow-sm">
                        <option value="">-- Semua Dosen Pembimbing --</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}" {{ ($pembimbingId ?? '') == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                        @endforeach
                    </select>

                    @if(request('entry_year') || request('pembimbing_id'))
                        <a href="{{ route('monitoring.index') }}" class="px-3 py-2 text-xs font-bold text-red-500 hover:text-red-700 transition-colors">Reset Filter</a>
                    @endif
                </form>
            </div>

            <!-- Canvas Container -->
            <div class="h-64 sm:h-80 w-full relative">
                @if(count($chartData['labels']) > 0)
                    <canvas id="monitoringSupervisorChart"></canvas>
                @else
                    <div class="h-full w-full flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 py-12">
                        <svg class="w-12 h-12 mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <p class="text-xs font-semibold">Tidak ada data bimbingan aktif yang sesuai dengan filter.</p>
                    </div>
                @endif
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @if(count($chartData['labels']) > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('monitoringSupervisorChart');
                if (!ctx) return;

                const rawData = @json($chartData);

                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: rawData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                    font: { family: 'Inter', size: 11, weight: '600' }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: 'Inter', size: 12, weight: '700' },
                                bodyFont: { family: 'Inter', size: 11 },
                                padding: 10,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                grid: { display: false },
                                ticks: { font: { family: 'Inter', size: 10, weight: '600' } }
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                grid: { color: 'rgba(226, 232, 240, 0.5)' },
                                ticks: { stepSize: 1, font: { family: 'Inter', size: 10 } }
                            }
                        }
                    }
                });
            });
        </script>
        @endif

        <x-table-card 
            title="Status Progres Mahasiswa"
            :footer="$theses->links()">
            
            <x-slot name="headerActions">
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
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
                                        <div class="flex items-center gap-1">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase border {{ $thesis->acc_up_p1 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border-emerald-100 dark:border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">P1</span>
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase border {{ $thesis->acc_up_p2 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border-emerald-100 dark:border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">P2</span>
                                            @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                                <form action="{{ route('theses.toggle-acc', [$thesis->id, 'up']) }}" method="POST" class="inline" onsubmit="return confirm('Toggle ACC Seminar UP untuk {{ $thesis->student->name ?? '' }}?')">
                                                    @csrf
                                                    <input type="hidden" name="slot" value="all">
                                                    <button type="submit" title="Toggle ACC UP" class="ml-1 text-[10px] text-slate-400 hover:text-emerald-600">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
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
                                        <div class="flex items-center gap-1">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase border {{ $thesis->acc_sidang_p1 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border-emerald-100 dark:border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">P1</span>
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase border {{ $thesis->acc_sidang_p2 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border-emerald-100 dark:border-emerald-500/20' : 'bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 border-slate-100 dark:border-slate-700' }}">P2</span>
                                            @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                                <form action="{{ route('theses.toggle-acc', [$thesis->id, 'sidang']) }}" method="POST" class="inline" onsubmit="return confirm('Toggle ACC Sidang untuk {{ $thesis->student->name ?? '' }}?')">
                                                    @csrf
                                                    <input type="hidden" name="slot" value="all">
                                                    <button type="submit" title="Toggle ACC Sidang" class="ml-1 text-[10px] text-slate-400 hover:text-emerald-600">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
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

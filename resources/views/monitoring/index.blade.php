<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Monitoring', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full" x-data="monitoringAccModal()">
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
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/40 text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6 whitespace-nowrap">Mahasiswa</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Bimbingan</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Dosen Pembimbing</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status ACC UP</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status ACC Sidang</th>
                            <th class="py-3.5 px-6 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($theses as $thesis)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors align-top group">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors tracking-tight">
                                        {{ ucwords(strtolower($thesis->student->name)) }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-slate-400 dark:text-slate-500 mt-0.5 font-medium">
                                        <span class="font-mono">{{ $thesis->student->identifier }}</span>
                                        @if($thesis->student->entry_year)
                                            <span class="text-slate-300 dark:text-slate-700">•</span>
                                            <span>Angkatan {{ $thesis->student->entry_year }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1.5 line-clamp-2 max-w-[280px] leading-relaxed font-normal" title="{{ $thesis->final_title ?? $thesis->title }}">
                                        {{ $thesis->final_title ?? $thesis->title }}
                                    </p>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl font-bold text-xs {{ $thesis->total_sessions >= 8 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : ($thesis->total_sessions >= 4 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400') }}">
                                            {{ $thesis->total_sessions }}
                                        </span>
                                        <div class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400 dark:text-slate-500">
                                            <span class="px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800/80">P1: {{ $thesis->sessions_p1 }}x</span>
                                            <span class="px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800/80">P2: {{ $thesis->sessions_p2 }}x</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-[10px] font-bold text-indigo-600 dark:text-indigo-400 shrink-0">1</span>
                                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-100 tracking-tight">{{ $thesis->pembimbing1 ? ($thesis->pembimbing1->name === strtoupper($thesis->pembimbing1->name) ? ucwords(strtolower($thesis->pembimbing1->name)) : $thesis->pembimbing1->name) : 'Belum Ditugaskan' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-400 shrink-0">2</span>
                                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-100 tracking-tight">{{ $thesis->pembimbing2 ? ($thesis->pembimbing2->name === strtoupper($thesis->pembimbing2->name) ? ucwords(strtolower($thesis->pembimbing2->name)) : $thesis->pembimbing2->name) : 'Belum Ditugaskan' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <!-- Dual Micro-Pill Status -->
                                        <div class="inline-flex items-center p-0.5 rounded-full {{ $thesis->isAccUpFinal() ? 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/50' : 'bg-slate-100 dark:bg-slate-800/80' }} gap-0.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $thesis->acc_up_p1 ? 'bg-emerald-500 text-white shadow-2xs' : 'text-slate-400 dark:text-slate-500' }}" title="Pembimbing 1: {{ $thesis->acc_up_p1 ? 'Sudah ACC' : 'Belum ACC' }}">
                                                <svg class="w-2.5 h-2.5 {{ $thesis->acc_up_p1 ? 'text-white' : 'text-slate-300 dark:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $thesis->acc_up_p1 ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/></svg>
                                                <span>P1</span>
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $thesis->acc_up_p2 ? 'bg-emerald-500 text-white shadow-2xs' : 'text-slate-400 dark:text-slate-500' }}" title="Pembimbing 2: {{ $thesis->acc_up_p2 ? 'Sudah ACC' : 'Belum ACC' }}">
                                                <svg class="w-2.5 h-2.5 {{ $thesis->acc_up_p2 ? 'text-white' : 'text-slate-300 dark:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $thesis->acc_up_p2 ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/></svg>
                                                <span>P2</span>
                                            </span>
                                        </div>

                                        <!-- Overall Badge & Action Trigger -->
                                        <div class="inline-flex items-center gap-1">
                                            @if($thesis->isAccUpFinal())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Siap Seminar
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ ($thesis->acc_up_p1 || $thesis->acc_up_p2) ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                                                    <span>Progres</span>
                                                    <span class="font-bold text-[9px]">({{ ($thesis->acc_up_p1 ? 1 : 0) + ($thesis->acc_up_p2 ? 1 : 0) }}/2)</span>
                                                </span>
                                            @endif

                                            @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                                <button type="button" 
                                                        @click="openModal({
                                                            thesisId: {{ $thesis->id }},
                                                            type: 'up',
                                                            typeName: 'Seminar UP',
                                                            studentName: '{{ addslashes(ucwords(strtolower($thesis->student->name))) }}',
                                                            studentNpm: '{{ $thesis->student->identifier }}',
                                                            isFinal: {{ $thesis->isAccUpFinal() ? 'true' : 'false' }},
                                                            accP1: {{ $thesis->acc_up_p1 ? 'true' : 'false' }},
                                                            accP2: {{ $thesis->acc_up_p2 ? 'true' : 'false' }},
                                                            p1Name: '{{ addslashes($thesis->pembimbing1->name ?? 'Dosen 1') }}',
                                                            p2Name: '{{ addslashes($thesis->pembimbing2->name ?? 'Dosen 2') }}'
                                                        })"
                                                        class="w-5 h-5 rounded-md flex items-center justify-center text-slate-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-slate-800 transition-colors cursor-pointer" 
                                                        title="Aksi Cepat Kaprodi: Kelola ACC Seminar">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <!-- Dual Micro-Pill Status -->
                                        <div class="inline-flex items-center p-0.5 rounded-full {{ $thesis->isAccSidangFinal() ? 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/50' : 'bg-slate-100 dark:bg-slate-800/80' }} gap-0.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $thesis->acc_sidang_p1 ? 'bg-emerald-500 text-white shadow-2xs' : 'text-slate-400 dark:text-slate-500' }}" title="Pembimbing 1: {{ $thesis->acc_sidang_p1 ? 'Sudah ACC' : 'Belum ACC' }}">
                                                <svg class="w-2.5 h-2.5 {{ $thesis->acc_sidang_p1 ? 'text-white' : 'text-slate-300 dark:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $thesis->acc_sidang_p1 ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/></svg>
                                                <span>P1</span>
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $thesis->acc_sidang_p2 ? 'bg-emerald-500 text-white shadow-2xs' : 'text-slate-400 dark:text-slate-500' }}" title="Pembimbing 2: {{ $thesis->acc_sidang_p2 ? 'Sudah ACC' : 'Belum ACC' }}">
                                                <svg class="w-2.5 h-2.5 {{ $thesis->acc_sidang_p2 ? 'text-white' : 'text-slate-300 dark:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $thesis->acc_sidang_p2 ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/></svg>
                                                <span>P2</span>
                                            </span>
                                        </div>

                                        <!-- Overall Badge & Action Trigger -->
                                        <div class="inline-flex items-center gap-1">
                                            @if($thesis->isAccSidangFinal())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Siap Sidang
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ ($thesis->acc_sidang_p1 || $thesis->acc_sidang_p2) ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                                                    <span>Progres</span>
                                                    <span class="font-bold text-[9px]">({{ ($thesis->acc_sidang_p1 ? 1 : 0) + ($thesis->acc_sidang_p2 ? 1 : 0) }}/2)</span>
                                                </span>
                                            @endif

                                            @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                                                <button type="button" 
                                                        @click="openModal({
                                                            thesisId: {{ $thesis->id }},
                                                            type: 'sidang',
                                                            typeName: 'Sidang Akhir',
                                                            studentName: '{{ addslashes(ucwords(strtolower($thesis->student->name))) }}',
                                                            studentNpm: '{{ $thesis->student->identifier }}',
                                                            isFinal: {{ $thesis->isAccSidangFinal() ? 'true' : 'false' }},
                                                            accP1: {{ $thesis->acc_sidang_p1 ? 'true' : 'false' }},
                                                            accP2: {{ $thesis->acc_sidang_p2 ? 'true' : 'false' }},
                                                            p1Name: '{{ addslashes($thesis->pembimbing1->name ?? 'Dosen 1') }}',
                                                            p2Name: '{{ addslashes($thesis->pembimbing2->name ?? 'Dosen 2') }}'
                                                        })"
                                                        class="w-5 h-5 rounded-md flex items-center justify-center text-slate-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-slate-800 transition-colors cursor-pointer" 
                                                        title="Aksi Cepat Kaprodi: Kelola ACC Sidang">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <a href="{{ route('theses.logbooks', $thesis->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 dark:hover:bg-slate-700 dark:hover:text-orange-400 transition-all shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Detail</span>
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

        <!-- Hidden Form for Kaprodi ACC Submission -->
        <form id="kaprodi-acc-form" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="slot" id="kaprodi-acc-slot" value="all">
        </form>

        <!-- Centralized Kaprodi Quick ACC Modal Dialog -->
        <div x-show="isOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="isOpen = false">
          
          <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200/80 dark:border-slate-700 w-full max-w-sm overflow-hidden"
               @click.away="isOpen = false"
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="opacity-0 scale-95 translate-y-2"
               x-transition:enter-end="opacity-100 scale-100 translate-y-0"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="opacity-100 scale-100 translate-y-0"
               x-transition:leave-end="opacity-0 scale-95 translate-y-2">
            
            <!-- Modal Header -->
            <div class="p-4 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
              <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center text-sm font-bold shadow-2xs">
                  ⚖️
                </span>
                <div>
                  <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100" x-text="'Kelola ACC ' + modalData.typeName"></h3>
                  <p class="text-[11px] text-slate-400">Persetujuan Seminar & Sidang Mahasiswa</p>
                </div>
              </div>
              <button @click="isOpen = false" type="button" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 text-lg font-bold transition-colors cursor-pointer">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="p-4 space-y-3.5">
              <!-- Student Info Box -->
              <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-700/60">
                <div class="text-xs font-bold text-slate-900 dark:text-white" x-text="modalData.studentName"></div>
                <div class="text-[11px] text-slate-400 mt-0.5" x-text="modalData.studentNpm"></div>
                
                <div class="mt-2.5 pt-2 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between text-xs">
                  <span class="text-slate-500 dark:text-slate-400 text-[11px]">Status Saat Ini:</span>
                  <template x-if="modalData.isFinal">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                      Siap <span x-text="modalData.type === 'up' ? 'Seminar' : 'Sidang'"></span> (Lengkap)
                    </span>
                  </template>
                  <template x-if="!modalData.isFinal">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-200/60 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                      Belum Lengkap
                    </span>
                  </template>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="space-y-2 pt-1">
                <!-- Full ACC Toggle (Primary) -->
                <template x-if="!modalData.isFinal">
                  <button type="button" 
                          @click="submitAcc('all')"
                          class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Beri ACC Penuh (P1 & P2)</span>
                  </button>
                </template>

                <template x-if="modalData.isFinal">
                  <button type="button" 
                          @click="submitAcc('all')"
                          class="w-full py-2.5 px-3 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Batalkan Semua Persetujuan (Reset)</span>
                  </button>
                </template>

                <!-- Secondary Actions: Individual Slots -->
                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60">
                  <p class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 mb-1.5">Atau Ubah per Pembimbing:</p>
                  <div class="grid grid-cols-2 gap-2">
                    <button type="button" 
                            @click="submitAcc('p1')"
                            class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-[11px] font-semibold transition-colors text-center cursor-pointer">
                      <span x-text="modalData.accP1 ? '❌ Cabut P1' : '✅ Beri ACC P1'"></span>
                    </button>
                    <button type="button" 
                            @click="submitAcc('p2')"
                            class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-[11px] font-semibold transition-colors text-center cursor-pointer">
                      <span x-text="modalData.accP2 ? '❌ Cabut P2' : '✅ Beri ACC P2'"></span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-3 bg-slate-50 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-700/80 flex justify-end">
              <button type="button" @click="isOpen = false" class="px-3 py-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 cursor-pointer">
                Batal
              </button>
            </div>

          </div>
        </div>

        <script>
            function monitoringAccModal() {
                return {
                    isOpen: false,
                    modalData: {
                        thesisId: null,
                        type: 'up',
                        typeName: '',
                        studentName: '',
                        studentNpm: '',
                        isFinal: false,
                        accP1: false,
                        accP2: false,
                        p1Name: '',
                        p2Name: ''
                    },
                    openModal(data) {
                        this.modalData = data;
                        this.isOpen = true;
                    },
                    submitAcc(slot) {
                        const form = document.getElementById('kaprodi-acc-form');
                        if (!form) return;
                        form.action = `/theses/${this.modalData.thesisId}/toggle-acc/${this.modalData.type}`;
                        document.getElementById('kaprodi-acc-slot').value = slot;
                        form.submit();
                    }
                }
            }
        </script>
    </div>
</x-app-layout>

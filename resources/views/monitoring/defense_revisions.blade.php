<x-app-layout>
    <x-slot name="header">
        <div x-data="{ 
            selectedIds: [], 
            allIds: {{ json_encode($defenseDetails->pluck('id')->toArray()) }},
            toggleAll() {
                if (this.selectedIds.length === this.allIds.length) {
                    this.selectedIds = [];
                } else {
                    this.selectedIds = [...this.allIds];
                }
            },
            downloadSelected() {
                if (this.selectedIds.length === 0) {
                    alert('Pilih minimal satu mahasiswa.');
                    return;
                }
                window.location.href = '{{ route('monitoring.batch-export-berita-acara') }}?category=defense&ids=' + this.selectedIds.join(',');
            }
        }">
            <x-breadcrumb :items="[
                ['label' => 'Monitoring', 'route' => route('monitoring.index')],
                ['label' => 'Revisi Sidang', 'route' => null]
            ]" />
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-4">
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight">
                    Monitoring Progres Revisi Sidang
                </h2>
                
                <div class="flex flex-wrap items-center gap-3">
                    <button @click="downloadSelected()" 
                            x-show="selectedIds.length > 0"
                            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[11px] font-bold transition-all shadow-lg shadow-indigo-900/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        ZIP (<span x-text="selectedIds.length"></span>)
                    </button>

                    <a href="{{ route('monitoring.batch-export-berita-acara', ['category' => 'defense', 'wave_id' => $selectedWaveId]) }}" 
                       class="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-[11px] font-bold transition-all shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        ZIP Semua
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
        <x-table-card 
            title="Monitoring Progres Revisi Sidang"
            subtitle="Pantau status perbaikan skripsi mahasiswa setelah sidang tugas akhir.">
            
            <x-slot name="headerActions">
                <!-- Wave Filter -->
                <form action="{{ route('monitoring.defense-revisions') }}" method="GET" class="flex items-center gap-2">
                    @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                    <select name="wave_id" onchange="this.form.submit()" 
                            class="pl-4 pr-10 py-1.5 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-[11px] font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all shadow-sm min-w-[180px] text-slate-700 dark:text-slate-300">
                        <option value="">Semua Gelombang</option>
                        @foreach($waves as $wave)
                            <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                {{ $wave->name }} {{ $wave->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <x-search-input 
                    name="search" 
                    :value="$search" 
                    placeholder="Cari mahasiswa..." 
                    :route="route('monitoring.defense-revisions')"
                    :params="['wave_id' => $selectedWaveId]" />
            </x-slot>

            <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-3 px-4 w-10 text-center">
                                <input type="checkbox" @click="toggleAll()" :checked="selectedIds.length === allIds.length && allIds.length > 0" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 transition-all cursor-pointer">
                            </th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">MAHASISWA & SIDANG</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">PENGUJI 1</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">PENGUJI 2</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">PEMBIMBING 1</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center">SUMMARY STATUS</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($defenseDetails as $detail)
                            @php
                                $rev1 = $detail->getRevisionFor($detail->examiner1_id);
                                $rev2 = $detail->getRevisionFor($detail->examiner2_id);
                                $revP1 = $detail->thesis ? $detail->getRevisionFor($detail->thesis->pembimbing1_id) : null;
                                
                                $grade1 = ($rev1 && $rev1->score_presentation !== null);
                                $grade2 = ($rev2 && $rev2->score_presentation !== null);
                                $gradeP1 = ($revP1 && $revP1->score_presentation !== null);
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors align-top" :class="selectedIds.includes({{ $detail->id }}) ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : ''">
                                <td class="py-4 px-4 text-center">
                                    <input type="checkbox" x-model="selectedIds" value="{{ $detail->id }}" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 cursor-pointer">
                                </td>
                                <td class="py-4 px-6 border-r border-slate-50 dark:border-slate-800">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 truncate max-w-[150px]" title="{{ $detail->thesis->student->name }}">{{ $detail->thesis->student->name ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 tracking-tight uppercase">{{ $detail->thesis->student->identifier ?? 'N/A' }}</div>
                                    <div class="mt-3">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Tanggal Sidang</span>
                                        <div class="inline-flex items-center gap-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                                            <svg class="w-3 h-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Penguji 1 -->
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-3">
                                        <span class="text-[10px] text-slate-600 dark:text-slate-300 font-bold uppercase truncate" title="{{ $detail->examiner1->name }}">{{ $detail->examiner1->name }}</span>
                                        <div class="space-y-2">
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Penilaian</span>
                                                @if($grade1)
                                                    <x-status-badge type="emerald" :label="$rev1->total_score" />
                                                @else
                                                    <x-status-badge type="rose" label="BELUM ADA" pulse />
                                                @endif
                                            </div>
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Revisi</span>
                                                @if($rev1 && $rev1->isApproved())
                                                    <x-status-badge type="emerald" label="Selesai" />
                                                @elseif($rev1 && $rev1->isResubmitted())
                                                    <x-status-badge type="blue" label="Terkirim" pulse />
                                                @elseif($rev1)
                                                    <x-status-badge type="orange" label="Dikirim" />
                                                @else
                                                    <x-status-badge type="slate" label="None" />
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Penguji 2 -->
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-3">
                                        <span class="text-[10px] text-slate-600 dark:text-slate-300 font-bold uppercase truncate" title="{{ $detail->examiner2->name }}">{{ $detail->examiner2->name }}</span>
                                        <div class="space-y-2">
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Penilaian</span>
                                                @if($grade2)
                                                    <x-status-badge type="emerald" :label="$rev2->total_score" />
                                                @else
                                                    <x-status-badge type="rose" label="BELUM ADA" pulse />
                                                @endif
                                            </div>
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Revisi</span>
                                                @if($rev2 && $rev2->isApproved())
                                                    <x-status-badge type="emerald" label="Selesai" />
                                                @elseif($rev2 && $rev2->isResubmitted())
                                                    <x-status-badge type="blue" label="Terkirim" pulse />
                                                @elseif($rev2)
                                                    <x-status-badge type="orange" label="Dikirim" />
                                                @else
                                                    <x-status-badge type="slate" label="None" />
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Pembimbing 1 -->
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-3">
                                        <span class="text-[10px] text-slate-600 dark:text-slate-300 font-bold uppercase truncate" title="{{ $detail->thesis->pembimbing1->name }}">{{ $detail->thesis->pembimbing1->name }}</span>
                                        <div class="space-y-2">
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Penilaian</span>
                                                @if($gradeP1)
                                                    <x-status-badge type="emerald" :label="$revP1->total_score" />
                                                @else
                                                    <x-status-badge type="rose" label="BELUM ADA" pulse />
                                                @endif
                                            </div>
                                            <div>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Revisi</span>
                                                <span class="text-[8px] font-bold text-slate-300 uppercase italic">Tidak Ada Revisi</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-6 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <!-- Grading Summary -->
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="w-7 h-7 rounded-full {{ $detail->isGradingComplete() ? 'bg-emerald-600' : 'bg-slate-200' }} flex items-center justify-center text-white shadow-sm transition-all duration-300">
                                                @if($detail->isGradingComplete())
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"></path></svg>
                                                @endif
                                            </div>
                                            <span class="text-[8px] font-black {{ $detail->isGradingComplete() ? 'text-emerald-600' : 'text-slate-400' }} uppercase tracking-tighter">Penilaian</span>
                                        </div>
                                        <!-- Revision Summary -->
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="w-7 h-7 rounded-full {{ $detail->isRevisionComplete() ? 'bg-blue-600' : 'bg-slate-200' }} flex items-center justify-center text-white shadow-sm transition-all duration-300">
                                                @if($detail->isRevisionComplete())
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                @endif
                                            </div>
                                            <span class="text-[8px] font-black {{ $detail->isRevisionComplete() ? 'text-blue-600' : 'text-slate-400' }} uppercase tracking-tighter">Revisi</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-6 text-center">
                                    <a href="{{ route('monitoring.defense-scores.berita-acara', $detail->id) }}" 
                                       class="inline-flex items-center gap-2.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-[10px] font-extrabold uppercase tracking-widest transition-all border border-indigo-100 shadow-sm whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                        Cetak Berita Acara
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="6" description="Belum ada mahasiswa yang dijadwalkan sidang" />
                        @endforelse
                    </tbody>
                </table>
            
            <x-slot name="footer">
                @if($defenseDetails->hasPages())
                    {{ $defenseDetails->links() }}
                @endif
            </x-slot>
        </x-table-card>
    </div>
</x-app-layout>

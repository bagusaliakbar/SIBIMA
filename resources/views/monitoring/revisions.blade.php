<x-app-layout>
    <x-slot name="header">
        <div x-data="{ 
            selectedIds: [], 
            allIds: {{ json_encode($seminarDetails->pluck('id')->toArray()) }},
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
                window.location.href = '{{ route('monitoring.batch-export-berita-acara') }}?category=seminar&ids=' + this.selectedIds.join(',');
            }
        }">
            <x-breadcrumb :items="[
                ['label' => 'Monitoring', 'route' => route('monitoring.index')],
                ['label' => 'Revisi Seminar', 'route' => null]
            ]" />
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-4">
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight">
                    Monitoring Progres Revisi Seminar
                </h2>
                
                <div class="flex flex-wrap items-center gap-3">
                    <button @click="downloadSelected()" 
                            x-show="selectedIds.length > 0"
                            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[11px] font-bold transition-all shadow-lg shadow-indigo-900/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        ZIP (<span x-text="selectedIds.length"></span>)
                    </button>

                    <a href="{{ route('monitoring.batch-export-berita-acara', ['category' => 'seminar', 'wave_id' => $selectedWaveId]) }}" 
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
            title="Monitoring Progres Revisi Seminar"
            subtitle="Pantau status perbaikan draf skripsi mahasiswa setelah seminar.">
            
            <x-slot name="headerActions">
                <!-- Wave Filter -->
                <form action="{{ route('monitoring.revisions') }}" method="GET" class="flex items-center gap-2">
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
                    :route="route('monitoring.revisions')"
                    :params="['wave_id' => $selectedWaveId]" />
            </x-slot>

            <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="py-3 px-4 w-10 text-center">
                                <input type="checkbox" @click="toggleAll()" :checked="selectedIds.length === allIds.length && allIds.length > 0" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 transition-all cursor-pointer">
                            </th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">MAHASISWA & SEMINAR</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">STATUS REVISI (PENGUJI 1)</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap">STATUS REVISI (PENGUJI 2)</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-center">OVERALL STATUS</th>
                            <th class="py-3 px-6 font-semibold text-xs tracking-wider whitespace-nowrap text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($seminarDetails as $detail)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors align-top" :class="selectedIds.includes({{ $detail->id }}) ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : ''">
                                <td class="py-4 px-4 text-center">
                                    <input type="checkbox" x-model="selectedIds" value="{{ $detail->id }}" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 cursor-pointer">
                                </td>
                                <td class="py-4 px-6">
                                     <div class="font-bold text-slate-800 dark:text-slate-100">{{ $detail->thesis->student->name ?? 'N/A' }}</div>
                                     <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 tracking-tight uppercase">{{ $detail->thesis->student->identifier ?? 'N/A' }}</div>
                                     <div class="mt-2 flex flex-col gap-1">
                                         <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500">
                                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                             <span>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                         </div>
                                         <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500">
                                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                             <span>{{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H:i') }} WIB</span>
                                         </div>
                                     </div>
                                 </td>
                                 
                                 <td class="py-4 px-6">
                                     <div class="flex flex-col gap-1.5">
                                         <div class="flex items-center gap-2">
                                             <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">{{ $detail->examiner1->name }}</span>
                                         </div>
                                         @php $rev1 = $detail->getRevisionFor($detail->examiner1_id); @endphp
                                         @if($rev1)
                                             @if($rev1->isApproved())
                                                 <x-status-badge type="emerald" label="Revisi Selesai" size="sm" />
                                             @elseif($rev1->isResubmitted())
                                                 <x-status-badge type="blue" label="Revisi Terkirim" size="sm" pulse />
                                             @else
                                                 <x-status-badge type="orange" label="Revisi Dikirim" size="sm" />
                                             @endif
                                         @else
                                             <x-status-badge type="slate" label="Belum Ada Revisi" size="sm" />
                                         @endif
                                     </div>
                                 </td>

                                 <td class="py-4 px-6">
                                     <div class="flex flex-col gap-1.5">
                                         <div class="flex items-center gap-2">
                                             <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">{{ $detail->examiner2->name }}</span>
                                         </div>
                                         @php $rev2 = $detail->getRevisionFor($detail->examiner2_id); @endphp
                                         @if($rev2)
                                             @if($rev2->isApproved())
                                                 <x-status-badge type="emerald" label="Revisi Selesai" size="sm" />
                                             @elseif($rev2->isResubmitted())
                                                 <x-status-badge type="blue" label="Revisi Terkirim" size="sm" pulse />
                                             @else
                                                 <x-status-badge type="orange" label="Revisi Dikirim" size="sm" />
                                             @endif
                                         @else
                                             <x-status-badge type="slate" label="Belum Ada Revisi" size="sm" />
                                         @endif
                                     </div>
                                 </td>

                                 <td class="py-4 px-6 text-center">
                                     @if($detail->isAllRevisionsApproved())
                                         <div class="flex flex-col items-center gap-1">
                                             <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-900/20 mx-auto">
                                                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                             </div>
                                             <span class="text-[10px] font-black text-emerald-600 uppercase tracking-tighter">FINALIZED</span>
                                         </div>
                                     @elseif($detail->isRevisionStarted())
                                         <div class="flex flex-col items-center gap-1">
                                             <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white shadow-lg shadow-orange-900/20 mx-auto animate-pulse">
                                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                             </div>
                                             <span class="text-[10px] font-black text-orange-500 uppercase tracking-tighter">IN PROGRESS</span>
                                         </div>
                                     @else
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 mx-auto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            </div>
                                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-tighter">NOT STARTED</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if($detail->isAllRevisionsApproved())
                                        <a href="{{ route('monitoring.seminar-scores.berita-acara', $detail->id) }}" 
                                           class="inline-flex items-center gap-2.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-[10px] font-extrabold uppercase tracking-widest transition-all border border-indigo-100 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                            Cetak Berita Acara
                                        </a>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-600 italic">Revisi belum disetujui</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="5" description="Belum ada mahasiswa yang dijadwalkan seminar" />
                        @endforelse
                    </tbody>
                </table>

            <x-slot name="footer">
                @if($seminarDetails->hasPages())
                    {{ $seminarDetails->links() }}
                @endif
            </x-slot>
        </x-table-card>
    </div>
</x-app-layout>

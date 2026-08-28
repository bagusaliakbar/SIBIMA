<x-app-layout>
    <x-slot name="header">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Monitoring', 'route' => route('monitoring.index')],
                ['label' => 'Revisi Seminar', 'route' => null]
            ]" />
            <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight mt-1">
                Monitoring Progres Revisi Seminar
            </h2>
        </div>
    </x-slot>

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
    }" class="space-y-6">

        <!-- Premium Toolbar Row -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-5 rounded-3xl border border-slate-100 dark:border-slate-700/50 flex flex-col lg:flex-row lg:items-center justify-between gap-4 shadow-sm">
            <!-- Left: Wave Filter and Search Input -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1 max-w-4xl w-full">
                <!-- Wave Filter -->
                <form action="{{ route('monitoring.revisions') }}" method="GET" class="shrink-0 w-full sm:w-auto">
                    @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                    <select name="wave_id" onchange="this.form.submit()" 
                            class="w-full sm:w-auto pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm min-w-[200px] text-slate-700 dark:text-slate-350">
                        <option value="">Semua Gelombang</option>
                        @foreach($waves as $wave)
                            <option value="{{ $wave->id }}" {{ $selectedWaveId == $wave->id ? 'selected' : '' }}>
                                {{ $wave->name }} {{ $wave->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- Search Input -->
                <form action="{{ route('monitoring.revisions') }}" method="GET" class="relative group flex-1 w-full">
                    @if($selectedWaveId) <input type="hidden" name="wave_id" value="{{ $selectedWaveId }}"> @endif
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-transform group-focus-within:scale-105">
                        <svg class="w-4 h-4 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari mahasiswa atau judul..." 
                           class="w-full pl-10 pr-9 py-2.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-medium focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm placeholder:text-slate-400/85">
                    @if($search)
                        <a href="{{ route('monitoring.revisions', ['wave_id' => $selectedWaveId]) }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-rose-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Right: ZIP Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <!-- Batch Actions (ZIP Selected) -->
                <button @click="downloadSelected()" 
                        x-show="selectedIds.length > 0"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold transition-all shadow-md animate-in fade-in zoom-in duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    ZIP Terpilih (<span x-text="selectedIds.length"></span>)
                </button>

                <a href="{{ route('monitoring.batch-export-berita-acara', ['category' => 'seminar', 'wave_id' => $selectedWaveId]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl text-xs font-bold transition-all shadow-md group w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    ZIP Semua
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/80 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                            <th class="py-5 px-4 w-10 text-center border-r border-slate-200/60 dark:border-slate-700">
                                <input type="checkbox" @click="toggleAll()" :checked="selectedIds.length === allIds.length && allIds.length > 0" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 transition-all cursor-pointer">
                            </th>
                            <th class="py-5 px-6 font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap">MAHASISWA & SEMINAR</th>
                            <th class="py-5 px-6 font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap">STATUS REVISI (PENGUJI 1)</th>
                            <th class="py-5 px-6 font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap">STATUS REVISI (PENGUJI 2)</th>
                            <th class="py-5 px-6 font-black text-[10px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap text-center">OVERALL STATUS</th>
                            <th class="py-5 px-6 font-black text-[10px] uppercase tracking-[0.2em] whitespace-nowrap text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($seminarDetails as $detail)
                            @php
                                $rev1 = $detail->getRevisionFor($detail->examiner1_id);
                                $rev2 = $detail->getRevisionFor($detail->examiner2_id);
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors align-top" :class="selectedIds.includes({{ $detail->id }}) ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : ''">
                                <td class="py-4 px-4 text-center">
                                    <input type="checkbox" x-model="selectedIds" value="{{ $detail->id }}" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 cursor-pointer">
                                </td>
                                <td class="py-4 px-6 border-r border-slate-50 dark:border-slate-800">
                                    <div class="font-black text-xs text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $detail->thesis->student->name ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 tracking-tight font-bold uppercase">{{ $detail->thesis->student->identifier ?? 'N/A' }}</div>
                                    @if($detail->thesis && $detail->thesis->title)
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 font-medium leading-relaxed italic">
                                            "{{ $detail->thesis->title }}"
                                        </div>
                                    @endif
                                    <div class="mt-3 space-y-1">
                                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500 dark:text-slate-400">
                                            <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500 dark:text-slate-400">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>{{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H:i') }} WIB</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Penguji 1 -->
                                <td class="py-4 px-6 border-r border-slate-50 dark:border-slate-800">
                                    <div class="flex flex-col gap-3.5">
                                        <div class="font-black text-[11px] text-slate-700 dark:text-slate-300 uppercase tracking-tight leading-snug" title="{{ $detail->examiner1?->name }}">
                                            {{ $detail->examiner1?->name ?? '-' }}
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60 max-w-[160px]">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Revisi</span>
                                                @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->examiner1_id)
                                                    <a href="{{ route('seminar-examiner.show', ['detail' => $detail->id, 'target_examiner_id' => $detail->examiner1_id, 'redirect_to' => 'monitoring-revisions']) }}"
                                                       class="p-0.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded transition-colors" title="Kelola / Input Revisi">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </a>
                                                @endif
                                            </div>
                                            @if($rev1)
                                                @if($rev1->isApproved())
                                                    <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase block">Selesai</span>
                                                @elseif($rev1->isResubmitted())
                                                    <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase block animate-pulse">Terkirim</span>
                                                @else
                                                    <span class="text-[9px] font-black text-orange-500 dark:text-orange-400 uppercase block">Dikirim</span>
                                                @endif
                                            @else
                                                <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase block">Belum Ada</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Penguji 2 -->
                                <td class="py-4 px-6 border-r border-slate-50 dark:border-slate-800">
                                    <div class="flex flex-col gap-3.5">
                                        <div class="font-black text-[11px] text-slate-700 dark:text-slate-300 uppercase tracking-tight leading-snug" title="{{ $detail->examiner2?->name }}">
                                            {{ $detail->examiner2?->name ?? '-' }}
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60 max-w-[160px]">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Revisi</span>
                                                @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->examiner2_id)
                                                    <a href="{{ route('seminar-examiner.show', ['detail' => $detail->id, 'target_examiner_id' => $detail->examiner2_id, 'redirect_to' => 'monitoring-revisions']) }}"
                                                       class="p-0.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded transition-colors" title="Kelola / Input Revisi">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </a>
                                                @endif
                                            </div>
                                            @if($rev2)
                                                @if($rev2->isApproved())
                                                    <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase block">Selesai</span>
                                                @elseif($rev2->isResubmitted())
                                                    <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase block animate-pulse">Terkirim</span>
                                                @else
                                                    <span class="text-[9px] font-black text-orange-500 dark:text-orange-400 uppercase block">Dikirim</span>
                                                @endif
                                            @else
                                                <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase block">Belum Ada</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-6 text-center border-r border-slate-50 dark:border-slate-800 align-middle">
                                    @if($detail->isAllRevisionsApproved())
                                        <div class="flex flex-col items-center gap-1.5">
                                            <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white shadow-sm mx-auto">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-[8px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">FINALIZED</span>
                                        </div>
                                    @elseif($detail->isRevisionStarted())
                                        <div class="flex flex-col items-center gap-1.5">
                                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white shadow-sm mx-auto animate-pulse">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <span class="text-[8px] font-black text-orange-500 uppercase tracking-widest">IN PROGRESS</span>
                                        </div>
                                    @else
                                       <div class="flex flex-col items-center gap-1.5">
                                           <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 mx-auto">
                                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                           </div>
                                           <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">NOT STARTED</span>
                                       </div>
                                   @endif
                               </td>
                               
                               <td class="py-4 px-6 text-center align-middle">
                                   @if($detail->isAllRevisionsApproved())
                                       <a href="{{ route('monitoring.seminar-scores.berita-acara', $detail->id) }}" 
                                          class="inline-flex items-center gap-2.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-[10px] font-extrabold uppercase tracking-widest transition-all border border-indigo-100 shadow-sm whitespace-nowrap">
                                           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                           </svg>
                                           Cetak Berita Acara
                                       </a>
                                   @else
                                       <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 italic whitespace-nowrap">Belum Disetujui</span>
                                   @endif
                               </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="6" description="Belum ada mahasiswa yang dijadwalkan seminar" />
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($seminarDetails->hasPages())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
                    {{ $seminarDetails->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

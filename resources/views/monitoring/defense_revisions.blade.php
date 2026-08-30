<x-app-layout>
    <x-slot name="header">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Monitoring', 'route' => route('monitoring.index')],
                ['label' => 'Revisi Sidang', 'route' => null]
            ]" />
            <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight mt-1">
                Monitoring Progres Revisi Sidang
            </h2>
        </div>
    </x-slot>

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
    }" class="space-y-6">

        <!-- Premium Toolbar Row -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-5 rounded-3xl border border-slate-100 dark:border-slate-700/50 flex flex-col lg:flex-row lg:items-center justify-between gap-4 shadow-sm">
            <!-- Left: Wave Filter and Search Input -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1 max-w-4xl w-full">
                <!-- Wave Filter -->
                <form action="{{ route('monitoring.defense-revisions') }}" method="GET" class="shrink-0 w-full sm:w-auto">
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
                <form action="{{ route('monitoring.defense-revisions') }}" method="GET" class="relative group flex-1 w-full">
                    @if($selectedWaveId) <input type="hidden" name="wave_id" value="{{ $selectedWaveId }}"> @endif
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-transform group-focus-within:scale-105">
                        <svg class="w-4 h-4 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari mahasiswa atau judul..." 
                           class="w-full pl-10 pr-9 py-2.5 bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-medium focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm placeholder:text-slate-400/85">
                    @if($search)
                        <a href="{{ route('monitoring.defense-revisions', ['wave_id' => $selectedWaveId]) }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-rose-500 transition-colors">
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

                <a href="{{ route('monitoring.batch-export-berita-acara', ['category' => 'defense', 'wave_id' => $selectedWaveId]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl text-xs font-bold transition-all shadow-md group w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    ZIP Semua
                </a>
            </div>
        </div>

        <!-- Table Card with Sticky Headers & Freeze Column -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-270px)] min-h-[460px] relative">
                <table class="w-full text-left border-collapse border-separate border-spacing-0">
                    <thead class="sticky top-0 z-30">
                        <tr class="bg-slate-100/95 dark:bg-slate-900/95 backdrop-blur-md text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                            <th class="sticky top-0 left-0 z-40 bg-slate-100 dark:bg-slate-900 py-4 px-3 w-12 min-w-[48px] text-center border-r border-b border-slate-200 dark:border-slate-700">
                                <input type="checkbox" @click="toggleAll()" :checked="selectedIds.length === allIds.length && allIds.length > 0" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 transition-all cursor-pointer">
                            </th>
                            <th class="sticky top-0 left-[48px] z-40 bg-slate-100 dark:bg-slate-900 py-4 px-6 font-black text-[10px] border-r-2 border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap w-72 min-w-[280px] shadow-[4px_0_12px_-2px_rgba(0,0,0,0.08)] dark:shadow-[4px_0_12px_-2px_rgba(0,0,0,0.4)]">
                                MAHASISWA & SIDANG
                            </th>
                            <th class="sticky top-0 z-30 bg-slate-100 dark:bg-slate-900 py-4 px-6 font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap min-w-[220px]">
                                PENGUJI 1
                            </th>
                            <th class="sticky top-0 z-30 bg-slate-100 dark:bg-slate-900 py-4 px-6 font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap min-w-[220px]">
                                PENGUJI 2
                            </th>
                            <th class="sticky top-0 z-30 bg-slate-100 dark:bg-slate-900 py-4 px-6 font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap min-w-[220px]">
                                PEMBIMBING 1
                            </th>
                            <th class="sticky top-0 z-30 bg-slate-100 dark:bg-slate-900 py-4 px-6 font-black text-[10px] border-r border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap text-center min-w-[160px]">
                                SUMMARY STATUS
                            </th>
                            <th class="sticky top-0 z-30 bg-slate-100 dark:bg-slate-900 py-4 px-6 font-black text-[10px] border-b border-slate-200 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap text-center min-w-[120px]">
                                AKSI
                            </th>
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
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors align-top group" :class="selectedIds.includes({{ $detail->id }}) ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : ''">
                                <td class="sticky left-0 z-20 bg-white dark:bg-slate-900 group-hover:bg-slate-50 dark:group-hover:bg-slate-800 py-4 px-3 text-center border-r border-b border-slate-100 dark:border-slate-700">
                                    <input type="checkbox" x-model="selectedIds" value="{{ $detail->id }}" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 cursor-pointer">
                                </td>
                                <td class="sticky left-[48px] z-20 bg-white dark:bg-slate-900 group-hover:bg-slate-50 dark:group-hover:bg-slate-800 py-4 px-6 border-r-2 border-b border-slate-200 dark:border-slate-700 shadow-[4px_0_12px_-2px_rgba(0,0,0,0.08)] dark:shadow-[4px_0_12px_-2px_rgba(0,0,0,0.4)]">
                                    <div class="font-black text-xs text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $detail->thesis->student->name ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 tracking-tight font-bold uppercase">{{ $detail->thesis->student->identifier ?? 'N/A' }}</div>
                                    @if($detail->thesis && $detail->thesis->title)
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 font-medium leading-relaxed italic">
                                            "{{ $detail->thesis->title }}"
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Tanggal Sidang</span>
                                        <div class="inline-flex items-center gap-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded shadow-sm">
                                            <svg class="w-3 h-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Penguji 1 -->
                                <td class="py-4 px-6 border-r border-slate-50 dark:border-slate-800">
                                    <div class="flex flex-col gap-3">
                                        <div class="font-black text-[11px] text-slate-700 dark:text-slate-300 uppercase tracking-tight leading-snug truncate" title="{{ $detail->examiner1?->name }}">
                                            {{ $detail->examiner1?->name ?? '-' }}
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <!-- Penilaian -->
                                            <div class="bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60 flex flex-col justify-between min-h-[58px]">
                                                <div class="flex items-center justify-between gap-1 mb-1">
                                                    <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Penilaian</span>
                                                    @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->examiner1_id)
                                                        <a href="{{ route('defense-examiner.grading', ['detail' => $detail->id, 'target_examiner_id' => $detail->examiner1_id, 'redirect_to' => 'monitoring-revisions']) }}" 
                                                           class="p-1 -mr-1 -mt-1 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 rounded-md transition-all shrink-0" 
                                                           title="Input / Ubah Nilai">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($grade1)
                                                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 block leading-none">{{ number_format($rev1->total_score, 1) }}</span>
                                                    @else
                                                        <span class="text-[9px] font-black text-rose-500 dark:text-rose-400 uppercase tracking-tighter block leading-none animate-pulse">Belum</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Revisi -->
                                            <div class="bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60 flex flex-col justify-between min-h-[58px]">
                                                <div class="flex items-center justify-between gap-1 mb-1">
                                                    <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Revisi</span>
                                                    @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->examiner1_id)
                                                        <a href="{{ route('defense-examiner.show', ['detail' => $detail->id, 'target_examiner_id' => $detail->examiner1_id, 'redirect_to' => 'monitoring-revisions']) }}" 
                                                           class="p-1 -mr-1 -mt-1 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-md transition-all shrink-0" 
                                                           title="Kelola / Input Revisi">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($rev1 && $rev1->isApproved())
                                                        <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase block leading-none">Selesai</span>
                                                    @elseif($rev1 && $rev1->isResubmitted())
                                                        <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase block leading-none animate-pulse">Terkirim</span>
                                                    @elseif($rev1)
                                                        <span class="text-[9px] font-black text-orange-500 dark:text-orange-400 uppercase block leading-none">Dikirim</span>
                                                    @else
                                                        <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase block leading-none">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Penguji 2 -->
                                <td class="py-4 px-6 border-r border-slate-50 dark:border-slate-800">
                                    <div class="flex flex-col gap-3">
                                        <div class="font-black text-[11px] text-slate-700 dark:text-slate-300 uppercase tracking-tight leading-snug truncate" title="{{ $detail->examiner2?->name }}">
                                            {{ $detail->examiner2?->name ?? '-' }}
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <!-- Penilaian -->
                                            <div class="bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60 flex flex-col justify-between min-h-[58px]">
                                                <div class="flex items-center justify-between gap-1 mb-1">
                                                    <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Penilaian</span>
                                                    @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->examiner2_id)
                                                        <a href="{{ route('defense-examiner.grading', ['detail' => $detail->id, 'target_examiner_id' => $detail->examiner2_id, 'redirect_to' => 'monitoring-revisions']) }}" 
                                                           class="p-1 -mr-1 -mt-1 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 rounded-md transition-all shrink-0" 
                                                           title="Input / Ubah Nilai">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($grade2)
                                                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 block leading-none">{{ number_format($rev2->total_score, 1) }}</span>
                                                    @else
                                                        <span class="text-[9px] font-black text-rose-500 dark:text-rose-400 uppercase tracking-tighter block leading-none animate-pulse">Belum</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Revisi -->
                                            <div class="bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60 flex flex-col justify-between min-h-[58px]">
                                                <div class="flex items-center justify-between gap-1 mb-1">
                                                    <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Revisi</span>
                                                    @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->examiner2_id)
                                                        <a href="{{ route('defense-examiner.show', ['detail' => $detail->id, 'target_examiner_id' => $detail->examiner2_id, 'redirect_to' => 'monitoring-revisions']) }}" 
                                                           class="p-1 -mr-1 -mt-1 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-md transition-all shrink-0" 
                                                           title="Kelola / Input Revisi">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($rev2 && $rev2->isApproved())
                                                        <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase block leading-none">Selesai</span>
                                                    @elseif($rev2 && $rev2->isResubmitted())
                                                        <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase block leading-none animate-pulse">Terkirim</span>
                                                    @elseif($rev2)
                                                        <span class="text-[9px] font-black text-orange-500 dark:text-orange-400 uppercase block leading-none">Dikirim</span>
                                                    @else
                                                        <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase block leading-none">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Pembimbing 1 -->
                                <td class="py-4 px-6 border-r border-slate-50 dark:border-slate-800">
                                    <div class="flex flex-col gap-3">
                                        <div class="font-black text-[11px] text-slate-700 dark:text-slate-300 uppercase tracking-tight leading-snug truncate" title="{{ $detail->thesis->pembimbing1?->name }}">
                                            {{ $detail->thesis->pembimbing1?->name ?? '-' }}
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <!-- Penilaian -->
                                            <div class="bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60 flex flex-col justify-between min-h-[58px]">
                                                <div class="flex items-center justify-between gap-1 mb-1">
                                                    <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Penilaian</span>
                                                    @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->thesis?->pembimbing1_id)
                                                        <a href="{{ route('defense-examiner.grading', ['detail' => $detail->id, 'target_examiner_id' => $detail->thesis->pembimbing1_id, 'redirect_to' => 'monitoring-revisions']) }}" 
                                                           class="p-1 -mr-1 -mt-1 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 rounded-md transition-all shrink-0" 
                                                           title="Input / Ubah Nilai">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($gradeP1)
                                                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 block leading-none">{{ number_format($revP1->total_score, 1) }}</span>
                                                    @else
                                                        <span class="text-[9px] font-black text-rose-500 dark:text-rose-400 uppercase tracking-tighter block leading-none animate-pulse">Belum</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Revisi (N/A) -->
                                            <div class="bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60 flex flex-col justify-between min-h-[58px]">
                                                <div class="flex items-center justify-between gap-1 mb-1">
                                                    <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Revisi</span>
                                                </div>
                                                <div>
                                                    <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase italic block leading-none">N/A</span>
                                                </div>
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
            </div>
            
            @if($defenseDetails->hasPages())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
                    {{ $defenseDetails->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

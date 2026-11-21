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
        downloadSelectedRevisions() {
            if (this.selectedIds.length === 0) {
                window.sibimaToast('Pilih minimal satu mahasiswa.', 'warning', 'Belum Ada Pilihan');
                return;
            }
            window.location.href = '{{ route('monitoring.batch-export-revisions') }}?category=seminar&ids=' + this.selectedIds.join(',');
        },
        downloadSelectedBeritaAcara() {
            if (this.selectedIds.length === 0) {
                window.sibimaToast('Pilih minimal satu mahasiswa.', 'warning', 'Belum Ada Pilihan');
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
                        <a href="{{ route('monitoring.revisions', ['wave_id' => $selectedWaveId]) }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 hover:text-rose-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Right: ZIP & Export Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <!-- Batch Actions (ZIP Lembar Revisi Terpilih) -->
                <button type="button" @click="downloadSelectedRevisions()" 
                        x-show="selectedIds.length > 0"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold transition-all shadow-md animate-in fade-in zoom-in duration-200 cursor-pointer"
                        title="Download ZIP Lembar Revisi Seminar Mahasiswa Terpilih">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    ZIP Revisi (<span x-text="selectedIds.length"></span>)
                </button>

                <!-- ZIP Semua Revisi -->
                <a href="{{ route('monitoring.batch-export-revisions', ['category' => 'seminar', 'wave_id' => $selectedWaveId]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800 rounded-2xl text-xs font-bold transition-all shadow-sm group w-full sm:w-auto justify-center"
                   title="Download ZIP Semua Lembar Revisi Seminar (Semua Mahasiswa)">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    ZIP Semua Revisi
                </a>

                <!-- Batch Actions (ZIP Berita Acara) -->
                <button type="button" @click="downloadSelectedBeritaAcara()" 
                        x-show="selectedIds.length > 0"
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-700 hover:bg-slate-800 text-white rounded-2xl text-xs font-bold transition-all shadow-md animate-in fade-in zoom-in duration-200 cursor-pointer"
                        title="Download ZIP Berita Acara Seminar Mahasiswa Terpilih">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    ZIP B.A (<span x-text="selectedIds.length"></span>)
                </button>

                <a href="{{ route('monitoring.batch-export-berita-acara', ['category' => 'seminar', 'wave_id' => $selectedWaveId]) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl text-xs font-bold transition-all shadow-md group w-full sm:w-auto justify-center"
                   title="Download ZIP Berita Acara Semua Mahasiswa">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    ZIP Semua B.A
                </a>

                <!-- Broadcast WA Penguji Seminar Belum Input Catatan Revisi -->
                <a href="{{ route('wa-broadcasts.create', ['target_type' => 'dosen_penguji_belum_revisi', 'exam_type' => 'seminar', 'scope' => 'yesterday', 'wave_id' => $selectedWaveId]) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold transition-all shadow-md group w-full sm:w-auto justify-center"
                   title="Kirim Pesan WhatsApp Pengingat ke Dosen Penguji Seminar yang Belum Mengisi Catatan Revisi">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span>Broadcast WA Penguji</span>
                </a>

                <!-- Broadcast WA Mahasiswa Seminar Belum Mengirimkan Balasan Revisi -->
                <a href="{{ route('wa-broadcasts.create', ['target_type' => 'mahasiswa_belum_balas_revisi', 'exam_type' => 'seminar', 'wave_id' => $selectedWaveId]) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-2xl text-xs font-bold transition-all shadow-md group w-full sm:w-auto justify-center"
                   title="Kirim Pesan WhatsApp Pengingat ke Mahasiswa yang Belum Mengirimkan Balasan Revisi Seminar">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Broadcast WA Mahasiswa Belum Revisi</span>
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/80 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                            <th class="py-5 px-4 w-10 text-center border-r border-slate-200/60 dark:border-slate-700">
                                <input type="checkbox" @click="toggleAll()" :checked="selectedIds.length === allIds.length && allIds.length > 0" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 transition-all cursor-pointer">
                            </th>
                            <th class="py-5 px-6 font-black text-[11px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap">MAHASISWA & SEMINAR</th>
                            <th class="py-5 px-6 font-black text-[11px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap">STATUS REVISI (PENGUJI 1)</th>
                            <th class="py-5 px-6 font-black text-[11px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap">STATUS REVISI (PENGUJI 2)</th>
                            <th class="py-5 px-6 font-black text-[11px] border-r border-slate-200/60 dark:border-slate-700 uppercase tracking-[0.2em] whitespace-nowrap text-center">OVERALL STATUS</th>
                            <th class="py-5 px-6 font-black text-[11px] uppercase tracking-[0.2em] whitespace-nowrap text-center">AKSI</th>
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
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 tracking-tight font-bold uppercase">{{ $detail->thesis->student->identifier ?? 'N/A' }}</div>
                                    @if($detail->thesis && $detail->thesis->title)
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 font-medium leading-relaxed italic">
                                            "{{ $detail->thesis->title }}"
                                        </div>
                                    @endif
                                    <div class="mt-3 space-y-1">
                                        <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                            <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>{{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H:i') }} WIB</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Penguji 1 -->
                                <td class="py-4 px-6 border-r border-slate-50 dark:border-slate-800">
                                    <div class="flex flex-col gap-3">
                                        <div class="font-black text-[11px] text-slate-700 dark:text-slate-300 uppercase tracking-tight leading-snug truncate" title="{{ $detail->examiner1?->name }}">
                                            {{ $detail->examiner1?->name ?? '-' }}
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60 max-w-[160px] flex flex-col justify-between min-h-[58px]">
                                            <div class="flex items-center justify-between gap-1 mb-1">
                                                <span class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Revisi</span>
                                                @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->examiner1_id)
                                                    <a href="{{ route('seminar-examiner.show', ['detail' => $detail->id, 'target_examiner_id' => $detail->examiner1_id, 'redirect_to' => 'monitoring-revisions']) }}"
                                                       class="p-1 -mr-1 -mt-1 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 rounded-md transition-all shrink-0" 
                                                       title="Kelola / Input Revisi">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    </a>
                                                @endif
                                            </div>
                                            <div>
                                                @if($rev1)
                                                    @if($rev1->isApproved())
                                                        <span class="text-[11px] font-black text-emerald-600 dark:text-emerald-400 uppercase block leading-none">Selesai</span>
                                                    @elseif($rev1->isResubmitted())
                                                        <span class="text-[11px] font-black text-blue-600 dark:text-blue-400 uppercase block leading-none animate-pulse">Terkirim</span>
                                                    @else
                                                        <span class="text-[11px] font-black text-orange-500 dark:text-orange-400 uppercase block leading-none">Dikirim</span>
                                                    @endif
                                                @else
                                                    <span class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase block leading-none">Belum Ada</span>
                                                @endif
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
                                        <div class="bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60 max-w-[160px] flex flex-col justify-between min-h-[58px]">
                                            <div class="flex items-center justify-between gap-1 mb-1">
                                                <span class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Revisi</span>
                                                @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $detail->examiner2_id)
                                                    <a href="{{ route('seminar-examiner.show', ['detail' => $detail->id, 'target_examiner_id' => $detail->examiner2_id, 'redirect_to' => 'monitoring-revisions']) }}"
                                                       class="p-1 -mr-1 -mt-1 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 rounded-md transition-all shrink-0" 
                                                       title="Kelola / Input Revisi">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    </a>
                                                @endif
                                            </div>
                                            <div>
                                                @if($rev2)
                                                    @if($rev2->isApproved())
                                                        <span class="text-[11px] font-black text-emerald-600 dark:text-emerald-400 uppercase block leading-none">Selesai</span>
                                                    @elseif($rev2->isResubmitted())
                                                        <span class="text-[11px] font-black text-blue-600 dark:text-blue-400 uppercase block leading-none animate-pulse">Terkirim</span>
                                                    @else
                                                        <span class="text-[11px] font-black text-orange-500 dark:text-orange-400 uppercase block leading-none">Dikirim</span>
                                                    @endif
                                                @else
                                                    <span class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase block leading-none">Belum Ada</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-6 text-center border-r border-slate-50 dark:border-slate-800 align-middle">
                                    @if($detail->isAllRevisionsApproved())
                                        <div class="flex flex-col items-center gap-1.5">
                                            <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white shadow-sm mx-auto">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-[11px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">FINALIZED</span>
                                        </div>
                                    @elseif($detail->isRevisionStarted())
                                        <div class="flex flex-col items-center gap-1.5">
                                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white shadow-sm mx-auto animate-pulse">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <span class="text-[11px] font-black text-orange-500 uppercase tracking-widest">IN PROGRESS</span>
                                        </div>
                                    @else
                                       <div class="flex flex-col items-center gap-1.5">
                                           <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 mx-auto">
                                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                           </div>
                                           <span class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">NOT STARTED</span>
                                       </div>
                                   @endif
                               </td>
                               
                                <td class="py-4 px-6 text-center align-middle">
                                    <div class="flex flex-col items-center gap-1.5 whitespace-nowrap min-w-[130px]">
                                        <!-- Tombol Cetak Revisi Lengkap (Penguji 1 & 2) -->
                                        <a href="{{ route('monitoring.seminar-revisions.print', $detail->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[11px] font-extrabold uppercase tracking-wider transition-all shadow-sm w-full justify-center group"
                                           title="Cetak Lembar Revisi Lengkap (Penguji 1 & 2)">
                                            <svg class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-6 4h4"/>
                                            </svg>
                                            <span>Cetak Revisi</span>
                                        </a>

                                        <!-- Sub-tombol revisi per penguji -->
                                        <div class="flex items-center gap-1 w-full">
                                            @if($detail->examiner1_id)
                                                <a href="{{ route('monitoring.seminar-revisions.print', ['detail' => $detail->id, 'examiner_id' => $detail->examiner1_id]) }}" target="_blank"
                                                   class="flex-1 inline-flex items-center justify-center gap-1 px-1.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-xs"
                                                   title="Cetak Lembar Revisi Penguji 1 ({{ $detail->examiner1?->name }})">
                                                    <span>Revisi P1</span>
                                                </a>
                                            @endif
                                            @if($detail->examiner2_id)
                                                <a href="{{ route('monitoring.seminar-revisions.print', ['detail' => $detail->id, 'examiner_id' => $detail->examiner2_id]) }}" target="_blank"
                                                   class="flex-1 inline-flex items-center justify-center gap-1 px-1.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-xs"
                                                   title="Cetak Lembar Revisi Penguji 2 ({{ $detail->examiner2?->name }})">
                                                    <span>Revisi P2</span>
                                                </a>
                                            @endif
                                        </div>

                                        <!-- Berita Acara -->
                                        @if($detail->isAllRevisionsApproved())
                                            <a href="{{ route('monitoring.seminar-scores.berita-acara', $detail->id) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 rounded-lg text-[11px] font-extrabold uppercase tracking-wider transition-all border border-slate-200 dark:border-slate-700 w-full justify-center">
                                                <span>Berita Acara</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="6" description="Belum ada mahasiswa yang dijadwalkan seminar" />
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="md:hidden">
                @if($seminarDetails->count() > 0)
                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer">
                            <input type="checkbox" @click="toggleAll()" :checked="selectedIds.length === allIds.length && allIds.length > 0" class="rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500 dark:bg-slate-800 cursor-pointer">
                            <span>Pilih Semua ({{ $seminarDetails->count() }})</span>
                        </label>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400" x-show="selectedIds.length > 0" x-text="selectedIds.length + ' terpilih'"></span>
                    </div>
                @endif

                <div class="divide-y divide-slate-100 dark:divide-slate-700/80">
                    @forelse($seminarDetails as $detail)
                        @php
                            $rev1 = $detail->getRevisionFor($detail->examiner1_id);
                            $rev2 = $detail->getRevisionFor($detail->examiner2_id);
                        @endphp
                        <div class="p-4 space-y-3 transition-colors" :class="selectedIds.includes({{ $detail->id }}) ? 'bg-orange-50/40 dark:bg-orange-950/20' : 'bg-white dark:bg-slate-800'">
                            <div class="flex items-start justify-between gap-2.5">
                                <div class="flex items-start gap-2.5 min-w-0">
                                    <input type="checkbox" x-model="selectedIds" value="{{ $detail->id }}" aria-label="Pilih {{ $detail->thesis->student->name ?? 'Mahasiswa' }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500 dark:bg-slate-800 cursor-pointer shrink-0">
                                    <div class="min-w-0">
                                        <div class="font-black text-sm text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $detail->thesis->student->name ?? 'N/A' }}</div>
                                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ $detail->thesis->student->identifier ?? 'N/A' }}</div>
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                            <span>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}</span>
                                            <span>&bull;</span>
                                            <span>{{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H:i') }} WIB</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    @if($detail->isAllRevisionsApproved())
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800/60">Finalized</span>
                                    @elseif($detail->isRevisionStarted())
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-black uppercase bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/60">In Progress</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-black uppercase bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600">Belum Mulai</span>
                                    @endif
                                </div>
                            </div>

                            @if($detail->thesis && $detail->thesis->title)
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 italic">"{{ $detail->thesis->title }}"</p>
                            @endif

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach([['label' => 'Penguji 1', 'name' => $detail->examiner1?->name, 'id' => $detail->examiner1_id, 'rev' => $rev1], ['label' => 'Penguji 2', 'name' => $detail->examiner2?->name, 'id' => $detail->examiner2_id, 'rev' => $rev2]] as $exItem)
                                    <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/80 flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="text-[11px] font-black uppercase text-slate-500 dark:text-slate-400">{{ $exItem['label'] }}</div>
                                            <div class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ $exItem['name'] ?? '-' }}</div>
                                            <div class="mt-0.5">
                                                @if($exItem['rev'])
                                                    @if($exItem['rev']->isApproved())
                                                        <span class="text-[11px] font-black uppercase text-emerald-600 dark:text-emerald-400">Selesai</span>
                                                    @elseif($exItem['rev']->isResubmitted())
                                                        <span class="text-[11px] font-black uppercase text-blue-600 dark:text-blue-400">Terkirim</span>
                                                    @else
                                                        <span class="text-[11px] font-black uppercase text-orange-600 dark:text-orange-400">Dikirim</span>
                                                    @endif
                                                @else
                                                    <span class="text-[11px] font-black uppercase text-slate-500 dark:text-slate-400">Belum Ada</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if(in_array(auth()->user()->role, ['admin', 'kaprodi']) && $exItem['id'])
                                            <a href="{{ route('seminar-examiner.show', ['detail' => $detail->id, 'target_examiner_id' => $exItem['id'], 'redirect_to' => 'monitoring-revisions']) }}"
                                               class="p-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-orange-600 shrink-0"
                                               title="Kelola / Input Revisi {{ $exItem['label'] }}" aria-label="Kelola revisi {{ $exItem['name'] ?? $exItem['label'] }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/80">
                                <a href="{{ route('monitoring.seminar-revisions.print', $detail->id) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-colors">
                                    Cetak Revisi
                                </a>
                                @if($detail->examiner1_id)
                                    <a href="{{ route('monitoring.seminar-revisions.print', ['detail' => $detail->id, 'examiner_id' => $detail->examiner1_id]) }}" target="_blank"
                                       class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 rounded-xl text-xs font-bold">
                                        Revisi P1
                                    </a>
                                @endif
                                @if($detail->examiner2_id)
                                    <a href="{{ route('monitoring.seminar-revisions.print', ['detail' => $detail->id, 'examiner_id' => $detail->examiner2_id]) }}" target="_blank"
                                       class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 rounded-xl text-xs font-bold">
                                        Revisi P2
                                    </a>
                                @endif
                                @if($detail->isAllRevisionsApproved())
                                    <a href="{{ route('monitoring.seminar-scores.berita-acara', $detail->id) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 rounded-xl text-xs font-bold">
                                        Berita Acara
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-12 px-6 text-center text-xs font-medium text-slate-500 dark:text-slate-400">
                            Belum ada mahasiswa yang dijadwalkan seminar.
                        </div>
                    @endforelse
                </div>
            </div>
            
            @if($seminarDetails->hasPages())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
                    {{ $seminarDetails->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

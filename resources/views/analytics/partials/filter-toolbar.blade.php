        <!-- FILTER TOOLBAR CARD -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm transition-all">
            <div role="button" tabindex="0" :aria-expanded="filterOpen ? 'true' : 'false'" aria-label="Buka atau tutup filter data analitik" class="flex items-center justify-between cursor-pointer select-none rounded-xl" @click="filterOpen = !filterOpen" @keydown.enter.prevent="filterOpen = !filterOpen" @keydown.space.prevent="filterOpen = !filterOpen">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-200/50 dark:border-orange-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">Filter Data Analitik</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Saring visualisasi grafik berdasarkan gelombang, rentang angkatan (misal 2020-2022), dosen, status, dan tanggal.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if(!empty($filters['wave_id']) || !empty($filters['entry_year_from']) || !empty($filters['entry_year_to']) || !empty($filters['dosen_id']) || ($filters['status'] ?? 'all') !== 'all' || !empty($filters['date_from']) || !empty($filters['date_to']))
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-black bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30">
                            Filter Aktif
                            @if(!empty($filters['entry_year_from']) || !empty($filters['entry_year_to']))
                                • {{ $filters['entry_year_label'] ?? '' }}
                            @endif
                        </span>
                    @endif
                    <span aria-hidden="true" class="p-1 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': filterOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </div>
            </div>

            <!-- Filter Inputs -->
            <form action="{{ route('analytics.index') }}" method="GET" x-show="filterOpen" x-transition class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <!-- Filter Gelombang -->
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Gelombang Pelaksanaan</label>
                        <select name="wave_id" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0">
                            <option value="all" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Semua Gelombang</option>
                            @foreach($waves as $w)
                                <option value="{{ $w->id }}" {{ ($filters['wave_id'] ?? '') == $w->id ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                    {{ $w->name }} {{ $w->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Angkatan: Per Angkatan & Rentang Angkatan -->
                    <div x-data="{ 
                        cohortType: '{{ ($filters['cohort_type'] ?? 'single') === 'range' ? 'range' : 'single' }}' 
                    }" class="relative">
                        <input type="hidden" name="cohort_type" :value="cohortType">

                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                <span x-show="cohortType === 'single'">Per Angkatan</span>
                                <span x-show="cohortType === 'range'">Rentang Angkatan</span>
                            </label>
                            
                            <!-- Mode Switcher Tabs -->
                            <div class="inline-flex items-center p-0.5 rounded-lg bg-slate-200/70 dark:bg-slate-700/70 text-[11px] font-bold">
                                <button type="button" 
                                        @click="cohortType = 'single'"
                                        :class="cohortType === 'single' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'"
                                        class="px-2 py-0.5 rounded-md transition-all cursor-pointer select-none"
                                        title="Saring satu tahun angkatan spesifik">
                                    Per Angkatan
                                </button>
                                <button type="button" 
                                        @click="cohortType = 'range'"
                                        :class="cohortType === 'range' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'"
                                        class="px-2 py-0.5 rounded-md transition-all cursor-pointer select-none"
                                        title="Saring rentang beberapa angkatan">
                                    Rentang
                                </button>
                            </div>
                        </div>

                        <!-- 1. Mode Per Angkatan (Single Select) -->
                        <div x-show="cohortType === 'single'">
                            <select name="entry_year" 
                                    :disabled="cohortType !== 'single'"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0">
                                <option value="all" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Semua Angkatan</option>
                                @foreach($cohortYears as $year)
                                    <option value="{{ $year }}" {{ ($filters['entry_year'] ?? '') == $year ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                        Angkatan {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Mode Rentang Angkatan (From - To) -->
                        <div x-show="cohortType === 'range'" class="grid grid-cols-2 gap-1.5">
                            <div>
                                <select name="entry_year_from" 
                                        :disabled="cohortType !== 'range'"
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0" 
                                        title="Dari Tahun Angkatan">
                                    <option value="all" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Dari Thn</option>
                                    @foreach($cohortYears as $year)
                                        <option value="{{ $year }}" {{ ($filters['entry_year_from'] ?? '') == $year ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="entry_year_to" 
                                        :disabled="cohortType !== 'range'"
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0" 
                                        title="Sampai Tahun Angkatan">
                                    <option value="all" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Sampai Thn</option>
                                    @foreach($cohortYears as $year)
                                        <option value="{{ $year }}" {{ ($filters['entry_year_to'] ?? '') == $year ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Dosen Pembimbing -->
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Dosen Pembimbing</label>
                        <select name="dosen_id" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0">
                            <option value="all" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Semua Dosen</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}" {{ ($filters['dosen_id'] ?? '') == $d->id ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status Skripsi -->
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Status Skripsi</label>
                        <select name="status" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-0">
                            <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Semua Status</option>
                            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Skripsi Aktif</option>
                            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Sudah Selesai / Lulus</option>
                            <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pending Pengajuan</option>
                        </select>
                    </div>

                    <!-- Rentang Tanggal (From - To) -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Dari Tgl</label>
                            <x-date-picker name="date_from"
                                           :value="$filters['date_from'] ?? ''"
                                           placeholder="Dari tgl"
                                           compact
                                           class="w-full" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Sampai Tgl</label>
                            <x-date-picker name="date_to"
                                           :value="$filters['date_to'] ?? ''"
                                           placeholder="Sampai tgl"
                                           compact
                                           class="w-full" />
                        </div>
                    </div>
                </div>

                <!-- Submit & Reset Buttons -->
                <div class="flex items-center justify-end gap-2.5 mt-5">
                    <a href="{{ route('analytics.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        Reset Filter
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md shadow-orange-600/20 hover:shadow-orange-600/30 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

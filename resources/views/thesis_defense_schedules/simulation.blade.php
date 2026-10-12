<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Sidang', 'route' => route('thesis-defense-schedules.index')],
            ['label' => 'Simulasi Jadwal Otomatis', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6" x-data="defenseSimulation()">
        <!-- Header Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl border border-slate-800">
            <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-orange-500/10 blur-3xl"></div>
            <div class="absolute -left-12 -bottom-12 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-500/20 border border-orange-500/30 text-orange-300 text-xs font-bold uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Fitur Otomatis Cerdas
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Simulasi & Generate Jadwal Sidang Skripsi
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                        Sistem mengelompokkan mahasiswa dengan <span class="text-orange-400 font-bold">Pembimbing 1 yang sama ke dalam 1 ruangan</span>, mencegah bentrok jadwal dosen antar-ruangan, dan membagi kuota peserta secara optimal (2 atau 3 ruangan, kapasitas 7-8 peserta).
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('thesis-defense-schedules.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 text-xs font-bold border border-slate-700 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Jadwal
                    </a>
                </div>
            </div>
        </div>

        <!-- Notification Message -->
        <div x-show="alertMessage" x-transition class="p-4 rounded-xl border text-xs font-bold"
             :class="alertType === 'success' ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300' : 'bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300'">
            <div class="flex items-center justify-between">
                <span x-text="alertMessage"></span>
                <button type="button" @click="alertMessage = ''" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
        </div>

        <!-- Main Grid: Parameters Form (Left) & Simulation Output (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- PARAMETERS FORM (Col 1-4) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 sm:p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-5">
                    <div class="border-b border-slate-100 dark:border-slate-700/80 pb-4">
                        <h2 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            Parameter Penjadwalan
                        </h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Konfigurasi ruangan, waktu, dan kapasitas</p>
                    </div>

                    <!-- Gelombang -->
                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                            Gelombang Pelaksanaan
                        </label>
                        <select x-model="form.wave_id" @change="onWaveChange()" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-2 focus:ring-orange-500">
                            @foreach($waves as $w)
                                <option value="{{ $w->id }}" {{ $selectedWaveId == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }} {{ $w->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Pelaksanaan -->
                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                            Tanggal Sidang
                        </label>
                        <input type="date" x-model="form.date" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-2 focus:ring-orange-500">
                    </div>

                    <!-- Pilihan Jumlah Ruangan -->
                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                            Jumlah Ruangan Per Hari
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" @click="setRoomCount(2)"
                                    :class="form.room_count === 2 ? 'bg-orange-600 text-white font-bold border-orange-600' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'"
                                    class="py-2 text-xs rounded-xl border transition-all text-center">
                                2 Ruangan
                            </button>
                            <button type="button" @click="setRoomCount(3)"
                                    :class="form.room_count === 3 ? 'bg-orange-600 text-white font-bold border-orange-600' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'"
                                    class="py-2 text-xs rounded-xl border transition-all text-center">
                                3 Ruangan
                            </button>
                            <button type="button" @click="setRoomCount(4)"
                                    :class="form.room_count === 4 ? 'bg-orange-600 text-white font-bold border-orange-600' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'"
                                    class="py-2 text-xs rounded-xl border transition-all text-center">
                                4 Ruangan
                            </button>
                        </div>
                    </div>

                    <!-- Nama Ruangan Dinamis -->
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Nama-Nama Ruangan
                        </label>
                        <template x-for="(roomName, rIdx) in form.room_names" :key="rIdx">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-slate-400 uppercase w-12" x-text="'R. ' + (rIdx + 1)"></span>
                                <input type="text" x-model="form.room_names[rIdx]" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-2 focus:ring-orange-500" placeholder="Contoh: Ruang Sidang Utama (R. 201)">
                            </div>
                        </template>
                    </div>

                    <!-- Kapasitas & Durasi -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                Maks Peserta / Ruang
                            </label>
                            <select x-model.number="form.max_students_per_room" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-2 focus:ring-orange-500">
                                <option value="6">6 Peserta</option>
                                <option value="7">7 Peserta</option>
                                <option value="8" selected>8 Peserta (Standar)</option>
                                <option value="9">9 Peserta</option>
                                <option value="10">10 Peserta</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                Durasi / Sesi
                            </label>
                            <select x-model.number="form.duration_minutes" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-2 focus:ring-orange-500">
                                <option value="45">45 Menit</option>
                                <option value="50">50 Menit</option>
                                <option value="60" selected>60 Menit (Standar)</option>
                                <option value="75">75 Menit</option>
                            </select>
                        </div>
                    </div>

                    <!-- Jam Mulai & Istirahat -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                Jam Mulai Sesi 1
                            </label>
                            <input type="time" x-model="form.start_time" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                Jeda Ishoma
                            </label>
                            <div class="flex items-center h-[38px]">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="form.break_enabled" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                    <span class="ml-2 text-xs font-medium text-slate-700 dark:text-slate-300">11:30 - 13:00</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Eksekusi Simulasi -->
                    <div class="pt-2">
                        <button type="button" @click="runSimulation()" :disabled="isLoading"
                                class="w-full py-3 px-4 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-md transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <template x-if="isLoading">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </template>
                            <template x-if="!isLoading">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </template>
                            <span x-text="isLoading ? 'Memproses Simulasi...' : 'Jalankan Simulasi Jadwal'"></span>
                        </button>
                    </div>

                    <!-- Info Pendaftar Terdeteksi -->
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/80">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">Pendaftar Siap Jadwal:</span>
                            <span class="font-bold text-orange-600 dark:text-orange-400 px-2 py-0.5 rounded-md bg-orange-100 dark:bg-orange-500/10">
                                {{ $candidates->count() }} Mahasiswa
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Sudah ACC sidang dan belum memiliki jadwal terbit.</p>
                    </div>
                </div>
            </div>

            <!-- SIMULATION PREVIEW RESULT (Col 5-12) -->
            <div class="lg:col-span-8 space-y-6">
                <!-- State: Initial / Belum disimulasikan -->
                <div x-show="!simulationResult && !isLoading" class="bg-white dark:bg-slate-800 rounded-2xl p-10 border border-dashed border-slate-300 dark:border-slate-700 text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Pratinjau Simulasi Belum Dibuat</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                        Tentukan parameter di samping lalu klik tombol <strong>"Jalankan Simulasi Jadwal"</strong> untuk menghasilkan rancangan jadwal cerdas yang bebas bentrok.
                    </p>
                </div>

                <!-- State: Hasil Simulasi Tersedia -->
                <div x-show="simulationResult" x-cloak class="space-y-6">
                    
                    <!-- KPI Cards Summary -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <!-- Bebas Bentrok -->
                        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xs">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Deteksi Konflik</span>
                            <div class="mt-1 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full" :class="simulationResult?.has_conflict ? 'bg-rose-500 animate-pulse' : 'bg-emerald-500'"></span>
                                <span class="text-sm font-black" :class="simulationResult?.has_conflict ? 'text-rose-600' : 'text-emerald-600 dark:text-emerald-400'"
                                      x-text="simulationResult?.has_conflict ? 'Ada Bentrok' : '100% Aman'"></span>
                            </div>
                            <span class="text-[10px] text-slate-400 mt-0.5 block" x-text="(simulationResult?.conflicts?.length || 0) + ' bentrok terdeteksi'"></span>
                        </div>

                        <!-- Peserta Terjadwal -->
                        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xs">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Peserta Terjadwal</span>
                            <div class="mt-1 text-sm font-black text-slate-800 dark:text-slate-100"
                                 x-text="(simulationResult?.stats?.scheduled_candidates || 0) + ' / ' + (simulationResult?.stats?.total_candidates || 0)"></div>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5 block">Mahasiswa siap sidang</span>
                        </div>

                        <!-- Pengelompokan P1 -->
                        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xs">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Grouping Pembimbing 1</span>
                            <div class="mt-1 text-sm font-black text-orange-600 dark:text-orange-400"
                                 x-text="(simulationResult?.stats?.grouping_p1_success_rate || 100) + '% Efisien'"></div>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Terpusat di 1 ruangan</span>
                        </div>

                        <!-- Ruangan Terpakai -->
                        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xs">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Ruangan Terpakai</span>
                            <div class="mt-1 text-sm font-black text-slate-800 dark:text-slate-100"
                                 x-text="(simulationResult?.rooms?.length || 0) + ' Ruangan'"></div>
                            <span class="text-[10px] text-slate-400 mt-0.5 block" x-text="simulationResult?.date_formatted"></span>
                        </div>
                    </div>

                    <!-- Room Tab Buttons & Action Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-2 bg-slate-100 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700/80">
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                            <button type="button" @click="activeRoomTab = 'all'"
                                    :class="activeRoomTab === 'all' ? 'bg-white dark:bg-slate-800 text-orange-600 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800'"
                                    class="px-3.5 py-2 text-xs rounded-xl transition-all whitespace-nowrap">
                                Semua Ruangan
                            </button>
                            <template x-for="(room, rIdx) in simulationResult?.rooms" :key="rIdx">
                                <button type="button" @click="activeRoomTab = rIdx"
                                        :class="activeRoomTab === rIdx ? 'bg-white dark:bg-slate-800 text-orange-600 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800'"
                                        class="px-3.5 py-2 text-xs rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5">
                                    <span x-text="room.room_name"></span>
                                    <span class="px-1.5 py-0.5 text-[9px] rounded-full bg-slate-100 dark:bg-slate-700 font-bold text-slate-600 dark:text-slate-300" x-text="room.sessions?.length + ' Mhs'"></span>
                                </button>
                            </template>
                        </div>

                        <!-- Apply Button -->
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" @click="applySchedule()" :disabled="isApplying"
                                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all active:scale-95 flex items-center gap-1.5">
                                <template x-if="isApplying">
                                    <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </template>
                                <template x-if="!isApplying">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </template>
                                <span x-text="isApplying ? 'Menerbitkan...' : 'Terapkan Sebagai Jadwal Resmi'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Room Cards List -->
                    <template x-for="(room, rIdx) in simulationResult?.rooms" :key="rIdx">
                        <div x-show="activeRoomTab === 'all' || activeRoomTab === rIdx"
                             class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden space-y-4">
                            
                            <!-- Room Header -->
                            <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/40">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-full bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300 text-[10px] font-black uppercase tracking-wider" x-text="'Ruangan ' + (rIdx + 1)"></span>
                                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400" x-text="room.sessions?.length + ' Peserta'"></span>
                                        </div>
                                        <input type="text" x-model="room.title" class="w-full text-sm font-black bg-transparent border-0 border-b border-dashed border-slate-300 dark:border-slate-600 focus:ring-0 p-0 text-slate-800 dark:text-slate-100">
                                    </div>

                                    <!-- Ketua Sidang & Moderator Selection -->
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div>
                                            <label class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Ketua Sidang</label>
                                            <select x-model="room.chairman_id" class="text-xs py-1 px-2.5 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-slate-700 dark:text-slate-200">
                                                <option value="">Pilih Ketua</option>
                                                <template x-for="d in simulationResult?.dosens" :key="d.id">
                                                    <option :value="d.id" :selected="d.id == room.chairman_id" x-text="d.name"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Moderator</label>
                                            <select x-model="room.moderator_id" class="text-xs py-1 px-2.5 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-slate-700 dark:text-slate-200">
                                                <option value="">Pilih Moderator</option>
                                                <template x-for="d in simulationResult?.dosens" :key="d.id">
                                                    <option :value="d.id" :selected="d.id == room.moderator_id" x-text="d.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sessions Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs text-left">
                                    <thead>
                                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                            <th class="py-3 px-4 w-12 text-center">No</th>
                                            <th class="py-3 px-4 w-32">Waktu</th>
                                            <th class="py-3 px-4">Mahasiswa & Judul</th>
                                            <th class="py-3 px-4">Pembimbing 1 & 2</th>
                                            <th class="py-3 px-4">Penguji 1</th>
                                            <th class="py-3 px-4">Penguji 2</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                                        <template x-for="(session, sIdx) in room.sessions" :key="sIdx">
                                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                                                <td class="py-3.5 px-4 text-center font-bold text-slate-400" x-text="sIdx + 1"></td>
                                                <td class="py-3.5 px-4 whitespace-nowrap">
                                                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="session.start_time + ' - ' + session.end_time"></span>
                                                    <span class="text-[10px] text-slate-400 block">WIB</span>
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <div class="font-bold text-slate-800 dark:text-slate-100" x-text="session.thesis.student_name"></div>
                                                    <div class="text-[10px] text-slate-400 font-normal" x-text="'NIM: ' + session.thesis.student_nim"></div>
                                                    <div class="text-[11px] text-slate-600 dark:text-slate-300 mt-1 line-clamp-1 italic font-normal" x-text="session.thesis.title"></div>
                                                </td>
                                                <td class="py-3.5 px-4 whitespace-nowrap">
                                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded border text-[10px] font-bold mb-1" :class="session.thesis.p1_color">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                        <span x-text="'P1: ' + session.thesis.pembimbing1_name"></span>
                                                    </div>
                                                    <div x-show="session.thesis.pembimbing2_name" class="text-[10px] text-slate-500 block" x-text="'P2: ' + session.thesis.pembimbing2_name"></div>
                                                </td>
                                                <td class="py-3.5 px-4 whitespace-nowrap">
                                                    <select x-model="session.examiner1_id" class="text-xs py-1 px-2 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-slate-700 dark:text-slate-200 w-44">
                                                        <template x-for="d in simulationResult?.dosens" :key="d.id">
                                                            <option :value="d.id" :selected="d.id == session.examiner1_id" x-text="d.name"></option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td class="py-3.5 px-4 whitespace-nowrap">
                                                    <select x-model="session.examiner2_id" class="text-xs py-1 px-2 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-slate-700 dark:text-slate-200 w-44">
                                                        <template x-for="d in simulationResult?.dosens" :key="d.id">
                                                            <option :value="d.id" :selected="d.id == session.examiner2_id" x-text="d.name"></option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                                                        Aman
                                                    </span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function defenseSimulation() {
            return {
                isLoading: false,
                isApplying: false,
                alertMessage: '',
                alertType: 'success',
                activeRoomTab: 'all',
                form: {
                    wave_id: '{{ $selectedWaveId }}',
                    date: '{{ \Carbon\Carbon::tomorrow()->toDateString() }}',
                    room_count: 2,
                    room_names: [
                        'Ruang Sidang Utama (R. 201)',
                        'Ruang Smart Class (R. 202)'
                    ],
                    max_students_per_room: 8,
                    duration_minutes: 60,
                    start_time: '08:30',
                    break_enabled: true,
                    break_start: '11:30',
                    break_end: '13:00'
                },
                simulationResult: null,

                setRoomCount(count) {
                    this.form.room_count = count;
                    const defaults = [
                        'Ruang Sidang Utama (R. 201)',
                        'Ruang Smart Class (R. 202)',
                        'Ruang Multimedia (R. 203)',
                        'Ruang Rapat Senat (R. 204)',
                    ];
                    this.form.room_names = [];
                    for (let i = 0; i < count; i++) {
                        this.form.room_names.push(defaults[i] || 'Ruangan ' + (i + 1));
                    }
                },

                onWaveChange() {
                    window.location.href = '{{ route("thesis-defense-schedules.simulation") }}?wave_id=' + this.form.wave_id;
                },

                async runSimulation() {
                    this.isLoading = true;
                    this.alertMessage = '';
                    try {
                        const response = await fetch('{{ route("thesis-defense-schedules.simulation.run") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();
                        if (data.status === 'empty') {
                            this.alertType = 'error';
                            this.alertMessage = data.message;
                            this.simulationResult = null;
                        } else if (data.status === 'success') {
                            this.simulationResult = data;
                            this.activeRoomTab = 'all';
                            this.alertType = 'success';
                            this.alertMessage = 'Simulasi jadwal sidang berhasil digenerate! Silakan periksa pratinjau sebelum menerapkan ke jadwal resmi.';
                        } else {
                            this.alertType = 'error';
                            this.alertMessage = data.message || 'Gagal memproses simulasi.';
                        }
                    } catch (e) {
                        this.alertType = 'error';
                        this.alertMessage = 'Terjadi kesalahan saat memproses simulasi: ' + e.message;
                    } finally {
                        this.isLoading = false;
                    }
                },

                async applySchedule() {
                    if (!confirm('Apakah Anda yakin ingin menerapkan hasil simulasi ini menjadi Jadwal Sidang Resmi? Jadwal akan diterbitkan dan dapat dilihat oleh dosen serta mahasiswa.')) {
                        return;
                    }

                    this.isApplying = true;
                    try {
                        const payload = {
                            wave_id: this.simulationResult.wave_id,
                            date: this.simulationResult.date,
                            rooms: this.simulationResult.rooms
                        };

                        const response = await fetch('{{ route("thesis-defense-schedules.simulation.apply") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });

                        const res = await response.json();
                        if (res.status === 'success') {
                            alert(res.message);
                            window.location.href = res.redirect_url;
                        } else {
                            alert(res.message || 'Gagal menyimpan jadwal.');
                        }
                    } catch (e) {
                        alert('Gagal menerapkan jadwal: ' + e.message);
                    } finally {
                        this.isApplying = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>

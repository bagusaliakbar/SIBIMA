            @php
                $isGraduated = $progress['isGraduated'];
                $progressPercent = $progress['percent'];
                $seminarDone = $progress['seminarDone'];
                $defenseDone = $progress['defenseDone'];
                $currentStage = $progress['currentStage'];
                $stages = $progress['stages'];
            @endphp

            @if($isStale)
                <div class="{{ $isCriticalStale ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/60' : 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/60' }} border p-4 sm:p-5 rounded-2xl mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 shadow-xs">
                    <div class="flex items-start gap-3.5 min-w-0">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 {{ $isCriticalStale ? 'bg-rose-600 text-white shadow-rose-600/20 animate-pulse' : 'bg-amber-500 text-white shadow-amber-500/20' }} rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0 shadow-md mt-0.5">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between sm:justify-start gap-2 flex-wrap">
                                <h4 class="text-xs font-black {{ $isCriticalStale ? 'text-rose-950 dark:text-rose-100' : 'text-amber-950 dark:text-amber-100' }} uppercase tracking-wider">
                                    {{ $isCriticalStale ? 'Peringatan Bimbingan Macet (> 2 Minggu)' : 'Peringatan Dini Bimbingan (> 1 Minggu)' }}
                                </h4>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-black uppercase shrink-0 {{ $isCriticalStale ? 'bg-rose-200 text-rose-900 dark:bg-rose-900 dark:text-rose-200' : 'bg-amber-200 text-amber-900 dark:bg-amber-900 dark:text-amber-200' }}">
                                    {{ $daysSinceLastSession }} Hari Belum Bimbingan
                                </span>
                            </div>
                            <p class="text-xs {{ $isCriticalStale ? 'text-rose-800 dark:text-rose-300' : 'text-amber-800 dark:text-amber-300' }} font-medium mt-1 leading-relaxed">
                                Sesuai standar prodi, mahasiswa dianjurkan bimbingan minimal 1x per minggu. Segera jadwalkan bimbingan dengan dosen pembimbing Anda agar skripsi berjalan lancar!
                            </p>
                        </div>
                    </div>
                    <div class="w-full sm:w-auto shrink-0">
                        <a href="{{ route('mentoring-sessions.create') }}" class="w-full sm:w-auto min-h-[38px] px-4 py-2 {{ $isCriticalStale ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20' : 'bg-amber-600 hover:bg-amber-700 shadow-amber-600/20' }} text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-md hover:scale-[1.02] active:scale-95 flex items-center justify-center cursor-pointer">
                            Jadwalkan Bimbingan
                        </a>
                    </div>
                </div>
            @endif

            @if(Auth::user()->is_critical_semester)
                <div class="bg-rose-50/90 dark:bg-rose-950/40 border border-rose-200/90 dark:border-rose-800/60 rounded-2xl p-4 sm:p-5 shadow-xs mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                    <div class="flex items-start gap-3.5 min-w-0">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 bg-rose-600 dark:bg-rose-500 text-white rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0 shadow-md shadow-rose-600/20 mt-0.5">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between sm:justify-start gap-2 flex-wrap">
                                <h4 class="text-xs font-black uppercase tracking-wider text-rose-950 dark:text-rose-100">Peringatan Masa Studi Kritikal</h4>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-200/80 text-rose-900 dark:bg-rose-900/60 dark:text-rose-200 border border-rose-300/80 dark:border-rose-700/60 shrink-0">Semester {{ Auth::user()->current_semester }}</span>
                            </div>
                            <p class="text-xs text-rose-800 dark:text-rose-300/90 font-medium mt-1 leading-relaxed">
                                Anda telah memasuki semester batas akhir. Harap prioritaskan penyelesaian skripsi untuk menghindari sanksi Drop Out (DO). Hubungi Koordinator Prodi jika butuh asistensi khusus.
                            </p>
                        </div>
                    </div>
                    <div class="w-full sm:w-auto shrink-0 flex justify-end">
                        <span class="w-full sm:w-auto text-center px-3 py-1.5 bg-rose-200/60 dark:bg-rose-900/40 text-rose-900 dark:text-rose-200 text-[11px] font-black uppercase tracking-widest rounded-xl border border-rose-300/60 dark:border-rose-700/60">
                            Perhatian Khusus
                        </span>
                    </div>
                </div>
            @endif
            <!-- Linear Progress Stepper (Hero Section) - Roadmap Akademik Mahasiswa (5 Tahap) -->
            <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 sm:p-8 rounded-3xl shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-200/80 dark:border-slate-700/50 mb-8 relative overflow-hidden group">
                <!-- Decorative Ambient Glows -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-orange-500/5 rounded-full blur-3xl group-hover:bg-orange-500/10 transition-colors duration-700 pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-colors duration-700 pointer-events-none"></div>

                <!-- Hero Section Header: Title, Pembimbing, and Progress Gauge -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 relative z-10">
                    <div>
                        @if($thesis)
                            <h3 class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-1 max-w-2xl leading-snug">
                                {{ $thesis->final_title ?? $thesis->title }}
                            </h3>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3.5">
                                Pembimbing: {{ $thesis->pembimbing1->name ?? 'Belum Ditentukan' }} & {{ $thesis->pembimbing2->name ?? 'Belum Ditentukan' }}
                            </p>
                            @if(!$thesis->isAccSidangFinal())
                                <button type="button" @click="$dispatch('open-edit-thesis-modal')" onclick="document.getElementById('edit-thesis-modal').classList.remove('hidden'); if(document.getElementById('edit-thesis-modal').__x) { document.getElementById('edit-thesis-modal').__x.$data.open = true; }" class="inline-flex items-center px-3 py-1.5 bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-100 dark:hover:bg-orange-900/50 transition-all border border-orange-200/80 dark:border-orange-800/60 shadow-xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Revisi Judul / Deskripsi
                                </button>
                            @endif
                        @else
                            <h3 class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight mb-1">
                                Selamat Datang di SIBIMA FASILKOM
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3.5">
                                Mulai perjalanan tugas akhir Anda dengan mengajukan judul skripsi melalui tombol di bawah atau klik tahap Judul.
                            </p>
                            <a href="{{ route('theses.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-[11px] font-black uppercase tracking-wider rounded-xl transition-all shadow-md hover:scale-105 active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                Ajukan Judul Skripsi Sekarang
                            </a>
                        @endif
                    </div>

                    <div class="w-full md:w-auto shrink-0">
                        <div class="w-full sm:w-auto min-w-[240px] bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/60 shadow-xs overflow-hidden divide-y divide-slate-200/70 dark:divide-slate-700/60">
                            <!-- Progress Metric Row -->
                            <div class="flex items-center justify-between gap-5 p-4">
                                <div>
                                    <p class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-none mb-1.5">Capaian Saat Ini</p>
                                    <p class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tighter">{{ round($progressPercent) }}%</p>
                                </div>
                                <div class="w-px h-10 bg-slate-200 dark:bg-slate-700"></div>
                                <div class="relative w-14 h-14 shrink-0">
                                    <svg class="w-14 h-14 transform -rotate-90" viewBox="0 0 56 56">
                                        <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="transparent" class="text-slate-200 dark:text-slate-800" />
                                        <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="transparent" stroke-dasharray="150.7" stroke-dashoffset="{{ 150.7 - (150.7 * $progressPercent / 100) }}" class="text-orange-600 transition-all duration-1000 ease-out" />
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Integrated Action: Lihat Histori -->
                            <a href="{{ route('student.history') }}" class="flex items-center justify-between gap-2 px-4 py-2.5 bg-white/70 dark:bg-slate-800/50 hover:bg-orange-50/80 dark:hover:bg-orange-950/40 text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 transition-all group/hist cursor-pointer">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover/hist:text-orange-600 dark:group-hover/hist:text-orange-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Lihat Histori Perjalanan</span>
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover/hist:text-orange-600 dark:group-hover/hist:text-orange-400 group-hover/hist:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Roadmap 3D (2D Stepper Hidden) -->
                <div x-data="{ roadmapMode: '3d' }" class="relative z-10">
                    <!-- 3D Interactive Mode -->
                    <div>
                        <x-mentoring-3d-process
                            role="mahasiswa"
                            :progress="$progress"
                            :pastSessionsCount="$pastSessionsCount ?? 0"
                            :embedded="true"
                        />
                    </div>

                    <!-- Linear Progress Stepper (6 Visual Stages - 2D Mode Hidden) -->
                    <div x-show="false" x-cloak class="hidden overflow-x-auto pb-4 -mx-4 px-4 sm:mx-0 sm:px-0 hide-scrollbar snap-x snap-mandatory">
                    <div class="min-w-[680px] sm:min-w-0 py-3">
                        <div class="flex items-start justify-between">
                        @foreach($stages as $index => $stage)
                            @php 
                                $stageNum = $stage['number'] ?? ($index + 1);
                                $isCompleted = !empty($stage['is_completed']);
                                $isCurrent = !empty($stage['is_current']);
                                $isLocked = !empty($stage['is_locked']);
                                $stageUrl = $stage['url'] ?? '#';
                                $isFirst = $index === 0;

                                // Connector line status from previous node
                                if (!$isFirst) {
                                    $prevStage = $stages[$index - 1];
                                    $prevCompleted = !empty($prevStage['is_completed']);
                                    $isLineCompleted = $prevCompleted && $isCompleted;
                                    $isLineActive = $prevCompleted && $isCurrent;
                                }
                            @endphp
                            
                            <!-- Stage Item with Connector Line -->
                            <div class="flex-1 flex flex-col items-center relative snap-center">
                                
                                <!-- Horizontal Connecting Line -->
                                @if(!$isFirst)
                                    <div class="absolute top-7 right-[50%] left-[-50%] h-1.5 -translate-y-1/2 z-0 rounded-full transition-all duration-500
                                        {{ $isLineCompleted ? 'bg-emerald-500' : ($isLineActive ? 'bg-gradient-to-r from-emerald-500 via-orange-400 to-orange-500' : 'bg-slate-200 dark:bg-slate-700') }}">
                                    </div>
                                @endif

                                <!-- Clickable Milestone Node -->
                                <a href="{{ $stageUrl }}" 
                                   title="{{ $isLocked ? 'Tahap Terkunci: Selesaikan tahap sebelumnya untuk membuka' : ('Buka Menu ' . $stage['name']) }}"
                                   class="relative z-10 flex flex-col items-center group/node cursor-pointer text-center w-full px-1 transition-all duration-300 {{ $isLocked ? 'opacity-70 hover:opacity-100' : 'hover:scale-105' }}">
                                    
                                    <!-- Node Circle / Icon Wrapper -->
                                    <div class="relative">
                                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-300 relative
                                            {{ $isCompleted ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25 ring-2 ring-emerald-500/20' : '' }}
                                            {{ $isCurrent ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30 scale-110 ring-4 ring-orange-500/25 animate-pulse' : '' }}
                                            {{ $isLocked ? 'bg-slate-100 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 border border-slate-200/80 dark:border-slate-700/80 group-hover/node:border-slate-300 dark:group-hover/node:border-slate-600' : '' }}">
                                            
                                            <!-- Step Icons -->
                                            @if($isCompleted)
                                                <!-- Selesai (Completed): Bold Checkmark -->
                                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            @elseif($isLocked)
                                                <!-- Terkunci (Locked): Lock Icon -->
                                                <svg class="w-5 h-5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            @else
                                                <!-- Sedang Berjalan (Active): Specific Stage Visual Icon matching Sidebar -->
                                                @if($stageNum == 1)
                                                    <!-- Pengajuan Judul -->
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                @elseif($stageNum == 2)
                                                    <!-- Pendaftaran Seminar -->
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                @elseif($stageNum == 3)
                                                    <!-- Revisi Seminar -->
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                @elseif($stageNum == 4)
                                                    <!-- Pendaftaran Sidang -->
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                                @elseif($stageNum == 5)
                                                    <!-- Revisi Sidang -->
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                @else
                                                    <!-- Yudisium & SKL -->
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                                    </svg>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Step Label & Status Badge -->
                                    <div class="mt-4 text-center w-full px-1">
                                        <div class="min-h-[2rem] flex items-center justify-center">
                                            <h4 class="text-[11px] font-black uppercase tracking-wider leading-tight transition-colors group-hover/node:text-orange-600 dark:group-hover/node:text-orange-400
                                                {{ $isCompleted ? 'text-slate-800 dark:text-slate-100' : '' }}
                                                {{ $isCurrent ? 'text-orange-600 dark:text-orange-400 font-extrabold' : '' }}
                                                {{ $isLocked ? 'text-slate-500 dark:text-slate-400' : '' }}">
                                                {{ $stage['number'] }}. {{ $stage['short_name'] ?? $stage['name'] }}
                                            </h4>
                                        </div>
                                        
                                        <!-- Three Visual Status Badges: Selesai | Sedang Berjalan (Active) | Terkunci (Locked) -->
                                        <div class="mt-2 flex justify-center">
                                            @if($isCompleted)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-200/80 dark:border-emerald-800/80 shadow-2xs">
                                                    <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    <span>Selesai {{ !empty($stage['badge']) ? '(' . $stage['badge'] . ')' : '' }}</span>
                                                </span>
                                            @elseif($isCurrent)
                                                <span class="inline-flex items-center gap-1.5 text-[11px] font-black text-orange-700 dark:text-orange-300 bg-orange-50 dark:bg-orange-950/60 px-2.5 py-0.5 rounded-full border border-orange-200/80 dark:border-orange-800/80 shadow-2xs">
                                                    <span class="inline-flex rounded-full h-2 w-2 bg-orange-500 shrink-0"></span>
                                                    <span>Sedang Berjalan {{ !empty($stage['badge']) ? '(' . $stage['badge'] . ')' : '' }}</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/60 px-2 py-0.5 rounded-full border border-slate-200/60 dark:border-slate-700/60">
                                                    <svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    <span>Terkunci</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </div>

                <!-- Mobile Active Stage Quick Card (< 768px) -->
                @php
                    $activeStageItem = collect($stages)->firstWhere('is_current', true) ?? collect($stages)->firstWhere('is_completed', false) ?? $stages[0];
                @endphp
                <div x-show="roadmapMode === '2d'" x-cloak class="block md:hidden mt-4 p-4 rounded-2xl bg-gradient-to-r from-orange-50/90 to-amber-50/40 dark:from-orange-950/30 dark:to-slate-800/40 border border-orange-200/80 dark:border-orange-800/60 shadow-xs">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-orange-600 dark:text-orange-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                            Tahap Aktif Anda Saat Ini
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-orange-100 dark:bg-orange-900/60 text-orange-800 dark:text-orange-200 border border-orange-300/80 dark:border-orange-700/60">
                            Tahap {{ $activeStageItem['number'] }} dari 6
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 truncate">{{ $activeStageItem['name'] }}</h4>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 font-medium leading-tight mt-0.5">{{ $activeStageItem['desc'] }}</p>
                        </div>
                        <a href="{{ $activeStageItem['url'] }}" class="px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-[11px] font-black uppercase tracking-wider shrink-0 transition-all shadow-xs flex items-center gap-1 cursor-pointer">
                            <span>Buka</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
                </div>

                <!-- Footer Stage Insight -->
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-[11px] font-medium text-slate-600 dark:text-slate-300">
                            @if($currentStage == 1)
                                Judul skripsi Anda sedang dalam tahap pengajuan. Pastikan proposal awal dipersiapkan dan pantau persetujuan dari Koordinator Skripsi / Pembimbing.
                            @elseif($currentStage == 2)
                                Tahap Pendaftaran Seminar. Lakukan bimbingan rutin (minimal 8 sesi bimbingan logbook) dan daftarkan diri untuk pelaksanaan Seminar Proposal jika kuota & syarat telah terpenuhi.
                            @elseif($currentStage == 3)
                                Tahap Revisi Seminar. Silakan diskusikan dan tuntaskan seluruh catatan revisi dari tim dosen penguji seminar hingga mendapatkan persetujuan (ACC).
                            @elseif($currentStage == 4)
                                Tahap Pendaftaran Sidang. Selesaikan naskah skripsi lengkap (Bab 4-5) serta konsultasikan dengan pembimbing untuk pendaftaran Sidang Akhir Skripsi.
                            @elseif($currentStage == 5)
                                Tahap Revisi Sidang. Tuntaskan catatan perbaikan naskah dari penguji sidang skripsi dan minta persetujuan revisi dari seluruh dosen penguji.
                            @elseif($currentStage == 6)
                                Tahap Yudisium & SKL. Lengkapi berkas bebas tanggungan (hardcover skripsi, bebas perpus, bebas lab, dan repositori) untuk menerbitkan <span class="text-emerald-600 dark:text-emerald-400 font-bold">Surat Keterangan Lulus (SKL) Resmi</span>.
                            @else
                                Silakan ajukan judul skripsi untuk memulai perjalanan akademik skripsi Anda di SIBIMA.
                            @endif
                        </p>
                    </div>
                </div>
            </div>



            @if($isGraduated)
            <div class="bg-slate-900 dark:bg-slate-800/80 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden mb-8 border border-white/10 backdrop-blur-md">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500/10 rounded-full -ml-24 -mb-24 blur-3xl"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-3xl font-black tracking-tight mb-3 bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Selamat, {{ Auth::user()->name }}!</h2>
                        <p class="text-slate-500 dark:text-slate-400 font-medium text-base leading-relaxed max-w-2xl">
                            Seluruh tahapan skripsi telah Anda selesaikan dengan baik. Anda kini dinyatakan <span class="text-amber-400 font-bold">LULUS</span>. Terima kasih atas dedikasi dan kerja keras Anda selama ini.
                        </p>
                    </div>

                    <div class="hidden lg:block w-px h-24 bg-gradient-to-b from-transparent via-white/10 to-transparent"></div>

                    <div class="text-center md:text-right">
                        <p class="text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1">Tanggal Yudisium</p>
                        <p class="text-xl font-black text-white tracking-tighter">{{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Stats Row Mahasiswa -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <x-stat-card title="Status Skripsi" :value="$thesis ? ($thesis->status === 'completed' ? 'Lulus' : ($thesis->status === 'active' ? 'Aktif' : 'Menunggu')) : 'Belum Ada'" color="emerald">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card title="Pembimbing" :value="$thesis && $thesis->pembimbing1 && $thesis->pembimbing2 ? '' : 'Belum Ada'" color="orange">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </x-slot>
                    @if($thesis && $thesis->pembimbing1 && $thesis->pembimbing2)
                        <x-slot name="subtitle">
                            <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate" title="{{ $thesis->pembimbing1->name }}">1. {{ $thesis->pembimbing1->name }}</p>
                            <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate" title="{{ $thesis->pembimbing2->name }}">2. {{ $thesis->pembimbing2->name }}</p>
                        </x-slot>
                    @endif
                </x-stat-card>

                <x-stat-card title="Sesi Selesai" color="blue">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </x-slot>
                    <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ $pastSessionsCount ?? 0 }} <span class="text-xs font-medium text-slate-400 lowercase tracking-normal">Sesi</span></h2>
                    @if($thesis && $thesis->pembimbing1 && $thesis->pembimbing2)
                        <x-slot name="subtitle">
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                <span>P1: {{ Str::limit($thesis->pembimbing1->name, 50) }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $pastSessionsCountP1 }} Sesi</span>
                            </p>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                <span>P2: {{ Str::limit($thesis->pembimbing2->name, 50) }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $pastSessionsCountP2 }} Sesi</span>
                            </p>
                        </x-slot>
                    @else
                        <x-slot name="subtitle">
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 italic mt-1">Belum ada pembimbing</p>
                        </x-slot>
                    @endif
                </x-stat-card>

                <x-stat-card title="Progres Keseluruhan" :value="round($progressPercent) . '%'" color="indigo">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </x-slot>
                </x-stat-card>
            </div>

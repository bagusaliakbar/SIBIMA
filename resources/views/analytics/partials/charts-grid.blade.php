        <!-- ROW 1: SEMINAR STATUS BY ADVISOR (P1 & P2) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart 1: Seminar Status per Dosen Pembimbing 1 -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            Mahasiswa Seminar per Pembimbing 1 (P1)
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Komparasi mahasiswa sudah vs belum seminar proposal berdasarkan Dosen Pembimbing 1.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartSeminarP1', 'grafik-seminar-pembimbing-1')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartSeminarP1"></canvas>
                </div>
            </div>

            <!-- Chart 2: Seminar Status per Dosen Pembimbing 2 -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            Mahasiswa Seminar per Pembimbing 2 (P2)
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Komparasi mahasiswa sudah vs belum seminar proposal berdasarkan Dosen Pembimbing 2.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartSeminarP2', 'grafik-seminar-pembimbing-2')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartSeminarP2"></canvas>
                </div>
            </div>
        </div>

        <!-- ROW 2: COHORT PROGRESS & UNFINISHED ADVISOR BACKLOG -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart 3: Mahasiswa Sudah vs Belum Seminar per Angkatan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Status Seminar per Angkatan Mahasiswa
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Distribusi mahasiswa yang sudah dan belum melaksanakan seminar proposal tiap angkatan.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartCohortSeminar', 'grafik-seminar-per-angkatan')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartCohortSeminar"></canvas>
                </div>
            </div>

            <!-- Chart 4: Mahasiswa Belum Lulus per Dosen Pembimbing -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Mahasiswa Belum Lulus per Pembimbing
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Jumlah mahasiswa aktif yang masih dalam proses bimbingan (belum lulus) per dosen.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartUnfinishedByAdvisor', 'grafik-belum-lulus-per-pembimbing')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartUnfinishedByAdvisor"></canvas>
                </div>
            </div>
        </div>

        <!-- ROW: DISTRIBUSI TAHAPAN BIMBINGAN PER DOSEN (FULL WIDTH) -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        Grafik Distribusi Bimbingan per Dosen
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pemetaan tahapan mahasiswa yang belum lulus (Belum Seminar, Seminar, Sidang Akhir, dan Kritikal Semester ≥ 13) per Dosen Pembimbing.</p>
                </div>
                <button type="button" onclick="downloadChartAsPng('chartDistributionByAdvisor', 'grafik-distribusi-bimbingan-per-dosen')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1 self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>PNG</span>
                </button>
            </div>
            <div class="h-80 sm:h-96 relative">
                <canvas id="chartDistributionByAdvisor"></canvas>
            </div>
        </div>

        <!-- ROW 3: OVERALL STAGES & DEFENSE PROGRESS PER COHORT -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart 5: Distribusi Tahapan Skripsi Keseluruhan (Donut) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Distribusi Tahapan Skripsi
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Proporsi mahasiswa di tiap tahapan skripsi.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartStages', 'grafik-tahapan-skripsi')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-72 relative flex items-center justify-center">
                    <canvas id="chartStages"></canvas>
                </div>
            </div>

            <!-- Chart 6: Status Sidang Akhir per Angkatan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm lg:col-span-2 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            Status Sidang Akhir & Kelulusan per Angkatan
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Komparasi mahasiswa yang sudah sidang/lulus vs yang belum sidang per tahun angkatan.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartCohortDefense', 'grafik-sidang-per-angkatan')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-72 relative">
                    <canvas id="chartCohortDefense"></canvas>
                </div>
            </div>
        </div>

        <!-- ROW 4: MONTHLY MENTORING TRENDS & HEALTH MONITOR -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart 7: Tren Frekuensi Sesi Bimbingan per Bulan (Area Spline) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm lg:col-span-2 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            Tren Frekuensi Bimbingan per Bulan
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Volume aktivitas sesi bimbingan yang terlaksana selama 6 bulan terakhir.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartMonthlyTrends', 'grafik-tren-bimbingan-bulanan')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-72 relative">
                    <canvas id="chartMonthlyTrends"></canvas>
                </div>
            </div>

            <!-- Chart 8: Status Keaktifan Bimbingan (Early Warning) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Keaktifan Bimbingan (Health)
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Monitoring kontinuitas bimbingan mahasiswa aktif.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartHealth', 'grafik-keaktifan-bimbingan')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-72 relative flex items-center justify-center">
                    <canvas id="chartHealth"></canvas>
                </div>
            </div>
        </div>

        <!-- ROW 5: WORKLOAD VS QUOTA & DEFENSE GRADES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart 9: Beban Bimbingan vs Kuota Maksimal Dosen -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Beban Bimbingan Aktif vs Kuota per Dosen
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kapasitas beban bimbingan dosen aktif terhadap batas kuota institusi.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartWorkload', 'grafik-beban-kuota-dosen')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartWorkload"></canvas>
                </div>
            </div>

            <!-- Chart 10: Distribusi Nilai Kelulusan Sidang Skripsi -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Distribusi Nilai Sidang Skripsi
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Sebaran nilai mutu kelulusan sidang skripsi (Grade A, B, C, D, E).</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartScores', 'grafik-distribusi-nilai-sidang')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartScores"></canvas>
                </div>
            </div>
        </div>

        <!-- ROW 6: TOPIC DISTRIBUTION & WAVE DURATION -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart 11: Sebaran Bidang Minat / Topik Skripsi -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            Sebaran Bidang Minat & Topik Skripsi
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Klasifikasi penelitian skripsi berdasarkan bidang konsentrasi keilmuan.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartTopics', 'grafik-topik-skripsi')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-80 relative flex items-center justify-center">
                    <canvas id="chartTopics"></canvas>
                </div>
            </div>

            <!-- Chart 12: Rata-rata Durasi Penyelesaian per Gelombang -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            Rata-rata Durasi Pengerjaan per Gelombang
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Rata-rata masa pengerjaan skripsi (dalam bulan) tiap gelombang pelaksanaan.</p>
                    </div>
                    <button type="button" onclick="downloadChartAsPng('chartWaveDuration', 'grafik-durasi-gelombang')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>PNG</span>
                    </button>
                </div>
                <div class="h-80 relative">
                    <canvas id="chartWaveDuration"></canvas>
                </div>
            </div>
        </div>

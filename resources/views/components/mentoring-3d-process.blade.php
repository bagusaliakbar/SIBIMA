@props([
    'role' => 'mahasiswa',
    'progress' => null,
    'pastSessionsCount' => 0,
    'studentProgressDistribution' => [],
    'activeThesesCount' => 0,
    'sessionsThisWeek' => 0,
    'totalCompletedSessions' => 0,
    'dosenActionCenter' => [],
    'adminApprovals' => [],
    'thesisStatusCounts' => [],
    'embedded' => false,
])

@php
    $uid = 'sibima3d_' . uniqid();
    $currentStageNum = (int) ($progress['currentStage'] ?? 2);
    if ($currentStageNum < 1 || $currentStageNum > 6) {
        $currentStageNum = 2;
    }
    $activeIdx = $currentStageNum - 1;

    // Build real 6 stages data based on user role
    if ($role === 'mahasiswa' && !empty($progress['stages'])) {
        $rawStages = $progress['stages'];
        $stagesPayload = [];
        $coords = [
            ['x' => -210, 'z' => 70],
            ['x' => -126, 'z' => -45],
            ['x' => -42,  'z' => 60],
            ['x' => 42,   'z' => -45],
            ['x' => 126,  'z' => 60],
            ['x' => 210,  'z' => -35],
        ];
        foreach ($rawStages as $idx => $st) {
            $numStr = str_pad((string) ($st['number'] ?? ($idx + 1)), 2, '0', STR_PAD_LEFT);
            $badgeText = !empty($st['badge']) ? $st['badge'] : (!empty($st['is_completed']) ? 'Tuntas' : (!empty($st['is_current']) ? 'Tahap Aktif' : 'Terkunci'));
            $stagesPayload[] = [
                'id' => 'stage-' . ($idx + 1),
                'num' => $numStr,
                'short' => $st['short_name'] ?? $st['name'],
                'title' => ($st['number'] ?? ($idx + 1)) . '. ' . ($st['name'] ?? ''),
                'desc' => $st['desc'] ?? '',
                'badge' => $badgeText,
                'countLabel' => !empty($st['badge']) ? $st['badge'] : '',
                'url' => $st['url'] ?? '#',
                'action' => !empty($st['is_locked']) ? 'Tahap Terkunci' : ('Buka ' . ($st['short_name'] ?? 'Tahap')),
                'isCompleted' => !empty($st['is_completed']),
                'isCurrent' => !empty($st['is_current']),
                'isLocked' => !empty($st['is_locked']),
                'height' => !empty($st['is_current']) ? 60 : (!empty($st['is_completed']) ? 46 : 26),
                'x' => $coords[$idx]['x'] ?? 0,
                'y' => 0,
                'z' => $coords[$idx]['z'] ?? 0,
            ];
        }
    } elseif ($role === 'dosen') {
        $cJudul = (int) ($studentProgressDistribution['Judul'] ?? 0);
        $cBimbingan = (int) ($studentProgressDistribution['Bimbingan'] ?? 0);
        $cSempro = (int) ($studentProgressDistribution['ACC Seminar'] ?? 0);
        $cSidang = (int) ($studentProgressDistribution['ACC Sidang'] ?? 0);
        $cPendingMentoring = (int) ($dosenActionCenter['pending_mentoring_count'] ?? 0);
        $cNeedReview = (int) ($dosenActionCenter['need_review_count'] ?? 0);
        $cNeedInput = (int) ($dosenActionCenter['need_input_count'] ?? 0);

        $stagesPayload = [
            [
                'id' => 'stage-1',
                'num' => '01',
                'short' => 'Judul Skripsi',
                'title' => '1. Pengajuan & Verifikasi Judul',
                'desc' => $cJudul . ' mahasiswa bimbingan Anda berada pada tahap pengajuan/awal judul skripsi.',
                'badge' => $cJudul . ' Mahasiswa',
                'countLabel' => $cJudul . ' Mhs',
                'url' => route('theses.index'),
                'action' => 'Lihat Daftar Bimbingan',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'height' => max(30, min(72, 30 + $cJudul * 4)),
                'x' => -210, 'y' => 0, 'z' => 70,
            ],
            [
                'id' => 'stage-2',
                'num' => '02',
                'short' => 'Siklus Bimbingan',
                'title' => '2. Siklus Bimbingan & Logbook (GDrive / GMeet)',
                'desc' => $cBimbingan . ' mahasiswa aktif bimbingan rutin (' . $cPendingMentoring . ' permintaan sesi menunggu konfirmasi Anda, ' . $sessionsThisWeek . ' sesi minggu ini).',
                'badge' => $cPendingMentoring > 0 ? ($cPendingMentoring . ' Perlu Respon') : ($cBimbingan . ' Mhs Aktif'),
                'countLabel' => $cBimbingan . ' Mhs',
                'url' => route('mentoring-sessions.index'),
                'action' => 'Buka Jadwal & Logbook',
                'isCompleted' => false,
                'isCurrent' => true,
                'isLocked' => false,
                'height' => max(48, min(78, 44 + $cBimbingan * 3)),
                'x' => -126, 'y' => 0, 'z' => -45,
            ],
            [
                'id' => 'stage-3',
                'num' => '03',
                'short' => 'Seminar & Revisi',
                'title' => '3. Seminar Proposal & Revisi Penguji',
                'desc' => $cSempro . ' mahasiswa telah ACC Seminar Proposal. Terdapat ' . $cNeedReview . ' berkas revisi masuk yang menunggu peninjauan.',
                'badge' => $cNeedReview > 0 ? ($cNeedReview . ' Revisi Masuk') : ($cSempro . ' ACC Sempro'),
                'countLabel' => $cSempro . ' Mhs',
                'url' => route('seminar-examiner.index'),
                'action' => 'Buka Penguji Seminar',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'height' => max(32, min(70, 32 + ($cSempro + $cNeedReview) * 4)),
                'x' => -42, 'y' => 0, 'z' => 60,
            ],
            [
                'id' => 'stage-4',
                'num' => '04',
                'short' => 'Bimbingan Sidang',
                'title' => '4. Bimbingan Lanjut (Bab 4–5) & Persiapan Sidang',
                'desc' => $cSidang . ' mahasiswa bimbingan berada pada tahap penyusunan akhir dan telah disetujui menuju Sidang Skripsi.',
                'badge' => $cSidang . ' Siap Sidang',
                'countLabel' => $cSidang . ' Mhs',
                'url' => route('theses.index'),
                'action' => 'Monitoring Bimbingan',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'height' => max(34, min(72, 34 + $cSidang * 4)),
                'x' => 42, 'y' => 0, 'z' => -45,
            ],
            [
                'id' => 'stage-5',
                'num' => '05',
                'short' => 'Ujian & Revisi Sidang',
                'title' => '5. Penilaian & Revisi Pasca Sidang Skripsi',
                'desc' => $cNeedInput > 0 ? ('Terdapat ' . $cNeedInput . ' berita acara / nilai ujian yang memerlukan pengisian catatan dari Anda.') : 'Seluruh catatan dan penilaian ujian sidang mahasiswa telah terisi.',
                'badge' => $cNeedInput > 0 ? ($cNeedInput . ' Belum Diisi') : 'Tuntas',
                'countLabel' => $cNeedInput > 0 ? ($cNeedInput . ' Tugas') : 'Tuntas',
                'url' => route('defense-examiner.index'),
                'action' => 'Buka Penguji Sidang',
                'isCompleted' => $cNeedInput === 0,
                'isCurrent' => $cNeedInput > 0,
                'isLocked' => false,
                'height' => max(30, min(68, 30 + $cNeedInput * 6)),
                'x' => 126, 'y' => 0, 'z' => 60,
            ],
            [
                'id' => 'stage-6',
                'num' => '06',
                'short' => 'Yudisium & SKL',
                'title' => '6. Kelulusan Akhir & Yudisium',
                'desc' => 'Total ' . $totalCompletedSessions . ' sesi bimbingan telah Anda selesaikan bersama ' . $activeThesesCount . ' mahasiswa bimbingan aktif.',
                'badge' => $totalCompletedSessions . ' Sesi Selesai',
                'countLabel' => $activeThesesCount . ' Total',
                'url' => route('theses.index'),
                'action' => 'Lihat Rekap Skripsi',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'height' => 42,
                'x' => 210, 'y' => 0, 'z' => -35,
            ],
        ];
        $activeIdx = 1;
    } else {
        // Kaprodi / Admin
        $pTheses = (int) (($adminApprovals['pending_theses'] ?? 0) + ($adminApprovals['unassigned_theses'] ?? 0));
        $pSeminars = (int) ($adminApprovals['pending_seminars'] ?? 0);
        $pDefenses = (int) ($adminApprovals['pending_defenses'] ?? 0);
        $pGraduations = (int) ($adminApprovals['pending_graduations'] ?? 0);
        $cActive = (int) ($thesisStatusCounts['active'] ?? $activeThesesCount ?? 0);
        $cCompleted = (int) ($thesisStatusCounts['completed'] ?? 0);

        $stagesPayload = [
            [
                'id' => 'stage-1',
                'num' => '01',
                'short' => 'Usulan Judul',
                'title' => '1. Usulan Judul & Plotting Pembimbing',
                'desc' => $pTheses > 0 ? ($pTheses . ' usulan judul skripsi menunggu verifikasi atau plotting dosen pembimbing.') : 'Seluruh usulan judul baru telah diverifikasi dan di-plot.',
                'badge' => $pTheses > 0 ? ($pTheses . ' Perlu Plotting') : 'Terverifikasi',
                'countLabel' => $pTheses . ' Antrean',
                'url' => route('theses.index', ['status' => 'pending']),
                'action' => 'Kelola Usulan Judul',
                'isCompleted' => $pTheses === 0,
                'isCurrent' => $pTheses > 0,
                'isLocked' => false,
                'height' => max(34, min(72, 34 + $pTheses * 4)),
                'x' => -210, 'y' => 0, 'z' => 70,
            ],
            [
                'id' => 'stage-2',
                'num' => '02',
                'short' => 'Bimbingan Aktif',
                'title' => '2. Siklus Bimbingan Skripsi Aktif',
                'desc' => $cActive . ' mahasiswa sedang menjalani bimbingan aktif (' . $sessionsThisWeek . ' sesi minggu ini, total ' . $totalCompletedSessions . ' sesi selesai).',
                'badge' => $cActive . ' Skripsi Aktif',
                'countLabel' => $cActive . ' Aktif',
                'url' => route('theses.index', ['status' => 'active']),
                'action' => 'Monitoring Bimbingan',
                'isCompleted' => false,
                'isCurrent' => true,
                'isLocked' => false,
                'height' => 66,
                'x' => -126, 'y' => 0, 'z' => -45,
            ],
            [
                'id' => 'stage-3',
                'num' => '03',
                'short' => 'Berkas Seminar',
                'title' => '3. Verifikasi & Penjadwalan Seminar Proposal',
                'desc' => $pSeminars > 0 ? ($pSeminars . ' pendaftar seminar proposal menunggu validasi berkas dan penjadwalan ruang/penguji.') : 'Tidak ada antrean berkas pendaftaran seminar proposal.',
                'badge' => $pSeminars > 0 ? ($pSeminars . ' Pendaftar') : 'Tuntas',
                'countLabel' => $pSeminars . ' Berkas',
                'url' => route('seminar-applications.index'),
                'action' => 'Verifikasi Seminar',
                'isCompleted' => $pSeminars === 0,
                'isCurrent' => $pSeminars > 0,
                'isLocked' => false,
                'height' => max(32, min(70, 32 + $pSeminars * 5)),
                'x' => -42, 'y' => 0, 'z' => 60,
            ],
            [
                'id' => 'stage-4',
                'num' => '04',
                'short' => 'Jadwal Seminar',
                'title' => '4. Pelaksanaan & Revisi Seminar Proposal',
                'desc' => 'Kelola jadwal pelaksanaan seminar proposal, berita acara, dan pemantauan revisi penguji.',
                'badge' => 'Terjadwal',
                'countLabel' => 'Jadwal',
                'url' => route('seminar-schedules.index'),
                'action' => 'Buka Jadwal Seminar',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'height' => 44,
                'x' => 42, 'y' => 0, 'z' => -45,
            ],
            [
                'id' => 'stage-5',
                'num' => '05',
                'short' => 'Sidang Skripsi',
                'title' => '5. Verifikasi & Penjadwalan Sidang Akhir',
                'desc' => $pDefenses > 0 ? ($pDefenses . ' mahasiswa mendaftar Sidang Akhir Skripsi dan menunggu verifikasi syarat.') : 'Seluruh berkas pendaftaran sidang akhir telah diproses.',
                'badge' => $pDefenses > 0 ? ($pDefenses . ' Pendaftar') : 'Tuntas',
                'countLabel' => $pDefenses . ' Berkas',
                'url' => route('thesis-defense-applications.index'),
                'action' => 'Verifikasi Sidang',
                'isCompleted' => $pDefenses === 0,
                'isCurrent' => $pDefenses > 0,
                'isLocked' => false,
                'height' => max(34, min(70, 34 + $pDefenses * 5)),
                'x' => 126, 'y' => 0, 'z' => 60,
            ],
            [
                'id' => 'stage-6',
                'num' => '06',
                'short' => 'Yudisium & SKL',
                'title' => '6. Verifikasi Bebas Tanggungan & SKL',
                'desc' => $pGraduations > 0 ? ($pGraduations . ' pengajuan SKL menunggu approval (' . $cCompleted . ' mahasiswa telah lulus).') : ('Total ' . $cCompleted . ' mahasiswa telah menyelesaikan skripsi & yudisium.'),
                'badge' => $pGraduations > 0 ? ($pGraduations . ' Perlu Approval') : ($cCompleted . ' Lulus'),
                'countLabel' => $cCompleted . ' Lulus',
                'url' => route('graduations.index'),
                'action' => 'Kelola Yudisium & SKL',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'height' => max(38, min(72, 38 + $pGraduations * 4)),
                'x' => 210, 'y' => 0, 'z' => -35,
            ],
        ];
        $activeIdx = 1;
    }
@endphp

<div id="{{ $uid }}_wrap" class="{{ $embedded ? '' : 'bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs p-4 sm:p-5 mb-6' }}"
     x-data="{ show3D: localStorage.getItem('sibima_hide_3d_{{ $role }}') !== 'true' }">

    @if(!$embedded)
        <!-- Standalone Header for Dosen / Kaprodi / Admin -->
        <div class="flex flex-wrap items-center justify-between gap-3 {{ !$embedded ? ' pb-3.5 mb-3.5 border-b border-slate-100 dark:border-slate-800' : '' }}">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-orange-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                            {{ $role === 'dosen' ? 'Visualisasi 3D Siklus & Progres Bimbingan Mahasiswa' : 'Peta 3D Ekosistem Bimbingan & Progres Skripsi Prodi' }}
                        </h3>
                        <span class="hidden sm:inline-flex px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800/60">
                            Interaktif 360°
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Klik pilar tahapan 3D untuk inspeksi status &amp; navigasi cepat, atau geser untuk memutar sudut pandang.
                    </p>
                </div>
            </div>

            <button type="button"
                    @click="show3D = !show3D; localStorage.setItem('sibima_hide_3d_{{ $role }}', !show3D); if(show3D) { setTimeout(() => window.dispatchEvent(new Event('resize')), 50); }"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-colors cursor-pointer">
                <svg class="w-3.5 h-3.5 text-orange-600 transition-transform duration-200" :class="show3D ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                <span x-text="show3D ? 'Sembunyikan 3D' : 'Tampilkan Peta 3D'"></span>
            </button>
        </div>
    @endif

    <div x-show="show3D" x-transition.opacity.duration.200ms class="space-y-3">
        <!-- Main 3D Viewport Container -->
        <div id="{{ $uid }}_sceneCard" class="relative rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 overflow-hidden transition-colors duration-300">

            <!-- Top Overlay HUD inside 3D Viewport -->
            <div class="absolute top-3 left-3 right-3 z-10 flex flex-wrap items-start justify-between gap-2 pointer-events-none">
                <!-- Left HUD: Context Title & Legend -->
                <div class="px-3 py-2 rounded-xl border border-slate-200/90 dark:border-slate-800/90 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md pointer-events-auto shadow-2xs">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-orange-600 text-white">
                            @if($role === 'mahasiswa')
                                Tahap {{ $currentStageNum }} Aktif
                            @elseif($role === 'dosen')
                                {{ $activeThesesCount }} Mahasiswa Aktif
                            @else
                                {{ $activeThesesCount }} Skripsi Terdaftar
                            @endif
                        </span>
                        <h4 class="text-xs font-black uppercase tracking-tight text-slate-800 dark:text-slate-100">
                            @if($role === 'mahasiswa')
                                Alur 3D Bimbingan &amp; Tugas Akhir
                            @elseif($role === 'dosen')
                                Siklus Bimbingan &amp; Antrean Penguji
                            @else
                                Distribusi Tahapan Skripsi Prodi
                            @endif
                        </h4>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-[11px] font-semibold text-slate-600 dark:text-slate-400">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span> Tahap Fokus
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Orbit Siklus Bimbingan
                        </span>
                        @if($role === 'mahasiswa')
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span> Terkunci
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Right HUD: 3D Camera Controls -->
                <div class="flex items-center gap-1 p-1 rounded-xl border border-slate-200/90 dark:border-slate-800/90 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md pointer-events-auto shadow-2xs">
                    <button type="button" id="{{ $uid }}_btnAutoRotate" class="px-2.5 py-1 rounded-lg text-[11px] font-bold flex items-center gap-1 transition-colors cursor-pointer bg-orange-600 text-white" title="Putar Otomatis">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Orbit</span>
                    </button>
                    <button type="button" id="{{ $uid }}_btnIso" class="px-2 py-1 rounded-lg text-[11px] font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Sudut Isometrik Default">
                        Isometrik
                    </button>
                    <button type="button" id="{{ $uid }}_btnTop" class="px-2 py-1 rounded-lg text-[11px] font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Tampak Atas">
                        Atas
                    </button>
                    <button type="button" id="{{ $uid }}_btnZoomIn" class="p-1 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Perbesar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"/></svg>
                    </button>
                    <button type="button" id="{{ $uid }}_btnZoomOut" class="p-1 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Perkecil">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 12H6"/></svg>
                    </button>
                </div>
            </div>

            <!-- 3D Interactive Canvas -->
            <canvas id="{{ $uid }}_canvas" class="w-full h-[270px] sm:h-[300px] block cursor-grab active:cursor-grabbing select-none" style="touch-action: none;"></canvas>

            <!-- Bottom Hint Overlay -->
            <div class="absolute bottom-2.5 left-3 right-3 flex items-center justify-between pointer-events-none text-[11px] font-medium text-slate-500 dark:text-slate-400">
                <span>Geser mouse / layar untuk memutar 360° • Klik pilar 3D untuk detail tahapan</span>
                <span class="hidden sm:inline font-mono text-[10px]">SIBIMA 3D Canvas</span>
            </div>
        </div>

        <!-- Bottom Interactive Inspector Card (Updates when clicking any 3D Node) -->
        <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all">
            <div class="flex items-start sm:items-center gap-3 min-w-0">
                <div id="{{ $uid }}_inspIcon" class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center shrink-0 font-black text-xs shadow-xs">
                    02
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h5 id="{{ $uid }}_inspTitle" class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                            Memuat Tahapan...
                        </h5>
                        <span id="{{ $uid }}_inspBadge" class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-50 text-orange-800 border border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800/60">
                            Aktif
                        </span>
                    </div>
                    <p id="{{ $uid }}_inspDesc" class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 leading-relaxed">
                        Pilih salah satu pilar 3D di atas untuk melihat rincian tahapan dan pintasan menu.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a id="{{ $uid }}_inspLink" href="#" class="w-full sm:w-auto justify-center px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span id="{{ $uid }}_inspAction">Buka Menu</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const uid = @json($uid);
    const role = @json($role);
    const stagesData = @json($stagesPayload);
    const activeStageIdx = @json($activeIdx);

    const canvas = document.getElementById(uid + '_canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    let isDarkScene = document.documentElement.classList.contains('dark');
    let selectedNodeId = stagesData[activeStageIdx] ? stagesData[activeStageIdx].id : stagesData[0].id;
    let hoveredNodeId = null;
    let autoRotate = true;
    let isVisible = true;

    // 3D Camera State
    let camYaw = -0.58;
    let camPitch = 0.56;
    let targetYaw = -0.58;
    let targetPitch = 0.56;
    let camZoom = 1.0;
    let targetZoom = 1.0;

    // Pointer State
    let isDragging = false;
    let dragStartX = 0;
    let dragStartY = 0;
    let pointerDownX = 0;
    let pointerDownY = 0;
    let projectedScreenNodes = [];

    // 4 Orbiting Sub-Nodes for Continuous Mentoring Cycle
    const mentoringCycleSteps = [
        { label: '1. Draft GDrive', angleOffset: 0 },
        { label: '2. Diskusi / GMeet', angleOffset: Math.PI * 0.5 },
        { label: '3. Revisi Dosen', angleOffset: Math.PI },
        { label: '4. ACC Logbook', angleOffset: Math.PI * 1.5 }
    ];

    const pathParticles = [
        { seg: 0, t: 0.15, speed: 0.007 },
        { seg: 1, t: 0.45, speed: 0.008 },
        { seg: 1, t: 0.85, speed: 0.008 },
        { seg: 2, t: 0.30, speed: 0.007 },
        { seg: 3, t: 0.60, speed: 0.008 },
        { seg: 4, t: 0.20, speed: 0.007 }
    ];

    // Observe Light / Dark mode changes on <html>
    const themeObserver = new MutationObserver(() => {
        isDarkScene = document.documentElement.classList.contains('dark');
    });
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

    // Pause rendering when scrolled out of viewport
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                isVisible = entry.isIntersecting;
            });
        }, { threshold: 0.05 });
        io.observe(canvas);
    }

    function updateInspector() {
        const idx = stagesData.findIndex(s => s.id === selectedNodeId);
        const stage = stagesData[idx >= 0 ? idx : activeStageIdx] || stagesData[0];

        const iconBox = document.getElementById(uid + '_inspIcon');
        const titleEl = document.getElementById(uid + '_inspTitle');
        const badgeEl = document.getElementById(uid + '_inspBadge');
        const descEl = document.getElementById(uid + '_inspDesc');
        const linkEl = document.getElementById(uid + '_inspLink');
        const actionEl = document.getElementById(uid + '_inspAction');

        if (!iconBox || !titleEl) return;

        titleEl.textContent = stage.title;
        descEl.textContent = stage.desc;
        badgeEl.textContent = stage.badge;
        actionEl.textContent = stage.action;
        linkEl.setAttribute('href', stage.url || '#');

        if (stage.isCurrent) {
            iconBox.className = 'w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center shrink-0 font-black text-xs shadow-xs';
            iconBox.textContent = stage.num;
            badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60';
        } else if (stage.isCompleted) {
            iconBox.className = 'w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 font-black text-xs shadow-xs';
            iconBox.textContent = '✓';
            badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60';
        } else {
            iconBox.className = 'w-10 h-10 rounded-xl bg-slate-500 text-white flex items-center justify-center shrink-0 font-black text-xs shadow-xs';
            iconBox.textContent = stage.num;
            badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700';
        }

        if (stage.isLocked) {
            linkEl.className = 'w-full sm:w-auto justify-center px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-bold transition-all flex items-center gap-1.5 cursor-not-allowed pointer-events-none';
        } else {
            linkEl.className = 'w-full sm:w-auto justify-center px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer';
        }
    }

    // Controls Binding
    const btnAutoRotate = document.getElementById(uid + '_btnAutoRotate');
    const btnIso = document.getElementById(uid + '_btnIso');
    const btnTop = document.getElementById(uid + '_btnTop');
    const btnZoomIn = document.getElementById(uid + '_btnZoomIn');
    const btnZoomOut = document.getElementById(uid + '_btnZoomOut');

    function syncAutoRotateBtn() {
        if (!btnAutoRotate) return;
        if (autoRotate) {
            btnAutoRotate.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold flex items-center gap-1 transition-colors cursor-pointer bg-orange-600 text-white';
        } else {
            btnAutoRotate.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold flex items-center gap-1 transition-colors cursor-pointer text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800';
        }
    }

    if (btnAutoRotate) {
        btnAutoRotate.addEventListener('click', () => {
            autoRotate = !autoRotate;
            syncAutoRotateBtn();
        });
    }
    if (btnIso) {
        btnIso.addEventListener('click', () => {
            autoRotate = false;
            syncAutoRotateBtn();
            targetYaw = -0.58;
            targetPitch = 0.56;
            targetZoom = 1.0;
        });
    }
    if (btnTop) {
        btnTop.addEventListener('click', () => {
            autoRotate = false;
            syncAutoRotateBtn();
            targetYaw = 0.0;
            targetPitch = 1.22;
            targetZoom = 0.94;
        });
    }
    if (btnZoomIn) {
        btnZoomIn.addEventListener('click', () => {
            targetZoom = Math.min(1.45, targetZoom + 0.14);
        });
    }
    if (btnZoomOut) {
        btnZoomOut.addEventListener('click', () => {
            targetZoom = Math.max(0.68, targetZoom - 0.14);
        });
    }

    function project3D(x, y, z, width, height) {
        const cosY = Math.cos(camYaw);
        const sinY = Math.sin(camYaw);
        const rx = x * cosY - z * sinY;
        const rz1 = x * sinY + z * cosY;

        const cosP = Math.cos(camPitch);
        const sinP = Math.sin(camPitch);
        const ry = y * cosP - rz1 * sinP;
        const rz = y * sinP + rz1 * cosP;

        const responsiveScale = width < 520 ? (width / 520) : 1.0;
        const fov = 550 * camZoom * responsiveScale;
        const viewerDist = 620;
        const scale = fov / Math.max(120, viewerDist + rz);

        return {
            sx: width / 2 + rx * scale,
            sy: height / 2 + 24 - ry * scale,
            depth: rz,
            scale: scale
        };
    }

    function draw3DBox(cx, cy, cz, w, h, d, colors, width, height) {
        const hw = w / 2;
        const hd = d / 2;
        const verts = [
            { x: cx - hw, y: cy,     z: cz - hd },
            { x: cx + hw, y: cy,     z: cz - hd },
            { x: cx + hw, y: cy,     z: cz + hd },
            { x: cx - hw, y: cy,     z: cz + hd },
            { x: cx - hw, y: cy + h, z: cz - hd },
            { x: cx + hw, y: cy + h, z: cz - hd },
            { x: cx + hw, y: cy + h, z: cz + hd },
            { x: cx - hw, y: cy + h, z: cz + hd }
        ];

        const p = verts.map(v => project3D(v.x, v.y, v.z, width, height));

        const faces = [
            { idx: [0, 1, 5, 4], color: colors.sideDark },
            { idx: [2, 3, 7, 6], color: colors.front },
            { idx: [3, 0, 4, 7], color: colors.sideDark },
            { idx: [1, 2, 6, 5], color: colors.sideLight },
            { idx: [4, 5, 6, 7], color: colors.top }
        ];

        faces.forEach(f => {
            f.avgDepth = f.idx.reduce((acc, i) => acc + p[i].depth, 0) / 4;
        });
        faces.sort((a, b) => b.avgDepth - a.avgDepth);

        faces.forEach(f => {
            ctx.beginPath();
            ctx.moveTo(p[f.idx[0]].sx, p[f.idx[0]].sy);
            for (let i = 1; i < f.idx.length; i++) {
                ctx.lineTo(p[f.idx[i]].sx, p[f.idx[i]].sy);
            }
            ctx.closePath();
            ctx.fillStyle = f.color;
            ctx.fill();
            ctx.strokeStyle = colors.stroke;
            ctx.lineWidth = 1;
            ctx.stroke();
        });

        return project3D(cx, cy + h, cz, width, height);
    }

    let animTime = 0;
    function render() {
        requestAnimationFrame(render);
        if (!isVisible || canvas.offsetParent === null) return;

        const rect = canvas.getBoundingClientRect();
        if (rect.width < 10 || rect.height < 10) return;

        animTime += 0.024;

        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        if (canvas.width !== Math.floor(rect.width * dpr) || canvas.height !== Math.floor(rect.height * dpr)) {
            canvas.width = Math.floor(rect.width * dpr);
            canvas.height = Math.floor(rect.height * dpr);
        }

        ctx.save();
        ctx.scale(dpr, dpr);

        const W = rect.width;
        const H = rect.height;

        if (autoRotate && !isDragging) {
            targetYaw += 0.0032;
        }
        camYaw += (targetYaw - camYaw) * 0.12;
        camPitch += (targetPitch - camPitch) * 0.12;
        camZoom += (targetZoom - camZoom) * 0.12;

        // Background Surface
        ctx.fillStyle = isDarkScene ? '#090d16' : '#f8fafc';
        ctx.fillRect(0, 0, W, H);

        // 1. Perspective 3D Floor Grid
        const gridStep = 45;
        const gridRange = 270;
        ctx.lineWidth = 1;
        ctx.strokeStyle = isDarkScene ? 'rgba(51, 65, 85, 0.35)' : 'rgba(203, 213, 225, 0.65)';

        for (let gx = -gridRange; gx <= gridRange; gx += gridStep) {
            const p1 = project3D(gx, -18, -150, W, H);
            const p2 = project3D(gx, -18, 150, W, H);
            ctx.beginPath();
            ctx.moveTo(p1.sx, p1.sy);
            ctx.lineTo(p2.sx, p2.sy);
            ctx.stroke();
        }
        for (let gz = -150; gz <= 150; gz += gridStep) {
            const p1 = project3D(-gridRange, -18, gz, W, H);
            const p2 = project3D(gridRange, -18, gz, W, H);
            ctx.beginPath();
            ctx.moveTo(p1.sx, p1.sy);
            ctx.lineTo(p2.sx, p2.sy);
            ctx.stroke();
        }

        // 2. 3D Connecting Pipelines between Stages
        for (let i = 0; i < stagesData.length - 1; i++) {
            const sA = stagesData[i];
            const sB = stagesData[i + 1];
            const pA = project3D(sA.x, 2, sA.z, W, H);
            const pB = project3D(sB.x, 2, sB.z, W, H);

            ctx.beginPath();
            ctx.moveTo(pA.sx, pA.sy);
            ctx.lineTo(pB.sx, pB.sy);
            ctx.lineWidth = 5 * ((pA.scale + pB.scale) * 0.5);
            ctx.lineCap = 'round';
            if (sA.isCompleted && (sB.isCompleted || sB.isCurrent)) {
                ctx.strokeStyle = isDarkScene ? 'rgba(16, 185, 129, 0.55)' : 'rgba(5, 150, 105, 0.55)';
            } else if (sA.isCurrent) {
                ctx.strokeStyle = 'rgba(234, 88, 12, 0.55)';
            } else {
                ctx.strokeStyle = isDarkScene ? 'rgba(71, 85, 105, 0.45)' : 'rgba(203, 213, 225, 0.85)';
            }
            ctx.stroke();
        }

        // 3. Animated Flow Particles
        pathParticles.forEach(pt => {
            pt.t += pt.speed;
            if (pt.t > 1) pt.t -= 1;

            if (pt.seg <= activeStageIdx || role !== 'mahasiswa') {
                const sA = stagesData[pt.seg];
                const sB = stagesData[pt.seg + 1];
                if (!sA || !sB) return;
                const px = sA.x + (sB.x - sA.x) * pt.t;
                const pz = sA.z + (sB.z - sA.z) * pt.t;
                const proj = project3D(px, 5, pz, W, H);

                ctx.beginPath();
                ctx.arc(proj.sx, proj.sy, Math.max(2.5, 4.2 * proj.scale), 0, Math.PI * 2);
                ctx.fillStyle = pt.seg < activeStageIdx ? '#10b981' : '#ea580c';
                ctx.fill();
            }
        });

        // 4. 3D Mentoring Cycle Orbit Ring around Stage 2 (or current active mentoring stage)
        const orbitCenterStage = stagesData[activeStageIdx] || stagesData[1];
        const orbitRadius = 54;
        const orbitSegments = 36;
        ctx.beginPath();
        for (let k = 0; k <= orbitSegments; k++) {
            const ang = (k / orbitSegments) * Math.PI * 2;
            const ox = orbitCenterStage.x + Math.cos(ang) * orbitRadius;
            const oz = orbitCenterStage.z + Math.sin(ang) * orbitRadius;
            const op = project3D(ox, 4, oz, W, H);
            if (k === 0) ctx.moveTo(op.sx, op.sy);
            else ctx.lineTo(op.sx, op.sy);
        }
        ctx.strokeStyle = isDarkScene ? 'rgba(59, 130, 246, 0.48)' : 'rgba(37, 99, 235, 0.42)';
        ctx.lineWidth = 1.75;
        ctx.setLineDash([4, 4]);
        ctx.stroke();
        ctx.setLineDash([]);

        // Orbiting Mentoring Satellites
        mentoringCycleSteps.forEach((step) => {
            const ang = animTime * 0.65 + step.angleOffset;
            const sx = orbitCenterStage.x + Math.cos(ang) * orbitRadius;
            const sz = orbitCenterStage.z + Math.sin(ang) * orbitRadius;
            const sp = project3D(sx, 8, sz, W, H);

            ctx.beginPath();
            ctx.arc(sp.sx, sp.sy, Math.max(3, 4.5 * sp.scale), 0, Math.PI * 2);
            ctx.fillStyle = '#3b82f6';
            ctx.fill();
            ctx.lineWidth = 1.5;
            ctx.strokeStyle = isDarkScene ? '#090d16' : '#ffffff';
            ctx.stroke();

            if (sp.depth < 12) {
                ctx.font = 'bold 9px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillStyle = isDarkScene ? '#93c5fd' : '#1d4ed8';
                ctx.fillText(step.label, sp.sx, sp.sy - 8);
            }
        });

        // 5. Depth-Sort and Draw the 6 Main 3D Stage Pillars
        projectedScreenNodes = [];
        const sortedStages = stagesData.map((st, idx) => {
            const centerProj = project3D(st.x, 0, st.z, W, H);
            return { st, idx, depth: centerProj.depth };
        }).sort((a, b) => b.depth - a.depth);

        sortedStages.forEach(({ st }) => {
            const isSelected = st.id === selectedNodeId;
            const isHovered = st.id === hoveredNodeId;

            const baseHeight = st.height || 40;
            const pillarHeight = st.isCurrent ? (baseHeight + Math.sin(animTime * 2.2) * 3.5) : baseHeight;

            let colors;
            if (st.isCurrent) {
                colors = {
                    top: '#fb923c',
                    front: '#ea580c',
                    sideLight: '#f97316',
                    sideDark: '#c2410c',
                    stroke: isSelected || isHovered ? '#ffffff' : 'rgba(255,255,255,0.28)'
                };
            } else if (st.isCompleted) {
                colors = {
                    top: '#34d399',
                    front: '#059669',
                    sideLight: '#10b981',
                    sideDark: '#047857',
                    stroke: isSelected || isHovered ? '#ffffff' : 'rgba(255,255,255,0.22)'
                };
            } else {
                colors = isDarkScene ? {
                    top: '#475569',
                    front: '#334155',
                    sideLight: '#3b4f6b',
                    sideDark: '#1e293b',
                    stroke: isSelected || isHovered ? '#94a3b8' : 'rgba(148,163,184,0.2)'
                } : {
                    top: '#e2e8f0',
                    front: '#cbd5e1',
                    sideLight: '#d8e0ea',
                    sideDark: '#94a3b8',
                    stroke: isSelected || isHovered ? '#475569' : 'rgba(100,116,139,0.25)'
                };
            }

            const baseColors = isDarkScene ? {
                top: '#1e293b', front: '#0f172a', sideLight: '#1e293b', sideDark: '#090d16',
                stroke: isSelected ? '#f97316' : 'rgba(71,85,105,0.5)'
            } : {
                top: '#ffffff', front: '#e2e8f0', sideLight: '#f1f5f9', sideDark: '#cbd5e1',
                stroke: isSelected ? '#ea580c' : 'rgba(148,163,184,0.45)'
            };

            draw3DBox(st.x, -18, st.z, 54, 8, 54, baseColors, W, H);
            const topPos = draw3DBox(st.x, -10, st.z, 36, pillarHeight, 36, colors, W, H);

            projectedScreenNodes.push({
                id: st.id,
                sx: topPos.sx,
                sy: topPos.sy + 14,
                r: Math.max(24, 34 * topPos.scale)
            });

            // Floating Label Pill Above Pillar
            const floatY = topPos.sy - 14;
            ctx.font = 'bold 10px sans-serif';
            const extraTag = (role !== 'mahasiswa' && st.countLabel) ? ` (${st.countLabel})` : '';
            const labelText = `${st.num}. ${st.short}${extraTag}`;
            const textWidth = ctx.measureText(labelText).width;
            const pillW = textWidth + 14;
            const pillH = 19;

            ctx.beginPath();
            if (ctx.roundRect) {
                ctx.roundRect(topPos.sx - pillW / 2, floatY - pillH / 2, pillW, pillH, 6);
            } else {
                ctx.rect(topPos.sx - pillW / 2, floatY - pillH / 2, pillW, pillH);
            }

            if (isSelected || st.isCurrent) {
                ctx.fillStyle = st.isCurrent ? '#ea580c' : '#0f172a';
                ctx.strokeStyle = '#fb923c';
            } else {
                ctx.fillStyle = isDarkScene ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.94)';
                ctx.strokeStyle = isDarkScene ? 'rgba(71, 85, 105, 0.85)' : 'rgba(203, 213, 225, 0.95)';
            }
            ctx.fill();
            ctx.lineWidth = isSelected ? 1.5 : 1;
            ctx.stroke();

            ctx.fillStyle = (isSelected || st.isCurrent) ? '#ffffff' : (isDarkScene ? '#e2e8f0' : '#1e293b');
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(labelText, topPos.sx, floatY);
        });

        ctx.restore();
    }

    canvas.addEventListener('pointerdown', e => {
        isDragging = true;
        dragStartX = e.clientX;
        dragStartY = e.clientY;
        pointerDownX = e.clientX;
        pointerDownY = e.clientY;
        if (canvas.setPointerCapture) {
            canvas.setPointerCapture(e.pointerId);
        }
    });

    canvas.addEventListener('pointermove', e => {
        const rect = canvas.getBoundingClientRect();
        const mx = e.clientX - rect.left;
        const my = e.clientY - rect.top;

        if (isDragging) {
            const dx = e.clientX - dragStartX;
            const dy = e.clientY - dragStartY;
            dragStartX = e.clientX;
            dragStartY = e.clientY;
            targetYaw += dx * 0.0085;
            targetPitch = Math.max(0.2, Math.min(1.35, targetPitch + dy * 0.007));
        } else {
            let found = null;
            for (let i = projectedScreenNodes.length - 1; i >= 0; i--) {
                const n = projectedScreenNodes[i];
                if (Math.hypot(mx - n.sx, my - n.sy) <= n.r) {
                    found = n.id;
                    break;
                }
            }
            hoveredNodeId = found;
            canvas.style.cursor = found ? 'pointer' : 'grab';
        }
    });

    canvas.addEventListener('pointerup', e => {
        isDragging = false;
        const moveDist = Math.hypot(e.clientX - pointerDownX, e.clientY - pointerDownY);
        if (moveDist < 6) {
            const rect = canvas.getBoundingClientRect();
            const mx = e.clientX - rect.left;
            const my = e.clientY - rect.top;
            for (let i = projectedScreenNodes.length - 1; i >= 0; i--) {
                const n = projectedScreenNodes[i];
                if (Math.hypot(mx - n.sx, my - n.sy) <= n.r) {
                    selectedNodeId = n.id;
                    updateInspector();
                    break;
                }
            }
        }
    });

    canvas.addEventListener('wheel', e => {
        e.preventDefault();
        const delta = e.deltaY < 0 ? 0.08 : -0.08;
        targetZoom = Math.max(0.68, Math.min(1.45, targetZoom + delta));
    }, { passive: false });

    updateInspector();
    requestAnimationFrame(render);
})();
</script>

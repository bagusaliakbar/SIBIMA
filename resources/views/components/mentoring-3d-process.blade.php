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
        $modelTypes = ['book', 'laptop', 'revision', 'podium', 'hardcover', 'toga'];
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
                'modelType' => $modelTypes[$idx] ?? 'book',
                'weight' => !empty($st['is_current']) ? 1.25 : (!empty($st['is_completed']) ? 1.0 : 0.8),
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
                'modelType' => 'book',
                'weight' => max(0.85, min(1.35, 0.85 + $cJudul * 0.06)),
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
                'modelType' => 'laptop',
                'weight' => max(1.15, min(1.45, 1.0 + $cBimbingan * 0.04)),
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
                'modelType' => 'revision',
                'weight' => max(0.9, min(1.35, 0.9 + ($cSempro + $cNeedReview) * 0.06)),
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
                'modelType' => 'podium',
                'weight' => max(0.9, min(1.35, 0.9 + $cSidang * 0.06)),
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
                'modelType' => 'hardcover',
                'weight' => max(0.9, min(1.35, 0.9 + $cNeedInput * 0.08)),
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
                'modelType' => 'toga',
                'weight' => 1.05,
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
                'modelType' => 'book',
                'weight' => max(0.9, min(1.35, 0.9 + $pTheses * 0.05)),
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
                'modelType' => 'laptop',
                'weight' => 1.3,
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
                'modelType' => 'revision',
                'weight' => max(0.9, min(1.35, 0.9 + $pSeminars * 0.06)),
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
                'modelType' => 'podium',
                'weight' => 1.0,
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
                'modelType' => 'hardcover',
                'weight' => max(0.9, min(1.35, 0.9 + $pDefenses * 0.06)),
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
                'modelType' => 'toga',
                'weight' => max(1.0, min(1.35, 1.0 + $pGraduations * 0.05)),
            ],
        ];
        $activeIdx = 1;
    }
@endphp

<div id="{{ $uid }}_wrap" class="{{ $embedded ? '' : 'bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs p-4 sm:p-5 mb-6' }}"
     x-data="{ show3D: localStorage.getItem('sibima_hide_3d_{{ $role }}') !== 'true' }">

    @if(!$embedded)
        <!-- Standalone Header for Dosen / Kaprodi / Admin -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3.5 mb-3.5 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-orange-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                            {{ $role === 'dosen' ? 'Studio 3D Siklus & Progres Bimbingan Mahasiswa' : 'Peta 3D Ekosistem Bimbingan & Progres Skripsi Prodi' }}
                        </h3>
                        <span class="hidden sm:inline-flex px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800/60">
                            3 Model 3D Interaktif
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Pilih model tampilan 3D (Carousel Fokus, Diorama Kampus, atau Monumen Tangga) dan klik objek 3D untuk inspeksi detail.
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
        <!-- Main 3D Viewport Container (Larger & Full Presence) -->
        <div id="{{ $uid }}_sceneCard" class="relative rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 overflow-hidden transition-colors duration-300">

            <!-- Top Overlay HUD inside 3D Viewport -->
            <div class="absolute top-3 left-3 right-3 z-10 flex flex-wrap items-start justify-between gap-2 pointer-events-none">
                <!-- Left HUD: 3D Model Selector (3 Distinct Large Models) -->
                <div class="p-1.5 rounded-xl border border-slate-200/90 dark:border-slate-800/90 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md pointer-events-auto shadow-xs flex flex-wrap items-center gap-1">
                    <span class="px-2 py-1 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden sm:inline">
                        Model 3D:
                    </span>
                    <button type="button" data-model="carousel" class="{{ $uid }}_modelBtn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer bg-orange-600 text-white">
                        1. Carousel Fokus
                    </button>
                    <button type="button" data-model="diorama" class="{{ $uid }}_modelBtn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        2. Diorama Kampus
                    </button>
                    <button type="button" data-model="monument" class="{{ $uid }}_modelBtn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        3. Tangga Puncak
                    </button>
                </div>

                <!-- Right HUD: 3D Camera Controls -->
                <div class="flex items-center gap-1 p-1 rounded-xl border border-slate-200/90 dark:border-slate-800/90 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md pointer-events-auto shadow-xs">
                    <button type="button" id="{{ $uid }}_btnAutoRotate" class="px-2.5 py-1 rounded-lg text-[11px] font-bold flex items-center gap-1 transition-colors cursor-pointer bg-orange-600 text-white" title="Putar Otomatis">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Putar</span>
                    </button>
                    <button type="button" id="{{ $uid }}_btnResetCam" class="px-2 py-1 rounded-lg text-[11px] font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Reset Sudut Kamera">
                        Reset
                    </button>
                    <button type="button" id="{{ $uid }}_btnZoomIn" class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Perbesar Objek 3D">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"/></svg>
                    </button>
                    <button type="button" id="{{ $uid }}_btnZoomOut" class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Perkecil Objek 3D">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 12H6"/></svg>
                    </button>
                </div>
            </div>

            <!-- Large 3D Interactive Canvas (390px - 440px tall) -->
            <canvas id="{{ $uid }}_canvas" class="w-full h-[380px] sm:h-[430px] block cursor-grab active:cursor-grabbing select-none" style="touch-action: none;"></canvas>

            <!-- Bottom Legend & Quick Stage Jump Pills -->
            <div class="absolute bottom-3 left-3 right-3 flex flex-wrap items-center justify-between gap-2 pointer-events-none">
                <div class="px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-white/85 dark:bg-slate-900/85 backdrop-blur-md flex flex-wrap items-center gap-3 text-[11px] font-bold text-slate-600 dark:text-slate-300 pointer-events-auto">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Selesai
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></span> Tahap Fokus
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Siklus Bimbingan
                    </span>
                    @if($role === 'mahasiswa')
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span> Terkunci
                        </span>
                    @endif
                </div>

                <!-- Quick Stage Selector Buttons (1 - 6) -->
                <div class="p-1 rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-white/85 dark:bg-slate-900/85 backdrop-blur-md flex items-center gap-1 pointer-events-auto">
                    <span class="text-[10px] font-bold text-slate-400 px-1.5 hidden md:inline">Pilih Tahap:</span>
                    @foreach($stagesPayload as $idx => $stItem)
                        <button type="button"
                                data-stage-id="{{ $stItem['id'] }}"
                                class="{{ $uid }}_stageJumpBtn w-7 h-7 rounded-lg text-[11px] font-black transition-all cursor-pointer flex items-center justify-center"
                                title="{{ $stItem['title'] }}">
                            {{ $idx + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Bottom Interactive Inspector Card (Updates when clicking any 3D Node) -->
        <div class="p-4 rounded-2xl bg-slate-50/90 dark:bg-slate-800/60 border border-slate-200/90 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all">
            <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                <div id="{{ $uid }}_inspIcon" class="w-11 h-11 rounded-xl bg-orange-600 text-white flex items-center justify-center shrink-0 font-black text-sm shadow-xs">
                    02
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h5 id="{{ $uid }}_inspTitle" class="text-sm font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                            Memuat Tahapan...
                        </h5>
                        <span id="{{ $uid }}_inspBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-50 text-orange-800 border border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800/60">
                            Aktif
                        </span>
                    </div>
                    <p id="{{ $uid }}_inspDesc" class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                        Pilih salah satu objek 3D di atas untuk melihat rincian tahapan dan pintasan menu.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a id="{{ $uid }}_inspLink" href="#" class="w-full sm:w-auto justify-center px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span id="{{ $uid }}_inspAction">Buka Menu</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
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
    let currentModel = localStorage.getItem('sibima_3d_model_style') || 'carousel';
    let selectedNodeId = stagesData[activeStageIdx] ? stagesData[activeStageIdx].id : stagesData[0].id;
    let hoveredNodeId = null;
    let autoRotate = true;
    let isVisible = true;

    // 3D Camera State (Close-up, High Scale)
    let camYaw = -0.52;
    let camPitch = 0.52;
    let targetYaw = -0.52;
    let targetPitch = 0.52;
    let camZoom = 1.18;
    let targetZoom = 1.18;

    // Pointer State
    let isDragging = false;
    let dragStartX = 0;
    let dragStartY = 0;
    let pointerDownX = 0;
    let pointerDownY = 0;
    let projectedScreenNodes = [];

    // 4 Orbiting 3D Sub-Models for Continuous Mentoring Cycle
    const mentoringCycleSteps = [
        { label: '1. Upload Draft (GDrive)', color: '#3b82f6', angleOffset: 0 },
        { label: '2. Diskusi / Google Meet', color: '#0ea5e9', angleOffset: Math.PI * 0.5 },
        { label: '3. Catatan Revisi Dosen', color: '#f59e0b', angleOffset: Math.PI },
        { label: '4. ACC Logbook Bimbingan', color: '#10b981', angleOffset: Math.PI * 1.5 }
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

    function computeStageCoordinates() {
        const selIdx = Math.max(0, stagesData.findIndex(s => s.id === selectedNodeId));
        stagesData.forEach((st, idx) => {
            if (currentModel === 'carousel') {
                // Model 1: Giant Focused Center + Orbital Carousel Ring
                if (idx === selIdx) {
                    st.tx = 0;
                    st.ty = 12;
                    st.tz = 0;
                    st.tScale = 1.55;
                } else {
                    const ringOrder = idx < selIdx ? idx : idx - 1;
                    const angle = (ringOrder / 5) * Math.PI * 2 + Math.PI * 0.5;
                    st.tx = Math.cos(angle) * 165;
                    st.ty = -6;
                    st.tz = Math.sin(angle) * 145;
                    st.tScale = 0.95;
                }
            } else if (currentModel === 'diorama') {
                // Model 2: 2-Row Large Isometric Campus Grid (3x2 S-Curve)
                const positions = [
                    { x: -135, y: 0, z: -68 },
                    { x: 0,    y: 0, z: -68 },
                    { x: 135,  y: 0, z: -68 },
                    { x: 135,  y: 0, z: 68 },
                    { x: 0,    y: 0, z: 68 },
                    { x: -135, y: 0, z: 68 }
                ];
                st.tx = positions[idx].x;
                st.ty = positions[idx].y;
                st.tz = positions[idx].z;
                st.tScale = idx === selIdx ? 1.28 : 1.12;
            } else {
                // Model 3: Monumental Ascending Staircase Pyramid
                const stepX = -155 + idx * 62;
                const stepY = -28 + idx * 18;
                const stepZ = (idx % 2 === 0) ? 28 : -28;
                st.tx = stepX;
                st.ty = stepY;
                st.tz = stepZ;
                st.tScale = idx === selIdx ? 1.25 : 1.08;
            }

            if (typeof st.x === 'undefined') {
                st.x = st.tx;
                st.y = st.ty;
                st.z = st.tz;
                st.renderScale = st.tScale;
            }
        });
    }

    function syncModelButtons() {
        document.querySelectorAll('.' + uid + '_modelBtn').forEach(btn => {
            const m = btn.getAttribute('data-model');
            if (m === currentModel) {
                btn.className = uid + '_modelBtn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer bg-orange-600 text-white shadow-2xs';
            } else {
                btn.className = uid + '_modelBtn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800';
            }
        });
    }

    function syncStageJumpButtons() {
        document.querySelectorAll('.' + uid + '_stageJumpBtn').forEach((btn, idx) => {
            const st = stagesData[idx];
            const isSel = st && st.id === selectedNodeId;
            if (isSel) {
                btn.className = uid + '_stageJumpBtn w-7 h-7 rounded-lg text-[11px] font-black transition-all cursor-pointer flex items-center justify-center bg-orange-600 text-white shadow-xs scale-105';
            } else if (st && st.isCompleted) {
                btn.className = uid + '_stageJumpBtn w-7 h-7 rounded-lg text-[11px] font-black transition-all cursor-pointer flex items-center justify-center bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 hover:bg-emerald-100';
            } else {
                btn.className = uid + '_stageJumpBtn w-7 h-7 rounded-lg text-[11px] font-black transition-all cursor-pointer flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200';
            }
        });
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
            iconBox.className = 'w-11 h-11 rounded-xl bg-orange-600 text-white flex items-center justify-center shrink-0 font-black text-sm shadow-xs';
            iconBox.textContent = stage.num;
            badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60';
        } else if (stage.isCompleted) {
            iconBox.className = 'w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 font-black text-sm shadow-xs';
            iconBox.textContent = '✓';
            badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60';
        } else {
            iconBox.className = 'w-11 h-11 rounded-xl bg-slate-500 text-white flex items-center justify-center shrink-0 font-black text-sm shadow-xs';
            iconBox.textContent = stage.num;
            badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700';
        }

        if (stage.isLocked) {
            linkEl.className = 'w-full sm:w-auto justify-center px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-bold transition-all flex items-center gap-1.5 cursor-not-allowed pointer-events-none';
        } else {
            linkEl.className = 'w-full sm:w-auto justify-center px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer';
        }

        computeStageCoordinates();
        syncStageJumpButtons();
    }

    // Bind Model Switcher Buttons
    document.querySelectorAll('.' + uid + '_modelBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            currentModel = btn.getAttribute('data-model') || 'carousel';
            localStorage.setItem('sibima_3d_model_style', currentModel);
            computeStageCoordinates();
            syncModelButtons();
        });
    });

    // Bind Stage Jump Buttons
    document.querySelectorAll('.' + uid + '_stageJumpBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            selectedNodeId = btn.getAttribute('data-stage-id');
            updateInspector();
        });
    });

    // Bind Camera Controls
    const btnAutoRotate = document.getElementById(uid + '_btnAutoRotate');
    const btnResetCam = document.getElementById(uid + '_btnResetCam');
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
    if (btnResetCam) {
        btnResetCam.addEventListener('click', () => {
            targetYaw = -0.52;
            targetPitch = 0.52;
            targetZoom = 1.18;
        });
    }
    if (btnZoomIn) {
        btnZoomIn.addEventListener('click', () => {
            targetZoom = Math.min(1.75, targetZoom + 0.16);
        });
    }
    if (btnZoomOut) {
        btnZoomOut.addEventListener('click', () => {
            targetZoom = Math.max(0.75, targetZoom - 0.16);
        });
    }

    // 3D Projection Math (Larger FOV & Closer Camera for Big Objects)
    function project3D(x, y, z, width, height) {
        const cosY = Math.cos(camYaw);
        const sinY = Math.sin(camYaw);
        const rx = x * cosY - z * sinY;
        const rz1 = x * sinY + z * cosY;

        const cosP = Math.cos(camPitch);
        const sinP = Math.sin(camPitch);
        const ry = y * cosP - rz1 * sinP;
        const rz = y * sinP + rz1 * cosP;

        const responsiveScale = width < 560 ? Math.max(0.72, width / 540) : 1.08;
        const fov = 720 * camZoom * responsiveScale;
        const viewerDist = 520;
        const scale = fov / Math.max(140, viewerDist + rz);

        return {
            sx: width / 2 + rx * scale,
            sy: height / 2 + 18 - ry * scale,
            depth: rz,
            scale: scale
        };
    }

    // Draw a 3D Extruded Box
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
            ctx.strokeStyle = colors.stroke || 'rgba(255,255,255,0.22)';
            ctx.lineWidth = 1;
            ctx.stroke();
        });

        return project3D(cx, cy + h, cz, width, height);
    }

    // Draw Sculpted 3D Stage Figurines on top of Pedestals
    function drawStageSculpture(type, cx, baseY, cz, sc, palette, W, H, animTime) {
        const whitePages = {
            top: '#ffffff', front: '#e2e8f0', sideLight: '#f8fafc', sideDark: '#cbd5e1', stroke: 'rgba(148,163,184,0.4)'
        };
        const darkScreen = {
            top: '#38bdf8', front: '#0284c7', sideLight: '#0ea5e9', sideDark: '#0369a1', stroke: 'rgba(255,255,255,0.35)'
        };
        const goldAccent = {
            top: '#fde047', front: '#eab308', sideLight: '#facc15', sideDark: '#ca8a04', stroke: 'rgba(255,255,255,0.4)'
        };

        if (type === 'book') {
            // 1. Stacked 3D Thesis Proposal Books
            draw3DBox(cx, baseY, cz, 38 * sc, 9 * sc, 30 * sc, palette, W, H);
            draw3DBox(cx + 2 * sc, baseY + 9 * sc, cz, 34 * sc, 7 * sc, 26 * sc, whitePages, W, H);
            return draw3DBox(cx - 1 * sc, baseY + 16 * sc, cz, 32 * sc, 8 * sc, 24 * sc, palette, W, H);
        } else if (type === 'laptop') {
            // 2. 3D Mentoring Laptop / Google Meet Station
            draw3DBox(cx, baseY, cz + 4 * sc, 42 * sc, 5 * sc, 32 * sc, palette, W, H);
            // Laptop vertical screen at back
            draw3DBox(cx, baseY + 5 * sc, cz - 10 * sc, 40 * sc, 26 * sc, 5 * sc, palette, W, H);
            return draw3DBox(cx, baseY + 8 * sc, cz - 7 * sc, 34 * sc, 20 * sc, 3 * sc, darkScreen, W, H);
        } else if (type === 'revision') {
            // 3. Twin 3D Revision Towers with Verification Bridge
            draw3DBox(cx - 11 * sc, baseY, cz, 15 * sc, 28 * sc, 18 * sc, palette, W, H);
            draw3DBox(cx + 11 * sc, baseY, cz, 15 * sc, 28 * sc, 18 * sc, palette, W, H);
            return draw3DBox(cx, baseY + 20 * sc, cz, 40 * sc, 8 * sc, 22 * sc, goldAccent, W, H);
        } else if (type === 'podium') {
            // 4. 3D Thesis Defense Podium & Lectern
            draw3DBox(cx, baseY, cz, 38 * sc, 8 * sc, 38 * sc, whitePages, W, H);
            draw3DBox(cx, baseY + 8 * sc, cz, 24 * sc, 26 * sc, 22 * sc, palette, W, H);
            return draw3DBox(cx, baseY + 34 * sc, cz, 32 * sc, 7 * sc, 26 * sc, goldAccent, W, H);
        } else if (type === 'hardcover') {
            // 5. Standing 3D Hardcover Thesis Monument with Gold Spine
            draw3DBox(cx, baseY, cz, 34 * sc, 34 * sc, 14 * sc, palette, W, H);
            return draw3DBox(cx, baseY + 6 * sc, cz + 6 * sc, 24 * sc, 22 * sc, 4 * sc, goldAccent, W, H);
        } else {
            // 6. 3D Graduation Mortarboard Cap (Toga Wisuda 3D)
            const floatBob = Math.sin(animTime * 2.5) * 3 * sc;
            // Skullcap base
            draw3DBox(cx, baseY + floatBob, cz, 26 * sc, 14 * sc, 26 * sc, palette, W, H);
            // Wide Mortarboard Top Square
            const topCap = draw3DBox(cx, baseY + 14 * sc + floatBob, cz, 48 * sc, 5 * sc, 48 * sc, palette, W, H);
            // Golden Button & Hanging Tassel
            draw3DBox(cx, baseY + 19 * sc + floatBob, cz, 8 * sc, 3 * sc, 8 * sc, goldAccent, W, H);
            draw3DBox(cx + 18 * sc, baseY + 6 * sc + floatBob, cz + 18 * sc, 5 * sc, 14 * sc, 5 * sc, goldAccent, W, H);
            return topCap;
        }
    }

    let animTime = 0;
    function render() {
        requestAnimationFrame(render);
        if (!isVisible || canvas.offsetParent === null) return;

        const rect = canvas.getBoundingClientRect();
        if (rect.width < 10 || rect.height < 10) return;

        animTime += 0.022;

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
            targetYaw += 0.0028;
        }
        camYaw += (targetYaw - camYaw) * 0.12;
        camPitch += (targetPitch - camPitch) * 0.12;
        camZoom += (targetZoom - camZoom) * 0.12;

        // Smoothly interpolate stage positions when switching models or clicking nodes
        stagesData.forEach(st => {
            st.x += (st.tx - st.x) * 0.12;
            st.y += (st.ty - st.y) * 0.12;
            st.z += (st.tz - st.z) * 0.12;
            st.renderScale += (st.tScale - st.renderScale) * 0.12;
        });

        // Clear Background
        ctx.fillStyle = isDarkScene ? '#090d16' : '#f8fafc';
        ctx.fillRect(0, 0, W, H);

        // 1. Perspective 3D Floor Grid
        const gridStep = 45;
        const gridRange = 225;
        ctx.lineWidth = 1;
        ctx.strokeStyle = isDarkScene ? 'rgba(51, 65, 85, 0.38)' : 'rgba(203, 213, 225, 0.7)';

        for (let gx = -gridRange; gx <= gridRange; gx += gridStep) {
            const p1 = project3D(gx, -32, -gridRange, W, H);
            const p2 = project3D(gx, -32, gridRange, W, H);
            ctx.beginPath();
            ctx.moveTo(p1.sx, p1.sy);
            ctx.lineTo(p2.sx, p2.sy);
            ctx.stroke();
        }
        for (let gz = -gridRange; gz <= gridRange; gz += gridStep) {
            const p1 = project3D(-gridRange, -32, gz, W, H);
            const p2 = project3D(gridRange, -32, gz, W, H);
            ctx.beginPath();
            ctx.moveTo(p1.sx, p1.sy);
            ctx.lineTo(p2.sx, p2.sy);
            ctx.stroke();
        }

        // If Carousel Model, draw Outer Orbital Ring on Floor
        if (currentModel === 'carousel') {
            ctx.beginPath();
            const segs = 48;
            for (let k = 0; k <= segs; k++) {
                const a = (k / segs) * Math.PI * 2;
                const p = project3D(Math.cos(a) * 165, -26, Math.sin(a) * 145, W, H);
                if (k === 0) ctx.moveTo(p.sx, p.sy);
                else ctx.lineTo(p.sx, p.sy);
            }
            ctx.strokeStyle = isDarkScene ? 'rgba(234, 88, 12, 0.28)' : 'rgba(234, 88, 12, 0.24)';
            ctx.lineWidth = 3;
            ctx.stroke();
        }

        // 2. 3D Connecting Pipelines between Stages
        for (let i = 0; i < stagesData.length - 1; i++) {
            const sA = stagesData[i];
            const sB = stagesData[i + 1];
            const pA = project3D(sA.x, sA.y - 12, sA.z, W, H);
            const pB = project3D(sB.x, sB.y - 12, sB.z, W, H);

            ctx.beginPath();
            ctx.moveTo(pA.sx, pA.sy);
            ctx.lineTo(pB.sx, pB.sy);
            ctx.lineWidth = 6 * ((pA.scale + pB.scale) * 0.5);
            ctx.lineCap = 'round';
            if (sA.isCompleted && (sB.isCompleted || sB.isCurrent)) {
                ctx.strokeStyle = isDarkScene ? 'rgba(16, 185, 129, 0.55)' : 'rgba(5, 150, 105, 0.55)';
            } else if (sA.isCurrent) {
                ctx.strokeStyle = 'rgba(234, 88, 12, 0.6)';
            } else {
                ctx.strokeStyle = isDarkScene ? 'rgba(71, 85, 105, 0.45)' : 'rgba(203, 213, 225, 0.85)';
            }
            ctx.stroke();
        }

        // 3. Animated Flow Spheres along Pipelines
        pathParticles.forEach(pt => {
            pt.t += pt.speed;
            if (pt.t > 1) pt.t -= 1;

            if (pt.seg <= activeStageIdx || role !== 'mahasiswa') {
                const sA = stagesData[pt.seg];
                const sB = stagesData[pt.seg + 1];
                if (!sA || !sB) return;
                const px = sA.x + (sB.x - sA.x) * pt.t;
                const py = (sA.y - 10) + ((sB.y - 10) - (sA.y - 10)) * pt.t;
                const pz = sA.z + (sB.z - sA.z) * pt.t;
                const proj = project3D(px, py, pz, W, H);

                ctx.beginPath();
                ctx.arc(proj.sx, proj.sy, Math.max(3.5, 5.5 * proj.scale), 0, Math.PI * 2);
                ctx.fillStyle = pt.seg < activeStageIdx ? '#10b981' : '#ea580c';
                ctx.fill();
            }
        });

        // 4. 3D Mentoring Cycle Orbit Ring around Focused Stage
        const focusStage = stagesData.find(s => s.id === selectedNodeId) || stagesData[activeStageIdx] || stagesData[1];
        const orbitRadius = 78 * (focusStage.renderScale || 1);
        const orbitSegments = 40;
        ctx.beginPath();
        for (let k = 0; k <= orbitSegments; k++) {
            const ang = (k / orbitSegments) * Math.PI * 2;
            const ox = focusStage.x + Math.cos(ang) * orbitRadius;
            const oz = focusStage.z + Math.sin(ang) * orbitRadius;
            const op = project3D(ox, focusStage.y + 4, oz, W, H);
            if (k === 0) ctx.moveTo(op.sx, op.sy);
            else ctx.lineTo(op.sx, op.sy);
        }
        ctx.strokeStyle = isDarkScene ? 'rgba(59, 130, 246, 0.55)' : 'rgba(37, 99, 235, 0.45)';
        ctx.lineWidth = 2;
        ctx.setLineDash([5, 5]);
        ctx.stroke();
        ctx.setLineDash([]);

        // 5. Depth-Sort and Draw the 6 Main 3D Stage Monuments + Sculptures
        projectedScreenNodes = [];
        const sortedStages = stagesData.map((st, idx) => {
            const centerProj = project3D(st.x, st.y, st.z, W, H);
            return { st, idx, depth: centerProj.depth };
        }).sort((a, b) => b.depth - a.depth);

        sortedStages.forEach(({ st }) => {
            const isSelected = st.id === selectedNodeId;
            const isHovered = st.id === hoveredNodeId;
            const sc = (st.renderScale || 1.0) * (st.weight || 1.0);

            let colors;
            if (st.isCurrent) {
                colors = {
                    top: '#fb923c',
                    front: '#ea580c',
                    sideLight: '#f97316',
                    sideDark: '#c2410c',
                    stroke: isSelected || isHovered ? '#ffffff' : 'rgba(255,255,255,0.32)'
                };
            } else if (st.isCompleted) {
                colors = {
                    top: '#34d399',
                    front: '#059669',
                    sideLight: '#10b981',
                    sideDark: '#047857',
                    stroke: isSelected || isHovered ? '#ffffff' : 'rgba(255,255,255,0.25)'
                };
            } else {
                colors = isDarkScene ? {
                    top: '#475569',
                    front: '#334155',
                    sideLight: '#3b4f6b',
                    sideDark: '#1e293b',
                    stroke: isSelected || isHovered ? '#cbd5e1' : 'rgba(148,163,184,0.25)'
                } : {
                    top: '#cbd5e1',
                    front: '#94a3b8',
                    sideLight: '#b4c2d4',
                    sideDark: '#64748b',
                    stroke: isSelected || isHovered ? '#1e293b' : 'rgba(100,116,139,0.28)'
                };
            }

            // Tiered Base Pedestal Platform (Large & Solid)
            const baseColors = isDarkScene ? {
                top: '#1e293b', front: '#0f172a', sideLight: '#334155', sideDark: '#090d16',
                stroke: isSelected ? '#f97316' : 'rgba(100,116,139,0.5)'
            } : {
                top: '#ffffff', front: '#e2e8f0', sideLight: '#f1f5f9', sideDark: '#cbd5e1',
                stroke: isSelected ? '#ea580c' : 'rgba(148,163,184,0.55)'
            };

            const pedW = 68 * (st.renderScale || 1);
            const pedH = 14 * (st.renderScale || 1);
            draw3DBox(st.x, st.y - 28, st.z, pedW, pedH, pedW, baseColors, W, H);

            // Second Pedestal Tier
            const tierW = 50 * (st.renderScale || 1);
            const tierH = 22 * sc;
            draw3DBox(st.x, st.y - 28 + pedH, st.z, tierW, tierH, tierW, colors, W, H);

            // Sculpted 3D Icon Model on Top of Pedestal
            const sculptureBaseY = st.y - 28 + pedH + tierH;
            const topPos = drawStageSculpture(st.modelType, st.x, sculptureBaseY, st.z, st.renderScale || 1, colors, W, H, animTime);

            projectedScreenNodes.push({
                id: st.id,
                sx: topPos.sx,
                sy: topPos.sy + 18,
                r: Math.max(38, 52 * topPos.scale)
            });

            // Large, High-Contrast Floating Label Pill Above 3D Sculpture
            const floatY = topPos.sy - 20;
            const isFocus = isSelected || st.isCurrent;
            ctx.font = isFocus ? '900 12px sans-serif' : 'bold 11px sans-serif';
            const extraTag = (role !== 'mahasiswa' && st.countLabel) ? ` • ${st.countLabel}` : '';
            const labelText = `${st.num}. ${st.short}${extraTag}`;
            const textWidth = ctx.measureText(labelText).width;
            const pillW = textWidth + 18;
            const pillH = isFocus ? 24 : 21;

            ctx.beginPath();
            if (ctx.roundRect) {
                ctx.roundRect(topPos.sx - pillW / 2, floatY - pillH / 2, pillW, pillH, 8);
            } else {
                ctx.rect(topPos.sx - pillW / 2, floatY - pillH / 2, pillW, pillH);
            }

            if (st.isCurrent) {
                ctx.fillStyle = '#ea580c';
                ctx.strokeStyle = '#fdba74';
            } else if (isSelected) {
                ctx.fillStyle = '#0f172a';
                ctx.strokeStyle = '#fb923c';
            } else {
                ctx.fillStyle = isDarkScene ? 'rgba(15, 23, 42, 0.92)' : 'rgba(255, 255, 255, 0.96)';
                ctx.strokeStyle = isDarkScene ? 'rgba(100, 116, 139, 0.85)' : 'rgba(148, 163, 184, 0.9)';
            }
            ctx.fill();
            ctx.lineWidth = isFocus ? 2 : 1.2;
            ctx.stroke();

            ctx.fillStyle = (st.isCurrent || isSelected) ? '#ffffff' : (isDarkScene ? '#f8fafc' : '#0f172a');
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(labelText, topPos.sx, floatY);
        });

        // 6. Draw 4 Orbiting 3D Mentoring Cycle Satellites around Focused Stage
        mentoringCycleSteps.forEach((step) => {
            const ang = animTime * 0.6 + step.angleOffset;
            const sx = focusStage.x + Math.cos(ang) * orbitRadius;
            const sz = focusStage.z + Math.sin(ang) * orbitRadius;
            const sy = focusStage.y + 8 + Math.sin(animTime * 2 + step.angleOffset) * 4;

            const satPalette = {
                top: '#60a5fa', front: '#2563eb', sideLight: '#3b82f6', sideDark: '#1d4ed8', stroke: '#ffffff'
            };
            const sp = draw3DBox(sx, sy, sz, 14, 14, 14, satPalette, W, H);

            // Show clear satellite badge for front-facing cycle nodes
            if (sp.depth < 35) {
                ctx.font = 'bold 10px sans-serif';
                const tw = ctx.measureText(step.label).width + 12;
                ctx.beginPath();
                if (ctx.roundRect) {
                    ctx.roundRect(sp.sx - tw / 2, sp.sy - 22, tw, 17, 5);
                } else {
                    ctx.rect(sp.sx - tw / 2, sp.sy - 22, tw, 17);
                }
                ctx.fillStyle = isDarkScene ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.94)';
                ctx.strokeStyle = '#3b82f6';
                ctx.lineWidth = 1;
                ctx.fill();
                ctx.stroke();

                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = isDarkScene ? '#93c5fd' : '#1d4ed8';
                ctx.fillText(step.label, sp.sx, sp.sy - 13.5);
            }
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
            targetPitch = Math.max(0.2, Math.min(1.25, targetPitch + dy * 0.007));
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
        const delta = e.deltaY < 0 ? 0.1 : -0.1;
        targetZoom = Math.max(0.75, Math.min(1.75, targetZoom + delta));
    }, { passive: false });

    syncModelButtons();
    updateInspector();
    requestAnimationFrame(render);
})();
</script>

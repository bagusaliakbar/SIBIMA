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

    $modelTypes = ['book', 'laptop', 'revision', 'podium', 'hardcover', 'toga'];

    if ($role === 'mahasiswa' && !empty($progress['stages'])) {
        $rawStages = $progress['stages'];
        $stagesPayload = [];
        foreach ($rawStages as $idx => $st) {
            $numStr = str_pad((string) ($st['number'] ?? ($idx + 1)), 2, '0', STR_PAD_LEFT);
            $badgeText = !empty($st['badge'])
                ? $st['badge']
                : (!empty($st['is_completed']) ? 'Selesai' : (!empty($st['is_current']) ? 'Sedang Berjalan' : 'Terkunci'));
            $stagesPayload[] = [
                'id' => 'stage-' . ($idx + 1),
                'num' => $numStr,
                'short' => $st['short_name'] ?? $st['name'],
                'title' => ($st['number'] ?? ($idx + 1)) . '. ' . ($st['name'] ?? ''),
                'desc' => $st['desc'] ?? '',
                'badge' => $badgeText,
                'subBadge' => !empty($st['is_completed']) ? 'Selesai' : (!empty($st['is_current']) ? 'Tahap Aktif' : 'Terkunci'),
                'url' => $st['url'] ?? '#',
                'action' => !empty($st['is_locked']) ? 'Tahap Terkunci' : ('Buka ' . ($st['short_name'] ?? 'Tahap')),
                'isCompleted' => !empty($st['is_completed']),
                'isCurrent' => !empty($st['is_current']),
                'isLocked' => !empty($st['is_locked']),
                'modelType' => $modelTypes[$idx] ?? 'book',
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
                'subBadge' => $cJudul . ' Mhs',
                'url' => route('theses.index'),
                'action' => 'Lihat Daftar Bimbingan',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'modelType' => 'book',
            ],
            [
                'id' => 'stage-2',
                'num' => '02',
                'short' => 'Siklus Bimbingan',
                'title' => '2. Siklus Bimbingan & Logbook (GDrive / GMeet)',
                'desc' => $cBimbingan . ' mahasiswa aktif bimbingan rutin (' . $cPendingMentoring . ' permintaan sesi menunggu konfirmasi Anda, ' . $sessionsThisWeek . ' sesi minggu ini).',
                'badge' => $cPendingMentoring > 0 ? ($cPendingMentoring . ' Perlu Respon') : ($cBimbingan . ' Mhs Aktif'),
                'subBadge' => $cBimbingan . ' Mhs Aktif',
                'url' => route('mentoring-sessions.index'),
                'action' => 'Buka Jadwal & Logbook',
                'isCompleted' => false,
                'isCurrent' => true,
                'isLocked' => false,
                'modelType' => 'laptop',
            ],
            [
                'id' => 'stage-3',
                'num' => '03',
                'short' => 'Seminar & Revisi',
                'title' => '3. Seminar Proposal & Revisi Penguji',
                'desc' => $cSempro . ' mahasiswa telah ACC Seminar Proposal. Terdapat ' . $cNeedReview . ' berkas revisi masuk yang menunggu peninjauan.',
                'badge' => $cNeedReview > 0 ? ($cNeedReview . ' Revisi Masuk') : ($cSempro . ' ACC Sempro'),
                'subBadge' => $cSempro . ' ACC Sempro',
                'url' => route('seminar-examiner.index'),
                'action' => 'Buka Penguji Seminar',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'modelType' => 'revision',
            ],
            [
                'id' => 'stage-4',
                'num' => '04',
                'short' => 'Bimbingan Sidang',
                'title' => '4. Bimbingan Lanjut (Bab 4–5) & Persiapan Sidang',
                'desc' => $cSidang . ' mahasiswa bimbingan berada pada tahap penyusunan akhir dan telah disetujui menuju Sidang Skripsi.',
                'badge' => $cSidang . ' Siap Sidang',
                'subBadge' => $cSidang . ' Siap Sidang',
                'url' => route('theses.index'),
                'action' => 'Monitoring Bimbingan',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'modelType' => 'podium',
            ],
            [
                'id' => 'stage-5',
                'num' => '05',
                'short' => 'Revisi Sidang',
                'title' => '5. Penilaian & Revisi Pasca Sidang Skripsi',
                'desc' => $cNeedInput > 0 ? ('Terdapat ' . $cNeedInput . ' berita acara / nilai ujian yang memerlukan pengisian catatan dari Anda.') : 'Seluruh catatan dan penilaian ujian sidang mahasiswa telah terisi.',
                'badge' => $cNeedInput > 0 ? ($cNeedInput . ' Belum Diisi') : 'Tuntas',
                'subBadge' => $cNeedInput > 0 ? ($cNeedInput . ' Belum Diisi') : 'Tuntas',
                'url' => route('defense-examiner.index'),
                'action' => 'Buka Penguji Sidang',
                'isCompleted' => $cNeedInput === 0,
                'isCurrent' => $cNeedInput > 0,
                'isLocked' => false,
                'modelType' => 'hardcover',
            ],
            [
                'id' => 'stage-6',
                'num' => '06',
                'short' => 'Yudisium & SKL',
                'title' => '6. Kelulusan Akhir & Yudisium',
                'desc' => 'Total ' . $totalCompletedSessions . ' sesi bimbingan telah Anda selesaikan bersama ' . $activeThesesCount . ' mahasiswa bimbingan aktif.',
                'badge' => $totalCompletedSessions . ' Sesi Selesai',
                'subBadge' => $totalCompletedSessions . ' Sesi',
                'url' => route('theses.index'),
                'action' => 'Lihat Rekap Skripsi',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'modelType' => 'toga',
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
                'subBadge' => $pTheses . ' Antrean',
                'url' => route('theses.index', ['status' => 'pending']),
                'action' => 'Kelola Usulan Judul',
                'isCompleted' => $pTheses === 0,
                'isCurrent' => $pTheses > 0,
                'isLocked' => false,
                'modelType' => 'book',
            ],
            [
                'id' => 'stage-2',
                'num' => '02',
                'short' => 'Bimbingan Aktif',
                'title' => '2. Siklus Bimbingan Skripsi Aktif',
                'desc' => $cActive . ' mahasiswa sedang menjalani bimbingan aktif (' . $sessionsThisWeek . ' sesi minggu ini, total ' . $totalCompletedSessions . ' sesi selesai).',
                'badge' => $cActive . ' Skripsi Aktif',
                'subBadge' => $cActive . ' Aktif',
                'url' => route('theses.index', ['status' => 'active']),
                'action' => 'Monitoring Bimbingan',
                'isCompleted' => false,
                'isCurrent' => true,
                'isLocked' => false,
                'modelType' => 'laptop',
            ],
            [
                'id' => 'stage-3',
                'num' => '03',
                'short' => 'Berkas Seminar',
                'title' => '3. Verifikasi & Penjadwalan Seminar Proposal',
                'desc' => $pSeminars > 0 ? ($pSeminars . ' pendaftar seminar proposal menunggu validasi berkas dan penjadwalan ruang/penguji.') : 'Tidak ada antrean berkas pendaftaran seminar proposal.',
                'badge' => $pSeminars > 0 ? ($pSeminars . ' Pendaftar') : 'Tuntas',
                'subBadge' => $pSeminars . ' Berkas',
                'url' => route('seminar-applications.index'),
                'action' => 'Verifikasi Seminar',
                'isCompleted' => $pSeminars === 0,
                'isCurrent' => $pSeminars > 0,
                'isLocked' => false,
                'modelType' => 'revision',
            ],
            [
                'id' => 'stage-4',
                'num' => '04',
                'short' => 'Jadwal Seminar',
                'title' => '4. Pelaksanaan & Revisi Seminar Proposal',
                'desc' => 'Kelola jadwal pelaksanaan seminar proposal, berita acara, dan pemantauan revisi penguji.',
                'badge' => 'Terjadwal',
                'subBadge' => 'Jadwal Ujian',
                'url' => route('seminar-schedules.index'),
                'action' => 'Buka Jadwal Seminar',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'modelType' => 'podium',
            ],
            [
                'id' => 'stage-5',
                'num' => '05',
                'short' => 'Sidang Skripsi',
                'title' => '5. Verifikasi & Penjadwalan Sidang Akhir',
                'desc' => $pDefenses > 0 ? ($pDefenses . ' mahasiswa mendaftar Sidang Akhir Skripsi dan menunggu verifikasi syarat.') : 'Seluruh berkas pendaftaran sidang akhir telah diproses.',
                'badge' => $pDefenses > 0 ? ($pDefenses . ' Pendaftar') : 'Tuntas',
                'subBadge' => $pDefenses . ' Berkas',
                'url' => route('thesis-defense-applications.index'),
                'action' => 'Verifikasi Sidang',
                'isCompleted' => $pDefenses === 0,
                'isCurrent' => $pDefenses > 0,
                'isLocked' => false,
                'modelType' => 'hardcover',
            ],
            [
                'id' => 'stage-6',
                'num' => '06',
                'short' => 'Yudisium & SKL',
                'title' => '6. Verifikasi Bebas Tanggungan & SKL',
                'desc' => $pGraduations > 0 ? ($pGraduations . ' pengajuan SKL menunggu approval (' . $cCompleted . ' mahasiswa telah lulus).') : ('Total ' . $cCompleted . ' mahasiswa telah menyelesaikan skripsi & yudisium.'),
                'badge' => $pGraduations > 0 ? ($pGraduations . ' Perlu Approval') : ($cCompleted . ' Lulus'),
                'subBadge' => $cCompleted . ' Lulus',
                'url' => route('graduations.index'),
                'action' => 'Kelola Yudisium & SKL',
                'isCompleted' => true,
                'isCurrent' => false,
                'isLocked' => false,
                'modelType' => 'toga',
            ],
        ];
        $activeIdx = 1;
    }
@endphp

<div id="{{ $uid }}_wrap" class="{{ $embedded ? '' : 'bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs p-4 sm:p-5 mb-6' }}"
     x-data="{ show3D: localStorage.getItem('sibima_hide_3d_{{ $role }}') !== 'true' }">

    @if(!$embedded)
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
                            {{ $role === 'dosen' ? 'Stepper 3D Siklus & Progres Bimbingan Mahasiswa' : 'Stepper 3D Alur Bimbingan & Progres Skripsi Prodi' }}
                        </h3>
                        <span class="hidden sm:inline-flex px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800/60">
                            Isometrik 3D
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Klik tahapan 3D (1 s.d. 6) untuk melihat detail status dan membuka menu terkait.
                    </p>
                </div>
            </div>

            <button type="button"
                    @click="show3D = !show3D; localStorage.setItem('sibima_hide_3d_{{ $role }}', !show3D); if(show3D) { setTimeout(() => window.dispatchEvent(new Event('resize')), 50); }"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-colors cursor-pointer">
                <svg class="w-3.5 h-3.5 text-orange-600 transition-transform duration-200" :class="show3D ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                <span x-text="show3D ? 'Sembunyikan 3D' : 'Tampilkan Stepper 3D'"></span>
            </button>
        </div>
    @endif

    <div x-show="show3D" x-transition.opacity.duration.200ms class="space-y-3">
        <!-- Stationary 3D Isometric Stepper Viewport (Horizontal Scroll on Very Small Screens) -->
        <div class="relative rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 overflow-x-auto hide-scrollbar transition-colors duration-300">
            <div class="min-w-[740px] sm:min-w-0 relative">
                <canvas id="{{ $uid }}_canvas" class="w-full h-[290px] sm:h-[320px] block select-none"></canvas>
            </div>
        </div>

        <!-- Bottom Interactive Inspector Card -->
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
                        Klik salah satu tahapan 3D di atas untuk melihat rincian dan pintasan menu.
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
    let selectedNodeId = stagesData[activeStageIdx] ? stagesData[activeStageIdx].id : stagesData[0].id;
    let hoveredNodeId = null;
    let isVisible = true;
    let hitBoxes = [];

    // Smooth elevation animation per stage when hovered/selected
    const stageLift = stagesData.map((_, i) => i === activeStageIdx ? 10 : 0);

    // Animated energy packets moving left-to-right along the stepper track
    const trackPackets = [
        { seg: 0, t: 0.2, speed: 0.008 },
        { seg: 1, t: 0.65, speed: 0.008 },
        { seg: 2, t: 0.35, speed: 0.008 },
        { seg: 3, t: 0.8, speed: 0.008 },
        { seg: 4, t: 0.1, speed: 0.008 }
    ];

    // Mentoring cycle badges floating gently around the active stage
    const cyclePills = [
        { label: 'Draft GDrive', dx: -44, dy: -66, phase: 0 },
        { label: 'Diskusi GMeet', dx: 46, dy: -66, phase: 1.6 },
        { label: 'ACC Logbook', dx: 0, dy: -92, phase: 3.2 }
    ];

    const themeObserver = new MutationObserver(() => {
        isDarkScene = document.documentElement.classList.contains('dark');
    });
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

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
    }

    // Fixed True Isometric Local Projection around an Anchor (ax, ay)
    // Never rotates the layout; preserves left-to-right stepper order while rendering crisp 3D blocks
    function isoPoint(ax, ay, lx, ly, lz) {
        const cos30 = 0.866025;
        const sin30 = 0.5;
        return {
            x: ax + (lx - lz) * cos30,
            y: ay + (lx + lz) * sin30 - ly
        };
    }

    // Draw a Stationary Isometric 3D Box at Anchor (ax, ay) with local center (cx, cy, cz) and size (w, h, d)
    function drawIsoBox(ax, ay, cx, cy, cz, w, h, d, colors) {
        const hw = w / 2;
        const hd = d / 2;

        // Top face (y = cy + h)
        const t0 = isoPoint(ax, ay, cx - hw, cy + h, cz - hd);
        const t1 = isoPoint(ax, ay, cx + hw, cy + h, cz - hd);
        const t2 = isoPoint(ax, ay, cx + hw, cy + h, cz + hd);
        const t3 = isoPoint(ax, ay, cx - hw, cy + h, cz + hd);

        // Bottom front vertices (y = cy)
        const b1 = isoPoint(ax, ay, cx + hw, cy, cz - hd);
        const b2 = isoPoint(ax, ay, cx + hw, cy, cz + hd);
        const b3 = isoPoint(ax, ay, cx - hw, cy, cz + hd);

        const strokeCol = colors.stroke || 'rgba(255,255,255,0.22)';

        // Left Front Face (t3 -> t2 -> b2 -> b3)
        ctx.beginPath();
        ctx.moveTo(t3.x, t3.y);
        ctx.lineTo(t2.x, t2.y);
        ctx.lineTo(b2.x, b2.y);
        ctx.lineTo(b3.x, b3.y);
        ctx.closePath();
        ctx.fillStyle = colors.front;
        ctx.fill();
        ctx.strokeStyle = strokeCol;
        ctx.lineWidth = 1;
        ctx.stroke();

        // Right Front Face (t2 -> t1 -> b1 -> b2)
        ctx.beginPath();
        ctx.moveTo(t2.x, t2.y);
        ctx.lineTo(t1.x, t1.y);
        ctx.lineTo(b1.x, b1.y);
        ctx.lineTo(b2.x, b2.y);
        ctx.closePath();
        ctx.fillStyle = colors.sideDark;
        ctx.fill();
        ctx.strokeStyle = strokeCol;
        ctx.lineWidth = 1;
        ctx.stroke();

        // Top Face (t0 -> t1 -> t2 -> t3)
        ctx.beginPath();
        ctx.moveTo(t0.x, t0.y);
        ctx.lineTo(t1.x, t1.y);
        ctx.lineTo(t2.x, t2.y);
        ctx.lineTo(t3.x, t3.y);
        ctx.closePath();
        ctx.fillStyle = colors.top;
        ctx.fill();
        ctx.strokeStyle = strokeCol;
        ctx.lineWidth = 1;
        ctx.stroke();

        return isoPoint(ax, ay, cx, cy + h, cz);
    }

    // Draw Sculpted 3D Stepper Icon on top of the Isometric Pedestal
    function drawStepper3DIcon(type, ax, ay, baseY, sc, palette, animTime, isAnimated) {
        const bob = isAnimated ? Math.sin(animTime * 2.6) * 4.5 : 0;
        const y0 = baseY + bob;

        const whitePages = {
            top: '#ffffff', front: '#e2e8f0', sideDark: '#cbd5e1', stroke: 'rgba(148,163,184,0.45)'
        };
        const blueScreen = {
            top: '#7dd3fc', front: '#0ea5e9', sideDark: '#0284c7', stroke: 'rgba(255,255,255,0.45)'
        };
        const goldAccent = {
            top: '#fde047', front: '#eab308', sideDark: '#ca8a04', stroke: 'rgba(255,255,255,0.45)'
        };

        if (type === 'book') {
            // 1. Stacked 3D Thesis Proposal Books
            drawIsoBox(ax, ay, 0, y0, 0, 38 * sc, 8 * sc, 30 * sc, palette);
            drawIsoBox(ax, ay, 0, y0 + 8 * sc, 0, 34 * sc, 6 * sc, 26 * sc, whitePages);
            return drawIsoBox(ax, ay, 0, y0 + 14 * sc, 0, 32 * sc, 7 * sc, 24 * sc, palette);
        } else if (type === 'laptop') {
            // 2. 3D Laptop (Mentoring & Google Meet)
            drawIsoBox(ax, ay, 0, y0, 4 * sc, 40 * sc, 5 * sc, 32 * sc, palette);
            drawIsoBox(ax, ay, 0, y0 + 5 * sc, -10 * sc, 38 * sc, 25 * sc, 5 * sc, palette);
            return drawIsoBox(ax, ay, 0, y0 + 8 * sc, -7 * sc, 32 * sc, 19 * sc, 3 * sc, blueScreen);
        } else if (type === 'revision') {
            // 3. 3D Revision Clipboard & Verification Stamp
            drawIsoBox(ax, ay, 0, y0, 0, 34 * sc, 7 * sc, 34 * sc, whitePages);
            drawIsoBox(ax, ay, -7 * sc, y0 + 7 * sc, -4 * sc, 14 * sc, 22 * sc, 14 * sc, palette);
            return drawIsoBox(ax, ay, 8 * sc, y0 + 7 * sc, 5 * sc, 14 * sc, 16 * sc, 14 * sc, goldAccent);
        } else if (type === 'podium') {
            // 4. 3D Seminar/Defense Podium Lectern
            drawIsoBox(ax, ay, 0, y0, 0, 36 * sc, 7 * sc, 36 * sc, whitePages);
            drawIsoBox(ax, ay, 0, y0 + 7 * sc, 0, 22 * sc, 24 * sc, 22 * sc, palette);
            return drawIsoBox(ax, ay, 0, y0 + 31 * sc, 0, 30 * sc, 6 * sc, 26 * sc, goldAccent);
        } else if (type === 'hardcover') {
            // 5. Standing 3D Hardcover Thesis Tome
            drawIsoBox(ax, ay, 0, y0, 0, 32 * sc, 30 * sc, 14 * sc, palette);
            return drawIsoBox(ax, ay, 0, y0 + 6 * sc, 6 * sc, 22 * sc, 18 * sc, 4 * sc, goldAccent);
        } else {
            // 6. 3D Graduation Cap (Toga Wisuda)
            drawIsoBox(ax, ay, 0, y0, 0, 24 * sc, 12 * sc, 24 * sc, palette);
            const topCap = drawIsoBox(ax, ay, 0, y0 + 12 * sc, 0, 44 * sc, 5 * sc, 44 * sc, palette);
            drawIsoBox(ax, ay, 0, y0 + 17 * sc, 0, 8 * sc, 3 * sc, 8 * sc, goldAccent);
            drawIsoBox(ax, ay, 16 * sc, y0 + 4 * sc, 16 * sc, 5 * sc, 13 * sc, 5 * sc, goldAccent);
            return topCap;
        }
    }

    let animTime = 0;
    function render() {
        requestAnimationFrame(render);
        if (!isVisible || canvas.offsetParent === null) return;

        const rect = canvas.getBoundingClientRect();
        if (rect.width < 10 || rect.height < 10) return;

        animTime += 0.025;

        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        if (canvas.width !== Math.floor(rect.width * dpr) || canvas.height !== Math.floor(rect.height * dpr)) {
            canvas.width = Math.floor(rect.width * dpr);
            canvas.height = Math.floor(rect.height * dpr);
        }

        ctx.save();
        ctx.scale(dpr, dpr);

        const W = rect.width;
        const H = rect.height;

        // Clear Surface
        ctx.fillStyle = isDarkScene ? '#090d16' : '#f8fafc';
        ctx.fillRect(0, 0, W, H);

        // Subtle Horizontal Perspective Grid Lines
        ctx.lineWidth = 1;
        ctx.strokeStyle = isDarkScene ? 'rgba(51, 65, 85, 0.28)' : 'rgba(203, 213, 225, 0.55)';
        for (let gy = 48; gy < H; gy += 36) {
            ctx.beginPath();
            ctx.moveTo(0, gy);
            ctx.lineTo(W, gy);
            ctx.stroke();
        }
        const colStep = W / 12;
        for (let gx = colStep; gx < W; gx += colStep) {
            ctx.beginPath();
            ctx.moveTo(gx, 0);
            ctx.lineTo(gx, H);
            ctx.stroke();
        }

        // Compute Fixed Left-to-Right Stepper Anchors (Stages 1 -> 6)
        const padX = Math.max(68, W * 0.085);
        const usableW = W - padX * 2;
        const stepSpan = usableW / 5;
        const baseLineY = H * 0.56;
        const sc = Math.max(0.82, Math.min(1.12, W / 960));

        const anchors = stagesData.map((st, idx) => {
            const isSel = st.id === selectedNodeId;
            const isHov = st.id === hoveredNodeId;
            const targetLift = (isSel || st.isCurrent) ? 12 : (isHov ? 7 : 0);
            stageLift[idx] += (targetLift - stageLift[idx]) * 0.15;

            return {
                x: padX + idx * stepSpan,
                y: baseLineY - stageLift[idx],
                baseY: baseLineY
            };
        });

        // 1. Draw 3D Isometric Stepper Track / Bridges connecting 1 -> 2 -> 3 -> 4 -> 5 -> 6
        for (let i = 0; i < stagesData.length - 1; i++) {
            const sA = stagesData[i];
            const sB = stagesData[i + 1];
            const a1 = anchors[i];
            const a2 = anchors[i + 1];

            const trackY = baseLineY - 12;

            // Outer 3D Track Beam
            ctx.beginPath();
            ctx.moveTo(a1.x, trackY);
            ctx.lineTo(a2.x, trackY);
            ctx.lineWidth = 10 * sc;
            ctx.lineCap = 'round';
            ctx.strokeStyle = isDarkScene ? '#1e293b' : '#e2e8f0';
            ctx.stroke();

            // Inner Lit Progress Conduit
            ctx.beginPath();
            ctx.moveTo(a1.x, trackY);
            ctx.lineTo(a2.x, trackY);
            ctx.lineWidth = 5 * sc;
            ctx.lineCap = 'round';
            if (sA.isCompleted && (sB.isCompleted || sB.isCurrent)) {
                ctx.strokeStyle = '#10b981';
            } else if (sA.isCurrent) {
                ctx.strokeStyle = '#ea580c';
            } else {
                ctx.strokeStyle = isDarkScene ? '#334155' : '#cbd5e1';
            }
            ctx.stroke();
        }

        // 2. Draw Flowing 3D Energy Spheres along Completed/Active Stepper Segments
        trackPackets.forEach(pk => {
            pk.t += pk.speed;
            if (pk.t > 1) pk.t -= 1;

            if (pk.seg <= activeStageIdx || role !== 'mahasiswa') {
                const a1 = anchors[pk.seg];
                const a2 = anchors[pk.seg + 1];
                if (!a1 || !a2) return;
                const px = a1.x + (a2.x - a1.x) * pk.t;
                const py = baseLineY - 12;

                ctx.beginPath();
                ctx.arc(px, py, 4.5 * sc, 0, Math.PI * 2);
                ctx.fillStyle = pk.seg < activeStageIdx ? '#34d399' : '#fb923c';
                ctx.fill();
            }
        });

        // 3. Draw Each of the 6 Stationary 3D Isometric Stepper Nodes (Left to Right)
        hitBoxes = [];

        stagesData.forEach((st, idx) => {
            const anc = anchors[idx];
            const isSelected = st.id === selectedNodeId;
            const isHovered = st.id === hoveredNodeId;

            // Ground Shadow Ellipse
            ctx.beginPath();
            ctx.ellipse(anc.x, anc.baseY + 8 * sc, 40 * sc, 16 * sc, 0, 0, Math.PI * 2);
            ctx.fillStyle = st.isCurrent
                ? 'rgba(234, 88, 12, 0.22)'
                : (isDarkScene ? 'rgba(0, 0, 0, 0.38)' : 'rgba(148, 163, 184, 0.28)');
            ctx.fill();

            // Active Stage Pulsing Isometric Halo Ring
            if (st.isCurrent || isSelected) {
                const pulseR = (46 + Math.sin(animTime * 3) * 4) * sc;
                ctx.beginPath();
                ctx.ellipse(anc.x, anc.y - 2 * sc, pulseR, pulseR * 0.48, 0, 0, Math.PI * 2);
                ctx.strokeStyle = st.isCurrent ? 'rgba(249, 115, 22, 0.65)' : 'rgba(59, 130, 246, 0.55)';
                ctx.lineWidth = 2;
                ctx.stroke();
            }

            // Semantic 3D Block Palette
            let colors;
            if (st.isCurrent) {
                colors = {
                    top: '#fb923c',
                    front: '#ea580c',
                    sideDark: '#c2410c',
                    stroke: isSelected || isHovered ? '#ffffff' : 'rgba(255,255,255,0.35)'
                };
            } else if (st.isCompleted) {
                colors = {
                    top: '#34d399',
                    front: '#059669',
                    sideDark: '#047857',
                    stroke: isSelected || isHovered ? '#ffffff' : 'rgba(255,255,255,0.28)'
                };
            } else {
                colors = isDarkScene ? {
                    top: '#475569',
                    front: '#334155',
                    sideDark: '#1e293b',
                    stroke: isSelected || isHovered ? '#cbd5e1' : 'rgba(148,163,184,0.25)'
                } : {
                    top: '#e2e8f0',
                    front: '#cbd5e1',
                    sideDark: '#94a3b8',
                    stroke: isSelected || isHovered ? '#334155' : 'rgba(100,116,139,0.28)'
                };
            }

            // Tier 1: Wide Isometric Base Platform
            const baseColors = isDarkScene ? {
                top: '#1e293b', front: '#0f172a', sideDark: '#090d16',
                stroke: isSelected ? '#fb923c' : 'rgba(100,116,139,0.45)'
            } : {
                top: '#ffffff', front: '#e2e8f0', sideDark: '#cbd5e1',
                stroke: isSelected ? '#ea580c' : 'rgba(148,163,184,0.55)'
            };
            drawIsoBox(anc.x, anc.y, 0, 0, 0, 56 * sc, 10 * sc, 56 * sc, baseColors);

            // Tier 2: Colored 3D Stepper Pedestal
            drawIsoBox(anc.x, anc.y, 0, 10 * sc, 0, 42 * sc, 18 * sc, 42 * sc, colors);

            // Tier 3: Sculpted 3D Stage Figurine on Top
            drawStepper3DIcon(
                st.modelType,
                anc.x,
                anc.y,
                28 * sc,
                sc,
                colors,
                animTime,
                st.isCurrent || isSelected
            );

            // Stepper Labels Below Pedestal (Just like a Classic Stepper!)
            const labelTopY = anc.baseY + 34;

            // Stage Title ("1. Judul Skripsi", etc.)
            ctx.font = (st.isCurrent || isSelected) ? '900 11.5px sans-serif' : 'bold 11px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = st.isCurrent
                ? (isDarkScene ? '#fb923c' : '#ea580c')
                : (st.isCompleted ? (isDarkScene ? '#f8fafc' : '#0f172a') : (isDarkScene ? '#94a3b8' : '#64748b'));
            ctx.fillText(`${idx + 1}. ${st.short}`, anc.x, labelTopY);

            // Status Pill Below Title
            const pillText = st.subBadge || st.badge;
            ctx.font = 'bold 9.5px sans-serif';
            const pillW = Math.max(64, ctx.measureText(pillText).width + 16);
            const pillH = 19;
            const pillY = labelTopY + 21;

            ctx.beginPath();
            if (ctx.roundRect) {
                ctx.roundRect(anc.x - pillW / 2, pillY - pillH / 2, pillW, pillH, 9);
            } else {
                ctx.rect(anc.x - pillW / 2, pillY - pillH / 2, pillW, pillH);
            }

            if (st.isCurrent) {
                ctx.fillStyle = '#ea580c';
                ctx.strokeStyle = '#fb923c';
            } else if (st.isCompleted) {
                ctx.fillStyle = isDarkScene ? 'rgba(6, 78, 59, 0.75)' : '#ecfdf5';
                ctx.strokeStyle = isDarkScene ? '#059669' : '#a7f3d0';
            } else {
                ctx.fillStyle = isDarkScene ? 'rgba(30, 41, 59, 0.8)' : '#f1f5f9';
                ctx.strokeStyle = isDarkScene ? '#334155' : '#cbd5e1';
            }
            ctx.fill();
            ctx.lineWidth = isSelected ? 1.5 : 1;
            ctx.stroke();

            ctx.fillStyle = st.isCurrent
                ? '#ffffff'
                : (st.isCompleted ? (isDarkScene ? '#6ee7b7' : '#047857') : (isDarkScene ? '#94a3b8' : '#64748b'));
            ctx.fillText(pillText, anc.x, pillY + 0.5);

            // Floating Mentoring Cycle Callouts above the Active Mentoring Stage
            if (idx === activeStageIdx) {
                cyclePills.forEach(cp => {
                    const floatY = anc.y + cp.dy * sc + Math.sin(animTime * 2.2 + cp.phase) * 3;
                    const floatX = anc.x + cp.dx * sc;

                    ctx.font = 'bold 9px sans-serif';
                    const cw = ctx.measureText(cp.label).width + 10;
                    const ch = 16;

                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(floatX - cw / 2, floatY - ch / 2, cw, ch, 5);
                    } else {
                        ctx.rect(floatX - cw / 2, floatY - ch / 2, cw, ch);
                    }
                    ctx.fillStyle = isDarkScene ? 'rgba(15, 23, 42, 0.92)' : 'rgba(255, 255, 255, 0.95)';
                    ctx.strokeStyle = '#3b82f6';
                    ctx.lineWidth = 1;
                    ctx.fill();
                    ctx.stroke();

                    ctx.fillStyle = isDarkScene ? '#93c5fd' : '#1d4ed8';
                    ctx.fillText(cp.label, floatX, floatY + 0.5);
                });
            }

            // Save Clickable Hitbox for this Stepper Column
            hitBoxes.push({
                id: st.id,
                x1: anc.x - 52 * sc,
                x2: anc.x + 52 * sc,
                y1: anc.y - 85 * sc,
                y2: pillY + 14
            });
        });

        ctx.restore();
    }

    canvas.addEventListener('pointermove', e => {
        const rect = canvas.getBoundingClientRect();
        const mx = e.clientX - rect.left;
        const my = e.clientY - rect.top;

        let found = null;
        for (let i = 0; i < hitBoxes.length; i++) {
            const b = hitBoxes[i];
            if (mx >= b.x1 && mx <= b.x2 && my >= b.y1 && my <= b.y2) {
                found = b.id;
                break;
            }
        }
        hoveredNodeId = found;
        canvas.style.cursor = found ? 'pointer' : 'default';
    });

    canvas.addEventListener('click', e => {
        const rect = canvas.getBoundingClientRect();
        const mx = e.clientX - rect.left;
        const my = e.clientY - rect.top;

        for (let i = 0; i < hitBoxes.length; i++) {
            const b = hitBoxes[i];
            if (mx >= b.x1 && mx <= b.x2 && my >= b.y1 && my <= b.y2) {
                selectedNodeId = b.id;
                updateInspector();
                break;
            }
        }
    });

    updateInspector();
    requestAnimationFrame(render);
})();
</script>

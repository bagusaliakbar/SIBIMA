@props([
    'role' => 'mahasiswa',
    'progress' => null,
    'pastSessionsCount' => 0,
    'embedded' => true,
])

@php
    $uid = 'sibima3d_' . uniqid();
    $currentStageNum = (int) ($progress['currentStage'] ?? 2);
    if ($currentStageNum < 1 || $currentStageNum > 6) {
        $currentStageNum = 2;
    }
    $activeIdx = $currentStageNum - 1;

    $rawStages = $progress['stages'] ?? [];
    $stagesPayload = [];

    foreach ($rawStages as $idx => $st) {
        $stageNo = (int) ($st['number'] ?? ($idx + 1));
        $numStr = str_pad((string) $stageNo, 2, '0', STR_PAD_LEFT);
        $cleanName = preg_replace('/^\d+\.\s*/', '', (string) ($st['name'] ?? ''));
        $cleanShort = preg_replace('/^\d+\.\s*/', '', (string) ($st['short_name'] ?? $cleanName));
        $badgeText = !empty($st['badge'])
            ? $st['badge']
            : (!empty($st['is_completed']) ? 'Selesai' : (!empty($st['is_current']) ? 'Sedang Berjalan' : 'Terkunci'));
        $stagesPayload[] = [
            'id' => 'stage-' . ($idx + 1),
            'stageNo' => $stageNo,
            'num' => $numStr,
            'short' => $cleanShort,
            'title' => $stageNo . '. ' . $cleanName,
            'desc' => $st['desc'] ?? '',
            'badge' => $badgeText,
            'subBadge' => !empty($st['is_completed']) ? 'Selesai' : (!empty($st['is_current']) ? 'Sedang Berjalan' : 'Terkunci'),
            'url' => $st['url'] ?? '#',
            'action' => !empty($st['is_locked']) ? 'Tahap Terkunci' : ('Buka ' . $cleanShort),
            'isCompleted' => !empty($st['is_completed']),
            'isCurrent' => !empty($st['is_current']),
            'isLocked' => !empty($st['is_locked']),
        ];
    }
@endphp

<div id="{{ $uid }}_wrap" class="space-y-3">
    <!-- Stationary Uniform 3D Isometric Stepper Viewport -->
    <div class="relative rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 overflow-x-auto hide-scrollbar transition-colors duration-300">
        <div class="min-w-[720px] sm:min-w-0 relative">
            <canvas id="{{ $uid }}_canvas" class="w-full h-[260px] sm:h-[280px] block select-none"></canvas>
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

<script>
(function() {
    const uid = @json($uid);
    const stagesData = @json($stagesPayload);
    const activeStageIdx = @json($activeIdx);

    const canvas = document.getElementById(uid + '_canvas');
    if (!canvas || !stagesData.length) return;
    const ctx = canvas.getContext('2d');

    let isDarkScene = document.documentElement.classList.contains('dark');
    let selectedNodeId = stagesData[activeStageIdx] ? stagesData[activeStageIdx].id : stagesData[0].id;
    let hoveredNodeId = null;
    let isVisible = true;
    let hitBoxes = [];

    // Smooth elevation animation per stage when hovered/selected
    const stageLift = stagesData.map((_, i) => i === activeStageIdx ? 8 : 0);

    // Animated energy packets moving left-to-right along the stepper track
    const trackPackets = [
        { seg: 0, t: 0.2, speed: 0.008 },
        { seg: 1, t: 0.65, speed: 0.008 },
        { seg: 2, t: 0.35, speed: 0.008 },
        { seg: 3, t: 0.8, speed: 0.008 },
        { seg: 4, t: 0.1, speed: 0.008 }
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
    function isoPoint(ax, ay, lx, ly, lz) {
        const cos30 = 0.866025;
        const sin30 = 0.5;
        return {
            x: ax + (lx - lz) * cos30,
            y: ay + (lx + lz) * sin30 - ly
        };
    }

    // Draw a Stationary Isometric 3D Box at Anchor (ax, ay)
    function drawIsoBox(ax, ay, cx, cy, cz, w, h, d, colors) {
        const hw = w / 2;
        const hd = d / 2;

        const t0 = isoPoint(ax, ay, cx - hw, cy + h, cz - hd);
        const t1 = isoPoint(ax, ay, cx + hw, cy + h, cz - hd);
        const t2 = isoPoint(ax, ay, cx + hw, cy + h, cz + hd);
        const t3 = isoPoint(ax, ay, cx - hw, cy + h, cz + hd);

        const b1 = isoPoint(ax, ay, cx + hw, cy, cz - hd);
        const b2 = isoPoint(ax, ay, cx + hw, cy, cz + hd);
        const b3 = isoPoint(ax, ay, cx - hw, cy, cz + hd);

        const strokeCol = colors.stroke || 'rgba(255,255,255,0.22)';

        // Left Front Face
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

        // Right Front Face
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

        // Top Face
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

    // Draw Uniform 3D Stepper Node (Same Geometry & Proportions for All 6 Stages)
    function drawUniform3DNode(st, ax, ay, sc, colors, baseColors, capColors, animTime, isSelected) {
        const bob = (st.isCurrent || isSelected) ? Math.sin(animTime * 2.6) * 3.5 : 0;

        // Tier 1: Uniform Isometric Base Pad (56 x 9 x 56)
        drawIsoBox(ax, ay, 0, 0, 0, 56 * sc, 9 * sc, 56 * sc, baseColors);

        // Tier 2: Uniform Colored Main 3D Cube/Block (42 x 26 x 42)
        drawIsoBox(ax, ay, 0, (9 + bob) * sc, 0, 42 * sc, 26 * sc, 42 * sc, colors);

        // Tier 3: Uniform Top Emblem Cap Plate (30 x 5 x 30)
        const topCenter = drawIsoBox(ax, ay, 0, (35 + bob) * sc, 0, 30 * sc, 5 * sc, 30 * sc, capColors);

        // Draw Uniform Status Symbol / Number on Top Emblem Plate
        ctx.save();
        ctx.translate(topCenter.x, topCenter.y);
        ctx.strokeStyle = (st.isCompleted || st.isCurrent) ? '#ffffff' : (isDarkScene ? '#94a3b8' : '#64748b');
        ctx.fillStyle = ctx.strokeStyle;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        if (st.isCompleted) {
            // Bold Checkmark Icon
            ctx.lineWidth = 2.6 * sc;
            ctx.beginPath();
            ctx.moveTo(-6 * sc, 0);
            ctx.lineTo(-1.5 * sc, 4.5 * sc);
            ctx.lineTo(6.5 * sc, -4 * sc);
            ctx.stroke();
        } else if (st.isLocked) {
            // Clean Padlock Icon
            ctx.lineWidth = 1.8 * sc;
            ctx.strokeRect(-4.5 * sc, -1 * sc, 9 * sc, 7 * sc);
            ctx.beginPath();
            ctx.arc(0, -1 * sc, 3 * sc, Math.PI, 0, false);
            ctx.stroke();
        } else {
            // Active Stage Number ("01" .. "06")
            ctx.font = `900 ${Math.round(11 * sc)}px sans-serif`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(st.num, 0, 0.5);
        }
        ctx.restore();
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

        // Subtle Perspective Grid Lines
        ctx.lineWidth = 1;
        ctx.strokeStyle = isDarkScene ? 'rgba(51, 65, 85, 0.25)' : 'rgba(203, 213, 225, 0.5)';
        for (let gy = 44; gy < H; gy += 36) {
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
        const baseLineY = H * 0.53;
        const sc = Math.max(0.84, Math.min(1.1, W / 960));

        const anchors = stagesData.map((st, idx) => {
            const isSel = st.id === selectedNodeId;
            const isHov = st.id === hoveredNodeId;
            const targetLift = (isSel || st.isCurrent) ? 9 : (isHov ? 5 : 0);
            stageLift[idx] += (targetLift - stageLift[idx]) * 0.15;

            return {
                x: padX + idx * stepSpan,
                y: baseLineY - stageLift[idx],
                baseY: baseLineY
            };
        });

        // 1. Draw 3D Isometric Stepper Track connecting 1 -> 2 -> 3 -> 4 -> 5 -> 6
        for (let i = 0; i < stagesData.length - 1; i++) {
            const sA = stagesData[i];
            const sB = stagesData[i + 1];
            const a1 = anchors[i];
            const a2 = anchors[i + 1];
            const trackY = baseLineY - 12;

            ctx.beginPath();
            ctx.moveTo(a1.x, trackY);
            ctx.lineTo(a2.x, trackY);
            ctx.lineWidth = 10 * sc;
            ctx.lineCap = 'round';
            ctx.strokeStyle = isDarkScene ? '#1e293b' : '#e2e8f0';
            ctx.stroke();

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

        // 2. Draw Flowing Energy Spheres along Completed/Active Segments
        trackPackets.forEach(pk => {
            pk.t += pk.speed;
            if (pk.t > 1) pk.t -= 1;

            if (pk.seg <= activeStageIdx) {
                const a1 = anchors[pk.seg];
                const a2 = anchors[pk.seg + 1];
                if (!a1 || !a2) return;
                const px = a1.x + (a2.x - a1.x) * pk.t;
                const py = baseLineY - 12;

                ctx.beginPath();
                ctx.arc(px, py, 4.2 * sc, 0, Math.PI * 2);
                ctx.fillStyle = pk.seg < activeStageIdx ? '#34d399' : '#fb923c';
                ctx.fill();
            }
        });

        // 3. Draw All 6 Uniform 3D Isometric Stepper Nodes
        hitBoxes = [];

        stagesData.forEach((st, idx) => {
            const anc = anchors[idx];
            const isSelected = st.id === selectedNodeId;
            const isHovered = st.id === hoveredNodeId;

            // Ground Shadow Ellipse
            ctx.beginPath();
            ctx.ellipse(anc.x, anc.baseY + 8 * sc, 38 * sc, 15 * sc, 0, 0, Math.PI * 2);
            ctx.fillStyle = st.isCurrent
                ? 'rgba(234, 88, 12, 0.22)'
                : (isDarkScene ? 'rgba(0, 0, 0, 0.36)' : 'rgba(148, 163, 184, 0.26)');
            ctx.fill();

            // Active / Selected Pulsing Isometric Ring
            if (st.isCurrent || isSelected) {
                const pulseR = (44 + Math.sin(animTime * 3) * 3.5) * sc;
                ctx.beginPath();
                ctx.ellipse(anc.x, anc.y - 2 * sc, pulseR, pulseR * 0.48, 0, 0, Math.PI * 2);
                ctx.strokeStyle = st.isCurrent ? 'rgba(249, 115, 22, 0.65)' : 'rgba(59, 130, 246, 0.55)';
                ctx.lineWidth = 2;
                ctx.stroke();
            }

            // Semantic 3D Block Palettes
            let colors, capColors;
            if (st.isCurrent) {
                colors = {
                    top: '#fb923c', front: '#ea580c', sideDark: '#c2410c',
                    stroke: isSelected || isHovered ? '#ffffff' : 'rgba(255,255,255,0.35)'
                };
                capColors = {
                    top: '#fdba74', front: '#f97316', sideDark: '#ea580c', stroke: '#ffffff'
                };
            } else if (st.isCompleted) {
                colors = {
                    top: '#34d399', front: '#059669', sideDark: '#047857',
                    stroke: isSelected || isHovered ? '#ffffff' : 'rgba(255,255,255,0.28)'
                };
                capColors = {
                    top: '#6ee7b7', front: '#10b981', sideDark: '#059669', stroke: 'rgba(255,255,255,0.5)'
                };
            } else {
                colors = isDarkScene ? {
                    top: '#475569', front: '#334155', sideDark: '#1e293b',
                    stroke: isSelected || isHovered ? '#cbd5e1' : 'rgba(148,163,184,0.25)'
                } : {
                    top: '#e2e8f0', front: '#cbd5e1', sideDark: '#94a3b8',
                    stroke: isSelected || isHovered ? '#334155' : 'rgba(100,116,139,0.28)'
                };
                capColors = isDarkScene ? {
                    top: '#334155', front: '#1e293b', sideDark: '#0f172a', stroke: 'rgba(148,163,184,0.3)'
                } : {
                    top: '#f8fafc', front: '#e2e8f0', sideDark: '#cbd5e1', stroke: 'rgba(148,163,184,0.45)'
                };
            }

            const baseColors = isDarkScene ? {
                top: '#1e293b', front: '#0f172a', sideDark: '#090d16',
                stroke: isSelected ? '#fb923c' : 'rgba(100,116,139,0.45)'
            } : {
                top: '#ffffff', front: '#e2e8f0', sideDark: '#cbd5e1',
                stroke: isSelected ? '#ea580c' : 'rgba(148,163,184,0.55)'
            };

            // Draw the Uniform 3D Isometric Node
            drawUniform3DNode(st, anc.x, anc.y, sc, colors, baseColors, capColors, animTime, isSelected);

            // Stepper Labels Below Node
            const labelTopY = anc.baseY + 34;

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
            const pillW = Math.max(68, ctx.measureText(pillText).width + 16);
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

            hitBoxes.push({
                id: st.id,
                x1: anc.x - 50 * sc,
                x2: anc.x + 50 * sc,
                y1: anc.y - 60 * sc,
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

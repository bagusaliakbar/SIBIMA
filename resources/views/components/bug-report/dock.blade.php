<!-- ========================================================================= -->
<!-- FLOATING CAPSULE DOCK (BOTTOM-RIGHT CORNER)                                -->
<!-- ========================================================================= -->
@if(\App\Models\Setting::isBugReportEnabled())
<div id="bug-report-dock-container" class="select-none">

    <!-- 1. Collapsed Mini Pill / FAB (Round compact button on mobile, pill on desktop) -->
    <div id="bug-report-collapsed-pill" class="pointer-events-auto">
        <button onclick="window.toggleBugCollapse(false); event.stopPropagation();"
                type="button"
                class="bug-report-pill-btn group relative flex items-center justify-center p-3 sm:px-3.5 sm:py-2.5 rounded-full hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer"
                title="Buka Toolbar Pelaporan Bug">
            <span class="relative flex items-center justify-center w-5 h-5 shrink-0">
                <!-- Pulsing Badge Indicator Dot -->
                <span class="absolute top-0 right-0 flex h-2 w-2 pointer-events-none">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="bug-badge-ring relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                </span>
                <!-- Bug/Alert Icon -->
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </span>
            <span class="bug-report-pill-text hidden sm:inline-block ml-2 text-xs font-bold tracking-tight">
                Lapor Bug
            </span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="bug-report-pill-chevron hidden sm:inline-block ml-1 w-4 h-4 transition-colors">
                <path d="M5 15l7-7 7 7" />
            </svg>
        </button>
    </div>

    <!-- 2. Full Vertical Capsule Dock -->
    <div id="bug-report-full-dock" class="bug-full-dock-container">

        <!-- Grip Drag Indicator (6 dots) -->
        <div class="bug-dock-grip flex flex-col items-center justify-center py-0.5 cursor-default" title="Toolbar Pelaporan Bug">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <circle cx="7" cy="5" r="1.5" />
                <circle cx="13" cy="5" r="1.5" />
                <circle cx="7" cy="10" r="1.5" />
                <circle cx="13" cy="10" r="1.5" />
                <circle cx="7" cy="15" r="1.5" />
                <circle cx="13" cy="15" r="1.5" />
            </svg>
        </div>

        <!-- Divider Line -->
        <div class="bug-dock-divider-line mb-0.5"></div>

        <!-- Button 1: Collapse/Perkecil -->
        <button onclick="window.toggleBugCollapse(true); event.stopPropagation();"
                type="button"
                aria-label="Perkecil Toolbar"
                class="bug-dock-circle-btn bug-dock-collapse-btn transition-all duration-200 hover:scale-105 active:scale-90 group relative"
                title="Perkecil Toolbar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:-translate-x-0.5 transition-transform">
                <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                Perkecil
            </span>
        </button>

        <!-- Button 2: Panduan Pelaporan Bug -->
        <button onclick="window.openBugGuideModal(); event.stopPropagation();"
                type="button"
                aria-label="Panduan & Bantuan Pelaporan"
                class="bug-dock-circle-btn bg-orange-500 hover:bg-orange-600 text-white transition-all duration-200 hover:scale-105 active:scale-90 shadow-md shadow-orange-500/30 group relative"
                title="Panduan & Bantuan Pelaporan">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="8.5" />
                <circle cx="12" cy="8" r="1.1" fill="currentColor" stroke="none" />
                <path d="M12 11.5v4.5" />
            </svg>
            <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                Panduan
            </span>
        </button>

        <!-- Button 3: Status Laporan Saya -->
        <button onclick="window.openBugHistoryModal(); event.stopPropagation();"
                type="button"
                aria-label="Status & Riwayat Laporan Saya"
                class="bug-dock-circle-btn bg-emerald-500 hover:bg-emerald-600 text-white transition-all duration-200 hover:scale-105 active:scale-90 shadow-md shadow-emerald-500/30 group relative"
                title="Status & Riwayat Laporan Saya">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 13l4.5 4.5L19 7" />
            </svg>
            <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                Status Laporan
            </span>
        </button>

        <!-- Button 4: Lapor Bug / Usulan Fitur -->
        <button onclick="window.openBugReportModal(); event.stopPropagation();"
                type="button"
                aria-label="Lapor Bug atau Usulan Fitur"
                class="bug-dock-circle-btn bg-orange-600 hover:bg-orange-700 text-white transition-all duration-200 hover:scale-105 active:scale-90 shadow-lg shadow-orange-500/30 group relative"
                title="Lapor Bug atau Usulan Fitur">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5" />
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                Lapor Bug / Usulan Fitur
            </span>
        </button>

        @if(in_array(Auth::user()?->role, ['admin', 'kaprodi']))
        <!-- Quick Link for Admin/Kaprodi to Bug Inbox -->
        <div class="bug-dock-divider-line my-0.5"></div>
        <a href="{{ route('admin.bug-reports.index') }}"
           aria-label="Inbox Kelola Bug (Admin/Kaprodi)"
           class="bug-dock-circle-btn bug-dock-admin-link transition-all duration-200 hover:scale-105 active:scale-90 group relative"
           title="Inbox Kelola Bug (Admin/Kaprodi)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            <span class="absolute right-14 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-md">
                Panel Kelola Bug
            </span>
        </a>
        @endif

    </div>
</div>
@endif

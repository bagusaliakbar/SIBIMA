            <!-- Sidebar Backdrop for Mobile -->
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false" 
                x-transition:enter="transition ease-in-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in-out duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 md:hidden"
                style="display: none;">
            </div>

            <!-- Sidebar (Dark & Premium) -->
            <aside 
                x-data="{ sidebarHovered: false }"
                @mouseenter="if (sidebarCollapsed) sidebarHovered = true"
                @mouseleave="sidebarHovered = false"
                :class="{
                    'translate-x-0': sidebarOpen,
                    '-translate-x-full': !sidebarOpen,
                    'md:w-[280px]': !sidebarCollapsed || sidebarHovered,
                    'md:w-[80px] sidebar-collapsed': sidebarCollapsed && !sidebarHovered,
                    'sidebar-hover-expanded shadow-2xl z-50': sidebarCollapsed && sidebarHovered
                }"
                class="w-[280px] bg-[#0c1427] dark:bg-[#070b14] text-slate-400 flex flex-col z-40 shrink-0 shadow-2xl fixed inset-y-0 left-0 transition-all duration-300 ease-in-out md:relative md:translate-x-0 md:inset-auto md:h-full md:flex">
                <!-- Sidebar Pattern Overlay -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: url('{{ asset('images/carbon-fibre.png') }}')"></div>
                
                <div class="h-20 flex items-center border-b border-white/5 dark:border-white/[0.02] relative z-10 transition-all duration-300"
                     :class="(sidebarCollapsed && !sidebarHovered) ? 'px-3 justify-center' : 'px-6 justify-between'">
                    <div class="flex items-center gap-3 overflow-hidden cursor-pointer">
                        <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-10 h-10 object-contain shrink-0 transition-transform duration-300" :class="{ 'hover:scale-105': (sidebarCollapsed && !sidebarHovered) }">
                        <div x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity.duration.200ms class="sidebar-header-title whitespace-nowrap overflow-hidden">
                            <h1 class="text-xl font-black text-white tracking-tighter">SIBIMA</h1>
                            <span class="text-[11px] uppercase tracking-wider text-orange-400 font-bold block -mt-1">Fasilkom UNSUB</span>
                        </div>
                    </div>
                    <!-- Close Button for Mobile -->
                    <button type="button" @click="sidebarOpen = false" aria-label="Tutup menu navigasi" title="Tutup menu navigasi" class="md:hidden w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 transition-all">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto py-6 custom-scrollbar relative z-10 transition-all duration-300"
                     :class="(sidebarCollapsed && !sidebarHovered) ? 'px-2 py-4' : 'px-4 py-6'">
                    <!-- General Menu -->
                    <div class="sidebar-section-header px-4 mb-4">
                        <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Menu Utama</p>
                        <div class="sidebar-section-divider hidden w-6 h-0.5 bg-white/10 rounded-full mx-auto my-2" title="Menu Utama"></div>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <a href="{{ route('dashboard') }}" 
                           data-tooltip="Dashboard" title="Dashboard"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span class="sidebar-text">Dashboard</span>
                        </a>

                        <a href="{{ route('chat.index') }}" 
                           data-tooltip="Pesan" title="Pesan"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('chat.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('chat.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            <span class="sidebar-text">Pesan</span>
                        </a>
                        <a href="{{ route('calendar.index') }}" 
                           data-tooltip="Kalender Akademik" title="Kalender Akademik"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('calendar.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('calendar.index') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="sidebar-text">Kalender Akademik</span>
                        </a>
                        <a href="{{ route('repositories.index') }}" 
                           data-tooltip="Katalog Pustaka" title="Katalog Pustaka"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('repositories.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('repositories.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <span class="sidebar-text">Katalog Pustaka</span>
                        </a>
                        <a href="{{ route('guidance-documents.index') }}" 
                           data-tooltip="Panduan & Dokumen" title="Panduan & Dokumen"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('guidance-documents.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('guidance-documents.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="sidebar-text">Panduan & Dokumen</span>
                        </a>
                        <a href="{{ route('faqs.index') }}" 
                           data-tooltip="Pusat Bantuan (FAQ)" title="Pusat Bantuan (FAQ)"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('faqs.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('faqs.index') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="sidebar-text">Pusat Bantuan (FAQ)</span>
                        </a>
                        <button type="button" 
                                data-tooltip="Lapor Kendala / Bug" title="Lapor Kendala / Bug"
                                @click="sidebarOpen = false; if (window.openBugReportModal) window.openBugReportModal();"
                                class="w-full sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 text-slate-400 hover:text-white hover:bg-white/5 font-medium text-left cursor-pointer">
                            <svg class="w-5 h-5 mr-3 transition-colors text-slate-500 group-hover:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="sidebar-text">Lapor Kendala / Bug</span>
                        </button>
                    </nav>
                        
                        @if(Auth::user()->role === 'mahasiswa')
                        @php 
                            $thesis = cache()->remember('u_' . Auth::id() . '_thesis_sidebar', 30, fn() => \App\Models\Thesis::where('student_id', Auth::id())->first());
                        @endphp

                        <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                            <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Tahapan Skripsi</p>
                            <div class="sidebar-section-divider hidden w-6 h-0.5 bg-white/10 rounded-full mx-auto my-2" title="Tahapan Skripsi"></div>
                        </div>
                        <nav class="space-y-1 mb-10">
                            @if($thesis)
                            <button type="button"
                               @click="$dispatch('notify', { title: 'Tahap 1 Selesai', message: 'Anda sudah mengajukan judul skripsi. Silakan pantau progres bimbingan pada Dashboard atau Logbook.', type: 'success' })"
                               data-tooltip="1. Pengajuan Judul (Selesai)" title="1. Pengajuan Judul (Selesai)"
                               class="w-full group flex items-center justify-between px-4 py-3 rounded-xl text-sm text-emerald-400/90 hover:text-emerald-300 bg-emerald-500/[0.06] hover:bg-emerald-500/10 border border-emerald-500/20 font-semibold transition-all cursor-pointer text-left">
                                <span class="flex items-center min-w-0">
                                    <svg aria-hidden="true" class="w-5 h-5 mr-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="sidebar-text truncate">1. Pengajuan Judul</span>
                                </span>
                                <span class="sidebar-badge px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 leading-none shrink-0">Selesai</span>
                            </button>
                            @else
                            <a href="{{ route('theses.create') }}" 
                               data-tooltip="1. Pengajuan Judul" title="1. Pengajuan Judul"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('theses.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('theses.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="sidebar-text">1. Pengajuan Judul</span>
                            </a>
                            @endif

                            @php
                                $hasSeminar = $thesis ? cache()->remember('u_' . Auth::id() . '_has_sem', 30, fn() => \App\Models\SeminarApplication::where('thesis_id', $thesis->id)->whereIn('status', ['approved', 'completed', 'finished'])->exists() || \App\Models\SeminarScheduleDetail::where('thesis_id', $thesis->id)->exists()) : false;
                            @endphp
                            @if($hasSeminar)
                            <a href="{{ route('seminar-applications.index') }}"
                               data-tooltip="2. Pendaftaran Seminar (Selesai)" title="2. Pendaftaran Seminar (Selesai - Klik untuk lihat detail)"
                               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm {{ request()->routeIs('seminar-applications.*') ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/40 ring-1 ring-emerald-400/30' : 'text-emerald-400/90 hover:text-emerald-300 bg-emerald-500/[0.06] hover:bg-emerald-500/10 border-emerald-500/20' }} border font-semibold transition-all">
                                <span class="flex items-center min-w-0">
                                    <svg aria-hidden="true" class="w-5 h-5 mr-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="sidebar-text truncate">2. Pendaftaran Seminar</span>
                                </span>
                                <span class="sidebar-badge px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 leading-none shrink-0">Selesai</span>
                            </a>
                            @else
                            <a href="{{ route('seminar-applications.index') }}" 
                               data-tooltip="2. Pendaftaran Seminar" title="2. Pendaftaran Seminar"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('seminar-applications.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('seminar-applications.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="sidebar-text">2. Pendaftaran Seminar</span>
                            </a>
                            @endif

                            @php
                                $hasSeminarRev = $thesis ? cache()->remember('u_' . Auth::id() . '_has_sem_rev', 30, fn() => $thesis->hasPassedSeminarRevision()) : false;
                            @endphp
                            @if($hasSeminarRev)
                            <a href="{{ route('student-seminar-revisions.index') }}"
                               data-tooltip="3. Revisi Seminar (Selesai)" title="3. Revisi Seminar (Selesai - Klik untuk lihat detail)"
                               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm {{ request()->routeIs('student-seminar-revisions.*') ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/40 ring-1 ring-emerald-400/30' : 'text-emerald-400/90 hover:text-emerald-300 bg-emerald-500/[0.06] hover:bg-emerald-500/10 border-emerald-500/20' }} border font-semibold transition-all">
                                <span class="flex items-center min-w-0">
                                    <svg aria-hidden="true" class="w-5 h-5 mr-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="sidebar-text truncate">3. Revisi Seminar</span>
                                </span>
                                <span class="sidebar-badge px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 leading-none shrink-0">Selesai</span>
                            </a>
                            @else
                            <a href="{{ route('student-seminar-revisions.index') }}" 
                               data-tooltip="3. Revisi Seminar" title="3. Revisi Seminar"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('student-seminar-revisions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('student-seminar-revisions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span class="sidebar-text">3. Revisi Seminar</span>
                            </a>
                            @endif

                            @php
                                $hasDefense = $thesis ? cache()->remember('u_' . Auth::id() . '_has_def', 30, fn() => \App\Models\ThesisDefenseApplication::where('thesis_id', $thesis->id)->whereIn('status', ['approved', 'completed', 'finished'])->exists() || \App\Models\ThesisDefenseScheduleDetail::where('thesis_id', $thesis->id)->exists()) : false;
                            @endphp
                            @if($hasDefense)
                            <a href="{{ route('thesis-defense-applications.index') }}"
                               data-tooltip="4. Pendaftaran Sidang (Selesai)" title="4. Pendaftaran Sidang (Selesai - Klik untuk lihat detail)"
                               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm {{ request()->routeIs('thesis-defense-applications.*') ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/40 ring-1 ring-emerald-400/30' : 'text-emerald-400/90 hover:text-emerald-300 bg-emerald-500/[0.06] hover:bg-emerald-500/10 border-emerald-500/20' }} border font-semibold transition-all">
                                <span class="flex items-center min-w-0">
                                    <svg aria-hidden="true" class="w-5 h-5 mr-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="sidebar-text truncate">4. Pendaftaran Sidang</span>
                                </span>
                                <span class="sidebar-badge px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 leading-none shrink-0">Selesai</span>
                            </a>
                            @else
                            <a href="{{ route('thesis-defense-applications.index') }}" 
                               data-tooltip="4. Pendaftaran Sidang" title="4. Pendaftaran Sidang"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('thesis-defense-applications.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('thesis-defense-applications.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <span class="sidebar-text">4. Pendaftaran Sidang</span>
                            </a>
                            @endif

                            @php
                                $hasDefenseRev = $thesis ? cache()->remember('u_' . Auth::id() . '_has_def_rev', 30, fn() => $thesis->hasPassedDefenseRevision()) : false;
                            @endphp
                            @if($hasDefenseRev)
                            <a href="{{ route('student-defense-revisions.index') }}" 
                               data-tooltip="5. Revisi Sidang (Selesai)" title="5. Revisi Sidang (Selesai - Klik untuk lihat detail)"
                               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm {{ request()->routeIs('student-defense-revisions.*') ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/40 ring-1 ring-emerald-400/30' : 'text-emerald-400/90 hover:text-emerald-300 bg-emerald-500/[0.06] hover:bg-emerald-500/10 border-emerald-500/20' }} border font-semibold transition-all">
                                <span class="flex items-center min-w-0">
                                    <svg aria-hidden="true" class="w-5 h-5 mr-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="sidebar-text truncate">5. Revisi Sidang</span>
                                </span>
                                <span class="sidebar-badge px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 leading-none shrink-0">Selesai</span>
                            </a>
                            @else
                            <a href="{{ route('student-defense-revisions.index') }}" 
                               data-tooltip="5. Revisi Sidang" title="5. Revisi Sidang"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('student-defense-revisions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('student-defense-revisions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span class="sidebar-text">5. Revisi Sidang</span>
                            </a>
                            @endif

                            @php
                                $hasGraduated = $thesis ? cache()->remember('u_' . Auth::id() . '_has_grad', 30, fn() => $thesis->isGraduated()) : false;
                            @endphp
                            @if($hasGraduated)
                            <a href="{{ route('student.graduation') }}" 
                               data-tooltip="6. Yudisium & SKL (Selesai)" title="6. Yudisium & SKL (Selesai - Klik untuk lihat detail)"
                               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm {{ request()->routeIs('student.graduation') ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/40 ring-1 ring-emerald-400/30' : 'text-emerald-400/90 hover:text-emerald-300 bg-emerald-500/[0.06] hover:bg-emerald-500/10 border-emerald-500/20' }} border font-semibold transition-all">
                                <span class="flex items-center min-w-0">
                                    <svg aria-hidden="true" class="w-5 h-5 mr-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="sidebar-text truncate">6. Yudisium & SKL</span>
                                </span>
                                <span class="sidebar-badge px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 leading-none shrink-0">Selesai</span>
                            </a>
                            @else
                            <a href="{{ route('student.graduation') }}" 
                               data-tooltip="6. Yudisium & SKL" title="6. Yudisium & SKL"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('student.graduation') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('student.graduation') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                <span class="sidebar-text">6. Yudisium & SKL</span>
                            </a>
                            @endif
                        </nav>

                        <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                            <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Aktivitas & Bimbingan</p>
                            <div class="sidebar-section-divider hidden w-6 h-0.5 bg-white/10 rounded-full mx-auto my-2" title="Aktivitas & Bimbingan"></div>
                        </div>
                        <nav class="space-y-1 mb-10">
                            <a href="{{ route('mentoring-sessions.index') }}" 
                               data-tooltip="Jadwal Bimbingan" title="Jadwal Bimbingan"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('mentoring-sessions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('mentoring-sessions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="sidebar-text">Jadwal Bimbingan</span>
                            </a>
                            <a href="{{ route('logbooks.index') }}" 
                               data-tooltip="Logbook Bimbingan" title="Logbook Bimbingan"
                               class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('logbooks.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('logbooks.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                <span class="sidebar-text">Logbook Bimbingan</span>
                            </a>
                        </nav>
                        @endif

                    <!-- Admin & Kaprodi Specific -->
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Pelaksanaan Skripsi</p>
                        <div class="sidebar-section-divider"></div>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <!-- Dropdown 1: Pengajuan & Bimbingan -->
                        @php $isBimbinganActive = request()->routeIs('theses.index') || request()->routeIs('advisor-decrees.*') || request()->routeIs('mentoring-sessions.*'); @endphp
                        <div x-data="{ open: {{ $isBimbinganActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Pengajuan & Bimbingan" title="Pengajuan & Bimbingan"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isBimbinganActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isBimbinganActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <span class="sidebar-text">Pengajuan & Bimbingan</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('theses.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('theses.index') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Pengajuan Judul
                                </a>
                                <a href="{{ route('advisor-decrees.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('advisor-decrees.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • SK Pembimbing
                                </a>
                                <a href="{{ route('mentoring-sessions.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('mentoring-sessions.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Bimbingan
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 2: Seminar Proposal -->
                        @php $isSeminarActive = request()->routeIs('seminar-applications.*') || request()->routeIs('seminar-schedules.*') || request()->routeIs('seminar-examiner.*'); @endphp
                        <div x-data="{ open: {{ $isSeminarActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Seminar Proposal" title="Seminar Proposal"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isSeminarActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isSeminarActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="sidebar-text">Seminar Proposal</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('seminar-applications.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-applications.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Validasi Pendaftaran
                                </a>
                                <a href="{{ route('seminar-schedules.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-schedules.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Seminar
                                </a>
                                <a href="{{ route('seminar-examiner.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-examiner.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Penguji Seminar
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 3: Sidang Skripsi -->
                        @php $isSidangActive = request()->routeIs('thesis-defense-applications.*') || request()->routeIs('thesis-defense-schedules.*') || request()->routeIs('defense-examiner.*'); @endphp
                        <div x-data="{ open: {{ $isSidangActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Sidang Skripsi" title="Sidang Skripsi"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isSidangActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isSidangActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <span class="sidebar-text">Sidang Skripsi</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('thesis-defense-applications.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('thesis-defense-applications.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Validasi Pendaftaran
                                </a>
                                <a href="{{ route('thesis-defense-schedules.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('thesis-defense-schedules.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Sidang
                                </a>
                                <a href="{{ route('defense-examiner.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('defense-examiner.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Penguji Sidang
                                </a>
                            </div>
                        </div>

                        <!-- Direct Link: Yudisium & SKL -->
                        @php $pendingGraduationsCount = \App\Models\Graduation::where('status', 'pending')->count(); @endphp
                        <a href="{{ route('graduations.index') }}" 
                           data-tooltip="Yudisium & SKL" title="Yudisium & SKL"
                           class="sidebar-link group flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('graduations.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('graduations.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                <span class="sidebar-text">Yudisium & SKL</span>
                            </div>
                            @if($pendingGraduationsCount > 0)
                                <span class="sidebar-badge px-2 py-0.5 text-[10px] font-black rounded-full bg-orange-500 text-white animate-pulse">
                                    {{ $pendingGraduationsCount }}
                                </span>
                            @endif
                        </a>
                    </nav>
                    @endif

                    <!-- Monitoring Section -->
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Monitoring & Laporan</p>
                        <div class="sidebar-section-divider"></div>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <!-- Dropdown 1: Monitoring Mahasiswa -->
                        @php 
                            $isMonitoringMhsActive = request()->routeIs('monitoring.index') || request()->routeIs('monitoring.weekly') || request()->routeIs('monitoring.activity*') || request()->routeIs('monitoring.uncompleted-mentoring*') || request()->routeIs('monitoring.revisions') || request()->routeIs('monitoring.defense-revisions') || request()->routeIs('monitoring.critical') || request()->routeIs('theses.unsubmitted-students'); 
                        @endphp
                        <div x-data="{ open: {{ $isMonitoringMhsActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Monitoring Mahasiswa" title="Monitoring Mahasiswa"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isMonitoringMhsActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isMonitoringMhsActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span class="sidebar-text">Monitoring Mahasiswa</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('monitoring.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ (request()->routeIs('monitoring.index') || request()->routeIs('monitoring.weekly') || request()->routeIs('monitoring.activity*') || request()->routeIs('monitoring.uncompleted-mentoring*')) ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Monitoring Bimbingan
                                </a>
                                <a href="{{ route('monitoring.revisions') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.revisions') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Revisi Seminar
                                </a>
                                <a href="{{ route('monitoring.defense-revisions') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.defense-revisions') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Revisi Sidang
                                </a>
                                <a href="{{ route('monitoring.critical') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.critical') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Monitoring Masa Studi
                                </a>
                                <a href="{{ route('theses.unsubmitted-students') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('theses.unsubmitted-students') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Belum Mengajukan Judul
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 2: Analitik & Evaluasi -->
                        @php 
                            $isAnalitikActive = request()->routeIs('analytics.*') || request()->routeIs('monitoring.defense-scores') || request()->routeIs('monitoring.advanced-reporting'); 
                        @endphp
                        <div x-data="{ open: {{ $isAnalitikActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Analitik & Evaluasi" title="Analitik & Evaluasi"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isAnalitikActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isAnalitikActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                    </svg>
                                    <span class="sidebar-text">Analitik & Evaluasi</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('analytics.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('analytics.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Grafik Analitik
                                </a>
                                <a href="{{ route('monitoring.defense-scores') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.defense-scores') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Nilai Sidang & Rekap
                                </a>
                                <a href="{{ route('monitoring.advanced-reporting') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('monitoring.advanced-reporting') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Statistik & Pelaporan
                                </a>
                            </div>
                        </div>
                    </nav>
                    @endif

                    <!-- Sistem & Konfigurasi Section -->
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Sistem & Konfigurasi</p>
                        <div class="sidebar-section-divider"></div>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <!-- Direct Link: Data Pengguna -->
                        <a href="{{ route('users.index') }}" 
                           data-tooltip="Data Pengguna" title="Data Pengguna"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="sidebar-text">Data Pengguna</span>
                        </a>

                        <!-- Dropdown 1: Pusat Informasi (Pengumuman & FAQ) -->
                        @php $isInfoActive = request()->routeIs('announcements.*') || request()->routeIs('faqs.manage'); @endphp
                        <div x-data="{ open: {{ $isInfoActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Pusat Informasi" title="Pusat Informasi"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isInfoActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isInfoActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                    </svg>
                                    <span class="sidebar-text">Pusat Informasi</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('announcements.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('announcements.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Pengumuman
                                </a>
                                <a href="{{ route('faqs.manage') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('faqs.manage') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Kelola FAQ
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 2: Konfigurasi Sistem -->
                        @php $isConfigActive = request()->routeIs('waves.*') || request()->routeIs('admin.letter-settings.*') || request()->routeIs('wa-templates.*') || request()->routeIs('wa-broadcasts.*'); @endphp
                        <div x-data="{ open: {{ $isConfigActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Konfigurasi Sistem" title="Konfigurasi Sistem"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isConfigActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isConfigActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                    <span class="sidebar-text">Konfigurasi Sistem</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('waves.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('waves.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Gelombang Pelaksanaan
                                </a>
                                <a href="{{ route('admin.letter-settings.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('admin.letter-settings.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Pengaturan Nomor Surat
                                </a>
                                <a href="{{ route('wa-templates.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('wa-templates.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Template WA
                                </a>
                                <a href="{{ route('wa-broadcasts.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('wa-broadcasts.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Broadcast WA
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown 3: Pemeliharaan Sistem (Backup, Log, Bug, Cache) -->
                        @php 
                            $openBugCount = cache()->remember('sidebar_open_bug_count', 30, function() {
                                try {
                                    if (\Illuminate\Support\Facades\Schema::hasTable('bug_reports')) {
                                        return \App\Models\BugReport::where('status', 'open')->count();
                                    }
                                } catch (\Throwable $e) {}
                                return 0;
                            });
                            $isMaintenanceActive = request()->routeIs('admin.backups.*') || request()->routeIs('admin.logs*') || request()->routeIs('admin.bug-reports.*');
                        @endphp
                        <div x-data="{ open: {{ $isMaintenanceActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Pemeliharaan Sistem" title="Pemeliharaan Sistem"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isMaintenanceActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isMaintenanceActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                                    </svg>
                                    <span class="sidebar-text">Pemeliharaan Sistem</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if($openBugCount > 0)
                                        <span class="sidebar-badge px-1.5 py-0.5 rounded-full text-[9px] font-black bg-rose-500 text-white leading-none">
                                            {{ $openBugCount }}
                                        </span>
                                    @endif
                                    <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('admin.backups.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('admin.backups.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Backup & Restore
                                </a>
                                <a href="{{ route('admin.logs') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('admin.logs*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Log Sistem
                                </a>
                                <a href="{{ route('admin.bug-reports.index') }}" 
                                   class="flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('admin.bug-reports.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    <span>• Laporan Bug</span>
                                    @if($openBugCount > 0)
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-rose-500 text-white leading-none">
                                            {{ $openBugCount }}
                                        </span>
                                    @endif
                                </a>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('system.clear-cache', ['migrate' => 1]) }}" 
                                   target="_blank"
                                   data-confirm="Bersihkan cache sistem dan jalankan migrasi database sekarang?"
                                   data-confirm-title="Bersihkan Cache &amp; Migrasi"
                                   data-confirm-type="warning"
                                   data-confirm-button="Ya, Jalankan"
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all text-slate-400 hover:text-amber-400 hover:bg-white/5 font-medium">
                                    • Bersihkan Cache
                                </a>
                                @endif
                            </div>
                        </div>
                    </nav>
                    @endif

                    <!-- Dosen Specific -->
                    @if(Auth::user()->role === 'dosen')
                    <div class="sidebar-section-header px-4 pt-12 pb-4 border-t border-white/[0.05] mt-10">
                        <p class="sidebar-section-title text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">Dokumentasi & Bimbingan</p>
                        <div class="sidebar-section-divider"></div>
                    </div>
                    <nav class="space-y-1 mb-10">
                        <a href="{{ route('theses.index') }}" 
                           data-tooltip="Mahasiswa Bimbingan" title="Mahasiswa Bimbingan"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ (request()->routeIs('theses.*') || request()->routeIs('logbooks.*')) ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ (request()->routeIs('theses.*') || request()->routeIs('logbooks.*')) ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="sidebar-text">Mahasiswa Bimbingan</span>
                        </a>
                        <a href="{{ route('mentoring-sessions.index') }}" 
                           data-tooltip="Jadwal Bimbingan" title="Jadwal Bimbingan"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('mentoring-sessions.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('mentoring-sessions.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="sidebar-text">Jadwal Bimbingan</span>
                        </a>

                        <!-- Direct Link: Surat Tugas Pembimbing (BKD / SISTER) -->
                        <a href="{{ route('advisor-decrees.surat-tugas') }}" 
                           data-tooltip="Surat Tugas" title="Surat Tugas"
                           class="sidebar-link group flex items-center px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('advisor-decrees.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold shadow-lg shadow-orange-900/20' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                            <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('advisor-decrees.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="sidebar-text">Surat Tugas</span>
                        </a>

                        <!-- Dropdown: Jadwal Ujian (Seminar & Sidang) -->
                        @php $isJadwalUjianActive = request()->routeIs('seminar-schedules.*') || request()->routeIs('thesis-defense-schedules.*'); @endphp
                        <div x-data="{ open: {{ $isJadwalUjianActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Jadwal Ujian" title="Jadwal Ujian"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isJadwalUjianActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isJadwalUjianActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="sidebar-text">Jadwal Ujian</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('seminar-schedules.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-schedules.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Seminar
                                </a>
                                <a href="{{ route('thesis-defense-schedules.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('thesis-defense-schedules.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Jadwal Sidang
                                </a>
                            </div>
                        </div>

                        <!-- Dropdown: Tugas Penguji (Seminar & Sidang) -->
                        @php $isTugasPengujiActive = request()->routeIs('seminar-examiner.*') || request()->routeIs('defense-examiner.*'); @endphp
                        <div x-data="{ open: {{ $isTugasPengujiActive ? 'true' : 'false' }} }">
                            <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; } open = !open" 
                                    data-tooltip="Tugas Penguji" title="Tugas Penguji"
                                    class="sidebar-link group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 {{ $isTugasPengujiActive ? 'text-white bg-white/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 transition-colors {{ $isTugasPengujiActive ? 'text-orange-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    <span class="sidebar-text">Tugas Penguji</span>
                                </div>
                                <svg class="sidebar-arrow w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="sidebar-dropdown-content pl-12 pr-2 py-1.5 space-y-1">
                                <a href="{{ route('seminar-examiner.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('seminar-examiner.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Penguji Seminar
                                </a>
                                <a href="{{ route('defense-examiner.index') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs transition-all {{ request()->routeIs('defense-examiner.*') ? 'text-orange-400 font-black bg-orange-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }}">
                                    • Penguji Sidang
                                </a>
                            </div>
                        </div>
                    </nav>
                    @endif
                </div>
            </aside>

<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Sistem & Konfigurasi', 'route' => null],
            ['label' => 'Laporan Bug Sistem', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6">
        
        <!-- Header Title & Quick Stats -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight flex items-center gap-3">
                    <span>Laporan Bug Sistem</span>
                    @if($stats['open'] > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-rose-500 text-white animate-pulse">
                            {{ $stats['open'] }} Baru
                        </span>
                    @endif
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Kelola dan tindak lanjuti laporan kendala teknis yang dilaporkan oleh Mahasiswa dan Dosen.
                </p>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <!-- Total -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Laporan</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total'] }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Semua waktu</div>
            </div>

            <!-- Menunggu Review (Open) -->
            <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/50 shadow-2xs">
                <div class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-widest">Menunggu Review</div>
                <div class="text-2xl font-black text-amber-800 dark:text-amber-300 mt-1">{{ $stats['open'] }}</div>
                <div class="text-[10px] text-amber-600/80 dark:text-amber-400/80 mt-0.5">Perlu ditanggapi</div>
            </div>

            <!-- Sedang Ditangani -->
            <div class="p-4 rounded-2xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-200/80 dark:border-blue-900/50 shadow-2xs">
                <div class="text-[10px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-widest">Sedang Diproses</div>
                <div class="text-2xl font-black text-blue-800 dark:text-blue-300 mt-1">{{ $stats['in_progress'] }}</div>
                <div class="text-[10px] text-blue-600/80 dark:text-blue-400/80 mt-0.5">Dalam investigasi</div>
            </div>

            <!-- Selesai (Resolved) -->
            <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-900/50 shadow-2xs">
                <div class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">Selesai / Teratasi</div>
                <div class="text-2xl font-black text-emerald-800 dark:text-emerald-300 mt-1">{{ $stats['resolved'] }}</div>
                <div class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80 mt-0.5">Kendala terselesaikan</div>
            </div>

            <!-- Urgensi Kritis -->
            <div class="p-4 rounded-2xl bg-rose-50/60 dark:bg-rose-950/20 border border-rose-200/80 dark:border-rose-900/50 shadow-2xs col-span-2 sm:col-span-1">
                <div class="text-[10px] font-bold text-rose-700 dark:text-rose-400 uppercase tracking-widest">Prioritas Kritis</div>
                <div class="text-2xl font-black text-rose-800 dark:text-rose-300 mt-1">{{ $stats['critical'] }}</div>
                <div class="text-[10px] text-rose-600/80 dark:text-rose-400/80 mt-0.5">Membutuhkan aksi segera</div>
            </div>
        </div>

        <!-- Filter & Search Bar Card -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
            <form method="GET" action="{{ route('admin.bug-reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                
                <!-- Keyword Search -->
                <div class="lg:col-span-2 relative">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari tiket, judul kendala, atau nama pelapor..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Filter Status -->
                <div>
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 text-xs font-semibold focus:ring-2 focus:ring-orange-500">
                        <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>🟡 Menunggu Review</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>🔵 Sedang Diproses</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>🟢 Selesai / Teratasi</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>⚪ Ditolak / Bukan Bug</option>
                    </select>
                </div>

                <!-- Filter Severity -->
                <div>
                    <select name="severity" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 text-xs font-semibold focus:ring-2 focus:ring-orange-500">
                        <option value="all" {{ request('severity') === 'all' || !request('severity') ? 'selected' : '' }}>Semua Urgensi</option>
                        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>🔴 Kritis (Fatal)</option>
                        <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>🟠 Tinggi (Mayor)</option>
                        <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>🟡 Sedang (Normal)</option>
                        <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>🟢 Rendah (Minor)</option>
                    </select>
                </div>

                <!-- Filter Role & Reset -->
                <div class="flex items-center gap-2">
                    <select name="role" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 text-xs font-semibold focus:ring-2 focus:ring-orange-500">
                        <option value="all" {{ request('role') === 'all' || !request('role') ? 'selected' : '' }}>Semua Pelapor</option>
                        <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                    </select>

                    @if(request()->anyFilled(['search', 'status', 'severity', 'role']))
                        <a href="{{ route('admin.bug-reports.index') }}" 
                           class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 text-xs font-bold transition-colors shrink-0"
                           title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <x-table-card title="Daftar Tiket Kendala Sistem" :footer="$bugReports->links()">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                        <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap">Tiket & Waktu</th>
                        <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap">Pelapor</th>
                        <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap">Kendala & Kategori</th>
                        <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap text-center">Tingkat Urgensi</th>
                        <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap text-center">Status</th>
                        <th class="py-4 px-6 font-black text-[10px] tracking-widest uppercase whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($bugReports as $report)
                        <tr class="bg-white dark:bg-slate-800 hover:bg-slate-50/80 dark:hover:bg-slate-900/50 transition-colors {{ request('highlight') == $report->id ? 'ring-2 ring-orange-500 bg-orange-50/20' : '' }}">
                            
                            <!-- Tiket & Waktu -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="font-mono text-xs font-bold text-orange-600 dark:text-orange-400">
                                    {{ $report->ticket_number }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $report->created_at->locale('id')->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Pelapor -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $report->user->avatar_url }}" alt="{{ $report->user->name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                    <div>
                                        <div class="font-bold text-xs text-slate-800 dark:text-slate-100">
                                            {{ $report->user->name }}
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[10px] font-semibold text-slate-400">
                                                {{ $report->user->identifier ?? $report->user->email }}
                                            </span>
                                            <span class="text-slate-300 dark:text-slate-700">•</span>
                                            <x-status-badge 
                                                :type="$report->user->role === 'dosen' ? 'indigo' : ($report->user->role === 'mahasiswa' ? 'emerald' : 'orange')" 
                                                :label="ucfirst($report->user->role)" 
                                                size="xs" />
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kendala & Kategori -->
                            <td class="py-4 px-6">
                                <div class="font-bold text-xs text-slate-800 dark:text-slate-100 line-clamp-1">
                                    {{ $report->title }}
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                                        {{ $report->category_label }}
                                    </span>
                                    @if($report->attachment_path)
                                        <span class="inline-flex items-center text-[10px] font-bold text-orange-600 dark:text-orange-400 gap-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            Ada Bukti
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Severity -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @php
                                    $severityType = match($report->severity) {
                                        'critical' => 'rose',
                                        'high' => 'orange',
                                        'medium' => 'amber',
                                        'low' => 'emerald',
                                        default => 'slate',
                                    };
                                @endphp
                                <x-status-badge :type="$severityType" :label="strtoupper($report->severity)" :pulse="$report->severity === 'critical' && in_array($report->status, ['open', 'in_progress'])" />
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @php
                                    $statusType = match($report->status) {
                                        'open' => 'amber',
                                        'in_progress' => 'blue',
                                        'resolved' => 'emerald',
                                        'rejected' => 'slate',
                                        default => 'slate',
                                    };
                                @endphp
                                <x-status-badge :type="$statusType" :label="$report->status_label" />
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" 
                                            onclick="window.openBugReviewModal({{ $report->id }}); event.stopPropagation();"
                                            class="cursor-pointer inline-flex items-center px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 active:scale-95 dark:bg-orange-950/60 dark:hover:bg-orange-900/60 text-orange-600 dark:text-orange-400 text-xs font-bold transition-all shadow-2xs">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        Tinjau & Tangani
                                    </button>

                                    @if(Auth::user()->role === 'admin')
                                        <form action="{{ route('admin.bug-reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Hapus laporan bug ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 transition-colors" title="Hapus Laporan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-2 text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Tidak ada laporan bug yang cocok.</p>
                                <p class="text-xs text-slate-400 mt-0.5">Sistem berjalan dengan baik dan lancar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-card>

        <!-- ========================================================================= -->
        <!-- DETAIL & REVIEW MODAL                                                     -->
        <!-- ========================================================================= -->
        <div id="bug-review-modal"
             class="fixed inset-0 overflow-y-auto"
             style="display: none; position: fixed !important; inset: 0 !important; z-index: 99999999 !important;">
            
            <!-- Backdrop -->
            <div onclick="window.closeBugReviewModal()"
                 class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity cursor-pointer"></div>

            <div class="min-h-screen px-4 py-8 flex items-center justify-center relative pointer-events-none">
                <div class="w-full max-w-3xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 flex flex-col max-h-[90vh] pointer-events-auto transition-all transform">
                    
                    <!-- Modal Header -->
                    <div class="px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-orange-50/60 to-transparent dark:from-orange-950/20 shrink-0">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span id="brm-ticket-number" class="text-xs font-mono font-bold text-orange-600 dark:text-orange-400"></span>
                                <span class="text-slate-300 dark:text-slate-700">•</span>
                                <span id="brm-created-at" class="text-xs text-slate-400"></span>
                            </div>
                            <h3 id="brm-title" class="text-lg font-black text-slate-900 dark:text-white"></h3>
                        </div>

                        <button onclick="window.closeBugReviewModal()"
                                type="button"
                                class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors cursor-pointer"
                                title="Tutup Modal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 sm:p-8 overflow-y-auto space-y-6 custom-scrollbar flex-1">
                        <!-- Pelapor & Metadata Pill Row -->
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <img id="brm-avatar" src="" alt="Avatar" class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                <div>
                                    <div id="brm-user-name" class="font-bold text-xs text-slate-800 dark:text-slate-100"></div>
                                    <div id="brm-user-meta" class="text-[11px] text-slate-400"></div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span id="brm-category" class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 shadow-2xs"></span>
                                <span id="brm-severity" class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider text-white"></span>
                            </div>
                        </div>

                        <!-- URL Lokasi Bug -->
                        <div id="brm-page-url-container" style="display: none;">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Halaman Kejadian (URL):</span>
                            <a id="brm-page-url" href="" target="_blank" class="text-xs font-mono text-orange-600 dark:text-orange-400 hover:underline break-all bg-slate-50 dark:bg-slate-800/80 p-2.5 rounded-xl block border border-slate-100 dark:border-slate-700">
                                <span class="page-url-text"></span>
                                <svg class="w-3.5 h-3.5 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            </a>
                        </div>

                        <!-- Uraian Masalah -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Uraian / Deskripsi Kendala:</span>
                            <div id="brm-description" class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-200 whitespace-pre-line leading-relaxed"></div>
                        </div>

                        <!-- Langkah Reproduksi -->
                        <div id="brm-steps-container" style="display: none;">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Langkah Memicu Masalah:</span>
                            <div id="brm-steps" class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-200 whitespace-pre-line leading-relaxed font-mono"></div>
                        </div>

                        <!-- Lampiran Screenshot / File Bukti -->
                        <div id="brm-attachment-container" style="display: none;">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Bukti Tangkapan Layar / Dokumen:</span>
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 text-center">
                                
                                <!-- Image Preview Wrapper -->
                                <div id="brm-image-wrapper" class="relative group inline-block max-w-full">
                                    <a id="brm-attachment-link" href="" target="_blank" class="block" title="Klik untuk membuka ukuran penuh di tab baru">
                                        <img id="brm-attachment-img" 
                                             src="" 
                                             alt="Screenshot" 
                                             class="max-h-80 mx-auto rounded-xl object-contain border border-slate-200 dark:border-slate-700 shadow-sm hover:scale-[1.01] transition-transform cursor-zoom-in bg-white dark:bg-slate-900"
                                             onerror="window.handleAttachmentImageError(this)">
                                    </a>
                                </div>

                                <!-- PDF Document Card (If PDF) -->
                                <div id="brm-pdf-wrapper" style="display: none;" class="py-2">
                                    <a id="brm-pdf-link" href="" target="_blank" class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:border-orange-400 dark:hover:border-orange-500 hover:shadow-md transition-all group">
                                        <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-xs uppercase">
                                            PDF
                                        </div>
                                        <div class="text-left">
                                            <div class="text-xs font-bold text-slate-800 dark:text-slate-100 group-hover:text-orange-600 transition-colors" id="brm-pdf-name">Dokumen Lampiran.pdf</div>
                                            <div class="text-[11px] text-slate-400">Klik untuk membuka atau mengunduh PDF di tab baru</div>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 group-hover:translate-x-0.5 transition-all ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </a>
                                </div>

                                <!-- Fallback if image failed to load -->
                                <div id="brm-attachment-fallback" style="display: none;" class="py-2 text-center">
                                    <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 text-xs font-medium">
                                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        <span>Preview gambar tidak dapat dimuat langsung. <a id="brm-fallback-link" href="" target="_blank" class="underline font-bold text-orange-600 dark:text-orange-400">Buka / Unduh Berkas Lampiran</a></span>
                                    </div>
                                </div>

                                <p id="brm-image-hint" class="text-[11px] text-slate-400 mt-2">Klik gambar untuk membuka ukuran penuh di tab baru.</p>
                            </div>
                        </div>

                        <!-- Device Info -->
                        <div id="brm-device-container" style="display: none;" class="text-[10px] text-slate-400 font-mono">
                            <span class="font-bold">Info Perangkat:</span> <span id="brm-device"></span>
                        </div>

                        <!-- Form Update Status & Tanggapan Admin -->
                        <div class="pt-6 border-t border-slate-200 dark:border-slate-700 space-y-4">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Tindak Lanjut & Tanggapan Admin/Kaprodi
                            </h4>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                    Perbarui Status Tiket:
                                </label>
                                <select id="brm-status-select" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs font-bold focus:ring-2 focus:ring-orange-500">
                                    <option value="open">🟡 Menunggu Review (Open)</option>
                                    <option value="in_progress">🔵 Sedang Ditangani (In Progress)</option>
                                    <option value="resolved">🟢 Selesai / Teratasi (Resolved)</option>
                                    <option value="rejected">⚪ Ditolak / Bukan Bug (Rejected)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                    Catatan Solusi / Tanggapan Balik ke Pelapor:
                                </label>
                                <textarea id="brm-admin-notes"
                                          rows="3"
                                          placeholder="Tuliskan catatan perbaikan atau petunjuk kepada pelapor (catatan ini dapat dibaca oleh mahasiswa/dosen pelapor)..."
                                          class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-orange-500"></textarea>
                            </div>

                            <div id="brm-feedback" style="display: none;" class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-semibold"></div>
                        </div>
                    </div>

                    <!-- Modal Footer Actions -->
                    <div class="p-4 px-6 sm:px-8 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                        <button type="button"
                                onclick="window.closeBugReviewModal()"
                                class="px-5 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                            Tutup
                        </button>

                        <button type="button"
                                id="brm-save-btn"
                                onclick="window.saveBugStatusUpdate()"
                                class="px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 active:scale-95 text-white text-xs font-black uppercase tracking-wider shadow-md shadow-orange-600/20 flex items-center gap-2 transition-all cursor-pointer">
                            <svg id="brm-save-spinner" style="display: none;" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="brm-save-text">Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    window._activeBugReportId = null;

    window.openBugReviewModal = async function(reportId) {
        window._activeBugReportId = reportId;
        const modal = document.getElementById('bug-review-modal');
        if (!modal) {
            console.error('Modal #bug-review-modal not found');
            return;
        }

        // Show immediately
        modal.style.setProperty('display', 'block', 'important');
        document.body.style.overflow = 'hidden';

        const feedbackEl = document.getElementById('brm-feedback');
        if (feedbackEl) feedbackEl.style.display = 'none';

        document.getElementById('brm-ticket-number').innerText = 'Memuat...';
        document.getElementById('brm-created-at').innerText = '';
        document.getElementById('brm-title').innerText = 'Mengambil rincian laporan bug...';
        document.getElementById('brm-description').innerText = 'Mohon tunggu sebentar...';
        document.getElementById('brm-status-select').value = 'open';
        document.getElementById('brm-admin-notes').value = '';

        try {
            const res = await fetch(`/bug-reports/${reportId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            const data = await res.json();
            if (data.success && data.report) {
                const r = data.report;
                document.getElementById('brm-ticket-number').innerText = r.ticket_number || ('#' + r.id);
                document.getElementById('brm-created-at').innerText = r.created_at || '';
                document.getElementById('brm-title').innerText = r.title || 'Laporan Bug';

                if (r.user) {
                    document.getElementById('brm-avatar').src = r.user.avatar_url || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(r.user.name || 'User'));
                    document.getElementById('brm-user-name').innerText = r.user.name || 'Pelapor';
                    document.getElementById('brm-user-meta').innerText = (r.user.identifier || r.user.email || '') + ' (' + (r.user.role || '') + ')';
                }

                document.getElementById('brm-category').innerText = 'Kategori: ' + (r.category_label || r.category || '-');
                
                const sevEl = document.getElementById('brm-severity');
                sevEl.innerText = 'Urgensi: ' + (r.severity_label || r.severity || '-');
                sevEl.className = 'px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider text-white ' + 
                    (r.severity === 'critical' ? 'bg-rose-600' :
                     r.severity === 'high' ? 'bg-orange-500' :
                     r.severity === 'medium' ? 'bg-amber-500' : 'bg-emerald-600');

                const pageUrlContainer = document.getElementById('brm-page-url-container');
                const pageUrlLink = document.getElementById('brm-page-url');
                if (r.page_url) {
                    pageUrlContainer.style.display = 'block';
                    pageUrlLink.href = r.page_url;
                    pageUrlLink.querySelector('.page-url-text').innerText = r.page_url;
                } else {
                    pageUrlContainer.style.display = 'none';
                }

                document.getElementById('brm-description').innerText = r.description || '-';

                const stepsContainer = document.getElementById('brm-steps-container');
                if (r.steps_to_reproduce) {
                    stepsContainer.style.display = 'block';
                    document.getElementById('brm-steps').innerText = r.steps_to_reproduce;
                } else {
                    stepsContainer.style.display = 'none';
                }

                const attachContainer = document.getElementById('brm-attachment-container');
                const imgWrapper = document.getElementById('brm-image-wrapper');
                const pdfWrapper = document.getElementById('brm-pdf-wrapper');
                const fallbackEl = document.getElementById('brm-attachment-fallback');
                const imgHint = document.getElementById('brm-image-hint');

                if (r.attachment_url) {
                    attachContainer.style.display = 'block';
                    if (fallbackEl) fallbackEl.style.display = 'none';

                    const isPdf = r.is_attachment_image === false || (r.attachment_filename && r.attachment_filename.toLowerCase().endsWith('.pdf'));

                    if (isPdf) {
                        imgWrapper.style.display = 'none';
                        if (imgHint) imgHint.style.display = 'none';
                        pdfWrapper.style.display = 'block';
                        document.getElementById('brm-pdf-link').href = r.attachment_url;
                        document.getElementById('brm-pdf-name').innerText = r.attachment_filename || 'Dokumen Lampiran.pdf';
                    } else {
                        pdfWrapper.style.display = 'none';
                        imgWrapper.style.display = 'inline-block';
                        if (imgHint) imgHint.style.display = 'block';
                        
                        const imgEl = document.getElementById('brm-attachment-img');
                        imgEl.style.display = 'block';
                        imgEl.src = r.attachment_url;
                        document.getElementById('brm-attachment-link').href = r.attachment_url;
                        const fbLink = document.getElementById('brm-fallback-link');
                        if (fbLink) fbLink.href = r.attachment_url;
                    }
                } else {
                    attachContainer.style.display = 'none';
                }

                const deviceContainer = document.getElementById('brm-device-container');
                if (r.device_info) {
                    deviceContainer.style.display = 'block';
                    document.getElementById('brm-device').innerText = r.device_info;
                } else {
                    deviceContainer.style.display = 'none';
                }

                document.getElementById('brm-status-select').value = r.status || 'open';
                document.getElementById('brm-admin-notes').value = r.admin_notes || '';
            }
        } catch (err) {
            console.error('Error loading bug report:', err);
            document.getElementById('brm-title').innerText = 'Gagal memuat rincian laporan';
            document.getElementById('brm-description').innerText = 'Terjadi kesalahan jaringan atau izin akses tidak mencukupi.';
        }
    };

    window.handleAttachmentImageError = function(img) {
        if (img) img.style.display = 'none';
        const fallback = document.getElementById('brm-attachment-fallback');
        if (fallback) fallback.style.display = 'block';
        const hint = document.getElementById('brm-image-hint');
        if (hint) hint.style.display = 'none';
    };

    window.closeBugReviewModal = function() {
        const modal = document.getElementById('bug-review-modal');
        if (modal) {
            modal.style.setProperty('display', 'none', 'important');
        }
        document.body.style.overflow = '';
    };

    window.saveBugStatusUpdate = async function() {
        if (!window._activeBugReportId) return;

        const saveBtn = document.getElementById('brm-save-btn');
        const spinner = document.getElementById('brm-save-spinner');
        const btnText = document.getElementById('brm-save-text');
        const feedbackEl = document.getElementById('brm-feedback');

        saveBtn.disabled = true;
        spinner.style.display = 'inline-block';
        btnText.innerText = 'Menyimpan...';
        feedbackEl.style.display = 'none';

        try {
            const statusVal = document.getElementById('brm-status-select').value;
            const notesVal = document.getElementById('brm-admin-notes').value;

            const res = await fetch(`/admin/bug-reports/${window._activeBugReportId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    status: statusVal,
                    admin_notes: notesVal,
                }),
            });

            const data = await res.json();
            if (data.success) {
                feedbackEl.className = 'p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-semibold';
                feedbackEl.innerText = 'Perubahan status dan tanggapan berhasil disimpan! Memuat ulang...';
                feedbackEl.style.display = 'block';
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                throw new Error(data.message || 'Gagal menyimpan status');
            }
        } catch (err) {
            console.error('Error updating status:', err);
            feedbackEl.className = 'p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 text-xs font-semibold';
            feedbackEl.innerText = 'Gagal menyimpan perubahan: ' + (err.message || 'Terjadi kesalahan sistem.');
            feedbackEl.style.display = 'block';
        } finally {
            saveBtn.disabled = false;
            spinner.style.display = 'none';
            btnText.innerText = 'Simpan Perubahan';
        }
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeBugReviewModal();
        }
    });

    function bugManagement() {
        return {
            openReviewModal(id) {
                window.openBugReviewModal(id);
            }
        };
    }
    </script>
</x-app-layout>

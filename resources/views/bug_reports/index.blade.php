<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Sistem & Konfigurasi', 'route' => null],
            ['label' => 'Laporan Bug Sistem', 'route' => null]
        ]" />
    </x-slot>

    <div x-data="bugManagement()" class="w-full space-y-6">
        
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
                                            @click="openReviewModal({{ $report->id }})"
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/60 dark:hover:bg-orange-900/60 text-orange-600 dark:text-orange-400 text-xs font-bold transition-all shadow-2xs">
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
        <!-- DETAIL & REVIEW MODAL (TELEPORTED TO BODY)                                -->
        <!-- ========================================================================= -->
        <template x-teleport="body">
            <div x-show="showModal"
                 x-cloak
                 class="fixed inset-0 overflow-y-auto"
                 style="z-index: 999999 !important;">
                
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showModal = false"
                     class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs"></div>

                <div class="min-h-screen px-4 py-8 flex items-center justify-center">
                    <div x-show="showModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                         class="w-full max-w-3xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative z-10 flex flex-col max-h-[90vh]">
                        
                        <!-- Modal Header -->
                        <div class="px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-orange-50/60 to-transparent dark:from-orange-950/20 shrink-0">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-mono font-bold text-orange-600 dark:text-orange-400" x-text="activeReport?.ticket_number"></span>
                                    <span class="text-slate-300 dark:text-slate-700">•</span>
                                    <span class="text-xs text-slate-400" x-text="activeReport?.created_at"></span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white" x-text="activeReport?.title"></h3>
                            </div>

                            <button @click="showModal = false"
                                    type="button"
                                    class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 sm:p-8 overflow-y-auto space-y-6 custom-scrollbar flex-1">
                            <!-- Pelapor & Metadata Pill Row -->
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <img :src="activeReport?.user?.avatar_url" alt="" class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                    <div>
                                        <div class="font-bold text-xs text-slate-800 dark:text-slate-100" x-text="activeReport?.user?.name"></div>
                                        <div class="text-[11px] text-slate-400" x-text="(activeReport?.user?.identifier || activeReport?.user?.email) + ' (' + activeReport?.user?.role + ')'"></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 shadow-2xs" x-text="'Kategori: ' + activeReport?.category_label"></span>
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider text-white"
                                          :class="{
                                              'bg-rose-600': activeReport?.severity === 'critical',
                                              'bg-orange-500': activeReport?.severity === 'high',
                                              'bg-amber-500': activeReport?.severity === 'medium',
                                              'bg-emerald-600': activeReport?.severity === 'low',
                                          }"
                                          x-text="'Urgensi: ' + activeReport?.severity_label"></span>
                                </div>
                            </div>

                            <!-- URL Lokasi Bug -->
                            <template x-if="activeReport?.page_url">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Halaman Kejadian (URL):</span>
                                    <a :href="activeReport?.page_url" target="_blank" class="text-xs font-mono text-orange-600 dark:text-orange-400 hover:underline break-all bg-slate-50 dark:bg-slate-800/80 p-2.5 rounded-xl block border border-slate-100 dark:border-slate-700">
                                        <span x-text="activeReport?.page_url"></span>
                                        <svg class="w-3.5 h-3.5 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </a>
                                </div>
                            </template>

                            <!-- Uraian Masalah -->
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Uraian / Deskripsi Kendala:</span>
                                <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-200 whitespace-pre-line leading-relaxed" x-text="activeReport?.description"></div>
                            </div>

                            <!-- Langkah Reproduksi -->
                            <template x-if="activeReport?.steps_to_reproduce">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Langkah Memicu Masalah:</span>
                                    <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-200 whitespace-pre-line leading-relaxed font-mono" x-text="activeReport?.steps_to_reproduce"></div>
                                </div>
                            </template>

                            <!-- Lampiran Screenshot -->
                            <template x-if="activeReport?.attachment_url">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Bukti Tangkapan Layar:</span>
                                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 text-center">
                                        <a :href="activeReport?.attachment_url" target="_blank" title="Klik untuk membuka ukuran penuh">
                                            <img :src="activeReport?.attachment_url" alt="Screenshot" class="max-h-72 mx-auto rounded-xl object-contain border border-slate-200 dark:border-slate-700 shadow-sm hover:scale-[1.01] transition-transform cursor-zoom-in">
                                        </a>
                                        <p class="text-[11px] text-slate-400 mt-2">Klik gambar untuk membuka ukuran penuh di tab baru.</p>
                                    </div>
                                </div>
                            </template>

                            <!-- Device Info -->
                            <template x-if="activeReport?.device_info">
                                <div class="text-[10px] text-slate-400 font-mono">
                                    <span class="font-bold">Info Perangkat:</span> <span x-text="activeReport?.device_info"></span>
                                </div>
                            </template>

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
                                    <select x-model="statusUpdate.status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs font-bold focus:ring-2 focus:ring-orange-500">
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
                                    <textarea x-model="statusUpdate.admin_notes"
                                              rows="3"
                                              placeholder="Tuliskan catatan perbaikan atau petunjuk kepada pelapor (catatan ini dapat dibaca oleh mahasiswa/dosen pelapor)..."
                                              class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-orange-500"></textarea>
                                </div>

                                <div x-show="updateFeedback" x-cloak class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-semibold" x-text="updateFeedback"></div>
                            </div>
                        </div>

                        <!-- Modal Footer Actions -->
                        <div class="p-4 px-6 sm:px-8 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                            <button type="button"
                                    @click="showModal = false"
                                    class="px-5 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-700 transition-colors">
                                Tutup
                            </button>

                            <button type="button"
                                    @click="saveStatusUpdate()"
                                    :disabled="isUpdating"
                                    class="px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-wider shadow-md shadow-orange-600/20 flex items-center gap-2 disabled:opacity-50 transition-all cursor-pointer">
                                <svg x-show="isUpdating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isUpdating ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </div>

    <script>
    function bugManagement() {
        return {
            showModal: false,
            isUpdating: false,
            updateFeedback: '',
            activeReport: null,
            statusUpdate: {
                status: 'open',
                admin_notes: '',
            },

            async openReviewModal(reportId) {
                this.updateFeedback = '';
                this.showModal = true;
                
                try {
                    const res = await fetch(`/bug-reports/${reportId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.activeReport = data.report;
                        this.statusUpdate.status = data.report.status;
                        this.statusUpdate.admin_notes = data.report.admin_notes || '';
                    }
                } catch (err) {
                    console.error('Error fetching bug report details:', err);
                }
            },

            async saveStatusUpdate() {
                if (!this.activeReport) return;

                this.isUpdating = true;
                this.updateFeedback = '';

                try {
                    const res = await fetch(`/admin/bug-reports/${this.activeReport.id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            status: this.statusUpdate.status,
                            admin_notes: this.statusUpdate.admin_notes,
                        }),
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.updateFeedback = 'Perubahan status dan tanggapan berhasil disimpan! Halaman akan dimuat ulang.';
                        setTimeout(() => {
                            window.location.reload();
                        }, 900);
                    }
                } catch (err) {
                    console.error('Error updating status:', err);
                    alert('Gagal memperbarui status laporan.');
                } finally {
                    this.isUpdating = false;
                }
            }
        };
    }
    </script>
</x-app-layout>

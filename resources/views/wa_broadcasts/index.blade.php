<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <x-breadcrumb :items="[
                ['label' => 'Konfigurasi Sistem', 'route' => null],
                ['label' => 'Broadcast WhatsApp', 'route' => null]
            ]" />
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Hero Banner with Stats -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
            <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-2xl space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30">
                            <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                            Targeted Audience Blaster
                        </span>
                        @if(!$isWhatsAppEnabled)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                Gateway WA Dinonaktifkan
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">
                        Broadcast WhatsApp Terarah
                    </h1>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Kirim pesan siaran massal dengan filter sasaran khusus (mahasiswa belum seminar, bimbingan mangkir, semester kritis, atau dosen pembimbing). Dilengkapi jeda acak anti-blokir nomor dan variabel nama dinamis.
                    </p>
                </div>

                <!-- Fast Stats Counter -->
                <div class="grid grid-cols-3 gap-3 shrink-0">
                    <div class="bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/10 text-center">
                        <span class="text-2xl font-black text-white block">{{ number_format($totalBroadcasts) }}</span>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mt-1">Total Siaran</span>
                    </div>
                    <div class="bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/10 text-center">
                        <span class="text-2xl font-black text-emerald-400 block">{{ number_format($totalSent) }}</span>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mt-1">Pesan Terkirim</span>
                    </div>
                    <div class="bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/10 text-center">
                        <span class="text-2xl font-black {{ $totalFailed > 0 ? 'text-rose-400' : 'text-slate-400' }} block">{{ number_format($totalFailed) }}</span>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mt-1">Gagal / Dilewati</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Broadcast History List -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="p-5 md:p-6 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white">Riwayat Siaran WhatsApp</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar seluruh siaran terarah yang telah diproses oleh Program Studi & Admin.</p>
                </div>

                <a href="{{ route('wa-broadcasts.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-orange-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Kirim Pesan Siaran Baru</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5">Waktu & Pengirim</th>
                            <th class="px-5 py-3.5">Judul & Filter Sasaran</th>
                            <th class="px-5 py-3.5 text-center">Sasaran</th>
                            <th class="px-5 py-3.5 text-center">Status Pengiriman</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($broadcasts as $bc)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $bc->created_at->locale('id')->translatedFormat('d M Y, H:i') }} WIB
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span>{{ $bc->sender->name ?? 'Admin' }}</span>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-black text-slate-900 dark:text-white line-clamp-1">
                                        {{ $bc->title }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 dark:bg-orange-950/50 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800">
                                            {{ $bc->target_type_label }}
                                        </span>
                                        @if(isset($bc->target_filter['cohort']) && $bc->target_filter['cohort'] !== 'all')
                                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                                                Angkatan: {{ $bc->target_filter['cohort'] }}
                                            </span>
                                        @endif
                                        @if(isset($bc->target_filter['days']))
                                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                                                > {{ $bc->target_filter['days'] }} Hari
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    <div class="font-black text-slate-900 dark:text-white text-sm">
                                        {{ $bc->total_recipients }} <span class="text-[11px] font-normal text-slate-500">Kontak</span>
                                    </div>
                                    <div class="flex items-center justify-center gap-1.5 text-[10px] font-bold mt-1">
                                        <span class="text-emerald-600 dark:text-emerald-400">{{ $bc->successful_count }} Sukses</span>
                                        @if($bc->failed_count > 0)
                                            <span class="text-slate-300 dark:text-slate-600">•</span>
                                            <span class="text-rose-600 dark:text-rose-400">{{ $bc->failed_count }} Gagal</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    @if($bc->status === 'completed')
                                        @if($bc->failed_count == 0)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Selesai 100%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Sebagian Terkirim
                                            </span>
                                        @endif
                                    @elseif($bc->status === 'processing')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            Sedang Mengirim...
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                            {{ ucfirst($bc->status) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('wa-broadcasts.show', $bc) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-bold transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            <span>Detail Laporan</span>
                                        </a>

                                        @if($bc->failed_count > 0)
                                            <form action="{{ route('wa-broadcasts.resend-failed', $bc) }}" method="POST" onsubmit="return confirm('Kirim ulang pesan hanya ke kontak yang gagal sebelumnya?');">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition-colors cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    <span>Kirim Ulang Gagal</span>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('wa-broadcasts.destroy', $bc) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siaran ini beserta seluruh log pengirimannya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Riwayat Siaran" class="inline-flex items-center justify-center p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                    </div>
                                    <p class="font-bold text-slate-700 dark:text-slate-300">Belum Ada Riwayat Siaran</p>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Mulai buat pesan siaran terarah pertama Anda untuk mengabarkan mahasiswa atau dosen secara proaktif.</p>
                                    <a href="{{ route('wa-broadcasts.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-orange-600/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        <span>Buat Broadcast Pertama</span>
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($broadcasts->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $broadcasts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

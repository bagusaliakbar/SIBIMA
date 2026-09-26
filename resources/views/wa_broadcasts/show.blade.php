<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <x-breadcrumb :items="[
                ['label' => 'Broadcast WhatsApp', 'route' => route('wa-broadcasts.index')],
                ['label' => 'Laporan Pengiriman', 'route' => null]
            ]" />

            <div class="flex items-center gap-2">
                <a href="{{ route('wa-broadcasts.index') }}" 
                   class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700">
                    &larr; Riwayat Siaran
                </a>

                <a href="{{ route('wa-broadcasts.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-orange-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Buat Siaran Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Header Banner & Summary -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-sm space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-orange-100 dark:bg-orange-950 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800">
                            {{ $waBroadcast->target_type_label }}
                        </span>

                        @if($waBroadcast->status === 'completed')
                            @if($waBroadcast->failed_count == 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Selesai 100%
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    Sebagian Terkirim
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                {{ ucfirst($waBroadcast->status) }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                        {{ $waBroadcast->title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $waBroadcast->created_at->locale('id')->translatedFormat('l, d F Y - H:i') }} WIB
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Pengirim: <strong class="text-slate-700 dark:text-slate-200">{{ $waBroadcast->sender->name ?? 'Admin' }}</strong>
                        </span>
                        <span>•</span>
                        <span>Jeda Anti-Spam: <strong>{{ $waBroadcast->delay_seconds }} detik/pesan</strong></span>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-3 gap-3 shrink-0">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 text-center">
                        <span class="text-2xl font-black text-slate-900 dark:text-white block">{{ $waBroadcast->total_recipients }}</span>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mt-1">Total Sasaran</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center">
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 block">{{ $waBroadcast->successful_count }}</span>
                        <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider block mt-1">Berhasil</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-center">
                        <span class="text-2xl font-black {{ $waBroadcast->failed_count > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }} block">{{ $waBroadcast->failed_count }}</span>
                        <span class="text-[10px] font-bold text-rose-700 dark:text-rose-300 uppercase tracking-wider block mt-1">Gagal / Lewat</span>
                    </div>
                </div>
            </div>

            <!-- Message Template Preview Accordion -->
            <div x-data="{ showTemplate: false }" class="border border-slate-200 dark:border-slate-700 rounded-2xl p-4 bg-slate-50/60 dark:bg-slate-800/40">
                <button type="button" @click="showTemplate = !showTemplate" class="w-full flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                    <span class="flex items-center gap-2">
                        <span>💬</span> Format Pesan Asli yang Dikirimkan
                    </span>
                    <span class="text-orange-600 dark:text-orange-400" x-text="showTemplate ? 'Tutup Pratinjau' : 'Lihat Teks Template'"></span>
                </button>
                <div x-show="showTemplate" x-cloak class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                    <pre class="font-mono text-xs text-slate-800 dark:text-slate-200 whitespace-pre-wrap bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 leading-relaxed">{{ $waBroadcast->message_template }}</pre>
                </div>
            </div>
        </div>

        <!-- Recipient Delivery Logs Table -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm overflow-hidden">
            <div class="p-5 md:p-6 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Log Pengiriman per Kontak</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Rincian status pengiriman untuk setiap penerima pada siaran ini.</p>
                </div>

                @if($waBroadcast->failed_count > 0)
                    <form action="{{ route('wa-broadcasts.resend-failed', $waBroadcast) }}" method="POST" onsubmit="return confirm('Kirim ulang pesan ke kontak yang berstatus gagal?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-rose-600/20 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Kirim Ulang {{ $waBroadcast->failed_count }} Pesan Gagal</span>
                        </button>
                    </form>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5 w-12 text-center">No</th>
                            <th class="px-5 py-3.5">Penerima</th>
                            <th class="px-5 py-3.5">Nomor WhatsApp</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5">Waktu Kirim / Alasan</th>
                            <th class="px-5 py-3.5 text-right">Isi Pesan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($logs as $index => $log)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors" x-data="{ showMsg: false }">
                                <td class="px-5 py-4 text-center text-slate-400 font-medium">
                                    {{ $logs->firstItem() + $index }}
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $log->recipient_name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $log->recipient_identifier ?? '-' }}</div>
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap font-mono text-[11px]">
                                    @if($log->recipient_phone)
                                        <span class="text-slate-700 dark:text-slate-300 font-bold">📱 {{ $log->recipient_phone }}</span>
                                    @else
                                        <span class="text-rose-500 font-bold text-[10px] bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded">
                                            Nomor Kosong
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    @if($log->status === 'sent')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Terkirim
                                        </span>
                                    @elseif($log->status === 'failed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Gagal
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                            Menunggu
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-xs">
                                    @if($log->status === 'sent')
                                        <span class="text-slate-600 dark:text-slate-300">
                                            {{ $log->sent_at ? $log->sent_at->locale('id')->translatedFormat('d M Y, H:i:s') : '-' }} WIB
                                        </span>
                                    @else
                                        <span class="text-rose-600 dark:text-rose-400 font-medium text-[11px]">
                                            {{ $log->error_message ?? 'Gagal mengirim pesan.' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <button type="button" @click="showMsg = !showMsg" 
                                            class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                                        <span x-text="showMsg ? 'Tutup' : 'Lihat Pesan'"></span>
                                    </button>

                                    <!-- Rendered personalized message viewer -->
                                    <div x-show="showMsg" x-cloak class="mt-2 text-left bg-slate-900 text-slate-100 p-3 rounded-xl font-mono text-[11px] whitespace-pre-wrap max-w-md ml-auto shadow-lg border border-slate-700">
                                        {{ $log->message_content }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    Belum ada log penerima untuk siaran ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

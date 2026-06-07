<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Revisi Hasil Sidang', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Revisi Hasil Sidang
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-table-card 
            title="Daftar Revisi Sidang">
            
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-50 dark:border-slate-700">
                        <th class="px-6 py-4">Dosen Penguji</th>
                        <th class="px-6 py-4 text-center">Tgl Sidang</th>
                        <th class="px-6 py-4">Catatan Revisi</th>
                        <th class="px-6 py-4 text-center">Status Revisi</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($revisions as $revision)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 flex items-center justify-center mr-4 font-black text-sm border border-rose-100 dark:border-rose-500/20 shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr($revision->examiner->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-800 dark:text-slate-100 text-sm group-hover:text-rose-600 transition-colors uppercase tracking-tight">{{ $revision->examiner->name }}</h4>
                                        @if($revision->detail->thesis->pembimbing1_id === $revision->examiner_id)
                                            <p class="text-[10px] text-rose-500 mt-1 font-black uppercase tracking-tighter">Pembimbing 1</p>
                                        @elseif($revision->detail->thesis->pembimbing2_id === $revision->examiner_id)
                                            <p class="text-[10px] text-rose-500 mt-1 font-black uppercase tracking-tighter">Pembimbing 2</p>
                                        @elseif($revision->detail->examiner1_id === $revision->examiner_id)
                                            <p class="text-[10px] text-slate-400 mt-1 uppercase font-black tracking-tighter">Penguji 1</p>
                                        @elseif($revision->detail->examiner2_id === $revision->examiner_id)
                                            <p class="text-[10px] text-slate-400 mt-1 uppercase font-black tracking-tighter">Penguji 2</p>
                                        @else
                                            <p class="text-[10px] text-slate-400 mt-1 uppercase font-black tracking-tighter">Penguji</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase">{{ \Carbon\Carbon::parse($revision->detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-5">
                                @if($revision->messages->count() > 0)
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 line-clamp-1 font-bold uppercase tracking-tighter italic">"{{ Str::limit($revision->messages->first()->message, 50) }}"</p>
                                @else
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">Belum ada catatan</p>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    @if($revision->status === 'approved')
                                        <x-status-badge type="emerald" label="REVISI SELESAI" />
                                        <p class="text-[8px] text-slate-400 font-black uppercase tracking-tighter">Telah Disetujui</p>
                                    @elseif($revision->resubmitted_at)
                                        <x-status-badge type="blue" label="TERKIRIM" />
                                        <p class="text-[8px] text-slate-400 font-black uppercase tracking-tighter">{{ \Carbon\Carbon::parse($revision->resubmitted_at)->diffForHumans() }}</p>
                                    @else
                                        <x-status-badge type="amber" label="BELUM SELESAI" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <a href="{{ route('student-defense-revisions.show', $revision->id) }}" class="inline-flex items-center px-4 py-2 bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-lg shadow-rose-500/20">
                                    Detail Revisi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="5" description="Dosen penguji belum mengunggah catatan revisi untuk sidang Anda." icon="revision" />
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>
</x-app-layout>

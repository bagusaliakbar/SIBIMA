<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Revisi Hasil Seminar', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Revisi Hasil Seminar
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-table-card 
            title="Daftar Revisi Seminar">
            
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-50 dark:border-slate-700">
                        <th class="px-6 py-4">Dosen Penguji</th>
                        <th class="px-6 py-4 text-center">Tgl Seminar</th>
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
                                    <div class="w-10 h-10 rounded-xl overflow-hidden mr-4 border border-slate-200 dark:border-slate-700 shadow-sm group-hover:scale-110 transition-transform flex items-center justify-center bg-slate-50 dark:bg-slate-800">
                                        <img src="{{ $revision->examiner->avatar_url }}" alt="{{ $revision->examiner->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-800 dark:text-slate-100 text-sm group-hover:text-orange-600 transition-colors uppercase tracking-tight">{{ $revision->examiner->name }}</h4>
                                        @if($revision->detail->examiner1_id === $revision->examiner_id)
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
                                    @if($revision->resubmitted_at)
                                        <x-status-badge type="emerald" label="REVISI SELESAI" />
                                        <p class="text-[8px] text-slate-400 font-black uppercase tracking-tighter">{{ \Carbon\Carbon::parse($revision->resubmitted_at)->diffForHumans() }}</p>
                                    @else
                                        <x-status-badge type="amber" label="ADA REVISI" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <a href="{{ route('student-seminar-revisions.show', $revision->id) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-500/20">
                                    Detail Revisi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="5" description="Dosen penguji belum mengunggah catatan revisi untuk seminar Anda." icon="revision" />
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>
</x-app-layout>

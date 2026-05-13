<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :links="[
                    ['name' => 'Revisi Seminar', 'url' => route('student-seminar-revisions.index')]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Revisi Hasil Seminar
                    <span class="ml-3 px-2 py-0.5 bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase tracking-wider rounded-md border border-orange-200 dark:border-orange-500/20 shadow-sm">Mahasiswa</span>
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    <span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-2"></span>
                    Pantau dan tindak lanjuti masukan dari dewan penguji
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-50 dark:border-slate-700">
                            <th class="px-6 py-4">Dosen Penguji</th>
                            <th class="px-6 py-4">Tgl Seminar</th>
                            <th class="px-6 py-4">Catatan Revisi</th>
                            <th class="px-6 py-4">Status Revisi</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                        @forelse($revisions as $revision)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-900/20 text-orange-600 flex items-center justify-center mr-4 font-bold text-sm shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr($revision->examiner->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm group-hover:text-orange-600 transition-colors">{{ $revision->examiner->name }}</h4>
                                        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-tighter">Penguji</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ \Carbon\Carbon::parse($revision->detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-5">
                                @if($revision->messages->count() > 0)
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">"{{ Str::limit($revision->messages->first()->message, 50) }}"</p>
                                @else
                                    <p class="text-[11px] text-slate-400">Belum ada catatan</p>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                @if($revision->resubmitted_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        Revisi Selesai
                                    </span>
                                    <p class="text-[8px] text-slate-400 mt-1 font-bold">{{ \Carbon\Carbon::parse($revision->resubmitted_at)->diffForHumans() }}</p>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-amber-100 text-amber-700 border border-amber-200">
                                        Ada Revisi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <a href="{{ route('student-seminar-revisions.show', $revision->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 hover:bg-orange-600 hover:text-white dark:hover:bg-orange-600 transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-700/50 rounded-2xl flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-200 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <h3 class="text-slate-400 font-bold text-sm uppercase tracking-widest">Belum Ada Revisi</h3>
                                    <p class="text-[11px] text-slate-400 mt-1 italic">Dosen penguji belum mengunggah catatan revisi untuk seminar Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

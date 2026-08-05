<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <x-breadcrumb :items="[
                ['label' => 'Data Skripsi', 'route' => route('theses.index')],
                ['label' => 'Papan Kanban', 'route' => null]
            ]" />
        </div>
    </x-slot>

    <div class="w-full">
        <!-- Kanban Board Container -->
        <div class="flex items-start gap-4 overflow-x-auto pb-6 custom-scrollbar" style="min-height: calc(100vh - 250px);">
            
            <!-- Column 1: Pengajuan Baru -->
            <div class="flex-shrink-0 w-80 bg-slate-50/50 dark:bg-slate-900/30 rounded-2xl flex flex-col max-h-full border border-slate-200/60 dark:border-slate-800/60">
                <div class="p-4 border-b border-slate-200/60 dark:border-slate-800/60 flex justify-between items-center bg-white/50 dark:bg-slate-800/50 rounded-t-2xl">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-400 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        Baru Daftar
                    </h3>
                    <span class="bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $pengajuanBaru->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @foreach($pengajuanBaru as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'slate'])
                    @endforeach
                </div>
            </div>

            <!-- Column 2: Belum Seminar -->
            <div class="flex-shrink-0 w-80 bg-blue-50/30 dark:bg-blue-900/10 rounded-2xl flex flex-col max-h-full border border-blue-100 dark:border-blue-900/30">
                <div class="p-4 border-b border-blue-100 dark:border-blue-900/30 flex justify-between items-center bg-blue-50/50 dark:bg-blue-900/20 rounded-t-2xl">
                    <h3 class="text-xs font-black uppercase tracking-widest text-blue-700 dark:text-blue-400 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Belum Seminar
                    </h3>
                    <span class="bg-blue-200 dark:bg-blue-800 text-blue-800 dark:text-blue-200 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $bimbinganUp->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @foreach($bimbinganUp as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'blue'])
                    @endforeach
                </div>
            </div>

            <!-- Column 3: Sudah Seminar -->
            <div class="flex-shrink-0 w-80 bg-amber-50/30 dark:bg-amber-900/10 rounded-2xl flex flex-col max-h-full border border-amber-100 dark:border-amber-900/30">
                <div class="p-4 border-b border-amber-100 dark:border-amber-900/30 flex justify-between items-center bg-amber-50/50 dark:bg-amber-900/20 rounded-t-2xl">
                    <h3 class="text-xs font-black uppercase tracking-widest text-amber-700 dark:text-amber-400 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Sudah Seminar
                    </h3>
                    <span class="bg-amber-200 dark:bg-amber-800 text-amber-800 dark:text-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $prosesSeminar->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @foreach($prosesSeminar as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'amber'])
                    @endforeach
                </div>
            </div>

            <!-- Column 4: Siap Sidang -->
            <div class="flex-shrink-0 w-80 bg-purple-50/30 dark:bg-purple-900/10 rounded-2xl flex flex-col max-h-full border border-purple-100 dark:border-purple-900/30">
                <div class="p-4 border-b border-purple-100 dark:border-purple-900/30 flex justify-between items-center bg-purple-50/50 dark:bg-purple-900/20 rounded-t-2xl">
                    <h3 class="text-xs font-black uppercase tracking-widest text-purple-700 dark:text-purple-400 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        Siap Sidang
                    </h3>
                    <span class="bg-purple-200 dark:bg-purple-800 text-purple-800 dark:text-purple-200 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $siapSidang->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @foreach($siapSidang as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'purple'])
                    @endforeach
                </div>
            </div>

            <!-- Column 5: Lulus -->
            <div class="flex-shrink-0 w-80 bg-emerald-50/30 dark:bg-emerald-900/10 rounded-2xl flex flex-col max-h-full border border-emerald-100 dark:border-emerald-900/30">
                <div class="p-4 border-b border-emerald-100 dark:border-emerald-900/30 flex justify-between items-center bg-emerald-50/50 dark:bg-emerald-900/20 rounded-t-2xl">
                    <h3 class="text-xs font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Lulus
                    </h3>
                    <span class="bg-emerald-200 dark:bg-emerald-800 text-emerald-800 dark:text-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $lulus->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @foreach($lulus as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'emerald'])
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

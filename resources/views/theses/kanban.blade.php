<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <x-breadcrumb :items="[
                ['label' => 'Data Skripsi', 'route' => route('theses.index')],
                ['label' => 'Papan Kanban', 'route' => null]
            ]" />

            <div class="flex items-center gap-3">
                <a href="{{ route('theses.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-50 dark:hover:bg-slate-700 transition-all border border-slate-200 dark:border-slate-700 shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span>Tampilan Tabel</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-4" x-data="{ search: '' }">
        <!-- Top Toolbar -->
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="relative w-full sm:w-80">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" 
                       x-model="search" 
                       placeholder="Cari mahasiswa, NPM, atau judul..." 
                       class="w-full pl-9 pr-8 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                <button x-show="search" @click="search = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs font-bold">
                    ✕
                </button>
            </div>

            <div class="flex items-center gap-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <span>Total: <strong class="text-slate-800 dark:text-slate-100 font-extrabold">{{ $pengajuanBaru->count() + $bimbinganUp->count() + $prosesSeminar->count() + $siapSidang->count() + $lulus->count() }}</strong> Skripsi</span>
            </div>
        </div>

        <!-- Kanban Board Columns Grid -->
        <div class="flex items-start gap-4 overflow-x-auto pb-4 custom-scrollbar" style="display: flex; align-items: flex-start; gap: 16px; overflow-x: auto; height: calc(100vh - 230px); min-height: 520px;">
            
            <!-- Column 1: Baru Daftar -->
            <div class="bg-slate-100/60 dark:bg-slate-900/50 rounded-2xl flex flex-col border border-slate-200/80 dark:border-slate-800 overflow-hidden" style="width: 300px !important; min-width: 300px !important; max-width: 300px !important; flex-shrink: 0 !important; height: 100% !important;">
                <div class="px-4 py-3 border-b border-slate-200/80 dark:border-slate-800 flex justify-between items-center bg-slate-200/40 dark:bg-slate-800/40 shrink-0">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        Baru Daftar
                    </h3>
                    <span class="bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $pengajuanBaru->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @forelse($pengajuanBaru as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'slate'])
                    @empty
                        <div class="py-8 flex flex-col items-center justify-center text-slate-400 border border-dashed border-slate-200/80 dark:border-slate-800 rounded-xl bg-white/40 dark:bg-slate-900/20">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Kosong</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 2: Belum Seminar -->
            <div class="bg-blue-50/30 dark:bg-blue-950/20 rounded-2xl flex flex-col border border-blue-100 dark:border-blue-900/40 overflow-hidden" style="width: 300px !important; min-width: 300px !important; max-width: 300px !important; flex-shrink: 0 !important; height: 100% !important;">
                <div class="px-4 py-3 border-b border-blue-100 dark:border-blue-900/40 flex justify-between items-center bg-blue-100/40 dark:bg-blue-900/30 shrink-0">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-300 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Belum Seminar
                    </h3>
                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $bimbinganUp->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @forelse($bimbinganUp as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'blue'])
                    @empty
                        <div class="py-8 flex flex-col items-center justify-center text-slate-400 border border-dashed border-blue-200/60 dark:border-blue-900/30 rounded-xl bg-white/40 dark:bg-slate-900/20">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Kosong</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 3: Sudah Seminar -->
            <div class="bg-amber-50/30 dark:bg-amber-950/20 rounded-2xl flex flex-col border border-amber-100 dark:border-amber-900/40 overflow-hidden" style="width: 300px !important; min-width: 300px !important; max-width: 300px !important; flex-shrink: 0 !important; height: 100% !important;">
                <div class="px-4 py-3 border-b border-amber-100 dark:border-amber-900/40 flex justify-between items-center bg-amber-100/40 dark:bg-amber-900/30 shrink-0">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-300 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Sudah Seminar
                    </h3>
                    <span class="bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $prosesSeminar->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @forelse($prosesSeminar as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'amber'])
                    @empty
                        <div class="py-8 flex flex-col items-center justify-center text-slate-400 border border-dashed border-amber-200/60 dark:border-amber-900/30 rounded-xl bg-white/40 dark:bg-slate-900/20">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Kosong</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 4: Siap Sidang -->
            <div class="bg-purple-50/30 dark:bg-purple-950/20 rounded-2xl flex flex-col border border-purple-100 dark:border-purple-900/40 overflow-hidden" style="width: 300px !important; min-width: 300px !important; max-width: 300px !important; flex-shrink: 0 !important; height: 100% !important;">
                <div class="px-4 py-3 border-b border-purple-100 dark:border-purple-900/40 flex justify-between items-center bg-purple-100/40 dark:bg-purple-900/30 shrink-0">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        Siap Sidang
                    </h3>
                    <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $siapSidang->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @forelse($siapSidang as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'purple'])
                    @empty
                        <div class="py-8 flex flex-col items-center justify-center text-slate-400 border border-dashed border-purple-200/60 dark:border-purple-900/30 rounded-xl bg-white/40 dark:bg-slate-900/20">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Kosong</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 5: Lulus -->
            <div class="bg-emerald-50/30 dark:bg-emerald-950/20 rounded-2xl flex flex-col border border-emerald-100 dark:border-emerald-900/40 overflow-hidden" style="width: 300px !important; min-width: 300px !important; max-width: 300px !important; flex-shrink: 0 !important; height: 100% !important;">
                <div class="px-4 py-3 border-b border-emerald-100 dark:border-emerald-900/40 flex justify-between items-center bg-emerald-100/40 dark:bg-emerald-900/30 shrink-0">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Lulus
                    </h3>
                    <span class="bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $lulus->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @forelse($lulus as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'emerald'])
                    @empty
                        <div class="py-8 flex flex-col items-center justify-center text-slate-400 border border-dashed border-emerald-200/60 dark:border-emerald-900/30 rounded-xl bg-white/40 dark:bg-slate-900/20">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Kosong</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

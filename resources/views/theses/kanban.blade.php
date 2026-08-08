<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <x-breadcrumb :items="[
                ['label' => 'Data Skripsi', 'route' => route('theses.index')],
                ['label' => 'Papan Kanban', 'route' => null]
            ]" />

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('theses.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all border border-slate-200 dark:border-slate-700">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span>Tampilan Tabel</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="{ search: '' }">
        <!-- Top Toolbar -->
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="relative flex-1 max-w-md">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" 
                       x-model="search" 
                       placeholder="Cari mahasiswa, NPM, atau judul skripsi..." 
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                <button x-show="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs font-bold">
                    ✕
                </button>
            </div>

            <div class="flex items-center gap-4 text-xs font-black text-slate-500 uppercase tracking-wider">
                <span>Total Skripsi: <strong class="text-slate-800 dark:text-slate-100">{{ $pengajuanBaru->count() + $bimbinganUp->count() + $prosesSeminar->count() + $siapSidang->count() + $lulus->count() }}</strong></span>
            </div>
        </div>

        <!-- Kanban Board Columns Grid -->
        <div class="flex items-start gap-4 overflow-x-auto pb-4 custom-scrollbar h-[calc(100vh-14rem)]">
            
            <!-- Column 1: Baru Daftar -->
            <div class="flex-shrink-0 w-80 bg-slate-100/70 dark:bg-slate-900/60 rounded-3xl flex flex-col h-full border border-slate-200/80 dark:border-slate-800/80 overflow-hidden shadow-xs">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-200/40 dark:bg-slate-800/40 shrink-0">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                        Baru Daftar
                    </h3>
                    <span class="bg-slate-300 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-[10px] font-black px-2.5 py-0.5 rounded-full shadow-2xs">{{ $pengajuanBaru->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @forelse($pengajuanBaru as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'slate'])
                    @empty
                        <div class="py-12 text-center text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                            <p class="text-[10px] font-black uppercase tracking-widest">Kosong</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 2: Belum Seminar -->
            <div class="flex-shrink-0 w-80 bg-blue-50/40 dark:bg-blue-950/20 rounded-3xl flex flex-col h-full border border-blue-100 dark:border-blue-900/40 overflow-hidden shadow-xs">
                <div class="p-4 border-b border-blue-100 dark:border-blue-900/40 flex justify-between items-center bg-blue-100/40 dark:bg-blue-900/30 shrink-0">
                    <h3 class="text-xs font-black uppercase tracking-widest text-blue-700 dark:text-blue-300 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        Belum Seminar
                    </h3>
                    <span class="bg-blue-200 dark:bg-blue-800 text-blue-900 dark:text-blue-100 text-[10px] font-black px-2.5 py-0.5 rounded-full shadow-2xs">{{ $bimbinganUp->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @forelse($bimbinganUp as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'blue'])
                    @empty
                        <div class="py-12 text-center text-slate-400 border-2 border-dashed border-blue-200/50 dark:border-blue-900/30 rounded-2xl">
                            <p class="text-[10px] font-black uppercase tracking-widest">Kosong</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 3: Sudah Seminar -->
            <div class="flex-shrink-0 w-80 bg-amber-50/40 dark:bg-amber-950/20 rounded-3xl flex flex-col h-full border border-amber-100 dark:border-amber-900/40 overflow-hidden shadow-xs">
                <div class="p-4 border-b border-amber-100 dark:border-amber-900/40 flex justify-between items-center bg-amber-100/40 dark:bg-amber-900/30 shrink-0">
                    <h3 class="text-xs font-black uppercase tracking-widest text-amber-700 dark:text-amber-300 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Sudah Seminar
                    </h3>
                    <span class="bg-amber-200 dark:bg-amber-800 text-amber-900 dark:text-amber-100 text-[10px] font-black px-2.5 py-0.5 rounded-full shadow-2xs">{{ $prosesSeminar->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @forelse($prosesSeminar as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'amber'])
                    @empty
                        <div class="py-12 text-center text-slate-400 border-2 border-dashed border-amber-200/50 dark:border-amber-900/30 rounded-2xl">
                            <p class="text-[10px] font-black uppercase tracking-widest">Kosong</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 4: Siap Sidang -->
            <div class="flex-shrink-0 w-80 bg-purple-50/40 dark:bg-purple-950/20 rounded-3xl flex flex-col h-full border border-purple-100 dark:border-purple-900/40 overflow-hidden shadow-xs">
                <div class="p-4 border-b border-purple-100 dark:border-purple-900/40 flex justify-between items-center bg-purple-100/40 dark:bg-purple-900/30 shrink-0">
                    <h3 class="text-xs font-black uppercase tracking-widest text-purple-700 dark:text-purple-300 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                        Siap Sidang
                    </h3>
                    <span class="bg-purple-200 dark:bg-purple-800 text-purple-900 dark:text-purple-100 text-[10px] font-black px-2.5 py-0.5 rounded-full shadow-2xs">{{ $siapSidang->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @forelse($siapSidang as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'purple'])
                    @empty
                        <div class="py-12 text-center text-slate-400 border-2 border-dashed border-purple-200/50 dark:border-purple-900/30 rounded-2xl">
                            <p class="text-[10px] font-black uppercase tracking-widest">Kosong</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 5: Lulus -->
            <div class="flex-shrink-0 w-80 bg-emerald-50/40 dark:bg-emerald-950/20 rounded-3xl flex flex-col h-full border border-emerald-100 dark:border-emerald-900/40 overflow-hidden shadow-xs">
                <div class="p-4 border-b border-emerald-100 dark:border-emerald-900/40 flex justify-between items-center bg-emerald-100/40 dark:bg-emerald-900/30 shrink-0">
                    <h3 class="text-xs font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        Lulus
                    </h3>
                    <span class="bg-emerald-200 dark:bg-emerald-800 text-emerald-900 dark:text-emerald-100 text-[10px] font-black px-2.5 py-0.5 rounded-full shadow-2xs">{{ $lulus->count() }}</span>
                </div>
                <div class="p-3 overflow-y-auto custom-scrollbar flex-1 space-y-3">
                    @forelse($lulus as $thesis)
                        @include('theses.partials.kanban-card', ['thesis' => $thesis, 'accent' => 'emerald'])
                    @empty
                        <div class="py-12 text-center text-slate-400 border-2 border-dashed border-emerald-200/50 dark:border-emerald-900/30 rounded-2xl">
                            <p class="text-[10px] font-black uppercase tracking-widest">Kosong</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

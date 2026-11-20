<x-app-layout>
    <x-slot name="header">
        <div class="w-full">
            <x-breadcrumb :items="[
                ['label' => 'Monitoring', 'route' => route('monitoring.index')],
                ['label' => 'Grafik Analitik', 'route' => null]
            ]" />

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight">
                    Grafik & Statistik Analitik SIBIMA
                </h2>

                <!-- Export Toolbar Actions -->
                <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap shrink-0">
                    <!-- Export Excel -->
                    <a href="{{ route('analytics.export-excel', request()->query()) }}" 
                       class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all"
                       title="Unduh seluruh data tabel analitik ke format Excel">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Export Excel</span>
                    </a>

                    <!-- Export PDF -->
                    <a href="{{ route('analytics.export-pdf', request()->query()) }}" 
                       class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all"
                       title="Cetak dan unduh laporan resmi berstandar PDF">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span>Export PDF</span>
                    </a>

                    <!-- Batch PNG Download Button -->
                    <button type="button" 
                            onclick="downloadAllChartsAsPng()"
                            class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-700 rounded-xl text-xs font-bold shadow-2xs hover:scale-[1.02] active:scale-95 transition-all cursor-pointer"
                            title="Unduh seluruh 13 grafik sebagai berkas gambar PNG">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Unduh Semua PNG</span>
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8 pb-16" x-data="{ filterOpen: true }">

        @include('analytics.partials.filter-toolbar')

        @include('analytics.partials.kpi-cards')

        @include('analytics.partials.charts-grid')

    </div>

    @include('analytics.partials.charts-script')
</x-app-layout>

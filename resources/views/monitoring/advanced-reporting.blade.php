<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :links="[
                    ['name' => 'Monitoring', 'url' => '#'],
                    ['name' => 'Pusat Laporan & Analisa', 'url' => route('monitoring.advanced-reporting')]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Pusat Laporan & Analisa
                    <span class="ml-3 px-2 py-0.5 bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-wider rounded-md border border-indigo-200 dark:border-indigo-500/20 shadow-sm">Advanced</span>
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    Platform terpadu untuk memantau data akademik, progres mahasiswa, dan performa dosen.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ activeTab: 'akademik' }">
        <!-- Navigation Tabs -->
        <div class="flex items-center gap-1 border-b border-slate-100 dark:border-slate-800 overflow-x-auto pb-px custom-scrollbar">
            <button @click="activeTab = 'akademik'" :class="activeTab === 'akademik' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'" class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Akademik
            </button>
            <button @click="activeTab = 'mahasiswa'" :class="activeTab === 'mahasiswa' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'" class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Mahasiswa
            </button>
            <button @click="activeTab = 'dosen'" :class="activeTab === 'dosen' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'" class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Dosen
            </button>
            <button @click="activeTab = 'kelulusan'" :class="activeTab === 'kelulusan' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'" class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Kelulusan
            </button>
            <button @click="activeTab = 'waktu'" :class="activeTab === 'waktu' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'" class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Waktu & Tren
            </button>
            <button @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'" class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Log Aktivitas
            </button>
        </div>

        <!-- Report Content -->
        <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex flex-col lg:flex-row min-h-[400px]">
                <!-- Left Info Section -->
                <div class="lg:w-1/3 p-10 bg-slate-50/50 dark:bg-slate-900/30 border-r border-slate-100 dark:border-slate-700">
                    <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center shadow-lg shadow-slate-200/50 dark:shadow-none mb-8 border border-slate-100 dark:border-slate-700">
                        <template x-if="activeTab === 'akademik'">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </template>
                        <template x-if="activeTab === 'mahasiswa'">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </template>
                        <template x-if="activeTab === 'dosen'">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </template>
                        <template x-if="activeTab === 'kelulusan'">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </template>
                        <template x-if="activeTab === 'waktu'">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </template>
                        <template x-if="activeTab === 'logs'">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </template>
                    </div>

                    <h4 class="text-xl font-black text-slate-800 dark:text-slate-100 mb-4 tracking-tight">
                        <template x-if="activeTab === 'akademik'"><span>Laporan Akademik Global</span></template>
                        <template x-if="activeTab === 'mahasiswa'"><span>Laporan Progres Mahasiswa</span></template>
                        <template x-if="activeTab === 'dosen'"><span>Laporan Kinerja & Beban Dosen</span></template>
                        <template x-if="activeTab === 'kelulusan'"><span>Laporan Rekapitulasi Kelulusan</span></template>
                        <template x-if="activeTab === 'waktu'"><span>Laporan Tren Masa Studi</span></template>
                        <template x-if="activeTab === 'logs'"><span>Laporan Audit Log Sistem</span></template>
                    </h4>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 leading-relaxed">
                        <template x-if="activeTab === 'akademik'"><span>Unduh ringkasan status skripsi mahasiswa secara keseluruhan termasuk yang aktif, tertunda, maupun selesai.</span></template>
                        <template x-if="activeTab === 'mahasiswa'"><span>Detail progres bimbingan, logbook, dan tahapan pendaftaran seminar/sidang untuk setiap mahasiswa.</span></template>
                        <template x-if="activeTab === 'dosen'"><span>Rekapitulasi jumlah bimbingan aktif per dosen serta status beban kerja (overload/normal).</span></template>
                        <template x-if="activeTab === 'kelulusan'"><span>Daftar mahasiswa yang telah lulus sidang beserta nilai akhir dan predikat kelulusan.</span></template>
                        <template x-if="activeTab === 'waktu'"><span>Analisis rata-rata durasi pengerjaan skripsi per gelombang dan angkatan untuk evaluasi institusi.</span></template>
                        <template x-if="activeTab === 'logs'"><span>Riwayat aktivitas admin dan perubahan data penting (Audit Trail) dalam periode tertentu.</span></template>
                    </p>
                </div>

                <!-- Right Form Section -->
                <div class="flex-1 p-10 flex flex-col justify-center">
                    <form action="{{ route('monitoring.export') }}" method="GET" class="space-y-8">
                        <input type="hidden" name="type" :value="activeTab">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dari Tanggal</label>
                                <div class="relative group">
                                    <input type="date" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                                <div class="relative group">
                                    <input type="date" name="end_date" value="{{ now()->format('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4 pt-4">
                            <button type="submit" name="format" value="pdf" class="w-full sm:w-auto flex-1 flex items-center justify-center gap-3 px-8 py-5 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-slate-200/50 dark:shadow-none group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Download PDF
                            </button>
                            <button type="submit" name="format" value="excel" class="w-full sm:w-auto flex-1 flex items-center justify-center gap-3 px-8 py-5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-emerald-50 dark:hover:bg-emerald-900/10 hover:border-emerald-200 dark:hover:border-emerald-800 transition-all group">
                                <svg class="w-5 h-5 text-emerald-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Export Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Summary Row (Secondary Information) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-6 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 flex items-center gap-5">
                <div class="w-12 h-12 bg-orange-50 dark:bg-orange-500/10 rounded-2xl flex items-center justify-center text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Skripsi</p>
                    <p class="text-xl font-black text-slate-800 dark:text-slate-100">{{ $activeThesesCount }}</p>
                </div>
            </div>
            <div class="p-6 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 flex items-center gap-5">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Lulus</p>
                    <p class="text-xl font-black text-slate-800 dark:text-slate-100">{{ $thesisStatusCounts['completed'] }}</p>
                </div>
            </div>
            <div class="p-6 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 flex items-center gap-5">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Masa Studi Avg</p>
                    <p class="text-xl font-black text-slate-800 dark:text-slate-100">{{ collect($cohortCompletionData)->avg() ? round(collect($cohortCompletionData)->avg(), 1) : '-' }} <span class="text-xs">Thn</span></p>
                </div>
            </div>
            <div class="p-6 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 flex items-center gap-5">
                <div class="w-12 h-12 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mhs Kritis</p>
                    <p class="text-xl font-black text-slate-800 dark:text-slate-100">{{ $studentHealthStats['Kritis'] }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

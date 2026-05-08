<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Log Aktivitas Sistem', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full mx-auto transition-colors duration-300">
        <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm border border-slate-100 dark:border-slate-700 rounded-lg">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/30 dark:bg-slate-900/30">
                <div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Daftar Aktivitas</h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold mt-1 uppercase">Mencatat seluruh interaksi penting di dalam sistem SIBIMA</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <!-- Export Button -->
                    <a href="{{ route('admin.logs.export', ['search' => $search, 'module' => $module]) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 border border-transparent rounded-md font-bold text-[10px] text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export Excel
                    </a>

                    <!-- Filter Module -->
                    <form action="{{ route('admin.logs') }}" method="GET" class="flex gap-2 w-full sm:w-auto">
                        <select name="module" onchange="this.form.submit()" class="text-xs border-slate-200 dark:border-slate-600 rounded-md bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:ring-orange-500 focus:border-orange-500 min-w-[120px]">
                            <option value="">Semua Modul</option>
                            @foreach($modules as $m)
                                <option value="{{ $m }}" {{ $module == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                        
                        <!-- Search Input -->
                        <div class="relative w-full sm:w-auto">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari aktivitas..." class="block w-full sm:w-64 pl-9 pr-10 py-1.5 border border-slate-200 dark:border-slate-600 rounded-md leading-5 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 text-xs transition-colors">
                            @if(isset($search) && $search !== '')
                                <a href="{{ route('admin.logs') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-[10px] text-slate-500 dark:text-slate-400 uppercase bg-slate-50/80 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-700 font-black tracking-wider">
                        <tr>
                            <th scope="col" class="py-4 px-6">Waktu</th>
                            <th scope="col" class="py-4 px-6">Pengguna</th>
                            <th scope="col" class="py-4 px-6">Aktivitas</th>
                            <th scope="col" class="py-4 px-6">Modul</th>
                            <th scope="col" class="py-4 px-6">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $log->created_at->format('d M Y') }}</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $log->created_at->format('H:i:s') }}</p>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="flex items-center">
                                            <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-500 flex items-center justify-center font-black text-[10px] mr-3">
                                                {{ substr($log->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $log->user->name }}</p>
                                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium uppercase tracking-tighter">{{ $log->user->role }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center text-slate-400 dark:text-slate-500">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            </div>
                                            <p class="text-xs italic font-medium">Guest / System</p>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col">
                                        <span class="inline-flex text-xs font-black text-slate-800 dark:text-slate-200">{{ $log->activity }}</span>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1 group-hover:line-clamp-none transition-all duration-300 max-w-md">{{ $log->description }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider
                                        {{ $log->module === 'Auth' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}
                                        {{ $log->module === 'Skripsi' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}
                                        {{ $log->module === 'User' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : '' }}
                                        {{ $log->module === 'Seminar' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}
                                        {{ !$log->module ? 'bg-slate-50 dark:bg-slate-700 text-slate-500 dark:text-slate-400' : '' }}
                                    ">
                                        {{ $log->module ?: 'General' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap font-mono text-[10px] text-slate-400">
                                    {{ $log->ip_address }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                                        <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Belum ada log aktivitas</h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest font-black">Sistem akan mencatat aktivitas secara otomatis</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

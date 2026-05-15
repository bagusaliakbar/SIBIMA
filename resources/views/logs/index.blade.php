<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Log Aktivitas Sistem', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full mx-auto transition-colors duration-300">
        <x-table-card 
            title="Daftar Aktivitas"
            :footer="$logs->links()">
            
            <x-slot name="headerActions">
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
                        
                        <x-search-input 
                            name="search" 
                            :value="$search ?? ''" 
                            placeholder="Cari aktivitas..." 
                            route="admin.logs" />
                    </form>
                </div>
            </x-slot>

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
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $log->created_at->locale('id')->translatedFormat('d M Y') }}</p>
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
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 max-w-md">{{ $log->description }}</span>
                                    
                                    @if($log->properties)
                                        <div class="mt-2 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700/50 shadow-inner">
                                            @if(isset($log->properties['before']) && isset($log->properties['after']))
                                                <div class="space-y-3">
                                                    @foreach($log->properties['after'] as $key => $newValue)
                                                        @php $oldValue = $log->properties['before'][$key] ?? 'N/A'; @endphp
                                                        @if($oldValue != $newValue)
                                                            <div class="flex flex-col gap-1">
                                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">{{ str_replace('_', ' ', $key) }}</span>
                                                                <div class="flex items-center gap-2">
                                                                    <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-[9px] font-bold rounded border border-rose-100 dark:border-rose-800/30 line-through decoration-rose-300">{{ is_scalar($oldValue) ? $oldValue : '...' }}</span>
                                                                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                                                    <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-[9px] font-bold rounded border border-emerald-100 dark:border-emerald-800/30">{{ is_scalar($newValue) ? $newValue : '...' }}</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @elseif(isset($log->properties['old_scores']) && isset($log->properties['new_scores']))
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                    @foreach($log->properties['new_scores'] as $key => $score)
                                                        <div class="flex flex-col gap-1">
                                                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">{{ $key }}</span>
                                                            <div class="flex items-center gap-1.5">
                                                                @if($log->properties['old_scores'])
                                                                    <span class="text-[9px] text-slate-400 line-through">{{ $log->properties['old_scores'][$key] }}</span>
                                                                    <svg class="w-2.5 h-2.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                                                @endif
                                                                <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400">{{ $score }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    @foreach($log->properties as $key => $value)
                                                        @if(!is_array($value))
                                                            <div class="flex items-center gap-2 text-[9px]">
                                                                <span class="font-bold text-slate-400 uppercase tracking-tighter">{{ str_replace('_', ' ', $key) }}:</span>
                                                                <span class="font-black text-slate-700 dark:text-slate-300 uppercase">{{ $value }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <x-status-badge 
                                    :type="$log->module === 'Auth' ? 'blue' : ($log->module === 'Skripsi' ? 'orange' : ($log->module === 'User' ? 'emerald' : ($log->module === 'Seminar' ? 'purple' : 'slate')))" 
                                    :label="$log->module ?: 'General'" />
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap font-mono text-[10px] text-slate-400">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="5" description="Sistem akan mencatat aktivitas secara otomatis" icon="logs" />
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Histori Status', 'route' => null]
                ]" />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Histori Perjalanan Skripsi
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8">
        @if($logs->isEmpty())
            <div class="bg-white dark:bg-slate-800/50 p-12 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm text-center">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-2">Belum Ada Histori</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xs mx-auto">Sistem belum mencatat aktivitas signifikan untuk skripsi Anda. Terus lakukan progres bimbingan!</p>
            </div>
        @else
            <div class="relative">
                <!-- Vertical Line -->
                <div class="absolute left-4 md:left-1/2 transform md:-translate-x-1/2 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 via-indigo-500 to-emerald-500 rounded-full opacity-20"></div>

                <div class="space-y-12">
                    @foreach($logs as $index => $log)
                        <div class="relative flex flex-col md:flex-row items-center group">
                            <!-- Dot -->
                            <div class="absolute left-4 md:left-1/2 transform md:-translate-x-1/2 w-8 h-8 rounded-full bg-white dark:bg-slate-900 border-4 border-indigo-500 z-10 shadow-lg shadow-indigo-500/20 group-hover:scale-125 transition-transform duration-300"></div>

                            <!-- Content -->
                            <div class="flex-1 w-full md:w-auto pl-16 md:pl-0 {{ $index % 2 == 0 ? 'md:pr-12 md:text-right' : 'md:order-last md:pl-12 md:text-left' }}">
                                <div class="bg-white dark:bg-slate-800/50 p-6 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm hover:shadow-xl hover:border-indigo-500/30 transition-all duration-500 relative overflow-hidden">
                                    <!-- Date Badge -->
                                    <div class="inline-flex items-center px-2 py-1 bg-slate-50 dark:bg-slate-900/50 text-slate-400 text-[9px] font-black uppercase tracking-widest rounded-lg mb-3 border border-slate-100 dark:border-slate-700">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $log->created_at->locale('id')->translatedFormat('d F Y, H:i') }}
                                    </div>

                                    <h4 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-2">{{ $log->activity }}</h4>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 font-medium leading-relaxed mb-4">
                                        {{ $log->description }}
                                    </p>

                                    @if($log->properties)
                                        <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-100 dark:border-slate-700/50 text-left">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Detail Perubahan</p>
                                            <div class="space-y-2">
                                                @foreach($log->properties as $key => $value)
                                                    @if(is_array($value))
                                                        <div class="flex flex-col gap-1">
                                                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-500 capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                                                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                                                @foreach($value as $subKey => $subVal)
                                                                    <div class="bg-white dark:bg-slate-800 p-2 rounded-lg border border-slate-100 dark:border-slate-700">
                                                                        <span class="block text-[8px] text-slate-400 uppercase font-black">{{ $subKey }}</span>
                                                                        <span class="font-bold text-slate-700 dark:text-slate-200">{{ is_scalar($subVal) ? $subVal : json_encode($subVal) }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-slate-700 last:border-0">
                                                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-500 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                                            <span class="text-[11px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-tight">{{ $value }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-700/50 flex items-center gap-3">
                                        @if($log->user)
                                            <img src="{{ $log->user->avatar_url }}" class="w-6 h-6 rounded-full ring-2 ring-indigo-500/20" alt="">
                                            <div class="text-left">
                                                <p class="text-[10px] font-black text-slate-800 dark:text-slate-200 leading-none mb-0.5">{{ $log->user->name }}</p>
                                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-none">{{ $log->user->role }}</p>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            </div>
                                            <div class="text-left">
                                                <p class="text-[10px] font-black text-slate-800 dark:text-slate-200 leading-none mb-0.5">Sistem SIBIMA</p>
                                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-none">Automated</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Spacer for md screen -->
                            <div class="hidden md:block flex-1"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

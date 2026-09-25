<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') ? route('theses.index') : route('logbooks.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-orange-100 dark:hover:bg-orange-900/40 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                Monitoring Bimbingan
            </h2>
        </div>
        <div class="hidden md:block mt-3 sm:mt-0 text-sm text-slate-500 dark:text-slate-400 ml-11">
            Pantau aktivitas bimbingan mahasiswa secara mendetail.
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Thesis Info Card -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-md shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex flex-col md:flex-row justify-between gap-6">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Mahasiswa</h3>
                    <div class="flex items-center gap-2">
                        <div class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $thesis->student->name }}</div>
                        @php
                            $waNumber = \App\Helpers\PhoneHelper::formatForWhatsApp($thesis->student->phone_number);
                            $dosenName = Auth::user()->name;
                            $roleLabel = ($thesis->pembimbing1_id === Auth::id()) ? 'Pembimbing 1' : (($thesis->pembimbing2_id === Auth::id()) ? 'Pembimbing 2' : 'Dosen');
                            $waMessage = urlencode("Halo {$thesis->student->name}, saya {$dosenName} ({$roleLabel}). Terkait logbook bimbingan skripsi: \"" . ($thesis->final_title ?? $thesis->title) . "\"");
                        @endphp
                        @if($waNumber)
                            <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-md text-[10px] font-bold transition-all shadow-2xs hover:scale-105"
                               title="Kirim Pesan WhatsApp ke {{ $thesis->student->name }} ({{ $thesis->student->phone_number }})">
                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400 fill-current shrink-0" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>WhatsApp</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-sm text-slate-500 dark:text-slate-400">{{ $thesis->student->identifier ?? '-' }}</div>
                </div>
                
                <div class="md:max-w-md">
                    <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Rencana Judul Skripsi</h3>
                    <div class="text-sm font-medium text-slate-700 dark:text-slate-300 leading-relaxed">{{ $thesis->final_title ?? $thesis->title }}</div>
                </div>
                
                <div>
                    <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Dosen Pembimbing</h3>
                    <div class="space-y-1.5">
                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300 flex items-center justify-between gap-3">
                            <div class="flex items-center">
                                <span class="w-4 h-4 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center mr-2 text-[10px] font-bold border border-indigo-200 dark:border-indigo-800">1</span>
                                <span class="{{ $thesis->pembimbing1_id === Auth::id() ? 'font-bold text-indigo-600 dark:text-indigo-400' : '' }}">{{ $thesis->pembimbing1->name ?? '-' }}</span>
                                @if($thesis->pembimbing1_id === Auth::id())
                                    <span class="ml-1 text-[10px] font-bold text-indigo-500">(Anda)</span>
                                @endif
                            </div>
                            <span class="text-[11px] font-semibold font-mono text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2 py-0.5 rounded">{{ $p1SessionCount ?? 0 }} / 8 Sesi</span>
                        </div>
                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300 flex items-center justify-between gap-3">
                            <div class="flex items-center">
                                <span class="w-4 h-4 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 flex items-center justify-center mr-2 text-[10px] font-bold border border-purple-200 dark:border-purple-800">2</span>
                                <span class="{{ $thesis->pembimbing2_id === Auth::id() ? 'font-bold text-purple-600 dark:text-purple-400' : '' }}">{{ $thesis->pembimbing2->name ?? '-' }}</span>
                                @if($thesis->pembimbing2_id === Auth::id())
                                    <span class="ml-1 text-[10px] font-bold text-purple-500">(Anda)</span>
                                @endif
                            </div>
                            <span class="text-[11px] font-semibold font-mono text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2 py-0.5 rounded">{{ $p2SessionCount ?? 0 }} / 8 Sesi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Active Sessions (Jadwal Mendatang) -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Jadwal Aktif</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sesi bimbingan yang akan datang atau menunggu persetujuan.</p>
                    </div>
                    
                    <div class="p-5 space-y-4">
                        @forelse($activeSessions as $session)
                            @php
                                $isP1Active = ($session->dosen_id === $thesis->pembimbing1_id);
                                $isP2Active = ($session->dosen_id === $thesis->pembimbing2_id);
                                $isMyActive = ($session->dosen_id === Auth::id());
                                $activeRoleTag = $isP1Active ? 'Pembimbing 1' : ($isP2Active ? 'Pembimbing 2' : 'Dosen');
                            @endphp
                            <div class="p-4 rounded border {{ $session->status === 'approved' ? 'border-orange-200 dark:border-orange-900/50 bg-orange-50/30 dark:bg-orange-900/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs font-bold {{ $session->status === 'approved' ? 'text-orange-600 dark:text-orange-400' : 'text-slate-500 dark:text-slate-400' }}">
                                        {{ $session->scheduled_at->format('d M Y • H:i') }}
                                    </span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $session->status === 'approved' ? 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400' }}">
                                        {{ $session->status }}
                                    </span>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $session->topic }}</h4>
                                <div class="mt-1.5 flex items-start text-[10px]">
                                    @if($session->type === 'online')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-semibold mr-2 border border-blue-100 dark:border-blue-800/50">Online</span>
                                        @if($session->location)
                                            <a href="{{ Str::startsWith($session->location, 'http') ? $session->location : 'https://' . $session->location }}" target="_blank" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline font-medium" title="{{ $session->location }}">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                Gabung Rapat
                                            </a>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold mr-2 border border-slate-200 dark:border-slate-600">Offline</span>
                                        @if($session->location)
                                            <span class="text-slate-500 dark:text-slate-400 truncate max-w-[150px] inline-block" title="{{ $session->location }}">{{ $session->location }}</span>
                                        @endif
                                    @endif
                                </div>
                                @if($session->notes)
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 italic">"{{ $session->notes }}"</p>
                                @endif
                                
                                @if($session->dosen)
                                    <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-1.5">
                                        <div class="flex items-center gap-1.5 text-[10px]">
                                            <svg class="w-3 h-3 text-slate-400 dark:text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                            <span class="font-bold text-slate-600 dark:text-slate-300">{{ $session->dosen->name }}</span>
                                            @if($isMyActive)
                                                <span class="text-[9px] font-bold text-orange-500">(Anda)</span>
                                            @endif
                                        </div>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold {{ $isP1Active ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800' }}">
                                            {{ $activeRoleTag }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-sm">
                                Tidak ada jadwal aktif.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Logbook (Riwayat Selesai) -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Filter Pembimbing Toolbar (Dropdown) -->
                @php
                    $isDosenFiltered = in_array($filterDosen ?? 'all', ['p1', 'p2']);
                    $currentDosenLabel = 'Semua Pembimbing';
                    $currentDosenBadge = $totalCompletedCount ?? $completedSessions->count();
                    $activeColorClass = 'bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700';

                    if (($filterDosen ?? '') === 'p1' && $thesis->pembimbing1) {
                        $currentDosenLabel = 'P1: ' . Str::limit($thesis->pembimbing1->name, 22) . ($thesis->pembimbing1_id === Auth::id() ? ' (Anda)' : '');
                        $currentDosenBadge = $p1SessionCount ?? 0;
                        $activeColorClass = 'bg-indigo-50/90 dark:bg-indigo-950/40 border-indigo-300 dark:border-indigo-600/60 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/10';
                    } elseif (($filterDosen ?? '') === 'p2' && $thesis->pembimbing2) {
                        $currentDosenLabel = 'P2: ' . Str::limit($thesis->pembimbing2->name, 22) . ($thesis->pembimbing2_id === Auth::id() ? ' (Anda)' : '');
                        $currentDosenBadge = $p2SessionCount ?? 0;
                        $activeColorClass = 'bg-purple-50/90 dark:bg-purple-950/40 border-purple-300 dark:border-purple-600/60 text-purple-700 dark:text-purple-300 ring-2 ring-purple-500/10';
                    }
                @endphp

                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xs border border-slate-200/80 dark:border-slate-700/80 p-3 px-4 flex items-center justify-between gap-3 flex-wrap sm:flex-nowrap relative z-20">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 border border-orange-200/70 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20 flex items-center justify-center shrink-0 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Filter:
                        </span>

                        <!-- Alpine.js Dropdown Menu -->
                        <div class="relative" x-data="{ openDosenDropdown: false }" @click.outside="openDosenDropdown = false">
                            <button type="button" 
                                    @click="openDosenDropdown = !openDosenDropdown"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border cursor-pointer shadow-2xs {{ $activeColorClass }}"
                                    aria-haspopup="true"
                                    :aria-expanded="openDosenDropdown">
                                @if(($filterDosen ?? '') === 'p1')
                                    <span class="w-2 h-2 rounded-full bg-indigo-500 shrink-0"></span>
                                @elseif(($filterDosen ?? '') === 'p2')
                                    <span class="w-2 h-2 rounded-full bg-purple-500 shrink-0"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                                @endif
                                
                                <span>{{ $currentDosenLabel }}</span>
                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($filterDosen ?? '') === 'p1' ? 'bg-indigo-200/80 dark:bg-indigo-500/30 text-indigo-800 dark:text-indigo-200' : (($filterDosen ?? '') === 'p2' ? 'bg-purple-200/80 dark:bg-purple-500/30 text-purple-800 dark:text-purple-200' : 'bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-400') }}">
                                    {{ $currentDosenBadge }}
                                </span>

                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openDosenDropdown ? 'rotate-180 text-orange-600 dark:text-orange-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Panel -->
                            <div x-show="openDosenDropdown"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                 class="absolute left-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200 dark:border-slate-700 p-1.5 z-50 text-left"
                                 style="display: none;">
                                
                                <div class="px-3 py-2 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
                                    <span>Pilih Pembimbing</span>
                                    @if($isDosenFiltered)
                                        <a href="{{ route('theses.logbooks', ['thesis' => $thesis->id, 'dosen' => 'all']) }}" 
                                           class="text-orange-600 hover:text-orange-700 dark:text-orange-400 font-bold lowercase hover:underline">
                                            Reset
                                        </a>
                                    @endif
                                </div>

                                <div class="py-1 space-y-0.5">
                                    <!-- 1. Semua Pembimbing -->
                                    <a href="{{ route('theses.logbooks', ['thesis' => $thesis->id, 'dosen' => 'all']) }}"
                                       class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ ($filterDosen ?? 'all') === 'all' ? 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full {{ ($filterDosen ?? 'all') === 'all' ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600' }} shrink-0"></span>
                                            <span>Semua Pembimbing</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($filterDosen ?? 'all') === 'all' ? 'bg-orange-200/80 dark:bg-orange-500/30 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                {{ $totalCompletedCount ?? $completedSessions->count() }}
                                            </span>
                                            @if(($filterDosen ?? 'all') === 'all')
                                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </div>
                                    </a>

                                    <!-- 2. Pembimbing 1 -->
                                    @if($thesis->pembimbing1)
                                        <a href="{{ route('theses.logbooks', ['thesis' => $thesis->id, 'dosen' => 'p1']) }}"
                                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ ($filterDosen ?? '') === 'p1' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>
                                                <div class="truncate">
                                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">P1:</span> 
                                                    <span>{{ $thesis->pembimbing1->name }}</span>
                                                    @if($thesis->pembimbing1_id === Auth::id())
                                                        <span class="ml-1 text-[10px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-950/60 px-1.5 py-0.5 rounded-md">(Anda)</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($filterDosen ?? '') === 'p1' ? 'bg-indigo-200/80 dark:bg-indigo-500/30 text-indigo-800 dark:text-indigo-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                    {{ $p1SessionCount ?? 0 }}
                                                </span>
                                                @if(($filterDosen ?? '') === 'p1')
                                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                @endif
                                            </div>
                                        </a>
                                    @endif

                                    <!-- 3. Pembimbing 2 -->
                                    @if($thesis->pembimbing2)
                                        <a href="{{ route('theses.logbooks', ['thesis' => $thesis->id, 'dosen' => 'p2']) }}"
                                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ ($filterDosen ?? '') === 'p2' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shrink-0"></span>
                                                <div class="truncate">
                                                    <span class="font-bold text-purple-600 dark:text-purple-400">P2:</span> 
                                                    <span>{{ $thesis->pembimbing2->name }}</span>
                                                    @if($thesis->pembimbing2_id === Auth::id())
                                                        <span class="ml-1 text-[10px] font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-950/60 px-1.5 py-0.5 rounded-md">(Anda)</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="min-w-[18px] h-4.5 px-1.5 inline-flex items-center justify-center rounded-full text-[10px] font-black {{ ($filterDosen ?? '') === 'p2' ? 'bg-purple-200/80 dark:bg-purple-500/30 text-purple-800 dark:text-purple-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                    {{ $p2SessionCount ?? 0 }}
                                                </span>
                                                @if(($filterDosen ?? '') === 'p2')
                                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Reset or Filter Active Indicator -->
                    @if($isDosenFiltered)
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 hidden sm:inline">
                                Menampilkan: <strong class="text-slate-700 dark:text-slate-200">{{ ($filterDosen ?? '') === 'p1' ? 'Pembimbing 1' : 'Pembimbing 2' }}</strong>
                            </span>
                            <a href="{{ route('theses.logbooks', ['thesis' => $thesis->id, 'dosen' => 'all']) }}" 
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-orange-200 dark:border-orange-800/60 transition-all shadow-2xs">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <span>Reset Filter</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/30">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Riwayat Logbook</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Catatan bimbingan dari Pembimbing 1 dan Pembimbing 2.</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('theses.logbooks.export-pdf', ['thesis' => $thesis->id, 'dosen' => ($filterDosen ?? 'all') !== 'all' ? $filterDosen : null]) }}" class="px-3 py-1.5 text-xs font-medium border border-slate-200 dark:border-slate-700 rounded text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm inline-flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Export PDF
                            </a>
                            <span class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-100 dark:border-emerald-800/50">
                                {{ $completedSessions->count() }} Sesi Ditampilkan
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="relative border-l-2 border-slate-100 dark:border-slate-700 ml-3 space-y-8">
                            @forelse($completedSessions as $session)
                                @php
                                    $isP1Session = ($session->dosen_id === $thesis->pembimbing1_id);
                                    $isP2Session = ($session->dosen_id === $thesis->pembimbing2_id);
                                    $isMySession = ($session->dosen_id === Auth::id());
                                    $roleTag = $isP1Session ? 'Pembimbing 1' : ($isP2Session ? 'Pembimbing 2' : 'Dosen');
                                    $badgeBg = $isP1Session 
                                        ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800' 
                                        : 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800';
                                @endphp
                                <div class="relative pl-6">
                                    <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full {{ $isP1Session ? 'bg-indigo-500' : 'bg-purple-500' }} ring-4 ring-white dark:ring-slate-800"></div>
                                    
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $isP1Session ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800' }}">
                                                Sesi #{{ $sessionOrderMap[$session->id] ?? ($loop->count - $loop->index) }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $badgeBg }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $isP1Session ? 'bg-indigo-500' : 'bg-purple-500' }}"></span>
                                                <span>{{ $roleTag }}: {{ $session->dosen->name ?? '-' }}</span>
                                                @if($isMySession)
                                                    <span class="text-[9px] font-black opacity-80">(Anda)</span>
                                                @endif
                                            </span>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                                            {{ $session->scheduled_at->format('d F Y • H:i') }} WIB
                                        </span>
                                    </div>

                                    <h4 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-1.5">{{ $session->topic }}</h4>

                                    <div class="flex items-start text-[11px] mb-2">
                                        @if($session->type === 'online')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-semibold mr-2 border border-blue-100 dark:border-blue-800/50">Online</span>
                                            @if($session->location)
                                                <a href="{{ Str::startsWith($session->location, 'http') ? $session->location : 'https://' . $session->location }}" target="_blank" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium" title="{{ $session->location }}">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                    Gabung Rapat
                                                </a>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold mr-2 border border-slate-200 dark:border-slate-600">Offline</span>
                                            @if($session->location)
                                                <span class="text-slate-500 dark:text-slate-400 truncate inline-block max-w-[250px]" title="{{ $session->location }}">{{ $session->location }}</span>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    <div class="bg-slate-50 dark:bg-slate-900 rounded-md p-4 border border-slate-100 dark:border-slate-700 text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-2 space-y-3">
                                        <!-- Co-Supervisor Note Callout Banner if session conducted by the other supervisor -->
                                        @if(!$isMySession && Auth::user()->role === 'dosen')
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $isP1Session ? 'bg-indigo-50/80 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/80' : 'bg-purple-50/80 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800/80' }} text-[11px] font-bold">
                                                <svg class="w-3.5 h-3.5 text-current shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>Catatan dari {{ $roleTag }} ({{ $session->dosen->name ?? '-' }})</span>
                                            </div>
                                        @endif

                                        <div>
                                            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Hasil & Catatan Bimbingan</div>
                                            <div class="font-medium text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ $session->feedback ?: 'Tidak ada catatan pembimbing untuk sesi ini.' }}</div>
                                            @if($session->feedback_document_url)
                                                <div class="mt-2">
                                                    <a href="{{ $session->feedback_document_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                        <span>Dokumen Feedback / Koreksi</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if($session->notes)
                                        <div class="pt-3 border-t border-slate-200 dark:border-slate-700">
                                            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Catatan Mahasiswa</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 italic">"{{ $session->notes }}"</div>
                                        </div>
                                        @endif

                                        @if($session->dosen)
                                            <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-1.5">
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tight">Dosen:</span>
                                                    <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200">{{ $session->dosen->name }}</span>
                                                </div>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $isP1Session ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50' : 'text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/50' }}">
                                                    {{ $roleTag }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="pl-6 text-slate-500 text-sm py-4">
                                    @if(($filterDosen ?? 'all') !== 'all')
                                        Tidak ada catatan bimbingan yang selesai untuk filter pembimbing ini.
                                    @else
                                        Mahasiswa ini belum memiliki riwayat bimbingan yang selesai.
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

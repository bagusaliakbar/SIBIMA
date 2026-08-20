<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Dashboard
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                    Sistem Informasi Bimbingan Mahasiswa
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- WhatsApp Bot Contact Save Banner -->
        @php
            $botNumberRaw = config('services.whatsapp.bot_number') ?: env('WHATSAPP_BOT_NUMBER', '');
            $botNumberFormatted = $botNumberRaw ? \App\Helpers\PhoneHelper::formatForWhatsApp($botNumberRaw) : null;
        @endphp
        <div x-data="{ dismissed: localStorage.getItem('hide_wa_bot_banner') === 'true' }" 
             x-show="!dismissed" 
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 rounded-2xl p-4 sm:p-5 text-white shadow-lg shadow-emerald-600/15 border border-emerald-400/30 relative overflow-hidden flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            
            <!-- Background glow -->
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex items-start sm:items-center gap-3.5 relative z-10">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center shrink-0 border border-white/30 shadow-inner">
                    <svg class="w-5 h-5 fill-current text-white" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="text-xs font-black tracking-wide uppercase text-white">Pemberitahuan Sistem WhatsApp Bot SIBIMA</h4>
                        <span class="px-2 py-0.5 rounded-full bg-white/20 text-[9px] font-black uppercase tracking-wider text-emerald-100 border border-white/20">Penting</span>
                    </div>
                    <p class="text-xs font-medium text-emerald-50 leading-relaxed mt-0.5">
                        Agar notifikasi bimbingan, revisi, & jadwal sidang masuk lancar tanpa terblokir, mohon <strong>simpan nomor WhatsApp Bot SIBIMA {{ $botNumberRaw ? "($botNumberRaw)" : '' }}</strong> ke kontak HP Anda.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-end sm:self-center shrink-0 relative z-10">
                @if($botNumberFormatted)
                    <a href="https://wa.me/{{ $botNumberFormatted }}?text={{ urlencode('Halo Bot SIBIMA, saya menyimpan nomor ini untuk menerima notifikasi akademik.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="px-3.5 py-1.5 bg-white hover:bg-emerald-50 text-emerald-800 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all shadow-sm hover:scale-105 active:scale-95 flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 fill-current text-emerald-600" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Chat / Simpan Bot</span>
                    </a>
                @endif
                <button @click="dismissed = true; localStorage.setItem('hide_wa_bot_banner', 'true')" 
                        type="button" 
                        class="p-1.5 text-emerald-100 hover:text-white hover:bg-white/10 rounded-lg transition-colors cursor-pointer" 
                        title="Tutup pemberitahuan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        @if(Auth::user()->role === 'mahasiswa')
            @php
                $isGraduated = $progress['isGraduated'];
                $progressPercent = $progress['percent'];
                $seminarDone = $progress['seminarDone'];
                $defenseDone = $progress['defenseDone'];
                $currentStage = $progress['currentStage'];
                $stages = $progress['stages'];
            @endphp

            @if($isStale)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 p-4 rounded-xl mb-6 flex items-center gap-4 animate-pulse">
                    <div class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-red-800 dark:text-red-200 uppercase tracking-tight">Peringatan Progress!</h4>
                        <p class="text-xs text-red-600 dark:text-red-400 font-medium">Anda belum melakukan bimbingan selama <b>{{ $daysSinceLastSession }} hari</b>. Segera hubungi pembimbing Anda!</p>
                    </div>
                </div>
            @endif

            @if(Auth::user()->is_critical_semester)
                <div class="bg-gradient-to-r from-red-600 to-rose-700 p-4 rounded-xl mb-6 flex items-center gap-4 shadow-lg shadow-red-200 dark:shadow-none">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-md text-white rounded-xl flex items-center justify-center flex-shrink-0 border border-white/30">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-black text-white uppercase tracking-wider">Peringatan Masa Studi Kritikal!</h4>
                        <p class="text-xs text-red-50 font-medium leading-relaxed">Saat ini Anda berada di <b>Semester {{ Auth::user()->current_semester }}</b>. Harap segera menyelesaikan skripsi Anda untuk menghindari potensi Drop Out (DO). Hubungi Koordinator Prodi jika memerlukan bantuan khusus.</p>
                    </div>
                    <div class="hidden md:block">
                        <span class="px-3 py-1 bg-white/10 text-white text-[10px] font-black uppercase tracking-widest rounded-full border border-white/20">Urgent</span>
                    </div>
                </div>
            @endif
            <!-- Road to Graduation (Premium Roadmap) -->
            <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-8 rounded-3xl shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-700/50 mb-8 relative overflow-hidden group">
                <!-- Decorative Elements -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-orange-500/5 rounded-full blur-3xl group-hover:bg-orange-500/10 transition-colors duration-700"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-colors duration-700"></div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12 relative z-10">
                    <div>
                        @if($thesis)
                            <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-1 max-w-2xl">{{ $thesis->title }}</h3>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Pembimbing: {{ $thesis->pembimbing1->name ?? 'Belum Ditentukan' }} & {{ $thesis->pembimbing2->name ?? 'Belum Ditentukan' }}</p>
                            @if(!$thesis->isAccSidangFinal())
                                <button onclick="document.getElementById('edit-thesis-modal').classList.remove('hidden')" class="inline-flex items-center px-3 py-1.5 bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-orange-200 dark:hover:bg-orange-500/20 transition-colors border border-orange-200 dark:border-orange-500/20 shadow-sm">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Revisi Judul / Deskripsi
                                </button>
                            @endif
                        @endif
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-5 w-full md:w-auto justify-between md:justify-end">
                        <a href="{{ route('student.history') }}" class="inline-flex flex-col items-center justify-center gap-1.5 group/btn">
                            <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center border border-slate-100 dark:border-slate-700 shadow-sm group-hover/btn:bg-indigo-600 group-hover/btn:text-white transition-all duration-300">
                                <svg class="w-5 h-5 text-indigo-600 group-hover/btn:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest group-hover/btn:text-indigo-600 transition-colors">Lihat Histori</span>
                        </a>
                        <div class="flex items-center justify-between w-full sm:w-auto gap-5 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                            <div class="text-right">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Capaian Saat Ini</p>
                                <p class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tighter">{{ round($progressPercent) }}%</p>
                            </div>
                            <div class="w-px h-10 bg-slate-200 dark:bg-slate-700"></div>
                            <div class="relative w-14 h-14">
                                <svg class="w-14 h-14 transform -rotate-90">
                                    <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="transparent" class="text-slate-200 dark:text-slate-800" />
                                    <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="transparent" stroke-dasharray="150.7" stroke-dashoffset="{{ 150.7 - (150.7 * $progressPercent / 100) }}" class="text-orange-600 transition-all duration-1000 ease-out" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto pb-6 -mx-4 px-4 sm:mx-0 sm:px-0 hide-scrollbar">
                    <div class="relative pt-4 pb-4 min-w-[500px] sm:min-w-0">
                        <!-- Progress Line (Background) -->
                        <div class="absolute top-[42px] left-0 right-0 h-1.5 bg-slate-100 dark:bg-slate-900 rounded-full"></div>
                        <!-- Progress Line (Active) -->
                        <div class="absolute top-[42px] left-0 h-1.5 bg-gradient-to-r from-orange-600 via-orange-500 to-amber-400 rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(249,115,22,0.4)]" style="width: {{ max(0, min(100, (($currentStage - 1) / (count($stages) - 1)) * 100)) }}%"></div>

                        <div class="relative flex justify-between gap-2">
                        @foreach($stages as $index => $stage)
                            @php 
                                $stageNum = $index + 1;
                                $isActive = $currentStage >= $stageNum;
                                $isCurrent = $currentStage == $stageNum;
                                
                                // Final stage logic: if currentStage is 6, then stage 6 is completed
                                $statusLabel = 'Pending';
                                if ($isActive) {
                                    if ($isCurrent) {
                                        $statusLabel = ($stageNum == 6 && $isGraduated) ? 'Completed' : 'In Progress';
                                    } else {
                                        $statusLabel = 'Completed';
                                    }
                                }
                            @endphp
                            <div class="flex flex-col items-center flex-1">
                                <div class="relative group/step">
                                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center z-10 transition-all duration-500 relative
                                        {{ $isActive ? 'bg-orange-600 text-white shadow-2xl shadow-orange-900/30' : 'bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-300 dark:text-slate-600' }}
                                        {{ $isCurrent ? 'ring-4 ring-orange-500/20 scale-110' : '' }}">
                                        
                                        @if($stageNum == 1)
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        @elseif($stageNum == 2)
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        @elseif($stageNum == 3)
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v4h4"></path></svg>
                                        @elseif($stageNum == 4)
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                        @elseif($stageNum == 5)
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"></path></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>
                                        @endif

                                        @if($isActive && !($isCurrent && $statusLabel != 'Completed'))
                                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center border-2 border-white dark:border-slate-800 shadow-sm">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Tooltip / Label -->
                                    <div class="mt-4 text-center">
                                        <h4 class="text-[11px] font-black uppercase tracking-tight {{ $isActive ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400' }}">{{ $stage['name'] }}</h4>
                                        <p class="text-[8px] font-bold {{ $isActive ? 'text-orange-500' : 'text-slate-400' }} leading-none mt-1 opacity-70">{{ $statusLabel }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

                <!-- Footer Insight -->
                <div class="mt-6 pt-6 border-t border-slate-50 dark:border-slate-700/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 italic">
                        @if($currentStage == 1)
                            Judul sudah diajukan! mohon untuk menunggu <span class="text-orange-600 font-bold">Persetujuan</span> dan matangkan konsep penelitiannya untuk didiskusikan dengan dosen pembimbing.
                        @elseif($currentStage == 2)
                            Anda sedang dalam tahap bimbingan. Terus perbaiki draf proposal (Bab 1-3) Anda hingga mendapatkan <span class="text-orange-600 font-bold">ACC Seminar</span> dari dosen pembimbing.
                        @elseif($currentStage == 3)
                            Selamat atas seminarnya! Segera revisi dan lanjutkan ke tahap <span class="text-orange-600 font-bold">Penelitian</span> untuk melengkapi bab 4 dan 5.
                        @elseif($currentStage == 4)
                            Fokus pada pengolahan data dan penyusunan <span class="text-orange-600 font-bold">Bab 4-5</span>. Anda sudah semakin dekat dengan sidang akhir!
                        @elseif($currentStage == 5)
                            Tahap krusial! Persiapkan materi presentasi Anda sebaik mungkin untuk menghadapi <span class="text-orange-600 font-bold">Sidang Akhir</span>.
                        @elseif($currentStage == 6)
                            Luar biasa! Anda telah menyelesaikan seluruh rintangan. Selamat atas status <span class="text-emerald-600 font-bold">LULUS</span> Anda!
                        @else
                            Silakan ajukan judul skripsi untuk memulai perjalanan akademik Anda di SIBIMA.
                        @endif
                    </p>
                </div>
            </div>

            @if($isGraduated)
            <div class="bg-slate-900 dark:bg-slate-800/80 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden mb-8 border border-white/10 backdrop-blur-md">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500/10 rounded-full -ml-24 -mb-24 blur-3xl"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-3xl font-black tracking-tight mb-3 bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Selamat, {{ Auth::user()->name }}!</h2>
                        <p class="text-slate-400 font-medium text-base leading-relaxed max-w-2xl">
                            Seluruh tahapan skripsi telah Anda selesaikan dengan baik. Anda kini dinyatakan <span class="text-amber-400 font-bold">LULUS</span>. Terima kasih atas dedikasi dan kerja keras Anda selama ini.
                        </p>
                    </div>

                    <div class="hidden lg:block w-px h-24 bg-gradient-to-b from-transparent via-white/10 to-transparent"></div>

                    <div class="text-center md:text-right">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tanggal Yudisium</p>
                        <p class="text-xl font-black text-white tracking-tighter">{{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
            </div>
            @endif
        @endif
        <!-- Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if(Auth::user()->role === 'mahasiswa')
                <!-- Stats Mahasiswa -->
                <x-stat-card title="Status Skripsi" :value="$thesis ? ($thesis->status === 'completed' ? 'Lulus' : ($thesis->status === 'active' ? 'Aktif' : 'Menunggu')) : 'Belum Ada'" color="emerald">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card title="Pembimbing" :value="$thesis && $thesis->pembimbing1 && $thesis->pembimbing2 ? '' : 'Belum Ada'" color="orange">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </x-slot>
                    @if($thesis && $thesis->pembimbing1 && $thesis->pembimbing2)
                        <x-slot name="subtitle">
                            <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate" title="{{ $thesis->pembimbing1->name }}">1. {{ $thesis->pembimbing1->name }}</p>
                            <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate" title="{{ $thesis->pembimbing2->name }}">2. {{ $thesis->pembimbing2->name }}</p>
                        </x-slot>
                    @endif
                </x-stat-card>

                <x-stat-card title="Sesi Selesai" color="blue">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </x-slot>
                    <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ $pastSessionsCount ?? 0 }} <span class="text-xs font-medium text-slate-400 lowercase tracking-normal">Sesi</span></h2>
                    @if($thesis && $thesis->pembimbing1 && $thesis->pembimbing2)
                        <x-slot name="subtitle">
                            <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                <span>P1: {{ Str::limit($thesis->pembimbing1->name, 50) }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $pastSessionsCountP1 }} Sesi</span>
                            </p>
                            <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                <span>P2: {{ Str::limit($thesis->pembimbing2->name, 50) }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $pastSessionsCountP2 }} Sesi</span>
                            </p>
                        </x-slot>
                    @else
                        <x-slot name="subtitle">
                            <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500 italic mt-1">Belum ada pembimbing</p>
                        </x-slot>
                    @endif
                </x-stat-card>

                <x-stat-card title="Progres Keseluruhan" :value="round($progressPercent) . '%'" color="indigo">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </x-slot>
                </x-stat-card>
            @else
                <!-- Stats Admin/Dosen -->
                <x-stat-card :title="(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') ? 'Total Skripsi' : 'Mhs Bimbingan'" :value="$activeThesesCount ?? 0" color="orange">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </x-slot>
                    @if(Auth::user()->role === 'dosen')
                        <x-slot name="subtitle">
                            <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                <span>Pembimbing 1</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $totalActiveStudentsP1 ?? 0 }} Mahasiswa</span>
                            </p>
                            <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                <span>Pembimbing 2</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $totalActiveStudentsP2 ?? 0 }} Mahasiswa</span>
                            </p>
                        </x-slot>
                    @endif
                </x-stat-card>

                <x-stat-card title="Jadwal Minggu Ini" color="blue">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </x-slot>
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 mt-1">{{ $sessionsThisWeek ?? 0 }} <span class="text-xs font-medium text-slate-400 lowercase tracking-normal">Sesi</span></h2>
                    @if(Auth::user()->role === 'dosen')
                        <x-slot name="subtitle">
                            <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                <span>Menunggu</span>
                                <span class="font-bold text-amber-600 dark:text-amber-400">{{ $pendingSessionsThisWeek ?? 0 }}</span>
                            </p>
                            <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex justify-between">
                                <span>Disetujui</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $approvedSessionsThisWeek ?? 0 }}</span>
                            </p>
                        </x-slot>
                    @endif
                </x-stat-card>

                <x-stat-card title="Sesi Selesai" :value="$totalCompletedSessions ?? 0" color="emerald">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </x-slot>
                    <p class="text-[10px] font-medium text-slate-400 mt-1 italic tracking-tight">Total seluruh bimbingan</p>
                </x-stat-card>

                <x-stat-card title="Progres Rata-rata" :value="($averageStudentProgress ?? 0) . '%'" color="pink">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </x-slot>
                    <p class="text-[10px] font-medium text-slate-400 mt-1 italic tracking-tight">Performa bimbingan global</p>
                </x-stat-card>
            @endif
        </div>

        @if(Auth::user()->role !== 'mahasiswa')
            <!-- Analytical Dashboard for Admin/Dosen -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Chart 1: Distribution -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                            {{ (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') ? 'Status Skripsi Global' : 'Distribusi Progres Bimbingan' }}
                        </h3>
                        <div class="h-64">
                            <canvas id="distributionChart"></canvas>
                        </div>
                        @if(Auth::user()->role === 'dosen')
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/50 space-y-2">
                            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Keterangan Progres:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                                <div class="flex items-start gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mt-0.5 flex-shrink-0"></span>
                                    <span><strong>Judul:</strong> Mahasiswa aktif yang belum memulai bimbingan.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mt-0.5 flex-shrink-0"></span>
                                    <span><strong>Bimbingan:</strong> Mahasiswa aktif yang sedang dalam proses bimbingan reguler.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-0.5 flex-shrink-0"></span>
                                    <span><strong>ACC Seminar:</strong> Sudah di-ACC Seminar UP oleh kedua pembimbing.</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 mt-0.5 flex-shrink-0"></span>
                                    <span><strong>ACC Sidang:</strong> Sudah di-ACC Sidang Akhir oleh kedua pembimbing.</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Chart 2: Trends -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 dark:bg-blue-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            Tren Aktivitas Bimbingan
                        </h3>
                        <div class="h-64">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
            <!-- Advanced Analytics Section -->
            <div class="mb-10 mt-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Score Distribution -->
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                Distribusi Nilai Akhir (Grade)
                            </h3>
                            <div class="h-64">
                                <canvas id="scoreDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Wave Duration -->
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Lama Pengerjaan per Gelombang (Bulan)
                            </h3>
                            <div class="h-64">
                                <canvas id="waveDurationChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- On-time Graduation -->
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 dark:bg-indigo-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Ketepatan Waktu Lulus
                            </h3>
                            <div class="h-64">
                                <canvas id="onTimeGraduationChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Student Health -->
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 dark:bg-rose-900/10 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Kesehatan Masa Studi Mahasiswa
                            </h3>
                            <div class="h-64">
                                <canvas id="studentHealthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                </div>

                <!-- Topic Trend Analysis (AI Insights) -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-8 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300 mb-6">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                            <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.989-2.386l-.548-.547z"></path></svg>
                                Analisa Tren Topik (AI Insights)
                            </h3>
                            <div class="px-3 py-1 bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase tracking-wider rounded-lg border border-orange-200 dark:border-orange-500/20">
                                Berbasis Data Tren
                            </div>
                        </div>
                        <div class="h-96">
                            <canvas id="topicTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Cohort Completion (Full Width) -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-8 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300 mb-6">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/10 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-8 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            Rata-rata Masa Studi per Angkatan (Tahun)
                        </h3>
                        <div class="h-80">
                            <canvas id="cohortCompletionChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Dosen Workload (Full Width) -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-8 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 dark:bg-emerald-900/10 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-8 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Dosen dengan Beban Bimbingan Terbanyak (Top 10)
                        </h3>
                        <div class="h-80">
                            <canvas id="workloadChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

        @if(Auth::user()->role === 'mahasiswa' && ($mySeminarSchedule || $myDefenseSchedule))
            {{-- Student Schedule Section (already here) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if($mySeminarSchedule)
                    @php
                        $isSeminarFinished = ($progress['currentStage'] >= 4)
                            || $mySeminarSchedule->isGraded()
                            || \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->isPast();
                    @endphp
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 {{ $isSeminarFinished ? 'bg-emerald-50 dark:bg-emerald-950/10' : 'bg-orange-50 dark:bg-orange-900/10' }} rounded-full -mr-12 -mt-12 opacity-50"></div>
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center">
                                    <svg class="w-4 h-4 mr-2 {{ $isSeminarFinished ? 'text-emerald-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Jadwal Seminar Skripsi
                                </h3>
                                @if($isSeminarFinished)
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-emerald-200">Selesai</span>
                                @else
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-orange-200">Terjadwal</span>
                                @endif
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-slate-50 dark:bg-slate-900/50 rounded-xl flex flex-col items-center justify-center border border-slate-100 dark:border-slate-700">
                                        <span class="text-[10px] font-black text-slate-400 uppercase leading-none">{{ \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->locale('id')->translatedFormat('M') }}</span>
                                        <span class="text-lg font-black text-slate-800 dark:text-slate-100 leading-none mt-1">{{ \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->format('d') }}</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Hari & Tanggal</p>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100">{{ \Carbon\Carbon::parse($mySeminarSchedule->schedule->date)->locale('id')->translatedFormat('l, d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Waktu</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($mySeminarSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($mySeminarSchedule->end_time)->format('H:i') }} WIB</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Ruangan/Tempat</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $mySeminarSchedule->schedule->location ?: '-' }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Dosen Pembimbing</p>
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 space-y-0.5">
                                            <p><span class="text-[10px] text-slate-400">P1:</span> {{ $mySeminarSchedule->thesis->pembimbing1->name ?? $thesis->pembimbing1->name ?? '-' }}</p>
                                            @if(($mySeminarSchedule->thesis->pembimbing2->name ?? $thesis->pembimbing2->name ?? false))
                                                <p><span class="text-[10px] text-slate-400">P2:</span> {{ $mySeminarSchedule->thesis->pembimbing2->name ?? $thesis->pembimbing2->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Dosen Penguji</p>
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 space-y-0.5">
                                            <p><span class="text-[10px] text-slate-400">U1:</span> {{ $mySeminarSchedule->examiner1->name ?? '-' }}</p>
                                            @if($mySeminarSchedule->examiner2)
                                                <p><span class="text-[10px] text-slate-400">U2:</span> {{ $mySeminarSchedule->examiner2->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($mySeminarSchedule->schedule->meeting_link && !$isSeminarFinished)
                                    <div class="pt-2">
                                        <a href="{{ $mySeminarSchedule->schedule->meeting_link }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors shadow-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            Gabung Sekarang
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if($myDefenseSchedule)
                    @php
                        $isDefenseFinished = ($progress['currentStage'] >= 6)
                            || $myDefenseSchedule->isGraded()
                            || \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->isPast();
                    @endphp
                    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 dark:bg-emerald-950/10 rounded-full -mr-12 -mt-12 opacity-50"></div>
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Jadwal Sidang Skripsi
                                </h3>
                                @if($isDefenseFinished)
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-emerald-200">Selesai</span>
                                @else
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-indigo-200">Terjadwal</span>
                                @endif
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-slate-50 dark:bg-slate-900/50 rounded-xl flex flex-col items-center justify-center border border-slate-100 dark:border-slate-700">
                                        <span class="text-[10px] font-black text-slate-400 uppercase leading-none">{{ \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->locale('id')->translatedFormat('M') }}</span>
                                        <span class="text-lg font-black text-slate-800 dark:text-slate-100 leading-none mt-1">{{ \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->format('d') }}</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Hari & Tanggal</p>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100">{{ \Carbon\Carbon::parse($myDefenseSchedule->schedule->date)->locale('id')->translatedFormat('l, d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Waktu</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($myDefenseSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($myDefenseSchedule->end_time)->format('H:i') }} WIB</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Ruangan/Tempat</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $myDefenseSchedule->schedule->location ?: '-' }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Dosen Pembimbing</p>
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 space-y-0.5">
                                            <p><span class="text-[10px] text-slate-400">P1:</span> {{ $myDefenseSchedule->thesis->pembimbing1->name ?? $thesis->pembimbing1->name ?? '-' }}</p>
                                            @if(($myDefenseSchedule->thesis->pembimbing2->name ?? $thesis->pembimbing2->name ?? false))
                                                <p><span class="text-[10px] text-slate-400">P2:</span> {{ $myDefenseSchedule->thesis->pembimbing2->name ?? $thesis->pembimbing2->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Dosen Penguji</p>
                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 space-y-0.5">
                                            <p><span class="text-[10px] text-slate-400">U1:</span> {{ $myDefenseSchedule->examiner1->name ?? '-' }}</p>
                                            @if($myDefenseSchedule->examiner2)
                                                <p><span class="text-[10px] text-slate-400">U2:</span> {{ $myDefenseSchedule->examiner2->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($myDefenseSchedule->schedule->meeting_link && !$isDefenseFinished)
                                    <div class="pt-2">
                                        <a href="{{ $myDefenseSchedule->schedule->meeting_link }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors shadow-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            Join Online Meeting
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if(Auth::user()->role === 'dosen' && ($examinerSeminarSchedules->count() > 0 || $examinerDefenseSchedules->count() > 0))
            <x-table-card 
                title="Jadwal Menguji Seminar & Sidang"
                subtitle="Daftar Mahasiswa yang akan Anda uji."
                class="mb-6">
                
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-50 dark:border-slate-700">
                            <th class="px-6 py-3">Mahasiswa</th>
                            <th class="px-6 py-3">Posisi</th>
                            <th class="px-6 py-3">Jenis Ujian</th>
                            <th class="px-6 py-3">Waktu & Tempat</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                        @foreach($examinerSeminarSchedules as $detail)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-slate-100 text-xs">{{ $detail->thesis->student->name }}</p>
                                        <p class="text-[10px] text-slate-400 italic mt-0.5 line-clamp-1">{{ $detail->thesis->title }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($detail->thesis->pembimbing1_id == Auth::id())
                                        <x-status-badge type="blue" label="Pembimbing 1" size="sm" />
                                    @elseif($detail->thesis->pembimbing2_id == Auth::id())
                                        <x-status-badge type="blue" label="Pembimbing 2" size="sm" />
                                    @elseif($detail->examiner1_id == Auth::id())
                                        <x-status-badge type="indigo" label="Penguji 1" size="sm" />
                                    @elseif($detail->examiner2_id == Auth::id())
                                        <x-status-badge type="indigo" label="Penguji 2" size="sm" />
                                    @else
                                        <x-status-badge type="indigo" label="Penguji" size="sm" />
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <x-status-badge type="orange" label="Seminar" size="sm" />
                                        @php
                                            $isFinished = ($detail->thesis && $detail->thesis->seminarApplication && $detail->thesis->seminarApplication->status === 'completed')
                                                || $detail->isGraded()
                                                || $detail->isAllRevisionsApproved()
                                                || \Carbon\Carbon::parse($detail->schedule->date)->isPast();
                                        @endphp
                                        @if($isFinished)
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-emerald-200">Selesai</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-[11px]">
                                        <p class="font-bold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}</p>
                                        <p class="text-slate-400 font-medium mt-0.5">{{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} WIB @ {{ $detail->schedule->location ?: '-' }}</p>
                                        @if($detail->schedule->meeting_link && !$isFinished)
                                            <a href="{{ $detail->schedule->meeting_link }}" target="_blank" class="text-[9px] text-blue-600 dark:text-blue-400 font-black flex items-center mt-1 hover:underline">
                                                <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                Link Google Meet
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('seminar-examiner.show', $detail->id) }}" class="inline-flex items-center px-3 py-1 bg-slate-100 dark:bg-slate-700 hover:bg-orange-600 hover:text-white text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded transition-all">
                                        Buka Lembar Revisi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @foreach($examinerDefenseSchedules as $detail)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-slate-100 text-xs">{{ $detail->thesis->student->name }}</p>
                                        <p class="text-[10px] text-slate-400 italic mt-0.5 line-clamp-1">{{ $detail->thesis->title }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($detail->thesis->pembimbing1_id == Auth::id())
                                        <x-status-badge type="blue" label="Pembimbing 1" size="sm" />
                                    @elseif($detail->thesis->pembimbing2_id == Auth::id())
                                        <x-status-badge type="blue" label="Pembimbing 2" size="sm" />
                                    @elseif($detail->examiner1_id == Auth::id())
                                        <x-status-badge type="indigo" label="Penguji 1" size="sm" />
                                    @elseif($detail->examiner2_id == Auth::id())
                                        <x-status-badge type="indigo" label="Penguji 2" size="sm" />
                                    @else
                                        <x-status-badge type="indigo" label="Penguji" size="sm" />
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <x-status-badge type="emerald" label="Sidang" size="sm" />
                                        @php
                                            $isFinished = ($detail->thesis && $detail->thesis->defenseApplication && $detail->thesis->defenseApplication->status === 'completed')
                                                || $detail->isGradingComplete()
                                                || $detail->isRevisionAllApproved()
                                                || \Carbon\Carbon::parse($detail->schedule->date)->isPast();
                                        @endphp
                                        @if($isFinished)
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-emerald-200">Selesai</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-[11px]">
                                        <p class="font-bold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d M Y') }}</p>
                                        <p class="text-slate-400 font-medium mt-0.5">{{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} WIB @ {{ $detail->schedule->location ?: '-' }}</p>
                                        @if($detail->schedule->meeting_link && !$isFinished)
                                            <a href="{{ $detail->schedule->meeting_link }}" target="_blank" class="text-[9px] text-blue-600 dark:text-blue-400 font-black flex items-center mt-1 hover:underline">
                                                <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                Link Google Meet
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('defense-examiner.show', $detail->id) }}" class="inline-flex items-center px-3 py-1 bg-slate-100 dark:bg-slate-700 hover:bg-emerald-600 hover:text-white text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded transition-all">
                                        Buka Lembar Penilaian
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-table-card>
        @endif
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Left Column: Main Feed -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Upcoming Sessions Table -->
                <x-table-card 
                    title="Jadwal Bimbingan Terdekat"
                    subtitle="Pantau jadwal yang telah disetujui atau menunggu konfirmasi.">
                    
                    <x-slot name="headerActions">
                        <a href="{{ route('mentoring-sessions.index') }}" class="text-xs font-bold text-orange-600 hover:underline flex items-center">
                            Lihat Semua
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </x-slot>

                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-50 dark:border-slate-700">
                                <th class="px-6 py-3">Topik & Mahasiswa</th>
                                <th class="px-6 py-3">Waktu Pelaksanaan</th>
                                <th class="px-6 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                            @forelse(isset($upcomingSessions) ? $upcomingSessions : [] as $session)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors cursor-pointer group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-600 flex items-center justify-center mr-3 font-bold text-xs">
                                            {{ substr($session->topic, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-700 dark:text-slate-200 text-xs">{{ $session->topic }}</h4>
                                            @if(Auth::user()->role !== 'mahasiswa')
                                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $session->thesis->student->name }}</p>
                                            @else
                                                <p class="text-[10px] text-slate-400 mt-0.5">Dosen: {{ $session->dosen->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-600">{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $session->scheduled_at->format('H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <x-status-badge 
                                        :type="$session->status === 'approved' ? 'emerald' : 'orange'" 
                                        :label="$session->status === 'approved' ? 'Disetujui' : 'Menunggu'" />
                                </td>
                            </tr>
                            @empty
                                <x-empty-state colspan="3" description="Belum ada jadwal bimbingan terdekat." icon="clock" />
                            @endforelse
                        </tbody>
                    </table>
                </x-table-card>

                <!-- Recent Activity / Logbook -->
                @if(Auth::user()->role !== 'mahasiswa')
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden transition-all duration-300">
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Logbook Terbaru</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative border-l border-slate-100 dark:border-slate-700 ml-3 space-y-8">
                            @forelse(isset($recentLogbooks) ? $recentLogbooks : [] as $logbook)
                            <div class="relative pl-7 group">
                                <div class="absolute -left-[5px] top-1.5 w-2.5 h-2.5 rounded-full bg-orange-500 ring-4 ring-orange-50 dark:ring-slate-900 transition-all group-hover:scale-125"></div>
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                                    <h4 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">{{ $logbook->topic }}</h4>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $logbook->scheduled_at->locale('id')->translatedFormat('d M Y • H:i') }} WIB</span>
                                </div>
                                
                                <div class="space-y-2.5 text-[11px]">
                                    <!-- Logbook Details Box -->
                                    <div class="bg-slate-50/50 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-800/80 rounded-xl p-3 space-y-2">
                                        @if($logbook->notes)
                                            <div class="flex items-start gap-1.5">
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider select-none shrink-0 w-16">Catatan:</span>
                                                <p class="text-slate-600 dark:text-slate-300 font-medium leading-relaxed italic">"{{ $logbook->notes }}"</p>
                                            </div>
                                        @endif

                                        @if($logbook->feedback)
                                            <div class="flex items-start gap-1.5 pt-2 {{ $logbook->notes ? 'border-t border-slate-100 dark:border-slate-800/50' : '' }}">
                                                <span class="text-[9px] font-black text-orange-500 uppercase tracking-wider select-none shrink-0 w-16">Feedback:</span>
                                                <p class="text-slate-800 dark:text-slate-200 font-bold leading-relaxed">{{ $logbook->feedback }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Metadata footer -->
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-tight">
                                        @if(Auth::user()->role === 'mahasiswa')
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                                <span>Dosen: <span class="text-slate-600 dark:text-slate-400 font-black">{{ $logbook->dosen->name ?? '-' }}</span> ({{ $logbook->dosen_id === $logbook->thesis->pembimbing1_id ? 'Pembimbing 1' : ($logbook->dosen_id === $logbook->thesis->pembimbing2_id ? 'Pembimbing 2' : 'Dosen') }})</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                                <span>Mahasiswa: <span class="text-slate-600 dark:text-slate-400 font-black">{{ $logbook->thesis->student->name }}</span></span>
                                            </div>
                                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                                                <div class="flex items-center gap-1.5 border-l border-slate-200 dark:border-slate-700 pl-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                                    <span>Dosen: <span class="text-slate-600 dark:text-slate-400 font-black">{{ $logbook->dosen->name ?? '-' }}</span> ({{ $logbook->dosen_id === $logbook->thesis->pembimbing1_id ? 'Pembimbing 1' : ($logbook->dosen_id === $logbook->thesis->pembimbing2_id ? 'Pembimbing 2' : 'Dosen') }})</span>
                                                </div>
                                            @endif
                                        @endif
                                        
                                        @if($logbook->document_path)
                                            <div class="flex items-center gap-1.5 border-l border-slate-200 dark:border-slate-700 pl-4">
                                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                <a href="{{ route('download.private', ['path' => $logbook->document_path]) }}" target="_blank" class="text-orange-600 hover:text-orange-700 transition-colors font-black hover:underline" title="{{ $logbook->document_original_name }}">Unduh Berkas</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-100 dark:border-slate-800">
                                    <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Belum ada catatan logbook terbaru.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @if(method_exists($recentLogbooks, 'links') && $recentLogbooks->hasPages())
                        <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/30 border-t border-slate-100 dark:border-slate-700/50">
                            {{ $recentLogbooks->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-6">
                <!-- Live Online Users Card (Admin & Kaprodi & Dosen) -->
                @if(Auth::user()->role !== 'mahasiswa')
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden transition-all duration-300">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/50 bg-gradient-to-r from-emerald-500/10 via-slate-50 to-emerald-500/5 dark:from-emerald-950/20 dark:to-slate-900 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse shadow-xs"></span>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">Pengguna Online</h3>
                        </div>
                        <span id="online-users-count-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50">
                            {{ $onlineUsersCount }} Aktif
                        </span>
                    </div>

                    <!-- Role Breakdown Pills -->
                    <div class="px-5 py-2.5 bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] gap-1 font-bold">
                        <span class="text-blue-600 dark:text-blue-400">Mhs: <strong id="online-count-mhs">{{ $onlineUsersByRole['mahasiswa'] ?? 0 }}</strong></span>
                        <span class="text-indigo-600 dark:text-indigo-400">Dosen: <strong id="online-count-dosen">{{ $onlineUsersByRole['dosen'] ?? 0 }}</strong></span>
                        <span class="text-orange-600 dark:text-orange-400">Admin/Kaprodi: <strong id="online-count-admin">{{ ($onlineUsersByRole['admin'] ?? 0) + ($onlineUsersByRole['kaprodi'] ?? 0) }}</strong></span>
                    </div>

                    <!-- Online Users List -->
                    <div class="p-3 max-h-72 overflow-y-auto space-y-2" id="online-users-list">
                        @forelse($onlineUsers as $u)
                        <div class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors group">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="relative w-10 h-10 shrink-0">
                                    <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-xs bg-slate-100 dark:bg-slate-800">
                                        <img src="{{ $u->avatar_url }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full shadow-xs z-10" title="Online"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate group-hover:text-orange-600 transition-colors">{{ $u->name }}</p>
                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ $u->role }}</span>
                                </div>
                            </div>
                            @if($u->id !== Auth::id())
                            <a href="{{ route('chat.show', $u->id) }}" class="p-1.5 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 hover:bg-orange-500 hover:text-white transition-all shrink-0" title="Kirim Pesan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </a>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <p class="text-xs text-slate-400 font-medium italic">Tidak ada pengguna aktif lain saat ini.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endif

                <!-- Announcements Card -->
                <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden transition-all duration-300">
                    <div class="px-5 py-4 border-b border-slate-50 dark:border-slate-700/50 bg-slate-800 dark:bg-slate-800 text-white flex justify-between items-center">
                        <h3 class="text-[10px] font-extrabold uppercase tracking-widest">Papan Informasi</h3>
                        <span class="p-1 bg-white/10 rounded">
                            <svg class="w-3.5 h-3.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </span>
                    </div>
                    <div class="divide-y divide-slate-50 dark:divide-slate-700">
                        @forelse($announcements as $announcement)
                        <div class="p-5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <div class="flex items-center gap-2 mb-2">
                                @php
                                    $color = $announcement->type === 'important' ? 'red' : ($announcement->type === 'warning' ? 'orange' : 'blue');
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-tighter border {{ $color === 'red' ? 'bg-red-100 text-red-600 border-red-200' : ($color === 'orange' ? 'bg-orange-100 text-orange-600 border-orange-200' : 'bg-blue-100 text-blue-600 border-blue-200') }}">
                                    {{ $announcement->type }}
                                </span>
                                <span class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter">{{ $announcement->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-tight mb-1">{{ $announcement->title }}</p>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-wrap">{{ $announcement->content }}</div>
                        </div>
                        @empty
                        <div class="p-10 text-center">
                            <svg class="w-10 h-10 text-slate-100 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-xs text-slate-400 italic">Belum ada pengumuman.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = document.documentElement.classList.contains('dark');
            @if(Auth::user()->role !== 'mahasiswa')
                // Distribution Chart
                const distCtx = document.getElementById('distributionChart').getContext('2d');
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    new Chart(distCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Aktif', 'Selesai', 'Menunggu'],
                            datasets: [{
                                data: [{{ $thesisStatusCounts['active'] ?? 0 }}, {{ $thesisStatusCounts['completed'] ?? 0 }}, {{ $thesisStatusCounts['pending'] ?? 0 }}],
                                backgroundColor: ['#f97316', '#10b981', '#6366f1'],
                                borderWidth: 0,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'bottom', 
                                    labels: { 
                                        usePointStyle: true, 
                                        padding: 20, 
                                        font: { weight: 'bold', size: 10 },
                                        color: darkMode ? '#94a3b8' : '#64748b'
                                    } 
                                } 
                            },
                            cutout: '75%'
                        }
                    });
                @else
                    new Chart(distCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_keys($studentProgressDistribution)) !!},
                            datasets: [{
                                label: 'Mahasiswa',
                                data: {!! json_encode(array_values($studentProgressDistribution)) !!},
                                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#6366f1'],
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { 
                                y: { 
                                    beginAtZero: true, 
                                    grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b', stepSize: 1 } 
                                }, 
                                x: { 
                                    grid: { display: false },
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b' }
                                } 
                            },
                            plugins: { legend: { display: false } }
                        }
                    });
                @endif

                // Activity Chart
                const actCtx = document.getElementById('activityChart').getContext('2d');
                new Chart(actCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_keys($monthlyMentoringCounts)) !!},
                        datasets: [{
                            label: 'Sesi Selesai',
                            data: {!! json_encode(array_values($monthlyMentoringCounts)) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.05)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#3b82f6',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { 
                            y: { 
                                beginAtZero: true, 
                                grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                ticks: { color: darkMode ? '#94a3b8' : '#64748b', stepSize: 1 } 
                            }, 
                            x: { 
                                grid: { display: false },
                                ticks: { color: darkMode ? '#94a3b8' : '#64748b' }
                            } 
                        },
                        plugins: { legend: { display: false } }
                    }
                });

                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    // Workload Chart
                    const workCtx = document.getElementById('workloadChart').getContext('2d');
                    new Chart(workCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_keys($dosenWorkload)) !!},
                            datasets: [{
                                label: 'Jumlah Mahasiswa',
                                data: {!! json_encode(array_values($dosenWorkload)) !!},
                                backgroundColor: '#10b981',
                                borderRadius: 8,
                                barThickness: 30
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { 
                                x: { 
                                    beginAtZero: true, 
                                    grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b', stepSize: 1 } 
                                }, 
                                y: { 
                                    grid: { display: false },
                                    ticks: { 
                                        color: darkMode ? '#f1f5f9' : '#1e293b',
                                        font: { weight: 'bold', size: 11 }
                                    }
                                } 
                            },
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: darkMode ? '#1e293b' : '#fff',
                                    titleColor: darkMode ? '#fff' : '#1e293b',
                                    bodyColor: darkMode ? '#cbd5e1' : '#64748b',
                                    borderColor: darkMode ? '#334155' : '#e2e8f0',
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: false
                                }
                            }
                        }
                    });

                    // Score Distribution Chart
                    const scoreDistCtx = document.getElementById('scoreDistributionChart').getContext('2d');
                    new Chart(scoreDistCtx, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode(array_keys($scoreDistribution)) !!},
                            datasets: [{
                                data: {!! json_encode(array_values($scoreDistribution)) !!},
                                backgroundColor: ['#10b981', '#6366f1', '#f59e0b', '#f97316', '#ef4444'],
                                borderWidth: 0,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'right', 
                                    labels: { 
                                        usePointStyle: true, 
                                        padding: 20, 
                                        font: { weight: 'bold', size: 10 },
                                        color: darkMode ? '#94a3b8' : '#64748b'
                                    } 
                                } 
                            },
                            cutout: '70%'
                        }
                    });

                    // Wave Duration Chart
                    const waveDurCtx = document.getElementById('waveDurationChart').getContext('2d');
                    new Chart(waveDurCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode(array_keys($waveDurationStats)) !!},
                            datasets: [{
                                label: 'Rata-rata (Bulan)',
                                data: {!! json_encode(array_values($waveDurationStats)) !!},
                                borderColor: '#f97316',
                                backgroundColor: 'rgba(249, 115, 22, 0.05)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#f97316',
                                pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { 
                                y: { 
                                    beginAtZero: true, 
                                    grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b' } 
                                }, 
                                x: { 
                                    grid: { display: false },
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b' }
                                } 
                            },
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y + ' Bulan';
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // On-time Graduation Chart
                    const onTimeCtx = document.getElementById('onTimeGraduationChart').getContext('2d');
                    new Chart(onTimeCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Tepat Waktu', 'Terlambat'],
                            datasets: [{
                                data: [{{ $onTimeStats['Tepat Waktu'] }}, {{ $onTimeStats['Terlambat'] }}],
                                backgroundColor: ['#10b981', '#ef4444'],
                                borderWidth: 0,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'bottom', 
                                    labels: { 
                                        usePointStyle: true, 
                                        padding: 20, 
                                        font: { weight: 'bold', size: 10 },
                                        color: darkMode ? '#94a3b8' : '#64748b'
                                    } 
                                } 
                            },
                            cutout: '75%'
                        }
                    });

                    // Student Health Chart
                    const healthCtx = document.getElementById('studentHealthChart').getContext('2d');
                    new Chart(healthCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Normal', 'Kritis (Sem 13+)'],
                            datasets: [{
                                data: [{{ $studentHealthStats['Normal'] }}, {{ $studentHealthStats['Kritis'] }}],
                                backgroundColor: ['#3b82f6', '#f43f5e'],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'bottom', 
                                    labels: { 
                                        usePointStyle: true, 
                                        padding: 20, 
                                        font: { weight: 'bold', size: 10 },
                                        color: darkMode ? '#94a3b8' : '#64748b'
                                    } 
                                } 
                            }
                        }
                    });

                    // Cohort Completion Chart
                    const cohortCtx = document.getElementById('cohortCompletionChart').getContext('2d');
                    new Chart(cohortCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_keys($cohortCompletionData)) !!},
                            datasets: [{
                                label: 'Rata-rata Masa Studi (Tahun)',
                                data: {!! json_encode(array_values($cohortCompletionData)) !!},
                                backgroundColor: '#3b82f6',
                                borderRadius: 8,
                                barThickness: 40
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { 
                                y: { 
                                    beginAtZero: true, 
                                    suggestedMax: 7,
                                    grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b' } 
                                }, 
                                x: { 
                                    grid: { display: false },
                                    ticks: { 
                                        color: darkMode ? '#f1f5f9' : '#1e293b',
                                        font: { weight: 'bold', size: 10 }
                                    }
                                } 
                            },
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: darkMode ? '#1e293b' : '#fff',
                                    titleColor: darkMode ? '#fff' : '#1e293b',
                                    bodyColor: darkMode ? '#cbd5e1' : '#64748b',
                                    borderColor: darkMode ? '#334155' : '#e2e8f0',
                                    borderWidth: 1,
                                    padding: 12,
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y + ' Tahun';
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Topic Trend Chart (Stacked Bar)
                    const topicTrendCtx = document.getElementById('topicTrendChart').getContext('2d');
                    const topicTrendData = {!! json_encode($topicTrends) !!};
                    const topicCohorts = Object.keys(topicTrendData);
                    
                    const allTopics = [...new Set(topicCohorts.flatMap(c => Object.keys(topicTrendData[c])))];
                    const topicColors = {
                        'Web Development': '#f59e0b', 'Mobile Development': '#10b981', 'Data Science': '#3b82f6',
                        'Security': '#f43f5e', 'Networking': '#8b5cf6', 'Lainnya': '#94a3b8'
                    };

                    new Chart(topicTrendCtx, {
                        type: 'bar',
                        data: {
                            labels: topicCohorts,
                            datasets: allTopics.map(topic => ({
                                label: topic,
                                data: topicCohorts.map(cohort => topicTrendData[cohort][topic] || 0),
                                backgroundColor: topicColors[topic] || '#cbd5e1',
                                borderRadius: 4
                            }))
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            scales: {
                                x: { stacked: true, grid: { display: false }, ticks: { color: darkMode ? '#94a3b8' : '#64748b', font: { weight: 'bold', size: 10 } } },
                                y: { stacked: true, beginAtZero: true, max: 100, grid: { color: darkMode ? '#334155' : '#f1f5f9' }, ticks: { color: darkMode ? '#94a3b8' : '#64748b', callback: v => v + '%' } }
                            },
                            plugins: {
                                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { weight: 'bold', size: 10 }, color: darkMode ? '#94a3b8' : '#64748b' } },
                                tooltip: {
                                    backgroundColor: darkMode ? '#1e293b' : '#fff', titleColor: darkMode ? '#fff' : '#1e293b', bodyColor: darkMode ? '#cbd5e1' : '#64748b',
                                    borderColor: darkMode ? '#334155' : '#e2e8f0', borderWidth: 1, padding: 12,
                                    callbacks: { label: c => c.dataset.label + ': ' + c.parsed.y + '%' }
                                }
                            }
                        }
                    });
                @endif
            @endif

            // Live Online Users Polling for Dashboard Widget
            @if(Auth::user()->role !== 'mahasiswa')
                const onlineEndpoint = "{{ route('dashboard.online-users') }}";
                function updateOnlineUsersWidget() {
                    fetch(onlineEndpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(data => {
                        const badge = document.getElementById('online-users-count-badge');
                        const mhsCount = document.getElementById('online-count-mhs');
                        const dosenCount = document.getElementById('online-count-dosen');
                        const adminCount = document.getElementById('online-count-admin');
                        const listContainer = document.getElementById('online-users-list');

                        if (badge) badge.innerText = `${data.count} Aktif`;
                        if (mhsCount) mhsCount.innerText = data.by_role.mahasiswa || 0;
                        if (dosenCount) dosenCount.innerText = data.by_role.dosen || 0;
                        if (adminCount) adminCount.innerText = (data.by_role.admin || 0) + (data.by_role.kaprodi || 0);

                        if (listContainer && data.users) {
                            if (data.users.length === 0) {
                                listContainer.innerHTML = '<div class="text-center py-6"><p class="text-xs text-slate-400 font-medium italic">Tidak ada pengguna aktif lain saat ini.</p></div>';
                            } else {
                                let html = '';
                                const currentUserId = {{ Auth::id() }};
                                data.users.forEach(u => {
                                    const isSelf = u.id == currentUserId;
                                    html += `
                                        <div class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors group">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="relative w-10 h-10 shrink-0">
                                                    <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-xs bg-slate-100 dark:bg-slate-800">
                                                        <img src="${u.avatar_url}" alt="" class="w-full h-full object-cover">
                                                    </div>
                                                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full shadow-xs z-10" title="Online"></span>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate group-hover:text-orange-600 transition-colors">${u.name}</p>
                                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">${u.role}</span>
                                                </div>
                                            </div>
                                            ${!isSelf ? `
                                            <a href="${u.chat_url}" class="p-1.5 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 hover:bg-orange-500 hover:text-white transition-all shrink-0" title="Kirim Pesan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                            </a>` : ''}
                                        </div>
                                    `;
                                });
                                listContainer.innerHTML = html;
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching online users:', err));
                }

                setInterval(updateOnlineUsersWidget, 10000);
            @endif
        });
    </script>

    @if(Auth::user()->role === 'mahasiswa' && $thesis && !$thesis->isAccSidangFinal())
    <div id="edit-thesis-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('edit-thesis-modal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-100 dark:border-slate-700">
                <form action="{{ route('theses.update', $thesis->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                        <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight" id="modal-title">Revisi Judul / Deskripsi Skripsi</h3>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Judul Skripsi <span class="text-orange-600">*</span></label>
                            <textarea name="title" id="title" rows="3" required class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-colors">{{ old('title', $thesis->title) }}</textarea>
                            @error('title')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="abstract" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Abstrak / Deskripsi</label>
                            <textarea name="abstract" id="abstract" rows="6" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-colors">{{ old('abstract', $thesis->abstract) }}</textarea>
                            @error('abstract')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-3">
                        <button type="button" onclick="document.getElementById('edit-thesis-modal').classList.add('hidden')" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-bold hover:bg-orange-700 transition-colors shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>

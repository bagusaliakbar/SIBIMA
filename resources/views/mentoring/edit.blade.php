<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('mentoring-sessions.index') }}" class="p-2 rounded-xl bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                    Edit / Reschedule Jadwal Bimbingan
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Sesuaikan tanggal, waktu, atau lokasi bimbingan mahasiswa.</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <div class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700 transition-colors space-y-6">
            
            <!-- Student & Thesis Info Card -->
            <div class="p-4 sm:p-5 bg-orange-50/60 dark:bg-orange-950/30 rounded-2xl border border-orange-200/60 dark:border-orange-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    @if($mentoringSession->thesis?->student?->avatar_url)
                        <img src="{{ $mentoringSession->thesis->student->avatar_url }}" class="w-12 h-12 rounded-2xl object-cover border border-orange-200 dark:border-orange-800 shrink-0">
                    @else
                        <div class="w-12 h-12 rounded-2xl bg-orange-600 text-white flex items-center justify-center font-black text-sm uppercase shadow-sm shrink-0">
                            {{ substr($mentoringSession->thesis?->student?->name ?? 'M', 0, 2) }}
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-orange-100 dark:bg-orange-900/60 text-orange-700 dark:text-orange-300">
                                {{ $mentoringSession->thesis?->student?->identifier ?? 'NPM -' }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Jadwal Sesi #{{ $mentoringSession->id }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                            {{ $mentoringSession->thesis?->student?->name ?? 'Mahasiswa' }}
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">
                            {{ $mentoringSession->thesis?->title ?? 'Judul Skripsi' }}
                        </p>
                    </div>
                </div>

                <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-orange-200/60 dark:border-orange-900/40">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 dark:text-orange-400">Jadwal Saat Ini</span>
                    <div class="text-xs font-bold text-slate-700 dark:text-slate-200">
                        {{ $mentoringSession->scheduled_at->locale('id')->translatedFormat('d M Y') }} • {{ $mentoringSession->scheduled_at->format('H:i') }} WIB
                    </div>
                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold rounded-full {{ $mentoringSession->type === 'online' ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' }}">
                        {{ ucfirst($mentoringSession->type) }}
                    </span>
                </div>
            </div>

            <!-- Group Mentoring Detection Box (Clean & Expandable) -->
            @if(isset($relatedSessions) && $relatedSessions->count() > 0)
                <div class="p-5 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/80 rounded-2xl space-y-4" x-data="{ expanded: false }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-bold text-indigo-950 dark:text-indigo-100">
                                        Sesi Bimbingan Bersama / Massal
                                    </h4>
                                    <span class="px-2 py-0.5 bg-indigo-200/80 dark:bg-indigo-900/80 text-indigo-800 dark:text-indigo-200 rounded-md text-[10px] font-bold">
                                        {{ $relatedSessions->count() + 1 }} Mahasiswa Terhubung
                                    </span>
                                </div>
                                <p class="text-xs text-indigo-700/90 dark:text-indigo-300/90 mt-0.5">Jadwal ini dibuat untuk beberapa mahasiswa sekaligus pada tanggal dan jam yang sama.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Clean Grid List of Students -->
                    <div class="pt-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
                            <!-- Current Selected Student -->
                            <div class="flex items-center gap-2.5 p-2.5 bg-white dark:bg-slate-900 border-2 border-orange-300 dark:border-orange-600/70 rounded-xl shadow-2xs">
                                <div class="w-7 h-7 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ substr($mentoringSession->thesis?->student?->name ?? 'M', 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ $mentoringSession->thesis?->student?->name }}</p>
                                    <p class="text-[10px] font-medium text-orange-600 dark:text-orange-400">{{ $mentoringSession->thesis?->student?->identifier ?? 'NPM -' }}</p>
                                </div>
                            </div>

                            <!-- Related Students (First batch or all based on expanded) -->
                            @foreach($relatedSessions as $idx => $rel)
                                <div x-show="expanded || {{ $idx }} < 7" class="flex items-center gap-2.5 p-2.5 bg-white dark:bg-slate-900 border border-indigo-100 dark:border-indigo-800/60 rounded-xl shadow-2xs">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-300 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ substr($rel->thesis?->student?->name ?? 'M', 0, 1) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $rel->thesis?->student?->name }}</p>
                                        <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500">{{ $rel->thesis?->student?->identifier ?? 'NPM -' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($relatedSessions->count() > 7)
                            <div class="mt-3 text-center">
                                <button type="button" 
                                        @click="expanded = !expanded" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                    <span x-text="expanded ? 'Tampilkan Lebih Sedikit' : 'Lihat Semua ({{ $relatedSessions->count() + 1 }} Mahasiswa)'"></span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-red-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-xs font-bold text-red-800 dark:text-red-400">Terdapat kendala saat menyimpan perubahan:</h3>
                            <ul class="mt-1 list-disc pl-4 text-xs text-red-700 dark:text-red-300 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('mentoring-sessions.update', $mentoringSession) }}" method="POST" class="space-y-6" x-data="{
                type: '{{ old('type', $mentoringSession->type) }}',
                location: '{{ old('location', $mentoringSession->location ?? '') }}',
                meetOpened: false,
                openGoogleMeetNew() {
                    window.open('https://meet.google.com/new', '_blank');
                    this.meetOpened = true;
                },
                async pasteClipboard() {
                    try {
                        const text = await navigator.clipboard.readText();
                        if (text) {
                            this.location = text.trim();
                        }
                    } catch (e) {
                        alert('Silakan tekan Ctrl + V untuk menempelkan tautan.');
                    }
                },
                isGoogleMeet() {
                    return this.location && this.location.includes('meet.google.com');
                },
                isZoom() {
                    return this.location && (this.location.includes('zoom.us') || this.location.includes('zoom.com'));
                }
            }">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tanggal & Jam Baru -->
                    <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="scheduled_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                Tanggal Bimbingan Baru <span class="text-orange-600">*</span>
                            </label>
                            <input type="date" 
                                   name="scheduled_date" 
                                   id="scheduled_date" 
                                   value="{{ old('scheduled_date', $mentoringSession->scheduled_at->format('Y-m-d')) }}"
                                   required 
                                   class="mt-2 block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-2xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-xs font-semibold transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                Waktu / Jam Bimbingan Baru <span class="text-orange-600">*</span>
                            </label>
                            <div class="mt-2 flex items-center space-x-2">
                                <div class="flex-1">
                                    <select name="scheduled_hour" id="scheduled_hour" required class="block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 py-2.5 px-3 text-slate-900 dark:text-slate-100 shadow-2xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-xs font-semibold transition-all">
                                        @for($h = 0; $h < 24; $h++)
                                            @php $hStr = sprintf('%02d', $h); @endphp
                                            <option value="{{ $hStr }}" {{ old('scheduled_hour', $mentoringSession->scheduled_at->format('H')) == $hStr ? 'selected' : '' }}>
                                                {{ $hStr }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <span class="text-slate-400 font-bold">:</span>
                                <div class="flex-1">
                                    <select name="scheduled_minute" id="scheduled_minute" required class="block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 py-2.5 px-3 text-slate-900 dark:text-slate-100 shadow-2xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-xs font-semibold transition-all">
                                        @for($m = 0; $m < 60; $m += 5)
                                            @php $mStr = sprintf('%02d', $m); @endphp
                                            <option value="{{ $mStr }}" {{ old('scheduled_minute', $mentoringSession->scheduled_at->format('i')) == $mStr ? 'selected' : '' }}>
                                                {{ $mStr }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-bold ml-1">WIB</span>
                            </div>
                        </div>
                    </div>

                    <!-- Topik Pembahasan -->
                    <div class="md:col-span-2">
                        <label for="topic" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                            Topik / Agenda Bimbingan <span class="text-orange-600">*</span>
                        </label>
                        <input type="text" 
                               name="topic" 
                               id="topic" 
                               value="{{ old('topic', $mentoringSession->topic) }}" 
                               required 
                               placeholder="Contoh: Revisi Bab 1 & Metodologi Penelitian" 
                               class="mt-2 block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-2xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-xs font-semibold transition-all">
                    </div>

                    <!-- Tipe Bimbingan -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                            Tipe Bimbingan <span class="text-orange-600">*</span>
                        </label>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer rounded-2xl border bg-white dark:bg-slate-900 p-3.5 shadow-2xs focus:outline-none transition-all" :class="type === 'offline' ? 'border-orange-500 ring-2 ring-orange-500/20 bg-orange-50/20 dark:bg-orange-950/20' : 'border-slate-200 dark:border-slate-700'">
                                <input type="radio" name="type" value="offline" x-model="type" class="sr-only">
                                <span class="flex flex-1 items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-900/50 text-orange-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="block text-xs font-bold text-slate-800 dark:text-slate-100">Tatap Muka (Offline)</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400">Ruangan dosen, laboratorium, atau kampus</span>
                                    </div>
                                </span>
                                <svg class="h-5 w-5 text-orange-600" :class="type === 'offline' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </label>

                            <label class="relative flex cursor-pointer rounded-2xl border bg-white dark:bg-slate-900 p-3.5 shadow-2xs focus:outline-none transition-all" :class="type === 'online' ? 'border-orange-500 ring-2 ring-orange-500/20 bg-orange-50/20 dark:bg-orange-950/20' : 'border-slate-200 dark:border-slate-700'">
                                <input type="radio" name="type" value="online" x-model="type" class="sr-only">
                                <span class="flex flex-1 items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="block text-xs font-bold text-slate-800 dark:text-slate-100">Daring (Online)</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400">Google Meet / Zoom Meeting</span>
                                    </div>
                                </span>
                                <svg class="h-5 w-5 text-orange-600" :class="type === 'online' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </label>
                        </div>
                    </div>

                    <!-- Lokasi / Link Google Meet -->
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between">
                            <label for="location" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300" x-text="type === 'online' ? 'Tautan Google Meet / Meeting Link' : 'Ruangan / Lokasi Bimbingan'"></label>
                            
                            <div x-show="type === 'online'" class="flex items-center gap-1.5">
                                <button type="button" 
                                        @click="pasteClipboard()" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold transition-all shadow-2xs active:scale-95 cursor-pointer">
                                    <span>📋 Tempel Link</span>
                                </button>
                                <button type="button" 
                                        @click="openGoogleMeetNew()" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-lg text-xs font-bold hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all shadow-2xs active:scale-95 cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>⚡ Buat Google Meet Instan</span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-2">
                            <input type="text" 
                                   name="location" 
                                   id="location" 
                                   x-model="location"
                                   :placeholder="type === 'online' ? 'https://meet.google.com/xxx-xxxx-xxx' : 'Contoh: Ruang Dosen T.I / Lab Komputer Lt. 2'" 
                                   class="block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-2xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-xs font-semibold transition-all">
                        </div>

                        <div x-show="type === 'online'" class="mt-2 space-y-2">
                            <div x-show="meetOpened && !location" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-xl flex items-center justify-between gap-3 text-xs text-emerald-800 dark:text-emerald-300">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Ruang Google Meet baru telah dibuka di tab sebelah. Salin link ruangannya dan tempelkan di sini.</span>
                                </div>
                                <button type="button" @click="pasteClipboard()" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs shrink-0 transition-all shadow-2xs cursor-pointer">
                                    📋 Tempel Sekarang
                                </button>
                            </div>

                            <div x-show="location" x-cloak class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs">
                                <div class="flex items-center gap-1.5">
                                    <template x-if="isGoogleMeet()">
                                        <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            Tautan Google Meet Terverifikasi
                                        </span>
                                    </template>
                                    <template x-if="isZoom()">
                                        <span class="flex items-center gap-1 text-blue-600 dark:text-blue-400 font-bold">
                                            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            Tautan Zoom Meeting Terverifikasi
                                        </span>
                                    </template>
                                    <template x-if="!isGoogleMeet() && !isZoom()">
                                        <span class="flex items-center gap-1 text-slate-600 dark:text-slate-300 font-medium">
                                            🔗 Tautan Meeting Online
                                        </span>
                                    </template>
                                </div>

                                <a :href="location.startsWith('http') ? location : 'https://' + location" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 rounded-lg text-xs font-bold transition-all shadow-2xs">
                                    <span>↗ Uji Buka Link</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan / Instruksi Dosen -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                            Catatan / Instruksi Persiapan untuk Mahasiswa (Opsional)
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3" 
                                  class="mt-2 block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-2xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-xs font-semibold transition-all" 
                                  placeholder="Sampaikan dokumen atau materi yang perlu disiapkan mahasiswa sebelum sesi bimbingan ini...">{{ old('notes', $mentoringSession->notes) }}</textarea>
                    </div>
                </div>

                <!-- Reschedule Bersama Checkbox Card -->
                @if(isset($relatedSessions) && $relatedSessions->count() > 0)
                    <div class="p-4.5 bg-indigo-50/80 dark:bg-indigo-950/40 border-2 border-indigo-200 dark:border-indigo-800/80 rounded-2xl">
                        <label class="flex items-start gap-3.5 cursor-pointer group">
                            <input type="checkbox" 
                                   name="apply_to_group" 
                                   value="1" 
                                   checked 
                                   class="mt-1 w-4.5 h-4.5 text-indigo-600 rounded-md border-slate-300 dark:border-slate-700 focus:ring-indigo-500 cursor-pointer">
                            <div>
                                <span class="text-xs font-black text-indigo-950 dark:text-indigo-100 group-hover:text-indigo-600 transition-colors flex items-center gap-2">
                                    <span>Terapkan Perubahan Jadwal ke Seluruh ({{ $relatedSessions->count() + 1 }}) Mahasiswa Terkait</span>
                                    <span class="px-2 py-0.5 bg-indigo-200 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded text-[10px] font-black uppercase">Reschedule Massal</span>
                                </span>
                                <p class="text-[11px] text-indigo-700/90 dark:text-indigo-300/90 mt-1 leading-relaxed">
                                    Tanggal/jam baru, metode, lokasi, dan instruksi akan otomatis disinkronkan ke seluruh {{ $relatedSessions->count() + 1 }} mahasiswa di atas dan masing-masing menerima notifikasi WhatsApp/Web baru.
                                </p>
                            </div>
                        </label>
                    </div>
                @endif

                <!-- Reschedule Info Notice -->
                <div class="p-3.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-2xl flex items-start gap-2.5 text-xs text-amber-800 dark:text-amber-300">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <span class="font-bold">Informasi Otomatis Reschedule:</span>
                        <p class="mt-0.5 text-amber-700 dark:text-amber-300/90 text-[11px] leading-relaxed">
                            Jika tanggal atau jam diubah, sistem akan otomatis mereset status konfirmasi kehadiran mahasiswa menjadi <em>Menunggu Konfirmasi</em> dan mengirimkan pesan notifikasi WhatsApp/Web ke mahasiswa.
                        </p>
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('mentoring-sessions.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-2xs">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 rounded-xl text-xs font-bold text-white shadow-sm hover:shadow-orange-500/25 transition-all border border-transparent cursor-pointer">
                        Simpan Perubahan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
            {{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Buat Jadwal Bimbingan' : 'Pengajuan Jadwal Bimbingan' }}
        </h2>
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 transition-colors">
            <div class="mb-6 border-b border-slate-200 dark:border-slate-700 pb-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Form {{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Penjadwalan' : 'Pengajuan' }}</h3>
                <p class="text-slate-500 dark:text-slate-400 mt-1 text-xs">{{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Pilih mahasiswa dan tentukan waktu bimbingan.' : 'Pilih jadwal dan topik untuk sesi bimbingan Anda berikutnya.' }}</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-md bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400 dark:text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-400">Terdapat kesalahan pada penginputan:</h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('mentoring-sessions.store') }}" method="POST" class="space-y-6" x-data="{
                type: '{{ old('type', 'offline') }}',
                location: '{{ old('location', '') }}',
                selectedThesisId: '',
                meetOpened: false,
                customTimeMode: false,
                thesesMap: {{ json_encode(($theses ?? collect())->keyBy('id')->map(fn($t) => [
                    'p1_id' => $t->pembimbing1_id,
                    'p1_name' => $t->pembimbing1?->name ?? 'Pembimbing 1',
                    'p2_id' => $t->pembimbing2_id,
                    'p2_name' => $t->pembimbing2?->name,
                ])) }},
                today: '{{ date('Y-m-d') }}',
                scheduledDate: '{{ old('scheduled_date', date('Y-m-d')) }}',
                selectedTime: '{{ old('scheduled_hour', '09') }}:{{ old('scheduled_minute', '00') }}',
                
                timeSlots: {
                    'Pagi': ['07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30'],
                    'Siang & Sore': ['13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'],
                    'Malam': ['18:30', '19:00', '19:30', '20:00']
                },

                get allSlots() {
                    return [...this.timeSlots['Pagi'], ...this.timeSlots['Siang & Sore'], ...this.timeSlots['Malam']];
                },

                get selectedHour() {
                    return this.selectedTime.split(':')[0] || '09';
                },

                get selectedMinute() {
                    return this.selectedTime.split(':')[1] || '00';
                },

                init() {
                    this.validateAndAdjustTime();
                },

                get selectedThesis() {
                    return this.thesesMap[this.selectedThesisId] || null;
                },

                isToday() {
                    return this.scheduledDate === this.today;
                },

                isSlotDisabled(slot) {
                    if (!this.isToday()) return false;
                    const [h, m] = slot.split(':').map(Number);
                    const now = new Date();
                    const currentH = now.getHours();
                    const currentM = now.getMinutes();
                    if (h < currentH) return true;
                    if (h === currentH) return m <= currentM;
                    return false;
                },

                selectTimeSlot(slot) {
                    if (this.isSlotDisabled(slot)) return;
                    this.selectedTime = slot;
                },

                onDateChange() {
                    if (this.scheduledDate < this.today) {
                        this.scheduledDate = this.today;
                    }
                    this.validateAndAdjustTime();
                },

                validateAndAdjustTime() {
                    if (this.isSlotDisabled(this.selectedTime)) {
                        const firstAvailable = this.allSlots.find(s => !this.isSlotDisabled(s));
                        if (firstAvailable) {
                            this.selectedTime = firstAvailable;
                        } else {
                            // If today has no slots left, automatically increment to tomorrow
                            const d = new Date(this.scheduledDate);
                            d.setDate(d.getDate() + 1);
                            this.scheduledDate = d.toISOString().split('T')[0];
                            this.selectedTime = '08:00';
                        }
                    }
                },

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
                
                <!-- Hidden inputs for backend compatibility -->
                <input type="hidden" name="scheduled_hour" :value="selectedHour">
                <input type="hidden" name="scheduled_minute" :value="selectedMinute">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label for="thesis_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mahasiswa Bimbingan <span class="text-orange-600">*</span></label>
                            <select name="thesis_id" id="thesis_id" x-model="selectedThesisId" required class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                                <option value="">Pilih Mahasiswa...</option>
                                <option value="all" class="font-bold text-orange-600">-- Pilih Semua Mahasiswa Bimbingan --</option>
                                @foreach($theses as $t)
                                    <option value="{{ $t->id }}">{{ $t->student?->name ?? 'Mahasiswa' }} - {{ \Illuminate\Support\Str::limit($t->title, 50) }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if(in_array(Auth::user()->role, ['admin', 'kaprodi']))
                        <div>
                            <label for="dosen_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Pilih Dosen Pembimbing</label>
                            <select name="dosen_id" id="dosen_id" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                                <option value="" x-text="selectedThesis ? ('Pembimbing 1: ' + selectedThesis.p1_name) : 'Pembimbing 1 Mahasiswa (Otomatis)'"></option>
                                <option value="p2" x-show="!selectedThesis || selectedThesis.p2_id" x-text="selectedThesis && selectedThesis.p2_name ? ('Pembimbing 2: ' + selectedThesis.p2_name) : 'Pembimbing 2 Mahasiswa (Otomatis)'"></option>
                            </select>
                            <p class="mt-1 text-xs text-slate-400 dark:text-slate-500 italic">Pilihan dibatasi khusus dosen pembimbing (Pembimbing 1 / Pembimbing 2) dari mahasiswa yang dipilih.</p>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="md:col-span-2">
                        <label for="dosen_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Pilih Dosen Pembimbing <span class="text-orange-600">*</span></label>
                        <select name="dosen_id" id="dosen_id" required class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                            <option value="">Pilih Pembimbing...</option>
                            @if($thesis->pembimbing1)
                                <option value="{{ $thesis->pembimbing1->id }}">Pembimbing 1: {{ $thesis->pembimbing1->name }}</option>
                            @endif
                            @if($thesis->pembimbing2)
                                <option value="{{ $thesis->pembimbing2->id }}">Pembimbing 2: {{ $thesis->pembimbing2->name }}</option>
                            @endif
                        </select>
                        <p class="mt-1 text-xs text-slate-400 dark:text-slate-500 italic">Jadwal bimbingan akan dikirimkan secara spesifik ke dosen yang dipilih.</p>
                    </div>
                    @endif

                    <!-- Tanggal Bimbingan -->
                    <div class="md:col-span-2">
                        <label for="scheduled_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Bimbingan <span class="text-orange-600">*</span></label>
                        <input type="date" 
                               name="scheduled_date" 
                               id="scheduled_date" 
                               min="{{ date('Y-m-d') }}"
                               x-model="scheduledDate"
                               @change="onDateChange()"
                               required 
                               class="mt-2 block w-full md:w-1/2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                        <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500">Pilih tanggal bimbingan. Tanggal sebelum hari ini otomatis dinonaktifkan.</p>
                    </div>

                    <!-- Visual Time Slot Selector -->
                    <div class="md:col-span-2 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Pilih Waktu Bimbingan <span class="text-orange-600">*</span>
                                <span class="ml-2 text-xs font-black text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/60 px-3 py-1 rounded-full border border-orange-200/60 dark:border-orange-900/50" x-text="selectedTime + ' WIB'"></span>
                            </label>
                            <button type="button" @click="customTimeMode = !customTimeMode" class="text-xs font-bold text-slate-500 hover:text-orange-600 dark:hover:text-orange-400 underline transition-colors">
                                <span x-text="customTimeMode ? '← Pilih Slot Waktu' : '⚙️ Waktu Kustom / Spesifik'"></span>
                            </button>
                        </div>

                        <!-- Modern Well-Spaced Slot Container -->
                        <div x-show="!customTimeMode" class="bg-slate-50/70 dark:bg-slate-900/40 p-5 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-5">
                            @php
                                $slotGroups = [
                                    ['label' => 'Pagi', 'icon' => '🌅', 'slots' => ['07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30']],
                                    ['label' => 'Siang & Sore', 'icon' => '☀️', 'slots' => ['13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00']],
                                    ['label' => 'Malam', 'icon' => '🌙', 'slots' => ['18:30', '19:00', '19:30', '20:00']]
                                ];
                            @endphp

                            @foreach($slotGroups as $group)
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-sm">{{ $group['icon'] }}</span>
                                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400">{{ $group['label'] }}</span>
                                        <div class="flex-1 h-px bg-slate-200 dark:bg-slate-800 ml-2"></div>
                                    </div>
                                    <div class="flex flex-wrap gap-2.5 sm:gap-3">
                                        @foreach($group['slots'] as $slot)
                                            <button type="button" 
                                                    @click="selectTimeSlot('{{ $slot }}')" 
                                                    :disabled="isSlotDisabled('{{ $slot }}')"
                                                    :title="isSlotDisabled('{{ $slot }}') ? 'Waktu telah terlewat' : 'Pilih jam {{ $slot }} WIB'"
                                                    :class="selectedTime === '{{ $slot }}' 
                                                        ? 'bg-orange-600 text-white font-black border-orange-600 shadow-md shadow-orange-500/25 ring-2 ring-orange-500/30 scale-[1.03]' 
                                                        : (isSlotDisabled('{{ $slot }}') 
                                                            ? 'bg-slate-100 dark:bg-slate-900/60 text-slate-300 dark:text-slate-600 border-slate-200/40 dark:border-slate-800 cursor-not-allowed line-through opacity-40' 
                                                            : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-orange-500 hover:text-orange-600 dark:hover:border-orange-400 dark:hover:text-orange-400 hover:bg-orange-50/50 shadow-sm')"
                                                    class="flex-1 min-w-[72px] max-w-[105px] py-2.5 px-2 rounded-xl border text-xs font-bold transition-all text-center flex items-center justify-center">
                                                <span>{{ $slot }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            <div class="pt-3 border-t border-slate-200/70 dark:border-slate-800 flex flex-wrap items-center gap-5 text-[11px] text-slate-500 dark:text-slate-400">
                                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-orange-600 inline-block shadow-sm"></span> <span class="font-medium">Terpilih</span></span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 inline-block shadow-sm"></span> <span class="font-medium">Tersedia</span></span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 inline-block line-through opacity-60"></span> <span class="font-medium">Terlewat</span></span>
                            </div>
                        </div>

                        <!-- Fallback Custom Time Picker -->
                        <div x-show="customTimeMode" class="bg-slate-50/60 dark:bg-slate-900/40 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800">
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-2">Input Waktu Kustom (Jam & Menit Manual):</label>
                            <input type="time" 
                                   x-model="selectedTime"
                                   class="block w-full sm:w-48 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2 px-3 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-sm font-bold">
                        </div>
                    </div>
                    
                    <div>
                        <label for="topic" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Topik Pembahasan <span class="text-orange-600">*</span></label>
                        <input type="text" name="topic" id="topic" required placeholder="Contoh: Revisi Bab 1" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tipe Bimbingan <span class="text-orange-600">*</span></label>
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-slate-900 p-3 shadow-sm focus:outline-none transition-all" :class="type === 'offline' ? 'border-orange-500 ring-1 ring-orange-500' : 'border-slate-300 dark:border-slate-700'">
                                <input type="radio" name="type" value="offline" x-model="type" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-slate-100">Tatap Muka (Offline)</span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-orange-600" :class="type === 'offline' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </label>
                            <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-slate-900 p-3 shadow-sm focus:outline-none transition-all" :class="type === 'online' ? 'border-orange-500 ring-1 ring-orange-500' : 'border-slate-300 dark:border-slate-700'">
                                <input type="radio" name="type" value="online" x-model="type" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-slate-100">Daring (Online)</span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-orange-600" :class="type === 'online' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </label>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="location" class="block text-sm font-medium text-slate-700 dark:text-slate-300" x-text="type === 'online' ? 'Tautan Google Meet' : 'Ruangan / Tempat Bimbingan'"></label>
                            
                            <!-- Google Meet Quick Action Helper (Visible when Online) -->
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
                                   :placeholder="type === 'online' ? 'https://meet.google.com/xxx-xxxx-xxx' : 'Contoh: Ruang Dosen T.I / Lab Komputer'" 
                                   class="block w-full rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-sm transition-all">
                        </div>

                        <!-- Smart Validation Feedback & Step Guide -->
                        <div x-show="type === 'online'" class="mt-2 space-y-2">
                            <div x-show="meetOpened && !location" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-xl flex items-center justify-between gap-3 text-xs text-emerald-800 dark:text-emerald-300">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Ruang Google Meet baru telah dibuka di tab sebelah. Salin link ruangannya dan tempelkan di sini.</span>
                                </div>
                                <button type="button" @click="pasteClipboard()" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs shrink-0 transition-all shadow-xs cursor-pointer">
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
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="4" class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all" placeholder="Sampaikan apa yang ingin Anda diskusikan secara spesifik..."></textarea>
                </div>

                <div class="pt-5 flex items-center space-x-3">
                    <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 rounded text-sm font-medium text-white shadow-sm transition-colors border border-transparent">
                        {{ Auth::user()->role === 'dosen' ? 'Simpan Jadwal' : 'Kirim Permintaan' }}
                    </button>
                    <a href="{{ Auth::user()->role === 'dosen' ? route('mentoring-sessions.index') : route('dashboard') }}" class="px-5 py-2 rounded text-sm font-medium text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

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
                thesesMap: {{ json_encode(($theses ?? collect())->keyBy('id')->map(fn($t) => [
                    'p1_id' => $t->pembimbing1_id,
                    'p1_name' => $t->pembimbing1?->name ?? 'Pembimbing 1',
                    'p2_id' => $t->pembimbing2_id,
                    'p2_name' => $t->pembimbing2?->name,
                ])) }},
                today: '{{ date('Y-m-d') }}',
                scheduledDate: '{{ old('scheduled_date', date('Y-m-d')) }}',
                scheduledHour: '{{ old('scheduled_hour', '09') }}',
                scheduledMinute: '{{ old('scheduled_minute', '00') }}',
                availableHours: ['07','08','09','10','11','12','13','14','15','16','17','18','19','20','21'],
                availableMinutes: ['00','05','10','15','20','25','30','35','40','45','50','55'],

                init() {
                    this.validateAndAdjustTime();
                },

                get selectedThesis() {
                    return this.thesesMap[this.selectedThesisId] || null;
                },

                isToday() {
                    return this.scheduledDate === this.today;
                },

                isHourDisabled(h) {
                    if (!this.isToday()) return false;
                    const now = new Date();
                    return parseInt(h) < now.getHours();
                },

                isMinuteDisabled(m) {
                    if (!this.isToday()) return false;
                    const now = new Date();
                    const currentH = now.getHours();
                    const selectedH = parseInt(this.scheduledHour);
                    if (selectedH < currentH) return true;
                    if (selectedH === currentH) return parseInt(m) <= now.getMinutes();
                    return false;
                },

                onDateChange() {
                    if (this.scheduledDate < this.today) {
                        this.scheduledDate = this.today;
                    }
                    this.validateAndAdjustTime();
                },

                onHourChange() {
                    this.validateAndAdjustTime();
                },

                validateAndAdjustTime() {
                    if (this.isHourDisabled(this.scheduledHour) || !this.availableHours.includes(this.scheduledHour)) {
                        const firstValidHour = this.availableHours.find(h => !this.isHourDisabled(h));
                        if (firstValidHour) {
                            this.scheduledHour = firstValidHour;
                        } else {
                            const d = new Date(this.scheduledDate);
                            d.setDate(d.getDate() + 1);
                            this.scheduledDate = d.toISOString().split('T')[0];
                            this.scheduledHour = '08';
                            this.scheduledMinute = '00';
                            return;
                        }
                    }

                    if (this.isMinuteDisabled(this.scheduledMinute)) {
                        const firstValidMin = this.availableMinutes.find(m => !this.isMinuteDisabled(m));
                        if (firstValidMin) {
                            this.scheduledMinute = firstValidMin;
                        } else {
                            const nextHourIndex = this.availableHours.indexOf(this.scheduledHour) + 1;
                            if (nextHourIndex < this.availableHours.length) {
                                this.scheduledHour = this.availableHours[nextHourIndex];
                                this.scheduledMinute = '00';
                            }
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

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal <span class="text-orange-600">*</span></label>
                            <input type="date" 
                                   name="scheduled_date" 
                                   id="scheduled_date" 
                                   min="{{ date('Y-m-d') }}"
                                   x-model="scheduledDate"
                                   @change="onDateChange()"
                                   required 
                                   class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                            <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500">Tanggal yang telah terlewat tidak dapat dipilih.</p>
                        </div>
                        <div>
                            <label for="scheduled_hour" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Waktu / Jam Tersedia <span class="text-orange-600">*</span></label>
                            <div class="mt-2 flex items-center space-x-2">
                                <div class="flex-1">
                                    <select name="scheduled_hour" 
                                            id="scheduled_hour" 
                                            x-model="scheduledHour"
                                            @change="onHourChange()"
                                            required 
                                            class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                                        <template x-for="h in availableHours" :key="h">
                                            <option :value="h" :disabled="isHourDisabled(h)" x-text="isHourDisabled(h) ? h + ' (Terlewat)' : h"></option>
                                        </template>
                                    </select>
                                </div>
                                <span class="text-slate-500 dark:text-slate-400 font-bold">:</span>
                                <div class="flex-1">
                                    <select name="scheduled_minute" 
                                            id="scheduled_minute" 
                                            x-model="scheduledMinute"
                                            required 
                                            class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                                        <template x-for="m in availableMinutes" :key="m">
                                            <option :value="m" :disabled="isMinuteDisabled(m)" x-text="m"></option>
                                        </template>
                                    </select>
                                </div>
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium ml-2">WIB</span>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500">Jam & menit yang telah terlewat hari ini otomatis dinonaktifkan.</p>
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

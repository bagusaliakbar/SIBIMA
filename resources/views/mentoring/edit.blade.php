<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('mentoring-sessions.index') }}" class="p-2 rounded-lg bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-xs transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                    Edit / Reschedule Jadwal Bimbingan
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Ubah tanggal, waktu, metode, atau lokasi bimbingan mahasiswa.</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="{ cancelModalOpen: false, reason: '', apply_to_group: true }">
        <div class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 transition-colors space-y-6">
            
            <!-- Student Header Summary -->
            <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    @if($mentoringSession->thesis?->student?->avatar_url)
                        <img src="{{ $mentoringSession->thesis->student->avatar_url }}" class="w-11 h-11 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                    @else
                        <div class="w-11 h-11 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold text-sm uppercase shadow-xs shrink-0">
                            {{ substr($mentoringSession->thesis?->student?->name ?? 'M', 0, 2) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-orange-100 dark:bg-orange-900/60 text-orange-700 dark:text-orange-300">
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

                <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-200 dark:border-slate-700 shrink-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Jadwal Saat Ini</span>
                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                        {{ $mentoringSession->scheduled_at->locale('id')->translatedFormat('d M Y') }} • {{ $mentoringSession->scheduled_at->format('H:i') }} WIB
                    </div>
                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold rounded {{ $mentoringSession->type === 'online' ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' }}">
                        {{ ucfirst($mentoringSession->type) }}
                    </span>
                </div>
            </div>

            <!-- Group Mentoring Detection Box -->
            @if(isset($relatedSessions) && $relatedSessions->count() > 0)
                <div class="p-4 bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/60 rounded-lg space-y-3" x-data="{ expanded: false }">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-xs shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-indigo-950 dark:text-indigo-100">
                                    Sesi Bimbingan Bersama ({{ $relatedSessions->count() + 1 }} Mahasiswa Terdaftar)
                                </h4>
                                <p class="text-[11px] text-indigo-700 dark:text-indigo-300">Jadwal ini terhubung dengan {{ $relatedSessions->count() + 1 }} mahasiswa sekaligus pada tanggal dan jam yang sama.</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/80 text-indigo-800 dark:text-indigo-200 rounded text-[10px] font-bold shrink-0">
                            Bimbingan Bersama
                        </span>
                    </div>

                    <!-- Clean Responsive Grid of Students -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 pt-1">
                        <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-900 border-2 border-orange-400 dark:border-orange-500 rounded-lg shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ $mentoringSession->thesis?->student?->name }}</p>
                                <p class="text-[10px] font-semibold text-orange-600 dark:text-orange-400">{{ $mentoringSession->thesis?->student?->identifier ?? 'NPM -' }}</p>
                            </div>
                        </div>
                        @foreach($relatedSessions as $idx => $rel)
                            <div x-show="expanded || {{ $idx }} < 7" class="flex items-center gap-2 p-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-indigo-400 shrink-0"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $rel->thesis?->student?->name }}</p>
                                    <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500">{{ $rel->thesis?->student?->identifier ?? 'NPM -' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($relatedSessions->count() > 7)
                        <div class="text-center pt-1">
                            <button type="button" 
                                    @click="expanded = !expanded" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 rounded text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                <span x-text="expanded ? 'Tampilkan Lebih Sedikit' : 'Lihat Semua ({{ $relatedSessions->count() + 1 }} Mahasiswa)'"></span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-md bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20">
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
                customTimeMode: false,
                today: '{{ date('Y-m-d') }}',
                scheduledDate: '{{ old('scheduled_date', $mentoringSession->scheduled_at->isPast() ? date('Y-m-d') : $mentoringSession->scheduled_at->format('Y-m-d')) }}',
                selectedTime: '{{ old('scheduled_hour', $mentoringSession->scheduled_at->format('H')) }}:{{ old('scheduled_minute', sprintf('%02d', (int)round((int)$mentoringSession->scheduled_at->format('i') / 5) * 5 % 60)) }}',
                
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
                @method('PUT')

                <!-- Hidden inputs for backend compatibility -->
                <input type="hidden" name="scheduled_hour" :value="selectedHour">
                <input type="hidden" name="scheduled_minute" :value="selectedMinute">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Row 1: Tanggal & Waktu -->
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Bimbingan Baru <span class="text-orange-600">*</span></label>
                            <input type="date" 
                                   name="scheduled_date" 
                                   id="scheduled_date" 
                                   min="{{ date('Y-m-d') }}"
                                   x-model="scheduledDate"
                                   @change="onDateChange()"
                                   required 
                                   class="mt-2 block w-full md:w-1/2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                            <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500">Pilih tanggal baru. Tanggal sebelum hari ini otomatis dinonaktifkan.</p>
                        </div>

                        <!-- Visual Time Slot Selector -->
                        <div class="space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <label class="text-sm font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-1">
                                        <span>Pilih Waktu / Jam Baru</span>
                                        <span class="text-orange-600 font-bold">*</span>
                                    </label>
                                    <span class="inline-flex items-center gap-1 text-xs font-black text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/60 px-2.5 py-1 rounded-lg border border-orange-200/60 dark:border-orange-900/50 whitespace-nowrap shadow-xs">
                                        <span x-text="selectedTime"></span>
                                        <span class="text-[10px] text-orange-500/90 font-bold">WIB</span>
                                    </span>
                                </div>
                                <button type="button" @click="customTimeMode = !customTimeMode" class="text-xs font-bold text-slate-500 hover:text-orange-600 dark:text-slate-400 dark:hover:text-orange-400 inline-flex items-center gap-1 transition-colors self-start sm:self-auto">
                                    <span x-text="customTimeMode ? '← Kembali ke Slot Waktu' : '⚙️ Input Manual / Kustom'"></span>
                                </button>
                            </div>

                            <!-- Modern Well-Spaced Slot Container -->
                            <div x-show="!customTimeMode" class="bg-slate-50/70 dark:bg-slate-900/40 p-4 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-5">
                                @php
                                    $slotGroups = [
                                        ['label' => 'Pagi', 'slots' => ['07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30']],
                                        ['label' => 'Siang & Sore', 'slots' => ['13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00']],
                                        ['label' => 'Malam', 'slots' => ['18:30', '19:00', '19:30', '20:00']]
                                    ];
                                @endphp

                                @foreach($slotGroups as $group)
                                    <div>
                                        <div class="flex items-center gap-2 mb-2.5">
                                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $group['label'] }}</span>
                                            <div class="flex-1 h-px bg-slate-200/80 dark:bg-slate-800 ml-1"></div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 sm:gap-2.5">
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
                                                        class="flex-1 min-w-[68px] sm:min-w-[72px] max-w-[105px] py-2.5 px-2 rounded-xl border text-xs font-bold transition-all text-center flex items-center justify-center">
                                                    <span>{{ $slot }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                                <div class="pt-3 border-t border-slate-200/70 dark:border-slate-800 flex flex-wrap items-center gap-4 sm:gap-6 text-[11px] text-slate-600 dark:text-slate-300">
                                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-orange-600 inline-block shadow-sm"></span> <span class="font-medium">Terpilih</span></span>
                                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 inline-block shadow-sm"></span> <span class="font-medium text-slate-700 dark:text-slate-200">Tersedia</span></span>
                                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-slate-200 dark:bg-slate-700/80 border border-slate-300 dark:border-slate-600 inline-block"></span> <span class="font-medium text-slate-400 dark:text-slate-400 line-through">Terlewat</span></span>
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
                    </div>

                    <!-- Row 2 Col 1: Topik Pembahasan -->
                    <div>
                        <label for="topic" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Topik / Agenda Bimbingan <span class="text-orange-600">*</span></label>
                        <input type="text" 
                               name="topic" 
                               id="topic" 
                               value="{{ old('topic', $mentoringSession->topic) }}" 
                               required 
                               placeholder="Contoh: Revisi Bab 1 & Metodologi Penelitian" 
                               class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                    </div>

                    <!-- Row 2 Col 2: Tipe Bimbingan -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tipe Pertemuan <span class="text-orange-600">*</span></label>
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <label class="flex items-center justify-between cursor-pointer rounded-xl border py-2.5 px-4 text-sm font-medium transition-all select-none bg-white dark:bg-slate-900 shadow-2xs"
                                   :class="type === 'offline' 
                                       ? 'border-orange-500 ring-1 ring-orange-500 text-slate-900 dark:text-slate-100' 
                                       : 'border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-slate-400 dark:hover:border-slate-600'">
                                <input type="radio" name="type" value="offline" x-model="type" class="sr-only">
                                <span>Tatap Muka (Offline)</span>
                                <svg x-show="type === 'offline'" class="w-4 h-4 text-orange-600 shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </label>

                            <label class="flex items-center justify-between cursor-pointer rounded-xl border py-2.5 px-4 text-sm font-medium transition-all select-none bg-white dark:bg-slate-900 shadow-2xs"
                                   :class="type === 'online' 
                                       ? 'border-orange-500 ring-1 ring-orange-500 text-slate-900 dark:text-slate-100' 
                                       : 'border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-slate-400 dark:hover:border-slate-600'">
                                <input type="radio" name="type" value="online" x-model="type" class="sr-only">
                                <span>Daring (Online)</span>
                                <svg x-show="type === 'online'" class="w-4 h-4 text-orange-600 shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </label>
                        </div>
                    </div>

                    <!-- Row 3: Ruangan / Lokasi Bimbingan -->
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between">
                            <label for="location" class="block text-sm font-medium text-slate-700 dark:text-slate-300" x-text="type === 'online' ? 'Tautan Google Meet' : 'Ruangan / Tempat Bimbingan'"></label>
                            
                            <div x-show="type === 'online'" class="flex items-center gap-2">
                                <button type="button" 
                                        @click="pasteClipboard()" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded text-xs font-semibold transition-all cursor-pointer">
                                    <span>📋 Tempel Link</span>
                                </button>
                                <button type="button" 
                                        @click="openGoogleMeetNew()" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded text-xs font-semibold hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all cursor-pointer">
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
                                   class="block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                        </div>

                        <div x-show="type === 'online'" class="mt-2 space-y-2">
                            <div x-show="meetOpened && !location" x-cloak class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-md flex items-center justify-between gap-3 text-xs text-emerald-800 dark:text-emerald-300">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Ruang Google Meet baru dibuka di tab sebelah. Silakan salin link dan tempelkan.</span>
                                </div>
                                <button type="button" @click="pasteClipboard()" class="px-2 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold shrink-0 transition-all cursor-pointer">
                                    📋 Tempel
                                </button>
                            </div>

                            <div x-show="location" x-cloak class="flex items-center justify-between p-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-md text-xs">
                                <div class="flex items-center gap-1.5">
                                    <template x-if="isGoogleMeet()">
                                        <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-semibold">
                                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            Tautan Google Meet Terverifikasi
                                        </span>
                                    </template>
                                    <template x-if="isZoom()">
                                        <span class="flex items-center gap-1 text-blue-600 dark:text-blue-400 font-semibold">
                                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
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
                                   class="inline-flex items-center gap-1 px-2 py-0.5 bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 rounded text-xs font-semibold transition-all">
                                    <span>↗ Uji Tautan</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Reschedule Massal Toggle -->
                    @if(isset($relatedSessions) && $relatedSessions->count() > 0)
                        <div class="md:col-span-2 p-3.5 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-md">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" 
                                       name="apply_to_group" 
                                       value="1" 
                                       checked 
                                       class="mt-0.5 w-4 h-4 text-indigo-600 rounded border-slate-300 dark:border-slate-700 focus:ring-indigo-500 cursor-pointer">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-indigo-950 dark:text-indigo-100 group-hover:text-indigo-600 transition-colors">
                                            Terapkan Perubahan ke Seluruh ({{ $relatedSessions->count() + 1 }}) Mahasiswa Terkait
                                        </span>
                                        <span class="px-1.5 py-0.2 bg-indigo-200 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded text-[9px] font-bold uppercase">
                                            Reschedule Massal
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-indigo-700 dark:text-indigo-300 mt-0.5">
                                        Tanggal/jam baru, metode, lokasi, dan catatan akan otomatis disinkronkan ke seluruh {{ $relatedSessions->count() + 1 }} mahasiswa dan masing-masing menerima notifikasi WhatsApp/Web baru.
                                    </p>
                                </div>
                            </label>
                        </div>
                    @endif

                    <!-- Row 5: Catatan Tambahan -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan / Instruksi Persiapan untuk Mahasiswa (Opsional)</label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3" 
                                  class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all" 
                                  placeholder="Sampaikan dokumen atau materi yang perlu disiapkan mahasiswa sebelum sesi bimbingan ini...">{{ old('notes', $mentoringSession->notes) }}</textarea>
                    </div>
                </div>

                <!-- Info Notice -->
                <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-md flex items-start gap-2 text-xs text-amber-800 dark:text-amber-300">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <span class="font-bold">Informasi Otomatis Reschedule:</span>
                        <span class="text-amber-700 dark:text-amber-300/90 text-[11px] ml-1">
                            Jika tanggal atau jam diubah, status kehadiran mahasiswa akan otomatis di-reset ke <em>Menunggu Konfirmasi</em> dan mahasiswa menerima notifikasi WhatsApp/Web.
                        </span>
                    </div>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-t border-slate-200 dark:border-slate-700">
                    <div>
                        @can('delete', $mentoringSession)
                            <button type="button" 
                                    @click="cancelModalOpen = true" 
                                    class="w-full sm:w-auto px-4 py-2 rounded-md text-sm font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800/80 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Batalkan Sesi Bimbingan Ini</span>
                            </button>
                        @endcan
                    </div>
                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('mentoring-sessions.index') }}" class="px-4 py-2 rounded-md text-sm font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            Kembali
                        </a>
                        <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 rounded-md text-sm font-semibold text-white shadow-sm transition-colors border border-transparent cursor-pointer">
                            Simpan Perubahan Jadwal
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- MODAL KONFIRMASI PEMBATALAN JADWAL BIMBINGAN -->
        <template x-teleport="body">
            <div x-show="cancelModalOpen" 
                 x-cloak 
                 class="fixed inset-0 overflow-y-auto" 
                 style="z-index: 99999 !important;"
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Backdrop -->
                    <div x-show="cancelModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                         @click="cancelModalOpen = false" 
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!-- Modal Content -->
                    <div x-show="cancelModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700 relative"
                         style="z-index: 100000 !important;">
                        
                        <form action="{{ route('mentoring-sessions.destroy', $mentoringSession->id) }}" method="POST" class="p-6 sm:p-8 space-y-5">
                            @csrf
                            @method('DELETE')

                            <!-- Header -->
                            <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100 dark:border-slate-700/80">
                                <div class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-xl shrink-0">
                                    🚫
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">
                                        Batalkan Jadwal Bimbingan
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Jadwal ini akan dibatalkan dan mahasiswa terkait akan menerima notifikasi.
                                    </p>
                                </div>
                            </div>

                            <!-- Summary Card -->
                            <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2.5 text-xs">
                                <div class="flex justify-between items-start gap-3">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Mahasiswa:</span>
                                    <div class="text-right">
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $mentoringSession->thesis?->student?->name }}</span>
                                        <span class="text-slate-400 text-[10px] ml-1">({{ $mentoringSession->thesis?->student?->identifier ?? '-' }})</span>
                                    </div>
                                </div>
                                <div class="h-px bg-slate-200/70 dark:bg-slate-700/70"></div>
                                <div class="flex justify-between items-start gap-3">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Waktu Sesi:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $mentoringSession->scheduled_at->locale('id')->translatedFormat('l, d F Y') }} • {{ $mentoringSession->scheduled_at->format('H:i') }} WIB</span>
                                </div>
                                <div class="h-px bg-slate-200/70 dark:bg-slate-700/70"></div>
                                <div class="flex justify-between items-start gap-3">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Topik:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right line-clamp-2">{{ $mentoringSession->topic }}</span>
                                </div>
                            </div>

                            <!-- Group Option if available -->
                            @if(isset($relatedSessions) && $relatedSessions->count() > 0)
                                <div class="p-3 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/60 rounded-xl space-y-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5">
                                        👥 Sesi Bimbingan Bersama Terdeteksi
                                    </span>
                                    <label class="flex items-start gap-2.5 cursor-pointer text-xs">
                                        <input type="checkbox" 
                                               name="apply_to_group" 
                                               value="1" 
                                               x-model="apply_to_group"
                                               class="mt-0.5 w-4 h-4 text-indigo-600 rounded border-slate-300 dark:border-slate-700 focus:ring-indigo-500">
                                        <div>
                                            <span class="font-bold text-slate-800 dark:text-slate-200">
                                                Batalkan untuk seluruh ({{ $relatedSessions->count() + 1 }}) mahasiswa dalam kelompok jam ini
                                            </span>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                Jika tidak dicentang, hanya jadwal untuk <strong>{{ $mentoringSession->thesis?->student?->name }}</strong> yang dibatalkan.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            <!-- Input Alasan -->
                            <div>
                                <label for="cancel_reason_edit" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Alasan Pembatalan (Opsional / Disampaikan ke Mahasiswa)
                                </label>
                                <textarea name="reason" 
                                          id="cancel_reason_edit" 
                                          x-model="reason"
                                          rows="3" 
                                          class="w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 p-3 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-1 focus:ring-rose-500 focus:border-rose-500 shadow-2xs transition-all" 
                                          placeholder="Contoh: Ada agenda rapat mendadak, silakan buat jadwal bimbingan kembali..."></textarea>
                            </div>

                            <!-- Info Notice -->
                            <div class="p-3 bg-rose-50/70 dark:bg-rose-950/20 border border-rose-200/80 dark:border-rose-800/40 rounded-xl text-[11px] text-rose-800 dark:text-rose-300 flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span>Tindakan ini akan menghapus jadwal dari agenda dan mengirim pemberitahuan ke mahasiswa.</span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button" 
                                        @click="cancelModalOpen = false" 
                                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                    Tutup
                                </button>
                                <button type="submit" 
                                        class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md shadow-rose-500/20 hover:scale-[1.02] active:scale-95 flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Ya, Batalkan Jadwal</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>

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

    <div class="w-full space-y-6" x-data="{ 
        cancelModalOpen: false, 
        reason: '', 
        apply_to_group: true,
        groupExpanded: false,
        addStudentModalOpen: false,
        stagedThesesToAdd: {{ json_encode(array_values(array_map('intval', old('add_thesis_ids', [])))) }},
        modalSelection: [],
        addStudentSearch: '',
        type: {{ json_encode(old('type', $mentoringSession->type)) }},
        topic: {{ json_encode(old('topic', $mentoringSession->topic)) }},
        location: {{ json_encode(old('location', $mentoringSession->location ?? '')) }},
        meetOpened: false,
        customTimeMode: false,
        today: '{{ date('Y-m-d') }}',
        scheduledDate: '{{ old('scheduled_date', $mentoringSession->scheduled_at->isPast() ? date('Y-m-d') : $mentoringSession->scheduled_at->format('Y-m-d')) }}',
        selectedTime: '{{ old('scheduled_hour', $mentoringSession->scheduled_at->format('H')) }}:{{ old('scheduled_minute', sprintf('%02d', (int)round((int)$mentoringSession->scheduled_at->format('i') / 5) * 5 % 60)) }}',
        selectedEndTime: '{{ old('scheduled_end_time', $mentoringSession->scheduled_end_at ? $mentoringSession->scheduled_end_at->format('H:i') : $mentoringSession->scheduled_at->copy()->addHour()->format('H:i')) }}',
        durationMinutes: 60,
        durationPresets: [
            { label: '+30 mnt', minutes: 30 },
            { label: '+1 Jam', minutes: 60 },
            { label: '+1,5 Jam', minutes: 90 },
            { label: '+2 Jam', minutes: 120 },
            { label: '+3 Jam', minutes: 180 }
        ],
        timeSlots: {
            'Pagi': ['07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30'],
            'Siang & Sore': ['13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'],
            'Malam': ['18:30', '19:00', '19:30', '20:00']
        },
        availableTheses: {{ json_encode(($availableTheses ?? collect())->map(fn($t) => [
            'id' => (int)$t->id,
            'name' => $t->student?->name ?? 'Mahasiswa',
            'npm' => $t->student?->identifier ?? 'NPM -',
            'avatar' => $t->student?->avatar_url,
            'title' => $t->final_title ?? $t->title ?? 'Judul Skripsi',
            'role_label' => (Auth::user()->role === 'dosen')
                ? (($t->pembimbing1_id === Auth::id()) ? 'Pembimbing 1' : (($t->pembimbing2_id === Auth::id()) ? 'Pembimbing 2' : 'Dosen'))
                : ($t->pembimbing1?->name ? 'P1: ' . $t->pembimbing1->name : 'Bimbingan'),
        ])->values()) }},
        get allSlots() {
            return [...this.timeSlots['Pagi'], ...this.timeSlots['Siang & Sore'], ...this.timeSlots['Malam']];
        },
        get selectedHour() {
            return this.selectedTime.split(':')[0] || '09';
        },
        get selectedMinute() {
            return this.selectedTime.split(':')[1] || '00';
        },
        get filteredAvailableTheses() {
            if (!this.addStudentSearch.trim()) return this.availableTheses;
            const q = this.addStudentSearch.toLowerCase();
            return this.availableTheses.filter(t => 
                t.name.toLowerCase().includes(q) || 
                t.npm.toLowerCase().includes(q) || 
                t.title.toLowerCase().includes(q)
            );
        },
        get stagedThesesObjects() {
            return this.availableTheses.filter(t => this.stagedThesesToAdd.includes(t.id));
        },
        init() {
            this.detectPresetDuration();
            this.validateAndAdjustTime();
        },
        timeToMinutes(t) {
            if (!t || !t.includes(':')) return 0;
            const [h, m] = t.split(':').map(Number);
            return (h || 0) * 60 + (m || 0);
        },
        minutesToTime(total) {
            const clamped = Math.min(Math.max(total, 0), 23 * 60 + 55);
            const h = String(Math.floor(clamped / 60)).padStart(2, '0');
            const m = String(clamped % 60).padStart(2, '0');
            return `${h}:${m}`;
        },
        detectPresetDuration() {
            const diff = this.timeToMinutes(this.selectedEndTime) - this.timeToMinutes(this.selectedTime);
            if (diff <= 0) {
                this.durationMinutes = 60;
                this.selectedEndTime = this.minutesToTime(this.timeToMinutes(this.selectedTime) + 60);
                return;
            }
            const preset = this.durationPresets.find(p => p.minutes === diff);
            this.durationMinutes = preset ? preset.minutes : null;
        },
        applyDuration(minutes) {
            this.durationMinutes = minutes;
            this.selectedEndTime = this.minutesToTime(this.timeToMinutes(this.selectedTime) + minutes);
        },
        syncEndTimeFromStart() {
            const startMin = this.timeToMinutes(this.selectedTime);
            if (this.durationMinutes) {
                this.selectedEndTime = this.minutesToTime(startMin + this.durationMinutes);
            } else if (this.timeToMinutes(this.selectedEndTime) <= startMin) {
                this.durationMinutes = 60;
                this.selectedEndTime = this.minutesToTime(startMin + 60);
            }
        },
        onManualEndTimeChange() {
            const startMin = this.timeToMinutes(this.selectedTime);
            const endMin = this.timeToMinutes(this.selectedEndTime);
            if (!this.selectedEndTime || endMin <= startMin) {
                if (typeof window.sibimaToast === 'function') {
                    window.sibimaToast('Jam selesai harus lebih akhir dari jam mulai.', 'warning', 'Waktu Tidak Valid');
                }
                this.applyDuration(30);
                return;
            }
            const diff = endMin - startMin;
            const preset = this.durationPresets.find(p => p.minutes === diff);
            this.durationMinutes = preset ? preset.minutes : null;
        },
        get formattedDuration() {
            const diff = this.timeToMinutes(this.selectedEndTime) - this.timeToMinutes(this.selectedTime);
            if (diff <= 0) return '';
            const h = Math.floor(diff / 60);
            const m = diff % 60;
            if (h > 0 && m > 0) return `${h} Jam ${m} Mnt`;
            if (h > 0) return `${h} Jam`;
            return `${m} Mnt`;
        },
        isSlotInRange(slot) {
            const s = this.timeToMinutes(slot);
            return s > this.timeToMinutes(this.selectedTime) && s < this.timeToMinutes(this.selectedEndTime);
        },
        isSlotEnd(slot) {
            return slot === this.selectedEndTime;
        },
        formatPreviewDate() {
            if (!this.scheduledDate) return '-';
            try {
                const d = new Date(this.scheduledDate + 'T00:00:00');
                return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            } catch (e) {
                return this.scheduledDate;
            }
        },
        openAddStudentModal() {
            this.modalSelection = [...this.stagedThesesToAdd];
            this.addStudentSearch = '';
            this.addStudentModalOpen = true;
        },
        toggleModalThesis(id) {
            if (this.modalSelection.includes(id)) {
                this.modalSelection = this.modalSelection.filter(x => x !== id);
            } else {
                this.modalSelection.push(id);
            }
        },
        selectAllAvailable() {
            const ids = this.filteredAvailableTheses.map(t => t.id);
            this.modalSelection = [...new Set([...this.modalSelection, ...ids])];
        },
        clearAllAvailable() {
            this.modalSelection = [];
        },
        confirmStagedStudents() {
            this.stagedThesesToAdd = [...this.modalSelection];
            this.addStudentModalOpen = false;
            if (this.stagedThesesToAdd.length > 0 && typeof window.sibimaToast === 'function') {
                window.sibimaToast(
                    this.stagedThesesToAdd.length + ' mahasiswa masuk daftar tunggu. Klik Simpan Perubahan Jadwal untuk menyimpan dan mengirim notifikasi.',
                    'info',
                    'Belum Disimpan'
                );
            }
        },
        removeStagedThesis(id) {
            this.stagedThesesToAdd = this.stagedThesesToAdd.filter(x => x !== id);
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
            this.syncEndTimeFromStart();
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
            this.syncEndTimeFromStart();
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
                window.sibimaToast('Silakan tekan Ctrl + V untuk menempelkan tautan.', 'info', 'Pintasan Tempel');
            }
        },
        isGoogleMeet() {
            return this.location && this.location.includes('meet.google.com');
        },
        isZoom() {
            return this.location && (this.location.includes('zoom.us') || this.location.includes('zoom.com'));
        }
    }">
        <div class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 transition-colors space-y-6">
            
            <!-- Student Header Summary -->
            <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        @if($mentoringSession->thesis?->student?->avatar_url)
                            <img src="{{ $mentoringSession->thesis->student->avatar_url }}" alt="{{ $mentoringSession->thesis->student->name ?? 'Mahasiswa' }}" class="w-11 h-11 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0">
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
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Jadwal Saat Ini</span>
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            {{ $mentoringSession->scheduled_at->locale('id')->translatedFormat('d M Y') }} • {{ $mentoringSession->time_range }}
                        </div>
                        <span class="inline-block mt-1 px-2 py-0.5 text-[11px] font-bold rounded {{ $mentoringSession->type === 'online' ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' }}">
                            {{ ucfirst($mentoringSession->type) }}
                        </span>
                    </div>
                </div>

                <!-- Quick Add Student Trigger for Single Session -->
                @if(!isset($relatedSessions) || $relatedSessions->count() === 0)
                    <div class="pt-3 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span>Ada mahasiswa bimbingan yang terlewat dipilih?</span>
                        </div>
                        <button type="button" 
                                @click="openAddStudentModal()" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/40 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60 rounded-lg text-xs font-bold transition-all shadow-2xs cursor-pointer self-start sm:self-auto">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            <span>Tambah Mahasiswa Terlewat</span>
                            <span x-show="stagedThesesToAdd.length > 0" x-cloak class="px-1.5 py-0.2 rounded bg-orange-600 text-white text-[11px] font-bold" x-text="'+' + stagedThesesToAdd.length"></span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Group Mentoring Detection Box -->
            @if(isset($relatedSessions) && $relatedSessions->count() > 0)
                <div class="p-4 bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/60 rounded-lg space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
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
                        <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto">
                            <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/80 text-indigo-800 dark:text-indigo-200 rounded text-[11px] font-bold">
                                Bimbingan Bersama
                            </span>
                            <button type="button" 
                                    @click="openAddStudentModal()" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white rounded-md text-xs font-bold transition-all shadow-xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tambah Mahasiswa</span>
                                <span x-show="stagedThesesToAdd.length > 0" x-cloak class="px-1.5 py-0.2 rounded bg-white/20 text-white text-[11px] font-bold" x-text="'+' + stagedThesesToAdd.length"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Clean Responsive Grid of Students -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 pt-1">
                        <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-900 border-2 border-orange-400 dark:border-orange-500 rounded-lg shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ $mentoringSession->thesis?->student?->name }}</p>
                                <p class="text-[11px] font-semibold text-orange-600 dark:text-orange-400">{{ $mentoringSession->thesis?->student?->identifier ?? 'NPM -' }}</p>
                            </div>
                        </div>
                        @foreach($relatedSessions as $idx => $rel)
                            <div x-show="groupExpanded || {{ $idx }} < 7" class="flex items-center gap-2 p-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-indigo-400 shrink-0"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $rel->thesis?->student?->name }}</p>
                                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400">{{ $rel->thesis?->student?->identifier ?? 'NPM -' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($relatedSessions->count() > 7)
                        <div class="text-center pt-1">
                            <button type="button" 
                                    @click="groupExpanded = !groupExpanded" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 rounded text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                <span x-text="groupExpanded ? 'Tampilkan Lebih Sedikit' : 'Lihat Semua ({{ $relatedSessions->count() + 1 }} Mahasiswa)'"></span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="groupExpanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Staged Missed Students Preview (Not Yet Saved / Not Yet Notified) -->
            <div x-show="stagedThesesObjects.length > 0" x-cloak class="p-4 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-lg space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-xs font-bold text-amber-950 dark:text-amber-100">
                                    Mahasiswa Baru Akan Ditambahkan (<span x-text="stagedThesesObjects.length"></span>)
                                </h4>
                                <span class="px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-900/70 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700/60 text-[11px] font-bold">
                                    Menunggu Disimpan
                                </span>
                            </div>
                            <p class="text-[11px] text-amber-800 dark:text-amber-300 mt-0.5">
                                Belum tersimpan dan belum mengirim notifikasi. Notifikasi WhatsApp &amp; sistem akan dikirim otomatis setelah Anda klik <strong>Simpan Perubahan Jadwal</strong>.
                            </p>
                        </div>
                    </div>
                    <button type="button"
                            @click="openAddStudentModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-amber-300 dark:border-amber-700 rounded-lg text-xs font-bold transition-all shadow-2xs cursor-pointer self-start sm:self-auto shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        <span>Ubah Pilihan</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 pt-1">
                    <template x-for="st in stagedThesesObjects" :key="st.id">
                        <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700/70 rounded-lg shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" x-text="st.name"></p>
                                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400" x-text="st.npm"></p>
                            </div>
                            <button type="button"
                                    @click="removeStagedThesis(st.id)"
                                    aria-label="Hapus mahasiswa dari daftar tambahan"
                                    title="Batalkan penambahan mahasiswa ini"
                                    class="p-1 rounded-md text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            @if (session('success'))
                <div class="p-4 rounded-md bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-emerald-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
                    </div>
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

            <form action="{{ route('mentoring-sessions.update', $mentoringSession) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Hidden inputs for backend compatibility -->
                <input type="hidden" name="scheduled_hour" :value="selectedHour">
                <input type="hidden" name="scheduled_minute" :value="selectedMinute">
                <input type="hidden" name="scheduled_end_time" :value="selectedEndTime">

                <!-- Hidden inputs for staged missed students -->
                <template x-for="id in stagedThesesToAdd" :key="id">
                    <input type="hidden" name="add_thesis_ids[]" :value="id">
                </template>

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
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Pilih tanggal baru. Tanggal sebelum hari ini otomatis dinonaktifkan.</p>
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
                                        <span class="text-orange-400">–</span>
                                        <span x-text="selectedEndTime"></span>
                                        <span class="text-[11px] text-orange-500/90 font-bold">WIB</span>
                                    </span>
                                    <span x-show="formattedDuration" class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700" x-text="formattedDuration"></span>
                                </div>
                                <button type="button" @click="customTimeMode = !customTimeMode" class="text-xs font-bold text-slate-500 hover:text-orange-600 dark:text-slate-400 dark:hover:text-orange-400 inline-flex items-center gap-1 transition-colors self-start sm:self-auto cursor-pointer">
                                    <span x-text="customTimeMode ? '← Kembali ke Slot Waktu' : 'Input Manual / Kustom'"></span>
                                </button>
                            </div>

                            <!-- Modern Well-Spaced Slot Container -->
                            <div x-show="!customTimeMode" class="bg-slate-50/70 dark:bg-slate-900/40 p-4 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-5">
                                <!-- Duration & End Time Bar (Required) -->
                                <div class="p-3.5 bg-white dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700/80 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Durasi / Batas Selesai <span class="text-orange-600">*</span></span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <template x-for="preset in durationPresets" :key="preset.minutes">
                                                <button type="button"
                                                        @click="applyDuration(preset.minutes)"
                                                        :class="durationMinutes === preset.minutes
                                                            ? 'bg-orange-600 text-white border-orange-600 shadow-xs'
                                                            : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-orange-500 hover:text-orange-600 dark:hover:text-orange-400'"
                                                        class="px-2.5 py-1.5 rounded-lg border text-xs font-bold transition-all cursor-pointer"
                                                        x-text="preset.label">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2.5 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100 dark:border-slate-700/80 shrink-0">
                                        <label for="edit_end_time_slot_input" class="text-xs font-semibold text-slate-600 dark:text-slate-300 whitespace-nowrap">Sampai Jam:</label>
                                        <input type="time"
                                               id="edit_end_time_slot_input"
                                               x-model="selectedEndTime"
                                               @change="onManualEndTimeChange()"
                                               required
                                               class="w-32 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-1.5 px-2.5 text-slate-900 dark:text-slate-100 shadow-2xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-xs font-bold">
                                    </div>
                                </div>

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
                                        <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-5 lg:grid-cols-9 gap-2 sm:gap-2.5 w-full">
                                            @foreach($group['slots'] as $slot)
                                                <button type="button" 
                                                        @click="selectTimeSlot('{{ $slot }}')" 
                                                        :disabled="isSlotDisabled('{{ $slot }}')"
                                                        :title="isSlotDisabled('{{ $slot }}') ? 'Waktu telah terlewat' : 'Pilih jam mulai {{ $slot }} WIB'"
                                                        :class="selectedTime === '{{ $slot }}' 
                                                            ? 'bg-orange-600 text-white font-black border-orange-600 shadow-md shadow-orange-500/25 ring-2 ring-orange-500/30 scale-[1.03]' 
                                                            : (isSlotDisabled('{{ $slot }}') 
                                                                ? 'bg-slate-100 dark:bg-slate-900/60 text-slate-300 dark:text-slate-600 border-slate-200/40 dark:border-slate-800 cursor-not-allowed line-through opacity-40' 
                                                                : (isSlotEnd('{{ $slot }}')
                                                                    ? 'bg-orange-100 dark:bg-orange-950/70 text-orange-800 dark:text-orange-200 border-orange-400 dark:border-orange-500 ring-1 ring-orange-400/40 shadow-sm'
                                                                    : (isSlotInRange('{{ $slot }}')
                                                                        ? 'bg-orange-50/90 dark:bg-orange-950/35 text-orange-700 dark:text-orange-300 border-orange-200 dark:border-orange-800/70 shadow-2xs'
                                                                        : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-orange-500 hover:text-orange-600 dark:hover:border-orange-400 dark:hover:text-orange-400 hover:bg-orange-50/50 shadow-sm')))"
                                                        class="w-full py-2.5 px-2 rounded-xl border text-xs font-bold transition-all text-center flex items-center justify-center cursor-pointer">
                                                    <span>{{ $slot }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                                <div class="pt-3 border-t border-slate-200/70 dark:border-slate-800 flex flex-wrap items-center gap-4 sm:gap-6 text-[11px] text-slate-600 dark:text-slate-300">
                                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-orange-600 inline-block shadow-sm"></span> <span class="font-medium">Jam Mulai</span></span>
                                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-orange-100 dark:bg-orange-950 border border-orange-400 inline-block shadow-sm"></span> <span class="font-medium">Rentang Selesai</span></span>
                                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 inline-block shadow-sm"></span> <span class="font-medium text-slate-700 dark:text-slate-200">Tersedia</span></span>
                                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-slate-200 dark:bg-slate-700/80 border border-slate-300 dark:border-slate-600 inline-block"></span> <span class="font-medium text-slate-500 dark:text-slate-400 line-through">Terlewat</span></span>
                                </div>
                            </div>

                            <!-- Fallback Custom Time Picker -->
                            <div x-show="customTimeMode" class="bg-slate-50/60 dark:bg-slate-900/40 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800">
                                <p class="text-xs font-bold text-slate-600 dark:text-slate-400 mb-3">Input Rentang Waktu Kustom (Jam Mulai &amp; Jam Selesai):</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-md">
                                    <div>
                                        <label for="edit_custom_start_time" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Jam Mulai <span class="text-orange-600">*</span></label>
                                        <input type="time" 
                                               id="edit_custom_start_time"
                                               x-model="selectedTime"
                                               @change="syncEndTimeFromStart()"
                                               required
                                               class="block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2 px-3 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label for="edit_custom_end_time" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Jam Selesai <span class="text-orange-600">*</span></label>
                                        <input type="time" 
                                               id="edit_custom_end_time"
                                               x-model="selectedEndTime"
                                               @change="onManualEndTimeChange()"
                                               required
                                               class="block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2 px-3 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-sm font-bold">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2 Col 1: Topik Pembahasan -->
                    <div>
                        <label for="topic" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Topik / Agenda Bimbingan <span class="text-orange-600">*</span></label>
                        <input type="text" 
                               name="topic" 
                               id="topic" 
                               x-model="topic"
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
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                    <span>Tempel Link</span>
                                </button>
                                <button type="button" 
                                        @click="openGoogleMeetNew()" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded text-xs font-semibold hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>Buat Google Meet Instan</span>
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
                                <button type="button" @click="pasteClipboard()" class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold shrink-0 transition-all cursor-pointer">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                    <span>Tempel</span>
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
                                            Tautan Meeting Online
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
                                        <span class="px-1.5 py-0.2 bg-indigo-200 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded text-[11px] font-bold uppercase">
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
                            <span x-text="stagedThesesToAdd.length > 0 ? 'Simpan Perubahan & Tambah (' + stagedThesesToAdd.length + ') Mahasiswa' : 'Simpan Perubahan Jadwal'">Simpan Perubahan Jadwal</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- MODAL KONFIRMASI PEMBATALAN JADWAL BIMBINGAN -->
        <template x-teleport="body">
            <div x-show="cancelModalOpen" 
                 x-cloak 
                 @keydown.escape.window="if (cancelModalOpen) cancelModalOpen = false"
                 class="fixed inset-0 z-[60] overflow-y-auto" 
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
                         class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700 relative z-10">
                        
                        <form action="{{ route('mentoring-sessions.destroy', $mentoringSession->id) }}" method="POST" class="p-6 sm:p-8 space-y-5">
                            @csrf
                            @method('DELETE')

                            <!-- Header -->
                            <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100 dark:border-slate-700/80">
                                <div class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
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
                                        <span class="text-slate-500 dark:text-slate-400 text-[11px] ml-1">({{ $mentoringSession->thesis?->student?->identifier ?? '-' }})</span>
                                    </div>
                                </div>
                                <div class="h-px bg-slate-200/70 dark:bg-slate-700/70"></div>
                                <div class="flex justify-between items-start gap-3">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Waktu Sesi:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $mentoringSession->scheduled_at->locale('id')->translatedFormat('l, d F Y') }} • {{ $mentoringSession->time_range }}</span>
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
                                    <span class="text-[11px] font-black uppercase tracking-wider text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Sesi Bimbingan Bersama Terdeteksi</span>
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

        <!-- MODAL TAMBAH MAHASISWA KE JADWAL BIMBINGAN -->
        <template x-teleport="body">
            <div x-show="addStudentModalOpen" 
                 x-cloak 
                 @keydown.escape.window="if (addStudentModalOpen) addStudentModalOpen = false"
                 class="fixed inset-0 z-[60] overflow-y-auto" 
                 aria-labelledby="modal-add-student-title" 
                 role="dialog" 
                 aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Backdrop -->
                    <div x-show="addStudentModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                         @click="addStudentModalOpen = false" 
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!-- Modal Content -->
                    <div x-show="addStudentModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100 dark:border-slate-700 relative z-10">
                        
                        <div class="flex flex-col overflow-hidden max-h-[85vh]">

                            <!-- Header -->
                            <div class="p-6 sm:p-7 pb-4 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between shrink-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center shadow-xs shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100" id="modal-add-student-title">
                                            Tambah Mahasiswa ke Jadwal Ini
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            Pilih mahasiswa bimbingan yang terlewat untuk digabungkan saat jadwal disimpan.
                                        </p>
                                    </div>
                                </div>
                                <button type="button" @click="addStudentModalOpen = false" aria-label="Tutup modal tambah mahasiswa" class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <!-- Scrollable Body Content -->
                            <div class="p-6 sm:p-7 pt-4 space-y-4 overflow-y-auto flex-1">
                                <!-- Session Schedule Info Card (Live Preview of Main Form Values) -->
                                <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3 text-xs">
                                    <div class="min-w-0">
                                        <span class="text-[11px] font-bold uppercase tracking-wider text-orange-600 dark:text-orange-400">Jadwal Sesi (Sesuai Form)</span>
                                        <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                                            <span x-text="formatPreviewDate()"></span> • <span x-text="selectedTime"></span> – <span x-text="selectedEndTime"></span> WIB
                                        </p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs mt-0.5">
                                            Topik: <span x-text="topic || '-'"></span>
                                        </p>
                                    </div>
                                    <span class="px-2 py-0.5 text-[11px] font-bold rounded shrink-0"
                                          :class="type === 'online' ? 'bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60'"
                                          x-text="type === 'online' ? 'Online' : 'Offline'">
                                    </span>
                                </div>

                                <!-- If No Available Theses -->
                                <template x-if="availableTheses.length === 0">
                                    <div class="py-8 px-4 text-center space-y-2 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                                        <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Semua Mahasiswa Telah Terdaftar</h4>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                                            Seluruh mahasiswa bimbingan aktif Anda sudah terdaftar pada sesi jadwal ini. Tidak ada mahasiswa bimbingan aktif lain yang tersedia.
                                        </p>
                                    </div>
                                </template>

                                <!-- If Available Theses Exist -->
                                <template x-if="availableTheses.length > 0">
                                    <div class="space-y-3">
                                        <!-- Search & Quick Actions -->
                                        <div class="flex items-center gap-2">
                                            <div class="relative flex-1">
                                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                </div>
                                                <input type="text" 
                                                       x-model="addStudentSearch" 
                                                       placeholder="Cari nama, NPM, atau judul skripsi..." 
                                                       style="padding-left: 2.5rem !important;"
                                                       class="w-full pr-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                            </div>
                                            <button type="button" 
                                                    @click="selectAllAvailable()" 
                                                    class="px-2.5 py-1.5 text-[11px] font-bold text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40 rounded-lg transition-colors shrink-0 cursor-pointer">
                                                Pilih Semua
                                            </button>
                                            <button type="button" 
                                                    @click="clearAllAvailable()" 
                                                    x-show="modalSelection.length > 0"
                                                    class="px-2.5 py-1.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg transition-colors shrink-0 cursor-pointer">
                                                Reset
                                            </button>
                                        </div>

                                        <!-- Student Checklist Scrollable Area -->
                                        <div class="max-h-56 overflow-y-auto space-y-1.5 pr-1 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-2 bg-slate-50/40 dark:bg-slate-900/30">
                                            <template x-for="t in filteredAvailableTheses" :key="t.id">
                                                <label class="flex items-center gap-3 p-2.5 rounded-xl border transition-all cursor-pointer select-none"
                                                     :class="modalSelection.includes(t.id) 
                                                         ? 'bg-orange-50/90 dark:bg-orange-950/50 border-orange-400 dark:border-orange-600 shadow-2xs' 
                                                         : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                                    
                                                    <!-- Checkbox -->
                                                    <input type="checkbox" 
                                                           :value="t.id" 
                                                           :checked="modalSelection.includes(t.id)" 
                                                           @change="toggleModalThesis(t.id)"
                                                           class="w-4 h-4 text-orange-600 rounded border-slate-300 dark:border-slate-600 focus:ring-orange-500 cursor-pointer">
                                                    
                                                    <!-- Avatar / Initials -->
                                                    <template x-if="t.avatar">
                                                        <img :src="t.avatar" :alt="t.name" class="w-8 h-8 rounded-lg object-cover shrink-0 border border-slate-200 dark:border-slate-700">
                                                    </template>
                                                    <template x-if="!t.avatar">
                                                        <div class="w-8 h-8 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                                            <span x-text="t.name.substr(0, 2)"></span>
                                                        </div>
                                                    </template>

                                                    <!-- Info -->
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" x-text="t.name"></span>
                                                            <span class="text-[11px] px-1.5 py-0.2 rounded bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300 font-bold" x-text="t.npm"></span>
                                                            <span class="text-[11px] px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-medium" x-text="t.role_label"></span>
                                                        </div>
                                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5" x-text="t.title"></p>
                                                    </div>
                                                </label>
                                            </template>

                                            <template x-if="filteredAvailableTheses.length === 0">
                                                <div class="py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                                                    Tidak ditemukan mahasiswa yang cocok dengan pencarian.
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Notification Info -->
                                        <div class="p-2.5 bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/40 rounded-xl text-[11px] text-amber-800 dark:text-amber-300 flex items-start gap-2">
                                            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Mahasiswa terpilih akan masuk daftar tunggu terlebih dahulu. Perubahan jadwal &amp; notifikasi WhatsApp/Web baru dikirim setelah Anda menekan <strong>Simpan Perubahan Jadwal</strong>.</span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Modal Footer Actions (Pinned at Bottom) -->
                            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-end gap-3 shrink-0 rounded-b-3xl">
                                <button type="button" 
                                        @click="addStudentModalOpen = false" 
                                        class="px-5 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600 rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                    <span x-text="availableTheses.length === 0 ? 'Tutup' : 'Batal'"></span>
                                </button>
                                <button type="button" 
                                        @click="confirmStagedStudents()"
                                        x-show="availableTheses.length > 0"
                                        :disabled="modalSelection.length === 0 && stagedThesesToAdd.length === 0"
                                        :class="(modalSelection.length === 0 && stagedThesesToAdd.length === 0)
                                            ? 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed border border-slate-300 dark:border-slate-600 shadow-none' 
                                            : 'bg-orange-600 hover:bg-orange-700 text-white shadow-sm cursor-pointer'"
                                        class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span x-text="modalSelection.length > 0 ? 'Masukkan ke Daftar (' + modalSelection.length + ' Mahasiswa)' : (stagedThesesToAdd.length > 0 ? 'Kosongkan Daftar Tambahan' : 'Pilih Mahasiswa')"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>

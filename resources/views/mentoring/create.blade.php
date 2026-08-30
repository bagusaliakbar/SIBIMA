<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? route('mentoring-sessions.index') : route('dashboard') }}" class="p-2 rounded-xl bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                    {{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Buat Jadwal Bimbingan' : 'Pengajuan Jadwal Bimbingan' }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Pilih satu, beberapa, atau seluruh mahasiswa bimbingan dan tentukan jadwal sesi.' : 'Pilih jadwal dan topik untuk sesi bimbingan Anda berikutnya.' }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
        <div class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 transition-colors">
            <div class="mb-6 border-b border-slate-200 dark:border-slate-700 pb-4">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Form {{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Penjadwalan' : 'Pengajuan' }}</h3>
                <p class="text-slate-500 dark:text-slate-400 mt-1 text-xs">{{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Tentukan mahasiswa yang akan dibimbing serta waktu dan lokasi pelaksanaan.' : 'Pilih jadwal dan topik untuk sesi bimbingan Anda berikutnya.' }}</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20">
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
            
            <form x-ref="mentoringForm" action="{{ route('mentoring-sessions.store') }}" method="POST" class="space-y-6" x-data="{
                type: '{{ old('type', 'offline') }}',
                location: '{{ old('location', '') }}',
                topic: '{{ old('topic', '') }}',
                notes: '{{ old('notes', '') }}',
                selectedDosenId: '{{ old('dosen_id', '') }}',
                meetOpened: false,
                customTimeMode: false,
                isLecturer: {{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'true' : 'false' }},
                showConfirmModal: false,
                isSubmitting: false,
                
                // Student Selection Multi-Select State (Lecturer Only)
                selectionMode: '{{ (is_array(old('thesis_ids')) && in_array('all', old('thesis_ids'))) ? 'all' : 'multiple' }}',
                selectedThesisIds: {{ json_encode(array_values(array_map('strval', array_filter((array)old('thesis_ids', []))))) }},
                studentSearch: '',
                thesesList: {{ json_encode(($theses ?? collect())->map(fn($t) => [
                    'id' => (string)$t->id,
                    'name' => $t->student?->name ?? 'Mahasiswa',
                    'npm' => $t->student?->identifier ?? 'NPM -',
                    'avatar' => $t->student?->avatar_url,
                    'title' => $t->final_title ?? $t->title ?? 'Judul Skripsi',
                    'is_p1' => $t->pembimbing1_id === Auth::id(),
                    'is_p2' => $t->pembimbing2_id === Auth::id(),
                    'role_label' => ($t->pembimbing1_id === Auth::id()) ? 'Pembimbing 1' : (($t->pembimbing2_id === Auth::id()) ? 'Pembimbing 2' : 'Dosen'),
                    'p1_name' => $t->pembimbing1?->name ?? 'Pembimbing 1',
                    'p2_id' => $t->pembimbing2_id,
                    'p2_name' => $t->pembimbing2?->name,
                ])->values()) }},

                dosenOptions: {
                    @if(isset($thesis))
                        @if($thesis->pembimbing1)
                            '{{ $thesis->pembimbing1->id }}': 'Pembimbing 1: {{ addslashes($thesis->pembimbing1->name) }}',
                        @endif
                        @if($thesis->pembimbing2)
                            '{{ $thesis->pembimbing2->id }}': 'Pembimbing 2: {{ addslashes($thesis->pembimbing2->name) }}',
                        @endif
                    @endif
                },

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

                get selectedDosenText() {
                    if (this.selectedDosenId && this.dosenOptions[this.selectedDosenId]) {
                        return this.dosenOptions[this.selectedDosenId];
                    }
                    const el = document.getElementById('dosen_id');
                    if (el && el.selectedIndex > 0) {
                        return el.options[el.selectedIndex]?.text || '-';
                    }
                    return '-';
                },

                get formattedScheduledDate() {
                    if (!this.scheduledDate) return '-';
                    try {
                        const parts = this.scheduledDate.split('-');
                        if (parts.length === 3) {
                            const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                            return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                        }
                        return this.scheduledDate;
                    } catch(e) {
                        return this.scheduledDate;
                    }
                },

                openConfirmModal() {
                    const form = this.$refs.mentoringForm;
                    if (form && !form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    if (!this.isLecturer) {
                        const el = document.getElementById('dosen_id');
                        if (el && el.value) {
                            this.selectedDosenId = el.value;
                        }
                    }

                    if (this.isLecturer && this.selectionMode === 'multiple' && this.selectedThesisIds.length === 0) {
                        alert('Silakan pilih minimal 1 mahasiswa bimbingan.');
                        return;
                    }

                    this.showConfirmModal = true;
                },

                submitConfirmed() {
                    this.isSubmitting = true;
                    this.$refs.mentoringForm.submit();
                },

                init() {
                    this.validateAndAdjustTime();
                },

                get filteredTheses() {
                    if (!this.studentSearch.trim()) return this.thesesList;
                    const q = this.studentSearch.toLowerCase();
                    return this.thesesList.filter(t => 
                        t.name.toLowerCase().includes(q) || 
                        t.npm.toLowerCase().includes(q) || 
                        t.title.toLowerCase().includes(q)
                    );
                },

                toggleThesis(id) {
                    if (this.selectedThesisIds.includes(id)) {
                        this.selectedThesisIds = this.selectedThesisIds.filter(x => x !== id);
                    } else {
                        this.selectedThesisIds.push(id);
                    }
                },

                selectAllFiltered() {
                    const idsToAdd = this.filteredTheses.map(t => t.id);
                    this.selectedThesisIds = [...new Set([...this.selectedThesisIds, ...idsToAdd])];
                },

                clearAllSelection() {
                    this.selectedThesisIds = [];
                },

                isSelected(id) {
                    return this.selectedThesisIds.includes(id);
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
                    <!-- Multi-Select Mahasiswa Bimbingan Section -->
                    <div class="md:col-span-2 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <label class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                                Mahasiswa Bimbingan <span class="text-orange-600">*</span>
                            </label>
                            
                            <!-- Selection Mode Toggle (Pilih Beberapa vs Semua) -->
                            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-bold">
                                <button type="button" 
                                        @click="selectionMode = 'multiple'" 
                                        :class="selectionMode === 'multiple' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
                                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span>Pilih Satu / Beberapa Mahasiswa</span>
                                </button>
                                <button type="button" 
                                        @click="selectionMode = 'all'" 
                                        :class="selectionMode === 'all' ? 'bg-orange-600 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
                                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Pilih Semua ({{ count($theses) }} Mahasiswa)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Mode: ALL STUDENTS -->
                        <div x-show="selectionMode === 'all'" x-cloak class="p-4 bg-orange-50 dark:bg-orange-500/10 border border-orange-200/80 dark:border-orange-500/25 rounded-2xl flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                                    🌐
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-orange-950 dark:text-orange-200">Sesi Bimbingan Massal (Semua Mahasiswa)</h4>
                                    <p class="text-xs text-orange-800/80 dark:text-orange-300/80 mt-0.5">Jadwal bimbingan akan dibuat dan dikirimkan untuk seluruh {{ count($theses) }} mahasiswa bimbingan aktif Anda sekaligus.</p>
                                </div>
                            </div>
                            <input type="hidden" name="thesis_ids[]" value="all" :disabled="selectionMode !== 'all'">
                        </div>

                        <!-- Mode: MULTI / SINGLE SELECTION -->
                        <div x-show="selectionMode === 'multiple'" class="space-y-3">
                            <!-- Search & Batch Action Bar -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700/80">
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" 
                                           x-model="studentSearch" 
                                           placeholder="Ketik nama mahasiswa, NPM, atau judul skripsi..." 
                                           style="padding-left: 2.4rem !important;"
                                           class="block w-full pr-3.5 py-2 text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" 
                                            @click="selectAllFiltered()" 
                                            class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold transition-all shadow-2xs active:scale-95 cursor-pointer">
                                        ☑️ Centang Semua
                                    </button>
                                    <button type="button" 
                                            @click="clearAllSelection()" 
                                            x-show="selectedThesisIds.length > 0"
                                            class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 rounded-lg text-xs font-bold transition-all shadow-2xs active:scale-95 cursor-pointer">
                                        ✕ Reset (<span x-text="selectedThesisIds.length"></span>)
                                    </button>
                                    <span class="px-2.5 py-1.5 rounded-lg text-xs font-bold" 
                                          :class="selectedThesisIds.length > 0 ? 'bg-orange-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'">
                                        <span x-text="selectedThesisIds.length"></span> Mahasiswa Terpilih
                                    </span>
                                </div>
                            </div>

                            <!-- Student Cards List (Max height scrollable) -->
                            <div class="max-h-[340px] overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                <template x-for="t in filteredTheses" :key="t.id">
                                    <div @click="toggleThesis(t.id)"
                                         class="p-3 rounded-xl border transition-all cursor-pointer flex items-start gap-3 select-none"
                                         :class="isSelected(t.id) 
                                             ? 'bg-orange-50/70 dark:bg-orange-500/10 border-orange-300 dark:border-orange-500/40 shadow-xs' 
                                             : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'">
                                        
                                        <!-- Custom Checkbox -->
                                        <div class="pt-0.5 shrink-0">
                                            <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-colors"
                                                 :class="isSelected(t.id) ? 'bg-orange-600 border-orange-600 text-white' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800'">
                                                <svg x-show="isSelected(t.id)" class="w-3.5 h-3.5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </div>

                                        <!-- Hidden Form Input for Selected Thesis -->
                                        <template x-if="isSelected(t.id) && selectionMode === 'multiple'">
                                            <input type="hidden" name="thesis_ids[]" :value="t.id">
                                        </template>

                                        <!-- Student Avatar & Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase" x-text="t.name"></span>
                                                    <span class="text-[10px] font-mono font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200/80 dark:border-slate-700" x-text="t.npm"></span>
                                                </div>
                                                <span class="text-[9px] font-bold px-2 py-0.5 rounded uppercase tracking-wider"
                                                      :class="t.is_p1 ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-500/20' : 'bg-purple-50 dark:bg-purple-500/15 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-500/20'"
                                                      x-text="t.role_label">
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-slate-600 dark:text-slate-400 line-clamp-1 mt-1 italic" x-text="'&quot;' + t.title + '&quot;'"></p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Empty Search Result -->
                                <div x-show="filteredTheses.length === 0" class="p-8 text-center bg-slate-50 dark:bg-slate-900/40 rounded-xl border border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 space-y-1">
                                    <p class="font-bold">Tidak ada mahasiswa yang cocok dengan kata kunci "<span x-text="studentSearch"></span>"</p>
                                    <button type="button" @click="studentSearch = ''" class="text-orange-600 dark:text-orange-400 hover:underline font-semibold mt-1">Hapus kata kunci pencarian</button>
                                </div>
                            </div>

                            <!-- Validation Alert if 0 selected -->
                            <div x-show="selectionMode === 'multiple' && selectedThesisIds.length === 0" class="p-3 bg-amber-50 dark:bg-amber-500/10 border border-amber-200/80 dark:border-amber-500/25 rounded-xl text-xs font-semibold text-amber-800 dark:text-amber-300 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span>Silakan centang minimal 1 mahasiswa bimbingan pada daftar di atas.</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="md:col-span-2">
                        <label for="dosen_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Pilih Dosen Pembimbing <span class="text-orange-600">*</span></label>
                        <select name="dosen_id" id="dosen_id" x-model="selectedDosenId" required class="mt-2 block w-full rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
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
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                            <div class="flex items-center gap-2 flex-wrap">
                                <label class="text-sm font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-1">
                                    <span>Pilih Waktu Bimbingan</span>
                                    <span class="text-orange-600 font-bold">*</span>
                                </label>
                                <span class="inline-flex items-center gap-1 text-xs font-black text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/15 px-2.5 py-1 rounded-lg border border-orange-200/60 dark:border-orange-500/25 whitespace-nowrap shadow-xs">
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
                    
                    <div>
                        <label for="topic" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Topik Pembahasan <span class="text-orange-600">*</span></label>
                        <input type="text" name="topic" id="topic" x-model="topic" required placeholder="Contoh: Revisi Bab 1 & Metodologi" class="mt-2 block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tipe Bimbingan <span class="text-orange-600">*</span></label>
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
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-500/25 rounded-lg text-xs font-bold hover:bg-emerald-100 dark:hover:bg-emerald-500/25 transition-all shadow-2xs active:scale-95 cursor-pointer">
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
                                   class="block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-sm transition-all">
                        </div>

                        <!-- Smart Validation Feedback & Step Guide -->
                        <div x-show="type === 'online'" class="mt-2 space-y-2">
                            <div x-show="meetOpened && !location" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200/80 dark:border-emerald-500/25 rounded-xl flex items-center justify-between gap-3 text-xs text-emerald-800 dark:text-emerald-300">
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
                    <textarea name="notes" id="notes" x-model="notes" rows="3" class="mt-2 block w-full rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-3.5 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm sm:leading-6 transition-all" placeholder="Sampaikan poin yang perlu dipersiapkan mahasiswa sebelum bimbingan..."></textarea>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
                    <a href="{{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? route('mentoring-sessions.index') : route('dashboard') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-2xs">
                        Batal
                    </a>
                    <button type="button" 
                            @click="openConfirmModal()"
                            :disabled="isLecturer && selectionMode === 'multiple' && selectedThesisIds.length === 0"
                            :class="isLecturer && selectionMode === 'multiple' && selectedThesisIds.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:scale-[1.01] active:scale-95 cursor-pointer'"
                            class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 rounded-xl text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-orange-500/20 transition-all border border-transparent flex items-center gap-2">
                        <span>{{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Simpan Jadwal Bimbingan' : 'Kirim Permintaan' }}</span>
                        @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                            <template x-if="selectionMode === 'multiple' && selectedThesisIds.length > 0">
                                <span class="px-2 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-black" x-text="selectedThesisIds.length + ' Mhs'"></span>
                            </template>
                            <template x-if="selectionMode === 'all'">
                                <span class="px-2 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-black" x-text="'Semua Mhs ({{ count($theses) }})'"></span>
                            </template>
                        @endif
                    </button>
                </div>

                <!-- Modal Konfirmasi Rincian Jadwal Bimbingan -->
                <div x-show="showConfirmModal" 
                     x-cloak 
                     class="fixed inset-0 z-50 overflow-y-auto" 
                     aria-labelledby="modal-title" 
                     role="dialog" 
                     aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Backdrop -->
                        <div x-show="showConfirmModal" 
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                             @click="showConfirmModal = false" 
                             aria-hidden="true"></div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <!-- Modal Panel -->
                        <div x-show="showConfirmModal" 
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-700">
                            
                            <div class="p-6 sm:p-8">
                                <!-- Modal Header -->
                                <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-slate-100 dark:border-slate-700/80">
                                    <div class="w-11 h-11 rounded-2xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center font-bold text-xl shrink-0">
                                        📋
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">
                                            {{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Konfirmasi Penjadwalan Bimbingan' : 'Konfirmasi Pengajuan Bimbingan' }}
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            Periksa kembali rincian data bimbingan sebelum dikirimkan.
                                        </p>
                                    </div>
                                </div>

                                <!-- Summary Details Card -->
                                <div class="space-y-3 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 text-xs">
                                    <!-- Mahasiswa / Dosen Target -->
                                    @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                        <div class="flex justify-between items-start gap-3 py-1">
                                            <span class="text-slate-500 dark:text-slate-400 shrink-0 font-medium">Mahasiswa Sasaran:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right">
                                                <template x-if="selectionMode === 'all'">
                                                    <span>Seluruh Mahasiswa ({{ count($theses) }} Mahasiswa)</span>
                                                </template>
                                                <template x-if="selectionMode === 'multiple'">
                                                    <span x-text="selectedThesisIds.length + ' Mahasiswa Terpilih'"></span>
                                                </template>
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex justify-between items-start gap-3 py-1">
                                            <span class="text-slate-500 dark:text-slate-400 shrink-0 font-medium">Dosen Pembimbing:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right" x-text="selectedDosenText"></span>
                                        </div>
                                    @endif

                                    <div class="h-px bg-slate-200/70 dark:bg-slate-700/70"></div>

                                    <!-- Tanggal & Waktu -->
                                    <div class="flex justify-between items-start gap-3 py-1">
                                        <span class="text-slate-500 dark:text-slate-400 shrink-0 font-medium">Waktu Sesi:</span>
                                        <div class="text-right">
                                            <div class="font-bold text-slate-800 dark:text-slate-200" x-text="formattedScheduledDate"></div>
                                            <div class="text-orange-600 dark:text-orange-400 font-extrabold font-mono text-[11px] mt-0.5">
                                                <span x-text="selectedTime"></span> WIB
                                            </div>
                                        </div>
                                    </div>

                                    <div class="h-px bg-slate-200/70 dark:bg-slate-700/70"></div>

                                    <!-- Tipe & Tempat -->
                                    <div class="flex justify-between items-start gap-3 py-1">
                                        <span class="text-slate-500 dark:text-slate-400 shrink-0 font-medium">Tipe & Tempat:</span>
                                        <div class="text-right">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold" 
                                                  :class="type === 'offline' ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300' : 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300'"
                                                  x-text="type === 'offline' ? 'Tatap Muka (Offline)' : 'Daring (Online)'"></span>
                                            <div class="text-slate-700 dark:text-slate-300 font-medium text-[11px] mt-0.5 break-all line-clamp-2" x-text="location || '-'"></div>
                                        </div>
                                    </div>

                                    <div class="h-px bg-slate-200/70 dark:bg-slate-700/70"></div>

                                    <!-- Topik Pembahasan -->
                                    <div class="flex justify-between items-start gap-3 py-1">
                                        <span class="text-slate-500 dark:text-slate-400 shrink-0 font-medium">Topik:</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 text-right line-clamp-2" x-text="topic || '-'"></span>
                                    </div>

                                    <!-- Catatan jika ada -->
                                    <template x-if="notes && notes.trim()">
                                        <div class="pt-1">
                                            <div class="h-px bg-slate-200/70 dark:bg-slate-700/70 mb-2"></div>
                                            <div class="flex justify-between items-start gap-3">
                                                <span class="text-slate-500 dark:text-slate-400 shrink-0 font-medium">Catatan:</span>
                                                <span class="font-normal italic text-slate-600 dark:text-slate-300 text-right line-clamp-2" x-text="notes"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Info Alert -->
                                <div class="mt-4 p-3 rounded-xl bg-orange-50/70 dark:bg-orange-500/10 border border-orange-200/80 dark:border-orange-500/20 text-orange-800 dark:text-orange-300 text-[11px] flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>{{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Notifikasi jadwal bimbingan ini akan langsung dikirimkan ke seluruh mahasiswa terkait melalui sistem & WhatsApp.' : 'Notifikasi pengajuan bimbingan ini akan langsung dikirimkan ke dosen pembimbing melalui sistem & WhatsApp.' }}</span>
                                </div>

                                <!-- Modal Actions -->
                                <div class="mt-6 flex items-center justify-end gap-3">
                                    <button type="button" 
                                            @click="showConfirmModal = false" 
                                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                        Periksa Kembali
                                    </button>
                                    <button type="button" 
                                            @click="submitConfirmed()" 
                                            :disabled="isSubmitting"
                                            class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md shadow-orange-500/20 hover:scale-[1.02] active:scale-95 flex items-center gap-1.5 cursor-pointer">
                                        <span x-show="!isSubmitting">✓ {{ in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) ? 'Ya, Simpan Jadwal' : 'Ya, Kirim Pengajuan' }}</span>
                                        <span x-show="isSubmitting" class="flex items-center gap-1.5">
                                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <span>Menyimpan...</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

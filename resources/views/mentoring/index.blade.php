<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Bimbingan', 'route' => null]
        ]" />
    </x-slot>

    <script>
        function mentoringSchedule() {
            return {
                viewMode: '{{ request('view', 'cards') }}',
                cardGrouping: localStorage.getItem('sibima_card_grouping') || 'session', // 'session' (default) or 'student'
                selectedEvent: null,
                eventModalOpen: false,
                calendarInitialized: false,
                events: @json($calendarEvents ?? []),
                attendanceStats: @json($attendanceStats ?? []),
                setGrouping(mode) {
                    this.cardGrouping = mode;
                    localStorage.setItem('sibima_card_grouping', mode);
                },
                initCalendar() {
                    if (this.calendarInitialized) return;
                    this.$nextTick(() => {
                        const calendarEl = document.getElementById('mentoring-calendar');
                        if (!calendarEl || typeof FullCalendar === 'undefined') return;
                        
                        const calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth',
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,listMonth'
                            },
                            buttonText: {
                                today: 'Hari Ini',
                                month: 'Bulan',
                                week: 'Minggu',
                                list: 'Agenda'
                            },
                            locale: 'id',
                            events: this.events,
                            eventClick: (info) => {
                                window.dispatchEvent(new CustomEvent('open-event-modal', { detail: info.event.extendedProps }));
                            },
                            eventTimeFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                meridiem: false,
                                hour12: false
                            },
                            height: 'auto',
                            contentHeight: 650,
                            dayMaxEvents: 3
                        });
                        calendar.render();
                        this.calendarInitialized = true;
                    });
                },
                switchView(mode) {
                    this.viewMode = mode;
                    if (mode === 'calendar') {
                        this.initCalendar();
                    }
                },
                init() {
                    window.__mentoringScope = this;
                    if (this.viewMode === 'calendar') {
                        this.initCalendar();
                    }
                }
            };
        }

        function mentoringLiveAttendance() {
            return {
                liveModalOpen: false,
                liveTab: 'all',
                liveSearch: '',
                liveViewMode: 'session', // 'session' (default, grouped per sesi) or 'flat' (daftar mahasiswa)
                isSyncing: false,
                lastUpdated: '{{ now()->locale('id')->translatedFormat('H:i:s') }} WIB',
                attendanceStats: @json($attendanceStats ?? []),
                liveSessions: [],
                openModal() {
                    this.liveModalOpen = true;
                    this.fetchLiveAttendance(false);
                },
                get filteredLiveSessions() {
                    if (!Array.isArray(this.liveSessions)) return [];
                    return this.liveSessions.filter(item => {
                        if (!item) return false;
                        const matchesTab = (this.liveTab === 'all') || (item.attendance_status === this.liveTab);
                        const searchLower = (this.liveSearch || '').toLowerCase().trim();
                        const matchesSearch = !searchLower || 
                            (item.student_name && item.student_name.toLowerCase().includes(searchLower)) ||
                            (item.student_identifier && String(item.student_identifier).toLowerCase().includes(searchLower)) ||
                            (item.topic && item.topic.toLowerCase().includes(searchLower)) ||
                            (item.scheduled_date_formatted && item.scheduled_date_formatted.toLowerCase().includes(searchLower)) ||
                            (item.dosen_name && item.dosen_name.toLowerCase().includes(searchLower));
                        return matchesTab && matchesSearch;
                    });
                },
                get groupedLiveSessions() {
                    if (!Array.isArray(this.liveSessions)) return [];
                    const searchLower = (this.liveSearch || '').toLowerCase().trim();
                    const groupsMap = new Map();

                    for (const item of this.liveSessions) {
                        if (!item) continue;
                        
                        const matchesTab = (this.liveTab === 'all') || (item.attendance_status === this.liveTab);
                        const matchesSearch = !searchLower || 
                            (item.student_name && item.student_name.toLowerCase().includes(searchLower)) ||
                            (item.student_identifier && String(item.student_identifier).toLowerCase().includes(searchLower)) ||
                            (item.topic && item.topic.toLowerCase().includes(searchLower)) ||
                            (item.scheduled_date_formatted && item.scheduled_date_formatted.toLowerCase().includes(searchLower)) ||
                            (item.dosen_name && item.dosen_name.toLowerCase().includes(searchLower));
                            
                        if (!matchesTab || !matchesSearch) continue;

                        const groupKey = item.session_group_key || 
                            `${item.scheduled_at}_${item.dosen_name || ''}_${item.topic || ''}_${item.type || ''}_${item.location || ''}`;

                        if (!groupsMap.has(groupKey)) {
                            groupsMap.set(groupKey, {
                                key: groupKey,
                                scheduled_at: item.scheduled_at,
                                scheduled_date_formatted: item.scheduled_date_formatted,
                                scheduled_time_formatted: item.scheduled_time_formatted,
                                is_today: item.is_today,
                                topic: item.topic || 'Bimbingan',
                                type: item.type,
                                location: item.location,
                                dosen_name: item.dosen_name,
                                students: [],
                                stats: {
                                    total: 0,
                                    attending: 0,
                                    permission: 0,
                                    pending: 0
                                }
                            });
                        }

                        const group = groupsMap.get(groupKey);
                        group.students.push(item);
                        group.stats.total++;
                        if (item.attendance_status === 'attending') group.stats.attending++;
                        else if (item.attendance_status === 'permission') group.stats.permission++;
                        else group.stats.pending++;
                    }

                    return Array.from(groupsMap.values());
                },
                getWaLink(item) {
                    if (!item || !item.student_phone) return '#';
                    const phone = String(item.student_phone).replace(/^0/, '62').replace(/\D/g, '');
                    const text = encodeURIComponent(`Halo ${item.student_name || 'Mahasiswa'}, pengingat jadwal bimbingan SIBIMA pada ${item.scheduled_date_formatted || ''} pukul ${item.scheduled_time_formatted || ''}. Topik: ${item.topic || ''}.`);
                    return `https://wa.me/${phone}?text=${text}`;
                },
                getInitials(name) {
                    if (!name) return 'MH';
                    return name.substring(0, 2).toUpperCase();
                },
                async fetchLiveAttendance(silent = false) {
                    if (!silent) this.isSyncing = true;
                    try {
                        const dosenId = '{{ $dosenId ?? '' }}';
                        const url = '{{ route('mentoring-sessions.live-attendance') }}' + (dosenId ? '?dosen_id=' + dosenId : '');
                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (data.summary) {
                                this.attendanceStats = data.summary;
                                this.lastUpdated = data.summary.last_updated || this.lastUpdated;
                                if (window.__mentoringScope) {
                                    window.__mentoringScope.attendanceStats = data.summary;
                                }
                            }
                            if (data.sessions) {
                                this.liveSessions = data.sessions;
                            }
                        }
                    } catch (e) {
                        console.error('Live attendance sync error:', e);
                    } finally {
                        if (!silent) this.isSyncing = false;
                    }
                },
                init() {
                    window.__liveAttendanceScope = this;
                    this.fetchLiveAttendance(true);
                    setInterval(() => {
                        this.fetchLiveAttendance(true);
                    }, 10000);
                }
            };
        }

        function mentoringCancelModal() {
            return {
                cancelModalOpen: false,
                cancelData: {
                    id: null,
                    student_name: '',
                    student_npm: '',
                    topic: '',
                    scheduled_date: '',
                    scheduled_time: '',
                    is_group: false,
                    group_count: 1,
                    reason: '',
                    apply_to_group: true,
                },
                openModal(data) {
                    if (!data) return;
                    this.cancelData = {
                        id: data.id,
                        student_name: data.student_name || 'Mahasiswa',
                        student_npm: data.student_npm || '-',
                        topic: data.topic || '-',
                        scheduled_date: data.scheduled_date || '-',
                        scheduled_time: data.scheduled_time || '-',
                        is_group: !!data.is_group,
                        group_count: data.group_count || 1,
                        reason: '',
                        apply_to_group: !!data.is_group,
                    };
                    this.cancelModalOpen = true;
                },
                init() {
                    window.__cancelModalScope = this;
                }
            };
        }

        function mentoringEventModal() {
            return {
                eventModalOpen: false,
                selectedEvent: null,
                editingFeedback: false,
                feedbackText: '',
                openModal(eventData) {
                    this.selectedEvent = eventData;
                    this.editingFeedback = false;
                    this.feedbackText = eventData?.feedback || '';
                    this.eventModalOpen = true;
                },
                init() {
                    window.__eventModalScope = this;
                }
            };
        }

        window.mentoringSchedule = mentoringSchedule;
        window.mentoringEventModal = mentoringEventModal;
        window.mentoringLiveAttendance = mentoringLiveAttendance;
        window.mentoringCancelModal = mentoringCancelModal;

        if (window.Alpine) {
            window.Alpine.data('mentoringSchedule', mentoringSchedule);
            window.Alpine.data('mentoringEventModal', mentoringEventModal);
            window.Alpine.data('mentoringLiveAttendance', mentoringLiveAttendance);
            window.Alpine.data('mentoringCancelModal', mentoringCancelModal);
        }
        document.addEventListener('alpine:init', () => {
            if (window.Alpine) {
                window.Alpine.data('mentoringSchedule', mentoringSchedule);
                window.Alpine.data('mentoringEventModal', mentoringEventModal);
                window.Alpine.data('mentoringLiveAttendance', mentoringLiveAttendance);
                window.Alpine.data('mentoringCancelModal', mentoringCancelModal);
            }
        });

        window.openCancelModal = function(data) {
            if (!data) return;
            if (window.__cancelModalScope && typeof window.__cancelModalScope.openModal === 'function') {
                window.__cancelModalScope.openModal(data);
            }
            window.dispatchEvent(new CustomEvent('open-cancel-modal', { detail: data }));
        };

        window.openCancelModalFromEl = function(el) {
            if (!el) return;
            const data = {
                id: el.getAttribute('data-session-id'),
                student_name: el.getAttribute('data-student-name') || 'Mahasiswa',
                student_npm: el.getAttribute('data-student-npm') || '-',
                topic: el.getAttribute('data-topic') || '-',
                scheduled_date: el.getAttribute('data-scheduled-date') || '-',
                scheduled_time: el.getAttribute('data-scheduled-time') || '-',
                is_group: el.getAttribute('data-is-group') === '1',
                group_count: parseInt(el.getAttribute('data-group-count') || '1'),
            };
            window.openCancelModal(data);
        };

        window.openLiveModal = function() {
            if (window.__liveAttendanceScope && typeof window.__liveAttendanceScope.openModal === 'function') {
                window.__liveAttendanceScope.openModal();
            }
            window.dispatchEvent(new CustomEvent('open-live-modal'));
        };
    </script>

    <div class="w-full" x-data="mentoringSchedule()">
        
        <!-- KPI Quick Bar (4 Metrik Ringkas) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- 1. Jadwal Minggu Ini -->
            <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-orange-200 dark:hover:border-orange-500/30">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Jadwal Minggu Ini</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $kpiStats['this_week'] ?? 0 }}</span>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Sesi</span>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Sesi terjadwal aktif minggu ini</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-orange-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- 2. Menunggu Hasil / Catatan Dosen -->
            <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-amber-200 dark:hover:border-amber-500/30">
                <div class="space-y-1">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Perlu Catatan / Hasil</span>
                        @if(($kpiStats['pending_feedback'] ?? 0) > 0)
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black {{ ($kpiStats['pending_feedback'] ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-100' }}">{{ $kpiStats['pending_feedback'] ?? 0 }}</span>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Sesi</span>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Sesi lewat waktu belum dinilai</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
            </div>

            <!-- 3. Mahasiswa Siap ACC Seminar -->
            <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-500/30">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Siap ACC Seminar</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black {{ ($kpiStats['ready_acc_seminar'] ?? 0) > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-800 dark:text-slate-100' }}">{{ $kpiStats['ready_acc_seminar'] ?? 0 }}</span>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Mhs</span>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Mencapai syarat &ge; 4x bimbingan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-600/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
            </div>

            <!-- 4. Mahasiswa Siap ACC Sidang -->
            <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xs flex items-center justify-between transition-all hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-500/30">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Siap ACC Sidang</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black {{ ($kpiStats['ready_acc_sidang'] ?? 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-slate-100' }}">{{ $kpiStats['ready_acc_sidang'] ?? 0 }}</span>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Mhs</span>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Mencapai syarat &ge; 8x bimbingan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-600/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <x-table-card 
            title="{{ $activeTab === 'history' ? 'Riwayat Bimbingan' : 'Jadwal Bimbingan' }}"
            :footer="method_exists($sessions, 'links') ? $sessions->links() : null">
            
            <x-slot name="headerActions">
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <!-- View Mode Toggle (Cards vs Calendar) -->
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                        <button type="button" 
                                @click="switchView('cards')" 
                                :class="viewMode === 'cards' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span>Kartu</span>
                        </button>
                        <button type="button" 
                                @click="switchView('calendar')" 
                                :class="viewMode === 'calendar' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Kalender</span>
                        </button>
                    </div>

                    @if(in_array(Auth::user()->role, ['admin', 'kaprodi']) && isset($dosens))
                        <form action="{{ route('mentoring-sessions.index') }}" method="GET" class="inline-block">
                            <input type="hidden" name="tab" value="{{ $activeTab }}">
                            <input type="hidden" name="search" value="{{ $search }}">
                            <select name="dosen_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-[11px] font-bold text-slate-700 dark:text-slate-200 py-2.5 px-3 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Filter Dosen Pembimbing...</option>
                                @foreach($dosens as $d)
                                    <option value="{{ $d->id }}" {{ ($dosenId ?? '') == $d->id ? 'selected' : '' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif

                    <template x-if="viewMode === 'cards'">
                        <x-search-input 
                            name="search" 
                            :value="$search ?? ''" 
                            placeholder="Cari nama atau topik..." 
                            route="mentoring-sessions.index"
                            :params="['tab' => $activeTab, 'dosen_id' => $dosenId ?? '']" />
                    </template>

                    @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                        <a href="{{ route('mentoring-sessions.create') }}" class="inline-flex items-center px-4 py-2.5 bg-orange-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-700 transition-all shadow-sm whitespace-nowrap">+ Tambah Jadwal</a>
                    @endif
                </div>
            </x-slot>

            <div class="p-6">
                <!-- 1. CARDS VIEW -->
                <div x-show="viewMode === 'cards'" x-transition>
                    <!-- Sub-Toolbar: Navigation Tabs & Real-Time Actions -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-800">
                        <!-- Primary Tabs: Aktif vs Riwayat -->
                        <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-2xs">
                            <a href="{{ route('mentoring-sessions.index', ['tab' => 'active', 'search' => $search, 'dosen_id' => $dosenId ?? '']) }}" 
                               class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $activeTab === 'active' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-200/60 dark:hover:bg-slate-700/60' }}">
                                Bimbingan Aktif
                            </a>
                            <a href="{{ route('mentoring-sessions.index', ['tab' => 'history', 'search' => $search, 'dosen_id' => $dosenId ?? '']) }}" 
                               class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $activeTab === 'history' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-200/60 dark:hover:bg-slate-700/60' }}">
                                Riwayat Bimbingan
                            </a>
                        </div>

                        <!-- Grouping Switcher & Real-Time Actions -->
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end flex-wrap">
                            <!-- Toggle Mode Grouping: Per Sesi (Default) vs Per Mahasiswa -->
                            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                                <button type="button" 
                                        @click="setGrouping('session')" 
                                        :class="cardGrouping === 'session' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs font-black' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-bold'"
                                        title="Kelompokkan kartu berdasarkan sesi bimbingan"
                                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    <span>Per Sesi</span>
                                </button>
                                <button type="button" 
                                        @click="setGrouping('student')" 
                                        :class="cardGrouping === 'student' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs font-black' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-bold'"
                                        title="Kelompokkan kartu berdasarkan nama mahasiswa"
                                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span>Per Mahasiswa</span>
                                </button>
                            </div>

                            <button type="button" 
                                    id="btn-monitor-kehadiran"
                                    @click="openLiveModal()" 
                                    onclick="window.openLiveModal()" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition-all active:scale-95 cursor-pointer">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-200 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                </span>
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>Monitor Kehadiran</span>
                            </button>

                            <button type="button" 
                                    @click="fetchLiveAttendance(false)" 
                                    title="Segarkan data kehadiran real-time"
                                    class="p-2 rounded-xl bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all cursor-pointer shadow-2xs hover:scale-105 active:scale-95">
                                <svg class="w-4 h-4 shrink-0" :class="isSyncing ? 'animate-spin text-orange-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                        </div>
                    </div>

                    @if($activeTab === 'active')
                    <!-- Quick Attendance Filter Pills (Clean, Spacious, Borderless) -->
                    <div class="flex items-center gap-3 overflow-x-auto pb-3 mb-6 pt-1">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 shrink-0 mr-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            <span>Filter Kehadiran:</span>
                        </span>

                        {{-- Semua --}}
                        <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '']) }}" 
                           class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ empty($attendanceFilter) ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200' }}">
                            <span>Semua</span>
                            <span class="min-w-[20px] h-5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ empty($attendanceFilter) ? 'bg-white/25 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200' }}" x-text="attendanceStats?.total ?? '{{ $attendanceStats['total'] ?? 0 }}'">
                                {{ $attendanceStats['total'] ?? 0 }}
                            </span>
                        </a>

                        {{-- Akan Hadir --}}
                        <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '', 'attendance' => 'attending']) }}" 
                           class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ $attendanceFilter === 'attending' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200' }}">
                            <span class="w-2 h-2 rounded-full {{ $attendanceFilter === 'attending' ? 'bg-white' : 'bg-emerald-500' }} shrink-0"></span>
                            <span>Akan Hadir</span>
                            <span class="min-w-[20px] h-5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ $attendanceFilter === 'attending' ? 'bg-white/25 text-white' : 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300' }}" x-text="attendanceStats?.attending ?? '{{ $attendanceStats['attending'] ?? 0 }}'">
                                {{ $attendanceStats['attending'] ?? 0 }}
                            </span>
                        </a>

                        {{-- Izin / Berhalangan --}}
                        <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '', 'attendance' => 'permission']) }}" 
                           class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ $attendanceFilter === 'permission' ? 'bg-amber-600 text-white shadow-md shadow-amber-500/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200' }}">
                            <span class="w-2 h-2 rounded-full {{ $attendanceFilter === 'permission' ? 'bg-white' : 'bg-amber-500' }} shrink-0"></span>
                            <span>Izin / Berhalangan</span>
                            <span class="min-w-[20px] h-5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ $attendanceFilter === 'permission' ? 'bg-white/25 text-white' : 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300' }}" x-text="attendanceStats?.permission ?? '{{ $attendanceStats['permission'] ?? 0 }}'">
                                {{ $attendanceStats['permission'] ?? 0 }}
                            </span>
                        </a>

                        {{-- Belum Respon --}}
                        <a href="{{ route('mentoring-sessions.index', ['tab' => $activeTab, 'search' => $search, 'dosen_id' => $dosenId ?? '', 'attendance' => 'pending']) }}" 
                           class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ $attendanceFilter === 'pending' ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200' }}">
                            <span class="w-2 h-2 rounded-full {{ $attendanceFilter === 'pending' ? 'bg-white' : 'bg-slate-400 dark:bg-slate-500' }} animate-pulse shrink-0"></span>
                            <span>Belum Respon</span>
                            <span class="min-w-[20px] h-5 px-1.5 inline-flex items-center justify-center rounded-full text-[11px] font-black {{ $attendanceFilter === 'pending' ? 'bg-white/25 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200' }}" x-text="attendanceStats?.pending ?? '{{ $attendanceStats['pending'] ?? 0 }}'">
                                {{ $attendanceStats['pending'] ?? 0 }}
                            </span>
                        </a>
                    </div>
                    @endif

                    @php
                        // 1. Grouped by Mentoring Session
                        $sessionsBySessionGroup = $sessions->groupBy(function($s) {
                            return ($s->dosen_id ?? '0') . '_' . $s->scheduled_at->format('Y-m-d_H:i') . '_' . md5($s->topic ?? '');
                        });

                        // 2. Grouped by Student (Original)
                        $groupedSessions = $sessions->groupBy(function($session) {
                            return $session->thesis->student->name ?? 'Mahasiswa';
                        });

                        $groupCountMap = [];
                        foreach($sessions as $s) {
                            $key = ($s->dosen_id ?? '0') . '_' . $s->scheduled_at->format('Y-m-d H:i');
                            $groupCountMap[$key] = ($groupCountMap[$key] ?? 0) + 1;
                        }
                    @endphp

                    @if($sessions->isEmpty())
                        @if($activeTab === 'history')
                            <x-empty-state description="Belum ada riwayat bimbingan untuk mahasiswa yang sudah lulus." icon="mentoring" />
                        @else
                            <x-empty-state description="Belum ada jadwal bimbingan aktif." icon="mentoring" />
                        @endif
                    @else
                        <!-- 1. TAMPILAN PER SESI BIMBINGAN (DEFAULT) -->
                        <div x-show="cardGrouping === 'session'" class="space-y-8">
                            @foreach($sessionsBySessionGroup as $groupKey => $sessionItems)
                                @php
                                    $firstSession = $sessionItems->first();
                                    $sessionDosen = $firstSession->dosen ?? $firstSession->thesis?->pembimbing1;
                                    $totalMhs = $sessionItems->count();
                                    $attendingCount = $sessionItems->where('student_attendance_status', 'attending')->count();
                                    $permissionCount = $sessionItems->where('student_attendance_status', 'permission')->count();
                                    $pendingCount = $sessionItems->where('student_attendance_status', 'pending')->count();
                                    
                                    $isMeet = Str::contains($firstSession->location ?? '', 'meet.google.com'); 
                                    $isZoom = Str::contains($firstSession->location ?? '', ['zoom.us', 'zoom.com']);
                                    $linkUrl = Str::startsWith($firstSession->location ?? '', 'http') ? $firstSession->location : 'https://' . $firstSession->location;
                                @endphp

                                <div class="bg-white dark:bg-slate-800/90 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden transition-all hover:border-slate-300 dark:hover:border-slate-600">
                                    <!-- Sesi Header Bar -->
                                    <div class="p-5 sm:px-6 bg-slate-50/90 dark:bg-slate-800/90 border-b border-slate-200/80 dark:border-slate-700/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="space-y-2 min-w-0 flex-1">
                                            <div class="flex items-center gap-2.5 flex-wrap">
                                                <!-- Tanggal & Jam -->
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-500 text-white text-xs font-black shadow-xs shadow-orange-500/20">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span>{{ $firstSession->scheduled_at->locale('id')->translatedFormat('d M Y') }} • {{ $firstSession->scheduled_at->format('H:i') }} WIB</span>
                                                </span>

                                                @if($firstSession->scheduled_at->isToday())
                                                    <span class="px-2.5 py-1 rounded-lg bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-300 text-[10px] font-black uppercase tracking-wider border border-red-200 dark:border-red-800/80 animate-pulse">
                                                        Hari Ini
                                                    </span>
                                                @endif

                                                <!-- Lokasi / Metode -->
                                                @if($firstSession->type === 'online')
                                                    @if($firstSession->location)
                                                        <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl {{ $isMeet ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100' : ($isZoom ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 hover:bg-blue-100' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800') }} transition-all font-bold text-xs shadow-2xs">
                                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                            <span>{{ $isMeet ? 'Buka Meet' : ($isZoom ? 'Buka Zoom' : 'Link Online') }}</span>
                                                        </a>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-xl border border-indigo-200 dark:border-indigo-800 text-xs font-bold shadow-2xs">
                                                            🎥 Daring
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center gap-1 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold shadow-2xs">
                                                        🏢 {{ $firstSession->location ?? 'Offline' }}
                                                    </span>
                                                @endif

                                                <!-- Dosen -->
                                                @if($sessionDosen)
                                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                                        • Dosen: <strong class="text-slate-700 dark:text-slate-200">{{ $sessionDosen->name }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Topik Sesi -->
                                            <div class="pt-1">
                                                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white leading-snug">
                                                    {{ $firstSession->topic }}
                                                </h4>
                                            </div>
                                        </div>

                                        <!-- Ringkasan Sesi / Counters -->
                                        <div class="flex items-center gap-2 shrink-0 flex-wrap">
                                            <span class="px-3 py-1.5 rounded-xl bg-slate-200/80 dark:bg-slate-700/80 text-slate-800 dark:text-slate-100 text-xs font-black uppercase tracking-wider shadow-2xs">
                                                👥 {{ $totalMhs }} Mahasiswa
                                            </span>
                                            @if($attendingCount > 0)
                                                <span class="px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-200 text-xs font-bold border border-emerald-200 dark:border-emerald-800/80 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    <span>{{ $attendingCount }} Hadir</span>
                                                </span>
                                            @endif
                                            @if($permissionCount > 0)
                                                <span class="px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-200 text-xs font-bold border border-amber-200 dark:border-amber-800/80 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01"></path></svg>
                                                    <span>{{ $permissionCount }} Izin</span>
                                                </span>
                                            @endif
                                            @if($pendingCount > 0)
                                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold border border-slate-200 dark:border-slate-700 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                    <span>{{ $pendingCount }} Belum Respon</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Mahasiswa Grid di dalam Sesi Ini -->
                                    <div class="p-5 sm:p-6 bg-slate-50/40 dark:bg-slate-900/40">
                                        <div class="grid grid-cols-1 {{ $totalMhs === 1 ? '' : ($totalMhs === 2 ? 'md:grid-cols-2' : 'md:grid-cols-2 lg:grid-cols-3') }} gap-5">
                                            @foreach($sessionItems as $session)
                                                @php
                                                    $student = $session->thesis?->student;
                                                    $thesis = $session->thesis;
                                                    $sMentoringCount = (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') 
                                                        ? $thesis?->completed_mentoring_count 
                                                        : $thesis?->getCompletedMentoringCountForDosen(Auth::id());
                                                        
                                                    $isAdminOrKaprodi = in_array(Auth::user()->role, ['admin', 'kaprodi']);
                                                    $isP1 = Auth::id() === $thesis?->pembimbing1_id;
                                                    $isP2 = Auth::id() === $thesis?->pembimbing2_id;
                                                    $hasAccUp = $isAdminOrKaprodi ? ($thesis?->acc_up_p1 && $thesis?->acc_up_p2) : ($isP1 ? $thesis?->acc_up_p1 : ($isP2 ? $thesis?->acc_up_p2 : false));
                                                    $hasAccSidang = $isAdminOrKaprodi ? ($thesis?->acc_sidang_p1 && $thesis?->acc_sidang_p2) : ($isP1 ? $thesis?->acc_sidang_p1 : ($isP2 ? $thesis?->acc_sidang_p2 : false));
                                                @endphp

                                                <div class="bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700/80 rounded-2xl p-5 relative overflow-hidden group hover:shadow-lg hover:shadow-slate-200/40 dark:hover:shadow-none hover:border-orange-300 dark:hover:border-orange-500/40 transition-all flex flex-col justify-between">
                                                    <div>
                                                        <!-- Top Bar Status Accent -->
                                                        <div class="absolute top-0 left-0 w-full h-1.5 
                                                            {{ $session->status === 'pending' ? 'bg-amber-400' : '' }}
                                                            {{ $session->status === 'approved' ? 'bg-orange-600' : '' }}
                                                            {{ $session->status === 'rejected' || $session->is_absent ? 'bg-red-500' : '' }}
                                                            {{ $session->status === 'completed' && !$session->is_absent ? 'bg-slate-300 dark:bg-slate-700' : '' }}
                                                        "></div>

                                                        <!-- Student Avatar & Name Header -->
                                                        <div class="flex items-start justify-between gap-3 mb-3 pt-1">
                                                            <div class="flex items-center gap-3 min-w-0">
                                                                <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-2xs bg-slate-100 dark:bg-slate-800 shrink-0">
                                                                    <img src="{{ $student?->avatar_url }}" alt="{{ $student?->name ?? 'Mhs' }}" class="w-full h-full object-cover">
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <h5 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight truncate">{{ $student?->name ?? 'Mahasiswa' }}</h5>
                                                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold font-mono">{{ $student?->identifier ?? '-' }}</p>
                                                                </div>
                                                            </div>

                                                            <!-- Status Badge -->
                                                            <div class="shrink-0">
                                                                @if($session->is_absent)
                                                                    <x-status-badge type="red" label="TIDAK HADIR" />
                                                                @else
                                                                    <x-status-badge 
                                                                        :type="$session->status === 'pending' ? 'amber' : ($session->status === 'approved' ? 'orange' : ($session->status === 'rejected' ? 'red' : ($session->status === 'completed' ? 'emerald' : 'slate')))" 
                                                                        :label="$session->status === 'completed' ? 'HADIR' : strtoupper($session->status)" />
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Judul Skripsi & Info Tambahan -->
                                                        <div class="space-y-2 mb-3">
                                                            @if($thesis?->title)
                                                                <p class="text-[11px] text-slate-600 dark:text-slate-300 font-medium line-clamp-2 leading-relaxed" title="{{ $thesis->title }}">
                                                                    <span class="text-slate-400 dark:text-slate-500 font-bold uppercase text-[9px] tracking-wider block">Judul Skripsi:</span>
                                                                    {{ $thesis->title }}
                                                                </p>
                                                            @endif

                                                            <div class="flex items-center gap-2 flex-wrap text-[10px]">
                                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/80 text-slate-700 dark:text-slate-300 font-bold">
                                                                    {{ $sMentoringCount }}x Bimbingan
                                                                </span>
                                                                @if($thesis?->pembimbing1)
                                                                    <span class="text-[10px] text-slate-500 dark:text-slate-400">P1: <strong class="text-slate-700 dark:text-slate-300">{{ $thesis->pembimbing1->name }}</strong></span>
                                                                @endif
                                                            </div>

                                                            <!-- Catatan Mahasiswa -->
                                                            @if($session->notes)
                                                                <div class="p-2.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                                                                    <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-0.5">Catatan Mhs:</p>
                                                                    <p class="text-[11px] text-slate-600 dark:text-slate-300 italic leading-relaxed">"{{ $session->notes }}"</p>
                                                                </div>
                                                            @endif

                                                            <!-- Catatan Dosen (dengan mode revisi) -->
                                                            <div x-data="{ editingFeedback: false }">
                                                                @if($session->feedback)
                                                                    <div x-show="!editingFeedback" class="p-2.5 bg-orange-50/50 dark:bg-orange-950/20 rounded-xl border border-orange-200/70 dark:border-orange-900/40 shadow-2xs group relative">
                                                                        <div class="flex items-center justify-between gap-2 mb-0.5">
                                                                            <p class="text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                                                                <span>Catatan Dosen:</span>
                                                                            </p>
                                                                            @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                                <button type="button" 
                                                                                        @click="editingFeedback = true" 
                                                                                        title="Ubah catatan bimbingan" 
                                                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 hover:bg-orange-100/60 dark:hover:bg-orange-900/40 transition-all cursor-pointer">
                                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                                    <span>Ubah</span>
                                                                                </button>
                                                                            @endif
                                                                        </div>
                                                                        <p class="text-[11px] text-slate-700 dark:text-slate-300 font-medium leading-relaxed whitespace-pre-line">{{ $session->feedback }}</p>
                                                                    </div>
                                                                @elseif($session->status === 'completed' && !$session->is_absent && in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                    <div x-show="!editingFeedback" class="p-2 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-center">
                                                                        <button type="button" 
                                                                                @click="editingFeedback = true" 
                                                                                class="text-[10px] font-bold text-orange-600 dark:text-orange-400 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                                            <span>+ Tambah Catatan Bimbingan</span>
                                                                        </button>
                                                                    </div>
                                                                @endif

                                                                @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                    <div x-show="editingFeedback" x-cloak class="p-2.5 bg-white dark:bg-slate-800 rounded-xl border border-orange-300 dark:border-orange-500/50 shadow-sm" x-transition>
                                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <input type="hidden" name="status" value="completed">
                                                                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                                                                <label class="block text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Revisi Catatan Dosen:</label>
                                                                                <span class="text-[9px] text-slate-400 font-medium">Bimbingan Selesai</span>
                                                                            </div>
                                                                            <textarea name="feedback" rows="2" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-2 leading-relaxed" placeholder="Tuliskan revisi catatan bimbingan...">{{ $session->feedback }}</textarea>
                                                                            <div class="flex items-center justify-end gap-1.5">
                                                                                <button type="button" @click="editingFeedback = false" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-black uppercase cursor-pointer">Batal</button>
                                                                                <button type="submit" class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer">Simpan Catatan</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <!-- Dokumen Link -->
                                                            @if($session->document_path)
                                                                <div class="pt-0.5">
                                                                    <a href="{{ $session->document_path }}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] text-orange-600 dark:text-orange-400 font-bold hover:underline">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                                        <span>Lihat Dokumen</span>
                                                                    </a>
                                                                </div>
                                                            @endif

                                                            <!-- Kehadiran Mahasiswa -->
                                                            @if(!in_array($session->status, ['completed', 'rejected']))
                                                                <div class="mt-2.5 p-2.5 rounded-xl border space-y-1 {{ $session->student_attendance_status === 'attending' ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800/80' : ($session->student_attendance_status === 'permission' ? 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/80' : 'bg-white dark:bg-slate-800/80 border-slate-200/80 dark:border-slate-700/80 shadow-2xs') }}">
                                                                    <div class="flex items-center justify-between gap-2">
                                                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Kehadiran:</span>
                                                                        @if($session->student_attendance_status === 'attending')
                                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[9px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-700">
                                                                                <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                                Akan Hadir
                                                                            </span>
                                                                        @elseif($session->student_attendance_status === 'permission')
                                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[9px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-700">
                                                                                <svg class="w-2.5 h-2.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"></path></svg>
                                                                                Izin
                                                                            </span>
                                                                        @else
                                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[9px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-600">
                                                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                                                Menunggu
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                    
                                                                    @if($session->student_attendance_status === 'permission' && $session->student_attendance_reason)
                                                                        <div class="mt-1 pt-1 border-t border-amber-200/60 dark:border-amber-800/40">
                                                                            <p class="text-[10px] text-amber-950 dark:text-amber-200 italic font-medium leading-relaxed">"{{ $session->student_attendance_reason }}"</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Bottom Actions & ACC Buttons -->
                                                    <div class="mt-4 pt-3 border-t border-slate-200/80 dark:border-slate-700/80 space-y-2">
                                                        <!-- Status Action Form (Pending / Approved / Absent) -->
                                                        @if($session->status === 'pending')
                                                            <div class="flex items-center space-x-2">
                                                                <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="approved">
                                                                    <button type="submit" class="w-full px-3 py-1.5 bg-orange-600 text-white hover:bg-orange-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Terima</button>
                                                                </form>
                                                                @can('update', $session)
                                                                    <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-orange-50 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all" title="Ubah Jadwal">Ubah</a>
                                                                @endcan
                                                                @can('delete', $session)
                                                                    <button type="button" 
                                                                            @click="openCancelModalFromEl($el)"
                                                                            onclick="window.openCancelModalFromEl(this)"
                                                                            data-session-id="{{ $session->id }}"
                                                                            data-student-name="{{ e($student?->name ?? 'Mahasiswa') }}"
                                                                            data-student-npm="{{ e($student?->identifier ?? '-') }}"
                                                                            data-topic="{{ e($session->topic) }}"
                                                                            data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                            data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                                            data-is-group="{{ $totalMhs > 1 ? '1' : '0' }}"
                                                                            data-group-count="{{ $totalMhs }}"
                                                                            class="px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 hover:text-rose-600 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer">Tolak</button>
                                                                @endcan
                                                            </div>
                                                        @elseif($session->status === 'approved')
                                                            <div x-data="{ showFeedback: false }">
                                                                <div class="flex items-center gap-1.5" x-show="!showFeedback">
                                                                    <button type="button" @click="showFeedback = true" class="flex-1 px-3 py-1.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Selesai</button>
                                                                    @can('update', $session)
                                                                        <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2 py-1.5 bg-white dark:bg-slate-800 hover:bg-orange-50 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all" title="Ubah Jadwal">Ubah</a>
                                                                    @endcan
                                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="absent">
                                                                        <button type="submit" class="px-2 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 hover:text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer">Absen</button>
                                                                    </form>
                                                                    @can('delete', $session)
                                                                        <button type="button" 
                                                                                @click="openCancelModalFromEl($el)"
                                                                                onclick="window.openCancelModalFromEl(this)"
                                                                                data-session-id="{{ $session->id }}"
                                                                                data-student-name="{{ e($student?->name ?? 'Mahasiswa') }}"
                                                                                data-student-npm="{{ e($student?->identifier ?? '-') }}"
                                                                                data-topic="{{ e($session->topic) }}"
                                                                                data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                                data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                                                data-is-group="{{ $totalMhs > 1 ? '1' : '0' }}"
                                                                                data-group-count="{{ $totalMhs }}"
                                                                                class="px-2 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-rose-600 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer flex items-center justify-center" 
                                                                                title="Batalkan Jadwal Bimbingan">
                                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                        </button>
                                                                    @endcan
                                                                </div>
                                                                <div x-show="showFeedback" x-cloak class="mt-2" x-transition>
                                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="completed">
                                                                        <textarea name="feedback" rows="2" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-2" placeholder="Catatan hasil bimbingan..."></textarea>
                                                                        <div class="flex space-x-2">
                                                                            <button type="submit" class="flex-1 px-3 py-1.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all cursor-pointer">Simpan</button>
                                                                            <button type="button" @click="showFeedback = false" class="px-2 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 rounded-xl text-[10px] font-black uppercase cursor-pointer">Batal</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @elseif($session->is_absent)
                                                            <div class="flex items-center gap-1.5">
                                                                <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="approved">
                                                                    <button type="submit" class="w-full px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all cursor-pointer">Batal Absen</button>
                                                                </form>
                                                                @can('update', $session)
                                                                    <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2 py-1.5 bg-white dark:bg-slate-800 hover:bg-orange-50 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Ubah</a>
                                                                @endcan
                                                            </div>
                                                        @endif

                                                        <!-- Quick ACC Buttons (Seminar & Sidang) if applicable -->
                                                        @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']) && $thesis)
                                                            <div class="flex items-center justify-between gap-1.5 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                                                                {{-- ACC UP --}}
                                                                <form action="{{ route('theses.toggle-acc', [$thesis->id, 'up']) }}" method="POST" class="flex-1"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin {{ $hasAccUp ? 'membatalkan' : 'memberikan' }} ACC Seminar untuk {{ $student?->name }}?')">
                                                                    @csrf
                                                                    @if($isAdminOrKaprodi)
                                                                        <input type="hidden" name="slot" value="all">
                                                                    @endif
                                                                    <button type="submit" 
                                                                        title="{{ $hasAccUp ? 'Batalkan ACC Seminar' : 'Berikan ACC Seminar' }}"
                                                                        class="w-full inline-flex items-center justify-center px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all
                                                                        {{ $hasAccUp ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-200' }}">
                                                                        <span>ACC SEMINAR</span>
                                                                    </button>
                                                                </form>

                                                                {{-- ACC Sidang --}}
                                                                <form action="{{ route('theses.toggle-acc', [$thesis->id, 'sidang']) }}" method="POST" class="flex-1"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin {{ $hasAccSidang ? 'membatalkan' : 'memberikan' }} ACC Sidang untuk {{ $student?->name }}?')">
                                                                    @csrf
                                                                    @if($isAdminOrKaprodi)
                                                                        <input type="hidden" name="slot" value="all">
                                                                    @endif
                                                                    <button type="submit" 
                                                                        title="{{ $hasAccSidang ? 'Batalkan ACC Sidang' : 'Berikan ACC Sidang' }}"
                                                                        class="w-full inline-flex items-center justify-center px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all
                                                                        {{ $hasAccSidang ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-200' }}">
                                                                        <span>ACC SIDANG</span>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- 2. TAMPILAN PER MAHASISWA -->
                        <div x-show="cardGrouping === 'student'" class="space-y-12">
                            @foreach($groupedSessions as $studentName => $studentSessions)
                                <div class="bg-white dark:bg-slate-800/80 rounded-3xl p-6 sm:p-7 border border-slate-200/90 dark:border-slate-700/80 shadow-xs space-y-6 transition-all hover:border-slate-300 dark:hover:border-slate-600">
                                    @php
                                        $studentThesis = $studentSessions->first()->thesis;
                                        $mentoringCount = (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') 
                                            ? $studentThesis->completed_mentoring_count 
                                            : $studentThesis->getCompletedMentoringCountForDosen(Auth::id());
                                    @endphp
                                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-5 border-b border-slate-200/80 dark:border-slate-700/80 gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-2xs bg-slate-100 dark:bg-slate-800 shrink-0">
                                                <img src="{{ $studentThesis->student?->avatar_url }}" alt="{{ $studentName }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="space-y-2 min-w-0">
                                                <div class="flex items-center gap-3 flex-wrap">
                                                    <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $studentName }}</h4>
                                                    @if($studentThesis->status === 'completed')
                                                        <span class="px-2.5 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase tracking-wider">
                                                            Lulus
                                                        </span>
                                                    @endif
                                                    <span class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-[10px] font-black uppercase tracking-wider shadow-2xs">
                                                        {{ $mentoringCount }} Bimbingan {{ (Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi') ? 'Total' : 'dengan Anda' }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-wrap items-center gap-2.5 text-[11px] font-medium">
                                                    @if($studentThesis->pembimbing1)
                                                        <span class="inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-700 text-xs shadow-2xs">
                                                            <span class="px-1.5 py-0.5 bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-black text-[9px] rounded-md border border-indigo-200 dark:border-indigo-800 shrink-0">P1</span>
                                                            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $studentThesis->pembimbing1->name }}</span>
                                                        </span>
                                                    @endif
                                                    @if($studentThesis->pembimbing2)
                                                        <span class="inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-700 text-xs shadow-2xs">
                                                            <span class="px-1.5 py-0.5 bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 font-black text-[9px] rounded-md border border-purple-200 dark:border-purple-800 shrink-0">P2</span>
                                                            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $studentThesis->pembimbing2->name }}</span>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                            <div class="flex flex-wrap gap-2">
                                                {{-- ACC UP Group --}}
                                                @php
                                                    $isAdminOrKaprodi = in_array(Auth::user()->role, ['admin', 'kaprodi']);
                                                    $isP1 = Auth::id() === $studentThesis->pembimbing1_id;
                                                    $isP2 = Auth::id() === $studentThesis->pembimbing2_id;
                                                    $hasAccUp = $isAdminOrKaprodi ? ($studentThesis->acc_up_p1 && $studentThesis->acc_up_p2) : ($isP1 ? $studentThesis->acc_up_p1 : ($isP2 ? $studentThesis->acc_up_p2 : false));
                                                @endphp
                                                <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/50 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-700">
                                                    <form action="{{ route('theses.toggle-acc', [$studentThesis->id, 'up']) }}" method="POST" class="inline"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin {{ $hasAccUp ? 'membatalkan' : 'memberikan' }} ACC Seminar untuk {{ $studentName }}?')">
                                                        @csrf
                                                        @if($isAdminOrKaprodi)
                                                            <input type="hidden" name="slot" value="all">
                                                        @endif
                                                        <button type="submit" 
                                                            title="{{ $hasAccUp ? 'Batalkan ACC Seminar' : 'Berikan ACC Seminar' }}"
                                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm
                                                            {{ $hasAccUp ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}">
                                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            ACC SEMINAR
                                                        </button>
                                                    </form>
                                                    <div class="flex gap-1 border-l border-slate-200 dark:border-slate-700 pl-2 ml-1">
                                                        <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_up_p1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P1"></div>
                                                        <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_up_p2 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P2"></div>
                                                    </div>
                                                </div>

                                                {{-- ACC Sidang Group --}}
                                                @php
                                                    $hasAccSidang = $isAdminOrKaprodi ? ($studentThesis->acc_sidang_p1 && $studentThesis->acc_sidang_p2) : ($isP1 ? $studentThesis->acc_sidang_p1 : ($isP2 ? $studentThesis->acc_sidang_p2 : false));
                                                @endphp
                                                <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/50 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-700">
                                                    <form action="{{ route('theses.toggle-acc', [$studentThesis->id, 'sidang']) }}" method="POST" class="inline"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin {{ $hasAccSidang ? 'membatalkan' : 'memberikan' }} ACC Sidang untuk {{ $studentName }}?')">
                                                        @csrf
                                                        @if($isAdminOrKaprodi)
                                                            <input type="hidden" name="slot" value="all">
                                                        @endif
                                                        <button type="submit" 
                                                            title="{{ $hasAccSidang ? 'Batalkan ACC Sidang' : 'Berikan ACC Sidang' }}"
                                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm
                                                            {{ $hasAccSidang ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}">
                                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            ACC SIDANG
                                                        </button>
                                                    </form>
                                                    <div class="flex gap-1 border-l border-slate-200 dark:border-slate-700 pl-2 ml-1">
                                                        <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_sidang_p1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P1"></div>
                                                        <div class="w-2 h-2 rounded-full {{ $studentThesis->acc_sidang_p2 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300 dark:bg-slate-700' }}" title="Status ACC P2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                                            <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-900/60 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-700">
                                                Dosen Pembimbing: <span class="text-slate-700 dark:text-slate-300 font-bold">{{ $studentSessions->first()->dosen->name }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        @foreach($studentSessions as $session)
                                            @php
                                                $gKey = ($session->dosen_id ?? '0') . '_' . $session->scheduled_at->format('Y-m-d H:i');
                                                $isGroupSession = ($groupCountMap[$gKey] ?? 1) > 1;
                                            @endphp
                                            <div class="bg-slate-50/60 dark:bg-slate-900/80 border border-slate-200/90 dark:border-slate-700/80 rounded-2xl p-5 sm:p-6 relative overflow-hidden group hover:shadow-lg hover:shadow-slate-200/40 dark:hover:shadow-none hover:border-orange-300 dark:hover:border-orange-500/40 transition-all flex flex-col justify-between">
                                                <div>
                                                    <!-- Status Indicator -->
                                                    <div class="absolute top-0 left-0 w-full h-1.5 
                                                        {{ $session->status === 'pending' ? 'bg-amber-400' : '' }}
                                                        {{ $session->status === 'approved' ? 'bg-orange-600' : '' }}
                                                        {{ $session->status === 'rejected' || $session->is_absent ? 'bg-red-500' : '' }}
                                                        {{ $session->status === 'completed' && !$session->is_absent ? 'bg-slate-300 dark:bg-slate-700' : '' }}
                                                    "></div>
                                                    
                                                    <div class="flex justify-between items-start gap-3 mb-4 pt-1">
                                                        <div class="space-y-0.5">
                                                            <p class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-wider">{{ $session->scheduled_at->locale('id')->translatedFormat('d M Y') }}</p>
                                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase">{{ $session->scheduled_at->format('H:i') }} WIB</p>
                                                        </div>
                                                        <div class="shrink-0">
                                                            @if($session->is_absent)
                                                                <x-status-badge type="red" label="TIDAK HADIR" />
                                                            @else
                                                                <x-status-badge 
                                                                    :type="$session->status === 'pending' ? 'amber' : ($session->status === 'approved' ? 'orange' : ($session->status === 'rejected' ? 'red' : ($session->status === 'completed' ? 'emerald' : 'slate')))" 
                                                                    :label="$session->status === 'completed' ? 'HADIR' : strtoupper($session->status)" />
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-4 space-y-3">
                                                        <h5 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-tight leading-snug">
                                                            {{ $session->topic }}
                                                        </h5>
                                                        
                                                        <div class="flex items-center gap-2 flex-wrap text-[10px] font-bold">
                                                            @if($session->type === 'online')
                                                                @php 
                                                                    $isMeet = Str::contains($session->location ?? '', 'meet.google.com'); 
                                                                    $isZoom = Str::contains($session->location ?? '', ['zoom.us', 'zoom.com']);
                                                                    $linkUrl = Str::startsWith($session->location ?? '', 'http') ? $session->location : 'https://' . $session->location;
                                                                @endphp
                                                                @if($session->location)
                                                                    <a href="{{ $linkUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $isMeet ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/60' : ($isZoom ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/60' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100') }} transition-all shadow-2xs font-bold text-[10px] uppercase tracking-wider">
                                                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                        <span>{{ $isMeet ? 'Buka Meet' : ($isZoom ? 'Buka Zoom' : 'Buka Link') }}</span>
                                                                    </a>
                                                                @else
                                                                    <span class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 px-2 py-0.5 rounded border border-indigo-100 dark:border-indigo-800 uppercase tracking-wider">
                                                                        🎥 Daring
                                                                    </span>
                                                                @endif
                                                            @else
                                                                <span class="inline-flex items-center gap-1 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 uppercase tracking-wider shadow-2xs">
                                                                     🏢 {{ $session->location ?? 'Offline' }}
                                                                </span>
                                                            @endif

                                                            @if($isGroupSession)
                                                                <span class="inline-flex items-center gap-1 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/40 px-2.5 py-1 rounded-lg border border-indigo-200 dark:border-indigo-800/80 uppercase tracking-wider shadow-2xs" title="Sesi Bimbingan Bersama">
                                                                    👥 Kelompok ({{ $groupCountMap[$gKey] }})
                                                                </span>
                                                            @endif
                                                        </div>
                                                        
                                                        @if($session->notes)
                                                            <div class="mt-3.5 p-3.5 bg-white dark:bg-slate-800/80 rounded-xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                                                                <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Catatan Mahasiswa</p>
                                                                <p class="text-xs text-slate-600 dark:text-slate-300 italic leading-relaxed">"{{ $session->notes }}"</p>
                                                            </div>
                                                        @endif

                                                        <!-- Catatan Dosen (dengan mode revisi) -->
                                                        <div x-data="{ editingFeedback: false }" class="mt-3.5">
                                                            @if($session->feedback)
                                                                <div x-show="!editingFeedback" class="p-3.5 bg-orange-50/50 dark:bg-orange-950/20 rounded-xl border border-orange-200/70 dark:border-orange-900/40 shadow-2xs group relative">
                                                                    <div class="flex items-center justify-between gap-2 mb-1">
                                                                        <p class="text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest flex items-center gap-1">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                                                            <span>Catatan Dosen</span>
                                                                        </p>
                                                                        @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                            <button type="button" 
                                                                                    @click="editingFeedback = true" 
                                                                                    title="Ubah catatan bimbingan" 
                                                                                    class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 hover:bg-orange-100/60 dark:hover:bg-orange-900/40 transition-all cursor-pointer">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                                <span>Ubah</span>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                    <p class="text-xs text-slate-700 dark:text-slate-300 font-medium leading-relaxed whitespace-pre-line">{{ $session->feedback }}</p>
                                                                </div>
                                                            @elseif($session->status === 'completed' && !$session->is_absent && in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                <div x-show="!editingFeedback" class="p-2.5 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-center">
                                                                    <button type="button" 
                                                                            @click="editingFeedback = true" 
                                                                            class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                                        <span>+ Tambah Catatan Hasil Bimbingan</span>
                                                                    </button>
                                                                </div>
                                                            @endif

                                                            @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                                                <div x-show="editingFeedback" x-cloak class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-orange-300 dark:border-orange-500/50 shadow-sm" x-transition>
                                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="completed">
                                                                        <div class="flex items-center justify-between gap-2 mb-1.5">
                                                                            <label class="block text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Revisi Catatan Dosen:</label>
                                                                            <span class="text-[9px] text-slate-400 font-medium">Bimbingan Selesai</span>
                                                                        </div>
                                                                        <textarea name="feedback" rows="3" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-2 leading-relaxed" placeholder="Tuliskan revisi catatan hasil bimbingan...">{{ $session->feedback }}</textarea>
                                                                        <div class="flex items-center justify-end gap-1.5">
                                                                            <button type="button" @click="editingFeedback = false" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-black uppercase cursor-pointer">Batal</button>
                                                                            <button type="submit" class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer">Simpan Catatan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @if($session->document_path)
                                                            <div class="mt-3 pt-1">
                                                                <a href="{{ $session->document_path }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-orange-600 dark:text-orange-400 font-bold hover:underline">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                                    <span>Lihat Dokumen Mahasiswa</span>
                                                                </a>
                                                            </div>
                                                        @endif

                                                        <!-- Konfirmasi Kehadiran Mahasiswa -->
                                                        @if(!in_array($session->status, ['completed', 'rejected']))
                                                            <div class="mt-3.5 p-3 rounded-xl border space-y-1.5 {{ $session->student_attendance_status === 'attending' ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800/80' : ($session->student_attendance_status === 'permission' ? 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/80' : 'bg-white dark:bg-slate-800/80 border-slate-200/80 dark:border-slate-700/80 shadow-2xs') }}">
                                                                <div class="flex items-center justify-between gap-2">
                                                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Kehadiran Mahasiswa:</span>
                                                                    @if($session->student_attendance_status === 'attending')
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[9px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-700">
                                                                            <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                            Akan Hadir
                                                                        </span>
                                                                    @elseif($session->student_attendance_status === 'permission')
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[9px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-700">
                                                                            <svg class="w-2.5 h-2.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"></path></svg>
                                                                            Izin / Berhalangan
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[9px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-600">
                                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                                            Menunggu Konfirmasi
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                
                                                                @if($session->student_attendance_status === 'permission' && $session->student_attendance_reason)
                                                                    <div class="mt-1 pt-1.5 border-t border-amber-200/60 dark:border-amber-800/40">
                                                                        <span class="text-[9px] font-bold text-amber-800 dark:text-amber-300">Alasan Izin:</span>
                                                                        <p class="text-[11px] text-amber-950 dark:text-amber-200 italic font-medium leading-relaxed">"{{ $session->student_attendance_reason }}"</p>
                                                                        @if($session->student_confirmed_at)
                                                                            <p class="text-[9px] text-amber-700/80 dark:text-amber-400/80 text-right mt-1 font-medium">{{ $session->student_confirmed_at->locale('id')->translatedFormat('d M H:i') }} WIB</p>
                                                                        @endif
                                                                    </div>
                                                                @elseif($session->student_attendance_status === 'attending' && $session->student_confirmed_at)
                                                                    <p class="text-[9px] text-emerald-700/80 dark:text-emerald-400/80 text-right font-medium">Dikonfirmasi: {{ $session->student_confirmed_at->locale('id')->translatedFormat('d M H:i') }} WIB</p>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                @if($session->status === 'pending')
                                                <div class="flex items-center space-x-2 mt-5 pt-4 border-t border-slate-200/70 dark:border-slate-800">
                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="w-full px-3 py-2 bg-orange-600 text-white hover:bg-orange-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Terima</button>
                                                    </form>
                                                    @can('update', $session)
                                                        <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2.5 py-2 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 dark:border-orange-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs inline-flex items-center gap-1" title="Ubah / Reschedule Jadwal">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                            <span>Ubah</span>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $session)
                                                        <button type="button" 
                                                                @click="openCancelModalFromEl($el)"
                                                                onclick="window.openCancelModalFromEl(this)"
                                                                data-session-id="{{ $session->id }}"
                                                                data-student-name="{{ e($session->thesis?->student?->name ?? 'Mahasiswa') }}"
                                                                data-student-npm="{{ e($session->thesis?->student?->identifier ?? '-') }}"
                                                                data-topic="{{ e($session->topic) }}"
                                                                data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                                data-is-group="{{ $isGroupSession ? '1' : '0' }}"
                                                                data-group-count="{{ $groupCountMap[$gKey] ?? 1 }}"
                                                                class="flex-1 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-600 hover:border-rose-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">
                                                            Tolak / Batal
                                                        </button>
                                                    @endcan
                                                </div>
                                                @elseif($session->status === 'approved')
                                                <div class="mt-auto pt-5 border-t border-slate-200/70 dark:border-slate-800" x-data="{ showFeedback: false }">
                                                    <div class="flex items-center gap-1.5" x-show="!showFeedback">
                                                        <button type="button" @click="showFeedback = true" class="flex-1 px-3 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Selesai</button>
                                                        @can('update', $session)
                                                            <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2.5 py-2 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 dark:border-orange-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs inline-flex items-center gap-1" title="Ubah / Reschedule Jadwal">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                            <span>Ubah</span>
                                                        </a>
                                                        @endcan
                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="absent">
                                                            <button type="submit" class="px-2.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 hover:border-red-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Absen</button>
                                                        </form>
                                                        @can('delete', $session)
                                                            <button type="button" 
                                                                    @click="openCancelModalFromEl($el)"
                                                                    onclick="window.openCancelModalFromEl(this)"
                                                                    data-session-id="{{ $session->id }}"
                                                                    data-student-name="{{ e($session->thesis?->student?->name ?? 'Mahasiswa') }}"
                                                                    data-student-npm="{{ e($session->thesis?->student?->identifier ?? '-') }}"
                                                                    data-topic="{{ e($session->topic) }}"
                                                                    data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                    data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                                    data-is-group="{{ $isGroupSession ? '1' : '0' }}"
                                                                    data-group-count="{{ $groupCountMap[$gKey] ?? 1 }}"
                                                                    class="px-2.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:border-rose-300 dark:border-rose-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs cursor-pointer flex items-center justify-center" 
                                                                    title="Batalkan Jadwal Bimbingan">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                    <div x-show="showFeedback" x-cloak class="mt-3" x-transition>
                                                        <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="completed">
                                                            <textarea name="feedback" rows="3" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-orange-500 focus:border-orange-500 mb-3" placeholder="Catatan hasil bimbingan..."></textarea>
                                                            <div class="flex space-x-2">
                                                                <button type="submit" class="flex-1 px-3 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer">Simpan</button>
                                                                <button type="button" @click="showFeedback = false" class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer">Batal</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                @elseif($session->is_absent)
                                                <div class="mt-auto pt-5 border-t border-slate-200/70 dark:border-slate-800 flex items-center gap-1.5">
                                                    <form action="{{ route('mentoring-sessions.status', $session->id) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" 
                                                                class="w-full px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm cursor-pointer flex items-center justify-center gap-1"
                                                                title="Batalkan status tidak hadir dan kembalikan ke jadwal aktif">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                            <span>Batalkan Tidak Hadir</span>
                                                        </button>
                                                    </form>
                                                    @can('update', $session)
                                                        <a href="{{ route('mentoring-sessions.edit', $session->id) }}" class="px-2.5 py-2 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-950/40 text-slate-700 dark:text-slate-200 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 hover:border-orange-200 dark:border-orange-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs inline-flex items-center gap-1" title="Ubah / Reschedule Jadwal">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                            <span>Ubah</span>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $session)
                                                        <button type="button" 
                                                                @click="openCancelModalFromEl($el)"
                                                                onclick="window.openCancelModalFromEl(this)"
                                                                data-session-id="{{ $session->id }}"
                                                                data-student-name="{{ e($session->thesis?->student?->name ?? 'Mahasiswa') }}"
                                                                data-student-npm="{{ e($session->thesis?->student?->identifier ?? '-') }}"
                                                                data-topic="{{ e($session->topic) }}"
                                                                data-scheduled-date="{{ $session->scheduled_at->locale('id')->translatedFormat('l, d F Y') }}"
                                                                data-scheduled-time="{{ $session->scheduled_at->format('H:i') }} WIB"
                                                                data-is-group="{{ $isGroupSession ? '1' : '0' }}"
                                                                data-group-count="{{ $groupCountMap[$gKey] ?? 1 }}"
                                                                class="px-2.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:border-rose-300 dark:border-rose-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xs cursor-pointer flex items-center justify-center" 
                                                                title="Batalkan / Hapus Sesi Bimbingan">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    @endcan
                                                </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- 2. CALENDAR VIEW -->
                <div x-show="viewMode === 'calendar'" x-cloak x-transition class="space-y-4">
                    <!-- Legend Indicators -->
                    <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <div class="flex flex-wrap items-center gap-4 text-xs font-bold">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></span>
                                <span class="text-slate-600 dark:text-slate-400 text-[11px]">Selesai / Hadir</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-600 shadow-[0_0_8px_rgba(234,88,12,0.4)]"></span>
                                <span class="text-slate-600 dark:text-slate-400 text-[11px]">Dijadwalkan (Aktif)</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.4)]"></span>
                                <span class="text-slate-600 dark:text-slate-400 text-[11px]">Menunggu Konfirmasi</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.4)]"></span>
                                <span class="text-slate-600 dark:text-slate-400 text-[11px]">Ditolak / Absen</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">💡 Klik pada jadwal untuk melihat rincian & aksi cepat</span>
                    </div>

                    <!-- Calendar Container -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-700 min-h-[600px]">
                        <div id="mentoring-calendar"></div>
                    </div>
                </div>
            </div>
        </x-table-card>

        <!-- Interactive Event Modal -->
        <template x-teleport="body">
            <div x-data="mentoringEventModal()"
                 @open-event-modal.window="openModal($event.detail)"
                 x-show="eventModalOpen" 
                 x-cloak 
                 class="fixed inset-0 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
                 style="z-index: 99999 !important;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 max-w-lg w-full overflow-hidden relative"
                     style="z-index: 100000 !important;"
                     @click.outside="eventModalOpen = false">
                
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-2xs bg-orange-50 shrink-0">
                            <template x-if="selectedEvent?.student_avatar">
                                <img :src="selectedEvent.student_avatar" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!selectedEvent?.student_avatar">
                                <span class="font-black text-xs text-orange-600" x-text="selectedEvent?.student_name?.charAt(0) || 'M'"></span>
                            </template>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase" x-text="selectedEvent?.student_name"></h4>
                            <p class="text-[10px] text-slate-400 font-bold" x-text="'NPM: ' + (selectedEvent?.student_npm || '-')"></p>
                        </div>
                    </div>
                    <button type="button" @click="eventModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4 text-xs">
                    <!-- Date & Time Banner -->
                    <div class="flex items-center justify-between p-3.5 bg-orange-50/60 dark:bg-orange-950/20 border border-orange-200/60 dark:border-orange-900/40 rounded-xl">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <div>
                                <p class="font-black text-slate-800 dark:text-slate-200" x-text="selectedEvent?.date"></p>
                                <p class="text-[10px] text-orange-600 dark:text-orange-400 font-bold" x-text="selectedEvent?.time"></p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider"
                              :class="{
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300': selectedEvent?.status === 'completed',
                                'bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-300': selectedEvent?.status === 'approved',
                                'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300': selectedEvent?.status === 'pending',
                                'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300': selectedEvent?.status === 'rejected' || selectedEvent?.is_absent,
                              }"
                              x-text="selectedEvent?.is_absent ? 'TIDAK HADIR' : (selectedEvent?.status === 'completed' ? 'HADIR / SELESAI' : selectedEvent?.status)">
                        </span>
                    </div>

                    <!-- Topic -->
                    <div class="space-y-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Topik Pembahasan:</span>
                        <p class="font-black text-sm text-slate-800 dark:text-slate-100" x-text="selectedEvent?.topic"></p>
                    </div>

                    <!-- Location / GMeet Link -->
                    <div class="space-y-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Metode & Lokasi:</span>
                        <div>
                            <template x-if="selectedEvent?.type === 'online' && selectedEvent?.location">
                                <a :href="selectedEvent.location.startsWith('http') ? selectedEvent.location : 'https://' + selectedEvent.location" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl font-bold hover:bg-emerald-100 transition-all text-xs">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>🎥 Buka Link Google Meet / Meeting</span>
                                </a>
                            </template>
                            <template x-if="selectedEvent?.type === 'offline' || !selectedEvent?.location">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">
                                    <span>🏢 Tatap Muka Langsung</span>
                                    <span x-text="selectedEvent?.location ? '(' + selectedEvent.location + ')' : ''"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Student Attendance Status in Calendar Modal -->
                    <template x-if="selectedEvent?.student_attendance_status && selectedEvent?.status !== 'completed'">
                        <div class="p-3.5 rounded-xl border space-y-1.5"
                             :class="{
                                 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800': selectedEvent.student_attendance_status === 'attending',
                                 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800': selectedEvent.student_attendance_status === 'permission',
                                 'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700': selectedEvent.student_attendance_status === 'pending'
                             }">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Konfirmasi Kehadiran Mahasiswa:</span>
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider"
                                      :class="{
                                          'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200': selectedEvent.student_attendance_status === 'attending',
                                          'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200': selectedEvent.student_attendance_status === 'permission',
                                          'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300': selectedEvent.student_attendance_status === 'pending'
                                      }"
                                      x-text="selectedEvent.student_attendance_status === 'attending' ? '🟢 Akan Hadir' : (selectedEvent.student_attendance_status === 'permission' ? '🟡 Izin / Berhalangan' : '⚪ Menunggu Konfirmasi')">
                                </span>
                            </div>
                            <template x-if="selectedEvent.student_attendance_status === 'permission' && selectedEvent.student_attendance_reason">
                                <div class="mt-1">
                                    <span class="text-[9px] font-bold text-amber-800 dark:text-amber-300">Alasan Izin:</span>
                                    <p class="text-[11px] text-amber-950 dark:text-amber-200 italic font-medium leading-relaxed" x-text="'&ldquo;' + selectedEvent.student_attendance_reason + '&rdquo;'"></p>
                                </div>
                            </template>
                            <template x-if="selectedEvent.student_confirmed_at">
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 text-right font-medium" x-text="'Dikonfirmasi: ' + selectedEvent.student_confirmed_at"></p>
                            </template>
                        </div>
                    </template>

                    <!-- Student Notes -->
                    <template x-if="selectedEvent?.notes">
                        <div class="space-y-1 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Catatan Pengajuan Mahasiswa:</span>
                            <p class="text-slate-600 dark:text-slate-300 italic" x-text="'&ldquo;' + selectedEvent.notes + '&rdquo;'"></p>
                        </div>
                    </template>

                    <!-- Lecturer Feedback (dengan mode revisi) -->
                    <template x-if="selectedEvent?.status === 'completed' || selectedEvent?.feedback">
                        <div class="space-y-1.5 p-3.5 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-xl border border-emerald-200/80 dark:border-emerald-800">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[9px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                    <span>Hasil / Catatan Bimbingan Dosen:</span>
                                </span>
                                @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                    <button type="button" 
                                            @click="editingFeedback = !editingFeedback; feedbackText = selectedEvent?.feedback || ''" 
                                            class="text-[9px] font-bold text-emerald-700 dark:text-emerald-400 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        <span x-text="editingFeedback ? 'Batal' : (selectedEvent?.feedback ? 'Ubah' : '+ Tulis Catatan')"></span>
                                    </button>
                                @endif
                            </div>

                            <div x-show="!editingFeedback">
                                <p class="text-emerald-900 dark:text-emerald-200 font-medium leading-relaxed whitespace-pre-line" x-text="selectedEvent?.feedback ? '&ldquo;' + selectedEvent.feedback + '&rdquo;' : 'Belum ada catatan hasil bimbingan.'"></p>
                            </div>

                            @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                                <div x-show="editingFeedback" x-cloak class="mt-2 pt-2 border-t border-emerald-200 dark:border-emerald-800" x-transition>
                                    <form :action="'/mentoring-sessions/' + selectedEvent?.id + '/status'" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <textarea name="feedback" x-model="feedbackText" rows="2" required class="block w-full rounded-xl border-emerald-300 dark:border-emerald-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs focus:ring-emerald-500 focus:border-emerald-500 mb-2 leading-relaxed" placeholder="Tuliskan catatan hasil bimbingan..."></textarea>
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" @click="editingFeedback = false" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-black uppercase cursor-pointer">Batal</button>
                                            <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer">Simpan Catatan</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 px-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <template x-if="((selectedEvent?.status !== 'completed') || selectedEvent?.is_absent) && selectedEvent?.id">
                            <a :href="'/mentoring-sessions/' + selectedEvent.id + '/edit'" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/40 dark:hover:bg-orange-900/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/80 rounded-xl font-bold text-xs transition-all shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                <span>Ubah / Reschedule</span>
                            </a>
                        </template>
                        <template x-if="((selectedEvent?.status !== 'completed') || selectedEvent?.is_absent) && selectedEvent?.id">
                            <button type="button" 
                                    @click="eventModalOpen = false; openCancelModal({
                                        id: selectedEvent.id,
                                        student_name: selectedEvent.student_name || 'Mahasiswa',
                                        student_npm: selectedEvent.student_identifier || '-',
                                        topic: selectedEvent.topic,
                                        scheduled_date: selectedEvent.start_formatted || '-',
                                        scheduled_time: selectedEvent.time_formatted || '-',
                                        is_group: false,
                                        group_count: 1,
                                    })"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/80 rounded-xl font-bold text-xs transition-all shadow-2xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <span>Batalkan</span>
                            </button>
                        </template>
                    </div>
                    <button type="button" @click="eventModalOpen = false" class="px-5 py-2 bg-slate-800 dark:bg-white text-white dark:text-slate-800 font-bold rounded-xl text-xs hover:bg-slate-900 dark:hover:bg-slate-100 transition-all shadow-xs cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
        </template>

        <!-- 3. LIVE REAL-TIME ATTENDANCE MONITOR MODAL -->
        <template x-teleport="body">
            <div x-data="mentoringLiveAttendance()" 
                 @open-live-modal.window="openModal()" 
                 x-show="liveModalOpen" 
                 x-cloak 
                 class="fixed inset-0 overflow-y-auto" 
                 style="z-index: 99999 !important;"
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Backdrop -->
                    <div x-show="liveModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                         @click="liveModalOpen = false" 
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!-- Modal Content -->
                    <div x-show="liveModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-slate-200 dark:border-slate-700 relative"
                         style="z-index: 100000 !important;">
                    
                    <!-- Modal Header -->
                    <div class="p-6 bg-slate-50/80 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-sm shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 tracking-tight">Monitor Kehadiran Mahasiswa</h3>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold border border-emerald-200/60 dark:border-emerald-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Real-Time Sync
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pantau status konfirmasi kehadiran mahasiswa untuk seluruh sesi bimbingan aktif.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="fetchLiveAttendance(false)" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-2xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" :class="isSyncing ? 'animate-spin text-orange-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Segarkan</span>
                            </button>
                            <button type="button" @click="liveModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                        <!-- KPI Status Quick Metrics -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Akan Hadir -->
                            <button type="button" 
                                    @click="liveTab = 'attending'" 
                                    :class="liveTab === 'attending' 
                                        ? 'ring-2 ring-emerald-500 border-emerald-300 dark:border-emerald-700 bg-emerald-50/90 dark:bg-emerald-950/50 shadow-xs' 
                                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/20 hover:border-emerald-200 dark:hover:border-emerald-800'"
                                    class="p-4 rounded-2xl border text-left transition-all cursor-pointer relative overflow-hidden group">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-100/80 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase tracking-wider border border-emerald-200/60 dark:border-emerald-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Akan Hadir
                                    </span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></span>
                                </div>
                                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-2.5" x-text="attendanceStats.attending || 0">0</div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Mahasiswa siap hadir</p>
                            </button>

                            <!-- Izin / Berhalangan -->
                            <button type="button" 
                                    @click="liveTab = 'permission'" 
                                    :class="liveTab === 'permission' 
                                        ? 'ring-2 ring-amber-500 border-amber-300 dark:border-amber-700 bg-amber-50/90 dark:bg-amber-950/50 shadow-xs' 
                                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 hover:bg-amber-50/40 dark:hover:bg-amber-950/20 hover:border-amber-200 dark:hover:border-amber-800'"
                                    class="p-4 rounded-2xl border text-left transition-all cursor-pointer relative overflow-hidden group">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-100/80 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 text-[10px] font-black uppercase tracking-wider border border-amber-200/60 dark:border-amber-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Izin / Berhalangan
                                    </span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.6)]"></span>
                                </div>
                                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-2.5" x-text="attendanceStats.permission || 0">0</div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Dengan alasan izin</p>
                            </button>

                            <!-- Belum Konfirmasi -->
                            <button type="button" 
                                    @click="liveTab = 'pending'" 
                                    :class="liveTab === 'pending' 
                                        ? 'ring-2 ring-slate-500 border-slate-400 dark:border-slate-500 bg-slate-100 dark:bg-slate-800/80 shadow-xs' 
                                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-800/40'"
                                    class="p-4 rounded-2xl border text-left transition-all cursor-pointer relative overflow-hidden group">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                                        Belum Konfirmasi
                                    </span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                </div>
                                <div class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-2.5" x-text="attendanceStats.pending || 0">0</div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Menunggu respon mahasiswa</p>
                            </button>
                        </div>

                        <!-- Filter Tabs, View Switcher & Search in Modal -->
                        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3 pt-1">
                            <!-- Status Tabs -->
                            <div class="flex items-center gap-1.5 overflow-x-auto p-1 bg-slate-100 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                                <button type="button" 
                                        @click="liveTab = 'all'" 
                                        :class="liveTab === 'all' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-2xs font-black' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-slate-800/60 font-bold'"
                                        class="px-3 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap cursor-pointer">
                                    Semua (<span x-text="attendanceStats.total || 0"></span>)
                                </button>
                                <button type="button" 
                                        @click="liveTab = 'attending'" 
                                        :class="liveTab === 'attending' ? 'bg-emerald-600 text-white shadow-2xs font-black' : 'text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 font-bold'"
                                        class="px-3 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap cursor-pointer">
                                    Akan Hadir (<span x-text="attendanceStats.attending || 0"></span>)
                                </button>
                                <button type="button" 
                                        @click="liveTab = 'permission'" 
                                        :class="liveTab === 'permission' ? 'bg-amber-600 text-white shadow-2xs font-black' : 'text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 font-bold'"
                                        class="px-3 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap cursor-pointer">
                                    Izin (<span x-text="attendanceStats.permission || 0"></span>)
                                </button>
                                <button type="button" 
                                        @click="liveTab = 'pending'" 
                                        :class="liveTab === 'pending' ? 'bg-slate-800 dark:bg-slate-700 text-white shadow-2xs font-black' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/80 dark:hover:bg-slate-800/60 font-bold'"
                                        class="px-3 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap cursor-pointer">
                                    Belum Konfirmasi (<span x-text="attendanceStats.pending || 0"></span>)
                                </button>
                            </div>

                            <!-- Right Controls: View Mode Switcher & Search -->
                            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                                <!-- Mode Tampilan: Per Sesi (Default) vs Semua Mahasiswa -->
                                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shrink-0">
                                    <button type="button" 
                                            @click="liveViewMode = 'session'" 
                                            :class="liveViewMode === 'session' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-bold'"
                                            title="Kelompokkan berdasarkan sesi bimbingan"
                                            class="px-2.5 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <span>Per Sesi</span>
                                    </button>
                                    <button type="button" 
                                            @click="liveViewMode = 'flat'" 
                                            :class="liveViewMode === 'flat' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-bold'"
                                            title="Tampilkan seluruh daftar mahasiswa"
                                            class="px-2.5 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                        <span>Semua Mhs</span>
                                    </button>
                                </div>

                                <div class="w-full sm:w-56">
                                    <input type="text" 
                                           x-model="liveSearch" 
                                           placeholder="Cari mhs / topik / tgl..." 
                                           class="w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-1.5 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                        </div>

                        <!-- 1. TAMPILAN PER SESI BIMBINGAN (DEFAULT) -->
                        <div x-show="liveViewMode === 'session'" class="space-y-4">
                            <template x-for="session in groupedLiveSessions" :key="session.key">
                                <div class="bg-slate-50/70 dark:bg-slate-900/60 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 overflow-hidden shadow-2xs transition-all hover:border-slate-300 dark:hover:border-slate-600">
                                    <!-- Session Header Banner -->
                                    <div class="p-4 sm:px-5 bg-white dark:bg-slate-800/90 border-b border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="space-y-1.5 min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-orange-50 dark:bg-orange-950/70 text-orange-700 dark:text-orange-300 text-xs font-black border border-orange-200 dark:border-orange-800/70 shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span x-text="session.scheduled_date_formatted + ' • ' + session.scheduled_time_formatted"></span>
                                                </span>

                                                <template x-if="session.is_today">
                                                    <span class="px-2 py-0.5 rounded-md bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-300 text-[10px] font-black uppercase tracking-wider border border-red-200 dark:border-red-800/80 animate-pulse">
                                                        Hari Ini
                                                    </span>
                                                </template>

                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                                    <span x-text="session.type === 'online' ? '🎥 Daring' : '🏢 Tatap Muka'"></span>
                                                    <template x-if="session.location">
                                                        <span class="text-slate-500 dark:text-slate-400" x-text="'(' + session.location + ')'"></span>
                                                    </template>
                                                </span>

                                                <template x-if="session.dosen_name">
                                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium" x-text="'• Dosen: ' + session.dosen_name"></span>
                                                </template>
                                            </div>

                                            <!-- Session Topic -->
                                            <div class="flex items-baseline gap-2 pt-0.5">
                                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 shrink-0">Topik Sesi:</span>
                                                <h4 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-1" x-text="session.topic"></h4>
                                            </div>
                                        </div>

                                        <!-- Session Quick Metrics -->
                                        <div class="flex items-center gap-1.5 shrink-0 flex-wrap">
                                            <span class="text-[11px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mr-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700" x-text="session.students.length + ' Mahasiswa'"></span>
                                            <template x-if="session.stats.attending > 0">
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-1">
                                                    <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    <span x-text="session.stats.attending + ' Hadir'"></span>
                                                </span>
                                            </template>
                                            <template x-if="session.stats.permission > 0">
                                                <span class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200 text-[10px] font-bold border border-amber-200 dark:border-amber-800/60 flex items-center gap-1">
                                                    <svg class="w-2.5 h-2.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01"></path></svg>
                                                    <span x-text="session.stats.permission + ' Izin'"></span>
                                                </span>
                                            </template>
                                            <template x-if="session.stats.pending > 0">
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold border border-slate-200 dark:border-slate-700 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                    <span x-text="session.stats.pending + ' Belum Respon'"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Student Rows Inside Session -->
                                    <div class="p-3 sm:p-4 space-y-2.5 bg-white/70 dark:bg-slate-900/40">
                                        <template x-for="item in session.students" :key="item.id">
                                            <div class="p-3.5 rounded-xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-800/80"
                                                 :class="{
                                                     'border-emerald-200 dark:border-emerald-700/60 bg-emerald-50/20 dark:bg-emerald-950/20': item.attendance_status === 'attending',
                                                     'border-amber-200 dark:border-amber-700/60 bg-amber-50/20 dark:bg-amber-950/20': item.attendance_status === 'permission',
                                                     'border-slate-200 dark:border-slate-700': item.attendance_status === 'pending'
                                                 }">
                                                
                                                <div class="flex items-start gap-3 min-w-0 flex-1">
                                                    <template x-if="item.student_avatar">
                                                        <img :src="item.student_avatar" class="w-9 h-9 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                                    </template>
                                                    <template x-if="!item.student_avatar">
                                                        <div class="w-9 h-9 rounded-xl text-white flex items-center justify-center font-bold text-xs shrink-0"
                                                             :class="{
                                                                 'bg-emerald-600': item.attendance_status === 'attending',
                                                                 'bg-amber-600': item.attendance_status === 'permission',
                                                                 'bg-slate-700': item.attendance_status === 'pending'
                                                             }"
                                                             x-text="getInitials(item.student_name)">
                                                        </div>
                                                    </template>
                                                    
                                                    <div class="min-w-0 flex-1 space-y-0.5">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <h5 class="text-xs font-black text-slate-900 dark:text-white" x-text="item.student_name"></h5>
                                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-[10px] font-bold border border-slate-200 dark:border-slate-700" x-text="item.student_identifier"></span>
                                                        </div>
                                                        <template x-if="item.thesis_title && item.thesis_title !== '-'">
                                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate" x-text="'Skripsi: ' + item.thesis_title"></p>
                                                        </template>

                                                        <!-- Permission Reason Callout -->
                                                        <template x-if="item.attendance_status === 'permission' && item.attendance_reason">
                                                            <div class="mt-1.5 p-2 bg-amber-100/70 dark:bg-amber-950/60 rounded-lg border border-amber-200 dark:border-amber-700/80">
                                                                <span class="text-[9px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider block">Alasan Izin:</span>
                                                                <p class="text-xs text-amber-950 dark:text-amber-100 font-medium italic mt-0.5" x-text="'&ldquo;' + item.attendance_reason + '&rdquo;'"></p>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                <!-- Status Badge & WhatsApp Action -->
                                                <div class="flex sm:flex-col items-end justify-between sm:justify-center gap-1.5 shrink-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-200 dark:border-slate-700">
                                                    <template x-if="item.attendance_status === 'attending'">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[10px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-700/80">
                                                            <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            <span>Akan Hadir</span>
                                                        </span>
                                                    </template>
                                                    <template x-if="item.attendance_status === 'permission'">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[10px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-700/80">
                                                            <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                            <span>Izin / Berhalangan</span>
                                                        </span>
                                                    </template>
                                                    <template x-if="item.attendance_status === 'pending'">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-[10px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-600">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                                                            <span>Belum Respon</span>
                                                        </span>
                                                    </template>

                                                    <template x-if="item.confirmed_at_formatted">
                                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium" x-text="item.confirmed_at_formatted"></span>
                                                    </template>

                                                    <!-- WhatsApp Reminder Button for Pending / Permission -->
                                                    <template x-if="item.student_phone && (item.attendance_status === 'pending' || item.attendance_status === 'permission')">
                                                        <a :href="getWaLink(item)" 
                                                           target="_blank" 
                                                           class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/80 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/80 rounded-lg text-[10px] font-bold transition-all shadow-2xs">
                                                            <span>📲 Chat WA</span>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="groupedLiveSessions.length === 0">
                                <div class="p-8 text-center bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-200 dark:border-slate-700">
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Tidak ada sesi bimbingan atau mahasiswa yang sesuai dengan filter kehadiran ini.</p>
                                </div>
                            </template>
                        </div>

                        <!-- 2. TAMPILAN SEMUA MAHASISWA (FLAT LIST ALTERNATIF) -->
                        <div x-show="liveViewMode === 'flat'" class="space-y-2.5">
                            <template x-for="item in filteredLiveSessions" :key="item.id">
                                <div class="p-4 rounded-2xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                     :class="{
                                         'bg-emerald-50/40 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-700/80': item.attendance_status === 'attending',
                                         'bg-amber-50/40 dark:bg-amber-950/30 border-amber-200 dark:border-amber-700/80': item.attendance_status === 'permission',
                                         'bg-white dark:bg-slate-900/80 border-slate-200 dark:border-slate-700': item.attendance_status === 'pending'
                                     }">
                                    
                                    <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                        <template x-if="item.student_avatar">
                                            <img :src="item.student_avatar" class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                        </template>
                                        <template x-if="!item.student_avatar">
                                            <div class="w-10 h-10 rounded-xl bg-slate-800 text-white flex items-center justify-center font-bold text-xs shrink-0"
                                                 :class="{
                                                     'bg-emerald-600': item.attendance_status === 'attending',
                                                     'bg-amber-600': item.attendance_status === 'permission',
                                                     'bg-slate-700': item.attendance_status === 'pending'
                                                 }"
                                                 x-text="getInitials(item.student_name)">
                                            </div>
                                        </template>
                                        
                                        <div class="min-w-0 flex-1 space-y-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="text-xs font-black text-slate-900 dark:text-white" x-text="item.student_name"></h4>
                                                <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-[10px] font-bold border border-slate-200/80 dark:border-slate-700" x-text="item.student_identifier"></span>
                                                <template x-if="item.is_today">
                                                    <span class="px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-300 text-[9px] font-black uppercase border border-red-200 dark:border-red-800/80">Hari Ini</span>
                                                </template>
                                            </div>
                                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate" x-text="item.topic"></p>
                                            <div class="flex items-center gap-3 text-[11px] text-slate-600 dark:text-slate-300">
                                                <span class="font-bold text-slate-800 dark:text-slate-100" x-text="item.scheduled_date_formatted + ' • ' + item.scheduled_time_formatted"></span>
                                                <span class="font-medium text-slate-600 dark:text-slate-300" x-text="item.type === 'online' ? '🎥 Daring' : '🏢 Tatap Muka'"></span>
                                                <template x-if="item.dosen_name">
                                                    <span class="text-slate-500 dark:text-slate-400" x-text="'Dosen: ' + item.dosen_name"></span>
                                                </template>
                                            </div>

                                            <!-- Permission Reason Callout -->
                                            <template x-if="item.attendance_status === 'permission' && item.attendance_reason">
                                                <div class="mt-2 p-2.5 bg-amber-100/70 dark:bg-amber-950/60 rounded-xl border border-amber-200 dark:border-amber-700/80">
                                                    <span class="text-[9px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider block">Alasan Izin Mahasiswa:</span>
                                                    <p class="text-xs text-amber-950 dark:text-amber-100 font-medium italic mt-0.5" x-text="'&ldquo;' + item.attendance_reason + '&rdquo;'"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Status Badge & Quick Actions -->
                                    <div class="flex sm:flex-col items-end justify-between sm:justify-center gap-2 shrink-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-200 dark:border-slate-700">
                                        <template x-if="item.attendance_status === 'attending'">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[10px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-700/80">
                                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Akan Hadir</span>
                                            </span>
                                        </template>
                                        <template x-if="item.attendance_status === 'permission'">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[10px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-700/80">
                                                <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <span>Izin / Berhalangan</span>
                                            </span>
                                        </template>
                                        <template x-if="item.attendance_status === 'pending'">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-[10px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                                                <span>Belum Respon</span>
                                            </span>
                                        </template>

                                        <template x-if="item.confirmed_at_formatted">
                                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium" x-text="item.confirmed_at_formatted"></span>
                                        </template>

                                        <!-- WhatsApp Reminder Button for Pending / Permission -->
                                        <template x-if="item.student_phone && (item.attendance_status === 'pending' || item.attendance_status === 'permission')">
                                            <a :href="getWaLink(item)" 
                                               target="_blank" 
                                               class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/80 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/80 rounded-lg text-[10px] font-bold transition-all shadow-2xs">
                                                <span>📲 Chat WA</span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="filteredLiveSessions.length === 0">
                                <div class="p-8 text-center bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-200 dark:border-slate-700">
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Tidak ada mahasiswa yang sesuai dengan filter kehadiran ini.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 px-6 bg-slate-50/80 dark:bg-slate-900/80 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2 text-[11px] text-slate-600 dark:text-slate-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Live Sync Otomatis Aktif (tiap 10 detik) • Terakhir: <strong class="text-slate-800 dark:text-slate-100" x-text="lastUpdated"></strong></span>
                        </div>
                        <button type="button" @click="liveModalOpen = false" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-100 dark:hover:bg-white text-white dark:text-slate-900 font-bold rounded-xl text-xs transition-all shadow-xs cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </template>

        <!-- 4. MODAL KONFIRMASI PEMBATALAN JADWAL BIMBINGAN -->
        <template x-teleport="body">
            <div x-data="mentoringCancelModal()" 
                 @open-cancel-modal.window="openModal($event.detail)" 
                 x-show="cancelModalOpen" 
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
                        
                        <form :action="'{{ url('mentoring-sessions') }}/' + cancelData.id" method="POST" class="p-6 sm:p-8 space-y-5">
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
                                        <span class="font-bold text-slate-800 dark:text-slate-200" x-text="cancelData.student_name"></span>
                                        <span class="text-slate-400 text-[10px] ml-1" x-text="'(' + cancelData.student_npm + ')'"></span>
                                    </div>
                                </div>
                                <div class="h-px bg-slate-200/70 dark:bg-slate-700/70"></div>
                                <div class="flex justify-between items-start gap-3">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Waktu Sesi:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="cancelData.scheduled_date + ' • ' + cancelData.scheduled_time"></span>
                                </div>
                                <div class="h-px bg-slate-200/70 dark:bg-slate-700/70"></div>
                                <div class="flex justify-between items-start gap-3">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Topik:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right line-clamp-2" x-text="cancelData.topic"></span>
                                </div>
                            </div>

                            <!-- Group Option -->
                            <template x-if="cancelData.is_group">
                                <div class="p-3 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/60 rounded-xl space-y-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5">
                                        👥 Sesi Bimbingan Bersama Terdeteksi
                                    </span>
                                    <label class="flex items-start gap-2.5 cursor-pointer text-xs">
                                        <input type="checkbox" 
                                               name="apply_to_group" 
                                               value="1" 
                                               x-model="cancelData.apply_to_group"
                                               class="mt-0.5 w-4 h-4 text-indigo-600 rounded border-slate-300 dark:border-slate-700 focus:ring-indigo-500">
                                        <div>
                                            <span class="font-bold text-slate-800 dark:text-slate-200">
                                                Batalkan untuk seluruh (<span x-text="cancelData.group_count"></span>) mahasiswa dalam kelompok jam ini
                                            </span>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                Jika tidak dicentang, hanya jadwal untuk <strong x-text="cancelData.student_name"></strong> yang dibatalkan.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </template>

                            <!-- Input Alasan -->
                            <div>
                                <label for="cancel_reason" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Alasan Pembatalan (Opsional / Disampaikan ke Mahasiswa)
                                </label>
                                <textarea name="reason" 
                                          id="cancel_reason" 
                                          x-model="cancelData.reason"
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

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
    <style>
        :root {
            --fc-border-color: #f1f5f9;
            --fc-daygrid-event-dot-width: 8px;
        }
        .dark {
            --fc-border-color: #334155;
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: #1e293b;
            --fc-list-event-hover-bg-color: #334155;
        }
        .fc {
            font-family: 'Inter', sans-serif;
        }
        .fc .fc-toolbar-title {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: -0.025em;
            color: #1e293b;
        }
        .dark .fc .fc-toolbar-title {
            color: #f1f5f9;
        }
        .fc .fc-button {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0.4rem 0.8rem;
            border-radius: 0.75rem;
            transition: all 0.2s;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .fc .fc-button {
            background-color: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }
        .fc .fc-button:hover {
            background-color: #f8fafc;
            color: #1e293b;
        }
        .dark .fc .fc-button:hover {
            background-color: #334155;
            color: #f1f5f9;
        }
        .fc .fc-button-active {
            background-color: #ea580c !important;
            border-color: #ea580c !important;
            color: #ffffff !important;
        }
        .fc-event {
            cursor: pointer;
            border-radius: 6px;
            padding: 2px 4px;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    @endpush
</x-app-layout>

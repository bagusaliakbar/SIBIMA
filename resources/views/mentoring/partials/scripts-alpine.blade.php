    <script>
        function mentoringSchedule() {
            return {
                openGuideModal: false,
                activeGuideTab: 'workflow', // 'workflow', 'views', 'tips', 'faq'
                viewMode: (function() {
                    const saved = '{{ request('view', '') }}' || localStorage.getItem('sibima_mentoring_view_mode');
                    return saved === 'cards' ? 'cards' : 'table';
                })(),
                cardGrouping: localStorage.getItem('sibima_card_grouping') || 'session', // 'session' (default) or 'student'
                attendanceStats: @json($attendanceStats ?? []),
                sessionStatusFilter: 'all', // 'all' | 'actionable' | 'completed'
                selectedSessionIds: [],
                expandedSessions: [],
                allSessionIds: @json($sessions->pluck('id')->map(fn($id) => (string)$id)->values()->all()),
                matchesStatusFilter(status) {
                    if (this.sessionStatusFilter === 'all') return true;
                    const isActionable = status === 'pending' || status === 'approved';
                    return this.sessionStatusFilter === 'actionable' ? isActionable : !isActionable;
                },
                
                init() {
                    window.__mentoringScope = this;
                    this.$watch('selectedSessionIds', () => {
                        this.notifySelection();
                    });
                    window.addEventListener('mentoring-clear-selection', () => {
                        this.clearSelection();
                    });
                },
                notifySelection() {
                    const ids = this.selectedSessionIds ? this.selectedSessionIds.map(String) : [];
                    window.dispatchEvent(new CustomEvent('mentoring-selection-changed', {
                        detail: { ids: ids }
                    }));
                },
                setGrouping(mode) {
                    this.cardGrouping = mode;
                    localStorage.setItem('sibima_card_grouping', mode);
                },
                switchView(mode) {
                    this.viewMode = mode === 'cards' ? 'cards' : 'table';
                    localStorage.setItem('sibima_mentoring_view_mode', this.viewMode);
                },
                toggleExpand(id) {
                    const strId = String(id);
                    if (this.expandedSessions.includes(strId)) {
                        this.expandedSessions = this.expandedSessions.filter(i => i !== strId);
                    } else {
                        this.expandedSessions.push(strId);
                    }
                },
                isExpanded(id) {
                    return this.expandedSessions.includes(String(id));
                },
                toggleExpandAll() {
                    if (this.expandedSessions.length > 0) {
                        this.expandedSessions = [];
                    } else {
                        this.expandedSessions = [...this.allSessionIds];
                    }
                },
                toggleSelectAll() {
                    const actionableIds = @json($sessions->filter(fn($s) => $s->status !== 'completed' || $s->is_absent)->pluck('id')->map(fn($id) => (string)$id)->values()->all());
                    const currentStr = this.selectedSessionIds.map(String);
                    const allSelected = actionableIds.length > 0 && actionableIds.every(id => currentStr.includes(id));
                    if (allSelected) {
                        this.selectedSessionIds = this.selectedSessionIds.filter(id => !actionableIds.includes(String(id)));
                    } else {
                        this.selectedSessionIds = Array.from(new Set([...currentStr, ...actionableIds]));
                    }
                    this.notifySelection();
                },
                isAllSelected() {
                    const actionableIds = @json($sessions->filter(fn($s) => $s->status !== 'completed' || $s->is_absent)->pluck('id')->map(fn($id) => (string)$id)->values()->all());
                    if (actionableIds.length === 0) return false;
                    const currentStr = this.selectedSessionIds.map(String);
                    return actionableIds.every(id => currentStr.includes(id));
                },
                toggleSession(id) {
                    const strId = String(id);
                    const current = this.selectedSessionIds.map(String);
                    if (current.includes(strId)) {
                        this.selectedSessionIds = current.filter(i => i !== strId);
                    } else {
                        this.selectedSessionIds = [...current, strId];
                    }
                    this.notifySelection();
                },
                isSelected(id) {
                    if (!this.selectedSessionIds || this.selectedSessionIds.length === 0) return false;
                    return this.selectedSessionIds.map(String).includes(String(id));
                },
                toggleSelectGroup(ids) {
                    if (!Array.isArray(ids) || ids.length === 0) return;
                    const strIds = ids.map(id => String(id));
                    const currentStr = this.selectedSessionIds.map(String);
                    const allSelected = strIds.every(id => currentStr.includes(id));
                    if (allSelected) {
                        this.selectedSessionIds = this.selectedSessionIds.filter(id => !strIds.includes(String(id)));
                    } else {
                        const combined = Array.from(new Set([...currentStr, ...strIds]));
                        this.selectedSessionIds = combined;
                    }
                    this.notifySelection();
                },
                isGroupSelected(ids) {
                    if (!Array.isArray(ids) || ids.length === 0) return false;
                    if (!this.selectedSessionIds || this.selectedSessionIds.length === 0) return false;
                    const strIds = ids.map(id => String(id));
                    const currentStr = this.selectedSessionIds.map(String);
                    return strIds.every(id => currentStr.includes(id));
                },
                clearSelection() {
                    this.selectedSessionIds = [];
                    this.notifySelection();
                },
                openBulkAbsent() {
                    if (!this.selectedSessionIds || this.selectedSessionIds.length === 0) return;
                    window.dispatchEvent(new CustomEvent('open-bulk-absent-modal', { detail: { ids: this.selectedSessionIds } }));
                },
                openBulkComplete() {
                    if (!this.selectedSessionIds || this.selectedSessionIds.length === 0) return;
                    window.dispatchEvent(new CustomEvent('open-bulk-complete-modal', { detail: { ids: this.selectedSessionIds } }));
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

        window.mentoringSchedule = mentoringSchedule;
        window.mentoringLiveAttendance = mentoringLiveAttendance;
        window.mentoringCancelModal = mentoringCancelModal;

        if (window.Alpine) {
            window.Alpine.data('mentoringSchedule', mentoringSchedule);
            window.Alpine.data('mentoringLiveAttendance', mentoringLiveAttendance);
            window.Alpine.data('mentoringCancelModal', mentoringCancelModal);
        }
        document.addEventListener('alpine:init', () => {
            if (window.Alpine) {
                window.Alpine.data('mentoringSchedule', mentoringSchedule);
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

        function mentoringBulkAbsentModal() {
            return {
                open: false,
                sessionIds: [],
                openModal(ids) {
                    this.sessionIds = Array.isArray(ids) ? ids : [];
                    this.open = true;
                },
                closeModal() {
                    this.open = false;
                }
            };
        }

        function mentoringBulkCompleteModal() {
            return {
                open: false,
                sessionIds: [],
                openModal(ids) {
                    this.sessionIds = Array.isArray(ids) ? ids : [];
                    this.open = true;
                },
                closeModal() {
                    this.open = false;
                }
            };
        }

        function mentoringBulkActionBar() {
            return {
                selectedIds: [],
                init() {
                    window.addEventListener('mentoring-selection-changed', (e) => {
                        this.selectedIds = (e.detail && Array.isArray(e.detail.ids)) ? e.detail.ids.map(String) : [];
                    });
                },
                clear() {
                    window.dispatchEvent(new CustomEvent('mentoring-clear-selection'));
                },
                openBulkAbsent() {
                    if (!this.selectedIds || this.selectedIds.length === 0) return;
                    window.dispatchEvent(new CustomEvent('open-bulk-absent-modal', { detail: { ids: this.selectedIds } }));
                },
                openBulkComplete() {
                    if (!this.selectedIds || this.selectedIds.length === 0) return;
                    window.dispatchEvent(new CustomEvent('open-bulk-complete-modal', { detail: { ids: this.selectedIds } }));
                }
            };
        }
    </script>

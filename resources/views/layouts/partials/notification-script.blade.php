                        <script>
                            function notificationDropdown() {
                                return {
                                    isOpen: false,
                                    notifications: [],
                                    unreadCount: 0,
                                    lastCount: 0,
                                    activeTab: 'all',
                                    userRole: '{{ Auth::user()->role }}',
                                    isStudent: {{ Auth::user()->role === 'mahasiswa' ? 'true' : 'false' }},
                                    soundEnabled: localStorage.getItem('sibima_notif_sound_enabled') !== 'false',
                                    pushEnabled: ('Notification' in window) && Notification.permission === 'granted',
                                    attendanceLoading: {},
                                    attendanceSuccess: {},
                                    activePermissionInput: null,
                                    permissionReason: {},

                                    async togglePushNotifications() {
                                        if (!('Notification' in window)) {
                                            window.sibimaToast('Browser ini belum mendukung fitur Web Push Notification.', 'warning', 'Tidak Didukung');
                                            return;
                                        }

                                        if (Notification.permission === 'granted') {
                                            // Trigger a test notification via server endpoint
                                            try {
                                                const res = await fetch('{{ route("push-subscriptions.test") }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    }
                                                });
                                                const data = await res.json();
                                                if (data.status === 'success' && data.payload) {
                                                    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                                                        const reg = await navigator.serviceWorker.ready;
                                                        reg.showNotification(data.payload.title, {
                                                            body: data.payload.body,
                                                            icon: data.payload.icon,
                                                            badge: data.payload.badge,
                                                            vibrate: [100, 50, 100],
                                                            data: { url: data.payload.url }
                                                        });
                                                    } else {
                                                        new Notification(data.payload.title, {
                                                            body: data.payload.body,
                                                            icon: data.payload.icon
                                                        });
                                                    }
                                                }
                                            } catch (err) {
                                                console.warn('[SIBIMA PWA] Error testing push notification:', err);
                                            }
                                            return;
                                        }

                                        if (Notification.permission === 'denied') {
                                            window.sibimaToast('Izin notifikasi saat ini diblokir di setelan browser. Silakan izinkan notifikasi melalui ikon gembok / pengaturan browser Anda.', 'warning', 'Izin Diblokir');
                                            return;
                                        }

                                        // Request permission from user
                                        const permission = await Notification.requestPermission();
                                        if (permission === 'granted') {
                                            this.pushEnabled = true;
                                            if ('serviceWorker' in navigator) {
                                                try {
                                                    const reg = await navigator.serviceWorker.ready;
                                                    let sub = await reg.pushManager.getSubscription();
                                                    if (!sub) {
                                                        sub = await reg.pushManager.subscribe({
                                                            userVisibleOnly: true,
                                                            applicationServerKey: null
                                                        }).catch(() => null);
                                                    }
                                                    if (sub) {
                                                        const subJson = sub.toJSON();
                                                        await fetch('{{ route("push-subscriptions.store") }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                            },
                                                            body: JSON.stringify({
                                                                endpoint: sub.endpoint,
                                                                keys: subJson.keys || {},
                                                                contentEncoding: 'aes128gcm'
                                                            })
                                                        });
                                                    }
                                                } catch (e) {
                                                    console.warn('[SIBIMA PWA] Push subscription sync notice:', e);
                                                }
                                            }
                                            new Notification('Notifikasi SIBIMA Aktif', {
                                                body: 'Perangkat Anda kini siap menerima pembaruan jadwal dan progres bimbingan secara langsung.',
                                                icon: '{{ asset("icons/icon-192x192.png") }}'
                                            });
                                        }
                                    },

                                    get filteredNotifications() {
                                        if (this.activeTab === 'unread') {
                                            return this.notifications.filter(n => !n.read_at);
                                        }
                                        return this.notifications;
                                    },

                                    init() {
                                        this.fetchNotifications();
                                        window.refreshNotifications = () => this.fetchNotifications();
                                        setInterval(() => this.fetchNotifications(), 30000); // Polling 30s
                                    },

                                    toggle() {
                                        this.isOpen = !this.isOpen;
                                        if (this.isOpen) this.fetchNotifications();
                                    },

                                    toggleSound() {
                                        this.soundEnabled = !this.soundEnabled;
                                        localStorage.setItem('sibima_notif_sound_enabled', this.soundEnabled);
                                    },

                                    fetchNotifications() {
                                        fetch('{{ route("notifications.index") }}', {
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json'
                                            }
                                        })
                                            .then(res => res.json())
                                            .then(data => {
                                                this.notifications = data.notifications || [];
                                                this.unreadCount = data.unread_count || 0;
                                                
                                                if (this.unreadCount > this.lastCount) {
                                                    this.playSound();
                                                }
                                                this.lastCount = this.unreadCount;
                                            });
                                    },

                                    playSound() {
                                        if (!this.soundEnabled) return;
                                        const audio = document.getElementById('notif-sound');
                                        if (audio) {
                                            audio.currentTime = 0;
                                            audio.play().catch(e => console.log('Audio play failed:', e));
                                        }
                                    },

                                    isAttendanceNotification(notif) {
                                        if (!this.isStudent) return false;
                                        const mentoringId = notif.data?.mentoring_id;
                                        if (!mentoringId) return false;
                                        const actionable = notif.data?.actionable;
                                        const type = notif.data?.type;
                                        const title = (notif.data?.title || '').toLowerCase();
                                        const msg = (notif.data?.message || '').toLowerCase();

                                        return actionable === 'attendance' ||
                                               type === 'attendance' ||
                                               title.includes('jadwal bimbingan baru') ||
                                               title.includes('menunggu konfirmasi') ||
                                               msg.includes('menjadwalkan');
                                    },

                                    async confirmAttendance(notif, status, reason = null) {
                                        const mentoringId = notif.data?.mentoring_id;
                                        if (!mentoringId) return;

                                        this.attendanceLoading[mentoringId] = true;
                                        try {
                                            const payload = {
                                                status: status,
                                                _token: '{{ csrf_token() }}'
                                            };
                                            if (status === 'permission' && reason) {
                                                payload.reason = reason;
                                            }

                                            const res = await fetch(`/mentoring-sessions/${mentoringId}/confirm-attendance`, {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-Requested-With': 'XMLHttpRequest',
                                                    'Accept': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify(payload)
                                            });

                                            const data = await res.json();
                                            if (res.ok && data.success) {
                                                this.attendanceSuccess[mentoringId] = status;
                                                this.activePermissionInput = null;
                                                this.markSingleRead(notif.id);
                                                window.sibimaToast('Konfirmasi kehadiran berhasil disimpan.', 'success', 'Terkonfirmasi');
                                            } else {
                                                window.sibimaToast(data.message || 'Gagal menyimpan konfirmasi kehadiran.', 'error', 'Gagal Menyimpan');
                                            }
                                        } catch (e) {
                                            console.error(e);
                                            window.sibimaToast('Terjadi kesalahan jaringan saat mengonfirmasi kehadiran.', 'error', 'Kesalahan Jaringan');
                                        } finally {
                                            this.attendanceLoading[mentoringId] = false;
                                        }
                                    },

                                    submitPermission(notif) {
                                        const reason = (this.permissionReason[notif.id] || '').trim();
                                        if (!reason) {
                                            window.sibimaToast('Mohon tuliskan alasan berhalangan hadir.', 'warning', 'Alasan Wajib Diisi');
                                            return;
                                        }
                                        this.confirmAttendance(notif, 'permission', reason);
                                    },

                                    getCategory(notif) {
                                        const type = notif.data?.type || '';
                                        const title = (notif.data?.title || '').toLowerCase();
                                        const msg = (notif.data?.message || '').toLowerCase();

                                        if (type === 'danger' || title.includes('batal') || title.includes('tolak') || msg.includes('dibatalkan') || msg.includes('ditolak')) {
                                            return 'cancelled';
                                        }
                                        if (type === 'success' || title.includes('acc') || title.includes('selesai') || msg.includes('completed') || msg.includes('selesai') || msg.includes('acc')) {
                                            return 'success';
                                        }
                                        if (type === 'warning' || title.includes('pengingat') || title.includes('h-1') || msg.includes('pengingat') || msg.includes('h-1') || msg.includes('deadline')) {
                                            return 'reminder';
                                        }
                                        if (title.includes('revisi') || msg.includes('revisi') || type === 'revision') {
                                            return 'revision';
                                        }
                                        if (type === 'message' || title.includes('pesan') || msg.includes('pesan')) {
                                            return 'message';
                                        }
                                        if (type === 'info' || type === 'attendance' || title.includes('jadwal') || msg.includes('menjadwalkan') || msg.includes('jadwal') || msg.includes('reschedule')) {
                                            return 'schedule';
                                        }
                                        return 'info';
                                    },

                                    getTitle(notif) {
                                        if (notif.data?.title) return notif.data.title;
                                        const msg = notif.data?.message || '';
                                        const lower = msg.toLowerCase();
                                        if (lower.includes('completed') || lower.includes('selesai')) return 'Status Bimbingan Selesai';
                                        if (lower.includes('status bimbingan')) return 'Pembaruan Status Bimbingan';
                                        if (lower.includes('pengingat')) return 'Pengingat Jadwal Bimbingan (H-1)';
                                        if (lower.includes('acc')) return 'Rekomendasi ACC Dosen';
                                        if (lower.includes('menjadwalkan')) return 'Jadwal Bimbingan Baru';
                                        if (lower.includes('batal')) return 'Jadwal Bimbingan Dibatalkan';
                                        if (lower.includes('revisi')) return 'Pembaruan Revisi Skripsi';
                                        return 'Pemberitahuan SIBIMA';
                                    },

                                    getUrl(notif) {
                                        let url = notif.data?.url;
                                        const title = (notif.data?.title || '').toLowerCase();
                                        const msg = (notif.data?.message || '').toLowerCase();
                                        const isLecturer = this.userRole === 'dosen';
                                        const isStaff = this.userRole === 'admin' || this.userRole === 'kaprodi';

                                        // If explicit URL is provided in notification data, validate role compatibility
                                        if (url && url !== '#') {
                                            if (isLecturer) {
                                                // Prevent lecturer from opening student-only revision routes
                                                if (url.includes('student-defense-revisions')) {
                                                    return '{{ route("defense-examiner.index") }}';
                                                }
                                                if (url.includes('student-seminar-revisions')) {
                                                    return '{{ route("seminar-examiner.index") }}';
                                                }
                                                // Prevent lecturer from opening student/admin-only application routes
                                                if (url.includes('seminar-applications') || url.includes('thesis-defense-applications')) {
                                                    return '{{ route("theses.index") }}';
                                                }
                                            }
                                            return url;
                                        }

                                        // 1. Revision Notifications: Role-aware deep link
                                        if (title.includes('revisi') || msg.includes('revisi') || notif.data?.type === 'revision') {
                                            const isSidang = title.includes('sidang') || msg.includes('sidang');
                                            if (isLecturer) {
                                                return isSidang ? '{{ route("defense-examiner.index") }}' : '{{ route("seminar-examiner.index") }}';
                                            }
                                            if (isStaff) {
                                                return isSidang ? '{{ route("thesis-defense-schedules.index") }}' : '{{ route("seminar-schedules.index") }}';
                                            }
                                            return isSidang ? '{{ route("student-defense-revisions.index") }}' : '{{ route("student-seminar-revisions.index") }}';
                                        }

                                        // 2. ACC Notifications: Role-aware deep link
                                        if (title.includes('acc') || msg.includes('acc')) {
                                            if (isLecturer) {
                                                return '{{ route("theses.index") }}';
                                            }
                                            const isSidang = title.includes('sidang') || msg.includes('sidang') || notif.data?.acc_type === 'sidang';
                                            return isSidang ? '{{ route("thesis-defense-applications.index") }}' : '{{ route("seminar-applications.index") }}';
                                        }

                                        // 3. Status Bimbingan / Jadwal: Deep link to mentoring-sessions.index with anchor/highlight
                                        if (notif.data?.mentoring_id) {
                                            return '{{ route("mentoring-sessions.index") }}?highlight=' + notif.data.mentoring_id + '#session-' + notif.data.mentoring_id;
                                        }

                                        // 4. Jadwal / Penugasan
                                        if (title.includes('jadwal') || msg.includes('jadwal') || title.includes('penugasan') || msg.includes('penugasan')) {
                                            const isSidang = title.includes('sidang') || msg.includes('sidang');
                                            if (isLecturer) {
                                                return isSidang ? '{{ route("defense-examiner.index") }}' : '{{ route("seminar-examiner.index") }}';
                                            }
                                        }

                                        if (notif.data?.thesis_id) return '{{ route("theses.index") }}';
                                        return '#';
                                    },

                                    markAsRead(id, url) {
                                        fetch(`/notifications/${id}/read`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json'
                                            }
                                        }).then(() => {
                                            if (url && url !== '#') {
                                                window.location.href = url;
                                            } else {
                                                this.fetchNotifications();
                                            }
                                        });
                                    },

                                    markSingleRead(id) {
                                        fetch(`/notifications/${id}/read`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json'
                                            }
                                        }).then(() => {
                                            this.fetchNotifications();
                                        });
                                    },

                                    markAllAsRead() {
                                        fetch('{{ route("notifications.read-all") }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json'
                                            }
                                        }).then(() => this.fetchNotifications());
                                    },

                                    formatDate(dateString) {
                                        if (!dateString) return '';
                                        const date = new Date(dateString);
                                        const now = new Date();
                                        const diffSec = Math.floor((now - date) / 1000);
                                        
                                        if (diffSec < 60) return 'Baru saja';
                                        const diffMin = Math.floor(diffSec / 60);
                                        if (diffMin < 60) return `${diffMin} menit lalu`;
                                        const diffHour = Math.floor(diffMin / 60);
                                        if (diffHour < 24) return `${diffHour} jam lalu`;
                                        
                                        const isYesterday = (now.getDate() - date.getDate() === 1) && 
                                                            (now.getMonth() === date.getMonth()) && 
                                                            (now.getFullYear() === date.getFullYear());
                                        const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
                                        if (isYesterday) return `Kemarin, ${timeStr}`;
                                        
                                        const diffDay = Math.floor(diffHour / 24);
                                        if (diffDay < 7) return `${diffDay} hari lalu`;
                                        
                                        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + `, ${timeStr}`;
                                    }
                                }
                            }
                        </script>

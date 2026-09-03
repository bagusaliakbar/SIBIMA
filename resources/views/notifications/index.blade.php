<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Riwayat Notifikasi', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-sm flex items-center border border-emerald-200 dark:border-emerald-500/20">
                <svg class="w-4 h-4 mr-3 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Top Card Header & Stats --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 transition-colors">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </span>
                        <span>Riwayat Notifikasi</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Daftar lengkap seluruh pemberitahuan jadwal bimbingan, status ACC, catatan revisi, dan pengingat aktivitas skripsi.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 hover:bg-orange-100 dark:hover:bg-orange-900/60 border border-orange-200 dark:border-orange-800 text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Tandai Semua Dibaca</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Filter Tabs and Search Bar --}}
            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700/60 flex flex-col md:flex-row items-center justify-between gap-4">
                {{-- Status Tabs --}}
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-xl w-full md:w-auto">
                    <a href="{{ route('notifications.index', ['tab' => 'all', 'search' => $search]) }}" 
                       class="flex-1 md:flex-initial px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-center {{ $activeTab === 'all' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                        Semua ({{ $totalCount }})
                    </a>
                    <a href="{{ route('notifications.index', ['tab' => 'unread', 'search' => $search]) }}" 
                       class="flex-1 md:flex-initial px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5 {{ $activeTab === 'unread' ? 'bg-white dark:bg-slate-800 text-orange-600 dark:text-orange-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                        <span>Belum Dibaca</span>
                        @if($unreadCount > 0)
                            <span class="px-1.5 py-0.5 rounded-full bg-orange-600 text-white text-[9px] font-black leading-none">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('notifications.index', ['tab' => 'read', 'search' => $search]) }}" 
                       class="flex-1 md:flex-initial px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-center {{ $activeTab === 'read' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                        Sudah Dibaca
                    </a>
                </div>

                {{-- Search Box --}}
                <form method="GET" action="{{ route('notifications.index') }}" class="w-full md:w-72 relative">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari isi notifikasi..." 
                           class="block w-full pl-9 pr-10 py-2 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs transition-all">
                    @if(!empty($search))
                        <a href="{{ route('notifications.index', ['tab' => $activeTab]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Notifications List Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
            @if($notifications->isEmpty())
                <div class="p-16 text-center">
                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-800 shadow-xs">
                        <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Tidak ada notifikasi ditemukan</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto">
                        @if(!empty($search))
                            Tidak ada notifikasi yang cocok dengan kata kunci "{{ $search }}". Silakan coba kata kunci lain.
                        @else
                            Anda belum memiliki riwayat notifikasi untuk kategori yang dipilih.
                        @endif
                    </p>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @foreach($notifications as $notif)
                        @php
                            $data = $notif->data ?? [];
                            $title = $data['title'] ?? 'Pemberitahuan SIBIMA';
                            $message = $data['message'] ?? '';
                            $type = $data['type'] ?? 'info';
                            $mentoringId = $data['mentoring_id'] ?? null;
                            $thesisId = $data['thesis_id'] ?? null;
                            $accType = $data['acc_type'] ?? null;

                            // Category mapping
                            $category = 'info';
                            $lowerTitle = strtolower($title);
                            $lowerMsg = strtolower($message);

                            if ($type === 'danger' || str_contains($lowerTitle, 'batal') || str_contains($lowerTitle, 'tolak') || str_contains($lowerMsg, 'dibatalkan') || str_contains($lowerMsg, 'ditolak')) {
                                $category = 'cancelled';
                            } elseif ($type === 'success' || str_contains($lowerTitle, 'acc') || str_contains($lowerTitle, 'selesai') || str_contains($lowerMsg, 'acc') || str_contains($lowerMsg, 'selesai')) {
                                $category = 'success';
                            } elseif ($type === 'warning' || str_contains($lowerTitle, 'pengingat') || str_contains($lowerTitle, 'h-1') || str_contains($lowerMsg, 'pengingat') || str_contains($lowerMsg, 'h-1')) {
                                $category = 'reminder';
                            } elseif (str_contains($lowerTitle, 'revisi') || str_contains($lowerMsg, 'revisi') || $type === 'revision') {
                                $category = 'revision';
                            } elseif ($type === 'message' || str_contains($lowerTitle, 'pesan')) {
                                $category = 'message';
                            } elseif ($type === 'schedule' || $type === 'attendance' || str_contains($lowerTitle, 'jadwal') || str_contains($lowerMsg, 'menjadwalkan') || str_contains($lowerMsg, 'jadwal')) {
                                $category = 'schedule';
                            }

                            // Deep Linking URL Resolver
                            $targetUrl = $data['url'] ?? null;
                            if (str_contains($lowerTitle, 'acc') || str_contains($lowerMsg, 'acc')) {
                                if (str_contains($lowerTitle, 'up') || str_contains($lowerMsg, 'up') || $accType === 'up') {
                                    $targetUrl = route('seminar-applications.index');
                                } elseif (str_contains($lowerTitle, 'sidang') || str_contains($lowerMsg, 'sidang') || $accType === 'sidang') {
                                    $targetUrl = route('thesis-defense-applications.index');
                                } else {
                                    $targetUrl = route('seminar-applications.index');
                                }
                            } elseif (str_contains($lowerTitle, 'revisi') || str_contains($lowerMsg, 'revisi') || $type === 'revision') {
                                if (empty($targetUrl) || $targetUrl === '#' || !str_contains($targetUrl, 'revision')) {
                                    $targetUrl = (str_contains($lowerTitle, 'sidang') || str_contains($lowerMsg, 'sidang'))
                                        ? route('student-defense-revisions.index')
                                        : route('student-seminar-revisions.index');
                                }
                            } elseif ($mentoringId) {
                                $targetUrl = route('mentoring-sessions.index', ['highlight' => $mentoringId]) . '#session-' . $mentoringId;
                            } elseif (empty($targetUrl) || $targetUrl === '#') {
                                $targetUrl = route('mentoring-sessions.index');
                            }

                            // Check if student attendance is actionable
                            $isStudent = Auth::user()->role === 'mahasiswa';
                            $isActionableAttendance = $isStudent && $mentoringId && (
                                ($data['actionable'] ?? '') === 'attendance' ||
                                $type === 'attendance' ||
                                str_contains($lowerTitle, 'jadwal bimbingan baru') ||
                                str_contains($lowerMsg, 'menjadwalkan')
                            );
                        @endphp

                        <div class="p-5 sm:p-6 transition-colors flex items-start gap-4 {{ $notif->read_at ? 'hover:bg-slate-50/60 dark:hover:bg-slate-700/30' : 'bg-orange-50/30 dark:bg-orange-950/15 hover:bg-orange-50/50 dark:hover:bg-orange-950/25' }}">
                            {{-- Semantic Icon --}}
                            <div class="w-10 h-10 min-w-[2.5rem] min-h-[2.5rem] rounded-xl flex items-center justify-center border shadow-xs shrink-0
                                @if($category === 'schedule') bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-blue-200/80 dark:border-blue-700/50
                                @elseif($category === 'success') bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-200/80 dark:border-emerald-700/50
                                @elseif($category === 'reminder') bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border-amber-200/80 dark:border-amber-700/50
                                @elseif($category === 'revision') bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border-indigo-200/80 dark:border-indigo-700/50
                                @elseif($category === 'cancelled') bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-200/80 dark:border-rose-700/50
                                @elseif($category === 'message') bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200/80 dark:border-orange-700/50
                                @else bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700
                                @endif">
                                @if($category === 'schedule')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @elseif($category === 'success')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                @elseif($category === 'reminder')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                @elseif($category === 'revision')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                @elseif($category === 'cancelled')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                @elseif($category === 'message')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>

                            {{-- Main Content --}}
                            <div class="flex-1 min-w-0" x-data="{ 
                                attendanceStatus: null, 
                                showReasonInput: false, 
                                reasonText: '', 
                                loading: false,
                                async sendAttendance(status, reason = null) {
                                    this.loading = true;
                                    try {
                                        const res = await fetch('{{ route('mentoring-sessions.confirm-attendance', $mentoringId ?? 0) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ status: status, reason: reason, _token: '{{ csrf_token() }}' })
                                        });
                                        const resData = await res.json();
                                        if (res.ok && resData.success) {
                                            this.attendanceStatus = status;
                                            this.showReasonInput = false;
                                        } else {
                                            alert(resData.message || 'Gagal menyimpan konfirmasi.');
                                        }
                                    } catch (e) {
                                        alert('Terjadi kesalahan.');
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                            {{ $title }}
                                        </h4>
                                        @if(!$notif->read_at)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-400 text-[10px] font-black">
                                                Baru
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                        {{ $notif->created_at->locale('id')->diffForHumans() }} ({{ $notif->created_at->format('d/m/Y H:i') }})
                                    </span>
                                </div>

                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                                    {{ $message }}
                                </p>

                                {{-- Actionable Attendance Buttons (If Applicable) --}}
                                @if($isActionableAttendance)
                                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                                        <template x-if="attendanceStatus">
                                            <div class="flex items-center gap-2 text-xs font-bold">
                                                <template x-if="attendanceStatus === 'attending'">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 text-xs font-black uppercase tracking-wider">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Konfirmasi Disimpan: Akan Hadir</span>
                                                    </span>
                                                </template>
                                                <template x-if="attendanceStatus === 'permission'">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700 text-xs font-black uppercase tracking-wider">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"></path></svg>
                                                        <span>Konfirmasi Disimpan: Izin</span>
                                                    </span>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!attendanceStatus">
                                            <div>
                                                <div x-show="!showReasonInput" class="flex flex-wrap items-center gap-2">
                                                    <button type="button" 
                                                            @click="sendAttendance('attending')" 
                                                            :disabled="loading"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider shadow-2xs transition-all cursor-pointer disabled:opacity-50">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Akan Hadir</span>
                                                    </button>

                                                    <button type="button" 
                                                            @click="showReasonInput = true" 
                                                            :disabled="loading"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 dark:bg-slate-700 dark:hover:bg-rose-950/40 text-slate-700 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200 dark:border-slate-600 text-xs font-black uppercase tracking-wider shadow-2xs transition-all cursor-pointer disabled:opacity-50">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"></path></svg>
                                                        <span>Izin</span>
                                                    </button>
                                                    <span x-show="loading" class="text-xs text-slate-400 animate-pulse font-medium">Menyimpan...</span>
                                                </div>

                                                <div x-show="showReasonInput" class="space-y-2 mt-2 max-w-md">
                                                    <input type="text" 
                                                           x-model="reasonText" 
                                                           placeholder="Tuliskan alasan berhalangan hadir..." 
                                                           class="w-full text-xs px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-orange-500"
                                                           @keydown.enter.prevent="if (reasonText.trim()) sendAttendance('permission', reasonText)">
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" 
                                                                @click="if (reasonText.trim()) { sendAttendance('permission', reasonText); } else { alert('Mohon sertakan alasan izin.'); }"
                                                                :disabled="loading"
                                                                class="px-3 py-1 rounded-md bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-colors cursor-pointer">
                                                            Kirim Izin
                                                        </button>
                                                        <button type="button" 
                                                                @click="showReasonInput = false" 
                                                                class="px-3 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors cursor-pointer">
                                                            Batal
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                @endif

                                {{-- Action Links / Buttons --}}
                                <div class="mt-3 flex items-center gap-3">
                                    <a href="{{ $targetUrl }}" class="inline-flex items-center gap-1 text-xs font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 hover:underline">
                                        <span>Buka Halaman Terkait</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>

                                    @if(!$notif->read_at)
                                        <span class="text-slate-300 dark:text-slate-600">•</span>
                                        <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 cursor-pointer">
                                                Tandai Telah Dibaca
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="p-4 sm:p-5 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

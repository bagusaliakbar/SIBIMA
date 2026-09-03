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

        {{-- Canonical SIBIMA Status Tabs Navigation --}}
        <div class="flex items-center gap-1 border-b border-slate-200 dark:border-slate-700 overflow-x-auto pb-px custom-scrollbar">
            {{-- Tab: Semua --}}
            <a href="{{ route('notifications.index', ['tab' => 'all', 'search' => $search]) }}" 
               class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2.5 shrink-0 {{ $activeTab === 'all' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/10 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span>Semua Notifikasi</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'all' ? 'bg-orange-600 text-white shadow-2xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                    {{ $totalCount }}
                </span>
            </a>

            {{-- Tab: Belum Dibaca --}}
            <a href="{{ route('notifications.index', ['tab' => 'unread', 'search' => $search]) }}" 
               class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2.5 shrink-0 {{ $activeTab === 'unread' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/10 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                @if($unreadCount > 0)
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                @endif
                <span>Belum Dibaca</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'unread' ? 'bg-orange-600 text-white shadow-2xs' : ($unreadCount > 0 ? 'bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400') }}">
                    {{ $unreadCount }}
                </span>
            </a>

            {{-- Tab: Sudah Dibaca --}}
            <a href="{{ route('notifications.index', ['tab' => 'read', 'search' => $search]) }}" 
               class="px-6 py-4 border-b-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2.5 shrink-0 {{ $activeTab === 'read' ? 'border-orange-500 text-orange-600 bg-orange-50/50 dark:bg-orange-500/10 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span>Sudah Dibaca</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'read' ? 'bg-orange-600 text-white shadow-2xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                    {{ $readCount ?? max(0, $totalCount - $unreadCount) }}
                </span>
            </a>
        </div>

        {{-- Canonical SIBIMA Table Card Component --}}
        <x-table-card 
            title="Daftar Notifikasi"
            subtitle="Seluruh pemberitahuan jadwal bimbingan, status ACC, catatan revisi, dan aktivitas skripsi"
            :footer="$notifications->hasPages() ? $notifications->links() : null">
            
            <x-slot name="headerActions">
                <div class="flex flex-wrap items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari isi notifikasi..." 
                        route="notifications.index"
                        :params="['tab' => $activeTab]" />

                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm shadow-orange-500/20 whitespace-nowrap cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Tandai Semua Dibaca</span>
                            </button>
                        </form>
                    @endif
                </div>
            </x-slot>

            {{-- Notifications List / Empty State --}}
            @if($notifications->isEmpty())
                <div class="py-16 text-center max-w-md mx-auto px-6">
                    @if(!empty($search))
                        {{-- Empty search state --}}
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-orange-500 dark:text-orange-400 border border-orange-200/80 dark:border-orange-800/60 flex items-center justify-center mx-auto mb-4 shadow-xs">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Pencarian Tidak Ditemukan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Tidak ditemukan notifikasi yang cocok dengan kata kunci <span class="font-bold text-slate-700 dark:text-slate-300">"{{ $search }}"</span>.
                        </p>
                        <div class="mt-5">
                            <a href="{{ route('notifications.index', ['tab' => $activeTab]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Reset Pencarian</span>
                            </a>
                        </div>
                    @elseif($activeTab === 'unread')
                        {{-- All caught up in unread tab --}}
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/60 flex items-center justify-center mx-auto mb-4 shadow-xs">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Semua Notifikasi Telah Dibaca</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Kotak masuk Anda bersih! Tidak ada pemberitahuan baru yang belum dibaca saat ini.
                        </p>
                    @elseif($activeTab === 'read')
                        {{-- No read notifications yet --}}
                        <div class="w-14 h-14 rounded-2xl bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-800 flex items-center justify-center mx-auto mb-4 shadow-xs">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Belum Ada Notifikasi yang Dibaca</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Pemberitahuan yang telah Anda tandai selesai dibaca akan diarsipkan di tab ini.
                        </p>
                    @else
                        {{-- Completely empty --}}
                        <div class="w-14 h-14 rounded-2xl bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-800 flex items-center justify-center mx-auto mb-4 shadow-xs">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Belum Ada Riwayat Notifikasi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Pemberitahuan aktivitas jadwal bimbingan, catatan revisi, dan status persetujuan skripsi Anda akan muncul di sini.
                        </p>
                    @endif
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

                        <div class="p-5 sm:p-6 transition-colors flex items-start gap-4 {{ $notif->read_at ? 'hover:bg-slate-50/60 dark:hover:bg-slate-700/30' : 'bg-orange-50/20 dark:bg-orange-950/15 hover:bg-orange-50/40 dark:hover:bg-orange-950/25' }}">
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
            @endif
        </x-table-card>
    </div>
</x-app-layout>

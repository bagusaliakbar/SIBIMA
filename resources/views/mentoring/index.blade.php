{{-- Force fresh compilation for bug report floating capsule dock: v2 --}}
<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Bimbingan', 'route' => null]
        ]" />
    </x-slot>

    @include('mentoring.partials.scripts-alpine')

    <div class="w-full" x-data="mentoringSchedule()">
        <x-table-card 
            title="{{ $activeTab === 'history' ? 'Riwayat Bimbingan' : 'Jadwal Bimbingan' }}"
            :footer="method_exists($sessions, 'links') ? $sessions->links() : null">
            
            <x-slot name="headerActions">
                <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                    <!-- View Mode Toggle (Table vs Cards) -->
                    <div class="h-10 inline-flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                        <!-- 1. Mode Tabel -->
                        <button type="button" 
                                @click="switchView('table')" 
                                :class="viewMode === 'table' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                                class="h-full px-3 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                                title="Mode Tabel: Data terstruktur dengan rincian per baris"
                                aria-label="Tampilan Tabel">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Tabel</span>
                        </button>

                        <!-- 2. Mode Kartu -->
                        <button type="button" 
                                @click="switchView('cards')" 
                                :class="viewMode === 'cards' ? 'bg-white dark:bg-slate-700 text-orange-600 dark:text-orange-400 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                                class="h-full px-3 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                                title="Mode Kartu: Visualisasi alur waktu bimbingan"
                                aria-label="Tampilan Kartu">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span>Kartu</span>
                        </button>
                    </div>

                    @if(in_array(Auth::user()->role, ['admin', 'kaprodi']) && isset($dosens))
                        <form action="{{ route('mentoring-sessions.index') }}" method="GET" class="inline-block">
                            <input type="hidden" name="tab" value="{{ $activeTab }}">
                            <input type="hidden" name="search" value="{{ $search }}">
                            <select name="dosen_id" onchange="this.form.submit()" aria-label="Filter Dosen Pembimbing" class="h-10 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 py-0 px-3 shadow-2xs focus:border-orange-500 focus:ring-orange-500 cursor-pointer">
                                <option value="">Filter Dosen Pembimbing...</option>
                                @foreach($dosens as $d)
                                    <option value="{{ $d->id }}" {{ ($dosenId ?? '') == $d->id ? 'selected' : '' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif

                    <x-search-input 
                        name="search" 
                        :value="$search ?? ''" 
                        placeholder="Cari nama atau topik..." 
                        route="mentoring-sessions.index"
                        :params="['tab' => $activeTab, 'dosen_id' => $dosenId ?? '']" />

                    <!-- Tombol Panduan Penggunaan Menu Bimbingan -->
                    <button type="button" 
                            @click="openGuideModal = true"
                            class="h-10 inline-flex items-center gap-1.5 px-3.5 bg-amber-50 hover:bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:hover:bg-amber-900/60 dark:text-amber-300 border border-amber-200/90 dark:border-amber-800/60 text-xs font-bold rounded-xl transition-all shadow-2xs hover:shadow-xs cursor-pointer whitespace-nowrap group"
                            title="Buka Panduan Penggunaan Jadwal Bimbingan">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Panduan</span>
                    </button>

                    @if(in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']))
                        <a href="{{ route('mentoring-sessions.create') }}" class="h-10 inline-flex items-center gap-1.5 px-4 bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white text-xs font-bold rounded-xl transition-all shadow-sm whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            <span>Tambah Jadwal</span>
                        </a>
                    @endif
                </div>
            </x-slot>

            <div class="p-6">
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

                        // 3. Chronological Timeline Sections for "Per Sesi" view
                        $timelineSections = [];
                        if ($activeTab === 'active') {
                            $todayGroups = collect();
                            $tomorrowGroups = collect();
                            $upcomingGroups = collect();
                            $overdueGroups = collect();
                            $pastCompletedGroups = collect();

                            foreach($sessionsBySessionGroup as $gKey => $sessionItems) {
                                $first = $sessionItems->first();
                                $scheduledAt = $first->scheduled_at;
                                $isPastDay = $scheduledAt->isPast() && !$scheduledAt->isToday();
                                $hasUnfinished = $sessionItems->contains(fn($s) => in_array($s->status, ['pending', 'approved']) || $s->is_absent);

                                if ($scheduledAt->isToday()) {
                                    $todayGroups->put($gKey, $sessionItems);
                                } elseif ($scheduledAt->isTomorrow()) {
                                    $tomorrowGroups->put($gKey, $sessionItems);
                                } elseif ($isPastDay && $hasUnfinished) {
                                    $overdueGroups->put($gKey, $sessionItems);
                                } elseif ($isPastDay) {
                                    $pastCompletedGroups->put($gKey, $sessionItems);
                                } else {
                                    $upcomingGroups->put($gKey, $sessionItems);
                                }
                            }

                            // Sort chronologically
                            $todayGroups = $todayGroups->sortBy(fn($items) => $items->first()->scheduled_at->timestamp);
                            $tomorrowGroups = $tomorrowGroups->sortBy(fn($items) => $items->first()->scheduled_at->timestamp);
                            $upcomingGroups = $upcomingGroups->sortBy(fn($items) => $items->first()->scheduled_at->timestamp);
                            $overdueGroups = $overdueGroups->sortByDesc(fn($items) => $items->first()->scheduled_at->timestamp);
                            $pastCompletedGroups = $pastCompletedGroups->sortByDesc(fn($items) => $items->first()->scheduled_at->timestamp);

                            if ($todayGroups->isNotEmpty()) {
                                $timelineSections[] = [
                                    'id' => 'today',
                                    'title' => 'Hari Ini',
                                    'subtitle' => now()->locale('id')->translatedFormat('l, d F Y'),
                                    'badge' => $todayGroups->count() . ' Sesi',
                                    'badge_class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                    'groups' => $todayGroups,
                                ];
                            }
                            if ($tomorrowGroups->isNotEmpty()) {
                                $timelineSections[] = [
                                    'id' => 'tomorrow',
                                    'title' => 'Besok',
                                    'subtitle' => now()->addDay()->locale('id')->translatedFormat('l, d F Y'),
                                    'badge' => $tomorrowGroups->count() . ' Sesi',
                                    'badge_class' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                    'groups' => $tomorrowGroups,
                                ];
                            }
                            if ($upcomingGroups->isNotEmpty()) {
                                $timelineSections[] = [
                                    'id' => 'upcoming',
                                    'title' => 'Jadwal Mendatang',
                                    'subtitle' => 'Sesi bimbingan yang akan datang',
                                    'badge' => $upcomingGroups->count() . ' Sesi',
                                    'badge_class' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                    'groups' => $upcomingGroups,
                                ];
                            }
                            if ($overdueGroups->isNotEmpty()) {
                                $timelineSections[] = [
                                    'id' => 'overdue',
                                    'title' => 'Perlu Tindakan Dosen (Melewati Jadwal)',
                                    'subtitle' => 'Sesi telah lewat waktu dan perlu diberi catatan atau diselesaikan',
                                    'badge' => $overdueGroups->count() . ' Sesi Perlu Ditinjau',
                                    'badge_class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                    'groups' => $overdueGroups,
                                ];
                            }
                            if ($pastCompletedGroups->isNotEmpty()) {
                                $timelineSections[] = [
                                    'id' => 'past_completed',
                                    'title' => 'Sesi Terdahulu (Selesai)',
                                    'subtitle' => 'Sesi bimbingan sebelumnya yang telah selesai dilaksanakan',
                                    'badge' => $pastCompletedGroups->count() . ' Sesi Selesai',
                                    'badge_class' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                    'groups' => $pastCompletedGroups,
                                ];
                            }
                        } else {
                            // Riwayat Selesai
                            $timelineSections[] = [
                                'id' => 'history',
                                'title' => 'Riwayat Bimbingan Selesai',
                                'subtitle' => 'Seluruh sesi bimbingan mahasiswa yang telah selesai',
                                'badge' => $sessionsBySessionGroup->count() . ' Sesi Selesai',
                                'badge_class' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                'groups' => $sessionsBySessionGroup,
                            ];
                        }
                    @endphp

                @include('mentoring.partials.table-view')

                @include('mentoring.partials.cards-view')
            </div>
        </x-table-card>

        <!-- Modal Panduan Interaktif Penggunaan Jadwal Bimbingan (Solusi 1) -->
        @include('mentoring.partials.guide-modal')

        @include('mentoring.partials.live-attendance-modal')

        @include('mentoring.partials.cancel-modal')

        @include('mentoring.partials.bulk-action-modals')
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const highlightId = urlParams.get('highlight') || window.location.hash.replace('#session-', '');

            if (highlightId) {
                const target = document.getElementById('session-' + highlightId);
                if (target) {
                    setTimeout(() => {
                        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        target.classList.add('ring-4', 'ring-orange-500', 'ring-offset-2', 'dark:ring-offset-slate-900');
                        setTimeout(() => {
                            target.classList.remove('ring-4', 'ring-orange-500', 'ring-offset-2', 'dark:ring-offset-slate-900');
                        }, 4000);
                    }, 400);
                }
            }
        });
    </script>
    @endpush
</x-app-layout>

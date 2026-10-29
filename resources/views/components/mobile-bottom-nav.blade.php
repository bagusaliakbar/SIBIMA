@php
    $user = Auth::user();
    $role = $user->role ?? 'mahasiswa';

    // 1. Dashboard active check
    $isDashboard = request()->routeIs('dashboard');
    $dashboardLabel = 'Dashboard';

    // 2. Bimbingan / Skripsi active check
    if ($role === 'mahasiswa') {
        $bimbinganRoute = route('mentoring.student_index');
        $isBimbingan = request()->routeIs('mentoring.*') || request()->routeIs('mentoring-sessions.*');
        $bimbinganLabel = 'Bimbingan';
    } else {
        $bimbinganRoute = route('theses.index');
        $isBimbingan = request()->routeIs('theses.*') || request()->routeIs('mentoring.*');
        $bimbinganLabel = ($role === 'dosen') ? 'Bimbingan' : 'Skripsi';
    }

    // 3. Seminar / Sidang / Jadwal (Disesuaikan otomatis dengan progres mahasiswa)
    if ($role === 'mahasiswa') {
        $thesis = $user ? \App\Models\Thesis::where('student_id', $user->id)->first() : null;
        $hasPassedSeminar = $thesis ? cache()->remember('u_' . $user->id . '_has_sem', 30, fn() => $thesis->hasPassedSeminar()) : false;

        if ($hasPassedSeminar) {
            // Mahasiswa sudah seminar -> Menu menjadi Sidang
            $jadwalRoute = route('thesis-defense-applications.index');
            $isJadwal = request()->routeIs('thesis-defense-applications.*') || request()->routeIs('student-defense-revisions.*') || request()->routeIs('student-seminar-revisions.*');
            $jadwalLabel = 'Sidang';
            $jadwalType = 'sidang';
        } else {
            // Mahasiswa belum seminar -> Menu tetap Seminar
            $jadwalRoute = route('seminar-applications.index');
            $isJadwal = request()->routeIs('seminar-applications.*') || request()->routeIs('student-seminar-revisions.*');
            $jadwalLabel = 'Seminar';
            $jadwalType = 'seminar';
        }
    } elseif ($role === 'dosen') {
        $jadwalRoute = route('seminar-schedules.index');
        $isJadwal = request()->routeIs('seminar-schedules.*') || request()->routeIs('thesis-defense-schedules.*') || request()->routeIs('seminar-examiner.*') || request()->routeIs('defense-examiner.*');
        $jadwalLabel = 'Jadwal';
        $jadwalType = 'jadwal';
    } else {
        $jadwalRoute = route('seminar-schedules.index');
        $isJadwal = request()->routeIs('seminar-schedules.*') || request()->routeIs('thesis-defense-schedules.*');
        $jadwalLabel = 'Jadwal';
        $jadwalType = 'jadwal';
    }

    // 4. Notifikasi active check
    $notifRoute = route('notifications.index');
    $isNotif = request()->routeIs('notifications.*');
    $unreadCount = $user ? $user->unreadNotifications()->count() : 0;

    // 5. Slot 5: Logbook untuk Mahasiswa, Menu Drawer untuk Dosen/Admin
    if ($role === 'mahasiswa') {
        $slot5Route = route('logbooks.index');
        $isSlot5Active = request()->routeIs('logbooks.*');
        $slot5Label = 'Logbook';
    } else {
        $slot5Route = null;
        $isSlot5Active = false;
        $slot5Label = 'Menu';
    }
@endphp

<nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-950/5 transition-colors duration-300 pb-[max(0.5rem,env(safe-area-inset-bottom))]"
     aria-label="Navigasi Bawah Mobile">
    <div class="h-14 grid grid-cols-5 items-center px-1 max-w-lg mx-auto">
        <!-- 1. Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center justify-center h-full py-1 group transition-transform active:scale-90 {{ $isDashboard ? 'text-orange-600 dark:text-orange-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 font-medium' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isDashboard ? '2.5' : '2' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                @if($isDashboard)
                    <span class="absolute -bottom-1 w-1 h-1 rounded-full bg-orange-600 dark:bg-orange-400"></span>
                @endif
            </div>
            <span class="text-[10px] tracking-tight mt-1">{{ $dashboardLabel }}</span>
        </a>

        <!-- 2. Bimbingan / Skripsi -->
        <a href="{{ $bimbinganRoute }}" 
           class="flex flex-col items-center justify-center h-full py-1 group transition-transform active:scale-90 {{ $isBimbingan ? 'text-orange-600 dark:text-orange-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 font-medium' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isBimbingan ? '2.5' : '2' }}" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                @if($isBimbingan)
                    <span class="absolute -bottom-1 w-1 h-1 rounded-full bg-orange-600 dark:bg-orange-400"></span>
                @endif
            </div>
            <span class="text-[10px] tracking-tight mt-1">{{ $bimbinganLabel }}</span>
        </a>

        <!-- 3. Jadwal / Seminar / Sidang -->
        <a href="{{ $jadwalRoute }}" 
           class="flex flex-col items-center justify-center h-full py-1 group transition-transform active:scale-90 {{ $isJadwal ? 'text-orange-600 dark:text-orange-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 font-medium' }}">
            <div class="relative flex items-center justify-center">
                @if(($jadwalType ?? '') === 'sidang')
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isJadwal ? '2.5' : '2' }}" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isJadwal ? '2.5' : '2' }}" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                @endif
                @if($isJadwal)
                    <span class="absolute -bottom-1 w-1 h-1 rounded-full bg-orange-600 dark:bg-orange-400"></span>
                @endif
            </div>
            <span class="text-[10px] tracking-tight mt-1">{{ $jadwalLabel }}</span>
        </a>

        <!-- 4. Notifikasi (dengan Red Badge Counter) -->
        <a href="{{ $notifRoute }}" 
           class="flex flex-col items-center justify-center h-full py-1 group transition-transform active:scale-90 relative {{ $isNotif ? 'text-orange-600 dark:text-orange-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 font-medium' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isNotif ? '2.5' : '2' }}" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-2 flex h-3.5 min-w-[14px] px-1 items-center justify-center rounded-full bg-rose-600 text-[9px] font-black text-white leading-none shadow-xs">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                @endif
                @if($isNotif)
                    <span class="absolute -bottom-1 w-1 h-1 rounded-full bg-orange-600 dark:bg-orange-400"></span>
                @endif
            </div>
            <span class="text-[10px] tracking-tight mt-1">Notifikasi</span>
        </a>

        <!-- 5. Logbook (Mahasiswa) / Menu Drawer (Dosen/Admin) -->
        @if($role === 'mahasiswa')
            <a href="{{ $slot5Route }}" 
               class="flex flex-col items-center justify-center h-full py-1 group transition-transform active:scale-90 {{ $isSlot5Active ? 'text-orange-600 dark:text-orange-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 font-medium' }}">
                <div class="relative flex items-center justify-center">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isSlot5Active ? '2.5' : '2' }}" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    @if($isSlot5Active)
                        <span class="absolute -bottom-1 w-1 h-1 rounded-full bg-orange-600 dark:bg-orange-400"></span>
                    @endif
                </div>
                <span class="text-[10px] tracking-tight mt-1">{{ $slot5Label }}</span>
            </a>
        @else
            <button type="button" 
                    @click="sidebarOpen = true" 
                    aria-label="Buka menu aplikasi lengkap"
                    class="flex flex-col items-center justify-center h-full py-1 group transition-transform active:scale-90 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 font-medium cursor-pointer">
                <div class="relative flex items-center justify-center">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </div>
                <span class="text-[10px] tracking-tight mt-1">Menu</span>
            </button>
        @endif
    </div>
</nav>

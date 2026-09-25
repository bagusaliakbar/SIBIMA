<x-guest-layout>
    @section('title', 'Masuk Akun')

    <!-- Header Section -->
    <div class="text-center mb-6">
        <!-- Top Official Badge -->
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-orange-50 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200/80 dark:border-orange-800/60 shadow-2xs mb-4">
            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
            <span>Portal Akademik SIBIMA</span>
        </div>

        <!-- Logo UNSUB Presentation -->
        <div class="flex justify-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white dark:bg-slate-800 p-2.5 border border-slate-200/80 dark:border-slate-700/80 shadow-lg shadow-orange-500/10 dark:shadow-none mb-3.5 group hover:scale-105 transition-transform duration-300">
                <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-14 h-14 object-contain">
            </div>
        </div>

        <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
            Selamat Datang Kembali
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium leading-relaxed">
            Sistem Informasi Bimbingan Mahasiswa & Skripsi<br>
            <span class="text-slate-400 dark:text-slate-500 text-[11px] font-semibold">FASILKOM Universitas Subang</span>
        </p>
    </div>

    <!-- Quick Role Hint Chips -->
    <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100/80 dark:bg-slate-800/60 rounded-2xl text-[11px] font-bold text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 mb-5 select-none">
        <div class="flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-xl bg-white dark:bg-slate-700/80 shadow-2xs text-slate-800 dark:text-slate-200">
            <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            <span><strong>Mhs:</strong> Gunakan NPM</span>
        </div>
        <div class="flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-xl bg-white dark:bg-slate-700/80 shadow-2xs text-slate-800 dark:text-slate-200">
            <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span><strong>Dosen:</strong> NIDN / User</span>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
        @csrf

        <!-- Username / NIDN / NPM -->
        <div>
            <label for="username" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Username / NIDN / NPM
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-orange-500 dark:group-focus-within:text-orange-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" 
                    class="block w-full pl-11 pr-4 py-3 bg-slate-50/70 dark:bg-slate-800/60 border border-slate-200/90 dark:border-slate-700/90 rounded-2xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/15 transition-all duration-200" 
                    placeholder="Ketik NPM, NIDN, atau Username">
            </div>
            <x-input-error :messages="$errors->get('username')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Password
                </label>
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-orange-500 dark:group-focus-within:text-orange-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" 
                    class="block w-full pl-11 pr-11 py-3 bg-slate-50/70 dark:bg-slate-800/60 border border-slate-200/90 dark:border-slate-700/90 rounded-2xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/15 transition-all duration-200" 
                    placeholder="••••••••">
                
                <!-- Toggle Password Button -->
                <button type="button" @click="showPassword = !showPassword" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors focus:outline-none cursor-pointer"
                        title="Tampilkan / Sembunyikan Password">
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg x-show="showPassword" x-cloak class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 19c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between text-xs pt-0.5">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input id="remember_me" type="checkbox" name="remember" 
                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500/20 bg-slate-50 dark:bg-slate-800 transition-all cursor-pointer">
                <span class="font-medium text-slate-600 dark:text-slate-400">Ingat saya</span>
            </label>
            
            <span class="text-slate-400 dark:text-slate-500 text-[11px] font-semibold">FASILKOM UNSUB</span>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" 
                class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-orange-500 via-orange-600 to-amber-600 hover:from-orange-600 hover:to-orange-700 text-white text-sm font-bold tracking-wide shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer">
                <span>Masuk ke Dashboard</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>

        <!-- Register Link & Trust Indicators -->
        <div class="text-center pt-4 border-t border-slate-100 dark:border-slate-800/80 mt-6 space-y-3">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 hover:underline">
                    Daftar Sekarang
                </a>
            </p>

            <div class="flex items-center justify-center gap-1.5 text-[11px] font-medium text-slate-400 dark:text-slate-500">
                <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Sistem Terenkripsi & Terintegrasi</span>
            </div>
        </div>
    </form>
    
    <div class="mt-6 text-center text-[11px] font-semibold text-slate-400 dark:text-slate-500">
        <p>&copy; {{ date('Y') }} SIBIMA — FASILKOM UNSUB</p>
    </div>
</x-guest-layout>

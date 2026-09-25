<x-guest-layout>
    @section('title', 'Masuk Akun')

    <!-- Card Brand Header & Theme Switcher (Integrated safely inside card) -->
    <div class="flex items-center justify-between pb-5 mb-5 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-orange-50 dark:bg-orange-950/40 border border-orange-200/70 dark:border-orange-900/40 flex items-center justify-center p-1.5 shadow-xs">
                <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="text-base font-black tracking-tight text-slate-900 dark:text-white leading-tight">
                    SIBIMA <span class="text-orange-600 dark:text-orange-400">FASILKOM</span>
                </h1>
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                    Universitas Subang
                </p>
            </div>
        </div>

        <!-- Integrated Theme Toggle (Clean, no floating, no overlapping) -->
        <button type="button" 
                @click="darkMode = !darkMode" 
                class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700/80 text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200/80 dark:border-slate-700 transition-all cursor-pointer flex items-center gap-1.5 text-xs font-semibold"
                :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
            <svg x-show="darkMode" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            <svg x-show="!darkMode" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>
    </div>

    <!-- Title & Subtitle -->
    <div class="mb-5">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Masuk ke Akun
        </h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
            Masukkan NPM, NIDN, atau Username dan kata sandi Anda.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
        @csrf

        <!-- Username / NPM / NIDN -->
        <div>
            <label for="username" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Username / NPM / NIDN
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" 
                    class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-800 focus:border-orange-500 transition-all duration-200" 
                    placeholder="Masukkan NPM, NIDN, atau Username">
            </div>
            <x-input-error :messages="$errors->get('username')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Password
                </label>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" 
                    class="block w-full pl-10 pr-11 py-2.5 bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-800 focus:border-orange-500 transition-all duration-200" 
                    placeholder="Masukkan password Anda">
                
                <!-- Toggle Password Button -->
                <button type="button" @click="showPassword = !showPassword" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors focus:outline-none cursor-pointer"
                        title="Tampilkan / Sembunyikan Password">
                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg x-show="showPassword" x-cloak class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 19c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between text-xs pt-0.5">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input id="remember_me" type="checkbox" name="remember" 
                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500/20 bg-slate-50 dark:bg-slate-800 transition-all cursor-pointer">
                <span class="font-medium text-slate-600 dark:text-slate-400">Ingat saya</span>
            </label>
        </div>

        <!-- Submit Button (Solid Metronic Primary Button) -->
        <div class="pt-2">
            <button type="submit" 
                class="w-full py-3 px-6 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white text-sm font-bold tracking-wide shadow-md shadow-orange-600/20 hover:shadow-orange-600/30 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <span>Masuk ke Dashboard</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>

        <!-- Footer inside Card: Register link -->
        <div class="pt-4 mt-5 border-t border-slate-100 dark:border-slate-800 text-center">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 transition-colors">
                    Daftar Akun Baru
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

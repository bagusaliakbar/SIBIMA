<x-guest-layout>
    @section('title', 'Masuk Akun')

    <!-- Clean Centered Brand Header -->
    <div class="text-center mb-6 sm:mb-8">
        <div class="inline-flex items-center justify-center mb-3">
            <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-xs">
        </div>
        <h1 class="auth-title text-2xl sm:text-3xl font-black tracking-tight">
            SIBIMA
        </h1>
        <p class="auth-subtitle text-xs sm:text-sm font-semibold mt-1">
            Fakultas Ilmu Komputer &bull; Universitas Subang
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5" x-data="{ showPassword: false }">
        @csrf

        <!-- Username / NPM / NIDN -->
        <div>
            <label for="username" class="auth-label block text-xs sm:text-sm font-bold uppercase tracking-wider mb-2">
                Username / NPM / NIDN
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" 
                    class="auth-input block w-full pl-11 pr-4 py-3 sm:py-3.5 rounded-xl text-sm sm:text-base font-medium transition-all duration-200" 
                    placeholder="Masukkan NPM, NIDN, atau Username">
            </div>
            <x-input-error :messages="$errors->get('username')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label for="password" class="auth-label block text-xs sm:text-sm font-bold uppercase tracking-wider">
                    Password
                </label>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" 
                    class="auth-input block w-full pl-11 pr-12 py-3 sm:py-3.5 rounded-xl text-sm sm:text-base font-medium transition-all duration-200" 
                    placeholder="Masukkan password Anda">
                
                <!-- Toggle Password Button -->
                <button type="button" @click="showPassword = !showPassword" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors focus:outline-none cursor-pointer"
                        title="Tampilkan / Sembunyikan Password">
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between text-xs sm:text-sm pt-0.5">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input id="remember_me" type="checkbox" name="remember" 
                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-orange-600 focus:ring-orange-500/20 bg-slate-50 dark:bg-slate-800 transition-all cursor-pointer">
                <span class="auth-label font-medium">Ingat saya</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2 sm:pt-3">
            <button type="submit" 
                class="w-full py-3.5 sm:py-4 px-6 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white text-sm sm:text-base font-bold tracking-wide shadow-lg shadow-orange-600/25 hover:shadow-orange-600/35 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <span>Masuk ke Dashboard</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>

        <!-- Card Footer -->
        <div class="auth-footer-border pt-5 sm:pt-6 mt-6 sm:mt-7 border-t text-center">
            <p class="auth-footer-text text-xs sm:text-sm font-medium">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 transition-colors">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

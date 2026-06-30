<x-guest-layout>
    <div class="text-center mb-8">
        <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-16 h-16 object-contain mx-auto mb-4">
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">SIBIMA-FASILKOM</h2>
        <p class="text-sm text-slate-500 mt-2">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2.5 transition-colors placeholder-slate-400" placeholder="admin@sibima.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center">
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-orange-600 hover:text-orange-500">Lupa password?</a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2.5 transition-colors placeholder-slate-400" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-orange-600 shadow-sm focus:ring-orange-500" name="remember">
                <span class="ml-2 text-sm text-slate-600">Ingat saya</span>
            </label>
        </div>

        <div>
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded text-sm font-semibold text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-sm transition-colors">
                Masuk ke Dasboard
            </button>
        </div>

        <div class="relative py-2">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="px-2 bg-white text-slate-500 uppercase tracking-wider font-medium">Atau</span>
            </div>
        </div>

        <div>
            <a href="{{ route('register') }}" class="w-full flex justify-center py-2.5 px-4 border border-slate-300 rounded text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-sm transition-colors">
                Daftar Akun Baru
            </a>
        </div>
    </form>
    
    <div class="mt-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} SIBIMA-FASILKOM</p>
    </div>
</x-guest-layout>

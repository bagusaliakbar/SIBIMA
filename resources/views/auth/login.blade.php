<x-guest-layout>
    <div class="text-center mb-8">
        <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-16 h-16 object-contain mx-auto mb-4">
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Sistem Informasi Bimbingan Mahasiswa</h2>
        <p class="text-sm text-slate-500 mt-2">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Username / NIDN / NPM -->
        <div>
            <label for="username" class="block text-sm font-medium text-slate-700">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" 
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2.5 transition-colors placeholder-slate-400" placeholder="Masukkan Username / NIDN / NPM">
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
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

        <div>
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded text-sm font-semibold text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-sm transition-colors">
                Masuk ke Dasboard
            </button>
        </div>


    </form>
    
    <div class="mt-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} SIBIMA-FASILKOM</p>
    </div>
</x-guest-layout>

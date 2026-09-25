<x-guest-layout>
    @section('title', 'Pendaftaran Akun')

    <!-- Clean Centered Brand Header -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center mb-2.5">
            <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-14 h-14 object-contain drop-shadow-xs">
        </div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            SIBIMA
        </h1>
        <p class="text-xs font-semibold text-slate-400 dark:text-slate-400 mt-1">
            Fakultas Ilmu Komputer &bull; Universitas Subang
        </p>
        <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-950/40 border border-orange-200/60 dark:border-orange-900/50 text-xs font-bold text-orange-600 dark:text-orange-400">
            Pendaftaran Akun Baru
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ role: '{{ old('role', 'mahasiswa') }}', showPassword: false, showPasswordConfirm: false }">
        @csrf

        <!-- Pilihan Role -->
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Daftar Sebagai</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative flex items-center justify-center py-3 px-4 rounded-xl border cursor-pointer transition-all duration-200 select-none"
                       :class="role === 'mahasiswa' ? 'bg-orange-50/80 dark:bg-orange-950/30 border-orange-500 text-orange-700 dark:text-orange-400 ring-2 ring-orange-500/20 font-bold shadow-xs' : 'bg-slate-50/70 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 font-medium'">
                    <input type="radio" name="role" value="mahasiswa" x-model="role" class="sr-only">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        <span class="text-sm">Mahasiswa</span>
                    </div>
                </label>
                <label class="relative flex items-center justify-center py-3 px-4 rounded-xl border cursor-pointer transition-all duration-200 select-none"
                       :class="role === 'dosen' ? 'bg-orange-50/80 dark:bg-orange-950/30 border-orange-500 text-orange-700 dark:text-orange-400 ring-2 ring-orange-500/20 font-bold shadow-xs' : 'bg-slate-50/70 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 font-medium'">
                    <input type="radio" name="role" value="dosen" x-model="role" class="sr-only">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="text-sm">Dosen</span>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-1" />
        </div>

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-orange-500 transition-all duration-200"
                   placeholder="Masukkan nama lengkap beserta gelar">
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Identitas (NPM / NIDN) -->
            <div>
                <label for="identifier" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5" x-text="role === 'mahasiswa' ? 'NPM' : 'NIDN / NIP'"></label>
                <input id="identifier" type="text" name="identifier" value="{{ old('identifier') }}" required
                       class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-orange-500 transition-all duration-200"
                       :placeholder="role === 'mahasiswa' ? '211010001' : '0412345678'">
                <x-input-error :messages="$errors->get('identifier')" class="mt-1" />
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Username <span class="text-[10px] font-normal text-slate-400 normal-case">(Opsional)</span></label>
                <input id="username" type="text" name="username" value="{{ old('username') }}"
                       class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-orange-500 transition-all duration-200"
                       placeholder="Sama dengan NPM/NIDN jika kosong">
                <x-input-error :messages="$errors->get('username')" class="mt-1" />
            </div>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-orange-500 transition-all duration-200"
                   placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Tahun Angkatan (Khusus Mahasiswa) -->
            <div x-show="role === 'mahasiswa'" x-transition>
                <label for="entry_year" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tahun Angkatan</label>
                <select id="entry_year" name="entry_year" :required="role === 'mahasiswa'"
                        class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-orange-500 transition-all duration-200">
                    <option value="">Pilih Angkatan</option>
                    @for($y = date('Y'); $y >= 2018; $y--)
                        <option value="{{ $y }}" {{ old('entry_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <x-input-error :messages="$errors->get('entry_year')" class="mt-1" />
            </div>

            <!-- Nomor WhatsApp -->
            <div :class="role === 'mahasiswa' ? '' : 'sm:col-span-2'">
                <label for="phone_number" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">No. WhatsApp / HP</label>
                <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}"
                       class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-orange-500 transition-all duration-200"
                       placeholder="081234567890">
                <x-input-error :messages="$errors->get('phone_number')" class="mt-1" />
            </div>
        </div>

        <!-- Password Fields -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
                <div class="relative rounded-xl">
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password"
                           class="block w-full pl-4 pr-11 py-3 bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-orange-500 transition-all duration-200"
                           placeholder="••••••••">
                    <button type="button" @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors focus:outline-none cursor-pointer"
                            title="Tampilkan / Sembunyikan Password">
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPassword" x-cloak class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Konfirmasi</label>
                <div class="relative rounded-xl">
                    <input id="password_confirmation" :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                           class="block w-full pl-4 pr-11 py-3 bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-orange-500 transition-all duration-200"
                           placeholder="••••••••">
                    <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors focus:outline-none cursor-pointer"
                            title="Tampilkan / Sembunyikan Password">
                        <svg x-show="!showPasswordConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPasswordConfirm" x-cloak class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" 
                class="w-full py-3.5 px-6 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white text-sm font-bold tracking-wide shadow-sm hover:shadow transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <span>Daftar Akun Baru</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>

        <div class="text-center pt-3 border-t border-slate-100 dark:border-slate-800 mt-5">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300">
                    Masuk ke Dashboard
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

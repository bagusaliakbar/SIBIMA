<x-guest-layout>
    @section('title', 'Pendaftaran Akun')

    <div class="text-center mb-6">
        <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-16 h-16 object-contain mx-auto mb-3">
        <h2 class="text-xl font-bold text-slate-800 tracking-tight">Pendaftaran Akun Baru</h2>
        <p class="text-xs text-slate-500 mt-1">Lengkapi data diri Anda untuk mendaftar di SIBIMA</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ role: '{{ old('role', 'mahasiswa') }}' }">
        @csrf

        <!-- Pilihan Role -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Daftar Sebagai</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative flex items-center justify-center p-3 rounded-lg border cursor-pointer transition-all"
                       :class="role === 'mahasiswa' ? 'bg-orange-50 border-orange-500 text-orange-700 ring-2 ring-orange-500/20 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                    <input type="radio" name="role" value="mahasiswa" x-model="role" class="sr-only">
                    <span class="text-sm">Mahasiswa</span>
                </label>
                <label class="relative flex items-center justify-center p-3 rounded-lg border cursor-pointer transition-all"
                       :class="role === 'dosen' ? 'bg-orange-50 border-orange-500 text-orange-700 ring-2 ring-orange-500/20 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                    <input type="radio" name="role" value="dosen" x-model="role" class="sr-only">
                    <span class="text-sm">Dosen</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-1" />
        </div>

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 placeholder-slate-400"
                   placeholder="Masukkan nama lengkap beserta gelar">
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Identitas (NPM / NIDN) -->
            <div>
                <label for="identifier" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1" x-text="role === 'mahasiswa' ? 'NPM (Nomor Pokok Mahasiswa)' : 'NIDN / NIP'"></label>
                <input id="identifier" type="text" name="identifier" value="{{ old('identifier') }}" required
                       class="block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 placeholder-slate-400"
                       :placeholder="role === 'mahasiswa' ? 'Contoh: 211010001' : 'Contoh: 0412345678'">
                <x-input-error :messages="$errors->get('identifier')" class="mt-1" />
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Username <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
                <input id="username" type="text" name="username" value="{{ old('username') }}"
                       class="block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 placeholder-slate-400"
                       placeholder="Disamakan dengan NPM/NIDN jika kosong">
                <x-input-error :messages="$errors->get('username')" class="mt-1" />
            </div>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 placeholder-slate-400"
                   placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Tahun Angkatan (Khusus Mahasiswa) -->
            <div x-show="role === 'mahasiswa'" x-transition>
                <label for="entry_year" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tahun Angkatan</label>
                <select id="entry_year" name="entry_year" :required="role === 'mahasiswa'"
                        class="block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2">
                    <option value="">Pilih Angkatan</option>
                    @for($y = date('Y'); $y >= 2018; $y--)
                        <option value="{{ $y }}" {{ old('entry_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <x-input-error :messages="$errors->get('entry_year')" class="mt-1" />
            </div>

            <!-- Nomor WhatsApp -->
            <div :class="role === 'mahasiswa' ? '' : 'sm:col-span-2'">
                <label for="phone_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}"
                       class="block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 placeholder-slate-400"
                       placeholder="081234567890">
                <x-input-error :messages="$errors->get('phone_number')" class="mt-1" />
            </div>
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       class="block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 placeholder-slate-400"
                       placeholder="••••••••">
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="block w-full rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 placeholder-slate-400"
                       placeholder="••••••••">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded text-sm font-semibold text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-sm transition-colors">
                Daftar Akun Baru
            </button>
        </div>

        <div class="text-center pt-2">
            <p class="text-xs text-slate-600">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="font-semibold text-orange-600 hover:text-orange-700 hover:underline">
                    Masuk ke Dashboard
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

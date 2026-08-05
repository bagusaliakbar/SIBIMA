<x-guest-layout :fullWidth="true">
    @section('title', 'Pendaftaran Akun')

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 bg-white dark:bg-slate-900">
        
        <!-- Left Side: Branding & Artwork -->
        <div class="hidden lg:flex lg:col-span-5 xl:col-span-5 bg-slate-900 text-white p-12 xl:p-16 flex-col justify-between overflow-hidden relative border-r border-slate-800">
            <!-- Glowing Blur Background Orbs -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Header Logo & Campus Name -->
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-xl p-2.5 border border-white/20 shadow-xl flex items-center justify-center">
                    <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-full h-full object-contain">
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-orange-400">FAKULTAS ILMU KOMPUTER</h3>
                    <p class="text-sm font-bold text-slate-300">UNIVERSITAS SUBANG</p>
                </div>
            </div>

            <!-- Hero Branding Content -->
            <div class="relative z-10 space-y-6 my-auto py-12 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-orange-500/10 border border-orange-500/30 rounded-full text-xs font-bold text-orange-400">
                    <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                    Registrasi Akun SIBIMA
                </div>

                <h1 class="text-3xl xl:text-4xl font-black tracking-tight text-white leading-tight">
                    Bergabung dengan Layanan Skripsi Digital.
                </h1>

                <p class="text-slate-300 text-sm leading-relaxed">
                    Daftarkan akun Mahasiswa atau Dosen Anda untuk dapat mengelola proposal, bimbingan berkala, hingga ujian seminar & sidang.
                </p>
            </div>

            <!-- Footer Branding -->
            <div class="relative z-10 text-xs font-medium text-slate-400 flex items-center justify-between border-t border-slate-800 pt-6">
                <span>&copy; {{ date('Y') }} SIBIMA FASILKOM UNSUB</span>
                <span class="text-slate-400 font-mono">v2.5</span>
            </div>
        </div>

        <!-- Right Side: Form Register -->
        <div class="lg:col-span-7 xl:col-span-7 flex flex-col justify-between p-6 sm:p-10 lg:p-14 bg-white dark:bg-slate-900 min-h-screen overflow-y-auto">
            
            <div class="w-full max-w-xl mx-auto my-auto space-y-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                        Pendaftaran Akun Baru 📝
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Lengkapi data diri Anda dengan benar di bawah ini.
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ role: '{{ old('role', 'mahasiswa') }}' }">
                    @csrf

                    <!-- Pilihan Role -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Daftar Sebagai</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center justify-center p-3.5 rounded-2xl border cursor-pointer transition-all"
                                   :class="role === 'mahasiswa' ? 'bg-orange-50 dark:bg-orange-950/40 border-orange-500 text-orange-700 dark:text-orange-400 ring-2 ring-orange-500/20 font-bold' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                <input type="radio" name="role" value="mahasiswa" x-model="role" class="sr-only">
                                <span class="text-sm">👨‍🎓 Mahasiswa</span>
                            </label>
                            <label class="relative flex items-center justify-center p-3.5 rounded-2xl border cursor-pointer transition-all"
                                   :class="role === 'dosen' ? 'bg-orange-50 dark:bg-orange-950/40 border-orange-500 text-orange-700 dark:text-orange-400 ring-2 ring-orange-500/20 font-bold' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                <input type="radio" name="role" value="dosen" x-model="role" class="sr-only">
                                <span class="text-sm">👨‍🏫 Dosen</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm py-3 px-4 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 placeholder-slate-400"
                               placeholder="Masukkan nama lengkap beserta gelar">
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Identitas (NPM / NIDN) -->
                        <div>
                            <label for="identifier" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1" x-text="role === 'mahasiswa' ? 'NPM (Nomor Pokok Mahasiswa)' : 'NIDN / NIP'"></label>
                            <input id="identifier" type="text" name="identifier" value="{{ old('identifier') }}" required
                                   class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm py-3 px-4 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 placeholder-slate-400"
                                   :placeholder="role === 'mahasiswa' ? 'Contoh: 211010001' : 'Contoh: 0412345678'">
                            <x-input-error :messages="$errors->get('identifier')" class="mt-1" />
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Username <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
                            <input id="username" type="text" name="username" value="{{ old('username') }}"
                                   class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm py-3 px-4 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 placeholder-slate-400"
                                   placeholder="Disamakan dengan NPM/NIDN jika kosong">
                            <x-input-error :messages="$errors->get('username')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Alamat Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm py-3 px-4 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 placeholder-slate-400"
                               placeholder="nama@email.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Tahun Angkatan (Khusus Mahasiswa) -->
                        <div x-show="role === 'mahasiswa'" x-transition>
                            <label for="entry_year" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Tahun Angkatan</label>
                            <select id="entry_year" name="entry_year" :required="role === 'mahasiswa'"
                                    class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm py-3 px-4 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
                                <option value="">Pilih Angkatan</option>
                                @for($y = date('Y'); $y >= 2018; $y--)
                                    <option value="{{ $y }}" {{ old('entry_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <x-input-error :messages="$errors->get('entry_year')" class="mt-1" />
                        </div>

                        <!-- Nomor WhatsApp -->
                        <div :class="role === 'mahasiswa' ? '' : 'sm:col-span-2'">
                            <label for="phone_number" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}"
                                   class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm py-3 px-4 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 placeholder-slate-400"
                                   placeholder="081234567890">
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                   class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm py-3 px-4 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 placeholder-slate-400"
                                   placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Konfirmasi Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                   class="block w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm py-3 px-4 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 placeholder-slate-400"
                                   placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full flex justify-center items-center gap-2 py-3.5 px-6 rounded-2xl text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 shadow-lg shadow-orange-600/30 transition-all">
                            <span>Daftar Akun Baru</span>
                        </button>
                    </div>

                    <div class="text-center pt-2">
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Sudah memiliki akun? 
                            <a href="{{ route('login') }}" class="font-extrabold text-orange-600 hover:underline">
                                Masuk ke Dashboard
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-guest-layout>

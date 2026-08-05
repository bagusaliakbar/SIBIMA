<x-guest-layout :fullWidth="true">
    @section('title', 'Login')

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 bg-white dark:bg-slate-900">
        
        <!-- Left Side: Branding & Artwork (Visible on LG screens) -->
        <div class="hidden lg:flex lg:col-span-6 xl:col-span-7 bg-slate-900 text-white p-12 xl:p-16 flex-col justify-between overflow-hidden relative border-r border-slate-800">
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
                    Sistem Informasi Bimbingan Mahasiswa
                </div>

                <h1 class="text-4xl xl:text-5xl font-black tracking-tight text-white leading-tight">
                    Kelola Bimbingan & Sidang Skripsi dalam Satu Portal.
                </h1>

                <p class="text-slate-300 text-sm xl:text-base leading-relaxed">
                    SIBIMA mempermudah alur pengajuan judul, bimbingan rutin, validasi dokumen, hingga penerbitan jadwal & Berita Acara Sidang secara digital dan terintegrasi.
                </p>

                <!-- Feature Badges -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4">
                    <div class="p-3.5 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10 space-y-1">
                        <div class="text-orange-400 font-black text-sm">⚡ Real-time</div>
                        <div class="text-[11px] text-slate-300">Bimbingan & Logbook</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10 space-y-1">
                        <div class="text-emerald-400 font-black text-sm">📱 WhatsApp</div>
                        <div class="text-[11px] text-slate-300">Fonnte Gateway</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10 space-y-1">
                        <div class="text-indigo-400 font-black text-sm">🎓 E-Berita Acara</div>
                        <div class="text-[11px] text-slate-300">Tanda Tangan Digital</div>
                    </div>
                </div>
            </div>

            <!-- Footer Branding -->
            <div class="relative z-10 text-xs font-medium text-slate-400 flex items-center justify-between border-t border-slate-800 pt-6">
                <span>&copy; {{ date('Y') }} SIBIMA FASILKOM UNSUB</span>
                <span class="text-slate-400 font-mono">v2.5 Release</span>
            </div>
        </div>

        <!-- Right Side: Form Login -->
        <div class="lg:col-span-6 xl:col-span-5 flex flex-col justify-between p-6 sm:p-12 lg:p-16 bg-white dark:bg-slate-900 min-h-screen">
            
            <!-- Mobile Header Logo (Visible on Small Screens) -->
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-12 h-12 object-contain">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-orange-600 dark:text-orange-400">SIBIMA FASILKOM</h3>
                    <p class="text-xs text-slate-500">Universitas Subang</p>
                </div>
            </div>

            <div class="w-full max-w-md mx-auto my-auto space-y-8">
                <!-- Title Greeting -->
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                        Selamat Datang Kembali 👋
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2">
                        Silakan masukkan kredensial Anda untuk mengakses dashboard SIBIMA.
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Form Login -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ showPassword: false }">
                    @csrf

                    <!-- Username / NIDN / NPM -->
                    <div class="space-y-2">
                        <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Username / NIDN / NPM
                        </label>
                        <div class="relative rounded-2xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input id="username" 
                                   type="text" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username" 
                                   class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all font-medium" 
                                   placeholder="Masukkan Username / NIDN / NPM">
                        </div>
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <!-- Password with Eye Toggle -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Kata Sandi (Password)
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 dark:text-orange-400 hover:underline">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                        <div class="relative rounded-2xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input id="password" 
                                   :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   class="block w-full pl-11 pr-12 py-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all font-medium" 
                                   placeholder="••••••••">
                            
                            <!-- Toggle Show/Hide Password Eye Button -->
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded-lg border-slate-300 text-orange-600 shadow-sm focus:ring-orange-500 dark:bg-slate-800 dark:border-slate-700">
                            <span class="ml-2 text-xs font-semibold text-slate-600 dark:text-slate-400">Ingat Saya di Perangkat Ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center items-center gap-2 py-4 px-6 rounded-2xl text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-4 focus:ring-orange-500/20 shadow-lg shadow-orange-600/30 hover:shadow-orange-600/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                            <span>Masuk ke Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>

                <!-- Footer Links -->
                <div class="text-center pt-4 border-t border-slate-100 dark:border-slate-800">
                    <p class="text-xs text-slate-600 dark:text-slate-400">
                        Belum memiliki akun SIBIMA? 
                        <a href="{{ route('register') }}" class="font-extrabold text-orange-600 hover:text-orange-700 dark:text-orange-400 hover:underline">
                            Daftar Akun Baru
                        </a>
                    </p>
                </div>
            </div>

            <!-- Mobile Footer -->
            <div class="lg:hidden text-center text-xs text-slate-400 pt-6">
                <p>&copy; {{ date('Y') }} SIBIMA FASILKOM UNSUB</p>
            </div>
        </div>

    </div>
</x-guest-layout>

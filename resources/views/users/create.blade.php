<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Manajemen Pengguna', 'route' => route('users.index')],
            ['label' => 'Tambah Baru', 'route' => null]
        ]" />
        <div class="flex items-center space-x-3">
            <a href="{{ route('users.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-orange-100 dark:hover:bg-orange-900/40 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight">
                {{ __('Tambah Pengguna Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="w-full mx-auto">
        <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm border border-slate-100 dark:border-slate-700 rounded-lg">
            <div class="p-6 sm:p-8">
                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Foto Profil -->
                        <div class="md:col-span-2 mb-4" x-data="{ photoName: null, photoPreview: null }">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Foto Profil</label>
                            
                            <div class="flex items-center space-x-5">
                                <!-- Preview -->
                                <div class="shrink-0">
                                    <template x-if="! photoPreview">
                                        <div class="w-20 h-20 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center border-2 border-slate-200 dark:border-slate-600 shadow-inner">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                    </template>
                                    <template x-if="photoPreview">
                                        <span class="block rounded-xl h-20 w-20 bg-cover bg-no-repeat bg-center border-2 border-orange-500 shadow-md"
                                              x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                        </span>
                                    </template>
                                </div>

                                <div>
                                    <input type="file" id="avatar" name="avatar" class="hidden" x-ref="avatar"
                                           x-on:change="
                                                photoName = $refs.avatar.files[0].name;
                                                const reader = new FileReader();
                                                reader.onload = (e) => {
                                                    photoPreview = e.target.result;
                                                };
                                                reader.readAsDataURL($refs.avatar.files[0]);
                                           " />
                                    <button type="button" x-on:click.prevent="$refs.avatar.click()" class="inline-flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-black text-[10px] text-slate-600 dark:text-slate-400 uppercase tracking-widest hover:bg-slate-100 transition-all shadow-sm">
                                        Pilih Foto
                                    </button>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Format: JPG, PNG, JPEG. Max 2MB.</p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                        </div>

                        <!-- Nama -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus 
                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 transition-colors" placeholder="Cth: Dr. Budi Santoso, M.Kom">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Peran Pengguna</label>
                            <select id="role" name="role" required class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 transition-colors">
                                <option value="">Pilih peran...</option>
                                <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen Pembimbing</option>
                                <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="kaprodi" {{ old('role') == 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Identifier (NPM/NIDN) -->
                        <div>
                            <label for="identifier" class="block text-sm font-medium text-slate-700 dark:text-slate-300">NPM / NIDN</label>
                            <input id="identifier" type="text" name="identifier" value="{{ old('identifier') }}" required 
                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 font-mono transition-colors" placeholder="Masukkan nomor identitas">
                            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
                        </div>

                        <!-- Tahun Angkatan (Khusus Mahasiswa) -->
                        <div>
                            <label for="entry_year" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tahun Angkatan <span class="text-[10px] text-slate-400 font-normal italic">(Khusus Mahasiswa)</span></label>
                            <input id="entry_year" type="number" name="entry_year" value="{{ old('entry_year') }}" 
                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 transition-colors" placeholder="Cth: 2020">
                            <p class="mt-1 text-[10px] text-slate-500">Digunakan untuk monitoring masa studi kritikal.</p>
                            <x-input-error :messages="$errors->get('entry_year')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Alamat Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 transition-colors" placeholder="nama@unsub.ac.id">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nomor WhatsApp</label>
                            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" 
                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 transition-colors" placeholder="Cth: 08123456789">
                            <p class="mt-1 text-[10px] text-slate-500 italic">Digunakan untuk pengiriman pengingat jadwal otomatis.</p>
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="md:col-span-2" x-data="{ showPassword: false }">
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                            <div class="relative mt-1">
                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required 
                                    class="block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 pr-10 transition-colors" placeholder="Minimal 8 karakter">
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pengguna dapat mengubah password ini nanti setelah login.</p>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-3">
                        <a href="{{ route('users.index') }}" class="px-6 py-2.5 text-[10px] font-black text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 uppercase tracking-widest transition-colors">Batal</a>
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-orange-500/20">
                            Simpan Pengguna Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

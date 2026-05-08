<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('users.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-orange-100 dark:hover:bg-orange-900/40 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight">
                {{ __('Edit Pengguna') }} - {{ $user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="w-full mx-auto">
        <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm border border-slate-100 dark:border-slate-700 rounded-lg">
            <div class="p-6 sm:p-8">
                <form method="POST" action="{{ route('users.update', $user->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus 
                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 transition-colors">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Peran Pengguna</label>
                            <select id="role" name="role" required class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 transition-colors">
                                <option value="">Pilih peran...</option>
                                <option value="dosen" {{ old('role', $user->role) == 'dosen' ? 'selected' : '' }}>Dosen Pembimbing</option>
                                <option value="mahasiswa" {{ old('role', $user->role) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Identifier (NPM/NIDN) -->
                        <div>
                            <label for="identifier" class="block text-sm font-medium text-slate-700 dark:text-slate-300">NPM / NIDN</label>
                            <input id="identifier" type="text" name="identifier" value="{{ old('identifier', $user->identifier) }}" required 
                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 font-mono transition-colors">
                            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Alamat Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required 
                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 transition-colors">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="md:col-span-2">
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password Baru (Opsional)</label>
                            <input id="password" type="password" name="password" 
                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-2 transition-colors" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Isi hanya jika Anda ingin mereset password pengguna ini.</p>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end">
                        <a href="{{ route('users.index') }}" class="text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 mr-4 transition-colors">Batal</a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            Perbarui Data Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

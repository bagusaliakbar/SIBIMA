<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-slate-800 dark:text-slate-100 leading-tight uppercase tracking-widest">
            {{ __('Pengaturan Nomor Surat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($settings as $setting)
                    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg mr-4">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $setting->title }}</h3>
                                        <span class="text-xs font-medium text-slate-400 uppercase tracking-widest">{{ $setting->type }}</span>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('admin.letter-settings.update', $setting) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="title_{{ $setting->id }}" :value="__('Judul Dokumen')" />
                                        <x-text-input id="title_{{ $setting->id }}" name="title" type="text" class="mt-1 block w-full" :value="old('title', $setting->title)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                    </div>

                                    <div>
                                        <x-input-label for="format_{{ $setting->id }}" :value="__('Format Nomor Surat')" />
                                        <x-text-input id="format_{{ $setting->id }}" name="format" type="text" class="mt-1 block w-full font-mono text-sm" :value="old('format', $setting->format)" required />
                                        <p class="mt-1 text-[10px] text-slate-500 italic">
                                            Placeholder: [NUMBER], [MONTH], [ROMAN_MONTH], [YEAR]
                                        </p>
                                        <x-input-error class="mt-2" :messages="$errors->get('format')" />
                                    </div>

                                    <div>
                                        <x-input-label for="last_number_{{ $setting->id }}" :value="__('Nomor Terakhir')" />
                                        <x-text-input id="last_number_{{ $setting->id }}" name="last_number" type="number" class="mt-1 block w-full" :value="old('last_number', $setting->last_number)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('last_number')" />
                                    </div>

                                    <div class="pt-4">
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-slate-800 dark:bg-slate-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-slate-800 uppercase tracking-widest hover:bg-slate-700 dark:hover:bg-white focus:bg-slate-700 dark:focus:bg-white active:bg-slate-900 dark:active:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                            {{ __('Simpan Perubahan') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <x-empty-state 
                            icon="documents" 
                            title="Belum Ada Pengaturan" 
                            description="Jalankan seeder LetterSettingSeeder untuk memunculkan data default." />
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

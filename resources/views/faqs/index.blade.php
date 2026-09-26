@auth
    <x-app-layout>
        <x-slot name="header">
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-orange-100 dark:hover:bg-orange-900/40 hover:text-orange-600 dark:hover:text-orange-400 transition-colors" title="Kembali ke Dashboard">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                    Pusat Bantuan & Tanya Jawab (FAQ)
                </h2>
            </div>
            <div class="hidden md:block mt-3 sm:mt-0 text-sm text-slate-500 dark:text-slate-400 ml-11">
                Panduan lengkap dan jawaban pertanyaan seputar bimbingan akademik dan skripsi.
            </div>
        </x-slot>

        <div class="w-full">
            @include('faqs.content')
        </div>
    </x-app-layout>
@else
    <x-guest-layout>
        <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Halaman Login
                </a>
                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">SIBIMA FASILKOM</span>
            </div>
            @include('faqs.content')
        </div>
    </x-guest-layout>
@endauth

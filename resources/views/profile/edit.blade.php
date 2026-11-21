<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight">
            {{ __('Pengaturan Profil') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full mx-auto space-y-6">
            <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 sm:rounded-lg">
                @include('profile.partials.mentoring-id-card')
            </div>

            <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 sm:rounded-lg">
                <div class="max-w-2xl">
                    @include('profile.partials.update-signature-form')
                </div>
            </div>

            @if(session()->has(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_ID))
                <div class="p-6 sm:p-8 bg-amber-50/80 dark:bg-amber-950/30 shadow-sm border border-amber-200 dark:border-amber-800/60 sm:rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">Proteksi Kredensial Mode Login As</h3>
                            <p class="mt-1 text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                                Perubahan password dan penghapusan akun dinonaktifkan sementara selama mode <strong>Login As</strong> aktif untuk menjaga keamanan akun pengguna. Gunakan menu Manajemen Pengguna pada akun Admin/Kaprodi untuk mereset password jika diperlukan.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

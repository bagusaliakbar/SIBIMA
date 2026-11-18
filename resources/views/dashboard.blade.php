{{-- Modular Dashboard View --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb />
                <h2 class="font-black text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Dashboard
                </h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest flex items-center">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                    Sistem Informasi Bimbingan Mahasiswa
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ dashboardLoaded: false }" x-init="$nextTick(() => { setTimeout(() => dashboardLoaded = true, 150) })">
        @include('dashboard.partials.banners')

        @if(Auth::user()->role === 'mahasiswa')
            @include('dashboard.partials.student-overview')
        @elseif(Auth::user()->role === 'dosen')
            @include('dashboard.partials.lecturer-overview')
        @else
            @include('dashboard.partials.admin-overview')
        @endif

        @include('dashboard.partials.student-schedules')

        @include('dashboard.partials.activity-feed')
    </div>

    @include('dashboard.partials.scripts-and-modals')
</x-app-layout>

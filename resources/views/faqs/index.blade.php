@auth
    <x-app-layout>
        <x-slot name="header">
            <x-breadcrumb :items="[
                ['label' => 'Pusat Bantuan & FAQ', 'route' => null]
            ]" />
        </x-slot>

        <div class="w-full max-w-5xl mx-auto py-2">
            @include('faqs.content')
        </div>
    </x-app-layout>
@else
    <x-guest-layout :card="false">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            @include('faqs.content')
        </div>
    </x-guest-layout>
@endauth

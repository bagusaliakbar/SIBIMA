<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div x-data="{ photoName: null, photoPreview: null }">
            <x-input-label for="avatar" :value="__('Foto Profil')" />
            
            <!-- Current Profile Photo -->
            <div class="mt-2" x-show="! photoPreview">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-xl h-20 w-20 object-cover border-2 border-orange-100 dark:border-slate-700 shadow-sm">
            </div>

            <!-- New Profile Photo Preview -->
            <div class="mt-2" x-show="photoPreview" style="display: none;">
                <span class="block rounded-xl h-20 w-20 bg-cover bg-no-repeat bg-center border-2 border-orange-500 shadow-md"
                      x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                </span>
            </div>

            <input type="file" id="avatar" name="avatar" class="hidden"
                   x-ref="avatar"
                   x-on:change="
                        photoName = $refs.avatar.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            photoPreview = e.target.result;
                        };
                        reader.readAsDataURL($refs.avatar.files[0]);
                   " />

            <x-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.avatar.click()">
                {{ __('Pilih Foto Baru') }}
            </x-secondary-button>
            <p class="mt-2 text-[10px] text-slate-500 italic">Maksimal ukuran file foto adalah 2 MB.</p>

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        @if($user->role === 'dosen')
            <div>
                <x-input-label for="research_interests" :value="__('Bidang Keahlian / Research Interest')" />
                <textarea id="research_interests" name="research_interests" class="mt-1 block w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl text-xs font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all leading-relaxed p-4" rows="3" placeholder="Contoh: AI, Web Development, Mobile Apps, Cybersecurity (Pisahkan dengan koma)">{{ old('research_interests', $user->research_interests) }}</textarea>
                <p class="mt-2 text-[10px] text-slate-500 italic">Gunakan kata kunci singkat dipisahkan koma untuk memudahkan sistem mencocokkan dengan topik mahasiswa.</p>
                <x-input-error class="mt-2" :messages="$errors->get('research_interests')" />
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

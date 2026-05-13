<section>
    <header>
        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">
            {{ __('Tanda Tangan Digital') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            {{ __('Unggah gambar tanda tangan Anda untuk digunakan pada logbook dan pengesahan revisi.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.signature.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf

        <div class="flex items-start space-x-6">
            <div class="shrink-0">
                @if($user->signature)
                    <div class="p-2 bg-white rounded-lg border-2 border-slate-200 dark:border-slate-700 shadow-sm inline-block">
                        <img src="{{ $user->signature_url }}" alt="Signature" class="h-24 w-auto object-contain">
                    </div>
                @else
                    <div class="h-24 w-48 bg-slate-100 dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-lg flex items-center justify-center text-slate-400 italic text-xs">
                        Belum ada tanda tangan
                    </div>
                @endif
            </div>

            <div class="flex-1 space-y-4">
                <div>
                    <x-input-label for="signature" :value="__('Pilih Gambar Tanda Tangan')" />
                    <input id="signature" name="signature" type="file" class="mt-1 block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-orange-50 dark:file:bg-orange-900/20 file:text-orange-700 dark:file:text-orange-500 hover:file:bg-orange-100 border border-slate-200 dark:border-slate-700 rounded p-1" required />
                    <x-input-error class="mt-2" :messages="$errors->get('signature')" />
                    <p class="mt-1 text-[10px] text-slate-500 italic">Disarankan gambar transparan (PNG) dengan ukuran 400x200px.</p>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Simpan Tanda Tangan') }}</x-primary-button>

                    @if (session('status') === 'signature-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-emerald-600 dark:text-emerald-400"
                        >{{ __('Tersimpan.') }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        @if($user->signature_token)
        <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700 flex items-center gap-4">
            <div class="p-2 bg-white rounded-lg">
                {!! QrCode::size(80)->generate(url('/verify-signature/' . $user->signature_token)) !!}
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Verifikasi QR Code</h4>
                <p class="text-[11px] text-slate-500 mt-1">Gunakan QR Code ini pada dokumen resmi untuk verifikasi keaslian tanda tangan digital Anda.</p>
            </div>
        </div>
        @endif
    </form>
</section>

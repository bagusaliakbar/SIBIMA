@props(['birthdays' => collect(), 'wishedUserIds' => []])

@if(isset($birthdays) && $birthdays->isNotEmpty())
    <div x-data="birthdayBoard({
            initialWishedIds: {{ Js::from($wishedUserIds ?? []) }},
            currentUserId: {{ Auth::id() }}
         })"
         @open-received-wishes.window="openReceivedModal($event.detail.userId, $event.detail.userName, $event.detail.userRole || '', $event.detail.userAvatar || '')"
         class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 shadow-sm mb-6 relative overflow-hidden">
        
        <!-- Subtle Glow Background -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-amber-500/10 dark:bg-amber-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800 relative z-10">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white uppercase tracking-tight">
                            Ulang Tahun Hari Ini
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            {{ $birthdays->count() }} Orang Bertambah Usia
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                        Mari berikan doa dan ucapan selamat kepada rekan mahasiswa &amp; dosen yang berulang tahun hari ini.
                    </p>
                </div>
            </div>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mt-5 relative z-10">
            @foreach($birthdays as $bUser)
                @php
                    $isSelf = ($bUser->id === Auth::id());
                    $roleLabel = match($bUser->role) {
                        'dosen' => 'Dosen',
                        'kaprodi' => 'Kaprodi',
                        'admin' => 'Admin',
                        default => 'Mahasiswa'
                    };
                    $roleBadgeClass = match($bUser->role) {
                        'dosen' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                        'kaprodi' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                        'admin' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                        default => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
                    };
                @endphp
                <div class="bg-slate-50/70 dark:bg-slate-800/40 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 hover:border-amber-300 dark:hover:border-amber-700/80 transition-all flex flex-col justify-between gap-3.5 group">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-2xl overflow-hidden bg-slate-200 dark:bg-slate-700 shrink-0 border border-slate-200 dark:border-slate-700 shadow-2xs relative">
                            <img src="{{ $bUser->avatar_url }}" alt="{{ $bUser->name }}" class="w-full h-full object-cover">
                            <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-amber-500 rounded-full border-2 border-white dark:border-slate-900" title="Ulang Tahun Hari Ini"></div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider border {{ $roleBadgeClass }}">
                                    {{ $roleLabel }}
                                </span>
                                @if($isSelf)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                        Anda
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate mt-1 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors" title="{{ $bUser->name }}">
                                {{ $bUser->name }}
                            </h4>
                            @if($bUser->identifier)
                                <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500 mt-0.5">
                                    {{ $bUser->identifier }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-2 border-t border-slate-200/50 dark:border-slate-700/50 flex items-center gap-2">
                        @if($isSelf)
                            <button type="button"
                                    @click="openReceivedModal({{ $bUser->id }}, '{{ addslashes($bUser->name) }}', '{{ $roleLabel }}', '{{ $bUser->avatar_url }}')"
                                    class="w-full py-2 px-3 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-black uppercase tracking-wider shadow-2xs active:scale-95 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg aria-hidden="true" class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>Lihat Doa Masuk</span>
                            </button>
                        @else
                            <template x-if="hasWished({{ $bUser->id }})">
                                <div class="w-full flex items-center gap-2">
                                    <button type="button" 
                                            disabled
                                            class="flex-1 py-2 px-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-black uppercase tracking-wider flex items-center justify-center gap-1.5 cursor-default">
                                        <svg aria-hidden="true" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Terkirim</span>
                                    </button>
                                    <button type="button"
                                            @click="openReceivedModal({{ $bUser->id }}, '{{ addslashes($bUser->name) }}', '{{ $roleLabel }}', '{{ $bUser->avatar_url }}')"
                                            class="p-2 rounded-xl bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white border border-slate-200 dark:border-slate-700 hover:bg-slate-50 transition-colors cursor-pointer shrink-0"
                                            title="Lihat seluruh ucapan untuk {{ $bUser->name }}">
                                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <template x-if="!hasWished({{ $bUser->id }})">
                                <div class="w-full flex items-center gap-2">
                                    <button type="button"
                                            @click="openSendModal({{ $bUser->id }}, '{{ addslashes($bUser->name) }}', '{{ $roleLabel }}', '{{ $bUser->avatar_url }}')"
                                            class="flex-1 py-2 px-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-wider shadow-sm active:scale-95 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg aria-hidden="true" class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        <span>Kirim Ucapan</span>
                                    </button>
                                    <button type="button"
                                            @click="openReceivedModal({{ $bUser->id }}, '{{ addslashes($bUser->name) }}', '{{ $roleLabel }}', '{{ $bUser->avatar_url }}')"
                                            class="p-2 rounded-xl bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white border border-slate-200 dark:border-slate-700 hover:bg-slate-50 transition-colors cursor-pointer shrink-0"
                                            title="Lihat seluruh ucapan untuk {{ $bUser->name }}">
                                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modal 1: Kirim Ucapan Selamat Ulang Tahun -->
        <template x-teleport="body">
            <div x-show="sendModalOpen" 
                 x-cloak 
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 style="display: none;"
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="sendModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" 
                         class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs transition-opacity" 
                         @click="if(!isSubmitting) sendModalOpen = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="sendModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                        
                        <!-- Modal Header -->
                        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="min-w-0">
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">
                                        Kirim Doa &amp; Ucapan Ulang Tahun
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                        Pesan Anda akan tampil di papan ucapan penerima.
                                    </p>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="sendModalOpen = false" 
                                    :disabled="isSubmitting"
                                    class="p-2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <form @submit.prevent="submitWish()" class="p-6 space-y-4">
                            <!-- Penerima Card -->
                            <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-700 shrink-0 border border-slate-200 dark:border-slate-600">
                                    <img :src="activeRecipient?.avatar" :alt="activeRecipient?.name" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Penerima</span>
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" x-text="activeRecipient?.name"></h4>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800 shrink-0" x-text="activeRecipient?.role"></span>
                            </div>

                            <!-- Pilihan Template Ucapan Cepat -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                                    Pilih template ucapan:
                                </label>
                                <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                    <button type="button" 
                                            @click="applyPreset('Selamat ulang tahun! Semoga senantiasa diberikan kesehatan, keberkahan usia, dan kemudahan dalam segala urusan.')" 
                                            class="w-full text-left p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 hover:bg-amber-50 dark:hover:bg-amber-950/40 border border-slate-200 dark:border-slate-700 text-[11px] text-slate-700 dark:text-slate-300 transition-colors flex items-center gap-2 cursor-pointer">
                                        <span class="text-amber-500 font-bold shrink-0">✦</span>
                                        <span class="line-clamp-1">Kesehatan, keberkahan usia &amp; kemudahan urusan</span>
                                    </button>
                                    <button type="button" 
                                            @click="applyPreset('Barakallah fii umrik. Semoga bertambahnya usia membawa keberkahan, keselamatan, dan kelancaran dalam berikhtiar.')" 
                                            class="w-full text-left p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 hover:bg-amber-50 dark:hover:bg-amber-950/40 border border-slate-200 dark:border-slate-700 text-[11px] text-slate-700 dark:text-slate-300 transition-colors flex items-center gap-2 cursor-pointer">
                                        <span class="text-amber-500 font-bold shrink-0">✦</span>
                                        <span class="line-clamp-1">Barakallah fii umrik, panjang umur yang berkah</span>
                                    </button>
                                    <template x-if="activeRecipient?.role === 'Dosen' || activeRecipient?.role === 'Kaprodi'">
                                        <button type="button" 
                                                @click="applyPreset('Selamat ulang tahun Bapak/Ibu! Terima kasih banyak atas ilmu dan bimbingannya. Semoga senantiasa sehat dan sukses dalam mengabdi.')" 
                                                class="w-full text-left p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 hover:bg-amber-50 dark:hover:bg-amber-950/40 border border-slate-200 dark:border-slate-700 text-[11px] text-slate-700 dark:text-slate-300 transition-colors flex items-center gap-2 cursor-pointer">
                                            <span class="text-amber-500 font-bold shrink-0">✦</span>
                                            <span class="line-clamp-1">Khusus Dosen: Terima kasih atas ilmu &amp; bimbingan</span>
                                        </button>
                                    </template>
                                    <template x-if="activeRecipient?.role === 'Mahasiswa'">
                                        <button type="button" 
                                                @click="applyPreset('Selamat ulang tahun! Semoga senantiasa semangat, skripsinya lancar, dan segera meraih gelar sarjana dengan hasil terbaik!')" 
                                                class="w-full text-left p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 hover:bg-amber-50 dark:hover:bg-amber-950/40 border border-slate-200 dark:border-slate-700 text-[11px] text-slate-700 dark:text-slate-300 transition-colors flex items-center gap-2 cursor-pointer">
                                            <span class="text-amber-500 font-bold shrink-0">✦</span>
                                            <span class="line-clamp-1">Khusus Mahasiswa: Skripsi lancar &amp; segera lulus</span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Pesan Textarea -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="birthday-wish-message" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                        Ucapan:
                                    </label>
                                    <span class="text-[10px] font-mono text-slate-400" x-text="wishMessage.length + ' / 300'"></span>
                                </div>
                                <textarea id="birthday-wish-message" 
                                          x-model="wishMessage" 
                                          maxlength="300"
                                          rows="3" 
                                          required
                                          placeholder="Tuliskan doa atau ucapan selamat ulang tahun di sini..."
                                          class="w-full p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all resize-none"></textarea>
                            </div>

                            <!-- Alert status -->
                            <template x-if="statusNotice">
                                <div :class="statusNotice.type === 'success' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-200 border-rose-200 dark:border-rose-800'"
                                     class="p-3 rounded-xl text-xs font-medium border flex items-center gap-2">
                                    <svg x-show="statusNotice.type === 'success'" aria-hidden="true" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg x-show="statusNotice.type === 'error'" aria-hidden="true" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span x-text="statusNotice.message"></span>
                                </div>
                            </template>

                            <!-- Modal Footer -->
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                                <button type="button" 
                                        @click="sendModalOpen = false" 
                                        :disabled="isSubmitting"
                                        class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-all cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" 
                                        :disabled="isSubmitting || !wishMessage.trim()"
                                        class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-black uppercase tracking-wider shadow-sm active:scale-95 transition-all flex items-center gap-2 cursor-pointer">
                                    <svg x-show="isSubmitting" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg x-show="!isSubmitting" aria-hidden="true" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Doa'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- Modal 2: Galeri / Kotak Ucapan Masuk -->
        <template x-teleport="body">
            <div x-show="receivedModalOpen" 
                 x-cloak 
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 style="display: none;"
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="receivedModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" 
                         class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs transition-opacity" 
                         @click="receivedModalOpen = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="receivedModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-200 dark:border-slate-800">
                        
                        <!-- Header -->
                        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/20">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">
                                        Kotak Doa &amp; Ucapan Ulang Tahun
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                        Untuk <strong class="text-slate-700 dark:text-slate-300" x-text="receivedUser?.name"></strong>
                                    </p>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="receivedModalOpen = false" 
                                    class="p-2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 max-h-[60vh] overflow-y-auto space-y-3">
                            <!-- Loading State -->
                            <div x-show="isLoadingReceived" class="py-12 text-center space-y-3">
                                <svg class="animate-spin w-8 h-8 text-orange-600 mx-auto" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-xs text-slate-500 font-medium">Memuat doa dan ucapan rekan...</p>
                            </div>

                            <!-- Empty State -->
                            <div x-show="!isLoadingReceived && receivedWishes.length === 0" class="py-12 text-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                                    <svg aria-hidden="true" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Ucapan Masuk</h4>
                                <p class="text-xs text-slate-400 max-w-xs mx-auto">
                                    Jadilah orang pertama yang mengirimkan doa dan ucapan selamat ulang tahun!
                                </p>
                            </div>

                            <!-- List of Wishes -->
                            <div x-show="!isLoadingReceived && receivedWishes.length > 0" class="space-y-3">
                                <template x-for="wish in receivedWishes" :key="wish.id">
                                    <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="w-8 h-8 rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-700 shrink-0 border border-slate-200 dark:border-slate-600">
                                                    <img :src="wish.sender_avatar" :alt="wish.sender_name" class="w-full h-full object-cover">
                                                </div>
                                                <div class="min-w-0">
                                                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" x-text="wish.sender_name"></h5>
                                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400" x-text="wish.sender_role"></span>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-medium text-slate-400 shrink-0" x-text="wish.time_ago"></span>
                                        </div>
                                        <p class="text-xs text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900/60 p-3 rounded-xl border border-slate-200/60 dark:border-slate-800 font-medium leading-relaxed" x-text="wish.message"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500" x-text="'Total: ' + receivedWishes.length + ' Ucapan'"></span>
                            <button type="button" 
                                    @click="receivedModalOpen = false" 
                                    class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-all cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function birthdayBoard(config) {
            return {
                wishedIds: config.initialWishedIds || [],
                currentUserId: config.currentUserId,
                sendModalOpen: false,
                receivedModalOpen: false,
                activeRecipient: null,
                wishMessage: '',
                isSubmitting: false,
                statusNotice: null,
                receivedUser: null,
                receivedWishes: [],
                isLoadingReceived: false,

                hasWished(userId) {
                    return this.wishedIds.includes(userId);
                },

                openSendModal(id, name, role, avatar) {
                    this.activeRecipient = { id, name, role, avatar };
                    this.wishMessage = '';
                    this.statusNotice = null;
                    this.sendModalOpen = true;
                },

                applyPreset(text) {
                    this.wishMessage = text;
                },

                async submitWish() {
                    if (!this.wishMessage.trim() || !this.activeRecipient) return;

                    this.isSubmitting = true;
                    this.statusNotice = null;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const response = await fetch("{{ route('birthday-wishes.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            },
                            body: JSON.stringify({
                                to_user_id: this.activeRecipient.id,
                                message: this.wishMessage
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            if (!this.wishedIds.includes(this.activeRecipient.id)) {
                                this.wishedIds.push(this.activeRecipient.id);
                            }
                            this.statusNotice = { type: 'success', message: data.message };
                            setTimeout(() => {
                                this.sendModalOpen = false;
                                this.statusNotice = null;
                            }, 1500);
                        } else {
                            this.statusNotice = { type: 'error', message: data.message || 'Gagal mengirim ucapan.' };
                        }
                    } catch (error) {
                        this.statusNotice = { type: 'error', message: 'Terjadi kesalahan jaringan. Silakan coba lagi.' };
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async openReceivedModal(id, name, role, avatar) {
                    this.receivedUser = { id, name, role, avatar };
                    this.receivedWishes = [];
                    this.isLoadingReceived = true;
                    this.receivedModalOpen = true;

                    try {
                        const response = await fetch(`/birthday-wishes/${id}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.receivedWishes = data.wishes || [];
                        }
                    } catch (e) {
                        console.error('Gagal memuat ucapan:', e);
                    } finally {
                        this.isLoadingReceived = false;
                    }
                }
            };
        }
    </script>
@endif


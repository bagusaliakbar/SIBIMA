        <!-- WhatsApp Bot Contact Save Banner -->
        @php
            $botNumberRaw = config('services.whatsapp.bot_number') ?: env('WHATSAPP_BOT_NUMBER', '');
            $botNumberFormatted = $botNumberRaw ? \App\Helpers\PhoneHelper::formatForWhatsApp($botNumberRaw) : null;
        @endphp
        <div x-data="{ dismissed: localStorage.getItem('hide_wa_bot_banner') === 'true' }" 
             x-show="!dismissed" 
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200/90 dark:border-emerald-800/60 rounded-2xl p-4 sm:p-5 shadow-xs dark:shadow-none relative overflow-hidden flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
            
            <!-- Background glow -->
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-emerald-400/10 dark:bg-emerald-400/5 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Close button in top-right corner -->
            <button @click="dismissed = true; localStorage.setItem('hide_wa_bot_banner', 'true')" 
                    type="button" 
                    class="absolute top-3 right-3 sm:top-4 sm:right-4 p-1.5 text-emerald-700/70 hover:text-emerald-950 hover:bg-emerald-200/60 dark:text-emerald-400/80 dark:hover:text-white dark:hover:bg-emerald-900/50 rounded-xl transition-colors cursor-pointer z-20" 
                    title="Tutup pemberitahuan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="flex items-start gap-3.5 pr-8 sm:pr-0 relative z-10 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 dark:bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                    <svg class="w-5 h-5 fill-current text-white" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-black tracking-wide uppercase text-emerald-950 dark:text-emerald-100">Pemberitahuan Sistem WhatsApp Bot SIBIMA</h4>
                    <p class="text-xs font-medium text-emerald-800 dark:text-emerald-300/90 leading-relaxed mt-0.5">
                        Agar notifikasi bimbingan, revisi, & jadwal sidang masuk lancar tanpa terblokir, mohon <strong class="text-emerald-950 dark:text-white font-black">simpan nomor WhatsApp Bot SIBIMA {{ $botNumberRaw ? "($botNumberRaw)" : '' }}</strong> ke kontak HP Anda.
                    </p>
                </div>
            </div>

            @if($botNumberFormatted)
                <div class="w-full sm:w-auto shrink-0 relative z-10">
                    <a href="https://wa.me/{{ $botNumberFormatted }}?text={{ urlencode('Halo Bot SIBIMA, saya menyimpan nomor ini untuk menerima notifikasi akademik.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="w-full sm:w-auto min-h-[38px] px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:text-slate-950 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all shadow-sm hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 fill-current text-white dark:text-slate-950" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Chat / Simpan Bot</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Birthday Celebration Card / Prompt -->
        @if(Auth::user()->is_birthday)
            <div x-data="birthdayCelebration()" 
                 x-init="initConfetti()"
                 x-show="!dismissed" 
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 sm:p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <!-- Floating Canvas for Confetti -->
                <canvas x-ref="confettiCanvas" class="pointer-events-none fixed inset-0 z-50 h-full w-full"></canvas>

                <div class="flex-1 min-w-0 relative z-10 text-left">
                    <div class="flex items-center gap-2 flex-wrap mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                            Hari Spesial
                        </span>
                        @if(Auth::user()->age)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                Usia ke-{{ Auth::user()->age }} Tahun
                            </span>
                        @endif
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                        <span>Selamat Ulang Tahun, {{ Auth::user()->name }}!</span>
                        <svg aria-hidden="true" class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </h3>
                    @if(Auth::user()->role === 'mahasiswa')
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-1 max-w-2xl leading-relaxed">
                            Keluarga besar FASILKOM UNSUB mengucapkan selamat bertambah usia! Semoga senantiasa diberikan kelancaran bimbingan, kemudahan revisi, dan segera meraih gelar sarjana dengan hasil terbaik. Tetap semangat skripsinya!
                        </p>
                    @else
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-1 max-w-2xl leading-relaxed">
                            Keluarga besar FASILKOM UNSUB mengucapkan selamat bertambah usia! Semoga senantiasa dilimpahkan kesehatan, keberkahan, kemudahan dalam mengabdi, serta terus menginspirasi mahasiswa dalam mencetak generasi unggul. Teriring doa terbaik untuk Bapak/Ibu!
                        </p>
                    @endif
                </div>

                <div class="flex items-center gap-2.5 shrink-0 relative z-10 w-full sm:w-auto justify-end flex-wrap">
                    <button type="button" 
                            @click="$dispatch('open-received-wishes', { userId: {{ Auth::id() }}, userName: '{{ addslashes(Auth::user()->name) }}', userRole: '{{ Auth::user()->role === 'dosen' ? 'Dosen' : (Auth::user()->role === 'kaprodi' ? 'Kaprodi' : (Auth::user()->role === 'admin' ? 'Admin' : 'Mahasiswa')) }}', userAvatar: '{{ Auth::user()->avatar_url }}' })"
                            class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-black uppercase tracking-wider shadow-2xs active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer w-full sm:w-auto">
                        <svg aria-hidden="true" class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span>Kotak Doa ({{ $myReceivedWishesCount ?? 0 }})</span>
                    </button>
                    <button type="button" 
                            @click="burst()" 
                            class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-wider shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer w-full sm:w-auto">
                        <svg aria-hidden="true" class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span class="text-white font-black">Rayakan Lagi</span>
                    </button>
                    <button type="button" 
                            @click="dismiss()" 
                            aria-label="Tutup ucapan ulang tahun hari ini"
                            class="p-2.5 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer"
                            title="Tutup ucapan hari ini">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <script>
                function birthdayCelebration() {
                    return {
                        dismissed: localStorage.getItem('hide_birthday_card_{{ date('Y-m-d') }}') === 'true',
                        canvas: null,
                        ctx: null,
                        particles: [],
                        animationId: null,

                        dismiss() {
                            this.dismissed = true;
                            localStorage.setItem('hide_birthday_card_{{ date('Y-m-d') }}', 'true');
                            if (this.animationId) {
                                cancelAnimationFrame(this.animationId);
                            }
                        },

                        initConfetti() {
                            if (this.dismissed) return;
                            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                            this.$nextTick(() => {
                                this.canvas = this.$refs.confettiCanvas;
                                if (!this.canvas) return;
                                this.ctx = this.canvas.getContext('2d');
                                this.resizeCanvas();
                                window.addEventListener('resize', () => this.resizeCanvas());
                                // Initial celebratory burst
                                this.burst();
                            });
                        },

                        resizeCanvas() {
                            if (!this.canvas) return;
                            this.canvas.width = window.innerWidth;
                            this.canvas.height = window.innerHeight;
                        },

                        burst() {
                            if (!this.canvas || !this.ctx) return;
                            this.resizeCanvas();
                            const colors = ['#f59e0b', '#ef4444', '#ec4899', '#8b5cf6', '#10b981', '#3b82f6', '#facc15'];
                            const count = 120;
                            const newParticles = [];

                            for (let i = 0; i < count; i++) {
                                const fromLeft = i % 2 === 0;
                                newParticles.push({
                                    x: fromLeft ? window.innerWidth * 0.15 + (Math.random() * 60) : window.innerWidth * 0.85 - (Math.random() * 60),
                                    y: window.innerHeight * 0.65,
                                    vx: fromLeft ? (Math.random() * 8 + 3) : -(Math.random() * 8 + 3),
                                    vy: -(Math.random() * 14 + 10),
                                    size: Math.random() * 8 + 6,
                                    color: colors[Math.floor(Math.random() * colors.length)],
                                    rotation: Math.random() * 360,
                                    rotationSpeed: (Math.random() - 0.5) * 12,
                                    gravity: 0.35,
                                    opacity: 1,
                                    decay: Math.random() * 0.008 + 0.006,
                                    shape: Math.random() > 0.4 ? 'rect' : 'circle'
                                });
                            }

                            this.particles.push(...newParticles);

                            if (!this.animationId) {
                                this.loop();
                            }
                        },

                        loop() {
                            if (!this.ctx || !this.canvas) return;
                            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

                            for (let i = this.particles.length - 1; i >= 0; i--) {
                                const p = this.particles[i];
                                p.x += p.vx;
                                p.y += p.vy;
                                p.vy += p.gravity;
                                p.vx *= 0.985;
                                p.rotation += p.rotationSpeed;
                                p.opacity -= p.decay;

                                if (p.opacity <= 0 || p.y > this.canvas.height + 20) {
                                    this.particles.splice(i, 1);
                                    continue;
                                }

                                this.ctx.save();
                                this.ctx.globalAlpha = Math.max(0, p.opacity);
                                this.ctx.translate(p.x, p.y);
                                this.ctx.rotate((p.rotation * Math.PI) / 180);
                                this.ctx.fillStyle = p.color;

                                if (p.shape === 'rect') {
                                    this.ctx.fillRect(-p.size / 2, -p.size / 4, p.size, p.size / 2);
                                } else {
                                    this.ctx.beginPath();
                                    this.ctx.arc(0, 0, p.size / 3, 0, Math.PI * 2);
                                    this.ctx.fill();
                                }
                                this.ctx.restore();
                            }

                            if (this.particles.length > 0) {
                                this.animationId = requestAnimationFrame(() => this.loop());
                            } else {
                                this.animationId = null;
                                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                            }
                        }
                    };
                }
            </script>
        @elseif(Auth::user()->birth_date === null)
            <div x-data="{ dismissed: localStorage.getItem('hide_birthdate_prompt') === 'true' }" 
                 x-show="!dismissed" 
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-800/50 rounded-2xl p-4 sm:p-5 shadow-xs relative overflow-hidden flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                
                <!-- Close button in top-right corner -->
                <button @click="dismissed = true; localStorage.setItem('hide_birthdate_prompt', 'true')" 
                        type="button" 
                        class="absolute top-3 right-3 sm:top-4 sm:right-4 p-1.5 text-rose-700/70 hover:text-rose-950 hover:bg-rose-200/60 dark:text-rose-400/80 dark:hover:text-white dark:hover:bg-rose-900/50 rounded-xl transition-colors cursor-pointer z-20" 
                        title="Nanti saja">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="flex items-start gap-3.5 pr-8 sm:pr-0 relative z-10 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-black tracking-wide uppercase text-rose-950 dark:text-rose-100">Lengkapi Tanggal Lahir Anda</h4>
                        <p class="text-xs font-medium text-rose-800 dark:text-rose-300/90 leading-relaxed mt-0.5">
                            Dapatkan ucapan spesial dan kejutan di hari ulang tahun Anda dari sistem SIBIMA.
                        </p>
                    </div>
                </div>

                <div class="w-full sm:w-auto shrink-0 relative z-10">
                    <a href="{{ route('profile.edit') }}#birth_date" 
                       class="w-full sm:w-auto min-h-[38px] px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-black uppercase tracking-wider rounded-xl transition-all shadow-sm hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Lengkapi di Profil</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>
        @endif

        <!-- Campus Birthday Board (Papan Ucapan Ulang Tahun Seluruh Civitas Akademika) -->
        <x-birthday-board :birthdays="$todayBirthdays ?? collect()" :wishedUserIds="$wishedUserIds ?? []" />

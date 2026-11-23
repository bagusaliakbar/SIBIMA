                <!-- Top Header (Clean & Modern, Fluid Mobile Height h-14 sm:h-20) -->
                <header class="h-14 sm:h-20 bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between px-3 sm:px-6 lg:px-10 shrink-0 transition-all duration-300">
                    <div class="flex items-center md:hidden gap-1.5 sm:gap-2">
                        <!-- Hamburger toggle for mobile sidebar -->
                        <button type="button" @click="sidebarOpen = !sidebarOpen" :aria-expanded="sidebarOpen" aria-label="Buka atau tutup menu navigasi" title="Menu Navigasi" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-slate-700/50 transition-all cursor-pointer">
                            <svg aria-hidden="true" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <img src="{{ asset('logo_unsub.png') }}" alt="Logo UNSUB" class="w-7 h-7 sm:w-8 sm:h-8 object-contain">
                            <span class="text-base sm:text-lg font-black text-slate-800 dark:text-white tracking-tighter">SIBIMA</span>
                        </div>
                    </div>

                    <!-- Desktop Sidebar Toggle & Quick Search Trigger -->
                    <div class="hidden md:flex items-center gap-2.5">
                        <button type="button" 
                                @click="sidebarCollapsed = !sidebarCollapsed" 
                                :aria-expanded="!sidebarCollapsed"
                                :aria-label="sidebarCollapsed ? 'Perluas Sidebar' : 'Kecilkan Sidebar'"
                                class="h-10 px-3.5 rounded-xl flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50/80 dark:hover:bg-slate-700/60 transition-all border border-slate-200/80 dark:border-slate-700/60 shadow-xs cursor-pointer group"
                                :title="sidebarCollapsed ? 'Perluas Sidebar' : 'Kecilkan Sidebar'">
                            <div class="w-5 h-5 flex items-center justify-center transition-colors">
                                <svg aria-hidden="true" class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors" x-text="sidebarCollapsed ? 'Buka Menu' : 'Kecilkan'"></span>
                        </button>

                        <button type="button"
                                @click="$dispatch('sibima-open-search')"
                                aria-label="Buka Pencarian Cepat (Ctrl+K)"
                                class="h-10 px-3.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/70 dark:hover:bg-slate-700/70 border border-slate-200/80 dark:border-slate-700/80 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 text-xs font-medium inline-flex items-center gap-2.5 transition-colors cursor-pointer lg:w-64 justify-between">
                            <span class="inline-flex items-center gap-2 truncate">
                                <svg aria-hidden="true" class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <span class="truncate">Cari cepat...</span>
                            </span>
                            <kbd class="hidden lg:inline-flex items-center px-1.5 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[10px] font-bold text-slate-500 dark:text-slate-400 shrink-0">Ctrl K</kbd>
                        </button>
                    </div>



                    <div class="flex items-center space-x-1 sm:space-x-5">
                        <div class="flex items-center space-x-0.5 sm:space-x-2">
                            <!-- Mobile Quick Search Trigger -->
                            <button type="button" @click="$dispatch('sibima-open-search')" aria-label="Buka Pencarian Cepat" class="md:hidden w-10 h-10 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-slate-700 transition-all cursor-pointer" title="Cari Cepat">
                                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                            <!-- Dark Mode Toggle -->
                            <button type="button" @click="darkMode = !darkMode" :aria-label="darkMode ? 'Aktifkan Mode Terang' : 'Aktifkan Mode Gelap'" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-slate-700 transition-all relative cursor-pointer" title="Toggle Mode Gelap/Terang">
                                <svg aria-hidden="true" x-cloak x-show="!darkMode" class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                <svg aria-hidden="true" x-cloak x-show="darkMode" class="w-5 h-5 text-yellow-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </button>

                            @include('layouts.partials.notification-dropdown')

                            <!-- Cool Divider -->
                            <div class="h-5 sm:h-6 w-px bg-slate-200 dark:bg-slate-700 mx-0.5 sm:mx-1 self-center"></div>

                            <!-- Logout Button (Direct Button Sibling for Perfect Flexbox Alignment) -->
                            <form id="header-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                                @csrf
                            </form>
                            <button type="button" 
                                    onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();" 
                                    aria-label="Keluar dari akun"
                                    class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all group cursor-pointer" 
                                    title="Keluar">
                                <svg aria-hidden="true" class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </div>

                        <audio id="notif-sound" preload="none" class="hidden" hidden>
                            <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
                        </audio>

                        @include('layouts.partials.notification-script')
                        
                        <a href="{{ route('profile.edit') }}" aria-label="Buka profil {{ Auth::user()->name }}" class="flex items-center pl-2.5 sm:pl-5 border-l border-slate-100 dark:border-slate-700 ml-1 sm:ml-2 group/profile">
                            <div class="hidden sm:flex flex-col items-end mr-4">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-none mb-1 group-hover/profile:text-orange-600 transition-colors">{{ Auth::user()->name }}</span>
                                <span class="text-[11px] font-black text-orange-600 uppercase tracking-widest">{{ Auth::user()->role }}</span>
                            </div>
                            <div class="w-10 h-10 rounded-xl overflow-hidden shadow-md shadow-orange-200 dark:shadow-none ring-2 ring-white dark:ring-slate-700 cursor-pointer hover:scale-105 transition-transform relative group">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </a>
                    </div>
                </header>

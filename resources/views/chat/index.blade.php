<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
            Pesan
        </h2>
    </x-slot>

    <div class="w-full h-[calc(100dvh-12rem)] sm:h-[calc(100vh-14rem)] min-h-[480px] sm:min-h-[600px]">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 overflow-hidden h-full flex flex-col md:flex-row transition-all">
            
            <!-- Sidebar Contacts (Full-width on mobile, 80 on desktop) -->
            <div class="w-full md:w-80 md:border-r border-slate-100 dark:border-slate-700 flex flex-col h-full bg-slate-50/30 dark:bg-slate-900/30">
                <div class="p-4 sm:p-6 border-b border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Daftar Kontak</h3>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-700/60 px-2 py-0.5 rounded-full">{{ $users->count() }} Kontak</span>
                    </div>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400 group-focus-within:text-orange-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" id="contact-search" class="w-full pl-11 pr-4 py-2.5 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-bold rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all placeholder:text-slate-400" placeholder="Cari nama atau jabatan kontak...">
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <ul id="contact-list" class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @forelse($users as $u)
                            <li class="contact-item" data-name="{{ strtolower($u->name) }}" data-role="{{ strtolower($u->role) }}">
                                <a href="{{ route('chat.show', $u->id) }}" class="flex items-center px-4 sm:px-6 py-3.5 sm:py-4 hover:bg-white dark:hover:bg-slate-800 transition-all group relative">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-orange-600 scale-y-0 group-hover:scale-y-100 transition-transform"></div>
                                    <div class="relative w-12 h-12 shrink-0">
                                        <div class="w-12 h-12 rounded-2xl overflow-hidden flex items-center justify-center border border-orange-200 dark:border-orange-500/20 shadow-sm bg-orange-50 dark:bg-orange-900/10 group-hover:scale-105 transition-transform">
                                            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <!-- Online Status Indicator Dot -->
                                        @if($u->is_online)
                                            <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full shadow-xs z-10" title="Online"></span>
                                        @else
                                            <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-slate-300 dark:bg-slate-600 border-2 border-white dark:border-slate-800 rounded-full shadow-xs z-10" title="Offline"></span>
                                        @endif
                                        @if(isset($unreadCounts[$u->id]) && $unreadCounts[$u->id] > 0)
                                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white dark:border-slate-800 rounded-lg flex items-center justify-center text-[8px] font-black text-white z-10">
                                                {{ $unreadCounts[$u->id] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1 min-w-0">
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100 truncate group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors uppercase tracking-tight">{{ $u->name }}</p>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 truncate font-black uppercase tracking-widest mt-0.5">{{ $u->role }}</p>
                                    </div>
                                    <div class="text-slate-300 dark:text-slate-600 group-hover:text-orange-500 group-hover:translate-x-0.5 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="py-12 px-6 text-center">
                                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Belum ada kontak tersedia</p>
                            </li>
                        @endforelse
                    </ul>
                    <div id="no-contacts-msg" class="hidden text-center py-12 px-6">
                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Tidak ada kontak ditemukan</p>
                    </div>
                </div>
            </div>

            <!-- Empty State / Chat Area (Hidden on mobile, visible on desktop as right split panel) -->
            <div class="hidden md:flex flex-1 items-center justify-center bg-white dark:bg-slate-900/20 relative">
                <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] dark:bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:20px_20px] opacity-30"></div>
                
                <div class="text-center relative z-10 px-6 max-w-sm">
                    <div class="w-20 h-20 bg-orange-50 dark:bg-orange-950/40 rounded-3xl flex items-center justify-center mx-auto mb-5 border border-orange-200/80 dark:border-orange-800/60 shadow-sm text-orange-600 dark:text-orange-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider mb-2">Pilih Kontak</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">Silakan pilih salah satu kontak di panel sebelah kiri untuk mulai berdiskusi secara privat.</p>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('contact-search');
            const contactItems = document.querySelectorAll('.contact-item');
            const noContactsMsg = document.getElementById('no-contacts-msg');

            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                let visibleCount = 0;

                contactItems.forEach(function (item) {
                    const name = item.getAttribute('data-name') || '';
                    const role = item.getAttribute('data-role') || '';
                    const matches = name.includes(query) || role.includes(query);
                    item.style.display = matches ? '' : 'none';
                    if (matches) visibleCount++;
                });

                noContactsMsg.classList.toggle('hidden', visibleCount > 0);
            });
        });
    </script>
</x-app-layout>


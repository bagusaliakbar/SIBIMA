<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
            Pesan
        </h2>
    </x-slot>

    <div class="w-full h-[calc(100vh-14rem)] min-h-[600px]">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 overflow-hidden h-full flex flex-col md:flex-row transition-all">
            
            <!-- Sidebar Contacts -->
            <div class="w-full md:w-80 border-r border-slate-100 dark:border-slate-700 flex flex-col h-full bg-slate-50/30 dark:bg-slate-900/30">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Kontak</h3>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400 group-focus-within:text-orange-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" id="contact-search" class="w-full pl-11 pr-4 py-2.5 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-bold rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all placeholder:text-slate-400" placeholder="Cari teman chat...">
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <ul id="contact-list" class="divide-y divide-slate-50 dark:divide-slate-700/50">
                        @foreach($users as $u)
                            <li class="contact-item" data-name="{{ strtolower($u->name) }}" data-role="{{ strtolower($u->role) }}">
                                <a href="{{ route('chat.show', $u->id) }}" class="flex items-center px-6 py-4 hover:bg-white dark:hover:bg-slate-800 transition-all group relative">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-orange-600 scale-y-0 group-hover:scale-y-100 transition-transform"></div>
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-2xl bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black text-sm border border-orange-200 dark:border-orange-500/20 group-hover:scale-110 transition-transform">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        @if(isset($unreadCounts[$u->id]) && $unreadCounts[$u->id] > 0)
                                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white dark:border-slate-800 rounded-lg flex items-center justify-center text-[8px] font-black text-white">
                                                {{ $unreadCounts[$u->id] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1 min-w-0">
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100 truncate group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors uppercase tracking-tight">{{ $u->name }}</p>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 truncate font-black uppercase tracking-widest mt-0.5">{{ $u->role }}</p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div id="no-contacts-msg" class="hidden text-center py-12 px-6">
                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Tidak ada kontak ditemukan</p>
                    </div>
                </div>
            </div>

            <!-- Empty State / Chat Area -->
            <div class="flex-1 flex items-center justify-center bg-white dark:bg-slate-900/20 relative">
                <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] dark:bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:20px_20px] opacity-30"></div>
                
                <div class="text-center relative z-10 px-6">
                    <div class="w-24 h-24 bg-orange-100 dark:bg-orange-500/10 rounded-[2rem] flex items-center justify-center mx-auto mb-6 border border-orange-200 dark:border-orange-500/20 rotate-12 group-hover:rotate-0 transition-transform duration-500">
                        <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 uppercase tracking-tighter">Pesan SIBIMA</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-xs mx-auto font-bold uppercase tracking-widest leading-loose">Silakan pilih salah satu kontak di samping untuk mulai berdiskusi secara privat.</p>
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


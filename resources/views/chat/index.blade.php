<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
            Pesan
        </h2>
        <div class="hidden md:block mt-3 sm:mt-0 text-sm text-slate-500 dark:text-slate-400">
            Kirim dan terima pesan dari Mahasiswa, Dosen, atau Kaprodi.
        </div>
    </x-slot>

    <div class="w-full h-[calc(100vh-12rem)] min-h-[500px]">
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden h-full flex flex-col md:flex-row">
            
            <!-- Sidebar Contacts -->
            <div class="w-full md:w-80 border-r border-slate-100 dark:border-slate-700 flex flex-col h-full bg-slate-50/50 dark:bg-slate-900/30">
                <div class="p-4 border-b border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" id="contact-search" class="w-full pl-10 pr-4 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-colors placeholder:text-slate-400 dark:placeholder:text-slate-600" placeholder="Cari kontak...">
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto">
                    <ul id="contact-list" class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($users as $u)
                            <li class="contact-item" data-name="{{ strtolower($u->name) }}" data-role="{{ strtolower($u->role) }}">
                                <a href="{{ route('chat.show', $u->id) }}" class="flex items-center px-4 py-3 hover:bg-white dark:hover:bg-slate-800 transition-colors group">
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center font-bold text-sm border border-orange-200 dark:border-orange-800">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        @if(isset($unreadCounts[$u->id]) && $unreadCounts[$u->id] > 0)
                                            <span class="absolute top-0 right-0 w-3 h-3 bg-red-500 border-2 border-white dark:border-slate-800 rounded-full"></span>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <div class="flex justify-between items-baseline">
                                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">{{ $u->name }}</p>
                                        </div>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate capitalize">{{ $u->role }}</p>
                                    </div>
                                    @if(isset($unreadCounts[$u->id]) && $unreadCounts[$u->id] > 0)
                                        <div class="ml-2">
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                                {{ $unreadCounts[$u->id] }}
                                            </span>
                                        </div>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <p id="no-contacts-msg" class="hidden text-center text-sm text-slate-400 py-8 px-4">Tidak ada kontak yang cocok.</p>
                </div>
            </div>

            <!-- Empty State / Chat Area -->
            <div class="flex-1 flex items-center justify-center bg-slate-50/30 dark:bg-slate-900/10">
                <div class="text-center">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-600">
                        <svg class="w-10 h-10 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Pesan SIBIMA</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">Pilih kontak di sebelah kiri untuk mulai mengirim dan menerima pesan.</p>
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


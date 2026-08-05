<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[1.1rem] font-bold text-slate-800 dark:text-slate-100 tracking-tight">
            Percakapan dengan {{ $user->name }}
        </h2>
    </x-slot>

    <div class="w-full h-[calc(100vh-12rem)] min-h-[500px]">
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden h-full flex flex-col md:flex-row">
            
            <!-- Sidebar Contacts (Hidden on very small screens, visible on md+) -->
            <div class="hidden md:flex w-80 border-r border-slate-100 dark:border-slate-700 flex-col h-full bg-slate-50/50 dark:bg-slate-900/30 shrink-0">
                <div class="p-4 border-b border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" id="contact-search-show" class="w-full pl-10 pr-4 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-colors placeholder:text-slate-400 dark:placeholder:text-slate-600" placeholder="Cari kontak...">
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto">
                    <ul id="contact-list-show" class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($users as $u)
                            <li class="contact-item-show" data-name="{{ strtolower($u->name) }}" data-role="{{ strtolower($u->role) }}">
                                <a href="{{ route('chat.show', $u->id) }}" class="flex items-center px-4 py-3 transition-colors group {{ $u->id === $user->id ? 'bg-orange-50/50 dark:bg-orange-900/20 border-r-2 border-orange-500' : 'hover:bg-white dark:hover:bg-slate-800' }}">
                                    <div class="relative w-10 h-10 shrink-0">
                                        <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-orange-200 dark:border-orange-800 shadow-sm bg-orange-50 dark:bg-orange-900/10">
                                            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                                        </div>
                                        @if($u->is_online)
                                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border border-white dark:border-slate-800 rounded-full" title="Online"></span>
                                        @else
                                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-slate-300 dark:bg-slate-600 border border-white dark:border-slate-800 rounded-full" title="Offline"></span>
                                        @endif
                                        @if(isset($unreadCounts[$u->id]) && $unreadCounts[$u->id] > 0 && $u->id !== $user->id)
                                            <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white dark:border-slate-800 rounded-full"></span>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <div class="flex justify-between items-baseline">
                                            <p class="text-sm font-semibold truncate transition-colors {{ $u->id === $user->id ? 'text-orange-700 dark:text-orange-400' : 'text-slate-800 dark:text-slate-100 group-hover:text-orange-600 dark:group-hover:text-orange-400' }}">{{ $u->name }}</p>
                                        </div>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate capitalize">{{ $u->role }}</p>
                                    </div>
                                    @if(isset($unreadCounts[$u->id]) && $unreadCounts[$u->id] > 0 && $u->id !== $user->id)
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
                    <p id="no-contacts-msg-show" class="hidden text-center text-sm text-slate-400 py-8 px-4">Tidak ada kontak yang cocok.</p>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 flex flex-col h-full bg-[#e5ddd5] dark:bg-slate-900 relative">
                <!-- Chat Header -->
                <div class="h-16 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex items-center px-4 shrink-0 shadow-sm z-10">
                    <a href="{{ route('chat.index') }}" class="md:hidden mr-3 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <div class="relative w-10 h-10 shrink-0">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-orange-200 dark:border-orange-800 shadow-sm bg-orange-50 dark:bg-orange-900/10">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        </div>
                        @if($user->is_online)
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full shadow-xs" title="Online"></span>
                        @else
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-slate-300 dark:bg-slate-600 border-2 border-white dark:border-slate-800 rounded-full shadow-xs" title="Offline"></span>
                        @endif
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>{{ $user->name }}</span>
                        </h3>
                        <div class="flex items-center gap-1.5 text-[11px]">
                            <span class="text-slate-500 dark:text-slate-400 capitalize font-medium">{{ $user->role }}</span>
                            <span class="text-slate-300 dark:text-slate-600">•</span>
                            @if($user->is_online)
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Online
                                </span>
                            @else
                                <span class="text-slate-400 dark:text-slate-500">Offline</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'#9C92AC\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                    
                    <div class="text-center my-4">
                        <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-[10px] font-bold px-3 py-1 rounded-full shadow-sm border border-blue-200 dark:border-blue-800/50">
                            Percakapan diamankan dengan enkripsi end-to-end
                        </span>
                    </div>

                    @forelse($messages as $message)
                        @if($message->sender_id === Auth::id())
                            <!-- Sent Message (Right) -->
                            <div class="flex justify-end">
                                <div class="bg-[#d9fdd3] dark:bg-emerald-900/30 text-slate-800 dark:text-slate-100 p-2.5 rounded-lg rounded-tr-none max-w-[85%] md:max-w-[70%] shadow-sm relative border border-emerald-200/50 dark:border-emerald-800/50">
                                    <p class="text-sm leading-relaxed pr-10 whitespace-pre-wrap">{{ $message->message }}</p>
                                    <div class="absolute bottom-1 right-2 flex items-center space-x-1">
                                        <span class="text-[9px] text-slate-500 dark:text-slate-400">{{ $message->created_at->format('H:i') }}</span>
                                        @if($message->is_read)
                                            <!-- Double Blue Checkmark -->
                                            <span class="inline-flex text-blue-500 font-bold" title="Dibaca">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                <svg class="w-3.5 h-3.5 -ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </span>
                                        @else
                                            <!-- Double Grey Checkmark -->
                                            <span class="inline-flex text-slate-400 dark:text-slate-500" title="Terkirim">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                <svg class="w-3.5 h-3.5 -ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Received Message (Left) -->
                            <div class="flex justify-start">
                                <div class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 p-2.5 rounded-lg rounded-tl-none max-w-[85%] md:max-w-[70%] shadow-sm relative border border-slate-100 dark:border-slate-700">
                                    <p class="text-sm leading-relaxed pr-8 whitespace-pre-wrap">{{ $message->message }}</p>
                                    <span class="absolute bottom-1 right-2 text-[9px] text-slate-400 dark:text-slate-500">{{ $message->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center mt-10">
                            <p class="text-sm text-slate-500 dark:text-slate-400 bg-white/60 dark:bg-slate-800/60 inline-block px-4 py-2 rounded-full border border-slate-100 dark:border-slate-700">Belum ada pesan. Mulai sapa {{ $user->name }}!</p>
                        </div>
                    @endforelse
                </div>

                <!-- Chat Input Section with Expandable Quick Templates Drawer -->
                <div class="bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 shrink-0" x-data="{ showTemplates: false }">

                    <!-- Expandable Quick Templates Drawer (Inline, Never Clipped) -->
                    <div x-show="showTemplates" 
                         x-cloak 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-64"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 max-h-64"
                         x-transition:leave-end="opacity-0 max-h-0"
                         class="border-b border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-900 overflow-hidden">
                        
                        <div class="px-4 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white flex justify-between items-center shadow-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span class="text-xs font-bold uppercase tracking-wider">Pilih Templat Pesan Cepat</span>
                            </div>
                            <button type="button" @click="showTemplates = false" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-3 max-h-56 overflow-y-auto custom-scrollbar grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @if(auth()->user()->role === 'mahasiswa')
                                <button type="button" @click="useTemplate('Yth. Bapak/Ibu, mohon izin mengonfirmasi permohonan bimbingan skripsi yang telah saya ajukan.'); showTemplates = false" 
                                    class="text-left p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200/70 dark:border-slate-700/60 hover:border-orange-200 dark:hover:border-orange-500/30 transition-all flex items-start gap-2.5 group">
                                    <span class="text-base shrink-0">📜</span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400">Konfirmasi Bimbingan</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">Yth. Bapak/Ibu, mohon izin mengonfirmasi...</p>
                                    </div>
                                </button>
                                <button type="button" @click="useTemplate('Yth. Bapak/Ibu, draf revisi skripsi telah saya perbarui. Mohon berkenan meninjau kembali.'); showTemplates = false" 
                                    class="text-left p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200/70 dark:border-slate-700/60 hover:border-orange-200 dark:hover:border-orange-500/30 transition-all flex items-start gap-2.5 group">
                                    <span class="text-base shrink-0">📝</span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400">Update Revisi</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">Yth. Bapak/Ibu, draf revisi skripsi telah...</p>
                                    </div>
                                </button>
                                <button type="button" @click="useTemplate('Yth. Bapak/Ibu, apakah ada waktu luang minggu ini untuk jadwal bimbingan tatap muka?'); showTemplates = false" 
                                    class="text-left p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200/70 dark:border-slate-700/60 hover:border-orange-200 dark:hover:border-orange-500/30 transition-all flex items-start gap-2.5 group">
                                    <span class="text-base shrink-0">📅</span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400">Jadwal Bimbingan</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">Yth. Bapak/Ibu, apakah ada waktu luang...</p>
                                    </div>
                                </button>
                                <button type="button" @click="useTemplate('Yth. Bapak/Ibu, terima kasih atas bimbingan dan arahan yang telah diberikan.'); showTemplates = false" 
                                    class="text-left p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200/70 dark:border-slate-700/60 hover:border-orange-200 dark:hover:border-orange-500/30 transition-all flex items-start gap-2.5 group">
                                    <span class="text-base shrink-0">🙏</span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400">Ucapan Terima Kasih</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">Yth. Bapak/Ibu, terima kasih atas bimbingan...</p>
                                    </div>
                                </button>
                            @else
                                <button type="button" @click="useTemplate('Baik, permohonan bimbingan disetujui. Silakan siapkan draf dan bahan diskusi.'); showTemplates = false" 
                                    class="text-left p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200/70 dark:border-slate-700/60 hover:border-orange-200 dark:hover:border-orange-500/30 transition-all flex items-start gap-2.5 group">
                                    <span class="text-base shrink-0">✅</span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400">Bimbingan Disetujui</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">Baik, permohonan bimbingan disetujui...</p>
                                    </div>
                                </button>
                                <button type="button" @click="useTemplate('Draf sudah saya periksa. Silakan cek catatan revisi lengkap yang telah saya tambahkan di Logbook.'); showTemplates = false" 
                                    class="text-left p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200/70 dark:border-slate-700/60 hover:border-orange-200 dark:hover:border-orange-500/30 transition-all flex items-start gap-2.5 group">
                                    <span class="text-base shrink-0">📌</span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400">Cek Revisi Logbook</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">Draf sudah saya periksa. Silakan cek...</p>
                                    </div>
                                </button>
                                <button type="button" @click="useTemplate('Silakan temui saya di ruang dosen FASILKOM pada jam operasional kerja untuk konsultasi.'); showTemplates = false" 
                                    class="text-left p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200/70 dark:border-slate-700/60 hover:border-orange-200 dark:hover:border-orange-500/30 transition-all flex items-start gap-2.5 group">
                                    <span class="text-base shrink-0">🏫</span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400">Jadwal Tatap Muka</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">Silakan temui saya di ruang dosen FASILKOM...</p>
                                    </div>
                                </button>
                                <button type="button" @click="useTemplate('Progres bimbingan bagus. Silakan lanjutkan pengerjaan ke bab berikutnya.'); showTemplates = false" 
                                    class="text-left p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 hover:bg-orange-50 dark:hover:bg-orange-950/40 border border-slate-200/70 dark:border-slate-700/60 hover:border-orange-200 dark:hover:border-orange-500/30 transition-all flex items-start gap-2.5 group">
                                    <span class="text-base shrink-0">👍</span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400">Lanjutkan Bab</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">Progres bimbingan bagus. Silakan lanjutkan...</p>
                                    </div>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Chat Form Input -->
                    <div class="p-3">
                        <form action="{{ route('chat.store', $user->id) }}" method="POST" id="chat-form" onsubmit="return false;" class="flex items-end space-x-2">
                            @csrf
                            
                            <!-- Toggle Button Templat Cepat -->
                            <button type="button" @click="showTemplates = !showTemplates" 
                                class="h-11 px-3 rounded-xl bg-orange-50 dark:bg-orange-950/50 hover:bg-orange-100 dark:hover:bg-orange-900/40 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30 flex items-center gap-1.5 transition-all text-xs font-bold shrink-0 shadow-2xs hover:scale-[1.02] active:scale-[0.98]"
                                title="Pilih Templat Pesan Cepat">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span class="hidden sm:inline">Templat</span>
                            </button>

                            <div class="flex-1 bg-white dark:bg-slate-900 rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm overflow-hidden focus-within:border-orange-500 focus-within:ring-2 focus-within:ring-orange-500/20 transition-all">
                                <textarea 
                                    name="message" 
                                    id="message-input" 
                                    rows="1" 
                                    class="w-full border-0 focus:ring-0 resize-none py-3 px-4 text-sm max-h-32 min-h-[44px] bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-600" 
                                    placeholder="Ketik pesan..."
                                    required
                                    oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 128) + 'px'"></textarea>
                            </div>

                            <button type="submit" class="h-11 w-11 rounded-xl bg-gradient-to-r from-orange-600 to-orange-500 text-white flex items-center justify-center hover:from-orange-500 hover:to-orange-600 transition-all shadow-md shadow-orange-500/20 shrink-0 hover:scale-[1.02] active:scale-[0.98]">
                                <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script for Real-time Chat and AJAX Submission -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chat-messages');
            const messageInput = document.getElementById('message-input');
            const chatForm = document.getElementById('chat-form');
            const userId = {{ Auth::id() }};
            const partnerId = {{ $user->id }};
            
            // Auto scroll to bottom
            function scrollToBottom() {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            scrollToBottom();

            // Quick Template Handler
            window.useTemplate = function(text) {
                if (!messageInput) return;
                messageInput.value = text;
                messageInput.style.height = '';
                messageInput.style.height = Math.min(messageInput.scrollHeight, 128) + 'px';
                messageInput.focus();
            };

            // Listen for new messages
            if (window.Echo) {
                window.Echo.private(`chat.${userId}`)
                    .listen('MessageSent', (e) => {
                        if (e.message.sender_id === partnerId) {
                            appendMessage(e.message, 'left');
                        }
                    });
            } else {
                console.warn('Echo is not defined. Real-time chat updates are disabled.');
            }

            // Function to append message to UI
            function appendMessage(message, side) {
                const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                let html = '';
                
                if (side === 'right') {
                    const checkColor = message.is_read ? 'text-blue-500 font-bold' : 'text-slate-400 dark:text-slate-500';
                    html = `
                        <div class="flex justify-end">
                            <div class="bg-[#d9fdd3] dark:bg-emerald-900/30 text-slate-800 dark:text-slate-100 p-2.5 rounded-lg rounded-tr-none max-w-[85%] md:max-w-[70%] shadow-sm relative border border-emerald-200/50 dark:border-emerald-800/50">
                                <p class="text-sm leading-relaxed pr-10 whitespace-pre-wrap">${message.message}</p>
                                <div class="absolute bottom-1 right-2 flex items-center space-x-1">
                                    <span class="text-[9px] text-slate-500 dark:text-slate-400">${time}</span>
                                    <span class="inline-flex ${checkColor}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <svg class="w-3.5 h-3.5 -ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="flex justify-start">
                            <div class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 p-2.5 rounded-lg rounded-tl-none max-w-[85%] md:max-w-[70%] shadow-sm relative border border-slate-100 dark:border-slate-700">
                                <p class="text-sm leading-relaxed pr-8 whitespace-pre-wrap">${message.message}</p>
                                <span class="absolute bottom-1 right-2 text-[9px] text-slate-400 dark:text-slate-500">${time}</span>
                            </div>
                        </div>
                    `;
                }
                
                chatMessages.insertAdjacentHTML('beforeend', html);
                scrollToBottom();
            }

            function sendMessageAjax() {
                const text = messageInput.value.trim();
                if (!text) return;

                // Clear input early for better UX
                messageInput.value = '';
                messageInput.style.height = '';

                const tokenElement = chatForm.querySelector('input[name="_token"]');
                const token = tokenElement ? tokenElement.value : '';

                if (window.axios) {
                    window.axios.post(chatForm.action, {
                        message: text
                    }, {
                        headers: {
                            'X-CSRF-TOKEN': token
                        }
                    })
                    .then(response => {
                        if (response.data.success) {
                            appendMessage(response.data.message, 'right');
                        }
                    })
                    .catch(error => {
                        console.error('Error sending message:', error);
                        // Restore text if it failed
                        messageInput.value = text;
                        alert('Gagal mengirim pesan. Silakan coba lagi.');
                    });
                } else {
                    const formData = new FormData();
                    formData.append('message', text);
                    formData.append('_token', token);

                    fetch(chatForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            appendMessage(data.message, 'right');
                        }
                    })
                    .catch(err => {
                        console.error('Error sending message:', err);
                        messageInput.value = text;
                        alert('Gagal mengirim pesan. Silakan coba lagi.');
                    });
                }
            }

            // Handle AJAX form submission
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                sendMessageAjax();
            });

            // Handle Enter key to submit
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessageAjax();
                }
            });

            // Contact Search Filter
            const searchInput = document.getElementById('contact-search-show');
            if (searchInput) {
                const contactItems = document.querySelectorAll('.contact-item-show');
                const noContactsMsg = document.getElementById('no-contacts-msg-show');

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
            }
        });
    </script>
</x-app-layout>


                            <div x-data="notificationDropdown()" x-init="init()" class="relative">
                                <button type="button" x-ref="button" @click="toggle()" :aria-expanded="isOpen" aria-label="Buka pusat notifikasi" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-slate-700 transition-all relative cursor-pointer" title="Notifikasi">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    <template x-if="unreadCount > 0">
                                        <span style="top: 2px; right: 2px;" class="absolute flex h-3.5 min-w-[14px] px-1 items-center justify-center rounded-full bg-rose-600 text-[11px] font-black text-white border border-white dark:border-slate-800 leading-none select-none shadow-xs pointer-events-none">
                                            <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                                        </span>
                                    </template>
                                </button>

                                <!-- Dropdown Panel: Responsive Width for 360px Compact Phones -->
                                <div x-show="isOpen" 
                                     @click.away="isOpen = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-1"
                                     class="fixed sm:absolute inset-x-2 sm:inset-x-auto sm:right-0 top-16 sm:top-full mt-0 sm:mt-2 w-auto sm:w-96 max-w-full sm:max-w-sm bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 z-50 overflow-hidden text-left"
                                     style="display: none;">
                                    
                                    <!-- Header -->
                                    <div class="p-4 border-b border-slate-50 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Notifikasi</h3>
                                            <span x-show="unreadCount > 0" class="px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-400 text-[11px] font-black" x-text="unreadCount + ' Baru'"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <!-- Push Notification Device Toggle Button -->
                                            <button type="button" 
                                                    @click.stop="togglePushNotifications()" 
                                                    class="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                                                    :aria-label="pushEnabled ? 'Notifikasi HP aktif, klik untuk uji notifikasi' : 'Aktifkan notifikasi HP di perangkat ini'"
                                                    :title="pushEnabled ? 'Notifikasi HP: Aktif (Klik untuk Uji Notifikasi)' : 'Notifikasi HP: Nonaktif (Klik untuk Aktifkan di Perangkat)'">
                                                <svg aria-hidden="true" class="w-4 h-4" :class="pushEnabled ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </button>

                                            <!-- Sound Mute/Unmute Toggle -->
                                            <button type="button" 
                                                    @click.stop="toggleSound()" 
                                                    class="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                                                    :aria-label="soundEnabled ? 'Bisukan suara notifikasi' : 'Aktifkan suara notifikasi'"
                                                    :title="soundEnabled ? 'Suara Notifikasi: Aktif (Klik untuk Mute)' : 'Suara Notifikasi: Nonaktif (Klik untuk Aktifkan)'">
                                                <template x-if="soundEnabled">
                                                    <!-- Speaker ON -->
                                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                                                </template>
                                                <template x-if="!soundEnabled">
                                                    <!-- Speaker OFF -->
                                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path></svg>
                                                </template>
                                            </button>

                                            <button type="button" @click="markAllAsRead()" x-show="unreadCount > 0" class="text-[11px] font-bold text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                                                Tandai semua dibaca
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Notification List -->
                                    <div class="max-h-96 overflow-y-auto custom-scrollbar divide-y divide-slate-50 dark:divide-slate-700/60">
                                        <template x-if="notifications.length === 0">
                                            <div class="p-8 text-center">
                                                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                                </div>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Tidak ada notifikasi baru</p>
                                            </div>
                                        </template>

                                        <template x-for="notif in notifications" :key="notif.id">
                                            <div role="button"
                                                 tabindex="0"
                                                 @click="markAsRead(notif.id, getUrl(notif))" 
                                                 @keydown.enter.prevent="markAsRead(notif.id, getUrl(notif))"
                                                 @keydown.space.prevent="markAsRead(notif.id, getUrl(notif))"
                                                 class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors cursor-pointer relative group flex items-start gap-3.5"
                                                 :class="notif.read_at ? 'opacity-60' : 'bg-orange-50/20 dark:bg-orange-900/10'">
                                                
                                                <!-- Category Semantic Icon (Square 40x40 with rounded-xl, perfectly proportioned) -->
                                                <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center border shadow-xs"
                                                     style="width: 40px; height: 40px; min-width: 40px; min-height: 40px;"
                                                     :class="{
                                                        'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-blue-200/80 dark:border-blue-700/50': getCategory(notif) === 'schedule',
                                                        'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-200/80 dark:border-emerald-700/50': getCategory(notif) === 'success',
                                                        'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border-amber-200/80 dark:border-amber-700/50': getCategory(notif) === 'reminder',
                                                        'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border-indigo-200/80 dark:border-indigo-700/50': getCategory(notif) === 'revision',
                                                        'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-200/80 dark:border-rose-700/50': getCategory(notif) === 'cancelled',
                                                        'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200/80 dark:border-orange-700/50': getCategory(notif) === 'message',
                                                        'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700': getCategory(notif) === 'info'
                                                     }">
                                                    
                                                    <!-- Schedule -->
                                                    <svg x-show="getCategory(notif) === 'schedule'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>

                                                    <!-- Success / ACC -->
                                                    <svg x-show="getCategory(notif) === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>

                                                    <!-- Reminder -->
                                                    <svg x-show="getCategory(notif) === 'reminder'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>

                                                    <!-- Revision -->
                                                    <svg x-show="getCategory(notif) === 'revision'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>

                                                    <!-- Cancelled -->
                                                    <svg x-show="getCategory(notif) === 'cancelled'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>

                                                    <!-- Message -->
                                                    <svg x-show="getCategory(notif) === 'message'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>

                                                    <!-- Default Info -->
                                                    <svg x-show="getCategory(notif) === 'info'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>

                                                <!-- Message Body -->
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-snug" x-text="getTitle(notif)"></p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed" 
                                                       :class="(notif.data.topic || notif.data.notes) ? '' : 'line-clamp-2'" 
                                                       x-text="(notif.data.topic || notif.data.notes) ? (notif.data.message || '').split('\n')[0] : (notif.data.message || '')"></p>

                                                    <template x-if="notif.data.topic || notif.data.notes">
                                                        <div class="mt-2 p-2 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-800 text-[11px] space-y-1">
                                                            <template x-if="notif.data.topic">
                                                                <div class="flex items-start gap-1.5">
                                                                    <span class="font-bold text-slate-700 dark:text-slate-300 shrink-0">Topik:</span>
                                                                    <span class="text-slate-900 dark:text-slate-100 font-semibold" x-text="notif.data.topic"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="notif.data.notes">
                                                                <div class="flex items-start gap-1.5">
                                                                    <span class="font-bold text-slate-700 dark:text-slate-300 shrink-0">Catatan:</span>
                                                                    <span class="text-slate-700 dark:text-slate-300 italic" x-text="notif.data.notes"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="notif.data.scheduled_at">
                                                                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-[11px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-200/60 dark:border-slate-800/60">
                                                                    <span class="inline-flex items-center gap-1">
                                                                        <svg aria-hidden="true" class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                        <span x-text="notif.data.scheduled_at"></span>
                                                                    </span>
                                                                    <template x-if="notif.data.type_location">
                                                                        <span class="inline-flex items-center gap-1">
                                                                            <svg aria-hidden="true" class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                                            <span x-text="notif.data.type_location"></span>
                                                                        </span>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <div class="flex items-center gap-2 mt-1.5">
                                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium" x-text="formatDate(notif.created_at)"></span>
                                                        <span x-show="!notif.read_at" class="text-[11px] font-black text-orange-600 dark:text-orange-400">&bull; Baru</span>
                                                    </div>

                                                    <!-- Actionable Attendance Confirmation (For Students) -->
                                                    <template x-if="isAttendanceNotification(notif)">
                                                        <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-700/60" @click.stop>
                                                            <!-- If already confirmed in this session -->
                                                            <template x-if="attendanceSuccess[notif.data.mentoring_id]">
                                                                <div class="flex items-center gap-1.5 py-0.5 text-[11px] font-bold">
                                                                    <template x-if="attendanceSuccess[notif.data.mentoring_id] === 'attending'">
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700/80 text-[11px] font-black uppercase tracking-wider">
                                                                            <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                            <span>Akan Hadir</span>
                                                                        </span>
                                                                    </template>
                                                                    <template x-if="attendanceSuccess[notif.data.mentoring_id] === 'permission'">
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700/80 text-[11px] font-black uppercase tracking-wider">
                                                                            <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"/></svg>
                                                                            <span>Izin Diajukan</span>
                                                                        </span>
                                                                    </template>
                                                                </div>
                                                            </template>

                                                            <!-- If not yet confirmed -->
                                                            <template x-if="!attendanceSuccess[notif.data.mentoring_id]">
                                                                <div>
                                                                    <!-- Fast Action Buttons -->
                                                                    <div x-show="activePermissionInput !== notif.id" class="flex items-center gap-2">
                                                                        <button type="button" 
                                                                                @click.stop="confirmAttendance(notif, 'attending')" 
                                                                                :disabled="attendanceLoading[notif.data.mentoring_id]"
                                                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black uppercase tracking-wider shadow-2xs transition-all cursor-pointer disabled:opacity-50">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                            <span>Akan Hadir</span>
                                                                        </button>

                                                                        <button type="button" 
                                                                                @click.stop="activePermissionInput = notif.id" 
                                                                                :disabled="attendanceLoading[notif.data.mentoring_id]"
                                                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 dark:bg-slate-700 dark:hover:bg-rose-950/40 text-slate-700 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200 dark:border-slate-600 text-[11px] font-black uppercase tracking-wider shadow-2xs transition-all cursor-pointer disabled:opacity-50">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"/></svg>
                                                                            <span>Izin</span>
                                                                        </button>

                                                                        <span x-show="attendanceLoading[notif.data.mentoring_id]" class="text-[11px] text-slate-500 dark:text-slate-400 animate-pulse font-medium">Menyimpan...</span>
                                                                    </div>

                                                                    <!-- Inline Reason Form for Izin -->
                                                                    <div x-show="activePermissionInput === notif.id" class="space-y-1.5 mt-1">
                                                                        <input type="text" 
                                                                               x-model="permissionReason[notif.id]" 
                                                                               placeholder="Alasan berhalangan hadir..." 
                                                                               class="w-full text-xs px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-orange-500"
                                                                               @keydown.enter.prevent="submitPermission(notif)">
                                                                        <div class="flex items-center gap-1.5">
                                                                            <button type="button" 
                                                                                    @click.stop="submitPermission(notif)"
                                                                                    :disabled="attendanceLoading[notif.data.mentoring_id]"
                                                                                    class="px-2.5 py-1 rounded-md bg-orange-600 hover:bg-orange-700 text-white text-[11px] font-black uppercase tracking-wider transition-colors cursor-pointer">
                                                                                Kirim Izin
                                                                            </button>
                                                                            <button type="button" 
                                                                                    @click.stop="activePermissionInput = null" 
                                                                                    class="px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-semibold hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors cursor-pointer">
                                                                                Batal
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>

                                                <!-- Unread Indicator Dot -->
                                                <div x-show="!notif.read_at" class="w-2.5 h-2.5 rounded-full bg-orange-600 flex-shrink-0 self-center ring-4 ring-orange-100 dark:ring-orange-950/60"></div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <!-- Footer -->
                                    <div class="p-2.5 bg-slate-50/70 dark:bg-slate-900/70 border-t border-slate-100 dark:border-slate-700 text-center">
                                        <a href="{{ route('notifications.index') }}" class="inline-flex items-center justify-center gap-1.5 w-full py-1.5 px-3 rounded-xl text-xs font-bold text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40 transition-colors">
                                            <span>Lihat Semua Riwayat Notifikasi</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>

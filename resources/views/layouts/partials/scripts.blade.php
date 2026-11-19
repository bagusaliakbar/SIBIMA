        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if(session('success'))
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { title: 'Berhasil', message: {!! json_encode(session('success')) !!}, type: 'success' } 
                    }));
                @endif

                @if(session('error'))
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { title: 'Kesalahan', message: {!! json_encode(session('error')) !!}, type: 'error' } 
                    }));
                @endif

                @if(session('warning'))
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { title: 'Peringatan', message: {!! json_encode(session('warning')) !!}, type: 'warning' } 
                    }));
                @endif

                @if($errors->any())
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { title: 'Validasi Gagal', message: 'Terdapat kesalahan pada input Anda. Silakan periksa kembali.', type: 'error' } 
                    }));
                @endif
            
                const userId = {{ Auth::id() }};
                
                // Generic Notifications
                if (window.Echo) {
                    window.Echo.private(`notifications.${userId}`)
                        .listen('NewNotification', (e) => {
                            window.dispatchEvent(new CustomEvent('notify', { 
                                detail: { 
                                    title: e.title, 
                                    message: e.message, 
                                    type: e.type 
                                } 
                            }));
                            
                            // Play sound
                            const audio = document.getElementById('notif-sound');
                            if (audio) {
                                audio.currentTime = 0;
                                audio.play().catch(err => console.log('Audio play failed:', err));
                            }

                            // Refresh dropdown count if the function exists
                            if (window.refreshNotifications) {
                                window.refreshNotifications();
                            }
                        });

                    // Chat Notifications (when not on chat page)
                    window.Echo.private(`chat.${userId}`)
                        .listen('MessageSent', (e) => {
                            // Only show toast if we're not on the chat page with that specific user
                            const isChatPage = window.location.pathname.includes('/chat/');
                            const chatPartnerId = window.location.pathname.split('/').pop();
                            
                            if (!isChatPage || chatPartnerId != e.message.sender_id) {
                                window.dispatchEvent(new CustomEvent('notify', { 
                                    detail: { 
                                        title: 'Pesan Baru', 
                                        message: `${e.message.sender.name}: ${e.message.message.substring(0, 50)}...`, 
                                        type: 'message' 
                                    } 
                                }));
                                
                                const audio = document.getElementById('notif-sound');
                                if (audio) {
                                    audio.currentTime = 0;
                                    audio.play().catch(err => console.log('Audio play failed:', err));
                                }
                            }
                        });
                } else {
                    console.warn('Echo is not defined. Real-time notifications are disabled.');
                }
            });
        </script>
        
        <!-- Script to translate HTML5 validation messages to Indonesian -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const overrideValidationMessages = () => {
                    const elements = document.querySelectorAll('input, select, textarea');
                    elements.forEach(el => {
                        // Prevent attaching multiple listeners
                        if (el.dataset.validationBound) return;
                        el.dataset.validationBound = "true";
                        
                        el.addEventListener('invalid', function(e) {
                            e.target.setCustomValidity("");
                            if (!e.target.validity.valid) {
                                if (e.target.validity.valueMissing) {
                                    e.target.setCustomValidity("Bagian ini wajib diisi.");
                                } else if (e.target.validity.typeMismatch) {
                                    if (e.target.type === 'email') {
                                        e.target.setCustomValidity("Harap masukkan alamat email yang valid.");
                                    } else if (e.target.type === 'url') {
                                        e.target.setCustomValidity("Harap masukkan URL yang valid.");
                                    } else {
                                        e.target.setCustomValidity("Format masukan tidak sesuai.");
                                    }
                                } else {
                                    e.target.setCustomValidity("Masukan tidak valid.");
                                }
                            }
                        });
                        
                        el.addEventListener('input', function(e) {
                            e.target.setCustomValidity("");
                        });
                    });
                };
                
                // Run on load
                overrideValidationMessages();
                
                // Also observe DOM changes in case of dynamic forms (Livewire/Alpine)
                const observer = new MutationObserver((mutations) => {
                    overrideValidationMessages();
                });
                observer.observe(document.body, { childList: true, subtree: true });
            });
        </script>

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

                // Global Non-Intrusive Form Double-Submit & Loading Feedback Guard
                const unlockForm = (form) => {
                    if (!form) return;
                    delete form.dataset.sibimaSubmitting;
                    form.querySelectorAll('[data-sibima-locked="1"]').forEach((btn) => {
                        btn.disabled = false;
                        btn.removeAttribute('aria-busy');
                        btn.classList.remove('opacity-75', 'cursor-wait');
                        if (btn.dataset.sibimaOriginalHtml !== undefined) {
                            btn.innerHTML = btn.dataset.sibimaOriginalHtml;
                            delete btn.dataset.sibimaOriginalHtml;
                        }
                        delete btn.dataset.sibimaLocked;
                    });
                };

                document.addEventListener('submit', function(e) {
                    const form = e.target;
                    if (!form || !(form instanceof HTMLFormElement)) return;
                    if (e.defaultPrevented) return;

                    const method = (form.getAttribute('method') || 'GET').toUpperCase();
                    if (method === 'GET') return;
                    if (form.target === '_blank' || form.hasAttribute('data-no-loading')) return;

                    if (form.dataset.sibimaSubmitting === '1') {
                        e.preventDefault();
                        return;
                    }

                    form.dataset.sibimaSubmitting = '1';
                    const submitter = e.submitter || form.querySelector('button[type="submit"], button:not([type]), input[type="submit"]');

                    // Defer disabling to next tick so browser FormData still includes submitter name/value
                    window.setTimeout(() => {
                        if (submitter && !submitter.disabled && !submitter.hasAttribute('data-no-loading')) {
                            submitter.dataset.sibimaLocked = '1';
                            submitter.disabled = true;
                            submitter.setAttribute('aria-busy', 'true');
                            submitter.classList.add('opacity-75', 'cursor-wait');

                            const isIconOnly = submitter.tagName === 'BUTTON' && submitter.textContent.trim() === '';
                            if (submitter.tagName === 'BUTTON' && !isIconOnly) {
                                submitter.dataset.sibimaOriginalHtml = submitter.innerHTML;
                                const label = submitter.dataset.loadingText || 'Memproses...';
                                const spinner = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                                spinner.setAttribute('class', 'w-4 h-4 animate-spin shrink-0 inline-block mr-1.5');
                                spinner.setAttribute('fill', 'none');
                                spinner.setAttribute('viewBox', '0 0 24 24');
                                spinner.setAttribute('aria-hidden', 'true');
                                spinner.innerHTML = '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>';
                                const textSpan = document.createElement('span');
                                textSpan.textContent = label;
                                submitter.replaceChildren(spinner, textSpan);
                            }
                        }
                    }, 0);

                    // Safety fallback unlock for download responses that keep the page open
                    window.setTimeout(() => unlockForm(form), 12000);
                });

                // Restore locked forms when returning via browser back-forward cache (bfcache)
                window.addEventListener('pageshow', function() {
                    document.querySelectorAll('form[data-sibima-submitting="1"]').forEach(unlockForm);
                });
            });
        </script>

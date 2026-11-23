import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;

if (pusherKey || reverbKey) {
    Promise.all([import('laravel-echo'), import('pusher-js')])
        .then(([{ default: Echo }, { default: Pusher }]) => {
            window.Pusher = Pusher;

            if (pusherKey) {
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: pusherKey,
                    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'ap1',
                    wsHost: import.meta.env.VITE_PUSHER_HOST
                        ? import.meta.env.VITE_PUSHER_HOST
                        : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'ap1'}.pusher.com`,
                    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
                    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
                    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
                    enabledTransports: ['ws', 'wss'],
                });
            } else if (reverbKey) {
                window.Echo = new Echo({
                    broadcaster: 'reverb',
                    key: reverbKey,
                    wsHost: import.meta.env.VITE_REVERB_HOST,
                    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
                    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
                    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
                    enabledTransports: ['ws', 'wss'],
                });
            }

            window.dispatchEvent(new Event('sibima:echo-ready'));
        })
        .catch((e) => {
            console.warn('Echo initialization skipped or failed:', e);
        });
}

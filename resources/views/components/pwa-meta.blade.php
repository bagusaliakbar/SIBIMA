<!-- SIBIMA Progressive Web App (PWA) Meta & Service Worker Registration -->
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#ea580c">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="SIBIMA">
<meta name="application-name" content="SIBIMA">
<meta name="msapplication-TileColor" content="#0c1427">
<meta name="msapplication-TileImage" content="{{ asset('icons/icon-144x144.png') }}">

<!-- Apple Touch Icons for iOS Devices -->
<link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('icons/icon-152x152.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
<link rel="apple-touch-icon" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}">

<!-- Non-blocking Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(function (registration) {
                    // Check for SW updates periodically
                    registration.onupdatefound = function () {
                        const installingWorker = registration.installing;
                        if (installingWorker) {
                            installingWorker.onstatechange = function () {
                                if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // New update available; safe to auto-claim or refresh on next session
                                    console.log('[SIBIMA PWA] Versi baru aplikasi telah siap digunakan.');
                                }
                            };
                        }
                    };
                })
                .catch(function (error) {
                    console.warn('[SIBIMA PWA] Service worker registration error:', error);
                });
        });
    }
</script>

<!DOCTYPE html>
<html lang="id"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val); document.documentElement.classList.toggle('dark', val); })"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Verifikasi Dokumen') — SIBIMA FASILKOM UNSUB</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Theme Persistence Guard (Prevents FOUC) -->
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Official SIBIMA Typography: Inter -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 selection:bg-orange-100 selection:text-orange-900 dark:selection:bg-orange-950 dark:selection:text-orange-200 transition-colors duration-300">
    <!-- Accessible Top-Right Dark Mode Toggle -->
    <div class="fixed top-4 right-4 z-30">
        <button type="button"
                @click="darkMode = !darkMode"
                :aria-label="darkMode ? 'Aktifkan Mode Terang' : 'Aktifkan Mode Gelap'"
                :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
                class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:text-orange-600 dark:hover:text-orange-400 shadow-sm flex items-center justify-center transition-colors cursor-pointer">
            <svg x-show="darkMode" x-cloak aria-hidden="true" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg x-show="!darkMode" aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </button>
    </div>

    <main class="w-full flex flex-col items-center justify-center my-8">
        @yield('content')
    </main>
</body>
</html>

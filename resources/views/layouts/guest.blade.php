<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') - @endif{{ config('app.name', 'SIBIMA') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white shadow-sm border border-slate-100 overflow-hidden sm:rounded-md">
                {{ $slot }}
            </div>
        </div>
    </body>
    <!-- Script to translate HTML5 validation messages to Indonesian -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overrideValidationMessages = () => {
                const elements = document.querySelectorAll('input, select, textarea');
                elements.forEach(el => {
                    if (el.dataset.validationBound) return;
                    el.dataset.validationBound = "true";
                    
                    el.addEventListener('invalid', function(e) {
                        e.target.setCustomValidity("");
                        if (!e.target.validity.valid) {
                            if (e.target.validity.valueMissing) {
                                e.target.setCustomValidity("Bagian ini wajib diisi.");
                            } else if (e.target.type === 'email') {
                                e.target.setCustomValidity("Harap masukkan alamat email yang valid.");
                            } else if (e.target.type === 'url') {
                                e.target.setCustomValidity("Harap masukkan URL yang valid.");
                            } else {
                                e.target.setCustomValidity("Format masukan tidak sesuai.");
                            }
                        }
                    });
                    
                    el.addEventListener('input', function(e) {
                        e.target.setCustomValidity("");
                    });
                });
            };
            
            overrideValidationMessages();
            
            const observer = new MutationObserver((mutations) => {
                overrideValidationMessages();
            });
            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>
</html>

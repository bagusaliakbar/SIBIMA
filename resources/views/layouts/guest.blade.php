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

        <style>
            /* Force completely remove native browser focus ring / blue / purple outline */
            *, *::before, *::after {
                --tw-ring-color: rgba(249, 115, 22, 0.25) !important;
            }
            
            input, textarea, select, button {
                outline: none !important;
                -webkit-tap-highlight-color: transparent !important;
            }

            input:focus, 
            input:focus-visible, 
            input:active,
            textarea:focus, 
            textarea:focus-visible, 
            textarea:active,
            select:focus, 
            select:focus-visible, 
            select:active {
                outline: none !important;
                outline-width: 0px !important;
                outline-style: none !important;
                outline-color: transparent !important;
                border-color: #f97316 !important;
                box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.2) !important;
            }

            /* Override Browser Autofill Blue/Purple Background & Border */
            input:-webkit-autofill,
            input:-webkit-autofill:hover, 
            input:-webkit-autofill:focus,
            input:-webkit-autofill:active,
            textarea:-webkit-autofill,
            textarea:-webkit-autofill:hover,
            textarea:-webkit-autofill:focus,
            textarea:-webkit-autofill:active,
            select:-webkit-autofill,
            select:-webkit-autofill:hover,
            select:-webkit-autofill:focus,
            select:-webkit-autofill:active {
                -webkit-text-fill-color: #1e293b !important;
                -webkit-box-shadow: 0 0 0px 1000px #ffffff inset, 0 0 0 4px rgba(249, 115, 22, 0.2) !important;
                box-shadow: 0 0 0px 1000px #ffffff inset, 0 0 0 4px rgba(249, 115, 22, 0.2) !important;
                border-color: #f97316 !important;
                outline: none !important;
                transition: background-color 5000s ease-in-out 0s !important;
            }
        </style>
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-50/80 min-h-screen">
        <div class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 relative">
            <!-- Background Decorative Glow -->
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-orange-200/40 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="w-full sm:max-w-md relative z-10 px-6 sm:px-8 py-8 sm:py-10 bg-white shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden rounded-3xl transition-all">
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

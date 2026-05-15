<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Tidak Valid - SIBIMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-2xl shadow-rose-500/10 border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-br from-rose-600 to-rose-700 p-12 text-center">
            <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-3xl flex items-center justify-center mx-auto mb-6 border border-white/30">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h1 class="text-white text-2xl font-extrabold uppercase tracking-widest">Dokumen Tidak Valid</h1>
            <p class="text-rose-100 text-xs font-medium mt-2 uppercase tracking-tight">Verifikasi Gagal / Token Tidak Ditemukan</p>
        </div>

        <div class="p-10 text-center">
            <p class="text-slate-500 text-sm leading-relaxed mb-8">
                Maaf, token verifikasi yang Anda gunakan tidak terdaftar di sistem SIBIMA atau telah kedaluwarsa. Mohon pastikan Anda memindai QR Code asli dari dokumen resmi.
            </p>
            
            <a href="/" class="inline-flex items-center justify-center px-8 py-3 bg-slate-900 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                Kembali ke Beranda
            </a>
        </div>
        
        <div class="bg-slate-50 p-6 text-center border-t border-slate-100">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Layanan Verifikasi SIBIMA</p>
        </div>
    </div>
</body>
</html>

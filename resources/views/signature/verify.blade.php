<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan Digital - SIBIMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl shadow-slate-200/60 overflow-hidden border border-slate-100">
        <div class="p-8 text-center">
            <div class="mb-6 flex justify-center">
                @if($status === 'valid')
                    <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                @else
                    <div class="w-20 h-20 bg-rose-100 rounded-full flex items-center justify-center text-rose-600">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                @endif
            </div>

            <h1 class="text-2xl font-extrabold text-slate-800 mb-2">
                {{ $status === 'valid' ? 'Tanda Tangan Valid' : 'Verifikasi Gagal' }}
            </h1>
            <p class="text-sm text-slate-500 font-medium mb-8">
                {{ $message }}
            </p>

            @if($status === 'valid')
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 text-left space-y-4 mb-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Nama Pemilik</label>
                        <p class="text-sm font-bold text-slate-800">{{ $user->name }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Role</label>
                            <p class="text-sm font-bold text-slate-800 capitalize">{{ $user->role }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">NIDN / NPM</label>
                            <p class="text-sm font-bold text-slate-800">{{ $user->identifier }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Sistem</label>
                        <p class="text-sm font-bold text-emerald-600 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            SIBIMA Digital Signature Verified
                        </p>
                    </div>
                </div>
                
                @if($user->signature)
                    <div class="mb-8">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3 text-center">Spesimen Tanda Tangan</label>
                        <div class="p-4 bg-white rounded-xl border border-slate-200 inline-block shadow-inner">
                            <img src="{{ $user->signature_url }}" alt="Specimen" class="h-16 w-auto">
                        </div>
                    </div>
                @endif
            @endif

            <div class="pt-6 border-t border-slate-100">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-orange-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke SIBIMA
                </a>
            </div>
        </div>
        <div class="bg-slate-900 p-4 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Verified by SIBIMA Portal</p>
        </div>
    </div>
</body>
</html>

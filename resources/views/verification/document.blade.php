<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen - SIBIMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full bg-white rounded-[2.5rem] shadow-2xl shadow-indigo-500/10 border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 p-8 text-center relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-indigo-400/20 rounded-full -ml-12 -mb-12 blur-xl"></div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/30 shadow-lg p-2">
                    <img src="{{ asset('logo_unsub.png') }}" class="w-full h-auto object-contain" alt="Unsub Logo">
                </div>
                <h1 class="text-white text-xl font-extrabold uppercase tracking-widest">Dokumen Terverifikasi</h1>
                <p class="text-indigo-100 text-xs font-medium mt-1 uppercase tracking-tight">Sistem Informasi Bimbingan Mahasiswa (SIBIMA)</p>
            </div>
        </div>

        <div class="p-8">
            <div class="space-y-6">
                <!-- Info Section -->
                <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Informasi Akademik</p>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-xs font-bold text-slate-500 whitespace-nowrap">Nama Mahasiswa</span>
                            <span class="text-xs font-black text-slate-800 text-right">{{ $detail->thesis->student->name }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-xs font-bold text-slate-500 whitespace-nowrap">NPM</span>
                            <span class="text-xs font-black text-slate-800 text-right font-mono">{{ $detail->thesis->student->identifier }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4 border-t border-slate-200 pt-4">
                            <span class="text-xs font-bold text-slate-500 whitespace-nowrap">Jenis Dokumen</span>
                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-black rounded-md border border-indigo-200">BERITA ACARA {{ strtoupper($type) }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-xs font-bold text-slate-500 whitespace-nowrap">Waktu Pelaksanaan</span>
                            <span class="text-xs font-black text-slate-800 text-right">{{ $detail->start_time->locale('id')->translatedFormat('d F Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Examiners & Supervisors -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Tim Akademik Terverifikasi</p>
                    
                    <div class="space-y-3">
                        <!-- Head of Board -->
                        <div class="flex items-center gap-3 p-3 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-800">Bagus Ali Akbar, S.SI., M.Kom</p>
                                <p class="text-[8px] font-bold text-indigo-600 uppercase tracking-widest">Ketua Program Studi</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Supervisors -->
                            <div class="space-y-3">
                                @if($detail->thesis->pembimbing1)
                                    <div class="flex items-center gap-3 p-3 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                                        <div class="w-7 h-7 rounded-full bg-emerald-500 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-black text-slate-800 line-clamp-1">{{ $detail->thesis->pembimbing1->name }}</p>
                                            <p class="text-[7px] font-bold text-emerald-600 uppercase tracking-widest leading-none">Pembimbing I</p>
                                        </div>
                                    </div>
                                @endif

                                @if($detail->thesis->pembimbing2)
                                    <div class="flex items-center gap-3 p-3 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                                        <div class="w-7 h-7 rounded-full bg-emerald-500 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-black text-slate-800 line-clamp-1">{{ $detail->thesis->pembimbing2->name }}</p>
                                            <p class="text-[7px] font-bold text-emerald-600 uppercase tracking-widest leading-none">Pembimbing II</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Examiners -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 p-3 bg-blue-50/50 rounded-2xl border border-blue-100">
                                    <div class="w-7 h-7 rounded-full bg-blue-500 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-800 line-clamp-1">{{ $detail->examiner1->name }}</p>
                                        <p class="text-[7px] font-bold text-blue-600 uppercase tracking-widest leading-none">Penguji I</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-3 bg-blue-50/50 rounded-2xl border border-blue-100">
                                    <div class="w-7 h-7 rounded-full bg-blue-500 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-800 line-clamp-1">{{ $detail->examiner2->name }}</p>
                                        <p class="text-[7px] font-bold text-blue-600 uppercase tracking-widest leading-none">Penguji II</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-[10px] font-medium text-slate-400 italic">
                        * Dokumen ini dihasilkan secara otomatis oleh sistem SIBIMA dan telah tervalidasi secara digital.
                    </p>
                    <p class="text-[9px] font-bold text-slate-300 mt-4 uppercase tracking-[0.2em]">
                        Token: {{ substr($detail->verification_token, 0, 8) }}...{{ substr($detail->verification_token, -8) }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-slate-50 p-4 text-center border-t border-slate-100">
            <a href="https://sibima.test" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-800 transition-colors">
                Kunjungi Portal Utama SIBIMA
            </a>
        </div>
    </div>
</body>
</html>

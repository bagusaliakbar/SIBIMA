<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Keterangan Lulus (SKL) - SIBIMA FASILKOM UNSUB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4 selection:bg-emerald-100 selection:text-emerald-900">
    <div class="max-w-2xl w-full bg-white rounded-[2.5rem] shadow-2xl shadow-emerald-500/10 border border-slate-100 overflow-hidden my-8">
        
        <!-- Header Terverifikasi -->
        <div class="bg-gradient-to-br from-emerald-600 via-emerald-600 to-teal-700 p-8 sm:p-10 text-center relative overflow-hidden text-white">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-36 h-36 bg-emerald-400/20 rounded-full -ml-16 -mb-16 blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-3">
                <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto shadow-xl p-2.5">
                    <img src="{{ asset('logo_unsub.png') }}" class="w-full h-auto object-contain" alt="Logo UNSUB">
                </div>
                
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider text-emerald-100 border border-white/25">
                    <svg class="w-4 h-4 text-emerald-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    Dokumen Resmi Terverifikasi
                </div>

                <h1 class="text-xl sm:text-2xl font-black tracking-tight">SURAT KETERANGAN LULUS (SKL)</h1>
                <p class="text-xs sm:text-sm text-emerald-100/90 font-medium max-w-md mx-auto">
                    Fakultas Ilmu Komputer &mdash; Universitas Subang
                </p>
            </div>
        </div>

        <!-- Body Details -->
        <div class="p-6 sm:p-10 space-y-6">
            
            <!-- Dokumen Information Box -->
            <div class="bg-emerald-50/60 border border-emerald-200/80 rounded-2xl p-5 space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-800">Nomor Surat Resmi</span>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-mono font-bold text-xs shadow-xs">
                        {{ $graduation->skl_number }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-emerald-200/50">
                    <span>Tanggal Yudisium / Kelulusan:</span>
                    <strong class="text-slate-800 font-bold">{{ $graduation->formatted_graduation_date }}</strong>
                </div>
            </div>

            <!-- Mahasiswa Detail Card -->
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 space-y-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Identitas Mahasiswa Lulusan</p>
                
                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-slate-500 font-medium">Nama Lengkap</span>
                        <strong class="text-slate-900 font-black text-right">{{ $graduation->student->name }}</strong>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-slate-500 font-medium">Nomor Pokok Mahasiswa (NPM)</span>
                        <span class="text-slate-900 font-black font-mono text-right">{{ $graduation->student->identifier }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-slate-500 font-medium">Program Studi</span>
                        @php
                            $id = strtoupper(trim($graduation->student->identifier ?? ''));
                            $pName = str_starts_with($id, 'D1A') ? 'Sistem Informasi' : (str_starts_with($id, 'D1B') ? 'Teknik Informatika' : 'Ilmu Komputer');
                        @endphp
                        <strong class="text-slate-900 font-bold text-right">{{ $pName }} (S1)</strong>
                    </div>
                    @if($graduation->gpa)
                    <div class="flex justify-between items-start gap-4 border-t border-slate-200/80 pt-3">
                        <span class="text-slate-500 font-medium">IPK Kelulusan</span>
                        <span class="text-emerald-700 font-black text-base">{{ number_format($graduation->gpa, 2) }}</span>
                    </div>
                    @endif
                    @if($graduation->predicate)
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-slate-500 font-medium">Predikat Kelulusan</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs">
                            {{ $graduation->predicate }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Thesis Detail -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-2">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Judul Skripsi Disahkan</p>
                <h3 class="text-sm sm:text-base font-bold text-slate-800 italic leading-snug">
                    "{{ $graduation->thesis->display_title }}"
                </h3>
                <div class="pt-3 border-t border-slate-100 text-xs text-slate-500 space-y-1">
                    <p><span class="font-medium">Pembimbing 1:</span> <strong class="text-slate-700">{{ $graduation->thesis->pembimbing1->name ?? '-' }}</strong></p>
                    <p><span class="font-medium">Pembimbing 2:</span> <strong class="text-slate-700">{{ $graduation->thesis->pembimbing2->name ?? '-' }}</strong></p>
                </div>
            </div>

            <!-- Verified By Section -->
            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Disetujui & Diterbitkan Oleh</p>
                        <p class="font-bold text-slate-800">{{ $graduation->approver->name ?? $kaprodi->name ?? 'Ketua Program Studi' }}</p>
                        <p class="text-[11px] text-slate-500">{{ $graduation->approved_at ? $graduation->approved_at->locale('id')->translatedFormat('d F Y, H:i') : '-' }} WIB</p>
                    </div>
                </div>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                    &larr; Masuk ke Portal SIBIMA FASILKOM
                </a>
            </div>
        </div>
    </div>
</body>
</html>

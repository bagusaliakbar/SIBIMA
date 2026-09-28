<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi SK Dosen Pembimbing Skripsi - SIBIMA FASILKOM UNSUB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4 selection:bg-orange-100 selection:text-orange-900">
    <div class="max-w-3xl w-full bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/60 border border-slate-200/80 overflow-hidden my-8">
        
        <!-- Header Terverifikasi (Solid White Theme) -->
        <div class="bg-white p-8 sm:p-10 text-center relative border-b border-slate-100">
            <div class="space-y-3.5 max-w-xl mx-auto">
                <div class="w-20 h-20 bg-white border border-slate-200/80 rounded-3xl flex items-center justify-center mx-auto shadow-xs p-2.5">
                    @if(file_exists(public_path('logo_unsub.png')))
                        <img src="{{ asset('logo_unsub.png') }}" class="w-full h-auto object-contain" alt="Logo UNSUB">
                    @elseif(file_exists(public_path('images/logo_unsub.png')))
                        <img src="{{ asset('images/logo_unsub.png') }}" class="w-full h-auto object-contain" alt="Logo UNSUB">
                    @else
                        <span class="text-orange-600 font-black text-xl">UNSUB</span>
                    @endif
                </div>
                
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 rounded-full text-xs font-bold uppercase tracking-wider text-emerald-700 border border-emerald-200/80">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>Dokumen Resmi Terverifikasi</span>
                </div>

                <h1 class="text-xl sm:text-2xl font-black tracking-tight uppercase text-slate-900">
                    {{ $decree->target_type === 'individual_dosen' ? 'SURAT TUGAS PEMBIMBING SKRIPSI' : 'SK DOSEN PEMBIMBING SKRIPSI' }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium max-w-md mx-auto">
                    Fakultas Ilmu Komputer &mdash; Universitas Subang
                </p>
            </div>
        </div>

        <!-- Body Details -->
        <div class="p-6 sm:p-10 space-y-6">
            
            <!-- Dokumen Information Box -->
            <div class="bg-orange-50/70 border border-orange-200/80 rounded-2xl p-5 space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-orange-800">
                        {{ $decree->target_type === 'individual_dosen' ? 'Nomor Surat Tugas' : 'Nomor Surat Keputusan' }}
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-orange-600 text-white font-mono font-bold text-xs shadow-xs">
                        {{ $decree->decree_number }}
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600 pt-2 border-t border-orange-200/50">
                    <div>
                        <span>Tanggal Ditetapkan:</span>
                        <strong class="text-slate-800 font-bold block mt-0.5">{{ $decree->formatted_decree_date }}</strong>
                    </div>
                    <div>
                        <span>Tahun Akademik:</span>
                        <strong class="text-slate-800 font-bold block mt-0.5">{{ $decree->academic_year }} (Semester {{ $decree->semester }})</strong>
                    </div>
                </div>
            </div>

            @if($decree->target_type === 'individual_dosen' && $decree->dosen)
                <!-- Dosen Pembimbing Info (For Surat Tugas) -->
                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-600 flex items-center justify-center font-bold shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Dosen Pembimbing Ditugaskan</p>
                            <p class="font-bold text-slate-800 text-sm">{{ $decree->dosen->name }}</p>
                            <p class="text-[11px] text-slate-500">NIDN: {{ $decree->dosen->identifier ?? '-' }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-100 text-orange-800">
                        Dosen Tetap
                    </span>
                </div>
            @endif

            <!-- Signatory Info -->
            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-600 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Pejabat Pengesah / Penandatangan</p>
                        <p class="font-bold text-slate-800 text-sm">{{ $decree->signatory_name }}</p>
                        <p class="text-[11px] text-slate-500">{{ $decree->signatory_title }} {{ $decree->signatory_identifier ? '('.$decree->signatory_identifier.')' : '' }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800">
                    {{ $decree->total_students }} Mahasiswa Disahkan
                </span>
            </div>

            <!-- Table of Enrolled Students -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Daftar Mahasiswa & Dosen Pembimbing</p>
                </div>

                <div class="border border-slate-200/80 rounded-2xl overflow-hidden max-h-96 overflow-y-auto divide-y divide-slate-100">
                    @foreach($decree->theses_data as $idx => $row)
                        <div class="p-4 hover:bg-slate-50 transition text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-slate-900 text-sm">{{ $idx + 1 }}. {{ $row['student_name'] ?? '-' }}</span>
                                <span class="font-mono text-slate-500 font-bold">{{ $row['student_npm'] ?? '-' }}</span>
                            </div>
                            <p class="text-slate-600 italic">"{{ $row['title'] ?? '-' }}"</p>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-slate-500 pt-1">
                                <span>Pembimbing 1: <strong class="text-slate-800">{{ $row['pembimbing1_name'] ?? '-' }}</strong></span>
                                <span>Pembimbing 2: <strong class="text-slate-800">{{ $row['pembimbing2_name'] ?? '-' }}</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer note & login -->
            <div class="text-center pt-2 space-y-2">
                <p class="text-[11px] text-slate-400">
                    Sistem Informasi Bimbingan Mahasiswa (SIBIMA) &bull; Fakultas Ilmu Komputer Universitas Subang
                </p>
                <div>
                    <a href="{{ route('login') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700 hover:underline">
                        &larr; Masuk ke Portal SIBIMA FASILKOM
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

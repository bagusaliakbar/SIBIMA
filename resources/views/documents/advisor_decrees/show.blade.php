<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <x-breadcrumb :items="[
                ['label' => 'Dokumen & Legalitas', 'route' => route('advisor-decrees.index')],
                ['label' => $advisorDecree->decree_number, 'route' => null]
            ]" />

            <div class="flex items-center gap-2">
                <a href="{{ route('advisor-decrees.pdf', $advisorDecree) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-black uppercase tracking-wider transition shadow-lg shadow-orange-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>{{ $advisorDecree->target_type === 'individual_dosen' ? 'Cetak Surat Tugas (PDF)' : 'Cetak SK PDF Resmi' }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Metadata & Verification Box -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Details -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-black text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                    Informasi Legalitas {{ $advisorDecree->target_type === 'individual_dosen' ? 'Surat Tugas Pembimbing' : 'Surat Keputusan Dekan' }}
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400">Nomor Dokumen Resmi:</span>
                        <div class="font-black text-slate-800 dark:text-slate-200 text-sm mt-0.5">{{ $advisorDecree->decree_number }}</div>
                    </div>
                    <div>
                        <span class="text-slate-400">Tanggal Ditetapkan:</span>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $advisorDecree->formatted_decree_date }}</div>
                    </div>
                    <div>
                        <span class="text-slate-400">Tahun Akademik / Semester:</span>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">T.A {{ $advisorDecree->academic_year }} - Semester {{ $advisorDecree->semester }}</div>
                    </div>
                    <div>
                        <span class="text-slate-400">Jenis Dokumen:</span>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                            @if($advisorDecree->target_type === 'individual_dosen' && $advisorDecree->dosen)
                                Surat Tugas Khusus Dosen: {{ $advisorDecree->dosen->name }}
                            @else
                                SK Kolektif Penetapan Seluruh Mahasiswa
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400">Pejabat Penandatangan:</span>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                            {{ $advisorDecree->signatory_name }}
                            @if($advisorDecree->signatory_identifier)
                                <span class="text-slate-400">({{ $advisorDecree->signatory_identifier }})</span>
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-500">{{ $advisorDecree->signatory_title }}</div>
                    </div>
                    <div>
                        <span class="text-slate-400">Total Mahasiswa Terlampir:</span>
                        <div class="font-black text-orange-600 text-base mt-0.5">{{ $advisorDecree->total_students }} Mahasiswa</div>
                    </div>
                </div>

                @if($advisorDecree->notes)
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-xs text-slate-400 font-bold">Catatan Pengarsipan:</span>
                        <p class="text-xs text-slate-600 dark:text-slate-300 italic mt-0.5">{{ $advisorDecree->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- QR Code Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center space-y-3">
                <div class="p-3 bg-white rounded-2xl shadow-inner border border-slate-200">
                    <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(130)->margin(0)->generate(route('sk-pembimbing.verify', $advisorDecree->verification_token))) }}" 
                         alt="QR Code Verifikasi SK" class="w-32 h-32">
                </div>

                <div>
                    <span class="text-[11px] font-black uppercase text-slate-500 dark:text-slate-400 tracking-wider">Verifikasi Keaslian Dokumen</span>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                        Pindai QR Code untuk memeriksa keaslian SK ini secara publik di SIBIMA FASILKOM UNSUB.
                    </p>
                </div>

                <div class="w-full pt-2">
                    <a href="{{ route('sk-pembimbing.verify', $advisorDecree->verification_token) }}" target="_blank"
                       class="inline-block w-full py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition">
                        Buka Halaman Verifikasi Publik ↗
                    </a>
                </div>
            </div>
        </div>

        <!-- Table of Attached Students & Advisors -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden space-y-4">
            <div class="p-6 pb-0 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Lampiran Penetapan Dosen Pembimbing Skripsi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Daftar mahasiswa, judul skripsi, dan dosen pembimbing yang sah.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400">
                    {{ count($advisorDecree->theses_data) }} Entri Tercatat
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px] border-y border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-3.5 w-12 text-center">No</th>
                            <th class="px-6 py-3.5 w-60">Mahasiswa (NPM)</th>
                            <th class="px-6 py-3.5">Judul Skripsi</th>
                            <th class="px-6 py-3.5 w-56">Dosen Pembimbing 1</th>
                            <th class="px-6 py-3.5 w-56">Dosen Pembimbing 2</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($advisorDecree->theses_data as $index => $item)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-black text-slate-900 dark:text-white">{{ $item['student_name'] ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $item['student_npm'] ?? '-' }}</div>
                                    @if(!empty($item['student_cohort']))
                                        <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold">
                                            Angkatan {{ $item['student_cohort'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-slate-700 dark:text-slate-300 italic font-medium leading-relaxed">
                                        "{{ $item['title'] ?? '-' }}"
                                    </div>
                                    @if(!empty($item['topic']) && $item['topic'] !== '-')
                                        <div class="text-[10px] text-orange-600 font-bold mt-1">
                                            Bidang/Topik: {{ $item['topic'] }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $item['pembimbing1_name'] ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500">NIDN: {{ $item['pembimbing1_nidn'] ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $item['pembimbing2_name'] ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500">NIDN: {{ $item['pembimbing2_nidn'] ?? '-' }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

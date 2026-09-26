<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <x-breadcrumb :items="[
                ['label' => 'Broadcast WhatsApp', 'route' => route('wa-broadcasts.index')],
                ['label' => 'Buat Siaran Baru', 'route' => null]
            ]" />

            <a href="{{ route('wa-broadcasts.index') }}" 
               class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700">
                &larr; Kembali ke Riwayat
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-6" x-data="waBroadcastComposer()">
        <!-- Master Form Wrapper -->
        <form action="{{ route('wa-broadcasts.store') }}" method="POST" @submit="handleSubmit($event)">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Filter & Message Composer (7 cols) -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- Section 1: Target Audience Selection -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-sm space-y-6">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 text-xs font-black flex items-center justify-center">1</span>
                                <h2 class="text-base font-black text-slate-900 dark:text-white">Pilih Target Sasaran Siaran</h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Tentukan kelompok mahasiswa atau dosen yang menjadi sasaran pengiriman pesan.</p>
                        </div>

                        <!-- Target Radio Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- 1. Belum Seminar -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   :class="targetType === 'mahasiswa_belum_seminar' ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-950/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <input type="radio" name="target_type" value="mahasiswa_belum_seminar" x-model="targetType" @change="fetchRecipients()" class="sr-only">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>🎓</span> Belum Seminar
                                    </span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                          :class="targetType === 'mahasiswa_belum_seminar' ? 'border-orange-500 bg-orange-500 text-white' : 'border-slate-300 dark:border-slate-600'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="targetType === 'mahasiswa_belum_seminar'"></span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    Mahasiswa skripsi aktif yang belum mendaftar/ACC seminar proposal.
                                </p>
                            </label>

                            <!-- 2. Bimbingan Pasif -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   :class="targetType === 'mahasiswa_bimbingan_pasif' ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-950/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <input type="radio" name="target_type" value="mahasiswa_bimbingan_pasif" x-model="targetType" @change="fetchRecipients()" class="sr-only">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>⏳</span> Bimbingan Pasif
                                    </span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                          :class="targetType === 'mahasiswa_bimbingan_pasif' ? 'border-orange-500 bg-orange-500 text-white' : 'border-slate-300 dark:border-slate-600'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="targetType === 'mahasiswa_bimbingan_pasif'"></span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    Mahasiswa aktif yang tidak bimbingan lebih dari 30 hari (mangkir).
                                </p>
                            </label>

                            <!-- 3. Semester Kritis -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   :class="targetType === 'mahasiswa_kritis' ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-950/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <input type="radio" name="target_type" value="mahasiswa_kritis" x-model="targetType" @change="fetchRecipients()" class="sr-only">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-xs font-black text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                                        <span>🚨</span> Semester Kritis
                                    </span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                          :class="targetType === 'mahasiswa_kritis' ? 'border-orange-500 bg-orange-500 text-white' : 'border-slate-300 dark:border-slate-600'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="targetType === 'mahasiswa_kritis'"></span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    Mahasiswa semester 13-14+ yang terancam Drop-Out (DO alert).
                                </p>
                            </label>

                            <!-- 4. Belum Sidang -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   :class="targetType === 'mahasiswa_belum_sidang' ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-950/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <input type="radio" name="target_type" value="mahasiswa_belum_sidang" x-model="targetType" @change="fetchRecipients()" class="sr-only">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>📝</span> Belum Sidang
                                    </span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                          :class="targetType === 'mahasiswa_belum_sidang' ? 'border-orange-500 bg-orange-500 text-white' : 'border-slate-300 dark:border-slate-600'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="targetType === 'mahasiswa_belum_sidang'"></span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    Lulus seminar proposal tapi belum mendaftar sidang skripsi.
                                </p>
                            </label>

                            <!-- 5. Belum Mengajukan Skripsi -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   :class="targetType === 'mahasiswa_belum_skripsi' ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-950/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <input type="radio" name="target_type" value="mahasiswa_belum_skripsi" x-model="targetType" @change="fetchRecipients()" class="sr-only">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>📋</span> Belum Judul
                                    </span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                          :class="targetType === 'mahasiswa_belum_skripsi' ? 'border-orange-500 bg-orange-500 text-white' : 'border-slate-300 dark:border-slate-600'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="targetType === 'mahasiswa_belum_skripsi'"></span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    Mahasiswa angkatan yang belum memiliki pengajuan skripsi.
                                </p>
                            </label>

                            <!-- 6. Dosen Pembimbing Aktif -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   :class="targetType === 'dosen_pembimbing_aktif' ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-950/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <input type="radio" name="target_type" value="dosen_pembimbing_aktif" x-model="targetType" @change="fetchRecipients()" class="sr-only">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>👨‍🏫</span> Dosen Pembimbing
                                    </span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                          :class="targetType === 'dosen_pembimbing_aktif' ? 'border-orange-500 bg-orange-500 text-white' : 'border-slate-300 dark:border-slate-600'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="targetType === 'dosen_pembimbing_aktif'"></span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    Seluruh dosen yang sedang aktif membimbing mahasiswa skripsi.
                                </p>
                            </label>

                            <!-- 7. Dosen Penguji Gelombang -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   :class="targetType === 'dosen_penguji_gelombang' ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-950/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <input type="radio" name="target_type" value="dosen_penguji_gelombang" x-model="targetType" @change="fetchRecipients()" class="sr-only">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>⚖️</span> Dosen Penguji
                                    </span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                          :class="targetType === 'dosen_penguji_gelombang' ? 'border-orange-500 bg-orange-500 text-white' : 'border-slate-300 dark:border-slate-600'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="targetType === 'dosen_penguji_gelombang'"></span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    Dosen penguji seminar/sidang pada gelombang tertentu.
                                </p>
                            </label>

                            <!-- 8. Seluruh Mahasiswa Aktif -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   :class="targetType === 'all_mahasiswa_aktif' ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-950/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <input type="radio" name="target_type" value="all_mahasiswa_aktif" x-model="targetType" @change="fetchRecipients()" class="sr-only">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>👥</span> Semua Mahasiswa
                                    </span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                          :class="targetType === 'all_mahasiswa_aktif' ? 'border-orange-500 bg-orange-500 text-white' : 'border-slate-300 dark:border-slate-600'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="targetType === 'all_mahasiswa_aktif'"></span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    Kirim siaran pengumuman ke seluruh mahasiswa aktif.
                                </p>
                            </label>
                        </div>

                        <!-- Secondary Filters -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/80 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Filter Angkatan -->
                            <div x-show="['mahasiswa_belum_seminar', 'mahasiswa_bimbingan_pasif', 'mahasiswa_belum_sidang', 'mahasiswa_belum_skripsi', 'all_mahasiswa_aktif'].includes(targetType)">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Filter Tahun Angkatan
                                </label>
                                <select name="cohort" x-model="cohort" @change="fetchRecipients()"
                                        class="w-full text-xs font-medium rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:border-orange-500 focus:ring-orange-500">
                                    <option value="all">Semua Angkatan</option>
                                    @foreach($cohorts as $year)
                                        <option value="{{ $year }}">Angkatan {{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Hari Mangkir -->
                            <div x-show="targetType === 'mahasiswa_bimbingan_pasif'">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Batas Hari Mangkir
                                </label>
                                <select name="days" x-model="days" @change="fetchRecipients()"
                                        class="w-full text-xs font-medium rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:border-orange-500 focus:ring-orange-500">
                                    <option value="14">> 14 Hari Tanpa Bimbingan</option>
                                    <option value="30">> 30 Hari Tanpa Bimbingan (Rekomendasi)</option>
                                    <option value="60">> 60 Hari Tanpa Bimbingan</option>
                                </select>
                            </div>

                            <!-- Filter Gelombang untuk Dosen Penguji -->
                            <div x-show="targetType === 'dosen_penguji_gelombang'" class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Pilih Gelombang Ujian
                                </label>
                                <select name="wave_id" x-model="waveId" @change="fetchRecipients()"
                                        class="w-full text-xs font-medium rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Gelombang Sedang Aktif</option>
                                    @foreach($waves as $wave)
                                        <option value="{{ $wave->id }}">{{ $wave->name }} ({{ ucfirst($wave->type) }}) - {{ $wave->start_date ? \Carbon\Carbon::parse($wave->start_date)->format('d M Y') : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Real-time Audience Review Table -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-sm space-y-4">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 text-xs font-black flex items-center justify-center">2</span>
                                    <h2 class="text-base font-black text-slate-900 dark:text-white">Daftar Sasaran Terpilih</h2>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tinjau daftar kontak yang akan menerima siaran WhatsApp ini.</p>
                            </div>

                            <!-- Live Counter Badges -->
                            <div class="flex items-center gap-2" x-show="!isLoadingRecipients">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span x-text="validPhoneCount + ' Siap Kirim'"></span>
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300" x-show="missingPhoneCount > 0">
                                    <span x-text="missingPhoneCount + ' Tanpa WA'"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div x-show="isLoadingRecipients" class="py-8 text-center text-slate-400">
                            <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="text-xs font-bold">Menganalisis data sasaran...</span>
                        </div>

                        <!-- Recipient List Table -->
                        <div x-show="!isLoadingRecipients" class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden max-h-72 overflow-y-auto custom-scrollbar">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700 sticky top-0 text-[10px] uppercase">
                                    <tr>
                                        <th class="p-3 w-10 text-center">
                                            <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected()" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                        </th>
                                        <th class="p-3">Nama & Identitas</th>
                                        <th class="p-3">Nomor WhatsApp</th>
                                        <th class="p-3">Keterangan Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                    <template x-for="rec in recipients" :key="rec.user_id">
                                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">
                                            <td class="p-3 text-center">
                                                <input type="checkbox" name="selected_user_ids[]" :value="rec.user_id" x-model="selectedUserIds" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer">
                                            </td>
                                            <td class="p-3 whitespace-nowrap">
                                                <div class="font-bold text-slate-900 dark:text-white" x-text="rec.name"></div>
                                                <div class="text-[10px] text-slate-500" x-text="rec.identifier + (rec.cohort && rec.cohort !== '-' ? ' • Angk. ' + rec.cohort : '')"></div>
                                            </td>
                                            <td class="p-3 whitespace-nowrap">
                                                <template x-if="rec.has_phone">
                                                    <span class="inline-flex items-center gap-1 font-mono text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">
                                                        <span>📱</span> <span x-text="rec.phone"></span>
                                                    </span>
                                                </template>
                                                <template x-if="!rec.has_phone">
                                                    <span class="text-[10px] text-rose-500 font-bold bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded">
                                                        Nomor Kosong
                                                    </span>
                                                </template>
                                            </td>
                                            <td class="p-3 text-slate-600 dark:text-slate-300 text-[11px]" x-text="rec.status_info"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="recipients.length === 0">
                                        <td colspan="4" class="p-6 text-center text-slate-400">
                                            Tidak ada kontak yang cocok dengan filter sasaran ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Section 3: Message Composer -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-sm space-y-5">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 text-xs font-black flex items-center justify-center">3</span>
                                <h2 class="text-base font-black text-slate-900 dark:text-white">Tulis Pesan Siaran</h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Susun teks WhatsApp dengan variabel dinamis personal untuk setiap penerima.</p>
                        </div>

                        <!-- Judul Broadcast -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Judul Siaran (Arsip Internal Prodi) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="title" x-model="title" required placeholder="Contoh: Pengingat Pendaftaran Seminar Proposal Gelombang 2"
                                   class="w-full text-xs font-medium rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <!-- Template Quick Presets -->
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Variabel Dinamis Tersedia:</span>
                            <button type="button" @click="applyPresetTemplate()" class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline cursor-pointer flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Muat Contoh Pesan Siaran</span>
                            </button>
                        </div>

                        <!-- Tag Pills Button -->
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" @click="insertVariable('{nama}')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-orange-100 hover:text-orange-700 dark:hover:bg-orange-950 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-mono font-bold transition-all cursor-pointer">
                                {nama}
                            </button>
                            <button type="button" @click="insertVariable('{npm}')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-orange-100 hover:text-orange-700 dark:hover:bg-orange-950 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-mono font-bold transition-all cursor-pointer">
                                {npm}
                            </button>
                            <button type="button" @click="insertVariable('{judul}')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-orange-100 hover:text-orange-700 dark:hover:bg-orange-950 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-mono font-bold transition-all cursor-pointer">
                                {judul}
                            </button>
                            <button type="button" @click="insertVariable('{pembimbing_1}')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-orange-100 hover:text-orange-700 dark:hover:bg-orange-950 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-mono font-bold transition-all cursor-pointer">
                                {pembimbing_1}
                            </button>
                            <button type="button" @click="insertVariable('{terakhir_bimbingan}')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-orange-100 hover:text-orange-700 dark:hover:bg-orange-950 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-mono font-bold transition-all cursor-pointer">
                                {terakhir_bimbingan}
                            </button>
                            <button type="button" @click="insertVariable('{hari_tanpa_bimbingan}')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-orange-100 hover:text-orange-700 dark:hover:bg-orange-950 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-mono font-bold transition-all cursor-pointer">
                                {hari_tanpa_bimbingan}
                            </button>
                            <button type="button" @click="insertVariable('{link_seminar}')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-orange-100 hover:text-orange-700 dark:hover:bg-orange-950 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-mono font-bold transition-all cursor-pointer">
                                {link_seminar}
                            </button>
                            <button type="button" @click="insertVariable('{link_login}')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/60 hover:bg-orange-100 hover:text-orange-700 dark:hover:bg-orange-950 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-mono font-bold transition-all cursor-pointer">
                                {link_login}
                            </button>
                        </div>

                        <!-- Message Textarea -->
                        <div>
                            <textarea id="messageTemplateArea" name="message_template" x-model="messageTemplate" required rows="9"
                                      placeholder="Tulis pesan siaran Anda di sini... Gunakan variabel seperti {nama} agar pesan personal."
                                      class="w-full text-xs font-mono rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:border-orange-500 focus:ring-orange-500 leading-relaxed"></textarea>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 mt-1">
                                <span>Tip: Gunakan tanda bintang untuk *tebal*, garis bawah untuk _miring_.</span>
                                <span x-text="messageTemplate.length + ' karakter'"></span>
                            </div>
                        </div>

                        <!-- Delay Setting (Anti-Spam) -->
                        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/60 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="text-amber-600 text-lg">🛡️</span>
                                <div>
                                    <div class="text-xs font-black text-amber-950 dark:text-amber-200">Proteksi Anti-Blokir Gateway WA</div>
                                    <div class="text-[11px] text-amber-800 dark:text-amber-300">Jeda acak antar pesan mencegah nomor SIBIMA terdeteksi broadcast massal.</div>
                                </div>
                            </div>

                            <select name="delay_seconds" x-model="delaySeconds"
                                    class="text-xs font-bold rounded-xl border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-800 text-amber-900 dark:text-amber-200 py-1.5 px-3">
                                <option value="3">Jeda 3 Detik</option>
                                <option value="4" selected>Jeda 4 Detik (Disarankan)</option>
                                <option value="6">Jeda 6 Detik</option>
                                <option value="10">Jeda 10 Detik (Sangat Aman)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sticky Mobile WhatsApp Preview & Action Box (5 cols) -->
                <div class="lg:col-span-5 space-y-6 sticky top-24">
                    
                    <!-- WhatsApp Simulator Frame -->
                    <div class="bg-slate-900 rounded-3xl p-4 shadow-2xl border border-slate-800 text-slate-100 overflow-hidden">
                        <!-- Phone Header Bar -->
                        <div class="bg-emerald-700 -mx-4 -mt-4 px-4 py-3 flex items-center justify-between text-white shadow-md">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-black text-xs">
                                    SB
                                </div>
                                <div>
                                    <div class="text-xs font-black leading-tight">SIBIMA FASILKOM</div>
                                    <div class="text-[10px] text-emerald-200">Official Notification Bot</div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full">Pratinjau Langsung</span>
                        </div>

                        <!-- Chat Area Background (WhatsApp subtle pattern style) -->
                        <div class="py-5 px-2 bg-[#0b141a] min-h-[300px] flex flex-col justify-end text-slate-900">
                            <!-- Recipient simulation header -->
                            <div class="text-center mb-3">
                                <span class="bg-[#182229] text-slate-400 text-[10px] font-medium px-3 py-1 rounded-lg">
                                    Simulasi Penerima: <strong class="text-slate-200" x-text="firstRecipientName"></strong>
                                </span>
                            </div>

                            <!-- Chat Bubble -->
                            <div class="bg-[#005c4b] text-white p-3.5 rounded-2xl rounded-tr-xs shadow-md max-w-[95%] ml-auto text-xs leading-relaxed space-y-2 relative">
                                <div class="whitespace-pre-wrap font-sans text-xs break-words" x-html="formattedPreviewMessage"></div>
                                <div class="flex items-center justify-end gap-1 text-[10px] text-emerald-200 mt-1">
                                    <span>{{ now()->format('H:i') }}</span>
                                    <span class="text-cyan-300 font-bold">✓✓</span>
                                </div>
                            </div>
                        </div>

                        <!-- Simulator Footer Info -->
                        <div class="pt-3 px-2 text-[10px] text-slate-400 text-center border-t border-slate-800">
                            *Teks di atas adalah simulasi personalisasi otomatis sesuai variabel data penerima.
                        </div>
                    </div>

                    <!-- Test Send Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-sm space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="text-orange-500 font-bold text-sm">🧪</span>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Uji Coba Pesan (Test Send)</h3>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Kirimkan 1 pesan simulasi ke nomor WhatsApp Anda sebelum mengirim ke seluruh sasaran.</p>

                        <div class="flex items-center gap-2">
                            <input type="text" x-model="testPhone" placeholder="Contoh: 08123456789"
                                   class="flex-1 text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white font-mono">
                            <button type="button" @click="sendTestMessage()" :disabled="isSendingTest"
                                    class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all disabled:opacity-50 cursor-pointer whitespace-nowrap">
                                <span x-show="!isSendingTest">Kirim Tes</span>
                                <span x-show="isSendingTest">Mengirim...</span>
                            </button>
                        </div>
                        <div x-show="testStatusMessage" class="text-xs font-bold p-2.5 rounded-xl"
                             :class="testStatusSuccess ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300'"
                             x-text="testStatusMessage"></div>
                    </div>

                    <!-- Final Submission Card -->
                    <div class="bg-gradient-to-br from-orange-600 via-amber-600 to-orange-700 rounded-3xl p-6 text-white shadow-xl space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-200">Ringkasan Siaran</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white" x-text="selectedUserIds.length + ' Sasaran'"></span>
                        </div>

                        <div class="space-y-1 text-xs">
                            <div class="flex justify-between text-orange-100">
                                <span>Target:</span>
                                <strong class="text-white" x-text="targetTypeLabel"></strong>
                            </div>
                            <div class="flex justify-between text-orange-100">
                                <span>Estimasi Waktu:</span>
                                <strong class="text-white" x-text="estimateTime"></strong>
                            </div>
                        </div>

                        <button type="submit" :disabled="selectedUserIds.length === 0 || isSubmitting"
                                class="w-full py-3.5 px-4 bg-white text-orange-950 hover:bg-orange-50 rounded-2xl font-black text-xs uppercase tracking-wider transition-all shadow-lg hover:shadow-xl cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span x-show="!isSubmitting">🚀 Kirim Siaran WhatsApp Sekarang</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin text-orange-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Sedang Memproses Siaran...</span>
                            </span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Full-screen Loading Overlay on Submit -->
            <div x-show="isSubmitting" x-cloak class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 max-w-md w-full shadow-2xl text-center space-y-5 animate-in fade-in zoom-in duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-orange-100 dark:bg-orange-950/80 text-orange-600 dark:text-orange-400 mx-auto flex items-center justify-center text-3xl shadow-inner relative">
                        <span>🚀</span>
                        <span class="absolute -top-1 -right-1 flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-orange-500"></span>
                        </span>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Siaran WhatsApp Sedang Diproses</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Sistem sedang mendistribusikan pesan ke <strong class="text-slate-800 dark:text-slate-200" x-text="selectedUserIds.length + ' sasaran terpilih'"></strong> dengan proteksi anti-spam.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-[11px] text-amber-800 dark:text-amber-300 font-medium text-left">
                        ⚠️ <strong>PENTING:</strong> Mohon tunggu hingga proses selesai. Jangan me-refresh atau menutup jendela peramban agar pesan tidak terkirim ganda.
                    </div>

                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 via-amber-500 to-orange-500 h-full w-full animate-pulse"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Alpine Controller Script -->
    <script>
        function waBroadcastComposer() {
            return {
                targetType: 'mahasiswa_belum_seminar',
                cohort: 'all',
                days: 30,
                waveId: '',
                delaySeconds: 4,
                title: 'Pengingat Pendaftaran Seminar Proposal Skripsi',
                messageTemplate: @json($presets['mahasiswa_belum_seminar']['message'] ?? ''),
                presets: @json($presets),
                recipients: [],
                selectedUserIds: [],
                validPhoneCount: 0,
                missingPhoneCount: 0,
                isLoadingRecipients: false,
                testPhone: '{{ $senderPhone ?? '' }}',
                isSendingTest: false,
                testStatusMessage: '',
                testStatusSuccess: true,
                isSubmitting: false,

                init() {
                    this.fetchRecipients();
                },

                get targetTypeLabel() {
                    const map = {
                        'mahasiswa_belum_seminar': 'Mahasiswa Belum Seminar',
                        'mahasiswa_bimbingan_pasif': 'Mahasiswa Bimbingan Pasif',
                        'mahasiswa_kritis': 'Mahasiswa Semester Kritis',
                        'mahasiswa_belum_sidang': 'Mahasiswa Belum Sidang',
                        'mahasiswa_belum_skripsi': 'Mahasiswa Belum Judul',
                        'dosen_pembimbing_aktif': 'Dosen Pembimbing Aktif',
                        'dosen_penguji_gelombang': 'Dosen Penguji Gelombang',
                        'all_mahasiswa_aktif': 'Semua Mahasiswa Aktif',
                    };
                    return map[this.targetType] || this.targetType;
                },

                get firstRecipientName() {
                    if (this.recipients.length > 0) {
                        return this.recipients[0].name;
                    }
                    return 'Nama Mahasiswa';
                },

                get estimateTime() {
                    const count = this.selectedUserIds.length;
                    if (count === 0) return '0 detik';
                    const totalSec = count * parseInt(this.delaySeconds);
                    const minutes = Math.floor(totalSec / 60);
                    const seconds = totalSec % 60;
                    if (minutes > 0) {
                        return `${minutes} menit ${seconds} detik`;
                    }
                    return `${seconds} detik`;
                },

                get formattedPreviewMessage() {
                    let msg = this.messageTemplate || 'Tulis pesan Anda...';
                    
                    // Replace sample tags
                    const first = this.recipients.length > 0 ? this.recipients[0] : null;
                    const sampleNama = first ? first.name : 'Aditya Pratama Putra';
                    const sampleNpm = first ? first.identifier : 'D1A210045';
                    const sampleAngkatan = first && first.cohort ? first.cohort : '2021';

                    msg = msg.replace(/\{nama\}/g, sampleNama);
                    msg = msg.replace(/\{npm\}/g, sampleNpm);
                    msg = msg.replace(/\{nidn\}/g, '0410019202');
                    msg = msg.replace(/\{angkatan\}/g, sampleAngkatan);
                    msg = msg.replace(/\{judul\}/g, 'Rancang Bangun Sistem Informasi Pelayanan Terpadu');
                    msg = msg.replace(/\{pembimbing_1\}/g, 'Bagus Ali Akbar, S.SI., M.Kom');
                    msg = msg.replace(/\{pembimbing_2\}/g, 'Tazkia Salsabila Ardan, M.Kom');
                    msg = msg.replace(/\{hari_tanpa_bimbingan\}/g, '> 30 hari');
                    msg = msg.replace(/\{terakhir_bimbingan\}/g, '14 Agustus 2026');
                    msg = msg.replace(/\{semester\}/g, '14');
                    msg = msg.replace(/\{link_seminar\}/g, '{{ url('/seminar-applications/create') }}');
                    msg = msg.replace(/\{link_sidang\}/g, '{{ url('/thesis-defense-applications/create') }}');
                    msg = msg.replace(/\{link_bimbingan\}/g, '{{ url('/mentoring-sessions') }}');
                    msg = msg.replace(/\{link_login\}/g, '{{ url('/login') }}');
                    msg = msg.replace(/\{link_dashboard\}/g, '{{ url('/dashboard') }}');

                    // Format bold and italic for preview
                    msg = msg.replace(/\*([^*]+)\*/g, '<strong>$1</strong>');
                    msg = msg.replace(/_([^_]+)_/g, '<em>$1</em>');

                    return msg;
                },

                fetchRecipients() {
                    this.isLoadingRecipients = true;
                    fetch('{{ route('wa-broadcasts.preview-targets') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            target_type: this.targetType,
                            cohort: this.cohort,
                            days: this.days,
                            wave_id: this.waveId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.recipients = data.recipients || [];
                        this.validPhoneCount = data.valid_phone_count || 0;
                        this.missingPhoneCount = data.missing_phone_count || 0;
                        // By default select all valid recipients
                        this.selectedUserIds = this.recipients.map(r => r.user_id);
                        this.isLoadingRecipients = false;
                    })
                    .catch(() => {
                        this.isLoadingRecipients = false;
                    });
                },

                toggleSelectAll(e) {
                    if (e.target.checked) {
                        this.selectedUserIds = this.recipients.map(r => r.user_id);
                    } else {
                        this.selectedUserIds = [];
                    }
                },

                isAllSelected() {
                    return this.recipients.length > 0 && this.selectedUserIds.length === this.recipients.length;
                },

                insertVariable(tag) {
                    const textarea = document.getElementById('messageTemplateArea');
                    if (!textarea) return;

                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = this.messageTemplate;

                    this.messageTemplate = text.substring(0, start) + tag + text.substring(end);

                    this.$nextTick(() => {
                        textarea.focus();
                        textarea.setSelectionRange(start + tag.length, start + tag.length);
                    });
                },

                applyPresetTemplate() {
                    if (this.presets && this.presets[this.targetType]) {
                        this.title = this.presets[this.targetType].title;
                        this.messageTemplate = this.presets[this.targetType].message;
                    } else {
                        alert('Tidak ada template bawaan untuk jenis sasaran ini.');
                    }
                },

                sendTestMessage() {
                    if (!this.testPhone) {
                        alert('Silakan masukkan nomor WhatsApp untuk uji coba.');
                        return;
                    }
                    this.isSendingTest = true;
                    this.testStatusMessage = '';

                    fetch('{{ route('wa-broadcasts.test-send') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            phone: this.testPhone,
                            message: this.messageTemplate,
                            target_type: this.targetType,
                            cohort: this.cohort
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSendingTest = false;
                        this.testStatusSuccess = data.success;
                        this.testStatusMessage = data.message;
                    })
                    .catch(err => {
                        this.isSendingTest = false;
                        this.testStatusSuccess = false;
                        this.testStatusMessage = 'Gagal menghubungi server pengujian.';
                    });
                },

                handleSubmit(e) {
                    if (this.isSubmitting) {
                        e.preventDefault();
                        return false;
                    }
                    if (this.selectedUserIds.length === 0) {
                        e.preventDefault();
                        alert('Pilih setidaknya satu kontak penerima.');
                        return false;
                    }
                    if (!confirm(`Apakah Anda yakin ingin mengirim siaran WhatsApp ini ke ${this.selectedUserIds.length} sasaran terpilih?`)) {
                        e.preventDefault();
                        return false;
                    }
                    this.isSubmitting = true;
                }
            }
        }
    </script>
</x-app-layout>

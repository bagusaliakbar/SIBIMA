<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'route' => route('dashboard')],
                ['label' => 'Template WhatsApp', 'route' => route('wa-templates.index')],
                ['label' => 'Edit Format Teks', 'route' => null]
            ]" />
        </div>
    </x-slot>

    <div class="w-full max-w-5xl mx-auto space-y-6" x-data="templateEditor(@js($waTemplate->content), @js($waTemplate->available_variables))">
        <!-- Header Edit Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-400 rounded-full text-xs font-bold mb-2">
                    <span>{{ $waTemplate->category }}</span>
                    <span>•</span>
                    <span class="font-mono">{{ $waTemplate->code }}</span>
                </div>
                <h1 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white">
                    Edit: {{ $waTemplate->name }}
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('wa-templates.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-colors">
                    Batal
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Form Editor (Left Side) -->
            <div class="lg:col-span-7 space-y-6">
                <form action="{{ route('wa-templates.update', $waTemplate) }}" method="POST" class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-slate-700/80 shadow-sm space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Available Variable Pills -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Klik Tag Variabel Untuk Menyisipkan Ke Teks:
                        </label>
                        <div class="flex flex-wrap gap-2 p-3 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                            @if($waTemplate->available_variables)
                                @foreach($waTemplate->available_variables as $varKey => $varDesc)
                                    <button type="button" 
                                            @click="insertVariable('{{ $varKey }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-200 hover:text-emerald-700 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold transition-all shadow-sm group">
                                        <span class="text-emerald-500 font-extrabold">+</span>
                                        <span>{<span>{{ $varKey }}</span>}</span>
                                        <span class="text-[10px] font-sans text-slate-400 dark:text-slate-500 ml-1 font-normal">({{ $varDesc }})</span>
                                    </button>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Textarea Editor -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="content" class="text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Isi Template Pesan WhatsApp
                            </label>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500">
                                Gunakan <code class="font-bold">*kata*</code> untuk menebalkan teks
                            </span>
                        </div>

                        <textarea id="content" 
                                  name="content" 
                                  x-model="content"
                                  x-ref="textarea"
                                  rows="10" 
                                  class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 text-sm font-mono focus:border-emerald-500 focus:ring-emerald-500 transition-colors p-4 leading-relaxed"
                                  required></textarea>
                    </div>

                    <!-- Save Button -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700/60">
                        <button type="submit" 
                                class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm rounded-xl transition-colors shadow-lg shadow-orange-600/30 inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Perubahan Template
                        </button>
                    </div>
                </form>
            </div>

            <!-- Live Preview Simulator (Right Side) -->
            <div class="lg:col-span-5">
                <div class="sticky top-24 bg-slate-900 text-white rounded-3xl p-6 shadow-2xl border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        <span class="text-xs font-mono text-slate-400">WhatsApp Live Preview</span>
                    </div>

                    <!-- Chat Bubble mockup -->
                    <div class="bg-emerald-900/40 border border-emerald-800/50 rounded-2xl p-4 space-y-2">
                        <div class="flex items-center gap-2 text-emerald-400 font-bold text-xs pb-2 border-b border-emerald-800/40">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.767 5.767 0 1.267.408 2.445 1.103 3.407l-.722 2.637 2.7-.709c.928.587 2.025.932 3.203.932 3.181 0 5.767-2.586 5.767-5.767 0-3.181-2.586-5.767-5.767-5.767z"></path></svg>
                            <span>Pesan Keluar (Fonnte Bot)</span>
                        </div>

                        <div class="text-sm font-sans text-slate-100 whitespace-pre-line leading-relaxed pt-1" 
                             x-html="formattedPreview">
                        </div>

                        <div class="text-right text-[10px] text-slate-400 font-mono pt-1">
                            <span>12:00 WIB</span> • <span class="text-emerald-400">✓✓</span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-800/60 rounded-xl border border-slate-800 text-[11px] text-slate-400 leading-normal">
                        <strong>Tips formatting:</strong> Teks di antara tanda bintang <code class="text-amber-300 font-bold">*teks*</code> akan otomatis tercetak <strong>Tebal (Bold)</strong> di WhatsApp.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function templateEditor(initialContent, availableVariables) {
            return {
                content: initialContent,
                variables: availableVariables,
                insertVariable(varKey) {
                    const tag = '{' + varKey + '}';
                    const textarea = this.$refs.textarea;
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;

                    this.content = this.content.substring(0, start) + tag + this.content.substring(end);
                    
                    this.$nextTick(() => {
                        textarea.focus();
                        textarea.setSelectionRange(start + tag.length, start + tag.length);
                    });
                },
                get formattedPreview() {
                    if (!this.content) return '<span class="text-slate-500 italic">Tulis teks template di samping untuk melihat preview...</span>';

                    let text = this.content;

                    // Replace dummy values for variable placeholders
                    const sampleValues = {
                        'nama_mahasiswa': 'Bagus Ali Akbar',
                        'nama_dosen': 'Dr. Irzan, M.T.',
                        'nama_penerima': 'Bagus Ali Akbar',
                        'nama_kaprodi': 'Kaprodi Informatika',
                        'judul_skripsi': 'Sistem Informasi Bimbingan Skripsi SIBIMA',
                        'tanggal_bimbingan': 'Senin, 10 Agustus 2026 09:00 WIB',
                        'topik_bimbingan': 'Pembahasan Bab 4 & Diagram UML',
                        'status_bimbingan': 'DISETUJUI',
                        'catatan_dosen': 'Lanjutkan perancangan tabel database.',
                        'peran_pembimbing': 'Pembimbing 1',
                        'pembimbing_1': 'Dr. Irzan, M.T.',
                        'pembimbing_2': 'Nurhayati, M.Kom.',
                        'jenis_acc': 'Seminar UP',
                        'nama_pemberi_acc': 'Dr. Irzan, M.T.',
                        'jenis_ujian': 'Seminar UP',
                        'tanggal_ujian': '15 Agustus 2026',
                        'jam_ujian': '08:00 - 09:30',
                        'lokasi_ujian': 'Ruang Laboratorium Komputer A',
                        'catatan_mahasiswa': 'Sudah diperbaiki pada bab 3 sesuai arahan penguji.',
                        'label_waktu': 'H-1',
                        'pesan_pengingat': 'Anda memiliki jadwal Seminar UP besok.',
                        'semester_ke': '13',
                        'jumlah_mahasiswa': '5',
                        'daftar_mahasiswa': '• Ahmad Syarif (Sem 13)\n• Budi Utomo (Sem 14)',
                        'link_login': 'https://sibima.ac.id/login',
                        'link_mentoring': 'https://sibima.ac.id/mentoring-sessions',
                        'link_monitoring': 'https://sibima.ac.id/monitoring/critical',
                        'link_dashboard': 'https://sibima.ac.id/dashboard'
                    };

                    for (const [key, val] of Object.entries(sampleValues)) {
                        text = text.replaceAll('{' + key + '}', val);
                    }

                    // Format WA bold *text* to <strong>text</strong>
                    text = text.replace(/\*(.*?)\*/g, '<strong class="font-bold text-white">$1</strong>');

                    return text;
                }
            }
        }
    </script>
</x-app-layout>

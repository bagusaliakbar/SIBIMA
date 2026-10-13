<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Sidang', 'route' => route('thesis-defense-schedules.index')],
            ['label' => 'Buat Baru', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full mx-auto" x-data="scheduleForm()">
        <form action="{{ route('thesis-defense-schedules.store') }}" method="POST" @submit="submitForm($event)">
            @csrf
            
            <!-- Basic Information -->
            <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Informasi Utama Sesi</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="title" value="Judul / Sesi Sidang" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full text-sm" placeholder="Contoh: SEMESTER GENAP GELOMBANG 2 TAHUN AKADEMIK 2025/2026" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="wave_id" value="Gelombang Pelaksanaan" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <select id="wave_id" name="wave_id" onchange="window.location.href = '{{ route('thesis-defense-schedules.create') }}?wave_id=' + this.value" class="mt-1 block w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm text-sm font-semibold">
                            @foreach($waves as $wave)
                                <option value="{{ $wave->id }}" {{ ($targetWave->id ?? null) == $wave->id ? 'selected' : '' }}>
                                    {{ $wave->name }} {{ $wave->is_active ? '(Gelombang Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('wave_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date" value="Tanggal Pelaksanaan" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <x-text-input id="date" name="date" type="date" @change="recheckAll()" class="mt-1 block w-full text-sm" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="location" value="Lokasi / Ruangan" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <x-text-input id="location" name="location" type="text" class="mt-1 block w-full text-sm" placeholder="Contoh: Online atau Ruang Rapat Lt. 2" />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <div>
                        <x-google-meet-input name="meeting_link" id="meeting_link" :value="old('meeting_link')" label="Link Meeting (Google Meet / Zoom)" />
                        <x-input-error :messages="$errors->get('meeting_link')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="chairman_id" value="Ketua Sidang (Opsional)" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <select id="chairman_id" name="chairman_id" x-model="chairman_id" @change="recheckAll()" class="mt-1 block w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm text-sm">
                            <option value="">Pilih Ketua Sidang (Opsional)</option>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('chairman_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="moderator_id" value="Moderator (Opsional)" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <select id="moderator_id" name="moderator_id" x-model="moderator_id" @change="recheckAll()" class="mt-1 block w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm text-sm">
                            <option value="">Pilih Moderator (Opsional)</option>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('moderator_id')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Details List -->
            <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Susunan Jadwal & Peserta</h3>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 uppercase font-medium text-xs">Tambahkan urutan waktu, kegiatan, atau mahasiswa yang akan sidang</p>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" @click="addRow('activity')" class="inline-flex items-center px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Kegiatan
                        </button>
                        <button type="button" @click="addRow('student')" class="inline-flex items-center px-3 py-1.5 bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded text-[10px] font-black uppercase tracking-widest hover:bg-orange-200 dark:hover:bg-orange-500/20 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Tambah Mahasiswa
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <template x-for="(row, index) in rows" :key="index">
                            <div class="p-4 border rounded-lg dark:border-slate-700 relative bg-slate-50/20 dark:bg-slate-900/10" :class="row.type === 'student' ? 'border-orange-100 dark:border-orange-900/30' : 'border-slate-200 dark:border-slate-700'">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] px-2 py-0.5 rounded" :class="row.type === 'student' ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'" x-text="row.type === 'student' ? 'Data Peserta Sidang' : 'Kegiatan / Persiapan'"></span>
                                    <button type="button" @click="removeRow(index)" class="text-slate-400 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                    <!-- Time -->
                                    <div class="md:col-span-2">
                                        <x-input-label value="Waktu Mulai" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <input type="time" :name="`details[${index}][start_time]`" x-model="row.start_time" @change="checkConflict(index)" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <x-input-label value="Waktu Selesai" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <input type="time" :name="`details[${index}][end_time]`" x-model="row.end_time" @change="checkConflict(index)" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500" required>
                                    </div>

                                    <!-- Content -->
                                    <div class="md:col-span-8" x-show="row.type === 'activity'">
                                        <x-input-label value="Nama Kegiatan" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <input type="text" :name="`details[${index}][activity_name]`" x-model="row.activity_name" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500" placeholder="Contoh: Persiapan dan Pembukaan Sesi Sidang">
                                    </div>

                                    <div class="md:col-span-4" x-show="row.type === 'student'">
                                        <x-input-label value="Mahasiswa & Judul" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <select :name="`details[${index}][thesis_id]`" x-model="row.thesis_id" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500">
                                            <option value="">Pilih Mahasiswa</option>
                                            @foreach($theses as $thesis)
                                                <option value="{{ $thesis->id }}">{{ $thesis->student->name }} - {{ Str::limit($thesis->final_title ?? $thesis->title, 50) }}</option>
                                            @endforeach
                                        </select>
                                        <div class="mt-1 text-[9px] text-slate-500 font-medium" x-show="row.thesis_id">
                                            Pembimbing: <span class="font-bold text-orange-600 dark:text-orange-400" x-text="getAdvisors(row.thesis_id)"></span>
                                        </div>
                                    </div>

                                    <div class="md:col-span-2" x-show="row.type === 'student'">
                                        <x-input-label value="Penguji 1" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <select :name="`details[${index}][examiner1_id]`" x-model="row.examiner1_id" @change="checkConflict(index)" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500">
                                            <option value="">Pilih Penguji</option>
                                            @foreach($dosens as $dosen)
                                                <option value="{{ $dosen->id }}" x-show="!isAdvisor({{ $dosen->id }}, row.thesis_id)" :disabled="isAdvisor({{ $dosen->id }}, row.thesis_id)">{{ $dosen->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-2" x-show="row.type === 'student'">
                                        <x-input-label value="Penguji 2" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <select :name="`details[${index}][examiner2_id]`" x-model="row.examiner2_id" @change="checkConflict(index)" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500">
                                            <option value="">Pilih Penguji</option>
                                            @foreach($dosens as $dosen)
                                                <option value="{{ $dosen->id }}" x-show="!isAdvisor({{ $dosen->id }}, row.thesis_id)" :disabled="isAdvisor({{ $dosen->id }}, row.thesis_id)">{{ $dosen->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Conflict Warning & Override Toggle -->
                                <div x-show="row.conflicts && row.conflicts.length > 0" x-transition 
                                     class="mt-3 p-3.5 rounded-xl border transition-all duration-200"
                                     :class="row.allow_override 
                                         ? 'bg-amber-50/70 dark:bg-amber-950/20 border-amber-300 dark:border-amber-700/60' 
                                         : 'bg-rose-50/80 dark:bg-rose-950/30 border-rose-200 dark:border-rose-800/70'">
                                    <div class="flex items-start gap-2.5">
                                        <div class="mt-0.5 shrink-0">
                                            <template x-if="!row.allow_override">
                                                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </template>
                                            <template x-if="row.allow_override">
                                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between flex-wrap gap-2">
                                                <p class="text-[10px] font-black uppercase tracking-widest"
                                                   :class="row.allow_override ? 'text-amber-800 dark:text-amber-300' : 'text-rose-700 dark:text-rose-300'">
                                                    Peringatan Bentrok Jadwal
                                                </p>
                                                <span x-show="row.allow_override" class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 border border-amber-300/60 dark:border-amber-700/60">
                                                    ✓ Pengecualian Disetujui
                                                </span>
                                            </div>
                                            <ul class="mt-1 space-y-1">
                                                <template x-for="msg in row.conflicts">
                                                    <li class="text-[10px] font-medium list-disc ml-3"
                                                        :class="row.allow_override ? 'text-amber-700/90 dark:text-amber-400/90' : 'text-rose-600 dark:text-rose-400'"
                                                        x-text="msg"></li>
                                                </template>
                                            </ul>

                                            <!-- Checkbox Pengecualian -->
                                            <div class="mt-2.5 pt-2 border-t"
                                                 :class="row.allow_override ? 'border-amber-200/80 dark:border-amber-800/40' : 'border-rose-200/80 dark:border-rose-800/40'">
                                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                                    <input type="checkbox" x-model="row.allow_override" 
                                                           class="rounded border-slate-300 dark:border-slate-700 text-amber-600 focus:ring-amber-500 w-3.5 h-3.5 transition-colors">
                                                    <span class="text-[10.5px] font-semibold"
                                                          :class="row.allow_override ? 'text-amber-800 dark:text-amber-300' : 'text-slate-700 dark:text-slate-300'">
                                                        Izinkan pengecualian jadwal bentrok ini (Disetujui Kaprodi/Admin)
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="rows.length === 0" class="py-12 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Klik tombol tambah di atas untuk menyusun jadwal</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-200 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30 flex justify-end">
                    <a href="{{ route('thesis-defense-schedules.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition ease-in-out duration-150 mr-3">Batal</a>
                    <x-primary-button>Simpan Jadwal</x-primary-button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function scheduleForm() {
            return {
                chairman_id: '',
                moderator_id: '',
                rows: [
                    { type: 'activity', start_time: '', end_time: '', activity_name: '', thesis_id: '', examiner1_id: '', examiner2_id: '', conflicts: [], allow_override: false }
                ],
                theses: @json($theses),
                addRow(type) {
                    this.rows.push({
                        type: type,
                        start_time: '',
                        end_time: '',
                        activity_name: '',
                        thesis_id: '',
                        examiner1_id: '',
                        examiner2_id: '',
                        conflicts: [],
                        allow_override: false
                    });
                },
                removeRow(index) {
                    this.rows.splice(index, 1);
                },
                isAdvisor(dosenId, thesisId) {
                    if (!thesisId) return false;
                    const thesis = this.theses.find(t => t.id == thesisId);
                    if (!thesis) return false;
                    return thesis.pembimbing1_id == dosenId || thesis.pembimbing2_id == dosenId;
                },
                getAdvisors(thesisId) {
                    if (!thesisId) return '';
                    const thesis = this.theses.find(t => t.id == thesisId);
                    if (!thesis) return '';
                    let advisors = thesis.pembimbing1.name;
                    if (thesis.pembimbing2) advisors += ' & ' + thesis.pembimbing2.name;
                    return advisors;
                },
                async checkConflict(index) {
                    const row = this.rows[index];
                    const date = document.getElementById('date').value;
                    
                    if (!date || !row.start_time || !row.end_time) return;

                    const dosenIds = [];
                    if (row.examiner1_id) dosenIds.push(row.examiner1_id);
                    if (row.examiner2_id && row.examiner2_id !== row.examiner1_id) dosenIds.push(row.examiner2_id);

                    if (dosenIds.length === 0) {
                        row.conflicts = [];
                        return;
                    }

                    try {
                        const response = await fetch('{{ route("check-dosen-availability") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                dosen_ids: dosenIds,
                                date: date,
                                start_time: row.start_time,
                                end_time: row.end_time,
                                schedule_type: 'defense'
                            })
                        });

                        const data = await response.json();
                        row.conflicts = [];
                        
                        if (data.has_conflict) {
                            Object.values(data.conflicts).forEach(conflict => {
                                conflict.messages.forEach(msg => {
                                    row.conflicts.push(`${conflict.name} - ${msg}`);
                                });
                            });
                        }
                    } catch (error) {
                        console.error('Error checking conflict:', error);
                    }
                },
                recheckAll() {
                    this.rows.forEach((row, index) => {
                        this.checkConflict(index);
                    });
                },
                submitForm(e) {
                    const unexempted = this.rows.filter(r => r.conflicts && r.conflicts.length > 0 && !r.allow_override);
                    if (unexempted.length > 0) {
                        let msg = `PERHATIAN: Terdeteksi bentrok jadwal pada ${unexempted.length} sesi yang belum diberi centang izin pengecualian:\n\n`;
                        unexempted.forEach((r, idx) => {
                            const name = r.thesis_id ? (this.theses.find(t => t.id == r.thesis_id)?.student?.name || 'Mahasiswa') : (r.activity_name || 'Kegiatan');
                            msg += `• Sesi (${name}):\n  ${r.conflicts.join('\n  ')}\n\n`;
                        });
                        msg += `Apakah Anda yakin ingin tetap MENYIMPAN jadwal ini dengan memberikan pengecualian bentrok?`;

                        if (!confirm(msg)) {
                            e.preventDefault();
                            return false;
                        }
                        // User setuju: tandai semua override menjadi true
                        unexempted.forEach(r => r.allow_override = true);
                    }
                }
            }
        }
    </script>
</x-app-layout>

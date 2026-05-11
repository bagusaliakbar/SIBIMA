<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Jadwal Sidang', 'route' => route('thesis-defense-schedules.index')],
            ['label' => 'Edit Jadwal', 'route' => null]
        ]" />
    </x-slot>

    <div class="w-full mx-auto" x-data="scheduleForm()">
        <form action="{{ route('thesis-defense-schedules.update', $thesisDefenseSchedule) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 rounded-lg overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30">
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Informasi Utama Sesi</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <x-input-label for="title" value="Judul / Sesi Sidang" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full text-sm" value="{{ $thesisDefenseSchedule->title }}" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date" value="Tanggal Pelaksanaan" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full text-sm" value="{{ $thesisDefenseSchedule->date }}" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="location" value="Lokasi / Ruangan" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <x-text-input id="location" name="location" type="text" class="mt-1 block w-full text-sm" value="{{ $thesisDefenseSchedule->location }}" placeholder="Contoh: Online atau Ruang Rapat Lt. 2" />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="meeting_link" value="Link Meeting (Google Meet/Zoom)" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <x-text-input id="meeting_link" name="meeting_link" type="url" class="mt-1 block w-full text-sm" value="{{ $thesisDefenseSchedule->meeting_link }}" placeholder="https://meet.google.com/xxx-xxxx-xxx" />
                        <x-input-error :messages="$errors->get('meeting_link')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="chairman_id" value="Ketua Sidang" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <select id="chairman_id" name="chairman_id" class="mt-1 block w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm text-sm" required>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}" {{ $thesisDefenseSchedule->chairman_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('chairman_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="moderator_id" value="Moderator" class="text-[10px] font-black uppercase tracking-widest text-slate-500" />
                        <select id="moderator_id" name="moderator_id" class="mt-1 block w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm text-sm" required>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}" {{ $thesisDefenseSchedule->moderator_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('moderator_id')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Details List -->
            <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 rounded-lg overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Susunan Jadwal & Peserta</h3>
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
                            <div class="p-4 border rounded-lg dark:border-slate-700 relative bg-slate-50/20 dark:bg-slate-900/10" :class="row.type === 'student' ? 'border-orange-100 dark:border-orange-900/30' : 'border-slate-100 dark:border-slate-700'">
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
                                        <input type="time" :name="`details[${index}][start_time]`" x-model="row.start_time" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <x-input-label value="Waktu Selesai" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <input type="time" :name="`details[${index}][end_time]`" x-model="row.end_time" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500" required>
                                    </div>

                                    <!-- Content -->
                                    <div class="md:col-span-8" x-show="row.type === 'activity'">
                                        <x-input-label value="Nama Kegiatan" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <input type="text" :name="`details[${index}][activity_name]`" x-model="row.activity_name" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500">
                                    </div>

                                    <div class="md:col-span-4" x-show="row.type === 'student'">
                                        <x-input-label value="Mahasiswa & Judul" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <select :name="`details[${index}][thesis_id]`" x-model="row.thesis_id" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500">
                                            <option value="">Pilih Mahasiswa</option>
                                            @foreach($theses as $thesis)
                                                <option value="{{ $thesis->id }}">{{ $thesis->student->name }} - {{ Str::limit($thesis->title, 50) }}</option>
                                            @endforeach
                                        </select>
                                        <div class="mt-1 text-[9px] text-slate-500 font-medium" x-show="row.thesis_id">
                                            Pembimbing: <span class="font-bold text-orange-600 dark:text-orange-400" x-text="getAdvisors(row.thesis_id)"></span>
                                        </div>
                                    </div>

                                    <div class="md:col-span-2" x-show="row.type === 'student'">
                                        <x-input-label value="Penguji 1" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <select :name="`details[${index}][examiner1_id]`" x-model="row.examiner1_id" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500">
                                            <option value="">Pilih Penguji</option>
                                            @foreach($dosens as $dosen)
                                                <option value="{{ $dosen->id }}" x-show="!isAdvisor({{ $dosen->id }}, row.thesis_id)" :disabled="isAdvisor({{ $dosen->id }}, row.thesis_id)">{{ $dosen->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-2" x-show="row.type === 'student'">
                                        <x-input-label value="Penguji 2" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1" />
                                        <select :name="`details[${index}][examiner2_id]`" x-model="row.examiner2_id" class="w-full border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md text-xs focus:ring-orange-500 focus:border-orange-500">
                                            <option value="">Pilih Penguji</option>
                                            @foreach($dosens as $dosen)
                                                <option value="{{ $dosen->id }}" x-show="!isAdvisor({{ $dosen->id }}, row.thesis_id)" :disabled="isAdvisor({{ $dosen->id }}, row.thesis_id)">{{ $dosen->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/30 flex justify-end">
                    <a href="{{ route('thesis-defense-schedules.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition ease-in-out duration-150 mr-3">Batal</a>
                    <x-primary-button>Update Jadwal</x-primary-button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function scheduleForm() {
            return {
                rows: @json($mappedDetails),
                theses: @json($theses),
                addRow(type) {
                    this.rows.push({
                        type: type,
                        start_time: '',
                        end_time: '',
                        activity_name: '',
                        thesis_id: '',
                        examiner1_id: '',
                        examiner2_id: ''
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
                }
            }
        }
    </script>
</x-app-layout>

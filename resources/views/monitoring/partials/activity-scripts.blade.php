    @push('scripts')
    <script>
        function mentoringActivityApp(initialTab) {
            return {
                activeMainTab: initialTab || 'mahasiswa',
                studentSubTab: 'terajin',
                dosenSubTab: 'terajin',
                
                modalOpen: false,
                modalTargetType: 'student',
                modalUserId: null,
                modalTargetName: '',
                modalTargetPhone: '',
                modalThesisId: null,
                modalDaysInactive: 0,
                modalMessage: '',

                switchMainTab(tab) {
                    this.activeMainTab = tab;
                    // update URL param without full reload
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tab);
                    window.history.replaceState({}, '', url);
                },

                openReminderModal(type, userId, name, phone, thesisId, daysInactive) {
                    this.modalTargetType = type;
                    this.modalUserId = userId;
                    this.modalTargetName = name;
                    this.modalTargetPhone = phone || '';
                    this.modalThesisId = thesisId;
                    this.modalDaysInactive = daysInactive;

                    if (type === 'dosen') {
                        const daysText = daysInactive > 0 ? `dalam ${daysInactive} hari terakhir` : 'pada periode ini';
                        this.modalMessage = `Yth. Bapak/Ibu ${name},\n\nSalam hangat dari Program Studi FASILKOM UNSUB.\n\nBerdasarkan pantauan sistem SIBIMA, belum tercatat aktivitas sesi bimbingan skripsi dengan mahasiswa bimbingan aktif Bapak/Ibu ${daysText}.\n\nMohon kesediaan Bapak/Ibu untuk memeriksa pengajuan jadwal atau mengoordinasikan sesi bimbingan bersama mahasiswa bimbingan agar progres skripsi mereka tetap berjalan lancar.\n\nTerima kasih banyak atas perhatian dan dedikasi Bapak/Ibu.`;
                    } else {
                        const daysText = daysInactive > 0 ? `selama ${daysInactive} hari terakhir` : 'sejak pengajuan skripsi';
                        this.modalMessage = `Halo Sdr/i ${name},\n\nBerdasarkan pantauan radar keaktifan bimbingan SIBIMA FASILKOM UNSUB, Anda tercatat belum melakukan sesi bimbingan skripsi ${daysText}.\n\nKami mengingatkan agar Anda SEGERA berinisiatif menghubungi Dosen Pembimbing untuk mengajukan jadwal dan berkonsultasi mengenai kelanjutan tugas akhir Anda.\n\nJangan biarkan skripsi Anda tertunda. Tetap semangat menyelesaikan studi!`;
                    }

                    this.modalOpen = true;
                },

                closeReminderModal() {
                    this.modalOpen = false;
                }
            };
        }
    </script>
    @endpush

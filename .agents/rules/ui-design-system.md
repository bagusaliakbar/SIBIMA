# SIBIMA UI/UX Design System Guidelines

Aturan ini wajib dipatuhi oleh asisten AI saat membuat, mengedit, atau merefaktor antarmuka (Blade, CSS, Tailwind) di proyek SIBIMA, mengacu pada standar **UI/UX Pro Max** (`.agents/skills/ui-ux-pro-max/`).

---

## 1. Desain Filosofi: Clean Enterprise SaaS
SIBIMA adalah sistem informasi akademik perguruan tinggi. Desain harus berorientasi pada **fungsionalitas tinggi, kejelasan data, keterbacaan, dan kerapian (Clean Enterprise ala Linear / TailwindUI)**.
- **HINDARI "AI Slop"**: Dilarang menggunakan gradien warna-warni acak (seperti ungu ke pink), bayangan berlebihan, efek neon, atau animasi yang tidak fungsional.
- **Gunakan SVG Icon**: Selalu gunakan ikon SVG monokromatik (Heroicons/Lucide), jangan pernah gunakan emoji sebagai ikon antarmuka.

---

## 2. Palet Warna Semantik SIBIMA
Seluruh elemen visual wajib menggunakan token warna semantik yang konsisten:
- **Primary / Brand Action**: `bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white shadow-sm`.
- **Neutral Surface**:
  - Background Halaman: `bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100`
  - Card & Modal: `bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm`
  - Sub-surface / Well: `bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/80`
- **Status Semantik (Badge & Sub-Button)**:
  - **Selesai / Approved**: `bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60`
  - **Perhatian / Pending / Simulasi**: `bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60`
  - **Info / Terkirim / Word**: `bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60`
  - **Bahaya / Ditolak / PDF**: `bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60`

---

## 3. Aturan Wajib Light Mode & Dark Mode
- **Jaminan Kontras WCAG AA (Minimal 4.5:1)**:
  - Dilarang keras memakai `text-white` tanpa latar yang dipastikan gelap di kedua mode.
  - Untuk tombol netral/sekunder, gunakan `bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700`.
  - Teks sekunder/muted wajib terbaca: `text-slate-500 dark:text-slate-400`.

---

## 4. Tata Letak Toolbar & Tabel (Pola 2-Tier Header)
Jika sebuah tabel manajemen memiliki judul, filter, pencarian, dan beberapa tombol aksi (misal halaman jadwal seminar/sidang):
- **Tier 1 (Atas)**:
  - Kiri: Judul Halaman (`text-base font-bold text-slate-800 dark:text-slate-100`) + Subtitle deskriptif (`text-xs text-slate-500 dark:text-slate-400`).
  - Kanan: Tombol aksi utama (misal: *Simulasi Otomatis*, *Import Excel*, *+ Tambah Manual*).
- **Tier 2 (Toolbar Bawah, dibatasi border pemisah tipis)**:
  - Kiri: Filter dropdown & Input pencarian dengan tinggi seragam `h-10` (40px) dan `rounded-xl`.
  - Kanan: Tombol aksi ekspor batch (*Cetak PDF*, *Cetak Word*).
- **Tinggi Kontrol Seragam**: Semua input form, select, dan button di toolbar wajib `h-10` (40px) dengan `box-sizing: border-box`.

---

## 5. Kerapian Kolom Tabel & Aksi
- Jangan menjejalkan lebih dari 2–3 tombol di dalam satu sel tabel data.
- Tombol aksi cetak/berita acara diletakkan secara terorganisir pada kolom **Aksi**.
- Kolom reviewer/penguji menggunakan kartu ringkas berlabel status (`Selesai`, `Terkirim`, `Dikirim`, atau `Belum Ada`) dengan aksi input/edit ringkas.

---

## 6. Prosedur Wajib Setelah Perubahan UI
Setiap kali mengedit styling Blade / Tailwind:
1. Jalankan `npm run build` untuk mengompilasi utility class baru ke production bundle Vite.
2. Jalankan `php artisan view:clear` untuk membersihkan cache Blade.
3. Lakukan `git add`, `git commit`, dan `git push origin main`.

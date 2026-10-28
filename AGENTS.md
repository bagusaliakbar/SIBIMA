# SIBIMA Workspace Rules (Always Active)

Aturan proyek ini wajib dipatuhi pada setiap sesi pengerjaan di repositori SIBIMA (`c:\Users\Bagus Ali Akbar\Herd\sibima`).

---

## 1. Otomatisasi Git Commit, Push & Server Deployment (WAJIB)

Proyek ini terhubung langsung ke GitHub (`origin git@github.com:bagusaliakbar/SIBIMA.git`) dan server produksi live (berjalan melalui root `index.php` dan `.htaccess`, berada di balik Cloudflare, serta disinkronisasi ke server via `/clear-cache?pull=1`).

Setiap kali selesai melakukan pembuatan fitur, perbaikan bug, peningkatan keamanan, atau perubahan kode/konfigurasi apa pun, **AGEN WAJIB LANGSUNG MELAKUKAN COMMIT DAN PUSH SECARA OTOMATIS TANPA PERLU MENUNGGU INSTRUKSI TAMBAHAN DARI USER** (kecuali user secara eksplisit meminta untuk menahan commit/push):

1. **Verifikasi & Build (Jika Diperlukan)**:
   - Jika mengubah file PHP, pastikan tidak ada error sintaks (`php -l`).
   - Jika mengubah file Blade, CSS, Tailwind, atau JS, jalankan `npm run build` dan `php artisan view:clear`.
2. **Jaga Kompatibilitas Server Produksi**:
   - Jangan merusak alur routing root `index.php` & `.htaccess`, akses file publik di `storage/app/public`, deteksi HTTPS Cloudflare (`TRUSTED_PROXIES`), maupun mekanisme sinkronisasi `/clear-cache`.
3. **Eksekusi `git add`, `git commit`, dan `git push` Otomatis**:
   - `git add` seluruh file yang dimodifikasi atau ditambahkan.
   - `git commit -m "..."` menggunakan format Conventional Commits (`feat: ...`, `fix: ...`, `security: ...`, `refactor: ...`, `chore: ...`).
   - `git push origin main` ke repository remote.

---

## 2. Standar UI/UX Design System SIBIMA

Saat membuat, mengedit, atau merefaktor antarmuka (Blade, CSS, Tailwind), patuhi panduan lengkap pada @[SIBIMA UI/UX Design System Guidelines](.agents/rules/ui-design-system.md):
- **Clean Enterprise SaaS**: Gunakan palet semantik SIBIMA (`orange-600` untuk primary action, `slate` untuk surface/netral, dan warna semantik `emerald`/`amber`/`blue`/`rose` untuk badge/status).
- **Light & Dark Mode**: Pastikan kontras memenuhi WCAG AA di kedua mode (`dark:` class wajib konsisten).
- **Ikon SVG**: Gunakan SVG (Heroicons/Lucide), hindari penggunaan emoji pada komponen UI.

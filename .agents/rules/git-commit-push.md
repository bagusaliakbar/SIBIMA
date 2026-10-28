---
trigger: always_on
---

# Git Commit, Push & Live Server Deployment Workflow

Repositori ini (`origin git@github.com:bagusaliakbar/SIBIMA.git`) terhubung langsung ke server produksi (shared hosting dengan root `index.php` & `.htaccess`, di balik Cloudflare, dan disinkronisasi menggunakan `/clear-cache?pull=1`).

Setiap kali selesai mengerjakan tugas, perbaikan bug, penambahan fitur, atau perubahan kode/konfigurasi di proyek ini, **WAJIB langsung menjalankan langkah berikut secara otomatis tanpa menunggu diminta oleh user** (kecuali user secara eksplisit meminta untuk tidak di-push):

1. **Build Assets (Jika Ada Perubahan UI / Blade / CSS / JS)**:
   - Jalankan `npm run build` dan `php artisan view:clear` jika ada perubahan pada tampilan, komponen Blade, CSS, atau JavaScript.
2. **Validasi Sintaks & Kompatibilitas Server**:
   - Pastikan file PHP yang diubah bebas syntax error (`php -l`).
   - Pastikan perubahan tetap kompatibel dengan arsitektur server produksi (routing `index.php` di root, akses file publik `storage/app/public`, dan endpoint `/clear-cache`).
3. **Git Add, Commit & Push Otomatis**:
   - Jalankan `git add` pada seluruh file yang relevan dengan perubahan.
   - Jalankan `git commit -m "..."` dengan pesan deskriptif mengikuti format Conventional Commits (`feat: ...`, `fix: ...`, `security: ...`, `refactor: ...`, `chore: ...`).
   - Jalankan `git push origin main` (atau branch aktif) secara otomatis agar perubahan langsung tersedia untuk ditarik ke server.

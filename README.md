<p align="center">
  <h1 align="center">DE-LMS</h1>
  <p align="center">Learning Management System berbasis Laravel 12 dengan fitur AI Recommendation</p>
  <p align="center">
    <img src="https://img.shields.io/badge/Laravel-12-red?style=flat-square&logo=laravel" />
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php" />
    <img src="https://img.shields.io/badge/Tailwind_CSS-3-38B2AC?style=flat-square&logo=tailwindcss" />
    <img src="https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=flat-square&logo=alpinedotjs" />
  </p>
</p>

---

## Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Akun Default (Seeder)](#akun-default-seeder)
- [Struktur Role](#struktur-role)
- [Struktur Modul](#struktur-modul)
- [Konfigurasi AI](#konfigurasi-ai)

---

## Tentang Proyek

DE-LMS adalah platform Learning Management System (LMS) yang dirancang untuk mendukung proses belajar-mengajar secara digital. Terinspirasi dari platform seperti ELENA UNNES dan Netacad, DE-LMS hadir dengan tiga peran pengguna (Admin, Guru, Siswa), sistem kursus berbasis kode unik, manajemen materi/tugas/kuis, dan fitur rekomendasi pembelajaran berbasis AI.

---

## Fitur Utama

### 🌐 Landing Page
- Informasi singkat tentang LMS
- Daftar course terintegrasi dari database (bukan dummy)
- Search & filter course berdasarkan judul atau kategori
- Sign In & Sign Up (Student / Teacher)

### 👤 User Management *(Admin)*
- Tambah, edit, hapus user dari semua role (Admin, Guru, Siswa)
- Reset password user

### 📚 Course System
- **Kode Kursus Unik** — Siswa bergabung menggunakan kode unik per course
- **Approval Siswa** — Guru menyetujui siswa yang mendaftar sebelum bisa mengakses materi
- **Materi** — Text editor lengkap + lampiran file (docx, xlsx, pptx, pdf, mp4, dll., maks. 50MB), bisa diunduh
- **Tugas / Assignment** — Pembuatan soal dari sisi guru + pengiriman jawaban dari sisi siswa
- **Kuis** — Pembuatan soal pilihan ganda dengan penentuan jawaban benar

### 📊 Dashboard Analitik
| Role | Konten |
|------|--------|
| **Admin** | Total user/guru/siswa/course, course & teacher paling aktif, grafik aktivitas LMS, pertumbuhan user, insight kategori (AI) |
| **Guru** | Jumlah course aktif, total siswa, rata-rata nilai kelas, progress kelas (grafik), siswa paling/kurang aktif, insight AI |
| **Siswa** | Course yang diikuti, progress belajar (grafik), nilai tugas/kuis (grafik), rekomendasi pembelajaran (AI) |

### 📋 Report Menu
| Role | Konten |
|------|--------|
| **Admin** | User report, course report, performance report per course, export Excel (opsional) |
| **Guru** | Student report (course/quiz/tugas/skor), progress report, AI recommendation report, export Excel (opsional) |
| **Siswa** | Ringkasan course diikuti, average progress & skor quiz, progress bar per course |

### 🤖 AI Recommendation
- **Analisis Tugas** — Penilaian manual oleh guru atau dibantu AI
- **Analisis Kuis** — AI memberikan insight singkat area yang perlu ditingkatkan
- **Rekomendasi** — AI merekomendasikan materi berikutnya, topik belajar, atau course relevan berdasarkan progress dan quiz

### ⚙️ Settings
- Profile Settings (nama, email, password, hapus akun)
- AI Settings — API Key AI dapat dikonfigurasi per user guru (tidak di-hardcode)
- Light / Dark Mode toggle

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, Tailwind CSS 3, Alpine.js |
| Build Tool | Vite |
| Database | MySQL / SQLite |
| AI | Configurable via API Key (per-user setting) |
| Charts | Chart.js |

---

## Persyaratan Sistem

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL / MariaDB
- Git

---

## Instalasi

```bash
# 1. Clone repository
git clone https://github.com/ahmadrozaq45/de-lms.git
cd de-lms

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=de_lms
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan migrasi + seeder
php artisan migrate --seed

# 8. Buat storage link
php artisan storage:link

# 9. Build assets frontend
npm run build
# atau dev mode:
npm run dev

# 10. Jalankan server
php artisan serve
```

Akses aplikasi di: **http://localhost:8000**

---

## Akun Default (Seeder)

Setelah menjalankan `php artisan migrate --seed`, akun berikut tersedia:

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@lms.com` | `password` |
| Guru | `teacher@lms.com` | `password` |
| Siswa | `student@lms.com` | `password` |

> Tersedia juga 9 akun guru dan 9 akun siswa tambahan. Lihat `database/seeders/UserSeeder.php` untuk daftar lengkapnya.

---

## Struktur Role

```
Admin
├── User Management (CRUD semua user)
├── Dashboard Analitik (overview seluruh LMS)
└── Report (user, course, performa)

Guru (Teacher)
├── Manajemen Course (buat, edit, hapus)
│   ├── Modul & Materi (upload file)
│   ├── Tugas / Assignment
│   └── Kuis
├── Approval Siswa
├── Penilaian & Review
├── Dashboard (analitik kelas)
├── Report (per siswa, per course)
└── AI Settings (konfigurasi API Key)

Siswa (Student)
├── Bergabung Course (via kode)
├── Akses Materi & Unduh File
├── Submit Tugas
├── Ikut Kuis
├── Dashboard (progress & nilai)
└── Report (ringkasan pribadi)
```

---

## Struktur Modul

```
Course
└── Module
    ├── Material      (materi + file lampiran)
    ├── Assignment    (tugas + submission siswa)
    └── Quiz          (soal + attempt siswa)
```

---

## Konfigurasi AI

API Key untuk fitur AI Recommendation dikonfigurasi **per user guru** melalui menu **Settings → AI Settings**, bukan di-hardcode di `.env`. Hal ini memungkinkan setiap guru menggunakan API Key dan model AI sesuai kebutuhan masing-masing.

Fitur AI mencakup:
- Analisis jawaban tugas
- Insight hasil kuis
- Rekomendasi materi & course
- Dashboard insight (kategori diminati, siswa perlu perhatian, dll.)

---

## Lisensi

Proyek ini dibuat untuk keperluan akademis dan pengembangan. Silakan fork dan modifikasi sesuai kebutuhan.

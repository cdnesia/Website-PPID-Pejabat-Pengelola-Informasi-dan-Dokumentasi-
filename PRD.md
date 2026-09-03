# Product Requirements Document (PRD)
# Website PPID — Pejabat Pengelola Informasi dan Dokumentasi
**Versi:** 1.0.0  
**Tanggal:** 2 September 2026  
**Stack Teknologi:** Laravel 13 + Vue 3  
**Status:** Draft

---

## Daftar Isi

1. [Latar Belakang](#1-latar-belakang)
2. [Tujuan Produk](#2-tujuan-produk)
3. [Ruang Lingkup](#3-ruang-lingkup)
4. [Pengguna (Stakeholder)](#4-pengguna-stakeholder)
5. [Fitur & Persyaratan Fungsional](#5-fitur--persyaratan-fungsional)
6. [Persyaratan Non-Fungsional](#6-persyaratan-non-fungsional)
7. [Arsitektur Sistem](#7-arsitektur-sistem)
8. [Struktur Database](#8-struktur-database)
9. [Desain UI/UX](#9-desain-uiux)
10. [API Endpoint](#10-api-endpoint)
11. [Keamanan](#11-keamanan)
12. [Milestone & Timeline](#12-milestone--timeline)
13. [Kriteria Keberhasilan](#13-kriteria-keberhasilan)
14. [Referensi Regulasi](#14-referensi-regulasi)

---

## 1. Latar Belakang

Berdasarkan **Undang-Undang No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik (KIP)**, setiap Badan Publik wajib menyediakan informasi publik yang dapat diakses oleh masyarakat secara mudah, cepat, dan murah. PPID (Pejabat Pengelola Informasi dan Dokumentasi) adalah pejabat yang bertanggung jawab di bidang penyimpanan, pendokumentasian, penyediaan, dan/atau pelayanan informasi di Badan Publik.

Website PPID ini dirancang untuk:
- Memenuhi kewajiban hukum keterbukaan informasi publik
- Mempermudah masyarakat dalam mengakses informasi publik
- Menyediakan mekanisme permohonan informasi secara digital
- Meningkatkan transparansi dan akuntabilitas lembaga

---

## 2. Tujuan Produk

| Tujuan | Indikator Keberhasilan |
|--------|------------------------|
| Menyediakan portal informasi publik yang mudah diakses | Waktu load halaman < 3 detik |
| Digitalisasi permohonan informasi publik | 100% permohonan dapat diajukan online |
| Mendukung kepatuhan terhadap UU KIP | Tidak ada sanksi administrasi dari Komisi Informasi |
| Meningkatkan pelayanan informasi kepada masyarakat | Respons permohonan dalam 10 hari kerja |
| Menyediakan dashboard monitoring bagi admin PPID | Semua permohonan terlacak secara real-time |

---

## 3. Ruang Lingkup

### 3.1 In Scope

- Portal informasi publik (front-end publik)
- Sistem permohonan informasi (e-Request)
- Sistem keberatan & banding informasi
- Daftar Informasi Publik (DIP) — informasi berkala, serta-merta, setiap saat
- Manajemen dokumen & publikasi
- Dashboard admin PPID
- Laporan & statistik permohonan
- Notifikasi email/WhatsApp kepada pemohon

### 3.2 Out of Scope

- Sistem Kepegawaian (SIMPEG)
- Sistem Keuangan (SIMKEU)
- Integrasi SIAK Kependudukan
- Aplikasi mobile native (iOS/Android)

---

## 4. Pengguna (Stakeholder)

### 4.1 Pengguna Publik (Guest / Pemohon)

- Warga masyarakat umum
- Jurnalis / Media
- Peneliti / Akademisi
- Organisasi masyarakat sipil (LSM)

**Kemampuan:**
- Mengakses informasi publik tanpa registrasi
- Mengajukan permohonan informasi (wajib registrasi)
- Melacak status permohonan
- Mengajukan keberatan atas jawaban PPID

### 4.2 Admin PPID (Internal)

| Peran | Tanggung Jawab |
|-------|----------------|
| **Super Admin** | Kelola seluruh sistem, user management |
| **Admin PPID Utama** | Kelola permohonan, validasi, jawab permohonan |
| **Admin PPID Pembantu** | Upload dokumen, kelola informasi unit kerja |
| **Pimpinan / Atasan PPID** | Monitor dashboard, approve keberatan |

---

## 5. Fitur & Persyaratan Fungsional

### 5.1 Modul Portal Publik

#### F-01: Halaman Beranda
- Hero section dengan informasi singkat PPID
- Quick links: Permohonan Informasi, Informasi Berkala, Daftar Informasi
- Statistik publik: jumlah informasi, permohonan terlayani, pengguna terdaftar
- Berita & pengumuman terkini
- Struktur organisasi PPID

#### F-02: Daftar Informasi Publik (DIP)

Informasi wajib dibagi dalam 3 kategori sesuai UU KIP:

| Jenis | Deskripsi |
|-------|-----------|
| **Berkala** | Dipublikasikan secara rutin (min. 6 bulan sekali) |
| **Serta-Merta** | Disebarkan segera saat dibutuhkan masyarakat |
| **Setiap Saat** | Tersedia kapan pun diminta (DTKS, anggaran, dll) |

Fitur:
- Filter berdasarkan kategori, unit kerja, tahun
- Pencarian full-text
- Preview & download dokumen (PDF, XLSX, dll)
- Breadcrumb dan metadata dokumen (tanggal publish, ukuran file)

#### F-03: Sistem Permohonan Informasi (e-Request)

**Alur Permohonan:**
```
Pemohon Registrasi/Login → Isi Form Permohonan → Upload KTP (opsional)
→ Kirim Permohonan → Notifikasi ke PPID → PPID Validasi (1×24 jam)
→ PPID Proses (max 10 hari kerja) → Kirim Jawaban → Pemohon Terima Jawaban
```

**Form Permohonan:**
- Identitas pemohon (nama, NIK, alamat, telepon, email)
- Tujuan penggunaan informasi
- Rincian informasi yang dimohon
- Format yang diinginkan (digital/cetak)
- Upload surat kuasa (jika mewakili)

**Status Permohonan:**
- `draft` → `submitted` → `in_review` → `in_process` → `answered` / `rejected`

#### F-04: Sistem Keberatan

- Pemohon dapat mengajukan keberatan jika:
  - Permohonan ditolak tanpa alasan jelas
  - Informasi tidak diberikan dalam batas waktu
  - Biaya tidak wajar
- Form keberatan online dengan upload bukti
- Tracking status keberatan
- Integrasi notifikasi email

#### F-05: Pencarian & Filter Dokumen

- Pencarian global (full-text search menggunakan Laravel Scout + Meilisearch)
- Filter: kategori, unit kerja, tahun, jenis dokumen
- Sorting: terbaru, terpopuler, relevansi
- Highlight kata kunci pada hasil pencarian

#### F-06: Halaman Statis

- Tentang PPID
- Dasar Hukum
- Struktur Organisasi (dengan foto pejabat)
- Alur Permohonan Informasi
- Standar Layanan
- FAQ
- Kontak & Lokasi

---

### 5.2 Modul Admin PPID

#### F-07: Dashboard Admin

- Ringkasan permohonan: total, pending, diproses, selesai, ditolak
- Grafik tren permohonan (bulanan/tahunan)
- Daftar permohonan terbaru
- Alert: permohonan mendekati batas waktu
- Statistik informasi yang paling banyak diakses

#### F-08: Manajemen Permohonan

- Daftar permohonan dengan filter & pencarian
- Detail permohonan + riwayat aktivitas
- Assign permohonan ke admin/unit kerja
- Upload dokumen jawaban
- Kirim notifikasi ke pemohon
- Export laporan (Excel/PDF)

#### F-09: Manajemen Konten & Dokumen

- Upload dokumen (drag & drop)
- Kategorisasi informasi (berkala/serta-merta/setiap saat)
- Editor teks kaya (TinyMCE/Quill) untuk berita/pengumuman
- Pengaturan visibilitas (draft/published/archived)
- Versioning dokumen

#### F-10: Manajemen User

- CRUD user admin
- Role & permission management (berbasis Spatie Laravel Permission)
- Log aktivitas user
- Reset password

#### F-11: Laporan & Statistik

- Laporan permohonan per periode
- Laporan per unit kerja
- Laporan keberatan
- Export ke PDF & Excel
- Grafik interaktif (Chart.js / ApexCharts via Vue)

#### F-12: Pengaturan Sistem

- Profil organisasi / instansi
- Template email notifikasi
- Pengaturan batas waktu respons (default: 10 hari kerja)
- Manajemen kategori & tag
- Banner & pengumuman situs

---

## 6. Persyaratan Non-Fungsional

### 6.1 Performa
- Waktu load halaman pertama < 3 detik (LCP)
- Time to Interactive (TTI) < 5 detik
- Mendukung minimal 500 concurrent users
- Uptime minimal 99.5% per bulan

### 6.2 Keamanan
- Autentikasi menggunakan Laravel Sanctum (SPA Token)
- HTTPS wajib di seluruh endpoint
- Rate limiting pada endpoint permohonan dan login
- Validasi & sanitasi input server-side
- CSRF protection
- XSS protection (Content Security Policy)
- Upload file: validasi MIME type, scan antivirus (opsional)

### 6.3 Aksesibilitas
- Memenuhi standar WCAG 2.1 Level AA
- Responsif di semua ukuran layar (mobile-first)
- Mendukung screen reader
- Konten dalam Bahasa Indonesia

### 6.4 Kompatibilitas Browser
- Chrome (terbaru)
- Firefox (terbaru)
- Safari (terbaru)
- Edge (terbaru)
- Mobile: Chrome Android, Safari iOS

### 6.5 Skalabilitas
- Arsitektur siap untuk horizontal scaling
- Implementasi caching (Redis)
- Queue worker untuk notifikasi email (Laravel Queue)
- Lazy loading aset dan gambar

---

## 7. Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────┐
│                    CLIENT LAYER                          │
│    Vue 3 + Vite + Pinia + Vue Router + Tailwind CSS     │
│    (SPA — Single Page Application)                       │
└──────────────────────────┬──────────────────────────────┘
                           │ HTTPS / REST API
┌──────────────────────────▼──────────────────────────────┐
│                    API LAYER                             │
│           Laravel 13 — RESTful JSON API                  │
│     Laravel Sanctum (Auth) · Laravel Scout (Search)     │
│     Spatie Permission · Spatie Media Library             │
│     Laravel Queue (Jobs) · Laravel Notification         │
└──────────┬──────────────────────────────────────────────┘
           │                        │
┌──────────▼──────────┐   ┌────────▼────────────┐
│   MySQL / PostgreSQL │   │       Redis          │
│   (Database Utama)  │   │   (Cache & Queue)    │
└─────────────────────┘   └─────────────────────┘
           │
┌──────────▼──────────┐   ┌─────────────────────┐
│   Meilisearch       │   │   Storage            │
│   (Full-text Search)│   │   (S3 / Local Disk)  │
└─────────────────────┘   └─────────────────────┘
```

### 7.1 Stack Teknologi Detail

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| Backend Framework | Laravel | 13.x |
| PHP | PHP | 8.3+ |
| Frontend Framework | Vue | 3.x |
| Build Tool | Vite | 5.x |
| State Management | Pinia | 2.x |
| Routing (SPA) | Vue Router | 4.x |
| CSS Framework | Tailwind CSS | 3.x |
| UI Component | shadcn/vue atau PrimeVue | Latest |
| HTTP Client | Axios | Latest |
| Database | MySQL 8 / PostgreSQL 15 | — |
| Cache & Queue | Redis | 7.x |
| Full-text Search | Meilisearch + Laravel Scout | — |
| Auth | Laravel Sanctum | — |
| File Storage | Laravel Media Library + S3 | — |
| Email | SMTP / Mailgun | — |
| Permission | Spatie Laravel Permission | — |
| Charts | ApexCharts | Latest |
| Rich Text Editor | Tiptap (Vue) | Latest |

---

## 8. Struktur Database

### Tabel Utama

```sql
-- Users (pemohon & admin)
users
  id, name, nik, email, phone, address, role, email_verified_at,
  password, remember_token, created_at, updated_at

-- Kategori Informasi
information_categories
  id, name, slug, type (berkala|serta_merta|setiap_saat),
  description, icon, is_active, created_at, updated_at

-- Unit Kerja / OPD
work_units
  id, code, name, head_name, head_title, description,
  is_active, created_at, updated_at

-- Dokumen / Informasi Publik
public_informations
  id, category_id, work_unit_id, title, slug, description,
  content, file_path, file_size, file_type, download_count,
  view_count, status (draft|published|archived), published_at,
  created_by, created_at, updated_at

-- Permohonan Informasi
information_requests
  id, request_number (auto: PPID-YYYY-NNNN), user_id,
  purpose, information_detail, format_requested (digital|cetak),
  status (draft|submitted|in_review|in_process|answered|rejected),
  rejection_reason, due_date, assigned_to, created_at, updated_at

-- Jawaban Permohonan
request_responses
  id, request_id, admin_id, response_text, file_path,
  responded_at, created_at, updated_at

-- Keberatan
objections
  id, request_id, user_id, reason, evidence_file,
  status (submitted|in_review|answered|escalated),
  response_text, responded_at, created_at, updated_at

-- Log Aktivitas Permohonan
request_logs
  id, request_id, user_id, action, description,
  old_status, new_status, created_at

-- Berita & Pengumuman
news
  id, title, slug, excerpt, content, thumbnail, category,
  is_published, published_at, author_id, view_count,
  created_at, updated_at

-- Notifikasi
notifications
  id, user_id, type, data (JSON), read_at, created_at, updated_at
```

---

## 9. Desain UI/UX

### 9.1 Prinsip Desain

- **Simplicity:** Navigasi intuitif, tanpa hambatan untuk akses informasi
- **Government-Friendly:** Tampilan formal namun modern, sesuai identitas lembaga
- **Accessibility First:** Kontras warna memadai, teks terbaca jelas
- **Mobile First:** Desain dari layar kecil ke besar

### 9.2 Palet Warna (Contoh)

| Elemen | Warna | Hex |
|--------|-------|-----|
| Primary | Biru Pemerintah | `#1B4F8A` |
| Secondary | Emas | `#F4A823` |
| Accent | Hijau Sukses | `#28A745` |
| Danger | Merah | `#DC3545` |
| Background | Abu Terang | `#F8F9FA` |
| Text | Hitam Lembut | `#212529` |

### 9.3 Halaman Utama (Sitemap)

```
/ (Beranda)
├── /informasi-publik
│   ├── /berkala
│   ├── /serta-merta
│   └── /setiap-saat
├── /permohonan
│   ├── /buat (form permohonan)
│   └── /lacak/:nomor
├── /keberatan
├── /berita
├── /tentang-ppid
├── /dasar-hukum
├── /alur-layanan
├── /faq
└── /kontak

/auth
├── /login
├── /register
└── /forgot-password

/admin (protected)
├── /dashboard
├── /permohonan
├── /dokumen
├── /berita
├── /keberatan
├── /laporan
├── /pengguna
└── /pengaturan
```

---

## 10. API Endpoint

### 10.1 Public Endpoints

```
GET    /api/informations              # Daftar informasi publik
GET    /api/informations/{slug}       # Detail informasi
GET    /api/categories                # Daftar kategori
GET    /api/news                      # Daftar berita
GET    /api/news/{slug}               # Detail berita
GET    /api/work-units                # Daftar unit kerja
GET    /api/search?q={query}          # Pencarian global
```

### 10.2 Auth Endpoints

```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/forgot-password
POST   /api/auth/reset-password
GET    /api/auth/me
```

### 10.3 Request (Authenticated)

```
GET    /api/requests                  # Daftar permohonan saya
POST   /api/requests                  # Buat permohonan baru
GET    /api/requests/{id}             # Detail permohonan
GET    /api/requests/track/{number}   # Lacak permohonan via nomor
POST   /api/requests/{id}/objection   # Ajukan keberatan
```

### 10.4 Admin Endpoints (Admin Only)

```
GET    /api/admin/dashboard           # Data dashboard
GET    /api/admin/requests            # Semua permohonan
PUT    /api/admin/requests/{id}/status
POST   /api/admin/requests/{id}/respond
GET    /api/admin/informations
POST   /api/admin/informations
PUT    /api/admin/informations/{id}
DELETE /api/admin/informations/{id}
GET    /api/admin/reports
GET    /api/admin/users
```

---

## 11. Keamanan

### 11.1 Autentikasi & Otorisasi

- Laravel Sanctum untuk token-based auth (SPA)
- Spatie Laravel Permission untuk RBAC (Role-Based Access Control)
- Session timeout: 8 jam (admin), 24 jam (pemohon)
- Verifikasi email wajib sebelum mengajukan permohonan

### 11.2 Perlindungan Data

- Data NIK pemohon dienkripsi di database (`AES-256`)
- Dokumen permohonan hanya dapat diakses oleh pemohon & admin terkait
- Audit log setiap perubahan status permohonan

### 11.3 Keamanan Aplikasi

- Rate Limiting: 60 request/menit (public), 10 request/menit (login)
- Upload file: max 10MB, whitelist MIME (pdf, xlsx, docx, jpg, png)
- Input sanitasi via Laravel Form Request Validation
- SQL Injection: Eloquent ORM (parameterized query)
- XSS: Vue auto-escaping + Laravel `htmlspecialchars`

---

## 12. Milestone & Timeline

| Fase | Deliverable | Durasi | Target |
|------|-------------|--------|--------|
| **Fase 1** | Setup proyek, ERD, desain UI/UX, struktur DB | 2 minggu | Minggu 1–2 |
| **Fase 2** | Backend API: Auth, Informasi Publik, Pencarian | 3 minggu | Minggu 3–5 |
| **Fase 3** | Backend API: Permohonan, Keberatan, Notifikasi | 3 minggu | Minggu 6–8 |
| **Fase 4** | Frontend Vue: Portal Publik (beranda, daftar info, permohonan) | 3 minggu | Minggu 9–11 |
| **Fase 5** | Frontend Vue: Admin Dashboard & Manajemen | 3 minggu | Minggu 12–14 |
| **Fase 6** | Laporan, Statistik, Export | 1 minggu | Minggu 15 |
| **Fase 7** | Testing (UAT, Security, Performance) | 2 minggu | Minggu 16–17 |
| **Fase 8** | Deployment, Training, Go-Live | 1 minggu | Minggu 18 |

**Total Estimasi: ±18 Minggu (4,5 Bulan)**

---

## 13. Kriteria Keberhasilan

### Teknis
- [ ] Semua endpoint API mengembalikan respons < 500ms
- [ ] Test coverage backend minimal 70%
- [ ] Zero critical security vulnerability pada OWASP Top 10
- [ ] Lighthouse score ≥ 85 (Performance, Accessibility, SEO)

### Bisnis
- [ ] Semua jenis informasi publik sesuai UU KIP tersedia
- [ ] Alur permohonan informasi dapat diselesaikan end-to-end
- [ ] Admin dapat menjawab permohonan dalam sistem dalam < 10 menit
- [ ] Laporan dapat diekspor ke Excel dan PDF

### Compliance
- [ ] Memenuhi ketentuan Permendagri / Peraturan KI terkait PPID
- [ ] SOP layanan informasi terdokumentasi dalam sistem
- [ ] Log audit setiap aktivitas permohonan tersimpan min. 5 tahun

---

## 14. Referensi Regulasi

1. **UU No. 14 Tahun 2008** — Keterbukaan Informasi Publik
2. **PP No. 61 Tahun 2010** — Pelaksanaan UU KIP
3. **Permendagri No. 35 Tahun 2010** — Pedoman Pengelolaan Pelayanan Informasi dan Dokumentasi di Lingkungan Kemendagri
4. **Peraturan Komisi Informasi No. 1 Tahun 2010** — Standar Layanan Informasi Publik
5. **Peraturan KI No. 1 Tahun 2021** — SLIP (Standar Layanan Informasi Publik) terbaru

---

## Lampiran

### A. Glosarium

| Istilah | Definisi |
|---------|----------|
| PPID | Pejabat Pengelola Informasi dan Dokumentasi |
| KIP | Keterbukaan Informasi Publik |
| DIP | Daftar Informasi Publik |
| Badan Publik | Lembaga yang tunduk pada UU KIP |
| SPA | Single Page Application |
| API | Application Programming Interface |
| RBAC | Role-Based Access Control |

### B. Asumsi & Dependensi

- Server telah tersedia dengan spesifikasi minimal: 4 vCPU, 8GB RAM, 100GB SSD
- Domain dan SSL certificate sudah disiapkan oleh instansi
- Identitas visual (logo, warna resmi) disediakan oleh instansi
- Konten awal (struktur organisasi, dasar hukum, informasi berkala) disediakan oleh tim PPID

---

*Dokumen ini bersifat living document dan akan diperbarui seiring perkembangan proyek.*

**Dibuat oleh:** Tim Pengembang  
**Disetujui oleh:** ________________________  
**Tanggal Persetujuan:** ____________________
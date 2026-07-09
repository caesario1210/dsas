# Task Development Plan

# Dealer Sales Analytics System (DSAS)

**Referensi:** PRD.md (v1.0), architectur.md (v1.0)
**Prinsip Penyusunan:** Dokumen ini disusun berdasarkan **alur kerja sistem (system execution order)**, bukan alur berpikir pengguna. Urutan mengikuti Data Pipeline yang menjadi jantung arsitektur: Setup → Database → Auth → Upload → ETL → KPI Engine → Dashboard → Insight → Report → Testing → Deployment.

Setiap task WAJIB mengikuti aturan di `architectur.md` Bagian 7 (AI Development Guidelines) — khususnya: KPI hanya dihitung di Backend, Dashboard tidak boleh menghitung/query, Controller tipis, business logic di Service, query kompleks di Repository.

---

## Phase 0 — Project Setup & Infrastructure

Fondasi teknis sebelum modul bisnis dikerjakan.

- [ ] Inisialisasi repository (backend & frontend, git, `.gitignore`)
- [ ] Setup project Laravel (Backend) — struktur folder: Controllers, Services, Repositories, Models, Middleware
- [ ] Setup project React (Frontend) — struktur folder: pages/, components/, services/, hooks/
- [ ] Konfigurasi koneksi database MySQL (`.env`, `config/database.php`)
- [ ] Konfigurasi CORS & komunikasi REST API antara React ↔ Laravel
- [ ] Setup konvensi response API (format JSON konsisten, HTTP status code standar, error handler global)
- [ ] Setup base routing API (`routes/api.php`) sesuai peta modul
- [ ] Setup environment terpisah (local/dev) & dokumentasi cara menjalankan project (README)

---

## Phase 1 — Database Module (Foundational Schema)

Skema database harus tersedia sebelum modul lain (Auth, ETL, KPI) dapat dibangun.

- [ ] Rancang ERD: users, roles, sales_periods, sales_transactions, dealers, products, branches (cabang), dsb.
- [ ] Buat migration untuk seluruh tabel, gunakan Primary Key & Foreign Key sesuai relasi
- [ ] Buat migration pendukung histori periode (agar data lama tidak terhapus saat upload baru — FR-017)
- [ ] Buat Seeder untuk data awal (role, akun admin/manager default)
- [ ] Buat Factory untuk kebutuhan testing data dummy (khusus environment testing, bukan fitur final)
- [ ] Validasi struktur skema mendukung analisis historis per periode (FR-018)

---

## Phase 2 — Authentication Module

- [ ] Buat Model `User` + Role (Admin/Manager)
- [ ] Implementasi Service `AuthService` (login, logout, hash password)
- [ ] Implementasi endpoint login (FR-001), gunakan token/session-based auth
- [ ] Implementasi middleware Role-based Access Control: Admin (upload + ETL) vs Manager (view + export only) (FR-002, FR-003, FR-004)
- [ ] Implementasi endpoint logout & session management
- [ ] Implementasi validasi input login (email/username, password)
- [ ] Buat halaman Login (Frontend) — form + error handling
- [ ] Buat route guard di Frontend berdasarkan role (redirect sesuai hak akses)
- [ ] Unit test: login sukses, login gagal, akses ditolak sesuai role

---

## Phase 3 — Upload Module

- [ ] Buat endpoint upload file (CSV/XLSX) — FR-005
- [ ] Implementasi validasi format file sebelum diproses (ekstensi, ukuran) — FR-006
- [ ] Implementasi parsing awal file (baca struktur kolom, baris) di Service layer
- [ ] Buat endpoint preview data sebelum import — FR-007
- [ ] Implementasi status tracking proses upload (uploading/validating/processing/done/failed) — FR-008
- [ ] Buat komponen Frontend: Upload area (drag & drop), progress indicator, preview table
- [ ] Frontend hanya mengirim file — tidak boleh melakukan parsing/validasi bisnis di client
- [ ] Unit/integration test: upload file valid, file format salah, file kosong

---

## Phase 4 — ETL Module (Inti Sistem)

Seluruh proses berikut dilakukan di Backend (Service Layer), mengikuti urutan wajib: Validation → Cleaning → Transformation → Import.

### 4.1 Validation
- [ ] Validasi struktur kolom sesuai template yang ditentukan — FR-009
- [ ] Validasi tipe data tiap kolom — FR-010
- [ ] Deteksi data duplikat — FR-011
- [ ] Deteksi missing value — FR-012

### 4.2 Cleaning & Transformation
- [ ] Standardisasi format tanggal, huruf besar/kecil, nilai numerik, spasi berlebih — FR-013
- [ ] Normalisasi penamaan dealer/cabang agar konsisten

### 4.3 Import
- [ ] Implementasi ringkasan hasil ETL (rows uploaded, imported, duplicate, missing value, failed) — FR-014
- [ ] Pastikan data yang tidak lolos validasi TIDAK masuk ke database — FR-015
- [ ] Implementasi transaksi database (DB transaction) agar import konsisten, tidak ada data terhitung ganda (NFR-002)
- [ ] Buat komponen Frontend: tampilan ringkasan hasil ETL setelah proses selesai
- [ ] Unit test: data valid penuh, data campur (sebagian gagal), file duplikat penuh, missing value

---

## Phase 5 — KPI Engine

KPI hanya dihitung di Backend menggunakan query SQL. Frontend hanya menerima hasil.

- [ ] Implementasi Repository untuk query KPI kompleks (agregasi SQL)
- [ ] Implementasi Service `KpiEngineService`:
  - [ ] Total Sales, Revenue, Profit, Profit Margin
  - [ ] Target Achievement, Sales Growth
  - [ ] Dealer Ranking, Dealer Achievement, Dealer Revenue/Profit/Growth
  - [ ] Product Ranking, Product Revenue/Profit, Product Sales Contribution
  - [ ] Monthly Sales (untuk tren bulanan)
- [ ] Trigger otomatis: KPI dihitung ulang setiap kali proses import berhasil — FR-019, FR-021
- [ ] Buat endpoint API untuk mengambil hasil KPI (read-only, sudah terhitung)
- [ ] Unit test: validasi rumus tiap KPI dengan dataset kontrol

---

## Phase 6 — Dashboard Module

Dashboard murni presentasi. Tidak boleh menjalankan query SQL atau business logic.

- [ ] Buat endpoint API agregat khusus dashboard (menggabungkan KPI cards + chart + ranking dalam response siap pakai)
- [ ] Implementasi filter di Backend: Tahun, Bulan, Cabang, Dealer — FR-026
- [ ] Frontend: komponen KPI Cards (Total Sales, Revenue, Profit, Margin, Achievement) — FR-022
- [ ] Frontend: komponen Chart (Sales Trend, Revenue Trend, Profit Trend, Monthly Comparison) — FR-023
- [ ] Frontend: komponen Ranking (Top/Bottom Dealer, Top/Bottom Product) — FR-024, FR-025
- [ ] Frontend: komponen Filter Bar (Tahun/Bulan/Cabang/Dealer) yang memanggil ulang API
- [ ] Pastikan seluruh angka yang tampil berasal langsung dari response API (tidak ada kalkulasi ulang di client)
- [ ] Uji performa: dashboard tampil < 5 detik sesuai NFR-001

---

## Phase 7 — Business Insight Module

- [ ] Implementasi `InsightRuleEngine` (rule-based, bukan AI/ML) — FR-027
- [ ] Definisikan rule bisnis, contoh:
  - Revenue naik/turun dibanding bulan sebelumnya
  - Dealer dengan pencapaian target tertinggi/terendah
  - Produk dengan penjualan tertinggi
  - Cabang mengalami penurunan profit
- [ ] Insight diperbarui otomatis setiap data berubah — FR-028
- [ ] Buat endpoint API untuk mengambil daftar insight
- [ ] Frontend: komponen tampilan Business Insight (list/card ringkas)
- [ ] Unit test: validasi rule menghasilkan insight yang sesuai skenario data

---

## Phase 8 — Reporting Module (Export)

- [ ] Implementasi export laporan PDF (Dashboard Summary, KPI, Grafik, Ranking, Insight) — FR-029
- [ ] Implementasi export data Excel — FR-030
- [ ] Pastikan layout laporan siap presentasi tanpa perlu editing tambahan
- [ ] Frontend: tombol export & pemilihan format (PDF/Excel)
- [ ] Unit/integration test: hasil export sesuai data dashboard saat itu

---

## Phase 9 — Testing

- [ ] Unit testing per Service (Auth, ETL, KPI Engine, Insight, Report)
- [ ] Integration testing alur pipeline penuh: Upload → ETL → Import → KPI → Dashboard
- [ ] Testing role-based access (Admin vs Manager) di seluruh endpoint
- [ ] Testing skenario data buruk: file rusak, kolom tidak sesuai template, duplikat masif
- [ ] Regression testing: upload periode baru tidak menghapus data lama (FR-017)
- [ ] Performance testing sesuai NFR-001 (login < 3s, dashboard < 5s, upload < 10s)
- [ ] Cross-browser testing (Chrome, Edge, Firefox) — NFR-007
- [ ] Responsive testing (desktop & tablet) — NFR-008

---

## Phase 10 — Deployment

- [ ] Setup environment production (server, database MySQL production)
- [ ] Konfigurasi environment variable production (.env production, secrets)
- [ ] Build & deploy Backend (Laravel)
- [ ] Build & deploy Frontend (React)
- [ ] Migrasi database di production (migration + seeder awal role/admin)
- [ ] Setup HTTPS & keamanan dasar server
- [ ] Smoke test end-to-end di production (login → upload → dashboard → export)
- [ ] Dokumentasi deployment & panduan penggunaan dasar untuk Admin/Manager

---

## Catatan Kepatuhan Arsitektur (Wajib Dicek Sebelum Merge/Review)

- [ ] Tidak ada kalkulasi KPI (Revenue/Profit/Margin/Achievement/Ranking) di Frontend
- [ ] Tidak ada query SQL langsung di Dashboard/Frontend
- [ ] Controller tetap tipis, seluruh logic ada di Service, query kompleks di Repository
- [ ] Tidak ada dummy data di fitur final — seluruh dashboard menggunakan data MySQL
- [ ] Tidak ada fitur di luar scope PRD (ERP, POS, CRM, AI/ML, prediksi) yang ditambahkan
- [ ] Alur pipeline tidak dilompati (Upload → Validation → Cleaning → Transformation → Import → KPI → Dashboard → Insight → Export)
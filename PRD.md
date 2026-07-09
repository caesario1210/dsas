# Product Requirements Document (PRD)

# Dealer Sales Analytics System (DSAS)

**Versi:** 1.0  
**Status:** Draft  
**Dokumen:** Product Requirements Document  
**Pemilik Produk:** Caesario Gumilang  
**Tanggal:** _(diisi saat finalisasi)_

---

# 1. Ringkasan Produk (Executive Summary)

Dealer Sales Analytics System (DSAS) adalah aplikasi Business Intelligence berbasis web yang dirancang untuk membantu proses analisis data penjualan dealer secara otomatis melalui alur ETL (Extract, Transform, Load). Sistem ini memungkinkan pengguna mengunggah data penjualan dalam format CSV atau XLSX, kemudian melakukan validasi, pembersihan data, transformasi, penyimpanan ke database, perhitungan KPI, hingga penyajian dashboard analitik dan laporan bisnis secara otomatis.

Berbeda dengan proses analisis manual menggunakan Microsoft Excel, DSAS mengintegrasikan seluruh proses pengolahan data ke dalam satu platform sehingga mampu mengurangi pekerjaan berulang, meningkatkan konsistensi data, dan mempercepat penyusunan laporan manajemen.

Proyek ini dikembangkan sebagai portfolio Data Analyst yang menunjukkan kemampuan dalam mengelola data secara end-to-end, mulai dari proses ETL, pengolahan data menggunakan SQL, perhitungan KPI bisnis, hingga visualisasi data dalam bentuk dashboard interaktif.

---

# 2. Latar Belakang

Pada banyak perusahaan, khususnya dealer otomotif, data penjualan biasanya diperoleh dari sistem operasional dalam bentuk file Microsoft Excel atau CSV.

Setiap akhir periode, Admin atau Data Analyst harus melakukan serangkaian pekerjaan seperti:

- Mengumpulkan file penjualan dari berbagai sumber.
- Memeriksa kelengkapan data.
- Membersihkan data yang tidak valid.
- Menghapus data duplikat.
- Menyesuaikan format data.
- Menghitung berbagai KPI menggunakan rumus Excel.
- Membuat Pivot Table.
- Membuat dashboard.
- Menyusun laporan untuk manajemen.

Proses tersebut dilakukan secara berulang setiap bulan dan memiliki beberapa kelemahan, antara lain:

- Membutuhkan waktu yang cukup lama.
- Rentan terhadap kesalahan perhitungan.
- Sulit menjaga konsistensi format data.
- Dashboard harus diperbarui secara manual.
- Pembuatan laporan menjadi tidak efisien.

Seiring bertambahnya volume data, proses manual menggunakan Excel menjadi semakin sulit dipertahankan.

Oleh karena itu, diperlukan sebuah sistem yang mampu mengotomatisasi proses pengolahan data sehingga pengguna hanya perlu mengunggah file penjualan, kemudian sistem akan melakukan seluruh proses analisis secara otomatis.

---

# 3. Permasalahan Bisnis (Business Problem)

Saat ini proses analisis penjualan masih sangat bergantung pada pekerjaan manual menggunakan Microsoft Excel.

Permasalahan utama yang ditemukan meliputi:

## 3.1 Proses Analisis Memakan Waktu

Setiap periode, pengguna harus mengulangi proses yang sama mulai dari membersihkan data hingga membuat dashboard.

Semakin besar jumlah data, semakin lama waktu yang dibutuhkan.

---

## 3.2 Tingginya Risiko Human Error

Kesalahan kecil seperti:

- Salah rumus
- Salah filter
- Data ganda
- Format tanggal berbeda
- Penamaan dealer tidak konsisten

dapat menyebabkan hasil analisis menjadi tidak akurat.

---

## 3.3 Dashboard Tidak Otomatis

Dashboard harus diperbarui secara manual setiap kali terdapat data baru.

Akibatnya proses pelaporan menjadi lambat.

---

## 3.4 Sulit Memantau Performa Secara Berkala

Manager membutuhkan informasi seperti:

- Dealer terbaik
- Produk terlaris
- Pertumbuhan penjualan
- Target Achievement
- Revenue
- Profit

Namun informasi tersebut baru tersedia setelah proses analisis selesai dilakukan.

---

## 3.5 Belum Ada Sistem Terintegrasi

Seluruh proses masih dilakukan menggunakan beberapa aplikasi secara terpisah seperti:

- Microsoft Excel
- Pivot Table
- Formula Manual
- Dashboard Terpisah

Belum terdapat satu sistem yang mampu menangani seluruh proses dari upload data hingga penyajian insight bisnis.

---

# 4. Tujuan Produk (Product Objectives)

Dealer Sales Analytics System dikembangkan untuk mencapai tujuan berikut.

## Tujuan Utama

Mengotomatisasi proses pengolahan data penjualan dealer sehingga pengguna cukup mengunggah file CSV atau XLSX, kemudian sistem secara otomatis menghasilkan dashboard dan laporan bisnis.

---

## Tujuan Bisnis

- Mengurangi waktu analisis data.
- Mengurangi kesalahan akibat proses manual.
- Menyediakan dashboard yang selalu menggunakan data terbaru.
- Membantu manajemen memonitor performa dealer.
- Mempermudah proses penyusunan laporan bulanan.

---

## Tujuan Pengguna

Pengguna dapat:

- Mengunggah file penjualan.
- Melihat hasil validasi data.
- Melihat hasil proses ETL.
- Mengakses dashboard secara real-time.
- Melihat KPI bisnis.
- Mendapatkan insight otomatis.
- Mengunduh laporan.

Tanpa perlu melakukan pengolahan data secara manual menggunakan Excel.

---

## Tujuan Portfolio

Selain sebagai aplikasi Business Intelligence, proyek ini bertujuan menunjukkan kemampuan dalam bidang:

- Data Analytics
- Business Intelligence
- ETL Process
- SQL
- Dashboard Development
- KPI Development
- Data Visualization
- Database Management

sehingga dapat digunakan sebagai portfolio profesional untuk posisi:

- Data Analyst
- Business Intelligence Analyst
- Business Analyst
- Junior Data Engineer

---

# 5. Target Pengguna & User Persona

Dealer Sales Analytics System dirancang untuk dua jenis pengguna utama.

## Persona 1 — Admin

### Profil

Admin bertanggung jawab mengelola data penjualan yang diterima setiap periode.

### Tugas

- Login ke sistem.
- Mengunggah file CSV/XLSX.
- Menjalankan proses ETL.
- Memastikan data berhasil diproses.
- Melihat dashboard.
- Mengunduh laporan.

### Pain Points

- Proses cleaning memakan waktu.
- Dashboard harus dibuat ulang.
- Sering terjadi kesalahan saat menggunakan Excel.
- Harus menghitung KPI secara manual.

### Goal

Menghasilkan dashboard dan laporan dengan cepat tanpa harus mengolah data secara manual.

---

## Persona 2 — Manager

### Profil

Manager membutuhkan informasi penjualan sebagai dasar pengambilan keputusan.

### Tugas

- Login ke sistem.
- Melihat dashboard.
- Memantau KPI.
- Membandingkan performa dealer.
- Melihat tren penjualan.
- Mengunduh laporan.

### Pain Points

- Sulit memperoleh laporan secara cepat.
- Dashboard sering terlambat diperbarui.
- Tidak memiliki insight secara langsung.

### Goal

Memperoleh informasi bisnis yang akurat, mudah dipahami, dan selalu menggunakan data terbaru untuk mendukung pengambilan keputusan.

---

# 6. Business Workflow

Business Workflow menggambarkan bagaimana data bergerak di dalam sistem hingga menghasilkan informasi yang dapat digunakan oleh pengguna.

Dealer Sales Analytics System dirancang untuk mengotomatisasi seluruh proses analisis data penjualan melalui satu alur kerja yang terintegrasi.

Workflow utama sistem adalah sebagai berikut:

```text
Dealer Export Data
        │
        ▼
Upload CSV / XLSX
        │
        ▼
Data Validation
        │
        ▼
Data Cleaning
        │
        ▼
Data Transformation
        │
        ▼
Import ke Database
        │
        ▼
KPI Calculation Engine
        │
        ▼
Dashboard Analytics
        │
        ▼
Business Insight
        │
        ▼
Export Report
```

Setiap tahapan memiliki tujuan yang berbeda.

| Tahapan | Tujuan |
|---------|---------|
| Upload | Menerima data penjualan dari pengguna |
| Validation | Memastikan struktur data sesuai standar |
| Cleaning | Menghapus atau memperbaiki data yang tidak valid |
| Transformation | Menyesuaikan format agar konsisten |
| Database | Menyimpan data yang telah valid |
| KPI Engine | Menghasilkan indikator performa bisnis |
| Dashboard | Menampilkan visualisasi data |
| Insight | Memberikan ringkasan performa bisnis |
| Export | Menghasilkan laporan siap dibagikan |

Business Workflow ini menjadi fondasi utama aplikasi dan merupakan proses inti yang membedakan DSAS dari dashboard biasa.

---

# 7. User Flow

## 7.1 User Flow Admin

Admin merupakan pengguna yang bertanggung jawab mengelola data penjualan.

Alur penggunaan sistem oleh Admin adalah sebagai berikut.

```text
Login
    │
    ▼
Dashboard
    │
    ▼
Upload CSV/XLSX
    │
    ▼
Preview File
    │
    ▼
Validasi Data
    │
    ▼
Cleaning & Transformation
    │
    ▼
Import Database
    │
    ▼
KPI Dihitung Otomatis
    │
    ▼
Dashboard Diperbarui
    │
    ▼
Lihat Insight
    │
    ▼
Export Report
```

---

## 7.2 User Flow Manager

Manager tidak melakukan upload data.

Manager hanya menggunakan hasil analisis.

```text
Login
    │
    ▼
Dashboard
    │
    ▼
Filter Data
    │
    ▼
Melihat KPI
    │
    ▼
Melihat Ranking
    │
    ▼
Melihat Insight
    │
    ▼
Export Report
```

---

# 8. Fitur Utama Sistem

Dealer Sales Analytics System terdiri dari lima modul utama.

## 8.1 Authentication

Authentication digunakan untuk mengatur hak akses pengguna.

Fitur meliputi:

- Login
- Logout
- Role Admin
- Role Manager
- Session Management

Admin memiliki hak untuk mengunggah data.

Manager hanya memiliki hak melihat hasil analisis.

---

## 8.2 Upload & ETL

Modul ini merupakan inti dari sistem.

Fitur meliputi:

- Upload CSV
- Upload XLSX
- Drag & Drop Upload
- Preview Data
- Validasi Kolom
- Validasi Tipe Data
- Duplicate Detection
- Missing Value Detection
- Data Cleaning
- Data Transformation
- Import ke Database

Setelah proses selesai sistem menampilkan ringkasan ETL.

Contoh:

Rows Uploaded

15.320

Rows Imported

15.250

Duplicate

42

Missing Value

18

Failed

10

---

## 8.3 KPI Engine

Setelah data berhasil masuk ke database, sistem secara otomatis menghitung KPI bisnis.

Perhitungan dilakukan menggunakan query SQL sehingga pengguna tidak perlu menghitung secara manual.

KPI yang dihasilkan antara lain:

- Total Sales
- Revenue
- Profit
- Margin
- Target Achievement
- Sales Growth
- Dealer Ranking
- Product Ranking
- Monthly Sales

Seluruh KPI akan diperbarui setiap kali data baru berhasil diimpor.

---

## 8.4 Dashboard Analytics

Dashboard merupakan media utama untuk menyajikan hasil analisis.

Dashboard harus interaktif, mudah dipahami, dan mendukung proses pengambilan keputusan.

Dashboard minimal memiliki:

### KPI Cards

- Total Sales
- Revenue
- Profit
- Margin
- Achievement

### Charts

- Sales Trend
- Revenue Trend
- Profit Trend
- Monthly Comparison

### Ranking

- Top Dealer
- Bottom Dealer
- Top Product
- Bottom Product

### Filter

- Tahun
- Bulan
- Cabang
- Dealer

Semua komponen dashboard diperbarui secara otomatis berdasarkan data terbaru.

---

## 8.5 Business Insight

Business Insight merupakan ringkasan hasil analisis yang dihasilkan secara otomatis menggunakan rule-based system.

Insight bertujuan membantu Manager memahami kondisi bisnis tanpa harus membaca seluruh dashboard.

Contoh insight:

- Revenue meningkat dibanding bulan sebelumnya.
- Dealer Surabaya memiliki pencapaian target tertinggi.
- Produk Beat menjadi produk dengan penjualan tertinggi.
- Cabang Bandung mengalami penurunan profit.

Insight tidak menggunakan Artificial Intelligence maupun Large Language Model.

Seluruh insight dihasilkan berdasarkan aturan bisnis yang telah ditentukan.

---

## 8.6 Export Report

Pengguna dapat mengunduh hasil analisis.

Format laporan:

- PDF
- Excel

Isi laporan meliputi:

- Dashboard Summary
- KPI
- Grafik
- Ranking
- Business Insight

Laporan dirancang agar siap digunakan sebagai bahan presentasi kepada manajemen tanpa perlu proses editing tambahan.

# 9. Kebutuhan Fungsional (Functional Requirements)

Bagian ini mendefinisikan kemampuan utama yang wajib dimiliki oleh Dealer Sales Analytics System. Seluruh kebutuhan fungsional menjadi dasar pengembangan sistem dan akan diterjemahkan menjadi task pengembangan pada tahap implementasi.

## Modul Authentication

### FR-001
Sistem harus menyediakan halaman login untuk pengguna.

### FR-002
Sistem harus membedakan hak akses berdasarkan role pengguna (Admin dan Manager).

### FR-003
Admin memiliki hak untuk mengunggah data dan menjalankan proses ETL.

### FR-004
Manager hanya memiliki hak untuk melihat hasil analisis dan mengunduh laporan.

---

## Modul Upload Data

### FR-005
Sistem harus menerima file dengan format CSV dan XLSX.

### FR-006
Sistem harus melakukan validasi format file sebelum diproses.

### FR-007
Sistem harus menampilkan preview data sebelum proses import dilakukan.

### FR-008
Sistem harus menampilkan status proses upload kepada pengguna.

---

## Modul ETL

### FR-009
Sistem harus memvalidasi struktur kolom sesuai template yang telah ditentukan.

### FR-010
Sistem harus memvalidasi tipe data setiap kolom.

### FR-011
Sistem harus mendeteksi data duplikat.

### FR-012
Sistem harus mendeteksi nilai yang kosong (missing value).

### FR-013
Sistem harus melakukan standardisasi format data.

Contoh:

- Format tanggal
- Huruf besar dan kecil
- Nilai numerik
- Spasi berlebih

### FR-014
Sistem harus memberikan ringkasan hasil proses ETL sebelum data disimpan.

### FR-015
Data yang tidak lolos validasi tidak boleh masuk ke database.

---

## Modul Database

### FR-016
Sistem harus menyimpan data yang telah lolos validasi ke dalam database MySQL.

### FR-017
Data lama tidak boleh terhapus ketika pengguna mengunggah data periode baru.

### FR-018
Sistem harus mendukung analisis historis berdasarkan periode.

---

## Modul KPI Engine

### FR-019
Sistem harus menghitung KPI secara otomatis setelah proses import berhasil.

### FR-020
Perhitungan KPI harus dilakukan menggunakan query SQL.

### FR-021
Setiap perubahan data harus memperbarui seluruh KPI terkait.

---

## Modul Dashboard

### FR-022
Sistem harus menampilkan KPI dalam bentuk kartu ringkasan.

### FR-023
Sistem harus menampilkan grafik tren penjualan.

### FR-024
Sistem harus menampilkan ranking dealer.

### FR-025
Sistem harus menampilkan ranking produk.

### FR-026
Sistem harus menyediakan filter berdasarkan:

- Tahun
- Bulan
- Cabang
- Dealer

---

## Modul Business Insight

### FR-027
Sistem harus menghasilkan insight berdasarkan aturan bisnis (rule-based).

### FR-028
Insight harus diperbarui setiap kali data berubah.

---

## Modul Reporting

### FR-029
Sistem harus mendukung export laporan dalam format PDF.

### FR-030
Sistem harus mendukung export data dalam format Excel.

---

# 10. Kebutuhan Non-Fungsional (Non-Functional Requirements)

Selain memenuhi kebutuhan fungsional, sistem juga harus memenuhi standar kualitas agar nyaman digunakan, mudah dikembangkan, dan dapat diandalkan.

## NFR-001 Performance

Dashboard harus dapat ditampilkan dengan cepat meskipun jumlah data terus bertambah.

Target waktu:

- Login < 3 detik
- Dashboard < 5 detik
- Upload file < 10 detik (tergantung ukuran file)

---

## NFR-002 Reliability

Sistem harus menjaga konsistensi data selama proses upload, ETL, dan perhitungan KPI.

Tidak boleh terdapat data yang terhitung ganda akibat proses import berulang.

---

## NFR-003 Usability

Antarmuka harus sederhana dan mudah digunakan oleh pengguna yang terbiasa bekerja menggunakan Microsoft Excel.

Pengguna tidak memerlukan pelatihan khusus untuk mengoperasikan sistem.

---

## NFR-004 Scalability

Sistem harus tetap dapat menangani peningkatan jumlah data pada periode berikutnya tanpa perubahan arsitektur yang signifikan.

---

## NFR-005 Maintainability

Struktur kode harus modular sehingga mudah dikembangkan dan dipelihara.

Setiap modul harus memiliki tanggung jawab yang jelas.

---

## NFR-006 Security

Pengguna wajib melakukan login.

Hak akses Admin dan Manager harus dipisahkan.

Password disimpan dalam bentuk hash.

---

## NFR-007 Compatibility

Sistem dapat digunakan melalui browser modern seperti:

- Google Chrome
- Microsoft Edge
- Mozilla Firefox

Tanpa instalasi aplikasi tambahan.

---

## NFR-008 Responsive Design

Dashboard harus tetap nyaman digunakan pada desktop maupun tablet.

---

# 11. KPI (Key Performance Indicator)

KPI merupakan indikator utama yang digunakan untuk mengukur performa penjualan.

## KPI Penjualan

- Total Sales
- Revenue
- Profit
- Profit Margin
- Target Achievement
- Sales Growth

---

## KPI Dealer

- Dealer Ranking
- Dealer Achievement
- Dealer Revenue
- Dealer Profit
- Dealer Growth

---

## KPI Produk

- Product Ranking
- Product Revenue
- Product Profit
- Product Sales Contribution

---

## KPI Dashboard

Dashboard minimal harus mampu menjawab pertanyaan berikut:

- Berapa total penjualan periode ini?
- Apakah target telah tercapai?
- Dealer mana yang memiliki performa terbaik?
- Produk apa yang paling banyak terjual?
- Bagaimana tren penjualan setiap bulan?
- Bagaimana pertumbuhan dibanding periode sebelumnya?

Seluruh KPI dihitung secara otomatis menggunakan data yang berhasil melalui proses ETL.

---

# 12. Ruang Lingkup (Project Scope)

## In Scope

Fitur yang termasuk dalam pengembangan proyek:

- Authentication
- Upload CSV/XLSX
- Data Validation
- Data Cleaning
- Data Transformation
- Import Database
- KPI Calculation
- Dashboard Analytics
- Business Insight (Rule-Based)
- Export PDF
- Export Excel
- Filter Dashboard
- Historical Data Analysis

---

# 13. Di Luar Ruang Lingkup (Out of Scope)

Agar proyek tetap fokus sebagai portfolio Data Analyst, fitur berikut tidak termasuk dalam pengembangan:

- ERP
- Point of Sales (POS)
- Inventory Management
- Purchasing
- Accounting
- Finance Module
- Customer Management (CRM)
- Manajemen Gudang
- Input transaksi penjualan secara manual
- Integrasi dengan sistem dealer asli
- Artificial Intelligence / Large Language Model
- Prediksi penjualan menggunakan Machine Learning

Fitur-fitur tersebut dapat dipertimbangkan sebagai pengembangan lanjutan, tetapi bukan bagian dari versi pertama (MVP).

---

# 14. Asumsi dan Batasan

## Asumsi

- Data yang diunggah mengikuti template yang telah ditentukan.
- Pengguna memiliki hak akses sesuai perannya.
- Setiap file mewakili satu periode penjualan.
- Database selalu tersedia selama proses import.
- KPI dihitung berdasarkan data yang telah lolos validasi.

---

## Batasan

- Sistem tidak menerima format file selain CSV dan XLSX.
- Sistem tidak melakukan mapping otomatis terhadap struktur file yang berbeda.
- Insight menggunakan rule-based, bukan AI.
- Dashboard hanya menampilkan data yang berhasil diimpor.
- Sistem tidak menggantikan ERP perusahaan.

---

# 15. Success Metrics

Proyek dianggap berhasil apabila memenuhi indikator berikut.

## Dari sisi pengguna

- Pengguna berhasil mengunggah file tanpa kendala.
- Dashboard diperbarui secara otomatis.
- KPI tampil dengan benar.
- Laporan dapat diunduh.

---

## Dari sisi bisnis

- Mengurangi waktu analisis dibanding proses manual menggunakan Excel.
- Mengurangi kesalahan perhitungan KPI.
- Mempermudah monitoring performa dealer.

---

## Dari sisi teknis

- Seluruh modul utama berjalan tanpa error kritis.
- Dashboard mampu menampilkan data historis.
- ETL berjalan sesuai aturan validasi.
- Struktur sistem mudah dikembangkan untuk fitur berikutnya.
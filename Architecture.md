# Dealer Sales Analytics System (DSAS)

# Architecture Document

Version : 1.0

---

# 1. Architecture Overview

Dealer Sales Analytics System (DSAS) adalah aplikasi Business Intelligence berbasis web yang dibangun menggunakan arsitektur berlapis (Layered Architecture).

Tujuan utama sistem adalah mengubah data penjualan mentah (CSV/XLSX) menjadi dashboard analitik secara otomatis melalui proses ETL.

Dashboard bukan merupakan inti sistem.

Pipeline data merupakan inti sistem.

---

# 2. High Level Architecture

```
                    USER

        Admin              Manager
           │                  │
           └────────┬─────────┘
                    │
                    ▼

          React Frontend (UI)

                    │
          REST API Request
                    │
                    ▼

        Laravel Backend (API)

                    │
      ┌─────────────┼─────────────┐
      │             │             │
      ▼             ▼             ▼

 Authentication   ETL Service   Report Service

                    │
                    ▼

             MySQL Database

                    │
                    ▼

              KPI Engine

                    │
                    ▼

      Dashboard & Business Insight
```

---

# 3. Data Pipeline

Pipeline merupakan jantung sistem.

Semua proses analisis harus mengikuti alur berikut.

```
CSV/XLSX

↓

Upload

↓

Validation

↓

Cleaning

↓

Transformation

↓

Import MySQL

↓

KPI Calculation

↓

Dashboard

↓

Business Insight

↓

Export Report
```

Tidak boleh ada proses yang melewati tahapan di atas.

---

# 4. System Modules

## Authentication

Bertanggung jawab terhadap:

- Login
- Logout
- Session
- Role

---

## Upload Module

Bertanggung jawab menerima file.

Supported:

- CSV
- XLSX

---

## ETL Module

Melakukan:

- Validation
- Cleaning
- Transformation
- Import

---

## Database Module

Menyimpan seluruh data yang telah lolos validasi.

---

## KPI Engine

Menghasilkan seluruh KPI bisnis.

---

## Dashboard

Menampilkan hasil KPI.

Tidak melakukan perhitungan.

---

## Business Insight

Menghasilkan insight berbasis rule.

---

## Report Module

Export PDF

Export Excel

---

# 5. Layer Responsibility

Frontend

↓

Menampilkan data

Tidak boleh menghitung KPI.

-------------------

Backend

↓

Menjalankan seluruh business logic.

-------------------

Database

↓

Menyimpan data.

Tidak melakukan business logic.

-------------------

KPI Engine

↓

Menghitung seluruh indikator bisnis.

-------------------

Dashboard

↓

Visualisasi data.

Tidak menghitung data.

---

# 6. Folder Responsibility

Frontend

pages/

components/

services/

hooks/

Backend

Controllers

Services

Repositories

Models

Middleware

Database

Migration

Seeder

Factory

---

# 7. AI Development Guidelines

Bagian ini WAJIB diikuti oleh AI Coding Agent selama proses pengembangan.

## General Rules

- Ikuti PRD sebagai sumber kebutuhan bisnis.
- Ikuti Architecture sebagai sumber desain teknis.
- Ikuti Tasks sebagai urutan implementasi.
- Jangan menambahkan fitur yang tidak ada di PRD.
- Jangan mengubah workflow utama sistem.

---

## Data Rules

Semua data harus berasal dari MySQL.

Tidak boleh menggunakan dummy data pada fitur final.

Semua dashboard harus menggunakan data database.

---

## KPI Rules

KPI hanya dihitung di Backend.

Frontend tidak boleh menghitung:

- Revenue

- Profit

- Margin

- Achievement

- Ranking

Frontend hanya menerima hasil KPI.

---

## Dashboard Rules

Dashboard hanya bertugas:

- Menampilkan KPI

- Menampilkan Chart

- Menampilkan Ranking

- Menampilkan Insight

Dashboard tidak boleh menjalankan query SQL.

Dashboard tidak boleh memiliki business logic.

---

## ETL Rules

Semua proses ETL dilakukan oleh Backend.

Meliputi:

- Validation

- Cleaning

- Transformation

- Import

Frontend hanya mengirim file.

---

## Database Rules

Gunakan MySQL.

Gunakan Primary Key.

Gunakan Foreign Key.

Gunakan Migration.

Gunakan Seeder bila diperlukan.

---

## API Rules

Semua komunikasi menggunakan REST API.

Gunakan JSON.

Response harus konsisten.

Gunakan HTTP Status Code yang benar.

---

## Coding Rules

Gunakan Clean Code.

Gunakan Service Layer.

Controller harus tipis.

Business Logic hanya berada di Service.

Query kompleks dipindahkan ke Repository.

Jangan melakukan duplicate code.

---

## Security Rules

Gunakan Authentication.

Gunakan Authorization.

Password harus di-hash.

Validasi seluruh input.

---

## Future Rules

Arsitektur harus mudah dikembangkan.

Penambahan fitur baru tidak boleh mengubah struktur utama sistem.

---

# 8. Architecture Principles

Seluruh pengembangan harus mengikuti prinsip berikut.

1. Data First

Semua proses dimulai dari data.

---

2. ETL Driven

ETL adalah inti sistem.

---

3. KPI Driven

Dashboard hanya menampilkan KPI.

---

4. Separation of Responsibility

Frontend

Backend

Database

KPI

Dashboard

memiliki tanggung jawab masing-masing.

---

5. Maintainability

Kode harus mudah dipelihara.

---

6. Scalability

Struktur harus siap menerima fitur baru di masa depan.
# Dealer Sales Analytics System (DSAS)

Business Intelligence web application untuk analisis data penjualan dealer melalui automated ETL pipeline.

## Tech Stack

**Backend:**
- Laravel 11.x
- MySQL 8.0
- Laravel Sanctum (Authentication)

**Frontend:**
- React 18.x
- Vite
- Recharts (Data Visualization)
- Axios (HTTP Client)
- React Router DOM

## Project Structure

```
Business_Analyst/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Middleware/
│   │   ├── Services/       # Business Logic
│   │   ├── Repositories/   # Database Queries
│   │   └── Models/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
│       └── api.php
├── frontend/               # React SPA
│   ├── src/
│   │   ├── pages/
│   │   ├── components/
│   │   ├── services/
│   │   └── hooks/
│   └── public/
└── docs/                   # Documentation
    ├── PRD.md
    ├── Architecture.md
    └── Task.md
```

## Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0
- Git

### Backend Setup

1. Navigate to backend directory:
```bash
cd backend
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Configure database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dealer_sales_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. Run seeders:
```bash
php artisan db:seed
```

8. Start development server:
```bash
php artisan serve
```

Backend will run at: `http://localhost:8000`

### Frontend Setup

1. Navigate to frontend directory:
```bash
cd frontend
```

2. Install dependencies:
```bash
npm install
```

3. Create `.env` file:
```
VITE_API_BASE_URL=http://localhost:8000/api
```

4. Start development server:
```bash
npm run dev
```

Frontend will run at: `http://localhost:5173`

## Database Setup

Create MySQL database:

```sql
CREATE DATABASE dealer_sales_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Default Users (After Seeding)

**Admin:**
- Email: admin@dsas.com
- Password: admin123
- Role: Admin (Upload + ETL + View + Export)

**Manager:**
- Email: manager@dsas.com
- Password: manager123
- Role: Manager (View + Export only)

## Features

### Completed
- [ ] Authentication (Laravel Sanctum)
- [ ] Upload Module (CSV/XLSX)
- [ ] ETL Pipeline (Validation, Cleaning, Transformation, Import)
- [ ] KPI Engine (Auto-calculation)
- [ ] Dashboard (KPI Cards, Charts, Rankings)
- [ ] Business Insight (Rule-based)
- [ ] Export Report (PDF, Excel)

### In Progress
- [x] Project Setup
- [ ] Database Schema

## Development Workflow

Project menggunakan **Vertical Slice Development**:
- Setiap slice = 1 fitur complete (DB + Backend + Frontend + Test)
- Slice dikerjakan secara sequential
- Setiap slice harus working end-to-end sebelum lanjut slice berikutnya

## API Documentation

API base URL: `http://localhost:8000/api`

### Authentication
```
POST /api/login
POST /api/logout
GET  /api/user
```

### Upload & ETL
```
POST /api/upload/file
POST /api/etl/validate
POST /api/etl/clean
POST /api/etl/import
GET  /api/etl/summary/:id
```

### Dashboard
```
GET /api/dashboard/kpi-cards
GET /api/dashboard/charts/sales-trend
GET /api/dashboard/ranking/dealers
GET /api/dashboard/ranking/products
```

### Reports
```
POST /api/reports/export/pdf
POST /api/reports/export/excel
```

## CSV Template Structure

Required columns (14):
```
transaction_date, invoice_no, dealer_code, dealer_name, branch, 
product_code, product_name, quantity, unit_price, revenue, cost, 
target, sales_person, sales_month
```

Download template: `GET /api/upload/template`

## Architecture Principles

1. **Pipeline is the core** (not dashboard)
2. **Strict separation of concerns:**
   - Backend: All KPI calculations, validations, business logic
   - Frontend: Display only (no calculations)
   - Controller: Thin (routing only)
   - Service: Business logic
   - Repository: Complex SQL queries
3. **Data integrity:**
   - Invoice must be globally unique
   - Data never deleted (historical analysis)
   - ETL validation strict (reject invalid data)

## Testing

Backend tests:
```bash
php artisan test
```

Frontend tests:
```bash
npm run test
```

## Deployment

Coming soon...

## License

MIT License - Portfolio Project

## Author

Caesario Gumilang
- Role: Business Analyst / Data Analyst
- Project: Portfolio Project
- Date: 2026

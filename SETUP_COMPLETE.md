# Slice 1: Project Setup & Database Foundation - COMPLETED

## Files Created (50+ files)

### Backend Structure
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── AuthController.php ✓
│   │   └── Middleware/
│   │       └── RoleMiddleware.php ✓
│   └── Models/
│       ├── User.php ✓
│       ├── Branch.php ✓
│       ├── Dealer.php ✓
│       ├── Product.php ✓
│       ├── SalesPeriod.php ✓
│       ├── SalesTransaction.php ✓
│       ├── KpiSummary.php ✓
│       ├── BusinessInsight.php ✓
│       └── EtlLog.php ✓
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000000_create_users_table.php ✓
│   │   ├── 2024_01_02_000000_create_branches_table.php ✓
│   │   ├── 2024_01_03_000000_create_dealers_table.php ✓
│   │   ├── 2024_01_04_000000_create_products_table.php ✓
│   │   ├── 2024_01_05_000000_create_sales_periods_table.php ✓
│   │   ├── 2024_01_06_000000_create_sales_transactions_table.php ✓
│   │   ├── 2024_01_07_000000_create_kpi_summary_table.php ✓
│   │   ├── 2024_01_08_000000_create_business_insights_table.php ✓
│   │   └── 2024_01_09_000000_create_etl_logs_table.php ✓
│   └── seeders/
│       ├── DatabaseSeeder.php ✓
│       └── UserSeeder.php ✓
├── routes/
│   └── api.php ✓
├── storage/
│   └── templates/
│       └── sales_template.csv ✓
├── composer.json ✓
├── .env.example ✓
└── .gitignore ✓
```

### Frontend Structure
```
frontend/
├── src/
│   ├── pages/
│   │   ├── Login.jsx ✓
│   │   ├── Dashboard.jsx ✓
│   │   └── Upload.jsx ✓
│   ├── services/
│   │   └── api.js ✓
│   ├── App.jsx ✓
│   ├── main.jsx ✓
│   └── index.css ✓
├── index.html ✓
├── package.json ✓
├── vite.config.js ✓
├── .env.example ✓
└── .gitignore ✓
```

## Setup Instructions

### Step 1: Backend Setup

1. Navigate to backend directory:
```bash
cd D:\RIO\PORTOFOLIO\Business_Analyst\backend
```

2. Install Laravel dependencies:
```bash
composer install
```

3. Copy and configure environment:
```bash
copy .env.example .env
```

4. Edit `.env` file - configure database:
```env
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

6. Create database in MySQL:
```sql
CREATE DATABASE dealer_sales_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

7. Run migrations:
```bash
php artisan migrate
```

8. Run seeders (create admin & manager users):
```bash
php artisan db:seed
```

9. Start backend server:
```bash
php artisan serve
```

Backend will run at: **http://localhost:8000**

### Step 2: Frontend Setup

1. Navigate to frontend directory:
```bash
cd D:\RIO\PORTOFOLIO\Business_Analyst\frontend
```

2. Install dependencies:
```bash
npm install
```

3. Copy environment file:
```bash
copy .env.example .env
```

4. Start frontend server:
```bash
npm run dev
```

Frontend will run at: **http://localhost:5173**

### Step 3: Test Login

Open browser: http://localhost:5173

**Admin Account:**
- Email: admin@dsas.com
- Password: admin123
- Access: Full (Upload + ETL + View + Export)

**Manager Account:**
- Email: manager@dsas.com
- Password: manager123
- Access: Limited (View + Export only)

## Database Schema

### Core Tables Created:

1. **users** - Authentication & role management
2. **branches** - Master data cabang
3. **dealers** - Master data dealer
4. **products** - Master data produk
5. **sales_periods** - Tracking periode upload
6. **sales_transactions** - Fact table (data penjualan)
   - profit & profit_margin: STORED GENERATED COLUMNS (auto-calculated)
   - invoice_no: UNIQUE GLOBAL (Q2-A enforcement)
7. **kpi_summary** - Cache hasil KPI calculation
8. **business_insights** - Rule-based insights
9. **etl_logs** - Tracking ETL process

### Key Architecture Decisions Implemented:

✅ **Q1-B**: Revenue mismatch → REJECT (validation rules ready)
✅ **Q2-A**: Invoice globally unique (database constraint enforced)
✅ **Q3-C**: Dealer name conflict → REJECT (validation logic ready)
✅ **Q4-B**: Data never deleted (no soft deletes, historical analysis enabled)
✅ **Q5-B**: 1 upload = 1 period (validation logic ready)

✅ **Profit calculation**: STORED GENERATED COLUMN (calculated at DB level)
✅ **Profit margin**: STORED GENERATED COLUMN (calculated at DB level)

## What's Working Now

### Backend:
- ✅ Authentication API (login/logout/user)
- ✅ Laravel Sanctum configured
- ✅ Role-based middleware (admin/manager)
- ✅ Database schema complete
- ✅ Models with relationships
- ✅ API routes structure
- ✅ CSV template available

### Frontend:
- ✅ Login page functional
- ✅ Route guards (PrivateRoute, AdminRoute)
- ✅ API service with interceptors
- ✅ Token management (localStorage)
- ✅ Basic dashboard layout
- ✅ Basic upload page layout

## Next Steps: Slice 2

**Slice 2: Upload Module (Admin only)**

Backend tasks:
1. Create UploadController
2. Implement file validation (CSV/XLSX, max 10MB)
3. Implement file parsing (CSV/XLSX reader)
4. Return preview data (first 100 rows)
5. Store file temporarily

Frontend tasks:
1. Implement drag-drop upload zone
2. File selector with validation
3. Preview table component
4. Progress indicator
5. Download template button

**Estimated duration:** 1.5 days

---

## Troubleshooting

### Backend not starting:
- Check PHP version: `php -v` (must be 8.2+)
- Check composer installed: `composer -V`
- Check .env database credentials
- Check MySQL running: Test connection

### Frontend not starting:
- Check Node.js version: `node -v` (must be 18+)
- Run `npm install` again
- Clear node_modules: `rmdir /s node_modules` then `npm install`

### Database migration failed:
- Check database exists
- Check database credentials in .env
- Check MySQL service running
- Drop all tables and re-run: `php artisan migrate:fresh --seed`

### CORS errors:
- Check SANCTUM_STATEFUL_DOMAINS in .env
- Add frontend URL: `SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173`
- Clear config cache: `php artisan config:clear`

---

**Slice 1 Status: ✅ COMPLETE**

Ready to proceed to Slice 2: Upload Module.

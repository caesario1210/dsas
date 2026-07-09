# Data Contract v1.0 - FINAL

## 1. CSV Input Structure

### Column Definition (14 columns)

| No | Column Name | Type | Required | Max Length | Example |
|----|-------------|------|----------|------------|---------|
| 1 | transaction_date | Date (YYYY-MM-DD) | Yes | - | 2026-01-15 |
| 2 | invoice_no | String | Yes | 50 | INV-2026-001 |
| 3 | dealer_code | String | Yes | 20 | DLR-JKT-001 |
| 4 | dealer_name | String | Yes | 100 | Dealer Jakarta Pusat |
| 5 | branch | String | Yes | 50 | Jakarta |
| 6 | product_code | String | Yes | 20 | PRD-BEAT |
| 7 | product_name | String | Yes | 100 | Honda Beat |
| 8 | quantity | Integer | Yes | - | 5 |
| 9 | unit_price | Decimal(15,2) | Yes | - | 15000000 |
| 10 | revenue | Decimal(15,2) | Yes | - | 75000000 |
| 11 | cost | Decimal(15,2) | Yes | - | 65000000 |
| 12 | target | Decimal(15,2) | Yes | - | 100000000 |
| 13 | sales_person | String | No | 100 | Budi Santoso |
| 14 | sales_month | String (YYYY-MM) | Yes | 7 | 2026-01 |

**Note:** 
- `profit` is NOT in CSV (calculated during ETL: profit = revenue - cost)
- `profit_margin` is NOT in CSV (calculated during ETL: profit_margin = profit/revenue × 100)

---

## 2. Validation Rules (ENFORCED)

### Critical Business Rules (Based on Q&A)

| Rule | Implementation | Action | Reason |
|------|----------------|--------|--------|
| **Q1-B: Revenue Consistency** | IF (quantity × unit_price) ≠ revenue | **REJECT row** | Data integrity |
| **Q2-A: Invoice Uniqueness** | Check invoice_no against ALL periods | **REJECT** | Global unique constraint |
| **Q3-C: Master Data Conflict** | dealer_code sama, dealer_name beda | **REJECT** | Prevent silent corruption |
| **Q4-B: Historical Data** | Data lama NEVER deleted | **PRESERVE** | Historical analysis |
| **Q5-B: Single Period per Upload** | Multiple sales_month in 1 CSV | **REJECT entire file** | Consistency |

### Field-Level Validation

```php
// transaction_date
- Format: YYYY-MM-DD
- Must be valid date
- Must match sales_month (same month)

// invoice_no
- Not empty
- Max 50 characters
- Must be globally unique (across all periods)
- Alphanumeric + dash/slash allowed

// dealer_code
- Not empty
- Max 20 characters
- UPPERCASE standardization
- Alphanumeric + dash allowed

// dealer_name
- Not empty
- Max 100 characters
- Title Case standardization

// branch
- Not empty
- Max 50 characters
- Title Case standardization

// product_code
- Not empty
- Max 20 characters
- UPPERCASE standardization

// product_name
- Not empty
- Max 100 characters
- Title Case standardization

// quantity
- Integer
- Min: 1 (cannot be 0 or negative)

// unit_price
- Decimal(15,2)
- Min: 0

// revenue
- Decimal(15,2)
- Min: 0
- Must equal: quantity × unit_price (Q1-B)

// cost
- Decimal(15,2)
- Min: 0
- Can be > revenue (valid business scenario: loss)

// target
- Decimal(15,2)
- Min: 0

// sales_person
- Optional (nullable)
- Max 100 characters
- Default: "N/A" if empty

// sales_month
- Format: YYYY-MM (e.g., "2026-01")
- Must match transaction_date month
- Must be consistent across entire CSV file (Q5-B)
```

---

## 3. ETL Process Flow

```
┌─────────────────────────────────────────────────────────┐
│ PHASE 1: UPLOAD                                         │
├─────────────────────────────────────────────────────────┤
│ 1. Receive file (CSV/XLSX)                              │
│ 2. Validate file format & size (<10MB)                  │
│ 3. Parse file → raw data array                          │
│ 4. Return preview (first 100 rows)                      │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ PHASE 2: VALIDATION                                     │
├─────────────────────────────────────────────────────────┤
│ 1. Check 14 columns exist                               │
│ 2. Check column names match template                    │
│ 3. Validate data types per column                       │
│ 4. Revenue consistency (Q1-B)                           │
│ 5. Invoice uniqueness (Q2-A) - check DB                 │
│ 6. Master data consistency (Q3-C) - check DB            │
│ 7. Single period check (Q5-B)                           │
│ 8. Date-period match                                    │
│ → Result: valid_rows[], invalid_rows[]                  │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ PHASE 3: CLEANING                                       │
├─────────────────────────────────────────────────────────┤
│ 1. Trim all strings                                     │
│ 2. UPPERCASE: dealer_code, product_code                 │
│ 3. Title Case: dealer_name, branch, product_name        │
│ 4. sales_person: null → "N/A"                           │
│ 5. Round decimals to 2 places                           │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ PHASE 4: TRANSFORMATION                                 │
├─────────────────────────────────────────────────────────┤
│ 1. Extract & upsert master data:                        │
│    - branches (unique by branch_name)                   │
│    - dealers (unique by dealer_code)                    │
│    - products (unique by product_code)                  │
│ 2. Create/get sales_period record                       │
│ 3. Map foreign keys (dealer_id, product_id, etc)        │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ PHASE 5: IMPORT                                         │
├─────────────────────────────────────────────────────────┤
│ DB::beginTransaction()                                  │
│   1. Insert to sales_transactions                       │
│      (profit & profit_margin auto-calculated by DB)     │
│   2. Update sales_period metadata                       │
│   3. Create etl_log record                              │
│ DB::commit() or DB::rollback()                          │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ PHASE 6: KPI AUTO-CALCULATION                           │
├─────────────────────────────────────────────────────────┤
│ Trigger: After successful import                        │
│ Action: KpiEngineService::calculate($period_id)         │
│ Store: kpi_summary table                                │
└─────────────────────────────────────────────────────────┘
```

---

## 4. Database Schema

### sales_transactions (Fact Table)

```sql
CREATE TABLE sales_transactions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    transaction_date DATE NOT NULL,
    invoice_no VARCHAR(50) NOT NULL UNIQUE,  -- Q2-A: Global unique
    dealer_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    revenue DECIMAL(15,2) NOT NULL,
    cost DECIMAL(15,2) NOT NULL,
    profit DECIMAL(15,2) GENERATED ALWAYS AS (revenue - cost) STORED,
    profit_margin DECIMAL(5,2) GENERATED ALWAYS AS ((revenue - cost) / revenue * 100) STORED,
    target DECIMAL(15,2) NOT NULL,
    sales_person VARCHAR(100) DEFAULT 'N/A',
    sales_month VARCHAR(7) NOT NULL,
    period_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (dealer_id) REFERENCES dealers(id) ON DELETE RESTRICT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (period_id) REFERENCES sales_periods(id) ON DELETE RESTRICT,
    
    INDEX idx_invoice (invoice_no),
    INDEX idx_date (transaction_date),
    INDEX idx_dealer (dealer_id),
    INDEX idx_product (product_id),
    INDEX idx_month (sales_month),
    INDEX idx_period (period_id),
    INDEX idx_month_dealer (sales_month, dealer_id),
    INDEX idx_month_product (sales_month, product_id)
);
```

**Key Features:**
- `profit` & `profit_margin`: STORED GENERATED COLUMNS (MySQL computes automatically)
- `invoice_no`: UNIQUE constraint enforces Q2-A
- Data NEVER deleted (Q4-B)
- Comprehensive indexing for fast KPI queries

---

## 5. API Response Format

### ETL Summary Response

```json
{
  "status": "success",
  "message": "ETL process completed",
  "data": {
    "period": {
      "id": 5,
      "sales_month": "2026-01",
      "upload_date": "2026-07-07",
      "status": "completed"
    },
    "summary": {
      "rows_uploaded": 15320,
      "rows_imported": 15250,
      "rows_duplicate": 42,
      "rows_missing_value": 18,
      "rows_failed": 10
    },
    "validation_errors": [
      {
        "row": 152,
        "invoice_no": "INV-2026-152",
        "type": "revenue_mismatch",
        "message": "Revenue mismatch: expected 75000000, got 70000000",
        "action": "rejected"
      },
      {
        "row": 1024,
        "invoice_no": "INV-2026-001",
        "type": "duplicate_invoice",
        "message": "Invoice already exists in period 2026-01",
        "action": "rejected"
      }
    ]
  }
}
```

### Error Response

```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "file": ["Multiple periods detected in single upload (2026-01, 2026-02)"]
  }
}
```

---

## 6. ETL Business Logic (Pseudocode)

### Revenue Consistency Check (Q1-B)

```php
function validateRevenueConsistency($row) {
    $expected_revenue = $row['quantity'] * $row['unit_price'];
    
    if (abs($expected_revenue - $row['revenue']) > 0.01) {
        throw new ValidationException(
            "Revenue mismatch at row {$row['line']}: " .
            "Expected {$expected_revenue}, got {$row['revenue']}"
        );
    }
}
```

### Invoice Uniqueness Check (Q2-A)

```php
function validateInvoiceUniqueness($invoice_no) {
    $exists = DB::table('sales_transactions')
        ->where('invoice_no', $invoice_no)
        ->exists();
    
    if ($exists) {
        throw new ValidationException(
            "Duplicate invoice: {$invoice_no} already exists"
        );
    }
}
```

### Master Data Consistency Check (Q3-C)

```php
function validateDealerConsistency($dealer_code, $dealer_name) {
    $existing = DB::table('dealers')
        ->where('dealer_code', $dealer_code)
        ->first();
    
    if ($existing && $existing->dealer_name !== $dealer_name) {
        throw new ValidationException(
            "Dealer name conflict: {$dealer_code} " .
            "exists as '{$existing->dealer_name}', " .
            "but CSV has '{$dealer_name}'"
        );
    }
}
```

### Single Period Check (Q5-B)

```php
function validateSinglePeriod($csvData) {
    $periods = array_unique(array_column($csvData, 'sales_month'));
    
    if (count($periods) > 1) {
        throw new ValidationException(
            "Multiple periods detected in single upload: " .
            implode(', ', $periods)
        );
    }
}
```

---

## 7. Data Cleaning Rules

```php
$cleaned = [
    'dealer_code' => strtoupper(trim($row['dealer_code'])),
    'dealer_name' => titleCase(trim($row['dealer_name'])),
    'branch' => titleCase(trim($row['branch'])),
    'product_code' => strtoupper(trim($row['product_code'])),
    'product_name' => titleCase(trim($row['product_name'])),
    'sales_person' => $row['sales_person'] ? titleCase(trim($row['sales_person'])) : 'N/A',
    'revenue' => round($row['revenue'], 2),
    'cost' => round($row['cost'], 2),
];

function titleCase($string) {
    return ucwords(strtolower($string));
}
```

**Examples:**
- `"DLR-jkt-001"` → `"DLR-JKT-001"`
- `"dealer jakarta pusat  "` → `"Dealer Jakarta Pusat"`
- `"JAKARTA"` → `"Jakarta"`
- `null` (sales_person) → `"N/A"`

---

**Data Contract Status: ✅ APPROVED & IMPLEMENTED**

All validation rules, ETL logic, and database constraints are now embedded in the system architecture.

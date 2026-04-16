# TipTap Database Setup - Complete Checklist

## ✅ COMPLETED ITEMS

### [✅] Configure Environment
- **File**: `.env`
- **Status**: CONFIGURED
- Database: `tiptap`
- Connection: `mysql`
- Host: `127.0.0.1:3306`
- Username: `root`
- Password: (empty)

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tiptap
DB_USERNAME=root
DB_PASSWORD=
```

---

### [✅] Create Migration - Users
- **File**: `database/migrations/0001_01_01_000000_create_users_table.php`
- **Status**: CREATED & CONFIGURED

**Columns Added:**
- ✅ `role` (string, default: 'user') - superadmin, admin, user
- ✅ `divisi` (string, nullable) - department
- ✅ `status_pekerjaan` (string, nullable) - aktif, cuti, resign
- ✅ `tgl_habis_kontrak` (date, nullable) - contract end date
- ✅ `is_approved` (boolean, default: false) - approval status

**Table Structure:**
```
users:
  ├─ id (primary)
  ├─ name
  ├─ email (unique)
  ├─ email_verified_at (nullable)
  ├─ password
  ├─ remember_token
  ├─ role ← NEW
  ├─ divisi ← NEW
  ├─ status_pekerjaan ← NEW
  ├─ tgl_habis_kontrak ← NEW
  ├─ is_approved ← NEW
  ├─ created_at
  └─ updated_at
```

---

### [✅] Create Migration - Attendances
- **File**: `database/migrations/0001_01_01_000004_create_attendances_table.php`
- **Status**: CREATED

**Columns:**
- ✅ `user_id` (foreign key → users, cascading delete)
- ✅ `tanggal` (date)
- ✅ `waktu_masuk` (time)
- ✅ `waktu_keluar` (time, nullable)
- ✅ `status` (string, default: 'hadir') - hadir, izin, sakit, libur

**Table Structure:**
```
attendances:
  ├─ id (primary)
  ├─ user_id (foreign key) ← points to users.id
  ├─ tanggal
  ├─ waktu_masuk
  ├─ waktu_keluar (nullable)
  ├─ status
  ├─ created_at
  └─ updated_at
```

---

### [✅] Create Migration - Leaves
- **File**: `database/migrations/0001_01_01_000005_create_leaves_table.php`
- **Status**: CREATED

**Columns:**
- ✅ `user_id` (foreign key → users, cascading delete)
- ✅ `jenis_izin` (string) - cuti, sakit, urgent, lainnya
- ✅ `tanggal_mulai` (date)
- ✅ `tanggal_selesai` (date)
- ✅ `alasan` (text)
- ✅ `bukti_file` (string, nullable)
- ✅ `status_approval` (string, default: 'pending') - pending, approved, rejected

**Table Structure:**
```
leaves:
  ├─ id (primary)
  ├─ user_id (foreign key) ← points to users.id
  ├─ jenis_izin
  ├─ tanggal_mulai
  ├─ tanggal_selesai
  ├─ alasan
  ├─ bukti_file (nullable)
  ├─ status_approval (default: pending)
  ├─ created_at
  └─ updated_at
```

---

### [✅] Create Migration - Settings
- **File**: `database/migrations/0001_01_01_000006_create_settings_table.php`
- **Status**: CREATED

**Columns:**
- ✅ `jam_masuk_kantor` (time, default: '08:00')
- ✅ `jam_mulai_pulang` (time, default: '17:00')

**Table Structure:**
```
settings:
  ├─ id (primary)
  ├─ jam_masuk_kantor (default: 08:00)
  ├─ jam_mulai_pulang (default: 17:00)
  ├─ created_at
  └─ updated_at
```

---

### [✅] Create Models
- **File**: `app/Models/Attendance.php` - ✅ CREATED
- **File**: `app/Models/Leave.php` - ✅ CREATED
- **File**: `app/Models/Setting.php` - ✅ CREATED
- **File**: `app/Models/User.php` - ✅ UPDATED

---

### [✅] Model Relationships
- **User Model**: 
  - ✅ `hasMany(Attendance)`
  - ✅ `hasMany(Leave)`
  
- **Attendance Model**:
  - ✅ `belongsTo(User)`
  
- **Leave Model**:
  - ✅ `belongsTo(User)`

---

### [✅] Database Seeder
- **File**: `database/seeders/DatabaseSeeder.php`
- **Status**: CREATED

**Default Data Created:**
- ✅ Superadmin Account:
  - Email: `superadmin@tiptap.com`
  - Password: `password123`
  - Role: `superadmin`
  - Divisi: `Management`
  - Status: `aktif`
  - Approved: `true`

- ✅ Default Settings:
  - Jam Masuk: `08:00`
  - Jam Pulang: `17:00`

- ✅ 5 Test Users (via Factory)

---

### [✅] User Factory
- **File**: `database/factories/UserFactory.php`
- **Status**: UPDATED with new fields

---

## 🚀 HOW TO RUN MIGRATIONS

### Option 1: Fresh Migration with Seeding (RECOMMENDED)
```powershell
cd c:\Users\Asus\Documents\Project\BOOTCAMP\TipTap

# Drop all tables and rerun all migrations with seeding
php artisan migrate:fresh --seed
```

### Option 2: If Database Already Exists
```powershell
# First, drop all tables
php artisan migrate:reset

# Then run migrations
php artisan migrate

# Then seed
php artisan db:seed
```

### Option 3: Step by Step
```powershell
# Run only new migrations
php artisan migrate

# Run seeder separately
php artisan db:seed --class=DatabaseSeeder
```

---

## 📊 DATABASE SCHEMA OVERVIEW

```
┌─────────────────────────┐
│        USERS            │
├─────────────────────────┤
│ id (PK)                 │
│ name                    │
│ email (UNIQUE)          │
│ password                │
│ role                    │
│ divisi                  │
│ status_pekerjaan        │
│ tgl_habis_kontrak       │
│ is_approved             │
│ timestamps              │
└─────────────────────────┘
         │
         │ (1:many)
         ├─────────────────────────────────┐
         │                                 │
         ▼                                 ▼
┌─────────────────────────┐  ┌──────────────────────────┐
│    ATTENDANCES          │  │       LEAVES             │
├─────────────────────────┤  ├──────────────────────────┤
│ id (PK)                 │  │ id (PK)                  │
│ user_id (FK)            │  │ user_id (FK)             │
│ tanggal                 │  │ jenis_izin               │
│ waktu_masuk             │  │ tanggal_mulai            │
│ waktu_keluar (NULL)     │  │ tanggal_selesai          │
│ status                  │  │ alasan                   │
│ timestamps              │  │ bukti_file (NULL)        │
└─────────────────────────┘  │ status_approval          │
                             │ timestamps               │
                             └──────────────────────────┘

                    ┌──────────────────┐
                    │    SETTINGS      │
                    ├──────────────────┤
                    │ id (PK)          │
                    │ jam_masuk_kantor │
                    │ jam_mulai_pulang │
                    │ timestamps       │
                    └──────────────────┘
```

---

## ✅ LOGIN CREDENTIALS (After Migration)

```
Email: superadmin@tiptap.com
Password: password123
Role: Superadmin
```

---

## 📝 MIGRATION FILES CREATED

1. ✅ `0001_01_01_000000_create_users_table.php` (UPDATED)
2. ✅ `0001_01_01_000001_create_cache_table.php` (EXISTS)
3. ✅ `0001_01_01_000002_create_jobs_table.php` (EXISTS)
4. ✅ `0001_01_01_000003_create_posts_table.php` (EXISTS)
5. ✅ `0001_01_01_000004_create_attendances_table.php` (NEW)
6. ✅ `0001_01_01_000005_create_leaves_table.php` (NEW)
7. ✅ `0001_01_01_000006_create_settings_table.php` (NEW)

---

## 🔍 TROUBLESHOOTING

**If you get "Unknown column" error:**
```powershell
# Clean and restart
php artisan migrate:reset
php artisan migrate:fresh --seed
```

**If database doesn't exist:**
```
1. Open MySQL/PhpMyAdmin
2. Create new database: tiptap
3. Run: php artisan migrate:fresh --seed
```

**To verify migrations ran:**
```powershell
php artisan migrate:status
```

---

## ✅ ALL CHECKLIST ITEMS COMPLETED

- [x] Configure Environment
- [x] Create Migration - Users
- [x] Create Migration - Attendances
- [x] Create Migration - Leaves
- [x] Create Migration - Settings
- [x] Create Models (Attendance, Leave, Setting)
- [x] Model Relationships (hasMany, belongsTo)
- [x] Database Seeder (Superadmin + Settings)
- [x] Update User Factory

**STATUS**: READY TO MIGRATE ✅

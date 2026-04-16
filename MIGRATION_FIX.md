# 🔧 QUICK FIX GUIDE - Database Migration Error

## ❌ ERROR YOU'RE GETTING:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'role' in 'field list'
```

**WHY:** The migrations haven't been executed properly on your database.

---

## ✅ FIX - STEP BY STEP

### Step 1: Reset Everything
Run this command in Terminal/PowerShell:

```powershell
cd c:\Users\Asus\Documents\Project\BOOTCAMP\TipTap

php artisan migrate:reset
```

**What it does:** Drops all tables (including migrations history)

---

### Step 2: Fresh Database Setup
```powershell
php artisan migrate:fresh --seed
```

**What it does:** 
- Runs all migrations in order
- Creates all tables with the correct schema
- Seeds the database with default data

---

### Step 3: Verify Success
```powershell
php artisan migrate:status
```

**Expected Output:**
```
Batch │ Migration                                │ Batch
────────────────────────────────────────────────
   1  │ 0001_01_01_000000_create_users_table     │ 1
   1  │ 0001_01_01_000001_create_cache_table     │ 1
   1  │ 0001_01_01_000002_create_jobs_table      │ 1
   1  │ 0001_01_01_000003_create_posts_table     │ 1
   1  │ 0001_01_01_000004_create_attendances_table │ 1
   1  │ 0001_01_01_000005_create_leaves_table    │ 1
   1  │ 0001_01_01_000006_create_settings_table  │ 1
```

---

## 📊 Database Tables Created

After running migrations, you should have:

1. ✅ `users` - with role, divisi, status_pekerjaan, tgl_habis_kontrak, is_approved
2. ✅ `attendances` - with user_id FK, tanggal, waktu_masuk, waktu_keluar
3. ✅ `leaves` - with user_id FK, jenis_izin, tanggal dates, bukti_file, status
4. ✅ `settings` - with jam_masuk_kantor and jam_mulai_pulang
5. ✅ `posts` - from previous setup
6. ✅ `cache` - system table
7. ✅ `jobs` - system table
8. ✅ `password_reset_tokens` - system table
9. ✅ `sessions` - system table

---

## 🔑 Test Login

After migration completes, use:

```
Email: superadmin@tiptap.com
Password: password123
```

---

## 🚑 Emergency Reset (If still having issues)

If there are still issues, do a complete reset:

```powershell
# Drop everything
php artisan migrate:reset

# Clear config cache
php artisan config:clear

# Clear app cache
php artisan cache:clear

# Fresh migration with seeds
php artisan migrate:fresh --seed
```

---

## ✨ All Done!

Your database should now have:
- ✅ Users table with new columns
- ✅ Attendances table (empty, ready to track attendance)
- ✅ Leaves table (empty, ready to track leave requests)  
- ✅ Settings table (jam_masuk_kantor, jam_mulai_pulang)
- ✅ Posts table (from your earlier setup)
- ✅ 1 Superadmin account pre-created
- ✅ 5 Test users
- ✅ Default office hours settings

**Next steps:**
1. Start Laravel server: `php artisan serve`
2. Login with superadmin credentials
3. Navigate to `/posts` to manage posts
4. Set up attendance, leave management pages

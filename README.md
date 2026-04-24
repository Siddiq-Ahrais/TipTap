<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Vite-5-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/TailwindCSS-3-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white" alt="Alpine.js">
</p>

# 🏢 TipTap — Employee Workspace

**TipTap** is a full-featured employee attendance and workforce management web application built for **PT Sukamaju**. It provides a modern, responsive admin dashboard and employee portal to manage daily clock-in/clock-out, leave requests, early checkout approvals, user registration approvals, and attendance reporting — all in one platform.

---

## 📑 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Architecture](#-architecture)
- [User Roles](#-user-roles)
- [Modules](#-modules)
  - [Authentication](#1-authentication)
  - [Employee Dashboard](#2-employee-dashboard)
  - [Attendance Management](#3-attendance-management)
  - [Leave Management](#4-leave-management)
  - [Approval Center (Admin)](#5-approval-center-admin)
  - [Attendance Export](#6-attendance-export)
  - [Posts / Announcements](#7-posts--announcements)
  - [Profile Management](#8-profile-management)
  - [System Configuration](#9-system-configuration)
  - [Alpha / Dev Tools](#10-alpha--dev-tools)
- [API Endpoints](#-api-endpoints)
- [Database Schema](#-database-schema)
- [Installation](#-installation)
- [Usage](#-usage)
- [License](#-license)

---

## ✨ Features

| Category | Features |
|---|---|
| **Attendance** | Clock-in/clock-out with late detection, early checkout requests, attendance history with pagination |
| **Leave Management** | Leave request form (Sick, Annual, Emergency), file upload for medical certificates, leave quota system (auto-deduct), date validation (no past dates) |
| **Admin Approval** | Approve/reject new user registrations, leave requests, and early checkouts from a unified dashboard |
| **Dashboard** | Real-time attendance stats, leave balance tracker, punctuality score, today's attendance ranking with medal icons (🥇🥈🥉) |
| **Export** | Download attendance data as Excel (.xls) files — supports Today, Weekly, and Monthly reports |
| **Security** | Role-based access control, contract expiration checks, approved-only access, no "Remember Me" on login |
| **Responsive UI** | Fully responsive design with a navy-themed sidebar, glassmorphism cards, and smooth transitions |

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 12, PHP 8.2 |
| **Frontend** | Blade Templates, TailwindCSS 3, Alpine.js 3 |
| **Build Tool** | Vite 5 |
| **Database** | SQLite (development) / MySQL (production) |
| **Auth** | Laravel Breeze |
| **Testing** | Pest PHP |

---

## 🏗 Architecture

```
TipTap/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AttendanceController.php      # Clock-in/out & history
│   │   │   ├── AttendanceExportController.php # Excel export (today/weekly/monthly)
│   │   │   ├── ApprovalController.php        # Admin approval center
│   │   │   ├── LeaveController.php           # Leave requests (web + API)
│   │   │   ├── PostController.php            # Announcements CRUD
│   │   │   ├── ProfileController.php         # User profile editing
│   │   │   ├── AlphaController.php           # Dev tools & system config
│   │   │   └── PageController.php            # Public pages (home, about)
│   │   └── Middleware/
│   │       ├── ApprovedUserMiddleware.php     # Block unapproved users
│   │       ├── EnsureContractIsActiveForAttendance.php
│   │       ├── RoleMiddleware.php            # Role-based access
│   │       └── RedirectAlphaUser.php
│   └── Models/
│       ├── User.php          # Employees with leave quota helpers
│       ├── Attendance.php    # Daily attendance records
│       ├── Leave.php         # Leave/sick requests
│       ├── Post.php          # Announcements
│       └── Setting.php       # System-wide settings
├── resources/views/
│   ├── dashboard.blade.php   # Main employee dashboard
│   ├── approval/             # Admin approval pages
│   ├── attendance/           # Attendance history
│   ├── leave/                # Leave request forms
│   ├── post/                 # Post/announcement pages
│   ├── auth/                 # Login pages (admin + employee)
│   └── layouts/              # App layout & navigation
└── routes/
    ├── web.php               # Web routes
    └── api.php               # Mobile/API endpoints
```

---

## 👥 User Roles

| Role | Access Level |
|---|---|
| **Super Admin** | Full access — manage settings, approve users, view all data, dev tools |
| **Admin** | Approval center — manage registrations, leaves, early checkouts, export reports |
| **User (Employee)** | Personal dashboard — clock-in/out, submit leave requests, view own history |

### Access Flow

```
New Registration → Pending → Admin Approves → Employee can Login & Clock-in
```

---

## 📦 Modules

### 1. Authentication

- **Employee Login** (`/login`) — Standard email/password login
- **Admin Login** (`/admin/login`) — Dedicated admin portal
- **Registration** — New employees register and wait for admin approval
- **Security** — No "Remember Me" checkbox, cookies cleared on logout

### 2. Employee Dashboard

The dashboard (`/dashboard`) displays:

- **Employee Profile Card** — Name, role, division, employee ID, email
- **Today's Attendance Status** — Real-time clock-in/out status with pending indicators
- **Metric Cards:**
  - 📊 **This Month Attendance** — Days attended vs target
  - 📅 **Leave Balance** — Remaining quota with used count (auto-calculated)
  - ⏰ **Punctuality Score** — On-time arrival percentage
- **Recent Attendance Activity** — Last 3 records with status badges
- **Clock-in / Clock-out Buttons** — With early checkout request option

### 3. Attendance Management

- **Clock-in** (`POST /clock-in`) — Records entry time, detects late arrivals based on office hours
- **Clock-out** (`PUT /clock-out`) — Records exit time, calculates early departure
- **Early Checkout** — If clocking out before office hours, a request is sent for admin approval
- **Attendance History** (`/attendance/history`) — Paginated history (10/20/30/50/100 per page) with status badges (On Time, Late, Early Checkout, etc.)
- **Contract Check** — Users with expired contracts cannot clock-in

### 4. Leave Management

- **Leave Form** (`/leaves/create`) — Submit absence requests with:
  - Leave types: **Sakit** (Sick), **Cuti Tahunan** (Annual), **Keperluan Mendadak** (Emergency)
  - Date range picker (past dates disabled — only today and future)
  - File upload for medical certificates (JPG, PNG, PDF — up to 5MB) with drag-and-drop
  - Auto-calculated leave quota display
- **Leave Quota System:**
  - Default: **8 days/year** per employee (`leave_quota` column)
  - Days are counted inclusively (Apr 24–26 = 3 days)
  - Both **Pending** and **Approved** leaves count toward usage
  - Client-side + server-side validation prevents exceeding quota
- **Leave History** (`/leaves`) — View all submitted requests with status filters

### 5. Approval Center (Admin)

The admin approval hub (`/approval`) includes:

- **Today's Attendance Table** — Ranked list with:
  - 🥇🥈🥉 medals for top 3 earliest arrivals
  - Pagination (5 records per page)
  - Real-time badge showing clocked-in count vs total employees
- **Sub-menus:**
  - **Pending Registrations** (`/approval/registrations`) — Approve or reject new employee signups
  - **Leave Requests** (`/approval/leaves`) — Review leave requests with medical certificate preview in a responsive modal
  - **Early Checkouts** (`/approval/early-checkouts`) — Approve or reject early departure requests
  - **Approval Settings** (`/approval/settings`) — Configure office hours and rules

### 6. Attendance Export

Download attendance reports as Excel-compatible `.xls` files:

| Button | Period | Filename Example |
|---|---|---|
| 📗 **Today** | Current day | `Attendance_Today_24_Apr_2026.xls` |
| 📘 **This Week** | Mon–Sun of current week | `Attendance_Weekly_21_Apr_to_27_Apr_2026.xls` |
| 📙 **This Month** | Full current month | `Attendance_Monthly_Apr_2026.xls` |

Each export includes:
- Header with report title, period, and generation timestamp
- Table: No, Employee ID, Name, Email, Division, Date, Clock In, Clock Out, Status
- Color-coded status cells (green = Checked In, red = Late, yellow = Pending)
- Total record count footer

### 7. Posts / Announcements

Full CRUD system for internal announcements:
- Create, read, update, delete posts
- Available to authenticated & approved users
- Resource routes at `/posts`

### 8. Profile Management

- **Edit Profile** (`/profile`) — Update name, email
- **Change Password** — Secure password update
- **Delete Account** — Self-service account deletion

### 9. System Configuration

- **Office Hours** — Set company check-in and check-out times
- **Company Email Domain** — Configure allowed email domain for registration
- **Approval Settings** — Configure how approvals are processed

### 10. Alpha / Dev Tools

Development utilities at `/alpha/devtools`:
- **Add Days** — Simulate date advancement for testing
- **Reset Testers** — Clear test data
- **Reset Attendance** — Wipe attendance records
- **System Settings** — Override global configuration

---

## 🔌 API Endpoints

RESTful API for mobile app integration:

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/attendance/clock-in` | Clock in (requires auth + approved + active contract) |
| `PUT` | `/api/attendance/clock-out` | Clock out |
| `POST` | `/api/leaves/apply` | Submit a leave request with file upload |
| `GET` | `/api/leaves/history` | Get paginated leave history |

All API routes require authentication via Laravel Sanctum/session.

---

## 🗄 Database Schema

### Users
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `name` | string | Employee name |
| `email` | string | Unique email |
| `password` | string | Hashed password |
| `role` | string | `superadmin`, `admin`, or `user` |
| `divisi` | string? | Department/division |
| `status_pekerjaan` | string? | `aktif`, `cuti`, `resign` |
| `tgl_habis_kontrak` | date? | Contract expiration date |
| `is_approved` | boolean | Admin approval status |
| `leave_quota` | int | Annual leave quota (default: 8) |

### Attendances
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `user_id` | foreignId | Employee reference |
| `tanggal` | date | Attendance date |
| `waktu_masuk` | time? | Clock-in time |
| `waktu_keluar` | time? | Clock-out time |
| `status` | string | `hadir`, `terlambat`, `pulang cepat` |
| `early_checkout_status` | string? | `pending`, `approved`, `rejected` |
| `early_checkout_reason` | text? | Reason for early checkout |

### Leaves
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `user_id` | foreignId | Employee reference |
| `jenis_izin` | string | `Sakit`, `Cuti Tahunan`, `Keperluan Mendadak` |
| `tanggal_mulai` | date | Start date |
| `tanggal_selesai` | date | End date |
| `alasan` | text | Reason for leave |
| `bukti_file` | string? | Uploaded document path |
| `status_approval` | string | `Pending`, `Approved`, `Rejected` |

### Settings
| Column | Type | Description |
|---|---|---|
| `key` | string | Setting key (e.g. `office_check_in`) |
| `value` | text | Setting value |

---

## 🚀 Installation

### Prerequisites

- PHP ≥ 8.2
- Composer
- Node.js ≥ 18
- npm

### Setup

```bash
# 1. Clone the repository
git clone https://github.com/Siddiq-Ahrais/TipTap.git
cd TipTap

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup
php artisan migrate

# 5. Create storage symlink (required for file uploads)
php artisan storage:link

# 6. Start development server
npm run dev                    # Vite dev server (terminal 1)
php artisan serve              # Laravel server (terminal 2)

# OR use the combined dev command:
composer dev
```

The application will be available at `http://localhost:8000`.

### Quick Setup (One Command)

```bash
composer setup
```

This runs the full setup script: install dependencies → generate key → migrate → build assets.

---

## 📖 Usage

### First-Time Setup

1. Register a new account at `/register`
2. Log in as Super Admin via `/admin/login` to approve the account
3. Once approved, the employee can log in and access the dashboard

### Daily Workflow

1. **Employee** logs in → clicks **Clock In** on dashboard
2. At end of day → clicks **Clock Out** (or requests Early Checkout)
3. If sick → goes to **Leaves** → submits absence request with medical certificate
4. **Admin** reviews and approves/rejects requests from the **Approval Center**
5. Admin can download attendance reports from the **Export** section

---

## 📄 License

This project is built for **PT Sukamaju** as part of the Bootcamp program. All rights reserved.

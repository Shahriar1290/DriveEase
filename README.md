<div align="center">

# 🚗 DriveEase

### Vehicle Rental Management System (VRMS)

A full-stack car rental platform built with **Laravel 12** — customers browse, compare and book vehicles, while admins manage the entire fleet, bookings, payments, maintenance and business reports.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql&logoColor=white)
![PHPUnit](https://img.shields.io/badge/PHPUnit-11-E93224?style=flat-square&logo=phpunit&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

</div>

---

## 📖 Overview

DriveEase is a complete vehicle rental management system with three distinct experiences:

| Role | Access | Purpose |
|------|--------|---------|
| **Guest** | Public | Search, filter and compare vehicles, read reviews, contact support |
| **Customer** | `/customer/*` | Book vehicles, manage profile, wishlist, invoices and reviews |
| **Admin** | `/admin/*` | Manage fleet, categories, bookings, payments, customers, maintenance and reports |

Pricing is displayed in **TK (BDT)** and the seeded dataset ships with **51 vehicles across 8 categories**, 20 customers, bookings, maintenance records and reviews so the application is fully browsable right after installation.

---

## ✨ Features

### 👤 Guest (Public)
- Home, About, Contact, FAQ, Privacy and Terms pages
- Vehicle catalogue with search, category/price filters and sorting
- Vehicle detail page with specifications, pricing calculator (daily/weekly/monthly) and reviews
- Side-by-side **vehicle comparison**
- Live navbar search with auto-suggest
- Contact form and newsletter subscription
- Registration, login and **forgot / reset password** flows (rate-limited)

### 🧍 Customer
- Personal dashboard with bookings and activity overview
- Create bookings (pickup/return dates → automatic cost calculation)
- Booking lifecycle: view, cancel, and download a printable **invoice**
- Wishlist (add / remove vehicles)
- Profile editing and password change
- Post reviews on completed rentals
- In-app **notification centre** with unread badge

### 🛡️ Admin
- Dashboard with KPIs and recent activity
- Fleet management (create / read / update / delete vehicles, images, availability)
- Vehicle **category** management
- Booking approvals: approve, reject, complete
- Payment tracking and status updates
- Customer management with account activation / deactivation
- Maintenance records for the fleet
- Review moderation (approve / reject / delete)
- Reports: **revenue, vehicle utilization, customers, maintenance** with **PDF export** (dompdf)

### ⚙️ Under the hood
- Role-based access control via `AdminMiddleware` / `CustomerMiddleware`
- Route throttling on login, registration and password reset endpoints
- Owner-based authorization checks on booking/invoice access
- Performance indexes migration for hot query paths
- Feature test suite covering auth, booking flow, public pages and admin access

---

## 🛠 Tech Stack

| Layer | Technologies |
|-------|--------------|
| Backend | Laravel 12, PHP 8.2+, Eloquent ORM, Blade templates |
| Frontend | Bootstrap 5.3, Tailwind CSS 4, Font Awesome, Animate.css, SweetAlert2, vanilla JS |
| Build | Vite 7, Laravel Vite Plugin, laravel-vite-plugin |
| Database | MySQL 8 (default) or SQLite (zero-config alternative) |
| PDF | barryvdh/laravel-dompdf |
| Testing | PHPUnit 11, Laravel feature tests |
| Tooling | Laravel Pint, Laravel Pail, Laravel Sail, Tinker |

---

## 📂 Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Fleet, bookings, payments, reports, maintenance…
│   │   ├── Auth/           # Login, register, password reset
│   │   ├── Customer/       # Bookings, wishlist, profile, reviews
│   │   ├── HomeController.php
│   │   └── VehicleController.php
│   ├── Http/Middleware/    # AdminMiddleware, CustomerMiddleware
│   ├── Models/             # Vehicle, Booking, Payment, Review, Wishlist…
│   └── Support/helpers.php # Blade view helpers (status badges, formatting)
├── database/
│   ├── migrations/         # Schema + performance indexes
│   └── seeders/            # Demo users, vehicles, bookings, reviews…
├── resources/views/        # admin/ customer/ auth/ home/ layouts/ partials/
├── routes/web.php          # All application routes
├── tests/Feature/          # Auth, booking flow, public pages, admin access
└── DriveEase_Project_Documentation.pdf   # Full project documentation
```

---

## 🚀 Getting Started

### Requirements

- PHP **8.2+** (with `pdo_mysql` / `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `curl`, `zip`)
- Composer 2
- Node.js 18+ and npm
- MySQL 8 (or use SQLite for a zero-config setup)

### 1. Clone & install

```bash
git clone https://github.com/Shahriar1290/DriveEase.git
cd DriveEase
composer install
npm install
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Then edit `.env`:

```env
# MySQL (default)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vrms
DB_USERNAME=root
DB_PASSWORD=
```

> 💡 Prefer no database server? Set `DB_CONNECTION=sqlite` and run `touch database/database.sqlite`.

### 3. Migrate & seed

```bash
php artisan migrate --seed
php artisan storage:link
```

### 4. Build assets & run

```bash
npm run build          # or: npm run dev
php artisan serve      # → http://localhost:8000
```

### ⚡ Quick setup (one command)

```bash
composer setup
```

Runs `composer install` → copies `.env` → `key:generate` → `migrate --force` → `npm install` → `npm run build`.

### 🧑‍💻 Everyday development

```bash
composer dev
```

Starts the server, queue worker, log tailer (`pail`) and Vite watcher concurrently.

---

## 🔑 Demo Accounts

Seeded automatically with `php artisan migrate --seed` — password for all accounts is `password`.

| Role | Email | URL |
|------|-------|-----|
| Admin | `admin@vrms.com` | `/admin/dashboard` |
| Customer | `alice@example.com` (and 19 more) | `/customer/dashboard` |

---

## 🧪 Testing

The test suite requires a reachable MySQL server with an empty `vrms_testing` database:

```sql
CREATE DATABASE vrms_testing;
```

> The database credentials for tests live in `phpunit.xml`.

```bash
composer test          # clears config cache + runs php artisan test
# or
php artisan test
```

**Test suites**

| File | Coverage |
|------|----------|
| `AuthenticationTest` | Login/registration pages, valid & invalid login, logout |
| `BookingFlowTest` | Booking list/create, guest protection, admin approve/reject |
| `AdminDashboardTest` | Guest redirection, role isolation, admin section access |
| `PublicPagesTest` | Home, vehicles, about, contact, FAQ, forgot-password |

---

## 🗺 Key Routes

<details>
<summary><b>Public</b></summary>

```
GET  /                      home
GET  /vehicles              catalogue
GET  /vehicles/search       search
GET  /vehicles/compare      comparison
GET  /vehicles/{slug}       vehicle detail
GET  /about | /contact | /faq | /privacy | /terms
POST /contact               contact form
POST /newsletter            newsletter subscribe
```
</details>

<details>
<summary><b>Auth</b></summary>

```
GET|POST /login             (throttle 5/min)
GET|POST /register          (throttle 10/min)
POST     /logout
GET|POST /forgot-password   (throttle 3/min)
GET|POST /reset-password/{token?}
```
</details>

<details>
<summary><b>Customer</b> — prefix <code>/customer</code>, middleware <code>auth + CustomerMiddleware</code></summary>

```
GET  /customer/dashboard
GET|POST /customer/bookings…          create, store, show, cancel, invoice
GET|POST /customer/wishlist…          index, toggle
GET|POST /customer/profile            edit, update, change password
POST /customer/reviews
```
</details>

<details>
<summary><b>Admin</b> — prefix <code>/admin</code>, middleware <code>auth + AdminMiddleware</code></summary>

```
GET      /admin/dashboard
GET|POST /admin/vehicles…             CRUD
GET|PUT|DELETE /admin/categories…     CRUD
GET      /admin/bookings…             approve / reject / complete
GET|POST /admin/payments…             status update
GET|POST /admin/customers…            toggle active status
GET|PUT|DELETE /admin/maintenance…    CRUD
GET|POST /admin/reviews…              approve / reject / destroy
GET      /admin/reports…              revenue, vehicles, customers, maintenance
```
</details>

---

## 🔒 Security

- **Middleware based role gating** — customers can never reach `/admin/*` and vice versa
- **Ownership checks** on booking details and invoices (`403` for foreign records)
- **Rate limiting** on credential and password-reset endpoints
- **Hashed passwords** (bcrypt, 12 rounds) and CSRF-protected Blade forms
- **Validation** on all write endpoints (controller-level `validate()`)
- Environment-driven configuration with `.env` excluded from version control

---

## 📄 Documentation

A full project report (ER diagram, module descriptions, screenshots, testing evidence) is included in the repository:

- [`DriveEase_Project_Documentation.pdf`](./DriveEase_Project_Documentation.pdf)
- [`DriveEase_Project_Documentation.docx`](./DriveEase_Project_Documentation.docx)

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m "Add amazing feature"`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please run `php artisan test` and Laravel Pint before submitting.

---

## 📜 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

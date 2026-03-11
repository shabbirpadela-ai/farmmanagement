# Dove Haven Farms – Management Portal

A **PHP 8 + MySQL MVC** farm management portal for poultry operations.
Deploy as a cPanel shared-hosting site at `crm.domain.com`.

---

## Features

| Module | Description |
|--------|-------------|
| **Dashboard** | Daily eggs, crates, revenue, weekly chart |
| **Rearing & Production** | Record egg collections per house with grade breakdown |
| **Stock & Feed** | Inventory purchases, usage tracking, low-stock alerts |
| **Sales & Purchases** | Customers, orders, payments, purchase recording |
| **Crate Inventory** | Real-time crate tracking with movement log |
| **Reports** | Generate daily/weekly/monthly reports; PDF export |
| **Admin Portal** | User management (admin-only) |

---

## Requirements

- PHP **8.1+**
- MySQL / MariaDB **5.7+**
- Apache with **mod_rewrite** enabled

---

## cPanel Deployment Steps

### 1. Upload the repository

Upload (or `git clone`) this repository to your server, e.g.:
```
/home/youraccount/farmmanagement/
```

### 2. Set Document Root to `public/`

In cPanel → **Addon Domains** (or the primary domain), set the Document Root to:
```
/home/youraccount/farmmanagement/public
```

### 3. Create the MySQL database

In cPanel → **MySQL Databases**:
1. Create a new database, e.g. `youraccount_farm`
2. Create a database user and assign it **All Privileges**

### 4. Import the schema

In **phpMyAdmin** (or via CLI):
```sql
SOURCE /path/to/farmmanagement/database/schema.sql;
```

Or via CLI:
```bash
mysql -u youraccount_farm -p youraccount_farm < database/schema.sql
```

### 5. Configure `.env`

Copy `.env.example` to `.env` **in the project root** (NOT inside `public/`):
```bash
cp .env.example .env
```

Edit `.env`:
```ini
APP_ENV=production
APP_URL=https://crm.domain.com

DB_HOST=localhost
DB_NAME=youraccount_farm
DB_USER=youraccount_farmuser
DB_PASS=your_db_password

SESSION_NAME=dhf_session
SESSION_LIFETIME=7200
```

> ⚠️ **Never commit `.env` to git.** It is listed in `.gitignore`.

### 6. Create the admin user

Run the seeder once from the project root via SSH:
```bash
ADMIN_USER=admin ADMIN_PASS=YourStrongPassword123 php database/seed.php
```

Or simply import `database/seed.sql` (default password: `password` — **change immediately**).

### 7. Verify

Visit `https://crm.domain.com` — you should be redirected to `/login`.

---

## First Login

| Username | Role  |
|----------|-------|
| `admin`  | Admin |

Password: whatever you set in step 6 (seed.php).  
**Change it immediately** after first login via the Admin Portal.

---

## URL Routes

| Method | URL | Description |
|--------|-----|-------------|
| `GET` | `/` | Redirect → `/dashboard` or `/login` |
| `GET` | `/login` | Login page |
| `GET` | `/dashboard` | Dashboard |
| `GET` | `/rearing` | Rearing & Production |
| `GET` | `/inventory` | Stock & Feed |
| `GET` | `/crm` | Sales & Purchases |
| `GET` | `/crates` | Crate Inventory |
| `GET` | `/reports` | Reports |
| `GET` | `/admin` | Admin Portal (admin role required) |

## API Endpoints

All API routes start with `/api/` and return `{ ok: true, data: ... }` or `{ ok: false, error: "..." }`.

| Method | URL | Description |
|--------|-----|-------------|
| `POST` | `/api/auth/login` | AJAX login |
| `POST` | `/api/auth/logout` | Logout |
| `GET` | `/api/bootstrap` | Current user + today's summary |
| `GET/POST` | `/api/production` | Production CRUD |
| `GET/POST` | `/api/inventory` | Inventory items |
| `POST` | `/api/inventory/purchase` | Record purchase |
| `POST` | `/api/inventory/usage` | Record usage |
| `GET/POST` | `/api/crm/customers` | Customer CRUD |
| `GET/POST` | `/api/crm/orders` | Order CRUD |
| `POST` | `/api/crm/orders/{id}/payment` | Record payment |
| `GET/POST` | `/api/crm/purchases` | Purchase CRUD |
| `GET` | `/api/crates` | Crate stock status |
| `POST` | `/api/crates/adjust` | Adjust crate stock |
| `GET` | `/api/reports/generate` | Generate report |
| `GET/POST` | `/api/admin/employees` | Employee management |

---

## Project Structure

```
farmmanagement/
├── app/
│   ├── Core/          # Router, Controller, Db, Auth
│   ├── Controllers/   # Page & API controllers
│   ├── Models/        # PDO-based models
│   └── Views/         # PHP view files + layouts
├── config/
│   ├── database.php   # PDO connection (reads .env)
│   └── env.php        # .env loader
├── database/
│   ├── schema.sql     # Full MySQL schema
│   ├── seed.sql       # Default seed data
│   └── seed.php       # CLI admin-user seeder
├── public/            # ← Set as Document Root
│   ├── index.php      # Front controller
│   ├── .htaccess      # Apache rewrite rules
│   ├── api/
│   │   └── index.php  # API front controller
│   └── assets/
│       ├── style.css
│       └── app.js
├── src/               # Original static UI (reference)
├── .env.example       # Environment variable template
└── .gitignore
```

---

## Security Notes

- Passwords stored as `bcrypt` hashes via `password_hash()`
- All database queries use **PDO prepared statements**
- CSRF token validated on all state-changing API requests
- Session cookie: `HttpOnly`, `SameSite=Lax`, `Secure` (in production)
- No secrets in tracked files (`.env` is gitignored)
- Admin routes protected by role check

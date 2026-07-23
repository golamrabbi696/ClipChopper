# ClipChopper Full-Stack

**Astro** (frontend) + **Laravel** (backend API) + **MySQL** (database)

## Structure

```
site/
├── frontend/   → Astro static site (http://localhost:4321)
├── backend/    → Laravel REST API (http://localhost:8000)
└── index.html  → (original static site, kept as reference)
```

## Quick Start

### Frontend (Astro)
```bash
cd frontend
npm install
npm run dev       # → http://localhost:4321
```

### Backend (Laravel)
```bash
cd backend
# 1. Fill DB_PASSWORD in .env (see SETUP.md)
# 2. Create the 'clipchopper' database in MySQL
php artisan migrate --seed
php artisan serve  # → http://localhost:8000
```

See `backend/SETUP.md` for full instructions including Stripe setup.

## Pages
| URL | Description |
|-----|-------------|
| `/` | Main landing page |
| `/contact` | Booking / contact form |
| `/login` | Client login |
| `/dashboard` | Client content dashboard (auth required) |

## API Routes
| Method | Route | Auth |
|--------|-------|------|
| `POST` | `/api/v1/contact` | Public |
| `POST` | `/api/v1/newsletter/subscribe` | Public |
| `POST` | `/api/v1/auth/login` | Public |
| `GET` | `/api/v1/dashboard/deliverables` | Client JWT |
| `POST` | `/api/v1/stripe/checkout` | Client JWT |
| `GET` | `/api/v1/admin/leads` | Admin JWT |
| `GET` | `/api/v1/admin/bookings` | Admin JWT |
| `GET` | `/api/v1/admin/subscribers` | Admin JWT |

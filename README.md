# InvoiceFlow

InvoiceFlow is a Laravel 12 invoicing application for freelancers and small service businesses. It includes authenticated client and invoice management, payment tracking, a public invoice share page, and a small JSON API for clients and invoices.

The codebase uses Laravel Breeze with Livewire Volt for authentication screens, Blade for the main UI, SQLite by default for local development, and Pest for feature and API tests.

## Current feature set

- dashboard with monthly revenue, outstanding invoices, overdue invoices, recent invoices, and recent payments
- authenticated CRUD flows for clients and invoices
- invoice line items with subtotal, tax, discount, total, and balance calculation
- invoice lifecycle management through `draft`, `sent`, `paid`, and `overdue` states
- payment recording against invoices
- public invoice view via UUID-backed `public_id`
- authenticated JSON API resources for clients and invoices
- demo data seeding for local development

## Tech stack

- PHP 8.2+
- Laravel 12
- Livewire 3
- Volt
- Blade
- Vite
- Tailwind CSS
- SQLite for the default local database
- Pest for testing

## Getting started

### Prerequisites

- PHP 8.2 or newer
- Composer
- Node.js 18+ and npm

### Install and run

1. Install PHP and frontend dependencies and generate the app key:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
```

2. Make sure the SQLite database file exists:

```powershell
if (-not (Test-Path database/database.sqlite)) { New-Item database/database.sqlite -ItemType File }
```

3. Run the database migrations and seed the demo data:

```bash
php artisan migrate:fresh --seed
```

4. Start the local app:

```bash
composer run dev
```

That starts:

- the Laravel development server
- the queue listener
- Laravel Pail log streaming
- the Vite dev server

Open `http://127.0.0.1:8000` or `http://localhost:8000` after the server starts.

### Quick setup shortcut

If you want the repo bootstrap script:

```powershell
composer run setup
php artisan db:seed
composer run dev
```

`composer run setup` installs dependencies, copies `.env`, generates an app key, runs migrations, and builds frontend assets. Seeding is still a separate step.

## Demo account

After seeding, you can sign in with:

- email: `demo@invoiceflow.test`
- password: `password`

The demo seeder creates:

- 1 freelancer account
- 3 clients
- invoices in draft, sent, overdue, and paid states
- sample payments for the paid invoice

## Testing

Run the test suite with:

```bash
composer test
```

The project currently includes feature coverage for:

- client management
- invoice creation, sending, and payment workflows
- database seeding
- public invoice access
- API resource responses
- auth scaffolding behavior

## Application structure

The current code is organized around a small invoicing domain:

```text
app/
  Enums/
    InvoiceStatus.php
  Http/
    Controllers/
      Api/
      Web/
    Requests/
    Resources/
  Models/
    Client.php
    Invoice.php
    InvoiceItem.php
    Payment.php
    User.php
  Policies/
  Providers/
  Services/
    InvoiceCalculator.php
    InvoiceNumberGenerator.php
    InvoiceStatusManager.php
  Support/
    Money.php
database/
  factories/
  migrations/
  seeders/
resources/
  css/
  js/
  views/
routes/
  web.php
  api.php
  auth.php
tests/
  Feature/
  Unit/
```

## Architecture notes

### Domain models

- `Client` belongs to a user and owns many invoices
- `Invoice` belongs to a user and client and owns many items and payments
- `Payment` belongs to a user and invoice
- money values are stored as integer minor units to avoid floating point issues

### Services

- `InvoiceCalculator` normalizes line items and computes subtotal, discount, tax, total, and balance
- `InvoiceNumberGenerator` generates sequential invoice numbers like `INV-0001`
- `InvoiceStatusManager` keeps invoice state in sync with send date, due date, and payments

### Web routes

- `/` welcome page
- `/dashboard`
- resource routes for `/clients`
- resource routes for `/invoices`
- `/payments`
- `/public/invoices/{invoice:public_id}`
- `/invoices/{invoice}/send`
- `/invoices/{invoice}/payments`

### API routes

Authenticated API endpoints are available for:

- `GET|POST /api/clients`
- `GET|PUT|PATCH|DELETE /api/clients/{client}`
- `GET|POST /api/invoices`
- `GET|PUT|PATCH|DELETE /api/invoices/{invoice}`

These routes use Laravel auth middleware plus `throttle:api`.

## Useful commands

```bash
php artisan migrate:fresh --seed
php artisan test
npm run dev
npm run build
php artisan route:list
```

## Notes

- the default `.env.example` is already configured for SQLite
- a committed `database/database.sqlite` file exists in this repo today, but creating it explicitly is the safest setup step for a fresh checkout
- the current UI uses Blade views for the invoicing screens and Livewire Volt for auth/profile pages

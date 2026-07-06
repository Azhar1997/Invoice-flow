# InvoiceFlow Implementation Plan

## Objective

Build and polish a portfolio-quality Laravel 12 invoicing application for freelancers that demonstrates strong Laravel fundamentals, clean architecture, and disciplined scope.

This document reflects the codebase as it exists today and separates completed work from likely next steps.

## Current status

InvoiceFlow is no longer in a scaffolding-only phase. The application already includes:

- authenticated web flows for clients, invoices, payments, dashboard, and profile management
- JSON API endpoints for clients and invoices
- invoice total and balance calculation services
- invoice status synchronization for `draft`, `sent`, `paid`, and `overdue`
- UUID-based public invoice pages
- demo data seeding
- Pest feature, API, auth, and seeder tests

## Implemented foundation

### Domain model

The current relational model is in place and wired into the UI, API, and tests:

- `users`
- `clients`
- `invoices`
- `invoice_items`
- `payments`

Relationships are implemented in the Eloquent models and used across controllers, policies, resources, factories, and tests.

### Money handling

The original money strategy is now implemented:

- persisted money values are stored as integer minor units such as `subtotal_amount` and `balance_due_amount`
- tax and discount remain percentage-based at the invoice level
- totals are calculated through `App\Services\InvoiceCalculator`
- formatting helpers live in `App\Support\Money`

### Invoice lifecycle

The invoice lifecycle is implemented through `App\Enums\InvoiceStatus` and `App\Services\InvoiceStatusManager`:

- `draft`: invoice exists but has not been sent
- `sent`: invoice has a `sent_at` timestamp and is not yet overdue or fully paid
- `paid`: `amount_paid` is greater than or equal to `total_amount`
- `overdue`: invoice was sent, remains unpaid, and the due date has passed

### Public invoice access

Public invoice sharing is implemented:

- invoices have a UUID-backed `public_id`
- public views use `/public/invoices/{invoice:public_id}`
- public views can mark `viewed_at` when the page is first opened

## Route surface

### Web

Implemented routes include:

- `/`
- `/dashboard`
- `/profile`
- resource routes for `/clients`
- resource routes for `/invoices`
- `/payments`
- `/public/invoices/{invoice:public_id}`
- `POST /invoices/{invoice}/send`
- `POST /invoices/{invoice}/payments`

### API

Authenticated API routes are implemented for:

- `apiResource('clients')`
- `apiResource('invoices')`

The current middleware is:

- `auth`
- `throttle:api`

Note: the earlier plan referenced Sanctum-protected routes, but the current code uses standard auth middleware and does not yet include Sanctum as a dependency.

## Completed work

### 1. App shell and authentication

Completed:

- Laravel Breeze auth scaffolding is installed
- Livewire 3 and Volt are installed and used for auth/profile pages
- dashboard screen is implemented
- demo account seeding is implemented

Not implemented from the original vision:

- no Filament admin surface
- no documented toast/notification system beyond standard session flash usage

### 2. Core invoice domain

Completed:

- schema and migrations for clients, invoices, invoice items, and payments
- Eloquent models and relationships
- form requests for clients, invoices, and payments
- authorization policies for clients and invoices
- invoice numbering service
- invoice calculation service
- invoice status synchronization service

### 3. Invoicing workflows

Completed:

- client CRUD
- invoice CRUD
- invoice item capture through invoice forms
- send invoice action
- payment recording flow
- payment history index
- overdue status handling
- public invoice preview

Changed from the original plan:

- the invoicing UI is currently Blade/controller driven
- there is not currently a dedicated Livewire invoice editor

### 4. API and resources

Completed:

- client API controller and resource
- invoice API controller and resource
- nested invoice resource output for client, items, payments, and public URL
- API tests for client resource behavior

### 5. Seeders, factories, and tests

Completed:

- factories for users, clients, invoices, invoice items, and payments
- demo workspace seeding
- feature tests for invoice workflows
- feature tests for client management
- feature tests for database seeding
- feature tests for auth and profile flows
- API tests

## Outstanding work

These items still appear to be valid next steps based on the current repository.

### High-value product polish

- add screenshots or a short walkthrough for portfolio presentation
- improve the public invoice page with stronger presentation and share/download affordances
- add richer dashboard insights such as aging buckets or outstanding totals by status

### API and integration hardening

- decide whether API auth should remain session-based or move to Sanctum
- add deeper API coverage for invoices, authorization, and validation errors
- document API usage examples in the README or a dedicated API doc

### Workflow and infrastructure improvements

- introduce explicit events such as `InvoiceSent` and `InvoicePaid`
- add notifications or email delivery for invoice sending
- add queued jobs only when there is a concrete async workflow to support
- decide whether Redis-backed queues are actually needed for this scope

### Delivery and quality

- add static analysis such as Larastan or PHPStan if desired for the portfolio target
- add CI automation such as GitHub Actions
- decide whether Docker/Sail setup is worth maintaining for this project

### Optional future features

- PDF generation for invoices
- payment provider or webhook integration
- client self-service actions beyond public viewing
- stronger audit/activity tracking

## De-scoped or not currently present

The earlier plan mentioned several items that are not part of the current implementation:

- FilamentPHP
- Sanctum
- Redis queue configuration
- queued PDF generation
- mocked Stripe webhook structure
- GitHub Actions workflow
- Docker/Sail-based local workflow documentation

These may still be added later, but they should be treated as optional roadmap items rather than implied existing behavior.

## Current constraints and notes

- the project currently targets `php: ^8.2` in `composer.json`
- the local default database setup is SQLite
- the README now documents the current app behavior and local setup
- this plan should be updated whenever roadmap assumptions change, especially around API auth, notifications, and deployment tooling

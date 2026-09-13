# Context Resume — B2B+B2C Commerce Platform (Bagisto)

## What This Project Is

- B2B + B2C ecommerce platform for an Indian agency
- Built on Bagisto (free, open-source, Laravel-based) + B2B Suite
- ~100 users, ~1,000 products
- Two custom modules built: Site Visits + Product Samples

## Current State

### What's Done

1. **Site Visits module** — fully coded (`packages/Agency/SiteVisit/`)
2. **Product Samples module** — fully coded (`packages/Agency/ProductSample/`)
3. **Bagisto installed locally** at `my-store/` with both modules registered
4. **Database seeded** — 4 site visits + 5 sample requests + 3 sample inventory records
5. **Test customer created**: test@example.com / password123
6. **Admin**: ahmad13faizan@gmail.com / admin123

### RESOLVED Issues (history)

- ✅ **Admin panel "Undefined array key icon" error** — FIXED. Root cause was views using Bagisto 1.x `@extends('admin::layouts.master')`. Bagisto 2.x uses `<x-admin::layouts>` component. All admin views updated.
- ✅ **All admin views working** — dashboard, site-visits list+detail, sample-requests list+detail, sample-inventory all return 200.
- ✅ **All shop (storefront) views working** — updated to `<x-shop::layouts.account>`. /site-visits, /site-visits/create, /sample-requests all return 200.
- ✅ **FK type mismatch** — Bagisto tables use `unsignedInteger` (not bigInteger) for IDs. Both sample_requests + site_visit_status_logs migrations fixed to use `unsignedInteger` for customer_id/product_id/admin FKs.
- ✅ **Google OAuth "missing client_id" error** — disabled all social logins (no OAuth creds). DB: `customer.settings.social_login.*` set to 0.
- ✅ **Email verification flow** — enabled (`customer.settings.email.verification = 1`). Registration creates unverified user, login blocks unverified. Flow was always correct in code; the issue was emails went to log file.
- ✅ **Mailpit added** — Docker email catcher. All outgoing emails appear at http://localhost:8025. `.env` MAIL_MAILER=smtp, port 1025.
- ✅ **QA public sharing** — Cloudflare Tunnel installed + tested. `./share-qa.sh` exposes the app over public HTTPS for free.
- ✅ **GST field (Agency/B2BFields package)** — admin customer create/edit forms now have an optional GST Number field with Indian GSTIN format validation (`22AAAAA0000A1Z5`). Stored on `customers.gst_number` column. Injected via view-render events (`bagisto.admin.customers.create.after` + `...view.edit.after`); validated/saved via `customer.create.before/after` + `customer.update.before/after` event listeners. Tested: valid saves, invalid rejected, empty allowed.
- ✅ **Workspace sync DONE** — all 3 packages (SiteVisit, ProductSample, B2BFields) now identical between `my-store/packages/` and workspace `packages/`. No more drift.
- ✅ **B2B Suite installed** — official `bagisto/b2b-suite` v2.0.1 (free, MIT license, from Webkul/Bagisto team on GitHub). Required PHP 8.3+ so **PHP 8.3 was installed alongside 8.2** (not replacing it) via the same ondrej/php PPA. Project now runs on PHP 8.3 (`php8.3 artisan serve --port=8001`, `php8.3 /usr/local/bin/composer ...`). Installed via `composer require bagisto/b2b-suite` + `php artisan b2b-suite:install` (ran 25 migrations: company accounts, roles, catalogs, credit, quotes, requisition lists, purchase orders). Provider registered LAST in `bootstrap/providers.php` (order matters, no auto-discovery): `Webkul\B2BSuite\Providers\B2BSuiteServiceProvider::class`. Enabled via `b2b.general.settings.active = 1` in core_config (default is `require_company_approval = true`, matching your "admin approves retailer accounts" decision). Verified: storefront `/customer/register` now shows Personal/Company tab toggle with company sign-up form; admin `/admin/b2b/companies` loads; all existing custom modules (Site Visits, Product Samples, GST field) still work on PHP 8.3 — zero regressions.
- ✅ **DataGrid `dropdown` column-type bug FIXED** — after the B2B Suite install, Bagisto's `ColumnTypeEnum` only allows `string/integer/decimal/boolean/date/datetime/aggregate`. Custom DataGrids used the now-invalid `'type' => 'dropdown'` for the status column → 500 on `/admin/site-visits` and `/admin/sample-requests`. Fixed both `SiteVisitDataGrid` + `SampleRequestDataGrid` to use `'type' => 'string'` + `'filterable_type' => 'dropdown'` + `'filterable_options'` (the current Bagisto pattern, matching core `OrderDataGrid`). Also added the missing `admin.site-visits.mass-cancel` route + `massCancel()` controller method. Both pages return 200 again.
- ✅ **Sample Inventory edit FIXED** — the DataGrid edit action redirected to `?edit=<product_id>` but the view ignored it. Added an inline edit form (shown when `?edit=` present) in `inventory/index.blade.php` + changed the update route from POST to PUT to match the form.
- ✅ **Razorpay payment gateway BUILT (free, custom `Agency/Razorpay` package)** — the polished ready-made packages now need a paid license, so built a free one using Razorpay's official `razorpay/razorpay` PHP SDK (installed via composer). Supports **UPI (QR + UPI ID/collect + intent), debit/credit cards, netbanking, wallets** via Razorpay standard checkout. Files: Payment class, `RazorpayController` (creates Razorpay order → renders checkout popup → verifies payment signature server-side → places Bagisto order via `OrderResource`/`OrderRepository`), routes (`razorpay.redirect` / `razorpay.callback` [CSRF-exempt] / `razorpay.cancel`), checkout view (`checkout.razorpay.com/v1/checkout.js`), admin config fields (Key ID / Key Secret / status / sort), lang file. Registered provider in `bootstrap/providers.php` + PSR-4 autoload in `composer.json`. Verified: routes registered, payment method + admin config load, SDK loads. **PENDING USER ACTION**: create Razorpay account → enter Test Mode `rzp_test_...` Key ID + Secret in Admin → Configure → Sales → Payment Methods → Razorpay, enable it, then test with Razorpay test cards / UPI `success@razorpay`. Swap to `rzp_live_...` keys to go live (no code change). Recommended next: add a Razorpay webhook for production reliability.
- ✅ **VAT ID relabelled to "GST/VAT Number"** — renamed the core `vat-id` address label (Bagisto `vat_id` on customer addresses + checkout) across `Webkul/Shop` (4 strings) and `Webkul/Admin` (3 strings) English lang files. NOTE: these are in-project core packages so a Bagisto upgrade could overwrite them; move to a lang override package if upgrade-safety is needed.
- ✅ **Company signup: GST OR Aadhaar choice added** — created two B2B **company attributes** (EAV) via `CompanyAttributeRepository`: `id_proof_type` (select: GST / Aadhaar, required, signup) + `id_proof_number` (text, required, signup). CRITICAL GOTCHA: `getSignUpAttributes()` filters by `whereHas('attribute_group')` AND `is_visible_on_sign_up=1` — a new attribute will NOT appear on signup until it's also mapped in `b2b_company_attribute_group_mappings` (mapped both to group 1). Verified both render on `/customer/register?type=company`. Aadhaar/GST are **manually verified by admin during approval** (no paid KYC/OTP provider — deferred to save cost). To create B2B company attributes programmatically, use `app(CompanyAttributeRepository::class)->create([... 'en'=>['name'=>...], 'options'=>[['admin_name'=>,'sort_order'=>,'en'=>['label'=>]]]])` — do NOT run `artisan tinker <file>` (hangs on stdin) or pipe a file starting with `<?php` (parse error) or with `use` statements (class conflict); pipe body-only with fully-qualified class names, or use `--execute`.
- ✅ **S Tree product catalog → import CSVs prepared** — from the S Tree electrical products catalog PDF. Built Bagisto-format product-import CSVs in `product-imports/`: `stree-gold-series.csv` (29), `stree-grey-series.csv` (31), `stree-black-series.csv` (35), `stree-plates.csv` (14, one product per plate type at base/1M price), `stree-accessories.csv` (55: holders, door bells, line testers, extension cords/power strips, plugs/multiplugs, MCBs, isolators, kit-kat fuses). All **simple products** (user chose simple, not colour variants), unique SKUs (prefixes SG-/SGR-/SB-/PLATE-/HLD-/BELL-/LT-/EXT-/PS-/PLUG-/MCB-/etc.), optional fields left blank or "sample", price=list price, stock=100, family=default. ~164 products total. Verified: 38 columns/row, no duplicate SKUs across files. **NOT yet imported** — Bagisto has no CLI import; import via Admin → Settings → Data Transfer → Imports → Create (type Products) → upload each CSV → Validate → Import. **White series (S-1xx) deliberately excluded** — the catalog reuses the same S-codes for rocker vs flat variants, which would collide; needs careful de-duping before generating.
- ✅ **"Sales Rep" admin role created** — Admin role (id 2) `permission_type='custom'` with permissions for dashboard, sales (orders/invoices/shipments/transactions), customers, B2B (companies/credit/quotes/purchase-orders), site-visits, product-samples, reporting. Assign new sales-rep admin users this role (Admin → Settings → Users → Create). Created via `Webkul\User\Models\Role::create([...])`.
- ✅ **Fixed empty B2B signup select-option labels** — the B2B Suite partial `b2b::shop.companies.sign-up.controls` renders `{{ $option->name ?? $option->name }}` but company-attribute options only have `label`/`admin_name` (no `name`), so select dropdowns (e.g. ID Proof Type) showed blank options. Fixed by overriding the view at `my-store/resources/views/vendor/b2b/shop/companies/sign-up/controls.blade.php` (Laravel vendor-view override) using `{{ $option->label ?? $option->admin_name }}`. NOTE: this override lives in `my-store/resources/views/vendor/` (NOT in packages/Agency), so it's outside the normal Agency rsync — remember it when moving code.
- ✅ **Sales Representative attach on individual customers** — B2B Suite added `customers.sales_rep_id` (FK→admins). Added a "Sales Representative" dropdown (list of admin users) to the admin customer create + edit forms via `B2BFields` view hooks, saved on `customer.create.after`/`customer.update.after` (extended `saveGst`→`saveSalesRep`), and shown read-only on the customer view page. Sales reps = admin users (Admin → Settings → Users). Verified renders on `/admin/customers/view/{id}`.
- ✅ **GST number now shown on admin customer VIEW page** — previously only on create/edit forms. Added `bagisto.admin.customers.customers.view.card.accordion.customer.after` view-render hook in `B2BFieldsServiceProvider` + `gst-field-view.blade.php` partial (Vue `<template v-if="customer && customer.gst_number">` card). Verified 200 + field renders at `/admin/customers/view/{id}`. NOTE: correct customer view route is `admin/customers/view/{id}` (NOT `customers/customers/view`).

### Important Notes

- All 3 custom packages are registered in `my-store/bootstrap/providers.php` and `my-store/composer.json` autoload. New package: `Agency\B2BFields\Providers\B2BFieldsServiceProvider`.

### QA Sharing (Free Public Access — No VPS)

- **cloudflared installed** at /usr/local/bin/cloudflared
- Run `./share-qa.sh` → starts Docker + PHP server + Cloudflare tunnel
- Prints a public `https://xxxx.trycloudflare.com` URL (random, changes each run)
- Laptop must stay on + script running. Ctrl+C stops it.
- `trustProxies('*')` already set in bootstrap/app.php so Laravel auto-detects tunnel host (assets/forms work)
- SESSION_DOMAIN=null so no CSRF/419 issues across domains
- Full guide: `docs/QA-SHARING-GUIDE.md`
- ⚠️ Admin password still `admin123` — change before sharing with external QA

### How to Start Dev Environment

```bash
cd ~/projects/bagistor-sample
./start-dev.sh
```

Or manually:

```bash
docker start bagisto-mysql bagisto-redis
cd my-store
php artisan serve --port=8001
```

- Store: http://localhost:8001
- Admin: http://localhost:8001/admin

### How to Stop

```bash
./stop-dev.sh
# or just Ctrl+C on the server + docker stop bagisto-mysql bagisto-redis
```

## What's Left To Do (Custom Build List)

- [x] Verify admin panel loads without errors (icon fix)
- [x] Test Site Visits admin datagrid + detail pages
- [x] Test Product Samples admin pages
- [x] Test storefront pages (customer login → request sample / site visit)
- [x] Email verification flow + Mailpit
- [x] Free QA public sharing (Cloudflare Tunnel)
- [x] Sync my-store/packages fixes back to workspace packages/ (version control)
- [x] GST field on retailer registration form (Agency/B2BFields — admin customer create/edit)
- [x] B2B Suite installation and configuration (company accounts, quotes, catalogs, credit — active + require_company_approval on)
- [x] Razorpay payment gateway (custom `Agency/Razorpay` package — UPI/cards/netbanking/wallets). Needs user's Razorpay API keys entered in admin to activate.
- [x] Show gst_number on admin customer VIEW page
- [x] Fix DataGrid `dropdown` column-type 500s (site-visits + sample-requests) + sample-inventory edit
- [ ] SMS/WhatsApp notification integration (Gupshup/MSG91) — needs provider account + API keys
- [ ] Shipping connector (Shiprocket/Delhivery) — needs provider account + API keys
- [ ] OTP/phone auth module (depends on SMS gateway)
- [ ] Configure B2B Suite details further: quotation prefixes, company catalogs/pricing tiers, sales reps, company attributes, email notification toggles (Admin Panel → Configure → B2B Suite)
- [ ] Test full company registration → admin approval → login flow end-to-end
- [ ] Change admin password from admin123 before any external sharing
- [ ] Enter Razorpay API keys in admin + test checkout (Razorpay test cards / UPI success@razorpay); add production webhook

## File Structure

```
~/projects/bagistor-sample/
├── packages/Agency/          ← SOURCE (workspace, version-controlled)
│   ├── SiteVisit/
│   └── ProductSample/
├── my-store/                 ← RUNNING BAGISTO INSTALL
│   ├── packages/Agency/      ← COPY of modules (running code)
│   ├── bootstrap/providers.php  ← Custom providers registered here
│   └── ...
├── docs/                     ← Business + Technical overview docs
├── .kiro/specs/              ← Spec documents (requirements, design, tasks)
├── start-dev.sh
├── stop-dev.sh
└── setup.sh
```

## Docker Services

- `bagisto-mysql` — MySQL on port 3306 (user: bagisto, pass: bagisto123, db: bagisto)
- `bagisto-redis` — Redis on port 6379
- `bagisto-mailpit` — Email catcher, web UI http://localhost:8025, SMTP port 1025
- Data persisted via Docker volume `bagisto_mysql_data`

## Test Accounts

- **Admin**: ahmad13faizan@gmail.com / admin123 (at /admin)
- **Customer**: ahmad13faizan@gmail.com (verified) — also test@example.com / password123

## PHP Version — IMPORTANT

- Project now requires **PHP 8.3+** (B2B Suite requirement). Both 8.2 and 8.3 are installed on this machine; 8.3 is what the project uses.
- **Always use `php8.3` explicitly**, not the bare `php` command (which may resolve to 8.2 depending on `update-alternatives`):
  - `php8.3 artisan serve --port=8001`
  - `php8.3 /usr/local/bin/composer <command>`
- If `start-dev.sh` / `share-qa.sh` still reference `php artisan serve` (not `php8.3`), update them or run manually with `php8.3`.

## Key Scripts

- `./start-dev.sh` — start Docker services (mysql, redis, mailpit)
- `./stop-dev.sh` — stop Docker services (data preserved)
- `./share-qa.sh` — start everything + Cloudflare tunnel for public QA access
- `./setup.sh` — full first-time install (only needed once)

## Tech Stack

- PHP 8.2, Laravel 11, Bagisto 2.x
- MySQL 8 (Docker), Redis 7 (Docker)
- Node 22, NPM 11
- Ubuntu 24.04

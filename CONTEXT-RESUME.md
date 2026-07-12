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
- [ ] SMS/WhatsApp notification integration (Gupshup/MSG91)
- [ ] Shipping connector (Shiprocket/Delhivery)
- [ ] OTP/phone auth module (specced separately later)
- [ ] Configure B2B Suite details further: quotation prefixes, company catalogs/pricing tiers, sales reps, company attributes, email notification toggles (Admin Panel → Configure → B2B Suite)
- [ ] Test full company registration → admin approval → login flow end-to-end
- [ ] Change admin password from admin123 before any external sharing
- [ ] (Optional) Show gst_number on admin customer VIEW page (currently only on create/edit forms)

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

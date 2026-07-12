# Technical Overview — B2B + B2C Commerce Platform

---

## 1. Technology Stack (What Runs the System)

```
┌─────────────────────────────────────────────────────────────┐
│                        BROWSER                               │
│            (Customer / Retailer / Admin)                      │
└──────────────────────────┬──────────────────────────────────┘
                           │ HTTP requests
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                     NGINX (Web Server)                        │
│              Receives all incoming traffic                    │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                   LARAVEL (PHP Framework)                     │
│                                                              │
│  ┌─────────────────────────────────────────────────────┐    │
│  │              BAGISTO (Ecommerce App)                  │    │
│  │                                                      │    │
│  │  ┌──────────────┐  ┌──────────────┐                 │    │
│  │  │ Core Store   │  │ B2B Suite    │                  │    │
│  │  │ (B2C stuff)  │  │ (Dealer      │                  │    │
│  │  │              │  │  features)   │                  │    │
│  │  └──────────────┘  └──────────────┘                 │    │
│  │                                                      │    │
│  │  ┌──────────────┐  ┌──────────────┐                 │    │
│  │  │ Samples      │  │ Site Visits  │  ← CUSTOM       │    │
│  │  │ Module       │  │ Module       │    MODULES       │    │
│  │  └──────────────┘  └──────────────┘                 │    │
│  └─────────────────────────────────────────────────────┘    │
└────────────┬───────────────────────────────┬────────────────┘
             │                               │
             ▼                               ▼
┌────────────────────────┐     ┌────────────────────────┐
│   MySQL 8 (Database)   │     │   Redis (Cache +       │
│                        │     │   Sessions + Queues)   │
│ - Products             │     │                        │
│ - Orders               │     │ - Page caching         │
│ - Customers            │     │ - Session storage      │
│ - Retailers            │     │ - Background jobs      │
│ - Samples              │     │   (emails, SMS)        │
│ - Site visits          │     │                        │
└────────────────────────┘     └────────────────────────┘
```

**In simple words:**

1. User opens website in browser
2. Nginx receives the request
3. Laravel/Bagisto processes it (figures out what to show, checks prices, handles orders)
4. MySQL stores all the data permanently
5. Redis makes things fast (caching) and handles background jobs (sending emails)

---

## 2. Server Setup

```
┌─────────────────────────────────────────────────┐
│           SINGLE VPS (Virtual Server)            │
│                                                  │
│  OS: Ubuntu 22.04                                │
│  CPU: 2 vCPU                                     │
│  RAM: 4 GB                                       │
│  Disk: 60-80 GB SSD                              │
│  Cost: ~$15-24/month                             │
│                                                  │
│  Running:                                        │
│  ├── Nginx (web server)                          │
│  ├── PHP 8.3 + Laravel + Bagisto                 │
│  ├── MySQL 8 (database)                          │
│  ├── Redis (cache)                               │
│  └── Supervisor (keeps background jobs running)  │
│                                                  │
│  NOT running (not needed at this scale):         │
│  ✗ Elasticsearch (overkill for 1000 products)    │
│  ✗ Load balancer (only 100 users)                │
│  ✗ Multiple servers (single box is enough)       │
│                                                  │
└─────────────────────────────────────────────────┘
```

**Why this is enough:**

- 100 users don't hit the site at the same time
- 1,000 products is tiny — MySQL handles search fine without Elasticsearch
- If traffic grows 10x later, just upgrade the VPS (more RAM/CPU, same box)

---

## 3. How a Page Request Works (Technical Flow)

**Example: Customer opens a product page**

```
Step 1: Browser sends request
         GET https://yourstore.com/products/led-panel-light

Step 2: Nginx receives it
         → Passes to PHP-FPM (PHP processor)

Step 3: Laravel Router
         → Matches URL to ProductController

Step 4: Controller Logic
         → Checks: Is user logged in?
         → Checks: Is user B2B or B2C?
         → Fetches product from MySQL
         → Calculates correct price based on user type

Step 5: View Rendering
         → Blade template generates HTML
         → Shows retail price OR dealer price

Step 6: Response sent back
         → HTML page arrives in browser
         → User sees the product with their price
```

```
 Browser          Nginx         PHP/Laravel        MySQL         Redis
    │                │               │               │             │
    │── GET /product─▶│               │               │             │
    │                │──forward──────▶│               │             │
    │                │               │──check cache──▶│             │
    │                │               │◀──cache miss───│             │
    │                │               │──SELECT * ─────▶│             │
    │                │               │◀──product data──│             │
    │                │               │──store cache───▶│             │
    │                │◀──HTML────────│               │             │
    │◀──page─────────│               │               │             │
    │                │               │               │             │
```

---

## 4. How Checkout Works (Technical Flow)

```
 Customer           Bagisto              MySQL           Razorpay       Redis/Queue
    │                  │                   │                │               │
    │─ Click Checkout ─▶│                   │                │               │
    │                  │─ Validate cart ────▶│                │               │
    │                  │◀─ Cart items OK ───│                │               │
    │                  │─ Calculate total ──▶│                │               │
    │                  │  (apply pricing    │                │               │
    │                  │   rules, coupons)  │                │               │
    │                  │◀─ Final amount ────│                │               │
    │◀─ Show payment ──│                   │                │               │
    │                  │                   │                │               │
    │─ Pay ₹5,000 ────▶│                   │                │               │
    │                  │─── Create order ───▶│                │               │
    │                  │─── Charge card ────────────────────▶│               │
    │                  │◀── Payment OK ─────────────────────│               │
    │                  │─── Update order ───▶│                │               │
    │                  │    status=PAID     │                │               │
    │                  │─── Queue email ────────────────────────────────────▶│
    │                  │─── Queue SMS ──────────────────────────────────────▶│
    │◀─ Order Confirmed│                   │                │               │
    │                  │                   │                │               │
```

**What happens behind the scenes:**

1. Cart items validated against current stock
2. Price calculated based on user type (B2C retail or B2B dealer)
3. Any coupons/discounts applied
4. Payment processed via Razorpay
5. Order saved to database with status "Paid"
6. Confirmation email + SMS queued (sent in background, doesn't slow down the page)
7. Inventory decremented

---

## 5. B2B Quote Flow (Technical)

```
 Retailer           Bagisto              MySQL              Admin
    │                  │                   │                   │
    │─ Request Quote ──▶│                   │                   │
    │  (products +     │─ Save quote ──────▶│                   │
    │   quantities)    │  status=PENDING    │                   │
    │                  │─ Notify admin ─────────────────────────▶│
    │◀─ "Quote sent" ──│                   │                   │
    │                  │                   │                   │
    │                  │                   │       Admin reviews│
    │                  │◀──────────────────────── Update price ─│
    │                  │─ Update quote ────▶│                   │
    │                  │  status=NEGOTIATING│                   │
    │◀─ "New offer" ───│                   │                   │
    │                  │                   │                   │
    │─ Accept offer ──▶│                   │                   │
    │                  │─ Convert to ──────▶│                   │
    │                  │  order             │                   │
    │                  │  status=APPROVED   │                   │
    │◀─ "Order ready" ─│                   │                   │
    │                  │                   │                   │
```

**Quote statuses:** `PENDING → NEGOTIATING → APPROVED → CONVERTED TO ORDER`

---

## 6. Database Structure (Main Tables)

```
┌─────────────────────────────────────────────────────────────────┐
│                    DATABASE (MySQL 8)                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  EXISTING (Bagisto built-in):                                    │
│  ┌──────────────┐ ┌───────────────┐ ┌───────────────────┐       │
│  │ products     │ │ customers     │ │ orders            │       │
│  │ - name       │ │ - name        │ │ - customer_id     │       │
│  │ - price      │ │ - email       │ │ - total           │       │
│  │ - sku        │ │ - phone       │ │ - status          │       │
│  │ - stock      │ │ - type(b2c/b2b│ │ - payment_method  │       │
│  │ - category   │ │ - company_id  │ │ - shipping_addr   │       │
│  └──────────────┘ └───────────────┘ └───────────────────┘       │
│                                                                  │
│  ┌──────────────┐ ┌───────────────┐ ┌───────────────────┐       │
│  │ companies    │ │ price_lists   │ │ quotes            │       │
│  │ (B2B Suite)  │ │ (B2B Suite)   │ │ (B2B Suite)       │       │
│  │ - name       │ │ - company_id  │ │ - company_id      │       │
│  │ - gst_number │ │ - product_id  │ │ - items           │       │
│  │ - status     │ │ - custom_price│ │ - status          │       │
│  └──────────────┘ └───────────────┘ └───────────────────┘       │
│                                                                  │
│  CUSTOM (We build these):                                        │
│  ┌───────────────────────────┐  ┌────────────────────────────┐  │
│  │ sample_requests           │  │ site_visits                │  │
│  │ - id                      │  │ - id                       │  │
│  │ - customer_id             │  │ - customer_id              │  │
│  │ - product_id              │  │ - product_interest         │  │
│  │ - quantity                │  │ - address                  │  │
│  │ - status (pending/        │  │ - preferred_date           │  │
│  │   approved/rejected/      │  │ - assigned_rep_id          │  │
│  │   shipped/delivered)      │  │ - status (requested/       │  │
│  │ - admin_notes             │  │   assigned/scheduled/      │  │
│  │ - created_at              │  │   completed)               │  │
│  └───────────────────────────┘  │ - admin_notes              │  │
│                                  │ - created_at               │  │
│  ┌───────────────────────────┐  └────────────────────────────┘  │
│  │ sample_inventory          │                                   │
│  │ - product_id              │                                   │
│  │ - sample_stock            │                                   │
│  │ - allocated               │                                   │
│  └───────────────────────────┘                                   │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 7. How Custom Modules Plug Into Bagisto

Bagisto uses Laravel's "package" system. Custom modules sit alongside the core without modifying it.

```
your-store/
├── app/                          ← Bagisto core (DON'T TOUCH)
├── packages/
│   └── YourAgency/
│       ├── SampleRequest/        ← CUSTOM: Samples module
│       │   ├── src/
│       │   │   ├── Models/       (database models)
│       │   │   ├── Http/
│       │   │   │   ├── Controllers/
│       │   │   │   └── Routes/
│       │   │   ├── Database/
│       │   │   │   └── Migrations/
│       │   │   ├── Resources/
│       │   │   │   └── views/    (admin + storefront pages)
│       │   │   └── Providers/
│       │   │       └── ServiceProvider.php
│       │   └── composer.json
│       │
│       └── SiteVisit/            ← CUSTOM: Site visits module
│           ├── src/
│           │   ├── Models/
│           │   ├── Http/
│           │   ├── Database/
│           │   ├── Resources/
│           │   └── Providers/
│           └── composer.json
│
├── config/
├── database/
├── public/
├── vendor/                       ← All dependencies (Bagisto, B2B Suite, etc.)
└── composer.json
```

**How it works:**

1. Each custom module is a Laravel package
2. It registers itself via a ServiceProvider
3. It adds its own routes, controllers, views, and database tables
4. Bagisto core stays untouched — upgrades won't break your custom code

---

## 8. Background Jobs (Things That Run Behind the Scenes)

```
┌────────────────────────────────────────────────────────┐
│                QUEUE SYSTEM (Redis)                      │
├────────────────────────────────────────────────────────┤
│                                                         │
│  Jobs that run in background (don't slow down pages):   │
│                                                         │
│  📧 Send order confirmation email                       │
│  📱 Send SMS notification                               │
│  📱 Send WhatsApp message                               │
│  📊 Update analytics/reports                            │
│  🔔 Notify admin of new sample request                  │
│  🔔 Notify sales rep of assigned visit                  │
│  📦 Sync inventory with shipping provider               │
│                                                         │
│  Processed by: Supervisor (keeps workers running)       │
│  If a job fails: retried 3 times, then logged           │
│                                                         │
└────────────────────────────────────────────────────────┘
```

**Why this matters:**
When a customer places an order, you don't want them waiting 5 seconds while the system sends emails and SMS. Instead, those tasks get "queued" and processed in the background immediately after.

---

## 9. External Services (Third-Party APIs)

```
┌──────────────────────────────────────────────────────────────┐
│                     YOUR SERVER                                │
│                                                               │
│                    Bagisto App                                 │
│                        │                                      │
│         ┌──────────────┼──────────────────┐                   │
│         │              │                  │                   │
│         ▼              ▼                  ▼                   │
│  ┌────────────┐ ┌────────────┐  ┌──────────────┐             │
│  │  Razorpay  │ │  MSG91 /   │  │  Shiprocket  │             │
│  │  (Payments)│ │  Gupshup   │  │  (Shipping)  │             │
│  │            │ │  (SMS/WA)  │  │              │             │
│  └────────────┘ └────────────┘  └──────────────┘             │
│                                                               │
│  How they connect:                                            │
│  - Razorpay: Customer pays → Razorpay processes → confirms   │
│  - SMS/WA: Order placed → system queues message → API sends  │
│  - Shipping: Order ready → system creates shipment → tracks  │
│                                                               │
└──────────────────────────────────────────────────────────────┘
```

**Cost of these services:**
| Service | Cost |
|---------|------|
| Razorpay | 2% per transaction (no monthly fee) |
| MSG91 SMS | ~₹0.15-0.25 per SMS |
| WhatsApp Business | ~₹0.50-1.00 per message |
| Shiprocket | Plans from ₹0 (pay per shipment) |

---

## 10. Deployment Flow (How Code Goes Live)

```
  Developer's Computer              Git Repository              Live Server
  ┌──────────────────┐           ┌──────────────────┐       ┌──────────────────┐
  │                  │           │                  │       │                  │
  │  Write code      │──push───▶│  GitHub/GitLab   │       │                  │
  │  Test locally    │           │  (stores code)   │       │                  │
  │                  │           │                  │       │                  │
  └──────────────────┘           └────────┬─────────┘       │  Your VPS        │
                                          │                 │                  │
                                          │──pull/deploy───▶│  Runs the store  │
                                          │                 │                  │
                                                            └──────────────────┘
```

**Deployment steps (simple):**

1. Developer writes and tests code locally
2. Pushes to Git repository
3. SSH into server → `git pull` → `composer install` → `php artisan migrate`
4. Done. Changes are live.

(For now, no CI/CD pipeline needed. Manual deploy is fine at this scale.)

---

## 11. Security Measures

```
┌────────────────────────────────────────────────────────┐
│                  SECURITY LAYERS                         │
├────────────────────────────────────────────────────────┤
│                                                         │
│  🔒 HTTPS (SSL certificate) — encrypts all traffic     │
│  🔒 Laravel CSRF protection — prevents fake form posts │
│  🔒 Password hashing (bcrypt) — passwords never stored │
│     in plain text                                       │
│  🔒 Role-based access — admin vs customer vs retailer  │
│  🔒 Input validation — prevents SQL injection, XSS     │
│  🔒 Rate limiting — prevents brute-force attacks       │
│  🔒 Regular backups — daily automated MySQL dump       │
│  🔒 Firewall (UFW) — only ports 80, 443, 22 open      │
│                                                         │
└────────────────────────────────────────────────────────┘
```

---

## 12. Backup Strategy

```
  DAILY (automated):
  ┌──────────────┐      ┌───────────────────────┐
  │  MySQL dump  │─────▶│  Stored offsite        │
  │  (all data)  │      │  (e.g., S3 or another  │
  └──────────────┘      │  server)               │
                        └───────────────────────┘

  WEEKLY:
  ┌──────────────┐      ┌───────────────────────┐
  │  Full server │─────▶│  VPS snapshot          │
  │  snapshot    │      │  (one-click restore)   │
  └──────────────┘      └───────────────────────┘
```

**Recovery:** If anything breaks, restore from last night's backup. Maximum data loss = 1 day.

---

## 13. Scaling Plan (If You Grow)

```
  NOW (100 users, 1000 products):
  ┌─────────────────────────┐
  │  Single VPS (4GB RAM)   │  ← You are here
  └─────────────────────────┘

  LATER (500-1000 users):
  ┌─────────────────────────┐
  │  Bigger VPS (8GB RAM)   │  ← Just upgrade same box
  │  + Add Elasticsearch    │
  └─────────────────────────┘

  MUCH LATER (5000+ users):
  ┌──────────┐  ┌──────────┐  ┌──────────┐
  │  App     │  │  App     │  │ Database │  ← Separate DB
  │  Server  │  │  Server  │  │ Server   │     + multiple app servers
  └──────────┘  └──────────┘  └──────────┘
       │              │
       ▼              ▼
  ┌──────────────────────────┐
  │    Load Balancer          │  ← Distributes traffic
  └──────────────────────────┘
```

**You don't need to plan for "much later" now.** Just know the path exists.

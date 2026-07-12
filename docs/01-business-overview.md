# Business Overview — B2B + B2C Commerce Platform

---

## 1. What Is This System?

A single online store that serves TWO types of buyers:

| Type                       | Who             | How They Buy                                                                            |
| -------------------------- | --------------- | --------------------------------------------------------------------------------------- |
| **B2C (Regular Customer)** | Any person      | Browse → Add to Cart → Register → Pay → Done                                            |
| **B2B (Retailer/Dealer)**  | Business buyers | Admin creates their account → They get special pricing + bulk ordering + quote requests |

Both share the same product catalog and inventory.
They see DIFFERENT prices based on who they are.

---

## 2. Who Uses the System?

```
┌─────────────────────────────────────────────────────────┐
│                    THE PLATFORM                          │
├─────────────┬──────────────┬─────────────┬──────────────┤
│  B2C        │  B2B         │  Admin      │  Sales Rep   │
│  Customer   │  Retailer    │             │              │
├─────────────┼──────────────┼─────────────┼──────────────┤
│ Self-       │ Admin        │ Full        │ Gets         │
│ registers   │ creates      │ backend     │ assigned     │
│             │ their ID     │ access      │ site visits  │
├─────────────┼──────────────┼─────────────┼──────────────┤
│ Buys at     │ Buys at      │ Manages     │ Completes    │
│ retail      │ dealer       │ everything  │ demos        │
│ price       │ price        │             │              │
└─────────────┴──────────────┴─────────────┴──────────────┘
```

---

## 3. B2C Customer Flow (Regular Buyer)

This is a simple shopping experience — like any online store.

```
  ┌──────────────────┐
  │ Visit the Store  │
  └────────┬─────────┘
           │
           ▼
  ┌──────────────────┐
  │ Browse Products  │  ← Sees RETAIL prices only
  │ (No login needed)│
  └────────┬─────────┘
           │
           ▼
  ┌──────────────────┐
  │ Add to Cart      │  ← Can do this as guest
  └────────┬─────────┘
           │
           ▼
  ┌──────────────────┐
  │ Want to Checkout?│
  │ Must Login/      │  ← FORCED to register here
  │ Register         │
  └────────┬─────────┘
           │
           ▼
  ┌──────────────────┐
  │ Choose Shipping  │
  │ Choose Payment   │  ← Razorpay / UPI / COD
  └────────┬─────────┘
           │
           ▼
  ┌──────────────────┐
  │ Order Confirmed  │  ← Gets email/SMS confirmation
  └────────┬─────────┘
           │
           ▼
  ┌──────────────────┐
  │ Track Order      │  ← Until delivered
  └──────────────────┘
```

**Rules for B2C:**

- Can browse and add to cart WITHOUT logging in
- MUST register/login to complete checkout
- Only sees retail prices (never dealer prices)
- Can use coupons and promo codes
- Can request product samples
- Can request site visits / demos

---

## 4. B2B Retailer Flow (Dealer/Business Buyer)

Retailers get special treatment — better prices, bulk ordering, and quote negotiation.

```
  ┌──────────────────────────┐
  │ Admin creates retailer   │  ← No self-registration
  │ account with GST + info  │
  └────────────┬─────────────┘
               │
               ▼
  ┌──────────────────────────┐
  │ Retailer gets login      │
  │ credentials              │
  └────────────┬─────────────┘
               │
               ▼
  ┌──────────────────────────┐
  │ Logs in → Dealer         │  ← Sees DEALER prices
  │ Dashboard                │
  └────────────┬─────────────┘
               │
       ┌───────┼───────────┐
       │       │           │
       ▼       ▼           ▼
  ┌────────┐ ┌─────────┐ ┌──────────────┐
  │ Direct │ │  Bulk   │ │ Request a    │
  │ Order  │ │  Order  │ │ Quote        │
  └───┬────┘ └────┬────┘ └──────┬───────┘
      │           │              │
      │           │              ▼
      │           │     ┌──────────────────┐
      │           │     │ Admin reviews    │
      │           │     └────────┬─────────┘
      │           │              │
      │           │         ┌────┴────┐
      │           │         │         │
      │           │         ▼         ▼
      │           │     ┌───────┐ ┌────────────┐
      │           │     │Approve│ │ Negotiate  │
      │           │     └───┬───┘ │ (back &    │
      │           │         │     │  forth)    │
      │           │         │     └─────┬──────┘
      │           │         │           │
      ▼           ▼         ▼           ▼
  ┌──────────────────────────────────────────┐
  │         ORDER PLACED ✓                   │
  └──────────────────────────────────────────┘
```

**Three ways a retailer can buy:**

| Method            | What It Means                                               |
| ----------------- | ----------------------------------------------------------- |
| **Direct Order**  | Add to cart → checkout → pay (like B2C but at dealer price) |
| **Bulk Order**    | Upload a list of SKUs + quantities at once                  |
| **Request Quote** | Ask admin for a price → negotiate → finalize → order        |

---

## 5. How Pricing Works

```
  ┌─────────────────────────┐
  │  Someone views a product │
  └────────────┬─────────────┘
               │
               ▼
        ┌──────────────┐
        │ Who is this? │
        └──────┬───────┘
               │
       ┌───────┴────────┐
       │                │
       ▼                ▼
  ┌──────────┐   ┌──────────────────────────────┐
  │ Regular  │   │ Retailer? Check price lists: │
  │ Customer │   │                              │
  │          │   │  1. Individual negotiated     │ ← Highest priority
  │ → Retail │   │  2. Product-specific price    │
  │   Price  │   │  3. Category-level discount   │
  │          │   │  4. Fallback to retail        │
  └──────────┘   │                              │
                 │  → Most specific price wins   │
                 └──────────────────────────────┘
```

**Example:**

- Retail price: ₹1,000
- Category discount for "Gold retailers": 20% off → ₹800
- Product-specific price for this retailer: ₹750
- Individually negotiated: ₹700 ← THIS WINS (most specific)

---

## 6. Product Samples Flow

Customers or retailers can request a free sample before buying in bulk.

```
  ┌──────────────────────────┐
  │ Customer/Retailer views  │
  │ a product                │
  └────────────┬─────────────┘
               │
               ▼
  ┌──────────────────────────┐
  │ Clicks "Request Sample"  │
  │ Fills form (quantity,    │
  │ shipping address)        │
  └────────────┬─────────────┘
               │
               ▼
  ┌──────────────────────────┐
  │ Admin gets notification  │
  │ Reviews the request      │
  └────────────┬─────────────┘
               │
       ┌───────┴───────┐
       │               │
       ▼               ▼
  ┌──────────┐   ┌──────────────┐
  │ APPROVE  │   │   REJECT     │
  └────┬─────┘   └──────┬───────┘
       │                 │
       ▼                 ▼
  ┌──────────────┐  ┌──────────────────┐
  │ Sample taken │  │ Customer gets    │
  │ from SAMPLE  │  │ "Sorry" message  │
  │ inventory    │  └──────────────────┘
  └──────┬───────┘
         │
         ▼
  ┌──────────────────┐
  │ Sample shipped   │
  │ & delivered ✓    │
  └──────────────────┘
```

**Key point:** Sample inventory is tracked SEPARATELY from selling inventory.
Admin can set limits (e.g., max 2 samples per customer per product).

---

## 7. Site Visits / Demo Flow

Customer wants someone to come to their location for a demo.

```
  ┌────────────────────────────────────┐
  │ Customer/Retailer requests visit   │
  │ (location, date, product interest) │
  └───────────────┬────────────────────┘
                  │
                  ▼
  ┌────────────────────────────────────┐
  │ Admin sees request                 │
  │ Assigns a Sales Rep               │
  └───────────────┬────────────────────┘
                  │
                  ▼
  ┌────────────────────────────────────┐
  │ Sales Rep gets notified            │
  │ Visit is scheduled                 │
  └───────────────┬────────────────────┘
                  │
                  ▼
  ┌────────────────────────────────────┐
  │ Rep visits → Does demo             │
  │ Marks as COMPLETED ✓               │
  └────────────────────────────────────┘
```

**Status flow:** `Requested → Assigned → Scheduled → Completed`

---

## 8. What Admin Does Every Day

```
  ┌─────────────────── ADMIN PANEL ───────────────────────┐
  │                                                        │
  │  📦 Products & Inventory                               │
  │     • Add/edit/delete products                         │
  │     • Manage stock levels                              │
  │     • Bulk import via CSV                              │
  │                                                        │
  │  👥 Retailer Management                                │
  │     • Create new retailer accounts                     │
  │     • Set their price lists (category/product/custom)  │
  │     • Enable/disable accounts                          │
  │                                                        │
  │  🛒 Orders                                             │
  │     • View all orders (B2C + B2B)                      │
  │     • Update order status                              │
  │     • Process returns/refunds                          │
  │                                                        │
  │  💬 Quotations (B2B only)                              │
  │     • Review quote requests                            │
  │     • Negotiate pricing                                │
  │     • Convert approved quotes to orders                │
  │                                                        │
  │  🧪 Sample Requests                                    │
  │     • Approve/reject sample requests                   │
  │     • Track sample inventory                           │
  │                                                        │
  │  🏠 Site Visits                                        │
  │     • Assign sales reps to visits                      │
  │     • Track visit status                               │
  │                                                        │
  │  📊 Reports                                            │
  │     • Order & revenue reports                          │
  │     • Product performance                              │
  │     • Retailer purchase history                        │
  │                                                        │
  └────────────────────────────────────────────────────────┘
```

---

## 9. What's Already Built vs What We Build

| Feature                         | Status          | Work Needed    |
| ------------------------------- | --------------- | -------------- |
| Product catalog, cart, checkout | ✅ Built-in     | Just configure |
| B2B accounts + dealer dashboard | ✅ Built-in     | Just configure |
| Quote negotiation workflow      | ✅ Built-in     | Just configure |
| Promotions & coupons            | ✅ Built-in     | Just configure |
| Razorpay / UPI payments         | ✅ Built-in     | Add API keys   |
| Inventory management            | ✅ Built-in     | Just configure |
| GST field on retailer form      | 🔧 Small tweak  | ~1 day         |
| SMS/WhatsApp notifications      | 🔧 Integration  | ~2-3 days      |
| Shipping (Shiprocket)           | 🔧 Integration  | ~2-3 days      |
| Site visits module              | 🛠️ Custom build | ~1-2 weeks     |
| Product samples module          | 🛠️ Custom build | ~2-3 weeks     |
| Analytics dashboard (Metabase)  | 🔧 Integration  | ~2-3 days      |

**Bottom line: ~80% is already done. We only custom-build the samples and site visits modules.**

---

## 10. Cost Summary

| Item                    | Cost                     |
| ----------------------- | ------------------------ |
| Bagisto + B2B Suite     | FREE (open source)       |
| Hosting (VPS)           | ~₹1,200-2,000/month      |
| Payment gateway         | Per-transaction fee only |
| Domain + SSL            | ~₹1,000/year             |
| SMS/WhatsApp service    | Pay per message          |
| **Total fixed monthly** | **~₹1,500-2,500/month**  |

No licensing fees. No subscriptions. All software is free.

# Entity Relationship Diagram (ERD) - PreminAja Marketplace

Dokumen ini memuat skema final basis data PreminAja setelah pembaruan Tahap 2 & Tahap 3, mencakup seluruh tabel, tipe data, indeks penting, dan relasi antar-entitas.

---

## 1. Hubungan Antara Tabel `tickets` dan `disputes`

PreminAja membedakan secara tegas antara **Layanan Pelanggan Umum (`tickets`)** dan **Mediasi Transaksi/Escrow (`disputes`)**:

```
+----------------------------------------------------------------------------------------+
|                                    TICKETS vs DISPUTES                                 |
+---------------------------------------+------------------------------------------------+
| Tickets (Customer Support)            | Disputes (Mediasi Transaksi & Escrow)          |
+---------------------------------------+------------------------------------------------+
| - Tujuan: Layanan umum CS / Q&A /     | - Tujuan: Komplain resmi buyer atas pesanan    |
|   bantuan teknis pembeli/penjual      |   yang bermasalah (akun invalid, garansi macet)|
| - Relasi: users (1:N)                 | - Relasi: Wajib mengikat order_items & buyer   |
|   (order_id bersifat opsional)        | - Efek Finansial: Menahan pencairan dana       |
| - Format: Thread obrolan interaktif   |   escrow seller hingga diputuskan admin        |
|   multi-pesan (ticket_messages)       | - Status: open -> investigating ->             |
| - Ditangani oleh: CS / Tim Support    |   resolved_buyer / resolved_seller / closed    |
|                                       | - Ditangani oleh: Superadmin / Arbiter Finansial|
+---------------------------------------+------------------------------------------------+
```

Kedua tabel ini **sengaja dipisahkan** karena:
1. `tickets` digunakan untuk konsultasi pra-pembelian, panduan pemakaian akun, keluhan bug platform, atau pertanyaan umum seputar toko yang tidak berdampak langsung membekukan saldo.
2. `disputes` adalah proses arbitrase hukum/keuangan formal yang mengunci pelepasan dana item pesanan tertentu (`order_items.escrow_release_at`), memungkinkan investigasi bukti antara pembeli dan penjual, serta memicu pencairan otomatis atau *refund balance* ke pembeli.

---

## 2. Mermaid ERD Diagram

```mermaid
erDiagram
    users ||--o| seller_profiles : "has one"
    users ||--o{ orders : "places"
    users ||--o{ reviews : "writes"
    users ||--o{ tickets : "opens"
    users ||--o{ ticket_messages : "sends"
    users ||--o{ disputes : "files"
    users ||--o{ cart_items : "adds"
    users ||--o{ wishlists : "bookmarks"

    seller_profiles ||--o{ products : "manages"
    seller_profiles ||--o{ order_items : "fulfills"
    seller_profiles ||--o| seller_balances : "owns"
    seller_profiles ||--o{ wallet_transactions : "logs"
    seller_profiles ||--o{ withdrawals : "requests"

    categories ||--o{ categories : "sub-category of"
    categories ||--o{ products : "classifies"

    products ||--o{ product_variants : "has"
    products ||--o{ reviews : "receives"
    products ||--o{ wishlists : "saved in"

    product_variants ||--o{ automated_credentials : "has stock"
    product_variants ||--o{ order_items : "ordered as"
    product_variants ||--o{ cart_items : "in cart"

    orders ||--o{ order_items : "contains"
    orders ||--o| payments : "paid via"
    orders ||--o{ tickets : "associated with"
    vouchers ||--o{ orders : "discounts"

    order_items ||--o| reviews : "reviewed in"
    order_items ||--o| automated_credentials : "delivered credential"
    order_items ||--o{ disputes : "disputed in"

    tickets ||--o{ ticket_messages : "contains messages"

    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string role "superadmin | seller | buyer"
        string phone
        string avatar_url
        boolean is_active
        timestamp last_login_at
        timestamps timestamps
    }

    seller_profiles {
        bigint id PK
        bigint user_id FK, UK
        string store_name
        string store_slug UK
        text bio
        string banner_url
        string bank_name
        text bank_account_number "encrypted"
        text bank_account_holder "encrypted"
        string verification_status "pending | approved | rejected"
        text verification_notes
        decimal rating_avg "3,2"
        int rating_count
        timestamps timestamps
    }

    categories {
        bigint id PK
        string name
        string slug UK
        string icon
        text description
        bigint parent_id FK, nullable
        boolean is_active
        timestamps timestamps
    }

    products {
        bigint id PK
        bigint seller_id FK
        bigint category_id FK
        string name
        string slug UK
        text description
        string delivery_type "automated | manual"
        string status "draft | pending_review | published | rejected | inactive | archived"
        text rejection_reason "nullable"
        string featured_image
        decimal rating_avg "3,2"
        int rating_count
        int sold_count
        timestamps timestamps
    }

    product_variants {
        bigint id PK
        bigint product_id FK
        string name
        decimal price "15,2"
        decimal original_price "15,2"
        int duration_days
        int stock
        boolean is_active
        timestamps timestamps
    }

    automated_credentials {
        bigint id PK
        bigint product_variant_id FK
        text credential_data "encrypted"
        text instructions
        string status "available | reserved | delivered"
        bigint order_item_id FK, nullable
        timestamps timestamps
    }

    vouchers {
        bigint id PK
        string code UK
        string discount_type "percentage | fixed"
        decimal discount_value "15,2"
        decimal min_purchase "15,2"
        decimal max_discount "15,2"
        int usage_limit
        int used_count
        timestamp starts_at
        timestamp expires_at
        boolean is_active
        timestamps timestamps
    }

    orders {
        bigint id PK
        string order_number UK
        bigint user_id FK
        decimal total_amount "15,2"
        decimal discount_amount "15,2"
        decimal fee_amount "15,2"
        decimal final_amount "15,2"
        string payment_method
        string payment_status "pending | paid | failed | expired"
        string order_status "pending | processing | completed | cancelled | disputed"
        bigint voucher_id FK, nullable
        text notes
        timestamp paid_at
        timestamp completed_at
        timestamp expires_at
        timestamps timestamps
    }

    order_items {
        bigint id PK
        bigint order_id FK
        bigint variant_id FK
        bigint seller_id FK
        int quantity
        decimal price "15,2"
        decimal subtotal "15,2"
        decimal platform_fee "15,2"
        decimal seller_earning "15,2"
        timestamp escrow_release_at
        string delivery_status "pending | delivered | failed"
        text credentials_payload "encrypted"
        string product_name_snapshot
        string variant_name_snapshot
        timestamp delivered_at
        timestamps timestamps
    }

    payments {
        bigint id PK
        bigint order_id FK
        string tripay_reference UK
        string merchant_ref UK
        string payment_method
        decimal amount "15,2"
        decimal fee "15,2"
        decimal total_amount "15,2"
        string status "UNPAID | PAID | FAILED | EXPIRED"
        text checkout_url
        text qr_url
        string pay_code
        json tripay_response
        timestamp paid_at
        timestamp expires_at
        timestamps timestamps
    }

    reviews {
        bigint id PK
        bigint order_item_id FK, UK
        bigint user_id FK
        bigint product_id FK
        tinyint rating "1..5"
        text comment
        text seller_reply
        timestamp seller_replied_at
        timestamps timestamps
    }

    tickets {
        bigint id PK
        string ticket_number UK
        bigint user_id FK
        bigint order_id FK, nullable
        string subject
        string category "order | technical | billing | general"
        string priority "low | medium | high"
        string status "open | replied | closed"
        timestamp closed_at
        timestamps timestamps
    }

    ticket_messages {
        bigint id PK
        bigint ticket_id FK
        bigint user_id FK
        text message
        string attachment_url
        timestamps timestamps
    }

    disputes {
        bigint id PK
        bigint order_item_id FK
        bigint buyer_id FK
        text reason
        string status "open | investigating | resolved_buyer | resolved_seller | closed"
        text resolution_note
        timestamps timestamps
    }

    seller_balances {
        bigint id PK
        bigint seller_id FK, UK
        decimal balance "15,2"
        decimal pending_balance "15,2"
        decimal total_withdrawn "15,2"
        timestamps timestamps
    }

    wallet_transactions {
        bigint id PK
        bigint seller_id FK
        string type "order_settlement | escrow_hold | escrow_release | platform_fee | withdrawal | refund | refund_deduction | adjustment"
        decimal amount "15,2"
        decimal balance_before "15,2"
        decimal balance_after "15,2"
        string reference_type
        bigint reference_id
        text description
        timestamps timestamps
    }

    withdrawals {
        bigint id PK
        string withdrawal_number UK
        bigint seller_id FK
        decimal amount "15,2"
        decimal fee "15,2"
        decimal net_amount "15,2"
        string bank_name
        text bank_account_number "encrypted"
        text bank_account_holder "encrypted"
        string status "pending | approved | rejected | processed"
        text proof_url
        text notes
        timestamp processed_at
        timestamps timestamps
    }

    cart_items {
        bigint id PK
        bigint user_id FK
        bigint variant_id FK
        int quantity
        timestamps timestamps
    }

    wishlists {
        bigint id PK
        bigint user_id FK
        bigint product_id FK
        timestamps timestamps
    }

    settings {
        bigint id PK
        string key UK
        text value
        string type "string | integer | decimal | boolean | json"
        string description
        timestamps timestamps
    }
```

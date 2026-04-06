# ZEROvia Sourcing & RFX Agent

> **ESG-ready Supplier Scouting & Request-for-Quotation Generator**
> Built for ZEROvia GmbH — zerovia.ch

---

## Overview

The ZEROvia Sourcing & RFX Agent is an AI-assisted procurement tool that helps SMEs find sustainable, ESG-qualified suppliers and generate professional RFQ documents in minutes.

**Core workflow (4 steps):**
1. **Bedarf** — Define procurement need via NOGA/NACE industry codes, description, volume, and search radius
2. **Bewertungskriterien** — Set scoring weights (Price, ESG, Delivery, Certifications, Quality)
3. **Shortlist** — View ranked supplier shortlist with ESG scores, certifications, and risk ratings
4. **RFx** — Auto-generated, editable RFQ document ready for email dispatch

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend Framework | Laravel 11 |
| Admin / UI | Filament 3 |
| Database | PostgreSQL (supplier data, RFQ archive) |
| Auth | Laravel Sanctum / Filament auth |
| Frontend Prototype | Vanilla HTML/CSS/JS (see `/public/supplier-agent-prototype.html`) |
| ESG Data Source | ZEROvia ESG Hub API (internal) |
| NOGA/NACE Data | Swiss BFS NOGA 2008 taxonomy |
| Email Dispatch | Laravel Mail + Mailgun |
| PDF Export | Laravel DomPDF |

---

## Repository Structure

```
zerovia-sourcing-rfx/
├── public/
│   └── supplier-agent-prototype.html   ← Interactive HTML prototype (start here)
├── resources/
│   └── views/                          ← Blade templates (Filament panels)
├── routes/
│   ├── web.php
│   └── api.php
├── database/
│   └── migrations/                     ← DB schema (see below)
├── docs/
│   ├── ARCHITECTURE.md                 ← System architecture & data flow
│   ├── API.md                          ← Internal API endpoints
│   ├── ESG_SCORING.md                  ← Scoring model documentation
│   └── NOGA_TAXONOMY.md               ← NOGA/NACE category mapping
├── .env.example
├── composer.json
└── README.md
```

---

## Getting Started

### Prerequisites

- PHP 8.3+
- Composer 2
- Node.js 20+ & npm
- PostgreSQL 15+

### Installation

```bash
# Clone repo
git clone https://github.com/zerovia/zerovia-sourcing-rfx.git
cd zerovia-sourcing-rfx

# Install PHP dependencies
composer install

# Install JS dependencies
npm install && npm run build

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed --class=SupplierSeeder
php artisan db:seed --class=NogaTaxonomySeeder

# Start development server
php artisan serve
```

### Prototype (no backend needed)

Open `public/supplier-agent-prototype.html` directly in any browser. No server required — all data is embedded.

---

## Database Schema

### `suppliers`
| Column | Type | Description |
|---|---|---|
| id | uuid | Primary key |
| name | varchar | Company name |
| country | char(2) | ISO 3166-1 alpha-2 |
| city | varchar | City |
| lat / lng | decimal | Geo coordinates |
| esg_score | smallint | 0–100 (EcoVadis-equivalent) |
| risk_level | enum | low / medium / high |
| noga_codes | jsonb | Array of NOGA codes |
| certifications | jsonb | Array of cert strings |
| website | varchar | Domain |
| created_at / updated_at | timestamp | — |

### `rfq_documents`
| Column | Type | Description |
|---|---|---|
| id | uuid | Primary key |
| reference_nr | varchar | e.g. ZEROvia-RFQ-2025-4821 |
| user_id | uuid | FK → users |
| supplier_ids | jsonb | Array of supplier UUIDs |
| noga_codes | jsonb | Selected codes |
| scoring_weights | jsonb | Price/ESG/Delivery/... weights |
| rfq_text | text | Full generated RFQ document |
| sent_at | timestamp | Nullable |
| created_at / updated_at | timestamp | — |

### `rfq_recipients`
| Column | Type | Description |
|---|---|---|
| id | uuid | Primary key |
| rfq_id | uuid | FK → rfq_documents |
| supplier_id | uuid | FK → suppliers |
| email | varchar | Recipient address |
| sent_at | timestamp | Nullable |
| opened_at | timestamp | Nullable (tracking) |

---

## Filament Panels

| Panel | Route | Description |
|---|---|---|
| Sourcing Wizard | `/sourcing/new` | 4-step procurement wizard |
| RFQ Archive | `/rfq` | All generated RFQs, status, resend |
| Supplier Database | `/suppliers` | Browse, filter, import suppliers |
| ESG Dashboard | `/esg` | Supplier ESG scores & trends |
| Settings | `/settings` | Scoring defaults, email templates |

---

## ESG Scoring Model

The supplier scoring algorithm weights five dimensions:

| Dimension | Default Weight | Source |
|---|---|---|
| Preis & Konditionen | 30% | User-configurable |
| ESG & Nachhaltigkeit | 25% | EcoVadis / VSME data |
| Lieferzuverlässigkeit | 20% | Supplier self-declaration |
| Zertifizierungen | 15% | Certificate upload |
| Qualität & Referenzen | 10% | Reference checks |

Full scoring methodology: see `docs/ESG_SCORING.md`

---

## Regulatory Alignment

- **OR 964b–j** (Swiss supply chain due diligence)
- **EU LKSG** (German Supply Chain Act)
- **CSRD / ESRS** (EU sustainability reporting)
- **VSME Standard** (SME-specific ESG reporting framework)

---

## Roadmap

- [ ] **v1.0** — Core wizard + RFQ generation + email dispatch
- [ ] **v1.1** — PDF export of RFQ documents
- [ ] **v1.2** — Supplier portal (supplier self-registration & ESG data upload)
- [ ] **v1.3** — AI-assisted requirement generation via Claude API
- [ ] **v2.0** — Real-time supplier ESG data sync from ZEROvia Hub

---

## Contributing

Internal ZEROvia project. Contact procurement@zerovia.app for access.

---

## License

Proprietary — ZEROvia GmbH. All rights reserved.

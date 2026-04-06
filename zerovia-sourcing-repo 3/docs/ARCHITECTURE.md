# System Architecture — ZEROvia Sourcing & RFX Agent

## Data Flow

```
User (Browser)
    │
    ▼
Filament Panel (Laravel)
    │
    ├──► NOGA/NACE Taxonomy DB ─────────────► Category suggestions
    │
    ├──► Supplier Database (PostgreSQL) ────► Geo-filtered, ESG-scored shortlist
    │       └── ESG Scores (ZEROvia Hub API)
    │
    ├──► Scoring Engine (PHP Service) ──────► Weighted ranking
    │
    ├──► RFQ Generator (PHP/Blade) ─────────► Structured RFQ document
    │
    └──► Email Dispatch (Laravel Mail) ─────► SMTP / Mailgun → Suppliers

```

## Key Services

### `SourcingService`
- Accepts procurement parameters (NOGA codes, location, radius, volume)
- Queries supplier database with geo-distance filter (Haversine formula)
- Applies ESG filter thresholds
- Returns ranked `SupplierCollection`

### `RfqGeneratorService`
- Takes `SupplierCollection` + `ScoringWeights` + procurement description
- Renders RFQ text via Blade template
- Stores `RfqDocument` in DB
- Returns reference number + full text

### `RfqDispatchService`
- Resolves recipient emails from supplier records
- Sends via Laravel Mail (queued)
- Updates `rfq_recipients.sent_at`
- Optionally tracks opens via pixel (v1.3+)

## API Endpoints (Internal)

```
POST /api/sourcing/search          ← supplier search
POST /api/sourcing/rfq/generate    ← generate RFQ document
POST /api/sourcing/rfq/send        ← dispatch RFQ emails
GET  /api/suppliers/{id}           ← supplier detail
GET  /api/noga/search?q=           ← NOGA taxonomy search
```

## Environment Variables

```env
APP_NAME="ZEROvia Sourcing Agent"
APP_URL=https://app.zerovia.ch

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=zerovia_sourcing
DB_USERNAME=zerovia
DB_PASSWORD=

ZEROVIA_HUB_API_KEY=         # Internal ESG Hub API key
ZEROVIA_HUB_BASE_URL=        # https://hub.zerovia.ch/api/v1

MAIL_MAILER=mailgun
MAILGUN_DOMAIN=zerovia.ch
MAILGUN_SECRET=

FILAMENT_FILESYSTEM_DISK=local
```

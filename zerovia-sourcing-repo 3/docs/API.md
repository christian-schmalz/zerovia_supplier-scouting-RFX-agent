# API Reference — ZEROvia Sourcing & RFX Agent

All API routes are prefixed with `/api` and require `Authorization: Bearer {token}` unless noted.

---

## Authentication

```bash
POST /api/sanctum/token
Content-Type: application/json

{ "email": "admin@zerovia.ch", "password": "zerovia2026!" }
```

Returns: `{ "token": "1|abc123..." }`

---

## NOGA Taxonomy

### Search categories

```
GET /api/noga/search?q=Verpackung
```

**Response:**
```json
[
  { "code": "C17.21", "section": "C", "de": "Herstellung von Wellpapier...", "fr": "Fabrication de carton ondulé..." },
  ...
]
```

---

## Supplier Search

### Search and rank suppliers

```
POST /api/sourcing/search
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "noga_codes":       ["C17", "C17.21"],
  "location":         "Zürich",
  "lat":              47.3769,
  "lng":              8.5417,
  "radius_km":        150,
  "min_esg":          60,
  "max_risk":         "mid",
  "require_iso14001": false,
  "top_n":            5,
  "scoring_weights": {
    "price":          30,
    "esg":            25,
    "delivery":       20,
    "certifications": 15,
    "quality":        10
  }
}
```

**Response:**
```json
{
  "count": 7,
  "suppliers": [
    {
      "id":             "uuid",
      "name":           "Mondi AG",
      "city":           "Wien",
      "country":        "AT",
      "esg_score":      87,
      "esg_category":   "Excellent",
      "risk_level":     "low",
      "risk_label":     "Niedrig",
      "certifications": ["ISO 9001", "ISO 14001", "FSC", "PEFC"],
      "distance_km":    312,
      "score":          84
    }
  ]
}
```

---

## RFQ Generation

### Generate RFQ document

```
POST /api/sourcing/rfq/generate
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:** Same as search + optional:
```json
{
  "description": "FSC-zertifizierte Kartonverpackungen für Lebensmittel",
  "volume":      350000,
  "supplier_ids": ["uuid1", "uuid2"]
}
```

**Response (201):**
```json
{
  "rfq_id":       "uuid",
  "reference_nr": "ZEROvia-RFQ-2026-4821",
  "rfq_text":     "ANGEBOTSANFORDERUNG (Request for Quotation)...",
  "recipients": [
    { "supplier": "Mondi AG", "email": "procurement@mondigroup.com" }
  ]
}
```

---

## RFQ Dispatch

### Send RFQ emails to suppliers

```
POST /api/sourcing/rfq/{rfq_id}/send
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "intro_text": "Sehr geehrte Damen und Herren, im Rahmen..."
}
```

**Response:**
```json
{
  "sent":    3,
  "message": "3 Empfänger erfolgreich angeschrieben."
}
```

---

## Open Tracking (no auth)

```
GET /rfq/track/{rfq_id}/{recipient_id}
```

Returns a 1×1 transparent GIF and updates `opened_at` timestamp.

---

## Error Responses

| Code | Meaning |
|------|---------|
| 401  | Unauthenticated — missing or invalid Bearer token |
| 422  | Validation error — see `errors` object in response |
| 404  | Resource not found |
| 500  | Server error |

```json
{
  "message": "The noga_codes field must be an array.",
  "errors": {
    "noga_codes": ["The noga_codes field must be an array."]
  }
}
```

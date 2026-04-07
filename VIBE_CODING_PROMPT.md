# ZEROvia Supplier Scouting & RFX Agent — Full App Prompt

## Overview
Build a **Supplier Scouting & RFQ Generation App** for sustainable procurement. The app helps SMEs find ESG-qualified suppliers using Swiss NOGA industry codes, rank them with a weighted scoring algorithm, and auto-generate professional German-language RFQ documents ready for email dispatch.

**Brand:** ZEROvia — Primary color `#7FC200` (green), Navy `#00274A`, Grey `#5E656D`

---

## Core User Flow: 4-Step Wizard

### Step 1: Bedarf (Procurement Need)
- **Standort** (Location): Text input for city or ZIP (e.g., "Zürich", "Basel", "München"). Geocode to lat/lng using OpenStreetMap Nominatim API (free, no key). Default: Zürich (47.3769, 8.5417).
- **Suchradius**: Dropdown — 50 km, 150 km (default), 300 km, 500 km, 1000 km, Weltweit (99999 km)
- **NOGA-Kategorien**: Searchable typeahead input for Swiss NOGA 2008 industry codes (e.g., C17.21 = Wellpapier). Show code + German label. Allow multiple selections as removable chips.
- **Jahresvolumen (CHF)**: Number input, default 250,000
- **Bedarfsbeschreibung**: Textarea describing the procurement need

### Step 2: Bewertungskriterien (Scoring Weights)
Five sliders/inputs that must sum to 100%:
- Preis & Konditionen: default 30%
- ESG & Nachhaltigkeit: default 25%
- Lieferzuverlässigkeit: default 20%
- Zertifizierungen: default 15%
- Qualität & Referenzen: default 10%

Plus filters:
- Mindest-ESG-Score: 0–100, default 60
- Max. Risikostufe: "Nur Niedrig" / "Niedrig + Mittel" / "Alle" (default)
- ISO 14001 Pflicht: toggle (default off)

Include preset buttons: "ESG Priority", "Price Focus", "Balanced"

### Step 3: Shortlist (Generated Results)
Show ranked supplier cards/table with:
- Rank #, Name, City, Country, Distance (km), Score (0–100), ESG score, Risk level badge, Certifications as pills, Website link
- Checkboxes to select/deselect suppliers for the RFQ
- Filter buttons: All / Low Risk / ISO 14001
- Optional: Map view with supplier locations (Leaflet.js)

### Step 4: RFx Document
- Auto-generated professional German RFQ document (see template below)
- Editable textarea (monospace font) in a "document preview" card
- Toolbar: Copy to clipboard, Download .txt, Regenerate, Open in mail client
- Mail modal: recipient chips (from selected suppliers), subject line, intro textarea

---

## Scoring Algorithm

```
Score = esgPts + riskPts + certPts + pricePts + qualityPts

esgPts     = (supplier.esg_score / 100) × weight_esg
riskPts    = weight_delivery × {1.0 if low, 0.6 if medium, 0.2 if high}
certPts    = min(certCount / 4, 1.0) × weight_certifications
pricePts   = weight_price × 0.75       (placeholder until real offer data)
qualityPts = weight_quality × 0.75     (placeholder)
```

Weights are normalized to sum to exactly 100 before calculation.

---

## Distance Calculation

Use the **Haversine formula** to calculate great-circle distance between the user's geocoded location and each supplier's lat/lng:

```
R = 6371 km
a = sin²(Δlat/2) + cos(lat1) × cos(lat2) × sin²(Δlng/2)
distance = R × 2 × asin(√a)
```

Filter out suppliers beyond the selected radius.

---

## Data Model

### Suppliers (seed ~30 real DACH companies)
| Field | Type | Example |
|-------|------|---------|
| id | UUID | — |
| name | string | "Geberit AG" |
| country | char(2) | CH, DE, AT, FR |
| city | string | "Rapperswil" |
| lat / lng | decimal(7) | 47.2266 / 8.8184 |
| esg_score | int 0–100 | 88 |
| risk_level | enum | low / medium / high |
| noga_codes | json array | ["C22", "C23"] |
| certifications | json array | ["ISO 9001", "ISO 14001", "EcoVadis Gold"] |
| website | string | "geberit.com" |
| email | string | "info@geberit.com" |
| description | text | "Leading supplier of..." |
| active | boolean | true |

**ESG Categories:** ≥75 = "Excellent", 50–74 = "Good", 25–49 = "Average", <25 = "Poor"

**Sample suppliers to seed:**

| Name | Country | City | ESG | Risk | NOGA | Key Certs |
|------|---------|------|-----|------|------|-----------|
| Mondi AG | AT | Wien | 87 | low | C17.21 | ISO 9001, ISO 14001, FSC, EcoVadis Gold |
| DSM-Firmenich | CH | Kaiseraugst | 91 | low | C20, C21 | ISO 9001, ISO 14001, EcoVadis Platinum |
| Geberit AG | CH | Rapperswil | 88 | low | C22, C23 | ISO 9001, ISO 14001, EcoVadis Gold |
| Trumpf GmbH | DE | Ditzingen | 86 | low | C28.4 | ISO 9001, ISO 14001, UN Global Compact |
| BACHEM AG | CH | Bubendorf | 85 | low | C20, C21 | ISO 9001, ISO 14001 |
| Hansgrohe SE | DE | Schiltach | 83 | low | C28.1 | ISO 9001, ISO 14001, ISO 50001 |
| SFS Group AG | CH | Heerbrugg | 82 | low | C25, C28 | ISO 9001, ISO 14001 |
| Metsä Board | DE | Düsseldorf | 81 | low | C17.21 | ISO 9001, FSC, PEFC, EcoVadis Gold |
| Anton Paar | AT | Graz | 80 | low | C26 | ISO 9001, ISO 14001, CE, REACH |
| Bühler AG | CH | Uzwil | 79 | low | C28, C29 | ISO 9001, ISO 14001, ISO 45001 |
| Smurfit Kappa | FR | Lyon | 79 | low | C17.21 | ISO 9001, ISO 14001, FSC |
| Dachser GmbH | DE | Kempten | 78 | low | H49, H52 | ISO 9001, ISO 14001 |
| Engel Austria | AT | Schwertberg | 77 | low | C28.9 | ISO 9001, ISO 14001, CE |
| Schüco | DE | Bielefeld | 76 | low | C25.1 | ISO 9001, ISO 14001, CE |
| Komax | CH | Dierikon | 76 | low | C28.4 | ISO 9001, ISO 14001, CE |
| Klöckner & Co | DE | Duisburg | 67 | medium | G46.7 | ISO 9001, ISO 14001 |
| Glatfelter | CH | Gernsbach | 71 | medium | C17.12 | ISO 9001, FSC, PEFC |
| Perlen Papier | CH | Root | 69 | medium | C17.21 | ISO 9001, ISO 14001, FSC |

### RFQ Documents
| Field | Type |
|-------|------|
| id | UUID |
| reference_nr | string, auto: "ZEROvia-RFQ-YYYY-NNNN" |
| supplier_ids | json array of UUIDs |
| noga_codes | json array |
| scoring_weights | json object |
| location | string |
| search_radius_km | int |
| annual_volume_chf | int |
| description | text |
| rfq_text | longtext (generated) |
| sent_at | timestamp (null = draft) |

### RFQ Recipients
| Field | Type |
|-------|------|
| rfq_id | FK → rfq_documents |
| supplier_id | FK → suppliers |
| email | string |
| sent_at | timestamp |
| opened_at | timestamp (email tracking) |

---

## RFQ Document Template (German)

Generate this text, filling in variables from the wizard data:

```
ANGEBOTSANFORDERUNG (Request for Quotation – RFQ)
NOGA/NACE-basiert | ZEROvia Supplier Scouting
══════════════════════════════════════════════════

Referenz:           {reference_nr}
Datum:              {today}
Einreichungsfrist:  {today + 21 days}, 23:59 Uhr MEZ
Zuschlagsentscheid: ca. {today + 35 days}

══════════════════════════════════════════════════
1. AUFTRAGGEBERIN
══════════════════════════════════════════════════

ZEROvia GmbH | UID CHE-387.599.569
7153 Schluein, Graubünden, Schweiz
procurement@zerovia.ch | zerovia.ch
Standort: {location}

══════════════════════════════════════════════════
2. GEGENSTAND
══════════════════════════════════════════════════

NOGA/NACE: {selected_noga_codes}
Radius:    {radius} km um {location}
Volumen:   CHF {volume} / Jahr
Incoterms: DDP {location}

{description}

══════════════════════════════════════════════════
3. ANGESCHRIEBENE LIEFERANTEN
══════════════════════════════════════════════════

   1. {supplier_name} ({city}, {country})
      Score: {score}/100 | ESG: {esg} | Distanz: {dist} km | Risiko: {risk}
      Zertifikate: {certs}

   2. ...

══════════════════════════════════════════════════
4. ALLGEMEINE ANFORDERUNGEN
══════════════════════════════════════════════════

4.1  ESG & Nachhaltigkeit ({esg_weight}%)
   ▶ [PFLICHT]   ESG-Score ≥ {min_esg}/100
   ▶ [PFLICHT]   Nachhaltigkeitsbericht (max. 24 Monate)
   ▶ [PFLICHT]   Klimastrategie mit Scope 1+2 Reduktionszielen
   ▶ [PFLICHT]   OR 964j / EU LkSG Konformität
   ○ [EMPFOHLEN] VSME-Berichterstattung

4.2  Preis & Konditionen ({price_weight}%)
   ▶ [PFLICHT]   CHF-Angebot gültig 90 Tage
   ▶ [PFLICHT]   Staffelpreistabelle (3 Mengenstufen)
   ▶ [PFLICHT]   Preisgleitklausel mit Indexbasis
   ○ [EMPFOHLEN] Skonto-Option, Zahlungsziel 30 Tage

4.3  Lieferzuverlässigkeit ({delivery_weight}%)
   ▶ [PFLICHT]   OTD ≥ 95% (letzte 12 Monate)
   ▶ [PFLICHT]   Vorlaufzeit max. 10 Werktage
   ▶ [PFLICHT]   DDP {location}
   ○ [EMPFOHLEN] Sicherheitsbestand, EDI/Track & Trace

4.4  Zertifizierungen ({cert_weight}%)
   ▶ [PFLICHT]   Alle Zertifikate mit Angebot einreichen
   ▶ [PFLICHT]   CE, REACH, RoHS (sofern einschlägig)
   ○ [EMPFOHLEN] ISO 9001, UN Global Compact

4.5  Qualität & Referenzen ({quality_weight}%)
   ▶ [PFLICHT]   2 DACH-Referenzprojekte
   ▶ [PFLICHT]   Reklamationsquote letzte 12 Monate
   ○ [EMPFOHLEN] Qualitätsaudit-Ergebnisse

══════════════════════════════════════════════════
5. ANGEBOTSBESTANDTEILE
══════════════════════════════════════════════════

   ☐  Preisliste (Excel + PDF, CHF)
   ☐  ESG-Bericht / EcoVadis Scorecard
   ☐  ISO 14001 und weitere Zertifikate
   ☐  Produktdatenblätter
   ☐  Referenzliste (mind. 2 Projekte, DACH)
   ☐  Lieferantenfragebogen (Anhang A)
   ☐  Lieferkettenerklärung (Anhang B)
   ☐  NDA unterzeichnet (Anhang C)

══════════════════════════════════════════════════
6. BEWERTUNGSMATRIX
══════════════════════════════════════════════════

   Preis & Konditionen              {price_weight}%
   ESG & Nachhaltigkeit             {esg_weight}%
   Lieferzuverlässigkeit            {delivery_weight}%
   Zertifizierungen & Compliance    {cert_weight}%
   Qualität & Referenzen            {quality_weight}%
   ─────────────────────────────────
   Total                            100%

══════════════════════════════════════════════════
7. TERMINE
══════════════════════════════════════════════════

   Versand RFQ:         {today}
   Rückfragen bis:      {today + 7 days}
   Angebotsfrist:       {today + 21 days}, 23:59 Uhr
   Zuschlagsentscheid:  ca. {today + 35 days}

   Einreichung: procurement@zerovia.ch
   Betreff:     [{reference_nr}] [Firma]
   Format:      PDF + Excel
```

---

## NOGA Taxonomy

Include a searchable NOGA 2008 taxonomy with ~120 entries. Example entries:
- A01 — Land- und Forstwirtschaft
- C10 — Herstellung von Nahrungs- und Futtermitteln
- C17 — Herstellung von Papier, Pappe und Waren daraus
- C17.12 — Herstellung von Papier, Karton und Pappe
- C17.21 — Herstellung von Wellpapier und -pappe
- C20 — Herstellung von chemischen Erzeugnissen
- C21 — Herstellung von pharmazeutischen Erzeugnissen
- C22 — Herstellung von Gummi- und Kunststoffwaren
- C25 — Herstellung von Metallerzeugnissen
- C26 — Herstellung von Datenverarbeitungsgeräten
- C28 — Maschinenbau
- G46 — Grosshandel
- H49 — Landverkehr und Transport in Rohrfernleitungen

The search should match by code prefix AND German label (case-insensitive).

---

## UI/UX Requirements

- Modern, clean design with ZEROvia brand colors (#7FC200 green, #00274A navy)
- 4-step wizard with visual progress bar
- Supplier cards: score ring (circular progress 0–100), ESG badge (color-coded), risk badge (green/yellow/red), certification pills
- Responsive: 2–3 column grid on desktop, single column on mobile
- Toast notifications for actions (copy, generate, send)
- Loading spinners during search/generation
- Document preview styled like a "paper" card with monospace text

---

## Tech Preferences

Use whatever stack the platform supports best (React/Next.js, Vue, Svelte, etc.). The app should work as a self-contained frontend prototype with:
- Supplier data hardcoded or loaded from a JSON file
- Scoring algorithm runs client-side
- Geocoding via fetch to Nominatim API
- RFQ text generated client-side from template
- No backend required for the prototype

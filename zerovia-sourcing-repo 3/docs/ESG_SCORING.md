# ESG Scoring Model — ZEROvia Sourcing & RFX Agent

## Overview

Every supplier in the shortlist receives a composite score from 0–100 based on five weighted dimensions. The weights are fully configurable per sourcing process.

## Default Weights

| Dimension             | Default | Range |
|-----------------------|---------|-------|
| Preis & Konditionen   | 30%     | 0–100 |
| ESG & Nachhaltigkeit  | 25%     | 0–100 |
| Lieferzuverlässigkeit | 20%     | 0–100 |
| Zertifizierungen      | 15%     | 0–100 |
| Qualität & Referenzen | 10%     | 0–100 |
| **Total**             | **100%**|       |

All weights are normalised to sum to exactly 100 before scoring.

## Scoring Formula

```
score = (esg_score / 100 × w_esg)
      + (risk_factor × w_delivery)
      + (cert_factor × w_certifications)
      + (0.75 × w_price)        # placeholder — replaced by actual offer data post-award
      + (0.75 × w_quality)      # placeholder — replaced by reference check result
```

### Risk Factor

| Risk Level | Multiplier |
|-----------|-----------|
| Low       | 1.0        |
| Medium    | 0.6        |
| High      | 0.2        |

### Certification Factor

`min(cert_count / 4, 1.0)` — capped at 4 certifications for full score.

## Filters Applied Before Scoring

| Filter              | Parameter        | Default |
|---------------------|------------------|---------|
| Minimum ESG score   | `min_esg`        | 60      |
| Maximum risk level  | `max_risk`       | all     |
| ISO 14001 required  | `require_iso14001`| false  |
| Search radius       | `radius_km`      | 150 km  |

If no suppliers match the NOGA category filter, the system falls back to all suppliers in the radius (with a UI warning).

## ESG Data Sources

| Source       | Description                                         |
|--------------|-----------------------------------------------------|
| EcoVadis     | Primary benchmark (0–100, methodology-aligned)      |
| Self-declared| Supplier upload via VSME standard (v1.2+)           |
| ZEROvia Hub  | Real-time sync from internal ESG data platform (v2.0)|

## Regulatory Alignment

Scores ≥ 60 indicate basic compliance readiness with:
- **OR 964b–j** (Swiss supply chain due diligence)
- **EU LkSG** (German Supply Chain Act)

Scores ≥ 75 indicate strong alignment with CSRD/ESRS reporting expectations.

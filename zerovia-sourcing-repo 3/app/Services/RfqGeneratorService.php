<?php

namespace App\Services;

use App\Models\RfqDocument;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RfqGeneratorService
{
    public function generate(array $params, Collection $suppliers): RfqDocument
    {
        $rfqText = $this->renderText($params, $suppliers);

        $doc = RfqDocument::create([
            'user_id'          => Auth::id(),
            'supplier_ids'     => $suppliers->pluck('supplier.id')->toArray(),
            'noga_codes'       => $params['noga_codes'] ?? [],
            'scoring_weights'  => $params['scoring_weights'] ?? [],
            'location'         => $params['location'] ?? 'Schweiz',
            'search_radius_km' => $params['radius_km'] ?? 150,
            'annual_volume_chf'=> $params['volume'] ?? null,
            'description'      => $params['description'] ?? '',
            'rfq_text'         => $rfqText,
        ]);

        // Create recipient records
        foreach ($suppliers as $item) {
            /** @var Supplier $supplier */
            $supplier = $item['supplier'];
            $doc->recipients()->create([
                'supplier_id' => $supplier->id,
                'email'       => $supplier->email ?? 'procurement@' . ($supplier->website ?? 'supplier.ch'),
            ]);
        }

        return $doc;
    }

    private function renderText(array $params, Collection $suppliers): string
    {
        $cfg       = config('zerovia.rfq');
        $weights   = $params['scoring_weights'] ?? config('zerovia.sourcing.scoring_weights');
        $now       = now();
        $deadline  = $now->copy()->addDays($cfg['lead_time_days']);
        $decision  = $now->copy()->addDays($cfg['decision_offset_days']);
        $questions = $now->copy()->addDays($cfg['question_deadline_days']);
        $refNr     = RfqDocument::generateReferenceNr();
        $loc       = $params['location'] ?? 'Schweiz';
        $radius    = $params['radius_km'] ?? 150;
        $vol       = number_format($params['volume'] ?? 250000, 0, '.', "'");
        $desc      = $params['description'] ?? 'Beschaffungsgüter gemäss Anforderungsprofil';
        $nogaList  = implode(', ', $params['noga_codes'] ?? ['Allgemeine Beschaffung']);

        $supList = $suppliers->map(function ($item, $i) {
            $s = $item['supplier'];
            return sprintf(
                "   %d. %s (%s, %s)\n      Score: %d/100 | ESG: %d | Distanz: %d km | Risiko: %s\n      Zertifikate: %s",
                $i + 1,
                $s->name,
                $s->city,
                $s->country,
                $item['score'],
                $s->esg_score,
                $item['distance'],
                $s->risk_label,
                implode(', ', $s->certifications ?? [])
            );
        })->implode("\n\n");

        $supEmails = $suppliers
            ->map(fn ($item) => $item['supplier']->email ?? 'procurement@' . $item['supplier']->website)
            ->implode(', ');

        $fmt = fn ($date) => $date->format('d.m.Y');

        return <<<EOT
ANGEBOTSANFORDERUNG (Request for Quotation – RFQ)
NOGA/NACE-basiert | ZEROvia Supplier Scouting
══════════════════════════════════════════════════════════════

Referenz:           {$refNr}
Datum:              {$fmt($now)}
Einreichungsfrist:  {$fmt($deadline)}, 23:59 Uhr MEZ
Zuschlagsentscheid: ca. {$fmt($decision)}

══════════════════════════════════════════════════════════════
1. AUFTRAGGEBERIN
══════════════════════════════════════════════════════════════

{$cfg['company_name']} | UID {$cfg['company_uid']}
{$cfg['company_address']}
{$cfg['submission_email']} | {$cfg['company_url']}
Standort: {$loc}

══════════════════════════════════════════════════════════════
2. GEGENSTAND
══════════════════════════════════════════════════════════════

NOGA/NACE: {$nogaList}
Radius:    {$radius} km um {$loc}
Volumen:   CHF {$vol} / Jahr
Incoterms: DDP {$loc}

{$desc}

══════════════════════════════════════════════════════════════
3. ANGESCHRIEBENE LIEFERANTEN
══════════════════════════════════════════════════════════════

{$supList}

E-Mail: {$supEmails}

══════════════════════════════════════════════════════════════
4. ALLGEMEINE ANFORDERUNGEN
══════════════════════════════════════════════════════════════

4.1  ESG & Nachhaltigkeit ({$weights['esg']}%)
   ▶ [PFLICHT]   ESG-Score ≥ {$params['min_esg']}/100 (EcoVadis oder gleichwertig)
   ▶ [PFLICHT]   Nachhaltigkeitsbericht (max. 24 Monate)
   ▶ [PFLICHT]   Klimastrategie mit Scope 1+2 Reduktionszielen
   ▶ [PFLICHT]   OR 964j / EU LkSG Konformität
   ○ [EMPFOHLEN] VSME-Berichterstattung

4.2  Preis & Konditionen ({$weights['price']}%)
   ▶ [PFLICHT]   CHF-Angebot gültig {$cfg['validity_days']} Tage
   ▶ [PFLICHT]   Staffelpreistabelle (3 Mengenstufen)
   ▶ [PFLICHT]   Preisgleitklausel mit Indexbasis
   ○ [EMPFOHLEN] Skonto-Option, Zahlungsziel 30 Tage

4.3  Lieferzuverlässigkeit ({$weights['delivery']}%)
   ▶ [PFLICHT]   OTD ≥ 95% (letzte 12 Monate)
   ▶ [PFLICHT]   Vorlaufzeit Standardabruf: max. 10 Werktage
   ▶ [PFLICHT]   DDP {$loc}
   ○ [EMPFOHLEN] Sicherheitsbestand A-Teile, EDI/Track & Trace

4.4  Zertifizierungen ({$weights['certifications']}%)
   ▶ [PFLICHT]   Alle Zertifikate mit Angebot einreichen
   ▶ [PFLICHT]   CE, REACH, RoHS (sofern einschlägig)
   ○ [EMPFOHLEN] ISO 9001, UN Global Compact

4.5  Qualität & Referenzen ({$weights['quality']}%)
   ▶ [PFLICHT]   2 DACH-Referenzprojekte (Kontaktangabe)
   ▶ [PFLICHT]   Reklamationsquote letzte 12 Monate
   ○ [EMPFOHLEN] Qualitätsaudit-Ergebnisse, EMPB-Fähigkeit

══════════════════════════════════════════════════════════════
5. ANGEBOTSBESTANDTEILE
══════════════════════════════════════════════════════════════

   ☐  Preisliste / Konditionsblatt (Excel + PDF, CHF)
   ☐  ESG-Bericht / EcoVadis Scorecard (max. 24 Monate)
   ☐  ISO 14001 und weitere Zertifikate
   ☐  Produktdatenblätter / Spezifikationsblätter
   ☐  Referenzliste (mind. 2 Projekte, DACH)
   ☐  Lieferantenfragebogen (Anhang A)
   ☐  Lieferkettenerklärung (Anhang B)
   ☐  NDA unterzeichnet (Anhang C)

══════════════════════════════════════════════════════════════
6. BEWERTUNGSMATRIX
══════════════════════════════════════════════════════════════

   Preis & Konditionen              {$weights['price']}%
   ESG & Nachhaltigkeit             {$weights['esg']}%
   Lieferzuverlässigkeit            {$weights['delivery']}%
   Zertifizierungen & Compliance    {$weights['certifications']}%
   Qualität & Referenzen            {$weights['quality']}%
   ─────────────────────────────────
   Total                            100%

══════════════════════════════════════════════════════════════
7. TERMINE
══════════════════════════════════════════════════════════════

   Versand RFQ:         {$fmt($now)}
   Rückfragen bis:      {$fmt($questions)}
   Angebotsfrist:       {$fmt($deadline)}, 23:59 Uhr
   Zuschlagsentscheid:  ca. {$fmt($decision)}
   Vertragsbeginn:      nach Vereinbarung

   Einreichung: {$cfg['submission_email']}
   Betreff:     [{$refNr}] [Firma]
   Format:      PDF + Excel

══════════════════════════════════════════════════════════════

{$cfg['company_name']} – Procurement & Sustainability
{$cfg['company_url']} | {$cfg['company_uid']} | {$cfg['company_address']}
EOT;
    }
}

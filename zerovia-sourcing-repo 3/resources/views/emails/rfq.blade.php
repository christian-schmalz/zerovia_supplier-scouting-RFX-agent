@component('mail::message')
# Angebotsanfrage (RFQ) — ZEROvia Supplier Scouting

Sehr geehrte Damen und Herren,
@if($introText)

{{ $introText }}
@else

im Rahmen unseres strukturierten Beschaffungsprozesses laden wir Sie herzlich ein, ein Angebot für den untenstehenden Bedarf einzureichen.
@endif

---

**Referenz:** {{ $rfq->reference_nr }}
**Einreichungsfrist:** {{ $rfq->created_at->addDays(config('zerovia.rfq.lead_time_days'))->format('d.m.Y') }}, 23:59 Uhr MEZ
**Einreichung an:** {{ config('zerovia.rfq.submission_email') }}
**Betreff:** [{{ $rfq->reference_nr }}] [Ihr Firmenname]

---

@component('mail::panel')
Das vollständige RFQ-Dokument finden Sie nachfolgend. Bitte beachten Sie die angegebenen Anforderungen, Bewertungskriterien und Fristen.
@endcomponent

@component('mail::button', ['url' => $trackUrl, 'color' => 'success'])
RFQ bestätigen
@endcomponent

---

**Vollständiges RFQ-Dokument:**

```
{{ $rfq->rfq_text }}
```

---

Mit freundlichen Grüssen

**{{ config('zerovia.rfq.from_name') }}**
{{ config('zerovia.rfq.company_name') }} | {{ config('zerovia.rfq.company_url') }}
{{ config('zerovia.rfq.company_uid') }}

@component('mail::subcopy')
Diese Nachricht wurde über den ZEROvia Sourcing & RFX Agent versandt.
Bei Fragen wenden Sie sich bitte an {{ config('zerovia.rfq.submission_email') }}.
@endcomponent
@endcomponent

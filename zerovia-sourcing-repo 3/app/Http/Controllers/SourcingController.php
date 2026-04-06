<?php

namespace App\Http\Controllers;

use App\Http\Requests\SourcingSearchRequest;
use App\Http\Requests\RfqGenerateRequest;
use App\Services\NogaService;
use App\Services\RfqDispatchService;
use App\Services\RfqGeneratorService;
use App\Services\SourcingService;
use App\Models\RfqDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SourcingController extends Controller
{
    public function __construct(
        private readonly SourcingService    $sourcing,
        private readonly RfqGeneratorService $rfqGenerator,
        private readonly RfqDispatchService  $rfqDispatch,
        private readonly NogaService         $noga,
    ) {}

    /**
     * GET /api/noga/search?q=Verpackung
     */
    public function nogaSearch(Request $request): JsonResponse
    {
        $results = $this->noga->search($request->string('q', ''), 25);
        return response()->json($results);
    }

    /**
     * POST /api/sourcing/search
     */
    public function search(SourcingSearchRequest $request): JsonResponse
    {
        $suppliers = $this->sourcing->search($request->validated());

        return response()->json([
            'count'     => $suppliers->count(),
            'suppliers' => $suppliers->map(fn ($item) => [
                'id'             => $item['supplier']->id,
                'name'           => $item['supplier']->name,
                'city'           => $item['supplier']->city,
                'country'        => $item['supplier']->country,
                'esg_score'      => $item['supplier']->esg_score,
                'esg_category'   => $item['supplier']->esg_category,
                'risk_level'     => $item['supplier']->risk_level,
                'risk_label'     => $item['supplier']->risk_label,
                'certifications' => $item['supplier']->certifications,
                'distance_km'    => $item['distance'],
                'score'          => $item['score'],
            ]),
        ]);
    }

    /**
     * POST /api/sourcing/rfq/generate
     */
    public function generateRfq(RfqGenerateRequest $request): JsonResponse
    {
        $params    = $request->validated();
        $suppliers = $this->sourcing->search($params);

        if ($suppliers->isEmpty()) {
            return response()->json(['message' => 'Keine Lieferanten für die gewählten Parameter gefunden.'], 422);
        }

        $doc = $this->rfqGenerator->generate($params, $suppliers);

        return response()->json([
            'rfq_id'       => $doc->id,
            'reference_nr' => $doc->reference_nr,
            'rfq_text'     => $doc->rfq_text,
            'recipients'   => $doc->recipients()->with('supplier')->get()->map(fn ($r) => [
                'supplier' => $r->supplier->name,
                'email'    => $r->email,
            ]),
        ], 201);
    }

    /**
     * POST /api/sourcing/rfq/{rfq}/send
     */
    public function sendRfq(Request $request, RfqDocument $rfq): JsonResponse
    {
        $intro = $request->string('intro_text', '');
        $sent  = $this->rfqDispatch->dispatch($rfq, $intro);

        return response()->json([
            'sent'    => $sent,
            'message' => "{$sent} Empfänger erfolgreich angeschrieben.",
        ]);
    }

    /**
     * GET /rfq/track/{rfq}/{recipient} — open tracking pixel
     */
    public function trackOpen(RfqDocument $rfq, \App\Models\RfqRecipient $recipient): \Illuminate\Http\Response
    {
        $this->rfqDispatch->markOpened($recipient);

        // 1×1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($pixel, 200, [
            'Content-Type'  => 'image/gif',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}

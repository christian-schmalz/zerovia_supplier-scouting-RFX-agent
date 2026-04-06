<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Collection;

class SourcingService
{
    /**
     * Search and rank suppliers based on procurement parameters.
     *
     * @param array $params {
     *   noga_codes: string[],
     *   lat: float,
     *   lng: float,
     *   radius_km: int,
     *   min_esg: int,
     *   max_risk: string (low|mid|all),
     *   require_iso14001: bool,
     *   scoring_weights: array,
     *   top_n: int
     * }
     */
    public function search(array $params): Collection
    {
        $weights  = $this->normalizeWeights($params['scoring_weights'] ?? config('zerovia.sourcing.scoring_weights'));
        $lat      = (float) ($params['lat'] ?? 47.3769);
        $lng      = (float) ($params['lng'] ?? 8.5417);
        $radius   = (int)   ($params['radius_km'] ?? config('zerovia.sourcing.default_radius_km'));
        $minEsg   = (int)   ($params['min_esg'] ?? config('zerovia.sourcing.default_min_esg'));
        $maxRisk  = $params['max_risk'] ?? 'all';
        $topN     = (int)   ($params['top_n'] ?? config('zerovia.sourcing.default_top_n'));
        $nogaCodes = $params['noga_codes'] ?? [];
        $requireIso = (bool) ($params['require_iso14001'] ?? false);

        return Supplier::query()
            ->active()
            ->minEsg($minEsg)
            ->when($maxRisk !== 'all', fn ($q) => $q->riskLevel($maxRisk))
            ->when($requireIso, fn ($q) => $q->withCertification('ISO 14001'))
            ->get()
            ->map(function (Supplier $supplier) use ($lat, $lng, $weights) {
                $dist  = $supplier->distanceTo($lat, $lng);
                $score = $this->computeScore($supplier, $weights);
                return [
                    'supplier' => $supplier,
                    'distance' => round($dist),
                    'score'    => $score,
                ];
            })
            ->filter(fn ($item) => $item['distance'] <= $radius || $radius >= 99_999)
            ->when(!empty($nogaCodes), function ($collection) use ($nogaCodes) {
                $filtered = $collection->filter(fn ($item) => $item['supplier']->matchesNogaCodes($nogaCodes));
                return $filtered->isNotEmpty() ? $filtered : $collection; // fallback if no category match
            })
            ->sortByDesc('score')
            ->take($topN * 3)
            ->values();
    }

    /**
     * Compute weighted score (0–100) for a supplier.
     */
    public function computeScore(Supplier $supplier, array $weights): int
    {
        $esgPts   = ($supplier->esg_score / 100) * $weights['esg'];
        $riskPts  = match ($supplier->risk_level) {
            'low'    => $weights['delivery'],
            'medium' => $weights['delivery'] * 0.6,
            default  => $weights['delivery'] * 0.2,
        };
        $certCount   = count($supplier->certifications ?? []);
        $certPts     = min($certCount / 4, 1) * $weights['certifications'];
        $pricePts    = $weights['price'] * 0.75; // placeholder — set from actual offer data
        $qualityPts  = $weights['quality'] * 0.75; // placeholder

        return (int) round($esgPts + $riskPts + $certPts + $pricePts + $qualityPts);
    }

    /**
     * Ensure weights sum to exactly 100.
     */
    private function normalizeWeights(array $weights): array
    {
        $sum = array_sum($weights);
        if ($sum === 0) {
            return config('zerovia.sourcing.scoring_weights');
        }
        return array_map(fn ($w) => ($w / $sum) * 100, $weights);
    }
}

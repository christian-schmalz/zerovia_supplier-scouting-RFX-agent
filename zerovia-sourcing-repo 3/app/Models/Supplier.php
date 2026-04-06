<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'country',
        'city',
        'lat',
        'lng',
        'esg_score',
        'risk_level',
        'noga_codes',
        'certifications',
        'website',
        'email',
        'description',
        'active',
    ];

    protected $casts = [
        'lat'            => 'decimal:7',
        'lng'            => 'decimal:7',
        'esg_score'      => 'integer',
        'noga_codes'     => 'array',
        'certifications' => 'array',
        'active'         => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function rfqRecipients(): HasMany
    {
        return $this->hasMany(RfqRecipient::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeMinEsg($query, int $min)
    {
        return $query->where('esg_score', '>=', $min);
    }

    public function scopeRiskLevel($query, string $level)
    {
        return match ($level) {
            'low' => $query->where('risk_level', 'low'),
            'mid' => $query->whereIn('risk_level', ['low', 'medium']),
            default => $query,
        };
    }

    public function scopeWithCertification($query, string $cert)
    {
        return $query->whereJsonContains('certifications', $cert);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getRiskLabelAttribute(): string
    {
        return match ($this->risk_level) {
            'low'    => 'Niedrig',
            'medium' => 'Mittel',
            'high'   => 'Hoch',
            default  => 'Unbekannt',
        };
    }

    public function getEsgCategoryAttribute(): string
    {
        return match (true) {
            $this->esg_score >= 75 => 'Excellent',
            $this->esg_score >= 50 => 'Good',
            $this->esg_score >= 25 => 'Average',
            default                => 'Poor',
        };
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function hasCertification(string $cert): bool
    {
        return in_array($cert, $this->certifications ?? []);
    }

    public function matchesNogaCodes(array $codes): bool
    {
        if (empty($codes)) {
            return true;
        }
        foreach ($this->noga_codes ?? [] as $supplierCode) {
            foreach ($codes as $code) {
                if (str_starts_with(strtoupper($supplierCode), strtoupper($code))) {
                    return true;
                }
            }
        }
        return false;
    }

    public function distanceTo(float $lat, float $lng): float
    {
        // Haversine formula — returns km
        $earthRadius = 6371;
        $dLat = deg2rad($lat - $this->lat);
        $dLng = deg2rad($lng - $this->lng);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($this->lat)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;
        return $earthRadius * 2 * asin(sqrt($a));
    }
}

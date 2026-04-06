<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RfqDocument extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rfq_documents';

    protected $fillable = [
        'reference_nr',
        'user_id',
        'supplier_ids',
        'noga_codes',
        'scoring_weights',
        'location',
        'search_radius_km',
        'annual_volume_chf',
        'description',
        'rfq_text',
        'sent_at',
    ];

    protected $casts = [
        'supplier_ids'     => 'array',
        'noga_codes'       => 'array',
        'scoring_weights'  => 'array',
        'sent_at'          => 'datetime',
    ];

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (RfqDocument $doc) {
            if (empty($doc->reference_nr)) {
                $doc->reference_nr = self::generateReferenceNr();
            }
        });
    }

    public static function generateReferenceNr(): string
    {
        return 'ZEROvia-RFQ-' . date('Y') . '-' . str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    // ── Relations ─────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(RfqRecipient::class, 'rfq_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsSentAttribute(): bool
    {
        return $this->sent_at !== null;
    }

    public function getRecipientCountAttribute(): int
    {
        return $this->recipients()->count();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_sent ? 'Versandt' : 'Entwurf';
    }
}

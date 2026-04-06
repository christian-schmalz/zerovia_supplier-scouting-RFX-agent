<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SourcingSearchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'noga_codes'              => ['nullable', 'array'],
            'noga_codes.*'            => ['string', 'max:20'],
            'lat'                     => ['nullable', 'numeric', 'between:-90,90'],
            'lng'                     => ['nullable', 'numeric', 'between:-180,180'],
            'location'                => ['nullable', 'string', 'max:100'],
            'radius_km'               => ['nullable', 'integer', 'min:1', 'max:99999'],
            'min_esg'                 => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_risk'                => ['nullable', 'in:low,mid,all'],
            'require_iso14001'        => ['nullable', 'boolean'],
            'top_n'                   => ['nullable', 'integer', 'min:1', 'max:50'],
            'scoring_weights'         => ['nullable', 'array'],
            'scoring_weights.price'   => ['nullable', 'numeric', 'min:0'],
            'scoring_weights.esg'     => ['nullable', 'numeric', 'min:0'],
            'scoring_weights.delivery'=> ['nullable', 'numeric', 'min:0'],
            'scoring_weights.certifications' => ['nullable', 'numeric', 'min:0'],
            'scoring_weights.quality' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

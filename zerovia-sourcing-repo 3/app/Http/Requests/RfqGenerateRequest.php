<?php

namespace App\Http\Requests;

class RfqGenerateRequest extends SourcingSearchRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'description'       => ['nullable', 'string', 'max:2000'],
            'volume'            => ['nullable', 'integer', 'min:0'],
            'supplier_ids'      => ['nullable', 'array'],
            'supplier_ids.*'    => ['uuid'],
        ]);
    }
}

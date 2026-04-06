<?php

use App\Models\Supplier;
use App\Models\User;
use App\Services\RfqGeneratorService;
use App\Services\SourcingService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('generates an rfq document with reference number', function () {
    $suppliers = collect([
        ['supplier' => Supplier::factory()->create(), 'distance' => 45, 'score' => 82],
        ['supplier' => Supplier::factory()->create(), 'distance' => 78, 'score' => 74],
    ]);

    $doc = app(RfqGeneratorService::class)->generate([
        'noga_codes'      => ['C17.21'],
        'location'        => 'Zürich',
        'min_esg'         => 60,
        'description'     => 'FSC-zertifizierte Kartonverpackungen',
        'volume'          => 350000,
        'scoring_weights' => ['price' => 30, 'esg' => 25, 'delivery' => 20, 'certifications' => 15, 'quality' => 10],
    ], $suppliers);

    expect($doc->reference_nr)->toStartWith('ZEROvia-RFQ-');
    expect($doc->rfq_text)->toContain('ANGEBOTSANFORDERUNG');
    expect($doc->rfq_text)->toContain('ZEROvia');
    expect($doc->recipients)->toHaveCount(2);
});

it('stores recipients for each supplier', function () {
    $supplier1 = Supplier::factory()->create(['email' => 'a@test.com']);
    $supplier2 = Supplier::factory()->create(['email' => 'b@test.com']);

    $suppliers = collect([
        ['supplier' => $supplier1, 'distance' => 10, 'score' => 85],
        ['supplier' => $supplier2, 'distance' => 20, 'score' => 70],
    ]);

    $doc = app(RfqGeneratorService::class)->generate([], $suppliers);

    expect($doc->recipients()->count())->toBe(2);
});

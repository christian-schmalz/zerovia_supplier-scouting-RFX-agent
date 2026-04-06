<?php

use App\Models\Supplier;

it('calculates haversine distance correctly', function () {
    $supplier = new Supplier(['lat' => 47.3769, 'lng' => 8.5417]); // Zürich

    // Distance from Zürich to Bern (~95 km)
    $dist = $supplier->distanceTo(46.9480, 7.4474);

    expect($dist)->toBeGreaterThan(80)->toBeLessThan(120);
});

it('matches noga codes correctly', function () {
    $supplier = new Supplier(['noga_codes' => ['C17', 'C17.21', 'G46']]);

    expect($supplier->matchesNogaCodes(['C17']))->toBeTrue();
    expect($supplier->matchesNogaCodes(['C17.21']))->toBeTrue();
    expect($supplier->matchesNogaCodes(['C20']))->toBeFalse();
    expect($supplier->matchesNogaCodes([]))->toBeTrue(); // empty = all match
});

it('returns correct esg category', function () {
    $excellent = new Supplier(['esg_score' => 80]);
    $good      = new Supplier(['esg_score' => 60]);
    $average   = new Supplier(['esg_score' => 35]);
    $poor      = new Supplier(['esg_score' => 10]);

    expect($excellent->esg_category)->toBe('Excellent');
    expect($good->esg_category)->toBe('Good');
    expect($average->esg_category)->toBe('Average');
    expect($poor->esg_category)->toBe('Poor');
});

it('correctly identifies certifications', function () {
    $supplier = new Supplier(['certifications' => ['ISO 9001', 'ISO 14001']]);

    expect($supplier->hasCertification('ISO 14001'))->toBeTrue();
    expect($supplier->hasCertification('EcoVadis'))->toBeFalse();
});

<?php

use App\Models\Supplier;
use App\Services\SourcingService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('returns suppliers within radius', function () {
    Supplier::factory()->count(10)->create([
        'lat' => 47.3769, // Zürich
        'lng' => 8.5417,
        'active' => true,
    ]);

    $service = app(SourcingService::class);
    $results = $service->search([
        'lat'       => 47.3769,
        'lng'       => 8.5417,
        'radius_km' => 50,
        'min_esg'   => 0,
    ]);

    expect($results)->not->toBeEmpty();
    foreach ($results as $item) {
        expect($item['distance'])->toBeLessThanOrEqual(50);
    }
});

it('filters by minimum esg score', function () {
    Supplier::factory()->create(['esg_score' => 30, 'lat' => 47.3769, 'lng' => 8.5417, 'active' => true]);
    Supplier::factory()->create(['esg_score' => 80, 'lat' => 47.3769, 'lng' => 8.5417, 'active' => true]);

    $service = app(SourcingService::class);
    $results = $service->search(['lat' => 47.3769, 'lng' => 8.5417, 'radius_km' => 99999, 'min_esg' => 70]);

    foreach ($results as $item) {
        expect($item['supplier']->esg_score)->toBeGreaterThanOrEqual(70);
    }
});

it('sorts results by score descending', function () {
    Supplier::factory()->count(5)->create(['lat' => 47.3769, 'lng' => 8.5417, 'active' => true]);

    $service = app(SourcingService::class);
    $results = $service->search(['lat' => 47.3769, 'lng' => 8.5417, 'radius_km' => 99999, 'min_esg' => 0]);

    $scores = $results->pluck('score')->toArray();
    expect($scores)->toBeSortedDescending();
});

it('falls back to all suppliers if no noga match', function () {
    Supplier::factory()->count(5)->create(['lat' => 47.3769, 'lng' => 8.5417, 'active' => true, 'noga_codes' => ['C17']]);

    $service = app(SourcingService::class);
    $results = $service->search([
        'lat'        => 47.3769,
        'lng'        => 8.5417,
        'radius_km'  => 99999,
        'min_esg'    => 0,
        'noga_codes' => ['Z99'], // non-existent code → fallback
    ]);

    expect($results)->not->toBeEmpty();
});

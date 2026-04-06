<?php

namespace App\Filament\Widgets;

use App\Models\Supplier;
use Filament\Widgets\ChartWidget;

class EsgScoreWidget extends ChartWidget
{
    protected static ?string $heading = 'ESG-Score Verteilung';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $excellent = Supplier::active()->where('esg_score', '>=', 75)->count();
        $good      = Supplier::active()->whereBetween('esg_score', [50, 74])->count();
        $average   = Supplier::active()->whereBetween('esg_score', [25, 49])->count();
        $poor      = Supplier::active()->where('esg_score', '<', 25)->count();

        return [
            'datasets' => [[
                'label' => 'Lieferanten',
                'data'  => [$excellent, $good, $average, $poor],
                'backgroundColor' => ['#7FC200', '#74A22D', '#f59e0b', '#ef4444'],
            ]],
            'labels' => ['Excellent (≥75)', 'Good (50–74)', 'Average (25–49)', 'Poor (<25)'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

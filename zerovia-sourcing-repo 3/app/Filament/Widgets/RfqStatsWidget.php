<?php

namespace App\Filament\Widgets;

use App\Models\RfqDocument;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RfqStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('RFQs gesamt', RfqDocument::count())
                ->description('Alle generierten Angebotsanfragen')
                ->color('primary')
                ->icon('heroicon-o-document-text'),

            Stat::make('Versandt', RfqDocument::whereNotNull('sent_at')->count())
                ->description('Davon bereits an Lieferanten versandt')
                ->color('success')
                ->icon('heroicon-o-paper-airplane'),

            Stat::make('Lieferanten', Supplier::active()->count())
                ->description('Aktive Lieferanten in der Datenbank')
                ->color('gray')
                ->icon('heroicon-o-building-office'),

            Stat::make('Ø ESG-Score', round(Supplier::active()->avg('esg_score') ?? 0))
                ->description('Durchschnitt aller aktiven Lieferanten')
                ->color('success')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}

<?php

namespace App\Filament\Pages;

use App\Services\GeocodingService;
use App\Services\NogaService;
use App\Services\RfqGeneratorService;
use App\Services\SourcingService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class SourcingWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'Neuer Scouting';
    protected static ?string $navigationGroup = 'Sourcing';
    protected static ?string $slug            = 'sourcing/new';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.sourcing-wizard';

    // Form state
    public array $formData = [];
    public ?Collection $shortlist = null;
    public ?string $rfqText = null;
    public ?string $rfqReferenceNr = null;

    public function mount(): void
    {
        $this->form->fill([
            'radius_km'       => 150,
            'min_esg'         => 60,
            'max_risk'        => 'all',
            'require_iso14001'=> false,
            'top_n'           => 5,
            'scoring_weights' => [
                'price'          => 30,
                'esg'            => 25,
                'delivery'       => 20,
                'certifications' => 15,
                'quality'        => 10,
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('formData')
            ->schema([
                Wizard::make([

                    // Step 1 — Bedarf
                    Wizard\Step::make('Bedarf')
                        ->icon('heroicon-o-tag')
                        ->schema([
                            TextInput::make('location')
                                ->label('Standort (Stadt oder PLZ)')
                                ->placeholder('z.B. Zürich, Basel, München')
                                ->default('Zürich')
                                ->required(),
                            Select::make('radius_km')
                                ->label('Suchradius')
                                ->options([50 => '50 km', 150 => '150 km', 300 => '300 km',
                                           500 => '500 km', 1000 => '1 000 km', 99999 => 'Weltweit'])
                                ->default(150)->required(),
                            TextInput::make('noga_codes_raw')
                                ->label('NOGA/NACE-Kategorien')
                                ->placeholder('z.B. C17.21, G46.7 (kommasepariert)')
                                ->helperText('Verwende NOGA 2008 Codes — z.B. C17 (Papier), G46 (Grosshandel)'),
                            TextInput::make('annual_volume_chf')
                                ->label('Jahresvolumen (CHF)')
                                ->numeric()->placeholder('250000'),
                            Textarea::make('description')
                                ->label('Bedarfsbeschreibung')
                                ->rows(4)
                                ->placeholder('Beschreiben Sie den Beschaffungsbedarf, Qualitätsanforderungen, Mengen …')
                                ->columnSpanFull(),
                        ])->columns(2),

                    // Step 2 — Scoring
                    Wizard\Step::make('Scoring')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->schema([
                            TextInput::make('scoring_weights.price')
                                ->label('Preis & Konditionen (%)')
                                ->numeric()->default(30)->minValue(0)->maxValue(100)->required(),
                            TextInput::make('scoring_weights.esg')
                                ->label('ESG & Nachhaltigkeit (%)')
                                ->numeric()->default(25)->minValue(0)->maxValue(100)->required(),
                            TextInput::make('scoring_weights.delivery')
                                ->label('Lieferzuverlässigkeit (%)')
                                ->numeric()->default(20)->minValue(0)->maxValue(100)->required(),
                            TextInput::make('scoring_weights.certifications')
                                ->label('Zertifizierungen (%)')
                                ->numeric()->default(15)->minValue(0)->maxValue(100)->required(),
                            TextInput::make('scoring_weights.quality')
                                ->label('Qualität & Referenzen (%)')
                                ->numeric()->default(10)->minValue(0)->maxValue(100)->required(),
                            TextInput::make('min_esg')
                                ->label('Mindest-ESG-Score')
                                ->numeric()->default(60)->minValue(0)->maxValue(100),
                            Select::make('max_risk')
                                ->label('Max. Risikostufe')
                                ->options(['low' => 'Nur Niedrig', 'mid' => 'Niedrig + Mittel', 'all' => 'Alle'])
                                ->default('all'),
                            Toggle::make('require_iso14001')
                                ->label('ISO 14001 Pflicht')
                                ->default(false),
                        ])->columns(2),

                    // Step 3 — Shortlist (generated server-side)
                    Wizard\Step::make('Shortlist')
                        ->icon('heroicon-o-list-bullet')
                        ->schema([
                            TextInput::make('top_n')
                                ->label('Anzahl Lieferanten')
                                ->numeric()->default(5)->minValue(1)->maxValue(20),
                        ]),

                    // Step 4 — RFx
                    Wizard\Step::make('RFx')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Textarea::make('rfq_intro')
                                ->label('Persönliche Einleitung (optional)')
                                ->rows(3)
                                ->placeholder('Sehr geehrte Damen und Herren, …')
                                ->columnSpanFull(),
                        ]),

                ])->submitAction(view('filament.pages.sourcing-wizard-submit')),
            ]);
    }

    public function buildShortlist(): void
    {
        $data   = $this->formData;
        $params = $this->buildParams($data);
        $this->shortlist = app(SourcingService::class)->search($params);
    }

    public function generateRfq(): void
    {
        if (!$this->shortlist || $this->shortlist->isEmpty()) {
            $this->buildShortlist();
        }
        $data   = $this->formData;
        $params = $this->buildParams($data);
        $doc    = app(RfqGeneratorService::class)->generate($params, $this->shortlist);
        $this->rfqText        = $doc->rfq_text;
        $this->rfqReferenceNr = $doc->reference_nr;

        Notification::make()
            ->title("RFQ {$doc->reference_nr} erstellt")
            ->success()->send();
    }

    private function buildParams(array $data): array
    {
        $location = $data['location'] ?? 'Zürich';
        $coords   = app(GeocodingService::class)->geocode($location);

        return [
            'noga_codes'      => array_filter(array_map('trim', explode(',', $data['noga_codes_raw'] ?? ''))),
            'location'        => $location,
            'lat'             => $coords['lat'],
            'lng'             => $coords['lng'],
            'radius_km'       => (int) ($data['radius_km'] ?? 150),
            'min_esg'         => (int) ($data['min_esg'] ?? 60),
            'max_risk'        => $data['max_risk'] ?? 'all',
            'require_iso14001'=> (bool) ($data['require_iso14001'] ?? false),
            'top_n'           => (int) ($data['top_n'] ?? 5),
            'scoring_weights' => $data['scoring_weights'] ?? [],
            'description'     => $data['description'] ?? '',
            'volume'          => (int) ($data['annual_volume_chf'] ?? 250000),
        ];
    }
}

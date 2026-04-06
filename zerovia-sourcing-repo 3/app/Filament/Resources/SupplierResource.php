<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Lieferanten';
    protected static ?string $navigationLabel = 'Lieferantendatenbank';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Lieferant';
    protected static ?string $pluralModelLabel = 'Lieferanten';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Stammdaten')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('country')
                    ->label('Land')
                    ->options(['CH' => 'Schweiz', 'DE' => 'Deutschland', 'AT' => 'Österreich',
                               'FR' => 'Frankreich', 'IT' => 'Italien', 'NL' => 'Niederlande'])
                    ->required()->searchable(),
                Forms\Components\TextInput::make('city')->label('Stadt')->required(),
                Forms\Components\TextInput::make('lat')->label('Breitengrad')->numeric()->required(),
                Forms\Components\TextInput::make('lng')->label('Längengrad')->numeric()->required(),
                Forms\Components\TextInput::make('website')->label('Website')->url(),
                Forms\Components\TextInput::make('email')->label('E-Mail')->email(),
            ])->columns(2),

            Forms\Components\Section::make('ESG & Risiko')->schema([
                Forms\Components\TextInput::make('esg_score')
                    ->label('ESG-Score (0–100)')
                    ->numeric()->minValue(0)->maxValue(100)->required()
                    ->helperText('EcoVadis-äquivalenter Score'),
                Forms\Components\Select::make('risk_level')
                    ->label('Risikostufe')
                    ->options(['low' => 'Niedrig', 'medium' => 'Mittel', 'high' => 'Hoch'])
                    ->required(),
                Forms\Components\TagsInput::make('certifications')
                    ->label('Zertifizierungen')
                    ->suggestions(['ISO 9001', 'ISO 14001', 'ISO 45001', 'EcoVadis', 'UN Global Compact',
                                   'FSC', 'PEFC', 'CE', 'REACH'])
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('noga_codes')
                    ->label('NOGA/NACE-Codes')
                    ->helperText('z.B. C17, C17.21, G46.7')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Beschreibung')->schema([
                Forms\Components\Textarea::make('description')->label('Beschreibung')->rows(4)->columnSpanFull(),
                Forms\Components\Toggle::make('active')->label('Aktiv')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('semibold'),
                Tables\Columns\TextColumn::make('city')->label('Stadt')->searchable(),
                Tables\Columns\TextColumn::make('country')->label('Land')->badge(),
                Tables\Columns\TextColumn::make('esg_score')
                    ->label('ESG')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state >= 75 => 'success',
                        $state >= 50 => 'warning',
                        default      => 'danger',
                    }),
                Tables\Columns\TextColumn::make('risk_level')
                    ->label('Risiko')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'low'    => 'success',
                        'medium' => 'warning',
                        default  => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'low' => 'Niedrig', 'medium' => 'Mittel', default => 'Hoch',
                    }),
                Tables\Columns\IconColumn::make('active')->label('Aktiv')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Aktualisiert')->dateTime('d.m.Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')->label('Land')
                    ->options(['CH' => 'Schweiz', 'DE' => 'Deutschland', 'AT' => 'Österreich']),
                Tables\Filters\SelectFilter::make('risk_level')->label('Risiko')
                    ->options(['low' => 'Niedrig', 'medium' => 'Mittel', 'high' => 'Hoch']),
                Tables\Filters\Filter::make('high_esg')
                    ->label('ESG ≥ 60')
                    ->query(fn ($query) => $query->where('esg_score', '>=', 60)),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('esg_score', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit'   => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}

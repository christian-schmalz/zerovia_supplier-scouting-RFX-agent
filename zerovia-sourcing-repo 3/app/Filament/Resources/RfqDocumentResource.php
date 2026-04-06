<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RfqDocumentResource\Pages;
use App\Models\RfqDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RfqDocumentResource extends Resource
{
    protected static ?string $model = RfqDocument::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sourcing';
    protected static ?string $navigationLabel = 'RFQ-Archiv';
    protected static ?string $modelLabel = 'RFQ-Dokument';
    protected static ?string $pluralModelLabel = 'RFQ-Dokumente';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Referenz')->schema([
                Forms\Components\TextInput::make('reference_nr')->label('Referenznummer')->disabled(),
                Forms\Components\Select::make('user_id')->relationship('user', 'name')->label('Ersteller')->disabled(),
                Forms\Components\TextInput::make('location')->label('Standort'),
                Forms\Components\TextInput::make('search_radius_km')->label('Suchradius (km)')->numeric(),
                Forms\Components\TextInput::make('annual_volume_chf')->label('Jahresvolumen (CHF)')->numeric(),
            ])->columns(2),

            Forms\Components\Section::make('RFQ-Text')->schema([
                Forms\Components\Textarea::make('rfq_text')
                    ->label('Dokument')
                    ->rows(30)
                    ->fontFamily('mono')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_nr')
                    ->label('Referenz')->searchable()->weight('semibold')
                    ->copyable()->copyMessage('Kopiert'),
                Tables\Columns\TextColumn::make('location')->label('Standort'),
                Tables\Columns\TextColumn::make('recipients_count')
                    ->label('Empfänger')
                    ->counts('recipients')
                    ->badge()->color('primary'),
                Tables\Columns\IconColumn::make('is_sent')
                    ->label('Versandt')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->sent_at !== null),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Versandt am')->dateTime('d.m.Y H:i')->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Erstellt')->dateTime('d.m.Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('sent')
                    ->label('Nur versandte')
                    ->query(fn ($q) => $q->whereNotNull('sent_at')),
                Tables\Filters\Filter::make('drafts')
                    ->label('Nur Entwürfe')
                    ->query(fn ($q) => $q->whereNull('sent_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (RfqDocument $record) => response()->streamDownload(
                        fn () => print($record->rfq_text),
                        $record->reference_nr . '.txt'
                    )),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRfqDocuments::route('/'),
            'view'   => Pages\ViewRfqDocument::route('/{record}'),
            'edit'   => Pages\EditRfqDocument::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\CarResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TempoRelationManager extends RelationManager
{
    protected static string $relationship = 'tempos';
    protected static ?string $title = 'Riwayat Pajak & Service';

    public function form(Form $form): Form
    {
        // Form untuk membuat/mengedit data tempo langsung dari sini
        return $form
            ->schema([
                Forms\Components\Select::make('perawatan')
                    ->options([
                        'pajak' => 'Pajak STNK',
                        'service' => 'Service Berkala',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('jatuh_tempo')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('perawatan')
            ->columns([
                Tables\Columns\TextColumn::make('perawatan')
                    ->label('Jenis Perawatan')
                    ->badge()
                    ->colors([
                        'primary' => 'pajak',
                        'danger' => 'service',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pajak' => 'Pajak STNK',
                        'service' => 'Service Berkala',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            ->defaultSort('jatuh_tempo', 'desc');
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CostResource\Pages;
use App\Filament\Resources\CostResource\RelationManagers;
use App\Models\Cost;
use App\Models\Pengeluaran;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CostResource extends Resource
{
    protected static ?string $model = Pengeluaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Data ';
    protected static ?string $label = 'Pengeluaran';
    protected static ?string $pluralLabel = 'Data Pengeluaran';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('nama_pengeluaran')
                    ->label('Nama Pengeluaran')
                    ->required(),
                DatePicker::make('tanggal_pengeluaran')
                    ->label('Tanggal Pengeluaran')
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi Pengeluaran')
                    ->rows(3)
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('pembayaran')
                    ->label('Nominal Pembayaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_pengeluaran')->label('Nama Pengeluaran'),
                TextColumn::make('description')->label('Deskripsi Pengeluaran'),
                TextColumn::make('tanggal_pengeluaran')->label('Tanggal Pengeluaran')->date('d M Y'),
                TextColumn::make('pembayaran')->label('Pembayaran')->money('IDR'),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCosts::route('/'),
            'create' => Pages\CreateCost::route('/create'),
            'edit' => Pages\EditCost::route('/{record}/edit'),
        ];
    }
}

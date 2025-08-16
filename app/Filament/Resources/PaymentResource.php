<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenaltyResource\Pages;
use App\Filament\Resources\PenaltyResource\RelationManagers;
use App\Models\Penalty;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenaltyResource extends Resource
{
    protected static ?string $model = Penalty::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Klaim Garasi';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('booking_id')
                    ->label('Booking')
                    ->relationship('booking', 'id')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $tanggalKeluar = \Carbon\Carbon::parse($record->tanggal_keluar)->format('d M Y');
                        $tanggalKembali = \Carbon\Carbon::parse($record->tanggal_kembali)->format('d M Y');

                        return '#BK' . str_pad($record->id, 3, '0', STR_PAD_LEFT) .
                            ' - ' . $record->customer->nama .
                            ' - ' . $record->car->nopol .
                            ' - ' . $record->car->nama_mobil .
                            ', ' . $tanggalKeluar . ' s/d ' . $tanggalKembali;
                    })

                    ->required()
                    ->selectablePlaceholder(),


                Select::make('klaim')
                    ->label('Klaim Garasi')
                    ->options([
                        'baret' => 'Baret',
                        'bbm' => 'BBM',
                        'overtime' => 'Overtime',
                        'no_penalty' => 'Tidak Ada Denda',
                    ])
                    ->default('no_penalty')
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3),

                TextInput::make('amount')
                    ->label('Jumlah Denda (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking.id')->label('ID')->alignCenter(),
                TextColumn::make('klaim')
                    ->label('Klaim Garasi')
                    ->toggleable()
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'bbm',
                        'danger' => 'baret',
                        'danger' => 'overtime',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'bbm' => 'BBM',
                        'overtime' => 'Overtime',
                        'baret' => 'Baret/Kerusakan',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('amount')->money('IDR')->label('Nominal')->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')->date('d M Y')->alignCenter(),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenalties::route('/'),
            'create' => Pages\CreatePenalty::route('/create'),
            'edit' => Pages\EditPenalty::route('/{record}/edit'),
        ];
    }
}

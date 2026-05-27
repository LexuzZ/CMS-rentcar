<?php

namespace App\Filament\Resources;

use App\Models\Car;
use Filament\Forms;
use Filament\Tables;
use App\Models\CarInstallment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\CarInstallmentResource\Pages;
use App\Models\Pengeluaran;
use Filament\Notifications\Notification;

class CarInstallmentResource extends Resource
{
    protected static ?string $model = CarInstallment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Laporan & Accounting';

    protected static ?string $label = 'Cicilan Mobil';

    protected static ?string $pluralLabel = 'Cicilan Mobil';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make(2)
                    ->schema([

                        Select::make('car_id')
                            ->label('Mobil')
                            ->relationship('car', 'nopol')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('nama_leasing')
                            ->label('Leasing'),

                        DatePicker::make('tanggal_mulai')
                            ->required(),

                        DatePicker::make('jatuh_tempo')
                            ->required(),

                        TextInput::make('total_hutang')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        TextInput::make('nominal_cicilan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        TextInput::make('tenor')
                            ->numeric()
                            ->suffix('Bulan')
                            ->required(),

                        TextInput::make('cicilan_ke')
                            ->numeric()
                            ->required(),

                        Select::make('status')
                            ->options([
                                'berjalan' => 'Berjalan',
                                'lunas' => 'Lunas',
                                'macet' => 'Macet',
                            ])
                            ->required(),

                        Textarea::make('catatan')
                            ->columnSpanFull(),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('car.nopol')
                    ->label('Mobil')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama_leasing')
                    ->label('Leasing'),

                Tables\Columns\TextColumn::make('total_hutang')
                    ->money('IDR', true),

                Tables\Columns\TextColumn::make('nominal_cicilan')
                    ->money('IDR', true),

                Tables\Columns\TextColumn::make('cicilan_ke')
                    ->label('Cicilan'),

                Tables\Columns\TextColumn::make('tenor')
                    ->label('Tenor'),

                Tables\Columns\TextColumn::make('sisa_hutang')
                    ->money('IDR', true)
                    ->color('danger'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'lunas',
                        'warning' => 'berjalan',
                        'danger' => 'macet',
                    ]),
            ])
            ->actions([


                Tables\Actions\Action::make('tambah_pengeluaran')
                    ->label('Tambah Pengeluaran')
                    ->icon('heroicon-o-banknotes')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn($record) => !$record->pengeluaran)
                    ->action(function ($record) {

                        Pengeluaran::create([
                            'car_installment_id' => $record->id,

                            'tanggal_pengeluaran' => now(),

                            'nama_pengeluaran' => 'cicilan',

                            'description' =>
                                'Pembayaran cicilan mobil ' .
                                $record->car?->nopol,

                            'pembayaran' =>
                                $record->nominal_cicilan,

                            'status' => 'paid',
                        ]);

                        Notification::make()
                            ->title('Berhasil ditambahkan ke pengeluaran')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),


            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarInstallments::route('/'),
            'create' => Pages\CreateCarInstallment::route('/create'),
            // 'view' => Pages\ViewCarInstallment::route('/{record}'),
            'edit' => Pages\EditCarInstallment::route('/{record}/edit'),
        ];
    }
}

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
use Illuminate\Database\Eloquent\Builder;

class CarInstallmentResource extends Resource
{
    protected static ?string $model = CarInstallment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Manajemen Mobil';

    protected static ?string $label = 'Cicilan Mobil';

    protected static ?string $pluralLabel = 'Cicilan Mobil';
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('car', function (Builder $query) {
                $query->where('garasi', 'SPT');
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make(2)
                    ->schema([

                        Forms\Components\Select::make('car_id')
                            ->label('Mobil')
                            ->relationship(
                                name: 'car',
                                titleAttribute: 'nopol',
                                modifyQueryUsing: fn(Builder $query) => $query
                                    ->where('garasi', 'SPT')
                                    ->with('carModel')
                            )
                            ->getOptionLabelFromRecordUsing(fn(Car $record) => "{$record->carModel->name} ({$record->nopol})")
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return Car::query()
                                    ->where('garasi', 'SPT')
                                    ->where(function ($query) use ($search) {
                                        $query->where('nopol', 'like', "%{$search}%")
                                            ->orWhereHas('carModel', fn($q) => $q->where('name', 'like', "%{$search}%"));
                                    })
                                    ->with('carModel')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn($car) => [
                                        $car->id => "{$car->carModel->name} ({$car->nopol})"
                                    ]);
                            })
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

                Tables\Columns\TextColumn::make('car.carModel.name')
                    ->label('Mobil')
                    ->description(fn(CarInstallment $record): string => $record->car->nopol)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('car.carModel', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('car', fn($q) => $q->where('nopol', 'like', "%{$search}%"));
                    }),

                Tables\Columns\TextColumn::make('nama_leasing')
                    ->label('Leasing')
                    // ->center()
                    ->wrap()->width(25),

                Tables\Columns\TextColumn::make('total_hutang')
                    ->alignCenter()->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('nominal_cicilan')
                    ->label('Cicilan')
                    ->alignCenter()->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),



                Tables\Columns\TextColumn::make('tenor')
                    // ->center()
                    ->label('Tenor'),

                Tables\Columns\TextColumn::make('sisa_hutang')
                    ->alignCenter()->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('danger'),

                Tables\Columns\BadgeColumn::make('status')
                    // ->center()
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

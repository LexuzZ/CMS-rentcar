<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Pemesanan';
    protected static ?string $pluralLabel = 'Pemesanan Sewa';

    protected static function calculatePrice(callable $set, callable $get)
    {
        $tanggalKeluar = $get('tanggal_keluar');
        $tanggalKembali = $get('tanggal_kembali');
        $hargaHarian = (int) $get('harga_harian');

        if (!$tanggalKeluar || !$tanggalKembali || !$hargaHarian) {
            $set('estimasi_biaya', 0);
            $set('total_hari', 0);
            return;
        }

        $start = \Carbon\Carbon::parse($tanggalKeluar);
        $end = \Carbon\Carbon::parse($tanggalKembali);
        $days = $start->diffInDays($end);
        $totalHari = $days >= 0 ? $days + 1 : 1; // Menghitung inklusif

        $set('total_hari', $totalHari);
        $set('estimasi_biaya', $hargaHarian * $totalHari);
    }


    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('id')
                    ->hidden()
                    ->dehydrated(),
                DatePicker::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::calculatePrice($set, $get))
                    ->rules([
                        function (Forms\Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $carId = $get('car_id');
                                $tanggalKembali = $get('tanggal_kembali');
                                $recordId = $get('id');

                                if (!$carId || !$value || !$tanggalKembali) {
                                    return;
                                }

                                $exists = \App\Models\Booking::where('car_id', $carId)
                                    ->when($recordId, fn ($q) => $q->where('id', '!=', $recordId))
                                    ->where(function ($query) use ($value, $tanggalKembali) {
                                        $query->whereBetween('tanggal_keluar', [$value, $tanggalKembali])
                                            ->orWhereBetween('tanggal_kembali', [$value, $tanggalKembali])
                                            ->orWhere(function ($q) use ($value, $tanggalKembali) {
                                                $q->where('tanggal_keluar', '<=', $value)
                                                    ->where('tanggal_kembali', '>=', $tanggalKembali);
                                            });
                                    })
                                    ->exists();

                                if ($exists) {
                                    $fail("Mobil sudah dibooking pada rentang tanggal tersebut.");
                                }
                            };
                        }
                    ]),
                DatePicker::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::calculatePrice($set, $get)),

                TimePicker::make('waktu_keluar')
                    ->label('Waktu Keluar')
                    ->nullable()
                    ->seconds(false),
                TimePicker::make('waktu_kembali')
                    ->label('Waktu Kembali')
                    ->nullable()
                    ->seconds(false),

                Select::make('brand_id')
                    ->label('Merek')
                    ->options(Brand::query()->pluck('name', 'id'))
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set) {
                        $set('car_model_id', null);
                        $set('car_id', null);
                    })
                    ->dehydrated(false),

                Select::make('car_model_id')
                    ->label('Nama Mobil')
                    ->options(fn (Forms\Get $get): array => CarModel::query()
                        ->where('brand_id', $get('brand_id'))
                        ->pluck('name', 'id')->all()
                    )
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('car_id', null))
                    ->dehydrated(false),

                Select::make('car_id')
                    ->label('Unit Mobil (No Polisi)')
                    ->options(fn (Forms\Get $get): array => Car::query()
                        ->where('car_model_id', $get('car_model_id'))
                        ->pluck('nopol', 'id')->all()
                    )
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($state && $car = \App\Models\Car::find($state)) {
                            $set('harga_harian', $car->harga_harian);
                        } else {
                            $set('harga_harian', 0);
                        }
                        static::calculatePrice($set, $get);
                    }),

                Select::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')->label('Nama Pelanggan')->required(),
                        Forms\Components\TextInput::make('no_telp')->label('No. HP')->tel()->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('alamat')->label('Alamat')->required(),
                        TextInput::make('ktp')->label('No KTP')->required()->unique(ignoreRecord: true),
                    ])
                    ->required(),

                Select::make('driver_id')
                    ->label('Staff Bertugas')
                    ->relationship('driver', 'nama')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Select::make('paket')
                    ->label('Paket Sewa')
                    ->options([
                        'lepas_kunci' => 'Lepas Kunci',
                        'dengan_driver' => 'Dengan Driver',
                        'tour' => 'Paket Tour',
                    ])
                    ->nullable(),

                // -- PENAMBAHAN FIELD BARU DIMULAI DI SINI --
                Textarea::make('lokasi_pengantaran')
                    ->label('Lokasi Pengantaran')
                    ->nullable()
                    ->rows(2)
                    ->columnSpanFull(),

                Textarea::make('lokasi_pengembalian')
                    ->label('Lokasi Pengembalian')
                    ->nullable()
                    ->rows(2)
                    ->columnSpanFull(),
                // -- PENAMBAHAN FIELD BARU SELESAI --

                TextInput::make('harga_harian')
                    ->label('Harga Harian')
                    ->prefix('Rp')
                    ->numeric()
                    ->dehydrated()
                    ->live()
                    ->afterStateUpdated(fn (callable $set, callable $get) => static::calculatePrice($set, $get)),

                TextInput::make('total_hari')
                    ->label('Total Hari Sewa')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),

                TextInput::make('estimasi_biaya')
                    ->label('Total Sewa')
                    ->prefix('Rp')
                    ->dehydrated(true)
                    ->required(),

                Select::make('status')
                    ->label('Status Pemesanan')
                    ->options([
                        'booking' => 'Booking',
                        'aktif' => 'Aktif',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    ])
                    ->default('booking')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'aktif',
                        'info' => 'booking',
                        'gray' => 'selesai',
                        'danger' => 'batal',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'aktif' => 'Aktif',
                        'booking' => 'Booking',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => ucfirst($state),
                    }),
                TextColumn::make('car.nopol')->label('No Polisi')->alignCenter()->searchable(),
                TextColumn::make('car.carModel.name')->label('Type Mobil')->alignCenter()->searchable(),
                TextColumn::make('car.carModel.brand.name')->label('Merk Mobil')->badge()->alignCenter()->searchable(),
                
                TextColumn::make('customer.nama')->label('Pelanggan')->alignCenter()->searchable(),
                TextColumn::make('tanggal_keluar')->label('Tanggal Keluar')->date('d M Y')->alignCenter(),
                TextColumn::make('tanggal_kembali')->label('Tanggal Kembali')->date('d M Y')->alignCenter(),
                TextColumn::make('estimasi_biaya')->label('Biaya')->money('IDR')->alignCenter(),

                // -- PENAMBAHAN KOLOM BARU DI SINI --
                TextColumn::make('lokasi_pengantaran')->label('Lokasi Antar')->toggleable(isToggledHiddenByDefault: true)->wrap(),
                TextColumn::make('lokasi_pengembalian')->label('Lokasi Kembali')->toggleable(isToggledHiddenByDefault: true)->wrap(),
            ])
            ->defaultSort('tanggal_keluar', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('status', 'booking')
            ->count();
    }
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Booking yang belum diproses';
    }
}

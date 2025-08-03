<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ramsey\Uuid\Type\Time;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Pemesanan';
    protected static ?string $pluralLabel = 'Pemesanan Sewa';

    protected static function calculatePrice(callable $set, callable $get = null)
    {
        $tanggalKeluar = $get('tanggal_keluar');
        $tanggalKembali = $get('tanggal_kembali');
        $hargaHarian = (int) $get('harga_harian');

        if (! $tanggalKeluar || ! $tanggalKembali || ! $hargaHarian) {
            $set('estimasi_biaya', 0);
            $set('total_hari', 0);
            return;
        }

        $start = \Carbon\Carbon::parse($tanggalKeluar);
        $end = \Carbon\Carbon::parse($tanggalKembali);
        $days = $start->diffInDays($end);
        $totalHari = $days > 0 ? $days : 1;

        $set('total_hari', $totalHari);
        $set('estimasi_biaya', $hargaHarian * $totalHari);
    }


    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('id')
                    ->hidden()
                    ->dehydrated(), // agar nilainya ikut dikirim ke validasi

                Select::make('car_id')
                    ->label('Mobil (No Polisi)')
                    ->relationship('car', 'nopol')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($state && $car = \App\Models\Car::find($state)) {
                            $set('harga_harian', $car->harga_harian);
                        } else {
                            $set('harga_harian', 0);
                        }

                        static::calculatePrice($set, $get); // ✅ sudah dikirim lengkap
                    }),
                Select::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('driver_id')
                    ->label('Sopir (Opsional)')
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



                DatePicker::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set, callable $get) => static::calculatePrice($set, $get))
                    ->rules([
                        function (\Filament\Forms\Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $carId = $get('car_id');
                                $tanggalKembali = $get('tanggal_kembali');
                                $recordId = $get('id'); // untuk pengecualian saat edit

                                if (! $carId || ! $value || ! $tanggalKembali) {
                                    return;
                                }

                                $exists = \App\Models\Booking::where('car_id', $carId)
                                    ->when($recordId, fn($q) => $q->where('id', '!=', $recordId))
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
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set, callable $get) => static::calculatePrice($set, $get)),


                TimePicker::make('waktu_keluar')
                    ->label('Waktu Keluar')
                    ->nullable()
                    ->seconds(false),
                TimePicker::make('waktu_kembali')
                    ->label('Waktu Kembali')
                    ->nullable()
                    ->seconds(false),
                // TextInput::make('estimasi_biaya')
                //     ->label('Harga Sewa')
                //     ->numeric()
                //     ->prefix('Rp')
                //     ->required(),
                TextInput::make('harga_harian')
                    ->label('Harga Harian')
                    ->prefix('Rp')
                    ->numeric()
                    ->dehydrated() // tetap dikirim ke backend walau reactive dan readonly
                    ->reactive()
                    ->afterStateHydrated(function (callable $set, $state, $record) {
                        if ($record && $record->car) {
                            $set('harga_harian', $record->car->harga_harian);
                        }
                    })
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Kalau harga_harian diganti manual, hitung ulang estimasi
                        \App\Filament\Resources\BookingResource::calculatePrice($set, $get);
                    }),


                TextInput::make('total_hari')
                    ->label('Total Hari Sewa')
                    ->numeric()
                    ->disabled() // jika ingin hanya tampilan, bukan input
                    ->dehydrated(), // agar tetap dikirim ke backend saat submit




                TextInput::make('estimasi_biaya')
                    ->label('Total Sewa')
                    ->prefix('Rp')
                    // ->disabled()
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
                TextColumn::make('car.nopol')->label('No Polisi')->alignCenter(),
                TextColumn::make('invoice.booking.car.merek')
                    ->label('Merk Mobil')
                    ->badge()
                    ->searchable()
                    ->alignCenter()
                    ->colors([
                        'info' => 'toyota',
                        'info' => 'mitsubishi',
                        'info' => 'suzuki',
                        'info' => 'daihatsu',
                        'info' => 'honda'
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'toyota' => 'Toyota',
                        'mitsubishi' => 'Mitsubishi',
                        'suzuki' => 'Suzuki',
                        'honda' => 'Honda',
                        'daihatsu' => 'Daihatsu',
                        default => ucfirst($state),
                    }),
                TextColumn::make('customer.nama')->label('Pelanggan')->alignCenter()->searchable(),
                // TextColumn::make('driver.nama')->label('Sopir')->toggleable(),
                TextColumn::make('tanggal_keluar')->label('Tanggal Keluar')->date('d M Y')->alignCenter(),
                TextColumn::make('tanggal_kembali')->label('Tanggal Kembali')->date('d M Y')->alignCenter(),
                TextColumn::make('waktu_keluar')->label('Waktu Keluar')->alignCenter()->time('H:i'),
                TextColumn::make('waktu_kembali')->label('Waktu Kembali')->alignCenter()->time('H:i'),
                TextColumn::make('estimasi_biaya')->label('Biaya')->money('IDR')->alignCenter(),
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
                    ->formatStateUsing(fn($state) => match ($state) {
                        'aktif' => 'Aktif',
                        'booking' => 'Booking',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => ucfirst($state),
                    }),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status ')
                    ->options([
                        'aktif' => 'Aktif',
                        'booking' => 'Booking',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',

                    ]),
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
        return static::getModel()::query(Booking::query()->latest())
            ->where('status', 'booking')
            ->count();
    }
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Booking yang belum diproses';
    }
}

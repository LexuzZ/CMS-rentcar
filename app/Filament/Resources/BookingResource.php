<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Pemesanan';
    protected static ?string $pluralLabel = 'Pemesanan Sewa';

    /**
     * Fungsi terpusat untuk menghitung total hari dan estimasi biaya.
     */
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

        // PERUBAHAN DI SINI: Menghitung selisih hari, bukan inklusif
        // Jika tanggal sama (0 hari), dihitung sebagai 1 hari sewa.
        $totalHari = $days > 0 ? $days : 1;

        $set('total_hari', $totalHari);
        $set('estimasi_biaya', $hargaHarian * $totalHari);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('id')->hidden()->dehydrated(),

                Forms\Components\DatePicker::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->required()
                    ->live() // Penting untuk memicu refresh pada dropdown mobil
                    ->afterStateUpdated(fn (callable $set, callable $get) => static::calculatePrice($set, $get)),

                Forms\Components\DatePicker::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->required()
                    ->live() // Penting untuk memicu refresh pada dropdown mobil
                    ->afterStateUpdated(fn (callable $set, callable $get) => static::calculatePrice($set, $get)),

                Forms\Components\TimePicker::make('waktu_keluar')->label('Waktu Keluar')->seconds(false),
                Forms\Components\TimePicker::make('waktu_kembali')->label('Waktu Kembali')->seconds(false),

                // -- Dependent Dropdown untuk Memilih Mobil --
                Forms\Components\Select::make('brand_id')
                    ->label('Merek')
                    ->options(Brand::query()->pluck('name', 'id'))
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set) {
                        $set('car_model_id', null);
                        $set('car_id', null);
                    })
                    ->dehydrated(false), // Field virtual, tidak disimpan

                Forms\Components\Select::make('car_model_id')
                    ->label('Nama Mobil')
                    ->options(fn (Forms\Get $get): array => CarModel::query()
                        ->where('brand_id', $get('brand_id'))
                        ->pluck('name', 'id')->all())
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('car_id', null))
                    ->dehydrated(false), // Field virtual, tidak disimpan

                Forms\Components\Select::make('car_id')
                    ->label('Unit Mobil Tersedia (No Polisi)')
                    ->options(function (Forms\Get $get): array {
                        $carModelId = $get('car_model_id');
                        $startDate = $get('tanggal_keluar');
                        $endDate = $get('tanggal_kembali');
                        $recordId = $get('id'); // Untuk mengecualikan booking saat ini (mode edit)

                        if (!$carModelId || !$startDate || !$endDate) {
                            return []; // Jangan tampilkan opsi jika data belum lengkap
                        }

                        // Query untuk mencari mobil yang TIDAK memiliki booking yang tumpang tindih
                        return Car::query()
                            ->where('car_model_id', $carModelId)
                            ->whereNotIn('status', ['perawatan', 'nonaktif'])
                            ->whereDoesntHave('bookings', function (Builder $query) use ($startDate, $endDate, $recordId) {
                                $query->where('id', '!=', $recordId) // Abaikan booking yang sedang diedit
                                    ->where(function (Builder $q) use ($startDate, $endDate) {
                                        $q->whereBetween('tanggal_keluar', [$startDate, $endDate])
                                          ->orWhereBetween('tanggal_kembali', [$startDate, $endDate])
                                          ->orWhere(function (Builder $subQ) use ($startDate, $endDate) {
                                              $subQ->where('tanggal_keluar', '<=', $startDate)
                                                   ->where('tanggal_kembali', '>=', $endDate);
                                          });
                                    });
                            })
                            ->pluck('nopol', 'id')->all();
                    })
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $car = Car::find($state);
                        $set('harga_harian', $car?->harga_harian ?? 0);
                        static::calculatePrice($set, $get);
                    }),
                // -- Batas Dependent Dropdown --

                Forms\Components\Select::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'nama')
                    ->searchable()->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')->label('Nama Pelanggan')->required(),
                        Forms\Components\TextInput::make('no_telp')->label('No. HP')->tel()->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('alamat')->label('Alamat')->required(),
                        Forms\Components\TextInput::make('ktp')->label('No KTP')->required()->unique(ignoreRecord: true),
                    ])
                    ->required(),

                Forms\Components\Select::make('driver_id')->label('Staff Bertugas')->relationship('driver', 'nama')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('paket')->label('Paket Sewa')->options(['lepas_kunci' => 'Lepas Kunci', 'dengan_driver' => 'Dengan Driver', 'tour' => 'Paket Tour'])->nullable(),

                Forms\Components\Textarea::make('lokasi_pengantaran')->label('Lokasi Pengantaran')->nullable()->rows(2)->columnSpanFull(),
                Forms\Components\Textarea::make('lokasi_pengembalian')->label('Lokasi Pengembalian')->nullable()->rows(2)->columnSpanFull(),

                Forms\Components\TextInput::make('harga_harian')->label('Harga Harian')->prefix('Rp')->numeric()->dehydrated()->live()->afterStateUpdated(fn (callable $set, callable $get) => static::calculatePrice($set, $get)),
                Forms\Components\TextInput::make('total_hari')->label('Total Hari Sewa')->numeric()->disabled()->dehydrated(),
                Forms\Components\TextInput::make('estimasi_biaya')->label('Total Sewa')->prefix('Rp')->dehydrated(true)->required(),

                Forms\Components\Select::make('status')
                    ->label('Status Pemesanan')
                    ->options(['booking' => 'Booking', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'batal' => 'Batal'])
                    ->default('booking')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()->alignCenter()
                    ->colors(['success' => 'aktif', 'info' => 'booking', 'gray' => 'selesai', 'danger' => 'batal'])
                    ->formatStateUsing(fn ($state) => match ($state) { 'aktif' => 'Aktif', 'booking' => 'Booking', 'selesai' => 'Selesai', 'batal' => 'Batal', default => ucfirst($state) }),
                Tables\Columns\TextColumn::make('car.nopol')->label('No Polisi')->alignCenter()->searchable(),
                Tables\Columns\TextColumn::make('car.carModel.name')->label('Type Mobil')->alignCenter()->searchable(),
                Tables\Columns\TextColumn::make('car.carModel.brand.name')->label('Merk Mobil')->badge()->alignCenter()->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')->label('Pelanggan')->alignCenter()->searchable(),
                Tables\Columns\TextColumn::make('tanggal_keluar')->label('Tanggal Keluar')->date('d M Y')->alignCenter(),
                Tables\Columns\TextColumn::make('tanggal_kembali')->label('Tanggal Kembali')->date('d M Y')->alignCenter(),
                Tables\Columns\TextColumn::make('estimasi_biaya')->label('Biaya')->money('IDR')->alignCenter(),
                Tables\Columns\TextColumn::make('lokasi_pengantaran')->label('Lokasi Antar')->toggleable(isToggledHiddenByDefault: true)->wrap(),
                Tables\Columns\TextColumn::make('lokasi_pengembalian')->label('Lokasi Kembali')->toggleable(isToggledHiddenByDefault: true)->wrap(),
            ])
            ->defaultSort('tanggal_keluar', 'desc')
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
        return static::getModel()::query()->where('status', 'booking')->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Booking yang belum diproses';
    }

    // -- KONTROL AKSES (superadmin, admin, staff) --

    public static function canViewAny(): bool
    {
        return true; // Semua peran bisa melihat
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->isSuperAdmin(); // Hanya superadmin
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->isSuperAdmin(); // Hanya superadmin
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Select::make('car_id')
                    ->label('Mobil (No Polisi)')
                    ->relationship('car', 'nopol')
                    ->searchable()
                    ->preload()
                    ->required(),

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
                    ->required(),

                DatePicker::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->required(),

                TextInput::make('estimasi_biaya')
                    ->label('Estimasi Biaya')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TimePicker::make('waktu_keluar')
                    ->label('Waktu Keluar')
                    ->nullable()
                    ->seconds(false),
                TimePicker::make('waktu_kembali')
                    ->label('Waktu Kembali')
                    ->nullable()
                    ->seconds(false),


                FileUpload::make('identity_file')
                    ->label('Upload KTP/SIM')
                    ->directory('identity_docs')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->nullable(),

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
                TextColumn::make('customer.nama')->label('Pelanggan')->alignCenter(),
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

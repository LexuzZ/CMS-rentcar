<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Car;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\Actions; // <-- 1. Import Infolist
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Pemesanan';

    protected static ?string $pluralLabel = 'Pemesanan Sewa';

    protected static function calculatePrice(callable $set, callable $get)
    {
        $tanggalKeluar = $get('tanggal_keluar');
        $tanggalKembali = $get('tanggal_kembali');
        $hargaHarian = (int) $get('harga_harian');

        if (! $tanggalKeluar || ! $tanggalKembali || ! $hargaHarian) {
            $set('estimasi_biaya', 0);
            $set('total_hari', 0);

            return;
        }

        $start = Carbon::parse($tanggalKeluar);
        $end = Carbon::parse($tanggalKembali);
        $days = $start->diffInDays($end);

        $totalHari = $days > 0 ? $days : 1;

        $set('total_hari', $totalHari);
        $set('estimasi_biaya', $hargaHarian * $totalHari);
    }

    public static function form(Form $form): Form
    {
        $isNotAdmin = ! Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor', 'staff']);

        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('id')->hidden()->dehydrated(),

                DatePicker::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->required()
                    ->live() // Penting untuk memicu refresh pada dropdown mobil
                    ->afterStateUpdated(fn (callable $set, callable $get) => static::calculatePrice($set, $get))
                    ->disabled($isNotAdmin),

                DatePicker::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->required()
                    ->live() // Penting untuk memicu refresh pada dropdown mobil
                    ->afterStateUpdated(fn (callable $set, callable $get) => static::calculatePrice($set, $get))
                    ->disabled($isNotAdmin),

                TimePicker::make('waktu_keluar')->label('Waktu Keluar')->seconds(false)->disabled($isNotAdmin),
                TimePicker::make('waktu_kembali')->label('Waktu Kembali')->seconds(false)->disabled($isNotAdmin),

                Select::make('garasi_type')
                    ->label('Pilih Garasi')
                    ->options([
                        'spt' => 'Garasi SPT',
                        'vendor' => 'Garasi Vendor',
                    ])
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('car_id', null)) // Kosongkan pilihan mobil
                    ->dehydrated(false), // Field ini virtual, tidak disimpan

                Select::make('car_id')
                    ->label('Unit Mobil Tersedia')
                    ->relationship(
                        name: 'car',
                        titleAttribute: 'nopol',
                        modifyQueryUsing: function (Builder $query, Forms\Get $get, ?Model $record) {
                            $startDate = $get('tanggal_keluar');
                            $endDate = $get('tanggal_kembali');
                            $garasiType = $get('garasi_type');

                            // Jika tidak ada tanggal / garasi → kosongkan dropdown
                            if (! $startDate || ! $endDate || ! $garasiType) {
                                $query->whereRaw('1 = 0');

                                return;
                            }

                            // Filter GARASI
                            if ($garasiType === 'spt') {
                                $query->where('garasi', 'SPT');
                            } else {
                                $query->where('garasi', '!=', 'SPT');
                            }

                            // Filter STATUS mobil
                            $query->whereNotIn('status', ['perawatan', 'nonaktif']);

                            // Filter mobils yang BERTABRAKAN dengan booking lain
                            $query->whereNotExists(function ($sub) use ($startDate, $endDate, $record) {
                                $sub->selectRaw(1)
                                    ->from('bookings')
                                    ->whereColumn('bookings.car_id', 'cars.id')
                                    ->whereIn('bookings.status', ['booking', 'disewa'])
                                    ->where('bookings.tanggal_keluar', '<', $endDate)
                                    ->where('bookings.tanggal_kembali', '>', $startDate);

                                if ($record) {
                                    $sub->where('bookings.id', '!=', $record->id);
                                }
                            });

                            // Saat edit → masukkan juga mobil yang dipakai saat ini
                            if ($record) {
                                $query->orWhere('id', $record->car_id);
                            }
                        }
                    )
                    ->getOptionLabelFromRecordUsing(function (Car $record) {
                        $label = "{$record->carModel->name} ({$record->nopol})";
                        if ($record->garasi !== 'SPT') {
                            $label .= " - {$record->garasi}";
                        }

                        return $label;
                    })
                    ->preload()
                    ->live()
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $car = Car::find($state);
                        $set('harga_harian', $car?->harga_harian ?? 0);
                        static::calculatePrice($set, $get);
                    })
                    ->disabled($isNotAdmin),

                Select::make('customer_id')
                    ->label('Penyewa')
                    ->relationship('customer', 'nama')
                    ->searchable()->preload()
                    ->createOptionForm([
                        TextInput::make('nama')->label('Nama Penyewa')->required(),
                        TextInput::make('no_telp')->label('No. HP')->tel()->required()->unique(ignoreRecord: true),
                        TextInput::make('alamat')->label('Alamat')->required(),
                        TextInput::make('ktp')->label('No KTP')->required()->unique(ignoreRecord: true),
                    ])
                    ->createOptionAction(fn (Forms\Components\Actions\Action $action) => $action->disabled($isNotAdmin))
                    ->required()
                    ->disabled($isNotAdmin),
                Select::make('source')->label('Sumber Orderan')->options([
                    'website' => 'Website',
                    'ro' => 'Repeat Order',
                    'instagram' => 'Instagram',
                    'facebook' => 'Facebook',
                    'cust_garasi' => 'Customer Garasi',
                    'agent' => 'Agent Garasi',
                    'tiket' => 'Tiket.com',
                    'traveloka' => 'Traveloka',
                    'tiktok' => 'Tiktok',
                ])->disabled($isNotAdmin)->required(),
                Select::make('driver_pengantaran_id')
                    ->label('Petugas Pengantaran')
                    ->relationship('driverPengantaran', 'nama')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Select::make('driver_pengembalian_id')
                    ->label('Petugas Pengembalian')
                    ->relationship('driverPengembalian', 'nama')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('paket')->label('Paket Sewa')->options([
                    'lepas_kunci' => 'Lepas Kunci',
                    'rr' => 'Rent to Rent',
                    'dengan_driver' => 'Dengan Driver',
                    'tour' => 'Paket Tour',
                    'kontrak' => 'Kontrak',
                    'perdua_belas_jam' => 'Per 12 Jam',
                ])->nullable()->disabled($isNotAdmin),

                Textarea::make('lokasi_pengantaran')->label('Lokasi Pengantaran')->nullable()->rows(2)->columnSpanFull()->disabled($isNotAdmin),
                Textarea::make('lokasi_pengembalian')->label('Lokasi Pengembalian')->nullable()->rows(2)->columnSpanFull()->disabled($isNotAdmin),
                TextInput::make('harga_harian')->label('Harga Harian')->prefix('Rp')->numeric()->dehydrated()->live()->afterStateUpdated(fn (callable $set, callable $get) => static::calculatePrice($set, $get))->disabled($isNotAdmin),
                TextInput::make('total_hari')->label('Total Hari Sewa')->numeric()->dehydrated(),
                TextInput::make('estimasi_biaya')->label('Total Sewa')->prefix('Rp')->dehydrated(true)->required()->disabled($isNotAdmin),

                // PERUBAHAN 1: Menyamakan nilai status
                Select::make('status')
                    ->label('Status Pemesanan')
                    ->options(['booking' => 'Booking', 'disewa' => 'Disewa', 'selesai' => 'Selesai', 'batal' => 'Batal'])
                    ->default('booking')
                    ->required(),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Langkah Berikutnya') // <-- Bagian tombol aksi cepat
                    ->schema([
                        Actions::make([
                            Action::make('createInvoice')
                                ->label('Terbitkan Tagihan')
                                ->icon('heroicon-o-document-plus')
                                ->color('primary')
                                ->visible(fn (Booking $record) => ! $record->invoice)
                                ->url(fn (Booking $record) => InvoiceResource::getUrl('create', ['booking_id' => $record->id])),
                            Action::make('addPayment')
                                ->label('Catat Pembayaran')
                                ->icon('heroicon-o-banknotes')
                                ->color('success')
                                ->visible(fn (Booking $record) => $record->invoice && ! $record->invoice->payment) // ✅ hanya muncul jika ada invoice, tapi belum ada payment
                                ->url(fn (Booking $record) => PaymentResource::getUrl('create', [
                                    'invoice_id' => $record->invoice->id,
                                ])),
                            Action::make('viewPayment')
                                ->label('Kelola Pembayaran')
                                ->icon('heroicon-o-pencil-square')
                                ->color('gray')
                                // Hanya muncul jika pembayaran SUDAH ada
                                ->visible(fn (Booking $record) => $record->invoice && $record->invoice->payment)
                                ->url(fn (Booking $record) => PaymentResource::getUrl('edit', ['record' => $record->invoice->payment->id])),
                            Action::make('addPenalty')
                                ->label('Tambah Klaim')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->color('danger')
                                ->url(fn (Booking $record) => PenaltyResource::getUrl('create', ['booking_id' => $record->id])),
                        ]),
                    ]),

                Section::make('Informasi Booking')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('status')
                                ->badge()
                                ->colors(['success' => 'disewa', 'info' => 'booking', 'gray' => 'selesai', 'danger' => 'batal'])
                                ->formatStateUsing(fn ($state) => match ($state) {
                                    'disewa' => 'Disewa',
                                    'booking' => 'Booking',
                                    'selesai' => 'Selesai',
                                    'batal' => 'Batal',
                                    default => ucfirst($state)
                                }),
                            TextEntry::make('paket')
                                ->badge()
                                ->formatStateUsing(fn ($state) => match ($state) {
                                    'lepas_kunci' => 'Lepas Kunci',
                                    'rr' => 'Rent to Rent',
                                    'dengan_driver' => 'Dengan Driver',
                                    'tour' => 'Paket Tour',
                                    'kontrak' => 'Kontrak',
                                    'perdua_belas_jam' => 'Per 12 Jam',
                                    default => '-'
                                }),
                            TextEntry::make('source')
                                ->badge()
                                ->formatStateUsing(fn ($state) => match ($state) {
                                    'website' => 'Website',
                                    'ro' => 'Repeat Order',
                                    'instagram' => 'Instagram',
                                    'facebook' => 'Facebook',
                                    'cust_garasi' => 'Customer Garasi',
                                    'agent' => 'Agent Garasi',
                                    'tiket' => 'Tiket.com',
                                    'traveloka' => 'Traveloka',
                                    'tiktok' => 'Tiktok',
                                    default => '-'
                                }),
                            TextEntry::make('driverPengantaran.nama')
                                ->label('Petugas Pengantaran')->badge(),

                            TextEntry::make('driverPengembalian.nama')
                                ->label('Petugas Pengembalian')->badge(),

                        ]),
                    ]),

                Section::make('Detail Jadwal & Biaya')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('tanggal_keluar')->dateTime('d M Y'),
                            TextEntry::make('tanggal_kembali')->dateTime('d M Y'),
                            TextEntry::make('total_hari')->suffix(' Hari'),
                            TextEntry::make('waktu_keluar')->dateTime('H:i')->suffix(' WITA'),
                            TextEntry::make('waktu_kembali')->dateTime('H:i')->suffix(' WITA'),
                            TextEntry::make('estimasi_biaya')->formatStateUsing(fn ($state) => 'Rp '.number_format($state, 0, ',', '.')),
                        ]),
                    ]),
                // Infolists\Components\Section::make('Rincian Biaya')
                //     ->schema([
                //         Infolists\Components\Grid::make(3)->schema([
                //             Infolists\Components\TextEntry::make('estimasi_biaya')
                //                 ->label('Biaya Sewa')
                //                 ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                //             Infolists\Components\TextEntry::make('invoice.pickup_dropOff')
                //                 ->label('Biaya Antar/Jemput')
                //                 ->formatStateUsing(fn($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.')),

                //             TextEntry::make('invoice.total_tagihan')
                //                 ->label('Total Tagihan')
                //                 ->money('IDR', true),

                //             TextEntry::make('invoice.total_denda')
                //                 ->label('Total Denda')
                //                 ->money('IDR', true),

                //             TextEntry::make('invoice.total_paid')
                //                 ->label('Total Dibayar')
                //                 ->money('IDR', true),

                //             TextEntry::make('invoice.sisa_pembayaran')
                //                 ->label('Sisa Pembayaran')
                //                 ->money('IDR', true),

                //             TextEntry::make('invoice.status')
                //                 ->badge()
                //                 ->colors([
                //                     'success' => 'lunas',
                //                     'danger' => 'belum_lunas',
                //                 ]),
                //         ]),
                //     ]),

                Section::make('Informasi Mobil')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('car.carModel.brand.name')->label('Merek')->badge(),
                            TextEntry::make('car.carModel.name')
                                ->label('Model')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => Str::upper($state)),
                            TextEntry::make('car.nopol')->label('No. Polisi')->badge(),
                        ]),
                    ]),

                Section::make('Informasi Pelanggan')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('customer.nama')->label('Nama Penyewa'),
                            TextEntry::make('customer.no_telp')->label('No. HP'),
                            TextEntry::make('customer.alamat')->label('Alamat'),
                        ]),
                    ]),

                Section::make('Informasi Lokasi')
                    ->schema([
                        TextEntry::make('lokasi_pengantaran'),
                        TextEntry::make('lokasi_pengembalian'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('car.nopol')->label('No Polisi')->alignCenter()->searchable(),
                TextColumn::make('car.carModel.name')->label('Nama Mobil')->searchable()->alignCenter()->wrap()->width(50),
                TextColumn::make('customer.nama')->label('Penyewa')->alignCenter()->searchable()->wrap() // <-- Tambahkan wrap agar teks turun
                    ->width(250),

                TextColumn::make('tanggal_keluar')->label('Tgl Keluar')->date('d M Y')->alignCenter(),
                TextColumn::make('tanggal_kembali')->label('Tgl Kembali')->date('d M Y')->alignCenter(),
                TextColumn::make('status')
                    ->badge()->alignCenter()
                    ->colors(['success' => 'disewa', 'info' => 'booking', 'gray' => 'selesai', 'danger' => 'batal'])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'disewa' => 'Disewa',
                        'booking' => 'Booking',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => ucfirst($state)
                    }),
                TextColumn::make('estimasi_biaya')->label('Biaya')->alignCenter()->formatStateUsing(fn ($state) => 'Rp '.number_format($state, 0, ',', '.')),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('bulan_ini')
                    ->label('Hanya Bulan Ini')
                    ->toggle() // Menjadikannya tombol on/off
                    ->default(true)
                    ->query(
                        fn (Builder $query) => $query
                            ->whereMonth('tanggal_keluar', Carbon::now()->month)
                            ->whereYear('tanggal_keluar', Carbon::now()->year)
                    ),
                SelectFilter::make('status')
                    ->options([
                        'booking' => 'Booking',
                        'disewa' => 'Disewa',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    ]),
                SelectFilter::make('garasi')
                    ->label('Garasi')
                    ->searchable()
                    ->options(
                        Car::query()
                            ->select('garasi')
                            ->distinct()
                            ->pluck('garasi', 'garasi')
                            ->toArray()
                    )
                    ->query(function (Builder $query, array $data) {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas(
                            'car',
                            fn ($q) => $q->where('garasi', $data['value'])
                        );
                    }),
                Filter::make('tanggal_keluar')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_keluar')
                            ->label('Tanggal Keluar'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['tanggal_keluar'],
                            fn (Builder $query, $date): Builder => $query->whereDate('tanggal_keluar', $date)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['tanggal_keluar']) {
                            return null;
                        }
                        $date = Carbon::parse($data['tanggal_keluar'])->isoFormat('D MMM Y');

                        return "Tanggal Keluar: {$date}";
                    }),

                // -- PENAMBAHAN FILTER BARU DI SINI --
                Filter::make('tanggal_kembali')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_kembali')
                            ->label('Tanggal Kembali'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['tanggal_kembali'],
                            fn (Builder $query, $date): Builder => $query->whereDate('tanggal_kembali', $date)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['tanggal_kembali']) {
                            return null;
                        }
                        $date = Carbon::parse($data['tanggal_kembali'])->isoFormat('D MMM Y');

                        return "Tanggal Kembali: {$date}";
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->label('')
                    ->tooltip('Detail Pesanan')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hiddenLabel()
                    ->button(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }


    // public static function getNavigationBadge(): ?string
    // {
    //     return Cache::remember(
    //         'booking_badge',
    //         60,
    //         fn () => static::getModel()::where('status', 'booking')->count()
    //     );
    // }

    // public static function getNavigationBadgeTooltip(): ?string
    // {
    //     return 'Booking yang belum diproses';
    // }

    // -- KONTROL AKSES (superadmin, admin, staff) --

    public static function canViewAny(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor', 'staff']);
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor', 'staff']);
    }

    public static function canEdit(Model $record): bool
    {
        // Semua peran bisa masuk ke halaman edit, tetapi field akan dikontrol di dalam form
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']); // Hanya superadmin
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->isSuperAdmin(); // Hanya superadmin
    }
}

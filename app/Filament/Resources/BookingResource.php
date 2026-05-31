<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Car;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid as ComponentsGrid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
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
    protected static ?int    $navigationSort = 1;
    protected static ?string $label         = 'Pemesanan';
    protected static ?string $pluralLabel   = 'Pemesanan Sewa';

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        return Cache::remember('booking_badge', 60, fn () =>
            static::getModel()::where('status', 'booking')->count() ?: null
        );
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Booking yang belum diproses';
    }

    // ─────────────────────────────────────────
    //  PRICE CALCULATOR (tidak diubah)
    // ─────────────────────────────────────────
    protected static function calculatePrice(callable $set, callable $get)
    {
        $tanggalKeluar  = $get('tanggal_keluar');
        $tanggalKembali = $get('tanggal_kembali');
        $hargaHarian    = (int) $get('harga_harian');

        if (!$tanggalKeluar || !$tanggalKembali || !$hargaHarian) {
            $set('estimasi_biaya', 0);
            $set('total_hari', 0);
            return;
        }

        $start    = Carbon::parse($tanggalKeluar);
        $end      = Carbon::parse($tanggalKembali);
        $days     = $start->diffInDays($end);
        $totalHari = $days > 0 ? $days : 1;

        $set('total_hari', $totalHari);
        $set('estimasi_biaya', $hargaHarian * $totalHari);
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        $isNotAdmin = !Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor', 'staff']);

        return $form->schema([

            Forms\Components\Section::make('Jadwal Sewa')
                ->icon('heroicon-o-calendar-days')
                ->columns(2)
                ->schema([
                    DatePicker::make('tanggal_keluar')
                        ->label('Tanggal Keluar')
                        ->required()->live()
                        ->afterStateUpdated(fn (callable $set, callable $get) => static::calculatePrice($set, $get))
                        ->disabled($isNotAdmin),

                    DatePicker::make('tanggal_kembali')
                        ->label('Tanggal Kembali')
                        ->required()->live()
                        ->afterStateUpdated(fn (callable $set, callable $get) => static::calculatePrice($set, $get))
                        ->disabled($isNotAdmin),

                    TimePicker::make('waktu_keluar')
                        ->label('Waktu Keluar')
                        ->seconds(false)
                        ->disabled($isNotAdmin),

                    TimePicker::make('waktu_kembali')
                        ->label('Waktu Kembali')
                        ->seconds(false)
                        ->disabled($isNotAdmin),
                ]),

            Forms\Components\Section::make('Unit Kendaraan')
                ->icon('heroicon-o-truck')
                ->columns(2)
                ->schema([
                    Select::make('garasi_type')
                        ->label('Pilih Garasi')
                        ->options(['spt' => 'Garasi SPT', 'vendor' => 'Garasi Vendor'])
                        ->live()
                        ->afterStateUpdated(fn (Forms\Set $set) => $set('car_id', null))
                        ->dehydrated(false),

                    Select::make('car_id')
                        ->label('Unit Mobil Tersedia')
                        ->relationship(
                            name: 'car',
                            titleAttribute: 'nopol',
                            modifyQueryUsing: function (Builder $query, Forms\Get $get, ?Model $record) {
                                $startDate  = $get('tanggal_keluar');
                                $endDate    = $get('tanggal_kembali');
                                $garasiType = $get('garasi_type');

                                if (!$startDate || !$endDate || !$garasiType) {
                                    $query->whereRaw('1 = 0'); return;
                                }
                                if ($garasiType === 'spt') {
                                    $query->where('garasi', 'SPT');
                                } else {
                                    $query->where('garasi', '!=', 'SPT');
                                }
                                $query->whereNotIn('status', ['perawatan', 'nonaktif']);
                                $query->whereNotExists(function ($sub) use ($startDate, $endDate, $record) {
                                    $sub->selectRaw(1)->from('bookings')
                                        ->whereColumn('bookings.car_id', 'cars.id')
                                        ->whereIn('bookings.status', ['booking', 'disewa'])
                                        ->where('bookings.tanggal_keluar', '<', $endDate)
                                        ->where('bookings.tanggal_kembali', '>', $startDate);
                                    if ($record) $sub->where('bookings.id', '!=', $record->id);
                                });
                                if ($record) $query->orWhere('id', $record->car_id);
                            }
                        )
                        ->getOptionLabelFromRecordUsing(function (Car $record) {
                            $label = "{$record->carModel->name} ({$record->nopol})";
                            if ($record->garasi !== 'SPT') $label .= " - {$record->garasi}";
                            return $label;
                        })
                        ->preload()->live()->searchable()->required()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $car = Car::find($state);
                            $set('harga_harian', $car?->harga_harian ?? 0);
                            static::calculatePrice($set, $get);
                        })
                        ->disabled($isNotAdmin),
                ]),

            Forms\Components\Section::make('Data Penyewa & Petugas')
                ->icon('heroicon-o-users')
                ->columns(2)
                ->schema([
                    Select::make('customer_id')
                        ->label('Penyewa')
                        ->relationship('customer', 'nama')
                        ->searchable()->preload(false)
                        ->createOptionForm([
                            TextInput::make('nama')->label('Nama Penyewa')->required(),
                            TextInput::make('no_telp')->label('No. HP')->tel()->required()->unique(ignoreRecord: true),
                            TextInput::make('alamat')->label('Alamat')->required(),
                            TextInput::make('ktp')->label('No KTP')->required()->unique(ignoreRecord: true),
                        ])
                        ->createOptionAction(fn (Forms\Components\Actions\Action $action) => $action->disabled($isNotAdmin))
                        ->required()
                        ->disabled($isNotAdmin),

                    Select::make('source')
                        ->label('Sumber Orderan')
                        ->options([
                            'website'     => 'Website',
                            'ro'          => 'Repeat Order',
                            'instagram'   => 'Instagram',
                            'facebook'    => 'Facebook',
                            'cust_garasi' => 'Customer Garasi',
                            'agent'       => 'Agent Garasi',
                            'tiket'       => 'Tiket.com',
                            'traveloka'   => 'Traveloka',
                            'tiktok'      => 'Tiktok',
                        ])
                        ->disabled($isNotAdmin)->required(),

                    Select::make('driver_pengantaran_id')
                        ->label('Petugas Pengantaran')
                        ->relationship('driverPengantaran', 'nama')
                        ->searchable()->preload()->nullable(),

                    Select::make('driver_pengembalian_id')
                        ->label('Petugas Pengembalian')
                        ->relationship('driverPengembalian', 'nama')
                        ->searchable()->preload()->nullable(),

                    Select::make('paket')
                        ->label('Paket Sewa')
                        ->options([
                            'lepas_kunci'      => 'Lepas Kunci',
                            'rr'               => 'Rent to Rent',
                            'dengan_driver'    => 'Dengan Driver',
                            'tour'             => 'Paket Tour',
                            'kontrak'          => 'Kontrak',
                            'perdua_belas_jam' => 'Per 12 Jam',
                        ])
                        ->nullable()->disabled($isNotAdmin),

                    Select::make('status')
                        ->label('Status Pemesanan')
                        ->options(['booking' => 'Booking', 'disewa' => 'Disewa', 'selesai' => 'Selesai', 'batal' => 'Batal'])
                        ->default('booking')->required(),

                    Textarea::make('lokasi_pengantaran')->label('Lokasi Pengantaran')->nullable()->rows(2)->columnSpanFull()->disabled($isNotAdmin),
                    Textarea::make('lokasi_pengembalian')->label('Lokasi Pengembalian')->nullable()->rows(2)->columnSpanFull()->disabled($isNotAdmin),
                ]),

            Forms\Components\Section::make('Rincian Biaya')
                ->icon('heroicon-o-banknotes')
                ->columns(3)
                ->schema([
                    TextInput::make('id')->hidden()->dehydrated(),
                    TextInput::make('harga_harian')->label('Harga Harian')->prefix('Rp')->numeric()->dehydrated()->live()
                        ->afterStateUpdated(fn (callable $set, callable $get) => static::calculatePrice($set, $get))
                        ->disabled($isNotAdmin),
                    TextInput::make('total_hari')->label('Total Hari Sewa')->numeric()->dehydrated(),
                    TextInput::make('estimasi_biaya')->label('Total Sewa')->prefix('Rp')->dehydrated(true)->required()->disabled($isNotAdmin),
                ]),
        ]);
    }

    // ─────────────────────────────────────────
    //  INFOLIST
    // ─────────────────────────────────────────
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Section::make('Langkah Berikutnya')
                ->icon('heroicon-o-bolt')
                ->schema([
                    Actions::make([
                        Action::make('createInvoice')
                            ->label('Terbitkan Tagihan')
                            ->icon('heroicon-o-document-plus')
                            ->color('primary')
                            ->visible(fn (Booking $record) => !$record->invoice)
                            ->url(fn (Booking $record) => InvoiceResource::getUrl('create', ['booking_id' => $record->id])),

                        Action::make('addPenalty')
                            ->label('Tambah Klaim')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->color('danger')
                            ->url(fn (Booking $record) => PenaltyResource::getUrl('create', ['booking_id' => $record->id])),
                    ]),
                ]),

            Section::make('Informasi Booking')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->icon(fn ($state) => match ($state) {
                                'booking' => 'heroicon-m-clock',
                                'disewa'  => 'heroicon-m-key',
                                'selesai' => 'heroicon-m-check-circle',
                                'batal'   => 'heroicon-m-x-circle',
                                default   => 'heroicon-m-question-mark-circle',
                            })
                            ->color(fn ($state) => match ($state) {
                                'disewa'  => 'success',
                                'booking' => 'info',
                                'selesai' => 'gray',
                                'batal'   => 'danger',
                                default   => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'disewa'  => 'Disewa',
                                'booking' => 'Booking',
                                'selesai' => 'Selesai',
                                'batal'   => 'Batal',
                                default   => ucfirst($state),
                            }),

                        TextEntry::make('paket')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'lepas_kunci'      => 'Lepas Kunci',
                                'rr'               => 'Rent to Rent',
                                'dengan_driver'    => 'Dengan Driver',
                                'tour'             => 'Paket Tour',
                                'kontrak'          => 'Kontrak',
                                'perdua_belas_jam' => 'Per 12 Jam',
                                default            => '-',
                            }),

                        TextEntry::make('source')
                            ->badge()
                            ->color('gray')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'website'     => 'Website',
                                'ro'          => 'Repeat Order',
                                'instagram'   => 'Instagram',
                                'facebook'    => 'Facebook',
                                'cust_garasi' => 'Customer Garasi',
                                'agent'       => 'Agent Garasi',
                                'tiket'       => 'Tiket.com',
                                'traveloka'   => 'Traveloka',
                                'tiktok'      => 'Tiktok',
                                default       => '-',
                            }),

                        TextEntry::make('driverPengantaran.nama')
                            ->label('Petugas Pengantaran')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('driverPengembalian.nama')
                            ->label('Petugas Pengembalian')
                            ->badge()
                            ->color('warning'),
                    ]),
                ]),

            Section::make('Detail Jadwal')
                ->icon('heroicon-o-calendar')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->dateTime('d M Y')
                            ->icon('heroicon-m-arrow-right-circle')
                            ->iconColor('success'),

                        TextEntry::make('tanggal_kembali')
                            ->label('Tanggal Kembali')
                            ->dateTime('d M Y')
                            ->icon('heroicon-m-arrow-left-circle')
                            ->iconColor('warning'),

                        TextEntry::make('total_hari')
                            ->label('Durasi')
                            ->suffix(' Hari')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('waktu_keluar')
                            ->label('Jam Keluar')
                            ->dateTime('H:i')
                            ->suffix(' WITA'),

                        TextEntry::make('waktu_kembali')
                            ->label('Jam Kembali')
                            ->dateTime('H:i')
                            ->suffix(' WITA'),

                        TextEntry::make('estimasi_biaya')
                            ->label('Estimasi Biaya')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->color('success')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                    ]),
                ]),

            Infolists\Components\Section::make('Rincian Biaya')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([
                        Infolists\Components\TextEntry::make('estimasi_biaya')
                            ->label('Biaya Sewa')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                        Infolists\Components\TextEntry::make('invoice.pickup_dropOff')
                            ->label('Biaya Antar/Jemput')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.')),

                        TextEntry::make('invoice.total_tagihan')
                            ->label('Total Tagihan')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->color('primary')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                        TextEntry::make('invoice.total_denda')
                            ->label('Total Denda')
                            ->color('danger')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                        TextEntry::make('invoice.total_paid')
                            ->label('Total Dibayar')
                            ->color('success')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                        TextEntry::make('invoice.sisa_pembayaran')
                            ->label('Sisa Pembayaran')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                    ]),
                ]),

            Section::make('Informasi Mobil')
                ->icon('heroicon-o-truck')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('car.carModel.brand.name')
                            ->label('Merek')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('car.carModel.name')
                            ->label('Model')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn (string $state): string => Str::upper($state)),

                        TextEntry::make('car.nopol')
                            ->label('No. Polisi')
                            ->badge()
                            ->color('warning')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold),
                    ]),
                ]),

            Section::make('Informasi Pelanggan')
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('customer.nama')
                            ->label('Nama Penyewa')
                            ->icon('heroicon-m-user')
                            ->weight(\Filament\Support\Enums\FontWeight::SemiBold),

                        TextEntry::make('customer.no_telp')
                            ->label('No. HP')
                            ->icon('heroicon-m-phone'),

                        TextEntry::make('customer.alamat')
                            ->label('Alamat')
                            ->icon('heroicon-m-map-pin'),
                    ]),
                ]),

            Section::make('Informasi Lokasi')
                ->icon('heroicon-o-map-pin')
                ->columns(2)
                ->schema([
                    TextEntry::make('lokasi_pengantaran')
                        ->label('Lokasi Pengantaran')
                        ->icon('heroicon-m-arrow-right-circle')
                        ->iconColor('success'),

                    TextEntry::make('lokasi_pengembalian')
                        ->label('Lokasi Pengembalian')
                        ->icon('heroicon-m-arrow-left-circle')
                        ->iconColor('warning'),
                ]),
        ]);
    }

    // ─────────────────────────────────────────
    //  TABLE
    // ─────────────────────────────────────────
    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([

                // Nomor booking
                // TextColumn::make('id')
                //     ->label('ID')
                //     ->formatStateUsing(fn ($state) => '#BK' . str_pad($state, 3, '0', STR_PAD_LEFT))
                //     ->badge()
                //     ->color('gray')
                //     ->sortable(),

                // Mobil + nopol
                TextColumn::make('car.carModel.name')
                    ->label('Kendaraan')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->description(fn (Booking $record): string => $record->car->nopol ?? '—')
                    ->searchable(),

                // Penyewa
                TextColumn::make('customer.nama')
                    ->label('Penyewa')
                    ->searchable()
                    ->description(fn (Booking $record): string =>
                        $record->customer->no_telp ?? '—'
                    )
                    ->wrap()
                    ->width(150),

                // Tanggal keluar & kembali dalam satu kolom
                TextColumn::make('tanggal_keluar')
                    ->label('Jadwal')
                    ->date('d M Y')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->iconColor('success')
                    ->description(fn (Booking $record): string =>
                        Carbon::parse($record->tanggal_kembali)->format('d M Y')
                    )
                    ->sortable(),

                // Durasi
                TextColumn::make('total_hari')
                    ->label('Durasi')
                    ->suffix(' hari')
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->toggleable(),

                // Status
                TextColumn::make('status')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn (string $state): string => match ($state) {
                        'booking' => 'heroicon-m-clock',
                        'disewa'  => 'heroicon-m-key',
                        'selesai' => 'heroicon-m-check-circle',
                        'batal'   => 'heroicon-m-x-circle',
                        default   => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'disewa'  => 'success',
                        'booking' => 'info',
                        'selesai' => 'gray',
                        'batal'   => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'disewa'  => 'Disewa',
                        'booking' => 'Booking',
                        'selesai' => 'Selesai',
                        'batal'   => 'Batal',
                        default   => ucfirst($state),
                    }),

                // Biaya
                TextColumn::make('estimasi_biaya')
                    ->label('Biaya')
                    ->alignEnd()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->color('success')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Filter::make('bulan_ini')
                    ->label('Hanya Bulan Ini')
                    ->toggle()
                    ->default(true)
                    ->query(fn (Builder $query) => $query
                        ->whereMonth('tanggal_keluar', Carbon::now()->month)
                        ->whereYear('tanggal_keluar', Carbon::now()->year)
                    )
                    ->indicateUsing(fn (array $data): ?string =>
                        $data['isActive']
                            ? 'Bulan ini: ' . now()->locale('id')->isoFormat('MMMM Y')
                            : null
                    ),

                SelectFilter::make('status')
                    ->options([
                        'booking' => 'Booking',
                        'disewa'  => 'Disewa',
                        'selesai' => 'Selesai',
                        'batal'   => 'Batal',
                    ]),

                SelectFilter::make('garasi')
                    ->label('Garasi')
                    ->searchable()
                    ->options(
                        Car::query()->select('garasi')->distinct()->pluck('garasi', 'garasi')->toArray()
                    )
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value']) return $query;
                        return $query->whereHas('car', fn ($q) => $q->where('garasi', $data['value']));
                    }),

                Filter::make('tanggal_keluar')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['tanggal_keluar'],
                            fn (Builder $q, $date) => $q->whereDate('tanggal_keluar', $date)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['tanggal_keluar']) return null;
                        return 'Keluar: ' . Carbon::parse($data['tanggal_keluar'])->isoFormat('D MMM Y');
                    }),

                Filter::make('tanggal_kembali')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_kembali')
                            ->label('Tanggal Kembali')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['tanggal_kembali'],
                            fn (Builder $q, $date) => $q->whereDate('tanggal_kembali', $date)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['tanggal_kembali']) return null;
                        return 'Kembali: ' . Carbon::parse($data['tanggal_kembali'])->isoFormat('D MMM Y');
                    }),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([
                    ViewAction::make()
                        ->label('Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info'),

                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->visible(fn () => Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor', 'staff'])),

                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->visible(fn () => Auth::user()->hasAnyRole(['superadmin', 'admin'])),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(\Filament\Support\Enums\ActionSize::Small)
                ->color('gray')
                ->button(),
            ])

            ->bulkActions([
                BulkActionGroup::make([]),
            ])

            ->striped()
            ->paginated([10, 25, 50])

            // Highlight baris sesuai status
            ->recordClasses(fn (Booking $record): string => match ($record->status) {
                'batal'  => 'opacity-60',
                'selesai' => 'opacity-75',
                default  => '',
            });
    }

    // ─────────────────────────────────────────
    //  PAGES & ACCESS
    // ─────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view'   => Pages\ViewBooking::route('/{record}'),
            'edit'   => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

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
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->isSuperAdmin();
    }
}

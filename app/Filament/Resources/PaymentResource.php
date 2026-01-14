<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    // protected static ?string $navigationGroup = 'Kelola Pesanan Sewa';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Pembayaran';
    protected static ?string $pluralLabel = 'Riwayat Pembayaran';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([

                Forms\Components\Select::make('invoice_id')
                    ->label('Faktur')
                    ->relationship('invoice', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn($record) => 'INV #' . $record->id . ' - ' . $record->booking->customer->nama
                    )
                    ->required()
                    ->searchable()
                    ->disabled(fn(string $operation) => $operation === 'edit'),

                DatePicker::make('tanggal_pembayaran')
                    ->label('Tanggal Pembayaran')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('pembayaran')
                    ->label('Jumlah Dibayar')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\Select::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->options([
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'tunai_transfer' => 'Tunai & Transfer',
                        'tunai_qris' => 'Tunai & QRIS',
                        'transfer_qris' => 'Transfer & QRIS',
                    ])
                    ->required(),

                // Forms\Components\FileUpload::make('proof')
                //     ->label('Bukti Pembayaran')
                //     ->directory('payment-proofs')
                //     ->image()
                //     ->maxSize(2048),
            ])
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                // Tables\Columns\TextColumn::make('invoice.id')->label('Faktur'),
                Tables\Columns\TextColumn::make('invoice.booking.customer.nama')->label('Penyewa')->searchable()->alignCenter()->wrap() // <-- Tambahkan wrap agar teks turun
                    ->width(150),
                Tables\Columns\TextColumn::make('invoice.booking.car.nopol')
                    ->label('No. Polisi')
                    ->searchable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('invoice.booking.car.carModel.name')
                    ->label('Mobil')
                    ->searchable()
                    ->wrap()->width(150)
                    ->alignCenter(),
                TextColumn::make('pembayaran')
                    ->label('Dibayar')
                    ->money('IDR', true),
                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal')
                    ->date('d M Y'),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->wrap()
                    ->width(150)
                    ->alignCenter()
                    ->colors([
                        'success' => 'tunai',
                        'info' => 'transfer',
                        'gray' => 'qris',
                        'warning' => ['tunai_transfer', 'tunai_qris', 'transfer_qris'],
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'tunai_transfer' => 'Tunai & Transfer',
                        'tunai_qris' => 'Tunai & QRIS',
                        'transfer_qris' => 'Transfer & QRIS',
                        default => ucfirst($state),
                    }),

            ])
            ->filters([
                // SelectFilter::make('status')
                //     ->label('Status')
                //     ->options([
                //         'lunas' => 'Lunas',
                //         'belum_lunas' => 'Belum Lunas',
                //     ]),
                SelectFilter::make('metode_pembayaran')
                    ->label('Metode')
                    ->options([
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'tunai_transfer' => 'Tunai & Transfer',
                        'tunai_qris' => 'Tunai & QRIS',
                        'transfer_qris' => 'Transfer & QRIS',
                    ]),

                Filter::make('tanggal_pembayaran')
                    ->form([
                        DatePicker::make('date')
                            ->label('Tanggal Pembayaran'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['date'],
                            fn(Builder $q, $date) => $q->whereDate('tanggal_pembayaran', $date)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return $data['date']
                            ? 'Tanggal Pembayaran: ' . \Carbon\Carbon::parse($data['date'])->isoFormat('D MMMM Y')
                            : null;
                    }),
                Filter::make('tanggal_pembayaran')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('month')
                                    ->label('Bulan')
                                    ->options(array_reduce(range(1, 12), function ($carry, $month) {
                                        $carry[$month] = Carbon::create(null, $month)->locale('id')->isoFormat('MMMM');
                                        return $carry;
                                    }, []))
                                    ->default(now()->month), // ✅ default bulan ini
                                Forms\Components\Select::make('year')
                                    ->label('Tahun')
                                    ->options(function () {
                                        $years = range(now()->year, now()->year - 5);
                                        return array_combine($years, $years);
                                    })
                                    ->default(now()->year), // ✅ default tahun ini
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['month'], fn(Builder $query, $month): Builder => $query->whereMonth('tanggal_pembayaran', $month))
                            ->when($data['year'], fn(Builder $query, $year): Builder => $query->whereYear('tanggal_pembayaran', $year));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['month'] && !$data['year']) {
                            return null;
                        }
                        $monthName = $data['month'] ? Carbon::create()->month((int) $data['month'])->isoFormat('MMMM') : '';
                        return 'Periode: ' . $monthName . ' ' . $data['year'];
                    })
                    ->columnSpan(2)->columns(2),

            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip('Hapus Pembayaran')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->hiddenLabel()
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::query()
    //         ->where('status', 'belum_lunas')
    //         ->count();
    // }

    // public static function getNavigationBadgeTooltip(): ?string
    // {
    //     return 'Pembayaran yang belum lunas';
    // }

    // -- KONTROL AKSES BARU (superadmin, admin, staff) --

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->isSuperAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->isSuperAdmin();
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}

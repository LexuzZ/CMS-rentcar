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
    protected static ?int    $navigationSort = 3;
    protected static ?string $label         = 'Pembayaran';
    protected static ?string $pluralLabel   = 'Riwayat Pembayaran';

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        $count = Payment::whereMonth('tanggal_pembayaran', now()->month)
            ->whereYear('tanggal_pembayaran', now()->year)
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pembayaran bulan ini';
    }

    // ─────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────
    private static function metodeOptions(): array
    {
        return [
            'tunai'          => 'Tunai',
            'transfer'       => 'Transfer',
            'qris'           => 'QRIS',
            'tunai_transfer' => 'Tunai & Transfer',
            'tunai_qris'     => 'Tunai & QRIS',
            'transfer_qris'  => 'Transfer & QRIS',
        ];
    }

    private static function metodeColor(string $state): string
    {
        return match ($state) {
            'tunai'          => 'success',
            'transfer'       => 'info',
            'qris'           => 'primary',
            'tunai_transfer',
            'tunai_qris',
            'transfer_qris'  => 'warning',
            default          => 'gray',
        };
    }

    private static function metodeIcon(string $state): string
    {
        return match ($state) {
            'tunai'          => 'heroicon-m-banknotes',
            'transfer'       => 'heroicon-m-building-library',
            'qris'           => 'heroicon-m-qr-code',
            'tunai_transfer',
            'tunai_qris',
            'transfer_qris'  => 'heroicon-m-arrows-right-left',
            default          => 'heroicon-m-credit-card',
        };
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Referensi Faktur')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\Select::make('invoice_id')
                        ->label('Faktur')
                        ->relationship(
                            'invoice',
                            'id',
                            fn($query) => $query
                                ->with(['booking.customer'])
                                ->where('status', 'belum_lunas')
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn(Invoice $record) =>
                            "INV #{$record->id} — {$record->booking->customer->nama}"
                        )
                        ->searchable()
                        ->required()
                        ->disabled(fn(string $operation) => $operation === 'edit')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Detail Pembayaran')
                ->icon('heroicon-o-banknotes')
                ->columns(2)
                ->schema([
                    DatePicker::make('tanggal_pembayaran')
                        ->label('Tanggal Pembayaran')
                        ->native(false)
                        ->default(now())
                        ->required(),

                    Forms\Components\Select::make('metode_pembayaran')
                        ->label('Metode Pembayaran')
                        ->options(self::metodeOptions())
                        ->required()
                        ->native(false),

                    Forms\Components\TextInput::make('pembayaran')
                        ->label('Jumlah Dibayar')
                        ->prefix('Rp')
                        ->numeric()
                        ->required()
                        ->columnSpanFull()
                        ->rules([
                            fn(Forms\Get $get) => function ($attribute, $value, $fail) use ($get) {
                                $invoice = Invoice::find($get('invoice_id'));
                                if ($invoice && $value > $invoice->sisa_pembayaran) {
                                    $fail('Jumlah pembayaran melebihi sisa tagihan.');
                                }
                            },
                        ]),
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

                // ID Faktur
                TextColumn::make('invoice.id')
                    ->label('Faktur')
                    ->formatStateUsing(fn($state) => '#INV' . str_pad($state, 3, '0', STR_PAD_LEFT))
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                // Penyewa + mobil
                Tables\Columns\TextColumn::make('invoice.booking.customer.nama')
                    ->label('Penyewa')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->searchable()
                    ->description(
                        fn(Payment $record): string => ($record->invoice->booking->car->carModel->name ?? '—') .
                            ' · ' . ($record->invoice->booking->car->nopol ?? '—')
                    ),

                // Jumlah
                TextColumn::make('pembayaran')
                    ->label('Jumlah')
                    ->alignEnd()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('success')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                // Tanggal
                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar')
                    ->color('gray'),

                // Metode — badge + ikon
                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn(string $state): string => self::metodeIcon($state))
                    ->color(fn(string $state): string => self::metodeColor($state))
                    ->formatStateUsing(fn($state): string => self::metodeOptions()[$state] ?? ucfirst($state)),
                TextColumn::make('proof')
                    ->label('Bukti')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state ? '✓ Ada' : '—')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray'),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                SelectFilter::make('metode_pembayaran')
                    ->label('Metode')
                    ->options(self::metodeOptions()),

                Filter::make('tanggal_harian')
                    ->label('Tanggal Tertentu')
                    ->form([
                        DatePicker::make('date')
                            ->label('Tanggal Pembayaran')
                            ->native(false),
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder =>
                        $query->when(
                            $data['date'],
                            fn(Builder $q, $date) => $q->whereDate('tanggal_pembayaran', $date)
                        )
                    )
                    ->indicateUsing(
                        fn(array $data): ?string =>
                        $data['date']
                            ? 'Tgl: ' . Carbon::parse($data['date'])->isoFormat('D MMMM Y')
                            : null
                    ),

                Filter::make('tanggal_pembayaran')
                    ->label('Periode Bulan & Tahun')
                    ->form([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('month')
                                ->label('Bulan')
                                ->options(array_reduce(range(1, 12), function ($carry, $month) {
                                    $carry[$month] = Carbon::create(null, $month)->locale('id')->isoFormat('MMMM');
                                    return $carry;
                                }, []))
                                ->default(now()->month),

                            Forms\Components\Select::make('year')
                                ->label('Tahun')
                                ->options(function () {
                                    $years = range(now()->year, now()->year - 5);
                                    return array_combine($years, $years);
                                })
                                ->default(now()->year),
                        ]),
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder =>
                        $query
                            ->when($data['month'], fn(Builder $q, $m) => $q->whereMonth('tanggal_pembayaran', $m))
                            ->when($data['year'],  fn(Builder $q, $y) => $q->whereYear('tanggal_pembayaran', $y))
                    )
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['month'] && !$data['year']) return null;
                        $monthName = $data['month']
                            ? Carbon::create()->month((int) $data['month'])->locale('id')->isoFormat('MMMM')
                            : '';
                        return 'Periode: ' . trim($monthName . ' ' . $data['year']);
                    }),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Pembayaran')
                    ->modalWidth('lg'),
                    // Tables\Actions\EditAction::make()
                    //     ->label('Edit')
                    //     ->icon('heroicon-o-pencil-square')
                    //     ->color('warning')
                    //     ->visible(fn() => Auth::user()->hasAnyRole(['superadmin', 'admin'])),

                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->visible(fn() => Auth::user()->isSuperAdmin()),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(\Filament\Support\Enums\ActionSize::Small)
                    ->color('gray')
                    ->button(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])

            ->striped()
            ->paginated([10, 25, 50]);
    }

    // ─────────────────────────────────────────
    //  PAGES & ACCESS
    // ─────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'edit'  => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

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
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}

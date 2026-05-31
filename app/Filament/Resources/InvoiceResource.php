<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers\PaymentsRelationManager;
use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Illuminate\Support\Facades\URL;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Faktur';
    protected static ?string $pluralLabel = 'Faktur Sewa';

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        $count = Invoice::where('sisa_pembayaran', '>', 0)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Faktur belum lunas';
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([

            Forms\Components\Section::make('Referensi Booking')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Select::make('booking_id')
                        ->label('Booking')
                        ->relationship(
                            'booking',
                            'id',
                            fn($query) => $query->with(['car.carModel', 'customer'])
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn($record) =>
                            "#{$record->id} — {$record->car?->nopol} ({$record->customer?->nama})"
                        )
                        ->searchable()
                        ->required()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Detail Faktur')
                ->icon('heroicon-o-banknotes')
                ->columns(2)
                ->schema([
                    DatePicker::make('tanggal_invoice')
                        ->label('Tanggal Invoice')
                        ->native(false)
                        ->required(),

                    TextInput::make('pickup_dropOff')
                        ->label('Biaya Antar / Jemput')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),

                    TextInput::make('total_tagihan')
                        ->label('Total Tagihan')
                        ->prefix('Rp')
                        ->numeric()
                        ->readOnly()
                        ->helperText('Dihitung otomatis dari sistem.'),
                ]),
        ]);
    }

    // ─────────────────────────────────────────
    //  INFOLIST
    // ─────────────────────────────────────────
    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist->schema([

            // ── Aksi ──
            Section::make('Aksi Invoice')
                ->icon('heroicon-o-bolt')
                ->headerActions([
                    Action::make('addPayment')
                        ->label('Tambah Pembayaran')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->visible(fn(Invoice $record) => $record->sisa_pembayaran > 0)
                        ->form([
                            DatePicker::make('tanggal_pembayaran')
                                ->label('Tanggal Pembayaran')
                                ->default(now())
                                ->required(),

                            TextInput::make('pembayaran')
                                ->label('Jumlah Pembayaran')
                                ->prefix('Rp')
                                ->numeric()
                                ->required()
                                ->rules([
                                    fn(Invoice $record) => function ($attribute, $value, $fail) use ($record) {
                                        if ($value > $record->sisa_pembayaran) {
                                            $fail('Jumlah pembayaran melebihi sisa tagihan.');
                                        }
                                    },
                                ]),

                            Select::make('metode_pembayaran')
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
                        ])
                        ->action(function (Invoice $record, array $data) {
                            $record->payments()->create($data);
                            $record->recalculate();
                        })
                        ->successNotificationTitle('Pembayaran berhasil ditambahkan'),

                    Action::make('download_pdf')
                        ->label('Unduh PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->url(fn(Invoice $record) => route('invoices.pdf.download', $record))
                        ->openUrlInNewTab(),

                    Action::make('copyInvoice')
                        ->label('Copy Tagihan')
                        ->icon('heroicon-o-clipboard-document')
                        ->color('gray')
                        ->modalHeading('Salin Detail Faktur')
                        ->modalContent(function (Invoice $record): View {
                            $booking = $record->booking;

                            if (!$booking) {
                                return view('filament.actions.copy-invoice', [
                                    'textToCopy' => 'Data booking tidak ditemukan.',
                                ]);
                            }

                            $totalDenda = $booking->penalties?->sum('amount') ?? 0;
                            $biayaSewa = $booking->estimasi_biaya ?? 0;
                            $pickupDropOff = $record->pickup_dropOff ?? 0;
                            $totalTagihan = $biayaSewa + $pickupDropOff + $totalDenda;
                            $dp = $record->total_paid ?? 0;
                            $sisaPembayaran = $record->sisa_pembayaran ?? 0;

                            $customerName = $booking->customer?->nama ?? '-';
                            $car = $booking->car;
                            $carDetails = $car
                                ? trim(
                                    ($car->carModel?->brand?->name ?? '') . ' ' .
                                    ($car->carModel?->name ?? '') .
                                    ($car->nopol ? " ({$car->nopol})" : '')
                                )
                                : '-';

                            $tglKeluar = $booking->tanggal_keluar
                                ? Carbon::parse($booking->tanggal_keluar)->isoFormat('D MMMM Y') : '-';
                            $tglKembali = $booking->tanggal_kembali
                                ? Carbon::parse($booking->tanggal_kembali)->isoFormat('D MMMM Y') : '-';

                            $text = [];
                            $text[] = "Halo *{$customerName}* 👋😊";
                            $text[] = "";
                            $text[] = "Berikut detail faktur sewa mobil Anda dari *Semeton Pesiar*:";
                            $text[] = "";
                            $text[] = "🧾 *No. Faktur:* #{$record->id}";
                            $text[] = "📅 *Tanggal:* " . Carbon::parse($record->tanggal_invoice)->isoFormat('D MMMM Y');
                            $text[] = "-----------------------------------";
                            $text[] = "🚗 *Mobil:* {$carDetails}";
                            $text[] = "⏳ *Durasi:* {$tglKeluar} - {$tglKembali} ({$booking->total_hari} hari)";
                            $text[] = "💰 *Biaya Sewa:* Rp " . number_format($biayaSewa, 0, ',', '.');
                            if ($pickupDropOff > 0) {
                                $text[] = "➡️⬅️ *Biaya Antar/Jemput:* Rp " . number_format($pickupDropOff, 0, ',', '.');
                            }
                            if ($totalDenda > 0) {
                                $text[] = "⚖️ *Denda / Klaim Garasi:* Rp " . number_format($totalDenda, 0, ',', '.');
                            }
                            $text[] = "-----------------------------------";
                            $text[] = "✉️ *Total Tagihan:* Rp " . number_format($totalTagihan, 0, ',', '.');
                            $text[] = "🔐 *Total Dibayar:* Rp " . number_format($dp, 0, ',', '.');
                            $text[] = "🔔 *Sisa Pembayaran:* *Rp " . number_format($sisaPembayaran, 0, ',', '.') . "*";
                            $text[] = "";
                            $text[] = "Mohon lakukan pembayaran ke salah satu rekening berikut:";
                            $text[] = "🏦 Mandiri: 1610006892835 a.n. ACHMAD MUZAMMIL";
                            $text[] = "🏦 BCA: 2320418758 a.n. SRI NOVYANA";
                            $text[] = "";
                            $text[] = "🙏 Terima kasih.";

                            return view('filament.actions.copy-invoice', [
                                'textToCopy' => implode("\n", $text),
                            ]);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false),
                ]),

            // ── Ringkasan Keuangan ──
            Section::make('Ringkasan Keuangan')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([

                        TextEntry::make('total_tagihan')
                            ->label('Tagihan Sewa')
                            ->icon('heroicon-m-document-text')
                            ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                            ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                        TextEntry::make('total_denda')
                            ->label('Klaim Garasi')
                            ->icon('heroicon-m-exclamation-triangle')
                            ->color(fn($state) => $state > 0 ? 'danger' : 'gray')
                            ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                        TextEntry::make('pickup_dropOff')
                            ->label('Biaya Antar/Jemput')
                            ->icon('heroicon-m-map-pin')
                            ->color(fn($state) => $state > 0 ? 'warning' : 'gray')
                            ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                        TextEntry::make('total_paid')
                            ->label('Total Dibayar')
                            ->icon('heroicon-m-check-circle')
                            ->color('success')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                        TextEntry::make('sisa_pembayaran')
                            ->label('Sisa Pembayaran')
                            ->icon('heroicon-m-clock')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                            ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                        TextEntry::make('status_lunas')
                            ->label('Status')
                            ->badge()
                            ->state(fn($record) => $record->sisa_pembayaran == 0 ? 'lunas' : 'belum_lunas')
                            ->icon(fn($state) => match ($state) {
                                'lunas' => 'heroicon-m-check-badge',
                                'belum_lunas' => 'heroicon-m-clock',
                                default => 'heroicon-m-question-mark-circle',
                            })
                            ->color(fn($state) => match ($state) {
                                'lunas' => 'success',
                                'belum_lunas' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn($state) => match ($state) {
                                'lunas' => 'Lunas',
                                'belum_lunas' => 'Belum Lunas',
                                default => ucfirst($state),
                            }),
                    ]),
                ]),

            // ── Informasi Booking ──
            Section::make('Informasi Booking')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([
                        TextEntry::make('id')
                            ->label('ID Faktur')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn($state) => '#INV' . str_pad($state, 3, '0', STR_PAD_LEFT)),

                        TextEntry::make('booking.id')
                            ->label('ID Booking')
                            ->badge()
                            ->color('gray')
                            ->formatStateUsing(fn($state) => '#BK' . str_pad($state, 3, '0', STR_PAD_LEFT)),

                        TextEntry::make('tanggal_invoice')
                            ->label('Tanggal Faktur')
                            ->date('d M Y')
                            ->icon('heroicon-m-calendar'),

                        TextEntry::make('booking.customer.nama')
                            ->label('Pelanggan')
                            ->icon('heroicon-m-user')
                            ->weight(\Filament\Support\Enums\FontWeight::SemiBold),

                        TextEntry::make('booking.car.carModel.name')
                            ->label('Mobil')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('booking.car.nopol')
                            ->label('No. Polisi')
                            ->badge()
                            ->color('warning')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold),
                    ]),
                ]),
        ]);
    }

    // ─────────────────────────────────────────
    //  TABLE
    // ─────────────────────────────────────────
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([

                // ID Faktur
                TextColumn::make('id')
                    ->label('Faktur')
                    ->formatStateUsing(fn($state) => '#INV' . str_pad($state, 3, '0', STR_PAD_LEFT))
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Penyewa + nopol
                TextColumn::make('booking.customer.nama')
                    ->label('Penyewa')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->searchable()
                    ->description(
                        fn(Invoice $record): string =>
                        ($record->booking->car->carModel->name ?? '—') .
                        ' · ' . ($record->booking->car->nopol ?? '—')
                    )
                    ->wrap()
                    ->width(150),

                // Tanggal invoice
                TextColumn::make('tanggal_invoice')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar')
                    ->color('gray')
                    ->toggleable(),

                // Total tagihan
                TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->alignEnd()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                // Total dibayar
                TextColumn::make('total_paid')
                    ->label('Dibayar')
                    ->alignEnd()
                    ->color('success')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                // Sisa pembayaran
                TextColumn::make('sisa_pembayaran')
                    ->label('Sisa')
                    ->alignEnd()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->formatStateUsing(
                        fn($state) => $state > 0
                        ? 'Rp ' . number_format($state, 0, ',', '.')
                        : '—'
                    ),

                // Status lunas
                TextColumn::make('status_lunas')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->state(fn($record) => $record->sisa_pembayaran == 0 ? 'lunas' : 'belum_lunas')
                    ->icon(fn(string $state) => match ($state) {
                        'lunas' => 'heroicon-m-check-badge',
                        'belum_lunas' => 'heroicon-m-clock',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn(string $state) => match ($state) {
                        'lunas' => 'success',
                        'belum_lunas' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                        default => ucfirst($state),
                    }),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\Filter::make('belum_lunas')
                    ->label('Belum Lunas')
                    ->toggle()
                    ->query(fn($query) => $query->where('sisa_pembayaran', '>', 0))
                    ->indicateUsing(
                        fn(array $data): ?string =>
                        $data['isActive'] ? '⚠ Belum lunas' : null
                    ),

                Tables\Filters\Filter::make('lunas')
                    ->label('Sudah Lunas')
                    ->toggle()
                    ->query(fn($query) => $query->where('sisa_pembayaran', '<=', 0))
                    ->indicateUsing(
                        fn(array $data): ?string =>
                        $data['isActive'] ? '✓ Sudah lunas' : null
                    ),

                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->toggle()
                    ->default(true)
                    ->query(
                        fn($query) => $query
                            ->whereMonth('tanggal_invoice', now()->month)
                            ->whereYear('tanggal_invoice', now()->year)
                    )
                    ->indicateUsing(
                        fn(array $data): ?string =>
                        $data['isActive']
                        ? 'Bulan ini: ' . now()->locale('id')->isoFormat('MMMM Y')
                        : null
                    ),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info'),

                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(\Filament\Support\Enums\ActionSize::Small)
                    ->color('gray')
                    ->button(),
            ])

            ->striped()
            ->paginated([10, 25, 50])

            ->recordClasses(
                fn(Invoice $record): string =>
                $record->sisa_pembayaran == 0
                ? 'opacity-70'
                : ''
            );
    }

    // ─────────────────────────────────────────
    //  RELATIONS & PAGES
    // ─────────────────────────────────────────
    public static function getRelations(): array
    {
        return [PaymentsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    // ─────────────────────────────────────────
    //  ACCESS
    // ─────────────────────────────────────────
    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
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
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}

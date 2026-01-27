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
// use Filament\Infolists\Components\Actions\Action;
use Illuminate\Support\Facades\URL;
use Filament\Infolists\Components\TextEntry;
// use Filament\Infolists\Components\View;
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

    /* =======================
     | FORM
     ======================= */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Select::make('booking_id')
                    ->label('Booking')
                    ->relationship(
                        'booking',
                        'id',
                        fn($query) => $query->with(['car.carModel', 'customer'])
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        "#{$record->id} - {$record->car?->nopol} ({$record->customer?->nama})"
                    )
                    ->searchable()
                    ->required(),

                DatePicker::make('tanggal_invoice')
                    ->label('Tanggal Invoice')
                    ->required(),

                TextInput::make('pickup_dropOff')
                    ->label('Biaya Antar / Jemput')
                    ->numeric()
                    ->default(0),

                TextInput::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->prefix('Rp')
                    ->numeric()
                    ->readOnly(),
            ]),
        ]);
    }

    /* =======================
     | INFOLIST (VIEW)
     ======================= */
    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist->schema([

            Section::make('Aksi Invoice')
                ->headerActions([

                    Action::make('download_pdf')
                        ->label('Unduh PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->url(
                            fn(Invoice $record) =>
                            route('invoices.pdf.download', $record)
                        )
                        ->openUrlInNewTab(),

                    Action::make('copyInvoice')
                        ->label('Copy Tagihan')
                        ->icon('heroicon-o-clipboard-document')
                        ->color('gray')
                        ->modalHeading('Salin Detail Faktur')
                        ->modalContent(function (Invoice $record): View {

                            $booking = $record->booking;

                            // ===============================
                            // SAFETY CHECK
                            // ===============================
                            if (!$booking) {
                                return view('filament.actions.copy-invoice', [
                                    'textToCopy' => 'Data booking tidak ditemukan.',
                                ]);
                            }

                            // ===============================
                            // HITUNGAN
                            // ===============================
                            $totalDenda = $booking->penalties?->sum('amount') ?? 0;

                            $biayaSewa = $booking->estimasi_biaya ?? 0;
                            $pickupDropOff = $record->pickup_dropOff ?? 0;

                            $totalTagihan = $biayaSewa + $pickupDropOff + $totalDenda;
                            $dp = $record->total_paid ?? 0;
                            $sisaPembayaran = $record->sisa_pembayaran ?? 0;

                            // ===============================
                            // DATA TAMBAHAN
                            // ===============================
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
                                ? Carbon::parse($booking->tanggal_keluar)->isoFormat('D MMMM Y')
                                : '-';

                            $tglKembali = $booking->tanggal_kembali
                                ? Carbon::parse($booking->tanggal_kembali)->isoFormat('D MMMM Y')
                                : '-';

                            // ===============================
                            // FORMAT TEXT
                            // ===============================
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

            /* =======================
             | RINGKASAN KEUANGAN
             ======================= */
            Section::make('Ringkasan Keuangan')
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([
                        TextEntry::make('total_tagihan')->label('Tagihan Sewa')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                        TextEntry::make('total_denda')->label('Klaim Garasi')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                        TextEntry::make('pickup_dropOff')->label('Biaya Ongkir')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                        TextEntry::make('total_paid')->label('Total Pembayaran')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))->color('success'),
                        TextEntry::make('sisa_pembayaran')->label('Sisa Payment')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))->color('danger'),
                        TextEntry::make('status')
                            ->badge()
                            ->state(
                                fn($record) =>
                                $record->sisa_pembayaran == 0 ? 'lunas' : 'belum_lunas'
                            )
                            ->colors([
                                'success' => 'lunas',
                                'danger' => 'belum_lunas',
                            ])
                            ->formatStateUsing(fn($state) => match ($state) {
                                'lunas' => 'Lunas',
                                'belum_lunas' => 'Belum Lunas',

                                default => ucfirst($state),
                            }),

                    ]),
                ]),

            Section::make('Informasi Booking')
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([
                        TextEntry::make('id')->label('ID Faktur'),
                        TextEntry::make('booking.id')->label('ID Booking'),
                        TextEntry::make('tanggal_invoice')->date('d M Y'),
                        TextEntry::make('booking.customer.nama')->label('Pelanggan'),
                        TextEntry::make('booking.car.carModel.name')->label('Mobil'),
                        TextEntry::make('booking.car.nopol')->label('No. Polisi'),
                    ]),
                ]),
        ]);
    }



    /* =======================
     | TABLE
     ======================= */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('booking.customer.nama')
                    ->label('Penyewa')
                    ->searchable()
                    ->alignCenter()
                    ->wrap()
                    ->width(150),

                TextColumn::make('booking.car.nopol')
                    ->label('Mobil'),

                TextColumn::make('total_paid')
                    ->label('Total Dibayar')
                    ->color('success')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('sisa_pembayaran')
                    ->label('Sisa')
                    ->color('danger')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),


                TextColumn::make('status')
                    ->badge()
                    ->alignCenter()
                    ->state(
                        fn($record) =>
                        $record->sisa_pembayaran == 0 ? 'lunas' : 'belum_lunas'
                    )
                    ->colors([
                        'success' => 'lunas',
                        'danger' => 'belum_lunas',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',

                        default => ucfirst($state),
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip('Detail Invoice')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hiddenLabel()
                    ->button(),

                Tables\Actions\EditAction::make()
                    ->tooltip('Ubah Faktur')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->hiddenLabel()
                    ->button(),
            ]);
    }

    /* =======================
     | RELATIONS
     ======================= */
    public static function getRelations(): array
    {
        return [
            PaymentsRelationManager::class,
        ];
    }

    /* =======================
     | PAGES
     ======================= */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    /* =======================
     | PERMISSIONS
     ======================= */
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

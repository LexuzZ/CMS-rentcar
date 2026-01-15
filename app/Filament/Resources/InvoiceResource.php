<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers\PaymentsRelationManager;
use App\Models\Invoice;
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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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
                    ->url(fn (Invoice $record) =>
                        route('invoices.pdf.download', $record)
                    )
                    ->openUrlInNewTab(),

                Action::make('copy_invoice')
                    ->label('Copy Teks Invoice')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->action(function (Invoice $record, $livewire) {
                        $livewire->dispatchBrowserEvent('copy-to-clipboard', [
                            'text' => self::invoiceText($record),
                        ]);
                    }),

                // Action::make('whatsapp')
                //     ->label('Kirim via WhatsApp')
                //     ->icon('heroicon-o-chat-bubble-left-right')
                //     ->color('success')
                //     ->url(fn (Invoice $record) =>
                //         'https://wa.me/' .
                //         preg_replace('/[^0-9]/', '', $record->booking->customer->no_hp) .
                //         '?text=' . urlencode(self::invoiceText($record))
                //     )
                //     ->openUrlInNewTab(),
            ]),

        /* =======================
         | RINGKASAN KEUANGAN
         ======================= */
        Section::make('Ringkasan Keuangan')
            ->schema([
                Infolists\Components\Grid::make(3)->schema([
                    TextEntry::make('total_tagihan')->money('IDR', true),
                    TextEntry::make('total_denda')->money('IDR', true),
                    TextEntry::make('total_paid')->money('IDR', true),
                    TextEntry::make('sisa_pembayaran')->money('IDR', true),
                    TextEntry::make('status')
                        ->badge()
                        ->colors([
                            'success' => 'lunas',
                            'danger' => 'belum_lunas',
                        ]),
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
                    TextColumn::make('booking.customer.nama')
                        ->label('Penyewa')
                        ->searchable()
                        ->wrap(),

                    TextColumn::make('booking.car.nopol')
                        ->label('Mobil'),

                    TextColumn::make('total_tagihan')
                        ->label('Total')
                        ->money('IDR', true),

                    TextColumn::make('sisa_pembayaran')
                        ->label('Sisa')
                        ->money('IDR', true),

                    TextColumn::make('status')
                        ->badge()
                        ->colors([
                                'success' => 'lunas',
                                'danger' => 'belum_lunas',
                            ]),
                ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                    Tables\Actions\ViewAction::make()
                        ->tooltip('Detail Faktur')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->hiddenLabel(),

                    Tables\Actions\EditAction::make()
                        ->tooltip('Ubah Faktur')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->hiddenLabel(),
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

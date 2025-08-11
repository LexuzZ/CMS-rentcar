<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Faktur';
    protected static ?string $pluralLabel = 'Faktur Sewa';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Select::make('booking_id')
                ->label('Booking')
                ->relationship('booking', 'id', fn($query) => $query->with('car', 'customer'))
                ->getOptionLabelFromRecordUsing(
                    fn($record) =>
                    $record->id . ' - ' . $record->car->nopol . ' (' . $record->customer->nama . ')'
                )
                ->selectablePlaceholder()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $booking = \App\Models\Booking::find($state);
                    $estimasi = $booking?->estimasi_biaya ?? 0;
                    $pickup = $get('pickup_dropOff') ?? 0;

                    $total = $estimasi + $pickup;

                    $set('total', $total);
                    $set('dp', 0);
                    $set('sisa_pembayaran', $total);
                }),

            DatePicker::make('tanggal_invoice')
                ->label('Tanggal Invoice')
                ->required(),

            TextInput::make('dp')
                ->label('Uang Muka')
                ->prefix('Rp')
                ->numeric()
                ->default(0)
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $total = $get('total') ?? 0;
                    $set('sisa_pembayaran', max($total - $state, 0));
                }),

            TextInput::make('pickup_dropOff')
                ->label('Biaya Pengantaran')
                ->reactive()
                ->prefix('Rp')
                ->numeric()
                ->default(0),

            TextInput::make('sisa_pembayaran')
                ->label('Sisa Pembayaran')
                ->prefix('Rp')
                ->numeric()
                ->readOnly()
                ->default(0),

            TextInput::make('total')
                ->label('Total Biaya')
                ->prefix('Rp')
                ->numeric()
                ->readOnly()
                ->required(),
        ]),
    ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('booking.id')->label('Booking')->alignCenter(),
            TextColumn::make('booking.customer.nama')->label('Pelanggan')->toggleable()->alignCenter(),
            TextColumn::make('booking.car.nopol')->label('Mobil')->alignCenter(),

            TextColumn::make('dp')->label('DP')->money('IDR')->alignCenter(),
            TextColumn::make('sisa_pembayaran')->label('Sisa')->money('IDR')->alignCenter(),
            TextColumn::make('pickup_dropOff')->label('Biaya Pengantaran')->money('IDR')->alignCenter(),
            TextColumn::make('total')->label('Total')->money('IDR')->toggleable()->alignCenter(),
            TextColumn::make('tanggal_invoice')->label('Tanggal')->date('d M Y')->alignCenter(),
        ])
            ->defaultSort('tanggal_invoice', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('download')
                    ->label('Unduh Invoice')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(Invoice $record) => route('invoices.pdf.download', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Semua peran bisa melihat daftar mobil
        return true;
    }

    public static function canCreate(): bool
    {
        // Hanya superadmin dan admin yang bisa membuat data baru
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        // Hanya superadmin dan admin yang bisa mengedit
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDelete(Model $record): bool
    {
        // Hanya superadmin dan admin yang bisa menghapus
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDeleteAny(): bool
    {
        // Hanya superadmin dan admin yang bisa hapus massal
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }
}

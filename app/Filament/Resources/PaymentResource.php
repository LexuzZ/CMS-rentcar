<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PaymentExporter;
use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Pembayaran';
    protected static ?string $pluralLabel = 'Riwayat Pembayaran';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Select::make('invoice_id')
                    ->label('Faktur')
                    ->relationship('invoice', 'id', fn($query) => $query->with('booking.customer'))
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        'INV #' . $record->id . ' - ' . $record->booking->customer->nama
                    )
                    ->selectablePlaceholder()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $invoice = \App\Models\Invoice::find($state);
                        $set('pembayaran', $invoice?->total ?? 0);
                    }),

                DatePicker::make('tanggal_pembayaran')
                    ->label('Tanggal Pembayaran')
                    ->required(),

                Select::make('metode_pembayaran')
                    ->label('Metode')
                    ->options([
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                    ])
                    ->required(),

                TextInput::make('pembayaran')
                    ->label('Jumlah')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->readOnly(), // Tidak bisa diubah manual

                FileUpload::make('proof')
                    ->label('Bukti Transfer')
                    ->directory('payments')
                    ->disk('public')
                    ->image()
                    ->visibility('public')
                    ->nullable(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                    ])
                    ->default('belum_lunas')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.id')->label('Faktur'),
                TextColumn::make('invoice.booking.customer.nama')->label('Pelanggan')->toggleable()->alignCenter(),
                TextColumn::make('tanggal_pembayaran')->label('Tanggal')->date('d M Y')->alignCenter(),
                TextColumn::make('pembayaran')->label('Jumlah')->money('IDR')->alignCenter(),
                TextColumn::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'tunai',
                        'info' => 'transfer',
                        'gray' => 'qris',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        default => ucfirst($state),
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'lunas',
                        'danger' => 'belum_lunas',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                        default => ucfirst($state),
                    })
            ])
            ->defaultSort('tanggal_pembayaran', 'desc')
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
    protected function getHeaderActions(): array
    {
        dd('getHeaderActions dipanggil');

        $selectedYear = $this->filterFormData['year'] ?? now()->year;

        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(route('exports.monthly-revenue-pdf', ['year' => $selectedYear]))
                ->openUrlInNewTab(),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query(Payment::query()->latest())
            ->where('status', 'belum_lunas')
            ->count();
    }
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pembayaran yang belum lunas';
    }
}

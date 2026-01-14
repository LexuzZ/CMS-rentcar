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
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Pembayaran';
    protected static ?string $pluralLabel = 'Riwayat Pembayaran';

    /**
     * ✅ Sinkronisasi otomatis data saat create / edit.
     */


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
                    ])
                    ->required(),

                Forms\Components\FileUpload::make('proof')
                    ->label('Bukti Pembayaran')
                    ->directory('payment-proofs')
                    ->image()
                    ->maxSize(2048),
            ])
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.id')
                    ->label('Invoice')
                    ->sortable(),

                TextColumn::make('invoice.booking.customer.nama')
                    ->label('Pelanggan')
                    ->searchable(),

                TextColumn::make('pembayaran')
                    ->label('Dibayar')
                    ->money('IDR', true),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->colors([
                        'success' => 'tunai',
                        'info' => 'transfer',
                        'gray' => 'qris',
                    ]),

                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal')
                    ->date('d M Y'),
            ])
            ->defaultSort('tanggal_pembayaran', 'desc');
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

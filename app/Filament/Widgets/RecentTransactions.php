<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactions extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Hari Ini';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = '1/3';
    public int | string $perPage = 10;

    protected static function table(Table $table): Table
{
    return $table
        ->query(fn () => Payment::query()->whereDate('tanggal_pembayaran', today())->latest('created_at'))
        ->columns([
            Tables\Columns\TextColumn::make('invoice.booking.customer.nama')->label('Penyewa')->alignCenter()->wrap()->width(200),
            Tables\Columns\TextColumn::make('pembayaran')->label('Nominal')->alignCenter()
                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->color(fn(Payment $record): string => $record->status === 'lunas' ? 'success' : 'danger'),
            Tables\Columns\TextColumn::make('metode_pembayaran')->label('Metode')->badge()->alignCenter()
                ->colors(['success' => 'tunai','info' => 'transfer','gray' => 'qris'])
                ->formatStateUsing(fn($state) => match ($state) {
                    'tunai' => 'Tunai',
                    'transfer' => 'Transfer',
                    'qris' => 'QRIS',
                    default => ucfirst($state),
                }),
        ])
        // set pilihan per-page yang tersedia dan default (pastikan 3 ada dalam array)
        ->paginated([3, 10, 25])
        ->defaultPaginationPageOption(3);
}


}

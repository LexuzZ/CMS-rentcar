<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactions extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Hari Ini';
    protected static ?int $sort = 2;

    protected static bool $isLazy = true;
    protected static ?int $pollingInterval = null;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 4,
        'lg' => 4,
    ];

    protected int|string|array $perPage = 2;

    protected function getTableQuery(): Builder
    {
        return Payment::query()
            ->with([
                    'invoice:id,booking_id,status',
                    'invoice.booking.customer:id,nama',
                ])
            ->whereDate('tanggal_pembayaran', today())
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('invoice.booking.customer.nama')
                ->label('Penyewa')
                ->alignCenter()
                ->wrap(),

            Tables\Columns\TextColumn::make('pembayaran')
                ->label('Nominal')
                ->alignCenter()
                ->money('IDR')
                ->color(
                    fn(Payment $record) =>
                    $record->invoice?->status === 'lunas'
                    ? 'success'
                    : 'danger'
                ),

            Tables\Columns\BadgeColumn::make('metode_pembayaran')
                ->label('Metode')
                ->alignCenter()
                ->colors([
                        'success' => 'tunai',
                        'info' => 'transfer',
                        'gray' => 'qris',
                        'primary' => 'tunai_transfer',
                        'warning' => 'tunai_qris',
                        'danger' => 'transfer_qris',
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


        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [2, 5];
    }
}

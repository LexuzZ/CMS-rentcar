<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class RecentTransactions extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Hari Ini';
    protected static ?int $sort = 2;

    protected static bool $isLazy = true;
    protected static ?int $pollingInterval = null;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => '4',
        'lg' => '4',
    ];

    protected int|string|array $perPage = 2;

    protected function getTableQuery(): Builder
    {
        return Cache::remember('recent_transactions_today', 60, function () {
            return Payment::query()
                ->select([
                    'id',
                    'invoice_id',
                    'pembayaran',
                    'metode_pembayaran',
                    'status',
                    'tanggal_pembayaran',
                    'created_at',
                ])
                ->with([
                    'invoice.booking.customer:id,nama',
                ])
                ->where('tanggal_pembayaran', today())
                ->latest('created_at');
        });
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
                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->color(
                    fn(Payment $record): string =>
                    $record->status === 'lunas' ? 'success' : 'danger'
                ),

            Tables\Columns\TextColumn::make('metode_pembayaran')
                ->label('Metode')
                ->badge()
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

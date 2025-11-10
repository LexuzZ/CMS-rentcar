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

    // Untuk column span widget di dashboard
    protected int|string|array $columnSpan = 8;

    // Untuk pagination table di dalam widget
    protected int | string | array $perPage = 3; // Ubah dari 10 menjadi 3

    protected function getTableQuery(): Builder
    {
        return Payment::query()
            ->whereDate('tanggal_pembayaran', today())
            ->latest('created_at');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('invoice.booking.customer.nama')
                ->label('Penyewa')
                ->alignCenter()
                ->wrap()
                ->width('33%'), // Atau ->width('1/3')

            Tables\Columns\TextColumn::make('pembayaran')
                ->label('Nominal')
                ->alignCenter()
                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->color(fn(Payment $record): string => $record->status === 'lunas' ? 'success' : 'danger')
                ->width('33%'), // Atau ->width('1/3')

            Tables\Columns\TextColumn::make('metode_pembayaran')
                ->label('Metode')
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
                })
                ->width('33%'), // Atau ->width('1/3')
        ];
    }

    // Opsional: Jika ingin custom pagination options
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [3]; // 3 sebagai default
    }
}

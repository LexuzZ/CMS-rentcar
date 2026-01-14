<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactions extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Hari Ini';
    protected static ?int $sort = 1; // Sesuaikan urutan widget di dashboard
    protected int|string|array $columnSpan = 'full';
    protected int|string|array $perPage = 5;

    protected function getTableQuery(): Builder
    {
        return Payment::query()
            ->with(['invoice.booking.customer']) // WAJIB eager load
            ->whereDate('tanggal_pembayaran', now()->toDateString())
            ->latest('tanggal_pembayaran');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tanggal_pembayaran')
                ->label('Tgl Pembayaran')
                ->date('d M Y')
                ->alignCenter(),

            Tables\Columns\TextColumn::make('invoice.booking.customer.nama')
                ->label('Penyewa')
                ->alignCenter()
                ->default('-')
                ->wrap()
                ->searchable(),

            Tables\Columns\TextColumn::make('pembayaran')
                ->label('Nominal')
                ->alignCenter()
                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

            Tables\Columns\TextColumn::make('metode_pembayaran')
                ->label('Metode')
                ->badge()
                ->alignCenter()
                ->formatStateUsing(fn($state) => match ($state) {
                    'tunai' => 'Tunai',
                    'transfer' => 'Transfer',
                    'qris' => 'QRIS',
                    default => ucfirst($state),
                }),
        ];
    }
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10]; // 3 sebagai default
    }
}


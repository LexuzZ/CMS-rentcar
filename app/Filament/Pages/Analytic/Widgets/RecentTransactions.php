<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactions extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Hari Ini';
    protected static ?int $sort = 5; // Sesuaikan urutan widget di dashboard
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        // PERBAIKAN: Query diubah untuk hanya mengambil data hari ini
        return Payment::query()
            ->whereDate('tanggal_pembayaran', today()) // Filter berdasarkan tanggal hari ini
            ->latest('created_at'); // Urutkan berdasarkan waktu pembuatan terbaru
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tanggal_pembayaran')
                ->label('Tanggal')
                ->date('d M Y H:i'), // Tambahkan format waktu
            Tables\Columns\TextColumn::make('pembayaran')
                ->label('Nominal')
                ->money('IDR')
                ->color(fn(Payment $record): string => $record->status === 'lunas' ? 'success' : 'danger'),
            Tables\Columns\TextColumn::make('invoice.booking.customer.nama')
                ->label('Nama Penyewa')
                ->searchable(),
            Tables\Columns\TextColumn::make('metode_pembayaran')
                ->label('Metode')
                ->badge()
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
        ];
    }
}


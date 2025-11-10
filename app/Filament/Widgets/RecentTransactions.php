<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactions extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Hari Ini';
    protected static ?int $sort = 2; // Sesuaikan urutan widget di dashboard
    protected int|string|array $columnSpan = 'one-third';

    // PERBAIKAN: Menambahkan pagination untuk 3 data per halaman
    protected static ?int $defaultPaginationPageSize = 3;

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
            // Tables\Columns\TextColumn::make('updated_at')
            //     ->label('Tgl Pembayaran')
            //     ->alignCenter()
            //     ->date('d M Y'), // Tambahkan format waktu
            Tables\Columns\TextColumn::make('invoice.booking.customer.nama')
                ->label('Penyewa')
                ->alignCenter()
                ->wrap()
                ->width(200),
                // ->searchable()
            Tables\Columns\TextColumn::make('pembayaran')
                ->label('Nominal')
                ->alignCenter()
                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->color(fn(Payment $record): string => $record->status === 'lunas' ? 'success' : 'danger'),

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
                }),
        ];
    }
}


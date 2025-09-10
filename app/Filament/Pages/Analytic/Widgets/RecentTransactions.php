<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use App\Models\Transaction; // Sesuaikan dengan model transaksi Anda
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactions extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Terbaru';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full'; // Agar memenuhi lebar

    protected function getTableQuery(): Builder
    {
        return Payment::query()
            ->latest('tanggal_pembayaran') // Urutkan berdasarkan tanggal terbaru
            ->limit(5); // Ambil 5 transaksi terbaru
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tanggal_pembayaran')
                ->label('Tanggal')
                ->date('d M Y'),
            Tables\Columns\TextColumn::make('pembayaran')
                ->label('Nominal')
                ->money('IDR') // Sesuaikan mata uang
                ->color(fn(Payment $record): string => $record->status === 'lunas' ? 'success' : 'danger'),
            Tables\Columns\TextColumn::make('invoice.booking.customer.nama') // Jika ada kolom nama merchant/pembayaran
                ->label('Nama Penyewa'),
            // Tables\Columns\TextColumn::make('method')
            //     ->label('METHOD'),
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

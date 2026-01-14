<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class Revenue extends TableWidget
{
    protected static ?string $heading = 'Pendapatan (Lunas)';
    protected int|string|array $columnSpan = '300px';

    protected function getTableQuery(): Builder
    {
        return Payment::query()
            ->select([
                'id',
                'invoice_id',
                'tanggal_pembayaran',
                'pembayaran',
            ])
            ->whereHas('invoice', function (Builder $query) {
                $query->where('status', 'lunas'); // 🔥 STATUS DI INVOICE
            })
            ->with([
                'invoice:id,status,booking_id',
                'invoice.booking:id,customer_id',
                'invoice.booking.customer:id,nama',
            ])
            ->latest('tanggal_pembayaran');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tanggal_pembayaran')
                ->label('Tanggal')
                ->date('d M Y')
                ->alignCenter(),

            Tables\Columns\TextColumn::make('invoice.booking.customer.nama')
                ->label('Penyewa')
                ->default('-')
                ->wrap()
                ->alignCenter()
                ->searchable(),

            Tables\Columns\TextColumn::make('pembayaran')
                ->label('Nominal')
                ->alignCenter()
                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->color('success'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('bulan_ini')
                ->label('Bulan Ini')
                ->toggle()
                ->default(true)
                ->query(fn (Builder $query) =>
                    $query->whereBetween('tanggal_pembayaran', [
                        now()->startOfMonth(),
                        now()->endOfMonth(),
                    ])
                ),

            SelectFilter::make('bulan')
                ->label('Bulan')
                ->options([
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ])
                ->query(fn (Builder $query, array $data) =>
                    $query->when(
                        $data['value'] ?? null,
                        fn ($q, $month) => $q->whereMonth('tanggal_pembayaran', $month)
                    )
                ),

            SelectFilter::make('tahun')
                ->label('Tahun')
                ->options(
                    collect(range(now()->year, now()->year - 5))
                        ->mapWithKeys(fn ($y) => [$y => $y])
                        ->toArray()
                )
                ->query(fn (Builder $query, array $data) =>
                    $query->when(
                        $data['value'] ?? null,
                        fn ($q, $year) => $q->whereYear('tanggal_pembayaran', $year)
                    )
                ),
        ];
    }

    // protected function getTableRecordsPerPage(): int
    // {
    //     return 5;
    // }
}

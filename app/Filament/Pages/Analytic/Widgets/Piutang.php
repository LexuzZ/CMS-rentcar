<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;

class Piutang extends TableWidget
{
    protected static ?string $heading = 'Piutang (Belum Lunas)';
    protected int|string|array $columnSpan = '300px';

    protected function getTableQuery(): Builder
    {
        return Invoice::query()
            ->where('status', 'belum_lunas')
            ->with([
                'booking.customer',
                'booking.penalty',
                'payments',
            ])
            ->latest('tanggal_invoice');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tanggal_invoice')
                ->label('Tanggal')
                ->date('d M Y')
                ->alignCenter(),

            Tables\Columns\TextColumn::make('booking.customer.nama')
                ->label('Penyewa')
                ->wrap()
                ->alignCenter()
                ->searchable(),

            Tables\Columns\TextColumn::make('total_tagihan')
                ->label('Total Tagihan')
                ->alignCenter()
                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

            Tables\Columns\TextColumn::make('total_paid')
                ->label('Sudah Dibayar')
                ->alignCenter()
                ->color('success')
                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

            Tables\Columns\TextColumn::make('sisa_pembayaran')
                ->label('Sisa')
                ->alignCenter()
                ->color('danger')
                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('bulan_ini')
                ->label('Hanya Bulan Ini')
                ->toggle()
                ->default(true)
                ->query(fn (Builder $query) =>
                    $query->whereBetween('tanggal_invoice', [
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
                        fn ($q, $month) =>
                            $q->whereMonth('tanggal_invoice', $month)
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
                        fn ($q, $year) =>
                            $q->whereYear('tanggal_invoice', $year)
                    )
                ),

            SelectFilter::make('customer')
                ->label('Pelanggan')
                ->relationship('booking.customer', 'nama')
                ->searchable()
                ->preload(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function ($livewire) {

                    $query = clone $livewire->getFilteredTableQuery();

                    $invoices = $query
                        ->with([
                            'booking.customer',
                            'booking.penalty',
                            'booking.car.carModel',
                            'payments',
                        ])
                        ->get();

                    $pdf = Pdf::loadView('exports.piutang', [
                        'piutang' => $invoices,
                    ])->setPaper('A4', 'landscape');

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'piutang.pdf'
                    );
                }),
        ];
    }
}

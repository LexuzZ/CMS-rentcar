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
    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
{
    return Invoice::query()
        ->where('status', 'belum_lunas') // 🔥 KUNCI UTAMA
        ->with([
            'booking.customer',
            'booking.penalties',
            'booking.car.carModel',
            'payments',
        ])
        ->latest();
}


    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('booking.customer.nama')
                ->label('Penyewa')
                ->default('-')
                ->alignCenter()
                ->searchable(),

            Tables\Columns\TextColumn::make('total_tagihan')
                ->label('Total Tagihan')
                ->formatStateUsing(
                    fn($state) =>
                    'Rp ' . number_format($state, 0, ',', '.')
                ),

            Tables\Columns\TextColumn::make('total_paid')
                ->label('Sudah Dibayar')
                ->formatStateUsing(
                    fn($state) =>
                    'Rp ' . number_format($state, 0, ',', '.')
                ),

            Tables\Columns\TextColumn::make('sisa_pembayaran')
                ->label('Sisa')
                ->color('danger')
                ->formatStateUsing(
                    fn($state) =>
                    'Rp ' . number_format($state, 0, ',', '.')
                ),

        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('bulan')
            ->label('Bulan')
            ->options([
                1  => 'Januari',
                2  => 'Februari',
                3  => 'Maret',
                4  => 'April',
                5  => 'Mei',
                6  => 'Juni',
                7  => 'Juli',
                8  => 'Agustus',
                9  => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ])
            ->query(function (Builder $query, array $data) {
                if (! $data['value']) {
                    return;
                }

                $query->whereMonth('tanggal_invoice', $data['value']);
            }),

        // 🔹 FILTER TAHUN
        SelectFilter::make('tahun')
            ->label('Tahun')
            ->options(
                Invoice::query()
                    ->selectRaw('YEAR(created_at) as year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year', 'year')
                    ->toArray()
            )
            ->query(function (Builder $query, array $data) {
                if (! $data['value']) {
                    return;
                }

                $query->whereYear('tanggal_invoice', $data['value']);
            }),


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
                    $piutang = $livewire->getFilteredTableQuery()->get();

                    $pdf = Pdf::loadView('exports.piutang', [
                        'piutang' => $piutang,
                    ]);

                    return response()->streamDownload(
                        fn() => print ($pdf->output()),
                        'piutang.pdf'
                    );
                }),
        ];
    }

    /* =======================
        HELPER METHOD
    ======================= */


}

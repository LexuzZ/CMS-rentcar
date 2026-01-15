<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
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
    return Payment::query()
        ->select([
            'id',
            'invoice_id',
            'tanggal_pembayaran',
            'pembayaran',
        ])
        ->whereHas('invoice') // 🔒 pastikan invoice ada
        ->with([
            'invoice:id,booking_id,pickup_dropOff',
            'invoice.payments:id,invoice_id,pembayaran',
            'invoice.booking:id,customer_id,estimasi_biaya',
            'invoice.booking.customer:id,nama',
            'invoice.booking.penalty:id,booking_id,amount,klaim', // 🔥 WAJIB
            'invoice.booking.car:id,car_model_id,nopol',
            'invoice.booking.car.carModel:id,name',
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
                ->label('Pembayaran Masuk')
                ->alignCenter()
                ->formatStateUsing(fn ($state) =>
                    'Rp ' . number_format($state, 0, ',', '.')
                )
                ->color('success'),
        ];
    }



    protected function getTableFilters(): array
    {
        return [
            Filter::make('bulan_ini')
                ->label('Hanya Bulan Ini')
                ->toggle()
                ->default(true)
                ->query(
                    fn(Builder $query) =>
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
                ->query(
                    fn(Builder $query, array $data) =>
                    $query->when(
                        $data['value'] ?? null,
                        fn($q, $month) => $q->whereMonth('tanggal_pembayaran', $month)
                    )
                ),

            SelectFilter::make('tahun')
                ->label('Tahun')
                ->options(
                    collect(range(now()->year, now()->year - 5))
                        ->mapWithKeys(fn($y) => [$y => $y])
                        ->toArray()
                )
                ->query(
                    fn(Builder $query, array $data) =>
                    $query->when(
                        $data['value'] ?? null,
                        fn($q, $year) => $q->whereYear('tanggal_pembayaran', $year)
                    )
                ),

            SelectFilter::make('customer')
                ->label('Pelanggan')
                ->relationship('invoice.booking.customer', 'nama')
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


}

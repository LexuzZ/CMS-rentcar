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
            ->where('status', 'belum_lunas') // 🔥 KUNCI
            ->with([
                'payments:id,invoice_id,pembayaran',
                'booking:id,customer_id,estimasi_biaya',
                'booking.customer:id,nama',
                'booking.penalties:id,booking_id,amount,klaim',
                'booking.car:id,car_model_id,nopol',
                'booking.car.carModel:id,name',
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
            Filter::make('bulan_ini')
                ->label('Bulan Ini')
                ->toggle()
                ->default(true)
                ->query(
                    fn(Builder $query) =>
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth(),
                    ])
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

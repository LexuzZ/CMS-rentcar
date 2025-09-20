<?php

namespace App\Filament\Pages\Overview\Widgets;

use App\Models\Payment;
use App\Models\Pengeluaran;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BiayaInvestorPerGarasiChart extends BaseWidget
{
    protected static ?string $heading = 'Total Biaya Investor per Garasi (Bulan Ini)';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $recordKey = 'nama_garasi';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->where('payments.status', 'lunas')
                    ->whereBetween('payments.tanggal_pembayaran', [now()->startOfMonth(), now()->endOfMonth()])
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->join('bookings', 'invoices.booking_id', '=', 'bookings.id')
                    ->join('cars', 'bookings.car_id', '=', 'cars.id')
                    ->select(
                        'cars.garasi as nama_garasi',
                        DB::raw('SUM(cars.harga_pokok * bookings.total_hari) as total_biaya_investor')
                    )
                    ->groupBy('cars.garasi')
            )
            // Baris ->recordKey() yang salah sudah dihapus dari sini
            ->columns([
                TextColumn::make('nama_garasi')
                    ->label('Garasi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_biaya_investor')
                    ->label('Total Biaya Investor')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultSort('total_biaya_investor', 'desc')
            ->paginated(false);
    }
}

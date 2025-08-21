<?php

namespace App\Filament\Resources\MonthlyReportResource\Pages;

use App\Filament\Exports\MonthlyDetailExporter;
use App\Filament\Exports\PaymentExporter;
use App\Filament\Resources\MonthlyReportResource;
use App\Models\Payment;

use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class DetailMonthlyReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = MonthlyReportResource::class;

    protected static string $view = 'filament.resources.monthly-report-resource.pages.detail-monthly-report';

    public string $record;

    public function mount(string $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        [$year, $month] = explode('-', $this->record);

        $monthName = \Carbon\Carbon::create()->month((int) $month)->locale('id')->isoFormat('MMMM');

        return "Detail Rekapan - {$monthName} {$year}";
    }

    public function table(Table $table): Table
    {
        [$year, $month] = explode('-', $this->record);

        return $table
            ->query(
                Payment::query()
                    ->whereYear('tanggal_pembayaran', $year)
                    ->whereMonth('tanggal_pembayaran', $month)

            )
            ->columns([
                TextColumn::make('invoice.id')->label('Faktur'),
                TextColumn::make('invoice.booking.customer.nama')->label('Pelanggan')->searchable()->wrap() // <-- Tambahkan wrap agar teks turun
                    ->width(250),
                TextColumn::make('invoice.booking.total_hari')->label('Total Hari'),
                TextColumn::make('invoice.booking.car.nopol')->label('No. Polisi')->searchable(),
                TextColumn::make('invoice.booking.car.garasi')->label('Vendor')->searchable()->wrap() // <-- Tambahkan wrap agar teks turun
                    ->width(250),
                TextColumn::make('tanggal_pembayaran')->label('Tanggal Pembayaran')->date('d M Y'),
                TextColumn::make('pembayaran')->label('Jumlah')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'lunas',
                        'danger' => 'belum_lunas',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                        default => ucfirst($state),
                    }),
            ])
            ->headerActions([
                ExportAction::make()->label('Export Excel')->exporter(PaymentExporter::class)
            ]);
    }
}

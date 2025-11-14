<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Invoice;
use App\Models\Payment;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Tables\Filters\SelectFilter;

class Revenue extends BaseWidget
{
    protected int|string|array $columnSpan = '300px';
    protected static ?string $heading = 'Pendapatan (Lunas)';
    protected int|string|array $perPage = 5;

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(function () {
                return Payment::query()->where('status', 'lunas');
            })
            ->columns([
                // Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('tanggal_pembayaran')->date('d M Y')->label('Tanggal')
                ->alignCenter()
                ->sortable(),
                Tables\Columns\TextColumn::make('invoice.booking.customer.nama')
                ->label('Penyewa')->wrap()->width(100)
                ->alignCenter()->searchable(),
                Tables\Columns\TextColumn::make('pembayaran')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->color('success')->alignCenter(),
            ])
            ->filters([
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
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] ?? null) {
                            $query->whereMonth('tanggal_pembayaran', $data['value']);
                        }
                        return $query;
                    }),

                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        return Payment::selectRaw('YEAR(tanggal_pembayaran) as tahun')
                            ->distinct()
                            ->orderBy('tahun', 'desc')
                            ->pluck('tahun', 'tahun')
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] ?? null) {
                            $query->whereYear('tanggal_pembayaran', $data['value']);
                        }
                        return $query;
                    }),
            ]);
    }
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [3,5]; // 3 sebagai default
    }
}

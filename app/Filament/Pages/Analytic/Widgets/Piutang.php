<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;

class Piutang extends BaseWidget
{
    protected int|string|array $columnSpan = '300px';
    protected static ?string $heading = 'Piutang (Belum Lunas)';
    protected int|string|array $perPage = 5;

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                fn() =>
                Payment::query()
                    ->select([
                        'id',
                        'invoice_id',
                        'tanggal_pembayaran',
                        'pembayaran',
                        'status',
                    ])
                    ->where('status', 'belum_lunas')
                    ->with([
                        'invoice:id,booking_id',
                        'invoice.booking:id,customer_id',
                        'invoice.booking.customer:id,nama',
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_pembayaran')
                    ->date('d M Y')
                    ->label('Tanggal')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('invoice.booking.customer.nama')
                    ->label('Penyewa')
                    ->wrap()
                    ->width(150)
                    ->alignCenter()
                    ->searchable(),

                Tables\Columns\TextColumn::make('pembayaran')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('danger')
                    ->alignCenter(),
            ])
            ->defaultSort('tanggal_pembayaran', 'desc')
            ->filters([
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
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] ?? null) {
                            $query->whereMonth('tanggal_pembayaran', $data['value']);
                        }
                        return $query;
                    }),

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
                    ->label('Filter Pelanggan')
                    ->relationship('invoice.booking.customer', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Action::make('exportPdf')
                    ->label('Export PDF')
                    ->action(function ($livewire) {
                        // Ambil query hasil filter aktif
                        $piutang = $livewire->getFilteredTableQuery()->get();

                        $pdf = Pdf::loadView('exports.piutang', [
                            'piutang' => $piutang,
                        ]);

                        return response()->streamDownload(
                            fn() => print ($pdf->output()),
                            'piutang.pdf'
                        );
                    }),
            ])
            ->paginated([5]);
    }

}

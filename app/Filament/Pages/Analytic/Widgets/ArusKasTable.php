<?php

namespace App\Filament\Pages\Analytic\Widgets;


use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\CashFlow; // Ensure this is the correct namespace for the Cashflow model
use Filament\Widgets\TableWidget as BaseWidget;

class ArusKasTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Arus Kas Perusahaan';

    public function table(Table $table): Table
    {
        // $query = DB::table(DB::raw("(
        //     (
        //         SELECT
        //             id,
        //             tanggal_pengeluaran AS tanggal,
        //             description AS keterangan,
        //             'Keluar' AS jenis,
        //             pembayaran
        //         FROM pengeluarans
        //     )
        //     UNION ALL
        //     (
        //         SELECT
        //             id,
        //             tanggal_pembayaran AS tanggal,
        //             'Pembayaran Invoice' AS keterangan,
        //             'Masuk' AS jenis,
        //             pembayaran
        //         FROM payments
        //     )
        // ) as arus_kas"))
        //     ->orderBy('tanggal', 'desc')
        //     ->orderBy('id', 'asc');

        return $table
            ->query(
                Cashflow::query()
                    ->fromSub(function ($query) {
                        $query->from('pengeluarans')
                            ->select(
                                'id',
                                'tanggal_pengeluaran as tanggal',
                                'nama_pengeluaran as keterangan',
                                DB::raw("'Kas Keluar' as jenis"),
                                'pembayaran'
                            )
                            ->unionAll(
                                DB::table('payments')
                                    ->select(
                                        'id',
                                        'tanggal_pembayaran as tanggal',
                                        DB::raw("
                                CASE
                                    WHEN status = 'lunas' THEN 'Pendapatan Sewa'
                                    ELSE 'Piutang Sewa'
                                END as keterangan
                            "),
                                        DB::raw("
                                CASE
                                    WHEN status = 'lunas' THEN 'Kas Masuk'
                                    ELSE 'Kas Piutang'
                                END as jenis
                            "),
                                        'pembayaran'
                                    )
                            );
                    }, 'cashflow')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('tanggal')->label('Tanggal')->date('d M Y')->alignCenter(),
                TextColumn::make('keterangan')->label('Keterangan')->alignCenter()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'gaji' => 'Gaji Karyawan',
                        'pajak' => 'Pajak/STNK',
                        'perawatan' => 'Perawatan',
                        'operasional' => 'Operasional Kantor',
                        default => ucfirst($state),
                    }),
                TextColumn::make('jenis')->label('Jenis')->alignCenter()->badge()
                    ->colors([
                        'success' => 'Kas Masuk',
                        'danger' => 'Kas Keluar',
                        'warning' => 'Kas Piutang',
                    ]),
                TextColumn::make('pembayaran')
                    ->label('Nominal')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])
            ->filters([
                Filter::make('Periode')
                    ->form([
                        Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ]),
                        TextInput::make('tahun')
                            ->label('Tahun')
                            ->numeric()
                            ->default(date('Y')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['bulan'], fn($q, $bulan) => $q->whereMonth('tanggal', $bulan))
                            ->when($data['tahun'], fn($q, $tahun) => $q->whereYear('tanggal', $tahun));
                    }),

            ]);
    }
}

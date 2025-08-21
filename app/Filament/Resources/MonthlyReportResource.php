<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlyReportResource\Pages;
use App\Models\Payment;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MonthlyReportResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $modelLabel = 'Rekapan Bulanan';
    protected static ?string $pluralModelLabel = 'Rekapan Bulanan';
    protected static ?string $slug = 'laporan-bulanan';

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->select(
                        DB::raw('YEAR(tanggal_pembayaran) as year'),
                        DB::raw('MONTH(tanggal_pembayaran) as month'),
                        DB::raw("SUM(CASE WHEN status = 'lunas' THEN pembayaran ELSE 0 END) as net_revenue"),
                        // Menjumlahkan tagihan dari yang belum lunas
                        DB::raw("SUM(CASE WHEN status = 'belum_lunas' THEN pembayaran ELSE 0 END) as pending_revenue"),
                        DB::raw('COUNT(*) as transaction_count'),
                        DB::raw('SUM(pembayaran) as total_revenue')
                    )
                    // ->where('status', 'lunas')
                    ->groupBy('year', 'month')
            )
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Bulan')
                    ->formatStateUsing(fn(string $state): string => \Carbon\Carbon::create()->month((int) $state)->isoFormat('MMMM'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_count')
                    ->label('Transaksi')
                    ->numeric()
                ,
                Tables\Columns\TextColumn::make('net_revenue')
                    ->label('Pendapatan (Lunas)')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ,
                // Kolom baru untuk menampilkan tagihan yang belum lunas
                Tables\Columns\TextColumn::make('pending_revenue')
                    ->label('Tagihan (Belum Lunas)')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('danger') // Memberi warna merah untuk menandakan tagihan
                ,
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Pendapatan')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Filter Tahun')
                    ->options(function () {
                        $years = Payment::selectRaw('YEAR(tanggal_pembayaran) as year')
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year')
                            ->toArray();
                        return array_combine($years, $years);
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn(Builder $query, $value) => $query->whereYear('tanggal_pembayaran', $value)
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewDetails')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Model $record): string => static::getUrl('details', ['record' => $record->year . '-' . $record->month])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlyReports::route('/'),
            'details' => Pages\DetailMonthlyReport::route('/{record}/details'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
    // PERBAIKAN DI SINI: Method canView() dihapus
    public static function canEdit(Model $record): bool
    {
        return false;
    }
}

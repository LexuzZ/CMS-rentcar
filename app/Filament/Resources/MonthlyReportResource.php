<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlyReportResource\Pages;
use App\Models\Payment;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlyReportResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Laporan & Accounting';
    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Rekapan Sewa Bulanan';
    protected static ?string $pluralModelLabel = 'Rekapan Sewa Bulanan';
    protected static ?string $slug = 'laporan-bulanan';

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->select([
                        DB::raw('YEAR(payments.tanggal_pembayaran) as year'),
                        DB::raw('MONTH(payments.tanggal_pembayaran) as month'),

                        DB::raw('COUNT(payments.id) as transaction_count'),
                        DB::raw('SUM(payments.pembayaran) as total_paid'),

                        DB::raw('SUM(DISTINCT invoices.total_tagihan) as total_tagihan'),
                        DB::raw('SUM(DISTINCT invoices.sisa_pembayaran) as total_sisa'),
                    ])
                    ->groupBy('year', 'month')
            )
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Bulan')
                    ->formatStateUsing(fn (string $state) =>
                        \Carbon\Carbon::create()
                            ->month((int) $state)
                            ->locale('id')
                            ->isoFormat('MMMM')
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction_count')
                    ->label('Transaksi')
                    ->numeric(),

                Tables\Columns\TextColumn::make('total_paid')
                    ->label('Total Dibayar')
                    ->color('success')
                    ->formatStateUsing(fn ($state) =>
                        'Rp ' . number_format($state, 0, ',', '.')
                    ),

                Tables\Columns\TextColumn::make('total_sisa')
                    ->label('Sisa Piutang')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) =>
                        'Rp ' . number_format($state, 0, ',', '.')
                    ),

                Tables\Columns\TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->formatStateUsing(fn ($state) =>
                        'Rp ' . number_format($state, 0, ',', '.')
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Filter Tahun')
                    ->options(function () {
                        $years = Payment::selectRaw('YEAR(tanggal_pembayaran) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year')
                            ->toArray();

                        return array_combine($years, $years);
                    })
                    ->query(fn (Builder $query, array $data) =>
                        $query->when(
                            $data['value'],
                            fn (Builder $query, $value) =>
                                $query->whereYear('payments.tanggal_pembayaran', $value)
                        )
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('viewDetails')
                    ->tooltip('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hiddenLabel()
                    ->button()
                    ->url(fn (Model $record): string =>
                        static::getUrl('details', [
                            'record' => "{$record->year}-{$record->month}",
                        ])
                    ),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'   => Pages\ListMonthlyReports::route('/'),
            'details' => Pages\DetailMonthlyReport::route('/{record}/details'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}

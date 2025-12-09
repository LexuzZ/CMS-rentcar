<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class StaffMonthlyRankWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Ranking Staff Bulanan (Bulan Ini)';
    protected static ?int $sort = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Driver::query()
                    ->withCount([
                        'pengantaran as antar_count' => function (Builder $query) {
                            $query->whereMonth('tanggal_keluar', Carbon::now()->month)
                                  ->whereYear('tanggal_keluar', Carbon::now()->year);
                        },
                        'pengembalian as jemput_count' => function (Builder $query) {
                            $query->whereMonth('tanggal_kembali', Carbon::now()->month)
                                  ->whereYear('tanggal_kembali', Carbon::now()->year);
                        },
                    ])
                    ->havingRaw('(antar_count + jemput_count) > 0')
                    ->orderByRaw('(antar_count + jemput_count) DESC')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Staff')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_job')
                    ->label('Total Job')
                    ->state(fn (Driver $record) => $record->antar_count + $record->jemput_count)
                    ->badge()
                    ->color('primary')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('(antar_count + jemput_count) ' . $direction);
                    }),

                Tables\Columns\TextColumn::make('antar_count')
                    ->label('Total Antar'),

                Tables\Columns\TextColumn::make('jemput_count')
                    ->label('Total Jemput'),
            ]);
    }
}

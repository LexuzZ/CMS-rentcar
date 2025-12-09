<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class StaffRankingWidget extends BaseWidget
{
    // Mengatur lebar widget agar full atau separuh
    protected int | string | array $columnSpan = 'full';

    // Judul Widget
    protected static ?string $heading = 'Ranking Staff Harian (Hari Ini)';

    // Urutan widget di dashboard
    protected static ?int $sort = 7;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Driver::query()
                    ->withCount([
                        'pengantaran as antar_count' => function (Builder $query) {
                            $query->whereDate('tanggal_keluar', Carbon::today());
                        },
                        'pengembalian as jemput_count' => function (Builder $query) {
                            $query->whereDate('tanggal_kembali', Carbon::today());
                        },
                    ])
                    // Filter agar hanya menampilkan driver yang ada kerjaan hari ini (Opsional, hapus jika ingin semua driver tampil)
                    ->havingRaw('(antar_count + jemput_count) > 0')
                    ->orderByRaw('(antar_count + jemput_count) DESC') // Ranking berdasarkan total tertinggi
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Staff')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_job')
                    ->label('Total Job')
                    ->state(function (Driver $record): int {
                        return $record->antar_count + $record->jemput_count;
                    })
                    ->badge()
                    ->color('success')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('(antar_count + jemput_count) ' . $direction);
                    }),

                Tables\Columns\TextColumn::make('antar_count')
                    ->label('Antar')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('jemput_count')
                    ->label('Jemput')
                    ->alignCenter(),
            ])
            ->paginated(false); // Matikan pagination jika list staff tidak terlalu banyak
    }
}

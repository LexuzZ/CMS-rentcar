<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class DaftarTungguTable extends BaseWidget
{
    protected static ?string $heading = 'Daftar Tunggu';
    protected int|string|array $columnSpan = 'full'; // biar lebar penuh di dashboard
    protected static ?int $sort = 8;

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                Booking::query()
                    ->whereHas('car', fn($q) => $q->where('garasi', 'Daftar Tunggu'))
                    ->where('status', 'booking') // ✅ hanya tampilkan yang status booking
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Penyewa')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('car.nopol')
                    ->label('No Polisi')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('tanggal_keluar')
                    ->label('Tgl Keluar')
                    ->date('d M Y')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('tanggal_kembali')
                    ->label('Tgl Kembali')
                    ->date('d M Y')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'info' => 'booking',
                        'warning' => 'disewa',
                        'success' => 'selesai',
                        'danger' => 'batal',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25]); // biar ada pagination
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MobilKembali extends BaseWidget
{
    protected static ?string $heading = 'Mobil Kembali Hari Ini';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::with('car')
                    ->whereDate('tanggal_kembali', Carbon::today()) // ✅ letakkan di sini
            )
            ->columns([
                TextColumn::make('car.nopol')->label('No Polisi')->alignCenter(),
                TextColumn::make('car.merek')
                    ->label('Merk Mobil')
                    ->badge()
                    ->alignCenter()

                    ->formatStateUsing(fn($state) => match ($state) {
                        'toyota' => 'Toyota',
                        'mitsubishi' => 'Mitsubishi',
                        'suzuki' => 'Suzuki',
                        'honda' => 'Honda',
                        'daihatsu' => 'Daihatsu',
                        default => ucfirst($state),
                    }),
                TextColumn::make('car.nama_mobil')->label('Nama Mobil')->alignCenter(),
                TextColumn::make('customer.nama')->label('Nama Penyewa')->alignCenter(),
                TextColumn::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->date('d M Y')->alignCenter(),
                TextColumn::make('waktu_kembali')->label('Waktu Kembali')->alignCenter()->time('H:i'),
            ]);
    }
}

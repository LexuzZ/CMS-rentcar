<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MobilKembali extends BaseWidget
{
    protected static ?string $heading = 'Mobil Kembali Hari Ini';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::with('car')
                    ->where('status', 'aktif') // ✅ hanya status aktif
                    ->whereDate('tanggal_kembali', \Carbon\Carbon::today()) // ✅ tanggal kembali hari ini
            )
            // Booking::with('car')->query()->where('status', 'aktif')->latest()->whereDate('tanggal_kembali', Carbon::today())
            // )
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
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'aktif',
                        'info' => 'booking',
                        'gray' => 'selesai',
                        'danger' => 'batal',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'aktif' => 'Aktif',
                        'booking' => 'Booking',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => ucfirst($state),
                    }),
                TextColumn::make('car.nama_mobil')->label('Nama Mobil')->alignCenter(),
                TextColumn::make('customer.nama')->label('Nama Penyewa')->alignCenter(),
                TextColumn::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->date('d M Y')->alignCenter(),
                TextColumn::make('waktu_kembali')->label('Waktu Kembali')->alignCenter()->time('H:i'),
            ])
            ->actions([
                Action::make('Selesai')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->url(fn(Booking $record) => route('filament.admin.resources.penalties.create', ['booking' => $record->id]))
                    ->openUrlInNewTab(), // atau hilangkan jika ingin buka di tab yang sama
            ]);
    }
}

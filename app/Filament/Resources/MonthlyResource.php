<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PaymentExporter;
use App\Filament\Resources\MonthlyResource\Pages;
use App\Models\Payment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MonthlyResource extends Resource
{
    // 1. Menentukan model utama untuk resource ini
    protected static ?string $model = Payment::class;

    protected static ?string $navigationLabel = 'Rekapan Bulanan';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    protected static ?string $modelLabel = 'Rekapan Bulanan';
    protected static ?string $pluralModelLabel = 'Rekapan Bulanan';

    // Resource ini hanya untuk menampilkan data, jadi form tidak diperlukan
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // 2. Mengoptimalkan query dengan eager loading
            ->query(
                Payment::query()->with([
                    'invoice.booking.customer',
                    'invoice.booking.car.carModel.brand',
                    'invoice.booking.penalty'
                ])
            )
            ->columns([
                TextColumn::make('invoice.id')->label('ID Faktur')->sortable(),
                TextColumn::make('tanggal_pembayaran')->label('Tanggal Bayar')->date('d M Y')->sortable(),
                TextColumn::make('invoice.booking.customer.nama')->label('Pelanggan')->searchable(),

                // 3. Memperbaiki relasi untuk menampilkan merek mobil
                TextColumn::make('invoice.booking.car.carModel.brand.name')
                    ->label('Merk Mobil')
                    ->badge()
                    ->searchable(),

                TextColumn::make('invoice.booking.car.nopol')->label('No. Polisi')->searchable(),

                TextColumn::make('pembayaran')->label('Sewa')->money('IDR'),

                // Kolom denda sekarang lebih efisien karena data sudah di-load
                TextColumn::make('total_denda')
                    ->label('Total Denda')
                    ->money('IDR')
                    ->getStateUsing(fn (Payment $record): float => $record->invoice?->booking?->penalty->sum('amount') ?? 0),

                TextColumn::make('total_bayar')
                    ->label('Jumlah Bayar')
                    ->money('IDR')
                    ->getStateUsing(function (Payment $record): float {
                        $totalSewa = $record->pembayaran ?? 0;
                        $totalDenda = $record->invoice?->booking?->penalty->sum('amount') ?? 0;
                        return $totalSewa + $totalDenda;
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->colors(['success' => 'lunas', 'danger' => 'belum_lunas'])
                    ->formatStateUsing(fn ($state) => match ($state) { 'lunas' => 'Lunas', 'belum_lunas' => 'Belum Lunas', default => ucfirst($state) }),
            ])
            // 4. MENAMBAHKAN FITUR FILTER TANGGAL
            ->filters([
                Filter::make('tanggal_pembayaran')
                    ->form([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari_tanggal'], fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pembayaran', '>=', $date))
                            ->when($data['sampai_tanggal'], fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pembayaran', '<=', $date));
                    }),
            ])
            ->headerActions([
                ExportAction::make()->exporter(PaymentExporter::class)
            ])
            ->actions([]) // Menghapus tombol aksi per baris karena ini adalah laporan
            ->bulkActions([]); // Menghapus aksi massal
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlies::route('/'),
        ];
    }

    // -- KONTROL AKSES (superadmin, admin, staff) --
    // Staff tidak perlu melihat rekapan ini
    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }
}

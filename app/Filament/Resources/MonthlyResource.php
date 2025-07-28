<?php

namespace App\Filament\Resources;

use App\Events\ExportCompleted;
use App\Filament\Exports\PaymentExporter;
use App\Filament\Resources\MonthlyResource\Pages;
use App\Filament\Resources\MonthlyResource\RelationManagers;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Monthly;
use App\Models\Payment;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonthlyResource extends Resource
{


    // protected static ?string $model = Invoice::class;
    protected static ?string $navigationLabel = 'Rekapan Bulanan';
    // protected static ?string $heading = 'Rekapan Bulanan';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    protected int | string | array $columnSpan = 'full';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Sesuai kebutuhan kamu
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                // Invoice::with(['booking.penalty', 'booking.customer', 'booking.car', 'payment'])
                //     ->latest(),
                Payment::query()
            )


            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('invoice.booking.customer.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->toggleable()
                    ->alignCenter(),

                TextColumn::make('invoice.booking.car.nopol')
                    ->label('No. Polisi')
                    ->toggleable()
                    ->alignCenter(),

                TextColumn::make('invoice.booking.car.merek')
                    ->label('Merk Mobil')
                    ->toggleable()
                    ->alignCenter(),
                TextColumn::make('invoice.booking.car.merek')
                    ->label('Merk Mobil')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'info' => 'toyota',
                        'info' => 'mitsubishi',
                        'info' => 'suzuki',
                        'info' => 'daihatsu',
                        'info' => 'honda'
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'toyota' => 'Toyota',
                        'mitsubishi' => 'Mitsubishi',
                        'suzuki' => 'Suzuki',
                        'honda' => 'Honda',
                        'daihatsu' => 'Daihatsu',
                        default => ucfirst($state),
                    }),



                TextColumn::make('invoice.booking.penalty.klaim')
                    ->label('Klaim Garasi')
                    ->toggleable()
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'bbm',
                        'danger' => 'baret',
                        'danger' => 'overtime',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'bbm' => 'BBM',
                        'overtime' => 'Overtime',
                        'baret' => 'Baret/Kerusakan',
                        default => ucfirst($state),
                    }),

                TextColumn::make('invoice.booking.tanggal_keluar')
                    ->label('Tanggal Keluar')->date('d M Y')->alignCenter(),
                TextColumn::make('invoice.booking.tanggal_kembali')
                    ->label('Tanggal Kembali')->date('d M Y')->alignCenter(),
                TextColumn::make('invoice.tanggal_invoice')->label('Tanggal Invoice')->date('d M Y')->alignCenter(),
                TextColumn::make('metode_pembayaran')
                    ->label('Metode Pembayaran'),
                TextColumn::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'tunai',
                        'info' => 'transfer',
                        'gray' => 'qris',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        default => ucfirst($state),
                    }),
                TextColumn::make('invoice.booking.car.harga_harian')
                    ->label('Harga Harian')
                    ->toggleable()
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.')),

                TextColumn::make('invoice.total')
                    ->toggleable()
                    ->label('Total Invoice')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.')),

                TextColumn::make('total_bbm')
                    ->label('Total BBM')
                    ->toggleable()
                    ->getStateUsing(function ($record) {
                        return $record->invoice
                            ? $record->invoice->booking->penalty->where('klaim', 'bbm')->sum('amount')
                            : 0;
                    })->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')) // Tanpa 2 digit desimal
                ,

                TextColumn::make('total_overtime')
                    ->label('Total Overtime')
                    ->toggleable()
                    ->getStateUsing(function ($record) {
                        return $record->invoice
                            ? $record->invoice->booking->penalty->where('klaim', 'overtime')->sum('amount')
                            : 0;
                    })->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')) // Tanpa 2 digit desimal
                    ->sortable(),

                TextColumn::make('total_baret')
                    ->label('Total Baret')
                    ->toggleable()
                    ->getStateUsing(function ($record) {
                        return $record->invoice
                            ? $record->invoice->booking->penalty->where('klaim', 'baret')->sum('amount')
                            : 0;
                    })->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')) // Tanpa 2 digit desimal
                    ->sortable(),
                TextColumn::make('invoice.booking.penalty.amount')
                    ->label('Total Denda')
                    ->alignCenter()
                    ->formatStateUsing(function ($record) {
                        $total = optional($record->invoice?->booking?->penalty)->sum('amount') ?? 0;
                        return 'Rp ' . number_format($total, 0, ',', '.');
                    }),

                TextColumn::make('total_bayar')
                    ->label('Jumlah Bayar')
                    ->alignCenter()
                    ->getStateUsing(function ($record) {
                        $invoice = $record->invoice;
                        $totalInvoice = $invoice?->total ?? 0;

                        // Sum all penalty amounts for the related booking
                        $totalDenda = $invoice?->booking?->penalty?->sum('amount') ?? 0;

                        return $totalInvoice + $totalDenda;
                    })->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')) // Tanpa 2 digit desimal
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'lunas',
                        'danger' => 'belum_lunas',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                        default => ucfirst($state),
                    })



            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(PaymentExporter::class)
                    ->after(function (Export $export) {
                        broadcast(new ExportCompleted(
                            PaymentExporter::getCompletedNotificationBody($export)
                        ));
                    })
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->exporter(PaymentExporter::class)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlies::route('/'),
        ];
    }
}

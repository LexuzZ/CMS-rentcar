<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Payment;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;

class InvoiceTable extends BaseWidget
{
    protected static ?string $heading = 'Rekapan Bulanan';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';



    public function table(Table $table): Table
    {

        return $table
            ->query(
                Payment::query()->latest()
            )
            ->columns([
                TextColumn::make('invoice.id')
                    ->label('Invoice ID')
                    ->searchable()->alignCenter(),


                TextColumn::make('invoice.booking.car.nopol')
                    ->label('No. Polisi')
                    ->searchable()->alignCenter(),
                // TextColumn::make('invoice.booking.car.merek')
                //     ->label('Merk Mobil')
                //     ->searchable(),

                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal Pembayaran')
                    ->date('d M Y')
                    ->searchable()->alignCenter(),

                // TextColumn::make('metode_pembayaran')
                //     ->label('Metode')
                //     ->sortable(),

                TextColumn::make('total_bayar')
                    ->label('Jumlah Bayar')
                    ->getStateUsing(function ($record) {
                        $invoice = $record->invoice;
                        $totalInvoice = $invoice?->total ?? 0;

                        // Sum all penalty amounts for the related booking
                        $totalDenda = $invoice?->booking?->penalty?->sum('amount') ?? 0;

                        return $totalInvoice + $totalDenda;
                    })
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')) // Tanpa 2 digit desimal
                    ->sortable()->alignCenter(),

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
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',

                    ]),
            ]);
    }
}

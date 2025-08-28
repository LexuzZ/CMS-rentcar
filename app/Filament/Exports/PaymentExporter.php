<?php

namespace App\Filament\Exports;

use App\Models\Payment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PaymentExporter extends Exporter
{
    protected static ?string $model = Payment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('invoice.booking.customer.nama')
                ->label('Customer'),
            ExportColumn::make('invoice.booking.car.carModel.brand.name')->label('Merek'),
            ExportColumn::make('invoice.booking.car.carModel.name')->label('Model Mobil'),
            ExportColumn::make('invoice.booking.car.nopol')->label('No. Polisi'),
            ExportColumn::make('invoice.booking.car.garasi')
                ->label('Garasi'),
            // ExportColumn::make('invoice.booking.penalty.klaim')
            //     ->label('Klaim')
            //     ->getStateUsing(function ($record) {
            //         return $record->invoice?->booking?->penalty
            //             ?->pluck('klaim')
            //             ->join(', ') ?? '-';
            //     }),
            ExportColumn::make('invoice.booking.tanggal_keluar')
                ->label('Tanggal Keluar'),
            ExportColumn::make('invoice.booking.tanggal_kembali')->label('Tanggal Kembali'),
            ExportColumn::make('invoice.booking.total_hari')->label('Total Hari'),
            ExportColumn::make('invoice.booking.car.harga_pokok')
                ->label('Harga Pokok')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ExportColumn::make('invoice.booking.car.harga_harian')
                ->label('Harga Harian')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),


            // ExportColumn::make('invoice.booking.penalty.amount'),


            // ExportColumn::make('pembayaran')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
            //     ->label('Pembayaran'),
            ExportColumn::make('total_denda')
                ->label('Total Denda')
                ->getStateUsing(fn(Payment $record): int => (int) ($record->invoice?->booking?->penalty->sum('amount') ?? 0)),
            ExportColumn::make('invoice.total')
                ->label('Total Invoice')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

            ExportColumn::make('total_bayar')
                ->label('Jumlah Bayar')->getStateUsing(function ($record) {
                    $invoice = $record->invoice;
                    $totalInvoice = $invoice?->total ?? 0;

                    // Sum all penalty amounts for the related booking
                    $totalDenda = $invoice?->booking?->penalty?->sum('amount') ?? 0;

                    return $totalInvoice + $totalDenda;
                }),
            ExportColumn::make('metode_pembayaran'),

            ExportColumn::make('status'),
            ExportColumn::make('tanggal_pembayaran')->label('Tanggal Pembayaran'),
            ExportColumn::make('invoice.tanggal_invoice')
                ->label('Tanggal Invoice'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export data pembayaran selesai. ' . number_format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}

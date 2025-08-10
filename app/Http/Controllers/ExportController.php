<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
// use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\Party;

class ExportController extends Controller
{
    public function download($record)
    {

        $invoiceModel = \App\Models\Invoice::with('booking.customer', 'booking.car', 'booking.penalty')->findOrFail($record);
        $tanggalSewa = Carbon::parse($invoiceModel->booking->tanggal_keluar)->format('d M Y') . ' - ' .
            Carbon::parse($invoiceModel->booking->tanggal_kembali)->format('d M Y');

        $buyer = new Buyer([
            'name' => $invoiceModel->booking->customer->nama,
            'custom_fields' => [
                'Alamat' => $invoiceModel->booking->customer->alamat,
                'No. Telp'   => $invoiceModel->booking->customer->no_telp,
                'Mobil Sewa'   => $invoiceModel->booking->car->nama_mobil,
                'Tanggal Sewa' => $tanggalSewa,
                'Total Hari Sewa'   => $invoiceModel->booking->total_hari . ' Hari',
            ],
        ]);

        $seller = new Party([
            'name' => 'PT Semeton Pesiar Trans',
            'address' => 'Jl. Panji Tilar Negara Jl. Batu Ringgit No.218, Tj. Karang, Kec. Mataram, Kota Mataram, Nusa Tenggara Bar. 83117',
            'custom_fields' => [
                'METODE PEMBAYARAN' => '',
                'Mandiri' => '1610006892835 (ACHMAD MUZAMMIL)',
                'BCA' => '2320418758 (SRI NOVYANA)',
            ],
        ]);

        $items = [
            InvoiceItem::make('Biaya Pengantaran')
                ->pricePerUnit((float) $invoiceModel->pickup_dropOff),


            InvoiceItem::make('Uang Muka')
                ->pricePerUnit((float) $invoiceModel->dp),
            InvoiceItem::make('Sisa Pembayaran')
                ->pricePerUnit((float) $invoiceModel->sisa_pembayaran),




        ];











        $invoice = Invoice::make('Invoice')
            ->series('BIG')
            // ability to include translated invoice status
            // in case it was paid
            ->sequence(667)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->date(now())
            ->dateFormat('m/d/Y')
            ->payUntilDays(14)
            ->currencySymbol('Rp ')
            ->currencyCode('IDR')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->logo(public_path('spt.png'))
            ->seller($seller)
            ->buyer($buyer)
            ->addItems($items)
            // You can additionally save generated invoice to configured disk
            ->save('public');

        $link = $invoice->url();
        // Then send email to party with link

        // And return invoice itself to browser or have a different view
        return $invoice->stream();
    }
}

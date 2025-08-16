<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    //
    public function downloadInvoice(Invoice $invoice)
    {
        // Eager load semua relasi yang dibutuhkan untuk menghindari query tambahan
        $invoice->load([
            'booking.customer',
            'booking.car.carModel.brand',
            'booking.penalty'
        ]);

        // Muat view Blade dengan data invoice
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

        // Unduh file PDF dengan nama file yang dinamis
        return $pdf->download('invoice-' . $invoice->id . '-' . $invoice->booking->customer->nama . '.pdf');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
    public function downloadMonthlyRecapPdf(int $year, int $month): Response
    {
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        // Ambil semua data pembayaran yang relevan
        $payments = Payment::with([
                'invoice.booking.customer',
                'invoice.booking.car.carModel',
                'invoice.booking.penalty'
            ])
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->get();

        // Hitung data ringkasan
        $summary = [
            'total_transactions' => $payments->count(),
            'status_breakdown' => $payments->countBy('status'),
        ];

        // Buat PDF
        $pdf = Pdf::loadView('pdf.monthly-recap', [
            'payments' => $payments,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        $fileName = "rekapan_{$year}-{$month}.pdf";

        return $pdf->download($fileName);
    }
}

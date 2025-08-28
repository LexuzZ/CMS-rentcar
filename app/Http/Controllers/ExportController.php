<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function exportCarBookings(Car $car, int $year, int $month): StreamedResponse
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->startOfDay();

        $bookings = $car->bookings()
            ->with('customer')
            ->where('status', '!=', 'batal')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('tanggal_keluar', '<=', $endOfMonth)
                    ->where('tanggal_kembali', '>=', $startOfMonth);
            })
            ->get();

        $fileName = "detail_booking_{$car->nopol}_{$year}-{$month}.csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Booking ID',
            'Pelanggan',
            'Tgl Keluar ',
            'Tgl Kembali ',
            'Total Hari (Bulan Ini)',
            'Hari Dihitung (Bulan Ini)',
            'Pendapatan Bulan Ini (Rp) ',
        ];

        $callback = function () use ($bookings, $columns, $startOfMonth, $endOfMonth) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($bookings as $booking) {
                $bookingStart = Carbon::parse($booking->tanggal_keluar)->startOfDay();
                $bookingEnd = Carbon::parse($booking->tanggal_kembali)->endOfDay();

                $periodeMulai = $bookingStart->greaterThan($startOfMonth) ? $bookingStart : $startOfMonth;
                $periodeSelesai = $bookingEnd->lessThan($endOfMonth) ? $bookingEnd : $endOfMonth;

                $hariDalamBulan = 0;
                if ($periodeMulai <= $periodeSelesai) {
                    $hariDalamBulan = $periodeMulai->diffInDays($periodeSelesai) - 1;
                }

                // hitung prorata pendapatan
                $revenueInMonth = 0;
                if ($booking->total_hari > 0 && $booking->estimasi_biaya > 0) {
                    $dailyRate = $booking->estimasi_biaya / $booking->total_hari;
                    $revenueInMonth = $dailyRate * $hariDalamBulan;
                }

                $row = [
                    $booking->id,
                    $booking->customer->nama ?? '-',
                    $booking->tanggal_keluar,
                    $booking->tanggal_kembali,
                    $booking->total_hari,
                    $hariDalamBulan,
                    round($revenueInMonth),
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

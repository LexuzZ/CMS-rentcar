<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function exportCarBookings($carId, $year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();

        $endDate = $startDate
            ->copy()
            ->endOfMonth()
            ->endOfDay();

        /**
         * =========================
         * AMBIL DATA MOBIL
         * =========================
         */
        $car = Car::with([
            'carModel.brand',
            'bookings' => function ($query) use ($startDate, $endDate) {

                $query->with('customer')
                    ->where('status', '!=', 'batal')
                    ->where(function ($q) use ($startDate, $endDate) {

                        $q->where('tanggal_keluar', '<=', $endDate)
                            ->where('tanggal_kembali', '>=', $startDate);
                    });
            }
        ])->findOrFail($carId);

        /**
         * =========================
         * EXCEL
         * =========================
         */
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        /**
         * =========================
         * JUDUL
         * =========================
         */
        $sheet->setCellValue('A1', 'Laporan Harga Pokok Mobil');

        $sheet->setCellValue(
            'A2',
            $car->carModel->brand->name .
            ' ' .
            $car->carModel->name .
            ' - ' .
            $car->nopol
        );

        $sheet->setCellValue(
            'A3',
            $startDate->locale('id')->isoFormat('MMMM YYYY')
        );

        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');

        $sheet->getStyle('A1:A3')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('A1:A3')
            ->getAlignment()
            ->setHorizontal('center');

        /**
         * =========================
         * HEADER
         * =========================
         */
        $sheet->fromArray([
            [
                'Pelanggan',
                'Tanggal Keluar',
                'Tanggal Kembali',
                'Hari Dalam Bulan',
                'Harga Pokok'
            ]
        ], null, 'A5');

        $sheet->getStyle('A5:E5')
            ->getFont()
            ->setBold(true);

        /**
         * =========================
         * DATA
         * =========================
         */
        $row = 6;

        $totalCost = 0;
        $totalDays = 0;

        foreach ($car->bookings as $booking) {

            $bookingStart = Carbon::parse($booking->tanggal_keluar)
                ->startOfDay();

            $bookingEnd = Carbon::parse($booking->tanggal_kembali)
                ->endOfDay();

            /**
             * =========================
             * TANGGAL EFEKTIF
             * =========================
             */
            $effectiveStartDate = $bookingStart
                ->copy()
                ->max($startDate);

            $effectiveEndDate = $bookingEnd
                ->copy()
                ->min($endDate);

            /**
             * =========================
             * HITUNG HARI
             * =========================
             */
            $daysInMonth = 0;

            if ($effectiveStartDate <= $effectiveEndDate) {

                $daysInMonth = $effectiveStartDate
                    ->diffInDays($effectiveEndDate) + 1;
            }

            /**
             * =========================
             * HARGA POKOK
             * =========================
             */
            $costInMonth = 0;

            // Ganti field jika berbeda
            $dailyCost = (float) ($car->harga_pokok ?? 0);

            $costInMonth = $dailyCost * $daysInMonth;

            /**
             * =========================
             * TOTAL
             * =========================
             */
            $totalCost += $costInMonth;

            $totalDays += $daysInMonth;

            /**
             * =========================
             * TULIS EXCEL
             * =========================
             */
            $sheet->setCellValue(
                "A{$row}",
                $booking->customer->nama ?? '-'
            );

            $sheet->setCellValue(
                "B{$row}",
                $effectiveStartDate->format('d-m-Y')
            );

            $sheet->setCellValue(
                "C{$row}",
                $effectiveEndDate->format('d-m-Y')
            );

            $sheet->setCellValue(
                "D{$row}",
                $daysInMonth
            );

            $sheet->setCellValue(
                "E{$row}",
                round($costInMonth)
            );

            /**
             * FORMAT RUPIAH
             */
            $sheet->getStyle("E{$row}")
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0');

            $row++;
        }

        /**
         * =========================
         * TOTAL
         * =========================
         */
        $summaryRow = $row + 1;

        $sheet->setCellValue(
            "A{$summaryRow}",
            'TOTAL'
        );

        $sheet->setCellValue(
            "D{$summaryRow}",
            $totalDays
        );

        $sheet->setCellValue(
            "E{$summaryRow}",
            round($totalCost)
        );

        $sheet->getStyle("A{$summaryRow}:E{$summaryRow}")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("E{$summaryRow}")
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0');

        /**
         * =========================
         * AUTO SIZE
         * =========================
         */
        foreach (range('A', 'E') as $col) {

            $sheet->getColumnDimension($col)
                ->setAutoSize(true);
        }

        /**
         * =========================
         * DOWNLOAD
         * =========================
         */
        $writer = new Xlsx($spreadsheet);

        $filename =
            'laporan_harga_pokok_' .
            str_replace(' ', '_', $car->nopol) .
            '_' .
            $year .
            '_' .
            $month .
            '.xlsx';

        return new StreamedResponse(function () use ($writer) {

            $writer->save('php://output');

        }, 200, [
            'Content-Type' =>
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

            'Content-Disposition' =>
                "attachment;filename=\"$filename\"",

            'Cache-Control' => 'max-age=0',
        ]);
    }
}

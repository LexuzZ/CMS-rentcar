<?php

namespace App\Filament\Pages\Worksheet\Widgets;

use App\Models\Attendance;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AttendanceMonthlySummaryWidget extends Widget
{
    protected static string $view = 'filament.widgets.attendance-monthly-summary';

    // Lebar widget: full width
    protected int | string | array $columnSpan = 'full';

    // State filter
    public ?int $selectedUserId = null;
    public string $selectedMonth = '';
    public string $selectedYear = '';

    public function mount(): void
    {
        $this->selectedMonth = now()->format('m');
        $this->selectedYear  = now()->format('Y');

        // Jika bukan superadmin, default ke user sendiri
        // Auth::user()->role === 'superadmin';
    }

    // ------------------------------------------------------------------
    // Computed: daftar user untuk dropdown (hanya superadmin yang bisa pilih)
    // ------------------------------------------------------------------
    public function getUsers(): \Illuminate\Support\Collection
    {
        return User::orderBy('name')->pluck('name', 'id');
    }

    // ------------------------------------------------------------------
    // Computed: ringkasan kehadiran
    // ------------------------------------------------------------------
    public function getSummary(): array
    {
        $query = Attendance::query()
            ->whereMonth('date', $this->selectedMonth)
            ->whereYear('date', $this->selectedYear);

        if ($this->selectedUserId) {
            $query->where('user_id', $this->selectedUserId);
        }

        $rows = $query->get();

        $totalHariKerja = $this->getWorkingDays((int) $this->selectedYear, (int) $this->selectedMonth);
        $hadir          = $rows->where('status', 'hadir')->count();
        $terlambat      = $rows->where('status', 'terlambat')->count();
        $izin           = $rows->where('status', 'izin')->count();
        $alpha          = $rows->where('status', 'alpha')->count();
        $totalTercatat  = $rows->count();
        $tidakTercatat  = max(0, $totalHariKerja - $totalTercatat);

        // Persentase kehadiran (hadir + terlambat dihitung hadir fisik)
        $totalHadir    = $hadir + $terlambat;
        $persentase    = $totalHariKerja > 0
            ? round(($totalHadir / $totalHariKerja) * 100, 1)
            : 0;

        // Rata-rata jam masuk
        $checkIns = $rows->whereNotNull('check_in_time')->pluck('check_in_time');
        $avgCheckIn = null;
        if ($checkIns->isNotEmpty()) {
            $totalSeconds = $checkIns->sum(function ($time) {
                [$h, $m, $s] = explode(':', $time);
                return ($h * 3600) + ($m * 60) + $s;
            });
            $avgSeconds = (int) ($totalSeconds / $checkIns->count());
            $avgCheckIn = sprintf('%02d:%02d', intdiv($avgSeconds, 3600), intdiv($avgSeconds % 3600, 60));
        }

        return [
            'hari_kerja'    => $totalHariKerja,
            'hadir'         => $hadir,
            'terlambat'     => $terlambat,
            'izin'          => $izin,
            'alpha'         => $alpha,
            'tidak_tercatat' => $tidakTercatat,
            'persentase'    => $persentase,
            'avg_check_in'  => $avgCheckIn ?? '—',
            'total_hadir'   => $totalHadir,
        ];
    }

    // ------------------------------------------------------------------
    // Helper: hitung hari kerja (Senin–Jumat) dalam sebulan
    // ------------------------------------------------------------------
    private function getWorkingDays(int $year, int $month): int
    {
        return \Carbon\Carbon::create($year, $month, 1)->daysInMonth;
    }

    // ------------------------------------------------------------------
    // Listener reaktif saat filter berubah
    // ------------------------------------------------------------------


    public function getSelectedUserName(): string
    {
        if (!$this->selectedUserId) {
            return 'Semua Karyawan';
        }
        return User::find($this->selectedUserId)?->name ?? '—';
    }

    public function getMonthLabel(): string
    {
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        return $months[$this->selectedMonth] ?? $this->selectedMonth;
    }
    public static function canView(): bool
    {
        return Auth::user()->role === 'superadmin';
    }
}

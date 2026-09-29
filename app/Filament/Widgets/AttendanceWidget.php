<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AttendanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.attendance-widget';

    protected static ?int $sort = 0; // tampil paling atas

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => '10',
        'lg' => '10',
    ];

    protected static bool $isLazy = false;

    // Koordinat kantor — GANTI dengan koordinat kantor Semeton Pesiar
    const OFFICE_LAT = -8.598337531302349;  // ← ganti ini

    const OFFICE_LON = 116.08490908043622; // ← ganti ini

    const MAX_DISTANCE = 500;       // meter

    // Jam kerja
    const JAM_MASUK = '08:30';

    const JAM_MASUK_BATAS = '09:00'; // lewat jam ini = terlambat

    const JAM_KERJA = [
        'admin' => 10,
        'supervisor' => 12,
        'staff' => 12,
    ];

    const JAM_KERJA_DEFAULT = 12;

    /**
     * Durasi kerja wajib (jam) untuk user yang sedang login
     */
    public function getWorkHours(): int
    {
        $user = Auth::user();

        foreach (self::JAM_KERJA as $role => $hours) {
            if ($user->hasRole($role)) {
                return $hours;
            }
        }

        return self::JAM_KERJA_DEFAULT;
    }

    public bool $loading = false;

    public bool $hasCheckedIn = false;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->hasCheckedIn = Attendance::alreadyCheckedIn(Auth::id());
    }

    /**
     * Dipanggil dari JS setelah browser berhasil ambil koordinat
     */
    public function checkIn(float $latitude, float $longitude): void
    {
        $this->errorMessage = null;
        $user = Auth::user();

        // Cek sudah absen
        if (Attendance::alreadyCheckedIn($user->id)) {
            $this->errorMessage = 'Anda sudah melakukan absensi hari ini.';

            return;
        }

        // Hitung jarak
        $distance = Attendance::calculateDistance(
            self::OFFICE_LAT, self::OFFICE_LON,
            $latitude, $longitude
        );

        if ($distance > self::MAX_DISTANCE) {
            $this->errorMessage = sprintf(
                'Anda berada %.0f meter dari kantor. Absensi hanya bisa dilakukan dalam radius %d meter.',
                $distance,
                self::MAX_DISTANCE
            );

            return;
        }

        // Tentukan status: hadir atau terlambat
        $now = Carbon::now();
        $batas = Carbon::today()->setTimeFromTimeString(self::JAM_MASUK_BATAS);
        $status = $now->greaterThan($batas) ? 'terlambat' : 'hadir';

        Attendance::create([
            'user_id' => $user->id,
            'date' => today(),
            'check_in_time' => $now->format('H:i:s'),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance_meters' => round($distance, 2),
            'status' => $status,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);

        $this->hasCheckedIn = true;
        $this->errorMessage = null;

        Notification::make()
            ->title($status === 'terlambat' ? '⚠️ Absen Terlambat' : '✅ Absensi Berhasil')
            ->body($status === 'terlambat'
                ? 'Anda tercatat terlambat pada '.$now->format('H:i').'.'
                : 'Selamat bekerja! Tercatat masuk pukul '.$now->format('H:i').'.'
            )
            ->status($status === 'terlambat' ? 'warning' : 'success')
            ->send();
    }

    public function getEarliestCheckOut(Attendance $record): Carbon
    {
        $checkIn = Carbon::parse(
            Carbon::parse($record->date)->toDateString().' '.$record->check_in_time
        );

        return $checkIn->addHours($this->getWorkHours());
    }

    /**
     * Dipanggil dari JS setelah browser berhasil ambil koordinat (absen pulang)
     */
    public function checkOut(float $latitude, float $longitude): void
    {
        $this->errorMessage = null;

        $record = Attendance::where('user_id', Auth::id())
            ->whereDate('date', today())
            ->first();

        if (! $record) {
            $this->errorMessage = 'Anda belum melakukan absensi masuk hari ini.';

            return;
        }

        if ($record->check_out_time) {
            $this->errorMessage = 'Anda sudah melakukan absensi pulang hari ini.';

            return;
        }

        // Cek durasi kerja minimum
        $now = Carbon::now();
        $earliest = $this->getEarliestCheckOut($record);

        if ($now->lessThan($earliest)) {
            $this->errorMessage = sprintf(
                'Belum waktunya pulang. Durasi kerja %d jam, Anda baru bisa absen pulang mulai pukul %s.',
                $this->getWorkHours(),
                $earliest->format('H:i')
            );

            return;
        }

        // Cek jarak
        $distance = Attendance::calculateDistance(
            self::OFFICE_LAT, self::OFFICE_LON,
            $latitude, $longitude
        );

        if ($distance > self::MAX_DISTANCE) {
            $this->errorMessage = sprintf(
                'Anda berada %.0f meter dari kantor. Absen pulang hanya bisa dilakukan dalam radius %d meter.',
                $distance,
                self::MAX_DISTANCE
            );

            return;
        }

        $record->update([
            'check_out_time' => $now->format('H:i:s'),
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'check_out_distance_meters' => round($distance, 2),
        ]);

        Notification::make()
            ->title('✅ Absen Pulang Berhasil')
            ->body('Terima kasih! Tercatat pulang pukul '.$now->format('H:i').'.')
            ->success()
            ->send();
    }

    public static function canView(): bool
    {
        // Semua user yang login bisa lihat widget ini
        return Auth::user()?->hasAnyRole(['staff', 'admin', 'supervisor']) ?? false;
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class UserActivityFeedWidget extends Widget
{
    protected static string $view = 'filament.widgets.user-activity-feed';
    protected static ?int   $sort = 7;
    protected static bool   $isLazy = true;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = '60s';

    public int    $perPage      = 15;
    public string $filterEvent  = 'all';   // all | created | updated | deleted
    public string $filterPeriod = 'today'; // today | week | month

    public function loadMore(): void   { $this->perPage += 15; }
    public function setEvent(string $e): void  { $this->filterEvent = $e;  $this->perPage = 15; }
    public function setPeriod(string $p): void { $this->filterPeriod = $p; $this->perPage = 15; }

    protected function getViewData(): array
    {
        $query = Activity::with('causer')
            ->latest()
            ->when($this->filterPeriod === 'today', fn($q) => $q->whereDate('created_at', today()))
            ->when($this->filterPeriod === 'week',  fn($q) => $q->where('created_at', '>=', now()->subWeek()))
            ->when($this->filterPeriod === 'month', fn($q) => $q->where('created_at', '>=', now()->startOfMonth()))
            ->when($this->filterEvent !== 'all',    fn($q) => $q->where('event', $this->filterEvent));

        $total      = $query->count();
        $activities = $query->limit($this->perPage)->get();

        // Summary counts (today)
        $todayCreated = Activity::whereDate('created_at', today())->where('event', 'created')->count();
        $todayUpdated = Activity::whereDate('created_at', today())->where('event', 'updated')->count();
        $todayDeleted = Activity::whereDate('created_at', today())->where('event', 'deleted')->count();

        // Map: model class → label indo
        $modelLabels = [
            'Booking'        => 'Booking',
            'Car'            => 'Mobil',
            'Customer'       => 'Pelanggan',
            'Invoice'        => 'Invoice',
            'Payment'        => 'Pembayaran',
            'Penalty'        => 'Penalti',
            'Driver'         => 'Driver',
            'User'           => 'Pengguna',
            'Pengeluaran'    => 'Pengeluaran',
            'ServiceHistory' => 'Servis',
        ];

        $mapped = $activities->map(function (Activity $a) use ($modelLabels) {
            $causerName  = $a->causer?->name ?? 'Sistem';
            $causerEmail = $a->causer?->email ?? '';
            $initial     = mb_strtoupper(mb_substr($causerName, 0, 1));
            $event       = $a->event ?? $a->description ?? 'updated';
            $subject     = $a->subject_type ? class_basename($a->subject_type) : null;
            $subjectLabel = $subject ? ($modelLabels[$subject] ?? $subject) : null;
            $subjectId   = $a->subject_id;

            // Build human sentence
            $eventText = match($event) {
                'created' => 'menambahkan',
                'updated' => 'mengubah',
                'deleted' => 'menghapus',
                default   => $event,
            };

            // Get changed fields for updated
            $changedFields = [];
            if ($event === 'updated' && $a->properties?->has('old')) {
                $old = $a->properties->get('old', []);
                $changedFields = array_keys($old);
            }

            return [
                'id'            => $a->id,
                'causerName'    => $causerName,
                'causerEmail'   => $causerEmail,
                'initial'       => $initial,
                'event'         => $event,
                'eventText'     => $eventText,
                'subjectLabel'  => $subjectLabel,
                'subjectId'     => $subjectId,
                'changedFields' => array_slice($changedFields, 0, 4),
                'description'   => $a->description,
                'timeRel'       => $a->created_at->locale('id')->diffForHumans(),
                'timeAbs'       => $a->created_at->format('d M Y, H:i'),
                'isToday'       => $a->created_at->isToday(),
            ];
        });

        return [
            'activities'    => $mapped,
            'total'         => $total,
            'hasMore'       => $total > $this->perPage,
            'todayCreated'  => $todayCreated,
            'todayUpdated'  => $todayUpdated,
            'todayDeleted'  => $todayDeleted,
            'filterEvent'   => $this->filterEvent,
            'filterPeriod'  => $this->filterPeriod,
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['superadmin', 'admin']);
    }
}

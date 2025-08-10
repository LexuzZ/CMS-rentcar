<x-filament::page>
    {{-- Filter Section --}}
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Filter Nama Mobil (diubah untuk menggunakan CarModel) --}}
            <div>
                <label for="mobilFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Filter Nama Mobil</label>
                <select id="mobilFilter" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <option value="">Semua Model</option>
                    {{-- Mengambil data dari tabel car_models --}}
                    @foreach (\App\Models\CarModel::orderBy('name')->get() as $model)
                        <option value="{{ $model->name }}">{{ $model->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Nopol (tetap sama) --}}
            <div>
                <label for="nopolFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Filter Nopol</label>
                <select id="nopolFilter" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <option value="">Semua Nopol</option>
                    @foreach (\App\Models\Car::orderBy('nopol')->get() as $car)
                        <option value="{{ $car->nopol }}">{{ $car->nopol }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-filament::section>

    {{-- Calendar Section --}}
    <x-filament::section>
        <div id="calendar" class="text-sm"></div>
    </x-filament::section>

</x-filament::page>

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');
            let mobilFilterEl = document.getElementById('mobilFilter');
            let nopolFilterEl = document.getElementById('nopolFilter');

            // Fungsi untuk mengambil event dari API
            function fetchEvents(fetchInfo, successCallback, failureCallback) {
                let url = new URL('/api/bookings-calendar', window.location.origin);
                if (mobilFilterEl.value) url.searchParams.append('mobil', mobilFilterEl.value);
                if (nopolFilterEl.value) url.searchParams.append('nopol', nopolFilterEl.value);

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            }

            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                timeZone:'local',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth,timeGridDay',
                },
                events: fetchEvents,
                eventClick: function(info) {
                    // Arahkan ke halaman edit booking saat event diklik
                    let bookingId = info.event.id;
                    if(bookingId) {
                        window.open(`/admin/bookings/${bookingId}/edit`, '_blank');
                    }
                }
            });

            calendar.render();

            // Fungsi untuk memuat ulang event saat filter berubah
            function refetch() {
                calendar.refetchEvents();
            }

            mobilFilterEl.addEventListener('change', refetch);
            nopolFilterEl.addEventListener('change', refetch);
        });
    </script>
@endpush

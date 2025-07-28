<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 bg-amber-500 dark:text-white text-gray-700">
        <div>
        <label for="mobilFilter" class="block  text-sm bg-amber-500 text-gray-700 dark:text-white">Filter Nama Mobil</label>
        <select id="mobilFilter" class="form-select mt-1 block w-full rounded-md  text-gray-700 dark:text-gray-100">
            <option value="">Semua</option>
            @foreach (\App\Models\Car::orderBy('nama_mobil')->get() as $car)
                <option value="{{ $car->nama_mobil }}">{{ $car->nama_mobil }}</option>
            @endforeach
        </select>
    </div>

        <div>
            <label for="nopolFilter" class="block font-medium text-sm bg-amber-500 text-gray-700 dark:text-white">Filter Nopol</label>
            <select id="nopolFilter" class="form-select mt-1 block w-full rounded-md bg-amber-500 text-gray-700 dark:text-gray-100">
                <option value="">Semua</option>
                @foreach (\App\Models\Car::orderBy('nopol')->get() as $car)
                    <option value="{{ $car->nopol }}">{{ $car->nopol }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="calendar" class="text-sm dark:text-white text-gray-700 bg-amber-400"></div>
</x-filament::page>

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');
            let mobilFilterEl = document.getElementById('mobilFilter');
            let nopolFilterEl = document.getElementById('nopolFilter');

            let currentMobil = '';
            let currentNopol = '';

            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                timeZone:'local',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,listMonth,timeGridDay',
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    let url = new URL('/api/bookings-calendar', window.location.origin);
                    if (currentMobil) url.searchParams.append('mobil', currentMobil);
                    if (currentNopol) url.searchParams.append('nopol', currentNopol);

                    fetch(url)
                        .then(response => response.json())
                        .then(data => successCallback(data))
                        .catch(error => failureCallback(error));
                }
            });

            calendar.render();

            function refetch() {
                currentMobil = mobilFilterEl.value;
                currentNopol = nopolFilterEl.value;
                calendar.refetchEvents();
            }

            mobilFilterEl.addEventListener('change', refetch);
            nopolFilterEl.addEventListener('change', refetch);
        });
    </script>
@endpush

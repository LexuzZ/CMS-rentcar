<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Booking Kendaraan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 to-blue-700 p-4 font-sans">

    <div class="bg-white shadow-2xl rounded-xl p-8 w-full max-w-lg animate-fade-in-up">

        <div class="text-center mb-4">
            <h2 class="text-3xl font-bold text-gray-800">Form Booking Rental Mobil</h2>
            <p class="text-base text-gray-600 mt-2">Hi, {{ $customer->nama }} 👋</p>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6 text-sm text-blue-800">
            <p><strong class="font-medium">Nama:</strong> {{ $customer->nama }}</p>
            <p><strong class="font-medium">NIK:</strong> {{ $customer->ktp }}</p>
        </div>

        <form id="bookingForm" class="space-y-5">
            @csrf

            {{-- Pilihan Mobil --}}
            <div>
                <label for="mobil" class="block text-sm font-medium text-gray-700 mb-2">Pilih Mobil</label>
                <select id="mobil"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 transition duration-200"
                    required>
                    <option value="">-- Pilih Mobil --</option>
                    @foreach ($cars as $car)
                        <option value="{{ $car->carModel->name }}">
                            {{ $car->carModel->brand->name }} - {{ $car->carModel->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal Keluar & Kembali --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="tanggal_keluar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                        Keluar</label>
                    <input type="date" id="tanggal_keluar"
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label for="tanggal_kembali" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                        Kembali</label>
                    <input type="date" id="tanggal_kembali"
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500" required>
                </div>
            </div>

            {{-- Jam --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="jam_keluar" class="block text-sm font-medium text-gray-700 mb-2">Waktu Pengantaran</label>
                    <input type="time" id="jam_keluar"
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label for="jam_kembali" class="block text-sm font-medium text-gray-700 mb-2">Waktu Pengembalian</label>
                    <input type="time" id="jam_kembali"
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500" required>
                </div>
            </div>

            {{-- Paket --}}
            <div>
                <label for="paket" class="block text-sm font-medium text-gray-700 mb-2">Pilih Paket Sewa</label>
                <select id="paket"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500" required>
                    <option value="">-- Pilih Paket --</option>
                    <option value="Lepas Kunci">Lepas Kunci</option>
                    <option value="Dengan Driver">Dengan Driver</option>
                    <option value="12 Jam Lepas Kunci">12 Jam (Lepas Kunci)</option>
                    <option value="12 Jam Dengan Driver">12 Jam (Dengan Driver)</option>
                    <option value="Paket Tour">Paket Tour</option>
                </select>
            </div>

            {{-- Lokasi --}}
            <div>
                <label for="lokasi_pengantaran" class="block text-sm font-medium text-gray-700 mb-2">Lokasi
                    Pengantaran</label>
                <input type="text" id="lokasi_pengantaran" placeholder="Hotel/Alamat Lengkap..."
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="lokasi_pengembalian" class="block text-sm font-medium text-gray-700 mb-2">Lokasi
                    Pengembalian</label>
                <input type="text" id="lokasi_pengembalian" placeholder="Hotel/Alamat Lengkap..."
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500" required>
            </div>

            {{-- Facebook --}}
            <div>
                <label for="facebook" class="block text-sm font-medium text-gray-700 mb-2">Facebook (Opsional)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M22.675 0H1.325A1.326 1.326 0 000 1.325v21.351A1.326 1.326 0 001.325 24h11.497v-9.294H9.692V11.01h3.13V8.41c0-3.1 1.894-4.788 4.66-4.788 1.325 0 2.463.099 2.794.143v3.24h-1.917c-1.505 0-1.797.716-1.797 1.767v2.317h3.59l-.467 3.697h-3.123V24h6.116A1.326 1.326 0 0024 22.676V1.325A1.326 1.326 0 0022.675 0z" />
                        </svg>
                    </span>
                    <input type="text" id="facebook" placeholder="Masukkan link atau username Facebook..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Instagram --}}
            <div>
                <label for="instagram" class="block text-sm font-medium text-gray-700 mb-2">Instagram (Opsional)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-pink-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2.2c3.2 0 3.584.012 4.85.07 1.17.056 1.96.24 2.418.404a4.9 4.9 0 011.772 1.153 4.9 4.9 0 011.153 1.772c.164.458.348 1.248.404 2.418.058 1.266.07 1.65.07 4.85s-.012 3.584-.07 4.85c-.056 1.17-.24 1.96-.404 2.418a4.9 4.9 0 01-1.153 1.772 4.9 4.9 0 01-1.772 1.153c-.458.164-1.248.348-2.418.404-1.266.058-1.65.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.056-1.96-.24-2.418-.404a4.9 4.9 0 01-1.772-1.153 4.9 4.9 0 01-1.153-1.772c-.164-.458-.348-1.248-.404-2.418C2.212 15.584 2.2 15.2 2.2 12s.012-3.584.07-4.85c.056-1.17.24-1.96.404-2.418a4.9 4.9 0 011.153-1.772A4.9 4.9 0 015.6 1.78c.458-.164 1.248-.348 2.418-.404C9.284 1.312 9.668 1.3 12 1.3zm0 1.8c-3.17 0-3.548.012-4.797.07-1.03.048-1.59.22-1.96.366-.493.191-.845.42-1.215.79a3.1 3.1 0 00-.79 1.215c-.146.37-.318.93-.366 1.96-.058 1.25-.07 1.627-.07 4.797s.012 3.548.07 4.797c.048 1.03.22 1.59.366 1.96.191.493.42.845.79 1.215.37.37.722.599 1.215.79.37.146.93.318 1.96.366 1.25.058 1.627.07 4.797.07s3.548-.012 4.797-.07c1.03-.048 1.59-.22 1.96-.366.493-.191.845-.42 1.215-.79.37-.37.599-.722.79-1.215.146-.37.318-.93.366-1.96.058-1.25.07-1.627.07-4.797s-.012-3.548-.07-4.797c-.048-1.03-.22-1.59-.366-1.96a3.1 3.1 0 00-.79-1.215 3.1 3.1 0 00-1.215-.79c-.37-.146-.93-.318-1.96-.366C15.548 4.012 15.17 4 12 4zM12 7.2a4.8 4.8 0 110 9.6 4.8 4.8 0 010-9.6zm0 1.8a3 3 0 100 6 3 3 0 000-6zm5.85-1.95a1.12 1.12 0 110 2.24 1.12 1.12 0 010-2.24z" />
                        </svg>
                    </span>
                    <input type="text" id="instagram" placeholder="Masukkan username Instagram..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Catatan --}}
            <div>
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                <textarea id="catatan" rows="3"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500"
                    placeholder="Contoh: Minta baby seat, jemput di bandara..."></textarea>
            </div>

            <button type="submit"
                class="w-full py-3 px-4 mt-6 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition-all">
                Kirim Booking via WhatsApp
            </button>
        </form>
    </div>

    <script>
        document.getElementById('bookingForm').onsubmit = function(e) {
            e.preventDefault();

            let nama = "{{ $customer->nama }}";
            let no_telp = "{{ $customer->no_telp }}";
            let ktp = "{{ $customer->ktp }}";

            let mobil = document.getElementById("mobil").value;
            let tanggal_keluar = document.getElementById("tanggal_keluar").value;
            let tanggal_kembali = document.getElementById("tanggal_kembali").value;
            let jam_keluar = document.getElementById("jam_keluar").value;
            let jam_kembali = document.getElementById("jam_kembali").value;
            let paket = document.getElementById("paket").value;
            let lokasi_pengantaran = document.getElementById("lokasi_pengantaran").value;
            let lokasi_pengembalian = document.getElementById("lokasi_pengembalian").value;
            let facebook = document.getElementById("facebook").value.trim();
            let instagram = document.getElementById("instagram").value.trim();
            let catatan = document.getElementById("catatan").value.trim();

            if (!mobil || !tanggal_keluar || !tanggal_kembali || !jam_keluar || !jam_kembali || !lokasi_pengantaran || !lokasi_pengembalian) {
                alert("Mohon lengkapi semua field wajib (Mobil, Tanggal, Jam, Lokasi)!");
                return;
            }

            const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });

            let message = `
*--- 🚗 BOOKING RENTAL MOBIL 🚗 ---*

*Nama:* ${nama}
*NIK:* ${ktp}
*WhatsApp:* ${no_telp}
*Facebook:* ${facebook || '-'}
*Instagram:* ${instagram || '-'}

*DETAIL BOOKING:*
*Mobil:* ${mobil}
*Tanggal Keluar:* ${formatDate(tanggal_keluar)}
*Tanggal Kembali:* ${formatDate(tanggal_kembali)}
*Jam Antar:* ${jam_keluar}
*Jam Kembali:* ${jam_kembali}
*Paket Sewa:* ${paket}
*Lokasi Antar:* ${lokasi_pengantaran}
*Lokasi Pengembalian:* ${lokasi_pengembalian}

*Catatan:*
${catatan || '-'}
`;

            let wa = "6281128948884"; // GANTI DENGAN NOMOR ADMIN
            window.open(`https://wa.me/${wa}?text=${encodeURIComponent(message)}`);
        };
    </script>

</body>
</html>

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
            {{-- <p><strong>WhatsApp:</strong> {{ $customer->no_telp }}</p> --}}
        </div>
        {{-- PESAN SUKSES & INFO --}}
        <div class="mb-4 space-y-3">
            @if (session('success'))
                <div class="bg-green-500 text-white rounded-lg p-3 flex items-center gap-2 text-sm font-medium">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="bg-blue-500 text-white rounded-lg p-3 flex items-center gap-2 text-sm font-medium">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif
        </div>

        <form id="bookingForm" class="space-y-5">
            @csrf

            <div>
                <label for="mobil" class="block text-sm font-medium text-gray-700 mb-2">Pilih Mobil</label>
                <select id="mobil"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200"
                    required>
                    <option value="">-- pilih mobil --</option>
                    @foreach ($cars as $car)
                        <option value="{{ $car->carModel->name }}">
                            {{ $car->carModel->brand->name }} - {{ $car->carModel->name }}
                        </option>
                    @endforeach
                </select>
                {{-- <select id="Mobil"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200"
                    required>
                    <option value="">-- Pilih Mobil --</option>
                    <option value="Avanza">Toyota Avanza</option>
                    <option value="Innova">Toyota Innova Zenix</option>
                    <option value="Innova">Toyota Innova Reborn</option>

                </select> --}}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="tanggal_keluar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                        Keluar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <input type="date" id="tanggal_keluar"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-700"
                            required>
                    </div>
                </div>
                <div>
                    <label for="tanggal_kembali" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                        Kembali</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <input type="date" id="tanggal_kembali"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-700"
                            required>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="jam_keluar" class="block text-sm font-medium text-gray-700 mb-2">Waktu
                        Pengantaran</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <input type="time" id="jam_keluar"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-700"
                            required>
                    </div>
                </div>
                <div>
                    <label for="jam_kembali" class="block text-sm font-medium text-gray-700 mb-2">Waktu
                        Pengantaran</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <input type="time" id="jam_kembali"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-700"
                            required>
                    </div>
                </div>
            </div>
            <div>
                <label for="paket" class="block text-sm font-medium text-gray-700 mb-2">Pilih Paket Sewa</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.81m5.84-2.57a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.57m5.841-2.57l-2.57-5.84m0 0a14.926 14.926 0 00-2.57 5.84m2.57-5.84l.7-1.68a.75.75 0 011.4 0l.7 1.68m-2.8 0l-.63.63.63-.63zM6.75 12.75l-1.06-1.06a1.5 1.5 0 010-2.12l1.06-1.06a1.5 1.5 0 012.12 0l1.06 1.06a1.5 1.5 0 010 2.12l-1.06 1.06a1.5 1.5 0 01-2.12 0z" />
                        </svg>
                    </div>
                    <select id="paket"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200"
                        required>
                        <option value="">-- Pilih Paket --</option>
                        <option value="Lepas Kunci">Lepas Kunci</option>
                        <option value="Dengan Driver">Dengan Driver</option>
                        <option value="12 Jam">12 Jam (Lepas Kunci)</option>
                        <option value="12 Jam">12 Jam (Dengan Driver)</option>
                        <option value="Paket Tour">Paket Tour</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="lokasi_pengantaran" class="block text-sm font-medium text-gray-700 mb-2">Lokasi
                    Pengantaran</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <input type="text" id="lokasi_pengantaran" placeholder="Hotel/Alamat Lengkap..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label for="lokasi_pengembalian" class="block text-sm font-medium text-gray-700 mb-2">Lokasi
                    Pengembalian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <input type="text" id="lokasi_pengembalian" placeholder="Hotel/Alamat Lengkap..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                <div class="relative">
                    <div class="absolute top-3.5 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                        </svg>
                    </div>
                    <textarea id="catatan" rows="3"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Contoh: Minta baby seat, jemput di bandara..."></textarea>
                </div>
            </div>

            <button type="submit"
                class="w-full flex justify-center items-center gap-2 py-3 px-4 !mt-8 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.875L6 12zm0 0h7.5" />
                </svg>
                <span>Kirim Booking via WhatsApp</span>
            </button>
        </form>

        <p class="text-center text-xs mt-5 text-gray-500">Admin akan merespon dalam ≤ 30 menit</p>

    </div>

    <style>
        .animate-fade-in-up {
            animation: fade-in-up 0.5s ease-out;
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Perbaikan placeholder untuk input date/time di beberapa browser */
        input[type="date"]:required:invalid::-webkit-datetime-edit,
        input[type="time"]:required:invalid::-webkit-datetime-edit {
            color: transparent;
        }

        input[type="date"]:focus::-webkit-datetime-edit,
        input[type="time"]:focus::-webkit-datetime-edit {
            color: #374151;
            /* gray-700 */
        }

        .ts-control {
            @apply w-full pl-3 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-transparent transition duration-200;
            padding-top: 0.60rem !important;
            padding-bottom: 0.60rem !important;
        }

        .ts-control input::placeholder {
            @apply text-gray-400;
        }

        .ts-control:not(.focus) input {
            padding-top: 0.1rem !important;
            /* Perbaikan kecil untuk alignment placeholder */
        }

        .ts-dropdown {
            @apply bg-white border border-gray-300 rounded-lg shadow-lg mt-1 z-50;
        }

        .ts-dropdown .ts-option {
            @apply p-2.5 transition duration-150 ease-in-out;
        }

        .ts-dropdown .ts-option:hover {
            @apply bg-indigo-50;
        }

        .ts-dropdown .active {
            @apply bg-indigo-500 text-white;
        }

        .ts-dropdown .active:hover {
            @apply bg-indigo-600 text-white;
        }
    </style>

    <script>
        document.getElementById('bookingForm').onsubmit = function(e) {
            e.preventDefault();

            // Ambil data Customer dari Blade
            let nama = "{{ $customer->nama }}";
            let no_telp = "{{ $customer->no_telp }}";
            let ktp = "{{ $customer->ktp }}";

            // Ambil data Form
            let mobil = document.getElementById("mobil").value;
            let tanggal_keluar = document.getElementById("tanggal_keluar").value;
            let tanggal_kembali = document.getElementById("tanggal_kembali").value;
            let paket = document.getElementById("paket").value;
            let jam_keluar = document.getElementById("jam_keluar").value;
            let jam_kembali = document.getElementById("jam_kembali").value;
            let lokasi_pengantaran = document.getElementById("lokasi_pengantaran").value;
            let lokasi_pengembalian = document.getElementById("lokasi_pengembalian").value;
            let catatan = document.getElementById("catatan").value;

            // --- VALIDASI (Diperbaiki) ---
            if (!mobil || !tanggal_keluar || !tanggal_kembali || !jam_keluar || !jam_kembali || !lokasi_pengantaran || !
                lokasi_pengembalian) {
                alert("Mohon lengkapi semua field wajib (Mobil, Tanggal, Jam, Lokasi)!");
                return;
            }

            // --- (Tambahan) Format Tanggal agar lebih rapi ---
            const formatDate = (dateString) => {
                if (!dateString) return '';
                const options = {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            };

            let tglKeluarFormatted = formatDate(tanggal_keluar);
            let tglKembaliFormatted = formatDate(tanggal_kembali);
            let catatanText = catatan.trim() === '' ? 'Tidak ada' : catatan;

            // --- Format Pesan WhatsApp (Ditingkatkan) ---
            let message = `
*--- 🚗 BOOKING RENTAL MOBIL 🚗 ---*

*Nama:* ${nama}
*NIK:* ${ktp}
*WhatsApp:* ${no_telp}

*DETAIL BOOKING:*
*Mobil:* ${mobil}
*Tanggal Keluar:* ${tglKeluarFormatted}
*Tanggal Kembali:* ${tglKembaliFormatted}
*Jam Antar:* ${jam_keluar}
*Jam Kembali:* ${jam_kembali}
*Paket Sewa:* ${paket}
*Lokasi Antar:* ${lokasi_pengantaran}
*Lokasi Pengembalian:* ${lokasi_pengembalian}

*Catatan:*
${catatanText}

Mohon segera cek ketersediaan unit. Terima kasih 🙏
`;

            let wa = "6281128948884"; // GANTI NOMOR ADMIN
            window.open(`https://wa.me/${wa}?text=${encodeURIComponent(message)}`);
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new TomSelect('#mobil', {
                create: false, // tidak izinkan user menambah mobil baru
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "-- Cari dan Pilih Mobil --"
            });
        });
    </script>

</body>

</html>

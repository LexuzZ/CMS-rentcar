<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak | Semeton Pesiar</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .card {
            background: white;
            color: #1e3a8a;
            border-radius: 1rem;
            padding: 3rem 2rem;
            max-width: 480px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            animation: fadeIn 0.7s ease-in-out;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: #2563eb;
            line-height: 1;
        }
        .error-message {
            font-size: 1.25rem;
            font-weight: 500;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
        }
        .btn {
            background-color: #2563eb;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn:hover {
            background-color: #1e40af;
            transform: translateY(-2px);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="error-code">403</div>
        <div class="error-message">Akses Ditolak 🚫</div>
        <p class="text-gray-600 mb-6">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator sistem.
        </p>

        @if(auth()->check())
            <a href="{{ url()->previous() }}" class="btn">Kembali ke Halaman Sebelumnya</a>
        @else
            <a href="{{ route('filament.admin.auth.login') }}" class="btn">Masuk ke Akun</a>
        @endif
    </div>
</body>
</html>

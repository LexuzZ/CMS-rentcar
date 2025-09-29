<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Operasional {{ $reportTitle }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Operasional - {{ $reportTitle }}</h2>

    <h3>Ringkasan</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Nilai</th>
                <th>Perubahan</th>
            </tr>
        </thead>
        <tbody>
        @foreach($summaryTableData as $row)
            <tr>
                <td>{{ $row['label'] }}</td>
                <td>
                    @if(is_numeric($row['value']))
                        Rp {{ number_format($row['value'], 0, ',', '.') }}
                    @else
                        {{ $row['value'] }}
                    @endif
                </td>
                <td>
                    @if(!is_null($row['change']))
                        {{ number_format($row['change'], 1) }}%
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h3>Rincian Pendapatan</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Nilai</th>
                <th>Perubahan</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rincianTableData as $row)
            <tr>
                <td>{{ $row['label'] }}</td>
                <td>Rp {{ number_format($row['value'], 0, ',', '.') }}</td>
                <td>{{ $row['change'] ?? '-' }}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h3>Rincian Biaya</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Nilai</th>
                <th>Perubahan</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rincianCostTableData as $row)
            <tr>
                <td>{{ $row['label'] }}</td>
                <td>Rp {{ number_format($row['value'], 0, ',', '.') }}</td>
                <td>{{ $row['change'] ?? '-' }}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

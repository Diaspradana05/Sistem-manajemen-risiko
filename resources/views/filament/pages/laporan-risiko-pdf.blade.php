<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Risiko PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Laporan Risiko</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Risiko</th>
                <th>Kode Risiko</th>
                <th>Dampak</th>
                <th>Probabilitas</th>
                <th>Skor Risiko</th>
                <th>Peringkat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($risikos as $index => $risiko)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $risiko->risiko }}</td>
                    <td>{{ $risiko->kode_risiko }}</td>
                    <td>{{ $risiko->dampak }}</td>
                    <td>{{ $risiko->analisa_probabilitas }}</td>
                    <td>{{ $risiko->skor }}</td>
                    <td>{{ $risiko->peringkat_risiko }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

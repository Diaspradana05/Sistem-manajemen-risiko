<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Risiko</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 2px;
        }
        h2 {
            text-align: center;
            font-size: 15px;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .logo {
            text-align: center;
            margin-bottom: 6px;
        }
        .logo img {
            width: 80px;
            height: auto;
        }
        .small-info {
            text-align: right;
            font-size: 11px;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #1f4e78;
            color: #fff;
        }
        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        tbody tr:nth-child(even) {
            background-color: #dce6f1;
        }
        .signature {
            margin-top: 40px;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- Logo dan Judul --}}
    <div class="logo">
        <img src="{{ public_path('image/PT Cipta Nirmala.png') }}" alt="Logo">
    </div>
    <h1>Sistem Manajemen Risiko</h1>
    <h2>Laporan Risiko</h2>

    <div class="small-info">
        Tanggal : {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

    <table>
    <thead>
        <tr>
            <th>No</th>
            <th>Company</th>
            <th>Divisi</th>
            <th>Tahun</th>
            <th>Nama Risiko</th>
            <th>Kode Risiko</th>
            <th>Dampak</th>
            <th>Probabilitas</th>
            <th>Skor Risiko</th>
            <th>Peringkat</th>
            <th>Tipe Risiko</th>
            <th>Status Persetujuan</th>
            <th>Dibuat Oleh</th>
            <th>Ditinjau Oleh</th>
            <th>Tanggal Tinjau</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($risikos as $index => $risiko)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ optional($risiko->company)->name }}</td>
                <td>{{ optional($risiko->division)->name }}</td>
                <td>{{ $risiko->year }}</td>
                <td>{{ $risiko->risiko }}</td>
                <td>{{ $risiko->kode_risiko }}</td>
                <td>{{ $risiko->dampak }}</td>
                <td>{{ $risiko->analisa_probabilitas }}</td>
                <td>{{ $risiko->skor }}</td>
                <td>{{ $risiko->peringkat_risiko }}</td>
                <td>{{ ucfirst($risiko->tipe_risiko) }}</td>
                <td>{{ ucfirst($risiko->status_persetujuan) ?? '-' }}</td>
                <td>{{ optional($risiko->dibuatOleh)->name ?? '-' }}</td>
                <td>{{ optional($risiko->ditinjauOleh)->name ?? '-' }}</td>
                <td>{{ $risiko->ditinjau_pada ? $risiko->ditinjau_pada->format('d-m-Y') : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

    {{-- Tanda Tangan Digital dengan QR Code --}}
    <div class="signature" style="margin-top:50px; text-align:right;">
    <p>Gresik, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    <p>Manajer Divisi,</p>

    <div style="margin-top:10px; margin-bottom:5px;">
        {{-- QR Code pakai base64, langsung tampil tanpa Imagick --}}
        <img src="data:image/png;base64,{!! base64_encode(
        \SimpleSoftwareIO\QrCode\Facades\QrCode::size(90)->generate(
                'Yogi Sucahyo | Laporan Risiko | ' . \Carbon\Carbon::now()->format('d/m/Y')
            )
        ) !!}" width="90" height="90">
    </div>

    <p style="margin-top:5px;"><strong>Manajer</strong></p>
    <p style="margin:0;">No.PEG : 7109720</p>
</div>



</body>
</html>

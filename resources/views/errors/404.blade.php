<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 450px;
        }
        h1 {
            font-size: 80px;
            margin: 0;
            color: #e3342f;
            font-weight: bold;
        }
        p {
            font-size: 18px;
            color: #444;
            margin: 15px 0 30px;
        }
        a {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3490dc;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        a:hover {
            background-color: #2779bd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>Oops! Halaman yang kamu cari tidak tersedia.</p>
        <a href="{{ url()->previous() }}">⬅ Kembali</a>

    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akses Ditolak</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: #fff;
            padding: 50px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
            animation: fadeIn 0.6s ease-out;
        }

        .icon {
            font-size: 70px;
            color: #e3342f;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 40px;
            color: #e3342f;
            margin: 0 0 10px;
        }

        p {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }

        a {
            display: inline-block;
            text-decoration: none;
            background: #3490dc;
            color: white;
            padding: 12px 28px;
            border-radius: 30px;
            font-size: 16px;
            transition: background 0.3s, transform 0.2s;
        }

        a:hover {
            background: #2779bd;
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🚫</div>
        <h1>Akses Ditolak</h1>
        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="{{ url()->previous() }}">⬅ Kembali</a>
    </div>
</body>
</html>

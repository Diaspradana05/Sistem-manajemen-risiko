<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lupa Kata Sandi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body 
    class="flex items-center justify-center min-h-screen relative"
    style="background: url('{{ asset('image/rumahsakitsemen.jpg') }}') center/cover no-repeat fixed;"
>
    <!-- Overlay blur -->
    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm"></div>

    <!-- Card -->
    <div class="relative bg-white/20 backdrop-blur-lg border border-white/30 shadow-xl p-8 rounded-2xl w-full max-w-md text-black">
        <!-- Title -->
        <h2 class="text-2xl font-bold mb-6 text-center drop-shadow">🔑 Lupa Kata Sandi</h2>

        <!-- Deskripsi -->
        <p class="text-base text-black mb-4 drop-shadow font-bold">
            Lupa kata sandi Anda? Tidak masalah. Masukkan alamat email Anda, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
        </p>

        <!-- Flash Status -->
        @if (session('status'))
            <div class="bg-green-500/20 text-green-200 p-3 rounded mb-4 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-500/20 text-red-200 p-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold drop-shadow">Email</label>
                <input id="email" name="email" type="email" required autofocus
                       value="{{ old('email') }}"
                       class="w-full mt-1 px-3 py-2 border border-white/40 bg-white/20 text-black placeholder-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 font-bold">
            </div>

            <div class="text-right">
                <button type="submit"
                        class="bg-blue-600/80 text-white px-4 py-2 rounded-lg hover:bg-blue-700/90 transition shadow-lg">
                    Kirim Tautan Reset Password
                </button>
            </div>
        </form>
    </div>
</body>

</html>

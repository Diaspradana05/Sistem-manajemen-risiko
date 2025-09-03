<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .toggle-icon {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen relative"
      style="background: url('{{ asset('image/rumahsakitsemen.jpg') }}') center/cover no-repeat fixed !important;">
    
    <!-- Overlay gelap transparan -->
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Form Card -->
    <div class="relative bg-white/40 backdrop-blur-sm p-8 rounded-2xl shadow-lg w-full max-w-md border border-white/40">
        <h2 class="text-3xl font-bold mb-6 text-center text-black drop-shadow">Reset Password</h2>

        {{-- Flash status --}}
        @if (session('status'))
            <div class="bg-green-100/70 text-green-900 p-3 rounded mb-4 text-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- Error message --}}
        @if ($errors->any())
            <div class="bg-red-100/70 text-red-900 p-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-base font-semibold text-black drop-shadow">Email</label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $request->email) }}"
                       required autofocus autocomplete="username"
                       class="w-full mt-1 px-3 py-2 border border-white/40 bg-white/60 text-black placeholder-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Password -->
            <div class="mb-4 relative">
                <label for="password" class="block text-base font-semibold text-black drop-shadow">Password</label>
                <input id="password" name="password" type="password"
                       required autocomplete="new-password"
                       class="w-full mt-1 px-3 py-2 border border-white/40 bg-white/60 text-black placeholder-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 pr-10">
                <span class="toggle-icon absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-lg"
                      onclick="togglePassword('password', this)">👁️</span>
            </div>

            <!-- Confirm Password -->
            <div class="mb-6 relative">
                <label for="password_confirmation" class="block text-base font-semibold text-black drop-shadow">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       required autocomplete="new-password"
                       class="w-full mt-1 px-3 py-2 border border-white/40 bg-white/60 text-black placeholder-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 pr-10">
                <span class="toggle-icon text-black absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer"
                      onclick="togglePassword('password_confirmation', this)">👁️</span>
            </div>

            <div class="text-right">
                <button type="submit"
                        class="bg-blue-600/80 hover:bg-blue-700/90 text-white font-bold px-4 py-2 rounded-lg shadow-md transition">
                    Reset Password
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(fieldId, icon) {
            const field = document.getElementById(fieldId);
            const isPassword = field.type === 'password';
            field.type = isPassword ? 'text' : 'password';
            icon.textContent = isPassword ? '🙈' : '👁️';
        }
    </script>
</body>
</html>

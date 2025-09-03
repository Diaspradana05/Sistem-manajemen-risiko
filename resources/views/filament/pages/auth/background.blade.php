<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: url("{{ asset('image/rumahsakit.jpg') }}") no-repeat center center fixed;
            background-size: cover;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        h1, h2, label, p, a {
            color: #000 !important; /* semua font hitam */
        }
        .btn-primary {
            background-color: #2563eb; 
            border: none;
            color: #000; /* teks hitam */
            font-weight: bold;
        }
        .btn-primary:hover {
            background-color: #1e40af;
            color: #fff; /* hover teks putih */
        }
        .toggle-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #333;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md glass-card">
        
        <!-- Logo -->
        <div class="flex justify-center mb-4">
            <img src="{{ asset('image/PT Cipta Nirmala.png') }}" 
                 alt="Logo" 
                 style="height: 80px; width: auto;">
        </div>

        <!-- Judul -->
        <h1 class="text-2xl font-bold text-center mb-2">Sistem Manajemen Risiko</h1>
        <h2 class="text-xl font-bold text-center mb-6">Login</h2>

        <!-- Form -->
        <form wire:submit.prevent="authenticate" class="space-y-5">
            @csrf

            <!-- Login (Email / Username) -->
            <div>
                <label class="block text-sm font-medium">Nama / Email</label>
                <input type="text" wire:model.defer="form.login" required autofocus
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                @error('login') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Password + Toggle -->
            <div class="relative">
                <label class="block text-sm font-medium">Password</label>
                <input type="password" id="password" wire:model.defer="form.password" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                <span class="toggle-icon" onclick="togglePassword('password', this)">👁</span>
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input type="checkbox" wire:model.defer="form.remember"
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                <label class="ml-2 text-sm">Ingat saya</label>
            </div>

            <!-- Lupa Password -->
            <div class="text-right">
                <a href="{{ route('password.request') }}" class="text-sm font-semibold hover:underline">
                    Lupa Password?
                </a>
            </div>

            <!-- Button -->
            <button type="submit" 
                    class="btn-primary w-full py-2 rounded-lg transition">
                Login
            </button>
        </form>

    <script>
        function togglePassword(fieldId, icon) {
            const field = document.getElementById(fieldId);
            if (field.type === "password") {
                field.type = "text";
                icon.textContent = "🙈"; // kalau kelihatan
            } else {
                field.type = "password";
                icon.textContent = "👁"; // kalau tertutup
            }
        }
    </script>
</body>
</html>

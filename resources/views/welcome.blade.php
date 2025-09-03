<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Manajemen Risiko Rumah Sakit</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen w-screen bg-gray-900 relative font-sans">

  <!-- Background -->
  <div class="absolute inset-0">
    <img src="{{ asset('image/rumahsakitsemen.jpg') }}" 
         class="w-full h-full object-cover opacity-4.5" alt="Background Rumah Sakit">
    <div class="absolute inset-0 bg-black/70"></div>
  </div>

  <!-- Content -->
  <div class="relative z-10 flex flex-col items-center justify-center h-screen px-6">

    <!-- Logo & Title -->
    <div class="flex flex-col items-center mb-10 text-center">
      <img src="{{ asset('image/PT Cipta Nirmala.png') }}" 
           alt="Logo PT Cipta Nirmala"
           class="w-28 h-auto mb-5 drop-shadow-lg">
      <h1 class="text-4xl font-extrabold text-white drop-shadow-lg leading-snug">
        Sistem Manajemen Risiko <br> Rumah Sakit
      </h1>
      <p class="text-gray-300 mt-3 text-lg max-w-lg">
        Kelola risiko dengan lebih mudah, terintegrasi, dan terpercaya
      </p>
    </div>

    <!-- Login Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-xl">
      <a href="http://127.0.0.1:8000/admin"
         class="px-8 py-5 bg-gradient-to-r from-blue-600 to-blue-700 
                hover:from-blue-700 hover:to-blue-800 
                rounded-2xl shadow-lg text-xl font-semibold text-center 
                transition transform hover:scale-105 hover:shadow-2xl">
        Login Admin
      </a>
      <a href="http://127.0.0.1:8000/user"
         class="px-8 py-5 bg-gradient-to-r from-green-600 to-green-700 
                hover:from-green-700 hover:to-green-800 
                rounded-2xl shadow-lg text-xl font-semibold text-center 
                transition transform hover:scale-105 hover:shadow-2xl">
        Login User
      </a>
    </div>
    
  </div>

  <!-- Footer -->
  <footer class="absolute bottom-5 w-full text-center text-gray-400 text-sm">
    &copy; 2025 PT Cipta Nirmala. Semua Hak Dilindungi.
  </footer>

</body>
</html>

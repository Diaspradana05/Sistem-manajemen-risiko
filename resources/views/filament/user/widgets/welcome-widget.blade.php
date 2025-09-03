<x-filament-widgets::widget>
    <div class="flex flex-col items-start space-y-2">
        {{-- Salam utama --}}
        <div class="text-2xl sm:text-3xl text-gray-800">
            <span class="font-bold text-indigo-600">Selamat datang</span>, 
            <span class="font-bold capitalize text-gray-900">{{ auth()->user()->name }}</span>!
        </div>

        {{-- Pesan tambahan --}}
        <div class="text-gray-600 text-base sm:text-lg">
            Semoga hari Anda menyenangkan dan produktif
        </div>
    </div>
</x-filament-widgets::widget>

<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Laporan Risiko</h2>
            <div class="flex space-x-2">
                <a href="{{ route('laporan-risiko.export.excel') }}"
                   class="px-4 py-2 bg-green-600 text-black text-sm font-semibold rounded hover:bg-green-700">
                    Export Excel
                </a>
                <a href="{{ route('laporan-risiko.export.pdf') }}"
                   class="px-4 py-2 bg-red-600 text-black text-sm font-semibold rounded hover:bg-red-700">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Tabel Data Risiko -->
        <div class="overflow-x-auto bg-white shadow rounded-lg p-4">
            <table class="min-w-full text-sm text-gray-700">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">Nama Risiko</th>
                        <th class="px-4 py-2 border">Kategori</th>
                        <th class="px-4 py-2 border">Dampak</th>
                        <th class="px-4 py-2 border">Probabilitas</th>
                        <th class="px-4 py-2 border">Skor Risiko</th>
                        <th class="px-4 py-2 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->risikos as $index => $risiko)
                        <tr>
                            <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 border">{{ $risiko->risiko }}</td>
                            <td class="px-4 py-2 border">{{ $risiko->kode_risiko }}</td>
                            <td class="px-4 py-2 border">{{ $risiko->dampak }}</td>
                            <td class="px-4 py-2 border">{{ $risiko->analisa_probabilitas }}</td>
                            <td class="px-4 py-2 border">{{ $risiko->skor }}</td>
                            <td class="px-4 py-2 border">{{ $risiko->peringkat_risiko }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-4">Tidak ada data risiko.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>

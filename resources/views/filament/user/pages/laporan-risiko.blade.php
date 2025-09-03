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

        {{-- FILTER --}}
        <form wire:submit.prevent="applyFilter" class="mb-4 flex gap-3">
            @if(auth()->user()->hasRole('superadmin'))
                {{-- Company --}}
                <select wire:model="filterCompany" class="border rounded p-1">
                    <option value="">Semua Company</option>
                    @foreach(\App\Models\Company::pluck('name','id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                 @endif

                {{-- Division berdasarkan company terpilih --}}
           {{-- Division --}}
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('direksi'))
            {{-- Division untuk superadmin & direksi --}}
            <select wire:model="filterDivision" class="border rounded p-1">
                <option value="">Semua Divisi</option>
                @foreach($availableDivisions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        @else
            {{-- Division untuk user biasa --}}
            <select wire:model="filterDivision" class="border rounded p-1">
                <option value="">Semua Divisi Saya</option>
                @foreach(auth()->user()->divisions->pluck('name', 'id') as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        @endif

        {{-- Tahun --}}
        <select wire:model="filterYear" class="border rounded p-1">
            <option value="">Semua Tahun</option>
            @foreach(\App\Models\Risk::select('year')->distinct()->pluck('year') as $yr)
                <option value="{{ $yr }}">{{ $yr }}</option>
            @endforeach
        </select>

        {{-- Tombol submit --}}
        <button type="submit" class="px-3 py-1 bg-primary-600 text-white rounded">
            Terapkan
        </button>
    </form>


    <!-- Tabel Data Risiko -->
<div class="overflow-x-auto bg-white shadow rounded-lg p-4">
    <table class="min-w-full text-sm text-gray-700">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 border">No</th>
                <th class="px-4 py-2 border">Company</th>
                <th class="px-4 py-2 border">Divisi</th>
                <th class="px-4 py-2 border">Tahun</th>
                <th class="px-4 py-2 border">Nama Risiko</th>
                <th class="px-4 py-2 border">Kategori</th>
                <th class="px-4 py-2 border">Dampak</th>
                <th class="px-4 py-2 border">Probabilitas</th>
                <th class="px-4 py-2 border">Skor Risiko</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Tipe Risiko</th>
                <th class="px-4 py-2 border">Status Persetujuan</th>
                <th class="px-4 py-2 border">Dibuat Oleh</th>
                <th class="px-4 py-2 border">Ditinjau / Disetujui / Ditolak Oleh</th>
                <th class="px-4 py-2 border">Tanggal Tinjau</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($this->risikos as $index => $risiko)
                <tr>
                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                    <td class="px-4 py-2 border">{{ optional($risiko->company)->name }}</td>
                    <td class="px-4 py-2 border">{{ optional($risiko->division)->name }}</td>
                    <td class="px-4 py-2 border">{{ $risiko->year }}</td>
                    <td class="px-4 py-2 border">{{ $risiko->risiko }}</td>
                    <td class="px-4 py-2 border">{{ $risiko->kode_risiko }}</td>
                    <td class="px-4 py-2 border">{{ $risiko->dampak }}</td>
                    <td class="px-4 py-2 border">{{ $risiko->analisa_probabilitas }}</td>
                    <td class="px-4 py-2 border">{{ $risiko->skor }}</td>
                    <td class="px-4 py-2 border">{{ $risiko->peringkat_risiko }}</td>
                    <td class="px-4 py-2 border">{{ ucfirst($risiko->tipe_risiko) }}</td>

                    {{-- Tambahan --}}
                    <td class="px-4 py-2 border">{{ ucfirst($risiko->status_persetujuan ?? '-') }}</td>
                    <td class="px-4 py-2 border">{{ optional($risiko->dibuatOleh)->name ?? '-' }}</td>
<td class="px-4 py-2 border">
    @if($risiko->ditinjauOleh)
        {{ $risiko->ditinjauOleh->name }} ({{ ucfirst($risiko->status_persetujuan) }})
    @else
        -
    @endif
</td>
<td class="px-4 py-2 border">
    {{ $risiko->ditinjau_pada ? $risiko->ditinjau_pada->format('d-m-Y') : '-' }}
</td>

                </tr>
            @empty
                <tr>
                    <td colspan="15" class="text-center text-gray-500 py-4">Tidak ada data risiko.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

    </div>
</x-filament-panels::page>

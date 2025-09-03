<x-filament::widget>
    <x-filament::card>

        {{-- Filter --}}
        <div class="mb-4 flex gap-3 flex-wrap">
            @if(auth()->user()->hasRole('superadmin'))
                {{-- Filter Company (superadmin: semua company) --}}
                <select wire:model="filterCompany" class="border rounded p-1">
                    <option value="">Semua Company</option>
                    @foreach($this->companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            @elseif(auth()->user()->hasRole('direksi'))
                {{-- Filter Company (direksi: hanya company miliknya) --}}
                <select wire:model="filterCompany" class="border rounded p-1">
                    <option value="">Semua Company Saya</option>
                    @foreach($this->companies as $company)
                        @if($company->id == auth()->user()->company_id)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endif
                    @endforeach
                </select>
            @endif

            {{-- Filter Division --}}
            <select wire:model="filterDivision" class="border rounded p-1">
                <option value="">Semua Divisi</option>
                @foreach($this->divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                @endforeach
            </select>

            {{-- Filter Year --}}
            <select wire:model="filterYear" class="border rounded p-1">
                <option value="">Semua Tahun</option>
                @foreach($this->years as $yr)
                    <option value="{{ $yr }}">{{ $yr }}</option>
                @endforeach
            </select>

            {{-- Tombol Terapkan (opsional, karena Livewire reactive) --}}
            <button wire:click="loadMatrix" class="px-3 py-1 bg-primary-600 text-white rounded">
                Terapkan
            </button>
        </div>

        <h2 class="text-xl font-bold mb-4 text-center">📊 Matriks Risiko</h2>

        <div class="overflow-x-auto">
            <table class="risk-matrix border-collapse text-center w-full text-sm md:text-base">
                <thead>
                    <tr>
                        <th class="border p-3 bg-gray-300 font-semibold">Probabilitas \ Dampak</th>
                        @for ($i = 1; $i <= 5; $i++)
                            <th class="border p-3 bg-gray-300 font-semibold">D{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_reverse($matrix, true) as $p => $row)
                        <tr>
                            <th class="border p-3 bg-gray-200 font-semibold">P{{ $p }}</th>
                            @foreach($row as $i => $cell)
                                @php
                                    $label = $cell['peringkat_risiko'] ?? '-';
                                    $label = ucwords(strtolower(trim($label)));
                                    $count = $cell['total'] ?? 0;
                                    $score = $cell['skor'] ?? ($p * $i);
                                    $bgColor = match($label) {
                                        'Sangat Tinggi' => '#dc2626',
                                        'Tinggi'        => '#f97316',
                                        'Sedang'        => '#facc15',
                                        'Rendah'        => '#4ade80',
                                        'Sangat Rendah' => '#bbf7d0',
                                        default         => '#f3f4f6',
                                    };
                                    $textColor = in_array($label, ['Sangat Tinggi','Tinggi']) ? '#ffffff' : '#000000';
                                @endphp
                                <td class="border p-6 font-bold" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                    <div class="text-lg">{{ $score }}</div>
                                    <div class="text-xs font-normal">({{ $count }} risiko)</div>
                                    <div class="text-[11px] italic">{{ $label }}</div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Keterangan warna --}}
        <div class="mt-5 text-sm">
            <p class="font-semibold mb-1">Keterangan:</p>
            <ul class="grid grid-cols-2 md:grid-cols-3 gap-2">
                <li><span class="inline-block w-5 h-5 bg-green-200 border"></span> Sangat Rendah (1–2)</li>
                <li><span class="inline-block w-5 h-5 bg-green-400 border"></span> Rendah (3–4)</li>
                <li><span class="inline-block w-5 h-5 bg-yellow-300 border"></span> Sedang (5–9)</li>
                <li><span class="inline-block w-5 h-5 bg-orange-500 border"></span> Tinggi (10–14)</li>
                <li><span class="inline-block w-5 h-5 bg-red-600 border"></span> Sangat Tinggi (15–25)</li>
            </ul>
        </div>

    </x-filament::card>
</x-filament::widget>

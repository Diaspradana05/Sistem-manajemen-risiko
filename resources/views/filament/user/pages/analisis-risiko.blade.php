<x-filament::page>
    {{-- Filter --}}
    <form wire:submit.prevent="applyFilter" class="mb-4 flex gap-3">

        {{-- Company (hanya untuk superadmin) --}}
        @if(auth()->user()->hasRole('superadmin'))
            <select wire:model="filterCompany" class="border rounded p-1">
                <option value="">Semua Company</option>
                @foreach(\App\Models\Company::pluck('name','id') as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        @endif

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


    {{-- Charts & Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($peringkatTipeRisiko as $tipe => $dataPeringkat)
            {{-- Pie Chart --}}
            <div class="bg-white p-4 shadow rounded text-center">
                <h2 class="text-lg font-bold mb-4">Distribusi Risiko ({{ ucfirst($tipe) }})</h2>
                <canvas wire:ignore id="pieChart_{{ $loop->index }}" width="300" height="300" class="mx-auto"></canvas>
            </div>

            {{-- Bar Chart --}}
            <div class="bg-white p-4 shadow rounded text-center">
                <h2 class="text-lg font-bold mb-4">Risiko per Lokasi ({{ ucfirst($tipe) }})</h2>
                <div class="w-full h-[250px]">
                    <canvas wire:ignore id="barChart_{{ $loop->index }}" class="w-full h-full"></canvas>
                </div>
            </div>
        @endforeach

        {{-- Statistik --}}
        <div class="bg-white p-4 shadow rounded col-span-2">
            <h2 class="text-lg font-bold mb-4">Statistik Cepat</h2>
            <p>📦 Total Risiko: {{ $totalRisiko }}</p>
            <p>🔧 Risiko Perlu Penanganan: {{ $perluPenanganan }}</p>
            <p>⚠️ Persentase Perlu Penanganan: {{ $persentasePenanganan }}%</p>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    let pieCharts = [];
    let barCharts = [];

    const colorMap = {
        'sangat rendah': '#3b82f6',
        'rendah':        '#22c55e',
        'sedang':        '#facc15',
        'tinggi':        '#f97316',
        'sangat tinggi': '#ef4444',
    };

    function destroyAll() {
        pieCharts.forEach(c => { try { c.destroy(); } catch(e) {} });
        barCharts.forEach(c => { try { c.destroy(); } catch(e) {} });
        pieCharts = [];
        barCharts = [];
    }

    function renderCharts(peringkatTipeRisiko, lokasiTipeRisiko) {
        destroyAll();

        Object.keys(peringkatTipeRisiko || {}).forEach((tipe, index) => {
            // ===== Pie =====
            const pieLabels = Object.keys(peringkatTipeRisiko[tipe] || {});
            const pieData   = Object.values(peringkatTipeRisiko[tipe] || {});
            const pieColors = pieLabels.map(l => colorMap[String(l).trim().toLowerCase()] ?? '#9ca3af');

            const pieEl = document.getElementById(`pieChart_${index}`);
            if (pieEl) {
                const pie = new Chart(pieEl, {
                    type: 'pie',
                    data: { labels: pieLabels, datasets: [{ data: pieData, backgroundColor: pieColors }] },
                    options: {
                        responsive: false,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: { callbacks: { label: ctx => `${ctx.label}: ${ctx.raw}` } },
                        },
                    },
                });
                pieCharts.push(pie);
            }

            // ===== Bar =====
            const barObj   = lokasiTipeRisiko?.[tipe] || {};
            const barLbls  = Object.keys(barObj);
            const barData  = barLbls.map(k => Number(barObj[k] ?? 0));

            const barEl = document.getElementById(`barChart_${index}`);
            if (barEl) {
                const bar = new Chart(barEl, {
                    type: 'bar',
                    data: {
                        labels: barLbls,
                        datasets: [{
                            label: 'Jumlah Risiko',
                            data: barData,
                            backgroundColor: '#3b82f6',
                            borderRadius: 4,
                            barThickness: 100,
                            maxBarThickness: 140,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: 10 },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                            x: {
    ticks: {
        autoSkip: false,
        maxRotation: barLbls.length > 2 ? 10 : 0, // miring kalau lebih dari 2
        minRotation: barLbls.length > 2 ? 10 : 0, // tetap lurus kalau <= 2
        callback: function(value, index, ticks) {
            let label = this.getLabelForValue(value);
            return label; // tampilkan apa adanya, tanpa dipotong

        }
    }
},

                        },
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } },
                    },
                });
                barCharts.push(bar);
            }
        });
    }

    // Render pertama kali
      // Render pertama kali
  document.addEventListener('DOMContentLoaded', () => {
      renderCharts(@json($peringkatTipeRisiko), @json($lokasiTipeRisiko));
  });

  // Listener Livewire v3: data ada di e.detail.{namaArgumen}
  window.addEventListener('risk-charts-update', (e) => {
      const pie = e.detail?.peringkatTipeRisiko ?? {};
      const bar = e.detail?.lokasiTipeRisiko ?? {};
      renderCharts(pie, bar);
  });

</script>
</x-filament::page>

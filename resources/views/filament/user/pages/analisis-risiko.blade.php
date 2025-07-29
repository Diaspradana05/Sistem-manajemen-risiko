<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Pie Chart --}}
        <div class="bg-white p-4 shadow rounded">
            <h2 class="text-lg font-bold mb-4">Distribusi Risiko (Pie Chart)</h2>
            <canvas id="pieChart"></canvas>
        </div>

        {{-- Bar Chart --}}
        <div class="bg-white p-4 shadow rounded">
            <h2 class="text-lg font-bold mb-4">Risiko per Unit/Lokasi</h2>
            <canvas id="barChart"></canvas>
        </div>

        {{-- Statistik --}}
        <div class="bg-white p-4 shadow rounded col-span-2">
            <h2 class="text-lg font-bold mb-4">Statistik Cepat</h2>
            <p>📦 Total Risiko: {{ $totalRisiko }}</p>
            <p>🔧 Risiko Perlu Penanganan: {{ $perluPenanganan }}</p>
            <p>⚠️ Persentase Perlu Penanganan: {{ $totalRisiko > 0 ? round(($perluPenanganan / $totalRisiko) * 100, 2) : 0 }}%</p>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data untuk Pie Chart
        const pieLabels = {!! json_encode(array_keys($peringkatRisiko)) !!};
        const pieData = {!! json_encode(array_values($peringkatRisiko)) !!};

        // Warna khusus berdasarkan label peringkat risiko
        const pieColors = pieLabels.map(label => {
            const lower = label.toLowerCase();
            if (lower.includes('rendah') || lower.includes('low')) return '#22c55e';      // Hijau
            if (lower.includes('sedang') || lower.includes('medium')) return '#facc15';   // Kuning
            if (lower.includes('tinggi') || lower.includes('high')) return '#ef4444';     // Merah
            if (lower.includes('ekstrem') || lower.includes('extreme')) return '#1f2937'; // Abu gelap
            return '#3b82f6'; // Fallback biru
        });

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: pieColors,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                return `${label}: ${value}`;
                            }
                        }
                    }
                }
            }
        });

        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($perUnit)) !!},
                datasets: [{
                    label: 'Jumlah Risiko',
                    data: {!! json_encode(array_values($perUnit)) !!},
                    backgroundColor: '#3b82f6',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                }
            }
        });
    </script>
</x-filament::page>

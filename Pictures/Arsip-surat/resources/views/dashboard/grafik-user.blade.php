@extends('layouts.app')

@section('title', 'Grafik User')

@section('content')

<div class="bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-5">
        Grafik Surat {{ $user->name }}
    </h2>

    <div style="height: 300px;">
        <canvas id="chartUser"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('chartUser'), {
    type: 'bar',

    data: {
        labels: {!! json_encode($grafikSurat->pluck('bulan')) !!},

        datasets: [
            {
                label: 'Surat Masuk',
                data: {!! json_encode($grafikSurat->pluck('masuk')) !!},
                backgroundColor: '#7A1E1E'
            },

            {
                label: 'Surat Keluar',
                data: {!! json_encode($grafikSurat->pluck('keluar')) !!},
                backgroundColor: '#525252'
            }
        ]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false,

        scales: {
            y: {
                beginAtZero: true,

                ticks: {
                    stepSize: 1,
                    precision: 0
                }
            }
        },

        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});
</script>

@endsection
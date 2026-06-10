@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ================= CARD STATISTIK ================= --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-white p-5 rounded shadow">
        <p class="text-gray-500 text-sm">Total Surat Masuk Bulan ini</p>
        <h2 class="text-3xl font-bold">{{ $totalSuratMasuk }}</h2>
    </div>

    <div class="bg-white p-5 rounded shadow">
        <p class="text-gray-500 text-sm">Total Surat Keluar Bulan ini </p>
        <h2 class="text-3xl font-bold">{{ $totalSuratKeluar }}</h2>
    </div>

    @if(auth()->user()->role === 'admin')
    <div class="bg-white p-5 rounded shadow">
        <p class="text-gray-500 text-sm">Total User</p>
        <h2 class="text-3xl font-bold">{{ $totalUser }}</h2>
    </div>
    @endif

    <div class="bg-white p-5 rounded shadow">
        <p class="text-gray-500 text-sm">total Surat Bulan Ini</p>
        <h2 class="text-3xl font-bold">{{ $totalSuratBulanIni }}</h2>
    </div>

</div>

{{-- ================= GRAFIK & AKTIVITAS ================= --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ===== GRAFIK (ADMIN ONLY) ===== --}}
  <div class="bg-white p-6 rounded shadow
    {{ auth()->user()->role === 'admin' ? 'lg:col-span-2' : 'lg:col-span-3' }}">

    {{-- HEADER + FILTER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">

        <h3 class="font-semibold">
            Grafik Surat - Grafik Surat Tahun {{ request('tahun', date('Y')) }}</h3>

        <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
            <select name="tahun"
                class="border rounded px-2 py-1 text-sm">

            @for($i = date('Y'); $i >= 2020; $i--)
                <option value="{{ $i }}"
                    {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
            @endfor

        </select>

            <button class="px-3 py-1 bg-[#7A1E1E] text-white rounded text-sm">
                Filter
            </button>
        </form>

    </div>

    {{-- CHART --}}
    <div class="relative w-full h-[300px]">
        <canvas id="chartSurat"></canvas>
    </div>

</div>


    {{-- ===== AKTIVITAS ===== --}}
    <div class="bg-white p-6 rounded shadow
        {{ auth()->user()->role !== 'admin' ? 'lg:col-span-3' : '' }}">

        <h3 class="font-semibold mb-4">
            {{ auth()->user()->role === 'admin' ? 'Aktivitas User' : 'Aktivitas Saya' }}
        </h3>

        <div class="max-h-[320px] overflow-y-auto pr-2">
            <ul class="space-y-3 text-sm">
                @forelse ($aktivitas as $log)
                    <li class="border-b pb-2">
                        <p class="font-semibold">
                            {{ $log->user->name }}
                        </p>
                        <p class="text-gray-700">
                            {{ $log->activity }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $log->created_at->diffForHumans() }}
                        </p>
                    </li>
                @empty
                    <li class="text-gray-400">Belum ada aktivitas</li>
                @endforelse
            </ul>
        </div>
    </div>

</div>

{{-- ================= CHART JS (ADMIN ONLY) ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('chartSurat'), {
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
                backgroundColor: '#1F2937'
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
                    precision: 0,
                    stepSize: 1
                },
                title: {
                    display: true,
                    text: 'Jumlah Surat'
                }
            },

            x: {
                title: {
                    display: true,
                    text: 'Bulan'
                }
            }
        }
    }
});
</script>
 @if(auth()->user()->role === 'admin')

<div class="bg-white p-6 rounded shadow mt-6">

    <h3 class="font-semibold mb-4">
        Monitoring User
    </h3>

    <table class="w-full">

        <thead>
            <tr>
                <th class="text-left p-2">Nama</th>
                <th class="text-center p-2">Surat Masuk</th>
                <th class="text-center p-2">Surat Keluar</th>
            </tr>
        </thead>

        <tbody>
        @foreach($statistikUser as $u)

            <tr class="border-t">
                <td class="p-2">{{ $u['nama'] }}</td>
                <td class="p-2 text-center">{{ $u['surat_masuk'] }}</td>
                <td class="p-2 text-center">{{ $u['surat_keluar'] }}</td>
            </tr>

        @endforeach
        </tbody>

    </table>

</div>

@endif

@endsection

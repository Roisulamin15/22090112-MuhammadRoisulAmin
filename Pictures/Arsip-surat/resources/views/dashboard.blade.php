@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ================= CARD STATISTIK ================= --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-white p-5 rounded-xl shadow">
        <p class="text-gray-500 text-sm">Total Surat Masuk Bulan Ini</p>
        <h2 class="text-3xl font-bold">
            {{ $totalSuratMasuk }}
        </h2>
    </div>

    <div class="bg-white p-5 rounded-xl shadow">
        <p class="text-gray-500 text-sm">Total Surat Keluar Bulan Ini</p>
        <h2 class="text-3xl font-bold">
            {{ $totalSuratKeluar }}
        </h2>
    </div>

    @if(auth()->user()->role === 'admin')
    <div class="bg-white p-5 rounded-xl shadow">
        <p class="text-gray-500 text-sm">Total User</p>
        <h2 class="text-3xl font-bold">
            {{ $totalUser }}
        </h2>
    </div>
    @endif

    <div class="bg-white p-5 rounded-xl shadow">
        <p class="text-gray-500 text-sm">Total Surat Bulan Ini</p>
        <h2 class="text-3xl font-bold">
            {{ $totalSuratBulanIni }}
        </h2>
    </div>

</div>

{{-- ================= GRAFIK & AKTIVITAS ================= --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ================= GRAFIK ================= --}}
    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">

            <h3 class="font-semibold text-lg">
                Grafik Surat Tahun {{ request('tahun', date('Y')) }}
            </h3>

            <form method="GET"
                action="{{ route('dashboard') }}"
                class="flex gap-2">

                <select name="tahun"
                    class="border rounded-lg px-3 py-2 text-sm focus:outline-none">

                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}"
                            {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor

                </select>

                <button
                    class="px-4 py-2 bg-[#7A1E1E] hover:bg-[#651616] text-white rounded-lg text-sm transition">
                    Filter
                </button>

            </form>

        </div>

        {{-- CHART --}}
        <div class="relative w-full h-[320px]">
            <canvas id="chartSurat"></canvas>
        </div>

    </div>

  {{-- ================= AKTIVITAS ================= --}}
<div class="bg-white p-5 rounded-xl shadow flex flex-col">

    <h3 class="font-semibold text-lg mb-4">
        {{ auth()->user()->role === 'admin'
            ? 'Aktivitas User'
            : 'Aktivitas Saya' }}
    </h3>

    <div class="space-y-2">

        @forelse ($aktivitas as $log)

            <div class="border border-gray-200 rounded-lg p-2.5 hover:bg-gray-50 transition">

                <div class="flex justify-between items-start gap-2">

                    <div class="flex-1 min-w-0">

                        <p class="font-medium text-sm text-gray-800 truncate">
                            {{ $log->user->name }}
                        </p>

                        <p class="text-xs text-gray-600 mt-1 break-words">
                            {{ $log->activity }}
                        </p>

                    </div>

                    <span class="text-[11px] text-gray-400 whitespace-nowrap">
                        {{ $log->created_at->diffForHumans() }}
                    </span>

                </div>

            </div>

        @empty

            <div class="text-center py-6 text-gray-400 text-sm">
                Belum ada aktivitas
            </div>

        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if($aktivitas->hasPages())

        <div class="mt-4 border-t pt-3">

            <div class="flex justify-between items-center text-sm">

                @if ($aktivitas->onFirstPage())

                    <span class="px-3 py-1.5 rounded bg-gray-100 text-gray-400">
                        ← Sebelumnya
                    </span>

                @else

                    <a href="{{ $aktivitas->previousPageUrl() }}"
                       class="px-3 py-1.5 rounded bg-gray-100 hover:bg-gray-200 transition">
                        ← Sebelumnya
                    </a>

                @endif


                @if ($aktivitas->hasMorePages())

                    <a href="{{ $aktivitas->nextPageUrl() }}"
                       class="px-3 py-1.5 rounded bg-[#7A1E1E] text-white hover:bg-[#651616] transition">
                        Selanjutnya →
                    </a>

                @else

                    <span class="px-3 py-1.5 rounded bg-gray-100 text-gray-400">
                        Selanjutnya →
                    </span>

                @endif

            </div>

        </div>

    @endif

</div>

        {{-- PAGINATION --}}
        @if($aktivitas instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-5">
                {{ $aktivitas->links() }}
            </div>
        @endif

    </div>

</div>

</div>

{{-- ================= CHART JS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('chartSurat');

    if (!ctx) return;

    new Chart(ctx, {

        type: 'bar',

        data: {

            labels: {!! json_encode($grafikSurat->pluck('bulan')) !!},

            datasets: [
                {
                    label: 'Surat Masuk',
                    data: {!! json_encode($grafikSurat->pluck('masuk')) !!},
                    backgroundColor: '#7A1E1E',
                    borderRadius: 8
                },

                {
                    label: 'Surat Keluar',
                    data: {!! json_encode($grafikSurat->pluck('keluar')) !!},
                    backgroundColor: '#525252',
                    borderRadius: 8
                }
            ]
        },

        options: {

            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'top'
                }
            },

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

});
</script>

{{-- ================= MONITORING USER ================= --}}
@if(auth()->user()->role === 'admin')

<div class="bg-white p-6 rounded-xl shadow mt-6">

    <h3 class="font-semibold text-lg mb-4">
        Monitoring User
    </h3>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left p-3">Nama</th>
                    <th class="text-center p-3">Surat Masuk</th>
                    <th class="text-center p-3">Surat Keluar</th>
                    <th class="text-center p-3">Aksi</th>

                </tr>
            </thead>

            <tbody>

            @foreach($statistikUser as $u)

            <tr class="border-b hover:bg-gray-50">

                <td class="p-3">{{ $u['nama'] }}</td>

                <td class="p-3 text-center">
                    {{ $u['surat_masuk'] }}
                </td>

                <td class="p-3 text-center">
                    {{ $u['surat_keluar'] }}
                </td>

                <td class="p-3 text-center">

                    <a href="{{ route('dashboard.user-grafik', $u['id']) }}"
                    class="px-3 py-1 bg-[#7A1E1E] text-white hover:bg-[#651616]">

                        Lihat Grafik

                    </a>

                </td>

            </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>

@endif

@endsection
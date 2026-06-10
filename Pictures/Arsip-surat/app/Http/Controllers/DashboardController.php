<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tahun = request('tahun', now()->year);

        // ======================
        // TOTAL DATA
        // ======================

        $totalSuratMasuk = SuratMasuk::where('penerima_id', $user->id)
            ->whereMonth('tanggal_surat', now()->month)
            ->whereYear('tanggal_surat', now()->year)
            ->count();

        $totalSuratKeluar = SuratKeluar::where('pengirim_id', $user->id)
            ->whereMonth('tanggal_surat', now()->month)
            ->whereYear('tanggal_surat', now()->year)
            ->count();

        $totalUser = User::count();

        $totalSuratBulanIni = $totalSuratMasuk + $totalSuratKeluar;

        // ======================
        // GRAFIK SURAT
        // ======================

        if ($user->role === 'admin') {

            $grafikMasuk = SuratMasuk::select(
                    DB::raw('EXTRACT(MONTH FROM tanggal_surat) as bulan'),
                    DB::raw('COUNT(*) as total')
                )
                ->whereYear('tanggal_surat', $tahun)
                ->groupBy(DB::raw('EXTRACT(MONTH FROM tanggal_surat)'))
                ->pluck('total', 'bulan');

            $grafikKeluar = SuratKeluar::select(
                    DB::raw('EXTRACT(MONTH FROM tanggal_surat) as bulan'),
                    DB::raw('COUNT(*) as total')
                )
                ->whereYear('tanggal_surat', $tahun)
                ->groupBy(DB::raw('EXTRACT(MONTH FROM tanggal_surat)'))
                ->pluck('total', 'bulan');

        } else {

            $grafikMasuk = SuratMasuk::select(
                    DB::raw('EXTRACT(MONTH FROM tanggal_surat) as bulan'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('penerima_id', $user->id)
                ->whereYear('tanggal_surat', $tahun)
                ->groupBy(DB::raw('EXTRACT(MONTH FROM tanggal_surat)'))
                ->pluck('total', 'bulan');

            $grafikKeluar = SuratKeluar::select(
                    DB::raw('EXTRACT(MONTH FROM tanggal_surat) as bulan'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('pengirim_id', $user->id)
                ->whereYear('tanggal_surat', $tahun)
                ->groupBy(DB::raw('EXTRACT(MONTH FROM tanggal_surat)'))
                ->pluck('total', 'bulan');
        }

        $namaBulan = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $grafikSurat = collect(range(1, 12))->map(function ($bulan) use ($grafikMasuk, $grafikKeluar, $namaBulan) {

            return [
                'bulan'  => $namaBulan[$bulan - 1],
                'masuk'  => $grafikMasuk[$bulan] ?? 0,
                'keluar' => $grafikKeluar[$bulan] ?? 0,
            ];
        });

        // ======================
        // AKTIVITAS
        // ======================

        $aktivitas = $user->role === 'admin'
            ? ActivityLog::with('user')->latest()->get()
            : ActivityLog::with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->get();

        // ======================
        // MONITORING USER
        // ======================

        $statistikUser = [];

        if ($user->role === 'admin') {

            $statistikUser = User::where('role', 'user')
                ->get()
                ->map(function ($u) {

                    return [
                        'nama' => $u->name,

                        'surat_masuk' => SuratMasuk::where('penerima_id', $u->id)->count(),

                        'surat_keluar' => SuratKeluar::where('pengirim_id', $u->id)->count(),
                    ];
                });
        }

        // ======================
        // RETURN VIEW
        // ======================

        return view('dashboard', compact(
            'totalSuratMasuk',
            'totalSuratKeluar',
            'totalUser',
            'totalSuratBulanIni',
            'grafikSurat',
            'aktivitas',
            'statistikUser'
        ));
    }
}
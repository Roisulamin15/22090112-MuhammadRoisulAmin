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
        // TOTAL DATA BULAN INI
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
        // GRAFIK USER LOGIN
        // ======================

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
            ? ActivityLog::with('user')
                ->latest()
                ->simplePaginate(5)
                ->withQueryString()

            : ActivityLog::with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->simplePaginate(5)
                ->withQueryString();

        // ======================
        // MONITORING USER
        // ======================

        $statistikUser = [];

        if ($user->role === 'admin') {

            $statistikUser = User::where('role', 'user')
                ->orderBy('name')
                ->get()
                ->map(function ($u) {

                    return [
                        'id' => $u->id,
                        'nama' => $u->name,

                        'surat_masuk' => SuratMasuk::where(
                            'penerima_id',
                            $u->id
                        )->count(),

                        'surat_keluar' => SuratKeluar::where(
                            'pengirim_id',
                            $u->id
                        )->count(),
                    ];
                });
        }

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

    // ======================
    // GRAFIK USER (ADMIN)
    // ======================

    public function grafikUser($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $user = User::findOrFail($id);
        $tahun = request('tahun', now()->year);

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

        return view('dashboard.grafik-user', compact(
            'user',
            'grafikSurat'
        ));
    }
}
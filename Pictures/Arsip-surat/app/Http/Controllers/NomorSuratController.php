<?php

namespace App\Http\Controllers;

use App\Models\NomorSurat;
use Illuminate\Http\Request;

class NomorSuratController extends Controller
{
    private $jenisSurat = [
        'surat_undangan' => 'Surat Undangan',
        'surat_pemberitahuan' => 'Surat Pemberitahuan',
        'surat_permohonan' => 'Surat Permohonan',
        'surat_edaran' => 'Surat Edaran',
        'surat_keputusan' => 'Surat Keputusan',
        'surat_keterangan' => 'Surat Keterangan',
        'surat_tugas' => 'Surat Tugas',
        'surat_perjalanan_dinas' => 'Surat Perjalanan Dinas',
        'surat_peraturan' => 'Surat Peraturan',
        'surat_pengantar' => 'Surat Pengantar',
        'surat_pernyataan' => 'Surat Pernyataan',
        'surat_kuasa' => 'Surat Kuasa',
        'surat_peringatan' => 'Surat Peringatan',
        'surat_memo' => 'Surat Memo',
        'surat_instruksi' => 'Surat Instruksi/Perintah',
        'surat_perjanjian' => 'Surat Perjanjian',
        'mou' => 'MoU',
        'surat_rekomendasi' => 'Surat Rekomendasi',
        'surat_balasan' => 'Surat Balasan',
        'surat_pengumuman' => 'Surat Pengumuman',
        'nota_dinas' => 'Nota Dinas',
        'berita_acara' => 'Berita Acara',
        'piagam_sertifikat' => 'Piagam/Sertifikat',
        'surat_persetujuan' => 'Surat Persetujuan',
        'surat_kontrak' => 'Surat Kontrak',
    ];

    public function index()
    {
        $nomorSurats = auth()->user()
            ->nomorSurats
            ->keyBy('jenis_surat');

        return view('nomor_surat.index', [
            'jenisSurat' => $this->jenisSurat,
            'nomorSurats' => $nomorSurats,
        ]);
    }

    public function store(Request $request)
    {
        foreach ($this->jenisSurat as $key => $label) {

            NomorSurat::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'jenis_surat' => $key,
                ],
                [
                    'nomor' => $request->input($key),
                ]
            );
        }

        return back()->with('success', 'Nomor surat berhasil disimpan');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    protected $table = 'surat_keluar';

    protected $fillable = [
        'nomor_surat',
        'tujuan_surat',
        'pengirim_id',
        'tujuan_id',
        'tanggal_surat',
        'jam',
        'perihal',
        'penanggung_jawab_id',
        'file',
        'is_read'
    ];
    public function penanggungJawab()
{
    return $this->belongsTo(
        \App\Models\PenanggungJawab::class,
        'penanggung_jawab_id'
    );
}
}
    

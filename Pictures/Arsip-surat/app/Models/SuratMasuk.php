<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $table = 'surat_masuk';

    protected $fillable = [
    'nomor_surat',
    'asal_surat',
    'pengirim_id',
    'penerima_id',
    'tanggal_surat',
    'jam',
    'perihal',
    'penanggung_jawab_id',
    'file'
];
    public function penanggungJawab()
{
    return $this->belongsTo(
        \App\Models\PenanggungJawab::class,
        'penanggung_jawab_id'
    );
}

public function pengirim()
{
    return $this->belongsTo(User::class, 'pengirim_id');
}

public function penerima()
{
    return $this->belongsTo(User::class, 'penerima_id');
}

}

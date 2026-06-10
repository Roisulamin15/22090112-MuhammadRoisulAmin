<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NomorSurat extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_surat',
        'nomor',
    ];
}
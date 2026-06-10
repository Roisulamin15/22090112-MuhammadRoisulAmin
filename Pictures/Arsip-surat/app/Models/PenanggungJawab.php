<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenanggungJawab extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'jabatan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
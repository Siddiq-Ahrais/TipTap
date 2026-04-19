<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'jam_masuk_kantor',
        'jam_mulai_pulang',
    ];

    protected $casts = [
        'jam_masuk_kantor' => 'string',
        'jam_mulai_pulang' => 'string',
    ];
}

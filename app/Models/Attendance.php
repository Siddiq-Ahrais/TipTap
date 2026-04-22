<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'waktu_masuk',
        'waktu_keluar',
        'status',
        'early_checkout_status',
        'early_checkout_requested_at',
        'early_checkout_reviewed_at',
        'early_checkout_reviewed_by',
        'early_checkout_note',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_masuk' => 'datetime:H:i:s',
        'waktu_keluar' => 'datetime:H:i:s',
        'early_checkout_requested_at' => 'datetime',
        'early_checkout_reviewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the attendance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

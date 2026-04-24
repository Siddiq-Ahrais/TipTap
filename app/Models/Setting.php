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
        'company_email_domain',
        'work_days',
    ];

    protected $casts = [
        'jam_masuk_kantor' => 'string',
        'jam_mulai_pulang' => 'string',
        'company_email_domain' => 'string',
        'work_days' => 'array',
    ];

    /**
     * Get work_days with fallback to Mon-Fri.
     */
    public function getActiveWorkDaysAttribute(): array
    {
        return $this->work_days ?? [1, 2, 3, 4, 5];
    }
}

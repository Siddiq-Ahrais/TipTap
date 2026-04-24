<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'divisi',
        'status_pekerjaan',
        'tgl_habis_kontrak',
        'is_approved',
        'leave_quota',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'tgl_habis_kontrak' => 'date',
        ];
    }

    /**
     * Get all attendances for the user.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get all leaves for the user.
     */
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Count total leave days used this year (approved + pending).
     */
    public function usedLeaveDays(): int
    {
        $leaves = $this->leaves()
            ->whereYear('tanggal_mulai', now()->year)
            ->whereIn('status_approval', ['Approved', 'Pending'])
            ->get(['tanggal_mulai', 'tanggal_selesai']);

        $total = 0;
        foreach ($leaves as $leave) {
            $start = \Illuminate\Support\Carbon::parse($leave->tanggal_mulai);
            $end = \Illuminate\Support\Carbon::parse($leave->tanggal_selesai);
            $total += $start->diffInDays($end) + 1; // inclusive
        }

        return $total;
    }

    /**
     * Get remaining leave quota for this year.
     */
    public function remainingLeaveDays(): int
    {
        return max(0, ($this->leave_quota ?? 8) - $this->usedLeaveDays());
    }
}

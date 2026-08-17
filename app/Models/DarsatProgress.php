<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DarsatProgress extends Model
{
    use HasFactory;

    protected $table = 'darsat_progress';

    protected $fillable = [
        'user_id', 'darsat_id', 'status', 'last_position_seconds',
        'times_played', 'last_played_at', 'completed_at',
    ];

    protected $casts = [
        'last_played_at' => 'datetime',
        'completed_at'   => 'datetime',
    ];

    public function darsat()
    {
        return $this->belongsTo(DarsatTable::class, 'darsat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

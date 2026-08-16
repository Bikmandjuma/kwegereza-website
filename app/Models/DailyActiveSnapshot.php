<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyActiveSnapshot extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'peak_online_count'];

    protected $casts = [
        'date' => 'date',
    ];
}

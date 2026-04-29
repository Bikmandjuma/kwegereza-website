<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestVisit extends Model
{
    protected $fillable = [
        'guest_id',
        'ip',
        'user_agent',
        'last_visit_at',
    ];
}
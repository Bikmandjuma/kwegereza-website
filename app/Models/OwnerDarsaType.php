<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerDarsaType extends Model
{
    use HasFactory;
    protected $fillable = [
        'sheikh_fk_id',
        'dataType_fk_id'
    ];
}

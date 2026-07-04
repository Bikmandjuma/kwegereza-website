<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DarsatTable extends Model{
    use HasFactory;

    protected $table = 'darsat_tables';

    protected $fillable = [
        'title',
        'teachers',
        'type',
        'audio',
    ];

    /**
     * Teacher relationship
     */
    public function teacher()
    {
        return $this->belongsTo(Owner::class, 'teachers');
    }
}
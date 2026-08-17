<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'action', 'entity_type', 'entity_id',
        'entity_label', 'before', 'after', 'ip',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function entityTypeLabel(): string
    {
        return class_basename($this->entity_type);
    }
}

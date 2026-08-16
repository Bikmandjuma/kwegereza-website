<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClassParticipant extends Model
{
    protected $fillable = [
        'live_class_id', 'participant_type', 'participant_id',
        'role', 'hand_raised', 'is_muted', 'joined_at', 'left_at',
    ];

    protected $casts = [
        'hand_raised' => 'boolean',
        'is_muted' => 'boolean',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function liveClass()
    {
        return $this->belongsTo(LiveClass::class);
    }

    public function name(): string
    {
        $model = $this->participant_type === 'student'
            ? User::find($this->participant_id)
            : Owner::find($this->participant_id);

        return $model ? trim($model->firstname.' '.$model->lastname) : 'Unknown';
    }
}

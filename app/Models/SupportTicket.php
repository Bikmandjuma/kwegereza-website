<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'user_id', 'subject', 'category', 'priority', 'status', 'assigned_to',
    ];

    protected static function booted(): void
    {
        static::created(function (SupportTicket $ticket) {
            if (!$ticket->ticket_number) {
                $ticket->updateQuietly(['ticket_number' => 'TCK-' . str_pad((string) $ticket->id, 6, '0', STR_PAD_LEFT)]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(Owner::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class)->oldest();
    }

    public function scopeOpenOrInProgress($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }
}

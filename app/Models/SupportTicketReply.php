<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketReply extends Model
{
    use HasFactory;

    protected $fillable = ['support_ticket_id', 'sender_type', 'sender_id', 'message'];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function senderName(): string
    {
        if ($this->sender_type === 'owner') {
            $owner = Owner::find($this->sender_id);
            return $owner ? trim($owner->firstname . ' ' . $owner->lastname) . ' (Support Team)' : 'Support Team';
        }

        $user = User::find($this->sender_id);
        return $user ? trim($user->firstname . ' ' . $user->lastname) : 'Umunyeshuri';
    }
}

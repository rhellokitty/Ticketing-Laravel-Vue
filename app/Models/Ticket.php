<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'title',
        'status',
        'description',
        'priority',
        'completed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ticketsReplies()
    {
        return $this->hasMany(TicketReply::class);
    }
}

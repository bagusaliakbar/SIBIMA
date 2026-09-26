<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaBroadcastLog extends Model
{
    protected $fillable = [
        'wa_broadcast_id',
        'recipient_id',
        'recipient_name',
        'recipient_identifier',
        'recipient_phone',
        'message_content',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function broadcast()
    {
        return $this->belongsTo(WaBroadcast::class, 'wa_broadcast_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}

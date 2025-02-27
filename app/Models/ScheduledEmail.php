<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_email',
        'subject',
        'body',
        'status',
        'send_at',
        'retry_count',
        'error_message',
    ];

    protected $casts = [
        'send_at' => 'datetime',
    ];
}

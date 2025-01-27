<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'send_at',
        'status',
        'platforms',
    ];

    protected $casts = [
        'send_at' => 'datetime',
        'platforms' => 'array',
    ];

    /**
     * Relationship : a message belongs to one user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

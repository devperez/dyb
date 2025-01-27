<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    /**
     * Relationship : a configuration belongs to one user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'avatar',
    ];

    public function chats()
    {
        return $this->belongsToMany(Chat::class);
    }
}

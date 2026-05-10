<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasUuids; // Habilita UUIDs automáticamente
    protected $fillable = ['name'];

    public function users() {
        return $this->belongsToMany(User::class);
    }
    public function messages() {
        return $this->hasMany(Message::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatChannel extends Model
{
    use HasFactory;

    public function messages() {
        return $this->hasMany(ChatMessage::class);
    }

    public function members() {
        return $this->hasMany(MemberChannel::class);
    }
}

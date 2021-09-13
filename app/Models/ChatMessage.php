<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    public function channel() {
       return $this->hasOne(ChatChannel::class, 'id', 'chat_channel_id');
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

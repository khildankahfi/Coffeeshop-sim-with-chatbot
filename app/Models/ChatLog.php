<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    protected $table    = 'ai_chat_logs';
    protected $fillable = ['session_id', 'role', 'content', 'tool_calls'];
    protected $casts    = ['tool_calls' => 'array'];
}
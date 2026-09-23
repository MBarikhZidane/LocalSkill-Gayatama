<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['conservation_id', 'sender_id', 'message', 'workflow_kind', 'workflow_from', 'workflow_to', 'workflow_token'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function conservation(): BelongsTo
    {
        return $this->belongsTo(Conservation::class, 'conservation_id');
    }
}

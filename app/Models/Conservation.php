<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conservation extends Model
{
    protected $fillable = ['order_id'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conservation_participants')->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}

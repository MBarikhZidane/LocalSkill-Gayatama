<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'name',
        'code',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
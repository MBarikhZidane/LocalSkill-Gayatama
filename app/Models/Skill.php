<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills')
                    ->withPivot(['proficiency_level', 'years_experience', 'is_verified'])
                    ->withTimestamps();
    }

    public function portofolios()
    {
        return $this->hasMany(Portofolio::class);
    }
}
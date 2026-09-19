<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'university_id',
        'study_program_id',
        'name',
        'email',
        'google_id',
        'role',
        'phone',
        'bio',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }
    
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function studyProgram()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills')
                    ->using(UserSkill::class)
                    ->withPivot(['proficiency_level', 'years_experience', 'is_verified'])
                    ->withTimestamps();
    }

    public function portofolios()
    {
        return $this->hasMany(Portofolio::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function customerOrders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function providerOrders()
    {
        return $this->hasMany(Order::class, 'provider_id');
    }

    public function location()
    {
        return $this->hasOne(Location::class);
    }

    public function communitiesCreated()
    {
        return $this->hasMany(Community::class, 'created_by');
    }

    public function communitiesJoined()
    {
        return $this->belongsToMany(Community::class, 'community_members')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    public function conservations()
    {
        return $this->belongsToMany(Conservation::class, 'conservation_participants')
                    ->withTimestamps();
    }

    
}

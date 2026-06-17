<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name', 'email', 'password',
    'nik', 'phone',
    'birth_date', 'birth_place', 'address',
    'village', 'district', 'city', 'province',
    'education_level', 'school_name', 'nisn',
    'university_name', 'major', 'current_semester',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'nik' => 'encrypted',
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'is_blacklisted' => 'boolean',
        ];
    }

    public function scholarshipVerifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ScholarshipVerifier::class);
    }
}

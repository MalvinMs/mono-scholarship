<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::observe(\App\Observers\ApplicationObserver::class);
    }
    protected $fillable = [
        'scholarship_id', 'user_id', 'registration_number',
        'snapshot_profile', 'status', 'is_renewal',
        'previous_application_id',
        'submitted_at', 'verified_at', 'selected_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_profile' => 'array',
            'is_renewal' => 'boolean',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'selected_at' => 'datetime',
        ];
    }

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function previousApplication(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'previous_application_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ApplicationAnswer::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(ApplicationScore::class);
    }

    public function verificationLogs(): HasMany
    {
        return $this->hasMany(VerificationLog::class);
    }
}

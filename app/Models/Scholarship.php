<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholarship extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'predecessor_scholarship_id',
        'description', 'academic_year', 'fund_amount',
        'quota_primary', 'quota_reserve', 'quota_renewal_locked',
        'date_start', 'date_end', 'status',
        'is_verification_enabled',
        'notification_channels', 'notification_templates',
        'otp_channel', 'min_gpa_renewal',
        'scoring_display_mode', 'tiebreaker_config',
        'created_by', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'date_start' => 'date',
            'date_end' => 'date',
            'notification_channels' => 'array',
            'notification_templates' => 'array',
            'tiebreaker_config' => 'array',
            'is_verification_enabled' => 'boolean',
            'published_at' => 'datetime',
            'min_gpa_renewal' => 'decimal:2',
        ];
    }

    public function predecessor(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class, 'predecessor_scholarship_id');
    }

    public function successor(): HasMany
    {
        return $this->hasMany(Scholarship::class, 'predecessor_scholarship_id');
    }

    public function qualificationGroups(): HasMany
    {
        return $this->hasMany(QualificationGroup::class)->orderBy('sort_order');
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(Qualification::class)->orderBy('sort_order');
    }

    public function verifiers(): HasMany
    {
        return $this->hasMany(ScholarshipVerifier::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

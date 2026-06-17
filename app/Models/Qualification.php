<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Qualification extends Model
{
    use HasFactory;
    protected $fillable = [
        'scholarship_id', 'qualification_group_id',
        'name', 'description', 'type',
        'is_required', 'is_file_upload_required',
        'file_upload_label', 'file_upload_description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_file_upload_required' => 'boolean',
        ];
    }

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(QualificationGroup::class, 'qualification_group_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QualificationOption::class)->orderBy('sort_order');
    }

    public function ranges(): HasMany
    {
        return $this->hasMany(QualificationRange::class)->orderBy('sort_order');
    }
}

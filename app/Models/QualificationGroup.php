<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QualificationGroup extends Model
{
    protected $fillable = ['scholarship_id', 'name', 'description', 'sort_order'];

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(Qualification::class)->orderBy('sort_order');
    }
}

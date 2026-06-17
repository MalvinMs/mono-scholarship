<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualificationRange extends Model
{
    use HasFactory;
    protected $fillable = ['qualification_id', 'range_min', 'range_max', 'value', 'label', 'sort_order'];

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class);
    }
}

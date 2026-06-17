<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualificationOption extends Model
{
    use HasFactory;
    protected $fillable = ['qualification_id', 'label', 'value', 'description', 'sort_order'];

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class);
    }
}

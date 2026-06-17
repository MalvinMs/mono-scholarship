<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAnswer extends Model
{
    use HasFactory;
    protected $fillable = [
        'application_id', 'qualification_id',
        'selected_option_id', 'selected_option_ids',
        'numeric_value', 'text_value',
        'computed_score',
        'is_corrected_by_verifier',
        'original_selected_option_id', 'original_numeric_value',
        'corrected_at', 'corrected_by',
    ];

    protected function casts(): array
    {
        return [
            'selected_option_ids' => 'array',
            'numeric_value' => 'decimal:2',
            'original_numeric_value' => 'decimal:2',
            'is_corrected_by_verifier' => 'boolean',
            'corrected_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QualificationOption::class, 'selected_option_id');
    }

    public function selectedOptions()
    {
        return QualificationOption::whereIn('id', $this->selected_option_ids ?? [])->get();
    }

    public function corrector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }
}
